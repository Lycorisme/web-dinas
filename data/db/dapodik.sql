-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 17, 2025 at 10:05 AM
-- Server version: 5.7.39
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dapodik`
--

-- --------------------------------------------------------

--
-- Table structure for table `import_log`
--

CREATE TABLE `import_log` (
  `id` int(11) NOT NULL,
  `process_type` enum('kabupaten','kecamatan','sekolah','transfer') NOT NULL,
  `url_induk_id` int(11) DEFAULT NULL,
  `total_processed` int(11) DEFAULT '0',
  `total_success` int(11) DEFAULT '0',
  `total_failed` int(11) DEFAULT '0',
  `status` enum('running','completed','failed','cancelled') DEFAULT 'running',
  `started_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL,
  `error_message` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabel untuk log import (terpisah dari scraping_logs)';

--
-- Dumping data for table `import_log`
--

INSERT INTO `import_log` (`id`, `process_type`, `url_induk_id`, `total_processed`, `total_success`, `total_failed`, `status`, `started_at`, `completed_at`, `error_message`) VALUES
(3, 'kecamatan', 1, 13, 13, 0, 'completed', '2025-09-16 02:08:03', '2025-09-16 02:21:46', NULL),
(8, 'kabupaten', 1, 0, 0, 0, 'cancelled', '2025-09-16 02:34:23', NULL, NULL),
(9, 'kabupaten', 1, 1, 1, 0, 'completed', '2025-09-16 02:34:24', '2025-09-16 02:34:50', NULL),
(10, 'kabupaten', 1, 1, 1, 0, 'completed', '2025-09-16 02:41:54', '2025-09-16 02:45:17', NULL),
(11, 'kabupaten', 1, 1, 1, 0, 'completed', '2025-09-16 02:41:55', '2025-09-16 02:44:22', NULL),
(12, 'kabupaten', 1, 1, 1, 0, 'completed', '2025-09-16 02:41:56', '2025-09-16 02:43:01', NULL),
(13, 'kabupaten', 1, 1, 1, 0, 'completed', '2025-09-16 02:41:58', '2025-09-16 02:42:57', NULL),
(14, 'kecamatan', 1, 0, 0, 0, 'cancelled', '2025-09-16 02:43:41', NULL, NULL),
(15, 'kecamatan', 1, 13, 11, 2, 'completed', '2025-09-16 02:43:41', '2025-09-16 03:01:45', NULL),
(16, 'kecamatan', 1, 13, 11, 2, 'completed', '2025-09-16 02:43:42', '2025-09-16 03:00:56', NULL),
(17, 'kecamatan', 1, 0, 0, 0, 'cancelled', '2025-09-16 13:23:42', NULL, NULL),
(18, 'kabupaten', 1, 0, 0, 0, 'cancelled', '2025-09-16 13:31:47', NULL, NULL),
(19, 'kabupaten', 1, 1, 1, 0, 'completed', '2025-09-16 19:01:47', '2025-09-16 19:02:08', NULL),
(20, 'kabupaten', 1, 1, 1, 0, 'completed', '2025-09-16 19:03:49', '2025-09-16 19:05:39', NULL),
(21, 'kabupaten', 1, 1, 1, 0, 'completed', '2025-09-16 19:03:59', '2025-09-16 19:04:17', NULL),
(22, 'kecamatan', 1, 13, 10, 3, 'completed', '2025-09-16 19:13:53', '2025-09-16 19:39:33', NULL),
(23, 'kecamatan', 1, 13, 13, 0, 'completed', '2025-09-16 19:14:16', '2025-09-16 19:34:35', NULL),
(24, 'kecamatan', 1, 1, 0, 1, 'failed', '2025-09-16 20:17:27', '2025-09-16 20:21:13', '1 kabupaten failed to process.'),
(25, 'kecamatan', 1, 1, 1, 0, 'completed', '2025-09-16 20:22:20', '2025-09-16 20:22:59', NULL),
(26, 'sekolah', 1, 1, 1, 0, 'completed', '2025-09-16 23:08:38', '2025-09-16 23:20:28', NULL),
(27, 'sekolah', 1, 1, 1, 0, 'completed', '2025-09-16 23:11:54', '2025-09-16 23:14:00', NULL),
(28, 'transfer', 1, 1, 1, 0, 'completed', '2025-09-16 23:29:39', '2025-09-16 23:29:39', NULL),
(29, 'transfer', 1, 1, 1, 0, 'completed', '2025-09-17 04:53:52', '2025-09-17 04:53:52', NULL),
(30, 'transfer', 1, 1, 1, 0, 'completed', '2025-09-17 04:54:24', '2025-09-17 04:54:24', NULL),
(31, 'transfer', 1, 1, 1, 0, 'completed', '2025-09-17 04:55:36', '2025-09-17 04:55:36', NULL),
(32, 'transfer', 1, 1, 1, 0, 'completed', '2025-09-17 05:00:16', '2025-09-17 05:00:16', NULL),
(33, 'transfer', 1, 1, 1, 0, 'completed', '2025-09-17 05:30:30', '2025-09-17 05:30:30', NULL),
(34, 'transfer', 1, 1, 1, 0, 'completed', '2025-09-17 05:40:04', '2025-09-17 05:40:04', NULL),
(35, 'transfer', 1, 1, 0, 0, 'completed', '2025-09-17 05:40:44', '2025-09-17 05:40:44', NULL),
(36, 'transfer', 1, 1, 1, 0, 'completed', '2025-09-17 05:45:19', '2025-09-17 05:45:19', NULL),
(37, 'transfer', 1, 1, 1, 0, 'completed', '2025-09-17 05:51:47', '2025-09-17 05:51:47', NULL),
(38, 'transfer', 1, 1, 1, 0, 'completed', '2025-09-17 05:59:30', '2025-09-17 05:59:30', NULL),
(39, 'transfer', 1, 1, 1, 0, 'completed', '2025-09-17 06:01:29', '2025-09-17 06:01:29', NULL),
(40, 'transfer', 1, 1, 1, 0, 'completed', '2025-09-17 06:02:12', '2025-09-17 06:02:12', NULL),
(41, 'transfer', 1, 1, 1, 0, 'completed', '2025-09-17 06:08:54', '2025-09-17 06:08:54', NULL),
(42, 'transfer', 1, 1, 1, 0, 'completed', '2025-09-17 06:12:05', '2025-09-17 06:12:05', NULL),
(43, 'transfer', 1, 1, 1, 0, 'completed', '2025-09-17 06:14:00', '2025-09-17 06:14:00', NULL),
(44, 'sekolah', 1, 1, 1, 0, 'completed', '2025-09-17 06:17:52', '2025-09-17 06:19:41', NULL),
(45, 'sekolah', 1, 1, 0, 0, 'cancelled', '2025-09-17 06:57:31', NULL, NULL),
(46, 'sekolah', 1, 1, 1, 0, 'completed', '2025-09-17 06:57:46', '2025-09-17 07:00:49', NULL),
(47, 'sekolah', 1, 1, 1, 0, 'completed', '2025-09-17 06:59:51', '2025-09-17 07:00:23', NULL),
(48, 'sekolah', 1, 1, 1, 0, 'completed', '2025-09-17 07:03:57', '2025-09-17 07:06:16', NULL),
(49, 'sekolah', 1, 1, 0, 0, 'cancelled', '2025-09-17 07:07:43', NULL, NULL),
(50, 'kabupaten', 1, 0, 0, 0, 'cancelled', '2025-09-17 07:07:54', NULL, NULL),
(51, 'kabupaten', 1, 1, 1, 0, 'completed', '2025-09-17 07:08:57', '2025-09-17 07:09:46', NULL),
(52, 'kecamatan', 1, 1, 0, 0, 'cancelled', '2025-09-17 07:23:10', NULL, NULL),
(53, 'kecamatan', 1, 1, 0, 0, 'cancelled', '2025-09-17 07:24:01', NULL, NULL),
(54, 'sekolah', 1, 1, 0, 0, 'cancelled', '2025-09-17 07:24:14', NULL, NULL),
(55, 'transfer', 1, 1, 1, 0, 'completed', '2025-09-17 07:24:17', '2025-09-17 07:24:17', NULL),
(56, 'kecamatan', 1, 1, 0, 1, 'failed', '2025-09-17 08:16:46', '2025-09-17 08:20:55', '1 kabupaten failed to process.'),
(57, 'transfer', 1, 1, 1, 0, 'completed', '2025-09-17 08:59:11', '2025-09-17 08:59:11', NULL),
(58, 'transfer', 1, 1, 0, 0, 'completed', '2025-09-17 09:17:34', '2025-09-17 09:17:34', NULL),
(59, 'transfer', 1, 1, 1, 0, 'completed', '2025-09-17 09:17:45', '2025-09-17 09:17:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kabupaten_kota`
--

CREATE TABLE `kabupaten_kota` (
  `id_kabupaten` int(11) NOT NULL,
  `nama_kabupaten` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_provinsi_fk` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kabupaten_kota`
--

INSERT INTO `kabupaten_kota` (`id_kabupaten`, `nama_kabupaten`, `id_provinsi_fk`) VALUES
(4, 'Balangan', 3);

-- --------------------------------------------------------

--
-- Table structure for table `kabupaten_scrape`
--

CREATE TABLE `kabupaten_scrape` (
  `id` int(11) NOT NULL,
  `kode_kabupaten` varchar(255) NOT NULL,
  `nama_kabupaten` varchar(255) NOT NULL,
  `url` varchar(500) NOT NULL,
  `url_induk_id` int(11) NOT NULL,
  `status` enum('active','inactive','processed') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabel untuk menyimpan data kabupaten hasil scraping';

--
-- Dumping data for table `kabupaten_scrape`
--

INSERT INTO `kabupaten_scrape` (`id`, `kode_kabupaten`, `nama_kabupaten`, `url`, `url_induk_id`, `status`, `created_at`, `updated_at`) VALUES
(53, '1', 'Kab. Banjar', 'https://dapo.kemendikdasmen.go.id/sp/2/150100', 1, 'active', '2025-09-16 02:42:57', '2025-09-16 02:42:57'),
(54, '2', 'Kota Banjarmasin', 'https://dapo.kemendikdasmen.go.id/sp/2/156000', 1, 'active', '2025-09-16 02:42:57', '2025-09-16 02:42:57'),
(55, '3', 'Kab. Barito Kuala', 'https://dapo.kemendikdasmen.go.id/sp/2/150300', 1, 'active', '2025-09-16 02:42:57', '2025-09-16 02:42:57'),
(56, '4', 'Kab. Kotabaru', 'https://dapo.kemendikdasmen.go.id/sp/2/150900', 1, 'active', '2025-09-16 02:42:57', '2025-09-16 02:42:57'),
(57, '5', 'Kab. Tanah Laut', 'https://dapo.kemendikdasmen.go.id/sp/2/150200', 1, 'active', '2025-09-16 02:42:57', '2025-09-16 02:42:57'),
(58, '6', 'Kab. Tanah Bumbu', 'https://dapo.kemendikdasmen.go.id/sp/2/151100', 1, 'active', '2025-09-16 02:42:57', '2025-09-16 02:42:57'),
(59, '7', 'Kab. Tabalong', 'https://dapo.kemendikdasmen.go.id/sp/2/150800', 1, 'active', '2025-09-16 02:42:57', '2025-09-16 02:42:57'),
(60, '8', 'Kab. Hulu Sungai Tengah', 'https://dapo.kemendikdasmen.go.id/sp/2/150600', 1, 'active', '2025-09-16 02:42:57', '2025-09-16 02:42:57'),
(61, '9', 'Kab. Hulu Sungai Selatan', 'https://dapo.kemendikdasmen.go.id/sp/2/150500', 1, 'active', '2025-09-16 02:42:57', '2025-09-16 02:42:57'),
(62, '10', 'Kab. Hulu Sungai Utara', 'https://dapo.kemendikdasmen.go.id/sp/2/150700', 1, 'active', '2025-09-16 02:42:57', '2025-09-16 02:42:57'),
(63, '11', 'Kab. Balangan', 'https://dapo.kemendikdasmen.go.id/sp/2/151000', 1, 'active', '2025-09-16 02:42:57', '2025-09-16 02:42:57'),
(64, '12', 'Kota Banjarbaru', 'https://dapo.kemendikdasmen.go.id/sp/2/156100', 1, 'active', '2025-09-16 02:42:57', '2025-09-16 02:42:57'),
(65, '13', 'Kab. Tapin', 'https://dapo.kemendikdasmen.go.id/sp/2/150400', 1, 'active', '2025-09-16 02:42:57', '2025-09-16 02:42:57');

-- --------------------------------------------------------

--
-- Table structure for table `kecamatan`
--

CREATE TABLE `kecamatan` (
  `id_kecamatan` int(11) NOT NULL,
  `nama_kecamatan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_kabupaten_fk` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kecamatan`
--

INSERT INTO `kecamatan` (`id_kecamatan`, `nama_kecamatan`, `id_kabupaten_fk`) VALUES
(6, 'Lampihong', 4),
(7, 'Tebing Tinggi', 4),
(8, 'Paringin', 4);

-- --------------------------------------------------------

--
-- Table structure for table `kecamatan_scrape`
--

CREATE TABLE `kecamatan_scrape` (
  `id` int(11) NOT NULL,
  `kode_kecamatan` varchar(255) NOT NULL,
  `nama_kecamatan` varchar(255) NOT NULL,
  `url` varchar(500) NOT NULL,
  `kabupaten_scrape_id` int(11) NOT NULL,
  `status` enum('active','inactive','processed') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabel untuk menyimpan data kecamatan hasil scraping';

--
-- Dumping data for table `kecamatan_scrape`
--

INSERT INTO `kecamatan_scrape` (`id`, `kode_kecamatan`, `nama_kecamatan`, `url`, `kabupaten_scrape_id`, `status`, `created_at`, `updated_at`) VALUES
(157, '1', 'Kec. Halong', 'https://dapo.kemendikdasmen.go.id/sp/3/151006', 63, 'active', '2025-09-16 20:22:54', '2025-09-16 20:22:54'),
(158, '2', 'Kec. Lampihong', 'https://dapo.kemendikdasmen.go.id/sp/3/151001', 63, 'active', '2025-09-16 20:22:54', '2025-09-16 20:22:54'),
(159, '3', 'Kec. Paringin Selatan', 'https://dapo.kemendikdasmen.go.id/sp/3/151008', 63, 'active', '2025-09-16 20:22:54', '2025-09-16 20:22:54'),
(160, '4', 'Kec. Batu Mandi', 'https://dapo.kemendikdasmen.go.id/sp/3/151002', 63, 'active', '2025-09-16 20:22:54', '2025-09-16 20:22:54'),
(161, '5', 'Kec. Awayan', 'https://dapo.kemendikdasmen.go.id/sp/3/151003', 63, 'active', '2025-09-16 20:22:54', '2025-09-16 20:22:54'),
(162, '6', 'Kec. Paringin', 'https://dapo.kemendikdasmen.go.id/sp/3/151004', 63, 'active', '2025-09-16 20:22:54', '2025-09-16 20:22:54'),
(163, '7', 'Kec. Juai', 'https://dapo.kemendikdasmen.go.id/sp/3/151005', 63, 'active', '2025-09-16 20:22:54', '2025-09-16 20:22:54'),
(164, '8', 'Kec. Tebing Tinggi', 'https://dapo.kemendikdasmen.go.id/sp/3/151007', 63, 'active', '2025-09-16 20:22:54', '2025-09-16 20:22:54'),
(165, '1', 'Kec. Banjarmasin Utara', 'https://dapo.kemendikdasmen.go.id/sp/3/156004', 54, 'active', '2025-09-17 07:25:22', '2025-09-17 07:25:22'),
(166, '2', 'Kec. Banjarmasin Selatan', 'https://dapo.kemendikdasmen.go.id/sp/3/156001', 54, 'active', '2025-09-17 07:25:22', '2025-09-17 07:25:22'),
(167, '3', 'Kec. Banjarmasin Timur', 'https://dapo.kemendikdasmen.go.id/sp/3/156002', 54, 'active', '2025-09-17 07:25:22', '2025-09-17 07:25:22'),
(168, '4', 'Kec. Banjarmasin Tengah', 'https://dapo.kemendikdasmen.go.id/sp/3/156005', 54, 'active', '2025-09-17 07:25:22', '2025-09-17 07:25:22'),
(169, '5', 'Kec. Banjarmasin Barat', 'https://dapo.kemendikdasmen.go.id/sp/3/156003', 54, 'active', '2025-09-17 07:25:22', '2025-09-17 07:25:22');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_pengguna` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id`, `username`, `password`, `nama_pengguna`) VALUES
(1, 'admin@gmail.com', '123456', 'Administrator');

-- --------------------------------------------------------

--
-- Table structure for table `negara`
--

CREATE TABLE `negara` (
  `id_negara` int(11) NOT NULL,
  `nama_negara` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `negara`
--

INSERT INTO `negara` (`id_negara`, `nama_negara`) VALUES
(1, 'Indonesia');

-- --------------------------------------------------------

--
-- Table structure for table `provinsi`
--

CREATE TABLE `provinsi` (
  `id_provinsi` int(11) NOT NULL,
  `nama_provinsi` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_negara_fk` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `provinsi`
--

INSERT INTO `provinsi` (`id_provinsi`, `nama_provinsi`, `id_negara_fk`) VALUES
(3, 'Kalimantan Selatan', 1);

-- --------------------------------------------------------

--
-- Table structure for table `rekap_ptk_pd`
--

CREATE TABLE `rekap_ptk_pd` (
  `id` int(11) NOT NULL,
  `npsn_fk` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guru` int(11) DEFAULT '0',
  `tendik` int(11) DEFAULT '0',
  `ptk_total` int(11) DEFAULT '0',
  `pd_total` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rekap_ptk_pd`
--

INSERT INTO `rekap_ptk_pd` (`id`, `npsn_fk`, `deskripsi`, `guru`, `tendik`, `ptk_total`, `pd_total`) VALUES
(161, '30303810', 'Laki - Laki', 0, 1, 1, 30),
(162, '30303810', 'Perempuan', 8, 1, 9, 34),
(163, '30305493', 'Laki - Laki', 0, 2, 2, 2),
(164, '30305493', 'Perempuan', 1, 0, 1, 1),
(165, '30303881', 'Laki - Laki', 8, 4, 12, 85),
(166, '30303881', 'Perempuan', 9, 3, 12, 97),
(167, '70003433', 'Laki - Laki', 3, 1, 4, 53),
(168, '70003433', 'Perempuan', 11, 2, 13, 45),
(169, '69947887', 'Laki - Laki', 2, 3, 5, 29),
(170, '69947887', 'Perempuan', 5, 3, 8, 38);

-- --------------------------------------------------------

--
-- Table structure for table `rekap_rombel`
--

CREATE TABLE `rekap_rombel` (
  `id` int(11) NOT NULL,
  `npsn_fk` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tingkat_kelas` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_laki_laki` int(11) DEFAULT '0',
  `jumlah_perempuan` int(11) DEFAULT '0',
  `jumlah_total` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rekap_rombel`
--

INSERT INTO `rekap_rombel` (`id`, `npsn_fk`, `tingkat_kelas`, `jumlah_laki_laki`, `jumlah_perempuan`, `jumlah_total`) VALUES
(355, '30303810', 'Kelas 1', 5, 8, 13),
(356, '30303810', 'Kelas 2', 8, 2, 10),
(357, '30303810', 'Kelas 3', 3, 4, 7),
(358, '30303810', 'Kelas 4', 5, 7, 12),
(359, '30303810', 'Kelas 5', 3, 6, 9),
(360, '30303810', 'Kelas 6', 6, 7, 13),
(361, '30305493', 'Kelas 3', 2, 1, 3),
(362, '30303881', 'Kelas 7', 33, 34, 67),
(363, '30303881', 'Kelas 8', 32, 29, 61),
(364, '30303881', 'Kelas 9', 20, 34, 54),
(365, '70003433', 'Kelas 1', 12, 10, 22),
(366, '70003433', 'Kelas 2', 8, 9, 17),
(367, '70003433', 'Kelas 3', 11, 5, 16),
(368, '70003433', 'Kelas 4', 7, 8, 15),
(369, '70003433', 'Kelas 5', 10, 7, 17),
(370, '70003433', 'Kelas 6', 5, 6, 11),
(371, '69947887', 'Kelas 1', 2, 8, 10),
(372, '69947887', 'Kelas 2', 5, 3, 8),
(373, '69947887', 'Kelas 3', 6, 9, 15),
(374, '69947887', 'Kelas 4', 6, 3, 9),
(375, '69947887', 'Kelas 5', 4, 9, 13),
(376, '69947887', 'Kelas 6', 6, 6, 12);

-- --------------------------------------------------------

--
-- Table structure for table `rekap_sarpras`
--

CREATE TABLE `rekap_sarpras` (
  `id` int(11) NOT NULL,
  `npsn_fk` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sarana` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rekap_sarpras`
--

INSERT INTO `rekap_sarpras` (`id`, `npsn_fk`, `sarana`, `jumlah`) VALUES
(657, '30303810', 'Ruang Kelas', 0),
(658, '30303810', 'Ruang Lab', 0),
(659, '30303810', 'Ruang Perpus', 0),
(660, '30305493', 'Ruang Kelas', 6),
(661, '30305493', 'Ruang Lab', 0),
(662, '30305493', 'Ruang Perpus', 0),
(663, '30303881', 'Ruang Kelas', 9),
(664, '30303881', 'Ruang Lab', 4),
(665, '30303881', 'Ruang Perpus', 1),
(666, '70003433', 'Ruang Kelas', 6),
(667, '70003433', 'Ruang Lab', 0),
(668, '70003433', 'Ruang Perpus', 0),
(669, '69947887', 'Ruang Kelas', 6),
(670, '69947887', 'Ruang Lab', 0),
(671, '69947887', 'Ruang Perpus', 1);

-- --------------------------------------------------------

--
-- Table structure for table `scraping_logs`
--

CREATE TABLE `scraping_logs` (
  `id` int(11) NOT NULL,
  `pid` int(11) DEFAULT NULL,
  `batch_name` varchar(100) NOT NULL,
  `total_urls` int(11) DEFAULT '0',
  `url_ids` text,
  `processed_urls` int(11) DEFAULT '0',
  `success_count` int(11) DEFAULT '0',
  `failed_count` int(11) DEFAULT '0',
  `status` enum('running','completed','failed','cancelled') DEFAULT 'running',
  `started_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL,
  `error_message` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `scraping_logs`
--

INSERT INTO `scraping_logs` (`id`, `pid`, `batch_name`, `total_urls`, `url_ids`, `processed_urls`, `success_count`, `failed_count`, `status`, `started_at`, `completed_at`, `error_message`) VALUES
(91, 15876, 'Update Pilihan - 1 URL', 1, '[5]', 1, 1, 2, 'completed', '2025-09-15 11:13:50', '2025-09-15 11:16:01', NULL),
(92, 20600, 'Update Pilihan - 1 URL', 1, '[7]', 1, 1, 0, 'completed', '2025-09-17 04:24:20', '2025-09-17 04:24:51', NULL),
(93, 11372, 'Update Pilihan - 1 URL', 1, '[11]', 1, 1, 3, 'completed', '2025-09-17 05:18:40', '2025-09-17 05:22:01', NULL),
(94, 15420, 'Update Pilihan - 1 URL', 1, '[25]', 1, 1, 7, 'completed', '2025-09-17 07:26:33', '2025-09-17 07:30:29', NULL),
(95, NULL, 'Update Pilihan - 1 URL', 1, '[5]', 0, 0, 0, 'cancelled', '2025-09-17 08:51:24', '2025-09-17 08:56:31', 'Proses dibatalkan oleh pengguna.'),
(96, NULL, 'Update Pilihan - 1 URL', 1, '[24]', 0, 0, 0, 'cancelled', '2025-09-17 08:56:50', '2025-09-17 08:57:51', 'Proses dibatalkan oleh pengguna.'),
(97, 23040, 'Update Pilihan - 1 URL', 1, '[21]', 1, 1, 1, 'completed', '2025-09-17 08:58:01', '2025-09-17 08:58:40', NULL),
(98, NULL, 'Update Pilihan - 1 URL', 1, '[5]', 0, 0, 0, 'cancelled', '2025-09-17 09:09:05', '2025-09-17 09:10:35', 'Proses dibatalkan oleh pengguna.'),
(99, NULL, 'Update Pilihan - 1 URL', 1, '[10]', 0, 0, 0, 'cancelled', '2025-09-17 09:10:42', '2025-09-17 09:10:51', 'Proses dibatalkan oleh pengguna.'),
(100, NULL, 'Update Pilihan - 1 URL', 1, '[17]', 0, 0, 0, 'cancelled', '2025-09-17 09:10:59', '2025-09-17 09:11:50', 'Proses dibatalkan oleh pengguna.'),
(101, NULL, 'Update Pilihan - 1 URL', 1, '[15]', 0, 0, 0, 'cancelled', '2025-09-17 09:11:57', '2025-09-17 09:12:22', 'Proses dibatalkan oleh pengguna.'),
(102, 6872, 'Update Pilihan - 1 URL', 1, '[16]', 1, 1, 0, 'completed', '2025-09-17 09:12:31', '2025-09-17 09:12:43', NULL),
(103, NULL, 'Update Pilihan - 1 URL', 1, '[15]', 0, 0, 0, 'cancelled', '2025-09-17 09:12:58', '2025-09-17 09:18:41', 'Proses dibatalkan oleh pengguna.'),
(104, NULL, 'Update Pilihan - 1 URL', 1, '[27]', 0, 0, 0, 'cancelled', '2025-09-17 09:18:52', '2025-09-17 09:19:15', 'Proses dibatalkan oleh pengguna.'),
(105, 14112, 'Update Pilihan - 1 URL', 1, '[27]', 1, 1, 10, 'completed', '2025-09-17 09:19:23', '2025-09-17 09:23:39', NULL),
(106, NULL, 'Update Pilihan - 1 URL', 1, '[16]', 0, 0, 0, 'cancelled', '2025-09-17 09:25:46', '2025-09-17 09:32:45', 'Proses dibatalkan oleh pengguna.'),
(107, NULL, 'Update Pilihan - 1 URL', 1, '[19]', 0, 0, 0, 'cancelled', '2025-09-17 09:32:53', '2025-09-17 09:38:18', 'Proses dibatalkan oleh pengguna.'),
(108, NULL, 'Update Pilihan - 1 URL', 1, '[26]', 0, 0, 0, 'cancelled', '2025-09-17 09:38:26', '2025-09-17 09:39:49', 'Proses dibatalkan oleh pengguna.'),
(109, 14108, 'Update Pilihan - 1 URL', 1, '[26]', 1, 1, 1, 'failed', '2025-09-17 09:39:56', '2025-09-17 09:41:28', 'name \'DB_CONFIG\' is not defined'),
(110, 2248, 'Update Pilihan - 1 URL', 1, '[10]', 0, 0, 1, 'cancelled', '2025-09-17 09:44:42', '2025-09-17 09:44:59', 'Proses dibatalkan oleh pengguna.'),
(111, 19548, 'Update Pilihan - 1 URL', 1, '[20]', 0, 0, 1, 'cancelled', '2025-09-17 09:45:14', '2025-09-17 09:45:24', 'Proses dibatalkan oleh pengguna.'),
(112, 18512, 'Update Pilihan - 1 URL', 1, '[20]', 1, 1, 1, 'failed', '2025-09-17 09:45:32', '2025-09-17 09:45:59', 'name \'DB_CONFIG\' is not defined'),
(113, 23444, 'Update Pilihan - 1 URL', 1, '[21]', 1, 1, 1, 'failed', '2025-09-17 09:46:23', '2025-09-17 09:47:37', 'name \'DB_CONFIG\' is not defined'),
(114, NULL, 'Update Pilihan - 1 URL', 1, '[25]', 0, 0, 0, 'cancelled', '2025-09-17 09:48:40', '2025-09-17 09:48:44', 'Proses dibatalkan oleh pengguna.'),
(115, NULL, 'Update Pilihan - 1 URL', 1, '[22]', 0, 0, 0, 'cancelled', '2025-09-17 09:48:54', '2025-09-17 09:51:56', 'Proses dibatalkan oleh pengguna.'),
(116, NULL, 'Update Pilihan - 1 URL', 1, '[22]', 0, 0, 0, 'cancelled', '2025-09-17 09:52:16', '2025-09-17 09:55:48', 'Proses dibatalkan oleh pengguna.'),
(117, 23224, 'Update Pilihan - 1 URL', 1, '[22]', 1, 1, 4, 'completed', '2025-09-17 09:58:53', '2025-09-17 10:02:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `scraping_urls`
--

CREATE TABLE `scraping_urls` (
  `id` int(11) NOT NULL,
  `sekolah_scrape_id` int(11) DEFAULT NULL,
  `url` varchar(500) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','processed') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `scraping_urls`
--

INSERT INTO `scraping_urls` (`id`, `sekolah_scrape_id`, `url`, `description`, `status`, `created_at`, `updated_at`) VALUES
(2, NULL, 'https://dapo.kemendikdasmen.go.id/sekolah/5DC358AA5607E31C13E6', '', 'active', '2025-09-06 07:56:47', '2025-09-06 07:56:47'),
(3, NULL, 'https://dapo.kemendikdasmen.go.id/sekolah/058B93D8FF070240302E', '', 'active', '2025-09-06 14:48:45', '2025-09-06 14:48:45'),
(4, NULL, 'https://dapo.kemendikdasmen.go.id/sekolah/4D4B7D14414D0939B40A', 'SMAN 1 MARTAPURA', 'active', '2025-09-07 09:22:11', '2025-09-07 09:22:11'),
(5, NULL, 'https://dapo.kemendikdasmen.go.id/sekolah/B58C229165B26222C115', 'SMK MUHAMMADIYAH 4 AL-AMIN', 'active', '2025-09-07 20:49:27', '2025-09-07 20:49:27'),
(6, NULL, 'https://dapo.kemendikdasmen.go.id/sekolah/421995C8BC0DB5F817C2', 'SD NEGERI BINCAU 1', 'active', '2025-09-08 21:18:24', '2025-09-08 21:18:24'),
(8, 10, 'https://dapo.kemendikdasmen.go.id/sekolah/25BA38345CA16195A428', 'SD NEGERI MAYANAU', 'active', '2025-09-17 04:53:52', '2025-09-17 04:53:52'),
(10, 16, 'https://dapo.kemendikdasmen.go.id/sekolah/56F4A1E7B2334FAF1877', 'SMAN 1 TEBING TINGGI', 'active', '2025-09-17 04:55:36', '2025-09-17 04:55:36'),
(15, 15, 'https://dapo.kemendikdasmen.go.id/sekolah/75D8EC99E1F8F594CD4C', 'SMP NEGERI 3 AWAYAN', 'active', '2025-09-17 05:30:30', '2025-09-17 05:30:30'),
(16, 14, 'https://dapo.kemendikdasmen.go.id/sekolah/C7273E9E04B44D120B3C', 'SMP NEGERI 2 AWAYAN', 'active', '2025-09-17 05:40:04', '2025-09-17 05:40:04'),
(17, 8, 'https://dapo.kemendikdasmen.go.id/sekolah/908C0A6995377276C02D', 'SD NEGERI KAMBIYAIN', 'active', '2025-09-17 05:45:19', '2025-09-17 05:45:19'),
(18, 4, 'https://dapo.kemendikdasmen.go.id/sekolah/112C63F536CFEF4D2EA5', 'SD NEGERI AJUNG', 'active', '2025-09-17 05:51:47', '2025-09-17 05:51:47'),
(19, 9, 'https://dapo.kemendikdasmen.go.id/sekolah/7BB894B73F64BE71135B', 'SD NEGERI LANGKAP', 'active', '2025-09-17 05:59:30', '2025-09-17 05:59:30'),
(20, 1, 'https://dapo.kemendikdasmen.go.id/sekolah/8447C7287D72135D0651', 'SD KECIL RANTAU PAKU', 'active', '2025-09-17 06:01:29', '2025-09-17 06:01:29'),
(21, 2, 'https://dapo.kemendikdasmen.go.id/sekolah/5572548939C4441001F1', 'SD KECIL RARANUM', 'active', '2025-09-17 06:02:12', '2025-09-17 06:02:12'),
(22, 3, 'https://dapo.kemendikdasmen.go.id/sekolah/2BF767F99AC1A8BFF1F6', 'SD KECIL SIMPANG BUMBUAN', 'active', '2025-09-17 06:08:54', '2025-09-17 06:08:54'),
(23, 5, 'https://dapo.kemendikdasmen.go.id/sekolah/16FA4D6628595247E612', 'SD NEGERI AUH', 'active', '2025-09-17 06:12:05', '2025-09-17 06:12:05'),
(24, 6, 'https://dapo.kemendikdasmen.go.id/sekolah/A58FFD640F166BD2799E', 'SD NEGERI DAYAK PITAP', 'active', '2025-09-17 06:14:00', '2025-09-17 06:14:00'),
(25, 56, 'https://dapo.kemendikdasmen.go.id/sekolah/E4F3E1DB57F17D37641C', 'SD NEGERI MUNDAR', 'active', '2025-09-17 07:24:17', '2025-09-17 07:24:17'),
(26, 44, 'https://dapo.kemendikdasmen.go.id/sekolah/60FBD269D09C6864AD88', 'SD NEGERI JUNGKAL', 'active', '2025-09-17 08:59:11', '2025-09-17 08:59:11'),
(27, 34, 'https://dapo.kemendikdasmen.go.id/sekolah/0E457074366304EA404B', 'SD ISLAM AL ISTIQOMAH', 'active', '2025-09-17 09:17:45', '2025-09-17 09:17:45');

-- --------------------------------------------------------

--
-- Table structure for table `sekolah_identitas`
--

CREATE TABLE `sekolah_identitas` (
  `npsn` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_sekolah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenjang_pendidikan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_sekolah` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat_jalan` text COLLATE utf8mb4_unicode_ci,
  `rt` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rw` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_pos` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kelurahan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lintang` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bujur` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_kecamatan_fk` int(11) DEFAULT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sekolah_identitas`
--

INSERT INTO `sekolah_identitas` (`npsn`, `nama_sekolah`, `jenjang_pendidikan`, `status_sekolah`, `alamat_jalan`, `rt`, `rw`, `kode_pos`, `kelurahan`, `lintang`, `bujur`, `id_kecamatan_fk`, `last_updated`) VALUES
('30303810', 'SD NEGERI MUNDAR', 'SD', 'Negeri', 'Desa Mundar', '4', '0.0', '71661', 'Mundar', NULL, NULL, 6, '2025-09-17 07:30:29'),
('30303881', 'SMP NEGERI 2 AWAYAN', 'SMP', 'Negeri', 'Desa Sungsum', '1', '0.0', '71664', 'SUNGSUM', NULL, NULL, 7, '2025-09-17 09:12:42'),
('30305493', 'SD KECIL RARANUM', 'SD', 'Negeri', 'Raranum Desa Langkap', '3', '0.0', '71664', 'Langkap', NULL, NULL, 7, '2025-09-17 08:58:40'),
('69947887', 'SD KECIL SIMPANG BUMBUAN', 'SD', 'Negeri', 'Jl. Simpang Bumbuan', '2', '2.0', '71666', 'Mayanau', NULL, NULL, 7, '2025-09-17 10:02:27'),
('70003433', 'SD ISLAM AL ISTIQOMAH', 'SD', 'Swasta', 'KELURAHAN PARINGIN KOTA', '6', '3.0', '71612', 'Paringin Kota', NULL, NULL, 8, '2025-09-17 09:23:39');

-- --------------------------------------------------------

--
-- Table structure for table `sekolah_kontak`
--

CREATE TABLE `sekolah_kontak` (
  `id` int(11) NOT NULL,
  `npsn_fk` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_telepon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_fax` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sekolah_kontak`
--

INSERT INTO `sekolah_kontak` (`id`, `npsn_fk`, `nomor_telepon`, `nomor_fax`, `email`, `website`) VALUES
(81, '30303810', '2147483647', 'None', 'sdnmundar.lampihong@gmail.com', 'http://'),
(82, '30305493', '2147483647', 'None', 'sdk_raranum@yahoo.co.id', 'http://'),
(83, '30303881', '2147483647', '0', 'smpn2awy@gmail.com', 'http://'),
(84, '70003433', '2147483647', 'None', 'sdialistiqamah1@gmail.com', 'http://'),
(85, '69947887', 'None', 'None', 'None', 'http://');

-- --------------------------------------------------------

--
-- Table structure for table `sekolah_lainnya`
--

CREATE TABLE `sekolah_lainnya` (
  `id` int(11) NOT NULL,
  `npsn_fk` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kepala_sekolah` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operator_pendataan` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `akreditasi` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kurikulum` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sekolah_lainnya`
--

INSERT INTO `sekolah_lainnya` (`id`, `npsn_fk`, `kepala_sekolah`, `operator_pendataan`, `akreditasi`, `kurikulum`) VALUES
(81, '30303810', 'Darmiah', 'None', 'B', 'Kurikulum Merdeka'),
(82, '30305493', 'Zainuddin', 'None', 'C', 'Kurikulum Merdeka'),
(83, '30303881', 'Purnamawati', 'Estiqomah', 'Tidak diisi', 'Kurikulum Merdeka'),
(84, '70003433', 'FAUZIE RAHMAN', 'None', 'Tidak diisi', 'Kurikulum Merdeka'),
(85, '69947887', 'Rohanah', 'None', 'C', 'Kurikulum Merdeka');

-- --------------------------------------------------------

--
-- Table structure for table `sekolah_pelengkap`
--

CREATE TABLE `sekolah_pelengkap` (
  `id` int(11) NOT NULL,
  `npsn_fk` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sk_pendirian` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_sk_pendirian` date DEFAULT NULL,
  `status_kepemilikan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sk_izin_operasional` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_sk_izin_operasional` date DEFAULT NULL,
  `kebutuhan_khusus_dilayani` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_rekening` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_bank` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cabang_kcp_unit` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rekening_atas_nama` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mbs` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `luas_tanah_milik_m2` int(11) DEFAULT NULL,
  `luas_tanah_bukan_milik_m2` int(11) DEFAULT NULL,
  `nama_wajib_pajak` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `npwp` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sekolah_pelengkap`
--

INSERT INTO `sekolah_pelengkap` (`id`, `npsn_fk`, `sk_pendirian`, `tgl_sk_pendirian`, `status_kepemilikan`, `sk_izin_operasional`, `tgl_sk_izin_operasional`, `kebutuhan_khusus_dilayani`, `nomor_rekening`, `nama_bank`, `cabang_kcp_unit`, `rekening_atas_nama`, `mbs`, `luas_tanah_milik_m2`, `luas_tanah_bukan_milik_m2`, `nama_wajib_pajak`, `npwp`) VALUES
(81, '30303810', '421/175/DISDIK/2018', '1978-07-10', 'Pemerintah Daerah', '421/175/DISDIK/2018', '1978-10-07', 'Tidak ada', '2147483647', 'BPD', 'Paringin', 'Bendahara BOS SDN Mundar', 'Ya', 3, 0, 'BENDAHARA SDN MUNDAR', '001220631735000'),
(82, '30305493', '421/1202/bk.sarpras/disdikbud/2022', '2010-01-01', 'Pemerintah Daerah', '421/1202/bk.sarpras/disdikbud/2022', '2010-01-01', 'Tidak ada', '2147483647', 'BPD KALIMANTAN SE...', 'BPD KALIMANTAN SELATAN CABANG PARINGIN...', 'BENDAHARABOSSDKECILRARANUM...', 'Tidak', 3, 0, 'SDK Raranum', '001220631735000'),
(83, '30303881', '0313/0/1993', '1993-08-23', 'Pemerintah Daerah', '03/13/0/1993', '1993-08-23', 'Tidak ada', '2147483647', 'BPD KALIMANTAN SE...', 'BPD KALIMANTAN SELATAN CABANG PARINGIN...', 'BENDAHARABOSSMPN2AWAYAN...', 'Ya', 3, 0, 'BEND.SMPN 2 AWAYAN', '001220631735000'),
(84, '70003433', '420/577/BPSD/Disdik/2020', '2020-07-21', 'Yayasan', '800/081/SKT/Disdik/2020', '2020-07-21', 'Tidak ada', '1234', 'nama_bank', 'cabang', 'rek_atas_nama', 'Tidak', 1, 250000, 'Yayasan Al Istiqamah Balangan', '534945878735000'),
(85, '69947887', '188.45/644/Kum TAHUN 2014', '2014-12-31', 'Pemerintah Daerah', '800/008.1/SKT/Disdik/2015', '2015-01-05', 'Tidak ada', '2147483647', 'BPD KALIMANTAN SE...', 'BPD KALIMANTAN SELATAN CABANG PARINGIN...', 'BENDAHARABOSSDKSIMPANGBUMBUAN...', 'Tidak', 3, 0, 'None', '001220631735000');

-- --------------------------------------------------------

--
-- Table structure for table `sekolah_scrape`
--

CREATE TABLE `sekolah_scrape` (
  `id` int(11) NOT NULL,
  `npsn` varchar(255) NOT NULL,
  `nama_sekolah` varchar(255) NOT NULL,
  `url` varchar(500) NOT NULL,
  `jenjang` varchar(10) NOT NULL,
  `kecamatan_scrape_id` int(11) NOT NULL,
  `status` enum('active','inactive','processed') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabel untuk menyimpan data sekolah hasil scraping';

--
-- Dumping data for table `sekolah_scrape`
--

INSERT INTO `sekolah_scrape` (`id`, `npsn`, `nama_sekolah`, `url`, `jenjang`, `kecamatan_scrape_id`, `status`, `created_at`, `updated_at`) VALUES
(1, '30311425', 'SD KECIL RANTAU PAKU', 'https://dapo.kemendikdasmen.go.id/sekolah/8447C7287D72135D0651', 'sd', 164, 'processed', '2025-09-16 23:12:53', '2025-09-17 06:01:29'),
(2, '30305493', 'SD KECIL RARANUM', 'https://dapo.kemendikdasmen.go.id/sekolah/5572548939C4441001F1', 'sd', 164, 'processed', '2025-09-16 23:12:53', '2025-09-17 06:02:12'),
(3, '69947887', 'SD KECIL SIMPANG BUMBUAN', 'https://dapo.kemendikdasmen.go.id/sekolah/2BF767F99AC1A8BFF1F6', 'sd', 164, 'processed', '2025-09-16 23:12:53', '2025-09-17 06:08:54'),
(4, '30304018', 'SD NEGERI AJUNG', 'https://dapo.kemendikdasmen.go.id/sekolah/112C63F536CFEF4D2EA5', 'sd', 164, 'processed', '2025-09-16 23:12:53', '2025-09-17 05:51:47'),
(5, '30304029', 'SD NEGERI AUH', 'https://dapo.kemendikdasmen.go.id/sekolah/16FA4D6628595247E612', 'sd', 164, 'active', '2025-09-16 23:12:53', '2025-09-17 06:12:05'),
(6, '30303997', 'SD NEGERI DAYAK PITAP', 'https://dapo.kemendikdasmen.go.id/sekolah/A58FFD640F166BD2799E', 'sd', 164, 'active', '2025-09-16 23:12:53', '2025-09-17 06:14:00'),
(7, '30304003', 'SD NEGERI JUUH', 'https://dapo.kemendikdasmen.go.id/sekolah/A1C40406FE7858B9708D', 'sd', 164, 'active', '2025-09-16 23:12:53', '2025-09-16 23:12:53'),
(8, '30311585', 'SD NEGERI KAMBIYAIN', 'https://dapo.kemendikdasmen.go.id/sekolah/908C0A6995377276C02D', 'sd', 164, 'processed', '2025-09-16 23:12:53', '2025-09-17 05:45:19'),
(9, '30303854', 'SD NEGERI LANGKAP', 'https://dapo.kemendikdasmen.go.id/sekolah/7BB894B73F64BE71135B', 'sd', 164, 'processed', '2025-09-16 23:12:54', '2025-09-17 05:59:30'),
(10, '30303803', 'SD NEGERI MAYANAU', 'https://dapo.kemendikdasmen.go.id/sekolah/25BA38345CA16195A428', 'sd', 164, 'active', '2025-09-16 23:12:54', '2025-09-17 05:02:25'),
(11, '30311593', 'SD NEGERI PANIKIN', 'https://dapo.kemendikdasmen.go.id/sekolah/9901906AA7EDBFD07FA2', 'sd', 164, 'active', '2025-09-16 23:12:54', '2025-09-16 23:12:54'),
(12, '30311587', 'SD NEGERI SUNGSUM', 'https://dapo.kemendikdasmen.go.id/sekolah/6FB1444FF628C333A6A3', 'sd', 164, 'active', '2025-09-16 23:12:54', '2025-09-16 23:12:54'),
(13, '30303915', 'SD NEGERI TEBING TINGGI', 'https://dapo.kemendikdasmen.go.id/sekolah/CF81422EB945336D043A', 'sd', 164, 'active', '2025-09-16 23:12:54', '2025-09-17 05:12:24'),
(14, '30303881', 'SMP NEGERI 2 AWAYAN', 'https://dapo.kemendikdasmen.go.id/sekolah/C7273E9E04B44D120B3C', 'smp', 164, 'processed', '2025-09-16 23:13:03', '2025-09-17 05:40:04'),
(15, '30304972', 'SMP NEGERI 3 AWAYAN', 'https://dapo.kemendikdasmen.go.id/sekolah/75D8EC99E1F8F594CD4C', 'smp', 164, 'processed', '2025-09-16 23:13:03', '2025-09-17 05:30:30'),
(16, '69786628', 'SMAN 1 TEBING TINGGI', 'https://dapo.kemendikdasmen.go.id/sekolah/56F4A1E7B2334FAF1877', 'sma', 164, 'processed', '2025-09-16 23:13:12', '2025-09-17 05:40:44'),
(17, '30304024', 'SD NEGERI BALIDA', 'https://dapo.kemendikdasmen.go.id/sekolah/DE08636FB1B6F1C06E42', 'sd', 162, 'active', '2025-09-17 06:18:40', '2025-09-17 06:18:40'),
(18, '30304034', 'SD NEGERI DAHAI', 'https://dapo.kemendikdasmen.go.id/sekolah/1D3B7279EF55FA67CCCE', 'sd', 162, 'active', '2025-09-17 06:18:40', '2025-09-17 06:18:40'),
(19, '30304017', 'SD NEGERI DANAU BANTA', 'https://dapo.kemendikdasmen.go.id/sekolah/7EA5426B2BF5B820F351', 'sd', 162, 'active', '2025-09-17 06:18:40', '2025-09-17 06:18:40'),
(20, '30303990', 'SD NEGERI GUNUNG PANDAU', 'https://dapo.kemendikdasmen.go.id/sekolah/756870AF4997A4DC5A92', 'sd', 162, 'active', '2025-09-17 06:18:40', '2025-09-17 06:18:40'),
(21, '30304007', 'SD NEGERI HUJAN AMAS 1', 'https://dapo.kemendikdasmen.go.id/sekolah/A6A418164F541450308D', 'sd', 162, 'active', '2025-09-17 06:18:40', '2025-09-17 06:18:40'),
(22, '30304006', 'SD NEGERI HUJAN AMAS 2', 'https://dapo.kemendikdasmen.go.id/sekolah/C988061CC68E60348D74', 'sd', 162, 'active', '2025-09-17 06:18:40', '2025-09-17 06:18:40'),
(23, '30303840', 'SD NEGERI KALAHIANG', 'https://dapo.kemendikdasmen.go.id/sekolah/1954933D917CA7797928', 'sd', 162, 'active', '2025-09-17 06:18:41', '2025-09-17 06:18:41'),
(24, '30311592', 'SD NEGERI LASUNG BATU 1', 'https://dapo.kemendikdasmen.go.id/sekolah/170EFC641BCB8EFE3F43', 'sd', 162, 'active', '2025-09-17 06:18:41', '2025-09-17 06:18:41'),
(25, '30303852', 'SD NEGERI LASUNG BATU 2', 'https://dapo.kemendikdasmen.go.id/sekolah/15ECAF41EFF0EDAC16CD', 'sd', 162, 'active', '2025-09-17 06:18:41', '2025-09-17 06:18:41'),
(26, '30303847', 'SD NEGERI LOK BATUNG', 'https://dapo.kemendikdasmen.go.id/sekolah/C50430105AE6A4A5C5D8', 'sd', 162, 'active', '2025-09-17 06:18:41', '2025-09-17 06:18:41'),
(27, '30303845', 'SD NEGERI MALIHU', 'https://dapo.kemendikdasmen.go.id/sekolah/D29AC886601D028A4F46', 'sd', 162, 'active', '2025-09-17 06:18:41', '2025-09-17 06:18:41'),
(28, '30303827', 'SD NEGERI MANGKAYAHU', 'https://dapo.kemendikdasmen.go.id/sekolah/15BBD9ADCB943D642891', 'sd', 162, 'active', '2025-09-17 06:18:41', '2025-09-17 06:18:41'),
(29, '30303822', 'SD NEGERI MURUNG ILUNG', 'https://dapo.kemendikdasmen.go.id/sekolah/727C57CCEE157BFCBF3D', 'sd', 162, 'active', '2025-09-17 06:18:41', '2025-09-17 06:18:41'),
(30, '30311590', 'SD NEGERI PARINGIN 1', 'https://dapo.kemendikdasmen.go.id/sekolah/FDFD0651CE5443145647', 'sd', 162, 'active', '2025-09-17 06:18:41', '2025-09-17 06:18:41'),
(31, '30311630', 'SD NEGERI PARINGIN 2', 'https://dapo.kemendikdasmen.go.id/sekolah/FCFE1D57BC3401C34D22', 'sd', 162, 'active', '2025-09-17 06:18:42', '2025-09-17 06:18:42'),
(32, '30303816', 'SD NEGERI PARINGIN TIMUR', 'https://dapo.kemendikdasmen.go.id/sekolah/6DC04A6EBEB2A830BED2', 'sd', 162, 'active', '2025-09-17 06:18:42', '2025-09-17 06:18:42'),
(33, '30303892', 'SD NEGERI SUNGAI KETAPI', 'https://dapo.kemendikdasmen.go.id/sekolah/BE001E93C997A6D8A2EC', 'sd', 162, 'active', '2025-09-17 06:18:42', '2025-09-17 06:18:42'),
(34, '70003433', 'SD ISLAM AL ISTIQOMAH', 'https://dapo.kemendikdasmen.go.id/sekolah/0E457074366304EA404B', 'sd', 162, 'active', '2025-09-17 06:18:42', '2025-09-17 09:17:45'),
(35, '30311638', 'SMP NEGERI 3 PARINGIN', 'https://dapo.kemendikdasmen.go.id/sekolah/AEB4EA9732C2C1DB71CE', 'smp', 162, 'active', '2025-09-17 06:19:04', '2025-09-17 06:19:04'),
(36, '69756868', 'SMP NEGERI 5 PARINGIN', 'https://dapo.kemendikdasmen.go.id/sekolah/69159EB16DDE9C89A2B5', 'smp', 162, 'active', '2025-09-17 06:19:05', '2025-09-17 06:19:05'),
(37, '30311478', 'SMPS AL QURAN IKHWANUL MUSLIMIN', 'https://dapo.kemendikdasmen.go.id/sekolah/A862041AA01A7B952EB7', 'smp', 162, 'active', '2025-09-17 06:19:05', '2025-09-17 06:19:05'),
(38, '30305424', 'SMAS AL - QUR AN IKHWANUL MUSLIMIN PARINGIN', 'https://dapo.kemendikdasmen.go.id/sekolah/501E7EDBBF66EC5C2306', 'sma', 162, 'active', '2025-09-17 06:19:14', '2025-09-17 06:19:14'),
(39, '30312933', 'SMKPP NEGERI PARINGIN', 'https://dapo.kemendikdasmen.go.id/sekolah/100AEF2453FF9A7EA16F', 'smk', 162, 'active', '2025-09-17 06:19:34', '2025-09-17 06:19:34'),
(40, '30304043', 'SD NEGERI BATU MERAH 1', 'https://dapo.kemendikdasmen.go.id/sekolah/D861D31505CF6BBCCF94', 'sd', 158, 'active', '2025-09-17 06:58:44', '2025-09-17 06:58:44'),
(41, '30304044', 'SD NEGERI BATU MERAH 3', 'https://dapo.kemendikdasmen.go.id/sekolah/E5C98BC97E7F82D8AE56', 'sd', 158, 'active', '2025-09-17 06:58:44', '2025-09-17 06:58:44'),
(42, '30304008', 'SD NEGERI HILIR PASAR', 'https://dapo.kemendikdasmen.go.id/sekolah/084E7EEDB16807586E9E', 'sd', 158, 'active', '2025-09-17 06:58:44', '2025-09-17 06:58:44'),
(43, '30304002', 'SD NEGERI JIMAMUN', 'https://dapo.kemendikdasmen.go.id/sekolah/43CB809D7BB9AE095C0F', 'sd', 158, 'active', '2025-09-17 06:58:44', '2025-09-17 06:58:44'),
(44, '30303921', 'SD NEGERI JUNGKAL', 'https://dapo.kemendikdasmen.go.id/sekolah/60FBD269D09C6864AD88', 'sd', 158, 'active', '2025-09-17 06:58:44', '2025-09-17 09:17:33'),
(45, '30303838', 'SD NEGERI KANDANG JAYA', 'https://dapo.kemendikdasmen.go.id/sekolah/7547F45EA93CDFCBC6A0', 'sd', 158, 'active', '2025-09-17 06:58:44', '2025-09-17 06:58:44'),
(46, '30303834', 'SD NEGERI KUPANG', 'https://dapo.kemendikdasmen.go.id/sekolah/3CF3020E2E6F39980366', 'sd', 158, 'active', '2025-09-17 06:58:44', '2025-09-17 06:58:44'),
(47, '30303833', 'SD NEGERI KUSAMBI HILIR', 'https://dapo.kemendikdasmen.go.id/sekolah/9F7A2E7E8A2E0B32CDDD', 'sd', 158, 'active', '2025-09-17 06:58:45', '2025-09-17 06:58:45'),
(48, '30303832', 'SD NEGERI KUSAMBI HULU 1', 'https://dapo.kemendikdasmen.go.id/sekolah/36E65FC7D2EDF72C65F4', 'sd', 158, 'active', '2025-09-17 06:58:45', '2025-09-17 06:58:45'),
(49, '30303831', 'SD NEGERI KUSAMBI HULU 2', 'https://dapo.kemendikdasmen.go.id/sekolah/11BF529FDCE6817DF5FC', 'sd', 158, 'active', '2025-09-17 06:58:45', '2025-09-17 06:58:45'),
(50, '30303830', 'SD NEGERI LAJAR', 'https://dapo.kemendikdasmen.go.id/sekolah/F979E9EB14E2E8DF5F5E', 'sd', 158, 'active', '2025-09-17 06:58:45', '2025-09-17 06:58:45'),
(51, '30303842', 'SD NEGERI LAMPIHONG KANAN', 'https://dapo.kemendikdasmen.go.id/sekolah/E7EBB60B80AA32DFC025', 'sd', 158, 'active', '2025-09-17 06:58:45', '2025-09-17 06:58:45'),
(52, '30303855', 'SD NEGERI LAMPIHONG KIRI', 'https://dapo.kemendikdasmen.go.id/sekolah/812176D2C7863D429584', 'sd', 158, 'active', '2025-09-17 06:58:45', '2025-09-17 06:58:45'),
(53, '30303849', 'SD NEGERI LOK HAMAWANG', 'https://dapo.kemendikdasmen.go.id/sekolah/B30EADA101717309D886', 'sd', 158, 'active', '2025-09-17 06:58:45', '2025-09-17 06:58:45'),
(54, '30303848', 'SD NEGERI LOK PANGINANGAN', 'https://dapo.kemendikdasmen.go.id/sekolah/3D73CB8A4EB76BBB5C95', 'sd', 158, 'active', '2025-09-17 06:58:45', '2025-09-17 06:58:45'),
(55, '30303804', 'SD NEGERI MATANG HANAU', 'https://dapo.kemendikdasmen.go.id/sekolah/BE926900BAC80EB5963F', 'sd', 158, 'active', '2025-09-17 06:58:45', '2025-09-17 06:58:45'),
(56, '30303810', 'SD NEGERI MUNDAR', 'https://dapo.kemendikdasmen.go.id/sekolah/E4F3E1DB57F17D37641C', 'sd', 158, 'active', '2025-09-17 06:58:45', '2025-09-17 07:24:17'),
(57, '30303819', 'SD NEGERI PANAITAN', 'https://dapo.kemendikdasmen.go.id/sekolah/47B99B964A681D070856', 'sd', 158, 'active', '2025-09-17 06:58:45', '2025-09-17 06:58:45'),
(58, '30303813', 'SD NEGERI PIMPING', 'https://dapo.kemendikdasmen.go.id/sekolah/BADACDE02623F6D4CD38', 'sd', 158, 'active', '2025-09-17 06:58:45', '2025-09-17 06:58:45'),
(59, '30313825', 'SD NEGERI PUPUYUAN', 'https://dapo.kemendikdasmen.go.id/sekolah/DAB05AC1B37ED74F13D5', 'sd', 158, 'active', '2025-09-17 06:58:45', '2025-09-17 06:58:45'),
(60, '30303899', 'SD NEGERI SIMPANG TIGA', 'https://dapo.kemendikdasmen.go.id/sekolah/C6C179668FB2CDB46CAA', 'sd', 158, 'active', '2025-09-17 06:58:46', '2025-09-17 06:58:46'),
(61, '30303919', 'SD NEGERI SUNGAI TABUK', 'https://dapo.kemendikdasmen.go.id/sekolah/54DC93C31EC835AA8E8E', 'sd', 158, 'active', '2025-09-17 06:58:46', '2025-09-17 06:58:46'),
(62, '30303912', 'SD NEGERI TAMPANG', 'https://dapo.kemendikdasmen.go.id/sekolah/7FD8761BC24C378CBF97', 'sd', 158, 'active', '2025-09-17 06:58:46', '2025-09-17 06:58:46'),
(63, '30303910', 'SD NEGERI TANAH HABANG KANAN 1', 'https://dapo.kemendikdasmen.go.id/sekolah/1B96EAD337E765258A44', 'sd', 158, 'active', '2025-09-17 06:58:46', '2025-09-17 06:58:46'),
(64, '30303911', 'SD NEGERI TANAH HABANG KANAN 2', 'https://dapo.kemendikdasmen.go.id/sekolah/AB64CF27B48C75EB6FA4', 'sd', 158, 'active', '2025-09-17 06:58:46', '2025-09-17 06:58:46'),
(65, '30303909', 'SD NEGERI TANAH HABANG KIRI', 'https://dapo.kemendikdasmen.go.id/sekolah/0330774BCE743BAF3E95', 'sd', 158, 'active', '2025-09-17 06:58:46', '2025-09-17 06:58:46'),
(66, '30303883', 'SMP NEGERI 1 LAMPIHONG', 'https://dapo.kemendikdasmen.go.id/sekolah/5D288D803FB298260674', 'smp', 158, 'active', '2025-09-17 06:59:09', '2025-09-17 06:59:09'),
(67, '30304971', 'SMP NEGERI 2 LAMPIHONG', 'https://dapo.kemendikdasmen.go.id/sekolah/117784E051C6FC30B461', 'smp', 158, 'active', '2025-09-17 06:59:10', '2025-09-17 06:59:10'),
(68, '70055905', 'SMP Darul Abrar Al Hasaniyyah', 'https://dapo.kemendikdasmen.go.id/sekolah/31E49CD97D9D4D197375', 'smp', 158, 'active', '2025-09-17 06:59:10', '2025-09-17 06:59:10'),
(69, '69851426', 'SMA NEGERI 1 LAMPIHONG', 'https://dapo.kemendikdasmen.go.id/sekolah/07448363EC615B2D6AE1', 'sma', 158, 'active', '2025-09-17 06:59:19', '2025-09-17 06:59:19'),
(70, '30303885', 'SMP NEGERI 1 HALONG', 'https://dapo.kemendikdasmen.go.id/sekolah/EED785DA17C0BFAFC7C4', 'smp', 157, 'active', '2025-09-17 07:15:58', '2025-09-17 07:15:58'),
(71, '30303879', 'SMP NEGERI 2 HALONG', 'https://dapo.kemendikdasmen.go.id/sekolah/66548072313FB4DE127F', 'smp', 157, 'active', '2025-09-17 07:15:59', '2025-09-17 07:15:59'),
(72, '30303877', 'SMP NEGERI 3 HALONG', 'https://dapo.kemendikdasmen.go.id/sekolah/088A859DB16024099336', 'smp', 157, 'active', '2025-09-17 07:15:59', '2025-09-17 07:15:59'),
(73, '30304974', 'SMP NEGERI 4 HALONG', 'https://dapo.kemendikdasmen.go.id/sekolah/61E9D74BD2E9BCBDC22A', 'smp', 157, 'active', '2025-09-17 07:15:59', '2025-09-17 07:15:59'),
(74, '30311641', 'SMP NEGERI 5 HALONG', 'https://dapo.kemendikdasmen.go.id/sekolah/9DD25F305FDDA0633E48', 'smp', 157, 'active', '2025-09-17 07:15:59', '2025-09-17 07:15:59'),
(75, '69900718', 'SMP NEGERI 6 HALONG SATU ATAP', 'https://dapo.kemendikdasmen.go.id/sekolah/07696553358D086CFB20', 'smp', 157, 'active', '2025-09-17 07:15:59', '2025-09-17 07:15:59'),
(76, '69984786', 'SMP SATU ATAP LIBARU SUNGKAI', 'https://dapo.kemendikdasmen.go.id/sekolah/6F04B999F9492ADD9776', 'smp', 157, 'active', '2025-09-17 07:15:59', '2025-09-17 07:15:59'),
(77, '30304976', 'SMAN 1 HALONG', 'https://dapo.kemendikdasmen.go.id/sekolah/916442C496404828BC0F', 'sma', 157, 'active', '2025-09-17 07:16:08', '2025-09-17 07:16:08'),
(78, '69851427', 'SMAN 2 HALONG', 'https://dapo.kemendikdasmen.go.id/sekolah/8FDC047DB16AFD7DD9A3', 'sma', 157, 'active', '2025-09-17 07:16:08', '2025-09-17 07:16:08');

-- --------------------------------------------------------

--
-- Table structure for table `url_induk_scrape`
--

CREATE TABLE `url_induk_scrape` (
  `id` int(11) NOT NULL,
  `url` varchar(500) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','processed') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabel untuk menyimpan URL induk yang diinput melalui website';

--
-- Dumping data for table `url_induk_scrape`
--

INSERT INTO `url_induk_scrape` (`id`, `url`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'https://dapo.kemendikdasmen.go.id/sp/1/150000', 'URL Induk Dapodik - 2025-09-15 23:39:26', 'active', '2025-09-15 23:39:26', '2025-09-15 23:39:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `import_log`
--
ALTER TABLE `import_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `url_induk_id` (`url_induk_id`),
  ADD KEY `idx_process_type_status` (`process_type`,`status`),
  ADD KEY `idx_started_at` (`started_at`),
  ADD KEY `idx_import_log_url_process` (`url_induk_id`,`process_type`);

--
-- Indexes for table `kabupaten_kota`
--
ALTER TABLE `kabupaten_kota`
  ADD PRIMARY KEY (`id_kabupaten`),
  ADD KEY `id_provinsi_fk` (`id_provinsi_fk`);

--
-- Indexes for table `kabupaten_scrape`
--
ALTER TABLE `kabupaten_scrape`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_kabupaten_unique` (`kode_kabupaten`,`url_induk_id`),
  ADD KEY `url_induk_id` (`url_induk_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_kabupaten_url_induk` (`url_induk_id`,`status`);

--
-- Indexes for table `kecamatan`
--
ALTER TABLE `kecamatan`
  ADD PRIMARY KEY (`id_kecamatan`),
  ADD KEY `id_kabupaten_fk` (`id_kabupaten_fk`);

--
-- Indexes for table `kecamatan_scrape`
--
ALTER TABLE `kecamatan_scrape`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_kecamatan_unique` (`kode_kecamatan`,`kabupaten_scrape_id`),
  ADD KEY `kabupaten_scrape_id` (`kabupaten_scrape_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_kecamatan_kabupaten` (`kabupaten_scrape_id`,`status`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `negara`
--
ALTER TABLE `negara`
  ADD PRIMARY KEY (`id_negara`),
  ADD UNIQUE KEY `nama_negara` (`nama_negara`);

--
-- Indexes for table `provinsi`
--
ALTER TABLE `provinsi`
  ADD PRIMARY KEY (`id_provinsi`),
  ADD KEY `id_negara_fk` (`id_negara_fk`);

--
-- Indexes for table `rekap_ptk_pd`
--
ALTER TABLE `rekap_ptk_pd`
  ADD PRIMARY KEY (`id`),
  ADD KEY `npsn_fk` (`npsn_fk`);

--
-- Indexes for table `rekap_rombel`
--
ALTER TABLE `rekap_rombel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `npsn_fk` (`npsn_fk`);

--
-- Indexes for table `rekap_sarpras`
--
ALTER TABLE `rekap_sarpras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `npsn_fk` (`npsn_fk`);

--
-- Indexes for table `scraping_logs`
--
ALTER TABLE `scraping_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scraping_urls`
--
ALTER TABLE `scraping_urls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sekolah_scrape_id` (`sekolah_scrape_id`);

--
-- Indexes for table `sekolah_identitas`
--
ALTER TABLE `sekolah_identitas`
  ADD PRIMARY KEY (`npsn`),
  ADD KEY `id_kecamatan_fk` (`id_kecamatan_fk`);

--
-- Indexes for table `sekolah_kontak`
--
ALTER TABLE `sekolah_kontak`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `npsn_fk` (`npsn_fk`);

--
-- Indexes for table `sekolah_lainnya`
--
ALTER TABLE `sekolah_lainnya`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `npsn_fk` (`npsn_fk`);

--
-- Indexes for table `sekolah_pelengkap`
--
ALTER TABLE `sekolah_pelengkap`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `npsn_fk` (`npsn_fk`);

--
-- Indexes for table `sekolah_scrape`
--
ALTER TABLE `sekolah_scrape`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `npsn_unique` (`npsn`,`kecamatan_scrape_id`),
  ADD KEY `kecamatan_scrape_id` (`kecamatan_scrape_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_jenjang` (`jenjang`),
  ADD KEY `idx_sekolah_kecamatan` (`kecamatan_scrape_id`,`status`);

--
-- Indexes for table `url_induk_scrape`
--
ALTER TABLE `url_induk_scrape`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `url_unique` (`url`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `import_log`
--
ALTER TABLE `import_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `kabupaten_kota`
--
ALTER TABLE `kabupaten_kota`
  MODIFY `id_kabupaten` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kabupaten_scrape`
--
ALTER TABLE `kabupaten_scrape`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `kecamatan`
--
ALTER TABLE `kecamatan`
  MODIFY `id_kecamatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `kecamatan_scrape`
--
ALTER TABLE `kecamatan_scrape`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `negara`
--
ALTER TABLE `negara`
  MODIFY `id_negara` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `provinsi`
--
ALTER TABLE `provinsi`
  MODIFY `id_provinsi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rekap_ptk_pd`
--
ALTER TABLE `rekap_ptk_pd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;

--
-- AUTO_INCREMENT for table `rekap_rombel`
--
ALTER TABLE `rekap_rombel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=377;

--
-- AUTO_INCREMENT for table `rekap_sarpras`
--
ALTER TABLE `rekap_sarpras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=672;

--
-- AUTO_INCREMENT for table `scraping_logs`
--
ALTER TABLE `scraping_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `scraping_urls`
--
ALTER TABLE `scraping_urls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `sekolah_kontak`
--
ALTER TABLE `sekolah_kontak`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `sekolah_lainnya`
--
ALTER TABLE `sekolah_lainnya`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `sekolah_pelengkap`
--
ALTER TABLE `sekolah_pelengkap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `sekolah_scrape`
--
ALTER TABLE `sekolah_scrape`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `url_induk_scrape`
--
ALTER TABLE `url_induk_scrape`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `import_log`
--
ALTER TABLE `import_log`
  ADD CONSTRAINT `import_log_ibfk_1` FOREIGN KEY (`url_induk_id`) REFERENCES `url_induk_scrape` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `kabupaten_kota`
--
ALTER TABLE `kabupaten_kota`
  ADD CONSTRAINT `kabupaten_kota_ibfk_1` FOREIGN KEY (`id_provinsi_fk`) REFERENCES `provinsi` (`id_provinsi`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `kabupaten_scrape`
--
ALTER TABLE `kabupaten_scrape`
  ADD CONSTRAINT `kabupaten_scrape_ibfk_1` FOREIGN KEY (`url_induk_id`) REFERENCES `url_induk_scrape` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kecamatan`
--
ALTER TABLE `kecamatan`
  ADD CONSTRAINT `kecamatan_ibfk_1` FOREIGN KEY (`id_kabupaten_fk`) REFERENCES `kabupaten_kota` (`id_kabupaten`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `kecamatan_scrape`
--
ALTER TABLE `kecamatan_scrape`
  ADD CONSTRAINT `kecamatan_scrape_ibfk_1` FOREIGN KEY (`kabupaten_scrape_id`) REFERENCES `kabupaten_scrape` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `provinsi`
--
ALTER TABLE `provinsi`
  ADD CONSTRAINT `provinsi_ibfk_1` FOREIGN KEY (`id_negara_fk`) REFERENCES `negara` (`id_negara`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `rekap_ptk_pd`
--
ALTER TABLE `rekap_ptk_pd`
  ADD CONSTRAINT `rekap_ptk_pd_ibfk_1` FOREIGN KEY (`npsn_fk`) REFERENCES `sekolah_identitas` (`npsn`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rekap_rombel`
--
ALTER TABLE `rekap_rombel`
  ADD CONSTRAINT `rekap_rombel_ibfk_1` FOREIGN KEY (`npsn_fk`) REFERENCES `sekolah_identitas` (`npsn`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rekap_sarpras`
--
ALTER TABLE `rekap_sarpras`
  ADD CONSTRAINT `rekap_sarpras_ibfk_1` FOREIGN KEY (`npsn_fk`) REFERENCES `sekolah_identitas` (`npsn`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `scraping_urls`
--
ALTER TABLE `scraping_urls`
  ADD CONSTRAINT `scraping_urls_ibfk_1` FOREIGN KEY (`sekolah_scrape_id`) REFERENCES `sekolah_scrape` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sekolah_identitas`
--
ALTER TABLE `sekolah_identitas`
  ADD CONSTRAINT `sekolah_identitas_ibfk_1` FOREIGN KEY (`id_kecamatan_fk`) REFERENCES `kecamatan` (`id_kecamatan`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `sekolah_kontak`
--
ALTER TABLE `sekolah_kontak`
  ADD CONSTRAINT `sekolah_kontak_ibfk_1` FOREIGN KEY (`npsn_fk`) REFERENCES `sekolah_identitas` (`npsn`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sekolah_lainnya`
--
ALTER TABLE `sekolah_lainnya`
  ADD CONSTRAINT `sekolah_lainnya_ibfk_1` FOREIGN KEY (`npsn_fk`) REFERENCES `sekolah_identitas` (`npsn`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sekolah_pelengkap`
--
ALTER TABLE `sekolah_pelengkap`
  ADD CONSTRAINT `sekolah_pelengkap_ibfk_1` FOREIGN KEY (`npsn_fk`) REFERENCES `sekolah_identitas` (`npsn`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sekolah_scrape`
--
ALTER TABLE `sekolah_scrape`
  ADD CONSTRAINT `sekolah_scrape_ibfk_1` FOREIGN KEY (`kecamatan_scrape_id`) REFERENCES `kecamatan_scrape` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
