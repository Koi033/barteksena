<?php
/**
 * app/controllers/InventarioController.php
 * CRUD para la gestión del inventario de bebidas.
 *
 * @package Bartek\Controllers
 */
require_once BASE_PATH . '/app/controllers/BaseController.php';

class InventarioController extends BaseController
{
    private InventarioModel $modelo;
    private MenuModel       $menuModel;

    public function __construct()
    {
        $this->modelo    = new InventarioModel();
        $this->menuModel = new MenuModel();
    }

    /**
     * Lista las bebidas del inventario con búsqueda y paginación.
     * GET /inventario
     *
     * @return void
     */
    public function index(): void
    {
        requerirAutenticacion();

        $pagina   = max(1, $this->entero('pagina', 'get', 1));
        $busqueda = $this->get('busqueda');
        $catId    = $this->entero('categoria', 'get', 0);

        $bebidas     = $this->modelo->obtenerTodos($busqueda, $catId, $pagina);
        $total       = $this->modelo->contarFiltrados($busqueda, $catId);
        $categorias  = $this->menuModel->obtenerCategorias();
        $stockBajo   = $this->modelo->obtenerStockBajo();

        $this->render('inventario/index', [
            'titulo'       => 'Inventario - Bartek',
            'bebidas'      => $bebidas,
            'categorias'   => $categorias,
            'stockBajo'    => $stockBajo,
            'busqueda'     => $busqueda,
            'catFiltro'    => $catId,
            'paginaActual' => $pagina,
            'totalPaginas' => (int) ceil($total / ITEMS_POR_PAGINA),
            'total'        => $total,
            'tokenCSRF'    => generarTokenCSRF('eliminar_inv'),
            'flash'        => obtenerFlash(),
        ]);
    }

    /**
     * Muestra formulario para agregar bebida.
     * GET /inventario/crear
     *
     * @return void
     */
    public function crear(): void
    {
        requerirAutenticacion();
        $this->render('inventario/formulario', [
            'titulo'     => 'Agregar Bebida',
            'bebida'     => null,
            'categorias' => $this->menuModel->obtenerCategorias(),
            'tokenCSRF'  => generarTokenCSRF('inventario'),
            'flash'      => obtenerFlash(),
        ]);
    }

    /**
     * Persiste una nueva bebida en el inventario.
     * POST /inventario/guardar
     *
     * @return void
     */
    public function guardar(): void
    {
        requerirAutenticacion();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/inventario');
        }

        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'inventario')) {
            flashMensaje('error', 'Token inválido.');
            $this->redirigir('/inventario/crear');
            return;
        }

        $datos   = $this->extraerDatos();
        $errores = $this->validar($datos);
        if (!empty($errores)) {
            foreach ($errores as $e) flashMensaje('error', $e);
            $this->redirigir('/inventario/crear');
            return;
        }

        $id = $this->modelo->crear($datos);
        flashMensaje($id > 0 ? 'success' : 'error',
                     $id > 0 ? 'Bebida agregada al inventario.' : 'Error al guardar.');
        $this->redirigir('/inventario');
    }

    /**
     * Formulario de edición de bebida existente.
     * GET /inventario/editar/{id}
     *
     * @param string|null $id
     */
    public function editar(?string $id = null): void
    {
        requerirAutenticacion();
        $bebida = $this->modelo->buscarPorId((int) $id);
        if (!$bebida) {
            flashMensaje('error', 'Bebida no encontrada.');
            $this->redirigir('/inventario');
            return;
        }
        $this->render('inventario/formulario', [
            'titulo'     => 'Editar Bebida',
            'bebida'     => $bebida,
            'categorias' => $this->menuModel->obtenerCategorias(),
            'tokenCSRF'  => generarTokenCSRF('inventario'),
            'flash'      => obtenerFlash(),
        ]);
    }

    /**
     * Actualiza una bebida del inventario.
     * POST /inventario/actualizar
     *
     * @return void
     */
    public function actualizar(): void
    {
        requerirAutenticacion();
        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'inventario')) {
            flashMensaje('error', 'Token inválido.');
            $this->redirigir('/inventario');
            return;
        }

        $id = $this->entero('id', 'post');
        $datos = $this->extraerDatos();
        $errores = $this->validar($datos);

        if (!empty($errores)) {
            foreach ($errores as $e) flashMensaje('error', $e);
            $this->redirigir('/inventario/editar/' . $id);
            return;
        }

        $filas = $this->modelo->actualizar($id, $datos);
        flashMensaje($filas > 0 ? 'success' : 'error',
                     $filas > 0 ? 'Bebida actualizada.' : 'Error al actualizar.');
        $this->redirigir('/inventario');
    }

    /**
     * Borrado lógico de una bebida.
     * POST /inventario/eliminar
     *
     * @return void
     */
    public function eliminar(): void
    {
        requerirAutenticacion();
        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'eliminar_inv')) {
            flashMensaje('error', 'Token inválido.');
            $this->redirigir('/inventario');
            return;
        }

        $id    = $this->entero('id', 'post');
        $filas = $this->modelo->eliminar($id);
        flashMensaje($filas > 0 ? 'success' : 'error',
                     $filas > 0 ? 'Bebida eliminada.' : 'No se pudo eliminar.');
        $this->redirigir('/inventario');
    }

    /** Extrae y sanea campos del formulario de inventario. */
    private function extraerDatos(): array
    {
        return [
            'categoria_id'   => $this->entero('categoria_id', 'post'),
            'codigo'         => $this->post('codigo', 20),
            'nombre'         => $this->post('nombre', 120),
            'stock_actual'   => $this->entero('stock_actual', 'post'),
            'stock_minimo'   => $this->entero('stock_minimo', 'post'),
            'precio_unitario'=> (float) ($_POST['precio_unitario'] ?? 0),
        ];
    }

    /** Valida los datos del formulario de inventario. */
    private function validar(array $datos): array
    {
        $errores = [];
        if ($datos['categoria_id'] <= 0) $errores[] = 'Selecciona una categoría.';
        if (empty($datos['codigo']))      $errores[] = 'El código es obligatorio.';
        if (empty($datos['nombre']))      $errores[] = 'El nombre es obligatorio.';
        if ($datos['precio_unitario'] < 0) $errores[] = 'El precio no puede ser negativo.';
        return $errores;
    }
}
