<?php

namespace App\Middleware;

use App\Core\Session;

/**
 * Middleware d'authentification
 * Vérifie que l'utilisateur est connecté
 */
class AuthMiddleware
{
    public function handle(): bool
    {
        if (!Session::has('user')) {
            Session::flash('error', 'Vous devez être connecté pour accéder à cette page.');
            header('Location: /login');
            exit;
        }
        return true;
    }
}
