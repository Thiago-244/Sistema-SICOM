-- ============================================================
-- SEEDERS INICIALES DEL SISTEMA SICOM - DREP
-- ============================================================

USE `sicom_db`;

-- 1. ROLES SOBERANOS
INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Administrador / Jefe RRPP', 'Jefe del Área de Relaciones Públicas e Imagen Institucional. Acceso operativo y funcional completo.'),
(2, 'Comunicador Social', 'Especialista responsable de atender solicitudes, producir contenidos y cargar evidencias.'),
(3, 'Autoridad / Consulta', 'Usuario directivo para monitoreo, dashboard, KPIs e indicadores ejecutivos sin modificación.'),
(4, 'Administrador Técnico', 'Gestión de infraestructura, parámetros, catálogos, auditoría y seguridad de usuarios.');

-- 2. CATÁLOGO DE ESPECIALIDADES
INSERT INTO `especialidades` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Fotografía Institucional', 'Toma, selección y tratamiento de fotografías en eventos y ceremonias.'),
(2, 'Redacción & Nota de Prensa', 'Elaboración de comunicados, notas periodísticas y síntesis informativas.'),
(3, 'Edición Audiovisual', 'Producción y edición de videos institucionales, reels y clips.'),
(4, 'Diseño Gráfico', 'Elaboración de afiches, flyers, infografías y piezas visuales para redes.'),
(5, 'Transmisión & Streaming', 'Cobertura en vivo vía redes sociales de actividades oficiales.');

-- 3. CATÁLOGO DE TIPOS DE PRODUCTO
INSERT INTO `tipos_producto` (`id`, `nombre`, `requiere_video`, `requiere_diseno`, `tiempo_estimado_horas`) VALUES
(1, 'Nota de Prensa PDF', 0, 0, 12),
(2, 'Galería Fotográfica HD (Zip/Drive)', 0, 0, 6),
(3, 'Video Resumen Institucional MP4', 1, 0, 48),
(4, 'Flyer / Afiche Gráfico PNG', 0, 1, 24),
(5, 'Transmisión en Vivo (FB Live / YT)', 1, 0, 4);

-- 4. DEPENDENCIAS INSTITUCIONALES DREP
INSERT INTO `dependencias` (`id`, `nombre`, `siglas`, `tipo`, `ugel_codigo`) VALUES
(1, 'Dirección Regional de Educación Piura - Dirección General', 'DREP-DG', 'Oficina DREP', 'DREP-01'),
(2, 'Oficina de Relaciones Públicas e Imagen Institucional', 'DREP-RRPP', 'Oficina DREP', 'DREP-02'),
(3, 'Dirección de Gestión Pedagógica', 'DREP-DGP', 'Oficina DREP', 'DREP-03'),
(4, 'UGEL Piura', 'UGEL-P', 'UGEL', 'UGEL-300'),
(5, 'UGEL Sullana', 'UGEL-S', 'UGEL', 'UGEL-301');

-- 5. USUARIOS INICIALES (Contraseña por defecto: Admin123!_ hash bcrypt)
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `rol_id`, `estado`) VALUES
(1, 'admin.rrpp', 'rrpp.jefatura@drep.gob.pe', '$2y$10$VltwucDW7Ztwnw5LuTlbruUBWeSN1Xt9DEtPAiPOpC9xomv3Fk9Be', 1, 'Activo'),
(2, 'comunicador1', 'comunicador1@drep.gob.pe', '$2y$10$VltwucDW7Ztwnw5LuTlbruUBWeSN1Xt9DEtPAiPOpC9xomv3Fk9Be', 2, 'Activo'),
(3, 'director.drep', 'direccion@drep.gob.pe', '$2y$10$VltwucDW7Ztwnw5LuTlbruUBWeSN1Xt9DEtPAiPOpC9xomv3Fk9Be', 3, 'Activo');

-- 6. PERFIL DE COMUNICADOR DE PRUEBA
INSERT INTO `comunicadores` (`id`, `user_id`, `dni`, `nombres`, `apellidos`, `telefono`, `cargo`, `dependencia_id`, `disponibilidad`, `estado`) VALUES
(1, 2, '45892134', 'Carlos Alberto', 'García Mendoza', '969123456', 'Especialista Audiovisual', 2, 'Disponible', 'Activo');

INSERT INTO `comunicador_especialidad` (`comunicador_id`, `especialidad_id`) VALUES
(1, 1),
(1, 3);
