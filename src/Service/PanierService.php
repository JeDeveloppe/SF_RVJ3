<?php

namespace App\Service;

use App\Entity\Panier;
use App\Repository\OccasionRepository;
use App\Repository\PanierRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class PanierService
{
    public function __construct(
        private EntityManagerInterface $em,
        private OccasionRepository $occasionRepository,
        private PanierRepository $panierRepository,
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

            //?on récupère l'occasion
            $occasion = $panier->getOccasion();
            //?on remet en ligne l'occasion
            $occasion->setIsOnline(true);
            $this->em->persist($occasion);

            //? on supprime la ligne du panier
            $this->em->remove($panier);

            //?on sauvegarde le tout
            $this->em->flush();

            $reponse = ['success', 'Ligne supprimée du panier'];
        }

        return $reponse;
    }

    public function totalPriceItems($items){

        $total = 0;

        foreach($items as $item){
            $total += $item->getPriceWithoutTax();
        }

        return $total;
    }
}