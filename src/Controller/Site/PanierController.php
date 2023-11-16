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

        $occasions = $this->panierRepository->findOccasionsByUser($user);
        $boites = $this->panierRepository->findBoitesByUser($user);

        return $this->render('site/panier/panier.html.twig', [
            'occasions' => $occasions,
            'boites' => $boites,
            'tax' => $this->taxRepository->findOneBy([])
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

    #[Route('/panier/ajout-occasion/{occasion_id}', name: 'app_panier_delete_item')]
    public function deleteItemInPanier($item): Response
    {
        $user = $this->checkUserIsConnected();

        //TODO DELETE ITEM IN PANIER
        $reponse = $this->panierService->addOccasionInCart($occasion_id,$user);

        $this->addFlash($reponse[0], $reponse[1]);

        return $this->redirectToRoute('app_catalogue_occasions');
    }
}
