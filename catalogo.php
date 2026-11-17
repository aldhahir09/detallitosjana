<?php
session_start();
require_once __DIR__ . '/conexion.php';

// Antes: el catálogo generaba 100 productos FALSOS con JavaScript (precios
// aleatorios distintos en cada carga) y "Agregar al Carrito" solo escribía en
// localStorage, sin relación con el carrito real. Ahora muestra la tabla
// `productos` y usa el mismo carrito que el resto del sitio.
$productos = $conexion->query("SELECT * FROM productos ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos | Detallitos JANA</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    
    <style>
        /* ============================================== */
        /* ESTILOS GENERALES Y LAYOUT                     */
        /* ============================================== */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f6f9;
            color: #333;
        }

        /* Header / Navbar */
        header {
            background: linear-gradient(135deg, #9d67d9, #b57be8);
            color: white;
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-container h1 {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-nav {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 20px;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-nav:hover {
            background: rgba(255, 255, 255, 0.35);
        }

        .cart-icon-btn {
            position: relative;
            background: white;
            color: #9d67d9;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4757;
            color: white;
            font-size: 0.75rem;
            font-weight: bold;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Contenido Principal */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .hero-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .hero-title h2 {
            font-size: 2rem;
            color: #4a2875;
            margin-bottom: 8px;
        }

        .hero-title p {
            color: #666;
        }

        /* Buscador y Filtros */
        .filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 15px;
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 250px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #ccc;
            border-radius: 25px;
            outline: none;
            font-size: 0.95rem;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        /* Grilla de Productos */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 25px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .product-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background-color: #eee;
        }

        .product-info {
            padding: 15px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-category {
            font-size: 0.75rem;
            color: #9d67d9;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .product-title {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .product-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2ed573;
            margin-top: auto;
            margin-bottom: 12px;
        }

        .btn-add-cart {
            width: 100%;
            background: #9d67d9;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s;
        }

        .btn-add-cart:hover {
            background: #874ec9;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 20px;
            background: #fff;
            margin-top: 50px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <header>
        <div class="logo-container">
            <i class="fa-solid fa-gift fa-2x"></i>
            <h1>Detallitos JANA</h1>
        </div>
        <div class="nav-actions">
            <a href="index.php" class="btn-nav"><i class="fa-solid fa-house"></i> Inicio</a>
            <div class="cart-icon-btn" onclick="window.location.href='carrito.php'">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="cart-badge" id="cart-count">0</span>
            </div>
        </div>
    </header>

    <!-- CONTENIDO -->
    <div class="container">
        <div class="hero-title">
            <h2>Catálogo de Regalos y Detalles</h2>
            <p>Descubre nuestra variedad de cajas sorpresa, peluches, arreglos florales y más</p>
        </div>

        <!-- BUSCADOR -->
        <div class="filter-bar">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" onkeyup="filterProducts()" placeholder="Buscar producto por nombre...">
            </div>
        </div>

        <!-- CONTENEDOR DE PRODUCTOS -->
        <div class="products-grid" id="products-container">
            <?php if (!$productos): ?>
                <p style="grid-column:1/-1; text-align:center; color:#888;">Aún no hay productos en el catálogo.</p>
            <?php endif; ?>

            <?php foreach ($productos as $p): ?>
                <div class="product-card"
                     data-busqueda="<?= htmlspecialchars(mb_strtolower($p['nombre'] . ' ' . ($p['categoria'] ?? ''))) ?>">
                    <img src="img/<?= htmlspecialchars($p['imagen'] ?? '') ?>" class="product-img"
                         alt="<?= htmlspecialchars($p['nombre']) ?>" loading="lazy">
                    <div class="product-info">
                        <span class="product-category"><?= htmlspecialchars($p['categoria'] ?: 'Detalles') ?></span>
                        <h4 class="product-title"><?= htmlspecialchars($p['nombre']) ?></h4>
                        <div class="product-price">S/ <?= number_format((float) $p['precio'], 2) ?></div>
                        <button class="btn-add-cart" data-id="<?= (int) $p['id'] ?>"
                                data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                                onclick="addToCart(this)">
                            <i class="fa-solid fa-cart-plus"></i> Agregar al Carrito
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        © <?php echo date("Y"); ?> Detallitos JANA | Todos los derechos reservados
    </footer>

    <script>
        // Agregar al carrito REAL (agregar_carrito.php); el precio lo toma el servidor de la BD.
        function addToCart(boton) {
            const formData = new FormData();
            formData.append("producto_id", boton.dataset.id);
            formData.append("cantidad", 1);

            fetch("agregar_carrito.php", { method: "POST", body: formData })
                .then(res => {
                    if (res.status === 401) {
                        alert("Debes iniciar sesión para comprar.");
                        window.location.href = "login.php";
                        return null;
                    }
                    return res.json();
                })
                .then(data => {
                    if (!data) return;
                    if (data.status !== "success") {
                        alert(data.message || "No se pudo agregar el producto.");
                        return;
                    }
                    document.getElementById("cart-count").innerText = data.cart_count;
                    alert('¡"' + boton.dataset.nombre + '" fue agregado al carrito!');
                })
                .catch(() => alert("Hubo un problema de conexión. Intenta nuevamente."));
        }

        // Filtro de búsqueda en tiempo real
        function filterProducts() {
            const query = document.getElementById("searchInput").value.toLowerCase();
            document.querySelectorAll(".product-card").forEach(card => {
                card.style.display = card.dataset.busqueda.includes(query) ? "" : "none";
            });
        }
    </script>
    <script src="session-nav.js"></script>
    <script src="img-fallback.js"></script>
</body>
</html>