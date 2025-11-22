<?php
header('Content-Type: text/plain; charset=UTF-8');
echo "Test mysqli availability\n";
echo "extension_loaded('mysqli'): "; var_export(extension_loaded('mysqli')); echo "\n";
echo "class_exists('mysqli'): "; var_export(class_exists('mysqli')); echo "\n";

// Try to include backend conexion and connect
$conexionPath = __DIR__ . '/backend/conexion.php';
if (file_exists($conexionPath)) {
    echo "Including backend/conexion.php...\n";
    // Include will attempt to connect (conexion.php exits on failure)
    try {
        include $conexionPath;
        if (isset($conexion) && $conexion instanceof mysqli) {
            echo "Connection: OK (connected to DB).\n";
            $conexion->close();
        } else {
            echo "Connection: no se creó el objeto \$conexion.\n";
        }
    } catch (Throwable $e) {
        echo "Exception while including conexion.php: " . $e->getMessage() . "\n";
    }
} else {
    echo "backend/conexion.php not found at: $conexionPath\n";
}

?>
