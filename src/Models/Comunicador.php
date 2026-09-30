<?php

namespace App\Models;

use App\Database;

class Comunicador {
    public static function all(): array {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT 
                c.*,
                u.username,
                u.email,
                d.nombre as dependencia_nombre,
                (SELECT COUNT(*) FROM asignaciones a JOIN solicitudes s ON a.solicitud_id = s.id WHERE a.comunicador_id = c.id AND a.es_activa = 1 AND s.estado_solicitud IN ('Asignada', 'En Proceso', 'En Revision', 'Observada')) as carga_activa,
                GROUP_CONCAT(e.nombre SEPARATOR ', ') as especialidades_lista
            FROM comunicadores c
            JOIN users u ON c.user_id = u.id
            JOIN dependencias d ON c.dependencia_id = d.id
            LEFT JOIN comunicador_especialidad ce ON c.id = ce.comunicador_id
            LEFT JOIN especialidades e ON ce.especialidad_id = e.id
            WHERE c.estado = 'Activo'
            GROUP BY c.id
            ORDER BY c.id DESC
        ");
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT c.*, u.username, u.email, d.nombre as dependencia_nombre
            FROM comunicadores c
            JOIN users u ON c.user_id = u.id
            JOIN dependencias d ON c.dependencia_id = d.id
            WHERE c.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }
}
