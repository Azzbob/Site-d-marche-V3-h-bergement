<?php
// ============================================================
//  facebook-callback.php  —  Retour OAuth Facebook
// ============================================================
session_start();
require_once 'db.php';
require_once 'auto-login.php';
require_once 'oauth-config.php';

// ── 1. Vérification état CSRF ────────────────────────────────
if (empty($_GET['state']) || empty($_SESSION['oauth_state'])
    || !hash_equals($_SESSION['oauth_state'], $_GET['state'])) {
    unset($_SESSION['oauth_state']);
    header('Location: connexion.php?erreur=state');
    exit;
}
unset($_SESSION['oauth_state']);

// ── 2. Erreur Facebook ──────────────────────────────────────
if (!empty($_GET['error'])) {
    header('Location: connexion.php?erreur=facebook_annule');
    exit;
}

$code = $_GET['code'] ?? '';
if (!$code) {
    header('Location: connexion.php?erreur=no_code');
    exit;
}

// ── 3. Échange code → access_token ──────────────────────────
$tokenUrl = 'https://graph.facebook.com/v19.0/oauth/access_token?' . http_build_query([
    'client_id'     => FACEBOOK_APP_ID,
    'client_secret' => FACEBOOK_APP_SECRET,
    'redirect_uri'  => FACEBOOK_REDIRECT_URI,
    'code'          => $code,
]);

$tokenResponse = file_get_contents($tokenUrl);
if (!$tokenResponse) {
    header('Location: connexion.php?erreur=facebook_token');
    exit;
}

$tokenData   = json_decode($tokenResponse, true);
$accessToken = $tokenData['access_token'] ?? '';

if (!$accessToken) {
    header('Location: connexion.php?erreur=facebook_token');
    exit;
}

// ── 4. Récupère les infos utilisateur ───────────────────────
$userUrl = 'https://graph.facebook.com/me?' . http_build_query([
    'fields'       => 'id,first_name,last_name,email',
    'access_token' => $accessToken,
]);

$userInfo = file_get_contents($userUrl);
if (!$userInfo) {
    header('Location: connexion.php?erreur=facebook_userinfo');
    exit;
}

$user = json_decode($userInfo, true);

$facebookId = $user['id']         ?? '';
$email      = $user['email']      ?? '';
$prenom     = $user['first_name'] ?? '';
$nom        = $user['last_name']  ?? 'Utilisateur';

// Facebook peut ne pas renvoyer l'email si l'utilisateur le refuse
if (!$email) {
    // On génère un email factice basé sur l'ID pour quand même créer le compte
    $email = 'fb_' . $facebookId . '@noemail.local';
}

// ── 5. Connexion ou création du compte ──────────────────────
$stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE facebook_id = ?');
$stmt->execute([$facebookId]);
$existant = $stmt->fetch();

if (!$existant) {
    $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
    $stmt->execute([$email]);
    $existant = $stmt->fetch();

    if ($existant) {
        $pdo->prepare('UPDATE utilisateurs SET facebook_id = ? WHERE id = ?')
            ->execute([$facebookId, $existant['id']]);
    } else {
        $pdo->prepare(
            'INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, facebook_id)
             VALUES (?, ?, ?, ?, ?)'
        )->execute([$nom, $prenom, $email, '', $facebookId]);

        $existant = [
            'id'     => (int) $pdo->lastInsertId(),
            'nom'    => $nom,
            'prenom' => $prenom,
            'email'  => $email,
        ];
    }
}

// ── 6. Session ──────────────────────────────────────────────
$_SESSION['user_id']     = $existant['id'];
$_SESSION['user_nom']    = $existant['nom'];
$_SESSION['user_prenom'] = $existant['prenom'];
$_SESSION['user_email']  = $existant['email'];
session_regenerate_id(true);

header('Location: mon-compte.php');
exit;