-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-10-2025 a las 01:05:06
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
-- Base de datos: `taller_el_cientifico`
--
CREATE DATABASE IF NOT EXISTS `taller_el_cientifico` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `taller_el_cientifico`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

DROP TABLE IF EXISTS `administradores`;
CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar_url` varchar(255) DEFAULT NULL COMMENT 'URL de la imagen de perfil del administrador',
  `nivel_acceso` enum('super_admin','admin','editor') DEFAULT 'admin',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `creado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id`, `nombre`, `email`, `password`, `avatar_url`, `nivel_acceso`, `fecha_creacion`, `ultimo_acceso`, `activo`, `creado_por`) VALUES
(3, 'Martinez', 'carlosstivengutierrez0@gmail.com', 'password_not_used', 'uploads/avatars/admin_3_1759798416.jpg', 'super_admin', '2025-09-25 01:15:07', NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_codes`
--

DROP TABLE IF EXISTS `admin_codes`;
CREATE TABLE `admin_codes` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_codes`
--

INSERT INTO `admin_codes` (`id`, `email`, `code`, `created_at`, `expires_at`, `used`, `ip_address`, `user_agent`) VALUES
(1, 'carlosstivengutierrezramirez@gmail.com', '711896', '2025-09-24 15:00:21', '2025-09-24 10:10:21', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(2, 'carlosstivengutierrezramirez@gmail.com', '227100', '2025-09-25 01:15:13', '2025-09-25 03:25:13', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(3, 'carlosstivengutierrezramirez@gmail.com', '201462', '2025-09-25 01:18:43', '2025-09-25 03:28:43', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(4, 'carlosstivengutierrezramirez@gmail.com', '476317', '2025-09-25 01:23:00', '2025-09-25 03:33:00', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(5, 'carlosstivengutierrezramirez@gmail.com', '934105', '2025-09-25 03:07:31', '2025-09-24 22:17:31', 0, NULL, NULL),
(6, 'carlosstivengutierrezramirez@gmail.com', '117056', '2025-09-25 03:12:36', '2025-09-24 22:22:36', 0, NULL, NULL),
(7, 'carlosstivengutierrezramirez@gmail.com', '745551', '2025-09-25 03:12:42', '2025-09-24 22:22:42', 0, NULL, NULL),
(8, 'carlosstivengutierrezramirez@gmail.com', '749249', '2025-09-25 03:13:30', '2025-09-24 22:23:30', 0, NULL, NULL),
(9, 'carlosstivengutierrezramirez@gmail.com', '892340', '2025-09-25 03:17:17', '2025-09-24 22:27:17', 1, NULL, NULL),
(10, 'carlosstivengutierrezramirez@gmail.com', '578444', '2025-09-25 03:22:23', '2025-09-24 22:32:23', 0, NULL, NULL),
(11, 'carlosstivengutierrezramirez@gmail.com', '403653', '2025-09-25 11:43:05', '2025-09-25 06:53:05', 1, NULL, NULL),
(12, 'carlosstivengutierrezramirez@gmail.com', '371892', '2025-09-25 11:46:21', '2025-09-25 06:56:21', 0, NULL, NULL),
(13, 'carlosstivengutierrezramirez@gmail.com', '832794', '2025-09-25 11:46:30', '2025-09-25 06:56:30', 0, NULL, NULL),
(14, 'carlosstivengutierrezramirez@gmail.com', '612691', '2025-09-25 12:07:36', '2025-09-25 07:17:36', 1, NULL, NULL),
(15, 'carlosstivengutierrezramirez@gmail.com', '682842', '2025-09-25 12:11:03', '2025-09-25 07:21:03', 1, NULL, NULL),
(16, 'carlosstivengutierrezramirez@gmail.com', '641604', '2025-09-25 15:03:10', '2025-09-25 10:13:10', 1, NULL, NULL),
(17, 'carlosstivengutierrezramirez@gmail.com', '852463', '2025-09-26 01:16:36', '2025-09-25 20:26:36', 1, NULL, NULL),
(18, 'carlosstivengutierrezramirez@gmail.com', '964937', '2025-09-26 12:09:10', '2025-09-26 07:19:10', 1, NULL, NULL),
(19, 'carlosstivengutierrezramirez@gmail.com', '457724', '2025-09-26 13:08:06', '2025-09-26 08:18:06', 1, NULL, NULL),
(20, 'carlosstivengutierrezramirez@gmail.com', '703167', '2025-09-26 13:24:29', '2025-09-26 08:34:29', 1, NULL, NULL),
(21, 'carlosstivengutierrezramirez@gmail.com', '110293', '2025-09-26 15:28:37', '2025-09-26 10:38:37', 1, NULL, NULL),
(22, 'carlosstivengutierrezramirez@gmail.com', '311438', '2025-10-06 22:34:12', '2025-10-06 17:44:12', 0, NULL, NULL),
(23, 'carlosstivengutierrezramirez@gmail.com', '610878', '2025-10-06 22:34:32', '2025-10-06 17:44:32', 0, NULL, NULL),
(24, 'carlosstivengutierrezramirez@gmail.com', '629930', '2025-10-06 22:37:18', '2025-10-06 17:47:18', 0, NULL, NULL),
(25, 'carlosstivengutierrezramirez@gmail.com', '268264', '2025-10-06 22:41:17', '2025-10-06 17:51:17', 1, NULL, NULL),
(26, 'carlosstivengutierrezramirez@gmail.com', '132081', '2025-10-10 16:49:35', '2025-10-10 11:59:35', 1, NULL, NULL),
(27, 'carlosstivengutierrezramirez@gmail.com', '604173', '2025-10-13 21:06:43', '2025-10-13 16:16:43', 1, NULL, NULL),
(28, 'carlosstivengutierrezramirez@gmail.com', '837036', '2025-10-14 11:41:36', '2025-10-14 06:51:36', 1, NULL, NULL),
(29, 'carlosstivengutierrezramirez@gmail.com', '508201', '2025-10-14 15:31:29', '2025-10-14 10:41:29', 0, NULL, NULL),
(30, 'carlosstivengutierrezramirez@gmail.com', '825137', '2025-10-14 15:34:18', '2025-10-14 10:44:18', 1, NULL, NULL),
(31, 'carlosstivengutierrezramirez@gmail.com', '338673', '2025-10-15 12:11:46', '2025-10-15 07:21:46', 0, NULL, NULL),
(32, 'carlosstivengutierrezramirez@gmail.com', '472261', '2025-10-15 12:15:21', '2025-10-15 07:25:21', 1, NULL, NULL),
(33, 'carlosstivengutierrezramirez@gmail.com', '538785', '2025-10-15 12:20:32', '2025-10-15 07:30:32', 0, NULL, NULL),
(34, 'carlosstivengutierrez0@gmail.com', '945707', '2025-10-15 12:27:27', '2025-10-15 07:37:27', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido_editable`
--

DROP TABLE IF EXISTS `contenido_editable`;
CREATE TABLE `contenido_editable` (
  `id` int(11) NOT NULL,
  `seccion` varchar(50) NOT NULL COMMENT 'home, services, about, etc',
  `campo` varchar(100) NOT NULL,
  `valor` text NOT NULL,
  `tipo` enum('text','textarea','number','url','email') DEFAULT 'text',
  `visible` tinyint(1) DEFAULT 1,
  `orden` int(11) DEFAULT 0,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `actualizado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `contenido_editable`
--

INSERT INTO `contenido_editable` (`id`, `seccion`, `campo`, `valor`, `tipo`, `visible`, `orden`, `descripcion`, `fecha_actualizacion`, `actualizado_por`) VALUES
(1, 'home', 'titulo_principal', 'Tu mecánico de confianza a un clic', 'text', 1, 1, 'Título principal del hero section', '2025-09-25 11:57:22', NULL),
(2, 'home', 'subtitulo', '<p>Somos la red de talleres mecánicos más grande del país, brindándote calidad, precios justos y garantía post servicio.</p>', 'textarea', 1, 2, 'Subtítulo descriptivo', '2025-09-25 01:36:34', NULL),
(3, 'home', 'vehiculos_atendidos', '100', 'number', 1, 3, 'Número de vehículos atendidos', '2025-09-25 01:36:34', NULL),
(4, 'home', 'talleres_aliados', '10', 'number', 1, 4, 'Número de talleres aliados', '2025-09-25 01:29:00', NULL),
(5, 'ubicacion', 'direccion', 'Carrera 13a #14-46', 'text', 1, 1, 'Dirección del taller', '2025-09-25 11:59:30', NULL),
(6, 'ubicacion', 'barrio', 'Los Molinos', 'text', 1, 2, 'Barrio donde está ubicado', '2025-09-25 11:59:30', NULL),
(7, 'ubicacion', 'horario_lunes_viernes', 'Lunes a Viernes: 8:30 a.m. - 6:00 p.m', 'text', 1, 3, 'Horario de lunes a viernes', '2025-09-25 11:59:30', NULL),
(8, 'ubicacion', 'horario_sabados', 'Sábados: 9:00 a.m. - 1:00 p.m.', 'text', 1, 4, 'Horario de sábados', '2025-09-25 11:59:30', NULL),
(9, 'ubicacion', 'descripcion', 'Somos un taller', 'textarea', 1, 5, 'Descripción del taller', '2025-09-25 11:59:30', NULL),
(10, 'contacto', 'telefono', '+57 315 675 6271', 'text', 1, 1, 'Teléfono principal', '2025-09-11 04:58:46', NULL),
(11, 'contacto', 'email', 'samuraqui1980@gmail.com', 'email', 1, 2, 'Email principal', '2025-09-11 04:58:46', NULL),
(12, 'contacto', 'whatsapp', '573156756271', 'text', 1, 3, 'Número de WhatsApp', '2025-09-11 04:58:46', NULL),
(16, 'home', 'about_title', 'La Ciencia Detrás de Cada Reparación', 'text', 1, 5, 'Título de la sección \"Sobre Nosotros\"', '2025-09-25 02:21:52', NULL),
(17, 'home', 'about_text', '<p>En Taller El Científico, no solo reparamos vehículos, aplicamos un método riguroso para diagnosticar y solucionar cada problema. Creemos que cada motor es un sistema complejo que merece ser entendido a la perfección. Nuestro equipo combina años de experiencia con las herramientas más avanzadas para garantizar que tu carro reciba el tratamiento más preciso y eficiente.</p><p>Nuestra misión es devolverte la tranquilidad y la seguridad al volante, sabiendo que tu vehículo ha sido atendido por verdaderos expertos apasionados por la mecánica.</p>', 'textarea', 1, 6, 'Texto principal de la sección \"Sobre Nosotros\"', '2025-09-25 02:21:52', NULL),
(18, 'home', 'why_title', '¿Por Qué Confiar en Nosotros?', 'text', 1, 7, 'Título de la sección de ventajas', '2025-09-25 02:21:52', NULL),
(19, 'home', 'why_1_title', 'Diagnóstico Preciso', 'text', 1, 8, 'Título del primer punto de ventaja', '2025-09-25 02:21:52', NULL),
(20, 'home', 'why_1_text', 'Utilizamos equipos de última generación para identificar la raíz del problema, ahorrándote tiempo y dinero en reparaciones innecesarias.', 'textarea', 1, 9, 'Texto del primer punto de ventaja', '2025-09-25 02:21:52', NULL),
(21, 'home', 'why_2_title', 'Técnicos Certificados', 'text', 1, 10, 'Título del segundo punto de ventaja', '2025-09-25 02:21:52', NULL),
(22, 'home', 'why_2_text', 'Nuestro equipo está en constante capacitación para estar al día con las últimas tecnologías automotrices y ofrecerte el mejor servicio.', 'textarea', 1, 11, 'Texto del segundo punto de ventaja', '2025-09-25 02:21:52', NULL),
(23, 'home', 'why_3_title', 'Garantía Total', 'text', 1, 12, 'Título del tercer punto de ventaja', '2025-09-25 02:21:52', NULL),
(24, 'home', 'why_3_text', 'Confiamos tanto en nuestro trabajo que todas nuestras reparaciones cuentan con una garantía completa para tu total tranquilidad.', 'textarea', 1, 13, 'Texto del tercer punto de ventaja', '2025-09-25 02:21:52', NULL),
(25, 'home', 'about_image_url', 'https://www.autoavance.co/wp-content/uploads/2021/10/taller-mecanico-productividad.jpeg', 'url', 1, 6, 'URL de la imagen para la sección \"Sobre Nosotros\"', '2025-10-14 11:44:41', NULL),
(26, 'ubicacion', 'imagen_url', 'uploads/sedes/sede_1760391098.png', 'url', 1, 0, 'Imagen principal de la sede', '2025-10-13 21:31:38', NULL),
(27, 'home', 'brands_title', 'Marcas que Atendemos', 'text', 1, 14, 'Título de la sección de marcas', '2025-10-13 21:52:07', NULL),
(28, 'home', 'brands_text', 'Atendemos una amplia gama de vehículos a gasolina, diésel y turbo. No trabajamos con volquetas ni vehículos de carga pesada.', 'textarea', 1, 15, 'Texto descriptivo debajo del título de marcas', '2025-10-13 21:52:07', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizaciones`
--

DROP TABLE IF EXISTS `cotizaciones`;
CREATE TABLE `cotizaciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `nombre_cliente` varchar(100) NOT NULL,
  `telefono_cliente` varchar(20) NOT NULL,
  `email_cliente` varchar(150) DEFAULT NULL,
  `ciudad` varchar(50) NOT NULL,
  `marca_vehiculo` varchar(50) NOT NULL,
  `modelo_vehiculo` varchar(50) DEFAULT NULL,
  `año_vehiculo` year(4) DEFAULT NULL,
  `tipo_vehiculo` enum('carro','moto') NOT NULL,
  `descripcion_problema` text DEFAULT NULL,
  `total_estimado` decimal(10,2) DEFAULT 0.00,
  `estado` enum('pendiente','contactado','agendado','completado','cancelado') DEFAULT 'pendiente',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_respuesta` datetime DEFAULT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cotizaciones`
--

INSERT INTO `cotizaciones` (`id`, `usuario_id`, `nombre_cliente`, `telefono_cliente`, `email_cliente`, `ciudad`, `marca_vehiculo`, `modelo_vehiculo`, `año_vehiculo`, `tipo_vehiculo`, `descripcion_problema`, `total_estimado`, `estado`, `fecha_creacion`, `fecha_respuesta`, `fecha_actualizacion`) VALUES
(14, 9, 'Carlos Stiven Gutierrez Ramirez', '3502931654', 'carlosstivengutierrezramirez@gmail.com', '', 'Renault', '2019', '2002', 'carro', 'Daños', 30000.00, 'pendiente', '2025-10-14 14:43:25', NULL, '2025-10-14 14:43:25'),
(15, 9, 'Carlos Stiven Gutierrez Ramirez', '3502931654', 'carlosstivengutierrezramirez@gmail.com', '', 'Toyota', 'txl', '2020', 'carro', 'Estoy parado.', 611000.00, 'pendiente', '2025-10-14 15:40:23', NULL, '2025-10-14 15:40:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizacion_servicios`
--

DROP TABLE IF EXISTS `cotizacion_servicios`;
CREATE TABLE `cotizacion_servicios` (
  `id` int(11) NOT NULL,
  `cotizacion_id` int(11) NOT NULL,
  `servicio_id` int(11) NOT NULL,
  `precio_cotizado` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cotizacion_servicios`
--

INSERT INTO `cotizacion_servicios` (`id`, `cotizacion_id`, `servicio_id`, `precio_cotizado`) VALUES
(20, 14, 3, 30000.00),
(21, 15, 2, 121000.00),
(22, 15, 20, 450000.00),
(23, 15, 15, 40000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs_actividad`
--

DROP TABLE IF EXISTS `logs_actividad`;
CREATE TABLE `logs_actividad` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `actividad` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `logs_actividad`
--

INSERT INTO `logs_actividad` (`id`, `admin_id`, `actividad`, `descripcion`, `fecha`, `ip_address`) VALUES
(1, 3, 'Actualización de Perfil', 'El administrador actualizó sus datos de perfil.', '2025-10-07 00:39:25', '::1'),
(2, 3, 'Actualización de Perfil', 'El administrador actualizó sus datos de perfil.', '2025-10-07 00:45:14', '::1'),
(3, 3, 'Actualización de Perfil', 'El administrador actualizó sus datos de perfil.', '2025-10-07 00:45:23', '::1'),
(4, 3, 'Actualización de Perfil', 'El administrador actualizó sus datos de perfil.', '2025-10-07 00:45:27', '::1'),
(5, 3, 'Actualización de Perfil', 'El administrador actualizó sus datos de perfil.', '2025-10-07 00:46:58', '::1'),
(6, 3, 'Actualización de Perfil', 'El administrador actualizó sus datos de perfil.', '2025-10-07 00:47:10', '::1'),
(7, 3, 'Actualización de Perfil', 'El administrador actualizó sus datos de perfil.', '2025-10-07 00:53:36', '::1'),
(8, 3, 'Actualización de Contenido', 'Se guardaron cambios en la Página de Sedes.', '2025-10-13 21:31:38', '::1'),
(9, 3, 'Eliminación de Cotización', 'Se eliminó la cotización #13.', '2025-10-15 12:16:54', '::1'),
(10, 3, 'Actualización de Perfil', 'El administrador actualizó sus datos de perfil.', '2025-10-15 12:26:57', '::1'),
(11, 3, 'Actualización de Perfil', 'El administrador actualizó sus datos de perfil.', '2025-10-15 12:28:21', '::1'),
(12, 3, 'Actualización de Perfil', 'El administrador actualizó sus datos de perfil.', '2025-10-15 12:28:31', '::1'),
(13, 3, 'Actualización de Perfil', 'El administrador actualizó sus datos de perfil.', '2025-10-15 12:28:42', '::1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

DROP TABLE IF EXISTS `marcas`;
CREATE TABLE `marcas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `logo_url` varchar(255) NOT NULL,
  `orden` int(11) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`id`, `nombre`, `logo_url`, `orden`, `activo`) VALUES
(1, 'Toyota', 'uploads/marcas/toyota.png', 1, 1),
(2, 'Chevrolet', 'uploads/marcas/chevrolet.png', 2, 1),
(3, 'Renault', 'uploads/marcas/renault.png', 3, 1),
(4, 'Mazda', 'uploads/marcas/mazda.png', 4, 1),
(5, 'Kia', 'uploads/marcas/kia.png', 5, 1),
(6, 'Toyota', 'uploads/marcas/toyota.png', 1, 1),
(7, 'Chevrolet', 'uploads/marcas/chevrolet.png', 2, 1),
(8, 'Renault', 'uploads/marcas/renault.png', 3, 1),
(9, 'Mazda', 'uploads/marcas/mazda.png', 4, 1),
(10, 'Kia', 'uploads/marcas/kia.png', 5, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

DROP TABLE IF EXISTS `servicios`;
CREATE TABLE `servicios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `precio_desde` decimal(10,2) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id`, `nombre`, `descripcion`, `imagen_url`, `precio_desde`, `activo`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Revisión General', 'Inspección completa del vehículo para identificar posibles problemas', 'uploads/servicios/servicio_1_1760390571.webp', 119000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(2, 'Falla en el Motor', 'Diagnóstico y reparación de fallas en el motor del vehículo', 'uploads/servicios/servicio_2_1760390571.webp', 121000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(3, 'Cambio de Aceite', 'Cambio de aceite del motor y filtro correspondiente', 'uploads/servicios/servicio_3_1760390571.jpg', 30000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(4, 'Revisión por Kilometraje', 'Mantenimiento preventivo según el kilometraje del vehículo', 'uploads/servicios/servicio_4_1760390571.jpg', 539000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(5, 'Servicio de Escáner', 'Diagnóstico computarizado del vehículo', 'uploads/servicios/servicio_5_1760390571.jpg', 105000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(6, 'Alineación y Balanceo', 'Alineación de las ruedas y balanceo de llantas', 'uploads/servicios/servicio_6_1760390571.jpg', 60000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(7, 'Lavado de Inyectores', 'Limpieza profunda de los inyectores de combustible', 'uploads/servicios/servicio_7_1760390571.jpg', 70000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(8, 'Revisión de Frenos', 'Inspección y mantenimiento del sistema de frenos', 'uploads/servicios/servicio_8_1760390571.jpg', 85000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(9, 'Revisión de Suspensión', 'Diagnóstico y reparación del sistema de suspensión', 'uploads/servicios/servicio_9_1760390571.jpg', 98000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(10, 'Pulida de Faros', 'Restauración y pulido de faros opacos', 'uploads/servicios/servicio_10_1760390571.webp', 100000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(11, 'Lavado de Motor', 'Limpieza externa del motor del vehículo', 'uploads/servicios/servicio_11_1760390571.jpg', 45000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(12, 'Rotación de Llantas', 'Rotación de llantas para desgaste uniforme', 'uploads/servicios/servicio_12_1760390571.jpg', 35000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(13, 'Sistema de Escape', 'Revisión y reparación del sistema de escape', 'uploads/servicios/servicio_13_1760390571.jpeg', 130000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(14, 'Cambio de Batería', 'Reemplazo de la batería del vehículo', 'uploads/servicios/servicio_14_1760390571.jpg', 150000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(15, 'Lavado y Detallado', 'Lavado completo y detallado del vehículo', 'uploads/servicios/servicio_15_1760390571.jpeg', 40000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(16, 'Sincronización', 'Sincronización del motor para óptimo rendimiento', 'uploads/servicios/servicio_16_1760390571.jpg', 80000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(17, 'Aire Acondicionado', 'Mantenimiento y reparación del sistema A/C', 'uploads/servicios/servicio_17_1760390571.webp', 120000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(18, 'Correa de Repartición', 'Cambio de correa de distribución', 'uploads/servicios/servicio_18_1760390571.webp', 250000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(19, 'Amortiguadores', 'Reemplazo de amortiguadores', 'uploads/servicios/servicio_19_1760390571.webp', 300000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(20, 'Kit de Embrague', 'Cambio completo del kit de embrague', 'uploads/servicios/servicio_20_1760390571.webp', 450000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:22:51'),
(21, 'Electricidad y Electrónica', 'Reparación de sistemas eléctricos y electrónicos', 'uploads/servicios/servicio_21_1760390614.jpg', 150000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:23:34'),
(22, 'Latonería y Pintura', 'Reparación de carrocería y pintura', 'uploads/servicios/servicio_22_1760390614.webp', 200000.00, 1, '2025-09-11 03:47:02', '2025-10-13 21:23:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `tipo_documento` enum('CC','CE','TI','PP','NIT') DEFAULT NULL,
  `numero_documento` varchar(20) DEFAULT NULL,
  `telefono_completo` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `token_expires_at` datetime DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL COMMENT 'URL de la imagen de perfil del usuario',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `provider` varchar(50) DEFAULT 'local'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `tipo_documento`, `numero_documento`, `telefono_completo`, `password_hash`, `is_verified`, `verification_token`, `token_expires_at`, `fecha_actualizacion`, `email`, `telefono`, `avatar_url`, `fecha_registro`, `ultimo_acceso`, `activo`, `provider`) VALUES
(7, 'Carlos Stiven', 'Gutierrez Ramirez', 'TI', '1079178510', NULL, '$2y$10$vhcKmoodj71AvSpLequqSed/i8DrA9YtkkgRRXy0PYk45N2bQi/82', 1, NULL, NULL, '2025-10-06 19:28:44', 'carlosstivengutierrez0@gmail.com', '3502931654', NULL, '2025-09-26 00:59:55', NULL, 1, 'local'),
(9, 'Carlos Stiven', 'Gutierrez Ramirez', 'TI', '1079178510', NULL, '$2y$10$5fDI.sci2UbFcvmSBReA.uBfIueyrbGwcDg4vkjyjizKPs1dyXKP.', 1, NULL, NULL, '2025-10-14 09:42:28', 'carlosstivengutierrezramirez@gmail.com', '3502931654', NULL, '2025-10-14 14:41:35', NULL, 1, 'local');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `creado_por` (`creado_por`);

--
-- Indices de la tabla `admin_codes`
--
ALTER TABLE `admin_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email_code` (`email`,`code`),
  ADD KEY `idx_expires` (`expires_at`),
  ADD KEY `idx_used` (`used`);

--
-- Indices de la tabla `contenido_editable`
--
ALTER TABLE `contenido_editable`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_seccion_campo` (`seccion`,`campo`),
  ADD KEY `actualizado_por` (`actualizado_por`);

--
-- Indices de la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cotizaciones_estado` (`estado`),
  ADD KEY `idx_cotizaciones_fecha` (`fecha_creacion`),
  ADD KEY `idx_usuario_id` (`usuario_id`);

--
-- Indices de la tabla `cotizacion_servicios`
--
ALTER TABLE `cotizacion_servicios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cotizacion_servicio` (`cotizacion_id`,`servicio_id`),
  ADD KEY `servicio_id` (`servicio_id`);

--
-- Indices de la tabla `logs_actividad`
--
ALTER TABLE `logs_actividad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_actividad` (`actividad`);

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_provider` (`provider`),
  ADD KEY `idx_numero_documento` (`numero_documento`),
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `admin_codes`
--
ALTER TABLE `admin_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `contenido_editable`
--
ALTER TABLE `contenido_editable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `cotizacion_servicios`
--
ALTER TABLE `cotizacion_servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `logs_actividad`
--
ALTER TABLE `logs_actividad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD CONSTRAINT `administradores_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `administradores` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `contenido_editable`
--
ALTER TABLE `contenido_editable`
  ADD CONSTRAINT `contenido_editable_ibfk_1` FOREIGN KEY (`actualizado_por`) REFERENCES `administradores` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  ADD CONSTRAINT `cotizaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cotizacion_servicios`
--
ALTER TABLE `cotizacion_servicios`
  ADD CONSTRAINT `cotizacion_servicios_ibfk_1` FOREIGN KEY (`cotizacion_id`) REFERENCES `cotizaciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cotizacion_servicios_ibfk_2` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `logs_actividad`
--
ALTER TABLE `logs_actividad`
  ADD CONSTRAINT `logs_actividad_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `administradores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
