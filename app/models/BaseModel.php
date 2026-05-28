<?php
/**
 * app/models/BaseModel.php
 * Clase base para todos los modelos del sistema Bartek.
 * Centraliza la conexión PDO y proporciona métodos CRUD
 * con prepared statements para prevenir inyección SQL.
 *
 * @package Bartek\Models
 */

class BaseModel
{
    /** @var PDO Conexión activa a la base de datos */
    protected PDO $db;

    /**
     * Constructor: obtiene la conexión PDO del Singleton Database.
     */
    public function __construct()
    {
        $this->db = Database::obtenerInstancia()->obtenerConexion();
    }

    /**
     * Ejecuta una consulta preparada y retorna todos los resultados.
     *
     * @param  string $sql      Consulta SQL con marcadores de posición
     * @param  array  $params   Parámetros a enlazar (previene inyección SQL)
     * @return array            Array de resultados como arrays asociativos
     */
    protected function consultarTodos(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('[Bartek][Model] consultarTodos: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ejecuta una consulta preparada y retorna un único registro.
     *
     * @param  string     $sql    Consulta SQL con marcadores de posición
     * @param  array      $params Parámetros a enlazar
     * @return array|false        Registro asociativo o false si no existe
     */
    protected function consultarUno(string $sql, array $params = []): array|false
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('[Bartek][Model] consultarUno: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ejecuta una instrucción INSERT, UPDATE o DELETE con parámetros.
     * Retorna el número de filas afectadas.
     *
     * @param  string $sql    Consulta SQL con marcadores de posición
     * @param  array  $params Parámetros a enlazar
     * @return int            Filas afectadas, o -1 en caso de error
     */
    protected function ejecutar(string $sql, array $params = []): int
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log('[Bartek][Model] ejecutar: ' . $e->getMessage());
            return -1;
        }
    }

    /**
     * Ejecuta un INSERT y retorna el ID autogenerado del nuevo registro.
     *
     * @param  string $sql    Consulta INSERT con marcadores de posición
     * @param  array  $params Parámetros a enlazar
     * @return int            ID insertado, o 0 en caso de error
     */
    protected function insertar(string $sql, array $params = []): int
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('[Bartek][Model] insertar: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Sanea un string para evitar XSS al mostrarlo en HTML.
     * Se usa en los controladores antes de pasar datos a las vistas.
     *
     * @param  string $valor Valor a sanear
     * @return string        Valor con caracteres especiales escapados
     */
    public static function sanear(string $valor): string
    {
        return htmlspecialchars(strip_tags(trim($valor)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Cuenta el total de registros de una tabla (para paginación).
     *
     * @param  string $tabla  Nombre de la tabla
     * @param  string $where  Cláusula WHERE opcional (ya validada)
     * @param  array  $params Parámetros del WHERE
     * @return int            Total de registros
     */
    protected function contarRegistros(string $tabla, string $where = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) AS total FROM `{$tabla}`";
        if ($where !== '') {
            $sql .= " WHERE {$where}";
        }
        $resultado = $this->consultarUno($sql, $params);
        return (int) ($resultado['total'] ?? 0);
    }
}
