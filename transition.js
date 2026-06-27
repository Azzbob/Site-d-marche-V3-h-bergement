// ============================================================
//  transition.js  —  Slide horizontal entre connexion et inscription
// ============================================================
(function () {

  var DURATION = 380;
  var EASING   = 'cubic-bezier(0.4, 0, 0.2, 1)';

  document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('page-slide');
    if (!el) return;

    document.documentElement.style.overflowX = 'hidden';
    document.body.style.overflowX = 'hidden';

    // ── SLIDE-IN à l'arrivée ──
    // On lit la direction d'entrée stockée par la page précédente
    var slideIn = sessionStorage.getItem('slide_in');
    if (slideIn) {
      sessionStorage.removeItem('slide_in');
      el.style.transition = 'none';
      el.style.transform  = 'translateX(' + slideIn + ')';

      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          el.style.transition = 'transform ' + DURATION + 'ms ' + EASING;
          el.style.transform  = 'translateX(0)';
        });
      });
    }

    // ── SLIDE-OUT au clic ──
    var links = document.querySelectorAll(
      'a[href="inscription.php"], a[href="connexion.php"]'
    );

    links.forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        var target = link.getAttribute('href');

        // connexion → inscription : sort à gauche, la destination arrive de droite
        // inscription → connexion : sort à droite, la destination arrive de gauche
        var outX, inX;
        if (target === 'inscription.php') {
          outX = '-100vw';
          inX  = '100vw';
        } else {
          outX = '100vw';
          inX  = '-100vw';
        }

        // On dit à la page suivante d'où elle doit arriver
        sessionStorage.setItem('slide_in', inX);

        el.style.transition = 'transform ' + DURATION + 'ms ' + EASING;
        el.style.transform  = 'translateX(' + outX + ')';

        setTimeout(function () {
          window.location.href = target;
        }, 320);
      });
    });
  });

})();