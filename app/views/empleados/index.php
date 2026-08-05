<?php/* app/views/empleados/index.php - Lista de empleados con DataTable */?>
<?php  $tokenEliminar = generarTokenCSRF('eliminar_emp');?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-users" aria-hidden="true"></i> Gestión de Empleados</h1>
    <p class="page-subtitle">Administra el personal del bar.</p>
</div>

<!-- Botones de acción -->
<div class="action-buttons">
    <a href="<?= BASE_URL ?>/empleados/crear" class="btn-primary">+ Agregar Empleado</a>
</div>

<!-- Barra de búsqueda y filtros (server-side simplificado) -->
<form method="GET" action="<?= BASE_URL ?>/empleados" class="controls-section">
    <input type="text" name="busqueda" class="search-bar"
           placeholder="Buscar por nombre, puesto o email..."
           value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>">

    <select name="departamento" class="filter-dropdown">
        <option value="">Todos los departamentos</option>
        <?php foreach ($departamentos as $d): ?>
            <option value="<?= htmlspecialchars($d['departamento'], ENT_QUOTES, 'UTF-8') ?>"
                <?= $deptoFiltro === $d['departamento'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($d['departamento'], ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn-filter"><i class="fas fa-search" aria-hidden="true"></i> Buscar</button>
    <a href="<?= BASE_URL ?>/empleados" class="btn-secondary"><i class="fas fa-times" aria-hidden="true"></i> Limpiar</a>
</form>

<!-- Tabla con DataTables -->
<div class="table-section">
    <table class="bartek-datatable dt-buttons" id="tablaEmpleados">
        <thead>
            <tr>
                <th>Nombre Completo</th>
                <th>Puesto</th>
                <th>Departamento</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($empleados)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        No se encontraron empleados.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($empleados as $emp): ?>
                <tr>
                    <td class="employee-name">
                        <?= htmlspecialchars($emp['nombre_completo'], ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td><?= htmlspecialchars($emp['puesto'],         ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($emp['departamento'],   ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="email-cell">
                        <?= htmlspecialchars($emp['email'],          ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td><?= htmlspecialchars($emp['telefono'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="actions-cell">
                        <!-- Editar -->
                        <a href="<?= BASE_URL ?>/empleados/editar/<?= (int)$emp['id'] ?>"
                           class="action-btn edit-btn" title="Editar"><i class="fas fa-edit" aria-hidden="true"></i></a>

                        <!-- Eliminar (formulario POST con CSRF) -->
                        <form method="POST" action="<?= BASE_URL ?>/empleados/eliminar"
                              style="display:inline"
                              onsubmit="return confirm('¿Eliminar a <?= htmlspecialchars(addslashes($emp['nombre_completo']), ENT_QUOTES, 'UTF-8') ?>?')">
                            <input type="hidden" name="csrf_token"
                                  value="<?= htmlspecialchars($tokenEliminar, ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="id" value="<?= (int)$emp['id'] ?>">
                            <button type="submit" class="action-btn delete-btn" title="Eliminar"><i class="fas fa-trash" aria-hidden="true"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Paginación server-side -->
<div class="pagination-section">
    <div class="pagination-info">
        Mostrando página <?= (int)$paginaActual ?> de <?= (int)$totalPaginas ?>
        · Total: <?= (int)$total ?> empleados
    </div>
    <div class="pagination-controls">
        <?php if ($paginaActual > 1): ?>
            <a href="?pagina=<?= $paginaActual - 1 ?>&busqueda=<?= urlencode($busqueda) ?>&departamento=<?= urlencode($deptoFiltro) ?>"
               class="pagination-btn"><i class="fas fa-arrow-left" aria-hidden="true"></i> Anterior</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <a href="?pagina=<?= $i ?>&busqueda=<?= urlencode($busqueda) ?>&departamento=<?= urlencode($deptoFiltro) ?>"
               class="pagination-btn <?= $i === (int)$paginaActual ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($paginaActual < $totalPaginas): ?>
            <a href="?pagina=<?= $paginaActual + 1 ?>&busqueda=<?= urlencode($busqueda) ?>&departamento=<?= urlencode($deptoFiltro) ?>"
               class="pagination-btn">Siguiente <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        <?php endif; ?>
    </div>
</div>
