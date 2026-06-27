<?php
// ============================================================
//  social-sante.php  —  Page catégorie Social & Santé
// ============================================================
session_start();
require_once 'db.php';
require_once 'auto-login.php';

$user_connecte = !empty($_SESSION['user_id']);

// Récupère tous les liens de la catégorie "Social & Santé"
$stmt = $pdo->prepare(
    "SELECT l.*, c.nom AS cat_nom
     FROM liens l
     LEFT JOIN categories c ON c.id = l.categorie_id
     WHERE l.actif = 1 AND c.nom = 'Social & Santé'
     ORDER BY l.ordre ASC"
);
$stmt->execute();
$liens = $stmt->fetchAll();

// Récupère les favoris de l'utilisateur connecté
$favoris = [];
if ($user_connecte) {
    $stmtFav = $pdo->prepare(
        "SELECT lien_id FROM favoris WHERE user_id = ?"
    );
    $stmtFav->execute([$_SESSION['user_id']]);
    $favoris = array_column($stmtFav->fetchAll(), 'lien_id');
}

// Gestion ajout/retrait favori via POST
if ($user_connecte && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_favori'])) {
    $lienId = (int) $_POST['lien_id'];
    if (in_array($lienId, $favoris)) {
        $pdo->prepare("DELETE FROM favoris WHERE user_id = ? AND lien_id = ?")->execute([$_SESSION['user_id'], $lienId]);
    } else {
        $pdo->prepare("INSERT IGNORE INTO favoris (user_id, lien_id) VALUES (?, ?)")->execute([$_SESSION['user_id'], $lienId]);
    }
    header('Location: social-sante.php');
    exit;
}

$page_title = 'Social & Santé – Liens Démarches';
$extra_css  = '<style>
.page-header {
  text-align: center;
  padding: 50px 40px 60px;
  background: linear-gradient(135deg, #3b006e 0%, #6a0dad 60%, #9b30ff 100%);
  color: #ffffff;
}
.page-header__title {
  font-size: 32px;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: .08em;
  text-transform: uppercase;
}
.page-header__sub {
  font-size: 14px;
  color: rgba(255,255,255,.8);
  margin-top: 6px;
}

.cat-content {
  max-width: 780px;
  margin: 0 auto;
  padding: 36px 24px 80px;
}

.cat-count {
  font-size: 13px;
  font-weight: 700;
  color: #1a1a2e;
  text-transform: uppercase;
  letter-spacing: .06em;
  margin-bottom: 24px;
}

.lien-card {
  background: #ffffff;
  border: 1px solid #e0e0e8;
  border-radius: 12px;
  padding: 20px 24px;
  margin-bottom: 20px;
  box-shadow: 0 2px 10px rgba(0,0,0,.05);
  transition: box-shadow .22s ease;
}
.lien-card:hover { box-shadow: 0 6px 24px rgba(106,13,173,.12); }

.lien-card__title {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a2e;
  text-align: center;
  margin-bottom: 14px;
  padding-bottom: 10px;
  border-bottom: 1px solid #f0e6fa;
}

.lien-card__body {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
}

.lien-card__left {
  display: flex;
  align-items: center;
  gap: 16px;
  flex: 1;
  min-width: 0;
}

.lien-card__logo {
  width: 60px;
  height: 42px;
  object-fit: contain;
  flex-shrink: 0;
}

.lien-card__logo-placeholder {
  width: 60px;
  height: 42px;
  background: #f0e6fa;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  color: #6a0dad;
  font-weight: 700;
  text-align: center;
  flex-shrink: 0;
}

.lien-card__url {
  font-size: 13px;
  color: #6a0dad;
  text-decoration: underline;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 340px;
}
.lien-card__url:hover { color: #5a0b99; }

.lien-card__fav {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  white-space: nowrap;
  flex-shrink: 0;
  background: none;
  border: none;
  padding: 0;
  color: #888;
  transition: color .2s;
}
.lien-card__fav:hover { color: #6a0dad; }
.lien-card__fav--active { color: #f59e0b; }
.lien-card__fav-icon { font-size: 18px; }

.empty-state {
  text-align: center;
  color: #666677;
  padding: 60px 20px;
  font-size: 15px;
}
</style>';

$extra_js = '<script src="index.js"></script>';

include 'header.php';
?>

<div class="page-header">
  <h1 class="page-header__title">Social &amp; Santé</h1>
  <p class="page-header__sub">Toutes vos démarches liées au social et à la santé en un seul endroit</p>
</div>

<div class="cat-content">

  <?php if (!empty($liens)): ?>
    <p class="cat-count"><?= count($liens) ?> LIEN<?= count($liens) > 1 ? 'S' : '' ?> TROUVÉ<?= count($liens) > 1 ? 'S' : '' ?> :</p>
  <?php endif; ?>

  <?php if (empty($liens)): ?>
    <div class="empty-state">Aucun lien disponible pour le moment.</div>
  <?php else: ?>
    <?php foreach ($liens as $lien): ?>
      <?php $estFavori = in_array($lien['id'], $favoris); ?>
      <div class="lien-card">
        <div class="lien-card__title"><?= htmlspecialchars($lien['titre']) ?></div>
        <div class="lien-card__body">

          <div class="lien-card__left">
            <?php if ($lien['logo']): ?>
              <img src="<?= htmlspecialchars($lien['logo']) ?>"
                   alt="<?= htmlspecialchars($lien['titre']) ?>"
                   class="lien-card__logo">
            <?php else: ?>
              <div class="lien-card__logo-placeholder">LOGO</div>
            <?php endif; ?>

            <a href="<?= htmlspecialchars($lien['url']) ?>"
               target="_blank" rel="noopener noreferrer"
               class="lien-card__url">
              <?= htmlspecialchars($lien['description'] ?: $lien['url']) ?>
            </a>
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
  <?php endif; ?>

</div>

<?php include 'footer.php'; ?>