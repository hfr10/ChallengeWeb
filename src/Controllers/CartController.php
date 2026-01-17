<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;

/**
 * Contrôleur du panier
 * Gère l'ajout, la modification et la suppression d'articles
 */
class CartController extends Controller
{
    private CartRepository $cartRepository;
    private ProductRepository $productRepository;

    public function __construct()
    {
        $this->cartRepository = new CartRepository();
        $this->productRepository = new ProductRepository();
    }

    /**
     * Récupère le panier courant
     */
    private function getCart()
    {
        $userId = $this->getCurrentUser()['id'] ?? null;
        return $this->cartRepository->findOrCreate($userId, Session::getId());
    }

    /**
     * Affiche la page du panier
     */
    public function show(): void
    {
        $cart = $this->getCart();

        $this->render('cart/index', [
            'title' => 'Mon panier',
            'cart' => $cart,
        ]);
    }

    /**
     * API: Récupère le contenu du panier
     */
    public function index(): void
    {
        $cart = $this->getCart();

        $items = [];
        foreach ($cart->items as $item) {
            $items[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'slug' => $item->product->slug,
                'price' => $item->getUnitPrice(),
                'quantity' => $item->quantity,
                'size' => $item->getSizeName(),
                'image' => $item->product->getImageUrl(),
                'total' => $item->getTotalPrice(),
            ];
        }

        $this->json([
            'items' => $items,
            'subtotal' => $cart->getSubtotal(),
            'count' => $cart->getItemCount(),
        ]);
    }

    /**
     * API: Ajoute un produit au panier
     */
    public function add(): void
    {
        $data = $this->getJsonData();

        $productId = (int) ($data['product_id'] ?? 0);
        $sizeId = !empty($data['size_id']) ? (int) $data['size_id'] : null;
        $quantity = (int) ($data['quantity'] ?? 1);

        // Vérifier le produit
        $product = $this->productRepository->findById($productId);
        if (!$product || !$product->is_active) {
            $this->json(['success' => false, 'error' => 'Produit non trouvé'], 404);
            return;
        }

        // Vérifier le stock
        if (!$product->isInStock()) {
            $this->json(['success' => false, 'error' => 'Produit en rupture de stock'], 400);
            return;
        }

        // Vérifier la taille si nécessaire
        if (!empty($product->sizes) && !$sizeId) {
            $this->json(['success' => false, 'error' => 'Veuillez sélectionner une taille'], 400);
            return;
        }

        $cart = $this->getCart();
        $this->cartRepository->addItem($cart->id, $productId, $sizeId, $quantity);

        // Recharger le panier
        $cart = $this->getCart();

        $this->json([
            'success' => true,
            'message' => 'Produit ajouté au panier',
            'count' => $cart->getItemCount(),
            'subtotal' => $cart->getSubtotal(),
        ]);
    }

    /**
     * API: Met à jour la quantité d'un item
     */
    public function update(): void
    {
        $data = $this->getJsonData();

        $itemId = (int) ($data['item_id'] ?? 0);
        $quantity = (int) ($data['quantity'] ?? 0);

        if ($quantity <= 0) {
            $this->cartRepository->removeItem($itemId);
        } else {
            $this->cartRepository->updateItemQuantity($itemId, $quantity);
        }

        $cart = $this->getCart();

        $this->json([
            'success' => true,
            'count' => $cart->getItemCount(),
            'subtotal' => $cart->getSubtotal(),
        ]);
    }

    /**
     * API: Supprime un item du panier
     */
    public function remove(): void
    {
        $data = $this->getJsonData();
        $itemId = (int) ($data['item_id'] ?? 0);

        $this->cartRepository->removeItem($itemId);

        $cart = $this->getCart();

        $this->json([
            'success' => true,
            'count' => $cart->getItemCount(),
            'subtotal' => $cart->getSubtotal(),
        ]);
    }

    /**
     * API: Compte les articles dans le panier
     */
    public function count(): void
    {
        $cart = $this->getCart();

        $this->json([
            'count' => $cart->getItemCount(),
        ]);
    }
}
