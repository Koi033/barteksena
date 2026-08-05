<?php
/**
 * app/controllers/EmpleadoController.php
 * CRUD completo para la gestión de empleados del bar.
 * Todas las rutas requieren autenticación activa.
 *
 * @package Bartek\Controllers
 */
require_once BASE_PATH . '/app/controllers/BaseController.php';

class EmpleadoController extends BaseController
{
    private EmpleadoModel $modelo;

    public function __construct()
    {
        $this->modelo = new EmpleadoModel();
    }

    /**
     * Lista empleados con búsqueda, filtro y paginación.
     * GET /empleados
     *
     * @return void
     */
    public function index(): void
    {
        requerirAutenticacion();

        $pagina   = max(1, $this->entero('pagina', 'get', 1));
        $busqueda = $this->get('busqueda');
        $depto    = $this->get('departamento');

        $empleados   = $this->modelo->obtenerTodos($busqueda, $depto, $pagina);
        $total       = $this->modelo->contarFiltrados($busqueda, $depto);
        $deptos      = $this->modelo->obtenerDepartamentos();
        $flash       = obtenerFlash();

        $this->render('empleados/index', [
            'titulo'       => 'Empleados - Bartek',
            'empleados'    => $empleados,
            'departamentos'=> $deptos,
            'busqueda'     => $busqueda,
            'deptoFiltro'  => $depto,
            'paginaActual' => $pagina,
            'totalPaginas' => (int) ceil($total / ITEMS_POR_PAGINA),
            'total'        => $total,
            'flash'        => $flash,
        ]);
    }

    /**
     * Muestra formulario de creación de empleado.
     * GET /empleados/crear
     *
     * @return void
     */
    public function crear(): void
    {
        requerirAutenticacion();
        $tokenCSRF = generarTokenCSRF('empleado');
        $this->render('empleados/formulario', [
            'titulo'    => 'Agregar Empleado',
            'empleado'  => null,
            'tokenCSRF' => $tokenCSRF,
            'flash'     => obtenerFlash(),
        ]);
    }

    /**
     * Procesa el formulario de creación.
     * POST /empleados/guardar
     *
     * @return void
     */
public function guardar(): void
    {
        requerirAutenticacion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/empleados');
        }

        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'empleado')) {
            flashMensaje('error', 'Token inválido.');
            $this->redirigir('/empleados/crear');
            return;
        }

        // Extraer datos del empleado
        $datosEmpleado = $this->extraerDatosFormulario();
        
        // Extraer credenciales para la cuenta de usuario
        $usuarioRaw = $this->post('usuario', 60);
        $passRaw    = $_POST['contrasena'] ?? '';

        $errores = $this->validarDatos($datosEmpleado);

        // Validaciones adicionales para la cuenta
        if (empty($usuarioRaw)) $errores[] = 'El nombre de usuario es obligatorio para el acceso.';
        if (strlen($passRaw) < 8) $errores[] = 'La contraseña debe tener mínimo 8 caracteres.';

        if (!empty($errores)) {
            foreach ($errores as $e) flashMensaje('error', $e);
            $this->redirigir('/empleados/crear');
            return;
        }

        // Preparar datos del usuario (dividir nombre completo para la tabla usuarios)
        $partesNombre = explode(' ', $datosEmpleado['nombre_completo'], 2);
        
        $datosUsuario = [
            'nombre'     => $partesNombre[0],
            'apellido'   => $partesNombre[1] ?? '',
            'email'      => $datosEmpleado['email'],
            'telefono'   => $datosEmpleado['telefono'],
            'usuario'    => $usuarioRaw,
            // Aplicar hash seguro como se hace en el AuthController
            'contrasena' => password_hash($passRaw, PASSWORD_BCRYPT, ['cost' => 12]), 
        ];

        // Usar el nuevo método transaccional
        $id = $this->modelo->crearConCuenta($datosEmpleado, $datosUsuario);
        
        if ($id > 0) {
            flashMensaje('success', 'Empleado y cuenta de acceso creados exitosamente.');
        } else {
            flashMensaje('error', 'Error al crear el empleado. Es posible que el nombre de usuario o correo ya existan.');
        }
        
        $this->redirigir('/empleados');
    }

    /**
     * Muestra formulario de edición de un empleado existente.
     * GET /empleados/editar/{id}
     *
     * @param  string|null $id ID del empleado
     * @return void
     */
    public function editar(?string $id = null): void
    {
        requerirAutenticacion();
        $id = (int) $id;

        $empleado = $this->modelo->buscarPorId($id);
        if (!$empleado) {
            flashMensaje('error', 'Empleado no encontrado.');
            $this->redirigir('/empleados');
            return;
        }

        $tokenCSRF = generarTokenCSRF('empleado');
        $this->render('empleados/formulario', [
            'titulo'    => 'Editar Empleado',
            'empleado'  => $empleado,
            'tokenCSRF' => $tokenCSRF,
            'flash'     => obtenerFlash(),
        ]);
    }

    /**
     * Procesa la actualización de un empleado.
     * POST /empleados/actualizar
     *
     * @return void
     */
    public function actualizar(): void
    {
        requerirAutenticacion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/empleados');
        }

        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'empleado')) {
            flashMensaje('error', 'Token inválido.');
            $this->redirigir('/empleados');
            return;
        }

        $id    = $this->entero('id', 'post');
        $datos = $this->extraerDatosFormulario();
        $errores = $this->validarDatos($datos);

        if (!empty($errores)) {
            foreach ($errores as $e) flashMensaje('error', $e);
            $this->redirigir('/empleados/editar/' . $id);
            return;
        }

        $filas = $this->modelo->actualizar($id, $datos);
        flashMensaje($filas > 0 ? 'success' : 'error',
                     $filas > 0 ? 'Empleado actualizado.' : 'Error al actualizar.');
        $this->redirigir('/empleados');
    }

    /**
     * Realiza borrado lógico de un empleado.
     * POST /empleados/eliminar
     *
     * @return void
     */
    public function eliminar(): void
    {
        requerirAutenticacion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/empleados');
        }

        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'eliminar_emp')) {
            flashMensaje('error', 'Token inválido.');
            $this->redirigir('/empleados');
            return;
        }

        $id = $this->entero('id', 'post');
        $filas = $this->modelo->eliminar($id);
        flashMensaje($filas > 0 ? 'success' : 'error',
                     $filas > 0 ? 'Empleado eliminado.' : 'No se pudo eliminar.');
        $this->redirigir('/empleados');
    }

    /**
     * Extrae y sanea los campos del formulario de empleado.
     *
     * @return array Datos saneados
     */
    private function extraerDatosFormulario(): array
    {
        return [
            'nombre_completo' => $this->post('nombre_completo', 150),
            'puesto'          => $this->post('puesto', 80),
            'departamento'    => $this->post('departamento', 80) ?: 'General',
            'email'           => filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?: '',
            'telefono'        => $this->post('telefono', 20),
        ];
    }

    /**
     * Valida los datos del formulario de empleado.
     *
     * @param  array $datos Datos saneados
     * @return array        Lista de errores (vacía si todo está bien)
     */
    private function validarDatos(array $datos): array
    {
        $errores = [];
        if (empty($datos['nombre_completo'])) $errores[] = 'El nombre completo es obligatorio.';
        if (empty($datos['puesto']))          $errores[] = 'El puesto es obligatorio.';
        if (empty($datos['departamento']))    $errores[] = 'El departamento es obligatorio.';
        if (empty($datos['email']))           $errores[] = 'El correo electrónico no es válido.';
        return $errores;
    }
}
