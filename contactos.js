document.addEventListener("DOMContentLoaded", function () {
    const formContacto = document.getElementById("formContacto");

    if (formContacto) {
        formContacto.addEventListener("submit", function (e) {
            e.preventDefault();

            // Capturar datos del formulario
            const nombre = document.getElementById("nombre").value.trim();
            const telefono = document.getElementById("telefono").value.trim();
            const distrito = document.getElementById("distrito").value;
            const mensaje = document.getElementById("mensaje").value.trim();

            if (!nombre || !telefono || !distrito || !mensaje) {
                mostrarToast("Por favor, completa todos los campos requeridos.");
                return;
            }

            // Preparar el envío con AJAX / Fetch
            const formData = new FormData(formContacto);

            fetch("procesar_contacto.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    mostrarToast(`¡Gracias ${nombre}! Tu mensaje ha sido enviado.`);
                    formContacto.reset();
                } else {
                    mostrarToast(data.message || "No se pudo enviar tu mensaje.");
                }
            })
            .catch(error => {
                console.error("Error al enviar mensaje:", error);
                mostrarToast("No se pudo enviar tu mensaje. Intenta nuevamente.");
            });
        });
    }
});

// Función Notificación Toast
function mostrarToast(mensaje) {
    const toast = document.getElementById("toast");
    if (!toast) return;

    toast.innerText = mensaje;
    toast.classList.add("show");

    setTimeout(function () {
        toast.classList.remove("show");
    }, 3500);
}