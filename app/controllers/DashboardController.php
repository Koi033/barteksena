<?php
/**
 * app/controllers/DashboardController.php
 * Controlador del panel principal (dashboard) del sistema.
 * Enruta la vista dependiendo del rol (Dueño o Empleado).
 *
 * @package Bartek\Controllers
 */
require_once BASE_PATH . '/app/controllers/BaseController.php';

class DashboardController extends BaseController
{
    private NotificacionModel $notifModel;
    private InventarioModel   $invModel;
    private VentaModel        $ventaModel;

    public function __construct()
    {
        $this->notifModel = new NotificacionModel();
        $this->invModel   = new InventarioModel();
        $this->ventaModel = new VentaModel();
    }

    /**
     * Punto de entrada principal. Enruta según el rol del usuario.
     *
     * @return void
     */
    public function index(): void
    {
        requerirAutenticacion();

        $rol = $_SESSION['usuario_rol'] ?? '';

        if ($rol === 'empleado') {
            $this->dashboardEmpleado();
        } else {
            // Por defecto, renderiza la vista administrativa (dueño)
            $this->dashboardDueno();
        }
    }

    /**
     * Prepara y muestra el dashboard específico para los empleados (Mesas).
     *
     * @return void
     */
    private function dashboardEmpleado(): void
    {
        // Obtiene un arreglo con los números de mesa ocupadas (ej: ['1', '4'])
        $mesasOcupadas = $this->ventaModel->obtenerMesasOcupadas();
        
        $this->render('mesas/dashboard_mesas', [
            'titulo'        => 'Control de Mesas - Bartek',
            'mesasOcupadas' => $mesasOcupadas,
            'totalMesas'    => 12, // Puedes cambiar este número según la capacidad de tu bar
            'flash'         => obtenerFlash(),
        ]);
    }

    /**
     * Muestra el panel principal con notificaciones y métricas del bar (Dueño).
     * (Este es tu código original del index)
     *
     * @return void
     */
    private function dashboardDueno(): void
    {
        $pagina        = max(1, $this->entero('pagina', 'get', 1));
        $notificaciones = $this->notifModel->obtenerTodas($pagina);
        $totalNotif    = $this->notifModel->contarTotal();
        $noLeidas      = $this->notifModel->contarNoLeidas();
        $stockBajo     = $this->invModel->obtenerStockBajo();
        $ventasHoy     = $this->ventaModel->totalHoy();
        $ventasMes     = $this->ventaModel->totalMes();
        $flash         = obtenerFlash();

        $this->render('dashboard/index', [
            'titulo'         => 'Dashboard - Bartek',
            'notificaciones' => $notificaciones,
            'totalNotif'     => $totalNotif,
            'noLeidas'       => $noLeidas,
            'stockBajo'      => $stockBajo,
            'ventasHoy'      => $ventasHoy,
            'ventasMes'      => $ventasMes,
            'paginaActual'   => $pagina,
            'totalPaginas'   => (int) ceil($totalNotif / ITEMS_POR_PAGINA),
            'flash'          => $flash,
        ]);
    }

    /**
     * Elimina una notificación.
     * POST /dashboard/eliminar
     *
     * @return void
     */
    public function eliminar(): void
    {
        requerirAutenticacion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/dashboard');
        }

        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'eliminar_notif')) {
            flashMensaje('error', 'Token inválido.');
            $this->redirigir('/dashboard');
            return;
        }

        $id = $this->entero('id', 'post');
        $filas = $this->notifModel->eliminar($id);
        flashMensaje($filas > 0 ? 'success' : 'error',
                     $filas > 0 ? 'Notificación eliminada.' : 'No se pudo eliminar la notificación.');
        $this->redirigir('/dashboard');
    }
}