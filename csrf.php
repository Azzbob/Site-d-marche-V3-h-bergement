<?php
// ============================================================
//  csrf.php  —  Génération et vérification de token CSRF
//  À inclure après session_start() sur toute page avec un <form>
// ============================================================

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Affiche un champ caché <input> prêt à insérer dans un <form>.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

/**
 * Vérifie le token reçu en POST. Retourne true/false.
 * Ne détruit pas le token : il reste valable pour tout la durée de la session
 * (suffisant ici, pas besoin d'un token à usage unique pour ce type de site).
 */
function csrf_verify(?string $tokenRecu): bool
{
    if (empty($_SESSION['csrf_token']) || empty($tokenRecu)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $tokenRecu);
}
