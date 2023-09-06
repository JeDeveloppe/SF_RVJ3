<?php

namespace App\Service\ImportRvj2;

use App\Entity\City;
use App\Entity\Ville;
use League\Csv\Reader;
use League\Csv\Statement;
use App\Repository\BoiteRepository;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DepartementRepository;
use App\Repository\DepartmentRepository;
use League\Csv\ResultSet;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportVillesBelgesService
{
    public function __construct(
        private EntityManagerInterface $em,
        private CityRepository $cityRepository,
        private DepartmentRepository $departmentRepository,
        private CountryRepository $countryRepository
        ){
    }

    public function importVilles1_5(SymfonyStyle $io): void
    {
        $io->title('Importation des villes Belges');

            $cities = $this->readCsvFileTotalVille();
        
            $io->progressStart(count($cities));

            foreach($cities as $arrayVille){

                $io->progressAdvance();
                $ville = $this->createOrUpdateCity($arrayVille);
                $this->em->persist($ville);
            }
            
            $this->em->flush();

            $io->progressFinish();
        

        $io->success('Importation terminée');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileVille($offset, $limit): ResultSet
    {
        $csv = Reader::createFromPath('%kernel.root.dir%/../import/villes_belgique.csv','r');
        $csv->setHeaderOffset(0);
        //get 25 records starting from the 11th row
        $stmt = Statement::create()
            ->offset($offset)
            ->limit($limit)
        ;

        $records = $stmt->process($csv);
        return $records;
    }

    private function readCsvFileTotalVille(): Reader
    {
        $csv = Reader::createFromPath('%kernel.root.dir%/../import/villes_belgique.csv','r');
        $csv->setHeaderOffset(0);

        return $csv;
    }

    private function createOrUpdateCity(array $arrayVille): City
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