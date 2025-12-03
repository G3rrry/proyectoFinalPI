-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Dec 03, 2025 at 08:26 PM
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

--
-- Dumping data for table `Carritos`
--

INSERT INTO `Carritos` (`id_carrito`, `id_usuario`, `fecha_creacion`) VALUES
(1, 1, '2025-11-26 19:17:59');

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

--
-- Dumping data for table `Compras`
--

INSERT INTO `Compras` (`id_compra`, `id_usuario`, `fecha_compra`, `total`) VALUES
(1, 1, '2025-11-26 19:18:45', 369.09),
(2, 1, '2025-11-26 19:20:37', 49.00);

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

--
-- Dumping data for table `Detalle_Compras`
--

INSERT INTO `Detalle_Compras` (`id_detalle`, `id_compra`, `id_producto`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 1, 12, 19.99),
(2, 1, 2, 1, 24.50),
(3, 1, 7, 1, 16.75),
(4, 1, 11, 4, 21.99),
(5, 2, 2, 2, 24.50);

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

--
-- Dumping data for table `Productos`
--

INSERT INTO `Productos` (`id_producto`, `nombre`, `descripcion`, `id_fotos`, `precio`, `cantidad_en_almacen`, `fabricante`, `origen`) VALUES
(1, 'Cien Años de Soledad', 'Novela emblemática de Gabriel García Márquez.', NULL, 19.99, 38, 'Editorial Sudamericana', 'Colombia'),
(2, 'El Señor de los Anillos: La Comunidad del Anillo', 'Primera parte de la famosa trilogía de Tolkien.', NULL, 24.50, 37, 'Minotauro', 'Reino Unido'),
(3, '1984', 'Distopía clásica escrita por George Orwell.', NULL, 14.99, 60, 'Secker & Warburg', 'Reino Unido'),
(4, 'Orgullo y Prejuicio', 'Obra romántica de Jane Austen.', NULL, 12.99, 45, 'T. Egerton', 'Reino Unido'),
(5, 'Crónica de una Muerte Anunciada', 'Novela corta de Gabriel García Márquez.', NULL, 11.50, 30, 'Editorial La Oveja Negra', 'Colombia'),
(6, 'Harry Potter y la Piedra Filosofal', 'Primera entrega de la saga de Harry Potter.', NULL, 18.50, 70, 'Bloomsbury', 'Reino Unido'),
(7, 'El Código Da Vinci', 'Thriller de misterio de Dan Brown.', NULL, 16.75, 54, 'Doubleday', 'Estados Unidos'),
(8, 'El Alquimista', 'Novela de Paulo Coelho sobre el destino y los sueños.', NULL, 13.99, 80, 'HarperCollins', 'Brasil'),
(9, 'Don Quijote de la Mancha', 'Clásico de la literatura española.', NULL, 22.90, 35, 'Francisco de Robles', 'España'),
(10, 'Rayuela', 'Novela experimental de Julio Cortázar.', NULL, 17.50, 25, 'Sudamericana', 'Argentina'),
(11, 'La Sombra del Viento', 'Primera novela de la saga El Cementerio de los Libros Olvidados.', NULL, 21.99, 46, 'Planeta', 'España'),
(12, 'Fahrenheit 451', 'Distopía de Ray Bradbury sobre un mundo sin libros.', NULL, 12.50, 40, 'Ballantine Books', 'Estados Unidos'),
(13, 'El Principito', 'Obra filosófica de Antoine de Saint-Exupéry.', NULL, 10.99, 90, 'Reynal & Hitchcock', 'Francia'),
(14, 'Matar a un Ruiseñor', 'Novela sobre injusticia racial de Harper Lee.', NULL, 14.50, 45, 'J. B. Lippincott & Co.', 'Estados Unidos'),
(15, 'Drácula', 'Clásico de terror de Bram Stoker.', NULL, 15.99, 30, 'Archibald Constable and Company', 'Irlanda'),
(16, 'El Hobbit', 'Aventura previa a El Señor de los Anillos.', NULL, 18.75, 60, 'George Allen & Unwin', 'Reino Unido'),
(17, 'La Metamorfosis', 'Novela de Franz Kafka.', NULL, 9.99, 80, 'Kurt Wolff Verlag', 'Austria'),
(18, 'El Perfume', 'Historia de un asesino con un olfato extraordinario.', NULL, 16.40, 50, 'Diogenes Verlag', 'Alemania'),
(19, 'Los Juegos del Hambre', 'Primera parte de la trilogía de Suzanne Collins.', NULL, 17.50, 70, 'Scholastic', 'Estados Unidos'),
(20, 'It', 'Novela de terror de Stephen King.', NULL, 25.99, 40, 'Viking Press', 'Estados Unidos'),
(21, 'La Isla del Tesoro', 'Clásico de aventuras de Robert Louis Stevenson.', NULL, 11.99, 60, 'Cassell and Company', 'Reino Unido'),
(22, 'El Nombre del Viento', 'Primera novela de Patrick Rothfuss.', NULL, 23.50, 35, 'DAW Books', 'Estados Unidos'),
(23, 'Dune', 'Novela épica de ciencia ficción de Frank Herbert.', NULL, 26.75, 50, 'Chilton Books', 'Estados Unidos'),
(24, 'Neuromante', 'Obra fundacional del cyberpunk.', NULL, 19.25, 30, 'Ace Books', 'Estados Unidos'),
(25, 'La Historia Interminable', 'Fantasía juvenil escrita por Michael Ende.', NULL, 20.99, 40, 'Thienemann Verlag', 'Alemania'),
(26, 'El Padrino', 'Novela de mafia escrita por Mario Puzo.', NULL, 18.99, 55, 'G. P. Putnam\'s Sons', 'Estados Unidos'),
(27, 'Los Miserables', 'Clásico de Victor Hugo sobre justicia y redención.', NULL, 24.99, 20, 'A. Lacroix, Verboeckhoven & Cie', 'Francia'),
(28, 'Cumbres Borrascosas', 'Novela de Emily Brontë.', NULL, 14.75, 45, 'Thomas Cautley Newby', 'Reino Unido'),
(29, 'El Retrato de Dorian Gray', 'Novela de Oscar Wilde sobre la corrupción moral.', NULL, 13.25, 70, 'Ward, Lock & Co.', 'Reino Unido'),
(30, 'La Odisea', 'Epopeya de Homero.', NULL, 19.50, 30, 'Dominio Público', 'Grecia');

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
  MODIFY `id_carrito` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Compras`
--
ALTER TABLE `Compras`
  MODIFY `id_compra` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Detalle_Carrito`
--
ALTER TABLE `Detalle_Carrito`
  MODIFY `id_detalle_carrito` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Detalle_Compras`
--
ALTER TABLE `Detalle_Compras`
  MODIFY `id_detalle` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Fotos_Producto`
--
ALTER TABLE `Fotos_Producto`
  MODIFY `id_fotos` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Productos`
--
ALTER TABLE `Productos`
  MODIFY `id_producto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

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
