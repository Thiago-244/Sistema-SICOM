<?php

namespace App\Controllers;

use App\Models\Solicitud;
use App\Helpers\RuleEngine;

class ApiController {
    public function stats(): void {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(Solicitud::getStats());
    }

    public function evaluateAssignment(): void {
        header('Content-Type: application/json; charset=utf-8');
        $id = (int)($_GET['solicitud_id'] ?? 0);
        if (!$id) {
            echo json_encode(['error' => 'ID de solicitud no proporcionado']);
            return;
        }

        $scores = RuleEngine::evaluateCandidates($id);
        echo json_encode(['solicitud_id' => $id, 'candidatos' => $scores]);
    }
}
