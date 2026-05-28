<?php
/**
 * config/sesion.php
 * Gestión centralizada de sesiones PHP.
 * Inicia la sesión con parámetros seguros y proporciona
 * funciones para autenticación y tokens CSRF.
 *
 * @package Bartek
 */

/**
 * Inicia la sesión con configuración segura.
 * Debe llamarse antes de cualquier salida HTML.
 *
 * @return void
 */
function iniciarSesion(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        // Configurar parámetros antes de session_start()
        session_name(SESSION_NAME);

        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'domain'   => '',           // Dominio actual
            'secure'   => false,        // true en HTTPS (producción)
            'httponly' => true,         // Inaccesible desde JavaScript
            'samesite' => 'Lax',        // Protección CSRF básica
        ]);

        session_start();

        // Regenerar ID para prevenir fijación de sesión
        if (empty($_SESSION['_iniciada'])) {
            session_regenerate_id(true);
            $_SESSION['_iniciada'] = true;
        }
    }
}

/**
 * Cierra la sesión del usuario destruyendo todos los datos.
 * Elimina la cookie de sesión del navegador.
 *
 * @return void
 */
function cerrarSesion(): void
{
    iniciarSesion();

    // Vaciar todas las variables de sesión
    $_SESSION = [];

    // Eliminar la cookie de sesión del cliente
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    // Destruir la sesión en el servidor
    session_destroy();
}

/**
 * Verifica si existe un usuario autenticado en sesión.
 *
 * @return bool true si el usuario está logueado
 */
function estaAutenticado(): bool
{
    iniciarSesion();
    return !empty($_SESSION['usuario_id']) && !empty($_SESSION['usuario_rol']);
}

/**
 * Redirige al login si el usuario no está autenticado.
 * Útil al inicio de cada controlador protegido.
 *
 * @return void
 */
function requerirAutenticacion(): void
{
    if (!estaAutenticado()) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}

/**
 * Genera y almacena un token CSRF en sesión para el formulario indicado.
 *
 * @param  string $formulario Identificador del formulario
 * @return string             Token hexadecimal generado
 */
function generarTokenCSRF(string $formulario = 'default'): string
{
    iniciarSesion();
    $token = bin2hex(random_bytes(TOKEN_LENGTH));
    $_SESSION['csrf'][$formulario] = $token;
    return $token;
}

/**
 * Valida el token CSRF enviado por el formulario.
 *
 * @param  string $token      Token recibido del formulario
 * @param  string $formulario Identificador del formulario
 * @return bool               true si el token es válido
 */
function validarTokenCSRF(string $token, string $formulario = 'default'): bool
{
    iniciarSesion();
    $tokenGuardado = $_SESSION['csrf'][$formulario] ?? '';
    // hash_equals previene ataques de temporización
    return hash_equals($tokenGuardado, $token);
}

/**
 * Guarda un mensaje flash en sesión para mostrarlo en la próxima carga.
 *
 * @param  string $tipo    'success' | 'error' | 'warning' | 'info'
 * @param  string $mensaje Texto del mensaje
 * @return void
 */
function flashMensaje(string $tipo, string $mensaje): void
{
    iniciarSesion();
    $_SESSION['flash'][] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

/**
 * Obtiene y limpia los mensajes flash almacenados en sesión.
 *
 * @return array Lista de mensajes flash
 */
function obtenerFlash(): array
{
    iniciarSesion();
    $mensajes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $mensajes;
}
