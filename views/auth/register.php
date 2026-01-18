<?php
$old = \App\Core\Session::getFlash('old', []);
$errors = \App\Core\Session::getFlash('errors', []);
?>

<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold text-center mb-6">Créer un compte</h1>

        <form action="/register" method="POST" class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Prénom
                    </label>
                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        value="<?= htmlspecialchars($old['first_name'] ?? '') ?>"
                        required
                        class="w-full px-4 py-2 border <?= isset($errors['first_name']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <?php if (isset($errors['first_name'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['first_name']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nom
                    </label>
                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        value="<?= htmlspecialchars($old['last_name'] ?? '') ?>"
                        required
                        class="w-full px-4 py-2 border <?= isset($errors['last_name']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <?php if (isset($errors['last_name'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['last_name']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Adresse email
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    required
                    class="w-full px-4 py-2 border <?= isset($errors['email']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="votre@email.com"
                >
                <?php if (isset($errors['email'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Mot de passe
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full px-4 py-2 border <?= isset($errors['password']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="Minimum 8 caractères"
                >
                <?php if (isset($errors['password'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['password']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-1">
                    Confirmer le mot de passe
                </label>
                <input
                    type="password"
                    id="password_confirm"
                    name="password_confirm"
                    required
                    class="w-full px-4 py-2 border <?= isset($errors['password_confirm']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
                <?php if (isset($errors['password_confirm'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['password_confirm']) ?></p>
                <?php endif; ?>
            </div>

            <button
                type="submit"
                class="w-full bg-green-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-green-700 transition"
            >
                Créer mon compte
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600">
                Déjà un compte ?
                <a href="/login" class="text-green-600 hover:text-green-700 font-medium">
                    Se connecter
                </a>
            </p>
        </div>
    </div>
</div>
