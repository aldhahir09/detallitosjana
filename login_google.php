<?php
/**
 * Inicio de sesión / registro con Google.
 *
 * Antes: (1) usaba métodos de mysqli (bind_param, get_result) sobre un
 * objeto PDO -> error fatal; (2) decodificaba el JWT SIN verificar su
 * firma, de modo que cualquiera podía fabricar un token con el correo
 * de otra persona (incluido el administrador) y entrar. Ahora el token
 * se valida con el endpoint oficial de Google y se comprueba que fue
 * emitido para ESTA aplicación.
 */
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers.php';

function falla(string $mensaje): void {
    jsonRespuesta(['status' => 'error', 'message' => $mensaje]);
}

$jwt = $_POST['credential'] ?? '';
if ($jwt === '') {
    falla('Token de Google inválido.');
}

$ctx = stream_context_create(['http' => ['timeout' => 5]]);
$resp = @file_get_contents('https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($jwt), false, $ctx);
if ($resp === false) {
    falla('No se pudo verificar el token con Google. Intenta nuevamente.');
}

$payload = json_decode($resp, true);
if (!is_array($payload) || !isset($payload['email'], $payload['aud']) || $payload['aud'] !== GOOGLE_CLIENT_ID) {
    falla('Token de Google inválido o no corresponde a esta aplicación.');
}
if (!in_array($payload['email_verified'] ?? false, [true, 'true'], true)) {
    falla('Tu correo de Google no está verificado.');
}

$correo = $payload['email'];
$nombre = $payload['name'] ?? explode('@', $correo)[0];

try {
    $stmt = $conexion->prepare('SELECT id, nombre, rol FROM usuarios WHERE correo = ?');
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        // Contraseña aleatoria: la cuenta solo se usa con Google (o tras "recuperar contraseña").
        $conexion->prepare("INSERT INTO usuarios (nombre, correo, password, rol) VALUES (?, ?, ?, 'Cliente')")
                 ->execute([$nombre, $correo, password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT)]);
        $usuario = ['id' => (int) $conexion->lastInsertId(), 'nombre' => $nombre, 'rol' => 'Cliente'];
    }

    session_regenerate_id(true);
    $_SESSION['id']     = $usuario['id'];
    $_SESSION['nombre'] = $usuario['nombre'];
    $_SESSION['rol']    = $usuario['rol'];

    jsonRespuesta([
        'status'   => 'success',
        'redirect' => (strcasecmp($usuario['rol'], 'Administrador') === 0) ? 'dashboard.php' : 'index.php',
    ]);
} catch (PDOException $e) {
    error_log('login_google: ' . $e->getMessage());
    falla('No se pudo iniciar sesión con Google. Intenta nuevamente.');
}
