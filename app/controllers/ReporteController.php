<?php
/**
 * app/controllers/ReporteController.php
 * Genera reportes en formato CSV descargables desde el panel.
 * Todos los reportes requieren autenticación.
 * Los datos se escapan para prevenir CSV Injection (fórmulas maliciosas).
 *
 * @package Bartek\Controllers
 */
require_once BASE_PATH . '/app/controllers/BaseController.php';

class ReporteController extends BaseController
{
    private EmpleadoModel  $empModel;
    private InventarioModel $invModel;
    private VentaModel     $ventaModel;
    private HorarioModel   $horarioModel;

    public function __construct()
    {
        $this->empModel     = new EmpleadoModel();
        $this->invModel     = new InventarioModel();
        $this->ventaModel   = new VentaModel();
        $this->horarioModel = new HorarioModel();
    }

    /**
     * Muestra la página de selección de reportes disponibles.
     * GET /reportes
     *
     * @return void
     */
    public function index(): void
    {
        requerirAutenticacion();
        $this->render('reportes/index', [
            'titulo'    => 'Reportes - Bartek',
            'tokenCSRF' => generarTokenCSRF('reporte'),
            'flash'     => obtenerFlash(),
        ]);
    }

    /**
     * Exporta el listado completo de empleados activos a CSV.
     * GET /reportes/empleados
     *
     * @return void
     */
    public function empleados(): void
    {
        requerirAutenticacion();

        $filas = $this->empModel->obtenerTodos('', '', 1, 9999);

        $cabeceras = ['ID', 'Nombre Completo', 'Puesto', 'Departamento', 'Email', 'Teléfono'];
        $datos     = array_map(fn($r) => [
            $r['id'],
            $r['nombre_completo'],
            $r['puesto'],
            $r['departamento'],
            $r['email'],
            $r['telefono'] ?? '',
        ], $filas);

        $this->descargarCSV('bartek_empleados_' . date('Ymd'), $cabeceras, $datos);
    }

    /**
     * Exporta el inventario completo a CSV.
     * GET /reportes/inventario
     *
     * @return void
     */
    public function inventario(): void
    {
        requerirAutenticacion();

        $filas     = $this->invModel->obtenerTodos('', 0, 1, 9999);
        $cabeceras = ['Código', 'Nombre', 'Categoría', 'Stock Actual', 'Stock Mínimo', 'Precio'];
        $datos     = array_map(fn($r) => [
            $r['codigo'],
            $r['nombre'],
            $r['categoria'],
            $r['stock_actual'],
            $r['stock_minimo'],
            $r['precio_unitario'],
        ], $filas);

        $this->descargarCSV('bartek_inventario_' . date('Ymd'), $cabeceras, $datos);
    }

    /**
     * Exporta el historial de ventas a CSV.
     * GET /reportes/ventas
     *
     * @return void
     */
    public function ventas(): void
    {
        requerirAutenticacion();

        $filas     = $this->ventaModel->obtenerTodos(1, 9999);
        $cabeceras = ['ID', 'Mesa/Cliente', 'Empleado', 'Total', 'Estado', 'Fecha'];
        $datos     = array_map(fn($r) => [
            $r['id'],
            $r['mesa'] ?? '',
            $r['empleado'],
            $r['total'],
            $r['estado'],
            $r['creado_en'],
        ], $filas);

        $this->descargarCSV('bartek_ventas_' . date('Ymd'), $cabeceras, $datos);
    }

    /**
     * Exporta el listado de horarios a CSV.
     * GET /reportes/horarios
     *
     * @return void
     */
    public function horarios(): void
    {
        requerirAutenticacion();

        $filas     = $this->horarioModel->obtenerTodos(1, 9999);
        $cabeceras = ['ID', 'Empleado', 'Fecha', 'Hora Inicio', 'Hora Fin', 'Estado'];
        $datos     = array_map(fn($r) => [
            $r['id'],
            $r['nombre_completo'],
            $r['fecha'],
            $r['hora_inicio'],
            $r['hora_fin'],
            $r['estado'],
        ], $filas);

        $this->descargarCSV('bartek_horarios_' . date('Ymd'), $cabeceras, $datos);
    }

    /**
     * Genera y envía un archivo CSV al navegador para su descarga.
     * Escapa valores que comiencen con =, +, -, @ para prevenir CSV Injection.
     *
     * @param  string $nombre    Nombre del archivo sin extensión
     * @param  array  $cabeceras Fila de encabezados
     * @param  array  $filas     Filas de datos
     * @return void
     */
    private function descargarCSV(string $nombre, array $cabeceras, array $filas): void
    {
        // Limpiar cualquier salida anterior
        if (ob_get_length()) {
            ob_end_clean();
        }

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $nombre . '.csv"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // BOM UTF-8 para que Excel abra correctamente con acentos
        echo "\xEF\xBB\xBF";

        $salida = fopen('php://output', 'w');

        // Escribir cabeceras
        fputcsv($salida, $cabeceras, ',', '"');

        // Escribir filas, escapando valores peligrosos para CSV Injection
        foreach ($filas as $fila) {
            $filaSegura = array_map([$this, 'escaparCsvValor'], $fila);
            fputcsv($salida, $filaSegura, ',', '"');
        }

        fclose($salida);
        exit;
    }

    /**
     * Escapa un valor de celda para prevenir CSV Injection.
     * Si el valor comienza con =, +, -, @ o TAB, se antepone un apóstrofo.
     *
     * @param  mixed  $valor Valor de la celda
     * @return string        Valor escapado
     */
    private function escaparCsvValor(mixed $valor): string
    {
        $str = (string) $valor;
        // Caracteres que inician fórmulas en Excel/LibreOffice
        if (preg_match('/^[=+\-@\t]/', $str)) {
            $str = "'" . $str;
        }
        return $str;
    }
}
