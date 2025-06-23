-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 23-06-2025 a las 23:52:00
-- Versión del servidor: 10.11.13-MariaDB
-- Versión de PHP: 8.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `wp_fp6zg`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_citas`
--

CREATE TABLE `wp_citas` (
  `id` int(10) UNSIGNED NOT NULL,
  `fecha_cita` date NOT NULL,
  `hora_cita` time NOT NULL,
  `nombre_cliente` varchar(100) NOT NULL,
  `email_cliente` varchar(100) NOT NULL,
  `tel_cliente` varchar(30) NOT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `wp_citas`
--

INSERT INTO `wp_citas` (`id`, `fecha_cita`, `hora_cita`, `nombre_cliente`, `email_cliente`, `tel_cliente`, `creado`) VALUES
(1, '2025-06-16', '08:00:00', 'Juanfran', 'juanfran@coditeca.es', '666142995', '2025-06-18 13:09:56'),
(3, '2025-06-16', '20:00:00', 'JUAN FRANCISCO', 'jfespinr@gmail.com', '+34665559669111', '2025-06-18 13:32:28'),
(6, '2025-06-24', '17:00:00', 'Pepe Luis PRUEBA', 'jfespin@gmail.com', '+34665559669', '2025-06-19 11:24:54'),
(8, '2025-06-23', '12:00:00', 'Maria Jesus Dominguez Simon', 'secretaria@federacion-matronas.org', '+34913300565', '2025-06-19 13:59:28'),
(9, '2025-06-23', '19:00:00', 'MarÃ­a Dolores Riquelme', 'jfespinr@gmail.com', '+34665559669', '2025-06-19 14:00:03'),
(10, '2025-06-25', '10:00:00', 'ENCARNA DÃVILA', 'jfespin@gmail.com', '+34665559669', '2025-06-19 14:01:15'),
(13, '2025-06-27', '18:00:00', 'JUAN FRANCISCO ESPÃN RIQUELME', 'jfespinr@gmail.com', '634448893', '2025-06-19 14:07:03'),
(14, '2025-06-27', '13:00:00', 'MarÃ­a Dolores Riquelme', 'juanfran@coditeca.es', '+34665559669', '2025-06-19 14:49:20'),
(16, '2025-06-20', '17:00:00', 'lucas', 'javimalo80@gmail.com', '8625737636', '2025-06-19 17:42:42'),
(17, '2025-06-23', '18:00:00', 'lucas', 'lumaza2@gnmai.com', '545644654', '2025-06-20 10:06:08'),
(18, '2025-07-28', '13:00:00', 'sss', 'sasf234@lksdjf.com', '234234234', '2025-06-23 20:20:09');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `wp_citas`
--
ALTER TABLE `wp_citas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `wp_citas`
--
ALTER TABLE `wp_citas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
