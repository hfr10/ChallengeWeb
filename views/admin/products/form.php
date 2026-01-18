<?php
$isEdit = $product !== null;
$old = \App\Core\Session::getFlash('old', []);
$errors = \App\Core\Session::getFlash('errors', []);
?>

<div class="max-w-4xl">
    <a href="/admin/products" class="text-gray-500 hover:text-gray-700 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i> Retour aux produits
    </a>

    <form action="<?= $isEdit ? '/admin/products/' . $product->id : '/admin/products' ?>" method="POST" class="space-y-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold mb-4">Informations générales</h2>

            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du produit *</label>
                    <input
                        type="text"
                        name="name"
                        value="<?= htmlspecialchars($old['name'] ?? $product->name ?? '') ?>"
                        required
                        class="w-full px-4 py-2 border <?= isset($errors['name']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-green-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                    <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">Sans catégorie</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->id ?>" <?= ($old['category_id'] ?? $product->category_id ?? '') == $cat->id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Marque</label>
                    <input
                        type="text"
                        name="brand"
                        value="<?= htmlspecialchars($old['brand'] ?? $product->brand ?? '') ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                    >
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description courte</label>
                    <input
                        type="text"
                        name="short_description"
                        value="<?= htmlspecialchars($old['short_description'] ?? $product->short_description ?? '') ?>"
                        maxlength="500"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                    >
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description complète</label>
                    <textarea
                        name="description"
                        rows="5"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                    ><?= htmlspecialchars($old['description'] ?? $product->description ?? '') ?></textarea>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image du produit</label>
                    <input
                        type="text"
                        name="image"
                        value="<?= htmlspecialchars($old['image'] ?? $product->image ?? '') ?>"
                        placeholder="URL de l'image (ex: https://exemple.com/image.jpg)"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                    >
                    <p class="text-sm text-gray-500 mt-1">Collez l'URL complète d'une image (https://...)</p>
                    <?php if ($isEdit && $product->image): ?>
                        <div class="mt-3">
                            <p class="text-sm text-gray-500 mb-2">Image actuelle :</p>
                            <img src="<?= htmlspecialchars($product->getImageUrl()) ?>" alt="Aperçu" class="w-32 h-32 object-cover rounded-lg border">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold mb-4">Prix et stock</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix *</label>
                    <input
                        type="number"
                        name="price"
                        value="<?= htmlspecialchars($old['price'] ?? $product->price ?? '') ?>"
                        step="0.01"
                        min="0"
                        required
                        class="w-full px-4 py-2 border <?= isset($errors['price']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-green-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix promo</label>
                    <input
                        type="number"
                        name="sale_price"
                        value="<?= htmlspecialchars($old['sale_price'] ?? $product->sale_price ?? '') ?>"
                        step="0.01"
                        min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                    <input
                        type="number"
                        name="stock"
                        value="<?= htmlspecialchars($old['stock'] ?? $product->stock ?? 0) ?>"
                        min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                    <input
                        type="text"
                        name="sku"
                        value="<?= htmlspecialchars($old['sku'] ?? $product->sku ?? '') ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                    >
                </div>
            </div>
        </div>

        <!-- Tailles -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold mb-4">Tailles (optionnel)</h2>
            <p class="text-sm text-gray-500 mb-4">Ajoutez les tailles disponibles pour ce produit.</p>

            <div id="sizes-container" class="space-y-3">
                <?php if ($isEdit && !empty($product->sizes)): ?>
                    <?php foreach ($product->sizes as $size): ?>
                        <div class="flex gap-4 items-center size-row">
                            <input
                                type="text"
                                name="sizes[size][]"
                                value="<?= htmlspecialchars($size['size']) ?>"
                                placeholder="Taille (ex: M, 42)"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg"
                            >
                            <input
                                type="number"
                                name="sizes[stock][]"
                                value="<?= $size['stock'] ?>"
                                placeholder="Stock"
                                min="0"
                                class="w-24 px-4 py-2 border border-gray-300 rounded-lg"
                            >
                            <button type="button" onclick="this.closest('.size-row').remove()" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button type="button" onclick="addSizeRow()" class="mt-3 text-green-600 hover:text-green-700 font-medium">
                <i class="fas fa-plus mr-1"></i> Ajouter une taille
            </button>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold mb-4">Options</h2>

            <div class="space-y-4">
                <label class="flex items-center gap-3">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        <?= ($old['is_active'] ?? $product->is_active ?? true) ? 'checked' : '' ?>
                        class="rounded text-green-600 focus:ring-green-500"
                    >
                    <span>Produit actif (visible sur le site)</span>
                </label>

                <label class="flex items-center gap-3">
                    <input
                        type="checkbox"
                        name="is_featured"
                        value="1"
                        <?= ($old['is_featured'] ?? $product->is_featured ?? false) ? 'checked' : '' ?>
                        class="rounded text-green-600 focus:ring-green-500"
                    >
                    <span>Produit vedette (affiché en page d'accueil)</span>
                </label>
            </div>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-green-700 transition">
                <i class="fas fa-save mr-2"></i>
                <?= $isEdit ? 'Enregistrer les modifications' : 'Créer le produit' ?>
            </button>
            <a href="/admin/products" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium hover:bg-gray-300 transition">
                Annuler
            </a>
        </div>
    </form>
</div>

<script>
function addSizeRow() {
    const container = document.getElementById('sizes-container');
    const row = document.createElement('div');
    row.className = 'flex gap-4 items-center size-row';
    row.innerHTML = `
        <input type="text" name="sizes[size][]" placeholder="Taille (ex: M, 42)" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg">
        <input type="number" name="sizes[stock][]" placeholder="Stock" min="0" class="w-24 px-4 py-2 border border-gray-300 rounded-lg">
        <button type="button" onclick="this.closest('.size-row').remove()" class="text-red-500 hover:text-red-700">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(row);
}
</script>
