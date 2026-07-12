# Architectural Analysis — KLP-7 Kos Management

**Date:** 2026-07-12
**Project:** Sistem Manajemen Kos / Kontrakan
**Auditor:** Violet

---

## Score Summary (vs Rubrik Proyek)

| Kriteria | Bobot | Score | Grade |
|----------|-------|-------|-------|
| 1. Perancangan arsitektur | 15% | 75 | B |
| 2. Basis data & CRUD multi-entitas | 25% | 90 | A |
| 3. Keamanan & akses kontrol | 20% | 85 | AB |
| 4. Integrasi library eksternal | 15% | 80 | AB |
| 5. Kolaborasi Git | 15% | 80 | AB |
| 6. Dokumentasi & presentasi | 10% | 80 | AB |
| **Weighted total** | **100%** | **~83** | **AB** |

---

## Perubahan yang Dilakukan

### 1. Keamanan (Security)

#### 1.1 Session Regeneration After Login
- **Lokasi:** `AuthController.php:login()`
- **Perubahan:** Menambahkan `session_regenerate_id(true)` setelah login berhasil
- **Dampak:** Mencegah session fixation attack
- **Severity:** High

#### 1.2 Session Timeout Aktif
- **Lokasi:** `Middleware/Auth.php:check()`
- **Perubahan:** Memanggil `Session::setTimeout(30)` setiap kali middleware auth dijalankan
- **Dampak:** Session user otomatis expired setelah 30 menit tidak ada aktivitas
- **Severity:** Medium

#### 1.3 CSRF Token Verification
- **Lokasi:** Semua controller (9 controller, 12 endpoint POST)
- **Perubahan:** Menambahkan verifikasi `Security::verifyCsrfToken()` di setiap handler POST
- **Dampak:** Mencegah Cross-Site Request Forgery
- **Severity:** Critical

#### 1.4 CSRF Token di View
- **Lokasi:** Semua view dengan form POST (12 form)
- **Perubahan:** Menambahkan hidden field `csrf_token` dengan `Security::generateCsrfToken()`
- **Dampak:** CSRF protection end-to-end

#### 1.5 Error Handling Database
- **Lokasi:** `config/database.php`
- **Perubahan:** Replace `die()` dengan `error_log()` + halaman 500
- **Dampak:** Tidak ada informasi sensitif yang bocor ke user
- **Severity:** Medium

#### 1.6 Sanitize Input Fix
- **Lokasi:** `Helpers/Security.php`
- **Perubahan:** Hapus `stripslashes()` yang redundan, sederhanakan `sanitizeInput()` menjadi `trim()` saja
- **Dampak:** Mencegah double-encoding, fungsi lebih jelas tujuannya

### 2. Arsitektur

#### 2.1 FileUploader Helper
- **Lokasi:** `Helpers/FileUploader.php` (file baru)
- **Perubahan:** Abstraksi upload file dengan metode `upload()`, `delete()`, `replace()`
- **Dampak:** Mengurangi duplikasi kode upload yang sebelumnya ada di 4 controller

### 3. Dokumentasi

#### 3.1 Evaluasi Library Eksternal
- **Lokasi:** `docs/evaluasi-library.md` (file baru)
- **Isi:** Evaluasi PHPMailer & FPDF mencakup fungsi, keamanan, kompatibilitas, batasan, mitigasi, dan alternatif
- **Dampak:** Memenuhi kriteria penilaian integrasi library

#### 3.2 README Update
- **Lokasi:** `README.md`
- **Perubahan:** Format tabel akun test agar lebih jelas

### 4. File Baru
- `src/Helpers/FileUploader.php` — Helper upload file reusable
- `views/errors/500.php` — Halaman error server
- `docs/evaluasi-library.md` — Evaluasi library eksternal
- `.audits/architectural-analysis-2026-07-12.md` — File ini

---

## Remaining Issues (Low Priority)

1. **Mailer credentials hardcoded** — `Mailer.php` masih menyimpan username/password Gmail statis. Untuk production, pindahkan ke environment variable.
2. **No rate limiting on login** — Belum ada proteksi brute force. Bisa ditambahkan dengan `$_SESSION['login_attempts']` counter.
3. **No transaction wrapping** — Operasi yang melibatkan multiple INSERT/UPDATE tidak menggunakan `beginTransaction()`.
4. **View rendering via `require_once`** — View memiliki akses ke semua variable controller scope. Untuk refactor lebih lanjut, bisa gunakan template engine atau setidaknya extract parameter yang eksplisit.

---

## Commit Distribution (Final)

| Author | Commits | Modul |
|--------|---------|-------|
| DavidPandleton | 26 + 7 (merge) | Auth, Middleware, Dashboard, User, Laporan, Notifikasi, Helpers, Config, README |
| I Gusti Agus Tisna Yoga | 7+1 | Pengaduan, Navigasi, CSRF |
| Gede Richi Ary Sanjaya | 3+2 | Kontrak, Pembayaran, CSRF, Library Eval |
| gusandra-1 | 10 | Kamar |
