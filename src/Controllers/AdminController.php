<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\User;
use App\Models\Comunicador;
use App\Database;

class AdminController {
    public function users(): void {
        if (!Auth::check() || Auth::user()['rol_id'] != 1 && Auth::user()['rol_id'] != 4) {
            header('Location: ' . $this->baseUrl() . '/dashboard');
            exit;
        }

        $usuarios = User::all();
        require __DIR__ . '/../Views/admin/users.php';
    }

    public function comunicadores(): void {
        if (!Auth::check() || Auth::user()['rol_id'] != 1 && Auth::user()['rol_id'] != 4) {
            header('Location: ' . $this->baseUrl() . '/dashboard');
            exit;
        }

        $comunicadores = Comunicador::all();
        require __DIR__ . '/../Views/admin/comunicadores.php';
    }

    public function auditLogs(): void {
        if (!Auth::check() || Auth::user()['rol_id'] != 1 && Auth::user()['rol_id'] != 4) {
            header('Location: ' . $this->baseUrl() . '/dashboard');
            exit;
        }

        $db = Database::getInstance();
        $logs = $db->query("
            SELECT a.*, u.username, u.email 
            FROM auditoria_logs a 
            LEFT JOIN users u ON a.user_id = u.id 
            ORDER BY a.id DESC LIMIT 100
        ")->fetchAll();

        require __DIR__ . '/../Views/admin/audit.php';
    }

    private function baseUrl(): string {
        $config = require __DIR__ . '/../../config/database.php';
        return $config['app_url'];
    }
}
