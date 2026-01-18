<?php

namespace App\Core;

/**
 * Classe principale de l'application
 * Point d'entrée et conteneur de services
 */
class Application
{
    private static ?Application $instance = null;
    private Router $router;
    private array $config = [];

    private function __construct()
    {
        $this->loadEnvironment();
        $this->loadConfig();
        $this->initSession();
        $this->router = new Router();
    }

    /**
     * Singleton - retourne l'instance unique de l'application
     */
    public static function getInstance(): Application
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Charge les variables d'environnement
     */
    private function loadEnvironment(): void
    {
        $envFile = dirname(__DIR__, 2) . '/.env';
        if (file_exists($envFile)) {
            $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
            $dotenv->load();
        }
    }

    /**
     * Charge les fichiers de configuration
     */
    private function loadConfig(): void
    {
        $configPath = dirname(__DIR__, 2) . '/config';
        foreach (glob($configPath . '/*.php') as $file) {
            $name = basename($file, '.php');
            $this->config[$name] = require $file;
        }
    }

    /**
     * Initialise la session PHP
     */
    private function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name($this->config('app.session.name', 'app_session'));
            session_start();
        }
    }

    /**
     * Récupère une valeur de configuration
     * Supporte la notation pointée: config('database.host')
     */
    public function config(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Retourne le routeur
     */
    public function router(): Router
    {
        return $this->router;
    }

    /**
     * Lance l'application
     */
    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        try {
            $this->router->dispatch($method, $uri);
        } catch (\Throwable $e) {
            $this->handleException($e);
        }
    }

    /**
     * Gère les exceptions non capturées
     */
    private function handleException(\Throwable $e): void
    {
        if ($this->config('app.debug', false)) {
            http_response_code(500);
            echo '<h1>Erreur</h1>';
            echo '<p><strong>' . get_class($e) . ':</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        } else {
            http_response_code(500);
            echo '<h1>Une erreur est survenue</h1>';
            error_log($e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }
}
