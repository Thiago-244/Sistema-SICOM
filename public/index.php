<?php

// Autoload simple de clases PSR-4 para App
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

use App\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\SolicitudController;
use App\Controllers\AsignacionController;
use App\Controllers\ProductoController;
use App\Controllers\PublicPortalController;
use App\Controllers\AdminController;
use App\Controllers\ApiController;

$router = new Router();

// Rutas de Autenticación
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login/process', [AuthController::class, 'processLogin']);
$router->get('/logout', [AuthController::class, 'logout']);

// Dashboard
$router->get('/', [DashboardController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/mis-tareas', [DashboardController::class, 'misTareas']);

// Solicitudes
$router->get('/solicitudes', [SolicitudController::class, 'index']);
$router->get('/solicitudes/crear', [SolicitudController::class, 'create']);
$router->post('/solicitudes/guardar', [SolicitudController::class, 'store']);
$router->get('/solicitudes/detalle', [SolicitudController::class, 'detail']);

// Asignaciones
$router->get('/asignaciones', [AsignacionController::class, 'index']);
$router->post('/asignaciones/ejecutar', [AsignacionController::class, 'assign']);

// Productos y Versionado
$router->post('/productos/crear', [ProductoController::class, 'storeProduct']);
$router->post('/productos/subir-version', [ProductoController::class, 'uploadVersion']);
$router->post('/productos/evaluar', [ProductoController::class, 'evaluate']);

// Portal Público de Solicitudes y Seguimiento por Ticket
$router->get('/portal', [PublicPortalController::class, 'index']);
$router->post('/portal/guardar', [PublicPortalController::class, 'storePublicRequest']);
$router->get('/portal/exito', [PublicPortalController::class, 'success']);
$router->get('/portal/seguimiento', [PublicPortalController::class, 'tracking']);

// Administración y Auditoría
$router->get('/admin/usuarios', [AdminController::class, 'users']);
$router->get('/admin/comunicadores', [AdminController::class, 'comunicadores']);
$router->get('/admin/auditoria', [AdminController::class, 'auditLogs']);

// APIs de integración y tiempo real
$router->get('/api/stats', [ApiController::class, 'stats']);
$router->get('/api/evaluar-asignacion', [ApiController::class, 'evaluateAssignment']);

$router->dispatch();
