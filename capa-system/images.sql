-- MySQL dump 10.13  Distrib 5.5.54, for debian-linux-gnu (armv7l)
--
-- Host: localhost    Database: images
-- ------------------------------------------------------
-- Server version	5.5.54-0+deb8u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `imgdat`
--

DROP TABLE IF EXISTS `imgdat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imgdat` (
  `imgId` int(11) NOT NULL AUTO_INCREMENT,
  `imgUrl` text NOT NULL,
  `imgNice` text NOT NULL,
  `imgComment` varchar(250) DEFAULT NULL,
  `imgDate` datetime NOT NULL,
  `imgRes` varchar(20) DEFAULT NULL,
  `imgAwb` varchar(20) DEFAULT NULL,
  `imgEx` varchar(20) DEFAULT NULL,
  `imgMeter` varchar(20) DEFAULT NULL,
  `imgEffect` varchar(20) DEFAULT NULL,
  `imgDrc` varchar(20) DEFAULT NULL,
  `imgSharpness` varchar(20) DEFAULT NULL,
  `imgContrast` varchar(20) DEFAULT NULL,
  `imgBrightness` varchar(20) DEFAULT NULL,
  `imgSaturation` varchar(20) DEFAULT NULL,
  `imgIso` varchar(20) DEFAULT NULL,
  `imgSS` varchar(20) DEFAULT NULL,
  `imgJpg` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`imgId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-06-06 12:51:57
