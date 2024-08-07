<?php

namespace App\Service;

use League\Csv\Reader;
use App\Entity\Occasion;
use App\Entity\MovementOccasion;
use App\Entity\ConditionOccasion;
use App\Repository\OccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BoiteRepository;
use App\Repository\MovementOccasionRepository;
use App\Repository\ConditionOccasionRepository;
use App\Repository\OffSiteOccasionSaleRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class OccasionService
{
    public function __construct(
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

    public function addConditions(SymfonyStyle $io)
    {

        $green = '#00BC9D';
        $orange = '#FDC448';
        $red = '#E84798';

        $conditions = [
            [
                'name' => 'BON ÉTAT',
                'color' => $green
            ],
            [
                'name' => 'ÉTAT MOYEN',
                'color' => $orange
            ],
            [
                'name' => 'ORIGINALE',
                'color' => $green
            ],
            [
                'name' => 'SUR LA BOITE',
                'color' => $green
            ],
            [
                'name' => 'IMPRIMÉE',
                'color' => $green
            ],
            [
                'name' => 'COMME NEUF',
                'color' => $green
            ],
            [
                'name' => 'SANS',
                'color' => $red
            ],
            [
                'name' => 'NEUF',
                'color' => $green
            ],
            [
                'name' => 'NEUVE',
                'color' => $green
            ]
            ];

        $ventesDons = ['DON','VENTE','INCONNU'];

        $io->title('Création: condition des occasion et des moyens de paiement');

        foreach($conditions as $conditionArray){

            $condition = $this->conditionOccasionRepository->findOneBy(['name' => $conditionArray]);

            if(!$condition){
                $condition = new ConditionOccasion();
            }

            $condition->setName($conditionArray['name'])->setColor($conditionArray['color']);
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

    public function returnAgesChoicesAndPageTitle(string $category = null):array
    {

        switch($category){
            case 'jeux-pour-enfants': //? pareil que la navbar et le footer
                $agesByCategory = [
                    'start' => 2,
                    'end' => 6
                ];
                $twig = [
                    'titleH1' => '<h1 class="col-11 text-center">Jeux pour <span class="text-purple">enfants</span></h1>'
                ]; //TODO Antoine
                break;
            case 'jeux-pour-initie-es': //? pareil que la navbar et le footer
                $agesByCategory = [
                    'start' => 12,
                    'end' => 99
                ];
                $twig = [
                    'titleH1' => '<h1 class="col-11 text-center">Jeux pour <span class="text-purple">initié·es</span></h1>'
                ]; //TODO Antoine
                break;
            case 'jeux-tout-public': //? pareil que la navbar et le footer
                $agesByCategory = [
                    'start' => 7,
                    'end' => 99
                ];
                $twig = [
                    'titleH1' => '<h1 class="col-11 text-center">Jeux tout <span class="text-purple">puplic</span></h1>'
                ]; //TODO Antoine
                break;
            default:
                $agesByCategory = [
                    'start' => 1,
                    'end' => 99
                ];
                $twig = [
                    'titleH1' => '<h1 class="col-11 text-center">Tous les <span class="text-purple">jeux</span></h1>'
                ]; //TODO Antoine
                break;
        }

        //calcul
        $choices = [];
        $ageChoices = [];
        for($i = $agesByCategory['start']; $i <= $agesByCategory['end']; $i++){
            $an = ' an';
            if($i > 1){
                $an = ' ans';
            }
            $ageChoices[$i.$an] = $i;
        }
        $choices['form_options'] = $ageChoices;
        $choices['start_and_end_ages'] = $agesByCategory;
        $choices['twig'] = $twig;

        return $choices;
    }
}