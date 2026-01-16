<?php

/**
 * Configuration générale de l'application
 */

return [
    'name' => $_ENV['APP_NAME'] ?? 'Football Shop',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'] ?? 'http://localhost:8000',

    'session' => [
        'lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 120),
        'name' => 'football_shop_session',
    ],
];
