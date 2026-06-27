<?php
// ============================================================
//  smtp-mailer.php  —  Client SMTP minimaliste en PHP pur
//  (sans Composer ni PHPMailer), pour environnements
//  où la fonction mail() est désactivée (ex: InfinityFree).
//
//  Utilisation :
//    require_once 'smtp-config.php';
//    require_once 'smtp-mailer.php';
//    smtp_send_mail('destinataire@exemple.fr', 'Sujet', '<p>Corps HTML</p>');
// ============================================================

/**
 * Envoie un email via une connexion SMTP authentifiée en TLS.
 * Retourne true si le serveur SMTP a accepté le message, false sinon.
 * En cas d'échec, le détail est ajouté dans $GLOBALS['smtp_last_error'].
 */
function smtp_send_mail(string $to, string $subject, string $htmlBody): bool
{
    $GLOBALS['smtp_last_error'] = '';

    $host = SMTP_HOST;
    $port = SMTP_PORT;

    $errno = 0;
    $errstr = '';

    // Connexion initiale en clair (le chiffrement TLS est activé après STARTTLS)
    $socket = @fsockopen($host, $port, $errno, $errstr, 15);
    if (!$socket) {
        $GLOBALS['smtp_last_error'] = "Connexion impossible à $host:$port ($errstr)";
        return false;
    }

    stream_set_timeout($socket, 15);

    $read = function () use ($socket) {
        $data = '';
        while ($line = fgets($socket, 515)) {
            $data .= $line;
            if (substr($line, 3, 1) === ' ') break; // fin de réponse multi-lignes
        }
        return $data;
    };

    $write = function (string $cmd) use ($socket) {
        fwrite($socket, $cmd . "\r\n");
    };

    $expect = function (string $response, string $code) {
        return substr($response, 0, 3) === $code;
    };

    // ── Poignée de main SMTP ──
    $resp = $read();
    if (!$expect($resp, '220')) { $GLOBALS['smtp_last_error'] = "Pas de bienvenue serveur: $resp"; fclose($socket); return false; }

    $write('EHLO ' . (parse_url(SMTP_FROM_EMAIL, PHP_URL_HOST) ?: 'localhost'));
    $resp = $read();
    if (!$expect($resp, '250')) { $GLOBALS['smtp_last_error'] = "EHLO refusé: $resp"; fclose($socket); return false; }

    $write('STARTTLS');
    $resp = $read();
    if (!$expect($resp, '220')) { $GLOBALS['smtp_last_error'] = "STARTTLS refusé: $resp"; fclose($socket); return false; }

    if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        $GLOBALS['smtp_last_error'] = "Échec de la négociation TLS";
        fclose($socket);
        return false;
    }

    // Second EHLO obligatoire après le TLS
    $write('EHLO ' . (parse_url(SMTP_FROM_EMAIL, PHP_URL_HOST) ?: 'localhost'));
    $resp = $read();
    if (!$expect($resp, '250')) { $GLOBALS['smtp_last_error'] = "EHLO (TLS) refusé: $resp"; fclose($socket); return false; }

    // ── Authentification ──
    $write('AUTH LOGIN');
    $resp = $read();
    if (!$expect($resp, '334')) { $GLOBALS['smtp_last_error'] = "AUTH LOGIN refusé: $resp"; fclose($socket); return false; }

    $write(base64_encode(SMTP_USERNAME));
    $resp = $read();
    if (!$expect($resp, '334')) { $GLOBALS['smtp_last_error'] = "Username refusé: $resp"; fclose($socket); return false; }

    $write(base64_encode(SMTP_PASSWORD));
    $resp = $read();
    if (!$expect($resp, '235')) { $GLOBALS['smtp_last_error'] = "Authentification refusée: $resp"; fclose($socket); return false; }

    // ── Enveloppe ──
    $write('MAIL FROM:<' . SMTP_FROM_EMAIL . '>');
    $resp = $read();
    if (!$expect($resp, '250')) { $GLOBALS['smtp_last_error'] = "MAIL FROM refusé: $resp"; fclose($socket); return false; }

    $write('RCPT TO:<' . $to . '>');
    $resp = $read();
    if (!$expect($resp, '250') && !$expect($resp, '251')) { $GLOBALS['smtp_last_error'] = "RCPT TO refusé: $resp"; fclose($socket); return false; }

    $write('DATA');
    $resp = $read();
    if (!$expect($resp, '354')) { $GLOBALS['smtp_last_error'] = "DATA refusé: $resp"; fclose($socket); return false; }

    // ── Construction du message ──
    $boundary = uniqid('ld_');
    $headers  = [];
    $headers[] = 'From: ' . SMTP_FROM_NAME . ' <' . SMTP_FROM_EMAIL . '>';
    $headers[] = 'To: <' . $to . '>';
    $headers[] = 'Subject: ' . mb_encode_mimeheader($subject, 'UTF-8');
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'Date: ' . date('r');

    // Echappe les lignes commençant par un point (règle SMTP)
    $body = preg_replace('/^\./m', '..', $htmlBody);

    $message = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.";
    $write($message);
    $resp = $read();
    if (!$expect($resp, '250')) { $GLOBALS['smtp_last_error'] = "Envoi refusé: $resp"; fclose($socket); return false; }

    $write('QUIT');
    fclose($socket);

    return true;
}
