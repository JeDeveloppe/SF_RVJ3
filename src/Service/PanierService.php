<?php

namespace App\Service;

use DateInterval;
use App\Entity\User;
use App\Entity\Panier;
use DateTimeImmutable;
use App\Entity\Occasion;
use App\Entity\ShippingMethod;
use App\Repository\TaxRepository;
use App\Repository\ItemRepository;
use App\Repository\UserRepository;
use App\Entity\Returndetailstostock;
use App\Repository\PanierRepository;
use App\Repository\DeliveryRepository;
use App\Repository\DiscountRepository;
use App\Repository\OccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SiteSettingRepository;
use Symfony\Component\BrowserKit\Request;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\ShippingMethodRepository;
use App\Repository\VoucherDiscountRepository;
use App\Repository\DocumentParametreRepository;
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
        private VoucherDiscountRepository $voucherDiscountRepository,
        ){
    }

    public function addOccasionInCartRealtime(int $occasion_id, int $qte)
    {

        //?on supprimer les paniers de plus de x heures
        $this->deletePanierFromDataBaseAndPuttingItemsBoiteOccasionBackInStock();

        $occasion = $this->occasionRepository->findOneBy(['id' => $occasion_id, 'isOnline' => true]);

        if(!$occasion){

            $reponse = ['warning', 'Occasion inconnu ou déjà réservé !'];

        }else{

            $now = new DateTimeImmutable('now');
            $endPanier = $now->add(new DateInterval('PT'.$_ENV['DELAY_TO_DELETE_CART_IN_HOURS'].'H'));

            $panier = new Panier();
            $panier->setOccasion($occasion);
            $panier->setQte($qte);
            $panier->setCreatedAt($endPanier);
            $panier->setPriceWithoutTax($occasion->getPriceWithoutTax() * $qte);
            $panier->setUnitPriceExclusingTax($occasion->getPriceWithoutTax());
            $panier->setTokenSession($this->request->getSession()->get('tokenSession'));
            $panier->setUser($this->security->getUser());
            $this->em->persist($panier);

            $occasion->setIsOnline(false);
            $this->em->persist($occasion);

            $this->em->flush();

            $reponse = ['success', 'Jeu ajouté au panier'];
        }

        return $reponse;
    }

    public function deleteCartLineRealtime(int $cart_id)
    {

        $session = $this->request->getSession();
        $tokenSession = $session->get('tokenSession');
        $user = $this->security->getUser();

        $panierA[] = $this->panierRepository->findOneBy(['tokenSession' => $tokenSession, 'id' => $cart_id]) ?? [];
        $panierB[] = $this->panierRepository->findOneBy(['user' => $user, 'id' => $cart_id]) ?? [];
        $panierCombined = array_unique(array_merge($panierA,$panierB), SORT_REGULAR);

        if(!$panierCombined){

            $reponse = ['warning', 'Ligne de panier inconnue !'];

        }else{
            $panier = $panierCombined[0];
            if(!is_null($panier->getItem())){

                $item = $panier->getItem();
                $item->setStockForSale($item->getStockForSale() + $panier->getQte());

            }
            if(!is_null($panier->getOccasion())){

                $panier->getOccasion()->setIsOnline(true);
            }

            $this->em->remove($panier);

            $this->em->flush();

            $reponse = ['success', 'Ligne supprimée du panier'];
        }

        return $reponse;
    }

    public function addArticleInCartRealtime($article_id,$qte)
    {
        $tokenSession = $this->request->getSession()->get('tokenSession');
        $item = $this->itemRepository->findOneBy(['id' => $article_id]);
        $user = $this->security->getUser();

        //?on supprimer les paniers de plus de x heures
        $this->deletePanierFromDataBaseAndPuttingItemsBoiteOccasionBackInStock();

        //?si pas d'article connu
        if(!$item){

            $reponse = ['warning', 'Article inconnu !'];

        }else{

            $stockDispo = $item->getStockForSale();

            if($stockDispo >= $qte){
                
                $panier = $this->panierRepository->findOneBy(['tokenSession' => $tokenSession, 'item' => $item]);
    
                $now = new DateTimeImmutable('now');
                $endPanier = $now->add(new DateInterval('PT'.$_ENV['DELAY_TO_DELETE_CART_IN_HOURS'].'H'));

                if($panier){

                    $panier->setQte($panier->getQte() + $qte)->setCreatedAt($endPanier)->setUser($user);
                    $this->em->persist($panier);

                }else{

                    $panier = new Panier();
                    $panier->setItem($item);
                    $panier->setQte($qte);
                    $panier->setPriceWithoutTax($item->getPriceExcludingTax() * $qte);
                    $panier->setUnitPriceExclusingTax($item->getPriceExcludingTax());
                    $panier->setCreatedAt($endPanier); //on rajoute x heure pour suppression
                    $panier->setTokenSession($tokenSession);
                    $panier->setUser($user);
                    $this->em->persist($panier);
                }
                
                $item->setStockForSale($item->getStockForSale() - $qte);
                $this->em->persist($item);
                
                $this->em->flush();
                $reponse = ['success', 'Article(s) ajouté au panier'];

            }else{

                $reponse = ['warning', 'Quantité insuffisante !'];

            }
        }

        return $reponse;
    }

    public function calculateAllCartRealtime()
    {
        $session = $this->request->getSession();
        $user = $this->security->getUser();
        $shippingMethod = $this->shippingMethodRepository->findOneById($session->get('shippingMethodId'));
        $docParams = $this->documentParametreRepository->findOneBy(['isOnline' => true]);
        $now = new DateTimeImmutable('now');
        $paniers = $this->returnAllPaniersFromUser();
        
        $responses = $this->separateBoitesItemsAndOccasion($paniers);
        $responses['preparationHt'] = $docParams->getPreparation();
        $responses['memberShipOnTime'] = false;

        if($user){

            //gestion membership au niveau du panier
            if($user->getMembership() > $now){
                $responses['preparationHt'] = 0;
                $responses['memberShipOnTime'] = true;
            }

        }else{

            $responses['remises']['volume'] = $this->calculateRemise($paniers);

        }


        //? et frais de préparation si pas d'articles
        if(count($responses['panier_items']) < 1){
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

    public function returnDeliveryCost(ShippingMethod $shipping, int $weigthPanier)
    {

        $delivery = $this->deliveryRepository->findCostByDeliveryShippingMethod($shipping, $weigthPanier);
        $result = $delivery->getPriceExcludingTax();

        return $result;
    }

    public function separateBoitesItemsAndOccasion(array $paniers): array
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

                foreach($products as $item_id => $qte){
                    $item = $this->itemRepository->findOneBy(['id' => $item_id]);

                    $price = $item->getPriceExcludingTax();

                    //?on crée la ligne du panier
                    $panier = new Panier();
                    $panier->setItem($item)
                        ->setCreatedAt( new DateTimeImmutable('now'))
                        ->setPriceWithoutTax($price)
                        ->setQte($qte);
                    // $this->em->persist($panier);

                    $paniers[] = $panier;
                }
            }
        }

        return $paniers;
    }

    public function returnAllPaniersFromUser()
    {

        $session = $this->request->getSession();
        $tokenSession = $session->get('tokenSession');
        $user = $this->security->getUser();

        $paniersA = $this->panierRepository->findBy(['tokenSession' => $tokenSession]) ?? [];
        $paniersB = $this->panierRepository->findBy(['user' => $user]) ?? [];
        ///?merge and delete doublons
        $paniers = array_unique(array_merge($paniersA,$paniersB), SORT_REGULAR);

        return $paniers;
    }

    public function deletePanierFromDataBaseAndPuttingItemsBoiteOccasionBackInStock()
    {
        $now = new DateTimeImmutable('now');
        $paniersToDelete = $this->panierRepository->findPaniersToDeleteWhenEndOfValidationIsToOld($now);

        foreach($paniersToDelete as $panier)
        {

            if(!is_null($panier->getItem())){
                $itemInDatabase = $this->itemRepository->find($panier->getItem());
                $itemInDatabase->setStockForSale($itemInDatabase->getStockForSale() + $panier->getQuantity());
                $this->em->persist($itemInDatabase);
                $this->em->remove($panier);
            }

            if(!is_null($panier->getOccasion())){
                $occasionInDatabase = $this->occasionRepository->find($panier->getOccasion());
                $occasionInDatabase->setIsOnline(true);
                $this->em->persist($occasionInDatabase);
                $this->em->remove($panier);
            }

            $this->em->remove($panier);
        }
        
        $this->em->flush();
    }
}
