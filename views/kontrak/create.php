<?php 
require_once __DIR__ . '/../../src/Helpers/Security.php';
use App\Helpers\Security;
$role = $_SESSION['user_role'] ?? '';
require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-2xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Buat Kontrak Sewa</h1>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">

        <?php if ($role === 'penyewa'): ?>
            <input type="hidden" name="penyewa_id" value="<?= $_SESSION['user_id'] ?>">
            <input type="hidden" name="kamar_id" value="<?= $_GET['kamar_id'] ?>">
            <div class="mb-3">
                <label class="block text-gray-700">Kamar</label>
                <p class="py-2"><strong><?= Security::escapeHtml($kamarList[0]['nomor_kamar'] ?? '') ?></strong> &mdash; Rp <?= number_format($kamarList[0]['harga'] ?? 0, 0, ',', '.') ?>/bln</p>
            </div>
        <?php else: ?>
            <div class="mb-3">
                <label class="block text-gray-700">Penyewa</label>
                <select name="penyewa_id" required class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih Penyewa --</option>
                    <?php foreach ($penyewaList as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= Security::escapeHtml($p['username']) ?> (<?= Security::escapeHtml($p['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="block text-gray-700">Kamar</label>
                <select name="kamar_id" required class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih Kamar --</option>
                    <?php foreach ($kamarList as $k): ?>
                        <option value="<?= $k['id'] ?>"><?= Security::escapeHtml($k['nomor_kamar']) ?> - Rp <?= number_format($k['harga'], 0, ',', '.') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label class="block text-gray-700">Tanggal Mulai</label>
            <input type="date" name="tgl_mulai" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Tanggal Akhir</label>
            <input type="date" name="tgl_akhir" required class="w-full border rounded px-3 py-2">
        </div>
        <button type="submit" class="bg-violet-600 text-white px-6 py-2 rounded hover:bg-violet-700">Buat Kontrak</button>
        <a href="/kamar/index" class="ml-2 text-gray-600">Batal</a>
    </form>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>



