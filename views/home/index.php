<!-- Hero Section -->
<section class="hero-section bg-gradient-to-r from-green-600 to-green-800 text-white rounded-xl p-12 mb-12">
    <div class="max-w-3xl">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">
            Équipez-vous comme un pro
        </h1>
        <p class="text-xl text-green-100 mb-8">
            Découvrez notre sélection de maillots, chaussures et équipements de football des plus grandes marques.
        </p>
        <a href="/products" class="inline-block bg-white text-green-700 px-8 py-3 rounded-lg font-bold hover:bg-green-100 transition">
            Voir tous les produits
        </a>
    </div>
</section>

<!-- Catégories -->
<section class="mb-12">
    <h2 class="section-title text-2xl font-bold mb-6">Nos catégories</h2>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <?php foreach ($categories as $category): ?>
            <a href="/categories/<?= htmlspecialchars($category->slug) ?>"
               class="category-card bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition group">
                <div class="category-icon w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center group-hover:bg-green-200 transition">
                    <?php
                    $icon = match($category->slug) {
                        'maillots' => 'fa-shirt',
                        'chaussures' => 'fa-shoe-prints',
                        'equipements' => 'fa-bag-shopping',
                        'ballons' => 'fa-futbol',
                        'gardien' => 'fa-hand',
                        default => 'fa-tag',
                    };
                    ?>
                    <i class="fas <?= $icon ?> text-2xl text-green-600"></i>
                </div>
                <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($category->name) ?></h3>
                <p class="text-sm text-gray-500"><?= $category->product_count ?> produits</p>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Produits vedettes -->
<section class="mb-12">
    <div class="flex justify-between items-center mb-6">
        <h2 class="section-title text-2xl font-bold">Produits vedettes</h2>
        <a href="/products" class="text-green-600 hover:text-green-700 font-medium">
            Voir tout <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <?php foreach ($featuredProducts as $product): ?>
            <a href="/products/<?= htmlspecialchars($product->slug) ?>"
               class="product-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition group">
                <div class="product-image aspect-square bg-gray-100 relative">
                    <img
                        src="<?= htmlspecialchars($product->getImageUrl()) ?>"
                        alt="<?= htmlspecialchars($product->name) ?>"
                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                    >
                    <?php if ($product->isOnSale()): ?>
                        <span class="sale-badge absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">
                            -<?= $product->getDiscountPercentage() ?>%
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
</section>

<!-- Bannière promotionnelle -->
<section class="promo-banner bg-gray-800 text-white rounded-xl p-8 mb-12 flex flex-col md:flex-row items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold mb-2">Livraison gratuite</h2>
        <p class="text-gray-300">Sur toutes les commandes de plus de 100€</p>
    </div>
    <div class="mt-4 md:mt-0">
        <i class="truck-icon fas fa-truck text-5xl text-green-400"></i>
    </div>
</section>

<!-- Derniers produits -->
<section>
    <div class="flex justify-between items-center mb-6">
        <h2 class="section-title text-2xl font-bold">Nouveautés</h2>
        <a href="/products?sort=newest" class="text-green-600 hover:text-green-700 font-medium">
            Voir tout <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <?php foreach ($latestProducts as $product): ?>
            <a href="/products/<?= htmlspecialchars($product->slug) ?>"
               class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition group">
                <div class="aspect-square bg-gray-100 relative">
                    <img
                        src="<?= htmlspecialchars($product->getImageUrl()) ?>"
                        alt="<?= htmlspecialchars($product->name) ?>"
                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                    >
                    <span class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded">
                        Nouveau
                    </span>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-500 mb-1"><?= htmlspecialchars($product->brand ?? '') ?></p>
                    <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2"><?= htmlspecialchars($product->name) ?></h3>
                    <span class="text-lg font-bold text-green-600"><?= number_format($product->getEffectivePrice(), 2, ',', ' ') ?> €</span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>
