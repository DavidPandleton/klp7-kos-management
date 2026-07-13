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

## Instalasi

### Persyaratan
- PHP 8.x
- MySQL (XAMPP / phpMyAdmin)
- Composer

### Langkah

```bash
git clone https://github.com/DavidPandleton/klp7-kos-management.git
cd klp7-kos-management
composer install
```

Jalankan `database/schema.sql` di phpMyAdmin untuk membuat database dan seed data.

```bash
php -S localhost:8000 -t public
```

Buka `http://localhost:8000`

---

## Akun Test

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@kos.com | password |
| Pemilik | pemilik@kos.com | password |
| Penyewa | penyewa@kos.com | password |

---

## Struktur Proyek

```
public/
  index.php              Router (front controller)
config/
  database.php           Koneksi PDO singleton
src/
  Controllers/           9 controller (Auth, Kamar, Kontrak, Pembayaran, dll)
  Models/                5 model (PDO prepared statement)
  Middleware/
    Auth.php             Auth check + RBAC
  Helpers/               Session, Validator, Security, FileUploader, Mailer
views/                   Template HTML + Tailwind CDN
database/
  schema.sql             DDL + seed data
docs/
  evaluasi-library.md    Evaluasi PHPMailer & FPDF
public/uploads/          Foto kamar, bukti bayar, foto pengaduan
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
| **RBAC** | `Auth::role(['admin'])` -- middleware di tiap method |
| **File Upload** | Validasi MIME type, ukuran, rename menggunakan timestamp |
| **Ownership** | Penyewa hanya dapat mengakses data milik sendiri |

---

## Library Eksternal

| Library | Fungsi | Dokumentasi |
|---------|--------|-------------|
| **PHPMailer** ^6.9 | Kirim notifikasi email via SMTP | `docs/evaluasi-library.md` |
| **FPDF** ^1.85 | Generate laporan PDF | `docs/evaluasi-library.md` |
