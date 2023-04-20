-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2023 at 11:44 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `siemas`
--

-- --------------------------------------------------------

--
-- Table structure for table `kabs`
--

CREATE TABLE `kabs` (
  `id_prov` char(2) NOT NULL,
  `id_kab` char(2) NOT NULL,
  `nama_kab` varchar(150) NOT NULL,
  `alias` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kabs`
--

INSERT INTO `kabs` (`id_prov`, `id_kab`, `nama_kab`, `alias`) VALUES
('16', '01', 'OGAN KOMERING ULU', 'OKU'),
('16', '02', 'OGAN KOMERING ILIR', 'OKI'),
('16', '03', 'MUARA ENIM', 'ME'),
('16', '04', 'LAHAT', 'LAHAT'),
('16', '05', 'MUSI RAWAS', 'MURA'),
('16', '06', 'MUSI BANYUASIN', 'MUBA'),
('16', '07', 'BANYU ASIN', 'BA'),
('16', '08', 'OGAN KOMERING ULU SELATAN', 'OKUS'),
('16', '09', 'OGAN KOMERING ULU TIMUR', 'OKUT'),
('16', '10', 'OGAN ILIR', 'OI'),
('16', '11', 'EMPAT LAWANG', '4 LAWANG'),
('16', '12', 'PENUKAL ABAB LEMATANG ILIR', 'PALI'),
('16', '13', 'MUSI RAWAS UTARA', 'MURATARA'),
('16', '71', 'PALEMBANG', 'PLG'),
('16', '72', 'PRABUMULIH', 'PRABU'),
('16', '73', 'PAGAR ALAM', 'PA'),
('16', '74', 'LUBUKLINGGAU', 'LLG');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kabs`
--
ALTER TABLE `kabs`
  ADD PRIMARY KEY (`id_kab`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
