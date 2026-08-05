<?php /* app/views/empleados/dashboard_mesas.php */ ?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-chair" aria-hidden="true"></i> Control de Mesas</h1>
    <p>Bienvenido, <?= htmlspecialchars($_SESSION['usuario_nombre'], ENT_QUOTES, 'UTF-8') ?></p>
</div>

<!-- Estilos rápidos para la cuadrícula de mesas (puedes moverlos a tu CSS principal) -->
<style>
    .mesas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .mesa-card {
        background: rgba(255,255,255,0.2);
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        text-decoration: none;
        color: #333;
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 120px;
    }
    .mesa-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .mesa-numero {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .estado-badge {
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: bold;
    }
    
    /* Mesa Libre */
    .mesa-libre { border-color: #28a745; }
    .mesa-libre .estado-badge { background: #d4edda; color: #155724; }
    
    /* Mesa Ocupada */
    .mesa-ocupada { border-color: #dc3545; }
    .mesa-ocupada .estado-badge { background: #f8d7da; color: #721c24; }
</style>

<div class="mesas-grid">
    <?php for ($i = 1; $i <= $totalMesas; $i++): ?>
        <?php 
            $mesaNumero = (string)$i;
            $estaOcupada = in_array($mesaNumero, $mesasOcupadas);
            $claseEstado = $estaOcupada ? 'mesa-ocupada' : 'mesa-libre';
            $textoEstado = $estaOcupada ? 'Ocupada' : 'Libre';
            
            // Si está ocupada, podríamos redirigir a ver la cuenta, si está libre a abrir una nueva
            $enlaceMesa = BASE_URL . '/ventas/mesa/' . $mesaNumero;
        ?>
        
        <a href="<?= $enlaceMesa ?>" class="mesa-card <?= $claseEstado ?>">
            <div class="mesa-numero">Mesa <?= $mesaNumero ?></div>
            <div class="estado-badge"><?= $textoEstado ?></div>
        </a>
    <?php endfor; ?>
</div>