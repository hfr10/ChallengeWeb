<?php

/**
 * Définition des routes de l'application
 */

use App\Core\Application;
use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\OrderController;
use App\Controllers\AuthController;
use App\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Controllers\Admin\ProductController as AdminProductController;
use App\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Controllers\Admin\OrderController as AdminOrderController;
use App\Controllers\Admin\UserController as AdminUserController;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

$router = Application::getInstance()->router();

// ============================================
// Routes publiques
// ============================================

// Accueil
$router->get('/', [HomeController::class, 'index']);

// Produits
$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/{slug}', [ProductController::class, 'show']);
$router->get('/categories/{slug}', [ProductController::class, 'byCategory']);

// Authentification
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

// ============================================
// Routes API (panier - utilisées par Vue.js)
// ============================================

$router->get('/api/cart', [CartController::class, 'index']);
$router->post('/api/cart/add', [CartController::class, 'add']);
$router->post('/api/cart/update', [CartController::class, 'update']);
$router->post('/api/cart/remove', [CartController::class, 'remove']);
$router->get('/api/cart/count', [CartController::class, 'count']);

// ============================================
// Routes protégées (authentification requise)
// ============================================

// Panier et commandes
$router->get('/cart', [CartController::class, 'show'], [AuthMiddleware::class]);
$router->get('/checkout', [OrderController::class, 'checkout'], [AuthMiddleware::class]);
$router->post('/checkout', [OrderController::class, 'processCheckout'], [AuthMiddleware::class]);
$router->get('/orders', [OrderController::class, 'index'], [AuthMiddleware::class]);
$router->get('/orders/{id}', [OrderController::class, 'show'], [AuthMiddleware::class]);

// ============================================
// Routes administration
// ============================================

$adminMiddlewares = [AuthMiddleware::class, AdminMiddleware::class];

// Dashboard
$router->get('/admin', [AdminDashboardController::class, 'index'], $adminMiddlewares);

// Gestion des produits
$router->get('/admin/products', [AdminProductController::class, 'index'], $adminMiddlewares);
$router->get('/admin/products/create', [AdminProductController::class, 'create'], $adminMiddlewares);
$router->post('/admin/products', [AdminProductController::class, 'store'], $adminMiddlewares);
$router->get('/admin/products/{id}/edit', [AdminProductController::class, 'edit'], $adminMiddlewares);
$router->post('/admin/products/{id}', [AdminProductController::class, 'update'], $adminMiddlewares);
$router->post('/admin/products/{id}/delete', [AdminProductController::class, 'destroy'], $adminMiddlewares);

// Gestion des catégories
$router->get('/admin/categories', [AdminCategoryController::class, 'index'], $adminMiddlewares);
$router->get('/admin/categories/create', [AdminCategoryController::class, 'create'], $adminMiddlewares);
$router->post('/admin/categories', [AdminCategoryController::class, 'store'], $adminMiddlewares);
$router->get('/admin/categories/{id}/edit', [AdminCategoryController::class, 'edit'], $adminMiddlewares);
$router->post('/admin/categories/{id}', [AdminCategoryController::class, 'update'], $adminMiddlewares);
$router->post('/admin/categories/{id}/delete', [AdminCategoryController::class, 'destroy'], $adminMiddlewares);

// Gestion des commandes
$router->get('/admin/orders', [AdminOrderController::class, 'index'], $adminMiddlewares);
$router->get('/admin/orders/{id}', [AdminOrderController::class, 'show'], $adminMiddlewares);
$router->post('/admin/orders/{id}/status', [AdminOrderController::class, 'updateStatus'], $adminMiddlewares);

// Gestion des utilisateurs
$router->get('/admin/users', [AdminUserController::class, 'index'], $adminMiddlewares);
$router->get('/admin/users/{id}', [AdminUserController::class, 'show'], $adminMiddlewares);
$router->post('/admin/users/{id}/role', [AdminUserController::class, 'updateRole'], $adminMiddlewares);
