<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240205221730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ambassador ADD CONSTRAINT FK_6C62F0CE5FB123FD FOREIGN KEY (privatecity_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_6C62F0CE5FB123FD ON ambassador (privatecity_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ambassador DROP FOREIGN KEY FK_6C62F0CE5FB123FD');
        $this->addSql('DROP INDEX IDX_6C62F0CE5FB123FD ON ambassador');
    }
}
