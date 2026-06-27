// ============================================================
//  index.js  —  JavaScript de la page d'accueil Liens Démarches
// ============================================================

// --- Carrousel logos ---
(function () {
  const track    = document.getElementById('carouselTrack');
  const viewport = document.getElementById('carousel');
  const prevBtn  = document.getElementById('prevBtn');
  const nextBtn  = document.getElementById('nextBtn');

  if (!track || !viewport) return;

  // Largeur d'un item + gap (140px + 20px)
  const ITEM_W = 160;
  // Nombre d'items visibles à la fois
  function visibleCount() { return Math.max(1, Math.floor(viewport.clientWidth / ITEM_W)); }
  // Nombre total d'items
  const total = track.children.length;

  let idx = 0; // index de l'item le plus à gauche affiché

  function maxIdx() { return Math.max(0, total - visibleCount()); }

  function updateCarousel(animate = true) {
    track.style.transition = animate ? 'transform .35s ease' : 'none';
    track.style.transform  = `translateX(-${idx * ITEM_W}px)`;
  }

  // Avance d'un pas, revient au début une fois la fin atteinte (boucle infinie)
  function next() {
    idx = idx >= maxIdx() ? 0 : idx + 1;
    updateCarousel();
  }

  // Recule d'un pas, repart de la fin si on est au début (boucle infinie)
  function prev() {
    idx = idx <= 0 ? maxIdx() : idx - 1;
    updateCarousel();
  }

  if (prevBtn) prevBtn.addEventListener('click', () => { prev(); restartAutoplay(); });
  if (nextBtn) nextBtn.addEventListener('click', () => { next(); restartAutoplay(); });

  // ── Défilement automatique ──
  const AUTOPLAY_DELAY = 1500; // ms entre chaque défilement
  let autoplayTimer = null;

  function startAutoplay() {
    stopAutoplay();
    if (total > visibleCount()) {
      autoplayTimer = setInterval(next, AUTOPLAY_DELAY);
    }
  }

  function stopAutoplay() {
    if (autoplayTimer) {
      clearInterval(autoplayTimer);
      autoplayTimer = null;
    }
  }

  function restartAutoplay() {
    stopAutoplay();
    startAutoplay();
  }

  // Pause au survol (souris) pour laisser le temps de cliquer/regarder
  viewport.addEventListener('mouseenter', stopAutoplay);
  viewport.addEventListener('mouseleave', startAutoplay);

  // ── Défilement à la molette de la souris ──
  let wheelCooldown = false;
  viewport.addEventListener('wheel', (e) => {
    e.preventDefault();
    if (wheelCooldown) return;
    wheelCooldown = true;

    const delta = e.deltaY !== 0 ? e.deltaY : e.deltaX;
    if (delta > 0) next(); else prev();

    restartAutoplay();
    setTimeout(() => { wheelCooldown = false; }, 250);
  }, { passive: false });

  // Recalcule si la fenêtre est redimensionnée
  window.addEventListener('resize', () => {
    idx = Math.min(idx, maxIdx());
    updateCarousel(false);
  });

  updateCarousel(false);
  startAutoplay();
})();

// --- Recherche live + filtre catégorie dans les résultats ---
const searchInput     = document.getElementById('searchInput');
const offresContainer = document.getElementById('offresContainer');
const offresEmpty     = document.getElementById('offresEmpty');
const offresHint      = document.getElementById('offresHint');

// Nombre de cards affichées au maximum en même temps
const LIMITE_AFFICHAGE = 2;

// Enlève les accents pour une recherche plus tolérante
// (ex : "chomage" retrouve "chômage")
function normalize(str) {
  return (str || '')
    .toString()
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '');
}

const selectedCats = new Set();

function applyFilters() {
  const rawQuery = normalize(searchInput.value).trim();
  const words = rawQuery.split(/\s+/).filter(Boolean);

  const cards = Array.from(document.querySelectorAll('#offresContainer .lien-card'));
  let matches = 0;
  let shown   = 0;

  cards.forEach(card => {
    const haystack = normalize(card.dataset.search || '');
    const cat = card.dataset.cat || '';

    const matchSearch = words.length === 0
      || words.some(w => haystack.includes(w));

    const matchCat = selectedCats.size === 0 || selectedCats.has(cat);

    const correspond = matchSearch && matchCat;

    if (correspond) {
      matches++;
      if (shown < LIMITE_AFFICHAGE) {
        card.style.display = '';
        shown++;
      } else {
        card.style.display = 'none';
      }
    } else {
      card.style.display = 'none';
    }
  });

  if (offresEmpty) {
    offresEmpty.style.display = matches === 0 ? '' : 'none';
  }

  if (offresHint) {
    const reste = matches - shown;
    if (reste > 0) {
      offresHint.textContent = `+${reste} autre${reste > 1 ? 's' : ''} résultat${reste > 1 ? 's' : ''} — précisez votre recherche pour les afficher`;
      offresHint.style.display = '';
    } else {
      offresHint.style.display = 'none';
    }
  }
}

if (searchInput) {
  searchInput.addEventListener('input', applyFilters);
}

const searchBtn = document.getElementById('searchBtn');
if (searchBtn) {
  searchBtn.addEventListener('click', (e) => {
    e.preventDefault();
    applyFilters();
  });
}

// --- Dropdown filtre par catégorie (élément <details> natif) ---
(function () {
  const dropdown = document.getElementById('filterDropdown');
  if (!dropdown) return;

  const panel     = document.getElementById('filterPanel');
  const resetBtn  = document.getElementById('filterResetBtn');
  const countEl   = document.getElementById('filterCount');
  const checkboxes = panel.querySelectorAll('input[type="checkbox"]');

  // Ferme le panneau si on clique en dehors (le <details> natif gère déjà l'ouverture/fermeture au clic sur "Filtre")
  document.addEventListener('click', (e) => {
    if (dropdown.open && !dropdown.contains(e.target)) {
      dropdown.open = false;
    }
  });

  checkboxes.forEach(cb => {
    cb.addEventListener('change', () => {
      if (cb.checked) {
        selectedCats.add(cb.value);
      } else {
        selectedCats.delete(cb.value);
      }
      dropdown.classList.toggle('has-filter', selectedCats.size > 0);
      countEl.textContent = selectedCats.size;
      applyFilters();
    });
  });

  resetBtn.addEventListener('click', () => {
    checkboxes.forEach(cb => { cb.checked = false; });
    selectedCats.clear();
    dropdown.classList.remove('has-filter');
    countEl.textContent = '0';
    dropdown.open = false;
    applyFilters();
  });
})();

// Affiche les 2 premiers résultats au chargement
applyFilters();