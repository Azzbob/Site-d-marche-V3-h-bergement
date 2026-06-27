<?php
// ============================================================
//  mot-de-passe-oublie.php  —  Demande de réinitialisation
// ============================================================
session_start();
require_once 'db.php';
require_once 'auto-login.php';
require_once 'csrf.php';
require_once 'password-reset.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: mon-compte.php');
    exit;
}

$message = '';
$erreur  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $erreur = 'Session expirée, veuillez réessayer.';
    } elseif (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = 'Veuillez saisir une adresse email valide.';
    } else {
        $resultat = password_reset_create_token($pdo, $email);

        if ($resultat) {
            $lien = app_base_url() . '/reinitialiser-mot-de-passe.php?token=' . urlencode($resultat['token']);
            password_reset_send_email($resultat['user']['email'], $resultat['user']['prenom'], $lien);
        }

        // Même message qu'un compte existe ou non avec cet email,
        // pour ne pas révéler quelles adresses sont inscrites sur le site.
        $message = "Si un compte existe avec cette adresse, un email contenant un lien de réinitialisation vient de vous être envoyé. Pensez à vérifier vos spams.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mot de passe oublié – Liens Démarches</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html, body {
  min-height: 100vh;
  font-family: 'Segoe UI', system-ui, sans-serif;
}

body {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background: radial-gradient(ellipse at 30% 20%, #6a0dad 0%, transparent 55%),
              radial-gradient(ellipse at 80% 80%, #9b30ff 0%, transparent 50%),
              #0d0020;
}

.card {
  width: 100%;
  max-width: 440px;
  background: #ffffff;
  border-radius: 16px;
  padding: 40px 40px 36px;
  box-shadow: 0 20px 60px rgba(0,0,0,.35);
}

.form-logo { display: flex; align-items: center; margin-bottom: 26px; }
.form-logo img { height: 38px; width: auto; }

.form-eyebrow {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: .16em;
  text-transform: uppercase;
  color: #6a0dad;
  margin-bottom: 8px;
}
.form-title {
  font-size: 26px;
  font-weight: 800;
  color: #0d0020;
  margin-bottom: 6px;
  letter-spacing: -.01em;
}
.form-subtitle {
  font-size: 14px;
  color: #888;
  line-height: 1.6;
  margin-bottom: 26px;
}

.field { margin-bottom: 18px; }
.field label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: #444;
  margin-bottom: 6px;
  letter-spacing: .03em;
}
input[type=email] {
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
.btn-submit:hover { opacity: .92; transform: translateY(-1px); }
.btn-submit:active { transform: translateY(0); }

.switch-link {
  text-align: center;
  margin-top: 22px;
  font-size: 13px;
  color: #888;
}
.switch-link a {
  color: #6a0dad;
  font-weight: 700;
  text-decoration: none;
  border-bottom: 1.5px solid transparent;
  transition: border-color .2s;
}
.switch-link a:hover { border-bottom-color: #6a0dad; }

.alert {
  padding: 11px 14px;
  border-radius: 8px;
  font-size: 13px;
  margin-bottom: 18px;
  display: flex;
  align-items: flex-start;
  gap: 8px;
  line-height: 1.5;
}
.alert--error   { background: #fff1f1; color: #c00; border: 1px solid #ffd0d0; }
.alert--error::before { content: '⚠'; font-size: 15px; }
.alert--success { background: #e6f9ee; color: #1a7a3f; border: 1px solid #b8ecc9; }
.alert--success::before { content: '✓'; font-size: 15px; }
</style>
</head>
<body>

<div class="card">
  <a href="index.php" class="form-logo">
    <img src="logo-horizontal.png" alt="Liens Démarches">
  </a>

  <p class="form-eyebrow">Récupération de compte</p>
  <h1 class="form-title">Mot de passe oublié ?</h1>
  <p class="form-subtitle">
    Indiquez votre adresse email, nous vous envoyons un lien pour
    choisir un nouveau mot de passe.
  </p>

  <?php if ($erreur): ?>
    <div class="alert alert--error"><?= htmlspecialchars($erreur) ?></div>
  <?php endif; ?>

  <?php if ($message): ?>
    <div class="alert alert--success"><?= htmlspecialchars($message) ?></div>
  <?php else: ?>
    <form method="POST" action="mot-de-passe-oublie.php">
      <?= csrf_field() ?>

      <div class="field">
        <label for="email">Adresse email</label>
        <input type="email" id="email" name="email"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
               placeholder="vous@exemple.fr" required>
      </div>

      <button type="submit" class="btn-submit">Envoyer le lien →</button>
    </form>
  <?php endif; ?>

  <p class="switch-link">
    <a href="connexion.php">← Retour à la connexion</a>
  </p>
</div>

</body>
</html>