<?php
// ============================================================
//  reinitialiser-mot-de-passe.php  —  Choix du nouveau mot de
//  passe via le lien reçu par email
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

$token  = $_GET['token'] ?? ($_POST['token'] ?? '');
$erreur = '';
$succes = false;

$resetRow = $token ? password_reset_verify_token($pdo, $token) : false;

if (!$token || !$resetRow) {
    $erreur = "Ce lien de réinitialisation est invalide ou a expiré. Merci de refaire une demande.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mdp  = $_POST['mdp']  ?? '';
    $mdp2 = $_POST['mdp2'] ?? '';

    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $erreur = 'Session expirée, veuillez réessayer.';
    } elseif (strlen($mdp) < 8) {
        $erreur = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif ($mdp !== $mdp2) {
        $erreur = 'Les mots de passe ne correspondent pas.';
    } else {
        $hash = password_hash($mdp, PASSWORD_BCRYPT);
        $pdo->prepare('UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?')
            ->execute([$hash, $resetRow['user_id']]);

        // Le mot de passe vient de changer : on invalide le lien utilisé
        // ainsi que les éventuels tokens "Se souvenir de moi" existants,
        // par sécurité (cf. même logique que dans mon-compte.php).
        password_reset_consume($pdo, (int) $resetRow['user_id']);
        $pdo->prepare('DELETE FROM remember_tokens WHERE user_id = ?')
            ->execute([$resetRow['user_id']]);

        $succes = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Réinitialiser le mot de passe – Liens Démarches</title>
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

.field { margin-bottom: 16px; }
.field label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: #444;
  margin-bottom: 6px;
  letter-spacing: .03em;
}
.input-wrap { position: relative; }
input[type=password] {
  width: 100%;
  padding: 11px 42px 11px 14px;
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
  margin-top: 6px;
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

  <?php if ($succes): ?>
    <p class="form-eyebrow">Récupération de compte</p>
    <h1 class="form-title">Mot de passe modifié !</h1>
    <div class="alert alert--success">
      Votre mot de passe a bien été mis à jour. Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.
    </div>
    <a href="connexion.php" class="btn-submit" style="display:block;text-align:center;text-decoration:none;line-height:1.4">
      Aller à la connexion →
    </a>

  <?php elseif ($erreur && !$resetRow): ?>
    <p class="form-eyebrow">Récupération de compte</p>
    <h1 class="form-title">Lien invalide</h1>
    <div class="alert alert--error"><?= htmlspecialchars($erreur) ?></div>
    <a href="mot-de-passe-oublie.php" class="btn-submit" style="display:block;text-align:center;text-decoration:none;line-height:1.4">
      Refaire une demande →
    </a>

  <?php else: ?>
    <p class="form-eyebrow">Récupération de compte</p>
    <h1 class="form-title">Nouveau mot de passe</h1>
    <p class="form-subtitle">
      Bonjour <?= htmlspecialchars($resetRow['prenom']) ?>, choisissez votre nouveau mot de passe.
    </p>

    <?php if ($erreur): ?>
      <div class="alert alert--error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="POST" action="reinitialiser-mot-de-passe.php?token=<?= urlencode($token) ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

      <div class="field">
        <label for="mdp">Nouveau mot de passe <span style="color:#bbb;font-weight:400">(8 caractères min.)</span></label>
        <div class="input-wrap">
          <input type="password" id="mdp" name="mdp" placeholder="••••••••" required minlength="8">
          <button type="button" class="toggle-pwd" onclick="togglePwd('mdp', this)" aria-label="Afficher/masquer le mot de passe">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>
      </div>

      <div class="field">
        <label for="mdp2">Confirmer le mot de passe</label>
        <div class="input-wrap">
          <input type="password" id="mdp2" name="mdp2" placeholder="••••••••" required minlength="8">
          <button type="button" class="toggle-pwd" onclick="togglePwd('mdp2', this)" aria-label="Afficher/masquer le mot de passe">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-submit">Mettre à jour le mot de passe →</button>
    </form>
  <?php endif; ?>

  <p class="switch-link">
    <a href="connexion.php">← Retour à la connexion</a>
  </p>
</div>

<script>
function togglePwd(inputId, btn) {
  var input = document.getElementById(inputId);
  var isHidden = input.type === 'password';
  input.type = isHidden ? 'text' : 'password';
  btn.style.color = isHidden ? '#6a0dad' : '#bbb';
}
</script>

</body>
</html>