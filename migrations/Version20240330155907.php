<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240330155907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occasion ADD reserve_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE55913AEBF FOREIGN KEY (reserve_id) REFERENCES reserve (id)');
        $this->addSql('CREATE INDEX IDX_8A66DCE55913AEBF ON occasion (reserve_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE55913AEBF');
        $this->addSql('DROP INDEX IDX_8A66DCE55913AEBF ON occasion');
        $this->addSql('ALTER TABLE occasion DROP reserve_id');
    }
}
