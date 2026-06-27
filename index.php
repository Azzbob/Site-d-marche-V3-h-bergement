<?php
// ============================================================
//  index.php  —  Page d'accueil Liens Démarches
// ============================================================
session_start();
require_once 'db.php';
require_once 'auto-login.php';

$user_connecte = !empty($_SESSION['user_id']);
$prenom        = htmlspecialchars($_SESSION['user_prenom'] ?? '');

// --- Gestion ajout/retrait favori (même logique que identite.php) ---
if ($user_connecte && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_favori'])) {
    $lienId = (int) $_POST['lien_id'];
    $stmtCheck = $pdo->prepare('SELECT 1 FROM favoris WHERE user_id = ? AND lien_id = ?');
    $stmtCheck->execute([$_SESSION['user_id'], $lienId]);
    if ($stmtCheck->fetch()) {
        $pdo->prepare('DELETE FROM favoris WHERE user_id = ? AND lien_id = ?')->execute([$_SESSION['user_id'], $lienId]);
    } else {
        $pdo->prepare('INSERT IGNORE INTO favoris (user_id, lien_id) VALUES (?, ?)')->execute([$_SESSION['user_id'], $lienId]);
    }
    header('Location: index.php#quest-ce');
    exit;
}

// --- Récupère tous les liens actifs (pour le carrousel ET la liste de recherche) ---
$stmt2 = $pdo->query(
    "SELECT l.*, c.nom AS cat_nom
     FROM liens l
     LEFT JOIN categories c ON c.id = l.categorie_id
     WHERE l.actif = 1
     ORDER BY l.ordre ASC"
);
$tous_liens = $stmt2->fetchAll();

// --- Catégories pour le filtre déroulant (DISTINCT évite les doublons si la table a des entrées répétées) ---
$categories = $pdo->query('SELECT DISTINCT id, nom, icone, ordre FROM categories ORDER BY ordre ASC')->fetchAll();

// --- Favoris de l'utilisateur connecté ---
$favoris = [];
if ($user_connecte) {
    $stmtFav = $pdo->prepare('SELECT lien_id FROM favoris WHERE user_id = ?');
    $stmtFav->execute([$_SESSION['user_id']]);
    $favoris = array_column($stmtFav->fetchAll(), 'lien_id');
}

// --- Liste dédupliquée par logo pour le carrousel (un seul exemplaire par logo) ---
$logos_vus = [];
$carousel_liens = [];
foreach ($tous_liens as $lien) {
    if (!empty($lien['logo']) && !in_array($lien['logo'], $logos_vus, true)) {
        $logos_vus[] = $lien['logo'];
        $carousel_liens[] = $lien;
    }
}

$page_title = 'Liens Démarches – Toutes vos démarches administratives en un seul endroit';
$page_desc  = 'Liens Démarches – Accédez facilement à toutes vos démarches administratives en France : CAF, Urssaf, Assurance Maladie, France Travail, impôts et bien plus.';
$extra_css  = '<link rel="stylesheet" href="index.css">';
$extra_js   = '<script src="index.js"></script>';

include 'header.php';
?>


<!-- ══════════════════════════════════════════
     HERO — Bienvenue
══════════════════════════════════════════ -->
<section class="hero">
  <div class="hero__inner">
    <div class="hero__text">
      <p class="hero__eyebrow">BIENVENUE SUR LA PAGE LIENS DÉMARCHES</p>
      <h1 class="hero__title">
        <?php if ($user_connecte): ?>
          Bonjour <?= $prenom ?>, retrouvez<br>toutes vos démarches
        <?php else: ?>
          Toutes vos démarches<br>administratives en ligne
        <?php endif; ?>
      </h1>
      <p class="hero__sub">
        Un seul endroit pour accéder à tous vos organismes : Urssaf, Retraite,
        CAF, Impôts, Ameli et bien plus encore.
      </p>
      <a href="#quest-ce" class="btn btn--primary btn--lg">Découvrir →</a>
    </div>
    <div class="hero__visual">
      <img src="logo.png" alt="Démarches en ligne" class="hero__img">
    </div>
  </div>
</section>


<!-- ══════════════════════════════════════════
     POURQUOI CE SITE ?
══════════════════════════════════════════ -->
<section class="section section--why">
  <div class="container container--two-col">

    <div class="why__visual">
      <div class="why__icon-wrap">
        <!-- Icône "Pourquoi ce site ?" — SVG inline (remplace img/icone-question.png) -->
        <svg class="why__icon" viewBox="0 0 180 180" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Pourquoi ce site ?">
          <!-- Fond cercle dégradé -->
          <defs>
            <radialGradient id="gWhy" cx="40%" cy="35%" r="70%">
              <stop offset="0%" stop-color="#9b30ff" stop-opacity="0.18"/>
              <stop offset="100%" stop-color="#3b006e" stop-opacity="0.06"/>
            </radialGradient>
          </defs>
          <circle cx="90" cy="90" r="82" fill="url(#gWhy)" stroke="rgba(106,13,173,0.15)" stroke-width="1.5"/>

          <!-- Point d'interrogation stylisé -->
          <text x="90" y="118" text-anchor="middle" font-size="90" font-family="'Segoe UI',system-ui,sans-serif" font-weight="700" fill="none" stroke="rgba(106,13,173,0.18)" stroke-width="3">?</text>
          <text x="90" y="118" text-anchor="middle" font-size="90" font-family="'Segoe UI',system-ui,sans-serif" font-weight="700" fill="rgba(106,13,173,0.22)">?</text>

          <!-- Petites icônes autour -->
          <!-- Document haut-gauche -->
          <rect x="22" y="28" width="24" height="30" rx="3" fill="rgba(106,13,173,0.10)" stroke="rgba(106,13,173,0.25)" stroke-width="1.2"/>
          <rect x="27" y="35" width="14" height="2" rx="1" fill="rgba(106,13,173,0.3)"/>
          <rect x="27" y="40" width="10" height="2" rx="1" fill="rgba(106,13,173,0.22)"/>
          <rect x="27" y="45" width="12" height="2" rx="1" fill="rgba(106,13,173,0.18)"/>

          <!-- Euro haut-droite -->
          <circle cx="148" cy="40" r="18" fill="rgba(155,48,255,0.09)" stroke="rgba(155,48,255,0.22)" stroke-width="1.2"/>
          <text x="148" y="47" text-anchor="middle" font-size="18" font-family="system-ui" fill="rgba(106,13,173,0.35)">€</text>

          <!-- Maison bas-gauche -->
          <path d="M38 150l-14-12h5V126h18v12h5z" fill="rgba(59,0,110,0.09)" stroke="rgba(59,0,110,0.22)" stroke-width="1.2" stroke-linejoin="round"/>

          <!-- Cœur bas-droite -->
          <path d="M148 142s-10-6.5-10-12a5.5 5.5 0 0 1 10-2.7 5.5 5.5 0 0 1 10 2.7c0 5.5-10 12-10 12z" fill="rgba(155,48,255,0.10)" stroke="rgba(155,48,255,0.25)" stroke-width="1.2"/>
        </svg>
      </div>
    </div>

    <div class="why__content">
      <span class="section__label">POURQUOI CE SITE ?</span>
      <p class="why__text">
        Nous vous référençons l'ensemble des démarches administratives ne savant pas bien
        comment y accéder. Ceci regroupe notamment des organismes publics d'aides pouvant
        aider la population française en leur expliquant et en leur facilitant l'accès à tous
        ces services publics essentiels.
      </p>
    </div>

  </div>
</section>


<!-- ══════════════════════════════════════════
     COMMENT L'UTILISER ?
══════════════════════════════════════════ -->
<!-- ══════════════════════════════════════════
     COMMENT L'UTILISER ?
══════════════════════════════════════════ -->
<section class="section section--how">
  <div class="container container--two-col">

    <div class="how__content">
      <span class="section__label">COMMENT L'UTILISER ?</span>
      <p class="how__text">
        Naviguez parmi nos différentes rubriques, choisissez le service
        correspondant et accédez en un seul clic directement à la plateforme
        officielle concernée. En cas de besoin, vous trouverez des informations
        claires sur chaque organisme pour vous aider à effectuer vos démarches.
        Notre objectif est simple : vous permettre de gagner du temps et de faciliter
        vos démarches auprès des services publics essentiels.
      </p>
    </div>

    <div class="how__visual">
      <!-- Illustration "Comment utiliser" — SVG inline (remplace img/how-laptop.jpg) -->
      <svg class="how__img" viewBox="0 0 560 280" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Illustration utilisation du site">
        <defs>
          <linearGradient id="gScreen" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#f8f3ff"/>
            <stop offset="100%" stop-color="#ede0ff"/>
          </linearGradient>
          <linearGradient id="gBase" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#c9a0f0"/>
            <stop offset="100%" stop-color="#a060d0"/>
          </linearGradient>
          <filter id="shadowLaptop" x="-10%" y="-10%" width="120%" height="140%">
            <feDropShadow dx="0" dy="12" stdDeviation="18" flood-color="rgba(106,13,173,0.22)"/>
          </filter>
        </defs>

        <!-- Arrière-plan doux -->
        <rect width="560" height="280" rx="16" fill="rgba(240,230,250,0.35)"/>

        <!-- Laptop — corps -->
        <g filter="url(#shadowLaptop)">
          <!-- Écran -->
          <rect x="100" y="20" width="360" height="210" rx="12" fill="#2a0060" stroke="#6a0dad" stroke-width="2"/>
          <!-- Écran intérieur -->
          <rect x="114" y="34" width="332" height="182" rx="6" fill="url(#gScreen)"/>

          <!-- Contenu de l'écran — mini navbar -->
          <rect x="114" y="34" width="332" height="28" rx="6" fill="#6a0dad"/>
          <rect x="114" y="56" width="332" height="6" rx="0" fill="#6a0dad"/>
          <circle cx="130" cy="48" r="5" fill="rgba(255,255,255,0.4)"/>
          <rect x="150" y="44" width="50" height="8" rx="4" fill="rgba(255,255,255,0.3)"/>
          <rect x="210" y="44" width="40" height="8" rx="4" fill="rgba(255,255,255,0.2)"/>
          <rect x="380" y="42" width="55" height="12" rx="6" fill="rgba(255,255,255,0.15)"/>

          <!-- Barre de recherche -->
          <rect x="140" y="76" width="220" height="22" rx="11" fill="white" stroke="rgba(106,13,173,0.2)" stroke-width="1"/>
          <circle cx="347" cy="87" r="9" fill="#6a0dad"/>
          <text x="347" y="92" text-anchor="middle" font-size="10" fill="white">→</text>

          <!-- Cards liens -->
          <rect x="130" y="112" width="300" height="36" rx="8" fill="white" stroke="rgba(106,13,173,0.12)" stroke-width="1"/>
          <rect x="142" y="121" width="28" height="18" rx="4" fill="rgba(106,13,173,0.08)" stroke="rgba(106,13,173,0.15)" stroke-width="0.8"/>
          <rect x="178" y="124" width="70" height="7" rx="3" fill="rgba(106,13,173,0.18)"/>
          <rect x="178" y="134" width="50" height="5" rx="2.5" fill="rgba(106,13,173,0.10)"/>
          <rect x="360" y="124" width="55" height="16" rx="8" fill="rgba(106,13,173,0.08)"/>

          <rect x="130" y="154" width="300" height="36" rx="8" fill="white" stroke="rgba(106,13,173,0.12)" stroke-width="1"/>
          <rect x="142" y="163" width="28" height="18" rx="4" fill="rgba(155,48,255,0.08)" stroke="rgba(155,48,255,0.15)" stroke-width="0.8"/>
          <rect x="178" y="166" width="85" height="7" rx="3" fill="rgba(106,13,173,0.15)"/>
          <rect x="178" y="176" width="55" height="5" rx="2.5" fill="rgba(106,13,173,0.08)"/>
          <rect x="360" y="166" width="55" height="16" rx="8" fill="rgba(245,158,11,0.12)"/>
          <text x="387" y="178" text-anchor="middle" font-size="8" fill="rgba(180,120,0,0.7)">★ favori</text>

          <!-- Socle laptop -->
          <rect x="130" y="232" width="300" height="14" rx="4" fill="url(#gBase)"/>
          <rect x="180" y="246" width="200" height="6" rx="3" fill="#b88de0"/>
        </g>

        <!-- Curseur souris -->
        <g transform="translate(310,155)">
          <path d="M0 0 L0 18 L4.5 14 L7 20 L9 19 L6.5 13 L12 13 Z" fill="white" stroke="#6a0dad" stroke-width="1.2" stroke-linejoin="round"/>
        </g>

        <!-- Étoiles décoratives -->
        <circle cx="80" cy="60" r="4" fill="rgba(106,13,173,0.2)"/>
        <circle cx="490" cy="200" r="5" fill="rgba(155,48,255,0.18)"/>
        <circle cx="70" cy="220" r="3" fill="rgba(59,0,110,0.15)"/>
        <circle cx="500" cy="50" r="3" fill="rgba(106,13,173,0.15)"/>
      </svg>
    </div>

  </div>
</section>


<!-- ══════════════════════════════════════════
     NOS LIENS — Carrousel logos
══════════════════════════════════════════ -->
<section class="section section--links" id="nos-liens">
  <div class="container">
    <span class="section__label section__label--center">NOS LIENS</span>
  </div>

  <div class="carousel-wrapper">
    <button class="carousel__btn carousel__btn--prev" id="prevBtn" aria-label="Précédent">&#8249;</button>

    <div class="carousel" id="carousel">
      <div class="carousel__track" id="carouselTrack">
        <?php foreach ($carousel_liens as $lien): ?>
        <div class="carousel__item" title="<?= htmlspecialchars($lien['titre']) ?>">
          <img src="<?= htmlspecialchars($lien['logo']) ?>"
               alt="<?= htmlspecialchars($lien['titre']) ?>">
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <button class="carousel__btn carousel__btn--next" id="nextBtn" aria-label="Suivant">&#8250;</button>
  </div>
</section>


<!-- ══════════════════════════════════════════
     QU'EST-CE QUE VOUS ATTENDEZ ?
══════════════════════════════════════════ -->
<section class="section section--cta" id="quest-ce">
  <div class="container">
    <span class="section__label section__label--center">QU'EST-CE QUE VOUS ATTENDEZ ?</span>

    <!-- Barre de recherche + filtre catégories -->
    <div class="search-bar">
      <div class="search-bar__input-wrap">
        <input type="text" id="searchInput" placeholder="Démarche, organisme, mot-clé..." class="search-bar__input">
      </div>
      <button class="search-bar__btn" id="searchBtn" title="Rechercher" aria-label="Rechercher">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <circle cx="11" cy="11" r="7"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
      </button>

      <details class="filter-dropdown" id="filterDropdown">
        <summary class="filter-dropdown__btn" id="filterToggleBtn">
          Filtre
          <span class="filter-dropdown__count" id="filterCount">0</span>
          <svg class="filter-dropdown__chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </summary>
        <div class="filter-dropdown__panel" id="filterPanel">
          <?php foreach ($categories as $cat): ?>
            <label class="filter-dropdown__option">
              <input type="checkbox" value="<?= htmlspecialchars($cat['nom']) ?>">
              <?= htmlspecialchars($cat['nom']) ?>
            </label>
          <?php endforeach; ?>
          <button type="button" class="filter-dropdown__reset" id="filterResetBtn">Réinitialiser les filtres</button>
        </div>
      </details>
    </div>

    <!-- Liste des liens (filtrable par recherche + catégorie) -->
    <div class="offres" id="offresContainer">
      <?php foreach ($tous_liens as $lien): ?>
        <?php
          $estFavori = in_array($lien['id'], $favoris);
          $recherche = strtolower($lien['titre'] . ' ' . ($lien['description'] ?? '') . ' ' . ($lien['mots_cles'] ?? '') . ' ' . ($lien['cat_nom'] ?? ''));
        ?>
        <div class="lien-card"
             data-cat="<?= htmlspecialchars($lien['cat_nom'] ?? '') ?>"
             data-search="<?= htmlspecialchars($recherche) ?>">
          <div class="lien-card__title"><?= htmlspecialchars($lien['titre']) ?></div>
          <div class="lien-card__body">

            <div class="lien-card__left">
              <?php if ($lien['logo']): ?>
                <img src="<?= htmlspecialchars($lien['logo']) ?>"
                     alt="<?= htmlspecialchars($lien['titre']) ?>"
                     class="lien-card__logo">
              <?php else: ?>
                <?php
                  // Initiales : première lettre de chaque mot (max 2)
                  $mots = preg_split('/\s+/', $lien['titre']);
                  $initiales = mb_strtoupper(
                      count($mots) >= 2
                          ? mb_substr($mots[0], 0, 1) . mb_substr($mots[1], 0, 1)
                          : mb_substr($mots[0], 0, 2)
                  );
                ?>
                <div class="lien-card__logo-placeholder"><?= htmlspecialchars($initiales) ?></div>
              <?php endif; ?>

              <div class="lien-card__info">
                <span class="lien-card__cat"><?= htmlspecialchars($lien['cat_nom'] ?? '') ?></span>
                <a href="<?= htmlspecialchars($lien['url']) ?>"
                   target="_blank" rel="noopener noreferrer"
                   class="lien-card__url">
                  <?= htmlspecialchars($lien['description'] ?: $lien['url']) ?>
                </a>
              </div>
            </div>

            <?php if ($user_connecte): ?>
              <form method="POST" style="margin:0">
                <input type="hidden" name="lien_id" value="<?= $lien['id'] ?>">
                <button type="submit" name="toggle_favori"
                        class="lien-card__fav <?= $estFavori ? 'lien-card__fav--active' : '' ?>">
                  <span class="lien-card__fav-icon"><?= $estFavori ? '★' : '☆' ?></span>
                  <?= $estFavori ? 'dans vos favoris' : 'ajouter au favoris' ?>
                </button>
              </form>
            <?php else: ?>
              <a href="connexion.php" class="lien-card__fav" title="Connectez-vous pour ajouter aux favoris">
                <span class="lien-card__fav-icon">☆</span>
                ajouter au favoris
              </a>
            <?php endif; ?>

          </div>
        </div>
      <?php endforeach; ?>

      <p class="offres__empty" id="offresEmpty" style="display:none">Aucun lien ne correspond à votre recherche.</p>
      <p class="offres__hint" id="offresHint" style="display:none"></p>

      <?php if (empty($tous_liens)): ?>
        <p class="offres__empty">Aucun lien disponible pour le moment.</p>
      <?php endif; ?>
    </div>

  </div>
</section>


<?php include 'footer.php'; ?>