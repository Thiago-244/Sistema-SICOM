<?php
$pageTitle = "Logs de Auditoría de Seguridad";
$pageSubtitle = "Registro inmutable de acciones críticas y cambios de estado";
require __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-shield-halved"></i> Audit Trail & Historial de Cambios
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th># ID</th>
                    <th>Marca de Tiempo</th>
                    <th>Usuario</th>
                    <th>Código Acción</th>
                    <th>Entidad Afectada</th>
                    <th>IP / User-Agent</th>
                    <th>Datos Previos vs Nuevos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $l): ?>
                    <tr>
                        <td>#<?= $l['id'] ?></td>
                        <td style="font-size: 0.82rem; color: #64748b;"><?= $l['created_at'] ?></td>
                        <td><strong><?= htmlspecialchars($l['username'] ?? 'Sistema') ?></strong></td>
                        <td><span style="font-family: monospace; font-weight: 700; color: #0284c7;"><?= htmlspecialchars($l['accion']) ?></span></td>
                        <td><?= htmlspecialchars($l['entidad']) ?> #<?= $l['entidad_id'] ?></td>
                        <td style="font-size: 0.78rem; color: #64748b;"><?= htmlspecialchars($l['ip_address']) ?></td>
                        <td style="font-size: 0.75rem; font-family: monospace;">
                            <?php if ($l['datos_nuevos']): ?>
                                <pre style="background: #f8fafc; padding: 4px 8px; border-radius: 4px; max-width: 250px; overflow-x: auto; margin:0;"><?= htmlspecialchars($l['datos_nuevos']) ?></pre>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
