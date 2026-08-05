-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: atoz_gadgets_db
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
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banners` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `image_url` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banners`
--

LOCK TABLES `banners` WRITE;
/*!40000 ALTER TABLE `banners` DISABLE KEYS */;
/*!40000 ALTER TABLE `banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'Apple','apple',NULL,'active','2026-07-25 07:31:30','2026-07-25 07:31:30');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carts_product_id_foreign` (`product_id`),
  KEY `carts_user_id_index` (`user_id`),
  CONSTRAINT `carts_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `cj_keyword` varchar(150) DEFAULT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_slug_index` (`slug`),
  KEY `categories_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Test Category','test-category','Test',NULL,'active','test_keyword',NULL,NULL,'2026-07-25 05:44:37','2026-07-25 05:44:37'),(3,'Tech Gadgets','tech-gadgets',NULL,NULL,'active',NULL,NULL,NULL,'2026-07-25 06:05:03','2026-07-25 06:05:03'),(4,'Electronics','electronics',NULL,NULL,'active',NULL,NULL,NULL,'2026-07-25 07:31:30','2026-07-25 07:31:30'),(7,'Tech','tech',NULL,NULL,'active',NULL,NULL,NULL,'2026-07-27 01:52:37','2026-07-27 01:52:37'),(9,'Tech Test','tech-test-slug',NULL,NULL,'active',NULL,NULL,NULL,'2026-07-27 01:53:13','2026-07-27 01:53:13');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cj_orders`
--

DROP TABLE IF EXISTS `cj_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cj_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `internal_order_id` bigint(20) unsigned NOT NULL,
  `cj_order_id` varchar(100) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `tracking_number` varchar(150) DEFAULT NULL,
  `shipping_cost` decimal(10,2) DEFAULT NULL,
  `cj_total_amount` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cj_orders_cj_order_id_unique` (`cj_order_id`),
  KEY `cj_orders_internal_order_id_foreign` (`internal_order_id`),
  CONSTRAINT `cj_orders_internal_order_id_foreign` FOREIGN KEY (`internal_order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cj_orders`
--

LOCK TABLES `cj_orders` WRITE;
/*!40000 ALTER TABLE `cj_orders` DISABLE KEYS */;
INSERT INTO `cj_orders` VALUES (1,1,'CJ-999','shipped',NULL,NULL,NULL,'2026-07-25 07:36:46','2026-07-27 03:18:41');
/*!40000 ALTER TABLE `cj_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cj_products`
--

DROP TABLE IF EXISTS `cj_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cj_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cj_product_id` varchar(100) NOT NULL,
  `internal_product_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `category_name` varchar(255) DEFAULT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `sell_price` decimal(10,2) DEFAULT NULL,
  `weight` double(8,2) DEFAULT NULL,
  `cj_image` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `list_status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cj_products_cj_product_id_unique` (`cj_product_id`),
  KEY `cj_products_internal_product_id_foreign` (`internal_product_id`),
  CONSTRAINT `cj_products_internal_product_id_foreign` FOREIGN KEY (`internal_product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cj_products`
--

LOCK TABLES `cj_products` WRITE;
/*!40000 ALTER TABLE `cj_products` DISABLE KEYS */;
INSERT INTO `cj_products` VALUES (1,'cj-123',5,NULL,NULL,NULL,NULL,10.99,NULL,NULL,NULL,NULL,'2026-07-25 05:47:02','2026-07-27 03:18:41'),(2,'cj-456',6,NULL,NULL,NULL,NULL,15.50,NULL,NULL,NULL,NULL,'2026-07-25 05:47:02','2026-07-27 03:18:41'),(19,'CJ-TEST-001',96,'Awesome Drone',NULL,'Drones',NULL,45.50,NULL,'https://example.com/drone.jpg','imported',NULL,'2026-07-25 07:48:42','2026-07-27 03:18:40'),(20,'CJ-TEST-XSS',97,'<script>alert(\"XSS\")</script> Awesome Drone',NULL,'Drones',NULL,45.50,NULL,'https://example.com/drone.jpg','imported',NULL,'2026-07-25 07:48:43','2026-07-27 03:18:40'),(21,'CJ-TEST-002\' OR 1=1 --',98,'Drone',NULL,'Drones',NULL,45.50,NULL,'https://example.com/drone.jpg','imported',NULL,'2026-07-25 07:48:43','2026-07-27 03:18:40');
/*!40000 ALTER TABLE `cj_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coupons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `value` decimal(8,2) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2026_07_25_091555_create_categories_table',1),(6,'2026_07_25_091556_create_sub_categories_table',1),(7,'2026_07_25_091558_create_brands_table',1),(8,'2026_07_25_091600_create_products_table',1),(9,'2026_07_25_092409_create_orders_table',1),(10,'2026_07_25_092411_create_order_items_table',1),(11,'2026_07_25_092412_create_cj_products_table',1),(12,'2026_07_25_092413_create_cj_orders_table',1),(13,'2026_07_25_094022_create_carts_table',1),(14,'2026_07_25_094023_create_wishlists_table',1),(15,'2026_07_25_094024_create_coupons_table',1),(16,'2026_07_25_094025_create_product_reviews_table',1),(17,'2026_07_25_094026_create_banners_table',1),(18,'2026_07_25_094027_create_offers_table',1),(19,'2026_07_25_094028_create_payments_table',1),(20,'2026_07_25_094029_create_shipments_table',1),(21,'2026_07_25_101903_add_performance_indexes',2),(22,'2026_07_25_110726_create_roles_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offers`
--

DROP TABLE IF EXISTS `offers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offers`
--

LOCK TABLES `offers` WRITE;
/*!40000 ALTER TABLE `offers` DISABLE KEYS */;
/*!40000 ALTER TABLE `offers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `variant_id` bigint(20) unsigned DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `order_number` varchar(100) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `payment_status` varchar(50) NOT NULL DEFAULT 'unpaid',
  `shipping_status` varchar(50) NOT NULL DEFAULT 'unshipped',
  `shipping_address` text DEFAULT NULL,
  `billing_address` text DEFAULT NULL,
  `contact_email` varchar(150) DEFAULT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_user_id_index` (`user_id`),
  KEY `orders_status_index` (`status`),
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,80,'ORD-123',100.00,0.00,0.00,0.00,0.00,'shipped','unpaid','unshipped',NULL,NULL,NULL,NULL,'2026-07-25 07:34:52','2026-07-27 03:18:41');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL DEFAULT 'payoneer',
  `payoneer_transaction_id` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_order_id_foreign` (`order_id`),
  CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',3,'auth_token','30c8d9ddf460d303bbecc02c4463e57634bfc66c77277bbb426a4517c1601973','[\"*\"]','2026-07-25 05:38:47','2026-07-25 05:38:47','2026-07-25 05:38:47'),(2,'App\\Models\\User',9,'auth_token','ffca4372a2f680286bc5107ffe1df10233acc3d09283f2a16504c85e9e92f8c2','[\"*\"]',NULL,'2026-07-25 05:42:06','2026-07-25 05:42:06'),(3,'App\\Models\\User',10,'auth_token','173bea90436ce962e4fb7af5bc748072892c321750431282fed6ac5eb1a16dac','[\"*\"]','2026-07-25 05:42:06','2026-07-25 05:42:06','2026-07-25 05:42:06'),(4,'App\\Models\\User',11,'auth_token','04817ffa0f73166607d45da30ce2a5c7558de181c3c178791e81bbb883d8b774','[\"*\"]',NULL,'2026-07-25 05:43:03','2026-07-25 05:43:03'),(5,'App\\Models\\User',11,'auth_token','53d76273de7325df87c40c8e9fecf70d8ed409da2e64f65117bf91497f92e253','[\"*\"]',NULL,'2026-07-25 05:43:03','2026-07-25 05:43:03'),(6,'App\\Models\\User',12,'auth_token','fcf6bdf8d02c57295dc5f8ec194b921257d03519bef9958810d010306c6eb49e','[\"*\"]','2026-07-25 05:43:04','2026-07-25 05:43:04','2026-07-25 05:43:04'),(7,'App\\Models\\User',13,'auth_token','7ff26e957695b354680d92ad2e06ef083c3e02c80ca978b463b6f58930ff0699','[\"*\"]',NULL,'2026-07-25 05:43:17','2026-07-25 05:43:17'),(8,'App\\Models\\User',13,'auth_token','3b937b2b415d46bcd7617a9e1475dc259fbcc3db6a50d5fde2d4a7f2a131c420','[\"*\"]',NULL,'2026-07-25 05:43:17','2026-07-25 05:43:17'),(9,'App\\Models\\User',14,'auth_token','937d5e7a8a23506e468b12b17fdbb64170e0e86c1eb4bb40c2dce5205e65313d','[\"*\"]','2026-07-25 05:43:17','2026-07-25 05:43:17','2026-07-25 05:43:17'),(10,'App\\Models\\User',15,'auth_token','74581073e7655578f6fd55140019d1a71029dd4f8075f13c36147ff41be0b7f8','[\"*\"]',NULL,'2026-07-25 07:31:30','2026-07-25 07:31:30'),(11,'App\\Models\\User',15,'auth_token','c517ca950eb665fa8d5b810cbe29fbb4909a6124347eadbe3db8c667db2ea332','[\"*\"]',NULL,'2026-07-25 07:31:30','2026-07-25 07:31:30'),(12,'App\\Models\\User',16,'auth_token','90c0c28d17348bff1e27e43211dc91fc6ee29783131138e01e7271ad5736fa49','[\"*\"]','2026-07-25 07:31:30','2026-07-25 07:31:30','2026-07-25 07:31:30'),(13,'App\\Models\\User',19,'auth_token','a7f6c6289af14177fb30477d7dd3916b74925c46f0c26ca83a51f35222ca366a','[\"*\"]',NULL,'2026-07-25 07:31:44','2026-07-25 07:31:44'),(14,'App\\Models\\User',19,'auth_token','e6b330efc000fe6128fbbe37627eec04ed5404059e217d730699d542ea3fd90b','[\"*\"]',NULL,'2026-07-25 07:31:44','2026-07-25 07:31:44'),(15,'App\\Models\\User',20,'auth_token','631d842223454fbe0f6a933d0bf0ff08b79fdd7ddb1e06973914a710c5da95d7','[\"*\"]','2026-07-25 07:31:44','2026-07-25 07:31:44','2026-07-25 07:31:44'),(16,'App\\Models\\User',23,'auth_token','4db05fbaba257cb82d3276f44d4434222e47eb018f2e69dd5bdd48dccc00ec78','[\"*\"]',NULL,'2026-07-25 07:34:52','2026-07-25 07:34:52'),(17,'App\\Models\\User',23,'auth_token','2afa37d43338c6df88b82610e3616c4416b7737c9eca78c7dca877d136e7fc23','[\"*\"]',NULL,'2026-07-25 07:34:52','2026-07-25 07:34:52'),(18,'App\\Models\\User',24,'auth_token','54877f13707776256d23d642e2e0abebc0290bf11f41bdc73564f23a9fd0d228','[\"*\"]','2026-07-25 07:34:52','2026-07-25 07:34:52','2026-07-25 07:34:52'),(19,'App\\Models\\User',27,'auth_token','4b85e5d23dc78b5858cdf06a2b807ec2201560634c15b62d658d348d6b33fa96','[\"*\"]',NULL,'2026-07-25 07:35:26','2026-07-25 07:35:26'),(20,'App\\Models\\User',27,'auth_token','456f3bb81d311911d1c37250d8ee849b0088dc6abce57cb1740730e5846acb7a','[\"*\"]',NULL,'2026-07-25 07:35:26','2026-07-25 07:35:26'),(21,'App\\Models\\User',28,'auth_token','1d4b32eaf1e8531a426b7c9cb3593540335dc5ae7908e8dc9fd6c1b6d20cc58f','[\"*\"]','2026-07-25 07:35:26','2026-07-25 07:35:26','2026-07-25 07:35:26'),(22,'App\\Models\\User',30,'auth_token','48691b82f29e314825a9cb17c69c611862fe4c5cfee69b20545f0314641cdd5e','[\"*\"]',NULL,'2026-07-25 07:35:46','2026-07-25 07:35:46'),(23,'App\\Models\\User',30,'auth_token','2bace6e9e3d3ae717a3be2a433adf56ed435e8c965ebb3309f33c7b8196be397','[\"*\"]',NULL,'2026-07-25 07:35:46','2026-07-25 07:35:46'),(24,'App\\Models\\User',31,'auth_token','c779875b7c993bac96bcb7add728013af212228be28ad8ff61d124d6d94c2eb3','[\"*\"]','2026-07-25 07:35:47','2026-07-25 07:35:47','2026-07-25 07:35:47'),(25,'App\\Models\\User',33,'auth_token','09635bd55c14bcc676e4b855865c64eb91b0a32ab6f01c70e75e16e56d31a91e','[\"*\"]',NULL,'2026-07-25 07:36:46','2026-07-25 07:36:46'),(26,'App\\Models\\User',33,'auth_token','121e96360b802528e46323d83a98afe144683e0856f0d8a324eeb0070f09f9c2','[\"*\"]',NULL,'2026-07-25 07:36:46','2026-07-25 07:36:46'),(27,'App\\Models\\User',34,'auth_token','c105e9134198089adcedc2323cc72ebcb0cd2b1a09820034e637a7c4e4780549','[\"*\"]','2026-07-25 07:36:46','2026-07-25 07:36:46','2026-07-25 07:36:46'),(28,'App\\Models\\User',36,'auth_token','5fc33528b5a6195ea1851527c8ae2ac9b4e6b6e4ed3d9bb8e12cbd7df0e08b95','[\"*\"]',NULL,'2026-07-25 07:37:22','2026-07-25 07:37:22'),(29,'App\\Models\\User',36,'auth_token','5d900dd09c886fbcf8159af82e15f46c17ed2a7ed1d82aba90059a8de41382d9','[\"*\"]',NULL,'2026-07-25 07:37:22','2026-07-25 07:37:22'),(30,'App\\Models\\User',37,'auth_token','174004c4f0579a847079fde7d4f12f7844b4ce52c2cc9e9d3bef482172b6ff0f','[\"*\"]','2026-07-25 07:37:22','2026-07-25 07:37:22','2026-07-25 07:37:22'),(31,'App\\Models\\User',39,'auth_token','28bc56dd0713534b31c89bc76d12f1d3a288fa7ce3b5c1f9af774dfe377cc3d3','[\"*\"]',NULL,'2026-07-25 07:37:55','2026-07-25 07:37:55'),(32,'App\\Models\\User',39,'auth_token','ac06456aa53e306eebe4d18ad857d0acadd0879106adf00633da6de2c1c76515','[\"*\"]',NULL,'2026-07-25 07:37:55','2026-07-25 07:37:55'),(33,'App\\Models\\User',40,'auth_token','e6c3960ac50b3cb855528b4a161a8bad2dad90bbd811238a959b2c3bc241d84d','[\"*\"]','2026-07-25 07:37:55','2026-07-25 07:37:55','2026-07-25 07:37:55'),(34,'App\\Models\\User',42,'auth_token','d4ddc33b1f0239fc5c5e1cb7812b64ac77628aa3d84eae728387ff323ea87278','[\"*\"]',NULL,'2026-07-27 01:19:20','2026-07-27 01:19:20'),(35,'App\\Models\\User',42,'auth_token','e1aece95622e1ca807bdf2ba29c0d6bd4e2f746ee256819dd42eed2c2cb05ab6','[\"*\"]',NULL,'2026-07-27 01:19:20','2026-07-27 01:19:20'),(36,'App\\Models\\User',43,'auth_token','50f6e978d3179182dba444d42421c089efd90cba8a6ef3fb346744cc3079c864','[\"*\"]','2026-07-27 01:19:20','2026-07-27 01:19:20','2026-07-27 01:19:20'),(37,'App\\Models\\User',45,'auth_token','6931e333b4aa41d4c5faf6adb309ece298617c8de6f245bf5d0176d9fc05aedf','[\"*\"]',NULL,'2026-07-27 01:19:39','2026-07-27 01:19:39'),(38,'App\\Models\\User',45,'auth_token','f95fd096b1370276ae949e639c5ef9dfc23116752b5057dc9abb61161543ca14','[\"*\"]',NULL,'2026-07-27 01:19:39','2026-07-27 01:19:39'),(39,'App\\Models\\User',46,'auth_token','d78c60681f30303d462e4bfb2724a2ad38b8fc688160a566fa0dc77cf41f44ad','[\"*\"]','2026-07-27 01:19:39','2026-07-27 01:19:39','2026-07-27 01:19:39'),(40,'App\\Models\\User',48,'auth_token','8a87a2ab577c743e26e1111f807a766ee86dac4a09bcee0421cac92f577d93de','[\"*\"]',NULL,'2026-07-27 01:20:36','2026-07-27 01:20:36'),(41,'App\\Models\\User',48,'auth_token','bb963de132d30c2fd07c90da9a3ce43d0506125b92514f872e3d596541ee72bb','[\"*\"]',NULL,'2026-07-27 01:20:36','2026-07-27 01:20:36'),(42,'App\\Models\\User',49,'auth_token','e36c7c4dd1edf0bd94c83467a26debb1ad0c7c9eb91123115c52ca63544c9d5d','[\"*\"]','2026-07-27 01:20:36','2026-07-27 01:20:36','2026-07-27 01:20:36'),(43,'App\\Models\\User',51,'auth_token','aded8d70f0f08acd6651dcda5408892ea53ecc7eae4b7507322d14bf590c273c','[\"*\"]',NULL,'2026-07-27 01:21:39','2026-07-27 01:21:39'),(44,'App\\Models\\User',51,'auth_token','c992bbe7c375ef515f275bb5dcb7846244fce80ce7c44632117b974f4b2a8ef4','[\"*\"]',NULL,'2026-07-27 01:21:40','2026-07-27 01:21:40'),(45,'App\\Models\\User',52,'auth_token','6bfdf4ec4e2bc084cbfb27893dba5762ecd596b8b0ae6ad2d1183de87a61f160','[\"*\"]','2026-07-27 01:21:40','2026-07-27 01:21:40','2026-07-27 01:21:40'),(46,'App\\Models\\User',54,'auth_token','59add39d269a58e6c98c6e8e21510207eda89559eb9aef536fbc1926a2677935','[\"*\"]',NULL,'2026-07-27 01:29:05','2026-07-27 01:29:05'),(47,'App\\Models\\User',54,'auth_token','5dd340930c2f7124e8999e1d79192b67f053e5749070659ee88338427ebca0f9','[\"*\"]',NULL,'2026-07-27 01:29:05','2026-07-27 01:29:05'),(48,'App\\Models\\User',55,'auth_token','55cdf49185b3e38d069b2cb843e91698e1eb6c37e66c7e0635f91081e0ce2cf9','[\"*\"]','2026-07-27 01:29:05','2026-07-27 01:29:05','2026-07-27 01:29:05'),(49,'App\\Models\\User',57,'auth_token','30ed6a1790417f0af58bb6f2bacb4e4082d483178deedc70d9555d24f2aa9b28','[\"*\"]',NULL,'2026-07-27 01:33:35','2026-07-27 01:33:35'),(50,'App\\Models\\User',57,'auth_token','e5178c61d8361568fefcc2b277e984f126477209e1de066423740cb90581a23d','[\"*\"]',NULL,'2026-07-27 01:33:35','2026-07-27 01:33:35'),(51,'App\\Models\\User',58,'auth_token','156a1eac35ab0111d01870468bb9d390dee8268e9e52ae122a893280b57880d4','[\"*\"]','2026-07-27 01:33:36','2026-07-27 01:33:36','2026-07-27 01:33:36'),(52,'App\\Models\\User',60,'auth_token','696afc80fa5dfa6e31752afd68bde45156cb6e98e608fdcd5330235443d57f23','[\"*\"]',NULL,'2026-07-27 01:35:46','2026-07-27 01:35:46'),(53,'App\\Models\\User',60,'auth_token','ff754f1fe75510aa91a9a6df147faeb2a7d08ebc545ab50cb58f867b1f95eb00','[\"*\"]',NULL,'2026-07-27 01:35:46','2026-07-27 01:35:46'),(54,'App\\Models\\User',61,'auth_token','f3b7a5d3d2c59e74fa788459fc5630c64e1a502dea1583d33a7d247e52bf6165','[\"*\"]','2026-07-27 01:35:46','2026-07-27 01:35:46','2026-07-27 01:35:46'),(55,'App\\Models\\User',63,'auth_token','19c19dd5d5f506dfc9ebe9382fbfbfb13b7db9617986d7b98ec6f75cf2345ed8','[\"*\"]',NULL,'2026-07-27 01:52:44','2026-07-27 01:52:44'),(56,'App\\Models\\User',63,'auth_token','31280ac8084c7c395610255bdf2986eaee90e554a10322b56dbe7a1af88ecd9b','[\"*\"]',NULL,'2026-07-27 01:52:44','2026-07-27 01:52:44'),(57,'App\\Models\\User',64,'auth_token','fb6d42a289585adf6d141659ee14e6ce1b12734597af98f205f90c773bca1927','[\"*\"]','2026-07-27 01:52:44','2026-07-27 01:52:44','2026-07-27 01:52:44'),(58,'App\\Models\\User',66,'auth_token','e733c8759a8ed9704ac159ed653a1bad50140ff370f20709953223c471b48bc6','[\"*\"]',NULL,'2026-07-27 01:53:09','2026-07-27 01:53:09'),(59,'App\\Models\\User',66,'auth_token','9a4434c85d28a460fb43a5fdd802dd3ee7783ba9529993beb913b7dba10a9b56','[\"*\"]',NULL,'2026-07-27 01:53:09','2026-07-27 01:53:09'),(60,'App\\Models\\User',67,'auth_token','4d2afd3719d3a0fcfd5bcfa1f617016f827156cdf10efa39ba9c3a9e08cefeae','[\"*\"]','2026-07-27 01:53:09','2026-07-27 01:53:09','2026-07-27 01:53:09'),(61,'App\\Models\\User',69,'auth_token','95781c962b64b4150a03b979bc721a0b44530daa5737dbc64df95f46e1e3981e','[\"*\"]',NULL,'2026-07-27 01:53:28','2026-07-27 01:53:28'),(62,'App\\Models\\User',69,'auth_token','2499d0f0109e8eb9411ab267bba375992a52022ae540a274196aae51db27b8f3','[\"*\"]',NULL,'2026-07-27 01:53:28','2026-07-27 01:53:28'),(63,'App\\Models\\User',70,'auth_token','debd878b54603c5a07b0589ebd7de28124168dae623c562439e0f1e2592667d9','[\"*\"]','2026-07-27 01:53:28','2026-07-27 01:53:28','2026-07-27 01:53:28'),(64,'App\\Models\\User',72,'auth_token','7f77901ed894801adc712aa5a0acf90e11c93c663aff4e3841f5d2c0ca69f79e','[\"*\"]',NULL,'2026-07-27 02:54:32','2026-07-27 02:54:32'),(65,'App\\Models\\User',72,'auth_token','7ced5af971f6f1804ada11eb19226cd34d1f9c8cb467b52d6f432b6ae3b27ff6','[\"*\"]',NULL,'2026-07-27 02:54:32','2026-07-27 02:54:32'),(66,'App\\Models\\User',73,'auth_token','f9a27cdd15a4113dab9c26947c73428e4861c74acd34aa88db6e8537aeedfbda','[\"*\"]','2026-07-27 02:54:32','2026-07-27 02:54:32','2026-07-27 02:54:32'),(67,'App\\Models\\User',75,'auth_token','f5bc8c659009433d11583ecb1bae671d092312987cf0b4a473ae30589562fa71','[\"*\"]',NULL,'2026-07-27 03:01:18','2026-07-27 03:01:18'),(68,'App\\Models\\User',75,'auth_token','24919eba64c64f17749cca7245dbd824596d60bf18cc3f1806a9af0047d817c9','[\"*\"]',NULL,'2026-07-27 03:01:18','2026-07-27 03:01:18'),(69,'App\\Models\\User',76,'auth_token','47dfed55f529e9a1f2589c83924719536a7794212005b6ce08616372daa4de46','[\"*\"]','2026-07-27 03:01:18','2026-07-27 03:01:18','2026-07-27 03:01:18'),(70,'App\\Models\\User',78,'auth_token','f2fb25fa27f2c89d68fed201c961bbfe4d8ae380a2d48b96b015c6458f35c3fb','[\"*\"]',NULL,'2026-07-27 03:18:39','2026-07-27 03:18:39'),(71,'App\\Models\\User',78,'auth_token','b8dd4d91d2460d239e2c48f58116578e1953d2eb8a80c41736f91cdd72f85436','[\"*\"]',NULL,'2026-07-27 03:18:39','2026-07-27 03:18:39'),(72,'App\\Models\\User',79,'auth_token','a92881f9937b3583a13eae14a8ebd48139edf7d3144a10f5259dca0fe7499ecc','[\"*\"]','2026-07-27 03:18:39','2026-07-27 03:18:39','2026-07-27 03:18:39');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `rating` int(11) NOT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_reviews_user_id_foreign` (`user_id`),
  KEY `product_reviews_product_id_foreign` (`product_id`),
  CONSTRAINT `product_reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `product_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_reviews`
--

LOCK TABLES `product_reviews` WRITE;
/*!40000 ALTER TABLE `product_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `subcategory_id` bigint(20) unsigned NOT NULL,
  `brand_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `short_description` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sku` varchar(100) NOT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `tax_percentage` decimal(5,2) DEFAULT 0.00,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `weight` double(8,2) DEFAULT NULL,
  `length` double(8,2) DEFAULT NULL,
  `width` double(8,2) DEFAULT NULL,
  `height` double(8,2) DEFAULT NULL,
  `thumbnail_image` varchar(255) DEFAULT NULL,
  `handle` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `option1_name` varchar(100) DEFAULT NULL,
  `option2_name` varchar(100) DEFAULT NULL,
  `option3_name` varchar(100) DEFAULT NULL,
  `hs_code` varchar(100) DEFAULT NULL,
  `country_of_origin` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `bin_name` varchar(100) DEFAULT NULL,
  `incoming` int(11) NOT NULL DEFAULT 0,
  `unavailable` int(11) NOT NULL DEFAULT 0,
  `committed` int(11) NOT NULL DEFAULT 0,
  `available` int(11) NOT NULL DEFAULT 0,
  `onhand_old` int(11) NOT NULL DEFAULT 0,
  `onhand_new` int(11) NOT NULL DEFAULT 0,
  `status` varchar(50) DEFAULT 'active',
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` bigint(20) unsigned NOT NULL,
  `fulfillment_type` varchar(255) NOT NULL DEFAULT 'cj',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  UNIQUE KEY `products_barcode_unique` (`barcode`),
  KEY `products_subcategory_id_foreign` (`subcategory_id`),
  KEY `products_brand_id_foreign` (`brand_id`),
  KEY `products_created_by_foreign` (`created_by`),
  KEY `products_slug_index` (`slug`),
  KEY `products_status_index` (`status`),
  KEY `products_category_id_index` (`category_id`),
  CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `products_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `products_subcategory_id_foreign` FOREIGN KEY (`subcategory_id`) REFERENCES `sub_categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (5,1,1,NULL,'Test Gadget 1','test-gadget-1-5a1327',NULL,NULL,'cj-123',NULL,21.98,NULL,0.00,100,0.00,NULL,NULL,NULL,'http://example.com/1.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-25 05:46:19','2026-07-27 03:18:41'),(6,1,1,NULL,'Test Gadget 2','test-gadget-2-b19276',NULL,NULL,'cj-456',NULL,31.00,NULL,0.00,100,0.00,NULL,NULL,NULL,'http://example.com/2.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-25 05:46:19','2026-07-27 03:18:41'),(8,4,2,1,'iPhone 15 Pro','iphone-15-pro',NULL,NULL,'IPH15P-256',NULL,999.99,NULL,0.00,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,17,'cj','2026-07-25 07:31:30','2026-07-25 07:31:30'),(25,1,1,NULL,'Awesome Drone','awesome-drone-472b38',NULL,NULL,'CJ-cb9f99fc',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-25 07:48:42','2026-07-25 07:48:42'),(26,1,1,NULL,'<script>alert(\"XSS\")</script> Awesome Drone','scriptalertxssscript-awesome-drone-eeb9cf',NULL,NULL,'CJ-ef3bbf8b',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-25 07:48:43','2026-07-25 07:48:43'),(27,1,1,NULL,'Drone','drone-30eae8',NULL,NULL,'CJ-f2ab53cb',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-25 07:48:43','2026-07-25 07:48:43'),(28,1,1,NULL,'Awesome Drone','awesome-drone-d58deb',NULL,NULL,'CJ-1a80dfcb',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:19:20','2026-07-27 01:19:20'),(29,1,1,NULL,'<script>alert(\"XSS\")</script> Awesome Drone','scriptalertxssscript-awesome-drone-975f89',NULL,NULL,'CJ-b09f4e88',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:19:20','2026-07-27 01:19:20'),(30,1,1,NULL,'Drone','drone-a23691',NULL,NULL,'CJ-730ec088',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:19:20','2026-07-27 01:19:20'),(33,1,1,NULL,'Awesome Drone','awesome-drone-820da7',NULL,NULL,'CJ-7df30850',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:19:39','2026-07-27 01:19:39'),(34,1,1,NULL,'<script>alert(\"XSS\")</script> Awesome Drone','scriptalertxssscript-awesome-drone-ae0d3a',NULL,NULL,'CJ-0cb0cd7b',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:19:39','2026-07-27 01:19:39'),(35,1,1,NULL,'Drone','drone-127b0f',NULL,NULL,'CJ-9a4b9c96',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:19:39','2026-07-27 01:19:39'),(38,1,1,NULL,'Awesome Drone','awesome-drone-1bae80',NULL,NULL,'CJ-2368c2a4',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:20:01','2026-07-27 01:20:01'),(39,1,1,NULL,'<script>alert(\"XSS\")</script> Awesome Drone','scriptalertxssscript-awesome-drone-4c93ba',NULL,NULL,'CJ-cfbe7e33',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:20:02','2026-07-27 01:20:02'),(40,1,1,NULL,'Drone','drone-924345',NULL,NULL,'CJ-788a2276',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:20:02','2026-07-27 01:20:02'),(41,1,1,NULL,'Awesome Drone','awesome-drone-245103',NULL,NULL,'CJ-6eaa6df1',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:20:37','2026-07-27 01:20:37'),(42,1,1,NULL,'<script>alert(\"XSS\")</script> Awesome Drone','scriptalertxssscript-awesome-drone-bc7946',NULL,NULL,'CJ-75e4de11',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:20:37','2026-07-27 01:20:37'),(43,1,1,NULL,'Drone','drone-00d6d2',NULL,NULL,'CJ-3b03ec95',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:20:37','2026-07-27 01:20:37'),(46,1,1,NULL,'Awesome Drone','awesome-drone-473541',NULL,NULL,'CJ-0f19fb5e',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:20:56','2026-07-27 01:20:56'),(47,1,1,NULL,'Awesome Drone','awesome-drone-5b8964',NULL,NULL,'CJ-2a8c51d1',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:21:11','2026-07-27 01:21:11'),(48,1,1,NULL,'Awesome Drone','awesome-drone-0ecbfd',NULL,NULL,'CJ-2f3eeb48',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:21:40','2026-07-27 01:21:40'),(49,1,1,NULL,'<script>alert(\"XSS\")</script> Awesome Drone','scriptalertxssscript-awesome-drone-36e0aa',NULL,NULL,'CJ-3017b41f',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:21:40','2026-07-27 01:21:40'),(50,1,1,NULL,'Drone','drone-a2af96',NULL,NULL,'CJ-5f5a0d76',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:21:40','2026-07-27 01:21:40'),(53,1,1,NULL,'Awesome Drone','awesome-drone-88bc4a',NULL,NULL,'CJ-7d2dabcd',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:29:05','2026-07-27 01:29:05'),(54,1,1,NULL,'<script>alert(\"XSS\")</script> Awesome Drone','scriptalertxssscript-awesome-drone-d174f2',NULL,NULL,'CJ-d960282b',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:29:05','2026-07-27 01:29:05'),(55,1,1,NULL,'Drone','drone-59166c',NULL,NULL,'CJ-5d15abac',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:29:05','2026-07-27 01:29:05'),(58,1,1,NULL,'Awesome Drone','awesome-drone-ce4587',NULL,NULL,'CJ-7f09e43b',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:33:36','2026-07-27 01:33:36'),(59,1,1,NULL,'<script>alert(\"XSS\")</script> Awesome Drone','scriptalertxssscript-awesome-drone-f78106',NULL,NULL,'CJ-34bf3c86',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:33:36','2026-07-27 01:33:36'),(60,1,1,NULL,'Drone','drone-d5b2fa',NULL,NULL,'CJ-bb4b7bc1',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:33:36','2026-07-27 01:33:36'),(63,1,1,NULL,'Awesome Drone','awesome-drone-8903d2',NULL,NULL,'CJ-34911adc',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:35:46','2026-07-27 01:35:46'),(64,1,1,NULL,'<script>alert(\"XSS\")</script> Awesome Drone','scriptalertxssscript-awesome-drone-8c002b',NULL,NULL,'CJ-d39062cb',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:35:46','2026-07-27 01:35:46'),(65,1,1,NULL,'Drone','drone-cabe58',NULL,NULL,'CJ-c318a2d0',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:35:47','2026-07-27 01:35:47'),(68,7,1,NULL,'Smart Watch','smart-watch',NULL,NULL,'',NULL,199.99,NULL,0.00,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:52:37','2026-07-27 01:52:37'),(69,1,1,NULL,'Awesome Drone','awesome-drone-511c95',NULL,NULL,'CJ-709d5061',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:52:44','2026-07-27 01:52:44'),(70,1,1,NULL,'<script>alert(\"XSS\")</script> Awesome Drone','scriptalertxssscript-awesome-drone-15f067',NULL,NULL,'CJ-30b24ae1',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:52:45','2026-07-27 01:52:45'),(71,1,1,NULL,'Drone','drone-c14f5e',NULL,NULL,'CJ-aedec870',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:52:45','2026-07-27 01:52:45'),(74,1,1,NULL,'Awesome Drone','awesome-drone-5d4a4e',NULL,NULL,'CJ-3958b91f',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:53:10','2026-07-27 01:53:10'),(75,1,1,NULL,'<script>alert(\"XSS\")</script> Awesome Drone','scriptalertxssscript-awesome-drone-872b9a',NULL,NULL,'CJ-5771704b',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:53:10','2026-07-27 01:53:10'),(76,1,1,NULL,'Drone','drone-6af5b8',NULL,NULL,'CJ-305a9cfb',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:53:10','2026-07-27 01:53:10'),(80,1,1,NULL,'Awesome Drone','awesome-drone-7c97b5',NULL,NULL,'CJ-7fe283be',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:53:28','2026-07-27 01:53:28'),(81,1,1,NULL,'<script>alert(\"XSS\")</script> Awesome Drone','scriptalertxssscript-awesome-drone-0a25c0',NULL,NULL,'CJ-a63239ec',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:53:29','2026-07-27 01:53:29'),(82,1,1,NULL,'Drone','drone-a74eaf',NULL,NULL,'CJ-c3810c32',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:53:29','2026-07-27 01:53:29'),(85,9,1,NULL,'Smart Watch','smart-watch-test',NULL,NULL,'SKU-SMART-WATCH-TEST',NULL,199.99,NULL,0.00,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 01:53:30','2026-07-27 01:53:30'),(86,1,1,NULL,'Awesome Drone','awesome-drone-c4f1c9',NULL,NULL,'CJ-e64461d6',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 02:54:32','2026-07-27 02:54:32'),(87,1,1,NULL,'<script>alert(\"XSS\")</script> Awesome Drone','scriptalertxssscript-awesome-drone-91c403',NULL,NULL,'CJ-231a6521',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 02:54:33','2026-07-27 02:54:33'),(88,1,1,NULL,'Drone','drone-169cc7',NULL,NULL,'CJ-91816393',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 02:54:33','2026-07-27 02:54:33'),(91,1,1,NULL,'Awesome Drone','awesome-drone-f4e816',NULL,NULL,'CJ-9350de8e',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 03:01:18','2026-07-27 03:01:18'),(92,1,1,NULL,'<script>alert(\"XSS\")</script> Awesome Drone','scriptalertxssscript-awesome-drone-b6e19a',NULL,NULL,'CJ-c84b4ae3',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 03:01:19','2026-07-27 03:01:19'),(93,1,1,NULL,'Drone','drone-fc8307',NULL,NULL,'CJ-1495420d',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 03:01:19','2026-07-27 03:01:19'),(96,1,1,NULL,'Awesome Drone','awesome-drone-4273df',NULL,NULL,'CJ-67f78d8e',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 03:18:40','2026-07-27 03:18:40'),(97,1,1,NULL,'<script>alert(\"XSS\")</script> Awesome Drone','scriptalertxssscript-awesome-drone-72b033',NULL,NULL,'CJ-17bf1976',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 03:18:40','2026-07-27 03:18:40'),(98,1,1,NULL,'Drone','drone-65d7ce',NULL,NULL,'CJ-3d350b5e',NULL,91.00,68.25,0.00,0,NULL,NULL,NULL,NULL,'https://example.com/drone.jpg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,'active',0,1,1,'cj','2026-07-27 03:18:40','2026-07-27 03:18:40');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_role_name_unique` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Customer','[]','2026-07-25 05:38:19','2026-07-25 05:38:19'),(2,'Admin','[]','2026-07-25 05:38:19','2026-07-25 05:38:19');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipments`
--

DROP TABLE IF EXISTS `shipments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `tracking_number` varchar(255) DEFAULT NULL,
  `carrier` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shipments_order_id_foreign` (`order_id`),
  CONSTRAINT `shipments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipments`
--

LOCK TABLES `shipments` WRITE;
/*!40000 ALTER TABLE `shipments` DISABLE KEYS */;
INSERT INTO `shipments` VALUES (1,1,'TRACK-123456','CJPacket','shipped','2026-07-27 03:18:41','2026-07-27 03:18:41');
/*!40000 ALTER TABLE `shipments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sub_categories`
--

DROP TABLE IF EXISTS `sub_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sub_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sub_categories_slug_unique` (`slug`),
  KEY `sub_categories_category_id_foreign` (`category_id`),
  CONSTRAINT `sub_categories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_categories`
--

LOCK TABLES `sub_categories` WRITE;
/*!40000 ALTER TABLE `sub_categories` DISABLE KEYS */;
INSERT INTO `sub_categories` VALUES (1,1,'Test Subcategory','test-subcategory','Test sub','active','2026-07-25 05:45:46','2026-07-25 05:45:46'),(2,4,'Mobile Phones','mobile-phones',NULL,'active','2026-07-25 07:31:30','2026-07-25 07:31:30');
/*!40000 ALTER TABLE `sub_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL DEFAULT 1,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_mobile_unique` (`mobile`),
  KEY `users_email_index` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'Admin','User','admin@atozgadgets.com','1234567890','$2y$10$Va/Idtisby8xxUiv8k5P/ufMX10tXy/lg5kEyPPYcxDnSktQui0Km',NULL,1,NULL,NULL,'2026-07-25 04:37:19','2026-07-25 04:37:19'),(3,3,'<script>alert(\"xss\")</script>','User','hacker@example.com','0987654321','$2y$04$5KUqiLSZ48h174voJCJceOsBYIcTFPW4ZeTsY4z1pCdMuU9ilgOAS',NULL,1,NULL,NULL,'2026-07-25 05:38:47','2026-07-25 05:38:47'),(9,3,'Test','User','1784977926_user@example.com','7854695710','$2y$04$esG/wMzxKtkyYIG1y.yfJuCeq.Xb6SbGroZ1fjAO1/GoN2KZAJBnK',NULL,1,NULL,NULL,'2026-07-25 05:42:06','2026-07-25 05:42:06'),(10,3,'<script>alert(\"xss\")</script>','User','1784977926_hacker@example.com','9504975119','$2y$04$V9kwxXEDTnUwBnafNXlK8O3oKkyBgR4a/dkY3.5ftXpqF/YH8ZBuO',NULL,1,NULL,NULL,'2026-07-25 05:42:06','2026-07-25 05:42:06'),(11,3,'Test','User','1784977983_user@example.com','3961966052','$2y$04$J8wIQZz/xhQoeCwUqVDjBugp/OV78v4Tx6R6Zg78ptVBgxqbmxKhy',NULL,1,NULL,NULL,'2026-07-25 05:43:03','2026-07-25 05:43:03'),(12,3,'<script>alert(\"xss\")</script>','User','1784977984_hacker@example.com','3828691652','$2y$04$ruIm6lGb/cGTla2ximT3c.gdevL3zAMhJhgqScFRep6deWf6dslX6',NULL,1,NULL,NULL,'2026-07-25 05:43:04','2026-07-25 05:43:04'),(13,3,'Test','User','1784977997_user@example.com','2293872654','$2y$04$gNmCvllbG7Bv77PZ8TfC8OzLxGstQqivbo.nvyKG0l5wA5J0FFP7C',NULL,1,NULL,NULL,'2026-07-25 05:43:17','2026-07-25 05:43:17'),(14,3,'<script>alert(\"xss\")</script>','User','1784977997_hacker@example.com','8923870949','$2y$04$2MNCsKoQUjkEhiQoRXDyc.4CieUm893tJXXz5rseMMmFjbeTvBr2u',NULL,1,NULL,NULL,'2026-07-25 05:43:17','2026-07-25 05:43:17'),(15,3,'Test','User','1784984490_user@example.com','7897358192','$2y$04$85oxkm9TfU8k4NOLTN.v/uzyZSuOLNRJrh8PFut8fcfriPiirv3wy',NULL,1,NULL,NULL,'2026-07-25 07:31:30','2026-07-25 07:31:30'),(16,3,'<script>alert(\"xss\")</script>','User','1784984490_hacker@example.com','4484271673','$2y$04$RjY9QONX/hyOdjE2eYCaGuCI3yjLyn8t8J26.kNq3IqhrxJwM2LWS',NULL,1,NULL,NULL,'2026-07-25 07:31:30','2026-07-25 07:31:30'),(17,1,'Admin',NULL,'admin@example.com','+199999999','$2y$04$Nw3hQ6PeSFWgAi6bBQZx8u13FgYmGEYIH.wkgkbvRb63HGiAXiTJy',NULL,1,NULL,NULL,'2026-07-25 07:31:30','2026-07-25 07:31:30'),(18,1,'John','Doe','john.doe@example.com','+1234567890','$2y$04$05pG4W3nuI34TVs70dBk8u2SlRAwKID6Jffr60Ome6EXibDRxChtu','profile.jpg',1,NULL,NULL,'2026-07-25 07:31:31','2026-07-27 03:18:43'),(19,3,'Test','User','1784984504_user@example.com','5217026899','$2y$04$rDtWYL3AdzL5R8JUF0tsJuKZ5frTBpsJnd48smA24ak/kfyurcOgm',NULL,1,NULL,NULL,'2026-07-25 07:31:44','2026-07-25 07:31:44'),(20,3,'<script>alert(\"xss\")</script>','User','1784984504_hacker@example.com','1753494069','$2y$04$ic0MNr28psXOB3/rF4JHIOFBMwzUF37Qkkvd4J//w.3erZXx/QBuO',NULL,1,NULL,NULL,'2026-07-25 07:31:44','2026-07-25 07:31:44'),(23,3,'Test','User','1784984692_user@example.com','4442968102','$2y$04$pfO0q3NT37hn9/CFAG2u6eoXe0X8G2x0Y3Z.z4azL7ZlD.gtK5RR.',NULL,1,NULL,NULL,'2026-07-25 07:34:52','2026-07-25 07:34:52'),(24,3,'<script>alert(\"xss\")</script>','User','1784984692_hacker@example.com','7681854853','$2y$04$KSUD4KwLxLX15rH/IMfB8O6Gtrh0bJHP6aXHKzJsnEWTwzN5py5Y6',NULL,1,NULL,NULL,'2026-07-25 07:34:52','2026-07-25 07:34:52'),(25,2,'Gus','Kassulke','blair08@example.org','617.784.5622','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,1,NULL,NULL,'2026-07-25 07:34:52','2026-07-25 07:34:52'),(27,3,'Test','User','1784984726_user@example.com','5302960889','$2y$04$S/tiqOFLYl6jjsP664ubSe6qCPJ429LlEIoiDtx2ROkg60gvkSmLG',NULL,1,NULL,NULL,'2026-07-25 07:35:26','2026-07-25 07:35:26'),(28,3,'<script>alert(\"xss\")</script>','User','1784984726_hacker@example.com','9060384339','$2y$04$omxPazlxrqH4X0PQWNswl.XIO4wxLua.UBbxe5RspsXj5H.qBu9wa',NULL,1,NULL,NULL,'2026-07-25 07:35:26','2026-07-25 07:35:26'),(29,2,'Baylee','Johnson','metz.rogelio@example.net','+1.410.875.9018','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,1,NULL,NULL,'2026-07-25 07:35:26','2026-07-25 07:35:26'),(30,3,'Test','User','1784984746_user@example.com','2978637672','$2y$04$utZdLq//Ta71iihiMSjlbeb8z6wlqJvHgLjK7MApup1mfjie5OuVK',NULL,1,NULL,NULL,'2026-07-25 07:35:46','2026-07-25 07:35:46'),(31,3,'<script>alert(\"xss\")</script>','User','1784984747_hacker@example.com','6094139525','$2y$04$x1cQrl29Cs7NWmJgJniWHOVCiVE6fA9Tfxw0Oz4vI8gcH1EVw4uN6',NULL,1,NULL,NULL,'2026-07-25 07:35:47','2026-07-25 07:35:47'),(32,2,'Eulah','Volkman','alejandra32@example.net','+1 (539) 995-8769','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,1,NULL,NULL,'2026-07-25 07:35:47','2026-07-25 07:35:47'),(33,3,'Test','User','1784984806_user@example.com','7980124167','$2y$04$L8/stXfYi91YlHU6dvJZ9uCNgxOPjVIT7yeurXuhnFJXzmhDxHhaS',NULL,1,NULL,NULL,'2026-07-25 07:36:46','2026-07-25 07:36:46'),(34,3,'<script>alert(\"xss\")</script>','User','1784984806_hacker@example.com','1981080255','$2y$04$nBILG9lN0PRS6M.uXYIFl.OwYtQF63q/9P2tl8Nwj6FsN4l.zThia',NULL,1,NULL,NULL,'2026-07-25 07:36:46','2026-07-25 07:36:46'),(35,2,'Ora','Von','gaetano68@example.com','1-505-488-5653','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,1,NULL,NULL,'2026-07-25 07:36:46','2026-07-25 07:36:46'),(36,3,'Test','User','1784984842_user@example.com','2901369485','$2y$04$GhdK8GI8S3nzXYyeMzp.JeN7yTPQYpXV1gmfJJGj.blMZSIOf385O',NULL,1,NULL,NULL,'2026-07-25 07:37:22','2026-07-25 07:37:22'),(37,3,'<script>alert(\"xss\")</script>','User','1784984842_hacker@example.com','4826185794','$2y$04$pySkd/sN/Wheu3O.EjVweOo.yL0Mz7XzR2m3LSK17salH67VsX0T6',NULL,1,NULL,NULL,'2026-07-25 07:37:22','2026-07-25 07:37:22'),(38,2,'Bettie','Daugherty','guy83@example.com','(619) 836-9329','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,1,NULL,NULL,'2026-07-25 07:37:22','2026-07-25 07:37:22'),(39,3,'Test','User','1784984875_user@example.com','6166578273','$2y$04$apgDgaxXlbFCURF7.wCq9.Q2Q0hJnG4UYY8ctF0dTbEScwcFQWBRu',NULL,1,NULL,NULL,'2026-07-25 07:37:55','2026-07-25 07:37:55'),(40,3,'<script>alert(\"xss\")</script>','User','1784984875_hacker@example.com','4334026025','$2y$04$XrzQk2th6P6CRFZMhd/JTOaY.QKVZBY9LREv1J1zPPKuLvZSSMnuG',NULL,1,NULL,NULL,'2026-07-25 07:37:55','2026-07-25 07:37:55'),(41,2,'Aurelio','Gerlach','evelyn.jerde@example.org','+1-341-430-7839','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,1,NULL,NULL,'2026-07-25 07:37:55','2026-07-25 07:37:55'),(42,3,'Test','User','1785134960_user@example.com','9887668851','$2y$04$k57qinZ3RfbJc.vhvEtouO8/mBQSLfXwc92kP96SmNtAhZXACe.dG',NULL,1,NULL,NULL,'2026-07-27 01:19:20','2026-07-27 01:19:20'),(43,3,'<script>alert(\"xss\")</script>','User','1785134960_hacker@example.com','6133495592','$2y$04$HHT/tQDvEDT8nblHnZlvKu5ogeVNw1WqWc6K/eqMe8eXjquwJJ8k.',NULL,1,NULL,NULL,'2026-07-27 01:19:20','2026-07-27 01:19:20'),(44,2,'Ardella','Leffler','wherman@example.org','719.207.4762','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,1,NULL,NULL,'2026-07-27 01:19:21','2026-07-27 01:19:21'),(45,3,'Test','User','1785134979_user@example.com','9793144776','$2y$04$YOjqu0WQuyrtodOxIcUACeHllBPSbBOiqqqvhdf8fF9FhXvSTLXPu',NULL,1,NULL,NULL,'2026-07-27 01:19:39','2026-07-27 01:19:39'),(46,3,'<script>alert(\"xss\")</script>','User','1785134979_hacker@example.com','5405032118','$2y$04$0xtkfgzKdUfn0Lus222Xsugn9hoMsfv.faI1xZAM888OwEPKEYLJW',NULL,1,NULL,NULL,'2026-07-27 01:19:39','2026-07-27 01:19:39'),(47,2,'Maryse','Schuppe','fschinner@example.com','630.317.7244','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,1,NULL,NULL,'2026-07-27 01:19:40','2026-07-27 01:19:40'),(48,3,'Test','User','1785135036_user@example.com','5660781564','$2y$04$iv4gDAyToJ.RyHbjxw.rfuftsc8coqLVyq9QfIjXbYCk0cmi4i3YO',NULL,1,NULL,NULL,'2026-07-27 01:20:36','2026-07-27 01:20:36'),(49,3,'<script>alert(\"xss\")</script>','User','1785135036_hacker@example.com','4254633210','$2y$04$5.tUc.ghxgfh44w5mv.fDuP8.4mhSMkP16G2j.Fk7HTNXX2.IuDza',NULL,1,NULL,NULL,'2026-07-27 01:20:36','2026-07-27 01:20:36'),(50,2,'Jarod','Ebert','mwilliamson@example.net','+1 (857) 717-5402','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,1,NULL,NULL,'2026-07-27 01:20:37','2026-07-27 01:20:37'),(51,3,'Test','User','1785135099_user@example.com','6664382271','$2y$04$11bo9WJGOGHQ9967boT/tOjDbtZcZcJMIxZ2xDjqCM4wBPVku9cH6',NULL,1,NULL,NULL,'2026-07-27 01:21:39','2026-07-27 01:21:39'),(52,3,'<script>alert(\"xss\")</script>','User','1785135100_hacker@example.com','3975673091','$2y$04$LesE64UyR0oUW0jkusTV1uMgHJ2iOYLK7VR.vtPriR20g9MUNyRJy',NULL,1,NULL,NULL,'2026-07-27 01:21:40','2026-07-27 01:21:40'),(53,2,'Lloyd','Little','irma.walsh@example.org','+1 (229) 539-2383','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,1,NULL,NULL,'2026-07-27 01:21:40','2026-07-27 01:21:40'),(54,3,'Test','User','1785135544_user@example.com','7959466234','$2y$04$lo8yHdQxAtZe3oL9aiI8GOndGI5dIWBGKLuUc.73maqQf/CkcYXDW',NULL,1,NULL,NULL,'2026-07-27 01:29:04','2026-07-27 01:29:04'),(55,3,'<script>alert(\"xss\")</script>','User','1785135545_hacker@example.com','2349381188','$2y$04$xttkYpcUsJVNFyHvzauqTONolGDVX1MIBhlaCYGM2oN1MoCMqVNUG',NULL,1,NULL,NULL,'2026-07-27 01:29:05','2026-07-27 01:29:05'),(56,2,'Bruce','Dietrich','reed56@example.org','(959) 978-7072','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,1,NULL,NULL,'2026-07-27 01:29:05','2026-07-27 01:29:05'),(57,3,'Test','User','1785135815_user@example.com','8006226297','$2y$04$UjJR22oxfdNyBWFEuTy2Pe3Mm4ZHrvlMsloLewXzYz2NKLMk59mFy',NULL,1,NULL,NULL,'2026-07-27 01:33:35','2026-07-27 01:33:35'),(58,3,'<script>alert(\"xss\")</script>','User','1785135816_hacker@example.com','4749653161','$2y$04$b1099AlK3gwlLOY.7MqlwevnLkJ87sFjc/QGza4aUCDKivVCKVYqa',NULL,1,NULL,NULL,'2026-07-27 01:33:36','2026-07-27 01:33:36'),(59,2,'Aimee','Franecki','jwolf@example.net','1-830-964-9375','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,1,NULL,NULL,'2026-07-27 01:33:36','2026-07-27 01:33:36'),(60,3,'Test','User','1785135946_user@example.com','1769339817','$2y$04$fvsqCN9Ay9G2xL4DSqc5oOEsuDdv4utgZRTPRtpbt2IxFxVXuPop.',NULL,1,NULL,NULL,'2026-07-27 01:35:46','2026-07-27 01:35:46'),(61,3,'<script>alert(\"xss\")</script>','User','1785135946_hacker@example.com','8860849981','$2y$04$ilZHhXDDnosJroraPcks4O.Y.Q63NPkrG82VYMJxX.r8g.JrdyQ.G',NULL,1,NULL,NULL,'2026-07-27 01:35:46','2026-07-27 01:35:46'),(62,2,'Alize','Altenwerth','marisol76@example.org','724-294-1277','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,1,NULL,NULL,'2026-07-27 01:35:47','2026-07-27 01:35:47'),(63,3,'Test','User','1785136964_user@example.com','2436831358','$2y$04$Iux3DEcmq.NaoXE6wPAITuaEFtTKUj0hQNKCR/.jKWRwhClGXAhDq',NULL,1,NULL,NULL,'2026-07-27 01:52:44','2026-07-27 01:52:44'),(64,3,'<script>alert(\"xss\")</script>','User','1785136964_hacker@example.com','6189595692','$2y$04$vTncOxQmLtOd02EbvSJLsenOsgPjdSaQXJ7jTwZjPnSBpT0L.4fz2',NULL,1,NULL,NULL,'2026-07-27 01:52:44','2026-07-27 01:52:44'),(65,2,'Maryse','Howell','soledad53@example.org','602-303-8106','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,1,NULL,NULL,'2026-07-27 01:52:45','2026-07-27 01:52:45'),(66,3,'Test','User','1785136989_user@example.com','7800860755','$2y$04$19YrY9l/yeH8UgqvgrtGl.OAPLrtel8gN5Jwv1pmuX31nr6KhnSH6',NULL,1,NULL,NULL,'2026-07-27 01:53:09','2026-07-27 01:53:09'),(67,3,'<script>alert(\"xss\")</script>','User','1785136989_hacker@example.com','5768081047','$2y$04$BS0K6cXhlN88IrGN4ywpdul4g9Zxy42q0TKIEVVpph1Wz1BNv18Z6',NULL,1,NULL,NULL,'2026-07-27 01:53:09','2026-07-27 01:53:09'),(68,2,'Kaylee','Wintheiser','nathan61@example.net','+18607864728','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,1,NULL,NULL,'2026-07-27 01:53:10','2026-07-27 01:53:10'),(69,3,'Test','User','1785137008_user@example.com','3728448997','$2y$04$2zInK87thQ8sQYcKfz6BxuF2cJNF/bxvZMP4tmgpKuJR2MVt1g.Uu',NULL,1,NULL,NULL,'2026-07-27 01:53:28','2026-07-27 01:53:28'),(70,3,'<script>alert(\"xss\")</script>','User','1785137008_hacker@example.com','4372094423','$2y$04$/ln6mc3My.o6bZxTgeajYum/a2JjypQlLl4GAZA78YTVb1rfOz/S.',NULL,1,NULL,NULL,'2026-07-27 01:53:28','2026-07-27 01:53:28'),(71,2,'Aglae','Paucek','brakus.dallas@example.com','341.668.8843','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,1,NULL,NULL,'2026-07-27 01:53:29','2026-07-27 01:53:29'),(72,3,'Test','User','1785140672_user@example.com','8969527733','$2y$04$WMLUX3noAMS.xsymmJdLR.Rqjr0sG84nI0eVwYbMjUlYTZjYHJ/.i',NULL,1,NULL,NULL,'2026-07-27 02:54:32','2026-07-27 02:54:32'),(73,3,'<script>alert(\"xss\")</script>','User','1785140672_hacker@example.com','2088395227','$2y$04$1AWVLE4hnUIKf7bkYy0tnOvEkvh5f1557lb275iHErfE1fWTPLFSO',NULL,1,NULL,NULL,'2026-07-27 02:54:32','2026-07-27 02:54:32'),(74,2,'Dalton','Toy','mikayla73@example.net','731.440.1262','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,1,NULL,NULL,'2026-07-27 02:54:33','2026-07-27 02:54:33'),(75,3,'Test','User','1785141078_user@example.com','7724745651','$2y$04$XTNKTYPeax4zbgBmHx/uTuzFIhGPPJVZy/kW1.Gs.W4o9W3VAAS4C',NULL,1,NULL,NULL,'2026-07-27 03:01:18','2026-07-27 03:01:18'),(76,3,'<script>alert(\"xss\")</script>','User','1785141078_hacker@example.com','4865196336','$2y$04$xLIIf/bnbuU6j1PHgbuK0eH4Xs5QiQpdW3qqsGYkCtwXZpkVLF.l2',NULL,1,NULL,NULL,'2026-07-27 03:01:18','2026-07-27 03:01:18'),(77,2,'Jett','Lowe','rohan.emelie@example.org','(640) 432-0864','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,1,NULL,NULL,'2026-07-27 03:01:19','2026-07-27 03:01:19'),(78,3,'Test','User','1785142119_user@example.com','7180594140','$2y$04$Rt9tkPxbJXSCAgUYIW.H3.vSzJPkcQpzVFWt.zgpDOMBi.XEHRQVW',NULL,1,NULL,NULL,'2026-07-27 03:18:39','2026-07-27 03:18:39'),(79,3,'<script>alert(\"xss\")</script>','User','1785142119_hacker@example.com','2292818488','$2y$04$OoraQPJr1YXvCU5frgYUPO/RoLLeM03ctlyOvRK3HbMkv2PzkVVQ.',NULL,1,NULL,NULL,'2026-07-27 03:18:39','2026-07-27 03:18:39'),(80,2,'Allan','Wintheiser','lance.kemmer@example.org','+1-347-661-9443','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,1,NULL,NULL,'2026-07-27 03:18:41','2026-07-27 03:18:41');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlists`
--

DROP TABLE IF EXISTS `wishlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wishlists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wishlists_user_id_foreign` (`user_id`),
  KEY `wishlists_product_id_foreign` (`product_id`),
  CONSTRAINT `wishlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlists`
--

LOCK TABLES `wishlists` WRITE;
/*!40000 ALTER TABLE `wishlists` DISABLE KEYS */;
/*!40000 ALTER TABLE `wishlists` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-27 14:23:30
