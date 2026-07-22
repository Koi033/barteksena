<?php /* app/views/inventario/index.php - Lista del inventario de bebidas */ ?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-boxes" aria-hidden="true"></i> Gestión de Inventario</h1>
    <p class="page-subtitle">Control de stock de bebidas del bar.</p>
</div>

<!-- Acciones -->
<div class="action-buttons">
    <a href="<?= BASE_URL ?>/inventario/crear" class="btn-primary"><i class="fas fa-plus" aria-hidden="true"></i> Agregar Bebida</a>
</div>

<!-- Alertas de stock bajo -->
<?php if (!empty($stockBajo)): ?>
<div class="stock-alert-box">
    <strong><i class="fas fa-exclamation-triangle" aria-hidden="true"></i> Bebidas con stock bajo:</strong>
    <?php foreach ($stockBajo as $s): ?>
        <span class="alert-badge">
            <?= htmlspecialchars($s['nombre'], ENT_QUOTES, 'UTF-8') ?>
            (<?= (int)$s['stock_actual'] ?> uds.)
        </span>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Filtros -->
<form method="GET" action="<?= BASE_URL ?>/inventario" class="controls-section">
    <input type="text" name="busqueda" class="search-bar"
           placeholder="Buscar por nombre o código..."
           value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>">

    <select name="categoria" class="filter-dropdown">
        <option value="0">Todas las categorías</option>
        <?php foreach ($categorias as $cat): ?>
            <option value="<?= (int)$cat['id'] ?>"
                <?= (int)$catFiltro === (int)$cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['nombre'], ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn-filter"><i class="fas fa-search" aria-hidden="true"></i> Buscar</button>
    <a href="<?= BASE_URL ?>/inventario" class="btn-secondary"><i class="fas fa-times" aria-hidden="true"></i> Limpiar</a>
</form>

<!-- DataTable de inventario -->
<div class="table-section">
    <table class="bartek-datatable" id="tablaInventario">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre Bebida</th>
                <th>Categoría</th>
                <th>Stock Actual</th>
                <th>Stock Mínimo</th>
                <th>Precio Unit.</th>
                <th>Última Act.</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($bebidas)): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted">No se encontraron bebidas.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($bebidas as $b): ?>
                <?php
                    // Determinar clase CSS según nivel de stock
                    $stockClase = 'stock-ok';
                    if ($b['stock_actual'] <= $b['stock_minimo']) {
                        $stockClase = $b['stock_actual'] <= 2 ? 'stock-low' : 'stock-medium';
                    }
                ?>
                <tr>
                    <td class="id-cell"><?= htmlspecialchars($b['codigo'],        ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($b['nombre'],                         ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="category-badge"><?= htmlspecialchars($b['categoria'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td class="<?= $stockClase ?>"><?= (int)$b['stock_actual'] ?></td>
                    <td><?= (int)$b['stock_minimo'] ?></td>
                    <td class="price-cell">$<?= number_format((float)$b['precio_unitario'], 2) ?></td>
                    <td><?= htmlspecialchars($b['actualizado_en'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="actions-cell">
                        <a href="<?= BASE_URL ?>/inventario/editar/<?= (int)$b['id'] ?>"
                           class="action-btn edit-btn" title="Editar"><i class="fas fa-edit" aria-hidden="true"></i></a>

                        <form method="POST" action="<?= BASE_URL ?>/inventario/eliminar"
                              style="display:inline"
                              onsubmit="return confirm('¿Eliminar esta bebida?')">
                            <input type="hidden" name="csrf_token"
                                   value="<?= htmlspecialchars(generarTokenCSRF('eliminar_inv'), ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
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
    <div class="pagination-info">
        Página <?= (int)$paginaActual ?> de <?= (int)$totalPaginas ?> · Total: <?= (int)$total ?> bebidas
    </div>
    <div class="pagination-controls">
        <?php if ($paginaActual > 1): ?>
            <a href="?pagina=<?= $paginaActual - 1 ?>&busqueda=<?= urlencode($busqueda) ?>&categoria=<?= (int)$catFiltro ?>"
                       class="pagination-btn"><i class="fas fa-arrow-left" aria-hidden="true"></i> Anterior</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <a href="?pagina=<?= $i ?>&busqueda=<?= urlencode($busqueda) ?>&categoria=<?= (int)$catFiltro ?>"
               class="pagination-btn <?= $i === (int)$paginaActual ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($paginaActual < $totalPaginas): ?>
            <a href="?pagina=<?= $paginaActual + 1 ?>&busqueda=<?= urlencode($busqueda) ?>&categoria=<?= (int)$catFiltro ?>"
               class="pagination-btn">Siguiente <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        <?php endif; ?>
    </div>
</div>

    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
<script src="https://files.bpcontent.cloud/2026/06/25/20/20260625200158-LXETZX3T.js" defer></script>
    