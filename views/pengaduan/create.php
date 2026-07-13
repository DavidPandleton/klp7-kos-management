<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-2xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Ajukan Pengaduan</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= \App\Helpers\Security::generateCsrfToken() ?>">
        <div class="mb-3">
            <label class="block text-gray-700">Kamar (opsional)</label>
            <input type="text" name="kamar_id" placeholder="Nomor kamar" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Keluhan</label>
            <textarea name="keluhan" rows="5" required class="w-full border rounded px-3 py-2" placeholder="Jelaskan masalah yang dialami..."></textarea>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Foto (opsional)</label>
            <input type="file" name="foto" accept="image/*" class="w-full border rounded px-3 py-2">
        </div>
        <button type="submit" class="bg-violet-600 text-white px-6 py-2 rounded hover:bg-violet-700">Kirim</button>
        <a href="/pengaduan/index" class="ml-2 text-gray-600">Batal</a>
    </form>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
