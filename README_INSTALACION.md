# 📧 Sistema de Email y Google Calendar - Instalación

## 🚀 Instalación del Sistema de Email (PHPMailer)

### 1. Instalar Dependencias
```bash
# En el directorio raíz del proyecto
composer install
```

### 2. Configurar Email
Edita el archivo `email_config.php`:

```php
// Configuración del servidor SMTP
define('SMTP_HOST', 'smtp.gmail.com'); // Tu servidor SMTP
define('SMTP_PORT', 587); // Puerto TLS
define('SMTP_USERNAME', 'tu-email@gmail.com'); // Tu email
define('SMTP_PASSWORD', 'tu-password-app'); // Tu contraseña de aplicación
define('SMTP_SECURE', 'tls'); // tls o ssl

// Configuración del remitente
define('FROM_EMAIL', 'reservas@tudominio.com'); // Email de reservas
define('FROM_NAME', 'Jorge Hernandez Fisioterapeuta'); // Nombre del remitente

// Configuración de respuestas
define('REPLY_TO_EMAIL', 'jorge@tudominio.com'); // Email para respuestas
define('REPLY_TO_NAME', 'Jorge Hernandez'); // Nombre para respuestas
```

### 3. Configuración Gmail (Recomendado)
1. Ve a [Google Account Settings](https://myaccount.google.com/)
2. Activa la verificación en 2 pasos
3. Genera una contraseña de aplicación
4. Usa esa contraseña en `SMTP_PASSWORD`

---

## 📅 Instalación de Google Calendar

### 1. Crear Proyecto en Google Cloud Console
1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un nuevo proyecto o selecciona uno existente
3. Activa la API de Google Calendar

### 2. Configurar OAuth 2.0
1. Ve a "Credentials" → "Create Credentials" → "OAuth 2.0 Client IDs"
2. Configura las URLs autorizadas:
   - **Authorized JavaScript origins**: `http://tudominio.com`
   - **Authorized redirect URIs**: `http://tudominio.com/jorge/google_calendar_auth.php`

### 3. Descargar Credenciales
1. Descarga el archivo JSON de credenciales
2. Renómbralo a `google_credentials.json`
3. Colócalo en `credentials/google_credentials.json`

### 4. Configurar el Sistema
Edita `google_calendar_config.php`:

```php
define('GOOGLE_CLIENT_ID', 'tu-client-id.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'tu-client-secret');
define('GOOGLE_REDIRECT_URI', 'http://tudominio.com/jorge/google_calendar_auth.php');
```

---

## 📁 Estructura de Archivos

```
jorge/
├── includes/
│   ├── EmailService.php          # Servicio de email
│   └── GoogleCalendarService.php # Servicio de Google Calendar
├── email_templates/
│   ├── booking_confirmation.html # Plantilla confirmación cliente
│   └── therapist_notification.html # Plantilla notificación fisioterapeuta
├── tokens/                       # Directorio para tokens (crear)
├── credentials/                  # Directorio para credenciales (crear)
├── email_config.php              # Configuración de email
├── google_calendar_config.php    # Configuración de Google Calendar
├── google_calendar_auth.php      # Página de autorización
├── composer.json                 # Dependencias
└── README_INSTALACION.md         # Este archivo
```

---

## 🔧 Configuración de Directorios

### Crear Directorios Necesarios
```bash
mkdir -p tokens
mkdir -p credentials
chmod 755 tokens
chmod 755 credentials
```

### Permisos de Archivos
```bash
chmod 644 email_config.php
chmod 644 google_calendar_config.php
chmod 644 includes/*.php
chmod 644 email_templates/*.html
```

---

## 📧 Funcionalidades del Sistema de Email

### ✅ Características Implementadas
- **PHPMailer** para envío confiable de emails
- **Plantillas HTML profesionales** y responsivas
- **Confirmación automática** al cliente
- **Notificación automática** al fisioterapeuta
- **Manejo de errores** robusto
- **Logs detallados** para debugging

### 📱 Plantillas Incluidas
1. **`booking_confirmation.html`** - Confirmación para el cliente
2. **`therapist_notification.html`** - Notificación para el fisioterapeuta

---

## 📅 Funcionalidades de Google Calendar

### ✅ Características Implementadas
- **Sincronización bidireccional** automática
- **Creación automática** de eventos en Google Calendar
- **Actualización en tiempo real** de citas
- **Recordatorios automáticos** configurados
- **Manejo de tokens** OAuth2
- **Interfaz de autorización** integrada

### 🔄 Proceso de Sincronización
1. **Nueva cita** → Se crea automáticamente en Google Calendar
2. **Modificación** → Se actualiza en Google Calendar
3. **Cancelación** → Se elimina de Google Calendar
4. **Recordatorios** → Configurados automáticamente

---

## 🧪 Pruebas del Sistema

### 1. Probar Sistema de Email
```bash
# Crear un archivo de prueba
php -r "
require_once 'includes/EmailService.php';
try {
    \$emailService = new EmailService();
    \$result = \$emailService->testConnection();
    echo \$result ? '✅ Email configurado correctamente' : '❌ Error en email';
} catch (Exception \$e) {
    echo '❌ Error: ' . \$e->getMessage();
}
"
```

### 2. Probar Google Calendar
1. Ve a `google_calendar_auth.php`
2. Sigue el proceso de autorización
3. Verifica la conexión

---

## 🚨 Solución de Problemas

### Problemas Comunes de Email

#### Error: "SMTP connect() failed"
- Verifica credenciales SMTP
- Comprueba puerto y seguridad
- Verifica firewall/antivirus

#### Error: "Authentication failed"
- Verifica usuario y contraseña
- Para Gmail, usa contraseña de aplicación
- Comprueba verificación en 2 pasos

### Problemas Comunes de Google Calendar

#### Error: "Invalid client"
- Verifica CLIENT_ID y CLIENT_SECRET
- Comprueba URLs autorizadas
- Verifica que la API esté activada

#### Error: "Redirect URI mismatch"
- Verifica GOOGLE_REDIRECT_URI
- Comprueba URLs en Google Cloud Console
- Asegúrate de que coincidan exactamente

---

## 📚 Recursos Adicionales

### Documentación Oficial
- [PHPMailer Documentation](https://github.com/PHPMailer/PHPMailer)
- [Google Calendar API](https://developers.google.com/calendar)
- [Google OAuth 2.0](https://developers.google.com/identity/protocols/oauth2)

### Soporte
- Revisa los logs de error en `/var/log/apache2/error.log`
- Verifica permisos de archivos y directorios
- Comprueba configuración de PHP y extensiones

---

## ✅ Checklist de Instalación

- [ ] Instalar dependencias con `composer install`
- [ ] Configurar `email_config.php`
- [ ] Crear directorios `tokens/` y `credentials/`
- [ ] Configurar Google Cloud Console
- [ ] Configurar `google_calendar_config.php`
- [ ] Probar sistema de email
- [ ] Probar integración con Google Calendar
- [ ] Verificar sincronización automática

---

**🎉 ¡Tu sistema está listo para usar!**

Una vez completada la instalación, tendrás:
- ✅ Emails profesionales automáticos
- ✅ Sincronización completa con Google Calendar
- ✅ Sistema robusto y confiable
- ✅ Interfaz integrada en tu panel de administración
