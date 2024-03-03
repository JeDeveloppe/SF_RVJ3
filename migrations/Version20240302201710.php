<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240302201710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document ADD voucher_discount_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A769848A099 FOREIGN KEY (voucher_discount_id) REFERENCES voucher_discount (id)');
        $this->addSql('CREATE INDEX IDX_D8698A769848A099 ON document (voucher_discount_id)');
        $this->addSql('ALTER TABLE voucher_discount ADD remaining_value_to_use_excluding_tax INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A769848A099');
        $this->addSql('DROP INDEX IDX_D8698A769848A099 ON document');
        $this->addSql('ALTER TABLE document DROP voucher_discount_id');
        $this->addSql('ALTER TABLE voucher_discount DROP remaining_value_to_use_excluding_tax');
    }
}
