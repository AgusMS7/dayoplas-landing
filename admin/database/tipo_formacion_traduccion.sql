-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-08-2025 a las 05:32:27
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
-- Estructura de tabla para la tabla `tipo_formacion_traduccion`
--

CREATE TABLE `tipo_formacion_traduccion` (
  `id_tipo_formacion` int(11) NOT NULL,
  `idioma` varchar(2) NOT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `subtitulo` text NOT NULL,
  `descripcion_html` text DEFAULT NULL,
  `pie_html` text NOT NULL,
  `descripcion_larga` text DEFAULT NULL,
  `estado` varchar(1) NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_formacion_traduccion`
--

INSERT INTO `tipo_formacion_traduccion` (`id_tipo_formacion`, `idioma`, `titulo`, `subtitulo`, `descripcion_html`, `pie_html`, `descripcion_larga`, `estado`) VALUES
(1, 'en', 'COURSES', 'Explore the available programs in this category.', '<p><strong>Comprehensive training to turn your passion into a profession.</strong></p>\r\n<p>Discover our intensive and certified courses, designed to provide solid knowledge in aesthetics, podiatry, and holistic health. Perfect for those seeking to take a professional leap, start a business, or refine their technique with academic support.</p>', '<p>🎓 <em>Register now and start your path to professional success today!</em></p>', 'Discover our intensive, certified training options designed to turn your passion into a career.', 'A'),
(1, 'es', 'CURSOS', 'Accedé a los programas disponibles en esta categoría.', '<p><strong>Formación completa para transformar tu vocación en profesión.</strong></p>\r\n<p>Descubrí nuestros cursos intensivos y certificados, diseñados para brindarte conocimientos sólidos en estética, podología y salud integral. Ideales si buscás dar un salto profesional, emprender o perfeccionar tu técnica con respaldo académico.</p>', '<p>🎓 <em>¡Inscribite y comenzá tu camino al éxito profesional hoy mismo!</em></p>', 'Descubrí nuestras opciones formativas intensivas y certificadas para transformar tu vocación en una profesión.', 'A'),
(1, 'pt', 'CURSOS', 'Acesse os programas disponíveis nesta categoria.', '<p><strong>Formação completa para transformar sua vocação em profissão.</strong></p>\r\n<p>Descubra nossos cursos intensivos e certificados, projetados para oferecer conhecimento sólido em estética, podologia e saúde integral. Ideal para quem busca um avanço profissional, iniciar um negócio ou aperfeiçoar sua técnica com respaldo acadêmico.</p>', '<p>🎓 <em>Inscreva-se e comece hoje mesmo seu caminho rumo ao sucesso profissional!</em></p>', 'Descubra nossas opções de formação intensiva e certificada para transformar sua vocação em profissão.', 'A'),
(2, 'en', 'WORKSHOPS', 'Explore the available programs in this category.', '<p><strong>Learn by doing: hands-on and dynamic training.</strong></p>\r\n<p>Join in-person or virtual workshops focused on practical experience, where you’ll learn cutting-edge techniques in beauty, foot care, and facial aesthetics from industry experts.</p>', '<p>🖐️ <em>Limited spots. Book now and get hands-on experience!</em></p>', NULL, 'A'),
(2, 'es', 'TALLERES', 'Accedé a los programas disponibles en esta categoría.', '<p><strong>Aprendé haciendo: formación práctica y dinámica.</strong></p>\r\n<p>Participá en talleres presenciales o virtuales con enfoque práctico, donde vas a incorporar técnicas actuales en belleza, cuidado podal y estética facial de la mano de profesionales del rubro.</p>', '\r\n<p>🖐️ <em>Plazas limitadas. ¡Reservá tu lugar y poné manos a la obra!</em></p>', NULL, 'A'),
(2, 'pt', 'OFICINAS', 'Acesse os programas disponíveis nesta categoria.', '<p><strong>Aprenda fazendo: formação prática e dinâmica.</strong></p>\r\n<p>Participe de oficinas presenciais ou virtuais com foco prático, onde você aprenderá técnicas atuais de beleza, cuidados podais e estética facial com profissionais experientes.</p>', '<p>🖐️ <em>Vagas limitadas. Reserve agora e coloque a mão na massa!</em></p>', NULL, 'A'),
(3, 'en', 'SEMINARS', 'Explore the available programs in this category.', '<p><strong>Intensive training to stay up-to-date with the latest in the industry.</strong></p>\r\n<p>Participate in our special seminars for updates in beauty, health, and podiatry. Perfect settings for networking, live demos, and learning new trends with renowned professionals.</p>', '<p>🗓️ <em>A transformative training experience in just one day.</em></p>', NULL, 'A'),
(3, 'es', 'JORNADAS', 'Accedé a los programas disponibles en esta categoría.', '<p><strong>Capacitación intensiva para estar al día con lo último del sector.</strong></p>\r\n<p>Sumate a nuestras jornadas especiales de actualización en belleza, salud y podología. Espacios ideales para networking, demostraciones en vivo y aprendizaje de nuevas tendencias con especialistas reconocidos.</p>', '<p>🗓️ <em>Una experiencia formativa que potencia tu carrera en un solo día.</em></p>', NULL, 'A'),
(3, 'pt', 'JORNADAS', 'Acesse os programas disponíveis nesta categoria.', '<p><strong>Capacitação intensiva para se manter atualizado com as últimas tendências do setor.</strong></p>\r\n<p>Participe das nossas jornadas especiais de atualização em beleza, saúde e podologia. Perfeitas para networking, demonstrações ao vivo e aprendizado com especialistas renomados.</p>', '<p>🗓️ <em>Uma experiência formativa que impulsiona sua carreira em apenas um dia.</em></p>', NULL, 'A'),
(4, 'en', 'Nataly', 'Explore the available programs in this category.', '<p><strong>Intensive training to stay up-to-date with the latest in the industry.</strong></p>\r\n<p>Participate in our special seminars for updates in beauty, health, and podiatry. Perfect settings for networking, live demos, and learning new trends with renowned professionals.</p>', '<p>🗓️ <em>A transformative training experience in just one day.</em></p>', NULL, 'A'),
(4, 'es', 'Natalia', 'Accedé a los programas disponibles en esta categoría.', '<p><strong>Capacitación intensiva para estar al día con lo último del sector.</strong></p>\r\n<p>Sumate a nuestras jornadas especiales de actualización en belleza, salud y podología. Espacios ideales para networking, demostraciones en vivo y aprendizaje de nuevas tendencias con especialistas reconocidos.</p>', '<p>🗓️ <em>Una experiencia formativa que potencia tu carrera en un solo día.</em></p>', NULL, 'A'),
(4, 'pt', 'Nataulie', 'Acesse os programas disponíveis nesta categoria.', '<p><strong>Capacitação intensiva para se manter atualizado com as últimas tendências do setor.</strong></p>\r\n<p>Participe das nossas jornadas especiais de atualização em beleza, saúde e podologia. Perfeitas para networking, demonstrações ao vivo e aprendizado com especialistas renomados.</p>', '<p>🗓️ <em>Uma experiência formativa que impulsiona sua carreira em apenas um dia.</em></p>', NULL, 'A');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tipo_formacion_traduccion`
--
ALTER TABLE `tipo_formacion_traduccion`
  ADD PRIMARY KEY (`id_tipo_formacion`,`idioma`),
  ADD KEY `idx_formacion_idioma` (`id_tipo_formacion`,`idioma`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tipo_formacion_traduccion`
--
ALTER TABLE `tipo_formacion_traduccion`
  ADD CONSTRAINT `tipo_formacion_traduccion_ibfk_1` FOREIGN KEY (`id_tipo_formacion`) REFERENCES `tipo_formacion` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
