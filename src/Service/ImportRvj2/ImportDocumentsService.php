<?php

namespace App\Service\ImportRvj2;

use League\Csv\Reader;
use App\Entity\Document;
use App\Repository\DocumentStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ShippingMethodRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportDocumentsService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ShippingMethodRepository $shippingMethodRepository,
        private DocumentStatusRepository $documentStatusRepository,
        private UserRepository $userRepository,
        // private DocumentRepository $documentRepository,
        // private PaysRepository $paysRepository,
        // private shippingMethodRepository $shippingMethodRepository,
        // private Utilities $utilities,
        // private InformationsLegalesRepository $informationsLegalesRepository,
        // private UserRepository $userRepository,
        // private OccasionRepository $occasionRepository,
        // private BoiteRepository $boiteRepository,
        // private PaiementRepository $paiementRepository,
        // private documentStatusRepository $documentStatusRepository
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

        //TODO faire Entity Document
        $document
        ->setToken($arrayDoc['validKey'])
        ->setRvj2Id($arrayDoc['idDocument'])
        ->setNumeroDevis((int) substr($arrayDoc['numero_devis'],3))
        ->setNumeroFacture((int) substr($arrayDoc['numero_facture'],3))
        ->setTotalHT($arrayDoc['totalHT'])
        ->setTotalTTC($arrayDoc['totalTTC'])
        ->setDeliveryPriceHt($arrayDoc['prix_expedition'])
        ->setAdresseFacturation($arrayDoc['adresse_facturation'])
        ->setAdresseLivraison($arrayDoc['adresse_livraison'])
        ->setIsRelanceDevis($arrayDoc['relance_devis'])
        ->setEndValidationDevis($this->utilities->getDateTimeImmutableFromTimestamp($arrayDoc['end_validation']))
        ->setCreatedAt($this->utilities->getDateTimeImmutableFromTimestamp($arrayDoc['time']))
        ->setEnvoiEmailDevis($this->utilities->getDateTimeImmutableFromTimestamp($arrayDoc['time_mail_devis']))
        ->setIsDeleteByUser(false)
        ->setPaiement(null)
        ->setMessage($arrayDoc['commentaire'])
        ->setTauxTva(0)
        ->setCost($arrayDoc['prix_preparation'])
        ->setTokenPaiementRvj2($arrayDoc['num_transaction']);

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
        $document->setEnvoi($expedition);

        //?ok version 3
        if($arrayDoc['etat'] == 2 && $arrayDoc['envoyer'] !== 0){
            $etat = $this->documentStatusRepository->findOneBy(['name' => 'EXPÉDIÉE / TERMINÉE']);
        }else if($arrayDoc['etat'] == 3){ // 3 = mis de cote dans la version 2
            $etat = $this->documentStatusRepository->findOneBy(['name' => 'MISE DE CÔTÉ']); 
        }else if($arrayDoc['etat'] == 2 && $arrayDoc['envoyer'] == 0){
            $etat = $this->documentStatusRepository->findOneBy(['name' => 'A PRÉPARER']); 
        }else if($arrayDoc['etat'] == 1){ // non facturer dans version 2
            $etat = $this->documentStatusRepository->findOneBy(['name' => 'NON FACTURÉ']);
        }else{
            $etat = $this->documentStatusRepository->findOneBy(['name' => 'INDÉFINIE']);
        }
        $document->setEtatDocument($etat);

        //?ok version 3
        $user = $this->userRepository->findOneBy(['rvj2Id' => (int) $arrayDoc['idUser']]);
        if(!$user){
            $document->setUser($this->userRepository->findOneBy(['email' => $_ENV['UNDEFINED_USER_EMAIL']]));
        }else{
            $document->setUser($user);
        }

        return $document;
    }
}