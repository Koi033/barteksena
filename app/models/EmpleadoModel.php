<?php
/**
 * app/models/EmpleadoModel.php
 * Modelo CRUD para la gestión de empleados del bar.
 *
 * @package Bartek\Models
 */

require_once BASE_PATH . '/app/models/BaseModel.php';

class EmpleadoModel extends BaseModel
{
    /**
     * Obtiene todos los empleados activos con soporte de búsqueda y paginación.
     *
     * @param  string $busqueda  Término de búsqueda (nombre, puesto, dpto)
     * @param  string $depto     Filtro de departamento
     * @param  int    $pagina    Página actual (inicia en 1)
     * @param  int    $porPagina Registros por página
     * @return array             Lista de empleados
     */
    public function obtenerTodos(
        string $busqueda = '',
        string $depto = '',
        int $pagina = 1,
        int $porPagina = ITEMS_POR_PAGINA
    ): array {
        $params = [':activo' => 1];
        $where  = 'activo = :activo';

        if ($busqueda !== '') {
            $where .= ' AND (nombre_completo LIKE :busqueda
                          OR puesto         LIKE :busqueda
                          OR email          LIKE :busqueda)';
            $params[':busqueda'] = '%' . $busqueda . '%';
        }

        if ($depto !== '') {
            $where .= ' AND departamento = :depto';
            $params[':depto'] = $depto;
        }

        $offset = ($pagina - 1) * $porPagina;
        $sql    = "SELECT id, nombre_completo, puesto, departamento, email, telefono
                   FROM empleados
                   WHERE {$where}
                   ORDER BY nombre_completo ASC
                   LIMIT :limite OFFSET :offset";

        // LIMIT y OFFSET no admiten named params en todos los drivers; se usan bindValue
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limite',  $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset',  $offset,    PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Cuenta el total de empleados activos (para paginación).
     *
     * @param  string $busqueda Término de búsqueda
     * @param  string $depto    Filtro de departamento
     * @return int              Total de registros
     */
    public function contarFiltrados(string $busqueda = '', string $depto = ''): int
    {
        $params = [':activo' => 1];
        $where  = 'activo = :activo';

        if ($busqueda !== '') {
            $where .= ' AND (nombre_completo LIKE :busqueda OR puesto LIKE :busqueda OR email LIKE :busqueda)';
            $params[':busqueda'] = '%' . $busqueda . '%';
        }
        if ($depto !== '') {
            $where .= ' AND departamento = :depto';
            $params[':depto'] = $depto;
        }

        $sql = "SELECT COUNT(*) AS total FROM empleados WHERE {$where}";
        $res = $this->consultarUno($sql, $params);
        return (int)($res['total'] ?? 0);
    }

    /**
     * Busca un empleado por su ID.
     *
     * @param  int         $id ID del empleado
     * @return array|false     Datos del empleado o false
     */
    public function buscarPorId(int $id): array|false
    {
        $sql = 'SELECT * FROM empleados WHERE id = :id AND activo = 1 LIMIT 1';
        return $this->consultarUno($sql, [':id' => $id]);
    }

    /**
     * Crea un nuevo empleado en la base de datos.
     *
     * @param  array $datos Datos validados del formulario
     * @return int          ID generado (0 en error)
     */
    public function crear(array $datos): int
    {
        $sql = 'INSERT INTO empleados (nombre_completo, puesto, departamento, email, telefono)
                VALUES (:nombre, :puesto, :departamento, :email, :telefono)';
        return $this->insertar($sql, [
            ':nombre'       => $datos['nombre_completo'],
            ':puesto'       => $datos['puesto'],
            ':departamento' => $datos['departamento'],
            ':email'        => $datos['email'],
            ':telefono'     => $datos['telefono'] ?? null,
        ]);
    }

    /**
     * Actualiza los datos de un empleado existente.
     *
     * @param  int   $id    ID del empleado a actualizar
     * @param  array $datos Datos nuevos validados
     * @return int          Filas afectadas
     */
    public function actualizar(int $id, array $datos): int
    {
        $sql = 'UPDATE empleados
                SET nombre_completo = :nombre,
                    puesto          = :puesto,
                    departamento    = :departamento,
                    email           = :email,
                    telefono        = :telefono
                WHERE id = :id AND activo = 1';
        return $this->ejecutar($sql, [
            ':nombre'       => $datos['nombre_completo'],
            ':puesto'       => $datos['puesto'],
            ':departamento' => $datos['departamento'],
            ':email'        => $datos['email'],
            ':telefono'     => $datos['telefono'] ?? null,
            ':id'           => $id,
        ]);
    }

    /**
     * Realiza borrado lógico del empleado (activo = 0).
     * Nunca se eliminan físicamente los registros para conservar historial.
     *
     * @param  int $id ID del empleado
     * @return int     Filas afectadas
     */
    public function eliminar(int $id): int
    {
        $sql = 'UPDATE empleados SET activo = 0 WHERE id = :id';
        return $this->ejecutar($sql, [':id' => $id]);
    }

    /**
     * Obtiene los departamentos únicos existentes para el filtro.
     *
     * @return array Lista de departamentos
     */
    public function obtenerDepartamentos(): array
    {
        $sql = 'SELECT DISTINCT departamento FROM empleados WHERE activo = 1 ORDER BY departamento ASC';
        return $this->consultarTodos($sql);
    }
}
