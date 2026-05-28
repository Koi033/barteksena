<?php
/**
 * app/models/NotificacionModel.php
 * Modelo para la gestión de notificaciones internas del sistema.
 *
 * @package Bartek\Models
 */
require_once BASE_PATH . '/app/models/BaseModel.php';

class NotificacionModel extends BaseModel
{
    /**
     * Retorna las últimas notificaciones con paginación.
     *
     * @param  int $pagina    Página actual
     * @param  int $porPagina Items por página
     * @return array
     */
    public function obtenerTodas(int $pagina = 1, int $porPagina = ITEMS_POR_PAGINA): array
    {
        $offset = ($pagina - 1) * $porPagina;
        $sql    = 'SELECT id, tipo, titulo, descripcion, leida, creado_en
                   FROM notificaciones
                   ORDER BY creado_en DESC
                   LIMIT :limite OFFSET :offset';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,    PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Cuenta notificaciones no leídas. */
    public function contarNoLeidas(): int
    {
        $res = $this->consultarUno('SELECT COUNT(*) AS total FROM notificaciones WHERE leida = 0');
        return (int)($res['total'] ?? 0);
    }

    /** Cuenta el total de notificaciones (para paginación). */
    public function contarTotal(): int
    {
        $res = $this->consultarUno('SELECT COUNT(*) AS total FROM notificaciones');
        return (int)($res['total'] ?? 0);
    }

    /** Marca una notificación como leída. */
    public function marcarLeida(int $id): int
    {
        return $this->ejecutar('UPDATE notificaciones SET leida = 1 WHERE id = :id', [':id' => $id]);
    }

    /** Elimina una notificación de la base de datos. */
    public function eliminar(int $id): int
    {
        return $this->ejecutar('DELETE FROM notificaciones WHERE id = :id', [':id' => $id]);
    }
}
