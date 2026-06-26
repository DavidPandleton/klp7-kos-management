<?php 
require_once __DIR__ . '/../../src/Helpers/Security.php';
use App\Helpers\Security;
?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-2xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Detail Kamar <?= Security::escapeHtml($kamar['nomor_kamar']) ?></h1>
    <?php if ($kamar['foto']): ?>
        <img src="/uploads/kamar/<?= Security::escapeHtml($kamar['foto']) ?>" class="w-full h-64 object-cover rounded mb-4">
    <?php endif; ?>
    <table class="w-full">
        <tr><td class="font-semibold py-2">Tipe</td><td><?= Security::escapeHtml($kamar['tipe']) ?></td></tr>
        <tr><td class="font-semibold py-2">Harga</td><td>Rp <?= number_format($kamar['harga'], 0, ',', '.') ?> / bln</td></tr>
        <tr><td class="font-semibold py-2">Kapasitas</td><td><?= Security::escapeHtml($kamar['kapasitas']) ?> orang</td></tr>
        <tr><td class="font-semibold py-2">Fasilitas</td><td><?= Security::escapeHtml($kamar['fasilitas']) ?></td></tr>
        <tr><td class="font-semibold py-2">Status</td><td><?= Security::escapeHtml($kamar['status']) ?></td></tr>
    </table>
    <div class="mt-4">
        <a href="/kamar/edit/<?= $kamar['id'] ?>" class="bg-yellow-600 text-white px-4 py-2 rounded">Edit</a>
        <a href="/kamar/index" class="ml-2 text-gray-600">Kembali</a>
    </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>