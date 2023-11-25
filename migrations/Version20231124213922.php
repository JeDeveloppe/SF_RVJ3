<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231124213922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document_parametre ADD updated_by_id INT NOT NULL, ADD is_online TINYINT(1) NOT NULL, ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE document_parametre ADD CONSTRAINT FK_BEFFB8E0896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_BEFFB8E0896DBBDE ON document_parametre (updated_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document_parametre DROP FOREIGN KEY FK_BEFFB8E0896DBBDE');
        $this->addSql('DROP INDEX IDX_BEFFB8E0896DBBDE ON document_parametre');
        $this->addSql('ALTER TABLE document_parametre DROP updated_by_id, DROP is_online, DROP updated_at');
    }
}
