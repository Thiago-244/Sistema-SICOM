<?php

namespace App\Helpers;

use App\Database;
use PDO;

class RuleEngine {
    /**
     * Calcula la matriz de compatibilidad de asignación para una solicitud dada.
     */
    public static function evaluateCandidates(int $solicitudId): array {
        $db = Database::getInstance();

        // Obtener detalles de la solicitud
        $sStmt = $db->prepare("SELECT * FROM solicitudes WHERE id = :id LIMIT 1");
        $sStmt->execute(['id' => $solicitudId]);
        $solicitud = $sStmt->fetch();

        if (!$solicitud) {
            return [];
        }

        // Obtener todos los comunicadores activos con sus especialidades y carga actual
        $sql = "
            SELECT 
                c.id as comunicador_id,
                c.nombres,
                c.apellidos,
                c.cargo,
                c.disponibilidad,
                d.nombre as dependencia_nombre,
                u.email,
                (
                    SELECT COUNT(*) 
                    FROM asignaciones a 
                    JOIN solicitudes s ON a.solicitud_id = s.id 
                    WHERE a.comunicador_id = c.id 
                    AND a.es_activa = 1 
                    AND s.estado_solicitud IN ('Asignada', 'En Proceso', 'En Revision', 'Observada')
                ) as carga_trabajo_activa,
                GROUP_CONCAT(e.nombre SEPARATOR ', ') as especialidades_lista,
                GROUP_CONCAT(e.id) as especialidades_ids
            FROM comunicadores c
            JOIN users u ON c.user_id = u.id
            JOIN dependencias d ON c.dependencia_id = d.id
            LEFT JOIN comunicador_especialidad ce ON c.id = ce.comunicador_id
            LEFT JOIN especialidades e ON ce.especialidad_id = e.id
            WHERE c.estado = 'Activo' AND u.estado = 'Activo'
            GROUP BY c.id
        ";
        
        $candidatos = $db->query($sql)->fetchAll();
        $resultados = [];

        foreach ($candidatos as $cand) {
            $score = 50; // Puntaje base
            $motivos = [];

            // 1. Evaluación de Disponibilidad
            if ($cand['disponibilidad'] === 'Disponible') {
                $score += 30;
                $motivos[] = "Estado de disponibilidad libre (+30)";
            } else {
                $score -= 40;
                $motivos[] = "Comunicador ocupado o en licencia (-40)";
            }

            // 2. Penalización por carga de trabajo actual
            $carga = (int)$cand['carga_trabajo_activa'];
            if ($carga === 0) {
                $score += 20;
                $motivos[] = "Sin tareas activas pendientes (+20)";
            } else {
                $penalizacion = $carga * 12;
                $score -= $penalizacion;
                $motivos[] = "Carga activa de {$carga} tareas (-{$penalizacion})";
            }

            // 3. Prioridad de la solicitud
            if (in_array($solicitud['prioridad'], ['Alta', 'Urgente'])) {
                if ($carga < 2) {
                    $score += 15;
                    $motivos[] = "Capacidad para atender prioridad Alta/Urgente (+15)";
                }
            }

            // Limitar score entre 0 y 100
            $finalScore = max(0, min(100, $score));

            // Nivel de recomendación
            $recomendacion = 'Baja';
            if ($finalScore >= 80) $recomendacion = 'Excelente';
            elseif ($finalScore >= 60) $recomendacion = 'Buena';
            elseif ($finalScore >= 40) $recomendacion = 'Aceptable';

            $resultados[] = [
                'comunicador_id' => (int)$cand['comunicador_id'],
                'nombre_completo' => $cand['nombres'] . ' ' . $cand['apellidos'],
                'cargo' => $cand['cargo'],
                'email' => $cand['email'],
                'disponibilidad' => $cand['disponibilidad'],
                'carga_activa' => $carga,
                'especialidades' => $cand['especialidades_lista'] ?? 'Sin definir',
                'score' => $finalScore,
                'recomendacion' => $recomendacion,
                'desglose' => implode(' | ', $motivos)
            ];
        }

        // Ordenar candidatos por el mayor puntaje de compatibilidad
        usort($resultados, fn($a, $b) => $b['score'] <=> $a['score']);

        return $resultados;
    }
}
