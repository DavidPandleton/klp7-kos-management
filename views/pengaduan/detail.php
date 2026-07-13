<?php 
require_once __DIR__ . '/../../src/Helpers/Security.php';
use App\Helpers\Security;
require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-3xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Detail Pengaduan</h1>
    <table class="w-full mb-4">
        <tr><td class="font-semibold py-2 w-40">Penyewa</td><td><?= Security::escapeHtml($data['nama_penyewa']) ?></td></tr>
        <tr><td class="font-semibold py-2">Kamar</td><td><?= Security::escapeHtml($data['nomor_kamar'] ?? '-') ?></td></tr>
        <tr><td class="font-semibold py-2">Status</td><td><?= Security::escapeHtml($data['status']) ?></td></tr>
        <tr><td class="font-semibold py-2">Tanggal</td><td><?= Security::escapeHtml(date('d/m/Y H:i', strtotime($data['created_at']))) ?></td></tr>
    </table>

    <div class="bg-gray-50 p-4 rounded mb-4">
        <h3 class="font-semibold mb-2">Keluhan:</h3>
        <p><?= nl2br(Security::escapeHtml($data['keluhan'])) ?></p>
    </div>

    <?php if ($data['foto']): ?>
        <div class="mb-4">
            <h3 class="font-semibold mb-2">Foto:</h3>
            <a href="/uploads/pengaduan/<?= Security::escapeHtml($data['foto']) ?>" target="_blank">
                <img src="/uploads/pengaduan/<?= Security::escapeHtml($data['foto']) ?>" class="max-w-md rounded border hover:opacity-90 transition">
            </a>
        </div>
    <?php endif; ?>

    <?php if ($data['respon']): ?>
        <div class="bg-blue-50 p-4 rounded mb-4">
            <h3 class="font-semibold mb-2">Respon:</h3>
            <p><?= nl2br(Security::escapeHtml($data['respon'])) ?></p>
        </div>
    <?php endif; ?>

    <div class="mt-4 flex gap-2">
        <?php if (($_SESSION['user_role'] ?? '') !== 'penyewa'): ?>
            <?php if ($data['status'] == 'baru'): ?>
                <a href="/pengaduan/proses/<?= $data['id'] ?>" class="bg-yellow-600 text-white px-4 py-2 rounded">Proses</a>
            <?php endif; ?>
            <?php if ($data['status'] != 'selesai'): ?>
                <a href="/pengaduan/selesai/<?= $data['id'] ?>" class="bg-green-600 text-white px-4 py-2 rounded">Selesaikan</a>
            <?php endif; ?>
        <?php endif; ?>
        <a href="/pengaduan/index" class="text-gray-600 px-4 py-2">Kembali</a>
    </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>



