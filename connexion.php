<?php
session_start();
require_once 'db.php';
require_once 'auto-login.php';
require_once 'csrf.php';
require_once 'rate-limit.php';
require_once 'oauth-config.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: mon-compte.php');
    exit;
}

$erreur_login = '';
$erreur_inscr = '';
$mode = $_GET['mode'] ?? 'connexion';

$erreurs_oauth = [
    'state'             => 'Erreur de sécurité, veuillez réessayer.',
    'google_annule'     => 'Connexion Google annulée.',
    'google_token'      => 'Impossible de contacter Google, réessayez.',
    'google_userinfo'   => 'Impossible de récupérer vos infos Google.',
    'google_email'      => 'Votre compte Google n\'a pas d\'email accessible.',
    'facebook_annule'   => 'Connexion Facebook annulée.',
    'facebook_token'    => 'Impossible de contacter Facebook, réessayez.',
    'facebook_userinfo' => 'Impossible de récupérer vos infos Facebook.',
];
$erreur_oauth_msg = '';
if (!empty($_GET['erreur']) && isset($erreurs_oauth[$_GET['erreur']])) {
    $erreur_oauth_msg = $erreurs_oauth[$_GET['erreur']];
}

function generate_oauth_state(): string {
    $state = bin2hex(random_bytes(16));
    $_SESSION['oauth_state'] = $state;
    return $state;
}

$state = generate_oauth_state();

$google_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
    'client_id'     => GOOGLE_CLIENT_ID,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope'         => 'openid email profile',
    'state'         => $state,
    'access_type'   => 'online',
]);

$facebook_url = 'https://www.facebook.com/v19.0/dialog/oauth?' . http_build_query([
    'client_id'     => FACEBOOK_APP_ID,
    'redirect_uri'  => FACEBOOK_REDIRECT_URI,
    'response_type' => 'code',
    'scope'         => 'email,public_profile',
    'state'         => $state,
]);

// ── Traitement formulaire connexion ─────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'login') {
    $email    = trim($_POST['email'] ?? '');
    $mdp      = $_POST['mdp'] ?? '';
    $remember = !empty($_POST['remember']);

    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $erreur_login = 'Session expirée, veuillez réessayer.';
    } elseif (!$email || !$mdp) {
        $erreur_login = 'Veuillez remplir tous les champs.';
    } else {
        $secondes = login_seconds_until_unlock($pdo, $email);
        if ($secondes > 0) {
            $erreur_login = 'Trop de tentatives. Réessayez dans ' . ceil($secondes/60) . ' minute(s).';
        } else {
            $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user && password_verify($mdp, $user['mot_de_passe'])) {
                login_clear_attempts($pdo, $email);
                $_SESSION['user_id']     = $user['id'];
                $_SESSION['user_nom']    = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_email']  = $user['email'];
                session_regenerate_id(true);
                if ($remember) remember_me_issue_token($pdo, (int)$user['id'], 30);
                header('Location: mon-compte.php');
                exit;
            } else {
                login_record_failed_attempt($pdo, $email);
                $erreur_login = 'Email ou mot de passe incorrect.';
            }
        }
    }
    $mode = 'connexion';
}

// ── Traitement formulaire inscription ───────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'register') {
    $nom    = trim($_POST['nom']    ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email  = trim($_POST['email_r'] ?? '');
    $mdp    = $_POST['mdp_r'] ?? '';
    $cgv    = $_POST['cgv']   ?? '';

    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $erreur_inscr = 'Session expirée, veuillez réessayer.';
    } elseif (!$nom || !$prenom || !$email || !$mdp) {
        $erreur_inscr = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur_inscr = 'Adresse email invalide.';
    } elseif (strlen($mdp) < 8) {
        $erreur_inscr = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif (!$cgv) {
        $erreur_inscr = "Vous devez accepter les conditions d'utilisation.";
    } else {
        $stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erreur_inscr = 'Un compte existe déjà avec cet email.';
        } else {
            $hash = password_hash($mdp, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe) VALUES (?,?,?,?)');
            $stmt->execute([$nom, $prenom, $email, $hash]);
            $_SESSION['user_id']     = $pdo->lastInsertId();
            $_SESSION['user_nom']    = $nom;
            $_SESSION['user_prenom'] = $prenom;
            $_SESSION['user_email']  = $email;
            session_regenerate_id(true);
            header('Location: mon-compte.php');
            exit;
        }
    }
    $mode = 'inscription';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion – Liens Démarches</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html, body {
  height: 100%;
  font-family: 'Segoe UI', system-ui, sans-serif;
  overflow: hidden;
  background: #0d0020;
}

/* ══════════════════════════════════════
   SLIDER
══════════════════════════════════════ */
.slider {
  display: flex;
  width: 200vw;
  height: 100vh;
  transition: transform 0.5s cubic-bezier(0.77, 0, 0.18, 1);
  will-change: transform;
}
.slider.show-inscription { transform: translateX(-50%); }

.page {
  width: 50%;
  height: 100vh;
  display: flex;
  flex-shrink: 0;
  overflow: hidden;
}

/* ══════════════════════════════════════
   PANEL DÉCORATIF (côté visuel)
══════════════════════════════════════ */
.panel-deco {
  flex: 0 0 48%;
  position: relative;
  overflow: hidden;
  background: #0d0020;
}

/* Gradient animé */
.deco__gradient {
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse at 30% 60%, #6a0dad 0%, transparent 55%),
              radial-gradient(ellipse at 75% 20%, #9b30ff 0%, transparent 45%),
              radial-gradient(ellipse at 60% 85%, #3b006e 0%, transparent 50%),
              #0d0020;
  animation: drifting 12s ease-in-out infinite alternate;
}
@keyframes drifting {
  0%   { filter: hue-rotate(0deg) brightness(1); }
  50%  { filter: hue-rotate(12deg) brightness(1.08); }
  100% { filter: hue-rotate(-8deg) brightness(0.95); }
}

/* Grille fine */
.deco__grid {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px);
  background-size: 44px 44px;
}

/* Cercles flottants */
.deco__orbs {
  position: absolute;
  inset: 0;
  pointer-events: none;
}
.orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(1px);
  animation: orbFloat linear infinite;
  opacity: 0;
}
.orb:nth-child(1) { width:90px;  height:90px;  left:8%;  background:rgba(155,48,255,.18); animation-duration:14s; animation-delay:0s; }
.orb:nth-child(2) { width:60px;  height:60px;  left:55%; background:rgba(106,13,173,.22); animation-duration:11s; animation-delay:2.5s; }
.orb:nth-child(3) { width:130px; height:130px; left:30%; background:rgba(180,100,255,.12); animation-duration:17s; animation-delay:5s; }
.orb:nth-child(4) { width:45px;  height:45px;  left:75%; background:rgba(255,255,255,.08); animation-duration:9s;  animation-delay:1s; }
.orb:nth-child(5) { width:75px;  height:75px;  left:20%; background:rgba(106,13,173,.15); animation-duration:13s; animation-delay:3.5s; }

@keyframes orbFloat {
  0%   { transform: translateY(105vh) scale(.7); opacity: 0; }
  8%   { opacity: 1; }
  92%  { opacity: 1; }
  100% { transform: translateY(-15vh) scale(1.15); opacity: 0; }
}

/* Contenu texte déco */
.deco__content {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 40px 44px 48px;
  z-index: 2;
}

/* Logo connexion : haut gauche — Logo inscription : haut droite */
.page-login .deco__logo   { justify-content: flex-start; }
.page-register .deco__logo { justify-content: flex-end; }

.deco__logo {
  display: flex;
  align-items: center;
  text-decoration: none;
}
.deco__logo img { height: 72px; width: auto; }

.deco__tagline {
  color: rgba(255,255,255,.9);
}
.deco__tagline-eyebrow {
  display: inline-block;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: .18em;
  text-transform: uppercase;
  color: rgba(255,255,255,.45);
  margin-bottom: 14px;
}
.deco__tagline h2 {
  font-size: clamp(24px, 3vw, 34px);
  font-weight: 800;
  line-height: 1.25;
  margin-bottom: 14px;
  letter-spacing: -.01em;
}
.deco__tagline p {
  font-size: 14px;
  color: rgba(255,255,255,.55);
  line-height: 1.7;
  max-width: 320px;
}

/* Pastilles organismes */
.deco__badges {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 24px;
}
.deco__badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.12);
  border-radius: 20px;
  padding: 5px 12px;
  font-size: 12px;
  color: rgba(255,255,255,.65);
  backdrop-filter: blur(4px);
}
.deco__badge-dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  background: #9b30ff;
  flex-shrink: 0;
}

/* Stat en bas */
.deco__stat {
  display: flex;
  align-items: center;
  gap: 14px;
  background: rgba(255,255,255,.06);
  border: 1px solid rgba(255,255,255,.1);
  border-radius: 12px;
  padding: 14px 20px;
  backdrop-filter: blur(8px);
}
.deco__stat-number {
  font-size: 28px;
  font-weight: 800;
  color: #ffffff;
  line-height: 1;
}
.deco__stat-label {
  font-size: 12px;
  color: rgba(255,255,255,.5);
  line-height: 1.4;
}

/* ══════════════════════════════════════
   PANEL FORMULAIRE
══════════════════════════════════════ */
.panel-form {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 40px 56px 40px 52px;
  background: #ffffff;
  overflow-y: auto;
  position: relative;
}

/* Lien retour accueil mobile */
.form-logo-mobile {
  display: none;
  margin-bottom: 24px;
}
.form-logo-mobile img { height: 36px; }

/* Titres */
.form-eyebrow {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: .16em;
  text-transform: uppercase;
  color: #6a0dad;
  margin-bottom: 8px;
}
.form-title {
  font-size: 28px;
  font-weight: 800;
  color: #0d0020;
  margin-bottom: 4px;
  letter-spacing: -.01em;
}
.form-subtitle {
  font-size: 14px;
  color: #888;
  margin-bottom: 28px;
}

/* ── Boutons sociaux ── */
.social-btns {
  display: flex;
  flex-direction: column;
  gap: 9px;
  margin-bottom: 22px;
}
.btn-social {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 11px 18px;
  border: 1.5px solid #e8e8f0;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 500;
  color: #222;
  background: #fafafa;
  cursor: pointer;
  transition: border-color .2s, background .2s, box-shadow .2s, transform .15s;
  text-decoration: none;
  position: relative;
  overflow: hidden;
}
.btn-social::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, transparent, rgba(106,13,173,.04));
  opacity: 0;
  transition: opacity .2s;
}
.btn-social:hover {
  border-color: #c4a0ee;
  background: #fff;
  box-shadow: 0 3px 12px rgba(106,13,173,.12);
  transform: translateY(-1px);
}
.btn-social:hover::before { opacity: 1; }
.btn-social:active { transform: translateY(0); }
.btn-social svg { flex-shrink: 0; }

/* Séparateur */
.divider {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: .08em;
  text-transform: uppercase;
  color: #bbb;
}
.divider::before,
.divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: linear-gradient(to right, transparent, #e8e8f0);
}
.divider::after {
  background: linear-gradient(to left, transparent, #e8e8f0);
}

/* ── Champs ── */
.field { margin-bottom: 14px; }
.field label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: #444;
  margin-bottom: 6px;
  letter-spacing: .03em;
}

.input-wrap { position: relative; }

input[type=text],
input[type=email],
input[type=password] {
  width: 100%;
  padding: 11px 14px;
  border: 1.5px solid #e8e8f0;
  border-radius: 9px;
  font-size: 14px;
  color: #111;
  background: #fafafa;
  outline: none;
  transition: border-color .2s, box-shadow .2s, background .2s;
}
input:focus {
  border-color: #6a0dad;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(106,13,173,.09);
}
input::placeholder { color: #c0c0cc; }

.toggle-pwd {
  position: absolute;
  right: 11px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #bbb;
  padding: 4px;
  display: flex;
  align-items: center;
  transition: color .2s;
}
.toggle-pwd:hover { color: #6a0dad; }
.input-wrap input { padding-right: 42px; }

/* 2 colonnes pour nom/prénom */
.fields-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

/* Options row */
.row-options {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 2px;
  margin-bottom: 16px;
  font-size: 13px;
}
.row-options label {
  display: flex;
  align-items: center;
  gap: 7px;
  font-size: 13px;
  font-weight: 400;
  color: #555;
  cursor: pointer;
  margin: 0;
}
.row-options input[type=checkbox] {
  width: auto;
  accent-color: #6a0dad;
}
.row-options a {
  color: #6a0dad;
  font-size: 13px;
  font-weight: 500;
  text-decoration: none;
  transition: opacity .2s;
}
.row-options a:hover { opacity: .7; }

/* CGV */
.cgv {
  display: flex;
  align-items: flex-start;
  gap: 9px;
  margin-top: 4px;
  margin-bottom: 16px;
  font-size: 13px;
  color: #555;
}
.cgv input[type=checkbox] {
  width: auto;
  margin-top: 2px;
  flex-shrink: 0;
  accent-color: #6a0dad;
}
.cgv label { margin: 0; font-size: 13px; font-weight: 400; cursor: pointer; }
.cgv a { color: #6a0dad; font-weight: 500; text-decoration: none; }

/* Bouton principal */
.btn-submit {
  width: 100%;
  padding: 13px;
  background: linear-gradient(135deg, #6a0dad, #9b30ff);
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  letter-spacing: .02em;
  transition: opacity .2s, transform .15s, box-shadow .2s;
  box-shadow: 0 4px 18px rgba(106,13,173,.3);
}
.btn-submit:hover {
  opacity: .92;
  transform: translateY(-1px);
  box-shadow: 0 6px 24px rgba(106,13,173,.38);
}
.btn-submit:active { transform: translateY(0); }

/* Lien switch */
.switch-link {
  text-align: center;
  margin-top: 20px;
  font-size: 13px;
  color: #888;
}
.switch-link a {
  color: #6a0dad;
  font-weight: 700;
  text-decoration: none;
  cursor: pointer;
  border-bottom: 1.5px solid transparent;
  transition: border-color .2s;
}
.switch-link a:hover { border-bottom-color: #6a0dad; }

/* Alertes */
.alert {
  padding: 11px 14px;
  border-radius: 8px;
  font-size: 13px;
  margin-bottom: 14px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.alert--error {
  background: #fff1f1;
  color: #c00;
  border: 1px solid #ffd0d0;
}
.alert--error::before { content: '⚠'; font-size: 15px; }

/* Disposition login/register */
.page-login   .panel-deco { order: 2; }
.page-login   .panel-form { order: 1; }
.page-register .panel-deco { order: 1; }
.page-register .panel-form { order: 2; }

/* ══════════════════════════════════════
   RESPONSIVE
══════════════════════════════════════ */
@media (max-width: 900px) {
  html, body { overflow: auto; }
  .slider { flex-direction: column; width: 100vw; height: auto; }
  .slider.show-inscription { transform: none; }
  .page { width: 100%; height: auto; flex-direction: column; }
  .panel-deco { display: none; }
  .panel-form {
    padding: 48px 28px 56px;
    min-height: 100vh;
    justify-content: flex-start;
    padding-top: 64px;
  }
  .form-logo-mobile { display: block; }
  .fields-row { grid-template-columns: 1fr; }
  .page-register { display: none; }
  .slider.show-inscription .page-login { display: none; }
  .slider.show-inscription .page-register { display: flex; }
}

@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { transition-duration: 0s !important; animation-duration: 0s !important; }
}
</style>
</head>
<body>

<div class="slider <?= $mode === 'inscription' ? 'show-inscription' : '' ?>" id="slider">

  <!-- ══════════════════════════════════
       PAGE CONNEXION
  ══════════════════════════════════ -->
  <div class="page page-login">

    <!-- Formulaire -->
    <div class="panel-form">
      <a href="index.php" class="form-logo-mobile">
        <img src="logo-horizontal.png" alt="Liens Démarches">
      </a>

      <p class="form-eyebrow">Espace personnel</p>
      <h1 class="form-title">Bon retour !</h1>
      <p class="form-subtitle">Connectez-vous pour accéder à vos démarches</p>

      <?php if ($erreur_oauth_msg): ?>
        <div class="alert alert--error"><?= htmlspecialchars($erreur_oauth_msg) ?></div>
      <?php endif; ?>

      <!-- Boutons sociaux -->
      <div class="social-btns">
        <a href="<?= htmlspecialchars($google_url) ?>" class="btn-social">
          <svg width="18" height="18" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
          </svg>
          Continuer avec Google
        </a>
        <a href="<?= htmlspecialchars($facebook_url) ?>" class="btn-social">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
          </svg>
          Continuer avec Facebook
        </a>
      </div>

      <div class="divider">ou par email</div>

      <?php if ($erreur_login): ?>
        <div class="alert alert--error"><?= htmlspecialchars($erreur_login) ?></div>
      <?php endif; ?>

      <form method="POST" action="connexion.php">
        <?= csrf_field() ?>
        <input type="hidden" name="form" value="login">

        <div class="field">
          <label for="l_email">Adresse email</label>
          <input type="email" id="l_email" name="email"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                 placeholder="vous@exemple.fr" required>
        </div>

        <div class="field">
          <label for="l_mdp">Mot de passe</label>
          <div class="input-wrap">
            <input type="password" id="l_mdp" name="mdp"
                   placeholder="••••••••" required>
            <button type="button" class="toggle-pwd" onclick="togglePwd('l_mdp', this)" aria-label="Afficher/masquer le mot de passe">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="row-options">
          <label>
            <input type="checkbox" name="remember" value="1">
            Se souvenir de moi
          </label>
          <a href="mot-de-passe-oublie.php">Mot de passe oublié ?</a>
        </div>

        <button type="submit" class="btn-submit">Se connecter →</button>
      </form>

      <p class="switch-link">
        Pas encore de compte ?
        <a id="go-register">Créer un compte</a>
      </p>
    </div>

    <!-- Panneau décoratif -->
    <div class="panel-deco">
      <div class="deco__gradient"></div>
      <div class="deco__grid"></div>
      <div class="deco__orbs">
        <div class="orb"></div><div class="orb"></div><div class="orb"></div>
        <div class="orb"></div><div class="orb"></div>
      </div>
      <div class="deco__content">
        <a href="index.php" class="deco__logo">
          <img src="logo-horizontal.png" alt="Liens Démarches">
        </a>
        <div class="deco__tagline">
          <p class="deco__tagline-eyebrow">Votre espace démarches</p>
          <h2>Toutes vos démarches<br>administratives,<br>au même endroit.</h2>
          <p>CAF, Impôts, Ameli, Urssaf, France Travail… Accédez en un clic aux plateformes officielles qui vous concernent.</p>
          <div class="deco__badges">
            <span class="deco__badge"><span class="deco__badge-dot"></span>Impôts</span>
            <span class="deco__badge"><span class="deco__badge-dot"></span>CAF</span>
            <span class="deco__badge"><span class="deco__badge-dot"></span>Ameli</span>
            <span class="deco__badge"><span class="deco__badge-dot"></span>Urssaf</span>
            <span class="deco__badge"><span class="deco__badge-dot"></span>Retraite</span>
          </div>
        </div>
        <div class="deco__stat">
          <div class="deco__stat-number">6</div>
          <div class="deco__stat-label">catégories de démarches<br>disponibles</div>
        </div>
      </div>
    </div>

  </div><!-- /page-login -->


  <!-- ══════════════════════════════════
       PAGE INSCRIPTION
  ══════════════════════════════════ -->
  <div class="page page-register">

    <!-- Panneau décoratif -->
    <div class="panel-deco">
      <div class="deco__gradient"></div>
      <div class="deco__grid"></div>
      <div class="deco__orbs">
        <div class="orb"></div><div class="orb"></div><div class="orb"></div>
        <div class="orb"></div><div class="orb"></div>
      </div>
      <div class="deco__content">
        <a href="index.php" class="deco__logo">
          <img src="logo-horizontal.png" alt="Liens Démarches">
        </a>
        <div class="deco__tagline">
          <p class="deco__tagline-eyebrow">Compte gratuit</p>
          <h2>Rejoignez<br>Liens Démarches<br>en 30 secondes.</h2>
          <p>Sauvegardez vos liens favoris, retrouvez vos démarches rapidement et gardez tout organisé en un seul endroit.</p>
          <div class="deco__badges">
            <span class="deco__badge"><span class="deco__badge-dot"></span>Favoris</span>
            <span class="deco__badge"><span class="deco__badge-dot"></span>Accès rapide</span>
            <span class="deco__badge"><span class="deco__badge-dot"></span>Gratuit</span>
          </div>
        </div>
        <div class="deco__stat">
          <div class="deco__stat-number">100%</div>
          <div class="deco__stat-label">gratuit, sans pub,<br>sans engagement</div>
        </div>
      </div>
    </div>

    <!-- Formulaire -->
    <div class="panel-form">
      <a href="index.php" class="form-logo-mobile">
        <img src="logo-horizontal.png" alt="Liens Démarches">
      </a>

      <p class="form-eyebrow">Inscription</p>
      <h1 class="form-title">Créer un compte</h1>
      <p class="form-subtitle">C'est gratuit et ça prend moins d'une minute</p>

      <?php if ($erreur_oauth_msg): ?>
        <div class="alert alert--error"><?= htmlspecialchars($erreur_oauth_msg) ?></div>
      <?php endif; ?>

      <!-- Boutons sociaux -->
      <div class="social-btns">
        <a href="<?= htmlspecialchars($google_url) ?>" class="btn-social">
          <svg width="18" height="18" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
          </svg>
          S'inscrire avec Google
        </a>
        <a href="<?= htmlspecialchars($facebook_url) ?>" class="btn-social">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
          </svg>
          S'inscrire avec Facebook
        </a>
      </div>

      <div class="divider">ou par email</div>

      <?php if ($erreur_inscr): ?>
        <div class="alert alert--error"><?= htmlspecialchars($erreur_inscr) ?></div>
      <?php endif; ?>

      <form method="POST" action="connexion.php">
        <?= csrf_field() ?>
        <input type="hidden" name="form" value="register">

        <div class="fields-row">
          <div class="field">
            <label for="r_nom">Nom</label>
            <input type="text" id="r_nom" name="nom"
                   value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>"
                   placeholder="Dupont" required>
          </div>
          <div class="field">
            <label for="r_prenom">Prénom</label>
            <input type="text" id="r_prenom" name="prenom"
                   value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>"
                   placeholder="Marie" required>
          </div>
        </div>

        <div class="field">
          <label for="r_email">Adresse email</label>
          <input type="email" id="r_email" name="email_r"
                 value="<?= htmlspecialchars($_POST['email_r'] ?? '') ?>"
                 placeholder="vous@exemple.fr" required>
        </div>

        <div class="field">
          <label for="r_mdp">Mot de passe <span style="color:#bbb;font-weight:400">(8 caractères min.)</span></label>
          <div class="input-wrap">
            <input type="password" id="r_mdp" name="mdp_r"
                   placeholder="••••••••" required minlength="8">
            <button type="button" class="toggle-pwd" onclick="togglePwd('r_mdp', this)" aria-label="Afficher/masquer le mot de passe">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="cgv">
          <input type="checkbox" id="cgv" name="cgv" value="1">
          <label for="cgv">
            J'ai lu et j'accepte la
            <a href="confidentialite.php">Politique de confidentialité</a>
            et les <a href="conditions.php">Conditions d'utilisation</a>
          </label>
        </div>

        <button type="submit" class="btn-submit">Créer mon compte →</button>
      </form>

      <p class="switch-link">
        Déjà un compte ?
        <a id="go-login">Se connecter</a>
      </p>
    </div>

  </div><!-- /page-register -->

</div><!-- /slider -->

<script>
(function () {
  var slider = document.getElementById('slider');

  document.getElementById('go-register').addEventListener('click', function () {
    slider.classList.add('show-inscription');
    history.replaceState(null, '', '?mode=inscription');
  });

  document.getElementById('go-login').addEventListener('click', function () {
    slider.classList.remove('show-inscription');
    history.replaceState(null, '', '?mode=connexion');
  });
})();

function togglePwd(inputId, btn) {
  var input = document.getElementById(inputId);
  var isHidden = input.type === 'password';
  input.type = isHidden ? 'text' : 'password';
  btn.style.color = isHidden ? '#6a0dad' : '#bbb';
}
</script>

</body>
</html>