<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jorge Hernández Villena - Fisioterapeuta en Granada | Centro de Fisioterapia</title>
    <meta name="description" content="Fisioterapeuta especializado en terapia manual con más de 10 años de experiencia. Centro de Fisioterapia Jorge Hernández en Granada. Cita previa online.">
    <meta name="keywords" content="fisioterapeuta, granada, terapia manual, rehabilitación, dolor de espalda, cervicalgia, lesiones deportivas">
    
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
        
        .hero {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                        url('https://pixel-p2.s3.eu-central-1.amazonaws.com/doctor/photos/f718ab46/f718ab46-ee6c-4348-b939-98242c3fee54_large.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            margin-top: 70px;
        }
        
        .hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
        }
        
        .hero-content p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.7);
        }
        

        
        .cta-button {
            background: linear-gradient(135deg, #495057, #343a40);
            color: white;
            padding: 0.9rem 1.8rem;
            border: none;
            border-radius: 30px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            background: linear-gradient(135deg, #343a40, #495057);
        }
        
        .cta-button:hover::before {
            left: 100%;
        }
        
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }
        
        .section {
            margin-bottom: 4rem;
        }
        
        .section h2 {
            font-size: 2.5rem;
            color: #1a1a1a;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }
        
        .about-text h3 {
            font-size: 1.8rem;
            color: #2c2c2c;
            margin-bottom: 1rem;
        }
        
        .about-text p {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: #555;
        }
        
        .about-image {
            text-align: center;
        }
        
        .about-image img {
            width: 300px;
            height: 300px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .specialties {
            background: linear-gradient(135deg, #3a3a3a 0%, #2c2c2c 100%);
            color: white;
            padding: 4rem 2rem;
            margin: 4rem -2rem;
        }
        
        .specialties h2 {
            color: white;
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .specialties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .specialty-card {
            background: rgba(255,255,255,0.1);
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .specialty-card h3 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }
        
        .specialty-card p {
            opacity: 0.9;
        }
        
        .services {
            background: white;
            padding: 4rem 2rem;
            margin: 4rem -2rem;
        }
        
        .services h2 {
            color: #1a1a1a;
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .service-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
        }
        
        .service-card h3 {
            color: #2c2c2c;
            margin-bottom: 1rem;
        }
        
        .service-card p {
            color: #666;
            margin-bottom: 1rem;
        }
        
        .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #d4a574;
        }
        
        .contact-info {
            background: #1a1a1a;
            color: white;
            padding: 4rem 2rem;
            margin: 4rem -2rem;
        }
        
        .contact-info h2 {
            color: white;
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .contact-item {
            text-align: center;
            padding: 2rem;
        }
        
        .contact-item h3 {
            margin-bottom: 1rem;
            color: #d4a574;
        }
        
        .contact-item p {
            font-size: 1.1rem;
        }
        
        .footer {
            background: #1a1a1a;
            color: white;
            text-align: center;
            padding: 2rem;
        }
        
        .admin-link {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #e74c3c;
            color: white;
            padding: 10px 15px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 0.9rem;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }
        
        .admin-link:hover {
            opacity: 1;
        }
        
        @media (max-width: 768px) {
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
            

            
            .hero-content h1 {
                font-size: 2rem;
                margin-bottom: 0.5rem;
            }
            
            .hero-content p {
                font-size: 1rem;
                margin-bottom: 1rem;
            }
            
            .cta-button {
                padding: 0.6rem 1.2rem;
                font-size: 0.9rem;
            }
            
            .about-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .about-image img {
                width: 200px;
                height: 200px;
            }
            
            .specialties-grid,
            .services-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .main-content {
                padding: 2rem 1rem;
            }
            
            .section {
                margin-bottom: 2rem;
            }
            
            .section h2 {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 480px) {
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
            

            
            .hero-content h1 {
                font-size: 1.8rem;
            }
            
            .hero-content p {
                font-size: 0.9rem;
            }
            
            .cta-button {
                padding: 0.5rem 1rem;
                font-size: 0.85rem;
            }
            
            .about-image img {
                width: 180px;
                height: 180px;
            }
            
            .main-content {
                padding: 1.5rem 0.5rem;
            }
            
            .section h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header y Navegación -->
    <header class="header">
        <nav class="nav-container">
            <ul class="nav-menu">
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#sobre-mi">Sobre Mí</a></li>
                <li><a href="#especialidades">Especialidades</a></li>
                <li><a href="#servicios">Servicios</a></li>
                <li><a href="#contacto">Contacto</a></li>
                <li><a href="booking.php" class="cta-button">Reservar Cita</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="inicio">
        <div class="hero-content">
            <h1>Jorge Hernández Villena</h1>
            <p>Fisioterapeuta Especializado en Terapia Manual</p>
            <p>Más de 10 años de experiencia en Granada</p>
            <a href="booking.php" class="cta-button">Reservar Cita Online</a>
        </div>
    </section>

    <!-- Contenido Principal -->
    <main class="main-content">
        <!-- Sobre Mí -->
        <section class="section" id="sobre-mi">
            <h2>Sobre Mí</h2>
            <div class="about-grid">
                <div class="about-text">
                    <h3>Fisioterapeuta Especializado</h3>
                    <p>Soy Jorge Hernández Villena, fisioterapeuta colegiado (Nº 10098) con más de diez años de experiencia en el campo de la fisioterapia.</p>
                    <p>Mi especialidad es la <strong>terapia manual</strong>, trabajando directamente con mis manos para proporcionar tratamientos personalizados y efectivos.</p>
                    <p>He trabajado en diversos centros de prestigio, incluyendo el <strong>Centro de Fisioterapia Jorge Hernández</strong> en Granada desde 2018, así como en centros en Suiza y Melilla.</p>
                    <p>Mi formación incluye estudios en la <strong>Universidad de Granada</strong> y formación adicional en <strong>Croix Rouge (Ginebra)</strong>.</p>
                </div>
                <div class="about-image">
                    <img src="https://pixel-p2.s3.eu-central-1.amazonaws.com/doctor/photos/f718ab46/f718ab46-ee6c-4348-b939-98242c3fee54_large.jpg" 
                         alt="Jorge Hernández Villena - Fisioterapeuta en Granada">
                </div>
            </div>
        </section>

        <!-- Especialidades -->
        <section class="specialties" id="especialidades">
            <h2>Mis Especialidades</h2>
            <div class="specialties-grid">
                <div class="specialty-card">
                    <h3>🏃‍♂️ Fisioterapia Deportiva</h3>
                    <p>Especializado en lesiones deportivas, rehabilitación y readaptación deportiva. Experiencia con equipos profesionales.</p>
                </div>
                <div class="specialty-card">
                    <h3>👴 Fisioterapia Geriátrica</h3>
                    <p>Tratamiento especializado para personas mayores, mejorando la movilidad y calidad de vida.</p>
                </div>
                <div class="specialty-card">
                    <h3>🦴 Fisioterapia Reumatológica</h3>
                    <p>Tratamiento de enfermedades reumáticas, artrosis, artritis y patologías del sistema musculoesquelético.</p>
                </div>
                <div class="specialty-card">
                    <h3>🔄 Readaptación Deportiva</h3>
                    <p>Programas personalizados para volver a la actividad deportiva después de una lesión.</p>
                </div>
            </div>
        </section>

        <!-- Servicios -->
        <section class="services" id="servicios">
            <h2>Servicios y Tratamientos</h2>
            <div class="services-grid">
                <div class="service-card">
                    <h3>🩺 Primera Visita</h3>
                    <p>Evaluación completa del paciente, diagnóstico y plan de tratamiento personalizado.</p>
                    <div class="price">Consultar precio</div>
                </div>
                <div class="service-card">
                    <h3>🔄 Visitas Sucesivas</h3>
                    <p>Sesiones de tratamiento y seguimiento de la evolución del paciente.</p>
                    <div class="price">Consultar precio</div>
                </div>
                <div class="service-card">
                    <h3>⚡ Urgencias</h3>
                    <p>Atención inmediata para lesiones agudas y dolor intenso.</p>
                    <div class="price">Consultar precio</div>
                </div>
                <div class="service-card">
                    <h3>🖐️ Terapia Manual</h3>
                    <p>Técnicas manuales especializadas para movilización articular y tejidos blandos.</p>
                    <div class="price">Consultar precio</div>
                </div>
                <div class="service-card">
                    <h3>💪 Rehabilitación</h3>
                    <p>Programas de ejercicios personalizados para recuperación funcional.</p>
                    <div class="price">Consultar precio</div>
                </div>
                <div class="service-card">
                    <h3>🎯 Tratamiento Especializado</h3>
                    <p>Tratamientos específicos para cada patología y paciente.</p>
                    <div class="price">Consultar precio</div>
                </div>
            </div>
        </section>

        <!-- Enfermedades Tratadas -->
        <section class="section">
            <h2>Enfermedades y Patologías Tratadas</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-top: 2rem;">
                <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h4 style="color: #d4a574; margin-bottom: 1rem;">Sistema Musculoesquelético</h4>
                    <ul style="list-style: none; color: #666;">
                        <li>• Contractura muscular</li>
                        <li>• Cervicalgia</li>
                        <li>• Tendinitis</li>
                        <li>• Esguinces</li>
                        <li>• Dolor de espalda</li>
                        <li>• Artrosis</li>
                    </ul>
                </div>
                <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h4 style="color: #d4a574; margin-bottom: 1rem;">Lesiones Deportivas</h4>
                    <ul style="list-style: none; color: #666;">
                        <li>• Lesiones deportivas</li>
                        <li>• Rotura de ligamentos</li>
                        <li>• Meniscopatía</li>
                        <li>• Condromalacia rotuliana</li>
                        <li>• Epicondilitis</li>
                        <li>• Fascitis plantar</li>
                    </ul>
                </div>
                <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h4 style="color: #d4a574; margin-bottom: 1rem;">Sistema Nervioso</h4>
                    <ul style="list-style: none; color: #666;">
                        <li>• Ciática</li>
                        <li>• Radiculopatía</li>
                        <li>• Puntos gatillo</li>
                        <li>• Neuralgias</li>
                        <li>• Mareo cervical</li>
                        <li>• Vértigo</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Contacto -->
        <section class="contact-info" id="contacto">
            <h2>Información de Contacto</h2>
            <div class="contact-grid">
                <div class="contact-item">
                    <h3>📍 Ubicación</h3>
                    <p><strong>Centro de Fisioterapia Jorge Hernández</strong></p>
                    <p>Calle Verónica de la Magdalena 24</p>
                    <p>Granada, España</p>
                </div>
                <div class="contact-item">
                    <h3>📱 Teléfono</h3>
                    <p><strong>672 72 40 90</strong></p>
                    <p>Horario de atención</p>
                </div>
                <div class="contact-item">
                    <h3>🌐 Reserva Online</h3>
                    <p>Reserva tu cita directamente desde nuestra web</p>
                    <p>Confirmación inmediata y recordatorios</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 Centro de Fisioterapia Jorge Hernández. Todos los derechos reservados.</p>
        <p>Fisioterapeuta Colegiado Nº 10098 | Granada</p>
    </footer>

    <!-- Enlace Administrativo -->
    <a href="admin/" class="admin-link">🔐 Admin</a>

    <!-- Scripts -->
    <script>
        // Smooth scrolling para los enlaces del menú
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navegación fija responsive
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>
