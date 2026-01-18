<!-- Statistiques -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Chiffre d'affaires</p>
                <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['total_revenue'], 2, ',', ' ') ?> €</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-euro-sign text-green-600 text-xl"></i>
            </div>
        </div>
        <p class="text-sm text-green-600 mt-2">
            <i class="fas fa-arrow-up"></i>
            <?= number_format($stats['today_revenue'], 2, ',', ' ') ?> € aujourd'hui
        </p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Commandes</p>
                <p class="text-2xl font-bold text-gray-800"><?= $stats['total_orders'] ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-shopping-bag text-blue-600 text-xl"></i>
            </div>
        </div>
        <p class="text-sm text-blue-600 mt-2">
            <?= $stats['today_orders'] ?> aujourd'hui
        </p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Produits</p>
                <p class="text-2xl font-bold text-gray-800"><?= $stats['total_products'] ?></p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-box text-purple-600 text-xl"></i>
            </div>
        </div>
        <a href="/admin/products" class="text-sm text-purple-600 mt-2 inline-block hover:underline">
            Gérer les produits
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Utilisateurs</p>
                <p class="text-2xl font-bold text-gray-800"><?= $stats['total_users'] ?></p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-yellow-600 text-xl"></i>
            </div>
        </div>
        <a href="/admin/users" class="text-sm text-yellow-600 mt-2 inline-block hover:underline">
            Voir tous
        </a>
    </div>
</div>

<!-- Alertes -->
<?php if ($stats['pending_orders'] > 0): ?>
<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8 rounded-r-lg">
    <div class="flex items-center">
        <i class="fas fa-exclamation-triangle text-yellow-400 text-xl mr-3"></i>
        <p class="text-yellow-700">
            <strong><?= $stats['pending_orders'] ?> commande(s)</strong> en attente de traitement.
            <a href="/admin/orders?status=pending" class="underline hover:no-underline">Voir les commandes</a>
        </p>
    </div>
</div>
<?php endif; ?>

<!-- Dernières commandes -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h2 class="text-xl font-bold">Dernières commandes</h2>
        <a href="/admin/orders" class="text-green-600 hover:text-green-700 font-medium">
            Voir toutes <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    <?php if (empty($recentOrders)): ?>
        <div class="p-6 text-center text-gray-500">
            Aucune commande pour le moment.
        </div>
    <?php else: ?>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commande</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($recentOrders as $order): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="/admin/orders/<?= $order->id ?>" class="font-medium text-green-600 hover:text-green-700">
                                #<?= htmlspecialchars($order->order_number) ?>
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($order->user): ?>
                                <?= htmlspecialchars($order->user->first_name . ' ' . $order->user->last_name) ?>
                            <?php else: ?>
                                <span class="text-gray-400">Invité</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                            $statusColor = $order->getStatusColor();
                            $bgClass = match($statusColor) {
                                'green' => 'bg-green-100 text-green-800',
                                'yellow' => 'bg-yellow-100 text-yellow-800',
                                'blue' => 'bg-blue-100 text-blue-800',
                                'red' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                            ?>
                            <span class="px-2 py-1 rounded-full text-xs font-medium <?= $bgClass ?>">
                                <?= htmlspecialchars($order->getStatusLabel()) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium">
                            <?= number_format($order->total, 2, ',', ' ') ?> €
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-sm">
                            <?= date('d/m/Y H:i', strtotime($order->created_at)) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
