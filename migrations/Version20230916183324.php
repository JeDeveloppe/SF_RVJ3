<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230916183324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, city_id INT NOT NULL, is_facturation TINYINT(1) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, organization VARCHAR(255) DEFAULT NULL, street VARCHAR(255) NOT NULL, rvj2id INT DEFAULT NULL, INDEX IDX_D4E6F81A76ED395 (user_id), INDEX IDX_D4E6F818BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE boite (id INT AUTO_INCREMENT NOT NULL, editor_id INT DEFAULT NULL, created_by_id INT NOT NULL, players_id INT NOT NULL, name VARCHAR(255) NOT NULL, initeditor VARCHAR(255) DEFAULT NULL, year INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, is_deliverable TINYINT(1) NOT NULL, is_occasion TINYINT(1) NOT NULL, weigth INT NOT NULL, age INT NOT NULL, ht_price INT DEFAULT NULL, is_deee TINYINT(1) NOT NULL, is_online TINYINT(1) NOT NULL, rvj2id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', image VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, content_message VARCHAR(255) DEFAULT NULL, INDEX IDX_7718EDEF6995AC4C (editor_id), INDEX IDX_7718EDEFB03A8386 (created_by_id), INDEX IDX_7718EDEFF1849495 (players_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, department_id INT NOT NULL, country_id INT NOT NULL, name VARCHAR(255) NOT NULL, latitude VARCHAR(255) NOT NULL, longitude VARCHAR(255) NOT NULL, postalcode VARCHAR(255) NOT NULL, rvj2id INT DEFAULT NULL, INDEX IDX_2D5B0234AE80F5DF (department_id), INDEX IDX_2D5B0234F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE color (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE condition_occasion (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, isocode VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE delivery (id INT AUTO_INCREMENT NOT NULL, shipping_method_id INT NOT NULL, start INT NOT NULL, end INT NOT NULL, price_excluding_tax INT NOT NULL, INDEX IDX_3781EC105F7D6850 (shipping_method_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE department (id INT AUTO_INCREMENT NOT NULL, country_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_CD1DE18AF92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discount (id INT AUTO_INCREMENT NOT NULL, start INT NOT NULL, end INT NOT NULL, value INT NOT NULL, is_online TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, tax_rate_id INT NOT NULL, sending_method_id INT NOT NULL, document_status_id INT NOT NULL, user_id INT NOT NULL, token VARCHAR(255) NOT NULL, rvj2id INT DEFAULT NULL, quote_number VARCHAR(255) NOT NULL, bill_number VARCHAR(255) DEFAULT NULL, total_excluding_tax INT NOT NULL, total_with_tax INT NOT NULL, delivery_price_excluding_tax INT NOT NULL, is_quote_reminder TINYINT(1) NOT NULL, end_of_quote_validation DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', time_of_sending_quote DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_delete_by_user TINYINT(1) NOT NULL, message LONGTEXT DEFAULT NULL, cost INT DEFAULT NULL, delivery_address LONGTEXT NOT NULL, billing_address LONGTEXT NOT NULL, token_payment VARCHAR(255) DEFAULT NULL, INDEX IDX_D8698A76FDD13F95 (tax_rate_id), INDEX IDX_D8698A761917568 (sending_method_id), INDEX IDX_D8698A7679CF281C (document_status_id), INDEX IDX_D8698A76A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document_line (id INT AUTO_INCREMENT NOT NULL, document_id INT NOT NULL, boite_id INT DEFAULT NULL, occasion_id INT DEFAULT NULL, price_excluding_tax INT NOT NULL, quantity INT NOT NULL, question LONGTEXT DEFAULT NULL, answer LONGTEXT DEFAULT NULL, rvj2idboite INT DEFAULT NULL, rvj2idoccasion INT DEFAULT NULL, INDEX IDX_76A03865C33F7837 (document_id), INDEX IDX_76A038653C43472D (boite_id), INDEX IDX_76A038654034998F (occasion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document_status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, is_to_be_traited_daily TINYINT(1) NOT NULL, action VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE editor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE envelope (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(5) NOT NULL, weight INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE legal_information (id INT AUTO_INCREMENT NOT NULL, tax_id INT NOT NULL, country_company_id INT NOT NULL, updated_by_id INT NOT NULL, street_company VARCHAR(255) NOT NULL, postal_code_company INT NOT NULL, city_company VARCHAR(255) NOT NULL, siret_company VARCHAR(255) NOT NULL, email_company VARCHAR(255) NOT NULL, webmaster_company_name VARCHAR(255) NOT NULL, webmaster_fist_name VARCHAR(255) NOT NULL, webmaster_last_name VARCHAR(255) NOT NULL, company_name VARCHAR(255) NOT NULL, full_url_company VARCHAR(255) NOT NULL, host_name VARCHAR(255) NOT NULL, host_street VARCHAR(255) NOT NULL, host_postal_code INT NOT NULL, host_city VARCHAR(255) NOT NULL, host_phone VARCHAR(20) NOT NULL, is_online TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', publication_manager_first_name VARCHAR(255) NOT NULL, publication_manager_last_name VARCHAR(255) NOT NULL, INDEX IDX_3CBFB562B2A824D8 (tax_id), INDEX IDX_3CBFB562DA9C988B (country_company_id), INDEX IDX_3CBFB562896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE means_of_payement (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movement_occasion (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE numbers_of_players (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, keyword VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE occasion (id INT AUTO_INCREMENT NOT NULL, boite_id INT NOT NULL, box_condition_id INT NOT NULL, equipment_condition_id INT NOT NULL, game_rule_id INT NOT NULL, off_site_sale_id INT DEFAULT NULL, off_site_occasion_sale_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, reference VARCHAR(255) NOT NULL, information VARCHAR(255) DEFAULT NULL, is_new TINYINT(1) NOT NULL, is_online TINYINT(1) NOT NULL, rvj2id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_8A66DCE53C43472D (boite_id), INDEX IDX_8A66DCE540827F21 (box_condition_id), INDEX IDX_8A66DCE52D1C6429 (equipment_condition_id), INDEX IDX_8A66DCE5E18C7C7E (game_rule_id), INDEX IDX_8A66DCE5AB3E7AEE (off_site_sale_id), INDEX IDX_8A66DCE5F72A3FCE (off_site_occasion_sale_id), INDEX IDX_8A66DCE5B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE off_site_occasion_sale (id INT AUTO_INCREMENT NOT NULL, occasion_id INT NOT NULL, movement_id INT NOT NULL, means_of_paiement_id INT NOT NULL, created_by_id INT DEFAULT NULL, movement_time DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', movement_price INT NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_99D2C74A4034998F (occasion_id), INDEX IDX_99D2C74A229E70A7 (movement_id), INDEX IDX_99D2C74AAE1A8563 (means_of_paiement_id), INDEX IDX_99D2C74AB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panier (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, boite_id INT DEFAULT NULL, occasion_id INT DEFAULT NULL, question LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_24CC0DF2A76ED395 (user_id), INDEX IDX_24CC0DF23C43472D (boite_id), INDEX IDX_24CC0DF24034998F (occasion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partner (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, collect LONGTEXT DEFAULT NULL, sells LONGTEXT DEFAULT NULL, is_accept_donations TINYINT(1) NOT NULL, full_url VARCHAR(255) NOT NULL, is_sells_spare_parts TINYINT(1) NOT NULL, is_web_shop TINYINT(1) NOT NULL, is_online TINYINT(1) NOT NULL, is_display_on_catalogue_when_search_is_null TINYINT(1) NOT NULL, is_sell_full_games TINYINT(1) NOT NULL, image VARCHAR(255) NOT NULL, rvj2id INT NOT NULL, INDEX IDX_312B3E168BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, means_of_payment_id INT NOT NULL, document_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', time_of_transaction DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', token_payment VARCHAR(255) NOT NULL, INDEX IDX_6D28840DEC4366A8 (means_of_payment_id), UNIQUE INDEX UNIQ_6D28840DC33F7837 (document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shipping_method (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, is_actived_in_cart TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tax (id INT AUTO_INCREMENT NOT NULL, value INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, country_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nickname VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', lastvisite DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', membership DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', rvj2id INT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F818BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE boite ADD CONSTRAINT FK_7718EDEF6995AC4C FOREIGN KEY (editor_id) REFERENCES editor (id)');
        $this->addSql('ALTER TABLE boite ADD CONSTRAINT FK_7718EDEFB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE boite ADD CONSTRAINT FK_7718EDEFF1849495 FOREIGN KEY (players_id) REFERENCES numbers_of_players (id)');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE delivery ADD CONSTRAINT FK_3781EC105F7D6850 FOREIGN KEY (shipping_method_id) REFERENCES shipping_method (id)');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18AF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76FDD13F95 FOREIGN KEY (tax_rate_id) REFERENCES tax (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A761917568 FOREIGN KEY (sending_method_id) REFERENCES shipping_method (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7679CF281C FOREIGN KEY (document_status_id) REFERENCES document_status (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE document_line ADD CONSTRAINT FK_76A03865C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE document_line ADD CONSTRAINT FK_76A038653C43472D FOREIGN KEY (boite_id) REFERENCES boite (id)');
        $this->addSql('ALTER TABLE document_line ADD CONSTRAINT FK_76A038654034998F FOREIGN KEY (occasion_id) REFERENCES occasion (id)');
        $this->addSql('ALTER TABLE legal_information ADD CONSTRAINT FK_3CBFB562B2A824D8 FOREIGN KEY (tax_id) REFERENCES tax (id)');
        $this->addSql('ALTER TABLE legal_information ADD CONSTRAINT FK_3CBFB562DA9C988B FOREIGN KEY (country_company_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE legal_information ADD CONSTRAINT FK_3CBFB562896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE53C43472D FOREIGN KEY (boite_id) REFERENCES boite (id)');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE540827F21 FOREIGN KEY (box_condition_id) REFERENCES condition_occasion (id)');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE52D1C6429 FOREIGN KEY (equipment_condition_id) REFERENCES condition_occasion (id)');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE5E18C7C7E FOREIGN KEY (game_rule_id) REFERENCES condition_occasion (id)');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE5AB3E7AEE FOREIGN KEY (off_site_sale_id) REFERENCES off_site_occasion_sale (id)');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE5F72A3FCE FOREIGN KEY (off_site_occasion_sale_id) REFERENCES off_site_occasion_sale (id)');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE5B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE off_site_occasion_sale ADD CONSTRAINT FK_99D2C74A4034998F FOREIGN KEY (occasion_id) REFERENCES occasion (id)');
        $this->addSql('ALTER TABLE off_site_occasion_sale ADD CONSTRAINT FK_99D2C74A229E70A7 FOREIGN KEY (movement_id) REFERENCES movement_occasion (id)');
        $this->addSql('ALTER TABLE off_site_occasion_sale ADD CONSTRAINT FK_99D2C74AAE1A8563 FOREIGN KEY (means_of_paiement_id) REFERENCES means_of_payement (id)');
        $this->addSql('ALTER TABLE off_site_occasion_sale ADD CONSTRAINT FK_99D2C74AB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF23C43472D FOREIGN KEY (boite_id) REFERENCES boite (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF24034998F FOREIGN KEY (occasion_id) REFERENCES occasion (id)');
        $this->addSql('ALTER TABLE partner ADD CONSTRAINT FK_312B3E168BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DEC4366A8 FOREIGN KEY (means_of_payment_id) REFERENCES means_of_payement (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DC33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81A76ED395');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F818BAC62AF');
        $this->addSql('ALTER TABLE boite DROP FOREIGN KEY FK_7718EDEF6995AC4C');
        $this->addSql('ALTER TABLE boite DROP FOREIGN KEY FK_7718EDEFB03A8386');
        $this->addSql('ALTER TABLE boite DROP FOREIGN KEY FK_7718EDEFF1849495');
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B0234AE80F5DF');
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B0234F92F3E70');
        $this->addSql('ALTER TABLE delivery DROP FOREIGN KEY FK_3781EC105F7D6850');
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18AF92F3E70');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76FDD13F95');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A761917568');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A7679CF281C');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76A76ED395');
        $this->addSql('ALTER TABLE document_line DROP FOREIGN KEY FK_76A03865C33F7837');
        $this->addSql('ALTER TABLE document_line DROP FOREIGN KEY FK_76A038653C43472D');
        $this->addSql('ALTER TABLE document_line DROP FOREIGN KEY FK_76A038654034998F');
        $this->addSql('ALTER TABLE legal_information DROP FOREIGN KEY FK_3CBFB562B2A824D8');
        $this->addSql('ALTER TABLE legal_information DROP FOREIGN KEY FK_3CBFB562DA9C988B');
        $this->addSql('ALTER TABLE legal_information DROP FOREIGN KEY FK_3CBFB562896DBBDE');
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE53C43472D');
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE540827F21');
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE52D1C6429');
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE5E18C7C7E');
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE5AB3E7AEE');
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE5F72A3FCE');
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE5B03A8386');
        $this->addSql('ALTER TABLE off_site_occasion_sale DROP FOREIGN KEY FK_99D2C74A4034998F');
        $this->addSql('ALTER TABLE off_site_occasion_sale DROP FOREIGN KEY FK_99D2C74A229E70A7');
        $this->addSql('ALTER TABLE off_site_occasion_sale DROP FOREIGN KEY FK_99D2C74AAE1A8563');
        $this->addSql('ALTER TABLE off_site_occasion_sale DROP FOREIGN KEY FK_99D2C74AB03A8386');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2A76ED395');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF23C43472D');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF24034998F');
        $this->addSql('ALTER TABLE partner DROP FOREIGN KEY FK_312B3E168BAC62AF');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DEC4366A8');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DC33F7837');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649F92F3E70');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE boite');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE color');
        $this->addSql('DROP TABLE condition_occasion');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE delivery');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE discount');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE document_line');
        $this->addSql('DROP TABLE document_status');
        $this->addSql('DROP TABLE editor');
        $this->addSql('DROP TABLE envelope');
        $this->addSql('DROP TABLE legal_information');
        $this->addSql('DROP TABLE means_of_payement');
        $this->addSql('DROP TABLE movement_occasion');
        $this->addSql('DROP TABLE numbers_of_players');
        $this->addSql('DROP TABLE occasion');
        $this->addSql('DROP TABLE off_site_occasion_sale');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE partner');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE shipping_method');
        $this->addSql('DROP TABLE tax');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
