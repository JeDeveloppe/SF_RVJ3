<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230906105923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occasion ADD off_site_occasion_sale_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE5F72A3FCE FOREIGN KEY (off_site_occasion_sale_id) REFERENCES off_site_occasion_sale (id)');
        $this->addSql('CREATE INDEX IDX_8A66DCE5F72A3FCE ON occasion (off_site_occasion_sale_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE5F72A3FCE');
        $this->addSql('DROP INDEX IDX_8A66DCE5F72A3FCE ON occasion');
        $this->addSql('ALTER TABLE occasion DROP off_site_occasion_sale_id');
    }
}
