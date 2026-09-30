<?php
$pageTitle = "Motor de Asignación de Responsables";
$pageSubtitle = "Algoritmo de puntuación ponderada y compatibilidad explicable";
require __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-wand-magic-sparkles" style="color: #0284c7;"></i> Evaluación Automatizada de Compatibilidad
        </div>
    </div>

    <?php if (empty($matrizAsignacion)): ?>
        <div style="text-align: center; color: #94a3b8; padding: 40px; background: #f8fafc; border-radius: 8px;">
            <i class="fa-solid fa-circle-check" style="font-size: 2.5rem; color: #16a34a; margin-bottom: 12px; display: block;"></i>
            No hay solicitudes pendientes de asignación en este momento. ¡Excelente trabajo!
        </div>
    <?php else: ?>
        <?php foreach ($matrizAsignacion as $item): 
            $sol = $item['solicitud'];
            $candidatos = $item['candidatos'];
        ?>
            <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; margin-bottom: 24px; background: #ffffff;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                    <div>
                        <span class="badge badge-prioridad-<?= strtolower($sol['prioridad']) ?>">Prioridad <?= $sol['prioridad'] ?></span>
                        <h3 style="font-size: 1.1rem; font-weight: 700; margin-top: 6px;">
                            Ticket <?= htmlspecialchars($sol['codigo_ticket']) ?>: <?= htmlspecialchars($sol['titulo_actividad']) ?>
                        </h3>
                        <div style="font-size: 0.8rem; color: #64748b;">
                            Solicitante: <strong><?= htmlspecialchars($sol['dependencia_nombre']) ?></strong> | Fecha Límite: <strong style="color: #dc2626;"><?= date('d/m/Y H:i', strtotime($sol['fecha_limite'])) ?></strong>
                        </div>
                    </div>
                    <a href="<?= $baseUrl ?>/solicitudes/detalle?id=<?= $sol['id'] ?>" class="btn btn-secondary" style="font-size: 0.8rem;">
                        <i class="fa-solid fa-eye"></i> Ver Solicitud
                    </a>
                </div>

                <h4 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; color: #475569; margin-bottom: 12px;">
                    Ranking de Comunicadores Recomendados por el Motor
                </h4>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Comunicador</th>
                                <th>Disponibilidad</th>
                                <th>Carga Activa</th>
                                <th>Score de Compatibilidad</th>
                                <th>Desglose de Reglas</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($candidatos as $cand): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 700;"><?= htmlspecialchars($cand['nombre_completo']) ?></div>
                                        <div style="font-size: 0.75rem; color: #64748b;"><?= htmlspecialchars($cand['cargo']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: <?= $cand['disponibilidad'] === 'Disponible' ? '#dcfce7' : '#fee2e2' ?>; color: <?= $cand['disponibilidad'] === 'Disponible' ? '#16a34a' : '#dc2626' ?>;">
                                            <?= $cand['disponibilidad'] ?>
                                        </span>
                                    </td>
                                    <td><strong><?= $cand['carga_activa'] ?></strong> tareas</td>
                                    <td>
                                        <div style="font-weight: 800; font-size: 1.1rem; color: #0284c7;">
                                            <?= $cand['score'] ?> / 100
                                        </div>
                                        <div style="font-size: 0.72rem; color: #64748b; font-weight: 600;">
                                            Recomendación <?= $cand['recomendacion'] ?>
                                        </div>
                                    </td>
                                    <td style="font-size: 0.78rem; color: #475569;">
                                        <?= htmlspecialchars($cand['desglose']) ?>
                                    </td>
                                    <td>
                                        <form action="<?= $baseUrl ?>/asignaciones/ejecutar" method="POST">
                                            <input type="hidden" name="solicitud_id" value="<?= $sol['id'] ?>">
                                            <input type="hidden" name="comunicador_id" value="<?= $cand['comunicador_id'] ?>">
                                            <button type="submit" class="btn btn-primary" style="padding: 4px 12px; font-size: 0.78rem;">
                                                <i class="fa-solid fa-user-check"></i> Asignar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
