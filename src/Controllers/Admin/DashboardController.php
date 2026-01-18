<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;

/**
 * Contrôleur du dashboard admin
 */
class DashboardController extends Controller
{
    private OrderRepository $orderRepository;
    private ProductRepository $productRepository;
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->productRepository = new ProductRepository();
        $this->userRepository = new UserRepository();
    }

    /**
     * Page d'accueil admin
     */
    public function index(): void
    {
        $stats = $this->orderRepository->getStats();
        $stats['total_products'] = $this->productRepository->count(['include_inactive' => true]);
        $stats['total_users'] = $this->userRepository->count();

        $recentOrders = $this->orderRepository->findAll([], 5);

        $this->render('admin/dashboard', [
            'title' => 'Dashboard',
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ], 'admin');
    }
}
