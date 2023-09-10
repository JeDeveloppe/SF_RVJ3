<?php

namespace App\Service\ImportRvj2;

use League\Csv\Reader;
use App\Entity\Paiement;
use App\Entity\Payment;
use App\Repository\DocumentRepository;
use App\Repository\MeansOfPayementRepository;
use App\Repository\PaymentRepository;
use App\Service\Utilities;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportPaiementService
{
    public function __construct(
        private EntityManagerInterface $em,
        private Utilities $utilities,
        private DocumentRepository $documentRepository,
        private PaymentRepository $paymentRepository,
        private MeansOfPayementRepository $meansOfPayementRepository
        ){
    }

    public function importPaiements(SymfonyStyle $io): void
    {
        $io->title('Importation des paiements');

        $docs = $this->readCsvFileDocuments();

        foreach($docs as $arrayDoc){

            $num_transaction = $this->utilities->stringToNull($arrayDoc['num_transaction']);

            if(!is_null($num_transaction)){

                $paiement = $this->createOrUpdatePaiement($arrayDoc);

                $this->em->persist($paiement);
            }
        }

        $this->em->flush();
        $io->success('Importation terminée');

    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileDocuments(): Reader
    {
        $csvDocuments = Reader::createFromPath('%kernel.root.dir%/../import/documents.csv','r');
        $csvDocuments->setHeaderOffset(0);

        return $csvDocuments;
    }

    private function createOrUpdatePaiement(array $arrayDoc): Payment
    {
        $document = $this->documentRepository->findOneBy(['rvj2id' => $arrayDoc['idDocument']]);

        $paiement = $this->paymentRepository->findOneBy(['document' => $document]);

        if(!$paiement){
            $paiement = new Payment();
        }

        //?cohérence mouvement ESPECES partout
        if($arrayDoc['moyen_paiement'] == 'ESP')
        {
            $moyenPaiement = 'ESPÈCES';

        }elseif($arrayDoc['moyen_paiement'] == 'NULL')
        {//?il peut y avoir ce cas

            $moyenPaiement = 'EN COURS';

        }else{

            $moyenPaiement = $arrayDoc['moyen_paiement'];

        }

        $paiement
        ->setTokenPayment($arrayDoc['num_transaction'])
        ->setDocument($document)
        ->setMeansOfPayment($this->meansOfPayementRepository->findOneBy(['name' => $moyenPaiement]))
        ->setCreatedAt($this->utilities->getDateTimeImmutableFromTimestamp($arrayDoc['time_transaction']))
        ->setTimeOfTransaction($this->utilities->getDateTimeImmutableFromTimestamp($arrayDoc['time_transaction']));

        return $paiement;
    }
}