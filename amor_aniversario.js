document.addEventListener("DOMContentLoaded", function () {
    // 1. Filtrado de productos por subcategoría
    const botonesFiltro = document.querySelectorAll(".btn-filtro");
    const tarjetasProductos = document.querySelectorAll(".card-producto");

    botonesFiltro.forEach(boton => {
        boton.addEventListener("click", function () {
            botonesFiltro.forEach(b => b.classList.remove("active"));
            this.classList.add("active");

            const categoria = this.getAttribute("data-categoria");

            tarjetasProductos.forEach(tarjeta => {
                if (categoria === "todos" || tarjeta.getAttribute("data-categoria") === categoria) {
                    tarjeta.style.display = "flex";
                } else {
                    tarjeta.style.display = "none";
                }
            });
        });
    });
});

// 2. Envío de producto al carrito mediante AJAX
// Antes apuntaba a "agregar_al_carrito.php", un archivo que NO existe (el real es
// agregar_carrito.php): ningún botón "Agregar" de esta página funcionaba. Además
// no enviaba nombre/precio, que el servidor necesita para estos productos.
function agregarAlCarrito(boton, productoId, nombreProducto, precio) {
    const formData = new FormData();
    formData.append("producto_id", productoId);
    formData.append("nombre", nombreProducto);
    formData.append("precio", precio);
    formData.append("cantidad", 1);

    const tarjeta = boton.closest(".card-producto");
    const img = tarjeta ? tarjeta.querySelector("img") : null;
    if (img && img.getAttribute("src")) {
        formData.append("imagen", img.getAttribute("src"));
    }

    fetch("agregar_carrito.php", { method: "POST", body: formData })
        .then(response => {
            if (response.status === 401) {
                mostrarToast("Debes iniciar sesión para comprar. Redirigiendo...");
                setTimeout(() => { window.location.href = "login.php"; }, 1500);
                return null;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;
            if (data.status !== "success") {
                mostrarToast(data.message || "No se pudo agregar el producto.");
                return;
            }
            const contador = document.querySelector(".carrito .contador");
            if (contador) contador.textContent = data.cart_count;
            mostrarToast(`¡"${nombreProducto}" agregado al carrito!`);
        })
        .catch(error => {
            console.error("Error al agregar producto:", error);
            mostrarToast("Hubo un problema al agregar al carrito.");
        });
}

// 3. Notificación flotante (Toast)
function mostrarToast(mensaje) {
    const toast = document.getElementById("toast");
    if (!toast) { alert(mensaje); return; }

    toast.innerText = mensaje;
    toast.classList.add("show");
    setTimeout(function () { toast.classList.remove("show"); }, 3000);
}
