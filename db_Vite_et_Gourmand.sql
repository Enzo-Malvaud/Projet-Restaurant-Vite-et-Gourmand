
-- =============================================================
--  NETTOYAGE
--  Suppression dans l'ordre inverse des dépendances.
-- =============================================================

DROP TABLE IF EXISTS user_adresse;
DROP TABLE IF EXISTS order_item;
DROP TABLE IF EXISTS order;
DROP TABLE IF EXISTS menu_theme_menu;
DROP TABLE IF EXISTS menu_regime;
DROP TABLE IF EXISTS menu_dish;
DROP TABLE IF EXISTS material_rental;
DROP TABLE IF EXISTS rental;
DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS theme_menu;
DROP TABLE IF EXISTS regime;
DROP TABLE IF EXISTS notice;
DROP TABLE IF EXISTS messenger_messages;
DROP TABLE IF EXISTS menu;
DROP TABLE IF EXISTS material;
DROP TABLE IF EXISTS doctrine_migration_versions;
DROP TABLE IF EXISTS dish;
DROP TABLE IF EXISTS adresse;


-- =============================================================
--  TABLES SANS DÉPENDANCES
--  Ces tables ne référencent aucune autre table.
-- =============================================================

-- Adresses liées aux utilisateurs
CREATE TABLE adresse (
    id          INT          NOT NULL AUTO_INCREMENT,
    city        VARCHAR(50)  NOT NULL,
    adresse     VARCHAR(255) NOT NULL,
    country     VARCHAR(50)  NOT NULL,
    postal_code VARCHAR(20)  NOT NULL,
    created_at  DATETIME     NOT NULL,
    updated_at  DATETIME     DEFAULT NULL,
    PRIMARY KEY (id)
);

-- Plats disponibles pour composer les menus
CREATE TABLE dish (
    id           INT          NOT NULL AUTO_INCREMENT,
    dish_title   VARCHAR(255) NOT NULL,
    picture      LONGTEXT     DEFAULT NULL,
    allergens    VARCHAR(255) DEFAULT NULL,
    type_of_dish VARCHAR(255) NOT NULL,
    description  LONGTEXT     DEFAULT NULL,
    price        DECIMAL(10,2) NOT NULL,
    created_at   DATETIME     NOT NULL,
    updated_at   DATETIME     DEFAULT NULL,
    PRIMARY KEY (id)
);

-- Suivi des migrations Doctrine
CREATE TABLE doctrine_migration_versions (
    version        VARCHAR(191) NOT NULL,
    executed_at    DATETIME     DEFAULT NULL,
    execution_time INT          DEFAULT NULL,
    PRIMARY KEY (version)
);

-- Matériel disponible à la location
CREATE TABLE material (
    id                 INT           NOT NULL AUTO_INCREMENT,
    name               VARCHAR(50)   NOT NULL,
    description        LONGTEXT      DEFAULT NULL,
    daily_rental_price DECIMAL(10,2) NOT NULL,
    total_quantity     INT           NOT NULL,
    picture            LONGTEXT      DEFAULT NULL,
    caution            DECIMAL(10,2) NOT NULL,
    rental_condition   LONGTEXT      DEFAULT NULL,
    created_at         DATETIME      NOT NULL,
    updated_at         DATETIME      DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unique_name (name)
);

-- Menus proposés aux clients
CREATE TABLE menu (
    id                        INT           NOT NULL AUTO_INCREMENT,
    title_menu                VARCHAR(255)  NOT NULL,
    minimum_number_of_persons INT           NOT NULL,
    price_menu                DECIMAL(10,2) NOT NULL,
    description               LONGTEXT      DEFAULT NULL,
    remaining_quantity        INT           NOT NULL,
    precaution_menu           LONGTEXT      DEFAULT NULL,
    storage_precautions       LONGTEXT      DEFAULT NULL,
    created_at                DATETIME      NOT NULL,
    updated_at                DATETIME      DEFAULT NULL,
    PRIMARY KEY (id)
);

-- File de messages Symfony Messenger
CREATE TABLE messenger_messages (
    id           BIGINT   NOT NULL AUTO_INCREMENT,
    body         LONGTEXT NOT NULL,
    headers      LONGTEXT NOT NULL,
    queue_name   VARCHAR(190) NOT NULL,
    created_at   DATETIME NOT NULL,
    available_at DATETIME NOT NULL,
    delivered_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_messenger (queue_name, available_at, delivered_at, id)
);

-- Avis laissés par les clients sur les commandes
CREATE TABLE notice (
    id          INT          NOT NULL AUTO_INCREMENT,
    title       VARCHAR(50)  NOT NULL,
    note        INT          NOT NULL,
    description LONGTEXT     DEFAULT NULL,
    created_at  DATETIME     NOT NULL,
    updated_at  DATETIME     DEFAULT NULL,
    PRIMARY KEY (id)
);

-- Régimes alimentaires (végétarien, sans gluten, etc.)
CREATE TABLE regime (
    id           INT        NOT NULL AUTO_INCREMENT,
    name_regime  VARCHAR(50) NOT NULL,
    created_at   DATE        NOT NULL,
    updated_at   DATE        DEFAULT NULL,
    PRIMARY KEY (id)
);

-- Thèmes disponibles pour les menus (mariage, anniversaire, etc.)
CREATE TABLE theme_menu (
    id          INT          NOT NULL AUTO_INCREMENT,
    name_theme  VARCHAR(50)  NOT NULL,
    description VARCHAR(255) NOT NULL,
    created_at  DATETIME     NOT NULL,
    updated_at  DATETIME     DEFAULT NULL,
    PRIMARY KEY (id)
);

-- Utilisateurs de la plateforme
-- L'id est un UUID binaire (16 octets) généré par l'application
CREATE TABLE user (
    id         BINARY(16)   NOT NULL,
    email      VARCHAR(180) NOT NULL,
    roles      JSON         NOT NULL,
    password   VARCHAR(255) NOT NULL,
    created_at DATETIME     NOT NULL,
    updated_at DATETIME     DEFAULT NULL,
    api_token  VARCHAR(255) NOT NULL,
    numero     VARCHAR(255) DEFAULT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name  VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unique_email (email)
);


-- =============================================================
--  TABLES AVEC DÉPENDANCES
--  Ces tables référencent les tables créées ci-dessus.
-- =============================================================

-- Locations de matériel passées par les utilisateurs
CREATE TABLE rental (
    id                      INT           NOT NULL AUTO_INCREMENT,
    rental_price            DECIMAL(10,2) NOT NULL,
    updated_at              DATETIME      DEFAULT NULL,
    title                   VARCHAR(255)  NOT NULL,
    date_time_of_rendering  DATETIME      NOT NULL,
    status                  VARCHAR(50)   NOT NULL,
    created_at              DATETIME      NOT NULL,
    user_id                 BINARY(16)    NOT NULL,  -- référence → user
    notice_id               INT           DEFAULT NULL,  -- référence → notice (optionnel)
    PRIMARY KEY (id),
    KEY idx_rental_user   (user_id),
    KEY idx_rental_notice (notice_id),
    FOREIGN KEY (user_id)   REFERENCES user   (id),
    FOREIGN KEY (notice_id) REFERENCES notice (id)
);

-- Commandes passées par les utilisateurs
CREATE TABLE `order` (
    id                INT           NOT NULL AUTO_INCREMENT,
    order_price       DECIMAL(10,2) NOT NULL,
    number_of_persons INT           NOT NULL,
    delivery_price    DECIMAL(10,2) NOT NULL,
    total_price       DECIMAL(10,2) NOT NULL,
    updated_at        DATETIME      DEFAULT NULL,
    title             VARCHAR(255)  NOT NULL,
    delivery_datetime DATETIME      NOT NULL,
    status            VARCHAR(50)   NOT NULL,
    created_at        DATETIME      NOT NULL,
    user_id           BINARY(16)    NOT NULL,  -- référence → user
    notice_id         INT           DEFAULT NULL,  -- référence → notice (optionnel)
    PRIMARY KEY (id),
    KEY idx_order_user   (user_id),
    KEY idx_order_notice (notice_id),
    FOREIGN KEY (user_id)   REFERENCES user   (id),
    FOREIGN KEY (notice_id) REFERENCES notice (id)
);


-- =============================================================
--  TABLES DE RELATION (Many-to-Many et lignes de commande)
-- =============================================================

-- Matériel inclus dans une location (avec quantité et prix unitaire)
CREATE TABLE material_rental (
    id          INT           NOT NULL AUTO_INCREMENT,
    quantity    INT           NOT NULL,
    unit_price  DECIMAL(10,2) NOT NULL,
    created_at  DATETIME      NOT NULL,
    material_id INT           NOT NULL,  -- référence → material
    rental_id   INT           NOT NULL,  -- référence → rental
    PRIMARY KEY (id),
    KEY idx_material_rental_material (material_id),
    KEY idx_material_rental_rental   (rental_id),
    FOREIGN KEY (material_id) REFERENCES material (id),
    FOREIGN KEY (rental_id)   REFERENCES rental   (id)
);

-- Table pivot : association Many-to-Many entre menu et dish
CREATE TABLE menu_dish (
    menu_id INT NOT NULL,  -- référence → menu
    dish_id INT NOT NULL,  -- référence → dish
    PRIMARY KEY (menu_id, dish_id),
    FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE,
    FOREIGN KEY (dish_id) REFERENCES dish (id) ON DELETE CASCADE
);

-- Table pivot : association Many-to-Many entre menu et regime
CREATE TABLE menu_regime (
    menu_id   INT NOT NULL,  -- référence → menu
    regime_id INT NOT NULL,  -- référence → regime
    PRIMARY KEY (menu_id, regime_id),
    FOREIGN KEY (menu_id)   REFERENCES menu   (id) ON DELETE CASCADE,
    FOREIGN KEY (regime_id) REFERENCES regime (id) ON DELETE CASCADE
);

-- Table pivot : association Many-to-Many entre menu et theme_menu
CREATE TABLE menu_theme_menu (
    menu_id       INT NOT NULL,  -- référence → menu
    theme_menu_id INT NOT NULL,  -- référence → theme_menu
    PRIMARY KEY (menu_id, theme_menu_id),
    FOREIGN KEY (menu_id)       REFERENCES menu       (id) ON DELETE CASCADE,
    FOREIGN KEY (theme_menu_id) REFERENCES theme_menu (id) ON DELETE CASCADE
);

-- Lignes de commande : menus commandés dans une commande
CREATE TABLE order_item (
    id         INT           NOT NULL AUTO_INCREMENT,
    quantity   INT           NOT NULL,
    price_unit DECIMAL(10,2) NOT NULL,
    created_at DATETIME      NOT NULL,
    menu_id    INT           NOT NULL,  -- référence → menu
    order_id   INT           NOT NULL,  -- référence → order
    PRIMARY KEY (id),
    KEY idx_order_item_menu  (menu_id),
    KEY idx_order_item_order (order_id),
    FOREIGN KEY (menu_id)  REFERENCES menu    (id),
    FOREIGN KEY (order_id) REFERENCES `order` (id)
);

-- Table pivot : adresses associées à un utilisateur
CREATE TABLE user_adresse (
    user_id    BINARY(16) NOT NULL,  -- référence → user
    adresse_id INT        NOT NULL,  -- référence → adresse
    PRIMARY KEY (user_id, adresse_id),
    FOREIGN KEY (user_id)    REFERENCES user    (id) ON DELETE CASCADE,
    FOREIGN KEY (adresse_id) REFERENCES adresse (id) ON DELETE CASCADE
);


-- =============================================================
--  DONNÉES
-- =============================================================


-- Menu de démonstration
INSERT INTO menu (id, title_menu, minimum_number_of_persons, price_menu, description, remaining_quantity, precaution_menu, storage_precautions, created_at, updated_at) VALUES
    (1, 'Menu Dégustation', 4, 75.50, 'Menu gastronomique', 4, 'Peut contenir des arachides', 'Conserver à 4°C', '2026-04-01 08:10:15', '2026-04-01 19:46:15');

-- Utilisateurs (mots de passe hashés en bcrypt)
INSERT INTO user (id, email, roles, password, created_at, updated_at, api_token, numero, first_name, last_name) VALUES
    (0x019d405a633370528a0452261ae33ad1, 'cnf@gmail.com',            '["ROLE_ADMIN"]', '$2y$13$NtaRq2sFMMajD88cyAWKMuA7MquNqNBGv8K0buR0dKoA3qJRWzKbS', '2026-03-30 20:05:58', NULL, '85fcbcf3d760421ff2f564e8c69deb61bef6ab94', '0612345678',    'Malvaud', 'Enzo'),
    (0x019d4087b4247c51bac15c933d97034d, 'enzo.malvaud@gmail.com',   '[]',             '$2y$13$nM54m4VZ2XcroqNmxHGu6eXSldl.q2BGBJMnIuGYU2s83tHzW8noa', '2026-03-30 20:55:28', NULL, 'e5040bf809cdfaaa22bfac36bb95dc2e78e0b83d', '0610053317',    'Malvaud', 'Enzo'),
    (0x019d408a0168744ab70c7e3864b43cb2, 'adresse@email.com',        '[]',             '$2y$13$YjtuZviZNxaHoNA7UWv3dOzYLQx2T7qapZ/QEAZJtmINZUx98mJRa', '2026-03-30 20:57:59', NULL, '49f844c1e81b36b8362657c7756ab3ac713a2a90', '06.XX.XX.XX.XX', 'User',    'User_lastName');

-- Commandes de test
INSERT INTO `order` (id, order_price, number_of_persons, delivery_price, total_price, updated_at, title, delivery_datetime, status, created_at, user_id, notice_id) VALUES
    (1, 0.00,   4, 5.00, 5.00,   NULL,                 'Commande anniversaire', '2026-04-15 18:30:00', 'pending', '2026-04-01 07:30:22', 0x019d405a633370528a0452261ae33ad1, NULL),
    (2, 302.00, 4, 5.00, 307.00, '2026-04-01 09:01:03', 'Commande anniversaire', '2026-04-15 18:30:00', 'pending', '2026-04-01 07:37:31', 0x019d405a633370528a0452261ae33ad1, NULL),
    (3, 0.00,   3, 5.00, 5.00,   NULL,                 'mariage',               '2026-04-14 19:10:00', 'pending', '2026-04-01 19:17:17', 0x019d405a633370528a0452261ae33ad1, NULL),
    (4, 75.50,  1, 5.00, 80.50,  '2026-04-01 19:21:06', 'Bla',                  '2026-04-28 19:20:00', 'pending', '2026-04-01 19:21:00', 0x019d405a633370528a0452261ae33ad1, NULL),
    (5, 75.50,  1, 5.00, 80.50,  '2026-04-01 19:46:15', 'Bla',                  '2026-04-19 19:45:00', 'pending', '2026-04-01 19:46:08', 0x019d405a633370528a0452261ae33ad1, NULL);

-- Lignes de commande
INSERT INTO order_item (id, quantity, price_unit, created_at, menu_id, order_id) VALUES
    (1, 2, 75.50, '2026-04-01 08:12:38', 1, 2),
    (2, 2, 75.50, '2026-04-01 09:01:02', 1, 2),
    (3, 1, 75.50, '2026-04-01 19:21:06', 1, 4),
    (4, 1, 75.50, '2026-04-01 19:46:14', 1, 5);