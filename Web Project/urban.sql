-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql112.byetcluster.com
-- Generation Time: May 12, 2025 at 12:04 PM
-- Server version: 10.6.19-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `icei_38922711_urban_harvest`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) GENERATED ALWAYS AS (`quantity` * `price`) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Fresh Fruits'),
(2, 'Fresh Vegetables'),
(3, 'Dairy Products'),
(4, 'Grains'),
(5, 'Toiletries');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `message`, `submitted_at`) VALUES
(1, '', '', '', '', '2025-05-01 20:02:59'),
(2, 'HADEED', 'HADEED@gmail.com', '0310998861', 'hello', '2025-05-01 20:06:16'),
(3, 'Mahad', 'Mahad@gmail.com', '0310998861', 'Hello', '2025-05-01 20:07:00'),
(4, 'Fareeba', 'Fareeba@gmail.com', '0310998861', 'Hello', '2025-05-01 20:08:58');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `image` varchar(100) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `price`, `unit`, `image`, `category_id`) VALUES
(1, 'Apples', 200, 'kg', 'apple.jpg', 1),
(2, 'Bananas', 150, 'dozen', 'bananas.jpg', 1),
(3, 'Mangoes', 300, 'kg', 'mango.jpg', 1),
(4, 'Orange', 220, 'kg', 'orange.jpg', 1),
(5, 'Grapes', 180, 'kg', 'grapes.jpg', 1),
(6, 'Pomegranate', 280, 'kg', 'pomegranate.png', 1),
(7, 'Carrots', 120, 'kg', 'carrots.png', 2),
(8, 'Tomato', 180, 'kg', 'tomato.jpg', 2),
(9, 'Potato', 100, 'kg', 'potato.jpg', 2),
(10, 'Spinach', 90, 'bunch', 'spinach.jpg', 2),
(11, 'Onion', 150, 'kg', 'onion.jpg', 2),
(12, 'Capsicum', 200, 'kg', 'capsicum.jpg', 2),
(13, 'Milk', 250, 'liter', 'milk.jpg', 3),
(14, 'Cheese', 500, 'pack', 'cheese.png', 3),
(15, 'Butter', 450, 'pack', 'butter.png', 3),
(16, 'Yogurt', 180, 'kg', 'yogurt.png', 3),
(17, 'Cream', 300, 'pack', 'cream.jpg', 3),
(18, 'Paneer', 400, 'kg', 'paneer.png', 3),
(19, 'Rice', 250, 'kg', 'rice.png', 4),
(20, 'Wheat', 220, 'kg', 'wheat.jpg', 4),
(21, 'Oats', 180, 'kg', 'oats.png', 4),
(22, 'Quinoa', 150, 'kg', 'quinoa.png', 4),
(23, 'Barley', 200, 'kg', 'barley.jpg', 4),
(24, 'Toothpaste', 120, 'tube', 'toothpaste.jpg', 5),
(25, 'Toothbrush', 250, 'piece', 'toothbrush.png', 5),
(26, 'Nailcutter', 150, 'piece', 'nailcutter.jpg', 5),
(27, 'Moisturizer', 875, 'pack', 'moisturizer.png', 5),
(28, 'Hand Wash', 180, 'bottle', 'handwash.png', 5);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `items` text DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `discount_code` varchar(50) DEFAULT NULL,
  `discounted_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `name`, `email`, `address`, `city`, `zipcode`, `items`, `total_price`, `discount_code`, `discounted_price`, `created_at`, `status`) VALUES
(1, 19, 'AAAAAAAAA', 'aaaaaaaa@gmail.com', 'Fast Nuces Peshawar  industrial Road', 'peshawar', '25000', 'Cheese (x3), Butter (x2), Yogurt (x2), Pomegranate (x1), Grapes (x1), Carrots (x1)', '3340.00', '0', '3340.00', '2025-05-01 04:41:24', 'accepted'),
(2, 20, 'Wajahat', 'wajahat@gmail.com', 'Sadar peshawar', 'peshawar', '25000', 'Milk (x1), Tomato (x2), Rice (x1)', '860.00', '0', '860.00', '2025-05-01 05:31:12', 'accepted'),
(3, 21, 'MAHAD', 'Mahad@gmail.com', 'Fast Nuces Peshawar  industrial Road', 'peshawar', '25000', 'Milk (x2), Cheese (x1), Yogurt (x1)', '1180.00', '0', '1180.00', '2025-05-01 17:33:14', 'accepted'),
(4, 22, 'Fatima', 'fatima@gmail.com', 'Fast Nuces Peshawar  industrial Road', 'peshawar', '25000', 'Milk (x1), Cheese (x1)', '750.00', '0', '750.00', '2025-05-07 14:26:14', 'accepted'),
(5, 22, 'Fatima', 'fatima@gmail.com', 'Fast Nuces Peshawar  industrial Road', 'peshawar', '25000', 'Butter (x1), Yogurt (x1)', '630.00', '0', '630.00', '2025-05-07 14:30:20', 'accepted'),
(6, 22, 'Fatima', 'fatima@gmail.com', 'Fast Nuces Peshawar  industrial Road', 'peshawar', '25000', 'Cheese (x1), Butter (x1)', '950.00', '0', '950.00', '2025-05-07 16:19:18', 'accepted'),
(7, 23, 'Hadeed Bin Toufeeque', 'hadeedkhan117@gmail.com', 'Village bhoraghari post office billitang kohat', 'Kohat', '26000', 'Cheese (x1)', '500.00', '0', '500.00', '2025-05-10 18:07:59', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `city` varchar(255) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `registered_at`, `city`, `zipcode`, `address`) VALUES
(2, 'ALI', 'hadeedALI@gmail.com', '$2y$10$y39LZg3zCZXjr1iPmMPZaeYW58v1y8lHc/K39OZk/RCwegnReh9QS', '2025-04-30 09:23:51', NULL, NULL, NULL),
(3, 'ahmed', 'ahmed@gmail.com', '$2y$10$1JVhiAEvF6ZWPE.b91vJ1ORRk5E65SoDRq1Rvdt2nYErASRlZR2Cq', '2025-04-30 09:25:29', NULL, NULL, NULL),
(16, 'Bilal', 'bilal@gmail.com', '$2y$10$rZGXWHd/dIvYYSxbqGVm9.pJ9i/kDYf3P0RIEuOwmAt3PdDISMf7u', '2025-04-30 10:46:16', 'peshawar', '25000', 'Fast Nuces Peshawar  industrial Road'),
(19, 'AAAAAAAAA', 'aaaaaaaa@gmail.com', '$2y$10$QfDcCrJ2plo9SbLDNn1baOlvbVqsC/L3sIaE6lfv6XBNbnCttIRue', '2025-04-30 10:53:15', 'peshawar', '25000', 'Fast Nuces Peshawar  industrial Road'),
(20, 'Wajahat', 'wajahat@gmail.com', '$2y$10$G.gmzRkNGav/JyTMeOyzC.32QCcHXoBJpgh0kYVCoxTCrJV03X.bu', '2025-05-01 08:24:27', 'peshawar', '25000', 'Sadar peshawar'),
(21, 'MAHAD', 'Mahad@gmail.com', '$2y$10$eM2lRjUfSZSn4qXH1JeAR./IR0p5nvzd3OzQDQD5ZgRinqAM3uQku', '2025-05-01 20:26:30', 'peshawar', '25000', 'Fast Nuces Peshawar  industrial Road'),
(22, 'Fatima', 'fatima@gmail.com', '$2y$10$erX2buznzqoiAVD41ai7DOuO6RqjHqsL4.xqmd8g4nop1NFWmvwHS', '2025-05-07 11:16:39', 'peshawar', '25000', 'Fast Nuces Peshawar  industrial Road'),
(23, 'Hadeed Bin Toufeeque', 'hadeedkhan117@gmail.com', '$2y$10$XKXniDQgd2P3MqfVYL2zae6LDvpNDYJKjSZ8aJYnL1ZT3NBQJa79C', '2025-05-10 15:07:12', 'Kohat', '26000', 'Village bhoraghari post office billitang kohat');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
