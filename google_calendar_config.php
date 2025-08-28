<?php
// Configuración para Google Calendar API
// Este archivo debe estar en el directorio raíz del proyecto

// Credenciales de Google Calendar API
define('GOOGLE_CLIENT_ID', 'tu-client-id.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'tu-client-secret');
define('GOOGLE_REDIRECT_URI', 'http://tudominio.com/jorge/google_calendar_auth.php');

// ID del calendario de Google Calendar
define('GOOGLE_CALENDAR_ID', 'primary'); // 'primary' para el calendario principal, o el ID específico

// Configuración de sincronización
define('SYNC_PAST_DAYS', 30); // Días hacia atrás para sincronizar
define('SYNC_FUTURE_DAYS', 90); // Días hacia adelante para sincronizar
define('SYNC_INTERVAL_MINUTES', 15); // Intervalo de sincronización en minutos

// Configuración de eventos
define('DEFAULT_EVENT_DURATION', 30); // Duración por defecto en minutos
define('EVENT_COLOR_ID', '3'); // Color del evento (1-11, ver documentación de Google)

// Archivos de tokens
define('TOKEN_FILE', __DIR__ . '/tokens/calendar_token.json');
define('CREDENTIALS_FILE', __DIR__ . '/credentials/google_credentials.json');

// Función para obtener configuración de Google Calendar
function getGoogleCalendarConfig() {
    return [
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'calendar_id' => GOOGLE_CALENDAR_ID,
        'scopes' => [
            'https://www.googleapis.com/auth/calendar',
            'https://www.googleapis.com/auth/calendar.events'
        ]
    ];
}

// Función para obtener configuración de sincronización
function getSyncConfig() {
    return [
        'past_days' => SYNC_PAST_DAYS,
        'future_days' => SYNC_FUTURE_DAYS,
        'interval_minutes' => SYNC_INTERVAL_MINUTES,
        'default_duration' => DEFAULT_EVENT_DURATION,
        'event_color_id' => EVENT_COLOR_ID
    ];
}

// Función para verificar si la integración está habilitada
function isGoogleCalendarEnabled() {
    return !empty(GOOGLE_CLIENT_ID) && 
           !empty(GOOGLE_CLIENT_SECRET) && 
           file_exists(CREDENTIALS_FILE);
}

// Función para obtener la URL de autorización
function getAuthorizationUrl() {
    $config = getGoogleCalendarConfig();
    $params = [
        'client_id' => $config['client_id'],
        'redirect_uri' => $config['redirect_uri'],
        'scope' => implode(' ', $config['scopes']),
        'response_type' => 'code',
        'access_type' => 'offline',
        'prompt' => 'consent'
    ];
    
    return 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($params);
}
?>
