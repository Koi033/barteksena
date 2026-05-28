<?php
/**
 * app/controllers/ContactoController.php
 * Gestión del formulario de contacto público.
 *
 * @package Bartek\Controllers
 */
require_once BASE_PATH . '/app/controllers/BaseController.php';

class ContactoController extends BaseController
{
    private ContactoModel $modelo;

    public function __construct()
    {
        $this->modelo = new ContactoModel();
    }

    /**
     * GET: muestra el formulario de contacto.
     * POST: procesa y guarda el mensaje.
     *
     * @return void
     */
    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarContacto();
            return;
        }

        $tokenCSRF = generarTokenCSRF('contacto');
        $this->render('public/contacto', [
            'titulo'    => 'Contacto - Bartek',
            'tokenCSRF' => $tokenCSRF,
            'flash'     => obtenerFlash(),
        ], 'public');
    }

    /**
     * Valida y persiste el mensaje de contacto.
     * Previene spam básico con validación de campos y CSRF.
     *
     * @return void
     */
    private function procesarContacto(): void
    {
        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'contacto')) {
            flashMensaje('error', 'Solicitud inválida.');
            $this->redirigir('/contacto');
            return;
        }

        $nombre  = $this->post('nombre', 100);
        $correo  = filter_input(INPUT_POST, 'correo', FILTER_VALIDATE_EMAIL);
        $mensaje = $this->post('mensaje', 2000);

        if (empty($nombre) || !$correo || empty($mensaje)) {
            flashMensaje('error', 'Todos los campos son obligatorios y deben ser válidos.');
            $this->redirigir('/contacto');
            return;
        }

        $id = $this->modelo->guardar([
            'nombre'  => $nombre,
            'correo'  => $correo,
            'mensaje' => $mensaje,
        ]);

        if ($id > 0) {
            flashMensaje('success', '¡Mensaje enviado! Nos pondremos en contacto pronto.');
        } else {
            flashMensaje('error', 'Error al enviar el mensaje. Intenta más tarde.');
        }
        $this->redirigir('/contacto');
    }
}
