<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240925164005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE granderegion ADD country_id INT NOT NULL');
        $this->addSql('ALTER TABLE granderegion ADD CONSTRAINT FK_185A0074F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('CREATE INDEX IDX_185A0074F92F3E70 ON granderegion (country_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE granderegion DROP FOREIGN KEY FK_185A0074F92F3E70');
        $this->addSql('DROP INDEX IDX_185A0074F92F3E70 ON granderegion');
        $this->addSql('ALTER TABLE granderegion DROP country_id');
    }
}
