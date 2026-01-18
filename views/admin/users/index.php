<div class="flex justify-between items-center mb-6">
    <p class="text-gray-500"><?= count($users) ?> utilisateurs</p>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (empty($users)): ?>
        <div class="p-8 text-center text-gray-500">
            Aucun utilisateur.
        </div>
    <?php else: ?>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rôle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inscription</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <span class="text-green-600 font-bold">
                                        <?= strtoupper(substr($user->first_name ?? 'U', 0, 1)) ?>
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium"><?= htmlspecialchars($user->getFullName() ?: 'Sans nom') ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            <?= htmlspecialchars($user->email) ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($user->role === 'admin'): ?>
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">Admin</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Client</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-sm">
                            <?= date('d/m/Y', strtotime($user->created_at)) ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="/admin/users/<?= $user->id ?>" class="text-blue-600 hover:text-blue-700">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
