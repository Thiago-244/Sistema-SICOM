<?php
use App\Helpers\Auth;
use App\Helpers\Session;

$user = Auth::user();
$config = require __DIR__ . '/../../../config/database.php';
$baseUrl = $config['app_url'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'SICOM - DREP') ?></title>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts Inter -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <!-- Main Style CSS -->
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/style.css">
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/sidebar.php'; ?>
    <div class="main-wrapper">
        <header class="top-navbar">
            <div class="page-title-area">
                <h1><?= htmlspecialchars($pageTitle ?? 'Sistema SICOM') ?></h1>
                <p><?= htmlspecialchars($pageSubtitle ?? 'Dirección Regional de Educación Piura') ?></p>
            </div>
            <div class="navbar-actions">
                <a href="<?= $baseUrl ?>/portal" target="_blank" class="btn btn-secondary" style="font-size:0.82rem;">
                    <i class="fa-solid fa-external-link"></i> Portal Público DE
                </a>
                <a href="<?= $baseUrl ?>/logout" class="btn btn-danger" style="padding: 8px 14px; font-size:0.82rem;">
                    <i class="fa-solid fa-right-from-bracket"></i> Salir
                </a>
            </div>
        </header>
        <main class="content-body">
            <?php if ($flashSuccess = Session::flash('success')): ?>
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span><?= htmlspecialchars($flashSuccess) ?></span>
                </div>
            <?php endif; ?>
            <?php if ($flashError = Session::flash('error')): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?= htmlspecialchars($flashError) ?></span>
                </div>
            <?php endif; ?>
