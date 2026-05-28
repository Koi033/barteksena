<?php
/**
 * app/models/MenuModel.php
 * Modelo para la gestión de categorías del menú interactivo.
 *
 * @package Bartek\Models
 */
require_once BASE_PATH . '/app/models/BaseModel.php';

class MenuModel extends BaseModel
{
    /** Retorna todas las categorías activas. */
    public function obtenerCategorias(): array
    {
        $sql = 'SELECT c.id, c.nombre, c.descripcion,
                       COUNT(i.id) AS total_bebidas
                FROM categorias_menu c
                LEFT JOIN inventario i ON i.categoria_id = c.id AND i.activo = 1
                WHERE c.activa = 1
                GROUP BY c.id
                ORDER BY c.nombre ASC';
        return $this->consultarTodos($sql);
    }

    /** Busca una categoría por ID. */
    public function buscarPorId(int $id): array|false
    {
        $sql = 'SELECT * FROM categorias_menu WHERE id = :id AND activa = 1 LIMIT 1';
        return $this->consultarUno($sql, [':id' => $id]);
    }

    /** Crea una nueva categoría. */
    public function crearCategoria(array $datos): int
    {
        $sql = 'INSERT INTO categorias_menu (nombre, descripcion) VALUES (:nombre, :desc)';
        return $this->insertar($sql, [
            ':nombre' => $datos['nombre'],
            ':desc'   => $datos['descripcion'] ?? null,
        ]);
    }

    /** Actualiza una categoría. */
    public function actualizarCategoria(int $id, array $datos): int
    {
        $sql = 'UPDATE categorias_menu SET nombre = :nombre, descripcion = :desc WHERE id = :id';
        return $this->ejecutar($sql, [
            ':nombre' => $datos['nombre'],
            ':desc'   => $datos['descripcion'] ?? null,
            ':id'     => $id,
        ]);
    }

    /** Borrado lógico de categoría. */
    public function eliminarCategoria(int $id): int
    {
        return $this->ejecutar('UPDATE categorias_menu SET activa = 0 WHERE id = :id', [':id' => $id]);
    }

    /** Bebidas de una categoría específica. */
    public function obtenerBebidasPorCategoria(int $catId): array
    {
        $sql = 'SELECT * FROM inventario WHERE categoria_id = :cat AND activo = 1 ORDER BY nombre ASC';
        return $this->consultarTodos($sql, [':cat' => $catId]);
    }
}
