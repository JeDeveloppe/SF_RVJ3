<?php

namespace App\Service;

use DateInterval;
use DateTimeImmutable;
use App\Entity\Document;
use App\Entity\DocumentLine;
use App\Entity\Returndetailstostock;
use App\Service\UtilitiesService;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\DocumentParametreRepository;
use App\Repository\DocumentStatusRepository;
use App\Repository\ItemRepository;
use App\Repository\OccasionRepository;

class DocumentService
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private DocumentStatusRepository $documentStatusRepository,
        private DocumentParametreRepository $documentParametreRepository,
        private Security $security,
        private AdresseService $adresseService,
        private UtilitiesService $utilitiesService,
        private ItemRepository $itemRepository,
        private OccasionRepository $occasionRepository,
        private EntityManagerInterface $em
        ){
    }

    public function quoteNumberGenerator($numberWithoutPrefix)
    {
        return $_ENV['QUOTE_TAG'].$numberWithoutPrefix;
    }

    public function billingNumberGenerator($numberWithoutPrefix)
    {
        return $_ENV['BILLING_TAG'].$numberWithoutPrefix;
    }

    public function generateNewNumberOf($column, $methode)
    {

        $dateTimeImmutable = new DateTimeImmutable('now');
        $yearForSearchInDatabase = $dateTimeImmutable->format('Y');
        $year = $dateTimeImmutable->format('y');  //format du numero => y pour 23  Y pour 2023
        $month = $dateTimeImmutable->format('m');

        //il faudra trouver le dernier document de la base et incrementer de 1 pour le document
        $lastDocumentByYear = $this->documentRepository->findLastEntryFromThisYear($column, $yearForSearchInDatabase);

        //si pas d'entree alors nouvelle annee
        if(count($lastDocumentByYear) == 0){
            
            $numero = 1;
            return $this->numberConstruction($numero,$year,$month);

        }else{
            //dernier entree on recupere le numero de devis DEV23090472
            $numero = substr($lastDocumentByYear[0]->getQuoteNumber(), -4) + 1; //2022010001 reste 0001 + 1
            return $this->numberConstruction($numero,$year,$month);
        }

    }

    public function numberConstruction($numero,$year,$month)
    {
        
        if($numero == 1){ //premier enregistrement de l'annee
            return $year.$month.'0001';
        }else{
            $longueur = strlen($numero); //dernier enregistrement

            if($longueur < 2){                        //moins de 10
                    $numeroCreer = $year.$month."000".$numero;
            }else if($longueur == 2){                 //de 10 à 99
                    $numeroCreer = $year.$month."00".$numero;
            }else if($longueur == 3){                 //de 100 à 999
                    $numeroCreer = $year.$month."0".$numero;
            }else if($longueur == 4){                 //de 1000 à 9999
                    $numeroCreer = $year.$month.$numero;
            }

            return $numeroCreer;
        }
    }

    public function saveDocumentInDataBase($panierParams,$billingAddress,$deliveryAddress)
    {

        // "tax" => App\Entity\Tax {#17248 ▶}
        // "redirectAfterSubmitPanierForPaiement" => true
        // "totauxItems" => array:2 [▶]
        // "totauxOccasions" => array:2 [▶]
        // "totauxBoites" => array:2 [▶]
        // "weigthPanier" => 1490
        // "deliveryCostWithoutTax" => App\Entity\Delivery {#16415 ▶}
        // "totalPanier" => 1630

        //ON genere un nouveau numero
        $newNumero = $this->generateNewNumberOf("quoteNumber", "getQuoteNumber");

        //on cherche les parametres des documents
        $docParams = $this->documentParametreRepository->findOneBy(['isOnline' => true]);

        //puis on met dans la base
        $document = new Document();
        $now = new DateTimeImmutable('now');
        $endDevis = $now->add(new DateInterval('P'.$docParams->getDelayBeforeDeleteDevis().'D'));

        if(count($panierParams['panier_boites']) > 0){
            $actionToSearch = 'DEVIS_WITHOUT_PRICE'; //? doit etre comme Service / importV2 / CreationDocumentStatusService
        }else{
            $actionToSearch = 'NO_PAID'; //? doit etre comme Service / importV2 / CreationDocumentStatusService
        }

        $document
                ->setToken($this->utilitiesService->generateRandomString())
                ->setQuoteNumber($docParams->getQuoteTag().$newNumero)
                ->setTotalExcludingTax($panierParams['totalPanier'])
                ->setDeliveryAddress($this->adresseService->constructAdresseForSaveInDatabase($deliveryAddress))
                ->setBillingAddress($this->adresseService->constructAdresseForSaveInDatabase($billingAddress))
                ->setTotalWithTax($this->utilitiesService->htToTTC($panierParams['totalPanier'],$panierParams['tax']->getValue()))
                ->setDeliveryPriceExcludingTax($panierParams['deliveryCostWithoutTax']->getPriceExcludingTax())
                ->setIsQuoteReminder(false)
                ->setEndOfQuoteValidation($endDevis)
                ->setCreatedAt($now)
                ->setTaxRate($panierParams['tax'])
                ->setTaxRateValue($panierParams['tax']->getValue())
                ->setCost($panierParams['preparationHt'])
                ->setSendingMethod($panierParams['shipping'])
                ->setSendingBy($panierParams['shipping']->getName())
                ->setUser($this->security->getUser())
                ->setIsDeleteByUser(false)
                ->setTimeOfSendingQuote(new DateTimeImmutable('now'))
                ->setDocumentStatus($this->documentStatusRepository->findOneBy(['action' => $actionToSearch]));
                


        $this->em->persist($document);

        //TODO a enlever
        $this->em->flush();

        // "panier_occasions" => array:1 [▶]
        // "panier_boites" => []
        // "panier_items" => array:2 [▶]
        $paniers = array_merge($panierParams['panier_occasions'],$panierParams['panier_boites'],$panierParams['panier_items']);

        foreach($paniers as $panier){
            $documentLine = new DocumentLine();
            $documentLine
                ->setQuestion($panier->getQuestion() ?? NULL)
                ->setQuantity($panier->getQte() ?? 1)
                ->setBoite($panier->getBoite() ?? NULL)
                ->setItem($panier->getItem() ?? NULL)
                ->setOccasion($panier->getOccasion() ?? NULL)
                ->setDocument($document)
                ->setPriceExcludingTax($panier->getUnitPriceExclusingTax() ?? 0);
            
                $this->em->persist($documentLine);
                $this->em->remove($panier);
        }
 
        //on met en BDD les differentes lignes
        //TODO a enlever
        $this->em->flush();

        return $document;
    }

    public function deleteDocumentFromDataBaseAndPuttingItemsBoiteOccasionBackInStock(){
        
        $now = new DateTimeImmutable('now');

        $docToDeletes = $this->documentRepository->findByDevisToDelete($now);

        foreach($docToDeletes as $doc){

            $nextDocument = $this->documentRepository->findOneBy(['id' => $doc->getId() + 1]);

            if($nextDocument){

                $docLines = $doc->getDocumentLines();

                foreach($docLines as $docLine){
                    
                    if(!is_null($docLine->getItem())){
                        $itemInDatabase = $this->itemRepository->find($docLine->getItem());
                        $itemInDatabase->setStockForSale($itemInDatabase->getStockForSale() + $docLine->getQuantity());
                        $this->em->persist($itemInDatabase);
                        $this->em->remove($docLine);
                    }

                    if(!is_null($docLine->getOccasion())){
                        $occasionInDatabase = $this->occasionRepository->find($docLine->getOccasion());
                        $occasionInDatabase->setIsOnline(true);
                        $this->em->persist($occasionInDatabase);
                        $this->em->remove($docLine);
                    }

                    if(!is_null($docLine->getBoite())){
                        $returnInStock = new Returndetailstostock();
                        $returnInStock->setDocument($doc->getQuoteNumber())
                            ->setQuestion($docLine->getQuestion())
                            ->setAnswer($docLine->getAnswer());
                        $this->em->persist($returnInStock);
                        $this->em->remove($docLine);
                    }

                }

                $this->em->remove($doc);

                $this->em->flush();
            }

        }

    }
}