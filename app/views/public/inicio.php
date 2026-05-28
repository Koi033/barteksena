<?php /* app/views/public/inicio.php - Landing page pública de Bartek */ ?>
<section class="catalogo">
    <div class="contenedor-mixto">
        <div class="info-texto">
            <h2>Bartek</h2>
            <p>La plataforma de gestión inteligente para bares modernos.</p>
            <a href="<?= BASE_URL ?>/registro" class="btn-info">Empezar Gratis</a>
        </div>

        <div class="carrusel-ventana">
            <div class="carrusel-track" id="track">
                <img src="<?= BASE_URL ?>/public/images/stock1.jpg" alt="Bar 1">
                <img src="<?= BASE_URL ?>/public/images/stock2.jpg" alt="Bar 2">
                <img src="<?= BASE_URL ?>/public/images/stock3.jpg" alt="Bar 3">
            </div>
            <button class="flecha anterior" onclick="moverIzquierda()" aria-label="Anterior">&#10094;</button>
            <button class="flecha siguiente" onclick="moverDerecha()"  aria-label="Siguiente">&#10095;</button>
        </div>
    </div>
</section>
