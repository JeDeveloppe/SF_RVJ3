<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230902160437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE off_site_occasion_sale (id INT AUTO_INCREMENT NOT NULL, occasion_id INT NOT NULL, movement_id INT NOT NULL, means_of_paiement_id INT NOT NULL, movement_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', movement_price INT NOT NULL, INDEX IDX_99D2C74A4034998F (occasion_id), INDEX IDX_99D2C74A229E70A7 (movement_id), INDEX IDX_99D2C74AAE1A8563 (means_of_paiement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE off_site_occasion_sale ADD CONSTRAINT FK_99D2C74A4034998F FOREIGN KEY (occasion_id) REFERENCES occasion (id)');
        $this->addSql('ALTER TABLE off_site_occasion_sale ADD CONSTRAINT FK_99D2C74A229E70A7 FOREIGN KEY (movement_id) REFERENCES movement_occasion (id)');
        $this->addSql('ALTER TABLE off_site_occasion_sale ADD CONSTRAINT FK_99D2C74AAE1A8563 FOREIGN KEY (means_of_paiement_id) REFERENCES means_of_payement (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE off_site_occasion_sale DROP FOREIGN KEY FK_99D2C74A4034998F');
        $this->addSql('ALTER TABLE off_site_occasion_sale DROP FOREIGN KEY FK_99D2C74A229E70A7');
        $this->addSql('ALTER TABLE off_site_occasion_sale DROP FOREIGN KEY FK_99D2C74AAE1A8563');
        $this->addSql('DROP TABLE off_site_occasion_sale');
    }
}
