-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 20, 2023 at 09:10 AM
-- Server version: 8.0.30-cll-lve
-- PHP Version: 7.2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;



-- --------------------------------------------------------

--
-- Table structure for table `categorys`
--

CREATE TABLE `categorys` (
  `category_id` int NOT NULL,
  `category_name` text NOT NULL,
  `category_status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `cates`
--

CREATE TABLE `cates` (
  `cate_id` int NOT NULL,
  `name` text NOT NULL,
  `category_id` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `myorder`
--

CREATE TABLE `myorder` (
  `order_id` int NOT NULL,
  `user_id` text NOT NULL,
  `retail` text NOT NULL,
  `status` text NOT NULL,
  `service` text NOT NULL,
  `order_create` text,
  `last_check` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `api_order` text NOT NULL,
  `provider` text NOT NULL,
  `status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `percent`
--

CREATE TABLE `percent` (
  `id` int NOT NULL,
  `percent` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `percent`
--

INSERT INTO `percent` (`id`, `percent`) VALUES
(1, '40');

-- --------------------------------------------------------

--
-- Table structure for table `providers`
--

CREATE TABLE `providers` (
  `id` int NOT NULL,
  `api_url` varchar(300) NOT NULL,
  `api_key` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `referal`
--

CREATE TABLE `referal` (
  `id` text NOT NULL,
  `referal` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `send`
--

CREATE TABLE `send` (
  `send_id` int NOT NULL,
  `time1` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `time2` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `start_id` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `stop_id` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `admin_id` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `message_id` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `reply_markup` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `step` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `time3` text NOT NULL,
  `time4` text NOT NULL,
  `time5` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int NOT NULL,
  `service_edit` text NOT NULL,
  `category_id` text NOT NULL,
  `service_price` text NOT NULL,
  `service_api` text NOT NULL,
  `api_service` text NOT NULL,
  `api_currency` text NOT NULL,
  `service_type` text NOT NULL,
  `api_detail` text NOT NULL,
  `service_name` text NOT NULL,
  `service_desc` text NOT NULL,
  `service_min` text NOT NULL,
  `service_max` text NOT NULL,
  `service_status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `site_style` text NOT NULL,
  `site_theme` text NOT NULL,
  `ref_status` text NOT NULL,
  `referal` text NOT NULL,
  `orders` text NOT NULL,
  `kabinet` text NOT NULL,
  `start` text NOT NULL,
  `id` int NOT NULL,
  `status` text NOT NULL,
  `bonus` text NOT NULL,
  `payme_id` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`site_style`, `site_theme`, `ref_status`, `referal`, `orders`, `kabinet`, `start`, `id`, `status`, `bonus`, `payme_id`) VALUES
('theme8.0', 'Eternity', 'off', 'MTAw', '4pyFIEJ1eXVydG1hIHFhYnVsIHFpbGluZGkKCvCfhpQgQnV5dXJ0bWEgSURzaTogPHByZT57b3JkZXJ9PC9wcmU+', '8J+StSBIaXNvYmluZ2l6OiB7YmFsYW5jZX0gc2/igJhtCvCflLwgS2lyaXRnYW4gcHVsbGFyaW5naXo6IHtvdXRpbmd9IHNv4oCYbQrwn4aUIElEOiA8Y29kZT57aWR9PC9jb2RlPg==', '8J+WpSBBc29zaXkgbWVueXVkYXNpeg==', 1, 'active', '4', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `referal` text NOT NULL,
  `outing` text NOT NULL,
  `user_id` int NOT NULL,
  `id` text NOT NULL,
  `status` text NOT NULL,
  `balance` text NOT NULL,
  `api_key` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categorys`
--
ALTER TABLE `categorys`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `cates`
--
ALTER TABLE `cates`
  ADD PRIMARY KEY (`cate_id`);

--
-- Indexes for table `myorder`
--
ALTER TABLE `myorder`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `percent`
--
ALTER TABLE `percent`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `send`
--
ALTER TABLE `send`
  ADD PRIMARY KEY (`send_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categorys`
--
ALTER TABLE `categorys`
  MODIFY `category_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cates`
--
ALTER TABLE `cates`
  MODIFY `cate_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `myorder`
--
ALTER TABLE `myorder`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `percent`
--
ALTER TABLE `percent`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `providers`
--
ALTER TABLE `providers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `send`
--
ALTER TABLE `send`
  MODIFY `send_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
