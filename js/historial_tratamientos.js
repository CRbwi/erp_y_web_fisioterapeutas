/**
 * JavaScript para el Historial de Tratamientos
 * Maneja toda la funcionalidad de la página
 */

class HistorialTratamientos {
    constructor() {
        this.historial = [];
        this.clientes = [];
        this.tiposTratamiento = [];
        this.escalasEvaluacion = [];
        this.currentSession = null;
        this.isEditing = false;
        
        this.init();
    }
    
    async init() {
        await this.loadInitialData();
        this.setupEventListeners();
        this.loadHistorial();
        this.updateStats();
    }
    
    async loadInitialData() {
        try {
            // Cargar clientes
            const clientesResponse = await fetch('api/clients.php');
            this.clientes = await clientesResponse.json();
            
            // Cargar tipos de tratamiento
            const tiposResponse = await fetch('api/historial.php?action=get_tipos_tratamiento');
            this.tiposTratamiento = await tiposResponse.json();
            
            // Cargar escalas de evaluación
            const escalasResponse = await fetch('api/historial.php?action=get_escalas_evaluacion');
            this.escalasEvaluacion = await escalasResponse.json();
            
            this.populateDropdowns();
        } catch (error) {
            console.error('Error cargando datos iniciales:', error);
            this.showNotification('Error cargando datos iniciales', 'error');
        }
    }
    
    populateDropdowns() {
        // Poblar dropdown de clientes
        const clientSelect = document.getElementById('session-client');
        clientSelect.innerHTML = '<option value="">Seleccionar paciente...</option>';
        this.clientes.forEach(cliente => {
            const option = document.createElement('option');
            option.value = cliente.id;
            option.textContent = `${cliente.nombre} ${cliente.apellidos}`;
            clientSelect.appendChild(option);
        });
        
        // Poblar dropdown de tipos de tratamiento
        const treatmentSelect = document.getElementById('session-type');
        treatmentSelect.innerHTML = '<option value="">Seleccionar tratamiento...</option>';
        this.tiposTratamiento.forEach(tipo => {
            const option = document.createElement('option');
            option.value = tipo.nombre;
            option.textContent = tipo.nombre;
            option.title = tipo.descripcion;
            treatmentSelect.appendChild(option);
        });
        
        // Poblar filtro de tratamientos
        const filterTreatment = document.getElementById('filter-treatment');
        filterTreatment.innerHTML = '<option value="">Todos los tratamientos</option>';
        this.tiposTratamiento.forEach(tipo => {
            const option = document.createElement('option');
            option.value = tipo.nombre;
            option.textContent = tipo.nombre;
            filterTreatment.appendChild(option);
        });
    }
    
    setupEventListeners() {
        // Formulario de sesión
        document.getElementById('session-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSessionSubmit();
        });
        
        // Filtros de búsqueda
        document.getElementById('search-patient').addEventListener('input', () => this.applyFilters());
        document.getElementById('filter-treatment').addEventListener('change', () => this.applyFilters());
        document.getElementById('filter-date-from').addEventListener('change', () => this.applyFilters());
        document.getElementById('filter-date-to').addEventListener('change', () => this.applyFilters());
        
        // Cerrar modales al hacer clic fuera
        window.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                this.closeAllModals();
            }
        });
    }
    
    async loadHistorial() {
        try {
            const response = await fetch('api/historial.php');
            this.historial = await response.json();
            this.renderHistorialTable();
        } catch (error) {
            console.error('Error cargando historial:', error);
            this.showNotification('Error cargando historial', 'error');
        }
    }
    
    renderHistorialTable() {
        const tbody = document.getElementById('historial-table-body');
        tbody.innerHTML = '';
        
        if (this.historial.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 40px;">No hay sesiones registradas</td></tr>';
            return;
        }
        
        this.historial.forEach(sesion => {
            const row = this.createHistorialRow(sesion);
            tbody.appendChild(row);
        });
    }
    
    createHistorialRow(sesion) {
        const row = document.createElement('tr');
        
        const fecha = new Date(sesion.fecha_sesion).toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const paciente = `${sesion.cliente_nombre} ${sesion.cliente_apellidos}`;
        const duracion = sesion.duracion_sesion ? `${sesion.duracion_sesion} min` : '-';
        
        const dolorAntes = this.renderPainScale(sesion.dolor_antes);
        const dolorDespues = this.renderPainScale(sesion.dolor_despues);
        
        const evolucion = this.calculatePainEvolution(sesion.dolor_antes, sesion.dolor_despues);
        
        row.innerHTML = `
            <td>${fecha}</td>
            <td>${paciente}</td>
            <td><span class="treatment-type">${sesion.tipo_tratamiento}</span></td>
            <td>${duracion}</td>
            <td>${dolorAntes}</td>
            <td>${dolorDespues}</td>
            <td>${evolucion}</td>
            <td class="action-buttons">
                <button class="btn-small btn-view" onclick="historialApp.viewSession(${sesion.id})">👁️</button>
                <button class="btn-small btn-edit" onclick="historialApp.editSession(${sesion.id})">✏️</button>
                <button class="btn-small btn-delete" onclick="historialApp.deleteSession(${sesion.id})">🗑️</button>
            </td>
        `;
        
        return row;
    }
    
    renderPainScale(painLevel) {
        if (painLevel === null || painLevel === undefined) return '-';
        
        let painClass = 'pain-0-3';
        if (painLevel >= 4 && painLevel <= 6) painClass = 'pain-4-6';
        else if (painLevel >= 7) painClass = 'pain-7-10';
        
        return `<span class="pain-scale ${painClass}">${painLevel}</span>`;
    }
    
    calculatePainEvolution(dolorAntes, dolorDespues) {
        if (dolorAntes === null || dolorDespues === null) return '-';
        
        const diferencia = dolorAntes - dolorDespues;
        if (diferencia > 0) {
            return `<span style="color: #4CAF50;">↓ ${diferencia}</span>`;
        } else if (diferencia < 0) {
            return `<span style="color: #f44336;">↑ ${Math.abs(diferencia)}</span>`;
        } else {
            return '<span style="color: #FF9800;">→ 0</span>';
        }
    }
    
    applyFilters() {
        const searchTerm = document.getElementById('search-patient').value.toLowerCase();
        const treatmentFilter = document.getElementById('filter-treatment').value;
        const dateFrom = document.getElementById('filter-date-from').value;
        const dateTo = document.getElementById('filter-date-to').value;
        
        const filteredHistorial = this.historial.filter(sesion => {
            const paciente = `${sesion.cliente_nombre} ${sesion.cliente_apellidos}`.toLowerCase();
            const matchesSearch = !searchTerm || paciente.includes(searchTerm);
            
            const matchesTreatment = !treatmentFilter || sesion.tipo_tratamiento === treatmentFilter;
            
            const sesionDate = new Date(sesion.fecha_sesion).toISOString().split('T')[0];
            const matchesDateFrom = !dateFrom || sesionDate >= dateFrom;
            const matchesDateTo = !dateTo || sesionDate <= dateTo;
            
            return matchesSearch && matchesTreatment && matchesDateFrom && matchesDateTo;
        });
        
        this.renderFilteredHistorial(filteredHistorial);
    }
    
    renderFilteredHistorial(filteredHistorial) {
        const tbody = document.getElementById('historial-table-body');
        tbody.innerHTML = '';
        
        if (filteredHistorial.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 40px;">No se encontraron sesiones con los filtros aplicados</td></tr>';
            return;
        }
        
        filteredHistorial.forEach(sesion => {
            const row = this.createHistorialRow(sesion);
            tbody.appendChild(row);
        });
    }
    
    updateStats() {
        const now = new Date();
        const thirtyDaysAgo = new Date(now.getTime() - (30 * 24 * 60 * 60 * 1000));
        
        // Sesiones del mes
        const sesionesMes = this.historial.filter(sesion => 
            new Date(sesion.fecha_sesion) >= thirtyDaysAgo
        ).length;
        
        // Pacientes activos
        const pacientesActivos = new Set(
            this.historial
                .filter(sesion => new Date(sesion.fecha_sesion) >= thirtyDaysAgo)
                .map(sesion => sesion.client_id)
        ).size;
        
        // Tipos de tratamiento únicos
        const tiposUnicos = new Set(this.historial.map(sesion => sesion.tipo_tratamiento)).size;
        
        // Promedio de duración
        const sesionesConDuracion = this.historial.filter(sesion => sesion.duracion_sesion);
        const promedioDuracion = sesionesConDuracion.length > 0 
            ? Math.round(sesionesConDuracion.reduce((sum, sesion) => sum + sesion.duracion_sesion, 0) / sesionesConDuracion.length)
            : 0;
        
        document.getElementById('total-sesiones').textContent = sesionesMes;
        document.getElementById('pacientes-activos').textContent = pacientesActivos;
        document.getElementById('tipos-tratamiento').textContent = tiposUnicos;
        document.getElementById('promedio-duracion').textContent = promedioDuracion;
    }
    
    openAddSessionModal() {
        this.isEditing = false;
        this.currentSession = null;
        document.getElementById('modal-title').textContent = 'Nueva Sesión de Tratamiento';
        document.getElementById('session-form').reset();
        document.getElementById('session-date').value = new Date().toISOString().slice(0, 16);
        document.getElementById('sessionModal').style.display = 'block';
    }
    
    async editSession(sesionId) {
        try {
            const sesion = this.historial.find(s => s.id === sesionId);
            if (!sesion) {
                this.showNotification('Sesión no encontrada', 'error');
                return;
            }
            
            this.isEditing = true;
            this.currentSession = sesion;
            document.getElementById('modal-title').textContent = 'Editar Sesión de Tratamiento';
            
            // Poblar formulario
            this.populateSessionForm(sesion);
            
            document.getElementById('sessionModal').style.display = 'block';
        } catch (error) {
            console.error('Error editando sesión:', error);
            this.showNotification('Error editando sesión', 'error');
        }
    }
    
    populateSessionForm(sesion) {
        document.getElementById('session-id').value = sesion.id;
        document.getElementById('session-client').value = sesion.client_id;
        document.getElementById('session-date').value = sesion.fecha_sesion.slice(0, 16);
        document.getElementById('session-type').value = sesion.tipo_tratamiento;
        document.getElementById('session-duration').value = sesion.duracion_sesion || '';
        document.getElementById('session-intensity').value = sesion.intensidad || 'moderada';
        document.getElementById('session-pain-before').value = sesion.dolor_antes || '';
        document.getElementById('session-pain-after').value = sesion.dolor_despues || '';
        document.getElementById('session-description').value = sesion.descripcion_tratamiento || '';
        document.getElementById('session-observations').value = sesion.observaciones_evolucion || '';
        document.getElementById('session-symptoms-before').value = sesion.sintomas_iniciales || '';
        document.getElementById('session-symptoms-after').value = sesion.sintomas_actuales || '';
        document.getElementById('session-objectives').value = sesion.objetivos_tratamiento || '';
        document.getElementById('session-techniques').value = sesion.tecnicas_aplicadas || '';
        document.getElementById('session-mobility-before').value = sesion.movilidad_antes || '';
        document.getElementById('session-mobility-after').value = sesion.movilidad_despues || '';
        document.getElementById('session-recommendations').value = sesion.recomendaciones || '';
        document.getElementById('session-exercises').value = sesion.ejercicios_prescritos || '';
        document.getElementById('session-next-session').value = sesion.proxima_sesion || '';
        document.getElementById('session-notes').value = sesion.notas_internas || '';
    }
    
    async handleSessionSubmit() {
        try {
            const formData = new FormData(document.getElementById('session-form'));
            const sessionData = Object.fromEntries(formData.entries());
            
            // Validar campos requeridos
            if (!sessionData.client_id || !sessionData.fecha_sesion || !sessionData.tipo_tratamiento) {
                this.showNotification('Por favor, completa todos los campos requeridos', 'error');
                return;
            }
            
            const url = this.isEditing 
                ? 'api/historial.php?action=update_sesion'
                : 'api/historial.php?action=add_sesion';
            
            const method = this.isEditing ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(sessionData)
            });
            
            const result = await response.json();
            
            if (response.ok) {
                this.showNotification(result.message, 'success');
                this.closeSessionModal();
                await this.loadHistorial();
                this.updateStats();
            } else {
                this.showNotification('Error: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Error guardando sesión:', error);
            this.showNotification('Error guardando sesión', 'error');
        }
    }
    
    async deleteSession(sesionId) {
        if (!confirm('¿Estás seguro de que quieres eliminar esta sesión? Esta acción no se puede deshacer.')) {
            return;
        }
        
        try {
            const response = await fetch('api/historial.php?action=delete_sesion', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: sesionId })
            });
            
            const result = await response.json();
            
            if (response.ok) {
                this.showNotification(result.message, 'success');
                await this.loadHistorial();
                this.updateStats();
            } else {
                this.showNotification('Error: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Error eliminando sesión:', error);
            this.showNotification('Error eliminando sesión', 'error');
        }
    }
    
    async viewSession(sesionId) {
        try {
            const sesion = this.historial.find(s => s.id === sesionId);
            if (!sesion) {
                this.showNotification('Sesión no encontrada', 'error');
                return;
            }
            
            const content = this.createSessionDetailsHTML(sesion);
            document.getElementById('session-details-content').innerHTML = content;
            document.getElementById('detailsModal').style.display = 'block';
        } catch (error) {
            console.error('Error mostrando detalles:', error);
            this.showNotification('Error mostrando detalles', 'error');
        }
    }
    
    createSessionDetailsHTML(sesion) {
        const fecha = new Date(sesion.fecha_sesion).toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const paciente = `${sesion.cliente_nombre} ${sesion.cliente_apellidos}`;
        
        return `
            <div style="color: #ccc;">
                <div style="margin-bottom: 20px; padding: 15px; background-color: #2a2a2a; border-radius: 6px;">
                    <h3 style="color: #4CAF50; margin-top: 0;">📅 ${fecha}</h3>
                    <p><strong>Paciente:</strong> ${paciente}</p>
                    <p><strong>Tratamiento:</strong> <span class="treatment-type">${sesion.tipo_tratamiento}</span></p>
                    <p><strong>Duración:</strong> ${sesion.duracion_sesion ? sesion.duracion_sesion + ' minutos' : 'No especificada'}</p>
                    <p><strong>Intensidad:</strong> ${sesion.intensidad || 'No especificada'}</p>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div style="padding: 15px; background-color: #2a2a2a; border-radius: 6px;">
                        <h4 style="color: #FF9800; margin-top: 0;">📊 Evaluación del Dolor</h4>
                        <p><strong>Antes:</strong> ${this.renderPainScale(sesion.dolor_antes)}</p>
                        <p><strong>Después:</strong> ${this.renderPainScale(sesion.dolor_despues)}</p>
                        <p><strong>Evolución:</strong> ${this.calculatePainEvolution(sesion.dolor_antes, sesion.dolor_despues)}</p>
                    </div>
                    
                    <div style="padding: 15px; background-color: #2a2a2a; border-radius: 6px;">
                        <h4 style="color: #2196F3; margin-top: 0;">🏃 Movilidad</h4>
                        <p><strong>Antes:</strong> ${sesion.movilidad_antes || 'No especificada'}</p>
                        <p><strong>Después:</strong> ${sesion.movilidad_despues || 'No especificada'}</p>
                    </div>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <h4 style="color: #4CAF50;">📝 Descripción del Tratamiento</h4>
                    <p style="padding: 15px; background-color: #2a2a2a; border-radius: 6px;">${sesion.descripcion_tratamiento || 'No especificada'}</p>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <h4 style="color: #FF9800;">📋 Observaciones de Evolución</h4>
                    <p style="padding: 15px; background-color: #2a2a2a; border-radius: 6px;">${sesion.observaciones_evolucion || 'No especificadas'}</p>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <h4 style="color: #2196F3;">🩺 Síntomas</h4>
                        <p><strong>Iniciales:</strong> ${sesion.sintomas_iniciales || 'No especificados'}</p>
                        <p><strong>Actuales:</strong> ${sesion.sintomas_actuales || 'No especificados'}</p>
                    </div>
                    
                    <div>
                        <h4 style="color: #4CAF50;">🎯 Objetivos</h4>
                        <p>${sesion.objetivos_tratamiento || 'No especificados'}</p>
                    </div>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <h4 style="color: #FF9800;">🖐️ Técnicas Aplicadas</h4>
                    <p style="padding: 15px; background-color: #2a2a2a; border-radius: 6px;">${sesion.tecnicas_aplicadas || 'No especificadas'}</p>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <h4 style="color: #2196F3;">💪 Ejercicios Prescritos</h4>
                    <p style="padding: 15px; background-color: #2a2a2a; border-radius: 6px;">${sesion.ejercicios_prescritos || 'No especificados'}</p>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <h4 style="color: #4CAF50;">💡 Recomendaciones</h4>
                    <p style="padding: 15px; background-color: #2a2a2a; border-radius: 6px;">${sesion.recomendaciones || 'No especificadas'}</p>
                </div>
                
                ${sesion.proxima_sesion ? `
                <div style="margin-bottom: 20px;">
                    <h4 style="color: #FF9800;">📅 Próxima Sesión</h4>
                    <p style="padding: 15px; background-color: #2a2a2a; border-radius: 6px;">${new Date(sesion.proxima_sesion).toLocaleDateString('es-ES')}</p>
                </div>
                ` : ''}
                
                ${sesion.notas_internas ? `
                <div style="margin-bottom: 20px;">
                    <h4 style="color: #9C27B0;">📝 Notas Internas</h4>
                    <p style="padding: 15px; background-color: #2a2a2a; border-radius: 6px;">${sesion.notas_internas}</p>
                </div>
                ` : ''}
            </div>
        `;
    }
    
    closeSessionModal() {
        document.getElementById('sessionModal').style.display = 'none';
        document.getElementById('session-form').reset();
    }
    
    closeDetailsModal() {
        document.getElementById('detailsModal').style.display = 'none';
    }
    
    closeAllModals() {
        this.closeSessionModal();
        this.closeDetailsModal();
    }
    
    showNotification(message, type = 'info') {
        // Crear notificación temporal
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 6px;
            color: white;
            font-weight: bold;
            z-index: 10000;
            max-width: 300px;
            word-wrap: break-word;
        `;
        
        switch (type) {
            case 'success':
                notification.style.backgroundColor = '#4CAF50';
                break;
            case 'error':
                notification.style.backgroundColor = '#f44336';
                break;
            case 'warning':
                notification.style.backgroundColor = '#FF9800';
                break;
            default:
                notification.style.backgroundColor = '#2196F3';
        }
        
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
    }
    
    exportHistorial() {
        // Implementar exportación a CSV/Excel
        this.showNotification('Función de exportación se implementará próximamente', 'info');
    }
}

// Inicializar la aplicación cuando se carga la página
let historialApp;
document.addEventListener('DOMContentLoaded', () => {
    historialApp = new HistorialTratamientos();
});

// Funciones globales para los botones HTML
function openAddSessionModal() {
    historialApp.openAddSessionModal();
}

function closeSessionModal() {
    historialApp.closeSessionModal();
}

function closeDetailsModal() {
    historialApp.closeDetailsModal();
}

function exportHistorial() {
    historialApp.exportHistorial();
}
