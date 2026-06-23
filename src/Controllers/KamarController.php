<?php

namespace App\Controllers;

use App\Models\Kamar;
use App\Middleware\Auth;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Validator;

class KamarController
{
    private Kamar $kamar;

    public function __construct()
    {
        Auth::check();
        Auth::role(['admin', 'pemilik']);
        $this->kamar = new Kamar();
    }

    public function index(): void
    {
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                    move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/../../uploads/kamar/' . $foto);
                }
            }

            if ($v->passes()) {
                $_POST['foto'] = $foto;
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
        $kamar = $this->kamar->find($id);
        if (!$kamar) {
            http_response_code(404);
            require_once __DIR__ . '/../../views/errors/404.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $v = new Validator();
            $v->required('nomor_kamar', $_POST['nomor_kamar'], 'Nomor kamar');
            $v->numeric('harga', $_POST['harga'], 'Harga');

            $foto = $kamar['foto'];
            if (!empty($_FILES['foto']['name'])) {
                $v->file('foto', $_FILES['foto'], ['image/jpeg', 'image/png', 'image/jpg'], 2097152, 'Foto');
                if ($v->passes()) {
                    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                    $foto = 'kamar_' . time() . '.' . $ext;
                    move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/../../uploads/kamar/' . $foto);
                    if ($kamar['foto'] && file_exists(__DIR__ . '/../../uploads/kamar/' . $kamar['foto'])) {
                        unlink(__DIR__ . '/../../uploads/kamar/' . $kamar['foto']);
                    }
                }
            }

            if ($v->passes()) {
                $_POST['foto'] = $foto;
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
        $kamar = $this->kamar->find($id);
        if ($kamar && $kamar['foto'] && file_exists(__DIR__ . '/../../uploads/kamar/' . $kamar['foto'])) {
            unlink(__DIR__ . '/../../uploads/kamar/' . $kamar['foto']);
        }
        $this->kamar->delete($id);
        Session::setFlash('success', 'Kamar berhasil dihapus.');
        header('Location: /kamar/index');
        exit;
    }

    public function detail(int $id): void
    {
        $kamar = $this->kamar->find($id);
        if (!$kamar) {
            http_response_code(404);
            require_once __DIR__ . '/../../views/errors/404.php';
            return;
        }
        require_once __DIR__ . '/../../views/kamar/detail.php';
    }
}
