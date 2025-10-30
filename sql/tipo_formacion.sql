-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-08-2025 a las 02:10:51
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
-- Estructura de tabla para la tabla `tipo_formacion`
--

CREATE TABLE `tipo_formacion` (
  `id` int(11) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `icono` varchar(100) DEFAULT NULL,
  `imagen_cabecera` varchar(255) DEFAULT NULL,
  `estado` varchar(1) NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_formacion`
--

INSERT INTO `tipo_formacion` (`id`, `clave`, `icono`, `imagen_cabecera`, `estado`) VALUES
(1, 'cursos', 'fa-graduation-cap', 'cursos.png', 'A'),
(2, 'talleres', 'fa-hands-helping', 'talleres.png', 'A'),
(3, 'jornadas', 'fa-calendar-alt', 'jornadas.png', 'A'),
(4, 'natalia', 'fa-hands-helping', 'talleres.png', 'A'),
(5, 'Pato', 'fa-hands-helping', 'talleres.png', 'A');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tipo_formacion`
--
ALTER TABLE `tipo_formacion`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tipo_formacion`
--
ALTER TABLE `tipo_formacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
