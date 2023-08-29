<?php

namespace App\Service\ImportRvj2;

use DateTimeImmutable;
use League\Csv\Reader;
use App\Entity\Document;
use App\Entity\Paiement;
use App\Repository\PaysRepository;
use App\Repository\UserRepository;
use App\Repository\BoiteRepository;
use App\Repository\DocumentRepository;
use App\Repository\OccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MethodeEnvoiRepository;
use App\Repository\InformationsLegalesRepository;
use App\Repository\PaiementRepository;
use App\Service\Utilities;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportPaiementService
{
    public function __construct(
        private EntityManagerInterface $em,
        private DocumentRepository $documentRepository,
        private PaysRepository $paysRepository,
        private MethodeEnvoiRepository $methodeEnvoiRepository,
        private Utilities $utilities,
        private InformationsLegalesRepository $informationsLegalesRepository,
        private UserRepository $userRepository,
        private OccasionRepository $occasionRepository,
        private BoiteRepository $boiteRepository,
        private PaiementRepository $paiementRepository
        ){
    }

    public function importPaiements(SymfonyStyle $io): void
    {
        $io->title('Importation des paiements');

        $docs = $this->readCsvFileDocuments();
        
        $io->progressStart(count($docs));

        foreach($docs as $arrayDoc){
            $io->progressAdvance();
            $paiement = $this->createOrUpdatePaiement($arrayDoc);

            $this->em->persist($paiement);
            $this->em->flush();

            $document = $this->documentRepository->findOneBy(['tokenPaiementRvj2' => $paiement->getTokenTransaction()]);

            $document->setPaiement($paiement);

            $this->em->persist($document);
            
        }
        $this->em->flush();


        $io->progressFinish();
        $io->success('Importation terminée');

    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileDocuments(): Reader
    {
        $csvDocuments = Reader::createFromPath('%kernel.root.dir%/../import/documents.csv','r');
        $csvDocuments->setHeaderOffset(0);

        return $csvDocuments;
    }

    private function createOrUpdatePaiement(array $arrayDoc): Paiement
    {
        $paiement = $this->paiementRepository->findOneBy(['tokenTransaction' => $arrayDoc['num_transaction']]);

        if(!$paiement){
            $paiement = new Paiement();
        }

        $paiement
        ->setTokenTransaction($arrayDoc['num_transaction'])
        ->setMoyenPaiement($arrayDoc['moyen_paiement'])
        ->setCreatedAt($this->utilities->getDateTimeImmutableFromTimestamp($arrayDoc['time_transaction']))
        ->setTimeTransaction($this->utilities->getDateTimeImmutableFromTimestamp($arrayDoc['time_transaction']))
        ;

        return $paiement;
    }
}