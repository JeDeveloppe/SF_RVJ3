<?php

namespace App\Service\ImportRvj2;

use App\Entity\Departement;
use App\Entity\Department;
use App\Repository\CountryRepository;
use App\Repository\DepartementRepository;
use App\Repository\DepartmentRepository;
use League\Csv\Reader;
use App\Repository\PartenaireRepository;
use App\Repository\PaysRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportDepartementsService
{
    public function __construct(
        private EntityManagerInterface $em,
        private DepartmentRepository $departmentRepository,
        private CountryRepository $countryRepository
        ){
    }

    public function importDepartements(SymfonyStyle $io): void
    {
        $io->title('Importation des départements');

        $departements = $this->readCsvFileDepartements();
        
        $io->progressStart(count($departements));

        foreach($departements as $arrayDepartement){
            $io->progressAdvance();
            $departement = $this->createOrUpdateDepartment($arrayDepartement);
            $this->em->persist($departement);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation des départements terminé');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileDepartements(): Reader
    {
        $csvDepartement = Reader::createFromPath('%kernel.root.dir%/../import/departement.csv','r');
        $csvDepartement->setHeaderOffset(0);

        return $csvDepartement;
    }

    private function createOrUpdateDepartment(array $arrayDepartement): Department
    {
        $departement = $this->departmentRepository->findOneBy(['id' => $arrayDepartement['id']]);

        if(!$departement){
            $departement = new Department();
        }

        $departement->setCountry($this->countryRepository->findOneBy(['id' => $arrayDepartement['pays_id']]))
        ->setName($arrayDepartement['name']);

        return $departement;
    }

}