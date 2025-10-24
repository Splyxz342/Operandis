<?php
// importar_excel.php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require 'vendor/autoload.php';
include 'conexion.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$tipo = $_POST['tipo_tabla'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_excel']) && $_FILES['archivo_excel']['error'] === UPLOAD_ERR_OK) {
    $inputFileName = $_FILES['archivo_excel']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, true);
        
        $inserted_rows = 0;
        
        // Se asume que la primera fila es la cabecera
        if (count($data) > 1) {
            
            switch ($tipo) {
                case 'productos':
                    // Asume columnas: A=Nombre
                    $stmt = $conexion->prepare("INSERT INTO productos (nombre) VALUES (?)");
                    if (!$stmt) die("Error al preparar la consulta: " . $conexion->error);

                    foreach ($data as $rowIndex => $row) {
                        if ($rowIndex === 1) continue; // Saltar cabecera
                        $nombre = $row['A'];
                        if (!empty($nombre)) {
                            $stmt->bind_param("s", $nombre);
                            $stmt->execute();
                            $inserted_rows++;
                        }
                    }
                    $stmt->close();
                    break;

                case 'paises':
                    // Asume columnas: A=Nombre, B=Codigo ISO, C=URL Bandera (opcional)
                    $stmt = $conexion->prepare("INSERT INTO paises (nombre, codigo_iso) VALUES (?, ?)");
                    if (!$stmt) die("Error al preparar la consulta: " . $conexion->error);

                    foreach ($data as $rowIndex => $row) {
                        if ($rowIndex === 1) continue; // Saltar cabecera
                        $nombre = $row['A'];
                        $codigo_iso = $row['B'];
                        if (!empty($nombre) && !empty($codigo_iso)) {
                            $stmt->bind_param("ss", $nombre, $codigo_iso);
                            $stmt->execute();
                            $inserted_rows++;
                        }
                    }
                    $stmt->close();
                    break;
                
                // Nota: La importación de exportaciones es más compleja por las claves foráneas.
                // Requeriría un proceso de búsqueda de IDs. Se puede implementar de manera similar.
            }
        }
        
        header("Location: index.php?mensaje=Se importaron $inserted_rows registros correctamente.&tipo_redirect=$tipo#$tipo");
        exit;
        
    } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
        die('Error al cargar el archivo: ' . $e->getMessage());
    }

} else {
    // Si no se subió un archivo o hubo un error
    header("Location: index.php?error=Error al subir el archivo.&tipo_redirect=$tipo#$tipo");
    exit;
}

$conexion->close();
?>