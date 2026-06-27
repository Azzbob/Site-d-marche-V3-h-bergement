<?php
// ============================================================
//  mon-compte.php  —  Espace personnel de l'utilisateur
// ============================================================
session_start();
require_once 'db.php';
require_once 'auto-login.php';
require_once 'csrf.php';

// Gestion déconnexion
if (isset($_GET['logout'])) {
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

// Protection connexion
if (empty($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

// Données fraîches depuis la BDD
$stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION = [];
    session_destroy();
    header('Location: connexion.php');
    exit;
}

// Nombre de favoris
$stmtFav = $pdo->prepare('SELECT COUNT(*) FROM favoris WHERE user_id = ?');
$stmtFav->execute([$_SESSION['user_id']]);
$nb_favoris = (int) $stmtFav->fetchColumn();

// Gestion mise à jour du profil
$msg_succes = '';
$msg_erreur = '';
$active_tab = $_GET['tab'] ?? 'profil';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $msg_erreur = 'Session expirée, veuillez réessayer.';

    } elseif ($_POST['action'] === 'update_profil') {
        $nom    = trim($_POST['nom']    ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email  = trim($_POST['email']  ?? '');

        if (!$nom || !$prenom || !$email) {
            $msg_erreur = 'Nom, prénom et email sont obligatoires.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg_erreur = 'Email invalide.';
        } else {
            $stmtCheck = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ? AND id != ?');
            $stmtCheck->execute([$email, $user['id']]);
            if ($stmtCheck->fetch()) {
                $msg_erreur = 'Cet email est déjà utilisé par un autre compte.';
            } else {
                $stmt = $pdo->prepare('UPDATE utilisateurs SET nom=?, prenom=?, email=? WHERE id=?');
                $stmt->execute([$nom, $prenom, $email, $user['id']]);
                $_SESSION['user_nom']    = $nom;
                $_SESSION['user_prenom'] = $prenom;
                $_SESSION['user_email']  = $email;
                $user['nom']    = $nom;
                $user['prenom'] = $prenom;
                $user['email']  = $email;
                $msg_succes = ' Vos informations ont bien été mises à jour.';
            }
        }
        $active_tab = 'profil';

    } elseif ($_POST['action'] === 'update_mdp') {
        $mdp_actuel = $_POST['mdp_actuel'] ?? '';
        $nouveau    = $_POST['new_mdp']    ?? '';
        $confirm    = $_POST['confirm_mdp'] ?? '';

        if (!$mdp_actuel || !$nouveau || !$confirm) {
            $msg_erreur = 'Tous les champs du mot de passe sont requis.';
        } elseif (!password_verify($mdp_actuel, $user['mot_de_passe'])) {
            $msg_erreur = 'Le mot de passe actuel est incorrect.';
        } elseif (strlen($nouveau) < 8) {
            $msg_erreur = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
        } elseif ($nouveau !== $confirm) {
            $msg_erreur = 'Les deux mots de passe ne correspondent pas.';
        } else {
            $hash = password_hash($nouveau, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('UPDATE utilisateurs SET mot_de_passe=? WHERE id=?');
            $stmt->execute([$hash, $user['id']]);
            // Révocation des tokens remember-me par sécurité
            $stmtRevoke = $pdo->prepare('DELETE FROM remember_tokens WHERE user_id = ?');
            $stmtRevoke->execute([$user['id']]);
            $msg_succes = ' Mot de passe modifié avec succès.';
        }
        $active_tab = 'securite';
    }
}

$page_title = 'Mon Compte – Liens Démarches';
$page_desc  = 'Mon compte Liens Démarches – Gérez votre profil, vos favoris et vos préférences.';
include 'header.php';
?>

<style>
/* ── VARIABLES ── */
:root {
  --violet:      #6a0dad;
  --violet-dark: #5a0b99;
  --violet-soft: #f0e6fa;
  --white:       #ffffff;
  --bg:          #f4f4f8;
  --text:        #1a1a2e;
  --text-muted:  #666677;
  --border:      #e0e0e8;
  --radius:      12px;
  --shadow-sm:   0 2px 8px rgba(0,0,0,.07);
  --shadow-md:   0 6px 24px rgba(106,13,173,.10);
}

/* ── HERO ── */
.account-hero {
  background: linear-gradient(135deg, #3b006e 0%, #6a0dad 60%, #9b30ff 100%);
  padding: 48px 24px 100px;
  color: #fff;
  position: relative;
  overflow: hidden;
}
.account-hero::before {
  content: '';
  position: absolute;
  width: 400px; height: 400px;
  border-radius: 50%;
  background: rgba(255,255,255,.05);
  right: -80px; top: -100px;
  pointer-events: none;
}
.account-hero__inner { max-width: 900px; margin: 0 auto; }
.account-hero h2 { font-size: clamp(20px, 3vw, 26px); margin-bottom: 4px; }
.account-hero p  { font-size: 14px; opacity: .75; }

/* ── CARTE UTILISATEUR (flottante) ── */
.user-card {
  position: relative;
  max-width: 900px;
  margin: -60px auto 0;
  padding: 0 24px;
  z-index: 10;
}
.user-card__inner {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 8px 36px rgba(106,13,173,.14);
  padding: 24px 32px;
  display: flex;
  align-items: center;
  gap: 24px;
  flex-wrap: wrap;
}
.user-card__avatar {
  width: 72px; height: 72px;
  background: linear-gradient(135deg, #6a0dad, #9b30ff);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 26px; color: #fff; font-weight: 700;
  flex-shrink: 0;
  box-shadow: 0 4px 16px rgba(106,13,173,.3);
}
.user-card__info { flex: 1; min-width: 160px; }
.user-card__name { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 2px; }
.user-card__email { font-size: 13px; color: #888; }
.user-card__badges { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 8px; }
.badge {
  font-size: 11px; font-weight: 700;
  padding: 3px 10px; border-radius: 20px;
}
.badge--green { background: #e6f9ee; color: #1a7a3f; }
.badge--violet { background: #f0e6fa; color: #6a0dad; }

.user-card__actions { display: flex; gap: 10px; flex-wrap: wrap; }
.btn-outline-violet {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 13px; font-weight: 600;
  padding: 9px 18px; border-radius: 8px;
  border: 1.5px solid #6a0dad; color: #6a0dad; background: transparent;
  text-decoration: none; cursor: pointer;
  transition: background .2s, color .2s;
}
.btn-outline-violet:hover { background: #f0e6fa; }
.btn-danger-outline {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 13px; font-weight: 600;
  padding: 9px 18px; border-radius: 8px;
  border: 1.5px solid #e0e0e8; color: #888; background: transparent;
  text-decoration: none; cursor: pointer;
  transition: border-color .2s, color .2s;
}
.btn-danger-outline:hover { border-color: #c00; color: #c00; }

/* ── STATS ── */
.stats-row {
  max-width: 900px; margin: 28px auto 0;
  padding: 0 24px;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
}
.stat-card {
  background: #fff;
  border: 1px solid #e0e0e8;
  border-radius: 12px;
  padding: 20px 18px;
  text-align: center;
  box-shadow: var(--shadow-sm);
  transition: box-shadow .2s;
}
.stat-card:hover { box-shadow: var(--shadow-md); }
.stat-card__icon { font-size: 22px; margin-bottom: 8px; }
.stat-card__value { font-size: 24px; font-weight: 800; color: #6a0dad; line-height: 1; }
.stat-card__label { font-size: 12px; color: #888; margin-top: 4px; }

/* ── CONTENU PRINCIPAL ── */
.account-container {
  max-width: 900px;
  margin: 28px auto 80px;
  padding: 0 24px;
}

/* ── ONGLETS ── */
.account-tabs {
  display: flex; gap: 4px;
  border-bottom: 2px solid #e0e0e8;
  margin-bottom: 28px;
  overflow-x: auto;
}
.account-tab {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 11px 18px;
  font-size: 13px; font-weight: 600; color: #888;
  border: none; background: none; cursor: pointer;
  border-bottom: 3px solid transparent;
  margin-bottom: -2px;
  white-space: nowrap;
  text-decoration: none;
  transition: color .2s, border-color .2s;
}
.account-tab:hover { color: #6a0dad; }
.account-tab.active { color: #6a0dad; border-bottom-color: #6a0dad; }

/* ── SECTIONS CARTE ── */
.account-card {
  background: #fff;
  border: 1px solid #e0e0e8;
  border-radius: 14px;
  padding: 32px 36px;
  box-shadow: var(--shadow-sm);
  margin-bottom: 20px;
}
.account-card h3 {
  font-size: 16px; font-weight: 700; color: #1a1a2e;
  margin-bottom: 6px;
}
.account-card .card-desc {
  font-size: 13px; color: #888; margin-bottom: 24px; line-height: 1.5;
}

/* ── FORMULAIRE ── */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { display: flex; flex-direction: column; margin-bottom: 0; }
.form-group.full { grid-column: 1 / -1; }
.form-group label { font-size: 13px; font-weight: 600; color: #444; margin-bottom: 6px; }
.form-group input {
  padding: 11px 14px;
  border: 1.5px solid #ddd; border-radius: 9px;
  font-size: 14px; font-family: inherit; outline: none;
  transition: border-color .2s, box-shadow .2s;
  background: #fafafa; color: #1a1a2e;
}
.form-group input:focus {
  border-color: #6a0dad;
  box-shadow: 0 0 0 3px rgba(106,13,173,.09);
  background: #fff;
}
.form-group input[readonly] { background: #f5f5f7; color: #999; cursor: not-allowed; }

/* Input mot de passe avec oeil */
.input-pw-wrap { position: relative; }
.input-pw-wrap input { width: 100%; padding-right: 42px; }
.input-pw-toggle {
  position: absolute; right: 12px; top: 50%;
  transform: translateY(-50%);
  background: none; border: none; cursor: pointer;
  color: #aaa; padding: 4px;
  transition: color .2s;
}
.input-pw-toggle:hover { color: #6a0dad; }

.form-hint { font-size: 11px; color: #aaa; margin-top: 4px; }

.btn-save {
  display: inline-flex; align-items: center; gap: 8px;
  margin-top: 24px;
  padding: 12px 28px;
  background: #6a0dad; color: #fff;
  border: none; border-radius: 9px;
  font-size: 14px; font-weight: 600;
  cursor: pointer; transition: background .2s, transform .15s;
}
.btn-save:hover { background: #5a0b99; }
.btn-save:active { transform: scale(.98); }

/* ── ALERTES ── */
.alert-success, .alert-error {
  padding: 13px 16px; border-radius: 10px;
  font-size: 13px; margin-bottom: 22px; line-height: 1.5;
}
.alert-success { background: #e6f9ee; color: #1a7a3f; }
.alert-error   { background: #ffe0e0; color: #c00; }

/* ── ACCÈS RAPIDES ── */
.quick-links {
  display: grid; grid-template-columns: 1fr 1fr;
  gap: 12px;
}
.quick-link {
  display: flex; align-items: center; gap: 14px;
  padding: 16px 18px;
  background: #fafafa; border: 1.5px solid #e0e0e8;
  border-radius: 12px; text-decoration: none;
  transition: border-color .2s, box-shadow .2s, background .2s;
}
.quick-link:hover {
  border-color: #6a0dad;
  background: #f0e6fa;
  box-shadow: 0 4px 14px rgba(106,13,173,.08);
}
.quick-link__icon {
  width: 42px; height: 42px;
  background: #f0e6fa; color: #6a0dad;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px; flex-shrink: 0;
}
.quick-link__text strong { display: block; font-size: 13px; color: #1a1a2e; }
.quick-link__text span   { font-size: 12px; color: #888; }

/* ── INFOS MEMBRE ── */
.member-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.member-info-item {
  padding: 14px 16px;
  background: #fafafa; border: 1px solid #eee;
  border-radius: 10px;
}
.member-info-item__label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #aaa; margin-bottom: 4px; }
.member-info-item__value { font-size: 14px; font-weight: 600; color: #1a1a2e; }

/* ── ZONE DANGER ── */
.danger-zone {
  border: 1.5px solid #ffd5d5;
  border-radius: 12px;
  padding: 24px 28px;
  background: #fff8f8;
}
.danger-zone h4 { font-size: 14px; font-weight: 700; color: #c00; margin-bottom: 8px; }
.danger-zone p  { font-size: 13px; color: #666; line-height: 1.6; margin-bottom: 16px; }
.btn-danger {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 10px 20px;
  border: 1.5px solid #c00; color: #c00; background: transparent;
  border-radius: 8px; font-size: 13px; font-weight: 600;
  cursor: pointer; transition: background .2s, color .2s;
}
.btn-danger:hover { background: #c00; color: #fff; }

/* ── PANNEAU ONGLETS ── */
.tab-panel { display: none; }
.tab-panel.active { display: block; }

/* ── RESPONSIVE ── */
@media (max-width: 700px) {
  .stats-row { grid-template-columns: repeat(2, 1fr); }
  .form-grid { grid-template-columns: 1fr; }
  .user-card__inner { flex-direction: column; align-items: flex-start; }
  .account-card { padding: 24px 20px; }
  .quick-links { grid-template-columns: 1fr; }
  .member-info-grid { grid-template-columns: 1fr; }
}
@media (max-width: 460px) {
  .stats-row { grid-template-columns: 1fr 1fr; }
}
</style>

<!-- HERO -->
<div class="account-hero">
  <div class="account-hero__inner">
    <h2>Bienvenue, <?= htmlspecialchars($user['prenom']) ?></h2>
    <p>Gérez votre profil et accédez à tous vos liens personnalisés</p>
  </div>
</div>

<!-- CARTE UTILISATEUR -->
<div class="user-card">
  <div class="user-card__inner">
    <div class="user-card__avatar">
      <?= mb_strtoupper(mb_substr($user['prenom'], 0, 1)) . mb_strtoupper(mb_substr($user['nom'], 0, 1)) ?>
    </div>
    <div class="user-card__info">
      <div class="user-card__name"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></div>
      <div class="user-card__email"><?= htmlspecialchars($user['email']) ?></div>
      <div class="user-card__badges">
        <span class="badge badge--green"> Compte vérifié</span>
        <span class="badge badge--violet">Membre depuis <?= date('Y', strtotime($user['created_at'])) ?></span>
      </div>
    </div>
    <div class="user-card__actions">
      <a href="favoris.php" class="btn-outline-violet"> Mes favoris</a>
      <a href="?logout=1" class="btn-danger-outline"
         onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">Déconnexion</a>
    </div>
  </div>
</div>

<!-- STATS -->
<div class="stats-row">
  <div class="stat-card">
    
    <div class="stat-card__value"><?= $nb_favoris ?></div>
    <div class="stat-card__label">Liens favoris</div>
  </div>
  <div class="stat-card">
    
    <div class="stat-card__value">6</div>
    <div class="stat-card__label">Catégories</div>
  </div>
  <div class="stat-card">
    
    <div class="stat-card__value"><?= date('d/m', strtotime($user['created_at'])) ?></div>
    <div class="stat-card__label">Inscrit le <?= date('d/m/Y', strtotime($user['created_at'])) ?></div>
  </div>
  <div class="stat-card">
    
    <div class="stat-card__value" style="font-size:18px;">Actif</div>
    <div class="stat-card__label">Statut du compte</div>
  </div>
</div>

<!-- CONTENU AVEC ONGLETS -->
<div class="account-container">

  <?php if ($msg_succes): ?>
    <div class="alert-success"><?= htmlspecialchars($msg_succes) ?></div>
  <?php endif; ?>
  <?php if ($msg_erreur): ?>
    <div class="alert-error"><?= htmlspecialchars($msg_erreur) ?></div>
  <?php endif; ?>

  <!-- Onglets -->
  <div class="account-tabs">
    <a href="?tab=profil"    class="account-tab <?= $active_tab === 'profil'    ? 'active' : '' ?>">Mon profil</a>
    <a href="?tab=securite"  class="account-tab <?= $active_tab === 'securite'  ? 'active' : '' ?>">Sécurité</a>
    <a href="?tab=activite"  class="account-tab <?= $active_tab === 'activite'  ? 'active' : '' ?>">Activité</a>
    <a href="?tab=raccourcis" class="account-tab <?= $active_tab === 'raccourcis' ? 'active' : '' ?>">Accès rapide</a>
  </div>

  <!-- ── Onglet Profil ── -->
  <div class="tab-panel <?= $active_tab === 'profil' ? 'active' : '' ?>">

    <div class="account-card">
      <h3>Mes informations personnelles</h3>
      <p class="card-desc">Modifiez votre nom, prénom ou adresse email à tout moment.</p>

      <form method="POST" action="?tab=profil">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="update_profil">
        <div class="form-grid">
          <div class="form-group">
            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom"
                   value="<?= htmlspecialchars($user['prenom']) ?>" required>
          </div>
          <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom"
                   value="<?= htmlspecialchars($user['nom']) ?>" required>
          </div>
          <div class="form-group full">
            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($user['email']) ?>" required>
          </div>
        </div>
        <button type="submit" class="btn-save">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
            <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
          </svg>
          Enregistrer les modifications
        </button>
      </form>
    </div>

  </div>

  <!-- ── Onglet Sécurité ── -->
  <div class="tab-panel <?= $active_tab === 'securite' ? 'active' : '' ?>">

    <div class="account-card">
      <h3>Changer le mot de passe</h3>
      <p class="card-desc">Choisissez un mot de passe fort d'au moins 8 caractères.</p>

      <form method="POST" action="?tab=securite">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="update_mdp">
        <div class="form-grid">
          <div class="form-group full">
            <label for="mdp_actuel">Mot de passe actuel</label>
            <div class="input-pw-wrap">
              <input type="password" id="mdp_actuel" name="mdp_actuel" placeholder="••••••••" autocomplete="current-password" required>
              <button type="button" class="input-pw-toggle" data-target="mdp_actuel" aria-label="Afficher">Voir</button>
            </div>
          </div>
          <div class="form-group">
            <label for="new_mdp">Nouveau mot de passe</label>
            <div class="input-pw-wrap">
              <input type="password" id="new_mdp" name="new_mdp" placeholder="8 caractères minimum" minlength="8" autocomplete="new-password" required>
              <button type="button" class="input-pw-toggle" data-target="new_mdp" aria-label="Afficher">Voir</button>
            </div>
            <span class="form-hint" id="pwStrength"></span>
          </div>
          <div class="form-group">
            <label for="confirm_mdp">Confirmer le nouveau mot de passe</label>
            <div class="input-pw-wrap">
              <input type="password" id="confirm_mdp" name="confirm_mdp" placeholder="Répétez le mot de passe" minlength="8" autocomplete="new-password" required>
              <button type="button" class="input-pw-toggle" data-target="confirm_mdp" aria-label="Afficher">Voir</button>
            </div>
          </div>
        </div>
        <button type="submit" class="btn-save">Mettre à jour le mot de passe</button>
      </form>
    </div>

    <div class="danger-zone">
      <h4> Zone de danger</h4>
      <p>La suppression de votre compte est définitive. Toutes vos données (favoris, préférences) seront effacées et ne pourront pas être récupérées.</p>
      <button class="btn-danger"
              onclick="if(confirm('Êtes-vous sûr de vouloir supprimer définitivement votre compte ?')) alert('Fonctionnalité à venir – contactez le support.')">
        Supprimer mon compte
      </button>
    </div>

  </div>

  <!-- ── Onglet Activité ── -->
  <div class="tab-panel <?= $active_tab === 'activite' ? 'active' : '' ?>">

    <div class="account-card">
      <h3>Informations du compte</h3>
      <p class="card-desc">Récapitulatif de votre compte et de votre activité.</p>
      <div class="member-info-grid">
        <div class="member-info-item">
          <div class="member-info-item__label">Membre depuis</div>
          <div class="member-info-item__value"><?= date('d/m/Y', strtotime($user['created_at'])) ?></div>
        </div>
        <div class="member-info-item">
          <div class="member-info-item__label">Statut</div>
          <div class="member-info-item__value" style="color:#1a7a3f"> Actif</div>
        </div>
        <div class="member-info-item">
          <div class="member-info-item__label">Liens en favoris</div>
          <div class="member-info-item__value"><?= $nb_favoris ?> lien<?= $nb_favoris > 1 ? 's' : '' ?></div>
        </div>
        <div class="member-info-item">
          <div class="member-info-item__label">Email</div>
          <div class="member-info-item__value" style="font-size:13px"><?= htmlspecialchars($user['email']) ?></div>
        </div>
        <div class="member-info-item">
          <div class="member-info-item__label">Catégories accessibles</div>
          <div class="member-info-item__value">6 / 6</div>
        </div>
        <div class="member-info-item">
          <div class="member-info-item__label">Type de connexion</div>
          <div class="member-info-item__value">Email &amp; mot de passe</div>
        </div>
      </div>
    </div>

  </div>

  <!-- ── Onglet Accès rapide ── -->
  <div class="tab-panel <?= $active_tab === 'raccourcis' ? 'active' : '' ?>">

    <div class="account-card">
      <h3>Accès rapide aux démarches</h3>
      <p class="card-desc">Retrouvez directement les catégories les plus utilisées.</p>
      <div class="quick-links">
        <a href="identite.php" class="quick-link">
          <div class="quick-link__text"><strong>Identité</strong><span>CNI, passeport, état civil</span></div>
        </a>
        <a href="social-sante.php" class="quick-link">
          <div class="quick-link__text"><strong>Social &amp; Santé</strong><span>CAF, Ameli, handicap</span></div>
        </a>
        <a href="travail-retraite.php" class="quick-link">
          <div class="quick-link__text"><strong>Travail &amp; Retraite</strong><span>France Travail, retraite</span></div>
        </a>
        <a href="logement.php" class="quick-link">
          <div class="quick-link__text"><strong>Logement</strong><span>APL, déménagement</span></div>
        </a>
        <a href="finances.php" class="quick-link">
          <div class="quick-link__text"><strong>Finances</strong><span>Impôts, banque, aides</span></div>
        </a>
        <a href="droits-services.php" class="quick-link">
          <div class="quick-link__text"><strong>Droits &amp; Services</strong><span>Justice, administration</span></div>
        </a>
        <a href="favoris.php" class="quick-link">
          <div class="quick-link__text"><strong>Mes favoris</strong><span><?= $nb_favoris ?> lien<?= $nb_favoris > 1 ? 's' : '' ?> enregistré<?= $nb_favoris > 1 ? 's' : '' ?></span></div>
        </a>
        <a href="configuration-cookies.php" class="quick-link">
          <div class="quick-link__text"><strong>Cookies</strong><span>Gérer mes préférences</span></div>
        </a>
      </div>
    </div>

  </div>

</div>

<script>
(function () {
  // ── Afficher/masquer mot de passe ──
  document.querySelectorAll('.input-pw-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.target);
      if (!input) return;
      input.type = input.type === 'password' ? 'text' : 'password';
      btn.textContent = input.type === 'password' ? '\u{1F441}' : '\u{1F648}';
    });
  });

  // ── Indicateur de force du mot de passe ──
  const newMdp    = document.getElementById('new_mdp');
  const pwStrength = document.getElementById('pwStrength');
  if (newMdp && pwStrength) {
    newMdp.addEventListener('input', () => {
      const v = newMdp.value;
      let score = 0;
      if (v.length >= 8)  score++;
      if (v.length >= 12) score++;
      if (/[A-Z]/.test(v)) score++;
      if (/[0-9]/.test(v)) score++;
      if (/[^a-zA-Z0-9]/.test(v)) score++;

      const levels = ['', 'Très faible', 'Faible', 'Moyen', 'Fort', 'Très fort'];
      const colors = ['', '#c00', '#e07b00', '#b89000', '#1a7a3f', '#6a0dad'];
      pwStrength.textContent  = v ? (levels[score] || levels[1]) : '';
      pwStrength.style.color  = v ? colors[score] : '';
    });
  }

  // ── Vérification correspondance mdp ──
  const confirmMdp = document.getElementById('confirm_mdp');
  if (newMdp && confirmMdp) {
    confirmMdp.addEventListener('input', () => {
      if (confirmMdp.value && newMdp.value !== confirmMdp.value) {
        confirmMdp.style.borderColor = '#c00';
      } else {
        confirmMdp.style.borderColor = '';
      }
    });
  }
})();
</script>

<?php include 'footer.php'; ?>