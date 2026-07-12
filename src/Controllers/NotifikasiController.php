<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Kontrak;
use App\Middleware\Auth;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Mailer;

class NotifikasiController
{
    public function __construct()
    {
        Auth::check();
        Auth::role(['admin', 'pemilik']);
    }

    public function index(): void
    {
        $userModel = new User();
        $penyewaList = $userModel->getByRole('penyewa');
        require_once __DIR__ . '/../../views/notifikasi/index.php';
    }

    public function kirim(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /notifikasi/index');
            exit;
        }

        if (!\App\Helpers\Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Token tidak valid.');
            header('Location: /notifikasi/index');
            exit;
        }

        $userId = (int) ($_POST['penyewa_id'] ?? 0);
        $jenis = $_POST['jenis'] ?? '';
        $pesan = $_POST['pesan'] ?? '';

        $userModel = new User();
        $penyewa = $userModel->find($userId);

        if (!$penyewa) {
            Session::setFlash('error', 'Penyewa tidak ditemukan.');
            header('Location: /notifikasi/index');
            exit;
        }

        $kontrakModel = new Kontrak();
        $kontrakAktif = $kontrakModel->getActiveByPenyewa($userId);
        $kamar = !empty($kontrakAktif[0]) ? $kontrakAktif[0]['nomor_kamar'] : '-';
        $harga = !empty($kontrakAktif[0]) ? $kontrakAktif[0]['harga'] : 0;

        $subject = '';
        $body = '';

        if ($jenis === 'jatuh_tempo') {
            $subject = 'Pengingat Pembayaran Kos';
            $batas = date('d/m/Y', strtotime('+7 days'));
            $body = Mailer::notifJatuhTempo($penyewa['username'], $kamar, (int)$harga, $batas);
        } elseif ($jenis === 'konfirmasi') {
            $subject = 'Pembayaran Dikonfirmasi';
            $status = 'dikonfirmasi dan diterima';
            $body = Mailer::notifKonfirmasi($penyewa['username'], $kamar, (int)$harga, $status);
        } else {
            Session::setFlash('error', 'Jenis notifikasi tidak valid.');
            header('Location: /notifikasi/index');
            exit;
        }

        if (!empty($pesan)) {
            $body .= "<br><p><i>Catatan: {$pesan}</i></p>";
        }

        $terkirim = Mailer::send($penyewa['email'], $penyewa['username'], $subject, $body);

        if ($terkirim) {
            Session::setFlash('success', 'Notifikasi berhasil dikirim ke ' . $penyewa['email']);
        } else {
            Session::setFlash('error', 'Gagal mengirim email. Periksa konfigurasi SMTP.');
        }

        header('Location: /notifikasi/index');
        exit;
    }
}
