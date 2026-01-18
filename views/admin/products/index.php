<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-gray-500"><?= $pagination['totalItems'] ?> produits au total</p>
    </div>
    <a href="/admin/products/create" class="bg-green-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-green-700 transition">
        <i class="fas fa-plus mr-2"></i> Nouveau produit
    </a>
</div>

<!-- Filtres -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="/admin/products" method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
            <input
                type="text"
                name="search"
                value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                placeholder="Nom, SKU..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
            >
        </div>
        <div class="w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
            <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                <option value="">Toutes</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat->id ?>" <?= ($filters['category_id'] ?? '') == $cat->id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition">
            Filtrer
        </button>
        <a href="/admin/products" class="text-gray-600 hover:text-gray-800 px-4 py-2">
            Réinitialiser
        </a>
    </form>
</div>

<!-- Liste des produits -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (empty($products)): ?>
        <div class="p-8 text-center text-gray-500">
            Aucun produit trouvé.
        </div>
    <?php else: ?>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($products as $product): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img
                                    src="<?= htmlspecialchars($product->getImageUrl()) ?>"
                                    alt=""
                                    class="w-12 h-12 object-cover rounded"
                                >
                                <div>
                                    <p class="font-medium text-gray-800"><?= htmlspecialchars($product->name) ?></p>
                                    <?php if ($product->sku): ?>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($product->sku) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            <?= $product->category ? htmlspecialchars($product->category->name) : '-' ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($product->sale_price): ?>
                                <span class="font-medium text-green-600"><?= number_format($product->sale_price, 2, ',', ' ') ?> €</span>
                                <span class="text-sm text-gray-400 line-through ml-1"><?= number_format($product->price, 2, ',', ' ') ?> €</span>
                            <?php else: ?>
                                <span class="font-medium"><?= number_format($product->price, 2, ',', ' ') ?> €</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($product->stock <= 0): ?>
                                <span class="text-red-600 font-medium">Rupture</span>
                            <?php elseif ($product->stock <= 5): ?>
                                <span class="text-yellow-600 font-medium"><?= $product->stock ?></span>
                            <?php else: ?>
                                <span class="text-green-600"><?= $product->stock ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($product->is_active): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Actif</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Inactif</span>
                            <?php endif; ?>
                            <?php if ($product->is_featured): ?>
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium ml-1">Vedette</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="/admin/products/<?= $product->id ?>/edit" class="text-blue-600 hover:text-blue-700 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="/admin/products/<?= $product->id ?>/delete" method="POST" class="inline" onsubmit="return confirm('Supprimer ce produit ?')">
                                <button type="submit" class="text-red-600 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
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
                        <a href="?page=<?= $i ?>" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">
                            <?= $i ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
