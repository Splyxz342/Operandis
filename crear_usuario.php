<?php
include 'conexion.php';

$usuario = 'admin';
$password = '12345678';

// Hashear la contraseña con password_hash()
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conexion->prepare("INSERT INTO usuarios (usuario, password_hash) VALUES (?, ?)");
if (!$stmt) {
    die("Error en prepare(): " . $conexion->error);
}

$stmt->bind_param("ss", $usuario, $password_hash);

if ($stmt->execute()) {
    echo "Usuario creado con éxito.";
} else {
    echo "Error al crear usuario: " . $stmt->error;
}
?>