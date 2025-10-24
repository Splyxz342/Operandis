<?php
session_start();
include 'conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($usuario) || empty($password)) {
        $mensaje = "Por favor, completa todos los campos.";
    } else {
        $stmt = $conexion->prepare("SELECT id, password_hash FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password_hash'])) {
                $_SESSION['usuario_id'] = $row['id'];
                $_SESSION['usuario'] = $usuario;
                header("Location: index.php");
                exit;
            } else {
                $mensaje = "Contraseña incorrecta.";
            }
        } else {
            $mensaje = "Usuario no encontrado.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión - Operandi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
        background-color: #0B1E2D; /* Color de fondo principal */
    }
    .card {
        background-color: #333; /* Fondo de la tarjeta */
        border: none;
        color: white;
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
    .btn-success {
        background-color: #00c3ff;
        border: none;
        color: #0B1E2D;
        font-weight: bold;
    }
    .btn-success:hover {
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
  </style>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card p-4 shadow-lg" style="width: 100%; max-width: 400px;">
    <div class="card-body">
      <h3 class="card-title text-center mb-4">Iniciar Sesión</h3>
      <?php if (!empty($mensaje)): ?>
        <div class="alert alert-warning text-center"><?= htmlspecialchars($mensaje) ?></div>
      <?php endif; ?>
      <form method="POST" action="login.php">
        <div class="mb-3">
          <label for="usuario" class="form-label">Usuario</label>
          <input type="text" class="form-control" id="usuario" name="usuario" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Contraseña</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-success">Ingresar</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>