<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240413140612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE collection_point ADD shippingmethod_id INT NOT NULL');
        $this->addSql('ALTER TABLE collection_point ADD CONSTRAINT FK_F05D4B77ABA732A8 FOREIGN KEY (shippingmethod_id) REFERENCES shipping_method (id)');
        $this->addSql('CREATE INDEX IDX_F05D4B77ABA732A8 ON collection_point (shippingmethod_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE collection_point DROP FOREIGN KEY FK_F05D4B77ABA732A8');
        $this->addSql('DROP INDEX IDX_F05D4B77ABA732A8 ON collection_point');
        $this->addSql('ALTER TABLE collection_point DROP shippingmethod_id');
    }
}
