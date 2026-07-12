# Evaluasi Library Eksternal

## A. PHPMailer (v6.9)

### 1. Alasan Pemilihan
- **Standar industri** untuk pengiriman email via PHP — digunakan secara luas (20k+ stars di GitHub).
- **Mendukung SMTP autentikasi** dengan enkripsi STARTTLS/SSL, cocok untuk integrasi Gmail.
- **Mudah diintegrasikan** — cukup Composer install, tanpa konfigurasi server email lokal.
- **Output HTML** untuk template notifikasi yang readable.

### 2. Cara Integrasi
```php
// Instalasi via Composer
// composer require phpmailer/phpmailer

// Contoh penggunaan di Mailer.php
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'kosku.notif@gmail.com';
$mail->Password = 'app-password';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
$mail->setFrom($username, $fromName);
$mail->addAddress($to, $toName);
$mail->isHTML(true);
$mail->Subject = $subject;
$mail->Body = $body;
$mail->send();
```

PHPMailer digunakan pada modul Notifikasi (`NotifikasiController`) untuk mengirim dua jenis email:
- **Pengingat jatuh tempo** pembayaran sewa
- **Konfirmasi pembayaran** ke penyewa

### 3. Fungsi
| Aspek | Keterangan |
|-------|-----------|
| SMTP | Mendukung Gmail, Outlook, dan SMTP server lain dengan auth |
| HTML email | Template HTML dengan fallback plaintext (AltBody) |
| Attachments | Lampiran file (tidak digunakan di proyek ini) |
| Error handling | Exception-based (`PHPMailer\PHPMailer\Exception`) |
| Keamanan | STARTTLS/SSL, DKIM signing (opsional) |

### 4. Keamanan
**Risiko teridentifikasi:**
- **Credential hardcoded** — `username` dan `password` SMTP ditulis statis di `Mailer.php`. Risiko kebocoran jika repo publik.
- **No rate limiting** — tidak ada pembatasan jumlah email per sesi, potensi abuse.
- **Email injection** — PHPMailer sudah memproteksi header injection, tapi input `$to` tetap harus divalidasi.

**Mitigasi:**
- Credential SMTP sebaiknya disimpan di environment variable atau file konfigurasi di luar repo.
- Tambahkan throttle (min 1 detik antar email) jika mengirim massal.
- Validasi alamat email penerima sebelum diproses (sudah ada di `Validator::email()`).

### 5. Kompatibilitas
| Aspek | Keterangan |
|-------|-----------|
| PHP version | PHP 5.5+ (cocok dengan proyek yang require PHP >=8.0) |
| Ekstensi | Membutuhkan `openssl` dan `sockets` (aktif default di XAMPP) |
| Server | SMTP Gmail membutuhkan koneksi internet dan port 587/465 terbuka |
| Library lain | Tidak konflik dengan FPDF atau kode native |

### 6. Batasan
- Membutuhkan koneksi SMTP eksternal — tidak bisa uji offline tanpa mock.
- Gmail membutuhkan App Password (2FA harus aktif), menyulitkan setup awal.
- Tidak ada queue system — jika SMTP lambat, request HTTP bisa timeout.

---

## B. FPDF (v1.85)

### 1. Alasan Pemilihan
- **Library PHP murni** — tanpa dependensi eksternal dan tanpa ekstensi tambahan.
- **Size kecil** — ~1.7MB, ringan.
- **Output langsung ke browser** atau save ke file, fleksibel.
- **Dokumentasi lengkap** dengan contoh untuk tabel, header, footer.

### 2. Cara Integrasi
```php
// Instalasi via Composer
// composer require fpdf/fpdf

// Contoh penggunaan di LaporanController.php
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Judul Laporan', 0, 1, 'C');
// ... isi tabel ...
$pdf->Output('I', 'laporan.pdf');
```

FPDF digunakan pada modul Laporan (`LaporanController`) untuk:
- **Laporan pembayaran bulanan** dalam format PDF dengan tabel, header, footer, dan total pendapatan.

### 3. Fungsi
| Aspek | Keterangan |
|-------|-----------|
| PDF generation | Multi-page, header/footer otomatis |
| Tabel | Cell, row coloring, alignment |
| Font | Built-in (Helvetica, Times, Courier) + TrueType support |
| Output | Browser (`I`), file (`F`), download (`D`) |
| Licensing | Bebas digunakan (LGPL) |

### 4. Keamanan
**Risiko teridentifikasi:**
- **Tidak ada sanitasi output** — data dari database langsung dicetak ke PDF. Jika ada XSS di data, PDF tetap aman karena FPDF bukan HTML parser.
- **Tidak enkripsi** — FPDF tidak mendukung PDF encryption built-in (perlu library tambahan).
- **File path injection** pada metode `Output()` — parameter filename harus divalidasi.
- **No input validation** — data `$_GET['bulan']` dan `$_GET['tahun']` langsung dipakai tanpa validasi numerik.

**Mitigasi:**
- Gunakan `htmlspecialchars()` untuk data yang ditampilkan di PDF (sudah diterapkan via `Security::escapeHtml()` di view, tapi di controller PDF belum).
- Validasi parameter `bulan` dan `tahun` dengan `ctype_digit()` sebelum digunakan.
- Untuk PDF encryption, bisa tambahkan library seperti `setasign/fpdi` atau beralih ke Dompdf/TCPDF.

### 5. Kompatibilitas
| Aspek | Keterangan |
|-------|-----------|
| PHP version | PHP 5.1+ (cocok dengan proyek) |
| Ekstensi | Minimal, hanya `zlib` untuk kompresi |
| Encoding | Mendukung UTF-8 via `UTF-8 to ISO-8859-1` konversi |
| Library lain | Tidak konflik dengan PHPMailer atau kode native |

### 6. Batasan
- **Tidak mendukung UTF-8 penuh** — karakter non-Latin perlu ekstensi tambahan.
- **HTML ke PDF tidak didukung** — semua layout harus dikode manual via `Cell()`, `MultiCell()`, dll.
- **Tidak ada CSS styling** — styling dilakukan via method PHP.
- **Tidak ada encryption/password protection** — untuk kebutuhan yang lebih advance.

---

## C. Alternatif & Saran

### Alternatif PHPMailer
| Library | Kelebihan | Kekurangan |
|---------|-----------|------------|
| **SwiftMailer** | Lebih modern, MIME support lebih baik | Sudah di-deprecate (diganti Symfony Mailer) |
| **Symfony Mailer** | Komponen Symfony, fitur lengkap | Butuh lebih banyak boilerplate |
| **Native `mail()`** | Tanpa library | Tidak SMTP, sering di-block hosting |

### Alternatif FPDF
| Library | Kelebihan | Kekurangan |
|---------|-----------|------------|
| **Dompdf** | Render HTML+CSS ke PDF | Berat, lambat untuk dokumen besar |
| **TCPDF** | UTF-8 support, encryption, lebih fitur | Ukuran library lebih besar |
| **mpdf** | HTML+CSS, UTF-8 | Memory usage tinggi |

**Rekomendasi:** Jika aplikasi dikembangkan lebih lanjut, migrasi ke **TCPDF** disarankan untuk UTF-8 support dan encryption. Sementara untuk kebutuhan saat ini, FPDF sudah cukup memadai.

---

*Dokumen ini disusun sebagai bagian dari Tugas 2 & Proyek mata kuliah Pengembangan Sistem Backend (SI253314).*
