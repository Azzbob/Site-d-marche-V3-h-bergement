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
$page_desc  = 'Mes favoris – Retrouvez et gérez tous vos liens de démarches administratives sauvegardés.';
$extra_css = '<style>

/* ══ HERO FAVORIS ══ */
.fav-hero {
  position: relative;
  text-align: center;
  padding: 56px 40px 70px;
  background: linear-gradient(135deg, #2a004f 0%, #6a0dad 55%, #9b30ff 100%);
  color: #fff;
  overflow: hidden;
}
.fav-hero::before {
  content: "";
  position: absolute;
  width: 500px; height: 500px;
  border-radius: 50%;
  background: rgba(255,255,255,.04);
  right: -180px; top: -200px;
  pointer-events: none;
}
.fav-hero::after {
  content: "";
  position: absolute;
  width: 300px; height: 300px;
  border-radius: 50%;
  background: rgba(255,255,255,.03);
  left: -80px; bottom: -120px;
  pointer-events: none;
}
.fav-hero__icon {
  width: 72px; height: 72px;
  margin: 0 auto 18px;
  background: rgba(255,255,255,.12);
  border: 2px solid rgba(255,255,255,.25);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
}
.fav-hero__icon svg { width: 36px; height: 36px; fill: #f5c842; }
.fav-hero__title {
  font-size: 34px;
  font-weight: 800;
  letter-spacing: .06em;
  text-transform: uppercase;
  margin-bottom: 10px;
}
.fav-hero__sub {
  font-size: 15px;
  color: rgba(255,255,255,.78);
  max-width: 440px;
  margin: 0 auto;
  line-height: 1.6;
}
.fav-hero__badge {
  display: inline-block;
  margin-top: 18px;
  background: rgba(255,255,255,.15);
  border: 1px solid rgba(255,255,255,.3);
  color: #fff;
  font-size: 13px;
  font-weight: 700;
  padding: 5px 16px;
  border-radius: 20px;
  letter-spacing: .04em;
}

/* ══ CONTENU ══ */
.fav-content {
  max-width: 820px;
  margin: 0 auto;
  padding: 44px 24px 90px;
}

/* ══ BARRE DE RECHERCHE / TRI ══ */
.fav-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 28px;
  flex-wrap: wrap;
}
.fav-search {
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
  min-width: 180px;
  background: #fff;
  border: 1.5px solid #e0e0e8;
  border-radius: 8px;
  padding: 8px 14px;
  transition: border-color .2s;
}
.fav-search:focus-within { border-color: #6a0dad; }
.fav-search svg { width: 16px; height: 16px; color: #aaa; flex-shrink: 0; }
.fav-search__input {
  border: none; outline: none;
  background: transparent;
  font-size: 14px; color: #1a1a2e;
  width: 100%;
}
.fav-search__input::placeholder { color: #bbb; }
.fav-count {
  font-size: 12px; font-weight: 700;
  color: #6a0dad;
  text-transform: uppercase;
  letter-spacing: .08em;
  white-space: nowrap;
}

/* ══ GRILLE DE CARTES ══ */
.fav-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 16px;
}

/* ══ CARTE ══ */
.fav-card {
  background: #fff;
  border: 1.5px solid #e8e8f0;
  border-radius: 14px;
  padding: 0;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(106,13,173,.05);
  transition: box-shadow .22s ease, border-color .22s ease, transform .22s ease;
  display: flex;
  flex-direction: column;
}
.fav-card:hover {
  box-shadow: 0 8px 30px rgba(106,13,173,.13);
  border-color: #c4a0e8;
  transform: translateY(-2px);
}

.fav-card__top {
  background: linear-gradient(90deg, #f5eeff 0%, #faf6ff 100%);
  border-bottom: 1px solid #ede6f8;
  padding: 14px 20px;
  display: flex;
  align-items: center;
  gap: 12px;
}
.fav-card__logo {
  width: 52px; height: 36px;
  object-fit: contain;
  flex-shrink: 0;
  border-radius: 4px;
}
.fav-card__logo-placeholder {
  width: 52px; height: 36px;
  background: #ede6f8;
  border-radius: 6px;
  display: flex; align-items: center; justify-content: center;
  font-size: 10px; color: #6a0dad; font-weight: 700;
  flex-shrink: 0;
}
.fav-card__title {
  font-size: 14px;
  font-weight: 700;
  color: #1a1a2e;
  flex: 1;
  line-height: 1.3;
}
.fav-card__cat-badge {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: .08em;
  text-transform: uppercase;
  color: #6a0dad;
  background: #f0e6fa;
  border-radius: 20px;
  padding: 3px 10px;
  white-space: nowrap;
  flex-shrink: 0;
}

.fav-card__bottom {
  padding: 14px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}
.fav-card__url {
  font-size: 13px;
  color: #6a0dad;
  text-decoration: underline;
  text-underline-offset: 2px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 400px;
  transition: color .2s;
}
.fav-card__url:hover { color: #4a0890; }

.fav-card__remove {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  color: #cc3333;
  background: #fff0f0;
  border: 1.5px solid #f5c0c0;
  border-radius: 6px;
  padding: 6px 14px;
  cursor: pointer;
  transition: background .2s, border-color .2s, color .2s;
  white-space: nowrap;
  flex-shrink: 0;
}
.fav-card__remove:hover {
  background: #ffe0e0;
  border-color: #e88888;
  color: #aa0000;
}
.fav-card__remove svg { width: 13px; height: 13px; }

/* ══ ÉTAT VIDE ══ */
.fav-empty {
  text-align: center;
  padding: 90px 24px;
  background: #fff;
  border: 1.5px dashed #d0c0e8;
  border-radius: 20px;
}
.fav-empty__star {
  width: 80px; height: 80px;
  margin: 0 auto 24px;
  background: linear-gradient(135deg, #f0e6fa, #e8d5f5);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
}
.fav-empty__star svg { width: 38px; height: 38px; fill: #c4a0e8; }
.fav-empty__title {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a2e;
  margin-bottom: 10px;
}
.fav-empty__text {
  font-size: 14px;
  color: #777;
  margin-bottom: 28px;
  line-height: 1.7;
  max-width: 380px;
  margin-left: auto;
  margin-right: auto;
}
.fav-empty__btn {
  display: inline-block;
  background: linear-gradient(135deg, #6a0dad, #9b30ff);
  color: #fff;
  padding: 13px 30px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
  transition: opacity .2s;
  letter-spacing: .02em;
}
.fav-empty__btn:hover { opacity: .88; }

/* ══ AUCUN RÉSULTAT FILTRE ══ */
.fav-no-result {
  text-align: center;
  padding: 48px 20px;
  color: #999;
  font-size: 14px;
  display: none;
}
.fav-no-result.show { display: block; }

@media (max-width: 640px) {
  .fav-card__bottom { flex-direction: column; align-items: flex-start; }
  .fav-card__url { max-width: 100%; }
}
</style>';

include 'header.php';
?>

<!-- HERO -->
<div class="fav-hero">
  <div class="fav-hero__icon">
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
    </svg>
  </div>
  <h1 class="fav-hero__title">Mes Favoris</h1>
  <p class="fav-hero__sub">Bonjour <?= htmlspecialchars($_SESSION['user_prenom'] ?? '') ?>, retrouvez ici tous vos liens sauvegardés</p>
  <?php if (!empty($favoris)): ?>
    <span class="fav-hero__badge"><?= count($favoris) ?> lien<?= count($favoris) > 1 ? 's' : '' ?> sauvegardé<?= count($favoris) > 1 ? 's' : '' ?></span>
  <?php endif; ?>
</div>

<!-- CONTENU -->
<div class="fav-content">

  <?php if (empty($favoris)): ?>
    <div class="fav-empty">
      <div class="fav-empty__star">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
      </div>
      <div class="fav-empty__title">Aucun favori pour le moment</div>
      <p class="fav-empty__text">Parcourez nos catégories et ajoutez des liens à vos favoris en cliquant sur l'étoile. Ils apparaîtront ici pour un accès rapide.</p>
      <a href="index.php" class="fav-empty__btn">Découvrir les liens &rarr;</a>
    </div>

  <?php else: ?>

    <div class="fav-toolbar">
      <div class="fav-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" class="fav-search__input" id="favSearch" placeholder="Rechercher dans mes favoris…" autocomplete="off">
      </div>
      <span class="fav-count" id="favCount"><?= count($favoris) ?> FAVORI<?= count($favoris) > 1 ? 'S' : '' ?></span>
    </div>

    <div class="fav-no-result" id="favNoResult">Aucun favori ne correspond à votre recherche.</div>

    <div class="fav-grid" id="favGrid">
      <?php foreach ($favoris as $lien): ?>
        <div class="fav-card" data-title="<?= htmlspecialchars(strtolower($lien['titre'])) ?>" data-cat="<?= htmlspecialchars(strtolower($lien['cat_nom'] ?? '')) ?>" data-desc="<?= htmlspecialchars(strtolower($lien['description'] ?? '')) ?>">

          <div class="fav-card__top">
            <?php if ($lien['logo']): ?>
              <img src="<?= htmlspecialchars($lien['logo']) ?>"
                   alt="<?= htmlspecialchars($lien['titre']) ?>"
                   class="fav-card__logo">
            <?php else: ?>
              <div class="fav-card__logo-placeholder">LOGO</div>
            <?php endif; ?>
            <span class="fav-card__title"><?= htmlspecialchars($lien['titre']) ?></span>
            <?php if ($lien['cat_nom']): ?>
              <span class="fav-card__cat-badge"><?= htmlspecialchars($lien['cat_nom']) ?></span>
            <?php endif; ?>
          </div>

          <div class="fav-card__bottom">
            <a href="<?= htmlspecialchars($lien['url']) ?>"
               target="_blank" rel="noopener noreferrer"
               class="fav-card__url">
              <?= htmlspecialchars($lien['description'] ?: $lien['url']) ?>
            </a>

            <form method="POST" style="margin:0">
              <input type="hidden" name="lien_id" value="<?= $lien['id'] ?>">
              <button type="submit" name="retirer_favori" class="fav-card__remove" title="Retirer des favoris">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                Retirer
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <script>
    (function() {
      var input = document.getElementById('favSearch');
      var cards = document.querySelectorAll('.fav-card');
      var noResult = document.getElementById('favNoResult');
      var count = document.getElementById('favCount');

      input.addEventListener('input', function() {
        var q = this.value.trim().toLowerCase();
        var visible = 0;
        cards.forEach(function(c) {
          var match = !q
            || c.dataset.title.includes(q)
            || c.dataset.cat.includes(q)
            || c.dataset.desc.includes(q);
          c.style.display = match ? '' : 'none';
          if (match) visible++;
        });
        noResult.classList.toggle('show', visible === 0);
        count.textContent = visible + ' FAVORI' + (visible > 1 ? 'S' : '');
      });
    })();
    </script>

  <?php endif; ?>

</div>

<?php include 'footer.php'; ?>