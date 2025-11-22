<?php
include("conexion.php");
session_start();

$data = json_decode(file_get_contents("php://input"), true);
$tipo = $data['tipo'];
$lista = $data['lista'];

$usuario = isset($_SESSION['username']) ? $_SESSION['username'] : 'usuario_demo';

foreach ($lista as $item) {
    $id = (int)$item['id'];
    $cantidad = (int)$item['cantidad'];

    // Obtener cantidad actual
    $q = $conexion->query("SELECT cantidad FROM inventario WHERE id = $id");
    $row = $q->fetch_assoc();
    $actual = (int)$row['cantidad'];

    if ($tipo === 'entrada') {
        $nuevo = $actual + $cantidad;
    } else { // salida
        $nuevo = max(0, $actual - $cantidad);
    }

    // Actualizar inventario
    $conexion->query("UPDATE inventario SET cantidad = $nuevo WHERE id = $id");

    // Registrar movimiento
    $stmt = $conexion->prepare("INSERT INTO registros (id_producto, tipo_movimiento, cantidad) VALUES (?, ?, ?)");
    $stmt->bind_param('isis', $id, $tipo, $cantidad);
    $stmt->execute();
}

echo json_encode(["status" => "ok"]);
?>