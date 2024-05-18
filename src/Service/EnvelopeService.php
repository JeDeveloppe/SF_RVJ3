<?php

namespace App\Service;

use App\Entity\Envelope;
use App\Repository\EnvelopeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EnvelopeService
{
    public function __construct(
        private EntityManagerInterface $em,
        private EnvelopeRepository $envelopeRepository,
        ){
    }

    public function addEnvelopes(SymfonyStyle $io)
    {

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
}