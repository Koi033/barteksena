<?php /* app/views/auth/registro.php - Formulario de registro de usuario */ ?>
<section class="form-section">
    <div class="login-card">
        <div class="login-image">
            <img src="<?= BASE_URL ?>/public/images/login_side.jpg" alt="Bartek">
        </div>

        <div class="form-content">
            <h1>Crear Cuenta</h1>

            <form id="formRegistro" method="POST" action="<?= BASE_URL ?>/registro" novalidate>
                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">

                <div class="campo-grupo">
                    <label for="rNombre">Nombre</label>
                    <input type="text" id="rNombre" name="nombre"
                           placeholder="Juan" maxlength="40" required>
                </div>
                <div class="campo-grupo">
                    <label for="rApellido">Apellido</label>
                    <input type="text" id="rApellido" name="apellido"
                           placeholder="Martinez" maxlength="40" required>
                </div>

                <div class="campo-grupo">
                    <label for="rEmail">Correo electrónico</label>
                    <input type="email" id="rEmail" name="email"
                           placeholder="tu@correo.com" maxlength="150" required>
                </div>

                <div class="campo-grupo">
                    <label for="rTelefono">Teléfono</label>
                    <input type="tel" id="rTelefono" name="telefono"
                           placeholder="Teléfono de contacto" maxlength="20">
                </div>

                <div class="campo-grupo">
                    <label for="rUsuario">Nombre de usuario</label>
                    <input type="text" id="rUsuario" name="usuario"
                           placeholder="Crea tu usuario" maxlength="60" required>
                </div>

                <div class="campo-grupo">
                    <label for="rPass">Contraseña</label>
                    <input type="password" id="rPass" name="contrasena"
                           placeholder="Mínimo 8 caracteres" minlength="8"
                           autocomplete="new-password" required>
                </div>

                <div class="campo-grupo">
                    <label for="rPassConf">Confirmar contraseña</label>
                    <input type="password" id="rPassConf" name="contrasena_confirm"
                           placeholder="Repite tu contraseña" minlength="8"
                           autocomplete="new-password" required>
                </div>

            <!--
                <div class="campo-grupo">
                    <label for="rRol">Rol</label>
                    <select id="rRol" name="rol" required>
                        <option value="">Selecciona tu rol</option>
                        <option value="empleado">Empleado</option>
                        <option value="dueno">Dueño</option>
                    </select>
                </div>
-->

                <button type="submit" class="btn-form">Registrarse</button>
            </form>

            <div class="login-footer">
                <p>¿Ya tienes cuenta? <a href="<?= BASE_URL ?>/login">Inicia sesión</a></p>
                <p><a href="<?= BASE_URL ?>/"><i class="fas fa-arrow-left" aria-hidden="true"></i> Volver al inicio</a></p>
            </div>
        </div>
    </div>
</section>
