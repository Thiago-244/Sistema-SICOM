<?php
/**
 * Configuración del Sistema SICOM - DREP
 */

return [
    'app_name' => 'SICOM - DREP',
    'app_url' => 'http://localhost/Sistema%20SICOM/public',
    'max_upload_size_mb' => 25, // Límite para archivos subidos directamente al servidor
    'db' => [
        'host' => 'localhost',
        'dbname' => 'sicom_db',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ],
    'mail' => [
        'driver' => 'log', // 'log' o 'smtp'
        'from_address' => 'rrpp.notificaciones@drep.gob.pe',
        'from_name' => 'Sistema SICOM - DREP'
    ]
];
