<?php
/** Elimina un ítem del carrito. Solo ítems del propio usuario. */
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers.php';

if (!isset($_SESSION['id'])) {
    jsonRespuesta(['status' => 'error', 'message' => 'Debes iniciar sesión.'], 401);
}

$usuarioId = (int) $_SESSION['id'];
$carritoId = (int) ($_POST['id'] ?? 0);

if ($carritoId <= 0) {
    jsonRespuesta(['status' => 'error', 'message' => 'Datos inválidos.'], 400);
}

try {
    $conexion->prepare('DELETE FROM carrito WHERE id = ? AND usuario_id = ?')->execute([$carritoId, $usuarioId]);
    jsonRespuesta(['status' => 'success', 'cart_count' => contarItemsCarrito($conexion, $usuarioId)]);
} catch (PDOException $e) {
    error_log('eliminar_carrito: ' . $e->getMessage());
    jsonRespuesta(['status' => 'error', 'message' => 'No se pudo eliminar el producto.'], 500);
}
