<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-2xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Tambah Kamar</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= \App\Helpers\Security::generateCsrfToken() ?>">
        <div class="mb-3">
            <label class="block text-gray-700">Nomor Kamar</label>
            <input type="text" name="nomor_kamar" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Tipe</label>
            <input type="text" name="tipe" class="w-full border rounded px-3 py-2" placeholder="Standar / VIP">
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Harga Sewa / Bulan</label>
            <input type="number" name="harga" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Kapasitas</label>
            <input type="number" name="kapasitas" value="1" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Fasilitas</label>
            <div class="flex flex-wrap gap-4">
                <label><input type="checkbox" name="fasilitas[]" value="AC"> AC</label>
                <label><input type="checkbox" name="fasilitas[]" value="WiFi"> WiFi</label>
                <label><input type="checkbox" name="fasilitas[]" value="Kamar Mandi Dalam"> Kamar Mandi Dalam</label>
                <label><input type="checkbox" name="fasilitas[]" value="Lemari"> Lemari</label>
                <label><input type="checkbox" name="fasilitas[]" value="Meja"> Meja</label>
            </div>
        </div>
        <div class="mb-3">
            <label class="block text-gray-700">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="tersedia">Tersedia</option>
                <option value="terisi">Terisi</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Foto Kamar</label>
            <input type="file" name="foto" accept="image/*" class="w-full border rounded px-3 py-2">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Simpan</button>
        <a href="/kamar/index" class="ml-2 text-gray-600">Batal</a>
    </form>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>