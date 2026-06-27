<?php
// ============================================================
//  mentions-legales.php  —  Mentions légales
// ============================================================
session_start();
require_once 'db.php';
require_once 'auto-login.php';

$page_title = 'Mentions légales – Liens Démarches';
$page_desc  = 'Mentions légales de Liens Démarches – Informations légales sur l éditeur et l hébergeur du site.';
$extra_css  = '<style>' . file_get_contents(__DIR__ . '/legal-pages.css') . '</style>';

include 'header.php';
?>

<div class="legal-hero">
  <p class="legal-hero__eyebrow">INFORMATIONS LÉGALES</p>
  <h1 class="legal-hero__title">Mentions légales</h1>
</div>

<div class="legal-content">

  <p class="legal-intro">
    L'accès au site <strong>Liens Démarches</strong> et l'utilisation de ses contenus sont régis par les présentes
    mentions légales. En naviguant sur ce site, vous reconnaissez en avoir pris connaissance et les accepter sans réserve.
  </p>

  <section class="legal-section">
    <h2>01. Éditeur du site</h2>
    <p>Le présent site est édité à titre non professionnel par une personne physique.</p>
    <p>Pour toute demande d'information ou de signalement, vous pouvez nous contacter à l'adresse suivante :</p>
    <ul>
      <li>Contact : <a href="mailto:azebob95@gmail.com">azebob95@gmail.com</a></li>
    </ul>
  </section>

  <section class="legal-section">
    <h2>02. Directeur de la publication</h2>
    <p>Le directeur de la publication est M. Katafi.</p>
  </section>

  <section class="legal-section">
    <h2>03. Hébergement du site</h2>
    <p>Le site est hébergé par :</p>
    <ul>
      <li>InfinityFree (iFastNet Ltd)</li>
      <li>Contact technique : <a href="mailto:azebob95@gmail.com">azebob95@gmail.com</a></li>
      <li>06 02 46 93 14</li>
    </ul>
  </section>

  <section class="legal-section">
    <h2>04. Objet du site</h2>
    <p>
      Liens Démarches a pour objet de centraliser et de faciliter l'accès à différents services administratifs,
      organismes publics et plateformes utiles, notamment l'Urssaf, l'Assurance Maladie, France Travail, les impôts,
      la CAF ou tout autre organisme similaire.
    </p>
    <p>
      Le site agit uniquement comme un annuaire de liens et n'est affilié à aucun organisme public ou administration,
      sauf mention contraire. Les marques, dénominations et logos des organismes référencés demeurent la propriété
      exclusive de leurs titulaires respectifs.
    </p>
  </section>

  <section class="legal-section">
    <h2>05. Responsabilité</h2>
    <p>
      Liens Démarches s'efforce de maintenir les informations et les liens à jour. Toutefois, aucune garantie n'est
      donnée quant à l'exactitude, l'exhaustivité ou la disponibilité permanente des contenus référencés.
    </p>
    <p>Liens Démarches ne saurait être tenu responsable :</p>
    <ul>
      <li>d'une modification ou d'une suppression des contenus par des sites tiers ;</li>
      <li>d'une indisponibilité temporaire ou permanente des liens proposés ;</li>
      <li>de tout dommage direct ou indirect résultant de l'utilisation des informations ou des services accessibles depuis les sites référencés.</li>
    </ul>
  </section>

  <section class="legal-section">
    <h2>06. Liens hypertextes</h2>
    <p>
      Le site contient des liens redirigeant vers des sites externes. Liens Démarches n'exerce aucun contrôle sur le
      contenu de ces sites et décline toute responsabilité concernant les informations, produits ou services qui y
      sont proposés.
    </p>
  </section>

  <section class="legal-section">
    <h2>07. Propriété intellectuelle</h2>
    <p>
      L'ensemble des éléments présents sur le site Liens Démarches (textes, logo, graphismes, structure, conception
      et contenus originaux) est protégé par les dispositions relatives à la propriété intellectuelle.
    </p>
    <p>Toute reproduction, représentation ou exploitation, totale ou partielle, sans autorisation préalable, est interdite.</p>
  </section>

  <section class="legal-section">
    <h2>08. Droit applicable</h2>
    <p>Les présentes mentions légales sont soumises au droit français.</p>
    <p>En cas de litige, et après tentative de résolution amiable, les tribunaux français seront seuls compétents.</p>
  </section>

  <p class="legal-update">Dernière mise à jour : <?= date('d/m/Y') ?></p>

</div>

<?php include 'footer.php'; ?>