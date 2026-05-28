<?php
/**
 * app/models/UsuarioModel.php
 * Modelo para la gestión de usuarios y autenticación.
 * Toda consulta usa prepared statements (PDO) para prevenir SQL Injection.
 *
 * @package Bartek\Models
 */

require_once BASE_PATH . '/app/models/BaseModel.php';

class UsuarioModel extends BaseModel
{
    /**
     * Busca un usuario por su nombre de usuario (login).
     *
     * @param  string      $usuario Nombre de usuario ingresado
     * @return array|false          Registro del usuario o false si no existe
     */
    public function buscarPorUsuario(string $usuario): array|false
    {
        $sql = 'SELECT id, nombre, apellido, email, usuario, contrasena, rol, activo
                FROM usuarios
                WHERE usuario = :usuario
                LIMIT 1';
        return $this->consultarUno($sql, [':usuario' => $usuario]);
    }

    /**
     * Busca un usuario por su correo electrónico.
     *
     * @param  string      $email Correo electrónico
     * @return array|false        Registro o false
     */
    public function buscarPorEmail(string $email): array|false
    {
        $sql = 'SELECT id, nombre, apellido, email, usuario, rol, activo
                FROM usuarios
                WHERE email = :email
                LIMIT 1';
        return $this->consultarUno($sql, [':email' => $email]);
    }

    /**
     * Registra un nuevo usuario en la base de datos.
     *
     * @param  array $datos Datos validados del formulario de registro
     * @return int          ID del nuevo usuario (0 en caso de error)
     */
    public function crear(array $datos): int
    {
        $sql = 'INSERT INTO usuarios (nombre, apellido, email, telefono, usuario, contrasena, rol)
                VALUES (:nombre, :apellido, :email, :telefono, :usuario, :contrasena, :rol)';
        return $this->insertar($sql, [
            ':nombre'     => $datos['nombre'],
            ':apellido'   => $datos['apellido'],
            ':email'      => $datos['email'],
            ':telefono'   => $datos['telefono']   ?? null,
            ':usuario'    => $datos['usuario'],
            ':contrasena' => $datos['contrasena'],
            ':rol'        => $datos['rol'],
        ]);
    }

    /**
     * Verifica si un nombre de usuario ya existe.
     *
     * @param  string $usuario
     * @return bool
     */
    public function existeUsuario(string $usuario): bool
    {
        $sql = 'SELECT COUNT(*) AS total FROM usuarios WHERE usuario = :usuario';
        $res = $this->consultarUno($sql, [':usuario' => $usuario]);
        return (int)($res['total'] ?? 0) > 0;
    }

    /**
     * Verifica si un email ya está registrado.
     *
     * @param  string $email
     * @return bool
     */
    public function existeEmail(string $email): bool
    {
        $sql = 'SELECT COUNT(*) AS total FROM usuarios WHERE email = :email';
        $res = $this->consultarUno($sql, [':email' => $email]);
        return (int)($res['total'] ?? 0) > 0;
    }

    // ── RECUPERACIÓN DE CONTRASEÑA ────────────────────────────────────────────

    /**
     * Guarda el token de recuperación y su fecha de expiración para un usuario.
     *
     * IMPORTANTE: Antes de ejecutar esto asegúrate de tener las columnas
     * en tu tabla usuarios:
     *   ALTER TABLE usuarios
     *     ADD COLUMN token_reset       VARCHAR(64)  NULL,
     *     ADD COLUMN token_expira      INT          NULL,
     *     ADD COLUMN solicitud_reset   TINYINT(1)   NOT NULL DEFAULT 0;
     *
     * @param  int    $userId    ID del usuario
     * @param  string $token     Token aleatorio (hex de 32 bytes)
     * @param  int    $expira    Timestamp Unix de expiración
     * @return bool              true si se actualizó correctamente
     */
    public function guardarTokenReset(int $userId, string $token, int $expira): bool
    {
        $sql = 'UPDATE usuarios
                SET token_reset     = :token,
                    token_expira    = :expira,
                    solicitud_reset = 1
                WHERE id = :id';
        return $this->ejecutar($sql, [
            ':token'  => $token,
            ':expira' => $expira,
            ':id'     => $userId,
        ]);
    }

    /**
     * Busca un usuario por token de reset válido (no expirado).
     *
     * @param  string      $token Token recibido por URL
     * @return array|false        Datos del usuario o false si inválido/expirado
     */
    public function buscarPorTokenReset(string $token): array|false
    {
        $ahora = time();
        $sql   = 'SELECT id, nombre, apellido, email, usuario
                  FROM usuarios
                  WHERE token_reset     = :token
                    AND solicitud_reset = 1
                    AND token_expira   > :ahora
                  LIMIT 1';
        return $this->consultarUno($sql, [
            ':token' => $token,
            ':ahora' => $ahora,
        ]);
    }

    /**
     * Actualiza la contraseña del usuario y limpia los campos de reset.
     *
     * @param  int    $userId      ID del usuario
     * @param  string $hashNuevo   Contraseña ya hasheada con bcrypt
     * @return bool
     */
    public function actualizarContrasena(int $userId, string $hashNuevo): bool
    {
        $sql = 'UPDATE usuarios
                SET contrasena      = :contrasena,
                    token_reset     = NULL,
                    token_expira    = NULL,
                    solicitud_reset = 0
                WHERE id = :id';
        return $this->ejecutar($sql, [
            ':contrasena' => $hashNuevo,
            ':id'         => $userId,
        ]);
    }
}
