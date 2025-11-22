<?php
session_start();
include('conexion.php');
header('Content-Type: application/json; charset=UTF-8');

// Solo admin
if (!isset($_SESSION['username']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$nombre = $conexion->real_escape_string(trim($input['nombre'] ?? ''));
$cantidad = (int)($input['cantidad'] ?? 0);
$lugar = $conexion->real_escape_string(trim($input['lugar'] ?? ''));

if ($nombre === '') {
    echo json_encode(['status' => 'error', 'message' => 'Nombre requerido']);
    exit;
}

$stmt = $conexion->prepare('INSERT INTO inventario (nombre, cantidad, lugar) VALUES (?, ?, ?)');
$stmt->bind_param('siss', $nombre, $cantidad, $lugar);
if ($stmt->execute()) {
    echo json_encode(['status' => 'ok', 'id' => $stmt->insert_id]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error al insertar producto']);
}

?>
