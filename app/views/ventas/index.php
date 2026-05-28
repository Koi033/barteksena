<?php /* app/views/ventas/index.php - Gestión de ventas del bar */ ?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-cash-register" aria-hidden="true"></i> Gestión de Ventas</h1>
    <p class="page-subtitle">Transacciones y análisis del bar.</p>
</div>

<!-- Abrir nueva venta -->
<div class="action-buttons">
    <button class="btn-primary" onclick="document.getElementById('modalNuevaVenta').style.display='flex'">
        <i class="fas fa-plus" aria-hidden="true"></i> Abrir Nueva Mesa / Venta
    </button>
    <button class="btn-secondary" onclick="window.print()"><i class="fas fa-print" aria-hidden="true"></i> Generar Reporte</button>
</div>

<!-- Tarjetas resumen -->
<div class="sales-summary">
    <div class="summary-card">
        <h3>Ventas Hoy</h3>
        <div class="value">$<?= number_format($ventasHoy, 2) ?></div>
        <div class="subtitle">Transacciones cerradas</div>
    </div>
    <div class="summary-card">
        <h3>Ventas del Mes</h3>
        <div class="value">$<?= number_format($ventasMes, 2) ?></div>
        <div class="subtitle">Acumulado mensual</div>
    </div>
</div>

<!-- Grid: Top bebidas + Tabla transacciones -->
<div class="content-grid">
    <!-- Top bebidas -->
    <div class="top-products">
        <h2><i class="fas fa-cocktail" aria-hidden="true"></i> Bebidas Más Vendidas</h2>
        <?php if (empty($topBebidas)): ?>
            <p class="text-muted">Sin datos aún.</p>
        <?php else: ?>
            <?php foreach ($topBebidas as $beb): ?>
            <div class="product-item">
                <span class="product-name">
                    <?= htmlspecialchars($beb['nombre'], ENT_QUOTES, 'UTF-8') ?>
                </span>
                <span class="product-quantity">
                    <?= (int)$beb['total_vendido'] ?> uds.
                </span>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Tabla de transacciones con DataTable -->
<div class="transactions">
    <h2>Transacciones Recientes</h2>
    <div class="table-section">
        <table class="bartek-datatable" id="tablaVentas">
            <thead>
                <tr>
                    <th>ID Venta</th>
                    <th>Fecha</th>
                    <th>Mesa / Cliente</th>
                    <th>Empleado</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ventas)): ?>
                    <tr><td colspan="7" class="text-center text-muted">Sin transacciones.</td></tr>
                <?php else: ?>
                    <?php foreach ($ventas as $v): ?>
                    <tr>
                        <td class="transaction-id">#BAR<?= str_pad((int)$v['id'], 3, '0', STR_PAD_LEFT) ?></td>
                        <td><?= htmlspecialchars($v['creado_en'],  ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($v['mesa'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($v['empleado'],   ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="price-cell">$<?= number_format((float)$v['total'], 2) ?></td>
                        <td>
                            <?php
                            $clases = [
                                'abierto'    => 'status-pending',
                                'cerrado'    => 'status-completed',
                                'cancelado'  => 'status-cancelled',
                            ];
                            $etiquetas = [
                                'abierto'   => 'Abierto',
                                'cerrado'   => 'Cerrado',
                                'cancelado' => 'Cancelado',
                            ];
                            $est = $v['estado'] ?? 'abierto';
                            ?>
                            <span class="status-badge <?= $clases[$est] ?? '' ?>">
                                <?= $etiquetas[$est] ?? ucfirst($est) ?>
                            </span>
                        </td>
                        <td class="actions-cell">
                            <?php if ($v['estado'] === 'abierto'): ?>
                            <form method="POST" action="<?= BASE_URL ?>/ventas/cerrar"
                                  style="display:inline"
                                  onsubmit="return confirm('¿Cerrar esta venta?')">
                                <input type="hidden" name="csrf_token"
                                       value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
                                <button type="submit" class="action-btn edit-btn" title="Cerrar venta">✔</button>
                            </form>
                            <?php endif; ?>
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
            Página <?= (int)$paginaActual ?> de <?= (int)$totalPaginas ?>
        </div>
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

<!-- Modal: Nueva Venta -->
<div id="modalNuevaVenta" class="modal-overlay" style="display:none">
    <div class="modal-box">
        <h2><i class="fas fa-shopping-cart" aria-hidden="true"></i> Abrir Nueva Venta</h2>
        <form method="POST" action="<?= BASE_URL ?>/ventas/guardar">
            <input type="hidden" name="csrf_token"
                   value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="mvMesa">Mesa o Cliente</label>
                <input type="text" id="mvMesa" name="mesa"
                       placeholder="Ej: Mesa 5 / Cliente A" maxlength="30" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Abrir Venta</button>
                <button type="button" class="btn-secondary"
                        onclick="document.getElementById('modalNuevaVenta').style.display='none'">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
