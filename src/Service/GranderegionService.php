<?php

namespace App\Service;

use App\Entity\Granderegion;
use App\Repository\CountryRepository;
use App\Repository\GranderegionRepository;
use League\Csv\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\SluggerInterface;

class GranderegionService
{
    public function __construct(
        private EntityManagerInterface $em,
        private GranderegionRepository $granderegionRepository,
        private SluggerInterface $sluggerInterface,
        private CountryRepository $countryRepository
        ){
    }

    public function importRegionsFrancaise(SymfonyStyle $io): void
    {
        $io->title('Importation des grandes régions');

        $regions = $this->readCsvFileRegionsFrancaise();
        
        $io->progressStart(count($regions));

        foreach($regions as $arrayRegion){
            $io->progressAdvance();
            $region = $this->createOrUpdateRegionFrancaise($arrayRegion);
            $this->em->persist($region);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation des régions terminé');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileRegionsFrancaise(): Reader
    {
        $csvregion = Reader::createFromPath('%kernel.root.dir%/../import/regionsFR.csv','r');
        $csvregion->setHeaderOffset(0);

        return $csvregion;
    }

    private function createOrUpdateRegionFrancaise(array $arrayRegion): Granderegion
    {
        $region = $this->granderegionRepository->findOneBy(['codeRegion' => $arrayRegion['code']]);

        if(!$region){
            $region = new Granderegion();
        }

        $region->setCodeRegion($arrayRegion['code'])
            ->setName($arrayRegion['name'])
            ->setSlug($this->sluggerInterface->slug($arrayRegion['slug']))
            ->setCountry($this->countryRepository->findOneByIsocode('FR'));

        return $region;
    }

    public function importRegionsBelge($io)
    {
        $regions = [];
        $regions[] = ['name' => 'Wallonie', 'code' => 'WAL1'];
        $regions[] = ['name' => 'Flandre', 'code' => 'FLA1'];
        $regions[] = ['name' => 'Bruxelles', 'code' => 'BRU1'];

        $io->title('Importation des grandes régions Belge');
        $io->progressStart(count($regions));

        foreach($regions as $regionArray){
            $io->progressAdvance();

            $region = $this->granderegionRepository->findOneBy(['name' => $regionArray['name']]);
    
            if(!$region){
                $region = new Granderegion();
            }

            $region->setCodeRegion($regionArray['code'])
            ->setName($regionArray['name'])
            ->setSlug($this->sluggerInterface->slug($regionArray['name']))
            ->setCountry($this->countryRepository->findOneByIsocode('BE'));

            $this->em->persist($region);
        }

        $this->em->flush();
        $io->progressFinish();
        $io->success('Importation des régions Belge terminé');
    }

}