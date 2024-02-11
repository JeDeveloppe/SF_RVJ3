<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240205203006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ambassador (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, organization VARCHAR(255) DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, phone VARCHAR(18) NOT NULL, email VARCHAR(255) NOT NULL, isshowname TINYINT(1) DEFAULT NULL, isshowaddress TINYINT(1) DEFAULT NULL, INDEX IDX_6C62F0CE8BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ambassador ADD CONSTRAINT FK_6C62F0CE8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ambassador DROP FOREIGN KEY FK_6C62F0CE8BAC62AF');
        $this->addSql('DROP TABLE ambassador');
    }
}
