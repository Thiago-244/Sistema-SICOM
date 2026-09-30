<?php

namespace App\Models;

use App\Database;
use App\Helpers\AuditLogger;

class Asignacion {
    public static function assign(int $solicitudId, int $comunicadorId, int $asignadoPor, ?string $motivoReasignacion = null): bool {
        $db = Database::getInstance();

        // 1. Desactivar asignación previa si existía
        $db->prepare("UPDATE asignaciones SET es_activa = 0 WHERE solicitud_id = :sid")->execute(['sid' => $solicitudId]);

        // 2. Crear nueva asignación activa
        $stmt = $db->prepare("
            INSERT INTO asignaciones (solicitud_id, comunicador_id, fecha_asignacion, asignado_por, motivo_reasignacion, es_activa)
            VALUES (:sid, :cid, NOW(), :por, :motivo, 1)
        ");
        $res = $stmt->execute([
            'sid' => $solicitudId,
            'cid' => $comunicadorId,
            'por' => $asignadoPor,
            'motivo' => $motivoReasignacion
        ]);

        if ($res) {
            // Actualizar estado de la solicitud a 'Asignada'
            Solicitud::updateStatus($solicitudId, 'Asignada');
            AuditLogger::log('SOLICITUD_ASIGNADA', 'asignaciones', (int)$db->lastInsertId(), null, [
                'solicitud_id' => $solicitudId,
                'comunicador_id' => $comunicadorId,
                'motivo' => $motivoReasignacion
            ]);
        }

        return $res;
    }
}
