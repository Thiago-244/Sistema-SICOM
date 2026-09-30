<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Mailer;
use App\Models\Asignacion;
use App\Models\Solicitud;
use App\Models\Comunicador;
use App\Helpers\RuleEngine;

class AsignacionController {
    public function index(): void {
        if (!Auth::check() || Auth::user()['rol_id'] == 2) {
            header('Location: ' . $this->baseUrl() . '/dashboard');
            exit;
        }

        $pendientes = Solicitud::all(['estado' => 'Registrada']);
        $matrizAsignacion = [];

        foreach ($pendientes as $sol) {
            $matrizAsignacion[] = [
                'solicitud' => $sol,
                'candidatos' => RuleEngine::evaluateCandidates((int)$sol['id'])
            ];
        }

        require __DIR__ . '/../Views/asignaciones/index.php';
    }

    public function assign(): void {
        if (!Auth::check() || Auth::user()['rol_id'] == 2) {
            header('Location: ' . $this->baseUrl() . '/dashboard');
            exit;
        }

        $solicitudId = (int)$_POST['solicitud_id'];
        $comunicadorId = (int)$_POST['comunicador_id'];
        $motivo = trim($_POST['motivo_reasignacion'] ?? '');

        if (!$solicitudId || !$comunicadorId) {
            Session::flash('error', 'Selección de solicitud y comunicador requerida.');
            header('Location: ' . $this->baseUrl() . '/solicitudes/detalle?id=' . $solicitudId);
            exit;
        }

        $user = Auth::user();
        Asignacion::assign($solicitudId, $comunicadorId, $user['id'], $motivo);

        $solicitud = Solicitud::find($solicitudId);
        $comunicador = Comunicador::find($comunicadorId);

        // Notificar al comunicador por correo
        if ($comunicador && !empty($comunicador['email'])) {
            $subject = "Asignación de Tarea Comunicacional [{$solicitud['codigo_ticket']}]";
            $html = "<p>Estimado/a <strong>{$comunicador['nombres']} {$comunicador['apellidos']}</strong>,</p>"
                  . "<p>Se le ha asignado oficialmente la atención de la siguiente solicitud comunicacional:</p>"
                  . "<ul>"
                  . "<li><strong>Ticket:</strong> {$solicitud['codigo_ticket']}</li>"
                  . "<li><strong>Actividad:</strong> {$solicitud['titulo_actividad']}</li>"
                  . "<li><strong>Fecha Límite:</strong> {$solicitud['fecha_limite']}</li>"
                  . "</ul>"
                  . "<p><a href='{$this->baseUrl()}/solicitudes/detalle?id={$solicitudId}'>Ingresar a SICOM para subir avance</a></p>";

            Mailer::send($comunicador['email'], $subject, $html);
        }

        Session::flash('success', "Comunicador asignado exitosamente a la solicitud {$solicitud['codigo_ticket']}.");
        header('Location: ' . $this->baseUrl() . '/solicitudes/detalle?id=' . $solicitudId);
        exit;
    }

    private function baseUrl(): string {
        $config = require __DIR__ . '/../../config/database.php';
        return $config['app_url'];
    }
}
