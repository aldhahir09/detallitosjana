<?php
/**
 * Recibe el formulario de contactos.html.
 * Antes este archivo no existía: el formulario "enviaba" a una URL inexistente
 * y el JavaScript igual mostraba "¡Gracias, tu mensaje ha sido enviado!".
 */
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonRespuesta(['status' => 'error', 'message' => 'Método no permitido.'], 405);
}

// Freno básico anti-spam: un mensaje cada 30 segundos por visitante.
if (isset($_SESSION['ultimo_contacto']) && time() - $_SESSION['ultimo_contacto'] < 30) {
    jsonRespuesta(['status' => 'error', 'message' => 'Espera unos segundos antes de enviar otro mensaje.'], 429);
}

$nombre   = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$distrito = trim($_POST['distrito'] ?? '');
$asunto   = trim($_POST['asunto'] ?? '');
$mensaje  = trim($_POST['mensaje'] ?? '');

if ($nombre === '' || $telefono === '' || $distrito === '' || $mensaje === '') {
    jsonRespuesta(['status' => 'error', 'message' => 'Completa todos los campos requeridos.'], 400);
}
if (mb_strlen($nombre) > 100 || mb_strlen($telefono) > 20 || mb_strlen($distrito) > 60
    || mb_strlen($asunto) > 150 || mb_strlen($mensaje) > 2000) {
    jsonRespuesta(['status' => 'error', 'message' => 'Alguno de los campos es demasiado largo.'], 400);
}

try {
    $conexion->prepare('INSERT INTO mensajes_contacto (nombre, telefono, distrito, asunto, mensaje) VALUES (?, ?, ?, ?, ?)')
             ->execute([$nombre, $telefono, $distrito, $asunto !== '' ? $asunto : null, $mensaje]);
    $_SESSION['ultimo_contacto'] = time();
    jsonRespuesta(['status' => 'success']);
} catch (PDOException $e) {
    error_log('procesar_contacto: ' . $e->getMessage());
    jsonRespuesta(['status' => 'error', 'message' => 'No pudimos guardar tu mensaje. Intenta nuevamente.'], 500);
}
