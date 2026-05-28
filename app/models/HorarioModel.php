<?php
/**
 * app/models/HorarioModel.php
 * Modelo para la gestión de horarios y turnos de empleados.
 *
 * @package Bartek\Models
 */
require_once BASE_PATH . '/app/models/BaseModel.php';

class HorarioModel extends BaseModel
{
    /**
     * Obtiene los horarios con datos del empleado, con paginación.
     *
     * @param  int $pagina    Página actual
     * @param  int $porPagina Items por página
     * @return array
     */
    public function obtenerTodos(int $pagina = 1, int $porPagina = ITEMS_POR_PAGINA): array
    {
        $offset = ($pagina - 1) * $porPagina;
        $sql    = 'SELECT h.id, e.nombre_completo, h.fecha,
                          h.hora_inicio, h.hora_fin, h.estado
                   FROM horarios h
                   INNER JOIN empleados e ON e.id = h.empleado_id
                   ORDER BY h.fecha DESC, h.hora_inicio ASC
                   LIMIT :limite OFFSET :offset';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,    PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Horarios pendientes de aprobación. */
    public function obtenerPendientes(): array
    {
        $sql = 'SELECT h.id, e.nombre_completo, h.fecha, h.hora_inicio, h.hora_fin
                FROM horarios h
                INNER JOIN empleados e ON e.id = h.empleado_id
                WHERE h.estado = "pendiente"
                ORDER BY h.fecha ASC';
        return $this->consultarTodos($sql);
    }

    /** Crea un nuevo horario. */
    public function crear(array $datos): int
    {
        $sql = 'INSERT INTO horarios (empleado_id, fecha, hora_inicio, hora_fin)
                VALUES (:emp, :fecha, :inicio, :fin)';
        return $this->insertar($sql, [
            ':emp'   => $datos['empleado_id'],
            ':fecha' => $datos['fecha'],
            ':inicio'=> $datos['hora_inicio'],
            ':fin'   => $datos['hora_fin'],
        ]);
    }

    /**
     * Cambia el estado de un horario (aprobado | rechazado).
     *
     * @param  int    $id     ID del horario
     * @param  string $estado Nuevo estado
     * @return int            Filas afectadas
     */
    public function cambiarEstado(int $id, string $estado): int
    {
        // Validar estado permitido para evitar valores inesperados
        $permitidos = ['pendiente', 'aprobado', 'rechazado'];
        if (!in_array($estado, $permitidos, true)) {
            return 0;
        }
        $sql = 'UPDATE horarios SET estado = :estado WHERE id = :id';
        return $this->ejecutar($sql, [':estado' => $estado, ':id' => $id]);
    }

    /** Elimina un horario por ID. */
    public function eliminar(int $id): int
    {
        return $this->ejecutar('DELETE FROM horarios WHERE id = :id', [':id' => $id]);
    }

    /** Total de horarios para paginación. */
    public function contarTotal(): int
    {
        $res = $this->consultarUno('SELECT COUNT(*) AS total FROM horarios');
        return (int)($res['total'] ?? 0);
    }
}
