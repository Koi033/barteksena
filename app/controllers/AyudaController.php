<?php
require_once BASE_PATH . '/app/controllers/BaseController.php';

class AyudaController extends BaseController
{
    /** GET /ayuda — Manual de usuario */
    public function index(): void
    {
        requerirAutenticacion();

        $this->render('ayuda.php/index', [
            'titulo'     => 'Ayuda - Bartek',
            'rolUsuario' => $_SESSION['usuario_rol'] ?? '',
            'flash'      => obtenerFlash(),
        ]);
    }
}