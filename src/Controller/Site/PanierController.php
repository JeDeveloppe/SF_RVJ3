<?php

namespace App\Controller\Site;

use App\Entity\Address;
use App\Entity\Delivery;
use App\Form\ShippingType;
use App\Service\PanierService;
use App\Service\DocumentService;
use App\Repository\TaxRepository;
use App\Repository\ItemRepository;
use App\Repository\BoiteRepository;
use App\Repository\PanierRepository;
use App\Repository\AddressRepository;
use App\Repository\DeliveryRepository;
use App\Form\BillingAndDeliveryAddressType;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\ShippingMethodRepository;
use App\Repository\CollectionPointRepository;
use App\Repository\DocumentRepository;
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
        private AddressRepository $addressRepository,
        private Security $security,
        private ShippingMethodRepository $shippingMethodRepository,
        private CollectionPointRepository $collectionPointRepository,
        private DeliveryRepository $deliveryRepository,
        private BoiteRepository $boiteRepository,
        private DocumentService $documentService,
        private ItemRepository $itemRepository,
        private DocumentRepository $documentRepository
    )
    {
        
    }
    
    #[Route('/panier', name: 'panier')]
    public function index(Request $request): Response
    {
        $user = $this->security->getUser();
        $user = $this->checkUserIsConnected();

        $paniers = $this->panierRepository->findBy(['user' => $user]);
        if(count($paniers) < 1){

            $this->addFlash('warning', 'Votre panier est vide !');
            
            return $this->redirectToRoute('app_home');
        }

        $occasionInPanier = 0;
        foreach($paniers as $panier){
            if($panier->getOccasion() != NULL){
                $occasionInPanier += 1;
            }
        }

        $shippingForm = $this->createForm(ShippingType::class, null, ['occasionInPanier' => $occasionInPanier]);
        $shippingForm->handleRequest($request);

        $reponses = $this->panierService->calculateAllCart($user,$shippingForm->get('shipping')->getData());

        $billingAndDeliveryForm = $this->createForm(BillingAndDeliveryAddressType::class, null, [
            'user' => $this->security->getUser(),
            'shipping' => $shippingForm->get('shipping')->getData(),
            'redirectAfterSubmitPanierForPaiement' => $reponses['redirectAfterSubmitPanierForPaiement']
        ]);
        $billingAndDeliveryForm->handleRequest($request);

        if($billingAndDeliveryForm->isSubmitted())
        {
            unset($reponses);

            $allValues = $request->request->all($billingAndDeliveryForm->getName());

            $billingAddress = $this->addressRepository->findOneBy(['id' => $allValues['billingAddress']]);
            $shippingMethod = $this->shippingMethodRepository->findOneBy(['id' => $allValues['shipping']]);

            if($shippingMethod->getPrice() == 'PAYANT'){
                
                $deliveryAddress = $this->addressRepository->findOneBy(['id' => $allValues['deliveryAddress'], 'user' => $user]);

            }else{

                $deliveryAddress = $this->collectionPointRepository->findOneBy(['id' => $allValues['deliveryAddress']]);
            }

            if(!$billingAddress or !$deliveryAddress){
                $this->addFlash('warning','Less adresses ne vous appartiennent pas !');

                $this->redirectToRoute('app_home');
            }

            //? on recupere toutes les infos du panier
            $panierParams = $this->panierService->calculateAllCart($user,$allValues['shipping']);

            //? sauvegarde document dans BDD avec articles, boites, etc... en fonction du Type DEVIS ou COMMANDE
            $document = $this->documentService->saveDocumentInDataBase($panierParams,$billingAddress,$deliveryAddress);

            if($panierParams['redirectAfterSubmitPanierForPaiement'] == true){
                //paiement direct donc on redirige vers la page de paiement avec le numero de document
                return $this->redirectToRoute('paiement', ['tokenDocument' => $document->getToken()]);

            }else{
    
                return $this->redirectToRoute('panier_success', ['tokenDocument' => $document->getToken()]);

            }

        }

        return $this->render('site/panier/panier.html.twig', [
            'occasions' => $reponses['panier_occasions'],
            'boites' => $reponses['panier_boites'],
            'items' => $reponses['panier_items'],
            'remises' => $reponses['remises'],
            'weigthPanier' => $reponses['weigthPanier'],
            'totalItems' => $reponses['totauxItems']['price'],
            'totalOccasions' => $reponses['totauxOccasions']['price'],
            'totalBoites' => $reponses['totauxBoites']['price'],
            'totalPanier' => $reponses['totalPanier'],
            'tax' => $reponses['tax'],
            'preparationHt' => $reponses['preparationHt'],
            'deliveryCostWithoutTax' => $reponses['deliveryCostWithoutTax'],
            'shippingForm' => $shippingForm,
            'billingAndDeliveryForm' => $billingAndDeliveryForm,
        ]);
    }

    #[Route('/panier/ajout-occasion/{occasion_id}', name: 'panier_add_occasion')]
    public function addOccasion($occasion_id): Response
    {
        $user = $this->checkUserIsConnected();

        $reponse = $this->panierService->addOccasionInCart($occasion_id,$user);

        $this->addFlash($reponse[0], $reponse[1]);

        return $this->redirectToRoute('app_catalogue_occasions');
    }

    #[Route('/panier/ajout-demande/{boite}', name: 'panier_add_demande')]
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

    #[Route('/panier/delete-item/{item_id}', name: 'panier_delete_item')]
    public function deleteItemInPanier($item_id): Response
    {
        $user = $this->checkUserIsConnected();

        $reponse = $this->panierService->deleteItemInCart($item_id,$user);

        $this->addFlash($reponse[0], $reponse[1]);

        return $this->redirectToRoute('panier');
    }

    #[Route('/panier/ajout-article/', name: 'panier_add_article')]
    public function addArticle(Request $request): Response
    {

        $user = $this->checkUserIsConnected();

        $reponse = $this->panierService->addArticleInCart($request->request->get('select-item'),$request->request->get('qte'),$user);

        $this->addFlash($reponse[0], $reponse[1]);

        $boite = $this->boiteRepository->findOneBy(['id' => $request->request->get('boiteId')]);

        return $this->redirectToRoute('catalogue_pieces_detachees_demande', [
            'editorSlug' => $boite->getEditor()->getSlug(),
            'slug' => $boite->getSlug(),
            'id' => $boite->getId()
        ]);
    }

    #[Route('/panier/success/{tokenDocument}', name: 'panier_success')]
    public function panierSuccess($tokenDocument):Response
    {

        $document = $this->documentRepository->findOneBy(['token' => $tokenDocument]);

        if(!$document){
            $this->addFlash('warning', 'Document inconnu!');
            return $this->redirectToRoute('app_home');
        }

        //TODO
        return $this->render('site/panier/panier_success.html.twig', ['document' => $document]);
    }
}
