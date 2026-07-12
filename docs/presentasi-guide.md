# Catatan Presentasi — Proyek Sistem Manajemen Kos

## A. Gambaran Umum

**Project:** Sistem Manajemen Kos/Kontrakan berbasis web
**Mata Kuliah:** Pengembangan Sistem Backend (SI253314)
**Kelompok:** 7
**Stack:** PHP 8 native + MySQL + Composer + Tailwind CSS

Project ini mengelola data kos: kamar, penyewa, kontrak sewa, pembayaran, dan pengaduan. Ada 3 role pengguna: Admin, Pemilik, dan Penyewa.

---

## B. Arsitektur Sistem

Proyek menggunakan pola **MVC (Model-View-Controller)** sederhana tanpa framework.

### Alur Request-Response

```
Browser request: http://localhost:8000/kamar/index
                        │
           ┌────────────┴────────────┐
           │    public/index.php     │ (Router / Front Controller)
           │  - Parse URL → pecah    │
           │    controller/method    │
           └────────────┬────────────┘
                        │
           ┌────────────┴────────────┐
           │  Controllers/           │ (Logika bisnis)
           │  AuthController.php     │
           │  KamarController.php    │
           │  KontrakController.php  │
           │  PembayaranController   │
           │  PengaduanController    │
           └────────────┬────────────┘
                        │
           ┌────────────┴────────────┐
           │  Models/                │ (Query database via PDO)
           │  User.php               │
           │  Kamar.php              │
           │  Kontrak.php            │
           └────────────┬────────────┘
                        │
           ┌────────────┴────────────┐
           │  views/                 │ (Template HTML + Tailwind)
           │  layouts/               │ header & footer
           │  auth/                  │ login, register, profile
           │  kamar/                 │ daftar, tambah, edit
           │  dashboard/             │ admin, pemilik, penyewa
           └─────────────────────────┘
```

**Penjelasan:**
1. **Router** (`public/index.php`) membaca URL dari browser
2. URL dipecah jadi `Controller/Method/Params`
3. Controller memproses data (memanggil Model jika perlu akses database)
4. Model menggunakan **PDO** untuk query MySQL yang aman dari SQL Injection
5. Controller memanggil View untuk menampilkan HTML
6. Semua output HTML menggunakan `htmlspecialchars()` untuk mencegah XSS

---

## C. Komponen Keamanan (Yang Dibanggakan)

| Komponen | Implementasi |
|----------|-------------|
| **SQL Injection** | Semua query pakai PDO prepared statement (`?` parameter) |
| **XSS** | Setiap output HTML di-escape dengan `htmlspecialchars()` |
| **Password** | Di-hash dengan `password_hash()` bcrypt, jangan plain text |
| **RBAC** | Middleware `Auth::role(['admin'])` — penyewa tidak bisa akses halaman admin |
| **Session** | Login/logout dengan session PHP native; session diregenerasi setelah login (cegah fixation); timeout otomatis 30 menit |
| **CSRF** | Token CSRF di-generate dan diverifikasi di setiap form POST |
| **Validasi Input** | Semua input dari form divalidasi sebelum diproses |
| **Upload File** | Tipe file diperiksa via MIME, ukuran dibatasi, nama file di-unique-kan |

---

## D. Fitur yang Dibuat (David)

### 1. Sistem Autentikasi (AuthController)
- Login dengan email & password
- Register untuk penyewa baru
- Logout — hapus session
- Edit profil & ganti password
- Session timeout otomatis (30 menit tidak aktif)

### 2. Middleware RBAC (Auth.php)
- `Auth::check()` — cek apakah user sudah login
- `Auth::role(['admin', 'pemilik'])` — cek role, redirect 403 jika tidak punya akses

### 3. Dashboard (DashboardController)
- **Admin:** melihat total kamar, kamar terisi/kosong, penyewa aktif, pendapatan bulan ini, pembayaran yang menunggu, pengaduan aktif
- **Pemilik:** sama seperti admin
- **Penyewa:** halaman sambutan dan navigasi ke fitur yang tersedia

### 4. Manajemen User (UserController)
- CRUD user (khusus admin)
- Assign role: admin, pemilik, penyewa

### 5. Helpers
- **Session.php:** mengelola session, flash messages
- **Security.php:** hash password, enkripsi data, token CSRF
- **Validator.php:** validasi input form

### 6. Email Notification (PHPMailer)
- Halaman `/notifikasi/index` — kirim notifikasi manual
- Pilih penyewa dari dropdown + jenis notifikasi (jatuh tempo / konfirmasi)
- Template HTML email otomatis: nama penyewa, kamar, nominal, batas waktu
- Konfigurasi SMTP Gmail di `src/Helpers/Mailer.php`
- Masih perlu setup SMTP credentials sebelum bisa kirim beneran

### 7. Laporan PDF (FPDF)
- Halaman `/laporan/index` — form filter bulan & tahun
- Klik "Cetak PDF" → download otomatis file PDF
- Tampilan PDF proper:
  - Kop: "KOSKU MANAGEMENT" + judul laporan + periode
  - Tabel: No, Penyewa, Kamar, Jumlah, Denda, Status
  - Footer: Total pendapatan, tempat/tanggal, tanda tangan pemilik
  - Font Arial, warna header abu-abu gelap
- File PDF: `laporan-pembayaran-{bulan}-{tahun}.pdf`
- Output via browser (`Output('I')`)

---

## E. Database (5 Tabel)

```
users → kontrak → pembayaran
  ↓
kamar → kontrak
  ↓
pengaduan
```

| Tabel | Entitas |
|-------|---------|
| `users` | Admin, Pemilik, Penyewa |
| `kamar` | Data kamar + foto + fasilitas |
| `kontrak` | Perjanjian sewa (penyewa + kamar) |
| `pembayaran` | Transaksi bayar + bukti upload |
| `pengaduan` | Keluhan penyewa + respon pemilik |

---

## F. Poin yang Sering Ditanya Dosen + Jawaban

### "Apa perbedaan framework dan native?"
> Native: kita tulis semuanya dari nol — routing manual, koneksi database manual. Framework (seperti Laravel) sudah menyediakan semuanya tinggal pakai. Proyek ini native agar kita paham fundamental backend.

### "Kenapa pakai PDO?"
> PDO (PHP Data Object) mendukung prepared statement yang otomatis mencegah SQL Injection. Berbeda dengan `mysqli_query()` yang rentan jika tidak dibersihkan inputnya.

### "Apa itu MVC?"
> Model untuk data/database, View untuk tampilan, Controller untuk logika. Tujuannya memisahkan kode agar lebih terstruktur dan mudah dipelihara.

### "Apa itu RBAC?"
> Role-Based Access Control — pengaturan akses berdasarkan peran (role). Admin bisa semua, pemilik bisa melihat kamar dan pembayaran, penyewa hanya bisa melihat kamar dan mengajukan pengaduan.

### "Bagaimana cara kerja session?"
> Saat login, server menyimpan data pengguna (user_id, role) ke variabel `$_SESSION`. Setiap halaman yang butuh akses dicek dulu session-nya. Saat logout, session dihapus.

### "Library eksternal apa yang digunakan?"
> PHPMailer untuk mengirim email notifikasi, FPDF untuk membuat laporan PDF. Keduanya diinstall via Composer (package manager PHP). Evaluasi lengkap fungsi, keamanan, dan kompatibilitas ada di `docs/evaluasi-library.md`.

### "Apa tantangan terbesar?"
> Integrasi RBAC — memastikan setiap halaman memiliki pengecekan role yang benar. Validasi file upload (memastikan tipe dan ukuran file sesuai). Serta memastikan semua anggota bisa berkontribusi via Git. Tantangan lain adalah implementasi CSRF — memastikan setiap form punya token dan diverifikasi di setiap POST handler.

### "Ada bug yang ditemukan dan diperbaiki?"
> Ada satu bug privilege escalation di halaman profil. Sebelumnya, data `$_POST` mentah dikirim ke model User, termasuk field `role`. Jadi penyewa bisa ngirim request dengan field `role=admin` dan otomatis naik kelas. Kami perbaiki dengan meng-unset field `role` dari data POST sebelum disimpan.

---

## G. Pembagian Tugas

| Anggota | Module |
|---------|--------|
| David | Auth, Middleware, Dashboard, User Management, Email, Laporan PDF |
| Gusandra | Manajemen Kamar |
| Richie | Kontrak Sewa, Pembayaran |
| Tisna | Pengaduan |

---

## H. Cara Demo

1. Buka `http://localhost:8000` — login page muncul
2. Login sebagai Admin (`admin@kos.com` / `password`)
3. **Dashboard Admin** — tunjukkan card statistik (total kamar, pendapatan, pembayaran menunggu, pengaduan aktif)
4. **User Management** (`/user/index`) — lihat daftar pengguna dengan 3 role berbeda
5. **Laporan PDF** (`/laporan/index`) — pilih bulan Juni 2026, klik Cetak PDF, download & buka PDF-nya
6. **Notifikasi Email** (`/notifikasi/index`) — pilih penyewa, pilih jenis notifikasi, klik Kirim
7. Logout → Login sebagai Pemilik — dashboard beda (tanpa user management)
8. Logout → Login sebagai Penyewa — dashboard sederhana, coba akses `/user/index` → 403 (RBAC jalan)
