<?php /* app/views/ventas/index.php - Gestión de ventas del bar */ ?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-cash-register" aria-hidden="true"></i> Gestión de Ventas</h1>
    <p class="page-subtitle">Transacciones y análisis del bar.</p>
</div>

<!-- Abrir nueva venta -->
<div class="action-buttons">
 
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
<div class="content-grid content-grid-full">
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
                            <button type="button" class="action-btn view-btn" title="Ver detalle"
                                    onclick="verDetalleVenta(<?= (int)$v['id'] ?>)">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </button>
                            <?php if ($v['estado'] === 'abierto'): ?>
                            <form method="POST" action="<?= BASE_URL ?>/ventas/cerrar"
                                  style="display:inline"
                                  onsubmit="return confirm('¿Cerrar esta venta?')">
                                <input type="hidden" name="csrf_token"
                                       value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
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

<!-- Modal: Detalle de venta -->
<div class="modal-overlay" id="modalDetalleVenta" style="display:none;">
    <div class="modal-box modal-detalle">
        <div class="modal-detalle-header">
            <h3><i class="fas fa-receipt" aria-hidden="true"></i> Detalle de la Venta <span id="modalVentaId"></span></h3>
            <button type="button" class="modal-close" onclick="cerrarModalDetalle()" aria-label="Cerrar">&times;</button>
        </div>
        <table class="modal-detalle-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unit.</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody id="modalDetalleBody">
                <tr><td colspan="4" class="text-center text-muted">Cargando...</td></tr>
            </tbody>
        </table>
        <div class="modal-detalle-total">Total: <strong>$<span id="modalTotalVenta">0.00</span></strong></div>
    </div>
</div>

<script>
const VENTAS_BASE_URL = '<?= BASE_URL ?>';

function abrirModalDetalle() {
    document.getElementById('modalDetalleVenta').style.display = 'flex';
}

function cerrarModalDetalle() {
    document.getElementById('modalDetalleVenta').style.display = 'none';
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
}

async function verDetalleVenta(id) {
    document.getElementById('modalVentaId').textContent = '#BAR' + String(id).padStart(3, '0');
    const tbody = document.getElementById('modalDetalleBody');
    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Cargando...</td></tr>';
    document.getElementById('modalTotalVenta').textContent = '0.00';
    abrirModalDetalle();

    try {
        const resp = await fetch(`${VENTAS_BASE_URL}/ventas/detalle/${id}`);
        const data = await resp.json();

        if (!data.success || !Array.isArray(data.detalles) || data.detalles.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Sin productos registrados.</td></tr>';
            return;
        }

        let total = 0;
        tbody.innerHTML = data.detalles.map(d => {
            const cantidad = parseInt(d.cantidad, 10) || 0;
            const precio = parseFloat(d.precio_unitario) || 0;
            const subtotal = parseFloat(d.subtotal) || 0;
            total += subtotal;
            return `<tr>
                <td>${escapeHtml(d.nombre)}</td>
                <td>${cantidad}</td>
                <td>$${precio.toFixed(2)}</td>
                <td>$${subtotal.toFixed(2)}</td>
            </tr>`;
        }).join('');

        document.getElementById('modalTotalVenta').textContent = total.toFixed(2);
    } catch (e) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Error al cargar el detalle.</td></tr>';
    }
}

document.getElementById('modalDetalleVenta').addEventListener('click', function (e) {
    if (e.target === this) cerrarModalDetalle();
});
</script>

