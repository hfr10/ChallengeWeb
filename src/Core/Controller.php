<?php

namespace App\Core;

/**
 * Contrôleur de base
 * Tous les contrôleurs héritent de cette classe
 */
abstract class Controller
{
    /**
     * Rend une vue avec un layout
     */
    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        // Extraire les données pour les rendre accessibles dans la vue
        extract($data);

        // Capturer le contenu de la vue
        ob_start();
        $viewPath = ROOT_PATH . '/views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View {$view} not found at {$viewPath}");
        }
        require $viewPath;
        $content = ob_get_clean();

        // Charger le layout
        $layoutPath = ROOT_PATH . '/views/layouts/' . $layout . '.php';
        if (!file_exists($layoutPath)) {
            throw new \RuntimeException("Layout {$layout} not found");
        }
        require $layoutPath;
    }

    /**
     * Rend une vue sans layout
     */
    protected function renderPartial(string $view, array $data = []): void
    {
        extract($data);
        $viewPath = ROOT_PATH . '/views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View {$view} not found");
        }
        require $viewPath;
    }

    /**
     * Retourne une réponse JSON
     */
    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Redirige vers une URL
     */
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Redirige avec un message flash
     */
    protected function redirectWithMessage(string $url, string $type, string $message): void
    {
        Session::flash($type, $message);
        $this->redirect($url);
    }

    /**
     * Récupère les données POST
     */
    protected function getPostData(): array
    {
        return $_POST;
    }

    /**
     * Récupère les données GET
     */
    protected function getQueryData(): array
    {
        return $_GET;
    }

    /**
     * Récupère les données JSON du corps de la requête
     */
    protected function getJsonData(): array
    {
        $json = file_get_contents('php://input');
        return json_decode($json, true) ?? [];
    }

    /**
     * Vérifie si la requête est AJAX
     */
    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Récupère l'utilisateur connecté
     */
    protected function getCurrentUser(): ?array
    {
        return Session::get('user');
    }

    /**
     * Vérifie si l'utilisateur est connecté
     */
    protected function isAuthenticated(): bool
    {
        return Session::has('user');
    }

    /**
     * Vérifie si l'utilisateur est admin
     */
    protected function isAdmin(): bool
    {
        $user = $this->getCurrentUser();
        return $user && ($user['role'] ?? '') === 'admin';
    }
}
