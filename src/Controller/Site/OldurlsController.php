<?php

namespace App\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class OldurlsController extends AbstractController
{

    #[Route('/catalogue', name: 'catalogue_old')]
    public function catalogueOld(): RedirectResponse
    {

        if($_ENV['APP_ENV'] == 'prod'){
            return $this->redirect($this->generateUrl('app_home') . '#piecesDetachees');
        }

        $url = $this->generateUrl('app_catalogue_switch');

        return new RedirectResponse($url, 301);

    }

    #[Route('/don-de-jeux/partenaires/{country}/', name: 'give_game_old')]
    public function giveGameOld(): RedirectResponse
    {
        $url = $this->generateUrl('app_give_your_games');

        return new RedirectResponse($url, 301);
    }


    #[Route('/nous-soutenir', name: 'support_us_old')]
    public function supportUsOld(): RedirectResponse
    {

        $url = $this->generateUrl('app_support_us');

        return new RedirectResponse($url, 301);
    }


    #[Route('/accueil', name: 'home_old')]
    public function homeOld(): RedirectResponse
    {

        $url = $this->generateUrl('app_home');

        return new RedirectResponse($url, 301);
    }

    #[Route('/projet/qui-sommes-nous/', name: 'wereWeAreOld')]
    public function wereWeAreOld(): RedirectResponse
    {

        $url = $this->generateUrl('app_support_us');

        return new RedirectResponse($url, 301);
    }
    
    #[Route('/carte-des-partenaires/{country}', name: 'partnersMapOld')]
    public function partnersMapOld(): RedirectResponse
    {

        $url = $this->generateUrl('app_partners');

        return new RedirectResponse($url, 301);
    }

}