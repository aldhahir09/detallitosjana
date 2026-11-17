-- ============================================================
-- Base de datos: detallitos_jana (versión corregida)
-- Importar con phpMyAdmin o:  mysql -u root < detallitos_jana.sql
--
-- Cambios respecto al original:
--  * NUEVA tabla carrito (el código la usaba pero no existía).
--  * NUEVA tabla password_resets (recuperación de contraseña).
--  * usuarios: se elimina la columna duplicada "nombres" (NOT NULL
--    sin valor por defecto, rompía todo registro), el rol ahora es
--    ENUM('Administrador','Cliente') (el código usa 'Cliente', el
--    original solo permitía 'Usuario'), correo es UNIQUE y la
--    contraseña del admin queda hasheada (sigue siendo admin123:
--    cámbiala después de la primera entrada).
--  * pedidos_personalizados: se agrega usuario_id;
--    horario pasa de varchar(20) a varchar(40) (cortaba el texto).
--  * productos: se agrega la columna categoria (opcional).
--  * NUEVA tabla mensajes_contacto (el formulario de contacto enviaba a
--    un archivo inexistente y los mensajes se perdían).
--  * Tus datos originales se conservan.
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `detallitos_jana` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `detallitos_jana`;

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('Administrador','Cliente') NOT NULL DEFAULT 'Cliente',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuarios_correo` (`correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `password`, `rol`) VALUES
(2, 'Detallitos JANA Admin', 'detallitosjana@gmail.com', '$2y$10$.1RyOYo6Vq8Cd7xczyeCv.iA4XomRfdSjSj2p7VGP0m3wlxJAvXDe', 'Administrador'),
(3, 'aldhahir', 'aldhahirsaldana15@gmail.com', '$2y$10$xccfcns4OH0b/7e/zIL3YO/nj2pJg8dS5/19kfXwp1qRylEh0wO0q', 'Cliente');

CREATE TABLE IF NOT EXISTS `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `imagen` varchar(200) DEFAULT NULL,
  `categoria` varchar(60) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `imagen`, `stock`) VALUES
(1, 'Rosa Eterna Lila', 'Rosa natural preservada.', 89.90, 'rosa1.jpg', 10),
(2, 'Caja Corazón', 'Caja con 24 rosas eternas.', 189.90, 'corazon.jpg', 5),
(3, 'Rosa con Peluche', 'Incluye oso de peluche.', 149.90, 'peluche.jpg', 8);

-- El carrito guarda nombre/precio/imagen propios porque gran parte del
-- catálogo (páginas de categoría) no está en la tabla productos.
CREATE TABLE IF NOT EXISTS `carrito` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `nombre_producto` varchar(180) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `fecha_agregado` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_carrito_usuario` (`usuario_id`),
  CONSTRAINT `fk_carrito_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_carrito_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `pedidos_personalizados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `tipo_arreglo` varchar(50) NOT NULL,
  `color_rosas` varchar(50) NOT NULL,
  `cantidad_rosas` int(11) NOT NULL,
  `complementos` varchar(255) DEFAULT 'Ninguno',
  `dedicatoria` text DEFAULT NULL,
  `fecha_entrega` date NOT NULL,
  `horario` varchar(40) NOT NULL,
  `nombre_cliente` varchar(100) NOT NULL,
  `telefono_cliente` varchar(20) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_pedidos_usuario` (`usuario_id`),
  CONSTRAINT `fk_pedidos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `pedidos_personalizados` (`id`, `tipo_arreglo`, `color_rosas`, `cantidad_rosas`, `complementos`, `dedicatoria`, `fecha_entrega`, `horario`, `nombre_cliente`, `telefono_cliente`, `fecha_registro`) VALUES
(1, 'Box Corazón', 'Rosa Pastel', 1, 'Luces LED, Peluche', 'te quiero', '2026-08-25', 'Tarde (2:00 PM - 7:0', 'aldahir saldaña', '984254147', '2026-08-02 05:19:30'),
(2, 'Box Circular', 'Azul', 20, 'Chocolates', 'jkserfnisdbfgis', '2026-08-11', 'Tarde (2:00 PM - 7:0', 'aldahir saldaña', '35684254124', '2026-08-02 21:17:38');

CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expira` datetime NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_reset_token` (`token`),
  CONSTRAINT `fk_resets_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `mensajes_contacto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `distrito` varchar(60) NOT NULL,
  `asunto` varchar(150) DEFAULT NULL,
  `mensaje` text NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
