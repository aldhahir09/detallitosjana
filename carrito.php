<?php
session_start();
require_once "conexion.php";

// Si el usuario no ha iniciado sesión, redirigir al login
if (!isset($_SESSION['id'])) {
    $_SESSION['mensaje'] = "Debes iniciar sesión para ver tu carrito.";
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['id'];
$items_carrito = [];
$subtotal = 0;

// El carrito guarda su propio nombre/precio/imagen: antes un JOIN con
// `productos` hacía desaparecer los artículos de las páginas de categoría.
try {
    $stmt = $conexion->prepare("
        SELECT id AS carrito_id, cantidad, producto_id,
               nombre_producto AS nombre, precio_unitario AS precio, imagen
        FROM carrito
        WHERE usuario_id = ?
        ORDER BY fecha_agregado DESC
    ");
    $stmt->execute([$usuario_id]);
    $items_carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('carrito.php: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito | Detallitos JANA</title>

    <link rel="stylesheet" href="carrito.css">
    <!-- ICONOS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>

<!--======================
BARRA SUPERIOR & HEADER
========================-->
<div class="top-bar">
    <i class="fa-solid fa-heart"></i> Envíos a todo el país
</div>

<header>
    <div class="logo">
        <a href="index.php"><img src="img/detallitolog.png" alt="Logo Detallitos JANA"></a>
    </div>

    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="graduacion.html">Graduación</a></li>
            <li><a href="amor_aniversario.html">Amor</a></li>
            <li><a href="para_el.html">Para Él</a></li>
            <li><a href="ninos.html">Niños</a></li>
            <li><a href="pedidos.php">Pedidos Personalizados</a></li>
        </ul>
    </nav>

    <div class="icons">
        <a href="index.php" title="Seguir comprando"><i class="fa-solid fa-store"></i></a>
        <a href="logout.php" title="Cerrar sesión" style="color: #ef4444;"><i class="fa-solid fa-right-from-bracket"></i></a>
    </div>
</header>

<!--======================
CONTENIDO DEL CARRITO
========================-->
<main class="contenedor-carrito">
    <h2><i class="fa-solid fa-cart-shopping"></i> Tu Carrito de Compras</h2>

    <?php if (empty($items_carrito)): ?>
        <div class="carrito-vacio">
            <i class="fa-solid fa-basket-shopping"></i>
            <h3>Tu carrito está vacío</h3>
            <p>¡Explora nuestras colecciones y añade tus detalles favoritos!</p>
            <a href="index.php" class="btn-volver">Ver Catálogos</a>
        </div>
    <?php else: ?>
        <div class="grid-carrito">
            <!-- Tabla / Lista de Productos -->
            <div class="tabla-productos">
                <?php foreach ($items_carrito as $item): 
                    $total_item = $item['precio'] * $item['cantidad'];
                    $subtotal += $total_item;
                ?>
                    <div class="item-carrito" data-id="<?= $item['carrito_id']; ?>">
                        <?php
                        $img = $item['imagen'] ?? '';
                        if ($img === '') { $srcImagen = 'img/detallitolog.png'; }
                        elseif (preg_match('#^https?://#i', $img) || strpos($img, 'img/') === 0) { $srcImagen = $img; }
                        else { $srcImagen = 'img/' . $img; }
                        ?>
                        <img src="<?= htmlspecialchars($srcImagen); ?>" alt="<?= htmlspecialchars($item['nombre']); ?>">
                        
                        <div class="info-item">
                            <h4><?= htmlspecialchars($item['nombre']); ?></h4>
                            <span class="precio-unitario">S/ <?= number_format($item['precio'], 2); ?> c/u</span>
                        </div>

                        <div class="control-cantidad">
                            <button onclick="actualizarCantidad(<?= $item['carrito_id']; ?>, -1)">-</button>
                            <span><?= $item['cantidad']; ?></span>
                            <button onclick="actualizarCantidad(<?= $item['carrito_id']; ?>, 1)">+</button>
                        </div>

                        <div class="subtotal-item">
                            S/ <?= number_format($total_item, 2); ?>
                        </div>

                        <button class="btn-eliminar" onclick="eliminarDelCarrito(<?= $item['carrito_id']; ?>)" title="Eliminar producto">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Resumen de Pedido -->
            <div class="resumen-pedido">
                <h3>Resumen de Compra</h3>
                <hr>
                <div class="fila-resumen">
                    <span>Subtotal:</span>
                    <strong>S/ <?= number_format($subtotal, 2); ?></strong>
                </div>
                <div class="fila-resumen">
                    <span>Envío (A coordinar):</span>
                    <span style="color: #22c55e; font-weight: 600;">Gratis / Shalom / Delivery</span>
                </div>
                <hr>
                <div class="fila-resumen total">
                    <span>Total a Pagar:</span>
                    <strong>S/ <?= number_format($subtotal, 2); ?></strong>
                </div>

                <button class="btn-checkout" onclick="procesarCompra(<?= $subtotal; ?>)">
                    <i class="fa-brands fa-whatsapp"></i> Finalizar Pedido por WhatsApp
                </button>
                <a href="index.php" class="btn-seguir">Seguir comprando</a>
            </div>
        </div>
    <?php endif; ?>
</main>

<!--======================
FOOTER
========================-->
<footer>
    <div>
        <i class="fa-solid fa-shield-halved"></i>
        <h3>Compra Segura</h3>
        <p>Garantía en todos tus productos</p>
    </div>
    <div>
        <i class="fa-brands fa-whatsapp"></i>
        <h3>Atención Inmediata</h3>
        <p>Confirmación al instante</p>
    </div>
</footer>

<script src="carrito.js"></script>
<script src="session-nav.js"></script>
<script src="img-fallback.js"></script>
</body>
</html>