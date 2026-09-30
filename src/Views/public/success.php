<?php
$config = require __DIR__ . '/../../../config/database.php';
$baseUrl = $config['app_url'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud Registrada - SICOM DREP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/style.css">
    <style>
        body { background: #f1f5f9; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
        .card-success { background: white; border-radius: 16px; padding: 40px; text-align: center; max-width: 500px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .ticket-badge { background: #e0f2fe; color: #0284c7; font-size: 1.8rem; font-weight: 800; padding: 12px 24px; border-radius: 12px; margin: 20px 0; display: inline-block; border: 2px dashed #0284c7; }
    </style>
</head>
<body>
    <div class="card-success">
        <i class="fa-solid fa-circle-check" style="font-size: 4rem; color: #16a34a; margin-bottom: 16px;"></i>
        <h1 style="font-size: 1.6rem; color: #0f172a; font-weight: 800;">¡Solicitud Registrada Exitosamente!</h1>
        <p style="color: #64748b; margin-top: 8px;">Guarde su número de ticket para hacerle seguimiento en tiempo real:</p>

        <div class="ticket-badge"><?= htmlspecialchars($ticket) ?></div>

        <p style="font-size: 0.85rem; color: #475569; margin-bottom: 24px;">
            El Área de Relaciones Públicas e Imagen Institucional de la DREP ha recibido su requerimiento y asignará un especialista comunicador a la brevedad.
        </p>

        <div style="display: flex; gap: 12px; justify-content: center;">
            <a href="<?= $baseUrl ?>/portal/seguimiento?ticket=<?= $ticket ?>" class="btn btn-primary">
                <i class="fa-solid fa-magnifying-glass"></i> Consultar Estado del Ticket
            </a>
            <a href="<?= $baseUrl ?>/portal" class="btn btn-secondary">
                <i class="fa-solid fa-plus"></i> Registrar Otra Solicitud
            </a>
        </div>
    </div>
</body>
</html>
