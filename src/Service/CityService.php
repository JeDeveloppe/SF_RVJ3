<?php

namespace App\Service;

use App\Entity\City;
use League\Csv\Reader;
use League\Csv\ResultSet;
use League\Csv\Statement;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\DepartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CityService
{
    public function __construct(
        private EntityManagerInterface $em,
        private CityRepository $cityRepository,
        private DepartmentRepository $departmentRepository,
        private CountryRepository $countryRepository
        ){
    }

    public function importCitiesOfFrance(SymfonyStyle $io): void
    {
        $io->title('Importation des villes Françaises');

            $cities = $this->readCsvFileFrance();
            
            $io->progressStart(count($cities));

            foreach($cities as $arrayVille){

                $io->progressAdvance();
                $ville = $this->createOrUpdateCityFrance($arrayVille);
                $this->em->persist($ville);
            }
            
            $this->em->flush();

            unset($cities);
            $io->progressFinish();
        

        $io->success('Importation terminée');
    }

    private function readCsvFileFrance(): Reader
    {
        $csv = Reader::createFromPath('%kernel.root.dir%/../import/_table_villes_france_free.csv','r');
        $csv->setHeaderOffset(0);

        return $csv;
    }

    private function createOrUpdateCityFrance(array $arrayVille): City
    {
        $city = $this->cityRepository->findOneBy(['id' => $arrayVille['ville_id'], 'country' => $this->countryRepository->findOneBy(['isocode' => 'FR'])]);

        if(!$city){
            $city = new City();
        }

        $city->setName($arrayVille['ville_nom'])
        ->setLatitude($arrayVille['lat'])
        ->setLongitude($arrayVille['lng'])
        ->setPostalcode($arrayVille['ville_code_postal'])
        ->setDepartment($this->departmentRepository->findOneBy(['name' => $arrayVille['ville_departement']]) ?? $this->departmentRepository->findOneBy(['id' => 14]))
        ->setCountry($this->countryRepository->findOneBy(['isocode' => 'FR']))
        ->setRvj2Id($arrayVille['ville_id']);

        return $city;
    }

    public function importCitiesOfBelgique(SymfonyStyle $io): void
    {
        $io->title('Importation des villes Belges');

            $cities = $this->readCsvFileBergique();
        
            $io->progressStart(count($cities));

            foreach($cities as $arrayVille){

                $io->progressAdvance();
                $ville = $this->createOrUpdateCityBelgique($arrayVille);
                $this->em->persist($ville);
            }
            
            $this->em->flush();

            $io->progressFinish();
        

        $io->success('Importation terminée');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileBergique(): Reader
    {
        $csv = Reader::createFromPath('%kernel.root.dir%/../import/_table_villes_belgique_free.csv','r');
        $csv->setHeaderOffset(0);

        return $csv;
    }

    private function createOrUpdateCityBelgique(array $arrayVille): City
    {
        $city = $this->cityRepository->findOneBy(['id' => $arrayVille['ville_id'], 'country' => $this->countryRepository->findOneBy(['isocode' => 'BE'])]);

        if(!$city){
            $city = new City();
        }

        $city->setName($arrayVille['ville_nom'])
        ->setLatitude($arrayVille['lat'])
        ->setLongitude($arrayVille['lng'])
        ->setPostalcode($arrayVille['ville_code_postal'])
        ->setDepartment($this->departmentRepository->findOneBy(['name' => $arrayVille['province']]) ?? $this->departmentRepository->findOneBy(['id' => 14]))
        ->setCountry($this->countryRepository->findOneBy(['isocode' => 'BE']))
        ->setRvj2Id($arrayVille['ville_id']);

        return $city;
    }
}