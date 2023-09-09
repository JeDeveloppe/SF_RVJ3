<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230909203252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE document_line (id INT AUTO_INCREMENT NOT NULL, document_id INT NOT NULL, boite_id INT DEFAULT NULL, occasion_id INT DEFAULT NULL, price_excluding_tax INT NOT NULL, quantity INT NOT NULL, question LONGTEXT DEFAULT NULL, answer LONGTEXT DEFAULT NULL, INDEX IDX_76A03865C33F7837 (document_id), INDEX IDX_76A038653C43472D (boite_id), INDEX IDX_76A038654034998F (occasion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE document_line ADD CONSTRAINT FK_76A03865C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE document_line ADD CONSTRAINT FK_76A038653C43472D FOREIGN KEY (boite_id) REFERENCES boite (id)');
        $this->addSql('ALTER TABLE document_line ADD CONSTRAINT FK_76A038654034998F FOREIGN KEY (occasion_id) REFERENCES occasion (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document_line DROP FOREIGN KEY FK_76A03865C33F7837');
        $this->addSql('ALTER TABLE document_line DROP FOREIGN KEY FK_76A038653C43472D');
        $this->addSql('ALTER TABLE document_line DROP FOREIGN KEY FK_76A038654034998F');
        $this->addSql('DROP TABLE document_line');
    }
}
