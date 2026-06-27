<?php
// ============================================================
//  cookies.php  —  Politique relative aux cookies
// ============================================================
session_start();
require_once 'db.php';
require_once 'auto-login.php';

$page_title = 'Politique relative aux cookies – Liens Démarches';
$extra_css  = '<style>' . file_get_contents(__DIR__ . '/legal-pages.css') . '</style>';

include 'header.php';
?>

<div class="legal-hero">
  <p class="legal-hero__eyebrow">TRANSPARENCE</p>
  <h1 class="legal-hero__title">Politique relative aux cookies</h1>
</div>

<div class="legal-content">

  <p class="legal-intro">
    Lors de votre navigation sur Liens Démarches, des informations peuvent être enregistrées sur votre terminal au
    moyen de cookies. Cette politique a pour objet de vous informer sur leur utilisation et sur les moyens dont vous
    disposez pour les gérer.
  </p>

  <section class="legal-section">
    <h2>01. Qu'est-ce qu'un cookie ?</h2>
    <p>
      Un cookie est un petit fichier texte déposé sur votre ordinateur, votre tablette ou votre smartphone lors de la
      consultation d'un site internet. Il permet notamment d'améliorer votre navigation et de mesurer l'audience du
      site.
    </p>
  </section>

  <section class="legal-section">
    <h2>02. Les cookies utilisés</h2>
    <p>Liens Démarches peut utiliser les catégories suivantes :</p>
    <ul>
      <li>
        <strong>Cookies essentiels</strong><br>
        Ces cookies sont indispensables au bon fonctionnement du site et ne peuvent pas être désactivés (ex : session de connexion, panier de favoris).
      </li>
      <li>
        <strong>Cookies de mesure d'audience</strong><br>
        Ils permettent d'établir des statistiques anonymes sur la fréquentation du site et d'améliorer son contenu.
      </li>
      <li>
        <strong>Cookies de personnalisation</strong><br>
        Ils permettent de mémoriser certaines préférences de navigation afin d'améliorer votre expérience utilisateur.
      </li>
    </ul>
  </section>

  <section class="legal-section">
    <h2>03. Durée de conservation</h2>
    <p>Les cookies sont conservés pour une durée maximale de 13 mois à compter de leur dépôt sur votre appareil.</p>
  </section>

  <section class="legal-section">
    <h2>04. Gestion des cookies</h2>
    <p>
      Vous pouvez accepter, refuser ou paramétrer les cookies via notre
      <a href="configuration-cookies.php">page de gestion des préférences</a>.
    </p>
    <p>Vous pouvez également configurer votre navigateur afin de limiter ou bloquer les cookies.</p>
  </section>

  <section class="legal-section">
    <h2>05. Plus d'informations</h2>
    <p>Pour toute question relative aux cookies ou à la protection de vos données personnelles, vous pouvez nous contacter à :</p>
    <p>Email : <a href="mailto:azebob95@gmail.com">azebob95@gmail.com</a></p>
  </section>

  <p class="legal-update">Dernière mise à jour : <?= date('d/m/Y') ?></p>

</div>

<?php include 'footer.php'; ?>
