<?php
/**
 * "Mis Pedidos" del cliente. El dashboard enlazaba a esta página pero no existía.
 * Muestra los pedidos personalizados hechos con la sesión iniciada.
 */
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers.php';

requiereLogin('Debes iniciar sesión para ver tus pedidos.');

$stmt = $conexion->prepare('SELECT * FROM pedidos_personalizados WHERE usuario_id = ? ORDER BY fecha_registro DESC');
$stmt->execute([(int) $_SESSION['id']]);
$pedidos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos | Detallitos JANA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background:#f8f5ff; padding:30px; color:#333; }
        .header-panel { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:10px; flex-wrap:wrap; }
        h1 { color:#3b0764; font-size:1.5rem; }
        .btn { background:#b57be8; color:#fff; padding:8px 15px; text-decoration:none; border-radius:6px; font-weight:600; }
        .lista { display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:20px; }
        .pedido { background:#fff; border-radius:15px; padding:20px; box-shadow:0 5px 20px rgba(0,0,0,.05); }
        .pedido h3 { color:#7e22ce; margin-bottom:10px; }
        .pedido p { margin:4px 0; font-size:.92rem; }
        .vacio { background:#fff; padding:40px; border-radius:15px; text-align:center; color:#777; }
    </style>
</head>
<body>
    <div class="header-panel">
        <h1><i class="fa-solid fa-truck-fast"></i> Mis Pedidos Personalizados</h1>
        <span>
            <a href="pedidos.php" class="btn"><i class="fa-solid fa-plus"></i> Nuevo pedido</a>
            <a href="dashboard.php" class="btn"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        </span>
    </div>

    <?php if (!$pedidos): ?>
        <div class="vacio">
            <p>Aún no tienes pedidos personalizados registrados con tu cuenta.</p>
            <p style="margin-top:12px;"><a href="pedidos.php" class="btn">Crear mi primer pedido</a></p>
        </div>
    <?php else: ?>
        <div class="lista">
            <?php foreach ($pedidos as $p): ?>
                <div class="pedido">
                    <h3>#<?= (int) $p['id'] ?> · <?= htmlspecialchars($p['tipo_arreglo']) ?></h3>
                    <p><strong>Rosas:</strong> <?= (int) $p['cantidad_rosas'] ?> (<?= htmlspecialchars($p['color_rosas']) ?>)</p>
                    <p><strong>Complementos:</strong> <?= htmlspecialchars($p['complementos']) ?></p>
                    <p><strong>Dedicatoria:</strong> <?= htmlspecialchars($p['dedicatoria'] ?? '') ?></p>
                    <p><strong>Entrega:</strong> <?= date('d/m/Y', strtotime($p['fecha_entrega'])) ?> · <?= htmlspecialchars($p['horario']) ?></p>
                    <p><strong>Registrado:</strong> <?= date('d/m/Y H:i', strtotime($p['fecha_registro'])) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>
