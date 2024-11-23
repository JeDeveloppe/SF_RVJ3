<?php

namespace App\Service;

use App\Entity\ItemGroup;
use App\Repository\ItemGroupRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ItemGroupService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ItemGroupRepository $itemGroupRepository
        ){
    }

    public function addItemGroups(SymfonyStyle $io){

        $groups = ['Pions', 'Dés', 'Jetons', 'Tuiles', 'Cartes'];

        foreach($groups as $group){

            $itemGroup = $this->itemGroupRepository->findOneBy(['name' => $group]);

            if(!$itemGroup){
                $itemGroup = new ItemGroup();
            }

            $itemGroup->setName($group)->setUpdatedAt(new DateTimeImmutable('now'));
            $this->em->persist($itemGroup);

        }
        $this->em->flush();
        $io->success('Créations terminée');
    }
}