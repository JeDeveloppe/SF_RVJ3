<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231120190454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, stock_for_sale INT NOT NULL, price_excluding_tax INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_item_group (item_id INT NOT NULL, item_group_id INT NOT NULL, INDEX IDX_FAD6771F126F525E (item_id), INDEX IDX_FAD6771F9259118C (item_group_id), PRIMARY KEY(item_id, item_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_boite (item_id INT NOT NULL, boite_id INT NOT NULL, INDEX IDX_5DBFF63F126F525E (item_id), INDEX IDX_5DBFF63F3C43472D (boite_id), PRIMARY KEY(item_id, boite_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_group_boite (item_group_id INT NOT NULL, boite_id INT NOT NULL, INDEX IDX_C75C9EBE9259118C (item_group_id), INDEX IDX_C75C9EBE3C43472D (boite_id), PRIMARY KEY(item_group_id, boite_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE item_item_group ADD CONSTRAINT FK_FAD6771F126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_item_group ADD CONSTRAINT FK_FAD6771F9259118C FOREIGN KEY (item_group_id) REFERENCES item_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_boite ADD CONSTRAINT FK_5DBFF63F126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_boite ADD CONSTRAINT FK_5DBFF63F3C43472D FOREIGN KEY (boite_id) REFERENCES boite (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_group_boite ADD CONSTRAINT FK_C75C9EBE9259118C FOREIGN KEY (item_group_id) REFERENCES item_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_group_boite ADD CONSTRAINT FK_C75C9EBE3C43472D FOREIGN KEY (boite_id) REFERENCES boite (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item_item_group DROP FOREIGN KEY FK_FAD6771F126F525E');
        $this->addSql('ALTER TABLE item_item_group DROP FOREIGN KEY FK_FAD6771F9259118C');
        $this->addSql('ALTER TABLE item_boite DROP FOREIGN KEY FK_5DBFF63F126F525E');
        $this->addSql('ALTER TABLE item_boite DROP FOREIGN KEY FK_5DBFF63F3C43472D');
        $this->addSql('ALTER TABLE item_group_boite DROP FOREIGN KEY FK_C75C9EBE9259118C');
        $this->addSql('ALTER TABLE item_group_boite DROP FOREIGN KEY FK_C75C9EBE3C43472D');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE item_item_group');
        $this->addSql('DROP TABLE item_boite');
        $this->addSql('DROP TABLE item_group');
        $this->addSql('DROP TABLE item_group_boite');
    }
}
