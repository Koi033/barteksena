<?php
/**
 * config/app.php
 * Constantes globales de la aplicación Bartek.
 *
 * @package Bartek
 */

// ── Entorno ───────────────────────────────────────────────────────────────────
define('APP_ENV',     getenv('APP_ENV')  ?: 'development');
define('APP_NAME',    'Bartek');
define('APP_VERSION', '1.0.0');

// ── Ruta base URL ─────────────────────────────────────────────────────────────
// BASE_URL = subcarpeta donde está el proyecto en el servidor.
// Ejemplos:
//   XAMPP en htdocs/bartek  → '/bartek'
//   Servidor en raíz        → ''  (cadena vacía)
define('BASE_URL', '/bartek');

// ── BASE_PATH ─────────────────────────────────────────────────────────────────
// Se define en index.php como __DIR__ antes de cargar este archivo.
// Solo lo definimos aquí si por alguna razón no viene definido aún.
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// ── Carpeta de assets públicos ────────────────────────────────────────────────
// La carpeta public/ solo contiene css/, js/, images/
define('PUBLIC_URL', BASE_URL . '/public');

// ── Sesión ────────────────────────────────────────────────────────────────────
define('SESSION_NAME',     'BARTEK_SESS');
define('SESSION_LIFETIME', 7200);

// ── Seguridad ─────────────────────────────────────────────────────────────────
define('BCRYPT_COST',   12);
define('TOKEN_LENGTH',  32);

// ── Paginación ────────────────────────────────────────────────────────────────
define('ITEMS_POR_PAGINA', 10);

// ── Mostrar errores en desarrollo ─────────────────────────────────────────────
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
define('TOTAL_MESAS', 10); // Cambia este número según la cantidad real de mesas del bar
