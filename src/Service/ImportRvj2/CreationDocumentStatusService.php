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

        $statusDocuments = [];

        //! BIEN GARDER CETTE ORDER CAUSE BESOIN/ LOGIQUE DANS COMPONENTS/ADMIN_GROUP_BUTTON 
        $statusDocuments[] = [
            'name' => 'PAYÉE / A PRÉPARER', 
            'action' => 'TO_PREPARE',
            'isTreatedDaily' => true
        ];
        $statusDocuments[] = [
            'name' => 'PAYÉE / MISE DE CÔTÉ', 
            'action' => 'SET_ASIDE',
            'isTreatedDaily' => true
        ];
        $statusDocuments[] = [
            'name' => 'EXPÉDIÉE / TERMINÉE', 
            'action' => 'END',
            'isTreatedDaily' => false
        ];
        $statusDocuments[] = [
            'name' => 'INDÉFINIE', 
            'action' => 'UNKNOWN',
            'isTreatedDaily' => true
        ];
        $statusDocuments[] = [
            'name' => 'NON PAYÉE', 
            'action' => 'NO_PAID',
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