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
use Symfony\Component\String\Slugger\SluggerInterface;

class CityService
{
    public function __construct(
        private EntityManagerInterface $em,
        private CityRepository $cityRepository,
        private DepartmentRepository $departmentRepository,
        private CountryRepository $countryRepository,
        private SluggerInterface $sluggerInterface
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
        $csv = Reader::createFromPath('%kernel.root.dir%/../import/citiesFR.csv','r');
        $csv->setHeaderOffset(0);

        return $csv;
    }

    private function createOrUpdateCityFrance(array $arrayVille): City
    {
        $city = $this->cityRepository->findOneBy(['inseeCode' => $arrayVille['insee_code'], 'country' => $this->countryRepository->findOneBy(['isocode' => 'FR'])]);

        if(!$city){
            $city = new City();
        }

        // id,department_code,insee_code,zip_code,name,slug,gps_lat,gps_lng

        $city->setName($arrayVille['name'])
        ->setLatitude($arrayVille['gps_lat'])
        ->setLongitude($arrayVille['gps_lng'])
        ->setPostalcode($arrayVille['zip_code'])
        ->setSlug($this->sluggerInterface->slug($arrayVille['name']))
        ->setDepartment($this->departmentRepository->findOneBy(['code' => $arrayVille['department_code']]))
        ->setCountry($this->countryRepository->findOneBy(['isocode' => 'FR']))
        ->setInseeCode($arrayVille['insee_code']);

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
        $csv = Reader::createFromPath('%kernel.root.dir%/../import/citiesBE.csv','r');
        $csv->setHeaderOffset(0);

        return $csv;
    }

    private function createOrUpdateCityBelgique(array $arrayVille): City
    {
        $city = $this->cityRepository->findOneBy(['postalcode' => $arrayVille['ville_code_postal'], 'name' => $arrayVille['ville_nom'],  'country' => $this->countryRepository->findOneBy(['isocode' => 'BE'])]);

        if(!$city){
            $city = new City();
        }

        $city->setName($arrayVille['ville_nom'])
        ->setLatitude($arrayVille['lat'])
        ->setLongitude($arrayVille['lng'])
        ->setPostalcode($arrayVille['ville_code_postal'])
        ->setInseeCode($arrayVille['ville_code_postal'])
        ->setSlug($this->sluggerInterface->slug($arrayVille['ville_nom']))
        ->setDepartment($this->departmentRepository->findOneBy(['name' => $arrayVille['province']]))
        ->setCountry($this->countryRepository->findOneBy(['isocode' => 'BE']))
        ->setRvj2Id($arrayVille['ville_id']);

        return $city;
    }
}