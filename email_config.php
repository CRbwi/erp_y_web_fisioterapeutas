<?php
// Configuración del sistema de email
// Este archivo debe estar en el directorio raíz del proyecto

// Configuración del servidor SMTP
define('SMTP_HOST', 'smtp.gmail.com'); // Cambiar según tu proveedor
define('SMTP_PORT', 587); // Puerto TLS
define('SMTP_USERNAME', 'tu-email@gmail.com'); // Cambiar por tu email
define('SMTP_PASSWORD', 'tu-password-app'); // Cambiar por tu contraseña de aplicación
define('SMTP_SECURE', 'tls'); // tls o ssl

// Configuración del remitente
define('FROM_EMAIL', 'reservas@tudominio.com'); // Email de reservas
define('FROM_NAME', 'Jorge Hernandez Fisioterapeuta'); // Nombre del remitente

// Configuración de respuestas
define('REPLY_TO_EMAIL', 'jorge@tudominio.com'); // Email para respuestas
define('REPLY_TO_NAME', 'Jorge Hernandez'); // Nombre para respuestas

// Configuración de plantillas
define('EMAIL_TEMPLATE_PATH', __DIR__ . '/email_templates/');

// Función para obtener configuración SMTP
function getSMTPConfig() {
    return [
        'host' => SMTP_HOST,
        'port' => SMTP_PORT,
        'username' => SMTP_USERNAME,
        'password' => SMTP_PASSWORD,
        'secure' => SMTP_SECURE
    ];
}

// Función para obtener configuración del remitente
function getSenderConfig() {
    return [
        'from_email' => FROM_EMAIL,
        'from_name' => FROM_NAME,
        'reply_to_email' => REPLY_TO_EMAIL,
        'reply_to_name' => REPLY_TO_NAME
    ];
}
?>
