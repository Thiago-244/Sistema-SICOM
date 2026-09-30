<?php
$config = require __DIR__ . '/../../../config/database.php';
$baseUrl = $config['app_url'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página No Encontrada - SICOM DREP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/style.css">
    <style>
        body { background: #f1f5f9; display: flex; align-items: center; justify-content: center; min-height: 100vh; text-align: center; }
        .card-404 { background: white; padding: 40px; border-radius: 16px; max-width: 480px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="card-404">
        <i class="fa-solid fa-compass-drafting" style="font-size: 4rem; color: #0284c7; margin-bottom: 16px;"></i>
        <h1 style="font-size: 2rem; font-weight: 800; color: #0f172a;">Error 404</h1>
        <p style="color: #64748b; margin-bottom: 24px;">La página o recurso que está buscando no existe o ha sido movido.</p>
        <a href="<?= $baseUrl ?>/dashboard" class="btn btn-primary">
            <i class="fa-solid fa-house"></i> Volver al Panel de Control
        </a>
    </div>
</body>
</html>
