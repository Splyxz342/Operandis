<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

include 'conexion.php';

// Definir la carpeta de subida
$upload_dir = 'uploads/banderas/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Función para subir una imagen
function subirImagen($file_input_name) {
    global $upload_dir;
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] === UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES[$file_input_name]['tmp_name'];
        $file_name = $_FILES[$file_input_name]['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $new_file_name = uniqid('flag_', true) . '.' . $file_ext;
        $dest_path = $upload_dir . $new_file_name;

        if (move_uploaded_file($file_tmp_path, $dest_path)) {
            return $dest_path;
        }
    }
    return null;
}

// Guardar producto
if (isset($_POST['guardar_producto'])) {
    $nombre = $conexion->real_escape_string($_POST['nombre_producto']);
    if ($nombre) {
        $conexion->query("INSERT INTO productos (nombre) VALUES ('$nombre')");
    }
    header("Location: index.php#productos");
    exit;
}

// Actualizar producto
if (isset($_POST['actualizar_producto'])) {
    $id = (int)$_POST['id'];
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    if ($nombre) {
        $conexion->query("UPDATE productos SET nombre='$nombre' WHERE id=$id");
    }
    header("Location: index.php#productos");
    exit;
}

// Eliminar producto
if (isset($_GET['eliminar_producto'])) {
    $id = (int)$_GET['eliminar_producto'];
    $conexion->query("DELETE FROM productos WHERE id=$id");
    header("Location: index.php#productos");
    exit;
}

// Guardar país
if (isset($_POST['guardar_pais'])) {
    $nombre = $conexion->real_escape_string($_POST['nombre_pais']);
    $codigo_iso = $conexion->real_escape_string($_POST['codigo_iso']);
    
    // Subir la bandera
    $bandera_path = subirImagen('bandera');

    if ($nombre && $codigo_iso && $bandera_path) {
        $conexion->query("INSERT INTO paises (nombre, codigo_iso, bandera) VALUES ('$nombre', '$codigo_iso', '$bandera_path')");
    }
    header("Location: index.php#paises");
    exit;
}

// Actualizar país
if (isset($_POST['actualizar_pais'])) {
    $id = (int)$_POST['id'];
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $codigo_iso = $conexion->real_escape_string($_POST['codigo_iso']);
    
    $bandera_path = null;
    // Si se subió un nuevo archivo, procesarlo
    if (isset($_FILES['bandera']) && $_FILES['bandera']['error'] === UPLOAD_ERR_OK) {
        // Eliminar la bandera anterior si existe
        $stmt_old_flag = $conexion->prepare("SELECT bandera FROM paises WHERE id=?");
        $stmt_old_flag->bind_param("i", $id);
        $stmt_old_flag->execute();
        $result_old_flag = $stmt_old_flag->get_result();
        if ($row_old_flag = $result_old_flag->fetch_assoc()) {
            if (file_exists($row_old_flag['bandera'])) {
                unlink($row_old_flag['bandera']);
            }
        }
        $stmt_old_flag->close();
        
        $bandera_path = subirImagen('bandera');
        if ($bandera_path) {
            $stmt = $conexion->prepare("UPDATE paises SET nombre=?, codigo_iso=?, bandera=? WHERE id=?");
            $stmt->bind_param("sssi", $nombre, $codigo_iso, $bandera_path, $id);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        // Si no se subió un nuevo archivo, actualizar solo nombre y código
        $stmt = $conexion->prepare("UPDATE paises SET nombre=?, codigo_iso=? WHERE id=?");
        $stmt->bind_param("ssi", $nombre, $codigo_iso, $id);
        $stmt->execute();
        $stmt->close();
    }
    
    header("Location: index.php#paises");
    exit;
}

// Eliminar país
if (isset($_GET['eliminar_pais'])) {
    $id = (int)$_GET['eliminar_pais'];
    // Obtener la ruta de la bandera para eliminar el archivo
    $stmt = $conexion->prepare("SELECT bandera FROM paises WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row && file_exists($row['bandera'])) {
        unlink($row['bandera']);
    }
    $stmt->close();

    $conexion->query("DELETE FROM paises WHERE id=$id");
    header("Location: index.php#paises");
    exit;
}

// Guardar exportación
if (isset($_POST['guardar_exportacion'])) {
    $producto_id = (int)$_POST['producto_id'];
    $pais_origen_id = (int)$_POST['pais_origen_id'];
    $pais_destino_id = (int)$_POST['pais_destino_id'];
    $cantidad = (int)$_POST['cantidad'];
    $fecha_exportacion = $conexion->real_escape_string($_POST['fecha_exportacion']);

    $stmt = $conexion->prepare("INSERT INTO exportaciones (producto_id, pais_origen_id, pais_destino_id, cantidad, fecha_exportacion) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiis", $producto_id, $pais_origen_id, $pais_destino_id, $cantidad, $fecha_exportacion);
    
    if ($stmt->execute()) {
      echo "Exportación registrada con éxito.";
    } else {
      echo "Error al registrar la exportación: " . $stmt->error;
    }

    $stmt->close();
    header("Location: index.php#exportaciones");
    exit;
}

// Actualizar exportación
if (isset($_POST['actualizar_exportacion'])) {
    $id = (int)$_POST['id'];
    $producto_id = (int)$_POST['producto_id'];
    $pais_origen_id = (int)$_POST['pais_origen_id'];
    $pais_destino_id = (int)$_POST['pais_destino_id'];
    $cantidad = (int)$_POST['cantidad'];
    $fecha_exportacion = $conexion->real_escape_string($_POST['fecha_exportacion']);
    $estado = $conexion->real_escape_string($_POST['estado']);

    $stmt = $conexion->prepare("UPDATE exportaciones SET producto_id=?, pais_origen_id=?, pais_destino_id=?, cantidad=?, fecha_exportacion=?, estado=? WHERE id=?");
    $stmt->bind_param("iiiissi", $producto_id, $pais_origen_id, $pais_destino_id, $cantidad, $fecha_exportacion, $estado, $id);
    
    if ($stmt->execute()) {
      echo "Exportación actualizada con éxito.";
    } else {
      echo "Error al actualizar la exportación: " . $stmt->error;
    }

    $stmt->close();
    header("Location: index.php#exportaciones");
    exit;
}

// Eliminar exportación
if (isset($_GET['eliminar_exportacion'])) {
    $id = (int)$_GET['eliminar_exportacion'];
    $conexion->query("DELETE FROM exportaciones WHERE id=$id");
    header("Location: index.php#exportaciones");
    exit;
}
?>