<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231125161344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document_line ADD item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE document_line ADD CONSTRAINT FK_76A03865126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('CREATE INDEX IDX_76A03865126F525E ON document_line (item_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document_line DROP FOREIGN KEY FK_76A03865126F525E');
        $this->addSql('DROP INDEX IDX_76A03865126F525E ON document_line');
        $this->addSql('ALTER TABLE document_line DROP item_id');
    }
}
