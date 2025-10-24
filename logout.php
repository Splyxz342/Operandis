<?php
session_start();

// Elimina todas las variables de sesión
$_SESSION = array();

// Si se usan cookies de sesión, las eliminamos también
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000, 
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}

// Destruye la sesión
session_destroy();

// Redirige al login
header("Location: login.php");
exit;
?>
