<?php

namespace App\Controller\Site;

use App\Entity\Delivery;
use App\Form\ShippingType;
use App\Service\PanierService;
use App\Repository\TaxRepository;
use App\Repository\BoiteRepository;
use App\Repository\PanierRepository;
use App\Repository\DeliveryRepository;
use App\Form\BillingAndDeliveryAddressType;
use App\Repository\ItemRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    public function __construct(
        private RequestStack $request,
        private PanierService $panierService,
        private PanierRepository $panierRepository,
        private Security $security,
        private TaxRepository $taxRepository,
        private DeliveryRepository $deliveryRepository,
        private BoiteRepository $boiteRepository,
        private ItemRepository $itemRepository
    )
    {
        
    }
    
    #[Route('/panier', name: 'app_panier')]
    public function index(Request $request): Response
    {
        $user = $this->security->getUser();
        $paniers = $this->panierRepository->findBy(['user' => $user]);

        if(count($paniers) < 1){

            $this->addFlash('warning', 'Votre panier est vide !');
            
            return $this->redirectToRoute('app_home');
        }

        $shippingForm = $this->createForm(ShippingType::class);
        $shippingForm->handleRequest($request);

        $billingAndDeliveryForm = $this->createForm(BillingAndDeliveryAddressType::class, null, [
            'user' => $this->security->getUser(),
            'shipping' => $shippingForm->get('shipping')->getData()
        ]);
        $billingAndDeliveryForm->handleRequest($request);

        $user = $this->checkUserIsConnected();
        $tax = $this->taxRepository->findOneBy([]);

        $panier_occasions = $this->panierRepository->findOccasionsByUser($user);
        $panier_boites = $this->panierRepository->findBoitesByUser($user);
        $panier_items = $this->panierRepository->findItemsByUser($user);

        $totauxItems = $this->panierService->totauxItems($panier_items);
        $totauxOccasions = $this->panierService->totauxItems($panier_occasions);
        $totauxBoites = $this->panierService->totauxItems($panier_boites);

        $weigthPanier = $totauxBoites['weigth'] + $totauxOccasions['weigth'] + $totauxItems['weigth'];

        if($shippingForm->get('shipping')->getData() == null){


            $deliveryCostWithoutTax = new Delivery();
            $deliveryCostWithoutTax->setPriceExcludingTax(0);

        }else{

            $deliveryCostWithoutTax = $this->deliveryRepository->findCostByDeliveryShippingMethod($shippingForm->get('shipping')->getData(), $weigthPanier);

        }

        $totalPanier = $totauxItems['price'] + $totauxBoites['price'] + $totauxOccasions['price'] + $deliveryCostWithoutTax->getPriceExcludingTax();

        return $this->render('site/panier/panier.html.twig', [
            'occasions' => $panier_occasions,
            'boites' => $panier_boites,
            'items' => $panier_items,
            'weigthPanier' => $weigthPanier,
            'totalItems' => $totauxItems['price'],
            'totalOccasions' => $totauxOccasions['price'],
            'totalBoites' => $totauxBoites['price'],
            'totalPanier' => $totalPanier,
            'tax' => $tax,
            'deliveryCostWithoutTax' => $deliveryCostWithoutTax,
            'shippingForm' => $shippingForm,
            'billingAndDeliveryForm' => $billingAndDeliveryForm,
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

    public function checkUserIsConnected()
    {
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

    #[Route('/panier/ajout-article/', name: 'app_panier_add_article')]
    public function addArticle(Request $request): Response
    {

        $user = $this->checkUserIsConnected();

        $reponse = $this->panierService->addArticleInCart($request->request->get('itemId'),$request->request->get('qte'),$user);

        $this->addFlash($reponse[0], $reponse[1]);

        $boite = $this->boiteRepository->findOneBy(['id' => $request->request->get('boiteId')]);

        return $this->redirectToRoute('app_catalogue_pieces_detachees_demande', [
            'editorSlug' => $boite->getEditor()->getSlug(),
            'slug' => $boite->getSlug(),
            'id' => $boite->getId()
        ]);
    }
}
