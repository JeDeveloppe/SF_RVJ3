<?php

namespace App\Controller\Site;

use App\Entity\Panier;
use App\Repository\OccasionRepository;
use App\Repository\PanierRepository;
use App\Repository\TaxRepository;
use App\Service\PanierService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    public function __construct(
        private RequestStack $request,
        private PanierService $panierService,
        private PanierRepository $panierRepository,
        private Security $security,
        private TaxRepository $taxRepository
    )
    {
        
    }
    
    #[Route('/panier', name: 'app_panier')]
    public function index(): Response
    {
        $user = $this->checkUserIsConnected();
        $tax = $this->taxRepository->findOneBy([]);

        $occasions = $this->panierRepository->findOccasionsByUser($user);
        $boites = $this->panierRepository->findBoitesByUser($user);

        $totalOccasions = $this->panierService->totalPriceItems($occasions);
        $totalArticles = $this->panierService->totalPriceItems($boites);

        $totalPanier = $totalArticles + $totalOccasions;

        return $this->render('site/panier/panier.html.twig', [
            'occasions' => $occasions,
            'boites' => $boites,
            'totalOccasions' => $totalOccasions,
            'totalArticles' => $totalArticles,
            'totalPanier' => $totalPanier,
            'tax' => $tax
        ]);
    }

    #[Route('/panier/ajout-occasion/{occasion_id}', name: 'app_panier_add_occasion')]
    public function addOccasion($occasion_id): Response
    {
        $user = $this->checkUserIsConnected();

        $reponse = $this->panierService->addOccasionInCart($occasion_id,$user);

        $this->addFlash($reponse[0], $reponse[1]);

        return $this->redirectToRoute('app_catalogue_occasions');
    }

    #[Route('/panier/ajout-demande/{boite}', name: 'app_panier_add_demande')]
    public function addDemande(): Response
    {
        //TODO
        return $this->redirectToRoute('app_catalogue_boites');
    }

    public function checkUserIsConnected(){
        $user = $this->security->getUser();

        if(!$user){

            $this->addFlash('warning','Vous n\'êtes pas identifé(e)');

            $this->redirectToRoute('app_home');
        }

        return $user;
    }

    #[Route('/panier/delete-item/{item_id}', name: 'app_panier_delete_item')]
    public function deleteItemInPanier($item_id): Response
    {
        $user = $this->checkUserIsConnected();

        $reponse = $this->panierService->deleteItemInCart($item_id,$user);

        $this->addFlash($reponse[0], $reponse[1]);

        return $this->redirectToRoute('app_panier');
    }
}
