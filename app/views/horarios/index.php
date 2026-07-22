<?php /* app/views/horarios/index.php - Gestión de horarios de empleados */ ?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-clock" aria-hidden="true"></i> Gestión de Horarios</h1>
    <p class="page-subtitle">Asigna y aprueba turnos de trabajo.</p>
</div>

<div class="content-grid">

    <!-- Tabla de horarios con DataTable -->
    <div class="calendar-section">
        <h2>Horarios Registrados</h2>
        <div class="table-section">
            <table class="bartek-datatable" id="tablaHorarios">
                <thead>
                    <tr>
                        <th>Empleado</th>
                        <th>Fecha</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($horarios)): ?>
                        <tr><td colspan="6" class="text-center text-muted">Sin horarios registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach ($horarios as $h): ?>
                        <?php
                            $estadoClase = [
                                'pendiente' => 'status-pending',
                                'aprobado'  => 'status-completed',
                                'rechazado' => 'status-cancelled',
                            ][$h['estado']] ?? 'status-pending';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($h['nombre_completo'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($h['fecha'],           ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($h['hora_inicio'],     ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($h['hora_fin'],        ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <span class="status-badge <?= $estadoClase ?>">
                                    <?= ucfirst(htmlspecialchars($h['estado'], ENT_QUOTES, 'UTF-8')) ?>
                                </span>
                            </td>
                            <td class="actions-cell">
                                <!-- Eliminar horario -->
                                <form method="POST" action="<?= BASE_URL ?>/horarios/eliminar"
                                      style="display:inline"
                                      onsubmit="return confirm('¿Eliminar este horario?')">
                                    <input type="hidden" name="csrf_token"
                                           value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id" value="<?= (int)$h['id'] ?>">
                                    <button type="submit" class="action-btn delete-btn" title="Eliminar"><i class="fas fa-trash" aria-hidden="true"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="pagination-section">
            <div class="pagination-info">Página <?= (int)$paginaActual ?> de <?= (int)$totalPaginas ?></div>
            <div class="pagination-controls">
                <?php if ($paginaActual > 1): ?>
                    <a href="?pagina=<?= $paginaActual - 1 ?>" class="pagination-btn"><i class="fas fa-arrow-left" aria-hidden="true"></i> Anterior</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <a href="?pagina=<?= $i ?>"
                       class="pagination-btn <?= $i === (int)$paginaActual ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                <?php if ($paginaActual < $totalPaginas): ?>
                    <a href="?pagina=<?= $paginaActual + 1 ?>" class="pagination-btn">Siguiente <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Formulario crear horario -->
    <div class="form-section">
        <h2>Crear Nuevo Horario</h2>
        <form method="POST" action="<?= BASE_URL ?>/horarios/guardar" novalidate>
            <input type="hidden" name="csrf_token"
                   value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="hEmp">Empleado *</label>
                <select id="hEmp" name="empleado_id" required>
                    <option value="">— Selecciona —</option>
                    <?php foreach ($empleados as $emp): ?>
                        <option value="<?= (int)$emp['id'] ?>">
                            <?= htmlspecialchars($emp['nombre_completo'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="hFecha">Fecha *</label>
                <input type="date" id="hFecha" name="fecha" required
                       min="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label>Hora de Inicio *</label>
                <div class="time-inputs">
                    <input type="time" name="hora_inicio" required>
                </div>
            </div>

            <div class="form-group">
                <label>Hora de Fin *</label>
                <div class="time-inputs">
                    <input type="time" name="hora_fin" required>
                </div>
            </div>

            <button type="submit" class="btn-submit"><i class="fas fa-save" aria-hidden="true"></i> Guardar Horario</button>
        </form>
    </div>

    <!-- Horarios pendientes de aprobación -->
    <div class="pending-schedules">
        <h2><i class="fas fa-hourglass-half" aria-hidden="true"></i> Pendientes de Aprobación</h2>

        <?php if (empty($pendientes)): ?>
            <p class="text-muted">No hay horarios pendientes.</p>
        <?php else: ?>
            <?php foreach ($pendientes as $p): ?>
            <div class="schedule-item">
                <div class="schedule-info">
                    <div class="schedule-employee">
                        <?= htmlspecialchars($p['nombre_completo'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="schedule-time">
                        <?= htmlspecialchars($p['fecha'],       ENT_QUOTES, 'UTF-8') ?>
                        (<?= htmlspecialchars($p['hora_inicio'], ENT_QUOTES, 'UTF-8') ?>
                        — <?= htmlspecialchars($p['hora_fin'],   ENT_QUOTES, 'UTF-8') ?>)
                    </div>
                </div>
                <div class="schedule-actions">
                    <!-- Aprobar -->
                    <form method="POST" action="<?= BASE_URL ?>/horarios/cambiarEstado" style="display:inline">
                        <input type="hidden" name="csrf_token"
                               value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="id"     value="<?= (int)$p['id'] ?>">
                        <input type="hidden" name="estado" value="aprobado">
                        <button type="submit" class="btn-action btn-approve"><i class="fas fa-check" aria-hidden="true"></i> Aprobar</button>
                    </form>
                    <!-- Rechazar -->
                    <form method="POST" action="<?= BASE_URL ?>/horarios/cambiarEstado" style="display:inline">
                        <input type="hidden" name="csrf_token"
                               value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="id"     value="<?= (int)$p['id'] ?>">
                        <input type="hidden" name="estado" value="rechazado">
                        <button type="submit" class="btn-action btn-reject"><i class="fas fa-times" aria-hidden="true"></i> Rechazar</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div><!-- /.content-grid -->

    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
<script src="https://files.bpcontent.cloud/2026/06/25/20/20260625200158-LXETZX3T.js" defer></script>
    