<?php
// ============================================================
//  favoris.php  —  Page des favoris de l'utilisateur
// ============================================================
session_start();
require_once 'db.php';
require_once 'auto-login.php';

// Redirige si non connecté
if (empty($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$user_connecte = true;

// Gestion retrait favori
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['retirer_favori'])) {
    $lienId = (int) $_POST['lien_id'];
    $pdo->prepare("DELETE FROM favoris WHERE user_id = ? AND lien_id = ?")->execute([$_SESSION['user_id'], $lienId]);
    header('Location: favoris.php');
    exit;
}

// Récupère les favoris de l'utilisateur avec les infos des liens
$stmt = $pdo->prepare(
    "SELECT l.*, c.nom AS cat_nom
     FROM favoris f
     JOIN liens l ON l.id = f.lien_id
     LEFT JOIN categories c ON c.id = l.categorie_id
     WHERE f.user_id = ?
     ORDER BY f.created_at DESC"
);
$stmt->execute([$_SESSION['user_id']]);
$favoris = $stmt->fetchAll();

$page_title = 'Mes Favoris – Liens Démarches';
$extra_css = '<style>
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
  margin-top: 8px;
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
  flex-shrink: 0;
}

.lien-card__info { display: flex; flex-direction: column; gap: 4px; min-width: 0; }

.lien-card__cat {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .08em;
  text-transform: uppercase;
  color: #6a0dad;
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
  color: #f59e0b;
  transition: color .2s;
}
.lien-card__fav:hover { color: #cc0000; }
.lien-card__fav-icon { font-size: 18px; }

.empty-state {
  text-align: center;
  padding: 80px 20px;
}
.empty-state__icon { font-size: 56px; margin-bottom: 16px; }
.empty-state__title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 10px; }
.empty-state__text { font-size: 14px; color: #666677; margin-bottom: 28px; }
.empty-state__btn {
  display: inline-block;
  background: #6a0dad;
  color: #ffffff;
  padding: 12px 28px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  transition: background .2s;
}
.empty-state__btn:hover { background: #5a0b99; }
</style>';

include 'header.php';
?>

<div class="page-header">
  <h1 class="page-header__title">Mes Favoris</h1>
  <p class="page-header__sub">Bonjour <?= htmlspecialchars($_SESSION['user_prenom'] ?? '') ?>, retrouvez ici tous vos liens sauvegardés</p>
</div>

<div class="cat-content">

  <?php if (empty($favoris)): ?>
    <div class="empty-state">
      <div class="empty-state__icon">☆</div>
      <div class="empty-state__title">Aucun favori pour le moment</div>
      <p class="empty-state__text">Parcourez nos catégories et ajoutez des liens à vos favoris en cliquant sur l'étoile.</p>
      <a href="index.php" class="empty-state__btn">Découvrir les liens →</a>
    </div>

  <?php else: ?>
    <p class="cat-count"><?= count($favoris) ?> FAVORI<?= count($favoris) > 1 ? 'S' : '' ?> :</p>

    <?php foreach ($favoris as $lien): ?>
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

            <div class="lien-card__info">
              <span class="lien-card__cat"><?= htmlspecialchars($lien['cat_nom'] ?? '') ?></span>
              <a href="<?= htmlspecialchars($lien['url']) ?>"
                 target="_blank" rel="noopener noreferrer"
                 class="lien-card__url">
                <?= htmlspecialchars($lien['description'] ?: $lien['url']) ?>
              </a>
            </div>
          </div>

          <form method="POST" style="margin:0">
            <input type="hidden" name="lien_id" value="<?= $lien['id'] ?>">
            <button type="submit" name="retirer_favori" class="lien-card__fav" title="Retirer des favoris">
              <span class="lien-card__fav-icon">★</span>
              retirer des favoris
            </button>
          </form>

        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

</div>

<?php include 'footer.php'; ?>