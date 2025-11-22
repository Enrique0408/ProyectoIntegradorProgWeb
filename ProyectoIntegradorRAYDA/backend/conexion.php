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
           "3) Elimina el punto y coma ";" al inicio para descomentarla, por ejemplo:\n" .
           "   extension=mysqli\n" .
           "4) Guarda el archivo y reinicia Apache desde el panel de XAMPP.\n\n" .
           "Comprueba también usando un phpinfo(): crea un archivo con <?php phpinfo(); ?> y ábrelo en el navegador para confirmar que 'mysqli' aparece en las extensiones cargadas.\n" .
           "Si ejecutas PHP desde la línea de comandos, asegúrate de que la versión de PHP que usas (php --ini) es la misma que usa Apache.\n";

    if (php_sapi_name() === 'cli') {
        // Mensaje en CLI
        fwrite(STDERR, $msg . PHP_EOL);
        exit(1);
    }

    // Mensaje en navegador (más legible)
    header('Content-Type: text/plain; charset=UTF-8');
    echo $msg;
    exit;
}

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
