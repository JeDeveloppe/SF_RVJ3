<?php

namespace App\Service;

use App\Entity\Panier;
use DateTimeImmutable;
use App\Entity\Delivery;
use App\Repository\ItemRepository;
use App\Repository\PanierRepository;
use App\Repository\DeliveryRepository;
use App\Repository\DocumentParametreRepository;
use App\Repository\OccasionRepository;
use App\Repository\ShippingMethodRepository;
use App\Repository\TaxRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class PanierService
{
    public function __construct(
        private EntityManagerInterface $em,
        private OccasionRepository $occasionRepository,
        private ItemRepository $itemRepository,
        private ShippingMethodRepository $shippingMethodRepository,
        private PanierRepository $panierRepository,
        private DeliveryRepository $deliveryRepository,
        private UtilitiesService $utilitiesService,
        private TaxRepository $taxRepository,
        private DocumentParametreRepository $documentParametreRepository,
        private Security $security
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

            $newQte = $item->getStockForSale() - $qte;

            if($panier){

                $panier
                ->setQte($panier->getQte() + $qte)
                ->setUnitPriceExclusingTax($item->getPriceExcludingTax())
                ->setCreatedAt( new DateTimeImmutable('now'))
                ->setPriceWithoutTax(($item->getPriceExcludingTax()) * ($panier->getQte() + $qte));

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
            $item->setStockForSale($newQte);
            $this->em->persist($item);

            //?on sauvegarde le tout
            $this->em->flush();

            $reponse = ['success', 'Article ajouté au panier'];
        }

        return $reponse;
    }

    public function calculateAllCart($user, $shippingIdOfEntity)
    {

        $docParams = $this->documentParametreRepository->findOneBy(['isOnline' => true]);
        $shipping = $this->shippingMethodRepository->findOneBy(['id' => $shippingIdOfEntity]);

        $responses = [];
        
        $responses['shipping'] = $shipping;
        $responses['panier_occasions'] = $this->panierRepository->findOccasionsByUser($user);
        $responses['panier_boites'] = $this->panierRepository->findBoitesByUser($user);
        $responses['panier_items'] = $this->panierRepository->findItemsByUser($user);
        $responses['tax'] = $this->taxRepository->findOneBy([]);
        $responses['preparationHt'] = $docParams->getPreparation();


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

            $responses['deliveryCostWithoutTax'] = $this->deliveryRepository->findCostByDeliveryShippingMethod($shippingIdOfEntity, $responses['weigthPanier']);

        }

        $responses['totalPanier'] = $responses['preparationHt'] + $responses['totauxItems']['price'] + $responses['totauxBoites']['price'] + $responses['totauxOccasions']['price'] + $responses['deliveryCostWithoutTax']->getPriceExcludingTax();

        return $responses;
    }
}