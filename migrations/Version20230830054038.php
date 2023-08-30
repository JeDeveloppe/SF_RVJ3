<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230830054038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE boite (id INT AUTO_INCREMENT NOT NULL, editor_id INT DEFAULT NULL, created_by_id INT NOT NULL, name VARCHAR(255) NOT NULL, initeditor VARCHAR(255) DEFAULT NULL, year INT DEFAULT NULL, image_blob LONGBLOB NOT NULL, slug VARCHAR(255) NOT NULL, is_deliverable TINYINT(1) NOT NULL, is_occasion TINYINT(1) NOT NULL, weigth INT NOT NULL, age INT NOT NULL, players INT NOT NULL, ht_price INT DEFAULT NULL, is_deee TINYINT(1) NOT NULL, is_online TINYINT(1) NOT NULL, rvj2id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_direct_sale TINYINT(1) NOT NULL, INDEX IDX_7718EDEF6995AC4C (editor_id), INDEX IDX_7718EDEFB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE boite ADD CONSTRAINT FK_7718EDEF6995AC4C FOREIGN KEY (editor_id) REFERENCES editor (id)');
        $this->addSql('ALTER TABLE boite ADD CONSTRAINT FK_7718EDEFB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boite DROP FOREIGN KEY FK_7718EDEF6995AC4C');
        $this->addSql('ALTER TABLE boite DROP FOREIGN KEY FK_7718EDEFB03A8386');
        $this->addSql('DROP TABLE boite');
    }
}
