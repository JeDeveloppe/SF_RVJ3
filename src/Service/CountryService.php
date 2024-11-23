<?php

namespace App\Service;

use App\Entity\Country;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CountryService
{
    public function __construct(
        private CountryRepository $countryRepository,
        private EntityManagerInterface $em,
        ){
    }

    public function addCountries(){

        $countries = [];

        $countries[] = ['name' => 'FRANCE', 'isoCode' => 'FR', 'actif' => true];
        $countries[] = ['name' => 'BELGIQUE', 'isoCode' => 'BE', 'actif' => true];
        $countries[] = ['name' => 'INCONNU', 'isoCode' => 'INC', 'actif' => false];

        foreach($countries as $countrie){

            $country = $this->countryRepository->findOneBy(['name' => $countrie['name']]);

            if(!$country){
                $country = new Country();
            }

            $country->setName($countrie['name'])->setIsocode($countrie['isoCode'])->setIsActifInInscriptionForm($countrie['actif']);
            $this->em->persist($country);

        }
        $this->em->flush();

    }
}