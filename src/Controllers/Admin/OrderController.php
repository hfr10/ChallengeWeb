<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Repository\OrderRepository;

/**
 * Contrôleur admin pour les commandes
 */
class OrderController extends Controller
{
    private OrderRepository $orderRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
    }

    /**
     * Liste des commandes
     */
    public function index(): void
    {
        $query = $this->getQueryData();
        $page = max(1, (int) ($query['page'] ?? 1));
        $perPage = 20;

        $filters = [
            'status' => $query['status'] ?? null,
            'search' => $query['search'] ?? null,
        ];

        $orders = $this->orderRepository->findAll($filters, $perPage, ($page - 1) * $perPage);
        $totalOrders = $this->orderRepository->count($filters);

        $this->render('admin/orders/index', [
            'title' => 'Commandes',
            'orders' => $orders,
            'filters' => $filters,
            'pagination' => [
                'current' => $page,
                'total' => ceil($totalOrders / $perPage),
                'totalItems' => $totalOrders,
            ],
        ], 'admin');
    }

    /**
     * Détail d'une commande
     */
    public function show(string $id): void
    {
        $order = $this->orderRepository->findById((int) $id);

        if (!$order) {
            $this->redirectWithMessage('/admin/orders', 'error', 'Commande non trouvée.');
            return;
        }

        $this->render('admin/orders/show', [
            'title' => 'Commande #' . $order->order_number,
            'order' => $order,
        ], 'admin');
    }

    /**
     * Met à jour le statut d'une commande
     */
    public function updateStatus(string $id): void
    {
        $order = $this->orderRepository->findById((int) $id);

        if (!$order) {
            $this->redirectWithMessage('/admin/orders', 'error', 'Commande non trouvée.');
            return;
        }

        $data = $this->getPostData();
        $newStatus = $data['status'] ?? '';

        $validStatuses = array_keys(\App\Models\Order::STATUS_LABELS);
        if (!in_array($newStatus, $validStatuses)) {
            $this->redirectWithMessage('/admin/orders/' . $id, 'error', 'Statut invalide.');
            return;
        }

        $this->orderRepository->updateStatus((int) $id, $newStatus);
        Session::flash('success', 'Statut de la commande mis à jour.');
        $this->redirect('/admin/orders/' . $id);
    }
}
