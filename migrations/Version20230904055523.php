<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230904055523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, tax_rate_id INT NOT NULL, sending_method_id INT NOT NULL, document_status_id INT NOT NULL, token VARCHAR(255) NOT NULL, rvj2id INT DEFAULT NULL, quote_number INT NOT NULL, bill_number INT DEFAULT NULL, total_excluding_tax INT NOT NULL, total_with_tax INT NOT NULL, delivery_price_excluding_tax INT NOT NULL, is_quote_reminder TINYINT(1) NOT NULL, end_of_quote_validation DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', time_of_sending_quote DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_delete_by_user TINYINT(1) NOT NULL, message VARCHAR(255) DEFAULT NULL, cost INT DEFAULT NULL, delivery_address LONGTEXT NOT NULL, billing_address LONGTEXT NOT NULL, token_payment VARCHAR(255) DEFAULT NULL, INDEX IDX_D8698A76FDD13F95 (tax_rate_id), INDEX IDX_D8698A761917568 (sending_method_id), INDEX IDX_D8698A7679CF281C (document_status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, means_of_payment_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', time_of_transaction DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', token_payment VARCHAR(255) NOT NULL, INDEX IDX_6D28840DEC4366A8 (means_of_payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76FDD13F95 FOREIGN KEY (tax_rate_id) REFERENCES tax (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A761917568 FOREIGN KEY (sending_method_id) REFERENCES shipping_method (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7679CF281C FOREIGN KEY (document_status_id) REFERENCES document_status (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DEC4366A8 FOREIGN KEY (means_of_payment_id) REFERENCES means_of_payement (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76FDD13F95');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A761917568');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A7679CF281C');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DEC4366A8');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE payment');
    }
}
