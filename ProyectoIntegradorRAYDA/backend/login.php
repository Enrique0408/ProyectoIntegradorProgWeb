<?php
session_start();
include("conexion.php");

header('Content-Type: application/json; charset=UTF-8');

// Leer input JSON o POST form
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$username = $input['username'] ?? null;
$password = $input['password'] ?? null;

if (!$username || !$password) {
    echo json_encode(['status' => 'error', 'message' => 'Credenciales requeridas']);
    exit;
}

// Asegurar que la tabla usuarios exista (si no existe, crear y sembrar un admin/demo)
$create = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user'
)";
$conexion->query($create);

// Sembrar usuarios por defecto si tabla vacía
$res = $conexion->query("SELECT COUNT(*) as c FROM usuarios");
$row = $res->fetch_assoc();
if (intval($row['c']) === 0) {
    $passAdmin = password_hash('admin123', PASSWORD_DEFAULT);
    $passUser = password_hash('user123', PASSWORD_DEFAULT);
    $conexion->query("INSERT INTO usuarios (username, password_hash, role) VALUES ('admin', '{$passAdmin}', 'admin')");
    $conexion->query("INSERT INTO usuarios (username, password_hash, role) VALUES ('user', '{$passUser}', 'user')");
}

// Buscar usuario
$stmt = $conexion->prepare('SELECT id, username, password_hash, role FROM usuarios WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
    exit;
}
$u = $result->fetch_assoc();

if (password_verify($password, $u['password_hash'])) {
    // Login OK: guardar sesión
    $_SESSION['user_id'] = $u['id'];
    $_SESSION['username'] = $u['username'];
    $_SESSION['role'] = $u['role'];

    echo json_encode(['status' => 'ok', 'username' => $u['username'], 'role' => $u['role']]);
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Contraseña incorrecta']);
    exit;
}

?>
