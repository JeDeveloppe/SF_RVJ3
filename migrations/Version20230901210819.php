<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230901210819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE movement_occasion (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE occasion (id INT AUTO_INCREMENT NOT NULL, boite_id INT NOT NULL, box_condition_id INT NOT NULL, equipment_condition_id INT NOT NULL, game_rule_id INT NOT NULL, reference VARCHAR(255) NOT NULL, information VARCHAR(255) DEFAULT NULL, is_new TINYINT(1) NOT NULL, is_online TINYINT(1) NOT NULL, INDEX IDX_8A66DCE53C43472D (boite_id), INDEX IDX_8A66DCE540827F21 (box_condition_id), INDEX IDX_8A66DCE52D1C6429 (equipment_condition_id), INDEX IDX_8A66DCE5E18C7C7E (game_rule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE53C43472D FOREIGN KEY (boite_id) REFERENCES boite (id)');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE540827F21 FOREIGN KEY (box_condition_id) REFERENCES condition_occasion (id)');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE52D1C6429 FOREIGN KEY (equipment_condition_id) REFERENCES condition_occasion (id)');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE5E18C7C7E FOREIGN KEY (game_rule_id) REFERENCES condition_occasion (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE53C43472D');
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE540827F21');
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE52D1C6429');
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE5E18C7C7E');
        $this->addSql('DROP TABLE movement_occasion');
        $this->addSql('DROP TABLE occasion');
    }
}
