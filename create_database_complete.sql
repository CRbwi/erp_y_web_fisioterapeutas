-- =====================================================
-- SCRIPT COMPLETO PARA CREAR BASE DE DATOS DE FISIOTERAPIA
-- =====================================================
-- Este script crea toda la estructura de la base de datos
-- y la pobla con datos de ejemplo
-- =====================================================

-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS jorge_fisioterapia 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE jorge_fisioterapia;

-- =====================================================
-- TABLA: usuarios_admin (Usuarios administrativos)
-- =====================================================
CREATE TABLE IF NOT EXISTS usuarios_admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    rol ENUM('admin', 'terapeuta', 'recepcionista') DEFAULT 'admin',
    activo BOOLEAN DEFAULT TRUE,
    ultimo_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =====================================================
-- TABLA: clientes (Información de clientes)
-- =====================================================
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE,
    direccion TEXT,
    historial_medico TEXT,
    alergias TEXT,
    medicamentos_actuales TEXT,
    observaciones_generales TEXT,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices para mejorar consultas
    INDEX idx_nombre_apellidos (nombre, apellidos),
    INDEX idx_telefono (telefono),
    INDEX idx_email (email),
    INDEX idx_activo (activo)
);

-- =====================================================
-- TABLA: disponibilidad (Horarios disponibles y bloqueados)
-- =====================================================
CREATE TABLE IF NOT EXISTS disponibilidad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dia_semana INT NULL, -- 0=Sunday, 1=Monday, ..., 6=Saturday. NULL for specific dates.
    fecha DATE NULL,    -- Specific date for blocks/vacations. NULL for recurring availability.
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    tipo ENUM('disponible', 'bloqueado') NOT NULL,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Constraint para asegurar que solo uno de los dos campos esté lleno
    CONSTRAINT chk_date_or_day CHECK ( 
        (dia_semana IS NOT NULL AND fecha IS NULL) OR 
        (dia_semana IS NULL AND fecha IS NOT NULL) 
    ),
    
    -- Índices
    INDEX idx_dia_semana (dia_semana),
    INDEX idx_fecha (fecha),
    INDEX idx_tipo (tipo)
);

-- =====================================================
-- TABLA: tipos_tratamiento (Tipos de tratamientos disponibles)
-- =====================================================
CREATE TABLE IF NOT EXISTS tipos_tratamiento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL UNIQUE,
    descripcion TEXT,
    categoria ENUM('masaje', 'terapia_manual', 'ejercicio', 'electroterapia', 'hidroterapia', 'otro') NOT NULL,
    duracion_estimada INT, -- En minutos
    precio DECIMAL(10,2),
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Índices
    INDEX idx_categoria (categoria),
    INDEX idx_activo (activo)
);

-- =====================================================
-- TABLA: citas_tratamientos (Citas y tratamientos)
-- =====================================================
CREATE TABLE IF NOT EXISTS citas_tratamientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    fecha_cita DATETIME NOT NULL,
    tipo_tratamiento VARCHAR(255) NOT NULL,
    observaciones TEXT,
    costo DECIMAL(10,2),
    status ENUM('pendiente', 'confirmada', 'completada', 'cancelada', 'no_show') DEFAULT 'pendiente',
    notas_internas TEXT,
    duracion_real INT, -- En minutos
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices para mejorar consultas
    INDEX idx_client_id (client_id),
    INDEX idx_fecha_cita (fecha_cita),
    INDEX idx_status (status),
    INDEX idx_tipo_tratamiento (tipo_tratamiento),
    
    -- Claves foráneas
    FOREIGN KEY (client_id) REFERENCES clientes(id) ON DELETE CASCADE
);

-- =====================================================
-- TABLA: historial_tratamientos (Historial médico de tratamientos)
-- =====================================================
CREATE TABLE IF NOT EXISTS historial_tratamientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    cita_id INT NULL, -- Referencia a la cita si existe
    fecha_sesion DATETIME NOT NULL,
    tipo_tratamiento VARCHAR(255) NOT NULL,
    descripcion_tratamiento TEXT,
    observaciones_evolucion TEXT,
    sintomas_iniciales TEXT,
    sintomas_actuales TEXT,
    objetivos_tratamiento TEXT,
    tecnicas_aplicadas TEXT,
    duracion_sesion INT, -- En minutos
    intensidad ENUM('leve', 'moderada', 'intensa') DEFAULT 'moderada',
    dolor_antes INT CHECK (dolor_antes >= 0 AND dolor_antes <= 10), -- Escala 0-10
    dolor_despues INT CHECK (dolor_despues >= 0 AND dolor_despues <= 10), -- Escala 0-10
    movilidad_antes TEXT,
    movilidad_despues TEXT,
    recomendaciones TEXT,
    ejercicios_prescritos TEXT,
    proxima_sesion DATE,
    notas_internas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices para mejorar consultas
    INDEX idx_client_id (client_id),
    INDEX idx_fecha_sesion (fecha_sesion),
    INDEX idx_cita_id (cita_id),
    INDEX idx_tipo_tratamiento (tipo_tratamiento),
    
    -- Claves foráneas
    FOREIGN KEY (client_id) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (cita_id) REFERENCES citas_tratamientos(id) ON DELETE SET NULL
);

-- =====================================================
-- TABLA: escalas_evaluacion (Escalas para evaluaciones)
-- =====================================================
CREATE TABLE IF NOT EXISTS escalas_evaluacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    rango_min INT NOT NULL,
    rango_max INT NOT NULL,
    unidad VARCHAR(50),
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- TABLA: documentos (Documentos de clientes)
-- =====================================================
CREATE TABLE IF NOT EXISTS documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    tipo_documento VARCHAR(50),
    descripcion TEXT,
    ruta_archivo VARCHAR(500) NOT NULL,
    tamano_archivo INT, -- En bytes
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Índices
    INDEX idx_client_id (client_id),
    INDEX idx_tipo_documento (tipo_documento),
    INDEX idx_fecha_subida (fecha_subida),
    
    -- Claves foráneas
    FOREIGN KEY (client_id) REFERENCES clientes(id) ON DELETE CASCADE
);

-- =====================================================
-- INSERTAR DATOS DE EJEMPLO
-- =====================================================

-- Insertar usuario administrador
INSERT INTO usuarios_admin (username, password_hash, nombre, email, rol) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jorge Hernández Villena', 'jorge@fisioterapia.com', 'admin'),
('terapeuta1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'María García López', 'maria@fisioterapia.com', 'terapeuta');

-- Insertar clientes de ejemplo
INSERT INTO clientes (nombre, apellidos, telefono, email, fecha_nacimiento, direccion, historial_medico, alergias) VALUES
('Juan', 'Pérez García', '600123456', 'juan.perez@email.com', '1985-03-15', 'Calle Mayor 123, Granada', 'Dolor lumbar crónico desde 2020. Hernia discal L4-L5 diagnosticada en 2021.', 'Ninguna conocida'),
('María', 'López Fernández', '600234567', 'maria.lopez@email.com', '1990-07-22', 'Avenida de la Constitución 45, Granada', 'Esguince de tobillo derecho en 2022. Recuperación completa.', 'Penicilina'),
('Carlos', 'Martínez Ruiz', '600345678', 'carlos.martinez@email.com', '1978-11-08', 'Plaza Nueva 7, Granada', 'Dolor de hombro derecho por tendinitis. Tratamiento previo en 2023.', 'Ninguna conocida'),
('Ana', 'González Moreno', '600456789', 'ana.gonzalez@email.com', '1992-04-30', 'Calle San Jerónimo 89, Granada', 'Dolor cervical por mala postura en el trabajo. Inicio en 2024.', 'Ninguna conocida'),
('Luis', 'Rodríguez Jiménez', '600567890', 'luis.rodriguez@email.com', '1983-09-12', 'Calle Recogidas 156, Granada', 'Fractura de radio izquierdo en 2023. Recuperación en progreso.', 'Ninguna conocida');

-- Insertar tipos de tratamiento
INSERT INTO tipos_tratamiento (nombre, descripcion, categoria, duracion_estimada, precio) VALUES
('Masaje Terapéutico', 'Masaje para aliviar tensión muscular y dolor', 'masaje', 45, 35.00),
('Masaje Deportivo', 'Masaje específico para deportistas y recuperación', 'masaje', 60, 45.00),
('Terapia Manual', 'Técnicas manuales para movilización articular', 'terapia_manual', 30, 30.00),
('Movilización Neural', 'Técnicas para liberar tensiones del sistema nervioso', 'terapia_manual', 45, 40.00),
('Ejercicios de Rehabilitación', 'Programa de ejercicios personalizado', 'ejercicio', 60, 50.00),
('Electroterapia', 'Uso de corrientes eléctricas para tratamiento', 'electroterapia', 30, 25.00),
('Ultrasonido', 'Terapia con ondas ultrasónicas', 'electroterapia', 15, 20.00),
('Crioterapia', 'Terapia con frío para inflamación', 'hidroterapia', 20, 15.00),
('Termoterapia', 'Terapia con calor para relajación muscular', 'hidroterapia', 25, 18.00),
('Punción Seca', 'Técnica para puntos gatillo', 'terapia_manual', 30, 35.00),
('Vendaje Funcional', 'Aplicación de vendas para estabilización', 'terapia_manual', 20, 20.00),
('Drenaje Linfático', 'Técnica para mejorar circulación linfática', 'masaje', 45, 40.00);

-- Insertar escalas de evaluación
INSERT INTO escalas_evaluacion (nombre, descripcion, rango_min, rango_max, unidad) VALUES
('Escala de Dolor', 'Escala visual analógica del dolor', 0, 10, '0-10'),
('Escala de Movilidad', 'Evaluación de rango de movimiento', 0, 100, '%'),
('Escala de Fuerza', 'Evaluación de fuerza muscular', 0, 5, '0-5'),
('Escala de Funcionalidad', 'Evaluación de capacidad funcional', 0, 100, '%');

-- Insertar disponibilidad semanal (lunes a viernes, 9:00-18:00)
INSERT INTO disponibilidad (dia_semana, hora_inicio, hora_fin, tipo, descripcion) VALUES
(1, '09:00:00', '18:00:00', 'disponible', 'Horario laboral lunes'),
(2, '09:00:00', '18:00:00', 'disponible', 'Horario laboral martes'),
(3, '09:00:00', '18:00:00', 'disponible', 'Horario laboral miércoles'),
(4, '09:00:00', '18:00:00', 'disponible', 'Horario laboral jueves'),
(5, '09:00:00', '18:00:00', 'disponible', 'Horario laboral viernes');

-- Insertar pausa para comida (12:00-13:00) todos los días laborables
INSERT INTO disponibilidad (dia_semana, hora_inicio, hora_fin, tipo, descripcion) VALUES
(1, '12:00:00', '13:00:00', 'bloqueado', 'Pausa para comida'),
(2, '12:00:00', '13:00:00', 'bloqueado', 'Pausa para comida'),
(3, '12:00:00', '13:00:00', 'bloqueado', 'Pausa para comida'),
(4, '12:00:00', '13:00:00', 'bloqueado', 'Pausa para comida'),
(5, '12:00:00', '13:00:00', 'bloqueado', 'Pausa para comida');

-- Insertar citas de ejemplo
INSERT INTO citas_tratamientos (client_id, fecha_cita, tipo_tratamiento, observaciones, costo, status) VALUES
(1, '2024-12-20 10:00:00', 'Masaje Terapéutico', 'Dolor lumbar agudo. Aplicar técnicas de relajación muscular.', 35.00, 'confirmada'),
(2, '2024-12-20 11:00:00', 'Terapia Manual', 'Movilización de tobillo derecho. Ejercicios de fortalecimiento.', 30.00, 'confirmada'),
(3, '2024-12-20 14:00:00', 'Ejercicios de Rehabilitación', 'Programa de rehabilitación para hombro derecho.', 50.00, 'pendiente'),
(4, '2024-12-21 09:00:00', 'Masaje Deportivo', 'Recuperación post-entrenamiento. Enfoque en piernas.', 45.00, 'confirmada'),
(5, '2024-12-21 10:30:00', 'Electroterapia', 'Tratamiento para dolor de muñeca. Aplicar TENS.', 25.00, 'pendiente');

-- Insertar historial de tratamientos de ejemplo
INSERT INTO historial_tratamientos (client_id, cita_id, fecha_sesion, tipo_tratamiento, descripcion_tratamiento, sintomas_iniciales, sintomas_actuales, objetivos_tratamiento, tecnicas_aplicadas, duracion_sesion, intensidad, dolor_antes, dolor_despues, recomendaciones) VALUES
(1, 1, '2024-12-15 10:00:00', 'Masaje Terapéutico', 'Primera sesión para dolor lumbar', 'Dolor intenso en zona lumbar, dificultad para moverse', 'Dolor moderado, mejor movilidad', 'Reducir dolor y mejorar movilidad', 'Masaje profundo, estiramientos suaves', 45, 'moderada', 8, 5, 'Aplicar calor local, evitar esfuerzos'),
(2, 2, '2024-12-16 11:00:00', 'Terapia Manual', 'Rehabilitación de tobillo', 'Inestabilidad en tobillo derecho', 'Mejor estabilidad, menos dolor', 'Recuperar estabilidad completa', 'Movilizaciones, ejercicios de equilibrio', 30, 'leve', 4, 2, 'Continuar ejercicios de equilibrio en casa'),
(3, 3, '2024-12-17 14:00:00', 'Ejercicios de Rehabilitación', 'Programa de hombro', 'Dolor al levantar el brazo', 'Mejor rango de movimiento', 'Recuperar función completa del hombro', 'Ejercicios de fortalecimiento progresivo', 60, 'moderada', 6, 3, 'Realizar ejercicios 3 veces al día');

-- Insertar documentos de ejemplo
INSERT INTO documentos (client_id, nombre_archivo, tipo_documento, descripcion, ruta_archivo) VALUES
(1, 'radiografia_lumbar.pdf', 'radiografia', 'Radiografía de columna lumbar', '/uploads/radiografia_lumbar.pdf'),
(1, 'analisis_sangre.pdf', 'analisis', 'Análisis de sangre completo', '/uploads/analisis_sangre.pdf'),
(2, 'ecografia_tobillo.pdf', 'ecografia', 'Ecografía de tobillo derecho', '/uploads/ecografia_tobillo.pdf'),
(3, 'resonancia_hombro.pdf', 'resonancia', 'Resonancia magnética de hombro', '/uploads/resonancia_hombro.pdf');

-- =====================================================
-- VERIFICAR LA CREACIÓN DE TABLAS
-- =====================================================
SELECT 'Base de datos creada exitosamente' as mensaje;

-- Mostrar todas las tablas creadas
SHOW TABLES;

-- Contar registros en cada tabla
SELECT 'clientes' as tabla, COUNT(*) as total FROM clientes
UNION ALL
SELECT 'citas_tratamientos', COUNT(*) FROM citas_tratamientos
UNION ALL
SELECT 'disponibilidad', COUNT(*) FROM disponibilidad
UNION ALL
SELECT 'historial_tratamientos', COUNT(*) FROM historial_tratamientos
UNION ALL
SELECT 'tipos_tratamiento', COUNT(*) FROM tipos_tratamiento
UNION ALL
SELECT 'escalas_evaluacion', COUNT(*) FROM escalas_evaluacion
UNION ALL
SELECT 'documentos', COUNT(*) FROM documentos
UNION ALL
SELECT 'usuarios_admin', COUNT(*) FROM usuarios_admin;

