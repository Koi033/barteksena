<?php /* app/views/configuracion_puntos/index.php - Configuración del sistema de fidelización */ ?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-sliders-h" aria-hidden="true"></i> Configuración de Puntos</h1>
    <p class="page-subtitle">Define cómo acumulan puntos tus clientes y qué recompensas pueden canjear.</p>
</div>

<!-- ============================================================ -->
<!-- Regla de acumulación de puntos                                -->
<!-- ============================================================ -->
<div class="form-card">
    <div class="section-header">
        <h2><i class="fa-solid fa-coins" aria-hidden="true"></i> Regla de Acumulación</h2>
    </div>

    <form method="POST" action="<?= BASE_URL ?>/configuracion-puntos/guardar" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">

        <div class="form-grid">
            <div class="form-group">
                <label for="fModo">¿Cómo se acumulan los puntos? *</label>
                <select id="fModo" name="modo_acumulacion" required>
                    <option value="dinero" <?= $configuracion['modo_acumulacion'] === 'dinero' ? 'selected' : '' ?>>
                        Por dinero gastado
                    </option>
                    <option value="productos" <?= $configuracion['modo_acumulacion'] === 'productos' ? 'selected' : '' ?>>
                        Por cantidad de productos comprados
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="fValor" id="labelValorAcumulacion">
                    Monto en pesos necesario ($) *
                </label>
                <input type="number" id="fValor" name="valor_acumulacion"
                       min="1" step="0.01" required
                       value="<?= htmlspecialchars((string)$configuracion['valor_acumulacion'], ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="fOtorgados">Puntos que se otorgan al cumplir la regla *</label>
                <input type="number" id="fOtorgados" name="puntos_otorgados"
                       min="1" required
                       value="<?= (int)$configuracion['puntos_otorgados'] ?>">
            </div>

            <div class="form-group">
                <label for="fBienvenida">Puntos de bienvenida (registro público)</label>
                <input type="number" id="fBienvenida" name="puntos_bienvenida"
                       min="0"
                       value="<?= (int)$configuracion['puntos_bienvenida'] ?>">
            </div>
        </div>

        <p class="puntos-instrucciones-texto" style="margin-top: -0.5rem; margin-bottom: 1rem;">
            <i class="fas fa-circle-info" aria-hidden="true"></i>
            Ejemplo: si eliges <strong>"Por dinero gastado"</strong> con un monto de <strong>$10.000</strong> y
            <strong>1</strong> punto otorgado, el cliente ganará 1 punto por cada $10.000 consumidos.
            Si eliges <strong>"Por cantidad de productos"</strong> con un valor de <strong>3</strong> y
            <strong>1</strong> punto otorgado, ganará 1 punto por cada 3 productos comprados.
        </p>

        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save" aria-hidden="true"></i> Guardar Configuración
            </button>
        </div>
    </form>
</div>

<!-- ============================================================ -->
<!-- Catálogo de recompensas                                       -->
<!-- ============================================================ -->
<div class="form-card">
    <div class="section-header">
        <h2><i class="fa-solid fa-gift" aria-hidden="true"></i> Recompensas por Puntos</h2>
    </div>

    <!-- Formulario para agregar una nueva recompensa -->
    <form method="POST" action="<?= BASE_URL ?>/configuracion-puntos/recompensa/guardar" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="id" value="0">

        <div class="form-grid">
            <div class="form-group">
                <label for="rNombre">Nombre de la Recompensa *</label>
                <input type="text" id="rNombre" name="nombre" maxlength="100" required
                       placeholder="Ej. Cerveza gratis">
            </div>

            <div class="form-group">
                <label for="rDescripcion">Descripción</label>
                <input type="text" id="rDescripcion" name="descripcion" maxlength="255"
                       placeholder="Ej. Aplica para cualquier cerveza nacional">
            </div>

            <div class="form-group">
                <label for="rPuntos">Puntos Requeridos *</label>
                <input type="number" id="rPuntos" name="puntos_requeridos" min="1" required>
            </div>

            <div class="form-group" style="display:flex; align-items:flex-end; gap:.5rem;">
                <label style="display:flex; align-items:center; gap:.4rem; margin:0;">
                    <input type="checkbox" name="activo" value="1" checked style="width:auto;">
                    Activa
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i class="fas fa-plus" aria-hidden="true"></i> Agregar Recompensa
            </button>
        </div>
    </form>

    <hr>

    <!-- Listado de recompensas existentes -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Puntos</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recompensas)): ?>
                    <?php foreach ($recompensas as $r): ?>
                        <?php $formId = 'recompensa-form-' . (int)$r['id']; ?>
                        <tr>
                            <td>
                                <!-- Formulario "flotante": sus campos viven en las celdas de esta
                                     fila gracias al atributo form="<?= $formId ?>" de HTML5, así
                                     que no necesitamos anidar <form> dentro de <tr>/<td>. -->
                                <form id="<?= $formId ?>" method="POST"
                                      action="<?= BASE_URL ?>/configuracion-puntos/recompensa/guardar"
                                      style="display:contents;">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                </form>
                                <input type="text" name="nombre" maxlength="100" required form="<?= $formId ?>"
                                       value="<?= htmlspecialchars($r['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                            </td>
                            <td>
                                <input type="text" name="descripcion" maxlength="255" form="<?= $formId ?>"
                                       value="<?= htmlspecialchars($r['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            </td>
                            <td>
                                <input type="number" name="puntos_requeridos" min="1" required form="<?= $formId ?>"
                                       style="width:90px;"
                                       value="<?= (int)$r['puntos_requeridos'] ?>">
                            </td>
                            <td>
                                <label style="display:flex; align-items:center; gap:.4rem;">
                                    <input type="checkbox" name="activo" value="1" style="width:auto;" form="<?= $formId ?>"
                                           <?= (int)$r['activo'] === 1 ? 'checked' : '' ?>>
                                    Activa
                                </label>
                            </td>
                            <td style="white-space:nowrap;">
                                <button type="submit" form="<?= $formId ?>" class="btn btn-success btn-sm" title="Guardar cambios">
                                    <i class="fas fa-save"></i>
                                </button>
                                <button type="submit" form="<?= $formId ?>" class="btn btn-danger btn-sm" title="Eliminar"
                                        formaction="<?= BASE_URL ?>/configuracion-puntos/recompensa/eliminar"
                                        formnovalidate
                                        onclick="return confirm('¿Eliminar esta recompensa?');">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Aún no has creado ninguna recompensa.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Ajusta la etiqueta del campo "valor_acumulacion" según el modo elegido
    document.getElementById('fModo').addEventListener('change', function () {
        const label = document.getElementById('labelValorAcumulacion');
        if (this.value === 'productos') {
            label.textContent = 'Cantidad de productos necesaria *';
        } else {
            label.textContent = 'Monto en pesos necesario ($) *';
        }
    });
    document.getElementById('fModo').dispatchEvent(new Event('change'));
</script>
