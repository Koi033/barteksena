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
        <h2 id="tituloFormRecompensa"><i class="fa-solid fa-gift" aria-hidden="true"></i> Nueva Recompensa</h2>
    </div>

    <p class="puntos-instrucciones-texto" style="margin-bottom: 1rem;">
        <i class="fas fa-circle-info" aria-hidden="true"></i>
        Las recompensas solo pueden ser de dos tipos: un <strong>descuento porcentual</strong> sobre una
        categoría del menú, o un <strong>producto gratis</strong>. Al elegir el tipo, la descripción se
        genera automáticamente y el descuento se aplicará solo cuando el mesero la canjee desde la mesa.
    </p>

    <!-- Un solo formulario sirve tanto para crear como para editar; el botón
         "Editar" de cada fila rellena estos campos vía JS (ver script abajo). -->
    <form id="formRecompensa" method="POST" action="<?= BASE_URL ?>/configuracion-puntos/recompensa/guardar" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="id" id="rId" value="0">

        <div class="form-grid">
            <div class="form-group">
                <label for="rNombre">Nombre de la Recompensa *</label>
                <input type="text" id="rNombre" name="nombre" maxlength="100" required
                       placeholder="Ej. Cerveza gratis">
            </div>

            <div class="form-group">
                <label for="rTipo">Tipo de Recompensa *</label>
                <select id="rTipo" name="tipo" required>
                    <option value="descuento_categoria">Descuento sobre una categoría</option>
                    <option value="producto_gratis">Producto gratis</option>
                </select>
            </div>

            <div class="form-group" id="grupoCategoria">
                <label for="rCategoria">Categoría *</label>
                <select id="rCategoria" name="categoria_id">
                    <option value="">— Selecciona —</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>">
                            <?= htmlspecialchars($cat['nombre'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" id="grupoPorcentaje">
                <label for="rPorcentaje">Porcentaje de Descuento (%) *</label>
                <input type="number" id="rPorcentaje" name="porcentaje_descuento" min="1" max="100" step="0.01"
                       placeholder="Ej. 20">
            </div>

            <div class="form-group" id="grupoProducto" style="display:none;">
                <label for="rProducto">Producto a Regalar *</label>
                <select id="rProducto" name="producto_id">
                    <option value="">— Selecciona —</option>
                    <?php foreach ($productos as $p): ?>
                        <option value="<?= (int)$p['id'] ?>">
                            <?= htmlspecialchars($p['nombre'], ENT_QUOTES, 'UTF-8') ?>
                            ($<?= number_format((float)$p['precio_unitario'], 2) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="rPuntos">Puntos Requeridos *</label>
                <input type="number" id="rPuntos" name="puntos_requeridos" min="1" required>
            </div>

            <div class="form-group" style="display:flex; align-items:flex-end; gap:.5rem;">
                <label style="display:flex; align-items:center; gap:.4rem; margin:0;">
                    <input type="checkbox" name="activo" id="rActivo" value="1" checked style="width:auto;">
                    Activa
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary" id="btnGuardarRecompensa">
                <i class="fas fa-plus" aria-hidden="true"></i> Agregar Recompensa
            </button>
            <button type="button" class="btn-secondary" id="btnCancelarEdicion" style="display:none;">
                <i class="fas fa-times" aria-hidden="true"></i> Cancelar edición
            </button>
        </div>
    </form>

    <hr>

    <!-- Listado de recompensas existentes (solo lectura + acciones) -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Detalle</th>
                    <th>Puntos</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recompensas)): ?>
                    <?php foreach ($recompensas as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <?php if ($r['tipo'] === 'descuento_categoria'): ?>
                                    <span class="category-badge"><i class="fas fa-percent" aria-hidden="true"></i> Descuento</span>
                                <?php else: ?>
                                    <span class="category-badge"><i class="fas fa-gift" aria-hidden="true"></i> Producto gratis</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($r['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= (int)$r['puntos_requeridos'] ?></td>
                            <td>
                                <?php if ((int)$r['activo'] === 1): ?>
                                    <span class="status-badge status-completed">Activa</span>
                                <?php else: ?>
                                    <span class="status-badge status-cancelled">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td style="white-space:nowrap;">
                                <button type="button" class="btn btn-primary btn-sm btn-editar-recompensa"
                                        data-id="<?= (int)$r['id'] ?>"
                                        data-nombre="<?= htmlspecialchars($r['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-tipo="<?= htmlspecialchars($r['tipo'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-categoria="<?= (int)($r['categoria_id'] ?? 0) ?>"
                                        data-porcentaje="<?= htmlspecialchars((string)($r['porcentaje_descuento'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                        data-producto="<?= (int)($r['producto_id'] ?? 0) ?>"
                                        data-puntos="<?= (int)$r['puntos_requeridos'] ?>"
                                        data-activo="<?= (int)$r['activo'] ?>"
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form method="POST" action="<?= BASE_URL ?>/configuracion-puntos/recompensa/eliminar"
                                      style="display:inline"
                                      onsubmit="return confirm('¿Eliminar esta recompensa?');">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Aún no has creado ninguna recompensa.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
(function () {
    const tipoSelect      = document.getElementById('rTipo');
    const grupoCategoria  = document.getElementById('grupoCategoria');
    const grupoPorcentaje = document.getElementById('grupoPorcentaje');
    const grupoProducto   = document.getElementById('grupoProducto');
    const form            = document.getElementById('formRecompensa');
    const btnCancelar     = document.getElementById('btnCancelarEdicion');
    const btnGuardar      = document.getElementById('btnGuardarRecompensa');
    const titulo          = document.getElementById('tituloFormRecompensa');

    const inputCategoria  = document.getElementById('rCategoria');
    const inputPorcentaje = document.getElementById('rPorcentaje');
    const inputProducto   = document.getElementById('rProducto');

    /** Muestra solo los campos relevantes según el tipo seleccionado. */
    function actualizarCamposPorTipo() {
        const esDescuento = tipoSelect.value === 'descuento_categoria';

        grupoCategoria.style.display  = esDescuento ? '' : 'none';
        grupoPorcentaje.style.display = esDescuento ? '' : 'none';
        grupoProducto.style.display   = esDescuento ? 'none' : '';

        inputCategoria.required  = esDescuento;
        inputPorcentaje.required = esDescuento;
        inputProducto.required   = !esDescuento;
    }

    tipoSelect.addEventListener('change', actualizarCamposPorTipo);
    actualizarCamposPorTipo();

    /** Vuelve el formulario a su estado de "crear nueva recompensa". */
    function resetFormulario() {
        form.reset();
        document.getElementById('rId').value = '0';
        actualizarCamposPorTipo();
        titulo.innerHTML = '<i class="fa-solid fa-gift" aria-hidden="true"></i> Nueva Recompensa';
        btnGuardar.innerHTML = '<i class="fas fa-plus" aria-hidden="true"></i> Agregar Recompensa';
        btnCancelar.style.display = 'none';
    }

    // Al pulsar "Editar" en una fila, precarga sus datos en el formulario
    // de arriba en lugar de tener un formulario independiente por fila.
    document.querySelectorAll('.btn-editar-recompensa').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('rId').value = btn.dataset.id;
            document.getElementById('rNombre').value = btn.dataset.nombre;
            tipoSelect.value = btn.dataset.tipo;
            inputCategoria.value = btn.dataset.categoria && btn.dataset.categoria !== '0' ? btn.dataset.categoria : '';
            inputPorcentaje.value = btn.dataset.porcentaje || '';
            inputProducto.value = btn.dataset.producto && btn.dataset.producto !== '0' ? btn.dataset.producto : '';
            document.getElementById('rPuntos').value = btn.dataset.puntos;
            document.getElementById('rActivo').checked = btn.dataset.activo === '1';

            actualizarCamposPorTipo();
            titulo.innerHTML = '<i class="fa-solid fa-gift" aria-hidden="true"></i> Editar Recompensa';
            btnGuardar.innerHTML = '<i class="fas fa-save" aria-hidden="true"></i> Guardar Cambios';
            btnCancelar.style.display = '';
            form.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    });

    btnCancelar.addEventListener('click', resetFormulario);

    // Ajusta la etiqueta del campo "valor_acumulacion" según el modo elegido
    const fModo = document.getElementById('fModo');
    if (fModo) {
        fModo.addEventListener('change', function () {
            const label = document.getElementById('labelValorAcumulacion');
            label.textContent = this.value === 'productos'
                ? 'Cantidad de productos necesaria *'
                : 'Monto en pesos necesario ($) *';
        });
        fModo.dispatchEvent(new Event('change'));
    }
})();
</script>
