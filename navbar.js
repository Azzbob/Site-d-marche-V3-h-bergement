// ============================================================
//  navbar.js  —  Burger menu Liens Démarches
// ============================================================

(function () {
  const btn      = document.getElementById('burgerBtn');
  const panel    = document.getElementById('burgerPanel');
  const overlay  = document.getElementById('burgerOverlay');
  const closeBtn = document.getElementById('burgerClose');

  function openMenu() {
    panel.classList.add('open');
    overlay.classList.add('open');
    btn.classList.add('active');
    btn.setAttribute('aria-expanded', 'true');
    panel.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closeMenu() {
    panel.classList.remove('open');
    overlay.classList.remove('open');
    btn.classList.remove('active');
    btn.setAttribute('aria-expanded', 'false');
    panel.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  btn.addEventListener('click', () => {
    panel.classList.contains('open') ? closeMenu() : openMenu();
  });

  closeBtn.addEventListener('click', closeMenu);
  overlay.addEventListener('click', closeMenu);

  // Fermeture avec Échap
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeMenu();
  });
})();
