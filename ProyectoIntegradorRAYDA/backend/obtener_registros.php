<?php
session_start();
include('conexion.php');
header('Content-Type: application/json; charset=UTF-8');

// --- 1. Seguridad: Solo administrador ---
if (!isset($_SESSION['username']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado']);
    exit;
}

// --- 2. Consulta SQL (Alineada a tus tablas) ---
$query = "SELECT 
            r.id, 
            r.id_producto, 
            i.nombre AS producto_nombre, 
            r.tipo_movimiento, 
            r.cantidad, 
            r.created_at
          FROM registros r
          LEFT JOIN inventario i ON i.id = r.id_producto
          ORDER BY r.created_at DESC";

// Ejecuta la consulta y maneja el error si falla
$res = $conexion->query($query);
$rows = [];

// --- 3. Manejo de Errores de Consulta ---
if (!$res) {
    // Si la consulta falla (probablemente por una columna faltante en registros o inventario),
    // devolvemos un JSON de error limpio para evitar el SyntaxError en el frontend.
    http_response_code(500);
    error_log("Error de MySQL en obtener_registros.php: " . $conexion->error);
    echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos al obtener registros.']);
    exit;
}

// Si la consulta es exitosa, procesamos los resultados
while ($row = $res->fetch_assoc()) {
    $rows[] = $row;
}

// Devolvemos el array de registros
echo json_encode($rows);
?>