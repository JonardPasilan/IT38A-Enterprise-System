-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2025 at 06:06 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medtrack`
--

-- --------------------------------------------------------

--
-- Table structure for table `medication_reminders`
--

CREATE TABLE `medication_reminders` (
  `reminder_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `dosage` varchar(100) NOT NULL,
  `frequency` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `medicine_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `low_stock_threshold` int(11) DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`medicine_id`, `name`, `description`, `price`, `image_url`, `stock_quantity`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES
(1, 'Paracetamol', 'Pain reliever and fever reducer', 5.00, '1747320165_paracetamol.jpg', 100, 10, '2025-05-15 13:01:29', '2025-05-15 14:42:45'),
(2, 'Ibuprofen', 'Nonsteroidal anti-inflammatory drug', 10.00, '1747320135_Ibuprofen.jpg', 50, 10, '2025-05-15 13:01:29', '2025-05-15 14:42:15'),
(3, 'Amoxicillin', 'Antibiotic for bacterial infections', 15.00, '1747319902_amoxicillin.jpg', 1, 10, '2025-05-15 13:01:29', '2025-05-15 14:38:22'),
(5, 'Cetirizine', 'Antihistamine for allergies', 8.00, '1747320125_Cetirizine.jpg', 90, 10, '2025-05-15 13:01:29', '2025-05-15 14:42:05');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('order','reminder','system') NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 1, 'Added to Cart', 'Added Paracetamol to your cart', 'order', 1, '2025-05-15 13:05:12'),
(2, 1, 'Order Placed', 'Your order #1 has been placed and is pending approval', 'order', 0, '2025-05-15 13:05:19'),
(3, 1, 'Added to Cart', 'Added Paracetamol to your cart', 'order', 0, '2025-05-15 13:05:27'),
(4, 1, 'Order Placed', 'Your order #2 has been placed and is pending approval', 'order', 0, '2025-05-15 13:05:30'),
(5, 1, 'Added to Cart', 'Added Paracetamol to your cart', 'order', 0, '2025-05-15 13:05:32'),
(6, 1, 'Order Placed', 'Your order #3 has been placed and is pending approval', 'order', 0, '2025-05-15 13:05:43'),
(7, 1, 'Added to Cart', 'Added Paracetamol to your cart', 'order', 0, '2025-05-15 13:05:52'),
(8, 1, 'Order Placed', 'Your order #4 has been placed and is pending approval', 'order', 1, '2025-05-15 13:05:53'),
(9, 1, 'Added to Cart', 'Added Paracetamol to your cart', 'order', 1, '2025-05-15 13:06:03'),
(10, 1, 'Added to Cart', 'Added Paracetamol to your cart', 'order', 1, '2025-05-15 13:06:06'),
(11, 1, 'Added to Cart', 'Added Paracetamol to your cart', 'order', 1, '2025-05-15 13:06:07'),
(12, 1, 'Added to Cart', 'Added Paracetamol to your cart', 'order', 1, '2025-05-15 13:06:08'),
(13, 1, 'Order Placed', 'Your order #5 has been placed and is pending approval', 'order', 1, '2025-05-15 13:06:15'),
(14, 1, 'Order Status Updated', 'Your order #5 status has been updated to: Processing', 'order', 0, '2025-05-15 13:39:27'),
(15, 1, 'Order Status Updated', 'Your order #4 status has been updated to: Preparing', 'order', 0, '2025-05-15 13:39:36'),
(16, 1, 'Order Status Updated', 'Your order #3 status has been updated to: Ready', 'order', 0, '2025-05-15 13:39:40'),
(17, 1, 'Order Status Updated', 'Your order #2 status has been updated to: Completed', 'order', 0, '2025-05-15 13:39:47'),
(18, 1, 'Order Status Updated', 'Your order #1 status has been updated to: Cancelled', 'order', 0, '2025-05-15 13:39:51'),
(19, 2, 'Added to Cart', 'Added Paracetamol to your cart', 'order', 0, '2025-05-15 13:41:39'),
(20, 2, 'Order Placed', 'Your order #6 has been placed and is pending approval', 'order', 0, '2025-05-15 13:41:42'),
(21, 2, 'Added to Cart', 'Added Ibuprofen to your cart', 'order', 0, '2025-05-15 13:41:43'),
(22, 2, 'Added to Cart', 'Added Amoxicillin to your cart', 'order', 0, '2025-05-15 13:41:45'),
(23, 2, 'Added to Cart', 'Added Omeprazole to your cart', 'order', 0, '2025-05-15 13:41:46'),
(24, 2, 'Added to Cart', 'Added Cetirizine to your cart', 'order', 0, '2025-05-15 13:41:47'),
(25, 2, 'Order Placed', 'Your order #7 has been placed and is pending approval', 'order', 0, '2025-05-15 13:41:54');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','preparing','ready','completed','cancelled') DEFAULT 'pending',
  `payment_status` enum('pending','paid','unpaid') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_amount`, `status`, `payment_status`, `created_at`, `updated_at`) VALUES
(1, 1, 5.00, 'cancelled', 'pending', '2025-05-15 13:05:19', '2025-05-15 13:39:51'),
(2, 1, 5.00, 'completed', 'pending', '2025-05-15 13:05:30', '2025-05-15 13:39:47'),
(3, 1, 5.00, 'ready', 'pending', '2025-05-15 13:05:43', '2025-05-15 13:39:40'),
(4, 1, 5.00, 'preparing', 'pending', '2025-05-15 13:05:53', '2025-05-15 13:39:36'),
(5, 1, 20.00, 'processing', 'pending', '2025-05-15 13:06:15', '2025-05-15 13:39:27'),
(6, 2, 5.00, 'pending', 'pending', '2025-05-15 13:41:42', '2025-05-15 13:41:42'),
(7, 2, 45.00, 'pending', 'pending', '2025-05-15 13:41:54', '2025-05-15 13:41:54');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `medicine_id`, `quantity`, `price`, `created_at`) VALUES
(1, 1, 1, 1, 5.00, '2025-05-15 13:05:19'),
(2, 2, 1, 1, 5.00, '2025-05-15 13:05:30'),
(3, 3, 1, 1, 5.00, '2025-05-15 13:05:43'),
(4, 4, 1, 1, 5.00, '2025-05-15 13:05:53'),
(5, 5, 1, 4, 5.00, '2025-05-15 13:06:15'),
(6, 6, 1, 1, 5.00, '2025-05-15 13:41:42'),
(7, 7, 2, 1, 10.00, '2025-05-15 13:41:54'),
(8, 7, 3, 1, 15.00, '2025-05-15 13:41:54'),
(10, 7, 5, 1, 8.00, '2025-05-15 13:41:54');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `prescription_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `dosage` varchar(100) NOT NULL,
  `instructions` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','expired') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `phone`, `email`, `password`) VALUES
(1, 'maricar', 'balagan', '09090909090', 'example@gmail.com', '$2y$10$O50PJwVBaVpbGFxLBWkSQu8bLpsfejR6KVcfzNIo1Whf7uc0P9rI6'),
(2, 'dongskie', 'saraum', '09090909090', 'example2@gmail.com', '$2y$10$K8y7Ddd7iIFSpMlFD131x.TSMpF.Lnqw2d.VSt/6gzufyfNJ.oTgS');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `medication_reminders`
--
ALTER TABLE `medication_reminders`
  ADD PRIMARY KEY (`reminder_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`medicine_id`),
  ADD KEY `idx_medicine_name` (`name`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_order_status` (`status`),
  ADD KEY `idx_payment_status` (`payment_status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`prescription_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `medicine_id` (`medicine_id`),
  ADD KEY `idx_prescription_status` (`status`);

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
-- AUTO_INCREMENT for table `medication_reminders`
--
ALTER TABLE `medication_reminders`
  MODIFY `reminder_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `medicine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `prescription_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `medication_reminders`
--
ALTER TABLE `medication_reminders`
  ADD CONSTRAINT `medication_reminders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medication_reminders_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`) ON DELETE CASCADE;

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
