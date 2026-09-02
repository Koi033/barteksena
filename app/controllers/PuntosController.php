<?php
require_once BASE_PATH . '/app/controllers/BaseController.php';

class PuntosController extends BaseController
{
    public function index(): void
    {
        requerirAutenticacion();
        $this->render('puntos/index', [
            'titulo' => 'Fidelización - Bartek'
        ]);
    }

 public function guardar(): void
{
    requerirAutenticacion();

    // Capturamos el nombre real que escribió el mesero en la vista
    $nombre = $_POST['nombre'] ?? '';
    $cedula = $_POST['cedula'] ?? '';
    $puntos = (int)($_POST['puntos'] ?? 0);

    if (!empty($cedula) && !empty($nombre) && $puntos > 0) {
        $puntoModel = new PuntosModel();
        // Enviamos el nombre real a la base de datos
        $puntoModel->registrarPuntos($cedula, $nombre, $puntos, 'ganado');
    }

    $this->redirigir('/puntos');

    
}
   public function listado(): void
    {
        requerirAutenticacion();

        $busqueda = $_GET['buscar'] ?? null;
        $puntoModel = new PuntosModel();

        if ($busqueda) {
            $registros = $puntoModel->buscarPorCedulaONombre($busqueda);
        } else {
            // Aquí llamamos al método que SÍ existe en tu PuntosModel.php
            $registros = $puntoModel->obtenerTodosLosRegistros();
        }

        $this->render('puntos/listado', [
            'titulo' => 'Historial y Control de Puntos - Bartek',
            'registros' => $registros
        ]);
    }
    // Muestra el formulario para editar el estado de un registro de puntos
    public function editar($id = null): void
    {
        requerirAutenticacion();

        // Si por alguna razón llega por GET, lo rescatamos, si no, usa el parámetro de la ruta
        $id = $id ?? ($_GET['id'] ?? null);

        $puntoModel = new PuntosModel();
        $registro = $puntoModel->obtenerPorId($id);

        $this->render('puntos/editar', [
            'titulo' => 'Modificar Estado - Bartek',
            'registro' => $registro
        ]);
    }

    // Procesa la actualización del estado (ej: cambiar a canjeado, cancelado, etc.)
    public function actualizar(): void
    {
        requerirAutenticacion();

        $id = $_POST['id'] ?? null;
        $puntos = $_POST['cantidad_puntos'] ?? 0;
        $tipo = $_POST['tipo'] ?? 'ganado';

        if ($id) {
            $puntoModel = new PuntosModel();
            $puntoModel->actualizarPuntosYEstado($id, (int)$puntos, $tipo);
        }

        // Redirige de vuelta al historial general
        header('Location: ' . BASE_URL . '/puntos/listado');
        exit;
    }
 // Eliminar
    public function eliminar(): void
    {
        requerirAutenticacion();

        $id = $_GET['id'] ?? null;

        if ($id) {
            $puntoModel = new PuntosModel();
            $puntoModel->eliminarRegistro($id);
        }

        $this->redirigir('/puntos/listado');
    }

    /**
     * Formulario público de inscripción al club de fidelización.
     * GET  /puntos/registro  -> muestra el formulario
     * POST /puntos/registro  -> procesa la inscripción
     *
     * A propósito NO lleva requerirAutenticacion(): cualquier cliente debe
     * poder registrarse por su cuenta (por ejemplo, escaneando un QR en su
     * mesa) sin que un mesero tenga que iniciar sesión por él.
     */
    public function registroPublico(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarRegistroPublico();
            return;
        }

        $this->render('puntos/registro_publico', [
            'titulo'    => 'Club de Fidelización - Bartek',
            'tokenCSRF' => generarTokenCSRF('puntos_registro'),
            'flash'     => obtenerFlash(),
            'old'       => $this->obtenerInputAntiguo('puntos_registro'),
        ], 'auth');
    }

    /**
     * Valida y guarda la inscripción pública de un cliente nuevo.
     *
     * @return void
     */
    private function procesarRegistroPublico(): void
    {
        // 1. Validar token CSRF (la sesión anónima ya existe gracias a iniciarSesion())
        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'puntos_registro')) {
            flashMensaje('error', 'Solicitud inválida. Por favor recarga la página e inténtalo de nuevo.');
            $this->redirigir('/puntos/registro');
            return;
        }

        // 2. Obtener y limpiar campos
        $nombre = $this->post('nombre', 80);
        $cedula = preg_replace('/[^0-9]/', '', $_POST['cedula'] ?? '');

        // 3. Validaciones básicas
        if (empty($nombre) || empty($cedula)) {
            flashMensaje('error', 'Debes ingresar tu nombre y tu número de cédula.');
            $this->guardarInputAntiguo('puntos_registro', $_POST);
            $this->redirigir('/puntos/registro');
            return;
        }

        if (strlen($cedula) < 6 || strlen($cedula) > 10) {
            flashMensaje('error', 'Ingresa un número de cédula válido (sin puntos ni espacios).');
            $this->guardarInputAntiguo('puntos_registro', $_POST);
            $this->redirigir('/puntos/registro');
            return;
        }

        $puntoModel = new PuntosModel();

        // 4. Evitar registros duplicados para la misma cédula
        if ($puntoModel->existeClienteRegistrado($cedula)) {
            flashMensaje('info', 'Esta cédula ya está inscrita en el Club de Fidelización. ¡Sigue acumulando puntos con tu consumo!');
            $this->redirigir('/puntos/registro');
            return;
        }

        // 5. Registrar al cliente con puntos de bienvenida
        $ok = $puntoModel->registrarClientePublico($cedula, $nombre);

        if ($ok) {
            flashMensaje(
                'success',
                '¡Bienvenido/a al Club de Fidelización, ' . $nombre . '! Ya tienes puntos de bienvenida. Muéstrale tu cédula al mesero en tu próxima visita para seguir acumulando.'
            );
        } else {
            flashMensaje('error', 'No pudimos completar tu registro. Intenta de nuevo en un momento.');
        }

        $this->redirigir('/puntos/registro');
    }
}