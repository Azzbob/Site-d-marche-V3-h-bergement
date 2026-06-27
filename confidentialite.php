<?php
// ============================================================
//  confidentialite.php  —  Politique de confidentialité
// ============================================================
session_start();
require_once 'db.php';
require_once 'auto-login.php';

$page_title = 'Politique de Confidentialité – Liens Démarches';
$extra_css  = '<style>' . file_get_contents(__DIR__ . '/legal-pages.css') . '</style>';

include 'header.php';
?>

<div class="legal-hero">
  <p class="legal-hero__eyebrow">VOS DONNÉES, NOS ENGAGEMENTS</p>
  <h1 class="legal-hero__title">Politique de Confidentialité</h1>
</div>

<div class="legal-content">

  <p class="legal-intro">
    Liens Démarches s'engage à assurer la protection de vos données personnelles conformément au Règlement Général
    sur la Protection des Données (RGPD) et à la législation française applicable. La présente politique a pour
    objet de vous informer sur les données susceptibles d'être collectées lors de votre utilisation du site et sur
    la manière dont elles sont traitées.
  </p>

  <section class="legal-section">
    <h2>01. Collecte des données</h2>
    <p>Lors de votre navigation sur le site, certaines données peuvent être collectées :</p>
    <ul>
      <li><strong>Données d'identification</strong> : nom, prénom et adresse électronique lorsque vous nous contactez via un formulaire ou par email.</li>
      <li><strong>Données techniques</strong> : adresse IP, type de navigateur, système d'exploitation et informations relatives à votre navigation.</li>
      <li><strong>Données de fréquentation</strong> : pages consultées, durée de visite et interactions avec le site, dans le but d'améliorer nos services.</li>
    </ul>
    <p>Aucune donnée sensible n'est collectée.</p>
  </section>

  <section class="legal-section">
    <h2>02. Finalités du traitement</h2>
    <p>Les données collectées sont utilisées exclusivement pour :</p>
    <ul>
      <li>répondre à vos demandes et messages ;</li>
      <li>assurer le bon fonctionnement et la sécurité du site ;</li>
      <li>améliorer l'expérience utilisateur et les contenus proposés ;</li>
      <li>établir des statistiques anonymes de fréquentation ;</li>
      <li>respecter nos obligations légales.</li>
    </ul>
    <p>Liens Démarches ne vend ni ne loue vos données personnelles à des tiers.</p>
  </section>

  <section class="legal-section">
    <h2>03. Liens vers des sites tiers</h2>
    <p>
      Le site Liens Démarches référence des liens vers des organismes et services externes (Urssaf, Assurance
      Maladie, CAF, impôts, France Travail, etc.). Lorsque vous quittez notre site pour accéder à l'un de ces
      services, leurs propres politiques de confidentialité et conditions d'utilisation s'appliquent. Liens
      Démarches n'est pas responsable du traitement des données effectué par ces sites tiers.
    </p>
  </section>

  <section class="legal-section">
    <h2>04. Conservation et sécurité</h2>
    <p>
      Les données personnelles sont conservées pendant une durée strictement nécessaire aux finalités pour lesquelles
      elles ont été collectées.
    </p>
    <p>
      Toutes les mesures techniques raisonnables sont mises en œuvre afin de protéger vos données contre toute perte,
      altération, divulgation ou accès non autorisé.
    </p>
  </section>

  <section class="legal-section">
    <h2>05. Vos droits</h2>
    <p>Conformément au RGPD, vous disposez des droits suivants :</p>
    <ul>
      <li>droit d'accès à vos données ;</li>
      <li>droit de rectification ;</li>
      <li>droit à l'effacement ;</li>
      <li>droit à la limitation du traitement ;</li>
      <li>droit d'opposition ;</li>
      <li>droit à la portabilité des données.</li>
    </ul>
    <p>Vous pouvez exercer ces droits à tout moment en nous contactant à :</p>
    <p>Email : <a href="mailto:azebob95@gmail.com">azebob95@gmail.com</a></p>
    <p>En cas de réclamation, vous pouvez également saisir la Commission Nationale de l'Informatique et des Libertés (CNIL).</p>
  </section>

  <section class="legal-section">
    <h2>06. Cookies</h2>
    <p>
      Le site peut utiliser des cookies nécessaires à son fonctionnement ainsi que des outils de mesure d'audience.
      Vous pouvez configurer votre navigateur afin de refuser tout ou partie des cookies, ou gérer vos préférences
      directement depuis notre page de <a href="configuration-cookies.php">configuration des cookies</a>.
    </p>
  </section>

  <p class="legal-update">Dernière mise à jour : <?= date('d/m/Y') ?></p>

</div>

<?php include 'footer.php'; ?>
