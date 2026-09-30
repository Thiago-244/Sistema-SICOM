<?php

namespace App\Controllers;

use App\Helpers\Session;
use App\Helpers\Mailer;
use App\Models\Solicitud;
use App\Models\Dependencia;

class PublicPortalController {
    public function index(): void {
        $dependencias = Dependencia::all();
        require __DIR__ . '/../Views/public/index.php';
    }

    public function storePublicRequest(): void {
        $data = [
            'dependencia_solicitante_id' => (int)$_POST['dependencia_solicitante_id'],
            'titulo_actividad' => trim($_POST['titulo_actividad'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'lugar' => trim($_POST['lugar'] ?? ''),
            'fecha_evento' => $_POST['fecha_evento'],
            'prioridad' => $_POST['prioridad'] ?? 'Media',
            'fecha_limite' => $_POST['fecha_limite'],
            'creado_por' => 1 // Registrado mediante Portal Público (Vía Admin/Sistema)
        ];

        if (empty($data['titulo_actividad']) || empty($data['descripcion'])) {
            Session::flash('error', 'Por favor complete todos los campos requeridos.');
            header('Location: ' . $this->baseUrl() . '/portal');
            exit;
        }

        $id = Solicitud::create($data);
        $solicitud = Solicitud::find($id);

        // Notificar por correo
        $subject = "NUEVA SOLICITUD PÚBLICA RECIBIDA [{$solicitud['codigo_ticket']}]: {$data['titulo_actividad']}";
        $html = "<p>Se ha recibido una nueva solicitud externa/institucional vía Portal Web.</p>"
              . "<ul>"
              . "<li><strong>Código Ticket:</strong> {$solicitud['codigo_ticket']}</li>"
              . "<li><strong>Origen:</strong> {$solicitud['dependencia_nombre']}</li>"
              . "<li><strong>Prioridad:</strong> {$data['prioridad']}</li>"
              . "</ul>";

        Mailer::send('rrpp.jefatura@drep.gob.pe', $subject, $html);

        Session::flash('ticket_creado', $solicitud['codigo_ticket']);
        header('Location: ' . $this->baseUrl() . '/portal/exito?ticket=' . $solicitud['codigo_ticket']);
        exit;
    }

    public function success(): void {
        $ticket = $_GET['ticket'] ?? '';
        require __DIR__ . '/../Views/public/success.php';
    }

    public function tracking(): void {
        $ticket = trim($_GET['ticket'] ?? '');
        $solicitud = null;
        if (!empty($ticket)) {
            $solicitud = Solicitud::findByTicket($ticket);
        }
        require __DIR__ . '/../Views/public/tracking.php';
    }

    private function baseUrl(): string {
        $config = require __DIR__ . '/../../config/database.php';
        return $config['app_url'];
    }
}
