-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Nov 22, 2025 at 08:28 PM
-- Server version: 8.1.0
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `TiendaOnline`
--

-- --------------------------------------------------------

--
-- Table structure for table `Carritos`
--

CREATE TABLE `Carritos` (
  `id_carrito` int NOT NULL,
  `id_usuario` int NOT NULL,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Compras`
--

CREATE TABLE `Compras` (
  `id_compra` int NOT NULL,
  `id_usuario` int NOT NULL,
  `fecha_compra` datetime DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Detalle_Carrito`
--

CREATE TABLE `Detalle_Carrito` (
  `id_detalle_carrito` int NOT NULL,
  `id_carrito` int NOT NULL,
  `id_producto` int NOT NULL,
  `cantidad` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Detalle_Compras`
--

CREATE TABLE `Detalle_Compras` (
  `id_detalle` int NOT NULL,
  `id_compra` int NOT NULL,
  `id_producto` int NOT NULL,
  `cantidad` int DEFAULT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Fotos_Producto`
--

CREATE TABLE `Fotos_Producto` (
  `id_fotos` int NOT NULL,
  `foto1` mediumblob NOT NULL,
  `foto2` mediumblob,
  `foto3` mediumblob,
  `foto4` mediumblob,
  `foto5` mediumblob
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Productos`
--

CREATE TABLE `Productos` (
  `id_producto` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `id_fotos` int DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `cantidad_en_almacen` int DEFAULT NULL,
  `fabricante` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origen` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Usuarios`
--

CREATE TABLE `Usuarios` (
  `id_usuario` int NOT NULL,
  `nombre_usuario` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo_electronico` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contrasena` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `numero_tarjeta_bancaria` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion_postal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Usuarios`
--

INSERT INTO `Usuarios` (`id_usuario`, `nombre_usuario`, `correo_electronico`, `contrasena`, `fecha_nacimiento`, `numero_tarjeta_bancaria`, `direccion_postal`, `fecha_registro`) VALUES
(1, 'prueba', 'prueba@prueba', '$2y$12$OQh7Yv6H5ZINPT1CGYOsMOlu8kjATEk07itrxHvMXFWEaKCuAt9q6', '2001-01-01', '213123131321312', '12345', '2025-11-22 20:03:42'),
(2, 'prueba2', 'prueba2@prueba2', '$2y$12$4pJ0DgVNaWr.mIjD14Wx3uIhKA9BmJNGUkgcRdZlee2Ba/ewVxQ3O', '2002-02-02', '1231231313131212', '31212', '2025-11-22 20:14:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Carritos`
--
ALTER TABLE `Carritos`
  ADD PRIMARY KEY (`id_carrito`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indexes for table `Compras`
--
ALTER TABLE `Compras`
  ADD PRIMARY KEY (`id_compra`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indexes for table `Detalle_Carrito`
--
ALTER TABLE `Detalle_Carrito`
  ADD PRIMARY KEY (`id_detalle_carrito`),
  ADD KEY `id_carrito` (`id_carrito`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indexes for table `Detalle_Compras`
--
ALTER TABLE `Detalle_Compras`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_compra` (`id_compra`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indexes for table `Fotos_Producto`
--
ALTER TABLE `Fotos_Producto`
  ADD PRIMARY KEY (`id_fotos`);

--
-- Indexes for table `Productos`
--
ALTER TABLE `Productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_fotos` (`id_fotos`);

--
-- Indexes for table `Usuarios`
--
ALTER TABLE `Usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Carritos`
--
ALTER TABLE `Carritos`
  MODIFY `id_carrito` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Compras`
--
ALTER TABLE `Compras`
  MODIFY `id_compra` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Detalle_Carrito`
--
ALTER TABLE `Detalle_Carrito`
  MODIFY `id_detalle_carrito` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Detalle_Compras`
--
ALTER TABLE `Detalle_Compras`
  MODIFY `id_detalle` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Fotos_Producto`
--
ALTER TABLE `Fotos_Producto`
  MODIFY `id_fotos` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Productos`
--
ALTER TABLE `Productos`
  MODIFY `id_producto` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Usuarios`
--
ALTER TABLE `Usuarios`
  MODIFY `id_usuario` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Carritos`
--
ALTER TABLE `Carritos`
  ADD CONSTRAINT `Carritos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `Usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Compras`
--
ALTER TABLE `Compras`
  ADD CONSTRAINT `Compras_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `Usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Detalle_Carrito`
--
ALTER TABLE `Detalle_Carrito`
  ADD CONSTRAINT `Detalle_Carrito_ibfk_1` FOREIGN KEY (`id_carrito`) REFERENCES `Carritos` (`id_carrito`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Detalle_Carrito_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `Productos` (`id_producto`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `Detalle_Compras`
--
ALTER TABLE `Detalle_Compras`
  ADD CONSTRAINT `Detalle_Compras_ibfk_1` FOREIGN KEY (`id_compra`) REFERENCES `Compras` (`id_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Detalle_Compras_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `Productos` (`id_producto`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `Productos`
--
ALTER TABLE `Productos`
  ADD CONSTRAINT `Productos_ibfk_1` FOREIGN KEY (`id_fotos`) REFERENCES `Fotos_Producto` (`id_fotos`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
