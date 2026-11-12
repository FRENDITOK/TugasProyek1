-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versi server:                 8.4.3 - MySQL Community Server - GPL
-- OS Server:                    Win64
-- HeidiSQL Versi:               12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Membuang struktur basisdata untuk barbershop_db
CREATE DATABASE IF NOT EXISTS `barbershop_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `barbershop_db`;

-- membuang struktur untuk table barbershop_db.alat
CREATE TABLE IF NOT EXISTS `alat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_alat` varchar(100) NOT NULL,
  `jumlah` int NOT NULL,
  `kondisi` enum('Baik','Perlu Perawatan','Rusak') NOT NULL DEFAULT 'Baik',
  `tanggal_beli` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Membuang data untuk tabel barbershop_db.alat: ~0 rows (lebih kurang)
INSERT INTO `alat` (`id`, `nama_alat`, `jumlah`, `kondisi`, `tanggal_beli`) VALUES
	(1, 'Clipper Wahl Cordless', 3, 'Baik', '2025-01-15'),
	(2, 'Gunting Potong Profesional', 5, 'Baik', '2025-02-10');

-- membuang struktur untuk table barbershop_db.layanan
CREATE TABLE IF NOT EXISTS `layanan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_layanan` varchar(100) NOT NULL,
  `harga` decimal(10,2) NOT NULL DEFAULT (0),
  `deskripsi` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Membuang data untuk tabel barbershop_db.layanan: ~0 rows (lebih kurang)
INSERT INTO `layanan` (`id`, `nama_layanan`, `harga`, `deskripsi`) VALUES
	(1, 'Regular Haircut', 35000.00, 'Potong rambut rapi + keramas + tonic'),
	(2, 'Haircut + Beard Trim', 50000.00, 'Potong rambut + rapikan jenggot & kumis'),
	(3, 'Hair Coloring', 120000.00, 'Pewarnaan rambut standar');

-- membuang struktur untuk table barbershop_db.transaksi
CREATE TABLE IF NOT EXISTS `transaksi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_booking` varchar(20) NOT NULL DEFAULT '',
  `user_id` int DEFAULT NULL,
  `layanan_id` int NOT NULL,
  `tanggal_booking` date NOT NULL,
  `jam_booking` time NOT NULL,
  `total_harga` decimal(10,2) NOT NULL DEFAULT (0),
  `metode_bayar` enum('Tunai','QRIS') NOT NULL DEFAULT 'Tunai',
  `status_bayar` enum('Pending','Lunas') NOT NULL DEFAULT 'Pending',
  `qris_ref` varchar(100) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT (now()) ON UPDATE CURRENT_TIMESTAMP,
  `nama_pelanggan` varchar(100) DEFAULT 'now()',
  `sumber_transaksi` enum('Booking','Manual') NOT NULL DEFAULT 'Booking',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Membuang data untuk tabel barbershop_db.transaksi: ~0 rows (lebih kurang)
INSERT INTO `transaksi` (`id`, `kode_booking`, `user_id`, `layanan_id`, `tanggal_booking`, `jam_booking`, `total_harga`, `metode_bayar`, `status_bayar`, `qris_ref`, `created_at`, `nama_pelanggan`, `sumber_transaksi`) VALUES
	(1, 'BRB-1794463461', 4, 1, '2026-11-13', '13:05:00', 35000.00, 'QRIS', 'Lunas', 'QRIS-SIM-2147', '2026-11-12 06:04:26', 'now()', 'Booking'),
	(2, 'BRB-1794463480', 4, 3, '2026-11-15', '13:05:00', 120000.00, 'QRIS', 'Lunas', 'QRIS-SIM-2299', '2026-11-12 06:04:42', 'now()', 'Booking'),
	(3, 'BRB-1794463552', 4, 1, '2026-11-12', '13:05:00', 35000.00, 'QRIS', 'Lunas', 'QRIS-SIM-3090', '2026-11-12 06:05:55', 'now()', 'Booking'),
	(4, 'BRB-1794463564', 4, 3, '2026-11-14', '13:07:00', 120000.00, 'QRIS', 'Lunas', 'QRIS-SIM-8856', '2026-11-12 06:06:05', 'now()', 'Booking'),
	(5, 'BRB-1794463574', 4, 3, '2026-11-14', '13:07:00', 120000.00, 'QRIS', 'Lunas', 'QRIS-SIM-9349', '2026-11-12 06:07:21', 'now()', 'Booking'),
	(6, 'BRB-1794463650', 4, 3, '2026-11-13', '13:08:00', 120000.00, 'QRIS', 'Lunas', 'QRIS-SIM-1630', '2026-11-12 06:07:34', 'now()', 'Booking'),
	(7, 'BRB-1794463670', 4, 3, '2026-11-14', '13:08:00', 120000.00, 'QRIS', 'Lunas', 'QRIS-SIM-2671', '2026-11-12 06:07:52', 'now()', 'Booking'),
	(8, 'BRB-1794465031', 4, 1, '2026-11-12', '13:30:00', 35000.00, 'QRIS', 'Lunas', '', '2026-11-12 15:52:42', 'now()', 'Booking'),
	(9, 'BRB-1794465361', 4, 3, '2026-11-13', '13:37:00', 1200000.00, 'QRIS', 'Lunas', 'QRIS-SIM-9921', '2026-11-12 06:36:04', 'now()', 'Booking'),
	(10, 'BRB-1794465421', 4, 3, '2026-11-12', '13:38:00', 1200000.00, 'QRIS', 'Lunas', 'QRIS-SIM-4513', '2026-11-12 06:37:04', 'now()', 'Booking'),
	(11, 'BRB-1794465434', 4, 3, '2026-11-13', '13:38:00', 1200000.00, 'QRIS', 'Lunas', 'QRIS-SIM-9012', '2026-11-12 06:37:16', 'now()', 'Booking'),
	(12, 'BRB-1794465444', 4, 3, '2026-11-13', '13:38:00', 1200000.00, 'QRIS', 'Lunas', 'QRIS-SIM-9697', '2026-11-12 06:37:25', 'now()', 'Booking'),
	(13, 'BRB-1794465452', 4, 3, '2026-11-13', '13:38:00', 1200000.00, 'QRIS', 'Lunas', 'QRIS-SIM-3597', '2026-11-12 06:37:33', 'now()', 'Booking'),
	(14, 'MNL-1794496554', NULL, 1, '2026-11-12', '15:15:00', 35000.00, 'QRIS', 'Lunas', 'QRIS-MNL-9652', '2026-11-12 15:15:54', 'asep', 'Manual'),
	(15, 'BKG-6AF5DD9102D77', 4, 1, '2026-11-13', '22:39:00', 35000.00, 'QRIS', 'Lunas', 'QRIS-SIM-7919', '2026-11-12 15:39:02', 'now()', 'Booking'),
	(16, 'BKG-6AF5DDB557DE5', 4, 1, '2026-11-12', '22:40:00', 35000.00, 'QRIS', 'Lunas', 'QRIS-SIM-7468', '2026-11-12 15:39:40', 'now()', 'Booking'),
	(17, 'BKG-6AF5DE24EA8B3', 4, 1, '2026-11-12', '22:42:00', 35000.00, 'Tunai', 'Lunas', '', '2026-11-12 15:41:24', 'now()', 'Booking'),
	(18, 'BKG-6AF5DEBD1A7C5', 4, 1, '2026-11-12', '22:46:00', 35000.00, 'Tunai', 'Lunas', 'QRIS-SIM-6997', '2026-11-12 15:44:02', 'now()', 'Booking'),
	(19, 'BKG-6AF5DECC75B3C', 4, 1, '2026-11-13', '22:45:00', 35000.00, 'Tunai', 'Lunas', 'QRIS-SIM-5407', '2026-11-12 15:44:49', 'now()', 'Booking'),
	(20, 'BKG-6AF5DEF75BB89', 4, 1, '2026-11-12', '22:45:00', 35000.00, 'Tunai', 'Lunas', '', '2026-11-12 15:52:40', 'now()', 'Booking'),
	(21, 'BKG-6AF5DF5C5634C', 4, 1, '2026-11-13', '22:47:00', 35000.00, 'Tunai', 'Lunas', '', '2026-11-12 15:52:38', 'now()', 'Booking'),
	(22, 'MNL-1794499098', NULL, 1, '2026-11-12', '15:57:00', 35000.00, 'Tunai', 'Lunas', '', '2026-11-12 15:58:18', 'asep', 'Manual'),
	(23, 'BKG-6AF5E27A4DF9D', 4, 1, '2026-11-12', '22:00:00', 35000.00, 'QRIS', 'Pending', 'QRIS-6652', '2026-11-12 15:59:54', 'now()', 'Booking');

-- membuang struktur untuk table barbershop_db.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '',
  `role` enum('admin','konsumen') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'konsumen',
  `created_at` timestamp NOT NULL DEFAULT (now()) ON UPDATE CURRENT_TIMESTAMP,
  `foto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Membuang data untuk tabel barbershop_db.users: ~0 rows (lebih kurang)
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `created_at`, `foto`) VALUES
	(3, 'admin1', 'admin1@barber.com', '$2y$10$Y7xpnlgBIKR53FRzRv.7tO.Lv96K6.QigtkiMRxaCuM8fh8.ahjHe', 'admin', '2026-11-12 16:23:49', 'admin_3_1794500629.png'),
	(4, 'fauzi', 'fauzi@ulbi.ac.id', '$2y$10$4SlkayCOiJ8yrMAUZVS0I.S1KSxuIs/fV/d56cEDSp0yQbeB8mCIS', 'konsumen', '2026-11-12 06:03:57', NULL),
	(5, 'Fathan', 'fathan@ulbi.ac.id', '$2y$10$sF80/i/bdKHY8P10cKsOoug48TQ0TKR3yMEC/FNOh6aFnas/xZ2LW', 'konsumen', '2026-11-12 16:25:14', 'user_5_1794500714.png');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
