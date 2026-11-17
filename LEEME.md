# Detallitos JANA — versión corregida

Sitio PHP + MySQL (PDO). Probado con XAMPP / WAMP (PHP 8+).

## Instalación (5 pasos)

1. Copia esta carpeta a `htdocs` (XAMPP) y abre `http://localhost/pagina_mejorada/`.
2. En phpMyAdmin **elimina la base `detallitos_jana` anterior** (si existe) e importa `detallitos_jana.sql`.
   > Si no la eliminas, las tablas viejas no se actualizan (el script usa `CREATE TABLE IF NOT EXISTS`) y el sitio fallará con columnas inexistentes. Se pierden los datos de prueba anteriores.
3. Revisa `config.php` (usuario/clave de MySQL, Client ID de Google).
4. Entra con el administrador: `detallitosjana@gmail.com` / `admin123` → **cámbiala** desde *Usuarios*.
5. Agrega las fotos de los productos (ver "Pendiente" abajo).

## Qué se corrigió

**El carrito no funcionaba en absoluto**
- La tabla `carrito` no existía en tu SQL, y `agregar_carrito.php` nunca insertaba nada.
- Los botones de las 5 categorías llamaban a `agregar_al_carrito.php` (archivo inexistente).
- `actualizar_carrito.php` / `eliminar_carrito.php` no verificaban sesión ni que el ítem fuera del usuario.

**Seguridad**
- `login_google.php` aceptaba el token de Google **sin verificar su firma**: cualquiera podía entrar como cualquier correo, incluido el admin. Ahora se valida con Google y se comprueba el `aud`.
- `ver_pedidos.php` era público (nombres y teléfonos de clientes). Ahora exige rol administrador.
- `recuperar.php` era una copia de `registro.php`. Ahora hay recuperación real (`recuperar.php` + `reset_password.php`, token de 1 hora y un solo uso). El enlace **se envía por correo**; solo se muestra en pantalla si accedes desde localhost.
- Contraseña del admin hasheada; migración automática de contraseñas antiguas en texto plano; `session_regenerate_id` al iniciar sesión; salida escapada con `htmlspecialchars`; errores de BD al log y no a la pantalla.

**Errores que impedían el funcionamiento**
- `usuarios.php` mezclaba mysqli con PDO y usaba una columna `email` inexistente (es `correo`); usaba la variable `$resultado` sin definirla.
- `pedidos.php` y `ver_pedidos.php` abrían su propia conexión mysqli; ahora usan `conexion.php`.
- `usuarios` tenía una columna `nombres` NOT NULL sin valor y un `rol` que no admitía `'Cliente'`: el registro fallaba siempre.
- `validar_registro.php` no comprobaba "confirmar contraseña".
- `login.php` tenía el botón de Google pegado dentro de un `<script>` abierto.
- El formulario de contacto enviaba a `procesar_contacto.php` (no existía) y mostraba "mensaje enviado" igual: **se perdían los mensajes**. Ahora se guardan (tabla `mensajes_contacto`) y el admin los ve en *Mensajes*.
- Enlaces rotos: `pages/carrito.html`, `pages/busqueda.html`, `contacto.html`, `contacto.js`, y archivos con la `ñ` mal codificada (`cumpleaños.*` → `cumpleanos.*`).
- Dashboard: faltaba `mis_pedidos.php` (creada); la tarjeta "Reportes" enlazaba a Usuarios (ahora es "Mensajes").
- `catalogo.php` generaba 100 productos falsos con precios aleatorios y un carrito aparte en `localStorage`; ahora muestra los productos reales de la BD y usa el carrito real.
- El encabezado de las páginas `.html` ahora refleja la sesión y el contador real del carrito (`session-nav.js`).

## Pendiente / límites conocidos

- **Fotos:** el ZIP original no traía las imágenes de producto (`img/ninos/`, `img/amor/`, `img/cumpleanos/`, `img/para_el/`, ni `rosa1.jpg`, `corazon.jpg`, `peluche.jpg`). Mientras no estén, se muestra el logo. Respeta los nombres que usan los HTML.
- **Precios de las páginas de categoría** están escritos en el HTML (no en la BD), por lo que el servidor los recibe del navegador. Para vender en serio conviene moverlos a la tabla `productos`.
- **No hay módulo de administración de productos** (la tarjeta "Productos" del dashboard abre el listado público).
- **El pago es por WhatsApp.** Números de ejemplo a reemplazar: `carrito.js` (línea 32), `contactos.html` (línea 121) y `dashboard.php` (línea 362).
- **Google:** pega tu Client ID en `config.php` (ahora es un texto de ejemplo, el botón no funcionará hasta entonces).
- **Correo:** para que "olvidé mi contraseña" funcione en un hosting real, configura `mail()` y `BASE_URL` en `config.php`, y pon `MODO_DESARROLLO` en `false`.
- No pude probar el sitio contra un servidor MySQL en mi entorno; sí verifiqué la sintaxis de todo el PHP/JS, los enlaces internos y que las páginas cargan sin avisos. Si algo falla al importar o al usarlo, avísame con el mensaje de error.
