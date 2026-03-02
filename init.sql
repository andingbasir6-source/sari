-- init.sql: create database, table, and sample data for Sari-Sari Inventory
CREATE DATABASE IF NOT EXISTS `sari_sari_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sari_sari_db`;

CREATE TABLE IF NOT EXISTS `items` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `manufacture` VARCHAR(255) DEFAULT NULL,
  `type` VARCHAR(255) DEFAULT NULL,
  `grams` INT DEFAULT NULL,
  `price` DECIMAL(10,2) DEFAULT NULL,
  `expiration` DATE DEFAULT NULL,
  `made_date` DATE DEFAULT NULL,
  `availability` INT DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data
INSERT INTO `items` (`name`, `manufacture`, `type`, `grams`, `price`, `expiration`, `made_date`, `availability`) VALUES
('Nido Fortified', 'Nestle', 'Milk Powder', 400, 450.00, '2026-12-31', '2024-01-01', 10),
('100 Plus', 'Fraser & Neave', 'Soft Drink', 330, 35.00, '2025-06-30', '2024-06-01', 20),
('Lucky Me Pancit Canton', 'Monde Nissin', 'Instant Noodles', 60, 12.50, '2026-03-31', '2024-02-15', 30);

-- User accounts (plaintext passwords for simplicity)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','staff') NOT NULL DEFAULT 'staff',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `users` (`username`,`password`,`role`) VALUES
('admin','admin123','admin'),
('staff','staff123','staff');
