<?php

namespace App\Models;

/**
 * Modèle CartItem
 * Représente un article dans le panier
 */
class CartItem
{
    public ?int $id = null;
    public int $cart_id;
    public int $product_id;
    public ?int $product_size_id = null;
    public int $quantity = 1;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    // Relations
    public ?Product $product = null;
    public ?array $size = null;

    /**
     * Crée une instance à partir d'un tableau
     */
    public static function fromArray(array $data): self
    {
        $item = new self();
        foreach ($data as $key => $value) {
            if (property_exists($item, $key)) {
                $item->$key = $value;
            }
        }
        return $item;
    }

    /**
     * Retourne le prix unitaire
     */
    public function getUnitPrice(): float
    {
        if ($this->product) {
            return $this->product->getEffectivePrice();
        }
        return 0;
    }

    /**
     * Retourne le prix total (quantité * prix unitaire)
     */
    public function getTotalPrice(): float
    {
        return $this->getUnitPrice() * $this->quantity;
    }

    /**
     * Retourne le nom de la taille
     */
    public function getSizeName(): ?string
    {
        return $this->size['size'] ?? null;
    }
}
