
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detallitos JANA</title>

    <link rel="stylesheet" href="estyle.css">

    <!-- ICONOS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>

<!--======================
BARRA SUPERIOR
========================-->
<div class="top-bar">
    <i class="fa-solid fa-heart"></i>
    DETALLITOS J.A.N.A
</div>

<!--======================
HEADER
========================-->
<header>

    <div class="logo">
        <img src="img/detallitolog.png" alt="Logo Detallitos JANA">
    </div>

    <nav>
        <ul>
            <li><a href="#">Inicio</a></li>

            <!-- MENÚ DESPLEGABLE PRODUCTOS -->
            <li class="dropdown">
                <a href="#" class="dropdown-link">
                    Productos <i class="fa-solid fa-chevron-down flecha"></i>
                </a>

                <div class="dropdown-menu">
                    <div class="columna">
                        <a href="graduacion.html">Graduación <span class="badge">Temporada</span></a>
                        <a href="amor_aniversario.html">Amor y Aniversario</a>
                        <a href="para_el.html">Para Él</a>
                        <a href="ninos.html">Niños</a>
                    </div>
                    <div class="columna">
                        <a href="cumpleanos.html">Cumpleaños</a>
                        <a href="#">Matrimonio</a>
                        <a href="#">Para Ella</a>
                    </div>
                </div>
            </li>

            <li><a href="nosotros.html">Nosotros</a></li>
            <li><a href="pedidos.php">Pedidos Personalizados</a></li>
            <li><a href="contactos.html">Contacto</a></li>
        </ul>
    </nav>

    <div class="icons">
        <!-- Buscador -->
        <a href="catalogo.php" title="Buscar productos">
            <i class="fa-solid fa-magnifying-glass"></i>
        </a>

        <!-- Login -->
        <a href="login.php" title="Iniciar sesión">
            <i class="fa-regular fa-user"></i>
        </a>

        <!-- Carrito -->
        <a href="carrito.php" class="carrito" title="Carrito de compras">
            <i class="fa-solid fa-cart-shopping"></i>
            <span class="contador">0</span>
        </a>
    </div>

</header>

<!--======================
HERO
========================-->
<section class="hero">

    <div class="contenido">
        <span class="etiqueta">
            ❤ DETALLES QUE DURAN PARA SIEMPRE
        </span>

        <h1>Rosas Eternas</h1>
        <h2>Hechas para recordar</h2>

        <p>
            Regala amor que permanece.
            <br>
            Rosas naturales preservadas que duran años.
        </p>

        <div class="botones">
            <a href="#" class="btn">Comprar Ahora</a>
            <a href="catalogo.php" class="btn2">Ver Catálogos</a>
        </div>
    </div>

    <div class="imagen">
        <img src="img/jaz.jpeg" alt="Rosa Eterna">
    </div>

</section>

<!--======================
CARACTERISTICAS
========================-->
<section class="caracteristicas">

    <div>
        <i class="fa-regular fa-gem"></i>
        <h4>Rosas 100% Eternas</h4>
        <p>Preservadas para durar</p>
    </div>

    <div>
        <i class="fa-solid fa-gift"></i>
        <h4>Empaque Premium</h4>
        <p>Listo para sorprender</p>
    </div>

    <div>
        <i class="fa-solid fa-truck"></i>
        <h4>Envíos Seguros</h4>
        <p>A todo el Perú</p>
    </div>

</section>

<!--======================
FOOTER INFO
========================-->
<footer>

    <div>
        <i class="fa-solid fa-box"></i>
        <h3>Pedidos Personalizados</h3>
        <p>Creamos el detalle perfecto</p>
    </div>

    <div>
        <i class="fa-brands fa-whatsapp"></i>
        <h3>¿Necesitas ayuda?</h3>
        <p>Escríbenos por WhatsApp</p>
    </div>

    <div>
        <i class="fa-solid fa-heart"></i>
        <h3>Hecho con Amor</h3>
        <p>Cada detalle importa</p>
    </div>
<a href="logout.php" class="btn-cerrar-sesion">
    <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
</a>
</footer>

<script src="session-nav.js"></script>
<script src="img-fallback.js"></script>
</body>
</html>