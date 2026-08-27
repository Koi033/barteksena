<?php
/**
 * Vista genérica de error
 * Variables esperadas: $codigo, $titulo, $mensaje, $imagen
 */
?>
<main class="form-section">
<section class="error-page">
    <div class="error-content">
        <span class="error-badge">Gestiona tu bar</span>
        <h1 class="error-code"><?= htmlspecialchars($codigo, ENT_QUOTES) ?></h1>
        <h2 class="error-titulo"><?= htmlspecialchars($titulo, ENT_QUOTES) ?></h2>
        <p class="error-mensaje"><?= htmlspecialchars($mensaje, ENT_QUOTES) ?></p>

        <div class="error-acciones">
            <a href="/bartek" class="btn-error btn-error-primary">Volver a inicio</a>
            <a href="/bartek/contacto" class="btn-error btn-error-secondary">Contactar soporte</a>
        </div>
    </div>

    <div class="error-ilustracion">
        <img src="<?= BASE_URL ?>/public/images/errores/error-<?= (int) $codigo ?>.png"
             alt="Ilustración error <?= htmlspecialchars($codigo, ENT_QUOTES) ?>"
             onerror="this.src='<?= BASE_URL ?>/public/images/errores/error-generico.png'">
    </div>
</section>
</main>