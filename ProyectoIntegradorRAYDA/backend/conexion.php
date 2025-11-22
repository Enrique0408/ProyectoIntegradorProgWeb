<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "inventario_construccion";

// Comprobar que la extensión mysqli está disponible
if (!class_exists('mysqli')) {
    $msg = "La extensión mysqli de PHP no está disponible.\n" .
        "Esto provoca: Class \"mysqli\" not found.\n\n" .
        "En XAMPP (Windows) habilítala así:\n" .
        "1) Abre: C:\\xampp\\php\\php.ini\n" .
        "2) Busca la línea: ;extension=mysqli o ;extension=php_mysqli.dll\n" .
        "3) Elimina el punto y coma \";\" al inicio para descomentarla, por ejemplo:\n" .
        "   extension=mysqli\n" .
        "4) Guarda el archivo y reinicia Apache desde el panel de XAMPP.\n\n" .
        "Comprueba también usando un phpinfo(): crea un archivo con <?php phpinfo(); ?> y ábrelo en el navegador para confirmar que 'mysqli' aparece en las extensiones cargadas.\n" .
        "Si ejecutas PHP desde la línea de comandos, asegúrate de que la versión de PHP que usas (php --ini) es la misma que usa Apache.\n";

    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        // Si es una llamada AJAX/fetch que espera JSON, devolvemos JSON 500.
        http_response_code(500);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['status' => 'error', 'message' => 'Fallo interno: extensión mysqli no disponible.']);
    } else {
        // Si no es AJAX (navegación directa), mantenemos tu lógica original para CLI y navegador.
        if (php_sapi_name() === 'cli') {
            fwrite(STDERR, $msg . PHP_EOL);
            exit(1);
        } else {
            header('Content-Type: text/plain; charset=UTF-8');
            echo $msg;
        }
    }
    exit;
}

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    $error_msg = "Error de conexión: " . $conexion->connect_error;

    // Detección de AJAX y respuesta JSON/500
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        // Si es una llamada AJAX/fetch que espera JSON, devolvemos JSON 500.
        http_response_code(500);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['status' => 'error', 'message' => $error_msg]);
    } else {
        // Si no es AJAX (navegación directa), mantenemos tu lógica original (die).
        die($error_msg);
    }
    exit;
}
