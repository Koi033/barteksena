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
     * Busca un empleado por el ID de su cuenta de usuario vinculada.
     * Útil al iniciar sesión, para saber qué empleado corresponde
     * al usuario autenticado (y así registrar quién hizo cada venta).
     *
     * @param  int         $usuarioId ID del usuario (usuarios.id)
     * @return array|false            Datos del empleado o false si no existe
     */
    public function buscarPorUsuarioId(int $usuarioId): array|false
    {
        $sql = 'SELECT id, nombre_completo, puesto, departamento
                FROM empleados
                WHERE usuario_id = :usuario_id AND activo = 1
                LIMIT 1';
        return $this->consultarUno($sql, [':usuario_id' => $usuarioId]);
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
    /**
     * Busca el empleado vinculado a un usuario del sistema (login).
     * Usada para resolver el empleado_id a partir de $_SESSION['usuario_id'].
     *
     * @param  int         $usuarioId ID del usuario logueado
     * @return array|false            Datos del empleado o false si no existe
     */
    public function buscarPorUsuarioId(int $usuarioId): array|false
    {
        $sql = 'SELECT * FROM empleados WHERE usuario_id = :usuario_id AND activo = 1 LIMIT 1';
        return $this->consultarUno($sql, [':usuario_id' => $usuarioId]);
    }

    /**
     * Crea un usuario y un empleado vinculado mediante una transacción.
     *
     * @param  array $datosEmpleado Datos del empleado
     * @param  array $datosUsuario  Datos para el inicio de sesión
     * @return int                  ID del empleado generado (0 en error)
     */
    public function crearConCuenta(array $datosEmpleado, array $datosUsuario): int
    {
        try {
            // Iniciar transacción
            $this->db->beginTransaction();

            // 1. Crear el usuario (sistema de acceso)
            $sqlUser = 'INSERT INTO usuarios (nombre, apellido, email, telefono, usuario, contrasena, rol)
                        VALUES (:nombre, :apellido, :email, :telefono, :usuario, :contrasena, :rol)';
            $stmtUser = $this->db->prepare($sqlUser);
            $stmtUser->execute([
                ':nombre'     => $datosUsuario['nombre'],
                ':apellido'   => $datosUsuario['apellido'],
                ':email'      => $datosUsuario['email'],
                ':telefono'   => $datosUsuario['telefono'],
                ':usuario'    => $datosUsuario['usuario'],
                ':contrasena' => $datosUsuario['contrasena'],
                ':rol'        => 'empleado', // Rol forzado
            ]);
            
            // Obtener el ID del usuario recién creado
            $usuarioId = (int) $this->db->lastInsertId();

            // 2. Crear el empleado vinculado al usuario
            $sqlEmp = 'INSERT INTO empleados (usuario_id, nombre_completo, puesto, departamento, email, telefono)
                       VALUES (:usuario_id, :nombre, :puesto, :departamento, :email, :telefono)';
            $stmtEmp = $this->db->prepare($sqlEmp);
            $stmtEmp->execute([
                ':usuario_id'   => $usuarioId,
                ':nombre'       => $datosEmpleado['nombre_completo'],
                ':puesto'       => $datosEmpleado['puesto'],
                ':departamento' => $datosEmpleado['departamento'],
                ':email'        => $datosEmpleado['email'],
                ':telefono'     => $datosEmpleado['telefono'] ?? null,
            ]);

            $empleadoId = (int) $this->db->lastInsertId();

            // Confirmar transacción
            $this->db->commit();
            
            return $empleadoId;

        } catch (PDOException $e) {
            // Revertir todo si hay un error (ej. usuario duplicado)
            $this->db->rollBack();
            error_log('[Bartek][EmpleadoModel] crearConCuenta: ' . $e->getMessage());
            return 0;
        }
    }
}
