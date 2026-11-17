// Si una imagen no carga (archivo faltante), se reemplaza por el logo del sitio
// en lugar de mostrar el ícono de "imagen rota" del navegador.
document.addEventListener("error", function (e) {
    var el = e.target;
    if (el && el.tagName === "IMG" && !el.dataset.fallback) {
        el.dataset.fallback = "1";
        el.src = "img/detallitolog.png";
    }
}, true);
