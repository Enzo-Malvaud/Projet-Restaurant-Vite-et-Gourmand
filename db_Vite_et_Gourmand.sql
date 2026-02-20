-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : ven. 20 fév. 2026 à 18:26
-- Version du serveur : 8.0.44
-- Version de PHP : 8.3.29

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
  `city` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL
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
  `type_of_dish` varchar(255) NOT NULL
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
('DoctrineMigrations\\Version20260202152853', '2026-02-02 15:30:55', 2886),
('DoctrineMigrations\\Version20260202161126', '2026-02-02 16:14:32', 3042),
('DoctrineMigrations\\Version20260209105403', '2026-02-09 11:02:14', 371),
('DoctrineMigrations\\Version20260212080828', '2026-02-12 08:10:46', 266);

-- --------------------------------------------------------

--
-- Structure de la table `horaire_restaurant`
--

CREATE TABLE `horaire_restaurant` (
  `id` int NOT NULL,
  `day` varchar(50) NOT NULL,
  `opening_hour` datetime NOT NULL,
  `closing_hour` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `material`
--

CREATE TABLE `material` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext,
  `daily_rental_price` decimal(10,0) NOT NULL,
  `total_quantity` int NOT NULL,
  `quantity_available` int NOT NULL,
  `picture` longtext,
  `caution` decimal(10,0) NOT NULL,
  `rental_condition` longtext
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
  `list_menu` longtext NOT NULL,
  `description` longtext,
  `remaining_quantity` int NOT NULL,
  `precaution_menu` longtext,
  `storage_precautions` longtext,
  `price_per_person` decimal(10,0) NOT NULL,
  `regime_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
-- Structure de la table `order`
--

CREATE TABLE `order` (
  `id` int NOT NULL,
  `order_date` datetime NOT NULL,
  `delivery_date` datetime NOT NULL,
  `delivery_time` datetime NOT NULL,
  `order_price` decimal(10,0) NOT NULL,
  `number_of_persons` int NOT NULL,
  `delivery_price` decimal(10,0) NOT NULL,
  `total_price` decimal(10,0) NOT NULL,
  `date_modified` datetime DEFAULT NULL,
  `adresse_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `order_menu`
--

CREATE TABLE `order_menu` (
  `order_id` int NOT NULL,
  `menu_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `regime`
--

CREATE TABLE `regime` (
  `id` int NOT NULL,
  `name_remige` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rental`
--

CREATE TABLE `rental` (
  `id` int NOT NULL,
  `date_rental` datetime NOT NULL,
  `date_of_rendering` datetime NOT NULL,
  `rendering_time` datetime NOT NULL,
  `rental_price` decimal(10,0) NOT NULL,
  `date_of_modification` datetime DEFAULT NULL,
  `adresse_id` int NOT NULL,
  `material_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `theme_menu`
--

CREATE TABLE `theme_menu` (
  `id` int NOT NULL,
  `name_theme` varchar(255) NOT NULL
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
  `created_at` date NOT NULL,
  `updated_at` date DEFAULT NULL,
  `api_token` varchar(255) NOT NULL,
  `numero` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `created_at`, `updated_at`, `api_token`, `numero`, `first_name`, `last_name`) VALUES
(0x019c42c5471d736baac1e12c04019bc1, 'test@gmail.com', '[]', '$2y$13$Yr3zz/2g9O2vVRWEvPErku0.goEa12vLvKJfGp5SvOANpBmhXpG92', '2026-02-09', '2026-02-12', 'dc0c780bcdbb5d03e7f669a85e371cad03b85493', '06.XX.XX.XX.XX', 'User', 'User_lastName'),
(0x019c5120efce79649b39aec1db8ca473, 'adresse@email.com', '[]', '$2y$13$Gd.rqa0zzojDO0DbT7bL6.p9YCnUi232olRJyhnu19PXX8r1bcooC', '2026-02-12', NULL, '87844e02ae3bf91c478b60d3141b0e378c73affd', '06.XX.XX.XX.XX', 'User', 'User_lastName');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `adresse`
--
ALTER TABLE `adresse`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_C35F0816C35F0816` (`adresse`);

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
-- Index pour la table `horaire_restaurant`
--
ALTER TABLE `horaire_restaurant`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_7D053A9335E7D534` (`regime_id`);

--
-- Index pour la table `menu_dish`
--
ALTER TABLE `menu_dish`
  ADD PRIMARY KEY (`menu_id`,`dish_id`),
  ADD KEY `IDX_5D327CF6CCD7E912` (`menu_id`),
  ADD KEY `IDX_5D327CF6148EB0CB` (`dish_id`);

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
-- Index pour la table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F52993984DE7DC5C` (`adresse_id`);

--
-- Index pour la table `order_menu`
--
ALTER TABLE `order_menu`
  ADD PRIMARY KEY (`order_id`,`menu_id`),
  ADD KEY `IDX_30F400848D9F6D38` (`order_id`),
  ADD KEY `IDX_30F40084CCD7E912` (`menu_id`);

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
  ADD KEY `IDX_1619C27D4DE7DC5C` (`adresse_id`),
  ADD KEY `IDX_1619C27DE308AC6F` (`material_id`);

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
-- AUTO_INCREMENT pour la table `horaire_restaurant`
--
ALTER TABLE `horaire_restaurant`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `material`
--
ALTER TABLE `material`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `order`
--
ALTER TABLE `order`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

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
-- Contraintes pour la table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `FK_7D053A9335E7D534` FOREIGN KEY (`regime_id`) REFERENCES `regime` (`id`);

--
-- Contraintes pour la table `menu_dish`
--
ALTER TABLE `menu_dish`
  ADD CONSTRAINT `FK_5D327CF6148EB0CB` FOREIGN KEY (`dish_id`) REFERENCES `dish` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_5D327CF6CCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `FK_F52993984DE7DC5C` FOREIGN KEY (`adresse_id`) REFERENCES `adresse` (`id`);

--
-- Contraintes pour la table `order_menu`
--
ALTER TABLE `order_menu`
  ADD CONSTRAINT `FK_30F400848D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_30F40084CCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `rental`
--
ALTER TABLE `rental`
  ADD CONSTRAINT `FK_1619C27D4DE7DC5C` FOREIGN KEY (`adresse_id`) REFERENCES `adresse` (`id`),
  ADD CONSTRAINT `FK_1619C27DE308AC6F` FOREIGN KEY (`material_id`) REFERENCES `material` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
