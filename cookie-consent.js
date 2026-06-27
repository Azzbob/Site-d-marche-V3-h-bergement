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

  // ── Bannière globale (injectée sur toutes les pages) ──
  function initBanner() {
    if (getPrefs()) return; // déjà répondu

    const banner = document.createElement('div');
    banner.className = 'cookie-banner';
    banner.id = 'cookieBanner';
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
    document.body.appendChild(banner);

    requestAnimationFrame(() => banner.classList.add('visible'));

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
    const togglePerso  = document.getElementById('cookiePersonnalisation');
    const toggleMesure = document.getElementById('cookieMesure');
    const toggleSociaux = document.getElementById('cookieSociaux');
    if (!togglePerso) return; // pas sur cette page

    const prefs = getPrefs() || { personnalisation: false, mesure: false, sociaux: false };
    togglePerso.checked = !!prefs.personnalisation;
    toggleMesure.checked = !!prefs.mesure;
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
      togglePerso.checked = true;
      toggleMesure.checked = true;
      toggleSociaux.checked = true;
      savePrefs({ personnalisation: true, mesure: true, sociaux: true });
      showConfirm();
    });

    document.getElementById('btnRefuserTout').addEventListener('click', () => {
      togglePerso.checked = false;
      toggleMesure.checked = false;
      toggleSociaux.checked = false;
      savePrefs({ personnalisation: false, mesure: false, sociaux: false });
      showConfirm();
    });

    document.getElementById('btnAnnuler').addEventListener('click', () => {
      const current = getPrefs() || { personnalisation: false, mesure: false, sociaux: false };
      togglePerso.checked = !!current.personnalisation;
      toggleMesure.checked = !!current.mesure;
      toggleSociaux.checked = !!current.sociaux;
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    initBanner();
    initConfigPage();
  });
})();
