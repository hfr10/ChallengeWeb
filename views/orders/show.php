<?php
$statusColor = $order->getStatusColor();
$bgClass = match($statusColor) {
    'green' => 'bg-green-100 text-green-800',
    'yellow' => 'bg-yellow-100 text-yellow-800',
    'blue' => 'bg-blue-100 text-blue-800',
    'red' => 'bg-red-100 text-red-800',
    'purple' => 'bg-purple-100 text-purple-800',
    'indigo' => 'bg-indigo-100 text-indigo-800',
    default => 'bg-gray-100 text-gray-800',
};
?>

<div class="max-w-4xl mx-auto">
    <div class="order-header">
        <a href="/orders" class="text-gray-500 hover:text-gray-700 mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Retour aux commandes
        </a>
        <div class="flex items-center justify-between">
            <h1 class="order-title text-3xl font-bold">Commande #<?= htmlspecialchars($order->order_number) ?></h1>
            <span class="order-status <?= $bgClass ?> px-4 py-2 rounded-full font-semibold">
                <?= htmlspecialchars($order->getStatusLabel()) ?>
            </span>
        </div>
        <p class="text-gray-500 mt-2">Passée le <?= date('d/m/Y à H:i', strtotime($order->created_at)) ?></p>
    </div>

    <div class="grid md:grid-cols-3 gap-8">
        <!-- Articles -->
        <div class="md:col-span-2">
            <div class="order-articles bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold">Articles commandés</h2>
                </div>

                <div class="divide-y divide-gray-100">
                    <?php foreach ($order->items as $item): ?>
                        <div class="order-article-item flex gap-4 p-6">
                            <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-box text-2xl text-gray-400"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold"><?= htmlspecialchars($item->product_name) ?></h3>
                                <?php if ($item->size): ?>
                                    <p class="text-sm text-gray-500">Taille: <?= htmlspecialchars($item->size) ?></p>
                                <?php endif; ?>
                                <p class="text-sm text-gray-500">Quantité: <?= $item->quantity ?></p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold"><?= number_format($item->total_price, 2, ',', ' ') ?> €</p>
                                <p class="text-sm text-gray-500"><?= number_format($item->unit_price, 2, ',', ' ') ?> € / unité</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Totaux -->
                <div class="order-totals p-6 space-y-2">
                    <div class="flex justify-between text-gray-600">
                        <span>Sous-total</span>
                        <span><?= number_format($order->subtotal, 2, ',', ' ') ?> €</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Livraison</span>
                        <?php if ($order->shipping_cost == 0): ?>
                            <span class="text-green-600">Gratuite</span>
                        <?php else: ?>
                            <span><?= number_format($order->shipping_cost, 2, ',', ' ') ?> €</span>
                        <?php endif; ?>
                    </div>
                    <div class="order-total-final flex justify-between pt-3">
                        <span>Total</span>
                        <span><?= number_format($order->total, 2, ',', ' ') ?> €</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations -->
        <div class="space-y-6">
            <!-- Adresse de livraison -->
            <div class="order-info-card bg-white rounded-lg shadow-md p-6">
                <h3 class="font-bold mb-4">
                    <i class="fas fa-truck mr-2 text-green-600"></i>
                    Adresse de livraison
                </h3>
                <address class="text-gray-600 not-italic">
                    <?= htmlspecialchars($order->shipping_first_name . ' ' . $order->shipping_last_name) ?><br>
                    <?= htmlspecialchars($order->shipping_address) ?><br>
                    <?= htmlspecialchars($order->shipping_postal_code . ' ' . $order->shipping_city) ?><br>
                    <i class="fas fa-phone text-sm mr-1"></i><?= htmlspecialchars($order->shipping_phone) ?>
                </address>
            </div>

            <!-- Paiement -->
            <div class="order-info-card bg-white rounded-lg shadow-md p-6">
                <h3 class="font-bold mb-4">
                    <i class="fas fa-credit-card mr-2 text-green-600"></i>
                    Paiement
                </h3>
                <p class="text-gray-600">
                    Statut: <span class="order-payment-status">
                        <?= $order->payment_status === 'paid' ? 'Payé' : 'En attente' ?>
                    </span>
                </p>
                <p class="text-gray-600">
                    Mode: <?= ucfirst($order->payment_method ?? 'Carte bancaire') ?>
                </p>
            </div>

            <!-- Notes -->
            <?php if ($order->notes): ?>
                <div class="order-info-card bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-bold mb-4">
                        <i class="fas fa-comment mr-2 text-green-600"></i>
                        Notes
                    </h3>
                    <p class="text-gray-600"><?= nl2br(htmlspecialchars($order->notes)) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
