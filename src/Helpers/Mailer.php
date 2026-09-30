<?php

namespace App\Helpers;

use Exception;

class Mailer {
    /**
     * Envia una notificación por correo electrónico o la registra en log según configuración.
     */
    public static function send(string $toEmail, string $subject, string $bodyHtml): bool {
        $config = require __DIR__ . '/../../config/database.php';
        $mailConfig = $config['mail'];

        // En todos los casos guardamos en log local para trazabilidad inmutable
        $logDir = __DIR__ . '/../../storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/mail_' . date('Y-m-d') . '.log';
        $logEntry = "[" . date('Y-m-d H:i:s') . "] TO: {$toEmail} | ASUNTO: {$subject}" . PHP_EOL . "CUERPO:" . PHP_EOL . strip_tags($bodyHtml) . PHP_EOL . "---" . PHP_EOL;
        file_put_contents($logFile, $logEntry, FILE_APPEND);

        // Si el driver es log, retornamos exitoso inmediatamente
        if ($mailConfig['driver'] === 'log') {
            return true;
        }

        // Si se habilita SMTP en producción se usa mail() nativo o SMTP
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: {$mailConfig['from_name']} <{$mailConfig['from_address']}>" . "\r\n";

        try {
            return @mail($toEmail, $subject, $bodyHtml, $headers);
        } catch (Exception $e) {
            error_log("Error al enviar correo SMTP: " . $e->getMessage());
            return false;
        }
    }
}
