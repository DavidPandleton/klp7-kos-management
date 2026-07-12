<?php 
require_once __DIR__ . '/../../src/Helpers/Security.php';
use App\Helpers\Security;
require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-2xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Edit User</h1>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
        <div class="mb-3">
            <label class="block text-gray-700">Username</label>
            <input type="text" name="username" value="<?= Security::escapeHtml($user['username']) ?>" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Email</label>
            <input type="email" name="email" value="<?= Security::escapeHtml($user['email']) ?>" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Password (kosongkan jika tidak diganti)</label>
            <input type="password" name="password" minlength="6" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Role</label>
            <select name="role" class="w-full border rounded px-3 py-2">
                <option value="penyewa" <?= $user['role'] == 'penyewa' ? 'selected' : '' ?>>Penyewa</option>
                <option value="pemilik" <?= $user['role'] == 'pemilik' ? 'selected' : '' ?>>Pemilik</option>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">No Telepon</label>
            <input type="text" name="no_telepon" value="<?= Security::escapeHtml($user['no_telepon'] ?? '') ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Alamat</label>
            <textarea name="alamat" rows="3" class="w-full border rounded px-3 py-2"><?= Security::escapeHtml($user['alamat'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="bg-violet-600 text-white px-6 py-2 rounded hover:bg-violet-700">Update</button>
        <a href="/user/index" class="ml-2 text-gray-600">Batal</a>
    </form>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>



