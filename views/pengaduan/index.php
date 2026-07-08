<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Pengaduan</h1>
        <?php if (($_SESSION['user_role'] ?? '') === 'penyewa'): ?>
        <a href="/pengaduan/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Ajukan Pengaduan</a>
        <?php endif; ?>
    </div>

    <form method="GET" class="mb-4 flex gap-2">
        <select name="status" class="border rounded px-3 py-2">
            <option value="">Semua Status</option>
            <option value="baru" <?= ($_GET['status'] ?? '') == 'baru' ? 'selected' : '' ?>>Baru</option>
            <option value="diproses" <?= ($_GET['status'] ?? '') == 'diproses' ? 'selected' : '' ?>>Diproses</option>
            <option value="selesai" <?= ($_GET['status'] ?? '') == 'selesai' ? 'selected' : '' ?>>Selesai</option>
        </select>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded">Filter</button>
    </form>

    <table class="w-full bg-white rounded shadow">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 text-left">Penyewa</th>
                <th class="p-2 text-left">Kamar</th>
                <th class="p-2 text-left">Keluhan</th>
                <th class="p-2 text-left">Status</th>
                <th class="p-2 text-left">Tanggal</th>
                <th class="p-2 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
            <tr class="border-t">
                <td class="p-2"><?= Security::escapeHtml($row['nama_penyewa']) ?></td>
                <td class="p-2"><?= Security::escapeHtml($row['nomor_kamar'] ?? '-') ?></td>
                <td class="p-2"><?= Security::escapeHtml(mb_substr($row['keluhan'], 0, 50)) ?>...</td>
                <td class="p-2">
                    <span class="px-2 py-1 rounded text-white text-xs
                        <?= $row['status'] == 'baru' ? 'bg-red-500' : ($row['status'] == 'diproses' ? 'bg-yellow-500' : 'bg-green-500') ?>">
                        <?= Security::escapeHtml($row['status']) ?>
                    </span>
                </td>
                <td class="p-2"><?= Security::escapeHtml($row['created_at']) ?></td>
                <td class="p-2">
                    <a href="/pengaduan/detail/<?= $row['id'] ?>" class="text-blue-600 text-sm">Detail</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
