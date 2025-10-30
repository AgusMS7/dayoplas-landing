-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-08-2025 a las 02:09:47
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
-- Base de datos: `dayloplas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formacion`
--

CREATE TABLE `formacion` (
  `id` int(11) NOT NULL,
  `tipo_formacion_id` int(11) DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `duracion` varchar(100) DEFAULT NULL,
  `horarios` varchar(100) DEFAULT NULL,
  `dias_cursado` varchar(100) DEFAULT NULL,
  `carga_horaria` varchar(100) DEFAULT NULL,
  `recurso_pdf` varchar(255) DEFAULT NULL,
  `recurso_imagen` varchar(255) DEFAULT NULL,
  `destacado` text DEFAULT NULL,
  `imagen_cabecera` varchar(255) DEFAULT NULL,
  `estado` varchar(1) NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `formacion`
--

INSERT INTO `formacion` (`id`, `tipo_formacion_id`, `slug`, `imagen`, `fecha_inicio`, `duracion`, `horarios`, `dias_cursado`, `carga_horaria`, `recurso_pdf`, `recurso_imagen`, `destacado`, `imagen_cabecera`, `estado`) VALUES
(1, 1, 'peluqueria', 'peluqueria.png', '2025-06-05', '9 meses', '18:00 - 21:00', 'Lunes y Miércoles', '108 hs', 'programa_peluqueria.pdf', 'detalle_peluqueria.jpg', '*Diploma habilitante*', 'cursos_cabecera.png', 'A'),
(2, 1, 'podocosmiatria', 'podocosmiatria.png', '2025-05-26', '10 meses', '18:30 - 21:30', 'Martes y Jueves', '120 hs', '', '', '', NULL, 'A'),
(3, 1, 'digitopuntura', 'digitopuntura.png', '2025-05-18', '6 meses', '17:00 - 20:00', 'Sábados', '90 hs', '', '', '', NULL, 'A'),
(4, 1, 'masaje_integral', 'masaje_integral.png', '2025-03-15', '8 meses', '19:00 - 21:30', 'Lunes y Jueves', '100 hs', '', '', '', NULL, 'A'),
(5, 1, 'depilacion', 'depilacion.png', '2025-05-15', '4 meses', '18:00 - 20:00', 'Miércoles', '60 hs', '', '', '', NULL, 'A'),
(6, 1, 'manicuria', 'manicuria.png', '2025-06-20', '5 meses', '16:00 - 18:00', 'Martes y Viernes', '70 hs', '', '', '', NULL, 'A'),
(7, 1, 'pestanas', 'pestanas.png', '2025-07-05', '3 meses', '15:00 - 17:00', 'Miércoles y Viernes', '40 hs', '', '', '', NULL, 'A'),
(8, 1, 'masaje_tailandes', 'masaje_tailandes.png', '2025-06-01', '2 meses', '18:30 - 21:00', 'Sábados', '36 hs', '', '', '', NULL, 'A'),
(9, 2, 'reflexologia', 'pestanas.png', '2025-06-10', '3 meses', '18:00 - 20:00', 'Martes y Jueves', '60 hs', '', '', '', NULL, 'A'),
(10, 2, 'aromaterapia', 'aromaterapia.png', '2025-06-15', '2 meses', '17:30 - 19:30', 'Miércoles', '40 hs', '', '', '', NULL, 'A'),
(11, 3, 'jornada_podologia', 'jornada_podologia.png', '2025-08-10', '1 día', '9:00 - 18:00', 'Sábado', '8 hs', '', '', '', NULL, 'A'),
(12, 5, 'jornada_masaje', 'jornada_masaje.png', '2025-08-25', '1 día', '10:00 - 17:00', 'Domingo', '7 hs', '', '', '', NULL, 'A'),
(13, 4, 'jornada_masaje', 'jornada_masaje.png', '2025-08-25', '1 día', '10:00 - 17:00', 'Domingo', '7 hs', '', '', '', NULL, 'A');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `formacion`
--
ALTER TABLE `formacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipo_formacion_id` (`tipo_formacion_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `formacion`
--
ALTER TABLE `formacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `formacion`
--
ALTER TABLE `formacion`
  ADD CONSTRAINT `formacion_ibfk_1` FOREIGN KEY (`tipo_formacion_id`) REFERENCES `tipo_formacion` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
