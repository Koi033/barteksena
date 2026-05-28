<?php
/**
 * app/controllers/MenuController.php
 * Gestión del menú interactivo (categorías y bebidas).
 *
 * @package Bartek\Controllers
 */
require_once BASE_PATH . '/app/controllers/BaseController.php';

class MenuController extends BaseController
{
    private MenuModel $modelo;

    public function __construct()
    {
        $this->modelo = new MenuModel();
    }

    /**
     * Vista principal del menú interactivo con listado de categorías.
     * GET /menu
     *
     * @return void
     */
    public function index(): void
    {
        requerirAutenticacion();
        $this->render('menu/index', [
            'titulo'     => 'Menú Interactivo - Bartek',
            'categorias' => $this->modelo->obtenerCategorias(),
            'tokenCSRF'  => generarTokenCSRF('menu'),
            'flash'      => obtenerFlash(),
        ]);
    }

    /**
     * Crea una nueva categoría de menú.
     * POST /menu/guardar
     *
     * @return void
     */
    public function guardar(): void
    {
        requerirAutenticacion();
        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'menu')) {
            flashMensaje('error', 'Token inválido.');
            $this->redirigir('/menu');
            return;
        }

        $datos = [
            'nombre'      => $this->post('nombre', 80),
            'descripcion' => $this->post('descripcion', 500),
        ];

        if (empty($datos['nombre'])) {
            flashMensaje('error', 'El nombre de la categoría es obligatorio.');
            $this->redirigir('/menu');
            return;
        }

        $id = $this->modelo->crearCategoria($datos);
        flashMensaje($id > 0 ? 'success' : 'error',
                     $id > 0 ? 'Categoría creada.' : 'Error al crear categoría.');
        $this->redirigir('/menu');
    }

    /**
     * Elimina (borrado lógico) una categoría del menú.
     * POST /menu/eliminar
     *
     * @return void
     */
    public function eliminar(): void
    {
        requerirAutenticacion();
        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'menu')) {
            flashMensaje('error', 'Token inválido.');
            $this->redirigir('/menu');
            return;
        }

        $id    = $this->entero('id', 'post');
        $filas = $this->modelo->eliminarCategoria($id);
        flashMensaje($filas > 0 ? 'success' : 'error',
                     $filas > 0 ? 'Categoría eliminada.' : 'Error al eliminar.');
        $this->redirigir('/menu');
    }
}
