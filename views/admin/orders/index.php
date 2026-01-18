<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-gray-500"><?= $pagination['totalItems'] ?> commandes</p>
    </div>
</div>

<!-- Filtres -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="/admin/orders" method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
            <input
                type="text"
                name="search"
                value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                placeholder="N° commande, email..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
            >
        </div>
        <div class="w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                <option value="">Tous</option>
                <?php foreach (\App\Models\Order::STATUS_LABELS as $value => $label): ?>
                    <option value="<?= $value ?>" <?= ($filters['status'] ?? '') == $value ? 'selected' : '' ?>>
                        <?= htmlspecialchars($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition">
            Filtrer
        </button>
        <a href="/admin/orders" class="text-gray-600 hover:text-gray-800 px-4 py-2">
            Réinitialiser
        </a>
    </form>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (empty($orders)): ?>
        <div class="p-8 text-center text-gray-500">
            Aucune commande trouvée.
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
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($orders as $order): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="/admin/orders/<?= $order->id ?>" class="font-medium text-green-600 hover:text-green-700">
                                #<?= htmlspecialchars($order->order_number) ?>
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($order->user): ?>
                                <p class="font-medium"><?= htmlspecialchars($order->user->first_name . ' ' . $order->user->last_name) ?></p>
                                <p class="text-sm text-gray-500"><?= htmlspecialchars($order->user->email) ?></p>
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
                                'purple' => 'bg-purple-100 text-purple-800',
                                'indigo' => 'bg-indigo-100 text-indigo-800',
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
                        <td class="px-6 py-4 text-right">
                            <a href="/admin/orders/<?= $order->id ?>" class="text-blue-600 hover:text-blue-700">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($pagination['total'] > 1): ?>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-center gap-2">
                <?php for ($i = 1; $i <= $pagination['total']; $i++): ?>
                    <?php if ($i == $pagination['current']): ?>
                        <span class="px-3 py-1 bg-green-600 text-white rounded"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?><?= !empty($filters['status']) ? '&status=' . $filters['status'] : '' ?>" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">
                            <?= $i ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
