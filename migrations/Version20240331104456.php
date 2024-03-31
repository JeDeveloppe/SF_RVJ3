<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240331104456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boite DROP FOREIGN KEY FK_7718EDEFF1849495');
        $this->addSql('DROP INDEX IDX_7718EDEFF1849495 ON boite');
        $this->addSql('ALTER TABLE boite CHANGE players_id players_min_id INT NOT NULL');
        $this->addSql('ALTER TABLE boite ADD CONSTRAINT FK_7718EDEFE56D5BD6 FOREIGN KEY (players_min_id) REFERENCES numbers_of_players (id)');
        $this->addSql('CREATE INDEX IDX_7718EDEFE56D5BD6 ON boite (players_min_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boite DROP FOREIGN KEY FK_7718EDEFE56D5BD6');
        $this->addSql('DROP INDEX IDX_7718EDEFE56D5BD6 ON boite');
        $this->addSql('ALTER TABLE boite CHANGE players_min_id players_id INT NOT NULL');
        $this->addSql('ALTER TABLE boite ADD CONSTRAINT FK_7718EDEFF1849495 FOREIGN KEY (players_id) REFERENCES numbers_of_players (id)');
        $this->addSql('CREATE INDEX IDX_7718EDEFF1849495 ON boite (players_id)');
    }
}
