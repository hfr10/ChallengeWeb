<?php

namespace App\Core;

/**
 * Gestionnaire de routes de l'application
 * Supporte les méthodes GET, POST, PUT, DELETE
 * Gère les paramètres dynamiques dans les URLs
 */
class Router
{
    private array $routes = [];
    private array $middlewares = [];

    /**
     * Enregistre une route GET
     */
    public function get(string $path, array $handler, array $middlewares = []): self
    {
        return $this->addRoute('GET', $path, $handler, $middlewares);
    }

    /**
     * Enregistre une route POST
     */
    public function post(string $path, array $handler, array $middlewares = []): self
    {
        return $this->addRoute('POST', $path, $handler, $middlewares);
    }

    /**
     * Enregistre une route PUT
     */
    public function put(string $path, array $handler, array $middlewares = []): self
    {
        return $this->addRoute('PUT', $path, $handler, $middlewares);
    }

    /**
     * Enregistre une route DELETE
     */
    public function delete(string $path, array $handler, array $middlewares = []): self
    {
        return $this->addRoute('DELETE', $path, $handler, $middlewares);
    }

    /**
     * Ajoute une route au registre
     */
    private function addRoute(string $method, string $path, array $handler, array $middlewares): self
    {
        $pattern = $this->convertPathToRegex($path);

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'controller' => $handler[0],
            'action' => $handler[1],
            'middlewares' => $middlewares,
        ];

        return $this;
    }

    /**
     * Convertit un chemin avec paramètres en regex
     * Ex: /products/{id} devient /products/([^/]+)
     */
    private function convertPathToRegex(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    /**
     * Résout la route correspondant à la requête
     */
    public function resolve(string $method, string $uri): ?array
    {
        // Nettoyer l'URI
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        // Gérer la méthode HTTP (support PUT/DELETE via _method)
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extraire les paramètres nommés
                $params = array_filter($matches, fn($key) => is_string($key), ARRAY_FILTER_USE_KEY);

                return [
                    'controller' => $route['controller'],
                    'action' => $route['action'],
                    'params' => $params,
                    'middlewares' => $route['middlewares'],
                ];
            }
        }

        return null;
    }

    /**
     * Dispatche la requête vers le contrôleur approprié
     */
    public function dispatch(string $method, string $uri): void
    {
        $route = $this->resolve($method, $uri);

        if ($route === null) {
            $this->handleNotFound();
            return;
        }

        // Exécuter les middlewares
        foreach ($route['middlewares'] as $middleware) {
            $middlewareInstance = new $middleware();
            $result = $middlewareInstance->handle();
            if ($result === false) {
                return;
            }
        }

        // Instancier le contrôleur et appeler l'action
        $controllerClass = $route['controller'];
        $action = $route['action'];
        $params = $route['params'];

        if (!class_exists($controllerClass)) {
            throw new \RuntimeException("Controller {$controllerClass} not found");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            throw new \RuntimeException("Action {$action} not found in {$controllerClass}");
        }

        call_user_func_array([$controller, $action], $params);
    }

    /**
     * Gère les erreurs 404
     */
    private function handleNotFound(): void
    {
        http_response_code(404);
        if (file_exists(__DIR__ . '/../../views/errors/404.php')) {
            require __DIR__ . '/../../views/errors/404.php';
        } else {
            echo '<h1>404 - Page non trouvée</h1>';
        }
    }
}
