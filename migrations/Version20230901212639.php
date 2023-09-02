<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230901212639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occasion ADD movement_id INT NOT NULL, ADD means_of_paiement_id INT NOT NULL, ADD movement_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE5229E70A7 FOREIGN KEY (movement_id) REFERENCES movement_occasion (id)');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE5AE1A8563 FOREIGN KEY (means_of_paiement_id) REFERENCES means_of_payement (id)');
        $this->addSql('CREATE INDEX IDX_8A66DCE5229E70A7 ON occasion (movement_id)');
        $this->addSql('CREATE INDEX IDX_8A66DCE5AE1A8563 ON occasion (means_of_paiement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE5229E70A7');
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE5AE1A8563');
        $this->addSql('DROP INDEX IDX_8A66DCE5229E70A7 ON occasion');
        $this->addSql('DROP INDEX IDX_8A66DCE5AE1A8563 ON occasion');
        $this->addSql('ALTER TABLE occasion DROP movement_id, DROP means_of_paiement_id, DROP movement_time');
    }
}
