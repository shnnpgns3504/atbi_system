CREATE DATABASE IF NOT EXISTS atbi_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE atbi_system;

-- Users
CREATE TABLE `users` (
    `user_id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`)
) ENGINE=InnoDB;

-- ATBIs (6 entries)
CREATE TABLE `atbis` (
    `atbi_id` INT(11) NOT NULL AUTO_INCREMENT,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `location` VARCHAR(255) DEFAULT NULL,
    `contact_person` VARCHAR(100) DEFAULT NULL,
    `contact_email` VARCHAR(100) DEFAULT NULL,
    `contact_phone` VARCHAR(50) DEFAULT NULL,
    `image_url` VARCHAR(255) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`atbi_id`)
) ENGINE=InnoDB;

-- Products
CREATE TABLE `products` (
    `product_id` INT(11) NOT NULL AUTO_INCREMENT,
    `atbi_id` INT(11) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `stock` INT(11) DEFAULT 0,
    `image_url` VARCHAR(255) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`product_id`),
    FOREIGN KEY (`atbi_id`) REFERENCES `atbis`(`atbi_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Cart (per user)
CREATE TABLE `cart_items` (
    `cart_id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `product_id` INT(11) NOT NULL,
    `quantity` INT(11) NOT NULL DEFAULT 1,
    `added_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`cart_id`),
    UNIQUE KEY `user_product` (`user_id`, `product_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Calendar events
CREATE TABLE `calendar_events` (
    `event_id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `event_date` DATE NOT NULL,
    `event_end_date` DATE DEFAULT NULL,
    `event_type` ENUM('event', 'announcement', 'holiday') DEFAULT 'event',
    `atbi_id` INT(11) DEFAULT NULL,
    `is_holiday` TINYINT(1) DEFAULT 0,
    `created_by` INT(11) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`event_id`),
    FOREIGN KEY (`atbi_id`) REFERENCES `atbis`(`atbi_id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Insert 6 ATBIs
INSERT INTO `atbis` (`slug`, `name`, `description`, `location`, `contact_person`, `contact_email`, `contact_phone`) VALUES
('atbi-1', 'ATBI 1 - Highland Vegetables', 'Highland vegetable production and processing', 'La Trinidad, Benguet', 'Juan Dela Cruz', 'atbi1@bsu.edu.ph', '0917-111-1111'),
('atbi-2', 'ATBI 2 - Arabica Coffee', 'Premium Arabica coffee production', 'Atok, Benguet', 'Maria Santos', 'atbi2@bsu.edu.ph', '0917-222-2222'),
('atbi-3', 'ATBI 3 - Strawberry Products', 'Fresh and processed strawberry products', 'La Trinidad, Benguet', 'Pedro Reyes', 'atbi3@bsu.edu.ph', '0917-333-3333'),
('atbi-4', 'ATBI 4 - Native Chicken', 'Native chicken raising and processing', 'Buguias, Benguet', 'Ana Lopez', 'atbi4@bsu.edu.ph', '0917-444-4444'),
('atbi-5', 'ATBI 5 - Root Crops', 'Root crops processing and packaging', 'Bokod, Benguet', 'Carlos Mendoza', 'atbi5@bsu.edu.ph', '0917-555-5555'),
('atbi-6', 'ATBI 6 - Honey and Bee Products', 'Local honey and bee by-products', 'Kabayan, Benguet', 'Lisa Torres', 'atbi6@bsu.edu.ph', '0917-666-6666');

-- Sample products
INSERT INTO `products` (`atbi_id`, `name`, `description`, `price`, `stock`) VALUES
(1, 'Fresh Broccoli (1kg)', 'Organically grown highland broccoli', 120.00, 50),
(1, 'Baguio Beans (1kg)', 'Fresh from La Trinidad farms', 90.00, 40),
(2, 'Arabica Coffee Beans (500g)', 'Medium roast, single origin', 450.00, 30),
(2, 'Coffee Grounds (250g)', 'Fine grind, ready to brew', 280.00, 25),
(3, 'Strawberry Jam (250g)', 'Made from fresh La Trinidad strawberries', 180.00, 60),
(3, 'Fresh Strawberries (1kg)', 'Sweet and juicy', 250.00, 20),
(4, 'Native Chicken (whole)', 'Free-range native chicken', 350.00, 15),
(4, 'Salted Egg (12 pcs)', 'Native duck eggs, salted', 200.00, 30),
(5, 'Sweet Potato (1kg)', 'Organic camote from Bokod', 80.00, 50),
(5, 'Taro/Gabi (1kg)', 'Fresh from the farm', 95.00, 40),
(6, 'Wild Honey (250ml)', 'Pure honey from Kabayan', 350.00, 20),
(6, 'Beeswax (100g)', 'Natural beeswax for candle making', 150.00, 30);

-- Philippine holidays 2026
INSERT INTO `calendar_events` (`title`, `event_date`, `event_type`, `is_holiday`) VALUES
('New Year\'s Day', '2026-01-01', 'holiday', 1),
('Chinese New Year', '2026-02-17', 'holiday', 1),
('EDSA People Power Revolution', '2026-02-25', 'holiday', 1),
('Maundy Thursday', '2026-04-02', 'holiday', 1),
('Good Friday', '2026-04-03', 'holiday', 1),
('Black Saturday', '2026-04-04', 'holiday', 1),
('Araw ng Kagitingan', '2026-04-09', 'holiday', 1),
('Labor Day', '2026-05-01', 'holiday', 1),
('Independence Day', '2026-06-12', 'holiday', 1),
('National Heroes Day', '2026-08-31', 'holiday', 1),
('Bonifacio Day', '2026-11-30', 'holiday', 1),
('Christmas Day', '2026-12-25', 'holiday', 1),
('Rizal Day', '2026-12-30', 'holiday', 1);