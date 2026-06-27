<?php
// ============================================================
//  404.php — Page d'erreur personnalisée
// ============================================================
session_start();
require_once 'db.php';
require_once 'auto-login.php';

http_response_code(404);
$page_title = 'Page introuvable – Liens Démarches';

include 'header.php';
?>

<style>
.error-wrap {
  min-height: 60vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 80px 24px;
}
.error-code {
  font-size: clamp(80px, 15vw, 140px);
  font-weight: 900;
  line-height: 1;
  background: linear-gradient(135deg, #6a0dad, #9b30ff);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 12px;
  animation: globalFadeUp .5s ease both;
}
.error-title {
  font-size: clamp(20px, 3vw, 28px);
  font-weight: 700;
  color: #1a1a2e;
  margin-bottom: 12px;
  animation: globalFadeUp .5s ease .08s both;
}
.error-sub {
  font-size: 15px;
  color: #666;
  max-width: 440px;
  line-height: 1.7;
  margin-bottom: 36px;
  animation: globalFadeUp .5s ease .14s both;
}
.error-actions {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: center;
  animation: globalFadeUp .5s ease .20s both;
}
@keyframes globalFadeUp {
  from { opacity: 0; transform: translateY(14px); }
  to   { opacity: 1; transform: translateY(0); }
}
</style>

<div class="error-wrap">
  <div class="error-code">404</div>
  <h1 class="error-title">Page introuvable</h1>
  <p class="error-sub">
    La page que vous cherchez n'existe pas ou a été déplacée.<br>
    Pas de panique, tout le reste du site est là pour vous aider !
  </p>
  <div class="error-actions">
    <a href="index.php" class="btn btn--primary">← Retour à l'accueil</a>
    <a href="contact.php" class="btn btn--outline" style="color:#6a0dad;border-color:#6a0dad;">Nous contacter</a>
  </div>
</div>

<?php include 'footer.php'; ?>
