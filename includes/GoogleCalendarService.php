<?php
/**
 * Servicio de Google Calendar
 * Maneja la sincronización bidireccional con Google Calendar
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../google_calendar_config.php';

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;

class GoogleCalendarService {
    private $client;
    private $service;
    private $calendarId;
    private $syncConfig;
    
    public function __construct() {
        if (!isGoogleCalendarEnabled()) {
            throw new Exception('Google Calendar no está configurado correctamente');
        }
        
        $this->calendarId = GOOGLE_CALENDAR_ID;
        $this->syncConfig = getSyncConfig();
        $this->initializeClient();
    }
    
    /**
     * Inicializa el cliente de Google
     */
    private function initializeClient() {
        $this->client = new Google_Client();
        $this->client->setClientId(GOOGLE_CLIENT_ID);
        $this->client->setClientSecret(GOOGLE_CLIENT_SECRET);
        $this->client->setRedirectUri(GOOGLE_REDIRECT_URI);
        $this->client->setScopes(getGoogleCalendarConfig()['scopes']);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
        
        // Cargar token existente si existe
        if (file_exists(TOKEN_FILE)) {
            $accessToken = json_decode(file_get_contents(TOKEN_FILE), true);
            $this->client->setAccessToken($accessToken);
        }
        
        // Si el token ha expirado, refrescarlo
        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                $this->saveToken();
            } else {
                throw new Exception('Token expirado y no hay refresh token disponible');
            }
        }
        
        $this->service = new Google_Service_Calendar($this->client);
    }
    
    /**
     * Guarda el token de acceso
     */
    private function saveToken() {
        if (!is_dir(dirname(TOKEN_FILE))) {
            mkdir(dirname(TOKEN_FILE), 0755, true);
        }
        
        file_put_contents(TOKEN_FILE, json_encode($this->client->getAccessToken()));
    }
    
    /**
     * Crea un evento en Google Calendar
     */
    public function createEvent($appointmentData) {
        try {
            $event = new Google_Service_Calendar_Event();
            
            // Configurar título del evento
            $event->setSummary('Cita: ' . $appointmentData['cliente_nombre'] . ' - ' . $appointmentData['tipo_tratamiento']);
            
            // Configurar descripción
            $description = "Cliente: " . $appointmentData['cliente_nombre'] . "\n";
            $description .= "Teléfono: " . $appointmentData['cliente_telefono'] . "\n";
            $description .= "Email: " . $appointmentData['cliente_email'] . "\n";
            $description .= "Motivo: " . $appointmentData['motivo_consulta'] . "\n";
            $description .= "Estado: " . $appointmentData['status'];
            
            $event->setDescription($description);
            
            // Configurar fecha y hora
            $startDateTime = new Google_Service_Calendar_EventDateTime();
            $startDateTime->setDateTime(date('c', strtotime($appointmentData['fecha_cita'])));
            $startDateTime->setTimeZone('Europe/Madrid');
            $event->setStart($startDateTime);
            
            $endDateTime = new Google_Service_Calendar_EventDateTime();
            $endTime = strtotime($appointmentData['fecha_cita']) + ($this->syncConfig['default_duration'] * 60);
            $endDateTime->setDateTime(date('c', $endTime));
            $endDateTime->setTimeZone('Europe/Madrid');
            $event->setEnd($endDateTime);
            
            // Configurar color y recordatorios
            $event->setColorId($this->syncConfig['event_color_id']);
            $event->setReminders([
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'email', 'minutes' => 24 * 60], // 1 día antes
                    ['method' => 'popup', 'minutes' => 60] // 1 hora antes
                ]
            ]);
            
            // Crear el evento
            $createdEvent = $this->service->events->insert($this->calendarId, $event);
            
            // Guardar el ID del evento de Google en la base de datos
            $this->saveGoogleEventId($appointmentData['id'], $createdEvent->getId());
            
            return [
                'success' => true,
                'google_event_id' => $createdEvent->getId(),
                'html_link' => $createdEvent->getHtmlLink()
            ];
            
        } catch (Exception $e) {
            error_log("Error creando evento en Google Calendar: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Actualiza un evento en Google Calendar
     */
    public function updateEvent($appointmentData) {
        try {
            // Obtener el ID del evento de Google
            $googleEventId = $this->getGoogleEventId($appointmentData['id']);
            
            if (!$googleEventId) {
                // Si no existe, crear uno nuevo
                return $this->createEvent($appointmentData);
            }
            
            // Obtener el evento existente
            $event = $this->service->events->get($this->calendarId, $googleEventId);
            
            // Actualizar campos
            $event->setSummary('Cita: ' . $appointmentData['cliente_nombre'] . ' - ' . $appointmentData['tipo_tratamiento']);
            
            $description = "Cliente: " . $appointmentData['cliente_nombre'] . "\n";
            $description .= "Teléfono: " . $appointmentData['cliente_telefono'] . "\n";
            $description .= "Email: " . $appointmentData['cliente_email'] . "\n";
            $description .= "Motivo: " . $appointmentData['motivo_consulta'] . "\n";
            $description .= "Estado: " . $appointmentData['status'];
            
            $event->setDescription($description);
            
            // Actualizar fecha y hora
            $startDateTime = new Google_Service_Calendar_EventDateTime();
            $startDateTime->setDateTime(date('c', strtotime($appointmentData['fecha_cita'])));
            $startDateTime->setTimeZone('Europe/Madrid');
            $event->setStart($startDateTime);
            
            $endDateTime = new Google_Service_Calendar_EventDateTime();
            $endTime = strtotime($appointmentData['fecha_cita']) + ($this->syncConfig['default_duration'] * 60);
            $endDateTime->setDateTime(date('c', $endTime));
            $endDateTime->setTimeZone('Europe/Madrid');
            $event->setEnd($endDateTime);
            
            // Actualizar el evento
            $updatedEvent = $this->service->events->update($this->calendarId, $googleEventId, $event);
            
            return [
                'success' => true,
                'google_event_id' => $updatedEvent->getId(),
                'html_link' => $updatedEvent->getHtmlLink()
            ];
            
        } catch (Exception $e) {
            error_log("Error actualizando evento en Google Calendar: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Elimina un evento de Google Calendar
     */
    public function deleteEvent($appointmentId) {
        try {
            $googleEventId = $this->getGoogleEventId($appointmentId);
            
            if ($googleEventId) {
                $this->service->events->delete($this->calendarId, $googleEventId);
                $this->removeGoogleEventId($appointmentId);
                
                return [
                    'success' => true,
                    'message' => 'Evento eliminado de Google Calendar'
                ];
            }
            
            return [
                'success' => true,
                'message' => 'No se encontró evento en Google Calendar'
            ];
            
        } catch (Exception $e) {
            error_log("Error eliminando evento de Google Calendar: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Sincroniza eventos desde Google Calendar a la base de datos local
     */
    public function syncFromGoogle($conn) {
        try {
            $timeMin = date('c', strtotime('-' . $this->syncConfig['past_days'] . ' days'));
            $timeMax = date('c', strtotime('+' . $this->syncConfig['future_days'] . ' days'));
            
            $optParams = [
                'timeMin' => $timeMin,
                'timeMax' => $timeMax,
                'singleEvents' => true,
                'orderBy' => 'startTime'
            ];
            
            $results = $this->service->events->listEvents($this->calendarId, $optParams);
            $events = $results->getItems();
            
            $syncedCount = 0;
            foreach ($events as $event) {
                if ($this->syncGoogleEventToLocal($conn, $event)) {
                    $syncedCount++;
                }
            }
            
            return [
                'success' => true,
                'synced_count' => $syncedCount,
                'total_events' => count($events)
            ];
            
        } catch (Exception $e) {
            error_log("Error sincronizando desde Google Calendar: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Guarda el ID del evento de Google en la base de datos
     */
    private function saveGoogleEventId($appointmentId, $googleEventId) {
        // Aquí deberías agregar una columna google_event_id a tu tabla citas_tratamientos
        // Por ahora, lo guardamos en un archivo temporal
        $mappingFile = __DIR__ . '/../tokens/event_mapping.json';
        
        if (!is_dir(dirname($mappingFile))) {
            mkdir(dirname($mappingFile), 0755, true);
        }
        
        $mapping = [];
        if (file_exists($mappingFile)) {
            $mapping = json_decode(file_get_contents($mappingFile), true) ?: [];
        }
        
        $mapping[$appointmentId] = $googleEventId;
        file_put_contents($mappingFile, json_encode($mapping));
    }
    
    /**
     * Obtiene el ID del evento de Google desde la base de datos
     */
    private function getGoogleEventId($appointmentId) {
        $mappingFile = __DIR__ . '/../tokens/event_mapping.json';
        
        if (file_exists($mappingFile)) {
            $mapping = json_decode(file_get_contents($mappingFile), true) ?: [];
            return $mapping[$appointmentId] ?? null;
        }
        
        return null;
    }
    
    /**
     * Elimina el mapeo del evento de Google
     */
    private function removeGoogleEventId($appointmentId) {
        $mappingFile = __DIR__ . '/../tokens/event_mapping.json';
        
        if (file_exists($mappingFile)) {
            $mapping = json_decode(file_get_contents($mappingFile), true) ?: [];
            unset($mapping[$appointmentId]);
            file_put_contents($mappingFile, json_encode($mapping));
        }
    }
    
    /**
     * Sincroniza un evento de Google a la base de datos local
     */
    private function syncGoogleEventToLocal($conn, $googleEvent) {
        // Implementar lógica para sincronizar eventos de Google a la base de datos local
        // Esto dependerá de tu estructura de base de datos
        return true;
    }
    
    /**
     * Obtiene la URL de autorización
     */
    public function getAuthorizationUrl() {
        return $this->client->createAuthUrl();
    }
    
    /**
     * Maneja el callback de autorización
     */
    public function handleAuthCallback($code) {
        try {
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
            $this->client->setAccessToken($accessToken);
            
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
            
            $this->saveToken();
            
            return [
                'success' => true,
                'message' => 'Autorización exitosa'
            ];
            
        } catch (Exception $e) {
            error_log("Error en callback de autorización: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Verifica si el servicio está autorizado
     */
    public function isAuthorized() {
        return !$this->client->isAccessTokenExpired();
    }
    
    /**
     * Obtiene información del calendario
     */
    public function getCalendarInfo() {
        try {
            $calendar = $this->service->calendars->get($this->calendarId);
            
            return [
                'id' => $calendar->getId(),
                'summary' => $calendar->getSummary(),
                'description' => $calendar->getDescription(),
                'timezone' => $calendar->getTimeZone(),
                'access_role' => $calendar->getAccessRole()
            ];
            
        } catch (Exception $e) {
            error_log("Error obteniendo información del calendario: " . $e->getMessage());
            return null;
        }
    }
}
?>
