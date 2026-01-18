<h1 class="text-3xl font-bold mb-8">Mes commandes</h1>

<?php if (empty($orders)): ?>
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <i class="fas fa-shopping-bag text-6xl text-gray-300 mb-4"></i>
        <h2 class="text-2xl font-semibold text-gray-600 mb-4">Aucune commande</h2>
        <p class="text-gray-500 mb-8">Vous n'avez pas encore passé de commande.</p>
        <a href="/products" class="inline-block bg-green-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-green-700 transition">
            Voir les produits
        </a>
    </div>
<?php else: ?>
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Commande</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Date</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Statut</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Total</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($orders as $order): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="font-medium">#<?= htmlspecialchars($order->order_number) ?></span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            <?= date('d/m/Y H:i', strtotime($order->created_at)) ?>
                        </td>
                        <td class="px-6 py-4">
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
                            <span class="px-3 py-1 rounded-full text-sm font-medium <?= $bgClass ?>">
                                <?= htmlspecialchars($order->getStatusLabel()) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold text-green-600">
                            <?= number_format($order->total, 2, ',', ' ') ?> €
                        </td>
                        <td class="px-6 py-4">
                            <a href="/orders/<?= $order->id ?>" class="text-green-600 hover:text-green-700 font-medium">
                                Voir détails
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
