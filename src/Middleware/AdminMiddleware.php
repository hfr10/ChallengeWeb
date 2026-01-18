<?php

namespace App\Middleware;

use App\Core\Session;

/**
 * Middleware d'administration
 * Vérifie que l'utilisateur est admin
 */
class AdminMiddleware
{
    public function handle(): bool
    {
        $user = Session::get('user');

        if (!$user || ($user['role'] ?? '') !== 'admin') {
            Session::flash('error', 'Accès réservé aux administrateurs.');
            header('Location: /');
            exit;
        }

        return true;
    }
}
