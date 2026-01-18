<div class="max-w-2xl">
    <a href="/admin/users" class="text-gray-500 hover:text-gray-700 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i> Retour aux utilisateurs
    </a>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                <span class="text-green-600 font-bold text-2xl">
                    <?= strtoupper(substr($user->first_name ?? 'U', 0, 1)) ?>
                </span>
            </div>
            <div>
                <h2 class="text-2xl font-bold"><?= htmlspecialchars($user->getFullName() ?: 'Sans nom') ?></h2>
                <p class="text-gray-500"><?= htmlspecialchars($user->email) ?></p>
            </div>
            <?php if ($user->role === 'admin'): ?>
                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium ml-auto">Admin</span>
            <?php else: ?>
                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-medium ml-auto">Client</span>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">Téléphone</p>
                <p class="font-medium"><?= htmlspecialchars($user->phone ?: '-') ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Inscrit le</p>
                <p class="font-medium"><?= date('d/m/Y à H:i', strtotime($user->created_at)) ?></p>
            </div>
            <div class="col-span-2">
                <p class="text-sm text-gray-500">Adresse</p>
                <p class="font-medium">
                    <?php if ($user->address): ?>
                        <?= htmlspecialchars($user->address) ?><br>
                        <?= htmlspecialchars($user->postal_code . ' ' . $user->city) ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Modifier le rôle -->
    <?php $currentUser = $_SESSION['user'] ?? []; ?>
    <?php if (($currentUser['id'] ?? 0) !== $user->id): ?>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="font-bold mb-4">Modifier le rôle</h3>
            <form action="/admin/users/<?= $user->id ?>/role" method="POST" class="flex gap-4">
                <select name="role" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="customer" <?= $user->role === 'customer' ? 'selected' : '' ?>>Client</option>
                    <option value="admin" <?= $user->role === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                </select>
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-green-700 transition">
                    Mettre à jour
                </button>
            </form>
        </div>
    <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <i class="fas fa-info-circle mr-2"></i>
                Vous ne pouvez pas modifier votre propre rôle.
            </p>
        </div>
    <?php endif; ?>
</div>
