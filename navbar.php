<?php
// navbar.php - Navbar universelle
if (!isset($user_connecte)) {
    $user_connecte = !empty($_SESSION['user_id']);
}

$initiales = '';
if ($user_connecte) {
    $prenom = $_SESSION['user_prenom'] ?? '';
    $nom    = $_SESSION['user_nom']    ?? '';
    $initiales = strtoupper(
        mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1)
    );
}
?>

<!-- NAVBAR -->
<header class="navbar">
  <div class="navbar__inner">

    <!-- Gauche : avatar + label statut -->
    <div class="navbar__left">
      <?php if ($user_connecte && $initiales): ?>
        <a href="mon-compte.php" class="navbar__avatar" title="Mon compte">
          <?= htmlspecialchars($initiales) ?>
        </a>
        <span class="navbar__status-label navbar__status-label--on">Connecté</span>
      <?php else: ?>
        <a href="connexion.php" class="navbar__avatar navbar__avatar--guest" title="Non connecté">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#999">
            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
          </svg>
        </a>
        <span class="navbar__status-label">Pas connecté</span>
      <?php endif; ?>
    </div>

    <!-- Centre : Logo -->
    <a href="index.php" class="navbar__logo">
      <img src="logo-horizontal.png" alt="Liens Démarches">
    </a>

    <!-- Droite : Burger -->
    <div class="navbar__right">
      <button class="navbar__burger" id="burgerBtn" aria-label="Ouvrir le menu" aria-expanded="false">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>

  </div>
</header>

<!-- MENU BURGER — Panneau latéral -->
<div class="burger-overlay" id="burgerOverlay"></div>

<nav class="burger-panel" id="burgerPanel" aria-hidden="true">

  <button class="burger-panel__close" id="burgerClose" aria-label="Fermer le menu">✕</button>

  <div class="burger-panel__logo">
    <img src="logo-horizontal.png" alt="Liens Démarches">
  </div>

  <ul class="burger-panel__nav">
    <li><a href="index.php">Accueil</a></li>
    <li><a href="identite.php">Identité</a></li>
    <li><a href="social-sante.php">Social &amp; Santé</a></li>
    <li><a href="travail-retraite.php">Travail &amp; Retraite</a></li>
    <li><a href="logement.php">Logement</a></li>
    <li><a href="finances.php">Finances</a></li>
    <li><a href="droits-services.php">Droits &amp; Services</a></li>
  </ul>

  <hr class="burger-panel__divider">

  <ul class="burger-panel__actions">
    <li>
      <a href="mon-compte.php" class="burger-panel__action-link">
        <span class="burger-panel__action-text">Mon compte</span>
        <span class="burger-panel__action-icon"></span>
      </a>
    </li>
    <li>
      <a href="favoris.php" class="burger-panel__action-link">
        <span class="burger-panel__action-text">Aller dans mes favoris</span>
        <span class="burger-panel__action-icon"></span>
      </a>
    </li>
  </ul>

  <hr class="burger-panel__divider">

  <div class="burger-panel__auth">
  <?php if ($user_connecte): ?>
    <p class="burger-panel__auth-name">
      Bonjour, <?= htmlspecialchars($_SESSION['user_prenom'] ?? '') ?> !
    </p>
    <a href="mon-compte.php?logout=1"
       style="display:block; width:100%; padding:12px 18px;
              background:transparent; color:#ffffff;
              border:2px solid rgba(255,255,255,0.5);
              border-radius:8px; font-size:14px; font-weight:600;
              text-align:center; text-decoration:none;
              transition:background .2s, border-color .2s;">
      Déconnexion
    </a>
  <?php else: ?>
    <a href="connexion.php"
       style="display:block; width:100%; padding:12px 18px;
              background:transparent; color:#ffffff;
              border:2px solid rgba(255,255,255,0.5);
              border-radius:8px; font-size:14px; font-weight:600;
              text-align:center; text-decoration:none;
              margin-bottom:10px;
              transition:background .2s, border-color .2s;">
      Connexion
    </a>
    <a href="inscription.php"
       style="display:block; width:100%; padding:12px 18px;
              background:transparent; color:#ffffff;
              border:2px solid rgba(255,255,255,0.5);
              border-radius:8px; font-size:14px; font-weight:600;
              text-align:center; text-decoration:none;
              transition:background .2s, border-color .2s;">
      Créer un compte
    </a>
  <?php endif; ?>
</div>

</nav>

<script src="navbar.js"></script>

<style>
  .navbar__inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
  }

  .navbar__left,
  .navbar__right {
    display: flex;
    align-items: center;
    min-width: 50px;
  }

  .navbar__logo {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
  }

  .navbar__avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2.5px solid #22c55e;
    background-color: #f0fdf4;
    color: #16a34a;
    font-weight: 700;
    font-size: 0.85rem;
    text-decoration: none;
    letter-spacing: 0.5px;
    transition: box-shadow 0.2s;
  }

  .navbar__avatar:hover {
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.25);
  }

  .navbar__avatar--guest {
    border-color: #999;
    background-color: #f5f5f5;
  }

  .navbar__status-label {
    font-size: 11px;
    color: rgba(255,255,255,0.7);
    margin-left: 6px;
    white-space: nowrap;
  }
  .navbar__status-label--on {
    color: #22c55e;
    font-weight: 600;
  }

  .navbar__nav,
  .navbar__actions {
    display: none !important;
  }

  /* ── Burger panel : styles fixes sur toutes les pages ── */
  .burger-panel {
    position: fixed !important;
    top: 0 !important;
    right: 0 !important;
    width: 320px !important;
    max-width: 90vw !important;
    height: 100% !important;
    z-index: 300 !important;
    background: #6a0dad !important;
    color: #fff !important;
    display: flex !important;
    flex-direction: column !important;
    padding: 20px 28px 36px !important;
    transform: translateX(100%) !important;
    transition: transform .32s cubic-bezier(.4,0,.2,1) !important;
    overflow-y: auto !important;
    text-align: left !important;
    align-items: flex-start !important;
  }
  .burger-panel.open {
    transform: translateX(0) !important;
  }

  .burger-panel__close {
    align-self: flex-end !important;
    background: none !important;
    border: none !important;
    cursor: pointer !important;
    font-size: 20px !important;
    color: #fff !important;
    padding: 4px !important;
    margin-bottom: 24px !important;
    font-weight: 400 !important;
  }

  .burger-panel__logo {
    margin-bottom: 24px !important;
    text-align: left !important;
  }
  .burger-panel__logo img {
  height: 40px;
  }

  .burger-panel__nav {
    list-style: none !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 4px !important;
    margin-bottom: 20px !important;
    width: 100% !important;
    padding: 0 !important;
    text-align: left !important;
  }
  .burger-panel__nav a {
    display: block !important;
    font-size: 15px !important;
    font-weight: 500 !important;
    color: rgba(255,255,255,.8) !important;
    padding: 10px 14px !important;
    border-radius: 8px !important;
    text-decoration: none !important;
    text-align: left !important;
  }
  .burger-panel__nav a:hover {
    background: rgba(255,255,255,.12) !important;
    color: #fff !important;
  }

  .burger-panel__divider {
    border: none !important;
    border-top: 1px solid rgba(255,255,255,.2) !important;
    margin: 8px 0 16px !important;
    width: 100% !important;
  }

  .burger-panel__actions {
    list-style: none !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 4px !important;
    width: 100% !important;
    padding: 0 !important;
    margin-bottom: 0 !important;
    flex: unset !important;
  }

  .burger-panel__action-link {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-start !important;
    padding: 10px 14px !important;
    border-radius: 8px !important;
    text-decoration: none !important;
    background: none !important;
    transition: background .22s !important;
  }
  .burger-panel__action-link:hover {
    background: rgba(255,255,255,.12) !important;
  }

  .burger-panel__action-text {
    font-size: 15px !important;
    font-weight: 500 !important;
    color: rgba(255,255,255,.8) !important;
    line-height: 1.2 !important;
  }

  .burger-panel__action-icon {
    display: none !important;
  }

  .burger-panel__auth {
    margin-top: 16px !important;
    padding-top: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 10px !important;
    width: 100% !important;
  }
  .burger-panel__auth-name {
    font-size: 14px !important;
    color: rgba(255,255,255,.7) !important;
    margin-bottom: 4px !important;
    text-align: left !important;
  }
</style>