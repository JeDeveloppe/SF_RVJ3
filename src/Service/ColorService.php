<?php

namespace App\Service;

use App\Entity\Color;
use App\Repository\ColorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ColorService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ColorRepository $colorRepository
        ){
    }

    public function addColors(SymfonyStyle $io)
    {

        $colors = ['BLEU','VERT','JAUNE','ROUGE','ROSE'];

        $io->progressStart(count($colors));
        foreach($colors as $colorArray){

            $io->progressAdvance();

            $color = $this->colorRepository->findOneBy(['name' => $colorArray]);

            if(!$color){
                $color = new Color();
            }

            $color->setName($colorArray);
            $this->em->persist($color);

        }
        $this->em->flush();
        $io->progressFinish();
        $io->success('Créations terminée');
    }

}