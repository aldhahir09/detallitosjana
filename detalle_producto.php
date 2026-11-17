<?php
session_start();
require_once __DIR__ . '/conexion.php';

$id = (int) ($_GET['id'] ?? 0);

$producto = false;
if ($id > 0) {
    $stmt = $conexion->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch();
}

// Antes, con un id inexistente la página seguía y mostraba errores de "null".
if (!$producto) {
    http_response_code(404);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $producto ? htmlspecialchars($producto['nombre']) : 'Producto no encontrado' ?> | Detallitos JANA</title>
    <link rel="stylesheet" href="productos.css">
</head>
<body>

<?php if (!$producto): ?>
    <div class="detalle" style="display:block; text-align:center;">
        <h1>Producto no encontrado</h1>
        <p>El producto que buscas no existe o fue eliminado.</p>
        <a href="productos.php" class="comprar">Ver todos los productos</a>
    </div>
<?php else: ?>
    <div class="detalle">
        <img src="img/<?= htmlspecialchars($producto['imagen'] ?? '') ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>">

        <div>
            <h1><?= htmlspecialchars($producto['nombre']) ?></h1>
            <h2>S/. <?= number_format((float) $producto['precio'], 2) ?></h2>
            <p><?= nl2br(htmlspecialchars($producto['descripcion'] ?? '')) ?></p>

            <a href="agregar_carrito.php?id=<?= (int) $producto['id'] ?>" class="comprar">Comprar</a>
            <p style="margin-top:15px;"><a href="productos.php">&larr; Volver a productos</a></p>
        </div>
    </div>
<?php endif; ?>

<script src="img-fallback.js"></script>
</body>
</html>
