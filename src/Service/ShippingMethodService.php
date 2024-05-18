<?php

namespace App\Service;

use App\Entity\ShippingMethod;
use App\Repository\ShippingMethodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ShippingMethodService
{
    public function __construct(
        private ShippingMethodRepository $shippingMethodRepository,
        private EntityManagerInterface $em
        ){
    }

    public function createShippingMethode(SymfonyStyle $io): void
    {
        $io->title('Création / mise à jour des modes de livraison');

        $shippingMethods = [];
        $shippingMethods[] = [
            'name' => $_ENV['SHIPPING_METHOD_BY_MONDIAL_RELAY_NAME'],
            'price' => 'PAYANT',
            'actifInCart' => false,
            'forOccasionOnly' => false
        ];
        $shippingMethods[] = [
            'name' => $_ENV['SHIPPING_METHOD_BY_POSTE_NAME'],
            'price' => 'PAYANT',
            'actifInCart' => true,
            'forOccasionOnly' => false
        ];
        $shippingMethods[] = [
            'name' => $_ENV['SHIPPING_METHOD_BY_COLISSIMO_NAME'],
            'price' => 'PAYANT',
            'actifInCart' => false,
            'forOccasionOnly' => false
        ];
        $shippingMethods[] = [
            'name' => $_ENV['SHIPPING_METHOD_BY_INDEFINED'],
            'price' => 'PAYANT',
            'actifInCart' => false,
            'forOccasionOnly' => false
        ];
        $shippingMethods[] = [
            'name' => $_ENV['SHIPPING_METHOD_BY_IN_RVJ_DEPOT_NAME'],
            'price' => 'GRATUIT',
            'actifInCart' => true,
            'forOccasionOnly' => true
        ];
        $shippingMethods[] = [
            'name' => $_ENV['SHIPPING_METHOD_BY_IN_FAIR_NAME'],
            'price' => 'GRATUIT',
            'actifInCart' => false,
            'forOccasionOnly' => true
        ];

        foreach($shippingMethods as $shippingMethodArray){
            $shipping = $this->shippingMethodRepository->findOneBy(['name' => $shippingMethodArray['name']]);

            if(!$shipping){
                $shipping = new ShippingMethod();
            }

            $shipping->setName($shippingMethodArray['name'])->setForOccasionOnly($shippingMethodArray['forOccasionOnly'])->setIsActivedInCart($shippingMethodArray['actifInCart'])->setPrice($shippingMethodArray['price']);
            $this->em->persist($shipping);
        }
        $this->em->flush();

    }

}