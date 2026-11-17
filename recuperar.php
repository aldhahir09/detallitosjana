<?php
/**
 * "Olvidé mi contraseña".
 * Antes este archivo era una copia de registro.php (un formulario de
 * "Crear cuenta") y no recuperaba nada.
 *
 * Flujo: el usuario escribe su correo -> si existe se genera un token de
 * un solo uso (1 hora) -> el enlace se ENVÍA POR CORREO. El enlace nunca
 * se muestra a quien hace la petición (si no, cualquiera podría restablecer
 * la contraseña de otra persona solo conociendo su correo). La única
 * excepción es el modo desarrollo en localhost, donde no hay correo.
 */
session_start();
require_once __DIR__ . '/conexion.php';

$mensaje = '';
$enlaceDev = '';
$esLocal = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'Por favor ingresa un correo electrónico válido.';
    } else {
        // Mismo mensaje exista o no el correo, para no revelar qué correos están registrados.
        $mensaje = 'Si el correo está registrado, te enviamos un enlace para restablecer tu contraseña.';

        try {
            $stmt = $conexion->prepare('SELECT id FROM usuarios WHERE correo = ?');
            $stmt->execute([$correo]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                $token = bin2hex(random_bytes(32));
                $conexion->prepare('UPDATE password_resets SET usado = 1 WHERE usuario_id = ? AND usado = 0')
                         ->execute([$usuario['id']]);
                $conexion->prepare('INSERT INTO password_resets (usuario_id, token, expira) VALUES (?, ?, ?)')
                         ->execute([$usuario['id'], $token, date('Y-m-d H:i:s', time() + 3600)]);

                $base = BASE_URL !== '' ? BASE_URL : 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
                $ruta = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
                $enlace = $base . $ruta . '/reset_password.php?token=' . $token;

                $enviado = @mail(
                    $correo,
                    'Restablece tu contraseña - Detallitos JANA',
                    "Hola,\n\nPara crear una nueva contraseña entra a este enlace (vale 1 hora):\n$enlace\n\nSi no lo pediste, ignora este mensaje.",
                    'From: ' . CORREO_REMITENTE . "\r\nContent-Type: text/plain; charset=UTF-8"
                );

                if (!$enviado && MODO_DESARROLLO && $esLocal) {
                    $enlaceDev = $enlace;
                } elseif (!$enviado) {
                    error_log('recuperar: no se pudo enviar el correo de recuperación (revisa la configuración de mail()).');
                }
            }
        } catch (PDOException $e) {
            error_log('recuperar: ' . $e->getMessage());
            $mensaje = 'Ocurrió un error. Intenta nuevamente más tarde.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña | Detallitos JANA</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
<div class="contenedor">
    <div class="login-box">
        <img src="img/detallitolog.png" class="logo" alt="Detallitos JANA">
        <h2>Recuperar Contraseña</h2>
        <p>Ingresa tu correo y te ayudaremos a restablecerla</p>

        <?php if ($mensaje): ?>
            <div class="error" style="background:#d4edda; color:#155724;"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <?php if ($enlaceDev): ?>
            <div class="error" style="background:#fff3cd; color:#856404; word-break:break-all;">
                <strong>Modo desarrollo (solo localhost):</strong> el servidor no tiene correo configurado, usa este enlace:<br>
                <a href="<?= htmlspecialchars($enlaceDev) ?>"><?= htmlspecialchars($enlaceDev) ?></a>
            </div>
        <?php endif; ?>

        <form action="recuperar.php" method="POST">
            <div class="grupo">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="correo" placeholder="Correo electrónico" required>
            </div>
            <button type="submit"><i class="fa-solid fa-paper-plane"></i> Enviar enlace de recuperación</button>
        </form>

        <div class="links"><a href="login.php">Volver a iniciar sesión</a></div>
    </div>
</div>
</body>
</html>
