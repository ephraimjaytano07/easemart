-- ============================================================
--  Ease Mart — Full Database Setup
--  Run this in phpMyAdmin or MySQL CLI
-- ============================================================

CREATE DATABASE IF NOT EXISTS `pos_inventory_db`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `pos_inventory_db`;

-- ── tbl_users ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `tbl_users` (
  `user_id`    INT(11)      NOT NULL AUTO_INCREMENT,
  `username`   VARCHAR(50)  NOT NULL,
  `email`      VARCHAR(100) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `role`       VARCHAR(20)  DEFAULT 'cashier',
  `created_at` TIMESTAMP    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── tbl_products ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `tbl_products` (
  `product_id`    INT(11)       NOT NULL AUTO_INCREMENT,
  `product_name`  VARCHAR(100)  NOT NULL,
  `price`         DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `product_image` VARCHAR(255)  DEFAULT 'default.png',
  `quantity`      INT(11)       NOT NULL DEFAULT 0,
  `is_deleted`    TINYINT(1)    NOT NULL DEFAULT 0,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── tbl_transactions ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS `tbl_transactions` (
  `transaction_id`  INT(11)       NOT NULL AUTO_INCREMENT,
  `customer_name`   VARCHAR(100)  NOT NULL,
  `total_amount`    DECIMAL(10,2) NOT NULL,
  `amount_paid`     DECIMAL(10,2) NOT NULL,
  `amount_change`   DECIMAL(10,2) NOT NULL,
  `transaction_date` TIMESTAMP    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Sample products ────────────────────────────────────────
INSERT INTO `tbl_products` (`product_name`, `price`, `product_image`, `quantity`, `is_deleted`) VALUES
('Milo 20g',               10.00, 'milo.avif',       20, 0),
('Bear Brand 20g',         12.00, 'bearbrand.webp',  17, 0),
('Datu Puti Suka 200mL',    8.00, 'suka.webp',       15, 0),
('Datu Puti Toyo 200mL',    8.00, 'toyo.jpg',        11, 0),
('Surf Powder 65g',         8.00, 'surf.webp',       18, 0),
('Downy 20mL',              6.00, 'downy.webp',       9, 0),
('Cream-O 10 packs',       65.00, 'creamo.jpg',      10, 0),
('SkyFlakes 10 packs',     60.00, 'skyflakes.webp',  12, 0),
('Joy 40g',                12.00, 'joy.webp',        10, 0);

-- ── If you already have the old DB, run just this line ─────
-- ALTER TABLE `tbl_products` ADD COLUMN IF NOT EXISTS `is_deleted` TINYINT(1) NOT NULL DEFAULT 0;
