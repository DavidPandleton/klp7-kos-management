<?php 
require_once __DIR__ . '/../../src/Helpers/Security.php';
use App\Helpers\Security;
require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Manajemen User</h1>
        <a href="/user/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Tambah User</a>
    </div>

    <?php if (count($data) > 0): ?>
    <p class="text-sm text-gray-500 mb-2">Total: <?= count($data) ?> user</p>
    <table class="w-full bg-white rounded shadow">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 text-left">Username</th>
                <th class="p-2 text-left">Email</th>
                <th class="p-2 text-left">Role</th>
                <th class="p-2 text-left">No Telepon</th>
                <th class="p-2 text-left">Tanggal Daftar</th>
                <th class="p-2 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $i => $row): ?>
            <tr class="border-t <?= $i % 2 == 0 ? 'bg-gray-50' : '' ?>">
                <td class="p-2"><?= Security::escapeHtml($row['username']) ?></td>
                <td class="p-2"><?= Security::escapeHtml($row['email']) ?></td>
                <td class="p-2"><?= Security::escapeHtml($row['role']) ?></td>
                <td class="p-2"><?= Security::escapeHtml($row['no_telepon'] ?? '-') ?></td>
                <td class="p-2"><?= Security::escapeHtml($row['created_at']) ?></td>
                <td class="p-2 flex gap-2">
                    <a href="/user/edit/<?= $row['id'] ?>" class="text-yellow-600 text-sm">Edit</a>
                    <?php if ($row['id'] !== \App\Middleware\Auth::getUserId()): ?>
                        <a href="/user/delete/<?= $row['id'] ?>" class="text-red-600 text-sm" onclick="return confirm('Yakin hapus user ini?')">Hapus</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="bg-white rounded shadow p-6 text-center text-gray-500">
        Belum ada user.
    </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>



