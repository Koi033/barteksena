<?php /* app/views/auth/recuperar_password.php - Formulario para solicitar restablecimiento */ ?>

<section class="form-section">
    <div class="login-card">
        <div class="form-content">
            <div class="recuperar-card">
                <div class="recuperar-icon">🔑</div>

                <h1>¿Olvidaste tu contraseña?</h1>
                <p class="recuperar-subtitle">
                    Ingresa tu correo y te enviaremos un enlace para restablecerla.<br>
                    El enlace expira en <strong class="highlight">5 minutos</strong>.
                </p>

                <form method="POST" action="<?= BASE_URL ?>/recuperar" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="campo-grupo">
                        <label for="email">Correo electrónico</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="tucorreo@ejemplo.com"
                            autocomplete="email"
                            required
                        >
                    </div>

                    <button type="submit" class="btn-form">Enviar instrucciones</button>
                </form>

                <a href="<?= BASE_URL ?>/login" class="back-link"><i class="fas fa-arrow-left" aria-hidden="true"></i> Volver al <span>inicio de sesión</span></a>
            </div>
        </div>
    </div>
</section>
