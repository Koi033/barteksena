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
        $sql    = "SELECT v.id, v.mesa, v.total, v.estado, v.creado_en,
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

    /** Cierra una venta activa. */
    public function cerrar(int $id): int
    {
        $sql = "UPDATE ventas SET estado = 'cerrado', cerrado_en = NOW() WHERE id = :id";
        return $this->ejecutar($sql, [':id' => $id]);
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

            // 4. Actualizar el total general en la tabla ventas
            $sqlUpdateVenta = "UPDATE ventas SET total = :total WHERE id = :id";
            $this->ejecutar($sqlUpdateVenta, [
                ':total' => $totalGeneral,
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
}