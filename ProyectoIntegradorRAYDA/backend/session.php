<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');
if (isset($_SESSION['username'])) {
    echo json_encode(['status' => 'ok', 'username' => $_SESSION['username'], 'role' => $_SESSION['role'] ?? 'user']);
} else {
    echo json_encode(['status' => 'not_logged']);
}
?>
