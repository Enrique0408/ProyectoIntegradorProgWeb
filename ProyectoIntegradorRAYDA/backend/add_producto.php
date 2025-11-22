<?php
session_start();
include('conexion.php'); // Asegúrate de que $conexion esté disponible

header('Content-Type: application/json; charset=UTF-8');

// --- 1. Seguridad: Solo administrador ---
if (!isset($_SESSION['username']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado.']);
    exit;
}

// --- 2. Capturar y Sanitizar Datos del Formulario (usando $_POST para FormData) ---
// Cuando se usa FormData, los datos de texto vienen en $_POST
$nombre = $conexion->real_escape_string(trim($_POST['nombre'] ?? ''));
$cantidad = (int)($_POST['cantidad'] ?? 0);
$lugar = $conexion->real_escape_string(trim($_POST['lugar'] ?? ''));

$nombre_archivo_db = null; // Inicializamos la variable para el nombre del archivo

// --- 3. Validaciones ---
if ($nombre === '') {
    echo json_encode(['status' => 'error', 'message' => 'Nombre requerido.']);
    exit;
}

// --- 4. Manejo de la Subida de Imagen (Clave para guardar la imagen) ---
if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'Imagen requerida o error de subida.']);
    exit;
}

$file_info = $_FILES['imagen'];
$allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
$max_size = 5 * 1024 * 1024; // 5 MB

// Validaciones de seguridad de la imagen
if (!in_array($file_info['type'], $allowed_types)) {
    echo json_encode(['status' => 'error', 'message' => 'Formato de imagen no permitido. Use JPG, PNG o WEBP.']);
    exit;
}

if ($file_info['size'] > $max_size) {
    echo json_encode(['status' => 'error', 'message' => 'La imagen es demasiado grande (> 5MB).']);
    exit;
}

// Generar nombre de archivo único y seguro
$extension = pathinfo($file_info['name'], PATHINFO_EXTENSION);
// Usamos el nombre del producto y un ID único para el nombre final
$base_name = strtolower(str_replace(' ', '_', $nombre));
$nombre_archivo_db = $base_name . '_' . time() . '.' . $extension;

// 5. Definir la ruta de destino y mover el archivo
// Asegúrate de que esta ruta sea correcta y la carpeta exista y tenga permisos de escritura.
$ruta_destino = '../assets/' . $nombre_archivo_db;

if (!move_uploaded_file($file_info['tmp_name'], $ruta_destino)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error al guardar la imagen en el servidor.']);
    exit;
}

// --- 6. Inserción en Base de Datos (Clave para registrar la imagen) ---
// 🔑 NOTA: La tabla `inventario` DEBE tener la columna `imagen` (VARCHAR).
$stmt = $conexion->prepare('INSERT INTO inventario (nombre, cantidad, lugar, imagen) VALUES (?, ?, ?, ?)');
// 🔑 Modificamos el bind_param: 's' para nombre, 'i' para cantidad, 's' para lugar, 's' para imagen
$stmt->bind_param('siss', $nombre, $cantidad, $lugar, $nombre_archivo_db);

if ($stmt->execute()) {
    echo json_encode(['status' => 'ok', 'id' => $stmt->insert_id]);
} else {
    // Si la inserción falla, intenta eliminar el archivo subido para limpiar
    if (file_exists($ruta_destino)) {
        unlink($ruta_destino);
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error al insertar producto en DB.']);
}
