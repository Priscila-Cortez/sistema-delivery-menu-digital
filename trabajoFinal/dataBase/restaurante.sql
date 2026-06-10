-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-06-2026 a las 03:36:57
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
-- Base de datos: `restaurante`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos_delivery`
--

CREATE TABLE `pedidos_delivery` (
  `id` int(11) NOT NULL,
  `cliente` varchar(100) NOT NULL,
  `direccion` varchar(150) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `total_pagar` decimal(10,2) NOT NULL,
  `fecha_pedido` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos_delivery`
--

INSERT INTO `pedidos_delivery` (`id`, `cliente`, `direccion`, `telefono`, `total_pagar`, `fecha_pedido`) VALUES
(1, 'Juan Pérez', 'Av. Larco 456, Trujillo', '987654321', 77.50, '2026-05-28 23:46:17'),
(2, 'María Mendoza', 'Calle Las Flores 123, California', '912345678', 50.00, '2026-05-28 23:46:17'),
(3, 'Cielo Burga', 'Av. Mansiche 450', '99559944', 77.50, '2026-05-31 20:26:02'),
(14, 'DANTE MOISES CORTEZ POMPA', 'Panamericana norte 1610 (Frente a comercial linares)', '937199293', 110.00, '2026-06-03 21:38:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `platos_menu`
--

CREATE TABLE `platos_menu` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `tiempo_preparacion` int(11) NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'Disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `platos_menu`
--

INSERT INTO `platos_menu` (`id`, `codigo`, `nombre`, `categoria`, `precio`, `tiempo_preparacion`, `estado`) VALUES
(1, 'ENT-01', 'Ceviche Clásico', 'Entradas', 35.00, 10, 'Disponible'),
(2, 'FON-01', 'Lomo Saltado Criollo', 'Fondos', 40.00, 15, 'Disponible'),
(4, 'POS-01', 'Suspiro a la Limeña', 'Postres', 12.00, 8, 'Agotado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(30) NOT NULL DEFAULT 'Administrador'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_completo`, `usuario`, `password`, `rol`) VALUES
(1, 'Priscila Cortez', 'priscila_admin', 'B130613', 'Administrador');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pedidos_delivery`
--
ALTER TABLE `pedidos_delivery`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `platos_menu`
--
ALTER TABLE `platos_menu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pedidos_delivery`
--
ALTER TABLE `pedidos_delivery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `platos_menu`
--
ALTER TABLE `platos_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
