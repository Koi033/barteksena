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
}   