<?php
/**
 * public/index.php
 * Front Controller de Bartek.
 * Punto de entrada único de la aplicación.
 * Carga la configuración, inicia sesión y enruta las solicitudes
 * al controlador y método correspondiente.
 *
 * @package Bartek
 */

declare(strict_types=1);

// ── Carga de configuración ────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/sesion.php';
require_once dirname(__DIR__) . '/config/setting.php';

// ── Composer Autoload ───────────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/vendor/autoload.php';

// ── Clases base (deben cargarse primero) ──────────────────────────────────────
require_once dirname(__DIR__) . '/app/models/BaseModel.php';
require_once dirname(__DIR__) . '/app/controllers/BaseController.php';

// ── Carga de Modelos ──────────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/app/models/UsuarioModel.php';
require_once dirname(__DIR__) . '/app/models/EmpleadoModel.php';
require_once dirname(__DIR__) . '/app/models/InventarioModel.php';
require_once dirname(__DIR__) . '/app/models/VentaModel.php';
require_once dirname(__DIR__) . '/app/models/HorarioModel.php';
require_once dirname(__DIR__) . '/app/models/MenuModel.php';
require_once dirname(__DIR__) . '/app/models/NotificacionModel.php';
require_once dirname(__DIR__) . '/app/models/ContactoModel.php';
require_once dirname(__DIR__) . '/app/models/PuntosModel.php';

// ── Carga de Controladores ────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/app/controllers/AuthController.php';
require_once dirname(__DIR__) . '/app/controllers/DashboardController.php';
require_once dirname(__DIR__) . '/app/controllers/EmpleadoController.php';
require_once dirname(__DIR__) . '/app/controllers/InventarioController.php';
require_once dirname(__DIR__) . '/app/controllers/VentaController.php';
require_once dirname(__DIR__) . '/app/controllers/HorarioController.php';
require_once dirname(__DIR__) . '/app/controllers/MenuController.php';
require_once dirname(__DIR__) . '/app/controllers/ContactoController.php';
require_once dirname(__DIR__) . '/app/controllers/PerfilController.php';
require_once dirname(__DIR__) . '/app/controllers/PuntosController.php';
require_once dirname(__DIR__) . '/app/controllers/ReporteController.php';

// ── Inicio de sesión segura ───────────────────────────────────────────────────
iniciarSesion();

// ── Enrutamiento ──────────────────────────────────────────────────────────────
/**
 * Se extrae la ruta de la URL eliminando el prefijo BASE_URL.
 * Ejemplo: /bartek/public/empleados/crear → /empleados/crear
 */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace(BASE_URL, '', $uri);
$uri = trim($uri, '/');

// Separar ruta en segmentos: [controlador, accion, parametro]
$segmentos  = explode('/', $uri);
$controlador = $segmentos[0] ?? '';
$accion      = $segmentos[1] ?? 'index';
$parametro   = $segmentos[2] ?? null;

// Obtener la ruta limpia completa (por ejemplo: puntos/guardar)
$rutaCompleta = trim($uri, '/');

// ── Tabla de enrutamiento ─────────────────────────────────────────────────────
$rutas = [
    ''                 => ['AuthController', 'index'],
    'login'            => ['AuthController', 'login'],
    'registro'         => ['AuthController', 'registro'],
    'logout'           => ['AuthController', 'logout'],
    'dashboard'        => ['DashboardController', 'index'],
    'empleados'        => ['EmpleadoController', $accion ?? 'index'],
    'inventario'       => ['InventarioController', $accion ?? 'index'],
    'ventas'           => ['VentaController', $accion ?? 'index'],
    'horarios'         => ['HorarioController', $accion ?? 'index'],
    'menu'             => ['MenuController', $accion ?? 'index'],
    'contacto'         => ['ContactoController', $accion ?? 'index'],
    'nosotros'         => ['AuthController', 'nosotros'],
    'servicios'        => ['AuthController', 'servicios'],
    'perfil'           => ['PerfilController', $accion ?? 'index'],
    'reportes'         => ['ReporteController', $accion ?? 'index'],
    'puntos'           => ['PuntosController', 'index'],
    'puntos/guardar'   => ['PuntosController', 'guardar'],
    'puntos/buscar'    => ['PuntosController', 'buscarCliente'],
    'puntos/listado'   => ['PuntosController', 'listado'],
    'puntos/actualizar'=> ['PuntosController', 'actualizar'],
    'puntos/eliminar' => ['PuntosController', 'eliminar'],
    'puntos/editar'    => ['PuntosController', 'editar'],
    ];

// ── Ejecución de la ruta encontrada ───────────────────────────────────────────
$clase = null;
$metodo = null;

if (array_key_exists($rutaCompleta, $rutas)) {
    [$clase, $metodo] = $rutas[$rutaCompleta];
} elseif ($controlador === 'ventas' && $accion === 'mesa' && $parametro !== null) {
    $clase = 'VentaController';
    $metodo = 'mesa';
} elseif ($controlador === 'puntos' && $accion === 'editar' && $parametro !== null) {
    //  NUEVA CONDICIÓN: Permite que /puntos/editar/{id} viaje con su parámetro numérico
    $clase = 'PuntosController';
    $metodo = 'editar';
//Permite eliminar puntos 
    $clase = 'PuntosController';
    $metodo = 'eliminar';

} else {
    $rutasControladores = [
        'empleados'  => 'EmpleadoController',
        'inventario' => 'InventarioController',
        'ventas'     => 'VentaController',
        'horarios'   => 'HorarioController',
        'menu'       => 'MenuController',
        'contacto'   => 'ContactoController',
        'perfil'     => 'PerfilController',
        'reportes'   => 'ReporteController'
    ];

    if (array_key_exists($controlador, $rutasControladores)) {
        $clase = $rutasControladores[$controlador];
        $metodo = $accion;
    }
}

//Pagina no encontrda
if ($clase && $metodo) {
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
        echo "Error interno: la clase $clase no fue encontrada.";
    }
} else {
    http_response_code(404);
    $ctrl = new AuthController();
    $ctrl->paginaNoEncontrada();
}