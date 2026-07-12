<?php

namespace App\Controllers;

use App\Models\Pembayaran;
use App\Models\Kontrak;
use App\Middleware\Auth;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Validator;
use App\Helpers\FileUploader;

class PembayaranController
{
    private Pembayaran $pembayaran;

    public function __construct()
    {
        Auth::check();
        $this->pembayaran = new Pembayaran();
    }

    public function index(): void
    {
        Auth::role(['admin', 'pemilik']);
        $status = $_GET['status'] ?? '';
        $filter = '';
        $params = [];

        if (!empty($status)) {
            $filter .= " AND p.status = ?";
            $params[] = $status;
        }

        $data = $this->pembayaran->getAll($filter, $params);
        require_once __DIR__ . '/../../views/pembayaran/index.php';
    }

    public function bayar(int $kontrakId): void
    {
        $kontrakModel = new Kontrak();
        $kontrak = $kontrakModel->find($kontrakId);

        if (!$kontrak) {
            http_response_code(404);
            require_once __DIR__ . '/../../views/errors/404.php';
            return;
        }

        $role = Auth::getUserRole();
        if ($role === 'penyewa') {
            if ($kontrak['status'] !== 'aktif' || (int) $kontrak['penyewa_id'] !== Auth::getUserId()) {
                http_response_code(403);
                require_once __DIR__ . '/../../views/errors/403.php';
                return;
            }
        } else {
            Auth::role(['admin', 'pemilik']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                Session::setFlash('error', 'Token tidak valid.');
                require_once __DIR__ . '/../../views/pembayaran/bayar.php';
                return;
            }

            $v = new Validator();
            $v->required('jumlah', $_POST['jumlah'], 'Jumlah bayar');
            $v->numeric('jumlah', $_POST['jumlah'], 'Jumlah bayar');

            $uploader = new FileUploader(dirname(__DIR__, 2) . '/public/uploads/bukti_bayar', ['image/jpeg', 'image/png', 'application/pdf']);
            $bukti = $uploader->upload($_FILES['bukti'] ?? [], 'bayar');

            if ($v->passes()) {
                $_POST['kontrak_id'] = $kontrakId;
                $_POST['bukti'] = $bukti;
                $_POST['status'] = 'menunggu';
                $_POST['bulan'] = (int) date('n', strtotime($kontrak['tgl_mulai']));
                $_POST['tahun'] = (int) date('Y', strtotime($kontrak['tgl_mulai']));
                $this->pembayaran->create($_POST);
                Session::setFlash('success', 'Pembayaran diajukan, tunggu konfirmasi.');
                header('Location: /kontrak/detail/' . $kontrakId);
                exit;
            }

            Session::setFlash('error', $v->firstError());
        }

        require_once __DIR__ . '/../../views/pembayaran/bayar.php';
    }

    public function konfirmasi(int $id): void
    {
        Auth::role(['admin', 'pemilik']);
        $this->pembayaran->konfirmasi($id);
        Session::setFlash('success', 'Pembayaran dikonfirmasi.');
        header('Location: /pembayaran/index');
        exit;
    }

    public function tolak(int $id): void
    {
        Auth::role(['admin', 'pemilik']);
        $bayar = $this->pembayaran->find($id);
        if ($bayar) {
            $uploader = new FileUploader(dirname(__DIR__, 2) . '/public/uploads/bukti_bayar');
            $uploader->delete($bayar['bukti']);
        }
        $this->pembayaran->tolak($id);
        Session::setFlash('error', 'Pembayaran ditolak.');
        header('Location: /pembayaran/index');
        exit;
    }
}
