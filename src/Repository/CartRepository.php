<?php

namespace App\Repository;

use App\Core\Database;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

/**
 * Repository pour le panier
 */
class CartRepository
{
    /**
     * Trouve ou crée un panier pour l'utilisateur/session
     */
    public function findOrCreate(?int $userId, string $sessionId): Cart
    {
        $cart = $this->findByUserOrSession($userId, $sessionId);

        if (!$cart) {
            $cart = new Cart();
            $cart->user_id = $userId;
            $cart->session_id = $sessionId;

            $cart->id = Database::insert('carts', [
                'user_id' => $userId,
                'session_id' => $sessionId,
            ]);
        }

        $this->loadItems($cart);
        return $cart;
    }

    /**
     * Trouve un panier par utilisateur ou session
     */
    public function findByUserOrSession(?int $userId, string $sessionId): ?Cart
    {
        $sql = "SELECT * FROM carts WHERE ";
        $params = [];

        if ($userId) {
            $sql .= "user_id = ?";
            $params[] = $userId;
        } else {
            $sql .= "session_id = ?";
            $params[] = $sessionId;
        }

        $sql .= " ORDER BY created_at DESC LIMIT 1";
        $data = Database::fetchOne($sql, $params);

        if (!$data) {
            return null;
        }

        return Cart::fromArray($data);
    }

    /**
     * Charge les items du panier
     */
    public function loadItems(Cart $cart): void
    {
        $sql = "SELECT ci.*, p.name as product_name, p.slug as product_slug,
                       p.price, p.sale_price, p.image, p.stock as product_stock,
                       ps.size, ps.stock as size_stock
                FROM cart_items ci
                JOIN products p ON ci.product_id = p.id
                LEFT JOIN product_sizes ps ON ci.product_size_id = ps.id
                WHERE ci.cart_id = ?
                ORDER BY ci.created_at DESC";

        $data = Database::fetchAll($sql, [$cart->id]);
        $cart->items = [];

        foreach ($data as $row) {
            $item = CartItem::fromArray($row);

            // Créer le produit associé
            $product = new Product();
            $product->id = $row['product_id'];
            $product->name = $row['product_name'];
            $product->slug = $row['product_slug'];
            $product->price = (float) $row['price'];
            $product->sale_price = $row['sale_price'] ? (float) $row['sale_price'] : null;
            $product->image = $row['image'];
            $product->stock = (int) $row['product_stock'];
            $item->product = $product;

            // Taille si applicable
            if ($row['size']) {
                $item->size = [
                    'size' => $row['size'],
                    'stock' => (int) $row['size_stock'],
                ];
            }

            $cart->items[] = $item;
        }
    }

    /**
     * Ajoute un produit au panier
     */
    public function addItem(int $cartId, int $productId, ?int $sizeId, int $quantity = 1): void
    {
        // Vérifier si l'item existe déjà
        $sql = "SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?";
        $params = [$cartId, $productId];

        if ($sizeId) {
            $sql .= " AND product_size_id = ?";
            $params[] = $sizeId;
        } else {
            $sql .= " AND product_size_id IS NULL";
        }

        $existing = Database::fetchOne($sql, $params);

        if ($existing) {
            // Mettre à jour la quantité
            Database::query(
                "UPDATE cart_items SET quantity = quantity + ? WHERE id = ?",
                [$quantity, $existing['id']]
            );
        } else {
            // Créer un nouvel item
            Database::insert('cart_items', [
                'cart_id' => $cartId,
                'product_id' => $productId,
                'product_size_id' => $sizeId,
                'quantity' => $quantity,
            ]);
        }
    }

    /**
     * Met à jour la quantité d'un item
     */
    public function updateItemQuantity(int $itemId, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->removeItem($itemId);
        }

        return Database::update('cart_items', ['quantity' => $quantity], 'id = ?', [$itemId]) > 0;
    }

    /**
     * Supprime un item du panier
     */
    public function removeItem(int $itemId): bool
    {
        return Database::delete('cart_items', 'id = ?', [$itemId]) > 0;
    }

    /**
     * Vide le panier
     */
    public function clear(int $cartId): void
    {
        Database::delete('cart_items', 'cart_id = ?', [$cartId]);
    }

    /**
     * Compte les articles dans le panier
     */
    public function getItemCount(int $cartId): int
    {
        $result = Database::fetchOne(
            "SELECT COALESCE(SUM(quantity), 0) as count FROM cart_items WHERE cart_id = ?",
            [$cartId]
        );
        return (int) $result['count'];
    }

    /**
     * Transfère le panier d'une session vers un utilisateur
     */
    public function transferToUser(string $sessionId, int $userId): void
    {
        // Trouver le panier de session
        $sessionCart = Database::fetchOne(
            "SELECT * FROM carts WHERE session_id = ? AND user_id IS NULL",
            [$sessionId]
        );

        if (!$sessionCart) {
            return;
        }

        // Trouver le panier utilisateur existant
        $userCart = Database::fetchOne(
            "SELECT * FROM carts WHERE user_id = ?",
            [$userId]
        );

        if ($userCart) {
            // Fusionner les items
            $sessionItems = Database::fetchAll(
                "SELECT * FROM cart_items WHERE cart_id = ?",
                [$sessionCart['id']]
            );

            foreach ($sessionItems as $item) {
                $this->addItem(
                    $userCart['id'],
                    $item['product_id'],
                    $item['product_size_id'],
                    $item['quantity']
                );
            }

            // Supprimer le panier de session
            Database::delete('carts', 'id = ?', [$sessionCart['id']]);
        } else {
            // Transférer le panier
            Database::update(
                'carts',
                ['user_id' => $userId, 'session_id' => null],
                'id = ?',
                [$sessionCart['id']]
            );
        }
    }
}
