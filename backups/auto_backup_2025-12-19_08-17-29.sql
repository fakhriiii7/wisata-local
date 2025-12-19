SET FOREIGN_KEY_CHECKS=0;

-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: wisata_simple
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `backup_log`
--

DROP TABLE IF EXISTS `backup_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backup_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `backup_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backup_log`
--

LOCK TABLES `backup_log` WRITE;
/*!40000 ALTER TABLE `backup_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `backup_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `destinasi`
--

DROP TABLE IF EXISTS `destinasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `destinasi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_destinasi` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga_dewasa` decimal(10,2) NOT NULL,
  `harga_anak` decimal(10,2) NOT NULL,
  `kuota_harian` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `destinasi`
--

LOCK TABLES `destinasi` WRITE;
/*!40000 ALTER TABLE `destinasi` DISABLE KEYS */;
INSERT INTO `destinasi` VALUES (1,'Taman Wisata Alam','Taman alam dengan berbagai flora dan fauna menarik',50000.00,25000.00,200,NULL,'2025-10-27 09:42:26',NULL),(2,'Museum Sejarah','Museum yang menyimpan sejarah dan budaya daerah',35000.00,15000.00,150,NULL,'2025-10-27 09:42:26',NULL),(3,'Air Terjun Seruni','Air terjun dengan pemandangan alam yang menakjubkan',45000.00,20000.00,100,NULL,'2025-10-27 09:42:26',NULL),(4,'Gunung Berapi View','Pemandangan gunung berapi yang menakjubkan dengan trekking ringan dan spot foto yang instagramable',75000.00,35000.00,150,'1765879655_c0aad982306e.jpg','2025-10-27 17:15:35',NULL),(5,'Pantai Pasir Putih','Pantai dengan pasir putih yang halus, air laut jernih, dan fasilitas water sport lengkap',60000.00,30000.00,250,'1765879692_ec2ff71041b1.jpg','2025-10-27 17:15:35',NULL),(6,'Hutan Wisata Alam','Hutan konservasi dengan berbagai flora dan fauna langka, cocok untuk edukasi dan healing',45000.00,20000.00,100,'1765879705_15fa50ed86e0.jpg','2025-10-27 17:15:35',NULL),(7,'Air Panas Alami','Pemandian air panas alami dari sumber geothermal dengan khasiat terapi kesehatan',55000.00,25000.00,120,'1765872238_8b191fe5a4a2.jpg','2025-10-27 17:15:35',NULL),(8,'Desa Wisata Budaya','Pengalaman budaya lokal dengan workshop kerajinan tangan dan kuliner tradisional',40000.00,15000.00,80,NULL,'2025-10-27 17:15:35',NULL),(9,'Taman Bunga Indah','Taman dengan berbagai jenis bunga tropis yang cantik, perfect untuk prewedding dan fotografi',35000.00,15000.00,200,NULL,'2025-10-27 17:15:35',NULL),(10,'Goa Alam Menakjubkan','Eksplorasi goa alam dengan stalaktit dan stalagmit yang unik serta sejarah geologinya',50000.00,20000.00,60,NULL,'2025-10-27 17:15:35',NULL),(11,'Danau Biru Alami','Danau dengan air berwarna biru alami, spot memancing, dan perahu tradisional',65000.00,30000.00,90,NULL,'2025-10-27 17:15:35',NULL),(12,'Kebun Buah Organik','Kebun buah-buahan organik dengan sistem petik sendiri dan edukasi pertanian',30000.00,10000.00,120,NULL,'2025-10-27 17:15:35',NULL),(13,'Museum Seni Kontemporer','Museum seni modern dengan koleksi karya seniman lokal dan internasional',25000.00,10000.00,180,NULL,'2025-10-27 17:15:35',NULL);
/*!40000 ALTER TABLE `destinasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pemesanan`
--

DROP TABLE IF EXISTS `pemesanan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pemesanan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_booking` varchar(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `destinasi_id` int(11) NOT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_telepon` varchar(15) NOT NULL,
  `tanggal_berkunjung` date NOT NULL,
  `jumlah_dewasa` int(11) NOT NULL,
  `jumlah_anak` int(11) NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_booking` (`kode_booking`),
  KEY `user_id` (`user_id`),
  KEY `destinasi_id` (`destinasi_id`),
  CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `pemesanan_ibfk_2` FOREIGN KEY (`destinasi_id`) REFERENCES `destinasi` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pemesanan`
--

LOCK TABLES `pemesanan` WRITE;
/*!40000 ALTER TABLE `pemesanan` DISABLE KEYS */;
INSERT INTO `pemesanan` VALUES (1,'WB20240101FA01',4,1,'Fakhri Azmar','fakhri.azmar@email.com','081298765431','2024-02-10',2,1,175000.00,'confirmed','2024-01-01 02:15:00'),(2,'WB20240115FA02',4,3,'Fakhri Azmar','fakhri.azmar@email.com','081298765431','2024-02-20',3,0,135000.00,'confirmed','2024-01-15 07:30:00'),(3,'WB20240102ZK01',5,2,'Zahra Kalista','zahra.kalista@email.com','081287654329','2024-02-15',4,2,300000.00,'confirmed','2024-01-02 03:20:00'),(4,'WB20240120ZK02',5,5,'Zahra Kalista','zahra.kalista@email.com','081287654329','2024-03-05',2,1,95000.00,'confirmed','2024-01-20 09:45:00'),(5,'WB20240103RA01',6,4,'Rafi Ananta','rafi.ananta@email.com','081276543218','2024-02-18',3,1,190000.00,'confirmed','2024-01-03 04:30:00'),(6,'WB20240118RA02',6,7,'Rafi Ananta','rafi.ananta@email.com','081276543218','2024-03-10',2,0,100000.00,'confirmed','2024-01-18 06:15:00'),(7,'WB20240104SN01',7,6,'Salsabila Naura','salsabila.naura@email.com','081265432107','2024-02-22',2,3,115000.00,'confirmed','2024-01-04 01:45:00'),(8,'WB20240122SN02',7,9,'Salsabila Naura','salsabila.naura@email.com','081265432107','2024-03-15',5,2,170000.00,'confirmed','2024-01-22 10:20:00'),(9,'WB20240105KA01',8,8,'Kenzo Arjuna','kenzo.arjuna@email.com','081254321096','2024-02-25',4,2,320000.00,'confirmed','2024-01-05 05:10:00'),(10,'WB20240125KA02',8,10,'Kenzo Arjuna','kenzo.arjuna@email.com','081254321096','2024-03-20',6,3,180000.00,'confirmed','2024-01-25 08:30:00'),(11,'WB20240106NP01',9,1,'Nayla Putri','nayla.putri@email.com','081243210985','2024-02-28',2,0,100000.00,'confirmed','2024-01-06 02:50:00'),(12,'WB20240128NP02',9,3,'Nayla Putri','nayla.putri@email.com','081243210985','2024-03-25',3,1,145000.00,'cancelled','2024-01-28 07:40:00'),(13,'WB20240107BP01',10,2,'Bagas Pratama','bagas.pratama@email.com','081232109874','2024-03-01',5,2,340000.00,'confirmed','2024-01-07 03:25:00'),(14,'WB20240130BP02',10,5,'Bagas Pratama','bagas.pratama@email.com','081232109874','2024-03-30',2,2,110000.00,'confirmed','2024-01-30 11:15:00'),(15,'WB20240108CM01',11,4,'Citra Melati','citra.melati@email.com','081221098763','2024-03-03',3,0,165000.00,'confirmed','2024-01-08 04:35:00'),(16,'WB20240201CM02',11,7,'Citra Melati','citra.melati@email.com','081221098763','2024-04-05',4,1,230000.00,'confirmed','2024-02-01 09:50:00'),(17,'WB20240109DR01',12,6,'Daffa Rizki','daffa.rizki@email.com','081210987652','2024-03-08',2,1,85000.00,'confirmed','2024-01-09 06:20:00'),(18,'WB20240205DR02',12,9,'Daffa Rizki','daffa.rizki@email.com','081210987652','2024-04-10',3,2,120000.00,'confirmed','2024-02-05 12:30:00'),(19,'WB20240110EP01',13,8,'Elsa Permata','elsa.permata@email.com','081209876541','2024-03-12',4,3,385000.00,'confirmed','2024-01-10 08:45:00'),(20,'WB20240210EP02',13,10,'Elsa Permata','elsa.permata@email.com','081209876541','2024-04-15',5,1,140000.00,'confirmed','2024-02-10 13:10:00'),(21,'WB202510288B354F',NULL,7,'Fakhri','fakhri.azmar@email.com','021234567891','2025-10-30',5,1,300000.00,'confirmed','2025-10-28 08:44:56'),(22,'WB20251105977D8B',4,7,'Fakhri Azmar','fakhri.azmar@email.com','081298765431','2025-11-15',121,0,6655000.00,'confirmed','2025-11-05 11:19:37'),(23,'WB202512092DDBC4',NULL,7,'fakhir','fakhri@gmail.com','123','2025-12-16',1,0,55000.00,'confirmed','2025-12-09 03:01:54'),(24,'WB202512099A0345',14,11,'fakhri','fakhri@mail.com','1234456','2025-12-09',1,1,95000.00,'confirmed','2025-12-09 03:03:53'),(25,'WB2025121613EE9F',14,7,'fakhri','fakhri@mail.com','1234456','2025-12-17',1,0,55000.00,'confirmed','2025-12-16 14:02:41'),(26,'WB20251218D8889E',14,7,'fakhri','fakhri@mail.com','1234456','2025-12-19',1,0,55000.00,'cancelled','2025-12-18 05:22:37'),(27,'WB202512181B291E',14,7,'fakhri','fakhri@mail.com','1234456','2025-12-19',1,0,55000.00,'confirmed','2025-12-18 05:24:01'),(28,'WB2025121909CDF4',14,3,'fakhri','fakhri@mail.com','1234456','2025-12-19',1,0,45000.00,'confirmed','2025-12-18 23:30:08'),(29,'WB20251219399DE8',14,3,'fakhri','fakhri@mail.com','1234456','2025-12-31',1,0,45000.00,'cancelled','2025-12-18 23:30:43'),(30,'WB20251219D329C5',14,8,'fakhri','fakhri@mail.com','1234456','2025-12-30',1,0,40000.00,'confirmed','2025-12-18 23:39:57');
/*!40000 ALTER TABLE `pemesanan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_telepon` varchar(15) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator','admin@wisata.com','$2y$10$N9yACAzVhYEvBw4BDVbpc.s/u8doDgJOsZpyrc3qctrHOMLlbyQ1G',NULL,'admin','2025-10-27 09:42:26'),(4,'Fakhri Azmar','fakhri.azmar@email.com','$2y$10$oiJvAD5bZmVhe1l8cPfLeOhLGAi7DfuqGEOpoFC83FxB26gkcsiR6','081298765431','user','2025-10-28 06:41:00'),(5,'Zahra Kalista','zahra.kalista@email.com','$2y$10$uz/k7H11SS5iNV7MsHYLu.ix1yT7rAgANhDMUJVHw4mVsMMgT7txG','081287654329','user','2025-10-28 06:41:01'),(6,'Rafi Ananta','rafi.ananta@email.com','$2y$10$t7rCY1enNhpyXRqpw8bGA.DIL8f3WVCGV3larnMHV1d9bDjrzxAZy','081276543218','user','2025-10-28 06:41:01'),(7,'Salsabila Naura','salsabila.naura@email.com','$2y$10$YFzcfjENNk8skU0aHKD1A.uloZ0oJqcZDTOidFm85vYcPj0fEbdcW','081265432107','user','2025-10-28 06:41:01'),(8,'Kenzo Arjuna','kenzo.arjuna@email.com','$2y$10$XtvHqwG/pq/K6z.nmIG1x.gmVHD4D4qK5ZElqeYwVz4P4k1rxV6pu','081254321096','user','2025-10-28 06:41:01'),(9,'Nayla Putri','nayla.putri@email.com','$2y$10$RJwGMgQto2yX2Nn9v4lSQub/ojc.8EA9oBkXKRL7Qg/.7RHgl01tK','081243210985','user','2025-10-28 06:41:01'),(10,'Bagas Pratama','bagas.pratama@email.com','$2y$10$fjepV2pvcXXQVQszylaCnOtizNuFgKGJWa4xTwD0CWgPPQTsunSOK','081232109874','user','2025-10-28 06:41:02'),(11,'Citra Melati','citra.melati@email.com','$2y$10$V9qBhxCwn5sWu1edUTpiKe9wEwkj8bGwkz//nNPa5H9al8r5qlEga','081221098763','user','2025-10-28 06:41:02'),(12,'Daffa Rizki','daffa.rizki@email.com','$2y$10$.K3i4QMEvooW62DsNea3QOr/9tk8ftvWYa6uvruWf7sywq.FWmpGa','081210987652','user','2025-10-28 06:41:02'),(13,'Elsa Permata','elsa.permata@email.com','$2y$10$ubMDCffwuLdX0dtINXNxKOshs22pLDhjN.Wrzx2xzXRneZu1.gXZ6','081209876541','user','2025-10-28 06:41:03'),(14,'fakhri','fakhri@mail.com','$2y$10$mfnjgBrGwplFunaYBdyoqueTEV9iCOtHYfR897XbvFKsxlyKhoOpq','1234456','user','2025-12-09 03:03:02');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'wisata_simple'
--

--
-- Dumping routines for database 'wisata_simple'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-19  8:17:29


SET FOREIGN_KEY_CHECKS=1;