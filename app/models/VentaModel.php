<?php
/**
 * app/models/VentaModel.php
 * Modelo para la gestión de ventas y transacciones del bar.
 *
 * @package Bartek\Models
 */
require_once BASE_PATH . '/app/models/BaseModel.php';

class VentaModel extends BaseModel
{
    /**
     * Retorna las ventas con soporte de paginación.
     *
     * @param  int $pagina    Página actual
     * @param  int $porPagina Items por página
     * @return array
     */
    public function obtenerTodos(int $pagina = 1, int $porPagina = ITEMS_POR_PAGINA): array
    {
        $offset = ($pagina - 1) * $porPagina;
        $sql    = "SELECT v.id, v.mesa, v.total, v.descuento_recompensa, v.descuento_categoria_acumulado,
                  v.estado, v.metodo_pago, v.creado_en,
                  COALESCE(e.nombre_completo, 'Sin asignar') AS empleado
           FROM ventas v
           LEFT JOIN empleados e ON e.id = v.empleado_id
           ORDER BY v.creado_en DESC
           LIMIT :limite OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,    PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Cuenta el total de ventas. */
    public function contarTotal(): int
    {
        $res = $this->consultarUno('SELECT COUNT(*) AS total FROM ventas');
        return (int)($res['total'] ?? 0);
    }

    /** Suma de ventas del día actual. */
    public function totalHoy(): float
    {
        $sql = "SELECT COALESCE(SUM(total), 0) AS total
                FROM ventas WHERE DATE(creado_en) = CURDATE() AND estado = 'cerrado'";
        $res = $this->consultarUno($sql);
        return (float)($res['total'] ?? 0);
    }

    /** Suma de ventas del mes actual. */
    public function totalMes(): float
    {
        $sql = "SELECT COALESCE(SUM(total), 0) AS total
                FROM ventas
                WHERE MONTH(creado_en) = MONTH(NOW())
                  AND YEAR(creado_en)  = YEAR(NOW())
                  AND estado = 'cerrado'";
        $res = $this->consultarUno($sql);
        return (float)($res['total'] ?? 0);
    }

    /** Crea una nueva venta (apertura de mesa). */
    public function crear(array $datos): int
    {
        $sql = "INSERT INTO ventas (empleado_id, mesa, total, estado)
                VALUES (:emp, :mesa, :total, 'abierto')";
        return $this->insertar($sql, [
            ':emp'   => $datos['empleado_id'] ?? null,
            ':mesa'  => $datos['mesa'],
            ':total' => $datos['total'] ?? 0.00,
        ]);
    }

    /**
     * Cierra una venta activa y registra opcionalmente el método de pago
     * utilizado (efectivo, tarjeta_credito, nequi_daviplata, bre_b).
     *
     * @param int         $id
     * @param string|null $metodoPago
     * @return int
     */
    public function cerrar(int $id, ?string $metodoPago = null): int
    {
        $sql = "UPDATE ventas
                SET estado = 'cerrado', metodo_pago = :metodo_pago, cerrado_en = NOW()
                WHERE id = :id";
        return $this->ejecutar($sql, [
            ':id'          => $id,
            ':metodo_pago' => $metodoPago,
        ]);
    }

    /** Top 5 bebidas más vendidas. */
    public function topBebidas(): array
    {
        $sql = "SELECT inv.nombre, SUM(dv.cantidad) AS total_vendido
                FROM detalle_ventas dv
                INNER JOIN inventario inv ON inv.id = dv.inventario_id
                GROUP BY dv.inventario_id
                ORDER BY total_vendido DESC
                LIMIT 5";
        return $this->consultarTodos($sql);
    }

    /**
     * Obtiene los números de mesa que tienen una venta abierta.
     *
     * @return array Arreglo con los números de las mesas ocupadas (ej. ['1', '5'])
     */
    public function obtenerMesasOcupadas(): array
    {
        $sql = "SELECT DISTINCT mesa FROM ventas WHERE estado = 'abierto'";
        $resultados = $this->consultarTodos($sql);

        return array_column($resultados, 'mesa');
    }

    /**
     * Obtiene una venta abierta por su número de mesa.
     *
     * @param string|int $mesa
     * @return array|false
     */
    public function obtenerVentaAbiertaPorMesa($mesa)
    {
        $sql = "SELECT * FROM ventas WHERE mesa = :mesa AND estado = 'abierto' LIMIT 1";
        return $this->consultarUno($sql, [':mesa' => $mesa]);
    }

    /**
     * Obtiene los detalles (productos) de una venta específica.
     *
     * @param int $ventaId
     * @return array
     */
    public function obtenerDetallesVenta(int $ventaId): array
    {
        $sql = "SELECT dv.*, inv.nombre, inv.stock_actual
                FROM detalle_ventas dv
                INNER JOIN inventario inv ON inv.id = dv.inventario_id
                WHERE dv.venta_id = :venta_id";
        return $this->consultarTodos($sql, [':venta_id' => $ventaId]);
    }

    /**
     * Actualiza o reemplaza los detalles de la venta, recalcula el total general
     * y descuenta las cantidades correspondientes del inventario.
     *
     * IMPORTANTE: al recalcular el total desde cero, se conserva el
     * descuento por recompensas ya aplicado (descuento_recompensa), para
     * que agregar/quitar productos de la cuenta no borre un beneficio
     * de fidelización que el cliente ya canjeó.
     *
     * @param int $ventaId
     * @param array $productos Array asociativo [inventario_id => ['cantidad' => X]]
     * @return void
     */
    public function actualizarDetallesVenta(int $ventaId, array $productos): void
    {
        $this->db->beginTransaction();

        try {
            // 1. Obtener los detalles anteriores para devolver el stock al inventario antes de recalcular
            $detallesAnteriores = $this->obtenerDetallesVenta($ventaId);
            foreach ($detallesAnteriores as $det) {
                $sqlDevolverStock = "UPDATE inventario SET stock_actual = stock_actual + :cantidad WHERE id = :id";
                $this->ejecutar($sqlDevolverStock, [
                    ':cantidad' => $det['cantidad'],
                    ':id'       => $det['inventario_id']
                ]);
            }

            // 2. Eliminar los detalles anteriores de esta venta
            $sqlDelete = "DELETE FROM detalle_ventas WHERE venta_id = :venta_id";
            $this->ejecutar($sqlDelete, [':venta_id' => $ventaId]);

            $totalGeneral = 0.00;

            // 3. Insertar los nuevos productos y descontar el stock actual
            foreach ($productos as $inventarioId => $info) {
                $cantidad = (int)($info['cantidad'] ?? 1);
                if ($cantidad <= 0) continue;

                // Obtener precio unitario y stock actual del inventario
                $sqlInventario = "SELECT nombre, precio_unitario, stock_actual FROM inventario WHERE id = :id FOR UPDATE";
                $invItem = $this->consultarUno($sqlInventario, [':id' => $inventarioId]);

                if ($invItem) {
                    if ($cantidad > (int)$invItem['stock_actual']) {
                        throw new \Exception(
                            'Stock insuficiente para "' . $invItem['nombre'] . '". ' .
                            'Disponible: ' . (int)$invItem['stock_actual'] . ', solicitado: ' . $cantidad
                        );
                    }

                    $precioUnitario = (float)$invItem['precio_unitario'];
                    $subtotal = $cantidad * $precioUnitario;
                    $totalGeneral += $subtotal;

                    // Insertar el detalle de la venta
                    $sqlInsert = "INSERT INTO detalle_ventas (venta_id, inventario_id, cantidad, precio_unitario, subtotal)
                                  VALUES (:venta_id, :inventario_id, :cantidad, :precio_unitario, :subtotal)";
                    $this->ejecutar($sqlInsert, [
                        ':venta_id'        => $ventaId,
                        ':inventario_id'   => $inventarioId,
                        ':cantidad'        => $cantidad,
                        ':precio_unitario' => $precioUnitario,
                        ':subtotal'        => $subtotal
                    ]);

                    // Descontar del inventario
                    $sqlDescontar = "UPDATE inventario SET stock_actual = stock_actual - :cantidad WHERE id = :id";
                    $this->ejecutar($sqlDescontar, [
                        ':cantidad' => $cantidad,
                        ':id'       => $inventarioId
                    ]);
                }
            }

            // 4. Restar el descuento por CATEGORÍA ya aplicado a esta venta
            //    (si el mesero ya había canjeado una recompensa de ese tipo
            //    antes de seguir agregando/quitando productos de la cuenta).
            //    Los productos regalados NO se restan aquí: ya entran en
            //    $totalGeneral con precio $0 porque son líneas reales de
            //    detalle_ventas, así que restarlos de nuevo los descontaría
            //    dos veces.
            $ventaActual = $this->consultarUno(
                'SELECT descuento_categoria_acumulado FROM ventas WHERE id = :id',
                [':id' => $ventaId]
            );
            $descuentoCategoria = (float) ($ventaActual['descuento_categoria_acumulado'] ?? 0);
            $totalConDescuento = max($totalGeneral - $descuentoCategoria, 0);

            // 5. Actualizar el total general en la tabla ventas
            $sqlUpdateVenta = "UPDATE ventas SET total = :total WHERE id = :id";
            $this->ejecutar($sqlUpdateVenta, [
                ':total' => $totalConDescuento,
                ':id'    => $ventaId
            ]);

            // Confirmar transacción
            $this->db->commit();

        } catch (\Exception $e) {
            // Revertir cambios en caso de error
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Aplica el efecto de una recompensa de fidelización sobre la venta
     * abierta de una mesa. Es el punto único donde vive la lógica de
     * automatización pedida: el mesero solo elige la recompensa y aquí
     * se calcula y descuenta el valor real de la cuenta.
     *
     *  - descuento_categoria: resta al total el % indicado, calculado
     *    sobre lo que el cliente ya consumió de esa categoría en la
     *    cuenta actual. Si no hay productos de esa categoría, falla con
     *    un mensaje claro en vez de aplicar un descuento de $0.
     *  - producto_gratis: agrega una unidad del producto al detalle con
     *    precio $0 (no afecta el total porque esa línea suma $0), valida
     *    stock antes de entregarlo y lo descuenta del inventario igual
     *    que cualquier otra venta.
     *
     * Todo ocurre en una única transacción: si algo falla (sin stock,
     * categoría sin consumo, etc.) no queda ningún cambio a medias.
     *
     * @param  int   $ventaId
     * @param  array $recompensa Fila de recompensas_puntos (con tipo,
     *                           categoria_id, porcentaje_descuento y/o
     *                           producto_id según corresponda)
     * @return float             Valor monetario del beneficio otorgado,
     *                           útil para mostrarlo en el mensaje al mesero
     * @throws \Exception        Con un mensaje listo para mostrar al usuario
     */
    public function aplicarDescuentoRecompensa(int $ventaId, array $recompensa): float
    {
        $this->db->beginTransaction();

        try {
            $valorBeneficio = 0.00;

            if ($recompensa['tipo'] === 'descuento_categoria') {
                $res = $this->consultarUno(
                    "SELECT COALESCE(SUM(dv.subtotal), 0) AS subtotal_categoria
                     FROM detalle_ventas dv
                     INNER JOIN inventario i ON i.id = dv.inventario_id
                     WHERE dv.venta_id = :venta_id AND i.categoria_id = :categoria_id",
                    [':venta_id' => $ventaId, ':categoria_id' => $recompensa['categoria_id']]
                );
                $subtotalCategoria = (float) ($res['subtotal_categoria'] ?? 0);

                if ($subtotalCategoria <= 0) {
                    throw new \Exception('La mesa no tiene productos de esa categoría para aplicar el descuento.');
                }

                $valorBeneficio = round($subtotalCategoria * ((float) $recompensa['porcentaje_descuento'] / 100), 2);

                // NOTA: se usa un placeholder distinto por cada aparición del
                // mismo valor porque el proyecto corre con
                // PDO::ATTR_EMULATE_PREPARES = false (ver config/database.php),
                // y los prepared statements nativos de MySQL no permiten
                // reutilizar un mismo parámetro con nombre más de una vez
                // dentro de la misma consulta.
                $this->ejecutar(
                    'UPDATE ventas
                     SET descuento_recompensa = descuento_recompensa + :d1,
                         descuento_categoria_acumulado = descuento_categoria_acumulado + :d2,
                         total = GREATEST(total - :d3, 0)
                     WHERE id = :id',
                    [':d1' => $valorBeneficio, ':d2' => $valorBeneficio, ':d3' => $valorBeneficio, ':id' => $ventaId]
                );

            } elseif ($recompensa['tipo'] === 'producto_gratis') {
                $producto = $this->consultarUno(
                    'SELECT id, nombre, precio_unitario, stock_actual
                     FROM inventario WHERE id = :id AND activo = 1 FOR UPDATE',
                    [':id' => $recompensa['producto_id']]
                );

                if (!$producto) {
                    throw new \Exception('El producto de la recompensa ya no está disponible.');
                }
                if ((int) $producto['stock_actual'] <= 0) {
                    throw new \Exception('No hay stock disponible de "' . $producto['nombre'] . '" para entregarlo gratis.');
                }

                $this->ejecutar(
                    'INSERT INTO detalle_ventas (venta_id, inventario_id, cantidad, precio_unitario, subtotal)
                     VALUES (:venta_id, :inventario_id, 1, 0, 0)',
                    [':venta_id' => $ventaId, ':inventario_id' => $producto['id']]
                );

                $this->ejecutar(
                    'UPDATE inventario SET stock_actual = stock_actual - 1 WHERE id = :id',
                    [':id' => $producto['id']]
                );

                $valorBeneficio = (float) $producto['precio_unitario'];

                // El total no se resta aquí: la línea gratis ya entra en $0
                // en detalle_ventas. Solo se registra el valor regalado
                // para reportes/trazabilidad.
                $this->ejecutar(
                    'UPDATE ventas SET descuento_recompensa = descuento_recompensa + :d WHERE id = :id',
                    [':d' => $valorBeneficio, ':id' => $ventaId]
                );
            } else {
                throw new \Exception('Tipo de recompensa no soportado.');
            }

            $this->db->commit();
            return $valorBeneficio;

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
