<?php
$pageTitle = "Panel de Control Operativo";
$pageSubtitle = "Monitoreo en tiempo real de productos comunicacionales DREP";
require __DIR__ . '/../layouts/header.php';
?>

<!-- Counter Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fa-solid fa-folder-open"></i>
        </div>
        <div>
            <div class="stat-val"><?= $stats['total'] ?></div>
            <div class="stat-lbl">Solicitudes Totales</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon amber">
            <i class="fa-solid fa-clock"></i>
        </div>
        <div>
            <div class="stat-val"><?= $stats['en_proceso'] ?></div>
            <div class="stat-lbl">En Proceso / Revisión</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
            <div class="stat-val"><?= $stats['aprobadas'] ?></div>
            <div class="stat-lbl">Aprobadas / Entregadas</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon red">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
            <div class="stat-val"><?= $stats['vencidas'] ?></div>
            <div class="stat-lbl">Alertas Vencidas SLA</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- Solicitudes Recientes -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-list"></i> Solicitudes Recientes
            </div>
            <a href="<?= $baseUrl ?>/solicitudes" class="btn btn-secondary" style="font-size: 0.8rem; padding: 6px 12px;">
                Ver Todas <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>Actividad / Dependencia</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentSolicitudes)): ?>
                        <tr><td colspan="5" style="text-align: center; color: #94a3b8;">No hay solicitudes registradas aún.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentSolicitudes as $sol): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($sol['codigo_ticket']) ?></strong></td>
                                <td>
                                    <div style="font-weight: 600;"><?= htmlspecialchars($sol['titulo_actividad']) ?></div>
                                    <div style="font-size:0.75rem; color:#64748b;"><?= htmlspecialchars($sol['dependencia_nombre']) ?></div>
                                </td>
                                <td>
                                    <span class="badge badge-prioridad-<?= strtolower($sol['prioridad']) ?>">
                                        <?= $sol['prioridad'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= strtolower(str_replace(' ', '-', $sol['estado_solicitud'])) ?>">
                                        <?= $sol['estado_solicitud'] ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= $baseUrl ?>/solicitudes/detalle?id=<?= $sol['id'] ?>" class="btn btn-primary" style="padding: 4px 10px; font-size: 0.78rem;">
                                        <i class="fa-solid fa-eye"></i> Detalle
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Equipo de Comunicadores & Carga -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-users"></i> Comunicadores
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach ($comunicadores as $com): ?>
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <div>
                        <div style="font-weight: 700; font-size: 0.88rem;"><?= htmlspecialchars($com['nombres'] . ' ' . $com['apellidos']) ?></div>
                        <div style="font-size: 0.75rem; color: #64748b;"><?= htmlspecialchars($com['cargo']) ?></div>
                    </div>
                    <div>
                        <span class="badge" style="background: #e0f2fe; color: #0284c7;">
                            <?= $com['carga_activa'] ?> asignadas
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Logs de Auditoría Inmutable Recientes -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-shield-halved"></i> Bitácora de Auditoría Reciente
        </div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha / Hora</th>
                    <th>Usuario</th>
                    <th>Acción Auditada</th>
                    <th>Entidad</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($auditLogs as $log): ?>
                    <tr>
                        <td style="font-size: 0.8rem; color: #64748b;"><?= $log['created_at'] ?></td>
                        <td><strong><?= htmlspecialchars($log['username'] ?? 'Sistema / Público') ?></strong></td>
                        <td><span style="font-family: monospace; font-weight: 700; color: #0284c7;"><?= htmlspecialchars($log['accion']) ?></span></td>
                        <td><?= htmlspecialchars($log['entidad']) ?> #<?= $log['entidad_id'] ?></td>
                        <td style="font-size: 0.8rem; color: #64748b;"><?= htmlspecialchars($log['ip_address']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
