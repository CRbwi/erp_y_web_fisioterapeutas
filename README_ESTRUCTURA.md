# 🏥 Centro de Fisioterapia Jorge Hernández - Estructura del Proyecto

## 📁 **Estructura de Archivos**

```
jorge/
├── index.php                    # Redirección automática a página pública
├── index_public.php            # 🏠 PÁGINA PRINCIPAL PÚBLICA
├── booking.php                 # 📅 Formulario de reserva online
├── admin/                      # 🔐 ÁREA ADMINISTRATIVA PROTEGIDA
│   ├── login.php              # Página de login
│   ├── auth_check.php         # Verificación de autenticación
│   ├── index.php              # Panel administrativo principal
│   └── .htaccess              # Protección del directorio
├── api/                        # 🔌 APIs del sistema
│   ├── clients.php            # Gestión de clientes y reservas
│   └── historial.php          # Historial de tratamientos
├── includes/                   # 📚 Clases y servicios
│   ├── EmailService.php       # Servicio de emails
│   └── GoogleCalendarService.php # Integración con Google Calendar
├── email_templates/            # 📧 Plantillas de email
│   ├── booking_confirmation.html
│   └── therapist_notification.html
├── js/                         # 📱 JavaScript del frontend
│   └── historial_tratamientos.js
├── style.css                   # 🎨 Estilos principales
├── historial_tratamientos.php  # 🏥 Historial médico
├── google_calendar_auth.php    # 🔗 Configuración Google Calendar
└── [otros archivos existentes]
```

## 🌐 **Páginas Públicas**

### 1. **Página Principal** (`index_public.php`)
- **URL**: `paginaweb.com/` o `paginaweb.com/index_public.php`
- **Contenido**: Información de la clínica, especialidades, servicios
- **Diseño**: Basado en tu perfil de Doctoralia y tarjeta de visita
- **Navegación**: Menú completo con enlaces a todas las secciones

### 2. **Formulario de Reserva** (`booking.php`)
- **URL**: `paginaweb.com/booking.php`
- **Funcionalidad**: Reserva online con selección de fecha/hora
- **Integración**: Conecta con el sistema de disponibilidad
- **Confirmación**: Email automático al cliente y notificación al fisioterapeuta

## 🔐 **Área Administrativa**

### **Acceso**: `paginaweb.com/admin/`
- **Usuario**: `jorge`
- **Contraseña**: `freakmondo`

### **Funcionalidades del Panel Admin**:
1. **📊 Dashboard** con estadísticas del sistema
2. **👥 Gestión de Clientes** - CRUD completo
3. **📅 Calendario de Citas** - Visualización y gestión
4. **⏰ Gestión de Disponibilidad** - Horarios y bloqueos
5. **🏥 Historial Médico** - Registro de sesiones
6. **🔗 Google Calendar** - Sincronización
7. **📊 Reportes** - Estadísticas y análisis

## 🚀 **Cómo Usar el Sistema**

### **Para Clientes (Público)**:
1. **Visitar**: `paginaweb.com/`
2. **Navegar** por la información de la clínica
3. **Hacer clic** en "Reservar Cita" o ir a `paginaweb.com/booking.php`
4. **Completar** el formulario con datos personales
5. **Seleccionar** fecha y hora disponible
6. **Confirmar** la reserva
7. **Recibir** confirmación por email

### **Para el Fisioterapeuta (Admin)**:
1. **Acceder**: `paginaweb.com/admin/`
2. **Iniciar sesión** con credenciales
3. **Gestionar** clientes, citas y disponibilidad
4. **Registrar** sesiones de tratamiento
5. **Sincronizar** con Google Calendar
6. **Generar** reportes y estadísticas

## 🔧 **Configuración Requerida**

### **1. Base de Datos MySQL**
- Puerto: 3307
- Tablas: `clientes`, `citas_tratamientos`, `historial_tratamientos`, etc.

### **2. Sistema de Emails**
- Configurar `email_config.php` con datos SMTP
- Instalar dependencias: `composer install`

### **3. Google Calendar**
- Configurar `google_calendar_config.php` con credenciales
- Instalar dependencias: `composer install`

## 📱 **Responsive Design**

- **Desktop**: Diseño completo con todas las funcionalidades
- **Tablet**: Adaptación automática para pantallas medianas
- **Mobile**: Navegación optimizada con menú hamburguesa

## 🔒 **Seguridad**

- **Autenticación**: Sistema de login con sesiones PHP
- **Protección**: Archivo `.htaccess` en área admin
- **Validación**: Sanitización de datos en formularios
- **HTTPS**: Recomendado para producción

## 🚀 **Despliegue en Producción**

### **Requisitos del Servidor**:
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Composer para dependencias
- Servidor web (Apache/Nginx)

### **Pasos de Instalación**:
1. **Subir** archivos al servidor
2. **Configurar** base de datos
3. **Instalar** dependencias: `composer install`
4. **Configurar** emails y Google Calendar
5. **Probar** funcionalidades

## 📞 **Soporte y Mantenimiento**

### **Archivos Importantes**:
- **Configuración**: `email_config.php`, `google_calendar_config.php`
- **Base de datos**: Scripts SQL en la raíz del proyecto
- **Logs**: Revisar errores en consola del navegador

### **Mantenimiento Regular**:
- **Backup** de base de datos
- **Actualización** de dependencias
- **Revisión** de logs de error
- **Monitoreo** de funcionalidades

---

## 🎯 **Resumen de Funcionalidades Implementadas**

✅ **Página pública profesional** con información de la clínica  
✅ **Formulario de reserva online** integrado  
✅ **Sistema de autenticación** para área administrativa  
✅ **Panel administrativo completo** con todas las funcionalidades  
✅ **Gestión de clientes y citas**  
✅ **Sistema de historial médico**  
✅ **Integración con Google Calendar**  
✅ **Sistema de emails automáticos**  
✅ **Diseño responsive** para todos los dispositivos  
✅ **Navegación intuitiva** entre secciones  

---

**🎉 ¡El sistema está listo para usar en producción!**
