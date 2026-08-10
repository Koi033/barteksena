<?php
/**
 * app/controllers/ReservaController.php
 * Controlador para la gestión de reservas desde el panel.
 *
 * @package Bartek\Controllers
 */
require_once BASE_PATH . '/app/controllers/BaseController.php';

class ReservaController extends BaseController
{
    private ReservaModel $reservaModel;

    public function __construct()
    {
        $this->reservaModel = new ReservaModel();
    }

    /**
     * Muestra el listado de reservas en el panel.
     */
    public function index(): void
    {
        requerirAutenticacion();

        $reservas = $this->reservaModel->obtenerTodas();

        // Carga la vista de reservas pasándole la variable $reservas
        $this->render('empleados/reservas', [
            'reservas' => $reservas,
            'titulo' => 'Reservas - Bartek',
            'flash' => obtenerFlash(),
        ]);
    }

    /**
     * Cierra la sesión del usuario y redirige al login.
     */
    public function logout(): void
    {
        cerrarSesion();
        $this->redirigir('/login');
    }
}
