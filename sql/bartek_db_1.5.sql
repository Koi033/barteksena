-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 10, 2026 at 02:56 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12
SET SESSION sql_require_primary_key = OFF;
SET FOREIGN_KEY_CHECKS = 0;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bartek_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categorias_menu`
--

CREATE TABLE `categorias_menu` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categorias_menu`
--

INSERT INTO `categorias_menu` (`id`, `nombre`, `descripcion`, `activa`, `creado_en`) VALUES
(1, 'Cervezas', 'Variedad de cervezas nacionales e importadas.', 1, '2026-05-03 14:21:53'),
(2, 'Vinos', 'Selección de vinos tintos, blancos y rosados.', 1, '2026-05-03 14:21:53'),
(3, 'Cócteles', 'Cócteles clásicos y de autor para cada gusto.', 1, '2026-05-03 14:21:53'),
(4, 'Licores', 'Whiskies, rones, vodkas y más.', 1, '2026-05-03 14:21:53'),
(5, 'Refrescos', 'Bebidas sin alcohol para todos.', 1, '2026-05-03 14:21:53');

-- --------------------------------------------------------

--
-- Table structure for table `contacto_mensajes`
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
-- Dumping data for table `contacto_mensajes`
--

INSERT INTO `contacto_mensajes` (`id`, `nombre`, `correo`, `mensaje`, `leido`, `creado_en`) VALUES
(1, 'ALAN', 'alanbetancourtbriceno101@gmail.com', 'no me gusto', 0, '2026-07-27 01:08:47'),
(2, 'alan', 'g@gmail.com', 'hola', 0, '2026-07-29 20:35:23'),
(3, 'alan', 'g@gmail.com', 'hola', 0, '2026-07-29 21:35:42');

-- --------------------------------------------------------

--
-- Table structure for table `detalle_ventas`
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
-- Dumping data for table `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id`, `venta_id`, `inventario_id`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 4, 1, 10.00, 10.00),
(2, 1, 2, 1, 7.50, 7.50),
(3, 2, 2, 1, 7.50, 7.50),
(4, 2, 41, 1, 1000000.00, 1000000.00);

-- --------------------------------------------------------

--
-- Table structure for table `empleados`
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
-- Dumping data for table `empleados`
--

INSERT INTO `empleados` (`id`, `usuario_id`, `nombre_completo`, `puesto`, `departamento`, `email`, `telefono`, `activo`, `creado_en`) VALUES
(1, NULL, 'juansito', 'mesero', 'bogota', 'juansito@gmail.com', '3016213528', 1, '2026-06-18 18:03:41'),
(2, NULL, 'julio', 'mesero', 'bogota', 'julioxs@gmail.com', '686767568', 1, '2026-06-18 19:11:53'),
(3, 15, 'alan2', 'gay 1', 'bogota', 'alannose@gmail.com', '1234567890', 0, '2026-08-02 22:00:01'),
(4, 16, 'alanpa', 'mesero', 'General', 'alanpa@gmail.com', '3012836286', 1, '2026-08-08 13:43:54');

-- --------------------------------------------------------

--
-- Table structure for table `historial_puntos`
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
-- Dumping data for table `historial_puntos`
--

INSERT INTO `historial_puntos` (`id`, `nombre`, `cedula_cliente`, `cantidad_puntos`, `tipo`, `fecha`) VALUES
(11, 'mari', '1023578907', 22, 'canjeado', '2026-08-09 01:46:58');

-- --------------------------------------------------------

--
-- Table structure for table `horarios`
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
-- Table structure for table `inventario`
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
-- Dumping data for table `inventario`
--

INSERT INTO `inventario` (`id`, `categoria_id`, `codigo`, `nombre`, `stock_actual`, `stock_minimo`, `precio_unitario`, `activo`, `actualizado_en`, `creado_en`) VALUES
(1, 4, 'BEB001', 'Whisky Escocés', 15, 5, 16.00, 1, '2026-05-03 14:21:53', '2026-05-03 14:21:53'),
(2, 1, 'BEB002', 'Cerveza Artesanal IPA', 6, 5, 7.50, 1, '2026-08-08 20:00:20', '2026-05-03 14:21:53'),
(3, 2, 'BEB003', 'Vino Tinto Malbec', 3, 5, 12.00, 1, '2026-05-03 14:21:53', '2026-05-03 14:21:53'),
(4, 4, 'BEB004', 'Ron Añejo', 24, 5, 10.00, 1, '2026-08-08 13:44:17', '2026-05-03 14:21:53'),
(5, 4, 'BEB005', 'Vodka Premium', 6, 5, 4.00, 1, '2026-05-03 14:21:53', '2026-05-03 14:21:53'),
(41, 2, '456645', 'tula', 9, 15, 1000000.00, 1, '2026-08-08 20:00:20', '2026-05-03 18:39:52');

-- --------------------------------------------------------

--
-- Table structure for table `notificaciones`
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
-- Dumping data for table `notificaciones`
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
-- Table structure for table `reservas`
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
-- Dumping data for table `reservas`
--

INSERT INTO `reservas` (`id`, `nombre_cliente`, `fecha`, `hora`, `numero_mesa`, `personas`, `telefono`, `creado_en`) VALUES
(1, 'a alan pe', '2026-08-11', '18:00:00', 7, 3, '3016213528', '2026-08-09 00:44:53'),
(2, 'a maria ca', '2026-08-10', '18:00:00', 9, 2, '3016213528', '2026-08-09 02:22:23'),
(3, 'a lana ma', '2026-08-12', '18:00:00', 8, 3, '3016213528', '2026-08-09 02:40:21'),
(4, 'julio papu', '2026-08-14', '17:00:00', 5, 2, '308237649', '2026-08-10 00:46:06');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
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
-- Dumping data for table `usuarios`
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
(16, 'alanpa', '', 'alanpa@gmail.com', '3012836286', 'alanpa', '$2y$12$PDJGq521RH/pDhpkOIbMRuaMHbjh7CmSY5JhOG/p8Wpve4clND/Ze', 'empleado', 1, NULL, '2026-08-08 13:43:54', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ventas`
--

CREATE TABLE `ventas` (
  `id` int(10) UNSIGNED NOT NULL,
  `empleado_id` int(10) UNSIGNED DEFAULT NULL,
  `mesa` varchar(30) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` enum('abierto','cerrado','cancelado') NOT NULL DEFAULT 'abierto',
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  `cerrado_en` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ventas`
--

INSERT INTO `ventas` (`id`, `empleado_id`, `mesa`, `total`, `estado`, `creado_en`, `cerrado_en`) VALUES
(1, NULL, '1', 17.50, 'abierto', '2026-08-08 13:44:17', NULL),
(2, NULL, '2', 1000007.50, 'abierto', '2026-08-08 20:00:20', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categorias_menu`
--
ALTER TABLE `categorias_menu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_categoria_nombre` (`nombre`);

--
-- Indexes for table `contacto_mensajes`
--
ALTER TABLE `contacto_mensajes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `inventario_id` (`inventario_id`);

--
-- Indexes for table `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `historial_puntos`
--
ALTER TABLE `historial_puntos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empleado_id` (`empleado_id`);

--
-- Indexes for table `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indexes for table `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indexes for table `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empleado_id` (`empleado_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categorias_menu`
--
ALTER TABLE `categorias_menu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contacto_mensajes`
--
ALTER TABLE `contacto_mensajes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `historial_puntos`
--
ALTER TABLE `historial_puntos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_ventas_ibfk_2` FOREIGN KEY (`inventario_id`) REFERENCES `inventario` (`id`);

--
-- Constraints for table `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_menu` (`id`);

--
-- Constraints for table `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE SET NULL;
COMMIT;
SET FOREIGN_KEY_CHECKS = 1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
