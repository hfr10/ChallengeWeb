<?php
$old = \App\Core\Session::getFlash('old', []);
$errors = \App\Core\Session::getFlash('errors', []);
$subtotal = $cart->getSubtotal();
$shippingCost = $subtotal >= 100 ? 0 : 5.99;
$total = $subtotal + $shippingCost;
?>

<div class="max-w-4xl mx-auto">
    <h1 class="checkout-title text-3xl font-bold mb-8">Finaliser ma commande</h1>

    <form action="/checkout" method="POST" class="flex flex-col lg:flex-row gap-8">
        <!-- Formulaire -->
        <div class="flex-1 space-y-6">
            <!-- Adresse de livraison -->
            <div class="checkout-section bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-6">
                    <i class="fas fa-truck mr-2 text-green-600"></i>
                    Adresse de livraison
                </h2>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                        <input
                            type="text"
                            name="shipping_first_name"
                            value="<?= htmlspecialchars($old['shipping_first_name'] ?? $user['first_name'] ?? '') ?>"
                            required
                            class="checkout-input w-full px-4 py-2 border <?= isset($errors['shipping_first_name']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                        <?php if (isset($errors['shipping_first_name'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['shipping_first_name']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                        <input
                            type="text"
                            name="shipping_last_name"
                            value="<?= htmlspecialchars($old['shipping_last_name'] ?? $user['last_name'] ?? '') ?>"
                            required
                            class="checkout-input w-full px-4 py-2 border <?= isset($errors['shipping_last_name']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse *</label>
                    <input
                        type="text"
                        name="shipping_address"
                        value="<?= htmlspecialchars($old['shipping_address'] ?? $user['address'] ?? '') ?>"
                        required
                        class="checkout-input w-full px-4 py-2 border <?= isset($errors['shipping_address']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-green-500"
                    >
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code postal *</label>
                        <input
                            type="text"
                            name="shipping_postal_code"
                            value="<?= htmlspecialchars($old['shipping_postal_code'] ?? $user['postal_code'] ?? '') ?>"
                            required
                            class="checkout-input w-full px-4 py-2 border <?= isset($errors['shipping_postal_code']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ville *</label>
                        <input
                            type="text"
                            name="shipping_city"
                            value="<?= htmlspecialchars($old['shipping_city'] ?? $user['city'] ?? '') ?>"
                            required
                            class="checkout-input w-full px-4 py-2 border <?= isset($errors['shipping_city']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                    <input
                        type="tel"
                        name="shipping_phone"
                        value="<?= htmlspecialchars($old['shipping_phone'] ?? $user['phone'] ?? '') ?>"
                        required
                        class="checkout-input w-full px-4 py-2 border <?= isset($errors['shipping_phone']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-green-500"
                    >
                </div>

                <div class="mt-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="same_billing_address" value="1" checked class="rounded text-green-600">
                        <span class="text-gray-700">Utiliser la même adresse pour la facturation</span>
                    </label>
                </div>
            </div>

            <!-- Notes -->
            <div class="checkout-section bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">
                    <i class="fas fa-comment mr-2 text-green-600"></i>
                    Notes (optionnel)
                </h2>
                <textarea
                    name="notes"
                    rows="3"
                    placeholder="Instructions spéciales pour la livraison..."
                    class="checkout-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                ><?= htmlspecialchars($old['notes'] ?? '') ?></textarea>
            </div>

            <!-- Paiement (simulation) -->
            <div class="checkout-section bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">
                    <i class="fas fa-credit-card mr-2 text-green-600"></i>
                    Paiement
                </h2>
                <div class="payment-simulation bg-yellow-50 text-yellow-800 p-4 rounded-lg">
                    <p class="text-yellow-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Mode simulation :</strong> Le paiement sera automatiquement validé pour cette démonstration.
                    </p>
                </div>
            </div>
        </div>

        <!-- Récapitulatif -->
        <div class="lg:w-80">
            <div class="checkout-summary bg-white rounded-lg shadow-md p-6 sticky top-6">
                <h2 class="text-xl font-bold mb-6">Récapitulatif</h2>

                <!-- Articles -->
                <div class="space-y-4 mb-6">
                    <?php foreach ($cart->items as $item): ?>
                        <div class="checkout-item flex gap-3">
                            <img
                                src="<?= htmlspecialchars($item->product->getImageUrl()) ?>"
                                alt=""
                                class="w-16 h-16 object-cover rounded"
                            >
                            <div class="flex-1">
                                <p class="text-sm font-medium line-clamp-2"><?= htmlspecialchars($item->product->name) ?></p>
                                <?php if ($item->getSizeName()): ?>
                                    <p class="text-xs text-gray-500">Taille: <?= htmlspecialchars($item->getSizeName()) ?></p>
                                <?php endif; ?>
                                <p class="text-sm text-gray-600">Qté: <?= $item->quantity ?></p>
                            </div>
                            <p class="text-sm font-bold"><?= number_format($item->getTotalPrice(), 2, ',', ' ') ?> €</p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <hr class="my-4">

                <!-- Totaux -->
                <div class="space-y-2">
                    <div class="flex justify-between text-gray-600">
                        <span>Sous-total</span>
                        <span><?= number_format($subtotal, 2, ',', ' ') ?> €</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Livraison</span>
                        <?php if ($shippingCost == 0): ?>
                            <span class="text-green-600">Gratuite</span>
                        <?php else: ?>
                            <span><?= number_format($shippingCost, 2, ',', ' ') ?> €</span>
                        <?php endif; ?>
                    </div>
                    <hr>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span class="text-green-600"><?= number_format($total, 2, ',', ' ') ?> €</span>
                    </div>
                </div>

                <button
                    type="submit"
                    class="confirm-btn w-full mt-6 bg-green-600 text-white py-4 rounded-lg font-bold hover:bg-green-700 transition"
                >
                    <i class="fas fa-check mr-2"></i>
                    Confirmer la commande
                </button>

                <a href="/cart" class="block text-center text-gray-600 hover:text-gray-800 mt-4">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Retour au panier
                </a>
            </div>
        </div>
    </form>
</div>
