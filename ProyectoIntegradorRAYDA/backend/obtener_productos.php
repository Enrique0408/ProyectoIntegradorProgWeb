<?php
// Alias para compatibilidad: devuelve el mismo JSON que obtener_inventario.php
if (file_exists(__DIR__ . '/obtener_inventario.php')) {
    include __DIR__ . '/obtener_inventario.php';
    exit;
}
header('Content-Type: application/json; charset=UTF-8');
echo json_encode([]);
?>