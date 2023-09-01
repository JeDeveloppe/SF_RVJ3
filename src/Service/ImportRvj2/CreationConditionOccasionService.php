<?php

namespace App\Service\ImportRvj2;

use App\Entity\ConditionOccasion;
use App\Repository\ConditionOccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreationConditionOccasionService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ConditionOccasionRepository $conditionOccasionRepository
        ){
    }

    public function addConditions(SymfonyStyle $io){

        $conditions = ['BON ÉTAT','ÉTAT MOYEN','ORIGINALE','SUR LA BOITE','IMPRIMÉE','COMME NEUF','SANS'];

        $io->title('Création: condition des occasion et des moyens de paiement');

        foreach($conditions as $conditionArray){

            $condition = $this->conditionOccasionRepository->findOneBy(['name' => $conditionArray]);

            if(!$condition){
                $condition = new ConditionOccasion();
            }

            $condition->setName($conditionArray);
            $this->em->persist($condition);

        }
        $this->em->flush();

    }
}