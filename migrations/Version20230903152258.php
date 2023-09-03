<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230903152258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE legal_information (id INT AUTO_INCREMENT NOT NULL, tax_id INT NOT NULL, country_id INT NOT NULL, street_company VARCHAR(255) NOT NULL, postal_code_company INT NOT NULL, city_company VARCHAR(255) NOT NULL, siret_company VARCHAR(255) NOT NULL, email_company VARCHAR(255) NOT NULL, webmaster_company_name VARCHAR(255) NOT NULL, webmaster_fist_name VARCHAR(255) NOT NULL, webmaster_last_name VARCHAR(255) NOT NULL, company_name VARCHAR(255) NOT NULL, full_url_company VARCHAR(255) NOT NULL, host_name VARCHAR(255) NOT NULL, host_street VARCHAR(255) NOT NULL, host_postal_code INT NOT NULL, host_city VARCHAR(255) NOT NULL, INDEX IDX_3CBFB562B2A824D8 (tax_id), INDEX IDX_3CBFB562F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tax (id INT AUTO_INCREMENT NOT NULL, value INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE legal_information ADD CONSTRAINT FK_3CBFB562B2A824D8 FOREIGN KEY (tax_id) REFERENCES tax (id)');
        $this->addSql('ALTER TABLE legal_information ADD CONSTRAINT FK_3CBFB562F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE legal_information DROP FOREIGN KEY FK_3CBFB562B2A824D8');
        $this->addSql('ALTER TABLE legal_information DROP FOREIGN KEY FK_3CBFB562F92F3E70');
        $this->addSql('DROP TABLE legal_information');
        $this->addSql('DROP TABLE tax');
    }
}
