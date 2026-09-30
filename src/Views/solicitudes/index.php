<?php
$pageTitle = "Gestión de Solicitudes";
$pageSubtitle = "Listado completo de requerimientos de producción comunicacional";
require __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-filter"></i> Filtros de Búsqueda
        </div>
        <a href="<?= $baseUrl ?>/solicitudes/crear" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Nueva Solicitud
        </a>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="<?= $baseUrl ?>/solicitudes" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) 120px; gap: 16px; margin-bottom: 24px;">
        <div>
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select" onchange="this.form.submit()">
                <option value="">-- Todos los Estados --</option>
                <?php foreach (['Registrada', 'Validada', 'Asignada', 'En Proceso', 'En Revision', 'Observada', 'Aprobada', 'Publicada', 'Cerrada', 'Cancelada'] as $e): ?>
                    <option value="<?= $e ?>" <?= ($filters['estado'] ?? '') === $e ? 'selected' : '' ?>><?= $e ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="form-label">Prioridad</label>
            <select name="prioridad" class="form-select" onchange="this.form.submit()">
                <option value="">-- Todas --</option>
                <?php foreach (['Baja', 'Media', 'Alta', 'Urgente'] as $p): ?>
                    <option value="<?= $p ?>" <?= ($filters['prioridad'] ?? '') === $p ? 'selected' : '' ?>><?= $p ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="form-label">Buscar Ticket / Título</label>
            <input type="text" name="search" class="form-control" placeholder="ej. SOL-2026..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
        </div>

        <div style="display: flex; align-items: flex-end;">
            <button type="submit" class="btn btn-secondary" style="width: 100%;">
                <i class="fa-solid fa-search"></i> Buscar
            </button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Código Ticket</th>
                    <th>Actividad / Evento</th>
                    <th>Dependencia</th>
                    <th>Responsable Asignado</th>
                    <th>Prioridad</th>
                    <th>Fecha Límite</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($solicitudes)): ?>
                    <tr><td colspan="8" style="text-align: center; color: #94a3b8; padding: 24px;">No se encontraron solicitudes con los criterios especificados.</td></tr>
                <?php else: ?>
                    <?php foreach ($solicitudes as $s): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($s['codigo_ticket']) ?></strong></td>
                            <td>
                                <div style="font-weight: 700;"><?= htmlspecialchars($s['titulo_actividad']) ?></div>
                                <div style="font-size: 0.75rem; color: #64748b;"><?= date('d/m/Y H:i', strtotime($s['fecha_evento'])) ?></div>
                            </td>
                            <td><?= htmlspecialchars($s['dependencia_nombre']) ?></td>
                            <td>
                                <?php if ($s['comunicador_nombres']): ?>
                                    <span style="font-weight: 600; color: #0284c7;">
                                        <i class="fa-solid fa-user-check"></i> <?= htmlspecialchars($s['comunicador_nombres'] . ' ' . $s['comunicador_apellidos']) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-style: italic;">Sin Asignar</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-prioridad-<?= strtolower($s['prioridad']) ?>">
                                    <?= $s['prioridad'] ?>
                                </span>
                            </td>
                            <td style="font-weight: 600; font-size: 0.85rem; color: #475569;">
                                <?= date('d/m/Y H:i', strtotime($s['fecha_limite'])) ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= strtolower(str_replace(' ', '-', $s['estado_solicitud'])) ?>">
                                    <?= $s['estado_solicitud'] ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= $baseUrl ?>/solicitudes/detalle?id=<?= $s['id'] ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.8rem;">
                                    <i class="fa-solid fa-eye"></i> Gestionar
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
