<?php
/**
 * Estado de sesión en JSON. Lo usa session-nav.js para que las páginas
 * estáticas (.html) muestren el estado real de la cuenta y del carrito.
 */
session_start();
require_once __DIR__ . '/helpers.php';

$respuesta = [
    'logueado'   => isset($_SESSION['id']),
    'nombre'     => $_SESSION['nombre'] ?? null,
    'es_admin'   => esAdmin(),
    'cart_count' => 0,
];

if (isset($_SESSION['id'])) {
    try {
        require_once __DIR__ . '/conexion.php';
        $respuesta['cart_count'] = contarItemsCarrito($conexion, (int) $_SESSION['id']);
    } catch (Throwable $e) {
        error_log('session_status: ' . $e->getMessage());
    }
}

jsonRespuesta($respuesta);
