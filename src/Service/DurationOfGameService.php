<?php

namespace App\Service;

use App\Entity\Color;
use App\Entity\DurationOfGame;
use App\Repository\DurationOfGameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DurationOfGameService
{
    public function __construct(
        private EntityManagerInterface $em,
        private DurationOfGameRepository $durationOfGameRepository
        ){
    }

    public function addDurations(SymfonyStyle $io)
    {

        $durations = ['- de 30 minutes', '30 à 45 minutes', '45 à 60 minutes', '+ de 60 minutes', 'Non indiquée'];

        $io->progressStart(count($durations));
        foreach($durations as $durationArray){

            $io->progressAdvance();

            $duration = $this->durationOfGameRepository->findOneBy(['name' => $durationArray]);

            if(!$duration){
                $duration = new DurationOfGame();
            }

            $duration->setName($durationArray);
            $this->em->persist($duration);

        }
        $this->em->flush();
        $io->progressFinish();
        $io->success('Créations terminée');
    }

}