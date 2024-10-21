<?php

namespace App\Service;

use App\Entity\Occasion;
use App\Entity\User;
use App\Entity\Panier;
use DateTimeImmutable;
use App\Entity\ShippingMethod;
use App\Repository\TaxRepository;
use App\Repository\ItemRepository;
use App\Repository\PanierRepository;
use App\Repository\DeliveryRepository;
use App\Repository\DiscountRepository;
use App\Repository\OccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SiteSettingRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\ShippingMethodRepository;
use App\Repository\VoucherDiscountRepository;
use App\Repository\DocumentParametreRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class PanierService
{
    public function __construct(
        private EntityManagerInterface $em,
        private SiteSettingRepository $siteSettingRepository,
        private DiscountRepository $discountRepository,
        private OccasionRepository $occasionRepository,
        private ItemRepository $itemRepository,
        private ShippingMethodRepository $shippingMethodRepository,
        private PanierRepository $panierRepository,
        private DeliveryRepository $deliveryRepository,
        private UtilitiesService $utilitiesService,
        private TaxRepository $taxRepository,
        private DocumentParametreRepository $documentParametreRepository,
        private Security $security,
        private RequestStack $request,
        private UserRepository $userRepository,
        private VoucherDiscountRepository $voucherDiscountRepository
        ){
    }

    public function addOccasionInCart(int $occasion_id, int $qte)
    {

        $occasion = $this->occasionRepository->findOneBy(['id' => $occasion_id, 'isOnline' => true]);

        if(!$occasion){

            $reponse = ['warning', 'Occasion inconnu ou déjà réservé !'];

        }else{

            $session = $this->request->getSession();

            $paniers = $session->get('paniers', []);
            $paniers['occasions'][$occasion_id] = $qte;

            $session->set('paniers', $paniers);

            $reponse = ['success', 'Occasion ajouté au panier'];
        }

        return $reponse;
    }

    public function deleteCartLine(string $category, int $cart_id)
    {
        $user = $this->security->getUser();
        $session = $this->request->getSession();
        $paniersInSession = $session->get('paniers');
        $panier = null;

        $panier = $paniersInSession[$category];

        if(is_null($panier)){

            $reponse = ['warning', 'Ligne de panier inconnue !'];

        }else{

            //? ON MET A JOUR LA SESSION
            unset($paniersInSession[$category][$cart_id]);
            //on remet en memoire
            $session->set('paniers', $paniersInSession);

            $reponse = ['success', 'Ligne supprimée du panier'];
        }

        return $reponse;
    }

    public function addArticleInCart($article_id,$qte,$user)
    {

        $item = $this->itemRepository->findOneBy(['id' => $article_id]);

        if(!$item OR $item->getStockForSale() - $qte < 0){

            $reponse = ['warning', 'Stock non disponible pour cette quantité: '.$qte];

        }else{

            $panier = $this->panierRepository->findOneBy(['item' => $item, 'user' => $this->security->getUser()]);

            $newQteInStock = $item->getStockForSale() - $qte;

            if($panier){

                $newQteInPanier = $panier->getQte() + $qte;

                $panier
                ->setQte($newQteInPanier)
                ->setUnitPriceExclusingTax($item->getPriceExcludingTax())
                ->setCreatedAt( new DateTimeImmutable('now'))
                ->setPriceWithoutTax($item->getPriceExcludingTax() * $newQteInPanier);

                $this->em->persist($panier);

            }else{

                //?on crée la ligne du panier
                $panier = new Panier();
                $panier->setItem($item)
                    ->setUnitPriceExclusingTax($item->getPriceExcludingTax())
                    ->setQte($qte)
                    ->setCreatedAt( new DateTimeImmutable('now'))
                    ->setUser($user)
                    ->setPriceWithoutTax($item->getPriceExcludingTax() * $qte);
                $this->em->persist($panier);
            }


            //?on met la qte article à jour
            $item->setStockForSale($newQteInStock);
            $this->em->persist($item);

            //?on sauvegarde le tout
            $this->em->flush();

            $reponse = ['success', 'Article ajouté au panier'];
        }

        return $reponse;
    }

    public function calculateAllCart($paniers)
    {
        $session = $this->request->getSession();
        $user = $this->security->getUser();
        $shippingMethod = $this->shippingMethodRepository->findOneById($session->get('shippingMethodId'));
        $docParams = $this->documentParametreRepository->findOneBy(['isOnline' => true]);
        $now = new DateTimeImmutable('now');

        $responses = $this->separateBoitesItemsAndOccasion($paniers);
        $responses['preparationHt'] = $docParams->getPreparation();
        $responses['memberShipOnTime'] = false;

        if($user){
            // $responses['panier_occasions'] = $this->panierRepository->findOccasionsByUser($user);
            // $responses['panier_boites'] = $this->panierRepository->findBoitesByUser($user);
            // $responses['panier_items'] = $this->panierRepository->findItemsByUser($user);

            //gestion membership au niveau du panier
            if($user->getMembership() > $now){
                $responses['preparationHt'] = 0;
                $responses['memberShipOnTime'] = true;
            }

        }else{

            $responses['remises']['volume'] = $this->calculateRemise($paniers);

        }

        //? et frais de préparation si pas d'articles
        if( count($responses['panier_items']) < 1){
            $responses['preparationHt'] = 0;
        }

        $responses['tax'] = $this->taxRepository->findOneBy([]);
        $responses['remises']['voucher']['voucherMax'] = 0;
        $responses['remises']['voucher']['actif'] = false;

        if(!is_null($session->get('voucherDiscountId'))){
            $voucherDiscount = $this->voucherDiscountRepository->find($session->get('voucherDiscountId'));
            $responses['remises']['voucher']['voucherMax'] = $voucherDiscount->getRemainingValueToUseExcludingTax();
            $responses['remises']['voucher']['token'] = $voucherDiscount->getToken();
            $responses['remises']['voucher']['actif'] = true;
        }



        

        //?action sur le bouton payer / demande de devis du panier
        if($responses['panier_boites'] > 0){
            //? after doc is save in bdd, redirect to paiement
            $responses['redirectAfterSubmitPanierForPaiement'] = true; 
        }else{
            $responses['redirectAfterSubmitPanierForPaiement'] = false;
        }

        $responses['totauxItems'] = $this->utilitiesService->totauxByPanierGroup($responses['panier_items']);
        $responses['totauxOccasions'] = $this->utilitiesService->totauxByPanierGroup($responses['panier_occasions']);
        $responses['totauxBoites'] = $this->utilitiesService->totauxByPanierGroup($responses['panier_boites']);

        $responses['weigthPanier'] = $responses['totauxBoites']['weigth'] + $responses['totauxOccasions']['weigth'] + $responses['totauxItems']['weigth'];
        $weigthPanier = $responses['weigthPanier'];

        //? calcul de la remise sur les articles
        $responses['remises']['volume'] = $this->calculateRemise($this->panierRepository->findBy(['user' => $user]));

        $responses['shippingMethod'] = $shippingMethod;

        if(is_null($shippingMethod)){

            $responses['deliveryCostWithoutTax'] = 0;

        }else{

            $responses['deliveryCostWithoutTax'] = $this->returnDeliveryCost($shippingMethod, $weigthPanier);

        }

        $responses['remises']['volume']['remiseDeQte'] = round($responses['totauxItems']['price'] * $responses['remises']['volume']['value'] / 100);
        
        $sousTotalItemHTAfterRemiseVolume = $responses['totauxItems']['price'] - $responses['remises']['volume']['remiseDeQte'];
        $totalHTItemsAndBoite = round(($responses['totauxItems']['price'] + $responses['totauxBoites']['price'] + $responses['totauxOccasions']['price']) - $responses['remises']['volume']['remiseDeQte']);

        //? calcule de la remise sur les articles
        $diff = $totalHTItemsAndBoite - $responses['remises']['voucher']['voucherMax'];

        if($diff >= 0){
            $responses['remises']['voucher']['used'] = $totalHTItemsAndBoite - $diff;
            $responses['remises']['voucher']['voucherRemaining'] = 0; // reste à utilisé du bon
        }else{
            $responses['remises']['voucher']['used'] = $totalHTItemsAndBoite;
            $responses['remises']['voucher']['voucherRemaining'] = $diff * -1; // reste à utilisé du bon
        }



        $responses['totalPanierHt'] = ($responses['preparationHt'] + $responses['totauxItems']['price'] + $responses['totauxBoites']['price'] + $responses['totauxOccasions']['price'] + $responses['deliveryCostWithoutTax']) - $responses['remises']['volume']['remiseDeQte'] - $responses['remises']['voucher']['used'];

        return $responses;
    }

    public function calculateRemise($paniers)
    {

        $qte = 0;
        foreach($paniers as $panier){
            //?remise que sur les articles
            if($panier->getItem() != NULL){
                $qte += $panier->getQte();
            }
        }

        $discount = $this->discountRepository->findDiscountPercentage($qte);
    
        if($discount)
        {
            $remises['actif'] = true;
            $remises['qte'] = $qte;
            $remises['value'] = $discount->getValue();
            $remises['nextQteForRemiseSupplementaire'] = $discount->getEnd() + 1;
            $nextRemise = $this->discountRepository->findDiscountPercentage($remises['nextQteForRemiseSupplementaire']);

            if($nextRemise)
            {
                $remises['nextRemiseSupplementaire'] = $nextRemise->getValue();

            }else{

                $remises['nextRemiseSupplementaire'] = false;
            }

        }else{

            $remises['actif'] = false;
            $remises['qte'] = 0;
            $remises['value'] = 0;
            $remises['nextQteForRemiseSupplementaire'] = 0;
            $remises['nextRemiseSupplementaire'] = 0;
        }
        

        return $remises;
    }

    public function checkSessionForSaveInDatabase($sessionObjet)
    {

        $sessionArray = json_decode(json_encode($sessionObjet->all()), true);
        $validation = false;
        $validationKO = [];

        $stringVariablesToCheckIfThereExists = ['voucherDiscountId','billingAddressId','deliveryAddressId','shippingMethodId'];

        foreach($stringVariablesToCheckIfThereExists as $stringVariablesToCheckIfThereExist){
            if(array_key_exists($stringVariablesToCheckIfThereExist, $sessionArray)){
                $validation = true;
            }else{
                $validationKO[] = $stringVariablesToCheckIfThereExist;
            }
        }

        if(count($validationKO) > 0){
            dd('Variables manquantes dans la session pour valider le panier: '. var_dump($validationKO));
        }
    }

    public function checkIfAllCartObjectsAreOnLine(array $carts)
    {
        $allOnLine = 0;
        foreach($carts as $cart){
            if(!is_null($cart->getOccasion()) && $cart->getOccasion()->getIsOnline() == false){
                $allOnLine += 1;
            }
            if(!is_null($cart->getItem()) && $cart->getItem()->getStockForSale() > 0){
                $allOnLine += 1;
            }
        }

        return $allOnLine;
    }

    public function returnDeliveryCost(ShippingMethod $shipping, int $weigthPanier)
    {

        $delivery = $this->deliveryRepository->findCostByDeliveryShippingMethod($shipping, $weigthPanier);
        $result = $delivery->getPriceExcludingTax();

        return $result;
    }

    public function separateBoitesItemsAndOccasion(array $paniers)
    {

        //responses['panier_boites'], $responses['panier_items'], $responses['panier_occasions']

        $responses['panier_occasions'] = [];
        $responses['panier_items'] = [];
        $responses['panier_boites'] = [];

        foreach($paniers as $panier){
            if(!is_null($panier->getOccasion())){
                $responses['panier_occasions'][] = $panier;
            }

            if(!is_null($panier->getItem())){
                $responses['panier_items'][] = $panier;
            }

            if(!is_null($panier->getBoite())){
                $responses['panier_boites'][] = $panier;
            }

        }

        return $responses;
    }

    public function returnArrayPaniersEntitiesFromPanierSession(array $paniersInSession)
    {

        foreach($paniersInSession as $categorie =>  $products){

            if($categorie == 'occasions'){

                foreach($products as $occasion_id => $qte){
                    $occasion = $this->occasionRepository->findOneBy(['id' => $occasion_id]);

                    //?en fonction du prix s'il est remisé ou pas
                    if($occasion->getDiscountedPriceWithoutTax() > 0){

                        $price = $occasion->getDiscountedPriceWithoutTax();

                    }else{

                        $price = $occasion->getPriceWithoutTax();
                    }

                    //?on crée la ligne du panier
                    $panier = new Panier();
                    $panier->setOccasion($occasion)
                        ->setCreatedAt( new DateTimeImmutable('now'))
                        ->setPriceWithoutTax($price)
                        ->setQte($qte);
                    // $this->em->persist($panier);

                    $paniers[] = $panier;
                }
            }
            if($categorie == 'items'){
                //TODO items
            }
        }

        return $paniers;
    }
}
