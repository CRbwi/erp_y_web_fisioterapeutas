<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Tratamientos - Jorge Hernandez Fisioterapeuta</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css' rel='stylesheet' />
    <style>
        .historial-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 1.2rem;
        }
        
        .stat-card .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .stat-card .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .controls-section {
            background-color: #1a1a1a;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }
        
        .control-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .control-group label {
            font-weight: bold;
            color: #ccc;
        }
        
        .btn-primary {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: #45a049;
        }
        
        .btn-secondary {
            background-color: #2196F3;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        
        .btn-secondary:hover {
            background-color: #1976D2;
        }
        
        .historial-table {
            background-color: #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .historial-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .historial-table th {
            background-color: #2a2a2a;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #444;
        }
        
        .historial-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #333;
            color: #ccc;
        }
        
        .historial-table tr:hover {
            background-color: #2a2a2a;
        }
        
        .treatment-type {
            background-color: #4CAF50;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .pain-scale {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            color: white;
        }
        
        .pain-0-3 { background-color: #4CAF50; }
        .pain-4-6 { background-color: #FF9800; }
        .pain-7-10 { background-color: #f44336; }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-small {
            padding: 6px 12px;
            font-size: 0.8rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: opacity 0.3s ease;
        }
        
        .btn-small:hover {
            opacity: 0.8;
        }
        
        .btn-edit { background-color: #2196F3; color: white; }
        .btn-delete { background-color: #f44336; color: white; }
        .btn-view { background-color: #4CAF50; color: white; }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: #1a1a1a;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .form-group label {
            font-weight: bold;
            color: #ccc;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 10px;
            border: 1px solid #444;
            border-radius: 4px;
            background-color: #333;
            color: white;
            font-size: 14px;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            justify-content: flex-end;
        }
        
        .search-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: end;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .filter-group label {
            font-weight: bold;
            color: #ccc;
            font-size: 0.9rem;
        }
        
        .filter-group input,
        .filter-group select {
            padding: 8px;
            border: 1px solid #444;
            border-radius: 4px;
            background-color: #333;
            color: white;
            font-size: 14px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: white;
        }
        
        .chart-container {
            background-color: #1a1a1a;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .chart-title {
            color: #ccc;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
        
        .chart-placeholder {
            background-color: #2a2a2a;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            border-radius: 4px;
        }
        
        @media (max-width: 768px) {
            .controls-section {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-filters {
                flex-direction: column;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .historial-table {
                overflow-x: auto;
            }
            
            .historial-table table {
                min-width: 800px;
            }
            
            header > div {
                flex-direction: column;
                gap: 15px;
            }
            
            header .btn-secondary {
                font-size: 0.9rem;
                padding: 8px 12px;
            }
            
            .navegacion-rapida {
                padding: 15px;
            }
            
            .navegacion-rapida > div {
                flex-direction: column;
                gap: 10px;
            }
            
            .navegacion-rapida .btn-primary,
            .navegacion-rapida .btn-secondary {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="historial-container">
        <header>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h1>🏥 Historial de Tratamientos</h1>
                    <p>Gestión completa del historial médico de todos los pacientes</p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <a href="index.php" class="btn-secondary" style="text-decoration: none;">
                        🏠 Panel Principal
                    </a>
                    <a href="index.php#client-list" class="btn-secondary" style="text-decoration: none;">
                        👥 Clientes
                    </a>
                    <a href="index.php#calendar-section" class="btn-secondary" style="text-decoration: none;">
                        📅 Calendario
                    </a>
                </div>
            </div>
        </header>
        
        <!-- Estadísticas Generales -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Sesiones</h3>
                <div class="stat-value" id="total-sesiones">0</div>
                <div class="stat-label">Este mes</div>
            </div>
            <div class="stat-card">
                <h3>Pacientes Activos</h3>
                <div class="stat-value" id="pacientes-activos">0</div>
                <div class="stat-label">Últimos 30 días</div>
            </div>
            <div class="stat-card">
                <h3>Tratamientos</h3>
                <div class="stat-value" id="tipos-tratamiento">0</div>
                <div class="stat-label">Diferentes tipos</div>
            </div>
            <div class="stat-card">
                <h3>Promedio Sesión</h3>
                <div class="stat-value" id="promedio-duracion">0</div>
                <div class="stat-label">Minutos</div>
            </div>
        </div>
        
        <!-- Controles y Filtros -->
        <div class="controls-section">
            <div class="search-filters">
                <div class="filter-group">
                    <label for="search-patient">Buscar Paciente</label>
                    <input type="text" id="search-patient" placeholder="Nombre, apellidos...">
                </div>
                <div class="filter-group">
                    <label for="filter-treatment">Tipo Tratamiento</label>
                    <select id="filter-treatment">
                        <option value="">Todos los tratamientos</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filter-date-from">Desde</label>
                    <input type="date" id="filter-date-from">
                </div>
                <div class="filter-group">
                    <label for="filter-date-to">Hasta</label>
                    <input type="date" id="filter-date-to">
                </div>
            </div>
            
            <button class="btn-primary" onclick="openAddSessionModal()">
                ➕ Nueva Sesión
            </button>
            <button class="btn-secondary" onclick="exportHistorial()">
                📊 Exportar
            </button>
        </div>
        
        <!-- Gráficos de Evolución -->
        <div class="chart-container">
            <h3 class="chart-title">📈 Evolución del Dolor - Últimos 30 días</h3>
            <div class="chart-placeholder" id="pain-chart">
                Gráfico de evolución del dolor se cargará aquí
            </div>
        </div>
        
        <!-- Tabla del Historial -->
        <div class="historial-table">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Paciente</th>
                        <th>Tratamiento</th>
                        <th>Duración</th>
                        <th>Dolor Antes</th>
                        <th>Dolor Después</th>
                        <th>Evolución</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="historial-table-body">
                    <!-- Los datos se cargarán dinámicamente -->
                </tbody>
            </table>
        </div>
        
        <!-- Navegación al final -->
        <div class="navegacion-rapida" style="text-align: center; margin-top: 40px; padding: 20px; background-color: #1a1a1a; border-radius: 8px;">
            <h3 style="color: #ccc; margin-bottom: 20px;">Navegación Rápida</h3>
            <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                <a href="index.php" class="btn-primary" style="text-decoration: none;">
                    🏠 Volver al Panel Principal
                </a>
                <a href="index.php#client-list" class="btn-secondary" style="text-decoration: none;">
                    👥 Gestionar Clientes
                </a>
                <a href="index.php#calendar-section" class="btn-secondary" style="text-decoration: none;">
                    📅 Ver Calendario
                </a>
                <a href="index.php#availability-section" class="btn-secondary" style="text-decoration: none;">
                    ⏰ Gestionar Disponibilidad
                </a>
                <a href="google_calendar_auth.php" class="btn-secondary" style="text-decoration: none;">
                    🔗 Google Calendar
                </a>
            </div>
        </div>
    </div>
    
    <!-- Modal para Añadir/Editar Sesión -->
    <div id="sessionModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeSessionModal()">&times;</span>
            <h2 id="modal-title">Nueva Sesión de Tratamiento</h2>
            
            <form id="session-form">
                <input type="hidden" id="session-id" name="id">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="session-client">Paciente *</label>
                        <select id="session-client" name="client_id" required>
                            <option value="">Seleccionar paciente...</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="session-date">Fecha y Hora *</label>
                        <input type="datetime-local" id="session-date" name="fecha_sesion" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="session-type">Tipo de Tratamiento *</label>
                        <select id="session-type" name="tipo_tratamiento" required>
                            <option value="">Seleccionar tratamiento...</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="session-duration">Duración (minutos)</label>
                        <input type="number" id="session-duration" name="duracion_sesion" min="15" max="180">
                    </div>
                    
                    <div class="form-group">
                        <label for="session-intensity">Intensidad</label>
                        <select id="session-intensity" name="intensidad">
                            <option value="leve">Leve</option>
                            <option value="moderada" selected>Moderada</option>
                            <option value="intensa">Intensa</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="session-pain-before">Dolor Antes (0-10)</label>
                        <input type="number" id="session-pain-before" name="dolor_antes" min="0" max="10">
                    </div>
                    
                    <div class="form-group">
                        <label for="session-pain-after">Dolor Después (0-10)</label>
                        <input type="number" id="session-pain-after" name="dolor_despues" min="0" max="10">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="session-description">Descripción del Tratamiento</label>
                    <textarea id="session-description" name="descripcion_tratamiento" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="session-observations">Observaciones de Evolución</label>
                    <textarea id="session-observations" name="observaciones_evolucion" rows="3"></textarea>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="session-symptoms-before">Síntomas Iniciales</label>
                        <textarea id="session-symptoms-before" name="sintomas_iniciales" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="session-symptoms-after">Síntomas Actuales</label>
                        <textarea id="session-symptoms-after" name="sintomas_actuales" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="session-objectives">Objetivos del Tratamiento</label>
                    <textarea id="session-objectives" name="objetivos_tratamiento" rows="2"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="session-techniques">Técnicas Aplicadas</label>
                    <textarea id="session-techniques" name="tecnicas_aplicadas" rows="2"></textarea>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="session-mobility-before">Movilidad Antes</label>
                        <textarea id="session-mobility-before" name="movilidad_antes" rows="2"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="session-mobility-after">Movilidad Después</label>
                        <textarea id="session-mobility-after" name="movilidad_despues" rows="2"></textarea>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="session-recommendations">Recomendaciones</label>
                    <textarea id="session-recommendations" name="recomendaciones" rows="2"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="session-exercises">Ejercicios Prescritos</label>
                    <textarea id="session-exercises" name="ejercicios_prescritos" rows="3"></textarea>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="session-next-session">Próxima Sesión</label>
                        <input type="date" id="session-next-session" name="proxima_sesion">
                    </div>
                    
                    <div class="form-group">
                        <label for="session-notes">Notas Internas</label>
                        <textarea id="session-notes" name="notas_internas" rows="2"></textarea>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeSessionModal()">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar Sesión</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal para Ver Detalles -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDetailsModal()">&times;</span>
            <h2>Detalles de la Sesión</h2>
            <div id="session-details-content">
                <!-- Los detalles se cargarán dinámicamente -->
            </div>
        </div>
    </div>
    
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script src="js/historial_tratamientos.js"></script>
</body>
</html>
