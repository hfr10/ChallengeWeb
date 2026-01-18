<?php
$currentCategory = $category ?? null;
?>

<div class="flex flex-col md:flex-row gap-8">
    <!-- Sidebar Filtres -->
    <aside class="w-full md:w-64 flex-shrink-0">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="font-bold text-lg mb-4">Filtres</h2>

            <form action="<?= $currentCategory ? '/categories/' . htmlspecialchars($currentCategory->slug) : '/products' ?>" method="GET">
                <!-- Recherche -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                    <input
                        type="text"
                        name="search"
                        value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                        placeholder="Rechercher..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                </div>

                <!-- Catégories -->
                <?php if (!$currentCategory): ?>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                    <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">Toutes</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->id ?>" <?= ($filters['category_id'] ?? '') == $cat->id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <!-- Marques -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Marque</label>
                    <select name="brand" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">Toutes</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?= htmlspecialchars($brand) ?>" <?= ($filters['brand'] ?? '') == $brand ? 'selected' : '' ?>>
                                <?= htmlspecialchars($brand) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Prix -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prix</label>
                    <div class="flex gap-2">
                        <input
                            type="number"
                            name="price_min"
                            value="<?= htmlspecialchars($filters['price_min'] ?? '') ?>"
                            placeholder="Min"
                            class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                        <input
                            type="number"
                            name="price_max"
                            value="<?= htmlspecialchars($filters['price_max'] ?? '') ?>"
                            placeholder="Max"
                            class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                        >
                    </div>
                </div>

                <!-- Tri -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trier par</label>
                    <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="newest" <?= ($filters['sort'] ?? '') == 'newest' ? 'selected' : '' ?>>Plus récents</option>
                        <option value="price_asc" <?= ($filters['sort'] ?? '') == 'price_asc' ? 'selected' : '' ?>>Prix croissant</option>
                        <option value="price_desc" <?= ($filters['sort'] ?? '') == 'price_desc' ? 'selected' : '' ?>>Prix décroissant</option>
                        <option value="name" <?= ($filters['sort'] ?? '') == 'name' ? 'selected' : '' ?>>Nom A-Z</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg font-medium hover:bg-green-700 transition">
                    Appliquer les filtres
                </button>

                <a href="<?= $currentCategory ? '/categories/' . htmlspecialchars($currentCategory->slug) : '/products' ?>"
                   class="block text-center text-gray-600 hover:text-gray-800 mt-3">
                    Réinitialiser
                </a>
            </form>
        </div>
    </aside>

    <!-- Liste des produits -->
    <div class="flex-1">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">
                <?= $currentCategory ? htmlspecialchars($currentCategory->name) : 'Tous les produits' ?>
            </h1>
            <p class="text-gray-600"><?= $pagination['totalItems'] ?> produits</p>
        </div>

        <?php if (empty($products)): ?>
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-search text-5xl text-gray-300 mb-4"></i>
                <h2 class="text-xl font-semibold text-gray-600 mb-2">Aucun produit trouvé</h2>
                <p class="text-gray-500">Essayez de modifier vos critères de recherche.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                <?php foreach ($products as $product): ?>
                    <a href="/products/<?= htmlspecialchars($product->slug) ?>"
                       class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition group">
                        <div class="aspect-square bg-gray-100 relative">
                            <img
                                src="<?= htmlspecialchars($product->getImageUrl()) ?>"
                                alt="<?= htmlspecialchars($product->name) ?>"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                            >
                            <?php if ($product->isOnSale()): ?>
                                <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">
                                    -<?= $product->getDiscountPercentage() ?>%
                                </span>
                            <?php endif; ?>
                            <?php if (!$product->isInStock()): ?>
                                <span class="absolute top-2 right-2 bg-gray-800 text-white text-xs px-2 py-1 rounded">
                                    Rupture
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="p-4">
                            <p class="text-sm text-gray-500 mb-1"><?= htmlspecialchars($product->brand ?? '') ?></p>
                            <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2"><?= htmlspecialchars($product->name) ?></h3>
                            <div class="flex items-center gap-2">
                                <?php if ($product->isOnSale()): ?>
                                    <span class="text-lg font-bold text-green-600"><?= number_format($product->sale_price, 2, ',', ' ') ?> €</span>
                                    <span class="text-sm text-gray-400 line-through"><?= number_format($product->price, 2, ',', ' ') ?> €</span>
                                <?php else: ?>
                                    <span class="text-lg font-bold text-green-600"><?= number_format($product->price, 2, ',', ' ') ?> €</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($pagination['total'] > 1): ?>
                <div class="mt-8 flex justify-center gap-2">
                    <?php if ($pagination['current'] > 1): ?>
                        <a href="?page=<?= $pagination['current'] - 1 ?><?= http_build_query(array_filter($filters)) ? '&' . http_build_query(array_filter($filters)) : '' ?>"
                           class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $pagination['total']; $i++): ?>
                        <?php if ($i == $pagination['current']): ?>
                            <span class="px-4 py-2 bg-green-600 text-white rounded-lg"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?><?= http_build_query(array_filter($filters)) ? '&' . http_build_query(array_filter($filters)) : '' ?>"
                               class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                                <?= $i ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($pagination['current'] < $pagination['total']): ?>
                        <a href="?page=<?= $pagination['current'] + 1 ?><?= http_build_query(array_filter($filters)) ? '&' . http_build_query(array_filter($filters)) : '' ?>"
                           class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
