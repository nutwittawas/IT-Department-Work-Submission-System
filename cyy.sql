-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 15, 2025 at 10:31 AM
-- Server version: 8.0.17
-- PHP Version: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cyy`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `model` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `usage_location` varchar(255) DEFAULT NULL,
  `item_condition` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `model`, `quantity`, `status`, `usage_location`, `item_condition`) VALUES
(1, 'ทรงนัท', 'a', 1, '2', NULL, '2'),
(2, 'ฮับ', 'ฟแ8880', 1, 'ถูกเบิก', NULL, 'พร้อมใช้งาน'),
(3, 'ทรงนัท', 'a', 1, 'ถูกเบิก', '5678', 'พร้อมใช้งาน');

-- --------------------------------------------------------

--
-- Table structure for table `it_inventory`
--

CREATE TABLE `it_inventory` (
  `id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `model` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `it_inventory`
--

INSERT INTO `it_inventory` (`id`, `item_name`, `model`, `quantity`, `created_at`) VALUES
(1, 'หมึกhp', '037', 2, '2025-03-04 06:47:58'),
(2, 'เครื่องสำรองไฟ', 'UPS 800VA ADVICE Smart LCD', 1, '2025-03-06 08:58:49'),
(3, 'เครื่องสำรองไฟ', '1000VA ETECH Ego Plus', 1, '2025-03-06 08:59:33'),
(4, 'จอคอม ', 'MONITOR 21.5 นิ้ว ACER EK220Q ', 2, '2025-03-06 09:00:55'),
(5, 'จอคอม ', 'MONITOR 27 นิ้ว ACER EK220Q ', 1, '2025-03-15 07:12:25'),
(6, 'เครื่องสำรองไฟ', 'UPS 1000VA ADVICE Smart LCD', 5, '2025-03-15 07:12:36');

-- --------------------------------------------------------

--
-- Table structure for table `job`
--

CREATE TABLE `job` (
  `id` int(11) NOT NULL,
  `job_name` varchar(255) NOT NULL,
  `equipment` text NOT NULL,
  `image` text NOT NULL,
  `sender_name` varchar(255) NOT NULL,
  `created_datetime` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_date` date GENERATED ALWAYS AS (cast(`created_datetime` as date)) VIRTUAL,
  `created_time` time GENERATED ALWAYS AS (cast(`created_datetime` as time)) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `job`
--

INSERT INTO `job` (`id`, `job_name`, `equipment`, `image`, `sender_name`, `created_datetime`) VALUES
(2, 'แก้ไขหมึกพิมพ์ หมด', 'หมึก hp80', '167113_0.jpg', 'พี่เกม', '2025-02-04 14:06:49'),
(3, 'ซ่อมเครื่องสำรองไฟ ', 'ตะกั่ว', '167110_0.jpg,167109_0.jpg', 'พี่นนท์', '2025-02-04 14:06:49'),
(4, 'แก้ไขเน็ตใช้ไม่ได้', 'อะแดปเตอร์ 5 v เทปดำ', '52029_0.jpg,52033_0.jpg,52031_0.jpg', 'พี่เกม', '2025-02-04 14:06:49'),
(5, 'แก้ไขกล้องจุด', 'ไม่มี', '167071_0.jpg,167070_0.jpg,167069_0.jpg', 'พี่นนท์', '2025-02-04 14:06:49'),
(6, 'แก้ไขกล้อง', 'ไม่มี', '167112_0.jpg,167113_0.jpg', 'พี่นนท์', '2025-02-04 14:07:58'),
(7, 'ทำปลั้กแลน เต้ารับ', 'หัวแลน CAT6A 10 ตัว', 'S__231931912_0.jpg', 'พี่นนท์', '2025-02-04 14:10:27'),
(10, 'ติดตั้งกล้องบ่อลากูน', 'หัว BNC 1 หัว,หัวแจ็ค DC ตัวผู้ 1 หัว', '138138_0.jpg,138139_0.jpg,138140_0.jpg', 'นัท', '2025-03-15 11:00:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `nickname` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `nickname`, `phone`, `username`, `password`, `status`, `image`) VALUES
(1, 'มานนท์ ดงสันเทียะ', 'นนท์', '0812345678', 'user1', '1234', 0, 'uploads/non.jpg'),
(2, 'วิชานาถ สุขวรเวท', 'หัวหน้านาว', '0898765432', 'admin', '1234', 1, 'uploads/now.jpg'),
(4, 'วิทวัส ปุ่นโพธิ์', 'นัท', '0983232323', 'root', '12345678', 0, 'uploads/nut.jpg'),
(5, 'กฤษณะพงศ์ ค้าขาย', 'เกมส์', '0934258717', 'user3', '$2y$10$K2MO/unaA2LVKcOy/Urn7uMsYi/YJYOfHQSUI.SjfoQKhwMSkfLuy', 0, 'uploads/476631377_1753502635470163_6589883881860610277_n.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `it_inventory`
--
ALTER TABLE `it_inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job`
--
ALTER TABLE `job`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `it_inventory`
--
ALTER TABLE `it_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `job`
--
ALTER TABLE `job`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
