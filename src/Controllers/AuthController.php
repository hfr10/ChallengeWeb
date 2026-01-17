<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Repository\UserRepository;
use App\Repository\CartRepository;
use App\Models\User;

/**
 * Contrôleur d'authentification
 * Gère la connexion, l'inscription et la déconnexion des utilisateurs
 */
class AuthController extends Controller
{
    private UserRepository $userRepository;
    private CartRepository $cartRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->cartRepository = new CartRepository();
    }

    /**
     * Affiche le formulaire de connexion
     */
    public function showLogin(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/');
        }

        $this->render('auth/login', [
            'title' => 'Connexion',
        ]);
    }

    /**
     * Traite la connexion
     */
    public function login(): void
    {
        $data = $this->getPostData();

        $validator = new Validator($data);
        $validator
            ->required('email', 'L\'email est requis.')
            ->email('email')
            ->required('password', 'Le mot de passe est requis.');

        if (!$validator->isValid()) {
            Session::flash('error', $validator->getFirstErrors()['email'] ?? $validator->getFirstErrors()['password']);
            Session::flash('old', $data);
            $this->redirect('/login');
            return;
        }

        $user = $this->userRepository->findByEmail($data['email']);

        if (!$user || !$user->verifyPassword($data['password'])) {
            Session::flash('error', 'Email ou mot de passe incorrect.');
            Session::flash('old', ['email' => $data['email']]);
            $this->redirect('/login');
            return;
        }

        // Connexion réussie
        Session::regenerate();
        Session::set('user', $user->toArray());

        // Transférer le panier de session vers l'utilisateur
        $this->cartRepository->transferToUser(Session::getId(), $user->id);

        Session::flash('success', 'Bienvenue, ' . $user->first_name . ' !');

        // Rediriger vers la page demandée ou l'accueil
        $redirect = Session::get('redirect_after_login', '/');
        Session::remove('redirect_after_login');

        $this->redirect($redirect);
    }

    /**
     * Affiche le formulaire d'inscription
     */
    public function showRegister(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/');
        }

        $this->render('auth/register', [
            'title' => 'Inscription',
        ]);
    }

    /**
     * Traite l'inscription
     */
    public function register(): void
    {
        $data = $this->getPostData();

        $validator = new Validator($data);
        $validator
            ->required('first_name', 'Le prénom est requis.')
            ->maxLength('first_name', 100)
            ->required('last_name', 'Le nom est requis.')
            ->maxLength('last_name', 100)
            ->required('email', 'L\'email est requis.')
            ->email('email')
            ->required('password', 'Le mot de passe est requis.')
            ->minLength('password', 8, 'Le mot de passe doit contenir au moins 8 caractères.')
            ->required('password_confirm', 'La confirmation du mot de passe est requise.')
            ->matches('password_confirm', 'password', 'Les mots de passe ne correspondent pas.');

        if (!$validator->isValid()) {
            Session::flash('errors', $validator->getFirstErrors());
            Session::flash('old', $data);
            $this->redirect('/register');
            return;
        }

        // Vérifier si l'email existe déjà
        if ($this->userRepository->emailExists($data['email'])) {
            Session::flash('error', 'Cette adresse email est déjà utilisée.');
            Session::flash('old', $data);
            $this->redirect('/register');
            return;
        }

        // Créer l'utilisateur
        $user = new User();
        $user->email = $data['email'];
        $user->setPassword($data['password']);
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->role = 'customer';

        $userId = $this->userRepository->create($user);
        $user->id = $userId;

        // Connexion automatique
        Session::regenerate();
        Session::set('user', $user->toArray());

        // Transférer le panier
        $this->cartRepository->transferToUser(Session::getId(), $userId);

        Session::flash('success', 'Votre compte a été créé avec succès !');
        $this->redirect('/');
    }

    /**
     * Déconnexion
     */
    public function logout(): void
    {
        Session::destroy();
        session_start();
        Session::flash('success', 'Vous avez été déconnecté.');
        $this->redirect('/');
    }
}
