<?php

namespace App\Models;

/**
 * Modèle OrderItem
 * Représente un article dans une commande
 */
class OrderItem
{
    public ?int $id = null;
    public int $order_id;
    public ?int $product_id = null;
    public string $product_name;
    public ?string $product_sku = null;
    public ?string $size = null;
    public int $quantity;
    public float $unit_price;
    public float $total_price;
    public ?string $created_at = null;

    // Relations
    public ?Product $product = null;

    /**
     * Crée une instance à partir d'un tableau
     */
    public static function fromArray(array $data): self
    {
        $item = new self();
        foreach ($data as $key => $value) {
            if (property_exists($item, $key)) {
                if (in_array($key, ['unit_price', 'total_price'])) {
                    $item->$key = (float) $value;
                } elseif ($key === 'quantity') {
                    $item->$key = (int) $value;
                } else {
                    $item->$key = $value;
                }
            }
        }
        return $item;
    }
}
