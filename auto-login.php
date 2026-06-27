<?php
// ============================================================
//  auto-login.php  —  Reconnexion automatique via "Se souvenir de moi"
//  À inclure APRÈS session_start() et require_once 'db.php',
//  AVANT tout affichage, sur les pages qui en ont besoin
//  (ou directement dans header.php pour que ça s'applique partout).
// ============================================================

// Si l'utilisateur est déjà connecté en session, rien à faire
if (!empty($_SESSION['user_id'])) {
    return;
}

// Pas de cookie "remember_token" → rien à faire
if (empty($_COOKIE['remember_token'])) {
    return;
}

$cookieValue = $_COOKIE['remember_token'];

// Le cookie est stocké sous la forme "id_du_token:token_en_clair"
// pour pouvoir retrouver la ligne en base sans devoir tout parcourir
// et comparer un hash à chaque token existant.
$parts = explode(':', $cookieValue, 2);

if (count($parts) === 2) {
    [$tokenId, $tokenPlain] = $parts;

    if (ctype_digit($tokenId) && $tokenPlain !== '') {
        $stmt = $pdo->prepare(
            'SELECT rt.id, rt.user_id, rt.token_hash, rt.expires_at,
                    u.nom, u.prenom, u.email
             FROM remember_tokens rt
             JOIN utilisateurs u ON u.id = rt.user_id
             WHERE rt.id = ?'
        );
        $stmt->execute([(int) $tokenId]);
        $row = $stmt->fetch();

        if ($row && strtotime($row['expires_at']) > time()
            && password_verify($tokenPlain, $row['token_hash'])
        ) {
            // Token valide → on reconnecte l'utilisateur
            $_SESSION['user_id']     = $row['user_id'];
            $_SESSION['user_nom']    = $row['nom'];
            $_SESSION['user_prenom'] = $row['prenom'];
            $_SESSION['user_email']  = $row['email'];

            // Sécurité : on régénère l'ID de session après une élévation de privilège
            session_regenerate_id(true);

            // Rotation du token : on supprime l'ancien et on en émet un nouveau
            // (limite la fenêtre d'exploitation si le cookie a été intercepté)
            $deleteStmt = $pdo->prepare('DELETE FROM remember_tokens WHERE id = ?');
            $deleteStmt->execute([$row['id']]);

            remember_me_issue_token($pdo, $row['user_id']);
        } else {
            // Token invalide, expiré, ou ne correspond pas → on nettoie tout
            if ($row) {
                $deleteStmt = $pdo->prepare('DELETE FROM remember_tokens WHERE id = ?');
                $deleteStmt->execute([$row['id']]);
            }
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
    }
}

/**
 * Crée un nouveau token "Se souvenir de moi" pour un utilisateur,
 * l'enregistre (haché) en base, et pose le cookie correspondant.
 *
 * Cette fonction est définie ici pour être réutilisable depuis
 * connexion.php sans dupliquer la logique.
 */
function remember_me_issue_token(PDO $pdo, int $userId, int $durationDays = 30): void
{
    $tokenPlain = bin2hex(random_bytes(32));
    $tokenHash  = password_hash($tokenPlain, PASSWORD_BCRYPT);
    $expiresAt  = date('Y-m-d H:i:s', time() + $durationDays * 86400);

    $stmt = $pdo->prepare(
        'INSERT INTO remember_tokens (user_id, token_hash, expires_at)
         VALUES (?, ?, ?)'
    );
    $stmt->execute([$userId, $tokenHash, $expiresAt]);

    $tokenId = $pdo->lastInsertId();

    // Cookie = "id:token_en_clair" — seul le hash est stocké en base
    setcookie(
        'remember_token',
        $tokenId . ':' . $tokenPlain,
        time() + $durationDays * 86400,
        '/',
        '',
        !empty($_SERVER['HTTPS']), // secure = true si HTTPS détecté
        true                       // httponly = toujours true
    );
}
