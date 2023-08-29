<?php

namespace App\Service\ImportRvj2;

use App\Entity\Occasion;
use DateTimeImmutable;
use League\Csv\Reader;
use App\Repository\BoiteRepository;
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
        $occasion = $this->occasionRepository->findOneBy(['reference' => $arrayOccasion['reference']]);


        
        if(!$occasion){
            $occasion = new Occasion();
        }

            $donnees = explode("|",$arrayOccasion['vente']);

            if(count($donnees) > 1){
                $vente = true;
                $moyenAchat = $donnees[1];
                $prixVente = $donnees[0];
                $timeVente = $this->utilities->getDateTimeImmutableFromTimestamp($arrayOccasion['timeVente']);
            }else{
                $vente = false;
                $moyenAchat = null;
                $prixVente = null;
                $timeVente = null;
            }

            $occasion->setBoite($this->boiteRepository->findOneBy(['rvj2Id' => $arrayOccasion['idCatalogue']]))
                    ->setReference($arrayOccasion['reference'])
                    ->setPriceHt($arrayOccasion['prixHT'])
                    ->setOldPriceHt($arrayOccasion['ancienPrixHT'])
                    ->setInformation($this->stringToNull($arrayOccasion['information']))
                    ->setIsNeuf($arrayOccasion['isNeuf'])
                    ->setEtatBoite($arrayOccasion['etatBoite'])
                    ->setEtatMateriel($arrayOccasion['etatMateriel'])
                    ->setRegleJeu($arrayOccasion['regleJeu'])
                    ->setIsOnLine($arrayOccasion['actif'])
                    ->setIsDonation($arrayOccasion['don'])
                    ->setIsSale($vente)
                    ->setStock($arrayOccasion['stock'])
                    ->setMeansOfSale($moyenAchat)
                    ->setPrixDeVente($prixVente)
                    ->setSale($timeVente)
                    ->setRvj2Id($arrayOccasion['idJeuxComplet']);

            if($arrayOccasion['timeDon'] != 0){
                $occasion->setDonation($this->utilities->getDateTimeImmutableFromTimestamp($arrayOccasion['timeDon']));
            }

        $this->em->persist($occasion);
    }

    private function stringToNull($value){
        
        if($value == "NULL"){
            $value = NULL;
        }

        return $value;
    }
}