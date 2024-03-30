<?php

namespace App\Service;

use App\Entity\Address;
use App\Entity\CollectionPoint;
use App\Entity\Panier;
use DateTimeImmutable;
use App\Entity\Delivery;
use App\Repository\ItemRepository;
use App\Repository\PanierRepository;
use App\Repository\DeliveryRepository;
use App\Repository\DiscountRepository;
use App\Repository\DocumentParametreRepository;
use App\Repository\OccasionRepository;
use App\Repository\ShippingMethodRepository;
use App\Repository\SiteSettingRepository;
use App\Repository\TaxRepository;
use App\Repository\VoucherDiscountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

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
        private VoucherDiscountRepository $voucherDiscountRepository
        ){
    }

    public function addOccasionInCart($occasion_id,$user)
    {

        $occasion = $this->occasionRepository->findOneBy(['id' => $occasion_id, 'isOnline' => true]);

        if(!$occasion){

            $reponse = ['warning', 'Occasion inconnu ou déjà réservé dans un panier !'];

        }else{

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
                ->setUser($user)
                ->setPriceWithoutTax($price);
            $this->em->persist($panier);

            //?on met l'occasion hors ligne
            $occasion->setIsOnline(false);
            $this->em->persist($occasion);

            //?on sauvegarde le tout
            $this->em->flush();

            $reponse = ['success', 'Occasion ajouté au panier'];
        }

        return $reponse;
    }

    public function deleteItemInCart($item_id,$user)
    {

        $panier = $this->panierRepository->findOneBy(['id' => $item_id, 'user' => $user]);

        if(!$panier){

            $reponse = ['warning', 'Ligne de panier inconnue !'];

        }else{

            if(!empty($panier->getOccasion())){
                //?on récupère l'occasion
                $occasion = $panier->getOccasion();
                //?on remet en ligne l'occasion
                $occasion->setIsOnline(true);
                $this->em->persist($occasion);
            }else if(!empty($panier->getItem())){
                //?on récupère l'article
                $item = $panier->getItem();
                //?on remet les articles en ligne
                $item->setStockForSale($item->getStockForSale() + $panier->getQte());
                $this->em->persist($item);
            }

            //? on supprime la ligne du panier
            $this->em->remove($panier);

            //?on sauvegarde le tout
            $this->em->flush();

            $reponse = ['success', 'Ligne supprimée du panier'];
        }

        return $reponse;
    }

    public function addArticleInCart($article_id,$qte,$user)
    {

        $item = $this->itemRepository->findOneBy(['id' => $article_id]);

        if($item->getStockForSale() - $qte < 0){

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

    public function calculateAllCart($user)
    {
        $session = $this->request->getSession();
        $shipping = $session->get('shippingMethodeId');
        $docParams = $this->documentParametreRepository->findOneBy(['isOnline' => true]);
        $responses = [];
        //? calcul de la remise sur les articles
        $responses['remises']['volume'] = $this->calculateRemise($this->panierRepository->findBy(['user' => $user]));
        
        $responses['shipping'] = $shipping;
        $responses['panier_occasions'] = $this->panierRepository->findOccasionsByUser($user);
        $responses['panier_boites'] = $this->panierRepository->findBoitesByUser($user);
        $responses['panier_items'] = $this->panierRepository->findItemsByUser($user);
        $responses['tax'] = $this->taxRepository->findOneBy([]);
        $responses['remises']['voucher']['voucherMax'] = 0;
        $responses['remises']['voucher']['actif'] = false;

        if(!is_null($session->get('voucherDiscountId'))){
            $voucherDiscount = $this->voucherDiscountRepository->find($session->get('voucherDiscountId'));
            $responses['remises']['voucher']['voucherMax'] = $voucherDiscount->getRemainingValueToUseExcludingTax();
            $responses['remises']['voucher']['token'] = $voucherDiscount->getToken();
            $responses['remises']['voucher']['actif'] = true;
        }

        $now = new DateTimeImmutable('now');

        //gestion membership au niveau du panier
        if($user->getMembership() > $now){

            $responses['preparationHt'] = 0;

        }else{

            $responses['preparationHt'] = $docParams->getPreparation();

        }
        

        //?action sur le bouton payer / demande de devis du panier
        if($responses['panier_boites'] > 0){
            //? after doc is save in bdd, redirect to paiement
            $responses['redirectAfterSubmitPanierForPaiement'] = true; 
        }else{
            $responses['redirectAfterSubmitPanierForPaiement'] = false;
        }

        $responses['totauxItems'] = $this->utilitiesService->totauxItems($responses['panier_items']);
        $responses['totauxOccasions'] = $this->utilitiesService->totauxItems($responses['panier_occasions']);
        $responses['totauxBoites'] = $this->utilitiesService->totauxItems($responses['panier_boites']);

        $responses['weigthPanier'] = $responses['totauxBoites']['weigth'] + $responses['totauxOccasions']['weigth'] + $responses['totauxItems']['weigth'];

        if(is_null($shipping)){

            $responses['deliveryCostWithoutTax'] = new Delivery();
            $responses['deliveryCostWithoutTax']->setPriceExcludingTax(0);

        }else{
                
            $responses['deliveryCostWithoutTax'] = $this->deliveryRepository->findCostByDeliveryShippingMethod($shipping, $responses['weigthPanier']);

        }

        $responses['remises']['volume']['remiseDeQte'] = round($responses['totauxItems']['price'] * $responses['remises']['volume']['value'] / 100);
        
        $sousTotalItemHTAfterRemiseVolume = $responses['totauxItems']['price'] - $responses['remises']['volume']['remiseDeQte'];

        //? calcule de la remise sur les articles
        $diff = $sousTotalItemHTAfterRemiseVolume - $responses['remises']['voucher']['voucherMax'];

        if($diff >= 0){
            $responses['remises']['voucher']['used'] = $sousTotalItemHTAfterRemiseVolume - $diff;
            $responses['remises']['voucher']['voucherRemaining'] = 0; // reste à utilisé du bon
        }else{
            $responses['remises']['voucher']['used'] = $sousTotalItemHTAfterRemiseVolume;
            $responses['remises']['voucher']['voucherRemaining'] = $diff * -1; // reste à utilisé du bon
        }


        $responses['totalPanier'] = ($responses['preparationHt'] + $responses['totauxItems']['price'] + $responses['totauxBoites']['price'] + $responses['totauxOccasions']['price'] + $responses['deliveryCostWithoutTax']->getPriceExcludingTax()) - $responses['remises']['volume']['remiseDeQte'] - $responses['remises']['voucher']['used'];

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

}
