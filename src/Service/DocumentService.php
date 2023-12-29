<?php

namespace App\Service;

use DateInterval;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;
use DateTimeImmutable;
use App\Entity\Payment;
use App\Entity\Document;
use App\Entity\DocumentLine;
use App\Service\UtilitiesService;
use App\Entity\DocumentLineTotals;
use App\Repository\ItemRepository;
use App\Repository\UserRepository;
use App\Entity\Returndetailstostock;
use App\Repository\PaymentRepository;
use App\Repository\DocumentRepository;
use App\Repository\OccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DocumentLineRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\DocumentStatusRepository;
use App\Repository\LegalInformationRepository;
use App\Repository\DocumentParametreRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DocumentService
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private DocumentStatusRepository $documentStatusRepository,
        private DocumentParametreRepository $documentParametreRepository,
        private DocumentLineRepository $documentLineRepository,
        private Security $security,
        private AdresseService $adresseService,
        private UtilitiesService $utilitiesService,
        private ItemRepository $itemRepository,
        private LegalInformationRepository $legalInformationRepository,
        private OccasionRepository $occasionRepository,
        private EntityManagerInterface $em,
        private PaymentRepository $paymentRepository,
        private Environment $twig,
        private UserRepository $userRepository,
        private ParameterBagInterface $parameter
        ){
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
                ->setTotalExcludingTax($panierParams['totalPanier']);

                if(is_string($deliveryAddress)){ //? vente direct d'un occasion
                    $document
                        ->setDeliveryAddress($deliveryAddress)
                        ->setUser($this->userRepository->findOneBy(['email' => 'client_de_passage@refaitesvosjeux.fr']))
                        ->setBillingAddress($billingAddress);
                }else{
                    $document
                        ->setUser($this->security->getUser())
                        ->setDeliveryAddress($this->adresseService->constructAdresseForSaveInDatabase($deliveryAddress))
                        ->setBillingAddress($this->adresseService->constructAdresseForSaveInDatabase($billingAddress));
                }

        $document
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
                ->setIsDeleteByUser(false)
                ->setTimeOfSendingQuote(new DateTimeImmutable('now'))
                ->setDocumentStatus($this->documentStatusRepository->findOneBy(['action' => $actionToSearch]));

        $this->em->persist($document);
        $this->em->flush();


        $docLineTotals = new DocumentLineTotals();
        $docLineTotals
            ->setDocument($document)
            ->setBoitesWeigth($panierParams['totauxBoites']['weigth'])->setBoitesPriceWithoutTax($panierParams['totauxBoites']['price'])
            ->setItemsWeigth($panierParams['totauxItems']['weigth'])->setItemsPriceWithoutTax($panierParams['totauxItems']['price'])
            ->setOccasionsWeigth($panierParams['totauxOccasions']['weigth'])->setOccasionsPriceWithoutTax($panierParams['totauxOccasions']['price']);
        $this->em->persist($docLineTotals);
        $this->em->flush();
        // "panier_occasions" => array:1 [▶]
        // "panier_boites" => []
        // "panier_items" => array:2 [▶]
        $paniers = array_merge($panierParams['panier_occasions'],$panierParams['panier_boites'],$panierParams['panier_items']);

        if(is_string($deliveryAddress)){ //? vente direct d'un occasion
            $documentLine = new DocumentLine();
            $documentLine
                ->setOccasion($panierParams['occasion'])
                ->setQuantity(1)
                ->setDocument($document)
                ->setPriceExcludingTax($panierParams['totauxItems']['price']);
            
                $this->em->persist($documentLine);
                $this->em->flush();

        }else{
            foreach($paniers as $panier){
                $documentLine = new DocumentLine();
                $documentLine
                    ->setQuestion($panier->getQuestion() ?? NULL)
                    ->setQuantity($panier->getQte() ?? 1)
                    ->setBoite($panier->getBoite() ?? NULL)
                    ->setItem($panier->getItem() ?? NULL)
                    ->setOccasion($panier->getOccasion() ?? NULL)
                    ->setDocument($document)
                    ->setPriceExcludingTax($panier->getPriceWithoutTax());
                
                    $this->em->persist($documentLine);
                    $this->em->remove($panier);
            }
            //on met en BDD les differentes lignes
            $this->em->flush();
        }

        return $document;
    }

    public function deleteDocumentFromDataBaseAndPuttingItemsBoiteOccasionBackInStock(array $documentsToDelete)
    {

        foreach($documentsToDelete as $doc){

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
            }else{

                //? pour ne pas afficher dans la partie membre
                $doc->setIsLastQuoteCantBeDeleted(true);
                $this->em->persist($doc);
                $this->em->flush();
            }

        }

    }

    public function generateValuesForDocument($document):array
    { 

        $results = [];

        $results['docLines'] = $document->getDocumentLines();
        $results['tauxTva'] = $this->utilitiesService->calculTauxTva($document->getTaxRateValue());

        foreach($results['docLines'] as $docLine){

            $results['docLine_items'] = $this->documentLineRepository->findBy(['document' => $docLine->getDocument()->getId(), 'occasion' => null, 'boite' => null ]);
            $results['docLine_occasions'] = $this->documentLineRepository->findBy(['document' => $docLine->getDocument()->getId(), 'item' => null, 'boite' => null]);
            $results['docLine_boites'] = $this->documentLineRepository->findBy(['document' => $docLine->getDocument()->getId(), 'occasion' => null, 'item' => null]);

        }

        return $results;
    }

    public function generatePdf($document,$request)
    {

        $results = $this->generateValuesForDocument($document);
        $legales = $this->legalInformationRepository->findOneBy([]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $pathToBootstrapCss = $this->parameter->get('kernel.project_dir').'/assets/styles/template_bootstrap.css';

        $dompdf = new Dompdf($options);
        $html = $this->twig->render('site/document_download/_document_download.html.twig', [
            'document' => $document,
            'css' => file_get_contents('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css'),
            'legales' => $legales,
            "docLines" => $document->getDocumentLines(),
            "tva" => $results['tauxTva'],
            "docLine_items" => $results['docLine_items'],
            "docLine_occasions" => $results['docLine_occasions'],
            "docLine_boites" => $results['docLine_boites']
        ]);

        $dompdf->setBasePath($pathToBootstrapCss);
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        // Render the HTML as PDF
        $dompdf->render();
        // Output the generated PDF to Browser
        $dompdf->stream($document->getBillNumber().".pdf");

    }

    public function generateDocumentInDatabaseFromSaleDuringAfair($panierParams,$billingAddress,$deliveryAddress,$entityInstance)
    {

        //ON genere un nouveau numero
        $newNumero = $this->generateNewNumberOf("quoteNumber", "getQuoteNumber");

        //on cherche les parametres des documents
        $docParams = $this->documentParametreRepository->findOneBy(['isOnline' => true]);

        //puis on met dans la base
        $document = new Document();
        $now = new DateTimeImmutable('now');
        $endDevis = $now->add(new DateInterval('P'.$docParams->getDelayBeforeDeleteDevis().'D'));
 
        $document
                ->setToken($this->utilitiesService->generateRandomString())
                ->setQuoteNumber($docParams->getQuoteTag().$newNumero)
                ->setTotalExcludingTax($panierParams['totalPanier'])
                ->setDeliveryAddress($deliveryAddress)
                ->setUser($this->userRepository->findOneBy(['email' => 'client_de_passage@refaitesvosjeux.fr']))
                ->setBillingAddress($billingAddress)
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
                ->setIsDeleteByUser(false)
                ->setTimeOfSendingQuote(new DateTimeImmutable('now'))
                ->setDocumentStatus($this->documentStatusRepository->findOneBy(['action' => 'END']));

        $this->em->persist($document);
        $this->em->flush();
 
 
        $docLineTotals = new DocumentLineTotals();
        $docLineTotals
            ->setDocument($document)
            ->setBoitesWeigth(0)->setBoitesPriceWithoutTax(0)
            ->setItemsWeigth(0)->setItemsPriceWithoutTax(0)
            ->setOccasionsWeigth($panierParams['totauxOccasions']['weigth'])->setOccasionsPriceWithoutTax($panierParams['totauxOccasions']['price']);
        $this->em->persist($docLineTotals);
        $this->em->flush();


        $documentLine = new DocumentLine();
        $documentLine
            ->setOccasion($panierParams['occasion'])
            ->setQuantity(1)
            ->setDocument($document)
            ->setPriceExcludingTax($panierParams['totauxOccasions']['price']);
        
        $this->em->persist($documentLine);
        $this->em->flush();


        $paiement = $this->paymentRepository->findOneBy(['document' => $document]);

        if(!$paiement){
            $paiement = new Payment();
        }

        //on renseigne le paiement
        $paiement->setDocument($document)
                ->setMeansOfPayment($entityInstance->getMeansOfPaiement())
                ->setTokenPayment('AUCUN')
                ->setCreatedAt(new DateTimeImmutable('now'))
                ->setTimeOfTransaction($entityInstance->getMovementTime());
        //on sauvegarde le paiement
        $this->em->persist($paiement);
        $this->em->flush();

        //il faut creer le numero de facture
        $newNumero = $this->generateNewNumberOf('billNumber', 'getBillNumber');

        //on renseigne le paiement
        $paiement->setDetails('AUCUN DETAILS')
                ->setTimeOfTransaction($entityInstance->getMovementTime());
        //on sauvegarde le paiement
        $this->em->persist($paiement);
        $this->em->flush();

        //on met a jour le document en BDD
        $etat = $this->documentStatusRepository->findOneBy(['action' => 'END']);
        $document->setDocumentStatus($etat)->setBillNumber($docParams->getBillingTag().$newNumero);
        $this->em->persist($document);
        $this->em->flush();

        return $document;
    }
}