-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2026 at 08:04 AM
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
-- Database: `sistem18_smarthealth`
--

-- --------------------------------------------------------

--
-- Table structure for table `reservasi`
--

CREATE TABLE `reservasi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT 0,
  `nama_pasien` varchar(150) NOT NULL,
  `telpon` varchar(20) NOT NULL,
  `dokter` varchar(150) NOT NULL,
  `layanan` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `jam` varchar(10) NOT NULL,
  `jaminan` varchar(50) DEFAULT 'Umum / Mandiri',
  `catatan` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('menunggu','terkonfirmasi','dibatalkan','selesai') DEFAULT 'menunggu',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservasi`
--

INSERT INTO `reservasi` (`id`, `user_id`, `nama_pasien`, `telpon`, `dokter`, `layanan`, `tanggal`, `jam`, `jaminan`, `catatan`, `foto`, `status`, `created_at`) VALUES
(5, 2, 'Sonic Pratama', '08978421889', 'dr. Agus Subianto', 'Laboratorium', '2026-06-14', '08.00', 'Umum / Mandiri', 'Saya sakit perut', NULL, 'selesai', '2026-06-14 04:59:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama_lengkap` varchar(150) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pengguna') NOT NULL DEFAULT 'pengguna',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Telko Medika', 'Medika', 'telkomedika@gmail.com', '$2y$12$OWUK0M5rG1OwNjWtKOnqL.vwlYmfF4P5eYnt7hRkBAPAc0HVcEpBC', 'admin', '2026-06-13 04:59:11'),
(2, 'Sonic Pratama', 'Sonic', 'sonicpratama021@gmail.com', '$2y$10$NZ.qFkRWJqIHxkoZh7pDAO8Ns2bsnpW6mgC1xNPoL48KRsOSryaou', 'pengguna', '2026-06-13 05:00:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tanggal` (`tanggal`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

-- --------------------------------------------------------
-- Migrasi tambahan: kolom `foto` untuk fitur upload foto reservasi
-- Jalankan query ini jika tabel `reservasi` sudah ada sebelumnya
-- --------------------------------------------------------
-- ALTER TABLE `reservasi` ADD COLUMN `foto` VARCHAR(255) DEFAULT NULL AFTER `catatan`;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
