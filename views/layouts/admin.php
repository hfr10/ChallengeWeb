<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Administration' ?> - Football Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 min-h-screen fixed">
            <div class="p-4">
                <a href="/admin" class="text-white text-xl font-bold flex items-center gap-2">
                    <i class="fas fa-futbol"></i>
                    Admin Panel
                </a>
            </div>

            <nav class="mt-8">
                <a href="/admin" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition <?= ($_SERVER['REQUEST_URI'] === '/admin') ? 'bg-gray-700 text-white' : '' ?>">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    Dashboard
                </a>
                <a href="/admin/products" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/products') ? 'bg-gray-700 text-white' : '' ?>">
                    <i class="fas fa-box w-5"></i>
                    Produits
                </a>
                <a href="/admin/categories" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/categories') ? 'bg-gray-700 text-white' : '' ?>">
                    <i class="fas fa-tags w-5"></i>
                    Catégories
                </a>
                <a href="/admin/orders" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/orders') ? 'bg-gray-700 text-white' : '' ?>">
                    <i class="fas fa-shopping-bag w-5"></i>
                    Commandes
                </a>
                <a href="/admin/users" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/users') ? 'bg-gray-700 text-white' : '' ?>">
                    <i class="fas fa-users w-5"></i>
                    Utilisateurs
                </a>

                <hr class="border-gray-700 my-4">

                <a href="/" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition">
                    <i class="fas fa-store w-5"></i>
                    Voir le site
                </a>
                <a href="/logout" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-gray-700 hover:text-red-300 transition">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    Déconnexion
                </a>
            </nav>
        </aside>

        <!-- Main content -->
        <div class="flex-1 ml-64">
            <!-- Top bar -->
            <header class="bg-white shadow-sm px-6 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800"><?= $title ?? 'Dashboard' ?></h1>
                    <div class="flex items-center gap-4">
                        <span class="text-gray-600">
                            <i class="fas fa-user mr-2"></i>
                            <?= htmlspecialchars($_SESSION['user']['first_name'] ?? 'Admin') ?>
                        </span>
                    </div>
                </div>
            </header>

            <!-- Messages flash -->
            <?php
            $flashMessages = \App\Core\Session::getAllFlash();
            if (!empty($flashMessages)):
            ?>
            <div class="px-6 pt-4">
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

            <!-- Page content -->
            <main class="p-6">
                <?= $content ?>
            </main>
        </div>
    </div>
</body>
</html>
