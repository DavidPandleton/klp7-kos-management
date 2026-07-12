<?php 
require_once __DIR__ . '/../../src/Helpers/Security.php';
use App\Helpers\Security;
?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Dashboard Admin</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded shadow p-4">
            <p class="text-gray-500 text-sm">Total Kamar</p>
            <p class="text-3xl font-bold"><?= $data['total_kamar'] ?></p>
        </div>
        <div class="bg-white rounded shadow p-4">
            <p class="text-gray-500 text-sm">Kamar Terisi</p>
            <p class="text-3xl font-bold text-violet-600"><?= $data['kamar_terisi'] ?></p>
        </div>
        <div class="bg-white rounded shadow p-4">
            <p class="text-gray-500 text-sm">Kamar Kosong</p>
            <p class="text-3xl font-bold text-green-600"><?= $data['kamar_kosong'] ?></p>
        </div>
        <div class="bg-white rounded shadow p-4">
            <p class="text-gray-500 text-sm">Penyewa Aktif</p>
            <p class="text-3xl font-bold text-purple-600"><?= $data['penyewa_aktif'] ?></p>
        </div>
    </div>

    <div class="bg-white rounded shadow p-4 mb-6">
        <h2 class="font-bold text-lg mb-2">Pendapatan Bulan Ini</h2>
        <p class="text-3xl font-bold text-green-600">Rp <?= number_format($data['pendapatan_bulan_ini'], 0, ',', '.') ?></p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded shadow p-4">
            <h2 class="font-bold text-lg mb-3">Pembayaran Menunggu</h2>
            <?php if (empty($data['pembayaran_menunggu'])): ?>
                <p class="text-gray-500">Tidak ada.</p>
            <?php else: ?>
                <?php foreach ($data['pembayaran_menunggu'] as $p): ?>
                    <div class="border-b py-2 flex justify-between">
                        <span><?= Security::escapeHtml($p['nama_penyewa']) ?> - <?= Security::escapeHtml($p['nomor_kamar']) ?></span>
                        <a href="/pembayaran/index" class="text-violet-600 text-sm">Konfirmasi</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded shadow p-4">
            <h2 class="font-bold text-lg mb-3">Pengaduan Aktif</h2>
            <?php if (empty($data['pengaduan_aktif'])): ?>
                <p class="text-gray-500">Tidak ada.</p>
            <?php else: ?>
                <?php foreach ($data['pengaduan_aktif'] as $a): ?>
                    <div class="border-b py-2 flex justify-between">
                        <span><?= Security::escapeHtml($a['nama_penyewa']) ?> - <?= Security::escapeHtml($a['keluhan']) ?></span>
                        <a href="/pengaduan/detail/<?= $a['id'] ?>" class="text-violet-600 text-sm">Lihat</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
