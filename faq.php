<?php
// ============================================================
//  faq.php  —  Foire Aux Questions
// ============================================================
session_start();
require_once 'db.php';
require_once 'auto-login.php';

$page_title = 'FAQ – Liens Démarches';
$page_desc  = 'FAQ Liens Démarches – Réponses aux questions fréquentes sur l utilisation du site et des démarches administratives.';
$extra_css  = '<link rel="stylesheet" href="legal-pages.css">';

include 'header.php';
?>

<style>
/* ── FAQ HERO ── */
.faq-hero {
  text-align: center;
  padding: 56px 24px 64px;
  background: linear-gradient(135deg, #3b006e 0%, #6a0dad 55%, #9b30ff 100%);
  color: #ffffff;
  position: relative;
  overflow: hidden;
}
.faq-hero::before {
  content: '';
  position: absolute;
  width: 420px; height: 420px;
  border-radius: 50%;
  background: rgba(255,255,255,.05);
  right: -120px; top: -140px;
}
.faq-hero__eyebrow {
  font-size: 11px; font-weight: 700;
  letter-spacing: .16em; text-transform: uppercase;
  color: rgba(255,255,255,.7); margin-bottom: 12px;
}
.faq-hero__title {
  font-size: clamp(26px, 4vw, 38px);
  font-weight: 800; letter-spacing: -.01em;
  margin-bottom: 14px;
}
.faq-hero__sub {
  font-size: 15px; color: rgba(255,255,255,.75);
  max-width: 520px; margin: 0 auto 28px; line-height: 1.6;
}

/* ── BARRE DE RECHERCHE FAQ ── */
.faq-search {
  display: flex; align-items: center;
  max-width: 460px; margin: 0 auto;
  background: rgba(255,255,255,.12);
  border: 1px solid rgba(255,255,255,.25);
  border-radius: 30px;
  padding: 6px 8px 6px 20px;
  transition: background .2s, border-color .2s;
}
.faq-search:focus-within {
  background: rgba(255,255,255,.18);
  border-color: rgba(255,255,255,.5);
}
.faq-search__input {
  flex: 1; border: none; outline: none;
  background: transparent; color: #fff;
  font-size: 14px;
}
.faq-search__input::placeholder { color: rgba(255,255,255,.5); }
.faq-search__btn {
  width: 36px; height: 36px; border-radius: 50%;
  background: rgba(255,255,255,.2); border: none;
  color: #fff; display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: background .2s; flex-shrink: 0;
}
.faq-search__btn:hover { background: rgba(255,255,255,.3); }

/* ── CONTENU ── */
.faq-content {
  max-width: 820px;
  margin: 0 auto;
  padding: 56px 24px 90px;
}

/* ── FILTRES CATÉGORIES ── */
.faq-cats {
  display: flex; flex-wrap: wrap; gap: 8px;
  margin-bottom: 40px;
}
.faq-cat {
  font-size: 13px; font-weight: 600;
  padding: 7px 16px; border-radius: 20px;
  border: 1.5px solid #e0e0e8;
  background: #fff; color: #555;
  cursor: pointer;
  transition: background .2s, border-color .2s, color .2s;
}
.faq-cat:hover, .faq-cat.active {
  background: #6a0dad; border-color: #6a0dad; color: #fff;
}

/* ── GROUPES ── */
.faq-group { margin-bottom: 48px; }
.faq-group__title {
  font-size: 13px; font-weight: 700;
  letter-spacing: .1em; text-transform: uppercase;
  color: #6a0dad; margin-bottom: 16px;
  display: flex; align-items: center; gap: 10px;
}
.faq-group__title::after {
  content: ''; flex: 1;
  height: 1px; background: #e0e0e8;
}

/* ── ACCORDÉON ── */
.faq-item {
  background: #fff;
  border: 1px solid #e0e0e8;
  border-radius: 12px;
  margin-bottom: 10px;
  overflow: hidden;
  transition: box-shadow .2s, border-color .2s;
}
.faq-item:hover { box-shadow: 0 4px 18px rgba(106,13,173,.08); border-color: #d8c5ef; }
.faq-item.open  { border-color: #6a0dad; box-shadow: 0 4px 18px rgba(106,13,173,.12); }

.faq-item__btn {
  width: 100%; background: none; border: none;
  display: flex; align-items: center; justify-content: space-between;
  gap: 16px;
  padding: 18px 22px;
  text-align: left; cursor: pointer;
}
.faq-item__question {
  font-size: 14px; font-weight: 600; color: #1a1a2e; line-height: 1.4;
}
.faq-item__icon {
  width: 28px; height: 28px; border-radius: 50%;
  background: #f0e6fa; color: #6a0dad;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; font-size: 18px; font-weight: 300;
  transition: transform .3s ease, background .2s;
}
.faq-item.open .faq-item__icon { transform: rotate(45deg); background: #6a0dad; color: #fff; }

.faq-item__body {
  max-height: 0; overflow: hidden;
  transition: max-height .35s ease, padding .35s ease;
}
.faq-item.open .faq-item__body { max-height: 500px; }

.faq-item__answer {
  padding: 0 22px 20px;
  font-size: 14px; color: #555; line-height: 1.75;
  border-top: 1px solid #f0e6fa;
  padding-top: 16px;
}
.faq-item__answer a { color: #6a0dad; text-decoration: underline; }
.faq-item__answer a:hover { color: #5a0b99; }
.faq-item__answer ul { margin: 8px 0 8px 20px; display: flex; flex-direction: column; gap: 5px; }

/* ── BLOC CONTACT ── */
.faq-contact {
  margin-top: 56px;
  background: linear-gradient(135deg, #f0e6fa 0%, #e8d5f5 100%);
  border: 1px solid #d8c5ef;
  border-radius: 16px;
  padding: 36px 40px;
  display: flex; align-items: center; gap: 28px;
  flex-wrap: wrap;
}
.faq-contact__icon {
  width: 60px; height: 60px; flex-shrink: 0;
  background: #6a0dad; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 26px;
}
.faq-contact__text { flex: 1; min-width: 200px; }
.faq-contact__text h3 { font-size: 17px; font-weight: 700; color: #1a1a2e; margin-bottom: 6px; }
.faq-contact__text p  { font-size: 14px; color: #555; line-height: 1.6; }
.faq-contact__actions { display: flex; gap: 10px; flex-wrap: wrap; }
.btn-faq {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 13px; font-weight: 600;
  padding: 10px 20px; border-radius: 8px; border: 2px solid transparent;
  cursor: pointer; text-decoration: none;
  transition: background .2s, border-color .2s;
}
.btn-faq--primary { background: #6a0dad; color: #fff; border-color: #6a0dad; }
.btn-faq--primary:hover { background: #5a0b99; border-color: #5a0b99; }
.btn-faq--outline { background: #fff; color: #6a0dad; border-color: #6a0dad; }
.btn-faq--outline:hover { background: #f0e6fa; }

/* ── AUCUN RÉSULTAT ── */
.faq-empty { text-align: center; padding: 40px 20px; color: #888; font-size: 14px; display: none; }
.faq-empty.show { display: block; }

@media (max-width: 640px) {
  .faq-contact { flex-direction: column; padding: 24px 20px; }
  .faq-contact__actions { flex-direction: column; width: 100%; }
  .btn-faq { justify-content: center; }
}
</style>

<!-- HERO -->
<div class="faq-hero">
  <p class="faq-hero__eyebrow">AIDE &amp; SUPPORT</p>
  <h1 class="faq-hero__title">Foire Aux Questions</h1>
  <p class="faq-hero__sub">Trouvez rapidement les réponses à vos questions sur Liens Démarches.</p>
  <div class="faq-search">
    <input type="text" class="faq-search__input" id="faqSearchInput" placeholder="Rechercher une question…" autocomplete="off">
    <button class="faq-search__btn" aria-label="Rechercher">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
      </svg>
    </button>
  </div>
</div>

<!-- CONTENU -->
<div class="faq-content">

  <!-- Filtres catégories -->
  <div class="faq-cats" id="faqCats">
    <button class="faq-cat active" data-cat="all">Tout afficher</button>
    <button class="faq-cat" data-cat="compte">Mon compte</button>
    <button class="faq-cat" data-cat="liens">Les liens</button>
    <button class="faq-cat" data-cat="donnees">Données &amp; confidentialité</button>
    <button class="faq-cat" data-cat="technique">Problèmes techniques</button>
    <button class="faq-cat" data-cat="abonnement">Abonnement</button>
  </div>

  <!-- Aucun résultat -->
  <div class="faq-empty" id="faqEmpty">
    <p>😕 Aucune question ne correspond à votre recherche.<br>
    N'hésitez pas à <a href="contact.php" style="color:#6a0dad">nous contacter</a> directement.</p>
  </div>

  <!-- Groupe : Mon compte -->
  <div class="faq-group" data-group="compte">
    <p class="faq-group__title">Mon compte</p>

    <div class="faq-item" data-cat="compte">
      <button class="faq-item__btn" aria-expanded="false">
        <span class="faq-item__question">Comment créer un compte sur Liens Démarches ?</span>
        <span class="faq-item__icon">+</span>
      </button>
      <div class="faq-item__body" role="region">
        <div class="faq-item__answer">
          Cliquez sur <strong>« Créer un compte »</strong> en haut à droite ou depuis la page
          <a href="connexion.php">connexion</a>. Renseignez votre prénom, nom et adresse email, puis
          choisissez un mot de passe d'au moins 8 caractères. Un email de confirmation peut vous être envoyé.
          Une fois votre compte créé, vous accédez immédiatement à tous les liens disponibles.
        </div>
      </div>
    </div>

    <div class="faq-item" data-cat="compte">
      <button class="faq-item__btn" aria-expanded="false">
        <span class="faq-item__question">J'ai oublié mon mot de passe, que faire ?</span>
        <span class="faq-item__icon">+</span>
      </button>
      <div class="faq-item__body" role="region">
        <div class="faq-item__answer">
          Rendez-vous sur la page <a href="mot-de-passe-oublie.php">Mot de passe oublié</a>, saisissez
          votre adresse email et vous recevrez un lien de réinitialisation valable 1 heure.
          Pensez à vérifier votre dossier <strong>spam</strong> si vous ne recevez rien sous quelques minutes.
        </div>
      </div>
    </div>

    <div class="faq-item" data-cat="compte">
      <button class="faq-item__btn" aria-expanded="false">
        <span class="faq-item__question">Comment modifier mes informations personnelles ?</span>
        <span class="faq-item__icon">+</span>
      </button>
      <div class="faq-item__body" role="region">
        <div class="faq-item__answer">
          Connectez-vous puis rendez-vous dans <a href="mon-compte.php">Mon compte</a>. Vous pouvez y
          modifier votre nom, prénom, adresse email et mot de passe à tout moment. Les modifications
          sont enregistrées instantanément.
        </div>
      </div>
    </div>

    <div class="faq-item" data-cat="compte">
      <button class="faq-item__btn" aria-expanded="false">
        <span class="faq-item__question">Puis-je me connecter avec Google ou Facebook ?</span>
        <span class="faq-item__icon">+</span>
      </button>
      <div class="faq-item__body" role="region">
        <div class="faq-item__answer">
          Oui ! La page de connexion propose une authentification via <strong>Google</strong> et
          <strong>Facebook</strong>. Vous n'avez alors pas besoin de créer un mot de passe distinct :
          votre compte est lié à votre profil social et la connexion se fait en un clic.
        </div>
      </div>
    </div>
  </div>

  <!-- Groupe : Les liens -->
  <div class="faq-group" data-group="liens">
    <p class="faq-group__title">Les liens &amp; démarches</p>

    <div class="faq-item" data-cat="liens">
      <button class="faq-item__btn" aria-expanded="false">
        <span class="faq-item__question">Qu'est-ce que Liens Démarches ?</span>
        <span class="faq-item__icon">+</span>
      </button>
      <div class="faq-item__body" role="region">
        <div class="faq-item__answer">
          Liens Démarches est un annuaire centralisé qui regroupe les liens officiels vers les services
          publics et organismes essentiels : Identité, Social &amp; Santé, Travail &amp; Retraite, Logement,
          Finances, Droits &amp; Services. L'objectif est de vous faire gagner du temps en évitant de
          chercher les bons sites sur les moteurs de recherche.
        </div>
      </div>
    </div>

    <div class="faq-item" data-cat="liens">
      <button class="faq-item__btn" aria-expanded="false">
        <span class="faq-item__question">Les liens sont-ils officiels et sécurisés ?</span>
        <span class="faq-item__icon">+</span>
      </button>
      <div class="faq-item__body" role="region">
        <div class="faq-item__answer">
          Oui. Tous les liens référencés sur Liens Démarches pointent vers les sites officiels des
          organismes publics (domaines en <strong>.gouv.fr</strong>, <strong>.service-public.fr</strong>
          ou équivalents). Ils sont vérifiés régulièrement. Si vous détectez un lien cassé ou incorrect,
          signalez-le nous via la page <a href="contact.php">Contact</a>.
        </div>
      </div>
    </div>

    <div class="faq-item" data-cat="liens">
      <button class="faq-item__btn" aria-expanded="false">
        <span class="faq-item__question">Comment ajouter un lien en favori ?</span>
        <span class="faq-item__icon">+</span>
      </button>
      <div class="faq-item__body" role="region">
        <div class="faq-item__answer">
          Vous devez être connecté. Sur chaque lien, cliquez sur l'icône ⭐ pour l'ajouter à vos favoris.
          Retrouvez l'ensemble de vos favoris dans la page <a href="favoris.php">Mes favoris</a>,
          accessible depuis le menu.
        </div>
      </div>
    </div>

    <div class="faq-item" data-cat="liens">
      <button class="faq-item__btn" aria-expanded="false">
        <span class="faq-item__question">Un lien ne fonctionne plus, que faire ?</span>
        <span class="faq-item__icon">+</span>
      </button>
      <div class="faq-item__body" role="region">
        <div class="faq-item__answer">
          Les sites gouvernementaux peuvent changer d'URL sans préavis. Si vous constatez un lien
          défaillant, merci de nous le signaler via notre <a href="contact.php">formulaire de contact</a>
          en précisant la page et le lien concerné. Nous le corrigerons dans les meilleurs délais.
        </div>
      </div>
    </div>
  </div>

  <!-- Groupe : Données & confidentialité -->
  <div class="faq-group" data-group="donnees">
    <p class="faq-group__title">Données &amp; confidentialité</p>

    <div class="faq-item" data-cat="donnees">
      <button class="faq-item__btn" aria-expanded="false">
        <span class="faq-item__question">Quelles données personnelles collectez-vous ?</span>
        <span class="faq-item__icon">+</span>
      </button>
      <div class="faq-item__body" role="region">
        <div class="faq-item__answer">
          Nous collectons uniquement les données nécessaires : prénom, nom et email pour la création
          de compte, ainsi que des données techniques anonymes de navigation (pages visitées, durée).
          Aucune donnée sensible n'est collectée. Consultez notre
          <a href="confidentialite.php">Politique de confidentialité</a> pour le détail complet.
        </div>
      </div>
    </div>

    <div class="faq-item" data-cat="donnees">
      <button class="faq-item__btn" aria-expanded="false">
        <span class="faq-item__question">Comment gérer ou supprimer mes cookies ?</span>
        <span class="faq-item__icon">+</span>
      </button>
      <div class="faq-item__body" role="region">
        <div class="faq-item__answer">
          Rendez-vous sur la page <a href="configuration-cookies.php">Configuration des cookies</a>
          pour activer ou désactiver chaque catégorie. Vous pouvez aussi consulter notre
          <a href="cookies.php">Politique relative aux cookies</a> pour comprendre leur utilisation.
          Les cookies strictement nécessaires ne peuvent pas être désactivés.
        </div>
      </div>
    </div>

    <div class="faq-item" data-cat="donnees">
      <button class="faq-item__btn" aria-expanded="false">
        <span class="faq-item__question">Comment exercer mon droit à la suppression de mes données ?</span>
        <span class="faq-item__icon">+</span>
      </button>
      <div class="faq-item__body" role="region">
        <div class="faq-item__answer">
          Conformément au RGPD, vous pouvez à tout moment demander la suppression de votre compte
          et de toutes les données associées en nous contactant via le
          <a href="contact.php">formulaire de contact</a> avec pour objet
          <em>« Demande de suppression de données »</em>. Votre demande sera traitée sous 30 jours.
        </div>
      </div>
    </div>
  </div>

  <!-- Groupe : Problèmes techniques -->
  <div class="faq-group" data-group="technique">
    <p class="faq-group__title">Problèmes techniques</p>

    <div class="faq-item" data-cat="technique">
      <button class="faq-item__btn" aria-expanded="false">
        <span class="faq-item__question">Le site ne s'affiche pas correctement sur mon téléphone.</span>
        <span class="faq-item__icon">+</span>
      </button>
      <div class="faq-item__body" role="region">
        <div class="faq-item__answer">
          Liens Démarches est conçu pour fonctionner sur tous les appareils. Si vous rencontrez un
          problème d'affichage, essayez de :
          <ul>
            <li>Vider le cache de votre navigateur (Ctrl+F5 sur PC, ou dans les paramètres sur mobile).</li>
            <li>Utiliser un navigateur à jour (Chrome, Firefox, Safari, Edge).</li>
            <li>Désactiver les extensions qui pourraient bloquer le contenu.</li>
          </ul>
          Si le problème persiste, <a href="contact.php">contactez-nous</a> en précisant votre appareil et navigateur.
        </div>
      </div>
    </div>

    <div class="faq-item" data-cat="technique">
      <button class="faq-item__btn" aria-expanded="false">
        <span class="faq-item__question">Je ne reçois pas les emails (confirmation, mot de passe…).</span>
        <span class="faq-item__icon">+</span>
      </button>
      <div class="faq-item__body" role="region">
        <div class="faq-item__answer">
          Vérifiez en priorité votre dossier <strong>spam / courrier indésirable</strong>. Si l'email
          ne s'y trouve pas, attendez quelques minutes car certains opérateurs peuvent retarder la
          livraison. Assurez-vous aussi que l'adresse email saisie est correcte. En cas de doute,
          <a href="contact.php">contactez notre support</a>.
        </div>
      </div>
    </div>

    <div class="faq-item" data-cat="technique">
      <button class="faq-item__btn" aria-expanded="false">
        <span class="faq-item__question">Je n'arrive pas à me connecter malgré un mot de passe correct.</span>
        <span class="faq-item__icon">+</span>
      </button>
      <div class="faq-item__body" role="region">
        <div class="faq-item__answer">
          Plusieurs causes possibles :
          <ul>
            <li>Le <strong>verrouillage des majuscules</strong> est activé sur votre clavier.</li>
            <li>Votre navigateur a auto-rempli un ancien mot de passe incorrect.</li>
            <li>Votre compte a été temporairement suspendu suite à trop de tentatives échouées.</li>
          </ul>
          Utilisez la fonction <a href="mot-de-passe-oublie.php">Mot de passe oublié</a> pour en
          définir un nouveau, ou <a href="contact.php">contactez-nous</a>.
        </div>
      </div>
    </div>
  </div>

  <!-- Groupe : Abonnement -->
  <div class="faq-group" data-group="abonnement">
    <p class="faq-group__title">Abonnement &amp; newsletter</p>

    <div class="faq-item" data-cat="abonnement">
      <button class="faq-item__btn" aria-expanded="false">
        <span class="faq-item__question">Le site est-il gratuit ?</span>
        <span class="faq-item__icon">+</span>
      </button>
      <div class="faq-item__body" role="region">
        <div class="faq-item__answer">
          Oui, Liens Démarches est entièrement <strong>gratuit</strong>. La création de compte, l'accès
          aux liens et la gestion des favoris sont sans frais. Nous ne proposons pas d'abonnement payant.
        </div>
      </div>
    </div>

    <div class="faq-item" data-cat="abonnement">
      <button class="faq-item__btn" aria-expanded="false">
        <span class="faq-item__question">Comment me désabonner de la newsletter ?</span>
        <span class="faq-item__icon">+</span>
      </button>
      <div class="faq-item__body" role="region">
        <div class="faq-item__answer">
          Chaque email de newsletter contient un lien de désabonnement en bas de page. Un clic suffit
          pour ne plus recevoir nos communications. Vous pouvez aussi nous en faire la demande via le
          <a href="contact.php">formulaire de contact</a>.
        </div>
      </div>
    </div>
  </div>

  <!-- Bloc contact -->
  <div class="faq-contact">
    <div class="faq-contact__icon">💬</div>
    <div class="faq-contact__text">
      <h3>Vous n'avez pas trouvé votre réponse ?</h3>
      <p>Notre équipe est disponible pour répondre à toutes vos questions. Contactez-nous par formulaire ou par téléphone.</p>
    </div>
    <div class="faq-contact__actions">
      <a href="contact.php" class="btn-faq btn-faq--primary">Nous contacter</a>
      <a href="tel:0602469314" class="btn-faq btn-faq--outline">📞 06 02 46 93 14</a>
    </div>
  </div>

</div>

<script>
(function () {
  // ── Accordéon ──
  document.querySelectorAll('.faq-item__btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const item = btn.closest('.faq-item');
      const isOpen = item.classList.contains('open');
      // Ferme tous les autres
      document.querySelectorAll('.faq-item.open').forEach(el => {
        el.classList.remove('open');
        el.querySelector('.faq-item__btn').setAttribute('aria-expanded', 'false');
      });
      if (!isOpen) {
        item.classList.add('open');
        btn.setAttribute('aria-expanded', 'true');
      }
    });
  });

  // ── Filtres catégories ──
  document.querySelectorAll('.faq-cat').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.faq-cat').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const cat = btn.dataset.cat;
      filterFaq(cat, document.getElementById('faqSearchInput').value.trim().toLowerCase());
    });
  });

  // ── Recherche ──
  document.getElementById('faqSearchInput').addEventListener('input', function () {
    const activeCat = document.querySelector('.faq-cat.active').dataset.cat;
    filterFaq(activeCat, this.value.trim().toLowerCase());
  });

  function filterFaq(cat, q) {
    let visible = 0;
    document.querySelectorAll('.faq-item').forEach(item => {
      const matchCat = cat === 'all' || item.dataset.cat === cat;
      const text     = item.textContent.toLowerCase();
      const matchQ   = !q || text.includes(q);
      const show     = matchCat && matchQ;
      item.style.display = show ? '' : 'none';
      if (show) visible++;
    });

    // Cache les groupes vides
    document.querySelectorAll('.faq-group').forEach(grp => {
      const hasVisible = [...grp.querySelectorAll('.faq-item')].some(i => i.style.display !== 'none');
      grp.style.display = hasVisible ? '' : 'none';
    });

    document.getElementById('faqEmpty').classList.toggle('show', visible === 0);
  }
})();
</script>

<?php include 'footer.php'; ?>
