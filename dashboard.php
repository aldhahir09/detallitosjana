<?php
session_start();

// 1. Validar que el usuario haya iniciado sesión
if (!isset($_SESSION['id']) && !isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Normalizar variables de sesión (soporta 'id' o 'usuario_id')
$id_usuario     = $_SESSION['id'] ?? $_SESSION['usuario_id'];
$nombre_usuario = $_SESSION['nombre'] ?? $_SESSION['usuario_nombre'] ?? 'Usuario';
$rol_usuario    = $_SESSION['rol'] ?? 'Cliente';

// Definir si el usuario actual es Administrador
$es_admin = (strcasecmp($rol_usuario, 'Administrador') === 0 || strcasecmp($rol_usuario, 'Admin') === 0);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $es_admin ? 'Panel Administrador' : 'Mi Cuenta'; ?> | Detallitos JANA</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f6f9;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* HEADER & NAV */
        header {
            background: linear-gradient(135deg, #b57be8 0%, #9d67d9 100%);
            color: white;
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: white;
        }

        .logo-brand i {
            font-size: 26px;
        }

        .logo-brand h2 {
            font-size: 22px;
            font-weight: 700;
        }

        .user-nav {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .usuario-info {
            text-align: right;
        }

        .usuario-info strong {
            display: block;
            font-size: 15px;
        }

        .usuario-info span {
            font-size: 12px;
            background: rgba(255, 255, 255, 0.25);
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .logout {
            background: #dc3545;
            padding: 9px 18px;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .logout:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
        }

        /* BANNER BIENVENIDA */
        .welcome-banner {
            background: white;
            padding: 25px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border-left: 5px solid #9d67d9;
        }

        .welcome-banner h1 {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .welcome-banner p {
            color: #6c757d;
            font-size: 14px;
        }

        /* CONTAINER & CARDS GRID */
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 30px auto;
            flex-grow: 1;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #f0f0f0;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(157, 103, 217, 0.18);
            border-color: #b57be8;
        }

        .card-icon {
            width: 70px;
            height: 70px;
            background: #f3ebfc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .card-icon i {
            font-size: 32px;
            color: #9d67d9;
        }

        .card h3 {
            margin-bottom: 10px;
            color: #2f3542;
            font-size: 18px;
        }

        .card p {
            color: #747d8c;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .card a {
            display: inline-block;
            width: 100%;
            text-decoration: none;
            color: white;
            background: #b57be8;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .card a:hover {
            background: #9d67d9;
            box-shadow: 0 4px 12px rgba(157, 103, 217, 0.4);
        }

        /* FOOTER */
        footer {
            background: white;
            margin-top: 50px;
            text-align: center;
            color: #747d8c;
            padding: 20px;
            font-size: 14px;
            border-top: 1px solid #e1e1e1;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .user-nav {
                flex-direction: column;
                gap: 12px;
            }

            .usuario-info {
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <!-- HEADER DE NAVEGACIÓN -->
    <header>
        <a href="index.php" class="logo-brand">
            <i class="fa-solid fa-gift"></i>
            <h2>Detallitos JANA</h2>
        </a>

        <div class="user-nav">
            <div class="usuario">
                <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong>
                <span><?php echo $es_admin ? 'Administrador' : 'Cliente'; ?></span>
            </div>

            <a class="logout" href="logout.php">
                <i class="fa-solid fa-right-from-bracket"></i> Salir
            </a>
        </div>
    </header>

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="container">

        <!-- MENSAJE DE BIENVENIDA -->
        <div class="welcome-banner">
            <h1>¡Hola, <?php echo htmlspecialchars($nombre_usuario); ?>! 👋</h1>
            <p>
                <?php if ($es_admin): ?>
                    Bienvenido al panel de administración. Selecciona un módulo para gestionar tu tienda.
                <?php else: ?>
                    Bienvenido a tu panel personal. Explora nuestro catálogo o revisa el estado de tus compras.
                <?php endif; ?>
            </p>
        </div>

        <div class="cards">

            <?php if ($es_admin): ?>
                <!-- ============================================== -->
                <!-- VISTA ADMINISTRADOR (Gestión de la Tienda)    -->
                <!-- ============================================== -->

                <!-- Card 1: Productos -->
                <div class="card">
                    <div class="card-icon"><i class="fa-solid fa-box-open"></i></div>
                    <h3>Productos</h3>
                    <p>Registrar, editar precios, stock y eliminar productos del catálogo.</p>
                    <a href="productos.php">Ingresar</a>
                </div>

                <!-- Card 2: Pedidos -->
                <div class="card">
                    <div class="card-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                    <h3>Pedidos</h3>
                    <p>Visualizar los pedidos personalizados de los clientes y contactarlos por WhatsApp.</p>
                    <a href="ver_pedidos.php">Ingresar</a>
                </div>

<!-- Card 3: Usuarios (CÓDIGO ACTUALIZADO) -->
<div class="card">
    <div class="card-icon"><i class="fa-solid fa-users"></i></div>
    <h3>Usuarios</h3>
    <p>Administrar las cuentas de clientes y administradores registrados.</p>
    <a href="usuarios.php">Ingresar</a>
</div>
                <!-- Card 4: Mensajes de contacto -->
                <div class="card">
                    <div class="card-icon"><i class="fa-solid fa-envelope-open-text"></i></div>
                    <h3>Mensajes</h3>
                    <p>Leer las consultas enviadas desde el formulario de contacto.</p>
                    <a href="ver_mensajes.php">Ingresar</a>
                </div>

           <?php else: ?>
                <!-- ============================================== -->
                <!-- VISTA CLIENTE (Experiencia de Compra)          -->
                <!-- ============================================== -->

                <!-- Card 1: Ver Catálogo -->
                <div class="card">
                    <div class="card-icon"><i class="fa-solid fa-store"></i></div>
                    <h3>Ver Catálogo</h3>
                    <p>Explora nuestras cajas sorpresa, peluches, arreglos y detalles.</p>
                    <a href="index.php">Ir a la Tienda</a>
                </div>

                <!-- Card 2: Mi Carrito -->
                <div class="card">
                    <div class="card-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                    <h3>Mi Carrito</h3>
                    <p>Revisa los regalos seleccionados y procede con el pago seguro.</p>
                    <a href="carrito.php">Ver Carrito</a>
                </div>

                <!-- Card 3: Mis Pedidos -->
                <div class="card">
                    <div class="card-icon"><i class="fa-solid fa-truck-fast"></i></div>
                    <h3>Mis Pedidos</h3>
                    <p>Revisa los pedidos personalizados que has solicitado.</p>
                    <a href="mis_pedidos.php">Ver Mis Pedidos</a>
                </div>

                <!-- Card 4: Soporte & Ayuda -->
                <div class="card">
                    <div class="card-icon"><i class="fa-brands fa-whatsapp"></i></div>
                    <h3>Soporte & Ayuda</h3>
                    <p>¿Dudas con tu entrega en Cañete? Contáctanos de inmediato por WhatsApp.</p>
                    <a href="https://api.whatsapp.com/send?phone=51900000000&text=Hola%20Detallitos%20JANA,%20necesito%20ayuda%20con%20mi%20pedido" target="_blank" rel="noopener noreferrer">
                        <i class="fa-brands fa-whatsapp"></i> Escribir al WhatsApp
                    </a>
                </div>

            <?php endif; ?>

        </div>

    </div>

    <!-- FOOTER -->
    <footer>
        © <?php echo date("Y"); ?> Detallitos JANA | Todos los derechos reservados
    </footer>

</body>

</html>