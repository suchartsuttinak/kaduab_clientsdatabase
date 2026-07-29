-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: clientsdatabase
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
-- Table structure for table `about_data`
--

DROP TABLE IF EXISTS `about_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `about_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('history','objective','mission') NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `about_data`
--

LOCK TABLES `about_data` WRITE;
/*!40000 ALTER TABLE `about_data` DISABLE KEYS */;
INSERT INTO `about_data` VALUES (1,'objective','ccccccccccccccccccc','2026-05-01 23:38:56','2026-05-01 23:38:56');
/*!40000 ALTER TABLE `about_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `absents`
--

DROP TABLE IF EXISTS `absents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `absents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `absent_date` date NOT NULL COMMENT 'วันที่ขาดเรียน',
  `cause` text DEFAULT NULL COMMENT 'สาเหตุที่ขาดเรียน',
  `operation` text DEFAULT NULL COMMENT 'การดำเนินการ',
  `remark` text DEFAULT NULL COMMENT 'หมายเหตุ',
  `record_date` date NOT NULL COMMENT 'วันที่บันทึก',
  `teacher` varchar(255) DEFAULT NULL COMMENT 'ชื่อครูผู้ให้ข้อมูล',
  `client_id` bigint(20) unsigned NOT NULL,
  `education_record_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `absents_client_id_absent_date_unique` (`client_id`,`absent_date`),
  KEY `absents_client_id_foreign` (`client_id`),
  KEY `absents_education_record_id_foreign` (`education_record_id`),
  CONSTRAINT `absents_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `absents_education_record_id_foreign` FOREIGN KEY (`education_record_id`) REFERENCES `education_records` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `absents`
--

LOCK TABLES `absents` WRITE;
/*!40000 ALTER TABLE `absents` DISABLE KEYS */;
/*!40000 ALTER TABLE `absents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `accidents`
--

DROP TABLE IF EXISTS `accidents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accidents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `incident_date` date NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `eyewitness` varchar(255) DEFAULT NULL,
  `detail` text DEFAULT NULL,
  `cause` varchar(255) DEFAULT NULL,
  `treat_no` enum('พบแพทย์','ไม่พบแพทย์') NOT NULL DEFAULT 'ไม่พบแพทย์',
  `hospital` varchar(255) DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `appointment` varchar(255) DEFAULT NULL,
  `protection` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `caretaker` varchar(255) DEFAULT NULL,
  `record_date` date NOT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accidents_incident_date_index` (`incident_date`),
  KEY `accidents_client_id_index` (`client_id`),
  CONSTRAINT `accidents_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accidents`
--

LOCK TABLES `accidents` WRITE;
/*!40000 ALTER TABLE `accidents` DISABLE KEYS */;
/*!40000 ALTER TABLE `accidents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `addictives`
--

DROP TABLE IF EXISTS `addictives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `addictives` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `count` int(11) NOT NULL,
  `exam` tinyint(4) NOT NULL DEFAULT 0,
  `refer` tinyint(4) DEFAULT NULL,
  `record` text DEFAULT NULL,
  `recorder` varchar(255) NOT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `addictives_client_id_foreign` (`client_id`),
  CONSTRAINT `addictives_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addictives`
--

LOCK TABLES `addictives` WRITE;
/*!40000 ALTER TABLE `addictives` DISABLE KEYS */;
/*!40000 ALTER TABLE `addictives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `behavior_screening_items`
--

DROP TABLE IF EXISTS `behavior_screening_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `behavior_screening_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `behavior_screening_id` bigint(20) unsigned NOT NULL,
  `category` varchar(30) NOT NULL,
  `item_no` tinyint(3) unsigned NOT NULL,
  `question` text NOT NULL,
  `answer` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `behavior_screening_items_behavior_screening_id_category_index` (`behavior_screening_id`,`category`),
  CONSTRAINT `behavior_screening_items_behavior_screening_id_foreign` FOREIGN KEY (`behavior_screening_id`) REFERENCES `behavior_screenings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `behavior_screening_items`
--

LOCK TABLES `behavior_screening_items` WRITE;
/*!40000 ALTER TABLE `behavior_screening_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `behavior_screening_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `behavior_screenings`
--

DROP TABLE IF EXISTS `behavior_screenings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `behavior_screenings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `screening_date` date NOT NULL,
  `observer_name` varchar(255) DEFAULT NULL,
  `age_text` varchar(255) DEFAULT NULL,
  `class_level` varchar(255) DEFAULT NULL,
  `learning_score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `ld_score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `adhd_score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `autism_score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `learning_risk` tinyint(1) NOT NULL DEFAULT 0,
  `ld_risk` tinyint(1) NOT NULL DEFAULT 0,
  `adhd_risk` tinyint(1) NOT NULL DEFAULT 0,
  `autism_risk` tinyint(1) NOT NULL DEFAULT 0,
  `summary` text DEFAULT NULL,
  `recommendation` text DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `behavior_screenings_created_by_foreign` (`created_by`),
  KEY `behavior_screenings_client_id_screening_date_index` (`client_id`,`screening_date`),
  CONSTRAINT `behavior_screenings_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `behavior_screenings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `behavior_screenings`
--

LOCK TABLES `behavior_screenings` WRITE;
/*!40000 ALTER TABLE `behavior_screenings` DISABLE KEYS */;
/*!40000 ALTER TABLE `behavior_screenings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `case_activities`
--

DROP TABLE IF EXISTS `case_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `case_activities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `module` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'info',
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `occurred_at` datetime DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `case_activities_user_id_foreign` (`user_id`),
  KEY `case_activities_client_id_occurred_at_index` (`client_id`,`occurred_at`),
  KEY `case_activities_module_type_index` (`module`,`type`),
  CONSTRAINT `case_activities_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `case_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `case_activities`
--

LOCK TABLES `case_activities` WRITE;
/*!40000 ALTER TABLE `case_activities` DISABLE KEYS */;
INSERT INTO `case_activities` VALUES (38,206,1,'client','success','แก้ไขข้อมูลผู้รับบริการ','ชื่อ: นายสุชาติ สุทธินาค | เลขทะเบียน: REG001','2026-07-28 20:31:38','bi-person-check','http://127.0.0.1:8000/client/edit/206','2026-07-28 13:31:38','2026-07-28 13:31:38'),(39,206,1,'education_record','success','บันทึกผลการเรียน','บันทึกผลการเรียน สถานศึกษา: โรงเรียนชลกันยานุกูล | เกรดเฉลี่ย: -','2026-07-28 00:00:00','bi-mortarboard','http://127.0.0.1:8000/education_record/show/206','2026-07-28 13:49:15','2026-07-28 13:49:15');
/*!40000 ALTER TABLE `case_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `case_outsides`
--

DROP TABLE IF EXISTS `case_outsides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `case_outsides` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `count` int(11) NOT NULL,
  `outside_id` bigint(20) unsigned NOT NULL,
  `dormitory` varchar(255) DEFAULT NULL,
  `follo_no` enum('หน่วยงานไปเอง','โทรศัพท์','จดหมาย') NOT NULL,
  `results` text DEFAULT NULL,
  `teacher` varchar(255) DEFAULT NULL,
  `remerk` text DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `case_outsides_client_id_date_unique` (`client_id`,`date`),
  KEY `case_outsides_outside_id_foreign` (`outside_id`),
  CONSTRAINT `case_outsides_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `case_outsides_outside_id_foreign` FOREIGN KEY (`outside_id`) REFERENCES `outsides` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `case_outsides`
--

LOCK TABLES `case_outsides` WRITE;
/*!40000 ALTER TABLE `case_outsides` DISABLE KEYS */;
/*!40000 ALTER TABLE `case_outsides` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `check_bodies`
--

DROP TABLE IF EXISTS `check_bodies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `check_bodies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assessor_date` date DEFAULT NULL,
  `development` enum('สมวัย','ไม่สมวัย') NOT NULL DEFAULT 'สมวัย',
  `detail` text DEFAULT NULL,
  `development_type` varchar(255) DEFAULT NULL,
  `special_support_type` varchar(255) DEFAULT NULL,
  `special_support_other` varchar(255) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `oral` varchar(255) DEFAULT NULL,
  `appearance` varchar(255) DEFAULT NULL,
  `wound` varchar(255) DEFAULT NULL,
  `disease` varchar(255) DEFAULT NULL,
  `hygiene` varchar(255) DEFAULT NULL,
  `health` varchar(255) DEFAULT NULL,
  `inoculation` varchar(255) DEFAULT NULL,
  `injection` varchar(255) DEFAULT NULL,
  `vaccination` varchar(255) DEFAULT NULL,
  `contagious` varchar(255) DEFAULT NULL,
  `other` varchar(255) DEFAULT NULL,
  `drug_allergy` varchar(255) DEFAULT NULL,
  `recorder` varchar(255) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `check_bodies_client_id_foreign` (`client_id`),
  CONSTRAINT `check_bodies_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `check_bodies`
--

LOCK TABLES `check_bodies` WRITE;
/*!40000 ALTER TABLE `check_bodies` DISABLE KEYS */;
/*!40000 ALTER TABLE `check_bodies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `citizen_idstation`
--

DROP TABLE IF EXISTS `citizen_idstation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `citizen_idstation` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `idstation_id` bigint(20) unsigned NOT NULL,
  `citizen_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `citizen_idstation_idstation_id_citizen_id_unique` (`idstation_id`,`citizen_id`),
  KEY `citizen_idstation_citizen_id_foreign` (`citizen_id`),
  CONSTRAINT `citizen_idstation_citizen_id_foreign` FOREIGN KEY (`citizen_id`) REFERENCES `citizens` (`id`) ON DELETE CASCADE,
  CONSTRAINT `citizen_idstation_idstation_id_foreign` FOREIGN KEY (`idstation_id`) REFERENCES `idstations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `citizen_idstation`
--

LOCK TABLES `citizen_idstation` WRITE;
/*!40000 ALTER TABLE `citizen_idstation` DISABLE KEYS */;
/*!40000 ALTER TABLE `citizen_idstation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `citizens`
--

DROP TABLE IF EXISTS `citizens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `citizens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `citizen_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `citizens`
--

LOCK TABLES `citizens` WRITE;
/*!40000 ALTER TABLE `citizens` DISABLE KEYS */;
INSERT INTO `citizens` VALUES (1,'ได้รับการจดทะเบียนการเกิด','2026-07-04 10:40:46','2026-07-04 13:37:06'),(2,'ได้รับบัตรประจำตัวประชาชนครั้งแรก','2026-07-04 10:43:14','2026-07-04 13:36:45'),(3,'ได้รับบัตรประจำตัวบุคคลไม่มีสถานะทางทะเบียน','2026-07-04 10:43:31','2026-07-04 13:36:36'),(4,'ได้รับบัตรประจำตัวบุคคลไม่มีสัญชาติไทย','2026-07-04 10:43:52','2026-07-04 13:36:27'),(5,'ได้รับการเพิ่มชื่อบุคคลในทะเบียนบ้าน','2026-07-04 10:44:24','2026-07-04 13:36:17'),(6,'ได้รับสัญชาติตามกฎหมายสัญชาติ','2026-07-04 10:46:09','2026-07-04 10:46:09');
/*!40000 ALTER TABLE `citizens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `citizenship_idstation`
--

DROP TABLE IF EXISTS `citizenship_idstation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `citizenship_idstation` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `idstation_id` bigint(20) unsigned NOT NULL,
  `citizenship_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `citizenship_idstation_idstation_id_citizenship_id_unique` (`idstation_id`,`citizenship_id`),
  KEY `citizenship_idstation_citizenship_id_foreign` (`citizenship_id`),
  CONSTRAINT `citizenship_idstation_citizenship_id_foreign` FOREIGN KEY (`citizenship_id`) REFERENCES `citizenships` (`id`) ON DELETE CASCADE,
  CONSTRAINT `citizenship_idstation_idstation_id_foreign` FOREIGN KEY (`idstation_id`) REFERENCES `idstations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `citizenship_idstation`
--

LOCK TABLES `citizenship_idstation` WRITE;
/*!40000 ALTER TABLE `citizenship_idstation` DISABLE KEYS */;
/*!40000 ALTER TABLE `citizenship_idstation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `citizenships`
--

DROP TABLE IF EXISTS `citizenships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `citizenships` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `citizenship_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `citizenships_citizenship_name_unique` (`citizenship_name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `citizenships`
--

LOCK TABLES `citizenships` WRITE;
/*!40000 ALTER TABLE `citizenships` DISABLE KEYS */;
INSERT INTO `citizenships` VALUES (1,'ขอมีรายการในทะเบียนราษฎร','2026-07-04 14:16:38','2026-07-04 14:16:38'),(2,'ขอเพิ่มชื่อบุคคลในทะเบียนบ้าน','2026-07-04 14:17:30','2026-07-04 14:17:30'),(3,'ขอมีบัตรประจำตัวประชาชนครั้งแรก','2026-07-04 14:18:07','2026-07-04 14:18:07'),(4,'ขอจดทะเบียนการเกิดเด็ก','2026-07-04 14:18:39','2026-07-04 14:18:39'),(5,'ขอมีสัญชาติไทยตามมาตรา 7 ทวิ วรรคสอง','2026-07-04 14:19:13','2026-07-04 14:19:13'),(6,'ขอมีสัญชาติไทยตามมาตรา 19/2 วรรคสอง','2026-07-04 14:19:40','2026-07-04 14:19:40');
/*!40000 ALTER TABLE `citizenships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_files`
--

DROP TABLE IF EXISTS `client_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_files_client_id_foreign` (`client_id`),
  CONSTRAINT `client_files_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_files`
--

LOCK TABLES `client_files` WRITE;
/*!40000 ALTER TABLE `client_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `client_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_house_transfers`
--

DROP TABLE IF EXISTS `client_house_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_house_transfers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `old_house_id` bigint(20) unsigned DEFAULT NULL,
  `new_house_id` bigint(20) unsigned NOT NULL,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `caregiver_id` bigint(20) unsigned DEFAULT NULL,
  `changed_by` bigint(20) unsigned DEFAULT NULL,
  `transfer_date` date DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_house_transfers_client_id_foreign` (`client_id`),
  KEY `client_house_transfers_old_house_id_foreign` (`old_house_id`),
  KEY `client_house_transfers_new_house_id_foreign` (`new_house_id`),
  KEY `client_house_transfers_project_id_foreign` (`project_id`),
  KEY `client_house_transfers_caregiver_id_foreign` (`caregiver_id`),
  KEY `client_house_transfers_changed_by_foreign` (`changed_by`),
  CONSTRAINT `client_house_transfers_caregiver_id_foreign` FOREIGN KEY (`caregiver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `client_house_transfers_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `client_house_transfers_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `client_house_transfers_new_house_id_foreign` FOREIGN KEY (`new_house_id`) REFERENCES `houses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `client_house_transfers_old_house_id_foreign` FOREIGN KEY (`old_house_id`) REFERENCES `houses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `client_house_transfers_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_house_transfers`
--

LOCK TABLES `client_house_transfers` WRITE;
/*!40000 ALTER TABLE `client_house_transfers` DISABLE KEYS */;
/*!40000 ALTER TABLE `client_house_transfers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_problem`
--

DROP TABLE IF EXISTS `client_problem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_problem` (
  `client_id` bigint(20) unsigned NOT NULL,
  `problem_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`client_id`,`problem_id`),
  KEY `client_problem_problem_id_foreign` (`problem_id`),
  CONSTRAINT `client_problem_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `client_problem_problem_id_foreign` FOREIGN KEY (`problem_id`) REFERENCES `problems` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_problem`
--

LOCK TABLES `client_problem` WRITE;
/*!40000 ALTER TABLE `client_problem` DISABLE KEYS */;
INSERT INTO `client_problem` VALUES (206,2,'2026-07-28 13:28:45','2026-07-28 13:28:45'),(206,4,'2026-07-28 13:28:45','2026-07-28 13:28:45');
/*!40000 ALTER TABLE `client_problem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_transfers`
--

DROP TABLE IF EXISTS `client_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_transfers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `from_project_id` bigint(20) unsigned DEFAULT NULL,
  `to_project_id` bigint(20) unsigned NOT NULL,
  `transfer_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `requested_by` bigint(20) unsigned DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_transfers_client_id_status_index` (`client_id`,`status`),
  KEY `client_transfers_from_project_id_to_project_id_index` (`from_project_id`,`to_project_id`),
  CONSTRAINT `client_transfers_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_transfers`
--

LOCK TABLES `client_transfers` WRITE;
/*!40000 ALTER TABLE `client_transfers` DISABLE KEYS */;
/*!40000 ALTER TABLE `client_transfers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `register_number` varchar(255) DEFAULT NULL,
  `title_id` bigint(20) unsigned NOT NULL,
  `nick_name` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `gender` varchar(10) NOT NULL DEFAULT 'male',
  `birth_date` date DEFAULT NULL,
  `id_card` varchar(17) DEFAULT NULL,
  `national_id` bigint(20) unsigned NOT NULL,
  `religion_id` bigint(20) unsigned NOT NULL,
  `marital_id` bigint(20) unsigned NOT NULL,
  `occupation_id` bigint(20) unsigned NOT NULL,
  `income_id` bigint(20) unsigned DEFAULT NULL,
  `education_id` bigint(20) unsigned NOT NULL,
  `scholl` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `moo` varchar(255) DEFAULT NULL,
  `soi` varchar(255) DEFAULT NULL,
  `road` varchar(255) DEFAULT NULL,
  `village` varchar(255) DEFAULT NULL,
  `province_id` bigint(20) unsigned DEFAULT NULL,
  `district_id` bigint(20) unsigned DEFAULT NULL,
  `sub_district_id` bigint(20) unsigned DEFAULT NULL,
  `zipcode` int(10) unsigned DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `origin_address` varchar(255) DEFAULT NULL,
  `origin_moo` varchar(255) DEFAULT NULL,
  `origin_soi` varchar(255) DEFAULT NULL,
  `origin_road` varchar(255) DEFAULT NULL,
  `origin_village` varchar(255) DEFAULT NULL,
  `origin_province_id` bigint(20) unsigned DEFAULT NULL,
  `origin_district_id` bigint(20) unsigned DEFAULT NULL,
  `origin_sub_district_id` bigint(20) unsigned DEFAULT NULL,
  `origin_zipcode` int(10) unsigned DEFAULT NULL,
  `origin_phone` varchar(50) DEFAULT NULL,
  `arrival_date` date DEFAULT NULL,
  `target_id` bigint(20) unsigned NOT NULL,
  `contact_id` bigint(20) unsigned NOT NULL,
  `project_id` bigint(20) unsigned NOT NULL,
  `house_id` bigint(20) unsigned DEFAULT NULL,
  `status_id` bigint(20) unsigned NOT NULL,
  `case_resident` varchar(255) NOT NULL DEFAULT 'Active',
  `image` varchar(255) DEFAULT NULL,
  `release_status` varchar(255) DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=207 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (206,'REG001',2,'เมย์','นายสุชาติ','สุทธินาค','female','2021-06-30','3-2099-00523-68-8',1,1,2,1,3,16,'โรงเรียนเมืองพัทยา 6 (วัดธรรมสามัคคี)','109/53','12',NULL,'พิพิธ',NULL,16,179,1300,25110,'0869783512','109/53','12',NULL,'พิพิธ',NULL,16,179,1300,25110,'0869783512','2025-07-21',7,3,3,4,1,'Active',NULL,'show','2026-07-28 13:28:45','2026-07-28 13:31:38');
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `contact_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
INSERT INTO `contacts` VALUES (1,'ติดต่อด้วยตนเอง',NULL,NULL),(2,'ผู้ปกครองนำส่ง',NULL,NULL),(3,'พลเมืองดีนำส่ง',NULL,NULL),(4,'หน่วยงานภาครัฐนำส่ง',NULL,NULL),(5,'หน่วยงานภาคเอกชนนำส่ง',NULL,NULL),(6,'เจ้าหน้าที่ตำรวจนำส่ง',NULL,NULL),(7,'บุคลากรในหน่วยงานเข้าไปช่วยเหลือ',NULL,NULL),(8,'อื่น ๆ',NULL,NULL),(9,'ไม่มีข้อมูล',NULL,NULL);
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `depression_screening_items`
--

DROP TABLE IF EXISTS `depression_screening_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `depression_screening_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `depression_screening_id` bigint(20) unsigned NOT NULL,
  `item_no` tinyint(3) unsigned NOT NULL,
  `question` text NOT NULL,
  `score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `choice_text` varchar(255) DEFAULT NULL,
  `is_reverse` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `depression_screening_items_depression_screening_id_foreign` (`depression_screening_id`),
  CONSTRAINT `depression_screening_items_depression_screening_id_foreign` FOREIGN KEY (`depression_screening_id`) REFERENCES `depression_screenings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `depression_screening_items`
--

LOCK TABLES `depression_screening_items` WRITE;
/*!40000 ALTER TABLE `depression_screening_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `depression_screening_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `depression_screenings`
--

DROP TABLE IF EXISTS `depression_screenings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `depression_screenings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `screening_date` date NOT NULL,
  `observer_name` varchar(255) DEFAULT NULL,
  `age_text` varchar(255) DEFAULT NULL,
  `class_level` varchar(255) DEFAULT NULL,
  `total_score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `result_level` varchar(255) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `recommendation` text DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `depression_screenings_client_id_screening_date_unique` (`client_id`,`screening_date`),
  KEY `depression_screenings_created_by_foreign` (`created_by`),
  CONSTRAINT `depression_screenings_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `depression_screenings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `depression_screenings`
--

LOCK TABLES `depression_screenings` WRITE;
/*!40000 ALTER TABLE `depression_screenings` DISABLE KEYS */;
/*!40000 ALTER TABLE `depression_screenings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `districts`
--

DROP TABLE IF EXISTS `districts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `districts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dist_code` varchar(255) DEFAULT NULL,
  `province_id` bigint(20) unsigned NOT NULL,
  `dist_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `districts_province_id_foreign` (`province_id`),
  CONSTRAINT `districts_province_id_foreign` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=929 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `districts`
--

LOCK TABLES `districts` WRITE;
/*!40000 ALTER TABLE `districts` DISABLE KEYS */;
INSERT INTO `districts` VALUES (1,'1001',1,'พระนคร',NULL,NULL),(2,'1002',1,'ดุสิต',NULL,NULL),(3,'1003',1,'หนองจอก',NULL,NULL),(4,'1004',1,'บางรัก',NULL,NULL),(5,'1005',1,'บางเขน',NULL,NULL),(6,'1006',1,'บางกะปิ',NULL,NULL),(7,'1007',1,'ปทุมวัน',NULL,NULL),(8,'1008',1,'ป้อมปราบศัตรูพ่าย',NULL,NULL),(9,'1009',1,'พระโขนง',NULL,NULL),(10,'1010',1,'มีนบุรี',NULL,NULL),(11,'1011',1,'ลาดกระบัง',NULL,NULL),(12,'1012',1,'ยานนาวา',NULL,NULL),(13,'1013',1,'สัมพันธวงศ์',NULL,NULL),(14,'1014',1,'พญาไท',NULL,NULL),(15,'1015',1,'ธนบุรี',NULL,NULL),(16,'1016',1,'บางกอกใหญ่',NULL,NULL),(17,'1017',1,'ห้วยขวาง',NULL,NULL),(18,'1018',1,'คลองสาน',NULL,NULL),(19,'1019',1,'ตลิ่งชัน',NULL,NULL),(20,'1020',1,'บางกอกน้อย',NULL,NULL),(21,'1021',1,'บางขุนเทียน',NULL,NULL),(22,'1022',1,'ภาษีเจริญ',NULL,NULL),(23,'1023',1,'หนองแขม',NULL,NULL),(24,'1024',1,'ราษฎร์บูรณะ',NULL,NULL),(25,'1025',1,'บางพลัด',NULL,NULL),(26,'1026',1,'ดินแดง',NULL,NULL),(27,'1027',1,'บึงกุ่ม',NULL,NULL),(28,'1028',1,'สาทร',NULL,NULL),(29,'1029',1,'บางซื่อ',NULL,NULL),(30,'1030',1,'จตุจักร',NULL,NULL),(31,'1031',1,'บางคอแหลม',NULL,NULL),(32,'1032',1,'ประเวศ',NULL,NULL),(33,'1033',1,'คลองเตย',NULL,NULL),(34,'1034',1,'สวนหลวง',NULL,NULL),(35,'1035',1,'จอมทอง',NULL,NULL),(36,'1036',1,'ดอนเมือง',NULL,NULL),(37,'1037',1,'ราชเทวี',NULL,NULL),(38,'1038',1,'ลาดพร้าว',NULL,NULL),(39,'1039',1,'วัฒนา',NULL,NULL),(40,'1040',1,'บางแค',NULL,NULL),(41,'1041',1,'หลักสี่',NULL,NULL),(42,'1042',1,'สายไหม',NULL,NULL),(43,'1043',1,'คันนายาว',NULL,NULL),(44,'1044',1,'สะพานสูง',NULL,NULL),(45,'1045',1,'วังทองหลาง',NULL,NULL),(46,'1046',1,'คลองสามวา',NULL,NULL),(47,'1047',1,'บางนา',NULL,NULL),(48,'1048',1,'ทวีวัฒนา',NULL,NULL),(49,'1049',1,'ทุ่งครุ',NULL,NULL),(50,'1050',1,'บางบอน',NULL,NULL),(51,'1101',2,'เมืองสมุทรปราการ',NULL,NULL),(52,'1102',2,'บางบ่อ',NULL,NULL),(53,'1103',2,'บางพลี',NULL,NULL),(54,'1104',2,'พระประแดง',NULL,NULL),(55,'1105',2,'พระสมุทรเจดีย์',NULL,NULL),(56,'1106',2,'บางเสาธง',NULL,NULL),(57,'1201',3,'เมืองนนทบุรี',NULL,NULL),(58,'1202',3,'บางกรวย',NULL,NULL),(59,'1203',3,'บางใหญ่',NULL,NULL),(60,'1204',3,'บางบัวทอง',NULL,NULL),(61,'1205',3,'ไทรน้อย',NULL,NULL),(62,'1206',3,'ปากเกร็ด',NULL,NULL),(63,'1301',4,'เมืองปทุมธานี',NULL,NULL),(64,'1302',4,'คลองหลวง',NULL,NULL),(65,'1303',4,'ธัญบุรี',NULL,NULL),(66,'1304',4,'หนองเสือ',NULL,NULL),(67,'1305',4,'ลาดหลุมแก้ว',NULL,NULL),(68,'1306',4,'ลำลูกกา',NULL,NULL),(69,'1307',4,'สามโคก',NULL,NULL),(70,'1401',5,'พระนครศรีอยุธยา',NULL,NULL),(71,'1402',5,'ท่าเรือ',NULL,NULL),(72,'1403',5,'นครหลวง',NULL,NULL),(73,'1404',5,'บางไทร',NULL,NULL),(74,'1405',5,'บางบาล',NULL,NULL),(75,'1406',5,'บางปะอิน',NULL,NULL),(76,'1407',5,'บางปะหัน',NULL,NULL),(77,'1408',5,'ผักไห่',NULL,NULL),(78,'1409',5,'ภาชี',NULL,NULL),(79,'1410',5,'ลาดบัวหลวง',NULL,NULL),(80,'1411',5,'วังน้อย',NULL,NULL),(81,'1412',5,'เสนา',NULL,NULL),(82,'1413',5,'บางซ้าย',NULL,NULL),(83,'1414',5,'อุทัย',NULL,NULL),(84,'1415',5,'มหาราช',NULL,NULL),(85,'1416',5,'บ้านแพรก',NULL,NULL),(86,'1501',6,'เมืองอ่างทอง',NULL,NULL),(87,'1502',6,'ไชโย',NULL,NULL),(88,'1503',6,'ป่าโมก',NULL,NULL),(89,'1504',6,'โพธิ์ทอง',NULL,NULL),(90,'1505',6,'แสวงหา',NULL,NULL),(91,'1506',6,'วิเศษชัยชาญ',NULL,NULL),(92,'1507',6,'สามโก้',NULL,NULL),(93,'1601',7,'เมืองลพบุรี',NULL,NULL),(94,'1602',7,'พัฒนานิคม',NULL,NULL),(95,'1603',7,'โคกสำโรง',NULL,NULL),(96,'1604',7,'ชัยบาดาล',NULL,NULL),(97,'1605',7,'ท่าวุ้ง',NULL,NULL),(98,'1606',7,'บ้านหมี่',NULL,NULL),(99,'1607',7,'ท่าหลวง',NULL,NULL),(100,'1608',7,'สระโบสถ์',NULL,NULL),(101,'1609',7,'โคกเจริญ',NULL,NULL),(102,'1610',7,'ลำสนธิ',NULL,NULL),(103,'1611',7,'หนองม่วง',NULL,NULL),(104,'1701',8,'เมืองสิงห์บุรี',NULL,NULL),(105,'1702',8,'บางระจัน',NULL,NULL),(106,'1703',8,'ค่ายบางระจัน',NULL,NULL),(107,'1704',8,'พรหมบุรี',NULL,NULL),(108,'1705',8,'ท่าช้าง',NULL,NULL),(109,'1706',8,'อินทร์บุรี',NULL,NULL),(110,'1801',9,'เมืองชัยนาท',NULL,NULL),(111,'1802',9,'มโนรมย์',NULL,NULL),(112,'1803',9,'วัดสิงห์',NULL,NULL),(113,'1804',9,'สรรพยา',NULL,NULL),(114,'1805',9,'สรรคบุรี',NULL,NULL),(115,'1806',9,'หันคา',NULL,NULL),(116,'1807',9,'หนองมะโมง',NULL,NULL),(117,'1808',9,'เนินขาม',NULL,NULL),(118,'1901',10,'เมืองสระบุรี',NULL,NULL),(119,'1902',10,'แก่งคอย',NULL,NULL),(120,'1903',10,'หนองแค',NULL,NULL),(121,'1904',10,'วิหารแดง',NULL,NULL),(122,'1905',10,'หนองแซง',NULL,NULL),(123,'1906',10,'บ้านหมอ',NULL,NULL),(124,'1907',10,'ดอนพุด',NULL,NULL),(125,'1908',10,'หนองโดน',NULL,NULL),(126,'1909',10,'พระพุทธบาท',NULL,NULL),(127,'1910',10,'เสาไห้',NULL,NULL),(128,'1911',10,'มวกเหล็ก',NULL,NULL),(129,'1912',10,'วังม่วง',NULL,NULL),(130,'1913',10,'เฉลิมพระเกียรติ',NULL,NULL),(131,'2001',11,'เมืองชลบุรี',NULL,NULL),(132,'2002',11,'บ้านบึง',NULL,NULL),(133,'2003',11,'หนองใหญ่',NULL,NULL),(134,'2004',11,'บางละมุง',NULL,NULL),(135,'2005',11,'พานทอง',NULL,NULL),(136,'2006',11,'พนัสนิคม',NULL,NULL),(137,'2007',11,'ศรีราชา',NULL,NULL),(138,'2008',11,'เกาะสีชัง',NULL,NULL),(139,'2009',11,'สัตหีบ',NULL,NULL),(140,'2010',11,'บ่อทอง',NULL,NULL),(141,'2011',11,'เกาะจันทร์',NULL,NULL),(142,'2101',12,'เมืองระยอง',NULL,NULL),(143,'2102',12,'บ้านฉาง',NULL,NULL),(144,'2103',12,'แกลง',NULL,NULL),(145,'2104',12,'วังจันทร์',NULL,NULL),(146,'2105',12,'บ้านค่าย',NULL,NULL),(147,'2106',12,'ปลวกแดง',NULL,NULL),(148,'2107',12,'เขาชะเมา',NULL,NULL),(149,'2108',12,'นิคมพัฒนา',NULL,NULL),(150,'2201',13,'เมืองจันทบุรี',NULL,NULL),(151,'2202',13,'ขลุง',NULL,NULL),(152,'2203',13,'ท่าใหม่',NULL,NULL),(153,'2204',13,'โป่งน้ำร้อน',NULL,NULL),(154,'2205',13,'มะขาม',NULL,NULL),(155,'2206',13,'แหลมสิงห์',NULL,NULL),(156,'2207',13,'สอยดาว',NULL,NULL),(157,'2208',13,'แก่งหางแมว',NULL,NULL),(158,'2209',13,'นายายอาม',NULL,NULL),(159,'2210',13,'เขาคิชฌกูฏ',NULL,NULL),(160,'2301',14,'เมืองตราด',NULL,NULL),(161,'2302',14,'คลองใหญ่',NULL,NULL),(162,'2303',14,'เขาสมิง',NULL,NULL),(163,'2304',14,'บ่อไร่',NULL,NULL),(164,'2305',14,'แหลมงอบ',NULL,NULL),(165,'2306',14,'เกาะกูด',NULL,NULL),(166,'2307',14,'เกาะช้าง',NULL,NULL),(167,'2401',15,'เมืองฉะเชิงเทรา',NULL,NULL),(168,'2402',15,'บางคล้า',NULL,NULL),(169,'2403',15,'บางน้ำเปรี้ยว',NULL,NULL),(170,'2404',15,'บางปะกง',NULL,NULL),(171,'2405',15,'บ้านโพธิ์',NULL,NULL),(172,'2406',15,'พนมสารคาม',NULL,NULL),(173,'2407',15,'ราชสาส์น',NULL,NULL),(174,'2408',15,'สนามชัยเขต',NULL,NULL),(175,'2409',15,'แปลงยาว',NULL,NULL),(176,'2410',15,'ท่าตะเกียบ',NULL,NULL),(177,'2411',15,'คลองเขื่อน',NULL,NULL),(178,'2501',16,'เมืองปราจีนบุรี',NULL,NULL),(179,'2502',16,'กบินทร์บุรี',NULL,NULL),(180,'2503',16,'นาดี',NULL,NULL),(181,'2506',16,'บ้านสร้าง',NULL,NULL),(182,'2507',16,'ประจันตคาม',NULL,NULL),(183,'2508',16,'ศรีมหาโพธิ',NULL,NULL),(184,'2509',16,'ศรีมโหสถ',NULL,NULL),(185,'2601',17,'เมืองนครนายก',NULL,NULL),(186,'2602',17,'ปากพลี',NULL,NULL),(187,'2603',17,'บ้านนา',NULL,NULL),(188,'2604',17,'องครักษ์',NULL,NULL),(189,'2701',18,'เมืองสระแก้ว',NULL,NULL),(190,'2702',18,'คลองหาด',NULL,NULL),(191,'2703',18,'ตาพระยา',NULL,NULL),(192,'2704',18,'วังน้ำเย็น',NULL,NULL),(193,'2705',18,'วัฒนานคร',NULL,NULL),(194,'2706',18,'อรัญประเทศ',NULL,NULL),(195,'2707',18,'เขาฉกรรจ์',NULL,NULL),(196,'2708',18,'โคกสูง',NULL,NULL),(197,'2709',18,'วังสมบูรณ์',NULL,NULL),(198,'3001',19,'เมืองนครราชสีมา',NULL,NULL),(199,'3002',19,'ครบุรี',NULL,NULL),(200,'3003',19,'เสิงสาง',NULL,NULL),(201,'3004',19,'คง',NULL,NULL),(202,'3005',19,'บ้านเหลื่อม',NULL,NULL),(203,'3006',19,'จักราช',NULL,NULL),(204,'3007',19,'โชคชัย',NULL,NULL),(205,'3008',19,'ด่านขุนทด',NULL,NULL),(206,'3009',19,'โนนไทย',NULL,NULL),(207,'3010',19,'โนนสูง',NULL,NULL),(208,'3011',19,'ขามสะแกแสง',NULL,NULL),(209,'3012',19,'บัวใหญ่',NULL,NULL),(210,'3013',19,'ประทาย',NULL,NULL),(211,'3014',19,'ปักธงชัย',NULL,NULL),(212,'3015',19,'พิมาย',NULL,NULL),(213,'3016',19,'ห้วยแถลง',NULL,NULL),(214,'3017',19,'ชุมพวง',NULL,NULL),(215,'3018',19,'สูงเนิน',NULL,NULL),(216,'3019',19,'ขามทะเลสอ',NULL,NULL),(217,'3020',19,'สีคิ้ว',NULL,NULL),(218,'3021',19,'ปากช่อง',NULL,NULL),(219,'3022',19,'หนองบุญมาก',NULL,NULL),(220,'3023',19,'แก้งสนามนาง',NULL,NULL),(221,'3024',19,'โนนแดง',NULL,NULL),(222,'3025',19,'วังน้ำเขียว',NULL,NULL),(223,'3026',19,'เทพารักษ์',NULL,NULL),(224,'3027',19,'เมืองยาง',NULL,NULL),(225,'3028',19,'พระทองคำ',NULL,NULL),(226,'3029',19,'ลำทะเมนชัย',NULL,NULL),(227,'3030',19,'บัวลาย',NULL,NULL),(228,'3031',19,'สีดา',NULL,NULL),(229,'3032',19,'เฉลิมพระเกียรติ',NULL,NULL),(230,'3101',20,'เมืองบุรีรัมย์',NULL,NULL),(231,'3102',20,'คูเมือง',NULL,NULL),(232,'3103',20,'กระสัง',NULL,NULL),(233,'3104',20,'นางรอง',NULL,NULL),(234,'3105',20,'หนองกี่',NULL,NULL),(235,'3106',20,'ละหานทราย',NULL,NULL),(236,'3107',20,'ประโคนชัย',NULL,NULL),(237,'3108',20,'บ้านกรวด',NULL,NULL),(238,'3109',20,'พุทไธสง',NULL,NULL),(239,'3110',20,'ลำปลายมาศ',NULL,NULL),(240,'3111',20,'สตึก',NULL,NULL),(241,'3112',20,'ปะคำ',NULL,NULL),(242,'3113',20,'นาโพธิ์',NULL,NULL),(243,'3114',20,'หนองหงส์',NULL,NULL),(244,'3115',20,'พลับพลาชัย',NULL,NULL),(245,'3116',20,'ห้วยราช',NULL,NULL),(246,'3117',20,'โนนสุวรรณ',NULL,NULL),(247,'3118',20,'ชำนิ',NULL,NULL),(248,'3119',20,'บ้านใหม่ไชยพจน์',NULL,NULL),(249,'3120',20,'โนนดินแดง',NULL,NULL),(250,'3121',20,'บ้านด่าน',NULL,NULL),(251,'3122',20,'แคนดง',NULL,NULL),(252,'3123',20,'เฉลิมพระเกียรติ',NULL,NULL),(253,'3201',21,'เมืองสุรินทร์',NULL,NULL),(254,'3202',21,'ชุมพลบุรี',NULL,NULL),(255,'3203',21,'ท่าตูม',NULL,NULL),(256,'3204',21,'จอมพระ',NULL,NULL),(257,'3205',21,'ปราสาท',NULL,NULL),(258,'3206',21,'กาบเชิง',NULL,NULL),(259,'3207',21,'รัตนบุรี',NULL,NULL),(260,'3208',21,'สนม',NULL,NULL),(261,'3209',21,'ศีขรภูมิ',NULL,NULL),(262,'3210',21,'สังขะ',NULL,NULL),(263,'3211',21,'ลำดวน',NULL,NULL),(264,'3212',21,'สำโรงทาบ',NULL,NULL),(265,'3213',21,'บัวเชด',NULL,NULL),(266,'3214',21,'พนมดงรัก',NULL,NULL),(267,'3215',21,'ศรีณรงค์',NULL,NULL),(268,'3216',21,'เขวาสินรินทร์',NULL,NULL),(269,'3217',21,'โนนนารายณ์',NULL,NULL),(270,'3301',22,'เมืองศรีสะเกษ',NULL,NULL),(271,'3302',22,'ยางชุมน้อย',NULL,NULL),(272,'3303',22,'กันทรารมย์',NULL,NULL),(273,'3304',22,'กันทรลักษ์',NULL,NULL),(274,'3305',22,'ขุขันธ์',NULL,NULL),(275,'3306',22,'ไพรบึง',NULL,NULL),(276,'3307',22,'ปรางค์กู่',NULL,NULL),(277,'3308',22,'ขุนหาญ',NULL,NULL),(278,'3309',22,'ราษีไศล',NULL,NULL),(279,'3310',22,'อุทุมพรพิสัย',NULL,NULL),(280,'3311',22,'บึงบูรพ์',NULL,NULL),(281,'3312',22,'ห้วยทับทัน',NULL,NULL),(282,'3313',22,'โนนคูณ',NULL,NULL),(283,'3314',22,'ศรีรัตนะ',NULL,NULL),(284,'3315',22,'น้ำเกลี้ยง',NULL,NULL),(285,'3316',22,'วังหิน',NULL,NULL),(286,'3317',22,'ภูสิงห์',NULL,NULL),(287,'3318',22,'เมืองจันทร์',NULL,NULL),(288,'3319',22,'เบญจลักษ์',NULL,NULL),(289,'3320',22,'พยุห์',NULL,NULL),(290,'3321',22,'โพธิ์ศรีสุวรรณ',NULL,NULL),(291,'3322',22,'ศิลาลาด',NULL,NULL),(292,'3401',23,'เมืองอุบลราชธานี',NULL,NULL),(293,'3402',23,'ศรีเมืองใหม่',NULL,NULL),(294,'3403',23,'โขงเจียม',NULL,NULL),(295,'3404',23,'เขื่องใน',NULL,NULL),(296,'3405',23,'เขมราฐ',NULL,NULL),(297,'3407',23,'เดชอุดม',NULL,NULL),(298,'3408',23,'นาจะหลวย',NULL,NULL),(299,'3409',23,'น้ำยืน',NULL,NULL),(300,'3410',23,'บุณฑริก',NULL,NULL),(301,'3411',23,'ตระการพืชผล',NULL,NULL),(302,'3412',23,'กุดข้าวปุ้น',NULL,NULL),(303,'3414',23,'ม่วงสามสิบ',NULL,NULL),(304,'3415',23,'วารินชำราบ',NULL,NULL),(305,'3419',23,'พิบูลมังสาหาร',NULL,NULL),(306,'3420',23,'ตาลสุม',NULL,NULL),(307,'3421',23,'โพธิ์ไทร',NULL,NULL),(308,'3422',23,'สำโรง',NULL,NULL),(309,'3424',23,'ดอนมดแดง',NULL,NULL),(310,'3425',23,'สิรินธร',NULL,NULL),(311,'3426',23,'ทุ่งศรีอุดม',NULL,NULL),(312,'3429',23,'นาเยีย',NULL,NULL),(313,'3430',23,'นาตาล',NULL,NULL),(314,'3431',23,'เหล่าเสือโก้ก',NULL,NULL),(315,'3432',23,'สว่างวีระวงศ์',NULL,NULL),(316,'3433',23,'น้ำขุ่น',NULL,NULL),(317,'3501',24,'เมืองยโสธร',NULL,NULL),(318,'3502',24,'ทรายมูล',NULL,NULL),(319,'3503',24,'กุดชุม',NULL,NULL),(320,'3504',24,'คำเขื่อนแก้ว',NULL,NULL),(321,'3505',24,'ป่าติ้ว',NULL,NULL),(322,'3506',24,'มหาชนะชัย',NULL,NULL),(323,'3507',24,'ค้อวัง',NULL,NULL),(324,'3508',24,'เลิงนกทา',NULL,NULL),(325,'3509',24,'ไทยเจริญ',NULL,NULL),(326,'3601',25,'เมืองชัยภูมิ',NULL,NULL),(327,'3602',25,'บ้านเขว้า',NULL,NULL),(328,'3603',25,'คอนสวรรค์',NULL,NULL),(329,'3604',25,'เกษตรสมบูรณ์',NULL,NULL),(330,'3605',25,'หนองบัวแดง',NULL,NULL),(331,'3606',25,'จัตุรัส',NULL,NULL),(332,'3607',25,'บำเหน็จณรงค์',NULL,NULL),(333,'3608',25,'หนองบัวระเหว',NULL,NULL),(334,'3609',25,'เทพสถิต',NULL,NULL),(335,'3610',25,'ภูเขียว',NULL,NULL),(336,'3611',25,'บ้านแท่น',NULL,NULL),(337,'3612',25,'แก้งคร้อ',NULL,NULL),(338,'3613',25,'คอนสาร',NULL,NULL),(339,'3614',25,'ภักดีชุมพล',NULL,NULL),(340,'3615',25,'เนินสง่า',NULL,NULL),(341,'3616',25,'ซับใหญ่',NULL,NULL),(342,'3701',26,'เมืองอำนาจเจริญ',NULL,NULL),(343,'3702',26,'ชานุมาน',NULL,NULL),(344,'3703',26,'ปทุมราชวงศา',NULL,NULL),(345,'3704',26,'พนา',NULL,NULL),(346,'3705',26,'เสนางคนิคม',NULL,NULL),(347,'3706',26,'หัวตะพาน',NULL,NULL),(348,'3707',26,'ลืออำนาจ',NULL,NULL),(349,'3801',27,'บึงกาฬ',NULL,NULL),(350,'3802',27,'พรเจริญ',NULL,NULL),(351,'3803',27,'โซ่พิสัย',NULL,NULL),(352,'3804',27,'เซกา',NULL,NULL),(353,'3805',27,'ปากคาด',NULL,NULL),(354,'3806',27,'บึงโขงหลง',NULL,NULL),(355,'3807',27,'ศรีวิไล',NULL,NULL),(356,'3808',27,'บุ่งคล้า',NULL,NULL),(357,'3901',28,'เมืองหนองบัวลำภู',NULL,NULL),(358,'3902',28,'นากลาง',NULL,NULL),(359,'3903',28,'โนนสัง',NULL,NULL),(360,'3904',28,'ศรีบุญเรือง',NULL,NULL),(361,'3905',28,'สุวรรณคูหา',NULL,NULL),(362,'3906',28,'นาวัง',NULL,NULL),(363,'4001',29,'เมืองขอนแก่น',NULL,NULL),(364,'4002',29,'บ้านฝาง',NULL,NULL),(365,'4003',29,'พระยืน',NULL,NULL),(366,'4004',29,'หนองเรือ',NULL,NULL),(367,'4005',29,'ชุมแพ',NULL,NULL),(368,'4006',29,'สีชมพู',NULL,NULL),(369,'4007',29,'น้ำพอง',NULL,NULL),(370,'4008',29,'อุบลรัตน์',NULL,NULL),(371,'4009',29,'กระนวน',NULL,NULL),(372,'4010',29,'บ้านไผ่',NULL,NULL),(373,'4011',29,'เปือยน้อย',NULL,NULL),(374,'4012',29,'พล',NULL,NULL),(375,'4013',29,'แวงใหญ่',NULL,NULL),(376,'4014',29,'แวงน้อย',NULL,NULL),(377,'4015',29,'หนองสองห้อง',NULL,NULL),(378,'4016',29,'ภูเวียง',NULL,NULL),(379,'4017',29,'มัญจาคีรี',NULL,NULL),(380,'4018',29,'ชนบท',NULL,NULL),(381,'4019',29,'เขาสวนกวาง',NULL,NULL),(382,'4020',29,'ภูผาม่าน',NULL,NULL),(383,'4021',29,'ซำสูง',NULL,NULL),(384,'4022',29,'โคกโพธิ์ไชย',NULL,NULL),(385,'4023',29,'หนองนาคำ',NULL,NULL),(386,'4024',29,'บ้านแฮด',NULL,NULL),(387,'4025',29,'โนนศิลา',NULL,NULL),(388,'4026',29,'เวียงเก่า',NULL,NULL),(389,'4101',30,'เมืองอุดรธานี',NULL,NULL),(390,'4102',30,'กุดจับ',NULL,NULL),(391,'4103',30,'หนองวัวซอ',NULL,NULL),(392,'4104',30,'กุมภวาปี',NULL,NULL),(393,'4105',30,'โนนสะอาด',NULL,NULL),(394,'4106',30,'หนองหาน',NULL,NULL),(395,'4107',30,'ทุ่งฝน',NULL,NULL),(396,'4108',30,'ไชยวาน',NULL,NULL),(397,'4109',30,'ศรีธาตุ',NULL,NULL),(398,'4110',30,'วังสามหมอ',NULL,NULL),(399,'4111',30,'บ้านดุง',NULL,NULL),(400,'4117',30,'บ้านผือ',NULL,NULL),(401,'4118',30,'น้ำโสม',NULL,NULL),(402,'4119',30,'เพ็ญ',NULL,NULL),(403,'4120',30,'สร้างคอม',NULL,NULL),(404,'4121',30,'หนองแสง',NULL,NULL),(405,'4122',30,'นายูง',NULL,NULL),(406,'4123',30,'พิบูลย์รักษ์',NULL,NULL),(407,'4124',30,'กู่แก้ว',NULL,NULL),(408,'4125',30,'ประจักษ์ศิลปาคม',NULL,NULL),(409,'4201',31,'เมืองเลย',NULL,NULL),(410,'4202',31,'นาด้วง',NULL,NULL),(411,'4203',31,'เชียงคาน',NULL,NULL),(412,'4204',31,'ปากชม',NULL,NULL),(413,'4205',31,'ด่านซ้าย',NULL,NULL),(414,'4206',31,'นาแห้ว',NULL,NULL),(415,'4207',31,'ภูเรือ',NULL,NULL),(416,'4208',31,'ท่าลี่',NULL,NULL),(417,'4209',31,'วังสะพุง',NULL,NULL),(418,'4210',31,'ภูกระดึง',NULL,NULL),(419,'4211',31,'ภูหลวง',NULL,NULL),(420,'4212',31,'ผาขาว',NULL,NULL),(421,'4213',31,'เอราวัณ',NULL,NULL),(422,'4214',31,'หนองหิน',NULL,NULL),(423,'4301',32,'เมืองหนองคาย',NULL,NULL),(424,'4302',32,'ท่าบ่อ',NULL,NULL),(425,'4305',32,'โพนพิสัย',NULL,NULL),(426,'4307',32,'ศรีเชียงใหม่',NULL,NULL),(427,'4308',32,'สังคม',NULL,NULL),(428,'4314',32,'สระใคร',NULL,NULL),(429,'4315',32,'เฝ้าไร่',NULL,NULL),(430,'4316',32,'รัตนวาปี',NULL,NULL),(431,'4317',32,'โพธิ์ตาก',NULL,NULL),(432,'4401',33,'เมืองมหาสารคาม',NULL,NULL),(433,'4402',33,'แกดำ',NULL,NULL),(434,'4403',33,'โกสุมพิสัย',NULL,NULL),(435,'4404',33,'กันทรวิชัย',NULL,NULL),(436,'4405',33,'เชียงยืน',NULL,NULL),(437,'4406',33,'บรบือ',NULL,NULL),(438,'4407',33,'นาเชือก',NULL,NULL),(439,'4408',33,'พยัคฆภูมิพิสัย',NULL,NULL),(440,'4409',33,'วาปีปทุม',NULL,NULL),(441,'4410',33,'นาดูน',NULL,NULL),(442,'4411',33,'ยางสีสุราช',NULL,NULL),(443,'4412',33,'กุดรัง',NULL,NULL),(444,'4413',33,'ชื่นชม',NULL,NULL),(445,'4501',34,'เมืองร้อยเอ็ด',NULL,NULL),(446,'4502',34,'เกษตรวิสัย',NULL,NULL),(447,'4503',34,'ปทุมรัตต์',NULL,NULL),(448,'4504',34,'จตุรพักตรพิมาน',NULL,NULL),(449,'4505',34,'ธวัชบุรี',NULL,NULL),(450,'4506',34,'พนมไพร',NULL,NULL),(451,'4507',34,'โพนทอง',NULL,NULL),(452,'4508',34,'โพธิ์ชัย',NULL,NULL),(453,'4509',34,'หนองพอก',NULL,NULL),(454,'4510',34,'เสลภูมิ',NULL,NULL),(455,'4511',34,'สุวรรณภูมิ',NULL,NULL),(456,'4512',34,'เมืองสรวง',NULL,NULL),(457,'4513',34,'โพนทราย',NULL,NULL),(458,'4514',34,'อาจสามารถ',NULL,NULL),(459,'4515',34,'เมยวดี',NULL,NULL),(460,'4516',34,'ศรีสมเด็จ',NULL,NULL),(461,'4517',34,'จังหาร',NULL,NULL),(462,'4518',34,'เชียงขวัญ',NULL,NULL),(463,'4519',34,'หนองฮี',NULL,NULL),(464,'4520',34,'ทุ่งเขาหลวง',NULL,NULL),(465,'4601',35,'เมืองกาฬสินธุ์',NULL,NULL),(466,'4602',35,'นามน',NULL,NULL),(467,'4603',35,'กมลาไสย',NULL,NULL),(468,'4604',35,'ร่องคำ',NULL,NULL),(469,'4605',35,'กุฉินารายณ์',NULL,NULL),(470,'4606',35,'เขาวง',NULL,NULL),(471,'4607',35,'ยางตลาด',NULL,NULL),(472,'4608',35,'ห้วยเม็ก',NULL,NULL),(473,'4609',35,'สหัสขันธ์',NULL,NULL),(474,'4610',35,'คำม่วง',NULL,NULL),(475,'4611',35,'ท่าคันโท',NULL,NULL),(476,'4612',35,'หนองกุงศรี',NULL,NULL),(477,'4613',35,'สมเด็จ',NULL,NULL),(478,'4614',35,'ห้วยผึ้ง',NULL,NULL),(479,'4615',35,'สามชัย',NULL,NULL),(480,'4616',35,'นาคู',NULL,NULL),(481,'4617',35,'ดอนจาน',NULL,NULL),(482,'4618',35,'ฆ้องชัย',NULL,NULL),(483,'4701',36,'เมืองสกลนคร',NULL,NULL),(484,'4702',36,'กุสุมาลย์',NULL,NULL),(485,'4703',36,'กุดบาก',NULL,NULL),(486,'4704',36,'พรรณานิคม',NULL,NULL),(487,'4705',36,'พังโคน',NULL,NULL),(488,'4706',36,'วาริชภูมิ',NULL,NULL),(489,'4707',36,'นิคมน้ำอูน',NULL,NULL),(490,'4708',36,'วานรนิวาส',NULL,NULL),(491,'4709',36,'คำตากล้า',NULL,NULL),(492,'4710',36,'บ้านม่วง',NULL,NULL),(493,'4711',36,'อากาศอำนวย',NULL,NULL),(494,'4712',36,'สว่างแดนดิน',NULL,NULL),(495,'4713',36,'ส่องดาว',NULL,NULL),(496,'4714',36,'เต่างอย',NULL,NULL),(497,'4715',36,'โคกศรีสุพรรณ',NULL,NULL),(498,'4716',36,'เจริญศิลป์',NULL,NULL),(499,'4717',36,'โพนนาแก้ว',NULL,NULL),(500,'4718',36,'ภูพาน',NULL,NULL),(501,'4801',37,'เมืองนครพนม',NULL,NULL),(502,'4802',37,'ปลาปาก',NULL,NULL),(503,'4803',37,'ท่าอุเทน',NULL,NULL),(504,'4804',37,'บ้านแพง',NULL,NULL),(505,'4805',37,'ธาตุพนม',NULL,NULL),(506,'4806',37,'เรณูนคร',NULL,NULL),(507,'4807',37,'นาแก',NULL,NULL),(508,'4808',37,'ศรีสงคราม',NULL,NULL),(509,'4809',37,'นาหว้า',NULL,NULL),(510,'4810',37,'โพนสวรรค์',NULL,NULL),(511,'4811',37,'นาทม',NULL,NULL),(512,'4812',37,'วังยาง',NULL,NULL),(513,'4901',38,'เมืองมุกดาหาร',NULL,NULL),(514,'4902',38,'นิคมคำสร้อย',NULL,NULL),(515,'4903',38,'ดอนตาล',NULL,NULL),(516,'4904',38,'ดงหลวง',NULL,NULL),(517,'4905',38,'คำชะอี',NULL,NULL),(518,'4906',38,'ว่านใหญ่',NULL,NULL),(519,'4907',38,'หนองสูง',NULL,NULL),(520,'5001',39,'เมืองเชียงใหม่',NULL,NULL),(521,'5002',39,'จอมทอง',NULL,NULL),(522,'5003',39,'แม่แจ่ม',NULL,NULL),(523,'5004',39,'เชียงดาว',NULL,NULL),(524,'5005',39,'ดอยสะเก็ด',NULL,NULL),(525,'5006',39,'แม่แตง',NULL,NULL),(526,'5007',39,'แม่ริม',NULL,NULL),(527,'5008',39,'สะเมิง',NULL,NULL),(528,'5009',39,'ฝาง',NULL,NULL),(529,'5010',39,'แม่อาย',NULL,NULL),(530,'5011',39,'พร้าว',NULL,NULL),(531,'5012',39,'สันป่าตอง',NULL,NULL),(532,'5013',39,'สันกำแพง',NULL,NULL),(533,'5014',39,'สันทราย',NULL,NULL),(534,'5015',39,'หางดง',NULL,NULL),(535,'5016',39,'ฮอด',NULL,NULL),(536,'5017',39,'ดอยเต่า',NULL,NULL),(537,'5018',39,'อมก๋อย',NULL,NULL),(538,'5019',39,'สารภี',NULL,NULL),(539,'5020',39,'เวียงแหง',NULL,NULL),(540,'5021',39,'ไชยปราการ',NULL,NULL),(541,'5022',39,'แม่วาง',NULL,NULL),(542,'5023',39,'แม่ออน',NULL,NULL),(543,'5024',39,'ดอยหล่อ',NULL,NULL),(544,'5025',39,'กัลยาณิวัฒนา',NULL,NULL),(545,'5101',40,'เมืองลำพูน',NULL,NULL),(546,'5102',40,'แม่ทา',NULL,NULL),(547,'5103',40,'บ้านโฮ่ง',NULL,NULL),(548,'5104',40,'ลี้',NULL,NULL),(549,'5105',40,'ทุ่งหัวช้าง',NULL,NULL),(550,'5106',40,'ป่าซาง',NULL,NULL),(551,'5107',40,'บ้านธิ',NULL,NULL),(552,'5108',40,'เวียงหนองล่อง',NULL,NULL),(553,'5201',41,'เมืองลำปาง',NULL,NULL),(554,'5202',41,'แม่เมาะ',NULL,NULL),(555,'5203',41,'เกาะคา',NULL,NULL),(556,'5204',41,'เสริมงาม',NULL,NULL),(557,'5205',41,'งาว',NULL,NULL),(558,'5206',41,'แจ้ห่ม',NULL,NULL),(559,'5207',41,'วังเหนือ',NULL,NULL),(560,'5208',41,'เถิน',NULL,NULL),(561,'5209',41,'แม่พริก',NULL,NULL),(562,'5210',41,'แม่ทะ',NULL,NULL),(563,'5211',41,'สบปราบ',NULL,NULL),(564,'5212',41,'ห้างฉัตร',NULL,NULL),(565,'5213',41,'เมืองปาน',NULL,NULL),(566,'5301',42,'เมืองอุตรดิตถ์',NULL,NULL),(567,'5302',42,'ตรอน',NULL,NULL),(568,'5303',42,'ท่าปลา',NULL,NULL),(569,'5304',42,'น้ำปาด',NULL,NULL),(570,'5305',42,'ฟากท่า',NULL,NULL),(571,'5306',42,'บ้านโคก',NULL,NULL),(572,'5307',42,'พิชัย',NULL,NULL),(573,'5308',42,'ลับแล',NULL,NULL),(574,'5309',42,'ทองแสนขัน',NULL,NULL),(575,'5401',43,'เมืองแพร่',NULL,NULL),(576,'5402',43,'ร้องกวาง',NULL,NULL),(577,'5403',43,'ลอง',NULL,NULL),(578,'5404',43,'สูงเม่น',NULL,NULL),(579,'5405',43,'เด่นชัย',NULL,NULL),(580,'5406',43,'สอง',NULL,NULL),(581,'5407',43,'วังชิ้น',NULL,NULL),(582,'5408',43,'หนองม่วงไข่',NULL,NULL),(583,'5501',44,'เมืองน่าน',NULL,NULL),(584,'5502',44,'แม่จริม',NULL,NULL),(585,'5503',44,'บ้านหลวง',NULL,NULL),(586,'5504',44,'นาน้อย',NULL,NULL),(587,'5505',44,'ปัว',NULL,NULL),(588,'5506',44,'ท่าวังผา',NULL,NULL),(589,'5507',44,'เวียงสา',NULL,NULL),(590,'5508',44,'ทุ่งช้าง',NULL,NULL),(591,'5509',44,'เชียงกลาง',NULL,NULL),(592,'5510',44,'นาหมื่น',NULL,NULL),(593,'5511',44,'สันติสุข',NULL,NULL),(594,'5512',44,'บ่อเกลือ',NULL,NULL),(595,'5513',44,'สองแคว',NULL,NULL),(596,'5514',44,'ภูเพียง',NULL,NULL),(597,'5515',44,'เฉลิมพระเกียรติ',NULL,NULL),(598,'5601',45,'เมืองพะเยา',NULL,NULL),(599,'5602',45,'จุน',NULL,NULL),(600,'5603',45,'เชียงคำ',NULL,NULL),(601,'5604',45,'เชียงม่วน',NULL,NULL),(602,'5605',45,'ดอกคำใต้',NULL,NULL),(603,'5606',45,'ปง',NULL,NULL),(604,'5607',45,'แม่ใจ',NULL,NULL),(605,'5608',45,'ภูซาง',NULL,NULL),(606,'5609',45,'ภูกามยาว',NULL,NULL),(607,'5701',46,'เมืองเชียงราย',NULL,NULL),(608,'5702',46,'เวียงชัย',NULL,NULL),(609,'5703',46,'เชียงของ',NULL,NULL),(610,'5704',46,'เทิง',NULL,NULL),(611,'5705',46,'พาน',NULL,NULL),(612,'5706',46,'ป่าแดด',NULL,NULL),(613,'5707',46,'แม่จัน',NULL,NULL),(614,'5708',46,'เชียงแสน',NULL,NULL),(615,'5709',46,'แม่สาย',NULL,NULL),(616,'5710',46,'แม่สรวย',NULL,NULL),(617,'5711',46,'เวียงป่าเป้า',NULL,NULL),(618,'5712',46,'พญาเม็งราย',NULL,NULL),(619,'5713',46,'เวียงแก่น',NULL,NULL),(620,'5714',46,'ขุนตาล',NULL,NULL),(621,'5715',46,'แม่ฟ้าหลวง',NULL,NULL),(622,'5716',46,'แม่ลาว',NULL,NULL),(623,'5717',46,'เวียงเชียงรุ้ง',NULL,NULL),(624,'5718',46,'ดอยหลวง',NULL,NULL),(625,'5801',47,'เมืองแม่ฮ่องสอน',NULL,NULL),(626,'5802',47,'ขุนยวม',NULL,NULL),(627,'5803',47,'ปาย',NULL,NULL),(628,'5804',47,'แม่สะเรียง',NULL,NULL),(629,'5805',47,'แม่ลาน้อย',NULL,NULL),(630,'5806',47,'สบเมย',NULL,NULL),(631,'5807',47,'ปางมะผ้า',NULL,NULL),(632,'6001',48,'เมืองนครสวรรค์',NULL,NULL),(633,'6002',48,'โกรกพระ',NULL,NULL),(634,'6003',48,'ชุมแสง',NULL,NULL),(635,'6004',48,'หนองบัว',NULL,NULL),(636,'6005',48,'บรรพตพิสัย',NULL,NULL),(637,'6006',48,'เก้าเลี้ยว',NULL,NULL),(638,'6007',48,'ตาคลี',NULL,NULL),(639,'6008',48,'ท่าตะโก',NULL,NULL),(640,'6009',48,'ไพศาลี',NULL,NULL),(641,'6010',48,'พยุหะคีรี',NULL,NULL),(642,'6011',48,'ลาดยาว',NULL,NULL),(643,'6012',48,'ตากฟ้า',NULL,NULL),(644,'6013',48,'แม่วงก์',NULL,NULL),(645,'6014',48,'แม่เปิน',NULL,NULL),(646,'6015',48,'ชุมตาบง',NULL,NULL),(647,'6101',49,'เมืองอุทัยธานี',NULL,NULL),(648,'6102',49,'ทัพทัน',NULL,NULL),(649,'6103',49,'สว่างอารมณ์',NULL,NULL),(650,'6104',49,'หนองฉาง',NULL,NULL),(651,'6105',49,'หนองขาหย่าง',NULL,NULL),(652,'6106',49,'บ้านไร่',NULL,NULL),(653,'6107',49,'ลานสัก',NULL,NULL),(654,'6108',49,'ห้วยคต',NULL,NULL),(655,'6201',50,'เมืองกำแพงเพชร',NULL,NULL),(656,'6202',50,'ไทรงาม',NULL,NULL),(657,'6203',50,'คลองลาน',NULL,NULL),(658,'6204',50,'ขาณุวรลักษบุรี',NULL,NULL),(659,'6205',50,'คลองขลุง',NULL,NULL),(660,'6206',50,'พรานกระต่าย',NULL,NULL),(661,'6207',50,'ลานกระบือ',NULL,NULL),(662,'6208',50,'ทรายทองวัฒนา',NULL,NULL),(663,'6209',50,'ปางศิลาทอง',NULL,NULL),(664,'6210',50,'บึงสามัคคี',NULL,NULL),(665,'6211',50,'โกสัมพีนคร',NULL,NULL),(666,'6301',51,'เมืองตาก',NULL,NULL),(667,'6302',51,'บ้านตาก',NULL,NULL),(668,'6303',51,'สามเงา',NULL,NULL),(669,'6304',51,'แม่ระมาด',NULL,NULL),(670,'6305',51,'ท่าสองยาง',NULL,NULL),(671,'6306',51,'แม่สอด',NULL,NULL),(672,'6307',51,'พบพระ',NULL,NULL),(673,'6308',51,'อุ้มผาง',NULL,NULL),(674,'6309',51,'วังเจ้า',NULL,NULL),(675,'6401',52,'เมืองสุโขทัย',NULL,NULL),(676,'6402',52,'บ้านด่านลานหอย',NULL,NULL),(677,'6403',52,'คีรีมาศ',NULL,NULL),(678,'6404',52,'กงไกรลาศ',NULL,NULL),(679,'6405',52,'ศรีสัชนาลัย',NULL,NULL),(680,'6406',52,'ศรีสำโรง',NULL,NULL),(681,'6407',52,'สวรรคโลก',NULL,NULL),(682,'6408',52,'ศรีนคร',NULL,NULL),(683,'6409',52,'ทุ่งเสลี่ยม',NULL,NULL),(684,'6501',53,'เมืองพิษณุโลก',NULL,NULL),(685,'6502',53,'นครไทย',NULL,NULL),(686,'6503',53,'ชาติตระการ',NULL,NULL),(687,'6504',53,'บางระกำ',NULL,NULL),(688,'6505',53,'บางกระทุ่ม',NULL,NULL),(689,'6506',53,'พรหมพิราม',NULL,NULL),(690,'6507',53,'วัดโบสถ์',NULL,NULL),(691,'6508',53,'วังทอง',NULL,NULL),(692,'6509',53,'เนินมะปราง',NULL,NULL),(693,'6601',54,'เมืองพิจิตร',NULL,NULL),(694,'6602',54,'วังทรายพูน',NULL,NULL),(695,'6603',54,'โพธิ์ประทับช้าง',NULL,NULL),(696,'6604',54,'ตะพานหิน',NULL,NULL),(697,'6605',54,'บางมูลนาก',NULL,NULL),(698,'6606',54,'โพทะเล',NULL,NULL),(699,'6607',54,'สามง่าม',NULL,NULL),(700,'6608',54,'ทับคล้อ',NULL,NULL),(701,'6609',54,'สากเหล็ก',NULL,NULL),(702,'6610',54,'บึงนาราง',NULL,NULL),(703,'6611',54,'ดงเจริญ',NULL,NULL),(704,'6612',54,'วชิรบารมี',NULL,NULL),(705,'6701',55,'เมืองเพชรบูรณ์',NULL,NULL),(706,'6702',55,'ชนแดน',NULL,NULL),(707,'6703',55,'หล่มสัก',NULL,NULL),(708,'6704',55,'หล่มเก่า',NULL,NULL),(709,'6705',55,'วิเชียรบุรี',NULL,NULL),(710,'6706',55,'ศรีเทพ',NULL,NULL),(711,'6707',55,'หนองไผ่',NULL,NULL),(712,'6708',55,'บึงสามพัน',NULL,NULL),(713,'6709',55,'น้ำหนาว',NULL,NULL),(714,'6710',55,'วังโป่ง',NULL,NULL),(715,'6711',55,'เขาค้อ',NULL,NULL),(716,'7001',56,'เมืองราชบุรี',NULL,NULL),(717,'7002',56,'จอมบึง',NULL,NULL),(718,'7003',56,'สวนผึ้ง',NULL,NULL),(719,'7004',56,'ดำเนินสะดวก',NULL,NULL),(720,'7005',56,'บ้านโป่ง',NULL,NULL),(721,'7006',56,'บางแพ',NULL,NULL),(722,'7007',56,'โพธาราม',NULL,NULL),(723,'7008',56,'ปากท่อ',NULL,NULL),(724,'7009',56,'วัดเพลง',NULL,NULL),(725,'7010',56,'บ้านคา',NULL,NULL),(726,'7101',57,'เมืองกาญจนบุรี',NULL,NULL),(727,'7102',57,'ไทรโยค',NULL,NULL),(728,'7103',57,'บ่อพลอย',NULL,NULL),(729,'7104',57,'ศรีสวัสดิ์',NULL,NULL),(730,'7105',57,'ท่ามะกา',NULL,NULL),(731,'7106',57,'ท่าม่วง',NULL,NULL),(732,'7107',57,'ทองผาภูมิ',NULL,NULL),(733,'7108',57,'สังขละบุรี',NULL,NULL),(734,'7109',57,'พนมทวน',NULL,NULL),(735,'7110',57,'เลาขวัญ',NULL,NULL),(736,'7111',57,'ด่านมะขามเตี้ย',NULL,NULL),(737,'7112',57,'หนองปรือ',NULL,NULL),(738,'7113',57,'ห้วยกระเจา',NULL,NULL),(739,'7201',58,'เมืองสุพรรณบุรี',NULL,NULL),(740,'7202',58,'เดิมบางนางบวช',NULL,NULL),(741,'7203',58,'ด่านช้าง',NULL,NULL),(742,'7204',58,'บางปลาม้า',NULL,NULL),(743,'7205',58,'ศรีประจันต์',NULL,NULL),(744,'7206',58,'ดอนเจดีย์',NULL,NULL),(745,'7207',58,'สองพี่น้อง',NULL,NULL),(746,'7208',58,'สามชุก',NULL,NULL),(747,'7209',58,'อู่ทอง',NULL,NULL),(748,'7210',58,'หนองหญ้าไซ',NULL,NULL),(749,'7301',59,'เมืองนครปฐม',NULL,NULL),(750,'7302',59,'กำแพงแสน',NULL,NULL),(751,'7303',59,'นครชัยศรี',NULL,NULL),(752,'7304',59,'ดอนตูม',NULL,NULL),(753,'7305',59,'บางเลน',NULL,NULL),(754,'7306',59,'สามพราน',NULL,NULL),(755,'7307',59,'พุทธมณฑล',NULL,NULL),(756,'7401',60,'เมืองสมุทรสาคร',NULL,NULL),(757,'7402',60,'กระทุ่มแบน',NULL,NULL),(758,'7403',60,'บ้านแพ้ว',NULL,NULL),(759,'7501',61,'เมืองสมุทรสงคราม',NULL,NULL),(760,'7502',61,'บางคนที',NULL,NULL),(761,'7503',61,'อัมพวา',NULL,NULL),(762,'7601',62,'เมืองเพชรบุรี',NULL,NULL),(763,'7602',62,'เขาย้อย',NULL,NULL),(764,'7603',62,'หนองหญ้าปล้อง',NULL,NULL),(765,'7604',62,'ชะอำ',NULL,NULL),(766,'7605',62,'ท่ายาง',NULL,NULL),(767,'7606',62,'บ้านลาด',NULL,NULL),(768,'7607',62,'บ้านแหลม',NULL,NULL),(769,'7608',62,'แก่งกระจาน',NULL,NULL),(770,'7701',63,'เมืองประจวบคีรีขันธ์',NULL,NULL),(771,'7702',63,'กุยบุรี',NULL,NULL),(772,'7703',63,'ทับสะแก',NULL,NULL),(773,'7704',63,'บางสะพาน',NULL,NULL),(774,'7705',63,'บางสะพานน้อย',NULL,NULL),(775,'7706',63,'ปราณบุรี',NULL,NULL),(776,'7707',63,'หัวหิน',NULL,NULL),(777,'7708',63,'สามร้อยยอด',NULL,NULL),(778,'8001',64,'เมืองนครศรีธรรมราช',NULL,NULL),(779,'8002',64,'พรหมคีรี',NULL,NULL),(780,'8003',64,'ลานสกา',NULL,NULL),(781,'8004',64,'ฉวาง',NULL,NULL),(782,'8005',64,'พิปูน',NULL,NULL),(783,'8006',64,'เชียรใหญ่',NULL,NULL),(784,'8007',64,'ชะอวด',NULL,NULL),(785,'8008',64,'ท่าศาลา',NULL,NULL),(786,'8009',64,'ทุ่งสง',NULL,NULL),(787,'8010',64,'นาบอน',NULL,NULL),(788,'8011',64,'ทุ่งใหญ่',NULL,NULL),(789,'8012',64,'ปากพนัง',NULL,NULL),(790,'8013',64,'ร่อนพิบูลย์',NULL,NULL),(791,'8014',64,'สิชล',NULL,NULL),(792,'8015',64,'ขนอม',NULL,NULL),(793,'8016',64,'หัวไทร',NULL,NULL),(794,'8017',64,'บางขัน',NULL,NULL),(795,'8018',64,'ถ้ำพรรณรา',NULL,NULL),(796,'8019',64,'จุฬาภรณ์',NULL,NULL),(797,'8020',64,'พระพรหม',NULL,NULL),(798,'8021',64,'นบพิตำ',NULL,NULL),(799,'8022',64,'ช้างกลาง',NULL,NULL),(800,'8023',64,'เฉลิมพระเกียรติ',NULL,NULL),(801,'8101',65,'เมืองกระบี่',NULL,NULL),(802,'8102',65,'เขาพนม',NULL,NULL),(803,'8103',65,'เกาะลันตา',NULL,NULL),(804,'8104',65,'คลองท่อม',NULL,NULL),(805,'8105',65,'อ่าวลึก',NULL,NULL),(806,'8106',65,'ปลายพระยา',NULL,NULL),(807,'8107',65,'ลำทับ',NULL,NULL),(808,'8108',65,'เหนือคลอง',NULL,NULL),(809,'8201',66,'เมืองพังงา',NULL,NULL),(810,'8202',66,'เกาะยาว',NULL,NULL),(811,'8203',66,'กะปง',NULL,NULL),(812,'8204',66,'ตะกั่วทุ่ง',NULL,NULL),(813,'8205',66,'ตะกั่วป่า',NULL,NULL),(814,'8206',66,'คุระบุรี',NULL,NULL),(815,'8207',66,'ทับปุด',NULL,NULL),(816,'8208',66,'ท้ายเหมือง',NULL,NULL),(817,'8301',67,'เมืองภูเก็ต',NULL,NULL),(818,'8302',67,'กะทู้',NULL,NULL),(819,'8303',67,'ถลาง',NULL,NULL),(820,'8401',68,'เมืองสุราษฎร์ธานี',NULL,NULL),(821,'8402',68,'กาญจนดิษฐ์',NULL,NULL),(822,'8403',68,'ดอนสัก',NULL,NULL),(823,'8404',68,'เกาะสมุย',NULL,NULL),(824,'8405',68,'เกาะพะงัน',NULL,NULL),(825,'8406',68,'ไชยา',NULL,NULL),(826,'8407',68,'ท่าชนะ',NULL,NULL),(827,'8408',68,'คีรีรัฐนิคม',NULL,NULL),(828,'8409',68,'บ้านตาขุน',NULL,NULL),(829,'8410',68,'พนม',NULL,NULL),(830,'8411',68,'ท่าฉาง',NULL,NULL),(831,'8412',68,'บ้านนาสาร',NULL,NULL),(832,'8413',68,'บ้านนาเดิม',NULL,NULL),(833,'8414',68,'เคียนซา',NULL,NULL),(834,'8415',68,'เวียงสระ',NULL,NULL),(835,'8416',68,'พระแสง',NULL,NULL),(836,'8417',68,'พุนพิน',NULL,NULL),(837,'8418',68,'ชัยบุรี',NULL,NULL),(838,'8419',68,'วิภาวดี',NULL,NULL),(839,'8501',69,'เมืองระนอง',NULL,NULL),(840,'8502',69,'ละอุ่น',NULL,NULL),(841,'8503',69,'กะเปอร์',NULL,NULL),(842,'8504',69,'กระบุรี',NULL,NULL),(843,'8505',69,'สุขสำราญ',NULL,NULL),(844,'8601',70,'เมืองชุมพร',NULL,NULL),(845,'8602',70,'ท่าแซะ',NULL,NULL),(846,'8603',70,'ปะทิว',NULL,NULL),(847,'8604',70,'หลังสวน',NULL,NULL),(848,'8605',70,'ละแม',NULL,NULL),(849,'8606',70,'พะโต๊ะ',NULL,NULL),(850,'8607',70,'สวี',NULL,NULL),(851,'8608',70,'ทุ่งตะโก',NULL,NULL),(852,'9001',71,'เมืองสงขลา',NULL,NULL),(853,'9002',71,'สทิงพระ',NULL,NULL),(854,'9003',71,'จะนะ',NULL,NULL),(855,'9004',71,'นาทวี',NULL,NULL),(856,'9005',71,'เทพา',NULL,NULL),(857,'9006',71,'สะบ้าย้อย',NULL,NULL),(858,'9007',71,'ระโนด',NULL,NULL),(859,'9008',71,'กระแสสินธุ์',NULL,NULL),(860,'9009',71,'รัตภูมิ',NULL,NULL),(861,'9010',71,'สะเดา',NULL,NULL),(862,'9011',71,'หาดใหญ่',NULL,NULL),(863,'9012',71,'นาหม่อม',NULL,NULL),(864,'9013',71,'ควนเนียง',NULL,NULL),(865,'9014',71,'บางกล่ำ',NULL,NULL),(866,'9015',71,'สิงหนคร',NULL,NULL),(867,'9016',71,'คลองหอยโข่ง',NULL,NULL),(868,'9101',72,'เมืองสตูล',NULL,NULL),(869,'9102',72,'ควนโดน',NULL,NULL),(870,'9103',72,'ควนกาหลง',NULL,NULL),(871,'9104',72,'ท่าแพ',NULL,NULL),(872,'9105',72,'ละงู',NULL,NULL),(873,'9106',72,'ทุ่งหว้า',NULL,NULL),(874,'9107',72,'มะนัง',NULL,NULL),(875,'9201',73,'เมืองตรัง',NULL,NULL),(876,'9202',73,'กันตัง',NULL,NULL),(877,'9203',73,'ย่านตาขาว',NULL,NULL),(878,'9204',73,'ปะเหลียน',NULL,NULL),(879,'9205',73,'สิเกา',NULL,NULL),(880,'9206',73,'ห้วยยอด',NULL,NULL),(881,'9207',73,'วังวิเศษ',NULL,NULL),(882,'9208',73,'นาโยง',NULL,NULL),(883,'9209',73,'รัษฎา',NULL,NULL),(884,'9210',73,'หาดสำราญ',NULL,NULL),(885,'9301',74,'เมืองพัทลุง',NULL,NULL),(886,'9302',74,'กงหรา',NULL,NULL),(887,'9303',74,'เขาชัยสน',NULL,NULL),(888,'9304',74,'ตะโหมด',NULL,NULL),(889,'9305',74,'ควนขนุน',NULL,NULL),(890,'9306',74,'ปากพะยูน',NULL,NULL),(891,'9307',74,'ศรีบรรพต',NULL,NULL),(892,'9308',74,'ป่าบอน',NULL,NULL),(893,'9309',74,'บางแก้ว',NULL,NULL),(894,'9310',74,'ป่าพยอม',NULL,NULL),(895,'9311',74,'ศรีนครินทร์',NULL,NULL),(896,'9401',75,'เมืองปัตตานี',NULL,NULL),(897,'9402',75,'โคกโพธิ์',NULL,NULL),(898,'9403',75,'หนองจิก',NULL,NULL),(899,'9404',75,'ปะนาเระ',NULL,NULL),(900,'9405',75,'มายอ',NULL,NULL),(901,'9406',75,'ทุ่งยางแดง',NULL,NULL),(902,'9407',75,'สายบุรี',NULL,NULL),(903,'9408',75,'ไม้แก่น',NULL,NULL),(904,'9409',75,'ยะหริ่ง',NULL,NULL),(905,'9410',75,'ยะรัง',NULL,NULL),(906,'9411',75,'กะพ้อ',NULL,NULL),(907,'9412',75,'แม่ลาน',NULL,NULL),(908,'9501',76,'เมืองยะลา',NULL,NULL),(909,'9502',76,'เบตง',NULL,NULL),(910,'9503',76,'บันนังสตา',NULL,NULL),(911,'9504',76,'ธารโต',NULL,NULL),(912,'9505',76,'ยะหา',NULL,NULL),(913,'9506',76,'รามัน',NULL,NULL),(914,'9507',76,'กาบัง',NULL,NULL),(915,'9508',76,'กรงปีนัง',NULL,NULL),(916,'9601',77,'เมืองนราธิวาส',NULL,NULL),(917,'9602',77,'ตากใบ',NULL,NULL),(918,'9603',77,'บาเจาะ',NULL,NULL),(919,'9604',77,'ยี่งอ',NULL,NULL),(920,'9605',77,'ระแงะ',NULL,NULL),(921,'9606',77,'รือเสาะ',NULL,NULL),(922,'9607',77,'ศรีสาคร',NULL,NULL),(923,'9608',77,'แว้ง',NULL,NULL),(924,'9609',77,'สุคิริน',NULL,NULL),(925,'9610',77,'สุไหงโก-ลก',NULL,NULL),(926,'9611',77,'สุไหงปาดี',NULL,NULL),(927,'9612',77,'จะแนะ',NULL,NULL),(928,'9613',77,'เจาะไอร้อง',NULL,NULL);
/*!40000 ALTER TABLE `districts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
INSERT INTO `documents` VALUES (1,'สูติบัตร',NULL,NULL),(2,'หนังสือรับรองการเกิด (ทร.1/1)',NULL,NULL),(3,'บัตรประจำตัวประชาชน',NULL,NULL),(4,'ทะเบียนบ้าน',NULL,NULL),(5,'เอกสารทางการศึกษา',NULL,NULL),(6,'สมุดบันทึกตรวจสุขภาพแม่และเด็ก',NULL,NULL),(7,'บันทึกประจำวันเกี่ยวกับคดี',NULL,NULL),(8,'หนังสือยินยอมบิดา/มารดา',NULL,NULL),(9,'เอกสารทางการแพทย์',NULL,NULL),(10,'หนังสือนำส่ง',NULL,NULL);
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `education(สำรอง)`
--

DROP TABLE IF EXISTS `education(สำรอง)`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `education(สำรอง)` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `education_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `education(สำรอง)`
--

LOCK TABLES `education(สำรอง)` WRITE;
/*!40000 ALTER TABLE `education(สำรอง)` DISABLE KEYS */;
INSERT INTO `education(สำรอง)` VALUES (1,'เตรียมอนุบาล',NULL,NULL),(2,'อนุบาล 1 ',NULL,NULL),(3,'อนุบาล 2',NULL,NULL),(4,'อนุบาล 3',NULL,NULL),(5,'ประถมศึกษาปีที่ 1',NULL,NULL),(6,'ประถมศึกษาปีที่ 2',NULL,NULL),(7,'ประถมศึกษาปีที่ 3',NULL,NULL),(8,'ประถมศึกษาปีที่ 4',NULL,NULL),(9,'ประถมศึกษาปีที่ 5',NULL,NULL),(10,'ประถมศึกษาปีที่ 6',NULL,NULL),(11,'มัธยมศึกษาปีที่ 1',NULL,NULL),(12,'มัธยมศึกษาปีที่ 2',NULL,NULL),(13,'มัธยมศึกษาปีที่ 3',NULL,NULL),(14,'มัธยมศึกษาปีที่ 4',NULL,NULL),(15,'มัธยมศึกษาปีที่ 5',NULL,NULL),(16,'มัธยมศึกษาปีที่ 6',NULL,NULL),(17,'ประกาศนียบัตรวิชาชีพ 1',NULL,NULL),(18,'ประกาศนียบัตรวิชาชีพ 2',NULL,NULL),(19,'ประกาศนียบัตรวิชาชีพ 3 ',NULL,NULL),(20,'ประกาศนียบัตรวิชาชีพ (ปวส)  1',NULL,NULL),(21,'ประกาศนียบัตรวิชาชีพ  (ปวส) 2',NULL,NULL),(22,'ประกาศนียบัตรวิชาชีพเทคนิค (ปวท.) 1',NULL,NULL),(23,'ประกาศนียบัตรวิชาชีพเทคนิค (ปวท.) 2',NULL,NULL),(24,'ปริญญาตรีชั้นปีที่ 1',NULL,NULL),(25,'ปริญญาตรีชั้นปีที่ 2',NULL,NULL),(26,'ปริญญาตรีชั้นปีที่ 3',NULL,NULL),(27,'ปริญญาตรีชั้นปีที่ 4',NULL,NULL),(28,'ปริญญาโทชั้นปีที่ 1',NULL,NULL),(29,'ปริญญาโทชั้นปีที่ 2',NULL,NULL),(30,'ระดับประถมศึกษา (กศน.)',NULL,NULL),(31,'ระดับมัธยมศึกษาตอนต้น (กศน.)',NULL,NULL),(32,'ระดับมัธยมศึกษาตอนปลาย (กศน.)',NULL,NULL),(33,'การศึกษาพิเศษ',NULL,NULL),(34,'ไม่ได้รับการศึกษา',NULL,NULL),(35,'ไม่ทราบข้อมูล',NULL,NULL);
/*!40000 ALTER TABLE `education(สำรอง)` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `education_levels`
--

DROP TABLE IF EXISTS `education_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `education_levels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `education_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `education_levels`
--

LOCK TABLES `education_levels` WRITE;
/*!40000 ALTER TABLE `education_levels` DISABLE KEYS */;
INSERT INTO `education_levels` VALUES (1,'เตรียมอนุบาล','2026-05-01 09:11:07','2026-05-01 09:11:07'),(2,'อนุบาล 1','2026-05-01 09:11:07','2026-05-01 09:11:07'),(3,'อนุบาล 2','2026-05-01 09:11:07','2026-05-01 09:11:07'),(4,'อนุบาล 3','2026-05-01 09:11:07','2026-05-01 09:11:07'),(5,'ประถมศึกษาปีที่ 1','2026-05-01 09:11:07','2026-05-01 09:11:07'),(6,'ประถมศึกษาปีที่ 2','2026-05-01 09:11:07','2026-05-01 09:11:07'),(7,'ประถมศึกษาปีที่ 3','2026-05-01 09:11:07','2026-05-01 09:11:07'),(8,'ประถมศึกษาปีที่ 4','2026-05-01 09:11:07','2026-05-01 09:11:07'),(9,'ประถมศึกษาปีที่ 5','2026-05-01 09:11:07','2026-05-01 09:11:07'),(10,'ประถมศึกษาปีที่ 6','2026-05-01 09:11:07','2026-05-01 09:11:07'),(11,'มัธยมศึกษาปีที่ 1','2026-05-01 09:11:07','2026-05-01 09:11:07'),(12,'มัธยมศึกษาปีที่ 2','2026-05-01 09:11:07','2026-05-01 09:11:07'),(13,'มัธยมศึกษาปีที่ 3','2026-05-01 09:11:07','2026-05-01 09:11:07'),(14,'มัธยมศึกษาปีที่ 4','2026-05-01 09:11:07','2026-05-01 09:11:07'),(15,'มัธยมศึกษาปีที่ 5','2026-05-01 09:11:07','2026-05-01 09:11:07'),(16,'มัธยมศึกษาปีที่ 6','2026-05-01 09:11:07','2026-05-01 09:11:07'),(17,'ประกาศนียบัตรวิชาชีพ 1','2026-05-01 09:11:07','2026-05-01 09:11:07'),(18,'ประกาศนียบัตรวิชาชีพ 2','2026-05-01 09:11:07','2026-05-01 09:11:07'),(19,'ประกาศนียบัตรวิชาชีพ 3','2026-05-01 09:11:07','2026-05-01 09:11:07'),(20,'ประกาศนียบัตรวิชาชีพ (ปวส) 1','2026-05-01 09:11:07','2026-05-01 09:11:07'),(21,'ประกาศนียบัตรวิชาชีพ (ปวส) 2','2026-05-01 09:11:07','2026-05-01 09:11:07'),(22,'ประกาศนียบัตรวิชาชีพเทคนิค (ปวท.) 1','2026-05-01 09:11:07','2026-05-01 09:11:07'),(23,'ประกาศนียบัตรวิชาชีพเทคนิค (ปวท.) 2','2026-05-01 09:11:07','2026-05-01 09:11:07'),(24,'ปริญญาตรีชั้นปีที่ 1','2026-05-01 09:11:07','2026-05-01 09:11:07'),(25,'ปริญญาตรีชั้นปีที่ 2','2026-05-01 09:11:07','2026-05-01 09:11:07'),(26,'ปริญญาตรีชั้นปีที่ 3','2026-05-01 09:11:07','2026-05-01 09:11:07'),(27,'ปริญญาตรีชั้นปีที่ 4','2026-05-01 09:11:07','2026-05-01 09:11:07'),(28,'ปริญญาโทชั้นปีที่ 1','2026-05-01 09:11:07','2026-05-01 09:11:07'),(29,'ปริญญาโทชั้นปีที่ 2','2026-05-01 09:11:07','2026-05-01 09:11:07'),(30,'ระดับประถมศึกษา (กศน.)','2026-05-01 09:11:07','2026-05-01 09:11:07'),(31,'ระดับมัธยมศึกษาตอนต้น (กศน.)','2026-05-01 09:11:07','2026-05-01 09:11:07'),(32,'ระดับมัธยมศึกษาตอนปลาย (กศน.)','2026-05-01 09:11:07','2026-05-01 09:11:07'),(33,'การศึกษาพิเศษ','2026-05-01 09:11:07','2026-05-01 09:11:07'),(34,'ไม่ได้รับการศึกษา','2026-05-01 09:11:07','2026-05-01 09:11:07'),(35,'ไม่ทราบข้อมูล','2026-05-01 09:11:07','2026-05-01 09:11:07');
/*!40000 ALTER TABLE `education_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `education_record_subjects`
--

DROP TABLE IF EXISTS `education_record_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `education_record_subjects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `education_record_id` bigint(20) unsigned NOT NULL,
  `subject_id` bigint(20) unsigned NOT NULL,
  `score` int(11) DEFAULT NULL,
  `grade` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `education_record_subjects_education_record_id_subject_id_unique` (`education_record_id`,`subject_id`),
  KEY `education_record_subjects_subject_id_foreign` (`subject_id`),
  CONSTRAINT `education_record_subjects_education_record_id_foreign` FOREIGN KEY (`education_record_id`) REFERENCES `education_records` (`id`) ON DELETE CASCADE,
  CONSTRAINT `education_record_subjects_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `education_record_subjects`
--

LOCK TABLES `education_record_subjects` WRITE;
/*!40000 ALTER TABLE `education_record_subjects` DISABLE KEYS */;
INSERT INTO `education_record_subjects` VALUES (12,9,1,85,'4.00','2026-07-28 13:49:15','2026-07-28 13:49:15');
/*!40000 ALTER TABLE `education_record_subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `education_records`
--

DROP TABLE IF EXISTS `education_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `education_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `education_id` bigint(20) unsigned NOT NULL,
  `institution_id` bigint(20) unsigned DEFAULT NULL,
  `semester_id` bigint(20) unsigned DEFAULT NULL,
  `semester` varchar(255) DEFAULT NULL,
  `school_name` varchar(255) NOT NULL,
  `record_date` date NOT NULL,
  `grade_average` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `education_records_client_id_foreign` (`client_id`),
  KEY `education_records_education_id_foreign` (`education_id`),
  KEY `education_records_institution_id_foreign` (`institution_id`),
  KEY `education_records_semester_id_foreign` (`semester_id`),
  CONSTRAINT `education_records_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `education_records_education_id_foreign` FOREIGN KEY (`education_id`) REFERENCES `education_levels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `education_records_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `education_records_semester_id_foreign` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `education_records`
--

LOCK TABLES `education_records` WRITE;
/*!40000 ALTER TABLE `education_records` DISABLE KEYS */;
INSERT INTO `education_records` VALUES (9,206,11,7,24,NULL,'โรงเรียนชลกันยานุกูล','2026-07-28',NULL,'2026-07-28 13:49:15','2026-07-28 13:49:15');
/*!40000 ALTER TABLE `education_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `escape_follows`
--

DROP TABLE IF EXISTS `escape_follows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `escape_follows` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `escape_id` bigint(20) unsigned NOT NULL,
  `trace_date` date NOT NULL,
  `count` int(11) NOT NULL,
  `trac_no` enum('พบ','ไม่พบ') NOT NULL,
  `detail` text DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `stop_date` date DEFAULT NULL,
  `punish` varchar(255) DEFAULT NULL,
  `punish_date` date DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `escape_follows_escape_id_foreign` (`escape_id`),
  CONSTRAINT `escape_follows_escape_id_foreign` FOREIGN KEY (`escape_id`) REFERENCES `escapes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `escape_follows`
--

LOCK TABLES `escape_follows` WRITE;
/*!40000 ALTER TABLE `escape_follows` DISABLE KEYS */;
/*!40000 ALTER TABLE `escape_follows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `escapes`
--

DROP TABLE IF EXISTS `escapes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `escapes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `retire_date` date NOT NULL,
  `retire_id` bigint(20) unsigned NOT NULL,
  `stories` text DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `escapes_client_id_foreign` (`client_id`),
  KEY `escapes_retire_id_foreign` (`retire_id`),
  CONSTRAINT `escapes_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `escapes_retire_id_foreign` FOREIGN KEY (`retire_id`) REFERENCES `retires` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `escapes`
--

LOCK TABLES `escapes` WRITE;
/*!40000 ALTER TABLE `escapes` DISABLE KEYS */;
/*!40000 ALTER TABLE `escapes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estimate_pictures`
--

DROP TABLE IF EXISTS `estimate_pictures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estimate_pictures` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `estimate_id` bigint(20) unsigned NOT NULL,
  `path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estimate_pictures_estimate_id_foreign` (`estimate_id`),
  CONSTRAINT `estimate_pictures_estimate_id_foreign` FOREIGN KEY (`estimate_id`) REFERENCES `estimates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estimate_pictures`
--

LOCK TABLES `estimate_pictures` WRITE;
/*!40000 ALTER TABLE `estimate_pictures` DISABLE KEYS */;
/*!40000 ALTER TABLE `estimate_pictures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estimates`
--

DROP TABLE IF EXISTS `estimates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estimates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `count` int(10) unsigned NOT NULL DEFAULT 1,
  `follo_no` enum('หน่วยงานไปเอง','โทรศัพท์','จดหมาย') NOT NULL,
  `results` text DEFAULT NULL,
  `family_income` decimal(10,2) DEFAULT NULL,
  `guardian_job` varchar(255) DEFAULT NULL,
  `income_sufficiency` varchar(50) DEFAULT NULL,
  `income_reason` text DEFAULT NULL,
  `debt` text DEFAULT NULL,
  `housing_condition` varchar(255) DEFAULT NULL,
  `teacher` varchar(255) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estimates_client_id_foreign` (`client_id`),
  CONSTRAINT `estimates_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estimates`
--

LOCK TABLES `estimates` WRITE;
/*!40000 ALTER TABLE `estimates` DISABLE KEYS */;
/*!40000 ALTER TABLE `estimates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factfinding_documents`
--

DROP TABLE IF EXISTS `factfinding_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `factfinding_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `factfinding_id` bigint(20) unsigned NOT NULL,
  `document_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `factfinding_documents_factfinding_id_document_id_unique` (`factfinding_id`,`document_id`),
  KEY `factfinding_documents_document_id_foreign` (`document_id`),
  CONSTRAINT `factfinding_documents_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `factfinding_documents_factfinding_id_foreign` FOREIGN KEY (`factfinding_id`) REFERENCES `factfindings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factfinding_documents`
--

LOCK TABLES `factfinding_documents` WRITE;
/*!40000 ALTER TABLE `factfinding_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `factfinding_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factfindings`
--

DROP TABLE IF EXISTS `factfindings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `factfindings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL COMMENT 'วันที่นำส่ง',
  `receive_date` date DEFAULT NULL COMMENT 'วันที่บันทึกข้อมูล',
  `fact_name` varchar(255) DEFAULT NULL COMMENT 'ชื่อผู้นำส่ง',
  `evidence` varchar(255) DEFAULT NULL COMMENT 'หลักฐานเพิ่มเติม',
  `appearance` varchar(255) DEFAULT NULL COMMENT 'รูปพรรณสัณฐาน',
  `skin` varchar(255) DEFAULT NULL COMMENT 'สีผิว',
  `scar` varchar(255) DEFAULT NULL COMMENT 'ตำหนิ/แผลเป็น',
  `disability` varchar(255) DEFAULT NULL COMMENT 'ลักษณะความพิการ',
  `sick` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ป่วย 1=yes 0=no',
  `sick_detail` text DEFAULT NULL COMMENT 'รายละเอียดการเจ็บป่วย',
  `treatment` varchar(255) DEFAULT NULL COMMENT 'การรักษา',
  `hospital` varchar(255) DEFAULT NULL COMMENT 'โรงพยาบาล',
  `weight` decimal(5,2) DEFAULT NULL COMMENT 'น้ำหนัก',
  `height` decimal(5,2) DEFAULT NULL COMMENT 'ส่วนสูง',
  `blood_group` varchar(255) DEFAULT NULL COMMENT 'กรุ๊ปเลือด',
  `hygiene` varchar(255) DEFAULT NULL COMMENT 'ความสะอาด',
  `oral_health` varchar(255) DEFAULT NULL COMMENT 'สุขภาพช่องปาก',
  `injury` varchar(255) DEFAULT NULL COMMENT 'การบาดเจ็บ',
  `marital_id` bigint(20) unsigned NOT NULL,
  `relation_parent` text DEFAULT NULL COMMENT 'ความสัมพันธ์บิดามารดา',
  `relation_family` text DEFAULT NULL COMMENT 'ความสัมพันธ์ในครอบครัว',
  `relation_child` text DEFAULT NULL COMMENT 'ความสัมพันธ์เด็กกับครอบครัว',
  `ex_conditions` text DEFAULT NULL COMMENT 'สภาพที่อยู่อาศัยภายนอก',
  `in_conditions` text DEFAULT NULL COMMENT 'สภาพที่อยู่อาศัยภายใน',
  `environment` text DEFAULT NULL COMMENT 'สภาพแวดล้อม',
  `cause_problem` text DEFAULT NULL COMMENT 'สาเหตุของปัญหา',
  `need` text DEFAULT NULL COMMENT 'ความต้องการ',
  `information` text DEFAULT NULL COMMENT 'ข้อเท็จจริงอื่น',
  `diagnosis` text DEFAULT NULL COMMENT 'การวินิจฉัยปัญหา',
  `case_history` text DEFAULT NULL COMMENT 'ประวัติความเป็นมา',
  `recorder` varchar(255) DEFAULT NULL COMMENT 'ผู้บันทึก',
  `client_id` bigint(20) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'สถานะใช้งาน',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `factfindings_client_id_unique` (`client_id`),
  KEY `factfindings_marital_id_foreign` (`marital_id`),
  CONSTRAINT `factfindings_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `factfindings_marital_id_foreign` FOREIGN KEY (`marital_id`) REFERENCES `maritals` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factfindings`
--

LOCK TABLES `factfindings` WRITE;
/*!40000 ALTER TABLE `factfindings` DISABLE KEYS */;
/*!40000 ALTER TABLE `factfindings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fathers`
--

DROP TABLE IF EXISTS `fathers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fathers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `income` decimal(10,2) DEFAULT NULL,
  `idcard` varchar(255) DEFAULT NULL,
  `address_no` varchar(255) DEFAULT NULL,
  `moo` varchar(255) DEFAULT NULL,
  `soi` varchar(255) DEFAULT NULL,
  `road` varchar(255) DEFAULT NULL,
  `village` varchar(255) DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `province_id` bigint(20) unsigned DEFAULT NULL,
  `district_id` bigint(20) unsigned DEFAULT NULL,
  `sub_district_id` bigint(20) unsigned DEFAULT NULL,
  `zipcode` int(10) unsigned DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fathers_client_id_unique` (`client_id`),
  CONSTRAINT `fathers_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fathers`
--

LOCK TABLES `fathers` WRITE;
/*!40000 ALTER TABLE `fathers` DISABLE KEYS */;
/*!40000 ALTER TABLE `fathers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `followups`
--

DROP TABLE IF EXISTS `followups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `followups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `followup_date` date NOT NULL COMMENT 'วันเดือนปี',
  `assistance_detail` text NOT NULL COMMENT 'การช่วยเหลือและติดตามผล',
  `note` text DEFAULT NULL COMMENT 'หมายเหตุ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `followups_client_id_index` (`client_id`),
  KEY `followups_followup_date_index` (`followup_date`),
  CONSTRAINT `followups_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `followups`
--

LOCK TABLES `followups` WRITE;
/*!40000 ALTER TABLE `followups` DISABLE KEYS */;
/*!40000 ALTER TABLE `followups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `healthc_heckups`
--

DROP TABLE IF EXISTS `healthc_heckups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `healthc_heckups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `checkup_date` date NOT NULL,
  `hospital_name` varchar(255) NOT NULL,
  `checkup_result` enum('normal','abnormal') NOT NULL DEFAULT 'normal',
  `abnormal_detail` text DEFAULT NULL,
  `medical_document` varchar(255) DEFAULT NULL,
  `recorded_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `healthc_heckups_recorded_by_foreign` (`recorded_by`),
  KEY `healthc_heckups_client_id_checkup_date_index` (`client_id`,`checkup_date`),
  KEY `healthc_heckups_checkup_result_index` (`checkup_result`),
  CONSTRAINT `healthc_heckups_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `healthc_heckups_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `healthc_heckups`
--

LOCK TABLES `healthc_heckups` WRITE;
/*!40000 ALTER TABLE `healthc_heckups` DISABLE KEYS */;
/*!40000 ALTER TABLE `healthc_heckups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `help_items`
--

DROP TABLE IF EXISTS `help_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `help_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `help_session_id` bigint(20) unsigned NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `help_items_help_session_id_foreign` (`help_session_id`),
  CONSTRAINT `help_items_help_session_id_foreign` FOREIGN KEY (`help_session_id`) REFERENCES `help_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `help_items`
--

LOCK TABLES `help_items` WRITE;
/*!40000 ALTER TABLE `help_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `help_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `help_sessions`
--

DROP TABLE IF EXISTS `help_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `help_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `help_date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `help_sessions_client_id_foreign` (`client_id`),
  CONSTRAINT `help_sessions_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `help_sessions`
--

LOCK TABLES `help_sessions` WRITE;
/*!40000 ALTER TABLE `help_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `help_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `help_types`
--

DROP TABLE IF EXISTS `help_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `help_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `help_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `help_types`
--

LOCK TABLES `help_types` WRITE;
/*!40000 ALTER TABLE `help_types` DISABLE KEYS */;
INSERT INTO `help_types` VALUES (1,'ด้านการศึกษา','2026-02-26 17:15:41','2026-02-26 17:19:01'),(2,'เครื่องอุปโภค/บริโภค','2026-02-26 17:16:04','2026-02-26 17:16:04'),(3,'ค่าอาหาร','2026-02-26 17:16:42','2026-02-26 17:16:42'),(4,'ค่าเดินทาง','2026-02-26 17:16:59','2026-02-26 17:16:59'),(5,'ค่าที่พัก','2026-02-26 17:17:38','2026-02-26 17:17:38');
/*!40000 ALTER TABLE `help_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `house_user`
--

DROP TABLE IF EXISTS `house_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `house_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `house_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `house_user_user_id_house_id_unique` (`user_id`,`house_id`),
  KEY `house_user_house_id_foreign` (`house_id`),
  CONSTRAINT `house_user_house_id_foreign` FOREIGN KEY (`house_id`) REFERENCES `houses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `house_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `house_user`
--

LOCK TABLES `house_user` WRITE;
/*!40000 ALTER TABLE `house_user` DISABLE KEYS */;
INSERT INTO `house_user` VALUES (1,2,1,'2026-05-01 01:32:05','2026-05-01 01:32:05'),(2,2,2,'2026-05-01 01:32:05','2026-05-01 01:32:05'),(3,2,3,'2026-05-01 01:32:05','2026-05-01 01:32:05'),(4,2,4,'2026-05-01 01:32:05','2026-05-01 01:32:05'),(5,2,5,'2026-05-01 01:32:05','2026-05-01 01:32:05'),(6,2,6,'2026-05-01 01:32:05','2026-05-01 01:32:05'),(7,2,7,'2026-05-01 01:32:05','2026-05-01 01:32:05'),(8,2,8,'2026-05-01 01:32:05','2026-05-01 01:32:05'),(9,2,9,'2026-05-01 01:32:05','2026-05-01 01:32:05'),(10,2,10,'2026-05-01 01:32:05','2026-05-01 01:32:05'),(11,2,11,'2026-05-01 01:32:05','2026-05-01 01:32:05'),(12,2,12,'2026-05-01 01:32:05','2026-05-01 01:32:05'),(13,3,1,'2026-05-04 02:48:28','2026-05-04 02:48:28');
/*!40000 ALTER TABLE `house_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `houses`
--

DROP TABLE IF EXISTS `houses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `houses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `house_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `houses`
--

LOCK TABLES `houses` WRITE;
/*!40000 ALTER TABLE `houses` DISABLE KEYS */;
INSERT INTO `houses` VALUES (1,'บ้านหลังที่ 1',NULL,NULL),(2,'บ้านหลังที่ 2',NULL,NULL),(3,'บ้านหลังที่ 3',NULL,NULL),(4,'บ้านหลังที่ 4',NULL,NULL),(5,'บ้านหลังที่ 5',NULL,NULL),(6,'บ้านหลังที่ 6',NULL,NULL),(7,'บ้านหลังที่ 7',NULL,NULL),(8,'บ้านหลังที่ 8',NULL,NULL),(9,'บ้านหลังที่ 9',NULL,NULL),(10,'บ้านหลังที่ 10',NULL,NULL),(11,'บ้านหลังที่ 11',NULL,NULL),(12,'บ้านหลังที่ 12',NULL,NULL);
/*!40000 ALTER TABLE `houses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `idstations`
--

DROP TABLE IF EXISTS `idstations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `idstations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `receive_date` date NOT NULL,
  `detail` text DEFAULT NULL,
  `process_status` enum('processing','received_status') NOT NULL DEFAULT 'processing',
  `received_status_date` date DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idstations_client_id_foreign` (`client_id`),
  KEY `idstations_created_by_foreign` (`created_by`),
  KEY `idstations_updated_by_foreign` (`updated_by`),
  CONSTRAINT `idstations_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `idstations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `idstations_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `idstations`
--

LOCK TABLES `idstations` WRITE;
/*!40000 ALTER TABLE `idstations` DISABLE KEYS */;
/*!40000 ALTER TABLE `idstations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `size` int(10) unsigned DEFAULT NULL,
  `visit_family_id` bigint(20) unsigned DEFAULT NULL,
  `client_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `images_visit_family_id_foreign` (`visit_family_id`),
  KEY `images_client_id_foreign` (`client_id`),
  CONSTRAINT `images_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `images_visit_family_id_foreign` FOREIGN KEY (`visit_family_id`) REFERENCES `visit_families` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `images`
--

LOCK TABLES `images` WRITE;
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
/*!40000 ALTER TABLE `images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incomes`
--

DROP TABLE IF EXISTS `incomes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `incomes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `income_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incomes`
--

LOCK TABLES `incomes` WRITE;
/*!40000 ALTER TABLE `incomes` DISABLE KEYS */;
INSERT INTO `incomes` VALUES (1,'ต่ำกว่า 3,000',NULL,NULL),(2,'3,000 - 5,000',NULL,NULL),(3,'5,001 - 9,000',NULL,NULL),(4,'9,001 - 12,000',NULL,NULL),(5,'12,001 - 15,000',NULL,NULL),(6,'18,001 - 21,000',NULL,NULL),(7,'21,001 - 25,000',NULL,NULL),(8,'25,001 - 30,000',NULL,NULL),(9,'35,001 - 40,000',NULL,NULL),(10,'มากว่า 40,000',NULL,NULL),(11,'ไม่มีรายได้',NULL,NULL),(12,'ไม่มีข้อมูล',NULL,NULL);
/*!40000 ALTER TABLE `incomes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `institutions`
--

DROP TABLE IF EXISTS `institutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `institutions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_name` varchar(255) NOT NULL COMMENT 'ชื่อสถานศึกษา',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `institutions`
--

LOCK TABLES `institutions` WRITE;
/*!40000 ALTER TABLE `institutions` DISABLE KEYS */;
INSERT INTO `institutions` VALUES (6,'โพธิสัมพันธ์วิทยาคาร (เมืองพัทยา 4)','2026-07-11 13:29:01','2026-07-11 13:29:01'),(7,'โรงเรียนชลกันยานุกูล','2026-07-28 13:49:15','2026-07-28 13:49:15');
/*!40000 ALTER TABLE `institutions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `issues`
--

DROP TABLE IF EXISTS `issues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `issues` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `subject` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `issues`
--

LOCK TABLES `issues` WRITE;
/*!40000 ALTER TABLE `issues` DISABLE KEYS */;
INSERT INTO `issues` VALUES (8,'นายสุชาติ สุทธินาค','0869783512','ทดสอบ',1,'2026-07-01 05:32:05','2026-07-01 04:25:48','2026-07-01 05:32:05'),(9,'นายสุชาติ สุทธินาค','0855976596','ทดสอบ',1,'2026-07-01 09:07:44','2026-07-01 08:26:23','2026-07-01 09:07:44');
/*!40000 ALTER TABLE `issues` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_agencies`
--

DROP TABLE IF EXISTS `job_agencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_agencies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `job_date` date NOT NULL,
  `position` varchar(255) NOT NULL,
  `income` decimal(10,2) NOT NULL,
  `company` varchar(255) NOT NULL,
  `coordinator` varchar(255) NOT NULL,
  `remark` text DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `occupation_id` bigint(20) unsigned NOT NULL,
  `count` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_agencies_client_id_foreign` (`client_id`),
  KEY `job_agencies_occupation_id_foreign` (`occupation_id`),
  CONSTRAINT `job_agencies_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_agencies_occupation_id_foreign` FOREIGN KEY (`occupation_id`) REFERENCES `occupations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_agencies`
--

LOCK TABLES `job_agencies` WRITE;
/*!40000 ALTER TABLE `job_agencies` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_agencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maritals`
--

DROP TABLE IF EXISTS `maritals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maritals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `marital_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maritals`
--

LOCK TABLES `maritals` WRITE;
/*!40000 ALTER TABLE `maritals` DISABLE KEYS */;
INSERT INTO `maritals` VALUES (1,'โสด',NULL,NULL),(2,'สมรสจดทะเบียน',NULL,NULL),(3,'สมรสไม่จดทะเบียน',NULL,NULL),(4,'หม้ายเสียชีวิต',NULL,NULL),(5,'หม้ายหย่าร้าง',NULL,NULL),(6,'แยกกันอยู่',NULL,NULL),(7,'เลิกร้าง',NULL,NULL),(8,'ไม่มีข้อมูล',NULL,NULL);
/*!40000 ALTER TABLE `maritals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medicals`
--

DROP TABLE IF EXISTS `medicals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `medicals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `medical_date` date NOT NULL,
  `disease_name` varchar(255) DEFAULT NULL,
  `illness` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `refer` enum('พบแพทย์','ไม่พบแพทย์') NOT NULL DEFAULT 'ไม่พบแพทย์',
  `diagnosis` text DEFAULT NULL,
  `appt_date` date DEFAULT NULL,
  `teacher` varchar(255) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `medicals_client_id_foreign` (`client_id`),
  CONSTRAINT `medicals_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medicals`
--

LOCK TABLES `medicals` WRITE;
/*!40000 ALTER TABLE `medicals` DISABLE KEYS */;
/*!40000 ALTER TABLE `medicals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `count` int(11) DEFAULT NULL,
  `fullname` varchar(255) NOT NULL,
  `member_age` int(11) DEFAULT NULL,
  `education_id` bigint(20) unsigned DEFAULT NULL,
  `relationship` varchar(255) DEFAULT NULL,
  `occupation_id` bigint(20) unsigned DEFAULT NULL,
  `income_id` bigint(20) unsigned DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `members_client_id_foreign` (`client_id`),
  KEY `members_education_id_foreign` (`education_id`),
  KEY `members_occupation_id_foreign` (`occupation_id`),
  KEY `members_income_id_foreign` (`income_id`),
  CONSTRAINT `members_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `members_education_id_foreign` FOREIGN KEY (`education_id`) REFERENCES `education(สำรอง)` (`id`),
  CONSTRAINT `members_income_id_foreign` FOREIGN KEY (`income_id`) REFERENCES `incomes` (`id`),
  CONSTRAINT `members_occupation_id_foreign` FOREIGN KEY (`occupation_id`) REFERENCES `occupations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_11_28_052656_create_institutions_table',1),(5,'2025_11_28_131556_create_titles_table',1),(6,'2025_11_28_132115_create_nationals_table',1),(7,'2025_11_28_132555_create_religions_table',1),(8,'2025_11_28_132757_create_maritals_table',1),(9,'2025_11_28_133346_create_occupations_table',1),(10,'2025_11_28_133852_create_education_levels_table',1),(11,'2025_11_28_133852_create_education_table',1),(12,'2025_11_28_133900_create_semesters_table',1),(13,'2025_11_28_135056_create_incomes_table',1),(14,'2025_11_28_140022_create_targets_table',1),(15,'2025_11_28_140732_create_contacts_table',1),(16,'2025_11_28_141332_create_documents_table',1),(17,'2025_11_29_054156_create_clients_table',1),(18,'2025_11_29_060737_create_statuses_table',1),(19,'2025_11_29_060844_create_houses_table',1),(20,'2025_11_29_061904_create_projects_table',1),(21,'2025_11_29_065500_create_problems_table',1),(22,'2025_11_29_065707_create_client_problem_table',1),(23,'2025_12_03_141034_create_factfindings_table',1),(24,'2025_12_03_142352_create_factfinding_document_table',1),(25,'2025_12_04_123926_create_fathers_table',1),(26,'2025_12_04_125133_create_mothers_table',1),(27,'2025_12_04_125751_create_spouses_table',1),(28,'2025_12_04_125936_create_relatives_table',1),(29,'2025_12_08_015242_create_subjects_table',1),(30,'2025_12_08_040432_create_education_records_table',1),(31,'2025_12_08_040650_create_education_record_subjects_table',1),(32,'2025_12_13_013932_create_scholl_followups_table',1),(33,'2025_12_21_114042_create_absents_table',1),(34,'2025_12_22_111547_create_accidents_table',1),(35,'2025_12_24_100356_create_check_bodies_table',1),(36,'2025_12_25_024415_create_medicals_table',1),(37,'2025_12_26_055000_create_vaccinations_table',1),(38,'2025_12_26_141123_create_psychos_table',1),(39,'2025_12_27_091945_create_psychiatrics_table',1),(40,'2025_12_27_144505_create_addictives_table',1),(41,'2025_12_28_104402_create_misbehaviors_table',1),(42,'2025_12_29_024735_create_observes_table',1),(43,'2025_12_29_025139_create_observe_followups_table',1),(44,'2026_01_02_103618_create_visit_families_table',1),(45,'2026_01_02_103631_create_images_table',1),(46,'2026_01_04_103119_create_members_table',1),(47,'2026_01_05_073934_create_retires_table',1),(48,'2026_01_05_102937_create_escapes_table',1),(49,'2026_01_05_103005_create_escape_follows_table',1),(50,'2026_01_08_114320_create_estimates_table',1),(51,'2026_01_08_114659_create_estimate_pictures_table',1),(52,'2026_01_09_063315_create_outsides_table',1),(53,'2026_01_10_074536_create_case_outsides_table',1),(54,'2026_01_11_011834_create_translates_table',1),(55,'2026_01_11_024716_create_refers_table',1),(56,'2026_02_26_101355_create_job_agencies_table',1),(57,'2026_02_27_133650_create_help_types_table',1),(58,'2026_02_28_034650_create_help_sessions_table',1),(59,'2026_02_28_034804_create_help_items_table',1),(60,'2026_03_23_094119_create_client_files_table',1),(61,'2026_03_24_132911_create_issues_table',1),(62,'2026_03_24_142745_create_news_table',1),(63,'2026_03_25_070430_create_about_data_table',1),(64,'2026_04_03_093649_create_publicizes_table',1),(65,'2026_04_04_020628_alter_role_default_in_users_table',1),(66,'2026_04_04_102147_create_operations_table',1),(67,'2026_04_05_094706_create_house_user_table',1),(68,'2026_04_11_014906_create_followups_table',1),(69,'2026_04_23_131357_create_healthc_heckups_table',1),(70,'2026_04_26_022702_add_release_status_to_clients_table',1),(71,'2026_04_26_025957_create_psychiatrics_table',1),(72,'2026_04_27_020733_add_receive_date_to_factfindings_table',1),(73,'2026_04_27_020922_add_missing_fields_to_factfindings_table',1),(74,'2026_04_27_021029_add_health_fields_to_factfindings_table',1),(75,'2026_04_27_021534_add_cause_problem_and_need_to_factfindings_table',1),(76,'2026_04_27_125325_add_residence_status_to_visit_families_table',1),(77,'2026_04_27_130533_add_income_fields_to_estimates_table',1),(78,'2026_04_30_132905_change_debt_column_to_text_in_estimates_table',1),(79,'2026_05_01_025718_add_development_support_fields_to_check_bodies_table',1),(80,'2026_05_01_043630_add_approve_status_to_refers_table',1),(81,'2026_05_01_043906_create_location_tables',1),(82,'2026_05_02_073656_create_scholarships_table',2),(83,'2026_05_02_085255_create_scholarship_donations_table',3),(84,'2026_05_03_123314_create_scholarship_children_table',4),(85,'2026_05_04_070014_add_psycho_foreign_to_psychiatrics_table',5),(86,'2026_05_04_093616_add_unique_absent_per_day',6),(87,'2026_05_06_143407_add_committee_result_to_refers_table',7),(88,'2026_05_06_144742_patch_add_missing_refer_workflow_fields_to_refers_table',7),(89,'2026_05_17_031515_add_project_id_to_users_table',7),(90,'2026_05_17_031603_add_project_id_to_users_table',7),(91,'2026_05_17_140631_create_client_transfers_table',7),(92,'2026_05_18_090907_create_client_house_transfers_table',7),(93,'2026_05_20_091830_create_behavior_screenings_table',7),(94,'2026_05_20_091844_create_behavior_screening_items_table',7),(95,'2026_05_21_035058_create_case_activities_table',7),(96,'2026_05_25_090511_create_snap_iv_screenings_table',7),(97,'2026_05_25_090634_create_snap_iv_screening_items_table',7),(98,'2026_05_28_205019_create_depression_screenings_table',7),(99,'2026_05_28_205044_create_depression_screening_items_table',7),(100,'2026_05_30_093308_create_nutrition_assessments_table',7),(101,'2026_05_30_100559_add_basic_result_to_nutrition_assessments_table',7),(102,'2026_05_30_101222_add_growth_result_to_nutrition_assessments_table',7),(103,'2026_05_30_101410_create_nutrition_growth_standards_table',7),(104,'2026_07_01_091744_add_read_fields_to_issues_table',7),(105,'2026_07_01_105154_add_read_fields_to_scholarships_table',8),(106,'2026_07_01_114619_add_gender_to_scholarship_children_table',9),(107,'2026_07_04_170007_create_citizens_table',10),(108,'2026_07_04_204330_create_citizenships_table',11),(110,'2026_07_06_202806_create_idstations_table',12),(111,'2026_07_06_202918_create_citizenship_idstation_table',12),(112,'2026_07_06_202938_create_citizen_idstation_table',12);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `misbehaviors`
--

DROP TABLE IF EXISTS `misbehaviors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `misbehaviors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `misbehavior_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `misbehaviors`
--

LOCK TABLES `misbehaviors` WRITE;
/*!40000 ALTER TABLE `misbehaviors` DISABLE KEYS */;
INSERT INTO `misbehaviors` VALUES (1,'ติดโทรศัพท์','2025-12-27 14:35:03','2025-12-27 14:35:03'),(2,'ติดเกมส์','2025-12-27 14:48:12','2025-12-27 14:48:12'),(3,'ลักเล็กขโมยน้อย','2025-12-27 14:48:25','2025-12-27 14:48:25'),(4,'ลักขโมยของนอกบ้าน','2025-12-27 14:48:49','2025-12-27 14:48:49'),(5,'เกียจค้าน/ขาดวินัย','2025-12-27 14:49:13','2025-12-27 14:49:13'),(6,'เสี่ยงต่อการกระทำความผิด','2025-12-27 14:49:27','2025-12-27 14:49:27'),(7,'พฤติกรรมชู้สาว','2025-12-27 14:49:42','2025-12-27 14:49:42'),(8,'ใช้กำลังทำร้ายผู้อื่น / ทำลายสิ่งของ','2025-12-27 14:49:56','2025-12-27 14:49:56'),(9,'ฉุนเฉียวง่าย /หุนหันพลันแล่น /เกรี้ยวกราด','2025-12-27 14:50:09','2025-12-27 14:50:09'),(10,'โกหกอยู่เสมอ /โทษผู้อื่น','2025-12-27 14:50:21','2025-12-27 14:50:21'),(11,'ก้าวร้าว /หยาบคาย','2025-12-27 14:50:32','2025-12-27 14:50:32'),(12,'หมกมุ่นในกิจกรรมทางเพศ','2025-12-27 14:50:43','2025-12-27 14:50:43'),(13,'ใช้สารเสพติด','2025-12-27 14:50:54','2025-12-27 14:50:54'),(14,'ทำร้ายตัวเอง','2025-12-27 14:51:10','2025-12-27 14:51:10'),(15,'เก็บตัวรู้สึกว่าตัวเองด้อยกว่าผู้อื่น','2025-12-27 14:51:24','2025-12-27 14:51:24'),(16,'เฉื่อยชา และมีลักษณะคล้ายเหนื่อยตลอดเวลา','2025-12-27 14:51:36','2025-12-27 14:51:36'),(17,'ขาดความมั่นใจ ขี้อาย ขี้กลัว ไม่ค่อยแสดงความรู้สึก','2025-12-27 14:51:48','2025-12-27 14:51:48'),(18,'หนีเรียน','2025-12-27 14:51:58','2025-12-27 14:51:58'),(19,'ไม่กลับบ้านตามเวลา','2025-12-27 14:52:10','2025-12-27 14:52:10'),(20,'โรงเรียนเชิญผู้ปกครอง','2025-12-27 14:52:21','2025-12-27 14:52:21'),(21,'ออกจากระบบการศึกษา','2025-12-27 14:52:32','2025-12-27 14:52:32');
/*!40000 ALTER TABLE `misbehaviors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mothers`
--

DROP TABLE IF EXISTS `mothers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mothers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `income` decimal(10,2) DEFAULT NULL,
  `idcard` varchar(255) DEFAULT NULL,
  `address_no` varchar(255) DEFAULT NULL,
  `moo` varchar(255) DEFAULT NULL,
  `soi` varchar(255) DEFAULT NULL,
  `road` varchar(255) DEFAULT NULL,
  `village` varchar(255) DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `province_id` bigint(20) unsigned DEFAULT NULL,
  `district_id` bigint(20) unsigned DEFAULT NULL,
  `sub_district_id` bigint(20) unsigned DEFAULT NULL,
  `zipcode` int(10) unsigned DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mothers_client_id_unique` (`client_id`),
  CONSTRAINT `mothers_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mothers`
--

LOCK TABLES `mothers` WRITE;
/*!40000 ALTER TABLE `mothers` DISABLE KEYS */;
/*!40000 ALTER TABLE `mothers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nationals`
--

DROP TABLE IF EXISTS `nationals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nationals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `national_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nationals`
--

LOCK TABLES `nationals` WRITE;
/*!40000 ALTER TABLE `nationals` DISABLE KEYS */;
INSERT INTO `nationals` VALUES (1,'ไทย',NULL,NULL),(2,'พม่า',NULL,NULL),(3,'ลาว',NULL,NULL),(4,'กัมพูชา',NULL,NULL),(5,'เวียดนาม',NULL,NULL),(6,'มาเลเซีย',NULL,NULL),(7,'อินโดนีเซีย',NULL,NULL),(8,'ฟิลิปปินส์',NULL,NULL),(9,'อื่น ๆ ',NULL,NULL),(10,'ไม่มีสัญชาติ',NULL,NULL),(11,'ไม่ทราบข้อมูล',NULL,NULL);
/*!40000 ALTER TABLE `nationals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nutrition_assessments`
--

DROP TABLE IF EXISTS `nutrition_assessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nutrition_assessments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `assessment_date` date NOT NULL COMMENT 'วันที่ชั่งวัด',
  `birth_date` date DEFAULT NULL COMMENT 'วันเกิด',
  `age_year` tinyint(3) unsigned DEFAULT NULL,
  `age_month` tinyint(3) unsigned DEFAULT NULL,
  `gender` enum('male','female') NOT NULL,
  `height_cm` decimal(5,2) DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `bmi` decimal(6,2) DEFAULT NULL,
  `bmi_result` varchar(255) DEFAULT NULL,
  `ibw` decimal(6,2) DEFAULT NULL,
  `ibw_percent` decimal(6,2) DEFAULT NULL,
  `ha_median` decimal(6,2) DEFAULT NULL,
  `ha_percent` decimal(6,2) DEFAULT NULL,
  `height_result` varchar(255) DEFAULT NULL,
  `weight_result` varchar(255) DEFAULT NULL,
  `summary_result` varchar(255) DEFAULT NULL,
  `nutrition_status` varchar(255) DEFAULT NULL,
  `height_for_age_result` varchar(255) DEFAULT NULL,
  `weight_for_height_result` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nutrition_assessments_created_by_foreign` (`created_by`),
  KEY `nutrition_assessments_updated_by_foreign` (`updated_by`),
  KEY `nutrition_assessments_client_id_assessment_date_index` (`client_id`,`assessment_date`),
  CONSTRAINT `nutrition_assessments_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nutrition_assessments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `nutrition_assessments_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nutrition_assessments`
--

LOCK TABLES `nutrition_assessments` WRITE;
/*!40000 ALTER TABLE `nutrition_assessments` DISABLE KEYS */;
/*!40000 ALTER TABLE `nutrition_assessments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nutrition_growth_standards`
--

DROP TABLE IF EXISTS `nutrition_growth_standards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nutrition_growth_standards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gender` enum('male','female') NOT NULL,
  `age_month` smallint(5) unsigned DEFAULT NULL,
  `height_cm` decimal(5,2) DEFAULT NULL,
  `standard_type` enum('height_for_age','weight_for_height') NOT NULL,
  `sd_minus_3` decimal(6,2) DEFAULT NULL,
  `sd_minus_2` decimal(6,2) DEFAULT NULL,
  `sd_minus_1_5` decimal(6,2) DEFAULT NULL,
  `median` decimal(6,2) DEFAULT NULL,
  `sd_plus_1_5` decimal(6,2) DEFAULT NULL,
  `sd_plus_2` decimal(6,2) DEFAULT NULL,
  `sd_plus_3` decimal(6,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nutrition_growth_standards_gender_age_month_standard_type_index` (`gender`,`age_month`,`standard_type`),
  KEY `nutrition_growth_standards_gender_height_cm_standard_type_index` (`gender`,`height_cm`,`standard_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nutrition_growth_standards`
--

LOCK TABLES `nutrition_growth_standards` WRITE;
/*!40000 ALTER TABLE `nutrition_growth_standards` DISABLE KEYS */;
/*!40000 ALTER TABLE `nutrition_growth_standards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `observe_followups`
--

DROP TABLE IF EXISTS `observe_followups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `observe_followups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `observe_id` bigint(20) unsigned NOT NULL,
  `followup_date` date NOT NULL,
  `followup_count` int(11) NOT NULL,
  `followup_action` text DEFAULT NULL,
  `followup_result` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `observe_followups_observe_id_foreign` (`observe_id`),
  CONSTRAINT `observe_followups_observe_id_foreign` FOREIGN KEY (`observe_id`) REFERENCES `observes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `observe_followups`
--

LOCK TABLES `observe_followups` WRITE;
/*!40000 ALTER TABLE `observe_followups` DISABLE KEYS */;
/*!40000 ALTER TABLE `observe_followups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `observes`
--

DROP TABLE IF EXISTS `observes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `observes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `behavior` text DEFAULT NULL,
  `cause` text DEFAULT NULL,
  `solution` text DEFAULT NULL,
  `action` text DEFAULT NULL,
  `obstacles` text DEFAULT NULL,
  `result` text DEFAULT NULL,
  `record_date` date DEFAULT NULL,
  `recorder` varchar(100) DEFAULT NULL,
  `misbehavior_id` bigint(20) unsigned NOT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `observes_client_id_foreign` (`client_id`),
  KEY `observes_misbehavior_id_foreign` (`misbehavior_id`),
  CONSTRAINT `observes_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `observes_misbehavior_id_foreign` FOREIGN KEY (`misbehavior_id`) REFERENCES `misbehaviors` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `observes`
--

LOCK TABLES `observes` WRITE;
/*!40000 ALTER TABLE `observes` DISABLE KEYS */;
/*!40000 ALTER TABLE `observes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `occupations`
--

DROP TABLE IF EXISTS `occupations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `occupations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `occupation_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `occupations`
--

LOCK TABLES `occupations` WRITE;
/*!40000 ALTER TABLE `occupations` DISABLE KEYS */;
INSERT INTO `occupations` VALUES (1,'รับจ้าง',NULL,NULL),(2,'ลูกจ้างเอกชน',NULL,NULL),(3,'ลูกจ้างภาครัฐ',NULL,NULL),(4,'รับราชการ',NULL,NULL),(5,'ค้าขาย',NULL,NULL),(6,'อาชีพอิสระ',NULL,NULL),(7,'เจ้าของกิจการ',NULL,NULL),(8,'เกษตรกรรม',NULL,NULL),(9,'ประมง',NULL,NULL),(10,'ไม่ได้ประกอบอาชีพ',NULL,NULL),(11,'ไม่มีข้อมูล',NULL,NULL),(12,'อื่น ๆ ',NULL,NULL);
/*!40000 ALTER TABLE `occupations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operations`
--

DROP TABLE IF EXISTS `operations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `operation_date` date NOT NULL COMMENT 'วันที่ปฏิบัติงาน',
  `sequence_no` int(10) unsigned NOT NULL COMMENT 'ครั้งที่',
  `subject` varchar(500) NOT NULL COMMENT 'เรื่องที่ดำเนินงาน',
  `result` text DEFAULT NULL COMMENT 'ผลการดำเนินงาน',
  `remark` text DEFAULT NULL COMMENT 'หมายเหตุ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `operations_user_date_sequence_unique` (`user_id`,`operation_date`,`sequence_no`),
  KEY `operations_operation_date_index` (`operation_date`),
  KEY `operations_user_id_operation_date_index` (`user_id`,`operation_date`),
  CONSTRAINT `operations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operations`
--

LOCK TABLES `operations` WRITE;
/*!40000 ALTER TABLE `operations` DISABLE KEYS */;
/*!40000 ALTER TABLE `operations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outsides`
--

DROP TABLE IF EXISTS `outsides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `outsides` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `outside_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outsides`
--

LOCK TABLES `outsides` WRITE;
/*!40000 ALTER TABLE `outsides` DISABLE KEYS */;
INSERT INTO `outsides` VALUES (2,'เข้ารับการศึกษาเล่าเรียน','2026-01-08 10:07:02','2026-01-08 11:12:13'),(3,'เข้ารับการฝึกอบรม/ฝึกอาชีพ','2026-01-08 10:07:16','2026-01-08 10:07:16'),(4,'เข้ารับการบำบัด/ฟื้นฟู','2026-01-08 10:07:29','2026-01-08 10:07:29'),(5,'เข้ารับการแก้ไขความประพฤติ','2026-01-08 10:07:41','2026-01-08 10:07:41'),(6,'อยู่ในสถานพินิจฯ','2026-01-08 10:07:52','2026-01-08 10:07:52'),(7,'อื่น ๆ','2026-01-08 10:08:04','2026-01-08 10:08:04');
/*!40000 ALTER TABLE `outsides` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `problems`
--

DROP TABLE IF EXISTS `problems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `problems` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `problem_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `problems`
--

LOCK TABLES `problems` WRITE;
/*!40000 ALTER TABLE `problems` DISABLE KEYS */;
INSERT INTO `problems` VALUES (1,'เร่ร่อน','2026-04-25 22:50:58','2026-04-25 22:50:58'),(2,'ถูกทอดทิ้ง','2026-04-25 22:50:58','2026-04-25 22:50:58'),(3,'ถูกเลี้ยงดูไม่เหมาะสม','2026-04-25 22:50:58','2026-04-25 22:50:58'),(4,'ถูกทารุณกรรม','2026-04-25 22:50:58','2026-04-25 22:50:58'),(5,'ถูกกระทำความรุนแรงในครอบครัว','2026-04-25 22:50:58','2026-04-25 22:50:58'),(6,'ถูกแสวงหาประโยชน์','2026-04-25 22:50:58','2026-04-25 22:50:58'),(7,'เหยื่อค้ามนุษย์','2026-04-25 22:50:58','2026-04-25 22:50:58'),(8,'กำพร้าบิดา','2026-04-25 22:50:58','2026-04-25 22:50:58'),(9,'กำพร้ามารดา','2026-04-25 22:50:58','2026-04-25 22:50:58'),(10,'กำพร้าบิดามารดา','2026-04-25 22:50:58','2026-04-25 22:50:58'),(11,'ปัญหาความประพฤติ','2026-04-25 22:50:58','2026-04-25 22:50:58'),(12,'ครอบครัวแตกแยก','2026-04-25 22:50:58','2026-04-25 22:50:58'),(13,'บิดาหรือมารดาถูกต้องโทษ','2026-04-25 22:50:58','2026-04-25 22:50:58'),(14,'ถูกล่อลวง','2026-04-25 22:50:58','2026-04-25 22:50:58'),(15,'ถูกกระทำทารุณกรรมทางเพศ','2026-04-25 22:50:58','2026-04-25 22:50:58'),(16,'อยู่ในสภาวะยากลำบาก','2026-04-25 22:50:58','2026-04-25 22:50:58'),(17,'ไม่มีสถานะทางทะเบียนราษฎร','2026-04-25 22:50:58','2026-04-25 22:50:58');
/*!40000 ALTER TABLE `problems` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1,'มูลนิธิคุณพ่อเรย์',NULL,NULL),(2,'มูลนิมือต่อมือ',NULL,NULL),(3,'สถานคุ้ครองเด็กและเหยื่อการค้ามนุษย์ (บ้านครูจา)',NULL,NULL),(4,'มูลนิธิบ้านจริงใจ',NULL,NULL),(5,'โรงเรียนเด็กพิเศษพระมหาไถ่',NULL,NULL),(6,'ศูนย์พัฒนาเด็กเล็กพระมหาไถ่',NULL,NULL);
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provinces`
--

DROP TABLE IF EXISTS `provinces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `provinces` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `prov_code` varchar(255) DEFAULT NULL,
  `prov_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provinces`
--

LOCK TABLES `provinces` WRITE;
/*!40000 ALTER TABLE `provinces` DISABLE KEYS */;
INSERT INTO `provinces` VALUES (1,NULL,'กรุงเทพมหานคร','2026-04-25 21:52:52','2026-04-25 21:52:52'),(2,NULL,'สมุทรปราการ','2026-04-25 21:52:52','2026-04-25 21:52:52'),(3,NULL,'นนทบุรี','2026-04-25 21:52:52','2026-04-25 21:52:52'),(4,NULL,'ปทุมธานี','2026-04-25 21:52:52','2026-04-25 21:52:52'),(5,NULL,'พระนครศรีอยุธยา','2026-04-25 21:52:52','2026-04-25 21:52:52'),(6,NULL,'อ่างทอง','2026-04-25 21:52:52','2026-04-25 21:52:52'),(7,NULL,'ลพบุรี','2026-04-25 21:52:52','2026-04-25 21:52:52'),(8,NULL,'สิงห์บุรี','2026-04-25 21:52:52','2026-04-25 21:52:52'),(9,NULL,'ชัยนาท','2026-04-25 21:52:52','2026-04-25 21:52:52'),(10,NULL,'สระบุรี','2026-04-25 21:52:52','2026-04-25 21:52:52'),(11,NULL,'ชลบุรี','2026-04-25 21:52:52','2026-04-25 21:52:52'),(12,NULL,'ระยอง','2026-04-25 21:52:52','2026-04-25 21:52:52'),(13,NULL,'จันทบุรี','2026-04-25 21:52:52','2026-04-25 21:52:52'),(14,NULL,'ตราด','2026-04-25 21:52:52','2026-04-25 21:52:52'),(15,NULL,'ฉะเชิงเทรา','2026-04-25 21:52:52','2026-04-25 21:52:52'),(16,NULL,'ปราจีนบุรี','2026-04-25 21:52:52','2026-04-25 21:52:52'),(17,NULL,'นครนายก','2026-04-25 21:52:52','2026-04-25 21:52:52'),(18,NULL,'สระแก้ว','2026-04-25 21:52:52','2026-04-25 21:52:52'),(19,NULL,'นครราชสีมา','2026-04-25 21:52:52','2026-04-25 21:52:52'),(20,NULL,'บุรีรัมย์','2026-04-25 21:52:52','2026-04-25 21:52:52'),(21,NULL,'สุรินทร์','2026-04-25 21:52:52','2026-04-25 21:52:52'),(22,NULL,'ศรีสะเกษ','2026-04-25 21:52:52','2026-04-25 21:52:52'),(23,NULL,'อุบลราชธานี','2026-04-25 21:52:52','2026-04-25 21:52:52'),(24,NULL,'ยโสธร','2026-04-25 21:52:52','2026-04-25 21:52:52'),(25,NULL,'ชัยภูมิ','2026-04-25 21:52:52','2026-04-25 21:52:52'),(26,NULL,'อำนาจเจริญ','2026-04-25 21:52:52','2026-04-25 21:52:52'),(27,NULL,'บึงกาฬ','2026-04-25 21:52:52','2026-04-25 21:52:52'),(28,NULL,'หนองบัวลำภู','2026-04-25 21:52:52','2026-04-25 21:52:52'),(29,NULL,'ขอนแก่น','2026-04-25 21:52:52','2026-04-25 21:52:52'),(30,NULL,'อุดรธานี','2026-04-25 21:52:52','2026-04-25 21:52:52'),(31,NULL,'เลย','2026-04-25 21:52:52','2026-04-25 21:52:52'),(32,NULL,'หนองคาย','2026-04-25 21:52:52','2026-04-25 21:52:52'),(33,NULL,'มหาสารคาม','2026-04-25 21:52:52','2026-04-25 21:52:52'),(34,NULL,'ร้อยเอ็ด','2026-04-25 21:52:52','2026-04-25 21:52:52'),(35,NULL,'กาฬสินธุ์','2026-04-25 21:52:52','2026-04-25 21:52:52'),(36,NULL,'สกลนคร','2026-04-25 21:52:52','2026-04-25 21:52:52'),(37,NULL,'นครพนม','2026-04-25 21:52:52','2026-04-25 21:52:52'),(38,NULL,'มุกดาหาร','2026-04-25 21:52:52','2026-04-25 21:52:52'),(39,NULL,'เชียงใหม่','2026-04-25 21:52:52','2026-04-25 21:52:52'),(40,NULL,'ลำพูน','2026-04-25 21:52:52','2026-04-25 21:52:52'),(41,NULL,'ลำปาง','2026-04-25 21:52:52','2026-04-25 21:52:52'),(42,NULL,'อุตรดิตถ์','2026-04-25 21:52:52','2026-04-25 21:52:52'),(43,NULL,'แพร่','2026-04-25 21:52:52','2026-04-25 21:52:52'),(44,NULL,'น่าน','2026-04-25 21:52:52','2026-04-25 21:52:52'),(45,NULL,'พะเยา','2026-04-25 21:52:52','2026-04-25 21:52:52'),(46,NULL,'เชียงราย','2026-04-25 21:52:52','2026-04-25 21:52:52'),(47,NULL,'แม่ฮ่องสอน','2026-04-25 21:52:52','2026-04-25 21:52:52'),(48,NULL,'นครสวรรค์','2026-04-25 21:52:52','2026-04-25 21:52:52'),(49,NULL,'อุทัยธานี','2026-04-25 21:52:52','2026-04-25 21:52:52'),(50,NULL,'กำแพงเพชร','2026-04-25 21:52:52','2026-04-25 21:52:52'),(51,NULL,'ตาก','2026-04-25 21:52:52','2026-04-25 21:52:52'),(52,NULL,'สุโขทัย','2026-04-25 21:52:52','2026-04-25 21:52:52'),(53,NULL,'พิษณุโลก','2026-04-25 21:52:52','2026-04-25 21:52:52'),(54,NULL,'พิจิตร','2026-04-25 21:52:52','2026-04-25 21:52:52'),(55,NULL,'เพชรบูรณ์','2026-04-25 21:52:52','2026-04-25 21:52:52'),(56,NULL,'ราชบุรี','2026-04-25 21:52:52','2026-04-25 21:52:52'),(57,NULL,'กาญจนบุรี','2026-04-25 21:52:52','2026-04-25 21:52:52'),(58,NULL,'สุพรรณบุรี','2026-04-25 21:52:52','2026-04-25 21:52:52'),(59,NULL,'นครปฐม','2026-04-25 21:52:52','2026-04-25 21:52:52'),(60,NULL,'สมุทรสาคร','2026-04-25 21:52:52','2026-04-25 21:52:52'),(61,NULL,'สมุทรสงคราม','2026-04-25 21:52:52','2026-04-25 21:52:52'),(62,NULL,'เพชรบุรี','2026-04-25 21:52:52','2026-04-25 21:52:52'),(63,NULL,'ประจวบคีรีขันธ์','2026-04-25 21:52:52','2026-04-25 21:52:52'),(64,NULL,'นครศรีธรรมราช','2026-04-25 21:52:52','2026-04-25 21:52:52'),(65,NULL,'กระบี่','2026-04-25 21:52:52','2026-04-25 21:52:52'),(66,NULL,'พังงา','2026-04-25 21:52:52','2026-04-25 21:52:52'),(67,NULL,'ภูเก็ต','2026-04-25 21:52:52','2026-04-25 21:52:52'),(68,NULL,'สุราษฎร์ธานี','2026-04-25 21:52:52','2026-04-25 21:52:52'),(69,NULL,'ระนอง','2026-04-25 21:52:52','2026-04-25 21:52:52'),(70,NULL,'ชุมพร','2026-04-25 21:52:52','2026-04-25 21:52:52'),(71,NULL,'สงขลา','2026-04-25 21:52:52','2026-04-25 21:52:52'),(72,NULL,'สตูล','2026-04-25 21:52:52','2026-04-25 21:52:52'),(73,NULL,'ตรัง','2026-04-25 21:52:52','2026-04-25 21:52:52'),(74,NULL,'พัทลุง','2026-04-25 21:52:52','2026-04-25 21:52:52'),(75,NULL,'ปัตตานี','2026-04-25 21:52:52','2026-04-25 21:52:52'),(76,NULL,'ยะลา','2026-04-25 21:52:52','2026-04-25 21:52:52'),(77,NULL,'นราธิวาส','2026-04-25 21:52:52','2026-04-25 21:52:52');
/*!40000 ALTER TABLE `provinces` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `psychiatrics`
--

DROP TABLE IF EXISTS `psychiatrics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `psychiatrics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sent_date` date DEFAULT NULL,
  `hotpital` varchar(255) DEFAULT NULL,
  `psycho_id` bigint(20) unsigned DEFAULT NULL,
  `diagnose` text DEFAULT NULL,
  `appoin_date` date DEFAULT NULL,
  `drug_no` varchar(255) DEFAULT NULL,
  `drug_name` varchar(255) DEFAULT NULL,
  `disa_no` varchar(255) DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `psychiatrics_client_id_foreign` (`client_id`),
  KEY `psychiatrics_psycho_id_foreign` (`psycho_id`),
  CONSTRAINT `psychiatrics_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `psychiatrics_psycho_id_foreign` FOREIGN KEY (`psycho_id`) REFERENCES `psychos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `psychiatrics`
--

LOCK TABLES `psychiatrics` WRITE;
/*!40000 ALTER TABLE `psychiatrics` DISABLE KEYS */;
/*!40000 ALTER TABLE `psychiatrics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `psychos`
--

DROP TABLE IF EXISTS `psychos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `psychos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `psycho_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `psychos`
--

LOCK TABLES `psychos` WRITE;
/*!40000 ALTER TABLE `psychos` DISABLE KEYS */;
INSERT INTO `psychos` VALUES (1,'เชาว์ปัญญาต่ำ (Mental Retardation (MR))','2026-05-02 13:26:05','2026-05-02 13:26:05'),(2,'ความผิดปกติด้านการสื่อสาร (Communication Disorders)','2026-05-02 13:26:05','2026-05-02 13:26:05'),(3,'ความผิดปกติเกี่ยวกับกระบวนการเรียนรู้ (Learning Disorder (LD))','2026-05-02 13:26:05','2026-05-02 13:26:05'),(4,'พัฒนาการด้านสังคมผิดปกติ (Autistic Spectrum Disorder (ASD))','2026-05-02 13:26:05','2026-05-02 13:26:05'),(5,'สมาธิสั้น (Attention Deficit Hyperactivity Disorder (ADHD))','2026-05-02 13:26:05','2026-05-02 13:26:05'),(6,'โรคซึมเศร้า (Major Depressive Disorder (MDD))','2026-05-02 13:26:05','2026-05-02 13:26:05'),(7,'โรควิตกกังวล (Anxiety Disorders)','2026-05-02 13:26:05','2026-05-02 13:26:05'),(8,'โรคอารมณ์แปรปรวน (Bipolar Disorders)','2026-05-02 13:26:05','2026-05-02 13:26:05');
/*!40000 ALTER TABLE `psychos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publicizes`
--

DROP TABLE IF EXISTS `publicizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publicizes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `recorded_at` date NOT NULL COMMENT 'วันที่บันทึก',
  `category` varchar(50) NOT NULL COMMENT 'ประเภทข้อมูล',
  `title` varchar(255) NOT NULL COMMENT 'ชื่อเรื่อง',
  `file_path` varchar(255) NOT NULL COMMENT 'path ไฟล์ pdf',
  `file_name` varchar(255) DEFAULT NULL COMMENT 'ชื่อไฟล์เดิม',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `publicizes_category_index` (`category`),
  KEY `publicizes_recorded_at_index` (`recorded_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publicizes`
--

LOCK TABLES `publicizes` WRITE;
/*!40000 ALTER TABLE `publicizes` DISABLE KEYS */;
/*!40000 ALTER TABLE `publicizes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `refers`
--

DROP TABLE IF EXISTS `refers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `refers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `refer_date` date NOT NULL,
  `translate_id` bigint(20) unsigned NOT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `guardian` enum('มี','ไม่มี') NOT NULL,
  `parent_name` varchar(255) DEFAULT NULL,
  `parent_tel` varchar(255) DEFAULT NULL,
  `member` varchar(255) DEFAULT NULL,
  `teacher` varchar(255) DEFAULT NULL,
  `committee_result` varchar(255) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `meeting_report_file` varchar(255) DEFAULT NULL,
  `approve_status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `refers_client_id_foreign` (`client_id`),
  KEY `refers_translate_id_foreign` (`translate_id`),
  CONSTRAINT `refers_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `refers_translate_id_foreign` FOREIGN KEY (`translate_id`) REFERENCES `translates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `refers`
--

LOCK TABLES `refers` WRITE;
/*!40000 ALTER TABLE `refers` DISABLE KEYS */;
/*!40000 ALTER TABLE `refers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `relatives`
--

DROP TABLE IF EXISTS `relatives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relatives` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `income` decimal(10,2) DEFAULT NULL,
  `idcard` varchar(255) DEFAULT NULL,
  `address_no` varchar(255) DEFAULT NULL,
  `moo` varchar(255) DEFAULT NULL,
  `soi` varchar(255) DEFAULT NULL,
  `road` varchar(255) DEFAULT NULL,
  `village` varchar(255) DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `province_id` bigint(20) unsigned DEFAULT NULL,
  `district_id` bigint(20) unsigned DEFAULT NULL,
  `sub_district_id` bigint(20) unsigned DEFAULT NULL,
  `zipcode` int(10) unsigned DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `relatives_client_id_unique` (`client_id`),
  CONSTRAINT `relatives_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `relatives`
--

LOCK TABLES `relatives` WRITE;
/*!40000 ALTER TABLE `relatives` DISABLE KEYS */;
/*!40000 ALTER TABLE `relatives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `religions`
--

DROP TABLE IF EXISTS `religions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `religions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `religion_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `religions`
--

LOCK TABLES `religions` WRITE;
/*!40000 ALTER TABLE `religions` DISABLE KEYS */;
INSERT INTO `religions` VALUES (1,'พุทธ',NULL,NULL),(2,'คริสต์',NULL,NULL),(3,'อิสลาม',NULL,NULL),(4,'อื่น ๆ',NULL,NULL),(5,'ไม่มีข้อมูล',NULL,NULL);
/*!40000 ALTER TABLE `religions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `retires`
--

DROP TABLE IF EXISTS `retires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `retires` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `retire_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `retires`
--

LOCK TABLES `retires` WRITE;
/*!40000 ALTER TABLE `retires` DISABLE KEYS */;
INSERT INTO `retires` VALUES (1,'หลบหนี',NULL,NULL),(2,'พลัดหลง',NULL,NULL),(3,'ไม่กลับบ้าน',NULL,NULL),(4,'อื่น ๆ',NULL,NULL);
/*!40000 ALTER TABLE `retires` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `scholarship_children`
--

DROP TABLE IF EXISTS `scholarship_children`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scholarship_children` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `age` tinyint(3) unsigned DEFAULT NULL,
  `education_level` varchar(255) DEFAULT NULL,
  `school_name` varchar(255) DEFAULT NULL,
  `academic_year` varchar(255) NOT NULL,
  `current_address` text DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `help_needed` text DEFAULT NULL,
  `more_detail` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scholarship_children`
--

LOCK TABLES `scholarship_children` WRITE;
/*!40000 ALTER TABLE `scholarship_children` DISABLE KEYS */;
INSERT INTO `scholarship_children` VALUES (12,'นายสุชาติ','สุทธินาค','male',15,'มัธยมศึกษาปีที่ 1','โพธิสัมพันธ์วิทยาคาร (เมืองพัทยา 42)','2569','25','นายปรีดา ใชดี','0892536256','ทดสอบ','เทสระบบ','ทดสอบระบบ','upload/scholarship_children/6c542165-095f-4f35-9f07-3e6abea31d75.jpg','2026-07-01 05:15:51','2026-07-28 12:55:05'),(13,'นางสาวปัทมา','แสงทอง','female',18,'ปริญญาตรีชั้นปีที่ 1','มหาวิทยาลัยแม่โจ้','2569','52/35 ตำบลหนองปรือ อำเภอบางละมุง จังหวัดชลบุรี','นายปรีดา ใชดี','0869783512','ยากจน','ทุนการศึกษา','เร่งด่วน','upload/scholarship_children/f0638c25-0c4e-4062-9d2d-59f32e1b8a49.jpg','2026-07-01 05:28:38','2026-07-01 05:28:38');
/*!40000 ALTER TABLE `scholarship_children` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `scholarship_donations`
--

DROP TABLE IF EXISTS `scholarship_donations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scholarship_donations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `scholarship_id` bigint(20) unsigned NOT NULL,
  `donation_date` date DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `donation_type` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `recorded_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scholarship_donations_scholarship_id_foreign` (`scholarship_id`),
  CONSTRAINT `scholarship_donations_scholarship_id_foreign` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scholarship_donations`
--

LOCK TABLES `scholarship_donations` WRITE;
/*!40000 ALTER TABLE `scholarship_donations` DISABLE KEYS */;
INSERT INTO `scholarship_donations` VALUES (3,7,'2026-07-01',5000.00,'เงินสด','เทส','Admin','2026-07-01 05:17:59','2026-07-01 05:17:59');
/*!40000 ALTER TABLE `scholarship_donations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `scholarships`
--

DROP TABLE IF EXISTS `scholarships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scholarships` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) NOT NULL,
  `support_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`support_types`)),
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `detail` text DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scholarships`
--

LOCK TABLES `scholarships` WRITE;
/*!40000 ALTER TABLE `scholarships` DISABLE KEYS */;
INSERT INTO `scholarships` VALUES (7,'นายสุชาติ สุทธินาค','[\"\\u0e17\\u0e38\\u0e19\\u0e01\\u0e32\\u0e23\\u0e28\\u0e36\\u0e01\\u0e29\\u0e32\",\"\\u0e0a\\u0e38\\u0e14\\u0e19\\u0e31\\u0e01\\u0e40\\u0e23\\u0e35\\u0e22\\u0e19\",\"\\u0e2d\\u0e38\\u0e1b\\u0e01\\u0e23\\u0e13\\u0e4c\\u0e01\\u0e32\\u0e23\\u0e40\\u0e23\\u0e35\\u0e22\\u0e19\"]','0885346727','teacher@gmail.com','ทดสอบ',1,'2026-07-01 05:17:23','2026-07-01 04:26:07','2026-07-01 05:17:23'),(8,'นายสุชาติ สุทธินาค','[\"\\u0e17\\u0e38\\u0e19\\u0e01\\u0e32\\u0e23\\u0e28\\u0e36\\u0e01\\u0e29\\u0e32\"]','0869783512','admin@gmail.com',NULL,1,'2026-07-01 09:07:09','2026-07-01 05:31:35','2026-07-01 09:07:09');
/*!40000 ALTER TABLE `scholarships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_followups`
--

DROP TABLE IF EXISTS `school_followups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `school_followups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `follow_date` date NOT NULL,
  `teacher_name` varchar(255) DEFAULT NULL,
  `tel` varchar(255) DEFAULT NULL,
  `follow_type` varchar(255) NOT NULL,
  `follo_no` tinyint(4) DEFAULT NULL,
  `result` text DEFAULT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `education_record_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `school_followups_client_id_foreign` (`client_id`),
  KEY `school_followups_education_record_id_foreign` (`education_record_id`),
  CONSTRAINT `school_followups_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `school_followups_education_record_id_foreign` FOREIGN KEY (`education_record_id`) REFERENCES `education_records` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_followups`
--

LOCK TABLES `school_followups` WRITE;
/*!40000 ALTER TABLE `school_followups` DISABLE KEYS */;
/*!40000 ALTER TABLE `school_followups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `semesters`
--

DROP TABLE IF EXISTS `semesters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `semesters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `semester_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `semesters`
--

LOCK TABLES `semesters` WRITE;
/*!40000 ALTER TABLE `semesters` DISABLE KEYS */;
INSERT INTO `semesters` VALUES (6,'1/2560','2026-02-21 11:07:18','2026-02-21 11:07:18'),(7,'2/2560','2026-02-21 11:07:30','2026-02-21 11:07:30'),(8,'1/2561','2026-02-21 11:07:46','2026-02-21 11:07:46'),(9,'2/2561','2026-02-21 11:08:01','2026-02-21 11:08:01'),(10,'1/2562','2026-02-21 11:08:18','2026-02-21 11:08:18'),(11,'2/2562','2026-02-21 11:08:27','2026-02-21 11:08:27'),(12,'1/2563','2026-02-21 11:08:45','2026-02-21 11:08:45'),(13,'2/2563','2026-02-21 11:08:53','2026-02-21 11:08:53'),(14,'1/2564','2026-02-21 11:09:02','2026-02-21 11:09:02'),(15,'2/2564','2026-02-21 11:09:10','2026-02-21 11:09:10'),(16,'1/2565','2026-02-21 11:09:19','2026-02-21 11:09:19'),(17,'2/2565','2026-02-21 11:09:27','2026-02-21 11:09:27'),(18,'1/2566','2026-02-21 11:09:36','2026-02-21 11:09:36'),(19,'2/2566','2026-02-21 11:09:43','2026-02-21 11:09:43'),(20,'1/2567','2026-02-21 11:09:51','2026-02-21 11:09:51'),(21,'2/2567','2026-02-21 11:09:58','2026-02-21 11:09:58'),(22,'1/2568','2026-02-21 11:10:09','2026-02-21 11:10:09'),(23,'2/2568','2026-02-21 11:10:16','2026-02-21 11:10:16'),(24,'1/2569','2026-02-21 11:10:25','2026-02-21 11:10:25'),(25,'2/2569','2026-02-21 11:10:32','2026-02-21 11:10:32');
/*!40000 ALTER TABLE `semesters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('d5EQrn4qMXfqw1GqLJLIvByWbscBIxowtYoXdJuj',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOWxNajhVenh6SVp1TWY3aUJ6eUtKMFlzRkZESlRzNXQ3eGNERXJ6bSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM2OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vY2hpbGQtYW5hbHl0aWNzLXJlcG9ydD9hZ2VfbWF4PSZhZ2VfbWluPSZlZHVjYXRpb25fZW5kPSZlZHVjYXRpb25fc3RhcnQ9JmVuZF9kYXRlPTIwMjYtMDctMjUmZ2VuZGVyPSZob3VzZV9pZD1hbGwmaW5zdGl0dXRpb25faWQ9JnByb2JsZW1faWQ9JnByb2plY3RfaWQ9YWxsJnJlbGVhc2Vfc3RhdHVzPWFsbCZzdGFydF9kYXRlPTE5NjgtMDEtMDEmdGFyZ2V0X2lkPSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==',1784993663),('ix4uBuvBATOL2BmbgIxZBsG3VDwKRlPtaqyz3636',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.1 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNkNVVGNDOGlhaEZuYVFTMDdiN3B6MmVDMzdxb0MwOWtzUXdDNTVMZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1784990815),('jNXaLXw9tPwvYiMfvICmz158T6uvOFZIB1TnRAWU',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoidHJrRmxXUXpQbEhKTGRVbGluajdwdTdOUnppTkEzcWZpRGRnak14eSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9jaGlsZC1hbmFseXRpY3MtcmVwb3J0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1785163240),('lIO7VqiRrlvACwenfbv2N9T842ZIwoPhu0LFW07h',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZ3l6V0xadmxDVjgxNGRMN3ZwUVphcGN1bmZPTVN0cjZNMUNFNzJlTSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hYm91dCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==',1785081229),('uXfKCc9r1VlmVB6age7engKVKBUmeQns60VUg9oK',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiV3ZkU0NJQmRJYWhBVEY0Nk1VUnZHZDl2TGJEa21lTXVOMkF1dmxzdSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9lZHVjYXRpb24tcmVjb3JkL2VkaXQvOSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==',1785246560);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `snap_iv_screening_items`
--

DROP TABLE IF EXISTS `snap_iv_screening_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `snap_iv_screening_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `snap_iv_screening_id` bigint(20) unsigned NOT NULL,
  `category` varchar(255) NOT NULL,
  `item_no` tinyint(3) unsigned NOT NULL,
  `question` text NOT NULL,
  `score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `snap_iv_screening_items_snap_iv_screening_id_foreign` (`snap_iv_screening_id`),
  CONSTRAINT `snap_iv_screening_items_snap_iv_screening_id_foreign` FOREIGN KEY (`snap_iv_screening_id`) REFERENCES `snap_iv_screenings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `snap_iv_screening_items`
--

LOCK TABLES `snap_iv_screening_items` WRITE;
/*!40000 ALTER TABLE `snap_iv_screening_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `snap_iv_screening_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `snap_iv_screenings`
--

DROP TABLE IF EXISTS `snap_iv_screenings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `snap_iv_screenings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `screening_date` date NOT NULL,
  `observer_name` varchar(255) DEFAULT NULL,
  `relationship` varchar(255) DEFAULT NULL,
  `age_text` varchar(255) DEFAULT NULL,
  `class_level` varchar(255) DEFAULT NULL,
  `term` varchar(255) DEFAULT NULL,
  `grade_average` varchar(255) DEFAULT NULL,
  `inattention_score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `hyperactivity_score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `oppositional_score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `total_score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `inattention_level` varchar(255) DEFAULT NULL,
  `hyperactivity_level` varchar(255) DEFAULT NULL,
  `oppositional_level` varchar(255) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `recommendation` text DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `snap_iv_screenings_client_id_screening_date_unique` (`client_id`,`screening_date`),
  KEY `snap_iv_screenings_created_by_foreign` (`created_by`),
  CONSTRAINT `snap_iv_screenings_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `snap_iv_screenings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `snap_iv_screenings`
--

LOCK TABLES `snap_iv_screenings` WRITE;
/*!40000 ALTER TABLE `snap_iv_screenings` DISABLE KEYS */;
/*!40000 ALTER TABLE `snap_iv_screenings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spouses`
--

DROP TABLE IF EXISTS `spouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spouses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `income` decimal(10,2) DEFAULT NULL,
  `idcard` varchar(255) DEFAULT NULL,
  `address_no` varchar(255) DEFAULT NULL,
  `moo` varchar(255) DEFAULT NULL,
  `soi` varchar(255) DEFAULT NULL,
  `road` varchar(255) DEFAULT NULL,
  `village` varchar(255) DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `province_id` bigint(20) unsigned DEFAULT NULL,
  `district_id` bigint(20) unsigned DEFAULT NULL,
  `sub_district_id` bigint(20) unsigned DEFAULT NULL,
  `zipcode` int(10) unsigned DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `spouses_client_id_unique` (`client_id`),
  CONSTRAINT `spouses_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spouses`
--

LOCK TABLES `spouses` WRITE;
/*!40000 ALTER TABLE `spouses` DISABLE KEYS */;
/*!40000 ALTER TABLE `spouses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statuses`
--

DROP TABLE IF EXISTS `statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `status_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statuses`
--

LOCK TABLES `statuses` WRITE;
/*!40000 ALTER TABLE `statuses` DISABLE KEYS */;
INSERT INTO `statuses` VALUES (1,'อยู่ในความอุปการะ',NULL,NULL),(2,'เคยอยู่ในความอุปการะ',NULL,NULL),(3,'ผู้รับรายใหม่',NULL,NULL),(4,'อื่น ๆ',NULL,NULL);
/*!40000 ALTER TABLE `statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sub_districts`
--

DROP TABLE IF EXISTS `sub_districts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sub_districts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subd_code` varchar(255) DEFAULT NULL,
  `district_id` bigint(20) unsigned NOT NULL,
  `subd_name` varchar(255) NOT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_districts_district_id_foreign` (`district_id`),
  CONSTRAINT `sub_districts_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7407 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_districts`
--

LOCK TABLES `sub_districts` WRITE;
/*!40000 ALTER TABLE `sub_districts` DISABLE KEYS */;
INSERT INTO `sub_districts` VALUES (1,'100101',1,'พระบรมมหาราชวัง','10200',NULL,NULL),(2,'100102',1,'วังบูรพาภิรมย์','10200',NULL,NULL),(3,'100103',1,'วัดราชบพิธ','10200',NULL,NULL),(4,'100104',1,'สำราญราษฎร์','10200',NULL,NULL),(5,'100105',1,'ศาลเจ้าพ่อเสือ','10200',NULL,NULL),(6,'100106',1,'เสาชิงช้า','10200',NULL,NULL),(7,'100107',1,'บวรนิเวศ','10200',NULL,NULL),(8,'100108',1,'ตลาดยอด','10200',NULL,NULL),(9,'100109',1,'ชนะสงคราม','10200',NULL,NULL),(10,'100110',1,'บ้านพานถม','10200',NULL,NULL),(11,'100111',1,'บางขุนพรหม','10200',NULL,NULL),(12,'100112',1,'วัดสามพระยา','10200',NULL,NULL),(13,'100201',2,'ดุสิต','10300',NULL,NULL),(14,'100202',2,'วชิรพยาบาล','10300',NULL,NULL),(15,'100203',2,'สวนจิตรลดา','10300',NULL,NULL),(16,'100204',2,'สี่แยกมหานาค','10300',NULL,NULL),(17,'100206',2,'ถนนนครไชยศรี','10300',NULL,NULL),(18,'100301',3,'กระทุ่มราย','10530',NULL,NULL),(19,'100302',3,'หนองจอก','10530',NULL,NULL),(20,'100303',3,'คลองสิบ','10530',NULL,NULL),(21,'100304',3,'คลองสิบสอง','10530',NULL,NULL),(22,'100305',3,'โคกแฝด','10530',NULL,NULL),(23,'100306',3,'คู้ฝั่งเหนือ','10530',NULL,NULL),(24,'100307',3,'ลำผักชี','10530',NULL,NULL),(25,'100308',3,'ลำต้อยติ่ง','10530',NULL,NULL),(26,'100401',4,'มหาพฤฒาราม','10500',NULL,NULL),(27,'100402',4,'สีลม','10500',NULL,NULL),(28,'100403',4,'สุริยวงศ์','10500',NULL,NULL),(29,'100404',4,'บางรัก','10500',NULL,NULL),(30,'100405',4,'สี่พระยา','10500',NULL,NULL),(31,'100502',5,'อนุสาวรีย์','10220',NULL,NULL),(32,'100508',5,'ท่าแร้ง','10220',NULL,NULL),(33,'100601',6,'คลองจั่น','10240',NULL,NULL),(34,'100608',6,'หัวหมาก','10240',NULL,NULL),(35,'100701',7,'รองเมือง','10330',NULL,NULL),(36,'100702',7,'วังใหม่','10330',NULL,NULL),(37,'100703',7,'ปทุมวัน','10330',NULL,NULL),(38,'100704',7,'ลุมพินี','10330',NULL,NULL),(39,'100801',8,'ป้อมปราบ','10100',NULL,NULL),(40,'100802',8,'วัดเทพศิรินทร์','10100',NULL,NULL),(41,'100803',8,'คลองมหานาค','10100',NULL,NULL),(42,'100804',8,'บ้านบาตร','10100',NULL,NULL),(43,'100805',8,'วัดโสมนัส','10100',NULL,NULL),(44,'100905',9,'บางจาก','10260',NULL,NULL),(45,'101001',10,'มีนบุรี','10510',NULL,NULL),(46,'101002',10,'แสนแสบ','10510',NULL,NULL),(47,'101101',11,'ลาดกระบัง','10520',NULL,NULL),(48,'101102',11,'คลองสองต้นนุ่น','10520',NULL,NULL),(49,'101103',11,'คลองสามประเวศ','10520',NULL,NULL),(50,'101104',11,'ลำปลาทิว','10520',NULL,NULL),(51,'101105',11,'ทับยาว','10520',NULL,NULL),(52,'101106',11,'ขุมทอง','10520',NULL,NULL),(53,'101203',12,'ช่องนนทรี','10120',NULL,NULL),(54,'101204',12,'บางโพงพาง','10120',NULL,NULL),(55,'101301',13,'จักรวรรดิ','10100',NULL,NULL),(56,'101302',13,'สัมพันธวงศ์','10100',NULL,NULL),(57,'101303',13,'ตลาดน้อย','10100',NULL,NULL),(58,'101401',14,'สามเสนใน','10400',NULL,NULL),(59,'101501',15,'วัดกัลยาณ์','10600',NULL,NULL),(60,'101502',15,'หิรัญรูจี','10600',NULL,NULL),(61,'101503',15,'บางยี่เรือ','10600',NULL,NULL),(62,'101504',15,'บุคคโล','10600',NULL,NULL),(63,'101505',15,'ตลาดพลู','10600',NULL,NULL),(64,'101601',16,'วัดอรุณ','10600',NULL,NULL),(65,'101602',16,'วัดท่าพระ','10600',NULL,NULL),(66,'101701',17,'ห้วยขวาง','10310',NULL,NULL),(67,'101702',17,'บางกะปิ','10310',NULL,NULL),(68,'101704',17,'สามเสนนอก','10310',NULL,NULL),(69,'101801',18,'สมเด็จเจ้าพระยา','10600',NULL,NULL),(70,'101802',18,'คลองสาน','10600',NULL,NULL),(71,'101803',18,'บางลำภูล่าง','10600',NULL,NULL),(72,'101804',18,'คลองต้นไทร','10600',NULL,NULL),(73,'101901',19,'คลองชักพระ','10170',NULL,NULL),(74,'101902',19,'ตลิ่งชัน','10170',NULL,NULL),(75,'101903',19,'ฉิมพลี','10170',NULL,NULL),(76,'101904',19,'บางพรม','10170',NULL,NULL),(77,'101905',19,'บางระมาด','10170',NULL,NULL),(78,'101907',19,'บางเชือกหนัง','10170',NULL,NULL),(79,'102004',20,'ศิริราช','10700',NULL,NULL),(80,'102005',20,'บ้านช่างหล่อ','10700',NULL,NULL),(81,'102006',20,'บางขุนนนท์','10700',NULL,NULL),(82,'102007',20,'บางขุนศรี','10700',NULL,NULL),(83,'102009',20,'อรุณอมรินทร์','10700',NULL,NULL),(84,'102105',21,'ท่าข้าม','10150',NULL,NULL),(85,'102107',21,'แสมดำ','10150',NULL,NULL),(86,'102201',22,'บางหว้า','10160',NULL,NULL),(87,'102202',22,'บางด้วน','10160',NULL,NULL),(88,'102206',22,'บางจาก','10160',NULL,NULL),(89,'102207',22,'บางแวก','10160',NULL,NULL),(90,'102208',22,'คลองขวาง','10160',NULL,NULL),(91,'102209',22,'ปากคลองภาษีเจริญ','10160',NULL,NULL),(92,'102210',22,'คูหาสวรรค์','10160',NULL,NULL),(93,'102302',23,'หนองแขม','10160',NULL,NULL),(94,'102303',23,'หนองค้างพลู','10160',NULL,NULL),(95,'102401',24,'ราษฎร์บูรณะ','10140',NULL,NULL),(96,'102402',24,'บางปะกอก','10140',NULL,NULL),(97,'102501',25,'บางพลัด','10700',NULL,NULL),(98,'102502',25,'บางอ้อ','10700',NULL,NULL),(99,'102503',25,'บางบำหรุ','10700',NULL,NULL),(100,'102504',25,'บางยี่ขัน','10700',NULL,NULL),(101,'102601',26,'ดินแดง','10400',NULL,NULL),(102,'102701',27,'คลองกุ่ม','10240',NULL,NULL),(103,'102801',28,'ทุ่งวัดดอน','10120',NULL,NULL),(104,'102802',28,'ยานนาวา','10120',NULL,NULL),(105,'102803',28,'ทุ่งมหาเมฆ','10120',NULL,NULL),(106,'102901',29,'บางซื่อ','10800',NULL,NULL),(107,'103001',30,'ลาดยาว','10900',NULL,NULL),(108,'103101',31,'บางคอแหลม','10120',NULL,NULL),(109,'103102',31,'วัดพระยาไกร','10120',NULL,NULL),(110,'103103',31,'บางโคล่','10120',NULL,NULL),(111,'103201',32,'ประเวศ','10250',NULL,NULL),(112,'103202',32,'หนองบอน','10250',NULL,NULL),(113,'103203',32,'ดอกไม้','10250',NULL,NULL),(114,'103301',33,'คลองเตย','10110',NULL,NULL),(115,'103302',33,'คลองตัน','10110',NULL,NULL),(116,'103303',33,'พระโขนง','10110',NULL,NULL),(117,'103401',34,'สวนหลวง','10250',NULL,NULL),(118,'103501',35,'บางขุนเทียน','10150',NULL,NULL),(119,'103502',35,'บางค้อ','10150',NULL,NULL),(120,'103503',35,'บางมด','10150',NULL,NULL),(121,'103504',35,'จอมทอง','10150',NULL,NULL),(122,'103602',36,'สีกัน','10210',NULL,NULL),(123,'103701',37,'ทุ่งพญาไท','10400',NULL,NULL),(124,'103702',37,'ถนนพญาไท','10400',NULL,NULL),(125,'103703',37,'ถนนเพชรบุรี','10400',NULL,NULL),(126,'103704',37,'มักกะสัน','10400',NULL,NULL),(127,'103801',38,'ลาดพร้าว','10230',NULL,NULL),(128,'103802',38,'จรเข้บัว','10230',NULL,NULL),(129,'103901',39,'คลองเตยเหนือ','10110',NULL,NULL),(130,'103902',39,'คลองตันเหนือ','10110',NULL,NULL),(131,'103903',39,'พระโขนงเหนือ','10110',NULL,NULL),(132,'104001',40,'บางแค','10160',NULL,NULL),(133,'104002',40,'บางแคเหนือ','10160',NULL,NULL),(134,'104003',40,'บางไผ่','10160',NULL,NULL),(135,'104004',40,'หลักสอง','10160',NULL,NULL),(136,'104101',41,'ทุ่งสองห้อง','10210',NULL,NULL),(137,'104102',41,'ตลาดบางเขน','10210',NULL,NULL),(138,'104201',42,'สายไหม','10220',NULL,NULL),(139,'104202',42,'ออเงิน','10220',NULL,NULL),(140,'104203',42,'คลองถนน','10220',NULL,NULL),(141,'104301',43,'คันนายาว','10230',NULL,NULL),(142,'104401',44,'สะพานสูง','10240',NULL,NULL),(143,'104501',45,'วังทองหลาง','10310',NULL,NULL),(144,'104601',46,'สามวาตะวันตก','10510',NULL,NULL),(145,'104602',46,'สามวาตะวันออก','10510',NULL,NULL),(146,'104603',46,'บางชัน','10510',NULL,NULL),(147,'104604',46,'ทรายกองดิน','10510',NULL,NULL),(148,'104605',46,'ทรายกองดินใต้','10510',NULL,NULL),(149,'104701',47,'บางนา','10260',NULL,NULL),(150,'104801',48,'ทวีวัฒนา','10170',NULL,NULL),(151,'104802',48,'ศาลาธรรมสพน์','10170',NULL,NULL),(152,'104901',49,'บางมด','10140',NULL,NULL),(153,'104902',49,'ทุ่งครุ','10140',NULL,NULL),(154,'105001',50,'บางบอน','10150',NULL,NULL),(155,'110101',51,'เทศบาลนครสมุทรปราการ','10270',NULL,NULL),(156,'110102',51,'สำโรงเหนือ','10270',NULL,NULL),(157,'110103',51,'บางเมือง','10270',NULL,NULL),(158,'110104',51,'ท้ายบ้าน','10280',NULL,NULL),(159,'110105',51,'ท้ายบ้านใหม่','0',NULL,NULL),(160,'110108',51,'บางปูใหม่','10280',NULL,NULL),(161,'110110',51,'แพรกษา','10280',NULL,NULL),(162,'110111',51,'บางโปรง','10270',NULL,NULL),(163,'110112',51,'เทศบาลบางปู','10270',NULL,NULL),(164,'110113',51,'บางด้วน','10270',NULL,NULL),(165,'110114',51,'เทศบาลบางเมือง','10270',NULL,NULL),(166,'110115',51,'เทพารักษ์','10270',NULL,NULL),(167,'110117',51,'แพรกษาใหม่','10280',NULL,NULL),(168,'110201',52,'บางบ่อ','10560',NULL,NULL),(169,'110202',52,'บ้านระกาศ','10560',NULL,NULL),(170,'110203',52,'บางพลีน้อย','10560',NULL,NULL),(171,'110204',52,'บางเพรียง','10560',NULL,NULL),(172,'110205',52,'คลองด่าน','10550',NULL,NULL),(173,'110206',52,'คลองสวน','10560',NULL,NULL),(174,'110207',52,'เปร็ง','10560',NULL,NULL),(175,'110208',52,'คลองนิยมยาตรา','10560',NULL,NULL),(176,'110301',53,'บางพลีใหญ่','10540',NULL,NULL),(177,'110302',53,'บางแก้ว','10540',NULL,NULL),(178,'110303',53,'บางปลา','10540',NULL,NULL),(179,'110304',53,'บางโฉลง','10540',NULL,NULL),(180,'110308',53,'ราชาเทวะ','10540',NULL,NULL),(181,'110309',53,'หนองปรือ','10540',NULL,NULL),(182,'110401',54,'เทศบาลเมืองพระประแดง','10130',NULL,NULL),(183,'110402',54,'ตลาด','10130',NULL,NULL),(184,'110403',54,'บางจาก','10130',NULL,NULL),(185,'110404',54,'บางครุ','10130',NULL,NULL),(186,'110405',54,'บางหญ้าแพรก','10130',NULL,NULL),(187,'110406',54,'บางหัวเสือ','10130',NULL,NULL),(188,'110407',54,'เทศบาลสำโรงใต้','10130',NULL,NULL),(189,'110408',54,'บางยอ','10130',NULL,NULL),(190,'110409',54,'บางกะเจ้า','10130',NULL,NULL),(191,'110410',54,'บางน้ำผึ้ง','10130',NULL,NULL),(192,'110411',54,'บางกระสอบ','10130',NULL,NULL),(193,'110412',54,'บางกอบัว','10130',NULL,NULL),(194,'110413',54,'ทรงคนอง','10130',NULL,NULL),(195,'110414',54,'สำโรง','10130',NULL,NULL),(196,'110415',54,'สำโรงกลาง','10130',NULL,NULL),(197,'110501',55,'นาเกลือ','10290',NULL,NULL),(198,'110502',55,'บ้านคลองสวน','10290',NULL,NULL),(199,'110503',55,'แหลมฟ้าผ่า','10290',NULL,NULL),(200,'110504',55,'ปากคลองบางปลากด','10290',NULL,NULL),(201,'110505',55,'ในคลองบางปลากด','10290',NULL,NULL),(202,'110601',56,'บางเสาธง','10540',NULL,NULL),(203,'110602',56,'ศรีษะจรเข้น้อย','10540',NULL,NULL),(204,'110603',56,'ศรีษะจรเข้ใหญ่','10540',NULL,NULL),(205,'120101',57,'สวนใหญ่','11000',NULL,NULL),(206,'120102',57,'ตลาดขวัญ','11000',NULL,NULL),(207,'120103',57,'บางเขน','11000',NULL,NULL),(208,'120104',57,'บางกระสอ','11000',NULL,NULL),(209,'120105',57,'ท่าทราย','11000',NULL,NULL),(210,'120106',57,'บางไผ่','11000',NULL,NULL),(211,'120107',57,'บางศรีเมือง','11000',NULL,NULL),(212,'120108',57,'บางกร่าง','11000',NULL,NULL),(213,'120109',57,'ไทรม้า','11000',NULL,NULL),(214,'120110',57,'บางรักน้อย','11000',NULL,NULL),(215,'120201',58,'วัดชลอ','11130',NULL,NULL),(216,'120202',58,'บางกรวย','11130',NULL,NULL),(217,'120203',58,'บางสีทอง','11130',NULL,NULL),(218,'120204',58,'บางขนุน','11130',NULL,NULL),(219,'120205',58,'บางขุนกอง','11130',NULL,NULL),(220,'120206',58,'บางคูเวียง','11130',NULL,NULL),(221,'120207',58,'มหาสวัสดิ์','11130',NULL,NULL),(222,'120208',58,'ปลายบาง','11130',NULL,NULL),(223,'120209',58,'ศาลากลาง','11130',NULL,NULL),(224,'120301',59,'บางม่วง','11140',NULL,NULL),(225,'120302',59,'บางแม่นาง','11140',NULL,NULL),(226,'120303',59,'บางเลน','11140',NULL,NULL),(227,'120304',59,'เสาธงหิน','11140',NULL,NULL),(228,'120305',59,'บางใหญ่','11140',NULL,NULL),(229,'120306',59,'บ้านใหม่','11140',NULL,NULL),(230,'120401',60,'โสนลอย','11110',NULL,NULL),(231,'120402',60,'บางบัวทอง','11110',NULL,NULL),(232,'120403',60,'บางรักใหญ่','11110',NULL,NULL),(233,'120404',60,'บางคูรัด','11110',NULL,NULL),(234,'120405',60,'ละหาร','11110',NULL,NULL),(235,'120406',60,'ลำโพ','11110',NULL,NULL),(236,'120407',60,'พิมลราช','11110',NULL,NULL),(237,'120408',60,'บางรักพัฒนา','11110',NULL,NULL),(238,'120501',61,'ไทรน้อย','11150',NULL,NULL),(239,'120502',61,'ราษฎร์นิยม','11150',NULL,NULL),(240,'120503',61,'หนองเพรางาย','11150',NULL,NULL),(241,'120504',61,'ไทรใหญ่','11150',NULL,NULL),(242,'120505',61,'ขุนศรี','11150',NULL,NULL),(243,'120506',61,'คลองขวาง','11150',NULL,NULL),(244,'120507',61,'ทวีวัฒนา','11150',NULL,NULL),(245,'120601',62,'ปากเกร็ด','11120',NULL,NULL),(246,'120602',62,'บางตลาด','11120',NULL,NULL),(247,'120603',62,'บ้านใหม่','11120',NULL,NULL),(248,'120604',62,'บางพูด','11120',NULL,NULL),(249,'120605',62,'บางตะไนย์','11120',NULL,NULL),(250,'120606',62,'คลองพระอุดม','11120',NULL,NULL),(251,'120607',62,'ท่าอิฐ','11120',NULL,NULL),(252,'120608',62,'เกาะเกร็ด','11120',NULL,NULL),(253,'120609',62,'อ้อมเกร็ด','11120',NULL,NULL),(254,'120610',62,'คลองข่อย','11120',NULL,NULL),(255,'120611',62,'บางพลับ','11120',NULL,NULL),(256,'120612',62,'คลองเกลือ','11120',NULL,NULL),(257,'130101',63,'บางปรอก','12000',NULL,NULL),(258,'130102',63,'บ้านใหม่','12000',NULL,NULL),(259,'130103',63,'บ้านกลาง','12000',NULL,NULL),(260,'130104',63,'บ้านฉาง','12000',NULL,NULL),(261,'130105',63,'บ้านกระแชง','12000',NULL,NULL),(262,'130106',63,'บางขะแยง','12000',NULL,NULL),(263,'130107',63,'บางคูวัด','12000',NULL,NULL),(264,'130108',63,'บางหลวง','12000',NULL,NULL),(265,'130109',63,'บางเดื่อ','12000',NULL,NULL),(266,'130110',63,'บางพูด','12000',NULL,NULL),(267,'130111',63,'บางพูน','12000',NULL,NULL),(268,'130112',63,'บางกะดี','12000',NULL,NULL),(269,'130113',63,'สวนพริกไทย','12000',NULL,NULL),(270,'130114',63,'หลักหก','12000',NULL,NULL),(271,'130201',64,'คลองหนึ่ง','12120',NULL,NULL),(272,'130202',64,'คลองสอง','12120',NULL,NULL),(273,'130203',64,'คลองสาม','12120',NULL,NULL),(274,'130204',64,'คลองสี่','12120',NULL,NULL),(275,'130205',64,'คลองห้า','12120',NULL,NULL),(276,'130206',64,'คลองหก','12120',NULL,NULL),(277,'130207',64,'คลองเจ็ด','12120',NULL,NULL),(278,'130301',65,'ประชาธิปัตย์','12130',NULL,NULL),(279,'130302',65,'บึงยี่โถ','12130',NULL,NULL),(280,'130303',65,'รังสิต','12110',NULL,NULL),(281,'130304',65,'ลำผักกูด','12110',NULL,NULL),(282,'130305',65,'บึงสนั่น','12110',NULL,NULL),(283,'130306',65,'บึงน้ำรักษ์','12110',NULL,NULL),(284,'130401',66,'บึงบา','12170',NULL,NULL),(285,'130402',66,'บึงบอน','12170',NULL,NULL),(286,'130403',66,'บึงกาสาม','12170',NULL,NULL),(287,'130404',66,'บึงชำอ้อ','12170',NULL,NULL),(288,'130405',66,'หนองสามวัง','12170',NULL,NULL),(289,'130406',66,'ศาลาครุ','12170',NULL,NULL),(290,'130407',66,'นพรัตน์','12170',NULL,NULL),(291,'130501',67,'ระแหง','12140',NULL,NULL),(292,'130502',67,'ลาดหลุมแก้ว','12140',NULL,NULL),(293,'130503',67,'คูบางหลวง','12140',NULL,NULL),(294,'130504',67,'คูขวาง','12140',NULL,NULL),(295,'130505',67,'คลองพระอุดม','12140',NULL,NULL),(296,'130506',67,'บ่อเงิน','12140',NULL,NULL),(297,'130507',67,'หน้าไม้','12140',NULL,NULL),(298,'130601',68,'คูคต','12130',NULL,NULL),(299,'130602',68,'ลาดสวาย','12150',NULL,NULL),(300,'130603',68,'บึงคำพร้อย','12150',NULL,NULL),(301,'130604',68,'ลำลูกกา','12150',NULL,NULL),(302,'130605',68,'บึงทองหลาง','12150',NULL,NULL),(303,'130606',68,'ลำไทร','12150',NULL,NULL),(304,'130607',68,'บึงคอไห','12150',NULL,NULL),(305,'130608',68,'พืชอุดม','12150',NULL,NULL),(306,'130701',69,'บางเตย','12160',NULL,NULL),(307,'130702',69,'คลองควาย','12160',NULL,NULL),(308,'130703',69,'สามโคก','12160',NULL,NULL),(309,'130704',69,'กระแชง','12160',NULL,NULL),(310,'130705',69,'บางโพธิ์เหนือ','12160',NULL,NULL),(311,'130706',69,'เชียงรากใหญ่','12160',NULL,NULL),(312,'130707',69,'บ้านปทุม','12160',NULL,NULL),(313,'130708',69,'บ้านงิ้ว','12160',NULL,NULL),(314,'130709',69,'เชียงรากน้อย','12160',NULL,NULL),(315,'130710',69,'บางกระบือ','12160',NULL,NULL),(316,'130711',69,'ท้ายเกาะ','12160',NULL,NULL),(317,'140101',70,'ประตูชัย','13000',NULL,NULL),(318,'140102',70,'กะมัง','13000',NULL,NULL),(319,'140103',70,'หอรัตนไชย','13000',NULL,NULL),(320,'140104',70,'หัวรอ','13000',NULL,NULL),(321,'140105',70,'ท่าวาสุกรี','13000',NULL,NULL),(322,'140106',70,'ไผ่ลิง','13000',NULL,NULL),(323,'140107',70,'ปากกราน','13000',NULL,NULL),(324,'140108',70,'ภูเขาทอง','13000',NULL,NULL),(325,'140109',70,'สำเภาล่ม','13000',NULL,NULL),(326,'140110',70,'สวนพริก','13000',NULL,NULL),(327,'140111',70,'คลองตะเคียน','13000',NULL,NULL),(328,'140112',70,'วัดตูม','13000',NULL,NULL),(329,'140113',70,'หันตรา','13000',NULL,NULL),(330,'140114',70,'ลุมพลี','13000',NULL,NULL),(331,'140115',70,'บ้านใหม่','13000',NULL,NULL),(332,'140116',70,'บ้านเกาะ','13000',NULL,NULL),(333,'140117',70,'คลองสวนพลู','13000',NULL,NULL),(334,'140118',70,'คลองสระบัว','13000',NULL,NULL),(335,'140119',70,'เกาะเรียน','13000',NULL,NULL),(336,'140120',70,'บ้านป้อม','13000',NULL,NULL),(337,'140121',70,'บ้านรุม','13000',NULL,NULL),(338,'140201',71,'ท่าเรือ','13130',NULL,NULL),(339,'140202',71,'จำปา','13130',NULL,NULL),(340,'140203',71,'ท่าหลวง','13130',NULL,NULL),(341,'140204',71,'บ้านร่อม','13130',NULL,NULL),(342,'140205',71,'ศาลาลอย','13130',NULL,NULL),(343,'140206',71,'วังแดง','13130',NULL,NULL),(344,'140207',71,'โพธิ์เอน','13130',NULL,NULL),(345,'140208',71,'ปากท่า','13130',NULL,NULL),(346,'140209',71,'หนองขนาก','13130',NULL,NULL),(347,'140210',71,'ท่าเจ้าสนุก','13130',NULL,NULL),(348,'140301',72,'นครหลวง','13260',NULL,NULL),(349,'140302',72,'ท่าช้าง','13260',NULL,NULL),(350,'140303',72,'บ่อโพง','13260',NULL,NULL),(351,'140304',72,'บ้านชุ้ง','13260',NULL,NULL),(352,'140305',72,'ปากจั่น','13260',NULL,NULL),(353,'140306',72,'บางระกำ','13260',NULL,NULL),(354,'140307',72,'บางพระครู','13260',NULL,NULL),(355,'140308',72,'แม่ลา','13260',NULL,NULL),(356,'140309',72,'หนองปลิง','13260',NULL,NULL),(357,'140310',72,'คลองสะแก','13260',NULL,NULL),(358,'140311',72,'สามไถ','13260',NULL,NULL),(359,'140312',72,'พระนอน','13260',NULL,NULL),(360,'140401',73,'บางไทร','13190',NULL,NULL),(361,'140402',73,'บางพลี','13190',NULL,NULL),(362,'140403',73,'สนามชัย','13190',NULL,NULL),(363,'140404',73,'บ้านแป้ง','13190',NULL,NULL),(364,'140405',73,'หน้าไม้','13190',NULL,NULL),(365,'140406',73,'บางยี่โท','13190',NULL,NULL),(366,'140407',73,'แคออก','13190',NULL,NULL),(367,'140408',73,'แคตก','13190',NULL,NULL),(368,'140409',73,'ช่างเหล็ก','13190',NULL,NULL),(369,'140410',73,'กระแชง','13190',NULL,NULL),(370,'140411',73,'บ้างกลึง','13190',NULL,NULL),(371,'140412',73,'ช้างน้อย','13190',NULL,NULL),(372,'140413',73,'ห่อหมก','13190',NULL,NULL),(373,'140414',73,'ไผ่พระ','13190',NULL,NULL),(374,'140415',73,'กกแก้วบูรพา','13190',NULL,NULL),(375,'140416',73,'ไม้ตรา','13190',NULL,NULL),(376,'140417',73,'บ้านม้า','13190',NULL,NULL),(377,'140418',73,'บ้านเกาะ','13190',NULL,NULL),(378,'140419',73,'ราชคราม','13290',NULL,NULL),(379,'140420',73,'ช้างใหญ่','13290',NULL,NULL),(380,'140422',73,'เชียงรากน้อย','13290',NULL,NULL),(381,'140423',73,'โคกช้าง','13190',NULL,NULL),(382,'140501',74,'บางบาล','13250',NULL,NULL),(383,'140502',74,'วัดยม','13250',NULL,NULL),(384,'140503',74,'ไทรน้อย','13250',NULL,NULL),(385,'140504',74,'สะพานไทย','13250',NULL,NULL),(386,'140505',74,'มหาพราหมณ์','13250',NULL,NULL),(387,'140506',74,'กบเจา','13250',NULL,NULL),(388,'140507',74,'บ้านคลัง','13250',NULL,NULL),(389,'140508',74,'พระขาว','13250',NULL,NULL),(390,'140509',74,'น้ำเต้า','13250',NULL,NULL),(391,'140510',74,'ทางช้าง','13250',NULL,NULL),(392,'140511',74,'วัดตะกู','13250',NULL,NULL),(393,'140512',74,'บางหลวง','13250',NULL,NULL),(394,'140513',74,'บางหลวงโดด','13250',NULL,NULL),(395,'140514',74,'บางหัก','13250',NULL,NULL),(396,'140515',74,'บางชะนี','13250',NULL,NULL),(397,'140516',74,'บ้านกุ่ม','13250',NULL,NULL),(398,'140601',75,'บ้านเลน','13160',NULL,NULL),(399,'140602',75,'เชียงรากน้อย','13180',NULL,NULL),(400,'140603',75,'บ้านโพ','13160',NULL,NULL),(401,'140604',75,'บ้านกรด','13160',NULL,NULL),(402,'140605',75,'บางกระสั้น','13160',NULL,NULL),(403,'140606',75,'คลองจิก','13160',NULL,NULL),(404,'140607',75,'บ้านหว้า','13160',NULL,NULL),(405,'140608',75,'วัดยม','13160',NULL,NULL),(406,'140609',75,'บางประแดง','13160',NULL,NULL),(407,'140610',75,'สามเรือน','13160',NULL,NULL),(408,'140611',75,'เกาะเกิด','13160',NULL,NULL),(409,'140612',75,'บ้านพลับ','13160',NULL,NULL),(410,'140613',75,'บ้านแป้ง','13160',NULL,NULL),(411,'140614',75,'คุ้งลาน','13160',NULL,NULL),(412,'140615',75,'ตลิ่งชัน','13160',NULL,NULL),(413,'140616',75,'บ้านสร้าง','13170',NULL,NULL),(414,'140618',75,'ขนอนหลวง','13160',NULL,NULL),(415,'140701',76,'บางปะหัน','13220',NULL,NULL),(416,'140702',76,'ขยาย','13220',NULL,NULL),(417,'140703',76,'บางเดื่อ','13220',NULL,NULL),(418,'140704',76,'เสาธง','13220',NULL,NULL),(419,'140705',76,'ทางกลาง','13220',NULL,NULL),(420,'140706',76,'บางเพลิง','13220',NULL,NULL),(421,'140707',76,'หันสัง','13220',NULL,NULL),(422,'140708',76,'บางนางร้า','13220',NULL,NULL),(423,'140709',76,'ตานิม','13220',NULL,NULL),(424,'140710',76,'ทับน้ำ','13220',NULL,NULL),(425,'140711',76,'บ้านม้า','13220',NULL,NULL),(426,'140712',76,'ขวัญเมือง','13220',NULL,NULL),(427,'140713',76,'บ้านลี่','13220',NULL,NULL),(428,'140714',76,'โพธิ์สามต้น','13220',NULL,NULL),(429,'140715',76,'พุทเลา','13220',NULL,NULL),(430,'140716',76,'ตาลเอน','13220',NULL,NULL),(431,'140717',76,'บ้านขล้อ','13220',NULL,NULL),(432,'140801',77,'ผักไห่','13120',NULL,NULL),(433,'140802',77,'อมฤต','13120',NULL,NULL),(434,'140803',77,'บ้านแค','13120',NULL,NULL),(435,'140804',77,'ลาดน้ำเค็ม','13120',NULL,NULL),(436,'140805',77,'ตาลาน','13120',NULL,NULL),(437,'140806',77,'ท่าดินแดง','13120',NULL,NULL),(438,'140807',77,'ดอนลาน','13280',NULL,NULL),(439,'140808',77,'นาคู','13280',NULL,NULL),(440,'140809',77,'กุฎี','13120',NULL,NULL),(441,'140810',77,'ลำตะเคียน','13280',NULL,NULL),(442,'140811',77,'โคกช้าง','13120',NULL,NULL),(443,'140812',77,'จักราช','13280',NULL,NULL),(444,'140813',77,'หนองน้ำใหญ่','13280',NULL,NULL),(445,'140814',77,'ลาดชิด','13120',NULL,NULL),(446,'140815',77,'หน้าโคก','13120',NULL,NULL),(447,'140816',77,'บ้านใหญ่','13120',NULL,NULL),(448,'140901',78,'ภาชี','13140',NULL,NULL),(449,'140902',78,'โคกม่วง','13140',NULL,NULL),(450,'140903',78,'ระโสม','13140',NULL,NULL),(451,'140904',78,'หนองน้ำใส','13140',NULL,NULL),(452,'140905',78,'ดอนหญ้านาง','13140',NULL,NULL),(453,'140906',78,'ไผ่ล้อม','13140',NULL,NULL),(454,'140907',78,'กระจิว','13140',NULL,NULL),(455,'140908',78,'พระแก้ว','13140',NULL,NULL),(456,'141001',79,'ลาดบัวหลวง','13230',NULL,NULL),(457,'141002',79,'หลักชัย','13230',NULL,NULL),(458,'141003',79,'สามเมือง','13230',NULL,NULL),(459,'141004',79,'พระยาบันลือ','13230',NULL,NULL),(460,'141005',79,'สิงหนาท','13230',NULL,NULL),(461,'141006',79,'คู้สลอด','13230',NULL,NULL),(462,'141007',79,'คลองพระยาบันลือ','13230',NULL,NULL),(463,'141101',80,'ลำตาเสา','13170',NULL,NULL),(464,'141102',80,'บ่อตาโล่','13170',NULL,NULL),(465,'141103',80,'วังน้อย','13170',NULL,NULL),(466,'141104',80,'ลำไทร','13170',NULL,NULL),(467,'141105',80,'สนับทึบ','13170',NULL,NULL),(468,'141106',80,'พยอม','13170',NULL,NULL),(469,'141107',80,'หันตะเภา','13170',NULL,NULL),(470,'141108',80,'ข้าวงาม','13170',NULL,NULL),(471,'141109',80,'วังจุฬา','13170',NULL,NULL),(472,'141110',80,'ชะแมบ','13170',NULL,NULL),(473,'141201',81,'เสนา','13110',NULL,NULL),(474,'141202',81,'บ้านแพน','13110',NULL,NULL),(475,'141203',81,'เจ้าเจ็ด','13110',NULL,NULL),(476,'141204',81,'สามกอ','13110',NULL,NULL),(477,'141205',81,'บางนมโค','13110',NULL,NULL),(478,'141206',81,'หัวเวียง','13110',NULL,NULL),(479,'141207',81,'มารวิชัย','13110',NULL,NULL),(480,'141208',81,'บ้านโพธิ์','13110',NULL,NULL),(481,'141209',81,'รางจรเข้','13110',NULL,NULL),(482,'141210',81,'บ้านกระทุ่ม','13110',NULL,NULL),(483,'141211',81,'บ้านแถว','13110',NULL,NULL),(484,'141212',81,'ชายนา','13110',NULL,NULL),(485,'141213',81,'สามตุ่ม','13110',NULL,NULL),(486,'141214',81,'ลาดงา','13110',NULL,NULL),(487,'141215',81,'ดอนทอง','13110',NULL,NULL),(488,'141216',81,'บ้านหลวง','13110',NULL,NULL),(489,'141217',81,'เจ้าเสด็จ','13110',NULL,NULL),(490,'141301',82,'บางซ้าย','13270',NULL,NULL),(491,'141302',82,'แก้วฟ้า','13270',NULL,NULL),(492,'141303',82,'เต่าเล่า','13270',NULL,NULL),(493,'141304',82,'ปลายกลัด','13270',NULL,NULL),(494,'141305',82,'เทพมงคล','13270',NULL,NULL),(495,'141306',82,'วังพัฒนา','13270',NULL,NULL),(496,'141401',83,'คานหาม','13210',NULL,NULL),(497,'141402',83,'บ้านช้าง','13210',NULL,NULL),(498,'141403',83,'สามบัณฑิต','13210',NULL,NULL),(499,'141404',83,'บ้านหีบ','13210',NULL,NULL),(500,'141405',83,'หนองไม้ซุง','13210',NULL,NULL),(501,'141406',83,'อุทัย','13210',NULL,NULL),(502,'141407',83,'เสนา','13210',NULL,NULL),(503,'141408',83,'หนองน้ำส้ม','13210',NULL,NULL),(504,'141409',83,'โพสาวหาญ','13210',NULL,NULL),(505,'141410',83,'ธนู','13210',NULL,NULL),(506,'141411',83,'ข้าวเม่า','13210',NULL,NULL),(507,'141501',84,'หัวไผ่','13150',NULL,NULL),(508,'141502',84,'กะทุ่ม','13150',NULL,NULL),(509,'141503',84,'มหาราช','13150',NULL,NULL),(510,'141504',84,'น้ำเต้า','13150',NULL,NULL),(511,'141505',84,'บางนา','13150',NULL,NULL),(512,'141506',84,'โรงช้าง','13150',NULL,NULL),(513,'141507',84,'เจ้าปลุก','13150',NULL,NULL),(514,'141508',84,'พิตเพียน','13150',NULL,NULL),(515,'141509',84,'บ้านนา','13150',NULL,NULL),(516,'141510',84,'บ้านขวาง','13150',NULL,NULL),(517,'141511',84,'ท่าตอ','13150',NULL,NULL),(518,'141512',84,'บ้านใหม่','13150',NULL,NULL),(519,'141601',85,'บ้านแพรก','13240',NULL,NULL),(520,'141602',85,'บ้านใหม่','13240',NULL,NULL),(521,'141603',85,'สำพะเนียง','13240',NULL,NULL),(522,'141604',85,'คลองน้อย','13240',NULL,NULL),(523,'141605',85,'สองห้อง','13240',NULL,NULL),(524,'150101',86,'ตลาดหลวง','14000',NULL,NULL),(525,'150102',86,'บางแก้ว','14000',NULL,NULL),(526,'150103',86,'ศาลาแดง','14000',NULL,NULL),(527,'150104',86,'ป่างิ้ว','14000',NULL,NULL),(528,'150105',86,'บ้านแห','14000',NULL,NULL),(529,'150106',86,'ตลาดกรวด','14000',NULL,NULL),(530,'150107',86,'มหาดไทย','14000',NULL,NULL),(531,'150108',86,'บ้านอิฐ','14000',NULL,NULL),(532,'150109',86,'หัวไผ่','14000',NULL,NULL),(533,'150110',86,'จำปาหล่อ','14000',NULL,NULL),(534,'150111',86,'โพสะ','14000',NULL,NULL),(535,'150112',86,'บ้านรี','14000',NULL,NULL),(536,'150113',86,'คลองวัว','14000',NULL,NULL),(537,'150114',86,'ย่านซื่อ','14000',NULL,NULL),(538,'150201',87,'จรเข้ร้อง','14140',NULL,NULL),(539,'150202',87,'ไชยภูมิ','14140',NULL,NULL),(540,'150203',87,'ชัยฤทธิ์','14140',NULL,NULL),(541,'150204',87,'เทวราช','14140',NULL,NULL),(542,'150205',87,'ราชสถิตย์','14140',NULL,NULL),(543,'150206',87,'ไชโย','14140',NULL,NULL),(544,'150207',87,'หลักฟ้า','14140',NULL,NULL),(545,'150208',87,'ชะไว','14140',NULL,NULL),(546,'150209',87,'ตรีณรงค์','14140',NULL,NULL),(547,'150301',88,'บางปลากด','14130',NULL,NULL),(548,'150302',88,'ป่าโมก','14130',NULL,NULL),(549,'150303',88,'สายทอง','14130',NULL,NULL),(550,'150304',88,'โรงช้าง','14130',NULL,NULL),(551,'150305',88,'บางเสด็จ','14130',NULL,NULL),(552,'150306',88,'นรสิงห์','14130',NULL,NULL),(553,'150307',88,'เอกราช','14130',NULL,NULL),(554,'150308',88,'โผงเผง','14130',NULL,NULL),(555,'150401',89,'อ่างแก้ว','14120',NULL,NULL),(556,'150402',89,'อินทประมูล','14120',NULL,NULL),(557,'150403',89,'บางพลับ','14120',NULL,NULL),(558,'150404',89,'หนองแม่ไก่','14120',NULL,NULL),(559,'150405',89,'รำมะสัก','14120',NULL,NULL),(560,'150406',89,'บางระกำ','14120',NULL,NULL),(561,'150407',89,'โพธิ์รังนก','14120',NULL,NULL),(562,'150408',89,'องครักษ์','14120',NULL,NULL),(563,'150409',89,'โคกพุทรา','14120',NULL,NULL),(564,'150410',89,'ยางซ้าย','14120',NULL,NULL),(565,'150411',89,'บ่อแร่','14120',NULL,NULL),(566,'150412',89,'ทางพระ','14120',NULL,NULL),(567,'150413',89,'สามง่าม','14120',NULL,NULL),(568,'150414',89,'บางเจ้าฉ่า','14120',NULL,NULL),(569,'150415',89,'คำหยาด','14120',NULL,NULL),(570,'150501',90,'แสวงหา','14150',NULL,NULL),(571,'150502',90,'ศรีพราน','14150',NULL,NULL),(572,'150503',90,'บ้านพราน','14150',NULL,NULL),(573,'150504',90,'วังน้ำเย็น','14150',NULL,NULL),(574,'150505',90,'สีบัวทอง','14150',NULL,NULL),(575,'150506',90,'ห้วยไผ่','14150',NULL,NULL),(576,'150507',90,'จำลอง','14150',NULL,NULL),(577,'150601',91,'ไผ่จำศีล','14110',NULL,NULL),(578,'150602',91,'ศาลเจ้าโรงทอง','14110',NULL,NULL),(579,'150603',91,'ไผ่ดำพัฒนา','14110',NULL,NULL),(580,'150604',91,'สาวร้องไห้','14110',NULL,NULL),(581,'150605',91,'ท่าช้าง','14110',NULL,NULL),(582,'150606',91,'ยี่ล้น','14110',NULL,NULL),(583,'150607',91,'บางจัก','14110',NULL,NULL),(584,'150608',91,'ห้วยคันแหลน','14110',NULL,NULL),(585,'150609',91,'คลองขนาก','14110',NULL,NULL),(586,'150610',91,'ไผ่วง','14110',NULL,NULL),(587,'150611',91,'สี่ร้อย','14110',NULL,NULL),(588,'150612',91,'ม่วงเตี้ย','14110',NULL,NULL),(589,'150613',91,'หัวตะพาน','14110',NULL,NULL),(590,'150614',91,'หลักแก้ว','14110',NULL,NULL),(591,'150615',91,'ตลาดใหม่','14110',NULL,NULL),(592,'150701',92,'สามโก้','14160',NULL,NULL),(593,'150702',92,'ราษฎรพัฒนา','14160',NULL,NULL),(594,'150703',92,'อบทม','14160',NULL,NULL),(595,'150704',92,'โพธิ์ม่วงพันธ์','14160',NULL,NULL),(596,'150705',92,'มงคลธรรมนิมิต','14160',NULL,NULL),(597,'160101',93,'ทะเลชุบศร','15000',NULL,NULL),(598,'160102',93,'ท่าหิน','15000',NULL,NULL),(599,'160103',93,'กกโก','15000',NULL,NULL),(600,'160104',93,'โก่งธนู','13240',NULL,NULL),(601,'160105',93,'เขาพระงาม','15000',NULL,NULL),(602,'160106',93,'เขาสามยอด','15000',NULL,NULL),(603,'160107',93,'โคกกระเทียม','15000',NULL,NULL),(604,'160108',93,'โคกลำพาน','15000',NULL,NULL),(605,'160109',93,'โคกตูม','15210',NULL,NULL),(606,'160110',93,'งิ้วราย','15000',NULL,NULL),(607,'160111',93,'ดอนโพธิ์','15000',NULL,NULL),(608,'160112',93,'ตะลุง','15000',NULL,NULL),(609,'160114',93,'ท่าแค','15000',NULL,NULL),(610,'160115',93,'ท่าศาลา','15000',NULL,NULL),(611,'160116',93,'นิคมสร้างตนเอง','15000',NULL,NULL),(612,'160117',93,'บางขันหมาก','15000',NULL,NULL),(613,'160118',93,'บ้านข่อย','15000',NULL,NULL),(614,'160119',93,'ท้ายตลาด','15000',NULL,NULL),(615,'160120',93,'ป่าตาล','15000',NULL,NULL),(616,'160121',93,'พรหมมาสตร์','15000',NULL,NULL),(617,'160122',93,'โพธิ์เก้าต้น','15000',NULL,NULL),(618,'160123',93,'โพธิ์ตรุ','15000',NULL,NULL),(619,'160124',93,'สี่คลอง','15000',NULL,NULL),(620,'160125',93,'ถนนใหญ่','15000',NULL,NULL),(621,'160201',94,'พัฒนานิคม','15140',NULL,NULL),(622,'160202',94,'ช่องสาริกา','15220',NULL,NULL),(623,'160203',94,'มะนาวหวาน','15140',NULL,NULL),(624,'160204',94,'ดีลัง','15220',NULL,NULL),(625,'160205',94,'โคกสลุง','15140',NULL,NULL),(626,'160206',94,'ชอนน้อย','15140',NULL,NULL),(627,'160207',94,'หนองบัว','15140',NULL,NULL),(628,'160208',94,'ห้วยขุนราม','18220',NULL,NULL),(629,'160209',94,'น้ำสุด','15140',NULL,NULL),(630,'160301',95,'โคกสำโรง','15120',NULL,NULL),(631,'160302',95,'เกาะแก้ว','15120',NULL,NULL),(632,'160303',95,'ถลุงเหล็ก','15120',NULL,NULL),(633,'160304',95,'หลุมข้าว','15120',NULL,NULL),(634,'160305',95,'ห้วยโป่ง','15120',NULL,NULL),(635,'160306',95,'คลองเกตุ','15120',NULL,NULL),(636,'160307',95,'สะแกราบ','15120',NULL,NULL),(637,'160308',95,'เพนียด','15120',NULL,NULL),(638,'160309',95,'วังเพลิง','15120',NULL,NULL),(639,'160310',95,'ดงมะรุม','15120',NULL,NULL),(640,'160318',95,'วังขอนขว้าง','15120',NULL,NULL),(641,'160320',95,'วังจั่น','15120',NULL,NULL),(642,'160322',95,'หนองแขม','15120',NULL,NULL),(643,'160401',96,'ลำนารายณ์','15130',NULL,NULL),(644,'160402',96,'ชัยนารายณ์','15130',NULL,NULL),(645,'160403',96,'ศิลาทิพย์','15130',NULL,NULL),(646,'160404',96,'ห้วยหิน','15130',NULL,NULL),(647,'160405',96,'ม่วงค่อม','15230',NULL,NULL),(648,'160406',96,'บัวชุม','15130',NULL,NULL),(649,'160407',96,'ท่าดินดำ','15130',NULL,NULL),(650,'160408',96,'มะกอกหวาน','15230',NULL,NULL),(651,'160409',96,'ซับตะเคียน','15130',NULL,NULL),(652,'160410',96,'นาโสม','15190',NULL,NULL),(653,'160411',96,'หนองยายโต๊ะ','15130',NULL,NULL),(654,'160412',96,'เกาะรัง','15130',NULL,NULL),(655,'160414',96,'ท่ามะนาว','15130',NULL,NULL),(656,'160417',96,'นิคมลำนารายณ์','15130',NULL,NULL),(657,'160418',96,'ชัยบาดาล','15230',NULL,NULL),(658,'160419',96,'บ้านใหม่สามัคคี','15130',NULL,NULL),(659,'160422',96,'เขาแหลม','15130',NULL,NULL),(660,'160501',97,'ท่าวุ้ง','15150',NULL,NULL),(661,'160502',97,'บางคู้','15150',NULL,NULL),(662,'160503',97,'โพตลาดแก้ว','15150',NULL,NULL),(663,'160504',97,'บางลี่','15150',NULL,NULL),(664,'160505',97,'บางงา','15150',NULL,NULL),(665,'160506',97,'โคกสลุด','15150',NULL,NULL),(666,'160507',97,'เขาสมอคอน','15180',NULL,NULL),(667,'160508',97,'หัวสำโรง','15150',NULL,NULL),(668,'160509',97,'ลาดสาลี','15150',NULL,NULL),(669,'160511',97,'มุจลินท์','15150',NULL,NULL),(670,'160601',98,'ไผ่ใหญ่','15110',NULL,NULL),(671,'160602',98,'บ้านทราย','15110',NULL,NULL),(672,'160603',98,'บ้านกล้วย','15110',NULL,NULL),(673,'160604',98,'ดงพลับ','15110',NULL,NULL),(674,'160605',98,'บ้านชี','15180',NULL,NULL),(675,'160606',98,'พุคา','15110',NULL,NULL),(676,'160607',98,'หินปัก','15110',NULL,NULL),(677,'160608',98,'บางพึ่ง','15110',NULL,NULL),(678,'160609',98,'หนองทรายขาว','15110',NULL,NULL),(679,'160610',98,'บางกะพี้','15110',NULL,NULL),(680,'160611',98,'หนองเต่า','15110',NULL,NULL),(681,'160612',98,'โพนทอง','15110',NULL,NULL),(682,'160613',98,'บางขาม','15180',NULL,NULL),(683,'160614',98,'ดอนดึง','15110',NULL,NULL),(684,'160615',98,'ชอนม่วง','15110',NULL,NULL),(685,'160616',98,'หนองกระเบียน','15110',NULL,NULL),(686,'160617',98,'สายห้วยแก้ว','15110',NULL,NULL),(687,'160618',98,'มหาสอน','15110',NULL,NULL),(688,'160621',98,'หนองเมือง','15110',NULL,NULL),(689,'160622',98,'สนามแจง','15110',NULL,NULL),(690,'160701',99,'ท่าหลวง','15230',NULL,NULL),(691,'160702',99,'แก่งผักกูด','15230',NULL,NULL),(692,'160703',99,'ซับจำปา','15230',NULL,NULL),(693,'160704',99,'หนองผักแว่น','15230',NULL,NULL),(694,'160705',99,'ทะเลวังวัด','15230',NULL,NULL),(695,'160706',99,'หัวลำ','15230',NULL,NULL),(696,'160801',100,'สระโบสถ์','15240',NULL,NULL),(697,'160802',100,'มหาโพธิ','15240',NULL,NULL),(698,'160803',100,'ทุ่งท่าช้าง','15240',NULL,NULL),(699,'160804',100,'ห้วยใหญ่','15240',NULL,NULL),(700,'160805',100,'นิยมชัย','15240',NULL,NULL),(701,'160901',101,'โคกเจริญ','15250',NULL,NULL),(702,'160902',101,'ยางราก','15250',NULL,NULL),(703,'160903',101,'หนองมะค่า','15250',NULL,NULL),(704,'160904',101,'วังทอง','15250',NULL,NULL),(705,'160905',101,'โคกแสมสาร','15250',NULL,NULL),(706,'161001',102,'ลำสนธิ','15190',NULL,NULL),(707,'161002',102,'ซับสมบูรณ์','15190',NULL,NULL),(708,'161003',102,'หนองรี','15190',NULL,NULL),(709,'161004',102,'กุดตาเพชร','15190',NULL,NULL),(710,'161005',102,'เขารวก','15190',NULL,NULL),(711,'161006',102,'เขาน้อย','15130',NULL,NULL),(712,'161101',103,'หนองม่วง','15170',NULL,NULL),(713,'161102',103,'บ่อทอง','15170',NULL,NULL),(714,'161103',103,'ดงดินแดง','15170',NULL,NULL),(715,'161104',103,'ชอนสมบูรณ์','15170',NULL,NULL),(716,'161105',103,'ยางโทน','15170',NULL,NULL),(717,'161106',103,'ชอนสารเดช','15170',NULL,NULL),(718,'170101',104,'บางพุทรา','16000',NULL,NULL),(719,'170102',104,'บางมัญ','16000',NULL,NULL),(720,'170103',104,'โพกรวม','16000',NULL,NULL),(721,'170104',104,'ม่วงหมู่','16000',NULL,NULL),(722,'170105',104,'หัวไผ่','16000',NULL,NULL),(723,'170106',104,'ต้นโพธิ์','16000',NULL,NULL),(724,'170107',104,'จักรสีห์','16000',NULL,NULL),(725,'170108',104,'บางกระบือ','16000',NULL,NULL),(726,'170201',105,'สิงห์','16130',NULL,NULL),(727,'170202',105,'ไม้ดัด','16130',NULL,NULL),(728,'170203',105,'เชิงกลัด','16130',NULL,NULL),(729,'170204',105,'โพชนไก่','16130',NULL,NULL),(730,'170205',105,'แม่ลา','16130',NULL,NULL),(731,'170206',105,'บ้านจ่า','16130',NULL,NULL),(732,'170207',105,'พักทัน','16130',NULL,NULL),(733,'170208',105,'สระแจง','16130',NULL,NULL),(734,'170301',106,'โพทะเล','16150',NULL,NULL),(735,'170302',106,'บางระจัน','16150',NULL,NULL),(736,'170303',106,'โพสังโฆ','16150',NULL,NULL),(737,'170304',106,'ท่าข้าม','16150',NULL,NULL),(738,'170305',106,'คอทราย','16150',NULL,NULL),(739,'170306',106,'หนองกระทุ่ม','16150',NULL,NULL),(740,'170401',107,'พระงาม','16120',NULL,NULL),(741,'170402',107,'พรหมบุรี','16160',NULL,NULL),(742,'170403',107,'บางน้ำเชี่ยว','16120',NULL,NULL),(743,'170404',107,'บ้านหม้อ','16120',NULL,NULL),(744,'170405',107,'บ้านแป้ง','16120',NULL,NULL),(745,'170406',107,'หัวป่า','16120',NULL,NULL),(746,'170407',107,'โรงช้าง','16120',NULL,NULL),(747,'170501',108,'ถอนสมอ','16140',NULL,NULL),(748,'170502',108,'โพประจักษ์','16140',NULL,NULL),(749,'170503',108,'วิหารขาว','16140',NULL,NULL),(750,'170504',108,'พิกุลทอง','16140',NULL,NULL),(751,'170601',109,'อินทร์บุรี','16110',NULL,NULL),(752,'170602',109,'ประศุก','16110',NULL,NULL),(753,'170603',109,'ทับยา','16110',NULL,NULL),(754,'170604',109,'งิ้วราย','16110',NULL,NULL),(755,'170605',109,'ชีน้ำร้าย','16110',NULL,NULL),(756,'170606',109,'ท่างาม','16110',NULL,NULL),(757,'170607',109,'น้ำตาล','16110',NULL,NULL),(758,'170608',109,'ทองเอน','16110',NULL,NULL),(759,'170609',109,'ห้วยชัน','16110',NULL,NULL),(760,'170610',109,'โพธิ์ชัย','16110',NULL,NULL),(761,'180101',110,'ในเมือง','17000',NULL,NULL),(762,'180102',110,'บ้านกล้วย','17000',NULL,NULL),(763,'180103',110,'ท่าชัย','17000',NULL,NULL),(764,'180104',110,'ชัยนาท','17000',NULL,NULL),(765,'180105',110,'เขาท่าพระ','17000',NULL,NULL),(766,'180106',110,'หาดท่าเสา','17000',NULL,NULL),(767,'180107',110,'ธรรมามูล','17000',NULL,NULL),(768,'180108',110,'เสือโฮก','17000',NULL,NULL),(769,'180109',110,'นางลือ','17000',NULL,NULL),(770,'180201',111,'คุ้งสำเภา','17110',NULL,NULL),(771,'180202',111,'วัดโคก','17110',NULL,NULL),(772,'180203',111,'ศิลาดาน','17110',NULL,NULL),(773,'180204',111,'ท่าฉนวน','17110',NULL,NULL),(774,'180205',111,'หางน้ำสาคร','17170',NULL,NULL),(775,'180206',111,'ไร่พัฒนา','17170',NULL,NULL),(776,'180207',111,'อู่ตะเภา','17170',NULL,NULL),(777,'180301',112,'วัดสิงห์','17120',NULL,NULL),(778,'180302',112,'มะขามเฒ่า','17120',NULL,NULL),(779,'180303',112,'หนองน้อย','17120',NULL,NULL),(780,'180304',112,'หนองบัว','17120',NULL,NULL),(781,'180306',112,'หนองขุ่น','17120',NULL,NULL),(782,'180307',112,'บ่อแร่','17120',NULL,NULL),(783,'180311',112,'วังหมัน','17120',NULL,NULL),(784,'180401',113,'สรรพยา','17150',NULL,NULL),(785,'180402',113,'ตลุก','17150',NULL,NULL),(786,'180403',113,'เขาแก้ว','17150',NULL,NULL),(787,'180404',113,'โพนางดำตก','17150',NULL,NULL),(788,'180405',113,'โพนางดำออก','17150',NULL,NULL),(789,'180406',113,'บางหลวง','17150',NULL,NULL),(790,'180407',113,'หาดอาษา','17150',NULL,NULL),(791,'180501',114,'แพรกศรีราชา','17140',NULL,NULL),(792,'180502',114,'เที่ยงแท้','17140',NULL,NULL),(793,'180503',114,'ห้วยกรด','17140',NULL,NULL),(794,'180504',114,'โพงาม','17140',NULL,NULL),(795,'180505',114,'บางขุด','17140',NULL,NULL),(796,'180506',114,'ดงคอน','17140',NULL,NULL),(797,'180507',114,'ดอนกำ','17140',NULL,NULL),(798,'180508',114,'ห้วยกรดพัฒนา','17140',NULL,NULL),(799,'180601',115,'หันคา','17130',NULL,NULL),(800,'180602',115,'บ้านเชี่ยน','17130',NULL,NULL),(801,'180605',115,'ไพรนกยูง','17130',NULL,NULL),(802,'180606',115,'หนองแซง','17160',NULL,NULL),(803,'180607',115,'ห้วยงู','17160',NULL,NULL),(804,'180608',115,'วังไก่เถื่อน','17130',NULL,NULL),(805,'180609',115,'เด่นใหญ่','17130',NULL,NULL),(806,'180611',115,'สามง่ามท่าโบสถ์','17160',NULL,NULL),(807,'180701',116,'หนองมะโมง','17120',NULL,NULL),(808,'180702',116,'วังตะเคียน','17120',NULL,NULL),(809,'180703',116,'สะพานหิน','17120',NULL,NULL),(810,'180704',116,'กุดจอก','17120',NULL,NULL),(811,'180801',117,'เนินขาม','17130',NULL,NULL),(812,'180802',117,'กะบกเตี้ย','17130',NULL,NULL),(813,'180803',117,'สุขเดือนห้า','17130',NULL,NULL),(814,'190101',118,'ปากเพรียว','18000',NULL,NULL),(815,'190105',118,'ดาวเรือง','18000',NULL,NULL),(816,'190106',118,'นาโฉง','18000',NULL,NULL),(817,'190107',118,'โคกสว่าง','18000',NULL,NULL),(818,'190108',118,'หนองโน','18000',NULL,NULL),(819,'190109',118,'หนองยาว','18000',NULL,NULL),(820,'190110',118,'ปากข้าวสาร','18000',NULL,NULL),(821,'190111',118,'หนองปลาไหล','18000',NULL,NULL),(822,'190112',118,'กุดนกเปล้า','18000',NULL,NULL),(823,'190113',118,'ตลิ่งชัน','18000',NULL,NULL),(824,'190114',118,'ตะกุด','18000',NULL,NULL),(825,'190201',119,'แก่งคอย','18110',NULL,NULL),(826,'190202',119,'ทับกวาง','18260',NULL,NULL),(827,'190203',119,'ตาลเดี่ยว','18110',NULL,NULL),(828,'190204',119,'ห้วยแห้ง','18110',NULL,NULL),(829,'190205',119,'ท่าคล้อ','18110',NULL,NULL),(830,'190206',119,'หินซ้อน','18110',NULL,NULL),(831,'190207',119,'บ้านธาตุ','18110',NULL,NULL),(832,'190208',119,'บ้านป่า','18110',NULL,NULL),(833,'190209',119,'ท่าตูม','18110',NULL,NULL),(834,'190210',119,'ชะอม','18110',NULL,NULL),(835,'190211',119,'สองคอน','18110',NULL,NULL),(836,'190212',119,'เตาปูน','18110',NULL,NULL),(837,'190213',119,'ชำผักแพว','18110',NULL,NULL),(838,'190215',119,'ท่ามะปราง','18110',NULL,NULL),(839,'190301',120,'หนองแค','18140',NULL,NULL),(840,'190302',120,'กุ่มหัก','18140',NULL,NULL),(841,'190303',120,'คชสิทธิ์','18250',NULL,NULL),(842,'190304',120,'โคกตูม','18250',NULL,NULL),(843,'190305',120,'โคกแย้','18230',NULL,NULL),(844,'190306',120,'บัวลอย','18230',NULL,NULL),(845,'190307',120,'ไผ่ต่ำ','18140',NULL,NULL),(846,'190308',120,'โพนทอง','18250',NULL,NULL),(847,'190309',120,'ห้วยขมิ้น','18230',NULL,NULL),(848,'190310',120,'ห้วยทราย','18230',NULL,NULL),(849,'190311',120,'หนองไข่น้ำ','18140',NULL,NULL),(850,'190312',120,'หนองแขม','18140',NULL,NULL),(851,'190313',120,'หนองจิก','18230',NULL,NULL),(852,'190314',120,'หนองจรเข้','18140',NULL,NULL),(853,'190315',120,'หนองนาก','18230',NULL,NULL),(854,'190316',120,'หนองปลาหมอ','18140',NULL,NULL),(855,'190317',120,'หนองปลิง','18140',NULL,NULL),(856,'190318',120,'หนองโรง','18140',NULL,NULL),(857,'190401',121,'หนองหมู','18150',NULL,NULL),(858,'190402',121,'บ้านลำ','18150',NULL,NULL),(859,'190403',121,'คลองเรือ','18150',NULL,NULL),(860,'190404',121,'วิหารแดง','18150',NULL,NULL),(861,'190405',121,'หนองสรวง','18150',NULL,NULL),(862,'190406',121,'เจริญธรรม','18150',NULL,NULL),(863,'190501',122,'หนองแซง','18170',NULL,NULL),(864,'190502',122,'หนองควายโซ','18170',NULL,NULL),(865,'190503',122,'หนองหัวโพ','18170',NULL,NULL),(866,'190504',122,'หนองสีดา','18170',NULL,NULL),(867,'190505',122,'หนองกบ','18170',NULL,NULL),(868,'190506',122,'ไก่เส่า','18170',NULL,NULL),(869,'190507',122,'โคกสะอาด','18170',NULL,NULL),(870,'190508',122,'ม่วงหวาน','18170',NULL,NULL),(871,'190601',123,'บ้านหมอ','18130',NULL,NULL),(872,'190602',123,'บางโขมด','18130',NULL,NULL),(873,'190603',123,'สร้างโศก','18130',NULL,NULL),(874,'190604',123,'ตลาดน้อย','18130',NULL,NULL),(875,'190605',123,'หรเทพ','18130',NULL,NULL),(876,'190606',123,'โคกใหญ่','18130',NULL,NULL),(877,'190607',123,'ไผ่ขวาง','18130',NULL,NULL),(878,'190608',123,'บ้านครัว','18270',NULL,NULL),(879,'190609',123,'หนองบัว','18130',NULL,NULL),(880,'190701',124,'ดอนพุด','18210',NULL,NULL),(881,'190702',124,'ไผ่หลิ่ว','18210',NULL,NULL),(882,'190703',124,'บ้านหลวง','18210',NULL,NULL),(883,'190704',124,'ดงตะงาว','18210',NULL,NULL),(884,'190801',125,'หนองโดน','18190',NULL,NULL),(885,'190802',125,'บ้านกลับ','18190',NULL,NULL),(886,'190803',125,'ดอนทอง','18190',NULL,NULL),(887,'190804',125,'บ้านโปร่ง','18190',NULL,NULL),(888,'190901',126,'พระพุทธบาท','18120',NULL,NULL),(889,'190902',126,'ขุนโขลน','18120',NULL,NULL),(890,'190903',126,'ธารเกษม','18120',NULL,NULL),(891,'190904',126,'นายาว','18120',NULL,NULL),(892,'190905',126,'พุคำจาน','18120',NULL,NULL),(893,'190906',126,'เขาวง','18120',NULL,NULL),(894,'190907',126,'ห้วยป่าหวาย','18120',NULL,NULL),(895,'190908',126,'พุกร่าง','18120',NULL,NULL),(896,'191001',127,'เสาไห้','18160',NULL,NULL),(897,'191002',127,'บ้านยาง','18160',NULL,NULL),(898,'191003',127,'หัวปลวก','18160',NULL,NULL),(899,'191004',127,'งิ้วงาม','18160',NULL,NULL),(900,'191005',127,'ศาลารีไทย','18160',NULL,NULL),(901,'191006',127,'ต้นตาล','18160',NULL,NULL),(902,'191007',127,'ท่าช้าง','18160',NULL,NULL),(903,'191008',127,'พระยาทด','18160',NULL,NULL),(904,'191009',127,'ม่วงงาม','18160',NULL,NULL),(905,'191010',127,'เริงราง','18160',NULL,NULL),(906,'191011',127,'เมืองเก่า','18160',NULL,NULL),(907,'191012',127,'สวนดอกไม้','18160',NULL,NULL),(908,'191101',128,'มวกเหล็ก','18180',NULL,NULL),(909,'191102',128,'มิตรภาพ','18180',NULL,NULL),(910,'191104',128,'หนองย่างเสือ','18180',NULL,NULL),(911,'191105',128,'ลำสมพุง','18180',NULL,NULL),(912,'191107',128,'ลำพญากลาง','18180',NULL,NULL),(913,'191109',128,'ซับสนุ่น','18220',NULL,NULL),(914,'191201',129,'แสลงพัน','18220',NULL,NULL),(915,'191202',129,'คำพราน','18220',NULL,NULL),(916,'191203',129,'วังม่วง','18220',NULL,NULL),(917,'191301',130,'เขาดินพัฒนา','18000',NULL,NULL),(918,'191302',130,'บ้านแก้ง','18000',NULL,NULL),(919,'191303',130,'ผึ้งรวง','18000',NULL,NULL),(920,'191304',130,'พุแค','18240',NULL,NULL),(921,'191305',130,'ห้วยบง','18000',NULL,NULL),(922,'191306',130,'หน้าพระลาน','18240',NULL,NULL),(923,'200101',131,'บางปลาสร้อย','20000',NULL,NULL),(924,'200102',131,'มะขามหย่ง','20000',NULL,NULL),(925,'200103',131,'บ้านโขด','20000',NULL,NULL),(926,'200104',131,'แสนสุข','20000',NULL,NULL),(927,'200105',131,'บ้านสวน','20000',NULL,NULL),(928,'200106',131,'หนองรี','20000',NULL,NULL),(929,'200107',131,'นาป่า','20000',NULL,NULL),(930,'200108',131,'หนองข้างคอก','20000',NULL,NULL),(931,'200109',131,'ดอนหัวฬอ','20000',NULL,NULL),(932,'200110',131,'หนองไม้แดง','20000',NULL,NULL),(933,'200111',131,'บางทราย','20000',NULL,NULL),(934,'200112',131,'คลองตำหรุ','20000',NULL,NULL),(935,'200113',131,'เหมือง','20130',NULL,NULL),(936,'200114',131,'บ้านปึก','20130',NULL,NULL),(937,'200115',131,'ห้วยกะปิ','20000',NULL,NULL),(938,'200116',131,'เสม็ด','20130',NULL,NULL),(939,'200117',131,'อ่างศิลา','20000',NULL,NULL),(940,'200118',131,'สำนักบก','20000',NULL,NULL),(941,'200201',132,'บ้านบึง','20170',NULL,NULL),(942,'200202',132,'คลองกิ่ว','20220',NULL,NULL),(943,'200203',132,'มาบไผ่','20170',NULL,NULL),(944,'200204',132,'หนองซ้ำซาก','20170',NULL,NULL),(945,'200205',132,'หนองบอนแดง','20170',NULL,NULL),(946,'200206',132,'หนองชาก','20170',NULL,NULL),(947,'200207',132,'หนองอิรุณ','20220',NULL,NULL),(948,'200208',132,'หนองไผ่แก้ว','20220',NULL,NULL),(949,'200301',133,'หนองใหญ่','20190',NULL,NULL),(950,'200302',133,'คลองพลู','20190',NULL,NULL),(951,'200303',133,'หนองเสือช้าง','20190',NULL,NULL),(952,'200304',133,'ห้างสูง','20190',NULL,NULL),(953,'200305',133,'เขาซก','20190',NULL,NULL),(954,'200401',134,'บางละมุง','20150',NULL,NULL),(955,'200402',134,'หนองปรือ','20150',NULL,NULL),(956,'200403',134,'หนองปลาไหล','20150',NULL,NULL),(957,'200404',134,'โป่ง','20150',NULL,NULL),(958,'200405',134,'เขาไม้แก้ว','20150',NULL,NULL),(959,'200406',134,'ห้วยใหญ่','20150',NULL,NULL),(960,'200407',134,'ตะเคียนเตี้ย','20150',NULL,NULL),(961,'200408',134,'นาเกลือ','20150',NULL,NULL),(962,'200409',134,'เขตการปกคองพิเศษพัทยา','0',NULL,NULL),(963,'200501',135,'พานทอง','20160',NULL,NULL),(964,'200502',135,'หนองตำลึง','20160',NULL,NULL),(965,'200503',135,'มาบโป่ง','20160',NULL,NULL),(966,'200504',135,'หนองกะขะ','20160',NULL,NULL),(967,'200505',135,'หนองหงษ์','20160',NULL,NULL),(968,'200506',135,'โคกขี้หนอน','20160',NULL,NULL),(969,'200507',135,'บ้านเก่า','20160',NULL,NULL),(970,'200508',135,'หน้าประดู่','20160',NULL,NULL),(971,'200509',135,'บางนาง','20160',NULL,NULL),(972,'200510',135,'เกาะลอย','20160',NULL,NULL),(973,'200511',135,'บางหัก','20160',NULL,NULL),(974,'200601',136,'พนัสนิคม','20140',NULL,NULL),(975,'200602',136,'หน้าพระธาตุ','20140',NULL,NULL),(976,'200603',136,'วัดหลวง','20140',NULL,NULL),(977,'200604',136,'บ้านเชิด','20140',NULL,NULL),(978,'200605',136,'นาเริก','20140',NULL,NULL),(979,'200606',136,'หมอนนาง','20140',NULL,NULL),(980,'200607',136,'สระสี่เหลี่ยม','20140',NULL,NULL),(981,'200608',136,'วัดโบสถ์','20140',NULL,NULL),(982,'200609',136,'กุฎโง้ง','20140',NULL,NULL),(983,'200610',136,'หัวถนน','20140',NULL,NULL),(984,'200611',136,'ท่าข้าม','20140',NULL,NULL),(985,'200613',136,'หนองปรือ','20140',NULL,NULL),(986,'200614',136,'หนองขยาด','20140',NULL,NULL),(987,'200615',136,'ทุ่งขวาง','20140',NULL,NULL),(988,'200616',136,'หนองเหียง','20140',NULL,NULL),(989,'200617',136,'นาวังหิน','20140',NULL,NULL),(990,'200618',136,'บ้านช้าง','20140',NULL,NULL),(991,'200620',136,'โคกเพลาะ','20140',NULL,NULL),(992,'200621',136,'ไร่หลักทอง','20140',NULL,NULL),(993,'200701',137,'ศรีราชา','20110',NULL,NULL),(994,'200702',137,'สุรศักดิ์','20110',NULL,NULL),(995,'200703',137,'ทุ่งสุขลา','20230',NULL,NULL),(996,'200704',137,'บึง','20230',NULL,NULL),(997,'200705',137,'หนองขาม','20110',NULL,NULL),(998,'200706',137,'เขาคันทรง','20110',NULL,NULL),(999,'200707',137,'บางพระ','20110',NULL,NULL),(1000,'200708',137,'บ่อวิน','20230',NULL,NULL),(1001,'200801',138,'ท่าเทววงษ์','20120',NULL,NULL),(1002,'200901',139,'สัตหีบ','20180',NULL,NULL),(1003,'200902',139,'นาจอมเทียน','20250',NULL,NULL),(1004,'200903',139,'พลูตาหลวง','20180',NULL,NULL),(1005,'200904',139,'บางเสร่','20250',NULL,NULL),(1006,'200905',139,'แสมสาร','20180',NULL,NULL),(1007,'201001',140,'บ่อทอง','20270',NULL,NULL),(1008,'201002',140,'วัดสุวรรณ','20270',NULL,NULL),(1009,'201003',140,'บ่อกวางทอง','20270',NULL,NULL),(1010,'201004',140,'ธาตุทอง','20270',NULL,NULL),(1011,'201005',140,'เกษตรสุวรรณ','20270',NULL,NULL),(1012,'201006',140,'พลวงทอง','20270',NULL,NULL),(1013,'201101',141,'เกาะจันทร์','20240',NULL,NULL),(1014,'201102',141,'ท่าบุญมี','20240',NULL,NULL),(1015,'210101',142,'ท่าประดู่','21000',NULL,NULL),(1016,'210102',142,'เชิงเนิน','21000',NULL,NULL),(1017,'210103',142,'ตะพง','21000',NULL,NULL),(1018,'210105',142,'เพ','21160',NULL,NULL),(1019,'210106',142,'แกลง','21160',NULL,NULL),(1020,'210107',142,'บ้านแลง','21000',NULL,NULL),(1021,'210108',142,'นาตาขวัญ','21000',NULL,NULL),(1022,'210109',142,'เนินพระ','21000',NULL,NULL),(1023,'210110',142,'กระเฉด','21100',NULL,NULL),(1024,'210111',142,'ทับมา','21000',NULL,NULL),(1025,'210112',142,'น้ำคอก','21000',NULL,NULL),(1026,'210114',142,'มาบตาพุด','21150',NULL,NULL),(1027,'210115',142,'สำนักทอง','21100',NULL,NULL),(1028,'210201',143,'สำนักท้อน','21130',NULL,NULL),(1029,'210202',143,'พลา','21130',NULL,NULL),(1030,'210203',143,'บ้านฉาง','21130',NULL,NULL),(1031,'210301',144,'ทางเกวียน','21110',NULL,NULL),(1032,'210302',144,'วังหว้า','21110',NULL,NULL),(1033,'210303',144,'ชากโดน','21110',NULL,NULL),(1034,'210304',144,'เนินฆ้อ','21110',NULL,NULL),(1035,'210305',144,'กร่ำ','21190',NULL,NULL),(1036,'210306',144,'ชากพง','21190',NULL,NULL),(1037,'210307',144,'กระแสบน','21110',NULL,NULL),(1038,'210308',144,'บ้านนา','21110',NULL,NULL),(1039,'210309',144,'ทุ่งควายกิน','21110',NULL,NULL),(1040,'210310',144,'กองดิน','22160',NULL,NULL),(1041,'210311',144,'คลองปูน','21170',NULL,NULL),(1042,'210312',144,'พังราด','21110',NULL,NULL),(1043,'210313',144,'ปากน้ำกระแส','21170',NULL,NULL),(1044,'210317',144,'ห้วยยาง','21110',NULL,NULL),(1045,'210318',144,'สองสลึง','21110',NULL,NULL),(1046,'210401',145,'วังจันทร์','21210',NULL,NULL),(1047,'210402',145,'ชุมแสง','21210',NULL,NULL),(1048,'210403',145,'ป่ายุบใน','21210',NULL,NULL),(1049,'210404',145,'พลงตาเอี่ยม','21210',NULL,NULL),(1050,'210501',146,'บ้านค่าย','21120',NULL,NULL),(1051,'210502',146,'หนองละลอก','21120',NULL,NULL),(1052,'210503',146,'หนองตะพาน','21120',NULL,NULL),(1053,'210504',146,'ตาขัน','21120',NULL,NULL),(1054,'210505',146,'บางบุตร','21120',NULL,NULL),(1055,'210506',146,'หนองบัว','21120',NULL,NULL),(1056,'210507',146,'ชากบก','21120',NULL,NULL),(1057,'210601',147,'ปลวกแดง','21140',NULL,NULL),(1058,'210602',147,'ตาสิทธิ์','21140',NULL,NULL),(1059,'210603',147,'ละหาร','21140',NULL,NULL),(1060,'210604',147,'แม่น้ำคู้','21140',NULL,NULL),(1061,'210605',147,'มาบยางพร','21140',NULL,NULL),(1062,'210606',147,'หนองไร่','21140',NULL,NULL),(1063,'210701',148,'น้ำเป็น','21110',NULL,NULL),(1064,'210702',148,'ห้วยทับมอญ','21110',NULL,NULL),(1065,'210703',148,'ชำฆ้อ','21110',NULL,NULL),(1066,'210704',148,'เขาน้อย','21110',NULL,NULL),(1067,'210801',149,'นิคมพัฒนา','21180',NULL,NULL),(1068,'210802',149,'มาบข่า','21180',NULL,NULL),(1069,'210803',149,'พนานิคม','21180',NULL,NULL),(1070,'210804',149,'มะขามคู่','21180',NULL,NULL),(1071,'220101',150,'ตลาด','22000',NULL,NULL),(1072,'220102',150,'วัดใหม่','22000',NULL,NULL),(1073,'220103',150,'คลองนารายณ์','22000',NULL,NULL),(1074,'220104',150,'เกาะขวาง','22000',NULL,NULL),(1075,'220105',150,'คมบาง','22000',NULL,NULL),(1076,'220106',150,'ท่าช้าง','22000',NULL,NULL),(1077,'220107',150,'จันทนิมิต','22000',NULL,NULL),(1078,'220108',150,'บางกะจะ','22000',NULL,NULL),(1079,'220109',150,'แสลง','22000',NULL,NULL),(1080,'220110',150,'หนองบัว','22000',NULL,NULL),(1081,'220111',150,'พลับพลา','22000',NULL,NULL),(1082,'220201',151,'ขลุง','22110',NULL,NULL),(1083,'220202',151,'บ่อ','22110',NULL,NULL),(1084,'220203',151,'เกวียนหัก','22110',NULL,NULL),(1085,'220204',151,'ตะปอน','22110',NULL,NULL),(1086,'220205',151,'บางชัน','22110',NULL,NULL),(1087,'220206',151,'วันยาว','22110',NULL,NULL),(1088,'220207',151,'ซึ้ง','22110',NULL,NULL),(1089,'220208',151,'มาบไพ','22110',NULL,NULL),(1090,'220209',151,'วังสรรพรส','22110',NULL,NULL),(1091,'220210',151,'ตรอกนอง','22110',NULL,NULL),(1092,'220211',151,'ตกพรม','22110',NULL,NULL),(1093,'220212',151,'บ่อเวฬุ','22150',NULL,NULL),(1094,'220301',152,'ท่าใหม่','22120',NULL,NULL),(1095,'220302',152,'ยายร้า','22120',NULL,NULL),(1096,'220303',152,'สีพยา','22120',NULL,NULL),(1097,'220304',152,'บ่อพุ','22120',NULL,NULL),(1098,'220305',152,'พลอยแหวน','22120',NULL,NULL),(1099,'220306',152,'เขาวัว','22120',NULL,NULL),(1100,'220307',152,'เขาบายศรี','22120',NULL,NULL),(1101,'220308',152,'สองพี่น้อง','22120',NULL,NULL),(1102,'220309',152,'ทุ่งเบญจา','22170',NULL,NULL),(1103,'220311',152,'รำพัน','22170',NULL,NULL),(1104,'220312',152,'โขมง','22170',NULL,NULL),(1105,'220313',152,'ตะกาดเง้า','22120',NULL,NULL),(1106,'220314',152,'คลองขุด','22120',NULL,NULL),(1107,'220324',152,'เขาแก้ว','22170',NULL,NULL),(1108,'220401',153,'ทับไทร','22140',NULL,NULL),(1109,'220402',153,'โป่งน้ำร้อน','22140',NULL,NULL),(1110,'220404',153,'หนองตาคง','22140',NULL,NULL),(1111,'220409',153,'เทพนิมิต','22140',NULL,NULL),(1112,'220410',153,'คลองใหญ่','22140',NULL,NULL),(1113,'220501',154,'มะขาม','22150',NULL,NULL),(1114,'220502',154,'ท่าหลวง','22150',NULL,NULL),(1115,'220503',154,'ปัถวี','22150',NULL,NULL),(1116,'220504',154,'วังแซ้ม','22150',NULL,NULL),(1117,'220506',154,'ฉมัน','22150',NULL,NULL),(1118,'220508',154,'อ่างคีรี','22150',NULL,NULL),(1119,'220601',155,'ปากน้ำแหลมสิงห์','22130',NULL,NULL),(1120,'220602',155,'เกาะเปริด','22130',NULL,NULL),(1121,'220603',155,'หนองชิ่ม','22130',NULL,NULL),(1122,'220604',155,'พลิ้ว','22190',NULL,NULL),(1123,'220605',155,'คลองน้ำเค็ม','22190',NULL,NULL),(1124,'220606',155,'บางสระเก้า','22190',NULL,NULL),(1125,'220607',155,'บางกะไชย','22120',NULL,NULL),(1126,'220701',156,'ปะตง','22180',NULL,NULL),(1127,'220702',156,'ทุ่งขนาน','22180',NULL,NULL),(1128,'220703',156,'ทับช้าง','22180',NULL,NULL),(1129,'220704',156,'ทรายขาว','22180',NULL,NULL),(1130,'220705',156,'สะตอน','22180',NULL,NULL),(1131,'220801',157,'แก่งหางแมว','22160',NULL,NULL),(1132,'220802',157,'ขุนซ่อง','22160',NULL,NULL),(1133,'220803',157,'สามพี่น้อง','22160',NULL,NULL),(1134,'220804',157,'พวา','22160',NULL,NULL),(1135,'220805',157,'เขาวงกต','22160',NULL,NULL),(1136,'220901',158,'นายายอาม','22160',NULL,NULL),(1137,'220902',158,'วังโตนด','22170',NULL,NULL),(1138,'220903',158,'กระแจะ','22170',NULL,NULL),(1139,'220904',158,'สนามไชย','22170',NULL,NULL),(1140,'220905',158,'ช้างข้าม','22160',NULL,NULL),(1141,'220906',158,'วังใหม่','22170',NULL,NULL),(1142,'221001',159,'ซากไทย','22210',NULL,NULL),(1143,'221002',159,'พลวง','22210',NULL,NULL),(1144,'221003',159,'ตะเคียนทอง','22210',NULL,NULL),(1145,'221004',159,'คลองพลู','22210',NULL,NULL),(1146,'221005',159,'จันทเขลม','22210',NULL,NULL),(1147,'230102',160,'หนองเสม็ด','23000',NULL,NULL),(1148,'230103',160,'หนองโสน','23000',NULL,NULL),(1149,'230104',160,'หนองคันทรง','23000',NULL,NULL),(1150,'230105',160,'ห้วงน้ำขาว','23000',NULL,NULL),(1151,'230106',160,'อ่าวใหญ่','23000',NULL,NULL),(1152,'230107',160,'วังกระแจะ','23000',NULL,NULL),(1153,'230108',160,'ห้วยแร้ง','23000',NULL,NULL),(1154,'230109',160,'เนินทราย','23000',NULL,NULL),(1155,'230110',160,'ท่าพริก','23000',NULL,NULL),(1156,'230111',160,'ท่ากุ่ม','23000',NULL,NULL),(1157,'230112',160,'ตะกาง','23000',NULL,NULL),(1158,'230113',160,'ชำราก','23000',NULL,NULL),(1159,'230114',160,'แหลมกลัด','23000',NULL,NULL),(1160,'230201',161,'คลองใหญ่','23110',NULL,NULL),(1161,'230202',161,'ไม้รูด','23110',NULL,NULL),(1162,'230203',161,'หาดเล็ก','23110',NULL,NULL),(1163,'230301',162,'เขาสมิง','23130',NULL,NULL),(1164,'230302',162,'แสนตุ้ง','23150',NULL,NULL),(1165,'230303',162,'วังตะเคียน','23130',NULL,NULL),(1166,'230304',162,'ท่าโสม','23150',NULL,NULL),(1167,'230305',162,'สะตอ','23150',NULL,NULL),(1168,'230306',162,'ประณีต','23150',NULL,NULL),(1169,'230307',162,'เทพนิมิต','23150',NULL,NULL),(1170,'230308',162,'ทุ่งนนทรี','23130',NULL,NULL),(1171,'230401',163,'บ่อพลอย','23140',NULL,NULL),(1172,'230402',163,'ช้างทูน','23140',NULL,NULL),(1173,'230403',163,'ด่านชุมพล','23140',NULL,NULL),(1174,'230404',163,'หนองบอน','23140',NULL,NULL),(1175,'230405',163,'นนทรีย์','23140',NULL,NULL),(1176,'230501',164,'แหลมงอบ','23120',NULL,NULL),(1177,'230502',164,'น้ำเชี่ยว','23120',NULL,NULL),(1178,'230503',164,'บางปิด','23120',NULL,NULL),(1179,'230507',164,'คลองใหญ่','23120',NULL,NULL),(1180,'230601',165,'เกาะหมาก','23000',NULL,NULL),(1181,'230602',165,'เกาะกูด','23000',NULL,NULL),(1182,'230701',166,'เกาะช้าง','23170',NULL,NULL),(1183,'230702',166,'เกาะช้างใต้','23170',NULL,NULL),(1184,'240101',167,'หน้าเมือง','24000',NULL,NULL),(1185,'240102',167,'ท่าไข่','24000',NULL,NULL),(1186,'240103',167,'บ้านใหม่','24000',NULL,NULL),(1187,'240104',167,'คลองนา','24000',NULL,NULL),(1188,'240105',167,'บางตีนเป็ด','24000',NULL,NULL),(1189,'240106',167,'บางไผ่','24000',NULL,NULL),(1190,'240107',167,'คลองจุกกระเฌอ','24000',NULL,NULL),(1191,'240108',167,'บางแก้ว','24000',NULL,NULL),(1192,'240109',167,'บางขวัญ','24000',NULL,NULL),(1193,'240110',167,'คลองนครเนื่องเขต','24000',NULL,NULL),(1194,'240111',167,'วังตะเคียน','24000',NULL,NULL),(1195,'240112',167,'โสธร','24000',NULL,NULL),(1196,'240113',167,'บางพระ','24000',NULL,NULL),(1197,'240114',167,'บางกระไห','24000',NULL,NULL),(1198,'240115',167,'หนามแดง','24000',NULL,NULL),(1199,'240116',167,'คลองเปรง','24000',NULL,NULL),(1200,'240117',167,'คลองอุดมชลจร','24000',NULL,NULL),(1201,'240118',167,'คลองหลวงแพ่ง','24000',NULL,NULL),(1202,'240119',167,'บางเตย','24000',NULL,NULL),(1203,'240201',168,'บางคล้า','24110',NULL,NULL),(1204,'240204',168,'บางสวน','24110',NULL,NULL),(1205,'240208',168,'บางกระเจ็ด','24110',NULL,NULL),(1206,'240209',168,'ปากน้ำ','24110',NULL,NULL),(1207,'240210',168,'ท่าทองหลาง','24110',NULL,NULL),(1208,'240211',168,'สาวชะโงก','24110',NULL,NULL),(1209,'240212',168,'เสม็ดเหนือ','24110',NULL,NULL),(1210,'240213',168,'เสม็ดใต้','24110',NULL,NULL),(1211,'240214',168,'หัวไทร','24110',NULL,NULL),(1212,'240301',169,'บางน้ำเปรี้ยว','24150',NULL,NULL),(1213,'240302',169,'บางขนาก','24150',NULL,NULL),(1214,'240303',169,'สิงโตทอง','24150',NULL,NULL),(1215,'240304',169,'หมอนทอง','24150',NULL,NULL),(1216,'240305',169,'บึงน้ำรักษ์','24170',NULL,NULL),(1217,'240306',169,'ดอนเกาะกา','24170',NULL,NULL),(1218,'240307',169,'โยธะกา','24150',NULL,NULL),(1219,'240308',169,'ดอนฉิมพลี','24170',NULL,NULL),(1220,'240309',169,'ศาลาแดง','24000',NULL,NULL),(1221,'240310',169,'โพรงอากาศ','24150',NULL,NULL),(1222,'240401',170,'บางปะกง','24130',NULL,NULL),(1223,'240402',170,'ท่าสะอ้าน','24130',NULL,NULL),(1224,'240403',170,'บางวัว','24180',NULL,NULL),(1225,'240404',170,'บางสมัคร','24180',NULL,NULL),(1226,'240405',170,'บางผึ้ง','24130',NULL,NULL),(1227,'240406',170,'บางเกลือ','24180',NULL,NULL),(1228,'240407',170,'สองคลอง','24130',NULL,NULL),(1229,'240408',170,'หนองจอก','24130',NULL,NULL),(1230,'240409',170,'พิมพา','24130',NULL,NULL),(1231,'240410',170,'ท่าข้าม','24130',NULL,NULL),(1232,'240411',170,'หอมศีล','24180',NULL,NULL),(1233,'240412',170,'เขาดิน','24130',NULL,NULL),(1234,'240501',171,'บ้านโพธิ์','24140',NULL,NULL),(1235,'240502',171,'เกาะไร่','24140',NULL,NULL),(1236,'240503',171,'คลองขุด','24140',NULL,NULL),(1237,'240504',171,'คลองบ้านโพธิ์','24140',NULL,NULL),(1238,'240505',171,'คลองประเวศ','24140',NULL,NULL),(1239,'240506',171,'ดอนทราย','24140',NULL,NULL),(1240,'240507',171,'เทพราช','24140',NULL,NULL),(1241,'240508',171,'ท่าพลับ','24140',NULL,NULL),(1242,'240509',171,'หนองตีนนก','24140',NULL,NULL),(1243,'240510',171,'หนองบัว','24140',NULL,NULL),(1244,'240511',171,'บางซ่อน','24140',NULL,NULL),(1245,'240512',171,'บางกรูด','24140',NULL,NULL),(1246,'240513',171,'แหลมประดู่','24140',NULL,NULL),(1247,'240514',171,'ลาดขวาง','24140',NULL,NULL),(1248,'240515',171,'สนามจันทร์','24140',NULL,NULL),(1249,'240516',171,'แสนภูดาษ','24140',NULL,NULL),(1250,'240517',171,'สิบเอ็ดศอก','24140',NULL,NULL),(1251,'240601',172,'เกาะขนุน','24120',NULL,NULL),(1252,'240602',172,'บ้านซ่อง','24120',NULL,NULL),(1253,'240603',172,'พนมสารคาม','24120',NULL,NULL),(1254,'240604',172,'เมืองเก่า','24120',NULL,NULL),(1255,'240605',172,'หนองยาว','24120',NULL,NULL),(1256,'240606',172,'ท่าถ่าน','24120',NULL,NULL),(1257,'240607',172,'หนองแหน','24120',NULL,NULL),(1258,'240608',172,'เขาหินซ้อน','24120',NULL,NULL),(1259,'240701',173,'บางคา','24120',NULL,NULL),(1260,'240702',173,'เมืองใหม่','24120',NULL,NULL),(1261,'240703',173,'ดงน้อย','24120',NULL,NULL),(1262,'240801',174,'คู้ยายหมี','24160',NULL,NULL),(1263,'240802',174,'ท่ากระดาน','24160',NULL,NULL),(1264,'240803',174,'ทุ่งพระยา','24160',NULL,NULL),(1265,'240805',174,'ลาดกระทิง','24160',NULL,NULL),(1266,'240901',175,'แปลงยาว','24190',NULL,NULL),(1267,'240902',175,'วังเย็น','24190',NULL,NULL),(1268,'240903',175,'หัวสำโรง','24190',NULL,NULL),(1269,'240904',175,'หนองไม้แก่น','24190',NULL,NULL),(1270,'241001',176,'ท่าตะเกียบ','24160',NULL,NULL),(1271,'241002',176,'คลองตะเกรา','24160',NULL,NULL),(1272,'241101',177,'ก้อนแก้ว','24000',NULL,NULL),(1273,'241102',177,'คลองเขื่อน','24000',NULL,NULL),(1274,'241103',177,'บางเล่า','24000',NULL,NULL),(1275,'241104',177,'บางโรง','24000',NULL,NULL),(1276,'241105',177,'บางตลาด','24110',NULL,NULL),(1277,'250101',178,'หน้าเมือง','25000',NULL,NULL),(1278,'250102',178,'รอบเมือง','25000',NULL,NULL),(1279,'250103',178,'วัดโบสถ์','25000',NULL,NULL),(1280,'250104',178,'บางเดชะ','25000',NULL,NULL),(1281,'250105',178,'ท่างาม','25000',NULL,NULL),(1282,'250106',178,'บางบริบูรณ์','25000',NULL,NULL),(1283,'250107',178,'ดงพระราม','25000',NULL,NULL),(1284,'250108',178,'บ้านพระ','25230',NULL,NULL),(1285,'250109',178,'โคกไม้ลาย','25230',NULL,NULL),(1286,'250111',178,'ดงขี้เหล็ก','25000',NULL,NULL),(1287,'250112',178,'เนินหอม','25230',NULL,NULL),(1288,'250113',178,'โนนห้อม','25000',NULL,NULL),(1289,'250201',179,'กบินทร์','25110',NULL,NULL),(1290,'250202',179,'เมืองเก่า','25240',NULL,NULL),(1291,'250203',179,'วังดาล','25110',NULL,NULL),(1292,'250204',179,'นนทรี','25110',NULL,NULL),(1293,'250205',179,'ย่านรี','25110',NULL,NULL),(1294,'250206',179,'วังตะเคียน','25110',NULL,NULL),(1295,'250207',179,'หาดนางแก้ว','25110',NULL,NULL),(1296,'250208',179,'ลาดตะเคียน','25110',NULL,NULL),(1297,'250209',179,'บ้านนา','25110',NULL,NULL),(1298,'250210',179,'บ่อทอง','25110',NULL,NULL),(1299,'250211',179,'หนองกี่','25110',NULL,NULL),(1300,'250212',179,'นาแขม','25110',NULL,NULL),(1301,'250213',179,'เขาไม้แก้ว','25110',NULL,NULL),(1302,'250214',179,'วังท่าช้าง','25110',NULL,NULL),(1303,'250301',180,'นาดี','25220',NULL,NULL),(1304,'250302',180,'สำพันตา','25220',NULL,NULL),(1305,'250303',180,'สะพานหิน','25220',NULL,NULL),(1306,'250304',180,'ทุ่งโพธิ์','25220',NULL,NULL),(1307,'250305',180,'แก่งดินสอ','25220',NULL,NULL),(1308,'250306',180,'บุพราหมณ์','25220',NULL,NULL),(1309,'250601',181,'บ้านสร้าง','25150',NULL,NULL),(1310,'250602',181,'บางกระเบา','25150',NULL,NULL),(1311,'250603',181,'บางเตย','25150',NULL,NULL),(1312,'250604',181,'บางยาง','25150',NULL,NULL),(1313,'250605',181,'บางแตน','25150',NULL,NULL),(1314,'250606',181,'บางพลวง','25150',NULL,NULL),(1315,'250607',181,'บางปลาร้า','25150',NULL,NULL),(1316,'250608',181,'บางขาม','25150',NULL,NULL),(1317,'250609',181,'กระทุ่มแพ้ว','25150',NULL,NULL),(1318,'250701',182,'ประจันตคาม','25130',NULL,NULL),(1319,'250702',182,'เกาะลอย','25130',NULL,NULL),(1320,'250703',182,'บ้านหอย','25130',NULL,NULL),(1321,'250704',182,'หนองแสง','25130',NULL,NULL),(1322,'250705',182,'ดงบัง','25130',NULL,NULL),(1323,'250706',182,'คำโตนด','25130',NULL,NULL),(1324,'250707',182,'บุฝ้าย','25130',NULL,NULL),(1325,'250708',182,'หนองแก้ว','25130',NULL,NULL),(1326,'250709',182,'โพธิ์งาม','25130',NULL,NULL),(1327,'250801',183,'ศรีมหาโพธิ','25140',NULL,NULL),(1328,'250802',183,'สัมพันธ์','25140',NULL,NULL),(1329,'250803',183,'บ้านทาม','25140',NULL,NULL),(1330,'250804',183,'ท่าตูม','25140',NULL,NULL),(1331,'250805',183,'บางกุ้ง','25140',NULL,NULL),(1332,'250806',183,'ดงกระทงยาม','25140',NULL,NULL),(1333,'250807',183,'หนองโพรง','25140',NULL,NULL),(1334,'250808',183,'หัวหว้า','25140',NULL,NULL),(1335,'250809',183,'หาดยาง','25140',NULL,NULL),(1336,'250810',183,'กรอกสมบูรณ์','25140',NULL,NULL),(1337,'250901',184,'โคกปีบ','25190',NULL,NULL),(1338,'250902',184,'โคกไทย','25190',NULL,NULL),(1339,'250903',184,'คู้ลำพัน','25190',NULL,NULL),(1340,'250904',184,'ไผ่ชะเลือด','25190',NULL,NULL),(1341,'260101',185,'นครนายก','26000',NULL,NULL),(1342,'260102',185,'ท่าช้าง','26000',NULL,NULL),(1343,'260103',185,'บ้านใหญ่','26000',NULL,NULL),(1344,'260104',185,'วังกระโจม','26000',NULL,NULL),(1345,'260105',185,'ท่าทราย','26000',NULL,NULL),(1346,'260106',185,'ดอนยอ','26000',NULL,NULL),(1347,'260107',185,'ศรีจุฬา','26000',NULL,NULL),(1348,'260108',185,'ดงละคร','26000',NULL,NULL),(1349,'260109',185,'ศรีนาวา','26000',NULL,NULL),(1350,'260110',185,'สาริกา','26000',NULL,NULL),(1351,'260111',185,'หินตั้ง','26000',NULL,NULL),(1352,'260112',185,'เขาพระ','26000',NULL,NULL),(1353,'260113',185,'พรหมณี','26000',NULL,NULL),(1354,'260201',186,'เกาะหวาย','26130',NULL,NULL),(1355,'260202',186,'เกาะโพธิ์','26130',NULL,NULL),(1356,'260203',186,'ปากพลี','26130',NULL,NULL),(1357,'260204',186,'โคกกรวด','26130',NULL,NULL),(1358,'260205',186,'ท่าเรือ','26130',NULL,NULL),(1359,'260206',186,'หนองแสง','26130',NULL,NULL),(1360,'260207',186,'นาหินลาด','26130',NULL,NULL),(1361,'260301',187,'บ้านนา','26110',NULL,NULL),(1362,'260302',187,'บ้านพร้าว','26110',NULL,NULL),(1363,'260303',187,'บ้านพริก','26110',NULL,NULL),(1364,'260304',187,'อาษา','26110',NULL,NULL),(1365,'260305',187,'ทองหลาง','26110',NULL,NULL),(1366,'260306',187,'บางอ้อ','26110',NULL,NULL),(1367,'260307',187,'พิกุลออก','26110',NULL,NULL),(1368,'260308',187,'ป่าขะ','26110',NULL,NULL),(1369,'260309',187,'เขาเพิ่ม','26110',NULL,NULL),(1370,'260310',187,'ศรีกะอาง','26110',NULL,NULL),(1371,'260401',188,'พระอาจารย์','26120',NULL,NULL),(1372,'260402',188,'บึงศาล','26120',NULL,NULL),(1373,'260403',188,'ศรีษะกระบือ','26120',NULL,NULL),(1374,'260404',188,'โพธิ์แทน','26120',NULL,NULL),(1375,'260405',188,'บางสมบูรณ์','26120',NULL,NULL),(1376,'260406',188,'ทรายมูล','26120',NULL,NULL),(1377,'260407',188,'บางปลากด','26120',NULL,NULL),(1378,'260408',188,'บางลูกเสือ','26120',NULL,NULL),(1379,'260409',188,'องครักษ์','26120',NULL,NULL),(1380,'260410',188,'ชุมพล','26120',NULL,NULL),(1381,'260411',188,'คลองใหญ่','26120',NULL,NULL),(1382,'270101',189,'สระแก้ว','27000',NULL,NULL),(1383,'270102',189,'บ้านแก้ง','27000',NULL,NULL),(1384,'270103',189,'ศาลาลำดวน','27000',NULL,NULL),(1385,'270104',189,'โคกปี่ฆ้อง','27000',NULL,NULL),(1386,'270105',189,'ท่าแยก','27000',NULL,NULL),(1387,'270106',189,'ท่าเกษม','27000',NULL,NULL),(1388,'270108',189,'สระขวัญ','27000',NULL,NULL),(1389,'270111',189,'หนองบอน','27000',NULL,NULL),(1390,'270201',190,'คลองหาด','27260',NULL,NULL),(1391,'270202',190,'ไทยอุดม','27260',NULL,NULL),(1392,'270203',190,'ซับมะกรูด','27260',NULL,NULL),(1393,'270204',190,'ไทรเดี่ยว','27260',NULL,NULL),(1394,'270205',190,'คลองไก่เถื่อน','27260',NULL,NULL),(1395,'270206',190,'เบญจขร','27260',NULL,NULL),(1396,'270207',190,'ไทรทอง','27260',NULL,NULL),(1397,'270301',191,'ตาพระยา','27180',NULL,NULL),(1398,'270302',191,'ทัพเสด็จ','27180',NULL,NULL),(1399,'270306',191,'ทัพราช','27180',NULL,NULL),(1400,'270307',191,'ทัพไทย','27180',NULL,NULL),(1401,'270309',191,'โคคลาน','27180',NULL,NULL),(1402,'270401',192,'วังน้ำเย็น','27210',NULL,NULL),(1403,'270403',192,'ตาหลังใน','27210',NULL,NULL),(1404,'270405',192,'คลองหินปูน','27210',NULL,NULL),(1405,'270406',192,'ทุ่งมหาเจริญ','27210',NULL,NULL),(1406,'270501',193,'วัฒนานคร','27160',NULL,NULL),(1407,'270502',193,'ท่าเกวียน','27160',NULL,NULL),(1408,'270503',193,'ผักขะ','27160',NULL,NULL),(1409,'270504',193,'โนนหมากเค็ง','27160',NULL,NULL),(1410,'270505',193,'หนองน้ำใส','27160',NULL,NULL),(1411,'270506',193,'ช่องกุ่ม','27160',NULL,NULL),(1412,'270507',193,'หนองแวง','27160',NULL,NULL),(1413,'270508',193,'แซร์ออ','27160',NULL,NULL),(1414,'270509',193,'หนองหมากฝ้าย','27160',NULL,NULL),(1415,'270510',193,'หนองตะเคียนบอน','27160',NULL,NULL),(1416,'270511',193,'ห้วยโจด','27160',NULL,NULL),(1417,'270601',194,'อรัญประเทศ','27120',NULL,NULL),(1418,'270602',194,'เมืองไผ่','27120',NULL,NULL),(1419,'270603',194,'หันทราย','27120',NULL,NULL),(1420,'270604',194,'คลองน้ำใส','27120',NULL,NULL),(1421,'270605',194,'ท่าข้าม','27120',NULL,NULL),(1422,'270606',194,'ป่าไร่','27120',NULL,NULL),(1423,'270607',194,'ทับพริก','27120',NULL,NULL),(1424,'270608',194,'บ้านใหม่หนองไทร','27120',NULL,NULL),(1425,'270609',194,'ผ่านศึก','27120',NULL,NULL),(1426,'270610',194,'หนองสังข์','27120',NULL,NULL),(1427,'270611',194,'คลองทับจันทร์','27120',NULL,NULL),(1428,'270612',194,'ฟากห้วย','27120',NULL,NULL),(1429,'270613',194,'บ้านด่าน','27120',NULL,NULL),(1430,'270701',195,'เขาฉกรรจ์','27000',NULL,NULL),(1431,'270702',195,'หนองหว้า','27000',NULL,NULL),(1432,'270703',195,'พระเพลิง','27000',NULL,NULL),(1433,'270704',195,'เขาสามสิบ','27000',NULL,NULL),(1434,'270801',196,'โคกสูง','27120',NULL,NULL),(1435,'270802',196,'หนองม่วง','27180',NULL,NULL),(1436,'270803',196,'หนองแวง','27180',NULL,NULL),(1437,'270804',196,'โนนหมากมุ่น','27120',NULL,NULL),(1438,'270901',197,'วังสมบูรณ์','27250',NULL,NULL),(1439,'270902',197,'วังใหม่','27250',NULL,NULL),(1440,'270903',197,'วังทอง','27250',NULL,NULL),(1441,'300102',198,'โพธิ์กลาง','30000',NULL,NULL),(1442,'300103',198,'หนองจะบก','30000',NULL,NULL),(1443,'300104',198,'โคกสูง','30310',NULL,NULL),(1444,'300105',198,'มะเริง','30000',NULL,NULL),(1445,'300106',198,'หนองระเวียง','30000',NULL,NULL),(1446,'300107',198,'ปรุใหญ่','30000',NULL,NULL),(1447,'300108',198,'หมื่นไวย','30000',NULL,NULL),(1448,'300109',198,'พลกรัง','30000',NULL,NULL),(1449,'300110',198,'หนองไผ่ล้อม','30000',NULL,NULL),(1450,'300111',198,'หัวทะเล','30000',NULL,NULL),(1451,'300112',198,'บ้านเกาะ','30000',NULL,NULL),(1452,'300114',198,'พุดซา','30000',NULL,NULL),(1453,'300115',198,'บ้านโพธิ์','30310',NULL,NULL),(1454,'300116',198,'จอหอ','30310',NULL,NULL),(1455,'300117',198,'โคกกรวด','30280',NULL,NULL),(1456,'300118',198,'ไชยมงคล','30000',NULL,NULL),(1457,'300119',198,'หนองบัวศาลา','30000',NULL,NULL),(1458,'300120',198,'สุรนารี','30000',NULL,NULL),(1459,'300121',198,'สีมุม','30000',NULL,NULL),(1460,'300122',198,'ตลาด','30310',NULL,NULL),(1461,'300123',198,'พะเนา','30000',NULL,NULL),(1462,'300124',198,'หนองกระทุ่ม','30000',NULL,NULL),(1463,'300125',198,'หนองไข่น้ำ','30310',NULL,NULL),(1464,'300201',199,'แชะ','30250',NULL,NULL),(1465,'300202',199,'เฉลียง','30250',NULL,NULL),(1466,'300203',199,'ครบุรี','30250',NULL,NULL),(1467,'300204',199,'โคกกระชาย','30250',NULL,NULL),(1468,'300205',199,'จระเข้หิน','30250',NULL,NULL),(1469,'300206',199,'มาบตะโกเอน','30250',NULL,NULL),(1470,'300207',199,'อรพิมพ์','30250',NULL,NULL),(1471,'300208',199,'บ้านใหม่','30250',NULL,NULL),(1472,'300209',199,'ลำเพียก','30250',NULL,NULL),(1473,'300210',199,'ครบุรีใต้','30250',NULL,NULL),(1474,'300211',199,'ตะแบกบาน','30250',NULL,NULL),(1475,'300212',199,'สระว่านพระยา','30250',NULL,NULL),(1476,'300301',200,'เสิงสาง','30330',NULL,NULL),(1477,'300302',200,'สระตะเคียน','30330',NULL,NULL),(1478,'300303',200,'โนนสมบูรณ์','30330',NULL,NULL),(1479,'300304',200,'กุดโบสถ์','30330',NULL,NULL),(1480,'300305',200,'สุขไพบูลย์','30330',NULL,NULL),(1481,'300306',200,'บ้านราษฎร์','30330',NULL,NULL),(1482,'300401',201,'เมืองคง','30260',NULL,NULL),(1483,'300402',201,'คูขาด','30260',NULL,NULL),(1484,'300403',201,'เทพาลัย','30260',NULL,NULL),(1485,'300404',201,'ตาจั่น','30260',NULL,NULL),(1486,'300405',201,'บ้านปรางค์','30260',NULL,NULL),(1487,'300406',201,'หนองมะนาว','30260',NULL,NULL),(1488,'300407',201,'หนองบัว','30260',NULL,NULL),(1489,'300408',201,'โนนเต็ง','30260',NULL,NULL),(1490,'300409',201,'ดอนใหญ่','30260',NULL,NULL),(1491,'300410',201,'ขามสมบูรณ์','30260',NULL,NULL),(1492,'300501',202,'บ้านเหลื่อม','30350',NULL,NULL),(1493,'300502',202,'วังโพธิ์','30350',NULL,NULL),(1494,'300503',202,'โคกกระเบื้อง','30350',NULL,NULL),(1495,'300504',202,'ช่อระกา','30350',NULL,NULL),(1496,'300601',203,'จักราช','30230',NULL,NULL),(1497,'300603',203,'ทองหลาง','30230',NULL,NULL),(1498,'300604',203,'สีสุก','30230',NULL,NULL),(1499,'300605',203,'หนองขาม','30230',NULL,NULL),(1500,'300607',203,'หนองพลวง','30230',NULL,NULL),(1501,'300610',203,'ศรีละกอ','30230',NULL,NULL),(1502,'300611',203,'คลองเมือง','30230',NULL,NULL),(1503,'300613',203,'หินโคน','30230',NULL,NULL),(1504,'300701',204,'กระโทก','30190',NULL,NULL),(1505,'300702',204,'พลับพลา','30190',NULL,NULL),(1506,'300703',204,'ท่าอ่าง','30190',NULL,NULL),(1507,'300704',204,'ทุ่งอรุณ','30190',NULL,NULL),(1508,'300705',204,'ท่าลาดขาว','30190',NULL,NULL),(1509,'300706',204,'ท่าจะหลุง','30190',NULL,NULL),(1510,'300707',204,'ท่าเยี่ยม','30190',NULL,NULL),(1511,'300708',204,'โชคชัย','30190',NULL,NULL),(1512,'300709',204,'ละลมใหม่พัฒนา','30190',NULL,NULL),(1513,'300710',204,'ด่านเกวียน','30190',NULL,NULL),(1514,'300801',205,'กุดพิมาน','30210',NULL,NULL),(1515,'300802',205,'ด่านขุนทด','30210',NULL,NULL),(1516,'300803',205,'ด่านนอก','30210',NULL,NULL),(1517,'300804',205,'ด่านใน','30210',NULL,NULL),(1518,'300805',205,'ตะเคียน','30210',NULL,NULL),(1519,'300806',205,'บ้านเก่า','30210',NULL,NULL),(1520,'300807',205,'บ้านแปรง','36220',NULL,NULL),(1521,'300808',205,'พันชนะ','30210',NULL,NULL),(1522,'300809',205,'สระจรเข้','30210',NULL,NULL),(1523,'300810',205,'หนองกราด','30210',NULL,NULL),(1524,'300811',205,'หนองบัวตะเกียด','30210',NULL,NULL),(1525,'300812',205,'หนองบัวละคร','30210',NULL,NULL),(1526,'300813',205,'หินดาด','30210',NULL,NULL),(1527,'300815',205,'ห้วยบง','30210',NULL,NULL),(1528,'300817',205,'โนนเมืองพัฒนา','30210',NULL,NULL),(1529,'300818',205,'หนองไทร','36220',NULL,NULL),(1530,'300901',206,'โนนไทย','30220',NULL,NULL),(1531,'300902',206,'ด่านจาก','30220',NULL,NULL),(1532,'300903',206,'กำปัง','30220',NULL,NULL),(1533,'300904',206,'สำโรง','30220',NULL,NULL),(1534,'300905',206,'ค้างพลู','30220',NULL,NULL),(1535,'300906',206,'บ้านวัง','30220',NULL,NULL),(1536,'300907',206,'บัลลังก์','30220',NULL,NULL),(1537,'300908',206,'สายออ','30220',NULL,NULL),(1538,'300909',206,'ถนนโพธิ์','30220',NULL,NULL),(1539,'300914',206,'มะค่า','30220',NULL,NULL),(1540,'301001',207,'โนนสูง','30160',NULL,NULL),(1541,'301002',207,'ใหม่','30160',NULL,NULL),(1542,'301003',207,'โตนด','30160',NULL,NULL),(1543,'301004',207,'บิง','30160',NULL,NULL),(1544,'301005',207,'ดอนชมพู','30160',NULL,NULL),(1545,'301006',207,'ธารปราสาท','30240',NULL,NULL),(1546,'301007',207,'หลุมข้าว','30160',NULL,NULL),(1547,'301008',207,'มะค่า','30160',NULL,NULL),(1548,'301009',207,'พลสงคราม','30160',NULL,NULL),(1549,'301010',207,'จันอัด','30160',NULL,NULL),(1550,'301011',207,'ขามเฒ่า','30160',NULL,NULL),(1551,'301012',207,'ด่านคล้า','30160',NULL,NULL),(1552,'301013',207,'ลำคอหงษ์','30160',NULL,NULL),(1553,'301014',207,'เมืองปราสาท','30160',NULL,NULL),(1554,'301015',207,'ดอนหวาย','30160',NULL,NULL),(1555,'301016',207,'ลำมูล','30160',NULL,NULL),(1556,'301101',208,'ขามสะแกแสง','30290',NULL,NULL),(1557,'301102',208,'โนนเมือง','30290',NULL,NULL),(1558,'301103',208,'เมืองนาท','30290',NULL,NULL),(1559,'301104',208,'ชีวึก','30290',NULL,NULL),(1560,'301105',208,'พะงาด','30290',NULL,NULL),(1561,'301106',208,'หนองหัวฟาน','30290',NULL,NULL),(1562,'301107',208,'เมืองเกษตร','30290',NULL,NULL),(1563,'301201',209,'บัวใหญ่','30120',NULL,NULL),(1564,'301203',209,'ห้วยยาง','30120',NULL,NULL),(1565,'301204',209,'เสมาใหญ่','30120',NULL,NULL),(1566,'301206',209,'ดอนตะหนิน','30120',NULL,NULL),(1567,'301207',209,'หนองบัวสะอาด','30120',NULL,NULL),(1568,'301208',209,'โนนทองหลาง','30120',NULL,NULL),(1569,'301214',209,'กุดจอก','30120',NULL,NULL),(1570,'301215',209,'ด่านช้าง','30120',NULL,NULL),(1571,'301220',209,'ขุนทอง','30120',NULL,NULL),(1572,'301224',209,'หนองแจ้งใหญ่','30120',NULL,NULL),(1573,'301301',210,'ประทาย','30180',NULL,NULL),(1574,'301303',210,'กระทุ่มราย','30180',NULL,NULL),(1575,'301304',210,'วังไม้แดง','30180',NULL,NULL),(1576,'301306',210,'ตลาดไทร','30180',NULL,NULL),(1577,'301307',210,'หนองพลวง','30180',NULL,NULL),(1578,'301308',210,'หนองค่าย','30180',NULL,NULL),(1579,'301309',210,'หันห้วยทราย','30180',NULL,NULL),(1580,'301310',210,'ดอนมัน','30180',NULL,NULL),(1581,'301313',210,'นางรำ','30180',NULL,NULL),(1582,'301314',210,'โนนเพ็ด','30180',NULL,NULL),(1583,'301315',210,'ทุ่งสว่าง','30180',NULL,NULL),(1584,'301317',210,'โคกกลาง','30180',NULL,NULL),(1585,'301318',210,'เมืองโดน','30180',NULL,NULL),(1586,'301401',211,'เมืองปัก','30150',NULL,NULL),(1587,'301402',211,'ตะคุ','30150',NULL,NULL),(1588,'301403',211,'โคกไทย','30150',NULL,NULL),(1589,'301404',211,'สำโรง','30150',NULL,NULL),(1590,'301405',211,'ตะขบ','30150',NULL,NULL),(1591,'301406',211,'นกออก','30150',NULL,NULL),(1592,'301407',211,'ดอน','30150',NULL,NULL),(1593,'301409',211,'ตูม','30150',NULL,NULL),(1594,'301410',211,'งิ้ว','30150',NULL,NULL),(1595,'301411',211,'สะแกราช','30150',NULL,NULL),(1596,'301412',211,'ลำนางแก้ว','30150',NULL,NULL),(1597,'301416',211,'ภูหลวง','30150',NULL,NULL),(1598,'301417',211,'ธงชัยเหนือ','30150',NULL,NULL),(1599,'301418',211,'สุขเกษม','30150',NULL,NULL),(1600,'301419',211,'เกษมทรัพย์','30150',NULL,NULL),(1601,'301420',211,'บ่อปลาทอง','30150',NULL,NULL),(1602,'301501',212,'ในเมือง','30110',NULL,NULL),(1603,'301502',212,'สัมฤทธิ์','30110',NULL,NULL),(1604,'301503',212,'โบสถ์','30110',NULL,NULL),(1605,'301504',212,'กระเบื้องใหญ่','30110',NULL,NULL),(1606,'301505',212,'ท่าหลวง','30110',NULL,NULL),(1607,'301506',212,'รังกาใหญ่','30110',NULL,NULL),(1608,'301507',212,'ชีวาน','30110',NULL,NULL),(1609,'301508',212,'นิคมสร้างตนเอง','30110',NULL,NULL),(1610,'301509',212,'กระชอน','30110',NULL,NULL),(1611,'301510',212,'ดงใหญ่','30110',NULL,NULL),(1612,'301511',212,'ธารละหลอด','30110',NULL,NULL),(1613,'301512',212,'หนองระเวียง','30110',NULL,NULL),(1614,'301601',213,'ห้วยแถลง','30240',NULL,NULL),(1615,'301602',213,'ทับสวาย','30240',NULL,NULL),(1616,'301603',213,'เมืองพลับพลา','30240',NULL,NULL),(1617,'301604',213,'หลุ่งตะเคียน','30240',NULL,NULL),(1618,'301605',213,'หินดาด','30240',NULL,NULL),(1619,'301606',213,'งิ้ว','30240',NULL,NULL),(1620,'301607',213,'กงรถ','30240',NULL,NULL),(1621,'301608',213,'หลุ่งประดู่','30240',NULL,NULL),(1622,'301609',213,'ตะโก','30240',NULL,NULL),(1623,'301610',213,'ห้วยแคน','30240',NULL,NULL),(1624,'301701',214,'ชุมพวง','30270',NULL,NULL),(1625,'301702',214,'ประสุข','30270',NULL,NULL),(1626,'301703',214,'ท่าลาด','30270',NULL,NULL),(1627,'301704',214,'สาหร่าย','30270',NULL,NULL),(1628,'301705',214,'ตลาดไทร','30270',NULL,NULL),(1629,'301710',214,'โนนรัง','30270',NULL,NULL),(1630,'301714',214,'หนองหลัก','30270',NULL,NULL),(1631,'301716',214,'โนนตูม','30270',NULL,NULL),(1632,'301717',214,'โนนยอ','30270',NULL,NULL),(1633,'301801',215,'สูงเนิน','30170',NULL,NULL),(1634,'301802',215,'เสมา','30170',NULL,NULL),(1635,'301803',215,'โคราช','30170',NULL,NULL),(1636,'301804',215,'บุ่งขี้เหล็ก','30170',NULL,NULL),(1637,'301805',215,'โนนค่า','30170',NULL,NULL),(1638,'301806',215,'โค้งยาง','30170',NULL,NULL),(1639,'301807',215,'มะเกลือเก่า','30170',NULL,NULL),(1640,'301808',215,'มะเกลือใหม่','30170',NULL,NULL),(1641,'301809',215,'นากลาง','30380',NULL,NULL),(1642,'301810',215,'หนองตะไก้','30380',NULL,NULL),(1643,'301901',216,'ขามทะเลสอ','30280',NULL,NULL),(1644,'301902',216,'โป่งแดง','30280',NULL,NULL),(1645,'301903',216,'พันดุง','30280',NULL,NULL),(1646,'301904',216,'หนองสรวง','30280',NULL,NULL),(1647,'301905',216,'บึงอ้อ','30280',NULL,NULL),(1648,'302001',217,'สีคิ้ว','30140',NULL,NULL),(1649,'302002',217,'บ้านหัน','30140',NULL,NULL),(1650,'302003',217,'กฤษณา','30140',NULL,NULL),(1651,'302004',217,'ลาดบัวขาว','30340',NULL,NULL),(1652,'302005',217,'หนองหญ้าขาว','30140',NULL,NULL),(1653,'302006',217,'กุดน้อย','30140',NULL,NULL),(1654,'302007',217,'หนองน้ำใส','30140',NULL,NULL),(1655,'302008',217,'วังโรงใหญ่','30140',NULL,NULL),(1656,'302009',217,'มิตรภาพ','30140',NULL,NULL),(1657,'302010',217,'คลองไผ่','30340',NULL,NULL),(1658,'302011',217,'ดอนเมือง','30140',NULL,NULL),(1659,'302012',217,'หนองบัวน้อย','30140',NULL,NULL),(1660,'302101',218,'ปากช่อง','30130',NULL,NULL),(1661,'302102',218,'กลางดง','30320',NULL,NULL),(1662,'302103',218,'จันทึก','30130',NULL,NULL),(1663,'302104',218,'วังกะทะ','30130',NULL,NULL),(1664,'302105',218,'หมูสี','30130',NULL,NULL),(1665,'302106',218,'หนองสาหร่าย','30130',NULL,NULL),(1666,'302107',218,'ขนงพระ','30130',NULL,NULL),(1667,'302108',218,'โป่งตาลอง','30130',NULL,NULL),(1668,'302109',218,'คลองม่วง','30130',NULL,NULL),(1669,'302110',218,'หนองน้ำแดง','30130',NULL,NULL),(1670,'302111',218,'วังไทร','30130',NULL,NULL),(1671,'302112',218,'พญาเย็น','30320',NULL,NULL),(1672,'302201',219,'หนองบุนนาก','30410',NULL,NULL),(1673,'302202',219,'สารภี','30410',NULL,NULL),(1674,'302203',219,'ไทยเจริญ','30410',NULL,NULL),(1675,'302204',219,'หนองหัวแรต','30410',NULL,NULL),(1676,'302205',219,'แหลมทอง','30410',NULL,NULL),(1677,'302206',219,'หนองตะไก้','30410',NULL,NULL),(1678,'302207',219,'ลุงเขว้า','30410',NULL,NULL),(1679,'302208',219,'หนองไม้ไผ่','30410',NULL,NULL),(1680,'302209',219,'บ้านใหม่','30410',NULL,NULL),(1681,'302301',220,'แก้งสนามนาง','30440',NULL,NULL),(1682,'302302',220,'โนนสำราญ','30440',NULL,NULL),(1683,'302303',220,'บึงพะไล','30440',NULL,NULL),(1684,'302304',220,'สีสุก','30440',NULL,NULL),(1685,'302305',220,'บึงสำโรง','30440',NULL,NULL),(1686,'302402',221,'โนนตาเถร','30360',NULL,NULL),(1687,'302403',221,'สำพะเนียง','30360',NULL,NULL),(1688,'302404',221,'วังหิน','30360',NULL,NULL),(1689,'302405',221,'ดอนยาวใหญ่','30360',NULL,NULL),(1690,'302501',222,'วังน้ำเขียว','30370',NULL,NULL),(1691,'302502',222,'วังหมี','30370',NULL,NULL),(1692,'302503',222,'ระเริง','30150',NULL,NULL),(1693,'302504',222,'อุดมทรัพย์','30370',NULL,NULL),(1694,'302505',222,'ไทยสามัคคี','30370',NULL,NULL),(1695,'302601',223,'สำนักตะคร้อ','30210',NULL,NULL),(1696,'302602',223,'หนองแวง','30210',NULL,NULL),(1697,'302603',223,'บึงปรือ','30210',NULL,NULL),(1698,'302604',223,'วังยายทอง','30210',NULL,NULL),(1699,'302701',224,'เมืองยาง','30270',NULL,NULL),(1700,'302702',224,'กระเบื้องนอก','30270',NULL,NULL),(1701,'302703',224,'ละหานปลาค้าว','30270',NULL,NULL),(1702,'302704',224,'โนนอุดม','30270',NULL,NULL),(1703,'302801',225,'สระพระ','30220',NULL,NULL),(1704,'302802',225,'มาบกราด','30220',NULL,NULL),(1705,'302803',225,'พังเทียม','30220',NULL,NULL),(1706,'302804',225,'ทัพรั้ง','30220',NULL,NULL),(1707,'302805',225,'หนองหอย','30220',NULL,NULL),(1708,'302901',226,'ขุย','30270',NULL,NULL),(1709,'302902',226,'บ้านยาง','30270',NULL,NULL),(1710,'302903',226,'ช่องแมว','30270',NULL,NULL),(1711,'302904',226,'ไพล','30270',NULL,NULL),(1712,'303001',227,'เมืองพะไล','30120',NULL,NULL),(1713,'303002',227,'โนนจาน','30120',NULL,NULL),(1714,'303003',227,'บัวลาย','30120',NULL,NULL),(1715,'303004',227,'หนองหว้า','30120',NULL,NULL),(1716,'303101',228,'สีดา','30430',NULL,NULL),(1717,'303102',228,'โพนทอง','30430',NULL,NULL),(1718,'303103',228,'โนนประดู่','30430',NULL,NULL),(1719,'303104',228,'สามเมือง','30430',NULL,NULL),(1720,'303105',228,'หนองตาดใหญ่','30430',NULL,NULL),(1721,'303201',229,'ช้างทอง','30230',NULL,NULL),(1722,'303202',229,'ท่าช้าง','30230',NULL,NULL),(1723,'303203',229,'พระพุทธ','30230',NULL,NULL),(1724,'303204',229,'หนองงูเหลือม','30000',NULL,NULL),(1725,'303205',229,'หนองยาง','30230',NULL,NULL),(1726,'310101',230,'ในเมือง','31000',NULL,NULL),(1727,'310102',230,'อิสาณ','31000',NULL,NULL),(1728,'310103',230,'เสม็ด','31000',NULL,NULL),(1729,'310104',230,'บ้านบัว','31000',NULL,NULL),(1730,'310105',230,'สะแกโพรง','31000',NULL,NULL),(1731,'310106',230,'สวายจึก','31000',NULL,NULL),(1732,'310108',230,'บ้านยาง','31000',NULL,NULL),(1733,'310112',230,'พระครู','31000',NULL,NULL),(1734,'310113',230,'ถลุงเหล็ก','31000',NULL,NULL),(1735,'310114',230,'หนองตาด','31000',NULL,NULL),(1736,'310117',230,'ลุมปุ๊ก','31000',NULL,NULL),(1737,'310118',230,'สองห้อง','31000',NULL,NULL),(1738,'310119',230,'บัวทอง','31000',NULL,NULL),(1739,'310120',230,'ชุมเห็ด','31000',NULL,NULL),(1740,'310122',230,'หลักเขต','31000',NULL,NULL),(1741,'310125',230,'สะแกซำ','31000',NULL,NULL),(1742,'310126',230,'กลันทา','31000',NULL,NULL),(1743,'310127',230,'กระสัง','31000',NULL,NULL),(1744,'310128',230,'เมืองฝาง','31000',NULL,NULL),(1745,'310201',231,'คูเมือง','31190',NULL,NULL),(1746,'310202',231,'ปะเคียบ','31190',NULL,NULL),(1747,'310203',231,'บ้านแพ','31190',NULL,NULL),(1748,'310204',231,'พรสำราญ','31190',NULL,NULL),(1749,'310205',231,'หินเหล็กไฟ','31190',NULL,NULL),(1750,'310206',231,'ตูมใหญ่','31190',NULL,NULL),(1751,'310207',231,'หนองขมาร','31190',NULL,NULL),(1752,'310301',232,'กระสัง','31160',NULL,NULL),(1753,'310302',232,'ลำดวน','31160',NULL,NULL),(1754,'310303',232,'สองชั้น','31160',NULL,NULL),(1755,'310304',232,'สูงเนิน','31160',NULL,NULL),(1756,'310305',232,'หนองเต็ง','31160',NULL,NULL),(1757,'310306',232,'เมืองไผ่','31160',NULL,NULL),(1758,'310307',232,'ชุมแสง','31160',NULL,NULL),(1759,'310308',232,'บ้านปรือ','31160',NULL,NULL),(1760,'310309',232,'ห้วยสำราญ','31160',NULL,NULL),(1761,'310310',232,'กันทรารมย์','31160',NULL,NULL),(1762,'310311',232,'ศรีภูมิ','31160',NULL,NULL),(1763,'310401',233,'นางรอง','31110',NULL,NULL),(1764,'310403',233,'สะเดา','31110',NULL,NULL),(1765,'310405',233,'ชุมแสง','31110',NULL,NULL),(1766,'310406',233,'หนองโบสถ์','31110',NULL,NULL),(1767,'310408',233,'หนองกง','31110',NULL,NULL),(1768,'310413',233,'ถนนหัก','31110',NULL,NULL),(1769,'310414',233,'หนองไทร','31110',NULL,NULL),(1770,'310415',233,'ก้านเหลือง','31110',NULL,NULL),(1771,'310416',233,'บ้านสิงห์','31110',NULL,NULL),(1772,'310417',233,'ลำไทรโยง','31110',NULL,NULL),(1773,'310418',233,'ทรัพย์พระยา','31110',NULL,NULL),(1774,'310424',233,'หนองยายพิมพ์','31110',NULL,NULL),(1775,'310425',233,'หัวถนน','31110',NULL,NULL),(1776,'310426',233,'ทุ่งแสงทอง','31110',NULL,NULL),(1777,'310427',233,'หนองโสน','31110',NULL,NULL),(1778,'310501',234,'หนองกี่','31210',NULL,NULL),(1779,'310502',234,'เย้ยปราสาท','31210',NULL,NULL),(1780,'310503',234,'เมืองไผ่','31210',NULL,NULL),(1781,'310504',234,'ดอนอะราง','31210',NULL,NULL),(1782,'310505',234,'โคกสว่าง','31210',NULL,NULL),(1783,'310506',234,'ทุ่งกระตาดพัฒนา','31210',NULL,NULL),(1784,'310507',234,'ทุ่งกระเต็น','31210',NULL,NULL),(1785,'310508',234,'ท่าโพธิ์ชัย','31210',NULL,NULL),(1786,'310509',234,'โคกสูง','31210',NULL,NULL),(1787,'310510',234,'บุกระสัง','31210',NULL,NULL),(1788,'310601',235,'ละหานทราย','31170',NULL,NULL),(1789,'310603',235,'ตาจง','31170',NULL,NULL),(1790,'310604',235,'สำโรงใหม่','31170',NULL,NULL),(1791,'310607',235,'หนองแวง','31170',NULL,NULL),(1792,'310610',235,'หนองตระครอง','31170',NULL,NULL),(1793,'310611',235,'โคกว่าน','31170',NULL,NULL),(1794,'310701',236,'ประโคนชัย','31140',NULL,NULL),(1795,'310702',236,'แสลงโทน','31140',NULL,NULL),(1796,'310703',236,'บ้านไทร','31140',NULL,NULL),(1797,'310705',236,'ละเวี้ย','31140',NULL,NULL),(1798,'310706',236,'จรเข้มาก','31140',NULL,NULL),(1799,'310707',236,'ปังกู','31140',NULL,NULL),(1800,'310708',236,'โคกย่าง','31140',NULL,NULL),(1801,'310710',236,'โคกม้า','31140',NULL,NULL),(1802,'310713',236,'ไพศาล','31140',NULL,NULL),(1803,'310714',236,'ตะโกตาพิ','31140',NULL,NULL),(1804,'310715',236,'เขาคอก','31140',NULL,NULL),(1805,'310716',236,'หนองบอน','31140',NULL,NULL),(1806,'310718',236,'โคกมะขาม','31140',NULL,NULL),(1807,'310719',236,'โคกตูม','31140',NULL,NULL),(1808,'310720',236,'ประทัดบุ','31140',NULL,NULL),(1809,'310721',236,'สี่เหลี่ยม','31140',NULL,NULL),(1810,'310801',237,'บ้านกรวด','31180',NULL,NULL),(1811,'310802',237,'โนนเจริญ','31180',NULL,NULL),(1812,'310803',237,'หนองไม้งาม','31180',NULL,NULL),(1813,'310804',237,'ปราสาท','31180',NULL,NULL),(1814,'310805',237,'สายตะกู','31180',NULL,NULL),(1815,'310806',237,'หินลาด','31180',NULL,NULL),(1816,'310807',237,'บึงเจริญ','31180',NULL,NULL),(1817,'310808',237,'จันทบเพชร','31180',NULL,NULL),(1818,'310809',237,'เขาดินเหนือ','31180',NULL,NULL),(1819,'310901',238,'พุทไธสง','31120',NULL,NULL),(1820,'310902',238,'มะเฟือง','31120',NULL,NULL),(1821,'310903',238,'บ้านจาน','31120',NULL,NULL),(1822,'310906',238,'บ้านเป้า','31120',NULL,NULL),(1823,'310907',238,'บ้านแวง','31120',NULL,NULL),(1824,'310909',238,'บ้านยาง','31120',NULL,NULL),(1825,'310910',238,'หายโศก','31120',NULL,NULL),(1826,'311001',239,'ลำปลายมาศ','31130',NULL,NULL),(1827,'311002',239,'หนองคู','31130',NULL,NULL),(1828,'311003',239,'แสลงพัน','31130',NULL,NULL),(1829,'311004',239,'ทะเมนชัย','31130',NULL,NULL),(1830,'311005',239,'ตลาดโพธิ์','31130',NULL,NULL),(1831,'311006',239,'หนองกระทิง','31130',NULL,NULL),(1832,'311007',239,'โคกกลาง','31130',NULL,NULL),(1833,'311008',239,'โคกสะอาด','31130',NULL,NULL),(1834,'311009',239,'เมืองแฝก','31130',NULL,NULL),(1835,'311010',239,'บ้านยาง','31130',NULL,NULL),(1836,'311011',239,'ผไทรินทร์','31130',NULL,NULL),(1837,'311012',239,'โคกล่าม','31130',NULL,NULL),(1838,'311013',239,'หินโคน','31130',NULL,NULL),(1839,'311014',239,'หนองบัวโคก','31130',NULL,NULL),(1840,'311015',239,'บุโพธิ์','31130',NULL,NULL),(1841,'311016',239,'หนองโดน','31130',NULL,NULL),(1842,'311101',240,'สตึก','31150',NULL,NULL),(1843,'311102',240,'นิคม','31150',NULL,NULL),(1844,'311103',240,'ทุ่งวัง','31150',NULL,NULL),(1845,'311104',240,'เมืองแก','31150',NULL,NULL),(1846,'311105',240,'หนองใหญ่','31150',NULL,NULL),(1847,'311106',240,'ร่อนทอง','31150',NULL,NULL),(1848,'311109',240,'ดอนมนต์','31150',NULL,NULL),(1849,'311110',240,'ชุมแสง','31150',NULL,NULL),(1850,'311111',240,'ท่าม่วง','31150',NULL,NULL),(1851,'311112',240,'สะแก','31150',NULL,NULL),(1852,'311114',240,'สนามชัย','31150',NULL,NULL),(1853,'311115',240,'กระสัง','31150',NULL,NULL),(1854,'311201',241,'ปะคำ','31220',NULL,NULL),(1855,'311202',241,'ไทยเจริญ','31220',NULL,NULL),(1856,'311203',241,'หนองบัว','31220',NULL,NULL),(1857,'311204',241,'โคกมะม่วง','31220',NULL,NULL),(1858,'311205',241,'หูทำนบ','31220',NULL,NULL),(1859,'311301',242,'นาโพธิ์','31230',NULL,NULL),(1860,'311302',242,'บ้านคู','31230',NULL,NULL),(1861,'311303',242,'บ้านดู่','31230',NULL,NULL),(1862,'311304',242,'ดอนกอก','31230',NULL,NULL),(1863,'311305',242,'ศรีสว่าง','31230',NULL,NULL),(1864,'311401',243,'สระแก้ว','31240',NULL,NULL),(1865,'311402',243,'ห้วยหิน','31240',NULL,NULL),(1866,'311403',243,'ไทยสามัคคี','31240',NULL,NULL),(1867,'311404',243,'หนองชัยศรี','31240',NULL,NULL),(1868,'311405',243,'เสาเดียว','31240',NULL,NULL),(1869,'311406',243,'เมืองฝ้าย','31240',NULL,NULL),(1870,'311407',243,'สระทอง','31240',NULL,NULL),(1871,'311501',244,'จันดุม','31250',NULL,NULL),(1872,'311502',244,'โคกขมิ้น','31250',NULL,NULL),(1873,'311503',244,'ป่าชัน','31250',NULL,NULL),(1874,'311504',244,'สะเดา','31250',NULL,NULL),(1875,'311505',244,'สำโรง','31250',NULL,NULL),(1876,'311601',245,'ห้วยราช','31000',NULL,NULL),(1877,'311602',245,'สามแวง','31000',NULL,NULL),(1878,'311603',245,'ตาเสา','31000',NULL,NULL),(1879,'311604',245,'บ้านตะโก','31000',NULL,NULL),(1880,'311605',245,'สนวน','31000',NULL,NULL),(1881,'311606',245,'โคกเหล็ก','31000',NULL,NULL),(1882,'311607',245,'เมืองโพธิ์','31000',NULL,NULL),(1883,'311608',245,'ห้วยราชา','31000',NULL,NULL),(1884,'311701',246,'โนนสุวรรณ','31110',NULL,NULL),(1885,'311702',246,'ทุ่งจังหัน','31110',NULL,NULL),(1886,'311703',246,'โกรกแก้ว','31110',NULL,NULL),(1887,'311704',246,'ดงอีจาน','31110',NULL,NULL),(1888,'311801',247,'ชำนิ','31110',NULL,NULL),(1889,'311802',247,'หนองปล่อง','31110',NULL,NULL),(1890,'311803',247,'เมืองยาง','31110',NULL,NULL),(1891,'311804',247,'ช่อผกา','31110',NULL,NULL),(1892,'311805',247,'ละลวด','31110',NULL,NULL),(1893,'311806',247,'โคกสนวน','31110',NULL,NULL),(1894,'311901',248,'หนองแวง','31120',NULL,NULL),(1895,'311902',248,'ทองหลาง','31120',NULL,NULL),(1896,'311903',248,'แดงใหญ่','31120',NULL,NULL),(1897,'311904',248,'กู่สวนแตง','31120',NULL,NULL),(1898,'311905',248,'หนองเยือง','31120',NULL,NULL),(1899,'312001',249,'โนนดินแดง','31260',NULL,NULL),(1900,'312002',249,'ส้มป่อย','31260',NULL,NULL),(1901,'312003',249,'ลำนางรอง','31260',NULL,NULL),(1902,'312101',250,'บ้านด่าน','31000',NULL,NULL),(1903,'312102',250,'ปราสาท','31000',NULL,NULL),(1904,'312103',250,'วังเหนือ','31000',NULL,NULL),(1905,'312104',250,'โนนขวาง','31000',NULL,NULL),(1906,'312201',251,'แคนดง','31150',NULL,NULL),(1907,'312202',251,'ดงพลอง','31150',NULL,NULL),(1908,'312203',251,'สระบัว','31150',NULL,NULL),(1909,'312204',251,'หัวฝาย','31150',NULL,NULL),(1910,'312301',252,'เจริญสุข','31110',NULL,NULL),(1911,'312302',252,'ตาเป๊ก','31110',NULL,NULL),(1912,'312303',252,'อีสานเขต','31110',NULL,NULL),(1913,'312304',252,'ถาวร','31170',NULL,NULL),(1914,'312305',252,'ยายแย้มวัฒนา','31170',NULL,NULL),(1915,'320101',253,'ในเมือง','32000',NULL,NULL),(1916,'320102',253,'ตั้งใจ','32000',NULL,NULL),(1917,'320103',253,'เพี้ยราม','32000',NULL,NULL),(1918,'320104',253,'นาดี','32000',NULL,NULL),(1919,'320105',253,'ท่าสว่าง','32000',NULL,NULL),(1920,'320106',253,'สลักได','32000',NULL,NULL),(1921,'320107',253,'ตาอ็อง','32000',NULL,NULL),(1922,'320109',253,'สำโรง','32000',NULL,NULL),(1923,'320110',253,'แกใหญ่','32000',NULL,NULL),(1924,'320111',253,'นอกเมือง','32000',NULL,NULL),(1925,'320112',253,'คอโค','32000',NULL,NULL),(1926,'320113',253,'สวาย','32000',NULL,NULL),(1927,'320114',253,'เฉนียง','32000',NULL,NULL),(1928,'320116',253,'เทนมีย์','32000',NULL,NULL),(1929,'320118',253,'นาบัว','32000',NULL,NULL),(1930,'320119',253,'เมืองที','32000',NULL,NULL),(1931,'320120',253,'ราม','32000',NULL,NULL),(1932,'320121',253,'บุฤาษี','32000',NULL,NULL),(1933,'320122',253,'ตระแสง','32000',NULL,NULL),(1934,'320125',253,'แสลงพันธ์','32000',NULL,NULL),(1935,'320126',253,'กาเกาะ','32000',NULL,NULL),(1936,'320201',254,'ชุมพลบุรี','32190',NULL,NULL),(1937,'320202',254,'นาหนองไผ่','32190',NULL,NULL),(1938,'320203',254,'ไพรขลา','32190',NULL,NULL),(1939,'320204',254,'ศรีณรงค์','32190',NULL,NULL),(1940,'320205',254,'ยะวึก','32190',NULL,NULL),(1941,'320206',254,'เมืองบัว','32190',NULL,NULL),(1942,'320207',254,'สระขุด','32190',NULL,NULL),(1943,'320208',254,'กระเบื้อง','32190',NULL,NULL),(1944,'320209',254,'หนองเรือ','32190',NULL,NULL),(1945,'320301',255,'ท่าตูม','32120',NULL,NULL),(1946,'320302',255,'กระโพ','32120',NULL,NULL),(1947,'320303',255,'พรมเทพ','32120',NULL,NULL),(1948,'320304',255,'โพนครก','32120',NULL,NULL),(1949,'320305',255,'เมืองแก','32120',NULL,NULL),(1950,'320306',255,'บะ','32120',NULL,NULL),(1951,'320307',255,'หนองบัว','32120',NULL,NULL),(1952,'320308',255,'บัวโคก','32120',NULL,NULL),(1953,'320309',255,'หนองเมธี','32120',NULL,NULL),(1954,'320310',255,'ทุ่งกุลา','32120',NULL,NULL),(1955,'320401',256,'จอมพระ','32180',NULL,NULL),(1956,'320402',256,'เมืองลีง','32180',NULL,NULL),(1957,'320403',256,'กระหาด','32180',NULL,NULL),(1958,'320404',256,'บุแกรง','32180',NULL,NULL),(1959,'320405',256,'หนองสนิท','32180',NULL,NULL),(1960,'320406',256,'บ้านผือ','32180',NULL,NULL),(1961,'320407',256,'ลุ่มระวี','32180',NULL,NULL),(1962,'320408',256,'ชุมแสง','32180',NULL,NULL),(1963,'320409',256,'เป็นสุข','32180',NULL,NULL),(1964,'320501',257,'กังแอน','32140',NULL,NULL),(1965,'320502',257,'ทมอ','32140',NULL,NULL),(1966,'320503',257,'ไพล','32140',NULL,NULL),(1967,'320504',257,'ปรือ','32140',NULL,NULL),(1968,'320505',257,'ทุ่งมน','32140',NULL,NULL),(1969,'320506',257,'ตาเบา','32140',NULL,NULL),(1970,'320507',257,'หนองใหญ่','32140',NULL,NULL),(1971,'320508',257,'โคกยาง','32140',NULL,NULL),(1972,'320509',257,'โคกสะอาด','32140',NULL,NULL),(1973,'320510',257,'บ้านไทร','32140',NULL,NULL),(1974,'320511',257,'โชคนาสาม','32140',NULL,NULL),(1975,'320512',257,'เชื้อเพลิง','32140',NULL,NULL),(1976,'320513',257,'ปราสาททนง','32140',NULL,NULL),(1977,'320514',257,'ตานี','32140',NULL,NULL),(1978,'320515',257,'บ้านพลวง','32140',NULL,NULL),(1979,'320516',257,'กันตรวจระมวล','32140',NULL,NULL),(1980,'320517',257,'สมุด','32140',NULL,NULL),(1981,'320518',257,'ประทัดบุ','32140',NULL,NULL),(1982,'320601',258,'กาบเชิง','32210',NULL,NULL),(1983,'320604',258,'คูตัน','32210',NULL,NULL),(1984,'320605',258,'ด่าน','32210',NULL,NULL),(1985,'320606',258,'แนงมุด','32210',NULL,NULL),(1986,'320607',258,'โคกตะเคียน','32210',NULL,NULL),(1987,'320610',258,'ตะเคียน','32210',NULL,NULL),(1988,'320701',259,'รัตนบุรี','32130',NULL,NULL),(1989,'320702',259,'ธาตุ','32130',NULL,NULL),(1990,'320703',259,'แก','32130',NULL,NULL),(1991,'320704',259,'ดอนแรด','32130',NULL,NULL),(1992,'320705',259,'หนองบัวทอง','32130',NULL,NULL),(1993,'320706',259,'หนองบัวบาน','32130',NULL,NULL),(1994,'320709',259,'ไผ่','32130',NULL,NULL),(1995,'320711',259,'เบิด','32130',NULL,NULL),(1996,'320713',259,'น้ำเขียว','32130',NULL,NULL),(1997,'320714',259,'กุดขาคีม','32130',NULL,NULL),(1998,'320715',259,'ยางสว่าง','32130',NULL,NULL),(1999,'320716',259,'ทับใหญ่','32130',NULL,NULL),(2000,'320801',260,'สนม','32160',NULL,NULL),(2001,'320802',260,'โพนโก','32160',NULL,NULL),(2002,'320803',260,'หนองระฆัง','32160',NULL,NULL),(2003,'320804',260,'นานวน','32160',NULL,NULL),(2004,'320805',260,'แคน','32160',NULL,NULL),(2005,'320806',260,'หัวงัว','32160',NULL,NULL),(2006,'320807',260,'หนองอียอ','32160',NULL,NULL),(2007,'320901',261,'ระแงง','32110',NULL,NULL),(2008,'320902',261,'ตรึม','32110',NULL,NULL),(2009,'320903',261,'จารพัต','32110',NULL,NULL),(2010,'320904',261,'ยาง','32110',NULL,NULL),(2011,'320905',261,'แตล','32110',NULL,NULL),(2012,'320906',261,'หนองบัว','32110',NULL,NULL),(2013,'320907',261,'คาละแมะ','32110',NULL,NULL),(2014,'320908',261,'หนองเหล็ก','32110',NULL,NULL),(2015,'320909',261,'หนองขวาว','32110',NULL,NULL),(2016,'320910',261,'ช่างปี่','32110',NULL,NULL),(2017,'320911',261,'กุดหวาย','32110',NULL,NULL),(2018,'320912',261,'ขวาวใหญ่','32110',NULL,NULL),(2019,'320913',261,'นารุ่ง','32110',NULL,NULL),(2020,'320914',261,'ตรมไพร','32110',NULL,NULL),(2021,'320915',261,'ผักไหม','32110',NULL,NULL),(2022,'321001',262,'สังขะ','32150',NULL,NULL),(2023,'321002',262,'ขอนแตก','32150',NULL,NULL),(2024,'321006',262,'ดม','32150',NULL,NULL),(2025,'321007',262,'พระแก้ว','32150',NULL,NULL),(2026,'321008',262,'บ้านจารย์','32150',NULL,NULL),(2027,'321009',262,'กระเทียม','32150',NULL,NULL),(2028,'321010',262,'สะกาด','32150',NULL,NULL),(2029,'321011',262,'ตาตุม','32150',NULL,NULL),(2030,'321012',262,'ทับทัน','32150',NULL,NULL),(2031,'321013',262,'ตาคง','32150',NULL,NULL),(2032,'321015',262,'บ้านชบ','32150',NULL,NULL),(2033,'321017',262,'เทพรักษา','32150',NULL,NULL),(2034,'321101',263,'ลำดวน','32220',NULL,NULL),(2035,'321102',263,'โชคเหนือ','32220',NULL,NULL),(2036,'321103',263,'อู่โลก','32220',NULL,NULL),(2037,'321104',263,'ตรำดม','32220',NULL,NULL),(2038,'321105',263,'ตระเปียงเตีย','32220',NULL,NULL),(2039,'321201',264,'สำโรงทาบ','32170',NULL,NULL),(2040,'321202',264,'หนองไผ่ล้อม','32170',NULL,NULL),(2041,'321203',264,'กระออม','32170',NULL,NULL),(2042,'321204',264,'หนองฮะ','32170',NULL,NULL),(2043,'321205',264,'ศรีสุข','32170',NULL,NULL),(2044,'321206',264,'เกาะแก้ว','32170',NULL,NULL),(2045,'321207',264,'หมื่นศรี','32170',NULL,NULL),(2046,'321208',264,'เสม็จ','32170',NULL,NULL),(2047,'321209',264,'สะโน','32170',NULL,NULL),(2048,'321210',264,'ประดู่','32170',NULL,NULL),(2049,'321301',265,'บัวเชด','32230',NULL,NULL),(2050,'321302',265,'สะเดา','32230',NULL,NULL),(2051,'321303',265,'จรัส','32230',NULL,NULL),(2052,'321304',265,'ตาวัง','32230',NULL,NULL),(2053,'321305',265,'อาโพน','32230',NULL,NULL),(2054,'321306',265,'สำเภาลูน','32230',NULL,NULL),(2055,'321401',266,'บักได','32140',NULL,NULL),(2056,'321402',266,'โคกกลาง','32140',NULL,NULL),(2057,'321403',266,'จีกแดก','32140',NULL,NULL),(2058,'321404',266,'ตาเมียง','32140',NULL,NULL),(2059,'321501',267,'ณรงค์','32150',NULL,NULL),(2060,'321502',267,'แจนแวน','32150',NULL,NULL),(2061,'321503',267,'ตรวจ','32150',NULL,NULL),(2062,'321504',267,'หนองแวง','32150',NULL,NULL),(2063,'321505',267,'ศรีสุข','32150',NULL,NULL),(2064,'321601',268,'เขวาสินรินทร์','32000',NULL,NULL),(2065,'321602',268,'บึง','32000',NULL,NULL),(2066,'321603',268,'ตากูก','32000',NULL,NULL),(2067,'321604',268,'ปราสาททอง','32000',NULL,NULL),(2068,'321605',268,'บ้านแร่','32000',NULL,NULL),(2069,'321701',269,'หนองหลวง','32130',NULL,NULL),(2070,'321702',269,'คำผง','32130',NULL,NULL),(2071,'321703',269,'โนน','32130',NULL,NULL),(2072,'321704',269,'ระเวียง','32130',NULL,NULL),(2073,'321705',269,'หนองเทพ','32130',NULL,NULL),(2074,'330101',270,'เมืองเหนือ','33000',NULL,NULL),(2075,'330103',270,'คูซอด','33000',NULL,NULL),(2076,'330104',270,'ซำ','33000',NULL,NULL),(2077,'330105',270,'จาน','33000',NULL,NULL),(2078,'330106',270,'ตะดอบ','33000',NULL,NULL),(2079,'330107',270,'หนองครก','33000',NULL,NULL),(2080,'330111',270,'โพนข่า','33000',NULL,NULL),(2081,'330112',270,'โพนค้อ','33000',NULL,NULL),(2082,'330116',270,'หญ้าปล้อง','33000',NULL,NULL),(2083,'330118',270,'ทุ่ม','33000',NULL,NULL),(2084,'330119',270,'หนองไฮ','33000',NULL,NULL),(2085,'330121',270,'หนองแก้ว','33000',NULL,NULL),(2086,'330122',270,'น้ำคำ','33000',NULL,NULL),(2087,'330123',270,'โพธิ์','33000',NULL,NULL),(2088,'330124',270,'หมากเขียบ','33000',NULL,NULL),(2089,'330127',270,'หนองไผ่','33000',NULL,NULL),(2090,'330201',271,'ยางชุมน้อย','33190',NULL,NULL),(2091,'330202',271,'ลิ้นฟ้า','33190',NULL,NULL),(2092,'330203',271,'คอนกาม','33190',NULL,NULL),(2093,'330204',271,'โนนคูณ','33190',NULL,NULL),(2094,'330205',271,'กุดเมืองฮาม','33190',NULL,NULL),(2095,'330206',271,'บึงบอน','33190',NULL,NULL),(2096,'330207',271,'ยางชุมใหญ่','33190',NULL,NULL),(2097,'330301',272,'ดูน','33130',NULL,NULL),(2098,'330302',272,'โนนสัง','33130',NULL,NULL),(2099,'330303',272,'หนองหัวช้าง','33130',NULL,NULL),(2100,'330304',272,'ยาง','33130',NULL,NULL),(2101,'330305',272,'หนองแวง','33130',NULL,NULL),(2102,'330306',272,'หนองแก้ว','33130',NULL,NULL),(2103,'330307',272,'ทาม','33130',NULL,NULL),(2104,'330308',272,'ละทาย','33130',NULL,NULL),(2105,'330309',272,'เมืองน้อย','33130',NULL,NULL),(2106,'330310',272,'อีปาด','33130',NULL,NULL),(2107,'330311',272,'บัวน้อย','33130',NULL,NULL),(2108,'330312',272,'หนองบัว','33130',NULL,NULL),(2109,'330313',272,'ดู่','33130',NULL,NULL),(2110,'330314',272,'ผักแพว','33130',NULL,NULL),(2111,'330315',272,'จาน','33130',NULL,NULL),(2112,'330320',272,'คำเนียม','33130',NULL,NULL),(2113,'330401',273,'บึงมะลู','33110',NULL,NULL),(2114,'330402',273,'กุดเสลา','33110',NULL,NULL),(2115,'330403',273,'เมือง','33110',NULL,NULL),(2116,'330405',273,'สังเม็ก','33110',NULL,NULL),(2117,'330406',273,'น้ำอ้อม','33110',NULL,NULL),(2118,'330407',273,'ละลาย','33110',NULL,NULL),(2119,'330408',273,'รุง','33110',NULL,NULL),(2120,'330409',273,'ตระกาจ','33110',NULL,NULL),(2121,'330411',273,'จานใหญ่','33110',NULL,NULL),(2122,'330412',273,'ภูเงิน','33110',NULL,NULL),(2123,'330413',273,'ชำ','33110',NULL,NULL),(2124,'330414',273,'กระแชง','33110',NULL,NULL),(2125,'330415',273,'โนนสำราญ','33110',NULL,NULL),(2126,'330416',273,'หนองหญ้าลาด','33110',NULL,NULL),(2127,'330419',273,'เสาธงชัย','33110',NULL,NULL),(2128,'330420',273,'ขนุน','33110',NULL,NULL),(2129,'330421',273,'สวนกล้วย','33110',NULL,NULL),(2130,'330423',273,'เวียงเหนือ','33110',NULL,NULL),(2131,'330424',273,'ทุ่งใหญ่','33110',NULL,NULL),(2132,'330425',273,'ภูผาหมอก','33110',NULL,NULL),(2133,'330501',274,'กันทรารมย์','33140',NULL,NULL),(2134,'330502',274,'จะกง','33140',NULL,NULL),(2135,'330503',274,'ใจดี','33140',NULL,NULL),(2136,'330504',274,'ดองกำเม็ด','33140',NULL,NULL),(2137,'330505',274,'โสน','33140',NULL,NULL),(2138,'330506',274,'ปรือใหญ่','33140',NULL,NULL),(2139,'330507',274,'สะเดาใหญ่','33140',NULL,NULL),(2140,'330508',274,'ตาอุด','33140',NULL,NULL),(2141,'330509',274,'ห้วยเหนือ','33140',NULL,NULL),(2142,'330510',274,'ห้วยใต้','33140',NULL,NULL),(2143,'330511',274,'หัวเสือ','33140',NULL,NULL),(2144,'330513',274,'ตะเคียน','33140',NULL,NULL),(2145,'330515',274,'นิคมพัฒนา','33140',NULL,NULL),(2146,'330517',274,'โคกเพชร','33140',NULL,NULL),(2147,'330518',274,'ปราสาท','33140',NULL,NULL),(2148,'330521',274,'สำโรงตาเจ็น','33140',NULL,NULL),(2149,'330522',274,'ห้วยสำราญ','33140',NULL,NULL),(2150,'330524',274,'กฤษณา','33140',NULL,NULL),(2151,'330525',274,'ลมศักดิ์','33140',NULL,NULL),(2152,'330526',274,'หนองฉลอง','33140',NULL,NULL),(2153,'330527',274,'ศรีตระกูล','33140',NULL,NULL),(2154,'330528',274,'ศรีสะอาด','33140',NULL,NULL),(2155,'330601',275,'ไพรบึง','33180',NULL,NULL),(2156,'330602',275,'ดินแดง','33180',NULL,NULL),(2157,'330603',275,'ปราสาทเยอ','33180',NULL,NULL),(2158,'330604',275,'สำโรงพลัน','33180',NULL,NULL),(2159,'330605',275,'สุขสวัสดิ์','33180',NULL,NULL),(2160,'330606',275,'โนนปูน','33180',NULL,NULL),(2161,'330701',276,'พิมาย','33170',NULL,NULL),(2162,'330702',276,'กู่','33170',NULL,NULL),(2163,'330703',276,'หนองเชียงทูน','33170',NULL,NULL),(2164,'330704',276,'ตูม','33170',NULL,NULL),(2165,'330705',276,'สมอ','33170',NULL,NULL),(2166,'330706',276,'โพธิ์ศรี','33170',NULL,NULL),(2167,'330707',276,'สำโรงปราสาท','33170',NULL,NULL),(2168,'330708',276,'ดู่','33170',NULL,NULL),(2169,'330709',276,'สวาย','33170',NULL,NULL),(2170,'330710',276,'พิมายเหนือ','33170',NULL,NULL),(2171,'330801',277,'สิ','33150',NULL,NULL),(2172,'330802',277,'บักดอง','33150',NULL,NULL),(2173,'330803',277,'พราน','33150',NULL,NULL),(2174,'330804',277,'โพธิ์วงศ์','33150',NULL,NULL),(2175,'330805',277,'ไพร','33150',NULL,NULL),(2176,'330806',277,'กระหวัน','33150',NULL,NULL),(2177,'330807',277,'ขุนหาญ','33150',NULL,NULL),(2178,'330808',277,'โนนสูง','33150',NULL,NULL),(2179,'330809',277,'กันทรอม','33150',NULL,NULL),(2180,'330810',277,'ภูฝ้าย','33150',NULL,NULL),(2181,'330811',277,'โพธิ์กระสังข์','33150',NULL,NULL),(2182,'330812',277,'ห้วยจันทร์','33150',NULL,NULL),(2183,'330901',278,'เมืองคง','33160',NULL,NULL),(2184,'330902',278,'เมืองแคน','33160',NULL,NULL),(2185,'330903',278,'หนองแค','33160',NULL,NULL),(2186,'330906',278,'จิกสังข์ทอง','33160',NULL,NULL),(2187,'330907',278,'ด่าน','33160',NULL,NULL),(2188,'330908',278,'ดู่','33160',NULL,NULL),(2189,'330909',278,'หนองอึ่ง','33160',NULL,NULL),(2190,'330910',278,'บัวหุ่ง','33160',NULL,NULL),(2191,'330911',278,'ไผ่','33160',NULL,NULL),(2192,'330912',278,'ส้มป่อย','33160',NULL,NULL),(2193,'330913',278,'หนองหมี','33160',NULL,NULL),(2194,'330914',278,'หว้านคำ','33160',NULL,NULL),(2195,'330915',278,'สร้างปี่','33160',NULL,NULL),(2196,'331001',279,'กำแพง','33120',NULL,NULL),(2197,'331002',279,'อี่หล่ำ','33120',NULL,NULL),(2198,'331003',279,'ก้านเหลือง','33120',NULL,NULL),(2199,'331004',279,'ทุ่งไชย','33120',NULL,NULL),(2200,'331005',279,'สำโรง','33120',NULL,NULL),(2201,'331006',279,'แขม','33120',NULL,NULL),(2202,'331007',279,'หนองไฮ','33120',NULL,NULL),(2203,'331008',279,'ขะยูง','33120',NULL,NULL),(2204,'331010',279,'ตาเกษ','33120',NULL,NULL),(2205,'331011',279,'หัวช้าง','33120',NULL,NULL),(2206,'331012',279,'รังแร้ง','33120',NULL,NULL),(2207,'331014',279,'แต้','33120',NULL,NULL),(2208,'331015',279,'แข้','33120',NULL,NULL),(2209,'331016',279,'โพธิ์ชัย','33120',NULL,NULL),(2210,'331017',279,'ปะอาว','33120',NULL,NULL),(2211,'331018',279,'หนองห้าง','33120',NULL,NULL),(2212,'331022',279,'สระกำแพงใหญ่','33120',NULL,NULL),(2213,'331024',279,'โคกหล่าม','33120',NULL,NULL),(2214,'331025',279,'โคกจาน','33120',NULL,NULL),(2215,'331101',280,'เป๊าะ','33220',NULL,NULL),(2216,'331102',280,'บึงบูรพ์','33220',NULL,NULL),(2217,'331201',281,'ห้วยทับทัน','33210',NULL,NULL),(2218,'331202',281,'เมืองหลวง','33210',NULL,NULL),(2219,'331203',281,'กล้วยกว้าง','33210',NULL,NULL),(2220,'331204',281,'ผักไหม','33210',NULL,NULL),(2221,'331205',281,'จานแสนไชย','33210',NULL,NULL),(2222,'331206',281,'ปราสาท','33210',NULL,NULL),(2223,'331301',282,'โนนค้อ','33250',NULL,NULL),(2224,'331302',282,'บก','33250',NULL,NULL),(2225,'331303',282,'โพธิ์','33250',NULL,NULL),(2226,'331304',282,'หนองกุง','33250',NULL,NULL),(2227,'331305',282,'เหล่ากวาง','33250',NULL,NULL),(2228,'331401',283,'ศรีแก้ว','33240',NULL,NULL),(2229,'331402',283,'พิงพวย','33240',NULL,NULL),(2230,'331403',283,'สระเยาว์','33240',NULL,NULL),(2231,'331404',283,'ตูม','33240',NULL,NULL),(2232,'331405',283,'เสืองข้าว','33240',NULL,NULL),(2233,'331406',283,'ศรีโนนงาม','33240',NULL,NULL),(2234,'331407',283,'สะพุง','33240',NULL,NULL),(2235,'331501',284,'น้ำเกลี้ยง','33130',NULL,NULL),(2236,'331502',284,'ละเอาะ','33130',NULL,NULL),(2237,'331503',284,'ตองปิด','33130',NULL,NULL),(2238,'331504',284,'เขิน','33130',NULL,NULL),(2239,'331505',284,'รุ่งระวี','33130',NULL,NULL),(2240,'331506',284,'คูบ','33130',NULL,NULL),(2241,'331601',285,'บุสูง','33270',NULL,NULL),(2242,'331602',285,'ธาตุ','33270',NULL,NULL),(2243,'331603',285,'ดวนใหญ่','33270',NULL,NULL),(2244,'331604',285,'บ่อแก้ว','33270',NULL,NULL),(2245,'331605',285,'ศรีสำราญ','33270',NULL,NULL),(2246,'331606',285,'ทุ่งสว่าง','33270',NULL,NULL),(2247,'331607',285,'วังหิน','33270',NULL,NULL),(2248,'331608',285,'โพนยาง','33270',NULL,NULL),(2249,'331701',286,'โคกตาล','33140',NULL,NULL),(2250,'331702',286,'ห้วยตามอญ','33140',NULL,NULL),(2251,'331703',286,'ห้วยตึ๊กชู','33140',NULL,NULL),(2252,'331704',286,'ละลม','33140',NULL,NULL),(2253,'331705',286,'ตะเคียนราม','33140',NULL,NULL),(2254,'331706',286,'ดงรัก','33140',NULL,NULL),(2255,'331707',286,'ไพรพัฒนา','33140',NULL,NULL),(2256,'331801',287,'เมืองจันทร์','33120',NULL,NULL),(2257,'331802',287,'ตาโกน','33120',NULL,NULL),(2258,'331803',287,'หนองใหญ่','33120',NULL,NULL),(2259,'331901',288,'เสียว','33110',NULL,NULL),(2260,'331902',288,'หนองหว้า','33110',NULL,NULL),(2261,'331903',288,'หนองงูเหลือม','33110',NULL,NULL),(2262,'331904',288,'หนองฮาง','33110',NULL,NULL),(2263,'331905',288,'ท่าคล้อ','33110',NULL,NULL),(2264,'332001',289,'พยุห์','33230',NULL,NULL),(2265,'332002',289,'พรหมสวัสดิ์','33230',NULL,NULL),(2266,'332003',289,'ตำแย','33230',NULL,NULL),(2267,'332004',289,'โนนเพ็ก','33230',NULL,NULL),(2268,'332005',289,'หนองค้า','33230',NULL,NULL),(2269,'332101',290,'โดด','33120',NULL,NULL),(2270,'332102',290,'เสียว','33120',NULL,NULL),(2271,'332103',290,'หนองม้า','33120',NULL,NULL),(2272,'332104',290,'ผือใหญ่','33120',NULL,NULL),(2273,'332105',290,'อีเซ','33120',NULL,NULL),(2274,'332201',291,'กุง','33160',NULL,NULL),(2275,'332202',291,'คลีกลิ้ง','33160',NULL,NULL),(2276,'332203',291,'หนองบัวดง','33160',NULL,NULL),(2277,'332204',291,'โจดม่วง','33160',NULL,NULL),(2278,'340101',292,'ในเมือง','34000',NULL,NULL),(2279,'340104',292,'หัวเรือ','34000',NULL,NULL),(2280,'340105',292,'หนองขอน','34000',NULL,NULL),(2281,'340107',292,'ปทุม','34000',NULL,NULL),(2282,'340108',292,'ขามใหญ่','34000',NULL,NULL),(2283,'340109',292,'แจระแม','34000',NULL,NULL),(2284,'340111',292,'หนองบ่อ','34000',NULL,NULL),(2285,'340112',292,'ไร่น้อย','34000',NULL,NULL),(2286,'340113',292,'กระโสบ','34000',NULL,NULL),(2287,'340116',292,'กุดลาด','34000',NULL,NULL),(2288,'340119',292,'ขี้เหล็ก','34000',NULL,NULL),(2289,'340120',292,'ปะอาว','34000',NULL,NULL),(2290,'340201',293,'นาคำ','34250',NULL,NULL),(2291,'340202',293,'แก้งกอก','34250',NULL,NULL),(2292,'340203',293,'เอือดใหญ่','34250',NULL,NULL),(2293,'340204',293,'วาริน','34250',NULL,NULL),(2294,'340205',293,'ลาดควาย','34250',NULL,NULL),(2295,'340206',293,'สงยาง','34250',NULL,NULL),(2296,'340207',293,'ตะบ่าย','34250',NULL,NULL),(2297,'340208',293,'คำไหล','34250',NULL,NULL),(2298,'340209',293,'หนามแท่ง','34250',NULL,NULL),(2299,'340210',293,'นาเลิน','34250',NULL,NULL),(2300,'340211',293,'ดอนใหญ่','34250',NULL,NULL),(2301,'340301',294,'โขงเจียม','34220',NULL,NULL),(2302,'340302',294,'ห้วยยาง','34220',NULL,NULL),(2303,'340303',294,'นาโพธิ์กลาง','34220',NULL,NULL),(2304,'340304',294,'หนองแสงใหญ่','34220',NULL,NULL),(2305,'340305',294,'ห้วยไผ่','34220',NULL,NULL),(2306,'340401',295,'เขื่องใน','34150',NULL,NULL),(2307,'340402',295,'สร้างถ่อ','34150',NULL,NULL),(2308,'340403',295,'ค้อทอง','34150',NULL,NULL),(2309,'340404',295,'ก่อเอ้','34150',NULL,NULL),(2310,'340405',295,'หัวดอน','34150',NULL,NULL),(2311,'340406',295,'ชีทวน','34150',NULL,NULL),(2312,'340407',295,'ท่าไห','34150',NULL,NULL),(2313,'340408',295,'นาคำใหญ่','34150',NULL,NULL),(2314,'340409',295,'แดงหม้อ','34150',NULL,NULL),(2315,'340410',295,'ธาตุน้อย','34150',NULL,NULL),(2316,'340411',295,'บ้านไทย','34320',NULL,NULL),(2317,'340412',295,'บ้านกอก','34320',NULL,NULL),(2318,'340413',295,'กลางใหญ่','34320',NULL,NULL),(2319,'340414',295,'โนนรัง','34320',NULL,NULL),(2320,'340415',295,'ยางขี้นก','34150',NULL,NULL),(2321,'340416',295,'ศรีสุข','34150',NULL,NULL),(2322,'340417',295,'สหธาตุ','34150',NULL,NULL),(2323,'340418',295,'หนองเหล่า','34150',NULL,NULL),(2324,'340501',296,'เขมราฐ','34170',NULL,NULL),(2325,'340503',296,'ขามป้อม','34170',NULL,NULL),(2326,'340504',296,'เจียด','34170',NULL,NULL),(2327,'340507',296,'หนองผือ','34170',NULL,NULL),(2328,'340508',296,'นาแวง','34170',NULL,NULL),(2329,'340510',296,'แก้งเหนือ','34170',NULL,NULL),(2330,'340511',296,'หนองนกทา','34170',NULL,NULL),(2331,'340512',296,'หนองสิม','34170',NULL,NULL),(2332,'340513',296,'หัวนา','34170',NULL,NULL),(2333,'340701',297,'เมืองเดช','34160',NULL,NULL),(2334,'340702',297,'นาส่วง','34160',NULL,NULL),(2335,'340704',297,'นาเจริญ','34160',NULL,NULL),(2336,'340706',297,'ทุ่งเทิง','34160',NULL,NULL),(2337,'340708',297,'สมสะอาด','34160',NULL,NULL),(2338,'340709',297,'กุดประทาย','34160',NULL,NULL),(2339,'340710',297,'ตบหู','34160',NULL,NULL),(2340,'340711',297,'กลาง','34160',NULL,NULL),(2341,'340712',297,'แก้ง','34160',NULL,NULL),(2342,'340713',297,'ท่าโพธิ์ศรี','34160',NULL,NULL),(2343,'340715',297,'บัวงาม','34160',NULL,NULL),(2344,'340716',297,'คำครั่ง','34160',NULL,NULL),(2345,'340717',297,'นากระแซง','34160',NULL,NULL),(2346,'340720',297,'โพนงาม','34160',NULL,NULL),(2347,'340721',297,'ป่าโมง','34160',NULL,NULL),(2348,'340723',297,'โนนสมบูรณ์','34160',NULL,NULL),(2349,'340801',298,'นาจะหลวย','34280',NULL,NULL),(2350,'340802',298,'โนนสมบูรณ์','34280',NULL,NULL),(2351,'340803',298,'พรสวรรค์','34280',NULL,NULL),(2352,'340804',298,'บ้านตูม','34280',NULL,NULL),(2353,'340805',298,'โสกแสง','34280',NULL,NULL),(2354,'340806',298,'โนนสวรรค์','34280',NULL,NULL),(2355,'340901',299,'โซง','34260',NULL,NULL),(2356,'340903',299,'ยาง','34260',NULL,NULL),(2357,'340904',299,'โดมประดิษฐ์','34260',NULL,NULL),(2358,'340906',299,'บุเปือย','34260',NULL,NULL),(2359,'340907',299,'สีวิเชียร','34260',NULL,NULL),(2360,'340909',299,'ยางใหญ่','34260',NULL,NULL),(2361,'340911',299,'เก่าขาม','34260',NULL,NULL),(2362,'341001',300,'โพนงาม','34230',NULL,NULL),(2363,'341002',300,'ห้วยข่า','34230',NULL,NULL),(2364,'341003',300,'คอแลน','34230',NULL,NULL),(2365,'341004',300,'นาโพธิ์','34230',NULL,NULL),(2366,'341005',300,'หนองสะโน','34230',NULL,NULL),(2367,'341006',300,'โนนค้อ','34230',NULL,NULL),(2368,'341007',300,'บัวงาม','34230',NULL,NULL),(2369,'341008',300,'บ้านแมด','34230',NULL,NULL),(2370,'341101',301,'ขุหลุ','34130',NULL,NULL),(2371,'341102',301,'กระเดียน','34130',NULL,NULL),(2372,'341103',301,'เกษม','34130',NULL,NULL),(2373,'341104',301,'กุศกร','34130',NULL,NULL),(2374,'341105',301,'ขามเปี้ย','34130',NULL,NULL),(2375,'341106',301,'คอนสาย','34130',NULL,NULL),(2376,'341107',301,'โคกจาน','34130',NULL,NULL),(2377,'341108',301,'นาพิน','34130',NULL,NULL),(2378,'341109',301,'นาสะไม','34130',NULL,NULL),(2379,'341110',301,'โนนกุง','34130',NULL,NULL),(2380,'341111',301,'ตระการ','34130',NULL,NULL),(2381,'341112',301,'ตากแดด','34130',NULL,NULL),(2382,'341113',301,'ไหล่ทุ่ง','34130',NULL,NULL),(2383,'341114',301,'เป้า','34130',NULL,NULL),(2384,'341115',301,'เซเป็ด','34130',NULL,NULL),(2385,'341116',301,'สะพือ','34130',NULL,NULL),(2386,'341117',301,'หนองเต่า','34130',NULL,NULL),(2387,'341118',301,'ถ้ำแข้','34130',NULL,NULL),(2388,'341119',301,'ท่าหลวง','34130',NULL,NULL),(2389,'341120',301,'ห้วยฝ้ายพัฒนา','34130',NULL,NULL),(2390,'341121',301,'กุดยาลวน','34130',NULL,NULL),(2391,'341122',301,'บ้านแดง','34130',NULL,NULL),(2392,'341123',301,'คำเจริญ','34130',NULL,NULL),(2393,'341201',302,'ข้าวปุ้น','34270',NULL,NULL),(2394,'341202',302,'โนนสวาง','34270',NULL,NULL),(2395,'341203',302,'แก่งเค็ง','34270',NULL,NULL),(2396,'341204',302,'กาบิน','34270',NULL,NULL),(2397,'341205',302,'หนองทันน้ำ','34270',NULL,NULL),(2398,'341401',303,'ม่วงสามสิบ','34140',NULL,NULL),(2399,'341402',303,'เหล่าบก','34140',NULL,NULL),(2400,'341403',303,'ดุมใหญ่','34140',NULL,NULL),(2401,'341404',303,'หนองช้างใหญ่','34140',NULL,NULL),(2402,'341405',303,'หนองเมือง','34140',NULL,NULL),(2403,'341406',303,'เตย','34140',NULL,NULL),(2404,'341407',303,'ยางสักกระโพหลุ่ม','34140',NULL,NULL),(2405,'341408',303,'หนองไข่นก','34140',NULL,NULL),(2406,'341409',303,'หนองเหล่า','34140',NULL,NULL),(2407,'341410',303,'หนองฮาง','34140',NULL,NULL),(2408,'341411',303,'ยางโยภาพ','34140',NULL,NULL),(2409,'341412',303,'ไผ่ใหญ่','34140',NULL,NULL),(2410,'341413',303,'นาเลิง','34140',NULL,NULL),(2411,'341414',303,'โพนแพง','34140',NULL,NULL),(2412,'341501',304,'วารินชำราบ','34190',NULL,NULL),(2413,'341502',304,'ธาตุ','34190',NULL,NULL),(2414,'341504',304,'ท่าลาด','34310',NULL,NULL),(2415,'341505',304,'โนนโหนน','34190',NULL,NULL),(2416,'341507',304,'คูเมือง','34190',NULL,NULL),(2417,'341508',304,'สระสมิง','34190',NULL,NULL),(2418,'341510',304,'คำน้ำแซบ','34190',NULL,NULL),(2419,'341511',304,'บุ่งหวาย','34310',NULL,NULL),(2420,'341515',304,'คำขวาง','34190',NULL,NULL),(2421,'341516',304,'โพธิ์ใหญ่','34190',NULL,NULL),(2422,'341518',304,'แสนสุข','34190',NULL,NULL),(2423,'341520',304,'หนองกินเพล','34190',NULL,NULL),(2424,'341521',304,'โนนผึ้ง','34190',NULL,NULL),(2425,'341522',304,'เมืองศรีไค','34190',NULL,NULL),(2426,'341524',304,'ห้วยขะยุง','34310',NULL,NULL),(2427,'341526',304,'บุ่งไหม','34190',NULL,NULL),(2428,'341901',305,'พิบูล','34110',NULL,NULL),(2429,'341902',305,'กุดชมภู','34110',NULL,NULL),(2430,'341904',305,'ดอนจิก','34110',NULL,NULL),(2431,'341905',305,'ทรายมูล','34110',NULL,NULL),(2432,'341906',305,'นาโพธิ์','34110',NULL,NULL),(2433,'341907',305,'โนนกลาง','34110',NULL,NULL),(2434,'341909',305,'โพธิ์ไทร','34110',NULL,NULL),(2435,'341910',305,'โพธิ์ศรี','34110',NULL,NULL),(2436,'341911',305,'ระเว','34110',NULL,NULL),(2437,'341912',305,'ไร่ใต้','34110',NULL,NULL),(2438,'341913',305,'หนองบัวฮี','34110',NULL,NULL),(2439,'341914',305,'อ่างศิลา','34110',NULL,NULL),(2440,'341918',305,'โนนกาหลง','34110',NULL,NULL),(2441,'341919',305,'บ้านแขม','34110',NULL,NULL),(2442,'342001',306,'ตาลสุม','34330',NULL,NULL),(2443,'342002',306,'สำโรง','34330',NULL,NULL),(2444,'342003',306,'จิกเทิง','34330',NULL,NULL),(2445,'342004',306,'หนองกุง','34330',NULL,NULL),(2446,'342005',306,'นาคาย','34330',NULL,NULL),(2447,'342006',306,'คำหว้า','34330',NULL,NULL),(2448,'342101',307,'โพธิ์ไทร','34340',NULL,NULL),(2449,'342102',307,'ม่วงใหญ่','34340',NULL,NULL),(2450,'342103',307,'สำโรง','34340',NULL,NULL),(2451,'342104',307,'สองคอน','34340',NULL,NULL),(2452,'342105',307,'สารภี','34340',NULL,NULL),(2453,'342106',307,'เหล่างาม','34340',NULL,NULL),(2454,'342201',308,'สำโรง','34360',NULL,NULL),(2455,'342202',308,'โคกก่อง','34360',NULL,NULL),(2456,'342203',308,'หนองไฮ','34360',NULL,NULL),(2457,'342204',308,'ค้อน้อย','34360',NULL,NULL),(2458,'342205',308,'โนนกาเล็น','34360',NULL,NULL),(2459,'342206',308,'โคกสว่าง','34360',NULL,NULL),(2460,'342207',308,'โนนกลาง','34360',NULL,NULL),(2461,'342208',308,'บอน','34360',NULL,NULL),(2462,'342209',308,'ขามป้อม','34360',NULL,NULL),(2463,'342401',309,'ดอนมดแดง','34000',NULL,NULL),(2464,'342402',309,'เหล่าแดง','34000',NULL,NULL),(2465,'342403',309,'ท่าเมือง','34000',NULL,NULL),(2466,'342404',309,'คำไฮใหญ่','34000',NULL,NULL),(2467,'342501',310,'คันไร่','34350',NULL,NULL),(2468,'342502',310,'ช่องเม็ก','34350',NULL,NULL),(2469,'342503',310,'โนนก่อ','34350',NULL,NULL),(2470,'342504',310,'นิคมลำโดมน้อย','34350',NULL,NULL),(2471,'342505',310,'ฝางคำ','34350',NULL,NULL),(2472,'342506',310,'คำเขื่อนแก้ว','34350',NULL,NULL),(2473,'342602',311,'หนองอ้ม','34160',NULL,NULL),(2474,'342603',311,'นาเกษม','34160',NULL,NULL),(2475,'342604',311,'กุดเรือ','34160',NULL,NULL),(2476,'342605',311,'โคกชำแระ','34160',NULL,NULL),(2477,'342606',311,'นาห่อม','34160',NULL,NULL),(2478,'342901',312,'นาเยีย','34160',NULL,NULL),(2479,'342902',312,'นาดี','34160',NULL,NULL),(2480,'342903',312,'นาเรือง','34160',NULL,NULL),(2481,'343001',313,'นาตาล','34170',NULL,NULL),(2482,'343002',313,'พะลาน','34170',NULL,NULL),(2483,'343003',313,'กองโพน','34170',NULL,NULL),(2484,'343004',313,'พังเคน','34170',NULL,NULL),(2485,'343101',314,'เหล่าเสือโก้ก','34000',NULL,NULL),(2486,'343102',314,'โพนเมือง','34000',NULL,NULL),(2487,'343103',314,'แพงใหญ่','34000',NULL,NULL),(2488,'343104',314,'หนองบก','34000',NULL,NULL),(2489,'343201',315,'แก่งโดม','34190',NULL,NULL),(2490,'343202',315,'ท่าช้าง','34190',NULL,NULL),(2491,'343203',315,'บุ่งมะแลง','34190',NULL,NULL),(2492,'343204',315,'สว่าง','34190',NULL,NULL),(2493,'343301',316,'ตาเกา','34260',NULL,NULL),(2494,'343302',316,'ไพบูลย์','34260',NULL,NULL),(2495,'343303',316,'ขี้เหล็ก','34260',NULL,NULL),(2496,'343304',316,'โคกสะอาด','34260',NULL,NULL),(2497,'350101',317,'ในเมือง','35000',NULL,NULL),(2498,'350102',317,'น้ำคำใหญ่','35000',NULL,NULL),(2499,'350103',317,'ตาดทอง','35000',NULL,NULL),(2500,'350104',317,'สำราญ','35000',NULL,NULL),(2501,'350105',317,'ค้อเหนือ','35000',NULL,NULL),(2502,'350106',317,'ดู่ทุ่ง','35000',NULL,NULL),(2503,'350107',317,'เดิด','35000',NULL,NULL),(2504,'350108',317,'ขั้นไดใหญ่','35000',NULL,NULL),(2505,'350109',317,'ทุ่งแต้','35000',NULL,NULL),(2506,'350110',317,'สิงห์','35000',NULL,NULL),(2507,'350111',317,'นาสะไมย์','35000',NULL,NULL),(2508,'350112',317,'เขื่องคำ','35000',NULL,NULL),(2509,'350113',317,'หนองหิน','35000',NULL,NULL),(2510,'350114',317,'หนองคู','35000',NULL,NULL),(2511,'350115',317,'ขุมเงิน','35000',NULL,NULL),(2512,'350116',317,'ทุ่งนางโอก','35000',NULL,NULL),(2513,'350117',317,'หนองเรือ','35000',NULL,NULL),(2514,'350118',317,'หนองเป็ด','35000',NULL,NULL),(2515,'350201',318,'ทรายมูล','35170',NULL,NULL),(2516,'350202',318,'ดู่ลาด','35170',NULL,NULL),(2517,'350203',318,'ดงมะไฟ','35170',NULL,NULL),(2518,'350204',318,'นาเวียง','35170',NULL,NULL),(2519,'350205',318,'ไผ่','35170',NULL,NULL),(2520,'350301',319,'กุดชุม','35140',NULL,NULL),(2521,'350302',319,'โนนเปือย','35140',NULL,NULL),(2522,'350303',319,'กำแมด','35140',NULL,NULL),(2523,'350304',319,'นาโส่','35140',NULL,NULL),(2524,'350305',319,'ห้วยแก้ง','35140',NULL,NULL),(2525,'350306',319,'หนองหมี','35140',NULL,NULL),(2526,'350307',319,'โพนงาม','35140',NULL,NULL),(2527,'350308',319,'คำน้ำสร้าง','35140',NULL,NULL),(2528,'350309',319,'หนองแหน','35140',NULL,NULL),(2529,'350401',320,'ลุมพุก','35110',NULL,NULL),(2530,'350402',320,'ย่อ','35110',NULL,NULL),(2531,'350403',320,'สงเปือย','35110',NULL,NULL),(2532,'350404',320,'โพนทัน','35110',NULL,NULL),(2533,'350405',320,'ทุ่งมน','35110',NULL,NULL),(2534,'350406',320,'นาคำ','35180',NULL,NULL),(2535,'350407',320,'ดงแคนใหญ่','35180',NULL,NULL),(2536,'350408',320,'กู่จาน','35110',NULL,NULL),(2537,'350409',320,'นาแก','35180',NULL,NULL),(2538,'350410',320,'กุดกุง','35110',NULL,NULL),(2539,'350411',320,'เหล่าไฮ','35110',NULL,NULL),(2540,'350412',320,'แคนน้อย','35180',NULL,NULL),(2541,'350413',320,'ดงเจริญ','35110',NULL,NULL),(2542,'350501',321,'โพธิ์ไทร','35150',NULL,NULL),(2543,'350502',321,'กระจาย','35150',NULL,NULL),(2544,'350503',321,'โคกนาโก','35150',NULL,NULL),(2545,'350504',321,'เชียงเพ็ง','35150',NULL,NULL),(2546,'350505',321,'ศรีฐาน','35150',NULL,NULL),(2547,'350601',322,'ฟ้าหยาด','35130',NULL,NULL),(2548,'350602',322,'หัวเมือง','35130',NULL,NULL),(2549,'350603',322,'คูเมือง','35130',NULL,NULL),(2550,'350604',322,'ผือฮี','35130',NULL,NULL),(2551,'350605',322,'บากเรือ','35130',NULL,NULL),(2552,'350606',322,'ม่วง','35130',NULL,NULL),(2553,'350607',322,'โนนทราย','35130',NULL,NULL),(2554,'350608',322,'บึงแก','35130',NULL,NULL),(2555,'350609',322,'พระเสาร์','35130',NULL,NULL),(2556,'350610',322,'สงยาง','35130',NULL,NULL),(2557,'350701',323,'ฟ้าห่วน','35160',NULL,NULL),(2558,'350702',323,'กุดน้ำใส','35160',NULL,NULL),(2559,'350703',323,'น้ำอ้อม','35160',NULL,NULL),(2560,'350704',323,'ค้อวัง','35160',NULL,NULL),(2561,'350802',324,'บุ่งค้า','35120',NULL,NULL),(2562,'350803',324,'สวาท','35120',NULL,NULL),(2563,'350805',324,'ห้องแซง','35120',NULL,NULL),(2564,'350806',324,'สามัคคี','35120',NULL,NULL),(2565,'350807',324,'กุดเชียงหมี','35120',NULL,NULL),(2566,'350810',324,'สามแยก','35120',NULL,NULL),(2567,'350811',324,'กุดแห่','35120',NULL,NULL),(2568,'350812',324,'โคกสำราญ','35120',NULL,NULL),(2569,'350813',324,'สร้างมิ่ง','35120',NULL,NULL),(2570,'350814',324,'ศรีแก้ว','35120',NULL,NULL),(2571,'350901',325,'ไทยเจริญ','35120',NULL,NULL),(2572,'350902',325,'น้ำคำ','35120',NULL,NULL),(2573,'350903',325,'ส้มผ่อ','35120',NULL,NULL),(2574,'350904',325,'คำเตย','35120',NULL,NULL),(2575,'350905',325,'คำไผ่','35120',NULL,NULL),(2576,'360101',326,'ในเมือง','36000',NULL,NULL),(2577,'360102',326,'รอบเมือง','36000',NULL,NULL),(2578,'360103',326,'โพนทอง','36000',NULL,NULL),(2579,'360104',326,'นาฝาย','36000',NULL,NULL),(2580,'360105',326,'บ้านค่าย','36240',NULL,NULL),(2581,'360106',326,'กุดตุ้ม','36000',NULL,NULL),(2582,'360107',326,'ชีลอง','36000',NULL,NULL),(2583,'360108',326,'บ้านเล่า','36000',NULL,NULL),(2584,'360109',326,'นาเสียว','36000',NULL,NULL),(2585,'360110',326,'หนองนาแซง','36000',NULL,NULL),(2586,'360111',326,'ลาดใหญ่','36000',NULL,NULL),(2587,'360112',326,'หนองไผ่','36240',NULL,NULL),(2588,'360113',326,'ท่าหินโงม','36000',NULL,NULL),(2589,'360114',326,'ห้วยต้อน','36000',NULL,NULL),(2590,'360115',326,'ห้วยบง','36000',NULL,NULL),(2591,'360116',326,'โนนสำราญ','36240',NULL,NULL),(2592,'360117',326,'โคกสูง','36000',NULL,NULL),(2593,'360118',326,'บุ่งคล้า','36000',NULL,NULL),(2594,'360119',326,'ซับสีทอง','36000',NULL,NULL),(2595,'360201',327,'บ้านเขว้า','36170',NULL,NULL),(2596,'360202',327,'ตลาดแร้ง','36170',NULL,NULL),(2597,'360203',327,'ลุ่มลำชี','36170',NULL,NULL),(2598,'360204',327,'ชีบน','36170',NULL,NULL),(2599,'360205',327,'ภูแลนคา','36170',NULL,NULL),(2600,'360206',327,'โนนแดง','36170',NULL,NULL),(2601,'360301',328,'คอนสวรรค์','36140',NULL,NULL),(2602,'360302',328,'ยางหวาย','36140',NULL,NULL),(2603,'360303',328,'ช่องสามหมอ','36140',NULL,NULL),(2604,'360304',328,'โนนสะอาด','36140',NULL,NULL),(2605,'360305',328,'ห้วยไร่','36140',NULL,NULL),(2606,'360306',328,'บ้านโสก','36140',NULL,NULL),(2607,'360307',328,'โคกมั่งงอย','36140',NULL,NULL),(2608,'360308',328,'หนองขาม','36140',NULL,NULL),(2609,'360309',328,'ศรีสำราญ','36140',NULL,NULL),(2610,'360401',329,'บ้านยาง','36120',NULL,NULL),(2611,'360402',329,'บ้านหัน','36120',NULL,NULL),(2612,'360403',329,'บ้านเดื่อ','36120',NULL,NULL),(2613,'360404',329,'บ้านเป้า','36120',NULL,NULL),(2614,'360405',329,'กุดเลาะ','36120',NULL,NULL),(2615,'360406',329,'โนนกอก','36120',NULL,NULL),(2616,'360407',329,'สระโพนทอง','36120',NULL,NULL),(2617,'360408',329,'หนองข่า','36120',NULL,NULL),(2618,'360409',329,'หนองโพนงาม','36120',NULL,NULL),(2619,'360410',329,'บ้านบัว','36120',NULL,NULL),(2620,'360412',329,'โนนทอง','36120',NULL,NULL),(2621,'360501',330,'หนองบัวแดง','36210',NULL,NULL),(2622,'360502',330,'กุดชุมแสง','36210',NULL,NULL),(2623,'360503',330,'ถ้ำวัวแดง','36210',NULL,NULL),(2624,'360504',330,'นางแดด','36210',NULL,NULL),(2625,'360507',330,'หนองแวง','36210',NULL,NULL),(2626,'360508',330,'คูเมือง','36210',NULL,NULL),(2627,'360509',330,'ท่าใหญ่','36210',NULL,NULL),(2628,'360511',330,'วังชมภู','36210',NULL,NULL),(2629,'360601',331,'บ้านกอก','36130',NULL,NULL),(2630,'360602',331,'หนองบัวบาน','36130',NULL,NULL),(2631,'360603',331,'บ้านขาม','36130',NULL,NULL),(2632,'360605',331,'กุดน้ำใส','36130',NULL,NULL),(2633,'360606',331,'หนองโดน','36130',NULL,NULL),(2634,'360607',331,'ละหาน','36130',NULL,NULL),(2635,'360610',331,'หนองบัวใหญ่','36130',NULL,NULL),(2636,'360611',331,'หนองบัวโคก','36220',NULL,NULL),(2637,'360613',331,'ส้มป่อย','36130',NULL,NULL),(2638,'360701',332,'บ้านชวน','36160',NULL,NULL),(2639,'360702',332,'บ้านเพชร','36160',NULL,NULL),(2640,'360703',332,'บ้านตาล','36220',NULL,NULL),(2641,'360704',332,'หัวทะเล','36220',NULL,NULL),(2642,'360705',332,'โคกเริงรมย์','36160',NULL,NULL),(2643,'360706',332,'เกาะมะนาว','36160',NULL,NULL),(2644,'360707',332,'โคกเพชรพัฒนา','36160',NULL,NULL),(2645,'360801',333,'หนองบัวระเหว','36250',NULL,NULL),(2646,'360802',333,'วังตะเฆ่','36250',NULL,NULL),(2647,'360803',333,'ห้วยแย้','36250',NULL,NULL),(2648,'360804',333,'โคกสะอาด','36250',NULL,NULL),(2649,'360805',333,'โสกปลาดุก','36250',NULL,NULL),(2650,'360901',334,'วะตะแบก','36230',NULL,NULL),(2651,'360902',334,'ห้วยยายจิ๋ว','36230',NULL,NULL),(2652,'360903',334,'นายางกลัก','36230',NULL,NULL),(2653,'360904',334,'บ้านไร่','36230',NULL,NULL),(2654,'360905',334,'โป่งนก','36230',NULL,NULL),(2655,'361001',335,'ผักปัง','36110',NULL,NULL),(2656,'361002',335,'กวางโจน','36110',NULL,NULL),(2657,'361003',335,'หนองคอนไทย','36110',NULL,NULL),(2658,'361004',335,'บ้านแก้ง','36110',NULL,NULL),(2659,'361005',335,'กุดยม','36110',NULL,NULL),(2660,'361006',335,'บ้านเพชร','36110',NULL,NULL),(2661,'361007',335,'โคกสะอาด','36110',NULL,NULL),(2662,'361008',335,'หนองตูม','36110',NULL,NULL),(2663,'361009',335,'โอโล','36110',NULL,NULL),(2664,'361010',335,'ธาตุทอง','36110',NULL,NULL),(2665,'361011',335,'บ้านดอน','36110',NULL,NULL),(2666,'361101',336,'บ้านแท่น','36190',NULL,NULL),(2667,'361102',336,'สามสวน','36190',NULL,NULL),(2668,'361103',336,'สระพัง','36190',NULL,NULL),(2669,'361104',336,'บ้านเต่า','36190',NULL,NULL),(2670,'361105',336,'หนองคู','36190',NULL,NULL),(2671,'361201',337,'ช่องสามหมอ','36150',NULL,NULL),(2672,'361202',337,'หนองขาม','36150',NULL,NULL),(2673,'361203',337,'นาหนองทุ่ม','36150',NULL,NULL),(2674,'361204',337,'บ้านแก้ง','36150',NULL,NULL),(2675,'361205',337,'หนองสังข์','36150',NULL,NULL),(2676,'361206',337,'หลุบคา','36150',NULL,NULL),(2677,'361207',337,'โคกกุง','36150',NULL,NULL),(2678,'361208',337,'เก่าย่าดี','36150',NULL,NULL),(2679,'361209',337,'ท่ามะไฟหวาน','36150',NULL,NULL),(2680,'361210',337,'หนองไผ่','36150',NULL,NULL),(2681,'361301',338,'คอนสาร','36180',NULL,NULL),(2682,'361302',338,'ทุ่งพระ','36180',NULL,NULL),(2683,'361303',338,'โนนคูณ','36180',NULL,NULL),(2684,'361304',338,'ห้วยยาง','36180',NULL,NULL),(2685,'361305',338,'ทุ่งลุยลาย','36180',NULL,NULL),(2686,'361306',338,'ดงบัง','36180',NULL,NULL),(2687,'361307',338,'ทุ่งนาเลา','36180',NULL,NULL),(2688,'361308',338,'ดงกลาง','36180',NULL,NULL),(2689,'361401',339,'บ้านเจียง','36260',NULL,NULL),(2690,'361402',339,'เจาทอง','36260',NULL,NULL),(2691,'361403',339,'วังทอง','36260',NULL,NULL),(2692,'361404',339,'แหลมทอง','36260',NULL,NULL),(2693,'361501',340,'หนองฉิม','36130',NULL,NULL),(2694,'361502',340,'ตาเนิน','36130',NULL,NULL),(2695,'361503',340,'กะฮาด','36130',NULL,NULL),(2696,'361504',340,'รังงาม','36130',NULL,NULL),(2697,'361601',341,'ซับใหญ่','36130',NULL,NULL),(2698,'361602',341,'ท่ากูบ','36130',NULL,NULL),(2699,'361603',341,'ตะโกทอง','36130',NULL,NULL),(2700,'370101',342,'บุ่ง','37000',NULL,NULL),(2701,'370102',342,'ไก่คำ','37000',NULL,NULL),(2702,'370103',342,'นาจิก','37000',NULL,NULL),(2703,'370104',342,'ปลาค้าว','37000',NULL,NULL),(2704,'370105',342,'เหล่าพรวน','37000',NULL,NULL),(2705,'370106',342,'สร้างนกทา','37000',NULL,NULL),(2706,'370107',342,'คึมใหญ่','37000',NULL,NULL),(2707,'370108',342,'นาผือ','37000',NULL,NULL),(2708,'370109',342,'น้ำปลีก','37000',NULL,NULL),(2709,'370110',342,'นาวัง','37000',NULL,NULL),(2710,'370111',342,'นาหมอม้า','37000',NULL,NULL),(2711,'370112',342,'โนนโพธิ์','37000',NULL,NULL),(2712,'370113',342,'โนนหนามแท่ง','37000',NULL,NULL),(2713,'370114',342,'ห้วยไร่','37000',NULL,NULL),(2714,'370115',342,'หนองมะแซว','37000',NULL,NULL),(2715,'370116',342,'กุดปลาดุก','37000',NULL,NULL),(2716,'370117',342,'ดอนเมย','37000',NULL,NULL),(2717,'370118',342,'นายม','37000',NULL,NULL),(2718,'370119',342,'นาแต้','37000',NULL,NULL),(2719,'370201',343,'ชานุมาน','37210',NULL,NULL),(2720,'370202',343,'โคกสาร','37210',NULL,NULL),(2721,'370203',343,'คำเขื่อนแก้ว','37210',NULL,NULL),(2722,'370204',343,'โคกก่ง','37210',NULL,NULL),(2723,'370205',343,'ป่าก่อ','37210',NULL,NULL),(2724,'370301',344,'หนองข่า','37110',NULL,NULL),(2725,'370302',344,'คำโพน','37110',NULL,NULL),(2726,'370303',344,'นาหว้า','37110',NULL,NULL),(2727,'370304',344,'ลือ','37110',NULL,NULL),(2728,'370305',344,'ห้วย','37110',NULL,NULL),(2729,'370306',344,'โนนงาม','37110',NULL,NULL),(2730,'370307',344,'นาป่าแซง','37110',NULL,NULL),(2731,'370401',345,'พนา','37180',NULL,NULL),(2732,'370402',345,'จานลาน','37180',NULL,NULL),(2733,'370403',345,'ไม้กลอน','37180',NULL,NULL),(2734,'370404',345,'พระเหลา','37180',NULL,NULL),(2735,'370501',346,'เสนางคนิคม','37290',NULL,NULL),(2736,'370502',346,'โพนทอง','37290',NULL,NULL),(2737,'370503',346,'ไร่สีสุก','37290',NULL,NULL),(2738,'370504',346,'นาเวียง','37290',NULL,NULL),(2739,'370505',346,'หนองไฮ','37290',NULL,NULL),(2740,'370506',346,'หนองสามสี','37290',NULL,NULL),(2741,'370601',347,'หัวตะพาน','37240',NULL,NULL),(2742,'370602',347,'คำพระ','37240',NULL,NULL),(2743,'370603',347,'เค็งใหญ่','37240',NULL,NULL),(2744,'370604',347,'หนองแก้ว','37240',NULL,NULL),(2745,'370605',347,'โพนเมืองน้อย','37240',NULL,NULL),(2746,'370606',347,'สร้างถ่อน้อย','37240',NULL,NULL),(2747,'370607',347,'จิกดู่','37240',NULL,NULL),(2748,'370608',347,'รัตนวารี','37240',NULL,NULL),(2749,'370701',348,'อำนาจ','37000',NULL,NULL),(2750,'370702',348,'ดงมะยาง','37000',NULL,NULL),(2751,'370703',348,'เปือย','37000',NULL,NULL),(2752,'370704',348,'ดงบัง','37000',NULL,NULL),(2753,'370705',348,'ไร่ขี','37000',NULL,NULL),(2754,'370706',348,'แมด','37000',NULL,NULL),(2755,'370707',348,'โคกกลาง','37000',NULL,NULL),(2756,'380101',349,'บึงกาฬ','38000',NULL,NULL),(2757,'380102',349,'โนนสมบูรณ์','38000',NULL,NULL),(2758,'380103',349,'หนองเข็ง','38000',NULL,NULL),(2759,'380104',349,'หอคำ','38000',NULL,NULL),(2760,'380105',349,'หนองเลิง','38000',NULL,NULL),(2761,'380106',349,'โคกก่อง','38000',NULL,NULL),(2762,'380107',349,'นาสวรรค์','38000',NULL,NULL),(2763,'380108',349,'ไคสี','38000',NULL,NULL),(2764,'380109',349,'ชัยพร','38000',NULL,NULL),(2765,'380110',349,'วิศิษฐ์','38000',NULL,NULL),(2766,'380111',349,'คำนาดี','38000',NULL,NULL),(2767,'380112',349,'โป่งเปือย','38000',NULL,NULL),(2768,'380201',350,'ศรีชมภู','38150',NULL,NULL),(2769,'380202',350,'ดอนหญ้านาง','38150',NULL,NULL),(2770,'380203',350,'พรเจริญ','38150',NULL,NULL),(2771,'380204',350,'หนองหัวช้าง','38150',NULL,NULL),(2772,'380205',350,'วังชมภู','38150',NULL,NULL),(2773,'380206',350,'ป่าแฝก','38150',NULL,NULL),(2774,'380207',350,'ศรีสำราญ','38150',NULL,NULL),(2775,'380301',351,'โซ่','38170',NULL,NULL),(2776,'380302',351,'หนองพันทา','38170',NULL,NULL),(2777,'380303',351,'ศรีชมภู','38170',NULL,NULL),(2778,'380304',351,'คำแก้ว','38170',NULL,NULL),(2779,'380305',351,'บัวตูม','38170',NULL,NULL),(2780,'380306',351,'ถ้ำเจริญ','38170',NULL,NULL),(2781,'380307',351,'เหล่าทอง','38170',NULL,NULL),(2782,'380401',352,'เซกา','38180',NULL,NULL),(2783,'380402',352,'ซาง','38180',NULL,NULL),(2784,'380403',352,'ท่ากกแดง','38180',NULL,NULL),(2785,'380404',352,'บ้านต้อง','38180',NULL,NULL),(2786,'380405',352,'ป่งไฮ','38180',NULL,NULL),(2787,'380406',352,'น้ำจั้น','38180',NULL,NULL),(2788,'380407',352,'ท่าสะอาด','38180',NULL,NULL),(2789,'380408',352,'หนองทุ่ม','0',NULL,NULL),(2790,'380409',352,'โสกก่าม','0',NULL,NULL),(2791,'380501',353,'ปากคาด','38210',NULL,NULL),(2792,'380502',353,'หนองยอง','38210',NULL,NULL),(2793,'380503',353,'นากั้ง','38210',NULL,NULL),(2794,'380504',353,'โนนศิลา','38210',NULL,NULL),(2795,'380505',353,'สมสนุก','38210',NULL,NULL),(2796,'380506',353,'นาดง','0',NULL,NULL),(2797,'380601',354,'บึงโขงหลง','38220',NULL,NULL),(2798,'380602',354,'โพธิ์หมากแข้ง','38220',NULL,NULL),(2799,'380603',354,'ดงบัง','38220',NULL,NULL),(2800,'380604',354,'ท่าดอกคำ','38220',NULL,NULL),(2801,'380701',355,'ศรีวิไล','38190',NULL,NULL),(2802,'380702',355,'ชุมภูพร','38190',NULL,NULL),(2803,'380703',355,'นาแสง','38190',NULL,NULL),(2804,'380704',355,'นาสะแบง','38190',NULL,NULL),(2805,'380705',355,'นาสิงห์','38190',NULL,NULL),(2806,'380801',356,'บุ่งคล้า','38000',NULL,NULL),(2807,'380802',356,'หนองเดิ่น','38000',NULL,NULL),(2808,'380803',356,'โคกกว้าง','38000',NULL,NULL),(2809,'390101',357,'หนองบัว','39000',NULL,NULL),(2810,'390102',357,'หนองภัยศูนย์','39000',NULL,NULL),(2811,'390103',357,'โพธิ์ชัย','39000',NULL,NULL),(2812,'390104',357,'หนองสวรรค์','39000',NULL,NULL),(2813,'390105',357,'หัวนา','39000',NULL,NULL),(2814,'390106',357,'บ้านขาม','39000',NULL,NULL),(2815,'390107',357,'นามะเฟือง','39000',NULL,NULL),(2816,'390108',357,'บ้านพร้าว','39000',NULL,NULL),(2817,'390109',357,'โนนขมิ้น','39000',NULL,NULL),(2818,'390110',357,'ลำภู','39000',NULL,NULL),(2819,'390111',357,'กุดจิก','39000',NULL,NULL),(2820,'390112',357,'โนนทัน','39000',NULL,NULL),(2821,'390113',357,'นาคำไฮ','39000',NULL,NULL),(2822,'390114',357,'ป่าไม้งาม','39000',NULL,NULL),(2823,'390115',357,'หนองหว้า','39000',NULL,NULL),(2824,'390201',358,'นากลาง','39170',NULL,NULL),(2825,'390202',358,'ด่านช้าง','39170',NULL,NULL),(2826,'390205',358,'กุดดินจี่','39350',NULL,NULL),(2827,'390206',358,'ฝั่งแดง','39170',NULL,NULL),(2828,'390207',358,'เก่ากลอย','39350',NULL,NULL),(2829,'390209',358,'โนนเมือง','39170',NULL,NULL),(2830,'390210',358,'อุทัยสวรรค์','39170',NULL,NULL),(2831,'390211',358,'ดงสวรรค์','39350',NULL,NULL),(2832,'390213',358,'กุดแห่','39170',NULL,NULL),(2833,'390301',359,'โนนสัง','39140',NULL,NULL),(2834,'390302',359,'บ้านถิ่น','39140',NULL,NULL),(2835,'390303',359,'หนองเรือ','39140',NULL,NULL),(2836,'390304',359,'กุดดู่','39140',NULL,NULL),(2837,'390305',359,'บ้านค้อ','39140',NULL,NULL),(2838,'390306',359,'โนนเมือง','39140',NULL,NULL),(2839,'390307',359,'โคกใหญ่','39140',NULL,NULL),(2840,'390308',359,'โคกม่วง','39140',NULL,NULL),(2841,'390309',359,'นิคมพัฒนา','39140',NULL,NULL),(2842,'390310',359,'ปางกู่','39140',NULL,NULL),(2843,'390401',360,'เมืองใหม่','39180',NULL,NULL),(2844,'390402',360,'ศรีบุญเรือง','39180',NULL,NULL),(2845,'390403',360,'หนองบัวใต้','39180',NULL,NULL),(2846,'390404',360,'กุดสะเทียน','39180',NULL,NULL),(2847,'390405',360,'นากอก','39180',NULL,NULL),(2848,'390406',360,'โนนสะอาด','39180',NULL,NULL),(2849,'390407',360,'ยางหล่อ','39180',NULL,NULL),(2850,'390408',360,'โนนม่วง','39180',NULL,NULL),(2851,'390409',360,'หนองกุงแก้ว','39180',NULL,NULL),(2852,'390410',360,'หนองแก','39180',NULL,NULL),(2853,'390411',360,'ทรายทอง','39180',NULL,NULL),(2854,'390412',360,'หันนางาม','39180',NULL,NULL),(2855,'390501',361,'นาสี','39270',NULL,NULL),(2856,'390502',361,'บ้านโคก','39270',NULL,NULL),(2857,'390503',361,'นาดี','39270',NULL,NULL),(2858,'390504',361,'นาด่าน','39270',NULL,NULL),(2859,'390505',361,'ดงมะไฟ','39270',NULL,NULL),(2860,'390506',361,'สุวรรณคูหา','39270',NULL,NULL),(2861,'390507',361,'บุญทัน','39270',NULL,NULL),(2862,'390508',361,'กุดผึ้ง','39270',NULL,NULL),(2863,'390601',362,'นาเหล่า','39170',NULL,NULL),(2864,'390602',362,'นาแก','39170',NULL,NULL),(2865,'390603',362,'วังทอง','39170',NULL,NULL),(2866,'390604',362,'วังปลาป้อม','39170',NULL,NULL),(2867,'390605',362,'เทพคีรี','39170',NULL,NULL),(2868,'400101',363,'ในเมือง','40000',NULL,NULL),(2869,'400102',363,'สำราญ','40000',NULL,NULL),(2870,'400103',363,'โคกสี','40000',NULL,NULL),(2871,'400104',363,'ท่าพระ','40260',NULL,NULL),(2872,'400105',363,'บ้านทุ่ม','40000',NULL,NULL),(2873,'400106',363,'เมืองเก่า','40000',NULL,NULL),(2874,'400107',363,'พระลับ','40000',NULL,NULL),(2875,'400108',363,'สาวะถี','40000',NULL,NULL),(2876,'400109',363,'บ้านหว้า','40000',NULL,NULL),(2877,'400110',363,'บ้านค้อ','40000',NULL,NULL),(2878,'400111',363,'แดงใหญ่','40000',NULL,NULL),(2879,'400112',363,'ดอนช้าง','40000',NULL,NULL),(2880,'400113',363,'ดอนหัน','40260',NULL,NULL),(2881,'400114',363,'ศิลา','40000',NULL,NULL),(2882,'400115',363,'บ้านเป็ด','40000',NULL,NULL),(2883,'400116',363,'หนองตูม','40000',NULL,NULL),(2884,'400117',363,'บึงเนียม','40000',NULL,NULL),(2885,'400118',363,'โนนท่อน','40000',NULL,NULL),(2886,'400201',364,'หนองบัว','40270',NULL,NULL),(2887,'400202',364,'ป่าหวายนั่ง','40270',NULL,NULL),(2888,'400203',364,'โนนฆ้อง','40270',NULL,NULL),(2889,'400204',364,'บ้านเหล่า','40270',NULL,NULL),(2890,'400205',364,'ป่ามะนาว','40270',NULL,NULL),(2891,'400206',364,'บ้านฝาง','40270',NULL,NULL),(2892,'400207',364,'โคกงาม','40270',NULL,NULL),(2893,'400301',365,'พระยืน','40320',NULL,NULL),(2894,'400302',365,'พระบุ','40320',NULL,NULL),(2895,'400303',365,'บ้านโต้น','40320',NULL,NULL),(2896,'400304',365,'หนองแวง','40320',NULL,NULL),(2897,'400305',365,'ขามป้อม','40320',NULL,NULL),(2898,'400401',366,'หนองเรือ','40210',NULL,NULL),(2899,'400402',366,'บ้านเม็ง','40210',NULL,NULL),(2900,'400403',366,'บ้านกง','40240',NULL,NULL),(2901,'400404',366,'ยางคำ','40240',NULL,NULL),(2902,'400405',366,'จระเข้','40240',NULL,NULL),(2903,'400406',366,'โนนทอง','40210',NULL,NULL),(2904,'400407',366,'กุดกว้าง','40210',NULL,NULL),(2905,'400408',366,'โนนทัน','40210',NULL,NULL),(2906,'400409',366,'โนนสะอาด','40210',NULL,NULL),(2907,'400410',366,'บ้านผือ','40240',NULL,NULL),(2908,'400501',367,'ชุมแพ','40130',NULL,NULL),(2909,'400502',367,'โนนหัน','40290',NULL,NULL),(2910,'400503',367,'นาหนองทุ่ม','40290',NULL,NULL),(2911,'400504',367,'โนนอุดม','40130',NULL,NULL),(2912,'400505',367,'ขัวเรียง','40130',NULL,NULL),(2913,'400506',367,'หนองไผ่','40130',NULL,NULL),(2914,'400507',367,'ไชยสอ','40130',NULL,NULL),(2915,'400508',367,'วังหินลาด','40130',NULL,NULL),(2916,'400509',367,'นาเพียง','40130',NULL,NULL),(2917,'400510',367,'หนองเขียด','40290',NULL,NULL),(2918,'400511',367,'หนองเสาเล้า','40130',NULL,NULL),(2919,'400512',367,'โนนสะอาด','40290',NULL,NULL),(2920,'400601',368,'สีชมพู','40220',NULL,NULL),(2921,'400602',368,'ศรีสุข','40220',NULL,NULL),(2922,'400603',368,'นาจาน','40220',NULL,NULL),(2923,'400604',368,'วังเพิ่ม','40220',NULL,NULL),(2924,'400605',368,'ซำยาง','40220',NULL,NULL),(2925,'400606',368,'หนองแดง','40220',NULL,NULL),(2926,'400607',368,'ดงลาน','40220',NULL,NULL),(2927,'400608',368,'บริบูรณ์','40220',NULL,NULL),(2928,'400609',368,'บ้านใหม่','40220',NULL,NULL),(2929,'400610',368,'ภูห่าน','40220',NULL,NULL),(2930,'400701',369,'น้ำพอง','40140',NULL,NULL),(2931,'400702',369,'วังชัย','40140',NULL,NULL),(2932,'400703',369,'หนองกุง','40140',NULL,NULL),(2933,'400704',369,'บัวใหญ่','40140',NULL,NULL),(2934,'400705',369,'สะอาด','40310',NULL,NULL),(2935,'400706',369,'ม่วงหวาน','40310',NULL,NULL),(2936,'400707',369,'บ้านขาม','40140',NULL,NULL),(2937,'400708',369,'บัวเงิน','40140',NULL,NULL),(2938,'400709',369,'ทรายมูล','40140',NULL,NULL),(2939,'400710',369,'ท่ากระเสริม','40140',NULL,NULL),(2940,'400711',369,'พังทุย','40140',NULL,NULL),(2941,'400712',369,'กุดน้ำใส','40140',NULL,NULL),(2942,'400801',370,'โคกสูง','40250',NULL,NULL),(2943,'400802',370,'บ้านดง','40250',NULL,NULL),(2944,'400803',370,'เขื่อนอุบลรัตน์','40250',NULL,NULL),(2945,'400804',370,'นาคำ','40250',NULL,NULL),(2946,'400805',370,'ศรีสุขสำราญ','40250',NULL,NULL),(2947,'400806',370,'ทุ่งโป่ง','40250',NULL,NULL),(2948,'400901',371,'หนองโก','40170',NULL,NULL),(2949,'400902',371,'หนองกุงใหญ่','40170',NULL,NULL),(2950,'400905',371,'ห้วยโจด','40170',NULL,NULL),(2951,'400906',371,'ห้วยยาง','40170',NULL,NULL),(2952,'400907',371,'บ้านฝาง','40170',NULL,NULL),(2953,'400909',371,'ดูนสาด','40170',NULL,NULL),(2954,'400910',371,'หนองโน','40170',NULL,NULL),(2955,'400911',371,'น้ำอ้อม','40170',NULL,NULL),(2956,'400912',371,'หัวนาคำ','40170',NULL,NULL),(2957,'401001',372,'บ้านไผ่','40110',NULL,NULL),(2958,'401002',372,'ในเมือง','40110',NULL,NULL),(2959,'401005',372,'เมืองเพีย','40110',NULL,NULL),(2960,'401009',372,'บ้านลาน','40110',NULL,NULL),(2961,'401010',372,'แคนเหนือ','40110',NULL,NULL),(2962,'401011',372,'ภูเหล็ก','40110',NULL,NULL),(2963,'401013',372,'ป่าปอ','40110',NULL,NULL),(2964,'401014',372,'หินตั้ง','40110',NULL,NULL),(2965,'401016',372,'หนองน้ำใส','40110',NULL,NULL),(2966,'401017',372,'หัวหนอง','40110',NULL,NULL),(2967,'401101',373,'เปือยน้อย','40340',NULL,NULL),(2968,'401102',373,'วังม่วง','40340',NULL,NULL),(2969,'401103',373,'ขามป้อม','40340',NULL,NULL),(2970,'401104',373,'สระแก้ว','40340',NULL,NULL),(2971,'401201',374,'เมืองพล','40120',NULL,NULL),(2972,'401203',374,'โจดหนองแก','40120',NULL,NULL),(2973,'401204',374,'เก่างิ้ว','40120',NULL,NULL),(2974,'401205',374,'หนองมะเขือ','40120',NULL,NULL),(2975,'401206',374,'หนองแวงโสกพระ','40120',NULL,NULL),(2976,'401207',374,'เพ็กใหญ่','40120',NULL,NULL),(2977,'401208',374,'โคกสง่า','40120',NULL,NULL),(2978,'401209',374,'หนองแวงนางเบ้า','40120',NULL,NULL),(2979,'401210',374,'ลอมคอม','40120',NULL,NULL),(2980,'401211',374,'โนนข่า','40120',NULL,NULL),(2981,'401212',374,'โสกนกเต็น','40120',NULL,NULL),(2982,'401213',374,'หัวทุ่ง','40120',NULL,NULL),(2983,'401301',375,'คอนฉิม','40330',NULL,NULL),(2984,'401302',375,'ใหม่นาเพียง','40330',NULL,NULL),(2985,'401303',375,'โนนทอง','40330',NULL,NULL),(2986,'401304',375,'แวงใหญ่','40330',NULL,NULL),(2987,'401305',375,'โนนสะอาด','40330',NULL,NULL),(2988,'401401',376,'แวงน้อย','40230',NULL,NULL),(2989,'401402',376,'ก้านเหลือง','40230',NULL,NULL),(2990,'401403',376,'ท่านางแนว','40230',NULL,NULL),(2991,'401404',376,'ละหานนา','40230',NULL,NULL),(2992,'401405',376,'ท่าวัด','40230',NULL,NULL),(2993,'401406',376,'ทางขวาง','40230',NULL,NULL),(2994,'401501',377,'หนองสองห้อง','40190',NULL,NULL),(2995,'401502',377,'คึมชาด','40190',NULL,NULL),(2996,'401503',377,'โนนธาตุ','40190',NULL,NULL),(2997,'401504',377,'ตะกั่วป่า','40190',NULL,NULL),(2998,'401505',377,'สำโรง','40190',NULL,NULL),(2999,'401506',377,'หนองเม็ก','40190',NULL,NULL),(3000,'401507',377,'ดอนดู่','40190',NULL,NULL),(3001,'401508',377,'ดงเค็ง','40190',NULL,NULL),(3002,'401509',377,'หันโจด','40190',NULL,NULL),(3003,'401510',377,'ดอนดั่ง','40190',NULL,NULL),(3004,'401511',377,'วังหิน','40190',NULL,NULL),(3005,'401512',377,'หนองไผ่ล้อม','40190',NULL,NULL),(3006,'401601',378,'บ้านเรือ','40150',NULL,NULL),(3007,'401604',378,'หว้าทอง','40150',NULL,NULL),(3008,'401605',378,'กุดขอนแก่น','40150',NULL,NULL),(3009,'401606',378,'นาชุมแสง','40150',NULL,NULL),(3010,'401607',378,'นาหว้า','40150',NULL,NULL),(3011,'401610',378,'หนองกุงธนสาร','40150',NULL,NULL),(3012,'401612',378,'หนองกุงเซิน','40150',NULL,NULL),(3013,'401613',378,'สงเปือย','40150',NULL,NULL),(3014,'401614',378,'ทุ่งชมพู','40150',NULL,NULL),(3015,'401616',378,'ดินดำ','40150',NULL,NULL),(3016,'401617',378,'ภูเวียง','40150',NULL,NULL),(3017,'401701',379,'กุดเค้า','40160',NULL,NULL),(3018,'401702',379,'สวนหม่อน','40160',NULL,NULL),(3019,'401703',379,'หนองแปน','40160',NULL,NULL),(3020,'401704',379,'โพนเพ็ก','40160',NULL,NULL),(3021,'401705',379,'คำแคน','40160',NULL,NULL),(3022,'401706',379,'นาข่า','40160',NULL,NULL),(3023,'401707',379,'นางาม','40160',NULL,NULL),(3024,'401710',379,'ท่าศาลา','40160',NULL,NULL),(3025,'401801',380,'ชนบท','40180',NULL,NULL),(3026,'401802',380,'กุดเพียขอม','40180',NULL,NULL),(3027,'401803',380,'วังแสง','40180',NULL,NULL),(3028,'401804',380,'ห้วยแก','40180',NULL,NULL),(3029,'401805',380,'บ้านแท่น','40180',NULL,NULL),(3030,'401806',380,'ศรีบุญเรือง','40180',NULL,NULL),(3031,'401807',380,'โนนพะยอม','40180',NULL,NULL),(3032,'401808',380,'ปอแดง','40180',NULL,NULL),(3033,'401901',381,'เขาสวนกวาง','40280',NULL,NULL),(3034,'401902',381,'ดงเมืองแอม','40280',NULL,NULL),(3035,'401903',381,'นางิ้ว','40280',NULL,NULL),(3036,'401904',381,'โนนสมบูรณ์','40280',NULL,NULL),(3037,'401905',381,'คำม่วง','40280',NULL,NULL),(3038,'402001',382,'โนนคอม','40350',NULL,NULL),(3039,'402002',382,'นาฝาย','40350',NULL,NULL),(3040,'402003',382,'ภูผาม่าน','40350',NULL,NULL),(3041,'402004',382,'วังสวาบ','40350',NULL,NULL),(3042,'402005',382,'ห้วยม่วง','40350',NULL,NULL),(3043,'402101',383,'กระนวน','40170',NULL,NULL),(3044,'402102',383,'คำแมด','40170',NULL,NULL),(3045,'402103',383,'บ้านโนน','40170',NULL,NULL),(3046,'402104',383,'คูคำ','40170',NULL,NULL),(3047,'402105',383,'ห้วยเตย','40170',NULL,NULL),(3048,'402201',384,'บ้านโคก','40160',NULL,NULL),(3049,'402202',384,'โพธิ์ไชย','40160',NULL,NULL),(3050,'402203',384,'ซับสมบูรณ์','40160',NULL,NULL),(3051,'402204',384,'นาแพง','40160',NULL,NULL),(3052,'402301',385,'กุดธาตุ','40150',NULL,NULL),(3053,'402302',385,'บ้านโคก','40150',NULL,NULL),(3054,'402303',385,'ขนวน','40150',NULL,NULL),(3055,'402401',386,'บ้านแฮด','40110',NULL,NULL),(3056,'402402',386,'โคกสำราญ','40110',NULL,NULL),(3057,'402403',386,'โนนสมบูรณ์','40110',NULL,NULL),(3058,'402404',386,'หนองแซง','40110',NULL,NULL),(3059,'402501',387,'โนนศิลา','40110',NULL,NULL),(3060,'402502',387,'หนองปลาหมอ','40110',NULL,NULL),(3061,'402503',387,'บ้านหัน','40110',NULL,NULL),(3062,'402504',387,'เปือยใหญ่','40110',NULL,NULL),(3063,'402505',387,'โนนแดง','40110',NULL,NULL),(3064,'402602',388,'ในเมือง','0',NULL,NULL),(3065,'402608',388,'เขาน้อย','0',NULL,NULL),(3066,'402615',388,'เมืองเก่าพัฒนา','0',NULL,NULL),(3067,'410101',389,'หมากแข้ง','41000',NULL,NULL),(3068,'410102',389,'นิคมสงเคราะห์','41000',NULL,NULL),(3069,'410103',389,'บ้านขาว','41000',NULL,NULL),(3070,'410104',389,'หนองบัว','41000',NULL,NULL),(3071,'410105',389,'บ้านตาด','41000',NULL,NULL),(3072,'410106',389,'โนนสูง','41330',NULL,NULL),(3073,'410107',389,'หมูม่น','41000',NULL,NULL),(3074,'410108',389,'เชียงยืน','41000',NULL,NULL),(3075,'410109',389,'หนองนาคำ','41000',NULL,NULL),(3076,'410110',389,'กุดสระ','41000',NULL,NULL),(3077,'410111',389,'นาข่า','41000',NULL,NULL),(3078,'410112',389,'บ้านเลื่อม','41000',NULL,NULL),(3079,'410113',389,'เชียงพิณ','41000',NULL,NULL),(3080,'410114',389,'สามพร้าว','41000',NULL,NULL),(3081,'410115',389,'หนองไฮ','41000',NULL,NULL),(3082,'410117',389,'บ้านจั่น','41000',NULL,NULL),(3083,'410118',389,'หนองขอนกว้าง','41000',NULL,NULL),(3084,'410119',389,'โคกสะอาด','41000',NULL,NULL),(3085,'410120',389,'นากว้าง','41000',NULL,NULL),(3086,'410121',389,'หนองไผ่','41330',NULL,NULL),(3087,'410201',390,'กุดจับ','41250',NULL,NULL),(3088,'410202',390,'ปะโค','41250',NULL,NULL),(3089,'410203',390,'ขอนยูง','41250',NULL,NULL),(3090,'410204',390,'เชียงเพ็ง','41250',NULL,NULL),(3091,'410205',390,'สร้างก่อ','41250',NULL,NULL),(3092,'410206',390,'เมืองเพีย','41250',NULL,NULL),(3093,'410207',390,'ตาลเลียน','41250',NULL,NULL),(3094,'410301',391,'หมากหญ้า','41360',NULL,NULL),(3095,'410302',391,'หนองอ้อ','41220',NULL,NULL),(3096,'410303',391,'อูบมุง','41220',NULL,NULL),(3097,'410304',391,'กุดหมากไฟ','41220',NULL,NULL),(3098,'410305',391,'น้ำพ่น','41360',NULL,NULL),(3099,'410306',391,'หนองบัวบาน','41360',NULL,NULL),(3100,'410307',391,'โนนหวาย','41220',NULL,NULL),(3101,'410308',391,'หนองวัวซอ','41360',NULL,NULL),(3102,'410401',392,'ตูมใต้','41110',NULL,NULL),(3103,'410402',392,'พันดอน','41370',NULL,NULL),(3104,'410403',392,'เวียงคำ','41110',NULL,NULL),(3105,'410404',392,'แชแล','41110',NULL,NULL),(3106,'410406',392,'เชียงแหว','41110',NULL,NULL),(3107,'410407',392,'ห้วยเกิ้ง','41110',NULL,NULL),(3108,'410409',392,'เสอเพลอ','41370',NULL,NULL),(3109,'410410',392,'สีออ','41110',NULL,NULL),(3110,'410411',392,'ปะโค','41370',NULL,NULL),(3111,'410413',392,'ผาสุก','41370',NULL,NULL),(3112,'410414',392,'ท่าลี่','41110',NULL,NULL),(3113,'410415',392,'กุมภวาปี','41110',NULL,NULL),(3114,'410416',392,'หนองหว้า','41110',NULL,NULL),(3115,'410501',393,'โนนสะอาด','41240',NULL,NULL),(3116,'410502',393,'บุ่งแก้ว','41240',NULL,NULL),(3117,'410503',393,'โพธิ์ศรีสำราญ','41240',NULL,NULL),(3118,'410504',393,'ทมนางาม','41240',NULL,NULL),(3119,'410505',393,'หนองกุงศรี','41240',NULL,NULL),(3120,'410506',393,'โคกกลาง','41240',NULL,NULL),(3121,'410601',394,'หนองหาน','41130',NULL,NULL),(3122,'410602',394,'หนองเม็ก','41130',NULL,NULL),(3123,'410605',394,'พังงู','41130',NULL,NULL),(3124,'410606',394,'สะแบง','41130',NULL,NULL),(3125,'410607',394,'สร้อยพร้าว','41130',NULL,NULL),(3126,'410609',394,'บ้านเชียง','41320',NULL,NULL),(3127,'410610',394,'บ้านยา','41320',NULL,NULL),(3128,'410611',394,'โพนงาม','41130',NULL,NULL),(3129,'410612',394,'ผักตบ','41130',NULL,NULL),(3130,'410614',394,'หนองไผ่','41130',NULL,NULL),(3131,'410617',394,'ดอนหายโศก','41130',NULL,NULL),(3132,'410618',394,'หนองสระปลา','41320',NULL,NULL),(3133,'410701',395,'ทุ่งฝน','41310',NULL,NULL),(3134,'410702',395,'ทุ่งใหญ่','41310',NULL,NULL),(3135,'410703',395,'นาชุมแสง','41310',NULL,NULL),(3136,'410704',395,'นาทม','41310',NULL,NULL),(3137,'410801',396,'ไชยวาน','41290',NULL,NULL),(3138,'410802',396,'หนองหลัก','41290',NULL,NULL),(3139,'410803',396,'คำเลาะ','41290',NULL,NULL),(3140,'410804',396,'โพนสูง','41290',NULL,NULL),(3141,'410901',397,'ศรีธาตุ','41230',NULL,NULL),(3142,'410902',397,'จำปี','41230',NULL,NULL),(3143,'410903',397,'บ้านโปร่ง','41230',NULL,NULL),(3144,'410904',397,'หัวนาคำ','41230',NULL,NULL),(3145,'410905',397,'หนองนกเขียน','41230',NULL,NULL),(3146,'410906',397,'นายูง','41230',NULL,NULL),(3147,'410907',397,'ตาดทอง','41230',NULL,NULL),(3148,'411001',398,'หนองกุงทับม้า','41280',NULL,NULL),(3149,'411002',398,'หนองหญ้าไซ','41280',NULL,NULL),(3150,'411003',398,'บะยาว','41280',NULL,NULL),(3151,'411004',398,'ผาสุก','41280',NULL,NULL),(3152,'411005',398,'คำโคกสูง','41280',NULL,NULL),(3153,'411006',398,'วังสามหมอ','41280',NULL,NULL),(3154,'411101',399,'ศรีสุทโธ','41190',NULL,NULL),(3155,'411102',399,'บ้านดุง','41190',NULL,NULL),(3156,'411103',399,'ดงเย็น','41190',NULL,NULL),(3157,'411104',399,'โพนสูง','41190',NULL,NULL),(3158,'411105',399,'อ้อมกอ','41190',NULL,NULL),(3159,'411106',399,'บ้านจันทน์','41190',NULL,NULL),(3160,'411107',399,'บ้านชัย','41190',NULL,NULL),(3161,'411108',399,'นาไหม','41190',NULL,NULL),(3162,'411109',399,'ถ่อนนาลับ','41190',NULL,NULL),(3163,'411110',399,'วังทอง','41190',NULL,NULL),(3164,'411111',399,'บ้านม่วง','41190',NULL,NULL),(3165,'411112',399,'บ้านตาด','41190',NULL,NULL),(3166,'411113',399,'นาคำ','41190',NULL,NULL),(3167,'411701',400,'บ้านผือ','41160',NULL,NULL),(3168,'411702',400,'หายโศก','41160',NULL,NULL),(3169,'411703',400,'เขือน้ำ','41160',NULL,NULL),(3170,'411704',400,'คำบง','41160',NULL,NULL),(3171,'411705',400,'โนนทอง','41160',NULL,NULL),(3172,'411706',400,'ข้าวสาร','41160',NULL,NULL),(3173,'411707',400,'จำปาโมง','41160',NULL,NULL),(3174,'411708',400,'กลางใหญ่','41160',NULL,NULL),(3175,'411709',400,'เมืองพาน','41160',NULL,NULL),(3176,'411710',400,'คำด้วง','41160',NULL,NULL),(3177,'411711',400,'หนองหัวคู','41160',NULL,NULL),(3178,'411712',400,'บ้านค้อ','41160',NULL,NULL),(3179,'411713',400,'หนองแวง','41160',NULL,NULL),(3180,'411801',401,'นางัว','41210',NULL,NULL),(3181,'411802',401,'น้ำโสม','41210',NULL,NULL),(3182,'411805',401,'หนองแวง','41210',NULL,NULL),(3183,'411806',401,'บ้านหยวก','41210',NULL,NULL),(3184,'411807',401,'โสมเยี่ยม','41210',NULL,NULL),(3185,'411810',401,'ศรีสำราญ','41210',NULL,NULL),(3186,'411812',401,'สามัคคี','41210',NULL,NULL),(3187,'411901',402,'เพ็ญ','41150',NULL,NULL),(3188,'411902',402,'บ้านธาตุ','41150',NULL,NULL),(3189,'411903',402,'นาพู่','41150',NULL,NULL),(3190,'411904',402,'เชียงหวาง','41150',NULL,NULL),(3191,'411905',402,'สุมเส้า','41150',NULL,NULL),(3192,'411906',402,'นาบัว','41150',NULL,NULL),(3193,'411907',402,'บ้านเหล่า','41150',NULL,NULL),(3194,'411908',402,'จอมศรี','41150',NULL,NULL),(3195,'411909',402,'เตาไห','41150',NULL,NULL),(3196,'411910',402,'โคกกลาง','41150',NULL,NULL),(3197,'411911',402,'สร้างแป้น','41150',NULL,NULL),(3198,'412001',403,'สร้างคอม','41260',NULL,NULL),(3199,'412002',403,'เชียงดา','41260',NULL,NULL),(3200,'412003',403,'บ้านยวด','41260',NULL,NULL),(3201,'412004',403,'บ้านโคก','41260',NULL,NULL),(3202,'412005',403,'นาสะอาด','41260',NULL,NULL),(3203,'412006',403,'บ้านหินโงม','41260',NULL,NULL),(3204,'412101',404,'หนองแสง','41340',NULL,NULL),(3205,'412102',404,'แสงสว่าง','41340',NULL,NULL),(3206,'412103',404,'นาดี','41340',NULL,NULL),(3207,'412104',404,'ทับกุง','41340',NULL,NULL),(3208,'412201',405,'นายูง','41380',NULL,NULL),(3209,'412202',405,'บ้านก้อง','41380',NULL,NULL),(3210,'412203',405,'นาแค','41380',NULL,NULL),(3211,'412204',405,'โนนทอง','41380',NULL,NULL),(3212,'412301',406,'บ้านแดง','41130',NULL,NULL),(3213,'412302',406,'นาทราย','41130',NULL,NULL),(3214,'412303',406,'ดอนกลอย','41130',NULL,NULL),(3215,'412401',407,'บ้านจีต','41130',NULL,NULL),(3216,'412402',407,'โนนทองอินทร์','41130',NULL,NULL),(3217,'412403',407,'ค้อใหญ่','41130',NULL,NULL),(3218,'412404',407,'คอนสาย','41130',NULL,NULL),(3219,'412501',408,'นาม่วง','41110',NULL,NULL),(3220,'412502',408,'ห้วยสามพาด','41110',NULL,NULL),(3221,'412503',408,'อุ่มจาน','41110',NULL,NULL),(3222,'420101',409,'กุดป่อง','42000',NULL,NULL),(3223,'420102',409,'เมือง','42000',NULL,NULL),(3224,'420103',409,'นาอ้อ','42100',NULL,NULL),(3225,'420104',409,'กกดู่','42000',NULL,NULL),(3226,'420105',409,'น้ำหมาน','42000',NULL,NULL),(3227,'420106',409,'เสี้ยว','42000',NULL,NULL),(3228,'420107',409,'นาอาน','42000',NULL,NULL),(3229,'420108',409,'นาโป่ง','42000',NULL,NULL),(3230,'420109',409,'นาดินดำ','42000',NULL,NULL),(3231,'420110',409,'น้ำสวย','42000',NULL,NULL),(3232,'420111',409,'ชัยพฤกษ์','42000',NULL,NULL),(3233,'420112',409,'นาแขม','42000',NULL,NULL),(3234,'420113',409,'ศรีสองรัก','42100',NULL,NULL),(3235,'420114',409,'กกทอง','42000',NULL,NULL),(3236,'420201',410,'นาด้วง','42210',NULL,NULL),(3237,'420202',410,'นาดอกคำ','42210',NULL,NULL),(3238,'420203',410,'ท่าสะอาด','42210',NULL,NULL),(3239,'420204',410,'ท่าสวรรค์','42210',NULL,NULL),(3240,'420301',411,'เชียงคาน','42110',NULL,NULL),(3241,'420302',411,'ธาตุ','42110',NULL,NULL),(3242,'420303',411,'นาซ่าว','42110',NULL,NULL),(3243,'420304',411,'เขาแก้ว','42110',NULL,NULL),(3244,'420305',411,'ปากตม','42110',NULL,NULL),(3245,'420306',411,'บุฮม','42110',NULL,NULL),(3246,'420307',411,'จอมศรี','42110',NULL,NULL),(3247,'420308',411,'หาดทรายขาว','42110',NULL,NULL),(3248,'420401',412,'ปากชม','42150',NULL,NULL),(3249,'420402',412,'เชียงกลม','42150',NULL,NULL),(3250,'420403',412,'หาดคัมภีร์','42150',NULL,NULL),(3251,'420404',412,'ห้วยบ่อซืน','42150',NULL,NULL),(3252,'420405',412,'ห้วยพิชัย','42150',NULL,NULL),(3253,'420406',412,'ชมเจริญ','42150',NULL,NULL),(3254,'420501',413,'ด่านซ้าย','42120',NULL,NULL),(3255,'420502',413,'ปากหมัน','42120',NULL,NULL),(3256,'420503',413,'นาดี','42120',NULL,NULL),(3257,'420504',413,'โคกงาม','42120',NULL,NULL),(3258,'420505',413,'โพนสูง','42120',NULL,NULL),(3259,'420506',413,'อิปุ่ม','42120',NULL,NULL),(3260,'420507',413,'กกสะทอน','42120',NULL,NULL),(3261,'420508',413,'โป่ง','42120',NULL,NULL),(3262,'420509',413,'วังยาว','42120',NULL,NULL),(3263,'420510',413,'นาหอ','42120',NULL,NULL),(3264,'420601',414,'นาแห้ว','42170',NULL,NULL),(3265,'420602',414,'แสงภา','42170',NULL,NULL),(3266,'420603',414,'นาพึง','42170',NULL,NULL),(3267,'420604',414,'นามาลา','42170',NULL,NULL),(3268,'420605',414,'เหล่ากอหก','42170',NULL,NULL),(3269,'420701',415,'หนองบัว','42160',NULL,NULL),(3270,'420702',415,'ท่าศาลา','42160',NULL,NULL),(3271,'420703',415,'ร่องจิก','42160',NULL,NULL),(3272,'420704',415,'ปลาบ่า','42160',NULL,NULL),(3273,'420705',415,'ลาดค่าง','42160',NULL,NULL),(3274,'420706',415,'สานตม','42160',NULL,NULL),(3275,'420801',416,'ท่าลี่','42140',NULL,NULL),(3276,'420802',416,'หนองผือ','42140',NULL,NULL),(3277,'420803',416,'อาฮี','42140',NULL,NULL),(3278,'420804',416,'น้ำแคม','42140',NULL,NULL),(3279,'420805',416,'โคกใหญ่','42140',NULL,NULL),(3280,'420806',416,'น้ำทูน','42140',NULL,NULL),(3281,'420901',417,'วังสะพุง','42130',NULL,NULL),(3282,'420902',417,'ทรายขาว','42130',NULL,NULL),(3283,'420903',417,'หนองหญ้าปล้อง','42130',NULL,NULL),(3284,'420904',417,'หนองงิ้ว','42130',NULL,NULL),(3285,'420905',417,'ปากปวน','42130',NULL,NULL),(3286,'420906',417,'ผาน้อย','42130',NULL,NULL),(3287,'420910',417,'ผาบิ้ง','42130',NULL,NULL),(3288,'420911',417,'เขาหลวง','42130',NULL,NULL),(3289,'420912',417,'โคกขมิ้น','42130',NULL,NULL),(3290,'420913',417,'ศรีสงคราม','42130',NULL,NULL),(3291,'421001',418,'ศรีฐาน','42180',NULL,NULL),(3292,'421005',418,'ผานกเค้า','42180',NULL,NULL),(3293,'421007',418,'ภูกระดึง','42180',NULL,NULL),(3294,'421010',418,'ห้วยส้ม','42180',NULL,NULL),(3295,'421101',419,'ภูหอ','42230',NULL,NULL),(3296,'421102',419,'หนองคัน','42230',NULL,NULL),(3297,'421104',419,'ห้วยสีเสียด','42230',NULL,NULL),(3298,'421105',419,'เลยวังไสย์','42230',NULL,NULL),(3299,'421106',419,'แก่งศรีภูมิ','42230',NULL,NULL),(3300,'421201',420,'ผาขาว','42240',NULL,NULL),(3301,'421202',420,'ท่าช้างคล้อง','42240',NULL,NULL),(3302,'421203',420,'โนนปอแดง','42240',NULL,NULL),(3303,'421204',420,'โนนป่าซาง','42240',NULL,NULL),(3304,'421205',420,'บ้านเพิ่ม','42240',NULL,NULL),(3305,'421301',421,'เอราวัณ','42220',NULL,NULL),(3306,'421302',421,'ผาอินทร์แปลง','42220',NULL,NULL),(3307,'421303',421,'ผาสามยอด','42220',NULL,NULL),(3308,'421304',421,'ทรัพย์ไพวัลย์','42220',NULL,NULL),(3309,'421401',422,'หนองหิน','42190',NULL,NULL),(3310,'421402',422,'ตาดข่า','42190',NULL,NULL),(3311,'421403',422,'ปวนพุ','42190',NULL,NULL),(3312,'430101',423,'ในเมือง','43000',NULL,NULL),(3313,'430102',423,'มีชัย','43000',NULL,NULL),(3314,'430103',423,'โพธิ์ชัย','43000',NULL,NULL),(3315,'430104',423,'กวนวัน','43000',NULL,NULL),(3316,'430105',423,'เวียงคุก','43000',NULL,NULL),(3317,'430106',423,'วัดธาตุ','43000',NULL,NULL),(3318,'430107',423,'หาดคำ','43000',NULL,NULL),(3319,'430108',423,'หินโงม','43000',NULL,NULL),(3320,'430109',423,'บ้านเดื่อ','43000',NULL,NULL),(3321,'430110',423,'ค่ายบกหวาน','43100',NULL,NULL),(3322,'430111',423,'สองห้อง','43100',NULL,NULL),(3323,'430113',423,'พระธาตุบังพวน','43100',NULL,NULL),(3324,'430116',423,'หนองกอมเกาะ','43000',NULL,NULL),(3325,'430117',423,'ปะโค','43000',NULL,NULL),(3326,'430118',423,'เมืองหมี','43000',NULL,NULL),(3327,'430119',423,'สีกาย','43000',NULL,NULL),(3328,'430201',424,'ท่าบ่อ','43110',NULL,NULL),(3329,'430202',424,'น้ำโมง','43110',NULL,NULL),(3330,'430203',424,'กองนาง','43110',NULL,NULL),(3331,'430204',424,'โคกคอน','43110',NULL,NULL),(3332,'430205',424,'บ้านเดื่อ','43110',NULL,NULL),(3333,'430206',424,'บ้านถ่อน','43110',NULL,NULL),(3334,'430207',424,'บ้านว่าน','43110',NULL,NULL),(3335,'430208',424,'นาข่า','43110',NULL,NULL),(3336,'430209',424,'โพนสา','43110',NULL,NULL),(3337,'430210',424,'หนองนาง','43110',NULL,NULL),(3338,'430501',425,'จุมพล','43120',NULL,NULL),(3339,'430502',425,'วัดหลวง','43120',NULL,NULL),(3340,'430503',425,'กุดบง','43120',NULL,NULL),(3341,'430504',425,'ชุมช้าง','43120',NULL,NULL),(3342,'430506',425,'ทุ่งหลวง','43120',NULL,NULL),(3343,'430507',425,'เหล่าต่างคำ','43120',NULL,NULL),(3344,'430508',425,'นาหนัง','43120',NULL,NULL),(3345,'430509',425,'เซิม','43120',NULL,NULL),(3346,'430513',425,'บ้านโพธิ์','43120',NULL,NULL),(3347,'430521',425,'บ้านผือ','43120',NULL,NULL),(3348,'430522',425,'สร้างนางขาว','43120',NULL,NULL),(3349,'430701',426,'พานพร้าว','43130',NULL,NULL),(3350,'430703',426,'บ้านหม้อ','43130',NULL,NULL),(3351,'430704',426,'พระพุทธบาท','43130',NULL,NULL),(3352,'430705',426,'หนองปลาปาก','43130',NULL,NULL),(3353,'430801',427,'แก้งไก่','43160',NULL,NULL),(3354,'430802',427,'ผาตั้ง','43160',NULL,NULL),(3355,'430803',427,'บ้านม่วง','43160',NULL,NULL),(3356,'430804',427,'นางิ้ว','43160',NULL,NULL),(3357,'430805',427,'สังคม','43160',NULL,NULL),(3358,'431401',428,'สระใคร','43100',NULL,NULL),(3359,'431402',428,'คอกช้าง','43100',NULL,NULL),(3360,'431403',428,'บ้านฝาง','43100',NULL,NULL),(3361,'431501',429,'เฝ้าไร่','43120',NULL,NULL),(3362,'431502',429,'นาดี','43120',NULL,NULL),(3363,'431503',429,'หนองหลวง','43120',NULL,NULL),(3364,'431504',429,'วังหลวง','43120',NULL,NULL),(3365,'431505',429,'อุดมพร','43120',NULL,NULL),(3366,'431601',430,'รัตนวาปี','43120',NULL,NULL),(3367,'431602',430,'นาทับไฮ','43120',NULL,NULL),(3368,'431603',430,'บ้านต้อน','43120',NULL,NULL),(3369,'431604',430,'พระบาทนาสิงห์','43120',NULL,NULL),(3370,'431605',430,'โพนแพง','43120',NULL,NULL),(3371,'431701',431,'โพธิ์ตาก','43130',NULL,NULL),(3372,'431702',431,'โพนทอง','43130',NULL,NULL),(3373,'431703',431,'ด่านศรีสุข','43130',NULL,NULL),(3374,'440101',432,'ตลาด','44000',NULL,NULL),(3375,'440102',432,'เขวา','44000',NULL,NULL),(3376,'440103',432,'ท่าตูม','44000',NULL,NULL),(3377,'440104',432,'แวงน่าง','44000',NULL,NULL),(3378,'440105',432,'โคกก่อ','44000',NULL,NULL),(3379,'440106',432,'ดอนหว่าน','44000',NULL,NULL),(3380,'440107',432,'เกิ้ง','44000',NULL,NULL),(3381,'440108',432,'แก่งเลิงจาน','44000',NULL,NULL),(3382,'440109',432,'ท่าสองคอน','44000',NULL,NULL),(3383,'440110',432,'ลาดพัฒนา','44000',NULL,NULL),(3384,'440111',432,'หนองปลิง','44000',NULL,NULL),(3385,'440112',432,'ห้วยแอ่ง','44000',NULL,NULL),(3386,'440113',432,'หนองโน','44000',NULL,NULL),(3387,'440114',432,'บัวค้อ','44000',NULL,NULL),(3388,'440201',433,'แกดำ','44190',NULL,NULL),(3389,'440202',433,'วังแสง','44190',NULL,NULL),(3390,'440203',433,'มิตรภาพ','44190',NULL,NULL),(3391,'440204',433,'หนองกุง','44190',NULL,NULL),(3392,'440205',433,'โนนภิบาล','44190',NULL,NULL),(3393,'440301',434,'หัวขวาง','44140',NULL,NULL),(3394,'440302',434,'ยางน้อย','44140',NULL,NULL),(3395,'440303',434,'วังยาว','44140',NULL,NULL),(3396,'440304',434,'เขวาไร่','44140',NULL,NULL),(3397,'440305',434,'แพง','44140',NULL,NULL),(3398,'440306',434,'แก้งแก','44140',NULL,NULL),(3399,'440307',434,'หนองเหล็ก','44140',NULL,NULL),(3400,'440308',434,'หนองบัว','44140',NULL,NULL),(3401,'440309',434,'เหล่า','44140',NULL,NULL),(3402,'440310',434,'เขื่อน','44140',NULL,NULL),(3403,'440311',434,'หนองบอน','44140',NULL,NULL),(3404,'440312',434,'โพนงาม','44140',NULL,NULL),(3405,'440313',434,'ยางท่าแจ้ง','44140',NULL,NULL),(3406,'440314',434,'แห่ใต้','44140',NULL,NULL),(3407,'440315',434,'หนองกุงสวรรค์','44140',NULL,NULL),(3408,'440316',434,'เลิงใต้','44140',NULL,NULL),(3409,'440317',434,'ดอนกลาง','44140',NULL,NULL),(3410,'440401',435,'โคกพระ','44150',NULL,NULL),(3411,'440402',435,'คันธารราษฎร์','44150',NULL,NULL),(3412,'440403',435,'มะค่า','44150',NULL,NULL),(3413,'440404',435,'ท่าขอนยาง','44150',NULL,NULL),(3414,'440405',435,'นาสีนวน','44150',NULL,NULL),(3415,'440406',435,'ขามเรียง','44150',NULL,NULL),(3416,'440407',435,'เขวาใหญ่','44150',NULL,NULL),(3417,'440408',435,'ศรีสุข','44150',NULL,NULL),(3418,'440409',435,'กุดใส้จ่อ','44150',NULL,NULL),(3419,'440410',435,'ขามเฒ่าพัฒนา','44150',NULL,NULL),(3420,'440501',436,'เชียงยืน','44160',NULL,NULL),(3421,'440503',436,'หนองซอน','44160',NULL,NULL),(3422,'440505',436,'ดอนเงิน','44160',NULL,NULL),(3423,'440506',436,'กู่ทอง','44160',NULL,NULL),(3424,'440507',436,'นาทอง','44160',NULL,NULL),(3425,'440508',436,'เสือเฒ่า','44160',NULL,NULL),(3426,'440511',436,'โพนทอง','44160',NULL,NULL),(3427,'440512',436,'เหล่าบัวบาน','44160',NULL,NULL),(3428,'440601',437,'บรบือ','44130',NULL,NULL),(3429,'440602',437,'บ่อใหญ่','44130',NULL,NULL),(3430,'440604',437,'วังไชย','44130',NULL,NULL),(3431,'440605',437,'หนองม่วง','44130',NULL,NULL),(3432,'440606',437,'กำพี้','44130',NULL,NULL),(3433,'440607',437,'โนนราษี','44130',NULL,NULL),(3434,'440608',437,'โนนแดง','44130',NULL,NULL),(3435,'440610',437,'หนองจิก','44130',NULL,NULL),(3436,'440611',437,'บัวมาศ','44130',NULL,NULL),(3437,'440613',437,'หนองคูขาด','44130',NULL,NULL),(3438,'440615',437,'วังใหม่','44130',NULL,NULL),(3439,'440616',437,'ยาง','44130',NULL,NULL),(3440,'440618',437,'หนองสิม','44130',NULL,NULL),(3441,'440619',437,'หนองโก','44130',NULL,NULL),(3442,'440620',437,'ดอนงัว','44130',NULL,NULL),(3443,'440701',438,'นาเชือก','44170',NULL,NULL),(3444,'440702',438,'สำโรง','44170',NULL,NULL),(3445,'440703',438,'หนองแดง','44170',NULL,NULL),(3446,'440704',438,'เขวาไร่','44170',NULL,NULL),(3447,'440705',438,'หนองโพธิ์','44170',NULL,NULL),(3448,'440706',438,'ปอพาน','44170',NULL,NULL),(3449,'440707',438,'หนองเม็ก','44170',NULL,NULL),(3450,'440708',438,'หนองเรือ','44170',NULL,NULL),(3451,'440709',438,'หนองกุง','44170',NULL,NULL),(3452,'440710',438,'สันป่าตอง','44170',NULL,NULL),(3453,'440801',439,'ปะหลาน','44110',NULL,NULL),(3454,'440802',439,'ก้ามปู','44110',NULL,NULL),(3455,'440803',439,'เวียงสะอาด','44110',NULL,NULL),(3456,'440804',439,'เม็กดำ','44110',NULL,NULL),(3457,'440805',439,'นาสีนวล','44110',NULL,NULL),(3458,'440809',439,'ราษฎร์เจริญ','44110',NULL,NULL),(3459,'440810',439,'หนองบัวแก้ว','44110',NULL,NULL),(3460,'440812',439,'เมืองเตา','44110',NULL,NULL),(3461,'440815',439,'ลานสะแก','44110',NULL,NULL),(3462,'440816',439,'เวียงชัย','44110',NULL,NULL),(3463,'440818',439,'ราษฎร์พัฒนา','44110',NULL,NULL),(3464,'440819',439,'เมืองเสือ','44110',NULL,NULL),(3465,'440820',439,'ภารแอ่น','44110',NULL,NULL),(3466,'440828',439,'หนองบัว','0',NULL,NULL),(3467,'440901',440,'หนองแสง','44120',NULL,NULL),(3468,'440902',440,'ขามป้อม','44120',NULL,NULL),(3469,'440903',440,'เสือโก้ก','44120',NULL,NULL),(3470,'440904',440,'ดงใหญ่','44120',NULL,NULL),(3471,'440905',440,'โพธิ์ชัย','44120',NULL,NULL),(3472,'440906',440,'หัวเรือ','44120',NULL,NULL),(3473,'440907',440,'แคน','44120',NULL,NULL),(3474,'440908',440,'งัวบา','44120',NULL,NULL),(3475,'440909',440,'นาข่า','44120',NULL,NULL),(3476,'440910',440,'บ้านหวาย','44120',NULL,NULL),(3477,'440911',440,'หนองไฮ','44120',NULL,NULL),(3478,'440912',440,'ประชาพัฒนา','44120',NULL,NULL),(3479,'440913',440,'หนองทุ่ม','44120',NULL,NULL),(3480,'440914',440,'หนองแสน','44120',NULL,NULL),(3481,'440915',440,'โคกสีทองหลาง','44120',NULL,NULL),(3482,'441001',441,'นาดูน','44180',NULL,NULL),(3483,'441002',441,'หนองไผ่','44180',NULL,NULL),(3484,'441003',441,'หนองคู','44180',NULL,NULL),(3485,'441004',441,'ดงบัง','44180',NULL,NULL),(3486,'441005',441,'ดงดวน','44180',NULL,NULL),(3487,'441006',441,'หัวดง','44180',NULL,NULL),(3488,'441007',441,'ดงยาง','44180',NULL,NULL),(3489,'441008',441,'กู่สันตรัตน์','44180',NULL,NULL),(3490,'441009',441,'พระธาตุ','44180',NULL,NULL),(3491,'441101',442,'ยางสีสุราช','44210',NULL,NULL),(3492,'441102',442,'นาภู','44210',NULL,NULL),(3493,'441103',442,'แวงดง','44210',NULL,NULL),(3494,'441104',442,'บ้านกู่','44210',NULL,NULL),(3495,'441105',442,'ดงเมือง','44210',NULL,NULL),(3496,'441106',442,'ขามเรียน','44210',NULL,NULL),(3497,'441107',442,'หนองบัวสันตุ','44210',NULL,NULL),(3498,'441201',443,'กุดรัง','44130',NULL,NULL),(3499,'441202',443,'นาโพธิ์','44130',NULL,NULL),(3500,'441203',443,'เลิงแฝก','44130',NULL,NULL),(3501,'441204',443,'หนองแวง','44130',NULL,NULL),(3502,'441205',443,'ห้วยเตย','44130',NULL,NULL),(3503,'441301',444,'ชื่นชม','44160',NULL,NULL),(3504,'441302',444,'กุดปลาดุก','44160',NULL,NULL),(3505,'441303',444,'เหล่าดอกไม้','44160',NULL,NULL),(3506,'441304',444,'หนองกุง','44160',NULL,NULL),(3507,'450101',445,'ในเมือง','45000',NULL,NULL),(3508,'450102',445,'รอบเมือง','45000',NULL,NULL),(3509,'450103',445,'เหนือเมือง','45000',NULL,NULL),(3510,'450104',445,'ขอนแก่น','45000',NULL,NULL),(3511,'450105',445,'นาโพธิ์','45000',NULL,NULL),(3512,'450106',445,'สะอาดสมบูรณ์','45000',NULL,NULL),(3513,'450108',445,'สีแก้ว','45000',NULL,NULL),(3514,'450109',445,'ปอภาร','45000',NULL,NULL),(3515,'450110',445,'โนนรัง','45000',NULL,NULL),(3516,'450117',445,'หนองแก้ว','45000',NULL,NULL),(3517,'450118',445,'หนองแวง','45000',NULL,NULL),(3518,'450120',445,'ดงลาน','45000',NULL,NULL),(3519,'450123',445,'แคนใหญ่','45000',NULL,NULL),(3520,'450124',445,'โนนตาล','45000',NULL,NULL),(3521,'450125',445,'เมืองทอง','45000',NULL,NULL),(3522,'450201',446,'เกษตรวิสัย','45150',NULL,NULL),(3523,'450202',446,'เมืองบัว','45150',NULL,NULL),(3524,'450203',446,'เหล่าหลวง','45150',NULL,NULL),(3525,'450204',446,'สิงห์โคก','45150',NULL,NULL),(3526,'450205',446,'ดงครั่งใหญ่','45150',NULL,NULL),(3527,'450206',446,'บ้านฝาง','45150',NULL,NULL),(3528,'450207',446,'หนองแวง','45150',NULL,NULL),(3529,'450208',446,'กำแพง','45150',NULL,NULL),(3530,'450209',446,'กู่กาสิงห์','45150',NULL,NULL),(3531,'450210',446,'น้ำอ้อม','45150',NULL,NULL),(3532,'450211',446,'โนนสว่าง','45150',NULL,NULL),(3533,'450212',446,'ทุ่งทอง','45150',NULL,NULL),(3534,'450213',446,'ดงครั่งน้อย','45150',NULL,NULL),(3535,'450301',447,'บัวแดง','45190',NULL,NULL),(3536,'450302',447,'ดอกล้ำ','45190',NULL,NULL),(3537,'450303',447,'หนองแคน','45190',NULL,NULL),(3538,'450304',447,'โพนสูง','45190',NULL,NULL),(3539,'450305',447,'โนนสวรรค์','45190',NULL,NULL),(3540,'450306',447,'สระบัว','45190',NULL,NULL),(3541,'450307',447,'โนนสง่า','45190',NULL,NULL),(3542,'450308',447,'ขี้เหล็ก','45190',NULL,NULL),(3543,'450401',448,'หัวช้าง','45180',NULL,NULL),(3544,'450402',448,'หนองผือ','45180',NULL,NULL),(3545,'450403',448,'เมืองหงส์','45180',NULL,NULL),(3546,'450404',448,'โคกล่าม','45180',NULL,NULL),(3547,'450405',448,'น้ำใส','45180',NULL,NULL),(3548,'450406',448,'ดงแดง','45180',NULL,NULL),(3549,'450407',448,'ดงกลาง','45180',NULL,NULL),(3550,'450408',448,'ป่าสังข์','45180',NULL,NULL),(3551,'450409',448,'อีง่อง','45180',NULL,NULL),(3552,'450410',448,'ลิ้นฟ้า','45180',NULL,NULL),(3553,'450411',448,'ดู่น้อย','45180',NULL,NULL),(3554,'450412',448,'ศรีโคตร','45180',NULL,NULL),(3555,'450501',449,'นิเวศน์','45170',NULL,NULL),(3556,'450502',449,'ธงธานี','45170',NULL,NULL),(3557,'450503',449,'หนองไผ่','45170',NULL,NULL),(3558,'450504',449,'ธวัชบุรี','45170',NULL,NULL),(3559,'450506',449,'อุ่มเม้า','45170',NULL,NULL),(3560,'450507',449,'มะอึ','45170',NULL,NULL),(3561,'450510',449,'เขวาทุ่ง','45170',NULL,NULL),(3562,'450515',449,'ไพศาล','45170',NULL,NULL),(3563,'450517',449,'เมืองน้อย','45170',NULL,NULL),(3564,'450520',449,'บึงนคร','45170',NULL,NULL),(3565,'450522',449,'ราชธานี','45170',NULL,NULL),(3566,'450524',449,'หนองพอก','45170',NULL,NULL),(3567,'450601',450,'พนมไพร','45140',NULL,NULL),(3568,'450602',450,'แสนสุข','45140',NULL,NULL),(3569,'450603',450,'กุดน้ำใส','45140',NULL,NULL),(3570,'450604',450,'หนองทัพไทย','45140',NULL,NULL),(3571,'450605',450,'โพธิ์ใหญ่','45140',NULL,NULL),(3572,'450606',450,'วารีสวัสดิ์','45140',NULL,NULL),(3573,'450607',450,'โคกสว่าง','45140',NULL,NULL),(3574,'450611',450,'โพธิ์ชัย','45140',NULL,NULL),(3575,'450612',450,'นานวล','45140',NULL,NULL),(3576,'450613',450,'คำไฮ','45140',NULL,NULL),(3577,'450614',450,'สระแก้ว','45140',NULL,NULL),(3578,'450615',450,'ค้อใหญ่','45140',NULL,NULL),(3579,'450617',450,'ชานุวรรณ','45140',NULL,NULL),(3580,'450701',451,'แวง','45110',NULL,NULL),(3581,'450702',451,'โคกกกม่วง','45110',NULL,NULL),(3582,'450703',451,'นาอุดม','45110',NULL,NULL),(3583,'450704',451,'สว่าง','45110',NULL,NULL),(3584,'450705',451,'หนองใหญ่','45110',NULL,NULL),(3585,'450706',451,'โพธิ์ทอง','45110',NULL,NULL),(3586,'450707',451,'โนนชัยศรี','45110',NULL,NULL),(3587,'450708',451,'โพธิ์ศรีสว่าง','45110',NULL,NULL),(3588,'450709',451,'อุ่มเม่า','45110',NULL,NULL),(3589,'450710',451,'คำนาดี','45110',NULL,NULL),(3590,'450711',451,'พรมสวรรค์','45110',NULL,NULL),(3591,'450712',451,'สระนกแก้ว','45110',NULL,NULL),(3592,'450713',451,'วังสามัคคี','45110',NULL,NULL),(3593,'450714',451,'โคกสูง','45110',NULL,NULL),(3594,'450801',452,'ขามเปี้ย','45230',NULL,NULL),(3595,'450802',452,'เชียงใหม่','45230',NULL,NULL),(3596,'450803',452,'บัวคำ','45230',NULL,NULL),(3597,'450804',452,'อัคคะคำ','45230',NULL,NULL),(3598,'450805',452,'สะอาด','45230',NULL,NULL),(3599,'450806',452,'คำพอุง','45230',NULL,NULL),(3600,'450807',452,'หนองตาไก้','45230',NULL,NULL),(3601,'450808',452,'ดอนโอง','45230',NULL,NULL),(3602,'450809',452,'โพธิ์ศรี','45230',NULL,NULL),(3603,'450901',453,'หนองพอก','45210',NULL,NULL),(3604,'450902',453,'บึงงาม','45210',NULL,NULL),(3605,'450903',453,'ภูเขาทอง','45210',NULL,NULL),(3606,'450904',453,'กกโพธิ์','45210',NULL,NULL),(3607,'450905',453,'โคกสว่าง','45210',NULL,NULL),(3608,'450906',453,'หนองขุ่นใหญ่','45210',NULL,NULL),(3609,'450907',453,'รอบเมือง','45210',NULL,NULL),(3610,'450908',453,'ผาน้ำย้อย','45210',NULL,NULL),(3611,'450909',453,'ท่าสีดา','45210',NULL,NULL),(3612,'451001',454,'กลาง','45120',NULL,NULL),(3613,'451002',454,'นางาม','45120',NULL,NULL),(3614,'451003',454,'เมืองไพร','45120',NULL,NULL),(3615,'451004',454,'นาแซง','45120',NULL,NULL),(3616,'451005',454,'นาเมือง','45120',NULL,NULL),(3617,'451006',454,'วังหลวง','45120',NULL,NULL),(3618,'451007',454,'ท่าม่วง','45120',NULL,NULL),(3619,'451008',454,'ขวาว','45120',NULL,NULL),(3620,'451009',454,'โพธิ์ทอง','45120',NULL,NULL),(3621,'451010',454,'ภูเงิน','45120',NULL,NULL),(3622,'451011',454,'เกาะแก้ว','45120',NULL,NULL),(3623,'451012',454,'นาเลิง','45120',NULL,NULL),(3624,'451013',454,'เหล่าน้อย','45120',NULL,NULL),(3625,'451014',454,'ศรีวิสัย','45120',NULL,NULL),(3626,'451015',454,'หนองหลวง','45120',NULL,NULL),(3627,'451016',454,'พรสวรรค์','45120',NULL,NULL),(3628,'451017',454,'ขวัญเมือง','45120',NULL,NULL),(3629,'451018',454,'บึงเกลือ','45120',NULL,NULL),(3630,'451101',455,'สระคู','45130',NULL,NULL),(3631,'451102',455,'ดอกไม้','45130',NULL,NULL),(3632,'451103',455,'นาใหญ่','45130',NULL,NULL),(3633,'451104',455,'หินกอง','45130',NULL,NULL),(3634,'451105',455,'เมืองทุ่ง','45130',NULL,NULL),(3635,'451106',455,'หัวโทน','45130',NULL,NULL),(3636,'451107',455,'บ่อพันขัน','45130',NULL,NULL),(3637,'451108',455,'ทุ่งหลวง','45130',NULL,NULL),(3638,'451109',455,'หัวช้าง','45130',NULL,NULL),(3639,'451110',455,'น้ำคำ','45130',NULL,NULL),(3640,'451111',455,'ห้วยหินลาด','45130',NULL,NULL),(3641,'451112',455,'ช้างเผือก','45130',NULL,NULL),(3642,'451113',455,'ทุ่งกุลา','45130',NULL,NULL),(3643,'451114',455,'ทุ่งศรีเมือง','45130',NULL,NULL),(3644,'451115',455,'จำปาขัน','45130',NULL,NULL),(3645,'451201',456,'หนองผือ','45220',NULL,NULL),(3646,'451202',456,'หนองหิน','45220',NULL,NULL),(3647,'451203',456,'คูเมือง','45220',NULL,NULL),(3648,'451204',456,'กกกุง','45220',NULL,NULL),(3649,'451205',456,'เมืองสรวง','45220',NULL,NULL),(3650,'451301',457,'โพนทราย','45240',NULL,NULL),(3651,'451302',457,'สามขา','45240',NULL,NULL),(3652,'451303',457,'ศรีสว่าง','45240',NULL,NULL),(3653,'451304',457,'ยางคำ','45240',NULL,NULL),(3654,'451305',457,'ท่าหาดยาว','45240',NULL,NULL),(3655,'451401',458,'อาจสามารถ','45160',NULL,NULL),(3656,'451402',458,'โพนเมือง','45160',NULL,NULL),(3657,'451403',458,'บ้านแจ้ง','45160',NULL,NULL),(3658,'451404',458,'หน่อม','45160',NULL,NULL),(3659,'451405',458,'หนองหมื่นถ่าน','45160',NULL,NULL),(3660,'451406',458,'หนองขาม','45160',NULL,NULL),(3661,'451407',458,'โหรา','45160',NULL,NULL),(3662,'451408',458,'หนองบัว','45160',NULL,NULL),(3663,'451409',458,'ขี้เหล็ก','45160',NULL,NULL),(3664,'451501',459,'เมยวดี','45250',NULL,NULL),(3665,'451502',459,'ชุมพร','45250',NULL,NULL),(3666,'451503',459,'บุ่งเลิศ','45250',NULL,NULL),(3667,'451504',459,'ชมสะอาด','45250',NULL,NULL),(3668,'451601',460,'โพธิ์ทอง','45000',NULL,NULL),(3669,'451602',460,'ศรีสมเด็จ','45000',NULL,NULL),(3670,'451603',460,'เมืองเปลือย','45000',NULL,NULL),(3671,'451604',460,'หนองใหญ่','45000',NULL,NULL),(3672,'451605',460,'สวนจิก','45280',NULL,NULL),(3673,'451606',460,'โพธิ์สัย','45280',NULL,NULL),(3674,'451607',460,'หนองแวงควง','45000',NULL,NULL),(3675,'451608',460,'บ้านบาก','45000',NULL,NULL),(3676,'451701',461,'ดินดำ','45000',NULL,NULL),(3677,'451702',461,'ปาฝา','45000',NULL,NULL),(3678,'451703',461,'ม่วงลาด','45000',NULL,NULL),(3679,'451704',461,'จังหาร','45000',NULL,NULL),(3680,'451705',461,'ดงสิงห์','45000',NULL,NULL),(3681,'451706',461,'ยางใหญ่','45000',NULL,NULL),(3682,'451707',461,'ผักแว่น','45000',NULL,NULL),(3683,'451708',461,'แสนชาติ','45000',NULL,NULL),(3684,'451801',462,'เชียงขวัญ','45000',NULL,NULL),(3685,'451802',462,'พลับพลา','45170',NULL,NULL),(3686,'451803',462,'พระธาตุ','45000',NULL,NULL),(3687,'451804',462,'พระเจ้า','45000',NULL,NULL),(3688,'451805',462,'หมูม้น','45170',NULL,NULL),(3689,'451806',462,'บ้านเขือง','45000',NULL,NULL),(3690,'451901',463,'หนองฮี','45140',NULL,NULL),(3691,'451902',463,'สาวแห','45140',NULL,NULL),(3692,'451903',463,'ดูกอึ่ง','45140',NULL,NULL),(3693,'451904',463,'เด่นราษฎร์','45140',NULL,NULL),(3694,'452001',464,'ทุ่งเขาหลวง','45170',NULL,NULL),(3695,'452002',464,'เทอดไทย','45170',NULL,NULL),(3696,'452003',464,'บึงงาม','45170',NULL,NULL),(3697,'452004',464,'มะบ้า','45170',NULL,NULL),(3698,'452005',464,'เหล่า','45170',NULL,NULL),(3699,'460101',465,'กาฬสินธุ์','46000',NULL,NULL),(3700,'460102',465,'เหนือ','46000',NULL,NULL),(3701,'460103',465,'หลุบ','46000',NULL,NULL),(3702,'460104',465,'ไผ่','46000',NULL,NULL),(3703,'460105',465,'ลำปาว','46000',NULL,NULL),(3704,'460106',465,'ลำพาน','46000',NULL,NULL),(3705,'460107',465,'เชียงเครือ','46000',NULL,NULL),(3706,'460108',465,'บึงวิชัย','46000',NULL,NULL),(3707,'460109',465,'ห้วยโพธิ์','46000',NULL,NULL),(3708,'460111',465,'ภูปอ','46000',NULL,NULL),(3709,'460113',465,'ภูดิน','46000',NULL,NULL),(3710,'460115',465,'หนองกุง','46000',NULL,NULL),(3711,'460116',465,'กลางหมื่น','46000',NULL,NULL),(3712,'460117',465,'ขมิ้น','46000',NULL,NULL),(3713,'460119',465,'โพนทอง','46000',NULL,NULL),(3714,'460120',465,'นาจารย์','46000',NULL,NULL),(3715,'460121',465,'ลำคลอง','46000',NULL,NULL),(3716,'460201',466,'นามน','46230',NULL,NULL),(3717,'460202',466,'ยอดแกง','46230',NULL,NULL),(3718,'460203',466,'สงเปลือย','46230',NULL,NULL),(3719,'460204',466,'หลักเหลี่ยม','46230',NULL,NULL),(3720,'460205',466,'หนองบัว','46230',NULL,NULL),(3721,'460301',467,'กมลาไสย','46130',NULL,NULL),(3722,'460302',467,'หลักเมือง','46130',NULL,NULL),(3723,'460303',467,'โพนงาม','46130',NULL,NULL),(3724,'460304',467,'ดงลิง','46130',NULL,NULL),(3725,'460305',467,'ธัญญา','46130',NULL,NULL),(3726,'460308',467,'หนองแปน','46130',NULL,NULL),(3727,'460310',467,'เจ้าท่า','46130',NULL,NULL),(3728,'460311',467,'โคกสมบูรณ์','46130',NULL,NULL),(3729,'460401',468,'ร่องคำ','46210',NULL,NULL),(3730,'460402',468,'สามัคคี','46210',NULL,NULL),(3731,'460403',468,'เหล่าอ้อย','46210',NULL,NULL),(3732,'460501',469,'บัวขาว','46110',NULL,NULL),(3733,'460502',469,'แจนแลน','46110',NULL,NULL),(3734,'460503',469,'เหล่าใหญ่','46110',NULL,NULL),(3735,'460504',469,'จุมจัง','46110',NULL,NULL),(3736,'460505',469,'เหล่าไฮงาม','46110',NULL,NULL),(3737,'460506',469,'กุดหว้า','46110',NULL,NULL),(3738,'460507',469,'สามขา','46110',NULL,NULL),(3739,'460508',469,'นาขาม','46110',NULL,NULL),(3740,'460509',469,'หนองห้าง','46110',NULL,NULL),(3741,'460510',469,'นาโก','46110',NULL,NULL),(3742,'460511',469,'สมสะอาด','46110',NULL,NULL),(3743,'460512',469,'กุดค้าว','46110',NULL,NULL),(3744,'460601',470,'คุ้มเก่า','46160',NULL,NULL),(3745,'460602',470,'สงเปลือย','46160',NULL,NULL),(3746,'460603',470,'หนองผือ','46160',NULL,NULL),(3747,'460606',470,'กุดสิมคุ้มใหม่','46160',NULL,NULL),(3748,'460608',470,'สระพังทอง','46160',NULL,NULL),(3749,'460611',470,'กุดปลาค้าว','46160',NULL,NULL),(3750,'460701',471,'ยางตลาด','46120',NULL,NULL),(3751,'460702',471,'หัวงัว','46120',NULL,NULL),(3752,'460703',471,'อุ่มเม่า','46120',NULL,NULL),(3753,'460704',471,'บัวบาน','46120',NULL,NULL),(3754,'460705',471,'เว่อ','46120',NULL,NULL),(3755,'460706',471,'อิตื้อ','46120',NULL,NULL),(3756,'460707',471,'หัวนาคำ','46120',NULL,NULL),(3757,'460708',471,'หนองอีเฒ่า','46120',NULL,NULL),(3758,'460709',471,'ดอนสมบูรณ์','46120',NULL,NULL),(3759,'460710',471,'นาเชือก','46120',NULL,NULL),(3760,'460711',471,'คลองขาม','46120',NULL,NULL),(3761,'460712',471,'เขาพระนอน','46120',NULL,NULL),(3762,'460713',471,'นาดี','46120',NULL,NULL),(3763,'460714',471,'โนนสูง','46120',NULL,NULL),(3764,'460715',471,'หนองตอกแป้น','46120',NULL,NULL),(3765,'460801',472,'ห้วยเม็ก','46170',NULL,NULL),(3766,'460802',472,'คำใหญ่','46170',NULL,NULL),(3767,'460803',472,'กุดโดน','46170',NULL,NULL),(3768,'460804',472,'บึงนาเรียง','46170',NULL,NULL),(3769,'460805',472,'หัวหิน','46170',NULL,NULL),(3770,'460806',472,'พิมูล','46170',NULL,NULL),(3771,'460807',472,'คำเหมือดแก้ว','46170',NULL,NULL),(3772,'460808',472,'โนนสะอาด','46170',NULL,NULL),(3773,'460809',472,'ทรายทอง','46170',NULL,NULL),(3774,'460902',473,'สหัสขันธ์','46140',NULL,NULL),(3775,'460903',473,'นามะเขือ','46140',NULL,NULL),(3776,'460904',473,'โนนศิลา','46140',NULL,NULL),(3777,'460905',473,'นิคม','46140',NULL,NULL),(3778,'460906',473,'โนนแหลมทอง','46140',NULL,NULL),(3779,'460907',473,'โนนบุรี','46140',NULL,NULL),(3780,'460908',473,'โนนน้ำเกลี้ยง','46140',NULL,NULL),(3781,'461001',474,'ทุ่งคลอง','46180',NULL,NULL),(3782,'461002',474,'โพน','46180',NULL,NULL),(3783,'461005',474,'ดินจี่','46180',NULL,NULL),(3784,'461006',474,'นาบอน','46180',NULL,NULL),(3785,'461007',474,'นาทัน','46180',NULL,NULL),(3786,'461009',474,'เนินยาง','46180',NULL,NULL),(3787,'461101',475,'ท่าคันโท','46190',NULL,NULL),(3788,'461102',475,'กุงเก่า','46190',NULL,NULL),(3789,'461103',475,'ยางอู้ม','46190',NULL,NULL),(3790,'461104',475,'กุดจิก','46190',NULL,NULL),(3791,'461105',475,'นาตาล','46190',NULL,NULL),(3792,'461106',475,'ดงสมบูรณ์','46190',NULL,NULL),(3793,'461201',476,'หนองกุงศรี','46220',NULL,NULL),(3794,'461202',476,'หนองบัว','46220',NULL,NULL),(3795,'461203',476,'โคกเครือ','46220',NULL,NULL),(3796,'461204',476,'หนองสรวง','46220',NULL,NULL),(3797,'461205',476,'เสาเล้า','46220',NULL,NULL),(3798,'461206',476,'หนองใหญ่','46220',NULL,NULL),(3799,'461207',476,'ดงมูล','46220',NULL,NULL),(3800,'461208',476,'ลำหนองแสน','46220',NULL,NULL),(3801,'461209',476,'หนองหิน','46220',NULL,NULL),(3802,'461301',477,'สมเด็จ','46150',NULL,NULL),(3803,'461302',477,'หนองแวง','46150',NULL,NULL),(3804,'461303',477,'แซงบาดาล','46150',NULL,NULL),(3805,'461304',477,'มหาไชย','46150',NULL,NULL),(3806,'461305',477,'หมูม่น','46150',NULL,NULL),(3807,'461306',477,'ผาเสวย','46150',NULL,NULL),(3808,'461307',477,'ศรีสมเด็จ','46150',NULL,NULL),(3809,'461308',477,'ลำห้วยหลัว','46150',NULL,NULL),(3810,'461401',478,'คำบง','46240',NULL,NULL),(3811,'461402',478,'ไค้นุ่น','46240',NULL,NULL),(3812,'461403',478,'นิคมห้วยผึ้ง','46240',NULL,NULL),(3813,'461404',478,'หนองอีบุตร','46240',NULL,NULL),(3814,'461501',479,'สำราญ','46180',NULL,NULL),(3815,'461502',479,'สำราญใต้','46180',NULL,NULL),(3816,'461503',479,'คำสร้างเที่ยง','46180',NULL,NULL),(3817,'461504',479,'หนองช้าง','46180',NULL,NULL),(3818,'461601',480,'นาคู','46160',NULL,NULL),(3819,'461602',480,'สายนาวัง','46160',NULL,NULL),(3820,'461603',480,'โนนนาจาง','46160',NULL,NULL),(3821,'461604',480,'บ่อแก้ว','46160',NULL,NULL),(3822,'461605',480,'ภูแล่นช้าง','46160',NULL,NULL),(3823,'461701',481,'ดอนจาน','46000',NULL,NULL),(3824,'461702',481,'สะอาดไชยศรี','46000',NULL,NULL),(3825,'461703',481,'พยุง','46000',NULL,NULL),(3826,'461704',481,'ม่วงนา','46000',NULL,NULL),(3827,'461705',481,'นาจำปา','46000',NULL,NULL),(3828,'461801',482,'ฆ้องชัยพัฒนา','46130',NULL,NULL),(3829,'461802',482,'เหล่ากลาง','46130',NULL,NULL),(3830,'461803',482,'โนนศิลา','46130',NULL,NULL),(3831,'461804',482,'ลำชี','46130',NULL,NULL),(3832,'461805',482,'โคกสะอาด','46130',NULL,NULL),(3833,'470101',483,'ธาตุเชิงชุม','47000',NULL,NULL),(3834,'470102',483,'ขมิ้น','47220',NULL,NULL),(3835,'470103',483,'งิ้วด่อน','47000',NULL,NULL),(3836,'470104',483,'โนนหอม','47000',NULL,NULL),(3837,'470106',483,'เชียงเครือ','47000',NULL,NULL),(3838,'470107',483,'ท่าแร่','47000',NULL,NULL),(3839,'470109',483,'ม่วงลาย','47000',NULL,NULL),(3840,'470111',483,'ดงชน','47000',NULL,NULL),(3841,'470112',483,'ห้วยยาง','47000',NULL,NULL),(3842,'470113',483,'พังขว้าง','47000',NULL,NULL),(3843,'470115',483,'ดงมะไฟ','47000',NULL,NULL),(3844,'470116',483,'ธาตุนาเวง','47000',NULL,NULL),(3845,'470117',483,'เหล่าปอแดง','47000',NULL,NULL),(3846,'470118',483,'หนองลาด','47220',NULL,NULL),(3847,'470120',483,'ฮางโฮง','47000',NULL,NULL),(3848,'470121',483,'โคกก่อง','47000',NULL,NULL),(3849,'470201',484,'กุสุมาลย์','47210',NULL,NULL),(3850,'470202',484,'นาโพธิ์','47210',NULL,NULL),(3851,'470203',484,'นาเพียง','47230',NULL,NULL),(3852,'470204',484,'โพธิไพศาล','47210',NULL,NULL),(3853,'470205',484,'อุ่มจาน','47230',NULL,NULL),(3854,'470301',485,'กุดบาก','47180',NULL,NULL),(3855,'470303',485,'นาม่อง','47180',NULL,NULL),(3856,'470305',485,'กุดไห','47180',NULL,NULL),(3857,'470401',486,'พรรณา','47130',NULL,NULL),(3858,'470402',486,'วังยาง','47130',NULL,NULL),(3859,'470403',486,'พอกน้อย','47220',NULL,NULL),(3860,'470404',486,'นาหัวบ่อ','47220',NULL,NULL),(3861,'470405',486,'ไร่','47130',NULL,NULL),(3862,'470406',486,'ช้างมิ่ง','47130',NULL,NULL),(3863,'470407',486,'นาใน','47130',NULL,NULL),(3864,'470408',486,'สว่าง','47130',NULL,NULL),(3865,'470409',486,'บะฮี','47130',NULL,NULL),(3866,'470410',486,'เชิงชุม','47130',NULL,NULL),(3867,'470501',487,'พังโคน','47160',NULL,NULL),(3868,'470502',487,'ม่วงไข่','47160',NULL,NULL),(3869,'470503',487,'แร่','47160',NULL,NULL),(3870,'470504',487,'ไฮหย่อง','47160',NULL,NULL),(3871,'470505',487,'ต้นผึ้ง','47160',NULL,NULL),(3872,'470601',488,'วาริชภูมิ','47150',NULL,NULL),(3873,'470602',488,'ปลาโหล','47150',NULL,NULL),(3874,'470603',488,'หนองลาด','47150',NULL,NULL),(3875,'470604',488,'คำบ่อ','47150',NULL,NULL),(3876,'470605',488,'ค้อเขียว','47150',NULL,NULL),(3877,'470701',489,'นิคมน้ำอูน','47270',NULL,NULL),(3878,'470702',489,'หนองปลิง','47270',NULL,NULL),(3879,'470703',489,'หนองบัว','47270',NULL,NULL),(3880,'470704',489,'สุวรรณคาม','47270',NULL,NULL),(3881,'470801',490,'วานรนิวาส','47120',NULL,NULL),(3882,'470802',490,'เดื่อศรีคันไชย','47120',NULL,NULL),(3883,'470803',490,'ขัวก่าย','47120',NULL,NULL),(3884,'470804',490,'หนองสนม','47120',NULL,NULL),(3885,'470805',490,'คูสะคาม','47120',NULL,NULL),(3886,'470806',490,'ธาตุ','47120',NULL,NULL),(3887,'470807',490,'หนองแวง','47120',NULL,NULL),(3888,'470808',490,'ศรีวิชัย','47120',NULL,NULL),(3889,'470809',490,'นาซอ','47120',NULL,NULL),(3890,'470810',490,'อินทร์แปลง','47120',NULL,NULL),(3891,'470811',490,'นาคำ','47120',NULL,NULL),(3892,'470812',490,'คอนสวรรค์','47120',NULL,NULL),(3893,'470813',490,'กุดเรือคำ','47120',NULL,NULL),(3894,'470814',490,'หนองแวงใต้','47120',NULL,NULL),(3895,'470901',491,'คำตากล้า','47250',NULL,NULL),(3896,'470902',491,'หนองบัวสิม','47250',NULL,NULL),(3897,'470903',491,'นาแต้','47250',NULL,NULL),(3898,'470904',491,'แพด','47250',NULL,NULL),(3899,'471001',492,'ม่วง','47140',NULL,NULL),(3900,'471002',492,'มาย','47140',NULL,NULL),(3901,'471003',492,'ดงหม้อทอง','47140',NULL,NULL),(3902,'471004',492,'ดงเหนือ','47140',NULL,NULL),(3903,'471005',492,'ดงหม้อทองใต้','47140',NULL,NULL),(3904,'471006',492,'ห้วยหลัว','47140',NULL,NULL),(3905,'471007',492,'โนนสะอาด','47140',NULL,NULL),(3906,'471008',492,'หนองกวั่ง','47140',NULL,NULL),(3907,'471009',492,'บ่อแก้ว','47140',NULL,NULL),(3908,'471101',493,'อากาศ','47170',NULL,NULL),(3909,'471102',493,'โพนแพง','47170',NULL,NULL),(3910,'471103',493,'วาใหญ่','47170',NULL,NULL),(3911,'471104',493,'โพนงาม','47170',NULL,NULL),(3912,'471105',493,'ท่าก้อน','47170',NULL,NULL),(3913,'471106',493,'นาฮี','47170',NULL,NULL),(3914,'471107',493,'บะหว้า','47170',NULL,NULL),(3915,'471108',493,'สามัคคีพัฒนา','47170',NULL,NULL),(3916,'471201',494,'สว่างแดนดิน','47110',NULL,NULL),(3917,'471203',494,'คำสะอาด','47110',NULL,NULL),(3918,'471204',494,'บ้านต้าย','47110',NULL,NULL),(3919,'471206',494,'บงเหนือ','47110',NULL,NULL),(3920,'471207',494,'โพนสูง','47110',NULL,NULL),(3921,'471208',494,'โคกสี','47110',NULL,NULL),(3922,'471210',494,'หนองหลวง','47110',NULL,NULL),(3923,'471211',494,'บงใต้','47110',NULL,NULL),(3924,'471212',494,'ค้อใต้','47110',NULL,NULL),(3925,'471213',494,'พันนา','47240',NULL,NULL),(3926,'471214',494,'แวง','47240',NULL,NULL),(3927,'471215',494,'ทรายมูล','47110',NULL,NULL),(3928,'471216',494,'ตาลโกน','47240',NULL,NULL),(3929,'471217',494,'ตาลเนิ้ง','47240',NULL,NULL),(3930,'471220',494,'ธาตุทอง','47240',NULL,NULL),(3931,'471221',494,'บ้านถ่อน','47110',NULL,NULL),(3932,'471301',495,'ส่องดาว','47190',NULL,NULL),(3933,'471302',495,'ท่าศิลา','47190',NULL,NULL),(3934,'471303',495,'วัฒนา','47190',NULL,NULL),(3935,'471304',495,'ปทุมวาปี','47190',NULL,NULL),(3936,'471401',496,'เต่างอย','47260',NULL,NULL),(3937,'471402',496,'บึงทวาย','47260',NULL,NULL),(3938,'471403',496,'นาตาล','47260',NULL,NULL),(3939,'471404',496,'จันทร์เพ็ญ','47260',NULL,NULL),(3940,'471501',497,'ตองโขบ','47280',NULL,NULL),(3941,'471502',497,'เหล่าโพนค้อ','47280',NULL,NULL),(3942,'471503',497,'ด่านม่วงคำ','47280',NULL,NULL),(3943,'471504',497,'แมดนาท่ม','47280',NULL,NULL),(3944,'471601',498,'บ้านเหล่า','47290',NULL,NULL),(3945,'471602',498,'เจริญศิลป์','47290',NULL,NULL),(3946,'471603',498,'ทุ่งแก','47290',NULL,NULL),(3947,'471604',498,'โคกศิลา','47290',NULL,NULL),(3948,'471605',498,'หนองแปน','47290',NULL,NULL),(3949,'471701',499,'บ้านโพน','47230',NULL,NULL),(3950,'471702',499,'นาแก้ว','47230',NULL,NULL),(3951,'471703',499,'นาตงวัฒนา','47230',NULL,NULL),(3952,'471704',499,'บ้านแป้น','47230',NULL,NULL),(3953,'471705',499,'เชียงสือ','47230',NULL,NULL),(3954,'471801',500,'สร้างค้อ','47180',NULL,NULL),(3955,'471802',500,'หลุบเลา','47180',NULL,NULL),(3956,'471803',500,'โคกภู','47180',NULL,NULL),(3957,'471804',500,'กกปลาซิว','47180',NULL,NULL),(3958,'480101',501,'ในเมือง','48000',NULL,NULL),(3959,'480103',501,'นาทราย','48000',NULL,NULL),(3960,'480104',501,'นาราชควาย','48000',NULL,NULL),(3961,'480105',501,'กุรุคุ','48000',NULL,NULL),(3962,'480106',501,'บ้านผึ้ง','48000',NULL,NULL),(3963,'480107',501,'อาจสามารถ','48000',NULL,NULL),(3964,'480108',501,'ขามเฒ่า','48000',NULL,NULL),(3965,'480109',501,'บ้านกลาง','48000',NULL,NULL),(3966,'480110',501,'ท่าค้อ','48000',NULL,NULL),(3967,'480111',501,'คำเตย','48000',NULL,NULL),(3968,'480112',501,'หนองญาติ','48000',NULL,NULL),(3969,'480113',501,'ดงขวาง','48000',NULL,NULL),(3970,'480114',501,'วังตามัว','48000',NULL,NULL),(3971,'480115',501,'โพธิ์ตาก','48000',NULL,NULL),(3972,'480201',502,'ปลาปาก','48160',NULL,NULL),(3973,'480202',502,'หนองฮี','48160',NULL,NULL),(3974,'480203',502,'กุตาไก้','48160',NULL,NULL),(3975,'480204',502,'โคกสว่าง','48160',NULL,NULL),(3976,'480205',502,'โคกสูง','48160',NULL,NULL),(3977,'480206',502,'มหาชัย','48160',NULL,NULL),(3978,'480207',502,'นามะเขือ','48160',NULL,NULL),(3979,'480208',502,'หนองเทาใหญ่','48160',NULL,NULL),(3980,'480301',503,'ท่าอุเทน','48120',NULL,NULL),(3981,'480302',503,'โนนตาล','48120',NULL,NULL),(3982,'480303',503,'ท่าจำปา','48120',NULL,NULL),(3983,'480304',503,'ไชยบุรี','48120',NULL,NULL),(3984,'480305',503,'พนอม','48120',NULL,NULL),(3985,'480306',503,'พะทาย','48120',NULL,NULL),(3986,'480311',503,'เวินพระบาท','48120',NULL,NULL),(3987,'480312',503,'รามราช','48120',NULL,NULL),(3988,'480314',503,'หนองเทา','48120',NULL,NULL),(3989,'480401',504,'บ้านแพง','48140',NULL,NULL),(3990,'480402',504,'ไผ่ล้อม','48140',NULL,NULL),(3991,'480403',504,'โพนทอง','48140',NULL,NULL),(3992,'480404',504,'หนองแวง','48140',NULL,NULL),(3993,'480408',504,'นางัว','48140',NULL,NULL),(3994,'480501',505,'ธาตุพนม','48110',NULL,NULL),(3995,'480502',505,'ฝั่งแดง','48110',NULL,NULL),(3996,'480503',505,'โพนแพง','48110',NULL,NULL),(3997,'480504',505,'พระกลางทุ่ง','48110',NULL,NULL),(3998,'480505',505,'นาถ่อน','48110',NULL,NULL),(3999,'480506',505,'แสนพัน','48110',NULL,NULL),(4000,'480507',505,'ดอนนางหงส์','48110',NULL,NULL),(4001,'480508',505,'น้ำก่ำ','48110',NULL,NULL),(4002,'480509',505,'อุ่มเหม้า','48110',NULL,NULL),(4003,'480510',505,'นาหนาด','48110',NULL,NULL),(4004,'480511',505,'กุดฉิม','48110',NULL,NULL),(4005,'480512',505,'ธาตุพนมเหนือ','48110',NULL,NULL),(4006,'480601',506,'เรณู','48170',NULL,NULL),(4007,'480602',506,'โพนทอง','48170',NULL,NULL),(4008,'480603',506,'ท่าลาด','48170',NULL,NULL),(4009,'480604',506,'นางาม','48170',NULL,NULL),(4010,'480605',506,'โคกหินแฮ่','48170',NULL,NULL),(4011,'480607',506,'หนองย่างชิ้น','48170',NULL,NULL),(4012,'480608',506,'เรณูใต้','48170',NULL,NULL),(4013,'480609',506,'นาขาม','48170',NULL,NULL),(4014,'480701',507,'นาแก','48130',NULL,NULL),(4015,'480702',507,'พระซอง','48130',NULL,NULL),(4016,'480703',507,'หนองสังข์','48130',NULL,NULL),(4017,'480704',507,'นาคู่','48130',NULL,NULL),(4018,'480705',507,'พิมาน','48130',NULL,NULL),(4019,'480706',507,'พุ่มแก','48130',NULL,NULL),(4020,'480707',507,'ก้านเหลือง','48130',NULL,NULL),(4021,'480708',507,'หนองบ่อ','48130',NULL,NULL),(4022,'480709',507,'นาเลียง','48130',NULL,NULL),(4023,'480712',507,'บ้านแก้ง','48130',NULL,NULL),(4024,'480713',507,'คำพี้','48130',NULL,NULL),(4025,'480715',507,'สีชมพู','48130',NULL,NULL),(4026,'480801',508,'ศรีสงคราม','48150',NULL,NULL),(4027,'480802',508,'นาเดื่อ','48150',NULL,NULL),(4028,'480803',508,'บ้านเอื้อง','48150',NULL,NULL),(4029,'480804',508,'สามผง','48150',NULL,NULL),(4030,'480805',508,'ท่าบ่อสงคราม','48150',NULL,NULL),(4031,'480806',508,'บ้านข่า','48150',NULL,NULL),(4032,'480807',508,'นาคำ','48150',NULL,NULL),(4033,'480808',508,'โพนสว่าง','48150',NULL,NULL),(4034,'480809',508,'หาดแพง','48150',NULL,NULL),(4035,'480901',509,'นาหว้า','48180',NULL,NULL),(4036,'480902',509,'นางัว','48180',NULL,NULL),(4037,'480903',509,'บ้านเสียว','48180',NULL,NULL),(4038,'480904',509,'นาคูณใหญ่','48180',NULL,NULL),(4039,'480905',509,'เหล่าพัฒนา','48180',NULL,NULL),(4040,'480906',509,'ท่าเรือ','48180',NULL,NULL),(4041,'481001',510,'โพนสวรรค์','48190',NULL,NULL),(4042,'481002',510,'นาหัวบ่อ','48190',NULL,NULL),(4043,'481003',510,'นาขมิ้น','48190',NULL,NULL),(4044,'481005',510,'บ้านค้อ','48190',NULL,NULL),(4045,'481006',510,'โพนจาน','48190',NULL,NULL),(4046,'481101',511,'นาทม','48140',NULL,NULL),(4047,'481102',511,'หนองซน','48140',NULL,NULL),(4048,'481103',511,'ดอนเตย','48140',NULL,NULL),(4049,'481201',512,'วังยาง','48130',NULL,NULL),(4050,'481202',512,'ยอดชาด','48130',NULL,NULL),(4051,'481203',512,'โคกสี','48130',NULL,NULL),(4052,'481204',512,'หนองโพธิ์','48130',NULL,NULL),(4053,'490101',513,'มุกดาหาร','49000',NULL,NULL),(4054,'490102',513,'ศรีบุญเรือง','49000',NULL,NULL),(4055,'490103',513,'บ้านโคก','49000',NULL,NULL),(4056,'490104',513,'บางทรายใหญ่','49000',NULL,NULL),(4057,'490105',513,'โพนทราย','49000',NULL,NULL),(4058,'490106',513,'ผึ่งแดด','49000',NULL,NULL),(4059,'490107',513,'นาโสก','49000',NULL,NULL),(4060,'490108',513,'นาสีนวน','49000',NULL,NULL),(4061,'490109',513,'คำป่าหลาย','49000',NULL,NULL),(4062,'490110',513,'คำอาฮวน','49000',NULL,NULL),(4063,'490111',513,'ดงเย็น','49000',NULL,NULL),(4064,'490112',513,'ดงมอน','49000',NULL,NULL),(4065,'490113',513,'กุดแข้','49000',NULL,NULL),(4066,'490201',514,'นิคมคำสร้อย','49130',NULL,NULL),(4067,'490202',514,'นากอก','49130',NULL,NULL),(4068,'490203',514,'หนองแวง','49130',NULL,NULL),(4069,'490204',514,'กกแดง','49130',NULL,NULL),(4070,'490205',514,'นาอุดม','49130',NULL,NULL),(4071,'490206',514,'โชคชัย','49130',NULL,NULL),(4072,'490207',514,'ร่มเกล้า','49130',NULL,NULL),(4073,'490301',515,'ดอนตาล','49120',NULL,NULL),(4074,'490302',515,'โพธิ์ไทร','49120',NULL,NULL),(4075,'490303',515,'ป่าไร่','49120',NULL,NULL),(4076,'490304',515,'เหล่าหมี','49120',NULL,NULL),(4077,'490305',515,'บ้านบาก','49120',NULL,NULL),(4078,'490306',515,'นาสะเม็ง','49120',NULL,NULL),(4079,'490307',515,'บ้านแก้ง','49120',NULL,NULL),(4080,'490401',516,'ดงหลวง','49140',NULL,NULL),(4081,'490402',516,'หนองบัว','49140',NULL,NULL),(4082,'490403',516,'กกตูม','49140',NULL,NULL),(4083,'490404',516,'หนองแคน','49140',NULL,NULL),(4084,'490405',516,'ชะโนดน้อย','49140',NULL,NULL),(4085,'490406',516,'พังแดง','49140',NULL,NULL),(4086,'490501',517,'บ้านช่ง','0',NULL,NULL),(4087,'490504',517,'คำชะอี','49110',NULL,NULL),(4088,'490505',517,'หนองเอี่ยน','49110',NULL,NULL),(4089,'490506',517,'บ้านค้อ','49110',NULL,NULL),(4090,'490507',517,'บ้านเหล่า','49110',NULL,NULL),(4091,'490508',517,'โพนงาม','49110',NULL,NULL),(4092,'490511',517,'เหล่าสร้างถ่อ','49110',NULL,NULL),(4093,'490512',517,'คำบก','49110',NULL,NULL),(4094,'490514',517,'น้ำเที่ยง','49110',NULL,NULL),(4095,'490601',518,'หว้านใหญ่','49150',NULL,NULL),(4096,'490602',518,'ป่งขาม','49150',NULL,NULL),(4097,'490603',518,'บางทรายน้อย','49150',NULL,NULL),(4098,'490604',518,'ชะโนด','49150',NULL,NULL),(4099,'490605',518,'ดงหมู','49150',NULL,NULL),(4100,'490701',519,'หนองสูง','49160',NULL,NULL),(4101,'490702',519,'โนนยาง','49160',NULL,NULL),(4102,'490703',519,'ภูวง','49160',NULL,NULL),(4103,'490704',519,'บ้านเป้า','49160',NULL,NULL),(4104,'490705',519,'หนองสูงใต้','49160',NULL,NULL),(4105,'490706',519,'หนองสูงเหนือ','49160',NULL,NULL),(4106,'500101',520,'ศรีภูมิ','50200',NULL,NULL),(4107,'500102',520,'พระสิงห์','50200',NULL,NULL),(4108,'500103',520,'หายยา','50100',NULL,NULL),(4109,'500104',520,'ช้างม่อย','50300',NULL,NULL),(4110,'500105',520,'ช้างคลาน','50100',NULL,NULL),(4111,'500106',520,'วัดเกต','50000',NULL,NULL),(4112,'500107',520,'ช้างเผือก','50300',NULL,NULL),(4113,'500109',520,'สุเทพ','50100',NULL,NULL),(4114,'500110',520,'ป่าแดด','50100',NULL,NULL),(4115,'500111',520,'หนองหอย','50000',NULL,NULL),(4116,'500112',520,'ท่าศาลา','50000',NULL,NULL),(4117,'500113',520,'หนองป่าครั่ง','50000',NULL,NULL),(4118,'500114',520,'ฟ้าฮ่าม','50000',NULL,NULL),(4119,'500115',520,'ป่าตัน','50300',NULL,NULL),(4120,'500116',520,'สันผีเสื้อ','50300',NULL,NULL),(4121,'500203',521,'บ้านหลวง','50160',NULL,NULL),(4122,'500204',521,'ข่วงเปา','50160',NULL,NULL),(4123,'500205',521,'สบเตี๊ยะ','50160',NULL,NULL),(4124,'500206',521,'บ้านแปะ','50240',NULL,NULL),(4125,'500207',521,'ดอยแก้ว','50160',NULL,NULL),(4126,'500209',521,'แม่สอย','50240',NULL,NULL),(4127,'500301',522,'ช่างเคิ่ง','50270',NULL,NULL),(4128,'500302',522,'ท่าผา','50270',NULL,NULL),(4129,'500303',522,'บ้านทับ','50270',NULL,NULL),(4130,'500304',522,'แม่ศึก','50270',NULL,NULL),(4131,'500305',522,'แม่นาจร','50270',NULL,NULL),(4132,'500307',522,'ปางหินฝน','50270',NULL,NULL),(4133,'500308',522,'กองแขก','50270',NULL,NULL),(4134,'500401',523,'เชียงดาว','50170',NULL,NULL),(4135,'500402',523,'เมืองนะ','50170',NULL,NULL),(4136,'500403',523,'เมืองงาย','50170',NULL,NULL),(4137,'500404',523,'แม่นะ','50170',NULL,NULL),(4138,'500405',523,'เมืองคอง','50170',NULL,NULL),(4139,'500406',523,'ปิงโค้ง','50170',NULL,NULL),(4140,'500407',523,'ทุ่งข้าวพวง','50170',NULL,NULL),(4141,'500501',524,'เชิงดอย','50220',NULL,NULL),(4142,'500502',524,'สันปูเลย','50220',NULL,NULL),(4143,'500503',524,'ลวงเหนือ','50220',NULL,NULL),(4144,'500504',524,'ป่าป้อง','50220',NULL,NULL),(4145,'500505',524,'สง่าบ้าน','50220',NULL,NULL),(4146,'500506',524,'ป่าลาน','50220',NULL,NULL),(4147,'500507',524,'ตลาดขวัญ','50220',NULL,NULL),(4148,'500508',524,'สำราญราษฎร์','50220',NULL,NULL),(4149,'500509',524,'แม่คือ','50220',NULL,NULL),(4150,'500510',524,'ตลาดใหญ่','50220',NULL,NULL),(4151,'500511',524,'แม่ฮ้อยเงิน','50220',NULL,NULL),(4152,'500512',524,'แม่โป่ง','50220',NULL,NULL),(4153,'500513',524,'ป่าเมี่ยง','50220',NULL,NULL),(4154,'500514',524,'เทพเสด็จ','50220',NULL,NULL),(4155,'500601',525,'สันมหาพน','50150',NULL,NULL),(4156,'500602',525,'แม่แตง','50150',NULL,NULL),(4157,'500603',525,'ขี้เหล็ก','50150',NULL,NULL),(4158,'500604',525,'ช่อแล','50150',NULL,NULL),(4159,'500605',525,'แม่หอพระ','50150',NULL,NULL),(4160,'500606',525,'สบเปิง','50150',NULL,NULL),(4161,'500607',525,'บ้านเป้า','50150',NULL,NULL),(4162,'500608',525,'สันป่ายาง','50330',NULL,NULL),(4163,'500609',525,'ป่าแป๋','50150',NULL,NULL),(4164,'500610',525,'เมืองก๋าย','50150',NULL,NULL),(4165,'500611',525,'บ้านช้าง','50150',NULL,NULL),(4166,'500612',525,'กื้ดช้าง','50150',NULL,NULL),(4167,'500613',525,'อินทขิล','50150',NULL,NULL),(4168,'500701',526,'ริมใต้','50180',NULL,NULL),(4169,'500702',526,'ริมเหนือ','50180',NULL,NULL),(4170,'500703',526,'สันโป่ง','50180',NULL,NULL),(4171,'500704',526,'ขี้เหล็ก','50180',NULL,NULL),(4172,'500705',526,'สะลวง','50330',NULL,NULL),(4173,'500706',526,'ห้วยทราย','50180',NULL,NULL),(4174,'500707',526,'แม่แรม','50180',NULL,NULL),(4175,'500708',526,'โป่งแยง','50180',NULL,NULL),(4176,'500709',526,'แม่สา','50180',NULL,NULL),(4177,'500710',526,'ดอนแก้ว','50180',NULL,NULL),(4178,'500711',526,'เหมืองแก้ว','50180',NULL,NULL),(4179,'500801',527,'สะเมิงใต้','50250',NULL,NULL),(4180,'500802',527,'สะเมิงเหนือ','50250',NULL,NULL),(4181,'500803',527,'แม่สาบ','50250',NULL,NULL),(4182,'500804',527,'บ่อแก้ว','50250',NULL,NULL),(4183,'500805',527,'ยั้งเมิน','50250',NULL,NULL),(4184,'500901',528,'เวียง','50110',NULL,NULL),(4185,'500903',528,'ม่อนปิ่น','50110',NULL,NULL),(4186,'500904',528,'แม่งอน','50320',NULL,NULL),(4187,'500906',528,'สันทราย','50110',NULL,NULL),(4188,'500910',528,'แม่คะ','50110',NULL,NULL),(4189,'500912',528,'โป่งน้ำร้อน','50110',NULL,NULL),(4190,'501001',529,'แม่อาย','50280',NULL,NULL),(4191,'501002',529,'แม่สาว','50280',NULL,NULL),(4192,'501003',529,'สันต้นหมื้อ','50280',NULL,NULL),(4193,'501004',529,'แม่นาวาง','50280',NULL,NULL),(4194,'501005',529,'ท่าตอน','50280',NULL,NULL),(4195,'501006',529,'บ้านหลวง','50280',NULL,NULL),(4196,'501101',530,'เวียง','50190',NULL,NULL),(4197,'501102',530,'ทุ่งหลวง','50190',NULL,NULL),(4198,'501103',530,'ป่าตุ้ม','50190',NULL,NULL),(4199,'501104',530,'ป่าไหน่','50190',NULL,NULL),(4200,'501105',530,'สันทราย','50190',NULL,NULL),(4201,'501106',530,'บ้านโป่ง','50190',NULL,NULL),(4202,'501107',530,'น้ำแพร่','50190',NULL,NULL),(4203,'501108',530,'เขื่อนผาก','50190',NULL,NULL),(4204,'501109',530,'แม่แวน','50190',NULL,NULL),(4205,'501110',530,'แม่ปั๋ง','50190',NULL,NULL),(4206,'501111',530,'โหล่งขอด','50190',NULL,NULL),(4207,'501201',531,'ยุหว่า','50120',NULL,NULL),(4208,'501202',531,'สันกลาง','50120',NULL,NULL),(4209,'501203',531,'ท่าวังพร้าว','50120',NULL,NULL),(4210,'501204',531,'มะขามหลวง','50120',NULL,NULL),(4211,'501205',531,'แม่ก๊า','50120',NULL,NULL),(4212,'501206',531,'บ้านแม','50120',NULL,NULL),(4213,'501207',531,'บ้านกลาง','50120',NULL,NULL),(4214,'501208',531,'ทุ่งสะโตก','50120',NULL,NULL),(4215,'501210',531,'ทุ่งต้อม','50120',NULL,NULL),(4216,'501214',531,'น้ำบ่อหลวง','50120',NULL,NULL),(4217,'501215',531,'มะขุนหวาน','50120',NULL,NULL),(4218,'501301',532,'สันกำแพง','50130',NULL,NULL),(4219,'501302',532,'ทรายมูล','50130',NULL,NULL),(4220,'501303',532,'ร้องวัวแดง','50130',NULL,NULL),(4221,'501304',532,'บวกค้าง','50130',NULL,NULL),(4222,'501305',532,'แช่ช้าง','50130',NULL,NULL),(4223,'501306',532,'ออนใต้','50130',NULL,NULL),(4224,'501310',532,'แม่ปูคา','50130',NULL,NULL),(4225,'501311',532,'ห้วยทราย','50130',NULL,NULL),(4226,'501312',532,'ต้นเปา','50130',NULL,NULL),(4227,'501313',532,'สันกลาง','50130',NULL,NULL),(4228,'501401',533,'สันทรายหลวง','50210',NULL,NULL),(4229,'501402',533,'สันทรายน้อย','50210',NULL,NULL),(4230,'501403',533,'สันพระเนตร','50210',NULL,NULL),(4231,'501404',533,'สันนาเม็ง','50210',NULL,NULL),(4232,'501405',533,'สันป่าเปา','50210',NULL,NULL),(4233,'501406',533,'หนองแหย่ง','50210',NULL,NULL),(4234,'501407',533,'หนองจ๊อม','50210',NULL,NULL),(4235,'501408',533,'หนองหาร','50290',NULL,NULL),(4236,'501410',533,'แม่แฝกใหม่','50290',NULL,NULL),(4237,'501411',533,'เมืองเล็น','50210',NULL,NULL),(4238,'501412',533,'ป่าไผ่','50210',NULL,NULL),(4239,'501501',534,'หางดง','50230',NULL,NULL),(4240,'501502',534,'หนองแก๋ว','50230',NULL,NULL),(4241,'501503',534,'หารแก้ว','50230',NULL,NULL),(4242,'501504',534,'หนองตอง','50340',NULL,NULL),(4243,'501505',534,'ขุนคง','50230',NULL,NULL),(4244,'501506',534,'สบแม่ข่า','50230',NULL,NULL),(4245,'501507',534,'บ้านแหวน','50230',NULL,NULL),(4246,'501508',534,'สันผักหวาน','50230',NULL,NULL),(4247,'501509',534,'หนองควาย','50230',NULL,NULL),(4248,'501510',534,'บ้านปง','50230',NULL,NULL),(4249,'501511',534,'น้ำแพร่','50230',NULL,NULL),(4250,'501601',535,'หางดง','50240',NULL,NULL),(4251,'501602',535,'ฮอด','50240',NULL,NULL),(4252,'501603',535,'บ้านตาล','50240',NULL,NULL),(4253,'501604',535,'บ่อหลวง','50240',NULL,NULL),(4254,'501605',535,'บ่อสลี','50240',NULL,NULL),(4255,'501606',535,'นาคอเรือ','50240',NULL,NULL),(4256,'501701',536,'ดอยเต่า','50260',NULL,NULL),(4257,'501702',536,'ท่าเดื่อ','50260',NULL,NULL),(4258,'501703',536,'มืดกา','50260',NULL,NULL),(4259,'501704',536,'บ้านแอ่น','50260',NULL,NULL),(4260,'501705',536,'บงตัน','50260',NULL,NULL),(4261,'501706',536,'โปงทุ่ง','50260',NULL,NULL),(4262,'501801',537,'อมก๋อย','50310',NULL,NULL),(4263,'501802',537,'ยางเปียง','50310',NULL,NULL),(4264,'501803',537,'แม่ตื่น','50310',NULL,NULL),(4265,'501804',537,'ม่อนจอง','50310',NULL,NULL),(4266,'501805',537,'สบโขง','50310',NULL,NULL),(4267,'501806',537,'นาเกียน','50310',NULL,NULL),(4268,'501901',538,'ยางเนิ้ง','50140',NULL,NULL),(4269,'501902',538,'สารภี','50140',NULL,NULL),(4270,'501903',538,'ชมภู','50140',NULL,NULL),(4271,'501904',538,'ไชยสถาน','50140',NULL,NULL),(4272,'501905',538,'ขัวมุง','50140',NULL,NULL),(4273,'501906',538,'หนองแฝก','50140',NULL,NULL),(4274,'501907',538,'หนองผึ้ง','50140',NULL,NULL),(4275,'501908',538,'ท่ากว้าง','50140',NULL,NULL),(4276,'501909',538,'ดอนแก้ว','50140',NULL,NULL),(4277,'501910',538,'ท่าวังตาล','50140',NULL,NULL),(4278,'501911',538,'สันทราย','50140',NULL,NULL),(4279,'501912',538,'ป่าบง','50140',NULL,NULL),(4280,'502001',539,'เมืองแหง','50350',NULL,NULL),(4281,'502002',539,'เปียงหลวง','50350',NULL,NULL),(4282,'502003',539,'แสนไห','50350',NULL,NULL),(4283,'502101',540,'ปงตำ','50320',NULL,NULL),(4284,'502102',540,'ศรีดงเย็น','50320',NULL,NULL),(4285,'502103',540,'แม่ทะลบ','50320',NULL,NULL),(4286,'502104',540,'หนองบัว','50320',NULL,NULL),(4287,'502201',541,'บ้านกาด','50360',NULL,NULL),(4288,'502202',541,'ทุ่งปี้','50360',NULL,NULL),(4289,'502203',541,'ทุ่งรวงทอง','50360',NULL,NULL),(4290,'502204',541,'แม่วิน','50360',NULL,NULL),(4291,'502205',541,'ดอนเปา','50360',NULL,NULL),(4292,'502301',542,'ออนเหนือ','50130',NULL,NULL),(4293,'502302',542,'ออนกลาง','50130',NULL,NULL),(4294,'502303',542,'บ้านสหกรณ์','50130',NULL,NULL),(4295,'502304',542,'ห้วยแก้ว','50130',NULL,NULL),(4296,'502305',542,'แม่ทา','50130',NULL,NULL),(4297,'502306',542,'ทาเหนือ','50130',NULL,NULL),(4298,'502401',543,'ดอยหล่อ','50160',NULL,NULL),(4299,'502402',543,'สองแคว','50160',NULL,NULL),(4300,'502403',543,'ยางคราม','50160',NULL,NULL),(4301,'502404',543,'สันติสุข','50160',NULL,NULL),(4302,'502501',544,'บ้านจันทร์','0',NULL,NULL),(4303,'502502',544,'แม่แดด','0',NULL,NULL),(4304,'502503',544,'แจ่มหลวง','0',NULL,NULL),(4305,'510101',545,'ในเมือง','51000',NULL,NULL),(4306,'510102',545,'เหมืองง่า','51000',NULL,NULL),(4307,'510103',545,'อุโมงค์','51150',NULL,NULL),(4308,'510104',545,'หนองช้างคืน','51150',NULL,NULL),(4309,'510105',545,'ประตูป่า','51000',NULL,NULL),(4310,'510106',545,'ริมปิง','51000',NULL,NULL),(4311,'510107',545,'ต้นธง','51000',NULL,NULL),(4312,'510108',545,'บ้านแป้น','51000',NULL,NULL),(4313,'510109',545,'เหมืองจี้','51000',NULL,NULL),(4314,'510110',545,'ป่าสัก','51000',NULL,NULL),(4315,'510111',545,'เวียงยอง','51000',NULL,NULL),(4316,'510112',545,'บ้านกลาง','51000',NULL,NULL),(4317,'510113',545,'มะเขือแจ้','51000',NULL,NULL),(4318,'510116',545,'ศรีบัวบาน','51000',NULL,NULL),(4319,'510117',545,'หนองหนาม','51000',NULL,NULL),(4320,'510201',546,'ทาปลาดุก','51140',NULL,NULL),(4321,'510202',546,'ทาสบเส้า','51140',NULL,NULL),(4322,'510203',546,'ทากาศ','51170',NULL,NULL),(4323,'510204',546,'ทาขุมเงิน','51170',NULL,NULL),(4324,'510205',546,'ทาทุ่งหลวง','51170',NULL,NULL),(4325,'510206',546,'ทาแม่ลอบ','51170',NULL,NULL),(4326,'510301',547,'บ้านโฮ่ง','51130',NULL,NULL),(4327,'510302',547,'ป่าพลู','51130',NULL,NULL),(4328,'510303',547,'เหล่ายาว','51130',NULL,NULL),(4329,'510304',547,'ศรีเตี้ย','51130',NULL,NULL),(4330,'510305',547,'หนองปลาสะวาย','51130',NULL,NULL),(4331,'510401',548,'ลี้','51110',NULL,NULL),(4332,'510402',548,'แม่ตืน','51110',NULL,NULL),(4333,'510403',548,'นาทราย','51110',NULL,NULL),(4334,'510404',548,'ดงดำ','51110',NULL,NULL),(4335,'510405',548,'ก้อ','51110',NULL,NULL),(4336,'510406',548,'แม่ลาน','51110',NULL,NULL),(4337,'510408',548,'ป่าไผ่','51110',NULL,NULL),(4338,'510409',548,'ศรีวิชัย','51110',NULL,NULL),(4339,'510501',549,'ทุ่งหัวช้าง','51160',NULL,NULL),(4340,'510502',549,'บ้านปวง','51160',NULL,NULL),(4341,'510503',549,'ตะเคียนปม','51160',NULL,NULL),(4342,'510601',550,'ปากบ่อง','51120',NULL,NULL),(4343,'510602',550,'ป่าซาง','51120',NULL,NULL),(4344,'510603',550,'แม่แรง','51120',NULL,NULL),(4345,'510604',550,'ม่วงน้อย','51120',NULL,NULL),(4346,'510605',550,'บ้านเรือน','51120',NULL,NULL),(4347,'510606',550,'มะกอก','51120',NULL,NULL),(4348,'510607',550,'ท่าตุ้ม','51120',NULL,NULL),(4349,'510608',550,'น้ำดิบ','51120',NULL,NULL),(4350,'510611',550,'นครเจดีย์','51120',NULL,NULL),(4351,'510701',551,'บ้านธิ','51180',NULL,NULL),(4352,'510702',551,'ห้วยยาบ','51180',NULL,NULL),(4353,'510801',552,'หนองล่อง','51120',NULL,NULL),(4354,'510802',552,'หนองยวง','51120',NULL,NULL),(4355,'510803',552,'วังผาง','51120',NULL,NULL),(4356,'520101',553,'เวียงเหนือ','52000',NULL,NULL),(4357,'520105',553,'พระบาท','52000',NULL,NULL),(4358,'520106',553,'ชมพู','52100',NULL,NULL),(4359,'520107',553,'กล้วยแพะ','52000',NULL,NULL),(4360,'520108',553,'ปงแสนทอง','52100',NULL,NULL),(4361,'520109',553,'บ้านแลง','52000',NULL,NULL),(4362,'520110',553,'บ้านเสด็จ','52000',NULL,NULL),(4363,'520111',553,'พิชัย','52000',NULL,NULL),(4364,'520112',553,'ทุ่งฝาย','52000',NULL,NULL),(4365,'520113',553,'บ้านเอื้อม','52100',NULL,NULL),(4366,'520114',553,'บ้านเป้า','52100',NULL,NULL),(4367,'520115',553,'บ้านค่า','52100',NULL,NULL),(4368,'520116',553,'บ่อแฮ้ว','52100',NULL,NULL),(4369,'520117',553,'ต้นธงชัย','52000',NULL,NULL),(4370,'520118',553,'นิคมพัฒนา','52000',NULL,NULL),(4371,'520119',553,'บุญนาคพัฒนา','52000',NULL,NULL),(4372,'520201',554,'บ้านดง','52220',NULL,NULL),(4373,'520202',554,'นาสัก','52220',NULL,NULL),(4374,'520203',554,'จางเหนือ','52220',NULL,NULL),(4375,'520204',554,'แม่เมาะ','52220',NULL,NULL),(4376,'520205',554,'สบป้าน','52220',NULL,NULL),(4377,'520301',555,'ลำปางหลวง','52130',NULL,NULL),(4378,'520302',555,'นาแก้ว','52130',NULL,NULL),(4379,'520303',555,'ไหล่หิน','52130',NULL,NULL),(4380,'520304',555,'วังพร้าว','52130',NULL,NULL),(4381,'520305',555,'ศาลา','52130',NULL,NULL),(4382,'520306',555,'เกาะคา','52130',NULL,NULL),(4383,'520307',555,'นาแส่ง','52130',NULL,NULL),(4384,'520308',555,'ท่าผา','52130',NULL,NULL),(4385,'520309',555,'ใหม่พัฒนา','52130',NULL,NULL),(4386,'520401',556,'ทุ่งงาม','52210',NULL,NULL),(4387,'520402',556,'เสริมขวา','52210',NULL,NULL),(4388,'520403',556,'เสริมซ้าย','52210',NULL,NULL),(4389,'520404',556,'เสริมกลาง','52210',NULL,NULL),(4390,'520501',557,'หลวงเหนือ','52110',NULL,NULL),(4391,'520502',557,'หลวงใต้','52110',NULL,NULL),(4392,'520503',557,'บ้านโป่ง','52110',NULL,NULL),(4393,'520504',557,'บ้านร้อง','52110',NULL,NULL),(4394,'520505',557,'ปงเตา','52110',NULL,NULL),(4395,'520506',557,'นาแก','52110',NULL,NULL),(4396,'520507',557,'บ้านอ้อน','52110',NULL,NULL),(4397,'520508',557,'บ้านแหง','52110',NULL,NULL),(4398,'520509',557,'บ้านหวด','52110',NULL,NULL),(4399,'520510',557,'แม่ตีบ','52110',NULL,NULL),(4400,'520601',558,'แจ้ห่ม','52120',NULL,NULL),(4401,'520602',558,'บ้านสา','52120',NULL,NULL),(4402,'520603',558,'ปงดอน','52120',NULL,NULL),(4403,'520604',558,'แม่สุก','52120',NULL,NULL),(4404,'520605',558,'เมืองมาย','52120',NULL,NULL),(4405,'520606',558,'ทุ่งผึ้ง','52120',NULL,NULL),(4406,'520607',558,'วิเชตนคร','52120',NULL,NULL),(4407,'520701',559,'ทุ่งฮั้ว','52140',NULL,NULL),(4408,'520702',559,'วังเหนือ','52140',NULL,NULL),(4409,'520703',559,'วังใต้','52140',NULL,NULL),(4410,'520704',559,'ร่องเคาะ','52140',NULL,NULL),(4411,'520705',559,'วังทอง','52140',NULL,NULL),(4412,'520706',559,'วังซ้าย','52140',NULL,NULL),(4413,'520707',559,'วังแก้ว','52140',NULL,NULL),(4414,'520708',559,'วังทรายคำ','52140',NULL,NULL),(4415,'520801',560,'ล้อมแรด','52160',NULL,NULL),(4416,'520802',560,'แม่วะ','52230',NULL,NULL),(4417,'520803',560,'แม่ปะ','52160',NULL,NULL),(4418,'520804',560,'แม่มอก','52160',NULL,NULL),(4419,'520805',560,'เวียงมอก','52160',NULL,NULL),(4420,'520806',560,'นาโป่ง','52160',NULL,NULL),(4421,'520807',560,'แม่ถอด','52160',NULL,NULL),(4422,'520808',560,'เถินบุรี','52160',NULL,NULL),(4423,'520901',561,'แม่พริก','52180',NULL,NULL),(4424,'520902',561,'ผาปัง','52180',NULL,NULL),(4425,'520903',561,'แม่ปุ','52180',NULL,NULL),(4426,'520904',561,'พระบาทวังตวง','52180',NULL,NULL),(4427,'521001',562,'แม่ทะ','52150',NULL,NULL),(4428,'521002',562,'นาครัว','52150',NULL,NULL),(4429,'521003',562,'ป่าตัน','52150',NULL,NULL),(4430,'521004',562,'บ้านกิ่ว','52150',NULL,NULL),(4431,'521005',562,'บ้านบอม','52150',NULL,NULL),(4432,'521006',562,'น้ำโจ้','52150',NULL,NULL),(4433,'521007',562,'ดอนไฟ','52150',NULL,NULL),(4434,'521008',562,'หัวเสือ','52150',NULL,NULL),(4435,'521010',562,'วังเงิน','52150',NULL,NULL),(4436,'521011',562,'สันดอนแก้ว','52150',NULL,NULL),(4437,'521101',563,'สบปราบ','52170',NULL,NULL),(4438,'521102',563,'สมัย','52170',NULL,NULL),(4439,'521103',563,'แม่กัวะ','52170',NULL,NULL),(4440,'521104',563,'นายาง','52170',NULL,NULL),(4441,'521201',564,'ห้างฉัตร','52190',NULL,NULL),(4442,'521202',564,'หนองหล่ม','52190',NULL,NULL),(4443,'521203',564,'เมืองยาว','52190',NULL,NULL),(4444,'521204',564,'ปงยางคก','52190',NULL,NULL),(4445,'521205',564,'เวียงตาล','52190',NULL,NULL),(4446,'521206',564,'แม่สัน','52190',NULL,NULL),(4447,'521207',564,'วอแก้ว','52190',NULL,NULL),(4448,'521301',565,'เมืองปาน','52240',NULL,NULL),(4449,'521302',565,'บ้านขอ','52240',NULL,NULL),(4450,'521303',565,'ทุ่งกว๋าว','52240',NULL,NULL),(4451,'521304',565,'แจ้ซ้อน','52240',NULL,NULL),(4452,'521305',565,'หัวเมือง','52240',NULL,NULL),(4453,'530101',566,'ท่าอิฐ','53000',NULL,NULL),(4454,'530102',566,'ท่าเสา','53000',NULL,NULL),(4455,'530103',566,'บ้านเกาะ','53000',NULL,NULL),(4456,'530104',566,'ป่าเซ่า','53000',NULL,NULL),(4457,'530105',566,'คุ้งตะเภา','53000',NULL,NULL),(4458,'530106',566,'วังกะพี้','53170',NULL,NULL),(4459,'530107',566,'หาดกรวด','53000',NULL,NULL),(4460,'530108',566,'น้ำริด','53000',NULL,NULL),(4461,'530109',566,'งิ้วงาม','53000',NULL,NULL),(4462,'530110',566,'บ้านด่านนาขาม','53000',NULL,NULL),(4463,'530111',566,'บ้านด่าน','53000',NULL,NULL),(4464,'530112',566,'ผาจุก','53000',NULL,NULL),(4465,'530113',566,'วังดิน','53000',NULL,NULL),(4466,'530114',566,'แสนตอ','53000',NULL,NULL),(4467,'530115',566,'หาดงิ้ว','53000',NULL,NULL),(4468,'530116',566,'ขุนฝาง','53000',NULL,NULL),(4469,'530117',566,'ถ้ำฉลอง','53000',NULL,NULL),(4470,'530201',567,'วังแดง','53140',NULL,NULL),(4471,'530202',567,'บ้านแก่ง','53140',NULL,NULL),(4472,'530203',567,'หาดสองแคว','53140',NULL,NULL),(4473,'530204',567,'น้ำอ่าง','53140',NULL,NULL),(4474,'530205',567,'ข่อยสูง','53140',NULL,NULL),(4475,'530301',568,'ท่าปลา','53150',NULL,NULL),(4476,'530302',568,'หาดล้า','53150',NULL,NULL),(4477,'530303',568,'ผาเลือด','53190',NULL,NULL),(4478,'530304',568,'จริม','53150',NULL,NULL),(4479,'530305',568,'น้ำหมัน','53150',NULL,NULL),(4480,'530306',568,'ท่าแฝก','53110',NULL,NULL),(4481,'530307',568,'นางพญา','53150',NULL,NULL),(4482,'530308',568,'ร่วมจิต','53190',NULL,NULL),(4483,'530401',569,'แสนตอ','53110',NULL,NULL),(4484,'530402',569,'บ้านฝาย','53110',NULL,NULL),(4485,'530403',569,'เด่นเหล็ก','53110',NULL,NULL),(4486,'530404',569,'น้ำไคร้','53110',NULL,NULL),(4487,'530405',569,'น้ำไฝ','53110',NULL,NULL),(4488,'530406',569,'ห้วยมุ่น','53110',NULL,NULL),(4489,'530501',570,'ฟากท่า','53160',NULL,NULL),(4490,'530502',570,'สองคอน','53160',NULL,NULL),(4491,'530503',570,'บ้านเสี้ยว','53160',NULL,NULL),(4492,'530504',570,'สองห้อง','53160',NULL,NULL),(4493,'530601',571,'ม่วงเจ็ดต้น','53180',NULL,NULL),(4494,'530602',571,'บ้านโคก','53180',NULL,NULL),(4495,'530603',571,'นาขุม','53180',NULL,NULL),(4496,'530604',571,'บ่อเบี้ย','53180',NULL,NULL),(4497,'530701',572,'ในเมือง','53120',NULL,NULL),(4498,'530702',572,'บ้านดารา','53220',NULL,NULL),(4499,'530703',572,'ไร่อ้อย','53120',NULL,NULL),(4500,'530704',572,'ท่าสัก','53220',NULL,NULL),(4501,'530705',572,'คอรุม','53120',NULL,NULL),(4502,'530706',572,'บ้านหม้อ','53120',NULL,NULL),(4503,'530707',572,'ท่ามะเฟือง','53120',NULL,NULL),(4504,'530708',572,'บ้านโคน','53120',NULL,NULL),(4505,'530709',572,'พญาแมน','53120',NULL,NULL),(4506,'530710',572,'นาอิน','53120',NULL,NULL),(4507,'530711',572,'นายาง','53120',NULL,NULL),(4508,'530801',573,'ศรีพนมมาศ','53130',NULL,NULL),(4509,'530802',573,'แม่พูล','53130',NULL,NULL),(4510,'530803',573,'นานกกก','53130',NULL,NULL),(4511,'530804',573,'ฝายหลวง','53130',NULL,NULL),(4512,'530805',573,'ชัยจุมพล','53130',NULL,NULL),(4513,'530806',573,'ไผ่ล้อม','53210',NULL,NULL),(4514,'530807',573,'ทุ่งยั้ง','53210',NULL,NULL),(4515,'530808',573,'ด่านแม่คำมัน','53210',NULL,NULL),(4516,'530901',574,'ผักขวง','53230',NULL,NULL),(4517,'530902',574,'บ่อทอง','53230',NULL,NULL),(4518,'530903',574,'ป่าคาย','53230',NULL,NULL),(4519,'530904',574,'น้ำพี้','53230',NULL,NULL),(4520,'540101',575,'ในเวียง','54000',NULL,NULL),(4521,'540102',575,'นาจักร','54000',NULL,NULL),(4522,'540103',575,'นาชำ','54000',NULL,NULL),(4523,'540104',575,'ป่าแดง','54000',NULL,NULL),(4524,'540105',575,'ทุ่งโฮ้ง','54000',NULL,NULL),(4525,'540106',575,'เหมืองหม้อ','54000',NULL,NULL),(4526,'540107',575,'วังธง','54000',NULL,NULL),(4527,'540108',575,'แม่หล่าย','54000',NULL,NULL),(4528,'540109',575,'ห้วยม้า','54000',NULL,NULL),(4529,'540110',575,'ป่าแมต','54000',NULL,NULL),(4530,'540111',575,'บ้านถิ่น','54000',NULL,NULL),(4531,'540112',575,'สวนเขื่อน','54000',NULL,NULL),(4532,'540113',575,'วังหงษ์','54000',NULL,NULL),(4533,'540114',575,'แม่คำมี','54000',NULL,NULL),(4534,'540115',575,'ทุ่งกวาว','54000',NULL,NULL),(4535,'540116',575,'ท่าข้าม','54000',NULL,NULL),(4536,'540117',575,'แม่ยม','54000',NULL,NULL),(4537,'540118',575,'ช่อแฮ','54000',NULL,NULL),(4538,'540119',575,'ร่องฟอง','54000',NULL,NULL),(4539,'540120',575,'กาญจนา','54000',NULL,NULL),(4540,'540201',576,'ร้องกวาง','54140',NULL,NULL),(4541,'540204',576,'ร้องเข็ม','54140',NULL,NULL),(4542,'540205',576,'น้ำเลา','54140',NULL,NULL),(4543,'540206',576,'บ้านเวียง','54140',NULL,NULL),(4544,'540207',576,'ทุ่งศรี','54140',NULL,NULL),(4545,'540208',576,'แม่ยางตาล','54140',NULL,NULL),(4546,'540209',576,'แม่ยางฮ่อ','54140',NULL,NULL),(4547,'540210',576,'ไผ่โทน','54140',NULL,NULL),(4548,'540213',576,'ห้วยโรง','54140',NULL,NULL),(4549,'540214',576,'แม่ทราย','54140',NULL,NULL),(4550,'540215',576,'แม่ยางร้อง','54140',NULL,NULL),(4551,'540301',577,'ห้วยอ้อ','54150',NULL,NULL),(4552,'540302',577,'บ้านปิน','54150',NULL,NULL),(4553,'540303',577,'ต้าผามอก','54150',NULL,NULL),(4554,'540304',577,'เวียงต้า','54150',NULL,NULL),(4555,'540305',577,'ปากกาง','54150',NULL,NULL),(4556,'540306',577,'หัวทุ่ง','54150',NULL,NULL),(4557,'540307',577,'ทุ่งแล้ง','54150',NULL,NULL),(4558,'540308',577,'บ่อเหล็กลอง','54150',NULL,NULL),(4559,'540309',577,'แม่ปาน','54150',NULL,NULL),(4560,'540401',578,'สูงเม่น','54130',NULL,NULL),(4561,'540402',578,'น้ำชำ','54130',NULL,NULL),(4562,'540403',578,'หัวฝาย','54130',NULL,NULL),(4563,'540404',578,'ดอนมูล','54130',NULL,NULL),(4564,'540405',578,'บ้านเหล่า','54130',NULL,NULL),(4565,'540406',578,'บ้านกวาง','54130',NULL,NULL),(4566,'540407',578,'บ้านปง','54130',NULL,NULL),(4567,'540408',578,'บ้านกาศ','54130',NULL,NULL),(4568,'540409',578,'ร่องกาศ','54130',NULL,NULL),(4569,'540410',578,'สบสาย','54130',NULL,NULL),(4570,'540411',578,'เวียงทอง','54000',NULL,NULL),(4571,'540412',578,'พระหลวง','54130',NULL,NULL),(4572,'540501',579,'เด่นชัย','54110',NULL,NULL),(4573,'540502',579,'แม่จั๊วะ','54110',NULL,NULL),(4574,'540503',579,'ไทรย้อย','54110',NULL,NULL),(4575,'540504',579,'ห้วยไร่','54110',NULL,NULL),(4576,'540505',579,'ปงป่าหวาย','54110',NULL,NULL),(4577,'540601',580,'บ้านหนุน','54120',NULL,NULL),(4578,'540602',580,'บ้านกลาง','54120',NULL,NULL),(4579,'540603',580,'ห้วยหม้าย','54120',NULL,NULL),(4580,'540604',580,'เตาปูน','54120',NULL,NULL),(4581,'540605',580,'หัวเมือง','54120',NULL,NULL),(4582,'540606',580,'สะเอียบ','54120',NULL,NULL),(4583,'540607',580,'แดนชุมพล','54120',NULL,NULL),(4584,'540608',580,'ทุ่งน้าว','54120',NULL,NULL),(4585,'540701',581,'วังชิ้น','54160',NULL,NULL),(4586,'540702',581,'สรอย','54160',NULL,NULL),(4587,'540703',581,'แม่ป้าก','54160',NULL,NULL),(4588,'540704',581,'นาพูน','54160',NULL,NULL),(4589,'540705',581,'แม่พุง','54160',NULL,NULL),(4590,'540706',581,'ป่าสัก','54160',NULL,NULL),(4591,'540707',581,'แม่เกิ๋ง','54160',NULL,NULL),(4592,'540801',582,'แม่คำมี','54170',NULL,NULL),(4593,'540802',582,'หนองม่วงไข่','54170',NULL,NULL),(4594,'540803',582,'น้ำรัด','54170',NULL,NULL),(4595,'540804',582,'วังหลวง','54170',NULL,NULL),(4596,'540805',582,'ตำหนักธรรม','54170',NULL,NULL),(4597,'540806',582,'ทุ่งแค้ว','54170',NULL,NULL),(4598,'550101',583,'ในเวียง','55000',NULL,NULL),(4599,'550102',583,'บ่อ','55000',NULL,NULL),(4600,'550103',583,'ผาสิงห์','55000',NULL,NULL),(4601,'550104',583,'ไชยสถาน','55000',NULL,NULL),(4602,'550105',583,'ถืมตอง','55000',NULL,NULL),(4603,'550106',583,'เรือง','55000',NULL,NULL),(4604,'550107',583,'นาชาว','55000',NULL,NULL),(4605,'550108',583,'ดู่ใต้','55000',NULL,NULL),(4606,'550109',583,'กองควาย','55000',NULL,NULL),(4607,'550116',583,'สวก','55000',NULL,NULL),(4608,'550117',583,'สะเนียน','55000',NULL,NULL),(4609,'550202',584,'หนองแดง','55170',NULL,NULL),(4610,'550203',584,'หมอเมือง','55170',NULL,NULL),(4611,'550204',584,'น้ำพาง','55170',NULL,NULL),(4612,'550205',584,'น้ำปาย','55170',NULL,NULL),(4613,'550206',584,'แม่จริม','55170',NULL,NULL),(4614,'550301',585,'บ้านฟ้า','55190',NULL,NULL),(4615,'550302',585,'ป่าคาหลวง','55190',NULL,NULL),(4616,'550303',585,'สวด','55190',NULL,NULL),(4617,'550304',585,'บ้านพี้','55190',NULL,NULL),(4618,'550401',586,'นาน้อย','55150',NULL,NULL),(4619,'550402',586,'เชียงของ','55150',NULL,NULL),(4620,'550403',586,'ศรีษะเกษ','55150',NULL,NULL),(4621,'550404',586,'สถาน','55150',NULL,NULL),(4622,'550405',586,'สันทะ','55150',NULL,NULL),(4623,'550406',586,'บัวใหญ่','55150',NULL,NULL),(4624,'550407',586,'น้ำตก','55150',NULL,NULL),(4625,'550501',587,'ปัว','55120',NULL,NULL),(4626,'550502',587,'แงง','55120',NULL,NULL),(4627,'550503',587,'สถาน','55120',NULL,NULL),(4628,'550504',587,'ศิลาแลง','55120',NULL,NULL),(4629,'550505',587,'ศิลาเพชร','55120',NULL,NULL),(4630,'550506',587,'อวน','55120',NULL,NULL),(4631,'550509',587,'ไชยวัฒนา','55120',NULL,NULL),(4632,'550510',587,'เจดียชัย','55120',NULL,NULL),(4633,'550511',587,'ภูคา','55120',NULL,NULL),(4634,'550512',587,'สกาด','55120',NULL,NULL),(4635,'550513',587,'ป่ากลาง','55120',NULL,NULL),(4636,'550514',587,'วรนคร','55120',NULL,NULL),(4637,'550601',588,'ริม','55140',NULL,NULL),(4638,'550602',588,'ป่าคา','55140',NULL,NULL),(4639,'550603',588,'ผาตอ','55140',NULL,NULL),(4640,'550604',588,'ยม','55140',NULL,NULL),(4641,'550605',588,'ตาลชุม','55140',NULL,NULL),(4642,'550606',588,'ศรีภูมิ','55140',NULL,NULL),(4643,'550607',588,'จอมพระ','55140',NULL,NULL),(4644,'550608',588,'แสนทอง','55140',NULL,NULL),(4645,'550609',588,'ท่าวังผา','55140',NULL,NULL),(4646,'550610',588,'ผาทอง','55140',NULL,NULL),(4647,'550701',589,'กลางเวียง','55110',NULL,NULL),(4648,'550702',589,'ขึ่ง','55110',NULL,NULL),(4649,'550703',589,'ไหล่น่าน','55110',NULL,NULL),(4650,'550704',589,'ตาลชุม','55110',NULL,NULL),(4651,'550705',589,'นาเหลือง','55110',NULL,NULL),(4652,'550706',589,'ส้าน','55110',NULL,NULL),(4653,'550707',589,'น้ำมวบ','55110',NULL,NULL),(4654,'550708',589,'น้ำปั้ว','55110',NULL,NULL),(4655,'550709',589,'ยาบหัวนา','55110',NULL,NULL),(4656,'550710',589,'ปงสนุก','55110',NULL,NULL),(4657,'550711',589,'อ่ายนาไลย','55110',NULL,NULL),(4658,'550712',589,'ส้านนาหนองใหม่','55110',NULL,NULL),(4659,'550713',589,'แม่ขะนิง','55110',NULL,NULL),(4660,'550714',589,'แม่สาคร','55110',NULL,NULL),(4661,'550715',589,'จอมจันทร์','55110',NULL,NULL),(4662,'550716',589,'แม่สา','55110',NULL,NULL),(4663,'550717',589,'ทุ่งศรีทอง','55110',NULL,NULL),(4664,'550801',590,'ปอน','55130',NULL,NULL),(4665,'550802',590,'งอบ','55130',NULL,NULL),(4666,'550803',590,'และ','55130',NULL,NULL),(4667,'550804',590,'ทุ่งช้าง','55130',NULL,NULL),(4668,'550901',591,'เชียงกลาง','55160',NULL,NULL),(4669,'550902',591,'เปือ','55160',NULL,NULL),(4670,'550903',591,'เชียงคาน','55160',NULL,NULL),(4671,'550904',591,'พระธาตุ','55160',NULL,NULL),(4672,'550908',591,'พญาแก้ว','55160',NULL,NULL),(4673,'550909',591,'พระพุทธบาท','55160',NULL,NULL),(4674,'551001',592,'นาทะนุง','55180',NULL,NULL),(4675,'551002',592,'บ่อแก้ว','55180',NULL,NULL),(4676,'551003',592,'เมืองลี่','55180',NULL,NULL),(4677,'551004',592,'ปิงหลวง','55180',NULL,NULL),(4678,'551101',593,'ดู่พงษ์','55210',NULL,NULL),(4679,'551102',593,'ป่าแลวหลวง','55210',NULL,NULL),(4680,'551103',593,'พงษ์','55210',NULL,NULL),(4681,'551201',594,'บ่อเกลือเหนือ','55220',NULL,NULL),(4682,'551202',594,'บ่อเกลือใต้','55220',NULL,NULL),(4683,'551204',594,'ภูฟ้า','55220',NULL,NULL),(4684,'551205',594,'ดงพญา','55220',NULL,NULL),(4685,'551301',595,'นาไร่หลวง','55160',NULL,NULL),(4686,'551302',595,'ชนแดน','55160',NULL,NULL),(4687,'551303',595,'ยอด','55160',NULL,NULL),(4688,'551401',596,'ม่วงตึ๊ด','55000',NULL,NULL),(4689,'551402',596,'นาปัง','55000',NULL,NULL),(4690,'551403',596,'น้ำแก่น','55000',NULL,NULL),(4691,'551404',596,'น้ำเกี๋ยน','55000',NULL,NULL),(4692,'551405',596,'เมืองจัง','55000',NULL,NULL),(4693,'551406',596,'ท่าน้าว','55000',NULL,NULL),(4694,'551407',596,'ฝายแก้ว','55000',NULL,NULL),(4695,'551501',597,'ห้วยโก๋น','55130',NULL,NULL),(4696,'551502',597,'ขุนน่าน','55130',NULL,NULL),(4697,'560101',598,'เวียง','56000',NULL,NULL),(4698,'560102',598,'แม่ต๋ำ','56000',NULL,NULL),(4699,'560104',598,'แม่นาเรือ','56000',NULL,NULL),(4700,'560105',598,'บ้านตุ่น','56000',NULL,NULL),(4701,'560106',598,'บ้านต๊ำ','56000',NULL,NULL),(4702,'560107',598,'บ้านต๋อม','56000',NULL,NULL),(4703,'560108',598,'แม่ปีม','56000',NULL,NULL),(4704,'560110',598,'แม่กา','56000',NULL,NULL),(4705,'560111',598,'บ้านใหม่','56000',NULL,NULL),(4706,'560112',598,'จำป่าหวาย','56000',NULL,NULL),(4707,'560113',598,'ท่าวังทอง','56000',NULL,NULL),(4708,'560114',598,'แม่ใส','56000',NULL,NULL),(4709,'560115',598,'บ้านสาง','56000',NULL,NULL),(4710,'560116',598,'ท่าจำปี','56000',NULL,NULL),(4711,'560118',598,'สันป่าม่วง','56000',NULL,NULL),(4712,'560201',599,'ห้วยข้าวก่ำ','56150',NULL,NULL),(4713,'560202',599,'จุน','56150',NULL,NULL),(4714,'560203',599,'ลอ','56150',NULL,NULL),(4715,'560204',599,'หงส์หิน','56150',NULL,NULL),(4716,'560205',599,'ทุ่งรวงทอง','56150',NULL,NULL),(4717,'560206',599,'ห้วยยางขาม','56150',NULL,NULL),(4718,'560207',599,'พระธาตุขิงแกง','56150',NULL,NULL),(4719,'560301',600,'หย่วน','56110',NULL,NULL),(4720,'560306',600,'น้ำแวน','56110',NULL,NULL),(4721,'560307',600,'เวียง','56110',NULL,NULL),(4722,'560308',600,'ฝายกวาง','56110',NULL,NULL),(4723,'560309',600,'เจดีย์คำ','56110',NULL,NULL),(4724,'560310',600,'ร่มเย็น','56110',NULL,NULL),(4725,'560311',600,'เชียงบาน','56110',NULL,NULL),(4726,'560312',600,'แม่ลาว','56110',NULL,NULL),(4727,'560313',600,'อ่างทอง','56110',NULL,NULL),(4728,'560314',600,'ทุ่งผาสุข','56110',NULL,NULL),(4729,'560401',601,'เชียงม่วน','56160',NULL,NULL),(4730,'560402',601,'บ้านมาง','56160',NULL,NULL),(4731,'560403',601,'สระ','56160',NULL,NULL),(4732,'560501',602,'ดอกคำใต้','56120',NULL,NULL),(4733,'560502',602,'ดอนศรีชุม','56120',NULL,NULL),(4734,'560503',602,'บ้านถ้ำ','56120',NULL,NULL),(4735,'560504',602,'บ้านปิน','56120',NULL,NULL),(4736,'560505',602,'ห้วยลาน','56120',NULL,NULL),(4737,'560506',602,'สันโค้ง','56120',NULL,NULL),(4738,'560507',602,'ป่าซาง','56120',NULL,NULL),(4739,'560508',602,'หนองหล่ม','56120',NULL,NULL),(4740,'560509',602,'ดงสุวรรณ','56120',NULL,NULL),(4741,'560510',602,'บุญเกิด','56120',NULL,NULL),(4742,'560511',602,'สว่างอารมณ์','56120',NULL,NULL),(4743,'560512',602,'คือเวียง','56120',NULL,NULL),(4744,'560601',603,'ปง','56140',NULL,NULL),(4745,'560602',603,'ควร','56140',NULL,NULL),(4746,'560603',603,'ออย','56140',NULL,NULL),(4747,'560604',603,'งิม','56140',NULL,NULL),(4748,'560605',603,'ผาช้างน้อย','56140',NULL,NULL),(4749,'560606',603,'นาปรัง','56140',NULL,NULL),(4750,'560607',603,'ขุนควร','56140',NULL,NULL),(4751,'560701',604,'แม่ใจ','56130',NULL,NULL),(4752,'560702',604,'ศรีถ้อย','56130',NULL,NULL),(4753,'560703',604,'แม่สุก','56130',NULL,NULL),(4754,'560704',604,'ป่าแฝก','56130',NULL,NULL),(4755,'560705',604,'บ้านเหล่า','56130',NULL,NULL),(4756,'560706',604,'เจริญราษฎร์','56130',NULL,NULL),(4757,'560801',605,'ภูซาง','56110',NULL,NULL),(4758,'560802',605,'ป่าสัก','56110',NULL,NULL),(4759,'560803',605,'ทุ่งกล้วย','56110',NULL,NULL),(4760,'560804',605,'เชียงแรง','56110',NULL,NULL),(4761,'560805',605,'สบบง','56110',NULL,NULL),(4762,'560901',606,'ห้วยแก้ว','56000',NULL,NULL),(4763,'560902',606,'ดงเจน','56000',NULL,NULL),(4764,'560903',606,'แม่อิง','56000',NULL,NULL),(4765,'570101',607,'เวียง','57000',NULL,NULL),(4766,'570102',607,'รอบเวียง','57000',NULL,NULL),(4767,'570103',607,'บ้านดู่','57100',NULL,NULL),(4768,'570104',607,'นางแล','57100',NULL,NULL),(4769,'570105',607,'แม่ข้าวต้ม','57100',NULL,NULL),(4770,'570106',607,'แม่ยาว','57100',NULL,NULL),(4771,'570107',607,'สันทราย','57000',NULL,NULL),(4772,'570111',607,'แม่กรณ์','57000',NULL,NULL),(4773,'570112',607,'ห้วยชมภู','57000',NULL,NULL),(4774,'570113',607,'ห้วยสัก','57000',NULL,NULL),(4775,'570114',607,'ริมกก','57100',NULL,NULL),(4776,'570115',607,'ดอยลาน','57000',NULL,NULL),(4777,'570116',607,'ป่าอ้อดอนชัย','57000',NULL,NULL),(4778,'570118',607,'ท่าสาย','57000',NULL,NULL),(4779,'570120',607,'ดอยฮาง','57000',NULL,NULL),(4780,'570202',608,'เวียงชัย','57210',NULL,NULL),(4781,'570203',608,'ผางาม','57210',NULL,NULL),(4782,'570204',608,'เวียงเหนือ','57210',NULL,NULL),(4783,'570206',608,'ดอนศิลา','57210',NULL,NULL),(4784,'570208',608,'เมืองชุม','57210',NULL,NULL),(4785,'570301',609,'เวียง','57140',NULL,NULL),(4786,'570302',609,'สถาน','57140',NULL,NULL),(4787,'570303',609,'ครึ่ง','57140',NULL,NULL),(4788,'570304',609,'บุญเรือง','57140',NULL,NULL),(4789,'570305',609,'ห้วยซ้อ','57140',NULL,NULL),(4790,'570308',609,'ศรีดอนชัย','57230',NULL,NULL),(4791,'570310',609,'ริมโขง','57140',NULL,NULL),(4792,'570401',610,'เวียง','57160',NULL,NULL),(4793,'570402',610,'งิ้ว','57160',NULL,NULL),(4794,'570403',610,'ปล้อง','57230',NULL,NULL),(4795,'570404',610,'แม่ลอย','57230',NULL,NULL),(4796,'570405',610,'เชียงเคี่ยน','57230',NULL,NULL),(4797,'570409',610,'ตับเต่า','57160',NULL,NULL),(4798,'570410',610,'หงาว','57160',NULL,NULL),(4799,'570411',610,'สันทรายงาม','57160',NULL,NULL),(4800,'570412',610,'ศรีดอนไชย','57160',NULL,NULL),(4801,'570413',610,'หนองแรด','57160',NULL,NULL),(4802,'570501',611,'สันมะเค็ด','57120',NULL,NULL),(4803,'570502',611,'แม่อ้อ','57120',NULL,NULL),(4804,'570503',611,'ธารทอง','57250',NULL,NULL),(4805,'570504',611,'สันติสุข','57120',NULL,NULL),(4806,'570505',611,'ดอยงาม','57120',NULL,NULL),(4807,'570506',611,'หัวง้ม','57120',NULL,NULL),(4808,'570507',611,'เจริญเมือง','57120',NULL,NULL),(4809,'570508',611,'ป่าหุ่ง','57120',NULL,NULL),(4810,'570509',611,'ม่วงคำ','57120',NULL,NULL),(4811,'570510',611,'ทรายขาว','57120',NULL,NULL),(4812,'570511',611,'สันกลาง','57120',NULL,NULL),(4813,'570512',611,'แม่เย็น','57280',NULL,NULL),(4814,'570513',611,'เมืองพาน','57120',NULL,NULL),(4815,'570514',611,'ทานตะวัน','57280',NULL,NULL),(4816,'570515',611,'เวียงห้าว','57120',NULL,NULL),(4817,'570601',612,'ป่าแดด','57190',NULL,NULL),(4818,'570602',612,'ป่าแงะ','57190',NULL,NULL),(4819,'570603',612,'สันมะค่า','57190',NULL,NULL),(4820,'570605',612,'โรงช้าง','57190',NULL,NULL),(4821,'570606',612,'ศรีโพธิ์เงิน','57190',NULL,NULL),(4822,'570701',613,'แม่จัน','57110',NULL,NULL),(4823,'570702',613,'จันจว้า','57270',NULL,NULL),(4824,'570703',613,'แม่คำ','57240',NULL,NULL),(4825,'570704',613,'ป่าซาง','57110',NULL,NULL),(4826,'570705',613,'สันทราย','57110',NULL,NULL),(4827,'570706',613,'ท่าข้าวเปลือก','57110',NULL,NULL),(4828,'570708',613,'ป่าตึง','57110',NULL,NULL),(4829,'570710',613,'แม่ไร่','57240',NULL,NULL),(4830,'570711',613,'ศรีค้ำ','57110',NULL,NULL),(4831,'570712',613,'จันจว้าใต้','57270',NULL,NULL),(4832,'570713',613,'จอมสวรรค์','57110',NULL,NULL),(4833,'570801',614,'เวียง','57150',NULL,NULL),(4834,'570802',614,'ป่าสัก','57150',NULL,NULL),(4835,'570803',614,'บ้านแซว','57150',NULL,NULL),(4836,'570804',614,'ศรีดอนมูล','57150',NULL,NULL),(4837,'570805',614,'แม่เงิน','57150',NULL,NULL),(4838,'570806',614,'โยนก','57150',NULL,NULL),(4839,'570901',615,'แม่สาย','57130',NULL,NULL),(4840,'570902',615,'ห้วยไคร้','57220',NULL,NULL),(4841,'570903',615,'เกาะช้าง','57130',NULL,NULL),(4842,'570904',615,'โป่งผา','57130',NULL,NULL),(4843,'570905',615,'ศรีเมืองชุม','57130',NULL,NULL),(4844,'570906',615,'เวียงพางคำ','57130',NULL,NULL),(4845,'570908',615,'บ้านด้าย','57220',NULL,NULL),(4846,'570909',615,'โป่งงาม','57130',NULL,NULL),(4847,'571001',616,'แม่สรวย','57180',NULL,NULL),(4848,'571002',616,'ป่าแดด','57180',NULL,NULL),(4849,'571003',616,'แม่พริก','57180',NULL,NULL),(4850,'571004',616,'ศรีถ้อย','57180',NULL,NULL),(4851,'571005',616,'ท่าก๊อ','57180',NULL,NULL),(4852,'571006',616,'วาวี','57180',NULL,NULL),(4853,'571007',616,'เจดีย์หลวง','57180',NULL,NULL),(4854,'571101',617,'สันสลี','57170',NULL,NULL),(4855,'571102',617,'เวียง','57170',NULL,NULL),(4856,'571103',617,'บ้านโป่ง','57170',NULL,NULL),(4857,'571104',617,'ป่างิ้ว','57170',NULL,NULL),(4858,'571105',617,'เวียงกาหลง','57260',NULL,NULL),(4859,'571106',617,'แม่เจดีย์','57260',NULL,NULL),(4860,'571107',617,'แม่เจดีย์ใหม่','57260',NULL,NULL),(4861,'571201',618,'แม่เปา','57290',NULL,NULL),(4862,'571202',618,'แม่ต๋ำ','57290',NULL,NULL),(4863,'571203',618,'ไม้ยา','57290',NULL,NULL),(4864,'571204',618,'เม็งราย','57290',NULL,NULL),(4865,'571205',618,'ตาดควัน','57290',NULL,NULL),(4866,'571301',619,'ม่วงยาย','57310',NULL,NULL),(4867,'571302',619,'ปอ','57310',NULL,NULL),(4868,'571303',619,'หล่ายงาว','57310',NULL,NULL),(4869,'571304',619,'ท่าข้าม','57310',NULL,NULL),(4870,'571401',620,'ต้า','57340',NULL,NULL),(4871,'571402',620,'ป่าตาล','57340',NULL,NULL),(4872,'571403',620,'ยางฮอม','57340',NULL,NULL),(4873,'571501',621,'เทอดไทย','57240',NULL,NULL),(4874,'571502',621,'แม่สลองใน','57110',NULL,NULL),(4875,'571503',621,'แม่สลองนอก','57110',NULL,NULL),(4876,'571504',621,'แม่ฟ้าหลวง','57240',NULL,NULL),(4877,'571601',622,'ดงมะดะ','57250',NULL,NULL),(4878,'571602',622,'จอมหมอกแก้ว','57250',NULL,NULL),(4879,'571603',622,'บัวสลี','57250',NULL,NULL),(4880,'571604',622,'ป่าก่อดำ','57250',NULL,NULL),(4881,'571605',622,'โป่งแพร่','57000',NULL,NULL),(4882,'571701',623,'ทุ่งก่อ','57210',NULL,NULL),(4883,'571702',623,'ดงมหาวัน','57210',NULL,NULL),(4884,'571703',623,'ป่าซาง','57210',NULL,NULL),(4885,'571801',624,'ปงน้อย','57110',NULL,NULL),(4886,'571802',624,'โชคชัย','57110',NULL,NULL),(4887,'571803',624,'หนองป่าก่อ','57110',NULL,NULL),(4888,'580101',625,'จองคำ','58000',NULL,NULL),(4889,'580102',625,'ห้วยโป่ง','58000',NULL,NULL),(4890,'580103',625,'ผาบ่อง','58000',NULL,NULL),(4891,'580104',625,'ปางหมู','58000',NULL,NULL),(4892,'580105',625,'หมอกจำแป่','58000',NULL,NULL),(4893,'580109',625,'ห้วยปูลิง','58000',NULL,NULL),(4894,'580201',626,'ขุนยวม','58140',NULL,NULL),(4895,'580202',626,'แม่เงา','58140',NULL,NULL),(4896,'580203',626,'เมืองปอน','58140',NULL,NULL),(4897,'580204',626,'แม่ยวมน้อย','58140',NULL,NULL),(4898,'580205',626,'แม่กิ๊','58140',NULL,NULL),(4899,'580206',626,'แม่อูคอ','58140',NULL,NULL),(4900,'580301',627,'เวียงใต้','58130',NULL,NULL),(4901,'580302',627,'เวียงเหนือ','58130',NULL,NULL),(4902,'580303',627,'แม่นาเติง','58130',NULL,NULL),(4903,'580304',627,'แม่ฮี้','58130',NULL,NULL),(4904,'580305',627,'ทุ่งยาว','58130',NULL,NULL),(4905,'580306',627,'เมืองแปง','58130',NULL,NULL),(4906,'580307',627,'โป่งสา','58130',NULL,NULL),(4907,'580401',628,'บ้านกาศ','58110',NULL,NULL),(4908,'580402',628,'แม่สะเรียง','58110',NULL,NULL),(4909,'580403',628,'แม่คง','58110',NULL,NULL),(4910,'580404',628,'แม่เหาะ','58110',NULL,NULL),(4911,'580405',628,'แม่ยวม','58110',NULL,NULL),(4912,'580406',628,'เสาหิน','58110',NULL,NULL),(4913,'580408',628,'ป่าแป๋','58110',NULL,NULL),(4914,'580501',629,'แม่ลาน้อย','58120',NULL,NULL),(4915,'580502',629,'แม่ลาหลวง','58120',NULL,NULL),(4916,'580503',629,'ท่าผาปุ้ม','58120',NULL,NULL),(4917,'580504',629,'แม่โถ','58120',NULL,NULL),(4918,'580505',629,'ห้วยห้อม','58120',NULL,NULL),(4919,'580506',629,'แม่นาจาง','58120',NULL,NULL),(4920,'580507',629,'สันติคีรี','58120',NULL,NULL),(4921,'580508',629,'ขุนแม่ลาน้อย','58120',NULL,NULL),(4922,'580601',630,'สบเมย','58110',NULL,NULL),(4923,'580602',630,'แม่คะตวน','58110',NULL,NULL),(4924,'580603',630,'กองก๋อย','58110',NULL,NULL),(4925,'580604',630,'แม่สวด','58110',NULL,NULL),(4926,'580605',630,'ป่าโปง','58110',NULL,NULL),(4927,'580606',630,'แม่สามแลบ','58110',NULL,NULL),(4928,'580701',631,'สบป่อง','58150',NULL,NULL),(4929,'580702',631,'ปางมะผ้า','58150',NULL,NULL),(4930,'580703',631,'ถ้ำลอด','58150',NULL,NULL),(4931,'580704',631,'นาปู่ป้อม','58150',NULL,NULL),(4932,'600101',632,'ปากน้ำโพ','60000',NULL,NULL),(4933,'600102',632,'กลางแดด','60000',NULL,NULL),(4934,'600103',632,'เกรียงไกร','60000',NULL,NULL),(4935,'600104',632,'แควใหญ่','60000',NULL,NULL),(4936,'600105',632,'ตะเคียนเลื่อน','60000',NULL,NULL),(4937,'600106',632,'นครสวรรค์ตก','60000',NULL,NULL),(4938,'600107',632,'นครสวรรค์ออก','60000',NULL,NULL),(4939,'600108',632,'บางพระหลวง','60000',NULL,NULL),(4940,'600109',632,'บางม่วง','60000',NULL,NULL),(4941,'600110',632,'บ้านมะเกลือ','60000',NULL,NULL),(4942,'600111',632,'บ้านแก่ง','60000',NULL,NULL),(4943,'600112',632,'พระนอน','60000',NULL,NULL),(4944,'600113',632,'วัดไทรย์','60000',NULL,NULL),(4945,'600114',632,'หนองกรด','60240',NULL,NULL),(4946,'600115',632,'หนองกระโดน','60240',NULL,NULL),(4947,'600116',632,'หนองปลิง','60000',NULL,NULL),(4948,'600117',632,'บึงเสนาท','60000',NULL,NULL),(4949,'600201',633,'โกรกพระ','60170',NULL,NULL),(4950,'600202',633,'ยางตาล','60170',NULL,NULL),(4951,'600203',633,'บางมะฝ่อ','60170',NULL,NULL),(4952,'600204',633,'บางประมุง','60170',NULL,NULL),(4953,'600205',633,'นากลาง','60170',NULL,NULL),(4954,'600206',633,'ศาลาแดง','60170',NULL,NULL),(4955,'600207',633,'เนินกว้าว','60170',NULL,NULL),(4956,'600208',633,'เนินศาลา','60170',NULL,NULL),(4957,'600209',633,'หาดสูง','60170',NULL,NULL),(4958,'600301',634,'ชุมแสง','60120',NULL,NULL),(4959,'600302',634,'ทับกฤช','60250',NULL,NULL),(4960,'600303',634,'พิกุล','60120',NULL,NULL),(4961,'600304',634,'เกยไชย','60120',NULL,NULL),(4962,'600305',634,'ท่าไม้','60120',NULL,NULL),(4963,'600306',634,'บางเคียน','60120',NULL,NULL),(4964,'600307',634,'หนองกระเจา','60120',NULL,NULL),(4965,'600308',634,'พันลาน','60250',NULL,NULL),(4966,'600309',634,'โคกหม้อ','60120',NULL,NULL),(4967,'600310',634,'ไผ่สิงห์','60120',NULL,NULL),(4968,'600311',634,'ฆะมัง','60120',NULL,NULL),(4969,'600312',634,'ทับกฤชใต้','60250',NULL,NULL),(4970,'600401',635,'หนองบัว','60110',NULL,NULL),(4971,'600402',635,'หนองกลับ','60110',NULL,NULL),(4972,'600403',635,'ธารทหาร','60110',NULL,NULL),(4973,'600404',635,'ห้วยร่วม','60110',NULL,NULL),(4974,'600405',635,'ห้วยถั่วใต้','60110',NULL,NULL),(4975,'600406',635,'ห้วยถั่วเหนือ','60110',NULL,NULL),(4976,'600407',635,'ห้วยใหญ่','60110',NULL,NULL),(4977,'600408',635,'ทุ่งทอง','60110',NULL,NULL),(4978,'600409',635,'วังบ่อ','60110',NULL,NULL),(4979,'600501',636,'ท่างิ้ว','60180',NULL,NULL),(4980,'600502',636,'บางตาหงาย','60180',NULL,NULL),(4981,'600503',636,'หูกวาง','60180',NULL,NULL),(4982,'600504',636,'อ่างทอง','60180',NULL,NULL),(4983,'600505',636,'บ้านแดน','60180',NULL,NULL),(4984,'600506',636,'บางแก้ว','60180',NULL,NULL),(4985,'600507',636,'ตาขีด','60180',NULL,NULL),(4986,'600508',636,'ตาสัง','60180',NULL,NULL),(4987,'600509',636,'ด่านช้าง','60180',NULL,NULL),(4988,'600510',636,'หนองกรด','60180',NULL,NULL),(4989,'600511',636,'หนองตางู','60180',NULL,NULL),(4990,'600512',636,'บึงปลาทู','60180',NULL,NULL),(4991,'600513',636,'เจริญผล','60180',NULL,NULL),(4992,'600601',637,'มหาโพธิ','60230',NULL,NULL),(4993,'600602',637,'เก้าเลี้ยว','60230',NULL,NULL),(4994,'600603',637,'หนองเต่า','60230',NULL,NULL),(4995,'600604',637,'เขาดิน','60230',NULL,NULL),(4996,'600605',637,'หัวดง','60230',NULL,NULL),(4997,'600701',638,'ตาคลี','60140',NULL,NULL),(4998,'600702',638,'ช่องแค','60210',NULL,NULL),(4999,'600703',638,'จันเสน','60260',NULL,NULL),(5000,'600704',638,'ห้วยหอม','60210',NULL,NULL),(5001,'600705',638,'หัวหวาย','60140',NULL,NULL),(5002,'600706',638,'หนองโพ','60140',NULL,NULL),(5003,'600707',638,'หนองหม้อ','60140',NULL,NULL),(5004,'600708',638,'สร้อยทอง','60210',NULL,NULL),(5005,'600709',638,'ลาดทิพรส','60260',NULL,NULL),(5006,'600710',638,'พรหมนิมิต','60210',NULL,NULL),(5007,'600801',639,'ท่าตะโก','60160',NULL,NULL),(5008,'600802',639,'พนมรอก','60160',NULL,NULL),(5009,'600803',639,'หัวถนน','60160',NULL,NULL),(5010,'600804',639,'สายลำโพง','60160',NULL,NULL),(5011,'600805',639,'วังมหากร','60160',NULL,NULL),(5012,'600806',639,'ดอนคา','60160',NULL,NULL),(5013,'600807',639,'ทำนบ','60160',NULL,NULL),(5014,'600808',639,'วังใหญ่','60160',NULL,NULL),(5015,'600809',639,'พนมเศษ','60160',NULL,NULL),(5016,'600810',639,'หนองหลวง','60160',NULL,NULL),(5017,'600901',640,'โคกเดื่อ','60220',NULL,NULL),(5018,'600902',640,'สำโรงชัย','60220',NULL,NULL),(5019,'600903',640,'วังน้ำลัด','60220',NULL,NULL),(5020,'600904',640,'ตะคร้อ','60220',NULL,NULL),(5021,'600905',640,'โพธิ์ประสาท','60220',NULL,NULL),(5022,'600906',640,'วังข่อย','60220',NULL,NULL),(5023,'600907',640,'นาขอม','60220',NULL,NULL),(5024,'600908',640,'ไพศาลี','60220',NULL,NULL),(5025,'601001',641,'พยุหะ','60130',NULL,NULL),(5026,'601002',641,'เนินมะกอก','60130',NULL,NULL),(5027,'601003',641,'นิคมเขาบ่อแก้ว','60130',NULL,NULL),(5028,'601004',641,'ม่วงหัก','60130',NULL,NULL),(5029,'601005',641,'ยางขาว','60130',NULL,NULL),(5030,'601006',641,'ย่านมัทรี','60130',NULL,NULL),(5031,'601007',641,'เขาทอง','60130',NULL,NULL),(5032,'601008',641,'ท่าน้ำอ้อย','60130',NULL,NULL),(5033,'601009',641,'น้ำทรง','60130',NULL,NULL),(5034,'601010',641,'เขากะลา','60130',NULL,NULL),(5035,'601011',641,'สระทะเล','60130',NULL,NULL),(5036,'601101',642,'ลาดยาว','60150',NULL,NULL),(5037,'601102',642,'ห้วยน้ำหอม','60150',NULL,NULL),(5038,'601103',642,'วังม้า','60150',NULL,NULL),(5039,'601104',642,'วังเมือง','60150',NULL,NULL),(5040,'601105',642,'สร้อยละคร','60150',NULL,NULL),(5041,'601106',642,'มาบแก','60150',NULL,NULL),(5042,'601107',642,'หนองยาว','60150',NULL,NULL),(5043,'601108',642,'หนองนมวัว','60150',NULL,NULL),(5044,'601109',642,'บ้านไร่','60150',NULL,NULL),(5045,'601110',642,'เนินขี้เหล็ก','60150',NULL,NULL),(5046,'601116',642,'ศาลเจ้าไก่ต่อ','60150',NULL,NULL),(5047,'601117',642,'สระแก้ว','60150',NULL,NULL),(5048,'601201',643,'ตากฟ้า','60190',NULL,NULL),(5049,'601202',643,'ลำพยนต์','60190',NULL,NULL),(5050,'601203',643,'สุขสำราญ','60190',NULL,NULL),(5051,'601204',643,'หนองพิกุล','60190',NULL,NULL),(5052,'601205',643,'พุนกยูง','60190',NULL,NULL),(5053,'601206',643,'อุดมธัญญา','60190',NULL,NULL),(5054,'601207',643,'เขาชายธง','60190',NULL,NULL),(5055,'601301',644,'แม่วงก์','60150',NULL,NULL),(5056,'601303',644,'แม่เลย์','60150',NULL,NULL),(5057,'601304',644,'วังซ่าน','60150',NULL,NULL),(5058,'601305',644,'เขาชนกัน','60150',NULL,NULL),(5059,'601401',645,'แม่เปิน','60150',NULL,NULL),(5060,'601501',646,'ชุมตาบง','60150',NULL,NULL),(5061,'601502',646,'ปางสวรรค์','60150',NULL,NULL),(5062,'610101',647,'อุทัยใหม่','61000',NULL,NULL),(5063,'610102',647,'น้ำซึม','61000',NULL,NULL),(5064,'610103',647,'สะแกกรัง','61000',NULL,NULL),(5065,'610104',647,'ดอนขวาง','61000',NULL,NULL),(5066,'610105',647,'หาดทนง','61000',NULL,NULL),(5067,'610106',647,'เกาะเทโพ','61000',NULL,NULL),(5068,'610107',647,'ท่าซุง','61000',NULL,NULL),(5069,'610108',647,'หนองแก','61000',NULL,NULL),(5070,'610109',647,'โนนเหล็ก','61000',NULL,NULL),(5071,'610110',647,'หนองเต่า','61000',NULL,NULL),(5072,'610111',647,'หนองไผ่แบน','61000',NULL,NULL),(5073,'610112',647,'หนองพังค่า','61000',NULL,NULL),(5074,'610113',647,'ทุ่งใหญ่','61000',NULL,NULL),(5075,'610114',647,'เนินแจง','61000',NULL,NULL),(5076,'610201',648,'ทัพทัน','61120',NULL,NULL),(5077,'610202',648,'ทุ่งนาไทย','61120',NULL,NULL),(5078,'610203',648,'เขาขี้ฝอย','61120',NULL,NULL),(5079,'610204',648,'หนองหญ้าปล้อง','61120',NULL,NULL),(5080,'610205',648,'โคกหม้อ','61120',NULL,NULL),(5081,'610206',648,'หนองยายดา','61120',NULL,NULL),(5082,'610207',648,'หนองกลางดง','61120',NULL,NULL),(5083,'610208',648,'หนองกระทุ่ม','61120',NULL,NULL),(5084,'610209',648,'หนองสระ','61120',NULL,NULL),(5085,'610210',648,'ตลุกดู่','61120',NULL,NULL),(5086,'610301',649,'สว่างอารมณ์','61150',NULL,NULL),(5087,'610302',649,'หนองหลวง','61150',NULL,NULL),(5088,'610303',649,'พลวงสองนาง','61150',NULL,NULL),(5089,'610304',649,'ไผ่เขียว','61150',NULL,NULL),(5090,'610305',649,'บ่อยาง','61150',NULL,NULL),(5091,'610401',650,'หนองฉาง','61110',NULL,NULL),(5092,'610402',650,'หนองยาง','61110',NULL,NULL),(5093,'610403',650,'หนองนางนวล','61110',NULL,NULL),(5094,'610404',650,'หนองสรวง','61110',NULL,NULL),(5095,'610405',650,'บ้านเก่า','61110',NULL,NULL),(5096,'610406',650,'อุทัยเก่า','61110',NULL,NULL),(5097,'610407',650,'ทุ่งโพ','61110',NULL,NULL),(5098,'610408',650,'ทุ่งพง','61110',NULL,NULL),(5099,'610409',650,'เขาบางแกรก','61170',NULL,NULL),(5100,'610410',650,'เขากวางทอง','61110',NULL,NULL),(5101,'610501',651,'หนองขาหย่าง','61130',NULL,NULL),(5102,'610502',651,'หนองไผ่','61130',NULL,NULL),(5103,'610503',651,'ดอนกลอย','61130',NULL,NULL),(5104,'610504',651,'ห้วยรอบ','61130',NULL,NULL),(5105,'610505',651,'ทุ่งพึ่ง','61130',NULL,NULL),(5106,'610506',651,'ท่าโพ','61130',NULL,NULL),(5107,'610507',651,'หมกแถว','61130',NULL,NULL),(5108,'610509',651,'ดงขวาง','61130',NULL,NULL),(5109,'610601',652,'บ้านไร่','61140',NULL,NULL),(5110,'610602',652,'ทัพหลวง','61140',NULL,NULL),(5111,'610603',652,'ห้วยแห้ง','61140',NULL,NULL),(5112,'610604',652,'คอกควาย','61140',NULL,NULL),(5113,'610605',652,'วังหิน','61180',NULL,NULL),(5114,'610606',652,'เมืองการุ้ง','61180',NULL,NULL),(5115,'610607',652,'แก่นมะกรูด','61140',NULL,NULL),(5116,'610609',652,'หนองจอก','61180',NULL,NULL),(5117,'610610',652,'หูช้าง','61180',NULL,NULL),(5118,'610611',652,'บ้านบึง','61140',NULL,NULL),(5119,'610612',652,'บ้านใหม่คลองเคียน','61180',NULL,NULL),(5120,'610613',652,'หนองบ่มกล้วย','61180',NULL,NULL),(5121,'610614',652,'เจ้าวัด','61140',NULL,NULL),(5122,'610701',653,'ลานสัก','61160',NULL,NULL),(5123,'610702',653,'ประดู่ยืน','61160',NULL,NULL),(5124,'610703',653,'ป่าอ้อ','61160',NULL,NULL),(5125,'610704',653,'ระบำ','61160',NULL,NULL),(5126,'610705',653,'น้ำรอบ','61160',NULL,NULL),(5127,'610706',653,'ทุ่งนางาม','61160',NULL,NULL),(5128,'610801',654,'สุขฤทัย','61170',NULL,NULL),(5129,'610802',654,'ทองหลาง','61170',NULL,NULL),(5130,'610803',654,'ห้วยคต','61170',NULL,NULL),(5131,'620101',655,'ในเมือง','62000',NULL,NULL),(5132,'620102',655,'ไตรตรึงษ์','62160',NULL,NULL),(5133,'620103',655,'อ่างทอง','62000',NULL,NULL),(5134,'620104',655,'นาบ่อคำ','62000',NULL,NULL),(5135,'620105',655,'นครชุม','62000',NULL,NULL),(5136,'620106',655,'ทรงธรรม','62000',NULL,NULL),(5137,'620107',655,'ลานดอกไม้','62000',NULL,NULL),(5138,'620110',655,'หนองปลิง','62000',NULL,NULL),(5139,'620111',655,'คณฑี','62000',NULL,NULL),(5140,'620112',655,'นิคมทุ่งโพธิ์ทะเล','62000',NULL,NULL),(5141,'620113',655,'เทพนคร','62000',NULL,NULL),(5142,'620114',655,'วังทอง','62000',NULL,NULL),(5143,'620115',655,'ท่าขุนราม','62000',NULL,NULL),(5144,'620117',655,'คลองแม่ลาย','62000',NULL,NULL),(5145,'620118',655,'ธำมรงค์','62160',NULL,NULL),(5146,'620119',655,'สระแก้ว','62000',NULL,NULL),(5147,'620201',656,'ไทรงาม','62150',NULL,NULL),(5148,'620202',656,'หนองคล้า','62150',NULL,NULL),(5149,'620203',656,'หนองทอง','62150',NULL,NULL),(5150,'620204',656,'หนองไม้กอง','62150',NULL,NULL),(5151,'620205',656,'มหาชัย','62150',NULL,NULL),(5152,'620206',656,'พานทอง','62150',NULL,NULL),(5153,'620207',656,'หนองแม่แตง','62150',NULL,NULL),(5154,'620301',657,'คลองน้ำไหล','62180',NULL,NULL),(5155,'620302',657,'โป่งน้ำร้อน','62180',NULL,NULL),(5156,'620303',657,'คลองลานพัฒนา','62180',NULL,NULL),(5157,'620304',657,'สักงาม','62180',NULL,NULL),(5158,'620403',658,'ยางสูง','62130',NULL,NULL),(5159,'620404',658,'ป่าพุทรา','62130',NULL,NULL),(5160,'620405',658,'แสนตอ','62130',NULL,NULL),(5161,'620406',658,'สลกบาตร','62140',NULL,NULL),(5162,'620407',658,'บ่อถ้ำ','62140',NULL,NULL),(5163,'620408',658,'ดอนแตง','62140',NULL,NULL),(5164,'620409',658,'วังชะพลู','62140',NULL,NULL),(5165,'620410',658,'โค้งไผ่','62140',NULL,NULL),(5166,'620411',658,'ปางมะค่า','62140',NULL,NULL),(5167,'620412',658,'วังหามแห','62140',NULL,NULL),(5168,'620413',658,'เกาะตาล','62130',NULL,NULL),(5169,'620501',659,'คลองขลุง','62120',NULL,NULL),(5170,'620502',659,'ท่ามะเขือ','62120',NULL,NULL),(5171,'620504',659,'ท่าพุทรา','62120',NULL,NULL),(5172,'620505',659,'แม่ลาด','62120',NULL,NULL),(5173,'620506',659,'วังยาง','62120',NULL,NULL),(5174,'620507',659,'วังแขม','62120',NULL,NULL),(5175,'620508',659,'หัวถนน','62120',NULL,NULL),(5176,'620509',659,'วังไทร','62120',NULL,NULL),(5177,'620513',659,'วังบัว','62120',NULL,NULL),(5178,'620516',659,'คลองสมบูรณ์','62120',NULL,NULL),(5179,'620601',660,'พรานกระต่าย','62110',NULL,NULL),(5180,'620602',660,'หนองหัววัว','62110',NULL,NULL),(5181,'620603',660,'ท่าไม้','62110',NULL,NULL),(5182,'620604',660,'วังควง','62110',NULL,NULL),(5183,'620605',660,'วังตะแบก','62110',NULL,NULL),(5184,'620606',660,'เขาคีริส','62110',NULL,NULL),(5185,'620607',660,'คุยบ้านโอง','62110',NULL,NULL),(5186,'620608',660,'คลองพิไกร','62110',NULL,NULL),(5187,'620609',660,'ถ้ำกระต่ายทอง','62110',NULL,NULL),(5188,'620610',660,'ห้วยยั้ง','62110',NULL,NULL),(5189,'620701',661,'ลานกระบือ','62170',NULL,NULL),(5190,'620702',661,'ช่องลม','62170',NULL,NULL),(5191,'620703',661,'หนองหลวง','62170',NULL,NULL),(5192,'620704',661,'โนนพลวง','62170',NULL,NULL),(5193,'620705',661,'ประชาสุขสันต์','62170',NULL,NULL),(5194,'620706',661,'บึงทับแรต','62170',NULL,NULL),(5195,'620707',661,'จันทิมา','62170',NULL,NULL),(5196,'620801',662,'ทุ่งทราย','62190',NULL,NULL),(5197,'620802',662,'ทุ่งทอง','62190',NULL,NULL),(5198,'620803',662,'ถาวรวัฒนา','62190',NULL,NULL),(5199,'620901',663,'โพธิ์ทอง','62120',NULL,NULL),(5200,'620902',663,'หินดาต','62120',NULL,NULL),(5201,'620903',663,'ปางตาไว','62120',NULL,NULL),(5202,'621001',664,'บึงสามัคคี','62210',NULL,NULL),(5203,'621002',664,'วังชะโอน','62210',NULL,NULL),(5204,'621003',664,'ระหาน','62210',NULL,NULL),(5205,'621004',664,'เทพนิมิต','62210',NULL,NULL),(5206,'621101',665,'โกสัมพี','62000',NULL,NULL),(5207,'621102',665,'เพชรชมภู','62000',NULL,NULL),(5208,'621103',665,'ลานดอกไม้ตก','62000',NULL,NULL),(5209,'630101',666,'ระแหง','63000',NULL,NULL),(5210,'630105',666,'หนองบัวเหนือ','63000',NULL,NULL),(5211,'630106',666,'ไม้งาม','63000',NULL,NULL),(5212,'630107',666,'โป่งแดง','63000',NULL,NULL),(5213,'630108',666,'น้ำรึม','63000',NULL,NULL),(5214,'630109',666,'วังหิน','63000',NULL,NULL),(5215,'630111',666,'แม่ท้อ','63000',NULL,NULL),(5216,'630112',666,'ป่ามะม่วง','63000',NULL,NULL),(5217,'630113',666,'หนองบัวใต้','63000',NULL,NULL),(5218,'630114',666,'วังประจบ','63000',NULL,NULL),(5219,'630115',666,'ตลุกกลางทุ่ง','63000',NULL,NULL),(5220,'630201',667,'ตากออก','63120',NULL,NULL),(5221,'630202',667,'สมอโคน','63120',NULL,NULL),(5222,'630203',667,'แม่สลิด','63120',NULL,NULL),(5223,'630204',667,'ตากตก','63120',NULL,NULL),(5224,'630205',667,'เกาะตะเภา','63120',NULL,NULL),(5225,'630206',667,'ทุ่งกระเซาะ','63120',NULL,NULL),(5226,'630207',667,'ท้องฟ้า','63120',NULL,NULL),(5227,'630301',668,'สามเงา','63130',NULL,NULL),(5228,'630302',668,'วังหมัน','63130',NULL,NULL),(5229,'630303',668,'ยกกระบัตร','63130',NULL,NULL),(5230,'630304',668,'ย่านรี','63130',NULL,NULL),(5231,'630305',668,'บ้านนา','63130',NULL,NULL),(5232,'630306',668,'วังจันทร์','63130',NULL,NULL),(5233,'630401',669,'แม่ระมาด','63140',NULL,NULL),(5234,'630402',669,'แม่จะเรา','63140',NULL,NULL),(5235,'630403',669,'ขะเนจื้อ','63140',NULL,NULL),(5236,'630404',669,'แม่ตื่น','63140',NULL,NULL),(5237,'630405',669,'สามหมื่น','63140',NULL,NULL),(5238,'630406',669,'พระธาตุ','63140',NULL,NULL),(5239,'630501',670,'ท่าสองยาง','63150',NULL,NULL),(5240,'630502',670,'แม่ต้าน','63150',NULL,NULL),(5241,'630503',670,'แม่สอง','63150',NULL,NULL),(5242,'630504',670,'แม่หละ','63150',NULL,NULL),(5243,'630505',670,'แม่วะหลวง','63150',NULL,NULL),(5244,'630506',670,'แม่อุสุ','63150',NULL,NULL),(5245,'630601',671,'แม่สอด','63110',NULL,NULL),(5246,'630602',671,'แม่กุ','63110',NULL,NULL),(5247,'630603',671,'พะวอ','63110',NULL,NULL),(5248,'630604',671,'แม่ตาว','63110',NULL,NULL),(5249,'630605',671,'แม่กาษา','63110',NULL,NULL),(5250,'630606',671,'ท่าสายลวด','63110',NULL,NULL),(5251,'630607',671,'แม่ปะ','63110',NULL,NULL),(5252,'630608',671,'มหาวัน','63110',NULL,NULL),(5253,'630609',671,'ด่านแม่ละเมา','63110',NULL,NULL),(5254,'630610',671,'พระธาตุผาแดง','63110',NULL,NULL),(5255,'630701',672,'พบพระ','63160',NULL,NULL),(5256,'630702',672,'ช่องแคบ','63160',NULL,NULL),(5257,'630703',672,'คีรีราษฎร์','63160',NULL,NULL),(5258,'630704',672,'วาเล่ย์','63160',NULL,NULL),(5259,'630705',672,'รวมไทยพัฒนา','63160',NULL,NULL),(5260,'630801',673,'อุ้มผาง','63170',NULL,NULL),(5261,'630802',673,'หนองหลวง','63170',NULL,NULL),(5262,'630803',673,'โมโกร','63170',NULL,NULL),(5263,'630804',673,'แม่จัน','63170',NULL,NULL),(5264,'630805',673,'แม่ละมุ้ง','63170',NULL,NULL),(5265,'630806',673,'แม่กลอง','63170',NULL,NULL),(5266,'630901',674,'เชียงทอง','63000',NULL,NULL),(5267,'630902',674,'นาโบสถ์','63000',NULL,NULL),(5268,'630903',674,'ประดาง','63000',NULL,NULL),(5269,'640101',675,'ธานี','64000',NULL,NULL),(5270,'640102',675,'บ้านสวน','64220',NULL,NULL),(5271,'640103',675,'เมืองเก่า','64210',NULL,NULL),(5272,'640104',675,'ปากแคว','64000',NULL,NULL),(5273,'640105',675,'ยางซ้าย','64000',NULL,NULL),(5274,'640106',675,'บ้านกล้วย','64000',NULL,NULL),(5275,'640107',675,'บ้านหลุม','64000',NULL,NULL),(5276,'640108',675,'ตาลเตี้ย','64220',NULL,NULL),(5277,'640109',675,'ปากพระ','64000',NULL,NULL),(5278,'640110',675,'วังทองแดง','64210',NULL,NULL),(5279,'640201',676,'ลานหอย','64140',NULL,NULL),(5280,'640202',676,'บ้านด่าน','64140',NULL,NULL),(5281,'640203',676,'วังตะคร้อ','64140',NULL,NULL),(5282,'640204',676,'วังน้ำขาว','64140',NULL,NULL),(5283,'640205',676,'ตลิ่งชัน','64140',NULL,NULL),(5284,'640206',676,'หนองหญ้าปล้อง','64140',NULL,NULL),(5285,'640207',676,'วังลึก','64140',NULL,NULL),(5286,'640301',677,'โตนด','64160',NULL,NULL),(5287,'640302',677,'ทุ่งหลวง','64160',NULL,NULL),(5288,'640303',677,'บ้านป้อม','64160',NULL,NULL),(5289,'640304',677,'สามพวง','64160',NULL,NULL),(5290,'640305',677,'ศรีคีรีมาศ','64160',NULL,NULL),(5291,'640306',677,'หนองจิก','64160',NULL,NULL),(5292,'640307',677,'นาเชิงคีรี','64160',NULL,NULL),(5293,'640308',677,'หนองกระดิ่ง','64160',NULL,NULL),(5294,'640309',677,'บ้านน้ำพุ','64160',NULL,NULL),(5295,'640310',677,'ทุ่งยางเมือง','64160',NULL,NULL),(5296,'640401',678,'กง','64170',NULL,NULL),(5297,'640402',678,'บ้านกร่าง','64170',NULL,NULL),(5298,'640403',678,'ไกรนอก','64170',NULL,NULL),(5299,'640404',678,'ไกรกลาง','64170',NULL,NULL),(5300,'640405',678,'ไกรใน','64170',NULL,NULL),(5301,'640406',678,'ดงเดือย','64170',NULL,NULL),(5302,'640407',678,'ป่าแฝก','64170',NULL,NULL),(5303,'640408',678,'กกแรต','64170',NULL,NULL),(5304,'640409',678,'ท่าฉนวน','64170',NULL,NULL),(5305,'640410',678,'หนองตูม','64170',NULL,NULL),(5306,'640411',678,'บ้านใหม่สุขเกษม','64170',NULL,NULL),(5307,'640501',679,'หาดเสี้ยว','64130',NULL,NULL),(5308,'640502',679,'ป่างิ้ว','64130',NULL,NULL),(5309,'640503',679,'แม่สำ','64130',NULL,NULL),(5310,'640504',679,'แม่สิน','64130',NULL,NULL),(5311,'640505',679,'บ้านตึก','64130',NULL,NULL),(5312,'640506',679,'หนองอ้อ','64130',NULL,NULL),(5313,'640507',679,'ท่าชัย','64190',NULL,NULL),(5314,'640508',679,'ศรีสัชนาลัย','64190',NULL,NULL),(5315,'640509',679,'ดงคู่','64130',NULL,NULL),(5316,'640510',679,'บ้านแก่ง','64130',NULL,NULL),(5317,'640511',679,'สารจิตร','64130',NULL,NULL),(5318,'640601',680,'คลองตาล','64120',NULL,NULL),(5319,'640602',680,'วังลึก','64120',NULL,NULL),(5320,'640603',680,'สามเรือน','64120',NULL,NULL),(5321,'640604',680,'บ้านนา','64120',NULL,NULL),(5322,'640605',680,'วังทอง','64120',NULL,NULL),(5323,'640606',680,'นาขุนไกร','64120',NULL,NULL),(5324,'640607',680,'เกาะตาเลี้ยง','64120',NULL,NULL),(5325,'640608',680,'วัดเกาะ','64120',NULL,NULL),(5326,'640609',680,'บ้านไร่','64120',NULL,NULL),(5327,'640610',680,'ทับผึ้ง','64120',NULL,NULL),(5328,'640611',680,'บ้านซ่าน','64120',NULL,NULL),(5329,'640612',680,'วังใหญ่','64120',NULL,NULL),(5330,'640613',680,'ราวต้นจันทร์','64120',NULL,NULL),(5331,'640701',681,'เมืองสวรรคโลก','64110',NULL,NULL),(5332,'640702',681,'ในเมือง','64110',NULL,NULL),(5333,'640703',681,'คลองกระจง','64110',NULL,NULL),(5334,'640704',681,'วังพิณพาทย์','64110',NULL,NULL),(5335,'640705',681,'วังไม้ขอน','64110',NULL,NULL),(5336,'640706',681,'ย่านยาว','64110',NULL,NULL),(5337,'640707',681,'นาทุ่ง','64110',NULL,NULL),(5338,'640708',681,'คลองยาง','64110',NULL,NULL),(5339,'640709',681,'เมืองบางยม','64110',NULL,NULL),(5340,'640710',681,'ท่าทอง','64110',NULL,NULL),(5341,'640711',681,'ปากน้ำ','64110',NULL,NULL),(5342,'640712',681,'ป่ากุมเกาะ','64110',NULL,NULL),(5343,'640713',681,'เมืองบางขลัง','64110',NULL,NULL),(5344,'640714',681,'หนองกลับ','64110',NULL,NULL),(5345,'640801',682,'ศรีนคร','64180',NULL,NULL),(5346,'640802',682,'นครเดิฐ','64180',NULL,NULL),(5347,'640803',682,'น้ำขุม','64180',NULL,NULL),(5348,'640804',682,'คลองมะพลับ','64180',NULL,NULL),(5349,'640805',682,'หนองบัว','64180',NULL,NULL),(5350,'640901',683,'บ้านใหม่ไชยมงคล','64230',NULL,NULL),(5351,'640902',683,'ไทยชนะศึก','64150',NULL,NULL),(5352,'640903',683,'ทุ่งเสลี่ยม','64150',NULL,NULL),(5353,'640904',683,'กลางดง','64150',NULL,NULL),(5354,'640905',683,'เขาแก้วศรีสมบูรณ์','64230',NULL,NULL),(5355,'650101',684,'ในเมือง','65000',NULL,NULL),(5356,'650102',684,'วังน้ำคู้','65230',NULL,NULL),(5357,'650103',684,'วัดจันทร์','65000',NULL,NULL),(5358,'650104',684,'วัดพริก','65230',NULL,NULL),(5359,'650105',684,'ท่าทอง','65000',NULL,NULL),(5360,'650106',684,'ท่าโพธิ์','65000',NULL,NULL),(5361,'650107',684,'สมอแข','65000',NULL,NULL),(5362,'650108',684,'ดอนทอง','65000',NULL,NULL),(5363,'650109',684,'บ้านป่า','65000',NULL,NULL),(5364,'650110',684,'ปากโทก','65000',NULL,NULL),(5365,'650111',684,'หัวรอ','65000',NULL,NULL),(5366,'650112',684,'จอมทอง','65000',NULL,NULL),(5367,'650113',684,'บ้านกร่าง','65000',NULL,NULL),(5368,'650114',684,'บ้านคลอง','65000',NULL,NULL),(5369,'650115',684,'พลายชุมพล','65000',NULL,NULL),(5370,'650116',684,'มะขามสูง','65000',NULL,NULL),(5371,'650117',684,'อรัญญิก','65000',NULL,NULL),(5372,'650118',684,'บึงพระ','65000',NULL,NULL),(5373,'650119',684,'ไผ่ขอดอน','65000',NULL,NULL),(5374,'650120',684,'งิ้วงาม','65230',NULL,NULL),(5375,'650201',685,'นครไทย','65120',NULL,NULL),(5376,'650202',685,'หนองกะท้าว','65120',NULL,NULL),(5377,'650203',685,'บ้านแยง','65120',NULL,NULL),(5378,'650204',685,'เนินเพิ่ม','65120',NULL,NULL),(5379,'650205',685,'นาบัว','65120',NULL,NULL),(5380,'650206',685,'นครชุม','65120',NULL,NULL),(5381,'650207',685,'น้ำกุ่ม','65120',NULL,NULL),(5382,'650208',685,'ยางโกลน','65120',NULL,NULL),(5383,'650209',685,'บ่อโพธิ์','65120',NULL,NULL),(5384,'650210',685,'บ้านพร้าว','65120',NULL,NULL),(5385,'650211',685,'ห้วยเฮี้ย','65120',NULL,NULL),(5386,'650301',686,'ป่าแดง','65170',NULL,NULL),(5387,'650302',686,'ชาติตระการ','65170',NULL,NULL),(5388,'650303',686,'สวนเมี่ยง','65170',NULL,NULL),(5389,'650304',686,'บ้านดง','65170',NULL,NULL),(5390,'650305',686,'บ่อภาค','65170',NULL,NULL),(5391,'650306',686,'ท่าสะแก','65170',NULL,NULL),(5392,'650401',687,'บางระกำ','65140',NULL,NULL),(5393,'650402',687,'ปลักแรด','65140',NULL,NULL),(5394,'650403',687,'พันเสา','65140',NULL,NULL),(5395,'650404',687,'วังอิทก','65140',NULL,NULL),(5396,'650405',687,'บึงกอก','65140',NULL,NULL),(5397,'650406',687,'หนองกุลา','65140',NULL,NULL),(5398,'650407',687,'ชุมแสงสงคราม','65240',NULL,NULL),(5399,'650408',687,'นิคมพัฒนา','65140',NULL,NULL),(5400,'650409',687,'บ่อทอง','65140',NULL,NULL),(5401,'650410',687,'ท่านางงาม','65140',NULL,NULL),(5402,'650411',687,'คุยม่วง','65240',NULL,NULL),(5403,'650501',688,'บางกระทุ่ม','65110',NULL,NULL),(5404,'650502',688,'บ้านไร่','65110',NULL,NULL),(5405,'650503',688,'โคกสลุด','65110',NULL,NULL),(5406,'650504',688,'สนามคลี','65110',NULL,NULL),(5407,'650505',688,'ท่าตาล','65110',NULL,NULL),(5408,'650506',688,'ไผ่ล้อม','65110',NULL,NULL),(5409,'650507',688,'นครป่าหมาก','65110',NULL,NULL),(5410,'650508',688,'เนินกุ่ม','65210',NULL,NULL),(5411,'650509',688,'วัดตายม','65210',NULL,NULL),(5412,'650601',689,'พรหมพิราม','65150',NULL,NULL),(5413,'650602',689,'ท่าช้าง','65150',NULL,NULL),(5414,'650603',689,'วงฆ้อง','65180',NULL,NULL),(5415,'650604',689,'มะตูม','65150',NULL,NULL),(5416,'650605',689,'หอกลอง','65150',NULL,NULL),(5417,'650606',689,'ศรีภิรมย์','65180',NULL,NULL),(5418,'650607',689,'ตลุกเทียม','65180',NULL,NULL),(5419,'650608',689,'วังวน','65150',NULL,NULL),(5420,'650609',689,'หนองแขม','65150',NULL,NULL),(5421,'650610',689,'มะต้อง','65180',NULL,NULL),(5422,'650611',689,'ทับยายเชียง','65150',NULL,NULL),(5423,'650612',689,'ดงประคำ','65180',NULL,NULL),(5424,'650701',690,'วัดโบสถ์','65160',NULL,NULL),(5425,'650702',690,'ท่างาม','65160',NULL,NULL),(5426,'650703',690,'ท้อแท้','65160',NULL,NULL),(5427,'650704',690,'บ้านยาง','65160',NULL,NULL),(5428,'650705',690,'หินลาด','65160',NULL,NULL),(5429,'650706',690,'คันโช้ง','65160',NULL,NULL),(5430,'650801',691,'วังทอง','65130',NULL,NULL),(5431,'650802',691,'พันชาลี','65130',NULL,NULL),(5432,'650803',691,'แม่ระกา','65130',NULL,NULL),(5433,'650804',691,'บ้านกลาง','65220',NULL,NULL),(5434,'650805',691,'วังพิกุล','65130',NULL,NULL),(5435,'650806',691,'แก่งโสภา','65220',NULL,NULL),(5436,'650807',691,'ท่าหมื่นราม','65130',NULL,NULL),(5437,'650808',691,'วังนกแอ่น','65130',NULL,NULL),(5438,'650809',691,'หนองพระ','65130',NULL,NULL),(5439,'650810',691,'ชัยนาม','65130',NULL,NULL),(5440,'650811',691,'ดินทอง','65130',NULL,NULL),(5441,'650901',692,'ชมพู','65190',NULL,NULL),(5442,'650902',692,'บ้านมุง','65190',NULL,NULL),(5443,'650903',692,'ไทรย้อย','65190',NULL,NULL),(5444,'650904',692,'วังโพรง','65190',NULL,NULL),(5445,'650905',692,'บ้านน้อยซุ้มขี้เหล็ก','65190',NULL,NULL),(5446,'650906',692,'เนินมะปราง','65190',NULL,NULL),(5447,'650907',692,'วังยาง','65190',NULL,NULL),(5448,'660101',693,'ในเมือง','66000',NULL,NULL),(5449,'660102',693,'ไผ่ขวาง','66000',NULL,NULL),(5450,'660103',693,'ย่านยาว','66000',NULL,NULL),(5451,'660104',693,'ท่าฬอ','66000',NULL,NULL),(5452,'660105',693,'ปากทาง','66000',NULL,NULL),(5453,'660106',693,'คลองคะเชนทร์','66000',NULL,NULL),(5454,'660107',693,'โรงช้าง','66000',NULL,NULL),(5455,'660108',693,'เมืองเก่า','66000',NULL,NULL),(5456,'660109',693,'ท่าหลวง','66000',NULL,NULL),(5457,'660110',693,'บ้านบุ่ง','66000',NULL,NULL),(5458,'660111',693,'ฆะมัง','66000',NULL,NULL),(5459,'660112',693,'ดงป่าคำ','66170',NULL,NULL),(5460,'660113',693,'หัวดง','66170',NULL,NULL),(5461,'660115',693,'ป่ามะคาบ','66000',NULL,NULL),(5462,'660119',693,'สายคำโห้','66000',NULL,NULL),(5463,'660120',693,'ดงกลาง','66170',NULL,NULL),(5464,'660201',694,'วังทรายพูน','66180',NULL,NULL),(5465,'660202',694,'หนองปลาไหล','66180',NULL,NULL),(5466,'660203',694,'หนองพระ','66180',NULL,NULL),(5467,'660204',694,'หนองปล้อง','66180',NULL,NULL),(5468,'660301',695,'โพธิ์ประทับช้าง','66190',NULL,NULL),(5469,'660302',695,'ไผ่ท่าโพ','66190',NULL,NULL),(5470,'660303',695,'วังจิก','66190',NULL,NULL),(5471,'660304',695,'ไผ่รอบ','66190',NULL,NULL),(5472,'660305',695,'ดงเสือเหลือง','66190',NULL,NULL),(5473,'660306',695,'เนินสว่าง','66190',NULL,NULL),(5474,'660307',695,'ทุ่งใหญ่','66190',NULL,NULL),(5475,'660401',696,'ตะพานหิน','66110',NULL,NULL),(5476,'660402',696,'งิ้วราย','66110',NULL,NULL),(5477,'660403',696,'ห้วยเกตุ','66110',NULL,NULL),(5478,'660404',696,'ไทรโรงโขน','66110',NULL,NULL),(5479,'660405',696,'หนองพยอม','66110',NULL,NULL),(5480,'660406',696,'ทุ่งโพธิ์','66150',NULL,NULL),(5481,'660407',696,'ดงตะขบ','66110',NULL,NULL),(5482,'660408',696,'คลองคูณ','66110',NULL,NULL),(5483,'660409',696,'วังสำโรง','66110',NULL,NULL),(5484,'660410',696,'วังหว้า','66110',NULL,NULL),(5485,'660411',696,'วังหลุม','66150',NULL,NULL),(5486,'660412',696,'ทับหมัน','66110',NULL,NULL),(5487,'660413',696,'ไผ่หลวง','66110',NULL,NULL),(5488,'660502',697,'บางไผ่','66120',NULL,NULL),(5489,'660503',697,'หอไกร','66120',NULL,NULL),(5490,'660504',697,'เนินมะกอก','66120',NULL,NULL),(5491,'660505',697,'วังสำโรง','66120',NULL,NULL),(5492,'660506',697,'ภูมิ','66120',NULL,NULL),(5493,'660507',697,'วังกรด','66120',NULL,NULL),(5494,'660508',697,'ห้วยเขน','66120',NULL,NULL),(5495,'660509',697,'วังตะกู','66210',NULL,NULL),(5496,'660514',697,'ลำประดา','66120',NULL,NULL),(5497,'660601',698,'โพทะเล','66130',NULL,NULL),(5498,'660602',698,'ท้ายน้ำ','66130',NULL,NULL),(5499,'660603',698,'ทะนง','66130',NULL,NULL),(5500,'660604',698,'ท่าบัว','66130',NULL,NULL),(5501,'660605',698,'ทุ่งน้อย','66130',NULL,NULL),(5502,'660606',698,'ท่าขมิ้น','66130',NULL,NULL),(5503,'660607',698,'ท่าเสา','66130',NULL,NULL),(5504,'660608',698,'บางคลาน','66130',NULL,NULL),(5505,'660611',698,'ท่านั่ง','66130',NULL,NULL),(5506,'660612',698,'บ้านน้อย','66130',NULL,NULL),(5507,'660613',698,'วัดขวาง','66130',NULL,NULL),(5508,'660701',699,'สามง่าม','66140',NULL,NULL),(5509,'660702',699,'กำแพงดิน','66140',NULL,NULL),(5510,'660703',699,'รังนก','66140',NULL,NULL),(5511,'660706',699,'เนินปอ','66140',NULL,NULL),(5512,'660707',699,'หนองโสน','66140',NULL,NULL),(5513,'660801',700,'ทับคล้อ','66150',NULL,NULL),(5514,'660802',700,'เขาทราย','66230',NULL,NULL),(5515,'660803',700,'เขาเจ็ดลูก','66230',NULL,NULL),(5516,'660804',700,'ท้ายทุ่ง','66150',NULL,NULL),(5517,'660901',701,'สากเหล็ก','66160',NULL,NULL),(5518,'660902',701,'ท่าเยี่ยม','66160',NULL,NULL),(5519,'660903',701,'คลองทราย','66160',NULL,NULL),(5520,'660904',701,'หนองหญ้าไทร','66160',NULL,NULL),(5521,'660905',701,'วังทับไทร','66160',NULL,NULL),(5522,'661001',702,'ห้วยแก้ว','66130',NULL,NULL),(5523,'661002',702,'โพธิ์ไทรงาม','66130',NULL,NULL),(5524,'661003',702,'แหลมรัง','66130',NULL,NULL),(5525,'661004',702,'บางลาย','66130',NULL,NULL),(5526,'661005',702,'บึงนาราง','66130',NULL,NULL),(5527,'661101',703,'วังงิ้วใต้','66210',NULL,NULL),(5528,'661102',703,'วังงิ้ว','66210',NULL,NULL),(5529,'661103',703,'ห้วยร่วม','66210',NULL,NULL),(5530,'661104',703,'ห้วยพุก','66210',NULL,NULL),(5531,'661105',703,'สำนักขุนเณร','66210',NULL,NULL),(5532,'661201',704,'บ้านนา','66140',NULL,NULL),(5533,'661202',704,'บึงบัว','66140',NULL,NULL),(5534,'661203',704,'วังโมกข์','66140',NULL,NULL),(5535,'661204',704,'หนองหลุม','66220',NULL,NULL),(5536,'670101',705,'ในเมือง','67000',NULL,NULL),(5537,'670102',705,'ตะเบาะ','67000',NULL,NULL),(5538,'670103',705,'บ้านโตก','67000',NULL,NULL),(5539,'670104',705,'สะเดียง','67000',NULL,NULL),(5540,'670105',705,'ป่าเลา','67000',NULL,NULL),(5541,'670106',705,'นางั่ว','67000',NULL,NULL),(5542,'670107',705,'ท่าพล','67250',NULL,NULL),(5543,'670108',705,'ดงมูลเหล็ก','67000',NULL,NULL),(5544,'670109',705,'บ้านโคก','67000',NULL,NULL),(5545,'670110',705,'ชอนไพร','67000',NULL,NULL),(5546,'670111',705,'นาป่า','67000',NULL,NULL),(5547,'670112',705,'นายม','67210',NULL,NULL),(5548,'670113',705,'วังชมภู','67210',NULL,NULL),(5549,'670114',705,'น้ำร้อน','67000',NULL,NULL),(5550,'670115',705,'ห้วยสะแก','67210',NULL,NULL),(5551,'670116',705,'ห้วยใหญ่','67000',NULL,NULL),(5552,'670117',705,'ระวิง','67210',NULL,NULL),(5553,'670201',706,'ชนแดน','67150',NULL,NULL),(5554,'670202',706,'ดงขุย','67190',NULL,NULL),(5555,'670203',706,'ท่าข้าม','67150',NULL,NULL),(5556,'670204',706,'พุทธบาท','67150',NULL,NULL),(5557,'670205',706,'ลาดแค','67150',NULL,NULL),(5558,'670206',706,'บ้านกล้วย','67190',NULL,NULL),(5559,'670208',706,'ซับพุทรา','67150',NULL,NULL),(5560,'670209',706,'ตะกุดไร','67190',NULL,NULL),(5561,'670210',706,'ศาลาลาย','67150',NULL,NULL),(5562,'670302',707,'วัดป่า','67110',NULL,NULL),(5563,'670303',707,'ตาลเดี่ยว','67110',NULL,NULL),(5564,'670304',707,'ฝายนาแซง','67110',NULL,NULL),(5565,'670305',707,'หนองสว่าง','67110',NULL,NULL),(5566,'670306',707,'น้ำเฮี้ย','67110',NULL,NULL),(5567,'670307',707,'สักหลง','67110',NULL,NULL),(5568,'670308',707,'ท่าอิบุญ','67110',NULL,NULL),(5569,'670309',707,'บ้านโสก','67110',NULL,NULL),(5570,'670310',707,'บ้านติ้ว','67110',NULL,NULL),(5571,'670311',707,'ห้วยไร่','67110',NULL,NULL),(5572,'670312',707,'น้ำก้อ','67110',NULL,NULL),(5573,'670313',707,'ปากช่อง','67110',NULL,NULL),(5574,'670314',707,'น้ำชุน','67110',NULL,NULL),(5575,'670315',707,'หนองไขว่','67110',NULL,NULL),(5576,'670316',707,'ลานบ่า','67110',NULL,NULL),(5577,'670317',707,'บุ่งคล้า','67110',NULL,NULL),(5578,'670318',707,'บุ่งน้ำเต้า','67110',NULL,NULL),(5579,'670319',707,'บ้านกลาง','67110',NULL,NULL),(5580,'670320',707,'ช้างตะลูด','67110',NULL,NULL),(5581,'670321',707,'บ้านไร่','67110',NULL,NULL),(5582,'670322',707,'ปากดุก','67110',NULL,NULL),(5583,'670323',707,'บ้านหวาย','67110',NULL,NULL),(5584,'670401',708,'หล่มเก่า','67120',NULL,NULL),(5585,'670402',708,'นาซำ','67120',NULL,NULL),(5586,'670403',708,'หินฮาว','67120',NULL,NULL),(5587,'670404',708,'บ้านเนิน','67120',NULL,NULL),(5588,'670405',708,'ศิลา','67120',NULL,NULL),(5589,'670406',708,'นาแซง','67120',NULL,NULL),(5590,'670407',708,'วังบาล','67120',NULL,NULL),(5591,'670408',708,'นาเกาะ','67120',NULL,NULL),(5592,'670409',708,'ตาดกลอย','67120',NULL,NULL),(5593,'670501',709,'ท่าโรง','67130',NULL,NULL),(5594,'670502',709,'สระประดู่','67130',NULL,NULL),(5595,'670503',709,'สามแยก','67130',NULL,NULL),(5596,'670504',709,'โคกปรง','67130',NULL,NULL),(5597,'670505',709,'น้ำร้อน','67130',NULL,NULL),(5598,'670506',709,'บ่อรัง','67130',NULL,NULL),(5599,'670507',709,'พุเตย','67180',NULL,NULL),(5600,'670508',709,'พุขาม','67180',NULL,NULL),(5601,'670509',709,'ภูน้ำหยด','67180',NULL,NULL),(5602,'670510',709,'ซับสมบูรณ์','67180',NULL,NULL),(5603,'670511',709,'บึงกระจับ','67130',NULL,NULL),(5604,'670512',709,'วังใหญ่','67180',NULL,NULL),(5605,'670513',709,'ยางสาว','67130',NULL,NULL),(5606,'670514',709,'ซับน้อย','67180',NULL,NULL),(5607,'670601',710,'ศรีเทพ','67170',NULL,NULL),(5608,'670602',710,'สระกรวด','67170',NULL,NULL),(5609,'670603',710,'คลองกระจัง','67170',NULL,NULL),(5610,'670604',710,'นาสนุ่น','67170',NULL,NULL),(5611,'670605',710,'โคกสะอาด','67170',NULL,NULL),(5612,'670606',710,'หนองย่างทอย','67170',NULL,NULL),(5613,'670607',710,'ประดู่งาม','67170',NULL,NULL),(5614,'670701',711,'กองทูล','67140',NULL,NULL),(5615,'670702',711,'นาเฉลียง','67220',NULL,NULL),(5616,'670703',711,'บ้านโภชน์','67140',NULL,NULL),(5617,'670704',711,'ท่าแดง','67140',NULL,NULL),(5618,'670705',711,'เพชรละคร','67140',NULL,NULL),(5619,'670706',711,'บ่อไทย','67140',NULL,NULL),(5620,'670707',711,'ห้วยโป่ง','67220',NULL,NULL),(5621,'670708',711,'วังท่าดี','67140',NULL,NULL),(5622,'670709',711,'บัววัฒนา','67140',NULL,NULL),(5623,'670710',711,'หนองไผ่','67140',NULL,NULL),(5624,'670711',711,'วังโบสถ์','67140',NULL,NULL),(5625,'670712',711,'ยางงาม','67220',NULL,NULL),(5626,'670713',711,'ท่าด้วง','67140',NULL,NULL),(5627,'670801',712,'ซับสมอทอด','67160',NULL,NULL),(5628,'670802',712,'ซับไม้แดง','67160',NULL,NULL),(5629,'670803',712,'หนองแจง','67160',NULL,NULL),(5630,'670804',712,'กันจุ','67160',NULL,NULL),(5631,'670805',712,'วังพิกุล','67230',NULL,NULL),(5632,'670806',712,'พญาวัง','67160',NULL,NULL),(5633,'670807',712,'ศรีมงคล','67160',NULL,NULL),(5634,'670808',712,'สระแก้ว','67160',NULL,NULL),(5635,'670809',712,'บึงสามพัน','67160',NULL,NULL),(5636,'670901',713,'น้ำหนาว','67260',NULL,NULL),(5637,'670902',713,'หลักด่าน','67260',NULL,NULL),(5638,'670903',713,'วังกวาง','67260',NULL,NULL),(5639,'670904',713,'โคกมน','67260',NULL,NULL),(5640,'671001',714,'วังโป่ง','67240',NULL,NULL),(5641,'671002',714,'ท้ายดง','67240',NULL,NULL),(5642,'671003',714,'ซับเปิบ','67240',NULL,NULL),(5643,'671004',714,'วังหิน','67240',NULL,NULL),(5644,'671005',714,'วังศาล','67240',NULL,NULL),(5645,'671101',715,'ทุ่งสมอ','67270',NULL,NULL),(5646,'671102',715,'แคมป์สน','67280',NULL,NULL),(5647,'671103',715,'เขาค้อ','67270',NULL,NULL),(5648,'671104',715,'ริมสีม่วง','67270',NULL,NULL),(5649,'671105',715,'สะเดาะพง','67270',NULL,NULL),(5650,'671106',715,'หนองแม่นา','67270',NULL,NULL),(5651,'671107',715,'เข็กน้อย','67280',NULL,NULL),(5652,'700101',716,'หน้าเมือง','70000',NULL,NULL),(5653,'700102',716,'เจดีย์หัก','70000',NULL,NULL),(5654,'700103',716,'ดอนตะโก','70000',NULL,NULL),(5655,'700104',716,'หนองกลางนา','70000',NULL,NULL),(5656,'700105',716,'ห้วยไผ่','70000',NULL,NULL),(5657,'700106',716,'คุ้งน้ำวน','70000',NULL,NULL),(5658,'700107',716,'คุ้งกระถิน','70000',NULL,NULL),(5659,'700108',716,'อ่างทอง','70000',NULL,NULL),(5660,'700109',716,'โคกหม้อ','70000',NULL,NULL),(5661,'700110',716,'สามเรือน','70000',NULL,NULL),(5662,'700111',716,'พิกุลทอง','70000',NULL,NULL),(5663,'700112',716,'น้ำพุ','70000',NULL,NULL),(5664,'700113',716,'ดอนแร่','70000',NULL,NULL),(5665,'700114',716,'หินกอง','70000',NULL,NULL),(5666,'700115',716,'เขาแร้ง','70000',NULL,NULL),(5667,'700116',716,'เกาะพลับพลา','70000',NULL,NULL),(5668,'700117',716,'หลุมดิน','70000',NULL,NULL),(5669,'700118',716,'บางป่า','70000',NULL,NULL),(5670,'700119',716,'พงสวาย','70000',NULL,NULL),(5671,'700120',716,'คูบัว','70000',NULL,NULL),(5672,'700121',716,'ท่าราบ','70000',NULL,NULL),(5673,'700122',716,'บ้านไร่','70000',NULL,NULL),(5674,'700201',717,'จอมบึง','70150',NULL,NULL),(5675,'700202',717,'ปากช่อง','70150',NULL,NULL),(5676,'700203',717,'เบิกไพร','70150',NULL,NULL),(5677,'700204',717,'ด่านทับตะโก','70150',NULL,NULL),(5678,'700205',717,'แก้มอ้น','70150',NULL,NULL),(5679,'700206',717,'รางบัว','70150',NULL,NULL),(5680,'700301',718,'สวนผึ้ง','70180',NULL,NULL),(5681,'700302',718,'ป่าหวาย','70180',NULL,NULL),(5682,'700304',718,'ท่าเคย','70180',NULL,NULL),(5683,'700307',718,'ตะนาวศรี','70180',NULL,NULL),(5684,'700401',719,'ดำเนินสะดวก','70130',NULL,NULL),(5685,'700402',719,'ประสาทสิทธิ์','70210',NULL,NULL),(5686,'700403',719,'ศรีสุราษฎร์','70130',NULL,NULL),(5687,'700404',719,'ตาหลวง','70130',NULL,NULL),(5688,'700405',719,'ดอนกรวย','70130',NULL,NULL),(5689,'700406',719,'ดอนคลัง','70130',NULL,NULL),(5690,'700407',719,'บัวงาม','70210',NULL,NULL),(5691,'700408',719,'บ้านไร่','70130',NULL,NULL),(5692,'700409',719,'แพงพวย','70130',NULL,NULL),(5693,'700410',719,'สี่หมื่น','70130',NULL,NULL),(5694,'700411',719,'ท่านัด','70130',NULL,NULL),(5695,'700412',719,'ขุนพิทักษ์','70130',NULL,NULL),(5696,'700413',719,'ดอนไผ่','70130',NULL,NULL),(5697,'700501',720,'บ้านโป่ง','70110',NULL,NULL),(5698,'700502',720,'ท่าผา','70110',NULL,NULL),(5699,'700503',720,'กรับใหญ่','70190',NULL,NULL),(5700,'700504',720,'ปากแรต','70110',NULL,NULL),(5701,'700505',720,'หนองกบ','70110',NULL,NULL),(5702,'700506',720,'หนองอ้อ','70110',NULL,NULL),(5703,'700507',720,'ดอนกระเบื้อง','70110',NULL,NULL),(5704,'700508',720,'สวนกล้วย','70110',NULL,NULL),(5705,'700509',720,'นครชุมน์','70110',NULL,NULL),(5706,'700510',720,'บ้านม่วง','70110',NULL,NULL),(5707,'700511',720,'คุ้งพยอม','70110',NULL,NULL),(5708,'700512',720,'หนองปลาหมอ','70110',NULL,NULL),(5709,'700513',720,'เขาขลุง','70110',NULL,NULL),(5710,'700514',720,'เบิกไพร','70110',NULL,NULL),(5711,'700515',720,'ลาดบัวขาว','70110',NULL,NULL),(5712,'700601',721,'บางแพ','70160',NULL,NULL),(5713,'700602',721,'วังเย็น','70160',NULL,NULL),(5714,'700603',721,'หัวโพ','70160',NULL,NULL),(5715,'700604',721,'วัดแก้ว','70160',NULL,NULL),(5716,'700605',721,'ดอนใหญ่','70160',NULL,NULL),(5717,'700606',721,'ดอนคา','70160',NULL,NULL),(5718,'700607',721,'โพหัก','70160',NULL,NULL),(5719,'700701',722,'โพธาราม','70120',NULL,NULL),(5720,'700702',722,'ดอนกระเบื้อง','70120',NULL,NULL),(5721,'700703',722,'หนองโพ','70120',NULL,NULL),(5722,'700704',722,'บ้านเลือก','70120',NULL,NULL),(5723,'700705',722,'คลองตาคต','70120',NULL,NULL),(5724,'700706',722,'บ้านฆ้อง','70120',NULL,NULL),(5725,'700707',722,'บ้านสิงห์','70120',NULL,NULL),(5726,'700708',722,'ดอนทราย','70120',NULL,NULL),(5727,'700709',722,'เจ็ดเสมียน','70120',NULL,NULL),(5728,'700710',722,'คลองข่อย','70120',NULL,NULL),(5729,'700711',722,'ชำแระ','70120',NULL,NULL),(5730,'700712',722,'สร้อยฟ้า','70120',NULL,NULL),(5731,'700713',722,'ท่าชุมพล','70120',NULL,NULL),(5732,'700714',722,'บางโตนด','70120',NULL,NULL),(5733,'700715',722,'เตาปูน','70120',NULL,NULL),(5734,'700716',722,'นางแก้ว','70120',NULL,NULL),(5735,'700717',722,'ธรรมเสน','70120',NULL,NULL),(5736,'700718',722,'เขาชะงุ้ม','70120',NULL,NULL),(5737,'700719',722,'หนองกวาง','70120',NULL,NULL),(5738,'700801',723,'ทุ่งหลวง','70140',NULL,NULL),(5739,'700802',723,'วังมะนาว','70140',NULL,NULL),(5740,'700803',723,'ดอนทราย','70140',NULL,NULL),(5741,'700804',723,'หนองกระทุ่ม','70140',NULL,NULL),(5742,'700805',723,'ปากท่อ','70140',NULL,NULL),(5743,'700806',723,'ป่าไก่','70140',NULL,NULL),(5744,'700807',723,'วัดยางงาม','70140',NULL,NULL),(5745,'700808',723,'อ่างหิน','70140',NULL,NULL),(5746,'700809',723,'บ่อกระดาน','70140',NULL,NULL),(5747,'700810',723,'ยางหัก','70140',NULL,NULL),(5748,'700811',723,'วันดาว','70140',NULL,NULL),(5749,'700812',723,'ห้วยยางโทน','70140',NULL,NULL),(5750,'700901',724,'เกาะศาลพระ','70170',NULL,NULL),(5751,'700902',724,'จอมประทัด','70170',NULL,NULL),(5752,'700903',724,'วัดเพลง','70170',NULL,NULL),(5753,'701001',725,'บ้านคา','70180',NULL,NULL),(5754,'701002',725,'บ้านบึง','70180',NULL,NULL),(5755,'701003',725,'หนองพันจันทร์','70180',NULL,NULL),(5756,'710103',726,'ปากแพรก','71000',NULL,NULL),(5757,'710104',726,'ท่ามะขาม','71000',NULL,NULL),(5758,'710105',726,'แก่งเสี้ยน','71000',NULL,NULL),(5759,'710106',726,'หนองบัว','71190',NULL,NULL),(5760,'710107',726,'ลาดหญ้า','71190',NULL,NULL),(5761,'710108',726,'วังด้ง','71190',NULL,NULL),(5762,'710109',726,'ช่องสะเดา','71190',NULL,NULL),(5763,'710110',726,'หนองหญ้า','71000',NULL,NULL),(5764,'710111',726,'เกาะสำโรง','71000',NULL,NULL),(5765,'710113',726,'บ้านเก่า','71000',NULL,NULL),(5766,'710116',726,'วังเย็น','71000',NULL,NULL),(5767,'710201',727,'ลุ่มสุ่ม','71150',NULL,NULL),(5768,'710202',727,'ท่าเสา','71150',NULL,NULL),(5769,'710203',727,'สิงห์','71150',NULL,NULL),(5770,'710204',727,'ไทรโยค','71150',NULL,NULL),(5771,'710205',727,'วังกระแจะ','71150',NULL,NULL),(5772,'710206',727,'ศรีมงคล','71150',NULL,NULL),(5773,'710207',727,'บ้องตี้','71150',NULL,NULL),(5774,'710301',728,'บ่อพลอย','71160',NULL,NULL),(5775,'710302',728,'หนองกุ่ม','71160',NULL,NULL),(5776,'710303',728,'หนองรี','71220',NULL,NULL),(5777,'710305',728,'หลุมรัง','71160',NULL,NULL),(5778,'710308',728,'ช่องด่าน','71160',NULL,NULL),(5779,'710401',729,'นาสวน','71250',NULL,NULL),(5780,'710402',729,'ด่านแม่แฉลบ','71250',NULL,NULL),(5781,'710403',729,'หนองเป็ด','71250',NULL,NULL),(5782,'710404',729,'ท่ากระดาน','71250',NULL,NULL),(5783,'710405',729,'เขาโจด','71220',NULL,NULL),(5784,'710406',729,'แม่กระบุง','71250',NULL,NULL),(5785,'710501',730,'พงตึก','71120',NULL,NULL),(5786,'710502',730,'ยางม่วง','71120',NULL,NULL),(5787,'710503',730,'ดอนชะเอม','71130',NULL,NULL),(5788,'710504',730,'ท่าไม้','71120',NULL,NULL),(5789,'710505',730,'ตะคร้ำเอน','71130',NULL,NULL),(5790,'710506',730,'ท่ามะกา','71120',NULL,NULL),(5791,'710507',730,'ท่าเรือ (เทศบาลเมืองพระแท่น)','71130',NULL,NULL),(5792,'710508',730,'โคกตะบอง','71120',NULL,NULL),(5793,'710509',730,'ดอนขมิ้น','71120',NULL,NULL),(5794,'710510',730,'อุโลกสี่หมื่น','71130',NULL,NULL),(5795,'710511',730,'เขาสามสิบหาบ','71120',NULL,NULL),(5796,'710512',730,'พระแท่น','71130',NULL,NULL),(5797,'710513',730,'หวายเหนียว','71120',NULL,NULL),(5798,'710514',730,'แสนตอ','71130',NULL,NULL),(5799,'710515',730,'สนามแย้','70190',NULL,NULL),(5800,'710516',730,'ท่าเสา','71120',NULL,NULL),(5801,'710517',730,'หนองลาน','71130',NULL,NULL),(5802,'710601',731,'ท่าม่วง','71110',NULL,NULL),(5803,'710602',731,'วังขนาย','71110',NULL,NULL),(5804,'710603',731,'วังศาลา','71110',NULL,NULL),(5805,'710604',731,'ท่าล้อ','71110',NULL,NULL),(5806,'710605',731,'หนองขาว','71110',NULL,NULL),(5807,'710606',731,'ทุ่งทอง','71110',NULL,NULL),(5808,'710607',731,'เขาน้อย','71110',NULL,NULL),(5809,'710608',731,'ม่วงชุม','71110',NULL,NULL),(5810,'710609',731,'บ้านใหม่','71110',NULL,NULL),(5811,'710610',731,'พังตรุ','71110',NULL,NULL),(5812,'710611',731,'ท่าตะคร้อ','71130',NULL,NULL),(5813,'710612',731,'รางสาลี่','71110',NULL,NULL),(5814,'710613',731,'หนองตากยา','71110',NULL,NULL),(5815,'710701',732,'ท่าขนุน','71180',NULL,NULL),(5816,'710702',732,'ปิล๊อก','71180',NULL,NULL),(5817,'710703',732,'หินดาด','71180',NULL,NULL),(5818,'710704',732,'ลิ่นถิ่น','71180',NULL,NULL),(5819,'710705',732,'ชะแล','71180',NULL,NULL),(5820,'710706',732,'ห้วยเขย่ง','71180',NULL,NULL),(5821,'710707',732,'สหกรณ์นิคม','71180',NULL,NULL),(5822,'710801',733,'หนองลู','71240',NULL,NULL),(5823,'710802',733,'ปรังเผล','71240',NULL,NULL),(5824,'710803',733,'ไล่โว่','71240',NULL,NULL),(5825,'710901',734,'พนมทวน','71140',NULL,NULL),(5826,'710902',734,'หนองโรง','71140',NULL,NULL),(5827,'710903',734,'ทุ่งสมอ','71140',NULL,NULL),(5828,'710904',734,'ดอนเจดีย์','71140',NULL,NULL),(5829,'710905',734,'พังตรุ','71140',NULL,NULL),(5830,'710906',734,'รางหวาย','71170',NULL,NULL),(5831,'710911',734,'หนองสาหร่าย','71140',NULL,NULL),(5832,'710912',734,'ดอนตาเพชร','71140',NULL,NULL),(5833,'711001',735,'เลาขวัญ','71210',NULL,NULL),(5834,'711002',735,'หนองโสน','71210',NULL,NULL),(5835,'711003',735,'หนองประดู่','71210',NULL,NULL),(5836,'711004',735,'หนองปลิง','71210',NULL,NULL),(5837,'711005',735,'หนองนกแก้ว','71210',NULL,NULL),(5838,'711006',735,'ทุ่งกระบ่ำ','71210',NULL,NULL),(5839,'711007',735,'หนองฝ้าย','71210',NULL,NULL),(5840,'711101',736,'ด่านมะขามเตี้ย','71260',NULL,NULL),(5841,'711102',736,'กลอนโด','71260',NULL,NULL),(5842,'711103',736,'จรเข้เผือก','71260',NULL,NULL),(5843,'711104',736,'หนองไผ่','71260',NULL,NULL),(5844,'711201',737,'หนองปรือ','71220',NULL,NULL),(5845,'711202',737,'หนองปลาไหล','71220',NULL,NULL),(5846,'711203',737,'สมเด็จเจริญ','71220',NULL,NULL),(5847,'711301',738,'ห้วยกระเจา','71170',NULL,NULL),(5848,'711302',738,'วังไผ่','71170',NULL,NULL),(5849,'711303',738,'ดอนแสลบ','71170',NULL,NULL),(5850,'711304',738,'สระลงเรือ','71170',NULL,NULL),(5851,'720101',739,'ท่าพี่เลี้ยง','72000',NULL,NULL),(5852,'720102',739,'รั้วใหญ่','72000',NULL,NULL),(5853,'720103',739,'ทับตีเหล็ก','72000',NULL,NULL),(5854,'720104',739,'ท่าระหัด','72000',NULL,NULL),(5855,'720105',739,'ไผ่ขวาง','72000',NULL,NULL),(5856,'720106',739,'โคกโคเฒ่า','72000',NULL,NULL),(5857,'720107',739,'ดอนตาล','72000',NULL,NULL),(5858,'720108',739,'ดอนมะสังข์','72000',NULL,NULL),(5859,'720109',739,'พิหารแดง','72000',NULL,NULL),(5860,'720110',739,'ดอนกำยาน','72000',NULL,NULL),(5861,'720111',739,'ดอนโพธิ์ทอง','72000',NULL,NULL),(5862,'720112',739,'บ้านโพธิ์','72000',NULL,NULL),(5863,'720113',739,'สระแก้ว','72230',NULL,NULL),(5864,'720114',739,'ตลิ่งชัน','72230',NULL,NULL),(5865,'720115',739,'บางกุ้ง','72210',NULL,NULL),(5866,'720116',739,'ศาลาขาว','72210',NULL,NULL),(5867,'720117',739,'สวนแตง','72210',NULL,NULL),(5868,'720118',739,'สนามชัย','72000',NULL,NULL),(5869,'720119',739,'โพธิ์พระยา','72000',NULL,NULL),(5870,'720120',739,'สนามคลี','72230',NULL,NULL),(5871,'720201',740,'เขาพระ','72120',NULL,NULL),(5872,'720202',740,'เดิมบาง','72120',NULL,NULL),(5873,'720203',740,'นางบวช','72120',NULL,NULL),(5874,'720204',740,'เขาดิน','72120',NULL,NULL),(5875,'720205',740,'ปากน้ำ','72120',NULL,NULL),(5876,'720206',740,'ทุ่งคลี','72120',NULL,NULL),(5877,'720207',740,'โคกช้าง','72120',NULL,NULL),(5878,'720208',740,'หัวเขา','72120',NULL,NULL),(5879,'720209',740,'หัวนา','72120',NULL,NULL),(5880,'720210',740,'บ่อกรุ','72120',NULL,NULL),(5881,'720211',740,'วังศรีราช','72120',NULL,NULL),(5882,'720213',740,'ยางนอน','72120',NULL,NULL),(5883,'720214',740,'หนองกระทุ่ม','72120',NULL,NULL),(5884,'720301',741,'หนองมะค่าโมง','72180',NULL,NULL),(5885,'720302',741,'ด่านช้าง','72180',NULL,NULL),(5886,'720303',741,'ห้วยขมิ้น','72180',NULL,NULL),(5887,'720304',741,'องค์พระ','72180',NULL,NULL),(5888,'720305',741,'วังคัน','72180',NULL,NULL),(5889,'720306',741,'นิคมกระเสียว','72180',NULL,NULL),(5890,'720307',741,'วังยาว','72180',NULL,NULL),(5891,'720401',742,'โคกคราม','72150',NULL,NULL),(5892,'720402',742,'บางปลาม้า','72150',NULL,NULL),(5893,'720403',742,'ตะค่า','72150',NULL,NULL),(5894,'720404',742,'บางใหญ่','72150',NULL,NULL),(5895,'720405',742,'กฤษณา','72150',NULL,NULL),(5896,'720406',742,'สาลี','72150',NULL,NULL),(5897,'720407',742,'ไผ่กองดิน','72150',NULL,NULL),(5898,'720408',742,'องครักษ์','72150',NULL,NULL),(5899,'720409',742,'จรเข้ใหญ่','72150',NULL,NULL),(5900,'720410',742,'บ้านแหลม','72150',NULL,NULL),(5901,'720411',742,'มะขามล้ม','72150',NULL,NULL),(5902,'720412',742,'วังน้ำเย็น','72150',NULL,NULL),(5903,'720413',742,'วัดโบสถ์','72150',NULL,NULL),(5904,'720414',742,'วัดดาว','72150',NULL,NULL),(5905,'720501',743,'ศรีประจันต์','72140',NULL,NULL),(5906,'720502',743,'บ้านกร่าง','72140',NULL,NULL),(5907,'720503',743,'มดแดง','72140',NULL,NULL),(5908,'720504',743,'บางงาม','72140',NULL,NULL),(5909,'720505',743,'ดอนปรู','72140',NULL,NULL),(5910,'720506',743,'ปลายนา','72140',NULL,NULL),(5911,'720507',743,'วังหว้า','72140',NULL,NULL),(5912,'720508',743,'วังน้ำซับ','72140',NULL,NULL),(5913,'720509',743,'วังยาง','72140',NULL,NULL),(5914,'720601',744,'ดอนเจดีย์','72170',NULL,NULL),(5915,'720602',744,'หนองสาหร่าย','72170',NULL,NULL),(5916,'720603',744,'ไร่รถ','72170',NULL,NULL),(5917,'720604',744,'สระกระโจม','72250',NULL,NULL),(5918,'720605',744,'ทะเลบก','72250',NULL,NULL),(5919,'720701',745,'สองพี่น้อง','72110',NULL,NULL),(5920,'720702',745,'บางเลน','72110',NULL,NULL),(5921,'720703',745,'บางตาเถร','72110',NULL,NULL),(5922,'720704',745,'บางตะเคียน','72110',NULL,NULL),(5923,'720705',745,'บ้านกุ่ม','72110',NULL,NULL),(5924,'720706',745,'หัวโพธิ์','72110',NULL,NULL),(5925,'720707',745,'บางพลับ','72110',NULL,NULL),(5926,'720708',745,'เนินพระปรางค์','72110',NULL,NULL),(5927,'720709',745,'บ้านช้าง','72110',NULL,NULL),(5928,'720710',745,'ต้นตาล','72110',NULL,NULL),(5929,'720711',745,'ศรีสำราญ','72110',NULL,NULL),(5930,'720712',745,'ทุ่งคอก','72190',NULL,NULL),(5931,'720713',745,'หนองบ่อ','72110',NULL,NULL),(5932,'720714',745,'บ่อสุพรรณ','72190',NULL,NULL),(5933,'720715',745,'ดอนมะนาว','72110',NULL,NULL),(5934,'720801',746,'ย่านยาว','72130',NULL,NULL),(5935,'720802',746,'วังลึก','72130',NULL,NULL),(5936,'720803',746,'สามชุก','72130',NULL,NULL),(5937,'720804',746,'หนองผักนาก','72130',NULL,NULL),(5938,'720805',746,'บ้านสระ','72130',NULL,NULL),(5939,'720806',746,'หนองสะเดา','72130',NULL,NULL),(5940,'720807',746,'กระเสียว','72130',NULL,NULL),(5941,'720901',747,'อู่ทอง','72160',NULL,NULL),(5942,'720902',747,'สระยายโสม','72220',NULL,NULL),(5943,'720903',747,'จรเข้สามพัน','72160',NULL,NULL),(5944,'720904',747,'บ้านดอน','72160',NULL,NULL),(5945,'720905',747,'ยุ้งทะลาย','72160',NULL,NULL),(5946,'720906',747,'ดอนมะเกลือ','72220',NULL,NULL),(5947,'720907',747,'หนองโอ่ง','72160',NULL,NULL),(5948,'720908',747,'ดอนคา','72160',NULL,NULL),(5949,'720909',747,'พลับพลาไชย','72160',NULL,NULL),(5950,'720910',747,'บ้านโข้ง','72160',NULL,NULL),(5951,'720911',747,'เจดีย์','72160',NULL,NULL),(5952,'720912',747,'สระพังลาน','72220',NULL,NULL),(5953,'720913',747,'กระจัน','72160',NULL,NULL),(5954,'721001',748,'หนองหญ้าไซ','72240',NULL,NULL),(5955,'721002',748,'หนองราชวัตร','72240',NULL,NULL),(5956,'721003',748,'หนองโพธิ์','72240',NULL,NULL),(5957,'721004',748,'แจงงาม','72240',NULL,NULL),(5958,'721005',748,'หนองขาม','72240',NULL,NULL),(5959,'721006',748,'ทัพหลวง','72240',NULL,NULL),(5960,'730101',749,'พระปฐมเจดีย์','73000',NULL,NULL),(5961,'730102',749,'บางแขม','73000',NULL,NULL),(5962,'730103',749,'พระประโทน','73000',NULL,NULL),(5963,'730104',749,'ธรรมศาลา','73000',NULL,NULL),(5964,'730105',749,'ตาก้อง','73000',NULL,NULL),(5965,'730106',749,'มาบแค','73000',NULL,NULL),(5966,'730107',749,'สนามจันทร์','73000',NULL,NULL),(5967,'730108',749,'ดอนยายหอม','73000',NULL,NULL),(5968,'730109',749,'ถนนขาด','73000',NULL,NULL),(5969,'730110',749,'บ่อพลับ','73000',NULL,NULL),(5970,'730111',749,'นครปฐม','73000',NULL,NULL),(5971,'730112',749,'วังตะกู','73000',NULL,NULL),(5972,'730113',749,'หนองปากโลง','73000',NULL,NULL),(5973,'730114',749,'สามควายเผือก','73000',NULL,NULL),(5974,'730115',749,'ทุ่งน้อย','73000',NULL,NULL),(5975,'730116',749,'หนองดินแดง','73000',NULL,NULL),(5976,'730117',749,'วังเย็น','73000',NULL,NULL),(5977,'730118',749,'โพรงมะเดื่อ','73000',NULL,NULL),(5978,'730119',749,'ลำพยา','73000',NULL,NULL),(5979,'730120',749,'สระกระเทียม','73000',NULL,NULL),(5980,'730121',749,'สวนป่าน','73000',NULL,NULL),(5981,'730122',749,'ห้วยจรเข้','73000',NULL,NULL),(5982,'730123',749,'ทัพหลวง','73000',NULL,NULL),(5983,'730124',749,'หนองงูเหลือม','73000',NULL,NULL),(5984,'730125',749,'บ้านยาง','73000',NULL,NULL),(5985,'730201',750,'ทุ่งกระพังโหม','73140',NULL,NULL),(5986,'730202',750,'กระตีบ','73180',NULL,NULL),(5987,'730203',750,'ทุ่งลูกนก','73140',NULL,NULL),(5988,'730204',750,'ห้วยขวาง','73140',NULL,NULL),(5989,'730205',750,'ทุ่งขวาง','73140',NULL,NULL),(5990,'730206',750,'สระสี่มุม','73140',NULL,NULL),(5991,'730207',750,'ทุ่งบัว','73140',NULL,NULL),(5992,'730208',750,'ดอนข่อย','73140',NULL,NULL),(5993,'730209',750,'สระพัฒนา','73180',NULL,NULL),(5994,'730210',750,'ห้วยหมอนทอง','73140',NULL,NULL),(5995,'730211',750,'ห้วยม่วง','73180',NULL,NULL),(5996,'730212',750,'กำแพงแสน','73140',NULL,NULL),(5997,'730213',750,'รางพิกุล','73140',NULL,NULL),(5998,'730214',750,'หนองกระทุ่ม','73140',NULL,NULL),(5999,'730215',750,'วังน้ำเขียว','73140',NULL,NULL),(6000,'730301',751,'นครชัยศรี','73120',NULL,NULL),(6001,'730302',751,'บางกระเบา','73120',NULL,NULL),(6002,'730303',751,'วัดแค','73120',NULL,NULL),(6003,'730304',751,'ท่าตำหนัก','73120',NULL,NULL),(6004,'730305',751,'บางแก้ว','73120',NULL,NULL),(6005,'730306',751,'ท่ากระชับ','73120',NULL,NULL),(6006,'730307',751,'ขุนแก้ว','73120',NULL,NULL),(6007,'730308',751,'ท่าพระยา','73120',NULL,NULL),(6008,'730309',751,'พะเนียด','73120',NULL,NULL),(6009,'730310',751,'บางระกำ','73120',NULL,NULL),(6010,'730311',751,'โคกพระเจดีย์','73120',NULL,NULL),(6011,'730312',751,'ศีรษะทอง','73120',NULL,NULL),(6012,'730313',751,'แหลมบัว','73120',NULL,NULL),(6013,'730314',751,'ศรีมหาโพธิ์','73120',NULL,NULL),(6014,'730315',751,'สัมปทวน','73120',NULL,NULL),(6015,'730316',751,'วัดสำโรง','73120',NULL,NULL),(6016,'730317',751,'ดอนแฝก','73120',NULL,NULL),(6017,'730318',751,'ห้วยพลู','73120',NULL,NULL),(6018,'730319',751,'วัดละมุด','73120',NULL,NULL),(6019,'730320',751,'บางพระ','73120',NULL,NULL),(6020,'730321',751,'บางแก้วฟ้า','73120',NULL,NULL),(6021,'730322',751,'ลานตากฟ้า','73120',NULL,NULL),(6022,'730323',751,'งิ้วราย','73120',NULL,NULL),(6023,'730324',751,'ไทยาวาส','73120',NULL,NULL),(6024,'730401',752,'สามง่าม','73150',NULL,NULL),(6025,'730402',752,'ห้วยพระ','73150',NULL,NULL),(6026,'730403',752,'ลำเหย','73150',NULL,NULL),(6027,'730404',752,'ดอนพุทรา','73150',NULL,NULL),(6028,'730405',752,'บ้านหลวง','73150',NULL,NULL),(6029,'730406',752,'ดอนรวก','73150',NULL,NULL),(6030,'730407',752,'ห้วยด้วน','73150',NULL,NULL),(6031,'730408',752,'ลำลูกบัว','73150',NULL,NULL),(6032,'730501',753,'บางเลน','73130',NULL,NULL),(6033,'730502',753,'บางปลา','73130',NULL,NULL),(6034,'730503',753,'บางหลวง','73190',NULL,NULL),(6035,'730504',753,'บางภาษี','73130',NULL,NULL),(6036,'730505',753,'บางระกำ','73130',NULL,NULL),(6037,'730506',753,'บางไทรป่า','73130',NULL,NULL),(6038,'730507',753,'หินมูล','73190',NULL,NULL),(6039,'730508',753,'ไทรงาม','73130',NULL,NULL),(6040,'730509',753,'ดอนตูม','73130',NULL,NULL),(6041,'730510',753,'นิลเพชร','73130',NULL,NULL),(6042,'730511',753,'บัวปากท่า','73130',NULL,NULL),(6043,'730512',753,'คลองนกกระทุง','73130',NULL,NULL),(6044,'730513',753,'นราภิรมย์','73130',NULL,NULL),(6045,'730514',753,'ลำพญา','73130',NULL,NULL),(6046,'730515',753,'ไผ่หูช้าง','73130',NULL,NULL),(6047,'730601',754,'ท่าข้าม','73110',NULL,NULL),(6048,'730602',754,'ทรงคนอง','73210',NULL,NULL),(6049,'730603',754,'หอมเกร็ด','73110',NULL,NULL),(6050,'730604',754,'บางกระทึก','73210',NULL,NULL),(6051,'730605',754,'บางเตย','73210',NULL,NULL),(6052,'730606',754,'สามพราน','73110',NULL,NULL),(6053,'730607',754,'บางช้าง','73110',NULL,NULL),(6054,'730608',754,'ไร่ขิง','73210',NULL,NULL),(6055,'730609',754,'ท่าตลาด','73110',NULL,NULL),(6056,'730610',754,'กระทุ่มล้ม','73220',NULL,NULL),(6057,'730611',754,'คลองใหม่','73110',NULL,NULL),(6058,'730612',754,'ตลาดจินดา','73110',NULL,NULL),(6059,'730613',754,'คลองจินดา','73110',NULL,NULL),(6060,'730614',754,'ยายชา','73110',NULL,NULL),(6061,'730615',754,'บ้านใหม่','73110',NULL,NULL),(6062,'730616',754,'อ้อมใหญ่','73160',NULL,NULL),(6063,'730701',755,'ศาลายา','73170',NULL,NULL),(6064,'730702',755,'คลองโยง','73170',NULL,NULL),(6065,'730703',755,'มหาสวัสดิ์','73170',NULL,NULL),(6066,'740101',756,'มหาชัย','74000',NULL,NULL),(6067,'740102',756,'ท่าฉลอม','74000',NULL,NULL),(6068,'740103',756,'โกรกกราก','74000',NULL,NULL),(6069,'740104',756,'บ้านบ่อ','74000',NULL,NULL),(6070,'740105',756,'บางโทรัด','74000',NULL,NULL),(6071,'740106',756,'กาหลง','74000',NULL,NULL),(6072,'740107',756,'นาโคก','74000',NULL,NULL),(6073,'740108',756,'ท่าจีน','74000',NULL,NULL),(6074,'740109',756,'นาดี','74000',NULL,NULL),(6075,'740110',756,'ท่าทราย','74000',NULL,NULL),(6076,'740111',756,'คอกกระบือ','74000',NULL,NULL),(6077,'740112',756,'บางน้ำจืด','74000',NULL,NULL),(6078,'740113',756,'พันท้ายนรสิงห์','74000',NULL,NULL),(6079,'740114',756,'โคกขาม','74000',NULL,NULL),(6080,'740115',756,'บ้านเกาะ','74000',NULL,NULL),(6081,'740116',756,'บางกระเจ้า','74000',NULL,NULL),(6082,'740117',756,'บางหญ้าแพรก','74000',NULL,NULL),(6083,'740118',756,'ชัยมงคล','74000',NULL,NULL),(6084,'740201',757,'ตลาดกระทุ่มแบน','74110',NULL,NULL),(6085,'740202',757,'อ้อมน้อย','74130',NULL,NULL),(6086,'740203',757,'ท่าไม้','74110',NULL,NULL),(6087,'740204',757,'สวนหลวง','74110',NULL,NULL),(6088,'740205',757,'บางยาง','74110',NULL,NULL),(6089,'740206',757,'คลองมะเดื่อ','74110',NULL,NULL),(6090,'740207',757,'หนองนกไข่','74110',NULL,NULL),(6091,'740208',757,'ดอนไก่ดี','74110',NULL,NULL),(6092,'740209',757,'แคราย','74110',NULL,NULL),(6093,'740210',757,'ท่าเสา','74110',NULL,NULL),(6094,'740301',758,'บ้านแพ้ว','74120',NULL,NULL),(6095,'740302',758,'หลักสาม','74120',NULL,NULL),(6096,'740303',758,'ยกกระบัตร','74120',NULL,NULL),(6097,'740304',758,'โรงเข้','74120',NULL,NULL),(6098,'740305',758,'หนองสองห้อง','74120',NULL,NULL),(6099,'740306',758,'หนองบัว','74120',NULL,NULL),(6100,'740307',758,'หลักสอง','74120',NULL,NULL),(6101,'740308',758,'เจ็ดริ้ว','74120',NULL,NULL),(6102,'740309',758,'คลองตัน','74120',NULL,NULL),(6103,'740310',758,'อำแพง','74120',NULL,NULL),(6104,'740311',758,'สวนส้ม','74120',NULL,NULL),(6105,'740312',758,'เกษตรพัฒนา','74120',NULL,NULL),(6106,'750101',759,'แม่กลอง','75000',NULL,NULL),(6107,'750102',759,'บางขันแตก','75000',NULL,NULL),(6108,'750103',759,'ลาดใหญ่','75000',NULL,NULL),(6109,'750104',759,'บ้านปรก','75000',NULL,NULL),(6110,'750105',759,'บางแก้ว','75000',NULL,NULL),(6111,'750106',759,'ท้ายหาด','75000',NULL,NULL),(6112,'750107',759,'แหลมใหญ่','75000',NULL,NULL),(6113,'750108',759,'คลองเขิน','75000',NULL,NULL),(6114,'750109',759,'คลองโคน','75000',NULL,NULL),(6115,'750110',759,'นางตะเคียน','75000',NULL,NULL),(6116,'750111',759,'บางจะเกร็ง','75000',NULL,NULL),(6117,'750201',760,'กระดังงา','75120',NULL,NULL),(6118,'750202',760,'บางสะแก','75120',NULL,NULL),(6119,'750203',760,'บางยี่รงค์','75120',NULL,NULL),(6120,'750204',760,'โรงหีบ','75120',NULL,NULL),(6121,'750205',760,'บางคนที','75120',NULL,NULL),(6122,'750206',760,'ดอนมะโนรา','75120',NULL,NULL),(6123,'750207',760,'บางพรม','75120',NULL,NULL),(6124,'750208',760,'บางกุ้ง','75120',NULL,NULL),(6125,'750209',760,'จอมปลวก','75120',NULL,NULL),(6126,'750210',760,'บางนกแขวก','75120',NULL,NULL),(6127,'750211',760,'ยายแพง','75120',NULL,NULL),(6128,'750212',760,'บางกระบือ','75120',NULL,NULL),(6129,'750213',760,'บ้านปราโมทย์','75120',NULL,NULL),(6130,'750301',761,'อัมพวา','75110',NULL,NULL),(6131,'750302',761,'สวนหลวง','75110',NULL,NULL),(6132,'750303',761,'ท่าคา','75110',NULL,NULL),(6133,'750304',761,'วัดประดู่','75110',NULL,NULL),(6134,'750305',761,'เหมืองใหม่','75110',NULL,NULL),(6135,'750306',761,'บางช้าง','75110',NULL,NULL),(6136,'750307',761,'แควอ้อม','75110',NULL,NULL),(6137,'750308',761,'ปลายโพงพาง','75110',NULL,NULL),(6138,'750309',761,'บางแค','75110',NULL,NULL),(6139,'750310',761,'แพรกหนามแดง','75110',NULL,NULL),(6140,'750311',761,'ยี่สาร','75110',NULL,NULL),(6141,'750312',761,'บางนางลี่','75110',NULL,NULL),(6142,'760101',762,'ท่าราบ','76000',NULL,NULL),(6143,'760102',762,'คลองกระแซง','76000',NULL,NULL),(6144,'760103',762,'บางจาน','76000',NULL,NULL),(6145,'760104',762,'นาพันสาม','76000',NULL,NULL),(6146,'760105',762,'ธงชัย','76000',NULL,NULL),(6147,'760106',762,'บ้านกุ่ม','76000',NULL,NULL),(6148,'760107',762,'หนองโสน','76000',NULL,NULL),(6149,'760108',762,'ไร่ส้ม','76000',NULL,NULL),(6150,'760109',762,'เวียงคอย','76000',NULL,NULL),(6151,'760110',762,'บางจาก','76000',NULL,NULL),(6152,'760111',762,'บ้านหม้อ','76000',NULL,NULL),(6153,'760112',762,'ต้นมะม่วง','76000',NULL,NULL),(6154,'760113',762,'ช่องสะแก','76000',NULL,NULL),(6155,'760114',762,'นาวุ้ง','76000',NULL,NULL),(6156,'760115',762,'สำมะโรง','76000',NULL,NULL),(6157,'760116',762,'โพพระ','76000',NULL,NULL),(6158,'760117',762,'หาดเจ้าสำราญ','76100',NULL,NULL),(6159,'760118',762,'หัวสะพาน','76000',NULL,NULL),(6160,'760119',762,'ต้นมะพร้าว','76000',NULL,NULL),(6161,'760120',762,'วังตะโก','76000',NULL,NULL),(6162,'760121',762,'โพไร่หวาน','76000',NULL,NULL),(6163,'760122',762,'ดอนยาง','76000',NULL,NULL),(6164,'760123',762,'หนองขนาน','76000',NULL,NULL),(6165,'760124',762,'หนองพลับ','76000',NULL,NULL),(6166,'760201',763,'เขาย้อย','76140',NULL,NULL),(6167,'760202',763,'สระพัง','76140',NULL,NULL),(6168,'760203',763,'บางเค็ม','76140',NULL,NULL),(6169,'760204',763,'ทับคาง','76140',NULL,NULL),(6170,'760205',763,'หนองปลาไหล','76140',NULL,NULL),(6171,'760206',763,'หนองปรง','76140',NULL,NULL),(6172,'760207',763,'หนองชุมพล','76140',NULL,NULL),(6173,'760208',763,'ห้วยโรง','76140',NULL,NULL),(6174,'760209',763,'ห้วยท่าช้าง','76140',NULL,NULL),(6175,'760210',763,'หนองชุมพลเหนือ','76140',NULL,NULL),(6176,'760301',764,'หนองหญ้าปล้อง','76160',NULL,NULL),(6177,'760302',764,'ยางน้ำกลัดเหนือ','76160',NULL,NULL),(6178,'760303',764,'ยางน้ำกลัดใต้','76160',NULL,NULL),(6179,'760304',764,'ท่าตะคร้อ','76160',NULL,NULL),(6180,'760401',765,'ชะอำ','76120',NULL,NULL),(6181,'760402',765,'บางเก่า','76120',NULL,NULL),(6182,'760403',765,'นายาง','76120',NULL,NULL),(6183,'760404',765,'เขาใหญ่','76120',NULL,NULL),(6184,'760405',765,'หนองศาลา','76120',NULL,NULL),(6185,'760406',765,'ห้วยทรายเหนือ','76120',NULL,NULL),(6186,'760407',765,'ไร่ใหม่พัฒนา','76120',NULL,NULL),(6187,'760408',765,'สามพระยา','76120',NULL,NULL),(6188,'760409',765,'ดอนขุนห้วย','76120',NULL,NULL),(6189,'760501',766,'ท่ายาง','76130',NULL,NULL),(6190,'760502',766,'ท่าคอย','76130',NULL,NULL),(6191,'760503',766,'ยางหย่อง','76130',NULL,NULL),(6192,'760504',766,'หนองจอก','76130',NULL,NULL),(6193,'760505',766,'มาบปลาเค้า','76130',NULL,NULL),(6194,'760506',766,'ท่าไม้รวก','76130',NULL,NULL),(6195,'760507',766,'วังไคร้','76130',NULL,NULL),(6196,'760511',766,'กลัดหลวง','76130',NULL,NULL),(6197,'760512',766,'ปึกเตียน','76130',NULL,NULL),(6198,'760513',766,'เขากระปุก','76130',NULL,NULL),(6199,'760514',766,'ท่าแลง','76130',NULL,NULL),(6200,'760515',766,'บ้านในดง','76130',NULL,NULL),(6201,'760601',767,'บ้านลาด','76150',NULL,NULL),(6202,'760602',767,'บ้านหาด','76150',NULL,NULL),(6203,'760603',767,'บ้านทาน','76150',NULL,NULL),(6204,'760604',767,'ตำหรุ','76150',NULL,NULL),(6205,'760605',767,'สมอพลือ','76150',NULL,NULL),(6206,'760606',767,'ไร่มะขาม','76150',NULL,NULL),(6207,'760607',767,'ท่าเสน','76150',NULL,NULL),(6208,'760608',767,'หนองกระเจ็ด','76150',NULL,NULL),(6209,'760609',767,'หนองกะปุ','76150',NULL,NULL),(6210,'760610',767,'ลาดโพธิ์','76150',NULL,NULL),(6211,'760612',767,'ไร่โคก','76150',NULL,NULL),(6212,'760613',767,'โรงเข้','76150',NULL,NULL),(6213,'760614',767,'ไร่สะท้อน','76150',NULL,NULL),(6214,'760615',767,'ห้วยข้อง','76150',NULL,NULL),(6215,'760616',767,'ท่าช้าง','76150',NULL,NULL),(6216,'760617',767,'ถ้ำรงค์','76150',NULL,NULL),(6217,'760618',767,'ห้วยลึก','76150',NULL,NULL),(6218,'760701',768,'บ้านแหลม','76110',NULL,NULL),(6219,'760702',768,'บางขุนไทร','76110',NULL,NULL),(6220,'760703',768,'ปากทะเล','76110',NULL,NULL),(6221,'760704',768,'บางแก้ว','76110',NULL,NULL),(6222,'760705',768,'แหลมผักเบี้ย','76100',NULL,NULL),(6223,'760706',768,'บางตะบูน','76110',NULL,NULL),(6224,'760707',768,'บางตะบูนออก','76110',NULL,NULL),(6225,'760708',768,'บางครก','76110',NULL,NULL),(6226,'760709',768,'ท่าแร้ง','76110',NULL,NULL),(6227,'760710',768,'ท่าแร้งออก','76110',NULL,NULL),(6228,'760801',769,'แก่งกระจาน','76170',NULL,NULL),(6229,'760802',769,'สองพี่น้อง','76170',NULL,NULL),(6230,'760803',769,'วังจันทร์','76170',NULL,NULL),(6231,'760804',769,'ป่าเด็ง','76170',NULL,NULL),(6232,'760805',769,'พุสวรรค์','76170',NULL,NULL),(6233,'760806',769,'ห้วยแม่เพรียง','76170',NULL,NULL),(6234,'770101',770,'ประจวบคีรีขันธ์','77000',NULL,NULL),(6235,'770102',770,'เกาะหลัก','77000',NULL,NULL),(6236,'770103',770,'คลองวาฬ','77000',NULL,NULL),(6237,'770104',770,'ห้วยทราย','77000',NULL,NULL),(6238,'770105',770,'อ่าวน้อย','77000',NULL,NULL),(6239,'770106',770,'บ่อนอก','77210',NULL,NULL),(6240,'770201',771,'กุยบุรี','77150',NULL,NULL),(6241,'770202',771,'กุยเหนือ','77150',NULL,NULL),(6242,'770203',771,'เขาแดง','77150',NULL,NULL),(6243,'770204',771,'ดอนยายหนู','77150',NULL,NULL),(6244,'770206',771,'สามกระทาย','77150',NULL,NULL),(6245,'770207',771,'หาดขาม','77150',NULL,NULL),(6246,'770301',772,'ทับสะแก','77130',NULL,NULL),(6247,'770302',772,'อ่างทอง','77130',NULL,NULL),(6248,'770303',772,'นาหูกวาง','77130',NULL,NULL),(6249,'770304',772,'เขาล้าน','77130',NULL,NULL),(6250,'770305',772,'ห้วยยาง','77130',NULL,NULL),(6251,'770306',772,'แสงอรุณ','77130',NULL,NULL),(6252,'770401',773,'กำเนิดนพคุณ','77140',NULL,NULL),(6253,'770402',773,'พงศ์ประศาสน์','77140',NULL,NULL),(6254,'770403',773,'ร่อนทอง','77230',NULL,NULL),(6255,'770404',773,'ธงชัย','77190',NULL,NULL),(6256,'770405',773,'ชัยเกษม','77190',NULL,NULL),(6257,'770406',773,'ทองมงคล','77230',NULL,NULL),(6258,'770407',773,'แม่รำพึง','77140',NULL,NULL),(6259,'770501',774,'ปากแพรก','77170',NULL,NULL),(6260,'770502',774,'บางสะพาน','77170',NULL,NULL),(6261,'770503',774,'ทรายทอง','77170',NULL,NULL),(6262,'770504',774,'ช้างแรก','77170',NULL,NULL),(6263,'770505',774,'ไชยราช','77170',NULL,NULL),(6264,'770601',775,'ปราณบุรี','77120',NULL,NULL),(6265,'770602',775,'เขาน้อย','77120',NULL,NULL),(6266,'770604',775,'ปากน้ำปราณ','77220',NULL,NULL),(6267,'770607',775,'หนองตาแต้ม','77120',NULL,NULL),(6268,'770608',775,'วังก์พง','77120',NULL,NULL),(6269,'770609',775,'เขาจ้าว','77120',NULL,NULL),(6270,'770701',776,'หัวหิน','77110',NULL,NULL),(6271,'770702',776,'หนองแก','77110',NULL,NULL),(6272,'770703',776,'หินเหล็กไฟ','77110',NULL,NULL),(6273,'770704',776,'หนองพลับ','77110',NULL,NULL),(6274,'770705',776,'ทับใต้','77110',NULL,NULL),(6275,'770706',776,'ห้วยสัตว์ใหญ่','77110',NULL,NULL),(6276,'770707',776,'บึงนคร','77110',NULL,NULL),(6277,'770801',777,'สามร้อยยอด','77120',NULL,NULL),(6278,'770802',777,'ศิลาลอย','77180',NULL,NULL),(6279,'770803',777,'ไร่เก่า','77180',NULL,NULL),(6280,'770804',777,'ศาลาลัย','77180',NULL,NULL),(6281,'770805',777,'ไร่ใหม่','77180',NULL,NULL),(6282,'800101',778,'ในเมือง','80000',NULL,NULL),(6283,'800102',778,'ท่าวัง','80000',NULL,NULL),(6284,'800103',778,'คลัง','80000',NULL,NULL),(6285,'800106',778,'ท่าไร่','80000',NULL,NULL),(6286,'800107',778,'ปากนคร','80000',NULL,NULL),(6287,'800108',778,'นาทราย','80280',NULL,NULL),(6288,'800112',778,'กำแพงเซา','80280',NULL,NULL),(6289,'800113',778,'ไชยมนตรี','80000',NULL,NULL),(6290,'800114',778,'มะม่วงสองต้น','80000',NULL,NULL),(6291,'800115',778,'นาเคียน','80000',NULL,NULL),(6292,'800116',778,'ท่างิ้ว','80280',NULL,NULL),(6293,'800118',778,'โพธิ์เสด็จ','80000',NULL,NULL),(6294,'800119',778,'บางจาก','80330',NULL,NULL),(6295,'800120',778,'ปากพูน','80000',NULL,NULL),(6296,'800121',778,'ท่าซัก','80000',NULL,NULL),(6297,'800122',778,'ท่าเรือ','80290',NULL,NULL),(6298,'800201',779,'พรหมโลก','80320',NULL,NULL),(6299,'800202',779,'บ้านเกาะ','80320',NULL,NULL),(6300,'800203',779,'อินคีรี','80320',NULL,NULL),(6301,'800204',779,'ทอนหงส์','80320',NULL,NULL),(6302,'800205',779,'นาเรียง','80320',NULL,NULL),(6303,'800301',780,'เขาแก้ว','80230',NULL,NULL),(6304,'800302',780,'ลานสกา','80230',NULL,NULL),(6305,'800303',780,'ท่าดี','80230',NULL,NULL),(6306,'800304',780,'กำโลน','80230',NULL,NULL),(6307,'800305',780,'ขุนทะเล','80230',NULL,NULL),(6308,'800401',781,'ฉวาง','80150',NULL,NULL),(6309,'800403',781,'ละอาย','80250',NULL,NULL),(6310,'800404',781,'นาแว','80260',NULL,NULL),(6311,'800405',781,'ไม้เรียง','80150',NULL,NULL),(6312,'800406',781,'กะเปียด','80260',NULL,NULL),(6313,'800407',781,'นากะชะ','80150',NULL,NULL),(6314,'800409',781,'ห้วยปริก','80260',NULL,NULL),(6315,'800410',781,'ไสหร้า','80150',NULL,NULL),(6316,'800415',781,'นาเขลียง','80260',NULL,NULL),(6317,'800416',781,'จันดี','80250',NULL,NULL),(6318,'800501',782,'พิปูน','80270',NULL,NULL),(6319,'800502',782,'กะทูน','80270',NULL,NULL),(6320,'800503',782,'เขาพระ','80270',NULL,NULL),(6321,'800504',782,'ยางค้อม','80270',NULL,NULL),(6322,'800505',782,'ควนกลาง','80270',NULL,NULL),(6323,'800601',783,'เชียรใหญ่','80190',NULL,NULL),(6324,'800603',783,'ท่าขนาน','80190',NULL,NULL),(6325,'800604',783,'บ้านกลาง','80190',NULL,NULL),(6326,'800605',783,'บ้านเนิน','80190',NULL,NULL),(6327,'800606',783,'ไสหมาก','80190',NULL,NULL),(6328,'800607',783,'ท้องลำเจียก','80190',NULL,NULL),(6329,'800610',783,'เสือหึง','80190',NULL,NULL),(6330,'800611',783,'การะเกด','80190',NULL,NULL),(6331,'800612',783,'เขาพระบาท','80190',NULL,NULL),(6332,'800613',783,'แม่เจ้าอยู่หัว','80190',NULL,NULL),(6333,'800701',784,'ชะอวด','80180',NULL,NULL),(6334,'800702',784,'ท่าเสม็ด','80180',NULL,NULL),(6335,'800703',784,'ท่าประจะ','80180',NULL,NULL),(6336,'800704',784,'เคร็ง','80180',NULL,NULL),(6337,'800705',784,'วังอ่าง','80180',NULL,NULL),(6338,'800706',784,'บ้านตูล','80180',NULL,NULL),(6339,'800707',784,'ขอนหาด','80180',NULL,NULL),(6340,'800708',784,'เกาะขันธ์','80180',NULL,NULL),(6341,'800709',784,'ควนหนองหงษ์','80180',NULL,NULL),(6342,'800710',784,'เขาพระทอง','80180',NULL,NULL),(6343,'800711',784,'นางหลง','80180',NULL,NULL),(6344,'800801',785,'ท่าศาลา','80160',NULL,NULL),(6345,'800802',785,'กลาย','80160',NULL,NULL),(6346,'800803',785,'ท่าขึ้น','80160',NULL,NULL),(6347,'800804',785,'หัวตะพาน','80160',NULL,NULL),(6348,'800806',785,'สระแก้ว','80160',NULL,NULL),(6349,'800807',785,'โมคลาน','80160',NULL,NULL),(6350,'800809',785,'ไทยบุรี','80160',NULL,NULL),(6351,'800810',785,'ดอนตะโก','80160',NULL,NULL),(6352,'800811',785,'ตลิ่งชัน','80160',NULL,NULL),(6353,'800813',785,'โพธิ์ทอง','80160',NULL,NULL),(6354,'800901',786,'เทศบาลเมืองทุ่งสง-ปากแพรก','80110',NULL,NULL),(6355,'800902',786,'ชะมาย','80110',NULL,NULL),(6356,'800903',786,'หนองหงส์','80110',NULL,NULL),(6357,'800904',786,'ควนกรด','80110',NULL,NULL),(6358,'800905',786,'นาไม้ไผ่','80110',NULL,NULL),(6359,'800906',786,'นาหลวงเสน','80110',NULL,NULL),(6360,'800907',786,'เขาโร','80110',NULL,NULL),(6361,'800908',786,'กะปาง','80310',NULL,NULL),(6362,'800909',786,'ที่วัง','80110',NULL,NULL),(6363,'800910',786,'น้ำตก','80110',NULL,NULL),(6364,'800911',786,'ถ้ำใหญ่','80110',NULL,NULL),(6365,'800912',786,'นาโพธิ์','80110',NULL,NULL),(6366,'800913',786,'เขาขาว','80110',NULL,NULL),(6367,'801001',787,'นาบอน','80220',NULL,NULL),(6368,'801002',787,'ทุ่งสง','80220',NULL,NULL),(6369,'801003',787,'แก้วแสน','80220',NULL,NULL),(6370,'801101',788,'ท่ายาง','80240',NULL,NULL),(6371,'801102',788,'ทุ่งสัง','80240',NULL,NULL),(6372,'801103',788,'ทุ่งใหญ่','80240',NULL,NULL),(6373,'801104',788,'กุแหระ','80240',NULL,NULL),(6374,'801105',788,'ปริก','80240',NULL,NULL),(6375,'801106',788,'บางรูป','80240',NULL,NULL),(6376,'801107',788,'กรุงหยัน','80240',NULL,NULL),(6377,'801202',789,'คลองน้อย','80330',NULL,NULL),(6378,'801203',789,'ป่าระกำ','80140',NULL,NULL),(6379,'801204',789,'ชะเมา','80330',NULL,NULL),(6380,'801205',789,'คลองกระบือ','80140',NULL,NULL),(6381,'801206',789,'เกาะทวด','80330',NULL,NULL),(6382,'801207',789,'บ้านใหม่','80140',NULL,NULL),(6383,'801208',789,'หูล่อง','80140',NULL,NULL),(6384,'801209',789,'แหลมตะลุมพุก','80140',NULL,NULL),(6385,'801210',789,'ปากพนังฝั่งตะวันตก','80140',NULL,NULL),(6386,'801211',789,'บางศาลา','80140',NULL,NULL),(6387,'801212',789,'บางพระ','80140',NULL,NULL),(6388,'801213',789,'บางตะพง','80140',NULL,NULL),(6389,'801214',789,'ปากพนังฝั่งตะวันออก','80140',NULL,NULL),(6390,'801215',789,'บ้านเพิง','80140',NULL,NULL),(6391,'801216',789,'ท่าพญา','80140',NULL,NULL),(6392,'801217',789,'ปากแพรก','80140',NULL,NULL),(6393,'801218',789,'ขนาบนาก','80140',NULL,NULL),(6394,'801301',790,'ร่อนพิบูลย์','80130',NULL,NULL),(6395,'801302',790,'หินตก','80350',NULL,NULL),(6396,'801303',790,'เสาธง','80350',NULL,NULL),(6397,'801304',790,'ควนเกย','80130',NULL,NULL),(6398,'801305',790,'ควนพัง','80130',NULL,NULL),(6399,'801306',790,'ควนชุม','80130',NULL,NULL),(6400,'801401',791,'สิชล','80120',NULL,NULL),(6401,'801402',791,'ทุ่งปรัง','80120',NULL,NULL),(6402,'801403',791,'ฉลอง','80120',NULL,NULL),(6403,'801404',791,'เสาเภา','80340',NULL,NULL),(6404,'801405',791,'เปลี่ยน','80120',NULL,NULL),(6405,'801406',791,'สีขีด','80120',NULL,NULL),(6406,'801407',791,'เทพราช','80340',NULL,NULL),(6407,'801408',791,'เขาน้อย','80120',NULL,NULL),(6408,'801409',791,'ทุ่งใส','80120',NULL,NULL),(6409,'801501',792,'ขนอม','80210',NULL,NULL),(6410,'801502',792,'ควนทอง','80210',NULL,NULL),(6411,'801503',792,'ท้องเนียน','80210',NULL,NULL),(6412,'801601',793,'หัวไทร','80170',NULL,NULL),(6413,'801602',793,'หน้าสตน','80170',NULL,NULL),(6414,'801603',793,'ทรายขาว','80170',NULL,NULL),(6415,'801604',793,'แหลม','80170',NULL,NULL),(6416,'801605',793,'เขาพังไกร','80170',NULL,NULL),(6417,'801606',793,'บ้านราม','80170',NULL,NULL),(6418,'801607',793,'บางนบ','80170',NULL,NULL),(6419,'801608',793,'ท่าซอม','80170',NULL,NULL),(6420,'801609',793,'ควนชะลิก','80170',NULL,NULL),(6421,'801610',793,'รามแก้ว','80170',NULL,NULL),(6422,'801611',793,'เกาะเพชร','80170',NULL,NULL),(6423,'801701',794,'บางขัน','80360',NULL,NULL),(6424,'801702',794,'บ้านลำนาว','80360',NULL,NULL),(6425,'801703',794,'วังหิน','80360',NULL,NULL),(6426,'801704',794,'บ้านนิคม','80360',NULL,NULL),(6427,'801801',795,'ถ้ำพรรณรา','80260',NULL,NULL),(6428,'801802',795,'คลองเส','80260',NULL,NULL),(6429,'801803',795,'ดุสิต','80260',NULL,NULL),(6430,'801901',796,'บ้านควนมุด','80180',NULL,NULL),(6431,'801902',796,'บ้านชะอวด','80180',NULL,NULL),(6432,'801903',796,'ควนหนองคว้า','80130',NULL,NULL),(6433,'801904',796,'ทุ่งโพธิ์','80130',NULL,NULL),(6434,'801905',796,'นาหมอบุญ','80130',NULL,NULL),(6435,'801906',796,'สาม','80130',NULL,NULL),(6436,'802001',797,'นาพรุ','80000',NULL,NULL),(6437,'802002',797,'นาสาร','80000',NULL,NULL),(6438,'802003',797,'ท้ายสำเภา','80000',NULL,NULL),(6439,'802004',797,'ช้างซ้าย','80000',NULL,NULL),(6440,'802101',798,'นบพิตำ','80160',NULL,NULL),(6441,'802102',798,'กรุงชิง','80160',NULL,NULL),(6442,'802103',798,'กะหรอ','80160',NULL,NULL),(6443,'802104',798,'นาเหรง','80160',NULL,NULL),(6444,'802201',799,'ช้างกลาง','80250',NULL,NULL),(6445,'802202',799,'หลักช้าง','80250',NULL,NULL),(6446,'802203',799,'สวนขัน','80250',NULL,NULL),(6447,'802301',800,'เชียรเขา','80190',NULL,NULL),(6448,'802302',800,'ดอนตรอ','80290',NULL,NULL),(6449,'802303',800,'สวนหลวง','80190',NULL,NULL),(6450,'802304',800,'ทางพูน','80190',NULL,NULL),(6451,'810101',801,'ปากน้ำ','81000',NULL,NULL),(6452,'810103',801,'กระบี่น้อย','81000',NULL,NULL),(6453,'810105',801,'เขาคราม','81000',NULL,NULL),(6454,'810106',801,'เขาทอง','81000',NULL,NULL),(6455,'810111',801,'ทับปริก','81000',NULL,NULL),(6456,'810115',801,'ไสไทย','81000',NULL,NULL),(6457,'810116',801,'อ่าวนาง','81000',NULL,NULL),(6458,'810117',801,'หนองทะเล','81000',NULL,NULL),(6459,'810118',801,'คลองประสงค์','81000',NULL,NULL),(6460,'810201',802,'เขาพนม','81140',NULL,NULL),(6461,'810202',802,'เขาดิน','81140',NULL,NULL),(6462,'810203',802,'สินปุน','80240',NULL,NULL),(6463,'810204',802,'พรุเตียว','81140',NULL,NULL),(6464,'810205',802,'หน้าเขา','81140',NULL,NULL),(6465,'810206',802,'โคกหาร','80240',NULL,NULL),(6466,'810301',803,'เกาะลันตาใหญ่','81150',NULL,NULL),(6467,'810302',803,'เกาะลันตาน้อย','81150',NULL,NULL),(6468,'810303',803,'เกาะกลาง','81120',NULL,NULL),(6469,'810304',803,'คลองยาง','81120',NULL,NULL),(6470,'810305',803,'ศาลาด่าน','81150',NULL,NULL),(6471,'810401',804,'คลองท่อมใต้','81120',NULL,NULL),(6472,'810402',804,'คลองท่อมเหนือ','81120',NULL,NULL),(6473,'810403',804,'คลองพน','81170',NULL,NULL),(6474,'810404',804,'ทรายขาว','81170',NULL,NULL),(6475,'810405',804,'ห้วยน้ำขาว','81120',NULL,NULL),(6476,'810406',804,'พรุดินนา','81120',NULL,NULL),(6477,'810407',804,'เพหลา','81120',NULL,NULL),(6478,'810501',805,'อ่าวลึกใต้','81110',NULL,NULL),(6479,'810502',805,'แหลมสัก','81110',NULL,NULL),(6480,'810503',805,'นาเหนือ','81110',NULL,NULL),(6481,'810504',805,'คลองหิน','81110',NULL,NULL),(6482,'810505',805,'อ่าวลึกน้อย','81110',NULL,NULL),(6483,'810506',805,'อ่าวลึกเหนือ','81110',NULL,NULL),(6484,'810507',805,'เขาใหญ่','81110',NULL,NULL),(6485,'810508',805,'คลองยา','81110',NULL,NULL),(6486,'810509',805,'บ้านกลาง','81110',NULL,NULL),(6487,'810601',806,'ปลายพระยา','81160',NULL,NULL),(6488,'810602',806,'เขาเขน','81160',NULL,NULL),(6489,'810603',806,'เขาต่อ','81160',NULL,NULL),(6490,'810604',806,'คีรีวง','81160',NULL,NULL),(6491,'810701',807,'ลำทับ','81120',NULL,NULL),(6492,'810702',807,'ดินอุดม','81120',NULL,NULL),(6493,'810703',807,'ทุ่งไทรทอง','81120',NULL,NULL),(6494,'810704',807,'ดินแดง','81120',NULL,NULL),(6495,'810801',808,'เหนือคลอง','81130',NULL,NULL),(6496,'810802',808,'เกาะศรีบอยา','81130',NULL,NULL),(6497,'810803',808,'คลองขนาน','81130',NULL,NULL),(6498,'810804',808,'คลองเขม้า','81130',NULL,NULL),(6499,'810805',808,'โคกยาง','81130',NULL,NULL),(6500,'810806',808,'ตลิ่งชัน','81130',NULL,NULL),(6501,'810807',808,'ปกาสัย','81130',NULL,NULL),(6502,'810808',808,'ห้วยยูง','81130',NULL,NULL),(6503,'820101',809,'ท้ายช้าง','82000',NULL,NULL),(6504,'820102',809,'นบปริง','82000',NULL,NULL),(6505,'820103',809,'ถ้ำน้ำผุด','82000',NULL,NULL),(6506,'820104',809,'บางเตย','82000',NULL,NULL),(6507,'820105',809,'ตากแดด','82000',NULL,NULL),(6508,'820106',809,'สองแพรก','82000',NULL,NULL),(6509,'820107',809,'ทุ่งคาโงก','82000',NULL,NULL),(6510,'820108',809,'เกาะปันหยี','82000',NULL,NULL),(6511,'820109',809,'ป่ากอ','82000',NULL,NULL),(6512,'820201',810,'เกาะยาวน้อย','82160',NULL,NULL),(6513,'820202',810,'เกาะยาวใหญ่','82160',NULL,NULL),(6514,'820203',810,'พรุใน','83000',NULL,NULL),(6515,'820301',811,'กะปง','82170',NULL,NULL),(6516,'820302',811,'ท่านา','82170',NULL,NULL),(6517,'820303',811,'เหมาะ','82170',NULL,NULL),(6518,'820304',811,'เหล','82170',NULL,NULL),(6519,'820305',811,'รมณีย์','82170',NULL,NULL),(6520,'820401',812,'ถ้ำ','82130',NULL,NULL),(6521,'820402',812,'กระโสม','82130',NULL,NULL),(6522,'820403',812,'กะไหล','82130',NULL,NULL),(6523,'820404',812,'ท่าอยู่','82130',NULL,NULL),(6524,'820405',812,'หล่อยูง','82140',NULL,NULL),(6525,'820406',812,'โคกกลอย','82140',NULL,NULL),(6526,'820407',812,'คลองเคียน','82130',NULL,NULL),(6527,'820501',813,'ตะกั่วป่า','82110',NULL,NULL),(6528,'820502',813,'บางนายสี','82110',NULL,NULL),(6529,'820503',813,'บางไทร','82110',NULL,NULL),(6530,'820504',813,'บางม่วง','82110',NULL,NULL),(6531,'820505',813,'ตำตัว','82110',NULL,NULL),(6532,'820506',813,'โคกเคียน','82110',NULL,NULL),(6533,'820507',813,'คึกคัก','82190',NULL,NULL),(6534,'820508',813,'เกาะคอเขา','82190',NULL,NULL),(6535,'820601',814,'คุระ','82150',NULL,NULL),(6536,'820602',814,'บางวัน','82150',NULL,NULL),(6537,'820603',814,'เกาะพระทอง','82150',NULL,NULL),(6538,'820605',814,'แม่นางขาว','82150',NULL,NULL),(6539,'820701',815,'ทับปุด','82180',NULL,NULL),(6540,'820702',815,'มะรุ่ย','82180',NULL,NULL),(6541,'820703',815,'บ่อแสน','82180',NULL,NULL),(6542,'820704',815,'ถ้ำทองหลาง','82180',NULL,NULL),(6543,'820705',815,'โคกเจริญ','82180',NULL,NULL),(6544,'820706',815,'บางเหรียง','82180',NULL,NULL),(6545,'820801',816,'ท้ายเหมือง','82120',NULL,NULL),(6546,'820802',816,'นาเตย','82120',NULL,NULL),(6547,'820803',816,'บางทอง','82120',NULL,NULL),(6548,'820804',816,'ทุ่งมะพร้าว','82120',NULL,NULL),(6549,'820805',816,'ลำภี','82120',NULL,NULL),(6550,'820806',816,'ลำแก่น','82120',NULL,NULL),(6551,'830101',817,'ตลาดใหญ่','83000',NULL,NULL),(6552,'830102',817,'ตลาดเหนือ','83000',NULL,NULL),(6553,'830103',817,'เกาะแก้ว','83000',NULL,NULL),(6554,'830105',817,'รัษฎา','83000',NULL,NULL),(6555,'830106',817,'ฉลอง','83130',NULL,NULL),(6556,'830107',817,'ราไวย์','83130',NULL,NULL),(6557,'830108',817,'กะรน','83100',NULL,NULL),(6558,'830201',818,'กะทู้','83120',NULL,NULL),(6559,'830202',818,'ป่าตอง','83150',NULL,NULL),(6560,'830203',818,'กมลา','83150',NULL,NULL),(6561,'830301',819,'เทพกระษัตรี','83110',NULL,NULL),(6562,'830302',819,'ศรีสุนทร','83110',NULL,NULL),(6563,'830303',819,'เชิงทะเล','83110',NULL,NULL),(6564,'830304',819,'ป่าคลอก','83110',NULL,NULL),(6565,'830305',819,'ไม้ขาว','83110',NULL,NULL),(6566,'830306',819,'สาคู','83110',NULL,NULL),(6567,'840101',820,'ตลาด','84000',NULL,NULL),(6568,'840102',820,'มะขามเตี้ย','84000',NULL,NULL),(6569,'840103',820,'วัดประดู่','84000',NULL,NULL),(6570,'840104',820,'ขุนทะเล','84100',NULL,NULL),(6571,'840105',820,'บางใบไม้','84000',NULL,NULL),(6572,'840106',820,'บางชนะ','84000',NULL,NULL),(6573,'840107',820,'คลองน้อย','84000',NULL,NULL),(6574,'840108',820,'บางไทร','84000',NULL,NULL),(6575,'840109',820,'บางโพธิ์','84000',NULL,NULL),(6576,'840110',820,'บางกุ้ง','84000',NULL,NULL),(6577,'840111',820,'คลองฉนาก','84000',NULL,NULL),(6578,'840201',821,'ท่าทองใหม่','84290',NULL,NULL),(6579,'840202',821,'ท่าทอง','84160',NULL,NULL),(6580,'840203',821,'กะแดะ','84160',NULL,NULL),(6581,'840204',821,'ทุ่งกง','84290',NULL,NULL),(6582,'840205',821,'กรูด','84160',NULL,NULL),(6583,'840206',821,'ช้างซ้าย','84160',NULL,NULL),(6584,'840207',821,'พลายวาส','84160',NULL,NULL),(6585,'840208',821,'ป่าร่อน','84160',NULL,NULL),(6586,'840209',821,'ตะเคียนทอง','84160',NULL,NULL),(6587,'840210',821,'ช้างขวา','84160',NULL,NULL),(6588,'840211',821,'ท่าอุแท','84160',NULL,NULL),(6589,'840212',821,'ทุ่งรัง','84290',NULL,NULL),(6590,'840213',821,'คลองสระ','84160',NULL,NULL),(6591,'840301',822,'ดอนสัก','84220',NULL,NULL),(6592,'840302',822,'ชลคราม','84160',NULL,NULL),(6593,'840303',822,'ไชยคราม','84220',NULL,NULL),(6594,'840304',822,'ปากแพรก','84340',NULL,NULL),(6595,'840401',823,'อ่างทอง','84140',NULL,NULL),(6596,'840402',823,'ลิปะน้อย','84140',NULL,NULL),(6597,'840403',823,'ตลิ่งงาม','84140',NULL,NULL),(6598,'840404',823,'หน้าเมือง','84140',NULL,NULL),(6599,'840405',823,'มะเร็ต','84310',NULL,NULL),(6600,'840406',823,'บ่อผุด','84320',NULL,NULL),(6601,'840407',823,'แม่น้ำ','84330',NULL,NULL),(6602,'840501',824,'เกาะพะงัน','84280',NULL,NULL),(6603,'840502',824,'บ้านใต้','84280',NULL,NULL),(6604,'840601',825,'ตลาดไชยา','84110',NULL,NULL),(6605,'840602',825,'พุมเรียง','84110',NULL,NULL),(6606,'840603',825,'เลม็ด','84110',NULL,NULL),(6607,'840604',825,'เวียง','84110',NULL,NULL),(6608,'840605',825,'ทุ่ง','84110',NULL,NULL),(6609,'840606',825,'ป่าเว','84110',NULL,NULL),(6610,'840607',825,'ตะกรบ','84110',NULL,NULL),(6611,'840608',825,'โมถ่าย','84110',NULL,NULL),(6612,'840609',825,'ปากหมาก','84110',NULL,NULL),(6613,'840701',826,'ท่าชนะ','84170',NULL,NULL),(6614,'840702',826,'สมอทอง','84170',NULL,NULL),(6615,'840703',826,'ประสงค์','84170',NULL,NULL),(6616,'840704',826,'คันธุลี','84170',NULL,NULL),(6617,'840705',826,'วัง','84170',NULL,NULL),(6618,'840706',826,'คลองพา','84170',NULL,NULL),(6619,'840801',827,'ท่าขนอน','84180',NULL,NULL),(6620,'840802',827,'บ้านยาง','84180',NULL,NULL),(6621,'840803',827,'น้ำหัก','84180',NULL,NULL),(6622,'840806',827,'กะเปา','84180',NULL,NULL),(6623,'840807',827,'ท่ากระดาน','84180',NULL,NULL),(6624,'840808',827,'ย่านยาว','84180',NULL,NULL),(6625,'840809',827,'ถ้ำสิงขร','84180',NULL,NULL),(6626,'840810',827,'บ้านทำเนียบ','84180',NULL,NULL),(6627,'840901',828,'เขาวง','84230',NULL,NULL),(6628,'840902',828,'พะแสง','84230',NULL,NULL),(6629,'840903',828,'พรุไทย','84230',NULL,NULL),(6630,'840904',828,'เขาพัง','84230',NULL,NULL),(6631,'841001',829,'พนม','84250',NULL,NULL),(6632,'841002',829,'ต้นยวน','84250',NULL,NULL),(6633,'841003',829,'คลองศก','84250',NULL,NULL),(6634,'841004',829,'พลูเถื่อน','84250',NULL,NULL),(6635,'841005',829,'พังกาญจน์','84250',NULL,NULL),(6636,'841006',829,'คลองชะอุ่น','84250',NULL,NULL),(6637,'841101',830,'ท่าฉาง','84150',NULL,NULL),(6638,'841102',830,'ท่าเคย','84150',NULL,NULL),(6639,'841103',830,'คลองไทร','84150',NULL,NULL),(6640,'841104',830,'เขาถ่าน','84150',NULL,NULL),(6641,'841105',830,'เสวียด','84150',NULL,NULL),(6642,'841106',830,'ปากฉลุย','84150',NULL,NULL),(6643,'841201',831,'นาสาร','84120',NULL,NULL),(6644,'841202',831,'พรุพี','84270',NULL,NULL),(6645,'841203',831,'ทุ่งเตา','84120',NULL,NULL),(6646,'841204',831,'ลำพูน','84120',NULL,NULL),(6647,'841205',831,'ท่าชี','84120',NULL,NULL),(6648,'841206',831,'ควนศรี','84270',NULL,NULL),(6649,'841207',831,'ควนสุบรรณ','84120',NULL,NULL),(6650,'841208',831,'คลองปราบ','84120',NULL,NULL),(6651,'841209',831,'น้ำพุ','84120',NULL,NULL),(6652,'841210',831,'ทุ่งเตาใหม่','84120',NULL,NULL),(6653,'841211',831,'เพิ่มพูนทรัพย์','84120',NULL,NULL),(6654,'841301',832,'บ้านนา','84240',NULL,NULL),(6655,'841302',832,'ท่าเรือ','84240',NULL,NULL),(6656,'841303',832,'ทรัพย์ทวี','84240',NULL,NULL),(6657,'841304',832,'นาใต้','84240',NULL,NULL),(6658,'841401',833,'เคียนซา','84260',NULL,NULL),(6659,'841402',833,'พ่วงพรมคร','84210',NULL,NULL),(6660,'841403',833,'เขาตอก','84260',NULL,NULL),(6661,'841404',833,'อรัญคามวารี','84260',NULL,NULL),(6662,'841405',833,'บ้านเสด็จ','84260',NULL,NULL),(6663,'841501',834,'เวียงสระ','84190',NULL,NULL),(6664,'841502',834,'บ้านส้อง','84190',NULL,NULL),(6665,'841503',834,'คลองฉนวน','84190',NULL,NULL),(6666,'841504',834,'ทุ่งหลวง','84190',NULL,NULL),(6667,'841505',834,'เขานิพันธ์','84190',NULL,NULL),(6668,'841601',835,'อิปัน','84210',NULL,NULL),(6669,'841602',835,'สินปุน','84210',NULL,NULL),(6670,'841603',835,'บางสวรรค์','84210',NULL,NULL),(6671,'841604',835,'ไทรขึง','84210',NULL,NULL),(6672,'841605',835,'สินเจริญ','84210',NULL,NULL),(6673,'841606',835,'ไทรโสภา','84210',NULL,NULL),(6674,'841607',835,'สาคู','84210',NULL,NULL),(6675,'841701',836,'ท่าข้าม','84130',NULL,NULL),(6676,'841702',836,'ท่าสะท้อน','84130',NULL,NULL),(6677,'841703',836,'ลีเล็ด','84130',NULL,NULL),(6678,'841704',836,'บางมะเดื่อ','84130',NULL,NULL),(6679,'841705',836,'บางเดือน','84130',NULL,NULL),(6680,'841706',836,'ท่าโรงช้าง','84130',NULL,NULL),(6681,'841707',836,'กรูด','84130',NULL,NULL),(6682,'841708',836,'พุนพิน','84130',NULL,NULL),(6683,'841709',836,'บางงอน','84130',NULL,NULL),(6684,'841710',836,'ศรีวิชัย','84130',NULL,NULL),(6685,'841711',836,'น้ำรอบ','84130',NULL,NULL),(6686,'841712',836,'มะลวน','84130',NULL,NULL),(6687,'841713',836,'หัวเตย','84130',NULL,NULL),(6688,'841714',836,'หนองไทร','84130',NULL,NULL),(6689,'841715',836,'เขาหัวควาย','84130',NULL,NULL),(6690,'841716',836,'ตะปาน','84130',NULL,NULL),(6691,'841801',837,'สองแพรก','84350',NULL,NULL),(6692,'841802',837,'ชัยบุรี','84350',NULL,NULL),(6693,'841803',837,'คลองน้อย','84350',NULL,NULL),(6694,'841804',837,'ไทรทอง','84350',NULL,NULL),(6695,'841901',838,'ตะกุกใต้','84180',NULL,NULL),(6696,'841902',838,'ตะกุกเหนือ','84180',NULL,NULL),(6697,'850101',839,'เขานิเวศน์','85000',NULL,NULL),(6698,'850102',839,'ราชกรูด','85000',NULL,NULL),(6699,'850103',839,'หงาว','85000',NULL,NULL),(6700,'850104',839,'บางริ้น','85000',NULL,NULL),(6701,'850105',839,'ปากน้ำ','85000',NULL,NULL),(6702,'850106',839,'บางนอน','85000',NULL,NULL),(6703,'850107',839,'หาดส้มแป้น','85000',NULL,NULL),(6704,'850108',839,'ทรายแดง','85130',NULL,NULL),(6705,'850109',839,'เกาะพยาม','85000',NULL,NULL),(6706,'850201',840,'ละอุ่นใต้','85130',NULL,NULL),(6707,'850202',840,'ละอุ่นเหนือ','85130',NULL,NULL),(6708,'850203',840,'บางพระใต้','85130',NULL,NULL),(6709,'850204',840,'บางพระเหนือ','85130',NULL,NULL),(6710,'850205',840,'บางแก้ว','85130',NULL,NULL),(6711,'850206',840,'ในวงเหนือ','85130',NULL,NULL),(6712,'850207',840,'ในวงใต้','85130',NULL,NULL),(6713,'850301',841,'ม่วงกลวง','85120',NULL,NULL),(6714,'850302',841,'กะเปอร์','85120',NULL,NULL),(6715,'850303',841,'เชี่ยวเหลียง','85120',NULL,NULL),(6716,'850304',841,'บ้านนา','85120',NULL,NULL),(6717,'850305',841,'บางหิน','85120',NULL,NULL),(6718,'850401',842,'น้ำจืด','85110',NULL,NULL),(6719,'850402',842,'น้ำจืดน้อย','85110',NULL,NULL),(6720,'850403',842,'มะมุ','85110',NULL,NULL),(6721,'850404',842,'ปากจั่น','85110',NULL,NULL),(6722,'850405',842,'ลำเลียง','85110',NULL,NULL),(6723,'850406',842,'จ.ป.ร.','85110',NULL,NULL),(6724,'850407',842,'บางใหญ่','85110',NULL,NULL),(6725,'850501',843,'นาคา','85120',NULL,NULL),(6726,'850502',843,'กำพวน','85120',NULL,NULL),(6727,'860101',844,'ท่าตะเภา','86000',NULL,NULL),(6728,'860102',844,'ปากน้ำ','86120',NULL,NULL),(6729,'860103',844,'ท่ายาง','86000',NULL,NULL),(6730,'860104',844,'บางหมาก','86000',NULL,NULL),(6731,'860105',844,'นาทุ่ง','86000',NULL,NULL),(6732,'860106',844,'นาชะอัง','86000',NULL,NULL),(6733,'860107',844,'ตากแดด','86000',NULL,NULL),(6734,'860108',844,'บางลึก','86000',NULL,NULL),(6735,'860109',844,'หาดพันไกร','86000',NULL,NULL),(6736,'860110',844,'วังไผ่','86000',NULL,NULL),(6737,'860111',844,'วังใหม่','86190',NULL,NULL),(6738,'860112',844,'บ้านนา','86190',NULL,NULL),(6739,'860113',844,'ขุนกระทิง','86000',NULL,NULL),(6740,'860114',844,'ทุ่งคา','86100',NULL,NULL),(6741,'860115',844,'วิสัยเหนือ','86100',NULL,NULL),(6742,'860116',844,'หาดทรายรี','86120',NULL,NULL),(6743,'860117',844,'ถ้ำสิงห์','86100',NULL,NULL),(6744,'860201',845,'ท่าแซะ','86140',NULL,NULL),(6745,'860202',845,'คุริง','86140',NULL,NULL),(6746,'860203',845,'สลุย','86140',NULL,NULL),(6747,'860204',845,'นากระตาม','86140',NULL,NULL),(6748,'860205',845,'รับร่อ','86190',NULL,NULL),(6749,'860206',845,'ท่าข้าม','86140',NULL,NULL),(6750,'860207',845,'หงษ์เจริญ','86140',NULL,NULL),(6751,'860208',845,'หินแก้ว','86190',NULL,NULL),(6752,'860209',845,'ทรัพย์อนันต์','86140',NULL,NULL),(6753,'860210',845,'สองพี่น้อง','86140',NULL,NULL),(6754,'860301',846,'บางสน','86160',NULL,NULL),(6755,'860302',846,'ทะเลทรัพย์','86160',NULL,NULL),(6756,'860303',846,'สะพลี','86230',NULL,NULL),(6757,'860304',846,'ชุมโค','86160',NULL,NULL),(6758,'860305',846,'ดอนยาง','86210',NULL,NULL),(6759,'860306',846,'ปากคลอง','86210',NULL,NULL),(6760,'860307',846,'เขาไชยราช','86210',NULL,NULL),(6761,'860401',847,'หลังสวน','86110',NULL,NULL),(6762,'860402',847,'ขันเงิน','86110',NULL,NULL),(6763,'860403',847,'ท่ามะพลา','86110',NULL,NULL),(6764,'860404',847,'นาขา','86110',NULL,NULL),(6765,'860405',847,'นาพญา','86110',NULL,NULL),(6766,'860406',847,'บ้านควน','86110',NULL,NULL),(6767,'860407',847,'บางมะพร้าว','86110',NULL,NULL),(6768,'860408',847,'บางน้ำจืด','86150',NULL,NULL),(6769,'860409',847,'ปากน้ำ','86150',NULL,NULL),(6770,'860410',847,'พ้อแดง','86110',NULL,NULL),(6771,'860411',847,'แหลมทราย','86110',NULL,NULL),(6772,'860412',847,'วังตะกอ','86110',NULL,NULL),(6773,'860413',847,'หาดยาย','86110',NULL,NULL),(6774,'860501',848,'ละแม','86170',NULL,NULL),(6775,'860502',848,'ทุ่งหลวง','86170',NULL,NULL),(6776,'860503',848,'สวนแตง','86170',NULL,NULL),(6777,'860504',848,'ทุ่งคาวัด','86170',NULL,NULL),(6778,'860601',849,'พะโต๊ะ','86180',NULL,NULL),(6779,'860602',849,'ปากทรง','86180',NULL,NULL),(6780,'860603',849,'ปังหวาน','86180',NULL,NULL),(6781,'860604',849,'พระรักษ์','86180',NULL,NULL),(6782,'860701',850,'นาโพธิ์','86130',NULL,NULL),(6783,'860702',850,'สวี','86130',NULL,NULL),(6784,'860703',850,'ทุ่งระยะ','86130',NULL,NULL),(6785,'860704',850,'ท่าหิน','86130',NULL,NULL),(6786,'860705',850,'ปากแพรก','86130',NULL,NULL),(6787,'860706',850,'ด่านสวี','86130',NULL,NULL),(6788,'860707',850,'ครน','86130',NULL,NULL),(6789,'860708',850,'วิสัยใต้','86130',NULL,NULL),(6790,'860709',850,'นาสัก','86130',NULL,NULL),(6791,'860710',850,'เขาทะลุ','86130',NULL,NULL),(6792,'860711',850,'เขาค่าย','86130',NULL,NULL),(6793,'860801',851,'ปากตะโก','86220',NULL,NULL),(6794,'860802',851,'ทุ่งตะไคร','86220',NULL,NULL),(6795,'860803',851,'ตะโก','86220',NULL,NULL),(6796,'860804',851,'ช่องไม้แก้ว','86220',NULL,NULL),(6797,'900101',852,'บ่อยาง','90000',NULL,NULL),(6798,'900102',852,'เขารูปช้าง','90000',NULL,NULL),(6799,'900103',852,'เกาะแต้ว','90000',NULL,NULL),(6800,'900104',852,'พะวง','90100',NULL,NULL),(6801,'900105',852,'ทุ่งหวัง','90000',NULL,NULL),(6802,'900106',852,'เกาะยอ','90100',NULL,NULL),(6803,'900201',853,'จะทิ้งพระ','90190',NULL,NULL),(6804,'900202',853,'กระดังงา','90190',NULL,NULL),(6805,'900203',853,'สนามชัย','90190',NULL,NULL),(6806,'900204',853,'ดีหลวง','90190',NULL,NULL),(6807,'900205',853,'ชุมพล','90190',NULL,NULL),(6808,'900206',853,'คลองรี','90190',NULL,NULL),(6809,'900207',853,'คูขุด','90190',NULL,NULL),(6810,'900208',853,'ท่าหิน','90190',NULL,NULL),(6811,'900209',853,'วัดจันทร์','90190',NULL,NULL),(6812,'900210',853,'บ่อแดง','90190',NULL,NULL),(6813,'900211',853,'บ่อดาน','90190',NULL,NULL),(6814,'900301',854,'บ้านนา','90130',NULL,NULL),(6815,'900302',854,'ป่าชิง','90130',NULL,NULL),(6816,'900303',854,'สะพานไม้แก่น','90130',NULL,NULL),(6817,'900304',854,'สะกอม','90130',NULL,NULL),(6818,'900305',854,'นาหว้า','90130',NULL,NULL),(6819,'900306',854,'นาทับ','90130',NULL,NULL),(6820,'900307',854,'น้ำขาว','90130',NULL,NULL),(6821,'900308',854,'ขุนตัดหวาย','90130',NULL,NULL),(6822,'900309',854,'ท่าหมอไทร','90130',NULL,NULL),(6823,'900310',854,'จะโหนง','90130',NULL,NULL),(6824,'900311',854,'คู','90130',NULL,NULL),(6825,'900312',854,'แค','90130',NULL,NULL),(6826,'900313',854,'คลองเปียะ','90130',NULL,NULL),(6827,'900314',854,'ตลิ่งชัน','90130',NULL,NULL),(6828,'900401',855,'นาทวี','90160',NULL,NULL),(6829,'900402',855,'ฉาง','90160',NULL,NULL),(6830,'900403',855,'นาหมอศรี','90160',NULL,NULL),(6831,'900404',855,'คลองทราย','90160',NULL,NULL),(6832,'900405',855,'ปลักหนู','90160',NULL,NULL),(6833,'900406',855,'ท่าประดู่','90160',NULL,NULL),(6834,'900407',855,'สะท้อน','90160',NULL,NULL),(6835,'900408',855,'ทับช้าง','90160',NULL,NULL),(6836,'900409',855,'ประกอบ','90160',NULL,NULL),(6837,'900410',855,'คลองกวาง','90160',NULL,NULL),(6838,'900501',856,'เทพา','90150',NULL,NULL),(6839,'900502',856,'ปากบาง','90150',NULL,NULL),(6840,'900503',856,'เกาะสะบ้า','90150',NULL,NULL),(6841,'900504',856,'ลำไพล','90260',NULL,NULL),(6842,'900505',856,'ท่าม่วง','90260',NULL,NULL),(6843,'900506',856,'วังใหญ่','90260',NULL,NULL),(6844,'900507',856,'สะกอม','90150',NULL,NULL),(6845,'900601',857,'สะบ้าย้อย','90210',NULL,NULL),(6846,'900602',857,'ทุ่งพอ','90210',NULL,NULL),(6847,'900603',857,'เปียน','90210',NULL,NULL),(6848,'900604',857,'บ้านโหนด','90210',NULL,NULL),(6849,'900605',857,'จะแหน','90210',NULL,NULL),(6850,'900606',857,'คูหา','90210',NULL,NULL),(6851,'900607',857,'เขาแดง','90210',NULL,NULL),(6852,'900608',857,'บาโหย','90210',NULL,NULL),(6853,'900609',857,'ธารคีรี','90210',NULL,NULL),(6854,'900701',858,'ระโนด','90140',NULL,NULL),(6855,'900702',858,'คลองแดน','90140',NULL,NULL),(6856,'900703',858,'ตาเครียะ','90140',NULL,NULL),(6857,'900704',858,'ท่าบอน','90140',NULL,NULL),(6858,'900705',858,'บ้านใหม่','90140',NULL,NULL),(6859,'900706',858,'บ่อตรุ','90140',NULL,NULL),(6860,'900707',858,'ปากแตระ','90140',NULL,NULL),(6861,'900708',858,'พังยาง','90140',NULL,NULL),(6862,'900709',858,'ระวะ','90140',NULL,NULL),(6863,'900710',858,'วัดสน','90140',NULL,NULL),(6864,'900711',858,'บ้านขาว','90140',NULL,NULL),(6865,'900712',858,'แดนสงวน','90140',NULL,NULL),(6866,'900801',859,'เกาะใหญ่','90270',NULL,NULL),(6867,'900802',859,'โรง','90270',NULL,NULL),(6868,'900803',859,'เชิงแส','90270',NULL,NULL),(6869,'900804',859,'กระแสสินธุ์','90270',NULL,NULL),(6870,'900901',860,'กำแพงเพชร','90180',NULL,NULL),(6871,'900902',860,'ท่าชะมวง','90180',NULL,NULL),(6872,'900903',860,'คูหาใต้','90180',NULL,NULL),(6873,'900904',860,'ควนรู','90180',NULL,NULL),(6874,'900909',860,'เขาพระ','90180',NULL,NULL),(6875,'901001',861,'สะเดา','90120',NULL,NULL),(6876,'901002',861,'ปริก','90120',NULL,NULL),(6877,'901003',861,'พังลา','90170',NULL,NULL),(6878,'901004',861,'สำนักแต้ว','90120',NULL,NULL),(6879,'901005',861,'ทุ่งหมอ','90240',NULL,NULL),(6880,'901006',861,'ท่าโพธิ์','90170',NULL,NULL),(6881,'901007',861,'ปาดังเบซาร์','90240',NULL,NULL),(6882,'901008',861,'สำนักขาม','90320',NULL,NULL),(6883,'901009',861,'เขามีเกียรติ','90170',NULL,NULL),(6884,'901101',862,'หาดใหญ่','90110',NULL,NULL),(6885,'901102',862,'ควนลัง','90110',NULL,NULL),(6886,'901103',862,'คูเต่า','90110',NULL,NULL),(6887,'901104',862,'คอหงส์','90110',NULL,NULL),(6888,'901105',862,'คลองแห','90110',NULL,NULL),(6889,'901107',862,'คลองอู่ตะเภา','90110',NULL,NULL),(6890,'901108',862,'ฉลุง','90110',NULL,NULL),(6891,'901111',862,'ทุ่งใหญ่','90110',NULL,NULL),(6892,'901112',862,'ทุ่งตำเสา','90110',NULL,NULL),(6893,'901113',862,'ท่าข้าม','90110',NULL,NULL),(6894,'901114',862,'น้ำน้อย','90110',NULL,NULL),(6895,'901116',862,'บ้านพรุ','90250',NULL,NULL),(6896,'901118',862,'พะตง','90230',NULL,NULL),(6897,'901201',863,'นาหม่อม','90310',NULL,NULL),(6898,'901202',863,'พิจิตร','90310',NULL,NULL),(6899,'901203',863,'ทุ่งขมิ้น','90310',NULL,NULL),(6900,'901204',863,'คลองหรัง','90310',NULL,NULL),(6901,'901301',864,'รัตภูมิ','90220',NULL,NULL),(6902,'901302',864,'ควนโส','90220',NULL,NULL),(6903,'901303',864,'ห้วยลึก','90220',NULL,NULL),(6904,'901304',864,'บางเหรียง','90220',NULL,NULL),(6905,'901401',865,'บางกล่ำ','90110',NULL,NULL),(6906,'901402',865,'ท่าช้าง','90110',NULL,NULL),(6907,'901403',865,'แม่ทอม','90110',NULL,NULL),(6908,'901404',865,'บ้านหาร','90110',NULL,NULL),(6909,'901501',866,'ชิงโค','90280',NULL,NULL),(6910,'901502',866,'สทิงหม้อ','90280',NULL,NULL),(6911,'901503',866,'ทำนบ','90280',NULL,NULL),(6912,'901504',866,'รำแดง','90330',NULL,NULL),(6913,'901505',866,'วัดขนุน','90330',NULL,NULL),(6914,'901506',866,'ชะแล้','90330',NULL,NULL),(6915,'901507',866,'ปากรอ','90330',NULL,NULL),(6916,'901508',866,'ป่าขาด','90330',NULL,NULL),(6917,'901509',866,'หัวเขา','90280',NULL,NULL),(6918,'901510',866,'บางเขียด','90330',NULL,NULL),(6919,'901511',866,'ม่วงงาม','90330',NULL,NULL),(6920,'901601',867,'คลองหอยโข่ง','90230',NULL,NULL),(6921,'901602',867,'ทุ่งลาน','90230',NULL,NULL),(6922,'901603',867,'โคกม่วง','90230',NULL,NULL),(6923,'901604',867,'คลองหลา','90115',NULL,NULL),(6924,'910101',868,'พิมาน','91000',NULL,NULL),(6925,'910102',868,'คลองขุด','91000',NULL,NULL),(6926,'910103',868,'ควนขัน','91000',NULL,NULL),(6927,'910104',868,'บ้านควน','91140',NULL,NULL),(6928,'910105',868,'ฉลุง','91140',NULL,NULL),(6929,'910106',868,'เกาะสาหร่าย','91000',NULL,NULL),(6930,'910107',868,'ตันหยงโป','91000',NULL,NULL),(6931,'910108',868,'เจ๊ะบิลัง','91000',NULL,NULL),(6932,'910109',868,'ตำมะลัง','91000',NULL,NULL),(6933,'910110',868,'ปูยู','91000',NULL,NULL),(6934,'910111',868,'ควนโพธิ์','91140',NULL,NULL),(6935,'910112',868,'เกตรี','91140',NULL,NULL),(6936,'910201',869,'ควนโดน','91160',NULL,NULL),(6937,'910202',869,'ควนสตอ','91160',NULL,NULL),(6938,'910203',869,'ย่านซื่อ','91160',NULL,NULL),(6939,'910204',869,'วังประจัน','91160',NULL,NULL),(6940,'910301',870,'ทุ่งนุ้ย','91130',NULL,NULL),(6941,'910302',870,'ควนกาหลง','91130',NULL,NULL),(6942,'910303',870,'อุไดเจริญ','91130',NULL,NULL),(6943,'910401',871,'ท่าแพ','91150',NULL,NULL),(6944,'910402',871,'แป-ระ','91150',NULL,NULL),(6945,'910403',871,'สาคร','91150',NULL,NULL),(6946,'910404',871,'ท่าเรือ','91150',NULL,NULL),(6947,'910501',872,'กำแพง','91110',NULL,NULL),(6948,'910502',872,'ละงู','91110',NULL,NULL),(6949,'910503',872,'เขาขาว','91110',NULL,NULL),(6950,'910504',872,'ปากน้ำ','91110',NULL,NULL),(6951,'910505',872,'น้ำผุด','91110',NULL,NULL),(6952,'910506',872,'แหลมสน','91110',NULL,NULL),(6953,'910601',873,'ทุ่งหว้า','91120',NULL,NULL),(6954,'910602',873,'นาทอน','91120',NULL,NULL),(6955,'910603',873,'ขอนคลาน','91120',NULL,NULL),(6956,'910604',873,'ทุ่งบุหลัง','91120',NULL,NULL),(6957,'910605',873,'ป่าแก่บ่อหิน','91120',NULL,NULL),(6958,'910701',874,'ปาล์มพัฒนา','91130',NULL,NULL),(6959,'910702',874,'นิคมพัฒนา','91130',NULL,NULL),(6960,'920101',875,'ทับเที่ยง','92000',NULL,NULL),(6961,'920104',875,'นาพละ','92000',NULL,NULL),(6962,'920105',875,'บ้านควน','92000',NULL,NULL),(6963,'920106',875,'นาบินหลา','92000',NULL,NULL),(6964,'920107',875,'ควนปริง','92000',NULL,NULL),(6965,'920108',875,'นาโยงใต้','92170',NULL,NULL),(6966,'920109',875,'บางรัก','92000',NULL,NULL),(6967,'920110',875,'โคกหล่อ','92000',NULL,NULL),(6968,'920113',875,'นาโต๊ะหมิง','92000',NULL,NULL),(6969,'920114',875,'หนองตรุด','92000',NULL,NULL),(6970,'920115',875,'น้ำผุด','92000',NULL,NULL),(6971,'920117',875,'นาตาล่วง','92000',NULL,NULL),(6972,'920118',875,'บ้านโพธิ์','92000',NULL,NULL),(6973,'920119',875,'นาท่ามเหนือ','92190',NULL,NULL),(6974,'920120',875,'นาท่ามใต้','92190',NULL,NULL),(6975,'920201',876,'กันตัง','92110',NULL,NULL),(6976,'920202',876,'ควนธานี','92110',NULL,NULL),(6977,'920203',876,'บางหมาก','92110',NULL,NULL),(6978,'920204',876,'บางเป้า','92110',NULL,NULL),(6979,'920205',876,'วังวน','92110',NULL,NULL),(6980,'920206',876,'กันตังใต้','92110',NULL,NULL),(6981,'920207',876,'โคกยาง','92110',NULL,NULL),(6982,'920208',876,'คลองลุ','92110',NULL,NULL),(6983,'920209',876,'ย่านซื่อ','92110',NULL,NULL),(6984,'920210',876,'บ่อน้ำร้อน','92110',NULL,NULL),(6985,'920211',876,'บางสัก','92110',NULL,NULL),(6986,'920212',876,'นาเกลือ','92110',NULL,NULL),(6987,'920213',876,'เกาะลิบง','92110',NULL,NULL),(6988,'920214',876,'คลองชีล้อม','92110',NULL,NULL),(6989,'920221',876,'เกาะลูกไม้','0',NULL,NULL),(6990,'920222',876,'เกาะนก','0',NULL,NULL),(6991,'920301',877,'ย่านตาขาว','92140',NULL,NULL),(6992,'920302',877,'หนองบ่อ','92140',NULL,NULL),(6993,'920303',877,'นาชุมเห็ด','92140',NULL,NULL),(6994,'920304',877,'ในควน','92140',NULL,NULL),(6995,'920305',877,'โพรงจรเข้','92140',NULL,NULL),(6996,'920306',877,'ทุ่งกระบือ','92140',NULL,NULL),(6997,'920307',877,'ทุ่งค่าย','92140',NULL,NULL),(6998,'920308',877,'เกาะเปียะ','92140',NULL,NULL),(6999,'920401',878,'ท่าข้าม','92120',NULL,NULL),(7000,'920402',878,'ทุ่งยาว','92180',NULL,NULL),(7001,'920403',878,'ปะเหลียน','92180',NULL,NULL),(7002,'920404',878,'บางด้วน','92140',NULL,NULL),(7003,'920407',878,'บ้านนา','92140',NULL,NULL),(7004,'920409',878,'สุโสะ','92120',NULL,NULL),(7005,'920410',878,'ลิพัง','92180',NULL,NULL),(7006,'920411',878,'เกาะสุกร','92120',NULL,NULL),(7007,'920412',878,'ท่าพญา','92140',NULL,NULL),(7008,'920413',878,'แหลมสอม','92180',NULL,NULL),(7009,'920414',878,'เกาะค้างคาว','0',NULL,NULL),(7010,'920501',879,'บ่อหิน','92150',NULL,NULL),(7011,'920502',879,'เขาไม้แก้ว','92150',NULL,NULL),(7012,'920503',879,'กะลาเส','92150',NULL,NULL),(7013,'920504',879,'ไม้ฝาด','92150',NULL,NULL),(7014,'920505',879,'นาเมืองเพชร','92000',NULL,NULL),(7015,'920601',880,'ห้วยยอด','92130',NULL,NULL),(7016,'920602',880,'หนองช้างแล่น','92130',NULL,NULL),(7017,'920605',880,'บางดี','92210',NULL,NULL),(7018,'920606',880,'บางกุ้ง','92210',NULL,NULL),(7019,'920607',880,'เขากอบ','92130',NULL,NULL),(7020,'920608',880,'เขาขาว','92130',NULL,NULL),(7021,'920609',880,'เขาปูน','92130',NULL,NULL),(7022,'920610',880,'ปากแจ่ม','92190',NULL,NULL),(7023,'920611',880,'ปากคม','92130',NULL,NULL),(7024,'920614',880,'ท่างิ้ว','92130',NULL,NULL),(7025,'920615',880,'ลำภูรา','92190',NULL,NULL),(7026,'920616',880,'นาวง','92210',NULL,NULL),(7027,'920617',880,'ห้วยนาง','92130',NULL,NULL),(7028,'920619',880,'ในเตา','92130',NULL,NULL),(7029,'920620',880,'ทุ่งต่อ','92130',NULL,NULL),(7030,'920621',880,'วังคีรี','92210',NULL,NULL),(7031,'920701',881,'เขาวิเศษ','92220',NULL,NULL),(7032,'920702',881,'วังมะปราง','92220',NULL,NULL),(7033,'920703',881,'อ่าวตง','92220',NULL,NULL),(7034,'920704',881,'ท่าสะบ้า','92000',NULL,NULL),(7035,'920705',881,'วังมะปรางเหนือ','92220',NULL,NULL),(7036,'920801',882,'นาโยงเหนือ','92170',NULL,NULL),(7037,'920802',882,'ช่อง','92170',NULL,NULL),(7038,'920803',882,'ละมอ','92170',NULL,NULL),(7039,'920804',882,'โคกสะบ้า','92170',NULL,NULL),(7040,'920805',882,'นาหมื่นศรี','92170',NULL,NULL),(7041,'920806',882,'นาข้าวเสีย','92170',NULL,NULL),(7042,'920901',883,'ควนเมา','92160',NULL,NULL),(7043,'920902',883,'คลองปาง','92160',NULL,NULL),(7044,'920903',883,'หนองบัว','92160',NULL,NULL),(7045,'920904',883,'หนองปรือ','92130',NULL,NULL),(7046,'920905',883,'เขาไพร','92160',NULL,NULL),(7047,'921001',884,'หาดสำราญ','92120',NULL,NULL),(7048,'921002',884,'บ้าหวี','92120',NULL,NULL),(7049,'921003',884,'ตะเสะ','92120',NULL,NULL),(7050,'930101',885,'คูหาสวรรค์','93000',NULL,NULL),(7051,'930103',885,'เขาเจียก','93000',NULL,NULL),(7052,'930104',885,'ท่ามิหรำ','93000',NULL,NULL),(7053,'930105',885,'โคกชะงาย','93000',NULL,NULL),(7054,'930106',885,'นาท่อม','93000',NULL,NULL),(7055,'930107',885,'ปรางหมู่','93000',NULL,NULL),(7056,'930108',885,'ท่าแค','93000',NULL,NULL),(7057,'930109',885,'ลำปำ','93000',NULL,NULL),(7058,'930110',885,'ตำนาน','93000',NULL,NULL),(7059,'930111',885,'ควนมะพร้าว','93000',NULL,NULL),(7060,'930112',885,'ร่มเมือง','93000',NULL,NULL),(7061,'930113',885,'ชัยบุรี','93000',NULL,NULL),(7062,'930114',885,'นาโหนด','93000',NULL,NULL),(7063,'930115',885,'พญาขัน','93000',NULL,NULL),(7064,'930201',886,'กงหรา','93180',NULL,NULL),(7065,'930202',886,'ชะรัด','93000',NULL,NULL),(7066,'930203',886,'คลองเฉลิม','93180',NULL,NULL),(7067,'930204',886,'คลองทรายขาว','93180',NULL,NULL),(7068,'930205',886,'สมหวัง','93000',NULL,NULL),(7069,'930301',887,'เขาชัยสน','93130',NULL,NULL),(7070,'930302',887,'ควนขนุน','93130',NULL,NULL),(7071,'930305',887,'จองถนน','93130',NULL,NULL),(7072,'930306',887,'หานโพธิ์','93130',NULL,NULL),(7073,'930307',887,'โคกม่วง','93130',NULL,NULL),(7074,'930401',888,'แม่ขรี','93160',NULL,NULL),(7075,'930402',888,'ตะโหมด','93160',NULL,NULL),(7076,'930403',888,'คลองใหญ่','93160',NULL,NULL),(7077,'930501',889,'ควนขนุน','93110',NULL,NULL),(7078,'930502',889,'ทะเลน้อย','93150',NULL,NULL),(7079,'930504',889,'นาขยาด','93110',NULL,NULL),(7080,'930505',889,'พนมวังก์','93110',NULL,NULL),(7081,'930506',889,'แหลมโตนด','93110',NULL,NULL),(7082,'930508',889,'ปันแต','93110',NULL,NULL),(7083,'930509',889,'โตนดด้วน','93110',NULL,NULL),(7084,'930510',889,'ดอนทราย','93110',NULL,NULL),(7085,'930511',889,'มะกอกเหนือ','93150',NULL,NULL),(7086,'930512',889,'พนางตุง','93150',NULL,NULL),(7087,'930513',889,'ชะมวง','93110',NULL,NULL),(7088,'930516',889,'แพรกหา','93110',NULL,NULL),(7089,'930601',890,'ปากพะยูน','93120',NULL,NULL),(7090,'930602',890,'ดอนประดู่','93120',NULL,NULL),(7091,'930603',890,'เกาะนางคำ','93120',NULL,NULL),(7092,'930604',890,'เกาะหมาก','93120',NULL,NULL),(7093,'930605',890,'ฝาละมี','93120',NULL,NULL),(7094,'930606',890,'หารเทา','93120',NULL,NULL),(7095,'930607',890,'ดอนทราย','93120',NULL,NULL),(7096,'930701',891,'เขาย่า','93190',NULL,NULL),(7097,'930702',891,'เขาปู่','93190',NULL,NULL),(7098,'930703',891,'ตะแพน','93190',NULL,NULL),(7099,'930801',892,'ป่าบอน','93170',NULL,NULL),(7100,'930802',892,'โคกทราย','93170',NULL,NULL),(7101,'930803',892,'หนองธง','93170',NULL,NULL),(7102,'930804',892,'ทุ่งนารี','93170',NULL,NULL),(7103,'930806',892,'วังใหม่','93170',NULL,NULL),(7104,'930901',893,'ท่ามะเดื่อ','93140',NULL,NULL),(7105,'930902',893,'นาปะขอ','93140',NULL,NULL),(7106,'930903',893,'โคกสัก','93140',NULL,NULL),(7107,'931001',894,'ป่าพะยอม','93110',NULL,NULL),(7108,'931002',894,'ลานข่อย','93110',NULL,NULL),(7109,'931003',894,'เกาะเต่า','93110',NULL,NULL),(7110,'931004',894,'บ้านพร้าว','93110',NULL,NULL),(7111,'931101',895,'ชุมพล','93000',NULL,NULL),(7112,'931102',895,'บ้านนา','93000',NULL,NULL),(7113,'931103',895,'อ่างทอง','93000',NULL,NULL),(7114,'931104',895,'ลำสินธุ์','93000',NULL,NULL),(7115,'940101',896,'สะบารัง','94000',NULL,NULL),(7116,'940102',896,'อาเนาะรู','94000',NULL,NULL),(7117,'940103',896,'จะบังติกอ','94000',NULL,NULL),(7118,'940104',896,'บานา','94000',NULL,NULL),(7119,'940105',896,'ตันหยงลุโละ','94000',NULL,NULL),(7120,'940106',896,'คลองมานิง','94000',NULL,NULL),(7121,'940107',896,'กะมิยอ','94000',NULL,NULL),(7122,'940108',896,'บาราโหม','94000',NULL,NULL),(7123,'940109',896,'ปะกาฮะรัง','94000',NULL,NULL),(7124,'940110',896,'รูสะมิแล','94000',NULL,NULL),(7125,'940111',896,'ตะลุโบะ','94000',NULL,NULL),(7126,'940112',896,'บาราเฮาะ','94000',NULL,NULL),(7127,'940113',896,'ปุยุด','94000',NULL,NULL),(7128,'940201',897,'โคกโพธิ์','94120',NULL,NULL),(7129,'940202',897,'มะกรูด','94120',NULL,NULL),(7130,'940203',897,'บางโกระ','94120',NULL,NULL),(7131,'940204',897,'ป่าบอน','94120',NULL,NULL),(7132,'940205',897,'ทรายขาว','94120',NULL,NULL),(7133,'940206',897,'นาประดู่','94180',NULL,NULL),(7134,'940207',897,'ปากล่อ','94180',NULL,NULL),(7135,'940208',897,'ทุ่งพลา','94180',NULL,NULL),(7136,'940211',897,'ท่าเรือ','94120',NULL,NULL),(7137,'940213',897,'นาเกตุ','94120',NULL,NULL),(7138,'940214',897,'ควนโนรี','94180',NULL,NULL),(7139,'940215',897,'ช้างให้ตก','94120',NULL,NULL),(7140,'940301',898,'เกาะเปาะ','94170',NULL,NULL),(7141,'940302',898,'คอลอตันหยง','94170',NULL,NULL),(7142,'940303',898,'ดอนรัก','94170',NULL,NULL),(7143,'940304',898,'ดาโต๊ะ','94170',NULL,NULL),(7144,'940305',898,'ตุยง','94170',NULL,NULL),(7145,'940306',898,'ท่ากำชำ','94170',NULL,NULL),(7146,'940307',898,'บ่อทอง','94170',NULL,NULL),(7147,'940308',898,'บางเขา','94170',NULL,NULL),(7148,'940309',898,'บางตาวา','94170',NULL,NULL),(7149,'940310',898,'ปุโละปุโย','94170',NULL,NULL),(7150,'940311',898,'ยาบี','94170',NULL,NULL),(7151,'940312',898,'ลิปะสะโง','94170',NULL,NULL),(7152,'940401',899,'ปะนาเระ','94130',NULL,NULL),(7153,'940402',899,'ท่าข้าม','94130',NULL,NULL),(7154,'940403',899,'บ้านนอก','94130',NULL,NULL),(7155,'940404',899,'ดอน','94130',NULL,NULL),(7156,'940405',899,'ควน','94190',NULL,NULL),(7157,'940406',899,'ท่าน้ำ','94130',NULL,NULL),(7158,'940407',899,'คอกกระบือ','94130',NULL,NULL),(7159,'940408',899,'พ่อมิ่ง','94130',NULL,NULL),(7160,'940409',899,'บ้านกลาง','94130',NULL,NULL),(7161,'940410',899,'บ้านน้ำบ่อ','94130',NULL,NULL),(7162,'940501',900,'มายอ','94140',NULL,NULL),(7163,'940502',900,'ถนน','94140',NULL,NULL),(7164,'940503',900,'ตรัง','94140',NULL,NULL),(7165,'940504',900,'กระหวะ','94140',NULL,NULL),(7166,'940505',900,'ลุโบะยิไร','94140',NULL,NULL),(7167,'940506',900,'ลางา','94190',NULL,NULL),(7168,'940507',900,'กระเสาะ','94140',NULL,NULL),(7169,'940508',900,'เกาะจัน','94140',NULL,NULL),(7170,'940509',900,'ปะโด','94140',NULL,NULL),(7171,'940510',900,'สาคอบน','94140',NULL,NULL),(7172,'940511',900,'สาคอใต้','94140',NULL,NULL),(7173,'940512',900,'สะกำ','94140',NULL,NULL),(7174,'940513',900,'ปานัน','94140',NULL,NULL),(7175,'940601',901,'ตะโละแมะนา','94140',NULL,NULL),(7176,'940602',901,'พิเทน','94140',NULL,NULL),(7177,'940603',901,'น้ำดำ','94140',NULL,NULL),(7178,'940604',901,'ปากู','94140',NULL,NULL),(7179,'940701',902,'ตะลุปัน','94110',NULL,NULL),(7180,'940702',902,'ตะบิ้ง','94110',NULL,NULL),(7181,'940703',902,'ปะเสยะวอ','94110',NULL,NULL),(7182,'940704',902,'บางเก่า','94110',NULL,NULL),(7183,'940705',902,'บือเระ','94110',NULL,NULL),(7184,'940706',902,'เตราะบอน','94110',NULL,NULL),(7185,'940707',902,'กะดุนง','94110',NULL,NULL),(7186,'940708',902,'ละหาร','94110',NULL,NULL),(7187,'940709',902,'มะนังดาลำ','94110',NULL,NULL),(7188,'940710',902,'แป้น','94110',NULL,NULL),(7189,'940711',902,'ทุ่งคล้า','94190',NULL,NULL),(7190,'940801',903,'ไทรทอง','94220',NULL,NULL),(7191,'940802',903,'ไม้แก่น','94220',NULL,NULL),(7192,'940803',903,'ตะโละไกรทอง','94220',NULL,NULL),(7193,'940804',903,'คอนทราย','94220',NULL,NULL),(7194,'940901',904,'ตะโละ','94150',NULL,NULL),(7195,'940902',904,'ตะโละกาโปร์','94150',NULL,NULL),(7196,'940903',904,'ตันหยงดาลอ','94150',NULL,NULL),(7197,'940904',904,'ตันหยงจึงงา','94190',NULL,NULL),(7198,'940905',904,'ตอหลัง','94150',NULL,NULL),(7199,'940906',904,'ตาแกะ','94150',NULL,NULL),(7200,'940907',904,'ตาลีอายร์','94150',NULL,NULL),(7201,'940908',904,'ยามู','94150',NULL,NULL),(7202,'940909',904,'บางปู','94150',NULL,NULL),(7203,'940910',904,'หนองแรต','94150',NULL,NULL),(7204,'940911',904,'ปิยามุมัง','94150',NULL,NULL),(7205,'940912',904,'ปุลากง','94150',NULL,NULL),(7206,'940913',904,'บาโลย','94190',NULL,NULL),(7207,'940914',904,'สาบัน','94150',NULL,NULL),(7208,'940915',904,'มะนังยง','94150',NULL,NULL),(7209,'940916',904,'ราตาปันยัง','94150',NULL,NULL),(7210,'940917',904,'จะรัง','94150',NULL,NULL),(7211,'940918',904,'แหลมโพธิ์','94150',NULL,NULL),(7212,'941001',905,'ยะรัง','94160',NULL,NULL),(7213,'941002',905,'สะดาวา','94160',NULL,NULL),(7214,'941003',905,'ประจัน','94160',NULL,NULL),(7215,'941004',905,'สะนอ','94160',NULL,NULL),(7216,'941005',905,'ระแว้ง','94160',NULL,NULL),(7217,'941006',905,'ปิตูมุดี','94160',NULL,NULL),(7218,'941007',905,'วัด','94160',NULL,NULL),(7219,'941008',905,'กระโด','94160',NULL,NULL),(7220,'941009',905,'คลองใหม่','94160',NULL,NULL),(7221,'941010',905,'เมาะมาวี','94160',NULL,NULL),(7222,'941011',905,'กอลำ','94160',NULL,NULL),(7223,'941012',905,'เขาตูม','94160',NULL,NULL),(7224,'941101',906,'กะรุบี','94230',NULL,NULL),(7225,'941102',906,'ตะโละดือรามัน','94230',NULL,NULL),(7226,'941103',906,'ปล่องหอย','94230',NULL,NULL),(7227,'941201',907,'แม่ลาน','94180',NULL,NULL),(7228,'941202',907,'ม่วงเตี้ย','94180',NULL,NULL),(7229,'941203',907,'ป่าไร่','94180',NULL,NULL),(7230,'950101',908,'สะเตง','95000',NULL,NULL),(7231,'950102',908,'บุดี','95000',NULL,NULL),(7232,'950103',908,'ยุโป','95000',NULL,NULL),(7233,'950104',908,'ลิดล','95160',NULL,NULL),(7234,'950106',908,'ยะลา','95000',NULL,NULL),(7235,'950108',908,'ท่าสาป','95000',NULL,NULL),(7236,'950109',908,'ลำใหม่','95160',NULL,NULL),(7237,'950110',908,'หน้าถ้ำ','95000',NULL,NULL),(7238,'950111',908,'ลำพะยา','95160',NULL,NULL),(7239,'950112',908,'เปาะเส้ง','95000',NULL,NULL),(7240,'950114',908,'พร่อน','95160',NULL,NULL),(7241,'950115',908,'บันนังสาเรง','95000',NULL,NULL),(7242,'950116',908,'สะเตงนอก','95000',NULL,NULL),(7243,'950118',908,'ตาเซะ','95000',NULL,NULL),(7244,'950201',909,'เบตง','95110',NULL,NULL),(7245,'950202',909,'ยะรม','95110',NULL,NULL),(7246,'950203',909,'ตาเนาะแมเราะ','95110',NULL,NULL),(7247,'950204',909,'อัยเยอร์เวง','95110',NULL,NULL),(7248,'950205',909,'ธารน้ำทิพย์','95110',NULL,NULL),(7249,'950301',910,'บันนังสตา','95130',NULL,NULL),(7250,'950302',910,'บาเจาะ','95130',NULL,NULL),(7251,'950303',910,'ตาเนาะปูเต๊ะ','95130',NULL,NULL),(7252,'950304',910,'ถ้ำทะลุ','95130',NULL,NULL),(7253,'950305',910,'ตลิ่งชัน','95130',NULL,NULL),(7254,'950306',910,'เขื่อนบางลาง','95130',NULL,NULL),(7255,'950401',911,'ธารโต','95150',NULL,NULL),(7256,'950402',911,'บ้านแหร','95150',NULL,NULL),(7257,'950403',911,'แม่หวาด','95170',NULL,NULL),(7258,'950404',911,'คีรีเขต','95150',NULL,NULL),(7259,'950501',912,'ยะหา','95120',NULL,NULL),(7260,'950502',912,'ละแอ','95120',NULL,NULL),(7261,'950503',912,'ปะแต','95120',NULL,NULL),(7262,'950504',912,'บาโร๊ะ','95120',NULL,NULL),(7263,'950506',912,'ตาชี','95120',NULL,NULL),(7264,'950507',912,'บาโงยซิแน','95120',NULL,NULL),(7265,'950508',912,'กาตอง','95120',NULL,NULL),(7266,'950601',913,'กายูบอเกาะ','95140',NULL,NULL),(7267,'950602',913,'กาลูปัง','95140',NULL,NULL),(7268,'950603',913,'กาลอ','95140',NULL,NULL),(7269,'950604',913,'กอตอตือร๊ะ','95140',NULL,NULL),(7270,'950605',913,'โกตาบารู','95140',NULL,NULL),(7271,'950606',913,'เกะรอ','95140',NULL,NULL),(7272,'950607',913,'จะกว๊ะ','95140',NULL,NULL),(7273,'950608',913,'ท่าธง','95140',NULL,NULL),(7274,'950609',913,'เนินงาม','95140',NULL,NULL),(7275,'950610',913,'บาลอ','95140',NULL,NULL),(7276,'950611',913,'บาโงย','95140',NULL,NULL),(7277,'950612',913,'บือมัง','95140',NULL,NULL),(7278,'950613',913,'ยะต๊ะ','95140',NULL,NULL),(7279,'950614',913,'วังพญา','95140',NULL,NULL),(7280,'950615',913,'อาซ่อง','95140',NULL,NULL),(7281,'950616',913,'ตะโล๊ะหะลอ','95140',NULL,NULL),(7282,'950701',914,'กาบัง','95120',NULL,NULL),(7283,'950702',914,'บาละ','95120',NULL,NULL),(7284,'950801',915,'กรงปินัง','95000',NULL,NULL),(7285,'950802',915,'สะเอะ','95000',NULL,NULL),(7286,'950803',915,'ห้วยกระทิง','95000',NULL,NULL),(7287,'950804',915,'ปุโรง','95000',NULL,NULL),(7288,'960101',916,'บางนาค','96000',NULL,NULL),(7289,'960102',916,'ลำภู','96000',NULL,NULL),(7290,'960103',916,'มะนังตายอ','96000',NULL,NULL),(7291,'960104',916,'บางปอ','96000',NULL,NULL),(7292,'960105',916,'กะลุวอ','96000',NULL,NULL),(7293,'960106',916,'กะลุวอเหนือ','96000',NULL,NULL),(7294,'960107',916,'โคกเคียน','96000',NULL,NULL),(7295,'960201',917,'เจ๊ะเห','96110',NULL,NULL),(7296,'960202',917,'ไพรวัน','96110',NULL,NULL),(7297,'960203',917,'พร่อน','96110',NULL,NULL),(7298,'960204',917,'ศาลาใหม่','96110',NULL,NULL),(7299,'960205',917,'บางขุนทอง','96110',NULL,NULL),(7300,'960206',917,'เกาะสะท้อน','96110',NULL,NULL),(7301,'960207',917,'นานาค','96110',NULL,NULL),(7302,'960208',917,'โฆษิต','96110',NULL,NULL),(7303,'960301',918,'บาเจาะ','96170',NULL,NULL),(7304,'960302',918,'ลุโบะสาวอ','96170',NULL,NULL),(7305,'960303',918,'กาเยาะมาตี','96170',NULL,NULL),(7306,'960304',918,'ปะลุกาสาเมาะ','96170',NULL,NULL),(7307,'960305',918,'บาเระเหนือ','96170',NULL,NULL),(7308,'960306',918,'บาเระใต้','96170',NULL,NULL),(7309,'960401',919,'ยี่งอ','96180',NULL,NULL),(7310,'960402',919,'ละหาร','96180',NULL,NULL),(7311,'960403',919,'จอเบาะ','96180',NULL,NULL),(7312,'960404',919,'ลุโบะบายะ','96180',NULL,NULL),(7313,'960405',919,'ลุโบะบือซา','96180',NULL,NULL),(7314,'960406',919,'ตะปอเยาะ','96180',NULL,NULL),(7315,'960501',920,'ตันหยงมัส','96130',NULL,NULL),(7316,'960502',920,'ตันหยงลิมอ','96130',NULL,NULL),(7317,'960506',920,'บองอ','96220',NULL,NULL),(7318,'960507',920,'กาลิชา','96130',NULL,NULL),(7319,'960508',920,'บาโงสะโต','96130',NULL,NULL),(7320,'960509',920,'เฉลิม','96130',NULL,NULL),(7321,'960510',920,'มะรือโบตก','96130',NULL,NULL),(7322,'960601',921,'รือเสาะ','96150',NULL,NULL),(7323,'960602',921,'สาวอ','96150',NULL,NULL),(7324,'960603',921,'เรียง','96150',NULL,NULL),(7325,'960604',921,'สามัคคี','96150',NULL,NULL),(7326,'960605',921,'บาตง','96150',NULL,NULL),(7327,'960606',921,'ลาโละ','96150',NULL,NULL),(7328,'960607',921,'รือเสาะออก','96150',NULL,NULL),(7329,'960608',921,'โคกสะตอ','96150',NULL,NULL),(7330,'960609',921,'สุวารี','96150',NULL,NULL),(7331,'960701',922,'ซากอ','96210',NULL,NULL),(7332,'960702',922,'ตะมะยูง','96210',NULL,NULL),(7333,'960703',922,'ศรีสาคร','96210',NULL,NULL),(7334,'960704',922,'เชิงคีรี','96210',NULL,NULL),(7335,'960705',922,'กาหลง','96210',NULL,NULL),(7336,'960706',922,'ศรีบรรพต','96210',NULL,NULL),(7337,'960801',923,'แว้ง','96160',NULL,NULL),(7338,'960802',923,'กายูคละ','96160',NULL,NULL),(7339,'960803',923,'ฆอเลาะ','96160',NULL,NULL),(7340,'960804',923,'โละจูด','96160',NULL,NULL),(7341,'960805',923,'แม่ดง','96160',NULL,NULL),(7342,'960806',923,'เอราวัณ','96160',NULL,NULL),(7343,'960901',924,'มาโมง','96190',NULL,NULL),(7344,'960902',924,'สุคิริน','96190',NULL,NULL),(7345,'960903',924,'เกียร์','96190',NULL,NULL),(7346,'960904',924,'ภูเขาทอง','96190',NULL,NULL),(7347,'960905',924,'ร่มไทร','96190',NULL,NULL),(7348,'961001',925,'สุไหงโกลก','96120',NULL,NULL),(7349,'961002',925,'ป่าเสมัส','96120',NULL,NULL),(7350,'961003',925,'มูโนะ','96120',NULL,NULL),(7351,'961004',925,'ปูโยะ','96120',NULL,NULL),(7352,'961101',926,'ปะลุรู','96140',NULL,NULL),(7353,'961102',926,'สุไหงปาดี','96140',NULL,NULL),(7354,'961103',926,'โต๊ะเด็ง','96140',NULL,NULL),(7355,'961104',926,'สากอ','96140',NULL,NULL),(7356,'961105',926,'ริโก๋','96140',NULL,NULL),(7357,'961106',926,'กาวะ','96140',NULL,NULL),(7358,'961201',927,'จะแนะ','96220',NULL,NULL),(7359,'961202',927,'ดุซงญอ','96220',NULL,NULL),(7360,'961203',927,'ผดุงมาตร','96220',NULL,NULL),(7361,'961204',927,'ช้างเผือก','96220',NULL,NULL),(7362,'961301',928,'จวบ','96130',NULL,NULL),(7363,'961302',928,'บูกิต','96130',NULL,NULL),(7364,'961303',928,'มะรือโบออก','96130',NULL,NULL),(7365,'961304',43,'รามอินทรา','10230',NULL,NULL),(7366,'961305',801,'กระบี่ใหญ่','81000',NULL,NULL),(7367,'961306',45,'พลับพลา','10310',NULL,NULL),(7368,'961307',30,'จันทรเกษม','10900',NULL,NULL),(7369,'961308',45,'คลองเจ้าคุณสิงห์','10310',NULL,NULL),(7371,'961310',36,'สนามบิน','10210',NULL,NULL),(7372,'961311',817,'วิชิต','83000',NULL,NULL),(7374,'961312',198,'ในเมือง','30000',NULL,NULL),(7375,'961313',160,'บางพระ','23000',NULL,NULL),(7376,'961314',451,'โพนทอง','45110',NULL,NULL),(7377,'961315',27,'นวลจันทร์','10230',NULL,NULL),(7378,'961316',30,'จอมพล','10900',NULL,NULL),(7379,'961317',3,'คู้ขวา','10530',NULL,NULL),(7380,'961318',607,'ท่าสุด','57100',NULL,NULL),(7381,'961319',30,'เสนานิคม','10900',NULL,NULL),(7382,'961320',707,'หล่มสัก','67110',NULL,NULL),(7383,'961321',45,'สะพานสอง','10310',NULL,NULL),(7384,'961322',51,'ปากน้ำ','10270',NULL,NULL),(7385,'961323',34,'อ่อนนุช','10250',NULL,NULL),(7386,'961324',36,'ดอนเมือง','10210',NULL,NULL),(7387,'961325',54,'บางพึ่ง','10130',NULL,NULL),(7388,'961326',740,'ป่าสะแก','72120',NULL,NULL),(7389,'961327',15,'สำเหร่','10600',NULL,NULL),(7390,'961328',118,'ปากเพรียว','18000',NULL,NULL),(7391,'961329',30,'จตุจักร','10900',NULL,NULL),(7392,'961330',520,'แม่เหียะ','50100',NULL,NULL),(7395,'961333',14,'พญาไท','10400',NULL,NULL),(7396,'961334',15,'ดาวคะนอง','10600',NULL,NULL),(7397,'961335',726,'บ้านเหนือ','71000',NULL,NULL),(7398,'961336',553,'สบตุ๋ย','52100',NULL,NULL),(7399,'961337',553,'สวนดอก','52100',NULL,NULL),(7400,'961338',501,'หนองแสง','48000',NULL,NULL),(7405,'961343',44,'ราษฎร์พัฒนา','10240',NULL,NULL),(7406,'961344',44,'ทับช้าง','10250',NULL,NULL);
/*!40000 ALTER TABLE `sub_districts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subjects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,'ภาษาไทย','2025-12-12 10:31:57','2025-12-12 10:31:57'),(2,'คณิตศาสตร์','2025-12-12 10:32:06','2025-12-12 10:32:06'),(3,'วิทยาศาสตร์','2025-12-12 10:32:20','2025-12-12 10:32:20'),(4,'สังคมศึกษา/ศาสนาและวัฒนธรรม','2025-12-12 10:32:43','2025-12-12 10:32:43'),(5,'สุขศึกษาและพลศึกษา','2025-12-12 10:32:57','2025-12-12 10:32:57'),(6,'ศิลปะ','2025-12-12 10:33:21','2025-12-12 10:33:21'),(7,'การงานอาชีพและเทคโนโลยี','2025-12-12 10:33:34','2025-12-12 10:33:34'),(8,'ภาษาต่างประเทศ','2025-12-12 10:33:54','2025-12-12 10:33:54');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `targets`
--

DROP TABLE IF EXISTS `targets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `targets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `target_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `targets`
--

LOCK TABLES `targets` WRITE;
/*!40000 ALTER TABLE `targets` DISABLE KEYS */;
INSERT INTO `targets` VALUES (1,'เด็ก',NULL,NULL),(2,'สตรี',NULL,NULL),(3,'ครอบครัว',NULL,NULL),(4,'ผู้สูงอายุ',NULL,NULL),(5,'คนพิการ',NULL,NULL),(6,'คนไร้ที่พึ่ง',NULL,NULL),(7,'บุคคลไม่มีสถานะทางทะเบียน',NULL,NULL),(8,'บุคคลด้อยโอกาส',NULL,NULL),(9,'อื่น ๆ',NULL,NULL);
/*!40000 ALTER TABLE `targets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `titles`
--

DROP TABLE IF EXISTS `titles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `titles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `titles`
--

LOCK TABLES `titles` WRITE;
/*!40000 ALTER TABLE `titles` DISABLE KEYS */;
INSERT INTO `titles` VALUES (1,'เด็กชาย',NULL,NULL),(2,'เด็กหญิง',NULL,NULL),(3,'นาย',NULL,NULL),(4,'นาง',NULL,NULL),(5,'นางสาว',NULL,NULL);
/*!40000 ALTER TABLE `titles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `translates`
--

DROP TABLE IF EXISTS `translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `translate_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `translates_translate_name_index` (`translate_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translates`
--

LOCK TABLES `translates` WRITE;
/*!40000 ALTER TABLE `translates` DISABLE KEYS */;
INSERT INTO `translates` VALUES (1,'การกลับคืนสู่ครอบครัว','2026-01-10 04:53:00','2026-01-10 04:53:00'),(2,'การบรรลุนิติภาวะ/พ้นจากเกณฑ์การดูแล,','2026-01-10 04:53:22','2026-01-10 04:53:22'),(3,'การพบปัญหาการปรับตัว','2026-01-10 04:53:44','2026-01-10 04:53:44'),(4,'การรับบุตรบุญธรรม','2026-01-10 04:54:39','2026-01-10 04:54:39'),(5,'การมีครอบครัวอุปถัมภ์','2026-01-10 04:54:53','2026-01-10 05:21:49'),(6,'การส่งต่อการดูแล','2026-01-10 04:55:07','2026-01-10 04:55:07'),(7,'อื่น ๆ','2026-01-10 04:55:25','2026-01-10 04:55:25');
/*!40000 ALTER TABLE `translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'general_user',
  `status` varchar(255) NOT NULL DEFAULT '1',
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_project_id_foreign` (`project_id`),
  CONSTRAINT `users_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@gmail.com',NULL,'$2y$12$x7S7KAiJI5.7kEksXJ/WmuxNSGv4jbOKW2OOGgJJ8E3ydcyqFefG.',NULL,NULL,NULL,'admin','1',NULL,NULL,'2026-04-30 22:17:52','2026-04-30 22:17:52'),(2,'suchart','suchart@gmail.com',NULL,'$2y$12$EYc0eL81jxo8abA0zXccq.j5/43wmg0SZKsrtF9ymthvuJ8Crrpuu',NULL,NULL,NULL,'executive','1',NULL,NULL,'2026-05-01 01:32:05','2026-05-01 01:32:05'),(3,'teacher','teacher@gmail.com',NULL,'$2y$12$rmz7cpqcb255qZEmekcNKujJwVyszPjUilGeTbcwLowh2G6f619wu',NULL,NULL,NULL,'teacher_caregiver','1',NULL,NULL,'2026-05-04 02:48:28','2026-05-04 02:48:28');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vaccinations`
--

DROP TABLE IF EXISTS `vaccinations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vaccinations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `vaccine_name` varchar(255) NOT NULL,
  `hospital` varchar(255) DEFAULT NULL,
  `recorder` varchar(255) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vaccinations_client_id_foreign` (`client_id`),
  CONSTRAINT `vaccinations_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vaccinations`
--

LOCK TABLES `vaccinations` WRITE;
/*!40000 ALTER TABLE `vaccinations` DISABLE KEYS */;
/*!40000 ALTER TABLE `vaccinations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visit_families`
--

DROP TABLE IF EXISTS `visit_families`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visit_families` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `visit_date` date NOT NULL,
  `count` int(10) unsigned NOT NULL,
  `family_fname` varchar(100) NOT NULL,
  `family_age` int(11) DEFAULT NULL,
  `member` varchar(100) DEFAULT NULL,
  `residence_status` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `moo` varchar(255) DEFAULT NULL,
  `soi` varchar(255) DEFAULT NULL,
  `road` varchar(255) DEFAULT NULL,
  `village` varchar(255) DEFAULT NULL,
  `province_id` bigint(20) unsigned DEFAULT NULL,
  `district_id` bigint(20) unsigned DEFAULT NULL,
  `sub_district_id` bigint(20) unsigned DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `outside_address` text DEFAULT NULL,
  `inside_address` text DEFAULT NULL,
  `environment` text DEFAULT NULL,
  `neighbor` text DEFAULT NULL,
  `member_relation` text DEFAULT NULL,
  `income_id` bigint(20) unsigned DEFAULT NULL,
  `problem` text DEFAULT NULL,
  `need` text DEFAULT NULL,
  `diagnose` text DEFAULT NULL,
  `assistance` text DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `modify` text DEFAULT NULL,
  `teacher` varchar(100) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `visit_families_client_id_foreign` (`client_id`),
  KEY `visit_families_income_id_foreign` (`income_id`),
  CONSTRAINT `visit_families_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `visit_families_income_id_foreign` FOREIGN KEY (`income_id`) REFERENCES `incomes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visit_families`
--

LOCK TABLES `visit_families` WRITE;
/*!40000 ALTER TABLE `visit_families` DISABLE KEYS */;
/*!40000 ALTER TABLE `visit_families` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-28 20:55:11
