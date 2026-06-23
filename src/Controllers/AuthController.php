<?php

namespace App\Controllers;

use App\Models\User;
use App\Middleware\Auth;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Validator;

class AuthController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login(): void
    {
        if (Auth::isLoggedIn()) {
            header('Location: /dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $v = new Validator();
            $v->required('email', $_POST['email'] ?? '', 'Email');
            $v->required('password', $_POST['password'] ?? '', 'Password');

            if ($v->passes()) {
                $userModel = new User();
                $user = $userModel->findByEmail($_POST['email']);

                if ($user && password_verify($_POST['password'], $user['password'])) {
                    Session::set('user_id', $user['id']);
                    Session::set('user_nama', $user['username']);
                    Session::set('user_role', $user['role']);
                    Session::set('last_activity', time());

                    Session::setFlash('success', 'Selamat datang, ' . $user['username'] . '!');
                    header('Location: /dashboard');
                    exit;
                }

                Session::setFlash('error', 'Email atau password salah.');
            } else {
                Session::setFlash('error', $v->firstError());
            }
        }

        require_once __DIR__ . '/../../views/auth/login.php';
    }

    public function register(): void
    {
        if (Auth::isLoggedIn()) {
            header('Location: /dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $v = new Validator();
            $v->required('username', $_POST['username'] ?? '', 'Username');
            $v->required('email', $_POST['email'] ?? '', 'Email');
            $v->email('email', $_POST['email'] ?? '', 'Email');
            $v->required('password', $_POST['password'] ?? '', 'Password');
            $v->minLength('password', $_POST['password'] ?? '', 6, 'Password');

            if ($v->passes()) {
                $userModel = new User();
                if ($userModel->findByEmail($_POST['email'])) {
                    Session::setFlash('error', 'Email sudah terdaftar.');
                } else {
                    $_POST['role'] = 'penyewa';
                    $userModel->create($_POST);
                    Session::setFlash('success', 'Registrasi berhasil. Silakan login.');
                    header('Location: /auth/login');
                    exit;
                }
            } else {
                Session::setFlash('error', $v->firstError());
            }
        }

        require_once __DIR__ . '/../../views/auth/register.php';
    }

    public function logout(): void
    {
        Session::destroy();
        header('Location: /auth/login');
        exit;
    }

    public function profile(): void
    {
        Auth::check();
        $userModel = new User();
        $user = $userModel->find(Auth::getUserId());

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $v = new Validator();
            $v->required('username', $_POST['username'], 'Username');
            $v->required('email', $_POST['email'], 'Email');
            $v->email('email', $_POST['email'], 'Email');

            if (!empty($_POST['password'])) {
                $v->minLength('password', $_POST['password'], 6, 'Password');
            }

            if ($v->passes()) {
                $userModel->update(Auth::getUserId(), $_POST);
                Session::set('user_nama', $_POST['username']);
                Session::setFlash('success', 'Profil berhasil diupdate.');
                header('Location: /auth/profile');
                exit;
            }

            Session::setFlash('error', $v->firstError());
        }

        require_once __DIR__ . '/../../views/auth/profile.php';
    }
}
