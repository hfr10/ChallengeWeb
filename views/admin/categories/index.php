<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-gray-500"><?= count($categories) ?> catégories</p>
    </div>
    <a href="/admin/categories/create" class="bg-green-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-green-700 transition">
        <i class="fas fa-plus mr-2"></i> Nouvelle catégorie
    </a>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (empty($categories)): ?>
        <div class="p-8 text-center text-gray-500">
            Aucune catégorie. <a href="/admin/categories/create" class="text-green-600 hover:underline">Créer la première</a>
        </div>
    <?php else: ?>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produits</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($categories as $category): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium">
                            <?= htmlspecialchars($category->name) ?>
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            <?= htmlspecialchars($category->slug) ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-gray-100 rounded text-sm">
                                <?= $category->product_count ?> produits
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($category->is_active): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Active</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="/admin/categories/<?= $category->id ?>/edit" class="text-blue-600 hover:text-blue-700 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="/admin/categories/<?= $category->id ?>/delete" method="POST" class="inline" onsubmit="return confirm('Supprimer cette catégorie ?')">
                                <button type="submit" class="text-red-600 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
