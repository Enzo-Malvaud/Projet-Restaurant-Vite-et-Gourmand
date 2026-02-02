<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260202161126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY `FK_7D053A93B5D26913`');
        $this->addSql('DROP INDEX IDX_7D053A93B5D26913 ON menu');
        $this->addSql('ALTER TABLE menu CHANGE id_regime_id regime_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A9335E7D534 FOREIGN KEY (regime_id) REFERENCES regime (id)');
        $this->addSql('CREATE INDEX IDX_7D053A9335E7D534 ON menu (regime_id)');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY `FK_F5299398E86D5C8B`');
        $this->addSql('DROP INDEX IDX_F5299398E86D5C8B ON `order`');
        $this->addSql('ALTER TABLE `order` CHANGE id_adresse_id adresse_id INT NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993984DE7DC5C FOREIGN KEY (adresse_id) REFERENCES adresse (id)');
        $this->addSql('CREATE INDEX IDX_F52993984DE7DC5C ON `order` (adresse_id)');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY `FK_1619C27DE86D5C8B`');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY `FK_1619C27DFB1A6198`');
        $this->addSql('DROP INDEX IDX_1619C27DE86D5C8B ON rental');
        $this->addSql('DROP INDEX IDX_1619C27DFB1A6198 ON rental');
        $this->addSql('ALTER TABLE rental ADD adresse_id INT NOT NULL, ADD material_id INT NOT NULL, DROP id_adresse_id, DROP id_material_id');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT FK_1619C27D4DE7DC5C FOREIGN KEY (adresse_id) REFERENCES adresse (id)');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT FK_1619C27DE308AC6F FOREIGN KEY (material_id) REFERENCES material (id)');
        $this->addSql('CREATE INDEX IDX_1619C27D4DE7DC5C ON rental (adresse_id)');
        $this->addSql('CREATE INDEX IDX_1619C27DE308AC6F ON rental (material_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A9335E7D534');
        $this->addSql('DROP INDEX IDX_7D053A9335E7D534 ON menu');
        $this->addSql('ALTER TABLE menu CHANGE regime_id id_regime_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT `FK_7D053A93B5D26913` FOREIGN KEY (id_regime_id) REFERENCES regime (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_7D053A93B5D26913 ON menu (id_regime_id)');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993984DE7DC5C');
        $this->addSql('DROP INDEX IDX_F52993984DE7DC5C ON `order`');
        $this->addSql('ALTER TABLE `order` CHANGE adresse_id id_adresse_id INT NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT `FK_F5299398E86D5C8B` FOREIGN KEY (id_adresse_id) REFERENCES adresse (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_F5299398E86D5C8B ON `order` (id_adresse_id)');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY FK_1619C27D4DE7DC5C');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY FK_1619C27DE308AC6F');
        $this->addSql('DROP INDEX IDX_1619C27D4DE7DC5C ON rental');
        $this->addSql('DROP INDEX IDX_1619C27DE308AC6F ON rental');
        $this->addSql('ALTER TABLE rental ADD id_adresse_id INT NOT NULL, ADD id_material_id INT NOT NULL, DROP adresse_id, DROP material_id');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT `FK_1619C27DE86D5C8B` FOREIGN KEY (id_adresse_id) REFERENCES adresse (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT `FK_1619C27DFB1A6198` FOREIGN KEY (id_material_id) REFERENCES material (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_1619C27DE86D5C8B ON rental (id_adresse_id)');
        $this->addSql('CREATE INDEX IDX_1619C27DFB1A6198 ON rental (id_material_id)');
    }
}
