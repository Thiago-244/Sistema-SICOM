<?php
use App\Helpers\Session;
$config = require __DIR__ . '/../../../config/database.php';
$baseUrl = $config['app_url'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - SICOM DREP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 440px;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-brand-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: white;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 16px;
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.4);
        }
        .login-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
        }
        .login-subtitle {
            font-size: 0.85rem;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="login-brand-icon">S</div>
            <h1 class="login-title">Sistema SICOM</h1>
            <p class="login-subtitle">Dirección Regional de Educación Piura</p>
        </div>

        <?php if ($flashError = Session::flash('error')): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?= htmlspecialchars($flashError) ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= $baseUrl ?>/login/process" method="POST">
            <div class="form-group">
                <label class="form-label">Usuario o Correo Institucional</label>
                <input type="text" name="username" class="form-control" placeholder="ej. admin.rrpp" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 10px; font-size: 1rem;">
                <i class="fa-solid fa-right-to-bracket"></i> Iniciar Sesión
            </button>
        </form>

        <div style="margin-top: 24px; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 16px;">
            <a href="<?= $baseUrl ?>/portal" style="font-size: 0.85rem; color: #0284c7; font-weight: 600;">
                <i class="fa-solid fa-paper-plane"></i> Acceder al Portal Público de Solicitudes
            </a>
        </div>
    </div>
</body>
</html>
