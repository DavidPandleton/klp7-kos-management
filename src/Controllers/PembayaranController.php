<?php

namespace App\Controllers;

use App\Models\Pembayaran;
use App\Models\Kontrak;
use App\Middleware\Auth;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Validator;

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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                Session::setFlash('error', 'Token tidak valid.');
                require_once __DIR__ . '/../../views/pembayaran/bayar.php';
                return;
            }

            $v = new Validator();
            $v->required('jumlah', $_POST['jumlah'], 'Jumlah bayar');
            $v->numeric('jumlah', $_POST['jumlah'], 'Jumlah bayar');

            $bukti = null;
            if (!empty($_FILES['bukti']['name'])) {
                $v->file('bukti', $_FILES['bukti'], ['image/jpeg', 'image/png', 'application/pdf'], 2097152, 'Bukti transfer');
                if ($v->passes()) {
                    $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
                    $bukti = 'bayar_' . time() . '.' . $ext;
                    move_uploaded_file($_FILES['bukti']['tmp_name'], __DIR__ . '/../../uploads/bukti_bayar/' . $bukti);
                }
            }

            if ($v->passes()) {
                $_POST['kontrak_id'] = $kontrakId;
                $_POST['bukti'] = $bukti;
                $_POST['status'] = 'menunggu';
                $_POST['bulan'] = date('n');
                $_POST['tahun'] = date('Y');
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
        if ($bayar && $bayar['bukti'] && file_exists(__DIR__ . '/../../uploads/bukti_bayar/' . $bayar['bukti'])) {
            unlink(__DIR__ . '/../../uploads/bukti_bayar/' . $bayar['bukti']);
        }
        $this->pembayaran->tolak($id);
        Session::setFlash('error', 'Pembayaran ditolak.');
        header('Location: /pembayaran/index');
        exit;
    }
}
