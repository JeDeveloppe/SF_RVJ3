<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231121184857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item_boite DROP FOREIGN KEY FK_5DBFF63F3C43472D');
        $this->addSql('ALTER TABLE item_boite DROP FOREIGN KEY FK_5DBFF63F126F525E');
        $this->addSql('DROP TABLE item_boite');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item_boite (item_id INT NOT NULL, boite_id INT NOT NULL, INDEX IDX_5DBFF63F3C43472D (boite_id), INDEX IDX_5DBFF63F126F525E (item_id), PRIMARY KEY(item_id, boite_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE item_boite ADD CONSTRAINT FK_5DBFF63F3C43472D FOREIGN KEY (boite_id) REFERENCES boite (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_boite ADD CONSTRAINT FK_5DBFF63F126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
    }
}
