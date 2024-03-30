<?php

namespace App\Service\ImportRvj2;

use League\Csv\Reader;
use App\Entity\Document;
use App\Entity\Documentsending;
use App\Repository\DocumentRepository;
use App\Repository\DocumentsendingRepository;
use App\Repository\DocumentStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ShippingMethodRepository;
use App\Repository\TaxRepository;
use App\Repository\UserRepository;
use App\Service\DocumentService;
use App\Service\UtilitiesService;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportDocumentsService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ShippingMethodRepository $shippingMethodRepository,
        private DocumentStatusRepository $documentStatusRepository,
        private UserRepository $userRepository,
        private DocumentsendingRepository $documentsendingRepository,
        private DocumentRepository $documentRepository,
        private TaxRepository $taxRepository,
        private UtilitiesService $utilitiesService,
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
        unset($docs);
        $io->success('Importation terminée');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileDocuments(): Reader
    {
        $csvDocuments = Reader::createFromPath('%kernel.root.dir%/../import/_table_documents.csv','r');
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
        ->setQuoteNumber($arrayDoc['numero_devis']);

        $numFacture = $arrayDoc['numero_facture'];
        if(is_null($this->utilitiesService->stringToNull($arrayDoc['numero_facture']))){
            $numFacture = null;
        }else{
            $numFacture = $arrayDoc['numero_facture'];
        }

        $document
            ->setBillNumber($numFacture)
            ->setTotalExcludingTax($arrayDoc['totalHT'])
            ->setTotalWithTax($arrayDoc['totalTTC'])
            ->setDeliveryPriceExcludingTax($arrayDoc['prix_expedition'])
            ->setBillingAddress($arrayDoc['adresse_facturation'])
            ->setDeliveryAddress($arrayDoc['adresse_livraison'])
            ->setIsQuoteReminder($arrayDoc['relance_devis'])
            ->setEndOfQuoteValidation($this->utilitiesService->getDateTimeImmutableFromTimestamp($arrayDoc['end_validation']))
            ->setCreatedAt($this->utilitiesService->getDateTimeImmutableFromTimestamp($arrayDoc['time']))
            ->setTimeOfSendingQuote($this->utilitiesService->getDateTimeImmutableFromTimestamp($arrayDoc['time_mail_devis']))
            ->setIsDeleteByUser(false)
            ->setMessage($this->utilitiesService->stringToNull($arrayDoc['commentaire']))
            ->setTaxRate($this->taxRepository->findOneBy(['value' => 0]))
            ->setTaxRateValue(0)
            ->setCost($arrayDoc['prix_preparation'])
            ->setTokenPayment($this->utilitiesService->stringToNull($arrayDoc['num_transaction']));

        //?ok version 3
        $envoyer = explode('|',$arrayDoc['envoyer']);

        if($arrayDoc['etat'] == 2 && count($envoyer) > 1){
            $etat = $this->documentStatusRepository->findOneBy(['name' => $_ENV['DOCUMENT_STATUS_END']]);
        }else if($arrayDoc['etat'] == 3){ // 3 = envoyé dans la version 2
            $etat = $this->documentStatusRepository->findOneBy(['name' => $_ENV['DOCUMENT_STATUS_END']]); 
        }else if($arrayDoc['etat'] == 2 && $arrayDoc['envoyer'] == 0){
            $etat = $this->documentStatusRepository->findOneBy(['name' => $_ENV['DOCUMENT_STATUS_PAID_TO_PREPARE']]); 
        }else if($arrayDoc['etat'] == 1){ // non facturer dans version 2
            $etat = $this->documentStatusRepository->findOneBy(['name' => $_ENV['DOCUMENT_STATUS_NO_PAID']]);
        }else{
            $etat = $this->documentStatusRepository->findOneBy(['name' => $_ENV['DOCUMENT_STATUS_INDEFINED']]);
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

    public function creationDocumentSending(SymfonyStyle $io): void
    {
        $io->title('Création des envois');

        $docs = $this->readCsvFileDocuments();
        
        $io->progressStart(count($docs));

        foreach($docs as $arrayDoc){
            $io->progressAdvance();
            $document = $this->createOrUpdateDocumentSending($arrayDoc);

            $this->em->persist($document);
        }

        $this->em->flush();

        $io->progressFinish();
        unset($docs);
        $io->success('Création terminée');
    }

    private function createOrUpdateDocumentSending(array $arrayDoc):Document
    {
        $document = $this->documentRepository->findOneBy(['rvj2id' => $arrayDoc['idDocument']]);

        //?ok version 3
        if($arrayDoc['expedition'] == "poste"){
            $expedition = $this->shippingMethodRepository->findOneBy(['name' => $_ENV['SHIPPING_METHOD_BY_POSTE_NAME']]);
        }else if($arrayDoc['expedition'] == "mondialRelay"){
            $expedition = $this->shippingMethodRepository->findOneBy(['name' => $_ENV['SHIPPING_METHOD_BY_MONDIAL_RELAY_NAME']]);
        }else if($arrayDoc['expedition'] == "retrait_caen1"){
            $expedition = $this->shippingMethodRepository->findOneBy(['name' => $_ENV['SHIPPING_METHOD_BY_IN_RVJ_DEPOT_NAME']]);
        }else if($arrayDoc['expedition'] == "colissimo"){
            $expedition = $this->shippingMethodRepository->findOneBy(['name' => $_ENV['SHIPPING_METHOD_BY_COLISSIMO_NAME']]);
        }else{
            $expedition = $this->shippingMethodRepository->findOneBy(['name' => $_ENV['SHIPPING_METHOD_BY_INDEFINED']]);
        }

        $envoyer = explode('|',$arrayDoc['envoyer']);

        if(count($envoyer) > 1){
            
            $timeSending = $this->utilitiesService->getDateTimeImmutableFromTimestamp($envoyer[0]);
            
        }else{
            
            $timeSending = NULL;
        }

        if(count($envoyer) > 1){

            if($envoyer[1] == "SANS"){

                $sendingNumber = NULL;

            }else{

                $sendingNumber = $envoyer[1];
            }

        }else{

            $sendingNumber = NULL;
        }

        $document->setShippingMethod($expedition)->setSendingAt($timeSending)->setSendingNumber($sendingNumber);

        return $document;
    }
}