<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240804131923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boite ADD duration_game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE boite ADD CONSTRAINT FK_7718EDEFA1096AAD FOREIGN KEY (duration_game_id) REFERENCES duration_of_game (id)');
        $this->addSql('CREATE INDEX IDX_7718EDEFA1096AAD ON boite (duration_game_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boite DROP FOREIGN KEY FK_7718EDEFA1096AAD');
        $this->addSql('DROP INDEX IDX_7718EDEFA1096AAD ON boite');
        $this->addSql('ALTER TABLE boite DROP duration_game_id');
    }
}
