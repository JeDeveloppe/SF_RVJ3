<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240327202349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reserve (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, content VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1FE0EA22B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reserve ADD CONSTRAINT FK_1FE0EA22B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE occasion ADD reserve_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE55913AEBF FOREIGN KEY (reserve_id) REFERENCES reserve (id)');
        $this->addSql('CREATE INDEX IDX_8A66DCE55913AEBF ON occasion (reserve_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE55913AEBF');
        $this->addSql('ALTER TABLE reserve DROP FOREIGN KEY FK_1FE0EA22B03A8386');
        $this->addSql('DROP TABLE reserve');
        $this->addSql('DROP INDEX IDX_8A66DCE55913AEBF ON occasion');
        $this->addSql('ALTER TABLE occasion DROP reserve_id');
    }
}
