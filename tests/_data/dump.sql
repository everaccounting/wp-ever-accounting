-- MySQL dump 10.13  Distrib 8.3.0, for macos13.6 (arm64)
--
-- Host: localhost    Database: wordpress
-- ------------------------------------------------------
-- Server version	8.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `test_commentmeta`
--

DROP TABLE IF EXISTS `test_commentmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_commentmeta` (
  `meta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `comment_id` (`comment_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_commentmeta`
--

LOCK TABLES `test_commentmeta` WRITE;
/*!40000 ALTER TABLE `test_commentmeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_commentmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_comments`
--

DROP TABLE IF EXISTS `test_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_comments` (
  `comment_ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `comment_post_ID` bigint unsigned NOT NULL DEFAULT '0',
  `comment_author` tinytext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `comment_author_email` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_author_url` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_author_IP` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_content` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `comment_karma` int NOT NULL DEFAULT '0',
  `comment_approved` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '1',
  `comment_agent` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'comment',
  `comment_parent` bigint unsigned NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_ID`),
  KEY `comment_post_ID` (`comment_post_ID`),
  KEY `comment_approved_date_gmt` (`comment_approved`,`comment_date_gmt`),
  KEY `comment_date_gmt` (`comment_date_gmt`),
  KEY `comment_parent` (`comment_parent`),
  KEY `comment_author_email` (`comment_author_email`(10))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_comments`
--

LOCK TABLES `test_comments` WRITE;
/*!40000 ALTER TABLE `test_comments` DISABLE KEYS */;
INSERT INTO `test_comments` VALUES (1,1,'A WordPress Commenter','wapuu@wordpress.example','https://wordpress.org/','','2024-04-21 09:06:27','2024-04-21 09:06:27','Hi, this is a comment.\nTo get started with moderating, editing, and deleting comments, please visit the Comments screen in the dashboard.\nCommenter avatars come from <a href=\"https://en.gravatar.com/\">Gravatar</a>.',0,'1','','comment',0,0);
/*!40000 ALTER TABLE `test_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_ea_accounts`
--

DROP TABLE IF EXISTS `test_ea_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_ea_accounts` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'Account Name',
  `number` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'Account Number',
  `opening_balance` double(15,4) NOT NULL DEFAULT '0.0000',
  `currency_code` varchar(3) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'USD',
  `bank_name` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `bank_phone` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `bank_address` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `thumbnail_id` int DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `creator_id` int DEFAULT NULL,
  `date_created` datetime DEFAULT NULL COMMENT 'Create Date',
  PRIMARY KEY (`id`),
  UNIQUE KEY `number` (`number`),
  KEY `currency_code` (`currency_code`),
  KEY `enabled` (`enabled`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_ea_accounts`
--

LOCK TABLES `test_ea_accounts` WRITE;
/*!40000 ALTER TABLE `test_ea_accounts` DISABLE KEYS */;
INSERT INTO `test_ea_accounts` VALUES (1,'Cash','001',0.0000,'USD',NULL,NULL,NULL,NULL,1,1,'2024-04-21 09:06:37');
/*!40000 ALTER TABLE `test_ea_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_ea_categories`
--

DROP TABLE IF EXISTS `test_ea_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_ea_categories` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `color` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `date_created` datetime DEFAULT NULL COMMENT 'Create Date',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`type`),
  KEY `type` (`type`),
  KEY `enabled` (`enabled`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_ea_categories`
--

LOCK TABLES `test_ea_categories` WRITE;
/*!40000 ALTER TABLE `test_ea_categories` DISABLE KEYS */;
INSERT INTO `test_ea_categories` VALUES (1,'Deposit','income','#a980f2',1,'2024-04-21 09:06:37'),(2,'Other','expense','#e863a4',1,'2024-04-21 09:06:37'),(3,'Sales','income','#b40d03',1,'2024-04-21 09:06:37'),(4,'Transfer','other','#d96140',1,'2024-04-21 09:06:37');
/*!40000 ALTER TABLE `test_ea_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_ea_contactmeta`
--

DROP TABLE IF EXISTS `test_ea_contactmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_ea_contactmeta` (
  `meta_id` bigint NOT NULL AUTO_INCREMENT,
  `contact_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `contact_id` (`contact_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_ea_contactmeta`
--

LOCK TABLES `test_ea_contactmeta` WRITE;
/*!40000 ALTER TABLE `test_ea_contactmeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_ea_contactmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_ea_contacts`
--

DROP TABLE IF EXISTS `test_ea_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_ea_contacts` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `company` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `vat_number` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `street` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `postcode` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `country` varchar(3) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `currency_code` varchar(3) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `type` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Customer or vendor',
  `thumbnail_id` int DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `creator_id` int DEFAULT NULL,
  `date_created` datetime DEFAULT NULL COMMENT 'Create Date',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `name` (`name`),
  KEY `email` (`email`),
  KEY `phone` (`phone`),
  KEY `enabled` (`enabled`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_ea_contacts`
--

LOCK TABLES `test_ea_contacts` WRITE;
/*!40000 ALTER TABLE `test_ea_contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_ea_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_ea_document_items`
--

DROP TABLE IF EXISTS `test_ea_document_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_ea_document_items` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `document_id` int DEFAULT NULL,
  `item_id` int DEFAULT NULL,
  `item_name` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `price` double(15,4) NOT NULL,
  `quantity` double(7,2) NOT NULL DEFAULT '0.00',
  `subtotal` double(15,4) NOT NULL DEFAULT '0.0000',
  `tax_rate` double(15,4) NOT NULL DEFAULT '0.0000',
  `discount` double(15,4) NOT NULL DEFAULT '0.0000',
  `tax` double(15,4) NOT NULL DEFAULT '0.0000',
  `total` double(15,4) NOT NULL DEFAULT '0.0000',
  `currency_code` varchar(3) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'USD',
  `extra` longtext COLLATE utf8mb4_unicode_520_ci,
  `date_created` datetime DEFAULT NULL COMMENT 'Create Date',
  PRIMARY KEY (`id`),
  KEY `document_id` (`document_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_ea_document_items`
--

LOCK TABLES `test_ea_document_items` WRITE;
/*!40000 ALTER TABLE `test_ea_document_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_ea_document_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_ea_documents`
--

DROP TABLE IF EXISTS `test_ea_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_ea_documents` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `document_number` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `type` varchar(60) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `order_number` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `status` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `issue_date` datetime DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `category_id` int NOT NULL,
  `contact_id` int NOT NULL,
  `address` longtext COLLATE utf8mb4_unicode_520_ci,
  `discount` double(15,4) DEFAULT '0.0000',
  `discount_type` enum('percentage','fixed') COLLATE utf8mb4_unicode_520_ci DEFAULT 'percentage',
  `subtotal` double(15,4) DEFAULT '0.0000',
  `total_tax` double(15,4) DEFAULT '0.0000',
  `total_discount` double(15,4) DEFAULT '0.0000',
  `total_fees` double(15,4) DEFAULT '0.0000',
  `total_shipping` double(15,4) DEFAULT '0.0000',
  `total` double(15,4) DEFAULT '0.0000',
  `tax_inclusive` tinyint(1) NOT NULL DEFAULT '0',
  `note` text COLLATE utf8mb4_unicode_520_ci,
  `terms` text COLLATE utf8mb4_unicode_520_ci,
  `attachment_id` int DEFAULT NULL,
  `currency_code` varchar(3) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'USD',
  `currency_rate` double(15,8) NOT NULL DEFAULT '1.00000000',
  `key` varchar(30) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `date_created` datetime DEFAULT NULL COMMENT 'Create Date',
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_number` (`document_number`),
  KEY `type` (`type`),
  KEY `status` (`status`),
  KEY `issue_date` (`issue_date`),
  KEY `contact_id` (`contact_id`),
  KEY `category_id` (`category_id`),
  KEY `total` (`total`),
  KEY `currency_code` (`currency_code`),
  KEY `currency_rate` (`currency_rate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_ea_documents`
--

LOCK TABLES `test_ea_documents` WRITE;
/*!40000 ALTER TABLE `test_ea_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_ea_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_ea_items`
--

DROP TABLE IF EXISTS `test_ea_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_ea_items` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `sku` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT '',
  `description` text COLLATE utf8mb4_unicode_520_ci,
  `sale_price` double(15,4) NOT NULL,
  `purchase_price` double(15,4) NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `category_id` int DEFAULT NULL,
  `sales_tax` double(15,4) DEFAULT NULL,
  `purchase_tax` double(15,4) DEFAULT NULL,
  `thumbnail_id` int DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `creator_id` int DEFAULT NULL,
  `date_created` datetime DEFAULT NULL COMMENT 'Create Date',
  PRIMARY KEY (`id`),
  KEY `sale_price` (`sale_price`),
  KEY `purchase_price` (`purchase_price`),
  KEY `category_id` (`category_id`),
  KEY `quantity` (`quantity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_ea_items`
--

LOCK TABLES `test_ea_items` WRITE;
/*!40000 ALTER TABLE `test_ea_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_ea_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_ea_notes`
--

DROP TABLE IF EXISTS `test_ea_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_ea_notes` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `parent_id` int NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_520_ci,
  `extra` longtext COLLATE utf8mb4_unicode_520_ci,
  `creator_id` int DEFAULT NULL,
  `date_created` datetime DEFAULT NULL COMMENT 'Create Date',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_ea_notes`
--

LOCK TABLES `test_ea_notes` WRITE;
/*!40000 ALTER TABLE `test_ea_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_ea_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_ea_transactions`
--

DROP TABLE IF EXISTS `test_ea_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_ea_transactions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `type` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `payment_date` date NOT NULL,
  `amount` double(15,4) NOT NULL,
  `currency_code` varchar(3) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'USD',
  `currency_rate` double(15,8) NOT NULL DEFAULT '1.00000000',
  `account_id` int NOT NULL,
  `document_id` int DEFAULT NULL,
  `contact_id` int DEFAULT NULL,
  `category_id` int NOT NULL,
  `description` text COLLATE utf8mb4_unicode_520_ci,
  `payment_method` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `reference` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `attachment_id` int DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `reconciled` tinyint(1) NOT NULL DEFAULT '0',
  `creator_id` int DEFAULT NULL,
  `date_created` datetime DEFAULT NULL COMMENT 'Create Date',
  PRIMARY KEY (`id`),
  KEY `amount` (`amount`),
  KEY `currency_code` (`currency_code`),
  KEY `currency_rate` (`currency_rate`),
  KEY `type` (`type`),
  KEY `account_id` (`account_id`),
  KEY `document_id` (`document_id`),
  KEY `category_id` (`category_id`),
  KEY `contact_id` (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_ea_transactions`
--

LOCK TABLES `test_ea_transactions` WRITE;
/*!40000 ALTER TABLE `test_ea_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_ea_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_ea_transfers`
--

DROP TABLE IF EXISTS `test_ea_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_ea_transfers` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `income_id` int NOT NULL,
  `expense_id` int NOT NULL,
  `creator_id` int DEFAULT NULL,
  `date_created` datetime DEFAULT NULL COMMENT 'Create Date',
  PRIMARY KEY (`id`),
  KEY `income_id` (`income_id`),
  KEY `expense_id` (`expense_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_ea_transfers`
--

LOCK TABLES `test_ea_transfers` WRITE;
/*!40000 ALTER TABLE `test_ea_transfers` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_ea_transfers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_eac_currencies`
--

DROP TABLE IF EXISTS `test_eac_currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_eac_currencies` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `exchange_rate` double(15,4) NOT NULL DEFAULT '1.0000',
  `precision` int NOT NULL DEFAULT '0',
  `symbol` varchar(5) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `subunit` int NOT NULL DEFAULT '100',
  `position` enum('before','after') COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'before',
  `thousand_separator` varchar(5) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT ',',
  `decimal_separator` varchar(5) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '.',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `date_updated` datetime DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `name` (`name`),
  KEY `exchange_rate` (`exchange_rate`),
  KEY `enabled` (`enabled`)
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_eac_currencies`
--

LOCK TABLES `test_eac_currencies` WRITE;
/*!40000 ALTER TABLE `test_eac_currencies` DISABLE KEYS */;
INSERT INTO `test_eac_currencies` VALUES (1,'AED','UAE Dirham',1.0000,2,'د.إ',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(2,'AFN','Afghani',1.0000,2,'؋',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(3,'ALL','Lek',1.0000,2,'L',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(4,'AMD','Armenian Dram',1.0000,2,'դր.',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(5,'ANG','Netherlands Antillean Guilder',1.0000,2,'ƒ',100,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(6,'AOA','Kwanza',1.0000,2,'Kz',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(7,'ARS','Argentine Peso',1.0000,2,'$',100,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(8,'AUD','Australian Dollar',1.0000,2,'$',100,'before',' ','.',0,NULL,'2024-04-21 09:06:37'),(9,'AWG','Aruban Florin',1.0000,2,'ƒ',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(10,'AZN','Azerbaijanian Manat',1.0000,2,'₼',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(11,'BAM','Convertible Mark',1.0000,2,'КМ',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(12,'BBD','Barbados Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(13,'BDT','Bangladeshi Taka',1.0000,2,'৳',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(14,'BGN','Bulgarian Lev',1.0000,2,'лв',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(15,'BHD','Bahraini Dinar',1.0000,3,'ب.د',1000,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(16,'BIF','Burundi Franc',1.0000,0,'Fr',1,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(17,'BMD','Bermudian Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(18,'BND','Brunei Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(19,'BOB','Boliviano',1.0000,2,'Bs.',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(20,'BOV','Mvdol',1.0000,2,'Bs.',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(21,'BRL','Brazilian Real',1.0000,2,'R$',100,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(22,'BSD','Bahamian Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(23,'BTN','Ngultrum',1.0000,2,'Nu.',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(24,'BWP','Pula',1.0000,2,'P',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(25,'BYN','Belarussian Ruble',1.0000,0,'Br',1,'before',' ',',',0,NULL,'2024-04-21 09:06:37'),(26,'BZD','Belize Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(27,'CAD','Canadian Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(28,'CDF','Congolese Franc',1.0000,2,'Fr',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(29,'CHF','Swiss Franc',1.0000,2,'CHF',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(30,'CLF','Unidades de fomento',1.0000,0,'UF',1,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(31,'CLP','Chilean Peso',1.0000,0,'$',1,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(32,'CNY','Yuan Renminbi',1.0000,2,'¥',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(33,'COP','Colombian Peso',1.0000,2,'$',100,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(34,'CRC','Costa Rican Colon',1.0000,2,'₡',100,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(35,'CUC','Peso Convertible',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(36,'CUP','Cuban Peso',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(37,'CVE','Cape Verde Escudo',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(38,'CZK','Czech Koruna',1.0000,2,'Kč',100,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(39,'DJF','Djibouti Franc',1.0000,0,'Fdj',1,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(40,'DKK','Danish Krone',1.0000,2,'kr',100,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(41,'DOP','Dominican Peso',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(42,'DZD','Algerian Dinar',1.0000,2,'د.ج',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(43,'EGP','Egyptian Pound',1.0000,2,'ج.م',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(44,'ERN','Nakfa',1.0000,2,'Nfk',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(45,'ETB','Ethiopian Birr',1.0000,2,'Br',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(46,'EUR','Euro',1.0000,2,'€',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(47,'FJD','Fiji Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(48,'FKP','Falkland Islands Pound',1.0000,2,'£',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(49,'GBP','Pound Sterling',1.0000,2,'£',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(50,'GEL','Lari',1.0000,2,'ლ',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(51,'GHS','Ghana Cedi',1.0000,2,'₵',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(52,'GIP','Gibraltar Pound',1.0000,2,'£',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(53,'GMD','Dalasi',1.0000,2,'D',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(54,'GNF','Guinea Franc',1.0000,0,'Fr',1,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(55,'GTQ','Quetzal',1.0000,2,'Q',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(56,'GYD','Guyana Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(57,'HKD','Hong Kong Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(58,'HNL','Lempira',1.0000,2,'L',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(59,'HRK','Croatian Kuna',1.0000,2,'kn',100,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(60,'HTG','Gourde',1.0000,2,'G',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(61,'HUF','Forint',1.0000,2,'Ft',100,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(62,'IDR','Rupiah',1.0000,2,'Rp',100,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(63,'ILS','New Israeli Sheqel',1.0000,2,'₪',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(64,'INR','Indian Rupee',1.0000,2,'₹',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(65,'IQD','Iraqi Dinar',1.0000,3,'ع.د',1000,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(66,'IRR','Iranian Rial',1.0000,2,'﷼',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(67,'ISK','Iceland Krona',1.0000,0,'kr',1,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(68,'JMD','Jamaican Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(69,'JOD','Jordanian Dinar',1.0000,3,'د.ا',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(70,'JPY','Yen',1.0000,0,'¥',1,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(71,'KES','Kenyan Shilling',1.0000,2,'KSh',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(72,'KGS','Som',1.0000,2,'som',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(73,'KHR','Riel',1.0000,2,'៛',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(74,'KMF','Comoro Franc',1.0000,0,'Fr',1,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(75,'KPW','North Korean Won',1.0000,2,'₩',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(76,'KRW','Won',1.0000,0,'₩',1,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(77,'KWD','Kuwaiti Dinar',1.0000,3,'د.ك',1000,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(78,'KYD','Cayman Islands Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(79,'KZT','Tenge',1.0000,2,'〒',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(80,'LAK','Kip',1.0000,2,'₭',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(81,'LBP','Lebanese Pound',1.0000,2,'ل.ل',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(82,'LKR','Sri Lanka Rupee',1.0000,2,'₨',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(83,'LRD','Liberian Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(84,'LSL','Loti',1.0000,2,'L',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(85,'LTL','Lithuanian Litas',1.0000,2,'Lt',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(86,'LVL','Latvian Lats',1.0000,2,'Ls',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(87,'LYD','Libyan Dinar',1.0000,3,'ل.د',1000,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(88,'MAD','Moroccan Dirham',1.0000,2,'د.م.',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(89,'MDL','Moldovan Leu',1.0000,2,'L',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(90,'MGA','Malagasy Ariary',1.0000,2,'Ar',5,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(91,'MKD','Denar',1.0000,2,'ден',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(92,'MMK','Kyat',1.0000,2,'K',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(93,'MNT','Tugrik',1.0000,2,'₮',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(94,'MOP','Pataca',1.0000,2,'P',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(95,'MRO','Ouguiya',1.0000,2,'UM',5,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(96,'MUR','Mauritius Rupee',1.0000,2,'₨',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(97,'MVR','Rufiyaa',1.0000,2,'MVR',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(98,'MWK','Kwacha',1.0000,2,'MK',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(99,'MXN','Mexican Peso',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(100,'MYR','Malaysian Ringgit',1.0000,2,'RM',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(101,'MZN','Mozambique Metical',1.0000,2,'MTn',100,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(102,'NAD','Namibia Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(103,'NGN','Naira',1.0000,2,'₦',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(104,'NIO','Cordoba Oro',1.0000,2,'C$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(105,'NOK','Norwegian Krone',1.0000,2,'kr',100,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(106,'NPR','Nepalese Rupee',1.0000,2,'₨',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(107,'NZD','New Zealand Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(108,'OMR','Rial Omani',1.0000,3,'ر.ع.',1000,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(109,'PAB','Balboa',1.0000,2,'B/.',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(110,'PEN','Sol',1.0000,2,'S/',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(111,'PGK','Kina',1.0000,2,'K',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(112,'PHP','Philippine Peso',1.0000,2,'₱',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(113,'PKR','Pakistan Rupee',1.0000,2,'₨',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(114,'PLN','Zloty',1.0000,2,'zł',100,'before',' ',',',0,NULL,'2024-04-21 09:06:37'),(115,'PYG','Guarani',1.0000,0,'₲',1,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(116,'QAR','Qatari Rial',1.0000,2,'ر.ق',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(117,'RON','New Romanian Leu',1.0000,2,'Lei',100,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(118,'RSD','Serbian Dinar',1.0000,2,'РСД',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(119,'RUB','Russian Ruble',1.0000,2,'₽',100,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(120,'RWF','Rwanda Franc',1.0000,0,'FRw',1,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(121,'SAR','Saudi Riyal',1.0000,2,'ر.س',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(122,'SBD','Solomon Islands Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(123,'SCR','Seychelles Rupee',1.0000,2,'₨',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(124,'SDG','Sudanese Pound',1.0000,2,'£',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(125,'SEK','Swedish Krona',1.0000,2,'kr',100,'before',' ',',',0,NULL,'2024-04-21 09:06:37'),(126,'SGD','Singapore Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(127,'SHP','Saint Helena Pound',1.0000,2,'£',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(128,'SLL','Leone',1.0000,2,'Le',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(129,'SOS','Somali Shilling',1.0000,2,'Sh',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(130,'SRD','Surinam Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(131,'SSP','South Sudanese Pound',1.0000,2,'£',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(132,'STD','Dobra',1.0000,2,'Db',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(133,'SVC','El Salvador Colon',1.0000,2,'₡',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(134,'SYP','Syrian Pound',1.0000,2,'£S',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(135,'SZL','Lilangeni',1.0000,2,'E',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(136,'THB','Baht',1.0000,2,'฿',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(137,'TJS','Somoni',1.0000,2,'ЅМ',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(138,'TMT','Turkmenistan New Manat',1.0000,2,'T',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(139,'TND','Tunisian Dinar',1.0000,3,'د.ت',1000,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(140,'TOP','Pa’anga',1.0000,2,'T$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(141,'TRY','Turkish Lira',1.0000,2,'₺',100,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(142,'TTD','Trinidad and Tobago Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(143,'TWD','New Taiwan Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(144,'TZS','Tanzanian Shilling',1.0000,2,'Sh',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(145,'UAH','Hryvnia',1.0000,2,'₴',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(146,'UGX','Uganda Shilling',1.0000,0,'USh',1,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(147,'USD','US Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(148,'UYU','Peso Uruguayo',1.0000,2,'$',100,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(149,'VEF','Bolivar',1.0000,2,'Bs F',100,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(150,'VND','Dong',1.0000,0,'₫',1,'before','.',',',0,NULL,'2024-04-21 09:06:37'),(151,'VUV','Vatu',1.0000,0,'Vt',1,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(152,'WST','Tala',1.0000,2,'T',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(153,'XAF','CFA Franc BEAC',1.0000,0,'Fr',1,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(154,'XAG','Silver',1.0000,0,'oz t',1,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(155,'XAU','Gold',1.0000,0,'oz t',1,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(156,'XCD','East Caribbean Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(157,'XDR','SDR (Special Drawing Right)',1.0000,0,'SDR',1,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(158,'XOF','CFA Franc BCEAO',1.0000,0,'Fr',1,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(159,'XPF','CFP Franc',1.0000,0,'Fr',1,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(160,'YER','Yemeni Rial',1.0000,2,'﷼',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(161,'ZAR','Rand',1.0000,2,'R',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(162,'ZMW','Zambian Kwacha',1.0000,2,'ZK',100,'before',',','.',0,NULL,'2024-04-21 09:06:37'),(163,'ZWL','Zimbabwe Dollar',1.0000,2,'$',100,'before',',','.',0,NULL,'2024-04-21 09:06:37');
/*!40000 ALTER TABLE `test_eac_currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_links`
--

DROP TABLE IF EXISTS `test_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_links` (
  `link_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `link_url` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_image` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_target` varchar(25) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_description` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_visible` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'Y',
  `link_owner` bigint unsigned NOT NULL DEFAULT '1',
  `link_rating` int NOT NULL DEFAULT '0',
  `link_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link_rel` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_notes` mediumtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `link_rss` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`link_id`),
  KEY `link_visible` (`link_visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_links`
--

LOCK TABLES `test_links` WRITE;
/*!40000 ALTER TABLE `test_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_options`
--

DROP TABLE IF EXISTS `test_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_options` (
  `option_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `option_value` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `autoload` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`),
  KEY `autoload` (`autoload`)
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_options`
--

LOCK TABLES `test_options` WRITE;
/*!40000 ALTER TABLE `test_options` DISABLE KEYS */;
INSERT INTO `test_options` VALUES (1,'siteurl','https://example.com','yes'),(2,'home','https://example.com','yes'),(3,'blogname','Tests','yes'),(4,'blogdescription','','yes'),(5,'users_can_register','0','yes'),(6,'admin_email','admin@example.com','yes'),(7,'start_of_week','1','yes'),(8,'use_balanceTags','0','yes'),(9,'use_smilies','1','yes'),(10,'require_name_email','1','yes'),(11,'comments_notify','1','yes'),(12,'posts_per_rss','10','yes'),(13,'rss_use_excerpt','0','yes'),(14,'mailserver_url','mail.example.com','yes'),(15,'mailserver_login','login@example.com','yes'),(16,'mailserver_pass','password','yes'),(17,'mailserver_port','110','yes'),(18,'default_category','1','yes'),(19,'default_comment_status','open','yes'),(20,'default_ping_status','open','yes'),(21,'default_pingback_flag','1','yes'),(22,'posts_per_page','10','yes'),(23,'date_format','F j, Y','yes'),(24,'time_format','g:i a','yes'),(25,'links_updated_date_format','F j, Y g:i a','yes'),(26,'comment_moderation','0','yes'),(27,'moderation_notify','1','yes'),(28,'permalink_structure','/%year%/%monthnum%/%postname%/','yes'),(29,'rewrite_rules','a:97:{s:11:\"^wp-json/?$\";s:22:\"index.php?rest_route=/\";s:14:\"^wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:21:\"^index.php/wp-json/?$\";s:22:\"index.php?rest_route=/\";s:24:\"^index.php/wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:17:\"^wp-sitemap\\.xml$\";s:23:\"index.php?sitemap=index\";s:17:\"^wp-sitemap\\.xsl$\";s:36:\"index.php?sitemap-stylesheet=sitemap\";s:23:\"^wp-sitemap-index\\.xsl$\";s:34:\"index.php?sitemap-stylesheet=index\";s:48:\"^wp-sitemap-([a-z]+?)-([a-z\\d_-]+?)-(\\d+?)\\.xml$\";s:75:\"index.php?sitemap=$matches[1]&sitemap-subtype=$matches[2]&paged=$matches[3]\";s:34:\"^wp-sitemap-([a-z]+?)-(\\d+?)\\.xml$\";s:47:\"index.php?sitemap=$matches[1]&paged=$matches[2]\";s:41:\"^eaccounting/invoice/([0-9]{1,})/(.*)?/?$\";s:73:\"index.php?eaccounting=true&ea_page=invoice&id=$matches[1]&key=$matches[2]\";s:38:\"^eaccounting/bill/([0-9]{1,})/(.*)?/?$\";s:70:\"index.php?eaccounting=true&ea_page=bill&id=$matches[1]&key=$matches[2]\";s:47:\"category/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:42:\"category/(.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:23:\"category/(.+?)/embed/?$\";s:46:\"index.php?category_name=$matches[1]&embed=true\";s:35:\"category/(.+?)/page/?([0-9]{1,})/?$\";s:53:\"index.php?category_name=$matches[1]&paged=$matches[2]\";s:17:\"category/(.+?)/?$\";s:35:\"index.php?category_name=$matches[1]\";s:44:\"tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:39:\"tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:20:\"tag/([^/]+)/embed/?$\";s:36:\"index.php?tag=$matches[1]&embed=true\";s:32:\"tag/([^/]+)/page/?([0-9]{1,})/?$\";s:43:\"index.php?tag=$matches[1]&paged=$matches[2]\";s:14:\"tag/([^/]+)/?$\";s:25:\"index.php?tag=$matches[1]\";s:45:\"type/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:40:\"type/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:21:\"type/([^/]+)/embed/?$\";s:44:\"index.php?post_format=$matches[1]&embed=true\";s:33:\"type/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?post_format=$matches[1]&paged=$matches[2]\";s:15:\"type/([^/]+)/?$\";s:33:\"index.php?post_format=$matches[1]\";s:12:\"robots\\.txt$\";s:18:\"index.php?robots=1\";s:13:\"favicon\\.ico$\";s:19:\"index.php?favicon=1\";s:48:\".*wp-(atom|rdf|rss|rss2|feed|commentsrss2)\\.php$\";s:18:\"index.php?feed=old\";s:20:\".*wp-app\\.php(/.*)?$\";s:19:\"index.php?error=403\";s:18:\".*wp-register.php$\";s:23:\"index.php?register=true\";s:32:\"feed/(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:27:\"(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:8:\"embed/?$\";s:21:\"index.php?&embed=true\";s:20:\"page/?([0-9]{1,})/?$\";s:28:\"index.php?&paged=$matches[1]\";s:41:\"comments/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:36:\"comments/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:17:\"comments/embed/?$\";s:21:\"index.php?&embed=true\";s:44:\"search/(.+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:39:\"search/(.+)/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:20:\"search/(.+)/embed/?$\";s:34:\"index.php?s=$matches[1]&embed=true\";s:32:\"search/(.+)/page/?([0-9]{1,})/?$\";s:41:\"index.php?s=$matches[1]&paged=$matches[2]\";s:14:\"search/(.+)/?$\";s:23:\"index.php?s=$matches[1]\";s:47:\"author/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:42:\"author/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:23:\"author/([^/]+)/embed/?$\";s:44:\"index.php?author_name=$matches[1]&embed=true\";s:35:\"author/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?author_name=$matches[1]&paged=$matches[2]\";s:17:\"author/([^/]+)/?$\";s:33:\"index.php?author_name=$matches[1]\";s:69:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:64:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:45:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/embed/?$\";s:74:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&embed=true\";s:57:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:81:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]\";s:39:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$\";s:63:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]\";s:56:\"([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:51:\"([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:32:\"([0-9]{4})/([0-9]{1,2})/embed/?$\";s:58:\"index.php?year=$matches[1]&monthnum=$matches[2]&embed=true\";s:44:\"([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:65:\"index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]\";s:26:\"([0-9]{4})/([0-9]{1,2})/?$\";s:47:\"index.php?year=$matches[1]&monthnum=$matches[2]\";s:43:\"([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:38:\"([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:19:\"([0-9]{4})/embed/?$\";s:37:\"index.php?year=$matches[1]&embed=true\";s:31:\"([0-9]{4})/page/?([0-9]{1,})/?$\";s:44:\"index.php?year=$matches[1]&paged=$matches[2]\";s:13:\"([0-9]{4})/?$\";s:26:\"index.php?year=$matches[1]\";s:47:\"[0-9]{4}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:57:\"[0-9]{4}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:77:\"[0-9]{4}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:72:\"[0-9]{4}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:72:\"[0-9]{4}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:53:\"[0-9]{4}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:40:\"([0-9]{4})/([0-9]{1,2})/([^/]+)/embed/?$\";s:75:\"index.php?year=$matches[1]&monthnum=$matches[2]&name=$matches[3]&embed=true\";s:44:\"([0-9]{4})/([0-9]{1,2})/([^/]+)/trackback/?$\";s:69:\"index.php?year=$matches[1]&monthnum=$matches[2]&name=$matches[3]&tb=1\";s:64:\"([0-9]{4})/([0-9]{1,2})/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:81:\"index.php?year=$matches[1]&monthnum=$matches[2]&name=$matches[3]&feed=$matches[4]\";s:59:\"([0-9]{4})/([0-9]{1,2})/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:81:\"index.php?year=$matches[1]&monthnum=$matches[2]&name=$matches[3]&feed=$matches[4]\";s:52:\"([0-9]{4})/([0-9]{1,2})/([^/]+)/page/?([0-9]{1,})/?$\";s:82:\"index.php?year=$matches[1]&monthnum=$matches[2]&name=$matches[3]&paged=$matches[4]\";s:59:\"([0-9]{4})/([0-9]{1,2})/([^/]+)/comment-page-([0-9]{1,})/?$\";s:82:\"index.php?year=$matches[1]&monthnum=$matches[2]&name=$matches[3]&cpage=$matches[4]\";s:48:\"([0-9]{4})/([0-9]{1,2})/([^/]+)(?:/([0-9]+))?/?$\";s:81:\"index.php?year=$matches[1]&monthnum=$matches[2]&name=$matches[3]&page=$matches[4]\";s:36:\"[0-9]{4}/[0-9]{1,2}/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:46:\"[0-9]{4}/[0-9]{1,2}/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:66:\"[0-9]{4}/[0-9]{1,2}/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:61:\"[0-9]{4}/[0-9]{1,2}/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:61:\"[0-9]{4}/[0-9]{1,2}/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:42:\"[0-9]{4}/[0-9]{1,2}/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:51:\"([0-9]{4})/([0-9]{1,2})/comment-page-([0-9]{1,})/?$\";s:65:\"index.php?year=$matches[1]&monthnum=$matches[2]&cpage=$matches[3]\";s:38:\"([0-9]{4})/comment-page-([0-9]{1,})/?$\";s:44:\"index.php?year=$matches[1]&cpage=$matches[2]\";s:27:\".?.+?/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:37:\".?.+?/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:57:\".?.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:33:\".?.+?/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:16:\"(.?.+?)/embed/?$\";s:41:\"index.php?pagename=$matches[1]&embed=true\";s:20:\"(.?.+?)/trackback/?$\";s:35:\"index.php?pagename=$matches[1]&tb=1\";s:40:\"(.?.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:35:\"(.?.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:28:\"(.?.+?)/page/?([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&paged=$matches[2]\";s:35:\"(.?.+?)/comment-page-([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&cpage=$matches[2]\";s:24:\"(.?.+?)(?:/([0-9]+))?/?$\";s:47:\"index.php?pagename=$matches[1]&page=$matches[2]\";}','yes'),(30,'hack_file','0','yes'),(31,'blog_charset','UTF-8','yes'),(32,'moderation_keys','','no'),(33,'active_plugins','a:1:{i:0;s:41:\"wp-ever-accounting/wp-ever-accounting.php\";}','yes'),(34,'category_base','','yes'),(35,'ping_sites','http://rpc.pingomatic.com/','yes'),(36,'comment_max_links','2','yes'),(37,'gmt_offset','0','yes'),(38,'default_email_category','1','yes'),(39,'recently_edited','','no'),(40,'template','twentytwentyfour','yes'),(41,'stylesheet','twentytwentyfour','yes'),(42,'comment_registration','0','yes'),(43,'html_type','text/html','yes'),(44,'use_trackback','0','yes'),(45,'default_role','subscriber','yes'),(46,'db_version','57155','yes'),(47,'uploads_use_yearmonth_folders','1','yes'),(48,'upload_path','','yes'),(49,'blog_public','1','yes'),(50,'default_link_category','2','yes'),(51,'show_on_front','posts','yes'),(52,'tag_base','','yes'),(53,'show_avatars','1','yes'),(54,'avatar_rating','G','yes'),(55,'upload_url_path','','yes'),(56,'thumbnail_size_w','150','yes'),(57,'thumbnail_size_h','150','yes'),(58,'thumbnail_crop','1','yes'),(59,'medium_size_w','300','yes'),(60,'medium_size_h','300','yes'),(61,'avatar_default','mystery','yes'),(62,'large_size_w','1024','yes'),(63,'large_size_h','1024','yes'),(64,'image_default_link_type','none','yes'),(65,'image_default_size','','yes'),(66,'image_default_align','','yes'),(67,'close_comments_for_old_posts','0','yes'),(68,'close_comments_days_old','14','yes'),(69,'thread_comments','1','yes'),(70,'thread_comments_depth','5','yes'),(71,'page_comments','0','yes'),(72,'comments_per_page','50','yes'),(73,'default_comments_page','newest','yes'),(74,'comment_order','asc','yes'),(75,'sticky_posts','a:0:{}','yes'),(76,'widget_categories','a:0:{}','yes'),(77,'widget_text','a:0:{}','yes'),(78,'widget_rss','a:0:{}','yes'),(79,'uninstall_plugins','a:0:{}','no'),(80,'timezone_string','','yes'),(81,'page_for_posts','0','yes'),(82,'page_on_front','0','yes'),(83,'default_post_format','0','yes'),(84,'link_manager_enabled','0','yes'),(85,'finished_splitting_shared_terms','1','yes'),(86,'site_icon','0','yes'),(87,'medium_large_size_w','768','yes'),(88,'medium_large_size_h','0','yes'),(89,'wp_page_for_privacy_policy','3','yes'),(90,'show_comments_cookies_opt_in','1','yes'),(91,'admin_email_lifespan','1729242387','yes'),(92,'disallowed_keys','','no'),(93,'comment_previously_approved','1','yes'),(94,'auto_plugin_theme_update_emails','a:0:{}','no'),(95,'auto_update_core_dev','enabled','yes'),(96,'auto_update_core_minor','enabled','yes'),(97,'auto_update_core_major','enabled','yes'),(98,'wp_force_deactivated_plugins','a:0:{}','yes'),(99,'wp_attachment_pages_enabled','0','yes'),(100,'initial_db_version','57155','yes'),(101,'test_user_roles','a:7:{s:13:\"administrator\";a:2:{s:4:\"name\";s:13:\"Administrator\";s:12:\"capabilities\";a:77:{s:13:\"switch_themes\";b:1;s:11:\"edit_themes\";b:1;s:16:\"activate_plugins\";b:1;s:12:\"edit_plugins\";b:1;s:10:\"edit_users\";b:1;s:10:\"edit_files\";b:1;s:14:\"manage_options\";b:1;s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:6:\"import\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:8:\"level_10\";b:1;s:7:\"level_9\";b:1;s:7:\"level_8\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:12:\"delete_users\";b:1;s:12:\"create_users\";b:1;s:17:\"unfiltered_upload\";b:1;s:14:\"edit_dashboard\";b:1;s:14:\"update_plugins\";b:1;s:14:\"delete_plugins\";b:1;s:15:\"install_plugins\";b:1;s:13:\"update_themes\";b:1;s:14:\"install_themes\";b:1;s:11:\"update_core\";b:1;s:10:\"list_users\";b:1;s:12:\"remove_users\";b:1;s:13:\"promote_users\";b:1;s:18:\"edit_theme_options\";b:1;s:13:\"delete_themes\";b:1;s:6:\"export\";b:1;s:18:\"manage_eaccounting\";b:1;s:16:\"ea_manage_report\";b:1;s:17:\"ea_manage_options\";b:1;s:9:\"ea_import\";b:1;s:9:\"ea_export\";b:1;s:18:\"ea_manage_customer\";b:1;s:16:\"ea_manage_vendor\";b:1;s:17:\"ea_manage_account\";b:1;s:17:\"ea_manage_payment\";b:1;s:17:\"ea_manage_revenue\";b:1;s:18:\"ea_manage_transfer\";b:1;s:18:\"ea_manage_category\";b:1;s:18:\"ea_manage_currency\";b:1;s:14:\"ea_manage_item\";b:1;s:17:\"ea_manage_invoice\";b:1;s:14:\"ea_manage_bill\";b:1;}}s:6:\"editor\";a:2:{s:4:\"name\";s:6:\"Editor\";s:12:\"capabilities\";a:34:{s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;}}s:6:\"author\";a:2:{s:4:\"name\";s:6:\"Author\";s:12:\"capabilities\";a:10:{s:12:\"upload_files\";b:1;s:10:\"edit_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;s:22:\"delete_published_posts\";b:1;}}s:11:\"contributor\";a:2:{s:4:\"name\";s:11:\"Contributor\";s:12:\"capabilities\";a:5:{s:10:\"edit_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;}}s:10:\"subscriber\";a:2:{s:4:\"name\";s:10:\"Subscriber\";s:12:\"capabilities\";a:2:{s:4:\"read\";b:1;s:7:\"level_0\";b:1;}}s:13:\"ea_accountant\";a:2:{s:4:\"name\";s:10:\"Accountant\";s:12:\"capabilities\";a:13:{s:18:\"manage_eaccounting\";b:1;s:18:\"ea_manage_customer\";b:1;s:16:\"ea_manage_vendor\";b:1;s:17:\"ea_manage_account\";b:1;s:17:\"ea_manage_payment\";b:1;s:17:\"ea_manage_revenue\";b:1;s:18:\"ea_manage_transfer\";b:1;s:18:\"ea_manage_category\";b:1;s:18:\"ea_manage_currency\";b:1;s:14:\"ea_manage_item\";b:1;s:17:\"ea_manage_invoice\";b:1;s:14:\"ea_manage_bill\";b:1;s:4:\"read\";b:1;}}s:10:\"ea_manager\";a:2:{s:4:\"name\";s:18:\"Accounting Manager\";s:12:\"capabilities\";a:17:{s:18:\"manage_eaccounting\";b:1;s:16:\"ea_manage_report\";b:1;s:17:\"ea_manage_options\";b:1;s:9:\"ea_import\";b:1;s:9:\"ea_export\";b:1;s:18:\"ea_manage_customer\";b:1;s:16:\"ea_manage_vendor\";b:1;s:17:\"ea_manage_account\";b:1;s:17:\"ea_manage_payment\";b:1;s:17:\"ea_manage_revenue\";b:1;s:18:\"ea_manage_transfer\";b:1;s:18:\"ea_manage_category\";b:1;s:18:\"ea_manage_currency\";b:1;s:14:\"ea_manage_item\";b:1;s:17:\"ea_manage_invoice\";b:1;s:14:\"ea_manage_bill\";b:1;s:4:\"read\";b:1;}}}','yes'),(102,'fresh_site','1','yes'),(103,'user_count','1','no'),(104,'widget_block','a:6:{i:2;a:1:{s:7:\"content\";s:19:\"<!-- wp:search /-->\";}i:3;a:1:{s:7:\"content\";s:154:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>Recent Posts</h2><!-- /wp:heading --><!-- wp:latest-posts /--></div><!-- /wp:group -->\";}i:4;a:1:{s:7:\"content\";s:227:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>Recent Comments</h2><!-- /wp:heading --><!-- wp:latest-comments {\"displayAvatar\":false,\"displayDate\":false,\"displayExcerpt\":false} /--></div><!-- /wp:group -->\";}i:5;a:1:{s:7:\"content\";s:146:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>Archives</h2><!-- /wp:heading --><!-- wp:archives /--></div><!-- /wp:group -->\";}i:6;a:1:{s:7:\"content\";s:150:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>Categories</h2><!-- /wp:heading --><!-- wp:categories /--></div><!-- /wp:group -->\";}s:12:\"_multiwidget\";i:1;}','yes'),(105,'sidebars_widgets','a:4:{s:19:\"wp_inactive_widgets\";a:0:{}s:9:\"sidebar-1\";a:3:{i:0;s:7:\"block-2\";i:1;s:7:\"block-3\";i:2;s:7:\"block-4\";}s:9:\"sidebar-2\";a:2:{i:0;s:7:\"block-5\";i:1;s:7:\"block-6\";}s:13:\"array_version\";i:3;}','yes'),(106,'cron','a:6:{i:1713690390;a:5:{s:32:\"recovery_mode_clean_expired_keys\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}s:34:\"wp_privacy_delete_old_export_files\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"hourly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:3600;}}s:16:\"wp_version_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}s:17:\"wp_update_plugins\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}s:16:\"wp_update_themes\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1713690407;a:1:{s:34:\"eaccounting_daily_scheduled_events\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1713701197;a:1:{s:35:\"eaccounting_weekly_scheduled_events\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"weekly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:604800;}}}i:1713711997;a:1:{s:39:\"eaccounting_twicedaily_scheduled_events\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1713776790;a:1:{s:30:\"wp_site_health_scheduled_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"weekly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:604800;}}}s:7:\"version\";i:2;}','yes'),(107,'widget_pages','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(108,'widget_calendar','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(109,'widget_archives','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(110,'widget_media_audio','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(111,'widget_media_image','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(112,'widget_media_gallery','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(113,'widget_media_video','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(114,'widget_meta','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(115,'widget_search','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(116,'widget_recent-posts','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(117,'widget_recent-comments','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(118,'widget_tag_cloud','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(119,'widget_nav_menu','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(120,'widget_custom_html','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(121,'_transient_wp_core_block_css_files','a:2:{s:7:\"version\";s:5:\"6.5.2\";s:5:\"files\";a:500:{i:0;s:23:\"archives/editor-rtl.css\";i:1;s:27:\"archives/editor-rtl.min.css\";i:2;s:19:\"archives/editor.css\";i:3;s:23:\"archives/editor.min.css\";i:4;s:22:\"archives/style-rtl.css\";i:5;s:26:\"archives/style-rtl.min.css\";i:6;s:18:\"archives/style.css\";i:7;s:22:\"archives/style.min.css\";i:8;s:20:\"audio/editor-rtl.css\";i:9;s:24:\"audio/editor-rtl.min.css\";i:10;s:16:\"audio/editor.css\";i:11;s:20:\"audio/editor.min.css\";i:12;s:19:\"audio/style-rtl.css\";i:13;s:23:\"audio/style-rtl.min.css\";i:14;s:15:\"audio/style.css\";i:15;s:19:\"audio/style.min.css\";i:16;s:19:\"audio/theme-rtl.css\";i:17;s:23:\"audio/theme-rtl.min.css\";i:18;s:15:\"audio/theme.css\";i:19;s:19:\"audio/theme.min.css\";i:20;s:21:\"avatar/editor-rtl.css\";i:21;s:25:\"avatar/editor-rtl.min.css\";i:22;s:17:\"avatar/editor.css\";i:23;s:21:\"avatar/editor.min.css\";i:24;s:20:\"avatar/style-rtl.css\";i:25;s:24:\"avatar/style-rtl.min.css\";i:26;s:16:\"avatar/style.css\";i:27;s:20:\"avatar/style.min.css\";i:28;s:20:\"block/editor-rtl.css\";i:29;s:24:\"block/editor-rtl.min.css\";i:30;s:16:\"block/editor.css\";i:31;s:20:\"block/editor.min.css\";i:32;s:21:\"button/editor-rtl.css\";i:33;s:25:\"button/editor-rtl.min.css\";i:34;s:17:\"button/editor.css\";i:35;s:21:\"button/editor.min.css\";i:36;s:20:\"button/style-rtl.css\";i:37;s:24:\"button/style-rtl.min.css\";i:38;s:16:\"button/style.css\";i:39;s:20:\"button/style.min.css\";i:40;s:22:\"buttons/editor-rtl.css\";i:41;s:26:\"buttons/editor-rtl.min.css\";i:42;s:18:\"buttons/editor.css\";i:43;s:22:\"buttons/editor.min.css\";i:44;s:21:\"buttons/style-rtl.css\";i:45;s:25:\"buttons/style-rtl.min.css\";i:46;s:17:\"buttons/style.css\";i:47;s:21:\"buttons/style.min.css\";i:48;s:22:\"calendar/style-rtl.css\";i:49;s:26:\"calendar/style-rtl.min.css\";i:50;s:18:\"calendar/style.css\";i:51;s:22:\"calendar/style.min.css\";i:52;s:25:\"categories/editor-rtl.css\";i:53;s:29:\"categories/editor-rtl.min.css\";i:54;s:21:\"categories/editor.css\";i:55;s:25:\"categories/editor.min.css\";i:56;s:24:\"categories/style-rtl.css\";i:57;s:28:\"categories/style-rtl.min.css\";i:58;s:20:\"categories/style.css\";i:59;s:24:\"categories/style.min.css\";i:60;s:19:\"code/editor-rtl.css\";i:61;s:23:\"code/editor-rtl.min.css\";i:62;s:15:\"code/editor.css\";i:63;s:19:\"code/editor.min.css\";i:64;s:18:\"code/style-rtl.css\";i:65;s:22:\"code/style-rtl.min.css\";i:66;s:14:\"code/style.css\";i:67;s:18:\"code/style.min.css\";i:68;s:18:\"code/theme-rtl.css\";i:69;s:22:\"code/theme-rtl.min.css\";i:70;s:14:\"code/theme.css\";i:71;s:18:\"code/theme.min.css\";i:72;s:22:\"columns/editor-rtl.css\";i:73;s:26:\"columns/editor-rtl.min.css\";i:74;s:18:\"columns/editor.css\";i:75;s:22:\"columns/editor.min.css\";i:76;s:21:\"columns/style-rtl.css\";i:77;s:25:\"columns/style-rtl.min.css\";i:78;s:17:\"columns/style.css\";i:79;s:21:\"columns/style.min.css\";i:80;s:29:\"comment-content/style-rtl.css\";i:81;s:33:\"comment-content/style-rtl.min.css\";i:82;s:25:\"comment-content/style.css\";i:83;s:29:\"comment-content/style.min.css\";i:84;s:30:\"comment-template/style-rtl.css\";i:85;s:34:\"comment-template/style-rtl.min.css\";i:86;s:26:\"comment-template/style.css\";i:87;s:30:\"comment-template/style.min.css\";i:88;s:42:\"comments-pagination-numbers/editor-rtl.css\";i:89;s:46:\"comments-pagination-numbers/editor-rtl.min.css\";i:90;s:38:\"comments-pagination-numbers/editor.css\";i:91;s:42:\"comments-pagination-numbers/editor.min.css\";i:92;s:34:\"comments-pagination/editor-rtl.css\";i:93;s:38:\"comments-pagination/editor-rtl.min.css\";i:94;s:30:\"comments-pagination/editor.css\";i:95;s:34:\"comments-pagination/editor.min.css\";i:96;s:33:\"comments-pagination/style-rtl.css\";i:97;s:37:\"comments-pagination/style-rtl.min.css\";i:98;s:29:\"comments-pagination/style.css\";i:99;s:33:\"comments-pagination/style.min.css\";i:100;s:29:\"comments-title/editor-rtl.css\";i:101;s:33:\"comments-title/editor-rtl.min.css\";i:102;s:25:\"comments-title/editor.css\";i:103;s:29:\"comments-title/editor.min.css\";i:104;s:23:\"comments/editor-rtl.css\";i:105;s:27:\"comments/editor-rtl.min.css\";i:106;s:19:\"comments/editor.css\";i:107;s:23:\"comments/editor.min.css\";i:108;s:22:\"comments/style-rtl.css\";i:109;s:26:\"comments/style-rtl.min.css\";i:110;s:18:\"comments/style.css\";i:111;s:22:\"comments/style.min.css\";i:112;s:20:\"cover/editor-rtl.css\";i:113;s:24:\"cover/editor-rtl.min.css\";i:114;s:16:\"cover/editor.css\";i:115;s:20:\"cover/editor.min.css\";i:116;s:19:\"cover/style-rtl.css\";i:117;s:23:\"cover/style-rtl.min.css\";i:118;s:15:\"cover/style.css\";i:119;s:19:\"cover/style.min.css\";i:120;s:22:\"details/editor-rtl.css\";i:121;s:26:\"details/editor-rtl.min.css\";i:122;s:18:\"details/editor.css\";i:123;s:22:\"details/editor.min.css\";i:124;s:21:\"details/style-rtl.css\";i:125;s:25:\"details/style-rtl.min.css\";i:126;s:17:\"details/style.css\";i:127;s:21:\"details/style.min.css\";i:128;s:20:\"embed/editor-rtl.css\";i:129;s:24:\"embed/editor-rtl.min.css\";i:130;s:16:\"embed/editor.css\";i:131;s:20:\"embed/editor.min.css\";i:132;s:19:\"embed/style-rtl.css\";i:133;s:23:\"embed/style-rtl.min.css\";i:134;s:15:\"embed/style.css\";i:135;s:19:\"embed/style.min.css\";i:136;s:19:\"embed/theme-rtl.css\";i:137;s:23:\"embed/theme-rtl.min.css\";i:138;s:15:\"embed/theme.css\";i:139;s:19:\"embed/theme.min.css\";i:140;s:19:\"file/editor-rtl.css\";i:141;s:23:\"file/editor-rtl.min.css\";i:142;s:15:\"file/editor.css\";i:143;s:19:\"file/editor.min.css\";i:144;s:18:\"file/style-rtl.css\";i:145;s:22:\"file/style-rtl.min.css\";i:146;s:14:\"file/style.css\";i:147;s:18:\"file/style.min.css\";i:148;s:23:\"footnotes/style-rtl.css\";i:149;s:27:\"footnotes/style-rtl.min.css\";i:150;s:19:\"footnotes/style.css\";i:151;s:23:\"footnotes/style.min.css\";i:152;s:23:\"freeform/editor-rtl.css\";i:153;s:27:\"freeform/editor-rtl.min.css\";i:154;s:19:\"freeform/editor.css\";i:155;s:23:\"freeform/editor.min.css\";i:156;s:22:\"gallery/editor-rtl.css\";i:157;s:26:\"gallery/editor-rtl.min.css\";i:158;s:18:\"gallery/editor.css\";i:159;s:22:\"gallery/editor.min.css\";i:160;s:21:\"gallery/style-rtl.css\";i:161;s:25:\"gallery/style-rtl.min.css\";i:162;s:17:\"gallery/style.css\";i:163;s:21:\"gallery/style.min.css\";i:164;s:21:\"gallery/theme-rtl.css\";i:165;s:25:\"gallery/theme-rtl.min.css\";i:166;s:17:\"gallery/theme.css\";i:167;s:21:\"gallery/theme.min.css\";i:168;s:20:\"group/editor-rtl.css\";i:169;s:24:\"group/editor-rtl.min.css\";i:170;s:16:\"group/editor.css\";i:171;s:20:\"group/editor.min.css\";i:172;s:19:\"group/style-rtl.css\";i:173;s:23:\"group/style-rtl.min.css\";i:174;s:15:\"group/style.css\";i:175;s:19:\"group/style.min.css\";i:176;s:19:\"group/theme-rtl.css\";i:177;s:23:\"group/theme-rtl.min.css\";i:178;s:15:\"group/theme.css\";i:179;s:19:\"group/theme.min.css\";i:180;s:21:\"heading/style-rtl.css\";i:181;s:25:\"heading/style-rtl.min.css\";i:182;s:17:\"heading/style.css\";i:183;s:21:\"heading/style.min.css\";i:184;s:19:\"html/editor-rtl.css\";i:185;s:23:\"html/editor-rtl.min.css\";i:186;s:15:\"html/editor.css\";i:187;s:19:\"html/editor.min.css\";i:188;s:20:\"image/editor-rtl.css\";i:189;s:24:\"image/editor-rtl.min.css\";i:190;s:16:\"image/editor.css\";i:191;s:20:\"image/editor.min.css\";i:192;s:19:\"image/style-rtl.css\";i:193;s:23:\"image/style-rtl.min.css\";i:194;s:15:\"image/style.css\";i:195;s:19:\"image/style.min.css\";i:196;s:19:\"image/theme-rtl.css\";i:197;s:23:\"image/theme-rtl.min.css\";i:198;s:15:\"image/theme.css\";i:199;s:19:\"image/theme.min.css\";i:200;s:29:\"latest-comments/style-rtl.css\";i:201;s:33:\"latest-comments/style-rtl.min.css\";i:202;s:25:\"latest-comments/style.css\";i:203;s:29:\"latest-comments/style.min.css\";i:204;s:27:\"latest-posts/editor-rtl.css\";i:205;s:31:\"latest-posts/editor-rtl.min.css\";i:206;s:23:\"latest-posts/editor.css\";i:207;s:27:\"latest-posts/editor.min.css\";i:208;s:26:\"latest-posts/style-rtl.css\";i:209;s:30:\"latest-posts/style-rtl.min.css\";i:210;s:22:\"latest-posts/style.css\";i:211;s:26:\"latest-posts/style.min.css\";i:212;s:18:\"list/style-rtl.css\";i:213;s:22:\"list/style-rtl.min.css\";i:214;s:14:\"list/style.css\";i:215;s:18:\"list/style.min.css\";i:216;s:25:\"media-text/editor-rtl.css\";i:217;s:29:\"media-text/editor-rtl.min.css\";i:218;s:21:\"media-text/editor.css\";i:219;s:25:\"media-text/editor.min.css\";i:220;s:24:\"media-text/style-rtl.css\";i:221;s:28:\"media-text/style-rtl.min.css\";i:222;s:20:\"media-text/style.css\";i:223;s:24:\"media-text/style.min.css\";i:224;s:19:\"more/editor-rtl.css\";i:225;s:23:\"more/editor-rtl.min.css\";i:226;s:15:\"more/editor.css\";i:227;s:19:\"more/editor.min.css\";i:228;s:30:\"navigation-link/editor-rtl.css\";i:229;s:34:\"navigation-link/editor-rtl.min.css\";i:230;s:26:\"navigation-link/editor.css\";i:231;s:30:\"navigation-link/editor.min.css\";i:232;s:29:\"navigation-link/style-rtl.css\";i:233;s:33:\"navigation-link/style-rtl.min.css\";i:234;s:25:\"navigation-link/style.css\";i:235;s:29:\"navigation-link/style.min.css\";i:236;s:33:\"navigation-submenu/editor-rtl.css\";i:237;s:37:\"navigation-submenu/editor-rtl.min.css\";i:238;s:29:\"navigation-submenu/editor.css\";i:239;s:33:\"navigation-submenu/editor.min.css\";i:240;s:25:\"navigation/editor-rtl.css\";i:241;s:29:\"navigation/editor-rtl.min.css\";i:242;s:21:\"navigation/editor.css\";i:243;s:25:\"navigation/editor.min.css\";i:244;s:24:\"navigation/style-rtl.css\";i:245;s:28:\"navigation/style-rtl.min.css\";i:246;s:20:\"navigation/style.css\";i:247;s:24:\"navigation/style.min.css\";i:248;s:23:\"nextpage/editor-rtl.css\";i:249;s:27:\"nextpage/editor-rtl.min.css\";i:250;s:19:\"nextpage/editor.css\";i:251;s:23:\"nextpage/editor.min.css\";i:252;s:24:\"page-list/editor-rtl.css\";i:253;s:28:\"page-list/editor-rtl.min.css\";i:254;s:20:\"page-list/editor.css\";i:255;s:24:\"page-list/editor.min.css\";i:256;s:23:\"page-list/style-rtl.css\";i:257;s:27:\"page-list/style-rtl.min.css\";i:258;s:19:\"page-list/style.css\";i:259;s:23:\"page-list/style.min.css\";i:260;s:24:\"paragraph/editor-rtl.css\";i:261;s:28:\"paragraph/editor-rtl.min.css\";i:262;s:20:\"paragraph/editor.css\";i:263;s:24:\"paragraph/editor.min.css\";i:264;s:23:\"paragraph/style-rtl.css\";i:265;s:27:\"paragraph/style-rtl.min.css\";i:266;s:19:\"paragraph/style.css\";i:267;s:23:\"paragraph/style.min.css\";i:268;s:25:\"post-author/style-rtl.css\";i:269;s:29:\"post-author/style-rtl.min.css\";i:270;s:21:\"post-author/style.css\";i:271;s:25:\"post-author/style.min.css\";i:272;s:33:\"post-comments-form/editor-rtl.css\";i:273;s:37:\"post-comments-form/editor-rtl.min.css\";i:274;s:29:\"post-comments-form/editor.css\";i:275;s:33:\"post-comments-form/editor.min.css\";i:276;s:32:\"post-comments-form/style-rtl.css\";i:277;s:36:\"post-comments-form/style-rtl.min.css\";i:278;s:28:\"post-comments-form/style.css\";i:279;s:32:\"post-comments-form/style.min.css\";i:280;s:27:\"post-content/editor-rtl.css\";i:281;s:31:\"post-content/editor-rtl.min.css\";i:282;s:23:\"post-content/editor.css\";i:283;s:27:\"post-content/editor.min.css\";i:284;s:23:\"post-date/style-rtl.css\";i:285;s:27:\"post-date/style-rtl.min.css\";i:286;s:19:\"post-date/style.css\";i:287;s:23:\"post-date/style.min.css\";i:288;s:27:\"post-excerpt/editor-rtl.css\";i:289;s:31:\"post-excerpt/editor-rtl.min.css\";i:290;s:23:\"post-excerpt/editor.css\";i:291;s:27:\"post-excerpt/editor.min.css\";i:292;s:26:\"post-excerpt/style-rtl.css\";i:293;s:30:\"post-excerpt/style-rtl.min.css\";i:294;s:22:\"post-excerpt/style.css\";i:295;s:26:\"post-excerpt/style.min.css\";i:296;s:34:\"post-featured-image/editor-rtl.css\";i:297;s:38:\"post-featured-image/editor-rtl.min.css\";i:298;s:30:\"post-featured-image/editor.css\";i:299;s:34:\"post-featured-image/editor.min.css\";i:300;s:33:\"post-featured-image/style-rtl.css\";i:301;s:37:\"post-featured-image/style-rtl.min.css\";i:302;s:29:\"post-featured-image/style.css\";i:303;s:33:\"post-featured-image/style.min.css\";i:304;s:34:\"post-navigation-link/style-rtl.css\";i:305;s:38:\"post-navigation-link/style-rtl.min.css\";i:306;s:30:\"post-navigation-link/style.css\";i:307;s:34:\"post-navigation-link/style.min.css\";i:308;s:28:\"post-template/editor-rtl.css\";i:309;s:32:\"post-template/editor-rtl.min.css\";i:310;s:24:\"post-template/editor.css\";i:311;s:28:\"post-template/editor.min.css\";i:312;s:27:\"post-template/style-rtl.css\";i:313;s:31:\"post-template/style-rtl.min.css\";i:314;s:23:\"post-template/style.css\";i:315;s:27:\"post-template/style.min.css\";i:316;s:24:\"post-terms/style-rtl.css\";i:317;s:28:\"post-terms/style-rtl.min.css\";i:318;s:20:\"post-terms/style.css\";i:319;s:24:\"post-terms/style.min.css\";i:320;s:24:\"post-title/style-rtl.css\";i:321;s:28:\"post-title/style-rtl.min.css\";i:322;s:20:\"post-title/style.css\";i:323;s:24:\"post-title/style.min.css\";i:324;s:26:\"preformatted/style-rtl.css\";i:325;s:30:\"preformatted/style-rtl.min.css\";i:326;s:22:\"preformatted/style.css\";i:327;s:26:\"preformatted/style.min.css\";i:328;s:24:\"pullquote/editor-rtl.css\";i:329;s:28:\"pullquote/editor-rtl.min.css\";i:330;s:20:\"pullquote/editor.css\";i:331;s:24:\"pullquote/editor.min.css\";i:332;s:23:\"pullquote/style-rtl.css\";i:333;s:27:\"pullquote/style-rtl.min.css\";i:334;s:19:\"pullquote/style.css\";i:335;s:23:\"pullquote/style.min.css\";i:336;s:23:\"pullquote/theme-rtl.css\";i:337;s:27:\"pullquote/theme-rtl.min.css\";i:338;s:19:\"pullquote/theme.css\";i:339;s:23:\"pullquote/theme.min.css\";i:340;s:39:\"query-pagination-numbers/editor-rtl.css\";i:341;s:43:\"query-pagination-numbers/editor-rtl.min.css\";i:342;s:35:\"query-pagination-numbers/editor.css\";i:343;s:39:\"query-pagination-numbers/editor.min.css\";i:344;s:31:\"query-pagination/editor-rtl.css\";i:345;s:35:\"query-pagination/editor-rtl.min.css\";i:346;s:27:\"query-pagination/editor.css\";i:347;s:31:\"query-pagination/editor.min.css\";i:348;s:30:\"query-pagination/style-rtl.css\";i:349;s:34:\"query-pagination/style-rtl.min.css\";i:350;s:26:\"query-pagination/style.css\";i:351;s:30:\"query-pagination/style.min.css\";i:352;s:25:\"query-title/style-rtl.css\";i:353;s:29:\"query-title/style-rtl.min.css\";i:354;s:21:\"query-title/style.css\";i:355;s:25:\"query-title/style.min.css\";i:356;s:20:\"query/editor-rtl.css\";i:357;s:24:\"query/editor-rtl.min.css\";i:358;s:16:\"query/editor.css\";i:359;s:20:\"query/editor.min.css\";i:360;s:19:\"quote/style-rtl.css\";i:361;s:23:\"quote/style-rtl.min.css\";i:362;s:15:\"quote/style.css\";i:363;s:19:\"quote/style.min.css\";i:364;s:19:\"quote/theme-rtl.css\";i:365;s:23:\"quote/theme-rtl.min.css\";i:366;s:15:\"quote/theme.css\";i:367;s:19:\"quote/theme.min.css\";i:368;s:23:\"read-more/style-rtl.css\";i:369;s:27:\"read-more/style-rtl.min.css\";i:370;s:19:\"read-more/style.css\";i:371;s:23:\"read-more/style.min.css\";i:372;s:18:\"rss/editor-rtl.css\";i:373;s:22:\"rss/editor-rtl.min.css\";i:374;s:14:\"rss/editor.css\";i:375;s:18:\"rss/editor.min.css\";i:376;s:17:\"rss/style-rtl.css\";i:377;s:21:\"rss/style-rtl.min.css\";i:378;s:13:\"rss/style.css\";i:379;s:17:\"rss/style.min.css\";i:380;s:21:\"search/editor-rtl.css\";i:381;s:25:\"search/editor-rtl.min.css\";i:382;s:17:\"search/editor.css\";i:383;s:21:\"search/editor.min.css\";i:384;s:20:\"search/style-rtl.css\";i:385;s:24:\"search/style-rtl.min.css\";i:386;s:16:\"search/style.css\";i:387;s:20:\"search/style.min.css\";i:388;s:20:\"search/theme-rtl.css\";i:389;s:24:\"search/theme-rtl.min.css\";i:390;s:16:\"search/theme.css\";i:391;s:20:\"search/theme.min.css\";i:392;s:24:\"separator/editor-rtl.css\";i:393;s:28:\"separator/editor-rtl.min.css\";i:394;s:20:\"separator/editor.css\";i:395;s:24:\"separator/editor.min.css\";i:396;s:23:\"separator/style-rtl.css\";i:397;s:27:\"separator/style-rtl.min.css\";i:398;s:19:\"separator/style.css\";i:399;s:23:\"separator/style.min.css\";i:400;s:23:\"separator/theme-rtl.css\";i:401;s:27:\"separator/theme-rtl.min.css\";i:402;s:19:\"separator/theme.css\";i:403;s:23:\"separator/theme.min.css\";i:404;s:24:\"shortcode/editor-rtl.css\";i:405;s:28:\"shortcode/editor-rtl.min.css\";i:406;s:20:\"shortcode/editor.css\";i:407;s:24:\"shortcode/editor.min.css\";i:408;s:24:\"site-logo/editor-rtl.css\";i:409;s:28:\"site-logo/editor-rtl.min.css\";i:410;s:20:\"site-logo/editor.css\";i:411;s:24:\"site-logo/editor.min.css\";i:412;s:23:\"site-logo/style-rtl.css\";i:413;s:27:\"site-logo/style-rtl.min.css\";i:414;s:19:\"site-logo/style.css\";i:415;s:23:\"site-logo/style.min.css\";i:416;s:27:\"site-tagline/editor-rtl.css\";i:417;s:31:\"site-tagline/editor-rtl.min.css\";i:418;s:23:\"site-tagline/editor.css\";i:419;s:27:\"site-tagline/editor.min.css\";i:420;s:25:\"site-title/editor-rtl.css\";i:421;s:29:\"site-title/editor-rtl.min.css\";i:422;s:21:\"site-title/editor.css\";i:423;s:25:\"site-title/editor.min.css\";i:424;s:24:\"site-title/style-rtl.css\";i:425;s:28:\"site-title/style-rtl.min.css\";i:426;s:20:\"site-title/style.css\";i:427;s:24:\"site-title/style.min.css\";i:428;s:26:\"social-link/editor-rtl.css\";i:429;s:30:\"social-link/editor-rtl.min.css\";i:430;s:22:\"social-link/editor.css\";i:431;s:26:\"social-link/editor.min.css\";i:432;s:27:\"social-links/editor-rtl.css\";i:433;s:31:\"social-links/editor-rtl.min.css\";i:434;s:23:\"social-links/editor.css\";i:435;s:27:\"social-links/editor.min.css\";i:436;s:26:\"social-links/style-rtl.css\";i:437;s:30:\"social-links/style-rtl.min.css\";i:438;s:22:\"social-links/style.css\";i:439;s:26:\"social-links/style.min.css\";i:440;s:21:\"spacer/editor-rtl.css\";i:441;s:25:\"spacer/editor-rtl.min.css\";i:442;s:17:\"spacer/editor.css\";i:443;s:21:\"spacer/editor.min.css\";i:444;s:20:\"spacer/style-rtl.css\";i:445;s:24:\"spacer/style-rtl.min.css\";i:446;s:16:\"spacer/style.css\";i:447;s:20:\"spacer/style.min.css\";i:448;s:20:\"table/editor-rtl.css\";i:449;s:24:\"table/editor-rtl.min.css\";i:450;s:16:\"table/editor.css\";i:451;s:20:\"table/editor.min.css\";i:452;s:19:\"table/style-rtl.css\";i:453;s:23:\"table/style-rtl.min.css\";i:454;s:15:\"table/style.css\";i:455;s:19:\"table/style.min.css\";i:456;s:19:\"table/theme-rtl.css\";i:457;s:23:\"table/theme-rtl.min.css\";i:458;s:15:\"table/theme.css\";i:459;s:19:\"table/theme.min.css\";i:460;s:23:\"tag-cloud/style-rtl.css\";i:461;s:27:\"tag-cloud/style-rtl.min.css\";i:462;s:19:\"tag-cloud/style.css\";i:463;s:23:\"tag-cloud/style.min.css\";i:464;s:28:\"template-part/editor-rtl.css\";i:465;s:32:\"template-part/editor-rtl.min.css\";i:466;s:24:\"template-part/editor.css\";i:467;s:28:\"template-part/editor.min.css\";i:468;s:27:\"template-part/theme-rtl.css\";i:469;s:31:\"template-part/theme-rtl.min.css\";i:470;s:23:\"template-part/theme.css\";i:471;s:27:\"template-part/theme.min.css\";i:472;s:30:\"term-description/style-rtl.css\";i:473;s:34:\"term-description/style-rtl.min.css\";i:474;s:26:\"term-description/style.css\";i:475;s:30:\"term-description/style.min.css\";i:476;s:27:\"text-columns/editor-rtl.css\";i:477;s:31:\"text-columns/editor-rtl.min.css\";i:478;s:23:\"text-columns/editor.css\";i:479;s:27:\"text-columns/editor.min.css\";i:480;s:26:\"text-columns/style-rtl.css\";i:481;s:30:\"text-columns/style-rtl.min.css\";i:482;s:22:\"text-columns/style.css\";i:483;s:26:\"text-columns/style.min.css\";i:484;s:19:\"verse/style-rtl.css\";i:485;s:23:\"verse/style-rtl.min.css\";i:486;s:15:\"verse/style.css\";i:487;s:19:\"verse/style.min.css\";i:488;s:20:\"video/editor-rtl.css\";i:489;s:24:\"video/editor-rtl.min.css\";i:490;s:16:\"video/editor.css\";i:491;s:20:\"video/editor.min.css\";i:492;s:19:\"video/style-rtl.css\";i:493;s:23:\"video/style-rtl.min.css\";i:494;s:15:\"video/style.css\";i:495;s:19:\"video/style.min.css\";i:496;s:19:\"video/theme-rtl.css\";i:497;s:23:\"video/theme-rtl.min.css\";i:498;s:15:\"video/theme.css\";i:499;s:19:\"video/theme.min.css\";}}','yes'),(122,'_transient_doing_cron','1713690390.9491031169891357421875','yes'),(123,'_site_transient_update_plugins','O:8:\"stdClass\":4:{s:12:\"last_checked\";i:1713690395;s:8:\"response\";a:0:{}s:12:\"translations\";a:0:{}s:9:\"no_update\";a:3:{s:19:\"akismet/akismet.php\";O:8:\"stdClass\":10:{s:2:\"id\";s:21:\"w.org/plugins/akismet\";s:4:\"slug\";s:7:\"akismet\";s:6:\"plugin\";s:19:\"akismet/akismet.php\";s:11:\"new_version\";s:5:\"5.3.2\";s:3:\"url\";s:38:\"https://wordpress.org/plugins/akismet/\";s:7:\"package\";s:56:\"https://downloads.wordpress.org/plugin/akismet.5.3.2.zip\";s:5:\"icons\";a:2:{s:2:\"2x\";s:60:\"https://ps.w.org/akismet/assets/icon-256x256.png?rev=2818463\";s:2:\"1x\";s:60:\"https://ps.w.org/akismet/assets/icon-128x128.png?rev=2818463\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:63:\"https://ps.w.org/akismet/assets/banner-1544x500.png?rev=2900731\";s:2:\"1x\";s:62:\"https://ps.w.org/akismet/assets/banner-772x250.png?rev=2900731\";}s:11:\"banners_rtl\";a:0:{}s:8:\"requires\";s:3:\"5.8\";}s:41:\"wp-ever-accounting/wp-ever-accounting.php\";O:8:\"stdClass\":10:{s:2:\"id\";s:32:\"w.org/plugins/wp-ever-accounting\";s:4:\"slug\";s:18:\"wp-ever-accounting\";s:6:\"plugin\";s:41:\"wp-ever-accounting/wp-ever-accounting.php\";s:11:\"new_version\";s:5:\"1.1.9\";s:3:\"url\";s:49:\"https://wordpress.org/plugins/wp-ever-accounting/\";s:7:\"package\";s:67:\"https://downloads.wordpress.org/plugin/wp-ever-accounting.1.1.9.zip\";s:5:\"icons\";a:2:{s:2:\"1x\";s:63:\"https://ps.w.org/wp-ever-accounting/assets/icon.svg?rev=2401994\";s:3:\"svg\";s:63:\"https://ps.w.org/wp-ever-accounting/assets/icon.svg?rev=2401994\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:74:\"https://ps.w.org/wp-ever-accounting/assets/banner-1544x500.png?rev=2954505\";s:2:\"1x\";s:73:\"https://ps.w.org/wp-ever-accounting/assets/banner-772x250.png?rev=2954505\";}s:11:\"banners_rtl\";a:0:{}s:8:\"requires\";s:5:\"4.7.0\";}s:9:\"hello.php\";O:8:\"stdClass\":10:{s:2:\"id\";s:25:\"w.org/plugins/hello-dolly\";s:4:\"slug\";s:11:\"hello-dolly\";s:6:\"plugin\";s:9:\"hello.php\";s:11:\"new_version\";s:5:\"1.7.2\";s:3:\"url\";s:42:\"https://wordpress.org/plugins/hello-dolly/\";s:7:\"package\";s:60:\"https://downloads.wordpress.org/plugin/hello-dolly.1.7.3.zip\";s:5:\"icons\";a:2:{s:2:\"2x\";s:64:\"https://ps.w.org/hello-dolly/assets/icon-256x256.jpg?rev=2052855\";s:2:\"1x\";s:64:\"https://ps.w.org/hello-dolly/assets/icon-128x128.jpg?rev=2052855\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:67:\"https://ps.w.org/hello-dolly/assets/banner-1544x500.jpg?rev=2645582\";s:2:\"1x\";s:66:\"https://ps.w.org/hello-dolly/assets/banner-772x250.jpg?rev=2052855\";}s:11:\"banners_rtl\";a:0:{}s:8:\"requires\";s:3:\"4.6\";}}}','no'),(126,'eaccounting_notices','a:0:{}','yes'),(127,'eaccounting_settings','a:18:{s:20:\"financial_year_start\";s:5:\"01-01\";s:22:\"default_payment_method\";s:4:\"cash\";s:15:\"default_account\";i:1;s:16:\"default_currency\";b:0;s:12:\"company_name\";s:11:\"example.com\";s:13:\"company_email\";b:0;s:14:\"invoice_prefix\";s:4:\"INV-\";s:13:\"invoice_digit\";s:1:\"5\";s:11:\"invoice_due\";s:2:\"15\";s:18:\"invoice_item_label\";s:4:\"Item\";s:19:\"invoice_price_label\";s:5:\"Price\";s:22:\"invoice_quantity_label\";s:8:\"Quantity\";s:11:\"bill_prefix\";s:5:\"BILL-\";s:10:\"bill_digit\";s:1:\"5\";s:8:\"bill_due\";s:2:\"15\";s:15:\"bill_item_label\";s:4:\"Item\";s:16:\"bill_price_label\";s:5:\"Price\";s:19:\"bill_quantity_label\";s:8:\"Quantity\";}','yes'),(128,'eaccounting_install_date','1713690397','yes'),(129,'_transient_timeout__eaccounting_activation_redirect','1713690427','no'),(130,'_transient__eaccounting_activation_redirect','1','no'),(131,'_transient_timeout_eaccounting_check_protection_files','1713776797','no'),(132,'_transient_eaccounting_check_protection_files','1','no'),(133,'eac_version','1.0.0','yes'),(134,'eaccounting_version','1.1.8','yes');
/*!40000 ALTER TABLE `test_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_postmeta`
--

DROP TABLE IF EXISTS `test_postmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_postmeta` (
  `meta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `post_id` (`post_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_postmeta`
--

LOCK TABLES `test_postmeta` WRITE;
/*!40000 ALTER TABLE `test_postmeta` DISABLE KEYS */;
INSERT INTO `test_postmeta` VALUES (1,2,'_wp_page_template','default'),(2,3,'_wp_page_template','default');
/*!40000 ALTER TABLE `test_postmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_posts`
--

DROP TABLE IF EXISTS `test_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_posts` (
  `ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_author` bigint unsigned NOT NULL DEFAULT '0',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_title` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_excerpt` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'open',
  `post_password` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `post_name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `to_ping` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `pinged` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_parent` bigint unsigned NOT NULL DEFAULT '0',
  `guid` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `menu_order` int NOT NULL DEFAULT '0',
  `post_type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_count` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `post_name` (`post_name`(191)),
  KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
  KEY `post_parent` (`post_parent`),
  KEY `post_author` (`post_author`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_posts`
--

LOCK TABLES `test_posts` WRITE;
/*!40000 ALTER TABLE `test_posts` DISABLE KEYS */;
INSERT INTO `test_posts` VALUES (1,1,'2024-04-21 09:06:27','2024-04-21 09:06:27','<!-- wp:paragraph -->\n<p>Welcome to WordPress. This is your first post. Edit or delete it, then start writing!</p>\n<!-- /wp:paragraph -->','Hello world!','','publish','open','open','','hello-world','','','2024-04-21 09:06:27','2024-04-21 09:06:27','',0,'https://example.com/?p=1',0,'post','',1),(2,1,'2024-04-21 09:06:27','2024-04-21 09:06:27','<!-- wp:paragraph -->\n<p>This is an example page. It\'s different from a blog post because it will stay in one place and will show up in your site navigation (in most themes). Most people start with an About page that introduces them to potential site visitors. It might say something like this:</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><p>Hi there! I\'m a bike messenger by day, aspiring actor by night, and this is my website. I live in Los Angeles, have a great dog named Jack, and I like pi&#241;a coladas. (And gettin\' caught in the rain.)</p></blockquote>\n<!-- /wp:quote -->\n\n<!-- wp:paragraph -->\n<p>...or something like this:</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><p>The XYZ Doohickey Company was founded in 1971, and has been providing quality doohickeys to the public ever since. Located in Gotham City, XYZ employs over 2,000 people and does all kinds of awesome things for the Gotham community.</p></blockquote>\n<!-- /wp:quote -->\n\n<!-- wp:paragraph -->\n<p>As a new WordPress user, you should go to <a href=\"https://example.com/wp-admin/\">your dashboard</a> to delete this page and create new pages for your content. Have fun!</p>\n<!-- /wp:paragraph -->','Sample Page','','publish','closed','open','','sample-page','','','2024-04-21 09:06:27','2024-04-21 09:06:27','',0,'https://example.com/?page_id=2',0,'page','',0),(3,1,'2024-04-21 09:06:27','2024-04-21 09:06:27','<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Who we are</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>Our website address is: https://example.com.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Comments</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>When visitors leave comments on the site we collect the data shown in the comments form, and also the visitor&#8217;s IP address and browser user agent string to help spam detection.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>An anonymized string created from your email address (also called a hash) may be provided to the Gravatar service to see if you are using it. The Gravatar service privacy policy is available here: https://automattic.com/privacy/. After approval of your comment, your profile picture is visible to the public in the context of your comment.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Media</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you upload images to the website, you should avoid uploading images with embedded location data (EXIF GPS) included. Visitors to the website can download and extract any location data from images on the website.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Cookies</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you leave a comment on our site you may opt-in to saving your name, email address and website in cookies. These are for your convenience so that you do not have to fill in your details again when you leave another comment. These cookies will last for one year.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>If you visit our login page, we will set a temporary cookie to determine if your browser accepts cookies. This cookie contains no personal data and is discarded when you close your browser.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>When you log in, we will also set up several cookies to save your login information and your screen display choices. Login cookies last for two days, and screen options cookies last for a year. If you select &quot;Remember Me&quot;, your login will persist for two weeks. If you log out of your account, the login cookies will be removed.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>If you edit or publish an article, an additional cookie will be saved in your browser. This cookie includes no personal data and simply indicates the post ID of the article you just edited. It expires after 1 day.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Embedded content from other websites</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>Articles on this site may include embedded content (e.g. videos, images, articles, etc.). Embedded content from other websites behaves in the exact same way as if the visitor has visited the other website.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>These websites may collect data about you, use cookies, embed additional third-party tracking, and monitor your interaction with that embedded content, including tracking your interaction with the embedded content if you have an account and are logged in to that website.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Who we share your data with</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you request a password reset, your IP address will be included in the reset email.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">How long we retain your data</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you leave a comment, the comment and its metadata are retained indefinitely. This is so we can recognize and approve any follow-up comments automatically instead of holding them in a moderation queue.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>For users that register on our website (if any), we also store the personal information they provide in their user profile. All users can see, edit, or delete their personal information at any time (except they cannot change their username). Website administrators can also see and edit that information.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">What rights you have over your data</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you have an account on this site, or have left comments, you can request to receive an exported file of the personal data we hold about you, including any data you have provided to us. You can also request that we erase any personal data we hold about you. This does not include any data we are obliged to keep for administrative, legal, or security purposes.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Where your data is sent</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>Visitor comments may be checked through an automated spam detection service.</p>\n<!-- /wp:paragraph -->\n','Privacy Policy','','draft','closed','open','','privacy-policy','','','2024-04-21 09:06:27','2024-04-21 09:06:27','',0,'https://example.com/?page_id=3',0,'page','',0);
/*!40000 ALTER TABLE `test_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_term_relationships`
--

DROP TABLE IF EXISTS `test_term_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_term_relationships` (
  `object_id` bigint unsigned NOT NULL DEFAULT '0',
  `term_taxonomy_id` bigint unsigned NOT NULL DEFAULT '0',
  `term_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  KEY `term_taxonomy_id` (`term_taxonomy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_term_relationships`
--

LOCK TABLES `test_term_relationships` WRITE;
/*!40000 ALTER TABLE `test_term_relationships` DISABLE KEYS */;
INSERT INTO `test_term_relationships` VALUES (1,1,0);
/*!40000 ALTER TABLE `test_term_relationships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_term_taxonomy`
--

DROP TABLE IF EXISTS `test_term_taxonomy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_term_taxonomy` (
  `term_taxonomy_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint unsigned NOT NULL DEFAULT '0',
  `taxonomy` varchar(32) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `description` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `parent` bigint unsigned NOT NULL DEFAULT '0',
  `count` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_taxonomy_id`),
  UNIQUE KEY `term_id_taxonomy` (`term_id`,`taxonomy`),
  KEY `taxonomy` (`taxonomy`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_term_taxonomy`
--

LOCK TABLES `test_term_taxonomy` WRITE;
/*!40000 ALTER TABLE `test_term_taxonomy` DISABLE KEYS */;
INSERT INTO `test_term_taxonomy` VALUES (1,1,'category','',0,1);
/*!40000 ALTER TABLE `test_term_taxonomy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_termmeta`
--

DROP TABLE IF EXISTS `test_termmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_termmeta` (
  `meta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `term_id` (`term_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_termmeta`
--

LOCK TABLES `test_termmeta` WRITE;
/*!40000 ALTER TABLE `test_termmeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_termmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_terms`
--

DROP TABLE IF EXISTS `test_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_terms` (
  `term_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `slug` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `term_group` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_id`),
  KEY `slug` (`slug`(191)),
  KEY `name` (`name`(191))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_terms`
--

LOCK TABLES `test_terms` WRITE;
/*!40000 ALTER TABLE `test_terms` DISABLE KEYS */;
INSERT INTO `test_terms` VALUES (1,'Uncategorized','uncategorized',0);
/*!40000 ALTER TABLE `test_terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_usermeta`
--

DROP TABLE IF EXISTS `test_usermeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_usermeta` (
  `umeta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`umeta_id`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_usermeta`
--

LOCK TABLES `test_usermeta` WRITE;
/*!40000 ALTER TABLE `test_usermeta` DISABLE KEYS */;
INSERT INTO `test_usermeta` VALUES (1,1,'nickname','admin'),(2,1,'first_name',''),(3,1,'last_name',''),(4,1,'description',''),(5,1,'rich_editing','true'),(6,1,'syntax_highlighting','true'),(7,1,'comment_shortcuts','false'),(8,1,'admin_color','fresh'),(9,1,'use_ssl','0'),(10,1,'show_admin_bar_front','true'),(11,1,'locale',''),(12,1,'test_capabilities','a:1:{s:13:\"administrator\";b:1;}'),(13,1,'test_user_level','10'),(14,1,'dismissed_wp_pointers',''),(15,1,'show_welcome_panel','1');
/*!40000 ALTER TABLE `test_usermeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_users`
--

DROP TABLE IF EXISTS `test_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_users` (
  `ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_pass` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_nicename` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_email` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_url` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_status` int NOT NULL DEFAULT '0',
  `display_name` varchar(250) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`),
  KEY `user_email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_users`
--

LOCK TABLES `test_users` WRITE;
/*!40000 ALTER TABLE `test_users` DISABLE KEYS */;
INSERT INTO `test_users` VALUES (1,'admin','$P$BPdIFK9avKiHk1Tj./RLuKDJf6Jpub0','admin','admin@example.com','https://example.com','2024-04-21 09:06:27','',0,'admin');
/*!40000 ALTER TABLE `test_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-04-21 15:06:37
