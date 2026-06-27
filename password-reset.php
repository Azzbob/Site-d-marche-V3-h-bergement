<?php
// ============================================================
//  password-reset.php  —  Réinitialisation du mot de passe
//  À inclure après db.php (et après session_start() si besoin)
//
//  Nécessite la table `password_resets` — voir password_resets.sql
// ============================================================

const RESET_TOKEN_VALID_MINUTES = 60; // durée de validité du lien (en minutes)

/**
 * Construit l'URL de base du site (ex: http://localhost/site-liens-d-marche-v2-main)
 * à partir de la requête courante, pour générer des liens absolus dans les emails.
 */
function app_base_url(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir    = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    return $scheme . '://' . $host . $dir;
}

/**
 * Crée un token de réinitialisation pour le compte correspondant à cet
 * email (si un compte existe) et l'enregistre haché en base.
 *
 * Retourne ['token' => 'id:token_en_clair', 'user' => [...]] si un compte
 * a été trouvé, ou null sinon. Seul le token EN CLAIR (jamais le hash)
 * doit être inséré dans le lien envoyé par email.
 */
function password_reset_create_token(PDO $pdo, string $email): ?array
{
    $stmt = $pdo->prepare('SELECT id, nom, prenom, email FROM utilisateurs WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        return null;
    }

    // On invalide les éventuelles demandes précédentes non utilisées
    $pdo->prepare('DELETE FROM password_resets WHERE user_id = ?')->execute([$user['id']]);

    $tokenPlain = bin2hex(random_bytes(32));
    $tokenHash  = password_hash($tokenPlain, PASSWORD_BCRYPT);
    $expiresAt  = date('Y-m-d H:i:s', time() + RESET_TOKEN_VALID_MINUTES * 60);

    $pdo->prepare(
        'INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, ?)'
    )->execute([$user['id'], $tokenHash, $expiresAt]);

    $tokenId = $pdo->lastInsertId();

    return [
        'token' => $tokenId . ':' . $tokenPlain,
        'user'  => $user,
    ];
}

/**
 * Vérifie un token reçu (format "id:token_en_clair").
 * Retourne la ligne password_resets + infos utilisateur si le token
 * est valide et non expiré, sinon false.
 */
function password_reset_verify_token(PDO $pdo, string $tokenRecu)
{
    $parts = explode(':', $tokenRecu, 2);
    if (count($parts) !== 2 || !ctype_digit($parts[0]) || $parts[1] === '') {
        return false;
    }
    [$tokenId, $tokenPlain] = $parts;

    $stmt = $pdo->prepare(
        'SELECT pr.id, pr.user_id, pr.token_hash, pr.expires_at,
                u.nom, u.prenom, u.email
         FROM password_resets pr
         JOIN utilisateurs u ON u.id = pr.user_id
         WHERE pr.id = ?'
    );
    $stmt->execute([(int) $tokenId]);
    $row = $stmt->fetch();

    if (!$row) {
        return false;
    }

    // Token expiré : on le supprime et on refuse
    if (strtotime($row['expires_at']) < time()) {
        $pdo->prepare('DELETE FROM password_resets WHERE id = ?')->execute([$row['id']]);
        return false;
    }

    if (!password_verify($tokenPlain, $row['token_hash'])) {
        return false;
    }

    return $row;
}

/**
 * À appeler une fois le mot de passe effectivement changé :
 * supprime tous les tokens de réinitialisation de cet utilisateur
 * (sécurité : un lien ne doit servir qu'une seule fois).
 */
function password_reset_consume(PDO $pdo, int $userId): void
{
    $pdo->prepare('DELETE FROM password_resets WHERE user_id = ?')->execute([$userId]);
}

/**
 * Envoie l'email contenant le lien de réinitialisation.
 *
 * Utilise la fonction mail() native de PHP, qui nécessite un serveur
 * mail local correctement configuré (sendmail/postfix sur un vrai
 * hébergeur, ou un outil comme Mailpit/Mailtrap en local pour tester).
 * Si mail() ne fonctionne pas chez vous, remplacez le corps de cette
 * fonction par un envoi via PHPMailer + SMTP (Gmail, Brevo, etc.) —
 * la signature de la fonction (mêmes paramètres, retourne bool) reste
 * identique, donc rien d'autre n'a besoin de changer.
 */
function password_reset_send_email(string $email, string $prenom, string $resetLink): bool
{
    $sujet = 'Réinitialisation de votre mot de passe – Liens Démarches';

    $corpsHtml = '
    <div style="font-family:Segoe UI,Arial,sans-serif;background:#f4f4f8;padding:32px">
      <div style="max-width:480px;margin:0 auto;background:#ffffff;border-radius:12px;
                  padding:36px;border:1px solid #e0e0e8">
        <p style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                  color:#6a0dad;margin:0 0 10px">Liens Démarches</p>
        <h2 style="color:#1a1a2e;margin:0 0 14px;font-size:20px">Réinitialisation de mot de passe</h2>
        <p style="color:#666677;font-size:14px;line-height:1.7;margin:0 0 22px">
          Bonjour ' . htmlspecialchars($prenom) . ',<br><br>
          Vous avez demandé à réinitialiser votre mot de passe sur Liens Démarches.
          Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.
          Ce lien est valable ' . RESET_TOKEN_VALID_MINUTES . ' minutes.
        </p>
        <p style="text-align:center;margin:0 0 24px">
          <a href="' . htmlspecialchars($resetLink) . '"
             style="background:#6a0dad;color:#ffffff;padding:13px 28px;border-radius:8px;
                    text-decoration:none;font-weight:600;font-size:14px;display:inline-block">
            Réinitialiser mon mot de passe →
          </a>
        </p>
        <p style="color:#999999;font-size:12px;line-height:1.6;margin:0">
          Si vous n\'êtes pas à l\'origine de cette demande, ignorez simplement cet email :
          votre mot de passe ne sera pas modifié.
        </p>
      </div>
    </div>';

    require_once __DIR__ . '/smtp-config.php';
    require_once __DIR__ . '/smtp-mailer.php';

    $envoye = smtp_send_mail($email, $sujet, $corpsHtml);

    if (!$envoye) {
        // Log l'erreur SMTP détaillée pour le débogage (jamais affichée à l'utilisateur)
        error_log('Erreur envoi email reset password: ' . ($GLOBALS['smtp_last_error'] ?? 'inconnue'));
    }

    return $envoye;
}
