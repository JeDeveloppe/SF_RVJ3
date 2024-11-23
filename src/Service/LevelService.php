<?php

namespace App\Service;

use App\Entity\Level;
use App\Repository\LevelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LevelService
{
    public function __construct(
        private EntityManagerInterface $em,
        private LevelRepository $levelRepository
        ){
    }

    public function addLevels(SymfonyStyle $io)
    {

        $levels = [];
        $levels[] = 
            [
                'name' => 'SUPER ADMIN',
                'nameInDatabase' => 'ROLE_SUPER_ADMIN'  //! variables a ne pas changer
            ];
        $levels[] = 
            [
                'name' => 'ADMIN',
                'nameInDatabase' => 'ROLE_ADMIN' //! variables a ne pas changer
            ];
        $levels[] = [
                'name' => 'BENEVOLE',
                'nameInDatabase' => 'ROLE_BENEVOLE' //! variables a ne pas changer
            ];

        $io->progressStart(count($levels));
        foreach($levels as $entity){

            $io->progressAdvance();

            $level = $this->levelRepository->findOneBy(['nameInDatabase' => $entity['nameInDatabase']]);

            if(!$level){
                $level = new Level();
            }

            $level->setName($entity['name'])->setNameInDatabase($entity['nameInDatabase']);
            $this->em->persist($level);

        }
        $this->em->flush();
        $io->progressFinish();
        $io->success('Créations terminée');
    }
}