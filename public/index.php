<?php

/**
 * Front Controller
 * Point d'entrée unique de l'application
 */

// Afficher les erreurs en développement
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Définir le chemin racine
define('ROOT_PATH', dirname(__DIR__));

// Charger l'autoloader Composer
require ROOT_PATH . '/vendor/autoload.php';

use App\Core\Application;

// Démarrer l'application
$app = Application::getInstance();

// Charger les routes
require ROOT_PATH . '/config/routes.php';

// Lancer l'application
$app->run();
