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

$query = "SELECT r.id, r.id_producto, i.nombre as producto_nombre, r.tipo_movimiento, r.cantidad, r.usuario, r.created_at
            FROM registros r
            LEFT JOIN inventario i ON i.id = r.id_producto
            ORDER BY r.created_at DESC";
$res = $conexion->query($query);
$rows = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }
}

echo json_encode($rows);
?>
