<?php

namespace App\Helpers;

use Exception;

class SmartStorage {
    private static array $allowedMimes = [
        'application/pdf' => 'pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.ms-excel' => 'xls',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'video/mp4' => 'mp4',
        'application/zip' => 'zip',
        'application/x-zip-compressed' => 'zip'
    ];

    /**
     * Procesa la subida local de un archivo con límites y validaciones estrictas.
     */
    public static function storeLocalFile(array $fileInput, int $maxMb = 25): array {
        if (!isset($fileInput['error']) || $fileInput['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error al cargar el archivo en el servidor. Código: " . ($fileInput['error'] ?? 'desconocido'));
        }

        $fileSize = $fileInput['size'];
        $maxBytes = $maxMb * 1024 * 1024;

        if ($fileSize > $maxBytes) {
            throw new Exception("El archivo excede el tamaño máximo permitido para almacenamiento local ({$maxMb} MB). Por favor inserte un enlace de Google Drive / YouTube.");
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fileInput['tmp_name']);
        finfo_close($finfo);

        if (!array_key_exists($mime, self::$allowedMimes)) {
            throw new Exception("Formato de archivo no permitido ({$mime}). Solo se aceptan PDF, DOCX, XLSX, JPG, PNG, MP4 y ZIP.");
        }

        $ext = self::$allowedMimes[$mime];
        $sha256 = hash_file('sha256', $fileInput['tmp_name']);
        
        $yearMonth = date('Y/m');
        $uploadDir = __DIR__ . '/../../storage/uploads/' . $yearMonth;
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $safeName = uniqid('sicom_evid_', true) . '.' . $ext;
        $targetPath = $uploadDir . '/' . $safeName;
        $relativePath = 'storage/uploads/' . $yearMonth . '/' . $safeName;

        if (!move_uploaded_file($fileInput['tmp_name'], $targetPath)) {
            throw new Exception("No se pudo guardar el archivo en el directorio de destino.");
        }

        return [
            'nombre_original' => basename($fileInput['name']),
            'ruta' => $relativePath,
            'mime' => $mime,
            'tamano_bytes' => $fileSize,
            'hash_sha256' => $sha256,
            'es_enlace_externo' => false
        ];
    }

    /**
     * Procesa y valida un enlace de transmisión o nube para archivos pesados.
     */
    public static function processCloudLink(string $url): array {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception("La URL ingresada no es válida.");
        }

        $provider = 'Almacenamiento en Nube / Enlace Externo';
        if (str_contains($url, 'drive.google.com')) {
            $provider = 'Google Drive';
        } elseif (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            $provider = 'YouTube Video Streaming';
        } elseif (str_contains($url, 'vimeo.com')) {
            $provider = 'Vimeo Video Streaming';
        } elseif (str_contains($url, 'onedrive') || str_contains($url, 'sharepoint')) {
            $provider = 'Microsoft OneDrive / SharePoint';
        }

        return [
            'nombre_original' => "Recurso Externo ({$provider})",
            'ruta' => $url,
            'mime' => 'url/external-stream',
            'tamano_bytes' => 0,
            'hash_sha256' => hash('sha256', $url),
            'es_enlace_externo' => true
        ];
    }
}
