<?php 
require_once __DIR__ . '/../../src/Helpers/Security.php';
use App\Helpers\Security;
require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Daftar Kontrak Sewa</h1>
        <a href="/kontrak/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Buat Kontrak</a>
    </div>

    <form method="GET" class="mb-4 flex gap-2">
        <select name="status" class="border rounded px-3 py-2">
            <option value="">Semua Status</option>
            <option value="aktif" <?= ($_GET['status'] ?? '') == 'aktif' ? 'selected' : '' ?>>Aktif</option>
            <option value="selesai" <?= ($_GET['status'] ?? '') == 'selesai' ? 'selected' : '' ?>>Selesai</option>
            <option value="dibatalkan" <?= ($_GET['status'] ?? '') == 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
        </select>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded">Filter</button>
    </form>

    <table class="w-full bg-white rounded shadow">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 text-left">Penyewa</th>
                <th class="p-2 text-left">Kamar</th>
                <th class="p-2 text-left">Tgl Mulai</th>
                <th class="p-2 text-left">Tgl Akhir</th>
                <th class="p-2 text-left">Status</th>
                <th class="p-2 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
            <tr class="border-t">
                <td class="p-2"><?= Security::escapeHtml($row['nama_penyewa']) ?></td>
                <td class="p-2"><?= Security::escapeHtml($row['nomor_kamar']) ?></td>
                <td class="p-2"><?= Security::escapeHtml($row['tgl_mulai']) ?></td>
                <td class="p-2"><?= Security::escapeHtml($row['tgl_akhir']) ?></td>
                <td class="p-2">
                    <span class="px-2 py-1 rounded text-white text-xs
                        <?= $row['status'] == 'aktif' ? 'bg-green-500' : ($row['status'] == 'selesai' ? 'bg-gray-500' : 'bg-red-500') ?>">
                        <?= Security::escapeHtml($row['status']) ?>
                    </span>
                </td>
                <td class="p-2 flex gap-2">
                    <a href="/kontrak/detail/<?= $row['id'] ?>" class="text-blue-600 text-sm">Detail</a>
                    <?php if ($row['status'] == 'aktif'): ?>
                        <a href="/kontrak/selesaikan/<?= $row['id'] ?>" class="text-green-600 text-sm" onclick="return confirm('Selesaikan kontrak?')">Selesaikan</a>
                        <a href="/kontrak/batalkan/<?= $row['id'] ?>" class="text-red-600 text-sm" onclick="return confirm('Batalkan kontrak?')">Batalkan</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>



