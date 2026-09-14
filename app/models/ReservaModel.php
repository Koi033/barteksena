<?php
/**
 * app/models/ReservaModel.php
 * Modelo para la gestión de reservas de mesas del bar.
 *
 * @package Bartek\Models
 */

require_once BASE_PATH . '/app/models/BaseModel.php';

class ReservaModel extends BaseModel
{
    /**
     * Obtiene todas las reservas ordenadas por fecha y hora.
     *
     * @return array Lista de reservas
     */
    public function obtenerTodas(): array
    {
        $sql = 'SELECT * FROM reservas ORDER BY fecha DESC, hora ASC';
        return $this->consultarTodos($sql);
    }

    /**
     * Busca una reserva por su ID.
     *
     * @param  int $id ID de la reserva
     * @return array|false
     */
    public function buscarPorId(int $id): array|false
    {
        $sql = 'SELECT * FROM reservas WHERE id = :id LIMIT 1';
        return $this->consultarUno($sql, [':id' => $id]);
    }

    /**
     * Crea una nueva reserva.
     *
     * @param  array $datos
     * @return int ID generado (0 en error)
     */
    public function crear(array $datos): int
    {
        $sql = 'INSERT INTO reservas (nombre_cliente, telefono, numero_mesa, personas, fecha, hora)
                VALUES (:nombre, :telefono, :mesa, :personas, :fecha, :hora)';

        return $this->insertar($sql, [
            ':nombre'   => $datos['nombre_cliente'],
            ':telefono' => $datos['telefono'] ?? null,
            ':mesa'     => $datos['numero_mesa'],
            ':personas' => $datos['personas'],
            ':fecha'    => $datos['fecha'],
            ':hora'     => $datos['hora'],
        ]);
    }

    /**
     * Elimina una reserva por ID.
     *
     * @param  int $id
     * @return int Filas afectadas
     */
    public function eliminar(int $id): int
    {
        return $this->ejecutar('DELETE FROM reservas WHERE id = :id', [':id' => $id]);
    }
}