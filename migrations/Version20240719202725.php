<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240719202725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ambassador ADD city_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ambassador ADD CONSTRAINT FK_6C62F0CE8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_6C62F0CE8BAC62AF ON ambassador (city_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ambassador DROP FOREIGN KEY FK_6C62F0CE8BAC62AF');
        $this->addSql('DROP INDEX IDX_6C62F0CE8BAC62AF ON ambassador');
        $this->addSql('ALTER TABLE ambassador DROP city_id');
    }
}
