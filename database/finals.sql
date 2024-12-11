-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2024 at 02:20 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `finals`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `CalculateScore` (IN `examineeId` INT, IN `subjectName` VARCHAR(255))   BEGIN
    DECLARE totalCorrect INT DEFAULT 0;
    DECLARE examineeName VARCHAR(255);

    -- Fetch the examinee's name from the `users` table
    SELECT name INTO examineeName
    FROM users
    WHERE id = examineeId;

    -- Calculate the total number of correct answers
    SELECT COUNT(*) INTO totalCorrect
    FROM response r
    JOIN questions q 
        ON r.exam_id = q.exam_id AND r.subject = q.subject 
    WHERE r.name = examineeName AND q.correct_answer = TRIM(r.answer) 
          AND r.subject = subjectName;

    -- Update the `result` table with the score
    UPDATE result
    SET score = totalCorrect
    WHERE name = examineeName AND subject = subjectName;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `exam_type` enum('multiple-choice','form-type') NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `correct_answer` varchar(255) NOT NULL,
  `exam_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `subject`, `exam_type`, `question`, `answer`, `correct_answer`, `exam_id`) VALUES
(37, 'DATABASE', 'multiple-choice', 'WHAT TYPE OF DATABASE MANAGEMENT SYSTEM IS MYSQL?', 'RELATIONAL DATABASE, DOCUMENT DATABASE, GRAPH DATABASE, OBJECT-ORIENTED DATABASE', 'RELATIONAL DATABASE', 0),
(39, 'DATABASE', 'multiple-choice', ' WHICH SQL STATEMENT IS USED TO UPDATE A RECORD IN A TABLE?', 'UPDATE, ALTER, MODIFY, CHANGE', 'UPDATE', 0),
(41, 'DATABASE', 'multiple-choice', 'WHAT DOES SQL STAND FOR?', 'STRUCTURED QUERY LANGUAGE, SIMPLE QUERY LANGUAGE, SYSTEM QUERY LANGUAGE, SELECT QUERY LANGUAGE', 'STRUCTURED QUERY LANGUAGE', 0);

-- --------------------------------------------------------

--
-- Table structure for table `response`
--

CREATE TABLE `response` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `response`
--

INSERT INTO `response` (`id`, `exam_id`, `subject`, `name`, `answer`, `timestamp`) VALUES
(255, 0, 'DATABASE', 'Marjon Incarnacion Judilla', 'RELATIONAL DATABASE', '2024-12-11 09:55:44'),
(256, 0, 'DATABASE', 'Marjon Incarnacion Judilla', 'UPDATE', '2024-12-11 09:55:44'),
(257, 0, 'DATABASE', 'Marjon Incarnacion Judilla', 'STRUCTURED QUERY LANGUAGE', '2024-12-11 09:55:44'),
(258, 0, 'DATABASE', 'Myles Vega Obalang', 'RELATIONAL DATABASE', '2024-12-11 10:01:58'),
(259, 0, 'DATABASE', 'Myles Vega Obalang', 'UPDATE', '2024-12-11 10:01:58'),
(260, 0, 'DATABASE', 'Myles Vega Obalang', 'STRUCTURED QUERY LANGUAGE', '2024-12-11 10:01:58'),
(261, 0, 'DATABASE', 'Myles Vega Obalang', 'RELATIONAL DATABASE', '2024-12-11 10:02:15'),
(262, 0, 'DATABASE', 'Myles Vega Obalang', 'UPDATE', '2024-12-11 10:02:15'),
(263, 0, 'DATABASE', 'Myles Vega Obalang', ' SIMPLE QUERY LANGUAGE', '2024-12-11 10:02:15'),
(264, 0, 'DATABASE', 'Myles Vega Obalang', 'RELATIONAL DATABASE', '2024-12-11 10:02:37'),
(265, 0, 'DATABASE', 'Myles Vega Obalang', 'UPDATE', '2024-12-11 10:02:37'),
(266, 0, 'DATABASE', 'Myles Vega Obalang', 'STRUCTURED QUERY LANGUAGE', '2024-12-11 10:02:37');

-- --------------------------------------------------------

--
-- Table structure for table `result`
--

CREATE TABLE `result` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `score` int(11) NOT NULL,
  `status` enum('Pass','Fail') NOT NULL,
  `percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_questions` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `result`
--

INSERT INTO `result` (`id`, `name`, `subject`, `score`, `status`, `percentage`, `total_questions`) VALUES
(61, 'Marjon Incarnacion Judilla', 'DATABASE', 3, 'Pass', 100.00, 3),
(62, 'Myles Vega Obalang', 'DATABASE', 3, 'Pass', 100.00, 3),
(63, 'Myles Vega Obalang', 'DATABASE', 2, 'Fail', 66.67, 3),
(64, 'Myles Vega Obalang', 'DATABASE', 3, 'Pass', 100.00, 3);

--
-- Triggers `result`
--
DELIMITER $$
CREATE TRIGGER `update_percentage_before_insert` BEFORE INSERT ON `result` FOR EACH ROW BEGIN
  DECLARE num_questions INT;

  -- Get the total number of questions for the subject
  SELECT COUNT(*) INTO num_questions
  FROM `questions`
  WHERE `subject` = NEW.subject;

  -- Set total_questions and calculate percentage
  SET NEW.total_questions = num_questions;
  IF num_questions > 0 THEN
    SET NEW.percentage = (NEW.score / num_questions) * 100;
  ELSE
    SET NEW.percentage = 0;
  END IF;

  -- Update status based on percentage
  IF NEW.percentage >= 75 THEN
    SET NEW.status = 'Pass';
  ELSE
    SET NEW.status = 'Fail';
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_percentage_before_update` BEFORE UPDATE ON `result` FOR EACH ROW BEGIN
  DECLARE num_questions INT;

  -- Get the total number of questions for the subject
  SELECT COUNT(*) INTO num_questions
  FROM `questions`
  WHERE `subject` = NEW.subject;

  -- Set total_questions and calculate percentage
  SET NEW.total_questions = num_questions;
  IF num_questions > 0 THEN
    SET NEW.percentage = (NEW.score / num_questions) * 100;
  ELSE
    SET NEW.percentage = 0;
  END IF;

  -- Update status based on percentage
  IF NEW.percentage >= 75 THEN
    SET NEW.status = 'Pass';
  ELSE
    SET NEW.status = 'Fail';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_id`, `name`, `email`, `password`) VALUES
(20191336, 0, 'Marjon Incarnacion Judilla', '20191336@nbsc.edu.ph', '$2y$10$Iv/cPmn3gL491ue4.QXkeO75id2knM5sA4ek9W/ybkohds5tqD5lG'),
(20221232, 0, 'Myles Vega Obalang', '20221232@nbsc.edu.ph', '$2y$10$D8.cjB72LO4JCdHdHtvNcOqU/hTFi0A6wQp7BfDe8OJBRYYsiGk9G');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `response`
--
ALTER TABLE `response`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `result`
--
ALTER TABLE `result`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `response`
--
ALTER TABLE `response`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=267;

--
-- AUTO_INCREMENT for table `result`
--
ALTER TABLE `result`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20221233;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
