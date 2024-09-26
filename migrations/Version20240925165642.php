<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240925165642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18A5FF94818 FOREIGN KEY (grande_region_id) REFERENCES granderegion (id)');
        $this->addSql('CREATE INDEX IDX_CD1DE18A5FF94818 ON department (grande_region_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18A5FF94818');
        $this->addSql('DROP INDEX IDX_CD1DE18A5FF94818 ON department');
    }
}
