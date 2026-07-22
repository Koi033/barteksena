<?php /* app/views/dashboard/index.php - Panel principal con notificaciones */ ?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-bell" aria-hidden="true"></i> Notificaciones</h1>
    <p class="page-subtitle">Centro de actividad del bar en tiempo real</p>
</div>

<!-- Métricas rápidas -->
<div class="sales-summary">
    <div class="summary-card">
        <h3>Ventas Hoy</h3>
        <div class="value">$<?= number_format($ventasHoy, 2) ?></div>
        <div class="subtitle">Transacciones cerradas hoy</div>
    </div>
    <div class="summary-card">
        <h3>Ventas del Mes</h3>
        <div class="value">$<?= number_format($ventasMes, 2) ?></div>
        <div class="subtitle">Total acumulado del mes</div>
    </div>
    <div class="summary-card">
        <h3>Sin Leer</h3>
        <div class="value"><?= (int) $noLeidas ?></div>
        <div class="subtitle">Notificaciones pendientes</div>
    </div>
    <?php if (!empty($stockBajo)): ?>
    <div class="summary-card summary-alert">
        <h3><i class="fas fa-exclamation-triangle" aria-hidden="true"></i> Stock Bajo</h3>
        <div class="value"><?= count($stockBajo) ?></div>
        <div class="subtitle">Bebidas bajo mínimo</div>
    </div>
    <?php endif; ?>
</div>

<!-- Alertas de stock bajo -->
<?php if (!empty($stockBajo)): ?>
<div class="stock-alert-box">
    <h2><i class="fas fa-exclamation-triangle" aria-hidden="true"></i> Alertas de Reposición</h2>
    <ul class="stock-alert-list">
        <?php foreach ($stockBajo as $item): ?>
            <li>
                <strong><?= htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8') ?></strong>
                — Stock: <span class="stock-low"><?= (int)$item['stock_actual'] ?></span>
                / Mínimo: <?= (int)$item['stock_minimo'] ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<!-- Notificaciones con DataTable -->
<section class="recent-orders">
    <div class="section-header">
        <h2>Actividad Reciente</h2>
    </div>

    <?php $tokenCSRF = generarTokenCSRF('eliminar_notif'); ?>
    <div class="table-section">
        <table class="bartek-datatable" id="tablaNotificaciones">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notificaciones as $notif): ?>
                <tr>
                    <td>
                        <?php
                        $iconos = [
                            'pedido'   => '<i class="fas fa-shopping-cart" aria-hidden="true"></i>',
                            'stock'    => '<i class="fas fa-boxes" aria-hidden="true"></i>',
                            'empleado' => '<i class="fas fa-user" aria-hidden="true"></i>',
                            'caja'     => '<i class="fas fa-money-bill-wave" aria-hidden="true"></i>',
                            'sistema'  => '<i class="fas fa-cogs" aria-hidden="true"></i>',
                        ];
                        $tipo = htmlspecialchars($notif['tipo'], ENT_QUOTES, 'UTF-8');
                        echo ($iconos[$notif['tipo']] ?? '<i class="fas fa-bell" aria-hidden="true"></i>') . ' ' . ucfirst($tipo);
                        ?>
                    </td>
                    <td><?= htmlspecialchars($notif['titulo'],      ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($notif['descripcion'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($notif['creado_en'],   ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php if ($notif['leida']): ?>
                            <span class="status-badge status-completed">Leída</span>
                        <?php else: ?>
                            <span class="status-badge status-pending">Nueva</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions-cell">
                        <form method="POST" action="<?= BASE_URL ?>/dashboard/eliminar"
                              style="display:inline"
                              onsubmit="return confirm('¿Eliminar esta notificación?');">
                            <input type="hidden" name="csrf_token"
                                   value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="id" value="<?= (int)$notif['id'] ?>">
                            <button type="submit" class="action-btn delete-btn" title="Eliminar"><i class="fas fa-trash" aria-hidden="true"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
<script src="https://files.bpcontent.cloud/2026/06/25/20/20260625200158-LXETZX3T.js" defer></script>
    