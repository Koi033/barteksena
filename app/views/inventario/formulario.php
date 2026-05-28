<?php /* app/views/inventario/formulario.php - Formulario crear/editar bebida */ ?>

<div class="page-header">
    <h1 class="page-title">
        <?= $bebida ? '<i class="fas fa-edit" aria-hidden="true"></i> Editar Bebida' : '<i class="fas fa-plus" aria-hidden="true"></i> Agregar Bebida' ?>
    </h1>
</div>

<div class="form-card">
    <form method="POST"
          action="<?= BASE_URL ?>/inventario/<?= $bebida ? 'actualizar' : 'guardar' ?>"
          novalidate>

        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">
        <?php if ($bebida): ?>
            <input type="hidden" name="id" value="<?= (int)$bebida['id'] ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="form-group">
                <label for="fCat">Categoría *</label>
                <select id="fCat" name="categoria_id" required>
                    <option value="">— Selecciona —</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>"
                            <?= isset($bebida) && (int)$bebida['categoria_id'] === (int)$cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nombre'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (!$bebida): ?>
            <div class="form-group">
                <label for="fCodigo">Código *</label>
                <input type="text" id="fCodigo" name="codigo"
                       maxlength="20" required
                       placeholder="Ej: BEB006">
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="fNombre">Nombre Bebida *</label>
                <input type="text" id="fNombre" name="nombre"
                       maxlength="120" required
                       value="<?= htmlspecialchars($bebida['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="fStock">Stock Actual</label>
                <input type="number" id="fStock" name="stock_actual"
                       min="0" max="99999" value="<?= (int)($bebida['stock_actual'] ?? 0) ?>">
            </div>

            <div class="form-group">
                <label for="fMinimo">Stock Mínimo (alerta)</label>
                <input type="number" id="fMinimo" name="stock_minimo"
                       min="1" max="9999" value="<?= (int)($bebida['stock_minimo'] ?? 5) ?>">
            </div>

            <div class="form-group">
                <label for="fPrecio">Precio Unitario ($) *</label>
                <input type="number" id="fPrecio" name="precio_unitario"
                       min="0" step="0.01" required
                       value="<?= number_format((float)($bebida['precio_unitario'] ?? 0), 2) ?>">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <?= $bebida ? '<i class="fas fa-save" aria-hidden="true"></i> Guardar Cambios' : '<i class="fas fa-plus" aria-hidden="true"></i> Agregar Bebida' ?>
            </button>
            <a href="<?= BASE_URL ?>/inventario" class="btn-secondary"><i class="fas fa-times" aria-hidden="true"></i> Cancelar</a>
        </div>
    </form>
</div>
