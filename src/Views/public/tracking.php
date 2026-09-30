<?php
$config = require __DIR__ . '/../../../config/database.php';
$baseUrl = $config['app_url'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguimiento de Ticket - SICOM DREP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/style.css">
    <style>
        body { background: #f1f5f9; padding: 40px 20px; }
        .track-card { max-width: 700px; margin: 0 auto; background: white; border-radius: 16px; padding: 36px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
    </style>
</head>
<body>
    <div class="track-card">
        <div style="text-align: center; margin-bottom: 24px;">
            <h1 style="font-size: 1.5rem; color: #0f172a; font-weight: 800;">Consultar Estado de Solicitud Comunicacional</h1>
            <p style="color: #64748b; font-size: 0.88rem;">Ingrese el código único de ticket generado (ej. SOL-202609-XXXX)</p>
        </div>

        <form action="<?= $baseUrl ?>/portal/seguimiento" method="GET" style="display: flex; gap: 12px; margin-bottom: 30px;">
            <input type="text" name="ticket" class="form-control" placeholder="Ingrese Código de Ticket..." value="<?= htmlspecialchars($_GET['ticket'] ?? '') ?>" required style="font-size: 1rem; font-weight: 700; text-transform: uppercase;">
            <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                <i class="fa-solid fa-search"></i> Buscar
            </button>
        </form>

        <?php if (isset($_GET['ticket']) && !empty($_GET['ticket'])): ?>
            <?php if ($solicitud): ?>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <span class="badge badge-<?= strtolower(str_replace(' ', '-', $solicitud['estado_solicitud'])) ?>" style="font-size: 0.9rem; padding: 6px 14px;">
                            Estado: <?= $solicitud['estado_solicitud'] ?>
                        </span>
                        <span style="font-size: 0.8rem; color: #64748b;">Ticket #<?= htmlspecialchars($solicitud['codigo_ticket']) ?></span>
                    </div>

                    <h2 style="font-size: 1.15rem; font-weight: 700; color: #0f172a; margin-bottom: 8px;">
                        <?= htmlspecialchars($solicitud['titulo_actividad']) ?>
                    </h2>
                    <p style="color: #475569; font-size: 0.9rem; margin-bottom: 16px;">
                        <?= htmlspecialchars($solicitud['descripcion']) ?>
                    </p>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; background: #ffffff; padding: 14px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.85rem;">
                        <div><strong>Solicitante:</strong> <?= htmlspecialchars($solicitud['dependencia_nombre']) ?></div>
                        <div><strong>Fecha Evento:</strong> <?= date('d/m/Y H:i', strtotime($solicitud['fecha_evento'])) ?></div>
                        <div><strong>Prioridad:</strong> <?= htmlspecialchars($solicitud['prioridad']) ?></div>
                        <div><strong>Fecha Límite:</strong> <?= date('d/m/Y H:i', strtotime($solicitud['fecha_limite'])) ?></div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>No se encontró ninguna solicitud con el código de ticket especificado.</span>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div style="margin-top: 24px; text-align: center;">
            <a href="<?= $baseUrl ?>/portal" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Volver al Portal de Solicitudes
            </a>
        </div>
    </div>
</body>
</html>
