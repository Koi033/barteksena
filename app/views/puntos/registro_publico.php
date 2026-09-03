<?php /* app/views/puntos/registro_publico.php - Registro público al club de fidelización, sin necesidad de iniciar sesión */ ?>
<section class="form-section">
    <div class="login-card">
        <div class="login-image">
            <img src="<?= BASE_URL ?>/public/images/login_side.jpg" alt="Bartek">
        </div>

        <div class="form-content">
            <h1><i class="fa-solid fa-coins" aria-hidden="true"></i> Club de Fidelización</h1>
            <p class="recuperar-subtitle">
                Regístrate en segundos y empieza a acumular puntos con cada consumo.<br>
                No necesitas cuenta ni contraseña: solo tu <strong class="highlight">nombre</strong> y tu <strong class="highlight">cédula</strong>.
            </p>

            <form id="formRegistroPuntos" method="POST" action="<?= BASE_URL ?>/puntos/registro" novalidate>
                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">

                <div class="campo-grupo">
                    <label for="rpNombre">Nombre y Apellido</label>
                    <input type="text" id="rpNombre" name="nombre"
                           placeholder="Ej. Carlos Pérez" maxlength="80"
                           value="<?= htmlspecialchars($old['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div class="campo-grupo">
                    <label for="rpCedula">Número de Cédula</label>
                    <input type="text" id="rpCedula" name="cedula" inputmode="numeric"
                           placeholder="Sin puntos ni espacios" maxlength="10"
                           value="<?= htmlspecialchars($old['cedula'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div class="campo-grupo campo-checkbox">
                    <label class="checkbox-label" for="rpAceptaPolitica">
                        <input type="checkbox" id="rpAceptaPolitica" name="acepta_politica" value="1"
                               <?= !empty($old['acepta_politica']) ? 'checked' : '' ?> required>
                        <span>
                            He leído y acepto la
                            <a href="<?= BASE_URL ?>/puntos/politica-datos" target="_blank" rel="noopener">
                                Política de Tratamiento de Datos Personales
                            </a>
                            del Club de Fidelización.
                        </span>
                    </label>
                </div>

                <button type="submit" class="btn-form">
                    <i class="fa-solid fa-star" aria-hidden="true"></i> Unirme al Club
                </button>
            </form>

            <div class="login-footer">
                <p><a href="<?= BASE_URL ?>/"><i class="fas fa-arrow-left" aria-hidden="true"></i> Volver al inicio</a></p>
            </div>
        </div>
    </div>
</section>

<style>
    .campo-checkbox {
        margin: 4px 0 18px;
    }
    .checkbox-label {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        cursor: pointer;
        font-size: 0.88rem;
        line-height: 1.45;
    }
    .checkbox-label input[type="checkbox"] {
        margin-top: 3px;
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        accent-color: #E67E22;
        cursor: pointer;
    }
    .checkbox-label a {
        color: #E67E22;
        font-weight: 600;
        text-decoration: underline;
    }
    .checkbox-label a:hover {
        color: #d35400;
    }
</style>
