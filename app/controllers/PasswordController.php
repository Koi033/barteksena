<?php
/**
 * app/controllers/PasswordController.php
 * Controlador para recuperación y restablecimiento de contraseña.
 *
 * Flujo completo:
 *   1. GET  /recuperar          → formulario "ingresa tu email"
 *   2. POST /recuperar          → valida email, genera token, envía correo
 *   3. GET  /restablecer?token= → formulario "nueva contraseña" (valida token)
 *   4. POST /restablecer        → guarda nueva contraseña, limpia token
 *
 * @package Bartek\Controllers
 */

require_once BASE_PATH . '/app/controllers/BaseController.php';

class PasswordController extends BaseController
{
    /** @var UsuarioModel */
    private UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    // ── PASO 1 y 2: Solicitar recuperación ───────────────────────────────────

    /**
     * GET  → muestra formulario de email.
     * POST → procesa solicitud y envía correo.
     */
    public function recuperar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarSolicitud();
            return;
        }

        $tokenCSRF = generarTokenCSRF('recuperar');
        $flash     = obtenerFlash();
        $this->render('auth/recuperar_password', [
            'titulo'    => 'Recuperar Contraseña',
            'tokenCSRF' => $tokenCSRF,
            'flash'     => $flash,
        ], 'auth');
    }

    /**
     * Valida el email, genera el token y envía el correo de recuperación.
     */
    private function procesarSolicitud(): void
    {
        // 1. Validar CSRF
        $token = $_POST['csrf_token'] ?? '';
        if (!validarTokenCSRF($token, 'recuperar')) {
            flashMensaje('error', 'Solicitud inválida. Recarga la página.');
            $this->redirigir(BASE_URL . '/recuperar');
            return;
        }

        // 2. Validar email
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if (!$email) {
            flashMensaje('error', 'Ingresa un correo electrónico válido.');
            $this->redirigir(BASE_URL . '/recuperar');
            return;
        }

        // 3. Buscar usuario — mismo mensaje aunque no exista (evita enumeración)
        $usuario = $this->usuarioModel->buscarPorEmail($email);

        if ($usuario) {
            // 4. Generar token seguro
            $tokenReset = bin2hex(random_bytes(32));   // 64 caracteres hex
            $expira     = time() + 300;                // 5 minutos (igual que TIEMPO_VIDA)

            // 5. Guardar token en BD
            $guardado = $this->usuarioModel->guardarTokenReset(
                (int)$usuario['id'],
                $tokenReset,
                $expira
            );

            // 6. Enviar correo solo si se guardó el token
            if ($guardado) {
                $this->enviarCorreoReset(
                    $usuario['email'],
                    $usuario['nombre'],
                    $tokenReset
                );
            }
        }

        // Siempre el mismo mensaje (no revela si el email existe)
        flashMensaje('success', 'Si el correo está registrado, recibirás las instrucciones en breve.');
        $this->redirigir(BASE_URL . '/recuperar');
    }

    // ── PASO 3 y 4: Restablecer contraseña ───────────────────────────────────

    /**
     * GET  → valida token y muestra formulario de nueva contraseña.
     * POST → guarda la nueva contraseña.
     */
    public function restablecer(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarRestablecimiento();
            return;
        }

        // Validar token en URL
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            flashMensaje('error', 'Enlace de recuperación inválido.');
            $this->redirigir(BASE_URL . '/recuperar');
            return;
        }

        $usuario = $this->usuarioModel->buscarPorTokenReset($token);
        if (!$usuario) {
            flashMensaje('error', 'El enlace ha expirado o no es válido. Solicita uno nuevo.');
            $this->redirigir(BASE_URL . '/recuperar');
            return;
        }

        $tokenCSRF = generarTokenCSRF('restablecer');
        $flash     = obtenerFlash();
        $this->render('auth/restablecer_password', [
            'titulo'      => 'Nueva Contraseña',
            'tokenCSRF'   => $tokenCSRF,
            'flash'        => $flash,
            'tokenReset'  => htmlspecialchars($token, ENT_QUOTES, 'UTF-8'),
        ], 'auth');
    }

    /**
     * Valida la nueva contraseña, la hashea y la guarda en BD.
     */
    private function procesarRestablecimiento(): void
    {
        // 1. Validar CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!validarTokenCSRF($csrfToken, 'restablecer')) {
            flashMensaje('error', 'Solicitud inválida. Recarga la página.');
            $this->redirigir(BASE_URL . '/recuperar');
            return;
        }

        // 2. Leer campos
        $tokenReset  = $_POST['token']              ?? '';
        $pass        = $_POST['contrasena']         ?? '';
        $passConfirm = $_POST['contrasena_confirm'] ?? '';

        // 3. Validar token (segunda verificación en POST)
        if (empty($tokenReset)) {
            flashMensaje('error', 'Token inválido.');
            $this->redirigir(BASE_URL . '/recuperar');
            return;
        }

        $usuario = $this->usuarioModel->buscarPorTokenReset($tokenReset);
        if (!$usuario) {
            flashMensaje('error', 'El enlace ha expirado. Solicita uno nuevo.');
            $this->redirigir(BASE_URL . '/recuperar');
            return;
        }

        // 4. Validar contraseña (mismas reglas que el registro)
        $errores = [];
        if (strlen($pass) < 8) {
            $errores[] = 'La contraseña debe tener mínimo 8 caracteres.';
        }
        if (!preg_match('/[A-Z]/', $pass)) {
            $errores[] = 'Debe incluir al menos una letra mayúscula.';
        }
        if (!preg_match('/[0-9]/', $pass)) {
            $errores[] = 'Debe incluir al menos un número.';
        }
        if (!preg_match('/[\W_]/', $pass)) {
            $errores[] = 'Debe incluir al menos un carácter especial (ej: @, $, !).';
        }
        if ($pass !== $passConfirm) {
            $errores[] = 'Las contraseñas no coinciden.';
        }

        if (!empty($errores)) {
            foreach ($errores as $err) {
                flashMensaje('error', $err);
            }
            $this->redirigir(BASE_URL . '/restablecer?token=' . urlencode($tokenReset));
            return;
        }

        // 5. Hashear y guardar
        $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
        $ok   = $this->usuarioModel->actualizarContrasena((int)$usuario['id'], $hash);

        if ($ok) {
            flashMensaje('success', 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.');
            $this->redirigir(BASE_URL . '/login');
        } else {
            flashMensaje('error', 'Error al actualizar la contraseña. Intenta de nuevo.');
            $this->redirigir(BASE_URL . '/recuperar');
        }
    }

    // ── Envío de correo ───────────────────────────────────────────────────────

    /**
     * Envía el correo con el enlace de restablecimiento usando la API HTTP
     * de Brevo (https://api.brevo.com), en vez de SMTP.
     *
     * Nota: Render (y otras plataformas gratuitas) bloquean el tráfico
     * saliente a los puertos SMTP en sus planes gratis, lo que hacía que
     * esta petición se quedara "colgada" cargando indefinidamente. La API
     * de Brevo viaja por HTTPS (puerto 443), que sí está permitido, y
     * además se usa un timeout corto para fallar rápido si algo sale mal.
     *
     * @param  string $correo        Email del destinatario
     * @param  string $nombre        Nombre del destinatario
     * @param  string $tokenReset    Token generado
     * @return void
     */
    private function enviarCorreoReset(string $correo, string $nombre, string $tokenReset): void
    {
        // URL base — ajusta APP_URL en config/app.php si necesitas un dominio fijo.
        if (defined('APP_URL')) {
            $baseUrl = APP_URL;
        } else {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === '443'
                ? 'https'
                : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $baseUrl = $protocol . '://' . $host . BASE_URL;
        }

        $enlace = $baseUrl . '/restablecer?token=' . urlencode($tokenReset);

        if (empty(BREVO_API_KEY) || empty(BREVO_SENDER_EMAIL)) {
            error_log('[Bartek][Mail] Faltan BREVO_API_KEY o BREVO_SENDER_EMAIL en las variables de entorno.');
            return;
        }

        $payload = json_encode([
            'sender'      => ['name' => BREVO_SENDER_NAME, 'email' => BREVO_SENDER_EMAIL],
            'to'          => [['email' => $correo, 'name' => $nombre]],
            'subject'     => 'Recuperación de contraseña - Bartek',
            'htmlContent' => $this->plantillaCorreo($nombre, $enlace),
            'textContent' => "Hola $nombre, visita este enlace para restablecer tu contraseña (válido 5 minutos): $enlace",
        ]);

        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'accept: application/json',
                'api-key: ' . BREVO_API_KEY,
                'content-type: application/json',
            ],
            // Timeouts cortos: si algo falla, falla rápido en vez de dejar la petición colgada.
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT        => 12,
        ]);

        $respuesta  = curl_exec($ch);
        $codigoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errorCurl  = curl_error($ch);
        curl_close($ch);

        if ($errorCurl || $codigoHttp >= 300) {
            // Solo log — no exponemos el error al usuario
            error_log('[Bartek][Mail] Error al enviar correo de reset (HTTP ' . $codigoHttp . '): ' . ($errorCurl ?: $respuesta));
        }
    }

    /**
     * Genera el HTML del correo de recuperación.
     *
     * @param  string $nombre  Nombre del usuario
     * @param  string $enlace  URL de restablecimiento
     * @return string          HTML del correo
     */
    private function plantillaCorreo(string $nombre, string $enlace): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head><meta charset="UTF-8"></head>
        <body style="font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:20px;">
          <div style="max-width:520px; margin:auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.1);">
            <div style="background:#1a1a2e; padding:28px 32px;">
              <h1 style="color:#fff; margin:0; font-size:22px;">Bartek App</h1>
            </div>
            <div style="padding:32px;">
              <p style="color:#333; font-size:16px;">Hola, <strong>$nombre</strong>.</p>
              <p style="color:#555; font-size:14px; line-height:1.6;">
                Recibimos una solicitud para restablecer tu contraseña.<br>
                El enlace es válido por <strong>5 minutos</strong>.
              </p>
              <div style="text-align:center; margin:32px 0;">
                <a href="$enlace"
                   style="background:#e94560; color:#fff; padding:14px 32px;
                          border-radius:6px; text-decoration:none; font-size:15px;
                          font-weight:bold; display:inline-block;">
                  Restablecer contraseña
                </a>
              </div>
              <p style="color:#999; font-size:12px;">
                Si no solicitaste esto, ignora este correo. Tu contraseña no cambiará.
              </p>
            </div>
          </div>
        </body>
        </html>
        HTML;
    }
}
