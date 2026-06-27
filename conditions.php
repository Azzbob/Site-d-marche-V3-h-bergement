<?php
// ============================================================
//  conditions.php  —  Conditions d'utilisation du service
// ============================================================
session_start();
require_once 'db.php';
require_once 'auto-login.php';

$page_title = "Conditions d'utilisation – Liens Démarches";
$extra_css  = '<style>' . file_get_contents(__DIR__ . '/legal-pages.css') . '</style>';

include 'header.php';
?>

<div class="legal-hero">
  <p class="legal-hero__eyebrow">AVANT D'UTILISER LE SERVICE</p>
  <h1 class="legal-hero__title">Conditions d'utilisation</h1>
</div>

<div class="legal-content">

  <p class="legal-intro">
    En créant un compte sur Liens Démarches, vous acceptez les présentes conditions d'utilisation. 
    Veuillez les lire attentivement avant de vous inscrire.
  </p>

  <section class="legal-section">
    <h2>01. Objet du service</h2>
    <p>
      Liens Démarches est un service en ligne qui facilite l'accès aux démarches administratives 
      en centralisant des liens vers les organismes publics et privés (Urssaf, CAF, Assurance Maladie, 
      France Travail, services fiscaux, etc.).
    </p>
    <p>
      Le site agit comme un annuaire de liens et n'est affilié à aucun organisme public officiel, 
      sauf mention contraire explicite.
    </p>
  </section>

  <section class="legal-section">
    <h2>02. Inscription et compte utilisateur</h2>
    <p>
      Pour accéder à certaines fonctionnalités (favoris, espace personnel), vous devez créer un compte 
      en fournissant des informations exactes et à jour.
    </p>
    <p>
      Vous êtes responsable de la confidentialité de vos identifiants de connexion. Toute activité 
      effectuée depuis votre compte est réputée être de votre fait.
    </p>
    <p>
      Il est interdit de créer un compte pour le compte d'un tiers sans son autorisation, ou à des fins 
      frauduleuses ou malveillantes.
    </p>
  </section>

  <section class="legal-section">
    <h2>03. Utilisation du service</h2>
    <p>
      Vous vous engagez à utiliser le service de manière loyale, conformément à la loi et aux présentes 
      conditions. Sont notamment interdits :
    </p>
    <p>— Toute tentative d'accès non autorisé aux systèmes informatiques du site ;</p>
    <p>— La diffusion de contenus illicites, diffamatoires ou portant atteinte aux droits de tiers ;</p>
    <p>— L'utilisation du service à des fins commerciales sans autorisation préalable.</p>
  </section>

  <section class="legal-section">
    <h2>04. Liens externes et responsabilité</h2>
    <p>
      Les liens présents sur Liens Démarches redirigent vers des plateformes tierces. Nous n'exerçons 
      aucun contrôle sur ces sites et déclinons toute responsabilité quant à leur contenu, disponibilité 
      ou modifications éventuelles.
    </p>
    <p>
      Les informations fournies sont à titre indicatif. L'utilisateur reste seul responsable des démarches 
      qu'il entreprend auprès des organismes concernés.
    </p>
  </section>

  <section class="legal-section">
    <h2>05. Données personnelles</h2>
    <p>
      Les données que vous nous confiez lors de l'inscription (nom, prénom, adresse email) sont utilisées 
      uniquement pour le fonctionnement du service. Elles ne sont pas cédées à des tiers.
    </p>
    <p>
      Conformément au Règlement Général sur la Protection des Données (RGPD), vous disposez d'un droit 
      d'accès, de rectification et de suppression de vos données. Pour exercer ces droits, contactez-nous 
      via la page <a href="contact.php">Contact</a>.
    </p>
    <p>
      Pour plus de détails, consultez notre <a href="confidentialite.php">Politique de confidentialité</a>.
    </p>
  </section>

  <section class="legal-section">
    <h2>06. Modification des conditions</h2>
    <p>
      Liens Démarches se réserve le droit de modifier les présentes conditions à tout moment. 
      Les utilisateurs seront informés des changements significatifs. La poursuite de l'utilisation 
      du service après modification vaut acceptation des nouvelles conditions.
    </p>
  </section>

  <section class="legal-section">
    <h2>07. Résiliation</h2>
    <p>
      Vous pouvez supprimer votre compte à tout moment depuis votre espace personnel. 
      Liens Démarches se réserve le droit de suspendre ou supprimer tout compte en cas de violation 
      des présentes conditions.
    </p>
  </section>

  <section class="legal-section">
    <h2>08. Droit applicable</h2>
    <p>
      Les présentes conditions d'utilisation sont soumises au droit français. En cas de litige, 
      une solution amiable sera recherchée avant toute procédure judiciaire. À défaut, les tribunaux 
      français seront compétents.
    </p>
  </section>

  <p class="legal-update">Dernière mise à jour : <?= date('d/m/Y') ?></p>

</div>

<?php include 'footer.php'; ?>
