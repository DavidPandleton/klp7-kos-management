# KLP-7 Sistem Manajemen Kos / Kontrakan

Project mata kuliah **Pengembangan Sistem Backend (SI253314)** --  Kelompok 7

**Repo:** https://github.com/DavidPandleton/klp7-kos-management

---

## Anggota & Tugas Masing-masing

| Nama | NIM | Modul |
|------|-----|-------|
| I Gusti Nyoman David Ray Tarigan | 250030487 | Auth, Middleware, Dashboard, User, Laporan PDF, Email |
| Ida Bagus Mastyendra Suja | 250030077 | Manajemen Kamar |
| Gede Richi Ary Sanjaya | 250030066 | Kontrak Sewa & Pembayaran |
| I Gusti Agus Tisna Yoga | 250030088 | Pengaduan |

---

## Cara Setup (Buat yang baru clone)

### 1. Jalankan XAMPP (Apache + MySQL)

### 2. Clone & Install

```bash
cd C:\xampp\htdocs
git clone https://github.com/DavidPandleton/klp7-kos-management.git
cd klp7-kos-management
composer install
```

### 3. Setup Database

Buka phpMyAdmin - tab SQL - paste isi `database/schema.sql` - jalankan.

### 4. Jalankan

```bash
php -S localhost:8000 -t public
```

Buka `http://localhost:8000`

---

## Akun Test

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@kos.com | password |
| **Pemilik** | pemilik@kos.com | password |
| **Penyewa** | penyewa@kos.com | password |

---

## Panduan Demo (Urutan Presentasi)

### 1. Register & Login (Penyewa)
- Buka `/auth/register` - buat akun baru
- Login dengan akun yang baru dibuat
- **Yang dinilai:** validasi email (cek email udah terdaftar), validasi username (cek duplikat), password di-hash

### 2. Lihat & Sewa Kamar (Penyewa)
- `/kamar/index` --  penyewa cuma lihat kamar yang `tersedia` (kamar terisi/maintenance gak muncul)
- Klik detail kamar - tombol **"Ajukan Sewa"**
- Isi tanggal mulai & akhir - submit
- **Yang dinilai:** filter role-based, validasi tanggal (gak boleh mundur, maks 12 bulan), CSRF token

### 3. Approval Kontrak (Admin)
- Login sebagai **admin@kos.com**
- Dashboard - lihat card **"Pengajuan Kontrak Baru"**
- Klik Review - lihat detail pengajuan
- Klik **Setujui** - kontrak jadi `aktif`, kamar jadi `terisi`, tagihan langsung auto-generate per bulan
- **Yang dinilai:** RBAC (hanya admin/pemilik bisa approve), transaction (kalo gagal rollback), auto-generate tagihan

Coba juga akses `/user/index` sebagai penyewa - harusnya 403. Ini bukti RBAC jalan.

### 4. Bayar Sewa (Penyewa + Admin)
- Login sebagai penyewa
- Dashboard - lihat card **"Kontrak Aktif"** + tombol **Bayar**
- Klik Bayar - pilih bulan dari dropdown (hanya bulan yg belum dibayar)
- Upload bukti transfer (file .png/.jpg/.pdf) - Ajukan Pembayaran
- Login sebagai admin - `/pembayaran/index` - lihat status **"Menunggu"**
- Klik **Konfirmasi** - status jadi `lunas`, denda otomatis dihitung kalo telat (lewat tgl 10)
- **Yang dinilai:** ownership check (penyewa cuma bisa bayar kontrak sendiri), denda logic, MIME validation upload

### 5. Pengaduan (Penyewa + Admin)
- Login sebagai penyewa - `/pengaduan/create` - isi keluhan - Kirim
- Login sebagai admin - `/pengaduan/index` - lihat pengaduan baru
- Klik Detail - **Proses** - status `diproses`
- Klik **Selesaikan** - isi respon - selesai
- **Yang dinilai:** ownership check, CRUD pengaduan, respon admin

### 6. Laporan PDF (Admin)
- Login sebagai admin - `/laporan/index`
- Pilih bulan/tahun - **Cetak PDF**
- PDF kebuka dengan: kop KOSKU, tabel pembayaran, total pendapatan, tanda tangan pemilik
- **Yang dinilai:** integrasi library FPDF

### 7. Manajemen User (Admin)
- `/user/index` --  CRUD user, role admin/pemilik/penyewa
- Admin gak bisa hapus diri sendiri
- Admin gak bisa ganti role sendiri
- Kalo hapus user yang punya kontrak aktif - ditolak
- **Yang dinilai:** RBAC, data integrity guard

---

## Struktur Folder (Buat Referensi)

```
|------ public/index.php            # Router (front controller)
|------ config/database.php         # Koneksi PDO singleton
|------ src/
|     |------ Controllers/            # 9 controller
|     |------ Models/                 # 5 model (PDO prepared statement)
|     |------ Middleware/Auth.php     # Auth check + RBAC
|     +------ Helpers/                # Session, Validator, Security, FileUploader, Mailer
|------ views/                      # Template + Tailwind CDN
|     |------ auth/                   # Login, register, profile
|     |------ dashboard/              # Admin, pemilik, penyewa
|     |------ kamar/                  # CRUD kamar
|     |------ kontrak/                # CRUD kontrak
|     |------ pembayaran/             # Bayar + riwayat
|     |------ pengaduan/              # CRUD pengaduan
|     |------ user/                   # Manajemen user (admin only)
|     |------ laporan/                # Form cetak PDF
|     |------ notifikasi/             # Kirim email
|     +------ errors/                 # 403, 404, 500
|------ database/schema.sql         # DDL + seed data
|------ public/uploads/             # Foto kamar, bukti bayar, foto pengaduan
|------ docs/evaluasi-library.md    # Evaluasi PHPMailer & FPDF
+------ docs/evaluasi-library.md
```

---

## Fitur Keamanan

| Fitur | Implementasi |
|-------|-------------|
| **SQL Injection** | PDO prepared statement (`?` parameter) di semua query |
| **XSS** | `htmlspecialchars()` di setiap output |
| **CSRF** | Token generate + verify di semua form POST |
| **Password** | `password_hash()` bcrypt |
| **Session** | Regenerasi setelah login, timeout 30 menit |
| **RBAC** | `Auth::role(['admin'])` --  middleware di tiap method |
| **File Upload** | Validasi MIME type, ukuran, rename pake timestamp |
| **Ownership** | Penyewa cuma bisa akses kontrak/pengaduan/pembayaran milik sendiri |

---

## Library Eksternal

| Library | Fungsi | Dokumentasi |
|---------|--------|-------------|
| **PHPMailer** ^6.9 | Kirim notifikasi email via SMTP | `docs/evaluasi-library.md` |
| **FPDF** ^1.85 | Generate laporan PDF | `docs/evaluasi-library.md` |
