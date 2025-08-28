<?php
require_once 'admin/auth_check.php';
requireAdminLogin();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - Gestión de Clínicas</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <style>
        /* Estilos para navegación activa */
        nav a.active {
            background-color: #d4a574;
            color: white;
        }
        
        /* Estilos para secciones */
        .content-section {
            margin-bottom: 2rem;
        }
        
        /* Ocultar secciones por defecto */
        #calendar-section,
        #historial-section,
        #documents-section,
        #availability-section,
        #add-availability,
        #block-date {
            display: none;
        }
        
        /* Mostrar solo la sección de clientes por defecto */
        #client-list {
            display: block;
        }
        
        /* Estilos para modales */
        .modal {
            display: block;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            position: relative;
        }
        
        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 10px;
        }
        
        .close-button:hover,
        .close-button:focus {
            color: #000;
            text-decoration: none;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-group button {
            background: linear-gradient(135deg, #d4a574, #b8945f);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .form-group button:hover {
            background: linear-gradient(135deg, #b8945f, #d4a574);
        }
    </style>
</head>
<body>
    <header>
        <h1>Jorge Hernandez Fisioterapeuta</h1>
        <nav>
            <ul>
                <li><a href="#client-list">Clientes</a></li>
                <li><a href="#calendar-section">Calendario</a></li>
                <li><a href="#availability-section">Disponibilidad</a></li>
                <li><a href="historial_tratamientos.php">Historial Médico</a></li>
                <li><a href="google_calendar_auth.php">Google Calendar</a></li>
                <li><a href="admin/">🔐 Panel Principal</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Sección de Clientes -->
        <section id="client-list">
            <h2>Gestión de Clientes</h2>
            <div class="controls">
                <button id="show-add-client-form-btn">Registrar Nuevo Cliente</button>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Las filas de clientes se insertarán aquí -->
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Formulario para añadir cliente -->
        <section id="add-client" style="display: none;">
            <h3>Registrar Nuevo Cliente</h3>
            <form id="add-client-form">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="apellidos">Apellidos:</label>
                    <input type="text" id="apellidos" name="apellidos" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit">Registrar Cliente</button>
                <button type="button" id="cancel-add-client">Cancelar</button>
            </form>
        </section>

        <!-- Sección de Calendario -->
        <section id="calendar-section" style="display: none;">
            <h2>Calendario de Citas</h2>
            <div class="controls">
                <button id="show-add-appointment-btn">➕ Nueva Cita</button>
                <button id="debug-citas-btn" onclick="debugCitas()">🐛 Debug Citas</button>
            </div>
            <div id="calendar"></div>
            <div id="debug-citas-info" style="margin-top: 20px; padding: 10px; background: #f5f5f5; border-radius: 5px; display: none;">
                <h4>Información de Debug:</h4>
                <div id="debug-content"></div>
            </div>
        </section>

        <!-- Sección de Historial -->
        <section id="historial-section" style="display: none;">
            <h3>Historial de Citas y Tratamientos</h3>
            <div id="appointments-list">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo de Tratamiento</th>
                                <th>Observaciones</th>
                                <th>Precio cobrado al cliente</th>
                            </tr>
                        </thead>
                        <tbody id="appointments-table-body">
                            <!-- Appointments will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Sección de Documentos -->
        <section id="documents-section" style="display: none;">
            <h3>Documentos Adjuntos</h3>
            <div id="documents-list">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre del Archivo</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Fecha de Subida</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="documents-table-body">
                            <!-- Documents will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Sección de Disponibilidad -->
        <section id="availability-section" style="display: none;">
            <h2>Gestión de Disponibilidad</h2>
            <div class="controls">
                <button id="show-add-availability-btn">Añadir Bloque de Disponibilidad</button>
                <button id="show-block-date-btn">Bloquear Fecha</button>
                <button id="debug-availability-btn" onclick="debugAvailability()">🐛 Debug Disponibilidad</button>
            </div>
            <div id="availability-list">
                <h3>Bloques de Disponibilidad y Bloqueos Existentes</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Día de la Semana</th>
                                <th>Fecha Específica</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="availability-table-body">
                            <!-- Availability blocks will be loaded here -->
                        </tbody>
                    </table>
                </div>
                <div id="debug-availability-info" style="margin-top: 20px; padding: 10px; background: #f5f5f5; border-radius: 5px; display: none;">
                    <h4>Información de Debug de Disponibilidad:</h4>
                    <div id="debug-availability-content"></div>
                </div>
            </div>
        </section>

        <!-- Formulario para añadir disponibilidad -->
        <section id="add-availability" style="display: none;">
            <h3>Añadir Bloque de Disponibilidad</h3>
            <form id="add-availability-form">
                <div class="form-group">
                    <label for="availability-type">Tipo:</label>
                    <select id="availability-type" name="availability-type" required>
                        <option value="disponible">Disponible (Horario de Trabajo)</option>
                        <option value="bloqueado">Bloqueado (Vacaciones/No Disponible)</option>
                    </select>
                </div>
                <div class="form-group" id="day-of-week-group">
                    <label for="day-of-week">Día de la Semana:</label>
                    <select id="day-of-week" name="day-of-week">
                        <option value="1">Lunes</option>
                        <option value="2">Martes</option>
                        <option value="3">Miércoles</option>
                        <option value="4">Jueves</option>
                        <option value="5">Viernes</option>
                        <option value="6">Sábado</option>
                        <option value="0">Domingo</option>
                    </select>
                </div>
                <div class="form-group" id="specific-date-group" style="display: none;">
                    <label for="specific-date">Fecha Específica:</label>
                    <input type="date" id="specific-date" name="specific-date">
                </div>
                <div class="form-group">
                    <label for="start-time">Hora de Inicio:</label>
                    <input type="time" id="start-time" name="start-time" required>
                </div>
                <div class="form-group">
                    <label for="end-time">Hora de Fin:</label>
                    <input type="time" id="end-time" name="end-time" required>
                </div>
                <div class="form-group">
                    <label for="description">Descripción:</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>
                <button type="submit">Añadir Disponibilidad</button>
                <button type="button" id="cancel-add-availability">Cancelar</button>
            </form>
        </section>

        <!-- Formulario para bloquear fecha -->
        <section id="block-date" style="display: none;">
            <h3>Bloquear Fecha</h3>
            <form id="block-date-form">
                <div class="form-group">
                    <label for="block-date-input">Fecha a Bloquear:</label>
                    <input type="date" id="block-date-input" name="block-date" required>
                </div>
                <div class="form-group">
                    <label for="block-reason">Motivo del Bloqueo:</label>
                    <textarea id="block-reason" name="block-reason" rows="3"></textarea>
                </div>
                <button type="submit">Bloquear Fecha</button>
                <button type="button" id="cancel-block-date">Cancelar</button>
            </form>
        </section>
    </main>

    <!-- Los modales se crearán dinámicamente cuando se necesiten -->

    <script>
        // Función para mostrar secciones específicas
        function showSection(sectionId) {
            // Ocultar todas las secciones
            const allSections = document.querySelectorAll('.content-section, #client-list, #add-client, #calendar-section, #historial-section, #documents-section, #availability-section, #add-availability, #block-date');
            allSections.forEach(section => {
                section.style.display = 'none';
            });
            
            // Mostrar la sección solicitada
            const targetSection = document.querySelector(sectionId);
            if (targetSection) {
                targetSection.style.display = 'block';
            }
            
            // Actualizar navegación activa
            updateActiveNav(sectionId);
            
            // Cargar datos específicos de la sección
            if (sectionId === '#client-list') {
                loadClients();
            } else if (sectionId === '#calendar-section') {
                loadCalendar();
            } else if (sectionId === '#availability-section') {
                loadAvailability();
            }
        }
        
        // Función para cargar clientes
        async function loadClients() {
            try {
                const response = await fetch('api/clients.php');
                if (response.ok) {
                    const clients = await response.json();
                    console.log('Clientes cargados:', clients);
                    renderClientsTable(clients);
                } else {
                    console.error('Error cargando clientes:', response.statusText);
                }
            } catch (error) {
                console.error('Error de conexión:', error);
            }
        }
        
        // Función para renderizar la tabla de clientes
        function renderClientsTable(clients) {
            const tbody = document.querySelector('#client-list tbody');
            if (!tbody) return;
            
            tbody.innerHTML = '';
            
            if (clients.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5">No hay clientes registrados</td></tr>';
                return;
            }
            
            clients.forEach(client => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${client.nombre || ''}</td>
                    <td>${client.apellidos || ''}</td>
                    <td>${client.telefono || ''}</td>
                    <td>${client.email || ''}</td>
                    <td>
                        <button onclick="editClient(${client.id})" class="btn-edit">✏️</button>
                        <button onclick="deleteClient(${client.id})" class="btn-delete">🗑️</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
        
        // Función para cargar calendario
        function loadCalendar() {
            console.log('Cargando calendario...');
            
            // Inicializar FullCalendar
            const calendarEl = document.getElementById('calendar');
            if (calendarEl && !calendarEl.classList.contains('fc')) {
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'es',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    buttonText: {
                        today: 'Hoy',
                        month: 'Mes',
                        week: 'Semana',
                        day: 'Día'
                    },
                    events: function(info, successCallback, failureCallback) {
                        // Cargar citas manualmente usando el endpoint correcto
                        console.log('Cargando citas para el calendario...');
                        
                        // Construir URL con parámetros de fecha si están disponibles
                        let url = 'api/clients.php?action=get_calendar_appointments';
                        if (info.startStr && info.endStr) {
                            url += `&start=${info.startStr}&end=${info.endStr}`;
                        }
                        
                        fetch(url)
                            .then(response => {
                                console.log('Respuesta del API de citas:', response);
                                if (!response.ok) {
                                    throw new Error('Error del API: ' + response.status);
                                }
                                return response.json();
                            })
                            .then(data => {
                                console.log('Datos de citas recibidos:', data);
                                
                                if (!Array.isArray(data)) {
                                    console.error('Los datos no son un array:', data);
                                    successCallback([]);
                                    return;
                                }
                                
                                const events = data.map(appointment => {
                                    console.log('Procesando cita:', appointment);
                                    
                                    // Verificar que el campo fecha_cita exista
                                    if (!appointment.start) {
                                        console.warn('Cita sin fecha:', appointment);
                                        return null;
                                    }
                                    
                                    const event = {
                                        id: appointment.id || 'cita_' + Math.random(),
                                        title: appointment.title || 'Cita sin título',
                                        start: appointment.start,
                                        end: appointment.end || appointment.start, // Si no hay end, usar start
                                        backgroundColor: '#d4a574',
                                        borderColor: '#b8945f',
                                        textColor: '#fff',
                                        extendedProps: {
                                            observaciones: appointment.extendedProps?.observaciones || 'Sin observaciones',
                                            costo: appointment.extendedProps?.costo || 'Sin especificar',
                                            status: appointment.extendedProps?.status || 'Sin estado',
                                            notas_internas: appointment.extendedProps?.notas_internas || 'Sin notas'
                                        }
                                    };
                                    
                                    console.log('Evento creado:', event);
                                    return event;
                                }).filter(event => event !== null); // Filtrar eventos nulos
                                
                                console.log('Eventos finales para el calendario:', events);
                                successCallback(events);
                            })
                            .catch(error => {
                                console.error('Error cargando citas:', error);
                                failureCallback(error);
                            });
                    },
                    eventClick: function(info) {
                        console.log('Cita clickeada:', info.event);
                        alert(`Cliente: ${info.event.title}\nTratamiento: ${info.event.extendedProps.tipo_tratamiento}\nObservaciones: ${info.event.extendedProps.observaciones}`);
                    }
                });
                calendar.render();
                calendarEl.classList.add('fc');
            }
        }
        
        // Función para cargar disponibilidad
        async function loadAvailability() {
            try {
                console.log('Cargando disponibilidad...');
                const response = await fetch('api/clients.php?action=get_availability');
                console.log('Respuesta del API:', response);
                
                if (response.ok) {
                    const availability = await response.json();
                    console.log('Disponibilidad cargada:', availability);
                    renderAvailabilityTable(availability);
                } else {
                    console.error('Error cargando disponibilidad:', response.statusText);
                    const errorText = await response.text();
                    console.error('Error completo:', errorText);
                    // Mostrar mensaje de error en la tabla
                    const tbody = document.querySelector('#availability-table-body');
                    if (tbody) {
                        tbody.innerHTML = '<tr><td colspan="7">Error cargando disponibilidad: ' + response.statusText + '</td></tr>';
                    }
                }
            } catch (error) {
                console.error('Error de conexión:', error);
                // Mostrar mensaje de error en la tabla
                const tbody = document.querySelector('#availability-table-body');
                if (tbody) {
                    tbody.innerHTML = '<tr><td colspan="7">Error de conexión: ' + error.message + '</td></tr>';
                }
            }
        }
        
        // Función para renderizar la tabla de disponibilidad
        function renderAvailabilityTable(availability) {
            const tbody = document.querySelector('#availability-table-body');
            if (!tbody) return;
            
            tbody.innerHTML = '';
            
            if (availability.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7">No hay bloques de disponibilidad configurados</td></tr>';
                return;
            }
            
            availability.forEach(block => {
                // Convertir día de la semana a nombre
                const diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                const diaSemanaNombre = block.dia_semana !== null ? diasSemana[block.dia_semana] : '';
                
                // Formatear fecha específica
                const fechaFormateada = block.fecha ? new Date(block.fecha).toLocaleDateString('es-ES') : '';
                
                // Formatear tipo
                const tipoFormateado = block.tipo === 'disponible' ? '✅ Disponible' : '❌ Bloqueado';
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${tipoFormateado}</td>
                    <td>${diaSemanaNombre}</td>
                    <td>${fechaFormateada}</td>
                    <td>${block.hora_inicio || ''}</td>
                    <td>${block.hora_fin || ''}</td>
                    <td>${block.descripcion || ''}</td>
                    <td>
                        <button onclick="editAvailability(${block.id})" class="btn-edit">✏️</button>
                        <button onclick="deleteAvailability(${block.id})" class="btn-delete">🗑️</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
        
        // Función para editar cliente
        function editClient(clientId) {
            console.log('Editando cliente:', clientId);
            showEditClientModal(clientId);
        }
        
        // Función para mostrar modal de editar cliente
        function showEditClientModal(clientId) {
            // Crear modal dinámicamente
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.id = 'edit-client-modal';
            
            modal.innerHTML = `
                <div class="modal-content">
                    <span class="close-button" onclick="closeModal('edit-client-modal')">&times;</span>
                    <h3>Editar Cliente</h3>
                    <form id="edit-client-form">
                        <input type="hidden" id="edit-client-id" name="client_id" value="${clientId}">
                        <div class="form-group">
                            <label for="edit-nombre">Nombre:</label>
                            <input type="text" id="edit-nombre" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-apellidos">Apellidos:</label>
                            <input type="text" id="edit-apellidos" name="apellidos" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-telefono">Teléfono:</label>
                            <input type="tel" id="edit-telefono" name="telefono" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-email">Email:</label>
                            <input type="email" id="edit-email" name="email" required>
                        </div>
                        <button type="submit">Guardar Cambios</button>
                    </form>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Cargar datos del cliente
            loadClientData(clientId);
            
            // Event listener para el formulario
            const form = document.getElementById('edit-client-form');
            form.addEventListener('submit', handleEditClient);
        }
        
        // Función para cerrar modal
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.remove();
            }
        }
        
        // Función para cargar datos del cliente en el modal
        async function loadClientData(clientId) {
            try {
                const response = await fetch(`api/clients.php?id=${clientId}`);
                if (response.ok) {
                    const client = await response.json();
                    document.getElementById('edit-nombre').value = client.nombre || '';
                    document.getElementById('edit-apellidos').value = client.apellidos || '';
                    document.getElementById('edit-telefono').value = client.telefono || '';
                    document.getElementById('edit-email').value = client.email || '';
                }
            } catch (error) {
                console.error('Error cargando datos del cliente:', error);
            }
        }
        
        // Función para manejar la edición del cliente
        async function handleEditClient(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const clientData = {
                id: formData.get('client_id'),
                nombre: formData.get('nombre'),
                apellidos: formData.get('apellidos'),
                telefono: formData.get('telefono'),
                email: formData.get('email')
            };
            
            try {
                const response = await fetch('api/clients.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(clientData)
                });
                
                if (response.ok) {
                    alert('Cliente actualizado correctamente');
                    closeModal('edit-client-modal');
                    loadClients(); // Recargar la tabla
                } else {
                    const error = await response.text();
                    alert('Error al actualizar cliente: ' + error);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error de conexión');
            }
        }
        
        // Función para mostrar modal de nueva cita
        function showAddAppointmentModal() {
            // Crear modal dinámicamente
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.id = 'add-appointment-modal';
            
            modal.innerHTML = `
                <div class="modal-content">
                    <span class="close-button" onclick="closeModal('add-appointment-modal')">&times;</span>
                    <h3>Añadir Nueva Cita</h3>
                    <form id="add-appointment-form">
                        <div class="form-group">
                            <label for="appointment-client">Cliente:</label>
                            <select id="appointment-client" name="client_id" required>
                                <option value="">Seleccionar cliente...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="appointment-date">Fecha:</label>
                            <input type="date" id="appointment-date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="appointment-time">Hora:</label>
                            <input type="time" id="appointment-time" name="time" required>
                        </div>
                        <div class="form-group">
                            <label for="appointment-type">Tipo de Tratamiento:</label>
                            <input type="text" id="appointment-type" name="treatment_type" required>
                        </div>
                        <div class="form-group">
                            <label for="appointment-notes">Observaciones:</label>
                            <textarea id="appointment-notes" name="notes" rows="3"></textarea>
                        </div>
                        <button type="submit">Crear Cita</button>
                    </form>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Cargar lista de clientes
            loadClientsForAppointment();
            
            // Event listener para el formulario
            const form = document.getElementById('add-appointment-form');
            form.addEventListener('submit', handleAddAppointment);
        }
        
        // Función para cargar clientes en el select de citas
        async function loadClientsForAppointment() {
            try {
                const response = await fetch('api/clients.php');
                if (response.ok) {
                    const clients = await response.json();
                    const select = document.getElementById('appointment-client');
                    select.innerHTML = '<option value="">Seleccionar cliente...</option>';
                    
                    clients.forEach(client => {
                        const option = document.createElement('option');
                        option.value = client.id;
                        option.textContent = `${client.nombre} ${client.apellidos}`;
                        select.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error cargando clientes:', error);
            }
        }
        
        // Función para manejar la creación de citas
        async function handleAddAppointment(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const appointmentData = {
                client_id: formData.get('client_id'),
                fecha: formData.get('date'),
                hora: formData.get('time'),
                tipo_tratamiento: formData.get('treatment_type'),
                observaciones: formData.get('notes')
            };
            
            try {
                const response = await fetch('api/clients.php?action=add_appointment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(appointmentData)
                });
                
                if (response.ok) {
                    alert('Cita creada correctamente');
                    closeModal('add-appointment-modal');
                    // Recargar calendario
                    const calendarEl = document.getElementById('calendar');
                    if (calendarEl && calendarEl.classList.contains('fc')) {
                        const calendar = FullCalendar.getApi();
                        calendar.refetchEvents();
                    }
                } else {
                    const error = await response.text();
                    alert('Error al crear cita: ' + error);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error de conexión');
            }
        }
        
                // Función para debug de citas
        async function debugCitas() {
            const debugInfo = document.getElementById('debug-citas-info');
            const debugContent = document.getElementById('debug-content');
            
            debugInfo.style.display = 'block';
            debugContent.innerHTML = '<p>Cargando información de debug...</p>';
            
            try {
                console.log('Debug: Verificando API de citas...');
                
                // Verificar endpoint correcto
                const response = await fetch('api/clients.php?action=get_calendar_appointments');
                console.log('Debug: Respuesta del API:', response);
                
                if (!response.ok) {
                    debugContent.innerHTML = `
                        <p><strong>Error del API:</strong> ${response.status} - ${response.statusText}</p>
                        <p><strong>URL:</strong> ${response.url}</p>
                    `;
                    return;
                }
                
                const data = await response.json();
                console.log('Debug: Datos recibidos:', data);
                
                let debugHTML = `
                    <p><strong>Estado del API:</strong> ✅ Funcionando</p>
                    <p><strong>Número de citas:</strong> ${Array.isArray(data) ? data.length : 'No es array'}</p>
                    <p><strong>Tipo de datos:</strong> ${typeof data}</p>
                `;
                
                if (Array.isArray(data) && data.length > 0) {
                    debugHTML += '<p><strong>Primera cita:</strong></p><pre>' + JSON.stringify(data[0], null, 2) + '</pre>';
                    
                    // Verificar estructura de datos del endpoint correcto
                    const firstAppointment = data[0];
                    const requiredFields = ['id', 'title', 'start'];
                    const missingFields = requiredFields.filter(field => !firstAppointment[field]);
                    
                    if (missingFields.length > 0) {
                        debugHTML += `<p><strong>⚠️ Campos faltantes:</strong> ${missingFields.join(', ')}</p>`;
                    } else {
                        debugHTML += '<p><strong>✅ Estructura de datos correcta</strong></p>';
                    }
                    
                    // Verificar campos adicionales
                    if (firstAppointment.extendedProps) {
                        debugHTML += '<p><strong>✅ Propiedades extendidas disponibles</strong></p>';
                    } else {
                        debugHTML += '<p><strong>⚠️ No hay propiedades extendidas</strong></p>';
                    }
                } else {
                    debugHTML += '<p><strong>⚠️ No hay citas o datos inválidos</strong></p>';
                }
                
                // Verificar también la tabla de clientes para comparar
                try {
                    const clientsResponse = await fetch('api/clients.php');
                    if (clientsResponse.ok) {
                        const clientsData = await clientsResponse.json();
                        debugHTML += `<p><strong>📊 Comparación:</strong></p>`;
                        debugHTML += `<p>• Clientes en BD: ${Array.isArray(clientsData) ? clientsData.length : 'No es array'}</p>`;
                        debugHTML += `<p>• Citas en BD: ${Array.isArray(data) ? data.length : 'No es array'}</p>`;
                        
                        if (Array.isArray(clientsData) && clientsData.length > 0) {
                            debugHTML += `<p><strong>Primer cliente:</strong></p><pre>${JSON.stringify(clientsData[0], null, 2)}</pre>';
                        }
                    }
                } catch (clientError) {
                    debugHTML += `<p><strong>⚠️ Error cargando clientes:</strong> ${clientError.message}</p>`;
                }
                
                debugContent.innerHTML = debugHTML;
                
            } catch (error) {
                console.error('Debug: Error:', error);
                debugContent.innerHTML = `
                    <p><strong>Error de conexión:</strong> ${error.message}</p>
                    <p><strong>Stack:</strong> <pre>${error.stack}</pre></p>
                `;
            }
        }
        
        // Función para debug de disponibilidad
        async function debugAvailability() {
            const debugInfo = document.getElementById('debug-availability-info');
            const debugContent = document.getElementById('debug-availability-content');
            
            debugInfo.style.display = 'block';
            debugContent.innerHTML = '<p>Cargando información de debug...</p>';
            
            try {
                console.log('Debug: Verificando API de disponibilidad...');
                
                const response = await fetch('api/clients.php?action=get_availability');
                console.log('Debug: Respuesta del API de disponibilidad:', response);
                
                if (!response.ok) {
                    debugContent.innerHTML = `
                        <p><strong>Error del API:</strong> ${response.status} - ${response.statusText}</p>
                        <p><strong>URL:</strong> ${response.url}</p>
                    `;
                    return;
                }
                
                const data = await response.json();
                console.log('Debug: Datos de disponibilidad recibidos:', data);
                
                let debugHTML = `
                    <p><strong>Estado del API:</strong> ✅ Funcionando</p>
                    <p><strong>Número de bloques:</strong> ${Array.isArray(data) ? data.length : 'No es array'}</p>
                    <p><strong>Tipo de datos:</strong> ${typeof data}</p>
                `;
                
                if (Array.isArray(data) && data.length > 0) {
                    debugHTML += '<p><strong>Primer bloque:</strong></p><pre>' + JSON.stringify(data[0], null, 2) + '</pre>';
                    
                    // Verificar estructura de datos
                    const firstBlock = data[0];
                    const requiredFields = ['id', 'tipo', 'hora_inicio', 'hora_fin'];
                    const missingFields = requiredFields.filter(field => !firstBlock[field]);
                    
                    if (missingFields.length > 0) {
                        debugHTML += `<p><strong>⚠️ Campos faltantes:</strong> ${missingFields.join(', ')}</p>`;
                    } else {
                        debugHTML += '<p><strong>✅ Estructura de datos correcta</strong></p>';
                    }
                } else {
                    debugHTML += '<p><strong>⚠️ No hay bloques de disponibilidad</strong></p>';
                }
                
                debugContent.innerHTML = debugHTML;
                
            } catch (error) {
                console.error('Debug: Error:', error);
                debugContent.innerHTML = `
                    <p><strong>Error de conexión:</strong> ${error.message}</p>
                    <p><strong>Stack:</strong> <pre>${error.stack}</pre></p>
                `;
            }
        }
        
        // Función para eliminar cliente
        async function deleteClient(clientId) {
            if (confirm('¿Estás seguro de que quieres eliminar este cliente?')) {
                try {
                    const response = await fetch('api/clients.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: clientId })
                    });
                    
                    if (response.ok) {
                        alert('Cliente eliminado correctamente');
                        loadClients(); // Recargar la tabla
                    } else {
                        alert('Error al eliminar el cliente');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error de conexión');
                }
            }
        }
        
        // Función para editar disponibilidad
        function editAvailability(availabilityId) {
            console.log('Editando disponibilidad:', availabilityId);
            // Aquí se abriría el modal de edición
        }
        
        // Función para eliminar disponibilidad
        async function deleteAvailability(availabilityId) {
            if (confirm('¿Estás seguro de que quieres eliminar este bloque de disponibilidad?')) {
                try {
                    const response = await fetch('api/clients.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: availabilityId, type: 'availability' })
                    });
                    
                    if (response.ok) {
                        alert('Disponibilidad eliminada correctamente');
                        loadAvailability(); // Recargar la tabla
                    } else {
                        alert('Error al eliminar la disponibilidad');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error de conexión');
                }
            }
        }
        
        // Función para actualizar navegación activa
        function updateActiveNav(activeSectionId) {
            const navLinks = document.querySelectorAll('nav a');
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === activeSectionId) {
                    link.classList.add('active');
                }
            });
        }
        
        // Event listeners para navegación
        document.addEventListener('DOMContentLoaded', function() {
            // Mostrar sección de clientes por defecto
            showSection('#client-list');
            
            // Event listeners para enlaces de navegación
            const navLinks = document.querySelectorAll('nav a[href^="#"]');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetSection = this.getAttribute('href');
                    showSection(targetSection);
                });
            });
            
            // Event listeners para botones de formularios
            const showAddClientBtn = document.getElementById('show-add-client-form-btn');
            if (showAddClientBtn) {
                showAddClientBtn.addEventListener('click', function() {
                    showSection('#add-client');
                });
            }
            
            const cancelAddClientBtn = document.getElementById('cancel-add-client');
            if (cancelAddClientBtn) {
                cancelAddClientBtn.addEventListener('click', function() {
                    showSection('#client-list');
                });
            }
            
            const showAddAvailabilityBtn = document.getElementById('show-add-availability-btn');
            if (showAddAvailabilityBtn) {
                showAddAvailabilityBtn.addEventListener('click', function() {
                    showSection('#add-availability');
                });
            }
            
            const showBlockDateBtn = document.getElementById('show-block-date-btn');
            if (showBlockDateBtn) {
                showBlockDateBtn.addEventListener('click', function() {
                    showSection('#block-date');
                });
            }
            
            const cancelAddAvailabilityBtn = document.getElementById('cancel-add-availability');
            if (cancelAddAvailabilityBtn) {
                cancelAddAvailabilityBtn.addEventListener('click', function() {
                    showSection('#availability-section');
                });
            }
            
            const cancelBlockDateBtn = document.getElementById('cancel-block-date');
            if (cancelBlockDateBtn) {
                cancelBlockDateBtn.addEventListener('click', function() {
                    showSection('#availability-section');
                });
            }
            
            // Event listener para el botón de nueva cita
            const showAddAppointmentBtn = document.getElementById('show-add-appointment-btn');
            if (showAddAppointmentBtn) {
                showAddAppointmentBtn.addEventListener('click', function() {
                    showAddAppointmentModal();
                });
            }
            
            // Event listener para cambiar tipo de disponibilidad
            const availabilityTypeSelect = document.getElementById('availability-type');
            if (availabilityTypeSelect) {
                availabilityTypeSelect.addEventListener('change', function() {
                    const dayOfWeekGroup = document.getElementById('day-of-week-group');
                    const specificDateGroup = document.getElementById('specific-date-group');
                    
                    // Mostrar ambos grupos por defecto, el usuario elegirá cuál usar
                    dayOfWeekGroup.style.display = 'block';
                    specificDateGroup.style.display = 'block';
                });
            }
            
            // Event listener para el formulario de añadir cliente
            const addClientForm = document.getElementById('add-client-form');
            if (addClientForm) {
                addClientForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const clientData = {
                        nombre: formData.get('nombre'),
                        apellidos: formData.get('apellidos'),
                        telefono: formData.get('telefono'),
                        email: formData.get('email')
                    };
                    
                    try {
                        const response = await fetch('api/clients.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(clientData)
                        });
                        
                        if (response.ok) {
                            alert('Cliente añadido correctamente');
                            this.reset();
                            showSection('#client-list');
                            loadClients(); // Recargar la tabla
                        } else {
                            const error = await response.text();
                            alert('Error al añadir cliente: ' + error);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error de conexión');
                    }
                });
            }
            
            // Event listener para el formulario de añadir disponibilidad
            const addAvailabilityForm = document.getElementById('add-availability-form');
            if (addAvailabilityForm) {
                addAvailabilityForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    
                    // Determinar si es recurrente o específico basado en los campos llenados
                    const diaSemana = formData.get('day-of-week');
                    const fechaEspecifica = formData.get('specific-date');
                    
                    // Validar que al menos uno esté lleno
                    if (!diaSemana && !fechaEspecifica) {
                        alert('Debes seleccionar un día de la semana O una fecha específica');
                        return;
                    }
                    
                    const availabilityData = {
                        tipo: formData.get('availability-type'),
                        dia_semana: diaSemana || null,
                        fecha: fechaEspecifica || null,
                        hora_inicio: formData.get('start-time'),
                        hora_fin: formData.get('end-time'),
                        descripcion: formData.get('description')
                    };
                    
                    console.log('Enviando datos de disponibilidad:', availabilityData);
                    
                    try {
                        const response = await fetch('api/clients.php?action=add_availability', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(availabilityData)
                        });
                        
                        if (response.ok) {
                            alert('Disponibilidad añadida correctamente');
                            this.reset();
                            showSection('#availability-section');
                            loadAvailability(); // Recargar la tabla
                        } else {
                            const error = await response.text();
                            alert('Error al añadir disponibilidad: ' + error);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error de conexión');
                    }
                });
            }
            
            // Event listener para el formulario de bloquear fecha
            const blockDateForm = document.getElementById('block-date-form');
            if (blockDateForm) {
                blockDateForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const blockData = {
                        fecha: formData.get('block-date'),
                        motivo: formData.get('block-reason')
                    };
                    
                    try {
                        const response = await fetch('api/clients.php?action=block_date', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(blockData)
                        });
                        
                        if (response.ok) {
                            alert('Fecha bloqueada correctamente');
                            this.reset();
                            showSection('#availability-section');
                            loadAvailability(); // Recargar la tabla
                        } else {
                            const error = await response.text();
                            alert('Error al bloquear fecha: ' + error);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error de conexión');
                    }
                });
            }
        });
    </script>
    <script src="script.js"></script>
</body>
</html>
