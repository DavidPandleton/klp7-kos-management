<?php 
require_once __DIR__ . '/../../src/Helpers/Security.php';
use App\Helpers\Security;
$role = $_SESSION['role'] ?? '';
$isOwner = in_array($role, ['admin', 'pemilik']);
?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Daftar Kamar</h1>
        <?php if ($isOwner): ?>
        <a href="/kamar/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Tambah Kamar</a>
        <?php endif; ?>
    </div>

    <form method="GET" class="mb-4 flex gap-2">
        <select name="status" class="border rounded px-3 py-2">
            <option value="">Semua Status</option>
            <option value="tersedia" <?= ($_GET['status'] ?? '') == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
            <option value="terisi" <?= ($_GET['status'] ?? '') == 'terisi' ? 'selected' : '' ?>>Terisi</option>
            <option value="maintenance" <?= ($_GET['status'] ?? '') == 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
        </select>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded">Filter</button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($data as $row): ?>
        <div class="bg-white rounded shadow p-4">
            <?php if ($row['foto']): ?>
                <img src="/uploads/kamar/<?= Security::escapeHtml($row['foto']) ?>" class="w-full h-40 object-cover rounded mb-3">
            <?php endif; ?>
            <h2 class="text-lg font-semibold">Kamar <?= Security::escapeHtml($row['nomor_kamar']) ?></h2>
            <p class="text-gray-600">Tipe: <?= Security::escapeHtml($row['tipe']) ?></p>
            <p class="text-blue-600 font-bold">Rp <?= number_format($row['harga'], 0, ',', '.') ?> /bln</p>
            <p class="text-sm mt-1">
                Status:
                <span class="px-2 py-1 rounded text-white text-xs
                    <?= $row['status'] == 'tersedia' ? 'bg-green-500' : ($row['status'] == 'terisi' ? 'bg-red-500' : 'bg-yellow-500') ?>">
                    <?= Security::escapeHtml($row['status']) ?>
                </span>
            </p>
            <p class="text-gray-500 text-sm mt-1">Fasilitas: <?= Security::escapeHtml($row['fasilitas']) ?></p>
            <div class="mt-3 flex gap-2">
                <a href="/kamar/detail/<?= $row['id'] ?>" class="text-blue-600 text-sm">Detail</a>
                <?php if ($isOwner): ?>
                <a href="/kamar/edit/<?= $row['id'] ?>" class="text-yellow-600 text-sm">Edit</a>
                <a href="/kamar/delete/<?= $row['id'] ?>" class="text-red-600 text-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>