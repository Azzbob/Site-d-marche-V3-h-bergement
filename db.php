<?php
// ============================================================
//  db.php  —  Connexion PDO à la base liens_demarches
//  Configuré pour l'hébergement InfinityFree
// ============================================================

define('DB_HOST', 'sql306.infinityfree.com');
define('DB_NAME', 'if0_42131220_liens_demarches');
define('DB_USER', 'if0_42131220');
define('DB_PASS', 'Xx9uV1TVnT');
define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = 'mysql:host=' . DB_HOST
         . ';dbname=' . DB_NAME
         . ';charset=' . DB_CHARSET;

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (PDOException $e) {
    // En production, loguez l'erreur plutôt que de l'afficher
    error_log($e->getMessage());
    die(json_encode(['error' => 'Connexion à la base de données impossible.']));
}
