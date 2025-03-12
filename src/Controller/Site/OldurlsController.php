<?php

namespace App\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

use function PHPUnit\Framework\throwException;

class OldurlsController extends AbstractController
{

    private $PERMANENT_REDIRECTION = 301;
    private $TEMPORAIRE_REDIRECTION = 302;

    #[Route('/catalogue', name: 'catalogue_old')]
    public function catalogueOld(): RedirectResponse
    {

        if($_ENV['APP_ENV'] == 'prod'){
            return $this->redirect($this->generateUrl('app_home') . '#piecesDetachees');
        }

        $url = $this->generateUrl('app_catalogue_switch');

        return new RedirectResponse($url, $this->TEMPORAIRE_REDIRECTION);

    }

    #[Route('/don-de-jeux/partenaires/{country}/', name: 'give_game_by_country_old')]
    public function giveGameByCountryOld(): RedirectResponse
    {
        $url = $this->generateUrl('app_give_your_games');

        return new RedirectResponse($url, $this->PERMANENT_REDIRECTION);
    }

    #[Route('/don-de-jeux/', name: 'give_game_old')]
    public function giveGameOld(): RedirectResponse
    {
        $url = $this->generateUrl('app_give_your_games');

        return new RedirectResponse($url, $this->PERMANENT_REDIRECTION);
    }

    #[Route('/nous-soutenir', name: 'support_us_old')]
    public function supportUsOld(): RedirectResponse
    {

        $url = $this->generateUrl('app_support_us');

        return new RedirectResponse($url, $this->PERMANENT_REDIRECTION);
    }


    #[Route('/accueil', name: 'home_old')]
    public function homeOld(): RedirectResponse
    {

        $url = $this->generateUrl('app_home');

        return new RedirectResponse($url, $this->PERMANENT_REDIRECTION);
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

        return new RedirectResponse($url, $this->PERMANENT_REDIRECTION);
    }

    #[Route('/conditions-generale-de-vente', name: 'cgvOld')]
    public function cgvOld(): RedirectResponse
    {

        $url = $this->generateUrl('app_conditions_generale_de_vente');

        return new RedirectResponse($url, $this->PERMANENT_REDIRECTION);
    }

    
    #[Route('/jeu/{editor}/{id}/{slug}/', name: 'piecesDetacheesOld')]
    public function piecesDetacheesOld($id, $editor, $slug): RedirectResponse
    {
        $url = $this->generateUrl('catalogue_pieces_detachees_articles_d_une_boite', [
            'id' => $id,
            'editorSlug' => $editor,
            'boiteSlug' => $slug,
            'year' => NULL
        ]);

        return new RedirectResponse($url, $this->PERMANENT_REDIRECTION);
    }

    // https://www.refaitesvosjeux.fr/comment-ca-marche/tarifs/
}