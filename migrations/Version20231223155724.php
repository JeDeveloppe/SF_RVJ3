<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231223155724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE boite_item (boite_id INT NOT NULL, item_id INT NOT NULL, INDEX IDX_BEE481173C43472D (boite_id), INDEX IDX_BEE48117126F525E (item_id), PRIMARY KEY(boite_id, item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_boite (item_id INT NOT NULL, boite_id INT NOT NULL, INDEX IDX_5DBFF63F126F525E (item_id), INDEX IDX_5DBFF63F3C43472D (boite_id), PRIMARY KEY(item_id, boite_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE boite_item ADD CONSTRAINT FK_BEE481173C43472D FOREIGN KEY (boite_id) REFERENCES boite (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE boite_item ADD CONSTRAINT FK_BEE48117126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_boite ADD CONSTRAINT FK_5DBFF63F126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_boite ADD CONSTRAINT FK_5DBFF63F3C43472D FOREIGN KEY (boite_id) REFERENCES boite (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boite_item DROP FOREIGN KEY FK_BEE481173C43472D');
        $this->addSql('ALTER TABLE boite_item DROP FOREIGN KEY FK_BEE48117126F525E');
        $this->addSql('ALTER TABLE item_boite DROP FOREIGN KEY FK_5DBFF63F126F525E');
        $this->addSql('ALTER TABLE item_boite DROP FOREIGN KEY FK_5DBFF63F3C43472D');
        $this->addSql('DROP TABLE boite_item');
        $this->addSql('DROP TABLE item_boite');
    }
}
