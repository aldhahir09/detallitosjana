<?php
session_start();
require_once __DIR__ . '/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

$correo   = trim($_POST['correo'] ?? '');
$password = $_POST['password'] ?? '';

if ($correo === '' || $password === '') {
    $_SESSION['error'] = 'Por favor completa todos los campos.';
    header('Location: login.php');
    exit();
}

try {
    $stmt = $conexion->prepare('SELECT * FROM usuarios WHERE correo = :correo');
    $stmt->execute([':correo' => $correo]);
    $usuario = $stmt->fetch();

    $valido = false;
    if ($usuario) {
        if (password_verify($password, $usuario['password'])) {
            $valido = true;
            if (password_needs_rehash($usuario['password'], PASSWORD_DEFAULT)) {
                $conexion->prepare('UPDATE usuarios SET password = ? WHERE id = ?')
                         ->execute([password_hash($password, PASSWORD_DEFAULT), $usuario['id']]);
            }
        } elseif (hash_equals((string) $usuario['password'], $password)) {
            // Contraseña antigua guardada en texto plano: se acepta y se migra a hash.
            $valido = true;
            $conexion->prepare('UPDATE usuarios SET password = ? WHERE id = ?')
                     ->execute([password_hash($password, PASSWORD_DEFAULT), $usuario['id']]);
        }
    }

    if (!$valido) {
        $_SESSION['error'] = 'Correo o contraseña incorrectos.';
        header('Location: login.php');
        exit();
    }

    session_regenerate_id(true);
    $_SESSION['id']     = $usuario['id'];
    $_SESSION['nombre'] = $usuario['nombre'];
    $_SESSION['rol']    = $usuario['rol'];

    header('Location: ' . (in_array(strtolower($usuario['rol']), ['administrador', 'admin'], true) ? 'dashboard.php' : 'index.php'));
    exit();

} catch (PDOException $e) {
    error_log('validar_login: ' . $e->getMessage());
    $_SESSION['error'] = 'Ocurrió un error al iniciar sesión. Intenta nuevamente.';
    header('Location: login.php');
    exit();
}
