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
        $this->render('reservas/reservas', [
            'reservas'  => $reservas,
            'titulo'     => 'Reservas - Bartek',
            'tokenCSRF'  => generarTokenCSRF('reserva_eliminar'),
            'flash'      => obtenerFlash(),
        ]);
    }

    /**
     * Elimina una reserva por ID.
     * POST /reservas/eliminar
     *
     * @return void
     */
    public function eliminar(): void
    {
        requerirAutenticacion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/reservas');
            return;
        }

        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'reserva_eliminar')) {
            flashMensaje('error', 'Token inválido.');
            $this->redirigir('/reservas');
            return;
        }

        $id = $this->entero('id', 'post');
        $filas = $this->reservaModel->eliminar($id);

        flashMensaje($filas > 0 ? 'success' : 'error',
                     $filas > 0 ? 'Reserva eliminada.' : 'No se pudo eliminar la reserva.');
        $this->redirigir('/reservas');
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
