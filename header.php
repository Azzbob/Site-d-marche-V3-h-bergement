<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $page_title ?? 'Liens Démarches' ?></title>
  <style>
/* ============================================================
   reset.css + header.css + footer.css + FOND ANIMÉ GLOBAL
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
  background: transparent; /* transparent pour laisser le fond animé visible */
  color: var(--text);
  font-size: 15px;
  line-height: 1.6;
  overflow-x: hidden;
}
a { color: inherit; text-decoration: none; }
img { display: block; max-width: 100%; }

/* ══════════════════════════════════════════
   FOND ANIMÉ GLOBAL — Icônes flottantes
   (s'affiche sur toutes les pages SAUF connexion/inscription)
══════════════════════════════════════════ */
.animated-bg {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  z-index: -1;
  pointer-events: none;
  overflow: hidden;
}

/* Chaque icône flottante */
.animated-bg__icon {
  position: absolute;
  opacity: 0;
  will-change: transform, opacity;
  animation: iconFloat linear infinite;
  filter: blur(0px);
}

@keyframes iconFloat {
  0%   {
    transform: translateY(105vh) rotate(0deg) scale(0.7);
    opacity: 0;
  }
  8%   { opacity: 1; }
  90%  { opacity: 0.45; }
  100% {
    transform: translateY(-15vh) rotate(540deg) scale(1.1);
    opacity: 0;
  }
}

/* Vague parallaxe bas de page */
.animated-bg__wave {
  position: absolute;
  bottom: -2px;
  left: -5%;
  width: 110%;
  pointer-events: none;
  will-change: transform;
  transition: transform 0.1s linear;
}

/* Halo dégradé doux qui pulse */
.animated-bg__halo {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
  filter: blur(80px);
  animation: haloPulse ease-in-out infinite alternate;
}

.animated-bg__halo--tl {
  width: 500px; height: 500px;
  top: -100px; left: -150px;
  background: radial-gradient(circle, rgba(106,13,173,0.07) 0%, transparent 70%);
  animation-duration: 9s;
}

.animated-bg__halo--br {
  width: 600px; height: 400px;
  bottom: -80px; right: -120px;
  background: radial-gradient(circle, rgba(155,48,255,0.05) 0%, transparent 70%);
  animation-duration: 13s;
  animation-delay: -4s;
}

.animated-bg__halo--mid {
  width: 350px; height: 350px;
  top: 40%; left: 35%;
  background: radial-gradient(circle, rgba(59,0,110,0.04) 0%, transparent 70%);
  animation-duration: 11s;
  animation-delay: -2s;
}

@keyframes haloPulse {
  0%   { transform: scale(1) translate(0, 0); opacity: 1; }
  100% { transform: scale(1.25) translate(20px, -15px); opacity: 0.6; }
}

/* Le fond est en dessous grâce au z-index: 0 du .animated-bg — pas besoin de z-index sur les enfants */

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
@media (max-width: 900px) { .footer__inner { grid-template-columns: 1fr 1fr; gap: 32px; } }
@media (max-width: 640px) { .footer__inner { grid-template-columns: 1fr; gap: 28px; } }

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
  .animated-bg__icon,
  .animated-bg__halo { animation: none !important; opacity: 0.06 !important; }
}
  </style>
  <?= $extra_css ?? '' ?>
</head>
<body>

<?php
// Affiche le fond animé sur toutes les pages SAUF connexion et inscription
$page_actuelle = basename($_SERVER['PHP_SELF'] ?? '');
$pages_sans_fond = ['connexion.php', 'inscription.php'];
$afficher_fond = !in_array($page_actuelle, $pages_sans_fond);

if ($afficher_fond):
?>
<!-- ══ FOND ANIMÉ GLOBAL ══ -->
<div class="animated-bg" id="animatedBg" aria-hidden="true">

  <!-- Halos de fond -->
  <div class="animated-bg__halo animated-bg__halo--tl"></div>
  <div class="animated-bg__halo animated-bg__halo--br"></div>
  <div class="animated-bg__halo animated-bg__halo--mid"></div>

  <!-- Icônes administratives flottantes (SVG inline) -->
  <!-- Document / Formulaire -->
  <div class="animated-bg__icon" style="left:8%;animation-duration:18s;animation-delay:-3s;width:38px;height:38px;">
    <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
      <rect x="6" y="3" width="22" height="28" rx="3" fill="rgba(106,13,173,0.13)" stroke="rgba(106,13,173,0.25)" stroke-width="1.5"/>
      <rect x="10" y="9" width="14" height="2" rx="1" fill="rgba(106,13,173,0.3)"/>
      <rect x="10" y="14" width="10" height="2" rx="1" fill="rgba(106,13,173,0.25)"/>
      <rect x="10" y="19" width="12" height="2" rx="1" fill="rgba(106,13,173,0.2)"/>
      <rect x="10" y="24" width="8" height="2" rx="1" fill="rgba(106,13,173,0.18)"/>
    </svg>
  </div>

  <!-- Maison / Logement -->
  <div class="animated-bg__icon" style="left:20%;animation-duration:22s;animation-delay:-8s;width:42px;height:42px;">
    <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M20 6L6 18h4v14h8V24h4v8h8V18h4L20 6z" fill="rgba(155,48,255,0.12)" stroke="rgba(155,48,255,0.28)" stroke-width="1.4" stroke-linejoin="round"/>
    </svg>
  </div>

  <!-- Cœur / Santé / CAF -->
  <div class="animated-bg__icon" style="left:35%;animation-duration:16s;animation-delay:-1s;width:34px;height:34px;">
    <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M20 33s-13-8.5-13-16a7 7 0 0 1 13-3.5A7 7 0 0 1 33 17c0 7.5-13 16-13 16z" fill="rgba(106,13,173,0.10)" stroke="rgba(106,13,173,0.22)" stroke-width="1.5"/>
    </svg>
  </div>

  <!-- Euro / Finances -->
  <div class="animated-bg__icon" style="left:55%;animation-duration:20s;animation-delay:-12s;width:36px;height:36px;">
    <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="20" cy="20" r="14" fill="rgba(59,0,110,0.08)" stroke="rgba(59,0,110,0.2)" stroke-width="1.5"/>
      <text x="20" y="26" text-anchor="middle" font-size="16" fill="rgba(106,13,173,0.35)" font-family="system-ui">€</text>
    </svg>
  </div>

  <!-- Valise / Travail -->
  <div class="animated-bg__icon" style="left:70%;animation-duration:25s;animation-delay:-5s;width:40px;height:40px;">
    <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
      <rect x="5" y="15" width="30" height="20" rx="3" fill="rgba(155,48,255,0.09)" stroke="rgba(155,48,255,0.22)" stroke-width="1.5"/>
      <path d="M15 15V11a5 5 0 0 1 10 0v4" stroke="rgba(155,48,255,0.28)" stroke-width="1.5" stroke-linecap="round"/>
      <line x1="20" y1="15" x2="20" y2="35" stroke="rgba(155,48,255,0.15)" stroke-width="1.2"/>
    </svg>
  </div>

  <!-- Roue dentée / Services -->
  <div class="animated-bg__icon" style="left:85%;animation-duration:19s;animation-delay:-9s;width:38px;height:38px;">
    <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M20 12a8 8 0 1 0 0 16 8 8 0 0 0 0-16zm0 4a4 4 0 1 1 0 8 4 4 0 0 1 0-8z" fill="rgba(106,13,173,0.12)" stroke="rgba(106,13,173,0.22)" stroke-width="0.5"/>
      <path d="M18 6h4v4h-4zM18 30h4v4h-4zM6 18v4h4v-4zM30 18v4h4v-4z" fill="rgba(106,13,173,0.15)"/>
    </svg>
  </div>

  <!-- Étoile / Favoris -->
  <div class="animated-bg__icon" style="left:48%;animation-duration:14s;animation-delay:-6s;width:32px;height:32px;">
    <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M20 5l3.9 8.6 9.1 1.3-6.6 6.5 1.6 9.1L20 26l-8 4.5 1.6-9.1L7 14.9l9.1-1.3z" fill="rgba(155,48,255,0.10)" stroke="rgba(155,48,255,0.25)" stroke-width="1.4" stroke-linejoin="round"/>
    </svg>
  </div>

  <!-- Retraite / Personne âgée -->
  <div class="animated-bg__icon" style="left:62%;animation-duration:21s;animation-delay:-15s;width:36px;height:36px;">
    <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="20" cy="10" r="5" fill="rgba(106,13,173,0.10)" stroke="rgba(106,13,173,0.22)" stroke-width="1.4"/>
      <path d="M12 30c0-6 3.5-10 8-10s8 4 8 10" stroke="rgba(106,13,173,0.22)" stroke-width="1.4" fill="rgba(106,13,173,0.06)" stroke-linecap="round"/>
      <line x1="14" y1="28" x2="11" y2="35" stroke="rgba(106,13,173,0.2)" stroke-width="1.4" stroke-linecap="round"/>
    </svg>
  </div>

  <!-- Enveloppe / Contact -->
  <div class="animated-bg__icon" style="left:28%;animation-duration:17s;animation-delay:-11s;width:40px;height:40px;">
    <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
      <rect x="5" y="11" width="30" height="20" rx="3" fill="rgba(59,0,110,0.08)" stroke="rgba(59,0,110,0.2)" stroke-width="1.5"/>
      <path d="M5 14l15 10 15-10" stroke="rgba(59,0,110,0.22)" stroke-width="1.4" stroke-linecap="round"/>
    </svg>
  </div>

  <!-- Vague de fond qui suit le scroll -->
  <svg class="animated-bg__wave" viewBox="0 0 1440 120" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
    <path d="M0,60 C240,120 480,0 720,60 C960,120 1200,0 1440,60 L1440,120 L0,120 Z" fill="rgba(106,13,173,0.04)"/>
    <path d="M0,80 C360,20 720,120 1080,40 C1260,0 1380,60 1440,80 L1440,120 L0,120 Z" fill="rgba(155,48,255,0.03)"/>
  </svg>
</div>

<script>
// Fond animé : effet parallaxe au scroll + souris
(function () {
  var bg   = document.getElementById('animatedBg');
  if (!bg) return;
  var wave = bg.querySelector('.animated-bg__wave');
  var icons = Array.from(bg.querySelectorAll('.animated-bg__icon'));

  var scrollY = 0, mouseX = 0.5, mouseY = 0.5;
  var rafPending = false;

  function render() {
    rafPending = false;

    // Vague qui monte légèrement au scroll
    if (wave) {
      wave.style.transform = 'translateY(' + (-scrollY * 0.08) + 'px)';
    }

    // Icônes : léger déport horizontal selon scroll + souris
    icons.forEach(function (icon, i) {
      var scrollFactor = (i % 3 === 0 ? 0.06 : i % 3 === 1 ? -0.04 : 0.03);
      var mouseFactor  = (i % 4 + 1) * 0.004;
      var dx = (mouseX - 0.5) * 40 * mouseFactor;
      var dy = scrollY * scrollFactor;
      icon.style.marginLeft  = dx.toFixed(1) + 'px';
      icon.style.marginBottom = dy.toFixed(1) + 'px';
    });
  }

  function tick() { if (!rafPending) { rafPending = true; requestAnimationFrame(render); } }

  window.addEventListener('scroll', function () { scrollY = window.scrollY; tick(); }, { passive: true });
  window.addEventListener('mousemove', function (e) {
    mouseX = e.clientX / window.innerWidth;
    mouseY = e.clientY / window.innerHeight;
    tick();
  }, { passive: true });

  // Gyroscope mobile
  if (window.DeviceOrientationEvent) {
    window.addEventListener('deviceorientation', function (e) {
      mouseX = 0.5 + (e.gamma || 0) / 90 * 0.5;
      mouseY = 0.5 + (e.beta  || 0) / 180 * 0.5;
      tick();
    }, { passive: true });
  }
})();
</script>
<?php endif; ?>

<?php include 'navbar.php'; ?>
