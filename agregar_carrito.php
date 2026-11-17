<?php
/**
 * Agrega un producto al carrito del usuario logueado.
 *
 * Antes este archivo nunca insertaba nada en la BD y comprobaba una
 * clave de sesión ($_SESSION['usuario_id']) que el login no define.
 *
 *  - GET  ?id=N  -> botón "Comprar" de productos.php / detalle_producto.php
 *                   (producto de la tabla `productos`). Redirige a carrito.php.
 *  - POST        -> páginas de categoría y catálogo (AJAX). Responde JSON.
 */
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers.php';

$esAjax = ($_SERVER['REQUEST_METHOD'] === 'POST');

if (!isset($_SESSION['id'])) {
    if ($esAjax) {
        jsonRespuesta(['status' => 'error', 'message' => 'Debes iniciar sesión para agregar productos al carrito.'], 401);
    }
    $_SESSION['mensaje'] = 'Debes iniciar sesión para comprar.';
    header('Location: login.php');
    exit();
}

if (esAdmin()) {
    if ($esAjax) {
        jsonRespuesta(['status' => 'error', 'message' => 'El administrador no realiza compras desde el catálogo.'], 403);
    }
    header('Location: dashboard.php');
    exit();
}

$usuarioId = (int) $_SESSION['id'];

try {
    if ($esAjax) {
        $productoId = (isset($_POST['producto_id']) && $_POST['producto_id'] !== '') ? (int) $_POST['producto_id'] : null;
        $nombre     = trim($_POST['nombre'] ?? '');
        $precio     = isset($_POST['precio']) ? (float) $_POST['precio'] : 0.0;
        $imagen     = trim($_POST['imagen'] ?? '') ?: null;
        $cantidad   = max(1, min(99, (int) ($_POST['cantidad'] ?? 1)));

        // Dos orígenes distintos:
        //  - catalogo.php envía SOLO producto_id -> nombre y precio se toman de la BD
        //    (así nadie puede manipular el precio desde el navegador).
        //  - Las páginas de categoría (ninos.html, etc.) envían nombre y precio porque
        //    sus productos están escritos en el HTML y NO existen en la tabla `productos`.
        //    Sus ids (1-112) chocan con los de la BD, por eso aquí no se consulta la BD.
        $real = false;
        if ($nombre === '' && $productoId !== null) {
            $stmt = $conexion->prepare('SELECT nombre, precio, imagen FROM productos WHERE id = ?');
            $stmt->execute([$productoId]);
            $real = $stmt->fetch();
            if (!$real) {
                jsonRespuesta(['status' => 'error', 'message' => 'El producto no existe.'], 404);
            }
            $nombre = $real['nombre'];
            $precio = (float) $real['precio'];
            $imagen = $real['imagen'];
        }

        if ($nombre === '' || mb_strlen($nombre) > 180 || $precio <= 0 || $precio > 100000) {
            jsonRespuesta(['status' => 'error', 'message' => 'Datos de producto inválidos.'], 400);
        }
    } else {
        $productoId = (int) ($_GET['id'] ?? 0);
        if ($productoId <= 0) {
            header('Location: productos.php');
            exit();
        }
        $stmt = $conexion->prepare('SELECT nombre, precio, imagen FROM productos WHERE id = ?');
        $stmt->execute([$productoId]);
        $real = $stmt->fetch();
        if (!$real) {
            $_SESSION['mensaje'] = 'El producto solicitado no existe.';
            header('Location: productos.php');
            exit();
        }
        $nombre   = $real['nombre'];
        $precio   = (float) $real['precio'];
        $imagen   = $real['imagen'];
        $cantidad = 1;
    }

    // ¿Ya está en el carrito? Entonces se suma la cantidad.
    if ($productoId !== null && isset($real) && $real) {
        $stmt = $conexion->prepare('SELECT id FROM carrito WHERE usuario_id = ? AND producto_id = ?');
        $stmt->execute([$usuarioId, $productoId]);
    } else {
        $productoId = null; // producto de categoría sin fila propia en `productos`
        $stmt = $conexion->prepare('SELECT id FROM carrito WHERE usuario_id = ? AND producto_id IS NULL AND nombre_producto = ? AND precio_unitario = ?');
        $stmt->execute([$usuarioId, $nombre, $precio]);
    }
    $existente = $stmt->fetch();

    if ($existente) {
        $conexion->prepare('UPDATE carrito SET cantidad = LEAST(99, cantidad + ?) WHERE id = ?')
                 ->execute([$cantidad, $existente['id']]);
    } else {
        $conexion->prepare('INSERT INTO carrito (usuario_id, producto_id, nombre_producto, precio_unitario, imagen, cantidad) VALUES (?, ?, ?, ?, ?, ?)')
                 ->execute([$usuarioId, $productoId, $nombre, $precio, $imagen, $cantidad]);
    }

    if ($esAjax) {
        jsonRespuesta([
            'status'     => 'success',
            'message'    => '"' . $nombre . '" se agregó al carrito.',
            'cart_count' => contarItemsCarrito($conexion, $usuarioId),
        ]);
    }
    header('Location: carrito.php');
    exit();

} catch (PDOException $e) {
    error_log('agregar_carrito: ' . $e->getMessage());
    if ($esAjax) {
        jsonRespuesta(['status' => 'error', 'message' => 'No se pudo agregar el producto al carrito.'], 500);
    }
    $_SESSION['mensaje'] = 'No se pudo agregar el producto al carrito.';
    header('Location: productos.php');
    exit();
}
