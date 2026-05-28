<?php /* app/views/auth/login.php - Formulario de inicio de sesión */ ?>
<section class="form-section">
    <div class="login-card">
        <div class="login-image">
            <img src="<?= BASE_URL ?>/public/images/login_side.jpg" alt="Bartek - Bar Management">
        </div>

        <div class="form-content">
            <h1>Inicia Sesión</h1>

            <form method="POST" action="<?= BASE_URL ?>/login" novalidate>
                <!-- Token CSRF oculto: previene ataques de falsificación de solicitudes -->
                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">

                <div class="campo-grupo">
                    <label for="loginUsuario">Usuario</label>
                    <input type="text"
                           id="loginUsuario"
                           name="usuario"
                           placeholder="Ingresa tu usuario"
                           autocomplete="username"
                           maxlength="60"
                           required>
                </div>

                <div class="campo-grupo">
                    <label for="loginPass">Contraseña</label>
                    <input type="password"
                           id="loginPass"
                           name="contrasena"
                           placeholder="Ingresa tu contraseña"
                           autocomplete="current-password"
                           maxlength="128"
                           required>
                </div>

                <button type="submit" class="btn-form">Ingresar</button>
            </form>

            <div class="login-footer">
                <p>¿No tienes cuenta? <a href="<?= BASE_URL ?>/registro">Regístrate aquí</a></p>
                <p><a href="<?= BASE_URL ?>/"><i class="fas fa-arrow-left" aria-hidden="true"></i> Volver al inicio</a></p>
                <a href="<?= BASE_URL ?>/recuperar">¿Olvidaste tu contraseña?</a>
            </div>
        </div>
    </div>
</section>
