<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $page_title ?? 'Liens Démarches' ?></title>
  <style>
/* ============================================================
   reset.css — Reset global appliqué à toutes les pages
   (évite la marge par défaut du navigateur sur <body>, etc.)
   ============================================================ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ============================================================
   header.css — Navbar Liens Démarches
   ============================================================ */
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
html { scroll-behavior: smooth; }
body {
  font-family: var(--font-main);
  background: var(--bg);
  color: var(--text);
  font-size: 15px;
  line-height: 1.6;
}
a { color: inherit; text-decoration: none; }
img { display: block; max-width: 100%; }

.navbar {
  position: sticky;
  top: 0;
  z-index: 200;
  background: #6a0dad;
  border-bottom: 1px solid rgba(255,255,255,.15);
  box-shadow: 0 2px 12px rgba(0,0,0,.15);
  /* Hauteur toujours identique sur toutes les pages */
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
.burger-panel__nav a:hover {
  background: rgba(255,255,255,.12);
  color: #ffffff;
}
.burger-panel__divider {
  border: none;
  border-top: 1px solid rgba(255,255,255,.2);
  margin: 8px 0 16px;
  width: 100%;
}
.burger-panel__actions {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 4px;
  width: 100%;
  padding: 0;
  flex: unset;
}
.burger-panel__action-link {
  display: flex;
  align-items: center;
  padding: 10px 14px;
  border-radius: 8px;
  text-decoration: none;
  background: none;
  transition: background .22s;
}
.burger-panel__action-link:hover { background: rgba(255,255,255,.12); }
.burger-panel__action-text {
  font-size: 15px;
  font-weight: 500;
  color: rgba(255,255,255,.8);
}
.burger-panel__action-icon { display: none; }
.burger-panel__auth {
  margin-top: 16px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  width: 100%;
}
.burger-panel__auth-name {
  font-size: 14px;
  color: rgba(255,255,255,.7);
  margin-bottom: 4px;
}

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

/* ============================================================
   footer.css — Footer Liens Démarches
   ============================================================ */
.footer {
  background: #6a0dad;
  color: rgba(255,255,255,.75);
  padding: 56px 24px 0;
}
.footer__inner {
  max-width: 1100px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 2fr 1fr 1.5fr 1fr;
  gap: 40px;
  padding-bottom: 48px;
  border-bottom: 1px solid rgba(255,255,255,.1);
}
.footer__col-title {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: #ffffff;
  margin-bottom: 14px;
}
.footer__col-text { font-size: 13px; color: rgba(255,255,255,.6); margin-bottom: 6px; }
.footer__col-text a { color: rgba(255,255,255,.6); transition: color .22s ease; }
.footer__col-text a:hover { color: #ffffff; }
.footer__list { list-style: none; display: flex; flex-direction: column; gap: 8px; }
.footer__list a { font-size: 13px; color: rgba(255,255,255,.6); transition: color .22s ease; }
.footer__list a:hover { color: #ffffff; }

.newsletter { display: flex; gap: 8px; margin-top: 12px; }
.newsletter__input {
  flex: 1;
  padding: 9px 12px;
  border: 1px solid rgba(255,255,255,.2);
  border-radius: 6px;
  background: rgba(255,255,255,.08);
  color: #ffffff;
  font-size: 13px;
  outline: none;
  transition: border-color .22s ease;
}
.newsletter__input::placeholder { color: rgba(255,255,255,.4); }
.newsletter__input:focus { border-color: rgba(255,255,255,.5); }
.newsletter .btn--primary {
  background: #ffffff;
  color: #6a0dad;
  border-color: #ffffff;
}
.newsletter .btn--primary:hover {
  background: #f0e6fa;
  border-color: #f0e6fa;
}

.footer__col--logo { display: flex; align-items: flex-start; justify-content: center; }
.footer__logo-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  background: rgba(255,255,255,.07);
  border: 1px solid rgba(255,255,255,.14);
  border-radius: 12px;
  padding: 18px 22px;
}
.footer__logo { width: 50px; height: auto; }
.footer__logo-name {
  font-size: 14px;
  font-weight: 700;
  color: #ffffff;
  text-align: center;
  line-height: 1.3;
  letter-spacing: .04em;
}
.footer__logo-name small {
  font-size: 10px;
  font-weight: 500;
  letter-spacing: .1em;
  color: rgba(255,255,255,.6);
}
.footer__bottom {
  max-width: 1100px;
  margin: 0 auto;
  padding: 20px 0;
  font-size: 12px;
  color: rgba(255,255,255,.35);
  text-align: center;
}
.back-to-top {
  position: fixed;
  bottom: 28px;
  right: 28px;
  z-index: 200;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: #ffffff;
  color: #6a0dad;
  border: none;
  font-size: 20px;
  cursor: pointer;
  box-shadow: 0 4px 16px rgba(106,13,173,.35);
  opacity: 0;
  transform: translateY(16px);
  transition: opacity .3s, transform .3s;
  pointer-events: none;
}
.back-to-top.visible { opacity: 1; transform: translateY(0); pointer-events: auto; }
.back-to-top:hover { background: #f0e6fa; }

@media (max-width: 900px) {
  .footer__inner { grid-template-columns: 1fr 1fr; gap: 32px; }
}
@media (max-width: 640px) {
  .footer__inner { grid-template-columns: 1fr; gap: 28px; }
}
  </style>
  <?= $extra_css ?? '' ?>
</head>
<body>

<?php include 'navbar.php'; ?>