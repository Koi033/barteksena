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
}