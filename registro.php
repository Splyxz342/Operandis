<?php
include 'conexion.php';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $nombre_completo = trim($_POST['nombre_completo'] ?? '');

    if (empty($usuario) || empty($password) || empty($nombre_completo)) {
        $mensaje = "Por favor, completa todos los campos.";
    } else {
        // Verificar si el usuario ya existe
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ?");
        if ($stmt) {
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $mensaje = "El nombre de usuario ya está en uso.";
            } else {
                $stmt->close();
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conexion->prepare("INSERT INTO usuarios (usuario, password_hash, nombre_completo) VALUES (?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("sss", $usuario, $password_hash, $nombre_completo);
                    if ($stmt->execute()) {
                        header("Location: login.php");
                        exit;
                    } else {
                        $mensaje = "Error al registrar el usuario.";
                    }
                    $stmt->close();
                } else {
                    $mensaje = "Error en la preparación de la consulta: " . $conexion->error;
                }
            }
        } else {
            $mensaje = "Error en la preparación de la consulta: " . $conexion->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro - Operandi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
        background-color: #0B1E2D;
        color: white;
    }
    .card {
        background-color: #333;
        border: none;
    }
    .form-control {
        background-color: #0B1E2D;
        border: 1px solid #00c3ff;
        color: white;
    }
    .form-control:focus {
        background-color: #0B1E2D;
        border-color: #00c3ff;
        box-shadow: 0 0 0 0.25rem rgba(0, 195, 255, 0.25);
        color: white;
    }
    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }
    .btn-primary {
        background-color: #00c3ff;
        border: none;
        color: #0B1E2D;
        font-weight: bold;
    }
    .btn-primary:hover {
        background-color: #0099cc;
    }
    .alert-warning {
        background-color: #00c3ff;
        color: #0B1E2D;
        border: none;
    }
    .card-title {
        color: #00c3ff;
    }
    .form-label {
        color: #00c3ff;
    }
  </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">

<div class="container" style="max-width: 480px;">
  <div class="card shadow-sm p-4">
    <div class="card-body">
      <h3 class="card-title text-center mb-4">Crear cuenta</h3>

      <?php if (!empty($mensaje)): ?>
        <div class="alert alert-warning"><?= htmlspecialchars($mensaje) ?></div>
      <?php endif; ?>

      <form method="POST" action="registro.php">
        <div class="mb-3">
          <label for="nombre_completo" class="form-label">Nombre completo</label>
          <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" required>
        </div>
        <div class="mb-3">
          <label for="usuario" class="form-label">Usuario</label>
          <input type="text" class="form-control" id="usuario" name="usuario" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Contraseña</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Registrar</button>
        </div>
      </form>
      <div class="text-center mt-3">
        <a href="login.php" class="text-decoration-none" style="color:#00c3ff;">¿Ya tienes una cuenta? Inicia sesión aquí.</a>
      </div>
    </div>
  </div>
</div>

</body>
</html>