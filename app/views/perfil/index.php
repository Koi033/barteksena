<?php /* app/views/perfil/index.php — Formulario de perfil del usuario autenticado */ ?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-user-circle" aria-hidden="true"></i> Mi Perfil</h1>
    <p class="page-subtitle">Actualiza tus datos personales y contraseña.</p>
</div>

<div class="form-card">
    <form method="POST" action="<?= BASE_URL ?>/perfil/actualizar" novalidate>

        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">

        <h2 style="color:#E67E22; font-size:1rem; margin-bottom:1.2rem;">
            Datos Generales
        </h2>

        <div class="form-grid">
            <div class="form-group">
                <label for="pNombre">Nombre Completo *</label>
                <input type="text" id="pNombre" name="nombre" maxlength="100" required
                       value="<?= htmlspecialchars($usuario['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="pEmail">Correo Electrónico *</label>
                <input type="email" id="pEmail" name="email" maxlength="150" required
                       value="<?= htmlspecialchars($usuario['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="pTel">Teléfono</label>
                <input type="tel" id="pTel" name="telefono" maxlength="20"
                       value="<?= htmlspecialchars($usuario['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label>Usuario</label>
                <!-- Solo lectura: el usuario no cambia -->
                <input type="text" value="<?= htmlspecialchars($usuario['usuario'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       readonly style="opacity:0.6; cursor:not-allowed;">
            </div>

            <div class="form-group">
                <label>Rol</label>
                <input type="text" value="<?= htmlspecialchars($usuario['rol'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       readonly style="opacity:0.6; cursor:not-allowed; text-transform:capitalize;">
            </div>
        </div>

        <hr style="border-color:#333; margin:1.5rem 0;">

        <h2 style="color:#E67E22; font-size:1rem; margin-bottom:1.2rem;">
            Cambiar Contraseña <span style="color:#888; font-size:0.8rem;">(opcional)</span>
        </h2>

        <div class="form-grid">
            <div class="form-group">
                <label for="pPassAct">Contraseña Actual</label>
                <input type="password" id="pPassAct" name="pass_actual"
                       autocomplete="current-password" maxlength="128"
                       placeholder="Requerida para cambiar la contraseña">
            </div>

            <div class="form-group">
                <label for="pPassNew">Nueva Contraseña</label>
                <input type="password" id="pPassNew" name="pass_nueva"
                       autocomplete="new-password" minlength="8" maxlength="128"
                       placeholder="Mínimo 8 caracteres">
            </div>

            <div class="form-group">
                <label for="pPassConf">Confirmar Nueva Contraseña</label>
                <input type="password" id="pPassConf" name="pass_confirmar"
                       autocomplete="new-password" minlength="8" maxlength="128"
                       placeholder="Repite la nueva contraseña">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary"><i class="fas fa-save" aria-hidden="true"></i> Guardar Cambios</button>
            <a href="<?= BASE_URL ?>/dashboard" class="btn-secondary"><i class="fas fa-arrow-left" aria-hidden="true"></i> Volver al Panel</a>
        </div>
    </form>
</div>
