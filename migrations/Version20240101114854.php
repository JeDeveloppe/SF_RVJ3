<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240101114854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE documentsending (id INT AUTO_INCREMENT NOT NULL, document_id INT NOT NULL, shipping_method_id INT NOT NULL, sending_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', sending_number VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_A26C36C1C33F7837 (document_id), INDEX IDX_A26C36C15F7D6850 (shipping_method_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE documentsending ADD CONSTRAINT FK_A26C36C1C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE documentsending ADD CONSTRAINT FK_A26C36C15F7D6850 FOREIGN KEY (shipping_method_id) REFERENCES shipping_method (id)');
        $this->addSql('ALTER TABLE document DROP goods_send_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE documentsending DROP FOREIGN KEY FK_A26C36C1C33F7837');
        $this->addSql('ALTER TABLE documentsending DROP FOREIGN KEY FK_A26C36C15F7D6850');
        $this->addSql('DROP TABLE documentsending');
        $this->addSql('ALTER TABLE document ADD goods_send_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
