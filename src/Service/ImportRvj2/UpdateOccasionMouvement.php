<?php

namespace App\Service\ImportRvj2;

use App\Repository\OccasionRepository;
use App\Repository\OffSiteOccasionSaleRepository;
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

        $offSiteOccasionSales = $this->offSiteOccasionSaleRepository->findAll();
        
        $io->progressStart(count($offSiteOccasionSales));

        foreach($offSiteOccasionSales as $offSiteOccasionSale){
            $io->progressAdvance();

            $occasion = $this->occasionRepository->find($offSiteOccasionSale->getOccasion());
            $occasion->setOffSiteOccasionSale($offSiteOccasionSale);
            $this->em->persist($occasion);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Mise à jour terminée');
    }
}