<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Football Shop' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        [v-cloak] { display: none; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-green-600 text-white shadow-lg">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <a href="/" class="text-2xl font-bold flex items-center gap-2">
                    <i class="fas fa-futbol"></i>
                    Football Shop
                </a>

                <!-- Navigation principale -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="/products" class="hover:text-green-200 transition">Produits</a>
                    <a href="/categories/maillots" class="hover:text-green-200 transition">Maillots</a>
                    <a href="/categories/chaussures" class="hover:text-green-200 transition">Chaussures</a>
                    <a href="/categories/equipements" class="hover:text-green-200 transition">Équipements</a>
                </div>

                <!-- Actions utilisateur -->
                <div class="flex items-center gap-4">
                    <!-- Panier -->
                    <a href="/cart" class="relative hover:text-green-200 transition" id="cart-icon">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                    </a>

                    <?php if (isset($_SESSION['user'])): ?>
                        <!-- Utilisateur connecté -->
                        <div class="relative group">
                            <button class="flex items-center gap-2 hover:text-green-200 transition py-2">
                                <i class="fas fa-user"></i>
                                <span><?= htmlspecialchars($_SESSION['user']['first_name'] ?? 'Mon compte') ?></span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div class="absolute right-0 top-full w-48 bg-white rounded-lg shadow-lg py-2 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                                <a href="/orders" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Mes commandes</a>
                                <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
                                    <a href="/admin" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Administration</a>
                                <?php endif; ?>
                                <hr class="my-2">
                                <a href="/logout" class="block px-4 py-2 text-red-600 hover:bg-gray-100">Déconnexion</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Utilisateur non connecté -->
                        <a href="/login" class="hover:text-green-200 transition">
                            <i class="fas fa-sign-in-alt"></i>
                            Connexion
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <!-- Messages flash -->
    <?php
    $flashMessages = \App\Core\Session::getAllFlash();
    if (!empty($flashMessages)):
    ?>
    <div class="container mx-auto px-4 mt-4">
        <?php foreach ($flashMessages as $type => $message): ?>
            <?php
            $bgColor = match($type) {
                'success' => 'bg-green-100 border-green-500 text-green-700',
                'error' => 'bg-red-100 border-red-500 text-red-700',
                'warning' => 'bg-yellow-100 border-yellow-500 text-yellow-700',
                default => 'bg-blue-100 border-blue-500 text-blue-700',
            };
            ?>
            <div class="<?= $bgColor ?> border-l-4 p-4 mb-4 rounded" role="alert">
                <p><?= htmlspecialchars($message) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Contenu principal -->
    <main class="flex-grow container mx-auto px-4 py-8">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-auto">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="font-bold text-lg mb-4">Football Shop</h3>
                    <p class="text-gray-400">Votre boutique en ligne de produits de football.</p>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">Catégories</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/categories/maillots" class="hover:text-white">Maillots</a></li>
                        <li><a href="/categories/chaussures" class="hover:text-white">Chaussures</a></li>
                        <li><a href="/categories/equipements" class="hover:text-white">Équipements</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">Mon compte</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/login" class="hover:text-white">Connexion</a></li>
                        <li><a href="/register" class="hover:text-white">Inscription</a></li>
                        <li><a href="/orders" class="hover:text-white">Mes commandes</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">Contact</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><i class="fas fa-envelope mr-2"></i>contact@footballshop.fr</li>
                        <li><i class="fas fa-phone mr-2"></i>01 23 45 67 89</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?= date('Y') ?> Football Shop - Projet B2 Dev Web</p>
            </div>
        </div>
    </footer>

    <script>
        // Charger le nombre d'articles dans le panier
        async function updateCartCount() {
            try {
                const response = await fetch('/api/cart/count');
                const data = await response.json();
                const countEl = document.getElementById('cart-count');
                if (data.count > 0) {
                    countEl.textContent = data.count;
                    countEl.classList.remove('hidden');
                } else {
                    countEl.classList.add('hidden');
                }
            } catch (e) {
                console.error('Erreur chargement panier:', e);
            }
        }
        updateCartCount();
    </script>
</body>
</html>
