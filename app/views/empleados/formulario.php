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
                <input type="text" id="fPuesto" name="puesto"
                       maxlength="80" required
                       value="<?= htmlspecialchars($empleado['puesto'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="fDepto">Departamento *</label>
                <input type="text" id="fDepto" name="departamento"
                       maxlength="80" required
                       value="<?= htmlspecialchars($empleado['departamento'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
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
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <?= $empleado ? '<i class="fas fa-save" aria-hidden="true"></i> Guardar Cambios' : '<i class="fas fa-plus" aria-hidden="true"></i> Crear Empleado' ?>
            </button>
            <a href="<?= BASE_URL ?>/empleados" class="btn-secondary"><i class="fas fa-times" aria-hidden="true"></i> Cancelar</a>
        </div>
    </form>
</div>
