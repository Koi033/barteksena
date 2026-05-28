<?php
/**
 * app/controllers/VentaController.php
 * Gestión de ventas y transacciones del bar.
 *
 * @package Bartek\Controllers
 */
require_once BASE_PATH . '/app/controllers/BaseController.php';

class VentaController extends BaseController
{
    private VentaModel $modelo;

    public function __construct()
    {
        $this->modelo = new VentaModel();
    }

    /**
     * Vista principal de ventas con métricas y tabla de transacciones.
     * GET /ventas
     *
     * @return void
     */
    public function index(): void
    {
        requerirAutenticacion();
        $pagina   = max(1, $this->entero('pagina', 'get', 1));
        $ventas   = $this->modelo->obtenerTodos($pagina);
        $total    = $this->modelo->contarTotal();
        $topBeb   = $this->modelo->topBebidas();

        $this->render('ventas/index', [
            'titulo'        => 'Ventas - Bartek',
            'ventas'        => $ventas,
            'totalVentas'   => $total,
            'ventasHoy'     => $this->modelo->totalHoy(),
            'ventasMes'     => $this->modelo->totalMes(),
            'topBebidas'    => $topBeb,
            'paginaActual'  => $pagina,
            'totalPaginas'  => (int) ceil($total / ITEMS_POR_PAGINA),
            'tokenCSRF'     => generarTokenCSRF('venta'),
            'flash'         => obtenerFlash(),
        ]);
    }

    /**
     * Crea una nueva venta (apertura de mesa).
     * POST /ventas/guardar
     *
     * @return void
     */
    public function guardar(): void
    {
        requerirAutenticacion();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/ventas');
        }
        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'venta')) {
            flashMensaje('error', 'Token inválido.');
            $this->redirigir('/ventas');
            return;
        }

        $id = $this->modelo->crear([
            'mesa'        => $this->post('mesa', 30),
            'empleado_id' => $this->entero('empleado_id', 'post') ?: null,
        ]);

        flashMensaje($id > 0 ? 'success' : 'error',
                     $id > 0 ? 'Venta abierta correctamente.' : 'Error al abrir venta.');
        $this->redirigir('/ventas');
    }

    /**
     * Cierra una venta activa.
     * POST /ventas/cerrar
     *
     * @return void
     */
    public function cerrar(): void
    {
        requerirAutenticacion();
        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'venta')) {
            flashMensaje('error', 'Token inválido.');
            $this->redirigir('/ventas');
            return;
        }

        $id = $this->entero('id', 'post');
        $this->modelo->cerrar($id);
        flashMensaje('success', 'Venta cerrada exitosamente.');
        $this->redirigir('/ventas');
    }
}
