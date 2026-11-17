<?php
session_start();
require_once __DIR__ . '/conexion.php';

function volver(string $mensaje): void {
    $_SESSION['error'] = $mensaje;
    header('Location: registro.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registro.php');
    exit();
}

$nombre   = trim($_POST['nombre'] ?? '');
$correo   = trim($_POST['correo'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

if ($nombre === '' || $correo === '' || $password === '' || $confirm === '') {
    volver('Por favor completa todos los campos del formulario.');
}
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    volver('El correo electrónico no es válido.');
}
// El formulario pide confirmar la contraseña, pero antes nunca se comprobaba.
if ($password !== $confirm) {
    volver('Las contraseñas no coinciden.');
}
if (strlen($password) < 6) {
    volver('La contraseña debe tener al menos 6 caracteres.');
}

try {
    $stmt = $conexion->prepare('SELECT id FROM usuarios WHERE correo = :correo');
    $stmt->execute([':correo' => $correo]);
    if ($stmt->fetch()) {
        volver('El correo electrónico ya está registrado. Intenta iniciar sesión.');
    }

    $conexion->prepare("INSERT INTO usuarios (nombre, correo, password, rol) VALUES (?, ?, ?, 'Cliente')")
             ->execute([$nombre, $correo, password_hash($password, PASSWORD_DEFAULT)]);

    $_SESSION['exito'] = '¡Cuenta creada exitosamente! Ya puedes iniciar sesión.';
    header('Location: login.php');
    exit();
} catch (PDOException $e) {
    error_log('validar_registro: ' . $e->getMessage());
    volver('Ocurrió un error al crear tu cuenta. Intenta nuevamente.');
}
