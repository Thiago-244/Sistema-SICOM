<?php
$config = require __DIR__ . '/../../../config/database.php';
$baseUrl = $config['app_url'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Público de Solicitudes Comunicacionales - DREP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/style.css">
    <style>
        body { background-color: #f1f5f9; padding-bottom: 40px; }
        .public-header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; padding: 40px 20px; text-align: center; margin-bottom: 40px; }
        .public-card { max-width: 800px; margin: 0 auto; background: white; border-radius: 16px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
    </style>
</head>
<body>
    <div class="public-header">
        <h1 style="font-size: 2rem; font-weight: 800;">Portal de Solicitudes Comunicacionales</h1>
        <p style="color: #94a3b8;">Dirección Regional de Educación Piura - Área de Relaciones Públicas</p>
        <div style="margin-top: 16px;">
            <a href="<?= $baseUrl ?>/portal/seguimiento" class="btn btn-secondary" style="font-size: 0.85rem;">
                <i class="fa-solid fa-magnifying-glass"></i> Consultar Estado de mi Ticket
            </a>
            <a href="<?= $baseUrl ?>/login" class="btn btn-primary" style="font-size: 0.85rem; margin-left: 10px;">
                <i class="fa-solid fa-lock"></i> Acceso Intranet Personal DREP
            </a>
        </div>
    </div>

    <div class="public-card">
        <h2 style="font-size: 1.3rem; margin-bottom: 8px; color: #0f172a;">Formulario de Registro de Requerimiento</h2>
        <p style="font-size: 0.88rem; color: #64748b; margin-bottom: 24px;">
            Utilice este formulario institucional para solicitar cobertura periodística, diseño gráfico, notas de prensa o producción audiovisual para su dependencia o UGEL.
        </p>

        <form action="<?= $baseUrl ?>/portal/guardar" method="POST">
            <div class="form-group">
                <label class="form-label">Dependencia / Oficina / UGEL Solicitante <span style="color:red;">*</span></label>
                <select name="dependencia_solicitante_id" class="form-select" required>
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($dependencias as $dep): ?>
                        <option value="<?= $dep['id'] ?>"><?= htmlspecialchars($dep['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Título del Evento o Requerimiento <span style="color:red;">*</span></label>
                <input type="text" name="titulo_actividad" class="form-control" placeholder="ej. Cobertura de la Inauguración de Infraestructura Educativa en UGEL Sullana" required>
            </div>

            <div class="form-group">
                <label class="form-label">Detalle del Servicio Solicitado <span style="color:red;">*</span></label>
                <textarea name="descripcion" class="form-control" rows="4" placeholder="Describa el objetivo del evento y los productos necesarios (Fotografía, Redacción, Video, etc.)..." required></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Lugar del Evento</label>
                    <input type="text" name="lugar" class="form-control" placeholder="ej. Auditorio Principal DREP">
                </div>

                <div class="form-group">
                    <label class="form-label">Urgencia / Prioridad <span style="color:red;">*</span></label>
                    <select name="prioridad" class="form-select" required>
                        <option value="Baja">Baja</option>
                        <option value="Media" selected>Media (Estándar)</option>
                        <option value="Alta">Alta</option>
                        <option value="Urgente">Urgente</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Fecha del Evento <span style="color:red;">*</span></label>
                    <input type="datetime-local" name="fecha_evento" class="form-control" required value="<?= date('Y-m-d\TH:i', strtotime('+1 day')) ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Fecha Límite Requerida <span style="color:red;">*</span></label>
                    <input type="datetime-local" name="fecha_limite" class="form-control" required value="<?= date('Y-m-d\TH:i', strtotime('+2 days')) ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 1rem; margin-top: 10px;">
                <i class="fa-solid fa-paper-plane"></i> Enviar Solicitud y Obtener Código Ticket
            </button>
        </form>
    </div>
</body>
</html>
