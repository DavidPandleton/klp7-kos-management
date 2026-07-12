<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-2xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Tambah User</h1>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= \App\Helpers\Security::generateCsrfToken() ?>">
        <div class="mb-3">
            <label class="block text-gray-700">Username</label>
            <input type="text" name="username" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Email</label>
            <input type="email" name="email" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Password</label>
            <input type="password" name="password" required minlength="6" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Role</label>
            <select name="role" class="w-full border rounded px-3 py-2">
                <option value="penyewa">Penyewa</option>
                <option value="pemilik">Pemilik</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">No Telepon</label>
            <input type="text" name="no_telepon" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Alamat</label>
            <textarea name="alamat" rows="3" class="w-full border rounded px-3 py-2"></textarea>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Simpan</button>
        <a href="/user/index" class="ml-2 text-gray-600">Batal</a>
    </form>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
