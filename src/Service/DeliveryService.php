<?php

namespace App\Service;

use App\Entity\Delivery;
use App\Repository\CountryRepository;
use App\Repository\DeliveryRepository;
use App\Repository\ShippingMethodRepository;
use Doctrine\ORM\EntityManagerInterface;

class DeliveryService
{
    public function __construct(
        private EntityManagerInterface $em,
        private DeliveryRepository $deliveryRepository,
        private ShippingMethodRepository $shippingMethodRepository,
        private CountryRepository $countryRepository
        ){
    }

    public function addDelivery()
    {
        $deliveries = [];
        $deliveries[] = [
            'shippingMethod' => $this->shippingMethodRepository->findOneBy(['name' => $_ENV['SHIPPING_METHOD_BY_IN_RVJ_DEPOT_NAME']]),
            'start' => 1,
            'end' => 9999,
            'price' => 0,
            'country' => $this->countryRepository->findOneBy(['name' => 'FRANCE']),
        ];
        $deliveries[] = [
            'shippingMethod' => $this->shippingMethodRepository->findOneBy(['name' => $_ENV['SHIPPING_METHOD_BY_IN_RVJ_DEPOT_NAME']]),
            'start' => 1,
            'end' => 9999,
            'price' => 0,
            'country' => $this->countryRepository->findOneBy(['name' => 'BELGIQUE']),
        ];

        foreach($deliveries as $deliverie){
            $delivery = $this->deliveryRepository->findOneBy(['shippingMethod' => $deliverie['shippingMethod'], 'start' => $deliverie['start']]);

            if(!$delivery){
                $delivery = new Delivery();
            }

            $delivery->setShippingMethod($deliverie['shippingMethod'])
                ->setStart($deliverie['start'])
                ->setEnd($deliverie['end'])
                ->setCountry($deliverie['country'])
                ->setPriceExcludingTax($deliverie['price']);

            $this->em->persist($delivery);
        }
        $this->em->flush($delivery);
    }

}