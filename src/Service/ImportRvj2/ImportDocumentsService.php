<?php

namespace App\Service\ImportRvj2;

use League\Csv\Reader;
use App\Entity\Document;
use App\Repository\DocumentRepository;
use App\Repository\DocumentStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ShippingMethodRepository;
use App\Repository\TaxRepository;
use App\Repository\UserRepository;
use App\Service\DocumentService;
use App\Service\Utilities;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportDocumentsService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ShippingMethodRepository $shippingMethodRepository,
        private DocumentStatusRepository $documentStatusRepository,
        private UserRepository $userRepository,
        private DocumentRepository $documentRepository,
        private TaxRepository $taxRepository,
        private Utilities $utilities,
        private DocumentService $documentService
        ){
    }

    public function importDocuments(SymfonyStyle $io): void
    {
        $io->title('Importation des documents');

        $docs = $this->readCsvFileDocuments();
        
        $io->progressStart(count($docs));

        foreach($docs as $arrayDoc){
            $io->progressAdvance();
            $document= $this->createOrUpdateDocument($arrayDoc);

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

    private function createOrUpdateDocument(array $arrayDoc): Document
    {
        $document = $this->documentRepository->findOneBy(['token' => $arrayDoc['validKey']]);

        if(!$document){
            $document = new Document();
        }

        $document
        ->setToken($arrayDoc['validKey'])
        ->setRvj2id($arrayDoc['idDocument'])
        ->setQuoteNumber($this->documentService->quoteNumberGenerator(substr($arrayDoc['numero_devis'],3)));

        $numFacture = $arrayDoc['numero_facture'];
        if(is_null($this->utilities->stringToNull($arrayDoc['numero_facture']))){
            $numFacture = null;
        }else{
            $numFacture = $this->documentService->billingNumberGenerator(substr($arrayDoc['numero_facture'],3));
        }

        $document
        ->setBillNumber($numFacture)
        ->setTotalExcludingTax($arrayDoc['totalHT'])
        ->setTotalWithTax($arrayDoc['totalTTC'])
        ->setDeliveryPriceExcludingTax($arrayDoc['prix_expedition'])
        ->setBillingAddress($arrayDoc['adresse_facturation'])
        ->setDeliveryAddress($arrayDoc['adresse_livraison'])
        ->setIsQuoteReminder($arrayDoc['relance_devis'])
        ->setEndOfQuoteValidation($this->utilities->getDateTimeImmutableFromTimestamp($arrayDoc['end_validation']))
        ->setCreatedAt($this->utilities->getDateTimeImmutableFromTimestamp($arrayDoc['time']))
        ->setTimeOfSendingQuote($this->utilities->getDateTimeImmutableFromTimestamp($arrayDoc['time_mail_devis']))
        ->setIsDeleteByUser(false)
        ->setMessage($this->utilities->stringToNull($arrayDoc['commentaire']))
        ->setTaxRate($this->taxRepository->findOneBy(['value' => 0]))
        ->setCost($arrayDoc['prix_preparation'])
        ->setTokenPayment($this->utilities->stringToNull($arrayDoc['num_transaction']));

        //?ok version 3
        if($arrayDoc['expedition'] == "poste"){
            $expedition = $this->shippingMethodRepository->findOneBy(['name' => 'POSTE']);
        }else if($arrayDoc['expedition'] == "mondialRelay"){
            $expedition = $this->shippingMethodRepository->findOneBy(['name' => 'MONDIAL RELAY']);
        }else if($arrayDoc['expedition'] == "retrait_caen1"){
            $expedition = $this->shippingMethodRepository->findOneBy(['name' => 'RETRAIT A LA COOP 100%']);
        }else if($arrayDoc['expedition'] == "colissimo"){
            $expedition = $this->shippingMethodRepository->findOneBy(['name' => 'COLISSIMO']);
        }else{
            $expedition = $this->shippingMethodRepository->findOneBy(['name' => 'INDEFINI']);
        }
        $document->setSendingMethod($expedition);

        //?ok version 3
        if($arrayDoc['etat'] == 2 && $arrayDoc['envoyer'] !== 0){
            $etat = $this->documentStatusRepository->findOneBy(['name' => 'EXPÉDIÉE / TERMINÉE']);
        }else if($arrayDoc['etat'] == 3){ // 3 = mis de cote dans la version 2
            $etat = $this->documentStatusRepository->findOneBy(['name' => 'PAYÉE / MISE DE CÔTÉ']); 
        }else if($arrayDoc['etat'] == 2 && $arrayDoc['envoyer'] == 0){
            $etat = $this->documentStatusRepository->findOneBy(['name' => 'PAYÉE / A PRÉPARER']); 
        }else if($arrayDoc['etat'] == 1){ // non facturer dans version 2
            $etat = $this->documentStatusRepository->findOneBy(['name' => 'NON PAYÉE']);
        }else{
            $etat = $this->documentStatusRepository->findOneBy(['name' => 'INDÉFINIE']);
        }
        $document->setDocumentStatus($etat);

        //?ok version 3
        $user = $this->userRepository->findOneBy(['rvj2id' => (int) $arrayDoc['idUser']]);
        if(!$user){
            $document->setUser($this->userRepository->findOneBy(['email' => $_ENV['UNDEFINED_USER_EMAIL']]));
        }else{
            $document->setUser($user);
        }

        return $document;
    }
}