<?php
/**
 * app/controllers/BaseController.php
 * Clase base para todos los controladores del sistema Bartek.
 * Provee el método render() que carga las vistas con el layout principal.
 *
 * @package Bartek\Controllers
 */

class BaseController
{
    /**
     * Renderiza una vista dentro del layout correspondiente.
     * Extrae las variables del array $datos para disponerlas en la vista.
     *
     * @param  string $vista  Ruta relativa de la vista (ej: 'empleados/index')
     * @param  array  $datos  Variables a pasar a la vista
     * @param  string $layout Layout a usar: 'dashboard' | 'public' | 'auth'
     * @return void
     */
    protected function render(string $vista, array $datos = [], string $layout = 'dashboard'): void
    {
        // Detecta automáticamente el módulo de CSS a partir del primer
        if (!isset($datos['cssModulo'])) {
            $segmentoVista = explode('/', $vista)[0] ?? '';
            $datos['cssModulo'] = $segmentoVista;
        }

        // Hacer disponibles las variables en el scope de la vista
        extract($datos, EXTR_SKIP);   // EXTR_SKIP evita sobrescribir variables existentes

        // Ruta completa de la vista
        $rutaVista  = BASE_PATH . '/app/views/' . $vista . '.php';
        $rutaLayout = BASE_PATH . '/app/views/layouts/' . $layout . '.php';

        if (!file_exists($rutaVista)) {
            http_response_code(404);
            die('Vista no encontrada: ' . htmlspecialchars($vista, ENT_QUOTES, 'UTF-8'));
        }

        if (!file_exists($rutaLayout)) {
            // Sin layout, renderizar sólo la vista
            require $rutaVista;
            return;
        }

        // Capturar el contenido de la vista para insertarlo en el layout
        ob_start();
        require $rutaVista;
        $contenido = ob_get_clean();

        // Cargar el layout (que usará $contenido)
        require $rutaLayout;
    }

    /**
     * Redirige a una URL relativa al BASE_URL.
     *
     * @param  string $ruta Ruta relativa (ej: '/login', '/empleados')
     * @return void
     */
    protected function redirigir(string $ruta): void
    {
        header('Location: ' . BASE_URL . $ruta);
        exit;
    }

    /**
     * Retorna una respuesta JSON (para peticiones AJAX).
     *
     * @param  mixed $datos  Datos a serializar
     * @param  int   $codigo Código de estado HTTP
     * @return void
     */
    protected function json(mixed $datos, int $codigo = 200): void
    {
        http_response_code($codigo);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Muestra una página de error personalizada (404, 403, 500, etc.)
     * usando la vista genérica 'public/error'.
     *
     * @param  int    $codigo  Código HTTP a mostrar (404, 403, 500...)
     * @param  string $mensaje Mensaje personalizado (opcional, sobreescribe el texto por defecto)
     * @return void
     */
    protected function renderError(int $codigo, string $mensaje = ''): void
    {
        http_response_code($codigo);

        $textos = [
            400 => ['titulo' => 'Solicitud incorrecta',   'mensaje' => 'La solicitud tiene datos inválidos o mal formados.'],
            401 => ['titulo' => 'No autenticado',         'mensaje' => 'Debes iniciar sesión para acceder a esta sección.'],
            403 => ['titulo' => 'Acceso denegado',        'mensaje' => 'No tienes permisos para acceder a esta sección.'],
            404 => ['titulo' => 'Página no encontrada',   'mensaje' => 'La página que buscas no existe o fue movida.'],
            500 => ['titulo' => 'Error interno',          'mensaje' => 'Ocurrió un error inesperado. Ya estamos trabajando en ello.'],
            503 => ['titulo' => 'Servicio no disponible', 'mensaje' => 'Estamos en mantenimiento. Vuelve a intentarlo en unos minutos.'],
        ];

        $data = [
            'codigo'  => $codigo,
            'titulo'  => $textos[$codigo]['titulo'] ?? 'Error',
            'mensaje' => $mensaje !== '' ? $mensaje : ($textos[$codigo]['mensaje'] ?? 'Ha ocurrido un error inesperado.'),
        ];

        $this->render('public/error', $data, 'public');
    }

    /**
     * Muestra la página 404 personalizada.
     * Mantiene compatibilidad con código existente que ya llama a este método.
     *
     * @return void
     */
    public function paginaNoEncontrada(): void
    {
        $this->renderError(404);
    }

    /**
     * Muestra la página 403 (acceso denegado) personalizada.
     *
     * @return void
     */
    public function accesoDenegado(): void
    {
        $this->renderError(403);
    }

    /**
     * Muestra la página 500 (error interno) personalizada.
     * Acepta null explícito (ej: cuando el router llama al método
     * sin haber recibido un tercer segmento en la URL).
     *
     * @param  string|null $mensaje Mensaje personalizado opcional
     * @return void
     */
    public function errorInterno(?string $mensaje = null): void
    {
        $this->renderError(500, $mensaje ?? '');
    }

    /**
     * Muestra la página 400 (solicitud incorrecta) personalizada.
     * Útil para datos de formulario inválidos o JSON mal formado
     * en los endpoints de public/api/ (check_mesa.php, crear_reserva.php).
     *
     * @param  string|null $mensaje Mensaje personalizado opcional
     * @return void
     */
    public function solicitudIncorrecta(?string $mensaje = null): void
    {
        $this->renderError(400, $mensaje ?? '');
    }

    /**
     * Muestra la página 401 (no autenticado) personalizada.
     * Diferencia de accesoDenegado(): esta es para cuando NO hay
     * sesión iniciada; 403 es cuando SÍ hay sesión pero sin el rol requerido.
     *
     * @return void
     */
    public function noAutenticado(): void
    {
        $this->renderError(401);
    }

    /**
     * Muestra la página 503 (servicio no disponible) personalizada.
     * Útil cuando la conexión a la BD falla o durante mantenimiento programado.
     *
     * @param  string|null $mensaje Mensaje personalizado opcional
     * @return void
     */
    public function servicioNoDisponible(?string $mensaje = null): void
    {
        $this->renderError(503, $mensaje ?? '');
    }

    /**
     * Valida y sanea un campo de texto plano.
     *
     * @param  string $valor    Valor crudo del formulario
     * @param  int    $maxLen   Longitud máxima permitida
     * @return string           Valor saneado
     */
    protected function limpiarTexto(string $valor, int $maxLen = 255): string
    {
        return mb_substr(
            htmlspecialchars(strip_tags(trim($valor)), ENT_QUOTES, 'UTF-8'),
            0,
            $maxLen
        );
    }

    /**
     * Obtiene y sanea un campo POST.
     *
     * @param  string $campo    Nombre del campo
     * @param  int    $maxLen   Longitud máxima permitida
     * @param  string $default  Valor por defecto si no existe
     * @return string
     */
    protected function post(string $campo, int $maxLen = 255, string $default = ''): string
    {
        return $this->limpiarTexto($_POST[$campo] ?? $default, $maxLen);
    }

    /**
     * Obtiene y sanea un campo GET.
     *
     * @param  string $campo   Nombre del campo
     * @param  int    $maxLen  Longitud máxima permitida
     * @param  string $default Valor por defecto
     * @return string
     */
    protected function get(string $campo, int $maxLen = 255, string $default = ''): string
    {
        return $this->limpiarTexto($_GET[$campo] ?? $default, $maxLen);
    }

    /**
     * Obtiene un entero de GET o POST, útil para IDs y paginación.
     *
     * @param  string $campo   Nombre del campo
     * @param  string $fuente  'get' | 'post'
     * @param  int    $default Valor por defecto
     * @return int
     */
    protected function entero(string $campo, string $fuente = 'get', int $default = 0): int
    {
        $valor = ($fuente === 'post') ? ($_POST[$campo] ?? $default) : ($_GET[$campo] ?? $default);
        return (int) filter_var($valor, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Guarda en sesión los datos del formulario para poder rellenarlos
     * de nuevo si la validación falla, evitando que el usuario tenga
     * que volver a escribir todo. Se guarda por 'clave' para no chocar
     * entre formularios distintos (ej: 'registro', 'contacto').
     *
     * NUNCA se guardan campos sensibles como contraseñas o tokens CSRF.
     *
     * @param  string $clave    Identificador del formulario (ej: 'registro')
     * @param  array  $datos    Normalmente $_POST
     * @param  array  $excluir  Nombres de campos a excluir (además de los sensibles por defecto)
     * @return void
     */
    protected function guardarInputAntiguo(string $clave, array $datos, array $excluir = []): void
    {
        $camposSensibles = array_merge(
            ['csrf_token', 'contrasena', 'contrasena_confirm', 'password', 'password_confirm'],
            $excluir
        );

        foreach ($camposSensibles as $campo) {
            unset($datos[$campo]);
        }

        $_SESSION['_old_input'][$clave] = $datos;
    }

    /**
     * Recupera y CONSUME (borra tras leer) el input anterior guardado
     * para un formulario. Así solo se rellena una vez, justo después
     * del error, y no queda "pegado" en visitas posteriores.
     *
     * @param  string $clave Identificador del formulario (ej: 'registro')
     * @return array         Array asociativo campo => valor (vacío si no había nada)
     */
    protected function obtenerInputAntiguo(string $clave): array
    {
        $datos = $_SESSION['_old_input'][$clave] ?? [];
        unset($_SESSION['_old_input'][$clave]);
        return $datos;
    }
}
