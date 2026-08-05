<?php /* app/views/empleados/mesa_detalle.php */ ?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-cash-register" aria-hidden="true"></i> Mesa <?= htmlspecialchars($numeroMesa, ENT_QUOTES, 'UTF-8') ?></h1>
    <a href="<?= BASE_URL ?>/ventas/mesas" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver a Mesas</a>
</div>

<div class="mesa-container" style="display: flex; gap: 20px; margin-top: 20px; flex-wrap: wrap;">
    
    <!-- Columna Izquierda: Productos del Inventario (AQUÍ ESTÁN LOS BOTONES) -->
    <div class="inventario-panel" style="flex: 1; min-width: 300px; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); color: #333;">
        <h3>Productos Disponibles</h3>
        <input type="text" id="buscarProducto" placeholder="Buscar producto..." class="form-control" style="margin-bottom: 15px; width: 100%; padding: 8px;">
        
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table" style="width: 100%; text-align: left;">
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
                            <tr>
                                <td><?= htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>$<?= number_format($item['precio_unitario'], 2) ?></td>
                                <td><?= $item['stock_actual'] ?></td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm add-item" 
                                            data-id="<?= $item['id'] ?>" 
                                            data-nombre="<?= htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8') ?>" 
                                            data-precio="<?= $item['precio_unitario'] ?>">
                                        <i class="fas fa-plus"></i> Agregar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">No hay productos en el inventario.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Columna Derecha: Cuenta Actual de la Mesa -->
    <div class="cuenta-panel" style="flex: 1; min-width: 300px; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); color: #333;">
        <h3>Cuenta de la Mesa</h3>
        <form action="<?= BASE_URL ?>/ventas/guardar-detalle" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $tokenCSRF ?>">
            <input type="hidden" name="mesa" value="<?= $numeroMesa ?>">
            <input type="hidden" name="venta_id" value="<?= $venta['id'] ?? '' ?>">
            
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table class="table" id="tablaCuenta" style="width: 100%; text-align: left;">
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
                                <tr>
                                    <td><?= htmlspecialchars($det['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><input type="number" name="productos[<?= $det['inventario_id'] ?>][cantidad]" value="<?= $det['cantidad'] ?>" min="1" class="form-control input-cant" style="width: 60px;" data-precio="<?= $det['precio_unitario'] ?>"></td>
                                    <td class="subtotal-item">$<?= number_format($det['subtotal'], 2) ?></td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <hr>
            <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: bold; margin-bottom: 20px;">
                <span>Total:</span>
                <span id="granTotal">$0.00</span>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px;">Guardar / Cobrar Cuenta</button>
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

            const tbody = document.querySelector('#tablaCuenta tbody');
            
            let existingRow = Array.from(tbody.querySelectorAll('tr')).find(row => {
                const input = row.querySelector('.input-cant');
                return input && input.name.includes(`[${id}]`);
            });

            if (existingRow) {
                const input = existingRow.querySelector('.input-cant');
                input.value = parseInt(input.value) + 1;
                actualizarSubtotal(existingRow, precio);
            } else {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${nombre}</td>
                    <td><input type="number" name="productos[${id}][cantidad]" value="1" min="1" class="form-control input-cant" style="width: 60px;" data-precio="${precio}"></td>
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
            const row = e.target.closest('tr');
            const precio = parseFloat(e.target.dataset.precio);
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