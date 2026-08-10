<?php
/**
 * index.php  ←  RAÍZ del proyecto (Front Controller)
 * ====================================================
 * Punto de entrada único de la aplicación Bartek.
 *
 * Estructura MVC correcta:
 *   BARTEK/
 *   ├── index.php          ← aquí (raíz)
 *   ├── .htaccess          ← aquí (raíz)
 *   ├── app/               ← controllers, models, views
 *   ├── config/
 *   ├── public/            ← solo assets: css/, js/, images/
 *   ├── setup/
 *   └── sql/
 *
 * @package Bartek
 */

declare(strict_types=1);

// BASE_PATH = directorio raíz del proyecto
define('BASE_PATH', __DIR__);

// ── Configuración ─────────────────────────────────────────────────────────────
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/config/sesion.php';
require_once BASE_PATH . '/config/setting.php';

// ── Composer Autoload ───────────────────────────────────────────────────────────
require_once BASE_PATH . '/vendor/autoload.php';

// ── Clases base ───────────────────────────────────────────────────────────────
require_once BASE_PATH . '/app/models/BaseModel.php';
require_once BASE_PATH . '/app/controllers/BaseController.php';

// ── Modelos ───────────────────────────────────────────────────────────────────
require_once BASE_PATH . '/app/models/UsuarioModel.php';
require_once BASE_PATH . '/app/models/EmpleadoModel.php';
require_once BASE_PATH . '/app/models/InventarioModel.php';
require_once BASE_PATH . '/app/models/ReservaModel.php';
require_once BASE_PATH . '/app/models/VentaModel.php';
require_once BASE_PATH . '/app/models/HorarioModel.php';
require_once BASE_PATH . '/app/models/MenuModel.php';
require_once BASE_PATH . '/app/models/NotificacionModel.php';
require_once BASE_PATH . '/app/models/ContactoModel.php';

// ── Controladores ─────────────────────────────────────────────────────────────
require_once BASE_PATH . '/app/controllers/AuthController.php';
require_once BASE_PATH . '/app/controllers/DashboardController.php';
require_once BASE_PATH . '/app/controllers/EmpleadoController.php';
require_once BASE_PATH . '/app/controllers/InventarioController.php';
require_once BASE_PATH . '/app/controllers/VentaController.php';
require_once BASE_PATH . '/app/controllers/ReservaController.php';
require_once BASE_PATH . '/app/controllers/HorarioController.php';
require_once BASE_PATH . '/app/controllers/MenuController.php';
require_once BASE_PATH . '/app/controllers/ContactoController.php';
require_once BASE_PATH . '/app/controllers/PerfilController.php';
require_once BASE_PATH . '/app/controllers/ReporteController.php';
require_once BASE_PATH . '/app/controllers/PasswordController.php';

// ── Sesión ────────────────────────────────────────────────────────────────────
iniciarSesion();

// ── Enrutamiento ──────────────────────────────────────────────────────────────
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace(BASE_URL, '', $uri);
$uri = trim($uri, '/');

$segmentos   = explode('/', $uri);
$controlador = $segmentos[0] ?? '';
$accion      = $segmentos[1] ?? 'index';
if (strpos($accion, '-') !== false) {
    $accion = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $accion))));
}
$parametro   = $segmentos[2] ?? null;

$rutas = [
    ''           => ['AuthController',       'index'],
    'login'      => ['AuthController',       'login'],
    'registro'   => ['AuthController',       'registro'],
    'logout'     => ['AuthController',       'logout'],
    'nosotros'   => ['AuthController',       'nosotros'],
    'servicios'  => ['AuthController',       'servicios'],
    'contacto'   => ['ContactoController',   $accion],
    'dashboard'  => ['DashboardController',  'index'],
    'empleados'  => ['EmpleadoController',   $accion],
    'inventario' => ['InventarioController', $accion],
    'ventas'     => ['VentaController',      $accion],
    'horarios'   => ['HorarioController',    $accion],
    'menu'       => ['MenuController',       $accion],
    'reservas'   => ['ReservaController',    $accion],
    'reportes'   => ['ReporteController',    $accion],
    'perfil'     => ['PerfilController',     $accion],
    'recuperar'  => ['PasswordController',  'recuperar'],
    'restablecer'=> ['PasswordController',  'restablecer'],
];

if (array_key_exists($controlador, $rutas)) {
    [$clase, $metodo] = $rutas[$controlador];
    if (class_exists($clase)) {
        $instancia = new $clase();
        if (method_exists($instancia, $metodo)) {
            $instancia->$metodo($parametro);
        } else {
            http_response_code(404);
            $instancia->paginaNoEncontrada();
        }
    } else {
        http_response_code(500);
        echo 'Error interno: controlador no encontrado.';
    }
} else {
    http_response_code(404);
    (new AuthController())->paginaNoEncontrada();
}
