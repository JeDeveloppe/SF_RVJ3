<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231129082607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE document_line_totals (id INT AUTO_INCREMENT NOT NULL, document_id INT DEFAULT NULL, items_weigth INT NOT NULL, items_price_without_tax INT NOT NULL, occasions_weigth INT NOT NULL, occasions_price_without_tax INT NOT NULL, boites_weigth INT NOT NULL, boites_price_without_tax INT NOT NULL, UNIQUE INDEX UNIQ_61F8FDCEC33F7837 (document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE document_line_totals ADD CONSTRAINT FK_61F8FDCEC33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document_line_totals DROP FOREIGN KEY FK_61F8FDCEC33F7837');
        $this->addSql('DROP TABLE document_line_totals');
    }
}
