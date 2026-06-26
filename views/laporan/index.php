<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-lg mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Cetak Laporan Pembayaran</h1>
    <form method="GET" action="/laporan/pdf">
        <div class="mb-3">
            <label class="block text-gray-700">Bulan</label>
            <select name="bulan" class="w-full border rounded px-3 py-2">
                <?php
                $bulanList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                $bulanSekarang = date('n');
                for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?= $i ?>" <?= $i == $bulanSekarang ? 'selected' : '' ?>><?= $bulanList[$i - 1] ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Tahun</label>
            <select name="tahun" class="w-full border rounded px-3 py-2">
                <?php for ($i = date('Y'); $i >= 2024; $i--): ?>
                    <option value="<?= $i ?>" <?= $i == date('Y') ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700">
            Cetak PDF
        </button>
    </form>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
