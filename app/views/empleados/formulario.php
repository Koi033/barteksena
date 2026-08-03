<?php /* app/views/empleados/formulario.php - Formulario crear/editar empleado */ ?>

<div class="page-header">
    <h1 class="page-title">
        <?= $empleado ? '<i class="fas fa-edit" aria-hidden="true"></i> Editar Empleado' : '<i class="fas fa-plus" aria-hidden="true"></i> Agregar Empleado' ?>
    </h1>
</div>

<div class="form-card">
    <form method="POST"
        action="<?= BASE_URL ?>/empleados/<?= $empleado ? 'actualizar' : 'guardar' ?>"
        novalidate>

        <!-- Token CSRF -->
        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">

        <!-- ID oculto en modo edición -->
        <?php if ($empleado): ?>
            <input type="hidden" name="id" value="<?= (int)$empleado['id'] ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="form-group">
                <label for="fNombre">Nombre Completo *</label>
                <input type="text" id="fNombre" name="nombre_completo"
                    maxlength="150" required
                    value="<?= htmlspecialchars($empleado['nombre_completo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="fPuesto">Puesto *</label>
                <select id="puesto" name="puesto" required class="form-control">
                    <option value="" disabled selected>Selecciona un puesto</option>
                    <option value="mesero">Mesero / Bartender</option>
                    <option value="cajero">Cajero</option>

                </select>
            </div>

            
                <div class="form-group">
                    <label for="fEmail">Correo Electrónico *</label>
                    <input type="email" id="fEmail" name="email"
                        maxlength="150" required
                        value="<?= htmlspecialchars($empleado['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="form-group">
                    <label for="fTel">Teléfono</label>
                    <input type="tel" id="fTel" name="telefono"
                        maxlength="20"
                        value="<?= htmlspecialchars($empleado['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <!-- ── CAMPOS DE ACCESO (SOLO CREACIÓN) ── -->
                <?php if (!$empleado): ?>
                    <div class="form-group" style="grid-column: 1 / -1; margin-top: 15px; border-top: 1px solid #eaeaea; padding-top: 15px;">
                        <h4 style="margin: 0 0 5px 0; color: #333;">
                            <i class="fas fa-key" aria-hidden="true"></i> Credenciales de Acceso
                        </h4>
                        <small style="color: #666;">Se creará automáticamente una cuenta con rol de "empleado".</small>
                    </div>

                    <div class="form-group">
                        <label for="fUsuario">Nombre de Usuario *</label>
                        <input type="text" id="fUsuario" name="usuario"
                            maxlength="60" required autocomplete="off"
                            placeholder="Ej: jperez">
                    </div>

                    <div class="form-group">
                        <label for="fContrasena">Contraseña Temporal *</label>
                        <input type="password" id="fContrasena" name="contrasena"
                            required minlength="8" autocomplete="new-password"
                            placeholder="Mínimo 8 caracteres">
                    </div>
                <?php endif; ?>
                <!-- ──────────────────────────────────────── -->
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <?= $empleado ? '<i class="fas fa-save" aria-hidden="true"></i> Guardar Cambios' : '<i class="fas fa-plus" aria-hidden="true"></i> Crear Empleado' ?>
                </button>
                <a href="<?= BASE_URL ?>/empleados" class="btn-secondary"><i class="fas fa-times" aria-hidden="true"></i> Cancelar</a>
            </div>
    </form>
</div>