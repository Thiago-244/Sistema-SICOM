<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\Solicitud;
use App\Models\Comunicador;
use App\Database;

class DashboardController {
    public function index(): void {
        if (!Auth::check()) {
            header('Location: ' . $this->baseUrl() . '/login');
            exit;
        }

        $user = Auth::user();
        if ($user['rol_id'] == 2) {
            header('Location: ' . $this->baseUrl() . '/mis-tareas');
            exit;
        }

        $stats = Solicitud::getStats();
        $recentSolicitudes = Solicitud::all(['limit' => 5]);
        $comunicadores = Comunicador::all();

        // Obtener logs recientes de auditoría
        $db = Database::getInstance();
        $auditLogs = $db->query("
            SELECT a.*, u.username 
            FROM auditoria_logs a 
            LEFT JOIN users u ON a.user_id = u.id 
            ORDER BY a.id DESC LIMIT 6
        ")->fetchAll();

        require __DIR__ . '/../Views/dashboard/index.php';
    }

    public function misTareas(): void {
        if (!Auth::check()) {
            header('Location: ' . $this->baseUrl() . '/login');
            exit;
        }

        $user = Auth::user();
        $comunicadorId = $user['comunicador_id'];

        $tareas = Solicitud::all(['comunicador_id' => $comunicadorId]);

        require __DIR__ . '/../Views/dashboard/comunicador.php';
    }

    private function baseUrl(): string {
        $config = require __DIR__ . '/../../config/database.php';
        return $config['app_url'];
    }
}
