<?php
session_start();
require_once __DIR__ . '/config.php';

if (isset($_SESSION['id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Detallitos JANA</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>

<div class="contenedor">
    <div class="login-box">

        <img src="img/detallitolog.png" class="logo" alt="Detallitos JANA">

        <h2>Bienvenido</h2>
        <p>Inicia sesión para continuar</p>

        <?php foreach (['error' => 'error', 'mensaje' => 'error'] as $clave => $clase): ?>
            <?php if (isset($_SESSION[$clave])): ?>
                <div class="<?= $clase ?>"><?= htmlspecialchars($_SESSION[$clave]) ?></div>
                <?php unset($_SESSION[$clave]); ?>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if (isset($_SESSION['exito'])): ?>
            <div class="exito" style="background:#d4edda; color:#155724; padding:10px; border-radius:8px; margin-bottom:15px; font-size:14px;">
                <?= htmlspecialchars($_SESSION['exito']) ?>
            </div>
            <?php unset($_SESSION['exito']); ?>
        <?php endif; ?>

        <div id="g_id_onload"
             data-client_id="<?= htmlspecialchars(GOOGLE_CLIENT_ID) ?>"
             data-callback="handleCredentialResponse">
        </div>
        <div class="g_id_signin" data-type="standard" data-shape="rectangular" data-theme="outline"
             data-text="continue_with" data-size="large"
             style="margin-bottom:15px; display:flex; justify-content:center;"></div>

        <div style="margin:15px 0; color:#aaa; font-size:13px; display:flex; align-items:center; gap:10px;">
            <hr style="flex:1; border:0; border-top:1px solid #ddd;"> O con tu correo <hr style="flex:1; border:0; border-top:1px solid #ddd;">
        </div>

        <form action="validar_login.php" method="POST">
            <div class="grupo">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="correo" placeholder="Correo electrónico" required>
            </div>

            <div class="grupo">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="Contraseña" required>
                <span onclick="mostrarPassword()"><i id="icono" class="fa-solid fa-eye"></i></span>
            </div>

            <button type="submit"><i class="fa-solid fa-right-to-bracket"></i> Ingresar</button>
        </form>

        <div class="links">
            <a href="registro.php">Crear cuenta</a>
            <a href="recuperar.php">¿Olvidaste tu contraseña?</a>
        </div>

    </div>
</div>

<script>
function mostrarPassword() {
    const pass = document.getElementById("password");
    const icono = document.getElementById("icono");
    const oculto = pass.type === "password";
    pass.type = oculto ? "text" : "password";
    icono.classList.toggle("fa-eye", !oculto);
    icono.classList.toggle("fa-eye-slash", oculto);
}

function handleCredentialResponse(response) {
    const formData = new FormData();
    formData.append("credential", response.credential);

    fetch("login_google.php", { method: "POST", body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                window.location.href = data.redirect;
            } else {
                alert("Error en la autenticación con Google: " + data.message);
            }
        })
        .catch(() => alert("No se pudo conectar con el servidor."));
}
</script>

</body>
</html>
