<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-2xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Bayar Sewa</h1>
    <p class="mb-4">Kamar: <strong><?= Security::escapeHtml($kontrak['nomor_kamar']) ?></strong> |
       Periode: <?= date('F Y') ?></p>

    <form method="POST" enctype="multipart/form-data">
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
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
