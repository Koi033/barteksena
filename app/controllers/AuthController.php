<?php
/**
 * app/controllers/AuthController.php
 * Controlador de autenticación: login, registro y cierre de sesión.
 * Gestiona también las páginas públicas (inicio, nosotros, servicios).
 *
 * @package Bartek\Controllers
 */

require_once BASE_PATH . '/app/controllers/BaseController.php';
require_once BASE_PATH . '/app/models/EmpleadoModel.php';

class AuthController extends BaseController
{
    /** @var UsuarioModel Modelo de usuarios */
    private UsuarioModel $usuarioModel;

    /** @var EmpleadoModel Modelo de empleados */
    private EmpleadoModel $empleadoModel;

    /**
     * Constructor: instancia el modelo de usuarios.
     */
    public function __construct()
    {
        $this->usuarioModel  = new UsuarioModel();
        $this->empleadoModel = new EmpleadoModel();
    }

    // ── Página principal pública ──────────────────────────────────────────────

    /**
     * Muestra la página de inicio (landing page).
     * Si el usuario ya está logueado lo redirige al dashboard.
     *
     * @return void
     */
    public function index(): void
    {
        if (estaAutenticado()) {
            $this->redirigir('/dashboard');
        }
        $this->render('public/inicio', ['titulo' => 'Bartek - Inicio'], 'public');
    }

    /**
     * Vista pública del menú digital para clientes vía Código QR.
     * GET /menu/publico (o la ruta libre que configures)
     *
     * @return void
     */
    public function publico(): void
    {
        // NO lleva requerirAutenticacion() para que sea libre para los clientes
        
        // Obtenemos las categorías y los productos/licores activos directamente de la BD
        $categorias = $this->modelo->obtenerCategorias();
        $licores = $this->modelo->obtenerLicoresDisponibles(); // Método que trae los licores con stock > 0

        $this->render('menu/publico', [
            'titulo'     => 'Carta Digital - Bartek',
            'categorias' => $categorias,
            'licores'    => $licores,
        ], false); // El 'false' al final indica que no use la plantilla de administración general, sino un diseño limpio para celulares
    }
    /**
     * Muestra la página "Nosotros".
     *
     * @return void
     */
    public function nosotros(): void
    {
        $this->render('public/nosotros', ['titulo' => 'Nosotros - Bartek'], 'public');
    }

    /**
     * Muestra la página de servicios.
     *
     * @return void
     */
    public function servicios(): void
    {
        $this->render('public/servicios', ['titulo' => 'Servicios - Bartek'], 'public');
    }

    // ── Login ─────────────────────────────────────────────────────────────────

    /**
     * GET: muestra el formulario de login.
     * POST: procesa las credenciales y autentica al usuario.
     *
     * @return void
     */
    public function login(): void
    {
        // Si ya está autenticado, redirigir
        if (estaAutenticado()) {
            $this->redirigir('/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarLogin();
            return;
        }

        // Generar token CSRF para el formulario
        $tokenCSRF = generarTokenCSRF('login');
        $flash     = obtenerFlash();
        $this->render('auth/login', [
            'titulo'    => 'Iniciar Sesión',
            'tokenCSRF' => $tokenCSRF,
            'flash'     => $flash,
        ], 'auth');
    }

    /**
     * Procesa las credenciales del formulario de login.
     * Valida CSRF, verifica usuario/contraseña con hash seguro y crea sesión.
     *
     * @return void
     */
    private function procesarLogin(): void
    {
        // 1. Validar token CSRF
        $token = $_POST['csrf_token'] ?? '';
        if (!validarTokenCSRF($token, 'login')) {
            flashMensaje('error', 'Solicitud inválida. Por favor recarga la página.');
            $this->redirigir('/login');
            return;
        }

        // 2. Obtener y sanear campos
        $usuario    = $this->post('usuario');
        $contrasena = $_POST['contrasena'] ?? '';   // No sanear contraseña antes de verificar hash

        // 3. Validación básica
        if (empty($usuario) || empty($contrasena)) {
            flashMensaje('error', 'Todos los campos son obligatorios.');
            $this->redirigir('/login');
            return;
        }

        // 4. Buscar usuario en BD
        $user = $this->usuarioModel->buscarPorUsuario($usuario);

        // 5. Verificar contraseña con bcrypt (password_verify previene timing attacks)
        if (!$user || !password_verify($contrasena, $user['contrasena'])) {
            // Mismo mensaje para no revelar si el usuario existe
            flashMensaje('error', 'Usuario o contraseña incorrectos.');
            $this->redirigir('/login');
            return;
        }

        // 6. Verificar cuenta activa
        if (!(bool)$user['activo']) {
            flashMensaje('error', 'Tu cuenta está desactivada. Contacta al administrador.');
            $this->redirigir('/login');
            return;
        }

        // 7. Crear variables de sesión (sin datos sensibles como contraseña)
        session_regenerate_id(true);   // Prevenir fijación de sesión
        $_SESSION['usuario_id']     = $user['id'];
        $_SESSION['usuario_nombre'] = $user['nombre'];
        $_SESSION['usuario_rol']    = $user['rol'];
        $_SESSION['usuario_login']  = $user['usuario'];

        // 8. Buscar el empleado vinculado a esta cuenta (si existe) para
        //    poder registrar quién realiza cada venta.
        $empleado = $this->empleadoModel->buscarPorUsuarioId((int) $user['id']);
        if ($empleado) {
            $_SESSION['empleado_id']     = (int) $empleado['id'];
            $_SESSION['empleado_nombre'] = $empleado['nombre_completo'];
        } else {
            $_SESSION['empleado_id']     = null;
            $_SESSION['empleado_nombre'] = null;
        }
        $_SESSION['usuario_foto']   = $user['foto'] ?? '';
        $this->redirigir('/dashboard');
    }

    // ── Registro ──────────────────────────────────────────────────────────────

    /**
     * GET: muestra el formulario de registro.
     * POST: valida y crea el nuevo usuario.
     *
     * @return void
     */
    public function registro(): void
    {
        if (estaAutenticado()) {
            $this->redirigir('/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarRegistro();
            return;
        }

        $tokenCSRF = generarTokenCSRF('registro');
        $flash     = obtenerFlash();
        $old       = $this->obtenerInputAntiguo('registro'); // Rellena campos si vino de un error
        $this->render('auth/registro', [
            'titulo'    => 'Crear Cuenta',
            'tokenCSRF' => $tokenCSRF,
            'flash'     => $flash,
            'old'       => $old,
        ], 'auth');
    }

/**
     * Procesa y valida el formulario de registro.
     * Hashea la contraseña con bcrypt antes de persistir.
     *
     * @return void
     */
    private function procesarRegistro(): void
    {
        // Validar CSRF
        $token = $_POST['csrf_token'] ?? '';
        if (!validarTokenCSRF($token, 'registro')) {
            flashMensaje('error', 'Solicitud inválida.');
            $this->redirigir('/registro');
            return;
        }

        $nombre    = $this->post('nombre', 40);
        $apellido  = $this->post('apellido', 40);
        $email     = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $telefono  = $this->post('telefono', 20);
        $usuario   = $this->post('usuario', 60);
        $pass      = $_POST['contrasena']         ?? '';
        $passConf  = $_POST['contrasena_confirm'] ?? '';
        $rol       = 'dueno';

        // Validaciones
        $errores = [];
        if (empty($nombre))   $errores[] = 'El nombre es obligatorio.';
        if (empty($apellido)) $errores[] = 'El apellido es obligatorio.';
        if (!$email)          $errores[] = 'El correo electrónico no es válido.';
        if (empty($usuario))  $errores[] = 'El usuario es obligatorio.';
        
        // ── NUEVA VALIDACIÓN DE CONTRASEÑA ROBUSTA ──────────────────────────
        if (strlen($pass) < 8) {
            $errores[] = 'La contraseña debe tener mínimo 8 caracteres.';
        }
        if (!preg_match('/[A-Z]/', $pass)) {
            $errores[] = 'La contraseña debe incluir al menos una letra mayúscula.';
        }
        if (!preg_match('/[0-9]/', $pass)) {
            $errores[] = 'La contraseña debe incluir al menos un número.';
        }
        if (!preg_match('/[\W_]/', $pass)) { // \W detecta caracteres que no son letras ni números (símbolos)
            $errores[] = 'La contraseña debe incluir al menos un carácter especial (ej: @, $, !, %).';
        }
        // ───────────────────────────────────────────────────────────────────

        if ($pass !== $passConf) $errores[] = 'Las contraseñas no coinciden.';

        if (!empty($errores)) {
            foreach ($errores as $err) {
                flashMensaje('error', $err);
            }
            $this->guardarInputAntiguo('registro', $_POST);
            $this->redirigir('/registro');
            return;
        }

        // Verificar unicidad
        if ($this->usuarioModel->existeUsuario($usuario)) {
            flashMensaje('error', 'El nombre de usuario ya está en uso.');
            $this->guardarInputAntiguo('registro', $_POST);
            $this->redirigir('/registro');
            return;
        }
        if ($this->usuarioModel->existeEmail($email)) {
            flashMensaje('error', 'El correo ya está registrado.');
            $this->guardarInputAntiguo('registro', $_POST);
            $this->redirigir('/registro');
            return;
        }

        // Hash seguro de contraseña (ya usas BCRYPT correctamente aquí)
        $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);

        $id = $this->usuarioModel->crear([
            'nombre'     => $nombre,
            'apellido'   => $apellido,
            'email'      => $email,
            'telefono'   => $telefono,
            'usuario'    => $usuario,
            'contrasena' => $hash,
            'rol'        => $rol,
        ]);

        if ($id > 0) {
            flashMensaje('success', 'Cuenta creada exitosamente. Inicia sesión.');
            $this->redirigir('/login');
        } else {
            flashMensaje('error', 'Error al crear la cuenta. Intenta más tarde.');
            $this->redirigir('/registro');
        }
    }
    // ── Logout ────────────────────────────────────────────────────────────────

    /**
     * Destruye la sesión y redirige al login.
     * Limpia completamente $_SESSION y elimina la cookie de sesión.
     *
     * @return void
     */
    public function logout(): void
    {
        cerrarSesion();
        $this->redirigir('/login');
    }
}
