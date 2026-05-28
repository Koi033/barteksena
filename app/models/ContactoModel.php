<?php
/**
 * app/models/ContactoModel.php
 * Modelo para almacenar mensajes del formulario de contacto público.
 *
 * @package Bartek\Models
 */
require_once BASE_PATH . '/app/models/BaseModel.php';

class ContactoModel extends BaseModel
{
    /**
     * Guarda un mensaje de contacto en la base de datos.
     * Los datos deben llegar saneados desde el controlador.
     *
     * @param  array $datos Datos validados: nombre, correo, mensaje
     * @return int          ID del mensaje insertado (0 en error)
     */
    public function guardar(array $datos): int
    {
        $sql = 'INSERT INTO contacto_mensajes (nombre, correo, mensaje)
                VALUES (:nombre, :correo, :mensaje)';
        return $this->insertar($sql, [
            ':nombre'  => $datos['nombre'],
            ':correo'  => $datos['correo'],
            ':mensaje' => $datos['mensaje'],
        ]);
    }

    /** Retorna todos los mensajes de contacto (para el panel admin). */
    public function obtenerTodos(): array
    {
        return $this->consultarTodos(
            'SELECT * FROM contacto_mensajes ORDER BY creado_en DESC'
        );
    }
}
