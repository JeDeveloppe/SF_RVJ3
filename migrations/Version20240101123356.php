<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240101123356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A761917568');
        $this->addSql('DROP INDEX IDX_D8698A761917568 ON document');
        $this->addSql('ALTER TABLE document DROP sending_method_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document ADD sending_method_id INT NOT NULL');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A761917568 FOREIGN KEY (sending_method_id) REFERENCES shipping_method (id)');
        $this->addSql('CREATE INDEX IDX_D8698A761917568 ON document (sending_method_id)');
    }
}
