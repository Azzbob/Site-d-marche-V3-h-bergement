// ============================================================
//  cookie-consent.js — Gestion du consentement cookies
//  Stocke les préférences dans un vrai cookie navigateur
//  "ld_cookie_prefs" (durée 13 mois, conformément au RGPD)
// ============================================================

(function () {
  const COOKIE_NAME = 'ld_cookie_prefs';
  const COOKIE_DAYS  = 396; // 13 mois

  function getCookie(name) {
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? decodeURIComponent(match[2]) : null;
  }

  function setCookie(name, value, days) {
    const date = new Date();
    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
    document.cookie = name + '=' + encodeURIComponent(value) +
      ';expires=' + date.toUTCString() + ';path=/;SameSite=Lax';
  }

  function getPrefs() {
    const raw = getCookie(COOKIE_NAME);
    if (!raw) return null;
    try { return JSON.parse(raw); } catch (e) { return null; }
  }

  function savePrefs(prefs) {
    setCookie(COOKIE_NAME, JSON.stringify(prefs), COOKIE_DAYS);
  }

  // ── Injection du CSS de la bannière (auto-injecté pour garantir
  //    l'affichage correct même si legal-pages.css n'est pas chargé) ──
  function injectBannerCSS() {
    if (document.getElementById('cookie-banner-style')) return;
    const style = document.createElement('style');
    style.id = 'cookie-banner-style';
    style.textContent = `
      .cookie-banner {
        position: fixed !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        top: auto !important;
        z-index: 9999 !important;
        background: #1a1a2e !important;
        color: #ffffff !important;
        padding: 20px 24px !important;
        box-shadow: 0 -6px 30px rgba(0,0,0,.30) !important;
        transform: translateY(110%);
        transition: transform .4s cubic-bezier(.4,0,.2,1);
        font-family: 'Segoe UI', system-ui, sans-serif;
        margin: 0 !important;
      }
      .cookie-banner.visible {
        transform: translateY(0) !important;
      }
      .cookie-banner__inner {
        max-width: 1100px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        gap: 24px;
        flex-wrap: wrap;
      }
      .cookie-banner__text {
        flex: 1;
        min-width: 240px;
        font-size: 13px;
        line-height: 1.6;
        color: rgba(255,255,255,.85);
      }
      .cookie-banner__text a {
        color: #c9a4ff;
        text-decoration: underline;
      }
      .cookie-banner__actions {
        display: flex;
        gap: 10px;
        flex-shrink: 0;
        flex-wrap: wrap;
      }
      .cookie-banner__btn {
        font-size: 13px;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: background .2s ease, border-color .2s ease, transform .15s ease;
        white-space: nowrap;
        line-height: 1;
      }
      .cookie-banner__btn:active { transform: scale(.96); }
      .cookie-banner__btn--ghost {
        background: transparent;
        color: #ffffff;
        border-color: rgba(255,255,255,.45);
      }
      .cookie-banner__btn--ghost:hover {
        background: rgba(255,255,255,.10);
        border-color: rgba(255,255,255,.70);
      }
      .cookie-banner__btn--accept {
        background: #6a0dad;
        color: #ffffff;
        border-color: #6a0dad;
      }
      .cookie-banner__btn--accept:hover {
        background: #5a0b99;
        border-color: #5a0b99;
      }

      @media (max-width: 640px) {
        .cookie-banner__inner { flex-direction: column; align-items: stretch; }
        .cookie-banner__actions { flex-direction: column; }
        .cookie-banner__btn { width: 100%; text-align: center; }
      }
    `;
    document.head.appendChild(style);
  }

  // ── Bannière globale (injectée directement dans <body>, jamais dans le footer) ──
  function initBanner() {
    if (getPrefs()) return; // déjà répondu

    injectBannerCSS();

    const banner = document.createElement('div');
    banner.className = 'cookie-banner';
    banner.id = 'cookieBanner';
    banner.setAttribute('role', 'dialog');
    banner.setAttribute('aria-label', 'Consentement aux cookies');
    banner.innerHTML = `
      <div class="cookie-banner__inner">
        <p class="cookie-banner__text">
          Nous utilisons des cookies pour assurer le bon fonctionnement du site et améliorer votre expérience.
          Vous pouvez accepter, refuser, ou personnaliser vos préférences sur notre
          <a href="configuration-cookies.php">page de configuration des cookies</a>.
        </p>
        <div class="cookie-banner__actions">
          <button type="button" class="cookie-banner__btn cookie-banner__btn--ghost" id="cookieBannerRefuse">Tout refuser</button>
          <button type="button" class="cookie-banner__btn cookie-banner__btn--accept" id="cookieBannerAccept">Tout accepter</button>
        </div>
      </div>
    `;

    // Inséré directement dans <body> (position: fixed garantit l'affichage en bas de l'écran)
    document.body.appendChild(banner);

    // Double requestAnimationFrame pour déclencher la transition CSS
    requestAnimationFrame(() => {
      requestAnimationFrame(() => banner.classList.add('visible'));
    });

    document.getElementById('cookieBannerAccept').addEventListener('click', () => {
      savePrefs({ personnalisation: true, mesure: true, sociaux: true });
      closeBanner();
    });
    document.getElementById('cookieBannerRefuse').addEventListener('click', () => {
      savePrefs({ personnalisation: false, mesure: false, sociaux: false });
      closeBanner();
    });
  }

  function closeBanner() {
    const banner = document.getElementById('cookieBanner');
    if (!banner) return;
    banner.classList.remove('visible');
    setTimeout(() => banner.remove(), 400);
  }

  // ── Page de configuration détaillée ──
  function initConfigPage() {
    const togglePerso   = document.getElementById('cookiePersonnalisation');
    const toggleMesure  = document.getElementById('cookieMesure');
    const toggleSociaux = document.getElementById('cookieSociaux');
    if (!togglePerso) return; // pas sur cette page

    const prefs = getPrefs() || { personnalisation: false, mesure: false, sociaux: false };
    togglePerso.checked   = !!prefs.personnalisation;
    toggleMesure.checked  = !!prefs.mesure;
    toggleSociaux.checked = !!prefs.sociaux;

    const confirmBox = document.getElementById('cookieConfirm');
    function showConfirm() {
      confirmBox.classList.add('visible');
      setTimeout(() => confirmBox.classList.remove('visible'), 3000);
    }

    document.getElementById('btnEnregistrer').addEventListener('click', () => {
      savePrefs({
        personnalisation: togglePerso.checked,
        mesure: toggleMesure.checked,
        sociaux: toggleSociaux.checked,
      });
      showConfirm();
    });

    document.getElementById('btnAccepterTout').addEventListener('click', () => {
      togglePerso.checked   = true;
      toggleMesure.checked  = true;
      toggleSociaux.checked = true;
      savePrefs({ personnalisation: true, mesure: true, sociaux: true });
      showConfirm();
    });

    document.getElementById('btnRefuserTout').addEventListener('click', () => {
      togglePerso.checked   = false;
      toggleMesure.checked  = false;
      toggleSociaux.checked = false;
      savePrefs({ personnalisation: false, mesure: false, sociaux: false });
      showConfirm();
    });

    document.getElementById('btnAnnuler').addEventListener('click', () => {
      const current = getPrefs() || { personnalisation: false, mesure: false, sociaux: false };
      togglePerso.checked   = !!current.personnalisation;
      toggleMesure.checked  = !!current.mesure;
      toggleSociaux.checked = !!current.sociaux;
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    initBanner();
    initConfigPage();
  });
})();
