<?php

namespace App\Service;

use App\Entity\SiteSetting;
use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SiteSettingRepository;
use App\Repository\StockRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class StockService
{
    public function __construct(
        private EntityManagerInterface $em,
        private StockRepository $stockRepository
        ){
    }

    public function addStocks(SymfonyStyle $io){

        $io->title('Importation / mise à jour des stocks du site');

        $stock = $this->stockRepository->findOneBy([]);

        if(!$stock){
            $stock = new Stock();
        }

        $stock->setName($_ENV['STOCK_NAME_DEFAULT']);
        $this->em->persist($stock);
        $this->em->flush();

        $io->success('Terminé !');
    }
}