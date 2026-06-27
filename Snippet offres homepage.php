<?php
// ============================================================
//  snippet-offres-homepage.php
//  À inclure dans index.php pour afficher les 2 liens mis en avant
//  Pré-requis : require_once 'db.php'; avant l'include
// ============================================================

$stmt = $pdo->prepare(
    "SELECT l.*, c.nom AS cat_nom
     FROM liens l
     LEFT JOIN categories c ON c.id = l.categorie_id
     WHERE l.mis_en_avant = 1
       AND l.actif = 1
     ORDER BY l.ordre ASC
     LIMIT 2"
);
$stmt->execute();
$offres = $stmt->fetchAll();
?>

<div class="offres" id="offresContainer">
  <?php foreach ($offres as $offre): ?>
  <div class="offre-card" data-titre="<?= strtolower(htmlspecialchars($offre['titre'])) ?>">

    <div class="offre-card__left">
      <?php if ($offre['logo']): ?>
        <img src="<?= htmlspecialchars($offre['logo']) ?>"
             alt="<?= htmlspecialchars($offre['titre']) ?>"
             class="offre-card__logo">
      <?php endif; ?>
      <div class="offre-card__info">
        <span class="offre-card__cat"><?= htmlspecialchars($offre['cat_nom'] ?? '') ?></span>
        <a href="<?= htmlspecialchars($offre['url']) ?>"
           target="_blank" rel="noopener noreferrer"
           class="offre-card__url">
          <?= htmlspecialchars($offre['url']) ?>
        </a>
      </div>
    </div>

    <a href="<?= htmlspecialchars($offre['url']) ?>"
       target="_blank" rel="noopener noreferrer"
       class="btn btn--primary btn--sm">
      Accéder au service →
    </a>

  </div>
  <?php endforeach; ?>

  <?php if (empty($offres)): ?>
    <p class="offres__empty">Aucun lien mis en avant pour le moment.</p>
  <?php endif; ?>
</div>
