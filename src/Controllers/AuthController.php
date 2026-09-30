<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Session;

class AuthController {
    public function login(): void {
        if (Auth::check()) {
            header('Location: ' . $this->getRedirectUrl());
            exit;
        }

        require __DIR__ . '/../Views/auth/login.php';
    }

    public function processLogin(): void {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            Session::flash('error', 'Por favor ingrese usuario y contraseña.');
            header('Location: ' . $this->baseUrl() . '/login');
            exit;
        }

        if (Auth::login($username, $password)) {
            Session::flash('success', '¡Bienvenido al Sistema SICOM - DREP!');
            header('Location: ' . $this->getRedirectUrl());
            exit;
        } else {
            Session::flash('error', 'Credenciales incorrectas o usuario inactivo.');
            header('Location: ' . $this->baseUrl() . '/login');
            exit;
        }
    }

    public function logout(): void {
        Auth::logout();
        header('Location: ' . $this->baseUrl() . '/login');
        exit;
    }

    private function getRedirectUrl(): string {
        $user = Auth::user();
        if ($user && $user['rol_id'] == 2) {
            return $this->baseUrl() . '/mis-tareas';
        }
        return $this->baseUrl() . '/dashboard';
    }

    private function baseUrl(): string {
        $config = require __DIR__ . '/../../config/database.php';
        return $config['app_url'];
    }
}
