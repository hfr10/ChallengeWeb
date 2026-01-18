<?php

namespace App\Core;

/**
 * Gestionnaire de session
 * Fournit des méthodes pour manipuler les données de session
 */
class Session
{
    /**
     * Récupère une valeur de session
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Définit une valeur de session
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Vérifie si une clé existe en session
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Supprime une valeur de session
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Efface toute la session
     */
    public static function clear(): void
    {
        $_SESSION = [];
    }

    /**
     * Détruit la session
     */
    public static function destroy(): void
    {
        self::clear();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * Régénère l'ID de session (sécurité)
     */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    /**
     * Définit un message flash (affiché une seule fois)
     */
    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Récupère et supprime un message flash
     */
    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    /**
     * Vérifie si un message flash existe
     */
    public static function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }

    /**
     * Récupère tous les messages flash et les supprime
     */
    public static function getAllFlash(): array
    {
        $flash = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $flash;
    }

    /**
     * Récupère l'ID de session
     */
    public static function getId(): string
    {
        return session_id();
    }
}
