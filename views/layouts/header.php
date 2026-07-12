<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Kos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-md px-6 py-3 flex justify-between items-center">
        <a href="/dashboard" class="text-xl font-bold text-blue-600">KosKu</a>
        <div class="flex items-center gap-4">
            <a href="/auth/profile" class="text-gray-600 hover:text-blue-600"><?= \App\Helpers\Security::escapeHtml($_SESSION['user_nama'] ?? '') ?></a>
            <span class="text-sm px-2 py-1 rounded bg-gray-200 text-gray-700"><?= \App\Helpers\Security::escapeHtml($_SESSION['user_role'] ?? '') ?></span>
            <a href="/auth/logout" class="text-red-500 hover:text-red-700">Logout</a>
        </div>
    </nav>

    <?php
    $role = $_SESSION['user_role'] ?? '';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    function isActive(string $path): string {
        return str_starts_with($GLOBALS['uri'], $path) ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100';
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
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mx-6 mt-4 rounded"><?= \App\Helpers\Security::escapeHtml($flash) ?></div>
    <?php endif; ?>

    <?php
    $flash = \App\Helpers\Session::getFlash('error');
    if ($flash): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mx-6 mt-4 rounded"><?= \App\Helpers\Security::escapeHtml($flash) ?></div>
    <?php endif; ?>

    <main class="p-6">
