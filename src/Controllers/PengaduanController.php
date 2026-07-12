<?php

namespace App\Controllers;

use App\Models\Pengaduan;
use App\Middleware\Auth;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Validator;
use App\Helpers\FileUploader;

class PengaduanController
{
    private Pengaduan $pengaduan;

    public function __construct()
    {
        Auth::check();
        $this->pengaduan = new Pengaduan();
    }

    public function index(): void
    {
        $role = Auth::getUserRole();
        $filter = '';
        $params = [];

        if ($role === 'penyewa') {
            $filter .= " AND a.penyewa_id = ?";
            $params[] = Auth::getUserId();
        }

        $status = $_GET['status'] ?? '';
        if (!empty($status)) {
            $filter .= " AND a.status = ?";
            $params[] = $status;
        }

        $data = $this->pengaduan->getAll($filter, $params);
        require_once __DIR__ . '/../../views/pengaduan/index.php';
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                Session::setFlash('error', 'Token tidak valid.');
                require_once __DIR__ . '/../../views/pengaduan/create.php';
                return;
            }

            $v = new Validator();
            $v->required('keluhan', $_POST['keluhan'], 'Keluhan');

            $uploader = new FileUploader(__DIR__ . '/../../uploads/pengaduan', ['image/jpeg', 'image/png', 'image/jpg']);
            $foto = $uploader->upload($_FILES['foto'] ?? [], 'aduan');

            if ($v->passes()) {
                $_POST['penyewa_id'] = Auth::getUserId();
                $_POST['foto'] = $foto;
                $this->pengaduan->create($_POST);
                Session::setFlash('success', 'Pengaduan berhasil dikirim.');
                header('Location: /pengaduan/index');
                exit;
            }

            Session::setFlash('error', $v->firstError());
        }

        require_once __DIR__ . '/../../views/pengaduan/create.php';
    }

    public function detail(int $id): void
    {
        $data = $this->pengaduan->find($id);
        if (!$data) {
            http_response_code(404);
            require_once __DIR__ . '/../../views/errors/404.php';
            return;
        }
        if (Auth::getUserRole() === 'penyewa' && (int) $data['penyewa_id'] !== Auth::getUserId()) {
            http_response_code(403);
            require_once __DIR__ . '/../../views/errors/403.php';
            return;
        }
        require_once __DIR__ . '/../../views/pengaduan/detail.php';
    }

    public function proses(int $id): void
    {
        Auth::role(['admin', 'pemilik']);
        $this->pengaduan->updateStatus($id, 'diproses');
        Session::setFlash('success', 'Status pengaduan diubah ke Diproses.');
        header('Location: /pengaduan/detail/' . $id);
        exit;
    }

    public function selesai(int $id): void
    {
        Auth::role(['admin', 'pemilik']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                Session::setFlash('error', 'Token tidak valid.');
                \App\Helpers\Session::setFlash('error', 'Token tidak valid.');
                header('Location: /pengaduan/detail/' . $id);
                exit;
            }

            $this->pengaduan->updateStatus($id, 'selesai', $_POST['respon']);
            Session::setFlash('success', 'Pengaduan selesai.');
            header('Location: /pengaduan/index');
            exit;
        }
        $data = $this->pengaduan->find($id);
        require_once __DIR__ . '/../../views/pengaduan/selesai.php';
    }
}
