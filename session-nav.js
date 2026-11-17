// Sincroniza el encabezado con la sesión real (session_status.php) en TODAS las
// páginas, incluidas las .html estáticas: enlace de cuenta, contador del carrito
// y botón "Cerrar sesión". Si el servidor no responde, la página queda como está.
document.addEventListener("DOMContentLoaded", function () {
    fetch("session_status.php", { credentials: "same-origin" })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            var cuenta = document.querySelector('a[title="Iniciar sesión"]');
            if (cuenta && data.logueado) {
                cuenta.href = "dashboard.php";
                cuenta.title = "Mi cuenta (" + (data.nombre || "Usuario") + ")";
            }

            document.querySelectorAll(".contador, #cart-count").forEach(function (el) {
                el.textContent = data.cart_count || 0;
            });

            document.querySelectorAll('a[href="logout.php"]').forEach(function (el) {
                if (!data.logueado) { el.style.display = "none"; }
            });
        })
        .catch(function () { /* sin backend disponible: no se hace nada */ });
});
