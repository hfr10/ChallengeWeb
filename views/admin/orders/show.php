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

<div class="max-w-4xl">
    <a href="/admin/orders" class="text-gray-500 hover:text-gray-700 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i> Retour aux commandes
    </a>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl text-gray-500"><?= date('d/m/Y à H:i', strtotime($order->created_at)) ?></h2>
        </div>
        <span class="px-4 py-2 rounded-full text-lg font-medium <?= $bgClass ?>">
            <?= htmlspecialchars($order->getStatusLabel()) ?>
        </span>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <!-- Infos commande -->
        <div class="md:col-span-2 space-y-6">
            <!-- Articles -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="font-bold">Articles commandés</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php foreach ($order->items as $item): ?>
                        <div class="flex gap-4 p-4">
                            <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center">
                                <i class="fas fa-box text-gray-400"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium"><?= htmlspecialchars($item->product_name) ?></p>
                                <?php if ($item->size): ?>
                                    <p class="text-sm text-gray-500">Taille: <?= htmlspecialchars($item->size) ?></p>
                                <?php endif; ?>
                                <p class="text-sm text-gray-500">Qté: <?= $item->quantity ?> × <?= number_format($item->unit_price, 2, ',', ' ') ?> €</p>
                            </div>
                            <p class="font-bold"><?= number_format($item->total_price, 2, ',', ' ') ?> €</p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="bg-gray-50 p-4 space-y-2">
                    <div class="flex justify-between">
                        <span>Sous-total</span>
                        <span><?= number_format($order->subtotal, 2, ',', ' ') ?> €</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Livraison</span>
                        <span><?= $order->shipping_cost == 0 ? 'Gratuite' : number_format($order->shipping_cost, 2, ',', ' ') . ' €' ?></span>
                    </div>
                    <hr>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span class="text-green-600"><?= number_format($order->total, 2, ',', ' ') ?> €</span>
                    </div>
                </div>
            </div>

            <!-- Adresse livraison -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-bold mb-4">
                    <i class="fas fa-truck mr-2 text-green-600"></i>
                    Adresse de livraison
                </h3>
                <address class="not-italic text-gray-600">
                    <?= htmlspecialchars($order->shipping_first_name . ' ' . $order->shipping_last_name) ?><br>
                    <?= htmlspecialchars($order->shipping_address) ?><br>
                    <?= htmlspecialchars($order->shipping_postal_code . ' ' . $order->shipping_city) ?><br>
                    <i class="fas fa-phone text-sm mr-1"></i><?= htmlspecialchars($order->shipping_phone) ?>
                </address>
            </div>
        </div>

        <!-- Actions -->
        <div class="space-y-6">
            <!-- Modifier le statut -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-bold mb-4">Modifier le statut</h3>
                <form action="/admin/orders/<?= $order->id ?>/status" method="POST">
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-4 focus:ring-2 focus:ring-green-500">
                        <?php foreach (\App\Models\Order::STATUS_LABELS as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $order->status === $value ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg font-medium hover:bg-green-700 transition">
                        Mettre à jour
                    </button>
                </form>
            </div>

            <!-- Infos client -->
            <?php if ($order->user): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-bold mb-4">
                        <i class="fas fa-user mr-2 text-green-600"></i>
                        Client
                    </h3>
                    <p class="font-medium"><?= htmlspecialchars($order->user->first_name . ' ' . $order->user->last_name) ?></p>
                    <p class="text-gray-600 text-sm"><?= htmlspecialchars($order->user->email) ?></p>
                    <a href="/admin/users/<?= $order->user_id ?>" class="text-green-600 hover:underline text-sm mt-2 inline-block">
                        Voir le profil
                    </a>
                </div>
            <?php endif; ?>

            <!-- Notes -->
            <?php if ($order->notes): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-bold mb-4">
                        <i class="fas fa-comment mr-2 text-green-600"></i>
                        Notes du client
                    </h3>
                    <p class="text-gray-600"><?= nl2br(htmlspecialchars($order->notes)) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
