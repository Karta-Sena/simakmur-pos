-- ============================================
-- FILE: simakmur_db.sql
-- Database: Kedai Sekar Makmur POS System
-- Last Updated: 2025-11-30
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ============================================
-- 1. CREATE DATABASE
-- ============================================

CREATE DATABASE IF NOT EXISTS `simakmur_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `simakmur_db`;

-- ============================================
-- 2. TABLE: categories
-- ============================================

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `sort_order` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Categories
INSERT INTO `categories` (`id`, `slug`, `name`, `sort_order`) VALUES
(1, 'makanan', 'Menu Makanan', 1),
(2, 'mie', 'Mie & Nasi', 2),
(3, 'tambahan', 'Menu Tambahan', 3),
(4, 'cemilan', 'Cemilan', 4),
(5, 'minuman', 'Minuman', 5);

-- ============================================
-- 3. TABLE: products
-- ============================================

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 100,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `has_sambal_addon` tinyint(1) DEFAULT 0,
  `has_saos_addon` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Products
INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `stock`, `image`, `is_active`, `has_sambal_addon`, `has_saos_addon`) VALUES
-- Menu Makanan (Category 1)
(1, 1, 'Nasi Telur', 'Nasi putih dengan telur dadar atau ceplok', 13000.00, 100, 'placeholder_nasi_telur.jpg', 1, 0, 0),
(2, 1, 'Nasi Ayam Suwir', 'Nasi dengan ayam suwir bumbu khas', 17000.00, 100, 'placeholder_ayam_suwir.jpg', 1, 0, 0),
(3, 1, 'Nasi Ayam Suwir Kemangi', 'Nasi ayam suwir dengan daun kemangi segar', 17000.00, 100, 'placeholder_ayam_suwir_kemangi.jpg', 1, 0, 0),
(4, 1, 'Nasi Ayam Bali', 'Nasi dengan ayam bumbu Bali pedas', 18000.00, 100, 'placeholder_ayam_bali.jpg', 1, 0, 0),
(5, 1, 'Nasi Chicken Katsu', 'Nasi dengan chicken katsu crispy', 20000.00, 100, 'placeholder_chicken_katsu.jpg', 1, 0, 1),
(6, 1, 'Nasi Chicken Katsu Mentai', 'Chicken katsu dengan saus mentai premium', 20000.00, 100, 'placeholder_chicken_katsu_mentai.jpg', 1, 0, 1),
(7, 1, 'Chicken Steak Katsu', 'Steak ayam katsu dengan sayuran', 20000.00, 100, 'placeholder_chicken_steak.jpg', 1, 0, 1),
(8, 1, 'Nasgor Jadul', 'Nasi goreng klasik ala jaman dulu', 15000.00, 100, 'placeholder_nasgor_jadul.jpg', 1, 0, 0),
(9, 1, 'Nasi Ayam Sereh', 'Nasi dengan ayam sereh wangi. Pilih 1 sambal!', 22000.00, 100, 'placeholder_ayam_sereh.jpg', 1, 1, 0),

-- Mie & Nasi (Category 2)
(10, 2, 'Mie Goreng', 'Mie goreng klasik dengan sayuran', 15000.00, 100, 'placeholder_mie_goreng.jpg', 1, 0, 0),
(11, 2, 'Mie Goreng Chicken Katsu', 'Mie goreng dengan topping chicken katsu', 23000.00, 100, 'placeholder_mie_goreng_katsu.jpg', 1, 0, 1),
(12, 2, 'Mie Nyemek', 'Mie dengan kuah gurih khas Jawa', 15000.00, 100, 'placeholder_mie_nyemek.jpg', 1, 0, 0),
(13, 2, 'Mie Nyemek Chicken Katsu', 'Mie nyemek dengan chicken katsu crispy', 23000.00, 100, 'placeholder_mie_nyemek_katsu.jpg', 1, 0, 1),

-- Menu Tambahan (Category 3)
(14, 3, 'Nasi', 'Nasi putih pulen', 4000.00, 200, 'placeholder_nasi.jpg', 1, 0, 0),
(15, 3, 'Nasi Daun Jeruk', 'Nasi pulen dengan daun jeruk wangi', 5000.00, 200, 'placeholder_nasi_daun_jeruk.jpg', 1, 0, 0),
(16, 3, 'Sambal', 'Sambal pilihan (terpisah)', 3000.00, 200, 'placeholder_sambal.jpg', 1, 0, 0),

-- Cemilan (Category 4)
(17, 4, 'Mendoan (Isi 4-5)', 'Mendoan tempe khas dengan 4-5 potong', 10000.00, 100, 'placeholder_mendoan.jpg', 1, 0, 0),
(18, 4, 'Tahu Home', 'Tahu goreng homemade crispy', 10000.00, 100, 'placeholder_tahu_home.jpg', 1, 0, 0),
(19, 4, 'Bakwan Jagung (Isi 4)', 'Bakwan jagung manis dengan 4 potong', 10000.00, 100, 'placeholder_bakwan.jpg', 1, 0, 0),

-- Minuman (Category 5)
(20, 5, 'Teh Tawar (Panas/Dingin)', 'Teh melati tawar tanpa gula', 3000.00, 200, 'placeholder_teh_tawar.jpg', 1, 0, 0),
(21, 5, 'Es Teh (Panas/Dingin)', 'Teh manis hangat atau dingin', 4000.00, 200, 'placeholder_es_teh.jpg', 1, 0, 0),
(22, 5, 'Es Jeruk (Panas/Dingin)', 'Jeruk segar hangat atau dingin', 6000.00, 200, 'placeholder_es_jeruk.jpg', 1, 0, 0),
(23, 5, 'Es Sirup', 'Sirup pilihan dengan es', 6000.00, 200, 'placeholder_es_sirup.jpg', 1, 0, 0),
(24, 5, 'Es Susu Sirup', 'Susu sirup dingin segar', 8000.00, 200, 'placeholder_es_susu_sirup.jpg', 1, 0, 0),
(25, 5, 'Kopi Good Day (Panas/Dingin)', 'Kopi Good Day instan', 5000.00, 200, 'placeholder_kopi_goodday.jpg', 1, 0, 0),
(26, 5, 'Milo (Panas/Dingin)', 'Milo coklat hangat atau dingin', 5000.00, 200, 'placeholder_milo.jpg', 1, 0, 0),
(27, 5, 'Mineral', 'Air mineral botol', 4000.00, 200, 'placeholder_mineral.jpg', 1, 0, 0),
(28, 5, 'Wedang Jahe', 'Wedang jahe hangat tradisional', 10000.00, 100, 'placeholder_wedang_jahe.jpg', 1, 0, 0),
(29, 5, 'Wedang Roti', 'Wedang roti manis khas Kudus', 15000.00, 100, 'placeholder_wedang_roti.jpg', 1, 0, 0);

-- ============================================
-- 4. TABLE: addons
-- ============================================

DROP TABLE IF EXISTS `addons`;

CREATE TABLE `addons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` enum('sambal','saos') NOT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Addons
INSERT INTO `addons` (`id`, `name`, `type`, `price`, `is_active`) VALUES
-- Sambal Options (Free with Nasi Ayam Sereh)
(1, 'Sambal Geprek', 'sambal', 0.00, 1),
(2, 'Sambal Kemangi', 'sambal', 0.00, 1),
(3, 'Sambal Terasi', 'sambal', 0.00, 1),
(4, 'Sambal Matah', 'sambal', 0.00, 1),

-- Saos Options (Free with Chicken Katsu products)
(5, 'Saos Katsu', 'saos', 0.00, 1),
(6, 'Saos BBQ', 'saos', 0.00, 1),
(7, 'Saos Mentai', 'saos', 0.00, 1);

-- ============================================
-- 5. TABLE: orders
-- ============================================

DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_number` varchar(10) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `qr_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. TABLE: order_items
-- ============================================

DROP TABLE IF EXISTS `order_items`;

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. TABLE: order_item_addons
-- ============================================

DROP TABLE IF EXISTS `order_item_addons`;

CREATE TABLE `order_item_addons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_item_id` int(11) NOT NULL,
  `addon_id` int(11) NOT NULL,
  `addon_name` varchar(100) NOT NULL,
  `addon_price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_item_id` (`order_item_id`),
  KEY `addon_id` (`addon_id`),
  CONSTRAINT `order_item_addons_ibfk_1` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_item_addons_ibfk_2` FOREIGN KEY (`addon_id`) REFERENCES `addons` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. TABLE: users (Admin/Cashier)
-- ============================================

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','cashier') NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Admin
-- Password: admin123 (hashed with password_hash)
INSERT INTO `users` (`username`, `password`, `role`, `full_name`, `is_active`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator', 1);

-- ============================================
-- COMMIT
-- ============================================

COMMIT;
