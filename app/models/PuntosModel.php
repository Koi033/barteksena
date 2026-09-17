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
        // Se une con recompensas_puntos para poder mostrar, en el historial,
        // el nombre real de la recompensa canjeada (antes se "adivinaba" el
        // premio según un umbral fijo de puntos, sin relación con lo que el
        // cliente realmente canjeó).
        $sql = "SELECT h.*, r.nombre AS recompensa_nombre, r.descripcion AS recompensa_descripcion
                FROM historial_puntos h
                LEFT JOIN recompensas_puntos r ON r.id = h.recompensa_id
                ORDER BY h.id DESC";
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
        $sql = "SELECT h.*, r.nombre AS recompensa_nombre, r.descripcion AS recompensa_descripcion
                FROM historial_puntos h
                LEFT JOIN recompensas_puntos r ON r.id = h.recompensa_id
                WHERE h.cedula_cliente LIKE :cedula OR h.nombre LIKE :nombre
                ORDER BY h.id DESC";
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
                    CASE WHEN tipo IN ('canjeado', 'cancelado', 'descontado') THEN -cantidad_puntos ELSE cantidad_puntos END
                ), 0) AS total
                FROM historial_puntos
                WHERE cedula_cliente = :cedula";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cedula' => $cedula]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Busca el nombre más reciente con el que quedó registrado un cliente,
     * a partir de su cédula. Útil para autocompletar el nombre en la vista
     * de mesas cuando el mesero solo digita la cédula.
     *
     * @param  string $cedula
     * @return string|null
     */
    public function obtenerNombrePorCedula(string $cedula): ?string
    {
        $sql = "SELECT nombre FROM historial_puntos
                WHERE cedula_cliente = :cedula
                ORDER BY id DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cedula' => $cedula]);
        $nombre = $stmt->fetchColumn();
        return $nombre !== false ? $nombre : null;
    }

    /**
     * Registra un abono ("ganado") de puntos para un cliente desde la vista
     * de mesas, dejando trazabilidad de qué empleado lo hizo y desde qué mesa.
     *
     * @param  string   $cedula
     * @param  string   $nombre
     * @param  int      $puntos
     * @param  int|null $empleadoId
     * @param  string|null $mesa
     * @return bool
     */
    public function agregarPuntos(string $cedula, string $nombre, int $puntos, ?int $empleadoId = null, ?string $mesa = null): bool
    {
        $sql = "INSERT INTO historial_puntos (nombre, cedula_cliente, cantidad_puntos, tipo, empleado_id, mesa)
                VALUES (:nombre, :cedula, :puntos, 'ganado', :empleado_id, :mesa)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre'      => $nombre,
            ':cedula'      => $cedula,
            ':puntos'      => $puntos,
            ':empleado_id' => $empleadoId,
            ':mesa'        => $mesa,
        ]);
    }

    /**
     * Descuenta puntos de un cliente (ej: corrección de un abono mal hecho,
     * penalización, etc.). Se registra con tipo 'descontado', que
     * totalPuntos() ya interpreta como una resta.
     *
     * @param  string   $cedula
     * @param  string   $nombre
     * @param  int      $puntos
     * @param  int|null $empleadoId
     * @param  string|null $mesa
     * @return bool
     */
    public function descontarPuntos(string $cedula, string $nombre, int $puntos, ?int $empleadoId = null, ?string $mesa = null): bool
    {
        $sql = "INSERT INTO historial_puntos (nombre, cedula_cliente, cantidad_puntos, tipo, empleado_id, mesa)
                VALUES (:nombre, :cedula, :puntos, 'descontado', :empleado_id, :mesa)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre'      => $nombre,
            ':cedula'      => $cedula,
            ':puntos'      => $puntos,
            ':empleado_id' => $empleadoId,
            ':mesa'        => $mesa,
        ]);
    }

    /**
     * Redime (canjea) una recompensa del catálogo a cambio de los puntos
     * que esta requiera. No valida aquí si el cliente tiene puntos
     * suficientes: eso lo hace el controlador antes de llamar a este método,
     * usando totalPuntos(), para poder mostrar un mensaje de error claro.
     *
     * @param  string   $cedula
     * @param  string   $nombre
     * @param  int      $puntosRequeridos
     * @param  int      $recompensaId
     * @param  int|null $empleadoId
     * @param  string|null $mesa
     * @return bool
     */
    public function redimirPuntos(
        string $cedula,
        string $nombre,
        int $puntosRequeridos,
        int $recompensaId,
        ?int $empleadoId = null,
        ?string $mesa = null
    ): bool {
        $sql = "INSERT INTO historial_puntos
                    (nombre, cedula_cliente, cantidad_puntos, tipo, empleado_id, mesa, recompensa_id)
                VALUES
                    (:nombre, :cedula, :puntos, 'canjeado', :empleado_id, :mesa, :recompensa_id)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre'        => $nombre,
            ':cedula'        => $cedula,
            ':puntos'        => $puntosRequeridos,
            ':empleado_id'   => $empleadoId,
            ':mesa'          => $mesa,
            ':recompensa_id' => $recompensaId,
        ]);
    }

    /**
     * Obtiene las recompensas canjeadas (puntos) asociadas a una mesa dentro
     * de un rango de fechas. Se usa desde el detalle de una venta para poder
     * mostrar las recompensas que el cliente redimió mientras esa mesa
     * estuvo abierta: canjear una recompensa NO genera ninguna fila en
     * detalle_ventas (el club de fidelización es independiente del
     * inventario), por lo que hasta ahora esas cuentas se veían sin detalle
     * en el listado de Ventas.
     *
     * @param  string      $mesa
     * @param  string      $desde  Fecha/hora de apertura de la venta (creado_en)
     * @param  string|null $hasta  Fecha/hora de cierre de la venta (cerrado_en); null = hasta ahora
     * @return array
     */
    public function obtenerRecompensasPorMesaYRango(string $mesa, string $desde, ?string $hasta = null): array
    {
        $sql = "SELECT h.id, h.cantidad_puntos, h.fecha, r.nombre AS recompensa_nombre
                FROM historial_puntos h
                INNER JOIN recompensas_puntos r ON r.id = h.recompensa_id
                WHERE h.tipo = 'canjeado'
                  AND h.mesa = :mesa
                  AND h.fecha >= :desde
                  AND h.fecha <= :hasta
                ORDER BY h.fecha ASC";

        return $this->consultarTodos($sql, [
            ':mesa'  => $mesa,
            ':desde' => $desde,
            ':hasta' => $hasta ?? date('Y-m-d H:i:s'),
        ]);
    }
}
