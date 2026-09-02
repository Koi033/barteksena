<?php
/**
 * app/models/PuntosModel.php
 * Modelo para gestionar las transacciones de puntos y fidelización
 */
require_once BASE_PATH . '/app/models/BaseModel.php';

class PuntosModel extends BaseModel
{
    // Método único para registrar directamente en el historial de puntos
    public function registrarPuntos(string $cedula, string $nombre, int $puntos, string $tipo)
    {
        $sql = "INSERT INTO historial_puntos (nombre, cedula_cliente, cantidad_puntos, tipo) 
                VALUES (:nombre, :cedula, :puntos, :tipo)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $nombre,
            ':cedula' => $cedula,
            ':puntos' => $puntos,
            ':tipo'   => $tipo
        ]);
        
    }
    public function obtenerTodosLosRegistros()
    {
        $sql = "SELECT * FROM historial_puntos ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un registro específico por ID para editarlo
    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM historial_puntos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar el estado (tipo) del movimiento de puntos
    public function actualizarEstado($id, string $tipo)
    {
        $sql = "UPDATE historial_puntos SET tipo = :tipo WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':tipo' => $tipo,
            ':id' => $id
        ]);
    }
    // Método para eliminar un registro del historial de puntos por su ID
    public function eliminarRegistro($id)
    {
        $sql = "DELETE FROM historial_puntos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

  // Método de búsqueda por cédula o nombre corregido
    public function buscarPorCedulaONombre($termino)
    {
        $sql = "SELECT * FROM historial_puntos WHERE cedula_cliente LIKE :cedula OR nombre LIKE :nombre ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        
        $busqueda = "%" . $termino . "%";
        $stmt->execute([
            ':cedula' => $busqueda,
            ':nombre' => $busqueda
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar el estado y la cantidad de puntos del cliente
    public function actualizarPuntosYEstado($id, int $puntos, string $tipo)
    {
        $sql = "UPDATE historial_puntos SET cantidad_puntos = :puntos, tipo = :tipo WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':puntos' => $puntos,
            ':tipo'   => $tipo,
            ':id'     => $id
        ]);
    }

    /**
     * Verifica si una cédula ya está inscrita en el club de fidelización,
     * es decir, si ya existe un movimiento de tipo 'registro' para ella.
     * Se usa para evitar que un mismo cliente se registre dos veces.
     *
     * @param  string $cedula
     * @return bool
     */
    public function existeClienteRegistrado(string $cedula): bool
    {
        $sql = "SELECT 1 FROM historial_puntos WHERE cedula_cliente = :cedula AND tipo = 'registro' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cedula' => $cedula]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Inscribe a un cliente nuevo en el club de fidelización de forma
     * PÚBLICA, es decir, sin que haya ningún empleado con sesión iniciada.
     * Se le otorgan puntos de bienvenida y el movimiento queda marcado con
     * tipo 'registro' para diferenciarlo de los puntos que gana un mesero
     * al momento de cobrar un consumo.
     *
     * @param  string $cedula
     * @param  string $nombre
     * @param  int    $puntosBienvenida
     * @return bool
     */
    public function registrarClientePublico(string $cedula, string $nombre, int $puntosBienvenida = 5): bool
    {
        $sql = "INSERT INTO historial_puntos (nombre, cedula_cliente, cantidad_puntos, tipo)
                VALUES (:nombre, :cedula, :puntos, 'registro')";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $nombre,
            ':cedula' => $cedula,
            ':puntos' => $puntosBienvenida,
        ]);
    }

    /**
     * Calcula el total de puntos vigentes de un cliente: suma lo ganado
     * y el registro (bienvenida), y resta lo canjeado o cancelado.
     *
     * @param  string $cedula
     * @return int
     */
    public function totalPuntos(string $cedula): int
    {
        $sql = "SELECT COALESCE(SUM(
                    CASE WHEN tipo IN ('canjeado', 'cancelado') THEN -cantidad_puntos ELSE cantidad_puntos END
                ), 0) AS total
                FROM historial_puntos
                WHERE cedula_cliente = :cedula";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cedula' => $cedula]);
        return (int) $stmt->fetchColumn();
    }
}   