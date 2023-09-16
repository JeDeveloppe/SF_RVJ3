<?php

namespace App\Service\ImportRvj2;

use App\Entity\Color;
use App\Entity\Delivery;
use App\Entity\Discount;
use App\Entity\Envelope;
use App\Repository\ColorRepository;
use App\Repository\DeliveryRepository;
use App\Repository\DiscountRepository;
use App\Repository\EnvelopeRepository;
use App\Repository\ShippingMethodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreationEnvelopesAndColorsAndDiscountsService
{
    public function __construct(
        private EntityManagerInterface $em,
        private EnvelopeRepository $envelopeRepository,
        private ColorRepository $colorRepository,
        private DiscountRepository $discountRepository,
        private ShippingMethodRepository $shippingMethodRepository,
        private DeliveryRepository $deliveryRepository
        ){
    }

    public function addDelivery(){
        $deliveries = [];
        $deliveries[] = [
            'shippingMethod' => $this->shippingMethodRepository->findOneBy(['name' => 'RETRAIT A LA COOP 100%']),
            'start' => 1,
            'end' => 9999,
            'price' => 0
        ];

        foreach($deliveries as $deliverie){
            $delivery = $this->deliveryRepository->findOneBy(['shippingMethod' => $deliverie['shippingMethod'], 'start' => $deliverie['start']]);

            if(!$delivery){
                $delivery = new Delivery();
            }

            $delivery->setShippingMethod($deliverie['shippingMethod'])
                ->setStart($deliverie['start'])
                ->setEnd($deliverie['end'])
                ->setPriceExcludingTax($deliverie['price']);

            $this->em->persist($delivery);
        }
        $this->em->flush($delivery);
    }

    public function addEnvelopes(SymfonyStyle $io){

        $envelopes = [];
        $envelopes[] = [
            'name' => 'A',
            'weight' => 7
        ];
        $envelopes[] = [
            'name' => 'B',
            'weight' => 7
        ];
        $envelopes[] = [
            'name' => 'C',
            'weight' => 7
        ];
        $envelopes[] = [
            'name' => 'D',
            'weight' => 7
        ];

        $io->progressStart(count($envelopes));
        foreach($envelopes as $envelopeArray){

            $io->progressAdvance();

            $envelope = $this->envelopeRepository->findOneBy(['name' => $envelopeArray['name']]);

            if(!$envelope){
                $envelope = new Envelope();
            }

            $envelope->setName($envelopeArray['name'])->setWeight($envelopeArray['weight']);
            $this->em->persist($envelope);

        }
        $this->em->flush();
        $io->progressFinish();
        $io->success('Créations terminée');
    }

    public function addColors(SymfonyStyle $io){

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

    public function addDiscounts(SymfonyStyle $io){

        $discounts = [];
        $discounts[] = [
            'start' => 1,
            'end' => 4,
            'value' => 0,
            'actif' => true
        ];
        $discounts[] = [
            'start' => 5,
            'end' => 9,
            'value' => 5,
            'actif' => true
        ];
        $discounts[] = [
            'start' => 10,
            'end' => 14,
            'value' => 10,
            'actif' => true
        ];
        $discounts[] = [
            'start' => 15,
            'end' => 19,
            'value' => 15,
            'actif' => true
        ];
        $discounts[] = [
            'start' => 20,
            'end' => 9999,
            'value' => 20,
            'actif' => true
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
                ->setIsOnline($discountsArray['actif']);
            $this->em->persist($discount);

        }
        $this->em->flush();
        $io->progressFinish();
        $io->success('Créations terminée');
    }
}