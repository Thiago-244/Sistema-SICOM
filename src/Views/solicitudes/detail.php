<?php
$pageTitle = "Detalle del Ticket: " . $solicitud['codigo_ticket'];
$pageSubtitle = "Gestión integral, trazabilidad y control de versiones del producto comunicacional";
require __DIR__ . '/../layouts/header.php';
$user = Auth::user();
?>

<!-- Action Bar & Timeline Status Stepper -->
<div class="card" style="margin-bottom: 24px; background: linear-gradient(135deg, #0f172a, #1e293b); color: white;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <span class="badge badge-<?= strtolower(str_replace(' ', '-', $solicitud['estado_solicitud'])) ?>" style="font-size: 0.9rem; padding: 6px 14px;">
                <?= $solicitud['estado_solicitud'] ?>
            </span>
            <span class="badge badge-prioridad-<?= strtolower($solicitud['prioridad']) ?>" style="font-size: 0.9rem; padding: 6px 14px; margin-left: 8px;">
                Prioridad <?= $solicitud['prioridad'] ?>
            </span>
        </div>
        <div style="font-size: 0.85rem; color: #94a3b8;">
            Ticket #<strong><?= htmlspecialchars($solicitud['codigo_ticket']) ?></strong> | Generado el <?= date('d/m/Y H:i', strtotime($solicitud['created_at'])) ?>
        </div>
    </div>

    <!-- Stepper Visual -->
    <div style="display: flex; justify-content: space-between; position: relative; padding: 10px 0;">
        <?php 
        $steps = ['Registrada', 'Asignada', 'En Proceso', 'En Revision', 'Aprobada'];
        $currentIdx = array_search($solicitud['estado_solicitud'], $steps);
        if ($currentIdx === false) $currentIdx = 1;
        foreach ($steps as $idx => $stepName): 
            $isDone = $idx <= $currentIdx;
        ?>
            <div style="display: flex; flex-direction: column; align-items: center; flex: 1; z-index: 2;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: <?= $isDone ? '#0284c7' : '#334155' ?>; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; margin-bottom: 8px;">
                    <?= $isDone ? '<i class="fa-solid fa-check"></i>' : ($idx + 1) ?>
                </div>
                <div style="font-size: 0.75rem; color: <?= $isDone ? '#ffffff' : '#94a3b8' ?>; font-weight: 600; text-align: center;">
                    <?= $stepName ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- Columna Izquierda: Información de Solicitud y Productos -->
    <div>
        <!-- Información Principal -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-file-lines"></i> Información del Requerimiento
                </div>
            </div>

            <h2 style="font-size: 1.3rem; margin-bottom: 12px; color: var(--text-primary);">
                <?= htmlspecialchars($solicitud['titulo_actividad']) ?>
            </h2>

            <p style="color: var(--text-secondary); margin-bottom: 20px; white-space: pre-line;">
                <?= htmlspecialchars($solicitud['descripcion']) ?>
            </p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div>
                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Dependencia Solicitante</div>
                    <div style="font-weight: 600; font-size: 0.95rem;"><?= htmlspecialchars($solicitud['dependencia_nombre']) ?></div>
                </div>

                <div>
                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Lugar del Evento</div>
                    <div style="font-weight: 600; font-size: 0.95rem;"><?= htmlspecialchars($solicitud['lugar']) ?></div>
                </div>

                <div>
                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Fecha del Evento</div>
                    <div style="font-weight: 600; font-size: 0.95rem; color: #0284c7;"><?= date('d/m/Y H:i', strtotime($solicitud['fecha_evento'])) ?></div>
                </div>

                <div>
                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Fecha Límite SLA</div>
                    <div style="font-weight: 700; font-size: 0.95rem; color: #dc2626;"><?= date('d/m/Y H:i', strtotime($solicitud['fecha_limite'])) ?></div>
                </div>
            </div>
        </div>

        <!-- Productos Comunicacionales & Entregas de Versiones -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-box-archive"></i> Productos Comunicacionales & Control de Versiones
                </div>
                <?php if ($user['rol_id'] != 3): ?>
                    <button type="button" onclick="toggleModal('modalNuevoProducto')" class="btn btn-primary" style="font-size: 0.8rem;">
                        <i class="fa-solid fa-plus"></i> Crear Contenedor de Producto
                    </button>
                <?php endif; ?>
            </div>

            <?php if (empty($productos)): ?>
                <div style="text-align: center; color: #94a3b8; padding: 30px; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;">
                    <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                    No hay productos asociados a esta solicitud todavía. El comunicador o la jefatura pueden registrar el contenedor del producto.
                </div>
            <?php else: ?>
                <?php foreach ($productos as $prod): 
                    $pDetalle = App\Models\Producto::find($prod['id']);
                ?>
                    <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; margin-bottom: 20px; background: #ffffff;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                            <div>
                                <h3 style="font-size: 1.05rem; font-weight: 700; color: #0f172a;">
                                    <?= htmlspecialchars($prod['titulo']) ?>
                                </h3>
                                <span style="font-size: 0.78rem; color: #64748b;">
                                    Tipo: <strong><?= htmlspecialchars($prod['tipo_producto_nombre']) ?></strong> | Total Versiones: <strong><?= $prod['total_versiones'] ?></strong>
                                </span>
                            </div>
                            <div>
                                <span class="badge badge-<?= strtolower(str_replace(' ', '-', $prod['estado_producto'])) ?>">
                                    <?= $prod['estado_producto'] ?>
                                </span>
                            </div>
                        </div>

                        <!-- Botón para subir nueva versión -->
                        <?php if ($user['rol_id'] == 1 || $user['comunicador_id'] == $prod['comunicador_id']): ?>
                            <div style="margin-bottom: 16px;">
                                <button type="button" onclick="openSubirVersionModal(<?= $prod['id'] ?>, '<?= htmlspecialchars($prod['titulo'], ENT_QUOTES) ?>')" class="btn btn-secondary" style="font-size: 0.8rem;">
                                    <i class="fa-solid fa-cloud-arrow-up"></i> Entregar Nueva Versión (N+1)
                                </button>
                            </div>
                        <?php endif; ?>

                        <!-- Historial de Versiones del Producto -->
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <?php foreach ($pDetalle['versiones'] as $v): ?>
                                <div style="background: #f8fafc; border-left: 4px solid #0284c7; padding: 12px 16px; border-radius: 6px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                        <div style="font-weight: 700; font-size: 0.88rem; color: #0284c7;">
                                            Versión <?= $v['numero_version'] ?> 
                                            <span style="font-weight: 400; color: #64748b; font-size: 0.75rem;">(Subido por <?= htmlspecialchars($v['creador_username']) ?> el <?= date('d/m/Y H:i', strtotime($v['created_at'])) ?>)</span>
                                        </div>
                                        <div>
                                            <?php if (str_contains($v['mime'], 'url/external-stream')): ?>
                                                <a href="<?= htmlspecialchars($v['archivo_ruta']) ?>" target="_blank" class="btn btn-primary" style="padding: 4px 10px; font-size: 0.75rem;">
                                                    <i class="fa-solid fa-external-link"></i> Abrir Enlace Nube / Video
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= $baseUrl ?>/<?= htmlspecialchars($v['archivo_ruta']) ?>" target="_blank" class="btn btn-secondary" style="padding: 4px 10px; font-size: 0.75rem;">
                                                    <i class="fa-solid fa-download"></i> Descargar Archivo (<?= round($v['archivo_tamano_bytes']/1024/1024, 2) ?> MB)
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <?php if (!empty($v['enlace_publicacion'])): ?>
                                        <div style="font-size: 0.8rem; margin-bottom: 4px;">
                                            <i class="fa-solid fa-link" style="color: #0284c7;"></i> Enlace Publicación: <a href="<?= htmlspecialchars($v['enlace_publicacion']) ?>" target="_blank"><?= htmlspecialchars($v['enlace_publicacion']) ?></a>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($v['observaciones_revision'])): ?>
                                        <div style="font-size: 0.82rem; background: #ffffff; padding: 8px; border-radius: 6px; border: 1px solid #e2e8f0; margin-top: 6px;">
                                            <strong>Comentarios / Observaciones de Revisión:</strong> <?= htmlspecialchars($v['observaciones_revision']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Formulario de Evaluación por la Jefatura -->
                                    <?php if (($user['rol_id'] == 1 || $user['rol_id'] == 4) && $prod['estado_producto'] === 'En Revision'): ?>
                                        <div style="margin-top: 12px; background: #fffbe6; padding: 12px; border-radius: 6px; border: 1px solid #ffe58f;">
                                            <div style="font-weight: 700; font-size: 0.85rem; color: #b45309; margin-bottom: 8px;">
                                                <i class="fa-solid fa-gavel"></i> Evaluación de la Jefatura
                                            </div>
                                            <form action="<?= $baseUrl ?>/productos/evaluar" method="POST">
                                                <input type="hidden" name="producto_id" value="<?= $prod['id'] ?>">
                                                <input type="hidden" name="solicitud_id" value="<?= $solicitud['id'] ?>">

                                                <div class="form-group" style="margin-bottom: 8px;">
                                                    <textarea name="observaciones_jefatura" class="form-control" rows="2" placeholder="Ingrese observaciones concretas o motivo de aprobación..." required></textarea>
                                                </div>

                                                <div style="display: flex; gap: 10px;">
                                                    <button type="submit" name="decision" value="Aprobar" class="btn btn-success" style="padding: 6px 14px; font-size: 0.8rem;">
                                                        <i class="fa-solid fa-check"></i> Aprobar Producto
                                                    </button>
                                                    <button type="submit" name="decision" value="Observar" class="btn btn-danger" style="padding: 6px 14px; font-size: 0.8rem;">
                                                        <i class="fa-solid fa-triangle-exclamation"></i> Emitir Observaciones
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Columna Derecha: Asignación de Responsable & Motor de Reglas -->
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-user-check"></i> Responsable Asignado
                </div>
            </div>

            <?php if ($solicitud['comunicador_nombres']): ?>
                <div style="background: #e0f2fe; border: 1px solid #bae6fd; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                    <div style="font-weight: 700; font-size: 1rem; color: #0369a1;">
                        <?= htmlspecialchars($solicitud['comunicador_nombres'] . ' ' . $solicitud['comunicador_apellidos']) ?>
                    </div>
                    <div style="font-size: 0.8rem; color: #0284c7; margin-bottom: 8px;">
                        <?= htmlspecialchars($solicitud['comunicador_email']) ?>
                    </div>
                    <div style="font-size: 0.75rem; color: #475569;">
                        Asignado el <?= date('d/m/Y H:i', strtotime($solicitud['fecha_asignacion'])) ?>
                    </div>
                </div>
            <?php else: ?>
                <div style="background: #fef3c7; border: 1px solid #fde68a; padding: 16px; border-radius: 8px; margin-bottom: 20px; color: #b45309; font-weight: 600; font-size: 0.9rem;">
                    <i class="fa-solid fa-circle-exclamation"></i> Solicitud pendiente de asignación de comunicador.
                </div>
            <?php endif; ?>

            <!-- Formulario de Asignación / Reasignación -->
            <?php if ($user['rol_id'] == 1 || $user['rol_id'] == 4): ?>
                <form action="<?= $baseUrl ?>/asignaciones/ejecutar" method="POST">
                    <input type="hidden" name="solicitud_id" value="<?= $solicitud['id'] ?>">

                    <div class="form-group">
                        <label class="form-label">Seleccionar Comunicador</label>
                        <select name="comunicador_id" class="form-select" required>
                            <option value="">-- Seleccionar --</option>
                            <?php foreach ($comunicadores as $com): ?>
                                <option value="<?= $com['id'] ?>" <?= ($solicitud['comunicador_id'] == $com['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($com['nombres'] . ' ' . $com['apellidos']) ?> (Carga: <?= $com['carga_activa'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Motivo (si es Reasignación)</label>
                        <input type="text" name="motivo_reasignacion" class="form-control" placeholder="ej. Ajuste por especialidad en video">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fa-solid fa-user-gear"></i> Confirmar Asignación
                    </button>
                </form>

                <!-- Matriz del Motor de Reglas -->
                <div style="margin-top: 24px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                    <h4 style="font-size: 0.88rem; font-weight: 700; color: #0f172a; margin-bottom: 12px;">
                        <i class="fa-solid fa-wand-magic-sparkles" style="color: #0284c7;"></i> Ranking Motor de Reglas
                    </h4>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <?php foreach ($evaluacionesReglas as $cand): ?>
                            <div style="padding: 10px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 0.8rem;">
                                <div style="display: flex; justify-content: space-between; font-weight: 700;">
                                    <span><?= htmlspecialchars($cand['nombre_completo']) ?></span>
                                    <span style="color: #0284c7;"><?= $cand['score'] ?> pts (<?= $cand['recomendacion'] ?>)</span>
                                </div>
                                <div style="font-size: 0.72rem; color: #64748b; margin-top: 2px;">
                                    <?= htmlspecialchars($cand['desglose']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal: Crear Nuevo Contenedor de Producto -->
<div id="modalNuevoProducto" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; background: #ffffff;">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-plus"></i> Crear Producto Comunicacional</div>
            <button type="button" onclick="toggleModal('modalNuevoProducto')" style="background:none; border:none; font-size:1.2rem; cursor:pointer;">&times;</button>
        </div>
        <form action="<?= $baseUrl ?>/productos/crear" method="POST">
            <input type="hidden" name="solicitud_id" value="<?= $solicitud['id'] ?>">
            <input type="hidden" name="comunicador_id" value="<?= $solicitud['comunicador_id'] ?? 1 ?>">

            <div class="form-group">
                <label class="form-label">Tipo de Producto</label>
                <select name="tipo_producto_id" class="form-select" required>
                    <option value="1">Nota de Prensa PDF</option>
                    <option value="2">Galería Fotográfica HD (Zip/Drive)</option>
                    <option value="3">Video Resumen MP4 / YouTube</option>
                    <option value="4">Flyer / Afiche Gráfico PNG</option>
                    <option value="5">Transmisión en Vivo (FB Live / YT)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Título del Producto</label>
                <input type="text" name="titulo" class="form-control" placeholder="ej. Nota de Prensa Cierre Año Escolar DREP 2026" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Guardar Contenedor</button>
        </form>
    </div>
</div>

<!-- Modal: Subir Nueva Versión -->
<div id="modalSubirVersion" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 550px; background: #ffffff;">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-cloud-arrow-up"></i> Entregar Nueva Versión de Producto</div>
            <button type="button" onclick="toggleModal('modalSubirVersion')" style="background:none; border:none; font-size:1.2rem; cursor:pointer;">&times;</button>
        </div>
        <form action="<?= $baseUrl ?>/productos/subir-version" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="producto_id" id="modalProduct_id">
            <input type="hidden" name="solicitud_id" value="<?= $solicitud['id'] ?>">

            <div class="form-group">
                <label class="form-label">Formato de Entrega</label>
                <select name="tipo_ingreso" id="selectTipoIngreso" class="form-select" onchange="toggleFormIngreso(this.value)">
                    <option value="archivo">Subir Archivo Local (<= 25MB)</option>
                    <option value="enlace">Enlace en la Nube (Google Drive / YouTube / Video)</option>
                </select>
            </div>

            <div class="form-group" id="groupArchivo">
                <label class="form-label">Seleccionar Archivo (PDF, DOCX, XLSX, JPG, PNG, MP4, ZIP)</label>
                <input type="file" name="archivo_evidencia" class="form-control">
            </div>

            <div class="form-group" id="groupEnlace" style="display: none;">
                <label class="form-label">URL de Google Drive / YouTube / Vimeo</label>
                <input type="url" name="url_nube" class="form-control" placeholder="https://drive.google.com/file/d/...">
            </div>

            <div class="form-group">
                <label class="form-label">Enlace de Publicación Final (Opcional)</label>
                <input type="url" name="enlace_publicacion" class="form-control" placeholder="https://facebook.com/drep/posts/...">
            </div>

            <div class="form-group">
                <label class="form-label">Observaciones / Comentarios del Comunicador</label>
                <textarea name="observaciones_comunicador" class="form-control" rows="2" placeholder="Notas sobre los cambios realizados en esta versión..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Subir y Notificar a Revisión</button>
        </form>
    </div>
</div>

<script>
function toggleModal(id) {
    const el = document.getElementById(id);
    el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'flex' : 'none';
}
function openSubirVersionModal(productId, productTitle) {
    document.getElementById('modalProduct_id').value = productId;
    toggleModal('modalSubirVersion');
}
function toggleFormIngreso(val) {
    if (val === 'enlace') {
        document.getElementById('groupArchivo').style.display = 'none';
        document.getElementById('groupEnlace').style.display = 'block';
    } else {
        document.getElementById('groupArchivo').style.display = 'block';
        document.getElementById('groupEnlace').style.display = 'none';
    }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
