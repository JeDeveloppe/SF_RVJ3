<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240127181427 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
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
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7679CF281C FOREIGN KEY (document_status_id) REFERENCES document_status (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE document_line ADD CONSTRAINT FK_76A03865C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE document_line ADD CONSTRAINT FK_76A038653C43472D FOREIGN KEY (boite_id) REFERENCES boite (id)');
        $this->addSql('ALTER TABLE document_line ADD CONSTRAINT FK_76A038654034998F FOREIGN KEY (occasion_id) REFERENCES occasion (id)');
        $this->addSql('ALTER TABLE document_line ADD CONSTRAINT FK_76A03865126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE document_line_totals ADD CONSTRAINT FK_61F8FDCEC33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE document_parametre ADD CONSTRAINT FK_BEFFB8E0896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE documentsending ADD CONSTRAINT FK_A26C36C1C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE documentsending ADD CONSTRAINT FK_A26C36C15F7D6850 FOREIGN KEY (shipping_method_id) REFERENCES shipping_method (id)');
        $this->addSql('ALTER TABLE item ADD image VARCHAR(255) NOT NULL');
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
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6495FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
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
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A7679CF281C');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76A76ED395');
        $this->addSql('ALTER TABLE document_line DROP FOREIGN KEY FK_76A03865C33F7837');
        $this->addSql('ALTER TABLE document_line DROP FOREIGN KEY FK_76A038653C43472D');
        $this->addSql('ALTER TABLE document_line DROP FOREIGN KEY FK_76A038654034998F');
        $this->addSql('ALTER TABLE document_line DROP FOREIGN KEY FK_76A03865126F525E');
        $this->addSql('ALTER TABLE document_line_totals DROP FOREIGN KEY FK_61F8FDCEC33F7837');
        $this->addSql('ALTER TABLE document_parametre DROP FOREIGN KEY FK_BEFFB8E0896DBBDE');
        $this->addSql('ALTER TABLE documentsending DROP FOREIGN KEY FK_A26C36C1C33F7837');
        $this->addSql('ALTER TABLE documentsending DROP FOREIGN KEY FK_A26C36C15F7D6850');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E4706CB17');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EB03A8386');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E896DBBDE');
        $this->addSql('ALTER TABLE item DROP image');
        $this->addSql('ALTER TABLE item_boite DROP FOREIGN KEY FK_5DBFF63F126F525E');
        $this->addSql('ALTER TABLE item_boite DROP FOREIGN KEY FK_5DBFF63F3C43472D');
        $this->addSql('ALTER TABLE item_group_boite DROP FOREIGN KEY FK_C75C9EBE9259118C');
        $this->addSql('ALTER TABLE item_group_boite DROP FOREIGN KEY FK_C75C9EBE3C43472D');
        $this->addSql('ALTER TABLE item_item_group DROP FOREIGN KEY FK_FAD6771F126F525E');
        $this->addSql('ALTER TABLE item_item_group DROP FOREIGN KEY FK_FAD6771F9259118C');
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
    }
}
