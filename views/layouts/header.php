<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Kos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white/80 backdrop-blur-md shadow-sm px-6 py-3 flex justify-between items-center sticky top-0 z-50">
        <a href="/dashboard" class="text-xl font-bold text-violet-600">KosKu</a>
        <div class="flex items-center gap-4">
            <a href="/auth/profile" class="text-gray-600 hover:text-violet-600"><?= \App\Helpers\Security::escapeHtml($_SESSION['user_nama'] ?? '') ?></a>
            <span class="text-sm px-2 py-1 rounded bg-gray-200 text-gray-700"><?= \App\Helpers\Security::escapeHtml($_SESSION['user_role'] ?? '') ?></span>
            <a href="/auth/logout" class="text-red-500 hover:text-red-700">Logout</a>
        </div>
    </nav>

    <?php
    $role = $_SESSION['user_role'] ?? '';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    function isActive(string $path): string {
        $u = $_SERVER['REQUEST_URI'] ?? '/';
        return str_starts_with($u, $path) ? 'bg-violet-600 text-white' : 'text-gray-600 hover:bg-gray-100';
    }
    ?>
    <nav class="bg-white border-b px-6 py-2 flex flex-wrap gap-1 text-sm">
        <a href="/dashboard" class="px-3 py-1.5 rounded transition <?= isActive('/dashboard') ?>">Dashboard</a>
        <?php if (in_array($role, ['admin', 'pemilik', 'penyewa'])): ?>
            <a href="/kamar/index" class="px-3 py-1.5 rounded transition <?= isActive('/kamar') ?>">Kamar</a>
        <?php endif; ?>
        <?php if (in_array($role, ['admin', 'pemilik'])): ?>
            <a href="/kontrak/index" class="px-3 py-1.5 rounded transition <?= isActive('/kontrak') ?>">Kontrak</a>
            <a href="/pembayaran/index" class="px-3 py-1.5 rounded transition <?= isActive('/pembayaran') ?>">Pembayaran</a>
            <a href="/pengaduan/index" class="px-3 py-1.5 rounded transition <?= isActive('/pengaduan') ?>">Pengaduan</a>
            <a href="/laporan/index" class="px-3 py-1.5 rounded transition <?= isActive('/laporan') ?>">Laporan</a>
        <?php endif; ?>
        <?php if ($role === 'penyewa'): ?>
            <a href="/pengaduan/index" class="px-3 py-1.5 rounded transition <?= isActive('/pengaduan') ?>">Pengaduan</a>
        <?php endif; ?>
        <?php if ($role === 'admin'): ?>
            <a href="/user/index" class="px-3 py-1.5 rounded transition <?= isActive('/user') ?>">User</a>
            <a href="/notifikasi/index" class="px-3 py-1.5 rounded transition <?= isActive('/notifikasi') ?>">Email</a>
        <?php endif; ?>
        <a href="/auth/profile" class="px-3 py-1.5 rounded transition <?= isActive('/auth/profile') ?>">Profile</a>
    </nav>

    <?php
    $flash = \App\Helpers\Session::getFlash('success');
    if ($flash): ?>
        <div data-auto-hide class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mx-6 mt-4 rounded"><?= \App\Helpers\Security::escapeHtml($flash) ?></div>
    <?php endif; ?>

    <?php
    $flash = \App\Helpers\Session::getFlash('error');
    if ($flash): ?>
        <div data-auto-hide class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mx-6 mt-4 rounded"><?= \App\Helpers\Security::escapeHtml($flash) ?></div>
    <?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        document.querySelectorAll('[data-auto-hide]').forEach(function (el) {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 500);
        });
    }, 3000);
    document.querySelectorAll('form').forEach(function (f) {
        f.addEventListener('submit', function () {
            var btn = f.querySelector('button[type="submit"]');
            if (btn) { btn.disabled = true; btn.classList.add('opacity-50', 'cursor-not-allowed'); }
        });
    });
});
</script>
    <main class="p-6">
