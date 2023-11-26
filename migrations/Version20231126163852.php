<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231126163852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE boite_item_group (boite_id INT NOT NULL, item_group_id INT NOT NULL, INDEX IDX_E370062D3C43472D (boite_id), INDEX IDX_E370062D9259118C (item_group_id), PRIMARY KEY(boite_id, item_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE boite_item_group ADD CONSTRAINT FK_E370062D3C43472D FOREIGN KEY (boite_id) REFERENCES boite (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE boite_item_group ADD CONSTRAINT FK_E370062D9259118C FOREIGN KEY (item_group_id) REFERENCES item_group (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boite_item_group DROP FOREIGN KEY FK_E370062D3C43472D');
        $this->addSql('ALTER TABLE boite_item_group DROP FOREIGN KEY FK_E370062D9259118C');
        $this->addSql('DROP TABLE boite_item_group');
    }
}
