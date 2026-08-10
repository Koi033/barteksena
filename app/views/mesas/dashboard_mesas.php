<?php /* app/views/mesas/dashboard_mesas.php */ ?>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/mesas.css">

<div class="page-header page-header-mesas">
    <h1 class="page-title"><i class="fas fa-chair" aria-hidden="true"></i> Control de Mesas</h1>
    <p>Bienvenido, <?= htmlspecialchars($_SESSION['usuario_nombre'], ENT_QUOTES, 'UTF-8') ?></p>
</div>

<div class="mesas-grid">
    <?php for ($i = 1; $i <= $totalMesas; $i++): ?>
        <?php 
            $mesaNumero = (string)$i;
            $estaOcupada = in_array($mesaNumero, $mesasOcupadas);
            $claseEstado = $estaOcupada ? 'mesa-ocupada' : 'mesa-libre';
            $textoEstado = $estaOcupada ? 'Ocupada' : 'Libre';
            
            // Enlace que redirige a la vista de detalle/productos de la mesa al hacer clic
            $enlaceMesa = BASE_URL . '/ventas/mesa/' . $mesaNumero;
            $iconoEstado = $estaOcupada ? 'fa-circle-xmark' : 'fa-circle-check';
        ?>
        
        <!-- Al hacer clic en esta tarjeta (<a>), el navegador abrirá la vista de la mesa -->
        <a href="<?= $enlaceMesa ?>" class="mesa-card <?= $claseEstado ?>">
            <div class="mesa-numero">Mesa <?= $mesaNumero ?></div>
            <div class="estado-badge">
                <i class="fas <?= $iconoEstado ?>" aria-hidden="true"></i>
                <?= $textoEstado ?>
            </div>
        </a>
    <?php endfor; ?>
</div>
