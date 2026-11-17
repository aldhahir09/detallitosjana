// Llamada común a los endpoints del carrito; recarga solo si el servidor confirma.
function llamarCarrito(url, datos, errorMsg) {
    const formData = new FormData();
    Object.keys(datos).forEach((k) => formData.append(k, datos[k]));

    fetch(url, { method: "POST", body: formData })
        .then((res) => res.json())
        .then((data) => {
            if (data.status !== "success") {
                alert(data.message || errorMsg);
                return;
            }
            window.location.reload();
        })
        .catch(() => alert("Error de conexión. Intenta nuevamente."));
}

// Actualizar la cantidad de productos
function actualizarCantidad(id, cambio) {
    llamarCarrito("actualizar_carrito.php", { id: id, cambio: cambio }, "No se pudo actualizar el carrito.");
}

// Eliminar ítem del carrito
function eliminarDelCarrito(id) {
    if (confirm("¿Deseas quitar este detalle de tu carrito?")) {
        llamarCarrito("eliminar_carrito.php", { id: id }, "No se pudo eliminar el producto.");
    }
}

// Enviar pedido por WhatsApp
function procesarCompra(total) {
    const numeroWhatsApp = "51987654321"; // Reemplazar con el número real de Detallitos JANA
    let mensaje = "¡Hola Detallitos JANA! 🌸 Deseo realizar el siguiente pedido desde la web:\n\n";

    const items = document.querySelectorAll(".item-carrito");
    items.forEach(item => {
        const nombre = item.querySelector("h4").innerText;
        const cantidad = item.querySelector(".control-cantidad span").innerText;
        const subtotal = item.querySelector(".subtotal-item").innerText;
        mensaje += `• ${cantidad}x ${nombre} (${subtotal})\n`;
    });

    mensaje += `\n*Total a Pagar:* S/ ${total.toFixed(2)}\n`;
    mensaje += "Quedo a la espera para coordinar el pago y la dirección de entrega. ¡Gracias!";

    const url = `https://wa.me/${numeroWhatsApp}?text=${encodeURIComponent(mensaje)}`;
    window.open(url, "_blank");
}