<?php

namespace App\Models;

use App\Database;
use App\Helpers\AuditLogger;
use PDO;

class Solicitud {
    public static function all(array $filters = []): array {
        $db = Database::getInstance();
        $sql = "
            SELECT 
                s.*,
                d.nombre as dependencia_nombre,
                u.username as creador_username,
                c.nombres as comunicador_nombres,
                c.apellidos as comunicador_apellidos,
                a.id as asignacion_id,
                a.fecha_asignacion
            FROM solicitudes s
            JOIN dependencias d ON s.dependencia_solicitante_id = d.id
            JOIN users u ON s.creado_por = u.id
            LEFT JOIN asignaciones a ON s.id = a.solicitud_id AND a.es_activa = 1
            LEFT JOIN comunicadores c ON a.comunicador_id = c.id
            WHERE 1=1
        ";

        $params = [];

        if (!empty($filters['estado'])) {
            $sql .= " AND s.estado_solicitud = :estado";
            $params['estado'] = $filters['estado'];
        }

        if (!empty($filters['prioridad'])) {
            $sql .= " AND s.prioridad = :prioridad";
            $params['prioridad'] = $filters['prioridad'];
        }

        if (!empty($filters['comunicador_id'])) {
            $sql .= " AND a.comunicador_id = :comunicador_id";
            $params['comunicador_id'] = $filters['comunicador_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (s.titulo_actividad LIKE :search OR s.codigo_ticket LIKE :search OR d.nombre LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY s.id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $db = Database::getInstance();
        $sql = "
            SELECT 
                s.*,
                d.nombre as dependencia_nombre,
                d.siglas as dependencia_siglas,
                u.username as creador_username,
                u.email as creador_email,
                c.id as comunicador_id,
                c.nombres as comunicador_nombres,
                c.apellidos as comunicador_apellidos,
                cu.email as comunicador_email,
                a.id as asignacion_id,
                a.fecha_asignacion,
                a.motivo_reasignacion
            FROM solicitudes s
            JOIN dependencias d ON s.dependencia_solicitante_id = d.id
            JOIN users u ON s.creado_por = u.id
            LEFT JOIN asignaciones a ON s.id = a.solicitud_id AND a.es_activa = 1
            LEFT JOIN comunicadores c ON a.comunicador_id = c.id
            LEFT JOIN users cu ON c.user_id = cu.id
            WHERE s.id = :id
            LIMIT 1
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByTicket(string $ticketCode): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM solicitudes WHERE codigo_ticket = :ticket LIMIT 1");
        $stmt->execute(['ticket' => trim($ticketCode)]);
        $res = $stmt->fetch();
        return $res ? self::find((int)$res['id']) : null;
    }

    public static function create(array $data): int {
        $db = Database::getInstance();
        
        // Generar código de ticket único SOL-202609-XXXX
        $prefix = 'SOL-' . date('Ym') . '-';
        $randSeq = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
        $codigoTicket = $prefix . $randSeq;

        $stmt = $db->prepare("
            INSERT INTO solicitudes (
                codigo_ticket, dependencia_solicitante_id, titulo_actividad, descripcion, 
                lugar, fecha_evento, prioridad, fecha_limite, estado_solicitud, creado_por, created_at
            ) VALUES (
                :ticket, :dep_id, :titulo, :descripcion, 
                :lugar, :fecha_evento, :prioridad, :fecha_limite, 'Registrada', :creado_por, NOW()
            )
        ");

        $stmt->execute([
            'ticket' => $codigoTicket,
            'dep_id' => $data['dependencia_solicitante_id'],
            'titulo' => $data['titulo_actividad'],
            'descripcion' => $data['descripcion'],
            'lugar' => $data['lugar'] ?? 'Instalaciones DREP / Modalidad Virtual',
            'fecha_evento' => $data['fecha_evento'],
            'prioridad' => $data['prioridad'] ?? 'Media',
            'fecha_limite' => $data['fecha_limite'],
            'creado_por' => $data['creado_por']
        ]);

        $id = (int)$db->lastInsertId();
        AuditLogger::log('SOLICITUD_CREADA', 'solicitudes', $id, null, ['ticket' => $codigoTicket, 'titulo' => $data['titulo_actividad']]);
        return $id;
    }

    public static function updateStatus(int $id, string $nuevoEstado): bool {
        $db = Database::getInstance();
        $prev = self::find($id);
        if (!$prev) return false;

        $stmt = $db->prepare("UPDATE solicitudes SET estado_solicitud = :estado, updated_at = NOW() WHERE id = :id");
        $res = $stmt->execute(['estado' => $nuevoEstado, 'id' => $id]);

        if ($res) {
            AuditLogger::log('SOLICITUD_CAMBIO_ESTADO', 'solicitudes', $id, ['estado' => $prev['estado_solicitud']], ['estado' => $nuevoEstado]);
        }
        return $res;
    }

    public static function getStats(): array {
        $db = Database::getInstance();
        $total = $db->query("SELECT COUNT(*) FROM solicitudes")->fetchColumn();
        $registradas = $db->query("SELECT COUNT(*) FROM solicitudes WHERE estado_solicitud = 'Registrada'")->fetchColumn();
        $enProceso = $db->query("SELECT COUNT(*) FROM solicitudes WHERE estado_solicitud IN ('Asignada', 'En Proceso', 'En Revision', 'Observada')")->fetchColumn();
        $aprobadas = $db->query("SELECT COUNT(*) FROM solicitudes WHERE estado_solicitud IN ('Aprobada', 'Publicada', 'Cerrada')")->fetchColumn();
        $vencidas = $db->query("SELECT COUNT(*) FROM solicitudes WHERE fecha_limite < NOW() AND estado_solicitud NOT IN ('Publicada', 'Cerrada', 'Cancelada')")->fetchColumn();

        return [
            'total' => (int)$total,
            'registradas' => (int)$registradas,
            'en_proceso' => (int)$enProceso,
            'aprobadas' => (int)$aprobadas,
            'vencidas' => (int)$vencidas
        ];
    }
}
