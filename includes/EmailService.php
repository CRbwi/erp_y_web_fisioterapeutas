<?php
/**
 * Servicio de Email usando PHPMailer
 * Maneja el envío de emails de confirmación y notificaciones
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../email_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mailer;
    private $smtpConfig;
    private $senderConfig;
    
    public function __construct() {
        $this->smtpConfig = getSMTPConfig();
        $this->senderConfig = getSenderConfig();
        $this->initializeMailer();
    }
    
    /**
     * Inicializa PHPMailer con configuración SMTP
     */
    private function initializeMailer() {
        $this->mailer = new PHPMailer(true);
        
        try {
            // Configuración del servidor
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->smtpConfig['host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->smtpConfig['username'];
            $this->mailer->Password = $this->smtpConfig['password'];
            $this->mailer->SMTPSecure = $this->smtpConfig['secure'];
            $this->mailer->Port = $this->smtpConfig['port'];
            
            // Configuración del remitente
            $this->mailer->setFrom(
                $this->senderConfig['from_email'], 
                $this->senderConfig['from_name']
            );
            $this->mailer->addReplyTo(
                $this->senderConfig['reply_to_email'], 
                $this->senderConfig['reply_to_name']
            );
            
            // Configuración general
            $this->mailer->isHTML(true);
            $this->mailer->CharSet = 'UTF-8';
            
        } catch (Exception $e) {
            error_log("Error inicializando PHPMailer: " . $e->getMessage());
            throw new Exception("Error en la configuración del email: " . $e->getMessage());
        }
    }
    
    /**
     * Envía email de confirmación al cliente
     */
    public function sendBookingConfirmation($clientData, $bookingData) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($clientData['email'], $clientData['nombre']);
            
            $this->mailer->Subject = 'Confirmación de Reserva - Jorge Hernandez Fisioterapeuta';
            
            // Cargar y procesar plantilla
            $template = $this->loadTemplate('booking_confirmation.html');
            $htmlContent = $this->processTemplate($template, [
                'NOMBRE_CLIENTE' => $clientData['nombre'],
                'FECHA_CITA' => date('d/m/Y', strtotime($bookingData['fecha_cita'])),
                'HORA_CITA' => date('H:i', strtotime($bookingData['fecha_cita'])),
                'MOTIVO_CONSULTA' => $bookingData['motivo_consulta']
            ]);
            
            $this->mailer->Body = $htmlContent;
            $this->mailer->AltBody = $this->generatePlainText($htmlContent);
            
            $result = $this->mailer->send();
            
            if ($result) {
                error_log("Email de confirmación enviado exitosamente a: " . $clientData['email']);
                return true;
            } else {
                error_log("Error enviando email de confirmación a: " . $clientData['email']);
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Error enviando email de confirmación: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envía notificación al fisioterapeuta
     */
    public function sendTherapistNotification($clientData, $bookingData, $isNewClient = false) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($this->senderConfig['reply_to_email'], $this->senderConfig['reply_to_name']);
            
            $this->mailer->Subject = 'Nueva Reserva - ' . $clientData['nombre'];
            
            // Cargar y procesar plantilla
            $template = $this->loadTemplate('therapist_notification.html');
            $htmlContent = $this->processTemplate($template, [
                'NOMBRE_CLIENTE' => $clientData['nombre'],
                'TELEFONO_CLIENTE' => $clientData['telefono'],
                'EMAIL_CLIENTE' => $clientData['email'],
                'FECHA_CITA' => date('d/m/Y', strtotime($bookingData['fecha_cita'])),
                'HORA_CITA' => date('H:i', strtotime($bookingData['fecha_cita'])),
                'MOTIVO_CONSULTA' => $bookingData['motivo_consulta'],
                'ES_CLIENTE_NUEVO' => $isNewClient ? 'Sí' : 'No',
                'ADMIN_PANEL_URL' => 'http://' . $_SERVER['HTTP_HOST'] . '/jorge/'
            ]);
            
            $this->mailer->Body = $htmlContent;
            $this->mailer->AltBody = $this->generatePlainText($htmlContent);
            
            $result = $this->mailer->send();
            
            if ($result) {
                error_log("Notificación al fisioterapeuta enviada exitosamente");
                return true;
            } else {
                error_log("Error enviando notificación al fisioterapeuta");
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Error enviando notificación al fisioterapeuta: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Carga una plantilla de email
     */
    private function loadTemplate($templateName) {
        $templatePath = EMAIL_TEMPLATE_PATH . $templateName;
        
        if (!file_exists($templatePath)) {
            throw new Exception("Plantilla no encontrada: " . $templatePath);
        }
        
        return file_get_contents($templatePath);
    }
    
    /**
     * Procesa una plantilla reemplazando variables
     */
    private function processTemplate($template, $variables) {
        foreach ($variables as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        return $template;
    }
    
    /**
     * Genera versión de texto plano del HTML
     */
    private function generatePlainText($html) {
        // Eliminar etiquetas HTML y convertir entidades
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        
        // Limpiar espacios extra
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        return $text;
    }
    
    /**
     * Verifica la configuración del email
     */
    public function testConnection() {
        try {
            $this->mailer->smtpConnect();
            return true;
        } catch (Exception $e) {
            error_log("Error en test de conexión SMTP: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene información de debug del último envío
     */
    public function getDebugInfo() {
        return [
            'smtp_config' => $this->smtpConfig,
            'sender_config' => $this->senderConfig,
            'last_error' => $this->mailer->ErrorInfo ?? null
        ];
    }
}
?>
