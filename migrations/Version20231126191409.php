<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231126191409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1EAE1A8563');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1EC33F7837');
        $this->addSql('DROP TABLE paiement');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE paiement (id INT AUTO_INCREMENT NOT NULL, means_of_paiement_id INT NOT NULL, document_id INT NOT NULL, token_transaction VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, time_transaction DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_B1DC7A1EC33F7837 (document_id), INDEX IDX_B1DC7A1EAE1A8563 (means_of_paiement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1EAE1A8563 FOREIGN KEY (means_of_paiement_id) REFERENCES means_of_payement (id)');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1EC33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
    }
}
