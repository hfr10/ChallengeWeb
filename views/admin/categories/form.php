<?php
$isEdit = $category !== null;
$old = \App\Core\Session::getFlash('old', []);
$errors = \App\Core\Session::getFlash('errors', []);
?>

<div class="max-w-2xl">
    <a href="/admin/categories" class="text-gray-500 hover:text-gray-700 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i> Retour aux catégories
    </a>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="<?= $isEdit ? '/admin/categories/' . $category->id : '/admin/categories' ?>" method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la catégorie *</label>
                <input
                    type="text"
                    name="name"
                    value="<?= htmlspecialchars($old['name'] ?? $category->name ?? '') ?>"
                    required
                    class="w-full px-4 py-2 border <?= isset($errors['name']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-green-500"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea
                    name="description"
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                ><?= htmlspecialchars($old['description'] ?? $category->description ?? '') ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie parente</label>
                <select name="parent_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="">Aucune (catégorie principale)</option>
                    <?php foreach ($categories as $cat): ?>
                        <?php if (!$isEdit || $cat->id !== $category->id): ?>
                            <option value="<?= $cat->id ?>" <?= ($old['parent_id'] ?? $category->parent_id ?? '') == $cat->id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat->name) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ordre d'affichage</label>
                <input
                    type="number"
                    name="sort_order"
                    value="<?= htmlspecialchars($old['sort_order'] ?? $category->sort_order ?? 0) ?>"
                    min="0"
                    class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                >
            </div>

            <div>
                <label class="flex items-center gap-3">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        <?= ($old['is_active'] ?? $category->is_active ?? true) ? 'checked' : '' ?>
                        class="rounded text-green-600 focus:ring-green-500"
                    >
                    <span>Catégorie active</span>
                </label>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-green-700 transition">
                    <i class="fas fa-save mr-2"></i>
                    <?= $isEdit ? 'Enregistrer' : 'Créer la catégorie' ?>
                </button>
                <a href="/admin/categories" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium hover:bg-gray-300 transition">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
