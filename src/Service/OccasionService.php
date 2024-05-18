<?php

namespace App\Service;

use League\Csv\Reader;
use App\Entity\Occasion;
use App\Entity\MovementOccasion;
use Symfony\Component\Form\Form;
use App\Entity\ConditionOccasion;
use App\Repository\OccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\SearchOccasionInCatalogueType;
use App\Repository\BoiteRepository;
use App\Repository\MovementOccasionRepository;
use App\Repository\ConditionOccasionRepository;
use App\Repository\OffSiteOccasionSaleRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class OccasionService
{
    public function __construct(
        private SearchOccasionInCatalogueType $searchOccasionInCatalogueType,
        private OccasionRepository $occasionRepository,
        private ConditionOccasionRepository $conditionOccasionRepository,
        private MovementOccasionRepository $movementOccasionRepository,
        private EntityManagerInterface $em,
        private OffSiteOccasionSaleRepository $offSiteOccasionSaleRepository,
        private BoiteRepository $boiteRepository,
        private UtilitiesService $utilitiesService
    )
    {
    }

    public function findOccasionsFromOccasionForm(Form $form)
    {
        //le texte saisie
        $search = $form->get('search')->getData();
        $phrase = str_replace(" ","%",$search);

        //age choisie
        $age = $form->get('age')->getData() ?? 0;

        //nombre de joueurs
        $players = $form->get('playerMin')->getData();
        //si pas de choix du nombre de joueurs
        if(count($players) == 0){
            [$playerChoices,$ageChoices,$lengthChoices] = $this->searchOccasionInCatalogueType->formOccasionChoices();

            foreach($playerChoices as $playerChoice){
                $players[] = $playerChoice;
            }
        }
        $occasionsFromSearchByPhraseAndAge = $this->occasionRepository->findOccasionsFromSearchByPhraseAndAge($phrase,$age);

        $occasions = [];

        foreach($players as $player){
            foreach($occasionsFromSearchByPhraseAndAge as $occasion){
                if($occasion->getBoite()->getPlayersMin()->getName() == $player){
                    $occasions[] = $occasion;
                }
            }
        }

        return array_unique($occasions);
    }

    public function addConditions(SymfonyStyle $io)
    {

        $conditions = ['BON ÉTAT','ÉTAT MOYEN','ORIGINALE','SUR LA BOITE','IMPRIMÉE','COMME NEUF','SANS','NEUF','NEUVE'];

        $ventesDons = ['DON','VENTE','INCONNU'];

        $io->title('Création: condition des occasion et des moyens de paiement');

        foreach($conditions as $conditionArray){

            $condition = $this->conditionOccasionRepository->findOneBy(['name' => $conditionArray]);

            if(!$condition){
                $condition = new ConditionOccasion();
            }

            $condition->setName($conditionArray);
            $this->em->persist($condition);

        }

        foreach($ventesDons as $venteDon){

            $move = $this->movementOccasionRepository->findOneBy(['name' => $venteDon]);

            if(!$move){
                $move = new MovementOccasion();
            }

            $move->setName($venteDon);
            $this->em->persist($move);
        }

        $this->em->flush();

    }

    public function updateOccasionMouvement(SymfonyStyle $io): void
    {
    
        $io->title('Mise à jour mouvements / occasions');

        $offSiteOccasionSales = $this->offSiteOccasionSaleRepository->findAll();
        
        $io->progressStart(count($offSiteOccasionSales));

        foreach($offSiteOccasionSales as $offSiteOccasionSale){
            $io->progressAdvance();

            $occasion = $this->occasionRepository->find($offSiteOccasionSale->getOccasion());
            $occasion->setOffSiteOccasionSale($offSiteOccasionSale);
            $this->em->persist($occasion);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Mise à jour terminée');
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

    private function stringToBoolean($value)
    {
        
        if($value = "NULL"){
            
            $value = 0;

        }else{

            $value = 1;
        }

        return $value;
    }
}