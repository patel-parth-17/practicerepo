-- Lumina Bookstore Database Schema for XAMPP (MySQL)
-- Database: bookstore_db

CREATE DATABASE IF NOT EXISTS `bookstore_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `bookstore_db`;

-- Drop existing tables if needed
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `books`;
DROP TABLE IF EXISTS `categories`;

-- Categories Table
CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `icon` VARCHAR(50) DEFAULT 'book',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Books Table
CREATE TABLE `books` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `author` VARCHAR(255) NOT NULL,
    `category_id` INT,
    `genre` VARCHAR(100) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `original_price` DECIMAL(10,2) DEFAULT NULL,
    `rating` DECIMAL(3,1) DEFAULT 4.5,
    `reviews_count` INT DEFAULT 120,
    `cover_theme` VARCHAR(50) DEFAULT 'midnight',
    `description` TEXT NOT NULL,
    `isbn` VARCHAR(30) DEFAULT '978-0134685991',
    `pages` INT DEFAULT 360,
    `format` VARCHAR(50) DEFAULT 'Hardcover',
    `publisher` VARCHAR(100) DEFAULT 'Apex Literary Press',
    `publish_year` INT DEFAULT 2024,
    `in_stock` INT DEFAULT 25,
    `is_bestseller` TINYINT(1) DEFAULT 0,
    `is_featured` TINYINT(1) DEFAULT 0,
    `badge` VARCHAR(50) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders Table
CREATE TABLE `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_number` VARCHAR(32) NOT NULL UNIQUE,
    `customer_name` VARCHAR(150) NOT NULL,
    `customer_email` VARCHAR(150) NOT NULL,
    `customer_phone` VARCHAR(50) DEFAULT NULL,
    `shipping_address` TEXT NOT NULL,
    `city` VARCHAR(100) NOT NULL,
    `postal_code` VARCHAR(30) NOT NULL,
    `payment_method` VARCHAR(50) DEFAULT 'Cash on Delivery',
    `subtotal` DECIMAL(10,2) NOT NULL,
    `discount` DECIMAL(10,2) DEFAULT 0.00,
    `shipping_fee` DECIMAL(10,2) DEFAULT 0.00,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `status` VARCHAR(50) DEFAULT 'Processing',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order Items Table
CREATE TABLE `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `book_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Reviews Table
CREATE TABLE `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `book_id` INT NOT NULL,
    `reviewer_name` VARCHAR(100) NOT NULL,
    `rating` INT NOT NULL DEFAULT 5,
    `comment` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`book_id`) REFERENCES `books`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `icon`) VALUES
(1, 'All Genres', 'all', 'compass'),
(2, 'Fiction & Literature', 'fiction', 'feather'),
(3, 'Sci-Fi & Fantasy', 'sci-fi', 'rocket'),
(4, 'Technology & AI', 'technology', 'cpu'),
(5, 'Business & Wealth', 'business', 'trending-up'),
(6, 'Psychology & Mind', 'psychology', 'smile'),
(7, 'Mystery & Thriller', 'thriller', 'eye'),
(8, 'Biography & History', 'biography', 'award');

-- Seed Books
INSERT INTO `books` (`id`, `title`, `author`, `category_id`, `genre`, `price`, `original_price`, `rating`, `reviews_count`, `cover_theme`, `description`, `isbn`, `pages`, `format`, `publisher`, `publish_year`, `in_stock`, `is_bestseller`, `is_featured`, `badge`) VALUES
(1, 'The Architecture of Tomorrow', 'Dr. Evelyn Vance', 4, 'Technology & AI', 28.50, 36.00, 4.9, 342, 'cyber-blue', 'A tour de force exploring neural engineering, autonomous systems, and how machine intelligence is redefining human capability and creative consciousness in the next fifty years.', '978-0134685991', 412, 'Hardcover', 'Silicon Horizon Books', 2024, 18, 1, 1, 'BESTSELLER'),
(2, 'Echoes of the Starlit Sea', 'Julian Thorne', 2, 'Fiction & Literature', 22.95, 29.99, 4.8, 518, 'ocean-deep', 'A breathtaking multi-generational tale woven across coastal Portugal and distant lighthouses, celebrating love, forgotten maps, and family secrets hidden beneath tidal tides.', '978-0451524935', 384, 'Hardcover', 'Pelican Bay Press', 2023, 14, 1, 1, 'STAFF PICK'),
(3, 'Zero to Mastery: Mental Models', 'Marcus Chen', 6, 'Psychology & Mind', 24.00, 32.00, 4.9, 840, 'emerald-forest', 'Master 30 battle-tested frameworks used by top strategists, Nobel laureates, and high-stakes problem solvers to cut through noise, conquer cognitive bias, and decide with certainty.', '978-0743273565', 320, 'Paperback', 'Keystone Mind Press', 2024, 45, 1, 1, 'HOT DEAL'),
(4, 'Neon Horizon: 2099', 'Kira Sterling', 3, 'Sci-Fi & Fantasy', 19.99, 24.99, 4.7, 276, 'purple-nebula', 'In a neon-drenched orbital megacity hovering above the Pacific, a renegade data courier unearths a simulated reality glitch that threatens the synthetic hierarchy.', '978-0385537858', 460, 'Paperback', 'Starlight Galaxy Imprint', 2024, 8, 0, 1, 'NEW RELEASE'),
(5, 'The Resilient Capitalist', 'Jonathan Sterling', 5, 'Business & Wealth', 31.00, 39.50, 4.8, 412, 'amber-gold', 'A candid playbook for surviving macroeconomic volatility, deploying asymmetric capital, and building enduring enterprises in hyper-competitive global landscapes.', '978-0307277671', 352, 'Hardcover', 'Vanguard Business House', 2023, 22, 1, 0, 'POPULAR'),
(6, 'Whispers in the Blackwood', 'Eleanor Finch', 7, 'Mystery & Thriller', 18.50, 23.00, 4.6, 195, 'crimson-noir', 'When an enigmatic botanist vanishes inside a secluded Appalachian valley, an insomniac investigator follows a trail of preserved specimens and cryptic journal entries.', '978-0143127741', 310, 'Paperback', 'Blackwood Literary', 2024, 12, 0, 0, 'THRILLER'),
(7, 'The Quantum Mindset', 'Prof. Aris Thorne', 4, 'Technology & AI', 26.00, 34.00, 4.8, 310, 'sapphire-dream', 'Demystifying quantum mechanics, quantum computing paradigms, and probabilistic thinking for founders, software engineers, and curious polymaths.', '978-0525559474', 298, 'Hardcover', 'Apex Science Press', 2024, 19, 0, 1, 'TRENDING'),
(8, 'The Alchemist of Florence', 'Matteo Rossi', 8, 'Biography & History', 27.50, 35.00, 4.9, 620, 'terracotta-sun', 'An intimate portrait of Renaissance polymaths, secret guild workshops, and the feverish race to formulate eternal pigments that shaped European art history.', '978-0679722762', 520, 'Hardcover', 'Medici Historical House', 2023, 16, 1, 0, 'EDITOR CHOICE'),
(9, 'Deep Work & The Quiet Brain', 'Clara Henderson', 6, 'Psychology & Mind', 21.00, 27.00, 4.7, 430, 'sage-zen', 'Scientific strategies to insulate your cognitive attention, enter flow states at will, and eliminate digital dopamine traps in an attention-starved economy.', '978-1400033416', 272, 'Paperback', 'Stillwater Books', 2024, 33, 0, 0, 'SELF CARE'),
(10, 'Chronicles of the Solar Empire', 'Aiden Zhao', 3, 'Sci-Fi & Fantasy', 25.50, 30.00, 4.9, 780, 'cosmic-dark', 'An interstellar epic exploring dynastic rivalries across Jovian colonies, asteroid mining guilds, and the discovery of a non-human Dyson sphere fragment.', '978-0062316097', 624, 'Hardcover', 'Starlight Galaxy Imprint', 2023, 11, 1, 1, 'BESTSELLER'),
(11, 'The Startup Crucible', 'Rohan Patel & Sarah Kim', 5, 'Business & Wealth', 29.00, 38.00, 4.8, 510, 'bronze-slate', 'Raw lessons from 50 founders who navigated near-death startup moments, pivot paralysis, and scale breakdowns to achieve durable enterprise market leadership.', '978-0399590504', 368, 'Hardcover', 'Vanguard Business House', 2024, 27, 0, 1, 'FOUNDER PICK'),
(12, 'Shadows on Baker Street', 'Arthur C. Campbell', 7, 'Mystery & Thriller', 17.90, 22.50, 4.7, 340, 'charcoal-mist', 'A masterfully crafted neo-Victorian detective novel tracing clandestine societies, underground rail vaults, and mechanical enigmas in fog-shrouded London.', '978-0345803481', 344, 'Paperback', 'Blackwood Literary', 2023, 15, 0, 0, 'CLASSIC NOIR');

-- Seed Reviews
INSERT INTO `reviews` (`book_id`, `reviewer_name`, `rating`, `comment`) VALUES
(1, 'Alexander Wright', 5, 'An absolute tour de force. The depth of analysis regarding machine intelligence and human agency is unparalleled.'),
(1, 'Sophia Loren', 5, 'Couldn\'t put it down! Dr. Vance explains complex deep tech paradigms with stunning clarity and poetic prose.'),
(2, 'David Miller', 5, 'One of the most evocative fictional novels I have read this decade. The coastal imagery still lingers with me.'),
(3, 'Jessica Reed', 5, 'Game changer for my daily executive decisions. The mental models are instantly actionable and thoroughly researched.');
