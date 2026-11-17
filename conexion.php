<?php
/**
 * Conexión única a la base de datos (PDO).
 * Todos los archivos del proyecto deben usar esta conexión: antes,
 * pedidos.php, ver_pedidos.php y usuarios.php abrían su propia
 * conexión mysqli y mezclaban su sintaxis con la de PDO.
 */
require_once __DIR__ . '/config.php';

try {
    $conexion = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // El detalle real va al log del servidor, no a la pantalla del visitante.
    error_log('Error de conexión a la BD: ' . $e->getMessage());
    http_response_code(500);
    die('No se pudo conectar con el servidor. Intenta más tarde.');
}
