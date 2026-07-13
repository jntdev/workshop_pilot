-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 29 juin 2026 à 17:23
-- Version du serveur : 10.6.21-MariaDB
-- Version de PHP : 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `vbmwfthz_workshop_pilot`
--

-- --------------------------------------------------------

--
-- Structure de la table `agenda_meta`
--

CREATE TABLE `agenda_meta` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `agenda_version` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `agenda_meta`
--

INSERT INTO `agenda_meta` (`id`, `agenda_version`, `created_at`, `updated_at`) VALUES
(1, 805, '2026-03-22 13:15:14', '2026-06-29 15:05:24');

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

CREATE TABLE `articles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `article_subcategory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reference` varchar(100) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `purchase_price_ht` int(11) NOT NULL DEFAULT 0,
  `sale_price_ttc` int(11) NOT NULL DEFAULT 0,
  `tva_rate` decimal(5,2) NOT NULL DEFAULT 20.00,
  `unit` varchar(50) NOT NULL DEFAULT 'pièce',
  `notes` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`id`, `article_subcategory_id`, `brand_id`, `supplier_id`, `reference`, `designation`, `purchase_price_ht`, `sale_price_ttc`, `tva_rate`, `unit`, `notes`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '520147-U', 'PLAQUETTE FREIN VTT 01 CLARKS ORGAN', 321, 1050, 20.00, 'paire', NULL, 0, '2026-06-16 19:18:57', '2026-06-16 19:18:57'),
(13, 15, 5, 1, '476517-u', 'Chambre à air 26 x 1.70-2.35', 495, 799, 20.00, 'pièce', NULL, 0, '2026-06-26 15:54:41', '2026-06-26 15:54:41'),
(9, 12, 2, 1, '468266', 'SONNETTE PING OPTIMIZ NOIR (DIAMETRE 22.2MM)', 79, 400, 20.00, 'pièce', NULL, 0, '2026-06-16 20:32:14', '2026-06-16 20:32:14'),
(10, 13, 3, 1, '479727', 'HUILE / LUBRIFIANT 3 EN 1 250ML CHAINE ET CABLE (AEROSOL) PTFE TEFLON', 567, 1035, 20.00, 'pièce', NULL, 0, '2026-06-16 20:33:51', '2026-06-16 20:33:51'),
(11, 14, 4, 1, '538383 -U', 'KMC 9V VAE antirouille', 1576, 3800, 20.00, 'pièce', 'chaine en rouleau', 0, '2026-06-24 12:54:20', '2026-06-24 12:54:20'),
(12, 15, 2, 1, '493772 - U', 'Chambre à air 29 x 2.10 - 2.40 VS', 200, 798, 20.00, 'pièce', 'livré en sachet de deux', 0, '2026-06-24 14:37:41', '2026-06-24 14:37:41');

-- --------------------------------------------------------

--
-- Structure de la table `article_categories`
--

CREATE TABLE `article_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `article_categories`
--

INSERT INTO `article_categories` (`id`, `name`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'freinage', 0, '2026-06-16 19:14:40', '2026-06-16 19:14:40'),
(10, 'entretient', 2, '2026-06-16 20:33:07', '2026-06-16 20:33:07'),
(12, 'pneumatique', 4, '2026-06-24 14:36:22', '2026-06-24 14:36:22'),
(11, 'transmission', 3, '2026-06-24 12:38:53', '2026-06-24 12:38:53'),
(9, 'accessoirs', 1, '2026-06-16 20:31:25', '2026-06-16 20:31:25');

-- --------------------------------------------------------

--
-- Structure de la table `article_subcategories`
--

CREATE TABLE `article_subcategories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `article_category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `article_subcategories`
--

INSERT INTO `article_subcategories` (`id`, `article_category_id`, `name`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'plaquettes', 0, '2026-06-16 19:14:52', '2026-06-16 19:14:52'),
(2, 1, 'patins', 1, '2026-06-16 19:15:01', '2026-06-16 19:15:01'),
(3, 1, 'câble', 2, '2026-06-16 19:15:08', '2026-06-16 19:15:08'),
(4, 1, 'gaine', 3, '2026-06-16 19:15:11', '2026-06-16 19:15:11'),
(15, 12, 'chambre à air', 0, '2026-06-24 14:36:28', '2026-06-24 14:36:28'),
(13, 10, 'lubrifiant chaine', 0, '2026-06-16 20:33:15', '2026-06-16 20:33:15'),
(14, 11, 'chaine', 0, '2026-06-24 12:38:58', '2026-06-24 12:38:58'),
(12, 9, 'sonettes', 0, '2026-06-16 20:31:34', '2026-06-16 20:31:34');

-- --------------------------------------------------------

--
-- Structure de la table `authorized_emails`
--

CREATE TABLE `authorized_emails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(191) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `authorized_emails`
--

INSERT INTO `authorized_emails` (`id`, `email`, `type`, `created_at`, `updated_at`) VALUES
(1, 'lesvelosdarmorbzh@gmail.com', 'admin', '2026-01-21 09:31:58', '2026-01-21 09:31:58'),
(2, 'jnt.marois@gmail.com', 'admin', '2026-01-21 09:31:58', '2026-01-21 09:31:58'),
(3, 'julien2705@gmail.com', 'admin', '2026-01-21 09:31:58', '2026-01-21 09:31:58');

-- --------------------------------------------------------

--
-- Structure de la table `bikes`
--

CREATE TABLE `bikes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bike_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bike_size_id` bigint(20) UNSIGNED DEFAULT NULL,
  `frame_type` varchar(5) DEFAULT NULL,
  `model` enum('500','625','autre') DEFAULT NULL,
  `battery_type` enum('rack','gourde','rail','intégrée') DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('OK','HS') NOT NULL DEFAULT 'OK',
  `notes` text DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `bikes`
--

INSERT INTO `bikes` (`id`, `bike_category_id`, `bike_size_id`, `frame_type`, `model`, `battery_type`, `name`, `status`, `notes`, `sort_order`, `created_at`, `updated_at`) VALUES
(48, 1, 1, 'b', '500', 'rack', 'Es1', 'OK', 'ERR 24 (une fois)', 0, '2026-02-16 20:17:03', '2026-05-23 06:30:17'),
(49, 1, 1, 'b', '500', 'rack', 'Es2', 'HS', 'bruit rack ALICIA', 1, '2026-02-16 20:17:12', '2026-05-28 15:09:56'),
(50, 1, 1, 'b', '500', 'rack', 'Es3', 'OK', NULL, 2, '2026-02-16 20:17:19', '2026-06-20 15:26:43'),
(51, 1, 2, 'b', '500', 'rack', 'Em4', 'OK', NULL, 0, '2026-02-16 20:17:31', '2026-02-17 08:20:42'),
(52, 1, 2, 'b', '500', 'rack', 'Em5', 'OK', NULL, 1, '2026-02-16 20:17:37', '2026-02-17 08:20:35'),
(53, 1, 2, 'b', '500', 'rack', 'em6', 'OK', NULL, 2, '2026-02-16 20:17:42', '2026-05-28 08:47:57'),
(54, 1, 2, 'b', '500', 'rack', 'Em7', 'OK', NULL, 3, '2026-02-16 20:17:48', '2026-02-17 08:20:21'),
(55, 1, 2, 'b', '500', 'rack', 'Em8', 'OK', NULL, 4, '2026-02-16 20:17:53', '2026-05-22 14:51:26'),
(56, 1, 2, 'h', '500', 'rack', 'Em1', 'OK', 'manque puissance', 0, '2026-02-16 20:18:35', '2026-04-30 12:26:14'),
(57, 1, 3, 'h', '500', 'rack', 'EL1', 'OK', 'deraille plateau a voir', 0, '2026-02-16 20:19:02', '2026-05-28 15:09:22'),
(58, 1, 3, 'h', '500', 'gourde', 'EL4', 'HS', 'probleme moteur', 0, '2026-02-16 20:19:15', '2026-06-24 14:16:23'),
(59, 1, 3, 'b', '500', 'rack', 'EL5', 'OK', NULL, 1, '2026-02-16 20:19:25', '2026-02-17 08:21:45'),
(60, 2, 1, 'b', '500', NULL, 'S2', 'OK', NULL, 0, '2026-02-16 20:19:59', '2026-04-21 15:01:19'),
(62, 2, 2, 'h', '500', NULL, 'M4', 'OK', NULL, 0, '2026-02-16 20:20:32', '2026-02-17 08:22:36'),
(63, 2, 2, 'h', '500', NULL, 'M5', 'OK', NULL, 1, '2026-02-16 20:20:41', '2026-02-17 08:22:40'),
(64, 2, 2, 'h', '500', NULL, 'M6', 'OK', NULL, 2, '2026-02-16 20:20:49', '2026-02-17 08:22:44'),
(65, 2, 2, 'h', '500', NULL, 'M7', 'OK', NULL, 3, '2026-02-16 20:20:57', '2026-02-17 08:22:48'),
(66, 2, 2, 'b', '500', NULL, 'M9', 'OK', NULL, 4, '2026-02-16 20:21:08', '2026-05-23 06:38:30'),
(67, 2, 2, 'b', '500', NULL, 'M10', 'OK', NULL, 5, '2026-02-16 20:21:15', '2026-05-23 07:20:42'),
(70, 2, 2, 'h', 'autre', NULL, 'M1', 'OK', NULL, 0, '2026-02-16 20:21:55', '2026-05-23 06:37:36'),
(71, 2, 2, 'b', '500', NULL, 'M2', 'OK', NULL, 1, '2026-02-16 20:22:03', '2026-02-17 08:22:21'),
(72, 2, 3, 'b', '500', NULL, 'L3', 'HS', 'check derailleur vitesses basses', 0, '2026-02-16 20:22:11', '2026-05-28 15:08:52'),
(73, 2, 3, 'b', '500', NULL, 'L4', 'OK', NULL, 1, '2026-02-16 20:22:18', '2026-05-23 06:45:56'),
(74, 1, 1, 'b', '500', 'rack', 'Es6', 'OK', 'lumiere avant HS', 3, '2026-02-21 13:57:19', '2026-06-20 12:57:49'),
(79, 4, 2, 'b', '500', NULL, 'remorque verte', 'OK', NULL, 1, '2026-02-27 18:16:26', '2026-02-27 18:16:26'),
(78, 4, 2, 'b', '500', NULL, 'remorque bleue', 'OK', NULL, 0, '2026-02-27 18:15:30', '2026-02-27 18:16:15'),
(80, 4, 2, 'b', '500', NULL, 'remorque verte 2', 'OK', NULL, 2, '2026-02-27 18:16:36', '2026-02-27 18:16:36'),
(81, 1, 2, 'b', '500', 'rack', 'Em2', 'HS', 'deraille plateau\nmanque de puissance?', 5, '2026-03-18 17:03:03', '2026-03-21 17:16:15'),
(82, 1, 2, 'h', 'autre', 'rail', 'Em9', 'OK', NULL, 1, '2026-03-21 11:18:35', '2026-04-30 12:26:25'),
(83, 5, 2, 'b', 'autre', NULL, '24\" VE1', 'OK', NULL, 0, '2026-04-07 16:04:19', '2026-04-08 08:44:31'),
(84, 5, 1, 'b', 'autre', NULL, '20\" VE5', 'OK', NULL, 0, '2026-04-07 16:08:40', '2026-04-08 08:36:56'),
(85, 1, 2, 'b', '500', 'rack', 'em 11', 'OK', NULL, 6, '2026-04-08 07:57:26', '2026-04-08 07:57:26'),
(86, 5, 2, 'b', '500', NULL, '24\" VE2', 'OK', NULL, 7, '2026-04-08 08:36:19', '2026-04-08 08:36:45'),
(87, 5, 2, 'b', '500', NULL, '24\" VE3', 'OK', NULL, 0, '2026-04-08 08:36:38', '2026-04-08 08:36:38'),
(88, 5, 2, 'b', '500', NULL, '24\" VE4', 'OK', NULL, 8, '2026-04-08 08:37:39', '2026-04-08 08:37:39'),
(89, 5, 2, 'b', '500', NULL, '24\" VE0', 'OK', NULL, 9, '2026-04-08 08:38:03', '2026-04-08 08:38:03'),
(90, 5, 1, 'b', '500', NULL, '20\" VE6', 'OK', NULL, 1, '2026-04-08 08:38:23', '2026-04-19 15:36:22'),
(91, 5, 1, 'b', '500', NULL, '20\" VE7', 'OK', NULL, 2, '2026-04-08 08:38:44', '2026-04-19 15:36:27'),
(92, 1, 3, 'h', '500', 'rack', 'EL3', 'OK', NULL, 1, '2026-04-10 07:19:32', '2026-04-10 07:19:32'),
(102, 1, 4, 'b', '500', 'intégrée', 'NCM 1', 'OK', NULL, 0, '2026-06-05 21:16:55', '2026-06-05 21:16:55'),
(93, 5, 1, 'h', 'autre', NULL, '20\" VE8', 'OK', NULL, 0, '2026-04-19 15:36:12', '2026-04-19 15:36:12'),
(94, 1, 3, 'b', '500', 'rack', 'EL6', 'OK', NULL, 2, '2026-04-19 16:39:46', '2026-04-19 16:39:46'),
(95, 2, 1, 'b', 'autre', NULL, 'S1', 'OK', NULL, 0, '2026-04-21 14:50:52', '2026-04-21 14:50:52'),
(98, 1, 3, 'h', '500', 'rack', 'EL2', 'OK', NULL, 2, '2026-05-01 10:03:51', '2026-06-25 06:44:09'),
(96, 1, 1, 'b', '500', 'rack', 'Es5', 'OK', NULL, 4, '2026-04-30 08:30:09', '2026-04-30 08:30:09'),
(97, 1, 2, 'b', '500', 'rack', 'Em3', 'OK', NULL, 7, '2026-05-01 09:21:10', '2026-05-01 09:21:10'),
(99, 4, 2, 'b', '500', NULL, 'carriole animaux', 'OK', NULL, 3, '2026-05-05 14:57:34', '2026-05-05 14:57:34'),
(100, 2, 3, 'h', '500', NULL, 'L1', 'OK', NULL, 0, '2026-05-09 09:05:56', '2026-05-28 14:24:14'),
(101, 6, NULL, NULL, '500', 'rack', 'CG7', 'OK', 'long tail', 0, '2026-06-05 21:16:36', '2026-06-05 21:16:36'),
(103, 1, 4, 'b', '500', 'intégrée', 'NCM 2', 'OK', NULL, 1, '2026-06-05 21:17:16', '2026-06-05 21:17:16'),
(104, 1, 4, 'b', NULL, 'intégrée', 'T3S', 'OK', NULL, 2, '2026-06-13 12:02:32', '2026-06-13 12:02:32'),
(105, 1, 4, 'b', '500', 'intégrée', 'NCM3', 'OK', NULL, 3, '2026-06-16 12:08:48', '2026-06-16 12:08:48'),
(106, 2, 4, 'h', NULL, NULL, 'M3', 'OK', NULL, 0, '2026-06-20 13:28:44', '2026-06-20 13:28:44'),
(107, 2, 1, 'h', NULL, NULL, 'S3', 'OK', NULL, 1, '2026-06-25 13:19:41', '2026-06-25 13:19:54');

-- --------------------------------------------------------

--
-- Structure de la table `bike_categories`
--

CREATE TABLE `bike_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#888888',
  `has_battery` tinyint(1) NOT NULL DEFAULT 0,
  `has_size` tinyint(1) NOT NULL DEFAULT 1,
  `has_frame_type` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `bike_categories`
--

INSERT INTO `bike_categories` (`id`, `name`, `color`, `has_battery`, `has_size`, `has_frame_type`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'VAE', '#FFD233', 1, 1, 1, 0, '2026-02-17 15:03:00', '2026-06-05 21:19:08'),
(2, 'VTC', '#005D66', 0, 1, 1, 2, '2026-02-17 15:03:00', '2026-06-05 21:19:08'),
(4, 'accessoires', '#6366f1', 0, 1, 1, 3, '2026-02-27 17:43:03', '2026-06-05 21:19:08'),
(5, 'ENFANT', '#ef4444', 0, 1, 1, 4, '2026-04-07 16:03:25', '2026-06-05 21:19:08'),
(6, 'Cargo', '#10b981', 1, 0, 0, 1, '2026-06-05 21:16:08', '2026-06-05 21:19:08');

-- --------------------------------------------------------

--
-- Structure de la table `bike_maintenance_logs`
--

CREATE TABLE `bike_maintenance_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `article_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bike_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `cost` int(10) UNSIGNED DEFAULT NULL COMMENT 'en centimes',
  `duration_minutes` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('todo','done') NOT NULL DEFAULT 'todo',
  `needs_order` tinyint(1) NOT NULL DEFAULT 0,
  `reference` varchar(255) DEFAULT NULL,
  `ordered_at` timestamp NULL DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `bike_maintenance_logs`
--

INSERT INTO `bike_maintenance_logs` (`id`, `article_id`, `bike_id`, `date`, `description`, `cost`, `duration_minutes`, `status`, `needs_order`, `reference`, `ordered_at`, `received_at`, `created_at`, `updated_at`) VALUES
(8, NULL, 59, '2026-06-23', 'chaine cran 1', 394, 10, 'todo', 0, NULL, NULL, NULL, '2026-06-23 06:55:00', '2026-06-23 06:55:00'),
(3, NULL, 55, '2026-06-17', 'chaine 8V cran +1', 394, 15, 'todo', 0, NULL, NULL, NULL, '2026-06-17 04:29:28', '2026-06-17 04:29:28'),
(4, NULL, 54, '2026-06-17', 'remplacement manette 8V', 0, NULL, 'todo', 0, 'a trouver', NULL, NULL, '2026-06-17 04:46:41', '2026-06-17 04:46:41'),
(5, NULL, 96, '2026-06-19', 'fonctionne par à coup (selon le client)', NULL, NULL, 'todo', 0, NULL, NULL, NULL, '2026-06-19 15:45:34', '2026-06-19 15:45:34'),
(6, NULL, 74, '2026-06-23', 'changer lampe AV', 800, 5, 'todo', 0, NULL, NULL, NULL, '2026-06-23 06:50:09', '2026-06-23 06:50:35'),
(9, NULL, 55, '2026-06-24', 'bruit pedalier \'anormale\' dire client', 0, NULL, 'todo', 0, NULL, NULL, NULL, '2026-06-24 14:57:47', '2026-06-24 14:57:47');

-- --------------------------------------------------------

--
-- Structure de la table `bike_models`
--

CREATE TABLE `bike_models` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `bike_models`
--

INSERT INTO `bike_models` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, '500', '2026-06-05 21:16:08', '2026-06-05 21:16:08'),
(2, '625', '2026-06-05 21:16:08', '2026-06-05 21:16:08');

-- --------------------------------------------------------

--
-- Structure de la table `bike_sizes`
--

CREATE TABLE `bike_sizes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(10) NOT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#888888',
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `bike_sizes`
--

INSERT INTO `bike_sizes` (`id`, `name`, `color`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'S', '#eab308', 0, '2026-02-17 15:03:00', '2026-02-17 15:13:14'),
(2, 'M', '#d97b5d', 2, '2026-02-17 15:03:00', '2026-02-17 15:13:19'),
(3, 'L', '#f43f5e', 4, '2026-02-17 15:03:00', '2026-02-17 15:13:22'),
(4, 'S/M', '#22c55e', 1, '2026-06-05 21:16:08', '2026-06-08 06:39:18'),
(5, 'M/L', '#c084fc', 3, '2026-06-05 21:16:08', '2026-06-05 21:16:08'),
(6, 'L/XL', '#e879f9', 5, '2026-06-05 21:16:08', '2026-06-05 21:16:08');

-- --------------------------------------------------------

--
-- Structure de la table `bike_types`
--

CREATE TABLE `bike_types` (
  `id` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `size` varchar(10) DEFAULT NULL,
  `frame_type` varchar(5) DEFAULT NULL,
  `label` varchar(255) NOT NULL,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `bike_types`
--

INSERT INTO `bike_types` (`id`, `category`, `size`, `frame_type`, `label`, `stock`, `created_at`, `updated_at`) VALUES
('VAE_sb', 'VAE', 'S', 'b', 'VAE S cadre bas', 5, '2026-02-16 16:26:15', '2026-06-20 15:26:43'),
('VAE_sh', 'VAE', 'S', 'h', 'VAE S cadre haut', 2, '2026-02-16 16:26:15', '2026-02-16 16:26:15'),
('VAE_mb', 'VAE', 'M', 'b', 'VAE M cadre bas', 8, '2026-02-16 16:26:15', '2026-05-28 08:47:57'),
('VAE_mh', 'VAE', 'M', 'h', 'VAE M cadre haut', 2, '2026-02-16 16:26:15', '2026-04-30 12:26:25'),
('VAE_lb', 'VAE', 'L', 'b', 'VAE L cadre bas', 2, '2026-02-16 16:26:15', '2026-04-28 15:59:21'),
('VAE_lh', 'VAE', 'L', 'h', 'VAE L cadre haut', 4, '2026-02-16 16:26:15', '2026-06-25 06:44:09'),
('VAE_xlb', 'VAE', 'XL', 'b', 'VAE XL cadre bas', 2, '2026-02-16 16:26:15', '2026-02-16 16:26:15'),
('VAE_xlh', 'VAE', 'XL', 'h', 'VAE XL cadre haut', 2, '2026-02-16 16:26:15', '2026-02-16 16:26:15'),
('VTC_sb', 'VTC', 'S', 'b', 'VTC S cadre bas', 2, '2026-02-16 16:26:15', '2026-06-25 13:19:54'),
('VTC_mb', 'VTC', 'M', 'b', 'VTC M cadre bas', 3, '2026-02-16 16:26:15', '2026-05-23 07:20:42'),
('VTC_mh', 'VTC', 'M', 'h', 'VTC M cadre haut', 5, '2026-02-16 16:26:15', '2026-05-23 07:20:42'),
('VTC_lb', 'VTC', 'L', 'b', 'VTC L cadre bas', 2, '2026-02-16 16:26:15', '2026-05-28 15:08:52'),
('VTC_lh', 'VTC', 'L', 'h', 'VTC L cadre haut', 1, '2026-02-16 16:26:15', '2026-05-28 14:24:14'),
('VTC_xlb', 'VTC', 'XL', 'b', 'VTC XL cadre bas', 2, '2026-02-16 16:26:15', '2026-02-16 16:26:15'),
('VTC_xlh', 'VTC', 'XL', 'h', 'VTC XL cadre haut', 3, '2026-02-16 16:26:15', '2026-02-16 16:26:15'),
('accessoires_mb', 'accessoires', 'M', 'b', 'accessoires M cadre bas', 4, '2026-02-27 18:15:30', '2026-05-05 14:57:34'),
('ENFANT_sh', 'ENFANT', 'S', 'h', 'ENFANT S cadre haut', 1, '2026-04-19 15:36:12', '2026-04-19 15:36:12'),
('ENFANT_sb', 'ENFANT', 'S', 'b', 'ENFANT S cadre bas', 3, '2026-04-07 16:08:40', '2026-04-19 15:36:27'),
('ENFANT_mb', 'ENFANT', 'M', 'b', 'ENFANT M cadre bas', 5, '2026-04-08 08:36:38', '2026-04-08 08:44:31'),
('Cargo_', 'Cargo', NULL, NULL, 'Cargo', 1, '2026-06-05 21:16:36', '2026-06-05 21:16:36'),
('VAE_s/mb', 'VAE', 'S/M', 'b', 'VAE S/M cadre bas', 4, '2026-06-05 21:16:55', '2026-06-16 12:08:48'),
('VTC_s/mh', 'VTC', 'S/M', 'h', 'VTC S/M cadre haut', 1, '2026-06-20 13:28:44', '2026-06-20 13:28:44'),
('VTC_sh', 'VTC', 'S', 'h', 'VTC S cadre haut', 1, '2026-06-25 13:19:54', '2026-06-25 13:19:54');

-- --------------------------------------------------------

--
-- Structure de la table `brands`
--

CREATE TABLE `brands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `brands`
--

INSERT INTO `brands` (`id`, `name`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Clarks', 1, '2026-06-16 19:15:39', '2026-06-16 19:15:39'),
(2, 'Optimiz', 2, '2026-06-16 20:31:53', '2026-06-16 20:31:53'),
(3, 'WD40', 3, '2026-06-16 20:33:25', '2026-06-16 20:33:25'),
(4, 'KMC', 4, '2026-06-24 12:39:11', '2026-06-24 12:39:11'),
(5, 'Hutchinson', 5, '2026-06-26 15:53:33', '2026-06-26 15:53:33');

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('workshop-pilot-cache-agenda_version', 'i:805;', 1782753834);

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `telephone` varchar(255) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `origine_contact` varchar(255) DEFAULT NULL,
  `commentaires` text DEFAULT NULL,
  `avantage_type` enum('aucun','pourcentage','montant') NOT NULL DEFAULT 'aucun',
  `avantage_valeur` decimal(8,2) NOT NULL DEFAULT 0.00,
  `avantage_expiration` datetime DEFAULT NULL,
  `avantage_applique` tinyint(1) NOT NULL DEFAULT 0,
  `avantage_applique_le` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id`, `prenom`, `nom`, `telephone`, `email`, `adresse`, `origine_contact`, `commentaires`, `avantage_type`, `avantage_valeur`, `avantage_expiration`, `avantage_applique`, `avantage_applique_le`, `created_at`, `updated_at`) VALUES
(1, 'L\'Hostis', 'Claudine', '07 88 20 36 65', '', '', NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-01-24 15:05:10', '2026-01-24 15:05:10'),
(2, 'Stephane', 'Avart', '0296558178', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-01-26 09:43:00', '2026-01-26 09:43:00'),
(3, 'Nicole', 'Le Barbu', '07 67 44 54 92', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-01-27 14:57:45', '2026-01-27 15:07:27'),
(4, 'm', 'Le Scoarnec', '06 66 34 56 48', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-01-27 15:45:34', '2026-01-27 15:45:34'),
(5, 'm', 'Briand', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-01-27 15:46:00', '2026-01-27 15:46:00'),
(6, 'Mme', 'Le deroff', '06 63 47 70 87', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-01-27 15:46:29', '2026-01-27 15:46:29'),
(7, 'Veronique', 'Baudat', '07 50 20 59 41', 'veronik.baudat@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-01-27 15:46:52', '2026-02-10 14:59:54'),
(8, 'Miloud', 'Ramdane', '0760319618', 'cadoret.helene@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-01-27 15:47:15', '2026-06-24 14:42:51'),
(9, 'Sophie', 'Kerimol', '06 52 64 25 00', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-01-27 15:47:44', '2026-01-27 15:47:44'),
(10, 'Pierre', 'Ayral', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-01-27 15:48:02', '2026-01-27 15:48:02'),
(11, 'Typhenn', 'Cabioch', '06 50 84 22 02', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-01-29 13:20:08', '2026-01-29 13:20:08'),
(12, 'M', 'Tricot', '06 70 43 12 87', 'frtricot92@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-01-29 13:44:56', '2026-02-04 10:42:19'),
(13, 'Henri', 'Vasserot', '+39 393 196 5033', 'public@vasserot.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-01-30 08:53:02', '2026-01-30 08:53:02'),
(14, 'Frederic', 'Bernabé', '06 33 88 85 74', 'frederic.bernabe@free.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-03 08:58:15', '2026-02-19 09:30:21'),
(15, 'Francoise', 'Becouarn', '06 76 34 25 74', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-04 09:47:55', '2026-02-04 09:47:55'),
(16, 'Mme', 'Barbier', '06 20 40 23 22', 'b.barbier@hotmail.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-04 16:45:19', '2026-02-04 16:53:19'),
(17, 'm', 'Le Moal', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-04 16:59:21', '2026-02-04 16:59:21'),
(22, 'francois', 'gaillard', '0', 'contact@francoisgaillard.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-10 09:04:29', '2026-02-10 09:04:29'),
(19, 'helia', 'couster', '0', NULL, 'inconnue', NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-06 13:27:45', '2026-02-06 13:27:45'),
(20, 'Catherine', 'Zelvelder', '06 33 61 37 02', 'michel.zelvelder@free.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-07 15:40:45', '2026-05-30 08:56:47'),
(21, 'Mélanie', 'Balcou', '06 65 24 24 06', 'balcoumelanie@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-09 08:47:34', '2026-02-09 09:38:03'),
(23, 'Maurice', 'Hamon', '06 87 83 44 94', 'mauhamon@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-12 10:39:56', '2026-02-17 16:24:45'),
(24, 'Laurence', 'Pasquini', '06 62 31 92 21', NULL, '43 boulevard Arago 75013', NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-12 11:15:35', '2026-02-12 11:15:35'),
(25, 'Stéphanie', 'Duvivier', '06 89 93 79 42', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-16 20:25:11', '2026-05-07 16:16:29'),
(26, 'Mme', 'Delanoe', '07 69 35 52 57', 'rolande.stef@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-17 15:17:50', '2026-03-12 08:09:01'),
(27, 'Gaston', 'Prevot', '06 28 26 18 61', 'gaston.prevot@laposte.net', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-17 15:54:41', '2026-02-17 15:54:41'),
(28, 'm', 'Pichavant', '07 67 06 40 05', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-17 16:56:19', '2026-02-17 16:56:19'),
(29, 'Charles', 'Coquelin', '06 76 57 56 91', 'charles.coquelin@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-18 08:56:11', '2026-02-18 14:38:32'),
(30, 'Morgane', 'Cathala', '06 66 53 28 20', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-18 13:28:15', '2026-02-18 13:28:15'),
(31, 'Jeremie', 'Derrien', '06 11 90 16 93', 'allo@taxi-derrien.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-18 14:49:48', '2026-02-18 14:49:48'),
(32, 'Charlene', 'Couinet', '06 86 84 11 56', 'charlenecouinet@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-18 15:31:05', '2026-02-18 15:31:05'),
(33, 'anne', 'pidou', '06 37 77 37 74', 'annepidou@outlook.fr', '37b rue becot 22500 paimpol', NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-19 10:42:23', '2026-02-19 10:42:23'),
(34, 'mme', 'Gouault', '06 68 84 28 68', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-19 13:53:51', '2026-02-19 13:53:51'),
(35, 'mme', 'gouault', '06 68 84 28 68', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-19 13:56:03', '2026-02-19 13:56:03'),
(36, 'M', 'Delanoe - Bocher', '02 92 55 91 95', 'contact@lesvelosdarmor.bzh', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-19 14:10:08', '2026-02-19 14:24:05'),
(37, 'M', 'Le Brun', '06 47 70 72 49', 'laurent.lebrun123@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-20 08:36:23', '2026-02-20 08:36:23'),
(38, 'Maiwen', 'Duvigneaux', '06 04 01 69 40', 'maiwennduvigneaux8@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-20 16:42:25', '2026-02-20 16:42:25'),
(39, 'Corinne', 'Coquelle', '06 14 08 78 60', 'thecocotouch@yahoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-21 11:00:16', '2026-02-21 11:00:16'),
(40, 'philippe', 'boucher', '06 58 88 79 69', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-21 13:18:34', '2026-02-21 13:18:34'),
(41, 'erwann', 'leffray', '06 63 98 57 67', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-21 13:53:22', '2026-02-21 13:53:22'),
(42, 'maxime', 'le merrer', '07 59 77 22 71', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-21 14:57:28', '2026-02-21 14:57:28'),
(43, 'Benoit', 'Dabout', '06 49 98 53 19', 'benoit.dabout@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-24 10:27:46', '2026-02-24 10:27:46'),
(44, 'tout venant', 'passant', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-24 16:26:34', '2026-02-24 16:26:34'),
(45, 'coralie', 'paris', '06 48 55 10 37', 'coralie.paris22580@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-25 14:02:36', '2026-02-25 14:02:36'),
(46, 'ingrid', 'Sassier', '06 98 61 31 68', 'ingrid.sassier@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-25 15:09:13', '2026-02-25 15:09:13'),
(47, 'M', 'Bergot', '06 77 18 88 40', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-26 16:14:44', '2026-02-26 16:14:44'),
(48, 'Catherine', 'Olichon', '06 06 45 42 32', 'ca.olichon@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-27 16:47:15', '2026-02-27 16:47:15'),
(49, 'Jonathan', 'Marois', '0699341246', 'jnt.marois@gmail.com', '1B Chemin des Bruyères', NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-27 18:15:49', '2026-02-27 18:15:49'),
(50, 'jean marc', 'wasselet', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-28 07:59:41', '2026-02-28 07:59:41'),
(51, 'julie', 'rojon', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-28 10:54:00', '2026-02-28 10:54:00'),
(52, 'helene', 'Ly', '06 26 85 00 15', 'helene-Ly@hotmail.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-28 10:58:01', '2026-02-28 10:58:01'),
(53, 'M', 'Passant', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-28 13:25:02', '2026-02-28 13:25:02'),
(54, 'gaston', 'prevot', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-28 17:03:30', '2026-02-28 17:03:30'),
(55, 'daniel', 'bernard', '07 80 20 55 74', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-02-28 17:09:15', '2026-06-03 12:22:07'),
(56, 'Françoise', 'Lukaszewicz', '06 60 36 24 78', 'lukaszewicz.francoise@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-02 11:54:50', '2026-03-02 11:54:50'),
(57, 'Chantal', 'Boismare', '06 38 43 52 70', 'chantalboismare@orange.fr', '38', NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-03 10:32:57', '2026-03-03 10:32:57'),
(58, 'Fréderique', 'Digard', '06 86 37 50 95', 'frederiquedigard@yahoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-04 08:44:12', '2026-03-04 08:44:12'),
(59, 'Cécile', 'Poisnel', '06 13 42 45 11', 'cecilepoisnel@outlook.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-04 14:26:50', '2026-03-04 14:26:50'),
(60, 'Frederique', 'Le Calvez', '06 50 51 38 40', 'fred.lecalvez@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-05 09:03:21', '2026-03-05 09:03:21'),
(61, 'm', 'passant', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-05 10:34:10', '2026-03-05 10:34:10'),
(62, 'Alice', 'Jegourel', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-05 10:35:45', '2026-03-05 10:35:45'),
(63, 'Estelle', 'Algrain', '06 22 66 33 19', 'estelle.algrain3@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-05 15:30:19', '2026-03-05 15:30:19'),
(64, 'Yann', 'Huchet du Guermeur', '06 08 09 07 16', 'yann.huchet50@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-06 09:15:18', '2026-03-06 09:15:18'),
(65, 'Boston', 'Mrelay', '0', 'boston.rognan@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-06 09:17:15', '2026-03-06 10:41:38'),
(66, 'M', 'passant', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-06 16:57:20', '2026-03-06 16:57:20'),
(67, 'fils', 'Huchet', '06 37 92 26 39', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-06 16:59:35', '2026-03-06 16:59:35'),
(68, 'Stephane', 'Hervo', '07 83 95 15 63', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-07 17:15:38', '2026-03-07 17:15:38'),
(69, 'Daniel', 'Conan', '06 41 57 36 33', 'dgconan@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-09 09:26:21', '2026-03-09 09:26:21'),
(70, 'Cedric', 'Vidal', '06 76 06 23 69', 'cedricovidalo@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-09 16:26:18', '2026-03-09 16:26:18'),
(71, 'Alain', 'Fournol', '06 08 48 04 08', 'alainfournol@yahoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-09 16:52:15', '2026-03-09 16:52:15'),
(72, 'Fabien', 'Szumigay', '06 60 70 48 90', 'fdelusion@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-09 17:01:01', '2026-03-11 17:32:33'),
(73, 'Bernard', 'Daniel', '07 80 20 55 74', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-09 17:05:51', '2026-05-02 08:22:22'),
(74, 'Yves', 'Hervé', '06 81 35 37 06', 'yvesherve@numericable.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-10 15:07:59', '2026-03-10 15:07:59'),
(75, 'Solina', 'Choron', '06 38 28 82 07', 'aurorechoron35@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-10 16:42:43', '2026-03-10 16:42:43'),
(76, 'mme', 'Gombault', '06 75 60 26 82', 'brehatfamily22@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-11 08:56:44', '2026-03-24 07:19:23'),
(77, 'Jean Paul', 'Guillemart', '06 21 12 09 11', 'jeanpaul.landes@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-11 09:49:50', '2026-03-11 09:49:50'),
(78, 'ambre', 'le bris', '0660324489', 'ambreboubaker@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-11 13:33:13', '2026-03-11 13:33:13'),
(79, 'Sonia', 'Le Fevre', '06 10 87 02 24', 'Sonia22.lefevre@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-11 15:43:18', '2026-03-11 15:43:18'),
(80, 'Erwan', 'Rabé', '06 98 01 06 10', 'erwan.rabe@yahoo.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-11 16:26:46', '2026-04-10 09:09:01'),
(81, 'Christian', 'Brunel', '06 26 65 26 73', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-11 20:37:49', '2026-03-11 20:37:49'),
(82, 'Mr', 'cagniet gonin', '07 87 84 66 30', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-12 08:27:30', '2026-03-12 08:37:33'),
(83, 'Sylvain', 'Bataillie', '06 89 80 33 92', 'batailliesylvain@gmail.com', 'kervarabes 22870 île de bréhat', NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-12 15:41:01', '2026-03-12 15:41:01'),
(84, 'daniel', 'Joniot', '06 85 69 40 43', 'daniel.joniot@outlook.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-12 15:43:29', '2026-04-30 15:31:16'),
(85, 'Mme', 'allainguillaume', '06 64 87 51 22', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-13 13:15:58', '2026-03-13 13:15:58'),
(86, 'Elise', 'BEAUCHESNE', '06 14 75 23 65', 'elise@upway.shop', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-16 10:13:27', '2026-03-16 10:13:27'),
(87, 'yoann', 'god', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-16 20:39:11', '2026-03-16 20:39:11'),
(88, 'Mme', 'Goarin', '06 51 15 78 71', NULL, NULL, 'Mr goarin', NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-17 14:40:48', '2026-03-17 14:40:48'),
(89, 'Veronique', 'Levieil', '06 23 41 24 32', 'levieilvero@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-17 15:38:12', '2026-03-17 15:38:12'),
(90, 'Lionel', 'carnec', '06 59 16 63 47', 'lionel.carnec@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-17 15:56:45', '2026-03-25 09:32:00'),
(91, 'Jacques', 'Piry', '06 14 11 49 69', 'jacques.piry@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-17 16:35:57', '2026-03-17 16:35:57'),
(92, 'Marie-Paule', 'Dahan', '06 82 37 87 82', 'marie-paule.dahan@wanadoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-18 09:50:57', '2026-03-18 09:50:57'),
(93, 'Yoann', 'Guillou', '06 31 80 18 26', 'guillouyoann@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-18 14:54:12', '2026-03-18 14:54:12'),
(94, 'Arnaud', 'Fournier', '06 08 01 80 75', 'arnaudpiano@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-18 15:26:19', '2026-03-18 15:26:19'),
(95, 'serge', 'schrepel', '06 08 06 42 94', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-18 17:04:02', '2026-03-18 17:05:56'),
(96, 'Stephane', 'Coquin', '07 66 68 28 45', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-19 17:38:31', '2026-03-20 08:18:43'),
(97, 'Paul', 'Zissala', '06 26 87 40 19', 'zissalapaul@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-21 09:03:57', '2026-03-21 09:03:57'),
(98, 'valerie', 'le gledic', '06 80 23 35 10', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-21 09:52:04', '2026-03-21 09:52:04'),
(99, 'Kem', 'de houwer', '06 40 78 40 20', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-21 10:22:20', '2026-03-21 10:22:20'),
(100, 'benoit', 'Levieux', '07 72 32 79 28', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-21 11:20:10', '2026-03-21 11:20:10'),
(101, 'jean claude', 'arze', '06 82 05 27 63', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-21 14:49:36', '2026-03-21 14:49:36'),
(102, 'Jonathan', 'Marois', '0699341246', NULL, '1B Chemin des Bruyères', NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-23 16:32:26', '2026-03-23 16:32:26'),
(103, 'tiffany', 'Mercier', '07 86 92 18 63', 'tiffanymercier2259@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-24 09:06:33', '2026-03-24 09:06:33'),
(104, 'Anthony', 'Humeau', '06 75 11 70 78', 'AnthonyHumeau@hotmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-24 13:55:20', '2026-03-24 13:55:20'),
(105, 'Marie', 'Herbouille', '06 26 97 19 19', 'herbouille.marie@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-26 08:09:03', '2026-05-01 20:25:59'),
(106, 'Bertrand', 'Feuillatre', '06 43 08 75 87', 'bertrandfeuillatre0@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-26 16:04:01', '2026-03-26 16:04:01'),
(107, 'claude', 'huitorel', '06 72 74 75 78', 'huitorelclaude@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-27 13:42:44', '2026-03-27 13:42:44'),
(108, 'Sylvie', 'Baynaud', '06 62 47 24 78', 'baynaud.sylvie@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-27 16:10:06', '2026-03-27 16:10:06'),
(109, 'Esther', 'Weber', '06 70 45 72 08', 'weber.esther@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-27 21:38:39', '2026-03-27 21:38:39'),
(110, 'luc', 'beranger', '06 14 48 04 54', 'luc.beranger@neuf.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-28 15:03:59', '2026-03-28 15:03:59'),
(111, 'louis', 'keromest', '06 25 18 49 38', 'louis.keromest@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-28 16:52:33', '2026-03-28 16:52:33'),
(112, 'Bertrand', 'Lourrier', '06 31 16 64 53', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-30 14:56:33', '2026-03-30 14:56:33'),
(113, 'Jean Pierre', 'Cosiaux', '06 78 25 74 46', 'jeanpierrecosiaux@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-31 08:14:05', '2026-03-31 09:07:39'),
(114, 'Patrick', 'Le Moine', '06 75 36 06 56', 'paddylemoine@wanadoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-31 13:08:39', '2026-03-31 13:08:39'),
(115, 'Hartmuth', 'Barché', '06 63 52 73 84', 'barche.hartmuth@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-31 13:59:53', '2026-03-31 13:59:53'),
(116, 'Frederique', 'Arnaud', '06 45 48 43 77', 'fred.kersa@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-03-31 15:16:55', '2026-03-31 15:16:55'),
(117, 'Hervé', 'Probst', '07 72 21 71 55', 'herveprobst94@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-01 15:21:08', '2026-04-01 15:21:08'),
(118, 'elisabeth', 'legeai', '06 59 82 45 29', 'babethlegeai@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-02 07:52:40', '2026-04-02 07:52:40'),
(119, 'Eric', 'Binard', '06 73 94 63 58', 'be.bi@wanadoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-02 12:36:23', '2026-04-02 12:36:23'),
(120, 'Armelle', 'Le Muzic', '06 79 68 48 14', 'armelle.lemuzic@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-02 14:26:04', '2026-04-02 14:26:04'),
(121, 'odile', 'barbotin', '06 06 46 45 10', 'odile.barbotin@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-02 15:55:11', '2026-04-02 15:55:11'),
(122, 'nadege', 'urbain', '06 85 28 29 67', 'nadegeurbain@sfr.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-03 15:08:29', '2026-04-03 15:08:29'),
(123, 'jean', 'palix', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-04 07:27:38', '2026-04-04 07:27:38'),
(124, 'norbert', 'jacquart', '06 78 90 22 79', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-04 09:34:04', '2026-04-04 09:34:04'),
(125, 'odile', 'coffin', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-04 16:03:13', '2026-04-04 16:03:13'),
(126, 'martine', 'airault', '06 83 41 90 88', NULL, '06 46 72 99 41', NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-04 16:04:34', '2026-04-04 16:04:34'),
(127, 'Olivier', 'Lallemant', '06 82 21 13 84', 'olallemant@yahoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-07 09:39:55', '2026-04-07 09:39:55'),
(128, 'Estelle', 'Thibault', '0', 'thibault.estelle@wanadoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-07 15:11:53', '2026-04-07 15:11:53'),
(129, 'Guy', 'Le Henaff', '06 88 97 20 88', 'guy.le-henaff@club-internet.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-07 16:52:53', '2026-04-07 16:52:53'),
(130, 'Olivier', 'Bony', '06 81 86 22 26', 'obony1@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-08 06:45:43', '2026-04-08 06:45:43'),
(131, 'Anne', 'Charpentier', '06 71 34 66 96', 'annecharpe@yahoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-08 07:58:43', '2026-04-08 07:58:43'),
(132, 'Olivier', 'Berzane', '0618193687', 'oberzane@yahoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-08 08:17:45', '2026-04-08 08:17:45'),
(133, 'm', 'Gaillemain', '06 11 14 91 74', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-08 08:43:38', '2026-04-08 08:43:38'),
(134, 'Juliette', 'Chevalier', '07 76 95 86 24', 'chev.juliette@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-08 12:12:25', '2026-04-08 12:12:25'),
(135, 'pelissier tanon', 'cedric', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-09 09:31:53', '2026-04-09 09:31:53'),
(136, 'ralph', 'Kolb', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-09 09:33:17', '2026-04-09 09:33:17'),
(137, 'David', 'Gaudenne', '06 12 44 10 02', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-09 09:36:09', '2026-04-09 09:36:09'),
(138, 'Mme', 'kissel', '06 82 22 28 81', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-09 13:05:58', '2026-04-09 13:05:58'),
(139, 'jo', 'balcou', '07 86 11 51 49', NULL, NULL, 'nakamura blanc', NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-09 14:52:23', '2026-04-09 14:52:23'),
(140, 'Lise', 'Picard', '06 68 96 17 31', 'lise.leboulbin@laposte.net', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-09 16:15:00', '2026-04-09 16:15:00'),
(141, 'celine', 'coudurier', '06 61 09 73 81', 'bouclesdor.22@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-10 12:35:50', '2026-04-10 12:35:50'),
(142, 'yvane', 'greco', '06 63 52 52 17', 'yvanegreco@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-11 09:34:23', '2026-04-11 09:34:23'),
(143, 'atelier', 'queffeulou', '06 61 45 91 00', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-11 12:06:20', '2026-04-11 14:12:38'),
(144, 'fabien', 'battello', '07 88 39 76 99', NULL, NULL, NULL, 'demi journee', 'aucun', 0.00, NULL, 0, NULL, '2026-04-11 14:35:48', '2026-04-11 14:35:48'),
(145, 'anne', 'bernard', '06 68 00 08 64', 'ennaplbz@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-11 14:39:14', '2026-04-11 14:39:14'),
(146, 'Charline', 'Avenel', '06 70 39 61 43', 'avenel.charline@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-14 07:31:33', '2026-05-14 09:04:19'),
(147, 'Ilaria', 'Scalise', '+39 333 5633526', 'ilaria.scalise@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-14 07:35:05', '2026-04-14 07:35:05'),
(148, 'Dufrenoy', 'Philippe', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-14 08:41:46', '2026-04-14 08:41:46'),
(149, 'celine', 'Froger', '06 62 94 56 39', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-14 12:21:15', '2026-04-14 12:21:15'),
(150, 'Christelle', 'Gobet', '06 12 56 95 53', 'chris92go@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-14 16:05:04', '2026-04-14 16:05:04'),
(151, 'Rodolphe', 'Labusquiere', '06 88 27 73 64', NULL, '6 rue de Goasplat', NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-15 13:46:35', '2026-04-15 13:46:35'),
(152, 'Thomas', 'Couchot', '06 13 49 06 36', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-15 15:30:52', '2026-04-15 15:30:52'),
(153, 'Corélie', 'Zoude', '07 66 17 21 98', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-16 15:19:59', '2026-04-16 15:19:59'),
(154, 'jojal', 'lebon', '0', NULL, NULL, NULL, 'demi', 'aucun', 0.00, NULL, 0, NULL, '2026-04-16 16:07:22', '2026-04-16 16:08:36'),
(155, 'Thibault', 'Perol', '06 21 06 27 29', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-17 07:54:06', '2026-04-17 07:54:06'),
(156, 'Jean Philippe', 'bidot', '06 11 73 33 56', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-17 13:45:46', '2026-04-17 13:45:46'),
(157, 'Helene', 'Pautrat', '06 59 81 18 34', 'helenepautrat@hotmail.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-17 13:48:18', '2026-04-17 13:48:42'),
(158, 'Mireille', 'Lambert', '07 85 41 98 95', 'yveslambert4@aol.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-17 14:24:49', '2026-04-17 14:45:05'),
(159, 'Nicolas', 'Grach', '06 73 14 96 19', 'nicolas.grach@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-17 15:30:57', '2026-04-17 15:30:57'),
(160, 'yvonnig', 'blanchet', '06 31 58 40 75', NULL, 'plouezec', NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-18 09:38:16', '2026-04-18 09:38:16'),
(161, 'alexis', 'gimenez', '0', NULL, NULL, NULL, 'demi', 'aucun', 0.00, NULL, 0, NULL, '2026-04-18 12:43:07', '2026-04-18 12:43:07'),
(162, 'glacier salon de thé', 'SAS TY Anne - Adam', '06 59 03 45 02', 'cuisine.islandais@gmail.com', 'place de la République 22500 Paimpol', NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-18 13:22:12', '2026-05-27 07:11:03'),
(163, 'antoine', 'cagniet', '07 87 84 66 30', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-18 14:31:39', '2026-04-18 14:31:39'),
(164, 'dominique', 'goncalves', '06 99 69 72 20', 'dominique.goncalvesconto@sfr.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-21 14:55:50', '2026-04-21 14:55:50'),
(165, 'pierre', 'demondion', '06 59 47 79 75', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-21 15:03:56', '2026-04-21 15:03:56'),
(166, 'isabelle', 'renvoise', '07 59 70 59 27', NULL, NULL, NULL, 'L potence\nM potence', 'aucun', 0.00, NULL, 0, NULL, '2026-04-21 15:07:57', '2026-04-21 15:08:38'),
(167, 'Philippe', 'Calmels', '06 74 37 30 03', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-22 09:28:14', '2026-04-22 09:28:14'),
(168, 'mathieu', 'jeanne', '06 62 78 49 24', NULL, NULL, NULL, 'demi heberg', 'aucun', 0.00, NULL, 0, NULL, '2026-04-22 09:28:32', '2026-04-22 09:28:32'),
(169, 'adrien', 'rublon', '06 71 06 20 17', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-22 14:17:02', '2026-04-22 14:17:02'),
(170, 'Henri', 'Meyer', '06 87 57 34 57', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-23 06:48:24', '2026-04-23 06:48:24'),
(171, 'serge', 'leffe', '06 66 67 39 45', NULL, NULL, 'courtoisie renault', '+ herbergeur', 'aucun', 0.00, NULL, 0, NULL, '2026-04-23 08:42:38', '2026-04-23 08:42:38'),
(172, 'charlotte', 'de clipelle', '06 88 36 79 57', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-23 08:49:21', '2026-04-23 08:49:21'),
(173, 'pierre', 'goudalier', '0', NULL, NULL, NULL, 'demi heber', 'aucun', 0.00, NULL, 0, NULL, '2026-04-23 15:39:23', '2026-04-23 15:39:23'),
(174, 'annick', 'guillemot', '06 76 15 01 11', 'nicou.guillemot@free.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-23 16:03:13', '2026-04-23 16:03:13'),
(175, 'ollivier', 'clament', '02 96 22 06 08', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-24 09:18:05', '2026-04-24 09:18:05'),
(176, 'gilles', 'malnory', '06 31 60 76 34', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-24 10:26:13', '2026-04-24 10:26:13'),
(177, 'stephanie', 'caro', '07 85 89 91 03', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-24 10:35:47', '2026-04-24 10:35:47'),
(178, 'cassiopée', 'decor', '06 17 04 11 49', 'cassiopee.decor@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-24 14:42:54', '2026-05-29 09:13:37'),
(179, 'natasha', 'condemine', '06 27 40 58 75', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-24 15:34:04', '2026-04-25 07:34:50'),
(180, '0', 'Ladde', '06 14 16 34 84', NULL, NULL, NULL, 'demi', 'aucun', 0.00, NULL, 0, NULL, '2026-04-24 16:13:43', '2026-04-24 16:14:13'),
(181, 'nicolas', 'verrerie de brehat', '07 69 94 04 25', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-25 07:42:33', '2026-04-25 07:42:33'),
(182, 'julien', 'mercier', '0', 'julien2705@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-25 09:32:42', '2026-04-25 09:32:42'),
(183, 'kamil', 'koscielecki', '07 85 09 79 89', 'kamilkoscielecki@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-25 13:29:41', '2026-04-25 13:29:41'),
(184, 'justine', 'lecutiez', '06 99 93 86 06', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-25 14:29:11', '2026-04-25 14:29:11'),
(185, 'silas', 'nerjat', '06 08 09 55 82', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-25 15:58:54', '2026-04-25 15:58:54'),
(186, 'SAV', 'Gaya', '09 74 99 86 28', 'administration@gaya.bike', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-27 10:13:39', '2026-04-27 10:13:39'),
(187, 'Lorraine', 'Carlotti', '06 28 82 08 54', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-27 12:26:29', '2026-04-27 12:26:29'),
(188, 'michele', 'ribaillier', '06 02 72 21 32', 'latornade2@hotmail.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-28 08:59:31', '2026-04-28 08:59:31'),
(189, 'christine', 'lepoittevin', '06 15 24 87 30', 'lamouet@hotmail.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-28 09:25:50', '2026-04-28 09:25:50'),
(190, 'Silke', 'Buenjes', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-28 09:55:54', '2026-04-28 09:55:54'),
(191, 'rodolph', 'labusquiere', '06 88 27 73 64', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-28 12:19:35', '2026-04-28 12:19:35'),
(192, 'M', 'Godest', '06 41 91 04 75', 'lgodest@yahoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-28 12:56:16', '2026-04-28 13:05:29'),
(193, 'Béatrice', 'Legrand', '06 82 01 37 73', 'beatrice.legrand1@sfr.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-28 13:43:13', '2026-04-28 13:43:13'),
(194, 'Marc', 'Breuzin', '06 06 49 35 81', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-29 13:06:44', '2026-04-29 13:06:44'),
(195, 'Kerguelen', 'Beaudron', '0', 'beaudronk@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-29 13:55:43', '2026-04-30 14:29:32'),
(196, 'Anne', 'Lenotte', '06 33 84 01 35', 'anne.lenotte@sfr.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-29 14:15:46', '2026-05-03 07:30:17'),
(197, 'Benoit', 'Volnait / Seguin', '06 50 65 91 85', 'benoitvolnais@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-29 14:36:05', '2026-05-05 14:52:52'),
(198, 'audrey', 'francon', '06 17 84 66 57', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-30 08:19:06', '2026-04-30 08:19:21'),
(199, 'Alexandre', 'Mojaisky', '0664018195', 'al.mojaisky@yahoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-30 10:57:27', '2026-04-30 10:57:27'),
(200, 'Frédéric', 'CHAIGNON', '06 86 54 01 42', 'f.chaignon@wanadoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-30 11:07:12', '2026-04-30 11:07:12'),
(201, 'Adèle', 'Goncalves', '06 49 38 37 80', 'adele.goncalves@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-30 11:13:25', '2026-04-30 11:13:25'),
(202, 'gerard', 'hello', '06 72 13 26 44', 'hello.gerard@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-30 13:52:37', '2026-04-30 13:52:37'),
(203, 'Laurent', 'Moret', '06 88 54 66 12', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-30 14:40:10', '2026-04-30 14:40:10'),
(204, 'Marjorie', 'Breton', '06 29 85 29 61', 'marjoriebreton28@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-30 15:49:24', '2026-06-15 07:30:03'),
(205, 'Benoit', 'MAROT', '0622900280', 'benoitmarot@yahoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-04-30 21:57:58', '2026-04-30 21:57:58'),
(206, 'François', 'Cauvin', '06 07 75 71 87', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-01 07:57:03', '2026-05-01 07:57:03'),
(207, 'Jerome', 'Magnard', '06 36 56 84 72', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-01 08:33:46', '2026-05-01 08:33:46'),
(208, 'Ut', 'Hartmann', '+49 173 25 666 02', 'Hartmann.Nietgen@t-online.de', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-01 10:20:28', '2026-05-01 10:20:54'),
(209, 'Erika', 'MOREAU', '06 23 36 47 35', 'erikamoreau@club-internet.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-01 16:26:54', '2026-05-01 16:26:54'),
(210, 'Charlotte', 'Jacobé', '0750995525', 'charlie.jacobe@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-01 22:43:03', '2026-05-01 22:43:03'),
(211, 'Agnès', 'SIGNORET', '0634282291', 'Agnes.SIGNORET@banque-france.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-02 11:33:53', '2026-05-02 11:33:53'),
(212, 'marion', 'caroff', '06 40 42 56 84', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-02 13:43:42', '2026-05-02 13:43:42'),
(213, 'Johann', 'Simon de Kergunic', '06 80 93 02 92', 'joanomad@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-04 09:29:14', '2026-05-04 09:29:14'),
(214, 'Didier', 'Costes', '07 67 89 98 34', 'didierlouis.c@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-05 12:54:24', '2026-05-05 12:54:24'),
(215, 'Jean-Guy', 'MAISONNEUVE', '0769063368', 'jgmaisonneuve@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-05 12:55:50', '2026-05-05 12:55:50'),
(216, 'Sylvie', 'Lucas', '06 72 69 81 19', 'lucasylvie49@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-05 15:04:06', '2026-05-05 15:04:06'),
(217, 'chloé', 'cosmidis', '07 81 83 61 21', 'chloecosmidis@yahoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-06 08:35:11', '2026-05-06 08:35:11'),
(218, 'Elise', 'Landenne', '0032 476 48 79 89', NULL, NULL, NULL, 'demi + 2PBB', 'aucun', 0.00, NULL, 0, NULL, '2026-05-06 09:26:32', '2026-05-06 13:42:11'),
(219, 'Vincent', 'Fabrege', '06 64 40 35 27', 'mv.fabreges@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-06 12:01:50', '2026-05-06 12:01:50'),
(220, 'daniel', 'besson', '06 18 14 56 67', NULL, NULL, NULL, 'demi', 'aucun', 0.00, NULL, 0, NULL, '2026-05-06 12:17:20', '2026-05-06 12:17:20'),
(221, 'Patricia', 'Hazard', '06 30 52 68 72', 'hazard.patricia@wanadoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-06 13:12:49', '2026-05-06 13:31:34'),
(222, 'delphine', 'Cordier', '06 80 76 73 59', 'delf.cordier@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-06 16:17:05', '2026-05-06 16:17:05'),
(223, 'Anthony', 'Belvaux', '07 71 94 75 77', 'anthonybelvaux@yahoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-06 17:16:24', '2026-05-06 17:16:24'),
(224, 'Yvain', 'Chambard', '06 87 85 78 81', 'yvainchambard994@hotmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-07 09:01:31', '2026-05-07 09:01:31'),
(225, 'Christophe', 'Perrey', '06 85 72 67 91', NULL, NULL, 'chambres St Michel', NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-07 09:49:45', '2026-05-07 09:49:45'),
(226, 'Veronique', 'Tartavel', '06 95 21 00 60', 'vronique.fleur@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-07 13:29:35', '2026-05-07 13:29:35'),
(227, 'Elise', 'Lengaigne', '06 85 15 93 16', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-08 08:31:29', '2026-05-08 08:31:29'),
(228, 'laurence', 'Vincelotte', '06 07 04 07 83', 'vincelotte.seregale@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-08 09:33:17', '2026-05-08 09:33:17'),
(229, 'Romain', 'Leconte', '07 59 59 30 24', 'romain.g.leconte@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-08 12:33:10', '2026-05-08 12:33:10'),
(230, 'xavier', 'borombo', '06 49 31 19 04', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-08 13:54:49', '2026-05-08 13:54:49'),
(231, 'jean marie', 'bourdon', '06 16 61 79 41', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-09 09:04:16', '2026-05-09 09:04:16'),
(232, 'Nathalie', 'Arnoux', '06 86 52 70 79', 'nathaliearnoux.miramont@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-09 11:06:48', '2026-05-09 11:06:48'),
(233, 'Corinne', 'Kauffmann', '06 41 69 25 04', 'corinne.kauffmann2@free.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-09 14:08:04', '2026-05-09 14:08:04'),
(234, 'Yan', 'Rospabe', '06 72 38 43 12', 'y.rospab@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-11 14:35:56', '2026-05-11 14:35:56'),
(235, 'Timothée', 'Gondet', '07 88 57 22 42', 'timothee.alex.gondet@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-11 14:38:24', '2026-05-11 14:38:24'),
(236, 'Serge', 'Briantais', '06 87 32 37 22', 'serge.briantais@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-12 07:10:48', '2026-05-12 07:10:48'),
(237, 'Jacques', 'Joffroy', '06 16 65 26 81', 'j.joffroy@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-12 09:09:54', '2026-05-12 09:09:54'),
(238, 'Lynda', 'Witz', '06 41 61 85 38', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-12 13:51:58', '2026-05-12 13:51:58'),
(239, 'johanna', 'neumager', '06 60 19 09 04', 'jneumager@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-12 15:50:20', '2026-05-12 15:50:20'),
(240, 'Bernard', 'Audoyer', '06 51 61 23 35', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-13 07:46:53', '2026-05-13 07:46:53'),
(241, 'M', 'Legras', '06 64 04 36 91', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-13 08:13:04', '2026-05-13 08:13:04'),
(242, 'Loic', 'Le guenec', '06 01 81 25 75', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-13 12:53:05', '2026-05-13 12:53:05'),
(243, 'Anouk', 'Servigne', '06 49 62 61 44', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-13 13:34:20', '2026-05-13 13:34:20'),
(244, 'Maeva', 'Diamant', '06 33 69 47 27', 'maevadiamant@hotmail.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-13 13:39:30', '2026-05-13 13:39:30'),
(245, 'Vahram', 'Muratyan', '06 25 08 55 18', 'hellovahram@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-13 15:35:19', '2026-05-13 15:35:19'),
(246, 'lucas', 'Brison', '06 38 87 80 25', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-14 15:45:05', '2026-05-14 15:45:05'),
(247, 'stephane', 'zwolinski', '06 74 11 14 92', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-15 09:05:23', '2026-05-15 09:05:23'),
(248, 'Antoine', 'Pecriaux', '06 62 08 92 72', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-15 10:00:54', '2026-05-15 10:00:54'),
(249, 'sebastien', 'bellenger', '06 82 06 43 35', 'bellenger.sebastien@neuf.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-15 15:19:31', '2026-05-15 15:19:31'),
(250, 'jean-gilles', 'fizaine', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-15 15:20:54', '2026-05-15 15:20:54'),
(251, 'marion', 'huchet', '06 37 99 53 02', 'marion.huchet@yahoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-16 10:04:07', '2026-05-16 10:04:07'),
(252, 'gwenael', 'le collen', '06 63 81 60 38', 'gwenlecollen0@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-16 12:26:28', '2026-05-16 12:26:28'),
(253, 'pierre', 'RIEHL', '06 41 55 00 02', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-16 12:33:39', '2026-05-16 12:33:39'),
(254, 'loic', 'cordier', '07 81 30 63 31', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-16 13:45:53', '2026-05-16 13:45:53'),
(255, 'blandine', 'busson', '06 81 07 57 21', 'blandine.busson@wanadoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-16 14:25:09', '2026-05-16 14:25:09'),
(256, 'Maxime', 'Michon', '06 80 10 95 85', 'lm.michon@outlook.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-18 11:50:58', '2026-05-31 14:34:00'),
(257, 'florence', 'herve', '06 60 14 10 94', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-19 07:39:41', '2026-05-19 07:39:41'),
(258, 'Nathalie', 'Girard', '06 84 43 52 00', 'girardnat39@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-19 08:35:13', '2026-05-19 08:35:13'),
(259, 'christophe', 'guilbaud', '07 60 68 30 93', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-19 13:26:57', '2026-05-19 13:26:57'),
(260, 'Claire', 'Rio', '06 77 87 01 25', 'claire.leguennec@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-19 15:23:24', '2026-05-19 15:23:24'),
(261, 'Françoise', 'Richard', '06 80 02 84 92', 'richard.francoise44@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-19 22:23:24', '2026-05-24 09:12:46'),
(262, 'Jane', 'Jelley', '0044 77 14 09 12 90', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-20 07:11:19', '2026-05-20 07:11:19'),
(263, 'sylvain', 'artigaud', '06 75 01 60 95', 'sylvain@artigaud.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-20 09:48:20', '2026-05-20 09:48:20'),
(264, 'Catherine', 'Le Herissé', '06 08 42 15 04', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-20 13:12:48', '2026-05-20 13:12:48'),
(265, 'Amélie', 'Fouqueray', '06 59 38 18 39', 'amelie.fouqueray@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-20 18:48:06', '2026-05-20 18:48:06'),
(266, 'yves', 'Lefranc', '06 59 78 34 77', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-21 07:01:53', '2026-05-21 07:01:53'),
(267, 'anna', 'gullman', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-21 08:21:31', '2026-05-21 08:21:31'),
(268, 'violetta', 'Norden', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-21 12:14:00', '2026-05-21 12:16:02'),
(269, 'Corine', 'Rayé', '06 76 08 23 80', 'isar89@wanadoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-21 12:40:33', '2026-05-21 12:40:33'),
(270, 'Francis', 'Blandiot', '06 69 51 88 26', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-21 13:42:36', '2026-05-21 13:42:36'),
(271, 'Laure', 'Antoni', '06 86 89 57 62', 'laureantoni@hotmail.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-21 16:37:37', '2026-05-21 16:37:37'),
(272, 'Guilain', 'tournemine', '07 49 88 57 42', 'guilain.tournemine@outlook.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-22 09:28:39', '2026-05-22 09:28:39'),
(273, 'François', 'Lefebvre', '0660139909', 'copinar@hotmail.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-22 09:55:17', '2026-05-22 10:09:55'),
(274, 'air', 'bike', '0', 'arnaud@air-bikes.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-22 10:09:09', '2026-05-22 10:09:09'),
(275, 'xavier', 'olagne', '06 11 47 24 42', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-22 15:26:47', '2026-05-22 15:26:47'),
(276, 'jacques', 'chabanne', '06 22 47 23 63', '321speedmasteur@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-22 16:23:36', '2026-05-23 15:30:32'),
(277, 'Sandra', 'Sievers', '0', 'nc-calchesa@netcologne.de', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-22 18:01:23', '2026-05-22 18:01:23'),
(278, 'ollivier', 'charron', '07 49 87 29 35', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-23 09:29:40', '2026-05-23 09:29:40'),
(279, 'steven', 'lominé', '06 88 03 83 50', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-23 09:59:36', '2026-05-23 09:59:36'),
(280, 'Anne', 'De la rue', '06 86 91 92 95', 'anne_delarue@yahoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-23 10:01:02', '2026-05-27 10:02:04'),
(281, 'sandrine', 'barreto', '07 83 66 21 93', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-23 15:31:57', '2026-05-23 15:31:57'),
(282, 'celine', 'durand', '06 71 79 73 93', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-23 16:56:11', '2026-05-23 16:56:11'),
(283, 'Claudine', 'Berenger', '07 67 66 55 60', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-25 13:52:14', '2026-05-25 13:52:14'),
(284, 'Karin', 'Van Koeverden', '0049 152 561 787 49', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-26 08:05:31', '2026-05-26 08:05:31'),
(285, 'Delphine', 'Logiou', '07 55 64 64 68', 'delphine.logiou@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-26 08:38:50', '2026-05-26 08:38:50'),
(286, 'juliette', 'liso', '06 68 69 95 24', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-26 08:55:14', '2026-05-26 08:55:14'),
(287, 'john ola', 'norum', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-26 12:41:31', '2026-05-26 12:41:31'),
(288, 'veronique', 'jaubert', '06 62 32 90 64', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-26 12:58:21', '2026-05-26 12:58:21'),
(289, 'Olivier', 'Heitz', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-26 12:59:44', '2026-05-26 12:59:44'),
(290, 'David', 'Contant', '004 07 36 64 92 80', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-26 13:03:47', '2026-05-26 13:03:47'),
(291, 'Virginie', 'Brunaud', '06 99 88 23 16', 'v.brunaud@laposte.net', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-26 14:31:05', '2026-05-26 14:31:05'),
(292, 'Nathalie', 'Kergal', '06 79 66 21 59', 'nathalie.kergal@hotmail.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-26 15:11:25', '2026-05-28 14:26:14'),
(293, 'claude', 'plouha', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-26 16:42:39', '2026-05-26 16:42:39'),
(294, 'bernard', 'audoyer', '06 51 61 23 35', 'mccbau@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-27 08:07:32', '2026-05-27 08:07:32'),
(295, 'Annie', 'Tilly', '06 99 24 08 33', 'tilly.annie@yahoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-27 12:41:46', '2026-05-27 12:41:46'),
(296, 'dominique', 'ecarnot', '06 52 14 78 44', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-27 13:29:29', '2026-05-27 13:29:29'),
(297, 'Anne', 'Gobyn', '06 15 74 13 73', 'annegobyn@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-27 15:26:37', '2026-05-27 15:26:37'),
(298, 'sophie', 'moser', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-28 08:45:10', '2026-05-28 08:45:10'),
(299, 'mme', 'Sanchez', '06 80 57 30 68', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-28 09:04:52', '2026-05-28 09:04:52'),
(300, 'Remy', 'Delafosse', '06 17 15 27 33', 'remydelafosse@gmail.com', '9 place de la république Paimpol', NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-28 09:29:24', '2026-06-10 14:49:23'),
(301, 'Frédérica', 'Gervasoni', '+3291906651', 'fede.gerva@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-28 09:47:48', '2026-05-28 09:47:48'),
(302, 'Lionel', 'DECLOUX', '0624293233', 'lionel.decloux@sfr.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-28 09:54:02', '2026-05-28 15:23:34'),
(303, 'Philippe', 'PLOTIN', '0608483753', 'philippe.plotin@wanadoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-28 09:59:57', '2026-05-28 09:59:57'),
(304, 'kevin', 'sohn', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-28 14:25:43', '2026-05-28 14:25:43'),
(305, 'Hélène', 'DE BLANDER', '+32497405392', 'hdeblander@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-28 19:04:21', '2026-05-28 19:04:21'),
(306, 'Sylvain', 'Ropars', '06 87 09 32 82', 'sylvain.ropars67360@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-29 08:15:24', '2026-05-29 08:15:24'),
(307, 'Regis', 'Le Cam', '06 52 17 79 36', 'lecamrm@live.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-29 12:10:43', '2026-05-30 09:37:51'),
(308, 'Ilaria', 'Scalise', '+39 333 5633526', 'andrea.angelini@wonderteam.net', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-29 12:43:03', '2026-05-29 12:43:03'),
(309, 'Simon', 'Toullet', '06 76 93 12 53', 'Simontoullet91@hotmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-29 14:21:24', '2026-05-29 14:21:24'),
(310, 'Christian', 'Favry', '06 20 26 34 59', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-29 14:47:45', '2026-05-29 14:47:45'),
(311, 'Batien', 'Le Bail', '06 30 71 58 95', 'bastien.lebail@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-29 15:06:52', '2026-05-29 15:06:52'),
(312, 'André', 'Blanquer', '06 73 34 77 73', 'ablach@laposte.net', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-29 15:27:29', '2026-05-29 15:27:29'),
(313, 'Mme', 'Vigouroux', '06 20 38 71 59', 'martine.bonaldi-vigouroux@wanadoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-30 06:22:46', '2026-06-17 13:09:56'),
(314, 'helene', 'nyzam', '06 14 72 47 06', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-30 08:01:41', '2026-05-30 08:01:41'),
(315, 'Emmanuel', 'MEGIER', '06 30 11 03 74', 'emmanuel.megier@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-30 12:04:26', '2026-05-30 12:04:26'),
(316, 'xavier', 'morellec', '06 81 54 05 26', 'morellecxavier@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-05-30 14:53:48', '2026-05-30 14:53:48'),
(317, 'Elouan', 'Kermanach', '06 77 90 71 20', 'kermanachelouan@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-02 14:09:11', '2026-06-02 14:09:11'),
(318, 'Melina', 'Pasquini', '06 63 09 95 53', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-02 14:15:55', '2026-06-02 14:15:55'),
(319, 'Cora', 'Van Muiswinkel', '+31 6 39750102', 'coravanmuiswinkel@gmail.com', '3 rue de la Cité Saint-Denis à Ploubazlanec', NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-02 14:20:17', '2026-06-28 16:47:19'),
(320, 'Agnès', 'Lenz-Gebhart', '+43 650 9872111', 'agnes.lenz@chello.at', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-02 14:26:00', '2026-06-02 14:26:00');
INSERT INTO `clients` (`id`, `prenom`, `nom`, `telephone`, `email`, `adresse`, `origine_contact`, `commentaires`, `avantage_type`, `avantage_valeur`, `avantage_expiration`, `avantage_applique`, `avantage_applique_le`, `created_at`, `updated_at`) VALUES
(321, 'jeremy', 'adam', '06 20 97 92 01', 'adam.jeremy@laposte.net', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-03 09:21:12', '2026-06-03 09:21:12'),
(322, 'nathalie', 'pellerain', '06 81 00 21 96', 'pellerain.nathalie@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-03 09:58:54', '2026-06-03 09:58:54'),
(323, 'Marc', 'Braibant', '06 07 33 67 15', 'mbraibant@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-03 13:24:20', '2026-06-03 13:24:20'),
(324, 'gwenael', 'henry', '06 81 59 91 61', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-03 15:24:10', '2026-06-03 15:24:10'),
(325, 'Camille', 'Prioul', '(0)6 72 09 26 75', 'cprioul@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-03 15:47:50', '2026-06-03 15:47:50'),
(326, 'Philippe', 'Revol', '06 23 25 60 64', 'phrevol71@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-03 16:33:16', '2026-06-03 16:33:16'),
(327, 'Mr', 'Riso', '06 64 17 90 77', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-04 07:41:47', '2026-06-04 07:41:47'),
(328, 'Jonathan', 'Cauret', '07 63 92 54 12', 'jo.cauret.22500@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-04 08:52:30', '2026-06-04 08:52:30'),
(329, 'patrick', 'de poret', '06 19 21 51 88', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-04 08:56:28', '2026-06-04 08:56:28'),
(330, 'Serge', 'Pierre', '06 81 09 48 34', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-04 12:38:25', '2026-06-04 12:38:25'),
(331, 'Cecile', 'Ogier', '06 70 27 05 99', 'cecile.ogier2202@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-04 16:08:39', '2026-06-04 16:08:39'),
(332, 'Charles', 'Froger', '06 37 90 76 53', 'charly_frog@hotmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-05 13:48:29', '2026-06-05 13:48:29'),
(333, 'arne', 'jonsson', '078 46 100 91', 'arne_b_jonsson@hotmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-05 14:37:56', '2026-06-05 14:37:56'),
(334, 'joan', 'burke', '06 48 92 31 06', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-05 15:01:49', '2026-06-05 15:01:49'),
(335, 'carole', 'touarin', '06 80 25 20 50', 'carole.touarin@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-06 08:39:20', '2026-06-06 08:39:20'),
(336, 'judith', 'smadja', '06 89 22 84 03', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-06 15:33:10', '2026-06-06 15:33:10'),
(337, 'Ian', 'John', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-06 15:39:02', '2026-06-06 15:39:02'),
(338, 'Aurélio', 'Restellini', '+41 79 817 37 22', 'aurelio.restellini@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-08 18:45:59', '2026-06-08 18:45:59'),
(339, 'Michel', 'ropers', '06 07 67 07 98', 'michelropers1@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-09 07:12:28', '2026-06-09 07:12:28'),
(340, 'mireille', 'poitevin', '06 98 40 16 13', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-09 08:17:11', '2026-06-09 08:17:11'),
(341, 'Nicole', 'Javault', '06 27 43 46 58', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-09 08:29:11', '2026-06-09 08:29:11'),
(342, 'georges', 'combet', '06 11 56 76 44', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-09 09:02:13', '2026-06-09 09:02:13'),
(343, 'Daniel', 'Mohr', '0049 174 88 46 978', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-09 12:19:22', '2026-06-09 12:19:22'),
(344, 'Clément', 'Emine', '0677623765', 'clement.emine@hotmail.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-09 12:29:41', '2026-06-09 12:29:41'),
(345, 'Fariba', 'Sadri', '0049 151 407 26162', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-09 12:37:28', '2026-06-09 12:37:28'),
(346, 'Hervé', 'Le Blais', '06 25 79 62 02', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-09 12:45:12', '2026-06-09 12:45:12'),
(347, 'Roger', 'Grot', '06 99 11 87 03', 'roger.grot22@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-09 14:50:04', '2026-06-12 07:40:21'),
(348, 'Alain', 'Peurian', '02 96 16 46 18', 'alainpeurian@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-09 14:51:14', '2026-06-09 14:51:14'),
(349, 'philippe', 'soubre', '06 07 53 71 28', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-09 15:06:59', '2026-06-09 15:06:59'),
(350, 'roger', 'grot', '06 99 11 87 03', NULL, NULL, NULL, 'pret atelier', 'aucun', 0.00, NULL, 0, NULL, '2026-06-09 15:12:09', '2026-06-09 15:12:09'),
(351, 'François - Christophe', 'Crozat', '06 65 55 35 26', 'franzcrozat@yahoo.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-09 15:15:32', '2026-06-15 08:50:05'),
(352, 'quentin', 'quemener', '07 72 21 63 78', 'quentin2.005@cloud.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-09 15:24:27', '2026-06-09 15:24:27'),
(353, 'Françoise', 'Margo', '06 07 03 32 65', 'frmargo1950@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-10 08:50:00', '2026-06-10 08:50:00'),
(354, 'patrick', 'erhel', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-10 12:53:07', '2026-06-10 12:53:07'),
(355, 'Caroline', 'Charnock', '+44 79 80 499 043', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-10 13:30:27', '2026-06-10 13:30:27'),
(356, 'gilles', 'boulon lefevre', '06 11 28 98 84', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-10 15:31:31', '2026-06-10 15:31:31'),
(357, 'thomas', 'corlouer', '06 84 99 74 43', 'thomas.corlouer@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-11 08:57:19', '2026-06-11 08:57:19'),
(358, 'Loumie', 'brehon', '0662135114', 'loumie06@proton.me', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-11 09:06:26', '2026-06-27 13:19:45'),
(359, 'Marc', 'Le Cavorzin', '06 49 98 15 42', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-11 12:39:41', '2026-06-11 12:39:41'),
(360, 'Armelle', 'Le Muzic', '06 79 68 48 14', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-11 12:45:37', '2026-06-11 12:45:37'),
(361, 'denis', 'attard', '06 30 36 72 92', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-11 14:59:05', '2026-06-11 14:59:05'),
(362, 'Jean-Baptiste', 'SAUREL', '07 60 09 49 82', 'jbsaurel@hotmail.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-12 06:49:35', '2026-06-12 06:49:35'),
(363, 'Sonia et Laurent', 'Swarowsky-Courcier', '06581391290651707447', 'sonia.swarowsky@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-12 08:59:24', '2026-06-12 08:59:24'),
(364, 'eddy', 'turbé', '07 65 89 22 77', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-13 12:50:19', '2026-06-13 12:50:19'),
(365, 'stephane', 'bouvier', '06 62 58 14 73', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-13 13:23:39', '2026-06-13 13:23:39'),
(366, 'sylvie', 'jego', '06 07 11 32 96', 'stephane.jego@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-13 14:03:36', '2026-06-13 14:03:36'),
(367, 'a definir', 'a definir', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-13 14:31:30', '2026-06-13 14:31:30'),
(368, 'nathalie', 'terme', '06 46 89 67 94', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-13 15:17:37', '2026-06-13 15:17:37'),
(369, 'Christophe', 'De Calbiac', '06 59 86 14 93', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-15 14:07:12', '2026-06-15 14:07:12'),
(370, 'regine', 'ollivier', '06 82 39 71 69', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-16 09:06:15', '2026-06-16 09:06:15'),
(371, 'Agnès', 'Fonlladosa', '0658974688', 'agnesfonlladosa@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-16 11:47:10', '2026-06-16 11:47:10'),
(372, 'erwan', 'heuze', '06 62 56 57 91', 'contact@lechantdelankou.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-16 12:05:42', '2026-06-16 12:05:42'),
(373, 'maxime', 'antar', '06 01 74 69 66', 'mxm.ntr@laposte.net', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-16 12:07:44', '2026-06-18 16:06:22'),
(374, 'Mathilde', 'Crone', '0031 653 22 70 26', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-16 12:45:19', '2026-06-16 12:45:19'),
(375, 'jan', 'welker', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-16 12:59:52', '2026-06-16 12:59:52'),
(376, 'François', 'Huchet du Guermeur', '06 37 99 53 02', 'francois.huchet22@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-16 14:38:56', '2026-06-16 14:38:56'),
(377, 'cyril', 'robert', '06 79 98 73 80', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-17 07:29:46', '2026-06-17 07:29:46'),
(378, 'Daniel', 'Lefevre', '06 95 06 11 20', 'dany.lefevre621@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-17 09:26:22', '2026-06-17 09:26:22'),
(379, 'Bruno', 'Le Dû', '07 82 53 77 42', 'brunoledu@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-17 12:07:36', '2026-06-17 12:07:36'),
(380, 'alison', 'poppea', '+44 777 0 544 619', NULL, NULL, NULL, '2 potences hautes', 'aucun', 0.00, NULL, 0, NULL, '2026-06-17 12:57:15', '2026-06-17 14:00:41'),
(381, 'jean luc', 'glory', '06 38 19 39 84', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-17 13:32:54', '2026-06-17 13:32:54'),
(382, 'jean marie', 'queste', '06 27 04 05 48', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-17 13:34:49', '2026-06-17 13:34:49'),
(383, 'Judith', 'Larnaud', '0687477644', 'judithlilith@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-17 14:03:32', '2026-06-17 14:03:32'),
(384, 'Marine', 'Ostapowicz', '06 77 47 51 59', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-17 14:40:50', '2026-06-17 14:40:50'),
(385, 'François', 'MENGIN LECREULX', '06 78 58 40 52', 'francois.menginlecreulx@ars.sante.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-17 21:43:24', '2026-06-17 21:43:24'),
(386, 'beatrice', 'Le breton', '06 60 55 04 13', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-18 10:03:14', '2026-06-18 10:03:14'),
(387, 'Michel', 'Czobyez', '06 78 91 59 35', 'miccz@free.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-18 12:24:41', '2026-06-18 12:24:41'),
(388, 'Philippe', 'Laporte', '07 71 61 78 77', 'laportephilipe@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-18 12:50:26', '2026-06-18 12:50:26'),
(389, 'Denis', 'Bernadet', '06 07 41 96 86', 'denis.bernadet@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-18 12:53:41', '2026-06-18 12:53:41'),
(390, 'Theo', 'Poupin', '07 87 00 43 60', 'poupin.theo.2007@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-18 14:50:07', '2026-06-18 14:50:07'),
(391, 'Emilie', 'Jost', '+35 3 86 107 1606', 'Emiliejost@yahoo.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-19 07:53:50', '2026-06-19 07:53:50'),
(392, 'brigitte', 'nicolas', '07 81 60 30 55', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-19 08:35:27', '2026-06-19 08:35:27'),
(393, 'hotel', 'les Agapanthes', '02 96 55 89 06', 'contact@hotel-les-agapanthes.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-19 09:29:37', '2026-06-25 13:00:37'),
(394, 'john', 'commacais', '07 71 18 02 26', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-19 12:15:44', '2026-06-19 12:15:44'),
(395, 'a definir', 'commencais', '07 71 18 02 26', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-19 12:17:21', '2026-06-19 12:17:21'),
(396, 'Isabelle', 'Deboos', '06 10 65 67 92', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-19 13:35:57', '2026-06-19 13:35:57'),
(397, 'Pascale', 'Ragot', '06 83 94 60 55', 'sergeragot22@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-19 15:28:15', '2026-06-19 15:28:15'),
(398, 'pauline', 'kalaschnikow', '06 16 81 82 03', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-19 16:15:23', '2026-06-20 10:05:08'),
(399, 'Victoire', 'Le Bellec', '0', NULL, NULL, NULL, 'pret de velo (atelier)', 'aucun', 0.00, NULL, 0, NULL, '2026-06-20 07:32:44', '2026-06-23 06:48:33'),
(400, 'florian', 'carry', '06 79 42 75 70', 'florian.carry@safrangroup.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-20 09:46:47', '2026-06-25 15:46:59'),
(401, 'raphael', 'breitburd', '06 75 88 65 42', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-20 12:30:35', '2026-06-20 12:30:35'),
(402, 'laura', 'gautier', '06 37 29 38 76', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-20 13:29:41', '2026-06-20 13:29:41'),
(403, 'kevin', 'brochen', '07 87 02 02 14', 'kevin.brochen@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-20 14:05:55', '2026-06-20 14:05:55'),
(404, 'isabelle', 'jardin', '06 41 22 14 75', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-20 14:47:40', '2026-06-20 14:47:40'),
(405, 'Olivier', 'Hermann', '06 09 78 52 69', 'olivier.hermann@free.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-20 15:28:48', '2026-06-20 15:28:48'),
(406, 'Magali', 'Wiedemann', '06.87.54.55.12', 'magwie27@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-20 16:43:29', '2026-06-20 16:43:29'),
(407, 'Alice', 'Drappier', '06 81 60 86 66', 'alice66@me.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-22 05:47:02', '2026-06-22 05:47:02'),
(408, 'Christine', 'Deltzung', '06 79 63 69 00', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-22 10:01:25', '2026-06-22 10:01:25'),
(409, 'Cécile', 'Diebolt', '0', 'tomblop@hotmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-22 16:20:27', '2026-06-22 16:20:27'),
(410, 'Asma', 'Miloua', '07 84 80 51 48', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-22 16:33:45', '2026-06-22 16:33:45'),
(411, 'maryse', 'arribehaute', '07 86 26 21 97', 'marysearribehaute@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-23 13:30:24', '2026-06-23 13:30:24'),
(412, 'Laura', 'Pacheco', '06 65 67 53 07', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-23 13:45:43', '2026-06-23 13:45:43'),
(413, 'iliane', 'jolibois', '07 69 00 10 86', 'ilianejolibois@outlook.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-23 14:59:54', '2026-06-23 14:59:54'),
(414, 'de passage', 'anglais', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-23 15:43:47', '2026-06-23 15:43:47'),
(415, 'Marie Pierre', 'Lebeuf', '06 22 73 84 52', 'marie.corrin@sfr.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-24 06:44:05', '2026-06-24 06:44:05'),
(416, 'julie', 'orieux', '06 08 17 83 99', 'zu.24@hotmail.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-24 07:08:05', '2026-06-24 07:08:05'),
(417, 'corinne', 'strutz', '06 10 57 66 09', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-24 08:15:25', '2026-06-24 08:15:25'),
(418, 'Joëlle', 'Hang Do', '06 31 61 68 42', 'joellehang.do@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-24 08:25:49', '2026-06-24 08:25:49'),
(419, 'gerard', 'tournier', '06 77 48 77 98', 'gtournier22@gmail.com', NULL, NULL, 'pret atelier', 'aucun', 0.00, NULL, 0, NULL, '2026-06-24 09:21:48', '2026-06-24 09:46:41'),
(420, 'pierre alexandre', 'laquitaine', '06 85 78 46 41', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-24 13:51:24', '2026-06-24 13:51:24'),
(421, 'Vincent', 'Méligne', '+1 718 715 3449', 'vincent.meligne@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-24 18:00:58', '2026-06-24 18:00:58'),
(422, 'armelle', 'benois', '06 88 69 27 23', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-25 07:46:14', '2026-06-25 07:46:14'),
(423, 'denis', 'nezan', '06 26 16 54 12', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-25 07:58:36', '2026-06-25 07:58:36'),
(424, 'Arnaud', 'Rossetti', '06 13 80 36 33', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-25 08:31:05', '2026-06-25 08:31:05'),
(425, 'fanny', 'cestier', '06 65 27 21 35', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-25 08:45:47', '2026-06-25 08:45:47'),
(426, 'Marie', 'De Fels', '06 32 60 48 12', 'marie@mariedefels.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-25 12:55:21', '2026-06-25 12:55:21'),
(427, 'vanessa', 'boullet', '06 16 52 59 40', 'vanessa.boullet71@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-25 13:15:13', '2026-06-25 13:15:13'),
(428, 'Sarah', 'Bertrand', '06 72 34 35 51', 'sarahbertrand00@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-25 14:14:52', '2026-06-25 14:14:52'),
(429, 'mathias', 'zahno', '0', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-26 08:19:44', '2026-06-26 08:19:44'),
(430, 'Benoit', 'Champomier', '06 75 81 57 35', 'benoit.champomier@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-26 12:22:47', '2026-06-26 12:22:47'),
(431, 'Fabienne', 'Gautschi', '06 51 06 35 67', 'fabiennegautschi@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-26 12:57:52', '2026-06-26 12:57:52'),
(432, 'Alicia', 'André', '06 30 33 56 75', 'alicia.andeker@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-26 15:42:19', '2026-06-26 15:42:19'),
(433, 'jean-pierre', 'heurtel', '06 18 45 80 05', 'jpheurtel@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-27 08:41:38', '2026-06-27 08:41:38'),
(434, 'caroline', 'le bozec', '06 40 12 36 90', 'dominique_lebozec@orange.fr', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-27 09:25:32', '2026-06-27 09:25:32'),
(435, 'loic', 'chaudoit', '06 37 63 75 36', 'loic.chaudoit@gmail.com', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-27 13:50:49', '2026-06-27 13:50:49'),
(436, 'mathieu', 'rousseau', '06 49 70 26 75', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-27 15:01:01', '2026-06-27 15:01:01'),
(437, 'anne catherine', 'bregeon', '06 87 19 09 21', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-27 16:33:39', '2026-06-27 16:33:39'),
(438, 'fanny', 'demangeat', '06 41 73 96 76', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-29 12:53:51', '2026-06-29 12:53:51'),
(439, 'a', 'la con', '00', NULL, NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-29 14:22:33', '2026-06-29 14:22:33'),
(440, 'Jérôme', 'Lefebvre', '+32 496 524 624', 'family@gennaker.be', NULL, NULL, NULL, 'aucun', 0.00, NULL, 0, NULL, '2026-06-29 14:49:54', '2026-06-29 14:49:54');

-- --------------------------------------------------------

--
-- Structure de la table `demandes_partenaire`
--

CREATE TABLE `demandes_partenaire` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `partenaire_id` bigint(20) UNSIGNED NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `creneau` varchar(255) NOT NULL DEFAULT 'journee',
  `velos_demandes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`velos_demandes`)),
  `commentaire` text DEFAULT NULL,
  `statut` varchar(20) NOT NULL DEFAULT 'en_attente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(191) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `media`
--

CREATE TABLE `media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(150) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `collection_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `disk` varchar(255) NOT NULL,
  `conversions_disk` varchar(255) DEFAULT NULL,
  `size` bigint(20) UNSIGNED NOT NULL,
  `manipulations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`manipulations`)),
  `custom_properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`custom_properties`)),
  `generated_conversions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`generated_conversions`)),
  `responsive_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`responsive_images`)),
  `order_column` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `media`
--

INSERT INTO `media` (`id`, `model_type`, `model_id`, `uuid`, `collection_name`, `name`, `file_name`, `mime_type`, `disk`, `conversions_disk`, `size`, `manipulations`, `custom_properties`, `generated_conversions`, `responsive_images`, `order_column`, `created_at`, `updated_at`) VALUES
(2, 'App\\Models\\Message', 1, '6a23a850-932b-4394-8245-94ed2df7d7e8', 'photos', '1000013969', '1000013969.png', 'image/png', 'public', 'public', 948311, '[]', '[]', '[]', '[]', 1, '2026-03-08 18:33:36', '2026-03-08 18:33:36'),
(3, 'App\\Models\\Message', 1, '7a03b22d-ee4c-4750-956b-19eeb3d26251', 'photos', '1000014059', '1000014059.jpg', 'image/jpeg', 'public', 'public', 66438, '[]', '[]', '[]', '[]', 2, '2026-03-08 20:45:42', '2026-03-08 20:45:42');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `author_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `recipient_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `status` enum('ouvert','resolu') NOT NULL DEFAULT 'ouvert',
  `read_at` timestamp NULL DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id`, `author_user_id`, `recipient_user_id`, `category_id`, `contact_name`, `contact_phone`, `contact_email`, `content`, `status`, `read_at`, `resolved_at`, `last_activity_at`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 4, NULL, NULL, NULL, 'Aziz LUMIERE', 'ouvert', NULL, NULL, '2026-03-08 18:32:04', '2026-03-08 18:32:04', '2026-03-08 18:32:04'),
(2, NULL, NULL, 2, 'Cedric Vidal', '06 76 06 23 69', 'cedricovidalo@gmail.com', 'faire le devis du Raleigh avec la roue AR hs', 'ouvert', NULL, NULL, '2026-03-09 10:07:40', '2026-03-09 10:07:14', '2026-03-09 10:07:14'),
(4, NULL, NULL, 4, 'Moreau Erika', '06 23 36 47 35', 'erikamoreau@club-internet.fr', 'réservation location pour lundi 1er septembre au samedi soir le 5 septembre\n4 personnes : 1,75 - 1,70, 1,65 - 1,58 VAE\nlivraison Kergiquel', 'ouvert', NULL, NULL, '2026-03-09 16:35:51', '2026-03-09 16:35:51', '2026-03-09 16:35:51'),
(5, 1, NULL, 3, 'erika moreau', '06 23 36 47 35', 'erikamoreau@club-internet.fr', 'location du 1 au 5 septembre de 4 VAE\nlivraison au gîte kergiquel \ntaille a reconfirmer par mail ou sms.', 'resolu', NULL, '2026-03-30 08:57:43', '2026-03-11 11:05:52', '2026-03-09 16:41:59', '2026-03-30 08:57:43'),
(6, 1, NULL, 2, 'Cedric Vidal', '06 76 06 23 69', 'cedricovidalo@gmail.com', 'Raleigh : devis en attente de validation', 'resolu', NULL, '2026-03-11 11:01:03', '2026-03-09 16:45:40', '2026-03-09 16:45:40', '2026-03-11 11:01:03'),
(7, 1, NULL, 4, 'Fabien Delusion', NULL, 'fdelusion@gmail.com', 'en attente de son numéro de téléphone pour ajouter à la réservation du 23 aout.', 'resolu', NULL, '2026-03-11 11:00:58', '2026-03-11 09:00:01', '2026-03-09 17:02:02', '2026-03-11 11:00:58'),
(8, 1, NULL, 3, 'Corinne Coquelle', '06 14 08 78 60', 'thecocotouch@yahoo.fr', 'réservation de 322 euros du 7 juillet au 12 juillet avec 2 VAE.\nun acompte à été ou doit être demandé de 96 euros.\n\nRappeler la personne pour vérifier l\'état de la réservation', 'resolu', NULL, '2026-03-11 11:04:55', '2026-03-09 17:24:09', '2026-03-09 17:24:09', '2026-03-11 11:04:55'),
(10, 1, NULL, 5, 'Pascale Grot', '07 68 69 53 23', NULL, 'Partenaire hébergement.', 'ouvert', NULL, NULL, '2026-03-10 13:36:21', '2026-03-10 13:36:21', '2026-04-12 19:37:41'),
(11, 1, NULL, 4, NULL, NULL, NULL, 'cofidis info', 'ouvert', NULL, NULL, '2026-03-11 13:28:33', '2026-03-11 09:05:52', '2026-03-11 09:05:52'),
(12, 1, NULL, 6, 'Witz Lynda', '06 41 61 85 38', NULL, 'Recherche une location \"longue durée\" 70/80 budget par mois', 'ouvert', NULL, NULL, '2026-03-11 15:37:14', '2026-03-11 15:36:42', '2026-04-12 19:37:16'),
(13, 1, NULL, 3, 'Marie Paule Dahan', '06 82 37 87 82', 'marie-paule.dahan@wanadoo.fr', '1 vae 3 jours 44euros + 93euros\n1 vtc 3jours\nprix partenaire\nhôtel des agapanthes livraison 2 juillet au matin\nrécupération le lundi matin avec batterie chargée', 'resolu', NULL, '2026-03-18 09:52:34', '2026-03-18 09:52:32', '2026-03-11 16:15:50', '2026-03-18 09:52:34'),
(14, 1, NULL, 3, 'Agnes Signoret', NULL, 'Agnes.SIGNORET@banque-france.fr', 'demande d\'info pour une location du 4 au 11 juillet\nen VAE.\nil faut le nombre de vélo et la taille pour verifier la disponibilité puis les arrhes afin de valider la reservation.', 'resolu', NULL, '2026-03-30 08:57:56', '2026-03-11 19:58:52', '2026-03-11 19:58:52', '2026-03-30 08:57:56'),
(15, 2, 1, 4, 'delanoe bocher', '07 69 35 52 57', NULL, '12/03/26 Mr Bocher a laissé un message je lui ai sms que tu le rappellerai au plus vite après ta \"réunion\"\nrappeler Mr Bocher au sujet du plateau vélo moustache', 'resolu', '2026-03-12 09:38:27', '2026-03-12 10:44:18', '2026-03-12 09:44:58', '2026-03-12 08:12:35', '2026-03-12 10:44:18'),
(16, 2, NULL, 4, 'RIO', NULL, NULL, '18P3AIOULS5Q', 'resolu', NULL, '2026-03-12 10:44:50', '2026-03-12 09:48:19', '2026-03-12 09:48:19', '2026-03-12 10:44:50'),
(17, 1, NULL, 4, 'Le Mée Philippe', '06 08 03 20 79', NULL, 'Cannondale Mottera CRB 2023, définir un prix de vente, definir marge', 'ouvert', NULL, NULL, '2026-03-12 16:43:11', '2026-03-12 16:43:11', '2026-03-12 16:43:11'),
(18, 1, NULL, 3, 'Christophe De Calbiac', '06 59 86 14 93', NULL, '22 juin matin\n7h45 rdv pour confier les vélos\nà confirmer\npeut etre 5 VAE', 'ouvert', NULL, NULL, '2026-03-13 10:07:33', '2026-03-13 10:07:33', '2026-03-13 10:07:33'),
(19, 1, NULL, 3, 'Serge Le Boucher', '06 08 80 19 08', NULL, 'La Classic Channel Regattaµ\nOn envisage une flotte de vélo a mettre en service le 14 juillet, à récupérer le 16 au soir\nil va y avoir un sondage pour évaluer le nombre de vélo à louer', 'ouvert', NULL, NULL, '2026-03-13 15:28:14', '2026-03-13 15:28:14', '2026-03-13 15:28:14'),
(22, 2, 1, 2, 'Balcou Melanie', NULL, NULL, 'elle a déposé sa batterie , si possible la récupérer mercredi si il fait beau', 'resolu', '2026-03-14 11:10:17', '2026-03-20 17:20:21', '2026-03-14 11:15:05', '2026-03-14 11:09:08', '2026-03-20 17:20:21'),
(21, 2, 1, 2, 'erwan rabé', '06 98 01 06 10', NULL, 'se renseigner pour des chambre a air , huile de chaine a vendre ? kit de survie (sacoche) en forme de gourde?', 'resolu', '2026-03-14 11:11:34', '2026-03-31 07:33:51', '2026-03-14 11:13:31', '2026-03-14 09:54:12', '2026-03-31 07:33:51'),
(23, 2, 1, 3, 'Moreau Erika', '06 23 36 47 35', 'erikamoreau@club-internet.fr', 'Pensez au devis de location a envoyer à cette dame. vu par mail ou sms', 'resolu', '2026-03-14 12:51:43', '2026-03-31 07:33:44', '2026-03-14 12:52:03', '2026-03-14 11:10:27', '2026-03-31 07:33:44'),
(24, 1, NULL, 4, 'Weber', '06 70 45 72 08', 'weber.esther@orange.fr', 'interet pour un VAE type milano : moteur roue ok, voir tailel L si possible en test', 'ouvert', NULL, NULL, '2026-03-26 09:23:51', '2026-03-17 10:34:18', '2026-03-17 10:34:18'),
(25, 1, NULL, 4, 'Paul Zissala', '06 26 87 40 19', 'zissalapaul@gmail.com', 'faire un devis pour l\'achat d\'un VAE valable pour 3 mois\nhttps://leoncycle.fr/products/%E2%99%BB%EF%B8%8F-ncm-milano-max', 'resolu', NULL, '2026-03-21 09:05:14', '2026-03-18 13:26:35', '2026-03-18 13:26:35', '2026-03-21 09:05:14'),
(26, 1, NULL, 3, 'Nathalie Arnoux', '06 86 52 70 79', NULL, 'fin juillet\nlocation VTC', 'ouvert', NULL, NULL, '2026-03-18 14:07:51', '2026-03-18 14:07:51', '2026-03-18 14:07:51'),
(27, 1, NULL, 3, 'Beatrice Le Breton', '06 60 55 04 13', NULL, 'reservation vélo electrique samedi 20', 'ouvert', NULL, NULL, '2026-03-18 14:16:06', '2026-03-18 14:09:33', '2026-03-18 14:09:33'),
(28, 2, 1, 6, 'LIDA', '06 70 41 66 41', NULL, 'recherche vae femme occasion , a rappeler', 'ouvert', '2026-03-21 09:05:50', NULL, '2026-03-19 13:42:33', '2026-03-19 13:42:33', '2026-04-12 19:37:28'),
(29, 2, 1, 4, 'tassia la gazette du trieux', '06 52 06 57 73', NULL, 'rappeler pour un potentiel rdv', 'resolu', '2026-03-21 09:05:34', '2026-03-21 09:05:36', '2026-03-19 16:22:05', '2026-03-19 16:22:05', '2026-03-21 09:05:36'),
(30, 2, 1, 2, 'carnec lapierre course', '06 33 51 95 88', NULL, 'appeler le client lundi , sms de prevenance d\'horaire si possible', 'resolu', '2026-03-21 09:05:40', '2026-03-31 07:33:20', '2026-03-20 15:22:47', '2026-03-20 15:22:47', '2026-03-31 07:33:20'),
(31, 2, 1, 6, 'Mme Le Floch', '06 33 48 77 56', 'estellelefloch@yahoo.fr', 'recherche vélo enfant fille 1m46\n\nestimation du petit Btwin vert occasion?', 'ouvert', '2026-03-22 13:18:25', NULL, '2026-03-21 10:28:29', '2026-03-21 10:28:29', '2026-04-12 19:37:10'),
(32, 2, 1, 3, 'Avenel Charline', '06 70 39 61 43', 'avenel.charline@gmail.com', 'demande devis location par mail:\n2 VAE 1.70m et 1.90m\ndu 14 mai au 18 mai\nprise a l\'atelier le 14\n\noption 1 : recup le 18 mai a la gare de lannion pour 18h30\noption 2 : recup le 18 mai a perros guirrec + deposer les gens a la gare de lannion pour 18h30\n\nsi possible , proposer le tarif pour les deux options', 'resolu', '2026-04-08 06:58:10', '2026-04-08 06:58:30', '2026-03-21 15:11:41', '2026-03-21 15:11:41', '2026-04-08 06:58:30'),
(33, 1, 2, 2, 'ambre le bris', '0', NULL, 'est-ce que t\'as checké le pneu AV pour la crevaison du VTT enfant \'vert\' \'jaune\' fluo ?', 'resolu', '2026-03-24 09:00:11', '2026-03-25 08:54:44', '2026-03-24 09:00:37', '2026-03-24 07:23:08', '2026-03-25 08:54:44'),
(34, 1, NULL, 5, 'Gougeon Marie Annick', '06 16 36 33 50', NULL, 'Hebergement partenaire\nTy Boeme à ploubazlanec\n4 rue pierre n\'aland', 'ouvert', NULL, NULL, '2026-03-25 15:22:44', '2026-03-25 15:22:44', '2026-04-14 08:06:06'),
(35, 1, NULL, 6, 'Le moine Patrick', '06 75 36 06 56', NULL, 'recherche gravel occasion\nentre 1000 1500', 'ouvert', NULL, NULL, '2026-03-26 09:21:39', '2026-03-26 09:21:39', '2026-04-12 19:36:57'),
(36, 1, NULL, 5, 'Lefevre Jean Marc / Marie', '06 79 99 89 33', NULL, '32 rue de l\'eglise Paimpol\npartenaire hebergement', 'ouvert', NULL, NULL, '2026-03-26 10:51:59', '2026-03-26 10:51:59', '2026-04-12 19:36:43'),
(37, 1, NULL, 4, 'Delaunay Bernard', '06 80 22 78 16', NULL, 'recherche vélo électrique pliant disponible rapidement.', 'resolu', NULL, '2026-04-01 12:42:41', '2026-03-26 16:46:42', '2026-03-26 16:46:42', '2026-04-01 12:42:41'),
(38, 2, 1, 2, 'feuillatre fat vtt capteur pedalage hs', NULL, NULL, 'voir le prix du capteur , si vraiment trop cher , on tente le point de colle', 'resolu', '2026-03-28 12:17:15', '2026-04-11 10:55:12', '2026-03-28 12:18:32', '2026-03-27 13:08:37', '2026-04-11 10:55:12'),
(41, 2, 1, 2, 'erwan rabé 2 vélos', NULL, NULL, 'il a déposé son ancien pour check , et son nouveau pour finaliser la commande', 'resolu', '2026-04-11 10:55:00', '2026-04-13 16:46:29', '2026-03-28 13:31:41', '2026-03-28 13:31:41', '2026-04-13 16:46:29'),
(42, 2, 1, 6, 'gauthier Pierre', '06 72 32 71 80', NULL, 'intéressé par un VAE VTC femme 1.70 occasion\nrappeler pour un rdv lundi ou mardi', 'resolu', '2026-04-13 16:46:24', '2026-04-13 16:46:25', '2026-03-28 14:14:57', '2026-03-28 14:14:57', '2026-04-13 16:46:25'),
(43, 2, 1, 3, 'Brenot charlie', '06 64 08 32 24', 'ch&arlie.brenot@gmail.com', 'devis location :\n2 vae 1.90m 1.63m 2.5 jours\nlivraison samedi 25 avril 9h gare st brieuc\nrecup lundi 14h30 gare st brieuc\n\ninfo = livraison recup = 65€\n       location environ = 90€ / vélo  \n   tarifs a ajuster dans le mail , ceux la étaient à titre informatif par téléphone', 'resolu', '2026-04-14 08:02:06', '2026-04-15 12:34:07', '2026-03-28 14:20:15', '2026-03-28 14:20:15', '2026-04-15 12:34:07'),
(44, 2, 1, 2, 'daniel labbé', '06 75 26 04 01', NULL, 'le renseigner pour une chaine longue 9v pour velo cargo\necartement entre derailleur et plateaux 85cm', 'resolu', '2026-04-13 16:45:20', '2026-04-13 16:45:21', '2026-03-28 14:22:23', '2026-03-28 14:22:23', '2026-04-13 16:45:21'),
(45, 2, NULL, 4, 'gille le lard', NULL, NULL, 'voir avec le tracking pour le colis qui etait soi disant deja reparti', 'resolu', NULL, '2026-04-29 07:33:23', '2026-03-28 16:56:58', '2026-03-28 16:56:58', '2026-04-29 07:33:23'),
(47, 2, 1, 2, 'sonia le fevre Look course', NULL, NULL, 'ok pour avoir le prix des 2 commandes freins/vitesse\nconfirmation de guidoline noire', 'resolu', '2026-04-07 13:46:41', '2026-06-03 07:58:14', '2026-03-28 16:59:19', '2026-03-28 16:59:19', '2026-06-03 07:58:14'),
(48, 1, NULL, 2, 'Tanguy Sophie', '06 11 60 42 80', 'sophietanguy241207@gmail.com', 'faire un devis d\'équipement D4 riverside 500 posture ville, condition rando', 'ouvert', NULL, NULL, '2026-03-30 15:48:51', '2026-03-30 15:48:51', '2026-03-30 15:48:51'),
(49, 1, NULL, 4, 'Brochen Kevin', '07 87 02 02 14', 'kevin.brochen@gmail.com', 'Depot vente du Scoot XL.\nmise à prix 700', 'resolu', NULL, '2026-04-01 12:42:44', '2026-04-01 08:27:21', '2026-04-01 08:27:21', '2026-04-01 12:42:44'),
(50, 1, NULL, 4, 'Floury Martine', '06 63 13 38 30', 'martinfloury22@orange.fr', 'batterie qui ne charge peut etre plus, à voir, \nvoir le prix d\'une batterie, voir le prix du reconditionnement', 'ouvert', NULL, NULL, '2026-04-02 13:07:18', '2026-04-02 13:07:18', '2026-04-02 13:07:18'),
(51, 1, NULL, 4, 'Henoch', '07 67 30 00 53', 'henocq.alexis34@gmail.com', 'VTT leger, mono plateau,\n pas trop de saut, mais ca peut arriver\nselle telescopique\nautour de 1000 euros', 'resolu', NULL, '2026-04-07 13:16:27', '2026-04-02 13:29:23', '2026-04-02 13:29:23', '2026-04-07 13:16:27'),
(52, 2, 1, 5, 'Olivier Lallemant', '06 82 21 13 84', 'olallemant@yahoo.fr', 'gite partenaire ti plom \npour madame : c\'est Maude 06 20 03 21 70', 'ouvert', '2026-04-07 13:16:19', NULL, '2026-04-07 09:42:21', '2026-04-07 09:42:21', '2026-04-07 13:42:54'),
(53, 1, NULL, 6, 'Dupas Denis', '06 51 12 09 55', 'nonretour@orange.fr', 'Recherche de pompe à air valve presta (à visser)', 'ouvert', NULL, NULL, '2026-04-07 14:19:21', '2026-04-07 14:19:21', '2026-04-07 14:19:21'),
(54, 1, NULL, 6, 'Le Henaff Guy', '06 88 97 20 88', 'guy.le-henaff@club-internet.fr', 'facture pour le carlyna NGE', 'ouvert', NULL, NULL, '2026-04-07 16:44:06', '2026-04-07 16:42:31', '2026-04-07 16:44:06'),
(55, 1, NULL, 6, 'Bobin Marie', '06 86 90 52 65', 'contact@mariebobin.net', 'idée cadeau pour François Gaillard\non doit trouver une idée pour faire venir le vélo rouge afin de vérifier la compatibilité avec un moteur.', 'resolu', NULL, '2026-05-06 13:18:52', '2026-04-08 09:14:09', '2026-04-08 09:09:52', '2026-05-06 13:18:52'),
(56, 2, 1, 1, 'marc', '07 81 12 50 12', NULL, 'interressé par le porte velo , a rapeller', 'resolu', '2026-04-12 06:38:13', '2026-04-13 16:43:46', '2026-04-09 12:59:07', '2026-04-09 12:59:07', '2026-04-13 16:43:46'),
(57, 1, NULL, 6, 'Malegeant David', '07 67 45 45 53', 'malegeantd@gmail.com', 'vélo ville, reconditionné ou occasion', 'ouvert', NULL, NULL, '2026-04-09 13:07:31', '2026-04-09 13:07:31', '2026-04-09 13:07:31'),
(58, 1, NULL, 6, 'Dauphin Marie-Claude', '06 83 27 71 53', 'marie-claude.dauphin@wanadoo.fr', 'en attente d\'un vélo reconditionné 26\"', 'ouvert', NULL, NULL, '2026-04-10 08:24:05', '2026-04-10 08:24:05', '2026-04-10 08:24:05'),
(59, 2, 1, 6, 'laurent bourgeois', '06 10 27 36 28', 'sanantonio312@gmail.com', 'a rapeler car interressé potentiellement par velo ville rouge neomouv , conscient du pb de batterie en attente. budget potentielement superieur a 500e jusque 1500e pour autre proposition\nMr fait 1.85m et 115 Kg', 'ouvert', '2026-04-13 16:48:46', NULL, '2026-04-11 09:56:19', '2026-04-11 09:56:19', '2026-04-13 16:48:46'),
(60, 2, 1, 3, 'francoise richard', '06 80 02 84 92', 'richard.francoise@gmail.com', 'devis par mail\n\n4 VAE 4 jours = 4x140€\nlivraison gare de st malo le samedi soir 13/06 ou dimanche matin 14/06\nretour atelier paimpol le 17/06', 'resolu', NULL, '2026-04-23 10:30:16', '2026-04-11 12:21:48', '2026-04-11 12:21:48', '2026-04-23 10:30:16'),
(61, 1, NULL, 6, 'Hazard  Patricia', '06 30 52 68 72', 'hazard.patricia@wanadoo.fr', 'fiche technique e-woki par mail', 'resolu', NULL, '2026-04-13 16:50:52', '2026-04-13 09:10:54', '2026-04-13 09:10:54', '2026-04-13 16:50:52'),
(62, 1, NULL, 1, 'jean claude Arze', NULL, 'arze.jeanclaude@gmail.com', 'attestation domicile a envoyer par mail\nphotocopie piece d\'identité', 'resolu', NULL, '2026-04-13 16:50:47', '2026-04-13 09:25:16', '2026-04-13 09:25:16', '2026-04-13 16:50:47'),
(63, 1, NULL, 6, 'Calmels Philippe', '06 74 37 30 03', NULL, 'achat d\'un casque sur CGN, prévenir lorsque la commande est passée / arrivée.', 'ouvert', NULL, NULL, '2026-04-14 08:00:47', '2026-04-14 08:00:47', '2026-04-14 08:00:47'),
(64, 2, 1, 5, 'Mme Gillot Annick', '06 79 26 90 00', NULL, 'affiche de tarifs\n\ngite = Enez-Roch \nentre ancien college goas plat ET hopital\n25 chemin de kerpuns 22500 paimpol\navant debut juin', 'ouvert', '2026-04-25 06:20:04', NULL, '2026-04-14 09:18:20', '2026-04-14 09:18:20', '2026-04-25 06:20:04'),
(65, 2, NULL, 6, 'Lanard James', '07 62 47 85 53', NULL, 'contacter M Lemarchand pour permettre un test du moteur virvolt', 'ouvert', NULL, NULL, '2026-04-14 09:38:11', '2026-04-14 09:38:11', '2026-04-14 09:38:11'),
(66, 1, NULL, 1, 'Labusquiere Rodolphe', '06 88 27 73 64', NULL, 'interessé par l\'achat d\'un carlyna HG NG', 'resolu', NULL, '2026-04-22 19:31:59', '2026-04-14 15:05:03', '2026-04-14 15:05:03', '2026-04-22 19:31:59'),
(67, 2, 1, 2, 'mr vidament', NULL, NULL, 'notice du cadran de velo', 'resolu', '2026-04-23 07:00:28', '2026-04-23 07:00:44', '2026-04-23 07:00:38', '2026-04-15 08:55:04', '2026-04-23 07:00:44'),
(68, 2, 1, 2, 'carnec', '06 59 16 63 47', NULL, 'prevenir si velo ok', 'resolu', '2026-04-16 08:22:49', '2026-04-16 08:22:50', '2026-04-16 07:43:46', '2026-04-16 07:43:46', '2026-04-16 08:22:50'),
(69, 1, NULL, 6, 'Mme Duvivier Stéphanie', '06 89 93 79 42', NULL, 'achat rétroviseur, garde boue, porte bagage, réhausse potence.montage selle néomouv', 'resolu', NULL, '2026-05-06 12:39:24', '2026-04-16 13:04:45', '2026-04-16 13:04:45', '2026-05-06 12:39:24'),
(70, 2, 1, 6, 'Landerouin Aurore', '06 32 23 31 81', 'aurore.landerouin@gmail.com', 'achat occasion 24\"', 'ouvert', '2026-04-25 06:19:34', NULL, '2026-04-16 14:54:06', '2026-04-16 14:54:06', '2026-04-25 06:19:34'),
(71, 2, 1, 6, 'dusart Françoise', '06 12 70 50 59', NULL, 'recherche vélo pour emmener quelqu\'un (personne agée) \npossible Amsterdamer ou Triobike\nvoir asso st brieuc', 'ouvert', '2026-04-25 06:19:39', NULL, '2026-04-17 08:01:02', '2026-04-17 08:01:02', '2026-04-25 06:19:39'),
(72, 2, 1, 2, 'Meyer henri (velo atelier CDM pedalier en recherche)', '06 87 57 34 57', NULL, 'a rappeler \n\n- des nouvelles pour le pedalier ?\n\n- potentiellement interressé par un occasion musculaire', 'resolu', '2026-04-22 19:30:30', '2026-04-23 07:31:11', '2026-04-23 06:54:16', '2026-04-18 09:04:53', '2026-04-23 07:31:11'),
(73, 2, 1, 2, 'glacier yvon clech', '06 59 03 45 02', 'cuisine.islandais@gmail.com', 'commander la commande de derailleur 7v rotative pour le cargo glacier', 'resolu', '2026-04-21 07:45:50', '2026-04-21 13:37:05', '2026-04-18 13:24:36', '2026-04-18 13:24:36', '2026-04-21 13:37:05'),
(74, 2, 1, 2, 'yan Rospabé', '06 72 38 43 12', 'y.rospabe@gmail.com', 'potentiel probleme de bracket , changement de plateau?\nle client voudrais mouliner plus facilement en 1ere vitesse en cote par ex (changement plateau?)\n\nappoint ou purge freins AR\ncommande de support de sacoches avant?\n\ndevis', 'ouvert', '2026-04-22 19:28:43', NULL, '2026-04-22 19:30:19', '2026-04-21 13:36:53', '2026-04-22 19:30:19'),
(75, 2, 1, 6, 'lotoux arnaud', '06 31 42 26 67', NULL, 'interressé\" par le gitane course gris (andreas) prix?\npotentiellement les info aussi du gitane bleu (youtube)', 'ouvert', '2026-04-25 06:19:42', NULL, '2026-04-23 07:14:17', '2026-04-23 07:14:17', '2026-04-25 06:19:42'),
(76, 2, 1, 2, 'le bihan gaelle', '06 45 49 93 64', NULL, 'batterie TC-3SL \nla batterie semble chargée (5 voyants vert) , des qu\'on la mets sur le velo , plus rien , et 1 seul voyant rouge sur batterie\nla commande guidon a été changée deja.', 'ouvert', NULL, NULL, '2026-04-23 08:18:53', '2026-04-23 08:18:53', '2026-04-23 08:18:53'),
(77, 2, 1, 2, 'Mme huchet', NULL, NULL, 'porte gourde avec fixation cerflex possible?\nune sonnette serait la bienvenue :)\nla rappeler si besoin de prendre quelques mesures sur le velo :)', 'resolu', '2026-05-14 17:00:42', '2026-05-14 17:00:43', '2026-04-23 13:57:45', '2026-04-23 13:57:45', '2026-05-14 17:00:43'),
(78, 2, 1, 6, 'proprietaire du top life VTT noir elec occaz', NULL, NULL, 'descendre la prix de vente a 300 car il st bradé en neuf en ce moment a carrefour', 'resolu', '2026-04-25 06:19:49', '2026-04-29 07:41:42', '2026-04-23 20:27:54', '2026-04-23 14:48:42', '2026-04-29 07:41:42'),
(79, 2, 1, 2, 'lambda', '0', NULL, 'rechercher une bequille qui se fixe sur les 2 parties du hauban , sont tube est trop circulaire pour qu\'une bequille simple ait une bonne accroche\n le client repasse dans la semaine prochaine (a partir du 28/04)', 'resolu', '2026-05-11 14:01:30', '2026-05-11 14:01:31', '2026-04-24 15:57:57', '2026-04-24 15:57:57', '2026-05-11 14:01:31'),
(80, 1, 2, 2, 'discussion devis jo/niko', '06 99 34 12 46', NULL, 'pour ce devis (le bertin route)\nhttps://admin.lesvelosdarmor.bzh/atelier/devis/131/modifier\n\nil faudra simplement :\n- compter les dents\n- mesurer l\'entraxe (du centre roue jusqu\'a une vis de plateau x2)\n- dire combien de vis de serrage (souvent 4 ou 5)\n\n\npour ce devis (le collier de tige de selle): \nhttps://admin.lesvelosdarmor.bzh/atelier/devis/138/modifier\n\nil faut le diamètre extérieur du tube (cadre du vélo) qui accueil le tube de selle', 'ouvert', '2026-04-28 07:26:10', NULL, '2026-04-29 07:46:10', '2026-04-24 19:21:19', '2026-04-29 07:46:10'),
(81, 2, NULL, 3, 'seguin benoit', '0650659185', 'benoitvolnais@gmail.com', 'devis LOCATION mail\na faire mail:\nlocation 2 VAE\n1.81m 1.68m.\nmardi 4/08 matin au samedi 8/08 après midi.', 'resolu', NULL, '2026-04-29 15:02:24', '2026-04-28 07:51:34', '2026-04-28 07:51:34', '2026-04-29 15:02:24'),
(82, 2, 1, 4, 'Mme pierre margot PPAV', '06 08 84 86 78', NULL, 'souhaite te rencontrer afin de discuter de l\'evenement \"convergence festive\" organisé par ppav', 'ouvert', '2026-04-29 15:49:03', NULL, '2026-04-28 09:27:59', '2026-04-28 09:27:59', '2026-04-29 15:49:03'),
(83, 2, 1, 1, 'Guy Le Hénaff', '0', NULL, 'achat panier optimiz', 'resolu', '2026-05-14 17:00:29', '2026-05-14 17:00:30', '2026-05-01 14:13:18', '2026-05-01 14:13:18', '2026-05-14 17:00:30'),
(84, 2, NULL, 6, 'Jean luc Rouxel', '06 86 20 19 99', NULL, 'interet pour vélo 20\"\nchiffrer le petit blanc.', 'ouvert', NULL, NULL, '2026-05-01 15:03:45', '2026-05-01 15:03:45', '2026-05-01 15:03:45'),
(85, 2, NULL, 1, 'Mr Michon Maxime', '06 80 10 95 85', 'lm.michon@outlook.com', 'DEVIS Location :\n\n2 VAE du 29 juillet au 3 aout\nprise de vélo à l\'atelier paimpol le 29 juillet\nrecuperation au mont st michel le 3 aout', 'resolu', NULL, '2026-05-10 09:56:25', '2026-05-02 08:46:41', '2026-05-02 08:46:41', '2026-05-10 09:56:25'),
(86, 2, 1, 2, 'duvivier', '06 89 93 79 42', NULL, 'aimerais savoir ou en est son velo :) a rappeler mardi 5/05 si possible', 'resolu', '2026-05-11 14:01:03', '2026-05-11 14:01:04', '2026-05-02 13:49:42', '2026-05-02 13:49:12', '2026-05-11 14:01:04'),
(87, 2, 1, 6, 'yves hazard', NULL, 'hazard.patricia@wanadoo.fr', 'devis concernant achat de vélo ou électrification', 'ouvert', '2026-05-09 11:48:00', NULL, '2026-05-02 14:18:10', '2026-05-02 14:18:10', '2026-05-09 11:48:00'),
(88, 2, 1, 1, 'Joel Bernard', '06 79 35 57 13', 'joelbernard3@icloud.com', 'interet pour le X Beat MMR,  envoyer la fiche du vélo par email.', 'ouvert', NULL, NULL, '2026-05-04 09:25:50', '2026-05-04 09:25:50', '2026-05-04 09:25:50'),
(89, 1, NULL, 1, 'Vasseur Typhaine', '06 84 89 04 75', 'typhaine.vasseur@gmail.com', 'recherche BMX pour un enfant 1m55 avec frein', 'ouvert', NULL, NULL, '2026-05-04 12:57:31', '2026-05-04 12:57:31', '2026-05-04 12:57:31'),
(90, 2, 1, 3, 'hartmann', NULL, NULL, 'rappelle hartmann', 'resolu', '2026-05-09 11:47:43', '2026-05-09 11:47:44', '2026-05-04 15:33:52', '2026-05-04 15:33:52', '2026-05-09 11:47:44'),
(91, 2, NULL, 3, 'Vincent Fabrege', '06 64 40 35 27', 'mv.fabreges@gmail.com', '2 vae fin aout\n22-23-24-25-26 aout\nMme 1.65,\nmonsieur 1.80\nprix partenaire (Thierry 9 rue chateaubriand)', 'resolu', NULL, '2026-05-06 12:02:09', '2026-05-05 13:07:19', '2026-05-05 13:07:19', '2026-05-06 12:02:09'),
(92, 2, 1, 3, NULL, NULL, NULL, 'J\'ai besoin que tu répondes au mail de Marot Benoit qui demande des adresses de location pour loger dans le coin stp', 'resolu', '2026-05-14 17:00:16', '2026-05-14 17:00:17', '2026-05-05 21:51:36', '2026-05-05 21:51:36', '2026-05-14 17:00:17'),
(93, 2, 1, 2, 'VELAIR', '06 63 52 52 17', NULL, 'APPELER CLIENTE URGENT\nMAX 150EUROS', 'resolu', '2026-05-14 17:02:02', '2026-05-14 17:02:03', '2026-05-06 08:42:48', '2026-05-06 08:42:48', '2026-05-14 17:02:03'),
(95, 2, 1, 3, 'Benoit VOLNAIS', NULL, 'benoitvolnais@gmail.com', 'Peux-tu rembourser l\'acompte de 70€ à Benoit VOLNAIS qui annule sa réservation.', 'resolu', '2026-05-10 21:16:28', '2026-05-10 21:16:31', '2026-05-06 11:53:26', '2026-05-06 11:53:26', '2026-05-10 21:16:31'),
(96, 2, 1, 3, 'Françoise RICHARD', NULL, 'richard.francoise44@gmail.com', 'J\'ai besoin que tu répondes au mail de Françoise RICHARD concernant la photos et références des sacoches.', 'resolu', '2026-05-11 14:00:45', '2026-05-11 14:00:47', '2026-05-06 11:55:04', '2026-05-06 11:55:04', '2026-05-11 14:00:47'),
(97, 2, 1, 6, 'Belvaux Anthony', '07 71 94 75 77', 'anthonybelvaux@yahoo.fr', 'indiquer le prix du Linaria en 18mois', 'resolu', '2026-05-09 11:46:02', '2026-05-09 11:46:04', '2026-05-06 14:10:12', '2026-05-06 14:10:12', '2026-05-09 11:46:04'),
(98, 2, 1, 6, 'Chatelon Stéphanie', '06 07 82 92 77', 'schatelon@gmail.com', 'interet pour le Roller NG Neomouv, \nDevis avec sacoches (impermeable) + antivol', 'ouvert', NULL, NULL, '2026-05-06 15:17:20', '2026-05-06 15:17:20', '2026-05-06 15:17:20'),
(99, 2, 1, 6, 'Brouard Francois', '07 89 20 56 04', NULL, 'info sur la dispo du T3S\nlocation 18 mois : 350euros puis 59/mois.', 'ouvert', NULL, NULL, '2026-05-08 15:02:21', '2026-05-08 15:02:21', '2026-05-08 15:02:21'),
(100, 2, 1, 6, 'blanquer andré', '06 73 34 77 73', 'ablach@laposte.net', 'intéressé ++ par le ENARA  TK rouge (budget 2000e)\nrétroviseur + antivol\nvoir si cale pieds possible\nà partir de mardi\nmail de préférence', 'ouvert', NULL, NULL, '2026-05-12 12:36:56', '2026-05-09 16:19:37', '2026-05-12 12:36:56'),
(108, 1, NULL, 6, 'Marina Chardron', '06 03 67 25 62', 'marina.chardron@gmail.com', 'interet pour un long tail\nenvoyer fiche neomouv et NCM', 'ouvert', NULL, NULL, '2026-05-15 08:08:40', '2026-05-15 08:08:40', '2026-05-15 08:08:40'),
(109, 2, 1, 3, 'Marot Benoit', NULL, 'benoitmarot@yahoo.fr', 'Je te laisse répondre à Benoit Marot concernant les locations dans le coin et sur son acompte.', 'resolu', NULL, '2026-05-21 13:16:39', '2026-05-18 13:01:18', '2026-05-18 13:01:18', '2026-05-21 13:16:39'),
(110, 1, NULL, 6, 'Rio Claire', '06 77 87 01 25', 'claire.leguennec@orange.fr', 'devis Elaia 2 NG blanc creme\nAntivol décathlon 500L\npanier 497167\nretroviseur', 'ouvert', NULL, NULL, '2026-05-19 07:30:44', '2026-05-19 07:30:44', '2026-05-19 07:30:44'),
(102, 1, NULL, 1, 'Sebastien Hélan', '06 49 19 30 90', 'sebastien.helan@gmail.com', 'envoyé un visuel logo pour Les vélos d\'Armor', 'resolu', NULL, '2026-05-14 17:01:55', '2026-05-11 14:42:48', '2026-05-11 14:42:48', '2026-05-14 17:01:55'),
(103, 1, NULL, 5, 'Fabien Jacob', '06 65 42 44 39', 'fabien.jacob@hotmail.fr', 'gites sur le port de Lezardrieux', 'ouvert', NULL, NULL, '2026-05-11 14:55:48', '2026-05-11 14:55:48', '2026-05-11 14:55:48'),
(104, 1, NULL, 4, 'Droniou Martine', '06 83 60 82 06', NULL, 'nous irions chercher le vélo (hs) à plouezec ( 20 route de la gare ) lorsque nous aurons à livrer un vélo dans le secteur.', 'ouvert', NULL, NULL, '2026-05-12 07:48:03', '2026-05-12 07:48:03', '2026-05-12 07:48:03'),
(105, 1, NULL, 6, 'Nicolas Grach', '0', NULL, 'bontrager gr checkout\ncintre evasé type richey logic', 'ouvert', NULL, NULL, '2026-05-12 15:35:32', '2026-05-12 15:35:32', '2026-05-12 15:35:32'),
(106, 1, 2, 3, 'Anne sophie creach', '06 12 94 03 82', 'annesophie.creach@gmail.com', '4 VAE\n10-13 aout \n1.76, 1.68, 1.70, 1.65\nenvoyer les deux options de sacoches\nprix de panier ?', 'resolu', NULL, '2026-05-18 13:06:59', '2026-05-18 12:30:44', '2026-05-13 09:40:58', '2026-05-18 13:06:59'),
(107, 2, NULL, 3, 'Girard Nathalie', '06 84 43 52 00', 'girardnat39@gmail.com', '4 VAE du lundi 18/05 au jeudi 21/05 soit 4 jours PARTENAIRE gite poulafret : 4x119 = 476€\nH 1.75\nH 1.75\nF 1.65\nF 1.68', 'resolu', NULL, '2026-05-19 16:17:21', '2026-05-18 13:21:26', '2026-05-14 09:44:43', '2026-05-19 16:17:21'),
(111, 2, 1, 6, 'Rolland Joseph', '06 84 37 65 05', 'joseph.rolland@orange.fr', 'jante mavic cassée\ntenir informé pour le prix chez mavic\n\n700 23 ou 25 avec 20 trous en campagnolo / patin', 'ouvert', NULL, NULL, '2026-05-19 08:07:30', '2026-05-19 08:07:30', '2026-05-19 08:07:30'),
(112, 2, 1, 6, 'Tilly annie', '06 99 24 08 33', 'tilly.annie@yahoo.fr', 'renseignements sur electrification de vélo de ville peugeot\ntechnologie? et prix?', 'ouvert', NULL, NULL, '2026-05-21 08:29:56', '2026-05-21 08:29:56', '2026-05-21 08:29:56'),
(113, 2, NULL, 3, 'antoni', '06 86 89 57 62', 'laureantoni@hotmail.fr', 'du 2 aout au 14 aout soit 13jours\nrécup le 1er aout après midi suivant heure de train\nH musculaire 1.85m\nF VAE 1.69m\nremorque + siège enfant(ou pas) suivant le test de taille sur place (modulable)\ndevis par mail svp', 'resolu', NULL, '2026-05-21 16:49:06', '2026-05-21 13:14:31', '2026-05-21 13:14:31', '2026-05-21 16:49:06'),
(114, 2, 1, 1, 'o2 feel depannage', NULL, NULL, 'envoyer la facture par mail , le devis n\'etait pas complet avant validation ;)', 'ouvert', NULL, NULL, '2026-05-26 07:05:49', '2026-05-26 07:05:49', '2026-05-26 07:05:49'),
(115, 2, 1, 2, 'picard patrick', '06 08 90 22 24', 'parick.picard22@gmail.com', 'vélo = vélo de ville LEB 400 sport\n\ncherche porte bagages avec lumière intégrée\nfixations basses cadre , et fixation haute GB AR\ncontacter si besoin d\'info supp', 'ouvert', NULL, NULL, '2026-05-27 08:14:01', '2026-05-27 08:14:01', '2026-05-27 08:14:01'),
(118, 2, NULL, 1, 'le guennec', '07 83 29 84 67', NULL, 'velo occasion\nvoir poignées 22mm diametre\npayé 90e', 'ouvert', NULL, NULL, '2026-05-28 07:58:42', '2026-05-28 07:58:42', '2026-05-28 07:58:42'),
(119, 1, NULL, 6, 'Allan Peron', '06 48 43 71 52', NULL, 'vélo tout chemin, grande taille\npas de difficulté avec l\'accessibilité\nprévois des parcours jusqu\'à Tréguier', 'ouvert', NULL, NULL, '2026-05-29 14:06:19', '2026-05-29 14:06:19', '2026-05-29 14:06:19'),
(117, 1, 2, 3, 'camille prioul', '06 72 09 26 75', 'cprioul@gmail.com', '20 -27 juin : 1vae : 1.50-55 (livraison ferme de kerloury)\n22-27 juin : 1vae 1.71 récupération atelier\n\nrécupération ferme de kerloury 27 juin fin de matinée', 'resolu', NULL, '2026-05-28 20:24:21', '2026-05-27 16:43:16', '2026-05-27 16:43:16', '2026-05-28 20:24:21'),
(120, 1, 2, 3, 'Mme Le Breton ou Mme Breton', NULL, NULL, 'attention, \n\nil y a dans les messages une reservation au nom de breton ou le breton. de mémoire elle est le samedi 20 juin, mais je n\'ai rien mis dans l\'agenda.\nje me charge de retrouver et de confirmer cette resa dans le week end.  attention, c\'est entre 4 et 6 vae donc ca n\'est pas neutre', 'resolu', NULL, '2026-05-30 17:26:38', '2026-05-30 06:19:32', '2026-05-30 06:19:32', '2026-05-30 17:26:38'),
(121, 2, NULL, 6, 'Mr Blanquer', NULL, NULL, 'ajout dans le devis de deux pinces pour pantalon', 'ouvert', NULL, NULL, '2026-05-30 14:20:33', '2026-05-30 14:20:33', '2026-05-30 14:20:33'),
(122, 2, NULL, 3, 'Mr Revol Philippe', '06 23 25 60 64', 'phrevol71@gmail.com', 'Location 2 VAE\n1.70m\n1.75m\ndu samedi 13juin au samedi 20 juin\nlivraison 2 impasse du moulin Loguivy de la mer 9h samedi 13 et récup 19h samedi 20\nPRIX PARTENAIRE', 'resolu', NULL, '2026-06-03 16:15:27', '2026-05-30 15:14:59', '2026-05-30 15:14:59', '2026-06-03 16:15:27'),
(123, 2, 1, 1, 'l\'hostis', NULL, NULL, 'ref rack batterie = c8707758 27.5 dgw07 / dgw10', 'ouvert', NULL, NULL, '2026-06-06 08:15:04', '2026-06-06 08:15:04', '2026-06-06 08:15:04'),
(125, 2, 1, 2, 'kergal', NULL, NULL, 'repasse mardi pour faire remettre sa selle en place', 'resolu', '2026-06-10 14:41:15', '2026-06-10 14:41:16', '2026-06-06 14:15:23', '2026-06-06 14:15:23', '2026-06-10 14:41:16'),
(126, 2, 1, 2, 'Mr ropers', '06 07 67 07 98', 'michelropers1@gmail.com', 'chaine 10V groupe 105\npneu (AV/AR) 700x25c\npatin freins AR\n\ndevis a envoyer par mail', 'ouvert', NULL, NULL, '2026-06-09 08:38:15', '2026-06-09 08:38:15', '2026-06-09 08:38:15'),
(127, 1, NULL, 1, 'Guy Le Henaff', '06 88 97 20 88', NULL, 'Collier de serrage de selle\nenregistrement des garanties', 'ouvert', NULL, NULL, '2026-06-09 09:50:42', '2026-06-09 09:50:42', '2026-06-09 09:50:42'),
(128, 2, 1, 3, 'Mr mengin', '06 78 58 40 52', NULL, 'loc du 12/13 juillet\nrappeler au sujet d\'une potentielle location du velo cargo', 'resolu', '2026-06-15 15:49:42', '2026-06-15 15:49:44', '2026-06-11 07:47:08', '2026-06-11 07:47:08', '2026-06-15 15:49:44'),
(129, 1, NULL, 6, 'Lebeuf Marie-Pierre', '06 22 73 84 52', NULL, 'souhaite acheter un virvolt 500, roue de 28pouces. informer de la disponibilité.', 'ouvert', NULL, NULL, '2026-06-11 08:50:40', '2026-06-11 08:50:40', '2026-06-11 08:50:40'),
(130, 1, NULL, 6, 'Alloca Marianne', '06 20 31 27 23', 'marianne.rondini@ac-rennes.fr', 'devis pour location 3 VTT 2-3 juillet / retour le 24 juillet.\nrenseignement pour du VTT en neuf, différent niveau de gamme à suggérer.\nobjectif : montagne facile.', 'ouvert', NULL, NULL, '2026-06-11 13:07:40', '2026-06-11 13:06:01', '2026-06-11 13:07:40'),
(131, 2, NULL, 3, 'Hermann olivier', '06 09 78 52 69', 'olivierhermann@free.fr', 'location 2 VAE Sb PARTENAIRE\ndu samedi 8/08 au samedi 22/08\nlivraison récupération = clos de la baie\ndevis par mail svp', 'resolu', NULL, '2026-06-17 18:36:18', '2026-06-12 08:05:40', '2026-06-12 08:05:40', '2026-06-17 18:36:18'),
(132, 1, NULL, 6, 'Melanie Lemaire', '06 12 56 73 08', 'melanielem@gmail.com', 'tenir au courant pour un carlina hy ng vert d\'eau tarif --', 'ouvert', NULL, NULL, '2026-06-16 13:37:37', '2026-06-16 13:37:23', '2026-06-16 13:37:37'),
(133, 1, NULL, 1, 'Le Bideau Jean-François', '06 48 76 64 45', NULL, 'recherche VAE -budget 1000 - 1200 typé plutôt rando', 'ouvert', NULL, NULL, '2026-06-17 15:53:29', '2026-06-17 15:53:29', '2026-06-17 15:53:29'),
(134, 1, NULL, 1, 'Lena Le Poullennec', '07 57 18 56 82', 'l.a.lp@outlook.com', 'intérêt pour un VAE longue durée : voir tarif et model', 'ouvert', NULL, NULL, '2026-06-17 15:55:00', '2026-06-17 15:55:00', '2026-06-17 15:55:00'),
(135, 1, NULL, 6, 'Tredan Gilbert', '06 75 405 725', NULL, 'recherche VAE - à deja tester le Elaia2. ( test de roue AR ? )', 'ouvert', NULL, NULL, '2026-06-18 14:10:40', '2026-06-18 14:10:40', '2026-06-18 14:10:40'),
(136, 2, NULL, 2, 'Mme Durey', '06 74 51 47 39', NULL, 'Mme voudrais des nouvelles svp', 'ouvert', NULL, NULL, '2026-06-19 08:19:09', '2026-06-19 08:19:09', '2026-06-19 08:19:09'),
(137, 2, 1, 2, 'touarin', '06 80 25 20 50', NULL, 'batterie deposé , elle voudrais des nouvelles\nfiche atelier crée', 'ouvert', NULL, NULL, '2026-06-20 08:35:59', '2026-06-20 08:35:59', '2026-06-20 08:35:59'),
(138, 2, 1, 6, 'le herissé catherine', '06 08 42 15 04', NULL, 'commander un panier \ndiametre cerceau = 22mm\nlargeur max avant courbure = 100 mm', 'ouvert', NULL, NULL, '2026-06-20 14:56:04', '2026-06-20 14:56:04', '2026-06-20 14:56:04'),
(139, 1, 2, 3, 'loïs Lamoine', '06 08 93 86 68', 'lamoine.lois@wanadoo.fr', '13-17 juillet\n1.64 VAE\nprise a l atelier', 'resolu', NULL, '2026-06-26 08:19:20', '2026-06-25 16:19:48', '2026-06-25 16:19:48', '2026-06-26 08:19:20'),
(140, 2, 1, 2, 'Mme bregeon', '06 87 19 09 21', NULL, 'commander 2 pneus 700x48c costaud\n1 chambre air schradder (et autre pour le stock)', 'ouvert', NULL, NULL, '2026-06-27 16:35:04', '2026-06-27 16:35:04', '2026-06-27 16:35:04'),
(141, 1, 2, 3, 'Gourlet Mathieu', '07 68 27 16 75', 'mathgourlet@gmail.com', '28 juillet - 7 aout\n5 VAE \ntaille a preciser (4M et un L mais info à suivre pour etre plus précis: attention, on a vraiment besoins des tailles car selon les constructeur velo,  S, M L XL n\'ont pas les memes correspondance. c\'est l\'info que j ai donné au client.)\nTreguier Livraison et recuperation', 'resolu', NULL, '2026-06-29 13:21:20', '2026-06-29 12:39:03', '2026-06-29 12:39:03', '2026-06-29 13:21:20');

-- --------------------------------------------------------

--
-- Structure de la table `message_categories`
--

CREATE TABLE `message_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#6b7280',
  `position` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `message_categories`
--

INSERT INTO `message_categories` (`id`, `slug`, `label`, `color`, `position`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'accueil', 'Accueil', '#3b82f6', 1, 0, '2026-04-07 13:08:58', '2026-04-07 13:08:58'),
(2, 'atelier', 'Atelier', '#f59e0b', 2, 0, '2026-04-07 13:08:58', '2026-04-07 13:08:58'),
(3, 'location', 'Location', '#10b981', 3, 0, '2026-04-07 13:08:58', '2026-04-07 13:08:58'),
(4, 'autre', 'Autre', '#6b7280', 99, 1, '2026-04-07 13:08:58', '2026-04-07 13:08:58'),
(5, 'partenaire', 'Partenaire', '#d6e22c', 4, 0, '2026-04-07 13:10:13', '2026-04-07 13:10:20'),
(6, 'achat', 'Achat', '#65d336', 5, 0, '2026-04-07 13:10:38', '2026-04-07 13:10:38');

-- --------------------------------------------------------

--
-- Structure de la table `message_replies`
--

CREATE TABLE `message_replies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `message_id` bigint(20) UNSIGNED NOT NULL,
  `author_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `recipient_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `content` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `message_replies`
--

INSERT INTO `message_replies` (`id`, `message_id`, `author_user_id`, `recipient_user_id`, `content`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, NULL, 'vélo M5 prêté', NULL, '2026-03-09 10:07:40', '2026-03-09 10:07:40'),
(2, 9, 1, 2, 'GG', '2026-03-10 10:09:14', '2026-03-10 10:09:03', '2026-03-10 10:09:14'),
(3, 9, 2, 1, 'TG', '2026-03-10 10:10:35', '2026-03-10 10:09:50', '2026-03-10 10:10:35'),
(4, 9, 1, 2, 'OK', '2026-03-10 10:11:07', '2026-03-10 10:10:51', '2026-03-10 10:11:07'),
(5, 7, 1, NULL, '06 60 70 48 90', NULL, '2026-03-11 09:00:01', '2026-03-11 09:00:01'),
(6, 5, 1, NULL, '1,70\n1,65\n1,60\n1,58', NULL, '2026-03-11 11:05:52', '2026-03-11 11:05:52'),
(7, 11, 1, NULL, 'client majeur\nparticulier,\nresidant en france\nsmartphone + connection internet pour valider le paiement\nla carte bancaire doit être valable 4 mois\nle nom de la CB doit etre le meme que sur la carte d\'identité\n\n100 a 3000 euros\nsur le 3/4 fois, pas de justificatif\n\nsur la partie credit (10x 60x), y\'a des justificatifs\nsi la demande est refusé pour sa situation personnelle, la demande peut etre faite conjointement (a deux avec son conjoint par exemple)\n\n1000 a 10000\npiece d\'identité et rib \nau delà de 3000\njustificatif de domicile et dernier bulletin de salaire (relevé d\'information pour les retraité)\n\nsur la partie credit, 20mn d\'attente pour avoir le dossier validé', NULL, '2026-03-11 13:28:33', '2026-03-11 13:28:33'),
(8, 12, 1, NULL, 'delai max  mai-juin', NULL, '2026-03-11 15:37:14', '2026-03-11 15:37:14'),
(9, 13, 1, NULL, 'je viens d\'envoyer un mail pour demander les tailles', NULL, '2026-03-11 17:35:11', '2026-03-11 17:35:11'),
(10, 13, 1, NULL, '1M57, 1M77', NULL, '2026-03-11 19:56:57', '2026-03-11 19:56:57'),
(11, 15, 1, 2, 'tu peux me dire la teneur du message ? \nj\'ai essayé de les appeler mais je suis tombé sur le repondeur.', '2026-03-12 14:28:49', '2026-03-12 09:44:58', '2026-03-12 14:28:49'),
(12, 22, 1, 2, 'Il me faudrait le numéro de téléphone de monsieur balcou steuplé :)', '2026-03-14 11:15:41', '2026-03-14 11:11:07', '2026-03-14 11:15:41'),
(13, 21, 1, 2, 'Trop bonne idée !\nJe vais chercher ca', '2026-03-14 11:15:38', '2026-03-14 11:11:51', '2026-03-14 11:15:38'),
(14, 21, 2, 1, 'c\'est lui meme qui a demandé :)', '2026-03-14 12:52:23', '2026-03-14 11:13:31', '2026-03-14 12:52:23'),
(15, 22, 2, 1, 'Mr Balcou : 07 86 11 51 49\nMail : balcou.joseph@gmail.com', '2026-03-14 12:52:16', '2026-03-14 11:15:05', '2026-03-14 12:52:16'),
(16, 23, 1, 2, 'oui, m\'en occupe lundi', '2026-03-14 13:19:10', '2026-03-14 12:52:03', '2026-03-14 13:19:10'),
(17, 13, 1, NULL, 'réservation enregistrée', NULL, '2026-03-18 09:52:32', '2026-03-18 09:52:32'),
(18, 27, 1, NULL, 'au moins 4 VAE\n16e de livraison\n1.65\n1.70\n1.70\n1.65', NULL, '2026-03-18 14:16:06', '2026-03-18 14:16:06'),
(19, 33, 2, 1, 'oui , checké le pneu , changé la chambre a air , jusque ici il conserve sa pression', '2026-03-24 11:56:52', '2026-03-24 09:00:37', '2026-03-24 11:56:52'),
(20, 24, 1, NULL, 'voir le prix d\'une batterie ou d\'un reconditionnement batterie\nmentionner pour le 26 la couleur noir en choix préférenciel\n\nvoir le prix d\'un antivol de roue et de son montage', NULL, '2026-03-26 09:23:37', '2026-03-26 09:23:37'),
(21, 24, 1, NULL, 'prix panier', NULL, '2026-03-26 09:23:51', '2026-03-26 09:23:51'),
(22, 38, 1, 2, 'Le capteur coute 20 euros +frais de port à determiner, mais il n\'a pas le bon connecteur donc il faut qu\'on passe du temps à faire de la soudure.\ntu peux proposer un devis a 25 + 20 de main d\'oeuvre.', '2026-03-31 07:35:20', '2026-03-28 12:18:32', '2026-03-31 07:35:20'),
(23, 54, 1, NULL, 'ajouter un panier avant', NULL, '2026-04-07 16:44:06', '2026-04-07 16:44:06'),
(24, 55, 1, NULL, '102 impasse gardenn park meur paimpol\naller chercher le vélo après le 12 avril\non a jusqu\'au 24 avril pour rendre le vélo (non inclu)', NULL, '2026-04-08 09:14:09', '2026-04-08 09:14:09'),
(25, 74, 1, 2, 'je peux y repondre en voyant des photos détaillées et des nombres de dents. \n(photos dérailleur et manette également)\n\npour le support de sacoche, est ce qu\'il a deja la sacoche ? il faut un model précis attendu par le client ?', NULL, '2026-04-22 19:30:19', '2026-04-22 19:30:19'),
(26, 72, 1, 2, 'c\'est le vélo doré \"motoconfort\" que j\'ai emmené chez andreas\n\nje dois faire la facture mais le vélo est fini.', '2026-04-24 07:32:53', '2026-04-22 19:31:17', '2026-04-24 07:32:53'),
(27, 72, 1, 2, 'la facture est prete. je t\'invite a lire le commentaire', '2026-04-24 07:32:53', '2026-04-23 06:54:16', '2026-04-24 07:32:53'),
(28, 67, 1, 2, 'c\'est envoyé', '2026-04-24 07:33:31', '2026-04-23 07:00:38', '2026-04-24 07:33:31'),
(29, 78, 1, 2, 'C\'est une remarque client ou du propriétaire ?', '2026-04-29 07:41:41', '2026-04-23 20:27:54', '2026-04-29 07:41:41'),
(30, 80, 2, 1, 'cadre bas = 35.7 \ncadre haut = 32.0  x2', '2026-05-11 14:01:11', '2026-04-29 07:46:10', '2026-05-11 14:01:11'),
(31, 86, 2, 1, 'c\'est le velo riverside neuf a monter avec ses accesoires', '2026-05-03 14:40:44', '2026-05-02 13:49:42', '2026-05-03 14:40:44'),
(32, 100, 2, 1, 'voir gravures cadre assurance \ncasque digne de ce nom', '2026-05-12 12:36:29', '2026-05-09 16:29:32', '2026-05-12 12:36:29'),
(33, 100, 1, 2, 'antivol decath 500 L', '2026-05-19 07:44:10', '2026-05-12 12:34:07', '2026-05-19 07:44:10'),
(34, 100, 1, 2, 'sacoche entre 14 et 18 L (sacoche cuire un peu typé ancien)', '2026-05-19 07:44:12', '2026-05-12 12:36:56', '2026-05-19 07:44:12'),
(35, 106, 2, 1, '+ livraison lannion', NULL, '2026-05-18 12:30:44', '2026-05-18 12:30:44'),
(36, 107, 2, NULL, '1.68 2J.', NULL, '2026-05-18 13:21:26', '2026-05-18 13:21:26'),
(37, 130, 1, NULL, 'nom de jeune fille, pour la mise en place du partenariat avec des gites (plouezec) -Mme Rault', NULL, '2026-06-11 13:07:40', '2026-06-11 13:07:40'),
(38, 132, 1, NULL, 'taille M/L', NULL, '2026-06-16 13:37:37', '2026-06-16 13:37:37');

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_11_16_174520_create_clients_table', 1),
(5, '2025_11_17_153517_add_two_factor_columns_to_users_table', 1),
(6, '2025_11_17_165349_create_quotes_table', 1),
(7, '2025_11_17_165350_create_quote_lines_table', 1),
(8, '2025_11_18_075915_make_purchase_price_ht_nullable_in_quote_lines_table', 1),
(9, '2025_11_18_082046_add_status_to_quotes_table', 1),
(10, '2025_11_18_084547_make_margin_fields_nullable_in_quote_lines_table', 1),
(11, '2025_11_18_095732_update_quotes_status_enum_values', 1),
(12, '2026_01_19_113441_add_invoiced_at_to_quotes_table', 1),
(13, '2026_01_19_160154_add_quantity_to_quote_lines_table', 1),
(14, '2026_01_20_090000_create_authorized_emails_table', 1),
(15, '2026_01_26_103144_add_metier_to_quotes_table', 2),
(16, '2026_01_26_103258_create_monthly_kpis_table', 3),
(17, '2026_01_26_132131_add_bike_fields_to_quotes_table', 3),
(18, '2026_01_30_091856_change_quotes_reference_unique_constraint', 4),
(19, '2026_02_02_090543_add_line_totals_to_quote_lines_table', 5),
(20, '2026_02_03_155806_convert_existing_quote_lines_to_unit_prices', 5),
(21, '2026_02_03_221211_add_time_tracking_to_quotes', 6),
(22, '2026_02_04_100212_add_remarks_to_quotes_table', 7),
(23, '2026_02_09_220435_create_bike_types_table', 8),
(24, '2026_02_09_220533_create_reservations_table', 8),
(25, '2026_02_09_220617_create_reservation_items_table', 8),
(26, '2026_02_13_100000_add_selection_to_reservations_table', 8),
(27, '2026_02_13_110000_add_color_to_reservations_table', 8),
(28, '2026_02_16_171439_create_bikes_table', 8),
(29, '2026_02_16_220000_recreate_bikes_table', 9),
(30, '2026_02_17_094143_add_battery_fields_to_bikes_table', 10),
(31, '2026_02_17_105154_create_bike_categories_table', 10),
(32, '2026_02_17_105205_create_bike_sizes_table', 10),
(33, '2026_02_17_105408_convert_bikes_to_foreign_keys', 10),
(34, '2026_02_17_120000_add_rail_to_battery_type_enum', 10),
(35, '2026_02_22_155225_populate_line_totals_for_existing_quote_lines', 11),
(36, '2026_02_27_100052_add_has_size_has_frame_type_to_bike_categories', 11),
(37, '2026_02_27_100120_make_frame_type_nullable_in_bikes', 11),
(38, '2026_02_27_100133_make_bike_types_columns_dynamic', 11),
(39, '2026_02_28_100000_create_reservation_payments_table', 12),
(40, '2026_03_07_100000_create_messages_table', 13),
(41, '2026_03_07_100001_create_message_replies_table', 13),
(42, '2026_03_08_100000_add_category_to_messages_table', 13),
(43, '2026_03_08_173932_create_media_table', 14),
(44, '2026_03_08_173955_create_upload_tokens_table', 14),
(45, '2026_03_08_183003_fix_media_table_index', 14),
(46, '2026_03_09_145635_add_work_mode_to_users_table', 15),
(47, '2026_03_09_151139_add_julien_to_work_mode_enums', 15),
(48, '2026_03_09_153122_replace_mode_with_user_id_in_messages', 15),
(49, '2026_03_09_160925_seed_production_users', 15),
(50, '2026_03_16_202430_add_revenue_ttc_to_monthly_kpis_table', 16),
(51, '2026_03_21_100000_create_agenda_meta_table', 17),
(52, '2026_04_02_110943_create_message_categories_table', 18),
(53, '2026_04_03_133429_add_last_activity_at_to_messages_table', 18),
(54, '2026_04_22_074558_add_order_tracking_to_quote_lines_table', 19),
(55, '2026_04_22_195612_normalize_quote_status_values', 19),
(56, '2026_05_31_150240_add_is_archived_to_quotes_table', 20),
(57, '2026_05_31_151859_add_email_note_to_quotes_table', 20),
(58, '2026_05_11_142301_create_partenaires_table', 21),
(59, '2026_05_11_142302_create_demandes_partenaire_table', 21),
(60, '2026_05_11_142303_add_type_to_authorized_emails_table', 21),
(61, '2026_05_11_142557_create_personal_access_tokens_table', 21),
(62, '2026_05_12_200939_add_creneau_to_demandes_partenaire_table', 22),
(63, '2026_06_02_055050_create_bike_maintenance_logs_table', 22),
(64, '2026_06_02_060516_add_order_tracking_to_bike_maintenance_logs_table', 22),
(65, '2026_06_03_092552_add_paid_at_to_quotes_table', 22),
(66, '2026_06_03_102425_add_date_recuperation_to_reservations_table', 22),
(67, '2026_06_05_201411_add_intermediate_sizes_to_bike_sizes_table', 23),
(68, '2026_06_05_201912_create_bike_models_table', 23),
(69, '2026_06_05_202855_add_integree_to_battery_type_enum', 23),
(70, '2026_06_05_203456_add_cargo_to_bike_categories_table', 23),
(71, '2026_06_15_125115_create_article_categories_table', 24),
(72, '2026_06_15_125115_create_article_subcategories_table', 24),
(73, '2026_06_15_125115_create_articles_table', 24),
(74, '2026_06_15_125115_create_stock_movements_table', 25),
(75, '2026_06_15_183830_add_article_id_to_bike_maintenance_logs_table', 25),
(76, '2026_06_15_183830_add_article_id_to_quote_lines_table', 25),
(77, '2026_06_15_200851_create_brands_table', 25),
(78, '2026_06_15_200852_add_brand_id_to_articles_table', 25),
(79, '2026_06_16_053613_create_suppliers_table', 25),
(80, '2026_06_16_053614_add_supplier_id_to_articles_table', 25),
(81, '2026_06_24_124533_rename_sale_price_ht_to_sale_price_ttc_on_articles_table', 26);

-- --------------------------------------------------------

--
-- Structure de la table `monthly_kpis`
--

CREATE TABLE `monthly_kpis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `metier` varchar(50) NOT NULL,
  `year` smallint(5) UNSIGNED NOT NULL,
  `month` tinyint(3) UNSIGNED NOT NULL,
  `invoice_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `revenue_ht` decimal(12,2) NOT NULL DEFAULT 0.00,
  `revenue_ttc` decimal(10,2) NOT NULL DEFAULT 0.00,
  `margin_ht` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `monthly_kpis`
--

INSERT INTO `monthly_kpis` (`id`, `metier`, `year`, `month`, `invoice_count`, `revenue_ht`, `revenue_ttc`, `margin_ht`, `created_at`, `updated_at`) VALUES
(144, 'atelier', 2026, 6, 57, 11623.72, 0.00, 5765.02, '2026-06-20 18:49:33', '2026-06-26 16:02:05'),
(143, 'atelier', 2026, 5, 42, 7975.13, 0.00, 3426.85, '2026-06-20 18:49:33', '2026-06-20 18:49:33'),
(142, 'atelier', 2026, 4, 45, 6702.29, 0.00, 3376.80, '2026-06-20 18:49:33', '2026-06-20 18:49:33'),
(138, 'location', 2026, 6, 57, 5955.83, 7147.00, 0.00, '2026-06-11 11:19:41', '2026-06-29 13:28:40'),
(137, 'location', 2026, 5, 56, 5194.21, 6233.05, 0.00, '2026-06-11 11:19:41', '2026-06-11 11:19:41'),
(141, 'atelier', 2026, 3, 40, 3595.59, 0.00, 2036.71, '2026-06-20 18:49:33', '2026-06-20 18:49:33'),
(136, 'location', 2026, 4, 24, 2157.50, 2589.00, 0.00, '2026-06-11 11:19:41', '2026-06-11 11:19:41'),
(140, 'atelier', 2026, 2, 27, 3470.02, 0.00, 1683.55, '2026-06-20 18:49:33', '2026-06-20 18:49:33'),
(135, 'location', 2026, 3, 16, 1470.00, 1764.00, 0.00, '2026-06-11 11:19:41', '2026-06-11 11:19:41'),
(134, 'location', 2026, 2, 1, 28.33, 34.00, 0.00, '2026-06-11 11:19:41', '2026-06-11 11:19:41'),
(139, 'atelier', 2026, 1, 9, 734.18, 0.00, 595.76, '2026-06-20 18:49:33', '2026-06-20 18:49:33');

-- --------------------------------------------------------

--
-- Structure de la table `partenaires`
--

CREATE TABLE `partenaires` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `quotes`
--

CREATE TABLE `quotes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `bike_description` varchar(255) NOT NULL DEFAULT '',
  `reception_comment` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `email_note` text DEFAULT NULL,
  `metier` varchar(255) NOT NULL DEFAULT 'atelier',
  `reference` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'reception',
  `invoiced_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `valid_until` date DEFAULT NULL,
  `discount_type` enum('amount','percent') DEFAULT NULL,
  `discount_value` decimal(10,2) DEFAULT NULL,
  `total_ht` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_tva` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_ttc` decimal(10,2) NOT NULL DEFAULT 0.00,
  `margin_total_ht` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_estimated_time_minutes` int(10) UNSIGNED DEFAULT NULL,
  `actual_time_minutes` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_invoice` tinyint(1) GENERATED ALWAYS AS (`invoiced_at` is not null) STORED
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `quotes`
--

INSERT INTO `quotes` (`id`, `client_id`, `bike_description`, `reception_comment`, `remarks`, `email_note`, `metier`, `reference`, `status`, `invoiced_at`, `paid_at`, `is_archived`, `valid_until`, `discount_type`, `discount_value`, `total_ht`, `total_tva`, `total_ttc`, `margin_total_ht`, `total_estimated_time_minutes`, `actual_time_minutes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'City 40 blanc + panier', 'vélo de Jean Marie Vidament. Potentiel vente d\'occasion, ceci est le devis de sa révision', NULL, NULL, 'atelier', '20260130-2', 'invoiced', '2026-01-30 16:10:30', '2026-01-30 16:10:30', 0, '2026-02-08', NULL, NULL, 99.97, 20.02, 120.00, 96.04, NULL, NULL, '2026-01-24 15:05:10', '2026-01-30 16:10:30', NULL),
(2, 2, 'vélo enfant noir/orange', 'demande de devis pour révision', NULL, NULL, 'atelier', '20260328-2', 'invoiced', '2026-03-28 15:12:25', '2026-03-28 15:12:25', 0, '2026-02-10', NULL, NULL, 85.82, 17.18, 103.00, 69.42, NULL, NULL, '2026-01-26 09:45:46', '2026-03-28 15:12:25', NULL),
(3, 2, 'Nakamura alu frame', 'devis révision', NULL, NULL, 'atelier', '20260328-1', 'invoiced', '2026-03-28 15:12:12', '2026-03-28 15:12:12', 0, '2026-02-10', NULL, NULL, 104.97, 21.01, 125.99, 99.68, NULL, NULL, '2026-01-26 17:20:40', '2026-03-28 15:12:12', NULL),
(4, 2, 'Scott noir / vert', 'devis révision\n\nAttention, filetage pédalier HS, extraction complexe.', NULL, NULL, 'atelier', '20260126-3', 'done', NULL, NULL, 0, '2026-02-10', NULL, NULL, 158.71, 31.77, 190.49, 98.89, NULL, NULL, '2026-01-26 17:23:06', '2026-06-23 14:51:25', NULL),
(5, 3, 'MBK ville gris', '\'frein hs\' + diag révision', NULL, NULL, 'atelier', '20260130-1', 'invoiced', '2026-01-30 08:45:37', '2026-01-30 08:45:37', 0, '2026-02-11', NULL, NULL, 129.38, 25.90, 155.29, 113.28, NULL, NULL, '2026-01-27 14:58:46', '2026-01-30 08:45:37', NULL),
(6, 10, 'roues de vélo', 'achat de piece', NULL, NULL, 'atelier', '20260128-7', 'invoiced', '2026-01-28 08:06:04', '2026-01-28 08:06:04', 0, '2026-02-11', NULL, NULL, 146.50, 29.30, 175.80, 96.30, NULL, NULL, '2026-01-27 15:51:01', '2026-01-28 08:06:04', NULL),
(7, 9, 'Velo de ville elec jaune', 'freinage HS', NULL, NULL, 'atelier', '20260128-6', 'invoiced', '2026-01-28 08:05:56', '2026-01-28 08:05:56', 0, '2026-02-11', NULL, NULL, 24.65, 4.94, 29.60, 19.89, NULL, NULL, '2026-01-27 15:53:37', '2026-01-28 08:05:56', NULL),
(8, 8, 'VTT decathlon', 'freinage hs', NULL, NULL, 'atelier', '20260128-5', 'invoiced', '2026-01-28 08:05:47', '2026-01-28 08:05:47', 0, '2026-02-11', NULL, NULL, 41.66, 8.34, 50.00, 19.16, NULL, NULL, '2026-01-27 15:59:16', '2026-01-28 08:05:47', NULL),
(9, 7, 'O2 feel pliant', 'révision', NULL, NULL, 'atelier', '20260128-4', 'invoiced', '2026-01-28 08:05:39', '2026-01-28 08:05:39', 0, '2026-02-11', NULL, NULL, 45.40, 9.10, 54.50, 30.30, NULL, NULL, '2026-01-27 16:05:23', '2026-01-28 08:05:39', NULL),
(10, 6, 'VTC Nakamura', 'le vélo à fait un séjour dans le port de Paimpol.\nune révision est à faire', NULL, NULL, 'atelier', '20260128-3', 'invoiced', '2026-01-28 08:05:30', '2026-01-28 08:05:30', 0, '2026-02-11', NULL, NULL, 65.81, 13.18, 79.00, 53.98, NULL, NULL, '2026-01-27 16:33:17', '2026-01-28 08:05:30', NULL),
(11, 5, 'ebike pliant beige', 'demande de revision', NULL, NULL, 'atelier', '20260128-1', 'invoiced', '2026-01-28 08:04:43', '2026-01-28 08:04:43', 0, '2026-02-11', NULL, NULL, 34.15, 6.84, 41.00, 34.15, NULL, NULL, '2026-01-27 16:34:34', '2026-01-28 08:04:43', NULL),
(12, 4, 'fat bike - pliant -', 'installation de freins hydraulique - pièces apportées par le client\ndemande de remplacement de pneus par un modèle + urbain', NULL, NULL, 'atelier', '20260128-2', 'invoiced', '2026-01-28 08:05:17', '2026-01-28 08:05:17', 0, '2026-02-11', NULL, NULL, 146.66, 29.34, 176.00, 132.66, NULL, NULL, '2026-01-27 16:36:27', '2026-01-28 08:05:17', NULL),
(13, 11, 'giant vert', 'diag pour révision', NULL, NULL, 'atelier', '20260219-2', 'invoiced', '2026-02-19 10:37:33', '2026-02-19 10:37:33', 0, '2026-02-13', NULL, NULL, 140.31, 28.07, 168.39, 109.98, NULL, NULL, '2026-01-29 13:27:39', '2026-02-19 10:37:33', NULL),
(14, 12, 'VTT gris titane', 'manette de sélection de vitesse gauche et droit hs', NULL, NULL, 'atelier', '20260204-1', 'invoiced', '2026-02-04 10:43:54', '2026-02-04 10:43:54', 0, '2026-02-13', NULL, NULL, 87.57, 17.53, 105.10, 56.39, NULL, NULL, '2026-01-29 14:37:56', '2026-02-04 10:43:54', NULL),
(15, 13, 'vélo pliant rouge', 'diagnostique freinage + devis révision\n\nchaine usure cran 1 : remplacement chaine sans remplacement roue libre/ plateau', NULL, NULL, 'atelier', '20260212-1', 'invoiced', '2026-02-12 10:27:27', '2026-02-12 10:27:27', 0, '2026-02-14', NULL, NULL, 100.00, 20.00, 120.00, 96.06, NULL, NULL, '2026-01-30 09:21:18', '2026-02-12 10:27:27', NULL),
(16, 13, 'vélo pliant bleu', 'devis révision :\n\ntout va bien sauf le réglage du frein AV', NULL, NULL, 'atelier', '20260212-2', 'invoiced', '2026-02-12 10:27:41', '2026-02-12 10:27:41', 0, '2026-02-14', NULL, NULL, 10.83, 2.17, 13.00, 10.83, NULL, NULL, '2026-01-30 09:27:34', '2026-02-12 10:27:41', NULL),
(17, 14, 'VTT LaPierre', 'Demande de devis pour une conversion Virvolt 750', NULL, NULL, 'atelier', '20260219-1', 'invoiced', '2026-02-19 09:25:31', '2026-02-19 09:25:31', 0, '2026-02-18', NULL, NULL, 529.16, 105.84, 635.00, 153.01, 45, NULL, '2026-02-03 09:00:13', '2026-02-19 09:25:31', NULL),
(18, 15, 'VAE TREK Noir nexus', 'Le client se plaint d\'instabilité sur la roue arrière.\nNous avons trouvé que le pneus était déchiré sur le coté.', 'PREVOIR : -Plaquettes AR 25% usure\n                         -Chaine usée', NULL, 'atelier', '20260204-2', 'invoiced', '2026-02-04 16:33:03', '2026-02-04 16:33:03', 0, '2026-02-19', NULL, NULL, 52.48, 10.51, 63.00, 33.48, 30, 120, '2026-02-04 09:47:55', '2026-02-04 16:33:03', NULL),
(19, 16, 'VAE NCM T3S 2025', 'Achat', NULL, NULL, 'atelier', '20260204-3', 'invoiced', '2026-02-04 16:48:40', '2026-02-04 16:48:40', 0, '2026-02-19', NULL, NULL, 815.83, 163.16, 979.00, 207.50, NULL, NULL, '2026-02-04 16:48:23', '2026-02-04 16:48:40', NULL),
(20, 17, 'VTT VAE', 'remplacement du garde boue et réglage derailleur', NULL, NULL, 'atelier', '20260204-4', 'invoiced', '2026-02-04 17:06:20', '2026-02-04 17:06:20', 0, '2026-02-19', NULL, NULL, 52.08, 10.42, 62.50, 30.13, 45, 45, '2026-02-04 17:05:46', '2026-02-04 17:06:20', NULL),
(21, 18, 'Vae pliant rouge EOVOLT', 'diag revision freins chaine devoilage', NULL, NULL, 'atelier', '20260205-1', 'reception', NULL, NULL, 0, '2026-02-20', NULL, NULL, 100.00, 20.00, 120.00, 100.00, NULL, NULL, '2026-02-05 08:19:32', '2026-02-09 08:22:13', '2026-02-09 08:22:13'),
(22, 19, 'vae voyage', 'achat cable de derailleur', 'payé esp le 06/02/26', NULL, 'atelier', '20260210-1', 'invoiced', '2026-02-10 08:15:08', '2026-02-10 08:15:08', 0, '2026-02-21', NULL, NULL, 2.91, 0.58, 3.49, 2.91, NULL, NULL, '2026-02-06 13:27:45', '2026-02-10 08:15:08', NULL),
(23, 20, 'nakamura vae noir cadre bas', 'devis frein ar', NULL, NULL, 'atelier', '20260207-1', 'invoiced', '2026-02-07 15:41:43', '2026-02-07 15:41:43', 0, '2026-02-22', NULL, NULL, 22.49, 4.50, 27.00, 22.49, NULL, NULL, '2026-02-07 15:40:45', '2026-02-07 15:41:43', NULL),
(24, 21, 'VAE nakamura city blanc', 'demande de devis pour une révision.', 'Le dévoilage n\'est pas trop important mais il est nécessaire pour régler correctement les patins AR.\nLa chaîne présente un début d\'usure et sera à contrôler dans l\'été.\nLa roue arrière présente un jeu dans son moyeu, nous profiterons du dévoilage (roue déposée) pour le régler (offert).\n\nNous avons constaté que les vitesses ne passaient pas correctement, d\'où la proposition de réglage.', NULL, 'atelier', '20260226-1', 'invoiced', '2026-02-26 11:51:26', '2026-02-26 11:51:26', 0, '2026-02-24', NULL, NULL, 82.06, 16.44, 98.50, 72.89, 105, NULL, '2026-02-09 08:56:49', '2026-02-26 11:51:27', NULL),
(25, 22, 'decathlon ville bleu', 'revision', NULL, NULL, 'atelier', '20260210-2', 'invoiced', '2026-02-10 13:17:19', '2026-02-10 13:17:19', 0, '2026-02-25', NULL, NULL, 62.47, 12.53, 75.00, 48.57, NULL, NULL, '2026-02-10 09:04:30', '2026-02-10 13:17:19', NULL),
(26, 23, 'gitane cadre haut bleu', 'réglage frein AR + diag/révision', NULL, NULL, 'atelier', '20260217-1', 'invoiced', '2026-02-17 16:24:58', '2026-02-17 16:24:58', 0, '2026-02-27', NULL, NULL, 52.91, 10.59, 63.50, 47.69, 75, 75, '2026-02-12 10:39:56', '2026-02-17 16:24:58', NULL),
(27, 24, 'Ville de ville électrique Neomouv', 'Achat d\'un VAE de la marque Neomouv', 'voici les numéros à renseigner lors de l\'enregistrement de la garantie sur le site Neomouv.com\nNARIA 26\' T43 S/B BLEU : NM2025/1003566\nNM2 PORTE-BAGAGES 468WH 36V S BMS : FG31J179690501362', NULL, 'atelier', '20260212-3', 'invoiced', '2026-02-12 11:16:49', '2026-02-12 11:16:49', 0, '2026-02-27', NULL, NULL, 707.50, 141.50, 849.00, 268.74, NULL, NULL, '2026-02-12 11:15:35', '2026-02-12 11:16:49', NULL),
(28, 28, 'roue AR', 'Roue libre à changer', NULL, NULL, 'atelier', '20260306-5', 'invoiced', '2026-03-06 15:40:00', '2026-03-06 15:40:00', 0, '2026-03-04', NULL, NULL, 15.00, 3.00, 18.00, 8.00, NULL, NULL, '2026-02-17 16:56:19', '2026-03-06 15:40:00', NULL),
(29, 29, 'moustache marron cadre bas', 'demande client : \n-plaquettes freins AV AR\n-réglage dérailleur AR\n-devis diag si besoin', 'Pneus a prévoir, effilochement au niveau des talons.', NULL, 'atelier', '20260225-1', 'invoiced', '2026-02-25 09:20:28', '2026-02-25 09:20:28', 0, '2026-03-05', NULL, NULL, 89.15, 17.85, 107.00, 67.27, 60, NULL, '2026-02-18 08:56:11', '2026-02-25 09:20:29', NULL),
(30, 31, 'velo course enfant orange Trek', 'réglage dérailleur AR + diag/devis', 'Il faudra prévoir le remplacement de la gaine et du câble de frein AR pour une meilleur fluidité du levier, mais il faudra prévoir un remplacement de la guidoline également.\n\nNous pourrons voir ça dans un second temps si vous le souhaitez.', NULL, 'atelier', '20260223-1', 'invoiced', '2026-02-23 08:35:29', '2026-02-23 08:35:29', 0, '2026-03-05', NULL, NULL, 34.15, 6.84, 41.00, 34.15, 45, 45, '2026-02-18 14:49:48', '2026-02-23 08:35:29', NULL),
(31, 32, 'MBK orange', 'devis révision', NULL, NULL, 'atelier', '20260307-2', 'invoiced', '2026-03-07 15:45:38', '2026-03-07 15:45:38', 0, '2026-03-05', NULL, NULL, 115.61, 23.17, 138.78, 74.20, 90, NULL, '2026-02-18 15:31:40', '2026-03-07 15:45:38', NULL),
(32, 33, 'Beachin Noir ville ancien', 'diag revision devis par mail', NULL, NULL, 'atelier', '20260226-2', 'invoiced', '2026-02-26 13:18:48', '2026-02-26 13:18:48', 0, '2026-03-06', NULL, NULL, 48.31, 9.68, 58.00, 45.22, 75, 90, '2026-02-19 10:42:23', '2026-02-26 13:18:48', NULL),
(33, 34, 'nakamura enfant rose/noir', 'diag', NULL, NULL, 'atelier', '20260219-4', 'invoiced', '2026-02-19 13:56:39', '2026-02-19 13:56:39', 0, '2026-03-06', NULL, NULL, 12.50, 2.50, 15.00, 12.50, 15, NULL, '2026-02-19 13:53:51', '2026-02-19 13:56:39', NULL),
(34, 35, 'nakamura enfant vert/noir', 'diag', NULL, NULL, 'atelier', '20260219-3', 'invoiced', '2026-02-19 13:56:21', '2026-02-19 13:56:21', 0, '2026-03-06', NULL, NULL, 72.39, 14.49, 86.89, 72.39, NULL, NULL, '2026-02-19 13:56:03', '2026-02-19 13:56:21', NULL),
(35, 36, 'VAE Scott cadre bas', 'diag révision', 'Choix de cassette Sunrace \"acier\" pour la solidité sur un VAE moteur central.\nChaine renforcée Shimano.\nPlateau 38 spécifique au moteur Bosch active line.', NULL, 'atelier', '20260226-3', 'invoiced', '2026-02-26 15:25:51', '2026-02-26 15:25:51', 0, '2026-03-06', NULL, NULL, 155.38, 31.11, 186.49, 95.38, 90, NULL, '2026-02-19 14:10:08', '2026-02-26 15:25:51', NULL),
(36, 37, 'Nakamu summit 105', 'dérailleur abimé\nfrein à vérifier.\nFaire le tour du vélo', 'Nous avons constaté un voile sur les deux roues.\nLe dérailleur AV semblait grippé mais nous avons passé pas mal de temps à essayer de le dégripper sans résultat. nous préconisons son remplacement.\n\nComme nous l\'avions vu ensemble : \nDérailleur AR + plaquette AV', NULL, 'atelier', '20260304-1', 'invoiced', '2026-03-04 08:14:49', '2026-03-04 08:14:49', 0, '2026-03-07', NULL, NULL, 110.45, 22.11, 132.56, 85.42, 90, 90, '2026-02-20 08:36:23', '2026-03-04 08:14:49', NULL),
(37, 44, 'Trek gravel', 'deraillement intempestif', NULL, NULL, 'atelier', '20260224-1', 'invoiced', '2026-02-24 16:26:43', '2026-02-24 16:26:43', 0, '2026-03-11', NULL, NULL, 10.83, 2.17, 13.00, 10.83, NULL, 15, '2026-02-24 16:26:34', '2026-02-24 16:26:43', NULL),
(38, 45, 'velo enfant rose/blanc', 'diag frein AV et AR + diag', NULL, NULL, 'atelier', '20260306-4', 'invoiced', '2026-03-06 15:39:50', '2026-03-06 15:39:50', 0, '2026-03-12', NULL, NULL, 46.65, 9.35, 56.00, 46.65, 60, NULL, '2026-02-25 14:02:36', '2026-03-06 15:39:50', NULL),
(39, 46, 'Nakamura gris', 'révision des freins + observation\nvoir si panier compatible', 'chaine à prévoir - à re mesurer pendant dans le printemps.\nbudget : 30euros.', NULL, 'atelier', '20260226-5', 'invoiced', '2026-02-26 17:28:41', '2026-02-26 17:28:41', 0, '2026-03-12', NULL, NULL, 99.97, 20.03, 120.00, 84.21, 90, NULL, '2026-02-25 15:09:13', '2026-02-26 17:28:41', NULL),
(40, 26, 'Moustache 27', 'diag réglage dérailleur\nexaminer le freinage AR\n\nglobalement, un diag révision', 'pour l\'étrier AR, le problème vient d\'un mauvais centrage de l\'étrier qui a usé prématurément la plaquette coté intérieur.\n\nPour le freinage AV, les plaquettes sont en très bon état mais le liquide à besoin d\'être renouvelé car la poignée est inconstante (par rapport à la pression de freinage)\n\nle Spider gen 3 est une pièce du plateau. je vous enverrai un visuel si vous le souhaitez.', NULL, 'atelier', '20260313-1', 'invoiced', '2026-03-13 09:25:44', '2026-03-13 09:25:44', 0, '2026-03-13', NULL, NULL, 159.01, 31.81, 190.82, 64.97, 105, NULL, '2026-02-26 15:28:05', '2026-03-13 09:25:44', NULL),
(41, 47, 'Chanteney', 'diag freinage', NULL, NULL, 'atelier', '20260226-4', 'invoiced', '2026-02-26 16:14:51', '2026-02-26 16:14:51', 0, '2026-03-13', NULL, NULL, 62.90, 12.59, 75.49, 35.59, 30, 45, '2026-02-26 16:14:44', '2026-02-26 16:14:51', NULL),
(42, 48, 'Solar', 'Diag révision + option lumière', NULL, NULL, 'atelier', '20260314-1', 'invoiced', '2026-03-14 09:03:54', '2026-03-14 09:03:54', 0, '2026-03-14', NULL, NULL, 185.75, 37.15, 222.90, 117.62, 105, NULL, '2026-02-27 16:47:15', '2026-03-14 09:03:54', NULL),
(43, 53, 'achat de casqye', 'le client est venu choisir un casque dans notre catalogue', NULL, NULL, 'atelier', '20260228-1', 'invoiced', '2026-02-28 13:25:06', '2026-02-28 13:25:06', 0, '2026-03-15', NULL, NULL, 44.17, 8.83, 53.00, 17.67, NULL, NULL, '2026-02-28 13:25:02', '2026-02-28 13:25:06', NULL),
(44, 54, 'bulls wildcross', 'devis reglage derailleur ar + diag', 'commande derailleur AV a prevoir', NULL, 'atelier', '20260228-2', 'invoiced', '2026-02-28 17:03:51', '2026-02-28 17:03:51', 0, '2026-03-15', NULL, NULL, 47.50, 9.50, 57.00, 47.50, NULL, NULL, '2026-02-28 17:03:30', '2026-02-28 17:03:51', NULL),
(45, 55, 'Scott beige vae cadre bas', 'achat sacoches + patins av ar', 'sacoches occasion mars 2025', NULL, 'atelier', '20260228-3', 'invoiced', '2026-02-28 17:09:31', '2026-02-28 17:09:31', 0, '2026-03-15', NULL, NULL, 59.17, 11.83, 71.00, -8.73, NULL, NULL, '2026-02-28 17:09:15', '2026-02-28 17:09:31', NULL),
(46, 20, 'vae noir cadre bas', 'pneu ar 26x1.75', NULL, NULL, 'atelier', '20260228-4', 'invoiced', '2026-02-28 18:06:20', '2026-02-28 18:06:20', 0, '2026-03-15', NULL, NULL, 15.00, 3.00, 18.00, 8.90, NULL, NULL, '2026-02-28 18:06:00', '2026-02-28 18:06:20', NULL),
(47, 56, 'Scott noir gris', 'poignée molle au frein av\ndiag revision\nvoir lampe AR', 'l\'étrier AV étant décentré, la course de plaquette était longue (donc la poignée aussi)\n\npar contre les plaquettes manquent d\'efficacité et effectivement, il y a des traces de gras (noir) sur le disque. je préconise plutôt le remplacement des plaquettes que la purge.\n\nPar contre mauvaise nouvelle, la transmission présente une usure très importante.\nTant que ça roule, c\'est ok mais quand vous commencerez à expérimenter des sauts de chaines / déraillements intempestifs, il faudra tout changer. je vous les mets dans le devis pour information et si vous souhaitez le faire.', NULL, 'atelier', '20260306-2', 'invoiced', '2026-03-06 15:38:56', '2026-03-06 15:38:56', 0, '2026-03-17', NULL, NULL, 160.82, 32.17, 192.99, 90.13, 60, NULL, '2026-03-02 11:54:50', '2026-03-06 15:38:56', NULL),
(48, 57, 'fat bike rouge', 'roue AR bloquée par le porte bagage', NULL, NULL, 'atelier', '20260306-3', 'invoiced', '2026-03-06 15:39:33', '2026-03-06 15:39:33', 0, '2026-03-18', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-03-03 10:32:58', '2026-03-06 15:39:33', NULL),
(49, 59, 'Ref Bike', 'Voilage roue avant (frottement garde boue)', NULL, NULL, 'atelier', '20260304-2', 'invoiced', '2026-03-04 14:26:57', '2026-03-04 14:26:57', 0, '2026-03-19', NULL, NULL, 12.50, 2.50, 15.00, 12.50, 15, 15, '2026-03-04 14:26:51', '2026-03-04 14:26:57', NULL),
(50, 60, 'Nakamura blanc', 'derailleur + pate de derailleur\ndiag revision', 'La mesure de chaine indique qu\'il faut la changer.\nles étriers de freins sont beaucoup plus proche d\'une plaquette que de l\'autre à l\'avant comme à l\'arrière.\n\nnous offrirons le réglage.\n\nPrévoir un temps de livraison des pièces après validation du devis.', NULL, 'atelier', '20260307-3', 'invoiced', '2026-03-07 16:39:51', '2026-03-07 16:39:51', 0, '2026-03-20', NULL, NULL, 72.50, 14.50, 87.00, 42.47, 30, NULL, '2026-03-05 09:03:21', '2026-03-07 16:39:51', NULL),
(51, 61, 'Pneus et chambre HS', 'achat de fourniture', NULL, NULL, 'atelier', '20260305-1', 'invoiced', '2026-03-05 10:34:17', '2026-03-05 10:34:17', 0, '2026-03-20', NULL, NULL, 28.82, 5.77, 34.59, 9.47, NULL, NULL, '2026-03-05 10:34:10', '2026-03-05 10:34:17', NULL),
(52, 64, 'Metayer vert', 'remplacement d\'un pneu sur le vélo homme,\nremplacement de l\'ecran sur le vélo femme', NULL, NULL, 'atelier', '20260306-6', 'invoiced', '2026-03-06 15:56:10', '2026-03-06 15:56:10', 0, '2026-03-21', NULL, NULL, 88.83, 17.77, 106.60, 10.93, NULL, NULL, '2026-03-06 09:15:18', '2026-03-06 15:56:10', NULL),
(53, 65, 'Roue de VTT', 'remplacement roue', NULL, NULL, 'atelier', '20260306-1', 'invoiced', '2026-03-06 10:48:38', '2026-03-06 10:48:38', 0, '2026-03-21', NULL, NULL, 144.00, 28.79, 172.79, 31.40, NULL, NULL, '2026-03-06 09:17:15', '2026-03-06 10:48:38', NULL),
(54, 66, 'achat sac decath', 'achat sac decath, le monsieur venait pour un colis.', NULL, NULL, 'atelier', '20260306-7', 'invoiced', '2026-03-06 16:57:27', '2026-03-06 16:57:27', 0, '2026-03-21', NULL, NULL, 62.49, 12.50, 74.99, 15.62, NULL, NULL, '2026-03-06 16:57:20', '2026-03-06 16:57:27', NULL),
(55, 67, 'VTT giant', 'bruit freinage, dérailleur bloqué', NULL, NULL, 'atelier', '20260307-1', 'invoiced', '2026-03-07 08:12:11', '2026-03-07 08:12:11', 0, '2026-03-21', NULL, NULL, 53.34, 10.66, 64.00, 40.18, NULL, NULL, '2026-03-06 16:59:35', '2026-03-07 08:12:11', NULL),
(56, 6, 'Elops bleu', 'remontage roue motorisé + controle', NULL, NULL, 'atelier', '20260317-2', 'invoiced', '2026-03-17 14:03:06', '2026-03-17 14:03:06', 0, '2026-03-22', NULL, NULL, 87.50, 17.50, 105.00, 84.25, 60, NULL, '2026-03-07 10:58:26', '2026-03-17 14:03:06', NULL),
(57, 68, 'achat materiel', 'recherche casque et antivol', NULL, NULL, 'atelier', '20260307-4', 'invoiced', '2026-03-07 17:15:44', '2026-03-07 17:15:44', 0, '2026-03-22', NULL, NULL, 69.17, 13.83, 83.00, 27.67, NULL, NULL, '2026-03-07 17:15:38', '2026-03-07 17:15:44', NULL),
(58, 70, 'Raleigh jaune et noir', 'roue AR HS', NULL, NULL, 'atelier', '20260320-2', 'invoiced', '2026-03-20 13:53:43', '2026-03-20 13:53:43', 0, '2026-03-24', NULL, NULL, 46.58, 9.32, 55.90, 25.08, 15, NULL, '2026-03-09 16:26:18', '2026-03-20 13:53:43', NULL),
(59, 73, 'Vélo Yose Power', 'demande de remplacement de batterie pour une plus grande autonomie', 'https://yosepower.com/products/36v-battery-with-taillight-and-rear-carrier?currency=EUR&utm_source=google&utm_medium=cpc&utm_campaign=Google+Shopping&stkn=626aad7f2a4f&gad_source=1&gad_campaignid=22297071745&gbraid=0AAAAACp0lPXQa0jXbLO5ZQ83k3uh02RnG&gclid=Cj0KCQiAwYrNBhDcARIsAGo3u30xLP8QDt6kIyxwZ1MYJIVLXYuRKN8oRI-xpmkEG20gDFw_W2wtkPQaAoEFEALw_wcB&variant=41800921284743\n\nLe problème de notre tarif, c\'est la TVA. nous ne pouvons pas appliquer le tarif YosePower car c\'est notre prix d\'achat sans TVA déductible et le client souhaite que l\'achetions pour lui', NULL, 'atelier', '20260526-1', 'invoiced', '2026-05-26 10:29:39', '2026-05-26 10:29:39', 0, '2026-03-24', NULL, NULL, 353.34, 70.66, 424.00, 93.34, 60, NULL, '2026-03-09 17:05:51', '2026-05-26 10:29:39', NULL),
(60, 74, 'Toplife', 'fuite lente pneu AR', NULL, NULL, 'atelier', '20260312-1', 'invoiced', '2026-03-12 09:35:33', '2026-03-12 09:35:33', 0, '2026-03-25', NULL, NULL, 14.92, 2.98, 17.90, 13.37, 15, 15, '2026-03-10 15:07:59', '2026-03-12 09:35:33', NULL),
(61, 76, 'Kalkhoff', 'diag révision', NULL, NULL, 'atelier', '20260324-1', 'invoiced', '2026-03-24 07:15:44', '2026-03-24 07:15:44', 0, '2026-03-26', NULL, NULL, 297.94, 59.58, 357.52, 185.09, 120, NULL, '2026-03-11 08:56:44', '2026-03-24 07:15:44', NULL),
(62, 77, 'vélo route peugeot', 'centrage etrier et voilage AR\nregarder si les vitesses passent bien', NULL, NULL, 'atelier', '20260317-1', 'invoiced', '2026-03-17 08:31:50', '2026-03-17 08:31:50', 0, '2026-03-26', NULL, NULL, 21.67, 4.33, 26.00, 21.67, 15, NULL, '2026-03-11 09:49:50', '2026-03-17 08:31:50', NULL),
(63, 6, 'nakamura doré', 'crevaison avant\nfreinage a regler', NULL, NULL, 'atelier', '20260311-3', 'invoiced', '2026-03-11 21:03:57', '2026-03-11 21:03:57', 0, '2026-03-26', NULL, NULL, 14.92, 2.98, 17.90, 13.35, 30, NULL, '2026-03-11 13:22:43', '2026-03-11 21:03:57', NULL),
(64, 78, 'nakamura vert enfant', 'revision devis', NULL, NULL, 'atelier', '20260409-5', 'invoiced', '2026-04-09 17:00:34', '2026-04-09 17:00:34', 0, '2026-03-26', NULL, NULL, 125.01, 24.99, 150.00, 102.83, NULL, NULL, '2026-03-11 13:33:13', '2026-04-09 17:00:34', NULL),
(65, 79, 'Look - route', 'devis remis en état', 'Nous offrons les pédales plates.\nIl ne nous sera possible de tester l\'efficacité du freinage que lorsque le vélo sera réellement roulant.', NULL, 'atelier', '20260530-3', 'invoiced', '2026-05-30 14:03:37', '2026-05-30 14:03:37', 0, '2026-03-26', NULL, NULL, 251.20, 50.24, 301.44, 199.41, 165, NULL, '2026-03-11 15:43:18', '2026-05-30 14:03:37', NULL),
(66, 80, 'BMC gris', 'probleme de transmission', NULL, NULL, 'atelier', '20260314-2', 'invoiced', '2026-03-14 09:56:02', '2026-03-14 09:56:02', 0, '2026-03-26', NULL, NULL, 29.17, 5.83, 35.00, 29.17, NULL, NULL, '2026-03-11 16:26:46', '2026-03-14 09:56:02', NULL),
(67, 81, 'VTT SUNN', 'remplacement transmission', NULL, NULL, 'atelier', '20260311-1', 'invoiced', '2026-03-11 20:38:17', '2026-03-11 20:38:17', 0, '2026-03-26', NULL, NULL, 238.25, 47.65, 285.90, 93.12, NULL, NULL, '2026-03-11 20:37:49', '2026-03-11 20:38:17', NULL),
(68, 50, 'VTT gris GIANT', 'pneus AR HS', NULL, NULL, 'atelier', '20260311-2', 'invoiced', '2026-03-11 20:39:16', '2026-03-11 20:39:16', 0, '2026-03-26', NULL, NULL, 8.33, 1.67, 10.00, 8.33, NULL, NULL, '2026-03-11 20:39:10', '2026-03-11 20:39:16', NULL),
(69, 82, 'velo cargo \"tente\" vert', 'plaquettes AV AR', NULL, NULL, 'atelier', '20260312-2', 'invoiced', '2026-03-12 15:32:53', '2026-03-12 15:32:53', 0, '2026-03-27', NULL, NULL, 34.17, 6.83, 41.00, 34.17, NULL, 30, '2026-03-12 08:27:30', '2026-03-12 15:32:53', NULL),
(70, 70, 'Raleigh', 'Devis suite à intervention', NULL, NULL, 'atelier', '20260320-1', 'invoiced', '2026-03-20 13:53:33', '2026-03-20 13:53:33', 0, '2026-03-27', NULL, NULL, 100.42, 20.08, 120.50, 69.26, 75, NULL, '2026-03-12 15:02:20', '2026-03-20 13:53:33', NULL),
(71, 83, 'Kalkhoff gris', 'diag revision\nl un des deux a un pneus qui se degonfle tout le temps', NULL, NULL, 'atelier', '20260409-3', 'invoiced', '2026-04-09 16:05:07', '2026-04-09 16:05:07', 0, '2026-03-27', NULL, NULL, 262.73, 52.54, 315.27, 158.43, 105, NULL, '2026-03-12 15:41:01', '2026-04-09 16:05:07', NULL),
(72, 84, 'triporteur', 'venu de brehat\nbientot a l\'atelier', NULL, NULL, 'atelier', '20260529-7', 'invoiced', '2026-05-29 21:05:31', '2026-05-29 21:05:31', 0, '2026-03-27', NULL, NULL, 172.10, 34.42, 206.52, 81.71, NULL, NULL, '2026-03-12 15:43:29', '2026-05-29 21:05:31', NULL),
(73, 85, 'Top life bleu foncé cadre bas VAE', 'réglage freins et patins', NULL, NULL, 'atelier', '20260314-3', 'invoiced', '2026-03-14 14:07:56', '2026-03-14 14:07:56', 0, '2026-03-28', NULL, NULL, 39.17, 7.83, 47.00, 39.17, 45, 30, '2026-03-13 13:15:58', '2026-03-14 14:07:56', NULL),
(74, 80, 'kit de survie', 'demande de kit outils pour sortie vélo', NULL, NULL, 'atelier', '20260314-1', 'pending_validation', NULL, NULL, 0, '2026-03-29', NULL, NULL, 101.79, 20.36, 122.15, 48.74, NULL, NULL, '2026-03-14 13:30:51', '2026-04-22 20:30:00', NULL),
(75, 86, 'Click n collect Upway', 'Réceptions et retour', NULL, NULL, 'atelier', '20260316-1', 'invoiced', '2026-03-16 10:13:43', '2026-03-16 10:13:43', 0, '2026-03-31', NULL, NULL, 95.00, 19.00, 114.00, 95.00, NULL, NULL, '2026-03-16 10:13:27', '2026-03-16 10:13:43', NULL),
(76, 90, 'lapierre course blanc bleu', 'devis diag', NULL, NULL, 'atelier', '20260416-1', 'invoiced', '2026-04-16 09:16:24', '2026-04-16 09:16:24', 0, '2026-04-01', NULL, NULL, 205.34, 41.06, 246.40, 89.29, NULL, NULL, '2026-03-17 15:56:45', '2026-04-16 09:16:25', NULL),
(77, 91, 'Nakamura VTC bleu', 'crevaison arriere', 'prévoir dévoilage AR', NULL, 'atelier', '20260318-1', 'invoiced', '2026-03-18 15:18:51', '2026-03-18 15:18:51', 0, '2026-04-01', NULL, NULL, 35.34, 7.06, 42.40, 32.04, 30, NULL, '2026-03-17 16:35:57', '2026-03-18 15:18:52', NULL),
(78, 93, 'Torpado blanc', 'diag', 'on va profiter du montage des pneus pour ajuster le serrage des roulements de roue car il y a un peu de jeu', NULL, 'atelier', '20260330-1', 'invoiced', '2026-03-30 14:07:07', '2026-03-30 14:07:07', 0, '2026-04-02', NULL, NULL, 108.99, 21.81, 130.80, 76.47, 45, NULL, '2026-03-18 14:54:12', '2026-03-30 14:07:07', NULL),
(79, 94, 'O2 feel', 'freinage AV et AR\nLampe AV\nserrage panier\ncaoutchouc béquille', 'On vous à trouvé une petite cale de béquille sur un vélo HS, donc de ce côté là : bonne nouvelle :)\n\npour la lampe je vous ai trouvé un modèle. je n\'aurai plus qu\'a refaire les soudure et à l\'installer.', NULL, 'atelier', '20260413-1', 'invoiced', '2026-04-13 14:02:42', '2026-04-13 14:02:42', 0, '2026-04-02', NULL, NULL, 100.98, 20.19, 121.17, 74.73, 90, NULL, '2026-03-18 15:26:20', '2026-04-13 14:02:42', NULL),
(80, 96, 'Motobecane blanc', 'freinage + bruit pédalier', NULL, NULL, 'atelier', '20260323-1', 'invoiced', '2026-03-23 14:59:46', '2026-03-23 14:59:46', 0, '2026-04-03', NULL, NULL, 46.67, 9.33, 56.00, 40.77, NULL, NULL, '2026-03-19 17:38:31', '2026-03-23 14:59:46', NULL),
(81, 97, 'VAE NCM Milano Max', 'Devis pour l\'achat d\'un VAE', NULL, NULL, 'atelier', '20260321-1', 'pending_validation', NULL, NULL, 0, '2026-06-20', NULL, NULL, 957.50, 191.50, 1149.00, 277.50, NULL, NULL, '2026-03-21 09:03:57', '2026-05-21 17:00:04', '2026-05-21 17:00:04'),
(82, 99, 'astra bordeau gris cadre bas', 'rayons AR complètement desserrés', 'a prévoir :\n- gaine + câbles frein AR 14€\n- réglage frein AV 13€\n- patins freins AV 5.50€ + 10€\n- pneu AR 700 x 35c\n\n- chaine + trans HS dans prochain devis', NULL, 'atelier', '20260321-1', 'invoiced', '2026-03-21 14:19:45', '2026-03-21 14:19:45', 0, '2026-04-05', NULL, NULL, 25.00, 5.00, 30.00, 25.00, 30, NULL, '2026-03-21 10:22:20', '2026-03-21 14:19:45', NULL),
(83, 101, 'Trek emonda rouge noir carbone', 'rayon cassé', NULL, NULL, 'atelier', '20260321-2', 'invoiced', '2026-03-21 15:25:55', '2026-03-21 15:25:55', 0, '2026-04-05', NULL, NULL, 20.83, 4.17, 25.00, 20.83, NULL, NULL, '2026-03-21 14:49:37', '2026-03-21 15:25:55', NULL),
(84, 103, 'rockrider rouge enfant', 'devis diag', NULL, NULL, 'atelier', '20260324-1', 'pending_validation', NULL, NULL, 1, '2026-04-08', NULL, NULL, 170.81, 34.17, 204.98, 145.59, 195, NULL, '2026-03-24 09:06:33', '2026-05-31 15:49:06', NULL),
(85, 103, 'Top Life blanc/rouge enfant', 'diag devis', NULL, NULL, 'atelier', '20260324-2', 'pending_validation', NULL, NULL, 1, '2026-04-08', NULL, NULL, 126.23, 25.25, 151.48, 116.01, 120, NULL, '2026-03-24 09:15:30', '2026-05-31 15:49:04', NULL),
(86, 106, 'Sheng mi lo VTT', 'capteur pédalage peut etre hs ?', NULL, NULL, 'atelier', '20260421-4', 'invoiced', '2026-04-21 15:52:47', '2026-04-21 15:52:47', 0, '2026-04-10', NULL, NULL, 37.50, 7.50, 45.00, 37.50, NULL, NULL, '2026-03-26 16:04:01', '2026-04-21 15:52:47', NULL),
(87, 107, 'Toscana vae beige cadre bas', 'revision devis', 'Leger jeu roue AV :\n - ajustement du serrage de roulement de roue AV offert', NULL, 'atelier', '20260328-3', 'invoiced', '2026-03-28 16:37:00', '2026-03-28 16:37:00', 0, '2026-04-11', NULL, NULL, 53.33, 10.67, 64.00, 53.33, 60, NULL, '2026-03-27 13:42:44', '2026-03-28 16:37:00', NULL),
(88, 108, 'Decathlon elops 300', 'crevaison avant évidente, voir si le pneu à toujours une épine.', NULL, NULL, 'atelier', '20260408-1', 'invoiced', '2026-04-08 10:31:12', '2026-04-08 10:31:12', 0, '2026-04-11', NULL, NULL, 41.58, 8.32, 49.90, 40.06, NULL, NULL, '2026-03-27 16:10:06', '2026-04-08 10:31:12', NULL),
(89, 109, 'NCM Milano', 'Achat VAE de la marque NCM : modèle Milano reconditionné 0km', NULL, NULL, 'atelier', '20260327-1', 'invoiced', '2026-03-27 21:38:47', '2026-03-27 21:38:47', 0, '2026-04-11', NULL, NULL, 658.33, 131.67, 790.00, 178.33, NULL, NULL, '2026-03-27 21:38:39', '2026-03-27 21:38:47', NULL),
(90, 80, 'Focus noir rouge blanc Course', 'diag devis', 'remontage du pneus AV dans le bon sens (cadeau)\nmain d\'œuvre plaquette AV offert', NULL, 'atelier', '20260328-1', 'done', NULL, NULL, 0, '2026-04-12', NULL, NULL, 329.26, 65.86, 395.12, 141.83, 105, NULL, '2026-03-28 13:35:15', '2026-05-03 12:36:04', NULL),
(91, 110, 'Bulls twenty noir bleu', 'plaquette AR + nettoyage disque\n\ndiag devis', NULL, NULL, 'atelier', '20260408-2', 'invoiced', '2026-04-08 13:30:28', '2026-04-08 13:30:28', 0, '2026-04-12', NULL, NULL, 51.25, 10.25, 61.50, 51.25, NULL, NULL, '2026-03-28 15:03:59', '2026-04-08 13:30:28', NULL),
(92, 111, 'riverside rouge cadre moyen ville', 'diag devis par mail', NULL, NULL, 'atelier', '20260410-1', 'invoiced', '2026-04-10 09:02:38', '2026-04-10 09:02:38', 0, '2026-04-12', NULL, NULL, 103.43, 20.68, 124.11, 90.55, 75, NULL, '2026-03-28 16:52:34', '2026-04-10 09:02:38', NULL),
(93, 113, 'roue crevée', 'remplacement chambre à air', NULL, NULL, 'atelier', '20260331-1', 'invoiced', '2026-03-31 09:09:33', '2026-03-31 09:09:33', 0, '2026-04-15', NULL, NULL, 17.50, 3.49, 20.99, 11.30, 15, NULL, '2026-03-31 08:14:05', '2026-03-31 09:09:33', NULL),
(94, 114, 'Pégasus noir', 'lumière av\nrayon AR\ndévoilage AV ?\nfrein à revoir', NULL, NULL, 'atelier', '20260505-1', 'invoiced', '2026-05-05 14:43:36', '2026-05-05 14:43:36', 0, '2026-04-15', NULL, NULL, 98.76, 19.74, 118.50, 82.80, NULL, NULL, '2026-03-31 13:08:39', '2026-05-05 14:43:36', NULL),
(95, 83, 'Kalkhoff noir', 'révision', 'contacter lanvollon pour garde boue Kalkhoff', NULL, 'atelier', '20260409-4', 'invoiced', '2026-04-09 16:05:21', '2026-04-09 16:05:21', 0, '2026-04-15', NULL, NULL, 213.48, 42.70, 256.18, 138.61, 90, NULL, '2026-03-31 13:29:55', '2026-04-09 16:05:21', NULL),
(96, 115, 'Giant orange', 'diagnostique révision', NULL, NULL, 'atelier', '20260409-1', 'invoiced', '2026-04-09 16:04:34', '2026-04-09 16:04:34', 0, '2026-04-15', NULL, NULL, 102.96, 20.59, 123.55, 76.76, 75, NULL, '2026-03-31 13:59:53', '2026-04-09 16:04:34', NULL),
(97, 115, 'Mbk bleu femme', 'diag révision', NULL, NULL, 'atelier', '20260409-2', 'invoiced', '2026-04-09 16:04:44', '2026-04-09 16:04:44', 0, '2026-04-15', NULL, NULL, 73.33, 14.67, 88.00, 73.33, NULL, NULL, '2026-03-31 14:00:22', '2026-04-09 16:04:44', NULL),
(98, 116, 'Nakamura noir', 'roue arriere et reglage de frein', NULL, NULL, 'atelier', '20260331-2', 'invoiced', '2026-03-31 15:17:06', '2026-03-31 15:17:06', 0, '2026-04-15', NULL, NULL, 85.83, 17.17, 103.00, 41.28, 15, NULL, '2026-03-31 15:16:57', '2026-03-31 15:17:06', NULL),
(99, 113, 'moustache bleu', 'frein anormale après remontage de roue', NULL, NULL, 'atelier', '20260401-1', 'invoiced', '2026-04-01 15:36:09', '2026-04-01 15:36:09', 0, '2026-04-16', NULL, NULL, 20.83, 4.17, 25.00, 20.83, NULL, NULL, '2026-04-01 14:45:51', '2026-04-01 15:36:09', NULL),
(100, 113, 'moustache bleu', 'diagnostique', NULL, NULL, 'atelier', '20260401-2', 'pending_validation', NULL, NULL, 1, '2026-04-16', NULL, NULL, 120.77, 24.15, 144.92, 65.72, NULL, NULL, '2026-04-01 14:54:34', '2026-05-31 15:49:14', NULL),
(101, 117, 'Orbea route', 'diag freinage\n(ne pas tenir compte du pneu AV)\ndiag transmission\na voir general', 'concernant le freinage, nous aimerions essayé de purger vos freins (offerts) pour voir si la course des leviers se réduit.\n\nNous avons aujourd\'hui un problème de téléphone à l\'atelier (changement opérateur qui semble compliqué) n\'hésitez pas à passer à l\'atelier pour constater nos remarques ou à répondre par mail si vous avez des question', NULL, 'atelier', '20260429-4', 'invoiced', '2026-04-29 15:31:16', '2026-04-29 15:31:16', 0, '2026-04-16', NULL, NULL, 303.73, 60.74, 364.47, 139.61, 90, NULL, '2026-04-01 15:21:08', '2026-04-29 15:31:16', NULL),
(102, 118, 'decat noir cadre bas elec perso moteur central', 'freins (patins fourni par la cliente)\nvoir pour remettre selle correctement\nbéquille sur cadre ar\nchaine renforcé\npneu AR\nvoir pour compteur de vitesse (réparer ou en prévoir un), pas indispensable', '100 esp\n75.99 en cb', NULL, 'atelier', '20260425-2', 'invoiced', '2026-04-25 14:37:18', '2026-04-25 14:37:18', 0, '2026-04-17', NULL, NULL, 146.67, 29.32, 175.99, 98.90, 45, NULL, '2026-04-02 07:52:41', '2026-04-25 14:37:18', NULL),
(143, 22, 'VTT rouge', 'conversion en VAE moteur central', NULL, NULL, 'atelier', '20260604-1', 'invoiced', '2026-06-04 07:13:28', '2026-06-04 07:13:28', 0, '2026-05-12', NULL, NULL, 500.00, 100.00, 600.00, 166.00, NULL, NULL, '2026-04-27 09:41:41', '2026-06-04 07:13:28', NULL),
(103, 119, 'elops bleu', 'manque de freinage', NULL, NULL, 'atelier', '20260402-1', 'invoiced', '2026-04-02 16:22:16', '2026-04-02 16:22:16', 0, '2026-04-17', NULL, NULL, 21.67, 4.33, 26.00, 21.67, NULL, NULL, '2026-04-02 12:36:23', '2026-04-02 16:22:16', NULL),
(104, 120, 'Beaufort billie très clair', 'Diagnostique révision\nvoir comment sortir la batterie et changer le kit serrure', 'Le vélo est en très bon état.\npour le kit serrure,  soit nous patientons d\'avoir reçu et appris a utiliser le matériel de crochetage, soit nous nous redonnons rendez-vous lorsque nous l\'avons. à votre guise :)', NULL, 'atelier', '20260430-2', 'invoiced', '2026-04-30 14:10:24', '2026-04-30 14:10:24', 0, '2026-04-17', NULL, NULL, 23.33, 4.67, 28.00, 23.33, NULL, NULL, '2026-04-02 14:26:04', '2026-04-30 14:10:24', NULL),
(105, 121, 'armor cycle E-sub orange noir', 'diag devis par mail', NULL, NULL, 'atelier', '20260418-2', 'invoiced', '2026-04-18 15:38:35', '2026-04-18 15:38:35', 0, '2026-04-17', NULL, NULL, 96.58, 19.32, 115.90, 68.25, NULL, NULL, '2026-04-02 15:55:11', '2026-04-18 15:38:35', NULL),
(106, 122, 'Mercier noir ville cadre bas', 'pneu av ar\ngaine cable freins ar\n\ndiag et devis par mail', 'prevoir transmission\nprevoir le prix de l\'ensemble dans un autre devis', NULL, 'atelier', '20260512-3', 'invoiced', '2026-05-12 08:58:07', '2026-05-12 08:58:07', 0, '2026-04-18', NULL, NULL, 48.32, 9.66, 57.98, 34.10, NULL, NULL, '2026-04-03 15:08:30', '2026-05-12 08:58:07', NULL),
(107, 123, 'vintage noir cadre bas ville', 'reglages freins', NULL, NULL, 'atelier', '20260414-2', 'invoiced', '2026-04-14 09:24:00', '2026-04-14 09:24:00', 0, '2026-04-19', NULL, NULL, 30.00, 6.00, 36.00, 30.00, NULL, NULL, '2026-04-04 07:27:38', '2026-04-14 09:24:01', NULL),
(108, 124, 'riverside electrique gris cadre haut', 'crevaison AR (bout de verre)', NULL, NULL, 'atelier', '20260422-2', 'invoiced', '2026-04-22 20:29:06', '2026-04-22 20:29:06', 0, '2026-04-19', NULL, NULL, 17.00, 3.40, 20.40, 17.00, NULL, NULL, '2026-04-04 09:34:04', '2026-04-22 20:29:06', NULL),
(109, 127, 'VTT bleu', 'regalge de frein AR', NULL, NULL, 'atelier', '20260407-1', 'invoiced', '2026-04-07 09:56:47', '2026-04-07 09:56:47', 0, '2026-04-22', NULL, NULL, 10.83, 2.17, 13.00, 10.83, NULL, NULL, '2026-04-07 09:39:55', '2026-04-07 09:56:47', NULL),
(110, 129, 'Neomouv  Carlyna NGE', 'Achat d\'un VAE', NULL, NULL, 'atelier', '20260421-2', 'invoiced', '2026-04-21 10:20:59', '2026-04-21 10:20:59', 0, '2026-04-22', NULL, NULL, 1249.17, 249.83, 1499.00, 386.21, NULL, NULL, '2026-04-07 16:52:53', '2026-04-21 10:20:59', NULL),
(111, 137, 'Gitanne noir', 'probleme de support batterie', NULL, NULL, 'atelier', '20260409-1', 'reception', NULL, NULL, 0, '2026-04-24', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-04-09 09:36:09', '2026-04-22 06:47:50', '2026-04-22 06:47:50'),
(112, 138, 'velo ville cardre bas bordeaux', 'diag / devis', NULL, NULL, 'atelier', '20260421-1', 'invoiced', '2026-04-21 07:31:25', '2026-04-21 07:31:25', 0, '2026-04-24', NULL, NULL, 95.57, 19.11, 114.68, 84.07, NULL, NULL, '2026-04-09 13:05:59', '2026-04-21 07:31:25', NULL),
(113, 78, 'rockrider enfant orange', 'diag devis', NULL, NULL, 'atelier', '20260424-1', 'invoiced', '2026-04-24 07:30:09', '2026-04-24 07:30:09', 0, '2026-04-24', NULL, NULL, 146.66, 29.31, 175.97, 98.16, 90, NULL, '2026-04-09 13:26:44', '2026-04-24 07:30:09', NULL),
(114, 140, 'Gaya jaune', 'échange contrôleur payé par le fabriquant\net devis freinage', 'nous offrons la main d\'oeuvre pour l\'installation des plaquettes car la purge nous demande d\'en faire la manutention de toute façon.', NULL, 'atelier', '20260409-4', 'reception', NULL, NULL, 0, '2026-04-24', NULL, NULL, 72.50, 14.50, 87.00, 67.74, NULL, NULL, '2026-04-09 16:15:01', '2026-04-22 06:47:22', '2026-04-22 06:47:22'),
(115, 141, 'toplife cadre bas electrique gris foncé clef porte cle mouton', 'diag / devis mail\npatins av ar\nreglage freins', 'pense bête pour l\'atelier : \nBéquille à resserrer, \ngarde boue AV à ajuster', NULL, 'atelier', '20260430-3', 'invoiced', '2026-04-30 15:13:19', '2026-04-30 15:13:19', 0, '2026-05-01', NULL, NULL, 59.09, 11.81, 70.90, 47.64, 45, NULL, '2026-04-10 12:35:50', '2026-04-30 15:13:19', NULL),
(116, 142, 'velair noir pliant', 'probleme electrique\npas de moteur , cablage ok , lumiere ar constante depuis un certain temps alors que dhabitude non.\necran ok , batterie pleine', 'Nous ne connaissons pas encore le montant des frais de ports.', NULL, 'atelier', '20260411-1', 'to_complete', NULL, NULL, 1, '2026-04-26', NULL, NULL, 107.37, 21.47, 128.84, 17.64, NULL, NULL, '2026-04-11 09:34:23', '2026-06-15 07:30:55', NULL),
(117, 143, 'Btwin vert enfant 24\"', 'diag devis avant loc/vente', 'entretient roulement roue AR offert', NULL, 'atelier', '20260411-2', 'to_quote', NULL, NULL, 0, '2026-04-26', NULL, NULL, 120.51, 24.09, 144.60, 95.64, 135, NULL, '2026-04-11 12:06:20', '2026-05-13 07:24:04', '2026-05-13 07:24:04'),
(118, 145, 'gitane gris cadre haut', 'diag devis par mail', 'Main d\'oeuvre pour le remplacement de la chambre à air offerte', NULL, 'atelier', '20260423-3', 'invoiced', '2026-04-23 12:08:52', '2026-04-23 12:08:52', 0, '2026-04-26', NULL, NULL, 123.17, 24.63, 147.80, 106.09, 120, NULL, '2026-04-11 14:39:14', '2026-04-23 12:08:52', NULL),
(119, 145, 'rockrider gris cadre moyen', 'diag devis par mail', NULL, NULL, 'atelier', '20260423-2', 'invoiced', '2026-04-23 12:08:37', '2026-04-23 12:08:37', 0, '2026-04-26', NULL, NULL, 68.34, 13.66, 82.00, 61.00, 60, NULL, '2026-04-11 14:40:05', '2026-04-23 12:08:37', NULL),
(120, 148, 'roue fat bike', 'roue crevé', NULL, NULL, 'atelier', '20260414-1', 'invoiced', '2026-04-14 08:41:52', '2026-04-14 08:41:52', 0, '2026-04-29', NULL, NULL, 19.08, 3.82, 22.90, 14.13, NULL, NULL, '2026-04-14 08:41:46', '2026-04-14 08:41:52', NULL),
(121, 149, 'achat chambre a air', 'Venant de bréhat', NULL, NULL, 'atelier', '20260415-1', 'invoiced', '2026-04-15 13:36:10', '2026-04-15 13:36:10', 0, '2026-04-29', NULL, NULL, 5.75, 1.15, 6.90, 4.18, NULL, NULL, '2026-04-14 12:21:15', '2026-04-15 13:36:10', NULL),
(122, 151, 'Carlina HY XR', 'Achat d\'un VAE', NULL, NULL, 'atelier', '20260421-3', 'invoiced', '2026-04-21 10:22:50', '2026-04-21 10:22:50', 0, '2026-04-30', NULL, NULL, 1665.83, 333.17, 1999.00, 506.47, NULL, NULL, '2026-04-15 13:46:35', '2026-04-21 10:22:50', NULL),
(123, 152, 'Gravel La pierre crosshill 3', 'crevaison', NULL, NULL, 'atelier', '20260416-2', 'invoiced', '2026-04-16 09:32:19', '2026-04-16 09:32:19', 0, '2026-04-30', NULL, NULL, 17.33, 3.47, 20.80, 13.83, NULL, NULL, '2026-04-15 15:30:52', '2026-04-16 09:32:19', NULL),
(124, 153, 'trotinette  grise orange', 'roue AV dégonflée', NULL, NULL, 'atelier', '20260416-1', 'reception', NULL, NULL, 0, '2026-05-01', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-04-16 15:19:59', '2026-04-20 09:00:00', '2026-04-20 09:00:00'),
(125, 157, 'vélo enfant bleu et rouge', 'diag entretien', NULL, NULL, 'atelier', '20260423-1', 'invoiced', '2026-04-23 12:08:21', '2026-04-23 12:08:21', 0, '2026-05-02', NULL, NULL, 33.34, 6.66, 40.00, 25.90, NULL, NULL, '2026-04-17 13:48:18', '2026-04-23 12:08:21', NULL),
(126, 158, 'Giant cabre bas blanc', 'carter de chaine en adaptable et diag revision\n(vitesse plateau difficile)', NULL, NULL, 'atelier', '20260417-2', 'reception', NULL, NULL, 0, '2026-05-02', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-04-17 14:24:49', '2026-04-17 14:24:49', NULL),
(127, 159, 'Trek gravel \"sable\"', 'diag revision : bruit plaquettes / leviers mous\npassage en tubless : voir adaptateur jante asymetrique\n(chez dt swiss ? )', NULL, NULL, 'atelier', '20260512-5', 'invoiced', '2026-05-12 15:36:22', '2026-05-12 15:36:22', 0, '2026-05-02', NULL, NULL, 71.33, 14.27, 85.60, 64.33, NULL, NULL, '2026-04-17 15:30:57', '2026-05-12 15:36:22', NULL),
(128, 160, 'O2 feel  noir cadre bas roues marrons', 'purge frein ar', NULL, NULL, 'atelier', '20260418-3', 'invoiced', '2026-04-18 15:52:09', '2026-04-18 15:52:09', 0, '2026-05-03', NULL, NULL, 27.50, 5.50, 33.00, 27.50, NULL, NULL, '2026-04-18 09:38:16', '2026-04-18 15:52:09', NULL),
(129, 162, 'cargo glacier', 'commande de dérailleur AR rotative à commander', 'prévoir chaine 7v\npneu AR  26x2.00\npneu AVD 16x2-1/4', NULL, 'atelier', '20260529-1', 'invoiced', '2026-05-29 04:28:02', '2026-05-29 04:28:02', 0, '2026-05-03', NULL, NULL, 30.74, 6.14, 36.88, 22.09, NULL, NULL, '2026-04-18 13:22:12', '2026-05-29 04:28:02', NULL),
(130, 163, 'urban biker sydney noir cadre bas', 'changement de plaquettes AV AR', NULL, NULL, 'atelier', '20260418-1', 'invoiced', '2026-04-18 14:31:48', '2026-04-18 14:31:48', 0, '2026-05-03', NULL, NULL, 28.33, 5.67, 34.00, 28.33, NULL, NULL, '2026-04-18 14:31:39', '2026-04-18 14:31:48', NULL),
(131, 164, 'bertin course gris bleu', 'diag devis mail', 'transmission a prévoir , plateau + chaine + cassette\noutil manquant pour mesures complète extracteur trop court', NULL, 'atelier', '20260520-2', 'invoiced', '2026-05-20 08:28:34', '2026-05-20 08:28:34', 0, '2026-05-06', NULL, NULL, 62.50, 12.50, 75.00, 49.50, NULL, NULL, '2026-04-21 14:55:50', '2026-05-20 08:28:34', NULL),
(132, 167, 'achat casque', 'Casque Polysport', NULL, NULL, 'atelier', '20260429-2', 'invoiced', '2026-04-29 14:55:56', '2026-04-29 14:55:56', 0, '2026-05-07', NULL, NULL, 41.58, 8.32, 49.90, 16.63, NULL, NULL, '2026-04-22 09:28:14', '2026-04-29 14:55:56', NULL),
(133, 169, 'scraper scr2 orange', 'fuite lente AV\nchangement de chambre a air , valve defectueuse', NULL, NULL, 'atelier', '20260422-1', 'invoiced', '2026-04-22 14:17:39', '2026-04-22 14:17:39', 0, '2026-05-07', NULL, NULL, 14.16, 2.84, 17.00, 11.69, NULL, NULL, '2026-04-22 14:17:02', '2026-04-22 14:17:39', NULL),
(134, 170, 'Motoconfort doré', 'vélo ayant un probleme de frein et de pédalier', 'nous avons choisi de ne pas compter les 5h de main d\'oeuvre nécessaire à l\'extraction de l\'ancien pédalier.\nPour rester sur un prix raisonnable, nous avons choisi de facturer 3/4 d\'heure.\n\nNous sommes heureux de remettre sur la route un morceau d\'histoire du vélo et nous excusons du temps passé pour la recherche de la pièce de remplacement.', NULL, 'atelier', '20260423-4', 'invoiced', '2026-04-23 15:51:25', '2026-04-23 15:51:25', 0, '2026-05-08', NULL, NULL, 120.00, 24.00, 144.00, 120.00, NULL, NULL, '2026-04-23 06:48:24', '2026-04-23 15:51:25', NULL),
(140, 181, 'lambda', 'plaquettes', NULL, NULL, 'atelier', '20260425-1', 'invoiced', '2026-04-25 07:42:41', '2026-04-25 07:42:41', 0, '2026-05-10', NULL, NULL, 35.00, 7.00, 42.00, 35.00, NULL, NULL, '2026-04-25 07:42:33', '2026-04-25 07:42:41', NULL),
(162, 213, 'VeloBecane', 'transmission a refaire', NULL, NULL, 'atelier', '20260519-1', 'invoiced', '2026-05-19 08:38:26', '2026-05-19 08:38:26', 0, '2026-05-19', NULL, NULL, 78.45, 15.69, 94.14, 43.06, NULL, NULL, '2026-05-04 09:29:15', '2026-05-19 08:38:26', NULL),
(135, 174, 'stablinski ville ancien cadre bas', 'diag devis par mail', NULL, NULL, 'atelier', '20260626-1', 'invoiced', '2026-06-26 13:21:40', '2026-06-26 00:00:00', 0, '2026-05-08', NULL, NULL, 78.29, 15.64, 93.93, 61.43, NULL, NULL, '2026-04-23 16:03:13', '2026-06-26 16:02:35', NULL),
(137, 176, 'specialized crosstail', 'roue libre HS\nchangement jante occasion', NULL, NULL, 'atelier', '20260424-3', 'invoiced', '2026-04-24 10:27:05', '2026-04-24 10:27:05', 0, '2026-05-09', NULL, NULL, 36.25, 7.25, 43.50, 36.25, NULL, NULL, '2026-04-24 10:26:13', '2026-04-24 10:27:05', NULL),
(136, 175, 'nakamura vae noir cadre moyen', 'chaine cassée', NULL, NULL, 'atelier', '20260424-2', 'invoiced', '2026-04-24 09:24:59', '2026-04-24 09:24:59', 0, '2026-05-09', NULL, NULL, 25.00, 5.00, 30.00, 25.00, NULL, NULL, '2026-04-24 09:18:05', '2026-04-24 09:24:59', NULL),
(138, 177, 'nakamura vtt noir jaune cliff 700', 'anneau de tube de selle HS 27.5mm\npret en attendant de commander\nrappeler la cliente pour avoir le nouveau et rendre le notre', NULL, NULL, 'atelier', '20260424-3', 'to_quote', NULL, NULL, 0, '2026-05-09', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-04-24 10:35:47', '2026-04-24 10:36:47', NULL),
(139, 178, 'ville beige vae', 'ar  a plat', NULL, NULL, 'atelier', '20260424-4', 'invoiced', '2026-04-24 14:43:01', '2026-04-24 14:43:01', 0, '2026-05-09', NULL, NULL, 14.16, 2.84, 17.00, 14.16, NULL, NULL, '2026-04-24 14:42:54', '2026-04-24 14:43:01', NULL),
(141, 183, 'specialized rouge grande taille', 'diag devis mail\nreglage derailleur arriere', NULL, NULL, 'atelier', '20260430-1', 'invoiced', '2026-04-30 12:43:00', '2026-04-30 12:43:00', 0, '2026-05-10', NULL, NULL, 40.83, 8.17, 49.00, 40.83, 45, NULL, '2026-04-25 13:29:41', '2026-04-30 12:43:00', NULL),
(142, 184, 'lambda', 'gilet orange ppav\n1L(10/12)\n1M/S', NULL, NULL, 'atelier', '20260425-3', 'reception', NULL, NULL, 0, '2026-05-10', NULL, NULL, 16.67, 3.33, 20.00, 16.67, NULL, NULL, '2026-04-25 14:29:11', '2026-05-26 10:31:04', '2026-05-26 10:31:04'),
(144, 186, 'Gaya Le court', 'intervention IT-13704', NULL, NULL, 'atelier', '20260427-1', 'invoiced', '2026-04-27 10:14:30', '2026-04-27 10:14:30', 0, '2026-05-12', NULL, NULL, 50.00, 10.00, 60.00, 50.00, NULL, NULL, '2026-04-27 10:13:39', '2026-04-27 10:14:30', NULL),
(145, 188, 'Raymon vae gris rouge', 'diag devis mail\ncache de batterie qui tombe\nbruit  de frottement gênant av ar\nfixation de garde boue ar a voir', 'Nous avons comptez 50 euros de main d\'oeuvre pour l\'installation des pneus ainsi que : \n- Garde boue AV AR à re-fixer\n- Guidon à ressérrer\n- Replacement commande freins\n- Ressérrage béquille\n- Ajustage de patte de fixation cache batterie', NULL, 'atelier', '20260512-2', 'invoiced', '2026-05-12 07:27:15', '2026-05-12 07:27:15', 0, '2026-05-13', NULL, NULL, 76.67, 15.33, 92.00, 54.67, NULL, NULL, '2026-04-28 08:59:31', '2026-05-12 07:27:15', NULL),
(146, 189, 'moustache vert amande vae cadre bas', 'diag devis mail', 'votre vélo est en super état.\nVos freins fonctionne très bien, cependant si nous respectons les préconisations constructeurs, nous vous invitons à faire le remplacement du liquide de frein.\nNous vous le proposons donc, sans urgence puisque tout va bien.\n\nNous avons noté un léger voile de la roue AR, mais vraiment léger.\nDites nous ce que vous souhaitez faire.\n\nCordialement', NULL, 'atelier', '20260512-1', 'invoiced', '2026-05-12 07:26:12', '2026-05-12 07:26:12', 0, '2026-05-13', NULL, NULL, 70.00, 14.00, 84.00, 70.00, NULL, NULL, '2026-04-28 09:25:50', '2026-05-12 07:26:12', NULL),
(147, 190, 'atelier', 'crevaison', NULL, NULL, 'atelier', '20260429-1', 'invoiced', '2026-04-29 06:44:16', '2026-04-29 06:44:16', 0, '2026-05-13', NULL, NULL, 5.42, 1.08, 6.50, 2.47, NULL, NULL, '2026-04-28 09:55:54', '2026-04-29 06:44:16', NULL),
(148, 191, 'Btwin rockrider 540 noir gris', 'diag devis par sms\ngarde boue avant a ajouter', 'La pose du garde boue est offerte', NULL, 'atelier', '20260508-1', 'invoiced', '2026-05-08 21:03:24', '2026-05-08 21:03:24', 0, '2026-05-13', NULL, NULL, 97.86, 19.57, 117.43, 69.71, NULL, NULL, '2026-04-28 12:19:35', '2026-05-08 21:03:24', NULL),
(149, 192, 'batterie hs', 'batterie 5-6 ans\nn s\'allume pas', NULL, NULL, 'atelier', '20260428-5', 'reception', NULL, NULL, 1, '2026-05-13', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-04-28 12:56:16', '2026-06-15 07:30:42', NULL),
(150, 193, 'devis VAE Neomouv', 'envoyer la fiche https://www.neomouv.com/produit/urbain/carlina-hy-xrp5179-xpr11579.html', NULL, NULL, 'atelier', '20260507-1', 'invoiced', '2026-05-07 08:02:05', '2026-05-07 08:02:05', 0, '2026-05-13', NULL, NULL, 17.50, 3.50, 21.00, 17.50, NULL, NULL, '2026-04-28 13:43:13', '2026-05-07 08:02:05', NULL),
(170, 223, 'Achat Neomouv Linaria', 'Achat VAE', 'Linaria  : 1075 - 350  / 18 = 40,5 euros mois.\n\n350réglé le 5-06-26\nprélèvement le 5 du mois', NULL, 'atelier', '20260506-6', 'done', NULL, NULL, 0, '2026-05-21', NULL, NULL, 895.83, 179.17, 1075.00, 285.83, NULL, NULL, '2026-05-06 17:16:24', '2026-06-15 07:29:04', NULL);
INSERT INTO `quotes` (`id`, `client_id`, `bike_description`, `reception_comment`, `remarks`, `email_note`, `metier`, `reference`, `status`, `invoiced_at`, `paid_at`, `is_archived`, `valid_until`, `discount_type`, `discount_value`, `total_ht`, `total_tva`, `total_ttc`, `margin_total_ht`, `total_estimated_time_minutes`, `actual_time_minutes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(151, 194, 'Decathlon vert', 'soudure à faire pour remettre le cadre', NULL, NULL, 'atelier', '20260429-1', 'done', NULL, NULL, 0, '2026-05-14', NULL, NULL, 125.00, 25.00, 150.00, 125.00, NULL, NULL, '2026-04-29 13:06:44', '2026-06-19 09:54:45', NULL),
(152, 195, 'Btwin ville gris', 'vélo à électrifier', NULL, NULL, 'atelier', '20260526-2', 'invoiced', '2026-05-26 10:30:17', '2026-05-26 10:30:17', 0, '2026-05-13', NULL, NULL, 27.50, 5.50, 33.00, 25.60, NULL, NULL, '2026-04-29 13:55:43', '2026-05-26 10:30:17', NULL),
(153, 196, 'Btwin riverside  900 gris vert fluo', 'diag devis par mail\npneus minimum', 'Nous avons noté un dépassement de tolérance dans la mesure de l’usure de la chaîne.\nCela implique le remplacement du plateau, de la cassette et de la chaîne.\n\nTant que vous ne constatez pas de déraillements intempestifs ou de sauts de chaîne réguliers, il n’y a pas d’urgence. L’idéal est toutefois de contrôler régulièrement l’état de la chaîne afin d’éviter d’impacter les autres composants.\n\nNous vous montrerons cela !\n\nEn attendant, vous pouvez nous dire ce que vous souhaitez faire, ou non.\nJ’ai séparé les sujets de main-d’œuvre afin que vous puissiez faire le tri au niveau des coûts.', NULL, 'atelier', '20260520-1', 'invoiced', '2026-05-20 08:06:05', '2026-05-20 08:06:05', 0, '2026-05-14', NULL, NULL, 202.87, 40.57, 243.44, 96.90, NULL, NULL, '2026-04-29 14:15:46', '2026-05-20 08:06:05', NULL),
(154, 158, 'Achat VAE', 'Achat VAE de la marque NCM', NULL, NULL, 'atelier', '20260429-3', 'invoiced', '2026-04-29 14:59:02', '2026-04-29 14:59:02', 0, '2026-05-14', NULL, NULL, 757.50, 151.50, 909.00, 222.50, NULL, NULL, '2026-04-29 14:58:51', '2026-04-29 14:59:02', NULL),
(155, 202, 'Top Life noir cadre haut VAE', 'probleme elctrique, batterie allumé , ecran allumé', NULL, NULL, 'atelier', '20260606-3', 'invoiced', '2026-06-06 09:54:31', '2026-06-06 00:00:00', 0, '2026-05-15', NULL, NULL, 37.50, 7.50, 45.00, 37.50, NULL, NULL, '2026-04-30 13:52:37', '2026-06-10 09:05:09', NULL),
(156, 203, 'Peugeot blanc', 'installation d\'une béquille', NULL, NULL, 'atelier', '20260515-1', 'invoiced', '2026-05-15 13:26:58', '2026-05-15 13:26:58', 0, '2026-05-15', NULL, NULL, 12.53, 2.51, 15.04, 12.53, NULL, NULL, '2026-04-30 14:40:10', '2026-05-15 13:26:58', NULL),
(157, 129, 'VAE Carlina HY NG', 'Achat d\'un VAE de la marque Neomouv', NULL, NULL, 'atelier', '20260501-1', 'invoiced', '2026-05-01 14:30:02', '2026-05-01 14:30:02', 0, '2026-05-15', NULL, NULL, 1375.00, 275.00, 1650.00, 368.24, NULL, NULL, '2026-04-30 15:12:42', '2026-05-01 14:30:02', NULL),
(225, 313, 'Btwin Seven (le grand)', 'diagnostique remise en route', 'note interne  : selle à ressérer', NULL, 'atelier', '20260617-7', 'invoiced', '2026-06-17 13:55:17', '2026-06-17 00:00:00', 0, '2026-06-14', NULL, NULL, 47.50, 9.50, 57.00, 47.50, NULL, NULL, '2026-05-30 06:22:46', '2026-06-17 13:55:27', NULL),
(226, 313, 'Btwin Conception (Le petit)', 'diagnostique remise en route', NULL, NULL, 'atelier', '20260617-6', 'invoiced', '2026-06-17 13:55:07', '2026-06-17 00:00:00', 0, '2026-06-14', NULL, NULL, 42.50, 8.50, 51.00, 40.60, NULL, NULL, '2026-05-30 06:24:02', '2026-06-17 13:55:29', NULL),
(158, 84, '#1 vélo de ville rouge cadre bas', 'diag revision\ninfo de différenciation : carter chaine cassé, 2 sonnettes', NULL, NULL, 'atelier', '20260430-4', 'reception', NULL, NULL, 0, '2026-05-15', NULL, NULL, 176.92, 35.39, 212.31, 118.24, 45, NULL, '2026-04-30 15:46:59', '2026-04-30 15:46:59', NULL),
(159, 204, 'BH cadre haut', 'remplacement de pneus', NULL, NULL, 'atelier', '20260430-4', 'invoiced', '2026-04-30 15:50:01', '2026-04-30 15:50:01', 0, '2026-05-15', NULL, NULL, 35.00, 7.00, 42.00, 35.00, NULL, NULL, '2026-04-30 15:49:24', '2026-04-30 15:50:01', NULL),
(160, 204, 'bh', 'remplacement des lampes \nentraxe mini 45 ; 87 maxi AR', NULL, NULL, 'atelier', '20260430-6', 'pending_validation', NULL, NULL, 0, '2026-05-15', NULL, NULL, 92.03, 18.40, 110.43, 77.64, NULL, NULL, '2026-04-30 16:00:19', '2026-05-12 09:53:43', NULL),
(161, 212, 'ncm T3s', 'vis de reglage plaquettes ar defectueuse', NULL, NULL, 'atelier', '20260502-1', 'reception', NULL, NULL, 0, '2026-05-17', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-05-02 13:43:42', '2026-05-02 13:43:42', NULL),
(163, 108, 'elops 320', 'remplacement de la roue libre', NULL, NULL, 'atelier', '20260511-1', 'invoiced', '2026-05-11 14:47:47', '2026-05-11 14:47:47', 0, '2026-05-19', NULL, NULL, 23.45, 4.69, 28.14, 16.15, NULL, NULL, '2026-05-04 14:22:11', '2026-05-11 14:47:47', NULL),
(164, 214, 'vtt rouge moteur central', 'probleme de transmission (deraille de l\'avant ?)\nprobleme de batterie qui ne s\'allume pas,  voir remplacement support + batterie', 'fixation de rack batterie HS , pas de vis foirés', NULL, 'atelier', '20260505-1', 'reception', NULL, NULL, 0, '2026-05-20', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-05-05 12:54:24', '2026-06-06 08:15:48', NULL),
(165, 217, 'gitane vert vae long tail AV AR', 'se coupe a l\'effort', 'on va obtenir le numero de l\'artisan qui a converti le vélo.', NULL, 'atelier', '20260506-1', 'reception', NULL, NULL, 0, '2026-05-21', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-05-06 08:35:11', '2026-05-13 08:41:36', NULL),
(166, 64, 'Metayer vert (femme)', 'porte gourde avec collier pour adapter sur le vélo\nmontage de la selle\nsonnette (à voir si on l\'a toujours)', NULL, NULL, 'atelier', '20260506-2', 'done', NULL, NULL, 0, '2026-05-21', NULL, NULL, 22.83, 4.57, 27.40, 18.16, NULL, NULL, '2026-05-06 12:31:33', '2026-06-26 09:27:50', NULL),
(167, 221, 'Nakamura blanc', 'diag révision', NULL, NULL, 'atelier', '20260610-2', 'invoiced', '2026-06-10 09:03:47', '2026-06-10 00:00:00', 0, '2026-05-21', NULL, NULL, 64.16, 12.84, 77.00, 57.20, NULL, NULL, '2026-05-06 13:12:49', '2026-06-10 09:04:33', NULL),
(168, 221, 'Nakamura vert', 'diag révision', 'Si nous convertissons ce vélo avec un moteur Virvolt 900,\nNous offrons toute la partie main d\'oeuvre du devis\n(montage pneus / réglage freins/ dévoillage).\nLes explications sur le prix du kit Virvolt par mail.', NULL, 'atelier', '20260610-1', 'invoiced', '2026-06-10 09:03:22', '2026-06-10 00:00:00', 0, '2026-05-21', NULL, NULL, 107.46, 21.48, 128.94, 90.03, 90, NULL, '2026-05-06 13:17:42', '2026-06-10 09:04:35', NULL),
(169, 222, 'Pressée :) Winora gris foncé VAE', 'assez pressée , moyen de locomotion\n-carter de chaine\ndiag  devis\npurge freins renouvellement\npedales a changer, metal et large (les memes serait parfait)', 'Nous lançons une recherche pour trouver un carter de chaine et vous communiquerons le prix de la pièce ultérieurement.', NULL, 'atelier', '20260516-3', 'invoiced', '2026-05-16 14:27:50', '2026-05-16 14:27:50', 0, '2026-05-21', NULL, NULL, 238.68, 47.72, 286.40, 171.41, NULL, NULL, '2026-05-06 16:17:05', '2026-05-16 14:27:50', NULL),
(171, 224, 'Convercycle (cargo pliantAR)', 'piston AR bloqué\nvisse a remettre sur le frein AR', NULL, NULL, 'atelier', '20260520-8', 'invoiced', '2026-05-20 14:59:06', '2026-05-20 14:59:06', 0, '2026-05-22', NULL, NULL, 10.83, 2.17, 13.00, 7.93, NULL, NULL, '2026-05-07 09:01:31', '2026-05-20 14:59:06', NULL),
(172, 148, 'roue FATBIKE', 'crevaison + remplacement des pneus', NULL, NULL, 'atelier', '20260520-5', 'invoiced', '2026-05-20 14:05:53', '2026-05-20 14:05:53', 0, '2026-05-22', NULL, NULL, 77.79, 15.55, 93.34, 44.02, NULL, NULL, '2026-05-07 12:06:01', '2026-05-20 14:05:53', NULL),
(173, 226, 'La Pierre Bordeau', 'Diag : \nporte patin toute méteo\nchaine\nvoir selle neomouv\ncompteur vitesse', 'règlement en 3 fois\n73,22 euros chaque fois.\n\nPremier encaissement 3-06-26*\ndeuxieme 3-07-26\ntroisieme 3-08-26', NULL, 'atelier', '20260603-1', 'invoiced', '2026-06-03 16:06:54', '2026-06-03 16:06:54', 0, '2026-05-22', NULL, NULL, 183.04, 36.62, 219.66, 131.27, NULL, NULL, '2026-05-07 13:29:35', '2026-06-03 16:06:54', NULL),
(234, 328, 'UpperTrack blanc-bleu', 'diag remis en état', NULL, NULL, 'atelier', '20260604-1', 'pending_validation', NULL, NULL, 1, '2026-06-19', NULL, NULL, 249.64, 49.93, 299.57, 189.84, NULL, NULL, '2026-06-04 08:52:30', '2026-06-15 07:25:57', NULL),
(174, 25, 'Riverside 500 bleu', 'fourniture des accessoirs et montage', 'montage béquille\nréglage des freins\nvérification vitesses et test routier', NULL, 'atelier', '20260507-2', 'invoiced', '2026-05-07 16:16:32', '2026-05-07 16:16:32', 0, '2026-05-22', NULL, NULL, 107.47, 21.49, 128.96, 107.47, NULL, NULL, '2026-05-07 15:58:43', '2026-05-07 16:16:32', NULL),
(175, 229, 'Achat VAE Neomouv', 'Carlina HY XR', 'acompte 500 euros payé le 8/05/26', NULL, 'atelier', '20260514-1', 'invoiced', '2026-05-14 13:28:45', '2026-05-14 13:28:45', 0, '2026-05-23', NULL, NULL, 1555.74, 311.15, 1866.89, 500.66, NULL, NULL, '2026-05-08 12:33:10', '2026-05-14 13:28:45', NULL),
(210, 289, 'achat VAE Neomouv', 'Carlina HY XR', NULL, NULL, 'atelier', '20260606-2', 'invoiced', '2026-06-06 08:44:46', '2026-06-06 00:00:00', 0, '2026-06-10', NULL, NULL, 1250.00, 250.00, 1500.00, 440.00, NULL, NULL, '2026-05-26 12:59:44', '2026-06-10 09:05:11', NULL),
(176, 234, 'Fahrrad', 'reduire le ratio transmission\n(a voir remplacement chaine et plateau)\n\nvoir pour mettre un porte bagage avant ?', NULL, NULL, 'atelier', '20260616-4', 'invoiced', '2026-06-16 13:31:58', NULL, 0, '2026-05-26', NULL, NULL, 149.47, 29.90, 179.37, 83.30, NULL, NULL, '2026-05-11 14:35:56', '2026-06-16 13:31:58', NULL),
(177, 235, 'Elops Bleu', 'voir \ndévoilage AV/AR\nproblablement regalge derailleur', NULL, NULL, 'atelier', '20260521-1', 'invoiced', '2026-05-21 14:48:49', '2026-05-21 14:48:49', 0, '2026-05-26', NULL, NULL, 30.00, 6.00, 36.00, 30.00, NULL, NULL, '2026-05-11 14:38:24', '2026-05-21 14:48:49', NULL),
(178, 236, 'demande de commande', 'commande d\'un pneu 26x1.9 avec sa chambre air shradder', NULL, NULL, 'atelier', '20260519-4', 'invoiced', '2026-05-19 14:16:15', '2026-05-19 14:16:15', 0, '2026-05-27', NULL, NULL, 29.15, 5.83, 34.98, 10.70, NULL, NULL, '2026-05-12 07:10:48', '2026-05-19 14:16:15', NULL),
(179, 237, 'Pegasus', 'demande de panier pour installer un petit chien\ncommande d\'un panier Optimiz', NULL, NULL, 'atelier', '20260512-2', 'to_quote', NULL, NULL, 0, '2026-05-27', NULL, NULL, 19.17, 3.83, 23.00, 7.67, NULL, NULL, '2026-05-12 09:09:54', '2026-06-15 07:28:56', NULL),
(180, 238, 'Achat VAE en 24 mensualités', 'VAE Carlina HY NG bleu', '1824 avec paiement en 24 fois\npremier versement le 15 mai, la suite au 6 du mois.\nLe panier et le rétroviseur sont payé comptant.', NULL, 'atelier', '20260512-4', 'invoiced', '2026-05-12 14:13:46', '2026-05-12 14:13:46', 0, '2026-05-27', NULL, NULL, 1552.75, 310.55, 1863.30, 549.20, NULL, NULL, '2026-05-12 13:51:58', '2026-05-12 14:13:46', NULL),
(181, 239, 'PRESSée coolect 2D enfant vert fluo', 'diag devis par mail\nplateau en priorité\npotentielle location le samedi pour le lundi ou pret', 'nous allons également ajuster votre garde boue AV', NULL, 'atelier', '20260516-2', 'invoiced', '2026-05-16 12:31:54', '2026-05-16 12:31:54', 0, '2026-05-27', NULL, NULL, 40.00, 8.00, 48.00, 38.10, NULL, NULL, '2026-05-12 15:50:20', '2026-05-16 12:31:54', NULL),
(183, 245, 'VeloMad blanc', 'crevaison lente / pneu abimé', 'facture à envoyer par mail au client ;)', NULL, 'atelier', '20260520-7', 'invoiced', '2026-05-20 14:58:23', '2026-05-20 14:58:23', 0, '2026-05-28', NULL, NULL, 87.42, 17.48, 104.90, 46.17, NULL, NULL, '2026-05-13 15:35:19', '2026-05-20 14:58:23', NULL),
(182, 244, 'Giant 16\" noir blanc orange', 'diagnostique revision', 'repositionnement chambre à air AV/AR', NULL, 'atelier', '20260613-1', 'invoiced', '2026-06-13 12:48:43', NULL, 0, '2026-05-28', NULL, NULL, 48.33, 9.67, 58.00, 48.33, NULL, NULL, '2026-05-13 13:39:30', '2026-06-13 12:48:43', NULL),
(184, 178, 'O2 feel beige cadre bas', 'diagnostique révision', NULL, NULL, 'atelier', '20260610-4', 'invoiced', '2026-06-10 15:17:59', '2026-06-10 00:00:00', 0, '2026-05-29', NULL, NULL, 77.84, 15.58, 93.42, 71.74, NULL, NULL, '2026-05-14 15:52:43', '2026-06-10 15:18:31', NULL),
(185, 249, 'GIANT VTT noir vert bleu gourde sacoche AV noire , sacoche AR gauche rouge', 'diag devis par mail\nfixation des cible de compteur roue + fourche', NULL, NULL, 'atelier', '20260520-6', 'invoiced', '2026-05-20 14:27:21', '2026-05-20 14:27:21', 0, '2026-05-30', NULL, NULL, 8.33, 1.67, 10.00, 8.33, NULL, NULL, '2026-05-15 15:19:31', '2026-05-20 14:27:21', NULL),
(186, 158, 'NCM Paris', 'achat vélo NCM', NULL, NULL, 'atelier', '20260516-1', 'invoiced', '2026-05-16 12:09:03', '2026-05-16 12:09:03', 0, '2026-05-30', NULL, NULL, 857.41, 171.49, 1028.90, 259.11, NULL, NULL, '2026-05-15 16:41:24', '2026-05-16 12:09:03', NULL),
(187, 251, 'vtt avalanche GT noir et vert', 'pedales, sonnette , poignées\ndiag devis mail', NULL, NULL, 'atelier', '20260530-2', 'invoiced', '2026-05-30 14:01:30', '2026-05-30 14:01:30', 0, '2026-05-31', NULL, NULL, 57.33, 11.47, 68.80, 44.20, NULL, NULL, '2026-05-16 10:04:07', '2026-05-30 14:01:30', NULL),
(195, 263, 'moov way blanc rouge noir VTT', 'batterie bloqué sur velo (clé perdue) changer serrure si batterie bonne?\ntest batterie , prix batterie\ndiag devis par mail', NULL, NULL, 'atelier', '20260520-1', 'reception', NULL, NULL, 0, '2026-06-04', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-05-20 09:48:20', '2026-05-20 09:48:20', NULL),
(188, 252, 'vtt lapierre gris', 'derailleur HS\ndiag devis par mail', NULL, NULL, 'atelier', '20260516-2', 'validated', NULL, NULL, 0, '2026-05-31', NULL, NULL, 73.78, 14.76, 88.54, 54.85, NULL, NULL, '2026-05-16 12:26:28', '2026-06-15 07:28:50', NULL),
(189, 253, 'roue rayon cassé', 'rayon + devoilage', NULL, NULL, 'atelier', '20260520-3', 'invoiced', '2026-05-20 09:02:20', '2026-05-20 09:02:20', 0, '2026-05-31', NULL, NULL, 16.67, 3.33, 20.00, 16.67, NULL, NULL, '2026-05-16 12:33:39', '2026-05-20 09:02:20', NULL),
(190, 254, 'van rysel course noir', 'crevaison AV', NULL, NULL, 'atelier', '20260519-2', 'invoiced', '2026-05-19 10:51:47', '2026-05-19 10:51:47', 0, '2026-05-31', NULL, NULL, 16.75, 3.35, 20.10, 13.35, NULL, NULL, '2026-05-16 13:45:53', '2026-05-19 10:51:47', NULL),
(191, 255, 'Gnome rhone ancien ville GRIS clair', 'diag devis par mail', NULL, NULL, 'atelier', '20260516-5', 'to_quote', NULL, NULL, 0, '2026-05-31', NULL, NULL, 85.00, 17.00, 102.00, 85.00, NULL, NULL, '2026-05-16 14:25:09', '2026-06-23 12:59:25', NULL),
(192, 257, 'victoire Le Bellec 06 69 29 28 33', 'changement de plaquette + réglage\nresserrage de panier', 'DEJA REGLE EN CB LE 19/05\n\n06 69 29 28 33\nvictoire Le Bellec', NULL, 'atelier', '20260529-6', 'invoiced', '2026-05-29 21:01:54', '2026-05-29 21:01:54', 0, '2026-06-03', NULL, NULL, 39.17, 7.83, 47.00, 39.17, NULL, NULL, '2026-05-19 07:39:41', '2026-05-29 21:01:54', NULL),
(193, 259, 'itinerant riv 500 / trocadero', 'reglage derailleur x2\ncable et gaine de freinage', NULL, NULL, 'atelier', '20260519-3', 'invoiced', '2026-05-19 13:27:02', '2026-05-19 13:27:02', 0, '2026-06-03', NULL, NULL, 27.50, 5.50, 33.00, 27.50, NULL, NULL, '2026-05-19 13:26:57', '2026-05-19 13:27:02', NULL),
(194, 260, 'Elaia 2 NG Neomouv', 'Devis pour un VAE avec accessoires', 'sont offert : \n- le panier (17.90 euros),\n- le rétroviseur (22euros),\n- l\'antivol (29,99à', NULL, 'atelier', '20260519-3', 'pending_validation', NULL, NULL, 1, '2026-06-03', NULL, NULL, 1991.67, 398.33, 2390.00, 603.18, NULL, NULL, '2026-05-19 15:23:24', '2026-06-15 07:28:42', NULL),
(196, 263, 'Velair ville cadre bas gris foncé panier avant noir', 'diag devis par mail', NULL, NULL, 'atelier', '20260520-2', 'pending_validation', NULL, NULL, 0, '2026-06-04', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-05-20 09:50:50', '2026-06-02 09:46:41', NULL),
(197, 226, 'VTT vecktor blanc rouge', 'diag devis par mail', NULL, NULL, 'atelier', '20260520-3', 'to_complete', NULL, NULL, 0, '2026-06-04', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-05-20 12:45:49', '2026-05-20 12:52:27', NULL),
(198, 264, 'achat retroviseur', 'Pays de Paimpol à vélo', NULL, NULL, 'atelier', '20260520-4', 'invoiced', '2026-05-20 13:13:40', '2026-05-20 13:13:40', 0, '2026-06-04', NULL, NULL, 14.32, 2.86, 17.18, 5.82, NULL, NULL, '2026-05-20 13:12:48', '2026-05-20 13:13:40', NULL),
(201, 272, 'Fyxation vert gravel', 'rupture câble frein AR +voir plaquette + voir réglage\n+ diag?', 'les plaquettes sont encore bonnes, il doit rester 30% (AV)', NULL, 'atelier', '20260618-4', 'invoiced', '2026-06-18 16:01:48', '2026-06-18 00:00:00', 0, '2026-06-06', NULL, NULL, 47.92, 9.58, 57.50, 42.12, NULL, NULL, '2026-05-22 09:28:39', '2026-06-20 18:49:08', NULL),
(199, 269, 'Achat Neomouv Elaia 2 NG blanc', 'retroviseur a ajouter sur le vélo\ncasque', 'ELAIA 2 NG 28\' T48 S/B CREME : NM2026/1000027\n BATTERIE NM-PRO2 522Wh 36V : A2594D0014223\n\npensez à enregistrer votre garantie sur le site www.neomouv.com (en bas de page : le bouton \"enregistrement de la garantie\"', NULL, 'atelier', '20260604-2', 'invoiced', '2026-06-04 08:47:15', '2026-06-04 08:47:15', 0, '2026-06-05', NULL, NULL, 1991.67, 398.33, 2390.00, 977.91, NULL, NULL, '2026-05-21 12:40:33', '2026-06-04 08:47:15', NULL),
(200, 270, 'roue de velo anciennes', 'deux roue velo\nune a devoiler\npneus et chambre a mettre', NULL, NULL, 'atelier', '20260521-2', 'to_quote', NULL, NULL, 0, '2026-06-05', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-05-21 13:42:36', '2026-06-15 07:28:28', NULL),
(202, 272, 'Fyxation vert gravel (selle noir)', 'voir plaquettes ? reglage et derailleur central', 'question manette plateau (position) (reponse : faut que ca marche mais ne pas defaire la guidoline)\nprivilégier petit plateau et moyen plateau\n\npneus AV/AR à surveiller', NULL, 'atelier', '20260618-3', 'invoiced', '2026-06-18 16:01:26', '2026-06-18 00:00:00', 0, '2026-06-06', NULL, NULL, 63.34, 12.66, 76.00, 61.44, NULL, NULL, '2026-05-22 09:29:20', '2026-06-20 18:49:10', NULL),
(203, 272, 'achat chambre a air', '650 x35 b  - 26x1/2', NULL, NULL, 'atelier', '20260522-3', 'reception', NULL, NULL, 0, '2026-06-06', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-05-22 12:35:12', '2026-05-22 12:35:12', NULL),
(204, 276, 'O2 feel vert', 'chaine cassée', NULL, NULL, 'atelier', '20260524-1', 'invoiced', '2026-05-24 06:25:39', '2026-05-24 06:25:39', 0, '2026-06-06', NULL, NULL, 55.83, 11.16, 66.99, 26.83, NULL, NULL, '2026-05-22 16:23:36', '2026-05-24 06:25:39', NULL),
(205, 279, 'velo enfant 16', 'crevaison avant (valve)', NULL, NULL, 'atelier', '20260615-1', 'invoiced', '2026-06-15 07:21:27', NULL, 0, '2026-06-07', NULL, NULL, 16.66, 3.34, 20.00, 13.71, NULL, NULL, '2026-05-23 09:59:36', '2026-06-15 07:21:27', NULL),
(206, 280, 'gitane bleu', 'crevaison arrière\nvoir état du pneus\nMaximum fin de semaine prochaine\n(depart le 28)', 'selon l\'état de la chambre à air AV, nous  vous consulterons pour la remplacer si l\'état ne nous rassure pas.\n\nPour l\'instant, nous ne la comptons pas dans le devis.\n(ajouter 8,25)', NULL, 'atelier', '20260529-4', 'invoiced', '2026-05-29 09:15:39', '2026-05-29 09:15:39', 0, '2026-06-07', NULL, NULL, 17.71, 3.54, 21.25, 14.41, NULL, NULL, '2026-05-23 10:01:02', '2026-05-29 09:15:39', NULL),
(207, 280, 'micmo violet vtt', 'degrippage des freins + tube de selle \ndiag', 'on vous tient au courant pour le dégrippage du tube de selle. Je pense qu\'on devrait s\'en sortir sans trop passer de temps.\n\nJe ne le compte pas pour le moment, sinon, on vous tiens au courant.\n\nretour au 11 rue henri dunant ( a coté du cinéma / en face du cimetiere)\n06 86 91 92 95', NULL, 'atelier', '20260529-3', 'invoiced', '2026-05-29 09:14:11', '2026-05-29 09:14:11', 0, '2026-06-07', NULL, NULL, 25.83, 5.17, 31.00, 25.83, NULL, NULL, '2026-05-23 12:43:03', '2026-05-29 09:14:11', NULL),
(208, 285, 'Raymond de Lisle gris', 'diag general\n(voile , gaine/cable et...?)', NULL, NULL, 'atelier', '20260612-1', 'invoiced', '2026-06-12 14:44:56', NULL, 0, '2026-06-10', NULL, NULL, 128.50, 25.70, 154.20, 109.90, NULL, NULL, '2026-05-26 08:38:50', '2026-06-12 14:44:56', NULL),
(209, 286, 'scott noir orange', 'passage de vitesse problématique', 'prévoir commande de dérailleur\nrévision dérailleur + tension de chaine', NULL, 'atelier', '20260529-5', 'invoiced', '2026-05-29 20:55:10', '2026-05-29 20:55:10', 0, '2026-06-10', NULL, NULL, 16.67, 3.33, 20.00, 16.67, NULL, NULL, '2026-05-26 08:55:14', '2026-05-29 20:55:10', NULL),
(211, 291, 'Velo de ville blanc', 'frein AR (cable rompu) \nroue AV a resserrer,  frein a regler\nlampe a voir (si on peut reparer, verifier chaine)', NULL, NULL, 'atelier', '20260616-2', 'invoiced', '2026-06-16 08:59:51', NULL, 0, '2026-06-10', NULL, NULL, 164.87, 32.97, 197.84, 134.18, NULL, NULL, '2026-05-26 14:31:05', '2026-06-16 08:59:51', NULL),
(241, 339, 'specialized noir et rouge', 'diag passage de vitesse ?\nvoir diag autre.', 'chaine 10V groupe 105\npneu (AV/AR) 700x25c\npatin freins AR', NULL, 'atelier', '20260609-1', 'invoiced', '2026-06-09 08:38:33', '2026-06-09 00:00:00', 0, '2026-06-24', NULL, NULL, 32.50, 6.50, 39.00, 32.50, NULL, NULL, '2026-06-09 07:12:28', '2026-06-10 09:04:56', NULL),
(212, 292, 'Kalkhoff bleu', 'diag revision\n(vitesses / crevaison)', 'nous partons sur un simple réglage des freins.\nPour information, il s\'agit d\'un système hydraulique. Si le réglage ne suffit pas, nous proposerons une purge du liquide\n(33 euros par levier)', NULL, 'atelier', '20260606-1', 'invoiced', '2026-06-06 07:26:30', '2026-06-06 00:00:00', 0, '2026-06-10', NULL, NULL, 62.50, 12.50, 75.00, 62.50, NULL, NULL, '2026-05-26 15:11:25', '2026-06-10 09:05:14', NULL),
(213, 294, 'trekking kaibike gris', 'réglage dérailleur diag devis par mail\nPPAV', 'prévoir transmission\nplateau 38\ncassette 11 - 42 10V\nchaine 10 V', NULL, 'atelier', '20260529-2', 'invoiced', '2026-05-29 08:04:35', '2026-05-29 08:04:35', 0, '2026-06-11', NULL, NULL, 10.83, 2.17, 13.00, 10.83, NULL, NULL, '2026-05-27 08:07:32', '2026-05-29 08:04:35', NULL),
(214, 115, 'mbk greenfield light  blue', 'direction très dure devis par mail', NULL, NULL, 'atelier', '20260527-2', 'reception', NULL, NULL, 0, '2026-06-11', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-05-27 09:40:20', '2026-05-27 09:40:20', NULL),
(215, 295, 'peugeot bordeau', 'verification compatibilité moteurs, devis.', NULL, NULL, 'atelier', '20260527-3', 'validated', NULL, NULL, 0, '2026-06-11', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-05-27 12:41:46', '2026-06-15 07:26:27', NULL),
(216, 297, 'transport CCI vélo Luc', 'btwin gris baroudeur\ndépôt vendredi pour livraison a 17h à Bréhat', NULL, NULL, 'atelier', '20260527-4', 'done', NULL, NULL, 0, '2026-06-11', NULL, NULL, 6.67, 1.33, 8.00, 6.67, NULL, NULL, '2026-05-27 15:26:37', '2026-05-29 20:42:58', NULL),
(217, 20, 'Micmo noir cadre bas ville', 'diag revision', 'recherche velo musculaire léger cadre ouvert', NULL, 'atelier', '20260528-1', 'pending_validation', NULL, NULL, 0, '2026-06-12', NULL, NULL, 133.10, 26.61, 159.71, 103.26, NULL, NULL, '2026-05-28 09:27:10', '2026-06-05 08:58:20', '2026-06-05 08:58:20'),
(218, 300, 'Canion aeroad cd SL bleu blanc', '(2022 - 2023)\ndiag transmission\nvoir diag revision', NULL, NULL, 'atelier', '20260610-3', 'invoiced', '2026-06-10 14:52:21', NULL, 0, '2026-06-12', NULL, NULL, 332.45, 66.49, 398.94, 188.79, NULL, NULL, '2026-05-28 09:29:24', '2026-06-10 14:52:21', NULL),
(244, 346, 'casual noir ville', 'crevaison', NULL, NULL, 'atelier', '20260609-4', 'done', NULL, NULL, 0, '2026-06-24', NULL, NULL, 18.25, 3.65, 21.90, 14.85, NULL, NULL, '2026-06-09 12:45:12', '2026-06-11 09:17:47', NULL),
(245, 347, 'scott vert elec', 'diag complet', 'Le prix décidé sur le devis de l\'entretien moteur est basé sur le temps estimé. \nNous préviendrons en cas de problème lié à cette estimation.', NULL, 'atelier', '20260609-5', 'pending_validation', NULL, NULL, 0, '2026-06-24', NULL, NULL, 258.53, 51.72, 310.25, 142.43, NULL, NULL, '2026-06-09 14:50:04', '2026-06-16 21:06:51', NULL),
(246, 348, 'Neomouv Elaia 2NG rouge', 'Achat VAE', 'Numéro de cadre :  NM2024/015514\nNuméro de batterie : A2594D0014063\n\nEn bas de la page www.neomouv.com, un bouton noir vous indique où enregistrer votre garantie.', NULL, 'atelier', '20260611-1', 'invoiced', '2026-06-11 09:57:42', '2026-06-10 00:00:00', 0, '2026-06-24', NULL, NULL, 1666.67, 333.33, 2000.00, 450.16, NULL, NULL, '2026-06-09 14:51:14', '2026-06-11 09:58:01', NULL),
(219, 294, 'VTC Gris rouge elec', 'transmission HS\nprévoir transmission\nplateau 38\ncassette 11 - 42 10V\nchaine 10 V', 'il me faudrait une photo du pédalier pour lever un doute.', NULL, 'atelier', '20260529-1', 'to_complete', NULL, NULL, 0, '2026-06-13', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-05-29 08:05:11', '2026-05-29 20:47:31', NULL),
(220, 306, 'VTT bleu Megamo', 'diag revision', NULL, NULL, 'atelier', '20260529-2', 'done', NULL, NULL, 0, '2026-06-13', NULL, NULL, 21.67, 4.33, 26.00, 21.67, NULL, NULL, '2026-05-29 08:15:24', '2026-06-26 12:06:49', NULL),
(221, 307, 'orbea bleu blanc course', 'réglage dérailleur\ndiag', NULL, NULL, 'atelier', '20260530-1', 'invoiced', '2026-05-30 13:15:55', '2026-05-30 13:15:55', 0, '2026-06-13', NULL, NULL, 10.83, 2.17, 13.00, 10.83, NULL, NULL, '2026-05-29 12:10:43', '2026-05-30 13:15:55', NULL),
(222, 311, 'Alré cycle - jaune poussin', 'vélo route, diag revision', NULL, NULL, 'atelier', '20260529-4', 'done', NULL, NULL, 0, '2026-06-13', NULL, NULL, 135.67, 27.13, 162.80, 118.92, NULL, NULL, '2026-05-29 15:06:52', '2026-06-11 09:25:12', NULL),
(223, 312, 'Neomouv Enara 2 TK', 'devis pour Achat VAE Neomouv', NULL, NULL, 'atelier', '20260529-5', 'to_complete', NULL, NULL, 0, '2026-06-13', NULL, NULL, 1650.82, 330.17, 1980.99, 796.93, NULL, NULL, '2026-05-29 15:27:29', '2026-05-29 20:43:36', NULL),
(224, 307, 'orbea bleu blanc course', 'devis reparation', 'A confirmer avec Regis si l\'adresse mail est bien : 	lecamrem@live.fr', NULL, 'atelier', '20260606-4', 'invoiced', '2026-06-06 14:08:35', '2026-06-06 00:00:00', 0, '2026-06-13', NULL, NULL, 274.14, 54.83, 328.97, 105.48, NULL, NULL, '2026-05-29 15:41:29', '2026-06-10 09:05:04', NULL),
(227, 313, 'Raleigh Brighton noir', 'diagnostique remise en route', NULL, NULL, 'atelier', '20260617-5', 'invoiced', '2026-06-17 13:54:56', '2026-06-17 00:00:00', 0, '2026-06-14', NULL, NULL, 116.82, 23.36, 140.18, 79.32, NULL, NULL, '2026-05-30 06:26:20', '2026-06-17 13:55:30', NULL),
(228, 313, 'Orbea', 'diagnostique remise en route', NULL, NULL, 'atelier', '20260617-4', 'invoiced', '2026-06-17 13:54:46', '2026-06-17 00:00:00', 0, '2026-06-14', NULL, NULL, 138.33, 27.67, 166.00, 121.32, NULL, NULL, '2026-05-30 06:35:17', '2026-06-17 13:55:33', NULL),
(229, 316, 'torpado sportage blanc cadre moyen', 'pneu prio\ndiag devis mail', NULL, NULL, 'atelier', '20260620-1', 'invoiced', '2026-06-20 09:59:41', '2026-06-20 00:00:00', 0, '2026-06-14', NULL, NULL, 92.37, 18.47, 110.84, 77.75, NULL, NULL, '2026-05-30 14:53:48', '2026-06-20 18:49:01', NULL),
(230, 317, 'roue VTT 29', 'trou quelque part, retirer le préventif séché et renouveler.\nNettoyage cassette', NULL, 'Nous avons reçu du liquide préventif aujourd\'hui,  je tente de simplement faire la procédure (le trou causé par la pointe que nous avons vu ensemble ne devrait pas nous gêner.)\n\nSi tout se passe bien, ce devis est valide. \nsi jamais il faut changer le pneus, il faudra ajouter 45,99 pour un Hutchinson Wyrm 29x2.40', 'atelier', '20260602-1', 'validated', NULL, NULL, 0, '2026-06-17', NULL, NULL, 29.17, 5.83, 35.00, 27.57, NULL, NULL, '2026-06-02 14:09:11', '2026-06-28 19:07:52', NULL),
(231, 321, 'scott noir cadre haut', 'diag devis par mail', NULL, NULL, 'atelier', '20260611-4', 'invoiced', '2026-06-11 15:00:32', NULL, 0, '2026-06-18', NULL, NULL, 30.00, 6.00, 36.00, 30.00, NULL, NULL, '2026-06-03 09:21:12', '2026-06-11 15:00:32', NULL),
(232, 322, 'gitane', 'devis électrification moteur AR / pédalier préférable', 'Bonne nouvelle, le moteur pédalier est compatible avec votre vélo, \nIl sera toute fois nécessaire de retirer la béquille centrale. \nNous prévoyons de la remplacer par une béquille qui s\'installera plus en arrière sur le vélo.', NULL, 'atelier', '20260603-2', 'validated', NULL, NULL, 0, '2026-06-18', NULL, NULL, 1084.84, 216.96, 1301.80, 1077.94, NULL, NULL, '2026-06-03 09:58:54', '2026-06-18 09:05:15', NULL),
(233, 323, 'Garneau', 'conversion YosePower (kit fourni)', NULL, NULL, 'atelier', '20260618-2', 'invoiced', '2026-06-18 14:12:37', '2026-06-18 00:00:00', 0, '2026-06-18', NULL, NULL, 111.90, 22.38, 134.28, 93.46, NULL, NULL, '2026-06-03 13:24:20', '2026-06-20 18:49:14', NULL),
(235, 331, 'NCM Milano', 'achat d\'un Milano 48 reconditionné blanc', '740 réglé aujourd\'hui, \n50 par quelqu\'un d\'autre \"carte cadeau\"', NULL, 'atelier', '20260604-2', 'in_progress', NULL, NULL, 0, '2026-06-19', NULL, NULL, 658.33, 131.67, 790.00, 658.33, NULL, NULL, '2026-06-04 16:08:39', '2026-06-09 12:15:06', NULL),
(236, 202, 'top life noir cadre haut', 'devis revision', NULL, NULL, 'atelier', '20260605-1', 'to_quote', NULL, NULL, 0, '2026-06-20', NULL, NULL, 55.83, 11.17, 67.00, 55.83, NULL, NULL, '2026-06-05 08:00:32', '2026-06-05 08:00:40', NULL),
(237, 4, 'Engwe pliant', 'frein hydraulique à poser', NULL, NULL, 'atelier', '20260609-4', 'invoiced', '2026-06-09 15:46:35', '2026-06-09 00:00:00', 0, '2026-06-20', NULL, NULL, 73.40, 14.68, 88.08, 9.20, NULL, NULL, '2026-06-05 10:00:22', '2026-06-10 09:04:39', NULL),
(238, 332, 'Move vélo ville gris avec panier AR', 'lampe AV ne fonctionne pas', NULL, NULL, 'atelier', '20260605-3', 'reception', NULL, NULL, 0, '2026-06-20', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-06-05 13:48:29', '2026-06-05 13:48:29', NULL),
(239, 333, 'triban kaki', 'devis diag email\nplaquettes + transmission\ngarde boue ? https://www.decathlon.fr/p/garde-boue-sks-speedrocker-xl-route-et-gravel/386140/m9026065', NULL, NULL, 'atelier', '20260618-1', 'invoiced', '2026-06-18 09:27:04', '2026-06-18 00:00:00', 0, '2026-06-20', NULL, NULL, 190.99, 38.19, 229.18, 119.64, NULL, NULL, '2026-06-05 14:37:56', '2026-06-20 18:49:16', NULL),
(240, 335, 'batterie selle ENERGY     vélo cycladin', 'reconditionnement? prix\nchanger batterie , prix?', NULL, NULL, 'atelier', '20260606-1', 'reception', NULL, NULL, 0, '2026-06-21', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-06-06 08:39:20', '2026-06-06 08:39:20', NULL),
(242, 341, 'Canondale Trekking noir', 'purge frein AR (probleme de bouchon ?)', NULL, NULL, 'atelier', '20260609-3', 'invoiced', '2026-06-09 12:41:55', '2026-06-09 00:00:00', 0, '2026-06-24', NULL, NULL, 33.33, 6.67, 40.00, 33.33, NULL, NULL, '2026-06-09 08:29:11', '2026-06-10 09:04:45', NULL),
(243, 342, 'scott reglage derailleur', 'diag', NULL, NULL, 'atelier', '20260609-2', 'invoiced', '2026-06-09 09:02:22', '2026-06-09 00:00:00', 0, '2026-06-24', NULL, NULL, 10.83, 2.17, 13.00, 10.83, NULL, NULL, '2026-06-09 09:02:13', '2026-06-10 09:04:58', NULL),
(247, 351, 'La Pierre VTT gris', 'diag revision \n- freinage : pistons qui bloquent', NULL, NULL, 'atelier', '20260609-7', 'pending_validation', NULL, NULL, 1, '2026-06-24', NULL, NULL, 51.67, 10.33, 62.00, 51.67, NULL, NULL, '2026-06-09 15:15:32', '2026-06-26 12:25:03', NULL),
(248, 352, 'nakamura jaune fluo', 'roue AR a changer\nroue AV tres gros devoilage (defi)\npoignée frein ar a changer\nchaine', 'On va essayer de vous redressé la poignée de frein (offert) on ne garanti pas forcement le resultat et on avisera à ce moment là', NULL, 'atelier', '20260609-8', 'pending_validation', NULL, NULL, 0, '2026-06-24', NULL, NULL, 181.48, 36.30, 217.78, 113.39, NULL, NULL, '2026-06-09 15:24:27', '2026-06-24 14:39:00', NULL),
(249, 353, 'Haibike sduro Bleu blanc', 'roue AR très voilée, \ndiag general', 'le graissage transmission est offert', NULL, 'atelier', '20260624-1', 'invoiced', '2026-06-24 09:06:27', '2026-06-24 00:00:00', 0, '2026-06-25', NULL, NULL, 106.00, 21.20, 127.20, 55.00, NULL, NULL, '2026-06-10 08:50:00', '2026-06-24 13:22:57', NULL),
(250, 354, 'client', 'chambre a air', 'réglé en liquide le 10/06/26', NULL, 'atelier', '20260616-5', 'invoiced', '2026-06-16 20:37:20', NULL, 0, '2026-06-25', NULL, NULL, 2.92, 0.58, 3.50, 1.28, NULL, NULL, '2026-06-10 12:53:07', '2026-06-16 20:37:20', NULL),
(251, 357, 'top life  gris cadre bas VAE', 'soucis elec (peut être capteur pédalage)\nvoir prix carter chaine\ncrevaison lente AR\nbequille\ndiag révision devis par mail', 'Concernant le carter de chaine, il nous faudra le vélo pour aller voir ce qu\'on peut trouver qui s\'adaptera bien, nous vous informerons du prix de cette pièce à ce moment là', NULL, 'atelier', '20260626-2', 'invoiced', '2026-06-26 16:02:05', '2026-06-26 00:00:00', 0, '2026-06-26', NULL, NULL, 105.35, 21.06, 126.41, 80.20, NULL, NULL, '2026-06-11 08:57:19', '2026-06-26 16:02:10', NULL),
(252, 359, 'vente d\'une lampe axa CL35-e', '524796', NULL, NULL, 'atelier', '20260611-2', 'invoiced', '2026-06-11 12:40:15', NULL, 0, '2026-06-26', NULL, NULL, 22.50, 4.50, 27.00, 9.00, NULL, NULL, '2026-06-11 12:39:41', '2026-06-11 12:40:15', NULL),
(253, 360, 'achat antivol', 'antivol AXA (la corde)', NULL, NULL, 'atelier', '20260613-3', 'invoiced', '2026-06-13 15:48:55', NULL, 0, '2026-06-26', NULL, NULL, 19.92, 3.98, 23.90, 7.02, NULL, NULL, '2026-06-11 12:45:37', '2026-06-13 15:48:55', NULL),
(275, 8, 'VAE rockrider noir', 'transmission hs , manette derailleur cassée', NULL, NULL, 'atelier', '20260618-4', 'validated', NULL, NULL, 0, '2026-07-03', NULL, NULL, 152.24, 30.46, 182.70, 55.94, NULL, NULL, '2026-06-18 13:11:38', '2026-06-26 12:08:04', NULL),
(254, 53, 'achat pneus', '700x42', NULL, NULL, 'atelier', '20260611-3', 'invoiced', '2026-06-11 13:40:30', NULL, 0, '2026-06-26', NULL, NULL, 13.66, 2.73, 16.39, 1.46, NULL, NULL, '2026-06-11 13:40:24', '2026-06-11 13:40:30', NULL),
(255, 361, 'pegasus rouge', 'crevaison', NULL, NULL, 'atelier', '20260616-1', 'invoiced', '2026-06-16 08:57:16', NULL, 0, '2026-06-26', NULL, NULL, 21.58, 4.32, 25.90, 15.88, NULL, NULL, '2026-06-11 14:59:05', '2026-06-16 08:57:16', NULL),
(256, 363, 'Mercier vert gris ville ancien', 'diagnostique remise en route.', NULL, NULL, 'atelier', '20260612-1', 'to_complete', NULL, NULL, 0, '2026-06-27', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-06-12 08:59:24', '2026-06-19 12:58:53', NULL),
(257, 363, 'A DECE OASIS pliant creme', 'probleme de coupure electrique non stop.', NULL, NULL, 'atelier', '20260612-2', 'reception', NULL, NULL, 0, '2026-06-27', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-06-12 09:00:13', '2026-06-12 09:00:13', NULL),
(258, 364, 'client casqueMAVIC taille L Vert', 'client casque', NULL, NULL, 'atelier', '20260613-2', 'invoiced', '2026-06-13 12:50:26', NULL, 0, '2026-06-28', NULL, NULL, 132.50, 26.50, 159.00, 132.50, NULL, NULL, '2026-06-13 12:50:19', '2026-06-13 12:50:26', NULL),
(259, 365, 'le velo breton ville cadre bas blanc', 'derailleur plateau grippé', NULL, NULL, 'atelier', '20260625-2', 'invoiced', '2026-06-25 06:19:49', NULL, 0, '2026-06-28', NULL, NULL, 22.79, 4.56, 27.35, 16.33, NULL, NULL, '2026-06-13 13:23:39', '2026-06-25 06:19:49', NULL),
(260, 366, 'neomouv gris cadre bas VAE', 'diag revision devis par mail\nfixation support de carter de chaine (colson a serrer tres fort + peut etre point de colle)\npatins de freins av ar\nderaillement plateau\nchanger chaine', NULL, NULL, 'atelier', '20260613-3', 'done', NULL, NULL, 0, '2026-06-28', NULL, NULL, 64.17, 12.83, 77.00, 56.43, NULL, NULL, '2026-06-13 14:03:36', '2026-06-27 13:04:27', NULL),
(261, 367, 'EOvolt', 'diag devis', NULL, NULL, 'atelier', '20260613-4', 'to_quote', NULL, NULL, 0, '2026-06-28', NULL, NULL, 70.00, 14.00, 84.00, 70.00, NULL, NULL, '2026-06-13 14:31:30', '2026-06-15 06:40:09', NULL),
(263, 370, 'moma bike', 'a déposé sa batterie pour voir si on peut en avoir une d\'une autre marque (chargeur également)', NULL, NULL, 'atelier', '20260616-2', 'reception', NULL, NULL, 0, '2026-07-01', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-06-16 09:06:15', '2026-06-16 09:06:15', NULL),
(262, 177, 'roue de VAE voilée', 'urban biker Dakota', NULL, NULL, 'atelier', '20260616-1', 'to_complete', NULL, NULL, 0, '2026-07-01', NULL, NULL, 70.00, 14.00, 84.00, 34.95, NULL, NULL, '2026-06-16 07:19:34', '2026-06-25 06:13:11', NULL),
(264, 372, 'vert et orange', 'devis remplacement chaine', NULL, NULL, 'atelier', '20260616-3', 'pending_validation', NULL, NULL, 0, '2026-07-01', NULL, NULL, 73.39, 14.67, 88.06, 66.69, NULL, NULL, '2026-06-16 12:05:42', '2026-06-22 05:45:31', NULL),
(265, 373, 'nakamura gris enfant', 'diag devis', NULL, NULL, 'atelier', '20260618-5', 'invoiced', '2026-06-18 16:06:43', '2026-06-18 00:00:00', 0, '2026-07-01', NULL, NULL, 56.67, 11.33, 68.00, 56.67, NULL, NULL, '2026-06-16 12:07:44', '2026-06-20 18:49:06', NULL),
(266, 375, 'itinerant', 'commabnde derailleur very hard', NULL, NULL, 'atelier', '20260616-3', 'invoiced', '2026-06-16 13:00:05', NULL, 0, '2026-07-01', NULL, NULL, 16.67, 3.33, 20.00, 16.67, NULL, NULL, '2026-06-16 12:59:52', '2026-06-16 13:00:05', NULL),
(267, 376, 'VTT giant blanc', 'pédale cassée', NULL, NULL, 'atelier', '20260623-1', 'invoiced', '2026-06-23 15:42:57', '2026-06-23 00:00:00', 0, '2026-07-01', NULL, NULL, 10.42, 2.08, 12.50, 7.12, NULL, NULL, '2026-06-16 14:38:56', '2026-06-24 13:22:39', NULL),
(273, 387, 'chargeur neomouv HS', 'photo dans le telephone (jo)', NULL, NULL, 'atelier', '20260618-2', 'in_progress', NULL, NULL, 0, '2026-07-03', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-06-18 12:24:41', '2026-06-26 11:38:59', NULL),
(274, 388, 'Neomouv Montana noir', 'moteur ne se lance plus', NULL, NULL, 'atelier', '20260622-1', 'invoiced', '2026-06-22 05:43:39', '2026-06-22 00:00:00', 0, '2026-07-03', NULL, NULL, 25.00, 5.00, 30.00, 25.00, NULL, NULL, '2026-06-18 12:50:26', '2026-06-24 13:22:36', NULL),
(282, 403, 'de passage', 'achat chambre a air', 'payé en cb', NULL, 'atelier', '20260620-1', 'done', NULL, NULL, 0, '2026-07-05', NULL, NULL, 5.75, 1.15, 6.90, 5.75, NULL, NULL, '2026-06-20 14:05:55', '2026-06-20 14:06:05', NULL),
(268, 378, 'Vtc SCOTT gris moteur central', 'diag revision', NULL, NULL, 'atelier', '20260625-3', 'invoiced', '2026-06-25 08:09:06', '2026-06-25 00:00:00', 0, '2026-07-02', NULL, NULL, 77.08, 15.42, 92.50, 77.08, NULL, NULL, '2026-06-17 09:26:22', '2026-06-25 08:10:49', NULL),
(269, 379, 'Kalkhoff gris', 'réglage derailleur', NULL, NULL, 'atelier', '20260617-1', 'invoiced', '2026-06-17 12:45:15', NULL, 0, '2026-07-02', NULL, NULL, 10.83, 2.17, 13.00, 10.83, NULL, NULL, '2026-06-17 12:07:36', '2026-06-17 12:45:15', NULL),
(270, 381, 'Mr glory', 'crevaison av', NULL, NULL, 'atelier', '20260617-2', 'invoiced', '2026-06-17 13:34:02', NULL, 0, '2026-07-02', NULL, NULL, 6.66, 1.33, 7.99, 1.36, NULL, NULL, '2026-06-17 13:32:54', '2026-06-17 13:34:02', NULL),
(271, 382, 'VAE voisin', 'installation rétroviseur', NULL, NULL, 'atelier', '20260617-3', 'invoiced', '2026-06-17 13:35:18', '2026-06-17 00:00:00', 0, '2026-07-02', NULL, NULL, 8.33, 1.67, 10.00, 3.21, NULL, NULL, '2026-06-17 13:34:49', '2026-06-17 13:35:25', NULL),
(272, 159, 'Trek Gris', 'installation cintre + guido + fond de jante, transformation en tubless\ndebloquage + nettoyage piston AR', NULL, NULL, 'atelier', '20260618-1', 'validated', NULL, NULL, 0, '2026-07-03', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-06-18 09:44:48', '2026-06-23 10:02:41', NULL),
(285, 413, 'Btwin bleu ville cadre bas VAE', 'diag revision mail\nchaine\nderaillement plateau a surveiller', NULL, NULL, 'atelier', '20260623-2', 'pending_validation', NULL, NULL, 0, '2026-07-08', NULL, NULL, 72.50, 14.50, 87.00, 68.56, NULL, NULL, '2026-06-23 14:59:54', '2026-06-25 06:14:36', NULL),
(276, 234, 'fahrad', 'plateau mal positionné et patins arrière qui grincent', NULL, NULL, 'atelier', '20260619-1', 'done', NULL, NULL, 0, '2026-07-04', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-06-19 08:48:25', '2026-06-26 11:39:25', '2026-06-26 11:39:25'),
(277, 393, '\"velo de ville\" rouge 1 et 2', 'diag revision', NULL, NULL, 'atelier', '20260625-5', 'invoiced', '2026-06-25 13:01:12', '2026-06-25 00:00:00', 0, '2026-07-04', NULL, NULL, 79.58, 15.92, 95.50, 75.68, NULL, NULL, '2026-06-19 09:29:37', '2026-06-25 13:01:21', NULL),
(278, 394, 'velo bleu vtt', 'dévoilage + placement pneu', NULL, NULL, 'atelier', '20260619-1', 'invoiced', '2026-06-19 12:15:52', '2026-06-19 00:00:00', 0, '2026-07-04', NULL, NULL, 15.00, 3.00, 18.00, 15.00, NULL, NULL, '2026-06-19 12:15:44', '2026-06-20 18:49:03', NULL),
(279, 395, 'vtt bleu vae', 'devis changement pneus', NULL, 'On a deux modèles de pneus à vous proposer\nl\'un à 22.9 l\'unité et l\'autre à 35.99 l\'unité.\n\nJe peux vous les montrer à l\'atelier pour vous puissiez faire votre choix', 'atelier', '20260619-4', 'pending_validation', NULL, NULL, 0, '2026-07-04', NULL, NULL, 21.67, 4.33, 26.00, 21.67, NULL, NULL, '2026-06-19 12:17:21', '2026-06-23 09:02:50', NULL),
(280, 396, 'Btwin gris', 'crevaison', NULL, NULL, 'atelier', '20260619-5', 'done', NULL, NULL, 0, '2026-07-04', NULL, NULL, 16.25, 3.25, 19.50, 16.25, NULL, NULL, '2026-06-19 13:35:57', '2026-06-23 08:55:16', NULL),
(281, 397, 'matra noir', 'i-flow avec display hs\n\nnuméro de matra france : \n02 33 77 25 78\n02 33 77 25 77', NULL, NULL, 'atelier', '20260619-6', 'reception', NULL, NULL, 0, '2026-07-04', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-06-19 15:28:15', '2026-06-19 15:28:15', NULL),
(283, 407, 'Elaia 2 NG Neomouv + Riverside 500 LVA', 'achat VAE + VTC', 'le Riverside 500 est équipé de :\n- transmission neuve (manette, dérailleur, cassette, pédalier)\n- kit de frein hydraulique Clarks neuf\n- potence haute\n- pneu Tundra 700X45\n-porte bagage\n-garde boue', 'Les informations à reporter pour la garantie de l\'Elaia 2 NG Neomouv sont : \n\nELAIA 2 NG 26\' T45 S/B ROUGE V2 : A2599D0012968\nBATTERIE NM-PRO2 522Wh 36V : NM2024/015521\n\nPour enregistrer la garantie : rendez-vous sur le site neomouv.com.\nen bas de page : le bouton \"Enregistrement de la garantie\"\nEn bas de besoin, je me tiens disponible pour aider.', 'atelier', '20260624-2', 'invoiced', '2026-06-24 13:06:19', '2026-06-20 00:00:00', 0, '2026-07-05', NULL, NULL, 2583.33, 516.67, 3100.00, 1025.85, NULL, NULL, '2026-06-22 05:47:02', '2026-06-24 13:22:48', NULL),
(294, 351, 'VTT Lapierre gris blanc', 'dégrippage piston etrier', NULL, NULL, 'atelier', '20260626-1', 'in_progress', NULL, NULL, 0, '2026-07-11', NULL, NULL, 21.67, 4.33, 26.00, 21.67, NULL, NULL, '2026-06-26 12:25:36', '2026-06-28 19:26:18', NULL),
(284, 411, 'gitane bleu nuitcadre bas ville debut aout maximum fini', 'diag devis mail\n\nlumiere a ajouter\nselle confort\ncommande de derailleur\nvoir si redressage possible du BG AV', NULL, NULL, 'atelier', '20260623-1', 'to_quote', NULL, NULL, 0, '2026-07-08', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-06-23 13:30:24', '2026-06-24 08:43:44', NULL),
(286, 414, 'de passage', 'reglage derailleur', NULL, NULL, 'atelier', '20260623-2', 'invoiced', '2026-06-23 15:43:51', '2026-06-23 00:00:00', 0, '2026-07-08', NULL, NULL, 10.83, 2.17, 13.00, 10.83, NULL, NULL, '2026-06-23 15:43:47', '2026-06-24 13:22:43', NULL),
(287, 415, 'Passage GT violet', 'pneus AR crevaison', NULL, NULL, 'atelier', '20260625-6', 'invoiced', '2026-06-25 15:58:10', '2026-06-25 00:00:00', 0, '2026-07-09', NULL, NULL, 17.49, 3.50, 20.99, 15.04, NULL, NULL, '2026-06-24 06:44:05', '2026-06-25 15:58:30', NULL),
(288, 416, 'cube gris foncé cadre bas VAE', 'chaine cassée', NULL, NULL, 'atelier', '20260624-3', 'invoiced', '2026-06-24 15:52:19', '2026-06-24 00:00:00', 0, '2026-07-09', NULL, NULL, 44.17, 8.83, 53.00, 28.41, NULL, NULL, '2026-06-24 07:08:05', '2026-06-25 08:10:57', NULL),
(289, 353, 'moustache noir cadre bas', 'diag devis mail\n\nbequille adaptable auban pour le haybike', NULL, NULL, 'atelier', '20260624-3', 'validated', NULL, NULL, 0, '2026-07-09', NULL, NULL, 132.40, 26.47, 158.87, 77.37, NULL, NULL, '2026-06-24 09:13:34', '2026-06-28 19:26:30', NULL),
(290, 419, 'cannondale noir cadre bas VAE', 'rayon ar cassé prio\ndiag revision devis mail', NULL, NULL, 'atelier', '20260625-1', 'invoiced', '2026-06-25 06:08:02', '2026-06-24 00:00:00', 0, '2026-07-09', NULL, NULL, 27.50, 5.50, 33.00, 27.50, NULL, NULL, '2026-06-24 09:21:48', '2026-06-25 08:10:55', NULL),
(299, 435, 'talbot rouge cadre haut ancien', 'diag revision devis mail', NULL, NULL, 'atelier', '20260627-3', 'reception', NULL, NULL, 0, '2026-07-12', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-06-27 13:50:49', '2026-06-27 13:50:49', NULL),
(291, 422, 'batterie lapierre', 'depose de batterie pour voir si reparable ou echangeable\ncomparaison de prix', NULL, NULL, 'atelier', '20260625-1', 'reception', NULL, NULL, 0, '2026-07-10', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-06-25 07:46:14', '2026-06-25 08:11:35', NULL),
(292, 426, 'Top Life ville', 'deraille, frein AR', NULL, NULL, 'atelier', '20260625-4', 'invoiced', '2026-06-25 12:55:29', '2026-06-25 00:00:00', 0, '2026-07-10', NULL, NULL, 21.66, 4.34, 26.00, 21.66, NULL, NULL, '2026-06-25 12:55:21', '2026-06-25 12:55:37', NULL),
(293, 428, 'Giant bleu trekking', 'diag revision', 'Le dévoilage AV/AR est léger, nous ne comptons le prix que pour un seul.', NULL, 'atelier', '20260625-3', 'validated', NULL, NULL, 0, '2026-07-10', NULL, NULL, 119.98, 24.00, 143.98, 80.27, NULL, NULL, '2026-06-25 14:14:52', '2026-06-26 11:36:58', NULL),
(295, 431, 'Time route (noir carbone)', 'verification compatibilité virvolt 750', NULL, NULL, 'atelier', '20260626-2', 'reception', NULL, NULL, 0, '2026-07-11', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-06-26 12:57:52', '2026-06-26 12:57:52', NULL),
(296, 432, 'NCM Paris blanc', 'Achat VAE NCM', NULL, NULL, 'atelier', '20260626-3', 'done', NULL, NULL, 0, '2026-07-11', NULL, NULL, 816.67, 163.33, 980.00, 236.67, NULL, NULL, '2026-06-26 15:42:19', '2026-06-26 16:03:19', NULL),
(297, 433, 'gitane crème cadre bas semi course', 'diag devis par mail', NULL, NULL, 'atelier', '20260627-1', 'reception', NULL, NULL, 0, '2026-07-12', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-06-27 08:41:38', '2026-06-27 08:41:38', NULL),
(298, 434, 'nakamura VAE gris cadre bas moteur central', 'probleme de lancement moteur / batterie\ndiag revision devis par mail', NULL, NULL, 'atelier', '20260627-2', 'reception', NULL, NULL, 0, '2026-07-12', NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-06-27 09:25:32', '2026-06-27 09:25:32', NULL),
(300, 437, 'de passage', 'crevaison ar', NULL, NULL, 'atelier', '20260627-4', 'reception', NULL, NULL, 0, '2026-07-12', NULL, NULL, 17.49, 3.50, 20.99, 17.49, NULL, NULL, '2026-06-27 16:33:39', '2026-06-27 16:33:39', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `quote_lines`
--

CREATE TABLE `quote_lines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `article_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quote_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
  `purchase_price_ht` decimal(10,2) DEFAULT NULL,
  `sale_price_ht` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sale_price_ttc` decimal(10,2) NOT NULL DEFAULT 0.00,
  `margin_amount_ht` decimal(10,2) DEFAULT NULL,
  `margin_rate` decimal(7,4) DEFAULT NULL,
  `line_purchase_ht` decimal(10,2) DEFAULT NULL,
  `line_margin_ht` decimal(10,2) DEFAULT NULL,
  `line_total_ht` decimal(10,2) DEFAULT NULL,
  `line_total_ttc` decimal(10,2) DEFAULT NULL,
  `tva_rate` decimal(7,4) NOT NULL DEFAULT 20.0000,
  `position` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `estimated_time_minutes` int(10) UNSIGNED DEFAULT NULL,
  `needs_order` tinyint(1) NOT NULL DEFAULT 0,
  `ordered_at` timestamp NULL DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `quote_lines`
--

INSERT INTO `quote_lines` (`id`, `article_id`, `quote_id`, `title`, `reference`, `quantity`, `purchase_price_ht`, `sale_price_ht`, `sale_price_ttc`, `margin_amount_ht`, `margin_rate`, `line_purchase_ht`, `line_margin_ht`, `line_total_ht`, `line_total_ttc`, `tva_rate`, `position`, `estimated_time_minutes`, `needs_order`, `ordered_at`, `received_at`, `created_at`, `updated_at`) VALUES
(150, NULL, 1, 'chaine + installation', '', 1.00, 3.94, 25.00, 30.00, 21.06, 84.2400, 3.94, 21.06, 25.00, 30.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-01-30 16:10:20', '2026-01-30 16:10:20'),
(151, NULL, 1, 'devoilage', '', 2.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-01-30 16:10:20', '2026-01-30 16:10:20'),
(152, NULL, 1, 'réglage frein AV', '', 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-01-30 16:10:20', '2026-01-30 16:10:20'),
(153, NULL, 1, 'gaine cable freins AR', '', 1.00, 0.00, 11.66, 14.00, 11.66, 100.0000, 0.00, 11.66, 11.66, 14.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-01-30 16:10:20', '2026-01-30 16:10:20'),
(154, NULL, 1, 'reglage derailleur AR', '', 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-01-30 16:10:20', '2026-01-30 16:10:20'),
(791, NULL, 2, 'Chaine + installation', NULL, 1.00, 3.94, 25.00, 30.00, 21.06, 84.2400, 3.94, 21.06, 25.00, 30.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-28 15:12:25', '2026-03-28 15:12:25'),
(792, NULL, 2, 'Cable + gaine freins', NULL, 2.00, 1.35, 11.67, 14.00, 10.32, 88.4286, 2.70, 20.63, 23.33, 28.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-03-28 15:12:25', '2026-03-28 15:12:25'),
(793, NULL, 2, 'dévoilage', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-03-28 15:12:25', '2026-03-28 15:12:25'),
(794, NULL, 2, 'Cable + gaine dérailleur AR', NULL, 1.00, 1.75, 11.66, 14.00, 9.91, 85.0000, 1.75, 9.91, 11.66, 14.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-03-28 15:12:25', '2026-03-28 15:12:25'),
(795, NULL, 2, 'béquille', '517522', 1.00, 8.00, 13.33, 16.00, 5.33, 40.0000, 8.00, 5.33, 13.33, 16.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-03-28 15:12:25', '2026-03-28 15:12:25'),
(790, NULL, 3, 'gaine + cable frein AR', NULL, 1.00, 1.35, 11.66, 14.00, 10.31, 88.4286, 1.35, 10.31, 11.66, 14.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-03-28 15:12:11', '2026-03-28 15:12:11'),
(789, NULL, 3, 'pneus AR', '494911', 1.00, 0.00, 10.82, 12.99, 10.82, 100.0000, 0.00, 10.82, 10.82, 12.99, 20.0000, 4, NULL, 0, NULL, NULL, '2026-03-28 15:12:11', '2026-03-28 15:12:11'),
(788, NULL, 3, 'réglage dérailleur', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.66, 21.66, 26.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-03-28 15:12:11', '2026-03-28 15:12:11'),
(787, NULL, 3, 'réglage frein AV', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-03-28 15:12:11', '2026-03-28 15:12:11'),
(786, NULL, 3, 'dévoilage', NULL, 2.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-03-28 15:12:11', '2026-03-28 15:12:11'),
(785, NULL, 3, 'chaine + installation', NULL, 1.00, 3.94, 25.00, 30.00, 21.06, 84.2400, 3.94, 21.06, 25.00, 30.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-28 15:12:11', '2026-03-28 15:12:11'),
(68, NULL, 4, 'réglage dérailleur AR', '', 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-01-26 17:28:55', '2026-01-26 17:28:55'),
(67, NULL, 4, 'réglage frein', '', 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-01-26 17:28:55', '2026-01-26 17:28:55'),
(66, NULL, 4, 'pneus', '517900', 1.00, 17.50, 29.16, 35.00, 11.66, 40.0000, 17.50, 11.66, 29.16, 35.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-01-26 17:28:55', '2026-01-26 17:28:55'),
(65, NULL, 4, 'poignées', '493202', 1.00, 7.45, 12.90, 15.49, 5.45, 42.2853, 7.45, 5.45, 12.90, 15.49, 20.0000, 3, NULL, 0, NULL, NULL, '2026-01-26 17:28:55', '2026-01-26 17:28:55'),
(64, NULL, 4, 'chaine + installation', '', 1.00, 3.94, 25.00, 30.00, 21.06, 84.2400, 3.94, 21.06, 25.00, 30.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-01-26 17:28:55', '2026-01-26 17:28:55'),
(63, NULL, 4, 'plateau x3 + installation', '495805', 1.00, 21.43, 49.16, 59.00, 27.73, 56.4136, 21.43, 27.73, 49.16, 59.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-01-26 17:28:55', '2026-01-26 17:28:55'),
(62, NULL, 4, 'roue libre + installation', '479780', 1.00, 9.50, 20.83, 25.00, 11.33, 54.4000, 9.50, 11.33, 20.83, 25.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-01-26 17:28:55', '2026-01-26 17:28:55'),
(69, NULL, 4, 'réglage jeu roulement AR', '', 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 7, NULL, 0, NULL, NULL, '2026-01-26 17:28:55', '2026-01-26 17:28:55'),
(91, NULL, 5, 'roue libre', '513608', 1.00, 7.00, 20.83, 25.00, 13.83, 66.4000, 7.00, 13.83, 20.83, 25.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-01-27 15:43:15', '2026-01-27 15:43:15'),
(90, NULL, 5, 'chaine + installation', '', 1.00, 3.75, 25.00, 30.00, 21.25, 85.0000, 3.75, 21.25, 25.00, 30.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-01-27 15:43:15', '2026-01-27 15:43:15'),
(89, NULL, 5, 'pair de patins', '', 2.00, 1.28, 4.08, 4.90, 2.80, 68.6531, 2.56, 5.60, 8.16, 9.80, 20.0000, 2, NULL, 0, NULL, NULL, '2026-01-27 15:43:15', '2026-01-27 15:43:15'),
(88, NULL, 5, 'dévoilage', '', 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-01-27 15:43:15', '2026-01-27 15:43:15'),
(87, NULL, 5, 'cable + gaine frein', '', 1.00, 2.80, 23.33, 28.00, 20.53, 88.0000, 2.80, 20.53, 23.33, 28.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-01-27 15:43:15', '2026-01-27 15:43:15'),
(92, NULL, 5, 'plateau 38d', '538114', 12.50, 0.00, 2.00, 2.40, 2.00, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-01-27 15:43:15', '2026-01-27 15:43:15'),
(93, NULL, 5, 'pneus', '479163', 6.10, 0.00, 2.39, 2.87, 2.39, 100.0000, 0.00, 14.57, 14.57, 17.49, 20.0000, 6, NULL, 0, NULL, NULL, '2026-01-27 15:43:15', '2026-01-27 15:43:15'),
(99, NULL, 6, 'valve tubless', '', 2.00, 1.30, 8.25, 9.90, 6.95, 84.2424, 2.60, 13.90, 16.50, 19.80, 20.0000, 2, NULL, 0, NULL, NULL, '2026-01-27 15:51:13', '2026-01-27 15:51:13'),
(98, NULL, 6, 'disque 180', '', 2.00, 7.00, 17.50, 21.00, 10.50, 60.0000, 14.00, 21.00, 35.00, 42.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-01-27 15:51:13', '2026-01-27 15:51:13'),
(97, NULL, 6, 'Cassette 11x51 12V', '', 1.00, 33.60, 95.00, 114.00, 61.40, 64.6316, 33.60, 61.40, 95.00, 114.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-01-27 15:51:13', '2026-01-27 15:51:13'),
(100, NULL, 7, 'plaquettes', '', 2.00, 2.38, 8.17, 9.80, 5.79, 70.8571, 4.76, 11.57, 16.33, 19.60, 20.0000, 0, NULL, 0, NULL, NULL, '2026-01-27 15:53:37', '2026-01-27 15:53:37'),
(101, NULL, 7, 'MO', '', 1.00, 0.00, 8.33, 10.00, 8.33, 100.0000, 0.00, 8.33, 8.33, 10.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-01-27 15:53:37', '2026-01-27 15:53:37'),
(102, NULL, 8, 'remplacement frein AR', '506240', 1.00, 22.50, 41.66, 50.00, 19.16, 46.0000, 22.50, 19.16, 41.66, 50.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-01-27 15:59:16', '2026-01-27 15:59:16'),
(103, NULL, 9, 'chaine + installation', '', 1.00, 13.43, 20.83, 25.00, 7.40, 35.5360, 13.43, 7.40, 20.83, 25.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-01-27 16:05:23', '2026-01-27 16:05:23'),
(104, NULL, 9, 'remplacement patins AR', '', 1.00, 1.67, 2.91, 3.50, 1.24, 42.7429, 1.67, 1.24, 2.91, 3.50, 20.0000, 1, NULL, 0, NULL, NULL, '2026-01-27 16:05:23', '2026-01-27 16:05:23'),
(105, NULL, 9, 'réglages freins', '', 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.66, 21.66, 26.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-01-27 16:05:23', '2026-01-27 16:05:23'),
(106, NULL, 10, 'remplacement plaquettes', '', 2.00, 3.94, 7.92, 9.50, 3.98, 50.2316, 7.88, 7.95, 15.83, 19.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-01-27 16:33:17', '2026-01-27 16:33:17'),
(107, NULL, 10, 'réglage freins', '', 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.66, 21.66, 26.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-01-27 16:33:17', '2026-01-27 16:33:17'),
(108, NULL, 10, 'chaine', '', 1.00, 3.94, 16.66, 20.00, 12.72, 76.3600, 3.94, 12.72, 16.66, 20.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-01-27 16:33:17', '2026-01-27 16:33:17'),
(109, NULL, 10, 'gaine + cable freins AR', '', 1.35, 0.00, 8.64, 10.37, 8.64, 100.0000, 0.00, 11.66, 11.66, 14.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-01-27 16:33:17', '2026-01-27 16:33:17'),
(110, NULL, 11, 'devoilage', '', 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-01-27 16:34:34', '2026-01-27 16:34:34'),
(111, NULL, 11, 'réglage frein AR', '', 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-01-27 16:34:34', '2026-01-27 16:34:34'),
(112, NULL, 11, 'réglage derailleur', '', 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-01-27 16:34:34', '2026-01-27 16:34:34'),
(113, NULL, 12, 'installation freinage hydraulique', '', 2.00, 0.00, 50.00, 60.00, 50.00, 100.0000, 0.00, 100.00, 100.00, 120.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-01-27 16:36:27', '2026-01-27 16:36:27'),
(114, NULL, 12, 'pneus', '', 2.00, 7.00, 23.33, 28.00, 16.33, 70.0000, 14.00, 32.66, 46.66, 56.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-01-27 16:36:27', '2026-01-27 16:36:27'),
(115, NULL, 13, 'axe pedalier + installation', '532765', 1.00, 17.97, 27.40, 32.89, 9.43, 34.4360, 17.97, 9.43, 27.40, 32.89, 20.0000, 0, NULL, 0, NULL, NULL, '2026-01-29 13:27:40', '2026-01-29 13:27:40'),
(116, NULL, 13, 'dévoilage', '', 2.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-01-29 13:27:40', '2026-01-29 13:27:40'),
(117, NULL, 13, 'chaine 9v + installation', '536048', 1.00, 8.40, 25.00, 30.00, 16.60, 66.4000, 8.40, 16.60, 25.00, 30.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-01-29 13:27:40', '2026-01-29 13:27:40'),
(118, NULL, 13, 'purge', '', 2.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 50.00, 50.00, 60.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-01-29 13:27:40', '2026-01-29 13:27:40'),
(119, NULL, 13, 'pédales', '7226', 1.00, 2.46, 9.16, 11.00, 6.70, 73.1636, 2.46, 6.70, 9.16, 11.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-01-29 13:27:40', '2026-01-29 13:27:40'),
(120, NULL, 13, 'poignées', '506160', 1.00, 1.50, 3.75, 4.50, 2.25, 60.0000, 1.50, 2.25, 3.75, 4.50, 20.0000, 5, NULL, 0, NULL, NULL, '2026-01-29 13:27:40', '2026-01-29 13:27:40'),
(197, NULL, 14, 'manette gauche', '488756', 1.00, 11.38, 15.75, 18.90, 4.36, 27.7460, 11.38, 4.36, 15.75, 18.90, 20.0000, 1, NULL, 0, NULL, NULL, '2026-02-04 10:43:51', '2026-02-04 10:43:51'),
(198, NULL, 14, 'gaine et cable derailleur', NULL, 2.00, 1.35, 11.67, 14.00, 10.32, 88.4286, 2.70, 20.63, 23.33, 28.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-02-04 10:43:51', '2026-02-04 10:43:51'),
(199, NULL, 14, 'réglage derailleur', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.66, 21.66, 26.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-02-04 10:43:51', '2026-02-04 10:43:51'),
(200, NULL, 14, 'poignée', '7116', 1.00, 1.55, 4.00, 4.80, 2.45, 61.2500, 1.55, 2.45, 4.00, 4.80, 20.0000, 4, NULL, 0, NULL, NULL, '2026-02-04 10:43:51', '2026-02-04 10:43:51'),
(135, NULL, 15, 'chaine + installation', '', 1.00, 3.94, 25.00, 30.00, 21.06, 84.2400, 3.94, 21.06, 25.00, 30.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-01-30 09:21:55', '2026-01-30 09:21:55'),
(134, NULL, 15, 'remplacement du liquide de frein', '', 2.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 50.00, 50.00, 60.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-01-30 09:21:55', '2026-01-30 09:21:55'),
(136, NULL, 15, 'dévoilage', '', 2.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-01-30 09:21:55', '2026-01-30 09:21:55'),
(137, NULL, 16, 'réglage frein AV', '', 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-01-30 09:27:34', '2026-01-30 09:27:34'),
(155, NULL, 1, 'lumières', '', 2.00, 0.00, 8.33, 10.00, 8.33, 100.0000, 0.00, 16.66, 16.66, 20.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-01-30 16:10:20', '2026-01-30 16:10:20'),
(296, NULL, 34, 'devoilage AV AR', NULL, 2.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-02-19 13:56:03', '2026-02-19 13:56:03'),
(295, NULL, 34, 'reglages freins AV AR', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.66, 21.66, 26.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-02-19 13:56:03', '2026-02-19 13:56:03'),
(378, NULL, 32, 'dévoilage', NULL, 2.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 0, 30, 0, NULL, NULL, '2026-02-26 11:56:14', '2026-02-26 11:56:14'),
(294, NULL, 33, 'devoilage AV', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 0, 15, 0, NULL, NULL, '2026-02-19 13:53:51', '2026-02-19 13:53:51'),
(202, NULL, 18, 'Pneu AR', '510459', 1.00, 19.00, 31.66, 38.00, 12.66, 40.0000, 19.00, 12.66, 31.66, 38.00, 20.0000, 0, 30, 0, NULL, NULL, '2026-02-04 16:31:58', '2026-02-04 16:31:58'),
(203, NULL, 18, 'M-O', NULL, 1.00, 0.00, 20.83, 25.00, 20.83, 100.0000, 0.00, 20.83, 20.83, 25.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-02-04 16:31:58', '2026-02-04 16:31:58'),
(201, NULL, 14, 'chambre à air', NULL, 1.00, 3.40, 6.33, 7.60, 2.93, 46.3158, 3.40, 2.93, 6.33, 7.60, 20.0000, 5, NULL, 0, NULL, NULL, '2026-02-04 10:43:51', '2026-02-04 10:43:51'),
(196, NULL, 14, 'manette droite', '488754', 1.00, 12.13, 16.50, 19.80, 4.36, 26.4848, 12.13, 4.36, 16.50, 19.80, 20.0000, 0, NULL, 0, NULL, NULL, '2026-02-04 10:43:51', '2026-02-04 10:43:51'),
(204, NULL, 19, 'NCM T3S 2025', NULL, 1.00, 608.33, 815.83, 979.00, 207.50, 25.4345, 608.33, 207.50, 815.83, 979.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-02-04 16:48:23', '2026-02-04 16:48:23'),
(205, NULL, 20, 'réglage dérailleur', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 0, 15, 0, NULL, NULL, '2026-02-04 17:05:46', '2026-02-04 17:05:46'),
(206, NULL, 20, 'garde boue + remplacement', '528155', 1.00, 21.95, 41.25, 49.50, 19.30, 46.7879, 21.95, 19.30, 41.25, 49.50, 20.0000, 1, 30, 0, NULL, NULL, '2026-02-04 17:05:46', '2026-02-04 17:05:46'),
(207, NULL, 21, 'devoilage', NULL, 2.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-02-05 08:19:32', '2026-02-05 08:19:32'),
(208, NULL, 21, 'purge freins', NULL, 2.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 50.00, 50.00, 60.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-02-05 08:19:32', '2026-02-05 08:19:32'),
(209, NULL, 21, 'chaine + MO', NULL, 1.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-02-05 08:19:32', '2026-02-05 08:19:32'),
(210, NULL, 22, 'cable derailleur', NULL, 1.00, 0.00, 2.91, 3.49, 2.91, 100.0000, 0.00, 2.91, 2.91, 3.49, 20.0000, 0, NULL, 0, NULL, NULL, '2026-02-06 13:27:45', '2026-02-06 13:27:45'),
(214, NULL, 23, 'redressement poignée + reglage de frein AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-02-07 15:41:02', '2026-02-07 15:41:02'),
(213, NULL, 23, 'cable + gaine frein AR', NULL, 1.00, 0.00, 11.66, 14.00, 11.66, 100.0000, 0.00, 11.66, 11.66, 14.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-02-07 15:41:02', '2026-02-07 15:41:02'),
(373, NULL, 24, 'remplacement cable gaine frein AR', NULL, 1.00, 1.35, 11.66, 14.00, 10.31, 88.4286, 1.35, 10.31, 11.66, 14.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-02-26 11:51:22', '2026-02-26 11:51:22'),
(374, NULL, 24, 'remplacement patins AV / AR', NULL, 2.00, 1.28, 4.58, 5.50, 3.30, 72.0727, 2.56, 6.60, 9.16, 11.00, 20.0000, 2, 15, 0, NULL, NULL, '2026-02-26 11:51:22', '2026-02-26 11:51:22'),
(375, NULL, 24, 'réglage frein AV / AR', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.66, 21.66, 26.00, 20.0000, 3, 15, 0, NULL, NULL, '2026-02-26 11:51:22', '2026-02-26 11:51:22'),
(376, NULL, 24, 'réglage dérailleur', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 4, 15, 0, NULL, NULL, '2026-02-26 11:51:22', '2026-02-26 11:51:22'),
(372, NULL, 24, 'dévoilage', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 0, 30, 0, NULL, NULL, '2026-02-26 11:51:22', '2026-02-26 11:51:22'),
(233, NULL, 25, 'degrippage derailleur', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-02-10 13:17:12', '2026-02-10 13:17:12'),
(232, NULL, 25, 'gaine cable frein AR', NULL, 1.00, 0.00, 11.66, 14.00, 11.66, 100.0000, 0.00, 11.66, 11.66, 14.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-02-10 13:17:12', '2026-02-10 13:17:12'),
(231, NULL, 25, 'reglage frein AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-02-10 13:17:12', '2026-02-10 13:17:12'),
(230, NULL, 25, 'pneu', '476512', 2.00, 6.95, 11.66, 14.00, 4.71, 40.4286, 13.90, 9.42, 23.32, 28.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-02-10 13:17:12', '2026-02-10 13:17:12'),
(234, NULL, 25, 'M-O', NULL, 1.00, 0.00, 16.66, 20.00, 16.66, 100.0000, 0.00, 16.66, 16.66, 20.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-02-10 13:17:12', '2026-02-10 13:17:12'),
(254, NULL, 28, 'roue libre 6V 14-28', '513608', 1.00, 7.00, 15.00, 18.00, 8.00, 53.3333, 7.00, 8.00, 15.00, 18.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-02-17 16:56:19', '2026-02-17 16:56:19'),
(253, NULL, 26, 'patins AR', NULL, 1.00, 1.28, 4.58, 5.50, 3.30, 72.0727, 1.28, 3.30, 4.58, 5.50, 20.0000, 3, 15, 0, NULL, NULL, '2026-02-17 16:24:45', '2026-02-17 16:24:45'),
(252, NULL, 26, 'réglage frein AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 2, 15, 0, NULL, NULL, '2026-02-17 16:24:45', '2026-02-17 16:24:45'),
(250, NULL, 26, 'chaine + montage', NULL, 1.00, 3.94, 25.00, 30.00, 21.06, 84.2400, 3.94, 21.06, 25.00, 30.00, 20.0000, 0, 30, 0, NULL, NULL, '2026-02-17 16:24:45', '2026-02-17 16:24:45'),
(251, NULL, 26, 'dévoilage roue AR', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-02-17 16:24:45', '2026-02-17 16:24:45'),
(245, NULL, 27, 'Neomouv Linaria', NULL, 1.00, 438.76, 707.50, 849.00, 268.74, 37.9845, 438.76, 268.74, 707.50, 849.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-02-12 11:16:39', '2026-02-12 11:16:39'),
(367, NULL, 38, 'cable+gaine frein AV AR', NULL, 2.00, 0.00, 11.66, 14.00, 11.66, 100.0000, 0.00, 23.32, 23.32, 28.00, 20.0000, 0, 30, 0, NULL, NULL, '2026-02-25 14:02:36', '2026-02-25 14:02:36'),
(366, NULL, 29, 'devoilage roue AV', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 4, 15, 0, NULL, NULL, '2026-02-25 09:17:49', '2026-02-25 09:17:49'),
(363, NULL, 29, 'réglage dérailleur AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-02-25 09:17:49', '2026-02-25 09:17:49'),
(364, NULL, 29, 'M-O freins / chaine', NULL, 1.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 2, 0, 0, NULL, NULL, '2026-02-25 09:17:49', '2026-02-25 09:17:49'),
(365, NULL, 29, 'chaine renforcée 9v', '492000', 1.00, 21.87, 31.66, 38.00, 9.79, 30.9368, 21.87, 9.78, 31.66, 38.00, 20.0000, 3, 15, 0, NULL, NULL, '2026-02-25 09:17:49', '2026-02-25 09:17:49'),
(351, NULL, 30, 'reglage frein AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 2, 15, 0, NULL, NULL, '2026-02-23 08:35:26', '2026-02-23 08:35:26'),
(350, NULL, 30, 'dévoilage AR', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-02-23 08:35:26', '2026-02-23 08:35:26'),
(349, NULL, 30, 'réglage dérailleur AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 0, 15, 0, NULL, NULL, '2026-02-23 08:35:26', '2026-02-23 08:35:26'),
(362, NULL, 29, 'plaquettes frein AV AR', NULL, 2.00, 0.00, 4.58, 5.50, 4.58, 100.0000, 0.00, 9.16, 9.16, 11.00, 20.0000, 0, 15, 0, NULL, NULL, '2026-02-25 09:17:49', '2026-02-25 09:17:49'),
(338, NULL, 31, 'commande derailleur AR 9v', '477620', 1.00, 8.50, 15.25, 18.30, 6.75, 44.2623, 8.50, 6.75, 15.25, 18.30, 20.0000, 2, 15, 0, NULL, NULL, '2026-02-19 15:06:10', '2026-02-19 15:06:10'),
(339, NULL, 31, 'commande derailleur AV 3v', '477618', 1.00, 7.95, 12.91, 15.50, 4.96, 38.4516, 7.95, 4.96, 12.91, 15.50, 20.0000, 3, 15, 0, NULL, NULL, '2026-02-19 15:06:10', '2026-02-19 15:06:10'),
(340, NULL, 31, 'manettes frein AV et AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-02-19 15:06:10', '2026-02-19 15:06:10'),
(341, NULL, 31, 'pneus 26x1.95', '476512', 2.00, 6.93, 10.82, 12.99, 3.89, 35.9815, 13.86, 7.78, 21.64, 25.98, 20.0000, 5, 15, 0, NULL, NULL, '2026-02-19 15:06:10', '2026-02-19 15:06:10'),
(292, NULL, 17, 'disque de frein', '495574', 1.00, 4.15, 9.16, 11.00, 5.01, 54.7273, 4.15, 5.01, 9.16, 11.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-02-18 17:52:17', '2026-02-18 17:52:17'),
(291, NULL, 17, 'Conversion Virvolt 750 sans batterie', NULL, 1.00, 372.00, 520.00, 624.00, 148.00, 28.4615, 372.00, 148.00, 520.00, 624.00, 20.0000, 0, 45, 0, NULL, NULL, '2026-02-18 17:52:17', '2026-02-18 17:52:17'),
(337, NULL, 31, 'gaines et câbles frein AV et AR', NULL, 2.00, 1.35, 11.66, 14.00, 10.31, 88.4286, 2.70, 20.62, 23.32, 28.00, 20.0000, 1, 30, 0, NULL, NULL, '2026-02-19 15:06:10', '2026-02-19 15:06:10'),
(336, NULL, 31, 'chaine 9vitesses + montage', NULL, 1.00, 8.40, 31.66, 38.00, 23.26, 73.4737, 8.40, 23.26, 31.66, 38.00, 20.0000, 0, 15, 0, NULL, NULL, '2026-02-19 15:06:10', '2026-02-19 15:06:10'),
(297, NULL, 34, 'pneu AR 26 x 1.95', '47559', 1.00, 0.00, 22.49, 26.99, 22.49, 100.0000, 0.00, 22.49, 22.49, 26.99, 20.0000, 2, NULL, 0, NULL, NULL, '2026-02-19 13:56:03', '2026-02-19 13:56:03'),
(298, NULL, 34, 'cable de frein', NULL, 1.00, 0.00, 3.25, 3.90, 3.25, 100.0000, 0.00, 3.25, 3.25, 3.90, 20.0000, 3, NULL, 0, NULL, NULL, '2026-02-19 13:56:03', '2026-02-19 13:56:03'),
(347, NULL, 35, 'Main d\'oeuvre transmission', NULL, 1.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 5, 0, 0, NULL, NULL, '2026-02-19 16:42:17', '2026-02-19 16:42:17'),
(346, NULL, 35, 'plateau 38', '513554', 1.00, 22.12, 36.91, 44.30, 14.79, 40.0813, 22.12, 14.79, 36.90, 44.30, 20.0000, 4, 15, 0, NULL, NULL, '2026-02-19 16:42:17', '2026-02-19 16:42:17'),
(345, NULL, 35, 'cassette 11-36', '496843', 1.00, 16.00, 26.83, 32.20, 10.83, 40.3727, 16.00, 10.83, 26.83, 32.20, 20.0000, 3, 15, 0, NULL, NULL, '2026-02-19 16:42:17', '2026-02-19 16:42:17'),
(344, NULL, 35, 'chaine 9v renforcée', '492000', 1.00, 21.87, 30.82, 36.99, 8.95, 29.0511, 21.87, 8.94, 30.82, 36.99, 20.0000, 2, 15, 0, NULL, NULL, '2026-02-19 16:42:17', '2026-02-19 16:42:17'),
(343, NULL, 35, 'réglage dérailleur', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-02-19 16:42:17', '2026-02-19 16:42:17'),
(342, NULL, 35, 'dévoilage', NULL, 2.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 0, 30, 0, NULL, NULL, '2026-02-19 16:42:17', '2026-02-19 16:42:17'),
(447, NULL, 36, 'plaquette + installation', NULL, 1.00, 2.38, 15.41, 18.50, 13.03, 84.5622, 2.38, 13.03, 15.41, 18.50, 20.0000, 4, 15, 0, NULL, NULL, '2026-03-04 08:14:42', '2026-03-04 08:14:42'),
(445, NULL, 36, 'derailleur AV + installation', '508743', 1.00, 6.15, 15.89, 19.07, 9.74, 61.3005, 6.15, 9.74, 15.89, 19.07, 20.0000, 2, 15, 0, NULL, NULL, '2026-03-04 08:14:42', '2026-03-04 08:14:42'),
(446, NULL, 36, 'réglage derailleur', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.66, 21.66, 26.00, 20.0000, 3, 15, 0, NULL, NULL, '2026-03-04 08:14:42', '2026-03-04 08:14:42'),
(444, NULL, 36, 'derailleur AR + installation', '465050', 1.00, 16.50, 32.49, 38.99, 15.99, 49.2177, 16.50, 15.99, 32.49, 38.99, 20.0000, 1, 15, 0, NULL, NULL, '2026-03-04 08:14:42', '2026-03-04 08:14:42'),
(443, NULL, 36, 'dévoilage AV AR', NULL, 2.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 0, 30, 0, NULL, NULL, '2026-03-04 08:14:42', '2026-03-04 08:14:42'),
(361, NULL, 37, 'reglage derailleur', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-02-24 16:26:36', '2026-02-24 16:26:36'),
(368, NULL, 38, 'devoilage av', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-02-25 14:02:36', '2026-02-25 14:02:36'),
(369, NULL, 38, 'réglage freins', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 2, 15, 0, NULL, NULL, '2026-02-25 14:02:36', '2026-02-25 14:02:36'),
(377, NULL, 24, 'pneus', '5625P2R', 1.00, 5.26, 16.25, 19.50, 10.99, 67.6308, 5.26, 10.99, 16.25, 19.50, 20.0000, 5, 15, 0, NULL, NULL, '2026-02-26 11:51:22', '2026-02-26 11:51:22'),
(379, NULL, 32, 'gaine cable freins', NULL, 1.00, 1.75, 11.66, 14.00, 9.91, 85.0000, 1.75, 9.91, 11.66, 14.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-02-26 11:56:14', '2026-02-26 11:56:14'),
(380, NULL, 32, 'gaine cable dérailleur', NULL, 1.00, 1.35, 11.66, 14.00, 10.31, 88.4286, 1.35, 10.31, 11.66, 14.00, 20.0000, 2, 30, 0, NULL, NULL, '2026-02-26 11:56:14', '2026-02-26 11:56:14'),
(393, NULL, 39, 'réglage frein', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.66, 21.66, 26.00, 20.0000, 3, 15, 0, NULL, NULL, '2026-02-26 16:20:18', '2026-02-26 16:20:18'),
(392, NULL, 39, 'ponçage jante', NULL, 2.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 2, 30, 0, NULL, NULL, '2026-02-26 16:20:18', '2026-02-26 16:20:18'),
(391, NULL, 39, 'gaines cables freins', NULL, 2.00, 1.35, 11.66, 14.00, 10.31, 88.4286, 2.70, 20.62, 23.32, 28.00, 20.0000, 1, 30, 0, NULL, NULL, '2026-02-26 16:20:18', '2026-02-26 16:20:18'),
(390, NULL, 39, 'paire de patins', NULL, 2.00, 1.28, 4.58, 5.50, 3.30, 72.0727, 2.56, 6.60, 9.16, 11.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-02-26 16:20:18', '2026-02-26 16:20:18'),
(387, NULL, 41, 'plaquettes AR', NULL, 1.00, 2.38, 8.75, 10.50, 6.37, 72.8000, 2.38, 6.37, 8.75, 10.50, 20.0000, 0, 15, 0, NULL, NULL, '2026-02-26 16:14:44', '2026-02-26 16:14:44'),
(388, NULL, 41, 'purge', NULL, 1.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-02-26 16:14:44', '2026-02-26 16:14:44'),
(389, NULL, 41, 'etrier hydrau', '511288', 1.00, 24.93, 29.15, 34.99, 4.22, 14.5013, 24.93, 4.22, 29.15, 34.99, 20.0000, 2, NULL, 0, NULL, NULL, '2026-02-26 16:14:44', '2026-02-26 16:14:44'),
(394, NULL, 39, 'panier', '510914', 1.00, 10.50, 20.83, 25.00, 10.33, 49.6000, 10.50, 10.33, 20.83, 25.00, 20.0000, 4, 15, 0, NULL, NULL, '2026-02-26 16:20:18', '2026-02-26 16:20:18'),
(472, NULL, 53, 'chambre à air', '508707', 1.00, 5.68, 11.50, 13.80, 5.82, 50.6087, 5.68, 5.82, 11.50, 13.80, 20.0000, 2, NULL, 0, NULL, NULL, '2026-03-06 10:48:28', '2026-03-06 10:48:28'),
(560, NULL, 40, 'réglage derailleur', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 6, 15, 0, NULL, NULL, '2026-03-13 09:25:28', '2026-03-13 09:25:28'),
(559, NULL, 40, 'purge frein AV', NULL, 1.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 5, 30, 0, NULL, NULL, '2026-03-13 09:25:28', '2026-03-13 09:25:28'),
(558, NULL, 40, 'main d\'oeuvre transmission', NULL, 1.00, 30.00, 25.00, 30.00, -5.00, -20.0000, 30.00, -5.00, 25.00, 30.00, 20.0000, 4, 0, 0, NULL, NULL, '2026-03-13 09:25:28', '2026-03-13 09:25:28'),
(557, NULL, 40, 'chaine 9V renforcée', '503211', 1.00, 16.90, 25.35, 30.42, 8.45, 33.3333, 16.90, 8.45, 25.35, 30.42, 20.0000, 3, 15, 0, NULL, NULL, '2026-03-13 09:25:28', '2026-03-13 09:25:28'),
(556, NULL, 40, 'plateau 40 dents gen 3', '497932', 1.00, 22.12, 36.92, 44.30, 14.80, 40.0813, 22.12, 14.80, 36.92, 44.30, 20.0000, 2, 15, 0, NULL, NULL, '2026-03-13 09:25:28', '2026-03-13 09:25:28'),
(555, NULL, 40, 'spider gen 3', '498026', 1.00, 9.02, 15.08, 18.10, 6.06, 40.1989, 9.02, 6.06, 15.08, 18.10, 20.0000, 1, 15, 0, NULL, NULL, '2026-03-13 09:25:28', '2026-03-13 09:25:28'),
(554, NULL, 40, 'cassette 9v', '496843', 1.00, 16.00, 20.83, 25.00, 4.83, 23.2000, 16.00, 4.83, 20.83, 25.00, 20.0000, 0, 15, 0, NULL, NULL, '2026-03-13 09:25:28', '2026-03-13 09:25:28'),
(413, NULL, 43, 'Casque Optimize O332', '517047', 1.00, 26.50, 44.17, 53.00, 17.67, 40.0000, 26.50, 17.67, 44.17, 53.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-02-28 13:25:02', '2026-02-28 13:25:02'),
(414, NULL, 44, 'cable + gaine frein AV', NULL, 1.00, 0.00, 11.67, 14.00, 11.67, 100.0000, 0.00, 11.67, 11.67, 14.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-02-28 17:03:30', '2026-02-28 17:03:30'),
(415, NULL, 44, 'reglage derailleur AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-02-28 17:03:30', '2026-02-28 17:03:30'),
(416, NULL, 44, 'devoilage AR AV', NULL, 2.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-02-28 17:03:30', '2026-02-28 17:03:30'),
(417, NULL, 45, 'sacoche jaune', NULL, 2.00, 32.67, 25.00, 30.00, -7.67, -30.6800, 65.34, -15.34, 50.00, 60.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-02-28 17:09:15', '2026-02-28 17:09:15'),
(418, NULL, 45, 'patin freins av ar', NULL, 2.00, 1.28, 4.58, 5.50, 3.30, 72.0727, 2.56, 6.61, 9.17, 11.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-02-28 17:09:15', '2026-02-28 17:09:15'),
(419, NULL, 46, 'pneu ar 26x1.75', NULL, 1.00, 6.10, 15.00, 18.00, 8.90, 59.3333, 6.10, 8.90, 15.00, 18.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-02-28 18:06:00', '2026-02-28 18:06:00'),
(553, NULL, 42, 'chaine + installation', NULL, 1.00, 3.94, 25.00, 30.00, 21.06, 84.2400, 3.94, 21.06, 25.00, 30.00, 20.0000, 8, 15, 0, NULL, NULL, '2026-03-13 09:13:11', '2026-03-13 09:13:11'),
(552, NULL, 42, 'éclairage AR 30 lumens', '513898', 1.00, 11.50, 24.92, 29.90, 13.42, 53.8462, 11.50, 13.42, 24.92, 29.90, 20.0000, 7, 15, 0, NULL, NULL, '2026-03-13 09:13:11', '2026-03-13 09:13:11'),
(551, NULL, 42, 'éclairage AV 500 lumens', NULL, 1.00, 23.09, 40.83, 49.00, 17.74, 43.4531, 23.09, 17.74, 40.83, 49.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-03-13 09:13:11', '2026-03-13 09:13:11'),
(550, NULL, 42, 'réglage potence sécurisé', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-03-13 09:13:11', '2026-03-13 09:13:11'),
(549, NULL, 42, 'réglage frein', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 4, 15, 0, NULL, NULL, '2026-03-13 09:13:11', '2026-03-13 09:13:11'),
(548, NULL, 42, 'montage pneu (roue motorisée)', NULL, 1.00, 0.00, 13.33, 16.00, 13.33, 100.0000, 0.00, 13.33, 13.33, 16.00, 20.0000, 3, 15, 0, NULL, NULL, '2026-03-13 09:13:11', '2026-03-13 09:13:11'),
(545, NULL, 42, 'dévoilage AR', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 0, 30, 0, NULL, NULL, '2026-03-13 09:13:11', '2026-03-13 09:13:11'),
(546, NULL, 42, 'pneu Continental CITY RIDE TT NOIR', '529985', 2.00, 14.80, 19.17, 23.00, 4.37, 22.7826, 29.60, 8.73, 38.33, 46.00, 20.0000, 1, 0, 0, NULL, NULL, '2026-03-13 09:13:11', '2026-03-13 09:13:11'),
(547, NULL, 42, 'montage pneu AV', NULL, 1.00, 0.00, 9.17, 11.00, 9.17, 100.0000, 0.00, 9.17, 9.17, 11.00, 20.0000, 2, 15, 0, NULL, NULL, '2026-03-13 09:13:11', '2026-03-13 09:13:11'),
(476, NULL, 47, 'plaquette AV + installation', NULL, 1.00, 2.38, 15.83, 19.00, 13.45, 84.9684, 2.38, 13.45, 15.83, 19.00, 20.0000, 3, 15, 0, NULL, NULL, '2026-03-06 15:38:54', '2026-03-06 15:38:54'),
(475, NULL, 47, 'chaine + installation', NULL, 1.00, 3.94, 25.00, 30.00, 21.06, 84.2400, 3.94, 21.06, 25.00, 30.00, 20.0000, 2, 15, 0, NULL, NULL, '2026-03-06 15:38:54', '2026-03-06 15:38:54'),
(474, NULL, 47, 'tripple plateau + installation', '495806', 1.00, 18.88, 32.50, 39.00, 13.62, 41.9077, 18.88, 13.62, 32.50, 39.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-03-06 15:38:54', '2026-03-06 15:38:54'),
(473, NULL, 47, 'cassette 11/34 + installation', '519685', 1.00, 10.90, 21.67, 26.00, 10.77, 49.6923, 10.90, 10.77, 21.67, 26.00, 20.0000, 0, 15, 0, NULL, NULL, '2026-03-06 15:38:54', '2026-03-06 15:38:54'),
(442, NULL, 48, 'diag', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-03 10:32:58', '2026-03-03 10:32:58'),
(448, NULL, 49, 'dévoilage', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 0, 15, 0, NULL, NULL, '2026-03-04 14:26:51', '2026-03-04 14:26:51'),
(450, NULL, 50, 'derailleur', '465050', 1.00, 16.50, 32.50, 39.00, 16.00, 49.2308, 16.50, 16.00, 32.50, 39.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-05 09:36:40', '2026-03-05 09:36:40'),
(451, NULL, 50, 'patte de derailleur', 'intersport n°57', 1.00, 11.19, 15.00, 18.00, 3.81, 25.3867, 11.19, 3.81, 15.00, 18.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-03-05 09:36:40', '2026-03-05 09:36:40'),
(452, NULL, 50, 'chaine + installation', NULL, 1.00, 2.34, 25.00, 30.00, 22.66, 90.6400, 2.34, 22.66, 25.00, 30.00, 20.0000, 2, 30, 0, NULL, NULL, '2026-03-05 09:36:40', '2026-03-05 09:36:40'),
(453, NULL, 50, 'centrage etrier AV AR', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-03-05 09:36:40', '2026-03-05 09:36:40'),
(454, NULL, 51, 'pneu', '531197', 1.00, 15.95, 22.49, 26.99, 6.54, 29.0848, 15.95, 6.54, 22.49, 26.99, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-05 10:34:10', '2026-03-05 10:34:10'),
(455, NULL, 51, 'chambre à air', '532754', 1.00, 3.40, 6.33, 7.60, 2.93, 46.3158, 3.40, 2.93, 6.33, 7.60, 20.0000, 1, NULL, 0, NULL, NULL, '2026-03-05 10:34:10', '2026-03-05 10:34:10'),
(456, NULL, 52, 'ecran Virvolt', NULL, 1.00, 68.00, 68.00, 81.60, 0.00, 0.0000, 68.00, 0.00, 68.00, 81.60, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-06 09:15:18', '2026-03-06 09:15:18'),
(457, NULL, 52, 'pneu', NULL, 1.00, 9.90, 20.83, 25.00, 10.93, 52.4800, 9.90, 10.93, 20.83, 25.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-03-06 09:15:18', '2026-03-06 09:15:18'),
(470, NULL, 53, 'disque centerlock', '517992', 1.00, 8.50, 14.17, 17.00, 5.67, 40.0000, 8.50, 5.67, 14.17, 17.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-06 10:48:28', '2026-03-06 10:48:28'),
(471, NULL, 53, 'roue shimano', '524765', 1.00, 98.42, 118.33, 141.99, 19.91, 16.8223, 98.42, 19.91, 118.33, 141.99, 20.0000, 1, NULL, 0, NULL, NULL, '2026-03-06 10:48:28', '2026-03-06 10:48:28'),
(477, NULL, 47, 'centrage étrier AR', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-03-06 15:38:54', '2026-03-06 15:38:54'),
(478, NULL, 47, 'éclairage AV 500 lumens', NULL, 1.00, 23.09, 40.83, 49.00, 17.74, 43.4531, 23.09, 17.74, 40.83, 49.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-03-06 15:38:54', '2026-03-06 15:38:54'),
(479, NULL, 47, 'éclairage AR 30 lumens', '513898', 1.00, 11.50, 24.99, 29.99, 13.49, 53.9847, 11.50, 13.49, 24.99, 29.99, 20.0000, 6, NULL, 0, NULL, NULL, '2026-03-06 15:38:54', '2026-03-06 15:38:54'),
(480, NULL, 54, 'sac décathlon', '4894386', 1.00, 46.87, 62.49, 74.99, 15.62, 24.9980, 46.87, 15.62, 62.49, 74.99, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-06 16:57:20', '2026-03-06 16:57:20'),
(515, NULL, 56, 'pneu AR + remplacement', NULL, 1.00, 0.00, 17.50, 21.00, 17.50, 100.0000, 0.00, 17.50, 17.50, 21.00, 20.0000, 0, 15, 0, NULL, NULL, '2026-03-11 14:48:36', '2026-03-11 14:48:36'),
(497, NULL, 57, 'casque O374', '527114', 1.00, 37.50, 62.50, 75.00, 25.00, 40.0000, 37.50, 25.00, 62.50, 75.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-07 17:15:38', '2026-03-07 17:15:38'),
(486, NULL, 55, 'chaine 9 v + installation', NULL, 1.00, 8.40, 31.67, 38.00, 23.27, 73.4737, 8.40, 23.27, 31.67, 38.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-03-07 08:11:57', '2026-03-07 08:11:57'),
(485, NULL, 55, 'plaquette', NULL, 2.00, 2.38, 10.83, 13.00, 8.45, 78.0308, 4.76, 16.91, 21.67, 26.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-07 08:11:57', '2026-03-07 08:11:57'),
(498, NULL, 57, 'antivol', '533333', 1.00, 4.00, 6.67, 8.00, 2.67, 40.0000, 4.00, 2.67, 6.67, 8.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-03-07 17:15:38', '2026-03-07 17:15:38'),
(499, NULL, 58, 'roue AR', '537845', 1.00, 21.50, 34.08, 40.90, 12.58, 36.9193, 21.50, 12.58, 34.08, 40.90, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-09 16:26:18', '2026-03-09 16:26:18'),
(500, NULL, 58, 'montage', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-03-09 16:26:19', '2026-03-09 16:26:19'),
(504, NULL, 59, 'installation', NULL, 1.00, 0.00, 41.67, 50.00, 41.67, 100.0000, 0.00, 41.67, 41.67, 50.00, 20.0000, 1, 60, 0, NULL, NULL, '2026-03-09 17:07:19', '2026-05-26 10:29:39'),
(503, NULL, 59, 'batterie yose power', NULL, 1.00, 260.00, 291.67, 350.00, 31.67, 10.8571, 260.00, 31.67, 291.67, 350.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-09 17:07:19', '2026-05-26 10:29:39'),
(511, NULL, 60, 'chambre à air', '²', 1.00, 1.55, 5.75, 6.90, 4.20, 73.0435, 1.55, 4.20, 5.75, 6.90, 20.0000, 0, 15, 0, NULL, NULL, '2026-03-11 13:27:38', '2026-03-11 13:27:38'),
(657, NULL, 61, 'remplacement patins', NULL, 2.00, 1.28, 4.58, 5.50, 3.30, 72.0727, 2.56, 6.61, 9.17, 11.00, 20.0000, 0, 30, 0, NULL, NULL, '2026-03-24 07:15:44', '2026-03-24 07:15:44'),
(514, NULL, 62, 'réglage de frein', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 0, 15, 0, NULL, NULL, '2026-03-11 14:02:17', '2026-03-11 14:02:17'),
(508, NULL, 63, 'chambre à air', NULL, 1.00, 1.57, 5.75, 6.90, 4.18, 72.6957, 1.57, 4.18, 5.75, 6.90, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-11 13:22:43', '2026-03-11 13:22:43'),
(509, NULL, 63, 'montage', NULL, 1.00, 0.00, 9.17, 11.00, 9.17, 100.0000, 0.00, 9.17, 9.17, 11.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-03-11 13:22:43', '2026-03-11 13:22:43'),
(510, NULL, 63, 'réglage de frein (reprise)', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 2, 15, 0, NULL, NULL, '2026-03-11 13:22:43', '2026-03-11 13:22:43'),
(512, NULL, 60, 'main d \'oeuvre', NULL, 1.00, 0.00, 9.17, 11.00, 9.17, 100.0000, 0.00, 9.17, 9.17, 11.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-03-11 13:27:38', '2026-03-11 13:27:38'),
(1122, NULL, 64, 'pedalier 3 plateau + installation', '484499', 1.00, 9.89, 20.83, 25.00, 10.94, 52.5280, 9.89, 10.94, 20.83, 25.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-04-09 17:00:34', '2026-04-09 17:00:34'),
(516, NULL, 56, 'devoilage AR', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-03-11 14:48:36', '2026-03-11 14:48:36'),
(517, NULL, 56, 'chaine 7 +installation', '7V', 1.00, 3.25, 25.00, 30.00, 21.75, 87.0000, 3.25, 21.75, 25.00, 30.00, 20.0000, 2, 15, 0, NULL, NULL, '2026-03-11 14:48:36', '2026-03-11 14:48:36'),
(518, NULL, 56, 'réglage dérailleur', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 3, 15, 0, NULL, NULL, '2026-03-11 14:48:36', '2026-03-11 14:48:36'),
(519, NULL, 56, 'réglage frein', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 4, 15, 0, NULL, NULL, '2026-03-11 14:48:36', '2026-03-11 14:48:36'),
(961, NULL, 65, 'direction grippé', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 4, 30, 0, NULL, NULL, '2026-04-01 07:39:33', '2026-05-30 14:03:37'),
(576, NULL, 66, 'Degrippage et lubrification roue libre', NULL, 1.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 16.67, 16.67, 20.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-13 20:26:31', '2026-03-13 20:26:31'),
(522, NULL, 67, 'plateau', '520367', 1.00, 44.38, 74.00, 88.80, 29.62, 40.0270, 44.38, 29.62, 74.00, 88.80, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-11 20:37:49', '2026-03-11 20:37:49'),
(523, NULL, 67, 'boitier de pédalier', '475432', 1.00, 22.62, 37.75, 45.30, 15.13, 40.0795, 22.62, 15.13, 37.75, 45.30, 20.0000, 1, NULL, 0, NULL, NULL, '2026-03-11 20:37:49', '2026-03-11 20:37:49'),
(524, NULL, 67, 'cassette 11v', '502623', 1.00, 64.93, 104.92, 125.90, 39.99, 38.1128, 64.93, 39.99, 104.92, 125.90, 20.0000, 2, NULL, 0, NULL, NULL, '2026-03-11 20:37:49', '2026-03-11 20:37:49'),
(525, NULL, 67, 'chaine 11v', '535938', 1.00, 13.20, 21.58, 25.90, 8.38, 38.8417, 13.20, 8.38, 21.58, 25.90, 20.0000, 3, NULL, 0, NULL, NULL, '2026-03-11 20:37:49', '2026-03-11 20:37:49'),
(526, NULL, 68, 'remplacement d\'un pneu (occasion)', NULL, 1.00, 0.00, 8.33, 10.00, 8.33, 100.0000, 0.00, 8.33, 8.33, 10.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-11 20:39:10', '2026-03-11 20:39:10'),
(530, NULL, 69, 'installation', NULL, 1.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 16.67, 16.67, 20.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-03-12 11:03:36', '2026-03-12 11:03:36'),
(529, NULL, 69, 'plaquettes AV AR', NULL, 2.00, 0.00, 8.75, 10.50, 8.75, 100.0000, 0.00, 17.50, 17.50, 21.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-12 11:03:36', '2026-03-12 11:03:36'),
(565, NULL, 70, 'plateau + installation', '495806', 1.00, 18.88, 31.67, 38.00, 12.79, 40.3789, 18.88, 12.79, 31.67, 38.00, 20.0000, 4, 15, 0, NULL, NULL, '2026-03-13 09:55:08', '2026-03-13 09:55:08'),
(564, NULL, 70, 'réglage patin AV', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 3, 15, 0, NULL, NULL, '2026-03-13 09:55:08', '2026-03-13 09:55:08'),
(563, NULL, 70, 'remplacement patins AV', NULL, 1.00, 1.34, 15.42, 18.50, 14.08, 91.3081, 1.34, 14.08, 15.42, 18.50, 20.0000, 2, 15, 0, NULL, NULL, '2026-03-13 09:55:08', '2026-03-13 09:55:08'),
(562, NULL, 70, 'roue libre 6V + installation', '513608', 1.00, 7.00, 17.50, 21.00, 10.50, 60.0000, 7.00, 10.50, 17.50, 21.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-03-13 09:55:08', '2026-03-13 09:55:08'),
(561, NULL, 70, 'chaine + installation', NULL, 1.00, 3.94, 25.00, 30.00, 21.06, 84.2400, 3.94, 21.06, 25.00, 30.00, 20.0000, 0, 15, 0, NULL, NULL, '2026-03-13 09:55:08', '2026-03-13 09:55:08'),
(1121, NULL, 64, 'roue libre 6V', '513608', 1.00, 7.00, 11.67, 14.00, 4.67, 40.0000, 7.00, 4.67, 11.67, 14.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-04-09 17:00:34', '2026-04-09 17:00:34'),
(1120, NULL, 64, 'chaine 6V + installation', NULL, 1.00, 3.94, 25.00, 30.00, 21.06, 84.2400, 3.94, 21.06, 25.00, 30.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-04-09 17:00:34', '2026-04-09 17:00:34'),
(1119, NULL, 64, 'dévoilage AV', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-04-09 17:00:34', '2026-04-09 17:00:34'),
(1118, NULL, 64, 'réglage dérailleur', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-09 17:00:34', '2026-04-09 17:00:34'),
(1117, NULL, 64, 'réglage frein', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-09 17:00:34', '2026-04-09 17:00:34'),
(1116, NULL, 64, 'cable gaine frein AR', NULL, 1.00, 1.35, 11.67, 14.00, 10.32, 88.4286, 1.35, 10.32, 11.67, 14.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-09 17:00:34', '2026-04-09 17:00:34'),
(579, NULL, 73, 'patins AV AR', NULL, 2.00, 0.00, 4.58, 5.50, 4.58, 100.0000, 0.00, 9.17, 9.17, 11.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-03-14 13:20:28', '2026-03-14 13:20:28'),
(578, NULL, 73, 'réglages freins', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 0, 15, 0, NULL, NULL, '2026-03-14 13:20:28', '2026-03-14 13:20:28'),
(658, NULL, 61, 'chaine renforcée et antirouille', '535893', 1.00, 31.75, 52.96, 63.55, 21.21, 40.0472, 31.75, 21.21, 52.96, 63.55, 20.0000, 1, 15, 0, NULL, NULL, '2026-03-24 07:15:44', '2026-03-24 07:15:44'),
(659, NULL, 61, 'pignon nexus 19dents', '494268', 1.00, 3.03, 3.33, 3.99, 0.30, 8.8722, 3.03, 0.30, 3.33, 3.99, 20.0000, 2, 30, 0, NULL, NULL, '2026-03-24 07:15:44', '2026-03-24 07:15:44'),
(660, NULL, 61, 'plateau 38 dents', 'https://www.ebike24.fr/derby-cycle-plateau-38-dents-pour-velos-electriques-avec-moteur-impulse', 1.00, 19.71, 25.83, 31.00, 6.12, 23.7032, 19.71, 6.12, 25.83, 31.00, 20.0000, 3, 15, 0, NULL, NULL, '2026-03-24 07:15:44', '2026-03-24 07:15:44'),
(661, NULL, 61, 'pneus hard skin', '508097', 2.00, 27.90, 41.66, 49.99, 13.76, 33.0266, 55.80, 27.52, 83.32, 99.98, 20.0000, 4, 30, 0, NULL, NULL, '2026-03-24 07:15:44', '2026-03-24 07:15:44'),
(662, NULL, 61, 'réglage vitesse', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 5, 0, 0, NULL, NULL, '2026-03-24 07:15:44', '2026-03-24 07:15:44'),
(663, NULL, 61, 'Main d\'oeuvre', NULL, 3.00, 0.00, 37.50, 45.00, 37.50, 100.0000, 0.00, 112.50, 112.50, 135.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-03-24 07:15:44', '2026-03-24 07:15:44'),
(577, NULL, 66, 'Nettoyage transmission', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-03-13 20:26:31', '2026-03-13 20:26:31'),
(580, NULL, 73, 'montage', NULL, 1.00, 0.00, 8.33, 10.00, 8.33, 100.0000, 0.00, 8.33, 8.33, 10.00, 20.0000, 2, 15, 0, NULL, NULL, '2026-03-14 13:20:28', '2026-03-14 13:20:28'),
(1127, NULL, 74, 'mini compresseur 8 bar', '538717', 1.00, 35.95, 70.75, 84.90, 34.80, 49.1873, 35.95, 34.80, 70.75, 84.90, 20.0000, 4, NULL, 0, NULL, NULL, '2026-04-09 17:07:35', '2026-04-09 17:07:35'),
(1126, NULL, 74, 'chambre a air x2 Hutchinson 700 x  28-35', '482011', 1.00, 4.90, 7.92, 9.50, 3.02, 38.1053, 4.90, 3.02, 7.92, 9.50, 20.0000, 3, NULL, 0, NULL, NULL, '2026-04-09 17:07:35', '2026-04-09 17:07:35'),
(1125, NULL, 74, 'lot démonte pneu', '511815', 1.00, 1.00, 3.29, 3.95, 2.29, 69.6203, 1.00, 2.29, 3.29, 3.95, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-09 17:07:35', '2026-04-09 17:07:35'),
(1124, NULL, 74, 'kit reparation', '529274', 1.00, 8.85, 15.00, 18.00, 6.15, 41.0000, 8.85, 6.15, 15.00, 18.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-09 17:07:35', '2026-04-09 17:07:35'),
(1123, NULL, 74, 'gourde porte outils', '487960', 1.00, 2.35, 4.83, 5.80, 2.48, 51.3793, 2.35, 2.48, 4.83, 5.80, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-09 17:07:35', '2026-04-09 17:07:35'),
(960, NULL, 65, 'gaine + cable vitesse', NULL, 2.00, 1.35, 15.00, 18.00, 13.65, 91.0000, 2.70, 27.30, 30.00, 36.00, 20.0000, 3, 30, 0, NULL, NULL, '2026-04-01 07:39:33', '2026-05-30 14:03:37'),
(959, NULL, 65, 'gaine + cable frein', NULL, 2.00, 1.75, 15.00, 18.00, 13.25, 88.3333, 3.50, 26.50, 30.00, 36.00, 20.0000, 2, 30, 0, NULL, NULL, '2026-04-01 07:39:33', '2026-05-30 14:03:37'),
(958, NULL, 65, 'pneus + montage', '542831', 2.00, 15.10, 29.17, 35.00, 14.07, 48.2286, 30.20, 28.13, 58.33, 70.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-04-01 07:39:33', '2026-05-30 14:03:37'),
(591, NULL, 75, 'Reception vélo ID : ECW16', NULL, 1.00, 0.00, 35.00, 42.00, 35.00, 100.0000, 0.00, 35.00, 35.00, 42.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-16 10:13:27', '2026-03-16 10:13:27'),
(592, NULL, 75, 'Reception vélo ID : OT45', NULL, 1.00, 0.00, 35.00, 42.00, 35.00, 100.0000, 0.00, 35.00, 35.00, 42.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-03-16 10:13:27', '2026-03-16 10:13:27'),
(593, NULL, 75, 'Retour vélo id : OT45', NULL, 1.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-03-16 10:13:27', '2026-03-16 10:13:27'),
(606, NULL, 77, 'montage (roue motorisée)', NULL, 1.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 16.67, 16.67, 20.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-03-18 10:09:15', '2026-03-18 10:09:15'),
(605, NULL, 77, 'chambre a air', NULL, 1.00, 3.30, 6.17, 7.40, 2.87, 46.4865, 3.30, 2.87, 6.17, 7.40, 20.0000, 0, 15, 0, NULL, NULL, '2026-03-18 10:09:15', '2026-03-18 10:09:15'),
(607, NULL, 77, 'dévoilage', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 2, 15, 0, NULL, NULL, '2026-03-18 10:09:15', '2026-03-18 10:09:15'),
(1241, NULL, 79, 'pneu', '479163', 1.00, 5.80, 11.18, 13.42, 5.38, 48.1371, 5.80, 5.38, 11.18, 13.42, 20.0000, 5, 15, 0, NULL, NULL, '2026-04-13 14:02:42', '2026-04-13 14:02:42'),
(639, NULL, 80, 'remplacement chaine + installation', '329155', 1.00, 5.90, 25.00, 30.00, 19.10, 76.4000, 5.90, 19.10, 25.00, 30.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-03-23 14:59:45', '2026-03-23 14:59:45'),
(638, NULL, 80, 'réglage frein', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-23 14:59:45', '2026-03-23 14:59:45'),
(843, NULL, 78, 'graissage trans', NULL, 1.00, 0.00, 8.33, 10.00, 8.33, 100.0000, 0.00, 8.33, 8.33, 10.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-03-30 14:07:07', '2026-03-30 14:07:07'),
(844, NULL, 78, 'pneus 700 x 35c', '507416', 2.00, 16.26, 27.42, 32.90, 11.16, 40.6930, 32.52, 22.31, 54.83, 65.80, 20.0000, 2, 15, 0, NULL, NULL, '2026-03-30 14:07:07', '2026-03-30 14:07:07'),
(845, NULL, 78, 'main d\'oeuvre montage', NULL, 1.00, 0.00, 20.83, 25.00, 20.83, 100.0000, 0.00, 20.83, 20.83, 25.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-03-30 14:07:07', '2026-03-30 14:07:07'),
(846, NULL, 78, 'ajustement roulement de roue', NULL, 2.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-03-30 14:07:07', '2026-03-30 14:07:07'),
(1314, NULL, 76, 'plateau 30d', '16192', 1.00, 14.50, 19.17, 23.00, 4.67, 24.3478, 14.50, 4.67, 19.17, 23.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-04-16 09:16:24', '2026-04-16 09:16:24'),
(1315, NULL, 76, 'cassette Primato 8V', '5334', 1.00, 28.71, 38.33, 46.00, 9.62, 25.1043, 28.71, 9.62, 38.33, 46.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-04-16 09:16:24', '2026-04-16 09:16:24'),
(1316, NULL, 76, 'chaine 8V', NULL, 1.00, 3.94, 16.67, 20.00, 12.73, 76.3600, 3.94, 12.73, 16.67, 20.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-04-16 09:16:24', '2026-04-16 09:16:24');
INSERT INTO `quote_lines` (`id`, `article_id`, `quote_id`, `title`, `reference`, `quantity`, `purchase_price_ht`, `sale_price_ht`, `sale_price_ttc`, `margin_amount_ht`, `margin_rate`, `line_purchase_ht`, `line_margin_ht`, `line_total_ht`, `line_total_ttc`, `tva_rate`, `position`, `estimated_time_minutes`, `needs_order`, `ordered_at`, `received_at`, `created_at`, `updated_at`) VALUES
(1317, NULL, 76, 'dévoilage  AR', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-04-16 09:16:24', '2026-04-16 09:16:24'),
(1318, NULL, 76, 'derailleur', '488640', 1.00, 13.00, 19.50, 23.40, 6.50, 33.3333, 13.00, 6.50, 19.50, 23.40, 20.0000, 7, NULL, 0, NULL, NULL, '2026-04-16 09:16:24', '2026-04-16 09:16:24'),
(1319, NULL, 76, 'réglage de frein AV AR', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 8, NULL, 0, NULL, NULL, '2026-04-16 09:16:24', '2026-04-16 09:16:24'),
(1240, NULL, 79, 'installation lampe', NULL, 1.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 16.67, 16.67, 20.00, 20.0000, 4, 30, 0, NULL, NULL, '2026-04-13 14:02:42', '2026-04-13 14:02:42'),
(1239, NULL, 79, 'lampe AV', '536483', 1.00, 17.50, 28.29, 33.95, 10.79, 38.1443, 17.50, 10.79, 28.29, 33.95, 20.0000, 3, NULL, 0, NULL, NULL, '2026-04-13 14:02:42', '2026-04-13 14:02:42'),
(1238, NULL, 79, 'ajustage bequille', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-13 14:02:42', '2026-04-13 14:02:42'),
(1313, NULL, 76, 'plateau 42d', '16158', 1.00, 26.90, 37.50, 45.00, 10.60, 28.2667, 26.90, 10.60, 37.50, 45.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-16 09:16:24', '2026-04-16 09:16:24'),
(633, NULL, 81, 'VAE NCM Milano Max', NULL, 1.00, 680.00, 957.50, 1149.00, 277.50, 28.9817, 680.00, 277.50, 957.50, 1149.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-21 09:04:34', '2026-03-21 09:04:34'),
(635, NULL, 82, 'serrage + voile', NULL, 1.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 0, 30, 0, NULL, NULL, '2026-03-21 10:25:45', '2026-03-21 10:25:45'),
(636, NULL, 83, 'mise en place d\'un rayon', NULL, 1.00, 0.00, 18.33, 22.00, 18.33, 100.0000, 0.00, 18.33, 18.33, 22.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-21 14:49:37', '2026-03-21 14:49:37'),
(637, NULL, 83, 'rayon', NULL, 1.00, 0.00, 2.50, 3.00, 2.50, 100.0000, 0.00, 2.50, 2.50, 3.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-03-21 14:49:37', '2026-03-21 14:49:37'),
(842, NULL, 78, 'devoilage x2', NULL, 2.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 0, 15, 0, NULL, NULL, '2026-03-30 14:07:07', '2026-03-30 14:07:07'),
(957, NULL, 65, 'chaine + montage', NULL, 1.00, 3.94, 25.00, 30.00, 21.06, 84.2400, 3.94, 21.06, 25.00, 30.00, 20.0000, 0, 15, 0, NULL, NULL, '2026-04-01 07:39:33', '2026-05-30 14:03:37'),
(1242, NULL, 79, 'chambre a air', '476516', 1.00, 2.95, 5.67, 6.80, 2.72, 47.9412, 2.95, 2.72, 5.67, 6.80, 20.0000, 6, 0, 0, NULL, NULL, '2026-04-13 14:02:42', '2026-04-13 14:02:42'),
(742, NULL, 84, 'devoilage av ar', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 2, 30, 0, NULL, NULL, '2026-03-24 14:33:30', '2026-03-24 14:33:30'),
(743, NULL, 84, 'leviers de frein av ar', NULL, 2.00, 0.00, 5.42, 6.50, 5.42, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 3, 15, 0, NULL, NULL, '2026-03-24 14:33:30', '2026-03-24 14:33:30'),
(744, NULL, 84, 'étrier freins (patins inclus) av', '533444', 1.00, 14.85, 17.49, 20.99, 2.64, 15.1024, 14.85, 2.64, 17.49, 20.99, 20.0000, 4, 15, 0, NULL, NULL, '2026-03-24 14:33:30', '2026-03-24 14:33:30'),
(745, NULL, 84, 'câble + gaine freins av ar', NULL, 2.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 33.33, 33.33, 40.00, 20.0000, 5, 30, 0, NULL, NULL, '2026-03-24 14:33:30', '2026-03-24 14:33:30'),
(746, NULL, 84, 'pneu av 27.5x2.00 + montage', '494911', 1.00, 8.35, 21.66, 25.99, 13.31, 61.4467, 8.35, 13.31, 21.66, 25.99, 20.0000, 6, 15, 0, NULL, NULL, '2026-03-24 14:33:30', '2026-03-24 14:33:30'),
(747, NULL, 84, 'câble + gaine dérailleur av', NULL, 1.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 16.67, 16.67, 20.00, 20.0000, 7, 30, 0, NULL, NULL, '2026-03-24 14:33:30', '2026-03-24 14:33:30'),
(748, NULL, 84, 'réglages dérailleurs ar', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 8, 15, 0, NULL, NULL, '2026-03-24 14:33:30', '2026-03-24 14:33:30'),
(726, NULL, 85, '2 pneus 24x1.95 + montage', '465758', 2.00, 4.10, 18.53, 22.24, 14.43, 77.8777, 8.20, 28.87, 37.07, 44.48, 20.0000, 1, 15, 0, NULL, NULL, '2026-03-24 14:29:49', '2026-03-24 14:29:49'),
(727, NULL, 85, 'poignées guidon + montage', '465798', 1.00, 2.02, 5.00, 6.00, 2.98, 59.6000, 2.02, 2.98, 5.00, 6.00, 20.0000, 2, 15, 0, NULL, NULL, '2026-03-24 14:29:49', '2026-03-24 14:29:49'),
(728, NULL, 85, 'reglage frein ar', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 3, 15, 0, NULL, NULL, '2026-03-24 14:29:49', '2026-03-24 14:29:49'),
(729, NULL, 85, 'câble + gaine dérailleur av ar', NULL, 2.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 33.33, 33.33, 40.00, 20.0000, 4, 30, 0, NULL, NULL, '2026-03-24 14:29:49', '2026-03-24 14:29:49'),
(730, NULL, 85, 'dévoilage ar', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 5, 15, 0, NULL, NULL, '2026-03-24 14:29:49', '2026-03-24 14:29:49'),
(741, NULL, 84, 'poignées guidon + montage', '465798', 1.00, 2.02, 5.00, 6.00, 2.98, 59.6000, 2.02, 2.98, 5.00, 6.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-03-24 14:33:30', '2026-03-24 14:33:30'),
(725, NULL, 85, 'chaine 6v + installation', NULL, 1.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 0, 30, 0, NULL, NULL, '2026-03-24 14:29:49', '2026-03-24 14:29:49'),
(740, NULL, 84, 'chaine 6v + installation', NULL, 1.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 0, 30, 0, NULL, NULL, '2026-03-24 14:33:30', '2026-03-24 14:33:30'),
(1312, NULL, 76, 'plateau 52d', '153573', 1.00, 29.00, 40.00, 48.00, 11.00, 27.5000, 29.00, 11.00, 40.00, 48.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-16 09:16:24', '2026-04-16 09:16:24'),
(1311, NULL, 76, 'diag', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-16 09:16:24', '2026-04-16 09:16:24'),
(798, NULL, 87, 'entretien transmission (nettoyage/graissage)', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 2, 15, 0, NULL, NULL, '2026-03-28 16:36:59', '2026-03-28 16:36:59'),
(796, NULL, 87, 'devoilage AV AR', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 0, 30, 0, NULL, NULL, '2026-03-28 16:36:59', '2026-03-28 16:36:59'),
(797, NULL, 87, 'reglage freins AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-03-28 16:36:59', '2026-03-28 16:36:59'),
(1066, NULL, 88, 'montage', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-08 10:31:12', '2026-04-08 10:31:12'),
(782, NULL, 89, 'NCM Milano', 'INV-1274', 1.00, 480.00, 658.33, 790.00, 178.33, 27.0886, 480.00, 178.33, 658.33, 790.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-27 21:38:47', '2026-03-27 21:38:47'),
(1135, NULL, 90, 'plateau 50', '525569', 1.00, 57.20, 72.49, 86.99, 15.29, 21.0944, 57.20, 15.29, 72.49, 86.99, 20.0000, 1, 15, 0, NULL, NULL, '2026-04-10 09:09:01', '2026-04-29 15:04:34'),
(1132, NULL, 92, 'patins AR', NULL, 1.00, 1.28, 4.58, 5.50, 3.30, 72.0727, 1.28, 3.30, 4.58, 5.50, 20.0000, 4, NULL, 0, NULL, NULL, '2026-04-10 09:02:37', '2026-04-10 09:02:37'),
(1136, NULL, 90, 'plateau 34', '525586', 1.00, 12.60, 17.07, 20.49, 4.47, 26.2079, 12.60, 4.47, 17.07, 20.49, 20.0000, 2, 15, 0, NULL, NULL, '2026-04-10 09:09:01', '2026-04-29 15:04:34'),
(1137, NULL, 90, 'cassette 11V', '500804', 1.00, 43.00, 54.16, 64.99, 11.16, 20.6032, 43.00, 11.16, 54.16, 64.99, 20.0000, 3, 15, 0, NULL, NULL, '2026-04-10 09:09:01', '2026-04-29 15:04:34'),
(1138, NULL, 90, 'chaine 11V', '535935', 1.00, 23.45, 39.08, 46.90, 15.63, 40.0000, 23.45, 15.63, 39.08, 46.90, 20.0000, 4, 15, 0, NULL, NULL, '2026-04-10 09:09:01', '2026-04-29 15:04:34'),
(1139, NULL, 90, 'main d\'oeuvre transmission', NULL, 1.00, 0.00, 37.50, 45.00, 37.50, 100.0000, 0.00, 37.50, 37.50, 45.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-04-10 09:09:01', '2026-04-29 15:04:34'),
(1140, NULL, 90, 'main d\'oeuvre remplacement pneu AR', NULL, 1.00, 0.00, 9.17, 11.00, 9.17, 100.0000, 0.00, 9.17, 9.17, 11.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-04-10 09:09:01', '2026-04-29 15:04:34'),
(1141, NULL, 90, 'réglage dérailleur (AR)', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 7, NULL, 0, NULL, NULL, '2026-04-10 09:09:01', '2026-04-29 15:04:34'),
(1142, NULL, 90, 'dévoilage (AV AR)', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 8, NULL, 0, NULL, NULL, '2026-04-10 09:09:01', '2026-04-29 15:04:34'),
(1143, NULL, 90, 'main d\'oeuvre plaquette', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 9, 15, 0, NULL, NULL, '2026-04-10 09:09:01', '2026-04-29 15:04:34'),
(1144, NULL, 90, 'plaquette AV', '533526', 1.00, 19.95, 26.66, 31.99, 6.71, 25.1641, 19.95, 6.71, 26.66, 31.99, 20.0000, 10, NULL, 0, NULL, NULL, '2026-04-10 09:09:01', '2026-04-29 15:04:34'),
(1145, NULL, 90, 'repositionnement pneu AV', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 11, 15, 0, NULL, NULL, '2026-04-10 09:09:01', '2026-04-29 15:04:34'),
(1134, NULL, 90, 'pneus AR Michelin pro5', '541603', 1.00, 22.80, 32.30, 38.76, 9.50, 29.4118, 22.80, 9.50, 32.30, 38.76, 20.0000, 0, 15, 0, NULL, NULL, '2026-04-10 09:09:01', '2026-04-29 15:04:34'),
(1107, NULL, 71, 'tige de selle', NULL, 1.00, 22.92, 22.92, 27.50, 0.00, -0.0145, 22.92, 0.00, 22.92, 27.50, 20.0000, 8, NULL, 0, NULL, NULL, '2026-04-09 16:05:07', '2026-04-09 16:05:07'),
(1106, NULL, 71, 'main d\'oeuvre plaquette', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 7, NULL, 0, NULL, NULL, '2026-04-09 16:05:07', '2026-04-09 16:05:07'),
(1105, NULL, 71, 'main d\'oeuvre transmission', NULL, 1.00, 0.00, 37.50, 45.00, 37.50, 100.0000, 0.00, 37.50, 37.50, 45.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-04-09 16:05:07', '2026-04-09 16:05:07'),
(1104, NULL, 71, 'remplacement liquide de frein', NULL, 2.00, 0.00, 27.50, 33.00, 27.50, 100.0000, 0.00, 55.00, 55.00, 66.00, 20.0000, 5, 30, 0, NULL, NULL, '2026-04-09 16:05:07', '2026-04-09 16:05:07'),
(1103, NULL, 71, 'dévoilage AR', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 4, 15, 0, NULL, NULL, '2026-04-09 16:05:07', '2026-04-09 16:05:07'),
(1102, NULL, 71, 'plaquette (AV/AR)', NULL, 2.00, 2.38, 8.75, 10.50, 6.37, 72.8000, 4.76, 12.74, 17.50, 21.00, 20.0000, 3, 15, 0, NULL, NULL, '2026-04-09 16:05:07', '2026-04-09 16:05:07'),
(1101, NULL, 71, 'chaine 8V E-bike', '536647', 1.00, 12.95, 16.50, 19.80, 3.55, 21.5152, 12.95, 3.55, 16.50, 19.80, 20.0000, 2, 15, 0, NULL, NULL, '2026-04-09 16:05:07', '2026-04-09 16:05:07'),
(863, NULL, 93, 'montage', NULL, 1.00, 0.00, 6.67, 8.00, 6.67, 100.0000, 0.00, 6.67, 6.67, 8.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-03-31 09:09:33', '2026-03-31 09:09:33'),
(1065, NULL, 88, 'chambre à air', '493773', 1.00, 1.52, 5.75, 6.90, 4.23, 73.5652, 1.52, 4.23, 5.75, 6.90, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-08 10:31:12', '2026-04-08 10:31:12'),
(862, NULL, 93, 'chambre a air', '537765', 1.00, 6.20, 10.83, 12.99, 4.63, 42.7252, 6.20, 4.63, 10.83, 12.99, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-31 09:09:33', '2026-03-31 09:09:33'),
(1108, NULL, 71, 'pneu', NULL, 1.00, 23.90, 23.90, 28.68, 0.00, 0.0000, 23.90, 0.00, 23.90, 28.68, 20.0000, 9, NULL, 0, NULL, NULL, '2026-04-09 16:05:07', '2026-04-09 16:05:07'),
(1110, NULL, 95, 'chaine 8V E-bike', '536647', 1.00, 12.95, 24.99, 29.99, 12.04, 48.1827, 12.95, 12.04, 24.99, 29.99, 20.0000, 1, 15, 0, NULL, NULL, '2026-04-09 16:05:20', '2026-04-09 16:05:20'),
(1111, NULL, 95, 'plateau 42 dents', '513558', 1.00, 22.15, 36.92, 44.30, 14.77, 40.0000, 22.15, 14.77, 36.92, 44.30, 20.0000, 2, 15, 0, NULL, NULL, '2026-04-09 16:05:20', '2026-04-09 16:05:20'),
(1131, NULL, 92, 'pneu AR 700x35c', '476644', 1.00, 11.60, 13.01, 15.61, 1.41, 10.8264, 11.60, 1.41, 13.01, 15.61, 20.0000, 3, 15, 0, NULL, NULL, '2026-04-10 09:02:37', '2026-04-10 09:02:37'),
(1130, NULL, 92, 'devoilage AV AR', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 2, 30, 0, NULL, NULL, '2026-04-10 09:02:37', '2026-04-10 09:02:37'),
(1129, NULL, 92, 'reglages derailleur AV AR', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-04-10 09:02:37', '2026-04-10 09:02:37'),
(1021, NULL, 94, 'lampe AV', '524796', 1.00, 13.50, 22.50, 27.00, 9.00, 40.0000, 13.50, 9.00, 22.50, 27.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-04-02 14:01:33', '2026-05-05 14:43:36'),
(1020, NULL, 94, 'devoilage AR', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-04-02 14:01:33', '2026-05-05 14:43:36'),
(1019, NULL, 94, 'rayon AR + montage', '459446', 1.00, 1.18, 10.00, 12.00, 8.82, 88.2000, 1.18, 8.82, 10.00, 12.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-02 14:01:33', '2026-05-05 14:43:36'),
(1018, NULL, 94, 'patins freins AR + montage', NULL, 1.00, 1.28, 12.92, 15.50, 11.64, 90.0903, 1.28, 11.64, 12.92, 15.50, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-02 14:01:33', '2026-05-05 14:43:36'),
(1112, NULL, 95, 'pneu AR', '497182', 1.00, 20.90, 28.25, 33.90, 7.35, 26.0177, 20.90, 7.35, 28.25, 33.90, 20.0000, 3, 15, 0, NULL, NULL, '2026-04-09 16:05:20', '2026-04-09 16:05:20'),
(1113, NULL, 95, 'main d\'oeuvre transmission', NULL, 1.00, 0.00, 37.50, 45.00, 37.50, 100.0000, 0.00, 37.50, 37.50, 45.00, 20.0000, 4, 0, 0, NULL, NULL, '2026-04-09 16:05:20', '2026-04-09 16:05:20'),
(1114, NULL, 95, 'main d\'oeuvre pneu', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 5, 0, 0, NULL, NULL, '2026-04-09 16:05:20', '2026-04-09 16:05:20'),
(1115, NULL, 95, 'remplacement liquide de frein', NULL, 2.00, 0.00, 27.50, 33.00, 27.50, 100.0000, 0.00, 55.00, 55.00, 66.00, 20.0000, 6, 30, 0, NULL, NULL, '2026-04-09 16:05:20', '2026-04-09 16:05:20'),
(1133, NULL, 92, 'main d\'oeuvre montage', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-04-10 09:02:37', '2026-04-10 09:02:37'),
(1069, NULL, 91, 'main d\'oeuvre', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-08 13:30:27', '2026-04-08 13:30:27'),
(1070, NULL, 91, 'devoilage AV AR', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-08 13:30:27', '2026-04-08 13:30:27'),
(1128, NULL, 92, 'reglage freins AV AR', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 0, 15, 0, NULL, NULL, '2026-04-10 09:02:37', '2026-04-10 09:02:37'),
(926, NULL, 98, 'reglage derailleur', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-03-31 15:17:06', '2026-03-31 15:17:06'),
(925, NULL, 98, 'roue AR', '537903', 1.00, 44.55, 75.00, 90.00, 30.45, 40.6000, 44.55, 30.45, 75.00, 90.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-03-31 15:17:06', '2026-03-31 15:17:06'),
(1109, NULL, 95, 'cassette 8v', '485146', 1.00, 18.87, 19.99, 23.99, 1.12, 5.6107, 18.87, 1.12, 19.99, 23.99, 20.0000, 0, 15, 0, NULL, NULL, '2026-04-09 16:05:20', '2026-04-09 16:05:20'),
(1098, NULL, 97, 'patin AR + montage', '1,28', 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-09 16:04:44', '2026-04-09 16:04:44'),
(1093, NULL, 96, 'plateau x3 24/42 axe 122mm', '295527', 1.00, 13.85, 23.08, 27.70, 9.23, 40.0000, 13.85, 9.23, 23.08, 27.70, 20.0000, 4, 15, 0, NULL, NULL, '2026-04-09 16:04:34', '2026-04-09 16:04:34'),
(1092, NULL, 96, 'roue libre 14/28  7V', '492240', 1.00, 8.40, 12.38, 14.85, 3.97, 32.1212, 8.40, 3.97, 12.38, 14.85, 20.0000, 3, 15, 0, NULL, NULL, '2026-04-09 16:04:34', '2026-04-09 16:04:34'),
(1091, NULL, 96, 'chaine 7V', NULL, 1.00, 3.94, 15.00, 18.00, 11.06, 73.7333, 3.94, 11.06, 15.00, 18.00, 20.0000, 2, 15, 0, NULL, NULL, '2026-04-09 16:04:34', '2026-04-09 16:04:34'),
(1090, NULL, 96, 'reglage freins AV', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-04-09 16:04:34', '2026-04-09 16:04:34'),
(1089, NULL, 96, 'degrippage Vbrake AR', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 0, 15, 0, NULL, NULL, '2026-04-09 16:04:34', '2026-04-09 16:04:34'),
(1522, NULL, 65, 'derailleur occasion 8V', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 6, 15, 0, NULL, NULL, '2026-05-04 14:07:27', '2026-05-30 14:03:37'),
(1097, NULL, 97, 'gaine + cable freins AV AR', NULL, 2.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 33.33, 33.33, 40.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-09 16:04:44', '2026-04-09 16:04:44'),
(1096, NULL, 97, 'chaine 7v + montage', '3,94', 1.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-09 16:04:44', '2026-04-09 16:04:44'),
(1094, NULL, 96, 'serrage roulement roue AV', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-04-09 16:04:34', '2026-04-09 16:04:34'),
(1095, NULL, 96, 'main d\'oeuvre transmission', NULL, 1.00, 0.00, 29.17, 35.00, 29.17, 100.0000, 0.00, 29.17, 29.17, 35.00, 20.0000, 6, 0, 0, NULL, NULL, '2026-04-09 16:04:34', '2026-04-09 16:04:34'),
(1022, NULL, 94, 'adaptation faisceau', NULL, 1.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 16.67, 16.67, 20.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-04-02 14:01:33', '2026-05-05 14:43:36'),
(1067, NULL, 88, 'remplacement chaine', NULL, 1.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-08 10:31:12', '2026-04-08 10:31:12'),
(986, NULL, 99, 'purge hydraulique', NULL, 1.00, 0.00, 20.83, 25.00, 20.83, 100.0000, 0.00, 20.83, 20.83, 25.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-01 15:36:08', '2026-04-01 15:36:08'),
(982, NULL, 100, 'chaine 9V', '503211', 1.00, 16.90, 25.35, 30.42, 8.45, 33.3333, 16.90, 8.45, 25.35, 30.42, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-01 14:54:34', '2026-04-01 14:54:34'),
(983, NULL, 100, 'plateau 42', '513558', 1.00, 22.15, 36.92, 44.30, 14.77, 40.0000, 22.15, 14.77, 36.92, 44.30, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-01 14:54:34', '2026-04-01 14:54:34'),
(984, NULL, 100, 'cassette 36', '496843', 1.00, 16.00, 21.00, 25.20, 5.00, 23.8095, 16.00, 5.00, 21.00, 25.20, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-01 14:54:34', '2026-04-01 14:54:34'),
(985, NULL, 100, 'main d\'oeuvre', NULL, 1.00, 0.00, 37.50, 45.00, 37.50, 100.0000, 0.00, 37.50, 37.50, 45.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-04-01 14:54:34', '2026-04-01 14:54:34'),
(1024, NULL, 103, 'reglage de frein', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-02 16:22:16', '2026-04-02 16:22:16'),
(1017, NULL, 94, 'reglage freins AV AR', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-02 14:01:33', '2026-05-05 14:43:36'),
(1068, NULL, 91, 'plaquettes', NULL, 1.00, 0.00, 8.75, 10.50, 8.75, 100.0000, 0.00, 8.75, 8.75, 10.50, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-08 13:30:27', '2026-04-08 13:30:27'),
(1100, NULL, 71, 'plateau 42 dents', '513558', 1.00, 22.12, 36.92, 44.30, 14.80, 40.0813, 22.12, 14.80, 36.92, 44.30, 20.0000, 1, 15, 0, NULL, NULL, '2026-04-09 16:05:07', '2026-04-09 16:05:07'),
(1099, NULL, 71, 'cassette 8V', '536211', 1.00, 17.65, 24.99, 29.99, 7.34, 29.3765, 17.65, 7.34, 24.99, 29.99, 20.0000, 0, 15, 0, NULL, NULL, '2026-04-09 16:05:07', '2026-04-09 16:05:07'),
(1261, NULL, 107, 'livraison', NULL, 1.00, 0.00, 8.33, 10.00, 8.33, 100.0000, 0.00, 8.33, 8.33, 10.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-14 09:24:00', '2026-04-14 09:24:00'),
(1260, NULL, 107, 'reglages freins', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-14 09:24:00', '2026-04-14 09:24:00'),
(1040, NULL, 108, 'chambre a iar', NULL, 1.00, 0.00, 6.17, 7.40, 6.17, 100.0000, 0.00, 6.17, 6.17, 7.40, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-04 09:34:04', '2026-04-22 20:29:05'),
(1041, NULL, 108, 'montage', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-04 09:34:04', '2026-04-22 20:29:05'),
(1168, NULL, 102, 'béquille', '517510', 1.00, 7.89, 13.15, 15.78, 5.26, 40.0000, 7.89, 5.26, 13.15, 15.78, 20.0000, 2, 15, 0, NULL, NULL, '2026-04-10 15:17:58', '2026-04-25 14:37:17'),
(1169, NULL, 102, 'main d\'oeuvre', NULL, 1.00, 0.00, 37.50, 45.00, 37.50, 100.0000, 0.00, 37.50, 37.50, 45.00, 20.0000, 3, 0, 0, NULL, NULL, '2026-04-10 15:17:58', '2026-04-25 14:37:17'),
(1170, NULL, 102, 'réglage derailleur', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-04-10 15:17:58', '2026-04-25 14:37:17'),
(1171, NULL, 102, 'réglage freins', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-04-10 15:17:58', '2026-04-25 14:37:17'),
(1172, NULL, 102, 'tige de selle', '460980', 1.00, 8.16, 13.60, 16.32, 5.44, 40.0000, 8.16, 5.44, 13.60, 16.32, 20.0000, 6, NULL, 0, NULL, NULL, '2026-04-10 15:17:58', '2026-04-25 14:37:17'),
(1167, NULL, 102, 'chaine 7V VAE', '536647', 1.00, 12.95, 19.43, 23.31, 6.48, 33.3333, 12.95, 6.48, 19.43, 23.31, 20.0000, 1, 15, 0, NULL, NULL, '2026-04-10 15:17:58', '2026-04-25 14:37:17'),
(1166, NULL, 102, 'pneus AR', '507410', 1.00, 17.00, 27.42, 32.90, 10.42, 37.9939, 17.00, 10.42, 27.42, 32.90, 20.0000, 0, 15, 0, NULL, NULL, '2026-04-10 15:17:58', '2026-04-25 14:37:17'),
(1055, NULL, 109, 'réglage de frein AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-07 09:56:47', '2026-04-07 09:56:47'),
(1340, NULL, 105, 'bequille', NULL, 1.00, 13.33, 19.08, 22.90, 5.75, 30.1485, 13.33, 5.75, 19.08, 22.90, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-18 15:38:34', '2026-04-18 15:38:34'),
(1339, NULL, 105, 'chaine 9v renforcée', NULL, 1.00, 15.00, 31.67, 38.00, 16.67, 52.6316, 15.00, 16.67, 31.67, 38.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-18 15:38:34', '2026-04-18 15:38:34'),
(1338, NULL, 105, 'devoilage AV AR', NULL, 2.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 33.33, 33.33, 40.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-18 15:38:34', '2026-04-18 15:38:34'),
(1376, NULL, 110, 'Collier de tige de selle', '441451', 1.00, 2.25, 0.00, 0.00, -2.25, 0.0000, 2.25, -2.25, 0.00, 0.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-21 10:20:59', '2026-04-21 10:20:59'),
(1377, NULL, 110, 'panier', '527603', 1.00, 12.95, 0.00, 0.00, -12.95, 0.0000, 12.95, -12.95, 0.00, 0.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-21 10:20:59', '2026-04-21 10:20:59'),
(1074, NULL, 104, 'nettoyage chaine + lubrification', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-08 13:47:55', '2026-04-30 14:10:24'),
(1073, NULL, 104, 'réglage vitesses', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-08 13:47:55', '2026-04-30 14:10:24'),
(1341, NULL, 105, 'main d\'oeuvre', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-04-18 15:38:34', '2026-04-18 15:38:34'),
(1363, NULL, 112, 'devoilage AV AR', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-21 07:31:24', '2026-04-21 07:31:24'),
(1364, NULL, 112, 'réglage freins AV AR', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-21 07:31:24', '2026-04-21 07:31:24'),
(1365, NULL, 112, 'réglage dérailleur', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-21 07:31:24', '2026-04-21 07:31:24'),
(1366, NULL, 112, 'pneus 26x44 AV AR', NULL, 2.00, 4.80, 8.20, 9.84, 3.40, 41.4634, 9.60, 6.80, 16.40, 19.68, 20.0000, 3, NULL, 0, NULL, NULL, '2026-04-21 07:31:24', '2026-04-21 07:31:24'),
(1367, NULL, 112, 'gaine câble dérailleur AR', NULL, 1.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 1.90, 14.77, 16.67, 20.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-04-21 07:31:24', '2026-04-21 07:31:24'),
(1403, NULL, 106, 'pneu 26x35', '459199', 2.00, 6.16, 8.33, 9.99, 2.17, 26.0060, 12.32, 4.33, 16.65, 19.98, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-22 15:04:49', '2026-05-12 08:58:07'),
(1404, NULL, 106, 'cable gaine frein AR', NULL, 1.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 1.90, 14.77, 16.67, 20.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-22 15:04:49', '2026-05-12 08:58:07'),
(1405, NULL, 106, 'devoilage AV AR mini', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-22 15:04:49', '2026-05-12 08:58:07'),
(1406, NULL, 106, 'disquer antivol sans code offert', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-04-22 15:04:49', '2026-05-12 08:58:07'),
(1408, NULL, 129, 'Main d\'oeuvre + gaine', NULL, 1.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 1.90, 14.77, 16.67, 20.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-22 15:06:25', '2026-05-29 04:28:02'),
(1173, NULL, 102, 'adaptateur tige de selle', '489632', 1.00, 1.77, 3.07, 3.68, 1.30, 42.2826, 1.77, 1.30, 3.07, 3.68, 20.0000, 7, NULL, 0, NULL, NULL, '2026-04-10 15:17:58', '2026-04-25 14:37:17'),
(1325, NULL, 114, 'main d\'oeuvre plaquettes', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-16 09:37:56', '2026-04-16 09:37:56'),
(1322, NULL, 123, 'chambre à air', NULL, 2.00, 1.75, 3.25, 3.90, 1.50, 46.1538, 3.50, 3.00, 6.50, 7.80, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-16 09:32:18', '2026-04-16 09:32:18'),
(1301, NULL, 113, 'gaine cable frein AR', NULL, 1.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 1.90, 14.77, 16.67, 20.00, 20.0000, 0, 15, 0, NULL, NULL, '2026-04-16 08:57:54', '2026-04-24 07:30:08'),
(1302, NULL, 113, 'gaine cable derailleur', NULL, 1.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 1.90, 14.77, 16.67, 20.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-04-16 08:57:54', '2026-04-24 07:30:08'),
(1303, NULL, 113, 'pneus ROCK II', '494909', 2.00, 8.48, 10.83, 12.99, 2.35, 21.6628, 16.96, 4.69, 21.65, 25.98, 20.0000, 2, 30, 1, '2026-04-24 19:25:07', '2026-04-24 19:25:09', '2026-04-16 08:57:54', '2026-04-24 19:25:09'),
(1304, NULL, 113, 'patin (AV / AR)', NULL, 2.00, 1.32, 4.58, 5.50, 3.26, 71.2000, 2.64, 6.53, 9.17, 11.00, 20.0000, 3, 15, 0, NULL, NULL, '2026-04-16 08:57:54', '2026-04-24 07:30:08'),
(1305, NULL, 113, 'main d\'oeuvre', NULL, 1.00, 0.00, 29.17, 35.00, 29.17, 100.0000, 0.00, 29.17, 29.17, 35.00, 20.0000, 4, 15, 0, NULL, NULL, '2026-04-16 08:57:54', '2026-04-24 07:30:08'),
(1234, NULL, 117, 'patins freins av ar', NULL, 2.00, 1.24, 4.58, 5.50, 3.34, 72.9455, 2.48, 6.69, 9.17, 11.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-04-11 15:08:08', '2026-04-11 15:08:08'),
(1233, NULL, 117, 'reglages freins av ar', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 5, 30, 0, NULL, NULL, '2026-04-11 15:08:08', '2026-04-11 15:08:08'),
(1232, NULL, 117, 'plateau 24/42  axe 120mm', '484541', 1.00, 11.95, 19.92, 23.90, 7.97, 40.0000, 11.95, 7.97, 19.92, 23.90, 20.0000, 4, 15, 0, NULL, NULL, '2026-04-11 15:08:08', '2026-04-11 15:08:08'),
(1231, NULL, 117, 'roue libre 14/28', '210019', 1.00, 6.50, 9.75, 11.70, 3.25, 33.3333, 6.50, 3.25, 9.75, 11.70, 20.0000, 3, 15, 0, NULL, NULL, '2026-04-11 15:08:08', '2026-04-11 15:08:08'),
(1230, NULL, 117, 'chaine 6v', NULL, 1.00, 3.94, 11.67, 14.00, 7.73, 66.2286, 3.94, 7.73, 11.67, 14.00, 20.0000, 2, 15, 0, NULL, NULL, '2026-04-11 15:08:08', '2026-04-11 15:08:08'),
(1189, NULL, 115, 'patins AV AR', NULL, 2.00, 1.28, 4.58, 5.50, 3.30, 72.0727, 2.56, 6.61, 9.17, 11.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-11 14:05:00', '2026-04-30 15:13:17'),
(1190, NULL, 115, 'reglages freins AV AR', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 1, 30, 0, NULL, NULL, '2026-04-11 14:05:00', '2026-04-30 15:13:17'),
(1191, NULL, 115, 'pneu AV 26x1.75', '478475', 1.00, 8.89, 17.42, 20.90, 8.53, 48.9569, 8.89, 8.53, 17.42, 20.90, 20.0000, 2, 15, 1, '2026-04-29 06:44:54', '2026-04-30 14:16:38', '2026-04-11 14:05:00', '2026-04-30 15:13:17'),
(1440, NULL, 115, 'montage pneu', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-04-28 14:58:46', '2026-04-30 15:13:17'),
(1441, NULL, 141, 'dévoilage AV/AR', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 0, 30, 0, NULL, NULL, '2026-04-28 15:10:50', '2026-04-30 12:43:00'),
(1235, NULL, 117, 'main d\'oeuvre transmission', NULL, 1.00, 0.00, 33.33, 40.00, 33.33, 100.0000, 0.00, 33.33, 33.33, 40.00, 20.0000, 7, NULL, 0, NULL, NULL, '2026-04-11 15:08:08', '2026-04-11 15:08:08'),
(1229, NULL, 117, 'entretien roulement AR', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 1, 30, 0, NULL, NULL, '2026-04-11 15:08:08', '2026-04-11 15:08:08'),
(1228, NULL, 117, 'devoilage AR', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 0, 30, 0, NULL, NULL, '2026-04-11 15:08:08', '2026-04-11 15:08:08'),
(1243, NULL, 79, 'main d\'oeuvre pneu / chambre à air', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 7, NULL, 0, NULL, NULL, '2026-04-13 14:02:42', '2026-04-13 14:02:42'),
(1237, NULL, 79, 'serrage panier + cales', NULL, 1.00, 0.00, 6.67, 8.00, 6.67, 100.0000, 0.00, 6.67, 6.67, 8.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-04-13 14:02:42', '2026-04-13 14:02:42'),
(1236, NULL, 79, 'reglage frein AV AR', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 0, 30, 0, NULL, NULL, '2026-04-13 14:02:42', '2026-04-13 14:02:42'),
(1247, NULL, 120, 'montage', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-14 08:41:52', '2026-04-14 08:41:52'),
(1246, NULL, 120, 'chambre a air', NULL, 1.00, 4.95, 8.25, 9.90, 3.30, 40.0000, 4.95, 3.30, 8.25, 9.90, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-14 08:41:52', '2026-04-14 08:41:52'),
(1258, NULL, 72, 'pignon 22T', '533524', 1.00, 3.80, 4.16, 4.99, 0.36, 8.6172, 3.80, 0.36, 4.16, 4.99, 20.0000, 4, NULL, 1, '2026-05-01 14:31:22', '2026-05-10 13:59:29', '2026-04-14 09:05:32', '2026-05-29 21:05:31'),
(1257, NULL, 72, 'chaine 1v', '481385', 1.00, 6.70, 10.05, 12.06, 3.35, 33.3333, 6.70, 3.35, 10.05, 12.06, 20.0000, 3, NULL, 0, NULL, NULL, '2026-04-14 09:05:32', '2026-05-29 21:05:31'),
(1256, NULL, 72, 'gaine cable freins AR', NULL, 1.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 1.90, 14.77, 16.67, 20.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-14 09:05:32', '2026-05-29 21:05:31'),
(1255, NULL, 72, 'equilibrage freins AV', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-14 09:05:32', '2026-05-29 21:05:31'),
(1254, NULL, 72, 'devoilage AR AVG', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-14 09:05:32', '2026-05-29 21:05:31'),
(1259, NULL, 72, 'pedalier (33d - 125mm axe)', '484512', 1.00, 15.99, 26.65, 31.98, 10.66, 40.0000, 15.99, 10.66, 26.65, 31.98, 20.0000, 5, NULL, 1, '2026-05-01 14:31:31', '2026-05-10 13:59:31', '2026-04-14 09:05:32', '2026-05-29 21:05:31'),
(1356, NULL, 118, 'gaine / cable dérailleur AR', NULL, 1.00, 1.80, 16.67, 20.00, 14.87, 89.2000, 1.80, 14.87, 16.67, 20.00, 20.0000, 1, 30, 0, NULL, NULL, '2026-04-20 09:44:07', '2026-04-23 12:08:52'),
(1357, NULL, 118, 'dévoilage (AV/AR)', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 2, 30, 0, NULL, NULL, '2026-04-20 09:44:07', '2026-04-23 12:08:52'),
(1358, NULL, 118, 'Pneu AR', 'P2R 5625', 1.00, 5.26, 8.25, 9.90, 2.99, 36.2424, 5.26, 2.99, 8.25, 9.90, 20.0000, 3, NULL, 0, NULL, NULL, '2026-04-20 09:44:07', '2026-04-23 12:08:52'),
(1359, NULL, 118, 'chaine 6V', NULL, 1.00, 3.94, 14.17, 17.00, 10.23, 72.1882, 3.94, 10.23, 14.17, 17.00, 20.0000, 4, 15, 0, NULL, NULL, '2026-04-20 09:44:07', '2026-04-23 12:08:52'),
(1360, NULL, 118, 'main d\'oeuvre transmission', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-04-20 09:44:07', '2026-04-23 12:08:52'),
(1361, NULL, 118, 'chambre à air AR 26X1.50', '532754', 1.00, 3.40, 8.25, 9.90, 4.85, 58.7879, 3.40, 4.85, 8.25, 9.90, 20.0000, 6, 15, 0, NULL, NULL, '2026-04-20 09:44:07', '2026-04-23 12:08:52'),
(1362, NULL, 118, 'main d\'oeuvre chambre à air', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 7, NULL, 0, NULL, NULL, '2026-04-20 09:44:07', '2026-04-23 12:08:52'),
(1271, NULL, 119, 'chaine 7V', NULL, 1.00, 3.94, 11.67, 14.00, 7.73, 66.2286, 3.94, 7.73, 11.67, 14.00, 20.0000, 0, 15, 0, NULL, NULL, '2026-04-15 12:44:12', '2026-04-23 12:08:36'),
(1272, NULL, 119, 'main d\'oeuvre chaine', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-15 12:44:12', '2026-04-23 12:08:36'),
(1273, NULL, 119, 'gaine / cable dérailleur AR', NULL, 1.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 1.90, 14.77, 16.67, 20.00, 20.0000, 2, 15, 0, NULL, NULL, '2026-04-15 12:44:12', '2026-04-23 12:08:36'),
(1274, NULL, 119, 'gaine / cable frein AV', NULL, 1.00, 1.50, 16.67, 20.00, 15.17, 91.0000, 1.50, 15.17, 16.67, 20.00, 20.0000, 3, 15, 0, NULL, NULL, '2026-04-15 12:44:12', '2026-04-23 12:08:36'),
(1275, NULL, 119, 'réglage frein AV', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 4, 15, 0, NULL, NULL, '2026-04-15 12:44:12', '2026-04-23 12:08:36'),
(1288, NULL, 101, 'dévoilage (AV/AR)', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-04-15 13:10:00', '2026-04-29 15:31:16'),
(1287, NULL, 101, 'main d\'oeuvre tranmission', NULL, 1.00, 0.00, 37.50, 45.00, 37.50, 100.0000, 0.00, 37.50, 37.50, 45.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-04-15 13:10:00', '2026-04-29 15:31:16'),
(1286, NULL, 101, 'chaine 105 12V', 'P2R 157707', 1.00, 25.00, 41.67, 50.00, 16.67, 40.0000, 25.00, 16.67, 41.67, 50.00, 20.0000, 3, 15, 0, NULL, NULL, '2026-04-15 13:10:00', '2026-04-29 15:31:16'),
(1285, NULL, 101, 'cassette 12V shimano 105', 'P2R 209505', 1.00, 40.90, 68.17, 81.80, 27.27, 40.0000, 40.90, 27.27, 68.17, 81.80, 20.0000, 2, 15, 0, NULL, NULL, '2026-04-15 13:10:00', '2026-04-29 15:31:16'),
(1284, NULL, 101, 'plateau 34 shimano 105 NX 12V', 'P2R 201480', 1.00, 12.60, 21.00, 25.20, 8.40, 40.0000, 12.60, 8.40, 21.00, 25.20, 20.0000, 1, 15, 0, NULL, NULL, '2026-04-15 13:10:00', '2026-04-29 15:31:16'),
(1283, NULL, 101, 'plateau 50 shimano 105 NX 12V@', '530173', 1.00, 52.72, 72.49, 86.99, 19.77, 27.2744, 52.72, 19.77, 72.49, 86.99, 20.0000, 0, 15, 0, NULL, NULL, '2026-04-15 13:10:00', '2026-04-29 15:31:16'),
(1289, NULL, 101, 'purge', NULL, 2.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 6, 30, 0, NULL, NULL, '2026-04-15 13:10:00', '2026-04-29 15:31:16'),
(1291, NULL, 121, 'chambre a air 700Cx32-40', '493773', 1.00, 1.57, 5.75, 6.90, 4.18, 72.6957, 1.57, 4.18, 5.75, 6.90, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-15 13:36:10', '2026-04-15 13:36:10'),
(1379, NULL, 122, 'panier', NULL, 1.00, 17.36, 0.00, 0.00, -17.36, 0.0000, 17.36, -17.36, 0.00, 0.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-21 10:22:50', '2026-04-21 10:22:50'),
(1323, NULL, 123, 'montage', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-16 09:32:18', '2026-04-16 09:32:18'),
(1324, NULL, 114, 'remplacement plaquette (AV/AR)', NULL, 2.00, 2.38, 8.75, 10.50, 6.37, 72.8000, 4.76, 12.74, 17.50, 21.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-16 09:37:56', '2026-04-16 09:37:56'),
(1326, NULL, 114, 'purge', NULL, 2.00, 0.00, 27.50, 33.00, 27.50, 100.0000, 0.00, 55.00, 55.00, 66.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-16 09:37:56', '2026-04-16 09:37:56'),
(1327, NULL, 124, 'regonflage roue AV', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-16 15:19:59', '2026-04-16 15:19:59'),
(1342, NULL, 128, 'purge frein AR', NULL, 1.00, 0.00, 27.50, 33.00, 27.50, 100.0000, 0.00, 27.50, 27.50, 33.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-18 15:52:09', '2026-04-18 15:52:09'),
(1337, NULL, 130, 'MO', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-18 14:31:47', '2026-04-18 14:31:47'),
(1336, NULL, 130, 'plaquettes AV AR', NULL, 2.00, 0.00, 8.75, 10.50, 8.75, 100.0000, 0.00, 17.50, 17.50, 21.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-18 14:31:47', '2026-04-18 14:31:47'),
(1407, NULL, 129, 'manette 7V indéxée', '458845', 1.00, 6.75, 14.07, 16.88, 7.32, 52.0142, 6.75, 7.32, 14.07, 16.88, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-22 15:06:25', '2026-05-29 04:28:02'),
(1355, NULL, 118, 'gaine / cable frein (AV/AR)', NULL, 2.00, 1.34, 16.67, 20.00, 15.33, 91.9600, 2.68, 30.65, 33.33, 40.00, 20.0000, 0, 30, 0, NULL, NULL, '2026-04-20 09:44:07', '2026-04-23 12:08:52'),
(1378, NULL, 122, 'Neomouv Carlina HY XR', NULL, 1.00, 1142.00, 1665.83, 1999.00, 523.83, 31.4457, 1142.00, 523.83, 1665.83, 1999.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-21 10:22:50', '2026-04-21 10:22:50'),
(1393, NULL, 125, 'reglage freins AV', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-04-22 06:45:42', '2026-04-23 12:08:21'),
(1392, NULL, 125, 'montage pneus', NULL, 1.00, 0.00, 4.17, 5.00, 4.17, 100.0000, 0.00, 4.17, 4.17, 5.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-22 06:45:42', '2026-04-23 12:08:21'),
(1391, NULL, 125, 'patins AV', NULL, 2.00, 1.34, 4.58, 5.50, 3.24, 70.7636, 2.68, 6.49, 9.17, 11.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-22 06:45:42', '2026-04-23 12:08:21'),
(1375, NULL, 110, 'Carlina NGE', NULL, 1.00, 847.76, 1249.17, 1499.00, 401.41, 32.1340, 847.76, 401.41, 1249.17, 1499.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-21 10:20:59', '2026-04-21 10:20:59'),
(1390, NULL, 125, 'pneu 16 x 1.75', '465752', 2.00, 2.38, 4.58, 5.50, 2.20, 48.0727, 4.76, 4.41, 9.17, 11.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-22 06:45:42', '2026-04-23 12:08:21'),
(1389, NULL, 86, 'montage capteur pedalier', NULL, 1.00, 0.00, 37.50, 45.00, 37.50, 100.0000, 0.00, 37.50, 37.50, 45.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-21 15:52:47', '2026-04-21 15:52:47'),
(1394, NULL, 132, 'casque', '498978', 1.00, 24.95, 41.58, 49.90, 16.63, 40.0000, 24.95, 16.63, 41.58, 49.90, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-22 09:28:14', '2026-04-29 14:55:55'),
(1398, NULL, 133, 'montage', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-22 14:17:39', '2026-04-22 14:17:39'),
(1397, NULL, 133, 'chambre a air', '476517', 1.00, 2.48, 3.33, 4.00, 0.86, 25.7500, 2.48, 0.86, 3.33, 4.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-22 14:17:39', '2026-04-22 14:17:39'),
(1409, NULL, 134, 'gaine cable AV/AR', NULL, 2.00, 0.00, 11.67, 14.00, 11.67, 100.0000, 0.00, 23.33, 23.33, 28.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-23 06:48:24', '2026-04-23 15:51:25'),
(1410, NULL, 134, 'réglage frein av/ar', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-23 06:48:24', '2026-04-23 15:51:25'),
(1411, NULL, 134, 'changement de plateau/pedale', NULL, 1.00, 0.00, 41.67, 50.00, 41.67, 100.0000, 0.00, 41.67, 41.67, 50.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-23 06:48:24', '2026-04-23 15:51:25'),
(1412, NULL, 134, 'main d\'oeuvre transmission', NULL, 1.00, 0.00, 33.33, 40.00, 33.33, 100.0000, 0.00, 33.33, 33.33, 40.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-04-23 06:48:24', '2026-04-23 15:51:25'),
(1413, NULL, 113, 'roue libre', '513608', 1.00, 7.00, 11.67, 14.00, 4.67, 40.0000, 7.00, 4.67, 11.67, 14.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-04-24 07:22:54', '2026-04-24 07:30:08'),
(1414, NULL, 113, 'chaine + installation', NULL, 1.00, 3.94, 25.00, 30.00, 21.06, 84.2400, 3.94, 21.06, 25.00, 30.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-04-24 07:22:54', '2026-04-24 07:30:09'),
(1415, NULL, 113, 'plateau', NULL, 1.00, 14.16, 16.66, 19.99, 2.50, 14.9975, 14.16, 2.50, 16.66, 19.99, 20.0000, 7, NULL, 0, NULL, NULL, '2026-04-24 07:22:54', '2026-04-24 07:30:09'),
(1416, NULL, 136, 'chaine 6v + montage', NULL, 1.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-24 09:18:05', '2026-04-24 09:24:59'),
(1417, NULL, 137, 'jante occasion', NULL, 1.00, 0.00, 33.33, 40.00, 33.33, 100.0000, 0.00, 33.33, 33.33, 40.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-24 10:26:13', '2026-04-24 10:27:05'),
(1419, NULL, 137, 'cable de frein', NULL, 1.00, 0.00, 2.92, 3.50, 2.92, 100.0000, 0.00, 2.92, 2.92, 3.50, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-24 10:26:58', '2026-04-24 10:27:05'),
(1420, NULL, 138, 'anneau de maintens de selle 27.5mm', 'A trouver', 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 0, NULL, 1, NULL, NULL, '2026-04-24 10:35:47', '2026-04-24 19:26:22'),
(1421, NULL, 131, 'devoilage AV AR', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-24 12:40:56', '2026-05-20 08:28:33'),
(1422, NULL, 131, 'pneus AV AR 700x23c', '510070', 2.00, 6.50, 10.83, 13.00, 4.33, 40.0000, 13.00, 8.67, 21.67, 26.00, 20.0000, 1, NULL, 1, '2026-04-29 06:44:43', '2026-04-29 06:44:45', '2026-04-24 12:40:56', '2026-05-20 08:28:33'),
(1423, NULL, 131, 'reglage frein AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-24 12:40:56', '2026-05-20 08:28:33'),
(1424, NULL, 139, 'chambre a air 26x1.75', NULL, 1.00, 0.00, 3.33, 4.00, 3.33, 100.0000, 0.00, 3.33, 3.33, 4.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-24 14:42:54', '2026-04-24 14:43:01'),
(1425, NULL, 139, 'mobntage', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-24 14:42:54', '2026-04-24 14:43:01'),
(1426, NULL, 140, 'plaquette tete standard', NULL, 4.00, 0.00, 8.75, 10.50, 8.75, 100.0000, 0.00, 35.00, 35.00, 42.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-25 07:42:33', '2026-04-25 07:42:41'),
(1427, NULL, 142, 'gilet orange', NULL, 2.00, 0.00, 8.33, 10.00, 8.33, 100.0000, 0.00, 16.67, 16.67, 20.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-25 14:29:11', '2026-04-25 14:29:11'),
(1428, NULL, 143, 'Kit de conversion Virvolt 500', 'virvolt 500', 1.00, 334.00, 500.00, 600.00, 166.00, 33.2000, 334.00, 166.00, 500.00, 600.00, 20.0000, 0, NULL, 1, '2026-06-01 16:47:01', '2026-06-01 16:47:03', '2026-04-27 09:41:41', '2026-06-04 07:13:28'),
(1544, NULL, 162, 'plateau', '141368 P2R', 1.00, 24.15, 27.50, 33.00, 3.35, 12.1818, 24.15, 3.35, 27.50, 33.00, 20.0000, 1, NULL, 1, '2026-05-10 14:25:50', '2026-06-01 16:47:16', '2026-05-07 09:12:11', '2026-06-01 16:47:16'),
(1542, NULL, 170, 'Linaria', NULL, 1.00, 610.00, 895.83, 1075.00, 285.83, 31.9070, 610.00, 285.83, 895.83, 1075.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-06 17:16:24', '2026-06-05 13:36:40'),
(1431, NULL, 144, 'remplacement contrôleur 1h', NULL, 1.00, 0.00, 50.00, 60.00, 50.00, 100.0000, 0.00, 50.00, 50.00, 60.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-27 10:13:39', '2026-04-27 10:14:29'),
(1432, NULL, 101, 'plaquette (AV/AR) L05A', '505182', 2.00, 16.45, 16.45, 19.74, 0.00, 0.0000, 32.90, 0.00, 32.90, 39.48, 20.0000, 7, NULL, 0, NULL, NULL, '2026-04-27 15:00:16', '2026-04-29 15:31:16'),
(1433, NULL, 147, 'chambre a air hutchinson 16 x 1.30 à 1.90', NULL, 1.00, 2.95, 5.42, 6.50, 2.47, 45.5385, 2.95, 2.47, 5.42, 6.50, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-28 09:55:54', '2026-04-29 06:44:16'),
(1434, NULL, 149, 'diag', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-28 12:56:16', '2026-04-28 13:05:29'),
(1435, NULL, 150, 'plaquette', NULL, 2.00, 0.00, 8.75, 10.50, 8.75, 100.0000, 0.00, 17.50, 17.50, 21.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-28 13:47:52', '2026-05-07 08:02:05'),
(1436, NULL, 135, 'chaine 5v', '21785 P2R', 1.00, 3.90, 8.57, 10.28, 4.67, 54.4747, 3.90, 4.67, 8.57, 10.28, 20.0000, 0, NULL, 1, '2026-05-04 06:24:16', '2026-06-01 16:47:06', '2026-04-28 14:41:33', '2026-06-26 13:21:40'),
(1437, NULL, 135, 'gaine cable frein AR', NULL, 1.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 1.90, 14.77, 16.67, 20.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-28 14:41:33', '2026-06-26 13:21:40'),
(1438, NULL, 135, 'dévoilage (AV/AR)', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-28 14:41:33', '2026-06-26 13:21:40'),
(1439, NULL, 135, 'chambre a air AR 26x 1   1/2', '183128 P2R', 1.00, 3.30, 6.88, 8.25, 3.58, 52.0000, 3.30, 3.58, 6.88, 8.25, 20.0000, 3, NULL, 1, '2026-05-04 06:24:18', '2026-05-10 13:59:41', '2026-04-28 14:41:33', '2026-06-26 13:21:40'),
(1442, NULL, 141, 'réglage dérailleur AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, 15, 0, NULL, NULL, '2026-04-28 15:10:50', '2026-04-30 12:43:00'),
(1443, NULL, 141, 'lubrification chaine', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-28 15:10:50', '2026-04-30 12:43:00'),
(1444, NULL, 151, 'prestation soudure', NULL, 1.00, 0.00, 66.67, 80.00, 66.67, 100.0000, 0.00, 66.67, 66.67, 80.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-29 13:06:44', '2026-06-19 09:54:44'),
(1445, NULL, 151, 'preparation vélo', NULL, 1.00, 0.00, 58.33, 70.00, 58.33, 100.0000, 0.00, 58.33, 58.33, 70.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-29 13:06:44', '2026-06-19 09:54:44'),
(1446, NULL, 152, 'reglage frein AV', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-29 13:55:43', '2026-05-26 10:30:16'),
(1447, NULL, 152, 'gaine / cable frein AR', NULL, 1.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 1.90, 14.77, 16.67, 20.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-29 13:55:43', '2026-05-26 10:30:16'),
(1592, NULL, 179, 'panier optimiz', '527602', 1.00, 11.50, 19.17, 23.00, 7.67, 40.0000, 11.50, 7.67, 19.17, 23.00, 20.0000, 0, NULL, 1, NULL, NULL, '2026-05-12 09:09:54', '2026-05-12 09:11:01'),
(1449, NULL, 154, 'NCM PARIS', NULL, 1.00, 535.00, 757.50, 909.00, 222.50, 29.3729, 535.00, 222.50, 757.50, 909.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-29 14:58:51', '2026-04-29 14:59:01'),
(1450, NULL, 90, 'Galets derailleur AR', '495802', 1.00, 8.43, 0.00, 0.00, -8.43, 0.0000, 8.43, -8.43, 0.00, 0.00, 20.0000, 12, NULL, 0, NULL, NULL, '2026-04-29 15:04:34', '2026-04-29 15:04:34'),
(1451, NULL, 72, 'frein tambour', '533454', 1.00, 44.90, 57.07, 68.49, 12.17, 21.3316, 44.90, 12.17, 57.07, 68.49, 20.0000, 6, NULL, 1, '2026-05-01 14:31:42', '2026-05-10 13:59:44', '2026-04-30 13:20:58', '2026-05-29 21:05:31'),
(1452, NULL, 156, 'béquille lateral', '484687', 1.00, 0.00, 12.53, 15.04, 12.53, 100.0000, 0.00, 12.53, 12.53, 15.04, 20.0000, 0, NULL, 1, '2026-05-01 14:31:51', '2026-05-10 13:59:46', '2026-04-30 14:40:10', '2026-05-15 13:26:58'),
(1453, NULL, 146, 'purge annuelle', NULL, 2.00, 0.00, 27.50, 33.00, 27.50, 100.0000, 0.00, 55.00, 55.00, 66.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-30 14:51:52', '2026-05-12 07:26:12'),
(1463, NULL, 146, 'dévoilage AR', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-30 15:27:03', '2026-05-12 07:26:12'),
(1455, NULL, 145, 'pneu  AR  27.5x2.20', '530069 ou 207152 P2R', 1.00, 22.00, 35.00, 42.00, 13.00, 37.1429, 22.00, 13.00, 35.00, 42.00, 20.0000, 0, NULL, 1, '2026-05-01 14:33:43', '2026-05-10 13:59:50', '2026-04-30 14:54:42', '2026-05-12 07:27:15'),
(1464, NULL, 158, 'cable + gaine frein AR', NULL, 1.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 1.90, 14.77, 16.67, 20.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-30 15:47:00', '2026-04-30 15:47:00'),
(1465, NULL, 158, 'etrier de frein AR', '251765', 1.00, 14.93, 17.49, 20.99, 2.56, 14.6451, 14.93, 2.56, 17.49, 20.99, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-30 15:47:00', '2026-04-30 15:47:00'),
(1466, NULL, 158, 'dégrippage étrier AV', NULL, 1.00, 4.00, 12.50, 15.00, 8.50, 68.0000, 4.00, 8.50, 12.50, 15.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-04-30 15:47:00', '2026-04-30 15:47:00'),
(1467, NULL, 158, 'cable + gaine dérailleur AR', NULL, 1.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 1.90, 14.77, 16.67, 20.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-04-30 15:47:00', '2026-04-30 15:47:00'),
(1462, NULL, 145, 'main d\'oeuvre', NULL, 1.00, 0.00, 41.67, 50.00, 41.67, 100.0000, 0.00, 41.67, 41.67, 50.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-30 15:20:36', '2026-05-12 07:27:15'),
(1461, NULL, 157, 'VAE Carlina HY NG', NULL, 1.00, 986.76, 1375.00, 1650.00, 388.24, 28.2356, 986.76, 388.24, 1375.00, 1650.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-30 15:12:42', '2026-05-01 14:30:01'),
(1468, NULL, 158, 'pneu AR 26 x 1.75', '479163', 1.00, 6.10, 11.18, 13.42, 5.08, 45.4545, 6.10, 5.08, 11.18, 13.42, 20.0000, 4, NULL, 0, NULL, NULL, '2026-04-30 15:47:00', '2026-04-30 15:47:00');
INSERT INTO `quote_lines` (`id`, `article_id`, `quote_id`, `title`, `reference`, `quantity`, `purchase_price_ht`, `sale_price_ht`, `sale_price_ttc`, `margin_amount_ht`, `margin_rate`, `line_purchase_ht`, `line_margin_ht`, `line_total_ht`, `line_total_ttc`, `tva_rate`, `position`, `estimated_time_minutes`, `needs_order`, `ordered_at`, `received_at`, `created_at`, `updated_at`) VALUES
(1469, NULL, 158, 'chaine 6V', NULL, 1.00, 3.90, 15.00, 18.00, 11.10, 74.0000, 3.90, 11.10, 15.00, 18.00, 20.0000, 5, 15, 0, NULL, NULL, '2026-04-30 15:47:00', '2026-04-30 15:47:00'),
(1470, NULL, 158, 'roue libre 14-28', '513608', 1.00, 7.00, 11.67, 14.00, 4.67, 40.0000, 7.00, 4.67, 11.67, 14.00, 20.0000, 6, 15, 0, NULL, NULL, '2026-04-30 15:47:00', '2026-04-30 15:47:00'),
(1471, NULL, 158, 'plateau simple 42d', '489375', 1.00, 18.95, 31.58, 37.90, 12.63, 40.0000, 18.95, 12.63, 31.58, 37.90, 20.0000, 7, 15, 0, NULL, NULL, '2026-04-30 15:47:00', '2026-04-30 15:47:00'),
(1472, NULL, 158, 'main d\'oeuvre tranmission', NULL, 1.00, 0.00, 33.33, 40.00, 33.33, 100.0000, 0.00, 33.33, 33.33, 40.00, 20.0000, 8, NULL, 0, NULL, NULL, '2026-04-30 15:47:00', '2026-04-30 15:47:00'),
(1473, NULL, 158, 'main d\'oeuvre pneu', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 9, NULL, 0, NULL, NULL, '2026-04-30 15:47:00', '2026-04-30 15:47:00'),
(1474, NULL, 159, 'pneus occasion', NULL, 2.00, 0.00, 6.67, 8.00, 6.67, 100.0000, 0.00, 13.33, 13.33, 16.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-04-30 15:49:24', '2026-04-30 15:50:00'),
(1475, NULL, 159, 'main d\'oeuvre', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-04-30 15:49:24', '2026-04-30 15:50:00'),
(1476, NULL, 157, 'rétroviseur', '495058', 1.00, 8.50, 0.00, 0.00, -8.50, 0.0000, 8.50, -8.50, 0.00, 0.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-01 14:29:54', '2026-05-01 14:30:01'),
(1477, NULL, 157, 'panier', '527602', 1.00, 11.50, 0.00, 0.00, -11.50, 0.0000, 11.50, -11.50, 0.00, 0.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-01 14:29:54', '2026-05-01 14:30:01'),
(1478, NULL, 135, 'pneu AR', '152969 P2R', 1.00, 7.76, 16.17, 19.40, 8.41, 52.0000, 7.76, 8.41, 16.17, 19.40, 20.0000, 4, NULL, 1, '2026-05-04 06:24:35', '2026-06-01 16:47:11', '2026-05-01 14:50:39', '2026-06-26 13:21:40'),
(1488, NULL, 148, 'main d\'oeuvre pneus', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-05-02 08:49:18', '2026-05-08 21:03:24'),
(1487, NULL, 148, 'garde boue AV (meme fixation que RIV 500)', '186447 P2R', 1.00, 8.25, 17.19, 20.63, 8.94, 52.0116, 8.25, 8.94, 17.19, 20.63, 20.0000, 3, NULL, 1, '2026-05-04 06:38:10', '2026-05-10 14:00:01', '2026-05-02 08:49:18', '2026-05-10 14:00:01'),
(1486, NULL, 148, 'pneus AV AR 26.2.10 Michelin', '26203 P2R P2R', 2.00, 9.95, 16.58, 19.90, 6.63, 40.0000, 19.90, 13.27, 33.17, 39.80, 20.0000, 2, NULL, 1, '2026-05-04 06:38:11', '2026-05-10 14:00:05', '2026-05-02 08:49:18', '2026-05-10 14:00:05'),
(1485, NULL, 148, 'reglage derailleur AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-02 08:49:18', '2026-05-08 21:03:24'),
(1484, NULL, 148, 'devoilage AR', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-02 08:49:18', '2026-05-08 21:03:24'),
(1489, NULL, 153, 'pneus AV AR  700x38 vtc', '223929 P2R', 2.00, 9.31, 15.52, 18.62, 6.21, 40.0000, 18.62, 12.41, 31.03, 37.24, 20.0000, 0, NULL, 1, '2026-05-04 06:24:56', '2026-05-10 14:00:06', '2026-05-02 09:58:12', '2026-05-20 08:06:04'),
(1524, NULL, 65, 'guidoline', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 8, NULL, 0, NULL, NULL, '2026-05-04 14:07:27', '2026-05-30 14:03:37'),
(1523, NULL, 65, 'cassette 11-34', '497879', 1.00, 11.45, 16.20, 19.44, 4.75, 29.3210, 11.45, 4.75, 16.20, 19.44, 20.0000, 7, 15, 1, '2026-05-10 14:25:46', '2026-06-01 16:47:14', '2026-05-04 14:07:27', '2026-06-01 16:47:14'),
(1492, NULL, 153, 'chambre a air AV 700x38', NULL, 1.00, 1.55, 5.75, 6.90, 4.20, 73.0435, 1.55, 4.20, 5.75, 6.90, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-02 09:58:12', '2026-05-20 08:06:04'),
(1493, NULL, 153, 'plateau 36T axe carré 115mm', 'https://www.decathlon.fr/p/plateau-vtc-36-dents-aluminium-et-son-carter/_/R-p-203451?mc=8618360', 1.00, 32.50, 32.50, 39.00, 0.00, 0.0000, 32.50, 0.00, 32.50, 39.00, 20.0000, 2, NULL, 1, '2026-05-06 17:02:22', '2026-05-10 14:00:08', '2026-05-02 09:58:12', '2026-05-20 08:06:04'),
(1495, NULL, 153, 'cassette 11-40', '148033 P2R', 1.00, 37.01, 42.50, 51.00, 5.49, 12.9176, 37.01, 5.49, 42.50, 51.00, 20.0000, 3, NULL, 1, '2026-05-04 06:25:32', '2026-05-10 14:00:12', '2026-05-02 09:58:12', '2026-05-20 08:06:04'),
(1504, NULL, 153, 'Main d\'oeuvre transmission', NULL, 1.00, 0.00, 37.50, 45.00, 37.50, 100.0000, 0.00, 37.50, 37.50, 45.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-05-03 07:30:17', '2026-05-20 08:06:04'),
(1498, NULL, 153, 'chaine 10v', '207387', 1.00, 13.90, 23.17, 27.80, 9.27, 40.0000, 13.90, 9.27, 23.17, 27.80, 20.0000, 4, NULL, 1, '2026-05-04 06:25:33', '2026-05-10 14:00:15', '2026-05-02 09:58:12', '2026-05-20 08:06:04'),
(1500, NULL, 153, 'Main d\'oeuvre pneus', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-05-02 09:58:12', '2026-05-20 08:06:04'),
(1501, NULL, 161, 'vis de reglage plaquette AR NCM T3s', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-02 13:43:42', '2026-05-02 13:43:42'),
(1521, NULL, 65, 'manette occasion 2X8', NULL, 1.00, 0.00, 66.67, 80.00, 66.67, 100.0000, 0.00, 66.67, 66.67, 80.00, 20.0000, 5, 15, 0, NULL, NULL, '2026-05-04 14:07:27', '2026-05-30 14:03:37'),
(1525, NULL, 163, 'roue libre  6V', '5361', 1.00, 7.30, 10.95, 13.14, 3.65, 33.3333, 7.30, 3.65, 10.95, 13.14, 20.0000, 0, NULL, 1, '2026-05-05 16:00:06', '2026-05-10 14:00:22', '2026-05-04 14:22:11', '2026-05-11 14:47:47'),
(1526, NULL, 163, 'main d\'oeuvre', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-04 14:22:11', '2026-05-11 14:47:47'),
(1527, NULL, 127, 'plaquette', '506167', 2.00, 3.50, 8.17, 9.80, 4.67, 57.1429, 7.00, 9.33, 16.33, 19.60, 20.0000, 0, NULL, 1, '2026-05-05 16:00:10', '2026-05-10 14:00:24', '2026-05-05 15:58:07', '2026-05-12 15:36:21'),
(1528, NULL, 127, 'remplacement du liquide', NULL, 2.00, 0.00, 27.50, 33.00, 27.50, 100.0000, 0.00, 55.00, 55.00, 66.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-05 15:58:07', '2026-05-12 15:36:21'),
(1531, NULL, 145, 'enlever batterie', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-06 12:57:48', '2026-05-12 07:27:15'),
(1532, NULL, 167, 'réglage frein AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-06 13:12:49', '2026-06-10 09:03:47'),
(1533, NULL, 167, 'remplacement patin AR', NULL, 1.00, 1.90, 13.33, 16.00, 11.43, 85.7500, 1.90, 11.43, 13.33, 16.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-06 13:12:49', '2026-06-10 09:03:47'),
(1534, NULL, 167, 'pneus AR + montage', '5632', 1.00, 5.06, 25.00, 30.00, 19.94, 79.7600, 5.06, 19.94, 25.00, 30.00, 20.0000, 2, NULL, 1, '2026-06-10 10:08:12', '2026-06-10 10:08:13', '2026-05-06 13:12:49', '2026-06-10 10:08:13'),
(1535, NULL, 167, 'dévoilage AV', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-05-06 13:12:49', '2026-06-10 09:03:47'),
(1536, NULL, 168, 'pneus (AV/AR)', '212189 P2R', 2.00, 7.94, 14.56, 17.47, 6.62, 45.4608, 15.88, 13.24, 29.12, 34.94, 20.0000, 0, 30, 1, '2026-06-10 10:08:14', '2026-06-10 10:08:15', '2026-05-06 13:17:42', '2026-06-10 10:08:15'),
(1537, NULL, 168, 'montage pneus', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-06 13:17:42', '2026-06-10 09:03:21'),
(1538, NULL, 168, 'réglage de frein (AV/AR)', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 2, 30, 0, NULL, NULL, '2026-05-06 13:17:42', '2026-06-10 09:03:21'),
(1539, NULL, 168, 'dévoilage (AV/AR)', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 3, 30, 0, NULL, NULL, '2026-05-06 13:17:42', '2026-06-10 09:03:21'),
(1540, NULL, 168, 'chambre à air AR', '493773', 1.00, 1.55, 5.00, 6.00, 3.45, 69.0000, 1.55, 3.45, 5.00, 6.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-05-06 13:17:42', '2026-06-10 09:03:21'),
(1543, NULL, 162, 'roue libre', '5361', 1.00, 7.30, 10.95, 13.14, 3.65, 33.3333, 7.30, 3.65, 10.95, 13.14, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-07 09:12:11', '2026-05-19 08:38:25'),
(1545, NULL, 162, 'chaine', NULL, 1.00, 3.94, 15.00, 18.00, 11.06, 73.7333, 3.94, 11.06, 15.00, 18.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-07 09:12:11', '2026-05-19 08:38:25'),
(1546, NULL, 162, 'main d\'oeuvre', NULL, 1.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-05-07 09:12:11', '2026-05-19 08:38:25'),
(1547, NULL, 172, 'pneus', '216281 P2R', 2.00, 14.36, 23.93, 28.72, 9.57, 40.0000, 28.72, 19.15, 47.87, 57.44, 20.0000, 0, NULL, 1, '2026-05-10 14:25:54', '2026-06-01 16:47:18', '2026-05-07 12:06:02', '2026-06-01 16:47:18'),
(1548, NULL, 174, 'garde boue', NULL, 1.00, 0.00, 21.66, 25.99, 21.66, 100.0000, 0.00, 21.66, 21.66, 25.99, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-07 15:58:43', '2026-05-07 16:16:31'),
(1549, NULL, 174, 'rétroviseur', NULL, 1.00, 0.00, 6.66, 7.99, 6.66, 100.0000, 0.00, 6.66, 6.66, 7.99, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-07 15:58:43', '2026-05-07 16:16:31'),
(1550, NULL, 174, 'porte bagage', NULL, 1.00, 0.00, 29.16, 34.99, 29.16, 100.0000, 0.00, 29.16, 29.16, 34.99, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-07 15:58:43', '2026-05-07 16:16:31'),
(1551, NULL, 174, 'montage et reglage', NULL, 1.00, 0.00, 33.33, 40.00, 33.33, 100.0000, 0.00, 33.33, 33.33, 40.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-05-07 15:58:43', '2026-05-07 16:16:31'),
(1554, NULL, 174, 'potence réglable', NULL, 1.00, 0.00, 16.66, 19.99, 16.66, 100.0000, 0.00, 16.66, 16.66, 19.99, 20.0000, 4, NULL, 0, NULL, NULL, '2026-05-07 16:16:31', '2026-05-07 16:16:31'),
(1555, NULL, 175, 'Carlina HY XR', NULL, 1.00, 986.76, 1457.50, 1749.00, 470.74, 32.2978, 986.76, 470.74, 1457.50, 1749.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-08 14:26:11', '2026-05-14 13:28:45'),
(1556, NULL, 175, 'sacoche', '527603', 1.00, 46.87, 62.49, 74.99, 15.62, 24.9980, 46.87, 15.62, 62.49, 74.99, 20.0000, 1, NULL, 1, NULL, NULL, '2026-05-08 14:26:11', '2026-05-14 13:28:45'),
(1557, NULL, 169, 'chaine 8V renforcée', '184012 P2R', 1.00, 14.71, 24.52, 29.42, 9.81, 40.0000, 14.71, 9.81, 24.52, 29.42, 20.0000, 0, NULL, 1, '2026-05-10 14:26:21', '2026-06-01 16:47:41', '2026-05-08 14:54:48', '2026-06-01 16:47:41'),
(1558, NULL, 169, 'cassette 11-30', '7740 P2R', 1.00, 13.85, 21.42, 25.70, 7.57, 35.3307, 13.85, 7.57, 21.42, 25.70, 20.0000, 1, NULL, 1, '2026-05-10 14:26:23', '2026-06-01 16:47:44', '2026-05-08 14:54:48', '2026-06-01 16:47:44'),
(1559, NULL, 169, 'plateau bosch 38d 104', '181119 P2R', 1.00, 31.00, 51.67, 62.00, 20.67, 40.0000, 31.00, 20.67, 51.67, 62.00, 20.0000, 2, NULL, 1, '2026-05-10 14:26:26', '2026-06-01 16:47:45', '2026-05-08 14:54:48', '2026-06-01 16:47:45'),
(1560, NULL, 169, 'dévoilage AV', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-05-08 14:54:48', '2026-05-16 14:27:50'),
(1561, NULL, 169, 'plaquettes (AV/AR)', NULL, 2.00, 0.00, 8.75, 10.50, 8.75, 100.0000, 0.00, 17.50, 17.50, 21.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-05-08 14:54:48', '2026-05-16 14:27:50'),
(1562, NULL, 169, 'purge (AV/AR)', NULL, 2.00, 0.00, 27.50, 33.00, 27.50, 100.0000, 0.00, 55.00, 55.00, 66.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-05-08 14:54:48', '2026-05-16 14:27:50'),
(1594, NULL, 180, 'panier', NULL, 1.00, 12.95, 21.58, 25.90, 8.63, 40.0000, 12.95, 8.63, 21.58, 25.90, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-12 13:51:58', '2026-05-12 14:13:46'),
(1564, NULL, 169, 'pédales', '222082 P2R', 1.00, 7.71, 16.07, 19.28, 8.36, 52.0124, 7.71, 8.36, 16.07, 19.28, 20.0000, 6, NULL, 1, '2026-05-10 14:26:28', '2026-06-01 16:47:46', '2026-05-08 14:54:48', '2026-06-01 16:47:46'),
(1565, NULL, 169, 'main d\'oeuvre transmission', NULL, 1.00, 0.00, 37.50, 45.00, 37.50, 100.0000, 0.00, 37.50, 37.50, 45.00, 20.0000, 7, NULL, 0, NULL, NULL, '2026-05-08 14:54:48', '2026-05-16 14:27:50'),
(1593, NULL, 180, 'Carlina', NULL, 1.00, 984.00, 1520.00, 1824.00, 536.00, 35.2632, 984.00, 536.00, 1520.00, 1824.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-12 13:51:58', '2026-05-12 14:13:46'),
(1567, NULL, 173, 'compteur', '180431 P2R', 1.00, 14.15, 23.58, 28.30, 9.43, 40.0000, 14.15, 9.43, 23.58, 28.30, 20.0000, 0, NULL, 1, '2026-05-10 14:26:33', '2026-06-01 16:47:48', '2026-05-08 20:50:41', '2026-06-03 16:06:54'),
(1601, NULL, 116, 'frais de port', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-12 13:55:43', '2026-05-12 13:55:43'),
(1569, NULL, 173, 'selle confort', NULL, 1.00, 12.37, 20.83, 25.00, 8.46, 40.6240, 12.37, 8.46, 20.83, 25.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-08 20:50:41', '2026-06-03 16:06:54'),
(1595, NULL, 180, 'rétroviseur', NULL, 1.00, 6.60, 11.17, 13.40, 4.57, 40.8955, 6.60, 4.57, 11.17, 13.40, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-12 13:51:58', '2026-05-12 14:13:46'),
(1591, NULL, 173, 'devoilage AV AR', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 8, NULL, 0, NULL, NULL, '2026-05-09 14:11:04', '2026-06-03 16:06:54'),
(1589, NULL, 173, 'pneu AR 700x35c ville', '509246', 1.00, 13.60, 15.33, 18.40, 1.73, 11.3043, 13.60, 1.73, 15.33, 18.40, 20.0000, 6, NULL, 0, NULL, NULL, '2026-05-09 14:11:04', '2026-06-03 16:06:54'),
(1590, NULL, 173, 'cable / gaine derailleur AR', NULL, 1.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 1.90, 14.77, 16.67, 20.00, 20.0000, 7, NULL, 0, NULL, NULL, '2026-05-09 14:11:04', '2026-06-03 16:06:54'),
(1575, NULL, 173, 'chaine', NULL, 1.00, 3.95, 23.33, 28.00, 19.38, 83.0714, 3.95, 19.38, 23.33, 28.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-08 20:50:41', '2026-06-03 16:06:54'),
(1576, NULL, 173, 'porte patins', '7068 P2R', 2.00, 2.90, 9.57, 11.48, 6.67, 69.6864, 5.80, 13.33, 19.13, 22.96, 20.0000, 3, NULL, 1, '2026-05-10 14:26:34', '2026-06-01 16:47:49', '2026-05-08 20:50:41', '2026-06-03 16:06:54'),
(1577, NULL, 173, 'réglage frein (AV/AR)', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-05-08 20:50:41', '2026-06-03 16:06:54'),
(1578, NULL, 173, 'montage chaine', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-05-08 20:50:41', '2026-06-03 16:06:54'),
(1584, NULL, 172, 'montage', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-08 20:54:12', '2026-05-20 14:05:53'),
(1583, NULL, 172, 'chambre a air', '200262 P2R', 1.00, 5.05, 8.25, 9.90, 3.20, 38.7879, 5.05, 3.20, 8.25, 9.90, 20.0000, 1, NULL, 1, '2026-05-10 14:26:37', '2026-06-01 16:47:51', '2026-05-08 20:54:12', '2026-06-01 16:47:51'),
(1585, NULL, 160, 'eclairage par dynamo AR', '5672 P2R', 1.00, 3.30, 6.88, 8.25, 3.58, 52.0000, 3.30, 3.58, 6.88, 8.25, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-08 21:25:52', '2026-06-15 07:30:03'),
(1586, NULL, 160, 'eclairage par dynamo AV 100lumen', '210100 P2R', 1.00, 11.09, 18.48, 22.18, 7.39, 40.0000, 11.09, 7.39, 18.48, 22.18, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-08 21:25:52', '2026-06-15 07:30:03'),
(1587, NULL, 160, 'roue AV dynamo occasion', NULL, 1.00, 0.00, 41.67, 50.00, 41.67, 100.0000, 0.00, 41.67, 41.67, 50.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-08 21:25:52', '2026-06-15 07:30:03'),
(1588, NULL, 160, 'montage + cablage', NULL, 1.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-05-08 21:25:52', '2026-06-15 07:30:03'),
(1600, NULL, 116, 'main d\'oeuvre', NULL, 1.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 16.67, 16.67, 20.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-12 13:55:43', '2026-05-12 13:55:43'),
(1599, NULL, 116, 'contrôleur', NULL, 1.00, 89.73, 90.70, 108.84, 0.97, 1.0695, 89.73, 0.97, 90.70, 108.84, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-12 13:55:43', '2026-05-12 13:55:43'),
(1602, NULL, 177, 'dévoilage AV/AR', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-13 11:34:20', '2026-05-21 14:48:49'),
(1673, NULL, 204, 'chaine 10V shimano renforcée', '492001', 1.00, 29.00, 43.33, 51.99, 14.33, 33.0641, 29.00, 14.33, 43.33, 51.99, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-22 16:23:36', '2026-05-24 06:25:39'),
(1671, NULL, 199, 'rétroviseur neomouv', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-21 12:40:33', '2026-06-04 08:47:15'),
(1669, NULL, 171, 'plaquette', NULL, 1.00, 2.90, 10.83, 13.00, 7.93, 73.2308, 2.90, 7.93, 10.83, 13.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-20 14:59:05', '2026-05-20 14:59:05'),
(1670, NULL, 199, 'Elaia 2 ng', NULL, 1.00, 1013.76, 1991.67, 2390.00, 977.91, 49.0999, 1013.76, 977.91, 1991.67, 2390.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-21 12:40:33', '2026-06-04 08:47:15'),
(1667, NULL, 188, 'rayon', '518095', 1.00, 0.98, 1.67, 2.00, 0.69, 41.2000, 0.98, 0.69, 1.67, 2.00, 20.0000, 2, NULL, 1, NULL, NULL, '2026-05-20 14:54:50', '2026-06-02 07:16:28'),
(1666, NULL, 198, 'retroviseur', '495058', 1.00, 8.50, 14.32, 17.18, 5.82, 40.6286, 8.50, 5.82, 14.32, 17.18, 20.0000, 0, NULL, 1, NULL, NULL, '2026-05-20 13:12:48', '2026-05-20 13:13:40'),
(1665, NULL, 183, 'montage', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-20 12:16:44', '2026-05-20 14:58:23'),
(1612, NULL, 181, 'main d\'œuvre redressement du plateau', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-13 11:38:05', '2026-05-16 12:31:54'),
(1613, NULL, 181, 'gaine / câble frein AR', NULL, 1.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 1.90, 14.77, 16.67, 20.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-13 11:38:05', '2026-05-16 12:31:54'),
(1615, NULL, 181, 'lubrification chaine', NULL, 1.00, 0.00, 8.33, 10.00, 8.33, 100.0000, 0.00, 8.33, 8.33, 10.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-13 14:07:43', '2026-05-16 12:31:54'),
(1616, NULL, 183, 'pneu 47x584 AV AR', '155470 P2R', 2.00, 19.50, 29.17, 35.00, 9.67, 33.1429, 39.00, 19.33, 58.33, 70.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-13 15:35:19', '2026-05-20 14:58:23'),
(1617, NULL, 183, 'chambre à air shradder', '143613 P2R', 1.00, 2.25, 7.42, 8.90, 5.17, 69.6629, 2.25, 5.17, 7.42, 8.90, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-13 15:35:19', '2026-05-20 14:58:23'),
(1618, NULL, 175, 'antivol', '500155', 1.00, 21.45, 35.75, 42.90, 14.30, 40.0000, 21.45, 14.30, 35.75, 42.90, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-14 13:28:37', '2026-05-14 13:28:45'),
(1619, NULL, 186, 'NCM Paris', NULL, 1.00, 535.00, 757.50, 909.00, 222.50, 29.3729, 535.00, 222.50, 757.50, 909.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-15 16:41:24', '2026-05-16 12:09:03'),
(1620, NULL, 186, 'antivol', '500155', 1.00, 21.45, 35.75, 42.90, 14.30, 40.0000, 21.45, 14.30, 35.75, 42.90, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-15 16:41:24', '2026-05-16 12:09:03'),
(1621, NULL, 186, 'panier', '527602', 1.00, 11.95, 20.83, 25.00, 8.88, 42.6400, 11.95, 8.88, 20.83, 25.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-15 16:41:24', '2026-05-16 12:09:03'),
(1622, NULL, 186, 'Selle Royal Drifter', '504487', 1.00, 29.90, 43.33, 52.00, 13.43, 31.0000, 29.90, 13.43, 43.33, 52.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-05-15 16:41:24', '2026-05-16 12:09:03'),
(1623, NULL, 153, 'plaquettes', NULL, 1.00, 2.39, 8.75, 10.50, 6.36, 72.6857, 2.39, 6.36, 8.75, 10.50, 20.0000, 7, NULL, 0, NULL, NULL, '2026-05-16 12:36:57', '2026-05-20 08:06:04'),
(1624, NULL, 190, 'chambre a air', '477599', 1.00, 3.40, 5.92, 7.10, 2.52, 42.5352, 3.40, 2.52, 5.92, 7.10, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-16 13:45:53', '2026-05-19 10:51:46'),
(1625, NULL, 190, 'montage', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-16 13:45:53', '2026-05-19 10:51:46'),
(1626, NULL, 184, 'reglages frein AV AR', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-16 15:12:20', '2026-06-10 15:17:58'),
(1627, NULL, 184, 'devoilage AV', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-16 15:12:20', '2026-06-10 15:17:58'),
(1628, NULL, 184, 'reglage derailleur AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-16 15:12:20', '2026-06-10 15:17:58'),
(1629, NULL, 184, 'graissage transmission', NULL, 1.00, 0.00, 8.33, 10.00, 8.33, 100.0000, 0.00, 8.33, 8.33, 10.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-05-16 15:12:20', '2026-06-10 15:17:58'),
(1630, NULL, 178, 'pneu 26x 2', '531197', 1.00, 15.95, 22.49, 26.99, 6.54, 29.0848, 15.95, 6.54, 22.49, 26.99, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-18 14:10:37', '2026-05-19 14:16:14'),
(1631, NULL, 178, 'chambre a air', '476517', 1.00, 2.50, 6.66, 7.99, 4.16, 62.4531, 2.50, 4.16, 6.66, 7.99, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-18 14:10:37', '2026-05-19 14:16:14'),
(1632, NULL, 192, 'plaquettes', NULL, 2.00, 0.00, 8.75, 10.50, 8.75, 100.0000, 0.00, 17.50, 17.50, 21.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-19 07:39:41', '2026-05-29 21:01:53'),
(1633, NULL, 192, 'reglage freins', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-19 07:39:41', '2026-05-29 21:01:53'),
(1639, NULL, 176, 'main d\'oeuvre', NULL, 1.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-19 10:48:46', '2026-06-16 13:31:58'),
(1638, NULL, 176, 'plateau AV', '170140 P2R', 1.00, 39.39, 65.67, 78.80, 26.28, 40.0152, 39.39, 26.28, 65.67, 78.80, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-19 10:48:46', '2026-06-16 13:31:58'),
(1637, NULL, 176, 'plateau 33', '137879 P2R', 1.00, 15.29, 27.08, 32.50, 11.79, 43.5446, 15.29, 11.79, 27.08, 32.50, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-19 10:48:46', '2026-06-16 13:31:58'),
(1640, NULL, 193, 'cable et gaine de frein AR', NULL, 1.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 16.67, 16.67, 20.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-19 13:26:57', '2026-05-19 13:27:02'),
(1641, NULL, 193, 'reglage drailleur', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-19 13:26:57', '2026-05-19 13:27:02'),
(1644, NULL, 185, 'Main d\'oeuvre fixation de cible compteur', NULL, 1.00, 0.00, 8.33, 10.00, 8.33, 100.0000, 0.00, 8.33, 8.33, 10.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-19 13:31:54', '2026-05-20 14:27:21'),
(1645, NULL, 194, 'Elaia 2 NG', NULL, 1.00, 1350.00, 1991.67, 2390.00, 641.67, 32.2176, 1350.00, 641.67, 1991.67, 2390.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-19 15:23:24', '2026-05-19 15:23:24'),
(1646, NULL, 194, 'antivol', 'D4 500 L', 1.00, 18.74, 0.00, 0.00, -18.74, 0.0000, 18.74, -18.74, 0.00, 0.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-19 15:23:24', '2026-05-19 15:23:24'),
(1647, NULL, 194, 'retroviseur', 'neomouv', 1.00, 10.80, 0.00, 0.00, -10.80, 0.0000, 10.80, -10.80, 0.00, 0.00, 20.0000, 2, NULL, 1, NULL, NULL, '2026-05-19 15:23:24', '2026-05-19 15:23:24'),
(1648, NULL, 194, 'panier', '497167', 1.00, 8.95, 0.00, 0.00, -8.95, 0.0000, 8.95, -8.95, 0.00, 0.00, 20.0000, 3, NULL, 1, NULL, NULL, '2026-05-19 15:23:24', '2026-05-19 15:23:24'),
(1649, NULL, 187, 'chaine 8V + installation', NULL, 1.00, 3.94, 29.17, 35.00, 25.23, 86.4914, 3.94, 25.23, 29.17, 35.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-20 06:18:39', '2026-05-30 14:01:30'),
(1650, NULL, 187, 'réglage etrier AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-20 06:18:39', '2026-05-30 14:01:30'),
(1651, NULL, 187, 'poignée', '528340', 1.00, 5.10, 8.50, 10.20, 3.40, 40.0000, 5.10, 3.40, 8.50, 10.20, 20.0000, 2, NULL, 1, '2026-06-10 10:08:28', '2026-06-10 10:08:29', '2026-05-20 06:18:39', '2026-06-10 10:08:29'),
(1652, NULL, 187, 'pédales', '511066', 1.00, 3.30, 5.50, 6.60, 2.20, 40.0000, 3.30, 2.20, 5.50, 6.60, 20.0000, 3, NULL, 1, '2026-06-10 10:08:30', '2026-06-10 10:08:31', '2026-05-20 06:18:39', '2026-06-10 10:08:31'),
(1653, NULL, 187, 'sonnettes', '468266', 1.00, 0.79, 3.33, 4.00, 2.54, 76.3000, 0.79, 2.54, 3.33, 4.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-05-20 06:18:39', '2026-05-30 14:01:30'),
(1654, NULL, 188, 'dévoilage (AV/AR)', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-20 06:41:56', '2026-06-02 07:16:28'),
(1656, NULL, 188, 'poignée', '495667', 1.00, 9.75, 16.25, 19.50, 6.50, 40.0000, 9.75, 6.50, 16.25, 19.50, 20.0000, 1, NULL, 1, NULL, NULL, '2026-05-20 06:41:56', '2026-06-02 07:16:28'),
(1657, NULL, 182, 'cable / gaine (AV/AR)', NULL, 2.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 33.33, 33.33, 40.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-20 06:43:04', '2026-06-13 12:48:43'),
(1658, NULL, 182, 'dévoilage AV', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-20 06:43:04', '2026-06-13 12:48:43'),
(1832, NULL, 155, 'capteur pédalage + pose', NULL, 1.00, 0.00, 37.50, 45.00, 37.50, 100.0000, 0.00, 37.50, 37.50, 45.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-04 09:32:44', '2026-06-06 09:54:31'),
(1664, NULL, 189, 'rayon', '489548', 1.00, 0.00, 1.67, 2.00, 1.67, 100.0000, 0.00, 1.67, 1.67, 2.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-20 08:32:18', '2026-05-20 09:02:20'),
(1663, NULL, 189, 'devoilage', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-20 08:32:18', '2026-05-20 09:02:20'),
(1674, NULL, 204, 'montage', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-22 16:23:36', '2026-05-24 06:25:39'),
(1691, NULL, 59, 'rayons', NULL, 3.00, 0.00, 1.67, 2.00, 1.67, 100.0000, 0.00, 5.00, 5.00, 6.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-26 10:29:25', '2026-05-26 10:29:39'),
(1680, NULL, 206, 'chambre à air AR', '183128  P2R', 1.00, 3.30, 6.88, 8.25, 3.58, 52.0000, 3.30, 3.58, 6.88, 8.25, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-25 12:48:39', '2026-05-29 09:15:38'),
(1677, NULL, 205, 'chambre a air', '466930', 1.00, 2.95, 5.00, 6.00, 2.05, 41.0000, 2.95, 2.05, 5.00, 6.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-23 09:59:36', '2026-06-15 07:21:27'),
(1678, NULL, 205, 'montage', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-23 09:59:36', '2026-06-15 07:21:27'),
(1679, NULL, 205, 'demonte pneu', NULL, 1.00, 0.00, 0.83, 1.00, 0.83, 100.0000, 0.00, 0.83, 0.83, 1.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-23 09:59:36', '2026-06-15 07:21:27'),
(1682, NULL, 206, 'main d\'oeuvre', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-25 12:48:39', '2026-05-29 09:15:38'),
(1683, NULL, 207, 'dévolage AR', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-25 12:55:11', '2026-05-29 09:14:10'),
(1684, NULL, 207, 'reglage frein AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-25 12:55:11', '2026-05-29 09:14:10'),
(1690, NULL, 209, 'cable + MO', NULL, 1.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 16.67, 16.67, 20.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-26 08:55:14', '2026-05-29 20:55:10'),
(2042, NULL, 268, 'dévoilage (AV/AR)', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-19 07:23:41', '2026-06-25 08:09:06'),
(1688, NULL, 201, 'réglage frein (AV/AR)', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-25 13:30:40', '2026-06-18 16:01:48'),
(1692, NULL, 59, 'devoilage', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-05-26 10:29:25', '2026-05-26 10:29:39'),
(1693, NULL, 210, 'Neomouv Carlina HY XR', NULL, 1.00, 810.00, 1250.00, 1500.00, 440.00, 35.2000, 810.00, 440.00, 1250.00, 1500.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-26 12:59:44', '2026-06-06 08:44:46'),
(1695, NULL, 129, 'reglage de frein + petit cable', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-26 15:43:17', '2026-05-29 04:28:02'),
(1696, NULL, 213, 'reglage derailleur', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-28 15:24:35', '2026-05-29 08:04:34'),
(1702, NULL, 202, 'dévoilage AV/AR', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-29 07:39:15', '2026-06-18 16:01:26'),
(1701, NULL, 202, 'cable frein AR', NULL, 1.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 1.90, 14.77, 16.67, 20.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-29 07:39:15', '2026-06-18 16:01:26'),
(1999, NULL, 255, 'chambre à air', '508710', 1.00, 5.70, 10.75, 12.90, 5.05, 46.9767, 5.70, 5.05, 10.75, 12.90, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-15 12:24:08', '2026-06-16 08:57:16'),
(1711, NULL, 208, 'pneus AV AR 700x28c', '19352 P2R', 2.00, 9.30, 15.50, 18.60, 6.20, 40.0000, 18.60, 12.40, 31.00, 37.20, 20.0000, 3, NULL, 1, '2026-06-10 10:08:49', '2026-06-10 10:08:50', '2026-05-29 09:02:15', '2026-06-12 14:44:55'),
(1710, NULL, 208, 'gaine cable freins AV AR', NULL, 2.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 33.33, 33.33, 40.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-29 09:02:15', '2026-06-12 14:44:55'),
(1709, NULL, 208, 'reglage derailleur et plateau', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-29 09:02:15', '2026-06-12 14:44:55'),
(1708, NULL, 208, 'devoilage AV AR', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-29 09:02:15', '2026-06-12 14:44:55'),
(1712, NULL, 208, 'nettoyage transmission', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-05-29 09:02:15', '2026-06-12 14:44:55'),
(1713, NULL, 201, 'cable de frein AR', NULL, 1.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 1.90, 14.77, 16.67, 20.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-29 09:09:06', '2026-06-18 16:01:48'),
(1714, NULL, 201, 'plaquette AR + installation', NULL, 1.00, 3.90, 9.58, 11.50, 5.68, 59.3043, 3.90, 5.68, 9.58, 11.50, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-29 09:09:06', '2026-06-18 16:01:48'),
(1715, NULL, 212, 'réglage frein', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-29 09:22:59', '2026-06-06 07:26:30'),
(1716, NULL, 212, 'devoilage (AV/AR)', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-29 09:22:59', '2026-06-06 07:26:30'),
(1717, NULL, 212, 'réglage vitesses', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-29 09:22:59', '2026-06-06 07:26:30'),
(1718, NULL, 202, 'cable de derailleur AV', NULL, 1.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 16.67, 16.67, 20.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-29 09:31:12', '2026-06-18 16:01:26'),
(1719, NULL, 217, 'gaine / cable derailleur AR', NULL, 1.00, 3.90, 16.67, 20.00, 12.77, 76.6000, 3.90, 12.77, 16.67, 20.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-29 14:39:57', '2026-06-02 14:55:38'),
(1720, NULL, 217, 'reglage frein AV/AR', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-29 14:39:57', '2026-06-02 14:55:38'),
(1721, NULL, 217, 'roue libre 14-28', '175431 P2R', 1.00, 7.00, 15.03, 18.03, 8.03, 53.4110, 7.00, 8.03, 15.03, 18.03, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-29 14:39:57', '2026-06-02 14:55:38'),
(1722, NULL, 217, 'chaine 6 V', NULL, 1.00, 3.94, 20.75, 24.90, 16.81, 81.0120, 3.94, 16.81, 20.75, 24.90, 20.0000, 3, NULL, 0, NULL, NULL, '2026-05-29 14:39:57', '2026-06-02 14:55:38'),
(1723, NULL, 217, 'pedalier', '20501 P2R', 1.00, 15.00, 25.65, 30.78, 10.65, 41.5205, 15.00, 10.65, 25.65, 30.78, 20.0000, 4, NULL, 0, NULL, NULL, '2026-05-29 14:39:57', '2026-06-02 14:55:38'),
(1724, NULL, 217, 'Main d\'oeuvre transmission', NULL, 1.00, 0.00, 33.33, 40.00, 33.33, 100.0000, 0.00, 33.33, 33.33, 40.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-05-29 14:39:57', '2026-06-02 14:55:38'),
(1725, NULL, 223, 'Enara 2 TK', NULL, 1.00, 700.00, 1458.33, 1750.00, 758.33, 52.0000, 700.00, 758.33, 1458.33, 1750.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-29 15:27:29', '2026-05-30 15:01:23'),
(1726, NULL, 223, 'casque MAVIC SpeedCity', 'Speedcity', 1.00, 86.00, 90.83, 109.00, 4.83, 5.3211, 86.00, 4.83, 90.83, 109.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-29 15:27:29', '2026-05-30 15:01:23'),
(1727, NULL, 223, 'sacoches', '535545', 1.00, 49.15, 76.67, 92.00, 27.52, 35.8913, 49.15, 27.52, 76.67, 92.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-29 15:27:29', '2026-05-30 15:01:23'),
(1728, NULL, 223, 'antivol Decathlon 500 L', NULL, 1.00, 18.74, 24.99, 29.99, 6.25, 25.0150, 18.74, 6.25, 24.99, 29.99, 20.0000, 3, NULL, 0, NULL, NULL, '2026-05-29 15:27:29', '2026-05-30 15:01:23'),
(1729, NULL, 221, 'reglage derailleur', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-29 15:40:25', '2026-05-30 13:15:55'),
(1730, NULL, 222, 'devoilage AV AR', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-29 15:45:04', '2026-06-05 08:45:24'),
(1731, NULL, 222, 'rayon cassé', NULL, 1.00, 0.00, 1.67, 2.00, 1.67, 100.0000, 0.00, 1.67, 1.67, 2.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-29 15:45:04', '2026-06-05 08:45:24'),
(1732, NULL, 222, 'MO remplacement rayon', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-29 15:45:04', '2026-06-05 08:45:24'),
(1733, NULL, 222, 'reglage freins AV AR', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-05-29 15:45:04', '2026-06-05 08:45:24'),
(1734, NULL, 222, 'cable gaine derailleur AR (inutile je pense a voir avec jo)', NULL, 1.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 16.67, 16.67, 20.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-05-29 15:45:04', '2026-06-05 08:45:24'),
(1735, NULL, 224, 'plateau 50 dents', '491916', 1.00, 45.92, 76.58, 91.90, 30.66, 40.0392, 45.92, 30.66, 76.58, 91.90, 20.0000, 0, NULL, 1, '2026-06-03 07:10:34', '2026-06-03 07:10:53', '2026-05-29 20:40:08', '2026-06-06 14:08:35'),
(1736, NULL, 224, 'plateau 34 dents', '491920', 1.00, 24.62, 41.08, 49.30, 16.46, 40.0730, 24.62, 16.46, 41.08, 49.30, 20.0000, 1, NULL, 1, '2026-06-03 07:10:36', '2026-06-03 07:10:55', '2026-05-29 20:40:08', '2026-06-06 14:08:35'),
(1737, NULL, 224, 'cassette 11 V', '500804', 1.00, 43.00, 54.16, 64.99, 11.16, 20.6032, 43.00, 11.16, 54.16, 64.99, 20.0000, 2, NULL, 1, '2026-06-03 07:10:39', '2026-06-03 07:10:56', '2026-05-29 20:40:08', '2026-06-06 14:08:35'),
(1738, NULL, 224, 'intravis (montage plateau)', '496480', 1.00, 8.12, 13.58, 16.30, 5.46, 40.2209, 8.12, 5.46, 13.58, 16.30, 20.0000, 3, NULL, 1, NULL, NULL, '2026-05-29 20:40:08', '2026-06-06 14:08:35'),
(1739, NULL, 224, 'patins (dispo à partir du 13 juin)', '524733', 2.00, 12.05, 14.16, 16.99, 2.11, 14.8911, 24.10, 4.22, 28.32, 33.98, 20.0000, 4, NULL, 1, '2026-06-03 07:11:00', '2026-06-03 07:11:04', '2026-05-29 20:40:08', '2026-06-06 14:08:35'),
(1740, NULL, 224, 'main d\'oeuvre', NULL, 1.00, 0.00, 37.50, 45.00, 37.50, 100.0000, 0.00, 37.50, 37.50, 45.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-05-29 20:40:08', '2026-06-06 14:08:35'),
(1742, NULL, 216, 'livraison', NULL, 1.00, 0.00, 6.67, 8.00, 6.67, 100.0000, 0.00, 6.67, 6.67, 8.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-29 20:43:00', '2026-05-29 20:43:00'),
(1758, NULL, 211, 'patins AR', NULL, 1.00, 1.90, 4.58, 5.50, 2.68, 58.5455, 1.90, 2.68, 4.58, 5.50, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-29 20:54:33', '2026-06-16 08:59:51'),
(1759, NULL, 211, 'dévoilage AV/AR', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-29 20:54:33', '2026-06-16 08:59:51'),
(1760, NULL, 211, 'réglage frein AV/AR', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-05-29 20:54:33', '2026-06-16 08:59:51'),
(1761, NULL, 211, 'chaine', NULL, 1.00, 3.94, 20.75, 24.90, 16.81, 81.0120, 3.94, 16.81, 20.75, 24.90, 20.0000, 4, NULL, 0, NULL, NULL, '2026-05-29 20:54:33', '2026-06-16 08:59:51'),
(1762, NULL, 211, 'roue libre 6V', '5361', 1.00, 7.30, 10.95, 13.14, 3.65, 33.3333, 7.30, 3.65, 10.95, 13.14, 20.0000, 5, NULL, 1, '2026-06-15 07:01:32', '2026-06-15 07:01:40', '2026-05-29 20:54:33', '2026-06-16 08:59:51'),
(1763, NULL, 211, 'pedalier', '482045', 1.00, 13.65, 22.75, 27.30, 9.10, 40.0000, 13.65, 9.10, 22.75, 27.30, 20.0000, 6, NULL, 1, '2026-06-15 07:01:34', '2026-06-15 07:01:42', '2026-05-29 20:54:33', '2026-06-16 08:59:51'),
(1757, NULL, 211, 'gaine/cable frein AR', NULL, 1.00, 3.90, 16.67, 20.00, 12.77, 76.6000, 3.90, 12.77, 16.67, 20.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-29 20:54:33', '2026-06-16 08:59:51'),
(1764, NULL, 72, 'cable de derailleur', '495659', 1.00, 2.10, 0.00, 0.00, -2.10, 0.0000, 2.10, -2.10, 0.00, 0.00, 20.0000, 7, NULL, 0, NULL, NULL, '2026-05-29 21:05:10', '2026-05-29 21:05:31'),
(1765, NULL, 72, 'livraison Bréhat', NULL, 1.00, 15.00, 15.00, 18.00, 0.00, 0.0000, 15.00, 0.00, 15.00, 18.00, 20.0000, 8, NULL, 0, NULL, NULL, '2026-05-29 21:05:10', '2026-05-29 21:05:31'),
(1766, NULL, 225, 'dévoilage AR', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-30 06:22:46', '2026-06-17 13:55:17'),
(1767, NULL, 225, 'reglage frein AV/AR', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-30 06:22:46', '2026-06-17 13:55:17'),
(1768, NULL, 225, 'reglage derailleur AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-30 06:22:46', '2026-06-17 13:55:17'),
(1769, NULL, 226, 'cable / gaine frein AR', NULL, 1.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 1.90, 14.77, 16.67, 20.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-05-30 06:24:02', '2026-06-17 13:55:07'),
(1770, NULL, 226, 'dévoilage AV', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-30 06:24:02', '2026-06-17 13:55:07'),
(1771, NULL, 226, 'réglage frein AV', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-30 06:24:02', '2026-06-17 13:55:07'),
(1772, NULL, 227, 'Pneus 40-584', '504837', 2.00, 10.40, 13.33, 15.99, 2.93, 21.9512, 20.80, 5.85, 26.65, 31.98, 20.0000, 0, NULL, 1, NULL, NULL, '2026-05-30 06:26:20', '2026-06-17 13:54:56'),
(1773, NULL, 227, 'chambre à air', '540782', 2.00, 3.30, 5.75, 6.90, 2.45, 42.6087, 6.60, 4.90, 11.50, 13.80, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-30 06:26:20', '2026-06-17 13:54:56'),
(1774, NULL, 227, 'fond de jante', '517592', 2.00, 4.10, 5.17, 6.20, 1.07, 20.6452, 8.20, 2.13, 10.33, 12.40, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-30 06:26:20', '2026-06-17 13:54:56'),
(1775, NULL, 227, 'gaine/cable frein AR', NULL, 1.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 1.90, 14.77, 16.67, 20.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-05-30 06:26:20', '2026-06-17 13:54:56'),
(1776, NULL, 227, 'dévoilage AV/AR', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-05-30 06:26:20', '2026-06-17 13:54:56'),
(1777, NULL, 227, 'graissage chaine', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-05-30 06:26:20', '2026-06-17 13:54:56'),
(1778, NULL, 228, 'derailleur + installation', '477683', 1.00, 9.27, 20.83, 25.00, 11.56, 55.5040, 9.27, 11.56, 20.83, 25.00, 20.0000, 0, NULL, 1, '2026-06-03 07:11:07', '2026-06-10 10:09:00', '2026-05-30 06:35:17', '2026-06-17 13:54:46'),
(1779, NULL, 228, 'chaine 7V + installation', NULL, 1.00, 3.94, 29.17, 35.00, 25.23, 86.4914, 3.94, 25.23, 29.17, 35.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-05-30 06:35:17', '2026-06-17 13:54:46'),
(1780, NULL, 228, 'dévoilage (AV/AR)', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-05-30 06:35:17', '2026-06-17 13:54:46'),
(1781, NULL, 228, 'cable gaine frein (AV/AR)', NULL, 2.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 3.80, 29.53, 33.33, 40.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-05-30 06:35:17', '2026-06-17 13:54:46'),
(1782, NULL, 228, 'pneus  700X35(AV/AR)', NULL, 2.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-05-30 06:35:17', '2026-06-17 13:54:46'),
(1783, NULL, 227, 'main d\'oeuvre pneus/chambre/fond de jante', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-05-30 06:46:56', '2026-06-17 13:54:56'),
(1787, NULL, 218, 'plateau 36dents ultegra', 'Y1W836000 shimano', 1.00, 7.59, 16.66, 19.99, 9.07, 54.4372, 7.59, 9.07, 16.66, 19.99, 20.0000, 1, NULL, 1, '2026-06-10 10:09:04', '2026-06-10 10:09:10', '2026-05-30 07:01:51', '2026-06-10 14:52:21'),
(1786, NULL, 218, 'plateau 52dents ultegra', 'Y1W898030 shimano', 1.00, 52.73, 116.66, 139.99, 63.93, 54.7996, 52.73, 63.93, 116.66, 139.99, 20.0000, 0, NULL, 1, '2026-06-10 10:09:05', '2026-06-10 10:09:11', '2026-05-30 07:01:51', '2026-06-10 14:52:21'),
(1789, NULL, 218, 'chaine 11V ultegra', 'ICNE800011138Q shimano', 1.00, 25.50, 46.66, 55.99, 21.16, 45.3474, 25.50, 21.16, 46.66, 55.99, 20.0000, 2, NULL, 1, '2026-06-10 10:09:06', '2026-06-10 10:09:12', '2026-05-30 14:09:22', '2026-06-10 14:52:21'),
(1790, NULL, 218, 'cassette 11V ultegra 11-30', 'ICSR800011130 shimano', 1.00, 38.70, 74.99, 89.99, 36.29, 48.3943, 38.70, 36.29, 74.99, 89.99, 20.0000, 3, NULL, 1, '2026-06-10 10:09:07', '2026-06-10 10:09:13', '2026-05-30 14:09:22', '2026-06-10 14:52:21'),
(1792, NULL, 223, 'pince pantalon', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-05-30 14:19:55', '2026-05-30 15:01:23'),
(1793, NULL, 223, 'tendeurs de porte bagages', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-05-30 15:01:23', '2026-05-30 15:01:23'),
(1794, NULL, 223, 'porte gourde sur tube de selle (collier)', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-05-30 15:01:23', '2026-05-30 15:01:23'),
(1795, NULL, 218, 'plaquettes AV AR L05A RF', 'EBPL05ARFA shimano', 2.00, 9.57, 19.99, 23.99, 10.42, 52.1301, 19.14, 20.84, 39.98, 47.98, 20.0000, 4, NULL, 1, '2026-06-10 10:09:09', '2026-06-10 10:09:14', '2026-05-31 05:32:06', '2026-06-10 14:52:21'),
(1798, NULL, 218, 'main d\'oeuvre', NULL, 1.00, 0.00, 37.50, 45.00, 37.50, 100.0000, 0.00, 37.50, 37.50, 45.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-06-01 14:46:25', '2026-06-10 14:52:21'),
(1825, NULL, 229, 'devoilage AV', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-02 16:53:03', '2026-06-20 09:59:41'),
(1824, NULL, 229, 'chaine 7v + montage', NULL, 1.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-02 16:53:03', '2026-06-20 09:59:41'),
(1808, NULL, 176, 'maillon rapide pour grande chaine', '25419', 1.00, 2.09, 5.22, 6.27, 3.13, 60.0000, 2.09, 3.13, 5.22, 6.27, 20.0000, 4, NULL, 0, NULL, NULL, '2026-06-02 06:49:21', '2026-06-16 13:31:58'),
(1807, NULL, 176, 'chaine', '206965', 1.00, 9.40, 15.67, 18.80, 6.27, 40.0000, 9.40, 6.27, 15.67, 18.80, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-02 06:49:21', '2026-06-16 13:31:58'),
(1815, NULL, 188, 'réglage derailleur', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-06-02 07:16:28', '2026-06-02 07:16:28'),
(1814, NULL, 188, 'pate de derailleur', '484433', 1.00, 8.20, 15.03, 18.04, 6.83, 45.4545, 8.20, 6.83, 15.03, 18.04, 20.0000, 3, NULL, 1, NULL, NULL, '2026-06-02 07:16:28', '2026-06-02 07:16:28'),
(1817, NULL, 184, 'pneu 26x1.75 delitire', NULL, 1.00, 6.10, 11.18, 13.42, 5.08, 45.4545, 6.10, 5.08, 11.18, 13.42, 20.0000, 4, NULL, 0, NULL, NULL, '2026-06-02 08:06:31', '2026-06-10 15:17:58'),
(1826, NULL, 229, 'pneu (AV/AR) 700x35c', '223929 P2R', 2.00, 7.31, 15.35, 18.42, 8.04, 52.3779, 14.62, 16.08, 30.70, 36.84, 20.0000, 2, NULL, 1, '2026-06-10 10:09:17', '2026-06-10 10:09:19', '2026-06-02 16:53:03', '2026-06-20 09:59:41'),
(1827, NULL, 224, 'chaine', '511481', 1.00, 22.90, 22.92, 27.50, 0.02, 0.0727, 22.90, 0.02, 22.92, 27.50, 20.0000, 6, NULL, 1, '2026-06-10 10:09:22', '2026-06-10 10:09:30', '2026-06-03 09:44:27', '2026-06-10 10:09:30'),
(1828, NULL, 233, 'cassette 7v', '175745 P2R', 1.00, 9.11, 15.75, 18.90, 6.64, 42.1587, 9.11, 6.64, 15.75, 18.90, 20.0000, 0, NULL, 1, '2026-06-10 10:09:43', '2026-06-10 10:09:45', '2026-06-03 13:24:20', '2026-06-18 14:12:37'),
(1829, NULL, 233, 'conversion', NULL, 1.00, 0.00, 75.00, 90.00, 75.00, 100.0000, 0.00, 75.00, 75.00, 90.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-03 13:24:20', '2026-06-18 14:12:37'),
(1833, NULL, 235, 'NCM Milano 48', NULL, 1.00, 0.00, 658.33, 790.00, 658.33, 100.0000, 0.00, 658.33, 658.33, 790.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-04 16:08:39', '2026-06-04 16:08:39'),
(1834, NULL, 236, 'reglage freins (AV/AR)', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-05 08:00:32', '2026-06-05 09:28:39'),
(1835, NULL, 236, 'reglage derailleur (AR)', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-05 08:00:32', '2026-06-05 09:28:39'),
(1836, NULL, 236, 'dévoilage (AR)', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-05 08:00:32', '2026-06-05 09:28:39'),
(1837, NULL, 236, 'mise en place pneu (AV/AR)', NULL, 1.00, 0.00, 8.33, 10.00, 8.33, 100.0000, 0.00, 8.33, 8.33, 10.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-05 08:00:32', '2026-06-05 09:28:39'),
(1838, NULL, 236, 'plateau (45mm plutôt vers petit pignon) 42T', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-06-05 08:00:32', '2026-06-05 09:28:39'),
(1945, NULL, 245, 'devoilage (AR)', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-11 09:29:25', '2026-06-16 21:06:52'),
(1943, NULL, 245, 'cassette 8v 11-37T', '502633', 1.00, 16.80, 22.49, 26.99, 5.69, 25.3057, 16.80, 5.69, 22.49, 26.99, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-11 09:29:25', '2026-06-16 21:06:52'),
(1944, NULL, 245, 'plateau x3 28-48T axe 122mm', '487054', 1.00, 13.00, 21.67, 26.00, 8.67, 40.0000, 13.00, 8.67, 21.67, 26.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-11 09:29:25', '2026-06-16 21:06:52'),
(1842, NULL, 231, 'devoilage (AV/AR)', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-05 08:26:25', '2026-06-11 15:00:32'),
(1844, NULL, 222, 'pneu', '510070P2R', 1.00, 6.00, 10.83, 13.00, 4.83, 44.6154, 6.00, 4.83, 10.83, 13.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-06-05 08:36:27', '2026-06-05 08:45:24'),
(1845, NULL, 222, 'montage pneu', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-06-05 08:36:27', '2026-06-05 08:45:24'),
(1846, NULL, 222, 'guidoline', 'Selle Italia SMART TAPE', 1.00, 10.75, 20.67, 24.80, 9.92, 47.9839, 10.75, 9.92, 20.67, 24.80, 20.0000, 7, NULL, 0, NULL, NULL, '2026-06-05 08:36:27', '2026-06-05 08:45:24'),
(1847, NULL, 222, 'nettoyage cintre et pose guidoline', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 8, NULL, 0, NULL, NULL, '2026-06-05 08:36:27', '2026-06-05 08:45:24'),
(1848, NULL, 184, 'main d\'oeuvre pneu', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-06-05 08:41:13', '2026-06-10 15:17:58'),
(1849, NULL, 229, 'main d\'oeuvre pneu', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-05 08:49:26', '2026-06-20 09:59:41'),
(1850, NULL, 237, 'frein hydraulique Clarks', NULL, 1.00, 50.40, 50.40, 60.48, 0.00, 0.0000, 50.40, 0.00, 50.40, 60.48, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-05 10:00:22', '2026-06-09 15:46:34'),
(1851, NULL, 237, 'bande anticrevaison', NULL, 1.00, 13.80, 23.00, 27.60, 9.20, 40.0000, 13.80, 9.20, 23.00, 27.60, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-05 10:00:22', '2026-06-09 15:46:34');
INSERT INTO `quote_lines` (`id`, `article_id`, `quote_id`, `title`, `reference`, `quantity`, `purchase_price_ht`, `sale_price_ht`, `sale_price_ttc`, `margin_amount_ht`, `margin_rate`, `line_purchase_ht`, `line_margin_ht`, `line_total_ht`, `line_total_ttc`, `tva_rate`, `position`, `estimated_time_minutes`, `needs_order`, `ordered_at`, `received_at`, `created_at`, `updated_at`) VALUES
(1925, NULL, 244, 'main d\'oeuvre', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-11 09:11:50', '2026-06-11 09:17:47'),
(1923, NULL, 250, 'chambre a air optimiz 24 x 1.5/2.00', NULL, 1.00, 1.64, 2.92, 3.50, 1.28, 43.7714, 1.64, 1.28, 2.92, 3.50, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-10 12:53:07', '2026-06-16 20:37:20'),
(1917, NULL, 176, 'reglage frein', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-06-10 08:01:43', '2026-06-16 13:31:58'),
(1861, NULL, 211, 'main d\'oeuvre transmission', NULL, 1.00, 0.00, 37.50, 45.00, 37.50, 100.0000, 0.00, 37.50, 37.50, 45.00, 20.0000, 7, NULL, 0, NULL, NULL, '2026-06-08 15:55:15', '2026-06-16 08:59:51'),
(1922, NULL, 230, 'nettoyage et remplacement liquide préventif', NULL, 1.00, 1.60, 29.17, 35.00, 27.57, 94.5143, 1.60, 27.57, 29.17, 35.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-10 11:32:21', '2026-06-10 11:32:21'),
(1876, NULL, 239, 'mono plateau 38d', 'https://www.decathlon.fr/p/pedalier-38-dents-monoplateau/202961/m8607693', 1.00, 29.25, 31.67, 38.00, 2.42, 7.6316, 29.25, 2.42, 31.67, 38.00, 20.0000, 5, NULL, 1, '2026-06-10 11:03:56', '2026-06-15 07:01:26', '2026-06-08 16:28:17', '2026-06-18 09:27:03'),
(1875, NULL, 239, 'cassette 11-36', '482655', 1.00, 24.90, 37.35, 44.82, 12.45, 33.3333, 24.90, 12.45, 37.35, 44.82, 20.0000, 4, NULL, 1, '2026-06-15 07:01:20', '2026-06-15 07:01:24', '2026-06-08 16:28:17', '2026-06-18 09:27:03'),
(1874, NULL, 239, 'chaine 10v', '492244', 1.00, 10.20, 15.30, 18.36, 5.10, 33.3333, 10.20, 5.10, 15.30, 18.36, 20.0000, 3, NULL, 1, '2026-06-15 07:01:21', '2026-06-15 07:01:28', '2026-06-08 16:28:17', '2026-06-18 09:27:03'),
(1873, NULL, 239, 'dévoilage (AV/AR)', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-08 16:28:17', '2026-06-18 09:27:03'),
(1872, NULL, 239, 'main d\'oeuvre freinage', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-08 16:28:17', '2026-06-18 09:27:03'),
(1871, NULL, 239, 'plaquettes', '495203', 2.00, 3.50, 8.75, 10.50, 5.25, 60.0000, 7.00, 10.50, 17.50, 21.00, 20.0000, 0, NULL, 1, '2026-06-15 07:01:22', '2026-06-15 07:01:30', '2026-06-08 16:28:17', '2026-06-18 09:27:03'),
(1919, NULL, 234, 'M-O pneumatique', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 10, NULL, 0, NULL, NULL, '2026-06-10 11:16:09', '2026-06-10 11:16:09'),
(1879, NULL, 239, 'main d\'oeuvre transmission', NULL, 1.00, 0.00, 37.50, 45.00, 37.50, 100.0000, 0.00, 37.50, 37.50, 45.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-06-08 16:28:17', '2026-06-18 09:27:03'),
(1880, NULL, 233, 'manette 7 et 3', '6986 P2R', 1.00, 9.33, 21.15, 25.38, 11.82, 55.8865, 9.33, 11.82, 21.15, 25.38, 20.0000, 2, NULL, 1, '2026-06-10 10:09:44', '2026-06-10 10:09:46', '2026-06-08 20:36:24', '2026-06-18 14:12:37'),
(1884, NULL, 241, 'reglage derailleur (AR)', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-09 07:35:13', '2026-06-09 08:38:33'),
(1883, NULL, 241, 'reglages freins (AV/AR)', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-09 07:35:13', '2026-06-09 08:38:33'),
(1885, NULL, 243, 'reglage derailleur', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-09 09:02:13', '2026-06-09 09:02:21'),
(1906, NULL, 234, 'gaine cable frein (AV/AR)', NULL, 2.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 3.80, 29.53, 33.33, 40.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-09 10:03:03', '2026-06-10 11:16:09'),
(1907, NULL, 234, 'devoilage (AV/AR)', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-09 10:03:03', '2026-06-10 11:16:09'),
(1908, NULL, 234, 'gaine cable derailleur (AV/AR)', NULL, 2.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 3.80, 29.53, 33.33, 40.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-06-09 10:03:03', '2026-06-10 11:16:09'),
(1909, NULL, 234, 'commande derailleur', '515188', 1.00, 4.90, 8.17, 9.80, 3.27, 40.0000, 4.90, 3.27, 8.17, 9.80, 20.0000, 5, NULL, 0, NULL, NULL, '2026-06-09 10:03:03', '2026-06-10 11:16:09'),
(1910, NULL, 234, 'chaine 6v', NULL, 1.00, 3.97, 23.33, 28.00, 19.36, 82.9857, 3.97, 19.36, 23.33, 28.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-06-09 10:03:03', '2026-06-10 11:16:09'),
(1911, NULL, 234, 'plateau x3 axe 142.5 24-42T', '495805', 1.00, 21.43, 23.32, 27.99, 1.89, 8.1243, 21.43, 1.89, 23.32, 27.99, 20.0000, 7, NULL, 0, NULL, NULL, '2026-06-09 10:03:03', '2026-06-10 11:16:09'),
(1912, NULL, 234, 'roue libre 6v 14-28T', '513608', 1.00, 7.00, 11.67, 14.00, 4.67, 40.0000, 7.00, 4.67, 11.67, 14.00, 20.0000, 8, NULL, 0, NULL, NULL, '2026-06-09 10:03:03', '2026-06-10 11:16:09'),
(1905, NULL, 234, 'chambre a air 26x1.95', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-09 10:03:03', '2026-06-10 11:16:09'),
(1904, NULL, 234, 'pneu (AV/AR) 26x1.95', '485032', 2.00, 7.45, 13.66, 16.39, 6.21, 45.4545, 14.90, 12.42, 27.32, 32.78, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-09 10:03:03', '2026-06-10 11:16:09'),
(1913, NULL, 234, 'M-O transmission', NULL, 1.00, 0.00, 37.50, 45.00, 37.50, 100.0000, 0.00, 37.50, 37.50, 45.00, 20.0000, 9, NULL, 0, NULL, NULL, '2026-06-09 10:03:03', '2026-06-10 11:16:09'),
(1915, NULL, 242, 'purge', NULL, 1.00, 0.00, 33.33, 40.00, 33.33, 100.0000, 0.00, 33.33, 33.33, 40.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-09 12:41:32', '2026-06-09 12:41:54'),
(1916, NULL, 244, 'chambre a air 26x1.95', '476517', 1.00, 3.40, 7.42, 8.90, 4.02, 54.1573, 3.40, 4.02, 7.42, 8.90, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-09 12:45:12', '2026-06-11 09:17:47'),
(1947, NULL, 245, 'pneu AV 29x2.0  622-50  VTC leger crampons', '492132', 1.00, 13.50, 15.22, 18.27, 1.72, 11.3300, 13.50, 1.72, 15.22, 18.27, 20.0000, 4, NULL, 0, NULL, NULL, '2026-06-11 09:29:25', '2026-06-16 21:06:52'),
(1981, NULL, 245, 'entretien moteur', NULL, 1.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 8, NULL, 0, NULL, NULL, '2026-06-12 07:57:35', '2026-06-16 21:06:52'),
(1949, NULL, 245, 'derailleur tourney 8v', '465050', 1.00, 16.50, 27.50, 33.00, 11.00, 40.0000, 16.50, 11.00, 27.50, 33.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-06-11 09:29:25', '2026-06-16 21:06:52'),
(1942, NULL, 245, 'chaine 8v', NULL, 1.00, 3.97, 15.00, 18.00, 11.03, 73.5333, 3.97, 11.03, 15.00, 18.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-11 09:29:25', '2026-06-16 21:06:52'),
(1951, NULL, 246, 'Elaia 2 NG', NULL, 1.00, 1216.51, 1666.67, 2000.00, 450.16, 27.0094, 1216.51, 450.16, 1666.67, 2000.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-11 09:57:21', '2026-06-11 09:57:42'),
(1963, NULL, 247, 'degrippage etrier (AV/AR)', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-11 12:23:29', '2026-06-26 12:24:53'),
(1964, NULL, 247, 'chaine 10v', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-11 12:23:29', '2026-06-26 12:24:53'),
(1965, NULL, 247, 'cassette 10v 11-36T', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-11 12:23:29', '2026-06-26 12:24:53'),
(1966, NULL, 247, 'plateau x3 entraxe 104mm 1=42T 2=32T 3=24T', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-06-11 12:23:29', '2026-06-26 12:24:53'),
(1962, NULL, 247, 'devoilage (AV/AR)', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-11 12:23:29', '2026-06-26 12:24:53'),
(1967, NULL, 252, 'lampe', '524796', 1.00, 13.50, 22.50, 27.00, 9.00, 40.0000, 13.50, 9.00, 22.50, 27.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-11 12:39:41', '2026-06-11 12:40:15'),
(1968, NULL, 253, 'antivol axa', '534309', 1.00, 12.90, 19.92, 23.90, 7.02, 35.2301, 12.90, 7.02, 19.92, 23.90, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-11 12:45:37', '2026-06-13 15:48:55'),
(2041, NULL, 249, 'main d\'oeuvre remplacement jante', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-18 12:11:53', '2026-06-24 09:06:26'),
(1973, NULL, 249, 'devoilage AV', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-11 13:18:02', '2026-06-24 09:06:26'),
(1972, NULL, 249, 'jante AR details dans \"remarques\"', '483545', 1.00, 51.00, 80.17, 96.20, 29.17, 36.3825, 51.00, 29.17, 80.17, 96.20, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-11 13:18:02', '2026-06-24 09:06:26'),
(1975, NULL, 254, 'contitech', '529638', 1.00, 12.20, 13.66, 16.39, 1.46, 10.6772, 12.20, 1.46, 13.66, 16.39, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-11 13:40:24', '2026-06-11 13:40:29'),
(1980, NULL, 245, 'porte bagage à ajouter (enlever antivol surement)', '540597', 1.00, 37.50, 43.33, 52.00, 5.83, 13.4615, 37.50, 5.83, 43.33, 52.00, 20.0000, 7, NULL, 0, NULL, NULL, '2026-06-12 07:57:35', '2026-06-16 21:06:52'),
(1979, NULL, 245, 'manette derailleur AR 8v', '518659', 1.00, 14.83, 16.66, 19.99, 1.83, 10.9755, 14.83, 1.83, 16.66, 19.99, 20.0000, 6, NULL, 0, NULL, NULL, '2026-06-12 07:57:35', '2026-06-16 21:06:52'),
(1982, NULL, 258, 'casque MAVIC taille L vert', NULL, 1.00, 0.00, 132.50, 159.00, 132.50, 100.0000, 0.00, 132.50, 132.50, 159.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-13 12:50:19', '2026-06-13 12:50:26'),
(1983, NULL, 259, 'degrippage/reglage de dérailleur', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-13 13:23:39', '2026-06-25 06:19:48'),
(1984, 10, 259, 'bombonne graisse chaine', '479727', 1.00, 5.67, 8.63, 10.35, 2.96, 34.2609, 5.67, 2.96, 8.63, 10.35, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-13 13:23:39', '2026-06-25 06:19:48'),
(1985, 9, 259, 'sonette', '468266', 1.00, 0.79, 3.33, 4.00, 2.54, 76.3000, 0.79, 2.54, 3.33, 4.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-13 13:23:39', '2026-06-25 06:19:48'),
(1993, NULL, 261, 'patte de dérailleur', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-15 06:40:09', '2026-06-22 10:00:09'),
(1994, NULL, 261, 'purge / appoint hydrau freins (AV/AR)', NULL, 2.00, 0.00, 27.50, 33.00, 27.50, 100.0000, 0.00, 55.00, 55.00, 66.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-15 06:40:09', '2026-06-22 10:00:09'),
(1992, NULL, 261, 'dévoilage (AR)', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-15 06:40:09', '2026-06-22 10:00:09'),
(1995, NULL, 215, 'virvolt 500', 'virvolt 500', 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 0, NULL, 1, NULL, NULL, '2026-06-15 07:26:41', '2026-06-15 07:26:41'),
(1998, NULL, 255, 'changement chambre à air', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-15 12:24:08', '2026-06-16 08:57:16'),
(2000, NULL, 266, 'cbable , gaine derailleur central', NULL, 1.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 16.67, 16.67, 20.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-16 12:59:52', '2026-06-16 13:00:05'),
(2007, NULL, 245, 'main d\'oeuvre transmission', NULL, 1.00, 0.00, 45.83, 55.00, 45.83, 100.0000, 0.00, 45.83, 45.83, 55.00, 20.0000, 9, NULL, 0, NULL, NULL, '2026-06-16 21:06:52', '2026-06-16 21:06:52'),
(2008, NULL, 245, 'main d\'oeuvre pneumatique', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 10, NULL, 0, NULL, NULL, '2026-06-16 21:06:52', '2026-06-16 21:06:52'),
(2013, NULL, 267, 'pédales', '511066', 1.00, 3.30, 6.25, 7.50, 2.95, 47.2000, 3.30, 2.95, 6.25, 7.50, 20.0000, 0, NULL, 1, NULL, NULL, '2026-06-16 21:09:37', '2026-06-23 15:42:56'),
(2014, NULL, 267, 'installation', NULL, 1.00, 0.00, 4.17, 5.00, 4.17, 100.0000, 0.00, 4.17, 4.17, 5.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-16 21:09:37', '2026-06-23 15:42:56'),
(2015, NULL, 269, 'réglage dérailleur', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-17 12:07:37', '2026-06-17 12:45:15'),
(2016, NULL, 270, 'chambre a air hutchinson 26 x 1.95', '476516', 1.00, 5.30, 6.66, 7.99, 1.36, 20.4005, 5.30, 1.36, 6.66, 7.99, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-17 13:32:54', '2026-06-17 13:34:02'),
(2017, NULL, 271, 'rétroviseur', NULL, 1.00, 5.12, 8.33, 10.00, 3.21, 38.5600, 5.12, 3.21, 8.33, 10.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-17 13:34:49', '2026-06-17 13:35:18'),
(2036, NULL, 232, 'moteur pedalier Virvolt 900', NULL, 1.00, 0.00, 1041.67, 1250.00, 1041.67, 100.0000, 0.00, 1041.67, 1041.67, 1250.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-17 15:48:05', '2026-06-17 15:48:05'),
(2027, NULL, 265, 'reglage frein (AV/AR)', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-17 14:27:10', '2026-06-18 16:06:43'),
(2028, NULL, 265, 'dévoilage AV', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-17 14:27:10', '2026-06-18 16:06:43'),
(2029, NULL, 265, 'réglage dérailleur', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-17 14:27:10', '2026-06-18 16:06:43'),
(2026, NULL, 265, 'patins (AV/AR)', NULL, 2.00, 0.00, 4.58, 5.50, 4.58, 100.0000, 0.00, 9.17, 9.17, 11.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-17 14:27:10', '2026-06-18 16:06:43'),
(2037, NULL, 232, 'chaine renforcée 7V', NULL, 1.00, 0.00, 31.67, 38.00, 31.67, 100.0000, 0.00, 31.67, 31.67, 38.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-17 15:48:05', '2026-06-17 15:48:05'),
(2038, NULL, 232, 'béquille laterale', '515659', 1.00, 6.90, 11.50, 13.80, 4.60, 40.0000, 6.90, 4.60, 11.50, 13.80, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-17 15:48:05', '2026-06-17 15:48:05'),
(2043, NULL, 268, 'plaquette AR', NULL, 1.00, 0.00, 8.75, 10.50, 8.75, 100.0000, 0.00, 8.75, 8.75, 10.50, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-19 07:23:41', '2026-06-25 08:09:06'),
(2044, NULL, 268, 'remplacement plaquettes et reglage etrier', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-19 07:23:41', '2026-06-25 08:09:06'),
(2045, NULL, 268, 'purge AR', NULL, 1.00, 0.00, 27.50, 33.00, 27.50, 100.0000, 0.00, 27.50, 27.50, 33.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-19 07:23:41', '2026-06-25 08:09:06'),
(2046, NULL, 220, 'reglage derailleur AV/AR', 'a', 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-19 07:27:28', '2026-06-26 12:06:49'),
(2047, NULL, 278, 'devoilage', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-19 12:15:45', '2026-06-19 12:15:51'),
(2048, NULL, 279, 'pneus 26 x 2.10 vtt', NULL, 2.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-19 12:17:21', '2026-06-23 09:04:22'),
(2049, NULL, 279, 'montage pneu (AV/AR)', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-19 12:17:21', '2026-06-23 09:04:22'),
(2063, NULL, 248, 'chaine 8v + installation', NULL, 1.00, 3.79, 25.00, 30.00, 21.21, 84.8400, 3.79, 21.21, 25.00, 30.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-19 12:30:23', '2026-06-24 14:39:01'),
(2064, NULL, 248, 'installation cassette', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-06-19 12:30:23', '2026-06-24 14:39:01'),
(2065, NULL, 248, 'pneu AR Michelin 29x2.10', '517896', 1.00, 13.80, 23.00, 27.60, 9.20, 40.0000, 13.80, 9.20, 23.00, 27.60, 20.0000, 5, NULL, 0, NULL, NULL, '2026-06-19 12:30:23', '2026-06-24 14:39:01'),
(2206, NULL, 248, 'montage jante', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-24 13:40:57', '2026-06-24 14:39:01'),
(2067, NULL, 248, 'gaine cable derailleur AR', NULL, 1.00, 1.90, 16.67, 20.00, 14.77, 88.6000, 1.90, 14.77, 16.67, 20.00, 20.0000, 7, NULL, 0, NULL, NULL, '2026-06-19 12:30:23', '2026-06-24 14:39:01'),
(2062, NULL, 248, 'jante AV', '481507', 1.00, 46.60, 76.00, 91.20, 29.40, 38.6842, 46.60, 29.40, 76.00, 91.20, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-19 12:30:23', '2026-06-24 14:39:01'),
(2082, NULL, 277, 'reglage derailleur AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-06-19 12:55:26', '2026-06-25 13:01:12'),
(2081, NULL, 277, 'devoilage (AV/AR)', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-06-19 12:55:26', '2026-06-25 13:01:12'),
(2080, NULL, 277, 'velo 2 No 110', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-06-19 12:55:26', '2026-06-25 13:01:12'),
(2079, NULL, 277, 'carter chaine cassé', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-19 12:55:26', '2026-06-25 13:01:12'),
(2078, NULL, 277, 'devoilage (AV/AR)', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-19 12:55:26', '2026-06-25 13:01:12'),
(2077, NULL, 277, 'chaine renforcee 8v', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-19 12:55:26', '2026-06-25 13:01:12'),
(2076, NULL, 277, 'velo 1  No 103', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-19 12:55:26', '2026-06-25 13:01:12'),
(2083, NULL, 277, 'reglage frein (course) /ou/ appoint hydrau (course)', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 7, NULL, 0, NULL, NULL, '2026-06-19 12:55:26', '2026-06-25 13:01:12'),
(2099, NULL, 251, 'carter chaine', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-19 12:58:42', '2026-06-26 16:02:05'),
(2100, NULL, 251, 'bequille simple', '468257', 1.00, 6.75, 11.25, 13.50, 4.50, 40.0000, 6.75, 4.50, 11.25, 13.50, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-19 12:58:42', '2026-06-26 16:02:05'),
(2101, NULL, 251, 'chambre a air 26x1.75', '476517-U', 1.00, 2.75, 6.66, 7.99, 3.91, 58.6984, 2.75, 3.91, 6.66, 7.99, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-19 12:58:42', '2026-06-26 16:02:05'),
(2102, NULL, 251, 'pneu AR 26x1.75 type ville', '476641', 1.00, 11.85, 13.27, 15.92, 1.42, 10.6784, 11.85, 1.42, 13.27, 15.92, 20.0000, 4, NULL, 0, NULL, NULL, '2026-06-19 12:58:42', '2026-06-26 16:02:05'),
(2103, NULL, 251, 'patins freins (AV/AR)', NULL, 2.00, 1.90, 4.58, 5.50, 2.68, 58.5455, 3.80, 5.37, 9.17, 11.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-06-19 12:58:42', '2026-06-26 16:02:05'),
(2104, NULL, 251, 'devoilage AV', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-06-19 12:58:42', '2026-06-26 16:02:05'),
(2098, NULL, 251, 'capteur pedalage + installation', NULL, 1.00, 0.00, 37.50, 45.00, 37.50, 100.0000, 0.00, 37.50, 37.50, 45.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-19 12:58:42', '2026-06-26 16:02:05'),
(2105, NULL, 280, 'chambre a air', NULL, 1.00, 0.00, 5.42, 6.50, 5.42, 100.0000, 0.00, 5.42, 5.42, 6.50, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-19 13:35:57', '2026-06-19 13:35:57'),
(2106, NULL, 280, 'main d\'oeuvre', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-19 13:35:57', '2026-06-19 13:35:57'),
(2107, NULL, 264, 'chaine 1-3V', '481385', 1.00, 6.70, 10.05, 12.06, 3.35, 33.3333, 6.70, 3.35, 10.05, 12.06, 20.0000, 0, NULL, 1, NULL, NULL, '2026-06-19 14:09:04', '2026-06-22 05:45:32'),
(2123, NULL, 283, 'Riverside 500 custom', NULL, 1.00, 505.48, 558.33, 670.00, 52.85, 9.4663, 505.48, 52.85, 558.33, 670.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-22 05:47:02', '2026-06-24 13:06:19'),
(2124, NULL, 283, 'elaia 2 NG', NULL, 1.00, 1012.00, 1991.67, 2390.00, 979.67, 49.1883, 1012.00, 979.67, 1991.67, 2390.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-22 05:47:02', '2026-06-24 13:06:19'),
(2110, NULL, 264, 'remplacement chaine', NULL, 1.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 16.67, 16.67, 20.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-19 14:09:04', '2026-06-22 05:45:32'),
(2112, NULL, 264, 'devoilage (AV/AR)', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-19 14:09:04', '2026-06-22 05:45:32'),
(2125, NULL, 283, 'siege enfant', NULL, 1.00, 40.00, 33.33, 40.00, -6.67, -20.0000, 40.00, -6.67, 33.33, 40.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-22 05:47:02', '2026-06-24 13:06:19'),
(2114, NULL, 264, 'gaine cable frein AV', NULL, 1.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 16.67, 16.67, 20.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-19 14:09:04', '2026-06-22 05:45:32'),
(2115, NULL, 282, 'chambre a air hutchinson 700x 37-50', NULL, 1.00, 0.00, 5.75, 6.90, 5.75, 100.0000, 0.00, 5.75, 5.75, 6.90, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-20 14:05:55', '2026-06-20 14:06:05'),
(2118, NULL, 247, 'main d\'oeuvre transmission', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-06-21 13:18:52', '2026-06-26 12:24:53'),
(2122, NULL, 274, 'diagnostique', NULL, 1.00, 0.00, 25.00, 30.00, 25.00, 100.0000, 0.00, 25.00, 25.00, 30.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-22 05:43:39', '2026-06-22 05:43:39'),
(2128, NULL, 261, 'derailleur shimano tourney 6/7 speed SIS index', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-22 10:00:09', '2026-06-22 10:00:09'),
(2129, NULL, 277, 'plaquettes (AV/AR)', NULL, 1.00, 3.90, 8.75, 10.50, 4.85, 55.4286, 3.90, 4.85, 8.75, 10.50, 20.0000, 8, NULL, 0, NULL, NULL, '2026-06-22 15:05:00', '2026-06-25 13:01:12'),
(2130, NULL, 191, 'chaine 1v', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-23 12:59:24', '2026-06-23 12:59:24'),
(2131, NULL, 191, 'chaine 1v', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-23 12:59:24', '2026-06-23 12:59:24'),
(2132, NULL, 191, 'plateau 46t', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-23 12:59:25', '2026-06-23 12:59:25'),
(2133, NULL, 191, 'plateau 46t', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-23 12:59:25', '2026-06-23 12:59:25'),
(2134, NULL, 191, 'pignon roue libre 16t', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-23 12:59:25', '2026-06-23 12:59:25'),
(2135, NULL, 191, 'pignon roue libre 16t', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-23 12:59:25', '2026-06-23 12:59:25'),
(2136, NULL, 191, 'devoilage (AV/AR)', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-23 12:59:25', '2026-06-23 12:59:25'),
(2137, NULL, 191, 'devoilage (AV/AR)', NULL, 2.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 30.00, 30.00, 36.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-23 12:59:25', '2026-06-23 12:59:25'),
(2138, NULL, 191, 'gaine cable freins (AV/AR)', NULL, 2.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 33.33, 33.33, 40.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-06-23 12:59:25', '2026-06-23 12:59:25'),
(2139, NULL, 191, 'gaine cable freins (AV/AR)', NULL, 2.00, 0.00, 16.67, 20.00, 16.67, 100.0000, 0.00, 33.33, 33.33, 40.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-06-23 12:59:25', '2026-06-23 12:59:25'),
(2140, NULL, 191, 'reglages freins (AV/AR)', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-06-23 12:59:25', '2026-06-23 12:59:25'),
(2141, NULL, 191, 'pneu AR 44-584 26x1 1/2', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-06-23 12:59:25', '2026-06-23 12:59:25'),
(2142, NULL, 191, 'reglages freins (AV/AR)', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 5, NULL, 0, NULL, NULL, '2026-06-23 12:59:25', '2026-06-23 12:59:25'),
(2143, NULL, 191, 'fixation Garde boue AV', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 7, NULL, 0, NULL, NULL, '2026-06-23 12:59:25', '2026-06-23 12:59:25'),
(2144, NULL, 191, 'pneu AR 44-584 26x1 1/2', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-06-23 12:59:25', '2026-06-23 12:59:25'),
(2145, NULL, 191, 'fixation Garde boue AV', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 7, NULL, 0, NULL, NULL, '2026-06-23 12:59:25', '2026-06-23 12:59:25'),
(2155, NULL, 275, 'cassette 12 42', NULL, 1.00, 48.75, 48.75, 58.50, 0.00, 0.0000, 48.75, 0.00, 48.75, 58.50, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-23 13:02:45', '2026-06-24 14:43:01'),
(2156, NULL, 275, 'chaine 8V renforcée', '222475 P2R', 1.00, 11.90, 20.18, 24.22, 8.28, 41.0405, 11.90, 8.28, 20.18, 24.22, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-23 13:02:45', '2026-06-24 14:43:01'),
(2157, NULL, 275, 'Main d\'oeuvre', NULL, 1.00, 0.00, 45.83, 55.00, 45.83, 100.0000, 0.00, 45.83, 45.83, 55.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-06-23 13:02:45', '2026-06-24 14:43:01'),
(2154, NULL, 275, 'plateau Miranda 34d', NULL, 1.00, 20.82, 20.82, 24.99, 0.00, 0.0240, 20.82, 0.00, 20.82, 24.99, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-23 13:02:45', '2026-06-24 14:43:01'),
(2166, NULL, 260, 'devoilage leger (AV/AR)', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-23 14:49:06', '2026-06-25 06:17:03'),
(2165, NULL, 260, 'chaine 7v', NULL, 1.00, 3.94, 25.00, 30.00, 21.06, 84.2400, 3.94, 21.06, 25.00, 30.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-23 14:49:06', '2026-06-25 06:17:03'),
(2164, NULL, 260, 'patins freins (AV/AR)', NULL, 2.00, 1.90, 4.58, 5.50, 2.68, 58.5455, 3.80, 5.37, 9.17, 11.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-23 14:49:06', '2026-06-25 06:17:03'),
(2163, NULL, 260, 'fixer carter de chaine', NULL, 1.00, 0.00, 4.17, 5.00, 4.17, 100.0000, 0.00, 4.17, 4.17, 5.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-23 14:49:06', '2026-06-25 06:17:03'),
(2167, NULL, 260, 'reglage frein AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-06-23 14:49:06', '2026-06-25 06:17:03'),
(2168, NULL, 286, 'reglage derailleur', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-23 15:43:47', '2026-06-23 15:43:50'),
(2169, NULL, 287, 'chambre a air', '533560', 1.00, 2.45, 6.66, 7.99, 4.21, 63.2040, 2.45, 4.21, 6.66, 7.99, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-24 06:44:05', '2026-06-25 15:58:09'),
(2170, NULL, 287, 'main d\'oeuvre', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-24 06:44:05', '2026-06-25 15:58:09'),
(2178, NULL, 284, 'reglage freins AV', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-24 08:43:44', '2026-06-24 08:43:44'),
(2177, NULL, 284, 'command derailleur rotative plateaux x3', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-24 08:43:44', '2026-06-24 08:43:44'),
(2176, NULL, 284, 'kit lumiere (AV/AR)', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-24 08:43:44', '2026-06-24 08:43:44'),
(2175, NULL, 284, 'selle confort moyen', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-24 08:43:44', '2026-06-24 08:43:44'),
(2228, NULL, 292, 'réglage frein AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-25 12:55:22', '2026-06-25 12:55:29'),
(2198, NULL, 285, 'devoilage AR', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-24 09:19:22', '2026-06-25 06:14:36'),
(2196, NULL, 285, 'réglage dérailleur AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-24 09:19:22', '2026-06-25 06:14:36'),
(2195, NULL, 285, 'réglage freins (AV/AR)', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-24 09:19:22', '2026-06-25 06:14:36'),
(2194, NULL, 285, 'chaine 7v + main d\'oeuvre', NULL, 1.00, 3.94, 25.00, 30.00, 21.06, 84.2400, 3.94, 21.06, 25.00, 30.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-24 09:19:22', '2026-06-25 06:14:36'),
(2202, NULL, 288, 'montage', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-24 12:35:26', '2026-06-24 15:52:19'),
(2201, NULL, 288, 'chaine 9v renfocée', '538383 -U', 1.00, 15.76, 31.67, 38.00, 15.91, 50.2316, 15.76, 15.91, 31.67, 38.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-24 12:35:26', '2026-06-24 15:52:19'),
(2203, NULL, 290, 'rayon 275mm / 2mm', NULL, 1.00, 0.00, 1.67, 2.00, 1.67, 100.0000, 0.00, 1.67, 1.67, 2.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-24 13:04:55', '2026-06-25 06:08:02'),
(2204, NULL, 290, 'montage rayon', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-24 13:04:55', '2026-06-25 06:08:02'),
(2205, NULL, 290, 'devoilage', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-24 13:04:55', '2026-06-25 06:08:02'),
(2210, NULL, 248, 'montage pneu AR', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 6, NULL, 0, NULL, NULL, '2026-06-24 13:45:47', '2026-06-24 14:39:01'),
(2212, 12, 248, 'Chambre à air 29 x 2.10 - 2.40 VS', '493772 - U', 1.00, 2.00, 6.65, 7.98, 4.65, 69.9248, 2.00, 4.65, 6.65, 7.98, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-24 14:39:01', '2026-06-24 14:39:01'),
(2215, NULL, 275, 'manette 8V', '518659', 1.00, 14.83, 16.66, 19.99, 1.83, 10.9755, 14.83, 1.83, 16.66, 19.99, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-24 14:42:56', '2026-06-24 14:43:01'),
(2226, NULL, 262, 'roue 27.5 AV disque', '536097', 1.00, 35.05, 57.50, 69.00, 22.45, 39.0435, 35.05, 22.45, 57.50, 69.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-25 06:13:11', '2026-06-25 06:13:11'),
(2227, NULL, 262, 'montage', NULL, 1.00, 0.00, 12.50, 15.00, 12.50, 100.0000, 0.00, 12.50, 12.50, 15.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-25 06:13:11', '2026-06-25 06:13:11'),
(2229, NULL, 292, 'réglage dérailleur', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-25 12:55:22', '2026-06-25 12:55:29'),
(2230, NULL, 277, 'carter chaine cassé', NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.00, 0.00, 0.00, 20.0000, 9, NULL, 0, NULL, NULL, '2026-06-25 13:00:37', '2026-06-25 13:01:12'),
(2231, NULL, 289, 'béquille Haibike', '517519', 1.00, 8.55, 14.25, 17.10, 5.70, 40.0000, 8.55, 5.70, 14.25, 17.10, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-25 14:49:15', '2026-06-26 09:18:12'),
(2241, NULL, 293, 'cassette 11-31', '502632', 1.00, 16.90, 22.49, 26.99, 5.59, 24.8611, 16.90, 5.59, 22.49, 26.99, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-25 15:38:22', '2026-06-25 20:22:24'),
(2242, NULL, 293, 'plateaux x3 28-48 axe123mm', '495806', 1.00, 18.88, 23.32, 27.99, 4.45, 19.0568, 18.88, 4.45, 23.32, 27.99, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-25 15:38:22', '2026-06-25 20:22:24'),
(2243, NULL, 293, 'devoilage leger (AV/AR)', NULL, 1.00, 0.00, 15.00, 18.00, 15.00, 100.0000, 0.00, 15.00, 15.00, 18.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-06-25 15:38:22', '2026-06-25 20:22:24'),
(2240, NULL, 293, 'chaine 8v', NULL, 1.00, 3.94, 21.67, 26.00, 17.73, 81.8154, 3.94, 17.73, 21.67, 26.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-25 15:38:22', '2026-06-25 20:22:24'),
(2251, NULL, 289, 'cassette 11-34', '466279', 1.00, 17.32, 26.66, 31.99, 9.34, 35.0297, 17.32, 9.34, 26.66, 31.99, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-25 15:44:53', '2026-06-26 09:18:12'),
(2252, NULL, 289, 'plateau 17T', NULL, 1.00, 13.40, 22.32, 26.78, 8.92, 39.9552, 13.40, 8.92, 22.32, 26.78, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-25 15:44:53', '2026-06-26 09:18:12'),
(2250, NULL, 289, 'chaine 9v renforcée', '538383 -U', 1.00, 15.76, 31.67, 38.00, 15.91, 50.2316, 15.76, 15.91, 31.67, 38.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-25 15:44:53', '2026-06-26 09:18:12'),
(2255, NULL, 293, 'main d\'oeuvre tranmission', NULL, 1.00, 0.00, 37.50, 45.00, 37.50, 100.0000, 0.00, 37.50, 37.50, 45.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-25 20:22:24', '2026-06-25 20:22:24'),
(2256, NULL, 289, 'main d\'oeuvre', NULL, 1.00, 0.00, 37.50, 45.00, 37.50, 100.0000, 0.00, 37.50, 37.50, 45.00, 20.0000, 4, NULL, 0, NULL, NULL, '2026-06-25 20:36:00', '2026-06-26 09:18:12'),
(2266, NULL, 166, 'porte bidon', '491633', 1.00, 3.88, 7.42, 8.90, 3.54, 47.6854, 3.88, 3.54, 7.42, 8.90, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-26 09:27:51', '2026-06-28 19:26:03'),
(2267, NULL, 166, 'bidon', NULL, 1.00, 0.00, 3.75, 4.50, 3.75, 100.0000, 0.00, 3.75, 3.75, 4.50, 20.0000, 2, NULL, 0, NULL, NULL, '2026-06-26 09:27:51', '2026-06-28 19:26:03'),
(2268, NULL, 166, 'main d\'oeuvre', NULL, 1.00, 0.00, 8.33, 10.00, 8.33, 100.0000, 0.00, 8.33, 8.33, 10.00, 20.0000, 3, NULL, 0, NULL, NULL, '2026-06-26 09:27:51', '2026-06-28 19:26:03'),
(2265, NULL, 166, 'SONNETTE PING OPTIMIZ NOIR (DIAMETRE 22.2MM)', '468266', 1.00, 0.79, 3.33, 4.00, 2.54, 76.3000, 0.79, 2.54, 3.33, 4.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-26 09:27:51', '2026-06-28 19:26:03'),
(2269, NULL, 294, 'dégrippage / nettoyage piston etrier AV/AR', NULL, 2.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 21.67, 21.67, 26.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-26 12:25:36', '2026-06-26 12:25:36'),
(2270, NULL, 296, 'NCM Paris', NULL, 1.00, 580.00, 816.67, 980.00, 236.67, 28.9796, 580.00, 236.67, 816.67, 980.00, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-26 15:42:19', '2026-06-26 16:03:19'),
(2271, NULL, 300, 'chambre a air', NULL, 1.00, 0.00, 6.66, 7.99, 6.66, 100.0000, 0.00, 6.66, 6.66, 7.99, 20.0000, 0, NULL, 0, NULL, NULL, '2026-06-27 16:33:39', '2026-06-27 16:33:39'),
(2272, NULL, 300, 'montage', NULL, 1.00, 0.00, 10.83, 13.00, 10.83, 100.0000, 0.00, 10.83, 10.83, 13.00, 20.0000, 1, NULL, 0, NULL, NULL, '2026-06-27 16:33:39', '2026-06-27 16:33:39');

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `date_contact` datetime NOT NULL,
  `date_reservation` date NOT NULL,
  `date_recuperation` date DEFAULT NULL,
  `date_retour` date NOT NULL,
  `livraison_necessaire` tinyint(1) NOT NULL DEFAULT 0,
  `adresse_livraison` text DEFAULT NULL,
  `contact_livraison` varchar(255) DEFAULT NULL,
  `creneau_livraison` varchar(255) DEFAULT NULL,
  `recuperation_necessaire` tinyint(1) NOT NULL DEFAULT 0,
  `adresse_recuperation` text DEFAULT NULL,
  `contact_recuperation` varchar(255) DEFAULT NULL,
  `creneau_recuperation` varchar(255) DEFAULT NULL,
  `prix_total_ttc` decimal(10,2) NOT NULL,
  `acompte_demande` tinyint(1) NOT NULL DEFAULT 0,
  `acompte_montant` decimal(10,2) DEFAULT NULL,
  `acompte_paye_le` date DEFAULT NULL,
  `paiement_final_le` date DEFAULT NULL,
  `statut` enum('reserve','en_attente_acompte','en_cours','paye','annule') NOT NULL DEFAULT 'reserve',
  `raison_annulation` text DEFAULT NULL,
  `commentaires` text DEFAULT NULL,
  `selection` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selection`)),
  `color` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id`, `client_id`, `date_contact`, `date_reservation`, `date_recuperation`, `date_retour`, `livraison_necessaire`, `adresse_livraison`, `contact_livraison`, `creneau_livraison`, `recuperation_necessaire`, `adresse_recuperation`, `contact_recuperation`, `creneau_recuperation`, `prix_total_ttc`, `acompte_demande`, `acompte_montant`, `acompte_paye_le`, `paiement_final_le`, `statut`, `raison_annulation`, `commentaires`, `selection`, `color`, `created_at`, `updated_at`) VALUES
(1, 25, '2026-02-16 20:22:00', '2026-02-17', NULL, '2026-02-21', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'reserve', NULL, 'Installation selle Drifter\nInstallation potence haute\nRDV 11h ou 11h30', '[{\"bike_id\":\"bike_73\",\"dates\":[\"2026-02-17\",\"2026-02-18\",\"2026-02-19\",\"2026-02-20\",\"2026-02-21\"],\"is_hs\":false}]', 7, '2026-02-16 20:25:11', '2026-02-17 09:33:22'),
(2, 26, '2026-02-17 15:09:00', '2026-02-17', NULL, '2026-02-28', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'en_cours', NULL, 'vélo de prêt pendant le diag révision de son vélo', '[{\"bike_id\":\"bike_50\",\"dates\":[\"2026-02-17\",\"2026-02-18\",\"2026-02-19\",\"2026-02-20\",\"2026-02-21\",\"2026-02-22\",\"2026-02-23\",\"2026-02-24\",\"2026-02-25\",\"2026-02-26\",\"2026-02-27\",\"2026-02-28\"],\"is_hs\":false}]', 11, '2026-02-17 15:17:50', '2026-02-24 09:44:34'),
(12, 26, '2026-02-24 08:00:00', '2026-02-22', NULL, '2026-02-22', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'annule', 'erreur de saisie', 'pret , diag revision', '[{\"bike_id\":\"bike_50\",\"dates\":[\"2026-02-22\"],\"is_hs\":false}]', 11, '2026-02-24 08:06:41', '2026-02-24 09:41:17'),
(3, 27, '2026-02-17 15:09:00', '2026-02-24', NULL, '2026-02-28', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'en_cours', NULL, 'avec potence (1m77 et 1m73)\n10h30', '[{\"bike_id\":\"bike_51\",\"dates\":[\"2026-02-24\",\"2026-02-25\",\"2026-02-26\",\"2026-02-27\",\"2026-02-28\"],\"is_hs\":false},{\"bike_id\":\"bike_52\",\"dates\":[\"2026-02-24\",\"2026-02-25\",\"2026-02-26\",\"2026-02-27\",\"2026-02-28\"],\"is_hs\":false}]', 19, '2026-02-17 15:54:41', '2026-02-24 10:01:08'),
(4, 30, '2026-02-18 13:27:00', '2026-02-21', NULL, '2026-02-28', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'en_cours', NULL, 'potence\nrdv 10h', '[{\"bike_id\":\"bike_49\",\"dates\":[\"2026-02-21\",\"2026-02-22\",\"2026-02-23\",\"2026-02-24\",\"2026-02-25\",\"2026-02-26\",\"2026-02-27\",\"2026-02-28\"],\"is_hs\":false}]', 0, '2026-02-18 13:28:15', '2026-02-24 09:44:27'),
(5, 38, '2026-02-20 14:06:00', '2026-02-21', NULL, '2026-02-28', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'reserve', NULL, 'rdv 14h', '[{\"bike_id\":\"bike_53\",\"dates\":[\"2026-02-21\",\"2026-02-22\",\"2026-02-23\",\"2026-02-24\",\"2026-02-25\",\"2026-02-26\",\"2026-02-27\",\"2026-02-28\"],\"is_hs\":false}]', 14, '2026-02-20 16:42:25', '2026-03-09 09:22:49'),
(6, 39, '2026-02-21 08:02:00', '2026-07-07', NULL, '2026-07-12', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 322.00, 1, 96.00, '2026-03-13', NULL, 'reserve', NULL, NULL, '[{\"bike_id\":\"bike_51\",\"dates\":[\"2026-07-07\",\"2026-07-08\",\"2026-07-09\",\"2026-07-10\",\"2026-07-11\",\"2026-07-12\"],\"is_hs\":false},{\"bike_id\":\"bike_59\",\"dates\":[\"2026-07-07\",\"2026-07-08\",\"2026-07-09\",\"2026-07-10\",\"2026-07-11\",\"2026-07-12\"],\"is_hs\":false}]', 11, '2026-02-21 11:00:16', '2026-06-29 14:31:09'),
(37, 102, '2026-03-23 16:31:00', '2026-02-27', NULL, '2026-03-01', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 30.00, 0, NULL, NULL, NULL, 'annule', 'test front end', NULL, '[{\"bike_id\":\"bike_58\",\"dates\":[\"2026-02-27\",\"2026-02-28\",\"2026-03-01\"],\"is_hs\":false}]', 0, '2026-03-23 16:32:26', '2026-03-23 16:32:55'),
(7, 40, '2026-02-21 13:17:00', '2026-02-24', NULL, '2026-02-24', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'annule', 'reservation double erreur niko', NULL, '[]', 0, '2026-02-21 13:18:34', '2026-02-21 14:16:21'),
(8, 40, '2026-02-21 13:17:00', '2026-02-24', NULL, '2026-02-24', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'en_cours', NULL, 'a 14h', '[{\"bike_id\":\"bike_48\",\"dates\":[\"2026-02-24\"],\"is_hs\":false},{\"bike_id\":\"bike_54\",\"dates\":[\"2026-02-24\"],\"is_hs\":false},{\"bike_id\":\"bike_55\",\"dates\":[\"2026-02-24\"],\"is_hs\":false}]', 4, '2026-02-21 13:24:21', '2026-02-24 13:37:33'),
(11, 40, '2026-02-24 08:00:00', '2026-02-24', NULL, '2026-02-24', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'reserve', NULL, NULL, '[{\"bike_id\":\"bike_74\",\"dates\":[\"2026-02-24\"],\"is_hs\":false}]', 4, '2026-02-24 08:03:54', '2026-02-24 09:43:57'),
(9, 41, '2026-02-21 13:17:00', '2026-02-25', NULL, '2026-02-28', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, '2026-02-28', 'paye', NULL, 'avec potence', '[{\"bike_id\":\"bike_55\",\"dates\":[\"2026-02-25\",\"2026-02-26\",\"2026-02-27\",\"2026-02-28\"],\"is_hs\":false}]', 5, '2026-02-21 13:53:22', '2026-02-28 14:06:25'),
(10, 42, '2026-02-21 13:17:00', '2026-02-21', NULL, '2026-02-21', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'reserve', NULL, NULL, '[{\"bike_id\":\"bike_51\",\"dates\":[\"2026-02-21\"],\"is_hs\":false},{\"bike_id\":\"bike_52\",\"dates\":[\"2026-02-21\"],\"is_hs\":false}]', 3, '2026-02-21 14:57:28', '2026-02-21 14:57:51'),
(13, 26, '2026-02-24 08:00:00', '2026-02-23', NULL, '2026-02-23', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'annule', 'erreur de saisie', NULL, '[{\"bike_id\":\"bike_50\",\"dates\":[\"2026-02-23\"],\"is_hs\":false}]', 11, '2026-02-24 08:08:21', '2026-02-24 09:40:31'),
(14, 26, '2026-02-24 08:00:00', '2026-02-24', NULL, '2026-02-24', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'annule', 'erreur de saisie', NULL, '[{\"bike_id\":\"bike_50\",\"dates\":[\"2026-02-24\"],\"is_hs\":false}]', 11, '2026-02-24 08:08:45', '2026-02-24 09:40:49'),
(15, 26, '2026-02-24 08:00:00', '2026-02-25', NULL, '2026-02-25', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'annule', 'erreur de saisie', NULL, '[{\"bike_id\":\"bike_50\",\"dates\":[\"2026-02-25\"],\"is_hs\":false}]', 11, '2026-02-24 08:10:16', '2026-02-24 09:41:02'),
(16, 43, '2026-02-24 08:00:00', '2026-03-22', NULL, '2026-03-30', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 173.00, 0, NULL, NULL, NULL, 'paye', NULL, 'sacoche jaune x2 à confirmer', '[{\"bike_id\":\"bike_63\",\"dates\":[\"2026-03-22\",\"2026-03-23\",\"2026-03-24\",\"2026-03-25\",\"2026-03-26\",\"2026-03-27\",\"2026-03-28\",\"2026-03-29\",\"2026-03-30\"],\"is_hs\":false}]', 13, '2026-02-24 10:27:46', '2026-03-30 08:48:09'),
(17, 49, '2026-02-27 18:15:00', '2026-02-27', NULL, '2026-02-27', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'annule', 'debug', NULL, '[{\"bike_id\":\"bike_78\",\"dates\":[\"2026-02-27\"],\"is_hs\":false}]', 0, '2026-02-27 18:15:49', '2026-02-27 18:16:56'),
(18, 50, '2026-02-28 07:59:00', '2026-03-02', NULL, '2026-03-05', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 50.00, 0, NULL, NULL, NULL, 'paye', NULL, 'rdv 9h 9h30', '[{\"bike_id\":\"bike_78\",\"dates\":[\"2026-03-02\",\"2026-03-03\",\"2026-03-04\",\"2026-03-05\"],\"is_hs\":false}]', 0, '2026-02-28 07:59:41', '2026-03-05 14:17:49'),
(19, 51, '2026-02-28 10:48:00', '2026-02-28', NULL, '2026-03-01', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 34.00, 0, NULL, NULL, NULL, 'paye', NULL, 'Jo pour Niko : j\'ai fais la mise à jour. \ntu me diras ce que tu en pense', '[{\"bike_id\":\"bike_60\",\"dates\":[\"2026-02-28\",\"2026-03-01\"],\"is_hs\":false}]', 5, '2026-02-28 10:54:00', '2026-03-01 12:35:00'),
(20, 52, '2026-02-28 10:48:00', '2026-03-03', NULL, '2026-03-04', 1, 'Minard, 7, Plouézec, 22470.', '06 26 85 00 15', 'lundi vers 19h', 1, 'Minard, 7, Plouézec, 22470.', '06 26 85 00 15', 'mercredi vers 19h', 380.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_48\",\"dates\":[\"2026-03-03\",\"2026-03-04\"],\"is_hs\":false},{\"bike_id\":\"bike_49\",\"dates\":[\"2026-03-03\",\"2026-03-04\"],\"is_hs\":false},{\"bike_id\":\"bike_50\",\"dates\":[\"2026-03-03\",\"2026-03-04\"],\"is_hs\":false},{\"bike_id\":\"bike_51\",\"dates\":[\"2026-03-03\",\"2026-03-04\"],\"is_hs\":false},{\"bike_id\":\"bike_79\",\"dates\":[\"2026-03-03\",\"2026-03-04\"],\"is_hs\":false},{\"bike_id\":\"bike_80\",\"dates\":[\"2026-03-03\",\"2026-03-04\"],\"is_hs\":false}]', 17, '2026-02-28 10:58:01', '2026-03-07 08:01:13'),
(24, 38, '2026-03-09 09:22:00', '2026-03-01', NULL, '2026-04-13', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 78.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_52\",\"dates\":[\"2026-03-01\",\"2026-03-02\",\"2026-03-03\",\"2026-03-04\",\"2026-03-05\",\"2026-03-06\",\"2026-03-07\",\"2026-03-08\",\"2026-03-09\",\"2026-03-10\",\"2026-03-11\",\"2026-03-12\",\"2026-03-13\",\"2026-03-14\",\"2026-03-15\",\"2026-03-16\",\"2026-03-17\",\"2026-03-18\",\"2026-03-19\",\"2026-03-20\",\"2026-03-21\",\"2026-03-22\",\"2026-03-23\",\"2026-03-24\",\"2026-03-25\",\"2026-03-26\",\"2026-03-27\",\"2026-03-28\",\"2026-03-29\",\"2026-03-30\",\"2026-03-31\",\"2026-04-01\",\"2026-04-02\",\"2026-04-03\",\"2026-04-04\",\"2026-04-05\",\"2026-04-06\",\"2026-04-07\",\"2026-04-08\",\"2026-04-09\",\"2026-04-10\",\"2026-04-11\",\"2026-04-12\",\"2026-04-13\"],\"is_hs\":false}]', 14, '2026-03-09 09:24:31', '2026-04-14 08:28:12'),
(21, 58, '2026-03-04 08:13:00', '2026-07-28', NULL, '2026-07-30', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 110.00, 1, 37.00, '2026-03-05', NULL, 'reserve', NULL, NULL, '[{\"bike_id\":\"bike_51\",\"dates\":[\"2026-07-28\",\"2026-07-29\",\"2026-07-30\"],\"is_hs\":false}]', 0, '2026-03-04 08:44:12', '2026-03-17 14:41:20'),
(26, 71, '2026-03-09 16:46:00', '2026-08-22', NULL, '2026-08-29', 0, NULL, NULL, NULL, 1, 'Roscoff', '06 08 48 04 08', NULL, 828.00, 1, 250.00, '2026-03-04', NULL, 'reserve', NULL, '- Alain : 188 cm\n\n- Caro : 156 cm\n\n- Dany : 178 cm.', '[{\"bike_id\":\"bike_48\",\"dates\":[\"2026-08-22\",\"2026-08-23\",\"2026-08-24\",\"2026-08-25\",\"2026-08-26\",\"2026-08-27\",\"2026-08-28\",\"2026-08-29\"],\"is_hs\":false},{\"bike_id\":\"bike_59\",\"dates\":[\"2026-08-22\",\"2026-08-23\",\"2026-08-24\",\"2026-08-25\",\"2026-08-26\",\"2026-08-27\",\"2026-08-28\",\"2026-08-29\"],\"is_hs\":false},{\"bike_id\":\"bike_98\",\"dates\":[\"2026-08-22\",\"2026-08-23\",\"2026-08-24\",\"2026-08-25\",\"2026-08-26\",\"2026-08-27\",\"2026-08-28\",\"2026-08-29\"],\"is_hs\":false}]', 0, '2026-03-09 16:52:15', '2026-06-29 14:28:29'),
(213, 440, '2026-06-29 14:14:00', '2026-07-06', NULL, '2026-07-06', 1, 'Four à Chaux, situé au croisement de la rue du Four à Chaux, de la rue des Jardins du Port et du quai Armand Dayot', NULL, NULL, 1, 'Four à Chaux, situé au croisement de la rue du Four à Chaux, de la rue des Jardins du Port et du quai Armand Dayot', NULL, NULL, 388.00, 1, 117.00, NULL, NULL, 'en_attente_acompte', NULL, 'Daddy	180	VAE	Homme\nDiane	170	VAE	Dame\nJérôme	190	VTC	Homme\nPamela	165	VTC	Dame\nAlbane	170	VTC	Dame\nAnouchka	165	VTC	Dame\nAntonia	155	VTC	Dame\nCédric	180	VTC	Homme\nAurore	160	VTC	Dame\nColin	160	VTC	Homme\nJuliette	155	VTC	Dame\nCôme	140	VTC	Enfant\nGrégory	180	VTC	Homme\nStéphanie	170	VAE	Dame\nGaston	150	VTC	Enfant\nAuguste	140	VTC	Enfant\nSixtine			Siège enfant à monter sur un des deux VAE Dame', '[{\"bike_id\":\"bike_51\",\"dates\":[\"2026-07-06\"],\"is_hs\":false},{\"bike_id\":\"bike_52\",\"dates\":[\"2026-07-06\"],\"is_hs\":false},{\"bike_id\":\"bike_98\",\"dates\":[\"2026-07-06\"],\"is_hs\":false},{\"bike_id\":\"bike_83\",\"dates\":[\"2026-07-06\"],\"is_hs\":false},{\"bike_id\":\"bike_87\",\"dates\":[\"2026-07-06\"],\"is_hs\":false},{\"bike_id\":\"bike_60\",\"dates\":[\"2026-07-06\"],\"is_hs\":false},{\"bike_id\":\"bike_95\",\"dates\":[\"2026-07-06\"],\"is_hs\":false},{\"bike_id\":\"bike_107\",\"dates\":[\"2026-07-06\"],\"is_hs\":false},{\"bike_id\":\"bike_106\",\"dates\":[\"2026-07-06\"],\"is_hs\":false},{\"bike_id\":\"bike_71\",\"dates\":[\"2026-07-06\"],\"is_hs\":false},{\"bike_id\":\"bike_66\",\"dates\":[\"2026-07-06\"],\"is_hs\":false},{\"bike_id\":\"bike_67\",\"dates\":[\"2026-07-06\"],\"is_hs\":false},{\"bike_id\":\"bike_62\",\"dates\":[\"2026-07-06\"],\"is_hs\":false},{\"bike_id\":\"bike_73\",\"dates\":[\"2026-07-06\"],\"is_hs\":false},{\"bike_id\":\"bike_100\",\"dates\":[\"2026-07-06\"],\"is_hs\":false},{\"bike_id\":\"bike_72\",\"dates\":[\"2026-07-06\"],\"is_hs\":true}]', 2, '2026-06-29 14:49:54', '2026-06-29 15:05:24'),
(22, 62, '2026-03-04 10:34:00', '2026-03-04', NULL, '2026-03-04', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 15.00, 0, NULL, NULL, NULL, 'paye', NULL, 'demie journée', '[{\"bike_id\":\"bike_60\",\"start_date\":\"2026-03-04\",\"end_date\":\"2026-03-04\",\"dates\":[\"2026-03-04\"],\"is_hs\":false}]', 0, '2026-03-05 10:35:45', '2026-03-05 10:35:45'),
(23, 63, '2026-03-05 14:15:00', '2026-03-06', NULL, '2026-03-08', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 110.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_51\",\"dates\":[\"2026-03-06\",\"2026-03-07\",\"2026-03-08\"],\"is_hs\":false}]', 0, '2026-03-05 15:30:19', '2026-03-08 18:03:14'),
(25, 69, '2026-03-09 09:25:00', '2026-03-01', NULL, '2026-03-30', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'en_cours', NULL, NULL, '[{\"bike_id\":\"bike_53\",\"start_date\":\"2026-03-01\",\"end_date\":\"2026-03-30\",\"dates\":[\"2026-03-01\",\"2026-03-02\",\"2026-03-03\",\"2026-03-04\",\"2026-03-05\",\"2026-03-06\",\"2026-03-07\",\"2026-03-08\",\"2026-03-09\",\"2026-03-10\",\"2026-03-11\",\"2026-03-12\",\"2026-03-13\",\"2026-03-14\",\"2026-03-15\",\"2026-03-16\",\"2026-03-17\",\"2026-03-18\",\"2026-03-19\",\"2026-03-20\",\"2026-03-21\",\"2026-03-22\",\"2026-03-23\",\"2026-03-24\",\"2026-03-25\",\"2026-03-26\",\"2026-03-27\",\"2026-03-28\",\"2026-03-29\",\"2026-03-30\"],\"is_hs\":false}]', 0, '2026-03-09 09:26:21', '2026-03-09 09:26:21'),
(27, 72, '2026-03-09 16:52:00', '2026-08-23', NULL, '2026-08-28', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 380.00, 1, 114.00, '2026-03-11', NULL, 'reserve', NULL, '1m94 taille XL\net 1m69 taille M', '[{\"bike_id\":\"bike_51\",\"dates\":[\"2026-08-23\",\"2026-08-24\",\"2026-08-25\",\"2026-08-26\",\"2026-08-27\",\"2026-08-28\"],\"is_hs\":false},{\"bike_id\":\"bike_57\",\"dates\":[\"2026-08-23\",\"2026-08-24\",\"2026-08-25\",\"2026-08-26\",\"2026-08-27\",\"2026-08-28\"],\"is_hs\":true}]', 9, '2026-03-09 17:01:01', '2026-06-29 14:12:29'),
(29, 87, '2026-03-16 20:35:00', '2026-03-03', NULL, '2026-03-03', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 255.00, 1, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_74\",\"dates\":[\"2026-03-03\"],\"is_hs\":false},{\"bike_id\":\"bike_54\",\"dates\":[\"2026-03-03\"],\"is_hs\":false}]', 0, '2026-03-16 20:39:11', '2026-03-23 16:34:04'),
(28, 75, '2026-03-10 15:40:00', '2026-03-10', NULL, '2026-04-10', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 52.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_48\",\"dates\":[\"2026-03-10\",\"2026-03-11\",\"2026-03-12\",\"2026-03-13\",\"2026-03-14\",\"2026-03-15\",\"2026-03-16\",\"2026-03-17\",\"2026-03-18\",\"2026-03-19\",\"2026-03-20\",\"2026-03-21\",\"2026-03-22\",\"2026-03-23\",\"2026-03-24\",\"2026-03-25\",\"2026-03-26\",\"2026-03-27\",\"2026-03-28\",\"2026-03-29\",\"2026-03-30\",\"2026-03-31\",\"2026-04-01\",\"2026-04-02\",\"2026-04-03\",\"2026-04-04\",\"2026-04-05\",\"2026-04-06\",\"2026-04-07\",\"2026-04-08\",\"2026-04-09\",\"2026-04-10\"],\"is_hs\":false}]', 12, '2026-03-10 16:42:43', '2026-04-10 14:51:01'),
(51, 144, '2026-04-09 12:51:00', '2026-04-11', NULL, '2026-04-11', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 42.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_63\",\"dates\":[\"2026-04-11\"],\"is_hs\":false},{\"bike_id\":\"bike_64\",\"dates\":[\"2026-04-11\"],\"is_hs\":false},{\"bike_id\":\"bike_66\",\"dates\":[\"2026-04-11\"],\"is_hs\":false}]', 0, '2026-04-11 14:35:48', '2026-04-12 06:12:13'),
(38, 104, '2026-03-24 13:48:00', '2026-03-28', NULL, '2026-03-29', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 80.00, 0, NULL, NULL, NULL, 'paye', NULL, 'vendredi soir 18h55 arrivé du train', '[{\"bike_id\":\"bike_60\",\"dates\":[\"2026-03-28\",\"2026-03-29\"],\"is_hs\":false},{\"bike_id\":\"bike_72\",\"dates\":[\"2026-03-28\",\"2026-03-29\"],\"is_hs\":false}]', 11, '2026-03-24 13:55:20', '2026-03-30 06:11:06'),
(30, 88, '2026-03-17 14:35:00', '2026-03-18', NULL, '2026-04-17', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 104.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_54\",\"dates\":[\"2026-03-18\",\"2026-03-19\",\"2026-03-20\",\"2026-03-21\",\"2026-03-22\",\"2026-03-23\",\"2026-03-24\",\"2026-03-25\",\"2026-03-26\",\"2026-03-27\",\"2026-03-28\",\"2026-03-29\",\"2026-03-30\",\"2026-03-31\",\"2026-04-01\",\"2026-04-02\",\"2026-04-03\",\"2026-04-04\",\"2026-04-05\",\"2026-04-06\",\"2026-04-07\",\"2026-04-08\",\"2026-04-09\",\"2026-04-10\",\"2026-04-11\",\"2026-04-12\",\"2026-04-13\",\"2026-04-14\",\"2026-04-15\",\"2026-04-16\",\"2026-04-17\"],\"is_hs\":false},{\"bike_id\":\"bike_55\",\"dates\":[\"2026-03-18\",\"2026-03-19\",\"2026-03-20\",\"2026-03-21\",\"2026-03-22\",\"2026-03-23\",\"2026-03-24\",\"2026-03-25\",\"2026-03-26\",\"2026-03-27\",\"2026-03-28\",\"2026-03-29\",\"2026-03-30\",\"2026-03-31\",\"2026-04-01\",\"2026-04-02\",\"2026-04-03\",\"2026-04-04\",\"2026-04-05\",\"2026-04-06\",\"2026-04-07\",\"2026-04-08\",\"2026-04-09\",\"2026-04-10\",\"2026-04-11\",\"2026-04-12\",\"2026-04-13\",\"2026-04-14\",\"2026-04-15\",\"2026-04-16\",\"2026-04-17\"],\"is_hs\":false}]', 7, '2026-03-17 14:40:48', '2026-04-17 07:58:42'),
(58, 89, '2026-04-17 07:44:00', '2026-04-19', NULL, '2026-04-30', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 138.00, 0, NULL, NULL, NULL, 'en_cours', NULL, NULL, '[{\"bike_id\":\"bike_51\",\"dates\":[\"2026-04-19\",\"2026-04-20\",\"2026-04-21\",\"2026-04-22\",\"2026-04-23\",\"2026-04-24\",\"2026-04-25\",\"2026-04-26\",\"2026-04-27\",\"2026-04-28\",\"2026-04-29\",\"2026-04-30\"],\"is_hs\":false},{\"bike_id\":\"bike_59\",\"dates\":[\"2026-04-19\",\"2026-04-20\",\"2026-04-21\",\"2026-04-22\",\"2026-04-23\",\"2026-04-24\",\"2026-04-25\",\"2026-04-26\",\"2026-04-27\",\"2026-04-28\",\"2026-04-29\",\"2026-04-30\"],\"is_hs\":false}]', 0, '2026-04-18 07:38:00', '2026-04-29 07:23:25'),
(31, 89, '2026-03-17 15:36:00', '2026-03-18', NULL, '2026-03-18', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'paye', NULL, 'incliner la selle vers l\'avant légèrement pour la selle confort', '[{\"bike_id\":\"bike_51\",\"dates\":[\"2026-03-18\"],\"is_hs\":false},{\"bike_id\":\"bike_59\",\"dates\":[\"2026-03-18\"],\"is_hs\":false}]', 3, '2026-03-17 15:38:12', '2026-04-29 07:23:00'),
(32, 92, '2026-03-11 17:31:00', '2026-07-02', NULL, '2026-07-05', 1, 'hotel les agapanthes à ploubazlanec', '06 82 37 87 82', 'matin', 1, 'hotel les agapanthes à ploubazlanec', '06 82 37 87 82', 'lundi matin avec le proprio', 147.00, 1, 45.00, '2026-03-23', NULL, 'reserve', NULL, 'paiement à la livraison', '[{\"bike_id\":\"bike_50\",\"dates\":[\"2026-07-02\",\"2026-07-03\",\"2026-07-04\",\"2026-07-05\"],\"is_hs\":false},{\"bike_id\":\"bike_73\",\"dates\":[\"2026-07-02\",\"2026-07-03\",\"2026-07-04\",\"2026-07-05\"],\"is_hs\":false}]', 0, '2026-03-18 09:50:58', '2026-06-29 15:00:52'),
(33, 89, '2026-03-17 15:46:00', '2026-03-19', NULL, '2026-04-18', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_51\",\"dates\":[\"2026-03-19\",\"2026-03-20\",\"2026-03-21\",\"2026-03-22\",\"2026-03-23\",\"2026-03-24\",\"2026-03-25\",\"2026-03-26\",\"2026-03-27\",\"2026-03-28\",\"2026-03-29\",\"2026-03-30\",\"2026-03-31\",\"2026-04-01\",\"2026-04-02\",\"2026-04-03\",\"2026-04-04\",\"2026-04-05\",\"2026-04-06\",\"2026-04-07\",\"2026-04-08\",\"2026-04-09\",\"2026-04-10\",\"2026-04-11\",\"2026-04-12\",\"2026-04-13\",\"2026-04-14\",\"2026-04-15\",\"2026-04-16\",\"2026-04-17\",\"2026-04-18\"],\"is_hs\":false},{\"bike_id\":\"bike_59\",\"dates\":[\"2026-03-19\",\"2026-03-20\",\"2026-03-21\",\"2026-03-22\",\"2026-03-23\",\"2026-03-24\",\"2026-03-25\",\"2026-03-26\",\"2026-03-27\",\"2026-03-28\",\"2026-03-29\",\"2026-03-30\",\"2026-03-31\",\"2026-04-01\",\"2026-04-02\",\"2026-04-03\",\"2026-04-04\",\"2026-04-05\",\"2026-04-06\",\"2026-04-07\",\"2026-04-08\",\"2026-04-09\",\"2026-04-10\",\"2026-04-11\",\"2026-04-12\",\"2026-04-13\",\"2026-04-14\",\"2026-04-15\",\"2026-04-16\",\"2026-04-17\",\"2026-04-18\"],\"is_hs\":false}]', 3, '2026-03-18 16:21:12', '2026-04-29 07:23:12'),
(34, 95, '2026-03-17 15:46:00', '2026-03-18', NULL, '2026-03-18', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 32.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_81\",\"dates\":[\"2026-03-18\"],\"is_hs\":false}]', 0, '2026-03-18 17:04:02', '2026-03-19 14:33:01'),
(35, 98, '2026-03-21 09:50:00', '2026-03-21', NULL, '2026-03-21', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 40.00, 0, NULL, NULL, NULL, 'paye', NULL, 'erreur de ma part sur le TPE , j\'ai facturé 50€ au lieu de 40€ , j\'ai rendu 10€ en espèces', '[{\"bike_id\":\"bike_66\",\"dates\":[\"2026-03-21\"],\"is_hs\":false},{\"bike_id\":\"bike_67\",\"dates\":[\"2026-03-21\"],\"is_hs\":false}]', 0, '2026-03-21 09:52:04', '2026-03-21 16:11:36'),
(36, 100, '2026-03-21 09:50:00', '2026-03-21', NULL, '2026-03-21', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 25.00, 0, NULL, NULL, NULL, 'paye', NULL, 'demi journee\n1 velo sur 2 a été un enfer , deraillement constant , manque de puissance...\nremise sinon scandale en vu', '[{\"bike_id\":\"bike_81\",\"dates\":[\"2026-03-21\"],\"is_hs\":false},{\"bike_id\":\"bike_82\",\"dates\":[\"2026-03-21\"],\"is_hs\":false}]', 5, '2026-03-21 11:20:10', '2026-03-21 17:10:00'),
(39, 105, '2026-03-26 07:09:00', '2026-07-16', NULL, '2026-07-22', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 210.00, 1, 63.00, '2026-05-02', NULL, 'reserve', NULL, '1m63', '[{\"bike_id\":\"bike_51\",\"dates\":[\"2026-07-16\",\"2026-07-17\",\"2026-07-18\",\"2026-07-19\",\"2026-07-20\",\"2026-07-21\",\"2026-07-22\"],\"is_hs\":false}]', 0, '2026-03-26 08:09:03', '2026-05-29 06:33:31'),
(81, 210, '2026-05-01 18:25:00', '2026-10-16', NULL, '2026-10-17', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 76.00, 1, 23.00, '2026-06-24', NULL, 'reserve', NULL, '1m63 / 1m87', '[{\"bike_id\":\"bike_51\",\"dates\":[\"2026-10-16\",\"2026-10-17\"],\"is_hs\":false},{\"bike_id\":\"bike_57\",\"dates\":[\"2026-10-16\",\"2026-10-17\"],\"is_hs\":false}]', 0, '2026-05-01 22:43:03', '2026-06-25 06:31:12'),
(40, 112, '2026-03-30 14:55:00', '2026-03-31', NULL, '2026-03-31', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 16.00, 0, NULL, NULL, NULL, 'reserve', NULL, NULL, '[{\"bike_id\":\"bike_72\",\"start_date\":\"2026-03-31\",\"end_date\":\"2026-03-31\",\"dates\":[\"2026-03-31\"],\"is_hs\":false}]', 0, '2026-03-30 14:56:33', '2026-03-30 14:56:33'),
(41, 125, '2026-04-04 08:56:00', '2026-04-04', NULL, '2026-04-04', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 76.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_74\",\"start_date\":\"2026-04-04\",\"end_date\":\"2026-04-04\",\"dates\":[\"2026-04-04\"],\"is_hs\":false},{\"bike_id\":\"bike_53\",\"start_date\":\"2026-04-04\",\"end_date\":\"2026-04-04\",\"dates\":[\"2026-04-04\"],\"is_hs\":false}]', 0, '2026-04-04 16:03:13', '2026-04-04 16:03:13'),
(42, 126, '2026-04-04 08:56:00', '2026-04-05', NULL, '2026-04-06', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 64.00, 0, NULL, NULL, NULL, 'paye', NULL, 'retour mardi matin 9h', '[{\"bike_id\":\"bike_50\",\"dates\":[\"2026-04-05\",\"2026-04-06\"],\"is_hs\":false}]', 0, '2026-04-04 16:04:34', '2026-04-07 07:10:04'),
(43, 128, '2026-04-07 15:09:00', '2026-08-22', NULL, '2026-08-28', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 276.00, 1, 83.00, '2026-04-14', NULL, 'reserve', NULL, NULL, '[{\"bike_id\":\"bike_70\",\"dates\":[\"2026-08-22\",\"2026-08-23\",\"2026-08-24\",\"2026-08-25\",\"2026-08-26\",\"2026-08-27\",\"2026-08-28\"],\"is_hs\":false},{\"bike_id\":\"bike_71\",\"dates\":[\"2026-08-22\",\"2026-08-23\",\"2026-08-24\",\"2026-08-25\",\"2026-08-26\",\"2026-08-27\",\"2026-08-28\"],\"is_hs\":false}]', 13, '2026-04-07 15:11:53', '2026-05-29 06:35:25'),
(44, 130, '2026-04-08 06:43:00', '2026-06-09', NULL, '2026-06-12', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 140.00, 1, 42.00, '2026-04-09', NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_85\",\"dates\":[\"2026-06-09\",\"2026-06-10\",\"2026-06-11\",\"2026-06-12\"],\"is_hs\":false}]', 0, '2026-04-08 06:45:43', '2026-06-12 12:56:59'),
(55, 154, '2026-04-16 07:43:00', '2026-04-15', NULL, '2026-04-15', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 44.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_62\",\"dates\":[\"2026-04-15\"],\"is_hs\":false},{\"bike_id\":\"bike_71\",\"dates\":[\"2026-04-15\"],\"is_hs\":false},{\"bike_id\":\"bike_83\",\"dates\":[\"2026-04-15\"],\"is_hs\":false},{\"bike_id\":\"bike_87\",\"dates\":[\"2026-04-15\"],\"is_hs\":false}]', 0, '2026-04-16 16:07:22', '2026-04-16 16:08:36'),
(45, 131, '2026-04-08 07:57:00', '2026-04-08', NULL, '2026-04-08', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 76.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_85\",\"dates\":[\"2026-04-08\"],\"is_hs\":false},{\"bike_id\":\"bike_57\",\"dates\":[\"2026-04-08\"],\"is_hs\":false}]', 0, '2026-04-08 07:58:43', '2026-04-08 15:07:34'),
(49, 135, '2026-04-09 09:27:00', '2026-04-09', NULL, '2026-04-09', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 190.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_50\",\"dates\":[\"2026-04-09\"],\"is_hs\":false},{\"bike_id\":\"bike_74\",\"dates\":[\"2026-04-09\"],\"is_hs\":false},{\"bike_id\":\"bike_53\",\"dates\":[\"2026-04-09\"],\"is_hs\":false},{\"bike_id\":\"bike_85\",\"dates\":[\"2026-04-09\"],\"is_hs\":false},{\"bike_id\":\"bike_58\",\"dates\":[\"2026-04-09\"],\"is_hs\":false}]', 1, '2026-04-09 09:31:53', '2026-04-09 15:59:59'),
(46, 132, '2026-04-08 08:13:00', '2026-08-01', NULL, '2026-08-22', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 704.00, 1, 175.00, '2026-06-08', NULL, 'reserve', NULL, NULL, '[{\"bike_id\":\"bike_51\",\"dates\":[\"2026-08-01\",\"2026-08-02\",\"2026-08-03\",\"2026-08-04\",\"2026-08-05\",\"2026-08-06\",\"2026-08-07\",\"2026-08-08\",\"2026-08-09\",\"2026-08-10\",\"2026-08-11\",\"2026-08-12\",\"2026-08-13\",\"2026-08-14\",\"2026-08-15\",\"2026-08-16\",\"2026-08-17\",\"2026-08-18\",\"2026-08-19\",\"2026-08-20\",\"2026-08-21\",\"2026-08-22\"],\"is_hs\":false},{\"bike_id\":\"bike_52\",\"dates\":[\"2026-08-01\",\"2026-08-02\",\"2026-08-03\",\"2026-08-04\",\"2026-08-05\",\"2026-08-06\",\"2026-08-07\",\"2026-08-08\",\"2026-08-09\",\"2026-08-10\",\"2026-08-11\",\"2026-08-12\",\"2026-08-13\",\"2026-08-14\",\"2026-08-15\",\"2026-08-16\",\"2026-08-17\",\"2026-08-18\",\"2026-08-19\",\"2026-08-20\",\"2026-08-21\",\"2026-08-22\"],\"is_hs\":false}]', 7, '2026-04-08 08:17:45', '2026-06-29 14:20:16'),
(144, 55, '2026-06-02 06:00:00', '2026-06-03', NULL, '2026-06-04', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'paye', NULL, 'prêt le temps du diag', '[{\"bike_id\":\"bike_58\",\"dates\":[\"2026-06-03\",\"2026-06-04\"],\"is_hs\":false}]', 17, '2026-06-03 12:22:07', '2026-06-04 15:58:29'),
(47, 133, '2026-04-08 08:39:00', '2026-04-04', NULL, '2026-04-13', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 196.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_84\",\"dates\":[\"2026-04-04\",\"2026-04-05\",\"2026-04-06\",\"2026-04-07\",\"2026-04-08\",\"2026-04-09\",\"2026-04-10\",\"2026-04-11\",\"2026-04-12\",\"2026-04-13\"],\"is_hs\":false},{\"bike_id\":\"bike_87\",\"dates\":[\"2026-04-04\",\"2026-04-05\",\"2026-04-06\",\"2026-04-07\",\"2026-04-08\",\"2026-04-09\",\"2026-04-10\",\"2026-04-11\",\"2026-04-12\",\"2026-04-13\"],\"is_hs\":false}]', 12, '2026-04-08 08:43:38', '2026-04-13 09:04:23'),
(52, 146, '2026-04-14 07:27:00', '2026-05-14', NULL, '2026-05-17', 0, NULL, NULL, NULL, 1, 'perros guirec 17h15', NULL, '16h45', 320.00, 1, 110.00, '2026-05-06', NULL, 'paye', NULL, 'Si le monsieur s\'appelle Yann Algan, l\'acompte est payé le 5 mai. sinon, non !', '[{\"bike_id\":\"bike_92\",\"dates\":[\"2026-05-14\",\"2026-05-15\",\"2026-05-16\",\"2026-05-17\"],\"is_hs\":false},{\"bike_id\":\"bike_97\",\"dates\":[\"2026-05-14\",\"2026-05-15\",\"2026-05-16\",\"2026-05-17\"],\"is_hs\":false}]', 0, '2026-04-14 07:31:33', '2026-05-17 16:56:35'),
(48, 134, '2026-04-08 08:39:00', '2026-04-14', NULL, '2026-04-14', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 22.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_70\",\"dates\":[\"2026-04-14\"],\"is_hs\":false},{\"bike_id\":\"bike_84\",\"dates\":[\"2026-04-14\"],\"is_hs\":false}]', 15, '2026-04-08 12:12:25', '2026-04-14 15:31:34'),
(54, 150, '2026-04-14 07:27:00', '2026-05-01', NULL, '2026-05-01', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 380.00, 0, NULL, NULL, NULL, 'paye', NULL, '170 x 4\n180 x 4\n190 x 2 potences', '[{\"bike_id\":\"bike_55\",\"dates\":[\"2026-05-01\"],\"is_hs\":false},{\"bike_id\":\"bike_56\",\"dates\":[\"2026-05-01\"],\"is_hs\":false},{\"bike_id\":\"bike_82\",\"dates\":[\"2026-05-01\"],\"is_hs\":false},{\"bike_id\":\"bike_57\",\"dates\":[\"2026-05-01\"],\"is_hs\":false},{\"bike_id\":\"bike_59\",\"dates\":[\"2026-05-01\"],\"is_hs\":false},{\"bike_id\":\"bike_94\",\"dates\":[\"2026-05-01\"],\"is_hs\":false},{\"bike_id\":\"bike_51\",\"dates\":[\"2026-05-01\"],\"is_hs\":false},{\"bike_id\":\"bike_53\",\"dates\":[\"2026-05-01\"],\"is_hs\":false},{\"bike_id\":\"bike_85\",\"dates\":[\"2026-05-01\"],\"is_hs\":false}]', 17, '2026-04-14 16:05:04', '2026-05-01 16:14:53'),
(50, 136, '2026-04-09 09:27:00', '2026-04-09', NULL, '2026-04-09', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 90.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_49\",\"dates\":[\"2026-04-09\"],\"is_hs\":false},{\"bike_id\":\"bike_62\",\"dates\":[\"2026-04-09\"],\"is_hs\":false},{\"bike_id\":\"bike_83\",\"dates\":[\"2026-04-09\"],\"is_hs\":false},{\"bike_id\":\"bike_86\",\"dates\":[\"2026-04-09\"],\"is_hs\":false}]', 13, '2026-04-09 09:33:17', '2026-04-09 14:25:08'),
(102, 247, '2026-05-13 15:42:00', '2026-05-15', NULL, '2026-05-15', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 64.00, 0, NULL, NULL, NULL, 'paye', NULL, 'demi journee', '[{\"bike_id\":\"bike_94\",\"dates\":[\"2026-05-15\"],\"is_hs\":false},{\"bike_id\":\"bike_55\",\"dates\":[\"2026-05-15\"],\"is_hs\":false},{\"bike_id\":\"bike_100\",\"dates\":[\"2026-05-15\"],\"is_hs\":false}]', 9, '2026-05-15 09:05:23', '2026-05-15 15:37:20'),
(53, 147, '2026-04-14 07:27:00', '2026-07-10', NULL, '2026-07-10', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 92.00, 1, 30.00, NULL, NULL, 'annule', 'Prends un vélo cargo à la place', NULL, '[]', 2, '2026-04-14 07:35:05', '2026-05-29 12:48:03'),
(137, 309, '2026-05-29 14:12:00', '2026-06-01', NULL, '2026-06-04', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 253.00, 0, NULL, NULL, NULL, 'en_cours', NULL, 'avec siège enfant\n1.80\n1.63 milano\n119X2 + 15', '[{\"bike_id\":\"bike_59\",\"dates\":[\"2026-06-01\",\"2026-06-02\",\"2026-06-03\",\"2026-06-04\"],\"is_hs\":false}]', 18, '2026-05-29 14:21:24', '2026-06-01 16:34:27'),
(77, 206, '2026-04-30 16:09:00', '2026-05-01', NULL, '2026-05-01', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 55.00, 0, NULL, NULL, NULL, 'paye', NULL, 'Penlan gite partenaire\nprobleme de vélo qui s\'eteint em5 (guidonnage : vient de l\'avant ou de l\'arriere)', '[{\"bike_id\":\"bike_48\",\"dates\":[\"2026-05-01\"],\"is_hs\":false},{\"bike_id\":\"bike_52\",\"dates\":[\"2026-05-01\"],\"is_hs\":false}]', 15, '2026-05-01 07:57:03', '2026-05-01 15:09:01'),
(73, 199, '2026-04-30 10:44:00', '2026-07-07', NULL, '2026-07-31', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 390.00, 1, 117.00, '2026-05-04', NULL, 'reserve', NULL, '1m75', '[{\"bike_id\":\"bike_92\",\"dates\":[\"2026-07-07\",\"2026-07-08\",\"2026-07-09\",\"2026-07-10\",\"2026-07-11\",\"2026-07-12\",\"2026-07-13\",\"2026-07-14\",\"2026-07-15\",\"2026-07-16\",\"2026-07-17\",\"2026-07-18\",\"2026-07-19\",\"2026-07-20\",\"2026-07-21\",\"2026-07-22\",\"2026-07-23\",\"2026-07-24\",\"2026-07-25\",\"2026-07-26\",\"2026-07-27\",\"2026-07-28\",\"2026-07-29\",\"2026-07-30\",\"2026-07-31\"],\"is_hs\":false}]', 23, '2026-04-30 10:57:27', '2026-05-29 06:33:15'),
(56, 155, '2026-04-17 07:44:00', '2026-04-17', NULL, '2026-04-17', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 133.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_57\",\"dates\":[\"2026-04-17\"],\"is_hs\":false},{\"bike_id\":\"bike_92\",\"dates\":[\"2026-04-17\"],\"is_hs\":false},{\"bike_id\":\"bike_52\",\"dates\":[\"2026-04-17\"],\"is_hs\":false},{\"bike_id\":\"bike_53\",\"dates\":[\"2026-04-17\"],\"is_hs\":false}]', 0, '2026-04-17 07:54:06', '2026-04-17 13:42:23'),
(57, 156, '2026-04-17 07:44:00', '2026-04-30', NULL, '2026-05-02', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 110.00, 0, NULL, NULL, NULL, 'paye', NULL, '1.66', '[{\"bike_id\":\"bike_74\",\"dates\":[\"2026-04-30\",\"2026-05-01\",\"2026-05-02\"],\"is_hs\":false}]', 0, '2026-04-17 13:45:46', '2026-05-02 15:49:27'),
(71, 197, '2026-04-29 14:10:00', '2026-08-04', NULL, '2026-08-08', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 230.00, 1, 70.00, '2026-05-04', NULL, 'reserve', NULL, '1.81m 1.68m.', '[{\"bike_id\":\"bike_53\",\"dates\":[\"2026-08-04\",\"2026-08-05\",\"2026-08-06\",\"2026-08-07\",\"2026-08-08\"],\"is_hs\":false},{\"bike_id\":\"bike_57\",\"dates\":[\"2026-08-04\",\"2026-08-05\",\"2026-08-06\",\"2026-08-07\",\"2026-08-08\"],\"is_hs\":false}]', 24, '2026-04-29 14:36:05', '2026-05-29 06:32:39'),
(59, 161, '2026-04-16 07:43:00', '2026-04-18', NULL, '2026-04-18', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 50.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_74\",\"dates\":[\"2026-04-18\"],\"is_hs\":false},{\"bike_id\":\"bike_52\",\"dates\":[\"2026-04-18\"],\"is_hs\":false}]', 5, '2026-04-18 12:43:07', '2026-04-18 16:28:46'),
(60, 165, '2026-04-21 15:01:00', '2026-04-20', NULL, '2026-04-23', 1, 'ile a bois lezardrieux', NULL, NULL, 1, 'ile a bois lezardrieux', NULL, NULL, 800.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_94\",\"dates\":[\"2026-04-20\",\"2026-04-21\",\"2026-04-22\",\"2026-04-23\"],\"is_hs\":false},{\"bike_id\":\"bike_64\",\"dates\":[\"2026-04-20\",\"2026-04-21\",\"2026-04-22\",\"2026-04-23\"],\"is_hs\":false},{\"bike_id\":\"bike_60\",\"dates\":[\"2026-04-20\",\"2026-04-21\",\"2026-04-22\",\"2026-04-23\"],\"is_hs\":false},{\"bike_id\":\"bike_95\",\"dates\":[\"2026-04-20\",\"2026-04-21\",\"2026-04-22\",\"2026-04-23\"],\"is_hs\":false},{\"bike_id\":\"bike_67\",\"dates\":[\"2026-04-20\",\"2026-04-21\",\"2026-04-22\",\"2026-04-23\"],\"is_hs\":false},{\"bike_id\":\"bike_65\",\"dates\":[\"2026-04-20\",\"2026-04-21\",\"2026-04-22\",\"2026-04-23\"],\"is_hs\":false},{\"bike_id\":\"bike_63\",\"dates\":[\"2026-04-20\",\"2026-04-21\",\"2026-04-22\",\"2026-04-23\"],\"is_hs\":false},{\"bike_id\":\"bike_84\",\"dates\":[\"2026-04-20\",\"2026-04-21\",\"2026-04-22\",\"2026-04-23\"],\"is_hs\":false},{\"bike_id\":\"bike_90\",\"dates\":[\"2026-04-20\",\"2026-04-21\",\"2026-04-22\",\"2026-04-23\"],\"is_hs\":false},{\"bike_id\":\"bike_91\",\"dates\":[\"2026-04-20\",\"2026-04-21\",\"2026-04-22\",\"2026-04-23\"],\"is_hs\":false},{\"bike_id\":\"bike_93\",\"dates\":[\"2026-04-20\",\"2026-04-21\",\"2026-04-22\",\"2026-04-23\"],\"is_hs\":false}]', 5, '2026-04-21 15:03:56', '2026-04-25 15:10:47'),
(66, 179, '2026-04-23 08:43:00', '2026-04-25', NULL, '2026-04-26', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 152.00, 0, NULL, NULL, NULL, 'paye', NULL, 'recup dimanche apres midi , vu avec niko', '[{\"bike_id\":\"bike_74\",\"dates\":[\"2026-04-25\",\"2026-04-26\"],\"is_hs\":false},{\"bike_id\":\"bike_52\",\"dates\":[\"2026-04-25\",\"2026-04-26\"],\"is_hs\":false}]', 5, '2026-04-24 15:34:04', '2026-04-25 07:35:42'),
(67, 180, '2026-04-23 08:43:00', '2026-04-25', NULL, '2026-04-25', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 131.00, 0, NULL, NULL, NULL, 'paye', NULL, 'prise des velos a 11h45', '[{\"bike_id\":\"bike_50\",\"dates\":[\"2026-04-25\"],\"is_hs\":false},{\"bike_id\":\"bike_53\",\"dates\":[\"2026-04-25\"],\"is_hs\":false},{\"bike_id\":\"bike_54\",\"dates\":[\"2026-04-25\"],\"is_hs\":false},{\"bike_id\":\"bike_95\",\"dates\":[\"2026-04-25\"],\"is_hs\":false},{\"bike_id\":\"bike_70\",\"dates\":[\"2026-04-25\"],\"is_hs\":false},{\"bike_id\":\"bike_71\",\"dates\":[\"2026-04-25\"],\"is_hs\":false},{\"bike_id\":\"bike_62\",\"dates\":[\"2026-04-25\"],\"is_hs\":false}]', 19, '2026-04-24 16:13:43', '2026-04-25 15:49:35'),
(61, 166, '2026-04-21 15:01:00', '2026-04-22', NULL, '2026-04-22', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 66.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_55\",\"dates\":[\"2026-04-22\"],\"is_hs\":false},{\"bike_id\":\"bike_54\",\"dates\":[\"2026-04-22\"],\"is_hs\":false},{\"bike_id\":\"bike_57\",\"dates\":[\"2026-04-22\"],\"is_hs\":false}]', 3, '2026-04-21 15:07:57', '2026-04-22 15:32:46'),
(62, 168, '2026-04-22 09:20:00', '2026-04-22', NULL, '2026-04-22', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 24.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_71\",\"dates\":[\"2026-04-22\"],\"is_hs\":false},{\"bike_id\":\"bike_62\",\"dates\":[\"2026-04-22\"],\"is_hs\":false}]', 9, '2026-04-22 09:28:32', '2026-04-22 15:50:55'),
(63, 171, '2026-04-23 08:38:00', '2026-04-29', NULL, '2026-04-29', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 32.00, 0, NULL, NULL, NULL, 'en_cours', NULL, 'courtoisie renault passe le prendre la veille fin d\'apres midi', '[{\"bike_id\":\"bike_56\",\"dates\":[\"2026-04-29\"],\"is_hs\":false}]', 1, '2026-04-23 08:42:38', '2026-04-28 15:54:52'),
(64, 172, '2026-04-23 08:43:00', '2026-05-01', NULL, '2026-05-03', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 157.00, 0, NULL, NULL, NULL, 'paye', NULL, 'potence es5\n+ 1.70\n+ 1.70 (T3S)', '[{\"bike_id\":\"bike_96\",\"dates\":[\"2026-05-01\"],\"is_hs\":false},{\"bike_id\":\"bike_54\",\"dates\":[\"2026-05-01\",\"2026-05-02\",\"2026-05-03\"],\"is_hs\":false},{\"bike_id\":\"bike_92\",\"dates\":[\"2026-05-01\"],\"is_hs\":false}]', 9, '2026-04-23 08:49:21', '2026-05-01 16:12:36'),
(65, 173, '2026-04-23 08:43:00', '2026-04-23', NULL, '2026-04-23', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 44.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_92\",\"dates\":[\"2026-04-23\"],\"is_hs\":false},{\"bike_id\":\"bike_74\",\"dates\":[\"2026-04-23\"],\"is_hs\":false}]', 2, '2026-04-23 15:39:23', '2026-04-23 15:39:39'),
(68, 182, '2026-04-25 09:18:00', '2026-01-02', NULL, '2026-01-02', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 1, 0.00, NULL, NULL, 'annule', 'reservation d\'exemple pour formation', NULL, '[]', 0, '2026-04-25 09:32:42', '2026-05-24 12:04:37'),
(69, 185, '2026-04-25 15:11:00', '2026-04-25', NULL, '2026-04-25', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 20.00, 0, NULL, NULL, NULL, 'paye', NULL, '1 heure 17h/18h', '[{\"bike_id\":\"bike_49\",\"dates\":[\"2026-04-25\"],\"is_hs\":false},{\"bike_id\":\"bike_55\",\"dates\":[\"2026-04-25\"],\"is_hs\":false}]', 0, '2026-04-25 15:58:54', '2026-04-25 16:00:12'),
(70, 187, '2026-04-27 12:04:00', '2026-04-27', NULL, '2026-04-27', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 24.00, 0, NULL, NULL, NULL, 'paye', NULL, 'demie journée', '[{\"bike_id\":\"bike_62\",\"dates\":[\"2026-04-27\"],\"is_hs\":false},{\"bike_id\":\"bike_71\",\"dates\":[\"2026-04-27\"],\"is_hs\":false}]', 0, '2026-04-27 12:26:29', '2026-04-27 15:51:43'),
(85, 216, '2026-05-05 14:58:00', '2026-05-23', NULL, '2026-05-24', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 35.00, 1, 11.00, '2026-05-07', NULL, 'reserve', NULL, NULL, '[{\"bike_id\":\"bike_99\",\"dates\":[\"2026-05-23\",\"2026-05-24\"],\"is_hs\":false}]', 0, '2026-05-05 15:04:06', '2026-05-22 19:24:32'),
(72, 198, '2026-04-30 07:00:00', '2026-04-30', NULL, '2026-04-30', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 38.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_55\",\"dates\":[\"2026-04-30\"],\"is_hs\":false}]', 1, '2026-04-30 08:19:06', '2026-04-30 15:52:25'),
(74, 200, '2026-04-30 10:44:00', '2026-05-09', NULL, '2026-05-09', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 304.00, 1, 92.00, '2026-05-30', NULL, 'paye', NULL, 'H 1m74 Em1  F 1m58\nH 1m74 Em9  F 1m58\nH 1m82      F 1m62\nH 1m84      F 1m59', '[{\"bike_id\":\"bike_92\",\"dates\":[\"2026-05-09\"],\"is_hs\":false},{\"bike_id\":\"bike_85\",\"dates\":[\"2026-05-09\"],\"is_hs\":false},{\"bike_id\":\"bike_58\",\"dates\":[\"2026-05-09\"],\"is_hs\":false},{\"bike_id\":\"bike_53\",\"dates\":[\"2026-05-09\"],\"is_hs\":false},{\"bike_id\":\"bike_56\",\"dates\":[\"2026-05-09\"],\"is_hs\":false},{\"bike_id\":\"bike_82\",\"dates\":[\"2026-05-09\"],\"is_hs\":false},{\"bike_id\":\"bike_74\",\"dates\":[\"2026-05-09\"],\"is_hs\":false},{\"bike_id\":\"bike_48\",\"dates\":[\"2026-05-09\"],\"is_hs\":false}]', 0, '2026-04-30 11:07:12', '2026-05-09 14:32:56'),
(93, 231, '2026-05-08 13:30:00', '2026-05-09', NULL, '2026-05-09', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 40.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_64\",\"dates\":[\"2026-05-09\"],\"is_hs\":false},{\"bike_id\":\"bike_100\",\"dates\":[\"2026-05-09\"],\"is_hs\":false}]', 19, '2026-05-09 09:04:16', '2026-05-09 15:45:27'),
(76, 205, '2026-04-30 21:54:00', '2026-07-24', NULL, '2026-07-28', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 990.00, 1, 297.00, '2026-05-11', NULL, 'reserve', NULL, 'Suzanne: 155-160 cm (enfant)\nArthur: 155 cm (enfant)\nNathalie: 165 cm\nEric: 172 cm\nOlivia : 163 cm\nBenoit: 190 cm', '[{\"bike_id\":\"bike_48\",\"dates\":[\"2026-07-24\",\"2026-07-25\",\"2026-07-26\",\"2026-07-27\",\"2026-07-28\"],\"is_hs\":false},{\"bike_id\":\"bike_52\",\"dates\":[\"2026-07-24\",\"2026-07-25\",\"2026-07-26\",\"2026-07-27\",\"2026-07-28\"],\"is_hs\":false},{\"bike_id\":\"bike_53\",\"dates\":[\"2026-07-24\",\"2026-07-25\",\"2026-07-26\",\"2026-07-27\",\"2026-07-28\"],\"is_hs\":false},{\"bike_id\":\"bike_54\",\"dates\":[\"2026-07-24\",\"2026-07-25\",\"2026-07-26\",\"2026-07-27\",\"2026-07-28\"],\"is_hs\":false},{\"bike_id\":\"bike_56\",\"dates\":[\"2026-07-24\",\"2026-07-25\",\"2026-07-26\",\"2026-07-27\",\"2026-07-28\"],\"is_hs\":false},{\"bike_id\":\"bike_50\",\"dates\":[\"2026-07-24\",\"2026-07-25\",\"2026-07-26\",\"2026-07-27\",\"2026-07-28\"],\"is_hs\":false}]', 12, '2026-04-30 21:57:58', '2026-06-29 14:28:08'),
(75, 201, '2026-04-30 10:44:00', '2026-05-04', NULL, '2026-05-07', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 180.00, 0, NULL, NULL, NULL, 'paye', NULL, '1 vélos taille S = 1.58m\n1 vélo taille M = 1.70m\navec sacoches itinérance (4)\n170  + 10 euros pour le rétroviseur cassé', '[{\"bike_id\":\"bike_70\",\"dates\":[\"2026-05-04\",\"2026-05-05\",\"2026-05-06\",\"2026-05-07\"],\"is_hs\":false},{\"bike_id\":\"bike_60\",\"dates\":[\"2026-05-04\",\"2026-05-05\",\"2026-05-06\",\"2026-05-07\"],\"is_hs\":false}]', 0, '2026-04-30 11:13:25', '2026-05-08 07:38:55'),
(79, 208, '2026-05-01 09:41:00', '2026-06-24', NULL, '2026-07-02', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 520.00, 0, 0.00, NULL, NULL, 'annule', 'nous nous sommes raté sur la livraison. nous n\'avions pas preciser dans la reservation la livraison sur le logiciel + je n\'avais pas repondu au dernier sms concernant la validité de la reservation', 'F : 160cm\nH : 185cm', '[{\"bike_id\":\"bike_92\",\"dates\":[\"2026-06-24\",\"2026-06-25\",\"2026-06-26\",\"2026-06-27\",\"2026-06-28\",\"2026-06-29\",\"2026-06-30\",\"2026-07-01\",\"2026-07-02\"],\"is_hs\":false},{\"bike_id\":\"bike_53\",\"dates\":[\"2026-06-24\",\"2026-06-25\",\"2026-06-26\",\"2026-06-27\",\"2026-06-28\",\"2026-06-29\",\"2026-06-30\",\"2026-07-01\",\"2026-07-02\"],\"is_hs\":false}]', 9, '2026-05-01 10:20:28', '2026-06-25 06:02:45'),
(114, 273, '2026-05-22 07:22:00', '2026-05-24', NULL, '2026-05-25', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 32.00, 0, NULL, NULL, NULL, 'paye', NULL, '1m90\nViens le chercher le samedi soir après 18h00', '[{\"bike_id\":\"bike_72\",\"dates\":[\"2026-05-24\",\"2026-05-25\"],\"is_hs\":false}]', 0, '2026-05-22 09:55:17', '2026-05-25 15:03:36'),
(80, 209, '2026-05-01 11:58:00', '2026-08-31', NULL, '2026-09-05', 1, 'Chambre d\'Hôtes Kergiquel', NULL, NULL, 1, 'Chambre d\'Hôtes Kergiquel', NULL, NULL, 644.00, 1, 194.00, '2026-05-04', NULL, 'reserve', NULL, '1,70m\n1,65m\n1,60m\n1,58m', '[{\"bike_id\":\"bike_74\",\"dates\":[\"2026-08-31\",\"2026-09-01\",\"2026-09-02\",\"2026-09-03\",\"2026-09-04\",\"2026-09-05\"],\"is_hs\":false},{\"bike_id\":\"bike_96\",\"dates\":[\"2026-08-31\",\"2026-09-01\",\"2026-09-02\",\"2026-09-03\",\"2026-09-04\",\"2026-09-05\"],\"is_hs\":false},{\"bike_id\":\"bike_51\",\"dates\":[\"2026-08-31\",\"2026-09-01\",\"2026-09-02\",\"2026-09-03\",\"2026-09-04\",\"2026-09-05\"],\"is_hs\":false},{\"bike_id\":\"bike_52\",\"dates\":[\"2026-08-31\",\"2026-09-01\",\"2026-09-02\",\"2026-09-03\",\"2026-09-04\",\"2026-09-05\"],\"is_hs\":false}]', 1, '2026-05-01 16:26:54', '2026-06-20 08:33:50'),
(78, 207, '2026-04-30 16:09:00', '2026-05-01', NULL, '2026-05-01', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 40.00, 0, NULL, NULL, NULL, 'paye', NULL, '1.68 vae\n1.92 vtc\ndemie journée', '[{\"bike_id\":\"bike_73\",\"dates\":[\"2026-05-01\"],\"is_hs\":false},{\"bike_id\":\"bike_97\",\"dates\":[\"2026-05-01\"],\"is_hs\":false}]', 0, '2026-05-01 08:33:46', '2026-05-01 16:03:50'),
(83, 49, '2026-05-04 06:40:00', '2026-05-04', NULL, '2026-05-20', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'reserve', NULL, 'Vélo pour Alicia', '[{\"bike_id\":\"bike_49\",\"start_date\":\"2026-05-04\",\"end_date\":\"2026-05-20\",\"dates\":[\"2026-05-04\",\"2026-05-05\",\"2026-05-06\",\"2026-05-07\",\"2026-05-08\",\"2026-05-09\",\"2026-05-10\",\"2026-05-11\",\"2026-05-12\",\"2026-05-13\",\"2026-05-14\",\"2026-05-15\",\"2026-05-16\",\"2026-05-17\",\"2026-05-18\",\"2026-05-19\",\"2026-05-20\"],\"is_hs\":false}]', 0, '2026-05-04 15:58:29', '2026-05-04 15:58:29'),
(82, 211, '2026-05-02 11:20:00', '2026-07-04', NULL, '2026-07-11', 1, 'au gîte Dour Even à Ploubalzanec', NULL, NULL, 1, 'au gîte Dour Even à Ploubalzanec', NULL, NULL, 390.00, 1, 117.00, '2026-05-05', NULL, 'reserve', NULL, '1m65\n1m65\n1m65', '[{\"bike_id\":\"bike_53\",\"dates\":[\"2026-07-04\",\"2026-07-05\",\"2026-07-06\",\"2026-07-07\",\"2026-07-08\",\"2026-07-09\",\"2026-07-10\",\"2026-07-11\"],\"is_hs\":false},{\"bike_id\":\"bike_54\",\"dates\":[\"2026-07-04\",\"2026-07-05\",\"2026-07-06\",\"2026-07-07\",\"2026-07-08\",\"2026-07-09\",\"2026-07-10\",\"2026-07-11\"],\"is_hs\":false},{\"bike_id\":\"bike_55\",\"dates\":[\"2026-07-04\",\"2026-07-05\",\"2026-07-06\",\"2026-07-07\",\"2026-07-08\",\"2026-07-09\",\"2026-07-10\",\"2026-07-11\"],\"is_hs\":false}]', 0, '2026-05-02 11:33:53', '2026-05-29 07:52:47'),
(84, 215, '2026-05-05 10:55:00', '2026-06-29', NULL, '2026-07-03', 0, NULL, NULL, NULL, 1, '1 Rue de l\'Église, 29600 Morlaix (Ploujean)', NULL, NULL, 421.00, 1, 127.00, '2026-05-05', NULL, 'en_cours', NULL, 'vélo femme taille 161 cm sans barre pour accès facile au vélo\n*Vélo homme taille 165 cm', '[{\"bike_id\":\"bike_97\",\"dates\":[\"2026-06-29\",\"2026-06-30\",\"2026-07-01\",\"2026-07-02\",\"2026-07-03\"],\"is_hs\":false},{\"bike_id\":\"bike_85\",\"dates\":[\"2026-06-29\",\"2026-06-30\",\"2026-07-01\",\"2026-07-02\",\"2026-07-03\"],\"is_hs\":false}]', 4, '2026-05-05 12:55:50', '2026-06-29 13:43:14'),
(86, 218, '2026-05-04 06:40:00', '2026-05-06', NULL, '2026-05-06', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 140.00, 0, NULL, NULL, NULL, 'paye', NULL, 'demi + 2 PBB', '[{\"bike_id\":\"bike_48\",\"dates\":[\"2026-05-06\"],\"is_hs\":false},{\"bike_id\":\"bike_51\",\"dates\":[\"2026-05-06\"],\"is_hs\":false},{\"bike_id\":\"bike_52\",\"dates\":[\"2026-05-06\"],\"is_hs\":false},{\"bike_id\":\"bike_53\",\"dates\":[\"2026-05-06\"],\"is_hs\":false},{\"bike_id\":\"bike_94\",\"dates\":[\"2026-05-06\"],\"is_hs\":false},{\"bike_id\":\"bike_57\",\"dates\":[\"2026-05-06\"],\"is_hs\":false}]', 1, '2026-05-06 09:26:32', '2026-05-06 13:42:28'),
(90, 227, '2026-05-07 09:45:00', '2026-05-08', NULL, '2026-05-08', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 38.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_53\",\"dates\":[\"2026-05-08\"],\"is_hs\":false}]', 17, '2026-05-08 08:31:29', '2026-05-08 14:50:05'),
(91, 228, '2026-05-08 09:04:00', '2026-05-09', NULL, '2026-05-13', 1, '14 rue du toull broc\'h ploubazlanec\nhttps://maps.app.goo.gl/HMj6QNPEukK6CKNY9', NULL, '17h30 le 8 mai', 1, '14 rue du toull broc\'h ploubazlanec\nhttps://maps.app.goo.gl/HMj6QNPEukK6CKNY9', NULL, 'mini 18h', 352.00, 0, NULL, NULL, NULL, 'paye', NULL, '1.60\n1.83', '[{\"bike_id\":\"bike_57\",\"dates\":[\"2026-05-09\",\"2026-05-10\",\"2026-05-11\",\"2026-05-12\",\"2026-05-13\"],\"is_hs\":false},{\"bike_id\":\"bike_96\",\"dates\":[\"2026-05-09\",\"2026-05-10\",\"2026-05-11\",\"2026-05-12\",\"2026-05-13\"],\"is_hs\":false}]', 7, '2026-05-08 09:33:17', '2026-05-14 07:10:55'),
(87, 219, '2026-05-06 11:59:00', '2026-08-22', NULL, '2026-08-26', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 280.00, 1, 56.00, '2026-05-18', NULL, 'reserve', NULL, '1m65 femme\n1m80 homme', '[{\"bike_id\":\"bike_97\",\"dates\":[\"2026-08-22\",\"2026-08-23\",\"2026-08-24\",\"2026-08-25\",\"2026-08-26\"],\"is_hs\":false},{\"bike_id\":\"bike_92\",\"dates\":[\"2026-08-22\",\"2026-08-23\",\"2026-08-24\",\"2026-08-25\",\"2026-08-26\"],\"is_hs\":false}]', 6, '2026-05-06 12:01:50', '2026-05-29 06:30:23'),
(105, 256, '2026-05-18 07:34:00', '2026-07-29', NULL, '2026-08-03', 0, NULL, NULL, NULL, 1, 'Mont St Michel', NULL, NULL, 549.00, 1, 165.00, '2026-05-26', NULL, 'reserve', NULL, '1m78 et 1m57', '[{\"bike_id\":\"bike_96\",\"dates\":[\"2026-07-29\",\"2026-07-30\",\"2026-07-31\",\"2026-08-01\",\"2026-08-02\",\"2026-08-03\"],\"is_hs\":false},{\"bike_id\":\"bike_94\",\"dates\":[\"2026-07-29\",\"2026-07-30\",\"2026-07-31\",\"2026-08-01\",\"2026-08-02\",\"2026-08-03\"],\"is_hs\":false}]', 15, '2026-05-18 11:50:58', '2026-06-29 14:25:50');
INSERT INTO `reservations` (`id`, `client_id`, `date_contact`, `date_reservation`, `date_recuperation`, `date_retour`, `livraison_necessaire`, `adresse_livraison`, `contact_livraison`, `creneau_livraison`, `recuperation_necessaire`, `adresse_recuperation`, `contact_recuperation`, `creneau_recuperation`, `prix_total_ttc`, `acompte_demande`, `acompte_montant`, `acompte_paye_le`, `paiement_final_le`, `statut`, `raison_annulation`, `commentaires`, `selection`, `color`, `created_at`, `updated_at`) VALUES
(88, 220, '2026-05-04 06:40:00', '2026-05-06', NULL, '2026-05-06', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 25.00, 0, NULL, NULL, NULL, 'paye', NULL, 'demi', '[{\"bike_id\":\"bike_85\",\"dates\":[\"2026-05-06\"],\"is_hs\":false}]', 13, '2026-05-06 12:17:20', '2026-05-06 14:57:21'),
(89, 225, '2026-05-07 09:45:00', '2026-05-07', NULL, '2026-05-07', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 32.00, 0, NULL, NULL, NULL, 'paye', NULL, 'rendu le 8 au matin (9h)', '[{\"bike_id\":\"bike_53\",\"dates\":[\"2026-05-07\"],\"is_hs\":false}]', 0, '2026-05-07 09:49:45', '2026-05-08 07:38:28'),
(92, 230, '2026-05-08 13:30:00', '2026-05-08', NULL, '2026-05-09', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 38.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_54\",\"dates\":[\"2026-05-08\",\"2026-05-09\"],\"is_hs\":false}]', 9, '2026-05-08 13:54:49', '2026-05-09 13:14:24'),
(95, 233, '2026-05-09 10:57:00', '2026-06-15', NULL, '2026-06-15', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 12.00, 0, NULL, NULL, NULL, 'reserve', NULL, '1m70 femme\n1/2 journée (matin)', '[{\"bike_id\":\"bike_71\",\"dates\":[\"2026-06-15\"],\"is_hs\":false}]', 15, '2026-05-09 14:08:04', '2026-06-15 09:04:33'),
(96, 240, '2026-05-12 15:34:00', '2026-05-23', NULL, '2026-05-24', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 32.00, 0, NULL, NULL, NULL, 'paye', NULL, '1,65-70\nretour lundi matin', '[{\"bike_id\":\"bike_62\",\"dates\":[\"2026-05-23\",\"2026-05-24\"],\"is_hs\":false}]', 12, '2026-05-13 07:46:53', '2026-05-29 08:04:14'),
(94, 232, '2026-05-09 10:57:00', '2026-07-24', NULL, '2026-07-31', 1, '49 route de Bréhec', NULL, NULL, 1, '49 route de Bréhec', NULL, NULL, 138.00, 1, 42.00, NULL, NULL, 'annule', 'annulation vacance', '1m60 femme', '[{\"bike_id\":\"bike_70\",\"dates\":[\"2026-07-24\",\"2026-07-25\",\"2026-07-26\",\"2026-07-27\",\"2026-07-28\",\"2026-07-29\",\"2026-07-30\",\"2026-07-31\"],\"is_hs\":false}]', 0, '2026-05-09 11:06:48', '2026-05-29 12:14:04'),
(106, 258, '2026-05-19 07:43:00', '2026-05-19', NULL, '2026-05-20', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 256.00, 0, NULL, NULL, NULL, 'paye', NULL, 'partenaire', '[{\"bike_id\":\"bike_56\",\"dates\":[\"2026-05-19\",\"2026-05-20\"],\"is_hs\":false},{\"bike_id\":\"bike_82\",\"dates\":[\"2026-05-19\",\"2026-05-20\"],\"is_hs\":false},{\"bike_id\":\"bike_85\",\"dates\":[\"2026-05-19\",\"2026-05-20\"],\"is_hs\":false},{\"bike_id\":\"bike_53\",\"dates\":[\"2026-05-19\",\"2026-05-20\"],\"is_hs\":false}]', 7, '2026-05-19 08:35:13', '2026-05-20 16:37:21'),
(97, 241, '2026-05-12 15:34:00', '2026-05-13', NULL, '2026-05-13', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 76.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_48\",\"dates\":[\"2026-05-13\"],\"is_hs\":false},{\"bike_id\":\"bike_94\",\"dates\":[\"2026-05-13\"],\"is_hs\":false}]', 13, '2026-05-13 08:13:04', '2026-05-13 13:13:20'),
(99, 243, '2026-05-12 15:34:00', '2026-05-23', NULL, '2026-05-25', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 118.00, 0, NULL, NULL, NULL, 'paye', NULL, '2 vtc \n1.70,\n1.78', '[{\"bike_id\":\"bike_65\",\"dates\":[\"2026-05-23\",\"2026-05-24\",\"2026-05-25\"],\"is_hs\":false},{\"bike_id\":\"bike_67\",\"dates\":[\"2026-05-23\",\"2026-05-24\",\"2026-05-25\"],\"is_hs\":false}]', 13, '2026-05-13 13:34:20', '2026-05-25 15:59:21'),
(98, 242, '2026-05-12 15:34:00', '2026-05-20', NULL, '2026-05-22', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 59.00, 0, NULL, NULL, NULL, 'paye', NULL, 'récupéré le mardi soir', '[{\"bike_id\":\"bike_63\",\"dates\":[\"2026-05-20\",\"2026-05-21\",\"2026-05-22\"],\"is_hs\":false}]', 0, '2026-05-13 12:53:05', '2026-05-22 19:22:47'),
(118, 278, '2026-05-23 07:20:00', '2026-05-23', NULL, '2026-05-23', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 20.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_64\",\"dates\":[\"2026-05-23\"],\"is_hs\":false}]', 0, '2026-05-23 09:29:40', '2026-05-23 15:38:43'),
(100, 40, '2026-05-13 15:42:00', '2026-07-29', NULL, '2026-07-29', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 76.00, 0, NULL, NULL, NULL, 'reserve', NULL, 'bonne selles', '[{\"bike_id\":\"bike_74\",\"dates\":[\"2026-07-29\"],\"is_hs\":false},{\"bike_id\":\"bike_52\",\"dates\":[\"2026-07-29\"],\"is_hs\":false}]', 13, '2026-05-13 15:43:19', '2026-05-13 15:43:28'),
(101, 246, '2026-05-14 09:40:00', '2026-05-15', NULL, '2026-05-15', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 76.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_74\",\"dates\":[\"2026-05-15\"],\"is_hs\":false},{\"bike_id\":\"bike_59\",\"dates\":[\"2026-05-15\"],\"is_hs\":false}]', 15, '2026-05-14 15:45:05', '2026-05-15 15:27:54'),
(103, 248, '2026-05-15 09:59:00', '2026-05-15', NULL, '2026-05-15', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 70.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_53\",\"dates\":[\"2026-05-15\"],\"is_hs\":false},{\"bike_id\":\"bike_57\",\"dates\":[\"2026-05-15\"],\"is_hs\":false},{\"bike_id\":\"bike_79\",\"dates\":[\"2026-05-15\"],\"is_hs\":false}]', 1, '2026-05-15 10:00:54', '2026-05-15 16:01:32'),
(104, 250, '2026-05-15 09:59:00', '2026-05-15', NULL, '2026-05-15', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 25.00, 0, NULL, NULL, NULL, 'paye', NULL, 'demi', '[{\"bike_id\":\"bike_98\",\"dates\":[\"2026-05-15\"],\"is_hs\":false}]', 0, '2026-05-15 15:20:54', '2026-05-15 15:28:04'),
(128, 296, '2026-05-27 09:53:00', '2026-06-04', NULL, '2026-06-05', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 76.00, 0, NULL, NULL, NULL, 'annule', 'météo', NULL, '[{\"bike_id\":\"bike_53\",\"dates\":[\"2026-06-04\",\"2026-06-05\"],\"is_hs\":false}]', 7, '2026-05-27 13:29:29', '2026-06-08 06:39:56'),
(109, 265, '2026-05-20 18:42:00', '2026-05-30', NULL, '2026-05-31', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 117.05, 1, 46.00, '2026-05-23', NULL, 'paye', NULL, 'Après l\'appel de ce dimanche matin 11H : \nnous offrons une demie journée de location sur un vae + l\'intervention de la crevaison.\n76 + 38+ 25 = 139(reservation) - 21,95 (crevaison)= 117,05', '[{\"bike_id\":\"bike_82\",\"dates\":[\"2026-05-30\",\"2026-05-31\"],\"is_hs\":false},{\"bike_id\":\"bike_96\",\"dates\":[\"2026-05-30\",\"2026-05-31\"],\"is_hs\":false}]', 0, '2026-05-20 18:48:06', '2026-05-31 16:07:38'),
(107, 261, '2026-05-19 22:20:00', '2026-06-14', NULL, '2026-06-17', 1, 'St Malo', NULL, NULL, 1, 'St Malo', NULL, NULL, 846.00, 1, 269.00, '2026-05-20', NULL, 'paye', NULL, 'livraison la veille (samedi 19h)\nEric 1,82\nAnne 1,57\nLaurence 1,65\nFrançoise 1,68\n+2 sacoches jaune par vélo', '[{\"bike_id\":\"bike_51\",\"dates\":[\"2026-06-14\",\"2026-06-15\",\"2026-06-16\",\"2026-06-17\"],\"is_hs\":false},{\"bike_id\":\"bike_52\",\"dates\":[\"2026-06-14\",\"2026-06-15\",\"2026-06-16\",\"2026-06-17\"],\"is_hs\":false},{\"bike_id\":\"bike_57\",\"dates\":[\"2026-06-14\",\"2026-06-15\",\"2026-06-16\",\"2026-06-17\"],\"is_hs\":false},{\"bike_id\":\"bike_48\",\"dates\":[\"2026-06-14\",\"2026-06-15\",\"2026-06-16\",\"2026-06-17\"],\"is_hs\":false}]', 13, '2026-05-19 22:23:24', '2026-06-17 16:01:57'),
(108, 262, '2026-05-20 07:06:00', '2026-05-20', NULL, '2026-05-20', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 76.00, 0, NULL, NULL, NULL, 'en_cours', NULL, NULL, '[{\"bike_id\":\"bike_97\",\"dates\":[\"2026-05-20\"],\"is_hs\":false},{\"bike_id\":\"bike_57\",\"dates\":[\"2026-05-20\"],\"is_hs\":false}]', 0, '2026-05-20 07:11:19', '2026-05-20 09:25:29'),
(110, 266, '2026-05-20 13:28:00', '2026-05-22', NULL, '2026-05-22', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 125.00, 0, NULL, NULL, NULL, 'paye', NULL, 'demi', '[{\"bike_id\":\"bike_96\",\"dates\":[\"2026-05-22\"],\"is_hs\":false},{\"bike_id\":\"bike_74\",\"dates\":[\"2026-05-22\"],\"is_hs\":false},{\"bike_id\":\"bike_54\",\"dates\":[\"2026-05-22\"],\"is_hs\":false},{\"bike_id\":\"bike_56\",\"dates\":[\"2026-05-22\"],\"is_hs\":false},{\"bike_id\":\"bike_98\",\"dates\":[\"2026-05-22\"],\"is_hs\":false}]', 5, '2026-05-21 07:01:53', '2026-05-22 16:04:19'),
(111, 267, '2026-05-21 07:43:00', '2026-05-21', NULL, '2026-05-21', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 38.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_85\",\"dates\":[\"2026-05-21\"],\"is_hs\":false}]', 19, '2026-05-21 08:21:31', '2026-05-21 14:53:00'),
(113, 271, '2026-05-21 16:34:00', '2026-08-02', NULL, '2026-08-13', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 720.00, 1, 216.00, '2026-06-08', NULL, 'reserve', NULL, '1 VAE 1.69\n1 VTC 1m85\nA retirer le 01/08 après 18h00', '[{\"bike_id\":\"bike_97\",\"dates\":[\"2026-08-02\",\"2026-08-03\",\"2026-08-04\",\"2026-08-05\",\"2026-08-06\",\"2026-08-07\",\"2026-08-08\",\"2026-08-09\",\"2026-08-10\",\"2026-08-11\",\"2026-08-12\",\"2026-08-13\"],\"is_hs\":false},{\"bike_id\":\"bike_100\",\"dates\":[\"2026-08-02\",\"2026-08-03\",\"2026-08-04\",\"2026-08-05\",\"2026-08-06\",\"2026-08-07\",\"2026-08-08\",\"2026-08-09\",\"2026-08-10\",\"2026-08-11\",\"2026-08-12\",\"2026-08-13\"],\"is_hs\":false}]', 9, '2026-05-21 16:37:37', '2026-06-09 07:56:38'),
(112, 268, '2026-05-21 07:43:00', '2026-05-21', NULL, '2026-05-21', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 14.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_60\",\"dates\":[\"2026-05-21\"],\"is_hs\":false}]', 18, '2026-05-21 12:14:00', '2026-05-22 09:09:15'),
(115, 274, '2026-05-22 07:22:00', '2026-05-23', NULL, '2026-05-23', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 76.00, 0, NULL, NULL, NULL, 'annule', 'pas venu', 'CF mail arnaud Hugelin (app air bike)', '[{\"bike_id\":\"bike_98\",\"dates\":[\"2026-05-23\"],\"is_hs\":false},{\"bike_id\":\"bike_55\",\"dates\":[\"2026-05-23\"],\"is_hs\":false}]', 4, '2026-05-22 10:09:09', '2026-05-23 18:29:03'),
(117, 277, '2026-05-22 17:58:00', '2026-05-24', NULL, '2026-05-28', 1, 'chez palix', NULL, NULL, 1, 'chez palix', NULL, NULL, 712.00, 0, NULL, NULL, NULL, 'reserve', NULL, 'potence sur un L\nprobable livraison samedi soir (jo)\nF : 160, 172\n712 + potentiellement livraison\nH : 175, 186', '[{\"bike_id\":\"bike_92\",\"dates\":[\"2026-05-24\",\"2026-05-25\",\"2026-05-26\",\"2026-05-27\",\"2026-05-28\"],\"is_hs\":false},{\"bike_id\":\"bike_98\",\"dates\":[\"2026-05-24\",\"2026-05-25\",\"2026-05-26\",\"2026-05-27\",\"2026-05-28\"],\"is_hs\":false},{\"bike_id\":\"bike_53\",\"dates\":[\"2026-05-24\",\"2026-05-25\"],\"is_hs\":false},{\"bike_id\":\"bike_74\",\"dates\":[\"2026-05-24\",\"2026-05-25\",\"2026-05-26\",\"2026-05-27\",\"2026-05-28\"],\"is_hs\":false},{\"bike_id\":\"bike_97\",\"dates\":[\"2026-05-26\",\"2026-05-27\",\"2026-05-28\"],\"is_hs\":false}]', 2, '2026-05-22 18:01:23', '2026-05-27 16:17:59'),
(116, 275, '2026-05-22 14:51:00', '2026-05-23', NULL, '2026-05-25', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 279.00, 0, NULL, NULL, NULL, 'paye', NULL, 'f: 1.60, 1.60', '[{\"bike_id\":\"bike_96\",\"dates\":[\"2026-05-23\",\"2026-05-24\",\"2026-05-25\"],\"is_hs\":false},{\"bike_id\":\"bike_63\",\"dates\":[\"2026-05-23\",\"2026-05-24\",\"2026-05-25\"],\"is_hs\":false},{\"bike_id\":\"bike_48\",\"dates\":[\"2026-05-23\",\"2026-05-24\",\"2026-05-25\"],\"is_hs\":false}]', 7, '2026-05-22 15:26:47', '2026-05-25 16:29:51'),
(119, 281, '2026-05-23 10:15:00', '2026-05-24', NULL, '2026-05-24', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 40.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_60\",\"dates\":[\"2026-05-24\"],\"is_hs\":false},{\"bike_id\":\"bike_95\",\"dates\":[\"2026-05-24\"],\"is_hs\":false}]', 19, '2026-05-23 15:31:57', '2026-05-24 15:28:37'),
(121, 283, '2026-05-25 13:48:00', '2026-05-26', NULL, '2026-05-26', 1, 'camping paimpol', NULL, NULL, 1, 'camping paimpol', NULL, NULL, 64.00, 0, NULL, NULL, NULL, 'paye', NULL, '1.62, 1.85', '[{\"bike_id\":\"bike_94\",\"dates\":[\"2026-05-26\"],\"is_hs\":false},{\"bike_id\":\"bike_85\",\"dates\":[\"2026-05-26\"],\"is_hs\":false}]', 13, '2026-05-25 13:52:14', '2026-05-27 07:23:12'),
(120, 282, '2026-05-23 10:15:00', '2026-05-24', NULL, '2026-05-25', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 152.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_55\",\"dates\":[\"2026-05-24\",\"2026-05-25\"],\"is_hs\":false},{\"bike_id\":\"bike_51\",\"dates\":[\"2026-05-24\",\"2026-05-25\"],\"is_hs\":false}]', 0, '2026-05-23 16:56:11', '2026-05-25 15:57:38'),
(123, 287, '2026-05-26 12:40:00', '2026-05-26', NULL, '2026-05-27', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 38.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_59\",\"dates\":[\"2026-05-26\",\"2026-05-27\"],\"is_hs\":false}]', 29, '2026-05-26 12:41:31', '2026-05-27 09:48:22'),
(122, 284, '2026-05-26 08:03:00', '2026-05-26', NULL, '2026-06-02', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 230.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_51\",\"dates\":[\"2026-05-26\",\"2026-05-27\",\"2026-05-28\",\"2026-05-29\",\"2026-05-30\",\"2026-05-31\",\"2026-06-01\",\"2026-06-02\"],\"is_hs\":false}]', 6, '2026-05-26 08:05:31', '2026-06-02 09:00:58'),
(124, 288, '2026-05-26 12:57:00', '2026-05-26', NULL, '2026-05-26', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 64.00, 0, NULL, NULL, NULL, 'paye', NULL, 'demi\nmilano en +', '[{\"bike_id\":\"bike_96\",\"dates\":[\"2026-05-26\"],\"is_hs\":false},{\"bike_id\":\"bike_72\",\"dates\":[\"2026-05-26\"],\"is_hs\":false}]', 5, '2026-05-26 12:58:21', '2026-05-27 09:47:14'),
(126, 293, '2026-05-26 16:41:00', '2026-05-25', NULL, '2026-05-25', 1, 'diduan plouha', NULL, NULL, 1, 'diduan plouha', NULL, NULL, 192.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_59\",\"start_date\":\"2026-05-25\",\"end_date\":\"2026-05-25\",\"dates\":[\"2026-05-25\"],\"is_hs\":false},{\"bike_id\":\"bike_94\",\"start_date\":\"2026-05-25\",\"end_date\":\"2026-05-25\",\"dates\":[\"2026-05-25\"],\"is_hs\":false},{\"bike_id\":\"bike_52\",\"start_date\":\"2026-05-25\",\"end_date\":\"2026-05-25\",\"dates\":[\"2026-05-25\"],\"is_hs\":false},{\"bike_id\":\"bike_54\",\"start_date\":\"2026-05-25\",\"end_date\":\"2026-05-25\",\"dates\":[\"2026-05-25\"],\"is_hs\":false}]', 9, '2026-05-26 16:42:39', '2026-05-26 16:42:39'),
(125, 290, '2026-05-26 12:40:00', '2026-05-26', NULL, '2026-06-02', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 178.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_58\",\"dates\":[\"2026-05-26\",\"2026-05-27\",\"2026-05-28\",\"2026-05-29\",\"2026-05-30\",\"2026-05-31\",\"2026-06-01\",\"2026-06-02\"],\"is_hs\":false}]', 0, '2026-05-26 13:03:47', '2026-06-02 13:58:49'),
(127, 288, '2026-05-27 09:09:00', '2026-05-27', NULL, '2026-05-27', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 64.00, 0, NULL, NULL, NULL, 'paye', NULL, 'demi\nmilano + en plus', '[{\"bike_id\":\"bike_96\",\"dates\":[\"2026-05-27\"],\"is_hs\":false},{\"bike_id\":\"bike_67\",\"dates\":[\"2026-05-27\"],\"is_hs\":false}]', 13, '2026-05-27 09:35:00', '2026-05-27 15:51:17'),
(148, 327, '2026-06-03 09:33:00', '2026-06-04', NULL, '2026-06-05', 1, 'gite le cornec', '06 64 17 90 77   Mr Riso', 'fin de matinée', 1, 'gite le cornec', '06 64 17 90 77   Mr Riso', 'fin de journee', 82.00, 0, NULL, NULL, NULL, 'paye', NULL, 'pas de casques', '[{\"bike_id\":\"bike_52\",\"dates\":[\"2026-06-04\",\"2026-06-05\"],\"is_hs\":false},{\"bike_id\":\"bike_65\",\"dates\":[\"2026-06-04\",\"2026-06-05\"],\"is_hs\":false}]', 5, '2026-06-04 07:41:47', '2026-06-06 06:51:09'),
(129, 298, '2026-05-28 07:46:00', '2026-05-28', NULL, '2026-05-28', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 76.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_53\",\"dates\":[\"2026-05-28\"],\"is_hs\":true},{\"bike_id\":\"bike_59\",\"dates\":[\"2026-05-28\"],\"is_hs\":false}]', 1, '2026-05-28 08:45:10', '2026-05-28 16:13:22'),
(135, 305, '2026-05-28 15:22:00', '2026-07-07', NULL, '2026-07-12', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 330.00, 1, 99.00, '2026-05-30', NULL, 'reserve', NULL, '1,83 m pour 75 kg : homme\nEt 1,72 cm pour 56 kg : femme.', '[{\"bike_id\":\"bike_85\",\"dates\":[\"2026-07-07\",\"2026-07-08\",\"2026-07-09\",\"2026-07-10\",\"2026-07-11\"],\"is_hs\":false},{\"bike_id\":\"bike_98\",\"dates\":[\"2026-07-07\",\"2026-07-08\",\"2026-07-09\",\"2026-07-10\",\"2026-07-11\",\"2026-07-12\"],\"is_hs\":false}]', 7, '2026-05-28 19:04:21', '2026-05-31 14:11:26'),
(130, 299, '2026-05-28 08:48:00', '2026-05-28', NULL, '2026-05-29', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 54.00, 0, NULL, NULL, NULL, 'reserve', NULL, NULL, '[{\"bike_id\":\"bike_96\",\"start_date\":\"2026-05-28\",\"end_date\":\"2026-05-29\",\"dates\":[\"2026-05-28\",\"2026-05-29\"],\"is_hs\":false}]', 19, '2026-05-28 09:04:52', '2026-05-28 09:04:52'),
(131, 301, '2026-05-28 09:44:00', '2026-05-31', NULL, '2026-06-01', 1, '1 Impasse de la Roche Noire\n22220 Plouguiel, Bretagne', NULL, '8h00', 1, '1 Impasse de la Roche Noire\n22220 Plouguiel, Bretagne', NULL, '18h00', 128.00, 1, 39.00, '2026-06-01', NULL, 'paye', NULL, '1m70 femme', '[{\"bike_id\":\"bike_52\",\"dates\":[\"2026-05-31\",\"2026-06-01\"],\"is_hs\":false}]', 14, '2026-05-28 09:47:48', '2026-06-01 16:33:44'),
(132, 302, '2026-05-28 09:49:00', '2026-07-19', NULL, '2026-07-24', 1, 'Plouha (Kermaria)', NULL, NULL, 1, 'Plouha (Kermaria)', NULL, NULL, 428.00, 1, 129.00, '2026-05-28', NULL, 'reserve', NULL, '1m80 H 1m70 F', '[{\"bike_id\":\"bike_97\",\"dates\":[\"2026-07-19\",\"2026-07-20\",\"2026-07-21\",\"2026-07-22\",\"2026-07-23\",\"2026-07-24\"],\"is_hs\":false},{\"bike_id\":\"bike_59\",\"dates\":[\"2026-07-19\",\"2026-07-20\",\"2026-07-21\",\"2026-07-22\",\"2026-07-23\",\"2026-07-24\"],\"is_hs\":false}]', 8, '2026-05-28 09:54:02', '2026-05-29 06:28:11'),
(133, 303, '2026-05-28 09:49:00', '2026-06-17', NULL, '2026-06-17', 1, 'hôtel les AGAPANTHES à PLOUBAZLANEC', NULL, '8h30', 1, 'hôtel les AGAPANTHES à PLOUBAZLANEC', NULL, NULL, 352.00, 1, 106.00, '2026-06-01', NULL, 'paye', NULL, 'Pour les femmes 162,163,165,167,172\nPour les hommes 165,173,175,180,187\nretirer 1.62: 9velos au lieu de 10\n\n10euros de plus pour retroviseur cassé', '[{\"bike_id\":\"bike_55\",\"dates\":[\"2026-06-17\"],\"is_hs\":false},{\"bike_id\":\"bike_97\",\"dates\":[\"2026-06-17\"],\"is_hs\":false},{\"bike_id\":\"bike_56\",\"dates\":[\"2026-06-17\"],\"is_hs\":false},{\"bike_id\":\"bike_82\",\"dates\":[\"2026-06-17\"],\"is_hs\":false},{\"bike_id\":\"bike_59\",\"dates\":[\"2026-06-17\"],\"is_hs\":false},{\"bike_id\":\"bike_94\",\"dates\":[\"2026-06-17\"],\"is_hs\":false},{\"bike_id\":\"bike_58\",\"dates\":[\"2026-06-17\"],\"is_hs\":false},{\"bike_id\":\"bike_53\",\"dates\":[\"2026-06-17\"],\"is_hs\":false},{\"bike_id\":\"bike_85\",\"dates\":[\"2026-06-17\"],\"is_hs\":false}]', 0, '2026-05-28 09:59:57', '2026-06-17 17:19:33'),
(150, 330, '2026-06-04 09:03:00', '2026-06-29', NULL, '2026-07-03', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 190.00, 0, NULL, NULL, NULL, 'reserve', NULL, NULL, '[{\"bike_id\":\"bike_54\",\"dates\":[\"2026-06-29\",\"2026-06-30\",\"2026-07-01\",\"2026-07-02\",\"2026-07-03\"],\"is_hs\":false}]', 17, '2026-06-04 12:38:25', '2026-06-29 13:23:19'),
(136, 308, '2026-05-29 12:05:00', '2026-07-10', NULL, '2026-07-10', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 88.00, 1, 27.00, '2026-05-29', NULL, 'reserve', NULL, '1m74 + 1m80 (vélo cargo)', '[{\"bike_id\":\"bike_101\",\"dates\":[\"2026-07-10\"],\"is_hs\":false},{\"bike_id\":\"bike_97\",\"dates\":[\"2026-07-10\"],\"is_hs\":false}]', 1, '2026-05-29 12:43:03', '2026-06-29 14:30:53'),
(134, 304, '2026-05-28 13:06:00', '2026-05-28', NULL, '2026-05-28', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 14.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_100\",\"start_date\":\"2026-05-28\",\"end_date\":\"2026-05-28\",\"dates\":[\"2026-05-28\"],\"is_hs\":true}]', 0, '2026-05-28 14:25:43', '2026-05-28 14:25:43'),
(141, 318, '2026-06-02 13:50:00', '2026-06-03', NULL, '2026-06-04', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 32.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_60\",\"dates\":[\"2026-06-03\",\"2026-06-04\"],\"is_hs\":false}]', 0, '2026-06-02 14:15:55', '2026-06-04 10:13:05'),
(138, 310, '2026-05-29 14:45:00', '2026-05-30', NULL, '2026-05-30', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 54.00, 0, NULL, NULL, NULL, 'paye', NULL, 'NCM MILANO + EM1\n1.78\n1.58 (milano)\ndepart 14h', '[{\"bike_id\":\"bike_56\",\"dates\":[\"2026-05-30\"],\"is_hs\":false}]', 13, '2026-05-29 14:47:45', '2026-05-30 15:40:44'),
(139, 314, '2026-05-29 16:29:00', '2026-05-30', NULL, '2026-05-30', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 76.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_85\",\"dates\":[\"2026-05-30\"],\"is_hs\":false},{\"bike_id\":\"bike_52\",\"dates\":[\"2026-05-30\"],\"is_hs\":false}]', 1, '2026-05-30 08:01:42', '2026-05-30 15:06:05'),
(140, 315, '2026-05-30 11:59:00', '2026-08-11', NULL, '2026-08-20', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 406.00, 1, 122.00, '2026-06-01', NULL, 'reserve', NULL, '1,81 m et 1,63m.', '[{\"bike_id\":\"bike_57\",\"dates\":[\"2026-08-11\",\"2026-08-12\",\"2026-08-13\",\"2026-08-14\",\"2026-08-15\",\"2026-08-16\",\"2026-08-17\",\"2026-08-18\",\"2026-08-19\",\"2026-08-20\"],\"is_hs\":false},{\"bike_id\":\"bike_85\",\"dates\":[\"2026-08-11\",\"2026-08-12\",\"2026-08-13\",\"2026-08-14\",\"2026-08-15\",\"2026-08-16\",\"2026-08-17\",\"2026-08-18\",\"2026-08-19\",\"2026-08-20\"],\"is_hs\":false}]', 5, '2026-05-30 12:04:26', '2026-06-29 14:10:34'),
(142, 319, '2026-06-02 14:17:00', '2026-06-28', NULL, '2026-07-03', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 165.00, 1, 50.00, '2026-06-03', NULL, 'en_cours', NULL, '1m67 femme', '[{\"bike_id\":\"bike_52\",\"dates\":[\"2026-06-28\",\"2026-06-29\",\"2026-06-30\",\"2026-07-01\",\"2026-07-02\",\"2026-07-03\"],\"is_hs\":false}]', 15, '2026-06-02 14:20:17', '2026-06-28 16:49:10'),
(143, 320, '2026-06-02 14:17:00', '2026-07-13', NULL, '2026-07-24', 1, 'Petites maisons Arin', NULL, NULL, 1, 'Petites Maisons Arin', NULL, NULL, 447.00, 1, 134.00, '2026-06-03', NULL, 'reserve', NULL, '172cm x 2 en VTC\n138 cm enfant', '[{\"bike_id\":\"bike_67\",\"dates\":[\"2026-07-13\",\"2026-07-14\",\"2026-07-15\",\"2026-07-16\",\"2026-07-17\",\"2026-07-18\",\"2026-07-19\",\"2026-07-20\",\"2026-07-21\",\"2026-07-22\",\"2026-07-23\",\"2026-07-24\"],\"is_hs\":false},{\"bike_id\":\"bike_62\",\"dates\":[\"2026-07-13\",\"2026-07-14\",\"2026-07-15\",\"2026-07-16\",\"2026-07-17\",\"2026-07-18\",\"2026-07-19\",\"2026-07-20\",\"2026-07-21\",\"2026-07-22\",\"2026-07-23\",\"2026-07-24\"],\"is_hs\":false}]', 0, '2026-06-02 14:26:00', '2026-06-04 16:20:11'),
(145, 324, '2026-06-02 06:00:00', '2026-06-05', NULL, '2026-06-05', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 38.00, 0, NULL, NULL, NULL, 'paye', NULL, 'prise la veille fin de journée', '[{\"bike_id\":\"bike_102\",\"dates\":[\"2026-06-05\"],\"is_hs\":false}]', 9, '2026-06-03 15:24:10', '2026-06-06 07:04:14'),
(146, 325, '2026-06-03 10:05:00', '2026-06-20', NULL, '2026-06-27', 1, 'Fermes de Kerloury', NULL, NULL, 1, 'kerloury', NULL, '12h', 450.00, 1, 135.00, '2026-06-12', NULL, 'paye', NULL, '1m55\n1m71', '[{\"bike_id\":\"bike_52\",\"dates\":[\"2026-06-23\",\"2026-06-24\",\"2026-06-25\",\"2026-06-26\",\"2026-06-27\"],\"is_hs\":false},{\"bike_id\":\"bike_74\",\"dates\":[\"2026-06-20\",\"2026-06-21\",\"2026-06-22\",\"2026-06-23\",\"2026-06-24\",\"2026-06-25\",\"2026-06-26\",\"2026-06-27\"],\"is_hs\":false}]', 8, '2026-06-03 15:47:50', '2026-06-28 19:04:51'),
(147, 326, '2026-06-03 10:05:00', '2026-06-13', NULL, '2026-06-20', 1, '2 impasse du moulin Loguivy de la mer', NULL, '9h30', 1, '2 impasse du moulin Loguivy de la mer', NULL, '19h00', 426.00, 1, 128.00, '2026-06-10', NULL, 'paye', NULL, '1m70 et 1m75', '[{\"bike_id\":\"bike_103\",\"dates\":[\"2026-06-13\",\"2026-06-14\",\"2026-06-15\",\"2026-06-16\",\"2026-06-17\",\"2026-06-18\",\"2026-06-19\",\"2026-06-20\"],\"is_hs\":false},{\"bike_id\":\"bike_104\",\"dates\":[\"2026-06-13\",\"2026-06-14\",\"2026-06-15\",\"2026-06-16\",\"2026-06-17\",\"2026-06-18\",\"2026-06-19\",\"2026-06-20\"],\"is_hs\":false}]', 5, '2026-06-03 16:33:16', '2026-06-20 18:45:38'),
(149, 329, '2026-06-02 06:00:00', '2026-06-26', NULL, '2026-06-26', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 165.00, 0, NULL, NULL, NULL, 'annule', 'deja reservé sous le nom DE Calbiac', NULL, '[]', 26, '2026-06-04 08:56:28', '2026-06-19 09:21:45'),
(193, 412, '2026-06-23 08:55:00', '2026-06-23', NULL, '2026-06-24', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 20.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_107\",\"dates\":[\"2026-06-23\",\"2026-06-24\"],\"is_hs\":false}]', 0, '2026-06-23 13:45:43', '2026-06-25 13:20:14'),
(151, 334, '2026-06-05 12:20:00', '2026-06-13', NULL, '2026-06-16', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 140.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_74\",\"dates\":[\"2026-06-13\",\"2026-06-14\",\"2026-06-15\",\"2026-06-16\"],\"is_hs\":false}]', 10, '2026-06-05 15:01:49', '2026-06-16 15:40:10'),
(152, 336, '2026-06-06 07:51:00', '2026-06-07', NULL, '2026-06-07', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 38.00, 0, NULL, NULL, NULL, 'paye', NULL, 'retour lundi vers 10h', '[{\"bike_id\":\"bike_74\",\"dates\":[\"2026-06-07\"],\"is_hs\":false}]', 0, '2026-06-06 15:33:10', '2026-06-08 07:59:02'),
(153, 337, '2026-06-06 07:51:00', '2026-06-07', NULL, '2026-06-07', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 38.00, 0, NULL, NULL, NULL, 'paye', NULL, 'retour lundi vers 10h', '[{\"bike_id\":\"bike_92\",\"dates\":[\"2026-06-07\"],\"is_hs\":false}]', 5, '2026-06-06 15:39:02', '2026-06-08 07:40:07'),
(154, 338, '2026-06-08 18:41:00', '2026-06-15', NULL, '2026-06-19', 1, '1 Lotissement Martin\n\nPloubazlanec\n\n22620 France', NULL, '16h00', 1, '1 Lotissement Martin\n\nPloubazlanec\n\n22620 France', NULL, NULL, 301.00, 1, 91.00, '2026-06-11', NULL, 'paye', NULL, '1m74 et 1m64\n+ 1 remorque\n et si besoin un siège enfant en +(pas compté le prix)', '[{\"bike_id\":\"bike_101\",\"dates\":[\"2026-06-16\",\"2026-06-17\",\"2026-06-18\",\"2026-06-19\"],\"is_hs\":false},{\"bike_id\":\"bike_92\",\"dates\":[\"2026-06-15\",\"2026-06-16\",\"2026-06-17\",\"2026-06-18\",\"2026-06-19\"],\"is_hs\":false}]', 14, '2026-06-08 18:45:59', '2026-06-20 18:45:56'),
(155, 340, '2026-06-08 14:20:00', '2026-06-09', NULL, '2026-06-09', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 41.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_74\",\"dates\":[\"2026-06-09\"],\"is_hs\":false}]', 5, '2026-06-09 08:17:11', '2026-06-09 15:33:11'),
(156, 343, '2026-06-08 14:20:00', '2026-06-09', NULL, '2026-06-09', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 54.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_53\",\"dates\":[\"2026-06-09\"],\"is_hs\":false},{\"bike_id\":\"bike_54\",\"dates\":[\"2026-06-09\"],\"is_hs\":false}]', 15, '2026-06-09 12:19:22', '2026-06-09 17:13:35'),
(157, 344, '2026-06-09 07:51:00', '2026-06-13', NULL, '2026-06-14', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 76.00, 1, 23.00, '2026-06-09', NULL, 'paye', NULL, 'VTC 180 et 165', '[{\"bike_id\":\"bike_100\",\"dates\":[\"2026-06-13\",\"2026-06-14\"],\"is_hs\":false},{\"bike_id\":\"bike_66\",\"dates\":[\"2026-06-13\",\"2026-06-14\"],\"is_hs\":false}]', 2, '2026-06-09 12:29:41', '2026-06-14 12:56:39'),
(166, 368, '2026-06-13 12:02:00', '2026-06-14', NULL, '2026-06-14', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 38.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_50\",\"start_date\":\"2026-06-14\",\"end_date\":\"2026-06-14\",\"dates\":[\"2026-06-14\"],\"is_hs\":false}]', 0, '2026-06-13 15:17:37', '2026-06-13 15:17:37'),
(158, 345, '2026-06-08 14:20:00', '2026-06-09', NULL, '2026-06-10', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 52.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_67\",\"dates\":[\"2026-06-09\",\"2026-06-10\"],\"is_hs\":false}]', 0, '2026-06-09 12:37:28', '2026-06-12 14:09:39'),
(159, 343, '2026-06-08 14:20:00', '2026-06-12', NULL, '2026-06-12', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 54.00, 0, NULL, NULL, NULL, 'paye', NULL, 'a partir de 11h', '[{\"bike_id\":\"bike_53\",\"dates\":[\"2026-06-12\"],\"is_hs\":false},{\"bike_id\":\"bike_54\",\"dates\":[\"2026-06-12\"],\"is_hs\":false}]', 15, '2026-06-09 15:01:30', '2026-06-12 14:46:27'),
(160, 349, '2026-06-08 14:20:00', '2026-06-10', NULL, '2026-06-10', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 76.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_53\",\"dates\":[\"2026-06-10\"],\"is_hs\":false},{\"bike_id\":\"bike_54\",\"dates\":[\"2026-06-10\"],\"is_hs\":false}]', 13, '2026-06-09 15:06:59', '2026-06-10 14:45:55'),
(162, 355, '2026-06-10 08:50:00', '2026-06-11', NULL, '2026-06-11', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 76.00, 0, NULL, NULL, NULL, 'en_cours', NULL, NULL, '[{\"bike_id\":\"bike_102\",\"dates\":[\"2026-06-11\"],\"is_hs\":false},{\"bike_id\":\"bike_103\",\"dates\":[\"2026-06-11\"],\"is_hs\":false}]', 0, '2026-06-10 13:30:27', '2026-06-11 08:57:32'),
(161, 350, '2026-06-08 14:20:00', '2026-06-09', NULL, '2026-06-12', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'reserve', NULL, NULL, '[{\"bike_id\":\"bike_82\",\"dates\":[\"2026-06-09\",\"2026-06-10\",\"2026-06-11\",\"2026-06-12\"],\"is_hs\":false}]', 1, '2026-06-09 15:12:09', '2026-06-09 15:12:19'),
(163, 356, '2026-06-10 08:50:00', '2026-06-11', NULL, '2026-06-11', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 76.00, 0, NULL, NULL, NULL, 'paye', NULL, '10h environ atelier', '[{\"bike_id\":\"bike_74\",\"dates\":[\"2026-06-11\"],\"is_hs\":false},{\"bike_id\":\"bike_59\",\"dates\":[\"2026-06-11\"],\"is_hs\":false}]', 7, '2026-06-10 15:31:31', '2026-06-11 15:38:55'),
(164, 358, '2026-06-11 06:39:00', '2026-06-28', NULL, '2026-07-03', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 380.00, 1, 138.00, '2026-06-15', NULL, 'en_cours', NULL, '2 VAE 1m60x2\nViennent chercher le 27/06 en fin d\'aprèm', '[{\"bike_id\":\"bike_96\",\"dates\":[\"2026-06-28\",\"2026-06-29\",\"2026-06-30\",\"2026-07-01\",\"2026-07-02\",\"2026-07-03\"],\"is_hs\":false},{\"bike_id\":\"bike_103\",\"dates\":[\"2026-06-28\",\"2026-06-29\",\"2026-06-30\",\"2026-07-01\",\"2026-07-02\",\"2026-07-03\"],\"is_hs\":false}]', 16, '2026-06-11 09:06:26', '2026-06-29 12:26:52'),
(165, 362, '2026-06-12 06:45:00', '2026-07-07', NULL, '2026-07-11', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 590.00, 1, 177.00, '2026-06-15', NULL, 'reserve', NULL, 'ELISE / adulte femme / électrique\nTaille 1m56\n\nARMAND / 11 ans / électrique\nTaille 1m45.\nJonathan m\'a parlé d\'un modèle pliable électrique qui pourrait correspondre.\n\nHADRIEN / 14 ans / électrique\nTaille 1m52 \n\nJB / adulte homme / classique\nTaille 1m72', '[{\"bike_id\":\"bike_96\",\"start_date\":\"2026-07-07\",\"end_date\":\"2026-07-11\",\"dates\":[\"2026-07-07\",\"2026-07-08\",\"2026-07-09\",\"2026-07-10\",\"2026-07-11\"],\"is_hs\":false},{\"bike_id\":\"bike_102\",\"start_date\":\"2026-07-07\",\"end_date\":\"2026-07-11\",\"dates\":[\"2026-07-07\",\"2026-07-08\",\"2026-07-09\",\"2026-07-10\",\"2026-07-11\"],\"is_hs\":false},{\"bike_id\":\"bike_74\",\"start_date\":\"2026-07-07\",\"end_date\":\"2026-07-11\",\"dates\":[\"2026-07-07\",\"2026-07-08\",\"2026-07-09\",\"2026-07-10\",\"2026-07-11\"],\"is_hs\":false},{\"bike_id\":\"bike_56\",\"start_date\":\"2026-07-07\",\"end_date\":\"2026-07-11\",\"dates\":[\"2026-07-07\",\"2026-07-08\",\"2026-07-09\",\"2026-07-10\",\"2026-07-11\"],\"is_hs\":false}]', 14, '2026-06-12 06:49:35', '2026-06-18 12:59:03'),
(184, 400, '2026-06-20 07:04:00', '2026-06-20', NULL, '2026-06-25', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 299.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_60\",\"dates\":[\"2026-06-20\",\"2026-06-21\",\"2026-06-22\",\"2026-06-23\",\"2026-06-24\",\"2026-06-25\"],\"is_hs\":false},{\"bike_id\":\"bike_63\",\"dates\":[\"2026-06-20\",\"2026-06-21\",\"2026-06-22\",\"2026-06-23\",\"2026-06-24\",\"2026-06-25\"],\"is_hs\":false},{\"bike_id\":\"bike_79\",\"dates\":[\"2026-06-20\",\"2026-06-21\",\"2026-06-22\",\"2026-06-23\",\"2026-06-24\",\"2026-06-25\"],\"is_hs\":false}]', 16, '2026-06-20 09:46:47', '2026-06-25 15:46:59'),
(167, 369, '2026-06-13 12:02:00', '2026-06-23', NULL, '2026-06-26', 0, NULL, NULL, NULL, 1, 'plougrescant\n7 kervoazec hent Tourot 22820', NULL, NULL, 430.00, 0, NULL, NULL, NULL, 'paye', NULL, '1.76, 1.82, 1.76\nsacoche a voir\n1 retroviseur cassé = 10euros', '[{\"bike_id\":\"bike_59\",\"dates\":[\"2026-06-23\",\"2026-06-24\",\"2026-06-25\",\"2026-06-26\"],\"is_hs\":false},{\"bike_id\":\"bike_94\",\"dates\":[\"2026-06-23\",\"2026-06-24\",\"2026-06-25\",\"2026-06-26\"],\"is_hs\":false},{\"bike_id\":\"bike_51\",\"dates\":[\"2026-06-23\",\"2026-06-24\",\"2026-06-25\",\"2026-06-26\"],\"is_hs\":false}]', 1, '2026-06-15 14:07:13', '2026-06-26 15:56:42'),
(168, 371, '2026-06-16 11:44:00', '2026-06-19', NULL, '2026-06-21', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 118.00, 0, NULL, NULL, NULL, 'reserve', NULL, '1m82 et 1m71', '[{\"bike_id\":\"bike_100\",\"dates\":[\"2026-06-19\",\"2026-06-20\",\"2026-06-21\"],\"is_hs\":false},{\"bike_id\":\"bike_66\",\"dates\":[\"2026-06-19\",\"2026-06-20\",\"2026-06-21\"],\"is_hs\":false}]', 0, '2026-06-16 11:47:10', '2026-06-17 07:01:51'),
(169, 374, '2026-06-16 12:02:00', '2026-06-17', NULL, '2026-06-20', 1, '5 minard Plouezec', NULL, '10h', 1, '5 minard Plouezec', NULL, '18h15', 174.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_64\",\"dates\":[\"2026-06-17\",\"2026-06-18\",\"2026-06-19\",\"2026-06-20\"],\"is_hs\":false},{\"bike_id\":\"bike_67\",\"dates\":[\"2026-06-17\",\"2026-06-18\",\"2026-06-19\",\"2026-06-20\"],\"is_hs\":false}]', 9, '2026-06-16 12:45:19', '2026-06-20 17:10:52'),
(170, 377, '2026-06-17 07:00:00', '2026-06-17', NULL, '2026-06-17', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 58.00, 0, NULL, NULL, NULL, 'en_cours', NULL, NULL, '[{\"bike_id\":\"bike_74\",\"start_date\":\"2026-06-17\",\"end_date\":\"2026-06-17\",\"dates\":[\"2026-06-17\"],\"is_hs\":false},{\"bike_id\":\"bike_65\",\"start_date\":\"2026-06-17\",\"end_date\":\"2026-06-17\",\"dates\":[\"2026-06-17\"],\"is_hs\":false}]', 13, '2026-06-17 07:29:46', '2026-06-17 07:29:46'),
(171, 380, '2026-06-17 07:00:00', '2026-06-18', NULL, '2026-06-18', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 76.00, 0, NULL, NULL, NULL, 'en_cours', NULL, '2 potence hautes\n2 porte gourdes', '[{\"bike_id\":\"bike_94\",\"dates\":[\"2026-06-18\"],\"is_hs\":false},{\"bike_id\":\"bike_97\",\"dates\":[\"2026-06-18\"],\"is_hs\":false}]', 1, '2026-06-17 12:57:15', '2026-06-18 09:04:58'),
(172, 383, '2026-06-17 12:40:00', '2026-07-11', NULL, '2026-07-25', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1032.00, 1, 310.00, '2026-06-23', NULL, 'reserve', NULL, '1;88m, 1,74m et 1,66m', '[{\"bike_id\":\"bike_105\",\"dates\":[\"2026-07-11\",\"2026-07-12\",\"2026-07-13\",\"2026-07-14\",\"2026-07-15\",\"2026-07-16\",\"2026-07-17\",\"2026-07-18\",\"2026-07-19\",\"2026-07-20\",\"2026-07-21\",\"2026-07-22\",\"2026-07-23\",\"2026-07-24\",\"2026-07-25\"],\"is_hs\":false},{\"bike_id\":\"bike_57\",\"dates\":[\"2026-07-11\",\"2026-07-12\",\"2026-07-13\",\"2026-07-14\",\"2026-07-15\",\"2026-07-16\",\"2026-07-17\",\"2026-07-18\",\"2026-07-19\",\"2026-07-20\",\"2026-07-21\",\"2026-07-22\",\"2026-07-23\",\"2026-07-24\",\"2026-07-25\"],\"is_hs\":false},{\"bike_id\":\"bike_94\",\"dates\":[\"2026-07-11\",\"2026-07-12\",\"2026-07-13\",\"2026-07-14\",\"2026-07-15\",\"2026-07-16\",\"2026-07-17\",\"2026-07-18\",\"2026-07-19\",\"2026-07-20\",\"2026-07-21\",\"2026-07-22\",\"2026-07-23\",\"2026-07-24\",\"2026-07-25\"],\"is_hs\":false}]', 3, '2026-06-17 14:03:32', '2026-06-23 15:06:34'),
(173, 384, '2026-06-17 14:35:00', '2026-06-16', NULL, '2026-06-18', 1, '15 cotes de terre neuvas Brehec', NULL, NULL, 1, '15 cotes de terre neuvas Brehec', NULL, NULL, 150.00, 0, NULL, NULL, NULL, 'en_cours', NULL, 'récupération vendredi matin 8h45', '[{\"bike_id\":\"bike_105\",\"start_date\":\"2026-06-16\",\"end_date\":\"2026-06-18\",\"dates\":[\"2026-06-16\",\"2026-06-17\",\"2026-06-18\"],\"is_hs\":false}]', 0, '2026-06-17 14:40:50', '2026-06-17 14:40:50'),
(174, 385, '2026-06-17 12:40:00', '2026-07-12', NULL, '2026-07-13', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 328.00, 1, 99.00, '2026-06-22', NULL, 'reserve', NULL, 'un vélo électrique adulte\nun vélo pour une jeune fille de 11 ans (1,4m)\nun autre pour une jeune fille de 9 ans (1,35 m)\nle vélo cargo que vous pouvez laisser en format deux places banquette', '[{\"bike_id\":\"bike_101\",\"dates\":[\"2026-07-12\",\"2026-07-13\"],\"is_hs\":false},{\"bike_id\":\"bike_97\",\"dates\":[\"2026-07-12\",\"2026-07-13\"],\"is_hs\":false},{\"bike_id\":\"bike_50\",\"dates\":[\"2026-07-12\",\"2026-07-13\"],\"is_hs\":false},{\"bike_id\":\"bike_48\",\"dates\":[\"2026-07-12\",\"2026-07-13\"],\"is_hs\":false}]', 9, '2026-06-17 21:43:24', '2026-06-23 15:06:55'),
(178, 8, '2026-06-18 12:50:00', '2026-06-18', NULL, '2026-06-29', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'reserve', NULL, 'pret le temps des travaux', '[{\"bike_id\":\"bike_57\",\"dates\":[\"2026-06-18\",\"2026-06-19\",\"2026-06-20\",\"2026-06-21\",\"2026-06-22\",\"2026-06-23\",\"2026-06-24\",\"2026-06-25\",\"2026-06-26\",\"2026-06-27\",\"2026-06-28\",\"2026-06-29\"],\"is_hs\":false}]', 9, '2026-06-18 12:56:01', '2026-06-28 19:05:30'),
(175, 386, '2026-06-18 09:52:00', '2026-06-20', NULL, '2026-06-20', 1, '9 lieu dit kersaux \npleudaniel', NULL, NULL, 1, '9 lieu dit kersaux \npleudaniel', NULL, NULL, 96.00, 0, NULL, NULL, NULL, 'reserve', NULL, '1.63-1.70-1.70\nlivraison à calculer', '[{\"bike_id\":\"bike_52\",\"start_date\":\"2026-06-20\",\"end_date\":\"2026-06-20\",\"dates\":[\"2026-06-20\"],\"is_hs\":false},{\"bike_id\":\"bike_96\",\"start_date\":\"2026-06-20\",\"end_date\":\"2026-06-20\",\"dates\":[\"2026-06-20\"],\"is_hs\":false},{\"bike_id\":\"bike_51\",\"start_date\":\"2026-06-20\",\"end_date\":\"2026-06-20\",\"dates\":[\"2026-06-20\"],\"is_hs\":false}]', 7, '2026-06-18 10:03:15', '2026-06-18 10:03:15'),
(176, 388, '2026-06-18 12:50:00', '2026-06-18', NULL, '2026-06-20', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'reserve', NULL, 'prêt de vélo contre vélo à l\'atelier', '[{\"bike_id\":\"bike_56\",\"start_date\":\"2026-06-18\",\"end_date\":\"2026-06-20\",\"dates\":[\"2026-06-18\",\"2026-06-19\",\"2026-06-20\"],\"is_hs\":false}]', 13, '2026-06-18 12:51:26', '2026-06-18 12:51:26'),
(177, 389, '2026-06-18 12:49:00', '2026-08-08', NULL, '2026-08-10', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 220.00, 1, 66.00, '2026-06-23', NULL, 'reserve', NULL, '1 femme, 1m65\n1 homme, 1m70', '[{\"bike_id\":\"bike_56\",\"dates\":[\"2026-08-08\",\"2026-08-09\",\"2026-08-10\"],\"is_hs\":false},{\"bike_id\":\"bike_85\",\"dates\":[\"2026-08-08\",\"2026-08-09\",\"2026-08-10\"],\"is_hs\":false}]', 0, '2026-06-18 12:53:41', '2026-06-29 14:25:29'),
(179, 390, '2026-06-18 14:09:00', '2026-06-23', NULL, '2026-06-23', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'annule', 'annulé pour cause de chaleurs', '1.70;1.80\nréservation ouest france\nenvoie des infos gps par mail', '[{\"bike_id\":\"bike_51\",\"dates\":[\"2026-06-23\"],\"is_hs\":false},{\"bike_id\":\"bike_57\",\"dates\":[\"2026-06-23\"],\"is_hs\":false}]', 0, '2026-06-18 14:50:07', '2026-06-23 15:55:50'),
(180, 391, '2026-06-19 07:48:00', '2026-06-30', NULL, '2026-07-02', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 203.00, 1, 61.00, '2026-06-22', NULL, 'reserve', NULL, '1.72m, notre petit 1.05m et moi-même 1.68m.\n+ siège bébé', '[{\"bike_id\":\"bike_84\",\"dates\":[\"2026-06-30\",\"2026-07-01\",\"2026-07-02\"],\"is_hs\":false},{\"bike_id\":\"bike_67\",\"dates\":[\"2026-06-30\",\"2026-07-01\",\"2026-07-02\"],\"is_hs\":false},{\"bike_id\":\"bike_62\",\"dates\":[\"2026-06-30\",\"2026-07-01\",\"2026-07-02\"],\"is_hs\":false}]', 3, '2026-06-19 07:53:50', '2026-06-23 15:27:18'),
(194, 417, '2026-06-24 07:31:00', '2026-06-24', NULL, '2026-06-25', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 40.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_66\",\"start_date\":\"2026-06-24\",\"end_date\":\"2026-06-25\",\"dates\":[\"2026-06-24\",\"2026-06-25\"],\"is_hs\":false}]', 0, '2026-06-24 08:15:25', '2026-06-24 08:15:25'),
(183, 399, '2026-06-20 07:04:00', '2026-06-30', NULL, '2026-07-02', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'reserve', NULL, NULL, '[{\"bike_id\":\"bike_102\",\"dates\":[\"2026-06-30\",\"2026-07-01\",\"2026-07-02\"],\"is_hs\":false}]', 5, '2026-06-20 07:32:45', '2026-06-24 07:32:19'),
(181, 392, '2026-06-19 06:43:00', '2026-06-19', NULL, '2026-06-19', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 25.00, 0, NULL, NULL, NULL, 'paye', NULL, 'demi', '[{\"bike_id\":\"bike_96\",\"dates\":[\"2026-06-19\"],\"is_hs\":false}]', 0, '2026-06-19 08:35:27', '2026-06-19 15:48:09'),
(182, 398, '2026-06-19 15:45:00', '2026-06-20', NULL, '2026-06-20', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 64.00, 0, NULL, NULL, NULL, 'paye', NULL, 'part\nretour dimanche', '[{\"bike_id\":\"bike_48\",\"dates\":[\"2026-06-20\"],\"is_hs\":false},{\"bike_id\":\"bike_54\",\"dates\":[\"2026-06-20\"],\"is_hs\":false}]', 0, '2026-06-19 16:15:23', '2026-06-20 10:19:44'),
(185, 401, '2026-06-20 07:04:00', '2026-06-20', NULL, '2026-06-20', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 50.00, 0, NULL, NULL, NULL, 'paye', NULL, 'demie', '[{\"bike_id\":\"bike_85\",\"dates\":[\"2026-06-20\"],\"is_hs\":false},{\"bike_id\":\"bike_92\",\"dates\":[\"2026-06-20\"],\"is_hs\":false}]', 25, '2026-06-20 12:30:35', '2026-06-20 15:56:29'),
(186, 402, '2026-06-20 08:35:00', '2026-06-20', NULL, '2026-06-20', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 68.00, 0, NULL, NULL, NULL, 'paye', NULL, 'retour dimanche', '[{\"bike_id\":\"bike_70\",\"dates\":[\"2026-06-20\"],\"is_hs\":false},{\"bike_id\":\"bike_106\",\"dates\":[\"2026-06-20\"],\"is_hs\":false}]', 4, '2026-06-20 13:29:41', '2026-06-20 15:27:46'),
(187, 404, '2026-06-20 13:28:00', '2026-06-21', NULL, '2026-06-24', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 119.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_55\",\"dates\":[\"2026-06-21\",\"2026-06-22\",\"2026-06-23\",\"2026-06-24\"],\"is_hs\":false}]', 9, '2026-06-20 14:47:40', '2026-06-24 14:56:58'),
(188, 405, '2026-06-20 08:30:00', '2026-08-08', NULL, '2026-08-22', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 611.00, 1, 184.00, '2026-06-21', NULL, 'reserve', NULL, '2 VAE Sb', '[{\"bike_id\":\"bike_50\",\"dates\":[\"2026-08-08\",\"2026-08-09\",\"2026-08-10\",\"2026-08-11\",\"2026-08-12\",\"2026-08-13\",\"2026-08-14\",\"2026-08-15\",\"2026-08-16\",\"2026-08-17\",\"2026-08-18\",\"2026-08-19\",\"2026-08-20\",\"2026-08-21\",\"2026-08-22\"],\"is_hs\":false},{\"bike_id\":\"bike_74\",\"dates\":[\"2026-08-08\",\"2026-08-09\",\"2026-08-10\",\"2026-08-11\",\"2026-08-12\",\"2026-08-13\",\"2026-08-14\",\"2026-08-15\",\"2026-08-16\",\"2026-08-17\",\"2026-08-18\",\"2026-08-19\",\"2026-08-20\",\"2026-08-21\",\"2026-08-22\"],\"is_hs\":false}]', 2, '2026-06-20 15:28:48', '2026-06-23 15:07:24'),
(189, 406, '2026-06-20 08:30:00', '2026-08-09', '2026-08-08', '2026-08-20', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 317.00, 1, 95.00, NULL, NULL, 'en_attente_acompte', NULL, 'VAE 1m71\nRécupère la veille à 18h00 (08/08)', '[{\"bike_id\":\"bike_53\",\"dates\":[\"2026-08-09\",\"2026-08-10\",\"2026-08-11\",\"2026-08-12\",\"2026-08-13\",\"2026-08-14\",\"2026-08-15\",\"2026-08-16\",\"2026-08-17\",\"2026-08-18\",\"2026-08-19\",\"2026-08-20\"],\"is_hs\":false}]', 4, '2026-06-20 16:43:29', '2026-06-29 14:24:08'),
(190, 408, '2026-06-22 09:59:00', '2026-06-22', NULL, '2026-06-22', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 50.00, 0, NULL, NULL, NULL, 'paye', NULL, '1.55 - 1.80', '[{\"bike_id\":\"bike_96\",\"dates\":[\"2026-06-22\"],\"is_hs\":false},{\"bike_id\":\"bike_59\",\"dates\":[\"2026-06-22\"],\"is_hs\":false}]', 16, '2026-06-22 10:01:25', '2026-06-22 15:10:20'),
(191, 409, '2026-06-22 16:16:00', '2026-08-13', NULL, '2026-08-15', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 160.00, 1, 48.00, '2026-06-26', NULL, 'reserve', NULL, 'Nous souhaiterions 2 VTC musculaires, 1homme (1m71) et 1 femme (1m65), 1 siège enfant ainsi qu\'1 vélo 20 pouces pour ma fille de 8 ans.', '[{\"bike_id\":\"bike_93\",\"start_date\":\"2026-08-13\",\"end_date\":\"2026-08-15\",\"dates\":[\"2026-08-13\",\"2026-08-14\",\"2026-08-15\"],\"is_hs\":false},{\"bike_id\":\"bike_67\",\"start_date\":\"2026-08-13\",\"end_date\":\"2026-08-15\",\"dates\":[\"2026-08-13\",\"2026-08-14\",\"2026-08-15\"],\"is_hs\":false},{\"bike_id\":\"bike_62\",\"start_date\":\"2026-08-13\",\"end_date\":\"2026-08-15\",\"dates\":[\"2026-08-13\",\"2026-08-14\",\"2026-08-15\"],\"is_hs\":false}]', 0, '2026-06-22 16:20:27', '2026-06-29 13:02:54'),
(192, 410, '2026-06-22 15:23:00', '2026-06-22', NULL, '2026-06-22', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 32.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_54\",\"dates\":[\"2026-06-22\"],\"is_hs\":false}]', 13, '2026-06-22 16:33:45', '2026-06-22 16:34:28'),
(198, 421, '2026-06-24 08:07:00', '2026-07-19', '2026-07-18', '2026-07-21', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 110.00, 1, 33.00, '2026-06-24', NULL, 'reserve', NULL, 'Dates de location: du mardi 18 août (en fin de journée) au vendredi 21 août (en fin de journée)\nType de vélo: 1 vélo électrique (sans barre horizontale si possible)\nTaille du cycliste: 1,68m', '[{\"bike_id\":\"bike_85\",\"dates\":[\"2026-07-19\",\"2026-07-20\",\"2026-07-21\"],\"is_hs\":false}]', 4, '2026-06-24 18:00:58', '2026-06-25 06:31:03'),
(195, 418, '2026-06-24 08:07:00', '2026-08-02', NULL, '2026-08-03', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 152.00, 1, 46.00, '2026-06-25', NULL, 'reserve', NULL, 'VAE Pour Joëlle :taille 1, 55 \nVAE Pour Pierre : taille 1, 80', '[{\"bike_id\":\"bike_57\",\"dates\":[\"2026-08-02\",\"2026-08-03\"],\"is_hs\":false},{\"bike_id\":\"bike_74\",\"dates\":[\"2026-08-02\",\"2026-08-03\"],\"is_hs\":false}]', 0, '2026-06-24 08:25:49', '2026-06-26 08:07:26'),
(205, 429, '2026-06-26 06:52:00', '2026-06-26', NULL, '2026-06-26', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 58.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_53\",\"dates\":[\"2026-06-26\"],\"is_hs\":false},{\"bike_id\":\"bike_60\",\"dates\":[\"2026-06-26\"],\"is_hs\":false}]', 9, '2026-06-26 08:19:44', '2026-06-26 13:38:05'),
(196, 419, '2026-06-24 07:31:00', '2026-06-24', NULL, '2026-06-24', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'reserve', NULL, NULL, '[{\"bike_id\":\"bike_54\",\"start_date\":\"2026-06-24\",\"end_date\":\"2026-06-24\",\"dates\":[\"2026-06-24\"],\"is_hs\":false}]', 0, '2026-06-24 09:46:41', '2026-06-24 09:46:41'),
(197, 420, '2026-06-24 13:33:00', '2026-06-25', NULL, '2026-06-26', 1, 'hôtel de la baie paimpol', NULL, NULL, 0, NULL, NULL, NULL, 64.00, 0, NULL, NULL, NULL, 'paye', NULL, 'part', '[{\"bike_id\":\"bike_98\",\"dates\":[\"2026-06-25\",\"2026-06-26\"],\"is_hs\":false}]', 0, '2026-06-24 13:51:24', '2026-06-28 19:05:21'),
(199, 423, '2026-06-25 06:42:00', '2026-06-25', NULL, '2026-06-25', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 48.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_48\",\"dates\":[\"2026-06-25\"],\"is_hs\":false},{\"bike_id\":\"bike_100\",\"dates\":[\"2026-06-25\"],\"is_hs\":false}]', 0, '2026-06-25 07:58:36', '2026-06-25 14:28:48'),
(204, 289, '2026-06-25 13:20:00', '2026-06-25', NULL, '2026-06-29', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'reserve', NULL, 'courtoisie, velo en garanti a l\'atelier', '[{\"bike_id\":\"bike_92\",\"start_date\":\"2026-06-25\",\"end_date\":\"2026-06-29\",\"dates\":[\"2026-06-25\",\"2026-06-26\",\"2026-06-27\",\"2026-06-28\",\"2026-06-29\"],\"is_hs\":false}]', 0, '2026-06-25 15:14:23', '2026-06-25 15:14:23'),
(200, 424, '2026-06-25 06:42:00', '2026-06-26', NULL, '2026-06-26', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 40.00, 0, NULL, NULL, NULL, 'en_cours', NULL, '1.65 - 1.75', '[{\"bike_id\":\"bike_66\",\"dates\":[\"2026-06-26\"],\"is_hs\":false},{\"bike_id\":\"bike_63\",\"dates\":[\"2026-06-26\"],\"is_hs\":false}]', 11, '2026-06-25 08:31:05', '2026-06-26 08:28:39'),
(201, 425, '2026-06-25 06:42:00', '2026-06-25', NULL, '2026-06-25', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 25.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_96\",\"dates\":[\"2026-06-25\"],\"is_hs\":false}]', 5, '2026-06-25 08:45:47', '2026-06-25 15:55:01'),
(202, 427, '2026-06-25 06:42:00', '2026-08-04', NULL, '2026-08-08', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 330.00, 1, 100.00, NULL, NULL, 'en_attente_acompte', NULL, NULL, '[{\"bike_id\":\"bike_96\",\"dates\":[\"2026-08-04\",\"2026-08-05\",\"2026-08-06\",\"2026-08-07\",\"2026-08-08\"],\"is_hs\":false},{\"bike_id\":\"bike_94\",\"dates\":[\"2026-08-04\",\"2026-08-05\",\"2026-08-06\",\"2026-08-07\",\"2026-08-08\"],\"is_hs\":false}]', 6, '2026-06-25 13:15:13', '2026-06-25 13:18:27'),
(203, 428, '2026-06-25 13:20:00', '2026-06-25', NULL, '2026-06-30', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, 'reserve', NULL, 'velo de courtoisie', '[{\"bike_id\":\"bike_64\",\"dates\":[\"2026-06-25\",\"2026-06-26\",\"2026-06-27\",\"2026-06-28\",\"2026-06-29\",\"2026-06-30\"],\"is_hs\":false}]', 0, '2026-06-25 14:18:55', '2026-06-26 16:06:08'),
(206, 430, '2026-06-26 08:06:00', '2026-07-11', NULL, '2026-07-14', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 55.00, 1, 17.00, NULL, NULL, 'en_attente_acompte', NULL, NULL, '[{\"bike_id\":\"bike_78\",\"start_date\":\"2026-07-11\",\"end_date\":\"2026-07-14\",\"dates\":[\"2026-07-11\",\"2026-07-12\",\"2026-07-13\",\"2026-07-14\"],\"is_hs\":false}]', 3, '2026-06-26 12:22:47', '2026-06-26 12:22:47');
INSERT INTO `reservations` (`id`, `client_id`, `date_contact`, `date_reservation`, `date_recuperation`, `date_retour`, `livraison_necessaire`, `adresse_livraison`, `contact_livraison`, `creneau_livraison`, `recuperation_necessaire`, `adresse_recuperation`, `contact_recuperation`, `creneau_recuperation`, `prix_total_ttc`, `acompte_demande`, `acompte_montant`, `acompte_paye_le`, `paiement_final_le`, `statut`, `raison_annulation`, `commentaires`, `selection`, `color`, `created_at`, `updated_at`) VALUES
(207, 429, '2026-06-26 06:52:00', '2026-06-27', NULL, '2026-06-27', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 50.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_96\",\"dates\":[\"2026-06-27\"],\"is_hs\":false},{\"bike_id\":\"bike_53\",\"dates\":[\"2026-06-27\"],\"is_hs\":false}]', 9, '2026-06-27 09:56:11', '2026-06-27 13:53:14'),
(208, 436, '2026-06-26 06:52:00', '2026-06-28', NULL, '2026-06-29', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 64.00, 0, NULL, NULL, NULL, 'en_cours', NULL, NULL, '[{\"bike_id\":\"bike_51\",\"dates\":[\"2026-06-28\",\"2026-06-29\"],\"is_hs\":false}]', 0, '2026-06-27 15:01:01', '2026-06-27 16:04:12'),
(209, 429, '2026-06-29 07:22:00', '2026-06-29', NULL, '2026-06-29', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 64.00, 0, NULL, NULL, NULL, 'paye', NULL, NULL, '[{\"bike_id\":\"bike_50\",\"dates\":[\"2026-06-29\"],\"is_hs\":false},{\"bike_id\":\"bike_53\",\"dates\":[\"2026-06-29\"],\"is_hs\":false}]', 9, '2026-06-29 08:23:18', '2026-06-29 13:28:40'),
(210, 438, '2026-06-29 07:22:00', '2026-06-29', NULL, '2026-06-30', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 126.00, 0, NULL, NULL, NULL, 'en_cours', NULL, NULL, '[{\"bike_id\":\"bike_104\",\"start_date\":\"2026-06-29\",\"end_date\":\"2026-06-30\",\"dates\":[\"2026-06-29\",\"2026-06-30\"],\"is_hs\":false},{\"bike_id\":\"bike_55\",\"start_date\":\"2026-06-29\",\"end_date\":\"2026-06-30\",\"dates\":[\"2026-06-29\",\"2026-06-30\"],\"is_hs\":false}]', 23, '2026-06-29 12:53:51', '2026-06-29 12:53:51'),
(211, 406, '2026-06-29 12:55:00', '2026-08-20', '2026-08-08', '2026-08-20', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 317.00, 0, NULL, NULL, NULL, 'annule', 'doublons en saisie', '1m71 récupération la veille à 18h00', '[]', 0, '2026-06-29 12:59:40', '2026-06-29 13:48:21'),
(212, 439, '2026-06-29 14:14:00', '2026-08-01', NULL, '2026-08-01', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 10.00, 0, NULL, NULL, NULL, 'annule', 'exemple data', NULL, '[{\"bike_id\":\"bike_103\",\"dates\":[\"2026-08-01\"],\"is_hs\":false},{\"bike_id\":\"bike_104\",\"dates\":[\"2026-08-01\"],\"is_hs\":false}]', 0, '2026-06-29 14:22:33', '2026-06-29 14:23:47');

-- --------------------------------------------------------

--
-- Structure de la table `reservation_items`
--

CREATE TABLE `reservation_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reservation_id` bigint(20) UNSIGNED NOT NULL,
  `bike_type_id` varchar(50) NOT NULL,
  `quantite` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reservation_items`
--

INSERT INTO `reservation_items` (`id`, `reservation_id`, `bike_type_id`, `quantite`, `created_at`, `updated_at`) VALUES
(6, 1, 'VTC_lb', 1, '2026-02-17 09:33:22', '2026-02-17 09:33:22'),
(61, 2, 'VAE_sb', 1, '2026-02-24 09:44:34', '2026-02-24 09:44:34'),
(62, 3, 'VAE_mb', 2, '2026-02-24 10:01:08', '2026-02-24 10:01:08'),
(60, 4, 'VAE_sb', 1, '2026-02-24 09:44:27', '2026-02-24 09:44:27'),
(101, 5, 'VAE_mb', 1, '2026-03-09 09:22:49', '2026-03-09 09:22:49'),
(1428, 6, 'VAE_lb', 1, '2026-06-29 14:31:09', '2026-06-29 14:31:09'),
(1427, 6, 'VAE_mb', 1, '2026-06-29 14:31:09', '2026-06-29 14:31:09'),
(65, 8, 'VAE_mb', 2, '2026-02-24 13:37:33', '2026-02-24 13:37:33'),
(64, 8, 'VAE_sb', 1, '2026-02-24 13:37:33', '2026-02-24 13:37:33'),
(34, 7, 'VAE_mb', 1, '2026-02-21 14:16:21', '2026-02-21 14:16:21'),
(84, 9, 'VAE_mb', 1, '2026-02-28 14:06:25', '2026-02-28 14:06:25'),
(36, 10, 'VAE_mb', 2, '2026-02-21 14:57:51', '2026-02-21 14:57:51'),
(59, 11, 'VAE_sb', 1, '2026-02-24 09:43:57', '2026-02-24 09:43:57'),
(56, 12, 'VAE_sb', 1, '2026-02-24 09:41:17', '2026-02-24 09:41:17'),
(53, 13, 'VAE_sb', 1, '2026-02-24 09:40:33', '2026-02-24 09:40:33'),
(54, 14, 'VAE_sb', 1, '2026-02-24 09:40:49', '2026-02-24 09:40:49'),
(55, 15, 'VAE_sb', 1, '2026-02-24 09:41:02', '2026-02-24 09:41:02'),
(168, 16, 'VTC_mh', 1, '2026-03-30 08:48:09', '2026-03-30 08:48:09'),
(67, 17, 'accessoires_mb', 1, '2026-02-27 18:16:56', '2026-02-27 18:16:56'),
(95, 18, 'accessoires_mb', 1, '2026-03-05 14:17:49', '2026-03-05 14:17:49'),
(86, 19, 'VTC_sh', 1, '2026-03-01 12:35:00', '2026-03-01 12:35:00'),
(99, 20, 'accessoires_mb', 2, '2026-03-07 08:01:13', '2026-03-07 08:01:13'),
(98, 20, 'VAE_mb', 1, '2026-03-07 08:01:13', '2026-03-07 08:01:13'),
(97, 20, 'VAE_sb', 3, '2026-03-07 08:01:13', '2026-03-07 08:01:13'),
(118, 21, 'VAE_mb', 1, '2026-03-17 14:41:20', '2026-03-17 14:41:20'),
(93, 22, 'VTC_sh', 1, '2026-03-05 10:35:45', '2026-03-05 10:35:45'),
(100, 23, 'VAE_mb', 1, '2026-03-08 18:03:15', '2026-03-08 18:03:15'),
(215, 24, 'VAE_mb', 1, '2026-04-14 08:28:12', '2026-04-14 08:28:12'),
(104, 25, 'VAE_mb', 1, '2026-03-09 09:26:21', '2026-03-09 09:26:21'),
(1423, 26, 'VAE_lb', 1, '2026-06-29 14:28:29', '2026-06-29 14:28:29'),
(1422, 26, 'VAE_sb', 1, '2026-06-29 14:28:29', '2026-06-29 14:28:29'),
(1407, 27, 'VAE_lh', 1, '2026-06-29 14:12:29', '2026-06-29 14:12:29'),
(1406, 27, 'VAE_mb', 1, '2026-06-29 14:12:29', '2026-06-29 14:12:29'),
(203, 28, 'VAE_sb', 1, '2026-04-10 14:51:02', '2026-04-10 14:51:02'),
(156, 29, 'VAE_mb', 1, '2026-03-23 16:34:04', '2026-03-23 16:34:04'),
(155, 29, 'VAE_sb', 1, '2026-03-23 16:34:04', '2026-03-23 16:34:04'),
(232, 30, 'VAE_mb', 2, '2026-04-17 07:58:42', '2026-04-17 07:58:42'),
(329, 31, 'VAE_lb', 1, '2026-04-29 07:23:00', '2026-04-29 07:23:00'),
(328, 31, 'VAE_mb', 1, '2026-04-29 07:23:00', '2026-04-29 07:23:00'),
(1444, 32, 'VTC_lb', 1, '2026-06-29 15:01:45', '2026-06-29 15:01:45'),
(1443, 32, 'VAE_sb', 1, '2026-06-29 15:01:45', '2026-06-29 15:01:45'),
(331, 33, 'VAE_lb', 1, '2026-04-29 07:23:12', '2026-04-29 07:23:12'),
(330, 33, 'VAE_mb', 1, '2026-04-29 07:23:12', '2026-04-29 07:23:12'),
(136, 34, 'VAE_mb', 1, '2026-03-19 14:33:01', '2026-03-19 14:33:01'),
(141, 35, 'VTC_mh', 2, '2026-03-21 16:11:36', '2026-03-21 16:11:36'),
(146, 36, 'VAE_mh', 1, '2026-03-21 17:10:00', '2026-03-21 17:10:00'),
(145, 36, 'VAE_mb', 1, '2026-03-21 17:10:00', '2026-03-21 17:10:00'),
(152, 37, 'VAE_lb', 1, '2026-03-23 16:32:55', '2026-03-23 16:32:55'),
(167, 38, 'VTC_lb', 1, '2026-03-30 06:11:06', '2026-03-30 06:11:06'),
(166, 38, 'VTC_sh', 1, '2026-03-30 06:11:06', '2026-03-30 06:11:06'),
(926, 39, 'VAE_mb', 1, '2026-05-29 06:33:31', '2026-05-29 06:33:31'),
(169, 40, 'VTC_lb', 1, '2026-03-30 14:56:33', '2026-03-30 14:56:33'),
(170, 41, 'VAE_sb', 1, '2026-04-04 16:03:13', '2026-04-04 16:03:13'),
(171, 41, 'VAE_mb', 1, '2026-04-04 16:03:13', '2026-04-04 16:03:13'),
(173, 42, 'VAE_sb', 1, '2026-04-07 07:10:04', '2026-04-07 07:10:04'),
(929, 43, 'VTC_mh', 1, '2026-05-29 06:35:25', '2026-05-29 06:35:25'),
(1099, 44, 'VAE_mb', 1, '2026-06-12 12:56:59', '2026-06-12 12:56:59'),
(184, 45, 'VAE_lh', 1, '2026-04-08 15:07:34', '2026-04-08 15:07:34'),
(183, 45, 'VAE_mb', 1, '2026-04-08 15:07:34', '2026-04-08 15:07:34'),
(1411, 46, 'VAE_mb', 2, '2026-06-29 14:20:16', '2026-06-29 14:20:16'),
(208, 47, 'ENFANT_mb', 1, '2026-04-13 09:04:23', '2026-04-13 09:04:23'),
(207, 47, 'ENFANT_sb', 1, '2026-04-13 09:04:23', '2026-04-13 09:04:23'),
(218, 48, 'VTC_mb', 1, '2026-04-14 15:31:34', '2026-04-14 15:31:34'),
(202, 49, 'VAE_lb', 1, '2026-04-09 15:59:59', '2026-04-09 15:59:59'),
(201, 49, 'VAE_mb', 2, '2026-04-09 15:59:59', '2026-04-09 15:59:59'),
(200, 49, 'VAE_sb', 2, '2026-04-09 15:59:59', '2026-04-09 15:59:59'),
(199, 50, 'ENFANT_mb', 2, '2026-04-09 14:25:08', '2026-04-09 14:25:08'),
(198, 50, 'VTC_mh', 1, '2026-04-09 14:25:08', '2026-04-09 14:25:08'),
(197, 50, 'VAE_sb', 1, '2026-04-09 14:25:08', '2026-04-09 14:25:08'),
(206, 51, 'VTC_mh', 3, '2026-04-12 06:12:13', '2026-04-12 06:12:13'),
(667, 52, 'VAE_mb', 1, '2026-05-17 16:56:35', '2026-05-17 16:56:35'),
(666, 52, 'VAE_lh', 1, '2026-05-17 16:56:35', '2026-05-17 16:56:35'),
(1009, 141, 'VTC_sb', 1, '2026-06-04 10:13:05', '2026-06-04 10:13:05'),
(974, 137, 'VAE_lb', 1, '2026-06-01 16:34:27', '2026-06-01 16:34:27'),
(945, 53, 'ENFANT_mb', 1, '2026-05-29 12:48:03', '2026-05-29 12:48:03'),
(219, 48, 'ENFANT_sb', 1, '2026-04-14 15:31:34', '2026-04-14 15:31:34'),
(467, 54, 'VAE_lb', 2, '2026-05-01 16:14:53', '2026-05-01 16:14:53'),
(466, 54, 'VAE_lh', 1, '2026-05-01 16:14:53', '2026-05-01 16:14:53'),
(465, 54, 'VAE_mh', 2, '2026-05-01 16:14:53', '2026-05-01 16:14:53'),
(229, 55, 'ENFANT_mb', 2, '2026-04-16 16:08:36', '2026-04-16 16:08:36'),
(228, 55, 'VTC_mb', 1, '2026-04-16 16:08:36', '2026-04-16 16:08:36'),
(227, 55, 'VTC_mh', 1, '2026-04-16 16:08:36', '2026-04-16 16:08:36'),
(234, 56, 'VAE_mb', 2, '2026-04-17 13:42:23', '2026-04-17 13:42:23'),
(233, 56, 'VAE_lh', 2, '2026-04-17 13:42:23', '2026-04-17 13:42:23'),
(481, 57, 'VAE_sb', 1, '2026-05-02 15:49:27', '2026-05-02 15:49:27'),
(333, 58, 'VAE_lb', 1, '2026-04-29 07:23:25', '2026-04-29 07:23:25'),
(332, 58, 'VAE_mb', 1, '2026-04-29 07:23:25', '2026-04-29 07:23:25'),
(241, 59, 'VAE_mb', 1, '2026-04-18 16:28:46', '2026-04-18 16:28:46'),
(240, 59, 'VAE_sb', 1, '2026-04-18 16:28:46', '2026-04-18 16:28:46'),
(312, 60, 'ENFANT_sb', 3, '2026-04-25 15:10:47', '2026-04-25 15:10:47'),
(313, 60, 'ENFANT_sh', 1, '2026-04-25 15:10:47', '2026-04-25 15:10:47'),
(311, 60, 'VTC_sb', 2, '2026-04-25 15:10:47', '2026-04-25 15:10:47'),
(310, 60, 'VTC_mh', 4, '2026-04-25 15:10:47', '2026-04-25 15:10:47'),
(262, 61, 'VAE_lh', 1, '2026-04-22 15:32:46', '2026-04-22 15:32:46'),
(261, 61, 'VAE_mb', 2, '2026-04-22 15:32:46', '2026-04-22 15:32:46'),
(264, 62, 'VTC_mh', 1, '2026-04-22 15:50:55', '2026-04-22 15:50:55'),
(263, 62, 'VTC_mb', 1, '2026-04-22 15:50:55', '2026-04-22 15:50:55'),
(327, 63, 'VAE_mh', 1, '2026-04-28 15:54:52', '2026-04-28 15:54:52'),
(463, 64, 'VAE_lh', 1, '2026-05-01 16:12:36', '2026-05-01 16:12:36'),
(273, 65, 'VAE_sb', 1, '2026-04-23 15:39:39', '2026-04-23 15:39:39'),
(272, 65, 'VAE_lh', 1, '2026-04-23 15:39:39', '2026-04-23 15:39:39'),
(309, 60, 'VAE_lb', 1, '2026-04-25 15:10:47', '2026-04-25 15:10:47'),
(306, 66, 'VAE_mb', 1, '2026-04-25 07:35:42', '2026-04-25 07:35:42'),
(305, 66, 'VAE_sb', 1, '2026-04-25 07:35:42', '2026-04-25 07:35:42'),
(318, 67, 'VTC_mh', 1, '2026-04-25 15:49:35', '2026-04-25 15:49:35'),
(317, 67, 'VTC_mb', 2, '2026-04-25 15:49:35', '2026-04-25 15:49:35'),
(316, 67, 'VTC_sb', 1, '2026-04-25 15:49:35', '2026-04-25 15:49:35'),
(315, 67, 'VAE_mb', 2, '2026-04-25 15:49:35', '2026-04-25 15:49:35'),
(314, 67, 'VAE_sb', 1, '2026-04-25 15:49:35', '2026-04-25 15:49:35'),
(1316, 79, 'VAE_mb', 1, '2026-06-25 06:02:45', '2026-06-25 06:02:45'),
(824, 68, 'VAE_mb', 1, '2026-05-24 12:04:37', '2026-05-24 12:04:37'),
(322, 69, 'VAE_mb', 1, '2026-04-25 16:00:12', '2026-04-25 16:00:12'),
(321, 69, 'VAE_sb', 1, '2026-04-25 16:00:12', '2026-04-25 16:00:12'),
(326, 70, 'VTC_mb', 1, '2026-04-27 15:51:43', '2026-04-27 15:51:43'),
(325, 70, 'VTC_mh', 1, '2026-04-27 15:51:43', '2026-04-27 15:51:43'),
(922, 71, 'VAE_lh', 1, '2026-05-29 06:32:39', '2026-05-29 06:32:39'),
(921, 71, 'VAE_mb', 1, '2026-05-29 06:32:39', '2026-05-29 06:32:39'),
(370, 72, 'VAE_mb', 1, '2026-04-30 15:52:25', '2026-04-30 15:52:25'),
(464, 54, 'VAE_mb', 4, '2026-05-01 16:14:53', '2026-05-01 16:14:53'),
(925, 73, 'VAE_lh', 1, '2026-05-29 06:33:15', '2026-05-29 06:33:15'),
(604, 74, 'VAE_sb', 2, '2026-05-09 14:32:56', '2026-05-09 14:32:56'),
(603, 74, 'VAE_mh', 2, '2026-05-09 14:32:56', '2026-05-09 14:32:56'),
(602, 74, 'VAE_mb', 2, '2026-05-09 14:32:56', '2026-05-09 14:32:56'),
(601, 74, 'VAE_lh', 2, '2026-05-09 14:32:56', '2026-05-09 14:32:56'),
(546, 75, 'VTC_sb', 1, '2026-05-08 07:38:55', '2026-05-08 07:38:55'),
(545, 75, 'VTC_mb', 1, '2026-05-08 07:38:55', '2026-05-08 07:38:55'),
(462, 64, 'VAE_mb', 1, '2026-05-01 16:12:36', '2026-05-01 16:12:36'),
(1421, 76, 'VAE_mh', 1, '2026-06-29 14:28:08', '2026-06-29 14:28:08'),
(1420, 76, 'VAE_mb', 3, '2026-06-29 14:28:08', '2026-06-29 14:28:08'),
(1419, 76, 'VAE_sb', 2, '2026-06-29 14:28:08', '2026-06-29 14:28:08'),
(458, 77, 'VAE_mb', 1, '2026-05-01 15:09:01', '2026-05-01 15:09:01'),
(457, 77, 'VAE_sb', 1, '2026-05-01 15:09:01', '2026-05-01 15:09:01'),
(473, 78, 'VAE_mb', 1, '2026-05-01 21:26:34', '2026-05-01 21:26:34'),
(472, 78, 'VTC_lb', 1, '2026-05-01 21:26:34', '2026-05-01 21:26:34'),
(461, 64, 'VAE_sb', 1, '2026-05-01 16:12:36', '2026-05-01 16:12:36'),
(1315, 79, 'VAE_lh', 1, '2026-06-25 06:02:45', '2026-06-25 06:02:45'),
(1232, 80, 'VAE_mb', 2, '2026-06-20 08:33:50', '2026-06-20 08:33:50'),
(1231, 80, 'VAE_sb', 2, '2026-06-20 08:33:50', '2026-06-20 08:33:50'),
(477, 81, 'VAE_lh', 1, '2026-05-01 22:43:27', '2026-05-01 22:43:27'),
(476, 81, 'VAE_mb', 1, '2026-05-01 22:43:27', '2026-05-01 22:43:27'),
(939, 82, 'VAE_mb', 3, '2026-05-29 07:52:47', '2026-05-29 07:52:47'),
(493, 83, 'VAE_sb', 1, '2026-05-04 15:58:29', '2026-05-04 15:58:29'),
(1394, 209, 'VAE_mb', 1, '2026-06-29 13:28:40', '2026-06-29 13:28:40'),
(1395, 84, 'VAE_mb', 2, '2026-06-29 13:43:14', '2026-06-29 13:43:14'),
(777, 85, 'accessoires_mb', 1, '2026-05-22 19:24:32', '2026-05-22 19:24:32'),
(537, 86, 'VAE_lh', 1, '2026-05-06 13:42:28', '2026-05-06 13:42:28'),
(536, 86, 'VAE_lb', 1, '2026-05-06 13:42:28', '2026-05-06 13:42:28'),
(535, 86, 'VAE_mb', 3, '2026-05-06 13:42:28', '2026-05-06 13:42:28'),
(534, 86, 'VAE_sb', 1, '2026-05-06 13:42:28', '2026-05-06 13:42:28'),
(917, 87, 'VAE_lh', 1, '2026-05-29 06:30:23', '2026-05-29 06:30:23'),
(916, 87, 'VAE_mb', 1, '2026-05-29 06:30:23', '2026-05-29 06:30:23'),
(539, 88, 'VAE_mb', 1, '2026-05-06 14:57:21', '2026-05-06 14:57:21'),
(544, 89, 'VAE_mb', 1, '2026-05-08 07:38:28', '2026-05-08 07:38:28'),
(565, 90, 'VAE_mb', 1, '2026-05-08 14:50:05', '2026-05-08 14:50:05'),
(625, 91, 'VAE_sb', 1, '2026-05-14 07:10:55', '2026-05-14 07:10:55'),
(624, 91, 'VAE_lh', 1, '2026-05-14 07:10:55', '2026-05-14 07:10:55'),
(596, 92, 'VAE_mb', 1, '2026-05-09 13:14:24', '2026-05-09 13:14:24'),
(608, 93, 'VTC_lh', 1, '2026-05-09 15:45:27', '2026-05-09 15:45:27'),
(607, 93, 'VTC_mh', 1, '2026-05-09 15:45:27', '2026-05-09 15:45:27'),
(941, 94, 'VTC_mh', 1, '2026-05-29 12:14:04', '2026-05-29 12:14:04'),
(1121, 95, 'VTC_mb', 1, '2026-06-15 09:04:33', '2026-06-15 09:04:33'),
(940, 96, 'VTC_mh', 1, '2026-05-29 08:04:14', '2026-05-29 08:04:14'),
(617, 97, 'VAE_lb', 1, '2026-05-13 13:13:20', '2026-05-13 13:13:20'),
(616, 97, 'VAE_sb', 1, '2026-05-13 13:13:20', '2026-05-13 13:13:20'),
(776, 98, 'VTC_mh', 1, '2026-05-22 19:22:47', '2026-05-22 19:22:47'),
(832, 99, 'VTC_mb', 1, '2026-05-25 15:59:21', '2026-05-25 15:59:21'),
(831, 99, 'VTC_mh', 1, '2026-05-25 15:59:21', '2026-05-25 15:59:21'),
(623, 100, 'VAE_mb', 1, '2026-05-13 15:43:28', '2026-05-13 15:43:28'),
(622, 100, 'VAE_sb', 1, '2026-05-13 15:43:28', '2026-05-13 15:43:28'),
(654, 101, 'VAE_lb', 1, '2026-05-15 15:27:54', '2026-05-15 15:27:54'),
(653, 101, 'VAE_sb', 1, '2026-05-15 15:27:54', '2026-05-15 15:27:54'),
(658, 102, 'VTC_lh', 1, '2026-05-15 15:37:20', '2026-05-15 15:37:20'),
(657, 102, 'VAE_mb', 1, '2026-05-15 15:37:20', '2026-05-15 15:37:20'),
(656, 102, 'VAE_lb', 1, '2026-05-15 15:37:20', '2026-05-15 15:37:20'),
(661, 103, 'accessoires_mb', 1, '2026-05-15 16:01:32', '2026-05-15 16:01:32'),
(660, 103, 'VAE_lh', 1, '2026-05-15 16:01:32', '2026-05-15 16:01:32'),
(659, 103, 'VAE_mb', 1, '2026-05-15 16:01:32', '2026-05-15 16:01:32'),
(655, 104, 'VAE_lh', 1, '2026-05-15 15:28:04', '2026-05-15 15:28:04'),
(1418, 105, 'VAE_lb', 1, '2026-06-29 14:25:50', '2026-06-29 14:25:50'),
(1417, 105, 'VAE_sb', 1, '2026-06-29 14:25:50', '2026-06-29 14:25:50'),
(703, 106, 'VAE_mb', 2, '2026-05-20 16:37:21', '2026-05-20 16:37:21'),
(702, 106, 'VAE_mh', 2, '2026-05-20 16:37:21', '2026-05-20 16:37:21'),
(1176, 107, 'VAE_sb', 1, '2026-06-17 16:01:57', '2026-06-17 16:01:57'),
(1175, 107, 'VAE_lh', 1, '2026-06-17 16:01:57', '2026-06-17 16:01:57'),
(1174, 107, 'VAE_mb', 2, '2026-06-17 16:01:57', '2026-06-17 16:01:57'),
(697, 108, 'VAE_lh', 1, '2026-05-20 09:25:29', '2026-05-20 09:25:29'),
(696, 108, 'VAE_mb', 1, '2026-05-20 09:25:29', '2026-05-20 09:25:29'),
(967, 109, 'VAE_sb', 1, '2026-05-31 16:07:38', '2026-05-31 16:07:38'),
(966, 109, 'VAE_mh', 1, '2026-05-31 16:07:38', '2026-05-31 16:07:38'),
(759, 110, 'VAE_lh', 1, '2026-05-22 16:04:19', '2026-05-22 16:04:19'),
(758, 110, 'VAE_mh', 1, '2026-05-22 16:04:19', '2026-05-22 16:04:19'),
(757, 110, 'VAE_mb', 1, '2026-05-22 16:04:19', '2026-05-22 16:04:19'),
(756, 110, 'VAE_sb', 2, '2026-05-22 16:04:19', '2026-05-22 16:04:19'),
(715, 111, 'VAE_mb', 1, '2026-05-21 14:53:00', '2026-05-21 14:53:00'),
(744, 112, 'VTC_sb', 1, '2026-05-22 09:09:15', '2026-05-22 09:09:15'),
(982, 113, 'VTC_lh', 1, '2026-06-02 14:30:40', '2026-06-02 14:30:40'),
(981, 113, 'VAE_mb', 1, '2026-06-02 14:30:40', '2026-06-02 14:30:40'),
(829, 114, 'VTC_lb', 1, '2026-05-25 15:03:36', '2026-05-25 15:03:36'),
(820, 115, 'VAE_mb', 1, '2026-05-24 06:26:44', '2026-05-24 06:26:44'),
(819, 115, 'VAE_lh', 1, '2026-05-24 06:26:44', '2026-05-24 06:26:44'),
(834, 116, 'VTC_mh', 1, '2026-05-25 16:29:51', '2026-05-25 16:29:51'),
(881, 117, 'VAE_sb', 1, '2026-05-27 16:17:59', '2026-05-27 16:17:59'),
(880, 117, 'VAE_mb', 2, '2026-05-27 16:17:59', '2026-05-27 16:17:59'),
(833, 116, 'VAE_sb', 2, '2026-05-25 16:29:51', '2026-05-25 16:29:51'),
(802, 118, 'VTC_mh', 1, '2026-05-23 15:38:43', '2026-05-23 15:38:43'),
(825, 119, 'VTC_sb', 2, '2026-05-24 15:28:38', '2026-05-24 15:28:38'),
(830, 120, 'VAE_mb', 2, '2026-05-25 15:57:38', '2026-05-25 15:57:38'),
(879, 117, 'VAE_lh', 2, '2026-05-27 16:17:59', '2026-05-27 16:17:59'),
(858, 121, 'VAE_mb', 1, '2026-05-27 07:23:12', '2026-05-27 07:23:12'),
(857, 121, 'VAE_lb', 1, '2026-05-27 07:23:12', '2026-05-27 07:23:12'),
(975, 122, 'VAE_mb', 1, '2026-06-02 09:00:58', '2026-06-02 09:00:58'),
(867, 123, 'VAE_lb', 1, '2026-05-27 09:48:22', '2026-05-27 09:48:22'),
(864, 124, 'VTC_lb', 1, '2026-05-27 09:47:14', '2026-05-27 09:47:14'),
(863, 124, 'VAE_sb', 1, '2026-05-27 09:47:14', '2026-05-27 09:47:14'),
(976, 125, 'VAE_lh', 1, '2026-06-02 13:58:49', '2026-06-02 13:58:49'),
(853, 126, 'VAE_lb', 2, '2026-05-26 16:42:39', '2026-05-26 16:42:39'),
(854, 126, 'VAE_mb', 2, '2026-05-26 16:42:39', '2026-05-26 16:42:39'),
(878, 127, 'VTC_mb', 1, '2026-05-27 15:51:17', '2026-05-27 15:51:17'),
(877, 127, 'VAE_sb', 1, '2026-05-27 15:51:17', '2026-05-27 15:51:17'),
(1036, 128, 'VAE_mb', 1, '2026-06-08 06:39:56', '2026-06-08 06:39:56'),
(904, 129, 'VAE_lb', 1, '2026-05-28 16:13:22', '2026-05-28 16:13:22'),
(903, 129, 'VAE_mb', 1, '2026-05-28 16:13:22', '2026-05-28 16:13:22'),
(884, 130, 'VAE_sb', 1, '2026-05-28 09:04:52', '2026-05-28 09:04:52'),
(973, 131, 'VAE_mb', 1, '2026-06-01 16:33:44', '2026-06-01 16:33:44'),
(910, 132, 'VAE_lb', 1, '2026-05-29 06:28:11', '2026-05-29 06:28:11'),
(909, 132, 'VAE_mb', 1, '2026-05-29 06:28:11', '2026-05-29 06:28:11'),
(1187, 133, 'VAE_lh', 1, '2026-06-18 06:37:20', '2026-06-18 06:37:20'),
(1186, 133, 'VAE_lb', 2, '2026-06-18 06:37:20', '2026-06-18 06:37:20'),
(1185, 133, 'VAE_mh', 2, '2026-06-18 06:37:20', '2026-06-18 06:37:20'),
(1184, 133, 'VAE_mb', 4, '2026-06-18 06:37:20', '2026-06-18 06:37:20'),
(892, 134, 'VTC_lh', 1, '2026-05-28 14:25:43', '2026-05-28 14:25:43'),
(963, 135, 'VAE_lh', 1, '2026-05-31 14:11:26', '2026-05-31 14:11:26'),
(962, 135, 'VAE_mb', 1, '2026-05-31 14:11:26', '2026-05-31 14:11:26'),
(930, 43, 'VTC_mb', 1, '2026-05-29 06:35:25', '2026-05-29 06:35:25'),
(1426, 136, 'VAE_mb', 1, '2026-06-29 14:30:53', '2026-06-29 14:30:53'),
(955, 138, 'VAE_mh', 1, '2026-05-30 15:40:44', '2026-05-30 15:40:44'),
(956, 139, 'VAE_mb', 2, '2026-05-30 15:53:03', '2026-05-30 15:53:03'),
(1402, 140, 'VAE_mb', 1, '2026-06-29 14:10:34', '2026-06-29 14:10:34'),
(1401, 140, 'VAE_lh', 1, '2026-06-29 14:10:34', '2026-06-29 14:10:34'),
(1371, 142, 'VAE_mb', 1, '2026-06-28 16:49:10', '2026-06-28 16:49:10'),
(1020, 143, 'VTC_mh', 1, '2026-06-04 16:24:47', '2026-06-04 16:24:47'),
(1019, 143, 'VTC_mb', 1, '2026-06-04 16:24:47', '2026-06-04 16:24:47'),
(1011, 144, 'VAE_lh', 1, '2026-06-04 15:58:29', '2026-06-04 15:58:29'),
(1373, 146, 'VAE_sb', 1, '2026-06-28 19:04:51', '2026-06-28 19:04:51'),
(1372, 146, 'VAE_mb', 1, '2026-06-28 19:04:51', '2026-06-28 19:04:51'),
(1267, 147, 'VAE_s/mb', 2, '2026-06-20 18:45:38', '2026-06-20 18:45:38'),
(1024, 148, 'VTC_mh', 1, '2026-06-06 06:51:09', '2026-06-06 06:51:09'),
(1023, 148, 'VAE_mb', 1, '2026-06-06 06:51:09', '2026-06-06 06:51:09'),
(1212, 149, 'VAE_mh', 1, '2026-06-19 09:21:45', '2026-06-19 09:21:45'),
(1392, 150, 'VAE_mb', 1, '2026-06-29 13:23:19', '2026-06-29 13:23:19'),
(1140, 151, 'VAE_sb', 1, '2026-06-16 15:40:10', '2026-06-16 15:40:10'),
(1026, 145, 'VAE_s/mb', 1, '2026-06-06 07:04:14', '2026-06-06 07:04:14'),
(1038, 152, 'VAE_sb', 1, '2026-06-08 07:59:02', '2026-06-08 07:59:02'),
(1037, 153, 'VAE_lh', 1, '2026-06-08 07:40:07', '2026-06-08 07:40:07'),
(1114, 166, 'VAE_sb', 1, '2026-06-13 15:17:38', '2026-06-13 15:17:38'),
(1269, 154, 'VAE_lh', 1, '2026-06-20 18:45:56', '2026-06-20 18:45:56'),
(1059, 155, 'VAE_sb', 1, '2026-06-09 15:33:11', '2026-06-09 15:33:11'),
(1060, 156, 'VAE_mb', 2, '2026-06-09 17:13:35', '2026-06-09 17:13:35'),
(1119, 157, 'VTC_mb', 1, '2026-06-14 12:56:39', '2026-06-14 12:56:39'),
(1118, 157, 'VTC_lh', 1, '2026-06-14 12:56:39', '2026-06-14 12:56:39'),
(1100, 158, 'VTC_mb', 1, '2026-06-12 14:09:39', '2026-06-12 14:09:39'),
(1101, 159, 'VAE_mb', 2, '2026-06-12 14:46:27', '2026-06-12 14:46:27'),
(1065, 160, 'VAE_mb', 2, '2026-06-10 14:45:55', '2026-06-10 14:45:55'),
(1058, 161, 'VAE_mh', 1, '2026-06-09 15:12:19', '2026-06-09 15:12:19'),
(1089, 162, 'VAE_s/mb', 2, '2026-06-11 08:57:32', '2026-06-11 08:57:32'),
(1093, 163, 'VAE_lb', 1, '2026-06-11 15:38:55', '2026-06-11 15:38:55'),
(1092, 163, 'VAE_sb', 1, '2026-06-11 15:38:55', '2026-06-11 15:38:55'),
(1156, 170, 'VAE_sb', 1, '2026-06-17 07:29:46', '2026-06-17 07:29:46'),
(1425, 136, 'Cargo_', 1, '2026-06-29 14:30:53', '2026-06-29 14:30:53'),
(1384, 164, 'VAE_sb', 1, '2026-06-29 12:26:52', '2026-06-29 12:26:52'),
(1094, 165, 'VAE_sb', 2, '2026-06-12 06:49:35', '2026-06-12 06:49:35'),
(1095, 165, 'VAE_s/mb', 1, '2026-06-12 06:49:35', '2026-06-12 06:49:35'),
(1096, 165, 'VAE_mh', 1, '2026-06-12 06:49:35', '2026-06-12 06:49:35'),
(1357, 167, 'VAE_mb', 1, '2026-06-26 15:56:42', '2026-06-26 15:56:42'),
(1155, 168, 'VTC_mb', 1, '2026-06-17 07:01:51', '2026-06-17 07:01:51'),
(1154, 168, 'VTC_lh', 1, '2026-06-17 07:01:51', '2026-06-17 07:01:51'),
(1266, 169, 'VTC_mb', 1, '2026-06-20 17:10:52', '2026-06-20 17:10:52'),
(1265, 169, 'VTC_mh', 1, '2026-06-20 17:10:52', '2026-06-20 17:10:52'),
(1268, 154, 'Cargo_', 1, '2026-06-20 18:45:56', '2026-06-20 18:45:56'),
(1157, 170, 'VTC_mh', 1, '2026-06-17 07:29:46', '2026-06-17 07:29:46'),
(1189, 171, 'VAE_mb', 1, '2026-06-18 09:04:58', '2026-06-18 09:04:58'),
(1188, 171, 'VAE_lb', 1, '2026-06-18 09:04:58', '2026-06-18 09:04:58'),
(1198, 172, 'VAE_lh', 1, '2026-06-18 12:54:52', '2026-06-18 12:54:52'),
(1197, 172, 'VAE_s/mb', 1, '2026-06-18 12:54:52', '2026-06-18 12:54:52'),
(1173, 173, 'VAE_s/mb', 1, '2026-06-17 14:40:50', '2026-06-17 14:40:50'),
(1202, 174, 'VAE_sb', 2, '2026-06-18 12:55:18', '2026-06-18 12:55:18'),
(1201, 174, 'VAE_mb', 1, '2026-06-18 12:55:18', '2026-06-18 12:55:18'),
(1200, 174, 'Cargo_', 1, '2026-06-18 12:55:18', '2026-06-18 12:55:18'),
(1190, 175, 'VAE_mb', 2, '2026-06-18 10:03:15', '2026-06-18 10:03:15'),
(1191, 175, 'VAE_sb', 1, '2026-06-18 10:03:15', '2026-06-18 10:03:15'),
(1192, 176, 'VAE_mh', 1, '2026-06-18 12:51:26', '2026-06-18 12:51:26'),
(1416, 177, 'VAE_mb', 1, '2026-06-29 14:25:29', '2026-06-29 14:25:29'),
(1415, 177, 'VAE_mh', 1, '2026-06-29 14:25:29', '2026-06-29 14:25:29'),
(1199, 172, 'VAE_lb', 1, '2026-06-18 12:54:52', '2026-06-18 12:54:52'),
(1375, 178, 'VAE_lh', 1, '2026-06-28 19:05:30', '2026-06-28 19:05:30'),
(1299, 179, 'VAE_lh', 1, '2026-06-23 15:55:50', '2026-06-23 15:55:50'),
(1298, 179, 'VAE_mb', 1, '2026-06-23 15:55:50', '2026-06-23 15:55:50'),
(1297, 180, 'VTC_mh', 1, '2026-06-23 15:27:18', '2026-06-23 15:27:18'),
(1296, 180, 'VTC_mb', 1, '2026-06-23 15:27:18', '2026-06-23 15:27:18'),
(1295, 180, 'ENFANT_sb', 1, '2026-06-23 15:27:18', '2026-06-23 15:27:18'),
(1217, 181, 'VAE_sb', 1, '2026-06-19 15:48:09', '2026-06-19 15:48:09'),
(1238, 182, 'VAE_mb', 1, '2026-06-20 10:19:44', '2026-06-20 10:19:44'),
(1237, 182, 'VAE_sb', 1, '2026-06-20 10:19:44', '2026-06-20 10:19:44'),
(1304, 183, 'VAE_s/mb', 1, '2026-06-24 07:32:19', '2026-06-24 07:32:19'),
(1261, 185, 'VAE_lh', 1, '2026-06-20 15:56:29', '2026-06-20 15:56:29'),
(1260, 185, 'VAE_mb', 1, '2026-06-20 15:56:29', '2026-06-20 15:56:29'),
(1254, 186, 'VTC_s/mh', 1, '2026-06-20 15:27:46', '2026-06-20 15:27:46'),
(1253, 186, 'VTC_mh', 1, '2026-06-20 15:27:46', '2026-06-20 15:27:46'),
(1310, 187, 'VAE_mb', 1, '2026-06-24 14:56:58', '2026-06-24 14:56:58'),
(1332, 184, 'VTC_mh', 1, '2026-06-25 15:46:59', '2026-06-25 15:46:59'),
(1256, 188, 'VAE_sb', 2, '2026-06-20 15:29:28', '2026-06-20 15:29:28'),
(1333, 184, 'accessoires_mb', 1, '2026-06-25 15:46:59', '2026-06-25 15:46:59'),
(1414, 189, 'VAE_mb', 1, '2026-06-29 14:24:08', '2026-06-29 14:24:08'),
(1275, 190, 'VAE_lb', 1, '2026-06-22 15:10:20', '2026-06-22 15:10:20'),
(1274, 190, 'VAE_sb', 1, '2026-06-22 15:10:20', '2026-06-22 15:10:20'),
(1276, 191, 'ENFANT_sh', 1, '2026-06-22 16:20:27', '2026-06-22 16:20:27'),
(1277, 191, 'VTC_mb', 1, '2026-06-22 16:20:27', '2026-06-22 16:20:27'),
(1278, 191, 'VTC_mh', 1, '2026-06-22 16:20:27', '2026-06-22 16:20:27'),
(1280, 192, 'VAE_mb', 1, '2026-06-22 16:34:28', '2026-06-22 16:34:28'),
(1356, 167, 'VAE_lb', 2, '2026-06-26 15:56:42', '2026-06-26 15:56:42'),
(1326, 193, 'VTC_sh', 1, '2026-06-25 13:20:14', '2026-06-25 13:20:14'),
(1331, 184, 'VTC_sb', 1, '2026-06-25 15:46:59', '2026-06-25 15:46:59'),
(1305, 194, 'VTC_mb', 1, '2026-06-24 08:15:25', '2026-06-24 08:15:25'),
(1343, 195, 'VAE_sb', 1, '2026-06-26 08:07:26', '2026-06-26 08:07:26'),
(1342, 195, 'VAE_lh', 1, '2026-06-26 08:07:26', '2026-06-26 08:07:26'),
(1308, 196, 'VAE_mb', 1, '2026-06-24 09:46:41', '2026-06-24 09:46:41'),
(1374, 197, 'VAE_lh', 1, '2026-06-28 19:05:21', '2026-06-28 19:05:21'),
(1314, 198, 'VAE_mb', 1, '2026-06-24 18:14:17', '2026-06-24 18:14:17'),
(1329, 199, 'VTC_lh', 1, '2026-06-25 14:28:48', '2026-06-25 14:28:48'),
(1328, 199, 'VAE_sb', 1, '2026-06-25 14:28:48', '2026-06-25 14:28:48'),
(1349, 200, 'VTC_mh', 1, '2026-06-26 08:28:39', '2026-06-26 08:28:39'),
(1348, 200, 'VTC_mb', 1, '2026-06-26 08:28:39', '2026-06-26 08:28:39'),
(1334, 201, 'VAE_sb', 1, '2026-06-25 15:55:01', '2026-06-25 15:55:01'),
(1325, 202, 'VAE_lb', 1, '2026-06-25 13:18:27', '2026-06-25 13:18:27'),
(1324, 202, 'VAE_sb', 1, '2026-06-25 13:18:27', '2026-06-25 13:18:27'),
(1358, 203, 'VTC_mh', 1, '2026-06-26 16:06:08', '2026-06-26 16:06:08'),
(1330, 204, 'VAE_lh', 1, '2026-06-25 15:14:24', '2026-06-25 15:14:24'),
(1352, 205, 'VTC_sb', 1, '2026-06-26 13:38:05', '2026-06-26 13:38:05'),
(1351, 205, 'VAE_mb', 1, '2026-06-26 13:38:05', '2026-06-26 13:38:05'),
(1350, 206, 'accessoires_mb', 1, '2026-06-26 12:22:47', '2026-06-26 12:22:47'),
(1363, 207, 'VAE_mb', 1, '2026-06-27 13:53:14', '2026-06-27 13:53:14'),
(1362, 207, 'VAE_sb', 1, '2026-06-27 13:53:14', '2026-06-27 13:53:14'),
(1369, 208, 'VAE_mb', 1, '2026-06-27 16:04:12', '2026-06-27 16:04:12'),
(1385, 164, 'VAE_s/mb', 1, '2026-06-29 12:26:52', '2026-06-29 12:26:52'),
(1393, 209, 'VAE_sb', 1, '2026-06-29 13:28:40', '2026-06-29 13:28:40'),
(1386, 210, 'VAE_s/mb', 1, '2026-06-29 12:53:51', '2026-06-29 12:53:51'),
(1387, 210, 'VAE_mb', 1, '2026-06-29 12:53:51', '2026-06-29 12:53:51'),
(1396, 211, 'VAE_mb', 1, '2026-06-29 13:48:21', '2026-06-29 13:48:21'),
(1413, 212, 'VAE_s/mb', 2, '2026-06-29 14:23:47', '2026-06-29 14:23:47'),
(1424, 26, 'VAE_lh', 1, '2026-06-29 14:28:29', '2026-06-29 14:28:29'),
(1452, 213, 'VTC_mh', 1, '2026-06-29 15:05:24', '2026-06-29 15:05:24'),
(1451, 213, 'VTC_mb', 3, '2026-06-29 15:05:24', '2026-06-29 15:05:24'),
(1450, 213, 'VTC_s/mh', 1, '2026-06-29 15:05:24', '2026-06-29 15:05:24'),
(1449, 213, 'VTC_sh', 1, '2026-06-29 15:05:24', '2026-06-29 15:05:24'),
(1448, 213, 'VTC_sb', 2, '2026-06-29 15:05:24', '2026-06-29 15:05:24'),
(1447, 213, 'ENFANT_mb', 2, '2026-06-29 15:05:24', '2026-06-29 15:05:24'),
(1446, 213, 'VAE_lh', 1, '2026-06-29 15:05:24', '2026-06-29 15:05:24'),
(1445, 213, 'VAE_mb', 2, '2026-06-29 15:05:24', '2026-06-29 15:05:24'),
(1453, 213, 'VTC_lb', 2, '2026-06-29 15:05:24', '2026-06-29 15:05:24'),
(1454, 213, 'VTC_lh', 1, '2026-06-29 15:05:24', '2026-06-29 15:05:24');

-- --------------------------------------------------------

--
-- Structure de la table `reservation_payments`
--

CREATE TABLE `reservation_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reservation_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('cb','liquide','cheque','virement','autre') NOT NULL,
  `paid_at` datetime NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reservation_payments`
--

INSERT INTO `reservation_payments` (`id`, `reservation_id`, `amount`, `method`, `paid_at`, `note`, `created_at`, `updated_at`) VALUES
(2, 19, 34.00, 'cb', '2026-02-28 12:34:00', NULL, '2026-03-01 12:35:00', '2026-03-01 12:35:00'),
(3, 22, 15.00, 'cb', '2026-03-05 10:35:00', NULL, '2026-03-05 10:35:45', '2026-03-05 10:35:45'),
(4, 18, 50.00, 'cb', '2026-03-05 14:17:00', 'voir pour pneu?', '2026-03-05 14:17:49', '2026-03-05 14:17:49'),
(5, 20, 380.00, 'virement', '2026-03-06 08:00:00', NULL, '2026-03-07 08:01:13', '2026-03-07 08:01:13'),
(6, 23, 110.00, 'cb', '2026-03-08 18:03:00', NULL, '2026-03-08 18:03:15', '2026-03-08 18:03:15'),
(12, 29, 255.00, 'cb', '2026-03-03 20:38:00', NULL, '2026-03-23 16:34:04', '2026-03-23 16:34:04'),
(8, 34, 32.00, 'cb', '2026-03-19 14:32:00', NULL, '2026-03-19 14:33:01', '2026-03-19 14:33:01'),
(9, 35, 50.00, 'cb', '2026-03-21 16:10:00', 'rendu 10€ en liquide', '2026-03-21 16:11:36', '2026-03-21 16:11:36'),
(10, 36, 25.00, 'cb', '2026-03-21 17:09:00', NULL, '2026-03-21 17:10:00', '2026-03-21 17:10:00'),
(13, 38, 80.00, 'cb', '2026-03-27 06:10:00', NULL, '2026-03-30 06:11:06', '2026-03-30 06:11:06'),
(14, 16, 173.00, 'cb', '2026-03-30 08:47:00', NULL, '2026-03-30 08:48:09', '2026-03-30 08:48:09'),
(15, 41, 76.00, 'cb', '2026-04-04 16:03:00', NULL, '2026-04-04 16:03:13', '2026-04-04 16:03:13'),
(16, 42, 64.00, 'cb', '2026-04-07 07:09:00', NULL, '2026-04-07 07:10:04', '2026-04-07 07:10:04'),
(17, 45, 76.00, 'cb', '2026-04-08 15:07:00', NULL, '2026-04-08 15:07:34', '2026-04-08 15:07:34'),
(18, 50, 90.00, 'cb', '2026-04-09 14:25:00', NULL, '2026-04-09 14:25:08', '2026-04-09 14:25:08'),
(19, 49, 190.00, 'cb', '2026-04-09 15:59:00', NULL, '2026-04-09 15:59:59', '2026-04-09 15:59:59'),
(20, 28, 52.00, 'cb', '2026-03-10 14:50:00', NULL, '2026-04-10 14:51:02', '2026-04-10 14:51:02'),
(21, 51, 42.00, 'cb', '2026-04-11 06:11:00', NULL, '2026-04-12 06:12:13', '2026-04-12 06:12:13'),
(22, 47, 196.00, 'cb', '2026-04-13 09:03:00', NULL, '2026-04-13 09:04:23', '2026-04-13 09:04:23'),
(24, 24, 78.00, 'cb', '2026-04-14 08:27:00', NULL, '2026-04-14 08:28:12', '2026-04-14 08:28:12'),
(25, 48, 22.00, 'cb', '2026-04-14 15:31:00', NULL, '2026-04-14 15:31:34', '2026-04-14 15:31:34'),
(27, 55, 44.00, 'cb', '2026-04-16 16:07:00', NULL, '2026-04-16 16:08:36', '2026-04-16 16:08:36'),
(28, 30, 104.00, 'cb', '2026-04-17 07:58:00', NULL, '2026-04-17 07:58:42', '2026-04-17 07:58:42'),
(29, 56, 133.00, 'cb', '2026-04-17 13:42:00', NULL, '2026-04-17 13:42:23', '2026-04-17 13:42:23'),
(30, 59, 50.00, 'cb', '2026-04-18 16:28:00', NULL, '2026-04-18 16:28:46', '2026-04-18 16:28:46'),
(31, 61, 66.00, 'cb', '2026-04-22 15:30:00', NULL, '2026-04-22 15:32:46', '2026-04-22 15:32:46'),
(32, 62, 24.00, 'cb', '2026-04-22 15:50:00', NULL, '2026-04-22 15:50:55', '2026-04-22 15:50:55'),
(33, 65, 44.00, 'cb', '2026-04-23 15:39:00', NULL, '2026-04-23 15:39:39', '2026-04-23 15:39:39'),
(37, 60, 800.00, 'liquide', '2026-04-23 20:04:00', NULL, '2026-04-25 15:10:47', '2026-04-25 15:10:47'),
(36, 66, 152.00, 'cb', '2026-04-25 07:35:00', NULL, '2026-04-25 07:35:42', '2026-04-25 07:35:42'),
(38, 67, 131.00, 'cb', '2026-04-25 15:49:00', NULL, '2026-04-25 15:49:35', '2026-04-25 15:49:35'),
(40, 69, 20.00, 'cb', '2026-04-25 15:58:00', NULL, '2026-04-25 16:00:12', '2026-04-25 16:00:12'),
(41, 70, 24.00, 'cb', '2026-04-27 15:51:00', NULL, '2026-04-27 15:51:43', '2026-04-27 15:51:43'),
(42, 72, 38.00, 'cb', '2026-04-30 15:52:00', NULL, '2026-04-30 15:52:25', '2026-04-30 15:52:25'),
(43, 77, 55.00, 'cb', '2026-05-01 15:08:00', NULL, '2026-05-01 15:09:01', '2026-05-01 15:09:01'),
(47, 78, 40.00, 'cb', '2026-05-01 16:03:00', NULL, '2026-05-01 21:26:34', '2026-05-01 21:26:34'),
(45, 64, 157.00, 'cb', '2026-05-01 16:11:00', NULL, '2026-05-01 16:12:36', '2026-05-01 16:12:36'),
(46, 54, 380.00, 'cheque', '2026-05-01 16:14:00', NULL, '2026-05-01 16:14:53', '2026-05-01 16:14:53'),
(48, 57, 110.00, 'cb', '2026-05-02 15:49:00', NULL, '2026-05-02 15:49:27', '2026-05-02 15:49:27'),
(50, 86, 140.00, 'cb', '2026-05-06 13:37:00', NULL, '2026-05-06 13:42:28', '2026-05-06 13:42:28'),
(51, 88, 25.00, 'cb', '2026-05-06 14:57:00', NULL, '2026-05-06 14:57:21', '2026-05-06 14:57:21'),
(52, 89, 32.00, 'cb', '2026-05-08 07:38:00', NULL, '2026-05-08 07:38:28', '2026-05-08 07:38:28'),
(53, 75, 180.00, 'cb', '2026-05-08 07:38:00', NULL, '2026-05-08 07:38:55', '2026-05-08 07:38:55'),
(54, 90, 38.00, 'cb', '2026-05-08 14:49:00', NULL, '2026-05-08 14:50:05', '2026-05-08 14:50:05'),
(55, 92, 38.00, 'liquide', '2026-05-09 13:13:00', NULL, '2026-05-09 13:14:24', '2026-05-09 13:14:24'),
(56, 74, 212.00, 'cb', '2026-05-09 14:32:00', NULL, '2026-05-09 14:32:56', '2026-05-09 14:32:56'),
(57, 93, 40.00, 'cb', '2026-05-09 15:45:00', NULL, '2026-05-09 15:45:27', '2026-05-09 15:45:27'),
(58, 97, 76.00, 'cb', '2026-05-13 13:12:00', NULL, '2026-05-13 13:13:20', '2026-05-13 13:13:20'),
(59, 91, 352.00, 'cb', '2026-05-14 07:10:00', NULL, '2026-05-14 07:10:55', '2026-05-14 07:10:55'),
(64, 104, 25.00, 'cb', '2026-05-15 15:20:00', NULL, '2026-05-15 15:28:04', '2026-05-15 15:28:04'),
(63, 101, 76.00, 'cb', '2026-05-15 15:26:00', NULL, '2026-05-15 15:27:54', '2026-05-15 15:27:54'),
(65, 102, 64.00, 'cb', '2026-05-15 15:37:00', NULL, '2026-05-15 15:37:20', '2026-05-15 15:37:20'),
(66, 103, 70.00, 'cb', '2026-05-15 16:01:00', NULL, '2026-05-15 16:01:32', '2026-05-15 16:01:32'),
(67, 52, 210.00, 'cb', '2026-05-14 16:56:00', NULL, '2026-05-17 16:56:35', '2026-05-17 16:56:35'),
(68, 106, 64.00, 'cb', '2026-05-20 16:35:00', NULL, '2026-05-20 16:37:21', '2026-05-20 16:37:21'),
(69, 106, 64.00, 'cb', '2026-05-20 16:35:00', NULL, '2026-05-20 16:37:21', '2026-05-20 16:37:21'),
(70, 106, 64.00, 'cb', '2026-05-20 16:36:00', NULL, '2026-05-20 16:37:21', '2026-05-20 16:37:21'),
(71, 106, 64.00, 'cb', '2026-05-20 16:36:00', NULL, '2026-05-20 16:37:21', '2026-05-20 16:37:21'),
(72, 111, 38.00, 'cb', '2026-05-21 14:52:00', NULL, '2026-05-21 14:53:00', '2026-05-21 14:53:00'),
(73, 112, 14.00, 'liquide', '2026-05-22 09:09:00', NULL, '2026-05-22 09:09:15', '2026-05-22 09:09:15'),
(74, 110, 125.00, 'cb', '2026-05-22 16:04:00', NULL, '2026-05-22 16:04:19', '2026-05-22 16:04:19'),
(75, 98, 59.00, 'cb', '2026-05-22 19:22:00', NULL, '2026-05-22 19:22:47', '2026-05-22 19:22:47'),
(76, 118, 20.00, 'cb', '2026-05-23 15:35:00', NULL, '2026-05-23 15:38:43', '2026-05-23 15:38:43'),
(77, 119, 40.00, 'cb', '2026-05-24 15:27:00', NULL, '2026-05-24 15:28:38', '2026-05-24 15:28:38'),
(78, 114, 32.00, 'cb', '2026-05-25 15:03:00', NULL, '2026-05-25 15:03:36', '2026-05-25 15:03:36'),
(79, 120, 152.00, 'cb', '2026-05-25 15:56:00', NULL, '2026-05-25 15:57:38', '2026-05-25 15:57:38'),
(80, 99, 118.00, 'cb', '2026-05-25 15:59:00', NULL, '2026-05-25 15:59:21', '2026-05-25 15:59:21'),
(81, 116, 279.00, 'cb', '2026-05-25 16:29:00', NULL, '2026-05-25 16:29:51', '2026-05-25 16:29:51'),
(86, 124, 64.00, 'cb', '2026-05-26 16:04:00', NULL, '2026-05-27 09:47:14', '2026-05-27 09:47:14'),
(83, 126, 192.00, 'cb', '2026-05-25 16:42:00', NULL, '2026-05-26 16:42:39', '2026-05-26 16:42:39'),
(85, 121, 64.00, 'cb', '2026-05-27 05:41:00', NULL, '2026-05-27 07:23:12', '2026-05-27 07:23:12'),
(87, 123, 38.00, 'cb', '2026-05-27 09:48:00', NULL, '2026-05-27 09:48:22', '2026-05-27 09:48:22'),
(88, 127, 64.00, 'cb', '2026-05-27 15:51:00', NULL, '2026-05-27 15:51:17', '2026-05-27 15:51:17'),
(89, 134, 14.00, 'cb', '2026-05-28 14:25:00', NULL, '2026-05-28 14:25:43', '2026-05-28 14:25:43'),
(90, 129, 76.00, 'cb', '2026-05-28 16:13:00', NULL, '2026-05-28 16:13:22', '2026-05-28 16:13:22'),
(91, 96, 32.00, 'cb', '2026-05-29 08:04:00', NULL, '2026-05-29 08:04:14', '2026-05-29 08:04:14'),
(94, 139, 76.00, 'cb', '2026-05-30 15:04:00', NULL, '2026-05-30 15:53:03', '2026-05-30 15:53:03'),
(93, 138, 54.00, 'cb', '2026-05-30 15:39:00', NULL, '2026-05-30 15:40:44', '2026-05-30 15:40:44'),
(95, 109, 117.05, 'cb', '2026-05-31 16:07:00', NULL, '2026-05-31 16:07:38', '2026-05-31 16:07:38'),
(96, 131, 89.00, 'cb', '2026-06-01 16:33:00', NULL, '2026-06-01 16:33:44', '2026-06-01 16:33:44'),
(97, 122, 230.00, 'cb', '2026-06-02 09:00:00', NULL, '2026-06-02 09:00:58', '2026-06-02 09:00:58'),
(98, 125, 178.00, 'cb', '2026-06-02 13:58:00', NULL, '2026-06-02 13:58:49', '2026-06-02 13:58:49'),
(99, 141, 32.00, 'cb', '2026-06-04 10:13:00', NULL, '2026-06-04 10:13:05', '2026-06-04 10:13:05'),
(100, 148, 82.00, 'cb', '2026-06-06 06:51:00', NULL, '2026-06-06 06:51:09', '2026-06-06 06:51:09'),
(101, 145, 38.00, 'cb', '2026-06-06 07:01:00', NULL, '2026-06-06 07:04:14', '2026-06-06 07:04:14'),
(102, 153, 38.00, 'cb', '2026-06-08 07:39:00', NULL, '2026-06-08 07:40:07', '2026-06-08 07:40:07'),
(103, 152, 38.00, 'cb', '2026-06-08 07:56:00', NULL, '2026-06-08 07:59:02', '2026-06-08 07:59:02'),
(104, 155, 41.00, 'cb', '2026-06-09 15:32:00', NULL, '2026-06-09 15:33:11', '2026-06-09 15:33:11'),
(105, 156, 54.00, 'cb', '2026-06-09 17:13:00', NULL, '2026-06-09 17:13:35', '2026-06-09 17:13:35'),
(106, 160, 76.00, 'cb', '2026-06-10 14:44:00', NULL, '2026-06-10 14:45:55', '2026-06-10 14:45:55'),
(107, 163, 76.00, 'cb', '2026-06-11 15:38:00', NULL, '2026-06-11 15:38:55', '2026-06-11 15:38:55'),
(108, 44, 98.00, 'cb', '2026-06-12 12:56:00', NULL, '2026-06-12 12:56:59', '2026-06-12 12:56:59'),
(109, 158, 52.00, 'cb', '2026-06-11 14:09:00', NULL, '2026-06-12 14:09:39', '2026-06-12 14:09:39'),
(110, 159, 54.00, 'cb', '2026-06-12 14:46:00', NULL, '2026-06-12 14:46:27', '2026-06-12 14:46:27'),
(113, 157, 53.00, 'cb', '2026-06-13 09:59:00', NULL, '2026-06-14 12:56:39', '2026-06-14 12:56:39'),
(112, 166, 38.00, 'liquide', '2026-06-13 15:14:00', NULL, '2026-06-13 15:17:38', '2026-06-13 15:17:38'),
(114, 151, 140.00, 'cb', '2026-06-16 15:40:00', NULL, '2026-06-16 15:40:10', '2026-06-16 15:40:10'),
(115, 107, 577.00, 'cb', '2026-06-17 16:01:00', NULL, '2026-06-17 16:01:57', '2026-06-17 16:01:57'),
(117, 133, 256.00, 'cb', '2026-06-17 17:19:00', NULL, '2026-06-18 06:37:20', '2026-06-18 06:37:20'),
(118, 181, 25.00, 'liquide', '2026-06-19 15:47:00', NULL, '2026-06-19 15:48:09', '2026-06-19 15:48:09'),
(119, 182, 64.00, 'liquide', '2026-06-20 10:19:00', NULL, '2026-06-20 10:19:44', '2026-06-20 10:19:44'),
(121, 186, 68.00, 'cb', '2026-06-20 13:36:00', NULL, '2026-06-20 15:27:46', '2026-06-20 15:27:46'),
(122, 185, 50.00, 'cb', '2026-06-20 15:54:00', NULL, '2026-06-20 15:56:29', '2026-06-20 15:56:29'),
(123, 169, 174.00, 'cb', '2026-06-20 17:10:00', NULL, '2026-06-20 17:10:52', '2026-06-20 17:10:52'),
(124, 147, 298.00, 'cb', '2026-06-20 18:45:00', NULL, '2026-06-20 18:45:38', '2026-06-20 18:45:38'),
(125, 154, 210.00, 'cb', '2026-06-20 18:45:00', NULL, '2026-06-20 18:45:56', '2026-06-20 18:45:56'),
(126, 190, 50.00, 'cb', '2026-06-22 15:10:00', NULL, '2026-06-22 15:10:20', '2026-06-22 15:10:20'),
(127, 192, 32.00, 'cb', '2026-06-22 16:34:00', NULL, '2026-06-22 16:34:28', '2026-06-22 16:34:28'),
(128, 194, 40.00, 'liquide', '2026-06-24 08:15:00', NULL, '2026-06-24 08:15:25', '2026-06-24 08:15:25'),
(129, 187, 119.00, 'cb', '2026-06-24 14:56:00', NULL, '2026-06-24 14:56:58', '2026-06-24 14:56:58'),
(131, 193, 20.00, 'cb', '2026-06-24 15:37:00', NULL, '2026-06-25 13:20:14', '2026-06-25 13:20:14'),
(132, 199, 48.00, 'cb', '2026-06-25 14:28:00', NULL, '2026-06-25 14:28:48', '2026-06-25 14:28:48'),
(133, 184, 299.00, 'cb', '2026-06-25 15:46:00', NULL, '2026-06-25 15:46:59', '2026-06-25 15:46:59'),
(134, 201, 25.00, 'cb', '2026-06-25 15:54:00', NULL, '2026-06-25 15:55:01', '2026-06-25 15:55:01'),
(135, 205, 58.00, 'cb', '2026-06-26 13:38:00', NULL, '2026-06-26 13:38:05', '2026-06-26 13:38:05'),
(141, 197, 64.00, 'cb', '2026-06-26 14:26:00', NULL, '2026-06-28 19:05:21', '2026-06-28 19:05:21'),
(138, 167, 430.00, 'cb', '2026-06-26 15:05:00', NULL, '2026-06-26 15:56:42', '2026-06-26 15:56:42'),
(139, 207, 50.00, 'cb', '2026-06-27 13:53:00', NULL, '2026-06-27 13:53:14', '2026-06-27 13:53:14'),
(140, 146, 315.00, 'virement', '2026-06-27 19:04:00', NULL, '2026-06-28 19:04:51', '2026-06-28 19:04:51'),
(142, 209, 64.00, 'cb', '2026-06-29 13:28:00', NULL, '2026-06-29 13:28:40', '2026-06-29 13:28:40');

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('xmu9Q3peXqlOMAA0g1lY48y3w34bAaCLuCMnXdH3', 1, '85.31.169.73', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiNFZ4QjNyYXo1cGJSYWtXSzROdFNLY0VUME80NGNMZGh6RmJaYzFUdSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9hcGkvbWVzc2FnZXMvdW5yZWFkLWNvdW50IjtzOjU6InJvdXRlIjtzOjI3OiJnZW5lcmF0ZWQ6OjFzYWlwdjZFNlhiSTNZa3EiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1782753759),
('v49CPTcDdtaeZvk4a7KFBwdBg0RKf9aTiAUpVe5J', 2, '85.31.169.73', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoieXAxZGREVE9VQ1J2TDFhMUY5MWhDVlJpT1lGWlAxQWZ0cnBDRUM4WiI7czozOiJ1cmwiO2E6MDp7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9hcGkvbWVzc2FnZXMvdW5yZWFkLWNvdW50IjtzOjU6InJvdXRlIjtzOjI3OiJnZW5lcmF0ZWQ6OjFzYWlwdjZFNlhiSTNZa3EiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1782461883),
('GoM9JyLv6DOgONZ1J6vHY233UQnRVZNLoR8LfbDy', 4, '127.0.0.1', 'Symfony', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiMERCdzI2Z0dNTjBrS0lwbVo3TFQwYk5ma0dGd0F0bVRHT2hDcVd2cyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781640819),
('JP3HJUUy2gBwzPxgN4jD3aeAksbAeDMRdXN3jMYh', NULL, '54.195.215.111', 'Mozilla/5.0 (compatible; NetcraftSurveyAgent/1.0; +info@netcraft.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibGJGSXpFWmEyVExrWEs3WDFMQllXcllnOVVsMXpNMTlmcUVqM0FLdSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cHM6Ly93d3cuYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vd3d3LmFkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773297824),
('ywWECYfPHQCRo5NeL7btqnwA0ip8BkNJnvAPxXKU', NULL, '66.249.93.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUUxsSlYzSFhXRjlHSERTbnZad1RkRVJYTWhjeHBoUm44QUJPVzBRNSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NzY6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL3VwbG9hZC8xMGIwNDg4MS1iM2YzLTQ3NTQtYWNkNi0xYjQ3Y2JkZjJkMzIiO3M6NToicm91dGUiO3M6MTE6InVwbG9hZC5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773137089),
('ekjUbq8JZgtNYUG9OOR4CUT2UDFiQL8UoqFMVqRG', NULL, '66.249.93.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNWxzSU9uZ21OaER5elVIeGVUdlowM3hiMmZtWFJSd2xibWVRSTlxciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NzY6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL3VwbG9hZC8xMGIwNDg4MS1iM2YzLTQ3NTQtYWNkNi0xYjQ3Y2JkZjJkMzIiO3M6NToicm91dGUiO3M6MTE6InVwbG9hZC5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773137089),
('S6uji3LHBBh6OpsrrnIJSsqXGUOoISMEIwue1EVn', 1, '85.31.169.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiME9pb0k2RU5PdkdrS05VaXgxVDFmdjM3TkZWV1ZqWlRZc0o5RkJReiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9hcGkvbWVzc2FnZXMvdW5yZWFkLWNvdW50IjtzOjU6InJvdXRlIjtzOjI3OiJnZW5lcmF0ZWQ6OjFzYWlwdjZFNlhiSTNZa3EiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1782753800),
('HtrjpYLBMCfpv0sASRAizkSfaKs3FHjo7Bw1smLI', NULL, '66.249.93.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSllPRWtXcWFzV05NVUFNNHJGSkw1alY4WE82Rk1ld2I1akxqblQ3ZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NzY6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL3VwbG9hZC8xMGIwNDg4MS1iM2YzLTQ3NTQtYWNkNi0xYjQ3Y2JkZjJkMzIiO3M6NToicm91dGUiO3M6MTE6InVwbG9hZC5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773137088),
('a03voNcXm0T3S4GeDCxflzuOLYIUXqYumj3rjHhS', NULL, '66.102.6.163', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT2I2cndIOVJ6ZHdESWd4SlVDRVJpQmxOU1E2Y0ppRmdhRVBqOTdsTyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NzY6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL3VwbG9hZC84YjNiNDcxZi04Mzc3LTRlOGEtOGVkYS1lZTkwZDgwM2E5MGQiO3M6NToicm91dGUiO3M6MTE6InVwbG9hZC5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773002737),
('tytH6tUQPf5DjuBofAsFtpJu3Pj4EpF9xZ65wEW3', NULL, '66.102.6.163', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSkdKR1h6WkxVbThUd2dHMHYyQmlnNEl0WXMxSTBVMzlxb1NLSDYyeiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NzY6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL3VwbG9hZC84YjNiNDcxZi04Mzc3LTRlOGEtOGVkYS1lZTkwZDgwM2E5MGQiO3M6NToicm91dGUiO3M6MTE6InVwbG9hZC5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773002737),
('qXuL0xcD8d0uidZZykyiZZQB67R4fY9InYzCzRuT', NULL, '66.102.6.164', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQVZtMlBDY1dETGVUUFBVeXRmaGlFY0YydFFhZ21rR0tiV0NVOTc3YSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NzY6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL3VwbG9hZC9mNmNlNWI2YS1hZGI5LTRmYjAtODUwMS05MWUwNzg2ZDFmMzMiO3M6NToicm91dGUiO3M6MTE6InVwbG9hZC5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1772994740),
('eVGOF3kfuYFtVji4Ur8zARQe4BD05PTIFZWZdwsj', NULL, '66.249.93.201', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMDNoQzZXNjFlcmZIbWZNd3pSSzFKSGxHRjRtTjQ1Z3Awb01RdU5lbCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NzY6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL3VwbG9hZC84YjNiNDcxZi04Mzc3LTRlOGEtOGVkYS1lZTkwZDgwM2E5MGQiO3M6NToicm91dGUiO3M6MTE6InVwbG9hZC5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773002735),
('RYbzqJOezHGuWKNXx6Xt24r9pIzXgjj7n4i9UMOC', NULL, '66.249.93.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMmF3M3VQVDFpRVZBY2hNMXZOeUsxT3NXa05UaTFjVTBic2VwSEdUQyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NzY6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL3VwbG9hZC9mNmNlNWI2YS1hZGI5LTRmYjAtODUwMS05MWUwNzg2ZDFmMzMiO3M6NToicm91dGUiO3M6MTE6InVwbG9hZC5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1772994738),
('gLqaoqcgN2nhb2tWnFO7r5rJqgRJIUpwze50fyo6', 1, '88.182.46.54', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSW90cW1JSW1oczhyUk1VMXpKTTFNbzd4alFqMWc3WWRvQzkzNzQ2YSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9hcGkvbWVzc2FnZXMvdW5yZWFkLWNvdW50IjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1779032696),
('g0vp2qPZ1Krr4Kr3DxwqQdqWhYNkVSq1qaruPhIB', NULL, '35.87.228.50', 'Mozilla/5.0 (compatible; wpbot/1.4; +https://forms.gle/ajBaxygz9jSR8p8G9)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYWtqQzRHb2l5YzVBdUVCRzBJekc5cW94VTB1cmlnVzd5N1REVHpGSSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773091306),
('oH5E6OyqeKJ1mudCwkYdeIhXruPoq78MfzCquto0', NULL, '159.223.128.197', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicGg2bHpPNmRaNHkySjBrVG5ibWk5ZDcxUklsZ0hHcENZMTd1dkt1NCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1772954827),
('PKOLRvBfmednQWCy3NzESIPGmEG9FP4gBdTsQtc1', NULL, '66.249.93.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNDRnM1dMSm1ydXJzamc3dk9obDRrWnR2dzcxMDRyaURUdkg3bGxBQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NzY6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL3VwbG9hZC9mNmNlNWI2YS1hZGI5LTRmYjAtODUwMS05MWUwNzg2ZDFmMzMiO3M6NToicm91dGUiO3M6MTE6InVwbG9hZC5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1772994739),
('bHqpYiIE8HZd7nHcTR3dLVCRt8p0JCXAYoqVVVqA', NULL, '104.234.180.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUjJvR0NOUHRLNXlHSVFmZ28yRTZDb1ZsMUV1U0FMZFpKMG5GRlNyZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1772660745),
('tYr6hXqTzvF62TrIXqESnzFBwDkdDwVVrcnwxJ3E', NULL, '159.223.128.197', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidngycG9RSDJEUWNXMDZqaGVJWUkwbm1TWU9oUGdJaTExWEpnZVgyRyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1772954824),
('vHw6gNfqTOhCD3kyMu9AMi91YzmKsldIFkOABmqP', 2, '88.178.9.183', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiTHhlSDJUZ0hRbGVNNVVKRDNNb3NaU25Cb0FCOHdjdWdld3l1bHFnSCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9hcGkvbWVzc2FnZXMvdW5yZWFkLWNvdW50IjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1774600884),
('1YfLSYndupMGwPHbb6aF4hqJ7yGaD0Gx2xTkm2vU', NULL, '104.234.180.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMHpSbUpxVk95Wk9ya1VXQ1p5Z2V4OTN5czY2emJtTTVaMlR1RFNPSyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1772660743),
('RwV2eZJIzmgsN2qgFnWemPge3Bvps92e1WxZMMQn', 1, '88.178.9.183', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiWVVwZldhcjVRTmdtZzhqS0F1TktJNm1GaWhPZWtPSHdaOWxyeFlGVSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9hcGkvbWVzc2FnZXMvdW5yZWFkLWNvdW50IjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1774600885),
('g4l02f4CM4kgqK7mab4wrTnDABk68VX3L0Tupdp5', 2, '85.31.169.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiRmhUOTU0MUxWbUlCRkxRVnFSWFpIcUhSdmtWMFcxd2lxVVVBZzN5aSI7czozOiJ1cmwiO2E6MDp7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9hcGkvbWVzc2FnZXMvdW5yZWFkLWNvdW50IjtzOjU6InJvdXRlIjtzOjI3OiJnZW5lcmF0ZWQ6OjFzYWlwdjZFNlhiSTNZa3EiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1782753803),
('NLggTR06tbJSXReZTEQUajps4fFGyv12GKR2HUtU', NULL, '34.230.220.241', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQjJEU29oQUJJYVpNZnFwNWVReUU4cGNCVnRjODBYMTA1bUxUS2JVcSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cHM6Ly93d3cuYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vd3d3LmFkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773461347),
('WzQloejJnVY3mqH7hfTnJaPFEEUvJE2FTVmQwi4X', NULL, '104.234.180.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTTZiN1kzM05nUlIwYzNSN2c5NjN1UFdrN1VJTXR4dXBKV0hIZndCbyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1772660743),
('azJPCUmtmbCs3Dlsk8fdUaznhWoPbMwpBrFzXShf', 2, '88.182.46.54', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiQ1RsSTdXQVZpaElld2ZEOGVVZUVHMHBadUxpRU9vdWpBMDc1RzBGVCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9hcGkvbWVzc2FnZXMvdW5yZWFkLWNvdW50IjtzOjU6InJvdXRlIjtzOjI3OiJnZW5lcmF0ZWQ6OjFzYWlwdjZFNlhiSTNZa3EiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1782753782),
('eoPK9Xees6gvZs62f3XKWtSGKvhGo7ujdnmFtg6U', NULL, '16.184.27.125', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoic3E1Y0d1dXUxNERaSzAzSmNhbnlUY3hTT05nQzBwRzd4d0tNTTNpdCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773323281),
('C1H7JnEOGBgK20gTuX2o4cyYdF3R52nJ8f8lbMjA', 1, '162.120.210.196', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiaE9rWkdsV09IQ0locUN1YmNQcVExdmRWNUM1VFpLem9NQmRWVEg0SyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9hcGkvbWVzc2FnZXMvdW5yZWFkLWNvdW50IjtzOjU6InJvdXRlIjtzOjI3OiJnZW5lcmF0ZWQ6OjFzYWlwdjZFNlhiSTNZa3EiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1782577983),
('jH16vRXvyUkdxCQ6nbjwJMYWr3WWKbLlbEyHh2hn', NULL, '66.249.93.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiR3NFSnh3bGFqanY5Tk1PRUc5eDUzSk9KZEtwU0d6bDh4bWFBa3JiTCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773474209),
('PZGhIqVXkfiFOkbMhKzVLXJj3EMv0FULCgxHNWbA', NULL, '66.249.93.201', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiM25ZZk5YNDFxcHBTa0Q2Mnl2Z0IxeUE5cGc5R3dtZ0l4VGozR1pYUCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0OToiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvYXRlbGllci9kZXZpcy80NiI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQ5OiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9hdGVsaWVyL2RldmlzLzQ2IjtzOjU6InJvdXRlIjtzOjE5OiJhdGVsaWVyLnF1b3Rlcy5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1772387108),
('PHr3UPKd2nEwFRhIXPqGG2jqnkJzadSMlsS8Q1LZ', 2, '88.178.9.183', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiTTRvd3dzV3VsbUlrRmxnUzFKdkZSNFdGeUlhM1UyYnZLbXJ4bThpcCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjcxOiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9hcGkvYXRlbGllci9pbnZvaWNlcz9tb250aD0yJnllYXI9MjAyNiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1772301983),
('elqP69qeBNKw4qoyDxlX9Su3VQGcPeVVXteEbfvv', NULL, '66.249.93.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZHJnd1l2NzA5WUc2STd3WGI4QUFXQ3ppbDFSTXFUaTlLQmZvUzlZNCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1772303848),
('7wWWwQz8iZ1bNeuUCzGOVxTGRBDZBlwcq3rzzcgW', NULL, '66.249.93.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQmhpR0tyUThKSUlBdGV2VDQzaFg0RUI4NERWSWpzWWNoOWd4OTMweCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1772387109),
('7iEOCrUdV2s0qCFArLAYDdG37eKqjgm403rley1K', 2, '78.240.104.104', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoibWFaVkdzbVVtY3VSU1ZxTEREaGY3V05aT1JlbElTdGdvamFVaFZVdyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjcxOiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9hcGkvYXRlbGllci9pbnZvaWNlcz9tb250aD0yJnllYXI9MjAyNiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1772304647),
('pdSiDXHmEcF3Q4QzEXePZfJmmTmItwLlXEE7cogC', NULL, '66.249.93.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidGZzelMzekJMOHR1SUJDOWlLWkRHNDJlUjZVaERJVkZOZjNhRGEzZiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1772303846),
('BsZIMwquVs2ToxnolNexFCqdZL7CUnPBGvK11lgL', NULL, '88.182.46.54', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicXpoalZYZHBRWFRHM1BxdWhBNEVzaVRtMWdPSzFSWTZVSldDa1NGUyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773299219),
('KZq5UxlOh5rPKMgWDqC879THgRrikSVFUN9brqXx', NULL, '63.34.171.77', 'Mozilla/5.0 (compatible; NetcraftSurveyAgent/1.0; +info@netcraft.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZ1c2dDl1ZVhRZ1dweDF4U1dINlBNZDNMbFNiS0pzUm1RNUJPdmkzeiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773307577),
('zHbtV59eM5KIzBsVSqFu8TOMsc5GVucl8fVqWUKj', NULL, '34.241.126.38', 'Mozilla/5.0 (compatible; NetcraftSurveyAgent/1.0; +info@netcraft.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSmJjd2g1WHFZNFRrR055c2pRam83R1N6YnZzdDdQcnVobU56alZaNCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773372031),
('5tZpfAbLwmKiorjqcOedJa1xb8TfGkVesUX7fBgl', NULL, '20.112.234.132', 'Mozilla/5.0 (ZZ; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMGtGNEJEUld4Qm5ZbFZWS2hmamIzVXZKQ05jNTFSckJzUU1TcmVNcCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773381435),
('zSA7VNlWfQwVJwE6XHlC0DrTsyiZa3I3Dlz9dUJh', NULL, '20.112.234.132', 'Mozilla/5.0 (ZZ; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieTZTZWNVRFZaVEpWRGdGaVhualJkYU1lQnlYbUVnVDk4WE9ZR2hsYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773381437),
('HQZf0oqZjYSx9Lj6Gyu9OL9EreH5YI198vNMZmQ5', NULL, '52.208.40.54', 'Mozilla/5.0 (compatible; NetcraftSurveyAgent/1.0; +info@netcraft.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiN2s1N3pZUlJuVmZrTzV4MzR5RHhSTmJQNnd5UjZqaDF2RkhnYTU1SCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cHM6Ly93d3cuYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vd3d3LmFkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773387096),
('507ewbaSSv5KQ4TfmVeo0P7OJiJTXtz9eIA0Zjqm', NULL, '34.230.220.241', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWU1PS0t2WHNxYlJ1cG1pTlNuQ1FSYUFkN2J0TUJweTFIbEtsaXdvMSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vd3d3LmFkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773461348),
('6Vzejeb47rNxoBIludia26EjMyH81PaX5010ywwu', NULL, '66.249.93.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieHgzZUtPQUdKV2NvaFlVYURaRXB3MHVsOFR4cWdmS0l0RXcxU1l3eSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773474210),
('WAHLYy184I8bqJ8qbkxDbuFVwDEAfhPt27ybIptY', NULL, '66.249.93.201', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZHhRSHNLbjJWRk9VN0dvMkppT1JZRHoxVWx4ZTNKQk1TbDd3V0VVOCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MToiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbWVzc2FnZXMiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo0MToiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbWVzc2FnZXMiO3M6NToicm91dGUiO3M6MTQ6Im1lc3NhZ2VzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773504802),
('hgaTJIgoIU6Uvsd3ThjM6tuY5lb7Oeq1aZRDlfW7', NULL, '66.249.93.201', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMUZ1NjJicDFMZFJ0a3FOeWpLU2FzdllkRGhiblBoZW52NnE3dTl5TiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1774040216),
('XitzStrDWgpv6XAlMhzWKBYX8dzhPe7XiMwln5O1', NULL, '66.249.93.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidHFncUhnbnhDMzc5Q1Z2UnJlaU1MVGxtOE5ucTNsd1FyVTFrYzh6cCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773504804),
('DwBCohGDnTAyqKLUt8eH03jKGjh0nsNt26iPFflE', NULL, '34.24.164.231', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYmtDZk9sNk0zWWxlU3ZKWnRMT05iMXdLVHVTOEd1ZXdPb2R5aHhqSiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773598347),
('iEyFba4KPFvNziGayleccFG35hdYjt4AInh0DST8', NULL, '45.141.152.76', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibUVDWTV6VHRnQmR3em5FNExxazBQY29rY3V4QmZBMnVkTFJyOERibSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773803689),
('phjE4RLvB6ZDdiExMyF0Nh38yCwQJxuyEliE0Rd4', NULL, '45.141.152.76', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidnVMbnV1S2hGMHByc0tDZ0M3dVdXTVpmdU5MdlUwTGk0VnlHMElZbCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773803688),
('nVIBHPxqbIpj59wJtIxrZJt9PDyyUXX1jw8zq9JP', NULL, '45.141.152.76', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMDhhYzdCN0RYYUloMUtmY3FObGVUNHdPT3V3RVBuQmtWR2hSaFhyWSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773803689),
('6MMkml8rLu15aMJz3oQk71yFjWnBRR5MyxT2NETh', NULL, '52.202.237.12', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 clst-pab', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSnM4QXZkTDdOZG5CVUxOYnE4UFlOTFVabnpKdGtsTUl0T0dJdjhiUSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773969990),
('g1AH9khvaampdvVmufgEMq7cNbkyutUvALd8BcYO', NULL, '52.202.237.12', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 clst-pab', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOFlwZm9LenBoT3NhZzlNVUh1ZDcyMGl0bW1lUnRxUTgwSENOQTBJVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773969991),
('hf5pIzJLRBzhePF7n8BY5mjIiEcpv1ofMQnwSxMU', NULL, '66.249.93.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNUxwS1pvWjVSNVg4RWQ1SWZKaFc0V0NJVUt3Q0xnM3NXVU1kYlNmbCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774040218),
('wGN9XRgjYoCnP7pX0LC17YcAseaGPAKnd9A2NASn', NULL, '66.249.93.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoic01SRkQ1V05VenpuWllZeGU3MDBJTWRRTFRXNkE0RGVsRTRuNk42QyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MDoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvYXRlbGllciI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQwOiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9hdGVsaWVyIjtzOjU6InJvdXRlIjtzOjEzOiJhdGVsaWVyLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774112170),
('vpqCSvWVWPzx4u70Duv3i7f8n3O7PJTwyJAFHeLk', NULL, '66.249.93.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibE5GbHl2cFdzTVBxWm0zRUJsWE5WekxLMXFHd3hObWhweXM0SDY2dCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1774112147),
('91rN06Fbh0k5c4xqmYcKnQI82rYH2xjrcaI0DJeg', NULL, '66.249.93.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWlFDNXpkU3U4MXZtTFpZdDNPY29zZEtkaG9DQXdLTGVsYzYyTjZ1MiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774112147),
('otWG9YUFXjodzq5bJuUz6gxlI7m8OKNW3pmHxpRn', NULL, '66.249.93.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQjdEUjJwamZyNU45Zkd0ZlpwbHlPSW5jd2dibG11ZFJwWHVVd3JRSyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774112170),
('oBUEOOuSDvpbLjz9QdIV3b61aVPlpu3fA3wkzIwC', NULL, '66.249.93.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWGZSemZsN3l0OFVkNHRjSkVFcG16ZG42RGd0NkYwQTdMT2hEWWFCbCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MDoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvYXRlbGllciI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQwOiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9hdGVsaWVyIjtzOjU6InJvdXRlIjtzOjEzOiJhdGVsaWVyLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774718131),
('enbKlDn3XBO7aioZtsbevRwKYRDOLm9B5OvHS3VH', NULL, '64.23.177.14', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUXNiTW9HbUN3Q0Q4dXc5aGpKUk9KY3MzbEptWGxjamVuTEZva0hYeSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774186080),
('79CE2TgqLLu5cf8vXTcmrdBwDXsXD4OUW80Nq8Ee', NULL, '64.23.177.14', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNzlmeWtQY2pYRW5mQWVOMTZFU1RSVFBjNVdXVFdmdWU1d3BXNFFKRSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774186086),
('PNQcG6RQ6njPs8jVn0ygDJSxrrAFqN7BYQ4jkrhY', NULL, '3.82.234.136', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiU281emxSMmI4UGFiV1prcGRvTlY5UUhaeVRrR2Y2T1RGelFEUW5HaiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774210341),
('2mOS4g6B3LCqJbLibcRMMcK9MXEacBhUtrSXV95A', NULL, '3.82.234.136', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSW1LdGttOVpXN1NwWTdCNEw4ZVhQNVBDNWhIenpKMXE5ZERKSlRvdSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774210343),
('f61fxh8M1SpOv1RMX9bhYKJtViP21xkBY7YxXdPj', NULL, '104.164.126.208', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaG45TkxNVEhMTURVZzFkTTJFZGxuSFEyNENncjhYc1h4MDVVR1Z4ciI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774236164),
('X3RoU9sjosCp9YCJ0tlkdG13PjtQiGooTzl4sSrW', NULL, '104.164.126.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNGtQcHpzWGdSTzRTV05YV3NMNlRaN29FWHBoRHdPNzl5TFVUYTl4RSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774236177),
('p2OrtpQvU1FpkMnggtT59TFZ4PSuY2CBBZNluqh0', NULL, '172.71.148.132', 'Go-http-client/1.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQkllQ2gzZU9rbFVRZnRNVlYxcDhMOTg5aW12ck1heXNPcE5nVlJZNSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1774236175),
('5DcJoOHpmxLhvuP70Wj26RDuV0aabhnfyVtnpzx6', NULL, '172.71.148.132', 'Go-http-client/1.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT1BoWFpMTXJsa0tCN3RvV3FpaUFxbWlXV2E4eUpvd255UGpoSUFXVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774236175),
('6EuD0ruO6MoOXFUo8407PgZMJAHQUtV6CYmNhNGA', NULL, '77.81.142.141', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZkpNVXplbHBpZ2VyNDV1bzhybTloR0NjZnNMWWhhY2FNV3BuWkFJWCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774236192),
('pkfTuQYXoRnbHnWTQU9NPVNl1BmN0xE2jdfnqfp9', NULL, '34.72.176.129', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiN1B3RGhvS3ZMNzVpWEh0aVpFQWFZN2NCMHBrWW9PMFBNV0t0c0k3YiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774236210),
('wZPjHMoNiOrsrIAN9dl8NbaecekgQwZhoEBsv4f4', NULL, '104.164.126.83', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVFY4c3NEa0FDaEk4ajYxU1B2TnhTRkhrWVpPSFJaWjN4cWphRDBHVSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1774237540),
('tLRhEgm9ferT3gFr3tTasxGGkzp21yxcQ2NfpDBi', NULL, '104.164.126.195', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoienJENGFsNVk0UDlpaHRTbEJMb3R0NVU0TUJlb0M4eUdwdE1pRzdYNCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774240198),
('QxVhiWTmGKJbCgdqvS6NOIaEPD0Q5A0rTBAcnf13', NULL, '104.164.126.195', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYkU4RGxKMDFhSmFoelpZbUFMRU9Mdm52WElWTHBrUW1nMTVxZzBmUyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774240219),
('9AHJryZBtSfvcm7jfTfWNyUBEROTHueUEuxmgyBg', NULL, '34.58.7.111', 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSktwMlRUYmVXcTBkRUtCeHU4QVphNkp6aU1wYlEzamE4djh5alBCZiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774244911),
('x2ZPa2d88Iq3FKZ5AhkqYiQu2XkVrjQBuyPSYIRv', NULL, '45.185.226.168', 'Go-http-client/1.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiN0RSR2RPQkdpU1B4Q09Qb3ZNRzlRS0djTnM4cVNJbXBKSnZLcnExTCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1774247395),
('ZLhel3qmQMdFxSZx5hawlkoLNR5jyqdCgFcUI90Z', NULL, '35.238.2.98', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibnpGbHNMRURtTkZyNEp4MnMxbGRFUHlKQldUc283TGM5TXpEME9oZyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774286754),
('WvFdhUBubEpq8BECh8hTJeLBPzbsUr3LASh3pPpg', NULL, '35.238.2.98', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWEhIZnFPUlZDcHgxMlVsdFZUTHM4YVRBREh1TTNWSXFpN1lwc3Y3NSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774286755),
('in2MUkib16R6Cj7s7rTkuJyCT549iBKqzciz7xRf', NULL, '52.69.69.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZVZ0WGdUY2NSMm1tU2RsREQxdmNxVkRqeWhtYXYwSlZwRWVmNElMdyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1774318481),
('pAXo7sk80shcRqpYB6ioUtLY0Oy6rXzQ1Ycsp7Pg', NULL, '137.74.246.152', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTTRxRWJoc2pucFhvTkZPMVZlUHJSOWp2VEkwU1VqNWxGZkdoZ2h2UiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1774335575),
('xHWXmrX5iYklyeqmkEpx0IoDgujGSP2eMVQw27dK', NULL, '137.74.246.152', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWXhmTVdHTlA5SkZSeVJER29qbG8yUE9ra0VLVTNlS2N0RUNhWGVoeCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774335577),
('QziYMYrIEi2YycqkhEo3FfiOysORa1SBul7fHMS7', NULL, '185.8.106.156', 'Mozilla/5.0 (Linux; Android 6.0; CAM-L23) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.89 Mobile Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZU1SS0VEcjE0T3pKTXJYNGl3eVZsMnQyMk9DRmxwSkNUQkM0cTNEMCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1774347870),
('wj5StLAw9isXLAlQApsiCmbyWv9lqJMqzlj8Ni0k', NULL, '45.92.86.22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYzRPak1YY0xiczJndk9oeVVFVkwxMjYyUDZFUVlMbVVwazQ3MlRDUSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774350681),
('13oFqKvzgeRFba3gdpkh7uEpTNa1Vqed0AsCJpQI', NULL, '45.92.86.22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieVZKQ3J0aTdOSnkxN0ppbERNSXNXR1BQRDBaN3BiR0VPdjJzcmdzdSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774350687),
('fMpGnIJY5x2XGFDc68ecTqYWTDCSjGgtNgdpZSHK', NULL, '34.59.160.94', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYUw1ZFVFbXJ2RmRHVzRoNDVOdXVpWXF3cVdHNXdjRkpnVVBhUFozViI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774368996),
('w28VxMUaEoWV2e8fdHWtV7QhyX5oPi3Owith5dAk', NULL, '34.59.160.94', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTFpMUUxJc3dBSUpJZmhMM09nVmpuQzNvNWlEbFd6Ukdta1BCSHcyYSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774368998),
('GtrqypPQbpAbOzEtMrrVGLFOR8bThOQLIAPgJxvv', NULL, '34.42.23.149', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTGhPN2hIYmxGUEx3ZGl3cnlwdDl0MjZJeVd3NTUyRzBJclk0MFhJZCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774371540),
('bcskhVWWOamW4rRN5qH7FuZK5CDQxoB73XlNriEh', NULL, '34.42.23.149', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN3M5b1FMWWRmNUd4UElSQW8xbnBXWm1qT0NhemVYeTI1QmxiWkNWQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774371542),
('jdLPehXOobDDZCu04QUdloRit6YFhFUMMke3CIZy', NULL, '34.241.195.207', 'Go-http-client/1.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibXhtYlZGUDBSOW5qVWxDeDBnZVpkTG1SOXBvZ3YycGdjRzE0NXlRNiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1774374178),
('BWTrK3InEYLBPzjU6lxjG2hIAt7bSTuKlemRphys', NULL, '34.241.195.207', 'Go-http-client/1.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOUtlbUNuU1lQNHppQ3c1dFMwcWh5OGJJMFdZa0NtZFNQeXBqQ1ZpYiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774374178);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('BsrZDKywOWPp1yFypH6Q718Q210es6MgRSsuzp1G', NULL, '136.109.47.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQW16cGhGb3RRUVJIWjdScnVWQVNTMDhmUU1WOGJBU3JHd2hCV3VxdSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774380103),
('ioAOHWe0YzspXEpcxIqMfJW1hsL8GeMc5FusAHVF', NULL, '159.203.37.15', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMlBpeThOZmRQSWlJNDNHQ2J0YkJVUHdKaHhNYldJYWV3a0RwUlRZVyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774405023),
('3fPETMiKzaHWyC7GfcdP8adhTrmgdYWMzNPcTW0f', NULL, '159.203.37.15', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZTdVTldtSHRXMWNLZGJOMDI2VmRqRHlkZUJzcjRtdnlXdFRVY0o5aiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774405028),
('PIaJeCNqzCOsRjvwUK3ZVkWzqesTF0o5JEsnX8UR', NULL, '34.127.15.231', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVVZXdHVLOUhoR3JuR1VpRnJ5dHpqWTdoS0dUOFBHUTBLRm5UcW5MOSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774438544),
('qCIrkUuULikyn8rc4s2lZ6HFuMgtnBpKgIyExqpU', NULL, '34.127.15.231', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU2h6Y2dKeHlTbGFHaXhjQzQ3RWpXdWRHWjZ1c01xSFZKMXJZOTlqTCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774438546),
('ST8KnRgxVXw2yuAGfgr3SfnyVlkXPHP14PftuhAx', NULL, '34.136.233.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVVNkUFVxN2lhZHNPSkZvWHlUckRMekFkRExtYUR3Y3Z1eVZiZ0JPbSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774445138),
('M9WJl8zhO1TNQa0hDfgZ9cTGGrnUVMKwtxLJSPDv', NULL, '34.58.102.174', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicFJsbnA1ajUzVnhvZ1puYWx3cmZQd3dudjZtVmhoR3hveVdSOGZ5YyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774453382),
('yUwrtAcdWojyFlKP90CnZ3C4n44gNEtECD0ioF3K', NULL, '34.58.102.174', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRWl1UG5JN0dxVFZmSW1MRXQ3QmlsaXFNNTJwNDhOZXdkdDF5dFNWZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774453385),
('DVMA2y8O6uQuli8o8zzZtP9YhNhK3ZhgSH395T5E', NULL, '45.148.10.51', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaFl5Tm1rTVExWTJER3FJQWRnZlVodXc2TFNVSk5vZFNXaFowcmk0biI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1774474958),
('Wmb9gp7HgixpRAYcIYYi1nYGUO9f4eH7joCSaSth', NULL, '45.148.10.51', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUEFheU01V0dNNkZBNFdjR01PZFNiRTJ5VVJzeEhNa1dDUVdScWtNRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774474960),
('BOz4bUe43n0vgx7OfiV3COdWL0EQh5moZnrSEFVn', NULL, '45.148.10.51', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQVpXeWF1RzN4V2FVallmZUVtRFk2NDljb1pZbktGUVZMUVc2QlBnOCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1774474960),
('AN7zNseIVYsZPmanJJa7dht7CWeCPfBVteSWFkrZ', NULL, '45.148.10.51', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic3BGR0YzQTRqREJwQnhpcDY0Vjk4OUZoWklZVlJTQXFSbkFsdXFMWSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774474960),
('keRe1Dic8t4MgW2dgTiXWpqE2UfffKSrAdjpT44b', NULL, '45.92.86.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUEtGMXBhbzNveVNHTnh2Tk1jeFFnaTVCWFNVUFAzZFBsVkpGczg0YSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774523175),
('MDB7i0VZglLhq4z8iNk8OkVYebAMoaIxl7Emjb5w', NULL, '45.92.86.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiajdZSjdOQ0FlVTFESU1hVjZKR1hYYmhnVFdVSThZVjRhd1ptYlVRWCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1774523179),
('aee0sxa1yp3sl1evCC3qCjAfVeSdrwFfzaCRdWmq', NULL, '45.92.86.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTkNlR1ZHQjV5b3dNS21qQ0UycjBYeDlqN3czYjlnTzhtTE9LNldHUCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774523182),
('aIsIkrMo7d2BF0WfMvcQZCFPj5oAf27rZnyrH8Nv', NULL, '16.184.32.185', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTFlwdzRCQjVPVExvM0JWZ2RTd2dhMlJyVU5MVUV4dnZocHRSblcydCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1774554057),
('SJEEVT8XRlbuLnqYyQJ73HJtYuXfbqGcwi4tfrJh', NULL, '159.89.20.13', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUWdTZWNtams3djFhTG9VekJEa2czeXRqVGd5Y1Bpc05hcnptRmROSCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774572671),
('HXougTHgdYf5Cgv8da3BjRgKmEgclay4yaWi0XUt', NULL, '159.89.20.13', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRVVpMzFYeGo3Zk5YRlNVNkNUWEwzVzZBcW1Hc0tPTzZ3QXRERlRpVCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774572678),
('QU5h69k0nnLen5T7KRp3l2jnoTU9kyhHjVHffcFY', NULL, '209.50.63.100', 'Mozilla/5.0 (X11; Linux x86_64; rv:132.0) Gecko/20100101 Firefox/132.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWVl6eks1ckI1T21oRGhCWTdaRlRDUEU5bTlsQkI5YlJzanQ0dnlxUSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1774589000),
('MexOskDxFR6pvgLHvycrygLY6uymnNkTUWDdunWL', NULL, '209.50.63.100', 'Mozilla/5.0 (X11; Linux x86_64; rv:132.0) Gecko/20100101 Firefox/132.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTXBnWXVGRHN0M1NFaDFZU0ZHcHZwbU45YVZZdGNZeW52bEpjTHZPUiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774589002),
('tpXEsPCyNmTQstompGk2TSEb76a3kkvlV7YiWJST', NULL, '18.170.3.118', 'Go-http-client/1.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaHZRVjRyRXF3SFBOOWI3UnFjNzM1NkRPTWMzVjNja0ZFWmFrS05UNSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1774670049),
('V9D8FXtkSJxA0JYOVAdxaoq0EhlQTGBmB6xJvvwL', NULL, '45.92.86.84', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaVdESzRGWkhEbmJCOXdab3FoaDVPcm5YenNDNHhjc3I4MTlaaDdiaSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1774675459),
('UWsavCUhL4w9NRD3j9YFkKWkIpiiidXZJHBZ3TPR', NULL, '45.92.86.84', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicnRUeEJ3TWN0ZjBlSW9YRWFpNTdZakd3V3hiTFJneWZLaGJhd0RvaCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774675460),
('tzCeDdn6bVnokQszCMGaadIuTxr9iZn5Uhzp8FH6', NULL, '66.249.93.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTUtnd0xxeTcyZzFKS3FaY000OEdaaG9ZT2V0U3A3ZExnOURlWkNRciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1774718132),
('e8pStNcSiCVddCEfIwqJxLJVoUmywunEeuDM63Wg', NULL, '217.216.37.56', 'Go-http-client/1.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRWhsejhFbnZtUWJ0dW5vanFnd2hNV3p2Z2RIT2hRbG9GYktaVFNHUCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774730735),
('GXmGeuoZ1tyQAuriURuTPBRR8LGsSnzQ6mImhZpn', NULL, '217.216.37.56', 'Go-http-client/1.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidU9KNHUzQ01YMFA5UGlqbldSMWNMQkVMeFpQZmc3Qlk1Qzk5UDZqMiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774730736),
('zPO6i6JleT7awh0HoTAlIkWfpKbRZBSyOxXJqAYe', NULL, '8.229.13.169', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZlNTaEpRVlo5cHVDS2xaUXdad3BEaTJWdFBPd01NSUZ3YW1ZcmRjSiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774869904),
('Yo6wH70nWmZOIEH7xH5m73S4QORfxwVHePRtxP9Y', NULL, '8.229.13.169', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSXA2VkZxUW8wcmlia2FpODBneTZKUEpLVDloVlo5MTNEdVBPNWxpRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774869906),
('kHYFEtK4FReD3IXrtImNGaDRn7TF1LD5lG8bvlw9', NULL, '136.112.194.219', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoieHhnUHFCYjJORXd1TWRqakJqYnJVVTJ1c0V2d3BCWmI4ckM0bzliUiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774878801),
('npKY0HfWsNfa5tYa2DCqSs4USWIKAUWXJCcykXwM', NULL, '136.112.194.219', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR2JVODYwNVY0aDQ5UEI0d1FGcDE3Mk1ObXZXQlZNblFxRVhlRkxLZiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1774878801),
('IphfFwQrX2t4Rc9G1NpnzgFGm98RZNfD7JZMlvgY', NULL, '142.250.32.4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUlE3TWZpeFJoWDY0dmVlV1JlRmVGTDJ3SzVsT2d1bXAxekRYb0RVbiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1775393801),
('Hd3OcT8rO6cwVsvLnIZxzLQlnKYq233R9Zb7oxvh', NULL, '34.57.201.173', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiODltZWRzbXZCZ1E1TXQ2eEdFY2xyTE1jd1FDUFY3RlJhU3ZySkdnRyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1775065417),
('ty6AYZCRs6pBwlHNQisHlx26Q4Y5QIENincUOSxo', NULL, '34.57.201.173', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicklPTGt3UHptaTVXTElvdzNqWFF5ZmFubk5CN3Y3TTJSTkc3YXFUeSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1775065420),
('DK7JlOtmA4x9WVJO2H4EF6N2qItQVNY0rIRyGRnZ', NULL, '37.59.187.20', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:115.0) Gecko/20100101 Chrome/115.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoid2x1QjBGS0xydXYxT1F3aDJ1SEx3bHlNVDN3cXNOdWZESHNMVGxkVyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1775085996),
('jwUFrGt3as6K4Eg83gRfDPCdfi2RGwFkQj7Jt3Zr', NULL, '37.59.187.20', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:115.0) Gecko/20100101 Chrome/115.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU2RLZkJXUktvVUltVUwwRkJvNXl6WU1rcVBmaFhPZmE2M3hMeEVhYyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1775086112),
('gfkBg3jyfcA7OA2RidNkjFOa4CU89rHji0qxqlvm', NULL, '165.22.206.74', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiR0pzbmpvWWFtckluMkVuRThoOHJDV0NucTVVVHhVZGdHbjQ1OGlHeiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1775366407),
('ZY13pjW8QI9tEEoTjlIpOYR7E2vzttgoYp3Fww6v', NULL, '165.22.206.74', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTEg0S1dGaUFUWmdxTHFzNktKZnlkM2VtQzdOUmZCcFFqczJPbFdDYiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1775366409),
('lrALrNeSs0a4agqZikU1syGvodpecwmzkrc6esp4', NULL, '142.250.32.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibGpVZGFEOUhLZ1pKOURCeFVyS0dYaHlVNnV6dHF3ajJXTE5nelFvYyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1775393802),
('6OFw5mxKVsIDyQjexQW6WK87rJp690sbvBE8Nj4c', NULL, '142.250.32.5', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibXY4SlFLMkNXSVJvMkdqOFVpQU5VdVEzT09EeW1tUFBJa2JCdDNyQyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MToiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9jYXRpb24iO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo0MToiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9jYXRpb24iO3M6NToicm91dGUiO3M6MTQ6ImxvY2F0aW9uLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1775393837),
('uoh7mqFj9vqHllCdMchWyfvFKzwWjv81udrv4krw', NULL, '142.250.32.4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidk5SVmRRR2FUVjFucVJSaFNXZ2JwNm9PWGNBbWRlMXYxNVdWNGlXcSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1775393838),
('IVkIfHy9KxdjRVpRbKuq9nz7igyuDt5biPDXroBx', NULL, '44.245.208.153', 'Mozilla/5.0 (compatible; wpbot/1.4; +https://forms.gle/ajBaxygz9jSR8p8G9)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoieEVLc1J2ZjFlUVRGQ05LZTU1QVRvTFFDOVQ4dFE0R3hweGVYRjhMYyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1775510107),
('fBSusW64rjkyNtVKJmShIOv77XIbYnwLHzZi8g3k', NULL, '35.185.103.220', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVklxbzhIZWV6NEJFbGZzWkR0Tk5WNng0TGRDUUU4Y1JIZXZlZ1dqSCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1775530561),
('ZtpnYtO9XogwQbGoWCTSOyC0NfoRj129nkeyFqvC', NULL, '142.250.32.5', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoia0s5TWxMNUgyZEdRUkE1V05jYUZHQzhpallzaDM4RjBLZHpCcXNNMiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1775746377),
('Pro51C5EZ5ePRv9xA5KfyE1utVsxCE6hUNrARmzB', NULL, '142.250.32.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNjNRSkhTM1U1am5XRE5reWxONmdncjg0bmlpZkJGODZucjRHZEg0ciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1775746378),
('z0tXSLPnBLVSFakC0yio2cvaIYuhFJcm9gYIFHF3', NULL, '142.250.32.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidm1oMnNGU1I3a2JZdWdCa2dHSE52R1NRaUFqcU54aUxIb052eGJPMiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1775803076),
('sHqQnZetvuE6EclamMOgLea358tNRv8tV3u8h0rl', 2, '88.182.46.54', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiN3lSbmx4YTg3WnNGTTR3UklWV3dIR3k3dzhYM3RGMEdoVjZJbnQ3RyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9hcGkvbWVzc2FnZXMvdW5yZWFkLWNvdW50IjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1777445174),
('uqHwcfWUv8hDmWxMz6OE7xF96yJSz0LJu1BWxhJT', NULL, '142.250.32.5', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidHloNDVvWWNlTFZpSHJQbW1ZSk1ZQTdJa1VWRU83VEhzd3hqa3l1SyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1775803079),
('2yJFu0IFvV6Y9FFJEpyuuIVeeUsmJI5PK9BaXeks', NULL, '34.240.13.77', 'Mozilla/5.0 (compatible; NetcraftSurveyAgent/1.0; +info@netcraft.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRmxSR240UVJpdnhMRkxJVUxRdlBOY3RONG5vOVFIU29MdU9oeHMwRyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1775810012),
('bqJwQ5zEP4xozXEvkVmbA7H2SeGHTEPCIax26PIk', NULL, '3.253.129.178', 'Mozilla/5.0 (compatible; NetcraftSurveyAgent/1.0; +info@netcraft.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUmhFeGxsdW5jVW9aZGRicndCQ1F3Q2VaYXpMb21ZTnNSakZ4QVdFUSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cHM6Ly93d3cuYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vd3d3LmFkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1775825989),
('lvez5ARnxqzty9mP09ISKuJKRfW8LD4Hf0mEPDTW', NULL, '44.209.43.227', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicnM3bmdIc0hMbnpnT3ZKYnFYZnBTT3dRa1RqbjM3VVNYVjEweHN4USI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1776116710),
('IRnckHqP3OYXDXcuROEtSIfRqFJFQxtlQIo9dJe8', NULL, '44.209.43.227', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMXZJdEEwZHdhR0l2V3hVVVlMZ1kwb3NEQU41VkRoMGxwTjAyeTJoMSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1776116713),
('cbQLLD56YjH21lHHFxGsnzrokgy79747ekGKUhMZ', NULL, '23.27.145.64', 'Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYUlGYk9rWXFZMWtSNnNXSVJqSGFwb0hlNWdYVWViUzM2R1FHNTFaeSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cHM6Ly93d3cuYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vd3d3LmFkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1776496111),
('8GB2bIyHb96vwPJvGmC7UTwACp8QVl8wIGbUuGIi', NULL, '23.27.145.179', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVXBwczB4eEF4c2llOU13QlhDRk92bUNoc0dIcWttaDdEY0RVb1RPYiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cHM6Ly93d3cuYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vd3d3LmFkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1776502269),
('E0W1GmkTkopnEGEm9fOjn6P8oZQiYj578HQk9avF', NULL, '23.27.145.104', 'Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRzFXSVhmYmgyZkVpYUtXSTZoUWZ1TlhvV1RjU2JxOE8wQ1hWMmN3cSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cHM6Ly93d3cuYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vd3d3LmFkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1776582273),
('seY2P1y7hgOObfB7sqNnW0Zf0N72Gmp4Azp9koY8', NULL, '32.194.215.91', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0 ;Build/0220;', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRXJEa0Ztd1ExM3FmdXRZdVZTU3lUS21VVjRSTzRGaEdyQlVYOWZPYiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1776601533),
('ovauPImeyyIJtQqoPZn0yYkcQPNexUddvzQRmuBh', NULL, '32.194.215.91', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0 ;Build/0220;', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSGczZnZKY1prYnIwV21TVnBwc1BGRlhWSmttZXdodlFOMHZqMjNYUCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1776601535),
('DOi9WguWZe89fZJMo0678kNcvfnU22aQhSQmiF4j', NULL, '178.62.206.141', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOEN1QkRoS1gxeW5LRHJGeUtBY3NoUmZBT2tQbkxyVXhEa2pFUVBUZSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1776615632),
('E3MwN0XKpjdzVyTfrnHDoYnFWUtMGTXGeuJoMdKv', NULL, '178.62.206.141', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYU83eTUweUF4VzNOQW9SdTNPQjRlOHhsaHhVZ0xiVGF6cUc2bjFQNCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1776615633),
('EAHLmIGXjzzz3RWV8XAuYTobubMXVfZ8UQugrWOK', NULL, '23.27.145.149', 'Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaUhUTk1tWEE1bVJnZlEyS2M3QVZOOHRZQ3htOHdudWdvSjBSR1hIeiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cHM6Ly93d3cuYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vd3d3LmFkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1776668885),
('IFC1x7r2K1Z7kxkWSddsf5JuELUu8RmCpRfZGbCA', NULL, '3.91.17.244', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibjNLWlFHOElzVldaWndFUTJOSVBrY0J4VXR5TkFFQkc4SFY1NjdZNiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1776754947),
('Die95t7hNcSeyKep8BkKjgQRSgHqHfcFVdpNl7b7', NULL, '35.185.91.137', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaFByOVhjaThHbzQ4aGpqb3hOMjFEeUFqWWdhQjZETGZESkxxQWdjVSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1776758324),
('qsvFTwPPUZhiXxzw6Qh25GjgrrTEf9ySkqarUawp', NULL, '104.198.0.18', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNUUwOEt3ZWM5TWd1ajVralk1SlgxcmhReFQ2ZFhrMk9MZTdQeGFtciI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1776779832),
('Wxd2z1nVtY70zjOM77OTwNy4UJWjxeWkoXjOow0k', NULL, '104.198.0.18', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiY1hhRDh2ZVB4cW1sY0FmYjVwdDNHeDdNS3FvM21mNGFKcVVWZTJBdCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1776779833),
('AQZ8xnZVgrFXlA84YNqyjhvBd0bS04m4aVD0iRtm', NULL, '35.202.39.117', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidWE2cVhnYlZKUTVJaWI1bHhzZ24yWG40aUd0Q1N5YU82cXpweUxodSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1776783753),
('Ma0KJIYVpO3RkZQIzZ1sF2bKY92S4sXHSddpZ7ji', NULL, '35.202.39.117', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNlRtd0VzSE5xbk9tbjMweFJYMVg2TlFxWDlaQ0E1SUJwMzZLUmF0eiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1776783754),
('vqZOhQqKulESvbEFtcokWBaMn5YegdDQTdowCfWk', NULL, '185.247.137.3', 'Mozilla/5.0 (compatible; InternetMeasurement/1.0; +https://internet-measurement.com/)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRmJhdno4YWhsenByc3RjNWNkUG5XNnVpZEFZNmg5bVVwemdsZm90ZiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1776810227),
('A0NrDoWqbyeFVgBiXLkXcJXB5M7TRIz2civ6AObx', NULL, '185.247.137.3', 'Mozilla/5.0 (compatible; InternetMeasurement/1.0; +https://internet-measurement.com/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieTA4Q1k4aDJ5TFR0UG91RWVDSkdMeFB5NUVpSHVtN2M5bzRGd0tEYyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1776810228),
('gULbEbMqnUrUIDLQXzH7CfZZoCF34QXRsYBaRjA3', 2, '91.170.119.27', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiUlpQbVUwMXhsOVFOZHlTUEhlRHlZM29CWFo0aVczTm9MWWduVWxkQiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9hcGkvbWVzc2FnZXMvdW5yZWFkLWNvdW50IjtzOjU6InJvdXRlIjtzOjI3OiJnZW5lcmF0ZWQ6OjFzYWlwdjZFNlhiSTNZa3EiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1782748179),
('p3EWklKYVEg5QNGTRtioVeaCGzjoeFrVQKxCAnCF', NULL, '23.27.145.181', 'Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaFVMQXBpZTg3T0dQRVFoTXNsUFpyUzF0VHM3b0VSRzR3M3RxeFowYiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cHM6Ly93d3cuYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vd3d3LmFkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777101046),
('RkmzdUnO8wJCU1nePQFvQHet65QW0Gmlr1PDc06h', NULL, '66.249.93.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiY3lkUTBOeGpqUTlBVzRXS0p4c21NRW5vUnZtR25vZnJaU0lCNnd1bCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1MDoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvYXRlbGllci9kZXZpcy8xNDAiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo1MDoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvYXRlbGllci9kZXZpcy8xNDAiO3M6NToicm91dGUiO3M6MTk6ImF0ZWxpZXIucXVvdGVzLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1777117275),
('kH5VUQTkRQIW4w6hLhhFJ5yDl7P6MviWs1VDBrGP', NULL, '66.249.93.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia25uSFF5M09RWXByNlExVmtmdDVScVYwQUxpSWpTdTV1dTBhbjRHQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777117277),
('80UeOCJF1fmwGFKhaMBKvRBQJOPQv9VfwQ5EEwdT', NULL, '168.144.97.159', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiM3ZpZmF2VG5RZVRLMVlja08xemdES1g4VDl3cmZzbjdRVWcyd1hTMyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1777122353),
('MxfvguKDZQqSwzCYSg29AZSFWSEzeuMwx4Qo2HXA', NULL, '104.197.230.46', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVlNHejllVXE4cVBPU3hSWHZ3N0VqYm9KOE5MUXJNWnR3SDU1WDBVVCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1777224857),
('JqTxpqhvy50NKNFroQnveDCefUbVzM286c4Nsioo', NULL, '104.197.230.46', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWXBMUko3bzlUSDE3dE1CZmtPcEdhOG9hQ0FxRzhXTnI2QUd1WGZndSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1777224859),
('yjsxVJS7uni6TvGxvHOjVtnnz0qcf4SfW8IUALEJ', NULL, '104.131.182.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVmhVQUVJVFgxeUw2SGZFRkU4REZyTWhzUzRId1BjZWQxZ0RWMW9POCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777293085),
('WTeSo68bUgmTmRXpJq30CEE6e2xpBIb8xrBvF09k', NULL, '34.10.87.55', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNmVMeDlLWDRVb21mdVBmeE1jMkFkNDZTeXY5M2R1RmR5cWdZc0NwRCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777386426),
('BUq1gMByPahvF2aBZvuq7FMkHOct3uVReAprmzg0', NULL, '88.182.46.54', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSDNZU0lITndDTG82a1FUYU5qYzhaUmNnRldCanlQS2tSYW1qMEM4OSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1777445131),
('FAitPvq0G39EUWrFcp9cZWURlvJmDL3ZQp7NV0Gp', NULL, '88.182.46.54', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiS0RxWEtzTjVQdktUOVY4eWd6TUZ5eTlaMmNBUjR1ZXBxdVBNUU5PUSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1777445132),
('o0Kn5qbKHaZaKs9YoX8FYRsy6PtoGXqnQOIVsP14', NULL, '88.182.46.54', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib1FaVmZMRUJaeHNic0ZNZDRQTmRDVnloTVU3MnRIQ01vMzRyUW1tRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1777445132),
('zr3wVmrqLShMSW84hw3Hmn5pECRwtvvlsLWkNGCB', NULL, '88.182.46.54', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUXJ5Z29jWFdDTkhsVngxcUxvS2RmQ3Q2YkNjWFRHODFjQm1CdlNVWiI7czo1OiJzdGF0ZSI7czo0MDoiUVFJNnBmdlFzNFpLWEhmVFN5bElMelh6QXh0cE9IWkxOcFE2TXpOcyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvYXV0aC9nb29nbGUiO3M6NToicm91dGUiO3M6MjA6ImF1dGguZ29vZ2xlLnJlZGlyZWN0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777445135),
('7I5vNTfUQ4nOwBcRION3HuEltuETQBipEuNEUhqo', NULL, '35.233.82.19', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoid2hDM01NRUc3UzQydzFLdDFFREx6emJZekpIM2tpTG5XZFB0TmdqaiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1777569113),
('yNuENfITH9GMXHLH9s1Pj1drwnav2HXYPE7RUmmN', NULL, '149.57.180.138', 'Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZkNzWkMxZEdpOVZIa1VobFViZ0hzMWFGUzRXSkxkdjZCWkt1RVFxQyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cHM6Ly93d3cuYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vd3d3LmFkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777705911),
('6kZHekM7R0m9wzFVcPUlbRRQJeB4usGZuHp2xLQQ', NULL, '66.249.93.201', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiN2tHc3l4UVdySjlIRmRLbTI3RlBlWlR3dklRaTU4TDl0RGZWVUxXQyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1OToiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvYXRlbGllci9kZXZpcy8xNTMvbW9kaWZpZXIiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo1OToiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvYXRlbGllci9kZXZpcy8xNTMvbW9kaWZpZXIiO3M6NToicm91dGUiO3M6MTk6ImF0ZWxpZXIucXVvdGVzLmVkaXQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1777731955),
('QQIeqKjkNaEHremTvoUmc2IiKFaLQ6c17ZJMlwJW', NULL, '66.249.93.201', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoielY2NEp5dk90TkFTUHdRa0l6c0lWZlZlV3B6THlDYU9xWnh5NEw1MyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777731957),
('jitJhdyaZdCYu1pILnLjaWNRcm3Hc1rjs4yrPMHI', NULL, '64.227.23.22', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiU2VFdGpQbURNQk9nUlJJZWw3cXNzc2F2YXRqbEgzR3lyVGZZSVU0dCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777782588),
('XCvkQb4xmgCDRpupoWGhddL8r90creXhdNTW4RnZ', NULL, '64.227.23.22', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaGVZYnpXZVdJUG9EbFQ5c2dDTkYwUHRyczZEemNIakZTMVB3bnZvciI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1777782591),
('aRNjwkDhhIcrELDnfUH0PvfbURt2Du2z9pKu51sv', NULL, '163.7.2.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Edge/120.0.0.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoidU5GcGFoUUp6bDhGTngwdHM0aDI4bnhlTlIyWDFxSm1obWxGUHJyTiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777855564),
('26eIUymyEBV2fhjSxfMmLMDn9KaBHqpSvLD3MoBD', NULL, '163.7.2.78', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic2ZwNHlJNmVVU3JVZHN1Nm9yOXhiU1NVYXR0b24yS0Rob0NZcExpbSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777855566),
('fAIWtzhpIJkgN36J4qXH18LlObssGnc0DPuKUhi1', NULL, '163.7.2.78', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR0dYb3lPWjFZVTNrdVpXMWdkOHhWN21IQ3lUV3FDSG5Eb1ZBZFo5UyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777855580),
('O9WvTuy5U7ntgUGuKpMRWCOmOzXuxQjrQwnVYtDC', NULL, '163.7.2.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiakMxRHVVMzdOTGtmOFJkdHBYMlNLVUY5Njl3THRVd2R4VFpFRGdDRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777855581),
('RQPUdXYaLH4zA1hwsHucVf8YfMpFBylGLgrW1VpL', NULL, '163.7.2.78', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Safari/605.1.15', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVW4xUDFOSmNkZ2NmN3MzUWNqMFBaTnc4Q2JPUW52dFNtVGVmRVFCaCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1777855624);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('nVaTEYq4hRIFtAQIEjB2ggqepADqUtSJckgO7eZl', NULL, '163.7.2.78', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Safari/605.1.15', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicHJsSmdwRm9VT0VLbk1hRzZscHROV0tqT0dabVYzOFkzRjIwRHBUNiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777855625),
('gsfZfR5omv8Qf5rEsHLXe0dhPR2nOvxtFdsGaY6B', NULL, '163.7.2.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUVNTMEl2cGJkbFFVYmUxUGJSYTQ5Uk1pN3JiRVVSN2FRdkI5ak9UdSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1777855886),
('J61NnehagTMfQNxovYlGqULgCmvkHrN6rFvlj4ey', NULL, '163.7.2.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNHZXNEROdlJ3dnV3ZDkyUDlTZjNQaU1jWmNlazJiN0tETUg5b3NuciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777855887),
('YnynkDD3iByYn5XllNwHDOcgldBweKIh9gvrp8mc', NULL, '35.208.102.247', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicm5WTEhOUTlKTFU3ODJoT0xMWjBhcWFTUEtxd0FBZWs0M3pGYzR1NiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1777874568),
('I97eBU3RgLnqA6fdTIFnIZG9PaeUA3LtmLi8rJhQ', NULL, '35.208.102.247', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSTVnTno3T2VVMUdhcVN0Wm9TUGN3a3FVajEwQVh3SnlVQ0hlTmxHSyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1777874569),
('NC41HLF0DL7QoPtwwV6Vj5guqseVQrlwA0ul78TB', NULL, '35.208.102.247', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTDd3TFYweVRJYWtybzdsUHhFYmRQcUsxaHhtR2lkNXpyZWhYOWJPdiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1777874572),
('aG0OhqP1Rzki5mz4l5uP3wFDC0CWQzfl40Cn0dxo', NULL, '37.59.187.20', 'python-httpx/0.27.2', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVjN5WHdLU0ptRUh5M0kzanZFWXZEMHNCSDltZkJnSWF5b29VQW1CTyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1778027921),
('QTjMVl3VZIsKWVkKdASOLXqspXSOhXJEjvdHKWJC', NULL, '37.59.187.20', 'python-httpx/0.27.2', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiN1hUWVFnVzJtc3Z3d3BlZlI4azBCU2tLR2FvZks4TG5sVlBPRXpBWCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1778027921),
('ScSRbPHntSThT6tIrMSebd9bs4rJXV2b8HpV9PZi', NULL, '37.59.187.20', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOUVIcm9BSEl3VWJxVmpLTUdDdEtQUjhZdVhMYUI3dnNZeVpwR05zViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1778027921),
('zv8eOBWWTb2Isq4pgWd9OibjfzgEaFpSeZORuZaX', NULL, '66.249.93.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaHh1eVpnazl3UVRsZ3Q5dnFVc0ZOd3ZOTVhjSzVZalY0VzZrQXpWcyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MDoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvYXRlbGllciI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQwOiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9hdGVsaWVyIjtzOjU6InJvdXRlIjtzOjEzOiJhdGVsaWVyLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1778345889),
('5iWtAhzJjh4YtHEnMhX6MApj8NcKYsV0D72GP8WN', NULL, '66.249.93.201', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWlZJUXFEblB5RHl0dGhBTTlxZmhTdXpjMk8wVU9WaVQ3WUxBTmtXYyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1778345890),
('ZyhVAYAraSEAOgFUBvwktlFZAsfFjsKxOmnk9MOt', NULL, '35.185.216.163', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNW03bmttSU1XZldwck04QXgwMWh3eXRrQzJrbjhqMWdtSUxRYmpTYyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1778362600),
('adTVNYaQiMyi15SmsUNPqsdtPaLpBC8xCSHbjSeA', NULL, '35.185.216.163', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQWNDU0N4enBsdDJEa3Y5SVREVmpKOFI3a2tRVTlXbHNLS1pOaHNHTyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1778362601),
('qNn0keD6AQuxYDrsNfMqzc0IQcTyA6j1PDlSSZBa', NULL, '37.59.187.20', 'python-httpx/0.27.2', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYW5mOHhueGlVaEhyVmxQekJyd0lBb0VNNUZGU2JDNEFsV0VwbTNRaSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1778387046),
('BkdeT65RTSzAokC7lVyg3BoaK5k4lutEKqk4cXFA', NULL, '37.59.187.20', 'python-httpx/0.27.2', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZG51QkhRV2NMbkZDQWNFdDMwVERyOGxoamN6a3Zwc0FsY21OYjJweiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1778387046),
('5Aa8v1eN7HIEj3bIGB0B2YZG4NZVtxtvcTerCvkS', NULL, '37.59.187.20', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid2FGWmhOUTYyMHZ6Q21CNlc1Y3RRWkEwRGtUakdIZTlib3RYTjhSMyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1778387047),
('gynhZb5vDqcsdwh9tL9Zyu2oxMI2Bn96GhWRur2i', NULL, '34.45.120.161', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWEZUNFQ5QUVnQzdOTzNoeE1yNEVEUlh0Nk1xTTdIVXU1V21kYlpHNCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1778513575),
('Bl0rjjiUM4aJ6OAgbYd2LN4eJ1jjOsHvefwrQBHv', NULL, '34.45.120.161', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicTZlRmxVOUpiaHBxNGFabFMzR294YzBJRXZEMWFyREhFRmlFUFYydSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1778513576),
('M4ZppC6R6wvjLVGqEKbTdvAoY6m6IAy4aJELd2QZ', NULL, '3.253.164.34', 'Mozilla/5.0 (compatible; NetcraftSurveyAgent/1.0; +info@netcraft.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTExkVTNNQ0tSMHFPZTE3ajJwUHhGN1JxREZpbkNsRDh0MTBzVUFLaiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cHM6Ly93d3cuYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vd3d3LmFkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1778515721),
('QdOLFCbINMGsVUKFNMSqkavQycmmtw4BqpY5V5oU', NULL, '35.95.103.117', 'Mozilla/5.0 (compatible; wpbot/1.4; +https://forms.gle/ajBaxygz9jSR8p8G9)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUXBCbTZ3R1VFdU4xUFRmejNFTEdPYWZlQ2E1UjV2YTBnSEZkY1BKUiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1778533219),
('SgP9gBf87npuu5lybulCeY1BZKPP11LsBA5r2OKG', NULL, '54.170.82.167', 'Mozilla/5.0 (compatible; NetcraftSurveyAgent/1.0; +info@netcraft.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiY3VzT0FBVkhNSDFIYVBXQ29rd2hyWXBXamNZVEVMMm14WWhMQ0ZDQiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1778537261),
('aU9Tvp54TGI1VlM0OUEWBQEmIVgQOAJh8x1mEgJn', NULL, '36.81.233.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZHJTOFc5cEFnRVdXYklkOW5lM3c2T0E4RndTcWhZZ2hFc2xINHltdiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1778574120),
('jQBRTYPVbSTqr0ePKYsQcvxEueXl2IH3UDI515d0', NULL, '36.81.233.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicm5jaDV5YWdqa1FHSlFqakNTcmZtdEluMWVaNUloQ09mYkFKQ2VOZCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1778574128),
('ugppRTvCGBHtHt4KDDl6RLWeQuf3BchTQkHCg64T', NULL, '36.81.233.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNEhlakw1ME5UQTcxMnFrQ0VWQmxrNzdIcGpCcDAxcjlnSXN6ZFBISCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9pbmRleC5waHAiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo0NzoiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9pbmRleC5waHAvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1778574130),
('vXWFshgvqiJHAHpMRv8Jcfnt07aSzG3S5XUkOpGR', NULL, '66.249.93.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibENyQmg4a0tuT1diSFpPQVZEM3dpZDgzRm5hSjhoaHFGTWFEa0JIQyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1778841236),
('3EbKZZI4zb37IMzKgqQ7Lgf6oOXf2fA9WIByhylb', NULL, '66.249.93.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiREV0MlBkUVlUQ3ZNOGkyZFZXcllKRm4zMTdRMjRtVHhWZEFRSm81ZiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1778841237),
('029bqNRerifiKdxKqrrmfp44Lee06zKiNOjuN5Kw', NULL, '66.249.93.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNHpZS3BRSzNsOGVpOVJ1TkQwcEQ0V2tYVlIwRGoxV0JHWlhWS1J1USI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1778958152),
('PbW1UBnJrEhaToytCAfqZT76EzQ43O8FNWotLE7N', NULL, '66.249.93.201', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTUJsbnNSMjhiRlNsZ1M2VEFxb3BoS2lFOVU2aTgyTDBWc3ZpMkxBWCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1778958153),
('d99OakhZ0TDtCIymL4lAOW3OayXeZnaPMwqhac42', NULL, '66.249.93.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNWNxYjdaM0tkSFQwcFBLQkJjZFpCOG9tOEd5cFI2Z2k4bHRhTFp5eSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NDoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvY2xpZW50cy8xOTYiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo0NDoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvY2xpZW50cy8xOTYiO3M6NToicm91dGUiO3M6MTI6ImNsaWVudHMuc2hvdyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1778959839),
('A2FzKEpPThoYHDstU0VUcjFEWzBZ56NHpDoqnw3o', NULL, '66.249.93.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVWpTajZqajNpeTlWcklEbFpCRDZMMVdHT2ZRU2FFNUJmaUdYSUp6TyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1778959840),
('k4PY8joC5Nh9LLQOPIBJRE60Kv2g2lmtGhSHbZTX', NULL, '168.144.90.124', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicVpuTXUyN2NSelJ4Z3NTYUY3WkJ5UHFkaEg0ckYxeFpCOEJMZDlGciI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1778989658),
('TWueTnZBSHIKY9iQhk75vsJczARkb8Q5gtJvW5SK', NULL, '168.144.90.124', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSE5VbUJVTzRLa2dHdTVnZ1VWVmJ5RUx0aFRkYlpDNWlTUXYyb0p3NCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1778989663),
('nD8jtmC4eeNWBTvgVQclMruhmhsefWhwPmnOATkH', NULL, '88.182.46.54', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQzVQRkRyVGZ0eXdaQjNhcUUyMzVPQnVzcURqVWV5UUE5OTNWZmY3TCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1ODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvYXBpL21lc3NhZ2VzL3VucmVhZC1jb3VudCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM4OiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779033062),
('Dtl8xELaCTyHalmHDhgfZGVSAzZktlhf4CTRm3ou', NULL, '88.182.46.54', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicU0xTEhNTldlR3RjNXZCc3ZSMzdZc0YxUEJ1SkVIZEtFWkFEblE0aSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1ODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvYXBpL21lc3NhZ2VzL3VucmVhZC1jb3VudCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM4OiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779033068),
('j0uJCCx56kh9QQoOhTUmyWoD5myIRZcVt2IdBGUv', NULL, '88.182.46.54', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZ1Ixa3lRbU16VEZIVmsyOEdTRDc2bjdYV1V6eFo5Q2JnOUFoNDM4ciI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO319', 1779034327),
('WiesEqtvjKZetSlEFPcx33LQ5CYRYHymjlSWXRXu', NULL, '104.164.126.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiNExVWGJRUmlXU0tDcFV6RDFPY0tvZlh1T2dxcDlScThqMWE3NUdjdCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo0NDoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvYXV0aC9nb29nbGUiO3M6NToicm91dGUiO3M6MjA6ImF1dGguZ29vZ2xlLnJlZGlyZWN0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1OiJzdGF0ZSI7czo0MDoiNmhKOHJrOUVWREJQRTFncE9XeUNFVkhNb1J6dDFuTzlUQ1NPV2VZTCI7fQ==', 1779479820),
('g2KSY8n5h2kzG5kgh87ZURqll8K10NF8dI1nfzt7', NULL, '149.57.180.113', 'Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNHplMkREYURkeUE3cTA0MVdOU2VtR2ZYVGoyYmN2OVlWSlAwQ0N0TiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cHM6Ly93d3cuYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vd3d3LmFkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779088052),
('gw13HPwnRh9B8R48QKrGez3EIcgaBL7njj88AJci', NULL, '45.148.10.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaUZScU9XOERvRnhyZEdzMkszRExUSWZDalhlYThleGxOb2NaNXdhViI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779205050),
('SxdMrjHLjAnk8c8p9b2kT7fqNgDEVmwmcZkrtfjw', NULL, '45.148.10.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibDZLZDRHdlVGSk85Mm1XaEpvWTZDYU85eDBJcDlpdXVtQmFnN2tFaCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779205050),
('RALm90O8RIDEoolCTctIIPO6ZzCzFc6Lf7oV8kkR', NULL, '66.249.93.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRGMzak01bFlWMjBVWVF5eGxFOW1heFl0dFhpa3JFNmhGRXpHS1ZXdiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779208270),
('VG9FEIxXwoxHOE50PMRfy8Bt05qAPF3XTt42OgOC', NULL, '66.249.93.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYkNyWmVmcEw5bUxxNVJlNXNxdFpQVk9xSnp1UEJQaUpRajlGVzlQNCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779208271),
('8m74quEpJYmwHXXAIH46tqICk868uOV353M6qqVL', NULL, '74.125.208.39', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiek9ZVlhxSkg5d3dJNThEd1ZuaEU3RFpyb2FLNXAySTlTMUlzNE81aiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MDoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvY2xpZW50cyI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQwOiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9jbGllbnRzIjtzOjU6InJvdXRlIjtzOjEzOiJjbGllbnRzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779219404),
('cxzSYlNKuvthx9fLl4q01ecjpv5z5SeRmklqSZiv', NULL, '66.249.81.132', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSnh2YWUxWHVPSGlSTVhRRG9qSEpBckMxVUx1SVExVW9nZ2tTMng1UiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779219405),
('1YD37slMr70cUwRS7ZBN1PD6Dq7QYoOXzRoRAmIj', NULL, '18.201.12.56', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicWo2aU0wQVNCMXczNE9LSnVzMDFURHZ4WEFtbFZCUm1NV0NrT3loWSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779276213),
('P43dPEkzmDJhfSw4WkHC9LFzT416PgyaRbMu3uAH', NULL, '13.201.28.32', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTUtWdmVqRjZERU5SSjh0aEVlZHF2MGlYSFFoOWlEZkFQNTZEQWZEeCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779345564),
('NNxWiUnrYYuNaR0AoDkUIvGhETkNxmzuzlE5BPDH', NULL, '34.10.101.110', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYU9kbmx3eDY3T0JGTWZTdUdhR2hpMHNDczhnSzNpZ21iR3lOYWVxNiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779373876),
('lVgZUdNTb6apIMfsxbuZ95luw2cDp6VuGLlOnnt1', NULL, '34.10.101.110', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUGwxU3c2VXNkeVd2ejhiZ3ZyNU92SzV3OTZpYnQxdDVsWjAwYzlUNSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779373878),
('2HNMNByq2wZQmw1IBmELRWlEedJusSjxP2glzD9l', NULL, '34.4.102.60', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoickh2YzFCU1NZZnVrajVPOGNGS29VTnVFQk5yR01KZzZwY2F4Q09JQiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNToiaHR0cDovL3d3dy5hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozNToiaHR0cDovL3d3dy5hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779406146),
('YpvAZANFjGDkeFFKE5jPJgLlRqPW7z4gdo2udyN1', NULL, '34.4.102.60', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia0I1TTI3dm1yWTVZNHFCWHRpUGxYVlJEbWlXQWJSOHlRWkZOTjhmYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly93d3cuYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779406148),
('W13SnEAsFptm7LusyPpdgljRlFj7ev4qFGpjteHe', NULL, '34.4.102.60', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUFh2dG5tRjIxVFlHTnE2ZUk5Q2VRVXp6UXpnT05VMnhOTVgxbnRPQyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly93d3cuYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779406150),
('YFQPznvwvhzy9ESK0vlSPIyfh6tyVqEUVgb9vOPL', NULL, '178.73.238.84', 'Mozilla/5.0 (Linux; Android 12; SAMSUNG SM-A415F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/23.0 Chrome/115.0.0.0 Mobile Safari/537.3', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidUZnRU16aFk2OXVCVUxCT1hRWWZ2TGRuNEgwclJOZXF1R01NSG84OSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779419238),
('3U20Gmctbj3iiRuYqxD1hem8pjzfzISkCpTJ3GrV', NULL, '194.132.202.137', 'Mozilla/5.0 (Linux; Android 12; SAMSUNG SM-A415F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/23.0 Chrome/115.0.0.0 Mobile Safari/537.3', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVnRDUUgwWGw1dUVyNDUxS0V2RTVkNjlBV1Q2YzJFT05KWVJLenQ5TSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779419239),
('JcWGEVvs8ECbQytD5fSiTJrBAnl4qa1udEaBVcPo', NULL, '66.249.93.201', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidEZvbVhmZFF2WVN1cWFqY1ZiZnEwSmVYRGZtYWg0WkFNc1NZaWdtWCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779607741),
('WMoV512oWmCr7KRwNJlkKdIHn7lAkCj8jhcSDcF7', 1, '88.182.46.54', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiYlNaQmJvM3BKa1VXTW5reTl4UXR6b3MyeHNYTmNYb0lYNXJWVmdYOCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMyOiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1782673124),
('pmj5Lxe4QeFBt739XWEniwlnDVjgiSP5sPmM7M5X', NULL, '103.4.250.197', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRkxIU2ZoNTNRQUE4OTlaUzYxWVNxMXA2Zm50MU9CTTZxSzk2aDU5MyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779496830),
('1yq0KCRh3GNwkaxb3X6URz0zeNsmwA0ECfVvu8M4', NULL, '103.4.250.197', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoib0RoY085SVdtYzlkbjVYZ2N1RFRxRkJ4MUdkUEFsaHdZWmZyU0o5aCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779496848),
('NLrpgTgIh5RFmhb86fY7FeOoMgAuPPs0FHiUqioH', NULL, '45.92.84.203', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicmZEU05OVWFGWGdsbDJ0c3hmcWZnSmxuSkRQa05jY2tWYUtjcnNYRCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779510545),
('UhK0T9pkS0geSCuVc6MFfkZhSu6Jl8b50TB43LyC', NULL, '45.92.84.203', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicUxIQUJvZTlNRGd6eVBON1pkWWxXcEttWDNIckdIQ3I1blQ5T09jeiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779510547),
('58uJu6Y1JQ3Rinp7ioQYLF8j5qk9gZGa03vsQcSA', NULL, '137.74.246.152', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRndUM3lqT1ZqWEpadk5tUUI5dmpFZURDeFFPN3FTc2VuZmVzTzZDViI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779515098),
('HDn3bQ21ySdVgeFDo8Vf0vjKTUbZhVd4FUxTnGhX', NULL, '137.74.246.152', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieEdKYkdSbDZXMmhOclFrMzljUG40T0hvY1UyVUtPb1JTQmtveUIyTyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779515099),
('Ny5h3VB9hFmUnsep4y39S044AjAXM3qlmLGrLyzW', NULL, '159.203.32.109', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNWJhRE1KZDU1YnljVm8xbDZIdENTalNLMDVUQ20xSUxUc3BBWkpZUSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779532633),
('RP2A9avbS6FRZdYcYXrfGmzvU1b5AH0XIJpjzK16', NULL, '159.203.32.109', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTWdLQ0s0dmNhVDdtR1lnZkhrYWk0Q29zTVM3VUxhZENwWWxkWGw4dCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779532637),
('DujtqGP1N8VnV1ev1Sjsq9AkwuXPbgeDTrPWVf2c', NULL, '206.189.4.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaGhJUDFZdmFBTUxkR2NHVlJhY0pVZXg0RTZ0VjhYeGdaNHFuelpRYiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779534621),
('dSbk7jP7udGB2gk8u2Fp1gN5kZ7vE0klvwPvQlL7', NULL, '206.189.4.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZEJxZVJ1M0xrenN5bWY3NHBtVHFUMEE5REhWcTZFRFltM1ZTekMwVCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779534622),
('ho9OtIHDBYNqA8bwr7MJVZ36tsKm6gNImCaRJP4I', NULL, '44.244.61.163', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYm9KNkNMY21RZlVrTFd2aWRSODdoaG5ncDh4b3lTWUc0Q1JXTkdmZCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779581559),
('LuoJqL2VxQd747gNRPiFsZ1BWpQLojmWZtdaP8Yc', NULL, '44.244.61.163', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidm1BOUJuTzZGNlNYV0RUZzVtc3R2RXlYckxCeHp2c3FadlJkYTN0byI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779581562),
('6JgjKMJq68xzYWfF0bIUGJZh1nInWNIlSbQc55YQ', NULL, '137.184.174.170', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMUlSa2dZanpnRGphZndzOTBTa1dJSVVUMzRObWZUZ3hORHl0QVp3MiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779592062),
('z187Kxa8587hVXZoCnLfpbIAX5SiketbqyQvx3aV', NULL, '137.184.174.170', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiR1d3MTFKOHhabWdzMFFldzh0ZDdHNEZiWjROWm9CcU9LZWlvM2NZaiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779592068),
('74kpUEOp0IArbNSNoYbbB5UJdQbAfGHpO1ulCPZD', NULL, '45.92.84.180', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiM3FwbDdOcUNHbzZlSUhmakF3VDh3NVpHbWU1WXA5MlhkTk1hODhOdSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779596849),
('z06po15gcWJCT3NMmdjtS5Hc8OebY5VJopfksC2x', NULL, '45.92.84.180', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVmF6MmxHSW9sOW9QVjhrMlhLdWRaVVo4ZTdnOGMzZktPamZud0ZtSiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779596849),
('WCoXoMF0CxxxZMhiUCFP0jKPL5axGgqRX9ZlSveW', NULL, '66.249.93.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRG5PTDBQMWQ0dW13QTNkdXJhMGlIeFdYcWtXaUoxYWg1NDBiYmM1MSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779607742),
('ICATyFe0GiWhwgaj3JHNFDRMc5d1vyfxfj8HKbpE', NULL, '66.249.93.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNEdHVXZQTDVaekQzaEUxdXFNYUZ1Y294M1p0elAzdXRCcnk4OVo5SCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779986689),
('fbxnFE4oQG4hmGjYPUOI2D46EtCcwlbQPxWpQAKU', NULL, '34.121.13.252', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUkRyRU5sSzFvOE9FZkRWMWd1RXE5Vm5xSFRXOVVQejlnaW0xZ3JaVCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779615578),
('EjlNtF6VLHRx6HWU6zPpr5n4XFE30fXDUWT5KHXd', NULL, '34.121.13.252', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic1dTQ1ZPeTZyVG9IbTZXTEQyQXhERVBnemQyVUpPZDRvVjNrY3FzRSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779615578),
('ZTLcffsXWQ5cXstRI3ieDmS2tppRGptJFMLeZl9B', NULL, '34.186.66.207', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiS1dDT2hKNXhyNWNGeEhUT1RkQTY0RnZ4emFHbjBJcURkUnpkbDlwMCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779642105),
('bPwHD7xZtAm93F2AkUg2vPzOnWBPtFpEkr2pdgU2', NULL, '34.46.13.30', 'Moz111a/5.0 (1Pad; CPU 0S 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Mobile/15E148 Safari/604.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQkFtYzlOcG5LOUdhVFE0N05mZUZvZHZ0UHBlSXJLS1p0ZzRySnNpdSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779672821),
('SGbxb4xkkIzteMMelWlwjaYWmwfQ7L94fSOBZ8Uv', NULL, '34.46.13.30', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:134.0) Gecko/20100101 Firefox/134.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYkw0NkQwVnNWdENtS1dWQjVBS0tFbHJiaU81TE1xVkZUbFoxZmdrViI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779672821),
('zBwJdG5tGI7E34y4CbQ6k6yCMydw2JNXJ8lhQWLh', NULL, '45.92.86.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOTl4dEh3TWdoaEFCekFPM3hvOFlwZGdLNGhjN2JqTmdkSlZUc25NdyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779683055),
('Td4EwlzBJVdkjaxa3J2aMKV9j4zt1lV43QCvY3E7', NULL, '45.92.86.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZnRLYmNYVmcyaXEwV1dsVUJpaFVnT2RsZ1pNZnJ3c2Y4N01ONnlzQyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779683056),
('Wtl8ghEJ3ymv8oetMq4DQvJJezg6tvmRSiqybEnD', NULL, '34.46.13.30', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:132.0) Gecko/20100101 Firefox/132.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicUhIYXUzVkdEVHBqSmJWMERqd0E1akwyZVNqQ1F3eU9HRjBYbXRqbSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779703540),
('klJ70P8Ddfs9eyMEMEk8fo00ECOJz2ilRgwdlWJj', NULL, '34.46.13.30', 'Mozilla/5.0 (Macintosh, Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Safari/605.1.15', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiM2thRkdTNDU4emd0emJmckJqWFZOclZsS2JWREZJRGFnenJHSlZCdyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779703540),
('04JXpxqG5MVJSkY5YpyslHIMjaTcNIsBhZGRT1AN', NULL, '24.144.123.37', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTlc3alZBOEg1UzBuWlVlWkNKMERYUjJNQlZUbzBIQWhNSGtqaFB5MiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779756888),
('6D8vrmnbtbJsJ0n6NrLEXGUV6utddDhR7d9xjz1R', NULL, '24.144.123.37', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoia3RRV0tDS0dEa1JERTJoVHZ4RUlqUnRlcmw3TmxZb2lwY0YwVldFaiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779756892),
('gWvT7DrrrOzGSVejIQxeGx4pzV5RuzKsOOuf0hic', NULL, '45.92.87.45', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiM3Z4cElmNjlXUHRVMUZ1alVHNEJMVHIyMkxOblM0bHllVmtXSE5iRiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779769426),
('btWjdO8oYXMX1Q7Zd9VfpBDeXjRfNEkQHJtujDRA', NULL, '45.92.87.45', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic1RLd1JiRmdlTDRMYWZnVGt4Y2dkV25EcjJoVFFQRWt2eUVPVmNvdCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779769429),
('lBGzdXGPhyOFU4qBj23qZZzTPlBeWAxFdF11AJuK', NULL, '45.92.87.248', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibXVBVndDS0wxem9xWmNaVnlVT3d3TGhJR0hIYXJHUmFRNHRHaEExZiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779856126),
('TwNvHsPBD2TSY8CCtvwcGJGTUxO0AD9naIdBNBmg', NULL, '45.92.87.248', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVHFtN29iUHFBQVJUSVliTUIzbWRRYW9saUp1bkJMaVRnZER3UDE4ayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779856127),
('U9NsjmdLK3wcwOsBTFMJAif4YpnXay9yqfU3bjQk', NULL, '136.107.180.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZlFyOEpMbWFBYmxOSmlWZ1l4eFNnTjBsNVdmSERWT3I4UkFzOTlTNSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779951603),
('SiQMHe49soVtk0LCKj5cqCE5KiZbxLLXYggWw0HT', NULL, '152.42.190.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoia05UTTFDQngzV3dQa0gwd2xJclhMSG9CdkRZZnlpelZETzZYNnoyNSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779971200);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('xZsxy4aMq8xEtIU4HW98uz6curkXi4BHyIIL5to7', NULL, '104.196.163.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiU05UNlBaSTY0RTd1aDlwa25Yck92QWNOTHpTT0NMS2lWMVA5OG4ySCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1779979826),
('qpQhO58KPtCxHxQpK6EaJQIthJuye1alsO53k3hR', NULL, '66.249.93.201', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVjJkTmpNYjd3RklxT1FqTUdKRlB5SVBSbUtJdDBKMGJGS0l1UXJMaiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779986690),
('5EQs8jH7wmHrbZGxkwKThxyAhNTVhdNv9139ThF6', NULL, '74.50.84.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQmVPVjVudVdXbEE4SDl6bkFtRVVSWGJKQUpUOENBdWR0eTVYa2prcCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1780038518),
('2mpUh1yTJGHztNt42TI7HtU5TPj1U9sKzmbrL2O3', NULL, '74.50.84.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibnhvdnFpVHlWRGV0eHRBTmJqQUo3elo2T0Q5VGljZUdiZ2dqZHlzVyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780038518),
('0Pdxduw11IrP5SzxWLQC1lUJItJL0PzpOU4Q5IoP', NULL, '137.74.246.152', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMjlCTWVzZjZCRWpmNzFqYXB4emRMQ0o0Y1Zia2htOVhyVTl1WWRGViI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1780214746),
('GwlWux9uEVizbPWnFMKqia4ApPEuaY4f5MB1WKdJ', NULL, '165.227.117.81', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTHZrd1d3bm9zOW1KRnNGZXg1MmdzOGkwd1JLQWU4aXdmOFBLUlQ0UyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780239556),
('tZfebSDWA1XN6Fp0FSqaiQFA1KaUOB7JHeC0tnAs', NULL, '165.227.117.81', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMHY2Uk83cjJqdmoxcW1BS1gxUnVCQXFRNVlSeGNWSjNiUGF5YUZTNCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1780239559),
('Kpt6NcopatGc8lYsN9BtDlkLagBMybzxQvrXhuS6', NULL, '66.249.93.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZWhBS2dGT2diT2tVNUxQZ1JCN1YzbU0xQkZRaGh6dWRZdlNRako1eiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1780288906),
('mJdtX2vnQlM2sw8zqs5OuT5N7A9KxssSNdRIVhi0', NULL, '66.249.93.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiY29naGF6UlpkMFBPSXZya2V0eU9zcGtTQTZZYTQxY25PSEVuNXltWSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780288906),
('lRUCDSPpNoSuYTMrs5T1BduM9TjKdirbWBiWswJY', NULL, '66.249.93.201', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNGR3bzdZNnl3SERjaEk1dUJUZXlLa01hZDhYeHlzbVdZMUJxOUxqTCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1780329481),
('Xi3Rs4istOCDJ3vcMzvHPapgIh8rgSCLk0HO84od', NULL, '66.249.93.201', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibFdkT2N5enFvRXNPRVRBZzdsbHFjaEJ6TFpBbVZuek4zSUV5OHhvcCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MDoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvYXRlbGllciI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQwOiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9hdGVsaWVyIjtzOjU6InJvdXRlIjtzOjEzOiJhdGVsaWVyLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780289276),
('NXgJ2eEXgaZAVk6r0VpmJSaCAPR9t7nGYb4QxBGv', NULL, '66.249.93.201', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMWFtR0I2d0RwelZwN3NBS0R6OXZWYXl5YnV3bGtZUWFPemlxQ0hSVCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780289276),
('OcaaHllJnfdpbo6m87TdsyPiYzuoG4XDs5OslZu9', NULL, '66.249.93.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMkRQOGhVbllrdGdWcXNhblNoUVU5NUFDNDNFaUYzNWQ0TGhlQ2tPbiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780329483),
('8tjpcB7f5HIoPMS1zbHaB24rqwX2u9IdwcaHi361', NULL, '3.16.152.153', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWXE0bWd1OG9yeldKeHJjTm9Gc2t0MzFBVHo1S29makx1aEdtakE5SCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781366657),
('uWWXl4om7QkvgIljLc4fhz22MQbb5iiW3BfWf1Du', NULL, '66.249.93.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSlZCZlk3MkRjY3VwRWdIaHVINHh0OEVqQVYxSDNCMkRNNkdnTURKWSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MDoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvYXRlbGllciI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQwOiJodHRwczovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9hdGVsaWVyIjtzOjU6InJvdXRlIjtzOjEzOiJhdGVsaWVyLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780756541),
('MJ9lfVer4G5gn9xXqws85B6c6vq71AZTOSIEKDia', NULL, '66.249.93.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTnZnN2tpZ3F3Q251STYxRWh2a3ZPNnVjNjNibmdGMU1TalhYeXdWOCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1780748078),
('wNJLrH06DwoFyi0dT9aTWYznsZgbZheSuh1EVR6v', NULL, '66.249.93.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiM3FZR05YV3ltTkJiTm9yMnNWZ3Y1OHdBYnUxRHhKMnV5TUF6SDRMQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780748080),
('75VVIN00yDHi6ALoBct9OxdPamGR2S3IfF88luda', NULL, '66.249.93.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYXdCbGFMQWJiMEdIeHB3SGxGeERhc0hWTkVCaFVrZFhNQm51eWpBZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780756543),
('3KUbQ0CgTi6r5jiXrOxmOC5REMtHOa93JzJXFGcJ', NULL, '144.126.202.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZGVXWldBaUd4a1RBZWprRFJoSGJYS2c1WHdUMHFwWnJmeUl4ODVQUiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1780789999),
('EDcZ5J5sIEz6bSQdU96XsiFaoQrNDn1NLRXkgTVe', NULL, '144.126.202.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoieXY2V0dGN2xKUGlIR1kzWm5ka1ppaWxqcFNBajgyUlhib2dKNjlkbyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1780790001),
('ujzik2gqD0GWYhiCLH0MaRQDDykxKO7g8i5WnRwI', NULL, '174.138.22.51', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVjRnV2s4NUM4UmliWEZQajNNSm5tZm81MGM5SkNRRnFKSnZOQmRXdyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1780790656),
('wdaYWUePW0fAdnjGSb2brsZq3iifPthaDeVXM7s9', NULL, '174.138.22.51', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoielZySGEyTmZRWThvdDJMVmszSDFyUzJ2YXdpa1ZncDVVZWJaVDlSWiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1780790657),
('TgX6CScW6DoWcwLrlNlQ32jPnVDyW0eSIw8HlHms', NULL, '137.74.246.152', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSmk2Y1hHS2Fnb1pLNnVJNnFYZHI5NExuZzkxczY2VjZLWWFsZTdSUSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1780819562),
('Bh33l7Bl4m19t85fgPk7FQzTZSB3XR53oNBh9tVX', NULL, '137.74.246.152', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZDVPb2VsVGdvQnNCZk14eW9Ta3dKSjZsdkk0dERmZk1tUHl5U1hPcyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780819564),
('KcAebLwG80ez9bw5NaFLgnEg84BiIggJue6ScQl7', NULL, '34.134.218.248', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiekZlZUVMN01KYjJsZkVCWDM5OUVORXZKc0tHSm0wS2loSDdaVlJpUSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1780895370),
('gOrhdrzZnlkc56pAbrVAnan0qr0YF2Nnl2HG8m8n', NULL, '34.134.218.248', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR0xqaHh0dGVkdXk1Y2xWZ3FXOUJxUU51NFQxdzNXRmRXYWVIT1hlRSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780895371),
('yxPIeKSEiSQUUMuP4UUtjTDkxTcBuidouonjExvh', NULL, '34.162.210.192', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZ0hEZnByR0VWdDNRNDZ6UzU1VjdRVjBrVEdSaHF2STB0bkJnZmNUNyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1780902657),
('D6huynrXp9eIKBodoaiZsHx4U5IWlatZDifKjLiG', NULL, '34.162.210.192', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNk9YSTZvNEV3UHhUdHA2TjF3Mk16R0lyRk8xTHg2cktyRDNBdzM2YiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780902658),
('ZMP9iASMDCVTMFehck9biHXEPof5bZ02UM7ajFcy', NULL, '32.184.18.93', 'Mozilla/5.0 (compatible; wpbot/1.4; +https://forms.gle/ajBaxygz9jSR8p8G9)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibTZEYmlVQjBOUFhyaGtXYTlhUVNNcWQ1b3dJZUlxM1JmT3BGQ2g0VyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1780951036),
('xCkzKMhUyDmWZCQnJFmH65a2W8E1XHa1FOysuQ9Z', NULL, '154.241.22.37', 'Mozilla/5.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUmtXQlZUdG15aTRXUXdZWVY1eTI2dkZ3TlJRWldtTDdLY1diOFVGNiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1780983265),
('L5cTbIke6fiGySFzVnDltNYUEQGIkQsvGinaePPR', NULL, '154.241.22.37', 'Mozilla/5.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRkQwVHR0VmVlQkJ0alFJWjZxOVZkblpNV3BrNE4zbHJ1djRLSXVobSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780983266),
('A0hWBASYoXbzNfsAtBi4f7HVjG0HTl6GcNXo0Wh1', NULL, '35.204.199.198', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidjhxUFVrWE1TWGxJU0hMUXR1VVQxTE4zdDB6YTc3dlAxTjhIVWNmaSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782736997),
('dxQfRvArS2HuLnwUtTKOD0gH5gPRGnfEFEkKgjo0', NULL, '136.112.198.28', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibFhZTFhwazZsOGQ2NG11MGFGWmdZczJTOXRhZFg4cFY0Q2R6eDJGSiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1781020016),
('HVi7olqnY1HcnuWvu2ucBUElLWgiXEkfWTlm1PSp', NULL, '136.112.198.28', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU1NJTDllR1FMeUc3bkVsOUhUSmhuZTU1cFdaY2lEQ0RnYlpjalRyUyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1781020017),
('0Gxl9kJLgBoJ9OnLUqRUHLcgyVTYObzxCRmFBWR2', NULL, '185.56.45.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoielV2RmVGVjZmUmppWkpSNTlkSldrdUs1eXBoTkY3Qll4TmV2NHdyMCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xpdmV3aXJlL3ByZXZpZXctZmlsZS8xMjMiO3M6NToicm91dGUiO3M6MjE6ImxpdmV3aXJlLnByZXZpZXctZmlsZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781101010),
('c3nOcoojkoSXYhOKLy9LQMd8h77FufRiqi6jBh0F', NULL, '107.172.195.150', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaXNtWVlWY3NJQlVjcVh6SEhIS2ZxWkF6U3VRdlNOeVJyRVVOQVE4WCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781147262),
('dLfMz4Wvf2R5udTrGkYumeRap0RBw7imqmYNbwRj', NULL, '173.239.201.11', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOU5UNDNlMDlwamtscUVKYnJ3SlpNeDhwVEpsSHpxaEdBQ0JMQU9UcyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1781147349),
('EGN2cWC3FTAeZbV4t95OkhzDVRNwrCe1MQspXWyU', NULL, '104.164.216.4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.6998.35/36 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiajVjYlhPMGNlNUJoY1NWdU5iVnJLTkFRaU5VakE0V2x0SXJ6aXNFZiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1781154525),
('oCl9h9AzXcBceeJ1IY9km7ZhvDZ1xUROgJT5L29W', NULL, '52.230.100.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiczlwVW9ZcFBWV0RxUUNxQVlOZ3BMZnd0enFWZ3l3Z0d6cktpZzBRNyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781168558),
('oPa9qfzOnpJPvmbOUCi38Devx7VhZ2EO15c6p1jr', NULL, '34.38.118.115', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiM3JFVXVEcEZtNnhEaVNCdVh4amF1czZtMTFkUTVrSUxNQk9KNzFXMyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781181936),
('jvIynU2TqdCFZBemqwMiHhVnQe6xSZxgRoRcaTBf', NULL, '34.38.118.115', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiM2J2d1djNEVvV0hTNGRHRUNSejZqdklnZ1BDTGdQUHFQNURzQ3BmRSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781181937),
('D4FLJjy16L2yUWZ1vLlrwgOpQTgPmoDY7RxbJsAW', NULL, '34.9.3.124', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOWNVTVlWUjVCV2RYMGg3TTRYS1dxallPU0RFSnk3OUowZFFoZ3hQciI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781189340),
('E2ZQcLmIubVnlLdxPh4jXBSKMwgXAguX0Yp1rqaK', NULL, '34.9.3.124', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUDFBbllYTnJldW4zTkVYT3pKSXZFSlBqNFlEd3o4RW5IVXdLTE5SUSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781189344),
('S55fy6vX8hsFrATOGUnaWKiDhqKz5ASCZL3pZS6X', NULL, '137.74.246.152', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiY3JGWWppcmljdHpmSWtlZ0hyem1Ld1ZhU3BNVUxwWHowTHl1cURHeiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781243531),
('89YF51VmH7RKAQxjx6egm1FgL65OE0nmXANvTbqf', NULL, '137.74.246.152', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic2F1THhGWmc5aTJyV1M5clJmUG0zYlNYQ3JlV1ZSeHRaT1hsdTJXMiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781243532),
('Rk9SWdVJj2nO0d7nK86bdSSPjHU44tTtkxst3DOv', NULL, '54.72.228.30', 'Mozilla/5.0 (compatible; NetcraftSurveyAgent/1.0; +info@netcraft.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSnpCaHdZMkl0ZTQzRXg1WHFqSlREcDl1YjB0QnE1YWFrM1VVY3FFcCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781261437),
('NpvjksECUvSAf2bLyz3cOPxEpGgftLhJIjWxKVeO', NULL, '104.154.158.78', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZ3RXcUNkb01vTjloTmI5SDVRVFNrYW1EUlIxZjZnTlo1RzZQOUNHRiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1781269927),
('NhY7iSElpi28I219dRTJlngziVRrneOLgSsBxo2j', NULL, '104.154.158.78', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVUJNbzIxNGJnb1QwNXJrWDJhWXE1RU5rUTNEdjhpM3UyTXVyb1I4biI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1781269930),
('MttuvQUGzJqbUZ2aBQdjGPSzTEcb3O4drsQRfE6G', NULL, '136.109.199.58', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZ0E3TzV3Q1Q3SDdreklYWFlyUXpoTlByRXA1ZHVmSmFiUFJNUTNFRyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1781270315),
('h5aMkTIeaU4r83D6G3TkQQe3Aab5g0UoxPUcYItb', NULL, '136.109.199.58', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiakVwTGZCY0dRbE43NjF0T09mc3B0OWQyZGxEbzhSRXZuUnJUcGVuQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1781270316),
('31AGn8lZXYVwenWrSURhvplrdjcT8xNHuY6xR9pf', NULL, '3.252.41.160', 'Mozilla/5.0 (compatible; NetcraftSurveyAgent/1.0; +info@netcraft.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoia2hHUkE0RngyaWNxV1VWcDR1S0IyMXZiVE03ZHV1NXNkVWtIcGVCUyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cHM6Ly93d3cuYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vd3d3LmFkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781280992),
('rF129zWpn41zj91HhZBnRlycvY9lExQlJnuTA52O', NULL, '5.133.192.221', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_1_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1.2 Mobile/15E148 Safari/604', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidUgxZ3B2UXEweGpSUGFtOHU3dVljUDJrdnF5dnAzbWFTcWVMaHBEYyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1781286022),
('wklJeD3wE2SSDegdancbbZnqgucLe5sxnFnpEitB', NULL, '176.74.199.22', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_1_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1.2 Mobile/15E148 Safari/604', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWlNEWHRxT1lBUUszTmRncXFFb3VzYjJleHF3NldjZlJoVm82OUwxSSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1781286023),
('tkhrOREu1UaV4bNgU0PWcBhDTeBz1XRLTrrYLsnQ', NULL, '159.89.174.125', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWW5kVUJGemN4VHVFcHQyUXZYZ0dicnpaRFBJb3JwSUU2R3FJdXlLYSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781313786),
('RfXnOemUvygnmR0YwC3cn8jdzsAOCXixrxZYJWlg', NULL, '159.89.174.125', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiM2JvendSaTM3Z0lJZ2p5cUtsU1N5ZnhRa1NEUzExSnZPVUx4VG1XUCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1781313791),
('KA1uCQ5CHoqwlsZFpw1ZzNe0chSQb2IyNhgheHOC', NULL, '104.248.243.2', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNjVKbkVLeDA5R29XR1FxeTU4c2xHRGFaTXJLVmFjUjlJaVZYNDFUOCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781405069),
('BAUHmxtJRplyrODGKPvCfooMRha2pVuHDhDK9BH1', NULL, '104.248.243.2', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoieWN6S2NNMjBEUXlNejQ2WDRJRzl2QmtTaVF2RWRWUkl0QWk4OHdYVSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1781405070),
('YeYJcYWZwnH4VTO826EEq8b1XX2HE4k0XQlexwbh', NULL, '137.74.246.152', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUlBpWUNzdUsxSFIzMURaTXJDZTI1Mm1zalZFNFJ2RWhxYnRPdVBoeSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781424373),
('z0LimCrmRTUVQgRrDDktoZuNamLDN3OxjtWyaYvt', NULL, '159.65.202.137', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoic0hLVXo3WUN0VnFBNWVKNHZZenlNazJoV01pYm0ya0JONmV4VDFzZSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781488547),
('LsHpuTYhJzQP9B28L5FK6uRsvWjnjzsNgJQOVIHv', NULL, '66.249.93.99', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRjdvMFdqN1JCY1Z5ZjJkMFc3WTFDd3Z1QXlmTFVrR3Z1TDdPQjB5dSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo3MToiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9jYXRpb24vZmljaGUtZGVwYXJ0cz9kYXRlPTIwMjYtMDYtMTQiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo3MToiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9jYXRpb24vZmljaGUtZGVwYXJ0cz9kYXRlPTIwMjYtMDYtMTQiO3M6NToicm91dGUiO3M6MjI6ImxvY2F0aW9uLmZpY2hlLWRlcGFydHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1781430989),
('eYU37WsRBUcoZMaQI8eIjvHMy6a5RRSYrmqr8bAX', NULL, '66.249.93.99', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRjRTYXBQNzRzZ1MwdU9UWWczUDd6VE03eWxaQlJEMlVtdmxaTkFOTyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781430989),
('kJNbOIVM7kFnuKygqkjkwbeq2Zlyyr5u32dpKZFS', NULL, '159.65.202.137', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibUtZcjZnOWJobnNpdjQ3RWoxekRTOFFKcnpoMnpBSWpGanhDNHliOSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1781488549),
('rDYrhyHSFMyKnRG1GoFTXTyBhU5IAiK1fGwCP6HJ', NULL, '45.130.203.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUE9SN0gyNHR4WWhLVDliVFQwSnY4a0Rjd3pJVno0RmN6cU1ZMHpBdCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781668252),
('xMoaVzLGtVfMIooj0TlT35Fqu6KgJSbr2UxXMJVj', 5, '127.0.0.1', 'Symfony', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiWDYxT2VQRTZ2aXoxUFZpanljUUl3R2ZVdW1sVzFkTExMbXRuV003SSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781640820),
('cHuLRLH59QqCFb4WLQCIJIPn22oiCIkJnCA4saQx', 6, '127.0.0.1', 'Symfony', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMVNaT2dJN1dOdnExMEwyRU5CdGFRMFdtUThPRVZxNEVGZU9VVGhVMSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2FwaS9hcnRpY2xlcy80L3N0b2NrLW1vdmVtZW50cyI7czo1OiJyb3V0ZSI7czoyNzoiZ2VuZXJhdGVkOjp0Tk8xc1dlclljZFAydGZ6Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781640820),
('Hyi6ah01Qh5TJ80U97h87CqV246jeMb0YEp1jK7t', 7, '127.0.0.1', 'Symfony', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiUmhCVUR1T2k2M0lYU0lucWs2ejhwMzRLOU1nZHN4ODJxTElVS0g2diI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781640820),
('PqG8u2lxAp3L45Ns6uCcFGn37zZpqAAo8wNK57tf', 8, '127.0.0.1', 'Symfony', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiUUlhZ2ZjUHdCQmlSUnlnZks2V1NjWW5LZlM0MGdaTmJkMkNXQWFSVCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781640820),
('luI0suR2lCnzNAfCbf3y4jtktsN8s3qKcUCGEfKT', 10, '127.0.0.1', 'Symfony', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiWTdhU0dtT0g5VHVQTEtKSW8zcHlUNFo3eVdGT0trY2pPS2lXMjdhSiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781640820),
('UQ8og4AiLEv8e3kH3cUyZxLnVnqHmLHS36dhRZDI', NULL, '45.130.203.134', 'Mozilla/5.0 (Kubuntu; Linux i686) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidVlBaE5HcldvazhFcGg5UW9seTVkQUM4MXpVUDBGYTZydFRKS0ZYMiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781688958),
('IWRYf99vv7wfjJznAyC6Sm9XuPpOT8cQVdRBHMnw', NULL, '34.59.208.119', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSGpCck1kWG02UmVTNmJzdlZ0THNiajhLVDBsZnd3V2FSRHFodjJzYiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1781688994),
('fcWUFGTgXO6bf7c4EYQLWwFu1Ocs81wE5T2mnU8O', NULL, '34.12.225.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWmhoRFJIeGVUVmIxSzQ4SGgybDYwbnlsWFBmT3Z5Mkc4dlRWM3psdCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1781757868),
('i7EwEZHaZN7thIsLmENG3filn78Odo3ocF0uNFLp', NULL, '34.61.232.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSGJrTUF4bzJLaXZLekxXRFRyQUljOTZRd3RYOHZzQUZuRUt6U3JCVSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781840707),
('L9jE3yhrvRss1B9WbIEAFzOPPlurqVWpGRxH24U3', NULL, '34.61.232.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR1BIYlVkcExCd3ZBbGsycXVBYlVtOXd2Uk43Q3lBdEVsd2tpUUxZVCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781840708),
('fLD0SK4PstJwoKLZrfiFxkktPAtEVt9Y5LPmmrMZ', NULL, '34.61.232.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiV0ZDOW1VQ1RyekJkQ0JKTUJEWFZKVEJiMFBTa2ZMcjVwT0w3UlU4MyI7czo1OiJzdGF0ZSI7czo0MDoieEFRUkZib3p2YkNhUTR3TUhmQnFadWhQclBHa1JxdE5TY2FaNHkxTSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2F1dGgvZ29vZ2xlIjtzOjU6InJvdXRlIjtzOjIwOiJhdXRoLmdvb2dsZS5yZWRpcmVjdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781840709),
('oWPTAtKwixAzITJR13fsinaalzVWobO6l7VnWF2k', NULL, '34.24.22.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRUJndnByUXFRWTVIeURtTHA2eFNHOFVqYUpzckhzS01LWDdoVWhaaCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781929957),
('vRPA57ceQM6wBNDZQRBAszN3AqCd8L8lH6x7PMsd', NULL, '34.24.22.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoianJXMlU2UzVZcGZ6aGdPSFY1akVIV2phNnpzVzQzdUlxS0dMOGFXVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781929958),
('xqtv1B0QrWEi2hqCPrRMcDFhX20GNixoUEIFSrNJ', NULL, '34.24.22.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaFRXY2p1YVFIUXZrcGdTVlJHdzN5WkY2aEtYWXhLUk1pQ0FOVzFWUCI7czo1OiJzdGF0ZSI7czo0MDoicGhGR1FpNmZyaDU1QU9CcXRPWlBFaEJzejJEa1JnbjA5cmhiMzVSZiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2F1dGgvZ29vZ2xlIjtzOjU6InJvdXRlIjtzOjIwOiJhdXRoLmdvb2dsZS5yZWRpcmVjdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781929959),
('QUHa2fLpHlXUgtMUXRghvUz4N1wA1g5iGUnMcsl8', NULL, '137.74.246.152', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRWJBdm01NXJjUlRrTkRpS1E2NGNiQlRIUHRiNjgydmdvTVBuSzA5SCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782029179),
('ilR4AzrzNFckyHkbEvWWF2JsAluPW4CFejaX3oOc', NULL, '137.74.246.152', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZnFFWkU4MnhQYTFhMXFJdnk2TWpGZ2ZaNXpQR0o0YThHRVUyTkRxRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782029179),
('zY5aQvIIzoT40Dalp8lJ0H1odLFiuzcKbL51xZLG', NULL, '35.153.7.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMW1taHFmTmZXQkFhdUF5bkpjaWJEelYzTDJRNTRLM3Zyd1NQRDBHWiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cHM6Ly93d3cuYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vd3d3LmFkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782332343),
('1eY1oaM4LkwdMzTU4Xbxux8c6wuAT3BYWlZJvdOD', NULL, '35.153.7.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTU5LRlN0WDhTbENvTjd6WGVmUWRjcUJJTXdPT1JSODlCQU1vdjE2YSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vd3d3LmFkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782332343),
('GpoVU5Q3rekQsLL4PJz26HSGysGz9loSKihSe1DY', NULL, '128.90.161.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Edge/115.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRzlVRVFBZ0pKNGFibWlpSEdVMm5GU2Zpa0pKRVMzSEVXVFJLSnJtYyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782405162),
('HavHx5s4A3GFbjNChEWtmKVf37kJEu3TeXlCzL1i', NULL, '128.90.161.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Edge/115.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibXlWYjE0RmNjTFY3NXhma0NsdkY3MUtOdTVqQ3NCWmFQSjRPWkd1SiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782405163),
('10gVagqltAD9b0EC37ZzdehLv6oAA5vAZQW5f1J1', NULL, '185.247.137.155', 'Mozilla/5.0 (compatible; InternetMeasurement/1.0; +https://internet-measurement.com/)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQ05ncG9CSEJXcGVrbm9CT1JYcTJTaVNJNkx1bEZwWENEbDM3MEVrdCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782496407),
('u70An2Cp52NgK6Xh8F3x7kUFdtQfIaarvizI0p5l', NULL, '185.247.137.155', 'Mozilla/5.0 (compatible; InternetMeasurement/1.0; +https://internet-measurement.com/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRzVJa1hFempmNEhmS1dqM1VzTzNwQzhXcldndXY4VDB1bHFDT2xPdyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782496408),
('DahZOvkNErk6SB6myUdJDkMei0W4NQmUBK7yMnfT', NULL, '34.195.125.215', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQmw4dmxLQ1Q3c21HWFVSSzNQY0lOY2g0MlVPVk5CSW1tT0dDa05ucSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cHM6Ly93d3cuYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vd3d3LmFkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782575234),
('s3wjnNyJAl4MCr1vwMj3bAnVpozFOGrYLkZvZdHN', NULL, '34.195.125.215', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRThtWkF1NFhYZ2xMOHJQZENFellwSUVBaE9GSTdDUUJ0dXYwdmxMdiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vd3d3LmFkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782575237),
('N4lWF5ADIXKAybp66bYfPGDtSRZxCOeD72emtqga', NULL, '64.23.136.166', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOHVuTVhBWGVWT2tBZ1huNWw0WVRKTEZNVXNHYmZOTU10QjdzVUhraCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782628822),
('N845TmJsPjsgddyAHWhYMfSzp1eiYbF46MUNEIBR', NULL, '64.23.136.166', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoia2JIMXkwUXZBenEybVNuOUdpdEhxRXdDRWQza0dCamNkV2hsVFo2ZCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozODoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782628827),
('lNdFdkGgR5F13yTRSQRwL53N6855pqIBopyHo3PI', NULL, '137.74.246.152', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoickJUSFF3b0wzSkw5UEFJbWUzeVRhbUxMaW9HallOOTY5NXJFT3doRiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782634096),
('P7EUNSadoowETxmSLW8CL2rVsuS6ND2FC733rgP5', NULL, '35.254.248.95', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZzlQaUFPRlZBM05tRFRMSzhIQ1pFQ3hTcW1QZXlvdGVpTjZNdk96RyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2FkbWluLmxlc3ZlbG9zZGFybW9yLmJ6aCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vYWRtaW4ubGVzdmVsb3NkYXJtb3IuYnpoIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782738713),
('DVAmcH4c8iuWO3p1XjjAPcxAEt1Ak6vrXDR8EALG', NULL, '35.254.248.95', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU1R3ZnZZUWdBalo5a2NBNTFOdGdmTTFzQ3dRUkFrcDM5YUNVVkIyayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hZG1pbi5sZXN2ZWxvc2Rhcm1vci5iemgvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782738713);

-- --------------------------------------------------------

--
-- Structure de la table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `article_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `type` enum('manual_in','manual_out','quote_consumption','maintenance_consumption') NOT NULL,
  `source_type` varchar(100) DEFAULT NULL,
  `source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `unit_price_ht` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `article_id`, `quantity`, `type`, `source_type`, `source_id`, `unit_price_ht`, `note`, `created_at`, `updated_at`) VALUES
(1, 3, 10, 'manual_in', NULL, NULL, NULL, NULL, '2026-06-16 20:13:40', '2026-06-16 20:13:40'),
(2, 4, 10, 'manual_in', NULL, NULL, NULL, NULL, '2026-06-16 20:13:40', '2026-06-16 20:13:40'),
(3, 4, -2, 'manual_out', NULL, NULL, NULL, NULL, '2026-06-16 20:13:40', '2026-06-16 20:13:40'),
(4, 5, 5, 'manual_in', NULL, NULL, NULL, NULL, '2026-06-16 20:13:40', '2026-06-16 20:13:40'),
(5, 6, -1, 'quote_consumption', NULL, NULL, NULL, 'Non reprehenderit neque veritatis molestias ipsa illum omnis.', '2026-06-16 20:13:40', '2026-06-16 20:13:40'),
(6, 7, 10, 'manual_in', NULL, NULL, NULL, NULL, '2026-06-16 20:13:40', '2026-06-16 20:13:40'),
(7, 7, -3, 'manual_out', NULL, NULL, NULL, NULL, '2026-06-16 20:13:40', '2026-06-16 20:13:40'),
(8, 7, -1, 'quote_consumption', NULL, NULL, NULL, NULL, '2026-06-16 20:13:40', '2026-06-16 20:13:40'),
(9, 7, 5, 'manual_in', NULL, NULL, NULL, NULL, '2026-06-16 20:13:40', '2026-06-16 20:13:40'),
(10, 1, 25, 'manual_in', NULL, NULL, 321, NULL, '2026-06-16 20:28:04', '2026-06-16 20:28:04'),
(11, 11, 38, 'manual_in', NULL, NULL, 1576, NULL, '2026-06-24 12:55:30', '2026-06-24 12:55:30'),
(12, 12, 1, 'manual_in', NULL, NULL, 200, 'livré en sachet de deux', '2026-06-24 14:38:05', '2026-06-24 14:38:05'),
(13, 13, 10, 'manual_in', NULL, NULL, 495, 'fournis en lot de 2', '2026-06-26 15:55:01', '2026-06-26 15:55:01');

-- --------------------------------------------------------

--
-- Structure de la table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'CGN', 1, '2026-06-16 19:18:19', '2026-06-16 19:18:19');

-- --------------------------------------------------------

--
-- Structure de la table `upload_tokens`
--

CREATE TABLE `upload_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `token` char(36) NOT NULL,
  `context_type` varchar(255) NOT NULL,
  `context_id` bigint(20) UNSIGNED DEFAULT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `max_uses` int(10) UNSIGNED NOT NULL DEFAULT 10,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `upload_tokens`
--

INSERT INTO `upload_tokens` (`id`, `token`, `context_type`, `context_id`, `expires_at`, `used_count`, `max_uses`, `created_at`, `updated_at`) VALUES
(1, 'f6ce5b6a-adb9-4fb0-8501-91e0786d1f33', 'message', 1, '2026-03-08 18:32:25', 1, 10, '2026-03-08 18:32:07', '2026-03-08 18:32:25'),
(2, '728f3b2e-c939-4609-a2b0-f1c645e7f1d2', 'message', 1, '2026-03-08 18:33:36', 1, 10, '2026-03-08 18:32:51', '2026-03-08 18:33:36'),
(3, '8b3b471f-8377-4e8a-8eda-ee90d803a90d', 'message', 1, '2026-03-08 20:45:41', 1, 10, '2026-03-08 20:45:24', '2026-03-08 20:45:41'),
(4, '10b04881-b3f3-4754-acd6-1b47cbdf2d32', 'message', 8, '2026-03-10 10:19:42', 0, 10, '2026-03-10 10:04:42', '2026-03-10 10:04:42');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `work_mode` varchar(20) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `work_mode`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Jonathan', 'jnt.marois@gmail.com', 'atelier', '2026-03-09 16:38:23', 'ubnLkfozZO4iombSukpp7TxzJCPuuz9LYFsn9Y7pUwyIqqzNCbok7c9XWBkOs8Gz', NULL, NULL, NULL, NULL, '2026-01-21 09:32:09', '2026-01-21 09:32:09'),
(2, 'Nicolas', 'lesvelosdarmorbzh@gmail.com', 'comptoir', '2026-03-09 16:38:23', 'oHhCtJM7w7QG53HGPKxj2BGw59OnWOZRPYJLtCGfuvuNGTfCzFfkUCJXUHLfpK5q', NULL, NULL, NULL, NULL, '2026-01-21 10:00:15', '2026-01-21 10:00:15'),
(3, 'Julien', 'julien2705@gmail.com', 'julien', '2026-03-09 16:38:23', 'x7m5FU2Rt1U5WdnaSf1pQEwBH8AlvM9SMqE9ZqHd1kJ1N135Cf5IvrrgAquvWNWC', NULL, NULL, NULL, NULL, '2026-01-28 09:28:04', '2026-01-28 09:28:04'),
(4, 'Benjamin Roche', 'payet.charles@example.org', NULL, '2026-06-16 20:13:38', '$2y$12$P3z.CtIswaqqMkDFI4/1lejXtfWHdeQGstGZn3Z4yxo/lIOFySMwK', NULL, NULL, NULL, 'GOF77lOIVT', '2026-06-16 20:13:38', '2026-06-16 20:13:38'),
(5, 'Christiane Vasseur-Pelletier', 'laroche.josephine@example.net', NULL, '2026-06-16 20:13:40', '$2y$12$P3z.CtIswaqqMkDFI4/1lejXtfWHdeQGstGZn3Z4yxo/lIOFySMwK', NULL, NULL, NULL, 'Zl0vE320uM', '2026-06-16 20:13:40', '2026-06-16 20:13:40'),
(6, 'Matthieu Lemonnier', 'qperon@example.com', NULL, '2026-06-16 20:13:40', '$2y$12$P3z.CtIswaqqMkDFI4/1lejXtfWHdeQGstGZn3Z4yxo/lIOFySMwK', NULL, NULL, NULL, 'wQ1q889pnZ', '2026-06-16 20:13:40', '2026-06-16 20:13:40'),
(7, 'Célina Lamy', 'adele95@example.com', NULL, '2026-06-16 20:13:40', '$2y$12$P3z.CtIswaqqMkDFI4/1lejXtfWHdeQGstGZn3Z4yxo/lIOFySMwK', NULL, NULL, NULL, 'hyNr7I57HR', '2026-06-16 20:13:40', '2026-06-16 20:13:40'),
(8, 'Joseph Le Fernandes', 'remy.marques@example.com', NULL, '2026-06-16 20:13:40', '$2y$12$P3z.CtIswaqqMkDFI4/1lejXtfWHdeQGstGZn3Z4yxo/lIOFySMwK', NULL, NULL, NULL, 'zbOXxGvRtB', '2026-06-16 20:13:40', '2026-06-16 20:13:40'),
(9, 'Guy Hamon', 'sallard@example.com', NULL, '2026-06-16 20:13:40', '$2y$12$P3z.CtIswaqqMkDFI4/1lejXtfWHdeQGstGZn3Z4yxo/lIOFySMwK', NULL, NULL, NULL, 'HWLqYCAmIa', '2026-06-16 20:13:40', '2026-06-16 20:13:40'),
(10, 'Christophe Besson', 'margot08@example.net', NULL, '2026-06-16 20:13:40', '$2y$12$P3z.CtIswaqqMkDFI4/1lejXtfWHdeQGstGZn3Z4yxo/lIOFySMwK', NULL, NULL, NULL, 'saVb1bCVYF', '2026-06-16 20:13:40', '2026-06-16 20:13:40');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `agenda_meta`
--
ALTER TABLE `agenda_meta`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `articles_reference_unique` (`reference`),
  ADD KEY `articles_article_subcategory_id_foreign` (`article_subcategory_id`),
  ADD KEY `articles_brand_id_foreign` (`brand_id`),
  ADD KEY `articles_supplier_id_foreign` (`supplier_id`);

--
-- Index pour la table `article_categories`
--
ALTER TABLE `article_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `article_categories_name_unique` (`name`);

--
-- Index pour la table `article_subcategories`
--
ALTER TABLE `article_subcategories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `article_subcategories_article_category_id_name_unique` (`article_category_id`,`name`);

--
-- Index pour la table `authorized_emails`
--
ALTER TABLE `authorized_emails`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `authorized_emails_email_unique` (`email`);

--
-- Index pour la table `bikes`
--
ALTER TABLE `bikes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bikes_bike_type_id_status_index` (`status`),
  ADD KEY `bikes_bike_category_id_foreign` (`bike_category_id`),
  ADD KEY `bikes_bike_size_id_foreign` (`bike_size_id`);

--
-- Index pour la table `bike_categories`
--
ALTER TABLE `bike_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bike_categories_name_unique` (`name`);

--
-- Index pour la table `bike_maintenance_logs`
--
ALTER TABLE `bike_maintenance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bike_maintenance_logs_bike_id_status_index` (`bike_id`,`status`),
  ADD KEY `bike_maintenance_logs_article_id_foreign` (`article_id`);

--
-- Index pour la table `bike_models`
--
ALTER TABLE `bike_models`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bike_models_name_unique` (`name`);

--
-- Index pour la table `bike_sizes`
--
ALTER TABLE `bike_sizes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bike_sizes_name_unique` (`name`);

--
-- Index pour la table `bike_types`
--
ALTER TABLE `bike_types`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brands_name_unique` (`name`);

--
-- Index pour la table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clients_email_unique` (`email`);

--
-- Index pour la table `demandes_partenaire`
--
ALTER TABLE `demandes_partenaire`
  ADD PRIMARY KEY (`id`),
  ADD KEY `demandes_partenaire_partenaire_id_foreign` (`partenaire_id`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`) USING HASH;

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`(250));

--
-- Index pour la table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_uuid_unique` (`uuid`),
  ADD KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `media_order_column_index` (`order_column`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_author_user_id_foreign` (`author_user_id`),
  ADD KEY `messages_recipient_user_id_foreign` (`recipient_user_id`),
  ADD KEY `messages_status_recipient_user_id_index` (`status`,`recipient_user_id`),
  ADD KEY `messages_status_author_user_id_index` (`status`,`author_user_id`),
  ADD KEY `messages_category_id_foreign` (`category_id`),
  ADD KEY `messages_last_activity_at_index` (`last_activity_at`);

--
-- Index pour la table `message_categories`
--
ALTER TABLE `message_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `message_categories_slug_unique` (`slug`);

--
-- Index pour la table `message_replies`
--
ALTER TABLE `message_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_replies_message_id_index` (`message_id`),
  ADD KEY `message_replies_author_user_id_foreign` (`author_user_id`),
  ADD KEY `message_replies_recipient_user_id_foreign` (`recipient_user_id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `monthly_kpis`
--
ALTER TABLE `monthly_kpis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `monthly_kpis_metier_year_month_unique` (`metier`,`year`,`month`),
  ADD KEY `monthly_kpis_metier_year_index` (`metier`,`year`);

--
-- Index pour la table `partenaires`
--
ALTER TABLE `partenaires`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `partenaires_email_unique` (`email`) USING HASH;

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `quotes`
--
ALTER TABLE `quotes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quotes_reference_type_unique` (`reference`,`is_invoice`) USING HASH,
  ADD KEY `quotes_client_id_foreign` (`client_id`);

--
-- Index pour la table `quote_lines`
--
ALTER TABLE `quote_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quote_lines_quote_id_foreign` (`quote_id`),
  ADD KEY `quote_lines_article_id_foreign` (`article_id`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservations_client_id_foreign` (`client_id`);

--
-- Index pour la table `reservation_items`
--
ALTER TABLE `reservation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_items_reservation_id_foreign` (`reservation_id`),
  ADD KEY `reservation_items_bike_type_id_foreign` (`bike_type_id`);

--
-- Index pour la table `reservation_payments`
--
ALTER TABLE `reservation_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_payments_reservation_id_foreign` (`reservation_id`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index pour la table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_movements_source_type_source_id_index` (`source_type`,`source_id`),
  ADD KEY `stock_movements_article_id_index` (`article_id`);

--
-- Index pour la table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_name_unique` (`name`);

--
-- Index pour la table `upload_tokens`
--
ALTER TABLE `upload_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `upload_tokens_token_unique` (`token`),
  ADD KEY `upload_tokens_token_expires_at_index` (`token`,`expires_at`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`) USING HASH;

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `agenda_meta`
--
ALTER TABLE `agenda_meta`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `article_categories`
--
ALTER TABLE `article_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `article_subcategories`
--
ALTER TABLE `article_subcategories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `authorized_emails`
--
ALTER TABLE `authorized_emails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `bikes`
--
ALTER TABLE `bikes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT pour la table `bike_categories`
--
ALTER TABLE `bike_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `bike_maintenance_logs`
--
ALTER TABLE `bike_maintenance_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `bike_models`
--
ALTER TABLE `bike_models`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `bike_sizes`
--
ALTER TABLE `bike_sizes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=441;

--
-- AUTO_INCREMENT pour la table `demandes_partenaire`
--
ALTER TABLE `demandes_partenaire`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT pour la table `message_categories`
--
ALTER TABLE `message_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `message_replies`
--
ALTER TABLE `message_replies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT pour la table `monthly_kpis`
--
ALTER TABLE `monthly_kpis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT pour la table `partenaires`
--
ALTER TABLE `partenaires`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `quotes`
--
ALTER TABLE `quotes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=301;

--
-- AUTO_INCREMENT pour la table `quote_lines`
--
ALTER TABLE `quote_lines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2273;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=214;

--
-- AUTO_INCREMENT pour la table `reservation_items`
--
ALTER TABLE `reservation_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1455;

--
-- AUTO_INCREMENT pour la table `reservation_payments`
--
ALTER TABLE `reservation_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT pour la table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `upload_tokens`
--
ALTER TABLE `upload_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
