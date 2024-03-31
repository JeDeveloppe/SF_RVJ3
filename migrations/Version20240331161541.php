<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240331161541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE off_site_occasion_sale DROP address_of_billing_id, DROP address_of_delivery_id');
        $this->addSql('ALTER TABLE off_site_occasion_sale RENAME INDEX fk_99d2c74aa76ed395 TO IDX_99D2C74AA76ED395');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE off_site_occasion_sale ADD address_of_billing_id INT DEFAULT NULL, ADD address_of_delivery_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE off_site_occasion_sale RENAME INDEX idx_99d2c74aa76ed395 TO FK_99D2C74AA76ED395');
    }
}
