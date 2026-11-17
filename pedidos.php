<?php
// Antes este archivo abría su PROPIA conexión mysqli (credenciales duplicadas)
// y creaba la tabla en cada visita. Ahora usa conexion.php (PDO).
session_start();
require_once __DIR__ . '/conexion.php';

$mensaje_exito = "";
$mensaje_error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tipo_arreglo   = trim($_POST['tipo_arreglo'] ?? '');
    $color_rosas    = trim($_POST['color_rosas'] ?? '');
    $cantidad_rosas = max(1, (int) ($_POST['cantidad_rosas'] ?? 1));

    $complementos_array = $_POST['complementos'] ?? [];
    $complementos = (is_array($complementos_array) && $complementos_array)
        ? implode(", ", array_map('strval', $complementos_array)) : "Ninguno";

    $dedicatoria      = trim($_POST['dedicatoria'] ?? '');
    $fecha_entrega    = trim($_POST['fecha_entrega'] ?? '');
    $horario          = trim($_POST['horario'] ?? '');
    $nombre_cliente   = trim($_POST['nombre_cliente'] ?? '');
    $telefono_cliente = trim($_POST['telefono_cliente'] ?? '');

    $fechaOk = DateTime::createFromFormat('Y-m-d', $fecha_entrega);

    if ($tipo_arreglo === '' || $color_rosas === '' || $horario === '' || $nombre_cliente === '' || $telefono_cliente === '') {
        $mensaje_error = "Por favor completa todos los campos obligatorios.";
    } elseif (!$fechaOk || $fechaOk->format('Y-m-d') !== $fecha_entrega) {
        $mensaje_error = "La fecha de entrega no es válida.";
    } else {
        try {
            $conexion->prepare(
                "INSERT INTO pedidos_personalizados
                    (usuario_id, tipo_arreglo, color_rosas, cantidad_rosas, complementos, dedicatoria, fecha_entrega, horario, nombre_cliente, telefono_cliente)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            )->execute([
                $_SESSION['id'] ?? null,
                $tipo_arreglo, $color_rosas, $cantidad_rosas, $complementos,
                $dedicatoria, $fecha_entrega, $horario, $nombre_cliente, $telefono_cliente,
            ]);
            $mensaje_exito = "¡Tu pedido ha sido registrado con éxito! Te contactaremos pronto.";
        } catch (PDOException $e) {
            error_log('pedidos.php: ' . $e->getMessage());
            $mensaje_error = "Ocurrió un error al guardar el pedido. Intenta nuevamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos Personalizados | Detallitos JANA</title>
    
    <!-- Hoja de Estilos de tu proyecto -->
    <link rel="stylesheet" href="estyle.css">
    
    <!-- Font Awesome (Iconos) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <style>
        /* Estilos específicos para el formulario de pedidos */
        .contenedor-pedidos {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .card-form {
            background-color: #ffffff;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 10px 25px rgba(168, 85, 247, 0.08);
            border: 1px solid #f3e8ff;
        }

        .card-form h2 {
            color: #3b0764;
            font-size: 26px;
            margin-bottom: 8px;
            text-align: center;
        }

        .card-form p.subtitulo {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .grid-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .campo-full {
            grid-column: span 2;
        }

        .grupo-campo {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .grupo-campo label {
            font-size: 14px;
            font-weight: 600;
            color: #3b0764;
        }

        .grupo-campo input, 
        .grupo-campo select, 
        .grupo-campo textarea {
            padding: 12px 15px;
            border: 1px solid #e9d5ff;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s;
        }

        .grupo-campo input:focus, 
        .grupo-campo select:focus, 
        .grupo-campo textarea:focus {
            border-color: #a855f7;
        }

        .opciones-checkbox {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
            margin-top: 5px;
        }

        .opciones-checkbox label {
            font-weight: normal;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            background: #f8f5ff;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #f3e8ff;
        }

        .btn-enviar {
            background-color: #a855f7;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s, transform 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-enviar:hover {
            background-color: #9333ea;
            transform: translateY(-2px);
        }

        .alerta {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
        }
        .alerta-exito { background-color: #dcfce7; color: #15803d; }
        .alerta-error { background-color: #fee2e2; color: #b91c1c; }

        @media (max-width: 600px) {
            .grid-form { grid-template-columns: 1fr; }
            .campo-full { grid-column: span 1; }
        }
    </style>
</head>
<body>

    <!-- BARRA SUPERIOR Y HEADER DE TU SITIO -->
    <div class="top-bar"><i class="fa-solid fa-heart"></i> Envíos a todo el país</div>
    
    <header>
        <div class="logo">
            <a href="index.php"><img src="img/detallitolog.png" alt="Logo Detallitos JANA"></a>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="nosotros.html">Nosotros</a></li>
                <li><a href="pedidos.php" class="active">Pedidos Personalizados</a></li>
            </ul>
        </nav>
    </header>

    <main class="contenedor-pedidos">
        <div class="card-form">
            <h2><i class="fa-solid fa-wand-magic-sparkles"></i> Diseña tu Pedido Personalizado</h2>
            <p class="subtitulo">Completa las especificaciones de tu arreglo y nos pondremos en contacto contigo.</p>

            <!-- Mensajes de Notificación -->
            <?php if (!empty($mensaje_exito)): ?>
                <div class="alerta alerta-exito"><i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($mensaje_exito); ?></div>
            <?php endif; ?>

            <?php if (!empty($mensaje_error)): ?>
                <div class="alerta alerta-error"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($mensaje_error); ?></div>
            <?php endif; ?>

            <form action="pedidos.php" method="POST">
                <div class="grid-form">
                    
                    <!-- Tipo de Arreglo -->
                    <div class="grupo-campo">
                        <label for="tipo_arreglo">Presentación del Arreglo:</label>
                        <select name="tipo_arreglo" id="tipo_arreglo" required>
                            <option value="">Selecciona una opción</option>
                            <option value="Box Circular">Box Circular</option>
                            <option value="Box Corazón">Box Corazón</option>
                            <option value="Cúpula de Cristal">Cúpula de Cristal</option>
                            <option value="Ramo / Bouquet">Ramo / Bouquet</option>
                            <option value="Canasta Especial">Canasta Especial</option>
                        </select>
                    </div>

                    <!-- Color de Rosas -->
                    <div class="grupo-campo">
                        <label for="color_rosas">Color Principal de Rosas:</label>
                        <select name="color_rosas" id="color_rosas" required>
                            <option value="">Selecciona el color</option>
                            <option value="Rojo Clásico">Rojo Clásico</option>
                            <option value="Rosa Pastel">Rosa Pastel</option>
                            <option value="Lila / Morado">Lila / Morado</option>
                            <option value="Blanco">Blanco</option>
                            <option value="Azul">Azul</option>
                            <option value="Combinado">Combinado</option>
                        </select>
                    </div>

                    <!-- Cantidad de Rosas -->
                    <div class="grupo-campo">
                        <label for="cantidad_rosas">Cantidad de Rosas:</label>
                        <input type="number" name="cantidad_rosas" id="cantidad_rosas" min="1" max="100" value="12" required>
                    </div>

                    <!-- Fecha de Entrega -->
                    <div class="grupo-campo">
                        <label for="fecha_entrega">Fecha de Entrega:</label>
                        <input type="date" name="fecha_entrega" id="fecha_entrega" required>
                    </div>

                    <!-- Horario Preferencial -->
                    <div class="grupo-campo campo-full">
                        <label for="horario">Horario Preferencial:</label>
                        <select name="horario" id="horario" required>
                            <option value="Mañana (9:00 AM - 1:00 PM)">Mañana (9:00 AM - 1:00 PM)</option>
                            <option value="Tarde (2:00 PM - 7:00 PM)">Tarde (2:00 PM - 7:00 PM)</option>
                        </select>
                    </div>

                    <!-- Complementos Adicionales -->
                    <div class="grupo-campo campo-full">
                        <label>Agregar Complementos:</label>
                        <div class="opciones-checkbox">
                            <label><input type="checkbox" name="complementos[]" value="Luces LED"> Luces LED</label>
                            <label><input type="checkbox" name="complementos[]" value="Peluche"> Peluche</label>
                            <label><input type="checkbox" name="complementos[]" value="Chocolates"> Chocolates</label>
                            <label><input type="checkbox" name="complementos[]" value="Globo Personalizado"> Globo Personalizado</label>
                        </div>
                    </div>

                    <!-- Dedicatoria -->
                    <div class="grupo-campo campo-full">
                        <label for="dedicatoria">Mensaje o Dedicatoria para la tarjeta:</label>
                        <textarea name="dedicatoria" id="dedicatoria" rows="3" placeholder="Escribe aquí tu dedicatoria especial..."></textarea>
                    </div>

                    <!-- Datos del Cliente -->
                    <div class="grupo-campo">
                        <label for="nombre_cliente">Tu Nombre Completo:</label>
                        <input type="text" name="nombre_cliente" id="nombre_cliente" placeholder="Ej. Ana García" required>
                    </div>

                    <div class="grupo-campo">
                        <label for="telefono_cliente">Teléfono / WhatsApp:</label>
                        <input type="tel" name="telefono_cliente" id="telefono_cliente" placeholder="Ej. 987654321" required>
                    </div>

                    <!-- Botón Enviar -->
                    <div class="grupo-campo campo-full" style="margin-top: 15px;">
                        <button type="submit" class="btn-enviar">
                            <i class="fa-solid fa-paper-plane"></i> Guardar y Solicitar Cotización
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </main>

<script src="session-nav.js"></script>
<script src="img-fallback.js"></script>
</body>
</html>