<?php /* app/views/mesas/mesa_detalle.php */ ?>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/mesa-detalle.css">

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-cash-register" aria-hidden="true"></i> Mesa <?= htmlspecialchars($numeroMesa, ENT_QUOTES, 'UTF-8') ?></h1>
    <a href="<?= BASE_URL ?>/ventas/mesas" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver a Mesas</a>
</div>

<div class="mesa-container">

    <!-- Columna Izquierda: Productos del Inventario (AQUÍ ESTÁN LOS BOTONES) -->
    <div class="inventario-panel">
        <h3>Productos Disponibles</h3>
        <input type="text" id="buscarProducto" placeholder="Buscar producto...">

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($inventario)): ?>
                        <?php foreach ($inventario as $item): ?>
                            <?php $agotado = (int)$item['stock_actual'] <= 0; ?>
                            <tr<?= $agotado ? ' class="fila-agotada"' : '' ?>>
                                <td><?= htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>$<?= number_format($item['precio_unitario'], 2) ?></td>
                                <td><?= $item['stock_actual'] ?></td>
                                <td>
                                    <?php if ($agotado): ?>
                                        <span class="badge-agotado"><i class="fas fa-ban"></i> Agotado</span>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-success btn-sm add-item"
                                                data-id="<?= $item['id'] ?>"
                                                data-nombre="<?= htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                                data-precio="<?= $item['precio_unitario'] ?>"
                                                data-stock="<?= (int)$item['stock_actual'] ?>">
                                            <i class="fas fa-plus"></i> Agregar
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No hay productos en el inventario.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Columna Derecha: Cuenta Actual de la Mesa -->
    <div class="cuenta-panel">
        <h3>Cuenta de la Mesa</h3>
        <form action="<?= BASE_URL ?>/ventas/guardar-detalle" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $tokenCSRF ?>">
            <input type="hidden" name="mesa" value="<?= $numeroMesa ?>">
            <input type="hidden" name="venta_id" value="<?= $venta['id'] ?? '' ?>">

            <div class="table-responsive">
                <table class="table" id="tablaCuenta">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cant</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($detallesVenta)): ?>
                            <?php foreach ($detallesVenta as $det): ?>
                                <?php $maxDisponible = (int)$det['stock_actual'] + (int)$det['cantidad']; ?>
                                <tr>
                                    <td><?= htmlspecialchars($det['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><input type="number" name="productos[<?= $det['inventario_id'] ?>][cantidad]" value="<?= $det['cantidad'] ?>" min="1" max="<?= $maxDisponible ?>" class="input-cant" data-precio="<?= $det['precio_unitario'] ?>" data-max="<?= $maxDisponible ?>"></td>
                                    <td class="subtotal-item">$<?= number_format($det['subtotal'], 2) ?></td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <hr>
            <div class="total-cuenta">
                <span>Total:</span>
                <span id="granTotal">$0.00</span>
            </div>

            <div class="acciones-cuenta">
                <button type="submit" class="btn btn-guardar-cuenta">
                    <i class="fas fa-save"></i> Guardar / Cobrar Cuenta
                </button>
                <button type="submit" class="btn btn-cerrar-cuenta"
                        formaction="<?= BASE_URL ?>/ventas/cerrar-cuenta"
                        onclick="return confirm('¿Cerrar la venta y liberar la mesa?')">
                    <i class="fas fa-lock"></i> Cerrar Venta
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Script de Interactividad -->
<script>
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-item')) {
            const btn = e.target.closest('.add-item');
            const id = btn.dataset.id;
            const nombre = btn.dataset.nombre;
            const precio = parseFloat(btn.dataset.precio);
            const stockDisponible = parseInt(btn.dataset.stock, 10) || 0;

            const tbody = document.querySelector('#tablaCuenta tbody');

            let existingRow = Array.from(tbody.querySelectorAll('tr')).find(row => {
                const input = row.querySelector('.input-cant');
                return input && input.name.includes(`[${id}]`);
            });

            if (existingRow) {
                const input = existingRow.querySelector('.input-cant');
                const max = parseInt(input.dataset.max, 10) || stockDisponible;
                const nuevaCantidad = parseInt(input.value, 10) + 1;

                if (nuevaCantidad > max) {
                    alert(`No hay suficiente stock de "${nombre}". Disponible: ${max}.`);
                    return;
                }

                input.value = nuevaCantidad;
                actualizarSubtotal(existingRow, precio);
            } else {
                if (stockDisponible <= 0) {
                    alert(`"${nombre}" no tiene stock disponible.`);
                    return;
                }

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${nombre}</td>
                    <td><input type="number" name="productos[${id}][cantidad]" value="1" min="1" max="${stockDisponible}" class="input-cant" data-precio="${precio}" data-max="${stockDisponible}"></td>
                    <td class="subtotal-item">$${precio.toFixed(2)}</td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
                `;
                tbody.appendChild(newRow);
            }
            calcularTotalGeneral();
        }

        if (e.target.closest('.remove-row')) {
            e.target.closest('tr').remove();
            calcularTotalGeneral();
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('input-cant')) {
            const input = e.target;
            const row = input.closest('tr');
            const precio = parseFloat(input.dataset.precio);
            const max = parseInt(input.dataset.max, 10);

            if (max && parseInt(input.value, 10) > max) {
                input.value = max;
            }

            actualizarSubtotal(row, precio);
            calcularTotalGeneral();
        }
    });

    function actualizarSubtotal(row, precio) {
        const cant = parseInt(row.querySelector('.input-cant').value) || 0;
        const subtotal = cant * precio;
        row.querySelector('.subtotal-item').textContent = `$${subtotal.toFixed(2)}`;
    }

    function calcularTotalGeneral() {
        let total = 0;
        document.querySelectorAll('#tablaCuenta tbody tr').forEach(row => {
            const subtotalText = row.querySelector('.subtotal-item').textContent.replace('$', '');
            total += parseFloat(subtotalText) || 0;
        });
        document.getElementById('granTotal').textContent = `$${total.toFixed(2)}`;
    }

    calcularTotalGeneral();
</script>
