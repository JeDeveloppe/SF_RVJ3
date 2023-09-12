<?php

namespace App\Controller\Site;

use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    public function __construct(
        private RequestStack $request,
        private PanierService $panierService
    )
    {
        
    }
    
    #[Route('/panier', name: 'app_panier')]
    public function index(): Response
    {
        return $this->render('site/panier/index.html.twig', [
            'controller_name' => 'PanierController',
        ]);
    }

    #[Route('/panier/ajout-occasion/{occasion}', name: 'app_panier_add_occasion')]
    public function addOccasion(): Response
    {
        //TODO
        return $this->redirectToRoute('app_catalogue_occasions');
    }

    #[Route('/panier/ajout-demande/{boite}', name: 'app_panier_add_demande')]
    public function addDemande(): Response
    {
        //TODO
        return $this->redirectToRoute('app_catalogue_boites');
    }
}
