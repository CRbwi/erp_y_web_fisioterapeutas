<?php
/**
 * API para el Historial de Tratamientos
 * Maneja todas las operaciones CRUD del historial médico
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db_connection.php';

if ($conn->connect_error) {
    die(json_encode(['message' => 'Database connection failed', 'error' => $conn->connect_error]));
}

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'get_historial':
                    handle_get_historial($conn);
                    break;
                case 'get_tipos_tratamiento':
                    handle_get_tipos_tratamiento($conn);
                    break;
                case 'get_escalas_evaluacion':
                    handle_get_escalas_evaluacion($conn);
                    break;
                case 'get_historial_cliente':
                    handle_get_historial_cliente($conn);
                    break;
                case 'get_estadisticas_cliente':
                    handle_get_estadisticas_cliente($conn);
                    break;
                default:
                    http_response_code(400);
                    echo json_encode(['message' => 'Invalid GET action']);
            }
        } else {
            handle_get_historial($conn);
        }
        break;
        
    case 'POST':
        if (isset($_GET['action']) && $_GET['action'] === 'add_sesion') {
            handle_add_sesion($conn);
        } else {
            handle_add_sesion($conn);
        }
        break;
        
    case 'PUT':
        if (isset($_GET['action']) && $_GET['action'] === 'update_sesion') {
            handle_update_sesion($conn);
        } else {
            handle_update_sesion($conn);
        }
        break;
        
    case 'DELETE':
        if (isset($_GET['action']) && $_GET['action'] === 'delete_sesion') {
            handle_delete_sesion($conn);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid DELETE action']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
        break;
}

/**
 * Obtiene el historial completo de tratamientos
 */
function handle_get_historial($conn) {
    $sql = "SELECT ht.*, c.nombre as cliente_nombre, c.apellidos as cliente_apellidos, 
                   ct.fecha_cita, ct.tipo_tratamiento as cita_tipo
            FROM historial_tratamientos ht
            LEFT JOIN clientes c ON ht.client_id = c.id
            LEFT JOIN citas_tratamientos ct ON ht.cita_id = ct.id
            ORDER BY ht.fecha_sesion DESC";
    
    $result = $conn->query($sql);
    
    if ($result) {
        $historial = [];
        while ($row = $result->fetch_assoc()) {
            $historial[] = $row;
        }
        echo json_encode($historial);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error obteniendo historial', 'error' => $conn->error]);
    }
}

/**
 * Obtiene el historial de un cliente específico
 */
function handle_get_historial_cliente($conn) {
    if (!isset($_GET['client_id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Client ID required']);
        return;
    }
    
    $client_id = intval($_GET['client_id']);
    
    $sql = "SELECT ht.*, c.nombre as cliente_nombre, c.apellidos as cliente_apellidos,
                   ct.fecha_cita, ct.tipo_tratamiento as cita_tipo
            FROM historial_tratamientos ht
            LEFT JOIN clientes c ON ht.client_id = c.id
            LEFT JOIN citas_tratamientos ct ON ht.cita_id = ct.id
            WHERE ht.client_id = ?
            ORDER BY ht.fecha_sesion DESC";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
        return;
    }
    
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result) {
        $historial = [];
        while ($row = $result->fetch_assoc()) {
            $historial[] = $row;
        }
        echo json_encode($historial);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error obteniendo historial del cliente', 'error' => $stmt->error]);
    }
    
    $stmt->close();
}

/**
 * Obtiene los tipos de tratamiento disponibles
 */
function handle_get_tipos_tratamiento($conn) {
    $sql = "SELECT * FROM tipos_tratamiento WHERE activo = 1 ORDER BY categoria, nombre";
    $result = $conn->query($sql);
    
    if ($result) {
        $tipos = [];
        while ($row = $result->fetch_assoc()) {
            $tipos[] = $row;
        }
        echo json_encode($tipos);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error obteniendo tipos de tratamiento', 'error' => $conn->error]);
    }
}

/**
 * Obtiene las escalas de evaluación disponibles
 */
function handle_get_escalas_evaluacion($conn) {
    $sql = "SELECT * FROM escalas_evaluacion WHERE activo = 1 ORDER BY nombre";
    $result = $conn->query($sql);
    
    if ($result) {
        $escalas = [];
        while ($row = $result->fetch_assoc()) {
            $escalas[] = $row;
        }
        echo json_encode($escalas);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error obteniendo escalas de evaluación', 'error' => $conn->error]);
    }
}

/**
 * Obtiene estadísticas del cliente
 */
function handle_get_estadisticas_cliente($conn) {
    if (!isset($_GET['client_id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Client ID required']);
        return;
    }
    
    $client_id = intval($_GET['client_id']);
    
    // Total de sesiones
    $sql_total = "SELECT COUNT(*) as total_sesiones FROM historial_tratamientos WHERE client_id = ?";
    $stmt_total = $conn->prepare($sql_total);
    $stmt_total->bind_param("i", $client_id);
    $stmt_total->execute();
    $result_total = $stmt_total->get_result();
    $total_sesiones = $result_total->fetch_assoc()['total_sesiones'];
    
    // Primera y última sesión
    $sql_fechas = "SELECT MIN(fecha_sesion) as primera_sesion, MAX(fecha_sesion) as ultima_sesion 
                   FROM historial_tratamientos WHERE client_id = ?";
    $stmt_fechas = $conn->prepare($sql_fechas);
    $stmt_fechas->bind_param("i", $client_id);
    $stmt_fechas->execute();
    $result_fechas = $stmt_fechas->get_result();
    $fechas = $result_fechas->fetch_assoc();
    
    // Tratamientos más frecuentes
    $sql_frecuentes = "SELECT tipo_tratamiento, COUNT(*) as frecuencia 
                       FROM historial_tratamientos 
                       WHERE client_id = ? 
                       GROUP BY tipo_tratamiento 
                       ORDER BY frecuencia DESC 
                       LIMIT 5";
    $stmt_frecuentes = $conn->prepare($sql_frecuentes);
    $stmt_frecuentes->bind_param("i", $client_id);
    $stmt_frecuentes->execute();
    $result_frecuentes = $stmt_frecuentes->get_result();
    
    $tratamientos_frecuentes = [];
    while ($row = $result_frecuentes->fetch_assoc()) {
        $tratamientos_frecuentes[] = $row;
    }
    
    // Evolución del dolor (últimas 5 sesiones)
    $sql_evolucion = "SELECT fecha_sesion, dolor_antes, dolor_despues 
                      FROM historial_tratamientos 
                      WHERE client_id = ? AND dolor_antes IS NOT NULL 
                      ORDER BY fecha_sesion DESC 
                      LIMIT 5";
    $stmt_evolucion = $conn->prepare($sql_evolucion);
    $stmt_evolucion->bind_param("i", $client_id);
    $stmt_evolucion->execute();
    $result_evolucion = $stmt_evolucion->get_result();
    
    $evolucion_dolor = [];
    while ($row = $result_evolucion->fetch_assoc()) {
        $evolucion_dolor[] = $row;
    }
    
    $estadisticas = [
        'total_sesiones' => $total_sesiones,
        'primera_sesion' => $fechas['primera_sesion'],
        'ultima_sesion' => $fechas['ultima_sesion'],
        'tratamientos_frecuentes' => $tratamientos_frecuentes,
        'evolucion_dolor' => $evolucion_dolor
    ];
    
    echo json_encode($estadisticas);
    
    $stmt_total->close();
    $stmt_fechas->close();
    $stmt_frecuentes->close();
    $stmt_evolucion->close();
}

/**
 * Añade una nueva sesión de tratamiento
 */
function handle_add_sesion($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid JSON received', 'error' => json_last_error_msg()]);
        return;
    }
    
    // Validar campos requeridos
    $required_fields = ['client_id', 'fecha_sesion', 'tipo_tratamiento'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            http_response_code(400);
            echo json_encode(['message' => "Missing required field: $field"]);
            return;
        }
    }
    
    $sql = "INSERT INTO historial_tratamientos (
                client_id, cita_id, fecha_sesion, tipo_tratamiento, descripcion_tratamiento,
                observaciones_evolucion, sintomas_iniciales, sintomas_actuales,
                objetivos_tratamiento, tecnicas_aplicadas, duracion_sesion, intensidad,
                dolor_antes, dolor_despues, movilidad_antes, movilidad_despues,
                recomendaciones, ejercicios_prescritos, proxima_sesion, notas_internas
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
        return;
    }
    
    // Preparar valores con valores por defecto para campos opcionales
    $cita_id = $data['cita_id'] ?? null;
    $descripcion = $data['descripcion_tratamiento'] ?? '';
    $observaciones = $data['observaciones_evolucion'] ?? '';
    $sintomas_iniciales = $data['sintomas_iniciales'] ?? '';
    $sintomas_actuales = $data['sintomas_actuales'] ?? '';
    $objetivos = $data['objetivos_tratamiento'] ?? '';
    $tecnicas = $data['tecnicas_aplicadas'] ?? '';
    $duracion = $data['duracion_sesion'] ?? null;
    $intensidad = $data['intensidad'] ?? 'moderada';
    $dolor_antes = $data['dolor_antes'] ?? null;
    $dolor_despues = $data['dolor_despues'] ?? null;
    $movilidad_antes = $data['movilidad_antes'] ?? '';
    $movilidad_despues = $data['movilidad_despues'] ?? '';
    $recomendaciones = $data['recomendaciones'] ?? '';
    $ejercicios = $data['ejercicios_prescritos'] ?? '';
    $proxima_sesion = $data['proxima_sesion'] ?? null;
    $notas = $data['notas_internas'] ?? '';
    
    $stmt->bind_param("iisssssssissssssssss", 
        $data['client_id'], $cita_id, $data['fecha_sesion'], $data['tipo_tratamiento'],
        $descripcion, $observaciones, $sintomas_iniciales, $sintomas_actuales,
        $objetivos, $tecnicas, $duracion, $intensidad, $dolor_antes, $dolor_despues,
        $movilidad_antes, $movilidad_despues, $recomendaciones, $ejercicios,
        $proxima_sesion, $notas
    );
    
    if ($stmt->execute()) {
        $sesion_id = $conn->insert_id;
        
        // Obtener la sesión creada para devolverla
        $sql_get = "SELECT ht.*, c.nombre as cliente_nombre, c.apellidos as cliente_apellidos
                    FROM historial_tratamientos ht
                    LEFT JOIN clientes c ON ht.client_id = c.id
                    WHERE ht.id = ?";
        
        $stmt_get = $conn->prepare($sql_get);
        $stmt_get->bind_param("i", $sesion_id);
        $stmt_get->execute();
        $result = $stmt_get->get_result();
        $sesion_creada = $result->fetch_assoc();
        
        http_response_code(201);
        echo json_encode([
            'message' => 'Sesión de tratamiento registrada exitosamente',
            'sesion_id' => $sesion_id,
            'sesion' => $sesion_creada
        ]);
        
        $stmt_get->close();
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error registrando sesión', 'error' => $stmt->error]);
    }
    
    $stmt->close();
}

/**
 * Actualiza una sesión de tratamiento existente
 */
function handle_update_sesion($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid JSON received', 'error' => json_last_error_msg()]);
        return;
    }
    
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Session ID required']);
        return;
    }
    
    $sql = "UPDATE historial_tratamientos SET 
                cita_id = ?, fecha_sesion = ?, tipo_tratamiento = ?, descripcion_tratamiento = ?,
                observaciones_evolucion = ?, sintomas_iniciales = ?, sintomas_actuales = ?,
                objetivos_tratamiento = ?, tecnicas_aplicadas = ?, duracion_sesion = ?, intensidad = ?,
                dolor_antes = ?, dolor_despues = ?, movilidad_antes = ?, movilidad_despues = ?,
                recomendaciones = ?, ejercicios_prescritos = ?, proxima_sesion = ?, notas_internas = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
        return;
    }
    
    // Preparar valores
    $cita_id = $data['cita_id'] ?? null;
    $descripcion = $data['descripcion_tratamiento'] ?? '';
    $observaciones = $data['observaciones_evolucion'] ?? '';
    $sintomas_iniciales = $data['sintomas_iniciales'] ?? '';
    $sintomas_actuales = $data['sintomas_actuales'] ?? '';
    $objetivos = $data['objetivos_tratamiento'] ?? '';
    $tecnicas = $data['tecnicas_aplicadas'] ?? '';
    $duracion = $data['duracion_sesion'] ?? null;
    $intensidad = $data['intensidad'] ?? 'moderada';
    $dolor_antes = $data['dolor_antes'] ?? null;
    $dolor_despues = $data['dolor_despues'] ?? null;
    $movilidad_antes = $data['movilidad_antes'] ?? '';
    $movilidad_despues = $data['movilidad_despues'] ?? '';
    $recomendaciones = $data['recomendaciones'] ?? '';
    $ejercicios = $data['ejercicios_prescritos'] ?? '';
    $proxima_sesion = $data['proxima_sesion'] ?? null;
    $notas = $data['notas_internas'] ?? '';
    
    $stmt->bind_param("isssssssssissssssssi", 
        $cita_id, $data['fecha_sesion'], $data['tipo_tratamiento'], $descripcion,
        $observaciones, $sintomas_iniciales, $sintomas_actuales, $objetivos,
        $tecnicas, $duracion, $intensidad, $dolor_antes, $dolor_despues,
        $movilidad_antes, $movilidad_despues, $recomendaciones, $ejercicios,
        $proxima_sesion, $notas, $data['id']
    );
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['message' => 'Sesión de tratamiento actualizada exitosamente']);
        } else {
            echo json_encode(['message' => 'No se encontraron cambios para aplicar']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error actualizando sesión', 'error' => $stmt->error]);
    }
    
    $stmt->close();
}

/**
 * Elimina una sesión de tratamiento
 */
function handle_delete_sesion($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid JSON received', 'error' => json_last_error_msg()]);
        return;
    }
    
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Session ID required']);
        return;
    }
    
    $sql = "DELETE FROM historial_tratamientos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
        return;
    }
    
    $stmt->bind_param("i", $data['id']);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['message' => 'Sesión de tratamiento eliminada exitosamente']);
        } else {
            echo json_encode(['message' => 'No se encontró la sesión para eliminar']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error eliminando sesión', 'error' => $stmt->error]);
    }
    
    $stmt->close();
}

$conn->close();
?>
