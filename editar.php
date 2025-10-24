<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

include "conexion.php";

$tipo = $_GET['tipo'] ?? '';
$id = (int)($_GET['id'] ?? 0);
$datos = null;

if ($tipo == 'producto') {
    $query = $conexion->query("SELECT * FROM productos WHERE id=$id");
    $datos = $query->fetch_assoc();
} elseif ($tipo == 'pais') {
    $query = $conexion->query("SELECT * FROM paises WHERE id=$id");
    $datos = $query->fetch_assoc();
} elseif ($tipo == 'exportacion') {
    $query = $conexion->query("SELECT * FROM exportaciones WHERE id=$id");
    $datos = $query->fetch_assoc();
    $productos = $conexion->query("SELECT * FROM productos");
    $paises = $conexion->query("SELECT * FROM paises");
}

if (!$datos) {
    die("No se encontraron datos para editar.");
}

$titulo = "Editar " . ucfirst($tipo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title><?= $titulo ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
        background-color: #f8f9fa;
        color: #333;
        padding-top: 50px;
    }
    .card {
      background-color: #ffffff;
      border: 1px solid #dee2e6;
      color: #333;
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .form-control:focus {
      border-color: #00c3ff;
      box-shadow: 0 0 0 0.25rem rgba(0, 195, 255, 0.25);
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
    .flag-preview {
        max-width: 100px;
        height: auto;
        display: block;
        margin-bottom: 10px;
    }
  </style>
</head>
<body>
<div class="container" style="max-width: 600px;">
    <div class="card p-4">
        <h3 class="mb-4"><?= $titulo ?></h3>
        <form action="acciones.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $datos['id'] ?>">

            <?php if ($tipo == 'producto'): ?>
                <input type="hidden" name="actualizar_producto" value="1">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Producto</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($datos['nombre']) ?>" required>
                </div>
            <?php elseif ($tipo == 'pais'): ?>
                <input type="hidden" name="actualizar_pais" value="1">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del País</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($datos['nombre']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="codigo_iso" class="form-label">Código ISO (3 letras)</label>
                    <input type="text" class="form-control" id="codigo_iso" name="codigo_iso" value="<?= htmlspecialchars($datos['codigo_iso']) ?>" maxlength="3" required>
                </div>
                <div class="mb-3">
                    <label for="bandera" class="form-label">Bandera Actual</label>
                    <?php if (!empty($datos['bandera'])): ?>
                        <img src="<?= htmlspecialchars($datos['bandera']) ?>" alt="Bandera actual" class="flag-preview">
                    <?php endif; ?>
                    <label for="bandera" class="form-label">Subir nueva Bandera (opcional)</label>
                    <input type="file" class="form-control" id="bandera" name="bandera" accept="image/*">
                </div>
            <?php elseif ($tipo == 'exportacion'): ?>
                <input type="hidden" name="actualizar_exportacion" value="1">
                <div class="mb-3">
                    <label for="producto_id" class="form-label">Producto</label>
                    <select class="form-control" id="producto_id" name="producto_id" required>
                        <?php while($prod = $productos->fetch_assoc()): ?>
                            <option value="<?= $prod['id'] ?>" <?= ($datos['producto_id'] == $prod['id']) ? 'selected' : '' ?>><?= htmlspecialchars($prod['nombre']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="pais_origen_id" class="form-label">País de Origen</label>
                    <select class="form-control" id="pais_origen_id" name="pais_origen_id" required>
                        <?php while($pais = $paises->fetch_assoc()): ?>
                            <option value="<?= $pais['id'] ?>" <?= ($datos['pais_origen_id'] == $pais['id']) ? 'selected' : '' ?>><?= htmlspecialchars($pais['nombre']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <?php $paises->data_seek(0); // Reiniciar puntero para el siguiente select ?>
                <div class="mb-3">
                    <label for="pais_destino_id" class="form-label">País de Destino</label>
                    <select class="form-control" id="pais_destino_id" name="pais_destino_id" required>
                        <?php while($pais = $paises->fetch_assoc()): ?>
                            <option value="<?= $pais['id'] ?>" <?= ($datos['pais_destino_id'] == $pais['id']) ? 'selected' : '' ?>><?= htmlspecialchars($pais['nombre']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="cantidad" class="form-label">Cantidad</label>
                    <input type="number" class="form-control" id="cantidad" name="cantidad" value="<?= $datos['cantidad'] ?>" required>
                </div>
                <div class="mb-3">
                    <label for="fecha_exportacion" class="form-label">Fecha de Exportación</label>
                    <input type="date" class="form-control" id="fecha_exportacion" name="fecha_exportacion" value="<?= $datos['fecha_exportacion'] ?>" required>
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-control" id="estado" name="estado" required>
                        <option value="Pendiente" <?= ($datos['estado'] == 'Pendiente') ? 'selected' : '' ?>>Pendiente</option>
                        <option value="En tránsito" <?= ($datos['estado'] == 'En tránsito') ? 'selected' : '' ?>>En tránsito</option>
                        <option value="Entregado" <?= ($datos['estado'] == 'Entregado') ? 'selected' : '' ?>>Entregado</option>
                        <option value="Cancelado" <?= ($datos['estado'] == 'Cancelado') ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                </div>
            <?php endif; ?>
            
            <div class="d-flex justify-content-between">
              <button type="submit" class="btn btn-primary">Guardar Cambios</button>
              <a href="index.php#<?= $tipo ?>s" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>