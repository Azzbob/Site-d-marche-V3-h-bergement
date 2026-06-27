<?php
// ============================================================
//  mon-compte.php  —  Espace personnel de l'utilisateur
// ============================================================
session_start();
require_once 'db.php';
require_once 'auto-login.php'; // reconnecte via cookie "remember me" si besoin
require_once 'csrf.php';

// Gestion déconnexion (avant le check "connecté", car on peut être
// connecté juste via le cookie remember-me sans le savoir)
if (isset($_GET['logout'])) {
    // Révoque le(s) token(s) remember-me en base, pas juste le cookie
    if (!empty($_SESSION['user_id'])) {
        $stmt = $pdo->prepare('DELETE FROM remember_tokens WHERE user_id = ?');
        $stmt->execute([$_SESSION['user_id']]);
    } elseif (!empty($_COOKIE['remember_token'])) {
        $parts = explode(':', $_COOKIE['remember_token'], 2);
        if (count($parts) === 2 && ctype_digit($parts[0])) {
            $stmt = $pdo->prepare('DELETE FROM remember_tokens WHERE id = ?');
            $stmt->execute([(int) $parts[0]]);
        }
    }

    setcookie('remember_token', '', time() - 3600, '/', '', false, true);

    $_SESSION = [];
    session_destroy();

    header('Location: connexion.php');
    exit;
}

// Protection : redirige si non connecté (auto-login.php a déjà tenté
// de reconnecter via le cookie avant qu'on arrive ici)
if (empty($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

// Recharge les données fraîches depuis la BDD
$stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION = [];
    session_destroy();
    header('Location: connexion.php');
    exit;
}

// Gestion mise à jour du profil
$msg_succes = '';
$msg_erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $msg_erreur = 'Session expirée, veuillez réessayer.';
    } else {
        $nom    = trim($_POST['nom']    ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email  = trim($_POST['email']  ?? '');
        $mdp    = $_POST['new_mdp']     ?? '';

        if (!$nom || !$prenom || !$email) {
            $msg_erreur = 'Nom, prénom et email sont obligatoires.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg_erreur = 'Email invalide.';
        } elseif ($mdp && strlen($mdp) < 8) {
            $msg_erreur = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
        } else {
            // Vérifie qu'aucun AUTRE compte n'utilise déjà cet email
            $stmtCheck = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ? AND id != ?');
            $stmtCheck->execute([$email, $user['id']]);

            if ($stmtCheck->fetch()) {
                $msg_erreur = 'Cet email est déjà utilisé par un autre compte.';
            } else {
                if ($mdp) {
                    $hash = password_hash($mdp, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare(
                        'UPDATE utilisateurs SET nom=?, prenom=?, email=?, mot_de_passe=? WHERE id=?'
                    );
                    $stmt->execute([$nom, $prenom, $email, $hash, $user['id']]);

                    // Le mot de passe a changé : on révoque les éventuels
                    // tokens "remember me" existants, par sécurité.
                    $stmtRevoke = $pdo->prepare('DELETE FROM remember_tokens WHERE user_id = ?');
                    $stmtRevoke->execute([$user['id']]);
                } else {
                    $stmt = $pdo->prepare(
                        'UPDATE utilisateurs SET nom=?, prenom=?, email=? WHERE id=?'
                    );
                    $stmt->execute([$nom, $prenom, $email, $user['id']]);
                }

                // Rafraîchit la session
                $_SESSION['user_nom']    = $nom;
                $_SESSION['user_prenom'] = $prenom;
                $_SESSION['user_email']  = $email;
                $user['nom']    = $nom;
                $user['prenom'] = $prenom;
                $user['email']  = $email;
                $msg_succes = 'Vos informations ont bien été mises à jour.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mon Compte – Liens Démarches</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Segoe UI', sans-serif;
    background: #f4f4f8;
    color: #222;
    min-height: 100vh;
  }

  /* navbar gérée globalement par header.php */

  /* ── HERO CARTE UTILISATEUR ── */
  .hero {
    background: linear-gradient(135deg, #3b006e 0%, #6a0dad 60%, #9b30ff 100%);
    padding: 50px 40px 80px;
    color: #fff;
    position: relative;
  }
  .hero h2 { font-size: 26px; margin-bottom: 6px; }
  .hero p  { font-size: 14px; opacity: .8; }

  .user-card {
    position: absolute;
    bottom: -50px;
    left: 50%;
    transform: translateX(-50%);
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,.12);
    padding: 30px 40px;
    display: flex;
    align-items: center;
    gap: 24px;
    min-width: 420px;
  }
  .avatar {
    width: 70px; height: 70px;
    background: #6a0dad;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; color: #fff; font-weight: 700;
    flex-shrink: 0;
  }
  .user-info h3 { font-size: 20px; color: #111; }
  .user-info p  { font-size: 13px; color: #666; margin-top: 2px; }
  .badge {
    margin-top: 8px; display: inline-block;
    background: #eef0ff; color: #6a0dad;
    font-size: 11px; font-weight: 600;
    padding: 3px 10px; border-radius: 20px;
  }

  /* ── CONTENU ── */
  .container {
    max-width: 900px;
    margin: 90px auto 60px;
    padding: 0 20px;
  }

  /* Cartes stats */
  .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 30px; }
  .stat-card {
    background: #fff; border-radius: 12px;
    padding: 20px 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,.07);
    text-align: center;
  }
  .stat-card .number { font-size: 32px; font-weight: 800; color: #6a0dad; }
  .stat-card .label  { font-size: 13px; color: #888; margin-top: 4px; }

  /* Formulaire profil */
  .section {
    background: #fff; border-radius: 12px;
    padding: 30px 36px;
    box-shadow: 0 2px 8px rgba(0,0,0,.07);
    margin-bottom: 24px;
  }
  .section h3 {
    font-size: 17px; color: #333; margin-bottom: 20px;
    padding-bottom: 12px; border-bottom: 1px solid #eee;
  }
  .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .form-group { display: flex; flex-direction: column; }
  .form-group.full { grid-column: 1 / -1; }
  .form-group label { font-size: 13px; color: #555; margin-bottom: 6px; }
  .form-group input {
    padding: 11px 14px; border: 1px solid #ddd;
    border-radius: 8px; font-size: 14px; outline: none;
    transition: border-color .2s;
  }
  .form-group input:focus { border-color: #6a0dad; }
  .form-group input[readonly] { background: #f9f9f9; color: #888; }

  .btn-save {
    margin-top: 20px; padding: 12px 30px;
    background: #6a0dad; color: #fff;
    border: none; border-radius: 8px;
    font-size: 14px; font-weight: 600;
    cursor: pointer; transition: background .2s;
  }
  .btn-save:hover { background: #5a0b99; }

  .alert-success { background: #e6f9ee; color: #1a7a3f; padding: 12px 16px;
                   border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
  .alert-error   { background: #ffe0e0; color: #c00; padding: 12px 16px;
                   border-radius: 8px; font-size: 13px; margin-bottom: 16px; }

  /* Membre depuis */
  .meta { font-size: 12px; color: #aaa; margin-top: 16px; }

  @media (max-width: 640px) {
    .form-grid { grid-template-columns: 1fr; }
    .stats     { grid-template-columns: 1fr; }
    .user-card { min-width: auto; width: calc(100% - 40px); flex-direction: column; text-align: center; }
    nav ul     { display: none; }
  }
</style>
</head>
<body>

<?php include 'header.php'; ?>

<!-- HERO -->
<div class="hero">
  <h2>Bienvenue dans votre espace personnel</h2>
  <p>Gérez votre profil et accédez à toutes vos démarches</p>

  <div class="user-card">
    <div class="avatar">
      <?= mb_strtoupper(mb_substr($user['prenom'], 0, 1)) . mb_strtoupper(mb_substr($user['nom'], 0, 1)) ?>
    </div>
    <div class="user-info">
      <h3><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h3>
      <p><?= htmlspecialchars($user['email']) ?></p>
      <span class="badge">Membre actif</span>
    </div>
  </div>
</div>

<!-- CONTENU PRINCIPAL -->
<div class="container">

  <!-- Stats -->
  <div class="stats">
    <div class="stat-card">
      <div class="number">6</div>
      <div class="label">Liens disponibles</div>
    </div>
    <div class="stat-card">
      <div class="number"><?= date('d/m/Y', strtotime($user['created_at'])) ?></div>
      <div class="label">Date d'inscription</div>
    </div>
    <div class="stat-card">
      <div class="number">✓</div>
      <div class="label">Compte vérifié</div>
    </div>
  </div>

  <!-- Alertes -->
  <?php if ($msg_succes): ?>
    <div class="alert-success"><?= htmlspecialchars($msg_succes) ?></div>
  <?php endif; ?>
  <?php if ($msg_erreur): ?>
    <div class="alert-error"><?= htmlspecialchars($msg_erreur) ?></div>
  <?php endif; ?>

  <!-- Formulaire profil -->
  <div class="section">
    <h3>Mes informations personnelles</h3>
    <form method="POST" action="">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="update">
      <div class="form-grid">
        <div class="form-group">
          <label for="nom">Nom</label>
          <input type="text" id="nom" name="nom"
                 value="<?= htmlspecialchars($user['nom']) ?>" required>
        </div>
        <div class="form-group">
          <label for="prenom">Prénom</label>
          <input type="text" id="prenom" name="prenom"
                 value="<?= htmlspecialchars($user['prenom']) ?>" required>
        </div>
        <div class="form-group full">
          <label for="email">Adresse Email</label>
          <input type="email" id="email" name="email"
                 value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="form-group full">
          <label for="new_mdp">Nouveau mot de passe <small style="color:#aaa">(laisser vide pour ne pas changer)</small></label>
          <input type="password" id="new_mdp" name="new_mdp" minlength="8" placeholder="8 caractères minimum">
        </div>
      </div>
      <button type="submit" class="btn-save">Enregistrer les modifications</button>
    </form>
    <p class="meta">Membre depuis le <?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
  </div>

</div>

</body>
</html>
<?php include 'footer.php'; ?>