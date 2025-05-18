-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2025 at 11:25 AM
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
-- Database: `piyubank_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_number` varchar(20) NOT NULL,
  `account_type` varchar(50) NOT NULL DEFAULT 'Savings',
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `user_id`, `account_number`, `account_type`, `balance`, `created_at`) VALUES
(18, 22, '1298643097', 'Savings', 9400.00, '2025-05-02 08:03:03'),
(19, 23, '8570300822', 'Savings', 10000.00, '2025-05-02 09:48:07'),
(20, 24, '4784852408', 'Savings', 19340.00, '2025-05-02 13:51:59'),
(21, 25, '9742905017', 'Savings', 10000.00, '2025-05-03 09:52:23'),
(22, 26, '1892089916', 'Savings', 10000.00, '2025-05-03 10:43:25'),
(23, 27, '2972737406', 'Savings', 15000.00, '2025-05-08 13:15:49'),
(24, 28, '4720428019', 'Savings', 10000.00, '2025-05-08 14:12:23'),
(25, 29, '4688928546', 'Savings', 10000.00, '2025-05-09 01:23:20'),
(26, 30, '8261395370', 'Savings', 10000.00, '2025-05-09 06:32:12'),
(27, 31, '2615222159', 'Savings', 5000.00, '2025-05-13 07:07:54'),
(28, 34, '1183209185', 'Savings', 10000.00, '2025-05-13 07:50:29'),
(29, 35, '5297878803', 'Savings', 10000.00, '2025-05-13 07:54:48');

-- --------------------------------------------------------

--
-- Table structure for table `bill_payments`
--

CREATE TABLE `bill_payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bill_type` varchar(50) NOT NULL,
  `account_number` varchar(100) NOT NULL,
  `account_name` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--

CREATE TABLE `logins` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logins`
--

INSERT INTO `logins` (`id`, `user_id`, `login_time`) VALUES
(1, 26, '2025-05-03 10:43:42'),
(2, 27, '2025-05-08 13:16:11'),
(3, 28, '2025-05-08 14:12:41'),
(4, 29, '2025-05-09 01:24:01'),
(5, 30, '2025-05-09 06:33:27'),
(6, 31, '2025-05-13 07:08:19'),
(7, 31, '2025-05-13 07:20:22'),
(8, 31, '2025-05-13 07:38:07'),
(9, 32, '2025-05-13 07:41:17'),
(10, 32, '2025-05-13 07:41:30'),
(11, 33, '2025-05-13 07:44:44'),
(12, 33, '2025-05-13 07:46:39'),
(13, 35, '2025-05-13 07:55:16'),
(14, 35, '2025-05-13 08:43:29');

-- --------------------------------------------------------

--
-- Table structure for table `profile_changes`
--

CREATE TABLE `profile_changes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `field_changed` varchar(100) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `changed_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profile_changes`
--

INSERT INTO `profile_changes` (`id`, `user_id`, `field_changed`, `old_value`, `new_value`, `changed_at`) VALUES
(1, 25, 'name', 'faceless void', 'faceless ', '2025-05-03 10:25:33'),
(2, 25, 'name', 'faceless ', 'fa', '2025-05-03 10:27:47'),
(3, 25, 'security_question', 'game', NULL, '2025-05-03 10:27:47'),
(4, 25, 'email', 'fv@gmail.com', 'fv1@gmail.com', '2025-05-03 10:28:34'),
(5, 25, 'security_question', '', NULL, '2025-05-03 10:28:34');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `type` enum('Deposit','Withdraw','Transfer','Payment') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `bill_type` enum('Electricity','Water','Internet') CHARACTER SET utf16 COLLATE utf16_general_ci DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `account_id`, `type`, `amount`, `bill_type`, `description`, `created_at`) VALUES
(38, 18, 'Withdraw', -200.00, NULL, 'Withdrawal made', '2025-05-02 08:03:56'),
(39, 18, 'Deposit', 500.00, NULL, 'Deposit made', '2025-05-02 08:04:14'),
(40, 18, 'Payment', -900.00, 'Internet', 'Paid Internet bill: ', '2025-05-02 08:04:32'),
(41, 20, 'Payment', -243.00, 'Water', 'Paid Water bill: ', '2025-05-02 13:52:39'),
(42, 20, 'Withdraw', -233.00, NULL, 'Withdrawal made', '2025-05-02 13:52:58'),
(43, 20, 'Deposit', 9816.00, NULL, 'Deposit made', '2025-05-02 13:53:13'),
(44, 27, 'Transfer', -5000.00, NULL, 'Transfer to Account 2972737406: allowance', '2025-05-13 07:28:33'),
(45, 23, 'Deposit', 5000.00, NULL, 'Received from Transfer: allowance', '2025-05-13 07:28:33');

-- --------------------------------------------------------

--
-- Table structure for table `transfer_logs`
--

CREATE TABLE `transfer_logs` (
  `id` int(11) NOT NULL,
  `sender_account_id` int(11) NOT NULL,
  `recipient_account_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `failed_attempts` int(11) DEFAULT 0,
  `lock_until` datetime DEFAULT NULL,
  `mobile` varchar(15) NOT NULL,
  `security_question` varchar(255) NOT NULL,
  `security_answer` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `failed_attempts`, `lock_until`, `mobile`, `security_question`, `security_answer`) VALUES
(22, 'goku', 'g1@gmail.com', '$2y$10$DEoA1HZ5GhATZ8y8N8wiPu2j8uzTdL.ITBu9//PCrnF1J1ukgzFDq', '2025-05-02 08:03:03', 0, NULL, '09222222226', 'goku surname', '$2y$10$FmaEEttfbK0XVQ7teNO9Z.I8IzwScVf6J2W6r6QDjh2k92RO0M.iK'),
(23, 'bronny', 'b@gmail.com', '$2y$10$V0DcSGS8BeXmerNgDdB5y.DkYmP827ydiWp/pRIlDxXZwHmdNw/rK', '2025-05-02 09:48:07', 0, NULL, '09222222227', 'papa', '$2y$10$dMZvjIt3//Rpy9iCv2cVOu5xe9cG60eZhlPt4xe8zeMrtVk2dKQue'),
(24, 'onib', 'onib@gmail.com', '$2y$10$tyVKVd/2lZjkINwH4/CeW.Fl5aflo2ICf2aQg71MHbf0uGBRBKZ2S', '2025-05-02 13:51:59', 0, NULL, '09222222228', 'sports', '$2y$10$wEV3UxYZxj5TRRntrsLQsu80dPikvVnHlRaHcIUvILquMTB5I70hq'),
(25, 'fa', 'fv1@gmail.com', '$2y$10$sweNxuUP2VaUfXAjGqpdyOnCIv5TgOqeBNDVfh0Hsz7P.Eef8LF9G', '2025-05-03 09:52:23', 0, NULL, '09222222221', '', '$2y$10$IlKnN2zQnlt3p0PutL2AjePy3ejxpGCH/64nZe8YAAs3sB6JkMpBu'),
(26, 'sniper', 'sniper@gmail.com', '$2y$10$Qr5nAtUM3CIYZYB/JkozYuLnT0bJn8yOzkdFOXy.cR5IKMwkkg4Ty', '2025-05-03 10:43:25', 0, NULL, '09222222220', 'lane', '$2y$10$3opyYsO539jupMvyRt0qteZLFyKfZjJNcytnLG.yX4suJrrkCAIZq'),
(27, 'shane acabado', 'sha@gmail.com', '$2y$10$cW5aZxT6goKotMMd.183D.11tcA1uruuYYsLiYsAwpVfS8m.3Llmi', '2025-05-08 13:15:49', 2, NULL, '09452340031', 'favorite drink', '$2y$10$e8pJWYiIj62rWYwMitZeFeO1IT22glQ9hMWlvmLqcfOWKCVrzZePu'),
(28, 'shane acabado', 'sss@gmail.com', '$2y$10$hCJluXwCVm1hh8TRMvsRveynUp.NNSchuao6PwhjgMJ7rjs40pR8K', '2025-05-08 14:12:23', 0, NULL, '09452340031', 'favorite drink', '$2y$10$JGCa/a4eqqfBMoPxz1vJYuIM4t.kLf3iPry1Fd390mmD2Od0yE0Y6'),
(29, 'shane acabado', 'shaa@gmail.com', '$2y$10$Z3oWTPscy4j9mkzCkC84Aekjs1QBVpymReywW4q/1/tvNIT1NpgDq', '2025-05-09 01:23:20', 0, NULL, '09452340031', 'favorite snack', '$2y$10$5/fwo3QQr0aRVXZ2vrmGUOZ1uDzmjMvqt22QaVawCvG7ozHs0yDUm'),
(30, 'cj cueto', 'cjcueto@gmail.com', '$2y$10$jcOzRlDqfNiCYw374FM1.uc/MXUqip.O9keL5liLk80rhQhw4seBO', '2025-05-09 06:32:12', 0, NULL, '09345678911', 'game', '$2y$10$tIv5QrluceLCe/I3Y6ro2uGyw8fy/V43iARbsz3Vn8LoSYbk6FRbW'),
(31, 'zeann balmes', 'zeann0@gmail.com', '$2y$10$I9KPO6jcfh3b2BPavO2wrOae2v8ytaFHVLtfKeXh/oz5WoyRfnzFm', '2025-05-13 07:07:54', 2, NULL, '09123456789', 'game', '$2y$10$yOPxgCYQk0dsD52T2ufUA.5dggKbJQuq4ehZLGMEHUHYOJnEDbpmy'),
(32, 'hazel tallud', 'hazel@gmail.com', '$2y$10$uHXmquSUyeVHngetD0oGV.aDdd29pqM0gkG1lwFVOsn2u6ING98pe', '2025-05-13 07:40:56', 0, NULL, '09987654321', 'food', '$2y$10$9m5u1HSfV7rdu0soOHjFpe/9fhwi.t7y5Xnm.KIZ0mjGNHOnXCafy'),
(33, 'luiz manzano', 'luiz10@gmail.com', '$2y$10$6kAakWuemc8qpzR11gkJFeKvUy/Lx3DzwHcvr3Bct9cEZfjCfZkF.', '2025-05-13 07:44:16', 0, NULL, '09129812981', 'games', '$2y$10$qcKBblUpHtEYhM5KsOjw.e7IUH4J3xNNsSmp4sYF9jiRS9ijDV7hi'),
(34, 'hazel ann', 'hazela@gmail.com', '$2y$10$ybFhSL0jJhdbZR6bk1DT1eJ9dOu1adNQfkwlnglgbCmW/aPjUF2Ve', '2025-05-13 07:50:29', 0, NULL, '09909090909', 'drink ', '$2y$10$/bWHFBOd1Pd0zeWDKXlmiO5TDxqx9jbcGka2g/Cb3fDGO0vDsoePi'),
(35, 'hazel ann tallud', 'hazeann@gmail.com', '$2y$10$l.WlwzxT.FD2jjdyaGqcWejFkxqml5EQEq3ZA.2uVGqx3bNXiepeO', '2025-05-13 07:54:48', 0, NULL, '09331234567', 'things', '$2y$10$i2QQ9Fm2eBS02F9Tpo4v2OpfDB8PWe6SNskFKjHfJAqFjDvxmieVW');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_number` (`account_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bill_payments`
--
ALTER TABLE `bill_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `logins`
--
ALTER TABLE `logins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `profile_changes`
--
ALTER TABLE `profile_changes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `transfer_logs`
--
ALTER TABLE `transfer_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_account_id` (`sender_account_id`),
  ADD KEY `recipient_account_id` (`recipient_account_id`);

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
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `bill_payments`
--
ALTER TABLE `bill_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `logins`
--
ALTER TABLE `logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `profile_changes`
--
ALTER TABLE `profile_changes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `transfer_logs`
--
ALTER TABLE `transfer_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bill_payments`
--
ALTER TABLE `bill_payments`
  ADD CONSTRAINT `bill_payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `logins`
--
ALTER TABLE `logins`
  ADD CONSTRAINT `logins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `profile_changes`
--
ALTER TABLE `profile_changes`
  ADD CONSTRAINT `profile_changes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transfer_logs`
--
ALTER TABLE `transfer_logs`
  ADD CONSTRAINT `transfer_logs_ibfk_1` FOREIGN KEY (`sender_account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transfer_logs_ibfk_2` FOREIGN KEY (`recipient_account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
