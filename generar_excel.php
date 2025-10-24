<?php
// generar_excel.php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require 'vendor/autoload.php';
include 'conexion.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$tipo = $_GET['tipo'] ?? '';
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$result = null;
$headers = [];

switch ($tipo) {
    case 'productos':
        $sql = "SELECT id, nombre FROM productos";
        $result = $conexion->query($sql);
        $headers = ['ID', 'Nombre'];
        break;
    case 'paises':
        $sql = "SELECT id, nombre, codigo_iso FROM paises";
        $result = $conexion->query($sql);
        $headers = ['ID', 'Nombre', 'Codigo ISO'];
        break;
    case 'exportaciones':
        $sql = "SELECT e.id, p.nombre AS producto, po.nombre AS pais_origen, pd.nombre AS pais_destino, e.cantidad, e.fecha_exportacion, e.estado
                FROM exportaciones e
                JOIN productos p ON e.producto_id = p.id
                JOIN paises po ON e.pais_origen_id = po.id
                JOIN paises pd ON e.pais_destino_id = pd.id";
        $result = $conexion->query($sql);
        $headers = ['ID', 'Producto', 'Pais Origen', 'Pais Destino', 'Cantidad', 'Fecha', 'Estado'];
        break;
    default:
        die("Tipo de reporte no válido.");
}

if ($result) {
    $sheet->fromArray($headers, null, 'A1');
    $rowIndex = 2;
    while ($row = $result->fetch_assoc()) {
        $sheet->fromArray($row, null, 'A' . $rowIndex);
        $rowIndex++;
    }
}

// Establecer headers para la descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=\"reporte_de_$tipo.xlsx\"");
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

$conexion->close();
exit;
?>