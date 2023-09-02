<?php

namespace App\Service\ImportRvj2;

use App\Repository\BoiteRepository;
use App\Repository\ConditionOccasionRepository;
use App\Repository\MeansOfPayementRepository;
use App\Repository\MovementOccasionRepository;
use App\Repository\OccasionRepository;
use App\Repository\OffSiteOccasionSaleRepository;
use App\Service\Utilities;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateOccasionMouvement
{
    public function __construct(
        private EntityManagerInterface $em,
        private OccasionRepository $occasionRepository,
        private OffSiteOccasionSaleRepository $offSiteOccasionSaleRepository,
        ){
    }

    public function updateOccasionMouvement(SymfonyStyle $io): void
    {
    
        $io->title('Mise à jour mouvements / occasions');

        $movements = $this->offSiteOccasionSaleRepository->findAll();
        
        $io->progressStart(count($movements));

        foreach($movements as $move){
            $io->progressAdvance();

            $occasion = $this->occasionRepository->find($move->getOccasion());
            $occasion->setOffSiteSale($move);
            $this->em->persist($occasion);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Mise à jour terminée');
    }
}