<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240522155522 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE manual_invoice (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, address_id INT NOT NULL, reserve_id INT NOT NULL, INDEX IDX_7292F067A76ED395 (user_id), INDEX IDX_7292F067F5B7AF75 (address_id), INDEX IDX_7292F0675913AEBF (reserve_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE manual_invoice ADD CONSTRAINT FK_7292F067A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE manual_invoice ADD CONSTRAINT FK_7292F067F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE manual_invoice ADD CONSTRAINT FK_7292F0675913AEBF FOREIGN KEY (reserve_id) REFERENCES reserve (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE manual_invoice DROP FOREIGN KEY FK_7292F067A76ED395');
        $this->addSql('ALTER TABLE manual_invoice DROP FOREIGN KEY FK_7292F067F5B7AF75');
        $this->addSql('ALTER TABLE manual_invoice DROP FOREIGN KEY FK_7292F0675913AEBF');
        $this->addSql('DROP TABLE manual_invoice');
    }
}
