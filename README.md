# KLP-7 Sistem Manajemen Kos / Kontrakan

Project mata kuliah **Pengembangan Sistem Backend (SI253314)** — Kelompok 7

> Sistem manajemen kos berbasis web dengan fitur CRUD multi-entitas, RBAC, pembayaran, pengaduan, dan laporan PDF.

---

## Anggota Kelompok

| No | Nama | NIM | Tugas |
|----|------|-----|-------|
| 1 | I Gusti Nyoman David Ray Tarigan | 250030487 | Auth, Middleware, Dashboard, Email, Laporan PDF |
| 2 | Ida Bagus Mastyendra Suja | 250030077 | Manajemen Kamar |
| 3 | Gede Richi Ary Sanjaya | 250030066 | Kontrak & Pembayaran |
| 4 | I Gusti Agus Tisna Yoga | 250030088 | Pengaduan |

---

## Tech Stack

- **PHP 8.x** (Native)
- **MySQL** (via phpMyAdmin)
- **Composer** — PHPMailer, FPDF
- **Tailwind CSS** (via CDN)
- **Git & GitHub** — kolaborasi

---

## Panduan Setup

### 1. Persiapan Tools

| Tool | Download | Keterangan |
|------|----------|------------|
| XAMPP | https://www.apachefriends.org/ | PHP + MySQL + phpMyAdmin |
| Composer | https://getcomposer.org/ | Dependency manager PHP |
| Git | https://git-scm.com/ | Version control |
| VS Code | https://code.visualstudio.com/ | Code editor (opsional) |

### 2. Clone Repository

```
cd C:\xampp\htdocs
git clone https://github.com/DavidPandleton/klp7-kos-management.git
cd klp7-kos-management
```

### 3. Install Dependencies (Composer)

```
composer install
```

Jika Composer tidak dikenali, gunakan PHP langsung dari XAMPP:

```
"C:\xampp\php\php.exe" composer.phar install
```

### 4. Setup Database

1. Buka **XAMPP Control Panel**
2. Start **Apache** dan **MySQL**
3. Buka browser: `http://localhost/phpmyadmin`
4. Klik tab **SQL**
5. Salin isi file `database/schema.sql` lalu jalankan

Atau melalui command line:

```
mysql -u root < database/schema.sql
```

### 5. Menjalankan Project

**Via PHP built-in server (disarankan):**

```
php -S localhost:8000 -t public
```

Buka: `http://localhost:8000`

**Via XAMPP:**
- Pastikan project berada di `C:\xampp\htdocs\klp7-kos-management`
- Buka: `http://localhost/klp7-kos-management/public/`

### 6. Login (Akun Test)

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@kos.com | password |
| **Pemilik** | pemilik@kos.com | password |
| **Penyewa** | penyewa@kos.com | password |

---

## Struktur Folder

```
klp7-kos-management/
│
├── public/                    # DIakses oleh browser
│   ├── index.php              # Router
│   └── .htaccess              # Rewrite URL
│
├── config/
│   └── database.php           # Koneksi database PDO
│
├── src/                       # Kode PHP (OOP)
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── KamarController.php
│   │   ├── KontrakController.php
│   │   ├── PembayaranController.php
│   │   ├── PengaduanController.php
│   │   └── UserController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Kamar.php
│   │   ├── Kontrak.php
│   │   ├── Pembayaran.php
│   │   └── Pengaduan.php
│   ├── Middleware/
│   │   └── Auth.php           # Cek login + role
│   └── Helpers/
│       ├── Session.php
│       ├── Validator.php
│       └── Security.php
│
├── views/
│   ├── layouts/               # Header & footer
│   ├── auth/                  # Login, register, profile
│   ├── dashboard/             # Admin, pemilik, penyewa
│   ├── kamar/                 # CRUD kamar
│   ├── kontrak/               # CRUD kontrak
│   ├── pembayaran/            # CRUD pembayaran
│   ├── pengaduan/             # CRUD pengaduan
│   ├── user/                  # Manajemen user (admin)
│   └── errors/                # 403, 404
│
├── uploads/
│   ├── kamar/                 # Foto kamar
│   ├── bukti_bayar/           # Bukti transfer
│   └── pengaduan/             # Foto pengaduan
│
├── database/
│   └── schema.sql             # DDL + seed data
│
├── vendor/                    # Composer packages
├── composer.json
├── composer.lock
└── README.md
```

---

## Alur Kerja Git

### Branch yang Tersedia

| Branch | Deskripsi |
|--------|-----------|
| `master` | Kode stabil |
| `dev` | Tempat penggabungan semua fitur |
| `fitur/auth` | Auth, Middleware, Dashboard (David) |
| `fitur/kamar` | CRUD Kamar + upload foto (Gusandra) |
| `fitur/kontrak` | Kontrak sewa (Richie) |
| `fitur/pembayaran` | Pembayaran (Richie) |
| `fitur/pengaduan` | Pengaduan (Tisna) |

### Cara Menggunakan Git

**Sebelum mulai mengerjakan:**

```
git checkout dev
git pull origin dev
git checkout fitur/nama_fitur
```

**Setelah selesai mengerjakan:**

```
git add .
git commit -m "keterangan singkat"
git push origin fitur/nama_fitur
```

**Untuk menggabungkan ke dev:**
1. Buka GitHub
2. Buat Pull Request dari branch fitur ke `dev`
3. Minta anggota lain untuk review
4. Jika sudah disetujui, merge

> **Catatan:** Jangan melakukan commit langsung ke `master` atau `dev`. Selalu gunakan branch fitur masing-masing.

---

## Pembagian Modul

### David — Auth, Middleware, Dashboard, Email, Laporan PDF
- `src/Controllers/AuthController.php`
- `src/Controllers/DashboardController.php`
- `src/Controllers/UserController.php`
- `views/auth/` — login, register, profile
- `views/dashboard/` — admin, pemilik, penyewa
- `views/user/` — manajemen user
- `src/Middleware/Auth.php`
- `src/Helpers/Session.php`, `Security.php`, `Validator.php`
- Notifikasi email (PHPMailer)
- Export laporan PDF (FPDF)

### Gusandra — Manajemen Kamar
- `src/Controllers/KamarController.php`
- `src/Models/Kamar.php`
- `views/kamar/index.php`
- `views/kamar/create.php`
- `views/kamar/edit.php`
- `views/kamar/detail.php`

### Richie — Kontrak & Pembayaran
- `src/Controllers/KontrakController.php`
- `src/Controllers/PembayaranController.php`
- `src/Models/Kontrak.php`
- `src/Models/Pembayaran.php`
- `views/kontrak/index.php`
- `views/kontrak/create.php`
- `views/kontrak/detail.php`
- `views/pembayaran/index.php`
- `views/pembayaran/bayar.php`

### Tisna — Pengaduan
- `src/Controllers/PengaduanController.php`
- `src/Models/Pengaduan.php`
- `views/pengaduan/index.php`
- `views/pengaduan/create.php`
- `views/pengaduan/detail.php`
- `views/pengaduan/selesai.php`

---

## Aturan Pengembangan

1. **Kode wajib menggunakan OOP** — class, method, properti. Tidak boleh procedural.
2. **Semua query menggunakan PDO prepared statement** — tidak boleh menggunakan `mysqli_query()`.
3. **Setiap output HTML menggunakan `htmlspecialchars()`** — untuk mencegah XSS.
4. **Input form harus divalidasi** — gunakan `Validator.php` yang sudah disediakan.
5. **Password di-hash menggunakan `password_hash()`** — jangan menyimpan plain text.
6. **Commit setiap selesai mengerjakan fitur** — agar history terlihat.
7. **Setiap anggota wajib memiliki minimal 3 commit** — akan dinilai oleh dosen.
