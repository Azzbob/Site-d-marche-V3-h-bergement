<?php
// ============================================================
//  newsletter.php — Endpoint AJAX pour l'inscription newsletter
// ============================================================
session_start();
require_once 'smtp-config.php';
require_once 'smtp-mailer.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'msg' => 'Méthode non autorisée.']);
    exit;
}

$email = trim($_POST['email'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'msg' => 'Adresse email invalide.']);
    exit;
}

// Email de bienvenue HTML
$sujet = 'Bienvenue dans la newsletter Liens Démarches !';
$corps  = '<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f4f4f8;font-family:Segoe UI,system-ui,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f8;padding:40px 0;">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(106,13,173,.10);">

        <!-- HEADER -->
        <tr>
          <td style="background:linear-gradient(135deg,#3b006e 0%,#6a0dad 55%,#9b30ff 100%);padding:40px 40px 36px;text-align:center;">
            <div style="width:64px;height:64px;background:rgba(255,255,255,.15);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:18px;">
              <span style="font-size:28px;color:#f5c842;">&#9733;</span>
            </div>
            <h1 style="margin:0;color:#ffffff;font-size:26px;font-weight:800;letter-spacing:.02em;">Liens D&eacute;marches</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,.75);font-size:14px;">Bienvenue dans notre newsletter !</p>
          </td>
        </tr>

        <!-- CORPS -->
        <tr>
          <td style="padding:40px 40px 32px;">
            <h2 style="margin:0 0 16px;font-size:20px;font-weight:700;color:#1a1a2e;">Merci pour votre inscription !</h2>
            <p style="margin:0 0 14px;font-size:15px;color:#444;line-height:1.7;">
              Vous &ecirc;tes maintenant abonn&eacute; &agrave; la newsletter <strong>Liens D&eacute;marches</strong>.
              Vous recevrez en priorit&eacute; toutes les nouveaut&eacute;s : nouveaux liens, mises &agrave; jour des services et conseils pratiques pour simplifier vos d&eacute;marches administratives.
            </p>
            <p style="margin:0 0 28px;font-size:15px;color:#444;line-height:1.7;">
              En attendant, n&rsquo;h&eacute;sitez pas &agrave; parcourir nos cat&eacute;gories et &agrave; sauvegarder vos liens favoris !
            </p>
            <div style="text-align:center;">
              <a href="https://liensde.infinityfreeapp.com/index.php"
                 style="display:inline-block;background:linear-gradient(135deg,#6a0dad,#9b30ff);color:#ffffff;padding:14px 32px;border-radius:10px;font-size:15px;font-weight:700;text-decoration:none;letter-spacing:.02em;">
                Acc&eacute;der au site &rarr;
              </a>
            </div>
          </td>
        </tr>

        <!-- SÉPARATEUR -->
        <tr><td style="padding:0 40px;"><hr style="border:none;border-top:1px solid #f0e6fa;"></td></tr>

        <!-- FOOTER EMAIL -->
        <tr>
          <td style="padding:24px 40px 32px;text-align:center;">
            <p style="margin:0 0 6px;font-size:12px;color:#999;">
              Vous recevez cet email car vous vous &ecirc;tes inscrit &agrave; la newsletter de Liens D&eacute;marches.
            </p>
            <p style="margin:0;font-size:12px;color:#bbb;">
              &copy; ' . date('Y') . ' Liens D&eacute;marches &mdash; Tous droits r&eacute;serv&eacute;s
            </p>
          </td>
        </tr>

      </table>
    </td></tr>
  </table>
</body>
</html>';

$ok = smtp_send_mail($email, $sujet, $corps);

if ($ok) {
    echo json_encode(['ok' => true, 'msg' => 'Merci ! Un email de bienvenue vient de vous être envoyé.']);
} else {
    // Log l'erreur mais ne bloque pas l'utilisateur
    error_log('Newsletter SMTP error: ' . ($GLOBALS['smtp_last_error'] ?? 'unknown'));
    echo json_encode(['ok' => true, 'msg' => 'Inscription enregistrée ! Merci pour votre intérêt.']);
}
