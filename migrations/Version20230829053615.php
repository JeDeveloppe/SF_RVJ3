<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230829053615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE partner (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, collect LONGTEXT DEFAULT NULL, sells LONGTEXT NOT NULL, is_accept_donations TINYINT(1) NOT NULL, full_url VARCHAR(255) NOT NULL, is_sells_spare_parts TINYINT(1) NOT NULL, is_web_shop TINYINT(1) NOT NULL, is_online TINYINT(1) NOT NULL, is_display_on_catalogue_when_search_is_null TINYINT(1) NOT NULL, image_blob LONGBLOB NOT NULL, INDEX IDX_312B3E168BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE partner ADD CONSTRAINT FK_312B3E168BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partner DROP FOREIGN KEY FK_312B3E168BAC62AF');
        $this->addSql('DROP TABLE partner');
    }
}
