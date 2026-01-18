<div id="product-page" class="max-w-6xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-500 mb-6">
        <a href="/" class="hover:text-green-600">Accueil</a>
        <span class="mx-2">/</span>
        <a href="/products" class="hover:text-green-600">Produits</a>
        <?php if ($product->category): ?>
            <span class="mx-2">/</span>
            <a href="/categories/<?= htmlspecialchars($product->category->slug) ?>" class="hover:text-green-600">
                <?= htmlspecialchars($product->category->name) ?>
            </a>
        <?php endif; ?>
        <span class="mx-2">/</span>
        <span class="text-gray-800"><?= htmlspecialchars($product->name) ?></span>
    </nav>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="md:flex">
            <!-- Image produit -->
            <div class="md:w-1/2 p-8">
                <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden relative">
                    <img
                        src="<?= htmlspecialchars($product->getImageUrl()) ?>"
                        alt="<?= htmlspecialchars($product->name) ?>"
                        class="w-full h-full object-cover"
                    >
                    <?php if ($product->isOnSale()): ?>
                        <span class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-lg font-medium">
                            -<?= $product->getDiscountPercentage() ?>%
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Informations produit -->
            <div class="md:w-1/2 p-8">
                <?php if ($product->brand): ?>
                    <p class="text-gray-500 mb-2"><?= htmlspecialchars($product->brand) ?></p>
                <?php endif; ?>

                <h1 class="text-3xl font-bold text-gray-800 mb-4"><?= htmlspecialchars($product->name) ?></h1>

                <!-- Prix -->
                <div class="mb-6">
                    <?php if ($product->isOnSale()): ?>
                        <div class="flex items-center gap-3">
                            <span class="text-3xl font-bold text-green-600">
                                <?= number_format($product->sale_price, 2, ',', ' ') ?> €
                            </span>
                            <span class="text-xl text-gray-400 line-through">
                                <?= number_format($product->price, 2, ',', ' ') ?> €
                            </span>
                        </div>
                    <?php else: ?>
                        <span class="text-3xl font-bold text-green-600">
                            <?= number_format($product->price, 2, ',', ' ') ?> €
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Description courte -->
                <?php if ($product->short_description): ?>
                    <p class="text-gray-600 mb-6"><?= htmlspecialchars($product->short_description) ?></p>
                <?php endif; ?>

                <!-- Application Vue.js pour ajouter au panier -->
                <div id="add-to-cart-app" v-cloak>
                    <!-- Sélection taille -->
                    <?php if (!empty($product->sizes)): ?>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Taille</label>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($product->sizes as $size): ?>
                                    <button
                                        type="button"
                                        @click="selectedSize = <?= $size['id'] ?>"
                                        :class="selectedSize === <?= $size['id'] ?>
                                            ? 'bg-green-600 text-white border-green-600'
                                            : 'bg-white text-gray-800 border-gray-300 hover:border-green-600'"
                                        class="px-4 py-2 border rounded-lg font-medium transition <?= $size['stock'] <= 0 ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                        <?= $size['stock'] <= 0 ? 'disabled' : '' ?>
                                    >
                                        <?= htmlspecialchars($size['size']) ?>
                                        <?php if ($size['stock'] <= 0): ?>
                                            <span class="text-xs">(épuisé)</span>
                                        <?php endif; ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Quantité -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantité</label>
                        <div class="flex items-center gap-3">
                            <button
                                @click="quantity > 1 && quantity--"
                                class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-lg hover:bg-gray-100 transition"
                            >
                                <i class="fas fa-minus"></i>
                            </button>
                            <input
                                type="number"
                                v-model.number="quantity"
                                min="1"
                                max="10"
                                class="w-20 text-center px-3 py-2 border border-gray-300 rounded-lg"
                            >
                            <button
                                @click="quantity < 10 && quantity++"
                                class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-lg hover:bg-gray-100 transition"
                            >
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Bouton ajouter au panier -->
                    <?php if ($product->isInStock()): ?>
                        <button
                            @click="addToCart"
                            :disabled="loading <?= !empty($product->sizes) ? '|| !selectedSize' : '' ?>"
                            class="w-full bg-green-600 text-white py-4 rounded-lg font-bold text-lg hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <span v-if="loading">
                                <i class="fas fa-spinner fa-spin"></i> Ajout en cours...
                            </span>
                            <span v-else>
                                <i class="fas fa-shopping-cart"></i> Ajouter au panier
                            </span>
                        </button>
                    <?php else: ?>
                        <button disabled class="w-full bg-gray-400 text-white py-4 rounded-lg font-bold text-lg cursor-not-allowed">
                            <i class="fas fa-times"></i> Rupture de stock
                        </button>
                    <?php endif; ?>

                    <!-- Message de confirmation -->
                    <div v-if="message" :class="messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="mt-4 p-4 rounded-lg">
                        {{ message }}
                    </div>
                </div>

                <!-- Informations supplémentaires -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <div class="flex items-center gap-4 text-gray-600">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-truck"></i>
                            <span>Livraison gratuite dès 100€</span>
                        </div>
                    </div>
                    <?php if ($product->sku): ?>
                        <p class="text-sm text-gray-400 mt-4">Réf: <?= htmlspecialchars($product->sku) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Description complète -->
        <?php if ($product->description): ?>
            <div class="p-8 border-t border-gray-200">
                <h2 class="text-xl font-bold mb-4">Description</h2>
                <div class="text-gray-600 prose max-w-none">
                    <?= nl2br(htmlspecialchars($product->description)) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Produits similaires -->
    <?php if (!empty($relatedProducts)): ?>
        <section class="mt-12">
            <h2 class="text-2xl font-bold mb-6">Produits similaires</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <?php foreach ($relatedProducts as $related): ?>
                    <a href="/products/<?= htmlspecialchars($related->slug) ?>"
                       class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition group">
                        <div class="aspect-square bg-gray-100">
                            <img
                                src="<?= htmlspecialchars($related->getImageUrl()) ?>"
                                alt="<?= htmlspecialchars($related->name) ?>"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                            >
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2"><?= htmlspecialchars($related->name) ?></h3>
                            <span class="text-lg font-bold text-green-600"><?= number_format($related->getEffectivePrice(), 2, ',', ' ') ?> €</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            productId: <?= $product->id ?>,
            selectedSize: null,
            quantity: 1,
            loading: false,
            message: '',
            messageType: ''
        };
    },
    methods: {
        async addToCart() {
            this.loading = true;
            this.message = '';

            try {
                const response = await fetch('/api/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: this.productId,
                        size_id: this.selectedSize,
                        quantity: this.quantity
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.message = 'Produit ajouté au panier !';
                    this.messageType = 'success';
                    // Mettre à jour le compteur du panier
                    if (typeof updateCartCount === 'function') {
                        updateCartCount();
                    }
                } else {
                    this.message = data.error || 'Erreur lors de l\'ajout au panier';
                    this.messageType = 'error';
                }
            } catch (error) {
                this.message = 'Erreur de connexion';
                this.messageType = 'error';
            } finally {
                this.loading = false;
            }
        }
    }
}).mount('#add-to-cart-app');
</script>
