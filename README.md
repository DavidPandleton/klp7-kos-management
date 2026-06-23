# KLP-7 Sistem Manajemen Kos / Kontrakan

Project mata kuliah **Pengembangan Sistem Backend (SI253314)** — Kelompok 7

> Sistem manajemen kos berbasis web dengan fitur CRUD multi-entitas, RBAC, pembayaran, pengaduan, dan laporan PDF.

---

## Anggota Kelompok

| No | Nama | NIM | Tugas |
|----|------|-----|-------|
| 1 | Anggota 1 | - | Auth & Middleware |
| 2 | Anggota 2 | - | Manajemen Kamar |
| 3 | Anggota 3 | - | Kontrak & Pembayaran |
| 4 | Anggota 4 | - | Pengaduan & Laporan PDF |

---

## Tech Stack

- **PHP 8.x** (Native — tanpa framework penuh)
- **MySQL** (via phpMyAdmin / command line)
- **Composer** (dependency manager PHP)
- **PHPMailer** — kirim email notifikasi
- **FPDF** — export laporan PDF
- **Tailwind CSS** — styling UI (via CDN)
- **Git & GitHub** — kolaborasi

---

## Panduan Setup (Wajib Dibaca)

### 1. Persiapan Tools

Install dulu yang diperlukan:

| Tool | Link Download | Keterangan |
|------|--------------|------------|
| XAMPP | https://www.apachefriends.org/ | Sudah include PHP + MySQL + phpMyAdmin |
| Composer | https://getcomposer.org/ | Pilih Windows Installer |
| Git | https://git-scm.com/ | Version control |
| VS Code | https://code.visualstudio.com/ | Code editor (opsional) |

### 2. Clone Project

Buka terminal (CMD / PowerShell / Git Bash), lalu:

```bash
cd C:\xampp\htdocs
git clone https://github.com/DavidPandleton/klp-7-sistem-manajemen-kos.git
cd klp-7-sistem-manajemen-kos
```

### 3. Install Dependencies (Composer)

```bash
composer install
```

Kalau Composer error, coba pake PHP langsung dari XAMPP:

```bash
"C:\xampp\php\php.exe" composer.phar install
```

### 4. Setup Database

1. Buka **XAMPP Control Panel**
2. Start **Apache** dan **MySQL**
3. Buka browser: `http://localhost/phpmyadmin`
4. Klik tab **SQL**
5. Copy paste isi file `database/schema.sql` lalu jalankan
6. Selesai — database `kos_management` sudah jadi beserta tabel dan data contoh

> **Atau lewat command line:**
> ```bash
> mysql -u root < database/schema.sql
> ```

### 5. Jalankan Project

Ada 2 cara:

**Cara 1 — Via PHP built-in server (recommended):**
```bash
php -S localhost:8000 -t public
```
Buka browser: `http://localhost:8000`

**Cara 2 — Via XAMPP:**
- Pastikan project ada di `C:\xampp\htdocs\klp-7-sistem-manajemen-kos`
- Buka: `http://localhost/klp-7-sistem-manajemen-kos/public/`

### 6. Login (Akun Test)

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@kos.com | password |
| Pemilik | pemilik@kos.com | password |
| Penyewa | penyewa@kos.com | password |

---

## Struktur Folder

```
klp-7-sistem-manajemen-kos/
│
├── public/                    # Hanya folder ini yang diakses browser
│   ├── index.php              # Router / front controller
│   └── .htaccess              # Rewrite URL
│
├── config/
│   └── database.php           # Koneksi database PDO
│
├── src/                       # Kode PHP utama (OOP)
│   ├── Controllers/           # Logic tiap fitur
│   │   ├── AuthController.php
│   │   ├── KamarController.php
│   │   ├── KontrakController.php
│   │   ├── PembayaranController.php
│   │   ├── PengaduanController.php
│   │   └── DashboardController.php
│   ├── Models/                # Query database tiap entitas
│   │   ├── User.php
│   │   ├── Kamar.php
│   │   ├── Kontrak.php
│   │   ├── Pembayaran.php
│   │   └── Pengaduan.php
│   ├── Middleware/
│   │   └── Auth.php           # Cek login + role
│   └── Helpers/
│       ├── Session.php        # Manajemen session
│       ├── Validator.php      # Validasi input
│       └── Security.php       # Hash, enkripsi, CSRF
│
├── views/                     # Template HTML
│   ├── layouts/               # Header & footer
│   ├── auth/                  # Halaman login/register
│   ├── kamar/                 # CRUD kamar
│   ├── kontrak/               # CRUD kontrak
│   ├── pembayaran/            # CRUD pembayaran
│   ├── pengaduan/             # CRUD pengaduan
│   ├── dashboard/             # Dashboard per role
│   └── errors/                # 403, 404
│
├── uploads/                   # File upload
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

## Cara Kerja Git (Penting!)

Kita pake **branching** biar kerjaan ga tabrakan.

### Branch yang Ada

| Branch | Isi |
|--------|-----|
| `master` | Kode yang udah stabil |
| `dev` | Gabungan semua fitur |
| `fitur/auth` | Login, register, RBAC (Anggota 1) |
| `fitur/kamar` | CRUD kamar + upload foto (Anggota 2) |
| `fitur/kontrak` | Kontrak sewa (Anggota 3) |
| `fitur/pembayaran` | Pembayaran + notifikasi email (Anggota 3) |
| `fitur/pengaduan` | Pengaduan + laporan PDF (Anggota 4) |

### Cara Pake Git

**Setiap kali mau ngoding:**
```bash
git checkout dev
git pull origin dev
git checkout -b fitur/apa-gitu   # bikin branch baru (cuma sekali)
git checkout fitur/apa-gitu      # pindah ke branch (udah ada)
```

**Abis selesai ngoding:**
```bash
git add .
git commit -m "judul singkat"
git push origin fitur/apa-gitu
```

**Udah kelar semua — merge ke dev:**
1. Buka GitHub → bikin Pull Request dari branch lo ke `dev`
2. Kasih tau temen biar di-review
3. Kalo udah OK, merge

> **Jangan commit langsung ke `master` atau `dev`!** Selalu pake branch fitur dulu.

---

## Pembagian Modul

### Anggota 1 — Auth & Middleware
File yang dikerjain:
- `src/Controllers/AuthController.php`
- `views/auth/login.php`, `views/auth/register.php`
- `src/Middleware/Auth.php` (sudah ada template)
- `src/Helpers/Session.php` (sudah ada)
- `src/Helpers/Security.php` (sudah ada)
- `src/Helpers/Validator.php` (sudah ada)
- `src/Controllers/DashboardController.php`
- `views/dashboard/` (3 role)

### Anggota 2 — Manajemen Kamar
File yang dikerjain:
- `src/Controllers/KamarController.php`
- `src/Models/Kamar.php`
- `views/kamar/index.php`, `create.php`, `edit.php`, `detail.php`

### Anggota 3 — Kontrak & Pembayaran
File yang dikerjain:
- `src/Controllers/KontrakController.php`
- `src/Controllers/PembayaranController.php`
- `src/Models/Kontrak.php`
- `src/Models/Pembayaran.php`
- `views/kontrak/index.php`, `create.php`, `edit.php`
- `views/pembayaran/index.php`, `create.php`, `detail.php`

### Anggota 4 — Pengaduan & Laporan
File yang dikerjain:
- `src/Controllers/PengaduanController.php`
- `src/Models/Pengaduan.php`
- `views/pengaduan/index.php`, `create.php`, `detail.php`
- Laporan PDF (FPDF)

---

## Aturan Main

1. **Kodingan wajib pake OOP** — class, method, properti. Jangan procedural spaghetti.
2. **Semua query pake PDO prepared statement** — jangan pake `mysqli_query()` langsung.
3. **Setiap output HTML pake `htmlspecialchars()`** — biar aman dari XSS.
4. **Input form divalidasi** — pake `Validator.php` yang udah disediain.
5. **Password di-hash pake `password_hash()`** — jangan simpen plain text.
6. **Commit tiap selesai bikin fitur** — biar history keliatan.
7. **Semua anggota wajib punya minimal 3 commit** — dosen bakal liat.
