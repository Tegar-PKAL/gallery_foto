-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for gallery_foto
CREATE DATABASE IF NOT EXISTS `gallery_foto` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `gallery_foto`;

-- Dumping structure for table gallery_foto.album
CREATE TABLE IF NOT EXISTS `album` (
  `AlbumID` int NOT NULL AUTO_INCREMENT,
  `NamaAlbum` varchar(255) NOT NULL,
  `Deskripsi` text,
  `TanggalDibuat` date DEFAULT NULL,
  `UserID` int DEFAULT NULL,
  PRIMARY KEY (`AlbumID`),
  KEY `UserID` (`UserID`),
  CONSTRAINT `album_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table gallery_foto.album: ~0 rows (approximately)
INSERT INTO `album` (`AlbumID`, `NamaAlbum`, `Deskripsi`, `TanggalDibuat`, `UserID`) VALUES
	(3, 'Buanzai', 'Nandakore', '2026-04-21', 2);

-- Dumping structure for table gallery_foto.foto
CREATE TABLE IF NOT EXISTS `foto` (
  `FotoID` int NOT NULL AUTO_INCREMENT,
  `JudulFoto` varchar(255) NOT NULL,
  `DeskripsiFoto` text,
  `TanggalUnggah` date DEFAULT NULL,
  `LokasiFile` varchar(255) NOT NULL,
  `AlbumID` int DEFAULT NULL,
  `UserID` int DEFAULT NULL,
  PRIMARY KEY (`FotoID`),
  KEY `AlbumID` (`AlbumID`),
  KEY `UserID` (`UserID`),
  CONSTRAINT `foto_ibfk_1` FOREIGN KEY (`AlbumID`) REFERENCES `album` (`AlbumID`) ON DELETE SET NULL,
  CONSTRAINT `foto_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table gallery_foto.foto: ~0 rows (approximately)
INSERT INTO `foto` (`FotoID`, `JudulFoto`, `DeskripsiFoto`, `TanggalUnggah`, `LokasiFile`, `AlbumID`, `UserID`) VALUES
	(5, 'Kamen Rider Faiz', '#kamenrider #faiz #kamenriderfaiz', '2026-04-21', '1776777413_25b7f796e6f3e478c96c051d30d4cea6.jpg', 3, 2),
	(6, 'The Triosen', '#thetriosen', '2026-04-21', '1776777504_Gemini_Generated_Image_hnc9ehhnc9ehhnc9.png', 3, 2);

-- Dumping structure for table gallery_foto.komentarfoto
CREATE TABLE IF NOT EXISTS `komentarfoto` (
  `KomentarID` int NOT NULL AUTO_INCREMENT,
  `FotoID` int DEFAULT NULL,
  `UserID` int DEFAULT NULL,
  `IsiKomentar` text,
  `TanggalKomentar` date DEFAULT NULL,
  PRIMARY KEY (`KomentarID`),
  KEY `FotoID` (`FotoID`),
  KEY `UserID` (`UserID`),
  CONSTRAINT `komentarfoto_ibfk_1` FOREIGN KEY (`FotoID`) REFERENCES `foto` (`FotoID`) ON DELETE CASCADE,
  CONSTRAINT `komentarfoto_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table gallery_foto.komentarfoto: ~0 rows (approximately)

-- Dumping structure for table gallery_foto.likefoto
CREATE TABLE IF NOT EXISTS `likefoto` (
  `LikeID` int NOT NULL AUTO_INCREMENT,
  `FotoID` int DEFAULT NULL,
  `UserID` int DEFAULT NULL,
  `TanggalLike` date DEFAULT NULL,
  PRIMARY KEY (`LikeID`),
  KEY `FotoID` (`FotoID`),
  KEY `UserID` (`UserID`),
  CONSTRAINT `likefoto_ibfk_1` FOREIGN KEY (`FotoID`) REFERENCES `foto` (`FotoID`) ON DELETE CASCADE,
  CONSTRAINT `likefoto_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table gallery_foto.likefoto: ~0 rows (approximately)

-- Dumping structure for table gallery_foto.user
CREATE TABLE IF NOT EXISTS `user` (
  `UserID` int NOT NULL AUTO_INCREMENT,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `NamaLengkap` varchar(255) NOT NULL,
  `Alamat` text,
  `Role` enum('admin','user') DEFAULT 'user',
  `FotoProfil` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table gallery_foto.user: ~1 rows (approximately)
INSERT INTO `user` (`UserID`, `Username`, `Password`, `Email`, `NamaLengkap`, `Alamat`, `Role`, `FotoProfil`) VALUES
	(1, 'ZenzRafel', 'Zenz240199', 'avalornecrownfall@gmail.com', 'LaferVelvet', '', 'admin', 'PP_1_1776736622.png'),
	(2, 'Lafera', '$2y$10$ZYX4h7UIGaqHZJqrbJyjjOuVjitmhUc0gZc0M91d7KhJJuJQ7JeLO', 'ercgeneralevros@gmail.com', 'Lafera Van Coofer', '', 'user', 'PP_2_1776777037.png');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
