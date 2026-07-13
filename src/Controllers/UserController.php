<?php

namespace App\Controllers;

use App\Models\User;
use App\Middleware\Auth;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Validator;
use App\Models\Kontrak;

class UserController
{
    private User $user;

    public function __construct()
    {
        Auth::check();
        Auth::role(['admin']);
        $this->user = new User();
    }

    public function index(): void
    {
        $data = $this->user->getAll();
        require_once __DIR__ . '/../../views/user/index.php';
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                Session::setFlash('error', 'Token tidak valid.');
                require_once __DIR__ . '/../../views/user/create.php';
                return;
            }

            $v = new Validator();
            $v->required('username', $_POST['username'], 'Username');
            $v->required('email', $_POST['email'], 'Email');
            $v->email('email', $_POST['email'], 'Email');
            $v->required('password', $_POST['password'], 'Password');
            $v->minLength('password', $_POST['password'], 6, 'Password');

            if ($v->passes()) {
                if ($this->user->findByEmail($_POST['email'])) {
                    Session::setFlash('error', 'Email sudah terdaftar.');
                } elseif ($this->user->findByUsername($_POST['username'])) {
                    Session::setFlash('error', 'Username sudah digunakan.');
                } else {
                    $this->user->create($_POST);
                    Session::setFlash('success', 'User berhasil ditambahkan.');
                    header('Location: /user/index');
                    exit;
                }
            } else {
                Session::setFlash('error', $v->firstError());
            }
        }

        require_once __DIR__ . '/../../views/user/create.php';
    }

    public function edit(int $id): void
    {
        $user = $this->user->find($id);
        if (!$user) {
            http_response_code(404);
            require_once __DIR__ . '/../../views/errors/404.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                Session::setFlash('error', 'Token tidak valid.');
                require_once __DIR__ . '/../../views/user/edit.php';
                return;
            }

            $v = new Validator();
            $v->required('username', $_POST['username'], 'Username');
            $v->required('email', $_POST['email'], 'Email');

            if (!empty($_POST['password'])) {
                $v->minLength('password', $_POST['password'], 6, 'Password');
            }

            if ($v->passes()) {
                if ($id === Auth::getUserId() && isset($_POST['role']) && $_POST['role'] !== Auth::getUserRole()) {
                    Session::setFlash('error', 'Tidak bisa mengubah role akun sendiri.');
                    require_once __DIR__ . '/../../views/user/edit.php';
                    return;
                }
                $this->user->update($id, $_POST);
                Session::setFlash('success', 'User berhasil diupdate.');
                header('Location: /user/index');
                exit;
            }

            Session::setFlash('error', $v->firstError());
        }

        require_once __DIR__ . '/../../views/user/edit.php';
    }

    public function delete(int $id): void
    {
        if ($id === Auth::getUserId()) {
            Session::setFlash('error', 'Tidak bisa menghapus akun sendiri.');
            header('Location: /user/index');
            exit;
        }

        $kontrakModel = new Kontrak();
        if ($kontrakModel->hasActiveByPenyewaId($id)) {
            Session::setFlash('error', 'User tidak bisa dihapus karena masih memiliki kontrak aktif.');
            header('Location: /user/index');
            exit;
        }

        $this->user->delete($id);
        Session::setFlash('success', 'User berhasil dihapus.');
        header('Location: /user/index');
        exit;
    }
}
