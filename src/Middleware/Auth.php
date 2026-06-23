<?php

namespace App\Middleware;

use App\Helpers\Session;

class Auth
{
    public static function check(): void
    {
        if (!Session::has('user_id')) {
            Session::setFlash('error', 'Silakan login terlebih dahulu.');
            header('Location: /auth/login');
            exit;
        }
    }

    public static function role(array $roles): void
    {
        self::check();

        $userRole = Session::get('user_role');
        if (!in_array($userRole, $roles)) {
            http_response_code(403);
            require_once __DIR__ . '/../../views/errors/403.php';
            exit;
        }
    }

    public static function isLoggedIn(): bool
    {
        return Session::has('user_id');
    }

    public static function getUserRole(): ?string
    {
        return Session::get('user_role');
    }

    public static function getUserId(): ?int
    {
        return Session::get('user_id');
    }

    public static function getUserName(): ?string
    {
        return Session::get('user_nama');
    }
}
