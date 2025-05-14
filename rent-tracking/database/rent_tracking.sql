-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: rent_tracking
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin_account`
--

DROP TABLE IF EXISTS `admin_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_account` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` text DEFAULT NULL,
  `resetToken` text DEFAULT NULL,
  `role` tinyint(1) NOT NULL DEFAULT 2 COMMENT '1=admin\r\n2=staff\r\n3=stall owner',
  `date_added` date NOT NULL DEFAULT current_timestamp(),
  `address` text NOT NULL COMMENT 'staff=address\r\nstallowner=stallname',
  `phonenumber` text DEFAULT NULL,
  `status_archived` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=unarchived 2= archived	',
  `date_archived` date DEFAULT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_account`
--

LOCK TABLES `admin_account` WRITE;
/*!40000 ALTER TABLE `admin_account` DISABLE KEYS */;
INSERT INTO `admin_account` VALUES (1,'admin','jpantoja@usa.edu.ph','admin','$2y$10$gLAl36tAIQDDSJjHjO18nOkr2agi/sW4qY9YDj9.z9fQUTnf4olOO','ea9954673fbce5ceced9c3e8356659f2859935b0c00cfffa57790f728a10b03cc03bc55cd364913c9ca741c44ab3d7993aeb',1,'2025-04-10','',NULL,1,NULL),(6,'Janelle','jpantoja@usa.edu.ph','Staff','$2y$10$Lt2oFwjA2WXvKMzTAPmcfu5bMD4jCXoQz9Y04H/8BTDqUwxqq70ZW',NULL,2,'2025-04-15','aaa','686987',1,NULL),(40,'Hoshimachi Suisei','danielreysoma@gmail.com','Lorem','$2y$10$DOm4Mqhh/1/28qAg8HiSQ.GIbYgsBJ0ylCJEymW7kRsN4EzIYuAp.',NULL,3,'2025-05-09','Lorem\'s Ipsum','09667332990',1,NULL),(41,'Hoshimachi Suisei','dannylreyes36@gmail.com','LoremThe2nd','$2y$10$cxvsUpEftuGWwxZvGND1iOZAar4F2x9eato3IordNnU0ZDMsbr/aO',NULL,3,'2025-05-09','Tester Stall','09667332990',1,NULL);
/*!40000 ALTER TABLE `admin_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stall_slots`
--

DROP TABLE IF EXISTS `stall_slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stall_slots` (
  `stall_slots_id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_account_id` int(11) NOT NULL,
  `tenantname` varchar(150) DEFAULT NULL,
  `monthly` float(10,2) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phonenumber` varchar(20) DEFAULT NULL,
  `location` tinyint(1) DEFAULT NULL COMMENT '1=USA BED Campus\r\n2=USA Main Campus Permanent\r\n3=USA Main Kiosks',
  `date_added` date DEFAULT current_timestamp(),
  `date_edited` date DEFAULT NULL,
  `edited_by` int(11) DEFAULT NULL,
  `date_archived` date DEFAULT current_timestamp(),
  `status` int(1) DEFAULT 1 COMMENT '1=unarchived\r\n2= archived',
  `manager_name` varchar(150) DEFAULT NULL,
  `confirmed` tinyint(1) DEFAULT 0,
  `confirmation_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`stall_slots_id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stall_slots`
--

LOCK TABLES `stall_slots` WRITE;
/*!40000 ALTER TABLE `stall_slots` DISABLE KEYS */;
INSERT INTO `stall_slots` VALUES (69,40,'Lorem\'s Ipsum',10000.00,'danielreysoma@gmail.com','09667332990',1,'2025-05-09','2025-05-09',1,'2025-05-09',1,'Hoshimachi Suisei',1,'c24d2033c7325e385ee84276b568b6ae'),(70,41,'Tester Stall',10000.00,'dannylreyes36@gmail.com','09667332990',1,'2025-05-09',NULL,NULL,'2025-05-09',1,'Hoshimachi Suisei',1,'3dfc85429b68ed30025ae78cd8ad5265');
/*!40000 ALTER TABLE `stall_slots` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER after_stall_insert
AFTER INSERT ON stall_slots
FOR EACH ROW
BEGIN
    -- Only create a new entry for unarchived stalls (status = 1)
    IF NEW.status = 1 THEN
        -- Insert a new transaction history entry for the newly registered stall
        INSERT INTO transaction_history (
            stall_slots_id, 
            balance, 
            amount_paid, 
            penalty, 
            duedate, 
            status, 
            completed_date
        ) VALUES (
            NEW.stall_slots_id,            -- Stall ID from the new stall record
            NEW.monthly,                   -- Use the 'monthly' value from stall_slots as the balance
            0.00,                          -- Amount paid (starts as 0)
            0.00,                          -- Penalty (starts as 0)
            DATE_ADD(NOW(), INTERVAL 30 DAY),  -- Set due date to 30 days from now
            2,                             -- Status 2 (ongoing)
            NULL                            -- No completed date for new transactions
        );
    END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `stall_slots_file`
--

DROP TABLE IF EXISTS `stall_slots_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stall_slots_file` (
  `stall_slots_file_id` int(11) NOT NULL AUTO_INCREMENT,
  `stall_slots_id` int(11) DEFAULT NULL,
  `stall_file` text DEFAULT NULL,
  PRIMARY KEY (`stall_slots_file_id`),
  KEY `stall_slots_id` (`stall_slots_id`),
  CONSTRAINT `stall_slots_id` FOREIGN KEY (`stall_slots_id`) REFERENCES `stall_slots` (`stall_slots_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stall_slots_file`
--

LOCK TABLES `stall_slots_file` WRITE;
/*!40000 ALTER TABLE `stall_slots_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `stall_slots_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_history`
--

DROP TABLE IF EXISTS `transaction_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_history` (
  `transaction_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `stall_slots_id` int(11) DEFAULT NULL,
  `balance` float(10,2) DEFAULT NULL,
  `amount_paid` float(10,2) DEFAULT NULL,
  `penalty` float(10,2) DEFAULT NULL,
  `duedate` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '1=Complete\r\n2=Incomplete\r\n3=Overdue',
  `completed_date` date DEFAULT NULL,
  `transaction_edited_by` int(11) DEFAULT NULL,
  `transaction_date_edited` date DEFAULT NULL,
  PRIMARY KEY (`transaction_history_id`)
) ENGINE=InnoDB AUTO_INCREMENT=391 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_history`
--

LOCK TABLES `transaction_history` WRITE;
/*!40000 ALTER TABLE `transaction_history` DISABLE KEYS */;
INSERT INTO `transaction_history` VALUES (379,63,10000.00,0.00,0.00,'2025-06-08',2,NULL,NULL,NULL),(380,64,6000.00,0.00,0.00,'2025-06-08',2,NULL,NULL,NULL),(381,65,1123414.00,0.00,0.00,'2025-06-08',2,NULL,NULL,NULL),(382,66,10000.00,0.00,0.00,'2025-06-08',2,NULL,NULL,NULL),(383,67,6000.00,0.00,0.00,'2025-06-08',2,NULL,NULL,NULL),(384,68,10000.00,0.00,0.00,'2025-06-08',2,NULL,NULL,NULL),(385,69,0.00,10000.00,0.00,'2025-04-15',1,'2025-05-14',1,'2025-05-14'),(386,70,10000.00,0.00,0.00,'2025-05-16',2,NULL,1,'2025-05-14'),(390,69,10000.00,0.00,0.00,'2025-05-15',2,NULL,NULL,NULL);
/*!40000 ALTER TABLE `transaction_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'rent_tracking'
--
/*!50106 SET @save_time_zone= @@TIME_ZONE */ ;
/*!50106 DROP EVENT IF EXISTS `create_next_due_entry` */;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = utf8mb4 */ ;;
/*!50003 SET character_set_results = utf8mb4 */ ;;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = 'SYSTEM' */ ;;
/*!50106 CREATE*/ /*!50117 DEFINER=`root`@`localhost`*/ /*!50106 EVENT `create_next_due_entry` ON SCHEDULE EVERY 10 SECOND STARTS '2025-04-19 17:05:12' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DECLARE done INT DEFAULT 0;

    DECLARE cur_stall_id INT;
    DECLARE cur_trans_id INT;
    DECLARE cur_balance DECIMAL(10,2);
    DECLARE cur_paid DECIMAL(10,2);
    DECLARE cur_penalty DECIMAL(10,2);
    DECLARE cur_duedate DATETIME;
    DECLARE cur_status INT;

    DECLARE monthly_rent DECIMAL(10,2);
    DECLARE computed_penalty DECIMAL(10,2);
    DECLARE new_balance DECIMAL(10,2);
    DECLARE new_duedate DATETIME;
    DECLARE new_status INT;

    DECLARE cur CURSOR FOR
        SELECT t.transaction_history_id, t.stall_slots_id, t.balance, t.amount_paid, t.penalty, t.duedate, t.status
        FROM transaction_history t
        INNER JOIN (
            SELECT stall_slots_id, MAX(transaction_history_id) AS max_id
            FROM transaction_history
            GROUP BY stall_slots_id
        ) latest ON t.transaction_history_id = latest.max_id
        INNER JOIN stall_slots s ON t.stall_slots_id = s.stall_slots_id
        WHERE s.status = 1;  -- Only unarchived stalls

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO cur_trans_id, cur_stall_id, cur_balance, cur_paid, cur_penalty, cur_duedate, cur_status;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Get monthly rent
        SELECT monthly INTO monthly_rent FROM stall_slots WHERE stall_slots_id = cur_stall_id;

        -- Calculate penalty only if overdue
        IF cur_status = 3 THEN
            SET computed_penalty = (0.02 * IFNULL(monthly_rent, 0)) + IFNULL(cur_balance, 0);
        ELSE
            SET computed_penalty = 0.00;
        END IF;

        -- Calculate new balance: monthly rent + penalty (if overdue)
        SET new_balance = IFNULL(monthly_rent, 0) + computed_penalty;

        -- Set new due date: 30 days from the previous due date
        SET new_duedate = DATE_ADD(cur_duedate, INTERVAL 30 DAY);

        -- Determine status for the new transaction based on the new due date
        IF new_duedate < NOW() THEN
            SET new_status = 3;  -- Overdue if the new due date is in the past
        ELSE
            SET new_status = 2;  -- Ongoing if the new due date is in the future
        END IF;

        -- Insert a new transaction only if the bill is paid or overdue
        IF cur_status IN (1, 3) THEN
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
                computed_penalty,
                new_duedate,
                new_status,  -- Status is dynamically set to 2 or 3 based on due date
                NULL
            );
        END IF;

    END LOOP;

    CLOSE cur;

END */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
DELIMITER ;
/*!50106 SET TIME_ZONE= @save_time_zone */ ;

--
-- Dumping routines for database 'rent_tracking'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-14 16:55:26
