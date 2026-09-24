<?php
/**
 * app/controllers/ConfiguracionPuntosController.php
 * Panel de administración del sistema de puntos: reglas de acumulación
 * (dinero/productos necesarios) y catálogo de recompensas. Solo accesible
 * para el rol 'dueno'.
 *
 * @package Bartek\Controllers
 */
require_once BASE_PATH . '/app/controllers/BaseController.php';
require_once BASE_PATH . '/app/models/ConfiguracionPuntosModel.php';

class ConfiguracionPuntosController extends BaseController
{
    private ConfiguracionPuntosModel $modelo;

    public function __construct()
    {
        $this->modelo = new ConfiguracionPuntosModel();
    }

    /**
     * Muestra la configuración general de acumulación de puntos y el
     * catálogo de recompensas.
     * GET /configuracion-puntos
     *
     * @return void
     */
    public function index(): void
    {
        requerirRol('dueno');

        $this->render('configuracion_puntos/index', [
            'titulo'       => 'Configuración de Puntos - Bartek',
            'configuracion' => $this->modelo->obtenerConfiguracion(),
            'recompensas'  => $this->modelo->obtenerRecompensas(),
            'tokenCSRF'    => generarTokenCSRF('configuracion_puntos'),
            'flash'        => obtenerFlash(),
        ]);
    }

    /**
     * Guarda la regla general de acumulación de puntos.
     * POST /configuracion-puntos/guardar
     *
     * @return void
     */
    public function guardar(): void
    {
        requerirRol('dueno');

        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'configuracion_puntos')) {
            flashMensaje('error', 'Token inválido. Intenta de nuevo.');
            $this->redirigir('/configuracion-puntos');
            return;
        }

        $modo = $this->post('modo_acumulacion', 20);
        if (!in_array($modo, ['dinero', 'productos'], true)) {
            $modo = 'dinero';
        }

        $valorAcumulacion = (float) str_replace(',', '.', $_POST['valor_acumulacion'] ?? '0');
        $puntosOtorgados  = max(1, $this->entero('puntos_otorgados', 'post', 1));
        $puntosBienvenida = max(0, $this->entero('puntos_bienvenida', 'post', 0));

        if ($valorAcumulacion <= 0) {
            flashMensaje('error', 'El valor de acumulación debe ser mayor a cero.');
            $this->redirigir('/configuracion-puntos');
            return;
        }

        $ok = $this->modelo->guardarConfiguracion($modo, $valorAcumulacion, $puntosOtorgados, $puntosBienvenida);

        flashMensaje(
            $ok ? 'success' : 'error',
            $ok ? 'Configuración de puntos actualizada correctamente.' : 'No se pudo guardar la configuración.'
        );
        $this->redirigir('/configuracion-puntos');
    }

    /**
     * Crea o actualiza una recompensa del catálogo.
     * POST /configuracion-puntos/recompensa/guardar
     *
     * @return void
     */
    public function guardarRecompensa(): void
    {
        requerirRol('dueno');

        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'configuracion_puntos')) {
            flashMensaje('error', 'Token inválido. Intenta de nuevo.');
            $this->redirigir('/configuracion-puntos');
            return;
        }

        $id = $this->entero('id', 'post');
        $nombre = $this->post('nombre', 100);
        $descripcion = $this->post('descripcion', 255);
        $puntosRequeridos = $this->entero('puntos_requeridos', 'post');
        $activo = isset($_POST['activo']);

        if (empty($nombre) || $puntosRequeridos <= 0) {
            flashMensaje('error', 'Debes indicar un nombre y una cantidad de puntos requeridos válida.');
            $this->redirigir('/configuracion-puntos');
            return;
        }

        if ($id > 0) {
            $ok = $this->modelo->actualizarRecompensa($id, $nombre, $descripcion, $puntosRequeridos, $activo);
            $mensajeOk = 'Recompensa actualizada correctamente.';
        } else {
            $ok = $this->modelo->crearRecompensa($nombre, $descripcion, $puntosRequeridos, $activo) > 0;
            $mensajeOk = 'Recompensa creada correctamente.';
        }

        flashMensaje($ok ? 'success' : 'error', $ok ? $mensajeOk : 'No se pudo guardar la recompensa.');
        $this->redirigir('/configuracion-puntos');
    }

    /**
     * Elimina una recompensa del catálogo.
     * POST /configuracion-puntos/recompensa/eliminar
     *
     * @return void
     */
    public function eliminarRecompensa(): void
    {
        requerirRol('dueno');

        if (!validarTokenCSRF($_POST['csrf_token'] ?? '', 'configuracion_puntos')) {
            flashMensaje('error', 'Token inválido. Intenta de nuevo.');
            $this->redirigir('/configuracion-puntos');
            return;
        }

        $id = $this->entero('id', 'post');

        if ($id > 0) {
            $this->modelo->eliminarRecompensa($id);
            flashMensaje('success', 'Recompensa eliminada.');
        }

        $this->redirigir('/configuracion-puntos');
    }
}
