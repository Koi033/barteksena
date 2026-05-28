<?php
/**
 * app/models/InventarioModel.php
 * Modelo CRUD para la gestión del inventario de bebidas.
 *
 * @package Bartek\Models
 */

require_once BASE_PATH . '/app/models/BaseModel.php';

class InventarioModel extends BaseModel
{
    /**
     * Obtiene todas las bebidas activas con filtrado y paginación.
     *
     * @param  string $busqueda  Término de búsqueda por nombre o código
     * @param  int    $catId     Filtro por categoría (0 = todas)
     * @param  int    $pagina    Página actual
     * @param  int    $porPagina Items por página
     * @return array             Lista de bebidas con nombre de categoría
     */
    public function obtenerTodos(
        string $busqueda = '',
        int $catId = 0,
        int $pagina = 1,
        int $porPagina = ITEMS_POR_PAGINA
    ): array {
        $params = [':activo' => 1];
        $where  = 'i.activo = :activo';

        if ($busqueda !== '') {
            $where .= ' AND (i.nombre LIKE :busqueda OR i.codigo LIKE :busqueda)';
            $params[':busqueda'] = '%' . $busqueda . '%';
        }
        if ($catId > 0) {
            $where .= ' AND i.categoria_id = :cat';
            $params[':cat'] = $catId;
        }

        $offset = ($pagina - 1) * $porPagina;
        $sql    = "SELECT i.id, i.codigo, i.nombre, c.nombre AS categoria,
                          i.stock_actual, i.stock_minimo, i.precio_unitario, i.actualizado_en
                   FROM inventario i
                   INNER JOIN categorias_menu c ON c.id = i.categoria_id
                   WHERE {$where}
                   ORDER BY i.nombre ASC
                   LIMIT :limite OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,    PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Cuenta bebidas con los mismos filtros (para paginación). */
    public function contarFiltrados(string $busqueda = '', int $catId = 0): int
    {
        $params = [':activo' => 1];
        $where  = 'activo = :activo';
        if ($busqueda !== '') {
            $where .= ' AND (nombre LIKE :busqueda OR codigo LIKE :busqueda)';
            $params[':busqueda'] = '%' . $busqueda . '%';
        }
        if ($catId > 0) {
            $where .= ' AND categoria_id = :cat';
            $params[':cat'] = $catId;
        }
        $sql = "SELECT COUNT(*) AS total FROM inventario WHERE {$where}";
        $res = $this->consultarUno($sql, $params);
        return (int)($res['total'] ?? 0);
    }

    /** Busca una bebida por ID. */
    public function buscarPorId(int $id): array|false
    {
        $sql = 'SELECT i.*, c.nombre AS categoria
                FROM inventario i
                INNER JOIN categorias_menu c ON c.id = i.categoria_id
                WHERE i.id = :id AND i.activo = 1 LIMIT 1';
        return $this->consultarUno($sql, [':id' => $id]);
    }

    /** Crea una nueva bebida en el inventario. */
    public function crear(array $datos): int
    {
        $sql = 'INSERT INTO inventario (categoria_id, codigo, nombre, stock_actual, stock_minimo, precio_unitario)
                VALUES (:cat, :codigo, :nombre, :stock, :minimo, :precio)';
        return $this->insertar($sql, [
            ':cat'    => $datos['categoria_id'],
            ':codigo' => $datos['codigo'],
            ':nombre' => $datos['nombre'],
            ':stock'  => $datos['stock_actual'],
            ':minimo' => $datos['stock_minimo'],
            ':precio' => $datos['precio_unitario'],
        ]);
    }

    /** Actualiza una bebida existente. */
    public function actualizar(int $id, array $datos): int
    {
        $sql = 'UPDATE inventario
                SET categoria_id = :cat, nombre = :nombre,
                    stock_actual = :stock, stock_minimo = :minimo, precio_unitario = :precio
                WHERE id = :id AND activo = 1';
        return $this->ejecutar($sql, [
            ':cat'    => $datos['categoria_id'],
            ':nombre' => $datos['nombre'],
            ':stock'  => $datos['stock_actual'],
            ':minimo' => $datos['stock_minimo'],
            ':precio' => $datos['precio_unitario'],
            ':id'     => $id,
        ]);
    }

    /** Borrado lógico de una bebida. */
    public function eliminar(int $id): int
    {
        return $this->ejecutar('UPDATE inventario SET activo = 0 WHERE id = :id', [':id' => $id]);
    }

    /** Retorna bebidas con stock por debajo del mínimo (alertas). */
    public function obtenerStockBajo(): array
    {
        $sql = 'SELECT i.codigo, i.nombre, i.stock_actual, i.stock_minimo
                FROM inventario i
                WHERE i.activo = 1 AND i.stock_actual <= i.stock_minimo
                ORDER BY i.stock_actual ASC';
        return $this->consultarTodos($sql);
    }
}
