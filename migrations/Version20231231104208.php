<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231231104208 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, city_id INT NOT NULL, is_facturation TINYINT(1) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, organization VARCHAR(255) DEFAULT NULL, street VARCHAR(255) NOT NULL, rvj2id INT DEFAULT NULL, INDEX IDX_D4E6F81A76ED395 (user_id), INDEX IDX_D4E6F818BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE boite (id INT AUTO_INCREMENT NOT NULL, editor_id INT DEFAULT NULL, created_by_id INT NOT NULL, players_id INT NOT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', image VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, initeditor VARCHAR(255) DEFAULT NULL, year INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, is_deliverable TINYINT(1) NOT NULL, is_occasion TINYINT(1) NOT NULL, weigth INT NOT NULL, age INT NOT NULL, ht_price INT DEFAULT NULL, is_deee TINYINT(1) NOT NULL, is_online TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', content LONGTEXT DEFAULT NULL, content_message VARCHAR(255) DEFAULT NULL, rvj2id INT DEFAULT NULL, INDEX IDX_7718EDEF6995AC4C (editor_id), INDEX IDX_7718EDEFB03A8386 (created_by_id), INDEX IDX_7718EDEFF1849495 (players_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE boite_item (boite_id INT NOT NULL, item_id INT NOT NULL, INDEX IDX_BEE481173C43472D (boite_id), INDEX IDX_BEE48117126F525E (item_id), PRIMARY KEY(boite_id, item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, department_id INT NOT NULL, country_id INT NOT NULL, name VARCHAR(255) NOT NULL, latitude VARCHAR(255) NOT NULL, longitude VARCHAR(255) NOT NULL, postalcode VARCHAR(255) NOT NULL, rvj2id INT DEFAULT NULL, INDEX IDX_2D5B0234AE80F5DF (department_id), INDEX IDX_2D5B0234F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE collection_point (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, name VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, is_actived_in_cart TINYINT(1) NOT NULL, INDEX IDX_F05D4B778BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE color (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE condition_occasion (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, isocode VARCHAR(10) NOT NULL, actif_in_inscription_form TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE delivery (id INT AUTO_INCREMENT NOT NULL, shipping_method_id INT NOT NULL, start INT NOT NULL, end INT NOT NULL, price_excluding_tax INT NOT NULL, INDEX IDX_3781EC105F7D6850 (shipping_method_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE department (id INT AUTO_INCREMENT NOT NULL, country_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_CD1DE18AF92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discount (id INT AUTO_INCREMENT NOT NULL, start INT NOT NULL, end INT NOT NULL, value INT NOT NULL, is_online TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, tax_rate_id INT NOT NULL, sending_method_id INT NOT NULL, document_status_id INT NOT NULL, user_id INT NOT NULL, token VARCHAR(255) NOT NULL, rvj2id INT DEFAULT NULL, quote_number VARCHAR(255) NOT NULL, bill_number VARCHAR(255) DEFAULT NULL, total_excluding_tax INT NOT NULL, total_with_tax INT NOT NULL, delivery_price_excluding_tax INT NOT NULL, is_quote_reminder TINYINT(1) NOT NULL, end_of_quote_validation DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', time_of_sending_quote DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_delete_by_user TINYINT(1) NOT NULL, message LONGTEXT DEFAULT NULL, cost INT DEFAULT NULL, delivery_address LONGTEXT NOT NULL, billing_address LONGTEXT NOT NULL, token_payment VARCHAR(255) DEFAULT NULL, sending_by VARCHAR(255) NOT NULL, tax_rate_value INT NOT NULL, is_last_quote_cant_be_deleted TINYINT(1) DEFAULT NULL, INDEX IDX_D8698A76FDD13F95 (tax_rate_id), INDEX IDX_D8698A761917568 (sending_method_id), INDEX IDX_D8698A7679CF281C (document_status_id), INDEX IDX_D8698A76A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document_line (id INT AUTO_INCREMENT NOT NULL, document_id INT NOT NULL, boite_id INT DEFAULT NULL, occasion_id INT DEFAULT NULL, item_id INT DEFAULT NULL, price_excluding_tax INT NOT NULL, quantity INT NOT NULL, question LONGTEXT DEFAULT NULL, answer LONGTEXT DEFAULT NULL, rvj2idboite INT DEFAULT NULL, rvj2idoccasion INT DEFAULT NULL, INDEX IDX_76A03865C33F7837 (document_id), INDEX IDX_76A038653C43472D (boite_id), INDEX IDX_76A038654034998F (occasion_id), INDEX IDX_76A03865126F525E (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document_line_totals (id INT AUTO_INCREMENT NOT NULL, document_id INT DEFAULT NULL, items_weigth INT NOT NULL, items_price_without_tax INT NOT NULL, occasions_weigth INT NOT NULL, occasions_price_without_tax INT NOT NULL, boites_weigth INT NOT NULL, boites_price_without_tax INT NOT NULL, UNIQUE INDEX UNIQ_61F8FDCEC33F7837 (document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document_parametre (id INT AUTO_INCREMENT NOT NULL, updated_by_id INT NOT NULL, billing_tag VARCHAR(10) NOT NULL, quote_tag VARCHAR(10) NOT NULL, delay_before_delete_devis INT NOT NULL, is_online TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', preparation INT NOT NULL, INDEX IDX_BEFFB8E0896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document_status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, is_to_be_traited_daily TINYINT(1) NOT NULL, action VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE editor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE envelope (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(5) NOT NULL, weight INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, envelope_id INT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT NOT NULL, name VARCHAR(255) NOT NULL, stock_for_sale INT NOT NULL, price_excluding_tax INT NOT NULL, weigth INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', reference VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1F1B251E4706CB17 (envelope_id), INDEX IDX_1F1B251EB03A8386 (created_by_id), INDEX IDX_1F1B251E896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_item_group (item_id INT NOT NULL, item_group_id INT NOT NULL, INDEX IDX_FAD6771F126F525E (item_id), INDEX IDX_FAD6771F9259118C (item_group_id), PRIMARY KEY(item_id, item_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_boite (item_id INT NOT NULL, boite_id INT NOT NULL, INDEX IDX_5DBFF63F126F525E (item_id), INDEX IDX_5DBFF63F3C43472D (boite_id), PRIMARY KEY(item_id, boite_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_group_boite (item_group_id INT NOT NULL, boite_id INT NOT NULL, INDEX IDX_C75C9EBE9259118C (item_group_id), INDEX IDX_C75C9EBE3C43472D (boite_id), PRIMARY KEY(item_group_id, boite_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE legal_information (id INT AUTO_INCREMENT NOT NULL, tax_id INT NOT NULL, country_company_id INT NOT NULL, updated_by_id INT NOT NULL, street_company VARCHAR(255) NOT NULL, postal_code_company INT NOT NULL, city_company VARCHAR(255) NOT NULL, siret_company VARCHAR(255) NOT NULL, email_company VARCHAR(255) NOT NULL, webmaster_company_name VARCHAR(255) NOT NULL, webmaster_fist_name VARCHAR(255) NOT NULL, webmaster_last_name VARCHAR(255) NOT NULL, company_name VARCHAR(255) NOT NULL, full_url_company VARCHAR(255) NOT NULL, host_name VARCHAR(255) NOT NULL, host_street VARCHAR(255) NOT NULL, host_postal_code INT NOT NULL, host_city VARCHAR(255) NOT NULL, host_phone VARCHAR(20) NOT NULL, is_online TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', publication_manager_first_name VARCHAR(255) NOT NULL, publication_manager_last_name VARCHAR(255) NOT NULL, INDEX IDX_3CBFB562B2A824D8 (tax_id), INDEX IDX_3CBFB562DA9C988B (country_company_id), INDEX IDX_3CBFB562896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE level (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, name_in_database VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE means_of_payement (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movement_occasion (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE numbers_of_players (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, keyword VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE occasion (id INT AUTO_INCREMENT NOT NULL, boite_id INT NOT NULL, box_condition_id INT NOT NULL, equipment_condition_id INT NOT NULL, game_rule_id INT NOT NULL, off_site_sale_id INT DEFAULT NULL, off_site_occasion_sale_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, reference VARCHAR(255) NOT NULL, information VARCHAR(255) DEFAULT NULL, is_new TINYINT(1) NOT NULL, is_online TINYINT(1) NOT NULL, rvj2id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', price_without_tax INT NOT NULL, discounted_price_without_tax INT NOT NULL, INDEX IDX_8A66DCE53C43472D (boite_id), INDEX IDX_8A66DCE540827F21 (box_condition_id), INDEX IDX_8A66DCE52D1C6429 (equipment_condition_id), INDEX IDX_8A66DCE5E18C7C7E (game_rule_id), INDEX IDX_8A66DCE5AB3E7AEE (off_site_sale_id), INDEX IDX_8A66DCE5F72A3FCE (off_site_occasion_sale_id), INDEX IDX_8A66DCE5B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE off_site_occasion_sale (id INT AUTO_INCREMENT NOT NULL, occasion_id INT NOT NULL, movement_id INT NOT NULL, means_of_paiement_id INT NOT NULL, created_by_id INT DEFAULT NULL, movement_time DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', movement_price INT NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', place_of_transaction VARCHAR(255) DEFAULT NULL, INDEX IDX_99D2C74A4034998F (occasion_id), INDEX IDX_99D2C74A229E70A7 (movement_id), INDEX IDX_99D2C74AAE1A8563 (means_of_paiement_id), INDEX IDX_99D2C74AB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panier (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, boite_id INT DEFAULT NULL, occasion_id INT DEFAULT NULL, item_id INT DEFAULT NULL, question LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', price_without_tax INT DEFAULT NULL, qte INT DEFAULT NULL, unit_price_exclusing_tax INT DEFAULT NULL, INDEX IDX_24CC0DF2A76ED395 (user_id), INDEX IDX_24CC0DF23C43472D (boite_id), INDEX IDX_24CC0DF24034998F (occasion_id), INDEX IDX_24CC0DF2126F525E (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partner (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, collect LONGTEXT DEFAULT NULL, sells LONGTEXT DEFAULT NULL, is_accept_donations TINYINT(1) NOT NULL, full_url VARCHAR(255) NOT NULL, is_sells_spare_parts TINYINT(1) NOT NULL, is_web_shop TINYINT(1) NOT NULL, is_online TINYINT(1) NOT NULL, is_display_on_catalogue_when_search_is_null TINYINT(1) NOT NULL, is_sell_full_games TINYINT(1) NOT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', image VARCHAR(255) DEFAULT NULL, rvj2id INT NOT NULL, INDEX IDX_312B3E168BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, means_of_payment_id INT NOT NULL, document_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', time_of_transaction DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', token_payment VARCHAR(255) NOT NULL, details VARCHAR(255) DEFAULT NULL, INDEX IDX_6D28840DEC4366A8 (means_of_payment_id), UNIQUE INDEX UNIQ_6D28840DC33F7837 (document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_used TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE returndetailstostock (id INT AUTO_INCREMENT NOT NULL, document VARCHAR(30) NOT NULL, question LONGTEXT NOT NULL, answer LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shipping_method (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, is_actived_in_cart TINYINT(1) NOT NULL, price VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE site_setting (id INT AUTO_INCREMENT NOT NULL, block_email_sending TINYINT(1) DEFAULT NULL, marquee LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tax (id INT AUTO_INCREMENT NOT NULL, value INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, country_id INT NOT NULL, level_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nickname VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', lastvisite DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', membership DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', rvj2id INT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649F92F3E70 (country_id), INDEX IDX_8D93D6495FB14BA7 (level_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE voucher_discount (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, email VARCHAR(255) NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', valid_until DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', used TINYINT(1) NOT NULL, number_of_kilos_collected INT NOT NULL, discount_value_excluding_tax INT NOT NULL, end_of_the_collect DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7F47EC87B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F818BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE boite ADD CONSTRAINT FK_7718EDEF6995AC4C FOREIGN KEY (editor_id) REFERENCES editor (id)');
        $this->addSql('ALTER TABLE boite ADD CONSTRAINT FK_7718EDEFB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE boite ADD CONSTRAINT FK_7718EDEFF1849495 FOREIGN KEY (players_id) REFERENCES numbers_of_players (id)');
        $this->addSql('ALTER TABLE boite_item ADD CONSTRAINT FK_BEE481173C43472D FOREIGN KEY (boite_id) REFERENCES boite (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE boite_item ADD CONSTRAINT FK_BEE48117126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE collection_point ADD CONSTRAINT FK_F05D4B778BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE delivery ADD CONSTRAINT FK_3781EC105F7D6850 FOREIGN KEY (shipping_method_id) REFERENCES shipping_method (id)');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18AF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76FDD13F95 FOREIGN KEY (tax_rate_id) REFERENCES tax (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A761917568 FOREIGN KEY (sending_method_id) REFERENCES shipping_method (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7679CF281C FOREIGN KEY (document_status_id) REFERENCES document_status (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE document_line ADD CONSTRAINT FK_76A03865C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE document_line ADD CONSTRAINT FK_76A038653C43472D FOREIGN KEY (boite_id) REFERENCES boite (id)');
        $this->addSql('ALTER TABLE document_line ADD CONSTRAINT FK_76A038654034998F FOREIGN KEY (occasion_id) REFERENCES occasion (id)');
        $this->addSql('ALTER TABLE document_line ADD CONSTRAINT FK_76A03865126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE document_line_totals ADD CONSTRAINT FK_61F8FDCEC33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE document_parametre ADD CONSTRAINT FK_BEFFB8E0896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E4706CB17 FOREIGN KEY (envelope_id) REFERENCES envelope (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE item_item_group ADD CONSTRAINT FK_FAD6771F126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_item_group ADD CONSTRAINT FK_FAD6771F9259118C FOREIGN KEY (item_group_id) REFERENCES item_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_boite ADD CONSTRAINT FK_5DBFF63F126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_boite ADD CONSTRAINT FK_5DBFF63F3C43472D FOREIGN KEY (boite_id) REFERENCES boite (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_group_boite ADD CONSTRAINT FK_C75C9EBE9259118C FOREIGN KEY (item_group_id) REFERENCES item_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_group_boite ADD CONSTRAINT FK_C75C9EBE3C43472D FOREIGN KEY (boite_id) REFERENCES boite (id) ON DELETE CASCADE');
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
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE partner ADD CONSTRAINT FK_312B3E168BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DEC4366A8 FOREIGN KEY (means_of_payment_id) REFERENCES means_of_payement (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DC33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D6495FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
        $this->addSql('ALTER TABLE voucher_discount ADD CONSTRAINT FK_7F47EC87B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81A76ED395');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F818BAC62AF');
        $this->addSql('ALTER TABLE boite DROP FOREIGN KEY FK_7718EDEF6995AC4C');
        $this->addSql('ALTER TABLE boite DROP FOREIGN KEY FK_7718EDEFB03A8386');
        $this->addSql('ALTER TABLE boite DROP FOREIGN KEY FK_7718EDEFF1849495');
        $this->addSql('ALTER TABLE boite_item DROP FOREIGN KEY FK_BEE481173C43472D');
        $this->addSql('ALTER TABLE boite_item DROP FOREIGN KEY FK_BEE48117126F525E');
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B0234AE80F5DF');
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B0234F92F3E70');
        $this->addSql('ALTER TABLE collection_point DROP FOREIGN KEY FK_F05D4B778BAC62AF');
        $this->addSql('ALTER TABLE delivery DROP FOREIGN KEY FK_3781EC105F7D6850');
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18AF92F3E70');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76FDD13F95');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A761917568');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A7679CF281C');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76A76ED395');
        $this->addSql('ALTER TABLE document_line DROP FOREIGN KEY FK_76A03865C33F7837');
        $this->addSql('ALTER TABLE document_line DROP FOREIGN KEY FK_76A038653C43472D');
        $this->addSql('ALTER TABLE document_line DROP FOREIGN KEY FK_76A038654034998F');
        $this->addSql('ALTER TABLE document_line DROP FOREIGN KEY FK_76A03865126F525E');
        $this->addSql('ALTER TABLE document_line_totals DROP FOREIGN KEY FK_61F8FDCEC33F7837');
        $this->addSql('ALTER TABLE document_parametre DROP FOREIGN KEY FK_BEFFB8E0896DBBDE');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E4706CB17');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EB03A8386');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E896DBBDE');
        $this->addSql('ALTER TABLE item_item_group DROP FOREIGN KEY FK_FAD6771F126F525E');
        $this->addSql('ALTER TABLE item_item_group DROP FOREIGN KEY FK_FAD6771F9259118C');
        $this->addSql('ALTER TABLE item_boite DROP FOREIGN KEY FK_5DBFF63F126F525E');
        $this->addSql('ALTER TABLE item_boite DROP FOREIGN KEY FK_5DBFF63F3C43472D');
        $this->addSql('ALTER TABLE item_group_boite DROP FOREIGN KEY FK_C75C9EBE9259118C');
        $this->addSql('ALTER TABLE item_group_boite DROP FOREIGN KEY FK_C75C9EBE3C43472D');
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
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2126F525E');
        $this->addSql('ALTER TABLE partner DROP FOREIGN KEY FK_312B3E168BAC62AF');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DEC4366A8');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DC33F7837');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649F92F3E70');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6495FB14BA7');
        $this->addSql('ALTER TABLE voucher_discount DROP FOREIGN KEY FK_7F47EC87B03A8386');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE boite');
        $this->addSql('DROP TABLE boite_item');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE collection_point');
        $this->addSql('DROP TABLE color');
        $this->addSql('DROP TABLE condition_occasion');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE delivery');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE discount');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE document_line');
        $this->addSql('DROP TABLE document_line_totals');
        $this->addSql('DROP TABLE document_parametre');
        $this->addSql('DROP TABLE document_status');
        $this->addSql('DROP TABLE editor');
        $this->addSql('DROP TABLE envelope');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE item_item_group');
        $this->addSql('DROP TABLE item_boite');
        $this->addSql('DROP TABLE item_group');
        $this->addSql('DROP TABLE item_group_boite');
        $this->addSql('DROP TABLE legal_information');
        $this->addSql('DROP TABLE level');
        $this->addSql('DROP TABLE means_of_payement');
        $this->addSql('DROP TABLE movement_occasion');
        $this->addSql('DROP TABLE numbers_of_players');
        $this->addSql('DROP TABLE occasion');
        $this->addSql('DROP TABLE off_site_occasion_sale');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE partner');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE reset_password');
        $this->addSql('DROP TABLE returndetailstostock');
        $this->addSql('DROP TABLE shipping_method');
        $this->addSql('DROP TABLE site_setting');
        $this->addSql('DROP TABLE tax');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE voucher_discount');
        $this->addSql('DROP TABLE messenger_messages');
    }
}