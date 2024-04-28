<?php

namespace App\Service;

use Mpdf\Mpdf;
use DateInterval;
use Twig\Environment;
use DateTimeImmutable;
use App\Entity\Payment;
use App\Entity\Document;
use App\Entity\DocumentLine;
use App\Service\UtilitiesService;
use App\Entity\DocumentLineTotals;
use App\Entity\DocumentStatus;
use App\Repository\ItemRepository;
use App\Repository\UserRepository;
use App\Entity\Returndetailstostock;
use App\Repository\AddressRepository;
use App\Repository\CollectionPointRepository;
use App\Repository\PaymentRepository;
use App\Repository\DocumentRepository;
use App\Repository\OccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DocumentLineRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\DocumentStatusRepository;
use App\Repository\LegalInformationRepository;
use App\Repository\DocumentParametreRepository;
use App\Repository\ShippingMethodRepository;
use App\Repository\VoucherDiscountRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;

class DocumentService
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private DocumentStatusRepository $documentStatusRepository,
        private DocumentParametreRepository $documentParametreRepository,
        private DocumentLineRepository $documentLineRepository,
        private Security $security,
        private AdresseService $adresseService,
        private AddressRepository $addressRepository,
        private UtilitiesService $utilitiesService,
        private ItemRepository $itemRepository,
        private LegalInformationRepository $legalInformationRepository,
        private OccasionRepository $occasionRepository,
        private EntityManagerInterface $em,
        private PaymentRepository $paymentRepository,
        private Environment $twig,
        private UserRepository $userRepository,
        private ParameterBagInterface $parameter,
        private MailService $mailService,
        private RouterInterface $router,
        private ShippingMethodRepository $shippingMethodRepository,
        private CollectionPointRepository $collectionPointRepository,
        private VoucherDiscountRepository $voucherDiscountRepository
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

            //on signal que le document precedent est supprimable
            $lastDocumentByYear[0]->setIsLastQuote(false);
            $this->em->persist($lastDocumentByYear[0]);
            $this->em->flush();

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

    public function saveDocumentLogicInDataBase(array $panierParams, Session $session):Document
    {

        $document = $this->generateDocument($panierParams, $session);
        $documentLineTotals = $this->generateDocumentLineTotals($panierParams, $document);
        $this->addVoucherDiscoundInDocumentLineTotals($panierParams, $documentLineTotals, $session);
        $this->generateAllLinesFromPanierIntoDocumentLines($panierParams, $document);

        $session->remove('shippingMethodeId');

        return $document;
    }

    public function generateDocument(array $panierParams, Session $session):Document
    {

        $billingAddress = $this->addressRepository->find($session->get('billingAddressId'));

        if($panierParams['shipping']->getPrice() == "PAYANT"){

            $deliveryAddress = $this->addressRepository->find($session->get('deliveryAddressId'));

        }else{

            $deliveryAddress = $this->collectionPointRepository->find($session->get('deliveryAddressId'));
        }
        
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
            ->setUser($this->security->getUser())
            ->setDeliveryAddress($this->adresseService->constructAdresseForSaveInDatabase($deliveryAddress))
            ->setBillingAddress($this->adresseService->constructAdresseForSaveInDatabase($billingAddress))
            ->setTotalWithTax($this->utilitiesService->htToTTC($panierParams['totalPanier'],$panierParams['tax']->getValue()))
            ->setDeliveryPriceExcludingTax($panierParams['deliveryCostWithoutTax'])
            ->setIsQuoteReminder(false)
            ->setEndOfQuoteValidation($endDevis)
            ->setCreatedAt($now)
            ->setIsLastQuote(true)
            ->setTaxRate($panierParams['tax'])
            ->setTaxRateValue($panierParams['tax']->getValue())
            ->setCost($panierParams['preparationHt'])
            ->setBillNumber(null)
            ->setIsDeleteByUser(false)
            ->setTimeOfSendingQuote(new DateTimeImmutable('now'))
            ->setDocumentStatus($this->documentStatusRepository->findOneBy(['action' => $actionToSearch]))
            ->setShippingMethod($panierParams['shipping']);

        $this->em->persist($document);
        $this->em->flush();

        return $document;

    }

    public function generateDocumentLineTotals($panierParams, Document $document):DocumentLineTotals
    {
        $docLineTotals = new DocumentLineTotals();
        $docLineTotals
            ->setDocument($document)
            ->setBoitesWeigth($panierParams['totauxBoites']['weigth'])->setBoitesPriceWithoutTax($panierParams['totauxBoites']['price'])
            ->setItemsWeigth($panierParams['totauxItems']['weigth'])->setItemsPriceWithoutTax($panierParams['totauxItems']['price'])
            ->setOccasionsWeigth($panierParams['totauxOccasions']['weigth'])->setOccasionsPriceWithoutTax($panierParams['totauxOccasions']['price'])
            ->setDiscountonpurchase(-1 * $panierParams['remises']['volume']['remiseDeQte'])->setDiscountonpurchaseinpurcentage($panierParams['remises']['volume']['value'])
            ->setVoucherDiscountValueUsed(-1 * $panierParams['remises']['voucher']['used']);
        $this->em->persist($docLineTotals);
        $this->em->flush();

        return $docLineTotals;
    }

    public function addVoucherDiscoundInDocumentLineTotals($panierParams,  $documentLineTotals, $session)
    {
        //on traite le bon de reduction s'il y en a un
        $voucherDiscountId = $session->get('voucherDiscountId');

        if(!is_null($voucherDiscountId)){
            $voucherDiscount = $this->voucherDiscountRepository->find($voucherDiscountId);
            $voucherDiscount->setRemainingValueToUseExcludingTax($panierParams['remises']['voucher']['voucherRemaining'])
            ->addDocumentLineTotal($documentLineTotals);
            $this->em->persist($voucherDiscount);
            $this->em->flush($voucherDiscount);
        }
    }

    public function generateAllLinesFromPanierIntoDocumentLines(array $panierParams, Document $document)
    {

        $paniers = array_merge($panierParams['panier_occasions'],$panierParams['panier_boites'],$panierParams['panier_items']);
        //on met en BDD par ligne de document
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
        }
        //on met en BDD les differentes lignes
        $this->em->flush();

        //on supprimer chaque panier de la BDD
        foreach($paniers as $panier){
            $this->em->remove($panier);
        }
        //on supprim en BDD les differentes lignes
        $this->em->flush();
    }

    public function deleteDocumentFromDataBaseAndPuttingItemsBoiteOccasionBackInStock(array $documentsToDelete)
    {

        foreach($documentsToDelete as $doc)
        {
            if($doc->getIsLastQuote() == false){

                $docLines = $this->documentLineRepository->findBy(['document' => $doc]);

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

            }else{

                $doc->setIsDeleteByUser(true);
                $this->em->persist($doc);
                $this->em->flush();

            }
        }
        
        $this->em->flush();
    }

    public function generateValuesForDocument($document):array
    { 
        $results = [];

        $docLines = $document->getDocumentLines();
        $results['tauxTva'] = $this->utilitiesService->calculTauxTva($document->getTaxRateValue());
        foreach($docLines as $docLine){

            $results['docLines_items'] = $this->documentLineRepository->findBy(['document' => $docLine->getDocument()->getId(), 'occasion' => null, 'boite' => null ]);
            $results['docLines_occasions'] = $this->documentLineRepository->findBy(['document' => $docLine->getDocument()->getId(), 'item' => null, 'boite' => null]);
            $results['docLines_boites'] = $this->documentLineRepository->findBy(['document' => $docLine->getDocument()->getId(), 'occasion' => null, 'item' => null]);

        }
        
        return $results;
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
                ->setIsDeleteByUser(false)
                ->setTimeOfSendingQuote(new DateTimeImmutable('now'))
                ->setDocumentStatus($this->documentStatusRepository->findOneBy(['action' => 'END']))
                ->setShippingMethod($panierParams['shipping']);

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
                ->setTimeOfTransaction($entityInstance->getMovementTime())
                ->setDetails('AUCUN DETAILS')
                ->setTimeOfTransaction($entityInstance->getMovementTime());
        //on sauvegarde le paiement
        $this->em->persist($paiement);
        $this->em->flush();

        //il faut creer le numero de facture
        $newNumero = $this->generateNewNumberOf('billNumber', 'getBillNumber');


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

    public function generateFpdf($document)
    {

        $results = $this->generateValuesForDocument($document);
        $legales = $this->legalInformationRepository->findOneBy([]);

        if($document->getCreatedAt()->format('Y') > 2023){
            $header = $this->twig->render('site/document_download/2024/_header.html.twig', [
                'legales' => $legales,
                'document' => $document,
    
            ]);
            $html = $this->twig->render('site/document_download/2024/_document_download.html.twig', [
                'document' => $document,
                'legales' => $legales,
                "docLines" => $document->getDocumentLines(),
                "tva" => $results['tauxTva'],
                "docLine_items" => $results['docLine_items'],
                "docLine_occasions" => $results['docLine_occasions'],
                "docLine_boites" => $results['docLine_boites']
            ]);
            $footer = $this->twig->render('site/document_download/2024/_totalsTable.html.twig', [
                'document' => $document,
                "tva" => $results['tauxTva'],
                "docLine_items" => $results['docLine_items'],
                "docLine_occasions" => $results['docLine_occasions'],
                "docLine_boites" => $results['docLine_boites']
            ]);
        }else{
            //TODO generate PDF
            dd('TODO');
        }
        
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'setAutoTopMargin' => 'stretch',
            'autoMarginPadding' => 30
        ]);

        $mpdf->SetHTMLHeader($header);
        $mpdf->WriteHTML($html);
        $mpdf->SetHTMLFooter($footer);
        $mpdf->Output();
    }

    public function statusChange(Document $document, DocumentStatus $status)
    {
        $legales = $this->legalInformationRepository->findOneBy([]);

        if($status->getAction() == 'END'){
            $now = new DateTimeImmutable('now');

            $document->getDocumentsending()->setSendingAt($now)->setSendingNumber(null);
        }

        $document->setDocumentStatus($status);
        $this->em->persist($document);
        $this->em->flush();

        $this->mailService->sendMail(
            $document->getUser()->getEmail(),
            'Suivi de votre document '.$document->getBillNumber(),
            'changement_statut',
            [
                'document' => $document,
                'legales' => $legales
            ],
            null,
            true
        );
    }

    public function renderIfDocumentNoExist()
    {
        $tableau = [
            'h1' => 'Document non trouvé !',
            'p1' => 'La consultation/ modification de ce document est impossible!',
            'p2' => 'Document inconnu ou supprimé !'
        ];

        return new Response($this->twig->render('site/document_view/_end_view.html.twig', [
            'tableau' => $tableau
        ]));
    }
}