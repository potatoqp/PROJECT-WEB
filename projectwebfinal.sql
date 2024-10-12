-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 11, 2024 at 05:51 PM
-- Server version: 8.0.31
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projectwebfinal`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `user_id`) VALUES
(3, 1),
(1, 2),
(2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `description`) VALUES
(1, 'We need help!'),
(2, 'Help us, we are gonna die!!!'),
(3, 'save our souls'),
(4, 'We need some stuff here!!'),
(5, 'heeeelp'),
(6, 'we need money'),
(7, 'we need cash'),
(8, 'oioioi'),
(9, 'heyo');

-- --------------------------------------------------------

--
-- Table structure for table `announcement_items`
--

CREATE TABLE `announcement_items` (
  `id` int NOT NULL,
  `announcement_id` int DEFAULT NULL,
  `item_id` int DEFAULT NULL,
  `needed_quantity` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcement_items`
--

INSERT INTO `announcement_items` (`id`, `announcement_id`, `item_id`, `needed_quantity`) VALUES
(1, 1, 5, 14),
(2, 2, 1, 5),
(3, 2, 10, 10),
(4, 3, 11, 2),
(5, 4, 25, 12),
(6, 4, 3, 7),
(7, 5, 3, 3),
(8, 5, 4, 4),
(9, 5, 5, 6),
(10, 6, 22, 2),
(11, 7, 9, 12),
(12, 8, 23, 12),
(13, 9, 20, 3),
(14, 9, 21, 4),
(15, 9, 23, 4);

-- --------------------------------------------------------

--
-- Table structure for table `base`
--

CREATE TABLE `base` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `base`
--

INSERT INTO `base` (`id`, `user_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `cargo`
--

CREATE TABLE `cargo` (
  `id` int NOT NULL,
  `vol_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cargo`
--

INSERT INTO `cargo` (`id`, `vol_id`) VALUES
(11, 5),
(16, 13),
(17, 3),
(18, 22);

-- --------------------------------------------------------

--
-- Table structure for table `cargo_items`
--

CREATE TABLE `cargo_items` (
  `id` int NOT NULL,
  `cargo_id` int NOT NULL,
  `item_id` int NOT NULL,
  `item_quantity` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cargo_items`
--

INSERT INTO `cargo_items` (`id`, `cargo_id`, `item_id`, `item_quantity`) VALUES
(12, 11, 5, 8),
(13, 11, 9, 4),
(24, 11, 10, 5),
(25, 11, 13, 10),
(26, 11, 4, 4),
(29, 11, 2, 6),
(31, 16, 5, 7),
(32, 16, 6, 5),
(35, 17, 1, 7),
(36, 17, 4, 13),
(37, 16, 4, 11),
(38, 16, 7, 16),
(39, 16, 20, 5),
(40, 16, 10, 30),
(41, 11, 1, 4),
(42, 11, 11, 2),
(43, 17, 25, 12),
(44, 17, 3, 10),
(45, 17, 5, 20),
(46, 16, 1, 10),
(47, 16, 9, 12),
(48, 17, 23, 12),
(49, 18, 25, 12),
(50, 18, 3, 7);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `category_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`) VALUES
(1, 'Food'),
(2, 'Drinks'),
(3, 'Clothes'),
(4, 'First Aid'),
(5, 'Electronics'),
(6, 'Household'),
(9, 'Santising'),
(10, 'Toys'),
(12, 'Books');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int NOT NULL,
  `category_id` int NOT NULL,
  `item_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `category_id`, `item_name`, `quantity`) VALUES
(1, 2, 'Water', 36),
(2, 2, 'Coca-Cola', 24),
(3, 2, 'Fanta-Lemon', 117),
(4, 1, 'Bread', 49),
(5, 1, 'Peanuts', 263),
(6, 1, 'Potatoes', 170),
(7, 3, 'T-Shirt', 33),
(8, 3, 'Shorts', 3),
(9, 3, 'Sneakers', 8),
(10, 4, 'Bandages', 333),
(11, 4, 'Covid-Vaccine', 23),
(12, 6, 'Table', 12),
(13, 1, 'Steak', 71),
(16, 2, 'Orange-Juice', 20),
(18, 6, 'test', 3),
(19, 9, 'Antiseptic', 23),
(20, 2, 'Coffee', 1),
(21, 5, 'Vacuum', 4),
(22, 5, 'SSD', 13),
(23, 3, 'Hat', 4),
(24, 3, 'test2', 3),
(25, 10, 'Lego Set', 35);

-- --------------------------------------------------------

--
-- Table structure for table `item_details`
--

CREATE TABLE `item_details` (
  `item_id` int NOT NULL,
  `detail_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `detail_value` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_details`
--

INSERT INTO `item_details` (`item_id`, `detail_name`, `detail_value`) VALUES
(1, 'volume', '1.5l'),
(1, 'pack size', '6'),
(2, 'volume', '0.5l'),
(3, 'volume', '1l'),
(4, 'weight', '500g'),
(5, 'weight', '100g'),
(5, 'pack size', '3'),
(6, 'weight', '1.5kg'),
(7, 'size', 'L'),
(8, 'size', 'S'),
(9, 'size', '44,5'),
(10, 'type', 'sticky'),
(10, 'pack size', '15'),
(11, 'company', 'pfizer'),
(12, 'material', 'wood'),
(13, 'weight', '500g'),
(13, 'cut', 'neck'),
(16, 'volume', '500ml'),
(16, 'pack-size', '6'),
(18, 'volume', 'dadad'),
(19, 'volume', '50ml'),
(20, 'brand', 'Lavazza'),
(20, 'volume', '100ml'),
(21, 'brand', 'Xiaomi'),
(22, 'brand', 'Samsung'),
(23, 'brand', 'Nike'),
(24, 'sddsds', 'dsdsds'),
(25, 'pieces', '1000'),
(25, 'theme', 'star wars');

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `id` int NOT NULL,
  `announcement_id` int DEFAULT NULL,
  `donator_id` int DEFAULT NULL,
  `date_made` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','ongoing','completed') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `date_accepted` datetime DEFAULT NULL,
  `date_completed` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`id`, `announcement_id`, `donator_id`, `date_made`, `status`, `date_accepted`, `date_completed`) VALUES
(9, 1, 8, '2024-08-25 22:50:19', 'pending', NULL, NULL),
(11, 3, 8, '2024-08-26 12:46:21', 'ongoing', '2024-08-26 22:27:25', NULL),
(14, 4, 18, '2024-08-26 18:53:52', 'completed', '2024-08-26 20:49:45', '2024-08-26 19:57:10'),
(15, 3, 18, '2024-08-26 22:37:16', 'ongoing', '2024-09-08 20:03:44', NULL),
(16, 2, 8, '2024-09-05 20:50:10', 'pending', NULL, NULL),
(17, 2, 20, '2024-09-07 12:37:17', 'completed', '2024-09-07 17:58:00', '2024-09-07 17:02:59'),
(18, 3, 20, '2024-09-07 12:37:18', 'completed', '2024-09-07 15:14:40', '2024-09-07 14:16:22'),
(19, 2, 18, '2024-09-07 15:23:18', 'completed', '2024-09-07 18:15:10', '2024-09-07 17:15:20'),
(20, 1, 18, '2024-09-07 15:23:19', 'ongoing', '2024-09-07 18:07:27', NULL),
(21, 4, 20, '2024-09-07 15:51:33', 'completed', '2024-09-07 15:51:53', '2024-09-07 14:53:02'),
(22, 1, 20, '2024-09-07 15:57:29', 'completed', '2024-09-07 15:57:43', '2024-09-07 14:57:55'),
(23, 5, 20, '2024-09-07 18:05:53', 'completed', '2024-09-07 18:06:03', '2024-09-07 17:07:41'),
(24, 6, 20, '2024-09-07 18:09:55', 'completed', '2024-09-07 18:10:24', '2024-09-07 17:10:32'),
(25, 7, 20, '2024-09-07 18:12:32', 'completed', '2024-09-07 18:14:37', '2024-09-07 17:14:53'),
(26, 8, 20, '2024-09-07 18:20:28', 'completed', '2024-09-07 18:20:47', '2024-09-07 18:21:04'),
(27, 4, 21, '2024-09-08 20:03:03', 'completed', '2024-09-08 20:03:31', '2024-09-08 20:04:10'),
(30, 3, 21, '2024-09-11 18:44:24', 'pending', NULL, NULL),
(31, 8, 18, '2024-09-11 18:45:55', 'pending', NULL, NULL),
(32, 9, 20, '2024-09-11 18:49:08', 'pending', NULL, NULL);

--
-- Triggers `offers`
--
DELIMITER $$
CREATE TRIGGER `check_for_active_request` BEFORE INSERT ON `offers` FOR EACH ROW BEGIN
    IF NEW.status IN ('pending', 'ongoing') THEN
        IF EXISTS (
            SELECT 1
            FROM requests
            WHERE user_id = NEW.donator_id
              AND status IN ('pending', 'ongoing')
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Cannot insert a new offer (you have an active request).';
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `check_offer` BEFORE INSERT ON `offers` FOR EACH ROW BEGIN
    DECLARE count INT;
    
    SELECT COUNT(*) INTO count
    FROM offers
    WHERE announcement_id = NEW.announcement_id AND donator_id = NEW.donator_id;
    
    IF count > 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'You have already made an offer for that';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int NOT NULL,
  `item_id` int NOT NULL,
  `people` int NOT NULL,
  `user_id` int NOT NULL,
  `date_made` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','ongoing','completed') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `date_accepted` datetime DEFAULT NULL,
  `date_completed` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `item_id`, `people`, `user_id`, `date_made`, `status`, `date_accepted`, `date_completed`) VALUES
(12, 21, 4, 7, '2024-08-25 22:02:13', 'pending', NULL, NULL),
(13, 22, 6, 7, '2024-08-25 22:02:13', 'pending', NULL, NULL),
(14, 13, 1, 14, '2024-08-26 13:48:46', 'completed', '2024-09-07 11:03:46', '2024-09-07 14:17:11'),
(15, 2, 6, 14, '2024-08-26 13:48:46', 'completed', '2024-08-26 15:01:55', '2024-08-26 14:02:01'),
(16, 22, 2, 14, '2024-08-26 13:48:46', 'completed', '2024-08-26 14:58:20', '2024-08-26 14:00:58'),
(20, 5, 12, 15, '2024-08-26 19:39:51', 'ongoing', '2024-09-08 20:03:47', NULL),
(21, 20, 7, 15, '2024-08-26 19:39:51', 'completed', '2024-09-07 18:10:20', '2024-09-07 17:11:32'),
(22, 21, 2, 7, '2024-09-05 19:39:36', 'ongoing', '2024-09-07 12:35:27', NULL),
(24, 10, 3, 19, '2024-09-05 19:52:48', 'completed', '2024-09-05 20:54:05', '2024-09-05 19:54:23'),
(25, 13, 4, 19, '2024-09-05 19:52:48', 'completed', '2024-09-07 15:17:29', '2024-09-07 14:17:37'),
(26, 20, 5, 19, '2024-09-05 19:52:48', 'ongoing', '2024-09-07 15:19:20', NULL),
(27, 19, 2, 7, '2024-09-07 10:01:38', 'pending', NULL, NULL),
(28, 20, 1, 7, '2024-09-07 10:01:38', 'pending', NULL, NULL),
(29, 16, 5, 15, '2024-09-08 19:05:28', 'pending', NULL, NULL),
(30, 19, 2, 15, '2024-09-08 19:05:28', 'pending', NULL, NULL),
(31, 13, 3, 19, '2024-09-08 19:07:05', 'ongoing', '2024-09-08 20:18:23', NULL),
(32, 7, 4, 14, '2024-09-09 11:09:25', 'completed', '2024-09-09 12:10:00', '2024-09-09 12:10:55'),
(33, 8, 4, 14, '2024-09-09 11:09:25', 'ongoing', '2024-09-09 12:11:04', NULL),
(34, 7, 3, 23, '2024-09-11 17:50:26', 'pending', NULL, NULL),
(35, 20, 2, 23, '2024-09-11 17:50:26', 'pending', NULL, NULL),
(36, 22, 1, 23, '2024-09-11 17:50:26', 'pending', NULL, NULL),
(37, 25, 8, 23, '2024-09-11 17:50:26', 'pending', NULL, NULL);

--
-- Triggers `requests`
--
DELIMITER $$
CREATE TRIGGER `check_for_active_offer` BEFORE INSERT ON `requests` FOR EACH ROW BEGIN
    
    IF NEW.status IN ('pending', 'ongoing') THEN

        IF EXISTS (
            SELECT 1
            FROM offers
            WHERE donator_id = NEW.user_id
              AND status IN ('pending', 'ongoing')
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Cannot insert a new request (you have an active offer).';
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int NOT NULL,
  `volunteer_id` int NOT NULL,
  `request_id` int DEFAULT NULL,
  `offer_id` int DEFAULT NULL,
  `date_made` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('ongoing','completed') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'ongoing'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `volunteer_id`, `request_id`, `offer_id`, `date_made`, `status`) VALUES
(32, 3, 16, NULL, '2024-08-26 11:58:20', 'completed'),
(33, 5, 15, NULL, '2024-08-26 12:01:55', 'completed'),
(56, 3, NULL, 14, '2024-08-26 17:49:45', 'completed'),
(59, 3, NULL, 11, '2024-08-26 19:27:25', 'ongoing'),
(61, 13, 24, NULL, '2024-09-05 17:54:05', 'completed'),
(63, 5, 14, NULL, '2024-09-07 08:03:46', 'completed'),
(65, 5, 22, NULL, '2024-09-07 09:35:27', 'ongoing'),
(69, 5, NULL, 18, '2024-09-07 12:14:40', 'completed'),
(70, 5, 25, NULL, '2024-09-07 12:17:29', 'completed'),
(71, 5, 26, NULL, '2024-09-07 12:19:20', 'ongoing'),
(72, 3, NULL, 21, '2024-09-07 12:51:53', 'completed'),
(73, 3, NULL, 22, '2024-09-07 12:57:43', 'completed'),
(74, 13, NULL, 17, '2024-09-07 14:58:00', 'completed'),
(75, 3, NULL, 23, '2024-09-07 15:06:03', 'completed'),
(76, 3, NULL, 20, '2024-09-07 15:07:27', 'ongoing'),
(77, 13, 21, NULL, '2024-09-07 15:10:20', 'completed'),
(78, 13, NULL, 24, '2024-09-07 15:10:24', 'completed'),
(79, 13, NULL, 25, '2024-09-07 15:14:37', 'completed'),
(80, 13, NULL, 19, '2024-09-07 15:15:10', 'completed'),
(81, 3, NULL, 26, '2024-09-07 15:20:47', 'completed'),
(82, 22, NULL, 27, '2024-09-08 17:03:31', 'completed'),
(83, 22, NULL, 15, '2024-09-08 17:03:44', 'ongoing'),
(84, 22, 20, NULL, '2024-09-08 17:03:47', 'ongoing'),
(85, 3, 31, NULL, '2024-09-08 17:18:23', 'ongoing'),
(86, 13, 32, NULL, '2024-09-09 09:10:00', 'completed'),
(88, 13, 33, NULL, '2024-09-09 09:11:04', 'ongoing');

--
-- Triggers `tasks`
--
DELIMITER $$
CREATE TRIGGER `max_tasks` BEFORE INSERT ON `tasks` FOR EACH ROW BEGIN
    DECLARE task_count INT;
    
    SELECT COUNT(*) INTO task_count
    FROM tasks
    WHERE volunteer_id = NEW.volunteer_id AND status = 'ongoing';
    
    IF task_count >= 4 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'The maximum is 4 ongoing tasks per volunteer';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `fullname` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `fullname`, `phone`, `password`, `latitude`, `longitude`) VALUES
(1, 'base', 'Head Quarters', '0000000000', 'base2321', 38.28991962207087, 21.788624592185307),
(2, 'potatoqp', 'Dimitris Vamvakas', '6848395999', 'KALAMATA1313', 38.2899, 21.7891),
(3, 'lkigai', 'Kwstas Stasinopoulos', '98989876', 'paokara2321', 38.289364, 21.78989),
(4, 'cheesedreamer', 'Marios Zagklas', '131314535', 'patra2321', 38.289949, 21.787874),
(5, 'ponawpsuxika', 'Zafeiria Tsala', '31869313', 'amaliada1313', 38.290345, 21.789622),
(6, 'perrrr', 'Periklis Fragos', '6978685', 'kalamata3131', 38.290143, 21.789853),
(7, 'vxiliki', 'Vasiliki Karzi', '669787869', 'periklisfragos32', 38.289047, 21.787541),
(8, 'jojo69', 'George Loukos', '6858578575', 'loukos32', 38.290016, 21.787412),
(10, 'testguy2', 'Nikolas Kolas', '6978787', 'testtest', 38.289789, 21.787),
(13, 'pappy', 'Iwannhs Papapetrou', '69696969696', 'pao1313', 38.291552, 21.788631),
(14, 'novagambler', 'Swthrhs Gewrgakopoulos', '69785687', 'dernw13', 38.292713, 21.780181),
(15, 'monkeyman', 'Thanasis Thanasopoulos', '697858748', 'monkey55', 38.293244, 21.792498),
(18, 'Potatohead', 'Iwannhs Patatas', '69584763', 'potato32', 38.302733, 21.788979),
(19, 'skulos21', 'Bob Sfougarakhs', '69784321', 'spongebob', 38.28001, 21.789129),
(20, 'nikolakis', 'Nikos Nikolas', '69787868', 'nikosnikos', 38.278827, 21.748896),
(21, 'helloworld', 'Orestis Nikolaidhs', '6955847', 'helloworld', 38.263533, 21.824512),
(22, 'superman', 'Clark Kent', '69348374', 'superman13', 38.246436, 21.735077),
(23, 'bombaclat', 'Bomba Clat', '6978685432', 'bobos32', 38.300982, 21.806316);

-- --------------------------------------------------------

--
-- Table structure for table `volunteers`
--

CREATE TABLE `volunteers` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `volunteers`
--

INSERT INTO `volunteers` (`id`, `user_id`) VALUES
(1, 3),
(2, 5),
(3, 13),
(4, 22);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ADMIN` (`user_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcement_items`
--
ALTER TABLE `announcement_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcement_id` (`announcement_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `base`
--
ALTER TABLE `base`
  ADD PRIMARY KEY (`id`),
  ADD KEY `BASE` (`user_id`);

--
-- Indexes for table `cargo`
--
ALTER TABLE `cargo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cargo_items`
--
ALTER TABLE `cargo_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ITEMCARGO` (`cargo_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `item_details`
--
ALTER TABLE `item_details`
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcement_id` (`announcement_id`),
  ADD KEY `donator_id` (`donator_id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_task` (`volunteer_id`,`request_id`,`offer_id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `offer_id` (`offer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `volunteers`
--
ALTER TABLE `volunteers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `VOLUNTEER` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `announcement_items`
--
ALTER TABLE `announcement_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `base`
--
ALTER TABLE `base`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cargo`
--
ALTER TABLE `cargo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `cargo_items`
--
ALTER TABLE `cargo_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `volunteers`
--
ALTER TABLE `volunteers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `ADMIN` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `announcement_items`
--
ALTER TABLE `announcement_items`
  ADD CONSTRAINT `announcement_items_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `announcement_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `base`
--
ALTER TABLE `base`
  ADD CONSTRAINT `BASE` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cargo_items`
--
ALTER TABLE `cargo_items`
  ADD CONSTRAINT `ITEMCARGO` FOREIGN KEY (`cargo_id`) REFERENCES `cargo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `item_details`
--
ALTER TABLE `item_details`
  ADD CONSTRAINT `item_details_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Constraints for table `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `offers_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`),
  ADD CONSTRAINT `offers_ibfk_2` FOREIGN KEY (`donator_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`volunteer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`),
  ADD CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`offer_id`) REFERENCES `offers` (`id`);

--
-- Constraints for table `volunteers`
--
ALTER TABLE `volunteers`
  ADD CONSTRAINT `VOLUNTEER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
