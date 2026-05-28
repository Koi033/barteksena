<?php /* app/views/public/contacto.php - Formulario de contacto público */ ?>
<main class="form-section">
    <div class="login-card">
        <div class="login-image">
            <img src="<?= BASE_URL ?>/public/images/login_side.jpg" alt="Contacto Bartek">
        </div>

        <div class="form-content">
            <h1>Contáctanos</h1>

            <form method="POST" action="<?= BASE_URL ?>/contacto" novalidate>
                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">

                <div class="campo-grupo">
                    <label for="cNombre">Nombre</label>
                    <input type="text" id="cNombre" name="nombre"
                           placeholder="Tu nombre completo" maxlength="100" required>
                </div>

                <div class="campo-grupo">
                    <label for="cCorreo">Correo Electrónico</label>
                    <input type="email" id="cCorreo" name="correo"
                           placeholder="tu@correo.com" maxlength="150" required>
                </div>

                <div class="campo-grupo">
                    <label for="cMensaje">Mensaje</label>
                    <textarea id="cMensaje" name="mensaje" rows="5"
                              placeholder="¿En qué podemos ayudarte?" maxlength="2000" required></textarea>
                </div>

                <button type="submit" class="btn-form">📨 Enviar Mensaje</button>
            </form>

            <div class="login-footer">
                <p><a href="<?= BASE_URL ?>/"><i class="fas fa-arrow-left" aria-hidden="true"></i> Volver al inicio</a></p>
            </div>
        </div>
    </div>
</main>
