<?php

/**
 * Router pour le serveur PHP intégré
 * Utilisé avec: php -S localhost:8000 router.php
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Si c'est un fichier réel (CSS, JS, images), le servir directement
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Sinon, router vers index.php
require_once __DIR__ . '/index.php';
