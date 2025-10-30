-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-08-2025 a las 02:10:34
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
-- Estructura de tabla para la tabla `formacion_trad`
--

CREATE TABLE `formacion_trad` (
  `id` int(11) NOT NULL,
  `formacion_id` int(11) DEFAULT NULL,
  `idioma_id` char(2) DEFAULT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `boton` varchar(100) DEFAULT NULL,
  `estado` varchar(1) NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `formacion_trad`
--

INSERT INTO `formacion_trad` (`id`, `formacion_id`, `idioma_id`, `titulo`, `descripcion`, `boton`, `estado`) VALUES
(1, 1, 'es', 'Curso de Peluquería', 'Aprendé corte, peinado y colorimetría.', 'Ver más', 'I'),
(2, 1, 'en', 'Hairdressing Course', 'Learn cutting, styling and colorimetry.', 'See more', 'I'),
(3, 1, 'pt', 'Curso de Cabeleireiro', 'Aprenda corte, penteado e colorimetria.', 'Ver mais', 'I'),
(4, 2, 'es', 'Curso de Podocosmiatría', 'Técnicas integrales para la salud podal.', 'Ver más', 'A'),
(5, 2, 'en', 'Podocosmiatry Course', 'Comprehensive techniques for foot health.', 'See more', 'A'),
(6, 2, 'pt', 'Curso de Podocosmiatria', 'Técnicas completas para a saúde dos pés.', 'Ver mais', 'A'),
(7, 3, 'es', 'Curso de Digitopuntura', 'Estudiá los puntos clave para armonizar el cuerpo.', 'Ver más', 'A'),
(8, 3, 'en', 'Digitopuncture Course', 'Learn key points for body balance.', 'See more', 'A'),
(9, 3, 'pt', 'Curso de Digitopuntura', 'Estude os pontos-chave para o equilíbrio do corpo.', 'Ver mais', 'A'),
(10, 4, 'es', 'Masaje Integral', 'Formación completa en técnicas de masaje corporal.', 'Ver más', 'A'),
(11, 4, 'en', 'Full Body Massage', 'Full training in body massage techniques.', 'See more', 'A'),
(12, 4, 'pt', 'Massagem Integral', 'Formação completa em técnicas de massagem corporal.', 'Ver mais', 'A'),
(13, 5, 'es', 'Curso de Depilación', 'Aprendé técnicas seguras y eficaces.', 'Ver más', 'A'),
(14, 5, 'en', 'Hair Removal Course', 'Learn safe and effective hair removal techniques.', 'See more', 'A'),
(15, 5, 'pt', 'Curso de Depilação', 'Aprenda técnicas seguras e eficazes.', 'Ver mais', 'A'),
(16, 6, 'es', 'Curso de Manicuría', 'Diseñá uñas impecables y profesionales.', 'Ver más', 'A'),
(17, 6, 'en', 'Manicure Course', 'Design flawless and professional nails.', 'See more', 'A'),
(18, 6, 'pt', 'Curso de Manicure', 'Projete unhas impecáveis e profissionais.', 'Ver mais', 'A'),
(19, 7, 'es', 'Curso de Pestañas', 'Colocación y diseño de pestañas con técnica.', 'Ver más', 'A'),
(20, 7, 'en', 'Eyelash Course', 'Learn placement and eyelash design.', 'See more', 'A'),
(21, 7, 'pt', 'Curso de Cílios', 'Aprenda a colocar e desenhar cílios.', 'Ver mais', 'A'),
(22, 8, 'es', 'Masaje Tailandés', 'Conocé esta técnica ancestral de relajación profunda.', 'Ver más', 'A'),
(23, 8, 'en', 'Thai Massage', 'Discover this ancient deep-relaxation technique.', 'See more', 'A'),
(24, 8, 'pt', 'Massagem Tailandesa', 'Conheça esta técnica ancestral de relaxamento.', 'Ver mais', 'A'),
(25, 9, 'es', 'Taller de Reflexología', 'Aprendé técnicas de estimulación en puntos reflejos.', 'Ver más', 'A'),
(26, 9, 'en', 'Reflexology Workshop', 'Learn reflex point stimulation techniques.', 'See more', 'A'),
(27, 9, 'pt', 'Oficina de Reflexologia', 'Aprenda técnicas de estimulação em pontos reflexos.', 'Ver mais', 'A'),
(28, 10, 'es', 'Taller de Aromaterapia', 'Descubrí el poder terapéutico de los aceites esenciales.', 'Ver más', 'A'),
(29, 10, 'en', 'Aromatherapy Workshop', 'Discover the therapeutic power of essential oils.', 'See more', 'A'),
(30, 10, 'pt', 'Oficina de Aromaterapia', 'Descubra o poder terapêutico dos óleos essenciais.', 'Ver mais', 'A'),
(31, 11, 'es', 'Jornada de Podología', 'Participá de charlas y demostraciones exclusivas sobre salud podal.', 'Ver más', 'A'),
(32, 11, 'en', 'Podiatry Day', 'Join exclusive lectures and demonstrations on foot health.', 'See more', 'A'),
(33, 11, 'pt', 'Jornada de Podologia', 'Participe de palestras e demonstrações sobre saúde dos pés.', 'Ver mais', 'A'),
(34, 12, 'es', 'Jornada de Masajes', 'Una experiencia intensiva sobre diferentes técnicas de masaje.', 'Ver más', 'A'),
(35, 12, 'en', 'Massage Day', 'An intensive experience on various massage techniques.', 'See more', 'A'),
(36, 12, 'pt', 'Jornada de Massagem', 'Uma experiência intensiva sobre técnicas de massagem.', 'Ver mais', 'A');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `formacion_trad`
--
ALTER TABLE `formacion_trad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `formacion_id` (`formacion_id`),
  ADD KEY `idioma_id` (`idioma_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `formacion_trad`
--
ALTER TABLE `formacion_trad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `formacion_trad`
--
ALTER TABLE `formacion_trad`
  ADD CONSTRAINT `formacion_trad_ibfk_1` FOREIGN KEY (`formacion_id`) REFERENCES `formacion` (`id`),
  ADD CONSTRAINT `formacion_trad_ibfk_2` FOREIGN KEY (`idioma_id`) REFERENCES `idioma` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
