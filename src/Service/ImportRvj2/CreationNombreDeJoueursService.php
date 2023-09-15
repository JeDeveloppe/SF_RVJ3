<?php

namespace App\Service\ImportRvj2;

use App\Entity\MeansOfPayement;
use App\Entity\NumbersOfPlayers;
use App\Repository\NumbersOfPlayersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreationNombreDeJoueursService
{
    public function __construct(
        private EntityManagerInterface $em,
        private NumbersOfPlayersRepository $numbersOfPlayersRepository
        ){
    }

    public function addplayers(SymfonyStyle $io){

        $players = [];

        for($i = 1; $i < 9; $i++){
            $players[] = [
                'name' => $i,
                'key' => $i
            ];
        }
        $players[] = [
            'name' => 'Uniquement 1 joueur',
            'key' => 'u1'
        ];
        $players[] = [
            'name' => 'Uniquement 2 joueurs',
            'key' => 'u2'
        ];
        $players[] = [
            'name' => 'A définir',
            'key' => null
        ];

        foreach($players as $playerArray){

            $player = $this->numbersOfPlayersRepository->findOneBy(['name' => $playerArray['name']]);

            if(!$player){
                $player = new NumbersOfPlayers();
            }

            $player->setName($playerArray['name'])->setKeyword($playerArray['key']);
            $this->em->persist($player);

        }
        $this->em->flush();
        $io->success('Créations terminée');
    }
}