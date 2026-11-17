<?php
/** Mensajes recibidos desde el formulario de contacto (solo administradores). */
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers.php';

requiereAdmin();

$mensajes = $conexion->query('SELECT * FROM mensajes_contacto ORDER BY fecha_registro DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes de Contacto | Detallitos JANA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background:#f8f5ff; padding:30px; color:#333; }
        .header-panel { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:10px; flex-wrap:wrap; }
        h1 { color:#3b0764; font-size:1.5rem; }
        .btn-volver { background:#b57be8; color:#fff; padding:8px 15px; text-decoration:none; border-radius:6px; font-weight:600; }
        .tabla-contenedor { background:#fff; padding:20px; border-radius:15px; box-shadow:0 5px 20px rgba(0,0,0,.05); overflow-x:auto; }
        table { width:100%; border-collapse:collapse; font-size:14px; }
        th, td { padding:12px 15px; text-align:left; border-bottom:1px solid #f3e8ff; vertical-align:top; }
        th { background:#a855f7; color:#fff; }
        tr:hover { background:#faf5ff; }
        .btn-wa { background:#25d366; color:#fff; padding:6px 12px; border-radius:20px; text-decoration:none; font-weight:bold; font-size:12px; white-space:nowrap; }
    </style>
</head>
<body>
    <div class="header-panel">
        <h1><i class="fa-solid fa-envelope-open-text"></i> Mensajes de Contacto</h1>
        <a href="dashboard.php" class="btn-volver"><i class="fa-solid fa-arrow-left"></i> Volver al Dashboard</a>
    </div>

    <div class="tabla-contenedor">
        <table>
            <thead>
                <tr><th>Fecha</th><th>Nombre</th><th>Teléfono</th><th>Distrito</th><th>Asunto</th><th>Mensaje</th><th>Acción</th></tr>
            </thead>
            <tbody>
            <?php if (!$mensajes): ?>
                <tr><td colspan="7" style="text-align:center; color:#888;">Aún no hay mensajes.</td></tr>
            <?php endif; ?>
            <?php foreach ($mensajes as $m): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($m['fecha_registro'])) ?></td>
                    <td><?= htmlspecialchars($m['nombre']) ?></td>
                    <td><?= htmlspecialchars($m['telefono']) ?></td>
                    <td><?= htmlspecialchars($m['distrito']) ?></td>
                    <td><?= htmlspecialchars($m['asunto'] ?? '') ?></td>
                    <td><?= nl2br(htmlspecialchars($m['mensaje'])) ?></td>
                    <td>
                        <a class="btn-wa" target="_blank" rel="noopener noreferrer"
                           href="https://wa.me/51<?= preg_replace('/\D/', '', $m['telefono']) ?>?text=<?= rawurlencode('Hola ' . $m['nombre'] . ', te escribimos de Detallitos JANA por tu consulta.') ?>">
                            <i class="fa-brands fa-whatsapp"></i> Responder
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
