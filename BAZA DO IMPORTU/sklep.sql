-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Cze 09, 2026 at 01:43 AM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sklep`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `administrators`
--

CREATE TABLE `administrators` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `administrators`
--

INSERT INTO `administrators` (`admin_id`, `username`, `email`, `password_hash`, `created_at`) VALUES
(3, 'admin', 'ytminikasper@gmail.com', '$2y$10$KbPf9HFb/lBSKf9vxsZYxeyq8pwQW5MSZrSG9aWH4ZyHCZhTTEDS6', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `carts`
--

CREATE TABLE `carts` (
  `cart_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`cart_id`, `customer_id`, `created_at`) VALUES
(5, 15, '2026-06-08 16:58:50'),
(6, 16, '2026-06-08 17:58:52');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1 CHECK (`quantity` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`) VALUES
(1, 'Materiały budowlane', 'Kategoria zawierająca materiały budowlane'),
(2, 'Narzędzia ręczne i elektronarzędzia', 'Kategoria zawierająca narzędzia ręczne i elektronarzędzia'),
(3, 'Hydraulika i instalacje wodne', 'Kategoria zawierająca hydraulikę i instalacje wodne'),
(4, 'Elektryka i oświetlenie', 'Kategoria zawierająca elektrykę i oświetlenie'),
(5, 'Farby, lakiery i chemia budowlana', 'Kategoria zawierająca farby, lakiery i chemia budowlana'),
(6, 'Podłogi i płytki', 'Kategoria zawierająca podłogi i płytki'),
(7, 'Drzwi, okna i bramy', 'Kategoria zawierająca drzwi, okna i bramy'),
(8, 'Ogrzewanie i wentylacja', 'Kategoria zawierająca ogrzewanie i wentylację'),
(9, 'Ogród i mała architektura', 'Kategoria zawierająca ogród i małą architekturę'),
(10, 'BHP i odzież robocza', 'Kategoria zawierająca BHP i odzież roboczą');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `contact_messages`
--

CREATE TABLE `contact_messages` (
  `message_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`message_id`, `customer_id`, `first_name`, `last_name`, `email`, `phone`, `subject`, `message`, `created_at`, `is_read`) VALUES
(1, 14, 'Kacper', 'Wielgórski', 'ytminikasper@gmail.com', '511188855', 'Test', 'Test', '2026-06-05 01:30:26', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `login` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `first_name`, `last_name`, `login`, `email`, `password_hash`, `phone`, `address`, `city`, `country`, `postal_code`, `created_at`) VALUES
(15, NULL, NULL, 'admin', 'ytminikasper@gmail.com', '$2y$10$lDtuZYbK4bvoKQNht.gK9e6CYKd1UbvqeggX1U8aNGGXy7WeS656m', NULL, NULL, NULL, NULL, NULL, '2026-06-08 16:58:38'),
(16, 'Kacper', '', 'KaiGreenWolf', 'kacper.wielgorskii2005@gmail.com', '$2y$10$kNJHQf97nurIHY/bFKjHhOQZ5wZJmxxD49nPTAndKkMS8j0fHBSLi', '', '', '', NULL, '', '2026-06-08 17:51:32');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `favorites`
--

CREATE TABLE `favorites` (
  `favorite_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`favorite_id`, `customer_id`, `product_id`, `created_at`) VALUES
(1, 16, 12, '2026-06-08 17:05:15'),
(2, 15, 13, '2026-06-08 19:25:42'),
(3, 15, 14, '2026-06-08 19:25:43');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'New',
  `payment_method` varchar(50) DEFAULT 'Unknown',
  `shipping_cost` decimal(10,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `order_date`, `total_amount`, `status`, `payment_method`, `shipping_cost`, `notes`) VALUES
(20, 16, '2026-06-08 18:00:35', 274.00, 'Pending', 'blik', 35.00, 'Klient: Kacper Wielgórski, Email: kacper.wielgorskii2005@gmail.com, Tel: 511188855, Adres: Akacjowa 14, 08-110 Siedlce.  ExtOrderID: ORDER_6a26e7231eca06.45307580'),
(21, NULL, '2026-06-08 18:36:17', 528.00, 'Pending', 'payu', 0.00, 'Klient: test test, Email: kacper.wielgorskii2005@gmail.com, Tel: 511188855, Adres: test 14, 08-110 test.  ExtOrderID: ORDER_6a26ef811c51f1.16734875'),
(22, 16, '2026-06-08 18:44:48', 999.00, 'Processing', 'payu', 0.00, 'Klient: KKK Kooo, Email: kacper.wielgorskii2005@gmail.com, Tel: 511188855, Adres: test 14, 11-111 ttt.  ExtOrderID: ORDER_6a26f18094ab50.79320027'),
(23, 16, '2026-06-08 18:46:06', 74.00, 'Pending', 'transfer', 35.00, 'Klient: hdxdhtfrdfrgdehr ggegrde, Email: kacper.wielgorskii2005@gmail.com, Tel: 511188855, Adres: rge 13, 33-333 ggg.  ExtOrderID: ORDER_6a26f1ce40e041.06880194'),
(24, 16, '2026-06-08 19:06:02', 1837.00, 'Pending', 'blik', 0.00, 'Klient: dfgdsefs fsdegfsde, Email: kacper.wielgorskii2005@gmail.com, Tel: 511188855, Adres: ihk 15, 88-888 ulojb.  ExtOrderID: ORDER_6a26f67abd74a1.69968388');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `price_each` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price_each`) VALUES
(23, 20, 13, 1, 289.00),
(24, 21, 13, 2, 289.00),
(25, 22, 14, 1, 150.00),
(26, 22, 12, 1, 899.00),
(27, 23, 16, 1, 89.00),
(28, 24, 16, 1, 89.00),
(29, 24, 12, 2, 899.00);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `customer_id`, `token`, `expires_at`, `used`) VALUES
(16, 16, 'acb6c5948051666a2ea8b650f14573b43eed6b402be2802f1ab6b0ec7e7aa35f', '2026-06-08 18:22:29', 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pending_users`
--

CREATE TABLE `pending_users` (
  `id` int(11) NOT NULL,
  `login` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pending_users`
--

INSERT INTO `pending_users` (`id`, `login`, `email`, `password_hash`, `token`, `created_at`) VALUES
(14, 'admin', 'admin@02.pl', '$2y$10$EnEAfVskEccR/KprcjBEYexWHwrty/mWVMqMhqJMbMYy9g68EXndW', '12ceeb54f11a91a8aaa03d35c209021586eca072f353c1722cc70a5b1313d0b2', '2026-06-08 16:48:59'),
(15, 'admin', 'ytminikasper@gmail.com', '$2y$10$oOqN1iZc57ZLQEh7w9NsQuApIFP4oHP67v1vVJDKw/2G1FYk6.9My', 'ba4da1331c005e5144847216dea22cf186cb23c5249d0aea90904d7858c0132c', '2026-06-08 16:49:48');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `product_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `image_url` varchar(255) DEFAULT 'https://placehold.co/600x400',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `product_name`, `description`, `price`, `stock_quantity`, `image_url`, `created_at`) VALUES
(12, 2, 'Wiertarka Bosch', 'Znakomita wiertarka', 899.00, 30, '../../src/img/products/wiertarka-bosch', '2026-05-29 15:46:02'),
(13, 2, 'Klucze YATO', 'Szef powiedział, że YATO pier..', 289.00, 30, '../../src/img/products/klucze-yato', '2026-05-29 15:46:02'),
(14, 2, 'Lutownica', 'Cieplutka lutownica', 150.00, 10, '../../src/img/products/lutownica', '2026-05-29 15:46:02'),
(15, 2, 'Multimetr', 'Miernik mocy (Nie kopie prądem)', 89.00, 120, '../../src/img/products/multimetr', '2026-05-29 15:46:02'),
(16, 2, 'Tarcza do piły', 'Przetnie wszystko', 89.00, 100, '../../src/img/products/tarcza', '2026-05-29 15:46:02'),
(17, 2, 'Przedłużacz', 'Idziesz 3 kilometry i końca nie widać..', 69.99, 80, '../../src/img/products/przedluzacz', '2026-05-29 15:46:02'),
(18, 10, 'Kurtka odblaskowa', 'BHP\'owiec to sie za głowe złapie..', 239.00, 120, '../../src/img/products/kurtka', '2026-05-29 15:46:02'),
(19, 4, 'Żarówka Philips', 'Lampa jak.. może nie bedziemy mowili?', 59.99, 1200, '../../src/img/products/lampa', '2026-05-29 15:46:02'),
(20, 5, 'Farba Magnat', 'Najlepsza na rynku', 49.99, 120, '../../src/img/products/farba', '2026-05-29 15:46:02'),
(21, 2, 'Koparka Bobcat', 'No, co tu więcej mówić?', 20000.00, 1, '../../src/img/products/bobcat', '2026-05-29 15:46:02'),
(22, 7, 'Brama WIŚNIEWSKI', 'Konkurencyjny na rynku.. niezawodny', 9000.00, 5, '../../src/img/products/brama', '2026-05-29 15:46:02'),
(23, 3, 'Przepychacz do zlewu', 'Idealny na głowę', 29.99, 1230, '../../src/img/products/przepychacz', '2026-05-29 15:46:02'),
(24, 1, 'Betonowy kloc', 'No co? Przecież wiemy, że go chcesz :)', 150.00, 1900, '../../src/img/products/kloc', '2026-05-29 15:46:02'),
(25, 9, 'Wąż ogrodowy (szlauf)', 'Tu nie chodzi o kobietę..', 49.99, 0, '../../src/img/products/szlauf', '2026-05-29 15:46:02'),
(26, 8, 'Wentylacja mechaniczna', 'Wypędzi ostatniego pierda z pokoju w mgnieniu oka', 4999.00, 50, '../../src/img/products/wentylacja', '2026-05-29 15:46:02'),
(27, 6, 'Płytka podłogowa', 'Położysz wszędzie, gdzie twoje ego elegancji sobie zażyczy.', 12.99, 12000, '../../src/img/products/plytka', '2026-05-29 15:46:02');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `administrators`
--
ALTER TABLE `administrators`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksy dla tabeli `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indeksy dla tabeli `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeksy dla tabeli `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indeksy dla tabeli `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indeksy dla tabeli `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksy dla tabeli `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`favorite_id`),
  ADD UNIQUE KEY `unique_customer_product` (`customer_id`,`product_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeksy dla tabeli `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indeksy dla tabeli `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeksy dla tabeli `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indeksy dla tabeli `pending_users`
--
ALTER TABLE `pending_users`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administrators`
--
ALTER TABLE `administrators`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `favorite_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `pending_users`
--
ALTER TABLE `pending_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`cart_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
