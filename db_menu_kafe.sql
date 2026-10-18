-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2026 at 08:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_menu_kafe`
--

-- --------------------------------------------------------

--
-- Table structure for table `memu`
--

CREATE TABLE `memu` (
  `id` int(11) NOT NULL,
  `kode_menu` varchar(20) NOT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `kategori` enum('Makanan','Minuman','Camilan') NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `status` enum('Tersedia','Habis') NOT NULL DEFAULT 'Tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `memu`
--

INSERT INTO `memu` (`id`, `kode_menu`, `nama_menu`, `kategori`, `harga`, `status`) VALUES
(1, 'MKN001', 'Nasi Goreng', 'Makanan', 18000.00, 'Tersedia'),
(2, 'MNM002', 'Teh Manis', 'Minuman', 5000.00, 'Tersedia'),
(4, 'MKN002', 'NASPAD', 'Makanan', 15000.00, 'Tersedia'),
(5, 'MKN005', 'NASPAD', 'Makanan', 15000.00, 'Tersedia'),
(6, 'MKN008', 'NASPAD', 'Makanan', 15000.00, 'Tersedia');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `memu`
--
ALTER TABLE `memu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_menu` (`kode_menu`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `memu`
--
ALTER TABLE `memu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
