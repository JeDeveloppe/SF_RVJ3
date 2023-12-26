<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231224120255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE voucher_discount ADD created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE voucher_discount ADD CONSTRAINT FK_7F47EC87B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_7F47EC87B03A8386 ON voucher_discount (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE voucher_discount DROP FOREIGN KEY FK_7F47EC87B03A8386');
        $this->addSql('DROP INDEX IDX_7F47EC87B03A8386 ON voucher_discount');
        $this->addSql('ALTER TABLE voucher_discount DROP created_by_id');
    }
}
