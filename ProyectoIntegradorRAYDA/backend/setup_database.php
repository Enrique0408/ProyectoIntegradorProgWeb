<?php
// setup_database.php
// Ejecutar desde navegador: http://localhost/Inventario-RAYDA/backend/setup_database.php
// O desde CLI: php setup_database.php

// Intentaremos extraer las credenciales de `conexion.php` para respetar su configuración.
$cfgPath = __DIR__ . '/conexion.php';
$host = 'localhost'; $user = 'root'; $pass = ''; $db = 'inventario_construccion';
if (file_exists($cfgPath)) {
    $cfg = file_get_contents($cfgPath);
    if (preg_match('/\\$host\s*=\s*["\']([^"\']+)["\']/', $cfg, $m)) $host = $m[1];
    if (preg_match('/\\$user\s*=\s*["\']([^"\']+)["\']/', $cfg, $m)) $user = $m[1];
    if (preg_match('/\\$pass\s*=\s*["\']([^"\']*)["\']/', $cfg, $m)) $pass = $m[1];
    if (preg_match('/\\$db\s*=\s*["\']([^"\']+)["\']/', $cfg, $m)) $db = $m[1];
}

$mysqli = new mysqli($host, $user, $pass);
if ($mysqli->connect_error) {
    $msg = "Error conectando a MySQL: " . $mysqli->connect_error;
    if (php_sapi_name() === 'cli') die($msg . PHP_EOL);
    die("<pre>$msg</pre>");
}

// Crear base de datos
if (!$mysqli->query("CREATE DATABASE IF NOT EXISTS `" . $mysqli->real_escape_string($db) . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
    $err = $mysqli->error;
    if (php_sapi_name() === 'cli') die("Error creando base de datos: $err" . PHP_EOL);
    die("<pre>Error creando base de datos: $err</pre>");
}

// Seleccionar la base
$mysqli->select_db($db);

// Ejecutar el SQL del esquema
$schemaFile = __DIR__ . '/schema.sql';
if (!file_exists($schemaFile)) {
    $msg = "No se encontró schema.sql en el directorio backend.";
    if (php_sapi_name() === 'cli') die($msg . PHP_EOL);
    die("<pre>$msg</pre>");
}

$schema = file_get_contents($schemaFile);
// Separar por punto y coma que termina una sentencia (con cierta robustez)
$statements = preg_split('/;\s*\n/', $schema);
$errors = [];
foreach ($statements as $stmt) {
    $s = trim($stmt);
    if ($s === '') continue;
    if (!$mysqli->query($s)) {
        $errors[] = $mysqli->error . " -- Statement: " . substr($s, 0, 120);
    }
}

if (php_sapi_name() === 'cli') {
    if (empty($errors)) {
        echo "Base de datos '$db' y tablas creadas correctamente." . PHP_EOL;
    } else {
        echo "Algunos errores ocurrieron:\n" . implode("\n", $errors) . PHP_EOL;
    }
    exit;
}

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Setup Database - Inventario-RAYDA</title>
  <style>body{font-family:Arial,Helvetica,sans-serif;padding:20px} pre{background:#f4f4f4;padding:10px;border-radius:6px}</style>
</head>
<body>
  <h1>Setup Database</h1>
  <p>Host: <strong><?php echo htmlspecialchars($host) ?></strong></p>
  <p>Base de datos: <strong><?php echo htmlspecialchars($db) ?></strong></p>
  <?php if (empty($errors)): ?>
    <p style="color:green">Base de datos y tablas creadas correctamente.</p>
  <?php else: ?>
    <p style="color:red">Ocurrieron algunos errores al ejecutar el esquema:</p>
    <pre><?php echo htmlspecialchars(implode("\n", $errors)) ?></pre>
  <?php endif; ?>
  <p>Ahora puedes visitar la aplicación en <a href="../frontend/pages/index.html">Catálogo</a> o usar el <code>login.php</code> para iniciar sesión.</p>
</body>
</html>
