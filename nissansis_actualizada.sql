-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-08-2025 a las 20:50:16
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
-- Base de datos: `nissansis`
--
DROP DATABASE IF EXISTS nissansis;
CREATE DATABASE nissansis CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
USE nissansis;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacen`
--

CREATE TABLE `almacen` (
  `Id_Almacen` int(11) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Direccion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `almacen`
--

INSERT INTO `almacen` (`Id_Almacen`, `Nombre`, `Direccion`) VALUES
(1, 'Alto Verde', 'Carretera internacional km 25'),
(2, 'Bustamante', 'Calle Rosalino lopez'),
(3, 'Navojoa', 'Calle Gral. I. Pesqueira 721 Norte, Reforma, 85830 Navojoa, Son.'),
(4, 'Magdalena', 'Av. Niños Héroes 310, Mirasol, 84160 Magdalena de Kino, Son.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` blob NOT NULL,
  `expiration` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel_cache_pruebaportal@grupogranauto.mx|127.0.0.1', 0x693a313b, 1752712750),
('laravel_cache_pruebaportal@grupogranauto.mx|127.0.0.1:timer', 0x693a313735323731323735303b, 1752712750);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `checklist`
--

CREATE TABLE `checklist` (
  `id_checklist` int(11) NOT NULL,
  `No_orden_entrada` int(11) NOT NULL,
  `tipo_checklist` enum('Madrina','Traspaso','Recepcion') NOT NULL,
  `documentos_completos` tinyint(1) DEFAULT 0,
  `accesorios_completos` tinyint(1) DEFAULT 0,
  `estado_exterior` enum('Excelente','Bueno','Regular','Malo') DEFAULT NULL,
  `estado_interior` enum('Excelente','Bueno','Regular','Malo') DEFAULT NULL,
  `pdi_realizada` tinyint(1) DEFAULT 0,
  `seguro_vigente` tinyint(1) DEFAULT 0,
  `nfc_instalado` tinyint(1) DEFAULT 0,
  `gps_instalado` tinyint(1) DEFAULT 0,
  `observaciones` text DEFAULT NULL,
  `recibido_por` varchar(50) DEFAULT NULL,
  `fecha_revision` datetime DEFAULT current_timestamp(),
  `folder_viajero` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `checklist`
--

INSERT INTO `checklist` (`id_checklist`, `No_orden_entrada`, `tipo_checklist`, `documentos_completos`, `accesorios_completos`, `estado_exterior`, `estado_interior`, `pdi_realizada`, `seguro_vigente`, `nfc_instalado`, `gps_instalado`, `observaciones`, `recibido_por`, `fecha_revision`, `folder_viajero`, `created_at`, `updated_at`) VALUES
(27, 30, 'Madrina', 1, 1, 'Bueno', 'Excelente', 1, 1, 1, 1, 'Checklist en orden', 'Raul Soriano', NULL, 1, '2025-08-02 06:49:12', '2025-08-02 07:04:30'),
(28, 31, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-02 06:49:12', '2025-08-02 06:49:12'),
(29, 32, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-02 06:54:08', '2025-08-02 06:54:08'),
(30, 33, 'Madrina', 1, 1, 'Bueno', 'Excelente', 1, 1, 1, 1, 'Checklist en orden', 'Raul Soriano', NULL, 1, '2025-08-02 07:02:17', '2025-08-05 06:26:31'),
(31, 34, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(32, 35, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(33, 36, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(34, 37, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(35, 38, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(36, 39, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(37, 40, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(38, 41, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(39, 42, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(40, 43, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(41, 44, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(42, 45, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(43, 46, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(44, 47, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(45, 48, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(46, 49, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(47, 50, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(48, 51, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(49, 52, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(50, 53, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(51, 54, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(52, 55, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(53, 56, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(54, 57, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(55, 58, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(56, 59, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(57, 60, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(58, 61, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(59, 62, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(60, 63, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(61, 64, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(62, 65, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(63, 66, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(64, 67, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(65, 68, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(66, 69, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(67, 70, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(68, 71, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(69, 72, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(70, 73, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(71, 74, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(72, 75, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(73, 76, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(74, 77, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(75, 78, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(76, 79, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(77, 80, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(78, 81, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Raul Soriano', '2025-08-01 00:00:00', 0, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(79, 82, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Saul Corona', '2025-08-04 00:00:00', 0, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(80, 83, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Saul Corona', '2025-08-04 00:00:00', 0, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(81, 84, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Saul Corona', '2025-08-04 00:00:00', 0, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(82, 85, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Saul Corona', '2025-08-04 00:00:00', 0, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(83, 86, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Saul Corona', '2025-08-04 00:00:00', 0, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(84, 87, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Saul Corona', '2025-08-04 00:00:00', 0, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(85, 88, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Saul Corona', '2025-08-04 00:00:00', 0, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(86, 89, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Saul Corona', '2025-08-04 00:00:00', 0, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(87, 90, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Saul Corona', '2025-08-04 00:00:00', 0, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(88, 91, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Saul Corona', '2025-08-04 00:00:00', 0, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(89, 92, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Saul Corona', '2025-08-04 00:00:00', 0, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(90, 93, 'Madrina', 0, 0, 'Bueno', 'Excelente', 0, 0, 0, 0, 'Checklist en orden', 'Saul Corona', '2025-08-04 00:00:00', 0, '2025-08-05 07:05:02', '2025-08-05 07:05:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entradas`
--

CREATE TABLE `entradas` (
  `No_orden` int(11) NOT NULL,
  `VIN` varchar(17) NOT NULL,
  `Almacen_entrada` int(11) DEFAULT NULL,
  `Fecha_entrada` date DEFAULT curdate(),
  `Estado` varchar(50) DEFAULT NULL,
  `Tipo` enum('Madrina','Traspaso','Devolucion','Otro') NOT NULL,
  `Almacen_salida` int(11) DEFAULT NULL,
  `Coordinador_Logistica` varchar(50) DEFAULT NULL,
  `Proximo_mantenimiento` date DEFAULT NULL,
  `Kilometraje_entrada` int(11) DEFAULT 0,
  `Movimientos` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `entradas`
--

INSERT INTO `entradas` (`No_orden`, `VIN`, `Almacen_entrada`, `Fecha_entrada`, `Estado`, `Tipo`, `Almacen_salida`, `Coordinador_Logistica`, `Proximo_mantenimiento`, `Kilometraje_entrada`, `Movimientos`, `created_at`, `updated_at`) VALUES
(30, '1HGCM82633A004352', 1, '2025-08-01', 'disponible', 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-02 06:49:12', '2025-08-02 07:04:30'),
(31, 'NMBVCXZAQ12345678', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-02 06:49:12', '2025-08-02 06:49:12'),
(32, '1HGCM82633A004363', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-02 06:54:08', '2025-08-02 06:54:08'),
(33, '1HGCM82633A004353', 1, '2025-08-01', 'disponible', 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-02 07:02:17', '2025-08-05 06:26:31'),
(34, '1HGCM82633A004354', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(35, '1HGCM82633A004355', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(36, '1HGCM82633A004356', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(37, '1HGCM82633A004357', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(38, '1HGCM82633A004358', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(39, '1HGCM82633A004359', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(40, '1HGCM82633A004360', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(41, '1HGCM82633A004361', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(42, '1HGCM82633A004362', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(43, '1HGCM82633A004364', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(44, '1HGCM82633A004365', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-02 07:02:17', '2025-08-02 07:02:17'),
(45, '1HGCM82633A004353', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(46, '1HGCM82633A004354', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(47, '1HGCM82633A004355', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(48, '1HGCM82633A004356', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(49, '1HGCM82633A004357', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(50, '1HGCM82633A004358', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(51, '1HGCM82633A004359', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(52, '1HGCM82633A004360', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(53, '1HGCM82633A004361', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(54, '1HGCM82633A004362', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(55, '1HGCM82633A004364', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(56, '1HGCM82633A004365', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:21', '2025-08-05 06:52:21'),
(57, '1HGCM82633A004353', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(58, '1HGCM82633A004354', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(59, '1HGCM82633A004355', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(60, '1HGCM82633A004356', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(61, '1HGCM82633A004357', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(62, '1HGCM82633A004358', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(63, '1HGCM82633A004359', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(64, '1HGCM82633A004360', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(65, '1HGCM82633A004361', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(66, '1HGCM82633A004362', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(67, '1HGCM82633A004364', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(68, '1HGCM82633A004365', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:52:48', '2025-08-05 06:52:48'),
(69, '1HGCM82633A004353', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(70, '1HGCM82633A004354', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(71, '1HGCM82633A004355', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(72, '1HGCM82633A004356', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(73, '1HGCM82633A004357', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(74, '1HGCM82633A004358', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(75, '1HGCM82633A004359', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(76, '1HGCM82633A004360', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(77, '1HGCM82633A004361', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(78, '1HGCM82633A004362', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(79, '1HGCM82633A004363', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(80, '1HGCM82633A004364', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(81, '1HGCM82633A004365', 1, '2025-08-01', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 06:58:10', '2025-08-05 06:58:10'),
(82, '1HGCM82633A004353', 1, '2025-08-04', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(83, '1HGCM82689A004354', 1, '2025-08-04', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(84, '1HGCM82633A004355', 1, '2025-08-04', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(85, '190CM82633A004356', 1, '2025-08-04', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(86, 'POGCM82633A004357', 1, '2025-08-04', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(87, 'MXGCM82633A004358', 1, '2025-08-04', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(88, 'UHGCM82633A004359', 1, '2025-08-04', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(89, '1HGCM82783A004360', 1, '2025-08-04', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(90, '1HGCM82033A004361', 1, '2025-08-04', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(91, '1HGCP82633A004362', 1, '2025-08-04', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(92, '1HMCM82633A004364', 1, '2025-08-04', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 07:05:02', '2025-08-05 07:05:02'),
(93, '145CM82633A004365', 1, '2025-08-04', NULL, 'Madrina', NULL, 'juan', NULL, 0, NULL, '2025-08-05 07:05:02', '2025-08-05 07:05:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `total_jobs` int(11) DEFAULT NULL,
  `pending_jobs` int(11) DEFAULT NULL,
  `failed_jobs` int(11) DEFAULT NULL,
  `failed_job_ids` longtext DEFAULT NULL,
  `options` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `finished_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salidas`
--

CREATE TABLE `salidas` (
  `id_salida` int(11) NOT NULL,
  `VIN` varchar(20) NOT NULL,
  `Motor` varchar(50) NOT NULL,
  `Version` varchar(30) NOT NULL,
  `Color` varchar(30) NOT NULL,
  `Tipo_salida` varchar(50) DEFAULT NULL,
  `Almacen_salida` int(11) DEFAULT NULL,
  `Almacen_entrada` int(11) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `Modelo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('31PH7C4liJlfiArYT9mPzXeBQDuFdhhVJwLlkjQH', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiS3gwMERJZEhVa1lLVXU0OFFTcFNDZnRqYWlzb1JPdmpWeWtvbVJQcCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi92ZWhpY3Vsb3M/YWxtYWNlbl9pZD0mZXN0YWRvPSZ2aW49MTkwQ004MjYzMyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6NDoiYXV0aCI7YToxOntzOjIxOiJwYXNzd29yZF9jb25maXJtZWRfYXQiO2k6MTc1NDM0OTk0ODt9fQ==', 1754355094),
('43BvFr4v3fTKjFD47zecZ0yeWePj6Im8hi1ke6wV', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiWWozVVU1WmZRMDdtdEFST3htazJaMTAzWmhzcU5nWVE0b0s2MG5XMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jaGVja2xpc3QvTWFkcmluYSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czo0OiJhdXRoIjthOjE6e3M6MjE6InBhc3N3b3JkX2NvbmZpcm1lZF9hdCI7aToxNzU0MzMzNzk1O319', 1754334008),
('5qw7NTBu6nf7mIsGBJfFODkA7RsPKiSt6X287pp1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid1NnZXVoejhyeUhRZEhJVTQzNEZneG9IUjJ1UFVHQlJUblVQbHdobCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jaGVja2xpc3QvTWFkcmluYSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754194367),
('By9zvBXI50vuxbzuFeO8gAfL8TQaGY97oX4TGCuw', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiUlFFOXo5OEt5UkJNeDR0alJKYmJocW1MRVZ1VFpuNE9zQVpjUHh4NyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM1OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vc2FsaWRhcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czo0OiJhdXRoIjthOjE6e3M6MjE6InBhc3N3b3JkX2NvbmZpcm1lZF9hdCI7aToxNzU0MTY2NTgxO319', 1754168622),
('JB0xjruheX6p5sf69Pv3j04x7H4f9pTkjoY6ocoI', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSEJYS1hKSTU5ZjJNR1FvRk4yRmRkQ21xWFhJVlhpTTBFaUxabnB0NyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9lbnRyYWRhcy8zMy9lZGl0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1754348584),
('lGIOBwSTnUzwNCpgSzWI8vjST5RNS6gchlbUoLhU', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiMmNHOVFYZnVsdnFxWFFjc2tPRExnZWVhSk5hYzVLS3VRZkkxOHJQaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi92ZWhpY3Vsb3MiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YTowOnt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjQ6ImF1dGgiO2E6MTp7czoyMToicGFzc3dvcmRfY29uZmlybWVkX2F0IjtpOjE3NTQwNzk1NTQ7fX0=', 1754093071),
('MwfFHJbRFm0Sz6Xbbe37aAjhEQhslgcrvRvsiLOJ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQlUzYUxMVno0cUozOFhmQ2ZJT0JaOVVSNjFJUjNXM0dCenZVdlVkSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jaGVja2xpc3QvTWFkcmluYSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754106339),
('xpIrZxjhjXex828vt7gILlw0nKCHbwousHiw43ji', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic0FBTU82cG9CZ204U2dwWG1nQ3VoMERqV2RUQTZNd2t5WkU3cWZrWCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9lbnRyYWRhcy8zMy9lZGl0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1754334502);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `google_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'juan', 'juan@example.com', NULL, '$2y$12$cDVORrogBPIxdJjONmhipuY8f2YDr0CWQEGAPdutv9tFM3vmo3C32', NULL, NULL, '2025-04-29 01:20:07', '2025-04-29 01:20:07'),
(3, 'Prueba Portal', 'pruebaportal@grupogranauto.mx', NULL, '$2y$12$zr2r7AfAIFUgOJnLLw7W6ublQP8Flxk2XwLZKZj4Plk8KHRqBW7TO', '105681389253993528613', NULL, '2025-07-10 08:45:38', '2025-07-10 08:45:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `VIN` varchar(17) NOT NULL,
  `Motor` varchar(50) NOT NULL,
  `Caracteristicas` varchar(30) NOT NULL,
  `Color` varchar(30) NOT NULL,
  `Modelo` varchar(100) NOT NULL,
  `Proximo_mantenimiento` date DEFAULT NULL,
  `Estado` varchar(50) DEFAULT NULL,
  `Coordinador_Logistica` varchar(50) DEFAULT NULL,
  `Almacen_actual` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tipo` enum('Madrina','Traspaso','Devolucion','Otro') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`VIN`, `Motor`, `Caracteristicas`, `Color`, `Modelo`, `Proximo_mantenimiento`, `Estado`, `Coordinador_Logistica`, `Almacen_actual`, `created_at`, `updated_at`, `tipo`) VALUES
('145CM82633A004365', '67TYFGDVC', 'Nissan Kicks', 'Negro', '2025', '2025-09-03', 'Mantenimiento', 'juan', 1, '2025-08-05 07:05:02', '2025-08-05 07:05:02', 'Madrina'),
('190CM82633A004356', 'FERF3W4F3', 'Nissan Frontier', 'Negro/Rojo', '2025', '2025-09-03', 'Mantenimiento', 'juan', 1, '2025-08-05 07:05:02', '2025-08-05 07:05:02', 'Madrina'),
('1HGCM82033A004361', '34REWFDWD', 'Nissan Frontier', 'Gris', '2025', '2025-09-03', 'Mantenimiento', 'juan', 1, '2025-08-05 07:05:02', '2025-08-05 07:05:02', 'Madrina'),
('1HGCM82633A004352', 'DVEFV', 'NISSAN MARCH', 'GRIS', '2023', '2025-08-31', 'disponible', 'juan', 1, '2025-08-02 06:49:12', '2025-08-02 07:04:30', 'Madrina'),
('1HGCM82633A004353', '67TYFGDVC', 'Nissan Frontier', 'Negro', '2025', '2025-09-03', 'Mantenimiento', 'juan', 1, '2025-08-02 07:02:17', '2025-08-05 07:05:02', 'Madrina'),
('1HGCM82633A004354', 'XYZ123458', 'Nissan sentra', 'Azul', '2025', '2025-08-31', 'Mantenimiento', 'juan', 1, '2025-08-02 07:02:17', '2025-08-02 07:02:17', 'Madrina'),
('1HGCM82633A004355', '4TFEDFE3FER', 'Tacoma', 'Negro', '2025', '2025-09-03', 'Mantenimiento', 'juan', 1, '2025-08-02 07:02:17', '2025-08-05 07:05:02', 'Madrina'),
('1HGCM82633A004356', 'XYZ123460', 'Nissan Frontier', 'Negro/Rojo', '2025', '2025-08-31', 'Mantenimiento', 'juan', 1, '2025-08-02 07:02:17', '2025-08-02 07:02:17', 'Madrina'),
('1HGCM82633A004357', 'XYZ123461', 'Nissan Magnite', 'Amarillo', '2025', '2025-08-31', 'Mantenimiento', 'juan', 1, '2025-08-02 07:02:17', '2025-08-02 07:02:17', 'Madrina'),
('1HGCM82633A004358', 'XYZ123462', 'Nissan X-trail', 'Naranja', '2025', '2025-08-31', 'Mantenimiento', 'juan', 1, '2025-08-02 07:02:17', '2025-08-02 07:02:17', 'Madrina'),
('1HGCM82633A004359', 'XYZ123463', 'Nissan versa', 'Gris/negro', '2025', '2025-08-31', 'Mantenimiento', 'juan', 1, '2025-08-02 07:02:17', '2025-08-02 07:02:17', 'Madrina'),
('1HGCM82633A004360', 'XYZ123464', 'Nissan Kicks', 'Blanco', '2025', '2025-08-31', 'Mantenimiento', 'juan', 1, '2025-08-02 07:02:17', '2025-08-02 07:02:17', 'Madrina'),
('1HGCM82633A004361', 'XYZ123465', 'Nissan Frontier', 'Gris', '2025', '2025-08-31', 'Mantenimiento', 'juan', 1, '2025-08-02 07:02:17', '2025-08-02 07:02:17', 'Madrina'),
('1HGCM82633A004362', 'XYZ123466', 'Nissan Magnite', 'Negro', '2025', '2025-08-31', 'Mantenimiento', 'juan', 1, '2025-08-02 07:02:17', '2025-08-02 07:02:17', 'Madrina'),
('1HGCM82633A004363', 'XYZ123467', 'Nissan X-trail', 'Azul', '2025', '2025-08-31', 'Mantenimiento', 'juan', 1, '2025-08-02 06:54:08', '2025-08-05 06:58:10', 'Madrina'),
('1HGCM82633A004364', 'XYZ123468', 'Nissan versa', 'Blanco', '2025', '2025-08-31', 'Mantenimiento', 'juan', 1, '2025-08-02 07:02:17', '2025-08-02 07:02:17', 'Madrina'),
('1HGCM82633A004365', 'XYZ123469', 'Nissan Kicks', 'Negro', '2025', '2025-08-31', 'Mantenimiento', 'juan', 1, '2025-08-02 07:02:17', '2025-08-02 07:02:17', 'Madrina'),
('1HGCM82689A004354', '89JHWYE8', 'Nissan sentra', 'Azul', '2025', '2025-09-03', 'Mantenimiento', 'juan', 1, '2025-08-05 07:05:02', '2025-08-05 07:05:02', 'Madrina'),
('1HGCM82783A004360', 'F344FW', 'Nissan Kicks', 'Blanco', '2025', '2025-09-03', 'Mantenimiento', 'juan', 1, '2025-08-05 07:05:02', '2025-08-05 07:05:02', 'Madrina'),
('1HGCP82633A004362', '34R3D', 'Nissan Magnite', 'Negro', '2025', '2025-09-03', 'Mantenimiento', 'juan', 1, '2025-08-05 07:05:02', '2025-08-05 07:05:02', 'Madrina'),
('1HMCM82633A004364', '342EWD', 'Nissan versa', 'Blanco', '2025', '2025-09-03', 'Mantenimiento', 'juan', 1, '2025-08-05 07:05:02', '2025-08-05 07:05:02', 'Madrina'),
('MXGCM82633A004358', 'VER343W', 'Nissan X-trail', 'Naranja', '2025', '2025-09-03', 'Mantenimiento', 'juan', 1, '2025-08-05 07:05:02', '2025-08-05 07:05:02', 'Madrina'),
('NMBVCXZAQ12345678', 'XYZ123467', 'Nissan X-trail', 'Azul', '2023', '2025-08-31', 'Mantenimiento', 'juan', 1, '2025-08-02 06:49:12', '2025-08-02 06:49:12', 'Madrina'),
('POGCM82633A004357', 'DFVERVER', 'Nissan Magnite', 'Amarillo', '2025', '2025-09-03', 'Mantenimiento', 'juan', 1, '2025-08-05 07:05:02', '2025-08-05 07:05:02', 'Madrina'),
('UHGCM82633A004359', '343RFE', 'Nissan versa', 'Gris/negro', '2025', '2025-09-03', 'Mantenimiento', 'juan', 1, '2025-08-05 07:05:02', '2025-08-05 07:05:02', 'Madrina');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `almacen`
--
ALTER TABLE `almacen`
  ADD PRIMARY KEY (`Id_Almacen`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `checklist`
--
ALTER TABLE `checklist`
  ADD PRIMARY KEY (`id_checklist`),
  ADD KEY `fk_no_orden_entrada_checklists` (`No_orden_entrada`);

--
-- Indices de la tabla `entradas`
--
ALTER TABLE `entradas`
  ADD PRIMARY KEY (`No_orden`),
  ADD KEY `fk_vin_vehiculos` (`VIN`),
  ADD KEY `fk_almacen_entrada` (`Almacen_entrada`),
  ADD KEY `fk_almacen_salida` (`Almacen_salida`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `salidas`
--
ALTER TABLE `salidas`
  ADD PRIMARY KEY (`id_salida`),
  ADD UNIQUE KEY `VIN` (`VIN`),
  ADD KEY `Almacen_salida` (`Almacen_salida`),
  ADD KEY `Almacen_entrada` (`Almacen_entrada`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD PRIMARY KEY (`VIN`),
  ADD KEY `fk_almacen_actual` (`Almacen_actual`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `almacen`
--
ALTER TABLE `almacen`
  MODIFY `Id_Almacen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `checklist`
--
ALTER TABLE `checklist`
  MODIFY `id_checklist` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT de la tabla `entradas`
--
ALTER TABLE `entradas`
  MODIFY `No_orden` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `salidas`
--
ALTER TABLE `salidas`
  MODIFY `id_salida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `checklist`
--
ALTER TABLE `checklist`
  ADD CONSTRAINT `fk_no_orden_entrada_checklists` FOREIGN KEY (`No_orden_entrada`) REFERENCES `entradas` (`No_orden`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `entradas`
--
ALTER TABLE `entradas`
  ADD CONSTRAINT `fk_almacen_entrada` FOREIGN KEY (`Almacen_entrada`) REFERENCES `almacen` (`Id_Almacen`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_almacen_salida` FOREIGN KEY (`Almacen_salida`) REFERENCES `almacen` (`Id_Almacen`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_vin_vehiculos` FOREIGN KEY (`VIN`) REFERENCES `vehiculos` (`VIN`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `salidas`
--
ALTER TABLE `salidas`
  ADD CONSTRAINT `fk_salidas_vehiculos` FOREIGN KEY (`VIN`) REFERENCES `vehiculos` (`VIN`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `salidas_ibfk_1` FOREIGN KEY (`Almacen_salida`) REFERENCES `almacen` (`Id_Almacen`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `salidas_ibfk_2` FOREIGN KEY (`Almacen_entrada`) REFERENCES `almacen` (`Id_Almacen`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD CONSTRAINT `fk_almacen_actual` FOREIGN KEY (`Almacen_actual`) REFERENCES `almacen` (`Id_Almacen`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
