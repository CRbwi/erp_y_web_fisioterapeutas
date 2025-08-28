<?php
require_once 'auth_check.php';
requireAdminLogin();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - Centro de Fisioterapia</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .admin-header {
            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }
        
        .admin-header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .admin-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .user-info {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 25px;
            backdrop-filter: blur(10px);
        }
        
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.9rem;
            margin-left: 15px;
            transition: background-color 0.3s ease;
        }
        
        .logout-btn:hover {
            background: #c0392b;
        }
        
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .admin-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e1e8ed;
        }
        
        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .admin-card .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .admin-card h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        
        .admin-card p {
            color: #666;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .admin-card .btn {
            background: linear-gradient(135deg, #d4a574, #b8945f);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .admin-card .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .stats-section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        
        .stat-item {
            text-align: center;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #4CAF50;
        }
        
        .stat-item .number {
            font-size: 2rem;
            font-weight: bold;
            color: #d4a574;
            margin-bottom: 0.5rem;
        }
        
        .stat-item .label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .quick-actions {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .quick-actions h3 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .quick-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
        }
        
        .quick-btn {
            background: linear-gradient(135deg, #2c2c2c, #1a1a1a);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: transform 0.3s ease;
        }
        
        .quick-btn:hover {
            transform: translateY(-2px);
        }
        
        .back-to-public {
            text-align: center;
            margin-top: 2rem;
        }
        
        .back-to-public a {
            color: #d4a574;
            text-decoration: none;
            font-size: 1rem;
        }
        
        .back-to-public a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .admin-header {
                padding: 1.5rem;
            }
            
            .admin-header h1 {
                font-size: 2rem;
            }
            
            .user-info {
                position: static;
                margin-bottom: 1rem;
            }
            
            .admin-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
            
            .quick-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Header Administrativo -->
        <div class="admin-header">
            <div class="user-info">
                👤 Bienvenido, <?php echo htmlspecialchars(getAdminUsername()); ?>
                <a href="?logout=1" class="logout-btn">🚪 Cerrar Sesión</a>
            </div>
            
            <h1>🏥 Panel Administrativo</h1>
            <p>Centro de Fisioterapia Jorge Hernández - Gestión Completa</p>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="stats-section">
            <h3 style="color: #2c3e50; margin-bottom: 1.5rem; text-align: center;">📊 Estadísticas del Sistema</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="number" id="total-clients">-</div>
                    <div class="label">Total Clientes</div>
                </div>
                <div class="stat-item">
                    <div class="number" id="total-appointments">-</div>
                    <div class="label">Citas Programadas</div>
                </div>
                <div class="stat-item">
                    <div class="number" id="total-sessions">-</div>
                    <div class="label">Sesiones Registradas</div>
                </div>
                <div class="stat-item">
                    <div class="number" id="pending-bookings">-</div>
                    <div class="label">Reservas Pendientes</div>
                </div>
            </div>
        </div>

        <!-- Funcionalidades Principales -->
        <div class="admin-grid">
            <div class="admin-card">
                <div class="icon">👥</div>
                <h3>Gestión de Clientes</h3>
                <p>Administra la base de datos de pacientes, añade nuevos clientes, edita información y consulta historiales.</p>
                <a href="../admin_panel.php#client-list" class="btn">Gestionar Clientes</a>
            </div>

            <div class="admin-card">
                <div class="icon">📅</div>
                <h3>Calendario de Citas</h3>
                <p>Visualiza y gestiona todas las citas programadas, organiza horarios y mantén el control del flujo de pacientes.</p>
                <a href="../admin_panel.php#calendar-section" class="btn">Ver Calendario</a>
            </div>

            <div class="admin-card">
                <div class="icon">⏰</div>
                <h3>Gestión de Disponibilidad</h3>
                <p>Configura horarios de trabajo, bloquea fechas no disponibles y establece patrones de disponibilidad recurrente.</p>
                <a href="../admin_panel.php#availability-section" class="btn">Gestionar Horarios</a>
            </div>

            <div class="admin-card">
                <div class="icon">🏥</div>
                <h3>Historial Médico</h3>
                <p>Registra sesiones de tratamiento, consulta historiales completos de pacientes y analiza la evolución de tratamientos.</p>
                <a href="../historial_tratamientos.php" class="btn">Ver Historial</a>
            </div>

            <div class="admin-card">
                <div class="icon">🔗</div>
                <h3>Google Calendar</h3>
                <p>Sincroniza tu agenda con Google Calendar, mantén todo organizado y accede desde cualquier dispositivo.</p>
                <a href="../google_calendar_auth.php" class="btn">Configurar</a>
            </div>

            <div class="admin-card">
                <div class="icon">📊</div>
                <h3>Reportes y Estadísticas</h3>
                <p>Genera informes detallados, analiza el rendimiento de la clínica y toma decisiones basadas en datos.</p>
                <a href="#" class="btn" onclick="alert('Funcionalidad en desarrollo')">Ver Reportes</a>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="quick-actions">
            <h3>⚡ Acciones Rápidas</h3>
            <div class="quick-buttons">
                <a href="../admin_panel.php#client-list" class="quick-btn">➕ Añadir Cliente</a>
                <a href="../admin_panel.php#calendar-section" class="quick-btn">📅 Nueva Cita</a>
                <a href="../historial_tratamientos.php" class="quick-btn">🏥 Nueva Sesión</a>
                <a href="../admin_panel.php#availability-section" class="quick-btn">⏰ Configurar Horarios</a>
                <a href="../booking.php" class="quick-btn">🌐 Ver Página Pública</a>
                <a href="../index_public.php" class="quick-btn">🏠 Página Principal</a>
            </div>
        </div>

        <!-- Enlace de Volver -->
        <div class="back-to-public">
            <a href="../index_public.php">← Volver a la Página Pública</a>
        </div>
    </div>

    <script>
        // Cargar estadísticas básicas
        async function loadStats() {
            try {
                // Cargar estadísticas de clientes
                const clientsResponse = await fetch('../api/clients.php');
                if (clientsResponse.ok) {
                    const clients = await clientsResponse.json();
                    document.getElementById('total-clients').textContent = clients.length;
                }
                
                // Cargar estadísticas de citas
                const appointmentsResponse = await fetch('../api/clients.php?action=get_appointments');
                if (appointmentsResponse.ok) {
                    const appointments = await appointmentsResponse.json();
                    document.getElementById('total-appointments').textContent = appointments.length;
                }
                
                // Cargar estadísticas del historial
                const historialResponse = await fetch('../api/historial.php');
                if (historialResponse.ok) {
                    const historial = await historialResponse.json();
                    document.getElementById('total-sessions').textContent = historial.length;
                }
                
                // Cargar reservas pendientes (booking.php)
                const bookingsResponse = await fetch('../api/clients.php?action=get_pending_bookings');
                if (bookingsResponse.ok) {
                    const bookings = await bookingsResponse.json();
                    document.getElementById('pending-bookings').textContent = bookings.length || 0;
                }
                
            } catch (error) {
                console.error('Error cargando estadísticas:', error);
                // En caso de error, mostrar guiones
                document.getElementById('total-clients').textContent = '-';
                document.getElementById('total-appointments').textContent = '-';
                document.getElementById('total-sessions').textContent = '-';
                document.getElementById('pending-bookings').textContent = '-';
            }
        }

        // Cargar estadísticas al cargar la página
        document.addEventListener('DOMContentLoaded', loadStats);
    </script>
</body>
</html>
