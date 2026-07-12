<?php 
require_once __DIR__ . '/../../src/Helpers/Security.php';
use App\Helpers\Security;
require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-4xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Detail Kontrak</h1>
    <table class="w-full mb-6">
        <tr><td class="font-semibold py-2 w-40">Penyewa</td><td><?= Security::escapeHtml($kontrak['nama_penyewa']) ?></td></tr>
        <tr><td class="font-semibold py-2">Email</td><td><?= Security::escapeHtml($kontrak['email']) ?></td></tr>
        <tr><td class="font-semibold py-2">No Telepon</td><td><?= Security::escapeHtml($kontrak['no_telepon'] ?? '-') ?></td></tr>
        <tr><td class="font-semibold py-2">Kamar</td><td><?= Security::escapeHtml($kontrak['nomor_kamar']) ?> - <?= Security::escapeHtml($kontrak['tipe']) ?></td></tr>
        <tr><td class="font-semibold py-2">Harga Sewa</td><td>Rp <?= number_format($kontrak['harga'], 0, ',', '.') ?> /bln</td></tr>
        <tr><td class="font-semibold py-2">Periode</td><td><?= Security::escapeHtml(date('d/m/Y', strtotime($kontrak['tgl_mulai']))) ?> s/d <?= Security::escapeHtml(date('d/m/Y', strtotime($kontrak['tgl_akhir']))) ?></td></tr>
        <tr><td class="font-semibold py-2">Status</td><td><?= Security::escapeHtml($kontrak['status']) ?></td></tr>
    </table>

    <hr class="my-4">
    <h2 class="text-xl font-bold mb-3">Riwayat Pembayaran</h2>

    <?php if (($_SESSION['user_role'] ?? '') !== 'penyewa'): ?>
    <a href="/pembayaran/bayar/<?= $kontrak['id'] ?>" class="bg-green-600 text-white px-4 py-2 rounded inline-block mb-3">+ Catat Pembayaran</a>
    <?php endif; ?>

    <?php if (count($riwayatBayar) > 0): ?>
    <table class="w-full bg-gray-50 rounded">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2">Periode</th>
                <th class="p-2">Jumlah</th>
                <th class="p-2">Denda</th>
                <th class="p-2">Status</th>
                <th class="p-2">Tanggal Bayar</th>
                <th class="p-2">Bukti</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($riwayatBayar as $i => $b): ?>
            <tr class="border-t <?= $i % 2 == 0 ? 'bg-gray-50' : '' ?>">
                <td class="p-2"><?= $b['bulan'] ?>/<?= $b['tahun'] ?></td>
                <td class="p-2">Rp <?= number_format($b['jumlah'], 0, ',', '.') ?></td>
                <td class="p-2">Rp <?= number_format($b['denda'], 0, ',', '.') ?></td>
                <td class="p-2"><?= Security::escapeHtml($b['status']) ?></td>
                <td class="p-2"><?= $b['tgl_bayar'] ? Security::escapeHtml(date('d/m/Y', strtotime($b['tgl_bayar']))) : '-' ?></td>
                <td class="p-2">
                    <?php if ($b['bukti']): ?>
                        <a href="/uploads/bukti_bayar/<?= Security::escapeHtml($b['bukti']) ?>" target="_blank" class="text-blue-600">Lihat</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p class="text-gray-500 text-sm">Belum ada riwayat pembayaran untuk kontrak ini.</p>
    <?php endif; ?>

    <div class="mt-4">
        <a href="/kontrak/index" class="text-gray-600">Kembali</a>
    </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>



