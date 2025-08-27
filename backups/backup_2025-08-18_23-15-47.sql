-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: ribbowsite_db
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
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'admin','shuhaidahrabiu@gmail.com','$2y$10$PSPTJyTTO0TSTY4NFirPMuf0OtyaM49c7O61o4L01QVN2upfaE/3W','2025-08-17 12:05:36');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_product` (`user_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tracking_id` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`items`)),
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Shipped','Delivered','Cancelled') NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `proof` varchar(255) DEFAULT NULL,
  `email_sent` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tracking_id` (`tracking_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=199 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (197,'PGBS22DY1E',1,'Shuhaidah','kjhgftghjkjhgh','07017557260','[{\"name\":\"Beaded Bag Combo\",\"price\":55000,\"image\":\"Images/beadedBagCombo.jpg\",\"quantity\":1},{\"name\":\"Beaded Bag White\",\"price\":30000,\"image\":\"Images/beadedwhite.jpg\",\"quantity\":1},{\"name\":\"Beaded Black\",\"price\":32000,\"image\":\"Images/beadedblack.jpg\",\"quantity\":1},{\"name\":\"Pink Face Massager\",\"price\":6000,\"image\":\"Images/PinkFaceMassager.jpg\",\"quantity\":1},{\"name\":\"Blue Face Massager\",\"price\":6000,\"image\":\"Images/BlueFaceMassager.jpg\",\"quantity\":1},{\"name\":\"Lip Balm\",\"price\":2500,\"image\":\"Images/BerriesFlavoredLipBalm.jpg\",\"quantity\":2},{\"name\":\"Hand Cream\",\"price\":2000,\"image\":\"Images/PeachFlavoredHandCream.jpg\",\"quantity\":1}]',136000.00,'2025-08-17 22:17:39','Cancelled','shuhaidahrabiu@gmail.com','uploads/payment_proofs/1755469059_68a255031037e.png',1),(198,'43YG2TIB4W',1,'Shuhaidah','kjhgftghjkjhgh','07017557260','[{\"name\":\"Beaded Bag Combo\",\"price\":55000,\"image\":\"Images/beadedBagCombo.jpg\",\"quantity\":1},{\"name\":\"Beaded Bag White\",\"price\":30000,\"image\":\"Images/beadedwhite.jpg\",\"quantity\":1},{\"name\":\"Beaded Black\",\"price\":32000,\"image\":\"Images/beadedblack.jpg\",\"quantity\":1},{\"name\":\"Pink Face Massager\",\"price\":6000,\"image\":\"Images/PinkFaceMassager.jpg\",\"quantity\":1},{\"name\":\"Blue Face Massager\",\"price\":6000,\"image\":\"Images/BlueFaceMassager.jpg\",\"quantity\":1},{\"name\":\"Lip Balm\",\"price\":2500,\"image\":\"Images/BerriesFlavoredLipBalm.jpg\",\"quantity\":2},{\"name\":\"Hand Cream\",\"price\":2000,\"image\":\"Images/PeachFlavoredHandCream.jpg\",\"quantity\":1},{\"id\":\"9\",\"name\":\"Beaded Bag Combo 2\",\"price\":55000,\"image\":\"product_68a339febe1fe.jpg\",\"quantity\":1}]',191000.00,'2025-08-18 15:16:30','Pending','shuhaidahrabiu@gmail.com','uploads/payment_proofs/1755530190_68a343ce13045.png',0);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (9,'Beaded Bag Combo 2',55000.00,'product_68a339febe1fe.jpg','beadedbag','2025-08-18 14:34:38');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'shuhaidah','shuhaidahrabiu@gmail.com','$2y$10$EsbOHUJpBETDbMtpS2K4XuaCvgNWs5ldHXEkQg0F6.ZmSF9niWhKy','2025-07-22 22:59:25'),(2,'Aisha','aisha@gmail.com','$2y$10$p4G7tf14QqTVRWeu6S9NweeZAlnatzWJUQ2Pz8t8fy3m9AyUP5Ye.','2025-07-22 23:26:39'),(3,'Aisha','aish@gmail.com','$2y$10$QvXZKKsA5/Uf1wDorWswGOIjgBCgz8Pea2GHQ4b3bJMTxEor0ejGK','2025-07-22 23:40:33'),(4,'Khadijah','khadijah@gmail.com','$2y$10$On1c74EAnqWAYhAVYFavXuWeQz1OF0t/kr4rB2e.Bf3B1Kcph.fBG','2025-07-23 00:14:15'),(5,'Zainab','zainab@gmail.com','$2y$10$K/fYO4egvn0P5aKWifLCTuLtVKa.nTexxBE1KiyeySx.tvl4MW.jq','2025-07-27 14:53:46'),(6,'Ray','ray@gmail.com','$2y$10$80xJdmUnwVLclOCoKaHko./zOWzfABPojtIkruJerOMbgnpH59lIe','2025-07-27 23:25:53'),(7,'Maryam','maryam@gmail.com','$2y$10$5rmUdUXdWrcZZoQlqh2Q8.sO58nMC/sUrVrgAlVFttwd4UecEaKAi','2025-07-30 21:11:54'),(8,'Aliyu','aliyu@gmail.com','$2y$10$dFoY4L2WTza14pn0f66n4OSTvpcKqyg5rceN9wahokJmCTcPzO1Qi','2025-07-30 21:14:21'),(9,'Amina','amina1234@gmail.com','$2y$10$VN369JZ3GDs0NrHXkvNcb.vAanfJ6AFG.HLmquSHcgf7OYhKyWYlG','2025-08-05 11:17:03'),(10,'Habib','habib4321@gmail.com','$2y$10$LXUfzvliLRR4IabiUCThV.xsrl24zb6EXzeu6kqEUxlb0sitq8VdC','2025-08-05 11:36:10'),(11,'Moha','moha00@gmail.com','$2y$10$ifahVP8qFwmbuI/my8Nfb.3Icgq3bve08TAr9Qa1AqIvgOKxqXS/2','2025-08-05 11:37:40'),(12,'Ahmad','ahmad24@gmail.com','$2y$10$DOm/Hwz6.xH3bhuVPdXpBOVeTaPJ9P26xzCHMlSXpbt/yJzNCRJSS','2025-08-06 22:44:16'),(13,'Muhammad','mabubakar419@gmail.com','$2y$10$.SsiKCsvoOgDRhu5d3V34.DM0TVr84fZ1Wf.slZADZpxFcYfhR27K','2025-08-07 09:12:09'),(14,'Hassan','Hasmohwaz@gmail.com','$2y$10$q7.zAISoZiGIvs.1B9zUQOFSzlasE/droRRw5SM3Oqi/o5gWAbB1e','2025-08-07 09:57:28'),(15,'Usman','usman1234@gmail.com','$2y$10$C82X0f5XSvkmzMfHEcEe6OoMLf1zDd1ZI/D6GtWGxReW4SoZyRK22','2025-08-08 22:43:30'),(16,'Ashim','ashim@gmail.com','$2y$10$UFRHTgRK6uWRs4abfykHCuriJGOF5E47kkg.6JUnI.xOQolGj0Ij2','2025-08-12 08:34:09'),(17,'Khadijah','khadijahibrahim08@gmail.com','$2y$10$.ocCX8Xq4mCUoFIfIZMeoe9PIafBJ/qNhbXMuhF5hzGdapswTLjty','2025-08-12 23:10:38'),(18,'Princess','fredblessingtommy@gmail.com','$2y$10$RBUGMr4Yu4E7qp9Z9bYpheGrXN4wqMiFuUknXrcR4NcGfpKH4FkD6','2025-08-14 09:35:57');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-18 23:15:48
