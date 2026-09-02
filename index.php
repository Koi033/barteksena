<?php
/**
 * index.php  ←  RAÍZ del proyecto (Front Controller ÚNICO)
 * ====================================================
 * Punto de entrada único de la aplicación Bartek.
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
require_once BASE_PATH . '/app/models/PuntosModel.php';

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
require_once BASE_PATH . '/app/controllers/PuntosController.php';

// ── Sesión ────────────────────────────────────────────────────────────────────
iniciarSesion();

// ── Enrutamiento ──────────────────────────────────────────────────────────────
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$uri = parse_url($requestUri, PHP_URL_PATH) ?: '';
$uri = str_replace(BASE_URL, '', $uri);
$uri = trim($uri, '/');

$segmentos   = explode('/', $uri);
$controlador = $segmentos[0] ?? '';
$accion      = $segmentos[1] ?? 'index';
if (strpos($accion, '-') !== false) {
    $accion = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $accion))));
}
$parametro   = $segmentos[2] ?? null;

// Ruta completa limpia, para rutas compuestas tipo "puntos/guardar"
$rutaCompleta = $uri;

$rutas = [
    ''             => ['AuthController',       'index'],
    'login'        => ['AuthController',       'login'],
    'registro'     => ['AuthController',       'registro'],
    'logout'       => ['AuthController',       'logout'],
    'nosotros'     => ['AuthController',       'nosotros'],
    'servicios'    => ['AuthController',       'servicios'],
    'contacto'     => ['ContactoController',   $accion],
    'dashboard'    => ['DashboardController',  'index'],
    'empleados'    => ['EmpleadoController',   $accion],
    'inventario'   => ['InventarioController', $accion],
    'ventas'       => ['VentaController',      $accion],
    'horarios'     => ['HorarioController',    $accion],
    'menu'         => ['MenuController',       $accion],
    'reservas'     => ['ReservaController',    $accion],
    'reportes'     => ['ReporteController',    $accion],
    'perfil'       => ['PerfilController',     $accion],
    'recuperar'    => ['PasswordController',   'recuperar'],
    'restablecer'  => ['PasswordController',   'restablecer'],

    // Rutas de puntos (coincidencia por ruta completa, ver abajo)
    'puntos'            => ['PuntosController', 'index'],
    'puntos/guardar'    => ['PuntosController', 'guardar'],
    'puntos/buscar'     => ['PuntosController', 'buscarCliente'],
    'puntos/listado'    => ['PuntosController', 'listado'],
    'puntos/actualizar' => ['PuntosController', 'actualizar'],
    'puntos/eliminar'   => ['PuntosController', 'eliminar'],
    'puntos/editar'     => ['PuntosController', 'editar'],
    // Registro público: cualquier cliente puede inscribirse sin iniciar sesión
    'puntos/registro'   => ['PuntosController', 'registroPublico'],
    'error/400'    => ['BaseController', 'solicitudIncorrecta'],
    'error/401'    => ['BaseController', 'noAutenticado'],
    'error/403'    => ['BaseController', 'accesoDenegado'],
    'error/404'    => ['BaseController', 'paginaNoEncontrada'],
    'error/500'    => ['BaseController', 'errorInterno'],
    'error/503'    => ['BaseController', 'servicioNoDisponible'],
];

// ── Ejecución de la ruta ────────────────────────────────────────────────────
$clase = null;
$metodo = null;

if (array_key_exists($rutaCompleta, $rutas)) {
    // Coincidencia exacta por ruta completa (cubre "puntos/guardar", etc.)
    [$clase, $metodo] = $rutas[$rutaCompleta];
} elseif (array_key_exists($controlador, $rutas)) {
    // Coincidencia por primer segmento (comportamiento original)
    [$clase, $metodo] = $rutas[$controlador];
} elseif ($controlador === 'ventas' && $accion === 'mesa' && $parametro !== null) {
    $clase  = 'VentaController';
    $metodo = 'mesa';
} elseif ($controlador === 'puntos' && $accion === 'editar' && $parametro !== null) {
    $clase  = 'PuntosController';
    $metodo = 'editar';
} elseif ($controlador === 'puntos' && $accion === 'eliminar' && $parametro !== null) {
    $clase  = 'PuntosController';
    $metodo = 'eliminar';
}

// TODO: verificar el método correcto para servir el menú público standalone
// (public/publico.php). Antes vivía como ['MenuController'] sin método,
// lo cual generaba un error. Ajusta 'metodoReal' al nombre real en
// MenuController, o cambia este bloque para incluir publico.php directamente.
if ($rutaCompleta === 'menu-publico') {
    require BASE_PATH . '/public/publico.php';
    exit;
}

if ($clase && $metodo) {
    if (class_exists($clase)) {
        $instancia = new $clase();
        if (method_exists($instancia, $metodo)) {
            // Solo se pasa $parametro si realmente viene en la URL.
            // Evita pasar null explícito a métodos con parámetros
            // tipados como string no-nullable (rompería con TypeError
            // aunque el método tenga un valor por defecto).
            if ($parametro !== null) {
                $instancia->$metodo($parametro);
            } else {
                $instancia->$metodo();
            }
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
