<?php
// ============================================================
//  configuration-cookies.php  —  Préférences cookies (fonctionnel)
// ============================================================
session_start();
require_once 'db.php';
require_once 'auto-login.php';

$page_title = 'Configuration des cookies – Liens Démarches';
$extra_css  = '<style>' . file_get_contents(__DIR__ . '/legal-pages.css') . '</style>';

include 'header.php';
?>

<div class="legal-hero">
  <p class="legal-hero__eyebrow">PERSONNALISEZ VOTRE EXPÉRIENCE</p>
  <h1 class="legal-hero__title">Configuration des cookies</h1>
</div>

<div class="legal-content">

  <p class="legal-intro">
    Lorsque vous visitez Liens Démarches, certaines informations peuvent être stockées dans votre navigateur sous
    forme de cookies. Vous pouvez gérer vos préférences à tout moment depuis cette page.
  </p>

  <div class="cookie-quick-actions">
    <button type="button" class="btn-cookie btn-cookie--outline" id="btnRefuserTout">Tout refuser</button>
    <button type="button" class="btn-cookie btn-cookie--filled" id="btnAccepterTout">Tout accepter</button>
  </div>

  <section class="legal-section">
    <h2>Gérer les préférences de consentement par catégorie</h2>

    <div class="cookie-pref">
      <div class="cookie-pref__header">
        <span class="cookie-pref__title">01. Essentiels</span>
        <span class="cookie-pref__always">Toujours actifs</span>
      </div>
      <p class="cookie-pref__desc">
        Ces cookies sont indispensables au fonctionnement du site et permettent notamment une navigation sécurisée
        et l'accès aux fonctionnalités de base (connexion, favoris, sécurité).
      </p>
    </div>

    <div class="cookie-pref">
      <div class="cookie-pref__header">
        <span class="cookie-pref__title">02. Personnalisation</span>
        <label class="cookie-toggle">
          <input type="checkbox" id="cookiePersonnalisation">
          <span class="cookie-toggle__slider"></span>
        </label>
      </div>
      <p class="cookie-pref__desc">
        Ces cookies permettent d'enregistrer certaines préférences afin d'améliorer votre expérience utilisateur
        (filtres de recherche, catégories favorites...).
      </p>
    </div>

    <div class="cookie-pref">
      <div class="cookie-pref__header">
        <span class="cookie-pref__title">03. Mesure d'audience</span>
        <label class="cookie-toggle">
          <input type="checkbox" id="cookieMesure">
          <span class="cookie-toggle__slider"></span>
        </label>
      </div>
      <p class="cookie-pref__desc">
        Ces cookies nous aident à comprendre comment les visiteurs utilisent le site afin d'améliorer son
        fonctionnement. Les données recueillies sont anonymes et agrégées.
      </p>
    </div>

    <div class="cookie-pref">
      <div class="cookie-pref__header">
        <span class="cookie-pref__title">04. Réseaux sociaux</span>
        <label class="cookie-toggle">
          <input type="checkbox" id="cookieSociaux">
          <span class="cookie-toggle__slider"></span>
        </label>
      </div>
      <p class="cookie-pref__desc">
        Ces cookies permettent l'intégration de contenus de réseaux sociaux et le partage de pages sur ces plateformes.
      </p>
    </div>

  </section>

  <div class="cookie-form-actions">
    <button type="button" class="btn-cookie btn-cookie--outline" id="btnAnnuler">Annuler</button>
    <button type="button" class="btn-cookie btn-cookie--filled" id="btnEnregistrer">Enregistrer les préférences</button>
  </div>

  <div class="cookie-confirm" id="cookieConfirm">✓ Vos préférences ont bien été enregistrées.</div>

  <p class="legal-update">Dernière mise à jour : <?= date('d/m/Y') ?></p>

</div>

<?php include 'footer.php'; ?>
