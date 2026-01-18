<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Database;
use App\Repository\OrderRepository;
use App\Repository\CartRepository;
use App\Models\Order;
use App\Models\OrderItem;

/**
 * Contrôleur des commandes
 */
class OrderController extends Controller
{
    private OrderRepository $orderRepository;
    private CartRepository $cartRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->cartRepository = new CartRepository();
    }

    /**
     * Liste des commandes de l'utilisateur
     */
    public function index(): void
    {
        $user = $this->getCurrentUser();
        $orders = $this->orderRepository->findByUser($user['id']);

        $this->render('orders/index', [
            'title' => 'Mes commandes',
            'orders' => $orders,
        ]);
    }

    /**
     * Détail d'une commande
     */
    public function show(string $id): void
    {
        $user = $this->getCurrentUser();
        $order = $this->orderRepository->findById((int) $id);

        // Vérifier que la commande appartient à l'utilisateur
        if (!$order || $order->user_id !== $user['id']) {
            $this->redirectWithMessage('/orders', 'error', 'Commande non trouvée.');
            return;
        }

        $this->render('orders/show', [
            'title' => 'Commande #' . $order->order_number,
            'order' => $order,
        ]);
    }

    /**
     * Page de checkout
     */
    public function checkout(): void
    {
        $user = $this->getCurrentUser();
        $cart = $this->cartRepository->findOrCreate($user['id'], Session::getId());

        if ($cart->isEmpty()) {
            $this->redirectWithMessage('/cart', 'error', 'Votre panier est vide.');
            return;
        }

        $this->render('orders/checkout', [
            'title' => 'Finaliser ma commande',
            'cart' => $cart,
            'user' => $user,
        ]);
    }

    /**
     * Traite la commande
     */
    public function processCheckout(): void
    {
        $user = $this->getCurrentUser();
        $cart = $this->cartRepository->findOrCreate($user['id'], Session::getId());

        if ($cart->isEmpty()) {
            $this->redirectWithMessage('/cart', 'error', 'Votre panier est vide.');
            return;
        }

        $data = $this->getPostData();

        // Validation
        $validator = new Validator($data);
        $validator
            ->required('shipping_first_name', 'Le prénom est requis.')
            ->required('shipping_last_name', 'Le nom est requis.')
            ->required('shipping_address', 'L\'adresse est requise.')
            ->required('shipping_city', 'La ville est requise.')
            ->required('shipping_postal_code', 'Le code postal est requis.')
            ->required('shipping_phone', 'Le téléphone est requis.');

        if (!$validator->isValid()) {
            Session::flash('errors', $validator->getFirstErrors());
            Session::flash('old', $data);
            $this->redirect('/checkout');
            return;
        }

        try {
            Database::beginTransaction();

            // Calculer les totaux
            $subtotal = $cart->getSubtotal();
            $shippingCost = $subtotal >= 100 ? 0 : 5.99;
            $total = $subtotal + $shippingCost;

            // Créer la commande
            $order = new Order();
            $order->user_id = $user['id'];
            $order->order_number = Order::generateOrderNumber();
            $order->status = 'pending';
            $order->subtotal = $subtotal;
            $order->shipping_cost = $shippingCost;
            $order->total = $total;

            // Adresse de livraison
            $order->shipping_first_name = $data['shipping_first_name'];
            $order->shipping_last_name = $data['shipping_last_name'];
            $order->shipping_address = $data['shipping_address'];
            $order->shipping_city = $data['shipping_city'];
            $order->shipping_postal_code = $data['shipping_postal_code'];
            $order->shipping_phone = $data['shipping_phone'];

            // Adresse de facturation (même que livraison si non spécifiée)
            if (!empty($data['same_billing_address'])) {
                $order->billing_first_name = $data['shipping_first_name'];
                $order->billing_last_name = $data['shipping_last_name'];
                $order->billing_address = $data['shipping_address'];
                $order->billing_city = $data['shipping_city'];
                $order->billing_postal_code = $data['shipping_postal_code'];
            } else {
                $order->billing_first_name = $data['billing_first_name'] ?? '';
                $order->billing_last_name = $data['billing_last_name'] ?? '';
                $order->billing_address = $data['billing_address'] ?? '';
                $order->billing_city = $data['billing_city'] ?? '';
                $order->billing_postal_code = $data['billing_postal_code'] ?? '';
            }

            $order->notes = $data['notes'] ?? null;
            $order->payment_method = 'simulation';
            $order->payment_status = 'paid'; // Simulation de paiement

            $orderId = $this->orderRepository->create($order);
            $order->id = $orderId;

            // Ajouter les items de la commande
            foreach ($cart->items as $cartItem) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $orderId;
                $orderItem->product_id = $cartItem->product_id;
                $orderItem->product_name = $cartItem->product->name;
                $orderItem->product_sku = $cartItem->product->sku;
                $orderItem->size = $cartItem->getSizeName();
                $orderItem->quantity = $cartItem->quantity;
                $orderItem->unit_price = $cartItem->getUnitPrice();
                $orderItem->total_price = $cartItem->getTotalPrice();

                $this->orderRepository->addItem($orderItem);
            }

            // Mettre à jour le statut (simulation de paiement réussi)
            $this->orderRepository->updateStatus($orderId, 'confirmed');

            // Vider le panier
            $this->cartRepository->clear($cart->id);

            Database::commit();

            Session::flash('success', 'Votre commande a été passée avec succès !');
            $this->redirect('/orders/' . $orderId);

        } catch (\Exception $e) {
            Database::rollback();
            Session::flash('error', 'Une erreur est survenue lors de la commande. Veuillez réessayer.');
            $this->redirect('/checkout');
        }
    }
}
