<?php

namespace App\Helpers;

use App\Database;
use Exception;

class AuditLogger {
    public static function log(string $accion, string $entidad, int $entidadId, ?array $datosPrevios = null, ?array $datosNuevos = null): void {
        try {
            $db = Database::getInstance();
            $userId = Session::get('user_id', null);
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'CLI', 0, 250);

            $stmt = $db->prepare("
                INSERT INTO auditoria_logs (user_id, accion, entidad, entidad_id, datos_previos, datos_nuevos, ip_address, user_agent, created_at)
                VALUES (:user_id, :accion, :entidad, :entidad_id, :prev, :nuev, :ip, :ua, NOW())
            ");

            $stmt->execute([
                'user_id' => $userId,
                'accion' => $accion,
                'entidad' => $entidad,
                'entidad_id' => $entidadId,
                'prev' => $datosPrevios ? json_encode($datosPrevios, JSON_UNESCAPED_UNICODE) : null,
                'nuev' => $datosNuevos ? json_encode($datosNuevos, JSON_UNESCAPED_UNICODE) : null,
                'ip' => $ipAddress,
                'ua' => $userAgent
            ]);
        } catch (Exception $e) {
            error_log("AuditLogger Failure: " . $e->getMessage());
        }
    }
}
