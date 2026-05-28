<?php
/**
 * app/controllers/PerfilController.php
 * Controlador para que el usuario autenticado vea y edite su propio perfil.
 * Permite cambiar nombre, email, teléfono y contraseña.
 *
 * @package Bartek\Controllers
 */
require_once BASE_PATH . '/app/controllers/BaseController.php';

class PerfilController extends BaseController
{
    /** @var UsuarioModel Modelo de usuarios */
    private UsuarioModel $modelo;

    public function __construct()
    {
        $this->modelo = new UsuarioModel();
    }

    /**
     * Muestra el formulario de perfil con los datos actuales del usuario.
     * GET /perfil
     *
     * @return void
     */
    public function index(): void
    {
        requerirAutenticacion();

        $usuario   = $this->modelo->buscarPorUsuario($_SESSION['usuario_login']);
        $tokenCSRF = generarTokenCSRF('perfil');
        $flash     = obtenerFlash();

        $this->render('perfil/index', [
            'titulo'    => 'Mi Perfil - Bartek',
            'usuario'   => $usuario,
            'tokenCSRF' => $tokenCSRF,
            'flash'     => $flash,
        ]);
    }

    /**
     * Procesa la actualización de datos del perfil.
     * POST /perfil/actualizar
     *
     * @return void
     */
    public function actualizar(): void
    {
        requerirAutenticacion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/perfil');
        }

        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'perfil')) {
            flashMensaje('error', 'Token inválido. Recarga la página.');
            $this->redirigir('/perfil');
            return;
        }

        $nombre   = $this->post('nombre', 100);
        $email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $telefono = $this->post('telefono', 20);

        if (empty($nombre) || !$email) {
            flashMensaje('error', 'Nombre y correo son obligatorios y deben ser válidos.');
            $this->redirigir('/perfil');
            return;
        }

        $id  = (int)$_SESSION['usuario_id'];
        $pdo = Database::obtenerInstancia()->obtenerConexion();

        // Actualizar datos generales
        $sql  = 'UPDATE usuarios SET nombre = :n, email = :e, telefono = :t WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':n' => $nombre, ':e' => $email, ':t' => $telefono, ':id' => $id]);

        // Actualizar nombre en sesión
        $_SESSION['usuario_nombre'] = $nombre;

        // Cambio de contraseña (opcional)
        $passActual = $_POST['pass_actual']   ?? '';
        $passNueva  = $_POST['pass_nueva']    ?? '';
        $passConf   = $_POST['pass_confirmar'] ?? '';

        if (!empty($passNueva)) {
            $usuario = $this->modelo->buscarPorUsuario($_SESSION['usuario_login']);
            if (!$usuario || !password_verify($passActual, $usuario['contrasena'])) {
                flashMensaje('error', 'La contraseña actual no es correcta.');
                $this->redirigir('/perfil');
                return;
            }
            if (strlen($passNueva) < 8) {
                flashMensaje('error', 'La nueva contraseña debe tener al menos 8 caracteres.');
                $this->redirigir('/perfil');
                return;
            }
            if ($passNueva !== $passConf) {
                flashMensaje('error', 'Las nuevas contraseñas no coinciden.');
                $this->redirigir('/perfil');
                return;
            }

            $hash   = password_hash($passNueva, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
            $stmtP  = $pdo->prepare('UPDATE usuarios SET contrasena = :h WHERE id = :id');
            $stmtP->execute([':h' => $hash, ':id' => $id]);
        }

        flashMensaje('success', 'Perfil actualizado correctamente.');
        $this->redirigir('/perfil');
    }
}
