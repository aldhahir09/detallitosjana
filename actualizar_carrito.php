<?php
/** Suma o resta 1 a la cantidad de un ítem. Solo sobre ítems del propio usuario. */
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers.php';

if (!isset($_SESSION['id'])) {
    jsonRespuesta(['status' => 'error', 'message' => 'Debes iniciar sesión.'], 401);
}

$usuarioId = (int) $_SESSION['id'];
$carritoId = (int) ($_POST['id'] ?? 0);
$cambio    = (int) ($_POST['cambio'] ?? 0);

if ($carritoId <= 0 || !in_array($cambio, [-1, 1], true)) {
    jsonRespuesta(['status' => 'error', 'message' => 'Datos inválidos.'], 400);
}

try {
    $stmt = $conexion->prepare('SELECT cantidad FROM carrito WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$carritoId, $usuarioId]);
    $item = $stmt->fetch();

    if (!$item) {
        jsonRespuesta(['status' => 'error', 'message' => 'Ítem no encontrado.'], 404);
    }

    $nueva = (int) $item['cantidad'] + $cambio;

    if ($nueva < 1) {
        // Bajar de 1 no elimina el ítem: para quitarlo está el botón de eliminar.
        $nueva = 1;
    } elseif ($nueva > 99) {
        $nueva = 99;
    }

    $conexion->prepare('UPDATE carrito SET cantidad = ? WHERE id = ? AND usuario_id = ?')
             ->execute([$nueva, $carritoId, $usuarioId]);

    jsonRespuesta(['status' => 'success', 'cantidad' => $nueva, 'cart_count' => contarItemsCarrito($conexion, $usuarioId)]);
} catch (PDOException $e) {
    error_log('actualizar_carrito: ' . $e->getMessage());
    jsonRespuesta(['status' => 'error', 'message' => 'No se pudo actualizar el carrito.'], 500);
}
