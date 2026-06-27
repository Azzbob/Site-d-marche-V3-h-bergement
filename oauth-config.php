<?php
// ============================================================
//  oauth-config.php  —  Clés API OAuth (Google, Facebook, Apple)
//  ⚠️  Ne jamais versionner ce fichier (ajoutez-le à .gitignore)
// ============================================================

// ── GOOGLE ──────────────────────────────────────────────────
define('GOOGLE_CLIENT_ID',     '542988859140-d1jjhguhr16b7q1sje2kb3337r3ko6u7.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI',  'https://liens-demarches.infinityfreeapp.com/google-callback.php');

// ── FACEBOOK ────────────────────────────────────────────────
define('FACEBOOK_APP_ID',      '1641623910282628');
define('FACEBOOK_APP_SECRET',  '895a72d4ab70e008c74fedbf35f6dc28');
define('FACEBOOK_REDIRECT_URI','https://liens-demarches.infinityfreeapp.com/facebook-callback.php');

// ── APPLE ───────────────────────────────────────────────────
// Apple Sign In nécessite un compte Apple Developer (99€/an)
// Laissez ces valeurs vides si vous ne l'utilisez pas encore
define('APPLE_CLIENT_ID',      '');   // ex: com.votredomaine.liens-demarches
define('APPLE_TEAM_ID',        '');   // ex: ABCDE12345
define('APPLE_KEY_ID',         '');   // ex: ABCDE12345
define('APPLE_PRIVATE_KEY',    '');   // Contenu du fichier .p8 (clé privée complète)
define('APPLE_REDIRECT_URI',   'https://liens-demarches.infinityfreeapp.com/apple-callback.php');
