<?php

namespace App\Service;

use App\Entity\DocumentParametre;
use App\Repository\DocumentParametreRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DocumentParametreService
{

    public function __construct(
        private DocumentParametreRepository $documentParametreRepository,
        private EntityManagerInterface $em,
        private UserRepository $userRepository
    )
    {
    }

    public function initDocumentParametre(SymfonyStyle $io){

        $documentParametres = $this->documentParametreRepository->findAll();

        if(count($documentParametres) < 1){

            $documentParametre = new DocumentParametre();
            $documentParametre
                ->setBillingTag('FAC')
                ->setDelayBeforeDeleteDevis(4)
                ->setIsOnline(true)
                ->setPreparation(150)
                ->setQuoteTag('DEV')
                ->setUpdatedAt(new DateTimeImmutable('now'))
                ->setUpdatedBy($this->userRepository->findOneBy(['email' => $_ENV['ADMIN_EMAIL']]));

            $this->em->persist($documentParametre);
            $this->em->flush($documentParametre);
            $io->success('Créations des paramêtres de document terminée');
        }

    }



}