<?php

namespace App\Service\ImportRvj2;

use App\Entity\OffSiteOccasionSale;
use League\Csv\Reader;
use App\Repository\BoiteRepository;
use App\Repository\ConditionOccasionRepository;
use App\Repository\MeansOfPayementRepository;
use App\Repository\MovementOccasionRepository;
use App\Repository\OccasionRepository;
use App\Repository\OffSiteOccasionSaleRepository;
use App\Service\Utilities;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreationMouvementsOccasionService
{
    public function __construct(
        private BoiteRepository $boiteRepository,
        private EntityManagerInterface $em,
        private OccasionRepository $occasionRepository,
        private MovementOccasionRepository $movementOccasionRepository,
        private MeansOfPayementRepository $meansOfPayementRepository,
        private ConditionOccasionRepository $conditionOccasionRepository,
        private Utilities $utilities,
        private OffSiteOccasionSaleRepository $offSiteOccasionSaleRepository,
        ){
    }

    public function importMouvementsOccasions(SymfonyStyle $io): void
    {

        $io->title('Importation des mouvements');

        $occasions = $this->readCsvFileJeuxComplets();
        
        $io->progressStart(count($occasions));

        foreach($occasions as $arrayOccasion){
            $io->progressAdvance();
            $this->createOrUpdateMouvements($arrayOccasion);
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

    private function createOrUpdateMouvements(array $arrayOccasion): void
    {
        if($arrayOccasion['timeDon'] != "0" || $arrayOccasion['timeVente'] != "NULL"){

                $occasion = $this->occasionRepository->findOneBy(['rvj2id' => $arrayOccasion['idJeuxComplet']]);
                $movementOfOccasion = $this->offSiteOccasionSaleRepository->findOneBy(['occasion' => $occasion]);
        
                if(!$movementOfOccasion){
                    $movementOfOccasion = new OffSiteOccasionSale();
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
                        $movement = $this->movementOccasionRepository->findOneBy(['name' => 'INCONNU']);
                        $timeMovement = null;
                        $movementPrice = null;
                        $meansOfPaiement = $this->meansOfPayementRepository->findOneBy(['name' => 'INCONNU']);
                    }
        
                    $movementOfOccasion->setMovement($movement)
                        ->setMeansOfPaiement($meansOfPaiement)
                        ->setMovementPrice($movementPrice * 100)
                        ->setMovementTime($timeMovement)
                        ->setOccasion($occasion);
        
                            $this->em->persist($movementOfOccasion);
        
            }
        }
}