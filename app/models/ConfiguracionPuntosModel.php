<?php
/**
 * app/models/ConfiguracionPuntosModel.php
 * Gestiona la configuración general de acumulación de puntos
 * (dinero/productos necesarios para ganar puntos) y el catálogo
 * de recompensas canjeables por puntos.
 *
 * @package Bartek\Models
 */
require_once BASE_PATH . '/app/models/BaseModel.php';

class ConfiguracionPuntosModel extends BaseModel
{
    /**
     * Obtiene la configuración general de acumulación de puntos.
     * Si por alguna razón no existe la fila (id = 1), devuelve valores
     * por defecto razonables en lugar de romper la vista.
     *
     * @return array
     */
    public function obtenerConfiguracion(): array
    {
        $config = $this->consultarUno(
            "SELECT * FROM configuracion_puntos WHERE id = 1 LIMIT 1"
        );

        if ($config) {
            return $config;
        }

        return [
            'id'                => 1,
            'modo_acumulacion'  => 'dinero',
            'valor_acumulacion' => 10000.00,
            'puntos_otorgados'  => 1,
            'puntos_bienvenida' => 5,
        ];
    }

    /**
     * Guarda (crea o actualiza) la configuración general de acumulación.
     *
     * @param  string $modo             'dinero' | 'productos'
     * @param  float  $valorAcumulacion Monto en pesos o cantidad de productos
     * @param  int    $puntosOtorgados  Puntos que se dan al cumplir la regla
     * @param  int    $puntosBienvenida Puntos de bienvenida al inscribirse
     * @return bool
     */
    public function guardarConfiguracion(
        string $modo,
        float $valorAcumulacion,
        int $puntosOtorgados,
        int $puntosBienvenida
    ): bool {
        $sql = "INSERT INTO configuracion_puntos
                    (id, modo_acumulacion, valor_acumulacion, puntos_otorgados, puntos_bienvenida)
                VALUES
                    (1, :modo, :valor, :otorgados, :bienvenida)
                ON DUPLICATE KEY UPDATE
                    modo_acumulacion  = VALUES(modo_acumulacion),
                    valor_acumulacion = VALUES(valor_acumulacion),
                    puntos_otorgados  = VALUES(puntos_otorgados),
                    puntos_bienvenida = VALUES(puntos_bienvenida)";

        return $this->ejecutar($sql, [
            ':modo'      => $modo,
            ':valor'     => $valorAcumulacion,
            ':otorgados' => $puntosOtorgados,
            ':bienvenida' => $puntosBienvenida,
        ]) >= 0;
    }

    /**
     * Lista las recompensas del catálogo.
     *
     * @param  bool $soloActivas Si es true, solo trae las recompensas activas
     * @return array
     */
    public function obtenerRecompensas(bool $soloActivas = false): array
    {
        $sql = "SELECT * FROM recompensas_puntos";
        if ($soloActivas) {
            $sql .= " WHERE activo = 1";
        }
        $sql .= " ORDER BY puntos_requeridos ASC";

        return $this->consultarTodos($sql);
    }

    /**
     * Obtiene una recompensa por su ID.
     *
     * @param  int $id
     * @return array|false
     */
    public function obtenerRecompensaPorId(int $id): array|false
    {
        return $this->consultarUno(
            "SELECT * FROM recompensas_puntos WHERE id = :id",
            [':id' => $id]
        );
    }

    /**
     * Crea una nueva recompensa en el catálogo.
     *
     * @param  string $nombre
     * @param  string $descripcion
     * @param  int    $puntosRequeridos
     * @param  bool   $activo
     * @return int    ID insertado (0 si falló)
     */
    public function crearRecompensa(string $nombre, string $descripcion, int $puntosRequeridos, bool $activo): int
    {
        $sql = "INSERT INTO recompensas_puntos (nombre, descripcion, puntos_requeridos, activo)
                VALUES (:nombre, :descripcion, :puntos, :activo)";

        return $this->insertar($sql, [
            ':nombre'      => $nombre,
            ':descripcion' => $descripcion,
            ':puntos'      => $puntosRequeridos,
            ':activo'      => $activo ? 1 : 0,
        ]);
    }

    /**
     * Actualiza una recompensa existente.
     *
     * @param  int    $id
     * @param  string $nombre
     * @param  string $descripcion
     * @param  int    $puntosRequeridos
     * @param  bool   $activo
     * @return bool
     */
    public function actualizarRecompensa(int $id, string $nombre, string $descripcion, int $puntosRequeridos, bool $activo): bool
    {
        $sql = "UPDATE recompensas_puntos
                SET nombre = :nombre, descripcion = :descripcion,
                    puntos_requeridos = :puntos, activo = :activo
                WHERE id = :id";

        return $this->ejecutar($sql, [
            ':nombre'      => $nombre,
            ':descripcion' => $descripcion,
            ':puntos'      => $puntosRequeridos,
            ':activo'      => $activo ? 1 : 0,
            ':id'          => $id,
        ]) >= 0;
    }

    /**
     * Elimina una recompensa del catálogo.
     *
     * @param  int $id
     * @return bool
     */
    public function eliminarRecompensa(int $id): bool
    {
        return $this->ejecutar(
            "DELETE FROM recompensas_puntos WHERE id = :id",
            [':id' => $id]
        ) >= 0;
    }
}
