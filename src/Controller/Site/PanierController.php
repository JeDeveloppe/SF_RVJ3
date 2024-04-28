<?php

namespace App\Controller\Site;

use App\Form\AcceptCartType;
use App\Service\PanierService;
use App\Service\DocumentService;
use App\Repository\ItemRepository;
use App\Repository\BoiteRepository;
use App\Form\ShippingAndVoucherType;
use App\Repository\PanierRepository;
use App\Repository\AddressRepository;
use App\Repository\DeliveryRepository;
use App\Repository\DocumentRepository;
use App\Form\BillingAndDeliveryAddressType;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\ShippingMethodRepository;
use App\Repository\CollectionPointRepository;
use App\Repository\VoucherDiscountRepository;
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
        private DocumentRepository $documentRepository,
        private VoucherDiscountRepository $voucherDiscountRepository
    )
    {
    }
    
    #[Route('/panier', name: 'panier_start')]
    public function index(Request $request): Response
    {
        $user = $this->security->getUser();
        $user = $this->checkUserIsConnected();

        $paniers = $this->panierRepository->findBy(['user' => $user]);
        if(count($paniers) < 1){

            $this->addFlash('warning', 'Votre panier est vide !');
            
            return $this->redirectToRoute('app_home');
        }

        //toutes les infos du panier sont là
        $reponses = $this->panierService->calculateAllCart($user);

        $occasionInPanier = 0;
        foreach($paniers as $panier){
            if($panier->getOccasion() != NULL){
                $occasionInPanier += 1;
            }
        }

        $shippingAndVoucherForm = $this->createForm(ShippingAndVoucherType::class, null, ['occasionInPanier' => $occasionInPanier]);
        $shippingAndVoucherForm->handleRequest($request);

        //on recupere le code saisie et on le met en session
        $session = $request->getSession();
        $session->set('step_address', false);

        if($shippingAndVoucherForm->isSubmitted() && $shippingAndVoucherForm->isValid())
        {
            
            $voucherDiscountCode = $shippingAndVoucherForm['voucherDiscount']->getData();
            $shippingMethodeId = $shippingAndVoucherForm['shipping']->getData()->getId();

            if(is_null($voucherDiscountCode)){

                $voucherDiscountId = null;

            }else{
                
                //on enlève les espaces au cas ou
                $voucherDiscountCode = str_replace(' ','', $voucherDiscountCode);
                $voucherDiscount = $this->voucherDiscountRepository->findOneVoucherIsActive($voucherDiscountCode);

                if(is_null($voucherDiscount)){

                    $voucherDiscountId = null;
                    $this->addFlash('warning', 'Bon d\'achat inconnu !');

                }else{

                    $voucherDiscountId = $voucherDiscount->getId();
                    $this->addFlash('success', 'Bon d\'achat reconnu !');

                }
            }

            $session->set('voucherDiscountId', $voucherDiscountId);
            $session->set('shippingMethodeId', $shippingMethodeId);
            $session->set('step_address', true);

            //et on recalcul le tout
            $reponses = $this->panierService->calculateAllCart($user);
        }

        return $this->render('site/panier/panier.html.twig', [
            'shippingAndVoucherForm' => $shippingAndVoucherForm,
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

        ]);
    }

    #[Route('/panier/choix-des-adresses', name: 'panier_addresses')]
    public function cartAddresses(Request $request): Response
    {
        $user = $this->security->getUser();
        $user = $this->checkUserIsConnected();

        $paniers = $this->panierRepository->findBy(['user' => $user]);
        if(count($paniers) < 1){

            $this->addFlash('warning', 'Votre panier est vide !');
            
            return $this->redirectToRoute('app_home');
        }

        $voucherDiscoundId = null;
        $session = $request->getSession();
        if(!is_null($session->get('voucherDiscountId'))){
            $voucherDiscoundId = $session->get('voucherDiscountId');
        }

        $shippingMethod = $this->shippingMethodRepository->findOneById($session->get('shippingMethodeId'));

        $billingAndDeliveryForm = $this->createForm(BillingAndDeliveryAddressType::class, null, [
            'user' => $this->security->getUser(),
            'shippingMethod' => $shippingMethod,
        ]);
        $billingAndDeliveryForm->handleRequest($request);

            if($billingAndDeliveryForm->isSubmitted() && $billingAndDeliveryForm->isValid()){
                
                $billingAddress = $billingAndDeliveryForm['billingAddress']->getData();
                $deliveryAddress = $billingAndDeliveryForm['deliveryAddress']->getData();

                //on recupere le code saisie et on le met en session
                $session = $request->getSession();
                
                $session->set('billingAddressId', $billingAddress->getId());
                $session->set('deliveryAddressId', $deliveryAddress->getId());

                //on redirige var la page suivante
                return $this->redirectToRoute('panier_before_paiement');

            }

        return $this->render('site/panier/panier_addresses.html.twig', [
            'user' => $user,
            'billingAndDeliveryForm' => $billingAndDeliveryForm,
            'voucherDiscountId' => $voucherDiscoundId,
            'shippingMethod' => $shippingMethod
        ]);
    }

    #[Route('/panier/verification-avant-paiement', name: 'panier_before_paiement')]
    public function cartEnd(Request $request): Response
    {
        $user = $this->security->getUser();
        $user = $this->checkUserIsConnected();

        $paniers = $this->panierRepository->findBy(['user' => $user]);
        if(count($paniers) < 1){

            $this->addFlash('warning', 'Votre panier est vide !');
            
            return $this->redirectToRoute('app_home');
        }

        //on recupere la session
        $session = $request->getSession();
        $billingAddressId = $session->get('billingAddressId');
        $deliveryAddressId = $session->get('deliveryAddressId');
        $shippingMethodId = $session->get('shippingMethodeId');

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
        $reponses = $this->panierService->calculateAllCart($user);

        if($acceptCartForm->isSubmitted() && $acceptCartForm->isValid())
        {
            //on vérifie si on a bien toutes les variables pour enregistrer le document
            $this->panierService->checkSessionForSaveInDatabase($session);

            //? on recupere toutes les infos du panier
            $panierParams = $reponses;

            //? sauvegarde document dans BDD avec articles, boites, etc... en fonction du Type DEVIS ou COMMANDE
            $document = $this->documentService->saveDocumentLogicInDataBase($panierParams,$session);

            if($panierParams['redirectAfterSubmitPanierForPaiement'] == true){
                //paiement direct donc on redirige vers la page de paiement avec le numero de document
                return $this->redirectToRoute('paiement', ['tokenDocument' => $document->getToken()]);

            }else{
    
                return $this->redirectToRoute('panier_success', ['tokenDocument' => $document->getToken()]);

            }

        }

        return $this->render('site/panier/panier_before_paiement.html.twig', [
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
            'acceptCartForm' => $acceptCartForm,
            'billingAddress' => $billingAddress,
            'deliveryAddress' => $deliveryAddress

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

        return $this->redirectToRoute('panier_start');
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
}
