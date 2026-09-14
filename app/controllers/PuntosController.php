<?php
require_once BASE_PATH . '/app/controllers/BaseController.php';
require_once BASE_PATH . '/app/models/ConfiguracionPuntosModel.php';
require_once BASE_PATH . '/app/models/VentaModel.php';

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
     * Busca un cliente por cédula y devuelve su nombre más reciente y su
     * total de puntos vigente, en JSON. Se usa desde la vista de mesas
     * para autocompletar el panel de puntos cuando el mesero escribe la
     * cédula del cliente.
     * GET /puntos/mesa/consultar?cedula=...
     *
     * @return void
     */
    public function consultarCliente(): void
    {
        requerirAutenticacion();
        header('Content-Type: application/json; charset=utf-8');

        $cedula = preg_replace('/[^0-9]/', '', $_GET['cedula'] ?? '');

        if (empty($cedula)) {
            echo json_encode(['success' => false, 'mensaje' => 'Cédula inválida.']);
            return;
        }

        $puntoModel = new PuntosModel();
        $nombre = $puntoModel->obtenerNombrePorCedula($cedula);
        $total  = $puntoModel->totalPuntos($cedula);

        echo json_encode([
            'success' => true,
            'existe'  => $nombre !== null,
            'nombre'  => $nombre,
            'total'   => $total,
        ]);
    }

    /**
     * Abona puntos a un cliente desde la vista de una mesa.
     * POST /puntos/mesa/agregar
     *
     * @return void
     */
    public function mesaAgregar(): void
    {
        requerirAutenticacion();

        $mesa = $this->post('mesa', 10);

        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'venta')) {
            flashMensaje('error', 'Token inválido. Intenta de nuevo.');
            $this->redirigir('/ventas/mesa/' . $mesa);
            return;
        }

        $cedula = preg_replace('/[^0-9]/', '', $_POST['cedula'] ?? '');
        $nombre = $this->post('nombre', 80);
        $puntos = (int) ($_POST['puntos'] ?? 0);
        $empleadoId = $_SESSION['empleado_id'] ?? null;

        if (empty($cedula) || empty($nombre) || $puntos <= 0) {
            flashMensaje('error', 'Debes indicar cédula, nombre y una cantidad de puntos válida.');
            $this->redirigir('/ventas/mesa/' . $mesa);
            return;
        }

        $puntoModel = new PuntosModel();
        $ok = $puntoModel->agregarPuntos($cedula, $nombre, $puntos, $empleadoId, $mesa);

        flashMensaje(
            $ok ? 'success' : 'error',
            $ok ? "Se abonaron {$puntos} puntos a {$nombre}." : 'No se pudieron abonar los puntos.'
        );
        $this->redirigir('/ventas/mesa/' . $mesa);
    }

    /**
     * Descuenta puntos de un cliente desde la vista de una mesa.
     * POST /puntos/mesa/descontar
     *
     * @return void
     */
    public function mesaDescontar(): void
    {
        requerirAutenticacion();

        $mesa = $this->post('mesa', 10);

        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'venta')) {
            flashMensaje('error', 'Token inválido. Intenta de nuevo.');
            $this->redirigir('/ventas/mesa/' . $mesa);
            return;
        }

        $cedula = preg_replace('/[^0-9]/', '', $_POST['cedula'] ?? '');
        $nombre = $this->post('nombre', 80);
        $puntos = (int) ($_POST['puntos'] ?? 0);
        $empleadoId = $_SESSION['empleado_id'] ?? null;

        if (empty($cedula) || $puntos <= 0) {
            flashMensaje('error', 'Debes indicar la cédula del cliente y una cantidad de puntos válida.');
            $this->redirigir('/ventas/mesa/' . $mesa);
            return;
        }

        $puntoModel = new PuntosModel();
        $nombreFinal = $nombre !== '' ? $nombre : ($puntoModel->obtenerNombrePorCedula($cedula) ?? 'Cliente');
        $totalActual = $puntoModel->totalPuntos($cedula);

        if ($totalActual <= 0) {
            flashMensaje('error', 'Este cliente no tiene puntos acumulados para descontar.');
            $this->redirigir('/ventas/mesa/' . $mesa);
            return;
        }

        // No permitir dejar el saldo en negativo
        $puntosADescontar = min($puntos, $totalActual);

        $ok = $puntoModel->descontarPuntos($cedula, $nombreFinal, $puntosADescontar, $empleadoId, $mesa);

        flashMensaje(
            $ok ? 'success' : 'error',
            $ok ? "Se descontaron {$puntosADescontar} puntos a {$nombreFinal}." : 'No se pudieron descontar los puntos.'
        );
        $this->redirigir('/ventas/mesa/' . $mesa);
    }

    /**
     * Redime (canjea) una recompensa del catálogo por los puntos de un
     * cliente, desde la vista de una mesa. A diferencia de la versión
     * anterior, aquí el descuento (o el producto gratis) se aplica
     * AUTOMÁTICAMENTE sobre el total de la venta abierta de la mesa,
     * usando VentaModel::aplicarDescuentoRecompensa(). El mesero ya no
     * tiene que calcular ni restar nada manualmente.
     * POST /puntos/mesa/redimir
     *
     * @return void
     */
    public function mesaRedimir(): void
    {
        requerirAutenticacion();

        $mesa = $this->post('mesa', 10);

        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'venta')) {
            flashMensaje('error', 'Token inválido. Intenta de nuevo.');
            $this->redirigir('/ventas/mesa/' . $mesa);
            return;
        }

        $cedula = preg_replace('/[^0-9]/', '', $_POST['cedula'] ?? '');
        $nombre = $this->post('nombre', 80);
        $recompensaId = $this->entero('recompensa_id', 'post');
        $empleadoId = $_SESSION['empleado_id'] ?? null;

        if (empty($cedula) || $recompensaId <= 0) {
            flashMensaje('error', 'Debes indicar la cédula del cliente y seleccionar una recompensa.');
            $this->redirigir('/ventas/mesa/' . $mesa);
            return;
        }

        $configModel = new ConfiguracionPuntosModel();
        $recompensa  = $configModel->obtenerRecompensaPorId($recompensaId);

        if (!$recompensa || !$recompensa['activo']) {
            flashMensaje('error', 'La recompensa seleccionada no está disponible.');
            $this->redirigir('/ventas/mesa/' . $mesa);
            return;
        }

        // La recompensa se aplica sobre la venta ABIERTA de la mesa: si aún
        // no hay ninguna cuenta iniciada, no hay total sobre el cual aplicar
        // el descuento (o registrar el producto gratis).
        $ventaModel = new VentaModel();
        $venta = $ventaModel->obtenerVentaAbiertaPorMesa($mesa);

        if (!$venta) {
            flashMensaje('error', 'Agrega al menos un producto a la cuenta antes de canjear una recompensa.');
            $this->redirigir('/ventas/mesa/' . $mesa);
            return;
        }

        $puntoModel  = new PuntosModel();
        $nombreFinal = $nombre !== '' ? $nombre : ($puntoModel->obtenerNombrePorCedula($cedula) ?? 'Cliente');
        $totalActual = $puntoModel->totalPuntos($cedula);
        $puntosRequeridos = (int) $recompensa['puntos_requeridos'];

        if ($totalActual < $puntosRequeridos) {
            flashMensaje('error', "El cliente solo tiene {$totalActual} puntos y la recompensa \"{$recompensa['nombre']}\" requiere {$puntosRequeridos}.");
            $this->redirigir('/ventas/mesa/' . $mesa);
            return;
        }

        // 1. Aplicar el efecto real de la recompensa sobre la venta
        //    (descuenta del total o agrega el producto gratis + stock).
        //    Si algo falla (sin stock, categoría sin consumo, etc.) no se
        //    descuentan puntos del cliente: se corta aquí.
        try {
            $valorBeneficio = $ventaModel->aplicarDescuentoRecompensa((int) $venta['id'], $recompensa);
        } catch (\Exception $e) {
            flashMensaje('error', $e->getMessage());
            $this->redirigir('/ventas/mesa/' . $mesa);
            return;
        }

        // 2. Solo si el paso anterior tuvo éxito, se descuentan los puntos
        //    del historial de fidelización del cliente.
        $ok = $puntoModel->redimirPuntos($cedula, $nombreFinal, $puntosRequeridos, $recompensaId, $empleadoId, $mesa);

        $mensajeExtra = $recompensa['tipo'] === 'descuento_categoria'
            ? sprintf('Se descontaron $%s de la cuenta.', number_format($valorBeneficio, 2))
            : 'El producto se agregó gratis a la cuenta.';

        flashMensaje(
            $ok ? 'success' : 'error',
            $ok
                ? "Se canjeó \"{$recompensa['nombre']}\" por {$puntosRequeridos} puntos. {$mensajeExtra}"
                : 'No se pudo redimir la recompensa.'
        );
        $this->redirigir('/ventas/mesa/' . $mesa);
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
