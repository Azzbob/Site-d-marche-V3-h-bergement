<?php
// ============================================================
//  cgv.php  —  Conditions générales de vente / d'utilisation
// ============================================================
session_start();
require_once 'db.php';
require_once 'auto-login.php';

$page_title = 'Conditions générales de vente – Liens Démarches';
$extra_css  = '<style>' . file_get_contents(__DIR__ . '/legal-pages.css') . '</style>';

include 'header.php';
?>

<div class="legal-hero">
  <p class="legal-hero__eyebrow">CONDITIONS D'UTILISATION</p>
  <h1 class="legal-hero__title">Conditions générales de vente</h1>
</div>

<div class="legal-content">

  <p class="legal-intro">
    Les présentes Conditions Générales d'Utilisation (CGU) définissent les modalités d'accès et d'utilisation du
    site Liens Démarches.
  </p>

  <section class="legal-section">
    <h2>01. Objet</h2>
    <p>
      Liens Démarches a pour objet de faciliter l'accès aux démarches administratives en regroupant des liens vers
      différents organismes et services publics ou privés.
    </p>
    <p>Le site agit uniquement comme un annuaire de liens et n'est affilié à aucun organisme public, sauf mention contraire.</p>
  </section>

  <section class="legal-section">
    <h2>02. Accès au site</h2>
    <p>Le site est accessible gratuitement à tout utilisateur disposant d'un accès à Internet.</p>
    <p>Liens Démarches s'efforce d'assurer la disponibilité du service, sans garantir une accessibilité permanente.</p>
  </section>

  <section class="legal-section">
    <h2>03. Contenus et liens externes</h2>
    <p>
      Le site contient des liens redirigeant vers des plateformes tierces telles que l'Urssaf, l'Assurance Maladie,
      la CAF, France Travail ou les services fiscaux.
    </p>
    <p>
      Liens Démarches n'exerce aucun contrôle sur ces sites et ne saurait être tenu responsable de leur contenu, de
      leur disponibilité ou des éventuelles modifications apportées par leurs éditeurs.
    </p>
  </section>

  <section class="legal-section">
    <h2>04. Responsabilité</h2>
    <p>Les informations présentées sur Liens Démarches sont fournies à titre informatif.</p>
    <p>
      L'utilisateur demeure responsable des démarches qu'il entreprend auprès des organismes concernés. Liens
      Démarches ne garantit ni l'exhaustivité ni l'actualité permanente des informations et décline toute
      responsabilité en cas d'erreur, d'omission ou d'indisponibilité d'un service tiers.
    </p>
  </section>

  <section class="legal-section">
    <h2>05. Propriété intellectuelle</h2>
    <p>
      Les contenus originaux du site (textes, logo, structure et présentation) sont protégés par les dispositions
      relatives à la propriété intellectuelle. Toute reproduction ou utilisation sans autorisation préalable est
      interdite.
    </p>
  </section>

  <section class="legal-section">
    <h2>06. Droit applicable</h2>
    <p>Les présentes CGU sont soumises au droit français.</p>
    <p>
      En cas de litige, une solution amiable sera recherchée avant toute procédure judiciaire. À défaut, les
      tribunaux français seront compétents.
    </p>
  </section>

  <p class="legal-update">Dernière mise à jour : <?= date('d/m/Y') ?></p>

</div>

<?php include 'footer.php'; ?>
