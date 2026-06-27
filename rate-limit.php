<?php
// ============================================================
//  rate-limit.php  —  Limitation des tentatives de connexion
//  À inclure dans connexion.php (après db.php)
// ============================================================

const LOGIN_MAX_ATTEMPTS   = 5;   // tentatives autorisées
const LOGIN_WINDOW_MINUTES = 15;  // par fenêtre de temps
const LOGIN_LOCK_MINUTES   = 15;  // durée du blocage une fois la limite atteinte

function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Retourne le nombre de secondes avant de pouvoir réessayer,
 * ou 0 si l'utilisateur n'est pas bloqué.
 */
function login_seconds_until_unlock(PDO $pdo, string $email): int
{
    $ip = client_ip();

    // On bloque sur la combinaison email + IP : ça évite qu'un attaquant
    // bloque le compte d'un tiers juste en spammant son email depuis ailleurs,
    // tout en limitant bien le bruteforce depuis une même IP.
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) AS nb, MAX(attempted_at) AS dernier
         FROM login_attempts
         WHERE email = ? AND ip_address = ?
           AND attempted_at > (NOW() - INTERVAL ' . LOGIN_WINDOW_MINUTES . ' MINUTE)'
    );
    $stmt->execute([$email, $ip]);
    $row = $stmt->fetch();

    if ((int) $row['nb'] >= LOGIN_MAX_ATTEMPTS) {
        $lockUntil = strtotime($row['dernier']) + LOGIN_LOCK_MINUTES * 60;
        $remaining = $lockUntil - time();
        return max(0, $remaining);
    }

    return 0;
}

function login_record_failed_attempt(PDO $pdo, string $email): void
{
    $stmt = $pdo->prepare(
        'INSERT INTO login_attempts (email, ip_address) VALUES (?, ?)'
    );
    $stmt->execute([$email, client_ip()]);
}

function login_clear_attempts(PDO $pdo, string $email): void
{
    $stmt = $pdo->prepare(
        'DELETE FROM login_attempts WHERE email = ? AND ip_address = ?'
    );
    $stmt->execute([$email, client_ip()]);
}
