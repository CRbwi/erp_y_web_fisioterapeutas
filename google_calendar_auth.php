<?php
/**
 * Página de autorización de Google Calendar
 * Maneja el flujo OAuth2 para conectar con Google Calendar
 */

require_once 'google_calendar_config.php';
require_once 'includes/GoogleCalendarService.php';

// Verificar si ya está autorizado
$isAuthorized = false;
$calendarInfo = null;
$errorMessage = null;

try {
    if (isGoogleCalendarEnabled()) {
        $googleService = new GoogleCalendarService();
        $isAuthorized = $googleService->isAuthorized();
        
        if ($isAuthorized) {
            $calendarInfo = $googleService->getCalendarInfo();
        }
    }
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
}

// Manejar callback de autorización
if (isset($_GET['code'])) {
    try {
        $googleService = new GoogleCalendarService();
        $result = $googleService->handleAuthCallback($_GET['code']);
        
        if ($result['success']) {
            header('Location: google_calendar_auth.php?success=1');
            exit;
        } else {
            $errorMessage = $result['error'];
        }
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}

// Manejar logout
if (isset($_GET['logout'])) {
    if (file_exists(TOKEN_FILE)) {
        unlink(TOKEN_FILE);
    }
    header('Location: google_calendar_auth.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Calendar - Jorge Hernandez Fisioterapeuta</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #1a1a1a;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .status-card {
            padding: 1.5rem;
            margin: 1rem 0;
            border-radius: 6px;
            border-left: 4px solid;
        }
        
        .status-success {
            background-color: #1b5e20;
            border-left-color: #4caf50;
        }
        
        .status-error {
            background-color: #c62828;
            border-left-color: #f44336;
        }
        
        .status-info {
            background-color: #1565c0;
            border-left-color: #2196f3;
        }
        
        .auth-button {
            display: inline-block;
            background-color: #4285f4;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 10px;
            transition: background-color 0.3s ease;
        }
        
        .auth-button:hover {
            background-color: #3367d6;
        }
        
        .auth-button.danger {
            background-color: #f44336;
        }
        
        .auth-button.danger:hover {
            background-color: #d32f2f;
        }
        
        .calendar-info {
            background-color: #2a2a2a;
            padding: 1rem;
            border-radius: 6px;
            margin: 1rem 0;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 0.5rem;
        }
        
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #ccc;
        }
        
        .info-value {
            color: #fff;
        }
        
        .sync-section {
            margin: 2rem 0;
            padding: 1.5rem;
            background-color: #2a2a2a;
            border-radius: 6px;
        }
        
        .sync-button {
            background-color: #4caf50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            margin: 10px;
        }
        
        .sync-button:hover {
            background-color: #45a049;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 2rem;
            color: #4caf50;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1>📅 Integración con Google Calendar</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="status-card status-success">
                <h3>✅ Autorización Exitosa</h3>
                <p>Tu cuenta de Google Calendar ha sido conectada correctamente.</p>
            </div>
        <?php endif; ?>
        
        <?php if ($errorMessage): ?>
            <div class="status-card status-error">
                <h3>❌ Error de Conexión</h3>
                <p><?php echo htmlspecialchars($errorMessage); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (!isGoogleCalendarEnabled()): ?>
            <div class="status-card status-error">
                <h3>⚠️ Configuración Requerida</h3>
                <p>Para usar Google Calendar, necesitas configurar las credenciales en <code>google_calendar_config.php</code></p>
                <ul>
                    <li>Configura <code>GOOGLE_CLIENT_ID</code></li>
                    <li>Configura <code>GOOGLE_CLIENT_SECRET</code></li>
                    <li>Configura <code>GOOGLE_REDIRECT_URI</code></li>
                    <li>Crea el archivo <code>credentials/google_credentials.json</code></li>
                </ul>
            </div>
        <?php elseif ($isAuthorized): ?>
            <div class="status-card status-success">
                <h3>✅ Conectado a Google Calendar</h3>
                <p>Tu sistema está sincronizado con Google Calendar.</p>
            </div>
            
            <?php if ($calendarInfo): ?>
                <div class="calendar-info">
                    <h3>📋 Información del Calendario</h3>
                    <div class="info-row">
                        <span class="info-label">Nombre:</span>
                        <span class="info-value"><?php echo htmlspecialchars($calendarInfo['summary']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">ID:</span>
                        <span class="info-value"><?php echo htmlspecialchars($calendarInfo['id']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Zona Horaria:</span>
                        <span class="info-value"><?php echo htmlspecialchars($calendarInfo['timezone']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Rol de Acceso:</span>
                        <span class="info-value"><?php echo htmlspecialchars($calendarInfo['access_role']); ?></span>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="sync-section">
                <h3>🔄 Sincronización</h3>
                <p>Tu sistema sincroniza automáticamente las citas con Google Calendar:</p>
                <ul>
                    <li>✅ Las nuevas citas se crean automáticamente en Google Calendar</li>
                    <li>✅ Las modificaciones se sincronizan en tiempo real</li>
                    <li>✅ Las cancelaciones se reflejan inmediatamente</li>
                    <li>✅ Recordatorios automáticos configurados</li>
                </ul>
                
                <button class="sync-button" onclick="manualSync()">🔄 Sincronización Manual</button>
            </div>
            
            <a href="?logout=1" class="auth-button danger">🚪 Desconectar Google Calendar</a>
            
        <?php else: ?>
            <div class="status-card status-info">
                <h3>🔗 Conectar con Google Calendar</h3>
                <p>Para sincronizar tus citas con Google Calendar, necesitas autorizar el acceso.</p>
            </div>
            
            <a href="<?php echo getAuthorizationUrl(); ?>" class="auth-button">
                🔐 Conectar con Google Calendar
            </a>
        <?php endif; ?>
        
        <a href="index.php" class="back-link">← Volver al Panel de Administración</a>
    </div>
    
    <script>
        function manualSync() {
            // Aquí puedes implementar la sincronización manual
            alert('Sincronización manual iniciada. Esta funcionalidad se implementará próximamente.');
        }
    </script>
</body>
</html>
