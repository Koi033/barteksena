<?php
/**
 * app/models/ConfiguracionPuntosModel.php
 * Gestiona la configuración general de acumulación de puntos
 * (dinero/productos necesarios para ganar puntos) y el catálogo
 * de recompensas canjeables por puntos.
 *
 * Cada recompensa tiene un `tipo` que determina cómo se aplica
 * automáticamente al momento de canjearla desde la vista de mesa:
 *   - 'descuento_categoria': resta un % del subtotal consumido en
 *      una categoría específica del menú.
 *   - 'producto_gratis': agrega un producto puntual con precio $0
 *      a la cuenta.
 *
 * @package Bartek\Models
 */
require_once BASE_PATH . '/app/models/BaseModel.php';

class ConfiguracionPuntosModel extends BaseModel
{
    /** Tipos de recompensa soportados por el sistema. */
    public const TIPOS_RECOMPENSA = ['descuento_categoria', 'producto_gratis'];

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
            ':modo'       => $modo,
            ':valor'      => $valorAcumulacion,
            ':otorgados'  => $puntosOtorgados,
            ':bienvenida' => $puntosBienvenida,
        ]) >= 0;
    }

    /**
     * Lista las recompensas del catálogo, incluyendo el nombre de la
     * categoría o del producto asociado cuando aplica (útil para mostrar
     * el detalle sin hacer consultas adicionales en la vista).
     *
     * @param  bool $soloActivas Si es true, solo trae las recompensas activas
     * @return array
     */
    public function obtenerRecompensas(bool $soloActivas = false): array
    {
        $sql = "SELECT r.*,
                       c.nombre AS categoria_nombre,
                       i.nombre AS producto_nombre
                FROM recompensas_puntos r
                LEFT JOIN categorias_menu c ON c.id = r.categoria_id
                LEFT JOIN inventario i      ON i.id = r.producto_id";

        if ($soloActivas) {
            $sql .= " WHERE r.activo = 1";
        }
        $sql .= " ORDER BY r.puntos_requeridos ASC";

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
     * Crea una nueva recompensa en el catálogo. La descripción se genera
     * automáticamente a partir del tipo, por lo que el administrador no
     * necesita redactarla a mano.
     *
     * @param  string     $nombre
     * @param  string     $tipo                 'descuento_categoria' | 'producto_gratis'
     * @param  int|null   $categoriaId          Requerido si tipo = descuento_categoria
     * @param  float|null $porcentajeDescuento  Requerido si tipo = descuento_categoria
     * @param  int|null   $productoId           Requerido si tipo = producto_gratis
     * @param  int        $puntosRequeridos
     * @param  bool       $activo
     * @return int        ID insertado (0 si falló)
     */
    public function crearRecompensa(
        string $nombre,
        string $tipo,
        ?int $categoriaId,
        ?float $porcentajeDescuento,
        ?int $productoId,
        int $puntosRequeridos,
        bool $activo
    ): int {
        $sql = "INSERT INTO recompensas_puntos
                    (nombre, tipo, categoria_id, porcentaje_descuento, producto_id, descripcion, puntos_requeridos, activo)
                VALUES
                    (:nombre, :tipo, :categoria_id, :porcentaje, :producto_id, :descripcion, :puntos, :activo)";

        return $this->insertar($sql, [
            ':nombre'       => $nombre,
            ':tipo'         => $tipo,
            ':categoria_id' => $categoriaId,
            ':porcentaje'   => $porcentajeDescuento,
            ':producto_id'  => $productoId,
            ':descripcion'  => $this->generarDescripcion($tipo, $categoriaId, $porcentajeDescuento, $productoId),
            ':puntos'       => $puntosRequeridos,
            ':activo'       => $activo ? 1 : 0,
        ]);
    }

    /**
     * Actualiza una recompensa existente. La descripción se regenera
     * automáticamente igual que en crearRecompensa().
     *
     * @param  int        $id
     * @param  string     $nombre
     * @param  string     $tipo
     * @param  int|null   $categoriaId
     * @param  float|null $porcentajeDescuento
     * @param  int|null   $productoId
     * @param  int        $puntosRequeridos
     * @param  bool       $activo
     * @return bool
     */
    public function actualizarRecompensa(
        int $id,
        string $nombre,
        string $tipo,
        ?int $categoriaId,
        ?float $porcentajeDescuento,
        ?int $productoId,
        int $puntosRequeridos,
        bool $activo
    ): bool {
        $sql = "UPDATE recompensas_puntos
                SET nombre = :nombre,
                    tipo = :tipo,
                    categoria_id = :categoria_id,
                    porcentaje_descuento = :porcentaje,
                    producto_id = :producto_id,
                    descripcion = :descripcion,
                    puntos_requeridos = :puntos,
                    activo = :activo
                WHERE id = :id";

        return $this->ejecutar($sql, [
            ':nombre'       => $nombre,
            ':tipo'         => $tipo,
            ':categoria_id' => $categoriaId,
            ':porcentaje'   => $porcentajeDescuento,
            ':producto_id'  => $productoId,
            ':descripcion'  => $this->generarDescripcion($tipo, $categoriaId, $porcentajeDescuento, $productoId),
            ':puntos'       => $puntosRequeridos,
            ':activo'       => $activo ? 1 : 0,
            ':id'           => $id,
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

    /**
     * Construye una descripción legible automáticamente a partir del tipo
     * de recompensa, para que el administrador nunca tenga que escribirla
     * manualmente ni pueda dejarla inconsistente con el tipo elegido.
     *
     * @param  string     $tipo
     * @param  int|null   $categoriaId
     * @param  float|null $porcentaje
     * @param  int|null   $productoId
     * @return string
     */
    private function generarDescripcion(
        string $tipo,
        ?int $categoriaId,
        ?float $porcentaje,
        ?int $productoId
    ): string {
        if ($tipo === 'descuento_categoria') {
            $cat = $categoriaId
                ? $this->consultarUno('SELECT nombre FROM categorias_menu WHERE id = :id', [':id' => $categoriaId])
                : false;

            $porcentajeTexto = rtrim(rtrim(number_format((float) $porcentaje, 2), '0'), '.');
            return sprintf('%s%% de descuento en %s', $porcentajeTexto, $cat['nombre'] ?? 'la categoría seleccionada');
        }

        $producto = $productoId
            ? $this->consultarUno('SELECT nombre FROM inventario WHERE id = :id', [':id' => $productoId])
            : false;

        return 'Producto gratis: ' . ($producto['nombre'] ?? 'producto seleccionado');
    }
}
