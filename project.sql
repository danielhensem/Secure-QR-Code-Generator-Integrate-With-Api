-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 02, 2025 at 05:21 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project`
--

-- --------------------------------------------------------

--
-- Table structure for table `accessrecord`
--

CREATE TABLE `accessrecord` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `typeaccess` varchar(50) NOT NULL,
  `qr_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

CREATE TABLE `activity` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `aid` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`aid`, `name`, `email`, `password`) VALUES
(1, 'admin', 'admin123@gmail.com', '$2y$10$rj2k7xzJY5GDyzUZu8m7ve8cc5eVVo8VAp.Ip0zMElk0yAEGzf0GW');

-- --------------------------------------------------------

--
-- Table structure for table `code`
--

CREATE TABLE `code` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `qr_code_id` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `feedback_text` text NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `id` int(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `status` int(10) NOT NULL COMMENT '0 - request\r\n1 - accept\r\n2 - block',
  `timestamp` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mycart`
--

CREATE TABLE `mycart` (
  `cartid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `cartqty` int(11) NOT NULL DEFAULT 1,
  `carttitle` varchar(30) NOT NULL,
  `cartdesc` varchar(100) NOT NULL,
  `cartprice` int(10) NOT NULL,
  `cartimg` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(255) NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `receiver_id` int(255) NOT NULL,
  `status` int(255) DEFAULT NULL COMMENT '1: not-display 2: has displayed',
  `timestamp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product-catagory`
--

CREATE TABLE `product-catagory` (
  `cid` int(11) NOT NULL,
  `cname` varchar(30) NOT NULL,
  `slug` varchar(30) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0= enable,1= Disable',
  `issubset` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product-catagory`
--

INSERT INTO `product-catagory` (`cid`, `cname`, `slug`, `status`, `issubset`) VALUES
(1, 'Dashboard', 'mobiles', 0, 0),
(2, 'Generate', 'laptops', 0, 0),
(3, 'Scan', 'smart watches', 0, 0),
(4, 'Friend', 'Audio items', 0, 1),
(5, 'Activity', '', 0, 0),
(6, 'Feedback', '0', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `pid` int(11) NOT NULL,
  `pname` varchar(30) NOT NULL,
  `cid` int(11) NOT NULL,
  `subid` int(11) NOT NULL DEFAULT 0,
  `price` int(8) NOT NULL,
  `dis-price` int(8) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `pimg` varchar(100) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0= enable,1= Disable'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `qr_code`
--

CREATE TABLE `qr_code` (
  `id` int(11) NOT NULL,
  `qr_code_image` longtext NOT NULL,
  `hash_number` varchar(64) NOT NULL,
  `status` varchar(50) DEFAULT 'generated',
  `uploaded_filename` varchar(255) DEFAULT '',
  `username` varchar(100) DEFAULT 'anonymous',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `qr_email_requests`
--

CREATE TABLE `qr_email_requests` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `qr_id` int(11) NOT NULL,
  `status` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `qr_secondlayer`
--

CREATE TABLE `qr_secondlayer` (
  `ide` int(11) NOT NULL,
  `encrypted_content` longblob DEFAULT NULL,
  `encrypted_key` text DEFAULT NULL,
  `digest` char(64) NOT NULL,
  `token` varchar(255) NOT NULL,
  `salt` varchar(255) DEFAULT NULL,
  `nonce` varchar(255) DEFAULT NULL,
  `nonce_key` varchar(255) DEFAULT NULL,
  `tag` varchar(32) DEFAULT NULL,
  `time` int(255) DEFAULT NULL,
  `id` int(11) DEFAULT NULL,
  `id_description` varchar(255) DEFAULT NULL,
  `file_type` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `scan_status` tinyint(1) DEFAULT 0,
  `scan_result` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `qr_security`
--

CREATE TABLE `qr_security` (
  `id` int(11) NOT NULL,
  `qr_filename` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `otp_enabled` tinyint(1) DEFAULT NULL,
  `otp_email` varchar(255) DEFAULT NULL,
  `qr_image` longblob DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `qr_shares`
--

CREATE TABLE `qr_shares` (
  `id` int(11) NOT NULL,
  `qr_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `shared_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sub-catagory`
--

CREATE TABLE `sub-catagory` (
  `subid` int(11) NOT NULL,
  `subname` varchar(30) NOT NULL,
  `cid` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub-catagory`
--

INSERT INTO `sub-catagory` (`subid`, `subname`, `cid`, `status`) VALUES
(1, 'HeadPhone', 4, 0),
(30, 'mouse', 13, 0),
(31, 'controler', 13, 0),
(32, 'consoles', 13, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user-address`
--

CREATE TABLE `user-address` (
  `adrid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `number` varchar(50) NOT NULL,
  `name` varchar(30) NOT NULL,
  `address` varchar(200) NOT NULL,
  `city` varchar(30) NOT NULL,
  `state` varchar(30) NOT NULL,
  `zip` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user-address`
--

INSERT INTO `user-address` (`adrid`, `uid`, `number`, `name`, `address`, `city`, `state`, `zip`) VALUES
(39, 44, '3435464656', 'test', 'sdsfdsf', 'dfsf', 'fsfd', 43434),
(40, 57, '3435454656', 'makima', 'sdsdada', 'surat', 'gujrat', 0),
(41, 57, '3434543535', 'makima', 'fdsf', 'fdf', 'dsf', 3232232),
(42, 57, '9824489823', 'makima', '478, Nai Sadak\r\nChandni chowk', 'Surat', 'Gujrat', 395009),
(43, 58, '0177649451', 'Aluya', 'UUM', 'Changlun', 'Kedah', 6010);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phrase` varchar(255) DEFAULT NULL,
  `timestamp` varchar(255) DEFAULT NULL,
  `status` int(2) NOT NULL DEFAULT 0 COMMENT '0= enable,1= Disable',
  `reset_token` varchar(128) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `password`, `email`, `phrase`, `timestamp`, `status`, `reset_token`, `reset_expires`) VALUES
(53, 'jainex', '$2y$10$PcgJcnHEsZklrtS41LUdXeTVoa/fei6bMNHjk5YYKsi3FEZP7JqIm', 'jainexp017@gmail.com', '', '2025-08-05 14:22:41', 0, NULL, NULL),
(56, 'luffy', '$2y$10$ARb0uU5So.eIgIgTt0d7ce0UITGcd2ibEA3HIo.LCaxSkEe4qegEC', 'luffy@gmail.com', '', '2025-08-19 07:53:09', 0, NULL, NULL),
(57, 'makima', '$2y$10$Cq4YdLOVcuPby5.GA1AkTuhQsXOlqFnw25SpwDHfp.8hBP2Dr8zzq', 'makima@gmail.com', '', '2025-08-27 22:11:55', 0, NULL, NULL),
(58, 'Aluy', '$2y$10$SoGP6csWX..RJCdpsHb7DOdvS8z22CFA1CEmQFpaQv5TR73UzS8Qy', 'danielhkl118@gmail.com', 'sayasukamakan', '2025-07-03 11:45:33', 0, NULL, NULL),
(59, 'dqi', '$2a$12$EiQ9Zybai60BeASM046F8evKNm3l1oiFkZ.TPgTzTzGON988Jx07e', 'dnqib118@gmail.com', 'danieljanganlahputusasaberkawandenganaku', '2025-07-14 04:28:49', 0, NULL, NULL),
(60, 'halim', '$2y$10$0l/y2FKaDg2p.wrzB6/sy.Frai45neY9pgeoAVFsag1cLL7vcs/pq', 'admin123@gmail.com', '', '2025-07-21 19:12:07', 0, NULL, NULL),
(61, 'admin-1', '$2y$10$lbPrQeG4EHkmLorO61lIj.WSkMgxvYNojIpyxttCyUSrKCZ1qNnYi', 'admin11@gmail.com', '', '2025-07-30 08:59:24', 0, NULL, NULL),
(72, 'zamba', '$2y$10$WvWHK.W6Gyju8mDheV/mauX4W5ep67Q2kRQBwdrPXUWupUSmcJlWy', 'nqib118@gmail.com', 'O)Le36zb!Bp-0<h7)<Ak3U=}LPv=-!h?$wT$jxVg', '2025-06-12 16:37:0', 0, NULL, NULL),
(76, 'Muhammad Najmi bin R', '$2y$10$uA72rbzYu6rAsnK54f8vUe77vIazKpn2zI2yZ2vLa90TmZugx8iCK', 'potab87133@obirah.com', 'Tf4Lr[L+!kYYq6UyL4TB(f%}rH*2&_gY)D+K?z%r', '2025-09-18 12:05:30', 0, NULL, NULL),
(77, 'saad', '$2y$10$9jpc3rj/rzigYJ8.h5Z47OBpnzgQy4flVSYyQmUvrs3SY6M4Cizru', 'fick@gmail.com', '+67FeZF1jSj{[]%Va}^*rkpyhN%O$H5wBb24mnZe', '2025-09-18 21:19:10', 0, NULL, NULL),
(78, 'Amin Asyraf bin Amin', '$2y$10$ey9PwjdfuvN1DDGL48BykOUVhc29IVrw6j1PrdzCWJnSwIlDqcXZi', 'aminasyraf3073@gmail.com', 'A$H2n<Lp{D2Im3E2^Z1{G6P+wEyGNX*9loVqfS$L', '2025-09-26 07:35:37', 0, NULL, NULL),
(79, 'Saad', '$2y$10$T9Wa7HGu9qO1Avy5T76UYu89ahSh45x8wWINEge1VKTLUXL.ZKLge', 'saad11@gmail.com', '7Wa+MRkUu5+7V{-jR-LyrfW5Wqp-Eyk=iokI3-w{', '2025-10-19 12:41:13', 0, NULL, NULL),
(80, 'gabriel', '$2y$10$HYUz/r/uP86A/QRZjvcxDe6kF7p./2fckSfrOMeCS1NkxYMHX8Mcm', 'gabriel123@gmail.com', '6Kcoh_5Y2A&GXe0[ca#SnD@2p3-a&$7wNu]I$xmN', '2025-10-19 12:55:03', 0, NULL, NULL),
(81, 'rodri', '$2y$10$u04G4G/rWj7adLYUnL8vVevj8/pAqR9t8MMFu/vhMEyKxljCwfSTC', 'Rodricus.Karas@InboxOrigin.com', 'DnaN=c7gv1L7SDYw#I2il!$-0ks&0{mhB)C)H35U', '2025-10-28 00:04:06', 0, NULL, NULL),
(82, 'saad', '$2y$10$EgVZRXF3C8VqQuQbeZjuQuDeO8//E2tysNNrRMqCpBhhpskLYDbSO', 'asdada@gmail.com', '>VtoZ&3McDqDm1!vL2X=vcU2-k4te(QUeC)fmPL#', '2025-10-28 00:23:55', 0, NULL, NULL),
(83, 'Aqib', '$2y$10$oPjlfzvl7ZogQH1.9T4sHO9Kyv8sllaccuC05jXMVpXolah47yhJW', 'mikela12@pinevalleytel.com', 'O-&K)qY)KE#z$Sz72q7K)h!7$c@A2Jj[!ybU(%yi', '2025-10-31 11:05:15', 0, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accessrecord`
--
ALTER TABLE `accessrecord`
  ADD PRIMARY KEY (`id`),
  ADD KEY `qr_id` (`qr_id`);

--
-- Indexes for table `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`aid`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `code`
--
ALTER TABLE `code`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `mycart`
--
ALTER TABLE `mycart`
  ADD PRIMARY KEY (`cartid`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product-catagory`
--
ALTER TABLE `product-catagory`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`pid`);

--
-- Indexes for table `qr_code`
--
ALTER TABLE `qr_code`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qr_email_requests`
--
ALTER TABLE `qr_email_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qr_secondlayer`
--
ALTER TABLE `qr_secondlayer`
  ADD PRIMARY KEY (`ide`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `qr_security`
--
ALTER TABLE `qr_security`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `qr_shares`
--
ALTER TABLE `qr_shares`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sub-catagory`
--
ALTER TABLE `sub-catagory`
  ADD PRIMARY KEY (`subid`);

--
-- Indexes for table `user-address`
--
ALTER TABLE `user-address`
  ADD PRIMARY KEY (`adrid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accessrecord`
--
ALTER TABLE `accessrecord`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=326;

--
-- AUTO_INCREMENT for table `activity`
--
ALTER TABLE `activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=306;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `aid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `code`
--
ALTER TABLE `code`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `mycart`
--
ALTER TABLE `mycart`
  MODIFY `cartid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `product-catagory`
--
ALTER TABLE `product-catagory`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `qr_code`
--
ALTER TABLE `qr_code`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `qr_email_requests`
--
ALTER TABLE `qr_email_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `qr_secondlayer`
--
ALTER TABLE `qr_secondlayer`
  MODIFY `ide` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=240;

--
-- AUTO_INCREMENT for table `qr_security`
--
ALTER TABLE `qr_security`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2226;

--
-- AUTO_INCREMENT for table `qr_shares`
--
ALTER TABLE `qr_shares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `sub-catagory`
--
ALTER TABLE `sub-catagory`
  MODIFY `subid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `user-address`
--
ALTER TABLE `user-address`
  MODIFY `adrid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accessrecord`
--
ALTER TABLE `accessrecord`
  ADD CONSTRAINT `accessrecord_ibfk_1` FOREIGN KEY (`qr_id`) REFERENCES `qr_secondlayer` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `qr_secondlayer`
--
ALTER TABLE `qr_secondlayer`
  ADD CONSTRAINT `qr_secondlayer_ibfk_1` FOREIGN KEY (`id`) REFERENCES `qr_security` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `qr_security`
--
ALTER TABLE `qr_security`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
