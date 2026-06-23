<?php

namespace App\Controllers;

use App\Models\Kontrak;
use App\Models\User;
use App\Models\Kamar;
use App\Middleware\Auth;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Validator;

class KontrakController
{
    private Kontrak $kontrak;

    public function __construct()
    {
        Auth::check();
        Auth::role(['admin', 'pemilik']);
        $this->kontrak = new Kontrak();
    }

    public function index(): void
    {
        $status = $_GET['status'] ?? '';
        $filter = '';
        $params = [];

        if (!empty($status)) {
            $filter .= " AND k.status = ?";
            $params[] = $status;
        }

        $data = $this->kontrak->getAll($filter, $params);
        require_once __DIR__ . '/../../views/kontrak/index.php';
    }

    public function create(): void
    {
        $userModel = new User();
        $kamarModel = new Kamar();
        $penyewaList = $userModel->getByRole('penyewa');
        $kamarList = $kamarModel->getAll(" AND status = 'tersedia'", []);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $v = new Validator();
            $v->required('penyewa_id', $_POST['penyewa_id'], 'Penyewa');
            $v->required('kamar_id', $_POST['kamar_id'], 'Kamar');
            $v->required('tgl_mulai', $_POST['tgl_mulai'], 'Tanggal mulai');
            $v->required('tgl_akhir', $_POST['tgl_akhir'], 'Tanggal akhir');

            if ($v->passes()) {
                $this->kontrak->create($_POST);
                Session::setFlash('success', 'Kontrak berhasil dibuat.');
                header('Location: /kontrak/index');
                exit;
            }

            Session::setFlash('error', $v->firstError());
        }

        require_once __DIR__ . '/../../views/kontrak/create.php';
    }

    public function selesaikan(int $id): void
    {
        $this->kontrak->updateStatus($id, 'selesai');
        Session::setFlash('success', 'Kontrak diselesaikan.');
        header('Location: /kontrak/index');
        exit;
    }

    public function batalkan(int $id): void
    {
        $this->kontrak->updateStatus($id, 'dibatalkan');
        Session::setFlash('success', 'Kontrak dibatalkan.');
        header('Location: /kontrak/index');
        exit;
    }

    public function detail(int $id): void
    {
        $kontrak = $this->kontrak->find($id);
        if (!$kontrak) {
            http_response_code(404);
            require_once __DIR__ . '/../../views/errors/404.php';
            return;
        }
        $pembayaranModel = new \App\Models\Pembayaran();
        $riwayatBayar = $pembayaranModel->getByKontrak($id);
        require_once __DIR__ . '/../../views/kontrak/detail.php';
    }
}
