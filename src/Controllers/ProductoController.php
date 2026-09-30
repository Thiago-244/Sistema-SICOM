<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\SmartStorage;
use App\Helpers\Mailer;
use App\Models\Producto;
use App\Models\Solicitud;
use Exception;

class ProductoController {
    public function storeProduct(): void {
        if (!Auth::check()) {
            header('Location: ' . $this->baseUrl() . '/login');
            exit;
        }

        $solicitudId = (int)$_POST['solicitud_id'];
        $tipoId = (int)$_POST['tipo_producto_id'];
        $titulo = trim($_POST['titulo'] ?? '');
        $comunicadorId = (int)$_POST['comunicador_id'];

        if (!$solicitudId || !$tipoId || empty($titulo)) {
            Session::flash('error', 'Complete todos los campos del producto.');
            header('Location: ' . $this->baseUrl() . '/solicitudes/detalle?id=' . $solicitudId);
            exit;
        }

        $id = Producto::create([
            'solicitud_id' => $solicitudId,
            'tipo_producto_id' => $tipoId,
            'titulo' => $titulo,
            'comunicador_id' => $comunicadorId
        ]);

        Session::flash('success', 'Contenedor de producto creado. Ahora puede subir su primera versión.');
        header('Location: ' . $this->baseUrl() . '/solicitudes/detalle?id=' . $solicitudId);
        exit;
    }

    public function uploadVersion(): void {
        if (!Auth::check()) {
            header('Location: ' . $this->baseUrl() . '/login');
            exit;
        }

        $productoId = (int)$_POST['producto_id'];
        $solicitudId = (int)$_POST['solicitud_id'];
        $enlacePublicacion = trim($_POST['enlace_publicacion'] ?? '');
        $observaciones = trim($_POST['observaciones_comunicador'] ?? '');
        $tipoIngreso = $_POST['tipo_ingreso'] ?? 'archivo'; // 'archivo' o 'enlace'

        try {
            if ($tipoIngreso === 'enlace' || !empty($_POST['url_nube'])) {
                $fileMeta = SmartStorage::processCloudLink(trim($_POST['url_nube']));
            } elseif (isset($_FILES['archivo_evidencia']) && $_FILES['archivo_evidencia']['error'] === UPLOAD_ERR_OK) {
                $fileMeta = SmartStorage::storeLocalFile($_FILES['archivo_evidencia']);
            } else {
                throw new Exception("Debe adjuntar un archivo válido o ingresar un enlace a la nube.");
            }

            Producto::addVersion([
                'producto_id' => $productoId,
                'nombre_original' => $fileMeta['nombre_original'],
                'ruta' => $fileMeta['ruta'],
                'mime' => $fileMeta['mime'],
                'tamano_bytes' => $fileMeta['tamano_bytes'],
                'hash_sha256' => $fileMeta['hash_sha256'],
                'enlace_publicacion' => $enlacePublicacion,
                'observaciones' => $observaciones,
                'creado_por' => Auth::user()['id']
            ]);

            $solicitud = Solicitud::find($solicitudId);
            Mailer::send(
                'rrpp.jefatura@drep.gob.pe',
                "Nueva Versión Entregada para Revisión [{$solicitud['codigo_ticket']}]",
                "<p>El comunicador ha subido una nueva versión para evaluación en el Ticket <strong>{$solicitud['codigo_ticket']}</strong>.</p>"
            );

            Session::flash('success', '¡Versión del producto subida exitosamente para revisión!');
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());
        }

        header('Location: ' . $this->baseUrl() . '/solicitudes/detalle?id=' . $solicitudId);
        exit;
    }

    public function evaluate(): void {
        if (!Auth::check() || Auth::user()['rol_id'] == 2) {
            header('Location: ' . $this->baseUrl() . '/dashboard');
            exit;
        }

        $productoId = (int)$_POST['producto_id'];
        $solicitudId = (int)$_POST['solicitud_id'];
        $decision = $_POST['decision']; // 'Aprobar' o 'Observar'
        $observaciones = trim($_POST['observaciones_jefatura'] ?? '');

        Producto::evaluateVersion($productoId, $decision, $observaciones, Auth::user()['id']);

        $solicitud = Solicitud::find($solicitudId);
        if (!empty($solicitud['comunicador_email'])) {
            $estadoTxt = ($decision === 'Aprobar') ? 'APROBADO' : 'OBSERVADO CON CORRECCIONES';
            Mailer::send(
                $solicitud['comunicador_email'],
                "Resultado de Revisión: Producto {$estadoTxt} [{$solicitud['codigo_ticket']}]",
                "<p>Su producto en el Ticket <strong>{$solicitud['codigo_ticket']}</strong> ha sido marcado como <strong>{$estadoTxt}</strong>.</p><p>Observaciones: {$observaciones}</p>"
            );
        }

        Session::flash('success', "Evaluación registrada. Estado actualizado a {$decision}.");
        header('Location: ' . $this->baseUrl() . '/solicitudes/detalle?id=' . $solicitudId);
        exit;
    }

    private function baseUrl(): string {
        $config = require __DIR__ . '/../../config/database.php';
        return $config['app_url'];
    }
}
