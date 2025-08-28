<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Cita - Jorge Hernández Villena | Centro de Fisioterapia</title>
    <meta name="description" content="Reserva tu cita online con Jorge Hernández Villena, fisioterapeuta especializado en Granada. Cita previa inmediata y confirmación automática.">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }
        
        .header {
            background: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            color: white;
            padding: 1.5rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .header.scrolled {
            background: rgba(26, 26, 26, 0.98);
            padding: 1rem 0;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0 2rem;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 0.5rem;
            align-items: center;
        }
        
        .nav-menu li {
            margin: 0;
        }
        
        .nav-menu a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s ease;
            padding: 0.8rem 1.2rem;
            border-radius: 30px;
            font-size: 0.95rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }
        
        .nav-menu a:not(.cta-button):hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }
        
        .nav-menu a:not(.cta-button)::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s ease;
        }
        
        .nav-menu a:not(.cta-button):hover::before {
            left: 100%;
        }
        
        .booking-container {
            max-width: 800px;
            margin: 120px auto 40px;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .booking-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .booking-header h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .booking-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            background-color: #f8f9fa;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #d4a574;
            background-color: white;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #495057, #343a40);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 1rem;
            position: relative;
            overflow: hidden;
        }
        
        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
            background: linear-gradient(135deg, #343a40, #495057);
        }
        
        .submit-btn:hover::before {
            left: 100%;
        }
        
        .submit-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid #c3e6cb;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid #f5c6cb;
        }
        
        .loading {
            display: none;
            text-align: center;
            margin: 1rem 0;
        }
        
        .loading.show {
            display: block;
        }
        
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4CAF50;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .back-link {
            text-align: center;
            margin-top: 2rem;
        }
        
        .back-link a {
            color: #d4a574;
            text-decoration: none;
            font-size: 1rem;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .booking-container {
                margin: 120px 1rem 2rem;
                padding: 1.5rem;
            }
            
            .booking-header h1 {
                font-size: 2rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .nav-container {
                flex-direction: column;
                gap: 1rem;
                padding: 0 1rem;
            }
            
            .nav-menu {
                gap: 0.3rem;
                flex-wrap: wrap;
                justify-content: center;
                max-width: 100%;
            }
            
            .nav-menu li {
                margin: 0.15rem;
                flex-shrink: 0;
            }
            
            .nav-menu a {
                padding: 0.5rem 1rem;
                background: rgba(255,255,255,0.1);
                border-radius: 20px;
                font-size: 0.85rem;
                white-space: nowrap;
                min-width: fit-content;
            }
            
            .submit-btn {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 600px) {
            .nav-menu {
                gap: 0.25rem;
            }
            
            .nav-menu a {
                padding: 0.45rem 0.9rem;
                font-size: 0.8rem;
            }
        }
        
        @media (max-width: 480px) {
            .booking-container {
                margin: 140px 0.5rem 1.5rem;
                padding: 1rem;
            }
            
            .booking-header h1 {
                font-size: 1.8rem;
            }
            
            .nav-container {
                padding: 0 0.5rem;
            }
            
            .nav-menu {
                gap: 0.2rem;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .nav-menu li {
                margin: 0.1rem;
            }
            
            .nav-menu a {
                padding: 0.4rem 0.8rem;
                font-size: 0.75rem;
                white-space: nowrap;
                min-width: fit-content;
            }
            
            .submit-btn {
                padding: 8px 16px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header y Navegación -->
    <header class="header">
        <nav class="nav-container">
            <ul class="nav-menu">
                <li><a href="index_public.php#inicio">Inicio</a></li>
                <li><a href="index_public.php#sobre-mi">Sobre Mí</a></li>
                <li><a href="index_public.php#especialidades">Especialidades</a></li>
                <li><a href="index_public.php#servicios">Servicios</a></li>
                <li><a href="index_public.php#contacto">Contacto</a></li>
                <li><a href="admin/" style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px;">Admin</a></li>
            </ul>
        </nav>
    </header>

    <div class="booking-container">
        <div class="booking-header">
            <h1>📅 Reservar Cita Online</h1>
            <p>Reserva tu cita con Jorge Hernández Villena de forma rápida y segura</p>
        </div>

        <div id="success-message" class="success-message" style="display: none;"></div>
        <div id="error-message" class="error-message" style="display: none;"></div>

        <form id="booking-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="nombre_cliente">Nombre Completo *</label>
                    <input type="text" id="nombre_cliente" name="nombre_cliente" required>
                </div>
                <div class="form-group">
                    <label for="telefono_cliente">Teléfono *</label>
                    <input type="tel" id="telefono_cliente" name="telefono_cliente" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email_cliente">Email *</label>
                <input type="email" id="email_cliente" name="email_cliente" required>
            </div>

            <div class="form-group">
                <label for="motivo_consulta">Motivo de la Consulta *</label>
                <textarea id="motivo_consulta" name="motivo_consulta" rows="3" required 
                          placeholder="Describe brevemente tu problema o motivo de consulta"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="fecha_cita">Fecha Preferida *</label>
                    <input type="date" id="fecha_cita" name="fecha_cita" required>
                </div>
                <div class="form-group">
                    <label for="hora_cita">Hora Preferida</label>
                    <select id="hora_cita" name="hora_cita" required>
                        <option value="">Selecciona una hora</option>
                        <!-- Las horas disponibles se cargarán dinámicamente -->
                    </select>
                </div>
            </div>

            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Buscando horarios disponibles...</p>
            </div>

            <button type="submit" class="submit-btn" id="submit-btn">
                📅 Confirmar Reserva
            </button>
        </form>

        <div class="back-link">
            <a href="index_public.php">← Volver a la Página Principal</a>
        </div>
    </div>

    <script>
        // Configurar fecha mínima (hoy)
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('fecha_cita').min = today;

        // Cargar horas disponibles cuando se selecciona una fecha
        document.getElementById('fecha_cita').addEventListener('change', function() {
            const selectedDate = this.value;
            if (selectedDate) {
                loadAvailableSlots(selectedDate);
            }
        });

        // Función para cargar horarios disponibles
        async function loadAvailableSlots(date) {
            const loading = document.getElementById('loading');
            const horaSelect = document.getElementById('hora_cita');
            
            loading.classList.add('show');
            horaSelect.innerHTML = '<option value="">Cargando horarios...</option>';

            try {
                const response = await fetch(`api/clients.php?action=search_available_slots&start_datetime=${date}T00:00:00&end_datetime=${date}T23:59:59&duration=30`);
                const data = await response.json();

                if (response.ok && data.filtered_available_slots && data.filtered_available_slots.length > 0) {
                    horaSelect.innerHTML = '<option value="">Selecciona una hora</option>';
                    
                    data.filtered_available_slots.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot;
                        option.textContent = slot;
                        horaSelect.appendChild(option);
                    });
                } else {
                    horaSelect.innerHTML = '<option value="">No hay horarios disponibles en esta fecha</option>';
                }
            } catch (error) {
                console.error('Error cargando horarios:', error);
                horaSelect.innerHTML = '<option value="">Error cargando horarios</option>';
            } finally {
                loading.classList.remove('show');
            }
        }

        // Manejar envío del formulario
        document.getElementById('booking-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submit-btn');
            const originalText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = '🔄 Procesando...';

            const formData = new FormData(this);
            const bookingData = {
                nombre_cliente: formData.get('nombre_cliente'),
                telefono_cliente: formData.get('telefono_cliente'),
                email_cliente: formData.get('email_cliente'),
                motivo_consulta: formData.get('motivo_consulta'),
                fecha_cita: formData.get('hora_cita'), // Usar la hora seleccionada
                tipo_tratamiento: formData.get('motivo_consulta'),
                status: 'pendiente'
            };

            try {
                const response = await fetch('api/clients.php?action=submit_booking', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(bookingData)
                });

                const result = await response.json();

                if (response.ok) {
                    showMessage('success', '✅ ¡Cita reservada con éxito! Recibirás una confirmación por email.');
                    this.reset();
                    document.getElementById('hora_cita').innerHTML = '<option value="">Selecciona una hora</option>';
                } else {
                    showMessage('error', '❌ Error: ' + (result.message || 'No se pudo procesar la reserva'));
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('error', '❌ Error de conexión. Por favor, inténtalo de nuevo.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });

        // Función para mostrar mensajes
        function showMessage(type, message) {
            const successDiv = document.getElementById('success-message');
            const errorDiv = document.getElementById('error-message');

            if (type === 'success') {
                successDiv.textContent = message;
                successDiv.style.display = 'block';
                errorDiv.style.display = 'none';
            } else {
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
                successDiv.style.display = 'none';
            }

            // Auto-ocultar mensajes después de 5 segundos
            setTimeout(() => {
                successDiv.style.display = 'none';
                errorDiv.style.display = 'none';
            }, 5000);
        }

        // Validación de teléfono
        document.getElementById('telefono_cliente').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9+\-\s]/g, '');
        });

        // Validación de nombre (solo letras y espacios)
        document.getElementById('nombre_cliente').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        });
    </script>
</body>
</html>