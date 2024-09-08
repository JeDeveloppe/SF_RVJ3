<?php

namespace App\Controller\Site;

use App\Form\VoucherType;
use App\Form\ShippingType;
use App\Form\AcceptCartType;
use App\Service\PanierService;
use App\Service\DocumentService;
use App\Repository\TaxRepository;
use App\Repository\ItemRepository;
use App\Repository\BoiteRepository;
use App\Repository\PanierRepository;
use App\Repository\AddressRepository;
use App\Repository\DeliveryRepository;
use App\Repository\DocumentRepository;
use App\Repository\OccasionRepository;
use App\Form\BillingAndDeliveryAddressType;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\ShippingMethodRepository;
use App\Repository\CollectionPointRepository;
use App\Repository\VoucherDiscountRepository;
use App\Service\UtilitiesService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        private DocumentRepository $documentRepository,
        private VoucherDiscountRepository $voucherDiscountRepository,
        private OccasionRepository $occasionRepository,
        private TaxRepository $taxRepository,
        private UtilitiesService $utilitiesService
    )
    {
    }
    
    #[Route('/panier', name: 'panier_start')]
    public function index(Request $request): Response
    {
    
        //?on demarre la session
        $session = $request->getSession();

        //?on compte le nombre de products dans le panier en session
        $count = $this->utilitiesService->countNumberOfProductsInSessionCart($session->get('paniers'));
    
        //?retour en arriere si panier vide
        if($count < 1){

            $this->addFlash('warning', 'Votre panier est vide !');
            
            return $this->redirectToRoute('app_home');

        }

        //?on genere un mega tableau d'entités Panier()
        $paniers = $this->panierService->returnArrayPaniersEntitiesFromPanierSession($session->get('paniers'));

        //?toutes les infos du panier sont là
        $allCartValues = $this->panierService->calculateAllCart($paniers);

        //?si y a au moins un occasion pas de possibilite de livraison donc methode == retrait obligatoire
        if(count($allCartValues['panier_occasions']) > 0){
            $shippingMethodRetraitInCaen = $this->shippingMethodRepository->findOneByName($_ENV['SHIPPING_METHOD_BY_IN_RVJ_DEPOT_NAME']);
            $session->set('shippingMethodeId', $shippingMethodRetraitInCaen->getId());
        }
        $shippingForm = $this->createForm(ShippingType::class, null, ['occasionInPanier' => $allCartValues['panier_occasions']]);
        $shippingForm->handleRequest($request);

        //form pour les codes de reduction
        $voucherDiscountId = null; // on initialise à null
        $voucherType = $this->createForm(VoucherType::class);
        $voucherType->handleRequest($request);
        $session->set('voucherDiscountId', $voucherDiscountId);

        if($voucherType->isSubmitted() && $voucherType->isValid())
        {
            
            $voucherDiscountCode = $voucherType['voucherDiscount']->getData();
            // $shippingMethodeId = $shippingAndVoucherForm['shipping']->getData()->getId();

            if(is_null($voucherDiscountCode)){

                $voucherDiscountId = null;

            }else{
                
                //on enlève les espaces au cas ou
                $voucherDiscountCode = str_replace(' ','', $voucherDiscountCode);
                $voucherDiscount = $this->voucherDiscountRepository->findOneVoucherIsActive($voucherDiscountCode);

                if(is_null($voucherDiscount)){

                    $this->addFlash('warning', 'Bon d\'achat inconnu !');

                }else{

                    $voucherDiscountId = $voucherDiscount->getId();
                    $this->addFlash('success', 'Bon d\'achat reconnu !');

                }

            }

            $session->set('voucherDiscountId', $voucherDiscountId);

            //et on recalcul le tout
            $allCartValues = $this->panierService->calculateAllCart($paniers);

        }

        return $this->render('site/pages/panier/panier.html.twig', [
            'voucherDiscountForm' => $voucherType,
            'shippingForm' => $shippingForm,
            'allCartValues' => $allCartValues,
            'shippingMethodRetraitInCaenId' => $shippingMethodRetraitInCaen->getId()
        ]);
    }

    #[Route('/panier/choix-des-adresses', name: 'panier_addresses')]
    public function cartAddresses(Request $request): Response
    {

        //?on demarre la session
        $session = $request->getSession();

        //?on compte le nombre de products dans le panier en session
        $count = $this->utilitiesService->countNumberOfProductsInSessionCart($session->get('paniers'));
    
        //?retour en arriere si panier vide
        if($count < 1){

            $this->addFlash('warning', 'Votre panier est vide !');
            
            return $this->redirectToRoute('app_home');

        }
        
        //?on genere un mega tableau d'entités Panier()
        $paniers = $this->panierService->returnArrayPaniersEntitiesFromPanierSession($session->get('paniers'));

        $voucherDiscoundId = null;
        
        $shippingMethodId = $request->cookies->get('shippingMethodId');

        if(is_null($shippingMethodId)){

            $this->addFlash('warning', 'ShippingMethodId-cookie absent');
            
            return $this->redirectToRoute('panier_start');

        }else{

            //on recupere la session
            $session = $request->getSession();
            //on met en session l'id de la methode d'envoi choisi
            $session->set('shippingMethodId', $shippingMethodId);
        }
        //on recupere les infos du panier
        $allCartValues = $this->panierService->calculateAllCart($paniers);

        // $shippingMethod = $this->shippingMethodRepository->findOneById($shippingMethodId);
        $shippingMethod = $allCartValues['shippingMethod'];

        $billingAndDeliveryForm = $this->createForm(BillingAndDeliveryAddressType::class, null, [
            'user' => $this->security->getUser(),
            'shippingMethod' => $shippingMethod,
        ]);
        $billingAndDeliveryForm->handleRequest($request);

            if($billingAndDeliveryForm->isSubmitted() && $billingAndDeliveryForm->isValid()){
                
                $billingAddress = $billingAndDeliveryForm['billingAddress']->getData();
                $deliveryAddress = $billingAndDeliveryForm['deliveryAddress']->getData();

                //on met en session les address choisies
                $session->set('billingAddressId', $billingAddress->getId());
                $session->set('deliveryAddressId', $deliveryAddress->getId());

                //on redirige var la page suivante
                return $this->redirectToRoute('panier_before_paiement');

            }

        return $this->render('site/pages/panier/panier_addresses.html.twig', [
            'billingAndDeliveryForm' => $billingAndDeliveryForm,
            'voucherDiscountId' => $voucherDiscoundId,
            'allCartValues' => $allCartValues,
            'shippingMethod' => $shippingMethod,
        ]);
    }

    #[Route('/panier/verification-avant-paiement', name: 'panier_before_paiement')]
    public function cartEnd(Request $request): Response
    {

        //?on demarre la session
        $session = $request->getSession();

        //?on compte le nombre de products dans le panier en session
        $count = $this->utilitiesService->countNumberOfProductsInSessionCart($session->get('paniers'));
    
        //?retour en arriere si panier vide
        if($count < 1){

            $this->addFlash('warning', 'Votre panier est vide !');
            
            return $this->redirectToRoute('app_home');

        }
        
        //?on genere un mega tableau d'entités Panier()
        $paniers = $this->panierService->returnArrayPaniersEntitiesFromPanierSession($session->get('paniers'));

        $billingAddressId = $session->get('billingAddressId');
        $deliveryAddressId = $session->get('deliveryAddressId');
        $shippingMethodId = $session->get('shippingMethodId');

        $shippingMethod = $this->shippingMethodRepository->findOneById($shippingMethodId);

        if($shippingMethod->getPrice() == 'GRATUIT'){

            $deliveryAddress = $this->collectionPointRepository->findOneById($deliveryAddressId);

        }else{

            $deliveryAddress = $this->addressRepository->findOneById($deliveryAddressId);
        }

        $billingAddress = $this->addressRepository->findOneById($billingAddressId);

        $acceptCartForm = $this->createForm(AcceptCartType::class);
        $acceptCartForm->handleRequest($request);

        //toutes les infos du panier sont là
        $allCartValues = $this->panierService->calculateAllCart($paniers);

        if($acceptCartForm->isSubmitted() && $acceptCartForm->isValid())
        {
            //on vérifie si on a bien toutes les variables pour enregistrer le document
            $this->panierService->checkSessionForSaveInDatabase($session);

            //? sauvegarde document dans BDD avec articles, boites, etc... en fonction du Type DEVIS ou COMMANDE
            $document = $this->documentService->saveDocumentLogicInDataBase($allCartValues,$session,$request);

            if($allCartValues['redirectAfterSubmitPanierForPaiement'] == true){
                //paiement direct donc on redirige vers la page de paiement avec le numero de document
                return $this->redirectToRoute('paiement', ['tokenDocument' => $document->getToken()]);

            }else{
    
                return $this->redirectToRoute('panier_success', ['tokenDocument' => $document->getToken()]);

            }

        }

        return $this->render('site/pages/panier/panier_before_paiement.html.twig', [
            'acceptCartForm' => $acceptCartForm,
            'billingAddress' => $billingAddress,
            'deliveryAddress' => $deliveryAddress,
            'allCartValues' => $allCartValues
        ]);
    }

    #[Route('/panier/ajout-occasion/{occasion_id}/{qte}/', name: 'panier_add_occasion')]
    public function addOccasion(Request $request, $occasion_id, $qte): Response
    {

        $reponse = $this->panierService->addOccasionInCart($occasion_id, $qte);

        $this->addFlash($reponse[0], $reponse[1]);

        //TODO redirect -2 René?
        return $this->redirect($request->headers->get('referer'));

        // return $this->redirectToRoute('app_catalogue_occasions');
    }

    #[Route('/panier/ajout-demande/{boite}', name: 'panier_add_demande')]
    public function addDemande(): Response
    {
        return $this->redirectToRoute('app_catalogue_boites');
    }

    public function checkUserIsConnected()
    {
        $user = $this->security->getUser();

        if(!$user){

            $this->addFlash('warning','Vous n\'êtes pas identifié.e');

            $this->redirectToRoute('app_home');
        }

        return $user;
    }

    #[Route('/panier/delete-cart-line/{category}/{cart_id}', name: 'delete_cart_line_when_logged')]
    public function deleteCartLineWhenLogged($category, $cart_id, Request $request): Response
    {
        $reponse = $this->panierService->deleteCartLine($category, $cart_id);

        $this->addFlash($reponse[0], $reponse[1]);

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/panier/delete-cart-line/{category}/{cart_id}', name: 'delete_cart_line_when_not_logged')]
    public function deleteCartLineWhenNotLogged($category, $cart_id, Request $request): Response
    {
        $reponse = $this->panierService->deleteCartLine($category, $cart_id);

        $this->addFlash($reponse[0], $reponse[1]);

        return $this->redirect($request->headers->get('referer'));
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

        return $this->render('site/panier/panier_success.html.twig', ['document' => $document]);
    }

    #[Route('/panier/calcul-delivery-cost/', name: 'panier_calcul_delivery_cost')]
    public function panierShippingMethod(Request $request):JsonResponse
    {
        $gets = $request->getContent();
        $datas = json_decode($gets, true);

        $shippingMethod = $this->shippingMethodRepository->findOneById($datas['shippingMethodId']);

        $result = $this->panierService->returnDeliveryCost($shippingMethod, $datas['weight']);

        return new JsonResponse(
            ['deliveryCost' => $result],
            $status = 200,
            $headers = []
        );
    }
}
