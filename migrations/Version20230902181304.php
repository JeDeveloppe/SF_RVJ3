<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230902181304 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE off_site_occasion_sale ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE off_site_occasion_sale ADD CONSTRAINT FK_99D2C74AB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_99D2C74AB03A8386 ON off_site_occasion_sale (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE off_site_occasion_sale DROP FOREIGN KEY FK_99D2C74AB03A8386');
        $this->addSql('DROP INDEX IDX_99D2C74AB03A8386 ON off_site_occasion_sale');
        $this->addSql('ALTER TABLE off_site_occasion_sale DROP created_by_id');
    }
}
