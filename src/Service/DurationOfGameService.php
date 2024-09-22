<?php

namespace App\Service;

use App\Entity\Color;
use App\Entity\DurationOfGame;
use App\Repository\BoiteRepository;
use App\Repository\DurationOfGameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DurationOfGameService
{
    public function __construct(
        private EntityManagerInterface $em,
        private DurationOfGameRepository $durationOfGameRepository,
        private BoiteRepository $boiteRepository
        ){
    }

    public function addDurations(SymfonyStyle $io)
    {

        $durations = [
            [
                'duration' => 'Moins de 30 min',
                'orderOfAppearance' => 1,
                'displayInForm' => true
            ],
            [
                'duration' => '30 à 60 min',
                'orderOfAppearance' => 2,
                'displayInForm' => true
            ],
            [
                'duration' => 'Plus de 60 min',
                'orderOfAppearance' => 3,
                'displayInForm' => true
            ],
            [
                'duration' => $_ENV['DURATION_OF_GAME_NO_TIME'],
                'orderOfAppearance' => 4,
                'displayInForm' => false
            ]
        ];

        $io->progressStart(count($durations));
        foreach($durations as $durationArray){

            $io->progressAdvance();

            $duration = $this->durationOfGameRepository->findOneBy(['name' => $durationArray['duration']]);

            if(!$duration){
                $duration = new DurationOfGame();
            }

            $duration->setName($durationArray['duration'])->setOrderOfAppearance($durationArray['orderOfAppearance'])->setDisplayInForm($durationArray['displayInForm']);
            $this->em->persist($duration);

        }
        $this->em->flush();
        $io->progressFinish();
        $io->success('Créations terminée');
    }

    public function addAleatoireDurationsToBoites(SymfonyStyle $io)
    {
        $durations = $this->durationOfGameRepository->findAll();

        $boites = $this->boiteRepository->findAll();

        foreach($boites as $boite){
            $boite->setDurationGame($durations[array_rand($durations,1)]);
            $this->em->persist($boite);
        }

        $this->em->flush();
    }
}