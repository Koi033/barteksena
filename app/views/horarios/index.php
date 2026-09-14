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
                <label for="hFechaInicio">Fecha Inicio *</label>
                <input type="date" id="hFechaInicio" name="fecha_inicio" required
                       min="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label for="hFechaFin">Fecha Fin *</label>
                <input type="date" id="hFechaFin" name="fecha_fin" required
                       min="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label>Días de la Semana *</label>
                <div class="dias-semana-checks">
                    <?php
                        $diasSemana = [1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom'];
                        foreach ($diasSemana as $numDia => $etiquetaDia):
                    ?>
                        <label class="dia-check">
                            <input type="checkbox" name="dias[]" value="<?= $numDia ?>">
                            <?= $etiquetaDia ?>
                        </label>
                    <?php endforeach; ?>
                </div>
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
            <form method="POST" action="<?= BASE_URL ?>/horarios/cambiarEstadoMasivo" id="formPendientes">
                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">

                <div class="pending-bulk-bar">
                    <label class="dia-check">
                        <input type="checkbox" id="chkTodosPendientes">
                        Seleccionar todos (<?= count($pendientes) ?>)
                    </label>
                    <div class="pending-bulk-actions">
                        <button type="submit" name="estado" value="aprobado"
                                class="btn-action btn-approve"
                                onclick="return confirmarMasivo('aprobar');">
                            <i class="fas fa-check-double" aria-hidden="true"></i> Aprobar seleccionados
                        </button>
                        <button type="submit" name="estado" value="rechazado"
                                class="btn-action btn-reject"
                                onclick="return confirmarMasivo('rechazar');">
                            <i class="fas fa-times" aria-hidden="true"></i> Rechazar seleccionados
                        </button>
                    </div>
                </div>

                <?php foreach ($pendientes as $p): ?>
                <div class="schedule-item">
                    <label class="schedule-check">
                        <input type="checkbox" name="ids[]" value="<?= (int) $p['id'] ?>" class="chk-pendiente">
                    </label>
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
                </div>
                <?php endforeach; ?>
            </form>

            <script>
                (function () {
                    var chkTodos = document.getElementById('chkTodosPendientes');
                    var chks     = document.querySelectorAll('.chk-pendiente');
                    if (!chkTodos) return;
                    chkTodos.addEventListener('change', function () {
                        chks.forEach(function (c) { c.checked = chkTodos.checked; });
                    });
                })();

                function confirmarMasivo(accion) {
                    var marcados = document.querySelectorAll('.chk-pendiente:checked').length;
                    if (marcados === 0) {
                        alert('Selecciona al menos un horario.');
                        return false;
                    }
                    return confirm('¿Seguro que deseas ' + accion + ' ' + marcados + ' horario(s)?');
                }
            </script>
        <?php endif; ?>
    </div>

</div><!-- /.content-grid -->
