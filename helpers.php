<?php
/** Funciones auxiliares compartidas. */

function esAdmin(): bool {
    $rol = $_SESSION['rol'] ?? '';
    return strcasecmp($rol, 'Administrador') === 0 || strcasecmp($rol, 'Admin') === 0;
}

function requiereLogin(string $mensaje = 'Debes iniciar sesión para continuar.'): void {
    if (!isset($_SESSION['id'])) {
        $_SESSION['mensaje'] = $mensaje;
        header('Location: login.php');
        exit();
    }
}

function requiereAdmin(): void {
    requiereLogin('Debes iniciar sesión para acceder a esta sección.');
    if (!esAdmin()) {
        header('Location: index.php');
        exit();
    }
}

function contarItemsCarrito(PDO $conexion, int $usuarioId): int {
    $stmt = $conexion->prepare('SELECT COALESCE(SUM(cantidad),0) FROM carrito WHERE usuario_id = ?');
    $stmt->execute([$usuarioId]);
    return (int) $stmt->fetchColumn();
}

/** Respuesta JSON estándar para los endpoints AJAX. */
function jsonRespuesta(array $datos, int $codigoHttp = 200): void {
    http_response_code($codigoHttp);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($datos);
    exit();
}
