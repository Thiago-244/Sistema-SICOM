<?php
$pageTitle = "Ficha de Comunicadores Sociales";
$pageSubtitle = "Directorio profesional, especialidades y carga de trabajo del equipo DREP";
require __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-address-card"></i> Directorio de Comunicadores
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Nombres y Apellidos</th>
                    <th>Cargo / Dependencia</th>
                    <th>Especialidades</th>
                    <th>Disponibilidad</th>
                    <th>Carga Activa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comunicadores as $c): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($c['dni']) ?></strong></td>
                        <td>
                            <div style="font-weight: 700;"><?= htmlspecialchars($c['nombres'] . ' ' . $c['apellidos']) ?></div>
                            <div style="font-size: 0.78rem; color: #64748b;"><?= htmlspecialchars($c['email']) ?></div>
                        </td>
                        <td>
                            <div><?= htmlspecialchars($c['cargo']) ?></div>
                            <div style="font-size: 0.75rem; color: #64748b;"><?= htmlspecialchars($c['dependencia_nombre']) ?></div>
                        </td>
                        <td>
                            <span class="badge" style="background: #f1f5f9; color: #475569;">
                                <?= htmlspecialchars($c['especialidades_lista'] ?? 'General') ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="background: <?= $c['disponibilidad'] === 'Disponible' ? '#dcfce7' : '#fee2e2' ?>; color: <?= $c['disponibilidad'] === 'Disponible' ? '#16a34a' : '#dc2626' ?>;">
                                <?= $c['disponibilidad'] ?>
                            </span>
                        </td>
                        <td><strong><?= $c['carga_activa'] ?></strong> tareas</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
