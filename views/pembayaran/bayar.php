<?php 
require_once __DIR__ . '/../../src/Helpers/Security.php';
use App\Helpers\Security;
$bulanNama = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-2xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Bayar Sewa</h1>
    <p class="mb-4">Kamar: <strong><?= Security::escapeHtml($kontrak['nomor_kamar']) ?></strong></p>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
        <?php if (!empty($tagihanBelumBayar)): ?>
        <div class="mb-3">
            <label class="block text-gray-700">Periode Pembayaran</label>
            <select name="bulan" required class="w-full border rounded px-3 py-2">
                <option value="">-- Pilih Bulan --</option>
                <?php foreach ($tagihanBelumBayar as $t): ?>
                <option value="<?= $t['bulan'] . '-' . $t['tahun'] ?>">
                    <?= $bulanNama[$t['bulan']] ?> <?= $t['tahun'] ?> — Rp <?= number_format($t['jumlah'], 0, ',', '.') ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        <input type="hidden" name="tahun" id="input_tahun" value="">
        <div class="mb-3">
            <label class="block text-gray-700">Jumlah Bayar</label>
            <input type="number" name="jumlah" value="<?= Security::escapeHtml($kontrak['harga']) ?>" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Upload Bukti Transfer</label>
            <input type="file" name="bukti" accept="image/*,application/pdf" required class="w-full border rounded px-3 py-2">
        </div>
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Ajukan Pembayaran</button>
        <a href="/kontrak/detail/<?= $kontrak['id'] ?>" class="ml-2 text-gray-600">Batal</a>
    </form>
</div>
<script>
document.querySelector('select[name="bulan"]')?.addEventListener('change', function() {
    var parts = this.value.split('-');
    if (parts.length === 2) {
        document.querySelector('input[name="bulan"]')?.remove();
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'bulan';
        input.value = parts[0];
        this.form.appendChild(input);
        document.getElementById('input_tahun').value = parts[1];
    }
});
</script>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>



