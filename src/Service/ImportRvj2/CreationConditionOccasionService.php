<?php

namespace App\Service\ImportRvj2;

use App\Entity\ConditionOccasion;
use App\Entity\MovementOccasion;
use App\Repository\ConditionOccasionRepository;
use App\Repository\MovementOccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreationConditionOccasionService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ConditionOccasionRepository $conditionOccasionRepository,
        private MovementOccasionRepository $movementOccasionRepository
        ){
    }

    public function addConditions(SymfonyStyle $io){

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
}