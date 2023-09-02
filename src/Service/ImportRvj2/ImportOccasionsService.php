<?php

namespace App\Service\ImportRvj2;

use App\Entity\Occasion;
use DateTimeImmutable;
use League\Csv\Reader;
use App\Repository\BoiteRepository;
use App\Repository\ConditionOccasionRepository;
use App\Repository\MeansOfPayementRepository;
use App\Repository\MovementOccasionRepository;
use App\Repository\OccasionRepository;
use App\Service\Utilities;
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
        private Utilities $utilities
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

        $io->progressFinish();
        $io->success('Importation terminée');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileJeuxComplets(): Reader
    {
        $csvOccasions = Reader::createFromPath('%kernel.root.dir%/../import/jeux_complets.csv','r');
        $csvOccasions->setHeaderOffset(0);

        return $csvOccasions;
    }

    private function createOrUpdateOccasion(array $arrayOccasion): void
    {
        $occasion = $this->occasionRepository->findOneBy(['rvj2id' => $arrayOccasion['idJeuxComplet']]);

        if(!$occasion){
            $occasion = new Occasion();
        }
            //! on detecte si on a fait un don
            if(count(explode("|",$arrayOccasion['don'])) > 1){

                $movement = $this->movementOccasionRepository->findOneBy(['name' => 'DON']);
                $timeMovement = $this->utilities->getDateTimeImmutableFromTimestamp($arrayOccasion['timeDon']);

                $donnees = explode("|",$arrayOccasion['don']);
                if(count($donnees) > 1){
                    $meansOfPaiement = $this->meansOfPayementRepository->findOneBy(['name' => $donnees[1]]);
                    $movementPrice = $donnees[0];

                }else{
                    $meansOfPaiement = null;
                    $movementPrice = null;
                }

            }elseif(count(explode("|",$arrayOccasion['vente'])) > 1){

                $movement = $this->movementOccasionRepository->findOneBy(['name' => 'VENTE']);
                $timeMovement = $this->utilities->getDateTimeImmutableFromTimestamp($arrayOccasion['timeVente']);

                $donnees = explode("|",$arrayOccasion['vente']);
                if(count($donnees) > 1){
                    $meansOfPaiement = $this->meansOfPayementRepository->findOneBy(['name' => $donnees[1]]);
                    $movementPrice = (int) $donnees[0];

                }else{
                    $meansOfPaiement = null;
                    $movementPrice = null;
                }

            }else{
                $movement = null;
                $timeMovement = null;
                $movementPrice = null;
                $meansOfPaiement = null;
            }

            $occasion->setBoite($this->boiteRepository->findOneBy(['rvj2id' => $arrayOccasion['idCatalogue']]))
                    ->setReference($arrayOccasion['reference'])
                    ->setInformation($this->stringToNull($arrayOccasion['information']))
                    ->setIsNew($this->stringToBoolean($arrayOccasion['isNeuf']))
                    ->setSellingPriceHT($arrayOccasion['prixHT'])
                    ->setBoxCondition($this->conditionOccasionRepository->findOneBy(['name' => $arrayOccasion['etatBoite']]))
                    ->setEquipmentCondition($this->conditionOccasionRepository->findOneBy(['name' => $arrayOccasion['etatMateriel']]))
                    ->setGameRule($this->conditionOccasionRepository->findOneBy(['name' => $arrayOccasion['regleJeu']]))
                    ->setIsOnLine($arrayOccasion['actif'])
                    ->setMovement($movement)
                    ->setMeansOfPaiement($meansOfPaiement)
                    ->setMovementPrice($movementPrice * 100)
                    ->setMovementTime($timeMovement)
                    ->setRvj2id($arrayOccasion['idJeuxComplet'])
                    ;

        $this->em->persist($occasion);
    }

    private function stringToNull($value){
        
        if($value == "NULL"){
            $value = NULL;
        }

        return $value;
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