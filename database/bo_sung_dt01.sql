-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 22, 2026 at 12:15 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `travel_booking_dt01`
--

-- --------------------------------------------------------

--
-- Table structure for table `chinh_sach_phu_thu`
--

CREATE TABLE `chinh_sach_phu_thu` (
  `ma_phu_thu` char(7) NOT NULL,
  `ma_san_pham` char(6) NOT NULL,
  `loai_phu_thu` varchar(60) NOT NULL,
  `gia_tri` decimal(12,2) NOT NULL,
  `kieu_tinh` enum('co_dinh','theo_ty_le') NOT NULL DEFAULT 'co_dinh',
  `mo_ta` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chinh_sach_phu_thu`
--

INSERT INTO `chinh_sach_phu_thu` (`ma_phu_thu`, `ma_san_pham`, `loai_phu_thu`, `gia_tri`, `kieu_tinh`, `mo_ta`) VALUES
('PT0001', 'SP0001', 'Phi dich vu', 10.00, 'theo_ty_le', 'Phi dich vu 10% tren gia goc'),
('PT0002', 'SP0002', 'Thue VAT', 8.00, 'theo_ty_le', 'Thue gia tri gia tang'),
('PT0003', 'SP0003', 'Phi huy don', 50000.00, 'co_dinh', 'Phi huy don trong 48 gio');

-- --------------------------------------------------------

--
-- Table structure for table `chi_so_minh_bach_gia`
--

CREATE TABLE `chi_so_minh_bach_gia` (
  `ma_chi_so` char(7) NOT NULL,
  `ma_ncc` char(6) NOT NULL,
  `diem_minh_bach` decimal(5,2) NOT NULL,
  `ngay_tinh` date NOT NULL
) ;

--
-- Dumping data for table `chi_so_minh_bach_gia`
--

INSERT INTO `chi_so_minh_bach_gia` (`ma_chi_so`, `ma_ncc`, `diem_minh_bach`, `ngay_tinh`) VALUES
('CS0001', 'NCC001', 85.50, '2026-09-01'),
('CS0002', 'NCC002', 72.30, '2026-09-01'),
('CS0003', 'NCC003', 91.00, '2026-09-01');

-- --------------------------------------------------------

--
-- Table structure for table `ty_gia_tien_te`
--

CREATE TABLE `ty_gia_tien_te` (
  `ma_ty_gia` char(6) NOT NULL,
  `ma_tien_te` varchar(10) NOT NULL,
  `ty_gia_quy_doi` decimal(12,4) NOT NULL,
  `ngay_cap_nhat` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ty_gia_tien_te`
--

INSERT INTO `ty_gia_tien_te` (`ma_ty_gia`, `ma_tien_te`, `ty_gia_quy_doi`, `ngay_cap_nhat`) VALUES
('TG0001', 'USD', 25400.0000, '2026-09-22'),
('TG0002', 'EUR', 27600.0000, '2026-09-22'),
('TG0003', 'JPY', 172.5000, '2026-09-22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chinh_sach_phu_thu`
--
ALTER TABLE `chinh_sach_phu_thu`
  ADD PRIMARY KEY (`ma_phu_thu`),
  ADD KEY `fk_pt_sp` (`ma_san_pham`);

--
-- Indexes for table `chi_so_minh_bach_gia`
--
ALTER TABLE `chi_so_minh_bach_gia`
  ADD PRIMARY KEY (`ma_chi_so`),
  ADD KEY `fk_cs_ncc` (`ma_ncc`);

--
-- Indexes for table `ty_gia_tien_te`
--
ALTER TABLE `ty_gia_tien_te`
  ADD PRIMARY KEY (`ma_ty_gia`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chinh_sach_phu_thu`
--
ALTER TABLE `chinh_sach_phu_thu`
  ADD CONSTRAINT `fk_pt_sp` FOREIGN KEY (`ma_san_pham`) REFERENCES `san_pham` (`ma_san_pham`) ON DELETE CASCADE;

--
-- Constraints for table `chi_so_minh_bach_gia`
--
ALTER TABLE `chi_so_minh_bach_gia`
  ADD CONSTRAINT `fk_cs_ncc` FOREIGN KEY (`ma_ncc`) REFERENCES `nha_cung_cap` (`ma_ncc`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
