<?php 
require_once __DIR__ . '/../../src/Helpers/Security.php';
use App\Helpers\Security;
require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Daftar Pembayaran</h1>

    <form method="GET" class="mb-4 flex gap-2">
        <select name="status" class="border rounded px-3 py-2">
            <option value="">Semua Status</option>
            <option value="menunggu" <?= ($_GET['status'] ?? '') == 'menunggu' ? 'selected' : '' ?>>Menunggu Konfirmasi</option>
            <option value="lunas" <?= ($_GET['status'] ?? '') == 'lunas' ? 'selected' : '' ?>>Lunas</option>
            <option value="belum_bayar" <?= ($_GET['status'] ?? '') == 'belum_bayar' ? 'selected' : '' ?>>Belum Bayar</option>
        </select>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded">Filter</button>
    </form>

    <table class="w-full bg-white rounded shadow">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 text-left">Penyewa</th>
                <th class="p-2 text-left">Kamar</th>
                <th class="p-2 text-left">Periode</th>
                <th class="p-2 text-left">Jumlah</th>
                <th class="p-2 text-left">Status</th>
                <th class="p-2 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
            <tr class="border-t">
                <td class="p-2"><?= Security::escapeHtml($row['nama_penyewa']) ?></td>
                <td class="p-2"><?= Security::escapeHtml($row['nomor_kamar']) ?></td>
                <td class="p-2"><?= $row['bulan'] ?>/<?= $row['tahun'] ?></td>
                <td class="p-2">Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                <td class="p-2">
                    <span class="px-2 py-1 rounded text-white text-xs
                        <?= $row['status'] == 'lunas' ? 'bg-green-500' : ($row['status'] == 'menunggu' ? 'bg-yellow-500' : 'bg-red-500') ?>">
                        <?= $row['status'] == 'menunggu' ? 'Menunggu' : Security::escapeHtml($row['status']) ?>
                    </span>
                </td>
                <td class="p-2 flex gap-2">
                    <?php if ($row['status'] == 'menunggu'): ?>
                        <a href="/pembayaran/konfirmasi/<?= $row['id'] ?>" class="text-green-600 text-sm" onclick="return confirm('Konfirmasi pembayaran ini?')">Konfirmasi</a>
                        <a href="/pembayaran/tolak/<?= $row['id'] ?>" class="text-red-600 text-sm" onclick="return confirm('Tolak pembayaran?')">Tolak</a>
                    <?php endif; ?>
                    <?php if ($row['bukti']): ?>
                        <a href="/uploads/bukti_bayar/<?= Security::escapeHtml($row['bukti']) ?>" target="_blank" class="text-blue-600 text-sm">Bukti</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>



