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
            <span class="text-gray-600"><?= \App\Helpers\Security::escapeHtml($_SESSION['user_nama'] ?? '') ?></span>
            <span class="text-sm px-2 py-1 rounded bg-gray-200 text-gray-700"><?= \App\Helpers\Security::escapeHtml($_SESSION['user_role'] ?? '') ?></span>
            <a href="/auth/logout" class="text-red-500 hover:text-red-700">Logout</a>
        </div>
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
