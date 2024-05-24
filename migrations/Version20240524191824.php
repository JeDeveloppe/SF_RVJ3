<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240524191824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reserve ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reserve ADD CONSTRAINT FK_1FE0EA22A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_1FE0EA22A76ED395 ON reserve (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reserve DROP FOREIGN KEY FK_1FE0EA22A76ED395');
        $this->addSql('DROP INDEX IDX_1FE0EA22A76ED395 ON reserve');
        $this->addSql('ALTER TABLE reserve DROP user_id');
    }
}
