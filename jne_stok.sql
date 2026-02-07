/*
 Navicat Premium Dump SQL

 Source Server         : MySql Local
 Source Server Type    : MySQL
 Source Server Version : 90300 (9.3.0)
 Source Host           : localhost:33066
 Source Schema         : jne_stok

 Target Server Type    : MySQL
 Target Server Version : 90300 (9.3.0)
 File Encoding         : 65001

 Date: 07/02/2026 00:42:01
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`) USING BTREE,
  INDEX `cache_expiration_index`(`expiration` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache
-- ----------------------------

-- ----------------------------
-- Table structure for cache_locks
-- ----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`) USING BTREE,
  INDEX `cache_locks_expiration_index`(`expiration` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache_locks
-- ----------------------------

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `failed_jobs_uuid_unique`(`uuid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for job_batches
-- ----------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `cancelled_at` int NULL DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of job_batches
-- ----------------------------

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED NULL DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `jobs_queue_index`(`queue` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jobs
-- ----------------------------

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` VALUES (4, '2024_01_01_000001_create_stock_tables', 1);

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens`  (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of password_reset_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sessions_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `sessions_last_activity_index`(`last_activity` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sessions
-- ----------------------------
INSERT INTO `sessions` VALUES ('ubQTZb3i7r0eBQjl9DJSMIbKdy4KE9jxth7EZLx0', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSHMzR2FXNVlqd2FWNkxrWE5MMTF1OVMyN1pQdEEzYWFaV3pMSWNxMiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjIxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Njt9', 1770398371);

-- ----------------------------
-- Table structure for stok_barang
-- ----------------------------
DROP TABLE IF EXISTS `stok_barang`;
CREATE TABLE `stok_barang`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_barang_satuan` bigint UNSIGNED NOT NULL,
  `qty_barang` int NOT NULL DEFAULT 0,
  `stok_awal` int NOT NULL DEFAULT 0,
  `harga_barang` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `warning_stok` int NOT NULL DEFAULT 10,
  `internal` tinyint(1) NOT NULL DEFAULT 0,
  `agen` tinyint(1) NOT NULL DEFAULT 0,
  `subagen` tinyint(1) NOT NULL DEFAULT 0,
  `corporate` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `stok_barang_kode_barang_unique`(`kode_barang` ASC) USING BTREE,
  INDEX `stok_barang_id_barang_satuan_foreign`(`id_barang_satuan` ASC) USING BTREE,
  CONSTRAINT `stok_barang_id_barang_satuan_foreign` FOREIGN KEY (`id_barang_satuan`) REFERENCES `stok_barang_satuan` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_barang
-- ----------------------------
INSERT INTO `stok_barang` VALUES (1, 'ATK-001', 'Kertas HVS A4 70gsm', 3, 66, 0, 0.00, 10, 1, 0, 0, 0, '2026-02-06 01:49:42', '2026-02-06 02:47:06', NULL);
INSERT INTO `stok_barang` VALUES (2, 'ATK-002', 'Pulpen Hitam Standard', 6, 18, 0, 35000.00, 10, 1, 0, 0, 0, '2026-02-06 01:49:42', '2026-02-06 02:28:08', NULL);
INSERT INTO `stok_barang` VALUES (3, 'ATK-003', 'Spidol Whiteboard', 6, 8, 0, 42000.00, 10, 1, 0, 0, 0, '2026-02-06 01:49:42', '2026-02-06 01:49:42', NULL);
INSERT INTO `stok_barang` VALUES (4, 'ATK-004', 'Binder Clips Besar', 2, 15, 0, 0.00, 10, 1, 0, 0, 0, '2026-02-06 01:49:42', '2026-02-06 02:24:47', NULL);
INSERT INTO `stok_barang` VALUES (5, 'ATK-005', 'Stapler HD-50', 1, 15, 0, 85000.00, 10, 1, 0, 0, 0, '2026-02-06 01:49:42', '2026-02-06 01:49:42', NULL);
INSERT INTO `stok_barang` VALUES (6, 'ATK-006', 'Isi Staples No.10', 2, 25, 0, 8000.00, 10, 1, 0, 0, 0, '2026-02-06 01:49:42', '2026-02-06 01:49:42', NULL);
INSERT INTO `stok_barang` VALUES (7, 'ATK-007', 'Map Plastik Kancing', 4, 12, 0, 36000.00, 10, 1, 0, 0, 0, '2026-02-06 01:49:42', '2026-02-06 01:49:42', NULL);
INSERT INTO `stok_barang` VALUES (8, 'ATK-008', 'Pensil 2B Faber', 4, 5, 0, 30000.00, 10, 1, 0, 0, 0, '2026-02-06 01:49:42', '2026-02-06 01:49:42', NULL);
INSERT INTO `stok_barang` VALUES (9, 'UM-001', 'Tinta Printer Epson Black', 8, 6, 0, 75000.00, 10, 0, 1, 0, 0, '2026-02-06 01:49:42', '2026-02-06 01:49:42', NULL);
INSERT INTO `stok_barang` VALUES (10, 'UM-002', 'Toner HP 35A', 1, 2, 0, 350000.00, 10, 0, 1, 0, 0, '2026-02-06 01:49:42', '2026-02-06 01:49:42', NULL);
INSERT INTO `stok_barang` VALUES (11, 'UM-003', 'Baterai AA Alkaline', 6, 10, 0, 12000.00, 10, 0, 1, 0, 0, '2026-02-06 01:49:42', '2026-02-06 02:30:11', NULL);
INSERT INTO `stok_barang` VALUES (12, 'UM-004', 'Lampu LED 12W', 1, 4, 0, 25000.00, 10, 0, 1, 0, 0, '2026-02-06 01:49:42', '2026-02-06 01:49:42', NULL);
INSERT INTO `stok_barang` VALUES (13, 'asfdaf', 'fffff', 8, 0, 0, 0.00, 10, 1, 1, 1, 1, '2026-02-06 02:22:13', '2026-02-06 02:22:26', '2026-02-06 02:22:26');

-- ----------------------------
-- Table structure for stok_barang_harga
-- ----------------------------
DROP TABLE IF EXISTS `stok_barang_harga`;
CREATE TABLE `stok_barang_harga`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_barang` bigint UNSIGNED NOT NULL,
  `id_barang_masuk` bigint UNSIGNED NULL DEFAULT NULL,
  `id_barang_keluar` bigint UNSIGNED NULL DEFAULT NULL,
  `qty_barang` int NOT NULL,
  `min_barang` int NOT NULL DEFAULT 0,
  `id_ref_min_barang` bigint UNSIGNED NULL DEFAULT NULL,
  `harga_barang` decimal(15, 2) NOT NULL,
  `harga_barang_invoice` decimal(15, 2) NULL DEFAULT NULL,
  `tanggal_barang` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `stok_barang_harga_id_barang_foreign`(`id_barang` ASC) USING BTREE,
  INDEX `stok_barang_harga_id_barang_masuk_foreign`(`id_barang_masuk` ASC) USING BTREE,
  INDEX `stok_barang_harga_id_barang_keluar_foreign`(`id_barang_keluar` ASC) USING BTREE,
  CONSTRAINT `stok_barang_harga_id_barang_foreign` FOREIGN KEY (`id_barang`) REFERENCES `stok_barang` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `stok_barang_harga_id_barang_keluar_foreign` FOREIGN KEY (`id_barang_keluar`) REFERENCES `stok_barang_keluar` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `stok_barang_harga_id_barang_masuk_foreign` FOREIGN KEY (`id_barang_masuk`) REFERENCES `stok_barang_masuk` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_barang_harga
-- ----------------------------
INSERT INTO `stok_barang_harga` VALUES (1, 1, 1, NULL, 50, 6, 2, 45000.00, NULL, '2026-02-01 01:49:42', '2026-02-06 01:49:42', '2026-02-06 02:47:06');
INSERT INTO `stok_barang_harga` VALUES (2, 2, 1, NULL, 20, 2, 1, 35000.00, NULL, '2026-02-01 01:49:42', '2026-02-06 01:49:42', '2026-02-06 02:28:08');
INSERT INTO `stok_barang_harga` VALUES (3, 3, 1, NULL, 8, 0, NULL, 42000.00, NULL, '2026-02-01 01:49:42', '2026-02-06 01:49:42', '2026-02-06 01:49:42');
INSERT INTO `stok_barang_harga` VALUES (4, 4, 1, NULL, 3, 0, NULL, 25000.00, NULL, '2026-02-01 01:49:42', '2026-02-06 01:49:42', '2026-02-06 01:49:42');
INSERT INTO `stok_barang_harga` VALUES (5, 5, 1, NULL, 15, 0, NULL, 85000.00, NULL, '2026-02-01 01:49:42', '2026-02-06 01:49:42', '2026-02-06 01:49:42');
INSERT INTO `stok_barang_harga` VALUES (6, 6, 1, NULL, 25, 0, NULL, 8000.00, NULL, '2026-02-01 01:49:42', '2026-02-06 01:49:42', '2026-02-06 01:49:42');
INSERT INTO `stok_barang_harga` VALUES (7, 7, 1, NULL, 12, 0, NULL, 36000.00, NULL, '2026-02-01 01:49:42', '2026-02-06 01:49:42', '2026-02-06 01:49:42');
INSERT INTO `stok_barang_harga` VALUES (8, 8, 1, NULL, 5, 0, NULL, 30000.00, NULL, '2026-02-01 01:49:42', '2026-02-06 01:49:42', '2026-02-06 01:49:42');
INSERT INTO `stok_barang_harga` VALUES (9, 9, 1, NULL, 6, 0, NULL, 75000.00, NULL, '2026-02-01 01:49:42', '2026-02-06 01:49:42', '2026-02-06 01:49:42');
INSERT INTO `stok_barang_harga` VALUES (10, 10, 1, NULL, 2, 0, NULL, 350000.00, NULL, '2026-02-01 01:49:42', '2026-02-06 01:49:42', '2026-02-06 01:49:42');
INSERT INTO `stok_barang_harga` VALUES (11, 12, 1, NULL, 4, 0, NULL, 25000.00, NULL, '2026-02-01 01:49:42', '2026-02-06 01:49:42', '2026-02-06 01:49:42');
INSERT INTO `stok_barang_harga` VALUES (12, 4, 2, NULL, 12, 0, NULL, 0.00, NULL, '2026-02-06 02:24:00', '2026-02-06 02:24:47', '2026-02-06 02:24:47');
INSERT INTO `stok_barang_harga` VALUES (13, 1, 2, NULL, 22, 0, NULL, 0.00, NULL, '2026-02-06 02:24:00', '2026-02-06 02:24:47', '2026-02-06 02:24:47');
INSERT INTO `stok_barang_harga` VALUES (14, 11, 3, NULL, 10, 0, NULL, 12000.00, NULL, '2026-02-06 02:29:00', '2026-02-06 02:30:11', '2026-02-06 02:30:11');

-- ----------------------------
-- Table structure for stok_barang_keluar
-- ----------------------------
DROP TABLE IF EXISTS `stok_barang_keluar`;
CREATE TABLE `stok_barang_keluar`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `no_barang_keluar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` datetime NOT NULL,
  `id_divisi` bigint UNSIGNED NULL DEFAULT NULL,
  `id_kategori` bigint UNSIGNED NULL DEFAULT NULL,
  `id_agen` bigint UNSIGNED NULL DEFAULT NULL,
  `id_order` bigint UNSIGNED NULL DEFAULT NULL,
  `nama_user_request` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `distribusi_sales` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `stok_barang_keluar_no_barang_keluar_unique`(`no_barang_keluar` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_barang_keluar
-- ----------------------------
INSERT INTO `stok_barang_keluar` VALUES (1, 'NBK-0206-6022808', '2026-02-06 02:28:08', NULL, NULL, NULL, 1, 'Ahmad Rizki', NULL, 6, NULL, '2026-02-06 02:28:08', '2026-02-06 02:28:08', NULL);
INSERT INTO `stok_barang_keluar` VALUES (2, 'NBK-0206-6024706', '2026-02-06 02:46:00', NULL, NULL, NULL, NULL, 'HENDRIK HERMANSYAH', NULL, 6, NULL, '2026-02-06 02:47:06', '2026-02-06 02:47:06', NULL);

-- ----------------------------
-- Table structure for stok_barang_keluar_detail
-- ----------------------------
DROP TABLE IF EXISTS `stok_barang_keluar_detail`;
CREATE TABLE `stok_barang_keluar_detail`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_barang_keluar` bigint UNSIGNED NOT NULL,
  `id_barang` bigint UNSIGNED NOT NULL,
  `qty_barang` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `stok_barang_keluar_detail_id_barang_keluar_foreign`(`id_barang_keluar` ASC) USING BTREE,
  INDEX `stok_barang_keluar_detail_id_barang_foreign`(`id_barang` ASC) USING BTREE,
  CONSTRAINT `stok_barang_keluar_detail_id_barang_foreign` FOREIGN KEY (`id_barang`) REFERENCES `stok_barang` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `stok_barang_keluar_detail_id_barang_keluar_foreign` FOREIGN KEY (`id_barang_keluar`) REFERENCES `stok_barang_keluar` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_barang_keluar_detail
-- ----------------------------
INSERT INTO `stok_barang_keluar_detail` VALUES (1, 1, 1, 5, '2026-02-06 02:28:08', '2026-02-06 02:28:08');
INSERT INTO `stok_barang_keluar_detail` VALUES (2, 1, 2, 2, '2026-02-06 02:28:08', '2026-02-06 02:28:08');
INSERT INTO `stok_barang_keluar_detail` VALUES (3, 2, 1, 1, '2026-02-06 02:47:06', '2026-02-06 02:47:06');

-- ----------------------------
-- Table structure for stok_barang_masuk
-- ----------------------------
DROP TABLE IF EXISTS `stok_barang_masuk`;
CREATE TABLE `stok_barang_masuk`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `no_barang_masuk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` datetime NOT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `stok_barang_masuk_no_barang_masuk_unique`(`no_barang_masuk` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_barang_masuk
-- ----------------------------
INSERT INTO `stok_barang_masuk` VALUES (1, 'NBM-0206-001', '2026-02-01 01:49:42', 1, NULL, '2026-02-06 01:49:42', '2026-02-06 01:49:42', NULL);
INSERT INTO `stok_barang_masuk` VALUES (2, 'NBM-0206-6022447', '2026-02-06 02:24:00', 6, NULL, '2026-02-06 02:24:47', '2026-02-06 02:24:47', NULL);
INSERT INTO `stok_barang_masuk` VALUES (3, 'NBM-0206-6023011', '2026-02-06 02:29:00', 6, NULL, '2026-02-06 02:30:11', '2026-02-06 02:30:11', NULL);

-- ----------------------------
-- Table structure for stok_barang_masuk_detail
-- ----------------------------
DROP TABLE IF EXISTS `stok_barang_masuk_detail`;
CREATE TABLE `stok_barang_masuk_detail`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_barang_masuk` bigint UNSIGNED NOT NULL,
  `id_barang` bigint UNSIGNED NOT NULL,
  `id_supplier` bigint UNSIGNED NOT NULL,
  `qty_barang` int NOT NULL,
  `harga_barang` decimal(15, 2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `stok_barang_masuk_detail_id_barang_masuk_foreign`(`id_barang_masuk` ASC) USING BTREE,
  INDEX `stok_barang_masuk_detail_id_barang_foreign`(`id_barang` ASC) USING BTREE,
  INDEX `stok_barang_masuk_detail_id_supplier_foreign`(`id_supplier` ASC) USING BTREE,
  CONSTRAINT `stok_barang_masuk_detail_id_barang_foreign` FOREIGN KEY (`id_barang`) REFERENCES `stok_barang` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `stok_barang_masuk_detail_id_barang_masuk_foreign` FOREIGN KEY (`id_barang_masuk`) REFERENCES `stok_barang_masuk` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `stok_barang_masuk_detail_id_supplier_foreign` FOREIGN KEY (`id_supplier`) REFERENCES `stok_supplier` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_barang_masuk_detail
-- ----------------------------
INSERT INTO `stok_barang_masuk_detail` VALUES (1, 2, 4, 2, 12, 0.00, '2026-02-06 02:24:47', '2026-02-06 02:24:47');
INSERT INTO `stok_barang_masuk_detail` VALUES (2, 2, 1, 5, 22, 0.00, '2026-02-06 02:24:47', '2026-02-06 02:24:47');
INSERT INTO `stok_barang_masuk_detail` VALUES (3, 3, 11, 5, 10, 12000.00, '2026-02-06 02:30:11', '2026-02-06 02:30:11');

-- ----------------------------
-- Table structure for stok_barang_satuan
-- ----------------------------
DROP TABLE IF EXISTS `stok_barang_satuan`;
CREATE TABLE `stok_barang_satuan`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_satuan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_barang_satuan
-- ----------------------------
INSERT INTO `stok_barang_satuan` VALUES (1, 'Pcs', '2026-02-06 01:49:42', '2026-02-06 01:49:42');
INSERT INTO `stok_barang_satuan` VALUES (2, 'Box', '2026-02-06 01:49:42', '2026-02-06 01:49:42');
INSERT INTO `stok_barang_satuan` VALUES (3, 'Rim', '2026-02-06 01:49:42', '2026-02-06 01:49:42');
INSERT INTO `stok_barang_satuan` VALUES (4, 'Lusin', '2026-02-06 01:49:42', '2026-02-06 01:49:42');
INSERT INTO `stok_barang_satuan` VALUES (5, 'Set', '2026-02-06 01:49:42', '2026-02-06 01:49:42');
INSERT INTO `stok_barang_satuan` VALUES (6, 'Pak', '2026-02-06 01:49:42', '2026-02-06 01:49:42');
INSERT INTO `stok_barang_satuan` VALUES (7, 'Unit', '2026-02-06 01:49:42', '2026-02-06 01:49:42');
INSERT INTO `stok_barang_satuan` VALUES (8, 'Botol', '2026-02-06 01:49:42', '2026-02-06 01:49:42');

-- ----------------------------
-- Table structure for stok_invoice
-- ----------------------------
DROP TABLE IF EXISTS `stok_invoice`;
CREATE TABLE `stok_invoice`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `no_invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_barang_keluar` bigint UNSIGNED NOT NULL,
  `tanggal_invoice` datetime NOT NULL,
  `status` enum('unpaid','paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `stok_invoice_no_invoice_unique`(`no_invoice` ASC) USING BTREE,
  INDEX `stok_invoice_id_barang_keluar_foreign`(`id_barang_keluar` ASC) USING BTREE,
  CONSTRAINT `stok_invoice_id_barang_keluar_foreign` FOREIGN KEY (`id_barang_keluar`) REFERENCES `stok_barang_keluar` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_invoice
-- ----------------------------

-- ----------------------------
-- Table structure for stok_order
-- ----------------------------
DROP TABLE IF EXISTS `stok_order`;
CREATE TABLE `stok_order`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `no_order` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` datetime NOT NULL,
  `id_divisi` bigint UNSIGNED NULL DEFAULT NULL,
  `id_kategori` bigint UNSIGNED NULL DEFAULT NULL,
  `nama_user_request` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `hp_user_request` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `approved_by` bigint UNSIGNED NULL DEFAULT NULL,
  `rejected_by` bigint UNSIGNED NULL DEFAULT NULL,
  `tanggal_update` datetime NULL DEFAULT NULL,
  `tanggal_approve` datetime NULL DEFAULT NULL,
  `tanggal_reject` datetime NULL DEFAULT NULL,
  `rejected_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `status` enum('menunggu','diproses','selesai','ditolak') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `stok_order_no_order_unique`(`no_order` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_order
-- ----------------------------
INSERT INTO `stok_order` VALUES (1, 'ORD-260206-001', '2026-02-04 01:49:42', 1, NULL, 'Ahmad Rizki', NULL, 1, NULL, 6, NULL, NULL, '2026-02-06 02:28:08', NULL, NULL, 'selesai', '2026-02-06 01:49:42', '2026-02-06 02:28:08', NULL);
INSERT INTO `stok_order` VALUES (2, 'ORD-260206-002', '2026-02-05 01:49:42', 2, NULL, 'Siti Nurhaliza', NULL, 1, NULL, NULL, 6, NULL, NULL, '2026-02-06 02:22:39', 'tidak layak', 'ditolak', '2026-02-06 01:49:42', '2026-02-06 02:22:39', NULL);

-- ----------------------------
-- Table structure for stok_order_detail
-- ----------------------------
DROP TABLE IF EXISTS `stok_order_detail`;
CREATE TABLE `stok_order_detail`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_order` bigint UNSIGNED NOT NULL,
  `id_barang` bigint UNSIGNED NOT NULL,
  `qty_barang` int NOT NULL,
  `qty_approved` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `stok_order_detail_id_order_foreign`(`id_order` ASC) USING BTREE,
  INDEX `stok_order_detail_id_barang_foreign`(`id_barang` ASC) USING BTREE,
  CONSTRAINT `stok_order_detail_id_barang_foreign` FOREIGN KEY (`id_barang`) REFERENCES `stok_barang` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `stok_order_detail_id_order_foreign` FOREIGN KEY (`id_order`) REFERENCES `stok_order` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_order_detail
-- ----------------------------
INSERT INTO `stok_order_detail` VALUES (1, 1, 1, 5, 5, '2026-02-06 01:49:42', '2026-02-06 02:28:08');
INSERT INTO `stok_order_detail` VALUES (2, 1, 2, 2, 2, '2026-02-06 01:49:42', '2026-02-06 02:28:08');
INSERT INTO `stok_order_detail` VALUES (3, 2, 5, 1, NULL, '2026-02-06 01:49:42', '2026-02-06 01:49:42');
INSERT INTO `stok_order_detail` VALUES (4, 2, 9, 3, NULL, '2026-02-06 01:49:42', '2026-02-06 01:49:42');

-- ----------------------------
-- Table structure for stok_supplier
-- ----------------------------
DROP TABLE IF EXISTS `stok_supplier`;
CREATE TABLE `stok_supplier`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_supplier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_supplier
-- ----------------------------
INSERT INTO `stok_supplier` VALUES (1, 'PT. Mitra Jaya Stationery', '2026-02-06 01:49:42', '2026-02-06 01:49:42', NULL);
INSERT INTO `stok_supplier` VALUES (2, 'CV. Berkah Office Supply', '2026-02-06 01:49:42', '2026-02-06 01:49:42', NULL);
INSERT INTO `stok_supplier` VALUES (3, 'Toko Maju Jaya ATK', '2026-02-06 01:49:42', '2026-02-06 01:49:42', NULL);
INSERT INTO `stok_supplier` VALUES (4, 'PT. Indo Alat Kantor', '2026-02-06 01:49:42', '2026-02-06 01:49:42', NULL);
INSERT INTO `stok_supplier` VALUES (5, 'CV. Sumber Makmur', '2026-02-06 01:49:42', '2026-02-06 01:49:42', NULL);

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
