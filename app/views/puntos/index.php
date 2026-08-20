<?php
/**
 * app/views/puntos/index.php
 * Formulario de abono de puntos — fragmento dentro del layout 'dashboard'.
 */
?>

<div class="page-header puntos-header">
    <div class="puntos-header-titulo">
        <div class="puntos-icon"><i class="fa-solid fa-coins" aria-hidden="true"></i></div>
        <div>
            <h1 class="page-title">Sistema de Fidelización</h1>
            <p class="page-subtitle">Premia la preferencia de tus clientes acumulando puntos al instante.</p>
        </div>
    </div>
    <span class="puntos-operador-badge">
        <i class="fas fa-user-circle" aria-hidden="true"></i>
        Operador: <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Invitado', ENT_QUOTES, 'UTF-8') ?>
    </span>
</div>

<!-- Instrucciones para el camarero -->
<p class="puntos-instrucciones">
    <i class="fas fa-circle-info" aria-hidden="true"></i>
    Completa estos datos con la información que te dé el cliente al momento de pagar:
    su <strong>nombre completo</strong>, su <strong>número de cédula</strong> (sin puntos ni espacios)
    y la <strong>cantidad de puntos</strong> que le corresponde según su consumo.
</p>

<!-- Abonar puntos: tarjeta horizontal -->
<div class="form-card puntos-form-horizontal">
    <div class="section-header">
        <h2><i class="fa-solid fa-circle-plus" aria-hidden="true"></i> Abonar Puntos</h2>
    </div>

    <form action="<?= BASE_URL ?>/puntos/guardar" method="POST" class="puntos-form-grid">
        <div class="form-group">
            <label for="pNombre"><i class="fas fa-signature" aria-hidden="true"></i> Nombre y Apellido</label>
            <input type="text" id="pNombre" name="nombre" required
                   placeholder="Ej. Carlos Pérez">
        </div>

        <div class="form-group">
            <label for="pCedula"><i class="fas fa-id-card" aria-hidden="true"></i> Cédula del Cliente</label>
            <input type="text" id="pCedula" name="cedula" required
                   placeholder="Ej. 1098765432">
        </div>

        <div class="form-group">
            <label for="pPuntos"><i class="fas fa-star" aria-hidden="true"></i> Puntos a Sumar</label>
            <input type="number" id="pPuntos" name="puntos" required min="1" value="10"
                   class="puntos-input-destacado">
        </div>

        <div class="form-group puntos-form-btn-wrap">
            <button type="submit" class="btn-primary puntos-form-btn">
                <i class="fa-solid fa-circle-check" aria-hidden="true"></i> Guardar Puntos
            </button>
        </div>
    </form>
</div>

<!-- Acceso al historial -->
<a href="<?= BASE_URL ?>/puntos/listado" class="puntos-historial-card">
    <div class="puntos-historial-icono"><i class="fas fa-users-viewfinder" aria-hidden="true"></i></div>
    <div class="puntos-historial-texto">
        <h3>Historial de Clientes</h3>
        <p>Consulta todos los puntos acumulados</p>
    </div>
    <i class="fas fa-chevron-right puntos-historial-flecha" aria-hidden="true"></i>
</a>
