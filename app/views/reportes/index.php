<?php /* app/views/reportes/index.php — Página de generación de reportes CSV */ ?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-chart-bar" aria-hidden="true"></i> Reportes</h1>
    <p class="page-subtitle">Descarga reportes en formato CSV listos para Excel.</p>
</div>

<div class="sales-summary">

    <!-- Reporte Empleados -->
    <div class="summary-card">
        <h3><i class="fas fa-users" aria-hidden="true"></i> Empleados</h3>
        <div class="subtitle" style="margin-bottom:1rem;">
            Lista completa de empleados activos con puestos y departamentos.
        </div>
        <a href="<?= BASE_URL ?>/reportes/empleados" class="btn-primary"
           style="display:inline-flex; width:100%; justify-content:center;">
            <i class="fas fa-download" aria-hidden="true"></i> Descargar CSV
        </a>
    </div>

    <!-- Reporte Inventario -->
    <div class="summary-card">
        <h3><i class="fas fa-boxes" aria-hidden="true"></i> Inventario</h3>
        <div class="subtitle" style="margin-bottom:1rem;">
            Stock actual de todas las bebidas con precios y alertas.
        </div>
        <a href="<?= BASE_URL ?>/reportes/inventario" class="btn-primary"
           style="display:inline-flex; width:100%; justify-content:center;">
            <i class="fas fa-download" aria-hidden="true"></i> Descargar CSV
        </a>
    </div>

    <!-- Reporte Ventas -->
    <div class="summary-card">
        <h3><i class="fas fa-dollar-sign" aria-hidden="true"></i> Ventas</h3>
        <div class="subtitle" style="margin-bottom:1rem;">
            Historial completo de transacciones con totales y estados.
        </div>
        <a href="<?= BASE_URL ?>/reportes/ventas" class="btn-primary"
           style="display:inline-flex; width:100%; justify-content:center;">
            <i class="fas fa-download" aria-hidden="true"></i> Descargar CSV
        </a>
    </div>

    <!-- Reporte Horarios -->
    <div class="summary-card">
        <h3><i class="fas fa-clock" aria-hidden="true"></i> Horarios</h3>
        <div class="subtitle" style="margin-bottom:1rem;">
            Todos los turnos asignados con estados de aprobación.
        </div>
        <a href="<?= BASE_URL ?>/reportes/horarios" class="btn-primary"
           style="display:inline-flex; width:100%; justify-content:center;">
            <i class="fas fa-download" aria-hidden="true"></i> Descargar CSV
        </a>
    </div>

</div>

<div class="table-section" style="margin-top:1.5rem; padding:1.2rem 1.5rem;">
    <p style="color:#888; font-size:0.88rem;">
        <i class="fas fa-info-circle" aria-hidden="true"></i> Los archivos CSV incluyen BOM UTF-8 para compatibilidad con Microsoft Excel y LibreOffice Calc.
</div>


    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
<script src="https://files.bpcontent.cloud/2026/06/25/20/20260625200158-LXETZX3T.js" defer></script>
    
