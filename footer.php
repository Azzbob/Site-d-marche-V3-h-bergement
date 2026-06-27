<style>
  .footer { background: #6a0dad !important; }
  .footer__col-title { color: #ffffff !important; }
  .footer__col-text, .footer__col-text a, .footer__list a { color: rgba(255,255,255,.6) !important; }
  .footer__col-text a:hover, .footer__list a:hover { color: #ffffff !important; }
  .footer__logo-name { color: #ffffff !important; }
  .footer__bottom { color: rgba(255,255,255,.35) !important; }
  .newsletter__input { color: #ffffff !important; }
  .newsletter .btn--primary { background: #ffffff !important; color: #6a0dad !important; border-color: #ffffff !important; }
  .newsletter .btn--primary:hover { background: #f0e6fa !important; border-color: #f0e6fa !important; }
  .back-to-top { background: #ffffff !important; color: #6a0dad !important; }
  .back-to-top:hover { background: #f0e6fa !important; }
</style>
<footer class="footer" id="contact">
  <div class="footer__inner">

    <div class="footer__col">
      <p class="footer__col-title">ABONNEZ-VOUS À NOTRE NEWSLETTER</p>
      <p class="footer__col-text">Restez informé des dernières mises à jour et nouveaux liens.</p>
      <div class="newsletter">
        <input type="email" placeholder="Votre adresse email" class="newsletter__input">
        <button class="btn btn--primary btn--sm">S'abonner</button>
      </div>
    </div>

    <div class="footer__col">
      <p class="footer__col-title">BESOIN D'AIDE ?</p>
      <p class="footer__col-text">Appelez-nous : 06 02 46 93 14</p>
      <p class="footer__col-text"><a href="contact.php">Contacts</a></p>
      <p class="footer__col-text"><a href="faq.php">FAQ</a></p>
    </div>

    <div class="footer__col">
      <p class="footer__col-title">CONDITIONS ET MENTIONS LÉGALES</p>
      <ul class="footer__list">
        <li><a href="mentions-legales.php">Mentions légales</a></li>
        <li><a href="confidentialite.php">Politique de Confidentialité</a></li>
        <li><a href="cookies.php">Politique relative aux cookies</a></li>
        <li><a href="configuration-cookies.php">Configuration des cookies</a></li>
        <li><a href="cgv.php">Conditions générales de ventes</a></li>
      </ul>
    </div>

    <div class="footer__col footer__col--logo">
      <div class="footer__logo-box">
        <img src="logo.png" alt="Liens Démarches" class="footer__logo">
        <span class="footer__logo-name">LIENS<br><small>DÉMARCHES</small></span>
      </div>
    </div>

  </div>
  <div class="footer__bottom">
    <p>© <?= date('Y') ?> Liens Démarches — Tous droits réservés</p>
  </div>
</footer>


<!-- ══════════════════════════════════════════
     BOUTON RETOUR EN HAUT
══════════════════════════════════════════ -->
<button class="back-to-top" id="backToTop" aria-label="Retour en haut">↑</button>

<?= $extra_js ?? '' ?>
<script src="cookie-consent.js"></script>

<script>
// Back to top — disponible sur toutes les pages
(function () {
  const backBtn = document.getElementById('backToTop');
  if (!backBtn) return;
  window.addEventListener('scroll', () => {
    backBtn.classList.toggle('visible', window.scrollY > 400);
  });
  backBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
})();
</script>

</body>
</html>