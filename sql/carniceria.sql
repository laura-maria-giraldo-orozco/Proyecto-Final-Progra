-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-11-2025 a las 19:59:18
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
-- Base de datos: `carniceria`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `usuario_id`, `fecha`, `total`) VALUES
(1, 2, '2024-01-15 10:30:00', 85970.00),
(2, 2, '2024-01-20 14:15:00', 25980.00),
(3, 3, '2024-01-18 11:00:00', 112970.00),
(4, 3, '2024-01-22 16:45:00', 43980.00),
(5, 4, '2024-01-19 09:30:00', 78960.00),
(6, 4, '2024-01-21 13:20:00', 82970.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compra`
--

CREATE TABLE `detalle_compra` (
  `id` int(11) NOT NULL,
  `compra_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_compra`
--

INSERT INTO `detalle_compra` (`id`, `compra_id`, `producto_id`, `cantidad`, `precio`) VALUES
(1, 1, 1, 1, 23990.00),
(2, 1, 5, 2, 13990.00),
(4, 2, 3, 2, 12990.00),
(6, 3, 1, 2, 23990.00),
(7, 3, 7, 1, 20000.00),
(8, 3, 14, 1, 30000.00),
(9, 3, 17, 1, 14990.00),
(10, 4, 4, 2, 16990.00),
(11, 4, 10, 2, 5000.00),
(12, 5, 2, 2, 19990.00),
(13, 5, 6, 1, 16990.00),
(14, 5, 13, 1, 21990.00),
(16, 6, 15, 2, 8990.00),
(17, 6, 21, 1, 13990.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `stock` int(11) NOT NULL CHECK (`stock` >= 0),
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `categoria`, `stock`, `imagen`) VALUES
(1, 'Filete de Res Premium', 'Filete de res de primera calidad, tierno y jugoso. Ideal para asar o freír. Origen: Ganado nacional.', 23990.00, 'Res', 25, '6913ce76731e4.webp'),
(2, 'Costilla de Res', 'Costilla de res fresca, perfecta para parrilla o estofado. Corte premium con buen marmoleo.', 19990.00, 'Res', 18, '6913ce7d38374.jpeg'),
(3, 'Carne Molida', 'Carne molida de res 80/20, fresca del día. Ideal para hamburguesas, albóndigas y pastas.', 12990.00, 'Res', 28, '6913ce841879b.jpg'),
(4, 'Bistec de Res', 'Bistec de res cortado fino, tierno y sabroso. Perfecto para freír o saltear.', 16990.00, 'Res', 13, '6913ce8b6a7ec.webp'),
(5, 'Chuleta de Cerdo', 'Chuleta de cerdo gruesa y jugosa, ideal para asar o freír. Origen nacional.', 13990.00, 'Cerdo', 28, '6913ce93b391d.png'),
(6, 'Costilla de Cerdo', 'Costilla de cerdo tierna, perfecta para barbacoa o parrilla. Con hueso.', 16990.00, 'Cerdo', 20, '6913ce9b493dc.jpeg'),
(7, 'Lomo de Cerdo', 'Lomo de cerdo premium, tierno y sin grasa. Ideal para hornear o asar.', 20000.00, 'Cerdo', 15, '6913cea4cf77b.jpeg'),
(8, 'Carne Molida de Cerdo', 'Carne molida de cerdo fresca, perfecta para albóndigas y empanadas.', 13990.00, 'Cerdo', 25, '6913ceae44268.jpeg'),
(10, 'Muslo de Pollo', 'Muslo de pollo con hueso, sabroso y económico. Perfecto para estofados y guisos.', 5000.00, 'Pollo', 40, '6913cec2e97d3.png'),
(12, 'Pollo Entero', 'Pollo entero fresco, limpio y listo para cocinar. Peso aproximado: 1.5 - 2 kg.', 24000.00, 'Pollo', 12, '6913ceca80a8a.webp'),
(13, 'Chorizo Español', 'Chorizo español artesanal, curado y con especias tradicionales. Listo para consumir.', 21990.00, 'Embutidos', 18, '6913cee49a1ac.jpg'),
(14, 'Jamón Serrano', 'Jamón serrano curado de primera calidad, cortado fino. Origen español.', 30000.00, 'Embutidos', 15, '6913cfbc509e6.webp'),
(15, 'Salchichas', 'Salchichas frescas tipo frankfurt, ideales para perros calientes y asados.', 8990.00, 'Embutidos', 30, '6913d0018af32.jpg'),
(16, 'Morcilla', 'Morcilla tradicional, con arroz y especias. Ideal para cocidos y guisos.', 12000.00, 'Embutidos', 18, '6913d009126b6.webp'),
(17, 'Longaniza', 'Longaniza fresca, perfecta para asar o freír. Tradicional y sabrosa.', 14990.00, 'Embutidos', 22, '6913d01172afe.png'),
(18, 'Hígado de Res', 'Hígado de res fresco, rico en hierro. Ideal para freír o guisar.', 9990.00, 'Otros', 10, '6913d01cbbba2.png'),
(19, 'Riñones de Res', 'Riñones de res frescos, limpios y listos para cocinar. Tradicional en guisos.', 11990.00, 'Otros', 8, '6913d0242879a.jpg'),
(20, 'Lengua de Res', 'Lengua de res tierna, perfecta para cocidos y guisos tradicionales.', 14990.00, 'Otros', 6, '6913d02bd95a6.webp'),
(21, 'Mollejas', 'Mollejas de pollo frescas, tiernas y delicadas. Ideal para asar o freír.', 13990.00, 'Otros', 12, '6913d032c131e.jpg'),
(23, 'Pechuga de pollo', 'una descripción bien interesante', 25000.00, 'Pollo', 52, '6918b3c990a71.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','usuario') DEFAULT 'usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`) VALUES
(1, 'Administrador', 'admin@carniceria.com', '$2y$10$ppSylsqz60Zle22V36aVVexDHsXq4L.Q6ML/rZCzmNS/K.YFUDHNW', 'admin'),
(2, 'Juan Pérez', 'juan.perez@email.com', '$2y$10$ppSylsqz60Zle22V36aVVexDHsXq4L.Q6ML/rZCzmNS/K.YFUDHNW', 'usuario'),
(3, 'María García', 'maria.garcia@email.com', '$2y$10$ppSylsqz60Zle22V36aVVexDHsXq4L.Q6ML/rZCzmNS/K.YFUDHNW', 'usuario'),
(4, 'Carlos López', 'carlos.lopez@email.com', '$2y$10$ppSylsqz60Zle22V36aVVexDHsXq4L.Q6ML/rZCzmNS/K.YFUDHNW', 'usuario'),
(5, 'Ana Martínez', 'ana.martinez@email.com', '$2y$10$ppSylsqz60Zle22V36aVVexDHsXq4L.Q6ML/rZCzmNS/K.YFUDHNW', 'usuario');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `compra_id` (`compra_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD CONSTRAINT `detalle_compra_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`),
  ADD CONSTRAINT `detalle_compra_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
