<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260323134901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE material_rental (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL, material_id INT NOT NULL, rental_id INT NOT NULL, INDEX IDX_872B4946E308AC6F (material_id), INDEX IDX_872B4946A7CF2329 (rental_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE menu_regime (menu_id INT NOT NULL, regime_id INT NOT NULL, INDEX IDX_79C112A4CCD7E912 (menu_id), INDEX IDX_79C112A435E7D534 (regime_id), PRIMARY KEY (menu_id, regime_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE notice (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(50) NOT NULL, note INT NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, price_unit NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL, menu_id INT NOT NULL, order_id INT NOT NULL, INDEX IDX_52EA1F09CCD7E912 (menu_id), INDEX IDX_52EA1F098D9F6D38 (order_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_adresse (user_id BINARY(16) NOT NULL, adresse_id INT NOT NULL, INDEX IDX_9B52161CA76ED395 (user_id), INDEX IDX_9B52161C4DE7DC5C (adresse_id), PRIMARY KEY (user_id, adresse_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE material_rental ADD CONSTRAINT FK_872B4946E308AC6F FOREIGN KEY (material_id) REFERENCES material (id)');
        $this->addSql('ALTER TABLE material_rental ADD CONSTRAINT FK_872B4946A7CF2329 FOREIGN KEY (rental_id) REFERENCES rental (id)');
        $this->addSql('ALTER TABLE menu_regime ADD CONSTRAINT FK_79C112A4CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_regime ADD CONSTRAINT FK_79C112A435E7D534 FOREIGN KEY (regime_id) REFERENCES regime (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE user_adresse ADD CONSTRAINT FK_9B52161CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_adresse ADD CONSTRAINT FK_9B52161C4DE7DC5C FOREIGN KEY (adresse_id) REFERENCES adresse (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_menu DROP FOREIGN KEY `FK_30F400848D9F6D38`');
        $this->addSql('ALTER TABLE order_menu DROP FOREIGN KEY `FK_30F40084CCD7E912`');
        $this->addSql('DROP TABLE horaire_restaurant');
        $this->addSql('DROP TABLE order_menu');
        $this->addSql('DROP INDEX UNIQ_C35F0816C35F0816 ON adresse');
        $this->addSql('ALTER TABLE adresse ADD country VARCHAR(50) NOT NULL, ADD postal_code VARCHAR(20) NOT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, CHANGE city city VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE dish ADD description LONGTEXT DEFAULT NULL, ADD price NUMERIC(10, 2) NOT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE material ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, DROP quantity_available, CHANGE name name VARCHAR(50) NOT NULL, CHANGE daily_rental_price daily_rental_price NUMERIC(10, 2) NOT NULL, CHANGE caution caution NUMERIC(10, 2) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7CBE75955E237E06 ON material (name)');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY `FK_7D053A9335E7D534`');
        $this->addSql('DROP INDEX IDX_7D053A9335E7D534 ON menu');
        $this->addSql('ALTER TABLE menu ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, DROP list_menu, DROP price_per_person, DROP regime_id');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY `FK_F52993984DE7DC5C`');
        $this->addSql('DROP INDEX IDX_F52993984DE7DC5C ON `order`');
        $this->addSql('ALTER TABLE `order` ADD title VARCHAR(255) NOT NULL, ADD delivery_datetime DATETIME NOT NULL, ADD status VARCHAR(50) NOT NULL, ADD created_at DATETIME NOT NULL, ADD user_id BINARY(16) NOT NULL, ADD notice_id INT DEFAULT NULL, DROP order_date, DROP delivery_date, DROP delivery_time, DROP adresse_id, CHANGE order_price order_price NUMERIC(10, 2) NOT NULL, CHANGE delivery_price delivery_price NUMERIC(10, 2) NOT NULL, CHANGE total_price total_price NUMERIC(10, 2) NOT NULL, CHANGE date_modified updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993987D540AB FOREIGN KEY (notice_id) REFERENCES notice (id)');
        $this->addSql('CREATE INDEX IDX_F5299398A76ED395 ON `order` (user_id)');
        $this->addSql('CREATE INDEX IDX_F52993987D540AB ON `order` (notice_id)');
        $this->addSql('ALTER TABLE regime ADD name_regime VARCHAR(50) NOT NULL, ADD created_at DATE NOT NULL, ADD updated_at DATE DEFAULT NULL, DROP name_remige');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY `FK_1619C27D4DE7DC5C`');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY `FK_1619C27DE308AC6F`');
        $this->addSql('DROP INDEX IDX_1619C27D4DE7DC5C ON rental');
        $this->addSql('DROP INDEX IDX_1619C27DE308AC6F ON rental');
        $this->addSql('ALTER TABLE rental ADD title VARCHAR(255) NOT NULL, ADD date_time_of_rendering DATETIME NOT NULL, ADD status VARCHAR(50) NOT NULL, ADD created_at DATETIME NOT NULL, ADD user_id BINARY(16) NOT NULL, ADD notice_id INT DEFAULT NULL, DROP date_rental, DROP date_of_rendering, DROP rendering_time, DROP adresse_id, DROP material_id, CHANGE rental_price rental_price NUMERIC(10, 2) NOT NULL, CHANGE date_of_modification updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT FK_1619C27DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT FK_1619C27D7D540AB FOREIGN KEY (notice_id) REFERENCES notice (id)');
        $this->addSql('CREATE INDEX IDX_1619C27DA76ED395 ON rental (user_id)');
        $this->addSql('CREATE INDEX IDX_1619C27D7D540AB ON rental (notice_id)');
        $this->addSql('ALTER TABLE theme_menu ADD description VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, CHANGE name_theme name_theme VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE horaire_restaurant (id INT AUTO_INCREMENT NOT NULL, day VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, opening_hour DATETIME NOT NULL, closing_hour DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE order_menu (order_id INT NOT NULL, menu_id INT NOT NULL, INDEX IDX_30F400848D9F6D38 (order_id), INDEX IDX_30F40084CCD7E912 (menu_id), PRIMARY KEY (order_id, menu_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE order_menu ADD CONSTRAINT `FK_30F400848D9F6D38` FOREIGN KEY (order_id) REFERENCES `order` (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_menu ADD CONSTRAINT `FK_30F40084CCD7E912` FOREIGN KEY (menu_id) REFERENCES menu (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE material_rental DROP FOREIGN KEY FK_872B4946E308AC6F');
        $this->addSql('ALTER TABLE material_rental DROP FOREIGN KEY FK_872B4946A7CF2329');
        $this->addSql('ALTER TABLE menu_regime DROP FOREIGN KEY FK_79C112A4CCD7E912');
        $this->addSql('ALTER TABLE menu_regime DROP FOREIGN KEY FK_79C112A435E7D534');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F09CCD7E912');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F098D9F6D38');
        $this->addSql('ALTER TABLE user_adresse DROP FOREIGN KEY FK_9B52161CA76ED395');
        $this->addSql('ALTER TABLE user_adresse DROP FOREIGN KEY FK_9B52161C4DE7DC5C');
        $this->addSql('DROP TABLE material_rental');
        $this->addSql('DROP TABLE menu_regime');
        $this->addSql('DROP TABLE notice');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE user_adresse');
        $this->addSql('ALTER TABLE adresse DROP country, DROP postal_code, DROP created_at, DROP updated_at, CHANGE city city VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C35F0816C35F0816 ON adresse (adresse)');
        $this->addSql('ALTER TABLE dish DROP description, DROP price, DROP created_at, DROP updated_at');
        $this->addSql('DROP INDEX UNIQ_7CBE75955E237E06 ON material');
        $this->addSql('ALTER TABLE material ADD quantity_available INT NOT NULL, DROP created_at, DROP updated_at, CHANGE name name VARCHAR(255) NOT NULL, CHANGE daily_rental_price daily_rental_price NUMERIC(10, 0) NOT NULL, CHANGE caution caution NUMERIC(10, 0) NOT NULL');
        $this->addSql('ALTER TABLE menu ADD list_menu LONGTEXT NOT NULL, ADD price_per_person NUMERIC(10, 0) NOT NULL, ADD regime_id INT DEFAULT NULL, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT `FK_7D053A9335E7D534` FOREIGN KEY (regime_id) REFERENCES regime (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_7D053A9335E7D534 ON menu (regime_id)');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993987D540AB');
        $this->addSql('DROP INDEX IDX_F5299398A76ED395 ON `order`');
        $this->addSql('DROP INDEX IDX_F52993987D540AB ON `order`');
        $this->addSql('ALTER TABLE `order` ADD order_date DATETIME NOT NULL, ADD delivery_date DATETIME NOT NULL, ADD delivery_time DATETIME NOT NULL, ADD adresse_id INT NOT NULL, DROP title, DROP delivery_datetime, DROP status, DROP created_at, DROP user_id, DROP notice_id, CHANGE order_price order_price NUMERIC(10, 0) NOT NULL, CHANGE delivery_price delivery_price NUMERIC(10, 0) NOT NULL, CHANGE total_price total_price NUMERIC(10, 0) NOT NULL, CHANGE updated_at date_modified DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT `FK_F52993984DE7DC5C` FOREIGN KEY (adresse_id) REFERENCES adresse (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_F52993984DE7DC5C ON `order` (adresse_id)');
        $this->addSql('ALTER TABLE regime ADD name_remige VARCHAR(255) NOT NULL, DROP name_regime, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY FK_1619C27DA76ED395');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY FK_1619C27D7D540AB');
        $this->addSql('DROP INDEX IDX_1619C27DA76ED395 ON rental');
        $this->addSql('DROP INDEX IDX_1619C27D7D540AB ON rental');
        $this->addSql('ALTER TABLE rental ADD date_rental DATETIME NOT NULL, ADD date_of_rendering DATETIME NOT NULL, ADD rendering_time DATETIME NOT NULL, ADD adresse_id INT NOT NULL, ADD material_id INT NOT NULL, DROP title, DROP date_time_of_rendering, DROP status, DROP created_at, DROP user_id, DROP notice_id, CHANGE rental_price rental_price NUMERIC(10, 0) NOT NULL, CHANGE updated_at date_of_modification DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT `FK_1619C27D4DE7DC5C` FOREIGN KEY (adresse_id) REFERENCES adresse (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT `FK_1619C27DE308AC6F` FOREIGN KEY (material_id) REFERENCES material (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_1619C27D4DE7DC5C ON rental (adresse_id)');
        $this->addSql('CREATE INDEX IDX_1619C27DE308AC6F ON rental (material_id)');
        $this->addSql('ALTER TABLE theme_menu DROP description, DROP created_at, DROP updated_at, CHANGE name_theme name_theme VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE created_at created_at DATE NOT NULL, CHANGE updated_at updated_at DATE DEFAULT NULL');
    }
}
