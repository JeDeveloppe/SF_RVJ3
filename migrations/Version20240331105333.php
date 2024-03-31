<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240331105333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boite ADD players_max_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE boite ADD CONSTRAINT FK_7718EDEFA06F1854 FOREIGN KEY (players_max_id) REFERENCES numbers_of_players (id)');
        $this->addSql('CREATE INDEX IDX_7718EDEFA06F1854 ON boite (players_max_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boite DROP FOREIGN KEY FK_7718EDEFA06F1854');
        $this->addSql('DROP INDEX IDX_7718EDEFA06F1854 ON boite');
        $this->addSql('ALTER TABLE boite DROP players_max_id');
    }
}
