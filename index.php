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
        <img src="img/icone-question.png" alt="?" class="why__icon">
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
      <img src="img/how-laptop.jpg" alt="Utilisation du site" class="how__img">
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
