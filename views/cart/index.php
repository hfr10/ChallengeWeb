<div id="cart-app" v-cloak>
    <h1 class="text-3xl font-bold mb-8">Mon panier</h1>

    <!-- Chargement -->
    <div v-if="loading" class="text-center py-20">
        <i class="fas fa-spinner fa-spin text-4xl text-green-600"></i>
        <p class="mt-4 text-gray-600">Chargement du panier...</p>
    </div>

    <!-- Panier vide -->
    <div v-else-if="items.length === 0" class="bg-white rounded-lg shadow-md p-12 text-center">
        <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
        <h2 class="text-2xl font-semibold text-gray-600 mb-4">Votre panier est vide</h2>
        <p class="text-gray-500 mb-8">Découvrez nos produits et ajoutez-les à votre panier.</p>
        <a href="/products" class="inline-block bg-green-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-green-700 transition">
            Voir les produits
        </a>
    </div>

    <!-- Panier avec articles -->
    <div v-else class="flex flex-col lg:flex-row gap-8">
        <!-- Liste des articles -->
        <div class="flex-1">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div v-for="item in items" :key="item.id" class="flex gap-4 p-6 border-b border-gray-100 last:border-0">
                    <!-- Image -->
                    <a :href="'/products/' + item.slug" class="flex-shrink-0">
                        <img :src="item.image" :alt="item.name" class="w-24 h-24 object-cover rounded-lg">
                    </a>

                    <!-- Détails -->
                    <div class="flex-1">
                        <a :href="'/products/' + item.slug" class="font-semibold text-gray-800 hover:text-green-600">
                            {{ item.name }}
                        </a>
                        <p v-if="item.size" class="text-sm text-gray-500 mt-1">Taille: {{ item.size }}</p>
                        <p class="text-green-600 font-bold mt-2">{{ formatPrice(item.price) }}</p>
                    </div>

                    <!-- Quantité -->
                    <div class="flex items-center gap-2">
                        <button
                            @click="updateQuantity(item, item.quantity - 1)"
                            class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded hover:bg-gray-100 transition"
                        >
                            <i class="fas fa-minus text-sm"></i>
                        </button>
                        <span class="w-8 text-center">{{ item.quantity }}</span>
                        <button
                            @click="updateQuantity(item, item.quantity + 1)"
                            class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded hover:bg-gray-100 transition"
                        >
                            <i class="fas fa-plus text-sm"></i>
                        </button>
                    </div>

                    <!-- Total ligne -->
                    <div class="text-right w-24">
                        <p class="font-bold text-gray-800">{{ formatPrice(item.total) }}</p>
                    </div>

                    <!-- Supprimer -->
                    <button
                        @click="removeItem(item)"
                        class="text-gray-400 hover:text-red-500 transition"
                        title="Supprimer"
                    >
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Continuer les achats -->
            <div class="mt-6">
                <a href="/products" class="text-green-600 hover:text-green-700 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i> Continuer mes achats
                </a>
            </div>
        </div>

        <!-- Récapitulatif -->
        <div class="lg:w-80">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                <h2 class="text-xl font-bold mb-6">Récapitulatif</h2>

                <div class="space-y-4 mb-6">
                    <div class="flex justify-between text-gray-600">
                        <span>Sous-total</span>
                        <span>{{ formatPrice(subtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Livraison</span>
                        <span v-if="subtotal >= 100" class="text-green-600">Gratuite</span>
                        <span v-else>5,99 €</span>
                    </div>
                    <hr>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span class="text-green-600">{{ formatPrice(total) }}</span>
                    </div>
                </div>

                <div v-if="subtotal < 100" class="bg-green-50 text-green-700 p-4 rounded-lg mb-6 text-sm">
                    <i class="fas fa-info-circle mr-2"></i>
                    Plus que {{ formatPrice(100 - subtotal) }} pour la livraison gratuite !
                </div>

                <a
                    href="/checkout"
                    class="block w-full bg-green-600 text-white py-4 rounded-lg font-bold text-center hover:bg-green-700 transition"
                >
                    Passer commande
                </a>

                <p class="text-center text-sm text-gray-500 mt-4">
                    <i class="fas fa-lock mr-1"></i> Paiement sécurisé
                </p>
            </div>
        </div>
    </div>
</div>

<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            items: [],
            subtotal: 0,
            loading: true
        };
    },
    computed: {
        total() {
            const shipping = this.subtotal >= 100 ? 0 : 5.99;
            return this.subtotal + shipping;
        }
    },
    mounted() {
        this.loadCart();
    },
    methods: {
        async loadCart() {
            try {
                const response = await fetch('/api/cart');
                const data = await response.json();
                this.items = data.items;
                this.subtotal = data.subtotal;
            } catch (error) {
                console.error('Erreur chargement panier:', error);
            } finally {
                this.loading = false;
            }
        },

        async updateQuantity(item, newQuantity) {
            if (newQuantity < 1) {
                return this.removeItem(item);
            }

            try {
                const response = await fetch('/api/cart/update', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        item_id: item.id,
                        quantity: newQuantity
                    })
                });

                const data = await response.json();
                if (data.success) {
                    item.quantity = newQuantity;
                    item.total = item.price * newQuantity;
                    this.subtotal = data.subtotal;
                    this.updateHeaderCart(data.count);
                }
            } catch (error) {
                console.error('Erreur mise à jour quantité:', error);
            }
        },

        async removeItem(item) {
            if (!confirm('Supprimer cet article du panier ?')) return;

            try {
                const response = await fetch('/api/cart/remove', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ item_id: item.id })
                });

                const data = await response.json();
                if (data.success) {
                    this.items = this.items.filter(i => i.id !== item.id);
                    this.subtotal = data.subtotal;
                    this.updateHeaderCart(data.count);
                }
            } catch (error) {
                console.error('Erreur suppression article:', error);
            }
        },

        updateHeaderCart(count) {
            const countEl = document.getElementById('cart-count');
            if (countEl) {
                if (count > 0) {
                    countEl.textContent = count;
                    countEl.classList.remove('hidden');
                } else {
                    countEl.classList.add('hidden');
                }
            }
        },

        formatPrice(price) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR'
            }).format(price);
        }
    }
}).mount('#cart-app');
</script>
