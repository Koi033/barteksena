<?php /* app/views/public/inicio.php - Landing page pública de Bartek */ ?>
<section class="catalogo">
    <div class="contenedor-mixto">
        <div class="info-texto">
            <span class="hero-label">Gestiona tu bar </span>
            <h2>Bartek</h2>
            <p>La plataforma de gestión inteligente para bares modernos. Controla inventario, horarios y ventas desde un solo panel.</p>

            <div class="hero-actions">
                <a href="<?= BASE_URL ?>/registro" class="btn-info">Empezar Gratis</a>
                <a href="<?= BASE_URL ?>/contacto" class="btn-form btn-secondary">Contáctanos</a>
            </div>

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

    <div class="beneficios">
    <!-- Tarjeta de Reservas sin el # -->
    <a class="beneficio-item" href="javascript:void(0);" aria-label="Reservas">
        <h3>Reservas</h3>
        <p>Ahora puedes reservar con nuestro chat bot de la esquina inferior derecha.</p>
    </a>
    
    <!-- Tu tarjeta de Menú digital que ya abre la carta perfecta -->
    <a class="beneficio-item" href="<?= BASE_URL ?>/public/publico.php" target="_blank" aria-label="Menú digital">
        <h3>Menú digital</h3>
        <p>Publica y actualiza tu carta con precios, descripciones y ofertas especiales desde un solo lugar.</p>
    </a>

    <!-- Tarjeta de Agenda de personal sin el # -->
    <a class="beneficio-item" href="javascript:void(0);" aria-label="Agenda de personal">
        <h3>Agenda de personal</h3>
        <p>Organiza turnos, horarios y asistencia del equipo desde una sola plataforma.</p>
    </a>
</div>

  
    <script src="https://cdn.botpress.cloud/webchat/v3.7/inject.js"></script>
<script src="https://files.bpcontent.cloud/2026/05/14/18/20260514181755-US80LTJD.js" defer></script>
    
</section>
