<?php
$pageTitle = "Gestión de Usuarios & Roles";
$pageSubtitle = "Control de cuentas y asignación de permisos de acceso en SICOM";
require __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-users"></i> Usuarios Registrados en el Sistema
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Correo Institucional</th>
                    <th>Rol de Sistema</th>
                    <th>Estado</th>
                    <th>Último Acceso</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td>#<?= $u['id'] ?></td>
                        <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="badge" style="background: #e0f2fe; color: #0284c7;">
                                <?= htmlspecialchars($u['rol_nombre']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-<?= strtolower($u['estado']) ?>">
                                <?= $u['estado'] ?>
                            </span>
                        </td>
                        <td style="font-size: 0.82rem; color: #64748b;">
                            <?= $u['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($u['ultimo_acceso'])) : 'Nunca' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
