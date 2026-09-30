<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Mailer;
use App\Models\Solicitud;
use App\Models\Dependencia;
use App\Models\Producto;
use App\Models\Comunicador;
use App\Helpers\RuleEngine;

class SolicitudController {
    public function index(): void {
        if (!Auth::check()) {
            header('Location: ' . $this->baseUrl() . '/login');
            exit;
        }

        $filters = [
            'estado' => $_GET['estado'] ?? '',
            'prioridad' => $_GET['prioridad'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        $solicitudes = Solicitud::all($filters);
        require __DIR__ . '/../Views/solicitudes/index.php';
    }

    public function create(): void {
        if (!Auth::check()) {
            header('Location: ' . $this->baseUrl() . '/login');
            exit;
        }

        $dependencias = Dependencia::all();
        require __DIR__ . '/../Views/solicitudes/create.php';
    }

    public function store(): void {
        if (!Auth::check()) {
            header('Location: ' . $this->baseUrl() . '/login');
            exit;
        }

        $data = [
            'dependencia_solicitante_id' => (int)$_POST['dependencia_solicitante_id'],
            'titulo_actividad' => trim($_POST['titulo_actividad'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'lugar' => trim($_POST['lugar'] ?? ''),
            'fecha_evento' => $_POST['fecha_evento'],
            'prioridad' => $_POST['prioridad'] ?? 'Media',
            'fecha_limite' => $_POST['fecha_limite'],
            'creado_por' => Auth::user()['id']
        ];

        if (empty($data['titulo_actividad']) || empty($data['descripcion'])) {
            Session::flash('error', 'Por favor complete todos los campos obligatorios.');
            header('Location: ' . $this->baseUrl() . '/solicitudes/crear');
            exit;
        }

        $id = Solicitud::create($data);
        $solicitud = Solicitud::find($id);

        // Notificar por correo
        $subject = "Nueva Solicitud Registrada [{$solicitud['codigo_ticket']}]: {$data['titulo_actividad']}";
        $html = "<p>Se ha registrado una nueva solicitud de cobertura comunicacional en el Sistema SICOM.</p>"
              . "<ul>"
              . "<li><strong>Ticket:</strong> {$solicitud['codigo_ticket']}</li>"
              . "<li><strong>Dependencia:</strong> {$solicitud['dependencia_nombre']}</li>"
              . "<li><strong>Prioridad:</strong> {$data['prioridad']}</li>"
              . "<li><strong>Fecha Límite:</strong> {$data['fecha_limite']}</li>"
              . "</ul>"
              . "<p><a href='{$this->baseUrl()}/solicitudes/detalle?id={$id}'>Ver Solicitud en SICOM</a></p>";

        Mailer::send('rrpp.jefatura@drep.gob.pe', $subject, $html);

        Session::flash('success', "Solicitud registrada con éxito. Ticket generado: {$solicitud['codigo_ticket']}");
        header('Location: ' . $this->baseUrl() . '/solicitudes/detalle?id=' . $id);
        exit;
    }

    public function detail(): void {
        if (!Auth::check()) {
            header('Location: ' . $this->baseUrl() . '/login');
            exit;
        }

        $id = (int)($_GET['id'] ?? 0);
        $solicitud = Solicitud::find($id);

        if (!$solicitud) {
            Session::flash('error', 'Solicitud no encontrada.');
            header('Location: ' . $this->baseUrl() . '/solicitudes');
            exit;
        }

        $productos = Producto::getBySolicitud($id);
        $evaluacionesReglas = RuleEngine::evaluateCandidates($id);
        $comunicadores = Comunicador::all();

        require __DIR__ . '/../Views/solicitudes/detail.php';
    }

    private function baseUrl(): string {
        $config = require __DIR__ . '/../../config/database.php';
        return $config['app_url'];
    }
}
