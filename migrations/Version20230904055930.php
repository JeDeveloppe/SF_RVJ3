<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230904055930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document ADD payment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A764C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D8698A764C3A3BB ON document (payment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A764C3A3BB');
        $this->addSql('DROP INDEX UNIQ_D8698A764C3A3BB ON document');
        $this->addSql('ALTER TABLE document DROP payment_id');
    }
}
