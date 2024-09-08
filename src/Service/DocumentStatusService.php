<?php

namespace App\Service;

use App\Entity\DocumentStatus;
use App\Repository\DocumentStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DocumentStatusService
{
    public function __construct(
        private DocumentStatusRepository $documentStatusRepository,
        private EntityManagerInterface $manager,
        ){
    }

    public function creationStatus(SymfonyStyle $io): void
    {
        $io->title('Création / mise à jour des status des documents');

        $statusDocuments = [];

        //! BIEN GARDER CETTE ORDER CAUSE BESOIN/ LOGIQUE DANS COMPONENTS/ADMIN_GROUP_BUTTON 
        $statusDocuments[] = [
            'name' => $_ENV['DOCUMENT_STATUS_PAID_TO_PREPARE'], 
            'action' => 'TO_PREPARE',
            'isTreatedDaily' => true
        ];
        $statusDocuments[] = [
            'name' => $_ENV['DOCUMENT_STATUS_PAID_TO_SET_ASIDE'], 
            'action' => 'SET_ASIDE',
            'isTreatedDaily' => true
        ];
        $statusDocuments[] = [
            'name' => $_ENV['DOCUMENT_STATUS_END'], 
            'action' => 'END',
            'isTreatedDaily' => false
        ];
        $statusDocuments[] = [
            'name' => $_ENV['DOCUMENT_STATUS_INDEFINED'], 
            'action' => 'UNKNOWN',
            'isTreatedDaily' => false
        ];
        $statusDocuments[] = [
            'name' => $_ENV['DOCUMENT_STATUS_NO_PAID'], 
            'action' => $_ENV['DEVIS_NO_PAID_LABEL'],
            'isTreatedDaily' => false
        ];
        $statusDocuments[] = [
            'name' => $_ENV['DOCUMENT_STATUS_QUOTE_WAITING_FOR_PRICE'], 
            'action' => $_ENV['DEVIS_WITHOUT_PRICE_LABEL'],
            'isTreatedDaily' => false
        ];


        foreach($statusDocuments as $statusDocument){
            $status = $this->documentStatusRepository->findOneBy(['name' => $statusDocument['name']]);

            if(!$status){
                $status = new DocumentStatus();
            }

            $status->setName($statusDocument['name'])->setAction($statusDocument['action'])->setIsToBeTraitedDaily($statusDocument['isTreatedDaily']);
            $this->manager->persist($status);
        }
        $this->manager->flush();

        $io->success('Terminée');

    }

}