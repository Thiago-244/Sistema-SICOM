<?php
$pageTitle = "Registrar Nueva Solicitud";
$pageSubtitle = "Creación de ticket de producción comunicacional";
require __DIR__ . '/../layouts/header.php';
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-file-circle-plus"></i> Formularios de Requerimiento Institucional
        </div>
        <a href="<?= $baseUrl ?>/solicitudes" class="btn btn-secondary" style="font-size: 0.8rem;">
            <i class="fa-solid fa-arrow-left"></i> Volver al Listado
        </a>
    </div>

    <form action="<?= $baseUrl ?>/solicitudes/guardar" method="POST">
        <div class="form-group">
            <label class="form-label">Dependencia / Área Solicitante <span style="color:red;">*</span></label>
            <select name="dependencia_solicitante_id" class="form-select" required>
                <option value="">-- Seleccione Área u Oficina --</option>
                <?php foreach ($dependencias as $dep): ?>
                    <option value="<?= $dep['id'] ?>"><?= htmlspecialchars($dep['nombre']) ?> (<?= $dep['siglas'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Título de la Actividad / Cobertura <span style="color:red;">*</span></label>
            <input type="text" name="titulo_actividad" class="form-control" placeholder="ej. Cobertura Periodística de la Ceremomía de Cierre Año Escolar DREP" required>
        </div>

        <div class="form-group">
            <label class="form-label">Descripción Detallada y Requerimiento <span style="color:red;">*</span></label>
            <textarea name="descripcion" class="form-control" rows="4" placeholder="Especifique los productos solicitados: Fotografía HD, Nota de Prensa, Reel para Facebook, Streaming en vivo..." required></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Lugar del Evento / Ubicación</label>
                <input type="text" name="lugar" class="form-control" placeholder="ej. Auditorio Principal DREP / Piura">
            </div>

            <div class="form-group">
                <label class="form-label">Nivel de Prioridad <span style="color:red;">*</span></label>
                <select name="prioridad" class="form-select" required>
                    <option value="Baja">Baja (Rutina)</option>
                    <option value="Media" selected>Media (Normal)</option>
                    <option value="Alta">Alta (Atención Prioritaria)</option>
                    <option value="Urgente">Urgente (SLA Inmediato)</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Fecha y Hora del Evento <span style="color:red;">*</span></label>
                <input type="datetime-local" name="fecha_evento" class="form-control" required value="<?= date('Y-m-d\TH:i', strtotime('+1 day')) ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Fecha Límite Entrega Final (SLA) <span style="color:red;">*</span></label>
                <input type="datetime-local" name="fecha_limite" class="form-control" required value="<?= date('Y-m-d\TH:i', strtotime('+2 days')) ?>">
            </div>
        </div>

        <div style="margin-top: 24px; text-align: right;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 24px; font-size: 1rem;">
                <i class="fa-solid fa-paper-plane"></i> Generar Ticket de Solicitud
            </button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
