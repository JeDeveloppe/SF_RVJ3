<?php

namespace App\Service;

use Symfony\Component\Routing\RouterInterface;

class SiteControllerService
{
    public function __construct(
        private RouterInterface $routerInterface
        ){
    }

    public function pagePrestations()
    {
        $siteControllerServiceContent = [
            'header_h1_no_purple'=> 'Nos',
            'header_h1_purple' => 'prestations',
            'header_description' => 'En plus de la valorisation des jeux, l\'association propose différentes prestations. N\'hésitez pas à nous contacter pour en savoir plus ou pour nous faire part de vos demandes !',
            'dark_button_link' => $this->routerInterface->generate('app_prestations'),
            'dark_button_link_archor' => '#id_animations',
            'dark_button_text' => 'Animations',
            'yellow_button_link' => $this->routerInterface->generate('app_prestations'),
            'yellow_button_link_archor' => '#id_inventaires',
            'yellow_button_text' => 'Inventaires',
            'img_asset' => 'prestations/prestation_header.png',
            'img_alt' => 'Image de pièces au détail'
        ];

        return $siteControllerServiceContent;
    }

    public function pageNousSoutenir()
    {
        $siteControllerServiceContent = [
            'header_h1_no_purple'=> 'Soutenir',
            'header_h1_purple' => 'l\'association',
            'header_description' => 'Refaites Vos Jeux, c’est une équipe de bénévoles soucieux de favoriser le réemploi des jeux et le lien social depuis 2020. L’équipe s’affaire tous les jours pour collecter, trier et valoriser les dons faits par les particuliers ou les structures (près de 230 jeux par mois !). L’association propose également différentes prestations : animation de jeux vintage, inventaire des stocks de jeux, formation…',
            'dark_button_link' => $this->routerInterface->generate('app_support_us'),
            'dark_button_link_archor' => '#missions',
            'dark_button_text' => 'Missions',
            'yellow_button_link' => $this->routerInterface->generate('app_support_us'),
            'yellow_button_link_archor' => '#support_us',
            'yellow_button_text' => 'Nous soutenir',
            'img_asset' => 'prestations/prestation_header.png',
            'img_alt' => 'Image de pièces au détail'
        ];

        return $siteControllerServiceContent;
    }

    public function pageOganizeCollection()
    {
        $siteControllerServiceContent = [
            'header_h1_no_purple'=> 'Organiser',
            'header_h1_purple' => 'une collecte',
            'header_description' => 'Vous souhaitez contribuer activement au projet porté par l’association ? Particuliers, structures, écoles, entreprises… où que vous soyez en France, <b>collectez des jeux près de chez vous et faites-les nous parvenir !</b>',
            'dark_button_link' => $this->routerInterface->generate('app_organize_a_collection'),
            'dark_button_link_archor' => '#organiserCollecte',
            'dark_button_text' => 'Organiser une collecte',
            'img_asset' => 'collecte/loveCollection.png',
            'img_alt' => 'Organiser une collecte'
        ];

        return $siteControllerServiceContent;
    }

    public function pageDonnerSesJeux()
    {
        $siteControllerServiceContent = [
            'header_h1_no_purple'=> 'Donner',
            'header_h1_purple' => 'ses jeux',
            'header_description' => 'L’association récupère les jeux de société complets et incomplets ainsi que les pièces détachées (pions, dés, sabliers…). Nous récupérons également les puzzles complets et les jeux éducatifs en boîte carton (pour apprendre à lire, compter…), qu’ils soient complets ou incomplets.',
            'dark_button_link' => $this->routerInterface->generate('app_give_your_games'),
            'dark_button_link_archor' => '#carte-des-points-de-collecte',
            'dark_button_text' => 'Trouvez un point de collecte',
            'img_asset' => 'donner_jeux/donner_piece_de_jeux.png',
            'img_alt' => 'Image donner des pièces de jeux'
        ];

        return $siteControllerServiceContent;
    }

}