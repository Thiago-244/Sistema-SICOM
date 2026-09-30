<?php

namespace App\Models;

use App\Database;
use App\Helpers\AuditLogger;

class Producto {
    public static function getBySolicitud(int $solicitudId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT 
                p.*,
                tp.nombre as tipo_producto_nombre,
                c.nombres as comunicador_nombres,
                c.apellidos as comunicador_apellidos,
                (SELECT COUNT(*) FROM producto_versiones pv WHERE pv.producto_id = p.id) as total_versiones,
                (SELECT pv.archivo_ruta FROM producto_versiones pv WHERE pv.producto_id = p.id ORDER BY pv.numero_version DESC LIMIT 1) as ultima_ruta,
                (SELECT pv.enlace_publicacion FROM producto_versiones pv WHERE pv.producto_id = p.id ORDER BY pv.numero_version DESC LIMIT 1) as ultimo_enlace
            FROM productos p
            JOIN tipos_producto tp ON p.tipo_producto_id = tp.id
            JOIN comunicadores c ON p.comunicador_id = c.id
            WHERE p.solicitud_id = :sid
            ORDER BY p.id DESC
        ");
        $stmt->execute(['sid' => $solicitudId]);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT p.*, tp.nombre as tipo_producto_nombre
            FROM productos p
            JOIN tipos_producto tp ON p.tipo_producto_id = tp.id
            WHERE p.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $producto = $stmt->fetch();
        if (!$producto) return null;

        // Cargar versiones
        $vStmt = $db->prepare("
            SELECT pv.*, u.username as evaluador_username, cu.username as creador_username
            FROM producto_versiones pv
            LEFT JOIN users u ON pv.evaluado_por = u.id
            JOIN users cu ON pv.creado_por = cu.id
            WHERE pv.producto_id = :pid
            ORDER BY pv.numero_version DESC
        ");
        $vStmt->execute(['pid' => $id]);
        $producto['versiones'] = $vStmt->fetchAll();

        return $producto;
    }

    public static function create(array $data): int {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO productos (solicitud_id, tipo_producto_id, titulo, comunicador_id, estado_producto, created_at)
            VALUES (:sid, :tipo_id, :titulo, :cid, 'Borrador', NOW())
        ");
        $stmt->execute([
            'sid' => $data['solicitud_id'],
            'tipo_id' => $data['tipo_producto_id'],
            'titulo' => $data['titulo'],
            'cid' => $data['comunicador_id']
        ]);
        $id = (int)$db->lastInsertId();
        AuditLogger::log('PRODUCTO_CREADO', 'productos', $id, null, ['titulo' => $data['titulo']]);
        return $id;
    }

    public static function addVersion(array $data): int {
        $db = Database::getInstance();

        // Obtener último número de versión
        $vStmt = $db->prepare("SELECT MAX(numero_version) FROM producto_versiones WHERE producto_id = :pid");
        $vStmt->execute(['pid' => $data['producto_id']]);
        $lastVer = (int)$vStmt->fetchColumn();
        $newVersion = $lastVer + 1;

        $stmt = $db->prepare("
            INSERT INTO producto_versiones (
                producto_id, numero_version, archivo_nombre_orig, archivo_ruta, archivo_mime,
                archivo_tamano_bytes, archivo_hash_sha256, enlace_publicacion, observaciones_revision, creado_por, created_at
            ) VALUES (
                :pid, :num, :orig, :ruta, :mime,
                :bytes, :hash, :enlace, :obs, :creado_por, NOW()
            )
        ");

        $stmt->execute([
            'pid' => $data['producto_id'],
            'num' => $newVersion,
            'orig' => $data['nombre_original'],
            'ruta' => $data['ruta'],
            'mime' => $data['mime'],
            'bytes' => $data['tamano_bytes'],
            'hash' => $data['hash_sha256'],
            'enlace' => $data['enlace_publicacion'] ?? null,
            'obs' => $data['observaciones'] ?? 'Nueva entrega de versión',
            'creado_por' => $data['creado_por']
        ]);

        $versionId = (int)$db->lastInsertId();

        // Actualizar estado del producto a 'En Revision'
        $db->prepare("UPDATE productos SET estado_producto = 'En Revision', updated_at = NOW() WHERE id = :pid")
           ->execute(['pid' => $data['producto_id']]);

        // Actualizar estado de la solicitud a 'En Revision'
        $p = self::find($data['producto_id']);
        if ($p) {
            Solicitud::updateStatus($p['solicitud_id'], 'En Revision');
        }

        AuditLogger::log('PRODUCTO_NUEVA_VERSION', 'producto_versiones', $versionId, null, [
            'producto_id' => $data['producto_id'],
            'version' => $newVersion
        ]);

        return $versionId;
    }

    public static function evaluateVersion(int $productoId, string $decision, ?string $observaciones, int $evaluadoPor): bool {
        $db = Database::getInstance();

        $nuevoEstadoProducto = ($decision === 'Aprobar') ? 'Aprobado' : 'Observado';
        $nuevoEstadoSolicitud = ($decision === 'Aprobar') ? 'Aprobada' : 'Observada';

        // Actualizar estado del producto
        $updP = $db->prepare("UPDATE productos SET estado_producto = :estado, updated_at = NOW() WHERE id = :pid");
        $updP->execute(['estado' => $nuevoEstadoProducto, 'pid' => $productoId]);

        // Actualizar última versión con observaciones y evaluador
        $vStmt = $db->prepare("SELECT id FROM producto_versiones WHERE producto_id = :pid ORDER BY numero_version DESC LIMIT 1");
        $vStmt->execute(['pid' => $productoId]);
        $lastVersionId = $vStmt->fetchColumn();

        if ($lastVersionId) {
            $updV = $db->prepare("
                UPDATE producto_versiones 
                SET observaciones_revision = :obs, evaluado_por = :ev, fecha_evaluacion = NOW()
                WHERE id = :vid
            ");
            $updV->execute(['obs' => $observaciones, 'ev' => $evaluadoPor, 'vid' => $lastVersionId]);
        }

        // Actualizar estado de la solicitud
        $p = self::find($productoId);
        if ($p) {
            Solicitud::updateStatus($p['solicitud_id'], $nuevoEstadoSolicitud);
        }

        AuditLogger::log('PRODUCTO_EVALUADO', 'productos', $productoId, null, [
            'decision' => $decision,
            'observaciones' => $observaciones,
            'nuevo_estado' => $nuevoEstadoProducto
        ]);

        return true;
    }
}
