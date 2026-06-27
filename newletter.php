<?php
// ============================================================
//  newsletter.php — Inscription newsletter + email de bienvenue
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

// Test si fsockopen est disponible (souvent bloqué sur InfinityFree)
$socketTest = @fsockopen('smtp.gmail.com', 587, $errno, $errstr, 5);
if (!$socketTest) {
    // fsockopen bloqué — on tente avec mail() en fallback
    $headers  = "From: Liens Démarches <azebob95@gmail.com>\r\n";
    $headers .= "Reply-To: azebob95@gmail.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $corps = getEmailBody();

    $sent = @mail($email, '=?UTF-8?B?' . base64_encode('Bienvenue dans la newsletter Liens Démarches !') . '?=', $corps, $headers);

    if ($sent) {
        echo json_encode(['ok' => true, 'msg' => 'Merci ! Un email de bienvenue vient de vous être envoyé.']);
    } else {
        echo json_encode(['ok' => false, 'msg' => 'Erreur : envoi impossible depuis ce serveur. Contactez-nous directement.', 'debug' => "fsockopen bloqué + mail() échoué"]);
    }
    exit;
}
fclose($socketTest);

// fsockopen OK — on utilise SMTP
$ok = smtp_send_mail($email, 'Bienvenue dans la newsletter Liens Démarches !', getEmailBody());

if ($ok) {
    echo json_encode(['ok' => true, 'msg' => 'Merci ! Un email de bienvenue vient de vous être envoyé.']);
} else {
    $erreur = $GLOBALS['smtp_last_error'] ?? 'erreur inconnue';
    echo json_encode(['ok' => false, 'msg' => 'Erreur d\'envoi : ' . $erreur]);
}

function getEmailBody(): string {
    return '<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f4f8;font-family:Segoe UI,system-ui,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f8;padding:40px 0;">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;">

        <tr>
          <td style="background:linear-gradient(135deg,#3b006e 0%,#6a0dad 55%,#9b30ff 100%);padding:40px;text-align:center;">
            <h1 style="margin:0;color:#ffffff;font-size:26px;font-weight:800;">Liens Démarches</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,.75);font-size:14px;">Bienvenue dans notre newsletter !</p>
          </td>
        </tr>

        <tr>
          <td style="padding:40px;">
            <h2 style="margin:0 0 16px;font-size:20px;font-weight:700;color:#1a1a2e;">Merci pour votre inscription !</h2>
            <p style="margin:0 0 14px;font-size:15px;color:#444;line-height:1.7;">
              Vous êtes maintenant abonné à la newsletter <strong>Liens Démarches</strong>.
              Vous recevrez toutes les nouveautés : nouveaux liens, mises à jour des services et conseils pratiques.
            </p>
            <p style="margin:0 0 28px;font-size:15px;color:#444;line-height:1.7;">
              En attendant, parcourez nos catégories et sauvegardez vos liens favoris !
            </p>
            <div style="text-align:center;">
              <a href="https://liens-demarches.infinityfreeapp.com/index.php"
                 style="display:inline-block;background:#6a0dad;color:#ffffff;padding:14px 32px;border-radius:10px;font-size:15px;font-weight:700;text-decoration:none;">
                Accéder au site &rarr;
              </a>
            </div>
          </td>
        </tr>

        <tr>
          <td style="padding:24px 40px 32px;text-align:center;border-top:1px solid #f0e6fa;">
            <p style="margin:0;font-size:12px;color:#999;">
              &copy; ' . date('Y') . ' Liens Démarches &mdash; Tous droits réservés
            </p>
          </td>
        </tr>

      </table>
    </td></tr>
  </table>
</body>
</html>';
}
