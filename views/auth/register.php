<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Manajemen Kos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold text-center mb-6">Daftar Akun Baru</h1>

        <?php
        $flash = \App\Helpers\Session::getFlash('error');
        if ($flash): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= \App\Helpers\Security::escapeHtml($flash) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= \App\Helpers\Security::generateCsrfToken() ?>">
            <div class="mb-4">
                <label class="block text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="username" required class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-1">Email</label>
                <input type="email" name="email" required class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required minlength="6" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-1">No Telepon</label>
                <input type="text" name="no_telepon" class="w-full border rounded px-3 py-2">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Daftar</button>
        </form>
        <p class="text-center mt-4 text-gray-600">
            Sudah punya akun? <a href="/auth/login" class="text-blue-600 hover:underline">Login</a>
        </p>
    </div>
</body>
</html>
