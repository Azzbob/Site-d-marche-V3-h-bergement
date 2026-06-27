<?php
// ============================================================
//  google-callback.php  —  Retour OAuth Google
// ============================================================
session_start();
require_once 'db.php';
require_once 'auto-login.php';
require_once 'oauth-config.php';

// ── 1. Vérification de l'état CSRF anti-forgery ─────────────
if (empty($_GET['state']) || empty($_SESSION['oauth_state'])
    || !hash_equals($_SESSION['oauth_state'], $_GET['state'])) {
    unset($_SESSION['oauth_state']);
    header('Location: connexion.php?erreur=state');
    exit;
}
unset($_SESSION['oauth_state']);

// ── 2. Erreur renvoyée par Google ───────────────────────────
if (!empty($_GET['error'])) {
    header('Location: connexion.php?erreur=google_annule');
    exit;
}

// ── 3. Échange du code contre un access_token ───────────────
$code = $_GET['code'] ?? '';
if (!$code) {
    header('Location: connexion.php?erreur=no_code');
    exit;
}

$tokenResponse = file_get_contents('https://oauth2.googleapis.com/token', false,
    stream_context_create(['http' => [
        'method'  => 'POST',
        'header'  => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query([
            'code'          => $code,
            'client_id'     => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'grant_type'    => 'authorization_code',
        ]),
    ]])
);

if (!$tokenResponse) {
    header('Location: connexion.php?erreur=google_token');
    exit;
}

$tokenData   = json_decode($tokenResponse, true);
$accessToken = $tokenData['access_token'] ?? '';

if (!$accessToken) {
    header('Location: connexion.php?erreur=google_token');
    exit;
}

// ── 4. Récupère les infos de l'utilisateur ──────────────────
$userInfo = file_get_contents('https://www.googleapis.com/oauth2/v2/userinfo', false,
    stream_context_create(['http' => [
        'header' => 'Authorization: Bearer ' . $accessToken,
    ]])
);

if (!$userInfo) {
    header('Location: connexion.php?erreur=google_userinfo');
    exit;
}

$user = json_decode($userInfo, true);

$googleId = $user['id']             ?? '';
$email    = $user['email']          ?? '';
$prenom   = $user['given_name']     ?? '';
$nom      = $user['family_name']    ?? 'Utilisateur';

if (!$email) {
    header('Location: connexion.php?erreur=google_email');
    exit;
}

// ── 5. Connexion ou création du compte ──────────────────────

// Cherche par google_id d'abord
$stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE google_id = ?');
$stmt->execute([$googleId]);
$existant = $stmt->fetch();

if (!$existant) {
    // Cherche par email (compte classique déjà existant)
    $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
    $stmt->execute([$email]);
    $existant = $stmt->fetch();

    if ($existant) {
        // Rattache le compte Google à ce compte existant
        $pdo->prepare('UPDATE utilisateurs SET google_id = ? WHERE id = ?')
            ->execute([$googleId, $existant['id']]);
    } else {
        // Crée un nouveau compte
        $pdo->prepare(
            'INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, google_id)
             VALUES (?, ?, ?, ?, ?)'
        )->execute([$nom, $prenom, $email, '', $googleId]);

        $existant = [
            'id'     => (int) $pdo->lastInsertId(),
            'nom'    => $nom,
            'prenom' => $prenom,
            'email'  => $email,
        ];
    }
}

// ── 6. Ouvre la session ─────────────────────────────────────
$_SESSION['user_id']     = $existant['id'];
$_SESSION['user_nom']    = $existant['nom'];
$_SESSION['user_prenom'] = $existant['prenom'];
$_SESSION['user_email']  = $existant['email'];
session_regenerate_id(true);

header('Location: mon-compte.php');
exit;