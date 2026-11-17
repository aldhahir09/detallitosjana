<?php
session_start();
require_once __DIR__ . '/config.php';

if (isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta | Detallitos JANA</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- SDK de Google -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>

<body>

    <div class="contenedor">
        <div class="login-box">

            <img src="img/detallitolog.png" class="logo" alt="Detallitos JANA">

            <h2>Crear Cuenta</h2>
            <p>Únete para realizar tus compras de forma rápida</p>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="error">
                    <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['exito'])): ?>
                <div class="exito" style="background:#d4edda; color:#155724; padding:10px; border-radius:8px; margin-bottom:15px; font-size:14px;">
                    <?php 
                    echo $_SESSION['exito']; 
                    unset($_SESSION['exito']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- Botón de Registro con Google -->
            <div id="g_id_onload"
                 data-client_id="<?= htmlspecialchars(GOOGLE_CLIENT_ID) ?>"
                 data-callback="handleCredentialResponse">
            </div>

            <div class="g_id_signin" 
                 data-type="standard" 
                 data-shape="rectangular" 
                 data-theme="outline" 
                 data-text="signup_with" 
                 data-size="large"
                 style="margin-bottom: 15px; display: flex; justify-content: center;">
            </div>

            <div style="margin: 15px 0; color: #aaa; font-size: 13px; display: flex; align-items: center; gap: 10px;">
                <hr style="flex:1; border:0; border-top:1px solid #ddd;"> O con tu correo <hr style="flex:1; border:0; border-top:1px solid #ddd;">
            </div>

            <!-- Formulario Tradicional -->
            <form action="validar_registro.php" method="POST">

                <div class="grupo">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="nombre" placeholder="Nombre completo" required>
                </div>

                <div class="grupo">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="correo" placeholder="Correo electrónico" required>
                </div>

                <div class="grupo">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Contraseña" required>
                    <span onclick="mostrarPassword('password', 'icono1')">
                        <i id="icono1" class="fa-solid fa-eye"></i>
                    </span>
                </div>

                <div class="grupo">
                    <i class="fa-solid fa-shield-halved"></i>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmar contraseña" required>
                    <span onclick="mostrarPassword('confirm_password', 'icono2')">
                        <i id="icono2" class="fa-solid fa-eye"></i>
                    </span>
                </div>

                <button type="submit">
                    <i class="fa-solid fa-user-plus"></i> Registra me
                </button>

            </form>

            <div class="links">
                <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
            </div>

        </div>
    </div>

    <script>
        function mostrarPassword(inputId, iconoId) {
            let pass = document.getElementById(inputId);
            let icono = document.getElementById(iconoId);

            if (pass.type === "password") {
                pass.type = "text";
                icono.classList.remove("fa-eye");
                icono.classList.add("fa-eye-slash");
            } else {
                pass.type = "password";
                icono.classList.remove("fa-eye-slash");
                icono.classList.add("fa-eye");
            }
        }

        function handleCredentialResponse(response) {
            const formData = new FormData();
            formData.append("credential", response.credential);

            fetch("login_google.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    window.location.href = data.redirect;
                } else {
                    alert("Error al registrarse con Google: " + data.message);
                }
            });
        }
    </script>

</body>
</html>