<?php
/**
 * app/controllers/HorarioController.php
 * Gestión de horarios y turnos de empleados.
 *
 * @package Bartek\Controllers
 */
require_once BASE_PATH . '/app/controllers/BaseController.php';

class HorarioController extends BaseController
{
    private HorarioModel  $modelo;
    private EmpleadoModel $empModel;

    public function __construct()
    {
        $this->modelo   = new HorarioModel();
        $this->empModel = new EmpleadoModel();
    }

    /**
     * Vista principal de horarios con lista y formulario de creación.
     * GET /horarios
     *
     * @return void
     */
    public function index(): void
    {
        requerirAutenticacion();
        $pagina    = max(1, $this->entero('pagina', 'get', 1));
        $pendientes = $this->modelo->obtenerPendientes();
        $horarios  = $this->modelo->obtenerTodos($pagina);
        $total     = $this->modelo->contarTotal();
        $empleados = $this->empModel->obtenerTodos('', '', 1, 100); // todos para el selector

        $this->render('horarios/index', [
            'titulo'       => 'Horarios - Bartek',
            'horarios'     => $horarios,
            'pendientes'   => $pendientes,
            'empleados'    => $empleados,
            'paginaActual' => $pagina,
            'totalPaginas' => (int) ceil($total / ITEMS_POR_PAGINA),
            'tokenCSRF'    => generarTokenCSRF('horario'),
            'flash'        => obtenerFlash(),
        ]);
    }

    /**
     * Crea un nuevo horario para un empleado.
     * POST /horarios/guardar
     *
     * @return void
     */
    public function guardar(): void
    {
        requerirAutenticacion();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/horarios');
        }
        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'horario')) {
            flashMensaje('error', 'Token inválido.');
            $this->redirigir('/horarios');
            return;
        }

        $datos   = [
            'empleado_id' => $this->entero('empleado_id', 'post'),
            'fecha'       => $this->post('fecha', 10),
            'hora_inicio' => $this->post('hora_inicio', 5),
            'hora_fin'    => $this->post('hora_fin', 5),
        ];

        $errores = [];
        if ($datos['empleado_id'] <= 0)  $errores[] = 'Selecciona un empleado.';
        if (empty($datos['fecha']))       $errores[] = 'La fecha es obligatoria.';
        if (empty($datos['hora_inicio'])) $errores[] = 'La hora de inicio es obligatoria.';
        if (empty($datos['hora_fin']))    $errores[] = 'La hora de fin es obligatoria.';

        if (!empty($errores)) {
            foreach ($errores as $e) flashMensaje('error', $e);
            $this->redirigir('/horarios');
            return;
        }

        $id = $this->modelo->crear($datos);
        flashMensaje($id > 0 ? 'success' : 'error',
                     $id > 0 ? 'Horario creado exitosamente.' : 'Error al guardar horario.');
        $this->redirigir('/horarios');
    }

    /**
     * Aprueba o rechaza un horario pendiente.
     * POST /horarios/cambiar-estado
     *
     * @return void
     */
    public function cambiarEstado(): void
    {
        requerirAutenticacion();
        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'horario')) {
            flashMensaje('error', 'Token inválido.');
            $this->redirigir('/horarios');
            return;
        }

        $id     = $this->entero('id', 'post');
        $estado = $this->post('estado', 20);
        $filas  = $this->modelo->cambiarEstado($id, $estado);

        flashMensaje($filas > 0 ? 'success' : 'error',
                     $filas > 0 ? 'Estado actualizado.' : 'No se pudo actualizar.');
        $this->redirigir('/horarios');
    }

    /**
     * Elimina un horario por ID.
     * POST /horarios/eliminar
     *
     * @return void
     */
    public function eliminar(): void
    {
        requerirAutenticacion();
        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'horario')) {
            flashMensaje('error', 'Token inválido.');
            $this->redirigir('/horarios');
            return;
        }

        $id    = $this->entero('id', 'post');
        $filas = $this->modelo->eliminar($id);
        flashMensaje($filas > 0 ? 'success' : 'error',
                     $filas > 0 ? 'Horario eliminado.' : 'Error al eliminar.');
        $this->redirigir('/horarios');
    }
}
