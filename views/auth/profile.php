<?php 
require_once __DIR__ . '/../../src/Helpers/Security.php';
use App\Helpers\Security;
require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-2xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Profil Saya</h1>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
        <div class="mb-3">
            <label class="block text-gray-700">Nama</label>
            <input type="text" name="username" value="<?= Security::escapeHtml($user['username']) ?>" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Email</label>
            <input type="email" name="email" value="<?= Security::escapeHtml($user['email']) ?>" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">No Telepon</label>
            <input type="text" name="no_telepon" value="<?= Security::escapeHtml($user['no_telepon'] ?? '') ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Alamat</label>
            <textarea name="alamat" rows="3" class="w-full border rounded px-3 py-2"><?= Security::escapeHtml($user['alamat'] ?? '') ?></textarea>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Password Baru (kosongkan jika tidak diganti)</label>
            <input type="password" name="password" minlength="6" class="w-full border rounded px-3 py-2">
        </div>
        <button type="submit" class="bg-violet-600 text-white px-6 py-2 rounded hover:bg-violet-700">Simpan</button>
    </form>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>



