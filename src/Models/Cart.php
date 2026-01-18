<?php

namespace App\Models;

/**
 * Modèle Cart
 * Représente un panier d'achat
 */
class Cart
{
    public ?int $id = null;
    public ?int $user_id = null;
    public ?string $session_id = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    // Items du panier
    public array $items = [];

    /**
     * Crée une instance à partir d'un tableau
     */
    public static function fromArray(array $data): self
    {
        $cart = new self();
        foreach ($data as $key => $value) {
            if (property_exists($cart, $key)) {
                $cart->$key = $value;
            }
        }
        return $cart;
    }

    /**
     * Calcule le sous-total du panier
     */
    public function getSubtotal(): float
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getTotalPrice();
        }
        return $total;
    }

    /**
     * Calcule le nombre total d'articles
     */
    public function getItemCount(): int
    {
        $count = 0;
        foreach ($this->items as $item) {
            $count += $item->quantity;
        }
        return $count;
    }

    /**
     * Vérifie si le panier est vide
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }
}
