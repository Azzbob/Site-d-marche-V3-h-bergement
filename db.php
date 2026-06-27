<?php
// ============================================================
//  db.php  —  Connexion PDO à la base liens_demarches
//  Configuré pour l'hébergement InfinityFree
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'liens_demarches');
define('DB_USER', 'root');        // ← modifiez selon votre config
define('DB_PASS', '');            // ← modifiez selon votre config
define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = 'mysql:host=' . DB_HOST
         . ';port=3306'
         . ';dbname=' . DB_NAME
         . ';charset=' . DB_CHARSET;

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}