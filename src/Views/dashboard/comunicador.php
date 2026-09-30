<?php
$pageTitle = "Mis Tareas Asignadas";
$pageSubtitle = "Bandeja de producción comunicacional para el comunicador social";
require __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-list-check"></i> Solicitudes y Productos Pendientes de Entrega
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Ticket</th>
                    <th>Actividad</th>
                    <th>Dependencia Solicitante</th>
                    <th>Fecha Evento</th>
                    <th>Fecha Límite SLA</th>
                    <th>Estado Solicitud</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tareas)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #94a3b8; padding: 30px;">
                            <i class="fa-solid fa-circle-check" style="font-size: 2rem; color: #16a34a; margin-bottom: 10px; display: block;"></i>
                            Actualmente no tiene tareas comunicacionales asignadas.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tareas as $t): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($t['codigo_ticket']) ?></strong></td>
                            <td>
                                <div style="font-weight: 700;"><?= htmlspecialchars($t['titulo_actividad']) ?></div>
                                <div style="font-size: 0.78rem; color: #64748b;"><?= htmlspecialchars($t['lugar']) ?></div>
                            </td>
                            <td><?= htmlspecialchars($t['dependencia_nombre']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($t['fecha_evento'])) ?></td>
                            <td style="color: #dc2626; font-weight: 700;"><?= date('d/m/Y H:i', strtotime($t['fecha_limite'])) ?></td>
                            <td>
                                <span class="badge badge-<?= strtolower(str_replace(' ', '-', $t['estado_solicitud'])) ?>">
                                    <?= $t['estado_solicitud'] ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= $baseUrl ?>/solicitudes/detalle?id=<?= $t['id'] ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.82rem;">
                                    <i class="fa-solid fa-cloud-arrow-up"></i> Entregar Versión
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
