<?php
session_start();

// Evitar que el navegador guarde en cache las páginas después del logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.php");
  exit;
}

include "conexion.php";

// Obtener datos para el dashboard
$sql_total_paises = "SELECT COUNT(*) AS total FROM paises";
$result_paises = $conexion->query($sql_total_paises);
$total_paises = $result_paises->fetch_assoc()['total'];

$sql_total_exportaciones = "SELECT COUNT(*) AS total FROM exportaciones";
$result_exportaciones = $conexion->query($sql_total_exportaciones);
$total_exportaciones = $result_exportaciones->fetch_assoc()['total'];

$sql_total_productos = "SELECT COUNT(*) AS total FROM productos";
$result_productos = $conexion->query($sql_total_productos);
$total_productos = $result_productos->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard Operandi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
      color: #333;
      margin: 0;
      padding: 0;
      display: flex;
      min-height: 100vh;
    }
    #wrapper {
        width: 100%;
        display: flex;
    }
    .sidebar {
      background-color: #0B1E2D;
      padding-top: 20px;
      width: 250px;
      flex-shrink: 0;
      height: 100vh;
      position: sticky;
      top: 0;
    }
    .sidebar-heading {
        color: #00c3ff;
        font-size: 1.8rem;
        padding: 1.5rem 1rem;
        border-bottom: 1px solid rgba(0, 195, 255, 0.2);
    }
    .sidebar .list-group-item {
      color: #00c3ff;
      font-weight: bold;
      background-color: transparent;
      border: none;
      padding: 0.75rem 1.5rem;
    }
    .sidebar .list-group-item:hover,
    .sidebar .list-group-item.active {
      background-color: rgba(0, 195, 255, 0.1);
      color: #00c3ff;
    }
    #page-content-wrapper {
        flex-grow: 1;
        padding: 20px;
        background-color: #f8f9fa;
    }
    .content-section {
      background-color: #f8f9fa;
      padding: 20px;
      min-height: calc(100vh - 40px);
    }
    .card {
      background-color: #ffffff;
      border: 1px solid #dee2e6;
      color: #333;
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .card-dashboard .card-title {
        color: #0B1E2D;
        font-weight: bold;
    }
    .card-dashboard .card-text {
        font-size: 2.5rem;
        font-weight: bold;
        color: #00c3ff;
    }
    .table-responsive {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
    .table {
      --bs-table-bg: #ffffff;
      --bs-table-striped-bg: #f8f9fa;
      --bs-table-striped-color: #333;
      --bs-table-hover-bg: #e2f4ff;
      --bs-table-hover-color: #0B1E2D;
      color: #333;
      border-color: #dee2e6;
    }
    .table thead th {
        background-color: #0B1E2D;
        color: #00c3ff;
        border-color: #0B1E2D;
    }
    .btn-primary {
      background-color: #00c3ff;
      border-color: #00c3ff;
      color: #0B1E2D;
      font-weight: bold;
    }
    .btn-primary:hover {
      background-color: #0099cc;
      border-color: #0099cc;
    }
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }
    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }
    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }
    .btn-warning:hover {
        background-color: #e0a800;
        border-color: #d39e00;
    }
    .btn-success {
      background-color: #28a745;
      border-color: #28a745;
      color: white;
      font-weight: bold;
    }
    .btn-success:hover {
      background-color: #218838;
      border-color: #1e7e34;
    }
    .form-control {
      background-color: #ffffff;
      border: 1px solid #ced4da;
      color: #333;
    }
    .form-control:focus {
      background-color: #ffffff;
      border-color: #00c3ff;
      box-shadow: 0 0 0 0.25rem rgba(0, 195, 255, 0.25);
      color: #333;
    }
    .form-control::placeholder {
        color: #6c757d;
    }
    .form-label {
        color: #0B1E2D;
    }
    h3 {
        color: #0B1E2D;
        margin-bottom: 1.5rem;
    }
    .flag-icon {
        width: 24px;
        height: 18px;
        margin-right: 8px;
    }
  </style>
</head>
<body>
<div class="d-flex" id="wrapper">
  <div class="sidebar" id="sidebar-wrapper">
    <div class="sidebar-heading text-center py-4 fw-bold text-uppercase">Operandi</div>
    <div class="list-group list-group-flush my-3">
      <a href="#dashboard" class="list-group-item list-group-item-action fw-bold" data-section="dashboard">Dashboard</a>
      <a href="#productos" class="list-group-item list-group-item-action fw-bold" data-section="productos">Productos</a>
      <a href="#paises" class="list-group-item list-group-item-action fw-bold" data-section="paises">Países</a>
      <a href="#exportaciones" class="list-group-item list-group-item-action fw-bold" data-section="exportaciones">Exportaciones</a>
      <a href="logout.php" class="list-group-item list-group-item-action text-danger fw-bold">Cerrar Sesión</a>
    </div>
  </div>
  <div id="page-content-wrapper">
    
    <div class="container-fluid px-4">
      <div id="dashboard" class="content-section d-none">
        <h3 class="mt-4">Resumen de Operandi</h3>
        <div class="row g-4 my-4">
          <div class="col-md-4">
            <div class="card p-3 card-dashboard">
              <div class="card-body text-center">
                <h5 class="card-title">Total de Países</h5>
                <p class="card-text"><?= $total_paises ?></p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card p-3 card-dashboard">
              <div class="card-body text-center">
                <h5 class="card-title">Total de Exportaciones</h5>
                <p class="card-text"><?= $total_exportaciones ?></p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card p-3 card-dashboard">
              <div class="card-body text-center">
                <h5 class="card-title">Total de Productos</h5>
                <p class="card-text"><?= $total_productos ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    
      <div id="productos" class="content-section d-none">
        <div class="row my-4">
          <h3 class="fs-4 mb-3">Gestión de Productos</h3>
          <div class="col-md-12 mb-3">
            <div class="card p-3">
              <h5>Agregar Producto</h5>
              <form action="acciones.php" method="POST">
                <div class="mb-3">
                  <label for="nombre_producto" class="form-label">Nombre del Producto</label>
                  <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" required>
                </div>
                <button type="submit" name="guardar_producto" class="btn btn-primary">Guardar Producto</button>
              </form>
            </div>
          </div>

          <div class="col-md-12 mb-3">
            <div class="card p-3">
              <h5>Importar Productos (Excel)</h5>
              <form action="importar_excel.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tipo_tabla" value="productos">
                <div class="mb-3">
                  <label for="archivo_excel_productos" class="form-label">Subir Archivo Excel</label>
                  <input type="file" class="form-control" id="archivo_excel_productos" name="archivo_excel" accept=".xlsx, .xls" required>
                  <div class="form-text">El archivo debe tener una columna 'Nombre' en la columna A.</div>
                </div>
                <button type="submit" class="btn btn-primary">Importar Datos</button>
              </form>
            </div>
          </div>
          <div class="col-md-12">
            <div class="card p-3">
              <h5>Lista de Productos</h5>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <input type="text" id="buscarProductos" class="form-control me-2" placeholder="Buscar productos...">
                <div>
                  <a href="generar_pdf.php?tipo=productos" class="btn btn-danger btn-sm me-2" target="_blank">Descargar PDF</a>
                  <a href="generar_excel.php?tipo=productos" class="btn btn-success btn-sm">Descargar Excel</a>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-striped table-hover" id="tablaProductos">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nombre</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $result = $conexion->query("SELECT * FROM productos");
                    if ($result->num_rows > 0) {
                      while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                        echo "<td>
                                <a href='editar.php?tipo=producto&id=" . $row['id'] . "' class='btn btn-warning btn-sm me-2'>Editar</a>
                                <a href='acciones.php?eliminar_producto=" . $row['id'] . "' class='btn btn-danger btn-sm'>Eliminar</a>
                              </td>";
                        echo "</tr>";
                      }
                    } else {
                      echo "<tr><td colspan='3'>No hay productos registrados.</td></tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div id="paises" class="content-section d-none">
        <div class="row my-4">
          <h3 class="fs-4 mb-3">Gestión de Países</h3>
          <div class="col-md-12 mb-3">
            <div class="card p-3">
              <h5>Agregar País</h5>
              <form action="acciones.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                  <label for="nombre_pais" class="form-label">Nombre del País</label>
                  <input type="text" class="form-control" id="nombre_pais" name="nombre_pais" required>
                </div>
                <div class="mb-3">
                  <label for="codigo_iso" class="form-label">Código ISO (3 letras)</label>
                  <input type="text" class="form-control" id="codigo_iso" name="codigo_iso" maxlength="3" required>
                </div>
                <div class="mb-3">
                  <label for="bandera" class="form-label">Subir Bandera</label>
                  <input type="file" class="form-control" id="bandera" name="bandera" accept="image/*" required>
                </div>
                <button type="submit" name="guardar_pais" class="btn btn-primary">Guardar País</button>
              </form>
            </div>
          </div>

          <div class="col-md-12 mb-3">
            <div class="card p-3">
              <h5>Importar Países (Excel)</h5>
              <form action="importar_excel.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tipo_tabla" value="paises">
                <div class="mb-3">
                  <label for="archivo_excel_paises" class="form-label">Subir Archivo Excel</label>
                  <input type="file" class="form-control" id="archivo_excel_paises" name="archivo_excel" accept=".xlsx, .xls" required>
                  <div class="form-text">El archivo debe tener las columnas 'Nombre' y 'Codigo ISO' en las columnas A y B.</div>
                </div>
                <button type="submit" class="btn btn-primary">Importar Datos</button>
              </form>
            </div>
          </div>
          <div class="col-md-12">
            <div class="card p-3">
              <h5>Lista de Países</h5>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <input type="text" id="buscarPaises" class="form-control me-2" placeholder="Buscar países...">
                <div>
                  <a href="generar_pdf.php?tipo=paises" class="btn btn-danger btn-sm me-2" target="_blank">Descargar PDF</a>
                  <a href="generar_excel.php?tipo=paises" class="btn btn-success btn-sm">Descargar Excel</a>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-striped table-hover" id="tablaPaises">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nombre</th>
                      <th>Código ISO</th>
                      <th>Bandera</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $result = $conexion->query("SELECT * FROM paises");
                    if ($result->num_rows > 0) {
                      while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['codigo_iso']) . "</td>";
                        echo "<td><img src='" . htmlspecialchars($row['bandera']) . "' alt='Bandera de " . htmlspecialchars($row['nombre']) . "' class='flag-icon'></td>";
                        echo "<td>
                                <a href='editar.php?tipo=pais&id=" . $row['id'] . "' class='btn btn-warning btn-sm me-2'>Editar</a>
                                <a href='acciones.php?eliminar_pais=" . $row['id'] . "' class='btn btn-danger btn-sm'>Eliminar</a>
                              </td>";
                        echo "</tr>";
                      }
                    } else {
                      echo "<tr><td colspan='5'>No hay países registrados.</td></tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div id="exportaciones" class="content-section d-none">
        <div class="row my-4">
          <h3 class="fs-4 mb-3">Gestión de Exportaciones</h3>
          <div class="col-md-12 mb-3">
            <div class="card p-3">
              <h5>Registrar Nueva Exportación</h5>
              <form action="acciones.php" method="POST">
                <div class="mb-3">
                  <label for="producto_id" class="form-label">Producto</label>
                  <select class="form-control" id="producto_id" name="producto_id" required>
                    <?php
                    $productos = $conexion->query("SELECT * FROM productos");
                    while($prod = $productos->fetch_assoc()) {
                      echo "<option value='" . $prod['id'] . "'>" . htmlspecialchars($prod['nombre']) . "</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="pais_origen_id" class="form-label">País de Origen</label>
                  <select class="form-control" id="pais_origen_id" name="pais_origen_id" required>
                    <?php
                    $paises = $conexion->query("SELECT * FROM paises");
                    while($pais = $paises->fetch_assoc()) {
                      echo "<option value='" . $pais['id'] . "'>" . htmlspecialchars($pais['nombre']) . "</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="pais_destino_id" class="form-label">País de Destino</label>
                  <select class="form-control" id="pais_destino_id" name="pais_destino_id" required>
                    <?php
                    $paises->data_seek(0); // Reiniciar el puntero para este select
                    while($pais = $paises->fetch_assoc()) {
                      echo "<option value='" . $pais['id'] . "'>" . htmlspecialchars($pais['nombre']) . "</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="cantidad" class="form-label">Cantidad</label>
                  <input type="number" class="form-control" id="cantidad" name="cantidad" required>
                </div>
                <div class="mb-3">
                  <label for="fecha_exportacion" class="form-label">Fecha de Exportación</label>
                  <input type="date" class="form-control" id="fecha_exportacion" name="fecha_exportacion" required>
                </div>
                <button type="submit" name="guardar_exportacion" class="btn btn-primary">Registrar Exportación</button>
              </form>
            </div>
          </div>
          <div class="col-md-12">
            <div class="card p-3">
              <h5>Lista de Exportaciones</h5>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <input type="text" id="buscarExportaciones" class="form-control me-2" placeholder="Buscar exportaciones...">
                <div>
                  <a href="generar_pdf.php?tipo=exportaciones" class="btn btn-danger btn-sm me-2" target="_blank">Descargar PDF</a>
                  <a href="generar_excel.php?tipo=exportaciones" class="btn btn-success btn-sm">Descargar Excel</a>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-striped table-hover" id="tablaExportaciones">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Producto</th>
                      <th>País Origen</th>
                      <th>País Destino</th>
                      <th>Cantidad</th>
                      <th>Fecha</th>
                      <th>Estado</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $sql = "SELECT e.id, p.nombre AS producto, po.nombre AS pais_origen, po.bandera AS bandera_origen, pd.nombre AS pais_destino, pd.bandera AS bandera_destino, e.cantidad, e.fecha_exportacion, e.estado
                            FROM exportaciones e
                            JOIN productos p ON e.producto_id = p.id
                            JOIN paises po ON e.pais_origen_id = po.id
                            JOIN paises pd ON e.pais_destino_id = pd.id";
                    $result = $conexion->query($sql);
                    if ($result->num_rows > 0) {
                      while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['producto']) . "</td>";
                        echo "<td>
                                <img src='" . htmlspecialchars($row['bandera_origen']) . "' alt='Bandera de " . htmlspecialchars($row['pais_origen']) . "' class='flag-icon'>
                                " . htmlspecialchars($row['pais_origen']) . "
                              </td>";
                        echo "<td>
                                <img src='" . htmlspecialchars($row['bandera_destino']) . "' alt='Bandera de " . htmlspecialchars($row['pais_destino']) . "' class='flag-icon'>
                                " . htmlspecialchars($row['pais_destino']) . "
                              </td>";
                        echo "<td>" . $row['cantidad'] . "</td>";
                        echo "<td>" . $row['fecha_exportacion'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['estado']) . "</td>";
                        echo "<td>
                                <a href='editar.php?tipo=exportacion&id=" . $row['id'] . "' class='btn btn-warning btn-sm me-2'>Editar</a>
                                <a href='acciones.php?eliminar_exportacion=" . $row['id'] . "' class='btn btn-danger btn-sm'>Eliminar</a>
                              </td>";
                        echo "</tr>";
                      }
                    } else {
                      echo "<tr><td colspan='8'>No hay exportaciones registradas.</td></tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Script para manejar la visibilidad de las secciones
  document.querySelectorAll('#sidebar-wrapper .list-group-item').forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const secId = this.getAttribute('data-section');
      document.querySelectorAll('.content-section').forEach(sec => sec.classList.add('d-none'));
      document.getElementById(secId).classList.remove('d-none');
      document.querySelectorAll('#sidebar-wrapper .list-group-item').forEach(l => l.classList.remove('active'));
      this.classList.add('active');
    });
  });

  // Mostrar la sección de dashboard al cargar la página
  window.addEventListener('load', () => {
    document.querySelector('#sidebar-wrapper .list-group-item[data-section="dashboard"]').click();
  });

  // Función genérica para filtrar tablas
  function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toLowerCase();
    const table = document.getElementById(tableId);
    if (!table) return;
    const trs = table.tBodies[0].getElementsByTagName('tr');

    for (let i = 0; i < trs.length; i++) {
      const tr = trs[i];
      tr.style.display = tr.textContent.toLowerCase().includes(filter) ? '' : 'none';
    }
  }

  // Buscadores
  document.getElementById('buscarProductos')?.addEventListener('input', () => filterTable('buscarProductos', 'tablaProductos'));
  document.getElementById('buscarPaises')?.addEventListener('input', () => filterTable('buscarPaises', 'tablaPaises'));
  document.getElementById('buscarExportaciones')?.addEventListener('input', () => filterTable('buscarExportaciones', 'tablaExportaciones'));
</script>
</body>
</html>