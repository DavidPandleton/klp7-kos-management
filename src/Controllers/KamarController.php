<?php

namespace App\Controllers;

use App\Models\Kamar;
use App\Middleware\Auth;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Validator;
use App\Helpers\FileUploader;
use App\Models\Kontrak;

class KamarController
{
    private Kamar $kamar;
    private string $uploadPath;

    public function __construct()
    {
        Auth::check();
        $this->kamar = new Kamar();
        $this->uploadPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'kamar' . DIRECTORY_SEPARATOR;
    }

    public function index(): void
    {
        // Semua role bisa lihat daftar kamar
        Auth::role(['admin', 'pemilik', 'penyewa']);

        $status = $_GET['status'] ?? '';
        $filter = '';
        $params = [];

        if (Auth::getUserRole() === 'penyewa') {
            $filter .= " AND status = 'tersedia'";
        } elseif (!empty($status)) {
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

            $foto = null;
            if (!empty($_FILES['foto']['name'])) {
                $v->file('foto', $_FILES['foto'], ['image/jpeg', 'image/png', 'image/jpg'], 2097152, 'Foto');
                if ($v->passes()) {
                    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                    $foto = 'kamar_' . time() . '.' . $ext;
                    move_uploaded_file($_FILES['foto']['tmp_name'], $this->uploadPath . $foto);
                }
            }

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

            // Hapus foto jika dicentang
            if (isset($_POST['hapus_foto']) && $_POST['hapus_foto'] == '1') {
                if ($kamar['foto'] && file_exists($this->uploadPath . $kamar['foto'])) {
                    unlink($this->uploadPath . $kamar['foto']);
                }
                $foto = null;
            }

            // Upload foto baru jika ada
            if (!empty($_FILES['foto']['name'])) {
                $v->file('foto', $_FILES['foto'], ['image/jpeg', 'image/png', 'image/jpg'], 2097152, 'Foto');
                if ($v->passes()) {
                    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                    $newFoto = 'kamar_' . time() . '.' . $ext;
                    move_uploaded_file($_FILES['foto']['tmp_name'], $this->uploadPath . $newFoto);
                    if ($foto && file_exists($this->uploadPath . $foto)) {
                        unlink($this->uploadPath . $foto);
                    }
                    $foto = $newFoto;
                }
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
        if ($kamar && $kamar['foto'] && file_exists($this->uploadPath . $kamar['foto'])) {
            unlink($this->uploadPath . $kamar['foto']);
        }

        $this->uploader->delete($kamar['foto']);
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