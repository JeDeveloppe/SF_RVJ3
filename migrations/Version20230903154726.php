<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230903154726 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE legal_information DROP FOREIGN KEY FK_3CBFB562F92F3E70');
        $this->addSql('DROP INDEX IDX_3CBFB562F92F3E70 ON legal_information');
        $this->addSql('ALTER TABLE legal_information CHANGE country_id country_company_id INT NOT NULL');
        $this->addSql('ALTER TABLE legal_information ADD CONSTRAINT FK_3CBFB562DA9C988B FOREIGN KEY (country_company_id) REFERENCES country (id)');
        $this->addSql('CREATE INDEX IDX_3CBFB562DA9C988B ON legal_information (country_company_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE legal_information DROP FOREIGN KEY FK_3CBFB562DA9C988B');
        $this->addSql('DROP INDEX IDX_3CBFB562DA9C988B ON legal_information');
        $this->addSql('ALTER TABLE legal_information CHANGE country_company_id country_id INT NOT NULL');
        $this->addSql('ALTER TABLE legal_information ADD CONSTRAINT FK_3CBFB562F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('CREATE INDEX IDX_3CBFB562F92F3E70 ON legal_information (country_id)');
    }
}
