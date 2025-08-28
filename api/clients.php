<?php
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
        if (isset($_GET['action']) && $_GET['action'] === 'get_appointments' && isset($_GET['client_id'])) {
            handle_get_appointments($conn, $_GET['client_id']);
        } else if (isset($_GET['action']) && $_GET['action'] === 'get_documents' && isset($_GET['client_id'])) {
            handle_get_documents($conn, $_GET['client_id']);
        } else if (isset($_GET['action']) && $_GET['action'] === 'get_calendar_appointments') {
            handle_get_calendar_appointments($conn);
        } else if (isset($_GET['action']) && $_GET['action'] === 'get_availability') {
            handle_get_availability($conn);
        } else if (isset($_GET['action']) && $_GET['action'] === 'search_available_slots') {
            handle_search_available_slots($conn);
        } else {
            handle_get_clients($conn);
        }
        break;
    case 'POST':
        if (isset($_GET['action']) && $_GET['action'] === 'add_appointment') {
            handle_post_appointment($conn);
        } else if (isset($_GET['action']) && $_GET['action'] === 'upload_document') {
            handle_upload_document($conn);
        } else if (isset($_GET['action']) && $_GET['action'] === 'add_calendar_appointment') {
            handle_add_calendar_appointment($conn);
        } else if (isset($_GET['action']) && $_GET['action'] === 'add_availability') {
            handle_add_availability($conn);
        } else if (isset($_GET['action']) && $_GET['action'] === 'submit_booking') {
            handle_submit_booking($conn);
        } else {
            handle_post_client($conn);
        }
        break;
    case 'PUT':
        if (isset($_GET['action']) && $_GET['action'] === 'update_appointment') {
            handle_update_appointment($conn);
        } else if (isset($_GET['action']) && $_GET['action'] === 'update_availability') {
            handle_update_availability($conn);
        } else {
            handle_put_client($conn);
        }
        break;
    case 'DELETE':
        if (isset($_GET['action']) && $_GET['action'] === 'delete_appointment') {
            handle_delete_appointment($conn);
        } else if (isset($_GET['action']) && $_GET['action'] === 'delete_availability') {
            handle_delete_availability($conn);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid DELETE action']);
        }
        break;
    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['message' => 'Method not allowed']);
        break;
}

function handle_get_clients($conn) {
    $sql = "SELECT id, nombre, apellidos, telefono, email, historial_medico FROM clientes";
    $params = [];
    $types = "";

    if (isset($_GET['query']) && !empty($_GET['query'])) {
        $search_query = "%" . $_GET['query'] . "%";
        $sql .= " WHERE nombre LIKE ? OR apellidos LIKE ? OR telefono LIKE ? OR email LIKE ?";
        $params = [$search_query, $search_query, $search_query, $search_query];
        $types = "ssss";
    }

    if (!empty($params)) {
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            http_response_code(500);
            echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
            return;
        }
        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(['message' => 'Execute failed: ' . $stmt->error]);
            return;
        }
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($sql);
    }

    if ($result === false) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Query failed', 'error' => $conn->error]);
        return;
    }
    $clients = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $clients[] = $row;
        }
    }
    echo json_encode($clients);
    if (isset($stmt)) {
        $stmt->close();
    }
}

function handle_get_appointments($conn, $client_id) {
    $stmt = $conn->prepare("SELECT id, fecha_cita, tipo_tratamiento, observaciones, costo FROM citas_tratamientos WHERE client_id = ? ORDER BY fecha_cita DESC");
    if ($stmt === false) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
        return;
    }
    $stmt->bind_param("i", $client_id);
    if (!$stmt->execute()) { // Added error checking
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Execute failed', 'error' => $stmt->error]);
        return;
    }
    $result = $stmt->get_result();
    $appointments = [];
    while($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    echo json_encode($appointments);
    $stmt->close();
}

function handle_post_client($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Invalid JSON received', 'error' => json_last_error_msg()]);
        return;
    }

    if (!isset($data['nombre'], $data['apellidos'], $data['telefono'], $data['email'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Missing required fields']);
        return;
    }

    $nombre = $data['nombre'];
    $apellidos = $data['apellidos'];
    $telefono = $data['telefono'];
    $email = $data['email'];

    $stmt = $conn->prepare("INSERT INTO clientes (nombre, apellidos, telefono, email) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("ssss", $nombre, $apellidos, $telefono, $email);

    if ($stmt->execute()) {
        http_response_code(201); // Created
        echo json_encode(['message' => 'Cliente añadido con éxito', 'id' => $conn->insert_id]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Error al añadir el cliente', 'error' => $stmt->error]);
    }

    $stmt->close();
}

function handle_post_appointment($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Invalid JSON received', 'error' => json_last_error_msg()]);
        return;
    }

    if (!isset($data['client_id'], $data['fecha_cita'], $data['tipo_tratamiento'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Missing required fields: client_id, fecha_cita, tipo_tratamiento']);
        return;
    }

    $client_id = $data['client_id'];
    $fecha_cita = $data['fecha_cita'];
    $tipo_tratamiento = $data['tipo_tratamiento'];
    $observaciones = isset($data['observaciones']) ? $data['observaciones'] : null;
    $costo = isset($data['costo']) ? $data['costo'] : null;

    $stmt = $conn->prepare("INSERT INTO citas_tratamientos (client_id, fecha_cita, tipo_tratamiento, observaciones, costo) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("isssd", $client_id, $fecha_cita, $tipo_tratamiento, $observaciones, $costo);

    if ($stmt->execute()) {
        http_response_code(201); // Created
        echo json_encode(['message' => 'Cita/Tratamiento añadido con éxito', 'id' => $conn->insert_id]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Error al añadir cita/tratamiento', 'error' => $stmt->error]);
    }

    $stmt->close();
}

function handle_put_client($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Invalid JSON received', 'error' => json_last_error_msg()]);
        return;
    }

    if (!isset($data['id']) || !isset($data['historial_medico'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Missing required fields: id and historial_medico']);
        return;
    }

    $id = $data['id'];
    $historial_medico = $data['historial_medico'];

    $stmt = $conn->prepare("UPDATE clientes SET historial_medico = ? WHERE id = ?");
    if ($stmt === false) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("si", $historial_medico, $id);

    if ($stmt->execute()) {
        http_response_code(200); // OK
        echo json_encode(['message' => 'Historial médico actualizado con éxito']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Error al actualizar el historial médico', 'error' => $stmt->error]);
    }

    $stmt->close();
}

function handle_get_documents($conn, $client_id) {
    $stmt = $conn->prepare("SELECT id, nombre_archivo, tipo_documento, descripcion, ruta_archivo, fecha_subida FROM documentos WHERE client_id = ? ORDER BY fecha_subida DESC");
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
        return;
    }
    $stmt->bind_param("i", $client_id);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['message' => 'Execute failed', 'error' => $stmt->error]);
        return;
    }
    $result = $stmt->get_result();
    $documents = [];
    while($row = $result->fetch_assoc()) {
        $documents[] = $row;
    }
    echo json_encode($documents);
    $stmt->close();
}

function handle_upload_document($conn) {
    if (!isset($_POST['client_id']) || !isset($_FILES['documento'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing client_id or document']);
        return;
    }

    $client_id = $_POST['client_id'];
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $file = $_FILES['documento'];

    // File upload handling
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($file["name"]);
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file already exists
    if (file_exists($target_file)) {
        http_response_code(409);
        echo json_encode(['message' => 'File already exists.']);
        return;
    }

    // Check file size (e.g., 5MB limit)
    if ($file["size"] > 5000000) {
        http_response_code(413);
        echo json_encode(['message' => 'File is too large.']);
        return;
    }

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        // Insert document info into database
        $stmt = $conn->prepare("INSERT INTO documentos (client_id, nombre_archivo, tipo_documento, descripcion, ruta_archivo) VALUES (?, ?, ?, ?, ?)");
        if ($stmt === false) {
            http_response_code(500);
            echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
            return;
        }

        $stmt->bind_param("issss", $client_id, $file['name'], $file_type, $descripcion, $target_file);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(['message' => 'Document uploaded successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to save document info', 'error' => $stmt->error]);
        }
        $stmt->close();
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to upload file.']);
    }
}

function handle_get_calendar_appointments($conn) {
    $start = isset($_GET['start']) ? $_GET['start'] : null;
    $end = isset($_GET['end']) ? $_GET['end'] : null;

    $sql = "SELECT c.id, c.fecha_cita, c.tipo_tratamiento, c.observaciones, c.costo, c.status, c.notas_internas, cl.nombre, cl.apellidos FROM citas_tratamientos c JOIN clientes cl ON c.client_id = cl.id";
    $params = [];
    $types = "";

    if ($start && $end) {
        $sql .= " WHERE c.fecha_cita BETWEEN ? AND ?";
        $params = [$start, $end];
        $types = "ss";
    }

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
        return;
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['message' => 'Execute failed: ' . $stmt->error]);
        return;
    }

    $result = $stmt->get_result();
    $appointments = [];
    while($row = $result->fetch_assoc()) {
        $appointments[] = [
            'id' => $row['id'],
            'title' => $row['tipo_tratamiento'] . ' - ' . $row['nombre'] . ' ' . $row['apellidos'],
            'start' => $row['fecha_cita'],
            'extendedProps' => [
                'observaciones' => $row['observaciones'],
                'costo' => $row['costo'],
                'status' => $row['status'],
                'notas_internas' => $row['notas_internas']
            ]
        ];
    }
    echo json_encode($appointments);
    $stmt->close();
}

function handle_get_availability($conn) {
    $sql = "SELECT id, dia_semana, fecha, hora_inicio, hora_fin, tipo, descripcion FROM disponibilidad";
    $result = $conn->query($sql);

    if ($result === false) {
        http_response_code(500);
        echo json_encode(['message' => 'Query failed', 'error' => $conn->error]);
        return;
    }

    $availability = [];
    while($row = $result->fetch_assoc()) {
        $event = [
            'id' => $row['id'],
            'startTime' => $row['hora_inicio'],
            'endTime' => $row['hora_fin'],
            'display' => 'background',
            'color' => ($row['tipo'] === 'bloqueado') ? '#ff9f89' : '#dff0d8' // Light red for blocked, light green for available
        ];

        if ($row['dia_semana'] !== null) {
            $event['daysOfWeek'] = [$row['dia_semana']];
        } else if ($row['fecha'] !== null) {
            $event['startRecur'] = $row['fecha']; // For specific date blocks
            $event['endRecur'] = $row['fecha'];
        }
        $availability[] = $event;
    }
    echo json_encode($availability);
}

function handle_add_calendar_appointment($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid JSON received', 'error' => json_last_error_msg()]);
        return;
    }

    if (!isset($data['client_id'], $data['fecha_cita'], $data['tipo_tratamiento'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing required fields: client_id, fecha_cita, tipo_tratamiento']);
        return;
    }

    $client_id = $data['client_id'];
    $fecha_cita = $data['fecha_cita'];
    $tipo_tratamiento = $data['tipo_tratamiento'];
    $observaciones = isset($data['observaciones']) ? $data['observaciones'] : null;
    $costo = isset($data['costo']) ? $data['costo'] : null;
    $status = isset($data['status']) ? $data['status'] : 'pendiente';
    $notas_internas = isset($data['notas_internas']) ? $data['notas_internas'] : null;

    $stmt = $conn->prepare("INSERT INTO citas_tratamientos (client_id, fecha_cita, tipo_tratamiento, observaciones, costo, status, notas_internas) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("isssdss", $client_id, $fecha_cita, $tipo_tratamiento, $observaciones, $costo, $status, $notas_internas);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(['message' => 'Cita añadida con éxito', 'id' => $conn->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error al añadir la cita', 'error' => $stmt->error]);
    }

    $stmt->close();
}

function handle_update_appointment($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid JSON received', 'error' => json_last_error_msg()]);
        return;
    }

    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing appointment ID']);
        return;
    }

    $id = $data['id'];
    $updates = [];
    $params = [];
    $types = "";

    if (isset($data['client_id'])) { $updates[] = "client_id = ?"; $params[] = $data['client_id']; $types .= "i"; }
    if (isset($data['fecha_cita'])) { $updates[] = "fecha_cita = ?"; $params[] = $data['fecha_cita']; $types .= "s"; }
    if (isset($data['tipo_tratamiento'])) { $updates[] = "tipo_tratamiento = ?"; $params[] = $data['tipo_tratamiento']; $types .= "s"; }
    if (isset($data['observaciones'])) { $updates[] = "observaciones = ?"; $params[] = $data['observaciones']; $types .= "s"; }
    if (isset($data['costo'])) { $updates[] = "costo = ?"; $params[] = $data['costo']; $types .= "d"; }
    if (isset($data['status'])) { $updates[] = "status = ?"; $params[] = $data['status']; $types .= "s"; }
    if (isset($data['notas_internas'])) { $updates[] = "notas_internas = ?"; $params[] = $data['notas_internas']; $types .= "s"; }

    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['message' => 'No fields to update']);
        return;
    }

    $sql = "UPDATE citas_tratamientos SET " . implode(", ", $updates) . " WHERE id = ?";
    $params[] = $id; // Add ID to the end of parameters
    $types .= "i"; // Add type for ID

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Cita actualizada con éxito']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Cita no encontrada o sin cambios']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error al actualizar la cita', 'error' => $stmt->error]);
    }

    $stmt->close();
}

function handle_delete_appointment($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid JSON received', 'error' => json_last_error_msg()]);
        return;
    }

    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing appointment ID']);
        return;
    }

    $id = $data['id'];

    $stmt = $conn->prepare("DELETE FROM citas_tratamientos WHERE id = ?");
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Cita eliminada con éxito']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Cita no encontrada']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error al eliminar la cita', 'error' => $stmt->error]);
    }

    $stmt->close();
}

function handle_add_availability($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid JSON received', 'error' => json_last_error_msg()]);
        return;
    }

    if (!isset($data['hora_inicio'], $data['hora_fin'], $data['tipo'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing required fields: hora_inicio, hora_fin, tipo']);
        return;
    }

    $dia_semana = isset($data['dia_semana']) ? $data['dia_semana'] : null;
    $fecha = isset($data['fecha']) ? $data['fecha'] : null;
    $hora_inicio = $data['hora_inicio'];
    $hora_fin = $data['hora_fin'];
    $tipo = $data['tipo'];
    $descripcion = isset($data['descripcion']) ? $data['descripcion'] : null;

    $stmt = $conn->prepare("INSERT INTO disponibilidad (dia_semana, fecha, hora_inicio, hora_fin, tipo, descripcion) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("isssss", $dia_semana, $fecha, $hora_inicio, $hora_fin, $tipo, $descripcion);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(['message' => 'Disponibilidad añadida con éxito', 'id' => $conn->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error al añadir disponibilidad', 'error' => $stmt->error]);
    }

    $stmt->close();
}

function handle_update_availability($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid JSON received', 'error' => json_last_error_msg()]);
        return;
    }

    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing availability ID']);
        return;
    }

    $id = $data['id'];
    $updates = [];
    $params = [];
    $types = "";

    if (isset($data['dia_semana'])) { $updates[] = "dia_semana = ?"; $params[] = $data['dia_semana']; $types .= "i"; }
    if (isset($data['fecha'])) { $updates[] = "fecha = ?"; $params[] = $data['fecha']; $types .= "s"; }
    if (isset($data['hora_inicio'])) { $updates[] = "hora_inicio = ?"; $params[] = $data['hora_inicio']; $types .= "s"; }
    if (isset($data['hora_fin'])) { $updates[] = "hora_fin = ?"; $params[] = $data['hora_fin']; $types .= "s"; }
    if (isset($data['tipo'])) { $updates[] = "tipo = ?"; $params[] = $data['tipo']; $types .= "s"; }
    if (isset($data['descripcion'])) { $updates[] = "descripcion = ?"; $params[] = $data['descripcion']; $types .= "s"; }

    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['message' => 'No fields to update']);
        return;
    }

    $sql = "UPDATE disponibilidad SET " . implode(", ", $updates) . " WHERE id = ?";
    $params[] = $id; // Add ID to the end of parameters
    $types .= "i"; // Add type for ID

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Disponibilidad actualizada con éxito']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Disponibilidad no encontrada o sin cambios']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error al actualizar disponibilidad', 'error' => $stmt->error]);
    }

    $stmt->close();
}

function handle_delete_availability($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid JSON received', 'error' => json_last_error_msg()]);
        return;
    }

    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing availability ID']);
        return;
    }

    $id = $data['id'];

    $stmt = $conn->prepare("DELETE FROM disponibilidad WHERE id = ?");
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Disponibilidad eliminada con éxito']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Disponibilidad no encontrada']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error al eliminar disponibilidad', 'error' => $stmt->error]);
    }

    $stmt->close();
}

function handle_search_available_slots($conn) {
    $start_datetime_str = $_GET['start_datetime'] ?? null;
    $end_datetime_str = $_GET['end_datetime'] ?? null;
    $duration_minutes = $_GET['duration'] ?? 30; // Default to 30 minutes

    $debug_info = [
        'start_datetime_param' => $start_datetime_str,
        'end_datetime_param' => $end_datetime_str,
        'duration_minutes_param' => $duration_minutes,
        'fetched_availability_blocks' => [],
        'fetched_existing_appointments' => [],
        'generated_potential_slots' => [],
        'filtered_available_slots' => [],
        'debug_messages' => []
    ];

    if (!$start_datetime_str || !$end_datetime_str) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing start_datetime or end_datetime']);
        return;
    }

    $start_datetime = new DateTime($start_datetime_str);
    $end_datetime = new DateTime($end_datetime_str);

    // 1. Get all availability blocks
    $sql_avail = "SELECT id, dia_semana, fecha, hora_inicio, hora_fin, tipo, descripcion FROM disponibilidad";
    $result_avail = $conn->query($sql_avail);
    if ($result_avail) {
        while ($row = $result_avail->fetch_assoc()) {
            $debug_info['fetched_availability_blocks'][] = $row;
        }
    }

    // 2. Get all existing appointments
    $sql_appointments = "SELECT fecha_cita FROM citas_tratamientos WHERE fecha_cita BETWEEN ? AND ?";
    $stmt_appointments = $conn->prepare($sql_appointments);
    if ($stmt_appointments === false) {
        $debug_info['debug_messages'][] = 'Prepare appointments failed: ' . $conn->error;
        echo json_encode($debug_info); // Return debug info even on error
        return;
    }
    $stmt_appointments->bind_param("ss", $start_datetime_str, $end_datetime_str);
    $stmt_appointments->execute();
    $result_appointments = $stmt_appointments->get_result();
    if ($result_appointments) {
        while ($row = $result_appointments->fetch_assoc()) {
            $debug_info['fetched_existing_appointments'][] = $row['fecha_cita'];
        }
    }
    $stmt_appointments->close();

    // Iterate through each day in the range
    $current_day = clone $start_datetime;
    while ($current_day <= $end_datetime) {
        $day_of_week = (int)$current_day->format('w'); // 0 (for Sunday) through 6 (for Saturday)
        $current_date_str = $current_day->format('Y-m-d');

        // Get relevant availability for this specific day
        $daily_availability = [];
        foreach ($debug_info['fetched_availability_blocks'] as $block) {
            if ($block['fecha'] !== null && $block['fecha'] === $current_date_str) {
                // Specific date block
                $daily_availability[] = $block;
            } else if ($block['dia_semana'] !== null && (int)$block['dia_semana'] === $day_of_week && $block['fecha'] === null) {
                // Recurring weekly block
                $daily_availability[] = $block;
            }
        }

        // Sort availability blocks by start time
        usort($daily_availability, function($a, $b) {
            return strtotime($a['hora_inicio']) - strtotime($b['hora_inicio']);
        });

        // Generate potential slots for the day
        foreach ($daily_availability as $block) {
            $block_start_time = new DateTime($current_date_str . ' ' . $block['hora_inicio']);
            $block_end_time = new DateTime($current_date_str . ' ' . $block['hora_fin']);

            // Adjust block times to fit within the overall search range
            if ($block_start_time < $start_datetime) $block_start_time = $start_datetime;
            if ($block_end_time > $end_datetime) $block_end_time = $end_datetime;

            if ($block['tipo'] === 'disponible') {
                $interval_start = $block_start_time;
                while ($interval_start->getTimestamp() + $duration_minutes * 60 <= $block_end_time->getTimestamp()) {
                    $slot_end = clone $interval_start;
                    $slot_end->modify('+' . $duration_minutes . ' minutes');

                    $potential_slot = [
                        'start' => $interval_start->format('Y-m-d H:i:s'),
                        'end' => $slot_end->format('Y-m-d H:i:s')
                    ];
                    $debug_info['generated_potential_slots'][] = $potential_slot;

                    $is_available = true;
                    // Check against existing appointments
                    foreach ($debug_info['fetched_existing_appointments'] as $appt_time_str) {
                        $appt_time = new DateTime($appt_time_str);
                        $appt_end = clone $appt_time;
                        $appt_end->modify('+' . $duration_minutes . ' minutes'); // Assuming appointments are also 30 min for simplicity

                        // Check for overlap
                        if (($interval_start < $appt_end && $slot_end > $appt_time)) {
                            $is_available = false;
                            $debug_info['debug_messages'][] = 'Slot ' . $potential_slot['start'] . ' blocked by appointment ' . $appt_time_str;
                            break;
                        }
                    }

                    // Check against 'bloqueado' availability blocks
                    foreach ($daily_availability as $blocked_block) {
                        if ($blocked_block['tipo'] === 'bloqueado') {
                            $blocked_start = new DateTime($current_date_str . ' ' . $blocked_block['hora_inicio']);
                            $blocked_end = new DateTime($current_date_str . ' ' . $blocked_block['hora_fin']);

                            if (($interval_start < $blocked_end && $slot_end > $blocked_start)) {
                                $is_available = false;
                                $debug_info['debug_messages'][] = 'Slot ' . $potential_slot['start'] . ' blocked by availability block ' . $blocked_block['id'] . ' (' . $blocked_block['hora_inicio'] . '-' . $blocked_block['hora_fin'] . ')';
                                break;
                            }
                        }
                    }

                    if ($is_available) {
                        $debug_info['filtered_available_slots'][] = $potential_slot;
                    }
                    $interval_start->modify('+' . $duration_minutes . ' minutes');
                }
            }
        }
        $current_day->modify('+1 day');
    }

    echo json_encode($debug_info);
}

function handle_submit_booking($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Invalid JSON received', 'error' => json_last_error_msg()]);
        return;
    }

    // Validate required fields
    if (!isset($data['nombre_cliente'], $data['telefono_cliente'], $data['email_cliente'], $data['motivo_consulta'], $data['fecha_cita'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing required booking fields.']);
        return;
    }

    $nombre_cliente = $data['nombre_cliente'];
    $telefono_cliente = $data['telefono_cliente'];
    $email_cliente = $data['email_cliente'];
    $motivo_consulta = $data['motivo_consulta'];
    $fecha_cita_str = $data['fecha_cita'];

    // Check for double booking (simplified: check if the exact slot is already taken)
    $stmt_check = $conn->prepare("SELECT COUNT(*) FROM citas_tratamientos WHERE fecha_cita = ?");
    if ($stmt_check === false) {
        http_response_code(500);
        echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
        return;
    }
    $stmt_check->bind_param("s", $fecha_cita_str);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($count > 0) {
        http_response_code(409); // Conflict
        echo json_encode(['message' => 'El hueco horario seleccionado ya está reservado. Por favor, elige otro.']);
        return;
    }

    // Find or create client
    $client_id = null;
    $isNewClient = false;
    $stmt_find_client = $conn->prepare("SELECT id FROM clientes WHERE email = ? OR telefono = ?");
    if ($stmt_find_client === false) {
        http_response_code(500);
        echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
        return;
    }
    $stmt_find_client->bind_param("ss", $email_cliente, $telefono_cliente);
    $stmt_find_client->execute();
    $result_find_client = $stmt_find_client->get_result();

    if ($result_find_client->num_rows > 0) {
        $client_row = $result_find_client->fetch_assoc();
        $client_id = $client_row['id'];
        $isNewClient = false;
    } else {
        // Create new client
        $stmt_create_client = $conn->prepare("INSERT INTO clientes (nombre, telefono, email) VALUES (?, ?, ?)");
        if ($stmt_create_client === false) {
            http_response_code(500);
            echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
            return;
        }
        $stmt_create_client->bind_param("sss", $nombre_cliente, $telefono_cliente, $email_cliente);
        if ($stmt_create_client->execute()) {
            $client_id = $conn->insert_id;
            $isNewClient = true;
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error al crear nuevo cliente', 'error' => $stmt_create_client->error]);
            return;
        }
        $stmt_create_client->close();
    }
    $stmt_find_client->close();

    if ($client_id === null) {
        http_response_code(500);
        echo json_encode(['message' => 'No se pudo determinar el ID del cliente.']);
        return;
    }

    // Insert booking into citas_tratamientos
    $stmt_insert_booking = $conn->prepare("INSERT INTO citas_tratamientos (client_id, fecha_cita, tipo_tratamiento, observaciones, status) VALUES (?, ?, ?, ?, ?)");
    if ($stmt_insert_booking === false) {
        http_response_code(500);
        echo json_encode(['message' => 'Prepare failed: ' . $conn->error]);
        return;
    }

    $status = 'pendiente'; // Default status for online bookings
    $stmt_insert_booking->bind_param("issss", $client_id, $fecha_cita_str, $motivo_consulta, $motivo_consulta, $status);

    if ($stmt_insert_booking->execute()) {
        // Send email confirmations using EmailService
        try {
            require_once __DIR__ . '/../includes/EmailService.php';
            $emailService = new EmailService();
            
            // Prepare client data
            $clientData = [
                'nombre' => $nombre_cliente,
                'telefono' => $telefono_cliente,
                'email' => $email_cliente
            ];
            
            // Prepare booking data
            $bookingData = [
                'fecha_cita' => $fecha_cita_str,
                'motivo_consulta' => $motivo_consulta
            ];
            
            // Send confirmation to client
            $clientEmailSent = $emailService->sendBookingConfirmation($clientData, $bookingData);
            
            // Send notification to therapist
            $therapistEmailSent = $emailService->sendTherapistNotification($clientData, $bookingData, $isNewClient);
            
            // Log email results
            if ($clientEmailSent) {
                error_log("Email de confirmación enviado exitosamente a: " . $email_cliente);
            } else {
                error_log("Error enviando email de confirmación a: " . $email_cliente);
            }
            
            if ($therapistEmailSent) {
                error_log("Notificación al fisioterapeuta enviada exitosamente");
            } else {
                error_log("Error enviando notificación al fisioterapeuta");
            }
            
            $emailStatus = $clientEmailSent ? "Se ha enviado una confirmación a su correo." : "La cita se reservó pero hubo un problema con el email.";
            
            http_response_code(201);
            echo json_encode([
                'message' => 'Cita reservada con éxito. ' . $emailStatus,
                'email_sent' => $clientEmailSent,
                'therapist_notified' => $therapistEmailSent
            ]);
            
        } catch (Exception $e) {
            error_log("Error en el sistema de email: " . $e->getMessage());
            
            // Still return success for the booking, but mention email issue
            http_response_code(201);
            echo json_encode([
                'message' => 'Cita reservada con éxito. Hubo un problema con el email de confirmación.',
                'email_sent' => false,
                'therapist_notified' => false,
                'email_error' => 'Error en el sistema de email'
            ]);
        }
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error al registrar la cita', 'error' => $stmt_insert_booking->error]);
    }

    $stmt_insert_booking->close();
}

$conn->close();
?>