<?php
// conexion.php

$host = "localhost";
$usuario = "root";
$contrasena = "";
$basedatos = "exportaciones"; // Nombre de la nueva base de datos

// Crear la conexión usando mysqli
$conexion = new mysqli($host, $usuario, $contrasena, $basedatos);

// Verificar si hubo error en la conexión
if ($conexion->connect_errno) {
    die("Error en la conexión a la base de datos: " . $conexion->connect_error);
}

// Opcional: establecer el conjunto de caracteres a utf8mb4
$conexion->set_charset("utf8mb4");
?>