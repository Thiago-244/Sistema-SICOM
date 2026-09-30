<?php
use App\Helpers\Auth;
$user = Auth::user();
$config = require __DIR__ . '/../../../config/database.php';
$baseUrl = $config['app_url'];
$currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo-badge">S</div>
        <div>
            <div class="brand-title">SICOM DREP</div>
            <div class="brand-subtitle">Relaciones Públicas</div>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section-title">Principal</div>
        
        <?php if ($user && $user['rol_id'] != 2): ?>
            <a href="<?= $baseUrl ?>/dashboard" class="nav-item <?= str_contains($currentUri, '/dashboard') || $currentUri === '/' ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Panel de Control</span>
            </a>
        <?php endif; ?>

        <?php if ($user && $user['rol_id'] == 2): ?>
            <a href="<?= $baseUrl ?>/mis-tareas" class="nav-item <?= str_contains($currentUri, '/mis-tareas') ? 'active' : '' ?>">
                <i class="fa-solid fa-list-check"></i>
                <span>Mis Tareas</span>
            </a>
        <?php endif; ?>

        <a href="<?= $baseUrl ?>/solicitudes" class="nav-item <?= str_contains($currentUri, '/solicitudes') ? 'active' : '' ?>">
            <i class="fa-solid fa-file-lines"></i>
            <span>Solicitudes</span>
        </a>

        <?php if ($user && $user['rol_id'] != 2): ?>
            <a href="<?= $baseUrl ?>/asignaciones" class="nav-item <?= str_contains($currentUri, '/asignaciones') ? 'active' : '' ?>">
                <i class="fa-solid fa-user-gear"></i>
                <span>Motor de Asignación</span>
            </a>
        <?php endif; ?>

        <div class="nav-section-title">Administración</div>
        
        <?php if ($user && ($user['rol_id'] == 1 || $user['rol_id'] == 4)): ?>
            <a href="<?= $baseUrl ?>/admin/usuarios" class="nav-item <?= str_contains($currentUri, '/admin/usuarios') ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i>
                <span>Usuarios & Roles</span>
            </a>
            <a href="<?= $baseUrl ?>/admin/comunicadores" class="nav-item <?= str_contains($currentUri, '/admin/comunicadores') ? 'active' : '' ?>">
                <i class="fa-solid fa-address-card"></i>
                <span>Comunicadores</span>
            </a>
            <a href="<?= $baseUrl ?>/admin/auditoria" class="nav-item <?= str_contains($currentUri, '/admin/auditoria') ? 'active' : '' ?>">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Logs de Auditoría</span>
            </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">
                <?= strtoupper(substr($user['username'] ?? 'U', 0, 2)) ?>
            </div>
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($user['username'] ?? 'Usuario') ?></div>
                <div class="user-role"><?= htmlspecialchars($user['rol_nombre'] ?? 'Invitado') ?></div>
            </div>
        </div>
    </div>
</aside>
