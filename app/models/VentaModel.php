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
        $sql    = 'SELECT v.id, v.mesa, v.total, v.estado, v.creado_en,
                          COALESCE(e.nombre_completo, "Sin asignar") AS empleado
                   FROM ventas v
                   LEFT JOIN empleados e ON e.id = v.empleado_id
                   ORDER BY v.creado_en DESC
                   LIMIT :limite OFFSET :offset';
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
        $sql = 'SELECT COALESCE(SUM(total), 0) AS total
                FROM ventas WHERE DATE(creado_en) = CURDATE() AND estado = "cerrado"';
        $res = $this->consultarUno($sql);
        return (float)($res['total'] ?? 0);
    }

    /** Suma de ventas del mes actual. */
    public function totalMes(): float
    {
        $sql = 'SELECT COALESCE(SUM(total), 0) AS total
                FROM ventas
                WHERE MONTH(creado_en) = MONTH(NOW())
                  AND YEAR(creado_en)  = YEAR(NOW())
                  AND estado = "cerrado"';
        $res = $this->consultarUno($sql);
        return (float)($res['total'] ?? 0);
    }

    /** Crea una nueva venta (apertura de mesa). */
    public function crear(array $datos): int
    {
        $sql = 'INSERT INTO ventas (empleado_id, mesa, total, estado)
                VALUES (:emp, :mesa, :total, "abierto")';
        return $this->insertar($sql, [
            ':emp'   => $datos['empleado_id'] ?? null,
            ':mesa'  => $datos['mesa'],
            ':total' => $datos['total'] ?? 0.00,
        ]);
    }

    /** Cierra una venta activa. */
    public function cerrar(int $id): int
    {
        $sql = 'UPDATE ventas SET estado = "cerrado", cerrado_en = NOW() WHERE id = :id';
        return $this->ejecutar($sql, [':id' => $id]);
    }

    /** Top 5 bebidas más vendidas. */
    public function topBebidas(): array
    {
        $sql = 'SELECT inv.nombre, SUM(dv.cantidad) AS total_vendido
                FROM detalle_ventas dv
                INNER JOIN inventario inv ON inv.id = dv.inventario_id
                GROUP BY dv.inventario_id
                ORDER BY total_vendido DESC
                LIMIT 5';
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
        
        // Extraemos solo los valores de la columna 'mesa' en un arreglo simple
        return array_column($resultados, 'mesa');
    }
}
