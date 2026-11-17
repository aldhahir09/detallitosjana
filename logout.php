<?php
// 1. Iniciar o reanudar la sesión
session_start();

// 2. Desvincular todas las variables de sesión ($_SESSION)
$_SESSION = array();

// 3. Si se desea destruir la sesión completamente, borrar también la cookie de sesión.
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

// 4. Destruir finalmente la sesión
session_destroy();

// 5. Redirigir al usuario al login
header("Location: login.php");
exit();
?>