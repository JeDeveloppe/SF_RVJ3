<?php

namespace App\Service\ImportRvj2;

use App\Entity\Country;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CreationCountrieService
{
    public function __construct(
        private CountryRepository $countryRepository,
        private EntityManagerInterface $em,
        ){
    }

    public function addCountries(){

        $countries = [];

        $countries[] = ['name' => 'FRANCE', 'isoCode' => 'FR'];
        $countries[] = ['name' => 'BELGIQUE', 'isoCode' => 'BE'];
        $countries[] = ['name' => 'INCONNU', 'isoCode' => 'INC'];

        foreach($countries as $countrie){

            $country = $this->countryRepository->findOneBy(['name' => $countrie['name']]);

            if(!$country){
                $country = new Country();
            }

            $country->setName($countrie['name'])->setIsocode($countrie['isoCode']);
            $this->em->persist($country);

        }
        $this->em->flush();

    }
}