<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Dashboard Penyewa</h1>

    <div class="bg-white rounded shadow p-6 mb-4">
        <h2 class="font-bold text-lg mb-2">Selamat datang, <?= \App\Helpers\Security::escapeHtml($_SESSION['user_nama']) ?>!</h2>
        <p class="text-gray-600">Gunakan menu di atas untuk:</p>
        <ul class="list-disc list-inside text-gray-600 mt-2">
            <li>Lihat daftar kamar yang tersedia</li>
            <li>Buat pengaduan jika ada masalah</li>
        </ul>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="/kamar/index" class="bg-white rounded shadow p-6 hover:shadow-md transition">
            <h3 class="font-bold text-lg text-violet-600">Kamar</h3>
            <p class="text-gray-500 text-sm">Lihat kamar tersedia</p>
        </a>
        <a href="/pengaduan/create" class="bg-white rounded shadow p-6 hover:shadow-md transition">
            <h3 class="font-bold text-lg text-yellow-600">Pengaduan</h3>
            <p class="text-gray-500 text-sm">Ajukan keluhan</p>
        </a>
        <a href="/auth/profile" class="bg-white rounded shadow p-6 hover:shadow-md transition">
            <h3 class="font-bold text-lg text-green-600">Profil</h3>
            <p class="text-gray-500 text-sm">Update data diri</p>
        </a>
    </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
