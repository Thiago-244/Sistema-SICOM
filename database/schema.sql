-- ============================================================
-- ESQUEMA RELACIONAL DEL SISTEMA SICOM - DREP
-- Motor: MySQL 8.0 / MariaDB 10.4 (InnoDB, UTF8MB4)
-- ============================================================

CREATE DATABASE IF NOT EXISTS `sicom_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sicom_db`;

-- ------------------------------------------------------------
-- 1. SEGURIDAD, ROLES Y PERMISOS (RBAC)
-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(50) NOT NULL UNIQUE,
    `descripcion` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `permisos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(100) NOT NULL UNIQUE,
    `modulo` VARCHAR(50) NOT NULL,
    `descripcion` VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `rol_permiso` (
    `rol_id` INT NOT NULL,
    `permiso_id` INT NOT NULL,
    PRIMARY KEY (`rol_id`, `permiso_id`),
    FOREIGN KEY (`rol_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`permiso_id`) REFERENCES `permisos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `rol_id` INT NOT NULL,
    `estado` ENUM('Activo', 'Inactivo', 'Bloqueado') DEFAULT 'Activo',
    `ultimo_acceso` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`rol_id`) REFERENCES `roles`(`id`)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 2. CATÁLOGOS INSTITUCIONALES Y COMUNICADORES
-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `dependencias` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(150) NOT NULL,
    `siglas` VARCHAR(20) NULL,
    `tipo` ENUM('Oficina DREP', 'UGEL', 'Institución Externa') DEFAULT 'Oficina DREP',
    `ugel_codigo` VARCHAR(20) NULL,
    `estado` ENUM('Activo', 'Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `comunicadores` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL UNIQUE,
    `dni` VARCHAR(8) NOT NULL UNIQUE,
    `nombres` VARCHAR(100) NOT NULL,
    `apellidos` VARCHAR(100) NOT NULL,
    `telefono` VARCHAR(20) NULL,
    `cargo` VARCHAR(100) DEFAULT 'Comunicador Social',
    `dependencia_id` INT NOT NULL,
    `disponibilidad` ENUM('Disponible', 'Ocupado', 'Licencia') DEFAULT 'Disponible',
    `estado` ENUM('Activo', 'Inactivo') DEFAULT 'Activo',
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`dependencia_id`) REFERENCES `dependencias`(`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `especialidades` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(50) NOT NULL UNIQUE,
    `descripcion` VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `comunicador_especialidad` (
    `comunicador_id` INT NOT NULL,
    `especialidad_id` INT NOT NULL,
    PRIMARY KEY (`comunicador_id`, `especialidad_id`),
    FOREIGN KEY (`comunicador_id`) REFERENCES `comunicadores`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 3. SOLICITUDES Y ASIGNACIÓN DE TRABAJO
-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tipos_producto` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(80) NOT NULL UNIQUE,
    `requiere_video` TINYINT(1) DEFAULT 0,
    `requiere_diseno` TINYINT(1) DEFAULT 0,
    `tiempo_estimado_horas` INT DEFAULT 24
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `solicitudes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo_ticket` VARCHAR(20) NOT NULL UNIQUE,
    `dependencia_solicitante_id` INT NOT NULL,
    `titulo_actividad` VARCHAR(200) NOT NULL,
    `descripcion` TEXT NOT NULL,
    `lugar` VARCHAR(150) NULL,
    `fecha_evento` DATETIME NOT NULL,
    `prioridad` ENUM('Baja', 'Media', 'Alta', 'Urgente') DEFAULT 'Media',
    `fecha_limite` DATETIME NOT NULL,
    `estado_solicitud` ENUM('Registrada', 'Validada', 'Asignada', 'En Proceso', 'En Revision', 'Observada', 'Aprobada', 'Publicada', 'Cerrada', 'Cancelada') DEFAULT 'Registrada',
    `creado_por` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`dependencia_solicitante_id`) REFERENCES `dependencias`(`id`),
    FOREIGN KEY (`creado_por`) REFERENCES `users`(`id`),
    INDEX `idx_solicitud_estado` (`estado_solicitud`),
    INDEX `idx_solicitud_fecha_limite` (`fecha_limite`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `asignaciones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `solicitud_id` INT NOT NULL,
    `comunicador_id` INT NOT NULL,
    `fecha_asignacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `asignado_por` INT NOT NULL,
    `motivo_reasignacion` VARCHAR(255) NULL,
    `es_activa` TINYINT(1) DEFAULT 1,
    FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`comunicador_id`) REFERENCES `comunicadores`(`id`),
    FOREIGN KEY (`asignado_por`) REFERENCES `users`(`id`)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 4. PRODUCTOS COMUNICACIONALES, REPOSICIÓN Y VERSIONADO
-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `productos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `solicitud_id` INT NOT NULL,
    `tipo_producto_id` INT NOT NULL,
    `titulo` VARCHAR(200) NOT NULL,
    `comunicador_id` INT NOT NULL,
    `estado_producto` ENUM('Borrador', 'En Revision', 'Observado', 'Aprobado', 'Publicado', 'Archivado') DEFAULT 'Borrador',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`tipo_producto_id`) REFERENCES `tipos_producto`(`id`),
    FOREIGN KEY (`comunicador_id`) REFERENCES `comunicadores`(`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `producto_versiones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `producto_id` INT NOT NULL,
    `numero_version` INT NOT NULL DEFAULT 1,
    `archivo_nombre_orig` VARCHAR(255) NOT NULL,
    `archivo_ruta` VARCHAR(255) NOT NULL,
    `archivo_mime` VARCHAR(100) NOT NULL,
    `archivo_tamano_bytes` BIGINT NOT NULL,
    `archivo_hash_sha256` VARCHAR(64) NOT NULL,
    `enlace_publicacion` VARCHAR(255) NULL,
    `observaciones_revision` TEXT NULL,
    `evaluado_por` INT NULL,
    `fecha_evaluacion` DATETIME NULL,
    `creado_por` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`producto_id`) REFERENCES `productos`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`evaluado_por`) REFERENCES `users`(`id`),
    FOREIGN KEY (`creado_por`) REFERENCES `users`(`id`),
    UNIQUE KEY `uk_producto_version` (`producto_id`, `numero_version`)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 5. AUDITORÍA E INMUTABILIDAD DE CAMBIOS
-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `auditoria_logs` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `accion` VARCHAR(100) NOT NULL,
    `entidad` VARCHAR(50) NOT NULL,
    `entidad_id` INT NOT NULL,
    `datos_previos` JSON NULL,
    `datos_nuevos` JSON NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_auditoria_entidad` (`entidad`, `entidad_id`),
    INDEX `idx_auditoria_fecha` (`created_at`)
) ENGINE=InnoDB;
