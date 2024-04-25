<?php

namespace App\Service\ImportRvj2;

use App\Entity\Occasion;
use League\Csv\Reader;
use App\Repository\BoiteRepository;
use App\Repository\ConditionOccasionRepository;
use App\Repository\MeansOfPayementRepository;
use App\Repository\MovementOccasionRepository;
use App\Repository\OccasionRepository;
use App\Repository\OffSiteOccasionSaleRepository;
use App\Service\UtilitiesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportOccasionsService
{
    public function __construct(
        private BoiteRepository $boiteRepository,
        private EntityManagerInterface $em,
        private OccasionRepository $occasionRepository,
        private MovementOccasionRepository $movementOccasionRepository,
        private MeansOfPayementRepository $meansOfPayementRepository,
        private ConditionOccasionRepository $conditionOccasionRepository,
        private UtilitiesService $utilitiesService,
        private OffSiteOccasionSaleRepository $offSiteOccasionSaleRepository,
        ){
    }

    public function importOccasions(SymfonyStyle $io): void
    {
        $io->title('Importation des occasions');

        $occasions = $this->readCsvFileJeuxComplets();
        
        $io->progressStart(count($occasions));

        foreach($occasions as $arrayOccasion){
            $io->progressAdvance();
            $this->createOrUpdateOccasion($arrayOccasion);
        }

        $this->em->flush();

        unset($occasions);
        $io->progressFinish();
        $io->success('Importation terminée');

    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileJeuxComplets(): Reader
    {
        $csvOccasions = Reader::createFromPath('%kernel.root.dir%/../import/_table_jeux_complets.csv','r');
        $csvOccasions->setHeaderOffset(0);

        return $csvOccasions;
    }

    private function createOrUpdateOccasion(array $arrayOccasion): void
    {
        $occasion = $this->occasionRepository->findOneBy(['rvj2id' => $arrayOccasion['idJeuxComplet']]);

        if(!$occasion){
            $occasion = new Occasion();
        }

        $occasion->setBoite($this->boiteRepository->findOneBy(['rvj2id' => $arrayOccasion['idCatalogue']]) ?? $this->boiteRepository->findOneBy(['name' => 'BOITE SUPPRIMEE']))
                ->setReference($arrayOccasion['reference'])
                ->setInformation($this->utilitiesService->stringToNull($arrayOccasion['information']))
                ->setIsNew($this->stringToBoolean($arrayOccasion['isNeuf']))
                ->setBoxCondition($this->conditionOccasionRepository->findOneBy(['name' => $arrayOccasion['etatBoite']]))
                ->setEquipmentCondition($this->conditionOccasionRepository->findOneBy(['name' => $arrayOccasion['etatMateriel']]))
                ->setGameRule($this->conditionOccasionRepository->findOneBy(['name' => $arrayOccasion['regleJeu']]))
                ->setIsOnLine($arrayOccasion['actif'])
                ->setOffSiteOccasionSale(null)
                ->setPriceWithoutTax($arrayOccasion['prixHT'])
                ->setDiscountedPriceWithoutTax($arrayOccasion['ancienPrixHT'])
                ->setRvj2id($arrayOccasion['idJeuxComplet'])
                ;

                $this->em->persist($occasion);

    }

    private function stringToBoolean($value){
        
        if($value = "NULL"){
            
            $value = 0;

        }else{

            $value = 1;
        }

        return $value;
    }
}