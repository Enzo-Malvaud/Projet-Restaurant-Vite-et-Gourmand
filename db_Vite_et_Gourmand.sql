-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : mer. 08 avr. 2026 à 16:20
-- Version du serveur : 8.0.45
-- Version de PHP : 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `db_Vite_et_Gourmand`
--

-- --------------------------------------------------------

--
-- Structure de la table `adresse`
--

CREATE TABLE `adresse` (
  `id` int NOT NULL,
  `city` varchar(50) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `country` varchar(50) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dish`
--

CREATE TABLE `dish` (
  `id` int NOT NULL,
  `dish_title` varchar(255) NOT NULL,
  `picture` longtext,
  `allergens` varchar(255) DEFAULT NULL,
  `type_of_dish` varchar(255) NOT NULL,
  `description` longtext,
  `price` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260202152853', '2026-03-30 11:08:30', 3101),
('DoctrineMigrations\\Version20260202161126', '2026-03-30 11:08:33', 955),
('DoctrineMigrations\\Version20260209105403', '2026-03-30 11:08:34', 46),
('DoctrineMigrations\\Version20260212080828', '2026-03-30 11:08:34', 69),
('DoctrineMigrations\\Version20260323134901', '2026-03-30 11:08:34', 2866),
('DoctrineMigrations\\Version20260330110741', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `material`
--

CREATE TABLE `material` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` longtext,
  `daily_rental_price` decimal(10,2) NOT NULL,
  `total_quantity` int NOT NULL,
  `picture` longtext,
  `caution` decimal(10,2) NOT NULL,
  `rental_condition` longtext,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `material_rental`
--

CREATE TABLE `material_rental` (
  `id` int NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `material_id` int NOT NULL,
  `rental_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `menu`
--

CREATE TABLE `menu` (
  `id` int NOT NULL,
  `title_menu` varchar(255) NOT NULL,
  `minimum_number_of_persons` int NOT NULL,
  `price_menu` decimal(10,2) NOT NULL,
  `description` longtext,
  `remaining_quantity` int NOT NULL,
  `precaution_menu` longtext,
  `storage_precautions` longtext,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Insertions des données de la table `menu`
--

INSERT INTO `menu` (`id`, `title_menu`, `minimum_number_of_persons`, `price_menu`, `description`, `remaining_quantity`, `precaution_menu`, `storage_precautions`, `created_at`, `updated_at`) VALUES
(1, 'Menu Dégustation', 4, 75.50, 'Menu gastronomique', 4, 'Peut contenir des arachides', 'Conserver à 4°C', '2026-04-01 08:10:15', '2026-04-01 19:46:15');

-- --------------------------------------------------------

--
-- Structure de la table `menu_dish`
--

CREATE TABLE `menu_dish` (
  `menu_id` int NOT NULL,
  `dish_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `menu_regime`
--

CREATE TABLE `menu_regime` (
  `menu_id` int NOT NULL,
  `regime_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `menu_theme_menu`
--

CREATE TABLE `menu_theme_menu` (
  `menu_id` int NOT NULL,
  `theme_menu_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notice`
--

CREATE TABLE `notice` (
  `id` int NOT NULL,
  `title` varchar(50) NOT NULL,
  `note` int NOT NULL,
  `description` longtext,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `order`
--

CREATE TABLE `order` (
  `id` int NOT NULL,
  `order_price` decimal(10,2) NOT NULL,
  `number_of_persons` int NOT NULL,
  `delivery_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `delivery_datetime` datetime NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `user_id` binary(16) NOT NULL,
  `notice_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Insertions des données de la table `order`
--

INSERT INTO `order` (`id`, `order_price`, `number_of_persons`, `delivery_price`, `total_price`, `updated_at`, `title`, `delivery_datetime`, `status`, `created_at`, `user_id`, `notice_id`) VALUES
(1, 0.00, 4, 5.00, 5.00, NULL, 'Commande anniversaire', '2026-04-15 18:30:00', 'pending', '2026-04-01 07:30:22', 0x019d405a633370528a0452261ae33ad1, NULL),
(2, 302.00, 4, 5.00, 307.00, '2026-04-01 09:01:03', 'Commande anniversaire', '2026-04-15 18:30:00', 'pending', '2026-04-01 07:37:31', 0x019d405a633370528a0452261ae33ad1, NULL),
(3, 0.00, 3, 5.00, 5.00, NULL, 'mariage', '2026-04-14 19:10:00', 'pending', '2026-04-01 19:17:17', 0x019d405a633370528a0452261ae33ad1, NULL),
(4, 75.50, 1, 5.00, 80.50, '2026-04-01 19:21:06', 'Bla', '2026-04-28 19:20:00', 'pending', '2026-04-01 19:21:00', 0x019d405a633370528a0452261ae33ad1, NULL),
(5, 75.50, 1, 5.00, 80.50, '2026-04-01 19:46:15', 'Bla', '2026-04-19 19:45:00', 'pending', '2026-04-01 19:46:08', 0x019d405a633370528a0452261ae33ad1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `order_item`
--

CREATE TABLE `order_item` (
  `id` int NOT NULL,
  `quantity` int NOT NULL,
  `price_unit` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `menu_id` int NOT NULL,
  `order_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `order_item`
--

INSERT INTO `order_item` (`id`, `quantity`, `price_unit`, `created_at`, `menu_id`, `order_id`) VALUES
(1, 2, 75.50, '2026-04-01 08:12:38', 1, 2),
(2, 2, 75.50, '2026-04-01 09:01:02', 1, 2),
(3, 1, 75.50, '2026-04-01 19:21:06', 1, 4),
(4, 1, 75.50, '2026-04-01 19:46:14', 1, 5);

-- --------------------------------------------------------

--
-- Structure de la table `regime`
--

CREATE TABLE `regime` (
  `id` int NOT NULL,
  `name_regime` varchar(50) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rental`
--

CREATE TABLE `rental` (
  `id` int NOT NULL,
  `rental_price` decimal(10,2) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `date_time_of_rendering` datetime NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `user_id` binary(16) NOT NULL,
  `notice_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `theme_menu`
--

CREATE TABLE `theme_menu` (
  `id` int NOT NULL,
  `name_theme` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` binary(16) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `api_token` varchar(255) NOT NULL,
  `numero` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `created_at`, `updated_at`, `api_token`, `numero`, `first_name`, `last_name`) VALUES
(0x019d405a633370528a0452261ae33ad1, 'cnf@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$NtaRq2sFMMajD88cyAWKMuA7MquNqNBGv8K0buR0dKoA3qJRWzKbS', '2026-03-30 20:05:58', NULL, '85fcbcf3d760421ff2f564e8c69deb61bef6ab94', '0612345678', 'Malvaud', 'Enzo'),
(0x019d4087b4247c51bac15c933d97034d, 'enzo.malvaud@gmail.com', '[]', '$2y$13$nM54m4VZ2XcroqNmxHGu6eXSldl.q2BGBJMnIuGYU2s83tHzW8noa', '2026-03-30 20:55:28', NULL, 'e5040bf809cdfaaa22bfac36bb95dc2e78e0b83d', '0610053317', 'Malvaud', 'Enzo'),
(0x019d408a0168744ab70c7e3864b43cb2, 'adresse@email.com', '[]', '$2y$13$YjtuZviZNxaHoNA7UWv3dOzYLQx2T7qapZ/QEAZJtmINZUx98mJRa', '2026-03-30 20:57:59', NULL, '49f844c1e81b36b8362657c7756ab3ac713a2a90', '06.XX.XX.XX.XX', 'User', 'User_lastName');

-- --------------------------------------------------------

--
-- Structure de la table `user_adresse`
--

CREATE TABLE `user_adresse` (
  `user_id` binary(16) NOT NULL,
  `adresse_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `adresse`
--
ALTER TABLE `adresse`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `dish`
--
ALTER TABLE `dish`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_7CBE75955E237E06` (`name`);

--
-- Index pour la table `material_rental`
--
ALTER TABLE `material_rental`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_872B4946E308AC6F` (`material_id`),
  ADD KEY `IDX_872B4946A7CF2329` (`rental_id`);

--
-- Index pour la table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `menu_dish`
--
ALTER TABLE `menu_dish`
  ADD PRIMARY KEY (`menu_id`,`dish_id`),
  ADD KEY `IDX_5D327CF6CCD7E912` (`menu_id`),
  ADD KEY `IDX_5D327CF6148EB0CB` (`dish_id`);

--
-- Index pour la table `menu_regime`
--
ALTER TABLE `menu_regime`
  ADD PRIMARY KEY (`menu_id`,`regime_id`),
  ADD KEY `IDX_79C112A4CCD7E912` (`menu_id`),
  ADD KEY `IDX_79C112A435E7D534` (`regime_id`);

--
-- Index pour la table `menu_theme_menu`
--
ALTER TABLE `menu_theme_menu`
  ADD PRIMARY KEY (`menu_id`,`theme_menu_id`),
  ADD KEY `IDX_166F7A7CCCD7E912` (`menu_id`),
  ADD KEY `IDX_166F7A7C95B4EC31` (`theme_menu_id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- Index pour la table `notice`
--
ALTER TABLE `notice`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F5299398A76ED395` (`user_id`),
  ADD KEY `IDX_F52993987D540AB` (`notice_id`);

--
-- Index pour la table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_52EA1F09CCD7E912` (`menu_id`),
  ADD KEY `IDX_52EA1F098D9F6D38` (`order_id`);

--
-- Index pour la table `regime`
--
ALTER TABLE `regime`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `rental`
--
ALTER TABLE `rental`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_1619C27DA76ED395` (`user_id`),
  ADD KEY `IDX_1619C27D7D540AB` (`notice_id`);

--
-- Index pour la table `theme_menu`
--
ALTER TABLE `theme_menu`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- Index pour la table `user_adresse`
--
ALTER TABLE `user_adresse`
  ADD PRIMARY KEY (`user_id`,`adresse_id`),
  ADD KEY `IDX_9B52161CA76ED395` (`user_id`),
  ADD KEY `IDX_9B52161C4DE7DC5C` (`adresse_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `adresse`
--
ALTER TABLE `adresse`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `dish`
--
ALTER TABLE `dish`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `material`
--
ALTER TABLE `material`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `material_rental`
--
ALTER TABLE `material_rental`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `notice`
--
ALTER TABLE `notice`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `order`
--
ALTER TABLE `order`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `regime`
--
ALTER TABLE `regime`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `rental`
--
ALTER TABLE `rental`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `theme_menu`
--
ALTER TABLE `theme_menu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `material_rental`
--
ALTER TABLE `material_rental`
  ADD CONSTRAINT `FK_872B4946A7CF2329` FOREIGN KEY (`rental_id`) REFERENCES `rental` (`id`),
  ADD CONSTRAINT `FK_872B4946E308AC6F` FOREIGN KEY (`material_id`) REFERENCES `material` (`id`);

--
-- Contraintes pour la table `menu_dish`
--
ALTER TABLE `menu_dish`
  ADD CONSTRAINT `FK_5D327CF6148EB0CB` FOREIGN KEY (`dish_id`) REFERENCES `dish` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_5D327CF6CCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `menu_regime`
--
ALTER TABLE `menu_regime`
  ADD CONSTRAINT `FK_79C112A435E7D534` FOREIGN KEY (`regime_id`) REFERENCES `regime` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_79C112A4CCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `menu_theme_menu`
--
ALTER TABLE `menu_theme_menu`
  ADD CONSTRAINT `FK_166F7A7C95B4EC31` FOREIGN KEY (`theme_menu_id`) REFERENCES `theme_menu` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_166F7A7CCCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `FK_F52993987D540AB` FOREIGN KEY (`notice_id`) REFERENCES `notice` (`id`),
  ADD CONSTRAINT `FK_F5299398A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `FK_52EA1F098D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`),
  ADD CONSTRAINT `FK_52EA1F09CCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`);

--
-- Contraintes pour la table `rental`
--
ALTER TABLE `rental`
  ADD CONSTRAINT `FK_1619C27D7D540AB` FOREIGN KEY (`notice_id`) REFERENCES `notice` (`id`),
  ADD CONSTRAINT `FK_1619C27DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `user_adresse`
--
ALTER TABLE `user_adresse`
  ADD CONSTRAINT `FK_9B52161C4DE7DC5C` FOREIGN KEY (`adresse_id`) REFERENCES `adresse` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_9B52161CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
