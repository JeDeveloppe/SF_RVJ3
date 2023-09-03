<?php

namespace App\Service\ImportRvj2;

use App\Entity\DocumentStatus;
use App\Repository\DocumentStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreationDocumentStatusService
{
    public function __construct(
        private DocumentStatusRepository $documentStatusRepository,
        private EntityManagerInterface $manager,
        ){
    }

    public function creationStatus(SymfonyStyle $io): void
    {
        $io->title('Création / mise à jour des status des documents');

        $statusDocuments = ['EXPÉDIÉE / TERMINÉE', 'MISE DE CÔTÉ', 'A PRÉPARER', 'NON FACTURÉ', 'INDÉFINIE'];

        foreach($statusDocuments as $statusDocument){
            $status = $this->documentStatusRepository->findOneBy(['name' => $statusDocument]);

            if(!$status){
                $status = new DocumentStatus();
            }

            $status->setName($statusDocument);
            $this->manager->persist($status);
        }
        $this->manager->flush();

        $io->success('Terminée');

    }

}