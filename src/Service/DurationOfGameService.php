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

        $durations = [
            [
                'duration' => '- de 30 minutes',
                'orderOfAppearance' => 1
            ],
            [
                'duration' => '30 à 60 minutes',
                'orderOfAppearance' => 2
            ],
            [
                'duration' => '+ de 60 minutes',
                'orderOfAppearance' => 3
            ],
            [
                'duration' => $_ENV['DURATION_OF_GAME_NO_TIME'],
                'orderOfAppearance' => 4
            ]
        ];

        $io->progressStart(count($durations));
        foreach($durations as $durationArray){

            $io->progressAdvance();

            $duration = $this->durationOfGameRepository->findOneBy(['name' => $durationArray['duration']]);

            if(!$duration){
                $duration = new DurationOfGame();
            }

            $duration->setName($durationArray['duration'])->setOrderOfAppearance($durationArray['orderOfAppearance']);
            $this->em->persist($duration);

        }
        $this->em->flush();
        $io->progressFinish();
        $io->success('Créations terminée');
    }

}