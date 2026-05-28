<?php
/**
 * app/controllers/DashboardController.php
 * Controlador del panel principal (dashboard) del sistema.
 * Muestra notificaciones, alertas de stock y resumen de actividad.
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
     * Muestra el panel principal con notificaciones y métricas del bar.
     * Requiere autenticación.
     *
     * @return void
     */
    public function index(): void
    {
        requerirAutenticacion();

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
