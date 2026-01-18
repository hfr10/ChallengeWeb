<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Repository\UserRepository;

/**
 * Contrôleur admin pour les utilisateurs
 */
class UserController extends Controller
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
     * Liste des utilisateurs
     */
    public function index(): void
    {
        $users = $this->userRepository->findAll();

        $this->render('admin/users/index', [
            'title' => 'Utilisateurs',
            'users' => $users,
        ], 'admin');
    }

    /**
     * Détail d'un utilisateur
     */
    public function show(string $id): void
    {
        $user = $this->userRepository->findById((int) $id);

        if (!$user) {
            $this->redirectWithMessage('/admin/users', 'error', 'Utilisateur non trouvé.');
            return;
        }

        $this->render('admin/users/show', [
            'title' => $user->getFullName(),
            'user' => $user,
        ], 'admin');
    }

    /**
     * Met à jour le rôle d'un utilisateur
     */
    public function updateRole(string $id): void
    {
        $user = $this->userRepository->findById((int) $id);

        if (!$user) {
            $this->redirectWithMessage('/admin/users', 'error', 'Utilisateur non trouvé.');
            return;
        }

        // Empêcher de modifier son propre rôle
        $currentUser = $this->getCurrentUser();
        if ($user->id === $currentUser['id']) {
            $this->redirectWithMessage('/admin/users/' . $id, 'error', 'Vous ne pouvez pas modifier votre propre rôle.');
            return;
        }

        $data = $this->getPostData();
        $newRole = $data['role'] ?? '';

        if (!in_array($newRole, ['customer', 'admin'])) {
            $this->redirectWithMessage('/admin/users/' . $id, 'error', 'Rôle invalide.');
            return;
        }

        $user->role = $newRole;
        $this->userRepository->update($user);

        Session::flash('success', 'Rôle de l\'utilisateur mis à jour.');
        $this->redirect('/admin/users/' . $id);
    }
}
