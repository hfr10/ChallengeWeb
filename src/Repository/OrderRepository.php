<?php

namespace App\Repository;

use App\Core\Database;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;

/**
 * Repository pour les commandes
 * Gère la persistance et récupération des commandes
 */
class OrderRepository
{
    /**
     * Trouve une commande par son ID
     */
    public function findById(int $id, bool $withItems = true): ?Order
    {
        $data = Database::fetchOne("SELECT * FROM orders WHERE id = ?", [$id]);

        if (!$data) {
            return null;
        }

        $order = Order::fromArray($data);

        if ($withItems) {
            $this->loadItems($order);
        }

        return $order;
    }

    /**
     * Trouve une commande par son numéro
     */
    public function findByOrderNumber(string $orderNumber): ?Order
    {
        $data = Database::fetchOne("SELECT * FROM orders WHERE order_number = ?", [$orderNumber]);

        if (!$data) {
            return null;
        }

        $order = Order::fromArray($data);
        $this->loadItems($order);

        return $order;
    }

    /**
     * Charge les items d'une commande
     */
    private function loadItems(Order $order): void
    {
        $data = Database::fetchAll(
            "SELECT * FROM order_items WHERE order_id = ?",
            [$order->id]
        );

        $order->items = array_map(fn($row) => OrderItem::fromArray($row), $data);
    }

    /**
     * Récupère les commandes d'un utilisateur
     */
    public function findByUser(int $userId, int $limit = 20, int $offset = 0): array
    {
        $data = Database::fetchAll(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$userId, $limit, $offset]
        );

        return array_map(fn($row) => Order::fromArray($row), $data);
    }

    /**
     * Récupère toutes les commandes avec filtres
     */
    public function findAll(array $filters = [], int $limit = 20, int $offset = 0): array
    {
        $sql = "SELECT o.*, u.email as user_email, u.first_name, u.last_name
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND o.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['user_id'])) {
            $sql .= " AND o.user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND o.created_at >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND o.created_at <= ?";
            $params[] = $filters['date_to'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (o.order_number ILIKE ? OR u.email ILIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $data = Database::fetchAll($sql, $params);

        return array_map(function ($row) {
            $order = Order::fromArray($row);
            if (isset($row['user_email'])) {
                $order->user = new User();
                $order->user->email = $row['user_email'];
                $order->user->first_name = $row['first_name'];
                $order->user->last_name = $row['last_name'];
            }
            return $order;
        }, $data);
    }

    /**
     * Compte les commandes
     */
    public function count(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) as count FROM orders WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['user_id'])) {
            $sql .= " AND user_id = ?";
            $params[] = $filters['user_id'];
        }

        $result = Database::fetchOne($sql, $params);
        return (int) $result['count'];
    }

    /**
     * Crée une commande
     */
    public function create(Order $order): int
    {
        $data = [
            'user_id' => $order->user_id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'subtotal' => $order->subtotal,
            'shipping_cost' => $order->shipping_cost,
            'total' => $order->total,
            'shipping_first_name' => $order->shipping_first_name,
            'shipping_last_name' => $order->shipping_last_name,
            'shipping_address' => $order->shipping_address,
            'shipping_city' => $order->shipping_city,
            'shipping_postal_code' => $order->shipping_postal_code,
            'shipping_phone' => $order->shipping_phone,
            'billing_first_name' => $order->billing_first_name,
            'billing_last_name' => $order->billing_last_name,
            'billing_address' => $order->billing_address,
            'billing_city' => $order->billing_city,
            'billing_postal_code' => $order->billing_postal_code,
            'notes' => $order->notes,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
        ];

        return Database::insert('orders', $data);
    }

    /**
     * Ajoute un item à une commande
     */
    public function addItem(OrderItem $item): int
    {
        return Database::insert('order_items', [
            'order_id' => $item->order_id,
            'product_id' => $item->product_id,
            'product_name' => $item->product_name,
            'product_sku' => $item->product_sku,
            'size' => $item->size,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
            'total_price' => $item->total_price,
        ]);
    }

    /**
     * Met à jour le statut d'une commande
     */
    public function updateStatus(int $orderId, string $status): bool
    {
        return Database::update('orders', ['status' => $status], 'id = ?', [$orderId]) > 0;
    }

    /**
     * Statistiques pour le dashboard admin
     */
    public function getStats(): array
    {
        $stats = [];

        // Total des ventes
        $result = Database::fetchOne(
            "SELECT COALESCE(SUM(total), 0) as total FROM orders WHERE status NOT IN ('cancelled', 'refunded')"
        );
        $stats['total_revenue'] = (float) $result['total'];

        // Nombre de commandes
        $result = Database::fetchOne("SELECT COUNT(*) as count FROM orders");
        $stats['total_orders'] = (int) $result['count'];

        // Commandes en attente
        $result = Database::fetchOne("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
        $stats['pending_orders'] = (int) $result['count'];

        // Commandes du jour
        $result = Database::fetchOne(
            "SELECT COUNT(*) as count, COALESCE(SUM(total), 0) as total
             FROM orders WHERE DATE(created_at) = CURRENT_DATE"
        );
        $stats['today_orders'] = (int) $result['count'];
        $stats['today_revenue'] = (float) $result['total'];

        return $stats;
    }
}
