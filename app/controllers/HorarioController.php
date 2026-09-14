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
        $rolUsuario = $_SESSION['usuario_rol'] ?? '';

        // Los empleados nunca ven el CRUD de administración: solo su propio
        // horario, en formato calendario y sin acciones de edición.
        if ($rolUsuario === 'empleado') {
            $this->misHorarios();
            return;
        }

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
            'rolUsuario'   => $rolUsuario,
        ]);
    }

    /**
     * Vista de solo lectura para el empleado: su horario del mes en formato
     * calendario. No incluye formularios de creación, edición ni acciones
     * de aprobación/eliminación.
     * GET /horarios (rol empleado, delegado desde index())
     *
     * Resuelve el empleado_id a partir de empleados.usuario_id, que enlaza
     * con el usuario logueado ($_SESSION['usuario_id']).
     *
     * @return void
     */
    private function misHorarios(): void
    {
        $usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);
        $empleado  = $usuarioId > 0 ? $this->empModel->buscarPorUsuarioId($usuarioId) : false;
        $empleadoId = $empleado ? (int) $empleado['id'] : 0;

        $mes  = max(1, min(12, $this->entero('mes', 'get', (int) date('n'))));
        $anio = max(2000, $this->entero('anio', 'get', (int) date('Y')));

        if ($empleadoId === 0) {
            flashMensaje('error', 'Tu usuario no está vinculado a un registro de empleado.');
        }

        $horarios = $empleadoId > 0
            ? $this->modelo->obtenerPorEmpleadoYMes($empleadoId, $mes, $anio)
            : [];

        // Agrupar los turnos por día del mes para pintar el calendario.
        $porDia = [];
        foreach ($horarios as $h) {
            $dia = (int) date('j', strtotime($h['fecha']));
            $porDia[$dia][] = $h;
        }

        $totalDias    = (int) date('t', mktime(0, 0, 0, $mes, 1, $anio));
        $primerDiaSem = (int) date('N', mktime(0, 0, 0, $mes, 1, $anio)); // 1=lun ... 7=dom

        $mesAnterior = $mes - 1; $anioAnterior = $anio;
        if ($mesAnterior < 1) { $mesAnterior = 12; $anioAnterior--; }

        $mesSiguiente = $mes + 1; $anioSiguiente = $anio;
        if ($mesSiguiente > 12) { $mesSiguiente = 1; $anioSiguiente++; }

        $nombresMeses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        $this->render('horarios/mi_horario', [
            'titulo'        => 'Mi Horario - Bartek',
            'porDia'        => $porDia,
            'mes'           => $mes,
            'anio'          => $anio,
            'totalDias'     => $totalDias,
            'primerDiaSem'  => $primerDiaSem,
            'mesAnterior'   => $mesAnterior,
            'anioAnterior'  => $anioAnterior,
            'mesSiguiente'  => $mesSiguiente,
            'anioSiguiente' => $anioSiguiente,
            'nombreMes'     => $nombresMeses[$mes],
            'flash'         => obtenerFlash(),
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

        if (($_SESSION['usuario_rol'] ?? '') === 'empleado') {
            flashMensaje('error', 'Los empleados no pueden modificar horarios.');
            $this->redirigir('/horarios');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/horarios');
        }
        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'horario')) {
            flashMensaje('error', 'Token inválido.');
            $this->redirigir('/horarios');
            return;
        }

        $empleadoId  = $this->entero('empleado_id', 'post');
        $fechaInicio = $this->post('fecha_inicio', 10);
        $fechaFin    = $this->post('fecha_fin', 10);
        $horaInicio  = $this->post('hora_inicio', 5);
        $horaFin     = $this->post('hora_fin', 5);
        $dias        = array_map('intval', $_POST['dias'] ?? []); // 1=lun ... 7=dom

        $errores = [];
        if ($empleadoId <= 0)    $errores[] = 'Selecciona un empleado.';
        if (empty($fechaInicio)) $errores[] = 'La fecha de inicio es obligatoria.';
        if (empty($fechaFin))    $errores[] = 'La fecha de fin es obligatoria.';
        if (empty($horaInicio))  $errores[] = 'La hora de inicio es obligatoria.';
        if (empty($horaFin))     $errores[] = 'La hora de fin es obligatoria.';
        if (empty($dias))        $errores[] = 'Selecciona al menos un día de la semana.';

        $inicio = $fin = null;
        if (empty($errores)) {
            $inicio = DateTime::createFromFormat('Y-m-d', $fechaInicio) ?: null;
            $fin    = DateTime::createFromFormat('Y-m-d', $fechaFin) ?: null;
            if (!$inicio || !$fin) {
                $errores[] = 'Las fechas no son válidas.';
            } elseif ($fin < $inicio) {
                $errores[] = 'La fecha de fin debe ser igual o posterior a la de inicio.';
            } elseif ($inicio->diff($fin)->days > 366) {
                $errores[] = 'El rango de fechas no puede superar un año.';
            }
        }

        if (!empty($errores)) {
            foreach ($errores as $e) flashMensaje('error', $e);
            $this->redirigir('/horarios');
            return;
        }

        // Genera un horario por cada fecha del rango cuyo día de la semana
        // esté entre los seleccionados.
        $creados = 0;
        $periodo = new DatePeriod($inicio, new DateInterval('P1D'), (clone $fin)->modify('+1 day'));
        foreach ($periodo as $fecha) {
            $diaSemana = (int) $fecha->format('N'); // 1=lun ... 7=dom
            if (!in_array($diaSemana, $dias, true)) {
                continue;
            }
            $id = $this->modelo->crear([
                'empleado_id' => $empleadoId,
                'fecha'       => $fecha->format('Y-m-d'),
                'hora_inicio' => $horaInicio,
                'hora_fin'    => $horaFin,
            ]);
            if ($id > 0) $creados++;
        }

        flashMensaje(
            $creados > 0 ? 'success' : 'error',
            $creados > 0 ? "Se crearon {$creados} horario(s) exitosamente." : 'No se pudo crear ningún horario con esos criterios.'
        );
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

        if (($_SESSION['usuario_rol'] ?? '') === 'empleado') {
            flashMensaje('error', 'Los empleados no pueden aprobar ni rechazar horarios.');
            $this->redirigir('/horarios');
            return;
        }

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
     * Aprueba o rechaza varios horarios pendientes de una sola vez.
     * POST /horarios/cambiarEstadoMasivo
     *
     * @return void
     */
    public function cambiarEstadoMasivo(): void
    {
        requerirAutenticacion();

        if (($_SESSION['usuario_rol'] ?? '') === 'empleado') {
            flashMensaje('error', 'Los empleados no pueden aprobar ni rechazar horarios.');
            $this->redirigir('/horarios');
            return;
        }

        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'horario')) {
            flashMensaje('error', 'Token inválido.');
            $this->redirigir('/horarios');
            return;
        }

        $ids = array_map('intval', $_POST['ids'] ?? []);
        $ids = array_values(array_filter($ids, fn ($id) => $id > 0));
        $estado = $this->post('estado', 20);

        if (empty($ids)) {
            flashMensaje('error', 'No seleccionaste ningún horario.');
            $this->redirigir('/horarios');
            return;
        }

        $actualizados = 0;
        foreach ($ids as $id) {
            if ($this->modelo->cambiarEstado($id, $estado) > 0) {
                $actualizados++;
            }
        }

        flashMensaje(
            $actualizados > 0 ? 'success' : 'error',
            $actualizados > 0
                ? "Se actualizaron {$actualizados} horario(s)."
                : 'No se pudo actualizar ningún horario.'
        );
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

        if (($_SESSION['usuario_rol'] ?? '') === 'empleado') {
            flashMensaje('error', 'Los empleados no pueden eliminar horarios.');
            $this->redirigir('/horarios');
            return;
        }

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
