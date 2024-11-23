<?php

namespace App\Service;

use App\Entity\Department;
use App\Repository\CountryRepository;
use App\Repository\DepartmentRepository;
use App\Repository\GranderegionRepository;
use League\Csv\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\SluggerInterface;

class DepartmentService
{
    public function __construct(
        private EntityManagerInterface $em,
        private DepartmentRepository $departmentRepository,
        private CountryRepository $countryRepository,
        private GranderegionRepository $granderegionRepository,
        private SluggerInterface $sluggerInterface
        ){
    }

    public function importDepartementsFrancais(SymfonyStyle $io): void
    {
        $io->title('Importation des départements Francais');

        $departements = $this->readCsvFileDepartementsFrancais();
        
        $io->progressStart(count($departements));

        foreach($departements as $arrayDepartement){
            $io->progressAdvance();
            $departement = $this->createOrUpdateDepartmentFrancais($arrayDepartement);
            $this->em->persist($departement);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation des départements terminé');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileDepartementsFrancais(): Reader
    {
        $csvDepartement = Reader::createFromPath('%kernel.root.dir%/../import/departmentsFR.csv','r');
        $csvDepartement->setHeaderOffset(0);

        return $csvDepartement;
    }

    private function createOrUpdateDepartmentFrancais(array $arrayDepartement): Department
    {
        $departement = $this->departmentRepository->findOneBy(['code' => $arrayDepartement['code'], 'name' => $arrayDepartement['name']]);

        if(!$departement){
            $departement = new Department();
        }

        $departement->setCountry($this->countryRepository->findOneBy(['isocode' => 'FR']))
                ->setName($arrayDepartement['name'])
                ->setCode($arrayDepartement['code'])
                ->setSlug($this->sluggerInterface->slug($arrayDepartement['slug']))
                ->setGrandeRegion($this->granderegionRepository->findOneBy(['codeRegion' =>$arrayDepartement['region_code'] ]));

        return $departement;
    }

    public function importDepartementsBelge(SymfonyStyle $io): void
    {
        $io->title('Importation des départements Belges');

        $departements = $this->readCsvFileDepartementsBelge();
        
        $io->progressStart(count($departements));

        foreach($departements as $arrayDepartement){
            $io->progressAdvance();
            $departement = $this->createOrUpdateDepartmentBelge($arrayDepartement);
            $this->em->persist($departement);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation des départements terminé');
    }

    private function createOrUpdateDepartmentBelge(array $arrayDepartement): Department
    {
        $departement = $this->departmentRepository->findOneBy(['name' => $arrayDepartement['name']]);

        if(!$departement){
            $departement = new Department();
        }

        $departement->setCountry($this->countryRepository->findOneBy(['isocode' => 'BE']))
                ->setName($arrayDepartement['name'])
                ->setCode($arrayDepartement['name'])
                ->setSlug($this->sluggerInterface->slug($arrayDepartement['name']))
                ->setGrandeRegion($this->granderegionRepository->findOneBy(['codeRegion' =>$arrayDepartement['region_code'] ]));

        return $departement;
    }

    private function readCsvFileDepartementsBelge(): Reader
    {
        $csvDepartement = Reader::createFromPath('%kernel.root.dir%/../import/departmentsBE.csv','r');
        $csvDepartement->setHeaderOffset(0);

        return $csvDepartement;
    }
}