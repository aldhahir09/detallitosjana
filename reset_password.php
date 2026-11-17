<?php
/** Pantalla para fijar una nueva contraseña a partir de un token válido. */
session_start();
require_once __DIR__ . '/conexion.php';

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$mensaje = '';
$reset = null;

if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
    $mensaje = 'Enlace de recuperación inválido.';
} else {
    try {
        $stmt = $conexion->prepare('SELECT usuario_id FROM password_resets WHERE token = ? AND usado = 0 AND expira > NOW()');
        $stmt->execute([$token]);
        $reset = $stmt->fetch();
        if (!$reset) {
            $mensaje = 'Este enlace ya expiró o no es válido. Solicita uno nuevo.';
        }
    } catch (PDOException $e) {
        error_log('reset_password: ' . $e->getMessage());
        $mensaje = 'Ocurrió un error al validar el enlace.';
    }
}

if ($reset && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 6) {
        $mensaje = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($password !== $confirm) {
        $mensaje = 'Las contraseñas no coinciden.';
    } else {
        try {
            $conexion->beginTransaction();
            $conexion->prepare('UPDATE usuarios SET password = ? WHERE id = ?')
                     ->execute([password_hash($password, PASSWORD_DEFAULT), $reset['usuario_id']]);
            $conexion->prepare('UPDATE password_resets SET usado = 1 WHERE token = ?')->execute([$token]);
            $conexion->commit();

            $_SESSION['exito'] = 'Tu contraseña se actualizó correctamente. Ya puedes iniciar sesión.';
            header('Location: login.php');
            exit();
        } catch (PDOException $e) {
            if ($conexion->inTransaction()) { $conexion->rollBack(); }
            error_log('reset_password: ' . $e->getMessage());
            $mensaje = 'No se pudo actualizar la contraseña. Intenta nuevamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña | Detallitos JANA</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
<div class="contenedor">
    <div class="login-box">
        <img src="img/detallitolog.png" class="logo" alt="Detallitos JANA">
        <h2>Nueva Contraseña</h2>

        <?php if ($mensaje): ?>
            <div class="error"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <?php if ($reset): ?>
            <form action="reset_password.php" method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <div class="grupo">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Nueva contraseña" required minlength="6">
                </div>
                <div class="grupo">
                    <i class="fa-solid fa-shield-halved"></i>
                    <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required minlength="6">
                </div>
                <button type="submit"><i class="fa-solid fa-check"></i> Guardar nueva contraseña</button>
            </form>
        <?php else: ?>
            <div class="links"><a href="recuperar.php">Solicitar un nuevo enlace</a></div>
        <?php endif; ?>

        <div class="links"><a href="login.php">Volver a iniciar sesión</a></div>
    </div>
</div>
</body>
</html>
