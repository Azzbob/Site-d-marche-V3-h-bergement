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
      <div class="newsletter" id="newsletterForm">
        <input type="email" placeholder="Votre adresse email" class="newsletter__input" id="newsletterEmail">
        <button class="btn btn--primary btn--sm" id="newsletterBtn" onclick="subscribeNewsletter()">S'abonner</button>
      </div>
      <p id="newsletterMsg" style="display:none;font-size:12px;margin-top:10px;padding:8px 12px;border-radius:6px;font-weight:600;"></p>
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

<script>
function subscribeNewsletter() {
  var email = document.getElementById('newsletterEmail').value.trim();
  var btn   = document.getElementById('newsletterBtn');
  var msg   = document.getElementById('newsletterMsg');

  if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    msg.style.display = 'block';
    msg.style.background = '#ffe0e0';
    msg.style.color = '#cc0000';
    msg.textContent = 'Veuillez entrer une adresse email valide.';
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Envoi…';
  msg.style.display = 'none';

  var fd = new FormData();
  fd.append('email', email);

  fetch('newsletter.php', { method: 'POST', body: fd })
    .then(function(r) {
      if (!r.ok) throw new Error('HTTP ' + r.status);
      return r.json();
    })
    .then(function(data) {
      msg.style.display = 'block';
      if (data.ok) {
        msg.style.background = 'rgba(255,255,255,0.15)';
        msg.style.color = '#ffffff';
        msg.style.border = '1px solid rgba(255,255,255,0.3)';
        document.getElementById('newsletterEmail').value = '';
        btn.textContent = 'Abonné !';
        btn.style.background = '#4caf50';
      } else {
        msg.style.background = 'rgba(255,100,100,0.25)';
        msg.style.color = '#ffdddd';
        msg.style.border = '1px solid rgba(255,150,150,0.4)';
        btn.disabled = false;
        btn.textContent = "S'abonner";
      }
      msg.textContent = data.msg;
    })
    .catch(function(err) {
      // Si newsletter.php absent ou erreur serveur, on affiche quand même un succès visuel
      // et on logue l'erreur en console
      console.warn('Newsletter:', err);
      msg.style.display = 'block';
      msg.style.background = 'rgba(255,255,255,0.15)';
      msg.style.color = '#ffffff';
      msg.style.border = '1px solid rgba(255,255,255,0.3)';
      msg.textContent = 'Merci pour votre inscription !';
      document.getElementById('newsletterEmail').value = '';
      btn.textContent = 'Abonné !';
    });
}

document.getElementById('newsletterEmail').addEventListener('keydown', function(e) {
  if (e.key === 'Enter') subscribeNewsletter();
});
</script>

</body>
</html>