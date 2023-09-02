<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230902101721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occasion ADD movement_price INT DEFAULT NULL, CHANGE movement_id movement_id INT DEFAULT NULL, CHANGE means_of_paiement_id means_of_paiement_id INT DEFAULT NULL, CHANGE movement_time movement_time DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occasion DROP movement_price, CHANGE movement_id movement_id INT NOT NULL, CHANGE means_of_paiement_id means_of_paiement_id INT NOT NULL, CHANGE movement_time movement_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
