<?php
session_start();
require_once __DIR__ . '/conexion.php';

$productos = $conexion->query("SELECT * FROM productos ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos | Detallitos JANA</title>
    <link rel="stylesheet" href="productos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>

<section class="productos">

    <p style="text-align:center; margin-bottom:10px;">
        <a href="index.php"><i class="fa-solid fa-arrow-left"></i> Volver al inicio</a> &nbsp;|&nbsp;
        <a href="carrito.php"><i class="fa-solid fa-cart-shopping"></i> Mi carrito</a>
    </p>

    <h2>Nuestros Productos</h2>

    <div class="grid">
        <?php if (!$productos): ?>
            <p style="text-align:center; width:100%;">Aún no hay productos registrados.</p>
        <?php endif; ?>

        <?php foreach ($productos as $p): ?>
            <div class="card">
                <img src="img/<?= htmlspecialchars($p['imagen'] ?? '') ?>" alt="<?= htmlspecialchars($p['nombre']) ?>">
                <h3><?= htmlspecialchars($p['nombre']) ?></h3>
                <p class="precio">S/. <?= number_format((float) $p['precio'], 2) ?></p>

                <a href="detalle_producto.php?id=<?= (int) $p['id'] ?>" class="detalle">Ver detalle</a>

                <a href="agregar_carrito.php?id=<?= (int) $p['id'] ?>" class="comprar">
                    <i class="fa-solid fa-cart-shopping"></i> Comprar
                </a>
            </div>
        <?php endforeach; ?>
    </div>

</section>

<script src="img-fallback.js"></script>
</body>
</html>
