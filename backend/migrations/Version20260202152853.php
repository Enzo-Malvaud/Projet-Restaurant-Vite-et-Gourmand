<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260202152853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adresse (id INT AUTO_INCREMENT NOT NULL, city VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C35F0816C35F0816 (adresse), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE dish (id INT AUTO_INCREMENT NOT NULL, dish_title VARCHAR(255) NOT NULL, picture LONGTEXT DEFAULT NULL, allergens VARCHAR(255) DEFAULT NULL, type_of_dish VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE horaire_restaurant (id INT AUTO_INCREMENT NOT NULL, day VARCHAR(50) NOT NULL, opening_hour DATETIME NOT NULL, closing_hour DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE material (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, daily_rental_price NUMERIC(10, 0) NOT NULL, total_quantity INT NOT NULL, quantity_available INT NOT NULL, picture LONGTEXT DEFAULT NULL, caution NUMERIC(10, 0) NOT NULL, rental_condition LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, title_menu VARCHAR(255) NOT NULL, minimum_number_of_persons INT NOT NULL, price_menu NUMERIC(10, 2) NOT NULL, list_menu LONGTEXT NOT NULL, description LONGTEXT DEFAULT NULL, remaining_quantity INT NOT NULL, precaution_menu LONGTEXT DEFAULT NULL, storage_precautions LONGTEXT DEFAULT NULL, price_per_person NUMERIC(10, 0) NOT NULL, id_regime_id INT DEFAULT NULL, INDEX IDX_7D053A93B5D26913 (id_regime_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE menu_dish (menu_id INT NOT NULL, dish_id INT NOT NULL, INDEX IDX_5D327CF6CCD7E912 (menu_id), INDEX IDX_5D327CF6148EB0CB (dish_id), PRIMARY KEY (menu_id, dish_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE menu_theme_menu (menu_id INT NOT NULL, theme_menu_id INT NOT NULL, INDEX IDX_166F7A7CCCD7E912 (menu_id), INDEX IDX_166F7A7C95B4EC31 (theme_menu_id), PRIMARY KEY (menu_id, theme_menu_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, order_date DATETIME NOT NULL, delivery_date DATETIME NOT NULL, delivery_time DATETIME NOT NULL, order_price NUMERIC(10, 0) NOT NULL, number_of_persons INT NOT NULL, delivery_price NUMERIC(10, 0) NOT NULL, total_price NUMERIC(10, 0) NOT NULL, date_modified DATETIME DEFAULT NULL, id_adresse_id INT NOT NULL, INDEX IDX_F5299398E86D5C8B (id_adresse_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE order_menu (order_id INT NOT NULL, menu_id INT NOT NULL, INDEX IDX_30F400848D9F6D38 (order_id), INDEX IDX_30F40084CCD7E912 (menu_id), PRIMARY KEY (order_id, menu_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE regime (id INT AUTO_INCREMENT NOT NULL, name_remige VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rental (id INT AUTO_INCREMENT NOT NULL, date_rental DATETIME NOT NULL, date_of_rendering DATETIME NOT NULL, rendering_time DATETIME NOT NULL, rental_price NUMERIC(10, 0) NOT NULL, date_of_modification DATETIME DEFAULT NULL, id_adresse_id INT NOT NULL, id_material_id INT NOT NULL, INDEX IDX_1619C27DE86D5C8B (id_adresse_id), INDEX IDX_1619C27DFB1A6198 (id_material_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE theme_menu (id INT AUTO_INCREMENT NOT NULL, name_theme VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93B5D26913 FOREIGN KEY (id_regime_id) REFERENCES regime (id)');
        $this->addSql('ALTER TABLE menu_dish ADD CONSTRAINT FK_5D327CF6CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_dish ADD CONSTRAINT FK_5D327CF6148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_theme_menu ADD CONSTRAINT FK_166F7A7CCCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_theme_menu ADD CONSTRAINT FK_166F7A7C95B4EC31 FOREIGN KEY (theme_menu_id) REFERENCES theme_menu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398E86D5C8B FOREIGN KEY (id_adresse_id) REFERENCES adresse (id)');
        $this->addSql('ALTER TABLE order_menu ADD CONSTRAINT FK_30F400848D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_menu ADD CONSTRAINT FK_30F40084CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT FK_1619C27DE86D5C8B FOREIGN KEY (id_adresse_id) REFERENCES adresse (id)');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT FK_1619C27DFB1A6198 FOREIGN KEY (id_material_id) REFERENCES material (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93B5D26913');
        $this->addSql('ALTER TABLE menu_dish DROP FOREIGN KEY FK_5D327CF6CCD7E912');
        $this->addSql('ALTER TABLE menu_dish DROP FOREIGN KEY FK_5D327CF6148EB0CB');
        $this->addSql('ALTER TABLE menu_theme_menu DROP FOREIGN KEY FK_166F7A7CCCD7E912');
        $this->addSql('ALTER TABLE menu_theme_menu DROP FOREIGN KEY FK_166F7A7C95B4EC31');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398E86D5C8B');
        $this->addSql('ALTER TABLE order_menu DROP FOREIGN KEY FK_30F400848D9F6D38');
        $this->addSql('ALTER TABLE order_menu DROP FOREIGN KEY FK_30F40084CCD7E912');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY FK_1619C27DE86D5C8B');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY FK_1619C27DFB1A6198');
        $this->addSql('DROP TABLE adresse');
        $this->addSql('DROP TABLE dish');
        $this->addSql('DROP TABLE horaire_restaurant');
        $this->addSql('DROP TABLE material');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE menu_dish');
        $this->addSql('DROP TABLE menu_theme_menu');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_menu');
        $this->addSql('DROP TABLE regime');
        $this->addSql('DROP TABLE rental');
        $this->addSql('DROP TABLE theme_menu');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
