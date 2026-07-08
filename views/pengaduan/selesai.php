<?php 
require_once __DIR__ . '/../../src/Helpers/Security.php';
use App\Helpers\Security;
require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-2xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Selesaikan Pengaduan</h1>
    <p class="mb-4"><strong>Keluhan:</strong> <?= Security::escapeHtml($data['keluhan']) ?></p>
    <form method="POST">
        <div class="mb-4">
            <label class="block text-gray-700">Respon / Tindakan</label>
            <textarea name="respon" rows="4" required class="w-full border rounded px-3 py-2" placeholder="Jelaskan tindakan yang dilakukan..."></textarea>
        </div>
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Selesaikan</button>
        <a href="/pengaduan/detail/<?= $data['id'] ?>" class="ml-2 text-gray-600">Batal</a>
    </form>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>



