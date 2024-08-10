<?php

namespace App\Service;

use App\Entity\CollectionPoint;
use App\Entity\Country;
use App\Repository\CityRepository;
use App\Repository\CollectionPointRepository;
use App\Repository\CountryRepository;
use App\Repository\ShippingMethodRepository;
use Doctrine\ORM\EntityManagerInterface;

class CollectionPointService
{
    public function __construct(
        private EntityManagerInterface $em,
        private CityRepository $cityRepository,
        private ShippingMethodRepository $shippingMethodRepository,
        private CollectionPointRepository $collectionPointRepository
        ){
    }

    public function addCollectionPoint(){

        $collectionPoints = [];

        $collectionPoints[] = [
            'firstname' => null,
            'lastname' => null,
            'street' => '33 route de Trouville',
            'city' => $this->cityRepository->findOneBy(['name' => 'CAEN', 'postalcode' => 14000]),
            'isActivedInCart' => true,
            'shippingmethod' => $this->shippingMethodRepository->findOneBy(['name' => $_ENV['SHIPPING_METHOD_BY_IN_RVJ_DEPOT_NAME']]),
            'organization' => 'COOP 5 pour 100',
            'isOriginForWebSiteCmds' => true
        ];


        foreach($collectionPoints as $collectionPointArray){

            $collectionPoint = $this->collectionPointRepository->findOneBy(['organization' => $collectionPointArray['organization'], 'street' => $collectionPointArray['street']]);

            if(!$collectionPoint){
                $collectionPoint = new CollectionPoint();
            }

            $collectionPoint->setCity($collectionPointArray['city'])
                ->setFirstname($collectionPointArray['firstname'])
                ->setLastname($collectionPointArray['lastname'])
                ->setStreet($collectionPointArray['street'])
                ->setIsActivedInCart($collectionPointArray['isActivedInCart'])
                ->setIsOriginForWebSiteCmds($collectionPointArray['isOriginForWebSiteCmds'])
                ->setShippingmethod($collectionPointArray['shippingmethod'])
                ->setOrganization($collectionPointArray['organization']);

                $this->em->persist($collectionPoint);

        }
        $this->em->flush();

    }
}