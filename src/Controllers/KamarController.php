<?php

namespace App\Controllers;

use App\Models\Kamar;
use App\Middleware\Auth;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Validator;
use App\Helpers\FileUploader;

class KamarController
{
    private Kamar $kamar;
    private FileUploader $uploader;

    public function __construct()
    {
        Auth::check();
        $this->kamar = new Kamar();
        $this->uploader = new FileUploader(
            dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'kamar',
            ['image/jpeg', 'image/png', 'image/jpg']
        );
    }

    public function index(): void
    {
        // Semua role bisa lihat daftar kamar
        Auth::role(['admin', 'pemilik', 'penyewa']);

        $status = $_GET['status'] ?? '';
        $filter = '';
        $params = [];

        if (!empty($status)) {
            $filter .= " AND status = ?";
            $params[] = $status;
        }

        $data = $this->kamar->getAll($filter, $params);
        require_once __DIR__ . '/../../views/kamar/index.php';
    }

    public function create(): void
    {
        Auth::role(['admin', 'pemilik']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                Session::setFlash('error', 'Token tidak valid.');
                require_once __DIR__ . '/../../views/kamar/create.php';
                return;
            }

            $v = new Validator();
            $v->required('nomor_kamar', $_POST['nomor_kamar'], 'Nomor kamar');
            $v->required('harga', $_POST['harga'], 'Harga');
            $v->numeric('harga', $_POST['harga'], 'Harga');

            $foto = $this->uploader->upload($_FILES['foto'] ?? [], 'kamar');

            if ($v->passes()) {
                $_POST['foto'] = $foto;
                $_POST['fasilitas'] = implode(', ', $_POST['fasilitas'] ?? []);
                $this->kamar->create($_POST);
                Session::setFlash('success', 'Kamar berhasil ditambahkan.');
                header('Location: /kamar/index');
                exit;
            }

            Session::setFlash('error', $v->firstError());
        }

        require_once __DIR__ . '/../../views/kamar/create.php';
    }

    public function edit(int $id): void
    {
        Auth::role(['admin', 'pemilik']);

        $kamar = $this->kamar->find($id);
        if (!$kamar) {
            http_response_code(404);
            require_once __DIR__ . '/../../views/errors/404.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                Session::setFlash('error', 'Token tidak valid.');
                require_once __DIR__ . '/../../views/kamar/edit.php';
                return;
            }

            $v = new Validator();
            $v->required('nomor_kamar', $_POST['nomor_kamar'], 'Nomor kamar');
            $v->numeric('harga', $_POST['harga'], 'Harga');

            $foto = $kamar['foto'];

            if (isset($_POST['hapus_foto']) && $_POST['hapus_foto'] == '1') {
                $this->uploader->delete($kamar['foto']);
                $foto = null;
            }

            $newFoto = $this->uploader->upload($_FILES['foto'] ?? [], 'kamar');
            if ($newFoto !== null) {
                $this->uploader->delete($foto);
                $foto = $newFoto;
            }

            if ($v->passes()) {
                $_POST['foto'] = $foto;
                $_POST['fasilitas'] = implode(', ', $_POST['fasilitas'] ?? []);
                $this->kamar->update($id, $_POST);
                Session::setFlash('success', 'Kamar berhasil diupdate.');
                header('Location: /kamar/index');
                exit;
            }

            Session::setFlash('error', $v->firstError());
        }

        require_once __DIR__ . '/../../views/kamar/edit.php';
    }

    public function delete(int $id): void
    {
        Auth::role(['admin', 'pemilik']);

        $kamar = $this->kamar->find($id);
        if ($kamar) {
            $this->uploader->delete($kamar['foto']);
        }
        $this->kamar->delete($id);
        Session::setFlash('success', 'Kamar berhasil dihapus.');
        header('Location: /kamar/index');
        exit;
    }

    public function detail(int $id): void
    {
        // Semua role bisa lihat detail kamar
        Auth::role(['admin', 'pemilik', 'penyewa']);

        $kamar = $this->kamar->find($id);
        if (!$kamar) {
            http_response_code(404);
            require_once __DIR__ . '/../../views/errors/404.php';
            return;
        }
        require_once __DIR__ . '/../../views/kamar/detail.php';
    }
}