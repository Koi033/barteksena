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
    /**
     * Lista todas las mesas del bar, indicando cuáles están ocupadas.
     * GET /ventas/mesas
     *
     * @return void
     */
    public function mesas(): void
    {
        requerirAutenticacion();

        $mesasOcupadas = $this->modelo->obtenerMesasOcupadas(); // ej. ['1', '5']

        $mesas = [];
        for ($i = 1; $i <= TOTAL_MESAS; $i++) {
            $mesas[] = [
                'numero'  => $i,
                'ocupada' => in_array((string)$i, $mesasOcupadas, true),
            ];
        }

        $this->render('empleados/dashboard_mesas', [
            'titulo'        => 'Mesas - Bartek',
            'totalMesas'    => TOTAL_MESAS,
            'mesasOcupadas' => $mesasOcupadas,
        ]);
    }

    /**
     * Muestra la vista de detalle de una mesa específica con el inventario y cuenta actual.
     * GET /ventas/mesa/{numero}
     *
     * @param string|int $numeroMesa
     * @return void
     */
    public function mesaDetalle($numeroMesa): void
    {
        requerirAutenticacion();
        
        // 1. Buscar si la mesa ya tiene una venta abierta
        $venta = $this->modelo->obtenerVentaAbiertaPorMesa($numeroMesa);
        $detallesVenta = [];

        if ($venta) {
            // Si existe, obtener los productos que ya se le han agregado a la cuenta
            $detallesVenta = $this->modelo->obtenerDetallesVenta($venta['id']);
        }

        // 2. Obtener todo el inventario activo para mostrarlo en la tabla de la izquierda
        require_once BASE_PATH . '/app/models/InventarioModel.php';
        $inventarioModel = new InventarioModel();
        $inventario = $inventarioModel->obtenerActivos(); // Asegúrate de tener este método en tu modelo de inventario

        // 3. Renderizar la vista de detalle
        $this->render('empleados/mesa_detalle', [
            'titulo'        => 'Mesa ' . $numeroMesa . ' - Bartek',
            'numeroMesa'    => $numeroMesa,
            'venta'         => $venta,
            'detallesVenta' => $detallesVenta,
            'inventario'    => $inventario,
            'tokenCSRF'     => generarTokenCSRF('venta'),
            'flash'         => obtenerFlash(),
        ]);
    }

    /**
     * Guarda o actualiza los productos agregados a una mesa.
     * POST /ventas/guardar-detalle
     *
     * @return void
     */
    public function guardarDetalle(): void
    {
        requerirAutenticacion();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/ventas/mesas');
            return;
        }

        $mesa = $this->post('mesa', 2);
        $ventaId = $this->entero('venta_id', 'post');
        $productos = $_POST['productos'] ?? []; // Array con [inventario_id => ['cantidad' => X]]

        // Si no existe una venta abierta para esta mesa, la creamos primero
        if (!$ventaId) {
            // Obtenemos el ID del empleado logueado si aplica
            $empleadoId = $_SESSION['empleado_id'] ?? null; 
            
            $ventaId = $this->modelo->crear([
                'mesa'        => $mesa,
                'empleado_id' => $empleadoId,
                'estado'      => 'abierto'
            ]);
        }

        if ($ventaId) {
            try {
                // Sincronizar o guardar los ítems en detalle_ventas y recalcular el total
                $this->modelo->actualizarDetallesVenta($ventaId, $productos);
                flashMensaje('success', 'Cuenta de la mesa actualizada correctamente.');
            } catch (\Exception $e) {
                // Ej: stock insuficiente para alguno de los productos solicitados
                flashMensaje('error', $e->getMessage());
            }
        } else {
            flashMensaje('error', 'No se pudo abrir o actualizar la venta.');
        }

        $this->redirigir('/ventas/mesa/' . $mesa);
    }
    public function mesa($numeroMesa): void
    {
        requerirAutenticacion();
        
        // 1. Buscar si la mesa ya tiene una venta abierta
        $venta = $this->modelo->obtenerVentaAbiertaPorMesa($numeroMesa);
        $detallesVenta = [];

        if ($venta) {
            $detallesVenta = $this->modelo->obtenerDetallesVenta($venta['id']);
        }

        // 2. Obtener todo el inventario activo
        require_once BASE_PATH . '/app/models/InventarioModel.php';
        $inventarioModel = new InventarioModel();
        $inventario = $inventarioModel->obtenerTodos();

        // 3. Renderizar la vista
        $this->render('empleados/mesa_detalle', [
            'titulo'        => 'Mesa ' . $numeroMesa . ' - Bartek',
            'numeroMesa'    => $numeroMesa,
            'venta'         => $venta,
            'detallesVenta' => $detallesVenta,
            'inventario'    => $inventario,
            'tokenCSRF'     => generarTokenCSRF('venta'),
            'flash'         => obtenerFlash(),
        ]);
    }
}
