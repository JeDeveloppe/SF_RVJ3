<?php

namespace App\Service;

use App\Entity\Discount;
use App\Repository\DiscountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DiscountService
{
    public function __construct(
        private EntityManagerInterface $em,
        private DiscountRepository $discountRepository,
        ){
    }

    public function addDiscounts(SymfonyStyle $io)
    {

        $discounts = [];
        $discounts[] = [
            'start' => 1,
            'end' => 4,
            'value' => 0,
            'actif' => true,
            'valueUsed' => 0
        ];
        $discounts[] = [
            'start' => 5,
            'end' => 9,
            'value' => 5,
            'actif' => true,
            'valueUsed' => 0
        ];
        $discounts[] = [
            'start' => 10,
            'end' => 14,
            'value' => 10,
            'actif' => true,
            'valueUsed' => 0
        ];
        $discounts[] = [
            'start' => 15,
            'end' => 19,
            'value' => 15,
            'actif' => true,
            'valueUsed' => 0
        ];
        $discounts[] = [
            'start' => 20,
            'end' => 9999,
            'value' => 20,
            'actif' => true,
            'valueUsed' => 0
        ];
        

        $io->progressStart(count($discounts));
        foreach($discounts as $discountsArray){

            $io->progressAdvance();

            $discount = $this->discountRepository->findOneBy(['start' => $discountsArray['start']]);

            if(!$discount){
                $discount = new Discount();
            }

            $discount->setStart($discountsArray['start'])
                ->setEnd($discountsArray['end'])
                ->setValue($discountsArray['value'])
                ->setIsOnline($discountsArray['actif'])
                ->setValueUsed($discountsArray['valueUsed']);
            $this->em->persist($discount);

        }
        $this->em->flush();
        $io->progressFinish();
        $io->success('Créations terminée');
    }
}