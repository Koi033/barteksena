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

// Tiempo de vida del token de recuperación de contraseña (5 minutos)
define("TIEMPO_VIDA", time() + 300);
