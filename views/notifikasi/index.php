<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Kirim Notifikasi Email</h1>

    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded mb-4 text-sm">
        <strong>Catatan:</strong> Email dikirim via SMTP Gmail.
    </div>

    <form method="POST" action="/notifikasi/kirim">
        <input type="hidden" name="csrf_token" value="<?= \App\Helpers\Security::generateCsrfToken() ?>">
        <div class="mb-3">
            <label class="block text-gray-700">Penerima (Penyewa)</label>
            <select name="penyewa_id" required class="w-full border rounded px-3 py-2">
                <option value="">-- Pilih Penyewa --</option>
                <?php foreach ($penyewaList as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= \App\Helpers\Security::escapeHtml($p['username']) ?> (<?= \App\Helpers\Security::escapeHtml($p['email']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Jenis Notifikasi</label>
            <select name="jenis" required class="w-full border rounded px-3 py-2">
                <option value="">-- Pilih Jenis --</option>
                <option value="jatuh_tempo">Pengingat Jatuh Tempo</option>
                <option value="konfirmasi">Konfirmasi Pembayaran</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Pesan Tambahan (opsional)</label>
            <textarea name="pesan" rows="3" class="w-full border rounded px-3 py-2" placeholder="Tulis pesan tambahan jika perlu..."></textarea>
        </div>
        <button type="submit" class="bg-violet-600 text-white px-6 py-2 rounded hover:bg-violet-700">Kirim Notifikasi</button>
        <a href="/dashboard" class="ml-2 text-gray-600">Batal</a>
    </form>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
