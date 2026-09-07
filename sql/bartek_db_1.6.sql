-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-09-2026 a las 19:17:15
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bartek_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_menu`
--

CREATE TABLE `categorias_menu` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias_menu`
--

INSERT INTO `categorias_menu` (`id`, `nombre`, `descripcion`, `activa`, `creado_en`) VALUES
(1, 'Cervezas', 'Variedad de cervezas nacionales e importadas.', 1, '2026-05-03 14:21:53'),
(2, 'Vinos', 'Selección de vinos tintos, blancos y rosados.', 1, '2026-05-03 14:21:53'),
(3, 'Cócteles', 'Cócteles clásicos y de autor para cada gusto.', 1, '2026-05-03 14:21:53'),
(4, 'Licores', 'Whiskies, rones, vodkas y más.', 1, '2026-05-03 14:21:53'),
(5, 'Refrescos', 'Bebidas sin alcohol para todos.', 1, '2026-05-03 14:21:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacto_mensajes`
--

CREATE TABLE `contacto_mensajes` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `contacto_mensajes`
--

INSERT INTO `contacto_mensajes` (`id`, `nombre`, `correo`, `mensaje`, `leido`, `creado_en`) VALUES
(1, 'ALAN', 'alanbetancourtbriceno101@gmail.com', 'no me gusto', 0, '2026-07-27 01:08:47'),
(2, 'alan', 'g@gmail.com', 'hola', 0, '2026-07-29 20:35:23'),
(3, 'alan', 'g@gmail.com', 'hola', 0, '2026-07-29 21:35:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id` int(10) UNSIGNED NOT NULL,
  `venta_id` int(10) UNSIGNED NOT NULL,
  `inventario_id` int(10) UNSIGNED NOT NULL,
  `cantidad` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id`, `venta_id`, `inventario_id`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(5, 1, 4, 1, 10.00, 10.00),
(6, 1, 2, 1, 7.50, 7.50),
(8, 2, 55, 3, 4000.00, 12000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `puesto` varchar(80) NOT NULL,
  `departamento` varchar(80) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `usuario_id`, `nombre_completo`, `puesto`, `departamento`, `email`, `telefono`, `activo`, `creado_en`) VALUES
(1, NULL, 'juansito', 'mesero', 'bogota', 'juansito@gmail.com', '3016213528', 1, '2026-06-18 18:03:41'),
(2, NULL, 'julio', 'mesero', 'bogota', 'julioxs@gmail.com', '686767568', 1, '2026-06-18 19:11:53'),
(3, 15, 'alan2', 'gay 1', 'bogota', 'alannose@gmail.com', '1234567890', 0, '2026-08-02 22:00:01'),
(4, 16, 'alanpa', 'mesero', 'General', 'alanpa@gmail.com', '3012836286', 1, '2026-08-08 13:43:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_puntos`
--

CREATE TABLE `historial_puntos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `cedula_cliente` varchar(10) NOT NULL,
  `cantidad_puntos` int(11) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_puntos`
--

INSERT INTO `historial_puntos` (`id`, `nombre`, `cedula_cliente`, `cantidad_puntos`, `tipo`, `fecha`) VALUES
(11, 'mari', '1023578907', 22, 'canjeado', '2026-08-09 01:46:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `empleado_id` int(10) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `estado` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id` int(10) UNSIGNED NOT NULL,
  `categoria_id` int(10) UNSIGNED NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `stock_actual` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `stock_minimo` int(10) UNSIGNED NOT NULL DEFAULT 5,
  `precio_unitario` decimal(10,2) NOT NULL DEFAULT 0.00,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `actualizado_en` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id`, `categoria_id`, `codigo`, `nombre`, `stock_actual`, `stock_minimo`, `precio_unitario`, `activo`, `actualizado_en`, `creado_en`) VALUES
(42, 1, 'BEB006', 'Cerveza Club Colombia Dorada 330ml', 48, 12, 6500.00, 1, '2026-09-03 15:04:00', '2026-09-03 15:04:00'),
(43, 1, 'BEB007', 'Cerveza Corona Extra 355ml', 36, 10, 10000.00, 1, '2026-09-03 15:04:00', '2026-09-03 15:04:00'),
(44, 1, 'BEB008', 'Cerveza BBC Candelaria 330ml', 24, 8, 12000.00, 1, '2026-09-03 15:04:00', '2026-09-03 15:04:00'),
(45, 2, 'BEB009', 'Vino Blanco Sauvignon Blanc 750ml', 10, 4, 6500.00, 1, '2026-09-03 15:04:00', '2026-09-03 15:04:00'),
(46, 2, 'BEB010', 'Vino Rosado Gato Negro 750ml', 8, 3, 48000.00, 1, '2026-09-03 15:04:00', '2026-09-03 15:04:00'),
(47, 3, 'BEB011', 'Mojito Clásico', 50, 10, 22000.00, 1, '2026-09-03 15:04:00', '2026-09-03 15:04:00'),
(48, 3, 'BEB012', 'Margarita de Limón', 40, 10, 25000.00, 1, '2026-09-03 15:04:00', '2026-09-03 15:04:00'),
(49, 3, 'BEB013', 'Gin Tonic Premium', 30, 8, 30000.00, 1, '2026-09-03 15:04:00', '2026-09-03 15:04:00'),
(50, 4, 'BEB014', 'Aguardiente Antioqueño Tapa Azul 750ml', 18, 5, 55000.00, 1, '2026-09-03 15:04:00', '2026-09-03 15:04:00'),
(51, 4, 'BEB015', 'Aguardiente Amarillo de Manzanares 750ml', 15, 5, 70000.00, 1, '2026-09-03 15:04:00', '2026-09-03 15:04:00'),
(52, 4, 'BEB016', 'Ron Viejo de Caldas 3 Años 750ml', 12, 4, 62000.00, 1, '2026-09-03 15:04:00', '2026-09-03 15:04:00'),
(53, 4, 'BEB017', 'Whisky Old Parr 12 Años 750ml', 6, 2, 160000.00, 1, '2026-09-03 15:04:00', '2026-09-03 15:04:00'),
(54, 5, 'BEB018', 'Coca-Cola Sabor Original 300ml', 60, 15, 5000.00, 1, '2026-09-03 15:04:00', '2026-09-03 15:04:00'),
(55, 5, 'BEB019', 'Agua Mineral Nacimiento 500ml', 37, 10, 4000.00, 1, '2026-09-03 15:26:41', '2026-09-03 15:04:00'),
(56, 5, 'BEB020', 'Red Bull Energizante 250ml', 24, 6, 12000.00, 1, '2026-09-03 15:04:00', '2026-09-03 15:04:00'),
(57, 1, 'BEB021', 'Cerveza Stella Artois 330ml', 30, 10, 11000.00, 1, '2026-09-03 15:05:08', '2026-09-03 15:05:08'),
(58, 1, 'BEB022', 'Cerveza Heineken 330ml', 36, 12, 10500.00, 1, '2026-09-03 15:05:08', '2026-09-03 15:05:08'),
(59, 1, 'BEB023', 'Cerveza Poker 330ml', 60, 15, 5000.00, 1, '2026-09-03 15:05:08', '2026-09-03 15:05:08'),
(60, 2, 'BEB024', 'Vino Tinto Cabernet Sauvignon 750ml', 12, 4, 72000.00, 1, '2026-09-03 15:05:08', '2026-09-03 15:05:08'),
(61, 2, 'BEB025', 'Vino Espumoso Prosecco 750ml', 6, 2, 85000.00, 1, '2026-09-03 15:05:08', '2026-09-03 15:05:08'),
(62, 3, 'BEB026', 'Piña Colada', 25, 8, 24000.00, 1, '2026-09-03 15:05:08', '2026-09-03 15:05:08'),
(63, 3, 'BEB027', 'Aperol Spritz', 20, 6, 28000.00, 1, '2026-09-03 15:05:08', '2026-09-03 15:05:08'),
(64, 3, 'BEB028', 'Cuba Libre Premium', 35, 10, 22000.00, 1, '2026-09-03 15:05:08', '2026-09-03 15:05:08'),
(65, 4, 'BEB029', 'Tequila Don Julio Reposado 700ml', 5, 2, 280000.00, 1, '2026-09-03 15:05:08', '2026-09-03 15:05:08'),
(66, 4, 'BEB030', 'Ginebra Tanqueray London Dry 750ml', 8, 3, 135000.00, 0, '2026-09-03 15:39:43', '2026-09-03 15:05:08'),
(67, 4, 'BEB031', 'Whisky Buchanans Deluxe 12 Años 750ml', 10, 3, 175000.00, 1, '2026-09-03 15:05:08', '2026-09-03 15:05:08'),
(68, 4, 'BEB032', 'Jägermeister 700ml', 12, 4, 110000.00, 1, '2026-09-03 15:05:08', '2026-09-03 15:05:08'),
(69, 5, 'BEB033', 'Soda Hatsu Sparkling 300ml', 30, 8, 7000.00, 1, '2026-09-03 15:05:08', '2026-09-03 15:05:08'),
(70, 5, 'BEB034', 'Tónica Mil976 250ml', 24, 6, 6500.00, 1, '2026-09-03 15:05:08', '2026-09-03 15:05:08'),
(71, 5, 'BEB035', 'Jugo de Naranja Natural 350ml', 20, 5, 8000.00, 1, '2026-09-03 15:05:08', '2026-09-03 15:05:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `tipo` enum('pedido','stock','empleado','caja','sistema') NOT NULL DEFAULT 'sistema',
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `tipo`, `titulo`, `descripcion`, `leida`, `creado_en`) VALUES
(1, 'pedido', 'Nuevo pedido en Mesa 5', '2 Cervezas, 1 Ración de Patatas Bravas', 0, '2026-05-03 14:21:53'),
(2, 'stock', 'Alerta de reposición', 'Quedaron pocas existencias de Cerveza Lager.', 0, '2026-05-03 14:21:53'),
(3, 'empleado', 'Nuevo empleado contratado', 'Sofía Rodríguez se ha unido al equipo de camareros.', 0, '2026-05-03 14:21:53'),
(4, 'caja', 'Cierre de caja diario', 'El cierre de caja del día ha sido completado.', 0, '2026-05-03 14:21:53'),
(5, 'pedido', 'Nuevo pedido en Mesa 5', '2 Cervezas, 1 Ración de Patatas Bravas', 0, '2026-05-03 14:35:09'),
(6, 'stock', 'Alerta de reposición', 'Quedaron pocas existencias de Cerveza Lager.', 0, '2026-05-03 14:35:09'),
(7, 'empleado', 'Nuevo empleado contratado', 'Sofía Rodríguez se ha unido al equipo de camareros.', 0, '2026-05-03 14:35:09'),
(8, 'caja', 'Cierre de caja diario', 'El cierre de caja del día ha sido completado.', 0, '2026-05-03 14:35:09'),
(9, 'pedido', 'Nuevo pedido en Mesa 5', '2 Cervezas, 1 Ración de Patatas Bravas', 0, '2026-05-03 14:35:54'),
(10, 'stock', 'Alerta de reposición', 'Quedaron pocas existencias de Cerveza Lager.', 0, '2026-05-03 14:35:54'),
(11, 'empleado', 'Nuevo empleado contratado', 'Sofía Rodríguez se ha unido al equipo de camareros.', 0, '2026-05-03 14:35:54'),
(12, 'caja', 'Cierre de caja diario', 'El cierre de caja del día ha sido completado.', 0, '2026-05-03 14:35:54'),
(13, 'pedido', 'Nuevo pedido en Mesa 5', '2 Cervezas, 1 Ración de Patatas Bravas', 0, '2026-05-03 14:38:17'),
(14, 'stock', 'Alerta de reposición', 'Quedaron pocas existencias de Cerveza Lager.', 0, '2026-05-03 14:38:17'),
(15, 'empleado', 'Nuevo empleado contratado', 'Sofía Rodríguez se ha unido al equipo de camareros.', 0, '2026-05-03 14:38:17'),
(16, 'caja', 'Cierre de caja diario', 'El cierre de caja del día ha sido completado.', 0, '2026-05-03 14:38:17'),
(17, 'pedido', 'Nuevo pedido en Mesa 5', '2 Cervezas, 1 Ración de Patatas Bravas', 0, '2026-05-03 14:41:18'),
(18, 'stock', 'Alerta de reposición', 'Quedaron pocas existencias de Cerveza Lager.', 0, '2026-05-03 14:41:18'),
(19, 'empleado', 'Nuevo empleado contratado', 'Sofía Rodríguez se ha unido al equipo de camareros.', 0, '2026-05-03 14:41:18'),
(20, 'caja', 'Cierre de caja diario', 'El cierre de caja del día ha sido completado.', 0, '2026-05-03 14:41:18'),
(21, 'pedido', 'Nuevo pedido en Mesa 5', '2 Cervezas, 1 Ración de Patatas Bravas', 0, '2026-05-03 14:41:42'),
(22, 'stock', 'Alerta de reposición', 'Quedaron pocas existencias de Cerveza Lager.', 0, '2026-05-03 14:41:42'),
(23, 'empleado', 'Nuevo empleado contratado', 'Sofía Rodríguez se ha unido al equipo de camareros.', 0, '2026-05-03 14:41:42'),
(24, 'caja', 'Cierre de caja diario', 'El cierre de caja del día ha sido completado.', 0, '2026-05-03 14:41:42'),
(25, 'pedido', 'Nuevo pedido en Mesa 5', '2 Cervezas, 1 Ración de Patatas Bravas', 0, '2026-05-03 14:44:50'),
(26, 'stock', 'Alerta de reposición', 'Quedaron pocas existencias de Cerveza Lager.', 0, '2026-05-03 14:44:50'),
(27, 'empleado', 'Nuevo empleado contratado', 'Sofía Rodríguez se ha unido al equipo de camareros.', 0, '2026-05-03 14:44:50'),
(28, 'caja', 'Cierre de caja diario', 'El cierre de caja del día ha sido completado.', 0, '2026-05-03 14:44:50'),
(29, 'pedido', 'Nuevo pedido en Mesa 5', '2 Cervezas, 1 Ración de Patatas Bravas', 0, '2026-05-03 14:50:44'),
(30, 'stock', 'Alerta de reposición', 'Quedaron pocas existencias de Cerveza Lager.', 0, '2026-05-03 14:50:44'),
(31, 'empleado', 'Nuevo empleado contratado', 'Sofía Rodríguez se ha unido al equipo de camareros.', 0, '2026-05-03 14:50:44'),
(32, 'caja', 'Cierre de caja diario', 'El cierre de caja del día ha sido completado.', 0, '2026-05-03 14:50:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `nombre_cliente` varchar(100) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `numero_mesa` int(11) NOT NULL,
  `personas` int(11) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id`, `nombre_cliente`, `fecha`, `hora`, `numero_mesa`, `personas`, `telefono`, `creado_en`) VALUES
(1, 'a alan pe', '2026-08-11', '18:00:00', 7, 3, '3016213528', '2026-08-09 00:44:53'),
(2, 'a maria ca', '2026-08-10', '18:00:00', 9, 2, '3016213528', '2026-08-09 02:22:23'),
(3, 'a lana ma', '2026-08-12', '18:00:00', 8, 3, '3016213528', '2026-08-09 02:40:21'),
(4, 'julio papu', '2026-08-14', '17:00:00', 5, 2, '308237649', '2026-08-10 00:46:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(40) NOT NULL,
  `apellido` varchar(40) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `usuario` varchar(60) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('dueno','empleado') NOT NULL DEFAULT 'empleado',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `foto` varchar(255) DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  `token_reset` varchar(64) DEFAULT NULL,
  `token_expira` int(10) UNSIGNED DEFAULT NULL,
  `solicitud_reset` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `telefono`, `usuario`, `contrasena`, `rol`, `activo`, `foto`, `creado_en`, `token_reset`, `token_expira`, `solicitud_reset`) VALUES
(7, 'Administrador', '', 'admin@ejemplo.com', '555-0001', 'admin', '$2y$12$8EeZfyfM6eUgVHhemCeJHeMucbar8vrveU5aq9nwNmGRpTgs9CRT.', 'dueno', 1, NULL, '2026-05-03 14:44:50', NULL, NULL, 0),
(9, 'Julio santana', '', 'ejemplo@ejemplo.com', NULL, 'empleado', '$2y$12$seaFZCsF9Ep7B8obzZHcPOjXLubQ5RDjok.M2izGS3nofwl4XVq6i', 'empleado', 1, NULL, '2026-05-03 14:50:44', NULL, NULL, 0),
(10, 'maria', '', 'maria@ejemplo.com', '45644655', 'mariabb', '$2y$12$4kKFqUtXdEr7kNzlgKXZKekelffBZAyuSp1VUgqN7Nvxb/oXBDegm', 'dueno', 1, NULL, '2026-05-21 15:50:53', NULL, NULL, 0),
(11, 'diego', 'arevalo', 'digo@gmail.com', '5437656876', 'diegobb', '$2y$12$3pJIBWyAZYqFf7gL/AThgun901oF65NYFiXGy435vbMyZi/7z29Iu', 'dueno', 1, NULL, '2026-05-21 15:59:29', NULL, NULL, 0),
(12, 'alan', 'brito', 'lan@ejemplo.com', '1234565487', 'alanbrito', '$2y$12$ekKiwfyQKzmGHHvoed6dCeqznQ9n056anlnp5r3NngG1wFdbFk36O', 'dueno', 1, NULL, '2026-05-21 16:10:33', NULL, NULL, 0),
(13, 'asdsadasd', 'sdasdasd', 'asdasdq@asda.com', '', 'asdasd', '$2y$12$EmzO9lmbj0Jcegk3m5iA9.LkK/RKntXLHjHn3Xcz552wHNe2k49DK', 'dueno', 1, NULL, '2026-05-21 16:12:25', NULL, NULL, 0),
(14, 'ALAN', 'BRICEÑO', 'alanbetancourtbriceno101@gmail.com', '32343432', 'alita', '$2y$12$YZgJDUQyLeP9Nk4c71OyS.lRvy.dIO3x9ulzhjKTdqY9O36CdH.5S', 'dueno', 1, NULL, '2026-05-22 16:45:43', NULL, NULL, 0),
(15, 'alan2', '', 'alannose@gmail.com', '1234567890', 'alitan2', '$2y$12$5II.Z2pDTMaW.S4DzzAYIeoJev7feEFHX8yxiRCsLmNTlq/TYz1Jy', 'empleado', 1, NULL, '2026-08-02 22:00:01', NULL, NULL, 0),
(16, 'alanpa', '', 'alanpa@gmail.com', '3012836286', 'alanpa', '$2y$12$PDJGq521RH/pDhpkOIbMRuaMHbjh7CmSY5JhOG/p8Wpve4clND/Ze', 'empleado', 1, NULL, '2026-08-08 13:43:54', NULL, NULL, 0),
(17, 'julio', 'ando', 'juliosantanamb@gmail.com', '3112348287', 'koi32', '$2y$12$pUr6R5aFE4i3w4sf5yE5Heygh7Bj4NeKyPyCuLceapOWtSkp2n8cC', 'dueno', 1, NULL, '2026-08-26 18:12:19', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(10) UNSIGNED NOT NULL,
  `empleado_id` int(10) UNSIGNED DEFAULT NULL,
  `mesa` varchar(30) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` enum('abierto','cerrado','cancelado') NOT NULL DEFAULT 'abierto',
  `metodo_pago` enum('efectivo','tarjeta_credito','nequi_daviplata','bre_b') DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  `cerrado_en` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `empleado_id`, `mesa`, `total`, `estado`, `metodo_pago`, `creado_en`, `cerrado_en`) VALUES
(1, NULL, '1', 17.50, 'cerrado', 'efectivo', '2026-08-08 13:44:17', '2026-09-03 14:58:48'),
(2, 4, '1', 12000.00, 'cerrado', 'bre_b', '2026-09-03 15:26:27', '2026-09-03 15:26:41');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias_menu`
--
ALTER TABLE `categorias_menu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_categoria_nombre` (`nombre`);

--
-- Indices de la tabla `contacto_mensajes`
--
ALTER TABLE `contacto_mensajes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `inventario_id` (`inventario_id`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `historial_puntos`
--
ALTER TABLE `historial_puntos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empleado_id` (`empleado_id`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empleado_id` (`empleado_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias_menu`
--
ALTER TABLE `categorias_menu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `contacto_mensajes`
--
ALTER TABLE `contacto_mensajes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `historial_puntos`
--
ALTER TABLE `historial_puntos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
