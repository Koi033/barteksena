<?php /* app/views/reservas/reservas.php */ ?>
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/reservas.css">

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-calendar-alt" aria-hidden="true"></i> Reservas</h1>
    <p class="page-description">Consulta y exporta las reservas registradas en el sistema.</p>
</div>

<div class="card card-dark">
    <div class="card-body">
        <?php if (empty($reservas)): ?>
            <div class="empty-state">
                <p>No hay reservas registradas.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="bartek-datatable bartek-datatable-buttons" id="tablaReservas">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Mesa</th>
                            <th>Personas</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Registrada</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $row): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['nombre_cliente'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['telefono'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><span class="badge badge-mesa">Mesa <?= htmlspecialchars($row['numero_mesa'], ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td><?= htmlspecialchars($row['personas'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['fecha'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['hora'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['creado_en'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <form method="POST" action="<?= BASE_URL ?>/reservas/eliminar"
                                          onsubmit="return confirm('¿Eliminar la reserva de <?= htmlspecialchars(addslashes($row['nombre_cliente']), ENT_QUOTES, 'UTF-8') ?> para la mesa <?= htmlspecialchars($row['numero_mesa'], ENT_QUOTES, 'UTF-8') ?>?')"
                                          style="display:inline-block; margin:0;">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                        <button type="submit" class="action-btn delete-btn" title="Eliminar reserva">
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

