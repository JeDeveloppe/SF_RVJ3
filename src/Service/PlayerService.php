<?php

namespace App\Service;

use App\Entity\NumbersOfPlayers;
use App\Repository\NumbersOfPlayersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PlayerService
{
    public function __construct(
        private EntityManagerInterface $em,
        private NumbersOfPlayersRepository $numbersOfPlayersRepository
        ){
    }

    public function addplayers(SymfonyStyle $io)
    {

        $players = [];

        for($i = 1; $i < 6; $i++){
            $players[] = [
                'name' => $i,
                'key' => $i,
                'display' => true
            ];
        }
        $players[] = [
            'name' => 6,
            'key' => '6+',
            'display'=> true
        ];
        for($i = 7; $i < 13; $i++){
            $players[] = [
                'name' => $i,
                'key' => $i,
                'display' => false
            ];
        }
        $players[] = [
            'name' => 'Uniquement 1 joueur',
            'key' => 'u1',
            'display'=> false
        ];
        $players[] = [
            'name' => 'Uniquement 2 joueurs',
            'key' => 'u2',
            'display'=> false
        ];
        $players[] = [
            'name' => '-', //! ne pas changer cette variable
            'key' => null,
            'display'=> false
        ];

        foreach($players as $playerArray){

            $player = $this->numbersOfPlayersRepository->findOneBy(['name' => $playerArray['name']]);

            if(!$player){
                $player = new NumbersOfPlayers();
            }

            $player->setName($playerArray['name'])->setKeyword($playerArray['key'])->setIsInOccasionFormSearch($playerArray['display']);
            $this->em->persist($player);

        }
        $this->em->flush();
        $io->success('Créations terminée');
    }
}