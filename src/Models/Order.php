<?php

namespace App\Models;

/**
 * Modèle Order
 * Représente une commande
 */
class Order
{
    public ?int $id = null;
    public ?int $user_id = null;
    public string $order_number;
    public string $status = 'pending';
    public float $subtotal;
    public float $shipping_cost = 0;
    public float $total;

    // Adresse de livraison
    public ?string $shipping_first_name = null;
    public ?string $shipping_last_name = null;
    public ?string $shipping_address = null;
    public ?string $shipping_city = null;
    public ?string $shipping_postal_code = null;
    public ?string $shipping_phone = null;

    // Adresse de facturation
    public ?string $billing_first_name = null;
    public ?string $billing_last_name = null;
    public ?string $billing_address = null;
    public ?string $billing_city = null;
    public ?string $billing_postal_code = null;

    public ?string $notes = null;
    public ?string $payment_method = null;
    public string $payment_status = 'pending';
    public ?string $created_at = null;
    public ?string $updated_at = null;

    // Relations
    public ?User $user = null;
    public array $items = [];

    // Labels des statuts
    public const STATUS_LABELS = [
        'pending' => 'En attente',
        'confirmed' => 'Confirmée',
        'paid' => 'Payée',
        'processing' => 'En préparation',
        'shipped' => 'Expédiée',
        'delivered' => 'Livrée',
        'cancelled' => 'Annulée',
        'refunded' => 'Remboursée',
    ];

    public const STATUS_COLORS = [
        'pending' => 'yellow',
        'confirmed' => 'blue',
        'paid' => 'green',
        'processing' => 'indigo',
        'shipped' => 'purple',
        'delivered' => 'green',
        'cancelled' => 'red',
        'refunded' => 'gray',
    ];

    /**
     * Crée une instance à partir d'un tableau
     */
    public static function fromArray(array $data): self
    {
        $order = new self();
        foreach ($data as $key => $value) {
            if (property_exists($order, $key)) {
                if (in_array($key, ['subtotal', 'shipping_cost', 'total'])) {
                    $order->$key = (float) $value;
                } else {
                    $order->$key = $value;
                }
            }
        }
        return $order;
    }

    /**
     * Génère un numéro de commande unique
     */
    public static function generateOrderNumber(): string
    {
        return 'FS-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    /**
     * Retourne le label du statut
     */
    public function getStatusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    /**
     * Retourne la couleur du statut
     */
    public function getStatusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'gray';
    }

    /**
     * Retourne l'adresse de livraison formatée
     */
    public function getFormattedShippingAddress(): string
    {
        $parts = [
            $this->shipping_first_name . ' ' . $this->shipping_last_name,
            $this->shipping_address,
            $this->shipping_postal_code . ' ' . $this->shipping_city,
        ];
        return implode("\n", array_filter($parts));
    }

    /**
     * Vérifie si la commande peut être annulée
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }
}
