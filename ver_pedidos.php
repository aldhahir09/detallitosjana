<?php
/**
 * Panel de pedidos personalizados (solo administradores).
 * Antes era público para cualquier visitante (fuga de nombres y teléfonos
 * de clientes) y abría su propia conexión mysqli.
 */
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers.php';

requiereAdmin();

$pedidos = $conexion->query("SELECT * FROM pedidos_personalizados ORDER BY fecha_registro DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Pedidos | Detallitos J.A.N.A.</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f5ff;
            padding: 30px;
            color: #333;
        }
        h1 {
            color: #3b0764;
            margin-bottom: 20px;
        }
        .tabla-contenedor {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #f3e8ff;
        }
        th {
            background-color: #a855f7;
            color: white;
            border-radius: 4px;
        }
        tr:hover {
            background-color: #faf5ff;
        }
        .btn-wa {
            background-color: #25d366;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: bold;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-wa:hover {
            background-color: #1eb757;
        }
    </style>
</head>
<body>

    <h1><i class="fa-solid fa-list-check"></i> Pedidos Personalizados Recibidos</h1>

    <div class="tabla-contenedor">
        <table>
            <thead>
                <tr>
                    <th># ID</th>
                    <th>Cliente</th>
                    <th>Teléfono</th>
                    <th>Arreglo</th>
                    <th>Rosas</th>
                    <th>Cant.</th>
                    <th>Complementos</th>
                    <th>Dedicatoria</th>
                    <th>Fecha Entrega</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($pedidos) > 0): ?>
                    <?php foreach ($pedidos as $fila): ?>
                        <tr>
                            <td><strong>#<?php echo (int) $fila['id']; ?></strong></td>
                            <td><?php echo htmlspecialchars($fila['nombre_cliente']); ?></td>
                            <td><?php echo htmlspecialchars($fila['telefono_cliente']); ?></td>
                            <td><?php echo htmlspecialchars($fila['tipo_arreglo']); ?></td>
                            <td><?php echo htmlspecialchars($fila['color_rosas']); ?></td>
                            <td><?php echo (int) $fila['cantidad_rosas']; ?></td>
                            <td><?php echo htmlspecialchars($fila['complementos']); ?></td>
                            <td><?php echo htmlspecialchars($fila['dedicatoria']); ?></td>
                            <td><?php echo date("d/m/Y", strtotime($fila['fecha_entrega'])); ?> (<?php echo htmlspecialchars($fila['horario']); ?>)</td>
                            <td>
                                <!-- Botón rápido para contactar al cliente por WhatsApp -->
                                <a class="btn-wa" href="https://wa.me/51<?php echo preg_replace('/[^0-9]/', '', $fila['telefono_cliente']); ?>?text=Hola%20<?php echo urlencode($fila['nombre_cliente']); ?>,%20te%20contactamos%20de%20Detallitos%20J.A.N.A.%20sobre%20tu%20pedido%20de%20<?php echo urlencode($fila['tipo_arreglo']); ?>" target="_blank" rel="noopener noreferrer">
                                    <i class="fa-brands fa-whatsapp"></i> Contactar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" style="text-align: center; color: #888;">Aún no hay pedidos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>