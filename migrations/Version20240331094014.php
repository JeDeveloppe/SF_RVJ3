<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240331094014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boite ADD updated_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE boite ADD CONSTRAINT FK_7718EDEF896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_7718EDEF896DBBDE ON boite (updated_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boite DROP FOREIGN KEY FK_7718EDEF896DBBDE');
        $this->addSql('DROP INDEX IDX_7718EDEF896DBBDE ON boite');
        $this->addSql('ALTER TABLE boite DROP updated_by_id');
    }
}
