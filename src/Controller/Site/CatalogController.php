<?php

namespace App\Controller\Site;

use App\Repository\BoiteRepository;
use App\Repository\OccasionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CatalogController extends AbstractController
{
    public function __construct(
        private BoiteRepository $boiteRepository,
        private OccasionRepository $occasionRepository
    )
    {
        
    }
    
    #[Route('/catalogue-pieces-detachees', name: 'app_catalogue_boites')]
    public function catalogueBoites(): Response
    {
        $boites = $this->boiteRepository->findBy(['isOnline' => true]);

        return $this->render('site/catalog/les_pieces_detachees.html.twig', [
            'boites' => $boites,
        ]);
    }

    #[Route('/catalogue-jeux-occasion', name: 'app_catalogue_occasions')]
    public function catalogueOccasions(): Response
    {
        $occasions = $this->occasionRepository->findBy(['isOnline' => true]);

        return $this->render('site/catalog/les_occasions.html.twig', [
            'occasions' => $occasions,
        ]);
    }

    #[Route('/jeu-occasion/{reference}/{editeur}/{name}', name: 'app_occasion')]
    public function occasion(): Response
    {
        $occasions = $this->occasionRepository->findBy(['isOnline' => true]);

        return $this->render('site/catalog/occasion.html.twig', [
            'occasions' => $occasions,
        ]);
    }
}
