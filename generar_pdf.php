<?php
// generar_pdf.php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
require('vendor/autoload.php'); // Esta línea es fundamental

use Fpdf\Fpdf;

// ... el resto de tu código

$tipo = $_GET['tipo'] ?? '';

class PDF extends Fpdf {
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, utf8_decode('Reporte de ' . ucfirst($_GET['tipo'])), 0, 1, 'C');
        $this->Ln(5);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

switch ($tipo) {
    case 'productos':
        $sql = "SELECT id, nombre FROM productos";
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(20, 10, 'ID', 1);
        $pdf->Cell(170, 10, 'Nombre', 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 10);
        $result = $conexion->query($sql);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(20, 10, $row['id'], 1);
            $pdf->Cell(170, 10, utf8_decode($row['nombre']), 1);
            $pdf->Ln();
        }
        break;
        
    case 'paises':
        $sql = "SELECT id, nombre, codigo_iso FROM paises";
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(20, 10, 'ID', 1);
        $pdf->Cell(130, 10, 'Nombre', 1);
        $pdf->Cell(40, 10, utf8_decode('Código ISO'), 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 10);
        $result = $conexion->query($sql);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(20, 10, $row['id'], 1);
            $pdf->Cell(130, 10, utf8_decode($row['nombre']), 1);
            $pdf->Cell(40, 10, $row['codigo_iso'], 1);
            $pdf->Ln();
        }
        break;
        
    case 'exportaciones':
        $sql = "SELECT e.id, p.nombre AS producto, po.nombre AS pais_origen, pd.nombre AS pais_destino, e.cantidad, e.fecha_exportacion, e.estado
                FROM exportaciones e
                JOIN productos p ON e.producto_id = p.id
                JOIN paises po ON e.pais_origen_id = po.id
                JOIN paises pd ON e.pais_destino_id = pd.id";
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 10, 'ID', 1);
        $pdf->Cell(35, 10, 'Producto', 1);
        $pdf->Cell(35, 10, 'Origen', 1);
        $pdf->Cell(35, 10, 'Destino', 1);
        $pdf->Cell(20, 10, 'Cantidad', 1);
        $pdf->Cell(30, 10, 'Fecha', 1);
        $pdf->Cell(25, 10, 'Estado', 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 10);
        $result = $conexion->query($sql);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(10, 10, $row['id'], 1);
            $pdf->Cell(35, 10, utf8_decode($row['producto']), 1);
            $pdf->Cell(35, 10, utf8_decode($row['pais_origen']), 1);
            $pdf->Cell(35, 10, utf8_decode($row['pais_destino']), 1);
            $pdf->Cell(20, 10, $row['cantidad'], 1);
            $pdf->Cell(30, 10, $row['fecha_exportacion'], 1);
            $pdf->Cell(25, 10, utf8_decode($row['estado']), 1);
            $pdf->Ln();
        }
        break;
    default:
        die("Tipo de reporte no válido.");
}

$pdf->Output('I', "reporte_de_$tipo.pdf");
$conexion->close();
?>