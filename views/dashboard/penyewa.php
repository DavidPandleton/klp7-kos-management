<?php
use App\Helpers\Security;
$kontrakModel = new \App\Models\Kontrak();
$pembayaranModel = new \App\Models\Pembayaran();
$userId = $_SESSION['user_id'] ?? 0;
$kontrakAktif = $kontrakModel->getActiveByPenyewa($userId);
$semuaKontrak = $kontrakModel->getByPenyewa($userId);
$menungguApproval = array_filter($semuaKontrak, fn($k) => $k['status'] === 'menunggu');
?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Dashboard Penyewa</h1>

    <div class="bg-white rounded shadow p-6 mb-4">
        <h2 class="font-bold text-lg mb-2">Selamat datang, <?= Security::escapeHtml($_SESSION['user_nama']) ?>!</h2>
        <p class="text-gray-600">Gunakan menu di atas untuk navigasi.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <?php if (!empty($kontrakAktif)): ?>
        <div class="bg-green-50 rounded shadow p-4 border border-green-200">
            <p class="text-green-700 text-sm">Kontrak Aktif</p>
            <p class="text-2xl font-bold text-green-700"><?= count($kontrakAktif) ?></p>
            <p class="text-green-600 text-sm">Kamar: <?= Security::escapeHtml($kontrakAktif[0]['nomor_kamar']) ?></p>
        </div>
        <?php else: ?>
        <div class="bg-gray-50 rounded shadow p-4">
            <p class="text-gray-500 text-sm">Kontrak Aktif</p>
            <p class="text-2xl font-bold text-gray-400">0</p>
            <p class="text-gray-400 text-sm">Belum ada kontrak</p>
        </div>
        <?php endif; ?>
        <div class="bg-white rounded shadow p-4">
            <p class="text-gray-500 text-sm">Menunggu Approval</p>
            <p class="text-2xl font-bold text-yellow-600"><?= count($menungguApproval) ?></p>
        </div>
        <div class="bg-white rounded shadow p-4">
            <p class="text-gray-500 text-sm">Riwayat Kontrak</p>
            <p class="text-2xl font-bold text-violet-600"><?= count($semuaKontrak) ?></p>
        </div>
    </div>

    <?php if (!empty($kontrakAktif)): ?>
    <div class="bg-white rounded shadow p-4 mb-6">
        <h2 class="font-bold text-lg mb-3">Kontrak Aktif</h2>
        <?php foreach ($kontrakAktif as $k): 
            $tagihan = $pembayaranModel->getUnpaidByKontrak($k['id']);
        ?>
        <div class="flex justify-between items-center border-b py-3">
            <div>
                <p class="font-semibold">Kamar <?= Security::escapeHtml($k['nomor_kamar']) ?></p>
                <p class="text-sm text-gray-500">Rp <?= number_format($k['harga'], 0, ',', '.') ?>/bln</p>
            </div>
            <div class="flex gap-2">
                <a href="/kontrak/detail/<?= $k['id'] ?>" class="text-violet-600 text-sm">Detail</a>
                <?php if (!empty($tagihan)): ?>
                <a href="/pembayaran/bayar/<?= $k['id'] ?>" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">Bayar</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="/kamar/index" class="bg-white rounded shadow p-6 hover:shadow-md transition">
            <h3 class="font-bold text-lg text-violet-600">Kamar</h3>
            <p class="text-gray-500 text-sm">Cari & sewa kamar</p>
        </a>
        <a href="/pengaduan/create" class="bg-white rounded shadow p-6 hover:shadow-md transition">
            <h3 class="font-bold text-lg text-yellow-600">Pengaduan</h3>
            <p class="text-gray-500 text-sm">Ajukan keluhan</p>
        </a>
        <a href="/auth/profile" class="bg-white rounded shadow p-6 hover:shadow-md transition">
            <h3 class="font-bold text-lg text-green-600">Profil</h3>
            <p class="text-gray-500 text-sm">Update data diri</p>
        </a>
    </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
