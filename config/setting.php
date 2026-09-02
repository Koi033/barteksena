<?php
/**
 * config/setting.php
 * Configuración del servidor SMTP para envío de correos (PHPMailer).
 * Las credenciales NUNCA deben quedar hardcodeadas en el código fuente.
 * Se cargan desde variables de entorno; los valores tras "?:" son solo
 * un fallback para entorno local de desarrollo.
 *
 * @package Bartek
 */

define("HOST",         getenv('SMTP_HOST')     ?: 'smtp.gmail.com');
define("USERNAME",     getenv('SMTP_USERNAME')  ?: '');
define("PASSWORD",     getenv('SMTP_PASSWORD')  ?: '');
define("PORT",          (int) (getenv('SMTP_PORT') ?: 587));
define("SMTP_SECURE",  getenv('SMTP_SECURE')    ?: 'TLS');

/**
 * Envío de correo vía API HTTP de Brevo (antes Sendinblue).
 * Se usa en vez de SMTP porque plataformas como Render bloquean el
 * tráfico saliente a los puertos SMTP (25/465/587) en sus planes
 * gratuitos; la API de Brevo viaja por HTTPS (puerto 443), que nunca
 * se bloquea.
 * Crea una cuenta gratis en https://www.brevo.com, verifica un
 * remitente y genera la API key en SMTP & API > API Keys.
 */
define("BREVO_API_KEY",      getenv('BREVO_API_KEY')      ?: '');
define("BREVO_SENDER_EMAIL", getenv('BREVO_SENDER_EMAIL') ?: '');
define("BREVO_SENDER_NAME",  getenv('BREVO_SENDER_NAME')  ?: 'Bartek App');

// Tiempo de vida del token de recuperación de contraseña (5 minutos)
define("TIEMPO_VIDA", time() + 300);
