<?php 
require_once __DIR__ . '/../../src/Helpers/Security.php';
use App\Helpers\Security;
$fasilitasList = ['AC', 'WiFi', 'Kamar Mandi Dalam', 'Lemari', 'Meja'];
$fasilitasTersimpan = array_map('trim', explode(',', $kamar['fasilitas'] ?? ''));
?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-2xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Edit Kamar</h1>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="block text-gray-700">Nomor Kamar</label>
            <input type="text" name="nomor_kamar" value="<?= Security::escapeHtml($kamar['nomor_kamar']) ?>" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Tipe</label>
            <input type="text" name="tipe" value="<?= Security::escapeHtml($kamar['tipe']) ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Harga Sewa / Bulan</label>
            <input type="number" name="harga" value="<?= Security::escapeHtml($kamar['harga']) ?>" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Kapasitas</label>
            <input type="number" name="kapasitas" value="<?= Security::escapeHtml($kamar['kapasitas']) ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Fasilitas</label>
            <div class="flex flex-wrap gap-4">
                <?php foreach ($fasilitasList as $f): ?>
                <label>
                    <input type="checkbox" name="fasilitas[]" value="<?= $f ?>"
                        <?= in_array($f, $fasilitasTersimpan) ? 'checked' : '' ?>> <?= $f ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="tersedia" <?= $kamar['status'] == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                <option value="terisi" <?= $kamar['status'] == 'terisi' ? 'selected' : '' ?>>Terisi</option>
                <option value="maintenance" <?= $kamar['status'] == 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Foto Kamar</label>
            <?php if ($kamar['foto']): ?>
                <img src="/uploads/kamar/<?= Security::escapeHtml($kamar['foto']) ?>" class="w-32 h-24 object-cover rounded mb-2">
                <label class="flex items-center gap-2 text-red-600 text-sm mt-1">
                    <input type="checkbox" name="hapus_foto" value="1"> Hapus foto ini
                </label>
            <?php endif; ?>
            <input type="file" name="foto" accept="image/*" class="w-full border rounded px-3 py-2 mt-2">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Update</button>
        <a href="/kamar/index" class="ml-2 text-gray-600">Batal</a>
    </form>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>