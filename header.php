<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars($page_desc ?? 'Liens Démarches – Accédez facilement à toutes vos démarches administratives : CAF, Urssaf, Assurance Maladie, France Travail, impôts et bien plus.') ?>">
  <title><?= $page_title ?? 'Liens Démarches' ?></title>
  <style>
/* ============================================================
   reset.css + header.css + footer.css
   ============================================================ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --violet:      #6a0dad;
  --violet-dark: #5a0b99;
  --violet-soft: #f0e6fa;
  --white:       #ffffff;
  --bg:          #f4f4f8;
  --text:        #1a1a2e;
  --text-muted:  #666677;
  --border:      #e0e0e8;
  --radius:      10px;
  --shadow:      0 4px 20px rgba(106, 13, 173, .10);
  --transition:  .22s ease;
  --font-main:   'Segoe UI', system-ui, sans-serif;
}
html {
  scroll-behavior: smooth;
  background: var(--bg); /* couleur de fond sur html pour couvrir le bas de page */
}
body {
  font-family: var(--font-main);
  background: var(--bg);
  color: var(--text);
  font-size: 15px;
  line-height: 1.6;
  overflow-x: hidden;
}
a { color: inherit; text-decoration: none; }
img { display: block; max-width: 100%; }


/* ── NAVBAR ── */
.navbar {
  position: sticky;
  top: 0;
  z-index: 200;
  background: #6a0dad;
  border-bottom: 1px solid rgba(255,255,255,.15);
  box-shadow: 0 2px 12px rgba(0,0,0,.15);
  height: 56px;
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  width: 100%;
}
.navbar__inner {
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 24px;
  height: 56px;
  display: flex;
  align-items: center;
  gap: 24px;
}
.navbar__logo img { height: 36px; width: auto; display: block; }
.navbar__nav { display: flex; gap: 2px; flex: 1; }
.navbar__link {
  font-size: 13px;
  font-weight: 500;
  color: rgba(255,255,255,.85);
  padding: 6px 12px;
  border-radius: 6px;
  transition: background .22s ease, color .22s ease;
  white-space: nowrap;
}
.navbar__link:hover, .navbar__link--active {
  background: rgba(255,255,255,.18);
  color: #ffffff;
}
.navbar__burger {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 5px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
  border-radius: 6px;
  flex-shrink: 0;
  margin-left: auto;
  transition: background .22s ease;
}
.navbar__burger:hover { background: rgba(255,255,255,.15); }
.navbar__burger span {
  display: block;
  width: 24px;
  height: 2px;
  background: #ffffff;
  border-radius: 2px;
  transition: transform .25s ease, opacity .25s ease;
}
.navbar__burger.active span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.navbar__burger.active span:nth-child(2) { opacity: 0; transform: scaleX(0); }
.navbar__burger.active span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

.burger-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.45);
  z-index: 299;
  opacity: 0;
  pointer-events: none;
  transition: opacity .3s ease;
}
.burger-overlay.open { opacity: 1; pointer-events: auto; }

.burger-panel {
  position: fixed;
  top: 0; right: 0;
  width: 320px;
  max-width: 90vw;
  height: 100%;
  z-index: 300;
  background: #6a0dad;
  color: #ffffff;
  display: flex;
  flex-direction: column;
  padding: 20px 28px 36px;
  transform: translateX(100%);
  transition: transform .32s cubic-bezier(.4,0,.2,1);
  overflow-y: auto;
  align-items: flex-start;
}
.burger-panel.open { transform: translateX(0); }
.burger-panel__close {
  align-self: flex-end;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 20px;
  color: #ffffff;
  padding: 4px;
  margin-bottom: 24px;
  font-weight: 400;
}
.burger-panel__logo { margin-bottom: 24px; }
.burger-panel__logo img { height: 40px; }
.burger-panel__nav {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 4px;
  margin-bottom: 20px;
  width: 100%;
  padding: 0;
}
.burger-panel__nav a {
  display: block;
  font-size: 15px;
  font-weight: 500;
  color: rgba(255,255,255,.8);
  padding: 10px 14px;
  border-radius: 8px;
  text-decoration: none;
}
.burger-panel__nav a:hover { background: rgba(255,255,255,.12); color: #ffffff; }
.burger-panel__divider { border: none; border-top: 1px solid rgba(255,255,255,.2); margin: 8px 0 16px; width: 100%; }
.burger-panel__actions { list-style: none; display: flex; flex-direction: column; gap: 4px; width: 100%; padding: 0; flex: unset; }
.burger-panel__action-link { display: flex; align-items: center; padding: 10px 14px; border-radius: 8px; text-decoration: none; background: none; transition: background .22s; }
.burger-panel__action-link:hover { background: rgba(255,255,255,.12); }
.burger-panel__action-text { font-size: 15px; font-weight: 500; color: rgba(255,255,255,.8); }
.burger-panel__action-icon { display: none; }
.burger-panel__auth { margin-top: 16px; display: flex; flex-direction: column; gap: 10px; width: 100%; }
.burger-panel__auth-name { font-size: 14px; color: rgba(255,255,255,.7); margin-bottom: 4px; }

/* Boutons globaux */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
  font-weight: 600;
  border-radius: 8px;
  padding: 11px 22px;
  border: 2px solid transparent;
  cursor: pointer;
  transition: background .22s ease, color .22s ease, border-color .22s ease;
  white-space: nowrap;
  text-decoration: none;
}
.btn--primary { background: #6a0dad; color: #ffffff; border-color: #6a0dad; }
.btn--primary:hover { background: #5a0b99; border-color: #5a0b99; }
.btn--outline { background: transparent; color: #ffffff; border-color: #ffffff; }
.btn--outline:hover { background: rgba(255,255,255,.1); }
.btn--ghost { background: transparent; color: #ffffff; border-color: rgba(255,255,255,.4); }
.btn--ghost:hover { background: rgba(255,255,255,.1); }
.btn--sm { font-size: 13px; padding: 8px 16px; border-radius: 6px; }
.btn--lg { font-size: 16px; padding: 14px 30px; border-radius: 10px; }

@media (max-width: 640px) {
  .navbar__nav, .navbar__actions { display: none; }
}

/* ── FOOTER ── */
.footer { background: #6a0dad; color: rgba(255,255,255,.75); padding: 56px 24px 0; }
.footer__inner { max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr 1.5fr 1fr; gap: 40px; padding-bottom: 48px; border-bottom: 1px solid rgba(255,255,255,.1); }
.footer__col-title { font-size: 11px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: #ffffff; margin-bottom: 14px; }
.footer__col-text { font-size: 13px; color: rgba(255,255,255,.6); margin-bottom: 6px; }
.footer__col-text a { color: rgba(255,255,255,.6); transition: color .22s ease; }
.footer__col-text a:hover { color: #ffffff; }
.footer__list { list-style: none; display: flex; flex-direction: column; gap: 8px; }
.footer__list a { font-size: 13px; color: rgba(255,255,255,.6); transition: color .22s ease; }
.footer__list a:hover { color: #ffffff; }
.newsletter { display: flex; gap: 8px; margin-top: 12px; }
.newsletter__input { flex: 1; padding: 9px 12px; border: 1px solid rgba(255,255,255,.2); border-radius: 6px; background: rgba(255,255,255,.08); color: #ffffff; font-size: 13px; outline: none; transition: border-color .22s ease; }
.newsletter__input::placeholder { color: rgba(255,255,255,.4); }
.newsletter__input:focus { border-color: rgba(255,255,255,.5); }
.newsletter .btn--primary { background: #ffffff; color: #6a0dad; border-color: #ffffff; }
.newsletter .btn--primary:hover { background: #f0e6fa; border-color: #f0e6fa; }
.footer__col--logo { display: flex; align-items: flex-start; justify-content: center; }
.footer__logo-box { display: flex; flex-direction: column; align-items: center; gap: 8px; background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.14); border-radius: 12px; padding: 18px 22px; }
.footer__logo { width: 50px; height: auto; }
.footer__logo-name { font-size: 14px; font-weight: 700; color: #ffffff; text-align: center; line-height: 1.3; letter-spacing: .04em; }
.footer__logo-name small { font-size: 10px; font-weight: 500; letter-spacing: .1em; color: rgba(255,255,255,.6); }
.footer__bottom { max-width: 1100px; margin: 0 auto; padding: 20px 0; font-size: 12px; color: rgba(255,255,255,.35); text-align: center; }
.back-to-top { position: fixed; bottom: 28px; right: 28px; z-index: 200; width: 44px; height: 44px; border-radius: 50%; background: #ffffff; color: #6a0dad; border: none; font-size: 20px; cursor: pointer; box-shadow: 0 4px 16px rgba(106,13,173,.35); opacity: 0; transform: translateY(16px); transition: opacity .3s, transform .3s; pointer-events: none; }
.back-to-top.visible { opacity: 1; transform: translateY(0); pointer-events: auto; }
.back-to-top:hover { background: #f0e6fa; }
/* Footer responsive */
@media (max-width: 900px) {
  .footer__inner { grid-template-columns: 1fr 1fr; gap: 32px; }
}
@media (max-width: 640px) {
  .footer__inner { grid-template-columns: 1fr; gap: 24px; }
  .footer { padding: 40px 16px 0; }
  .newsletter { flex-direction: column; }
  .newsletter__input { width: 100%; }
  .newsletter .btn { width: 100%; justify-content: center; }
}
@media (max-width: 480px) {
  .footer { padding: 32px 12px 0; }
  .footer__col-title { font-size: 10px; }
  .footer__col-text, .footer__list a { font-size: 12px; }
}

}

/* ═══════════════════════════════════════════════════════════
   FOND GLOBAL — Orbes animées (CSS pur, position:fixed,
   zéro impact layout, exclues des pages légales/auth)
   ═══════════════════════════════════════════════════════════ */

/* ── Fond dégradé de base sur html ── */
html.with-bg {
  background: linear-gradient(160deg, #0d001f 0%, #1a0035 40%, #0a0018 100%);
}
html.with-bg body {
  background: transparent;
}

/* ── Conteneur fixed ── */
.site-bg {
  position: fixed;
  inset: 0;
  z-index: -1;
  pointer-events: none;
  overflow: hidden;
}

/* ── Les orbes ── */
.site-bg__orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  animation: orbFloat ease-in-out infinite alternate;
  will-change: transform;
}

.site-bg__orb--1 {
  width: 600px; height: 600px;
  top: -150px; left: -150px;
  background: radial-gradient(circle, rgba(138,43,226,0.55) 0%, rgba(106,13,173,0.25) 50%, transparent 70%);
  animation-duration: 11s;
  animation-delay: 0s;
}
.site-bg__orb--2 {
  width: 500px; height: 500px;
  top: 20%; right: -100px;
  background: radial-gradient(circle, rgba(180,0,255,0.40) 0%, rgba(120,0,200,0.15) 50%, transparent 70%);
  animation-duration: 14s;
  animation-delay: -4s;
}
.site-bg__orb--3 {
  width: 450px; height: 450px;
  bottom: 5%; left: 10%;
  background: radial-gradient(circle, rgba(75,0,160,0.45) 0%, rgba(50,0,120,0.20) 50%, transparent 70%);
  animation-duration: 17s;
  animation-delay: -8s;
}
.site-bg__orb--4 {
  width: 350px; height: 350px;
  bottom: 20%; right: 5%;
  background: radial-gradient(circle, rgba(200,100,255,0.30) 0%, rgba(150,50,220,0.12) 50%, transparent 70%);
  animation-duration: 13s;
  animation-delay: -2s;
}
.site-bg__orb--5 {
  width: 300px; height: 300px;
  top: 45%; left: 38%;
  background: radial-gradient(circle, rgba(255,150,255,0.18) 0%, rgba(200,80,255,0.08) 50%, transparent 70%);
  animation-duration: 19s;
  animation-delay: -10s;
}

@keyframes orbFloat {
  0%   { transform: translate(0px, 0px) scale(1); }
  33%  { transform: translate(30px, -20px) scale(1.05); }
  66%  { transform: translate(-15px, 25px) scale(0.97); }
  100% { transform: translate(20px, 10px) scale(1.03); }
}

/* ── Grille de points (noise texture) ── */
.site-bg__dots {
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(circle, rgba(255,255,255,0.07) 1px, transparent 1px);
  background-size: 40px 40px;
  animation: dotsShift 30s linear infinite;
}

@keyframes dotsShift {
  0%   { background-position: 0 0; }
  100% { background-position: 40px 40px; }
}

/* ── Lignes diagonales subtiles ── */
.site-bg__lines {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(45deg, rgba(255,255,255,0.025) 1px, transparent 1px),
    linear-gradient(-45deg, rgba(255,255,255,0.015) 1px, transparent 1px);
  background-size: 80px 80px;
}

/* ── Éclat central lumineux ── */
.site-bg__glow {
  position: absolute;
  top: 50%; left: 50%;
  width: 800px; height: 400px;
  transform: translate(-50%, -50%);
  background: radial-gradient(ellipse, rgba(120,0,200,0.12) 0%, transparent 70%);
  animation: glowPulse 8s ease-in-out infinite alternate;
}

@keyframes glowPulse {
  0%   { opacity: 0.6; transform: translate(-50%,-50%) scale(1); }
  100% { opacity: 1;   transform: translate(-50%,-50%) scale(1.15); }
}

/* ── Vignette sur les bords ── */
.site-bg__vignette {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse at center, transparent 50%, rgba(0,0,0,0.45) 100%);
}

/* ── Étoiles statiques (CSS box-shadow trick) ── */
.site-bg__stars {
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(circle, rgba(255,255,255,0.6) 1px, transparent 1px),
    radial-gradient(circle, rgba(255,255,255,0.4) 1px, transparent 1px),
    radial-gradient(circle, rgba(255,255,255,0.3) 1px, transparent 1px);
  background-size: 300px 300px, 500px 500px, 700px 700px;
  background-position: 0 0, 150px 80px, 300px 200px;
  animation: starsScroll 60s linear infinite;
}

@keyframes starsScroll {
  0%   { background-position: 0 0, 150px 80px, 300px 200px; }
  100% { background-position: 300px 300px, 450px 380px, 600px 500px; }
}

/* ── Adapt couleur body/cards pour contraster sur fond sombre ── */
html.with-bg .lien-card,
html.with-bg .cat-card {
  background: rgba(255,255,255,0.06) !important;
  border-color: rgba(255,255,255,0.12) !important;
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
}
html.with-bg .lien-card:hover {
  background: rgba(255,255,255,0.10) !important;
  border-color: rgba(180,80,255,0.35) !important;
}
html.with-bg .lien-card__title,
html.with-bg .cat-count,
html.with-bg .lien-card__cat {
  color: rgba(255,255,255,0.9) !important;
}
html.with-bg .lien-card__url { color: #c084fc !important; }
html.with-bg .lien-card__url:hover { color: #e9d5ff !important; }
html.with-bg .lien-card__fav { color: rgba(255,255,255,0.45) !important; }
html.with-bg .lien-card__fav:hover { color: #f59e0b !important; }

/* sections avec fond blanc/clair deviennent glassmorphism */
html.with-bg .section--why,
html.with-bg .section--how,
html.with-bg .section--links,
html.with-bg .section--cta,
html.with-bg .cat-content,
html.with-bg .page-header {
  background: transparent !important;
}

html.with-bg .why__content p,
html.with-bg .how__content p,
html.with-bg .section__label,
html.with-bg .cat-count {
  color: rgba(255,255,255,0.75) !important;
}
html.with-bg h1, html.with-bg h2, html.with-bg h3 {
  color: #ffffff !important;
}

/* Barre de recherche glassmorphism */
html.with-bg .search-bar {
  background: rgba(255,255,255,0.08) !important;
  border-color: rgba(255,255,255,0.18) !important;
  backdrop-filter: blur(16px) !important;
}
html.with-bg .search-bar input {
  background: transparent !important;
  color: #ffffff !important;
}
html.with-bg .search-bar input::placeholder { color: rgba(255,255,255,0.45) !important; }

/* Favoris page */
html.with-bg .favoris-card,
html.with-bg .mon-compte-card {
  background: rgba(255,255,255,0.06) !important;
  border-color: rgba(255,255,255,0.12) !important;
  backdrop-filter: blur(12px);
}

@media (prefers-reduced-motion: reduce) {
  .site-bg__orb { animation: none !important; }
  .site-bg__dots { animation: none !important; }
  .site-bg__glow { animation: none !important; }
  .site-bg__stars { animation: none !important; }
}

  </style>
  <?= $extra_css ?? '' ?>
</head>
<body>


<?php
/* ── Fond activé sur toutes les pages sauf auth + légales ── */
$_bg_exclus = [
  'connexion.php','inscription.php',
  'conditions.php','confidentialite.php','mentions-legales.php',
  'cgv.php','cookies.php','configuration-cookies.php','faq.php','contact.php',
];
$_page_courante = basename($_SERVER['PHP_SELF'] ?? '');
$_avec_bg = !in_array($_page_courante, $_bg_exclus);
if ($_avec_bg): ?>
<div class="site-bg" aria-hidden="true">
  <div class="site-bg__stars"></div>
  <div class="site-bg__dots"></div>
  <div class="site-bg__lines"></div>
  <div class="site-bg__orb site-bg__orb--1"></div>
  <div class="site-bg__orb site-bg__orb--2"></div>
  <div class="site-bg__orb site-bg__orb--3"></div>
  <div class="site-bg__orb site-bg__orb--4"></div>
  <div class="site-bg__orb site-bg__orb--5"></div>
  <div class="site-bg__glow"></div>
  <div class="site-bg__vignette"></div>
</div>
<script>if(document.documentElement)document.documentElement.classList.add('with-bg');</script>
<?php endif; ?>

<?php include 'navbar.php'; ?>
