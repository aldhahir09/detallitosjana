<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers.php';

// Solo administradores
requiereAdmin();

$mensaje = "";

// Acciones: crear usuario / restablecer contraseña.
// Antes se usaban métodos de mysqli (bind_param) sobre un objeto PDO y una
// columna `email` que no existe (la real es `correo`): fallaba siempre.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['accion_crear'])) {
            $nombre   = trim($_POST['nombre'] ?? '');
            $correo   = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $rol      = (($_POST['rol'] ?? '') === 'Administrador') ? 'Administrador' : 'Cliente';

            if ($nombre === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
                $mensaje = "Revisa los datos: nombre, correo válido y contraseña de al menos 6 caracteres.";
            } else {
                $check = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
                $check->execute([$correo]);
                if ($check->fetch()) {
                    $mensaje = "Ya existe un usuario con ese correo.";
                } else {
                    $conexion->prepare("INSERT INTO usuarios (nombre, correo, password, rol) VALUES (?, ?, ?, ?)")
                             ->execute([$nombre, $correo, password_hash($password, PASSWORD_DEFAULT), $rol]);
                    $mensaje = "Usuario creado exitosamente.";
                }
            }
        }

        if (isset($_POST['accion_reset_pass'])) {
            $id_user    = (int) ($_POST['id_usuario'] ?? 0);
            $nueva_pass = $_POST['nueva_password'] ?? '';

            if ($id_user <= 0 || strlen($nueva_pass) < 6) {
                $mensaje = "La nueva contraseña debe tener al menos 6 caracteres.";
            } else {
                $conexion->prepare("UPDATE usuarios SET password = ? WHERE id = ?")
                         ->execute([password_hash($nueva_pass, PASSWORD_DEFAULT), $id_user]);
                $mensaje = "Contraseña restablecida correctamente.";
            }
        }
    } catch (PDOException $e) {
        error_log('usuarios.php: ' . $e->getMessage());
        $mensaje = "Ocurrió un error al procesar la solicitud.";
    }
}

$usuarios = $conexion->query("SELECT * FROM usuarios ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios | Detallitos JANA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', sans-serif; margin: 0; padding: 0; }
        body { background: #f4f6f9; padding: 20px; color: #333; }
        .header-panel { display: flex; justify-content: space-between; align-items: center; background: white; padding: 15px 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .btn-volver { background: #b57be8; color: white; padding: 8px 15px; text-decoration: none; border-radius: 6px; font-weight: 600; }
        .grid-layout { display: grid; grid-template-columns: 1fr 2fr; gap: 20px; }
        .box { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .box h3 { margin-bottom: 15px; color: #9d67d9; }
        form label { display: block; margin: 10px 0 5px; font-weight: 600; font-size: 0.9rem; }
        form input, form select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; margin-bottom: 10px; }
        .btn-submit { background: #2ed573; color: white; border: none; padding: 10px; width: 100%; border-radius: 6px; font-weight: bold; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; font-size: 0.9rem; }
        th { background: #9d67d9; color: white; }
        .alerta { background: #eccc68; color: #333; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-weight: bold; }
        .btn-action { background: #70a1ff; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>

<div class="header-panel">
    <h2><i class="fa-solid fa-users"></i> Módulo de Usuarios</h2>
    <a href="dashboard.php" class="btn-volver"><i class="fa-solid fa-arrow-left"></i> Volver al Dashboard</a>
</div>

<?php if ($mensaje): ?>
    <div class="alerta"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>

<div class="grid-layout">
    
    <!-- FORMULARIO CREAR USUARIO -->
    <div class="box">
        <h3><i class="fa-solid fa-user-plus"></i> Crear Nuevo Usuario</h3>
        <form method="POST">
            <input type="hidden" name="accion_crear" value="1">
            <label>Nombre Completo</label>
            <input type="text" name="nombre" required placeholder="Ej. Maria Lopez">
            
            <label>Correo Electrónico</label>
            <input type="email" name="email" required placeholder="correo@ejemplo.com">
            
            <label>Contraseña</label>
            <input type="password" name="password" required minlength="6" placeholder="Mínimo 6 caracteres">
            
            <label>Rol</label>
            <select name="rol">
                <option value="Cliente">Cliente</option>
                <option value="Administrador">Administrador</option>
            </select>
            
            <button type="submit" class="btn-submit">Registrar Usuario</button>
        </form>
    </div>

    <!-- TABLA DE USUARIOS & CAMBIO DE CLAVE -->
    <div class="box">
        <h3><i class="fa-solid fa-list"></i> Lista de Usuarios Registrados</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Restablecer Clave</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td>#<?php echo (int) $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($u['correo']); ?></td>
                        <td><strong><?php echo htmlspecialchars($u['rol']); ?></strong></td>
                        <td>
                            <!-- Formulario rápido para resetear clave -->
                            <form method="POST" style="display:flex; gap:5px;">
                                <input type="hidden" name="accion_reset_pass" value="1">
                                <input type="hidden" name="id_usuario" value="<?php echo (int) $u['id']; ?>">
                                <input type="password" name="nueva_password" placeholder="Nueva clave" required minlength="6" style="margin:0; padding:4px;">
                                <button type="submit" class="btn-action" title="Guardar nueva clave"><i class="fa-solid fa-key"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>