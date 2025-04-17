-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 17, 2025 at 03:07 PM
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
-- Database: `rent_tracking`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_account`
--

CREATE TABLE `admin_account` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` text DEFAULT NULL,
  `resetToken` text DEFAULT NULL,
  `role` tinyint(1) NOT NULL DEFAULT 2 COMMENT '1=admin\r\n2=staff',
  `date_added` date NOT NULL DEFAULT current_timestamp(),
  `address` text NOT NULL,
  `phonenumber` text DEFAULT NULL,
  `status_archived` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=unarchived 2= archived	'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_account`
--

INSERT INTO `admin_account` (`admin_id`, `name`, `email`, `username`, `password`, `resetToken`, `role`, `date_added`, `address`, `phonenumber`, `status_archived`) VALUES
(1, 'admin', 'jpantoja@usa.edu.ph', 'admin', '$2y$10$gLAl36tAIQDDSJjHjO18nOkr2agi/sW4qY9YDj9.z9fQUTnf4olOO', 'ea9954673fbce5ceced9c3e8356659f2859935b0c00cfffa57790f728a10b03cc03bc55cd364913c9ca741c44ab3d7993aeb', 1, '2025-04-10', '', NULL, 1),
(6, 'Janelle', 'jpantoja@usa.edu.ph', 'Staff', '$2y$10$Lt2oFwjA2WXvKMzTAPmcfu5bMD4jCXoQz9Y04H/8BTDqUwxqq70ZW', NULL, 2, '2025-04-15', 'aaa', '686987', 1);

-- --------------------------------------------------------

--
-- Table structure for table `stall_slots`
--

CREATE TABLE `stall_slots` (
  `stall_slots_id` int(11) NOT NULL,
  `tenantname` varchar(150) DEFAULT NULL,
  `monthly` float(10,2) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phonenumber` int(11) DEFAULT NULL,
  `location` tinyint(1) DEFAULT NULL COMMENT '1=USA BED Campus\r\n2=USA Main Campus Permanent\r\n3=USA Main Kiosks',
  `date_added` date DEFAULT current_timestamp(),
  `status` int(1) DEFAULT 1 COMMENT '1=unarchived\r\n2= archived',
  `manager_name` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stall_slots`
--

INSERT INTO `stall_slots` (`stall_slots_id`, `tenantname`, `monthly`, `email`, `phonenumber`, `location`, `date_added`, `status`, `manager_name`) VALUES
(26, 'Krispy Kings', 5000.00, 'jpantoja@usa.edu.ph', 2147483647, 1, '2025-04-15', 1, 'Hezekiah'),
(28, 'Mcdonald', 5000.00, 'jpantoja@usa.edu.ph', 2147483647, 3, '2025-04-15', 1, 'Hezekiah'),
(29, 'kopi', 5000.00, 'jpantoja@usa', 2147483647, 2, '2025-04-15', 1, 'angelie'),
(30, 'Admin', 6000.00, 'danielreysoma@gmail.com', 2147483647, 2, '2025-04-17', 1, 'TEster');

-- --------------------------------------------------------

--
-- Table structure for table `stall_slots_file`
--

CREATE TABLE `stall_slots_file` (
  `stall_slots_file_id` int(11) NOT NULL,
  `stall_slots_id` int(11) DEFAULT NULL,
  `stall_file` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_history`
--

CREATE TABLE `transaction_history` (
  `transaction_history_id` int(11) NOT NULL,
  `stall_slots_id` int(11) DEFAULT NULL,
  `balance` float(10,2) DEFAULT NULL,
  `amount_paid` float(10,2) DEFAULT NULL,
  `penalty` float(10,2) DEFAULT NULL,
  `duedate` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '1=Complete\r\n2=Incomplete\r\n3=Overdue',
  `completed_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_history`
--

INSERT INTO `transaction_history` (`transaction_history_id`, `stall_slots_id`, `balance`, `amount_paid`, `penalty`, `duedate`, `status`, `completed_date`) VALUES
(184, 26, 905.00, 100.00, 5.00, '2025-04-15', 3, NULL),
(186, 26, 1405.00, 500.00, 905.00, '2025-04-16', 3, NULL),
(187, 26, 0.00, 2405.00, 1405.00, '2025-05-17', 1, '2025-04-17'),
(196, 26, 1000.00, 0.00, 0.00, '2025-06-16', 2, NULL),
(197, 30, 1000.00, NULL, 0.00, '2025-03-16', 3, NULL),
(198, 30, 1500.00, 500.00, 1000.00, '2025-04-16', 3, NULL),
(199, 30, 0.00, 2500.00, 1500.00, '2025-05-16', 1, '2025-04-17'),
(200, 30, 1000.00, 0.00, 0.00, '2025-06-15', 2, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_account`
--
ALTER TABLE `admin_account`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `stall_slots`
--
ALTER TABLE `stall_slots`
  ADD PRIMARY KEY (`stall_slots_id`);

--
-- Indexes for table `stall_slots_file`
--
ALTER TABLE `stall_slots_file`
  ADD PRIMARY KEY (`stall_slots_file_id`),
  ADD KEY `stall_slots_id` (`stall_slots_id`);

--
-- Indexes for table `transaction_history`
--
ALTER TABLE `transaction_history`
  ADD PRIMARY KEY (`transaction_history_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_account`
--
ALTER TABLE `admin_account`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `stall_slots`
--
ALTER TABLE `stall_slots`
  MODIFY `stall_slots_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `stall_slots_file`
--
ALTER TABLE `stall_slots_file`
  MODIFY `stall_slots_file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `transaction_history`
--
ALTER TABLE `transaction_history`
  MODIFY `transaction_history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `stall_slots_file`
--
ALTER TABLE `stall_slots_file`
  ADD CONSTRAINT `stall_slots_id` FOREIGN KEY (`stall_slots_id`) REFERENCES `stall_slots` (`stall_slots_id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `create_next_due_entry` ON SCHEDULE EVERY 10 SECOND STARTS '2025-04-17 20:44:54' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DECLARE done INT DEFAULT 0;

    DECLARE cur_stall_id INT;
    DECLARE cur_trans_id INT;
    DECLARE cur_balance DECIMAL(10,2);
    DECLARE cur_paid DECIMAL(10,2);
    DECLARE cur_penalty DECIMAL(10,2);
    DECLARE cur_duedate DATETIME;
    DECLARE cur_status INT;

    DECLARE new_balance DECIMAL(10,2);
    DECLARE new_duedate DATETIME;

    DECLARE cur CURSOR FOR
        SELECT t.transaction_history_id, t.stall_slots_id, t.balance, t.amount_paid, t.penalty, t.duedate, t.status
        FROM transaction_history t
        INNER JOIN (
            SELECT stall_slots_id, MAX(transaction_history_id) AS max_id
            FROM transaction_history
            GROUP BY stall_slots_id
        ) latest ON t.transaction_history_id = latest.max_id;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO cur_trans_id, cur_stall_id, cur_balance, cur_paid, cur_penalty, cur_duedate, cur_status;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- If the due date is overdue, or status is 1 (paid)
        IF cur_duedate < NOW() OR cur_status = 1 THEN

            -- If status was 2 (ongoing), mark it as 3 (overdue)
            IF cur_status = 2 THEN
                UPDATE transaction_history
                SET status = 3
                WHERE transaction_history_id = cur_trans_id;
            END IF;

            -- Compute the new balance safely
            SET new_balance = ((IFNULL(cur_balance, 0) + IFNULL(cur_paid, 0)) - IFNULL(cur_penalty, 0)) + IFNULL(cur_balance, 0);

            -- Set new due date as 30 days from the previous due date
            SET new_duedate = DATE_ADD(cur_duedate, INTERVAL 30 DAY);

            -- Insert new entry
            INSERT INTO transaction_history (
                stall_slots_id, 
                balance, 
                amount_paid, 
                penalty, 
                duedate, 
                status, 
                completed_date
            ) VALUES (
                cur_stall_id,
                new_balance,
                0.00,
                cur_balance,  -- the previous balance as the new penalty
                new_duedate,
                2,  -- status 2 (ongoing)
                NULL  -- completed date
            );
        END IF;

    END LOOP;

    CLOSE cur;

END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
