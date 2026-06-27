<?php
// ============================================================
//  contact.php  —  Page Contact
// ============================================================
session_start();
require_once 'db.php';
require_once 'auto-login.php';
require_once 'csrf.php';

$page_title = 'Contact – Liens Démarches';
$extra_css  = '<link rel="stylesheet" href="legal-pages.css">';

// Pré-remplissage si connecté
$user_nom    = '';
$user_prenom = '';
$user_email  = '';
if (!empty($_SESSION['user_id'])) {
    $user_prenom = $_SESSION['user_prenom'] ?? '';
    $user_nom    = $_SESSION['user_nom']    ?? '';
    $user_email  = $_SESSION['user_email']  ?? '';
}

// Traitement du formulaire
$msg_succes = '';
$msg_erreur = '';
$form_sent  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $msg_erreur = 'Session expirée, veuillez réessayer.';
    } else {
        $prenom  = trim($_POST['prenom']  ?? '');
        $nom     = trim($_POST['nom']     ?? '');
        $email   = trim($_POST['email']   ?? '');
        $sujet   = trim($_POST['sujet']   ?? '');
        $message = trim($_POST['message'] ?? '');

        if (!$prenom || !$nom || !$email || !$sujet || !$message) {
            $msg_erreur = 'Tous les champs sont obligatoires.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg_erreur = 'Adresse email invalide.';
        } elseif (strlen($message) < 20) {
            $msg_erreur = 'Votre message doit contenir au moins 20 caractères.';
        } else {
            // Ici vous pourriez appeler smtp-mailer.php pour envoyer l'email
            // Pour l'instant on simule un succès
            $form_sent  = true;
            $msg_succes = 'Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.';
        }
    }
}

include 'header.php';
?>

<style>
/* ── CONTACT HERO ── */
.contact-hero {
  text-align: center;
  padding: 56px 24px 64px;
  background: linear-gradient(135deg, #3b006e 0%, #6a0dad 55%, #9b30ff 100%);
  color: #ffffff;
  position: relative;
  overflow: hidden;
}
.contact-hero::before {
  content: '';
  position: absolute;
  width: 420px; height: 420px;
  border-radius: 50%;
  background: rgba(255,255,255,.05);
  right: -120px; top: -140px;
}
.contact-hero__eyebrow {
  font-size: 11px; font-weight: 700;
  letter-spacing: .16em; text-transform: uppercase;
  color: rgba(255,255,255,.7); margin-bottom: 12px;
}
.contact-hero__title {
  font-size: clamp(26px, 4vw, 38px);
  font-weight: 800; letter-spacing: -.01em;
  margin-bottom: 14px;
}
.contact-hero__sub {
  font-size: 15px; color: rgba(255,255,255,.75);
  max-width: 480px; margin: 0 auto; line-height: 1.6;
}

/* ── LAYOUT ── */
.contact-content {
  max-width: 1060px;
  margin: 0 auto;
  padding: 56px 24px 90px;
  display: grid;
  grid-template-columns: 1fr 340px;
  gap: 40px;
  align-items: start;
}

/* ── FORMULAIRE ── */
.contact-form-card {
  background: #fff;
  border: 1px solid #e0e0e8;
  border-radius: 16px;
  padding: 36px 40px;
  box-shadow: 0 4px 20px rgba(106,13,173,.07);
}
.contact-form-card h2 {
  font-size: 20px; font-weight: 700;
  color: #1a1a2e; margin-bottom: 6px;
}
.contact-form-card p.subtitle {
  font-size: 13px; color: #888; margin-bottom: 28px; line-height: 1.5;
}

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { display: flex; flex-direction: column; margin-bottom: 18px; }
.form-group label {
  font-size: 13px; font-weight: 600; color: #444; margin-bottom: 6px;
}
.form-group label span.required { color: #6a0dad; margin-left: 2px; }
.form-group input,
.form-group select,
.form-group textarea {
  padding: 11px 14px;
  border: 1.5px solid #ddd;
  border-radius: 9px; font-size: 14px;
  font-family: inherit; outline: none;
  transition: border-color .2s, box-shadow .2s;
  color: #1a1a2e; background: #fafafa;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  border-color: #6a0dad;
  box-shadow: 0 0 0 3px rgba(106,13,173,.09);
  background: #fff;
}
.form-group textarea { resize: vertical; min-height: 130px; line-height: 1.6; }
.form-group select { cursor: pointer; }

.contact-rgpd {
  font-size: 12px; color: #888; line-height: 1.5;
  margin-bottom: 20px; display: flex; align-items: flex-start; gap: 8px;
}
.contact-rgpd input[type="checkbox"] { margin-top: 2px; accent-color: #6a0dad; flex-shrink: 0; }

.btn-contact {
  display: inline-flex; align-items: center; gap: 8px;
  width: 100%;
  justify-content: center;
  padding: 13px 24px;
  background: #6a0dad; color: #fff;
  border: none; border-radius: 10px;
  font-size: 15px; font-weight: 600;
  cursor: pointer;
  transition: background .2s, transform .15s;
}
.btn-contact:hover { background: #5a0b99; }
.btn-contact:active { transform: scale(.98); }

.alert-success {
  background: #e6f9ee; color: #1a7a3f;
  padding: 14px 18px; border-radius: 10px;
  font-size: 14px; margin-bottom: 20px;
  display: flex; gap: 10px; align-items: flex-start; line-height: 1.5;
}
.alert-error {
  background: #ffe0e0; color: #c00;
  padding: 14px 18px; border-radius: 10px;
  font-size: 14px; margin-bottom: 20px;
  display: flex; gap: 10px; align-items: flex-start; line-height: 1.5;
}

/* ── SIDEBAR INFOS ── */
.contact-sidebar { display: flex; flex-direction: column; gap: 18px; }

.contact-info-card {
  background: #fff;
  border: 1px solid #e0e0e8;
  border-radius: 14px;
  padding: 24px 26px;
  box-shadow: 0 2px 10px rgba(0,0,0,.05);
}
.contact-info-card h3 {
  font-size: 14px; font-weight: 700; color: #1a1a2e;
  margin-bottom: 16px; padding-bottom: 12px;
  border-bottom: 1px solid #f0e6fa;
}

.contact-info-item {
  display: flex; gap: 14px; align-items: flex-start;
  margin-bottom: 16px;
}
.contact-info-item:last-child { margin-bottom: 0; }
.contact-info-icon {
  width: 38px; height: 38px; border-radius: 10px;
  background: #f0e6fa; color: #6a0dad;
  display: flex; align-items: center; justify-content: center;
  font-size: 18px; flex-shrink: 0;
}
.contact-info-text strong { display: block; font-size: 13px; color: #1a1a2e; margin-bottom: 2px; }
.contact-info-text span  { font-size: 13px; color: #666; line-height: 1.5; }
.contact-info-text a     { color: #6a0dad; text-decoration: none; }
.contact-info-text a:hover { text-decoration: underline; }

/* Horaires */
.contact-hours { display: flex; flex-direction: column; gap: 6px; }
.contact-hours-row {
  display: flex; justify-content: space-between;
  font-size: 13px; color: #555;
}
.contact-hours-row .day { color: #1a1a2e; font-weight: 500; }
.contact-hours-row .time { color: #6a0dad; font-weight: 600; }
.contact-hours-row .closed { color: #aaa; }

/* Badge temps de réponse */
.response-badge {
  display: inline-flex; align-items: center; gap: 6px;
  background: #e6f9ee; color: #1a7a3f;
  font-size: 12px; font-weight: 700;
  padding: 5px 12px; border-radius: 20px;
  margin-top: 12px;
}

/* Lien FAQ */
.contact-faq-link {
  display: flex; align-items: center; justify-content: space-between;
  padding: 16px 20px;
  background: linear-gradient(135deg, #f0e6fa 0%, #e8d5f5 100%);
  border: 1px solid #d8c5ef;
  border-radius: 12px;
  text-decoration: none;
  transition: box-shadow .2s;
}
.contact-faq-link:hover { box-shadow: 0 4px 16px rgba(106,13,173,.15); }
.contact-faq-link__text strong { display: block; font-size: 13px; color: #1a1a2e; }
.contact-faq-link__text span   { font-size: 12px; color: #888; }
.contact-faq-link__arrow { font-size: 20px; color: #6a0dad; }

/* Compteur de caractères */
.char-count {
  font-size: 11px; color: #aaa; text-align: right;
  margin-top: 4px;
}

@media (max-width: 860px) {
  .contact-content { grid-template-columns: 1fr; }
  .contact-sidebar { order: -1; display: grid; grid-template-columns: 1fr 1fr; }
  .contact-faq-link { grid-column: 1 / -1; }
}
@media (max-width: 560px) {
  .form-row { grid-template-columns: 1fr; }
  .contact-form-card { padding: 24px 20px; }
  .contact-sidebar { grid-template-columns: 1fr; }
}
</style>

<!-- HERO -->
<div class="contact-hero">
  <p class="contact-hero__eyebrow">NOUS ÉCRIRE</p>
  <h1 class="contact-hero__title">Contactez-nous</h1>
  <p class="contact-hero__sub">Une question, un lien à signaler ou une suggestion ? Nous vous répondons sous 48 heures.</p>
</div>

<!-- CONTENU -->
<div class="contact-content">

  <!-- Formulaire -->
  <div class="contact-form-card">
    <h2>Envoyer un message</h2>
    <p class="subtitle">Tous les champs marqués d'un <span style="color:#6a0dad">*</span> sont obligatoires.</p>

    <?php if ($msg_succes): ?>
      <div class="alert-success">✅ <?= htmlspecialchars($msg_succes) ?></div>
    <?php endif; ?>
    <?php if ($msg_erreur): ?>
      <div class="alert-error">⚠️ <?= htmlspecialchars($msg_erreur) ?></div>
    <?php endif; ?>

    <?php if (!$form_sent): ?>
    <form method="POST" action="" id="contactForm" novalidate>
      <?= csrf_field() ?>

      <div class="form-row">
        <div class="form-group">
          <label for="prenom">Prénom <span class="required">*</span></label>
          <input type="text" id="prenom" name="prenom"
                 value="<?= htmlspecialchars($_POST['prenom'] ?? $user_prenom) ?>"
                 placeholder="Votre prénom" autocomplete="given-name" required>
        </div>
        <div class="form-group">
          <label for="nom">Nom <span class="required">*</span></label>
          <input type="text" id="nom" name="nom"
                 value="<?= htmlspecialchars($_POST['nom'] ?? $user_nom) ?>"
                 placeholder="Votre nom" autocomplete="family-name" required>
        </div>
      </div>

      <div class="form-group">
        <label for="email">Adresse email <span class="required">*</span></label>
        <input type="email" id="email" name="email"
               value="<?= htmlspecialchars($_POST['email'] ?? $user_email) ?>"
               placeholder="exemple@email.fr" autocomplete="email" required>
      </div>

      <div class="form-group">
        <label for="sujet">Sujet <span class="required">*</span></label>
        <select id="sujet" name="sujet" required>
          <option value="" disabled <?= empty($_POST['sujet']) ? 'selected' : '' ?>>Choisir un sujet…</option>
          <option value="Question générale"   <?= (($_POST['sujet'] ?? '') === 'Question générale')   ? 'selected' : '' ?>>Question générale</option>
          <option value="Lien cassé à signaler" <?= (($_POST['sujet'] ?? '') === 'Lien cassé à signaler') ? 'selected' : '' ?>>Lien cassé / incorrect à signaler</option>
          <option value="Problème de compte"  <?= (($_POST['sujet'] ?? '') === 'Problème de compte')  ? 'selected' : '' ?>>Problème de compte</option>
          <option value="Suggestion d'amélioration" <?= (($_POST['sujet'] ?? '') === 'Suggestion d\'amélioration') ? 'selected' : '' ?>>Suggestion d'amélioration</option>
          <option value="Données personnelles" <?= (($_POST['sujet'] ?? '') === 'Données personnelles') ? 'selected' : '' ?>>Données personnelles (RGPD)</option>
          <option value="Autre"               <?= (($_POST['sujet'] ?? '') === 'Autre')               ? 'selected' : '' ?>>Autre</option>
        </select>
      </div>

      <div class="form-group">
        <label for="message">Message <span class="required">*</span></label>
        <textarea id="message" name="message" rows="5"
                  placeholder="Décrivez votre demande en détail (minimum 20 caractères)…"
                  maxlength="2000" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
        <span class="char-count" id="charCount">0 / 2000</span>
      </div>

      <div class="contact-rgpd">
        <input type="checkbox" id="rgpd" name="rgpd" required>
        <label for="rgpd">
          J'accepte que mes données soient utilisées pour traiter ma demande, conformément à la
          <a href="confidentialite.php" style="color:#6a0dad">Politique de confidentialité</a>.
        </label>
      </div>

      <button type="submit" class="btn-contact">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
        </svg>
        Envoyer le message
      </button>
    </form>
    <?php else: ?>
      <div style="text-align:center; padding: 30px 0;">
        <div style="font-size:48px; margin-bottom:16px;">🎉</div>
        <h3 style="font-size:18px; color:#1a1a2e; margin-bottom:8px;">Message envoyé !</h3>
        <p style="font-size:14px; color:#666;">Nous vous répondrons dans les meilleurs délais à l'adresse <strong><?= htmlspecialchars($_POST['email'] ?? '') ?></strong>.</p>
        <a href="index.php" style="display:inline-block; margin-top:24px; padding:11px 24px; background:#6a0dad; color:#fff; border-radius:8px; font-weight:600; font-size:14px; text-decoration:none;">Retour à l'accueil</a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Sidebar -->
  <aside class="contact-sidebar">

    <!-- Coordonnées -->
    <div class="contact-info-card">
      <h3>📋 Nos coordonnées</h3>

      <div class="contact-info-item">
        <div class="contact-info-icon">📞</div>
        <div class="contact-info-text">
          <strong>Téléphone</strong>
          <span><a href="tel:0602469314">06 02 46 93 14</a></span>
        </div>
      </div>

      <div class="contact-info-item">
        <div class="contact-info-icon">✉️</div>
        <div class="contact-info-text">
          <strong>Email</strong>
          <span><a href="mailto:contact@liens-demarches.fr">contact@liens-demarches.fr</a></span>
        </div>
      </div>

      <div class="contact-info-item">
        <div class="contact-info-icon">⏱️</div>
        <div class="contact-info-text">
          <strong>Délai de réponse</strong>
          <span>Sous 48h ouvrées</span>
        </div>
      </div>

      <div class="response-badge">✓ Réponse garantie sous 48h</div>
    </div>

    <!-- Horaires -->
    <div class="contact-info-card">
      <h3>🕐 Disponibilité téléphonique</h3>
      <div class="contact-hours">
        <div class="contact-hours-row">
          <span class="day">Lundi – Vendredi</span>
          <span class="time">9h – 18h</span>
        </div>
        <div class="contact-hours-row">
          <span class="day">Samedi</span>
          <span class="time">10h – 13h</span>
        </div>
        <div class="contact-hours-row">
          <span class="day">Dimanche</span>
          <span class="closed">Fermé</span>
        </div>
      </div>
      <p style="font-size:12px; color:#aaa; margin-top:14px; line-height:1.5;">
        En dehors de ces horaires, utilisez le formulaire et nous vous contacterons dès notre prochaine disponibilité.
      </p>
    </div>

    <!-- Lien FAQ -->
    <a href="faq.php" class="contact-faq-link">
      <div class="contact-faq-link__text">
        <strong>Consulter la FAQ</strong>
        <span>Peut-être que votre réponse s'y trouve déjà !</span>
      </div>
      <span class="contact-faq-link__arrow">→</span>
    </a>

  </aside>
</div>

<script>
(function () {
  const textarea  = document.getElementById('message');
  const charCount = document.getElementById('charCount');

  if (textarea && charCount) {
    function update() {
      const len = textarea.value.length;
      charCount.textContent = len + ' / 2000';
      charCount.style.color = len > 1900 ? '#e07b00' : '#aaa';
    }
    textarea.addEventListener('input', update);
    update();
  }

  // Validation côté client légère
  const form = document.getElementById('contactForm');
  if (form) {
    form.addEventListener('submit', function (e) {
      const rgpd = document.getElementById('rgpd');
      if (rgpd && !rgpd.checked) {
        e.preventDefault();
        alert('Vous devez accepter la politique de confidentialité pour envoyer votre message.');
      }
    });
  }
})();
</script>

<?php include 'footer.php'; ?>
