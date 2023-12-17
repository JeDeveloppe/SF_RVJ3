<?php

namespace App\Service;

use DateInterval;
use Dompdf\Dompdf;
use DateTimeImmutable;
use App\Entity\Document;
use App\Entity\DocumentLine;
use App\Service\UtilitiesService;
use App\Entity\DocumentLineTotals;
use App\Repository\ItemRepository;
use App\Entity\Returndetailstostock;
use App\Repository\DocumentRepository;
use App\Repository\OccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DocumentLineRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\DocumentStatusRepository;
use App\Repository\LegalInformationRepository;
use App\Repository\DocumentParametreRepository;
use Dompdf\Options;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;

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
        private Environment $twig,
        private ParameterBagInterface $parameter
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

    public function generatePdf($document,$request){

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

        $dompdf->loadHtml($html);
        $dompdf->setBasePath($pathToBootstrapCss);
        $dompdf->set_option('isHtml5ParserEnabled', true);

dd($dompdf);
      
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        // Render the HTML as PDF
        $dompdf->render();
        // Output the generated PDF to Browser
        $dompdf->stream($document->getBillNumber().".pdf");

    }

    //HEADER
    // $pdf->Image('build/images/design/logoSite.png',$leftMargin,6,30);
    // // Infos de la socièté sous le logo à gauche
    // $pdf->SetFont('Helvetica','',8);
    // $pdf->Text($leftMargin,35,utf8_decode($legales->getStreetCompany().', '.$legales->getPostalCodeCompany().' '.$legales->getCityCompany()));
    // $pdf->Text($leftMargin,40,'Siret : '.$legales->getSiretCompany());









    // //EN TETE DU TABLEAU
    // $position_entete_produits = 105;
    // $lines = $document->getDocumentLines();
    // $tableDesignationsHeader = ['Désignation','Qté','P.U HT', 'Total HT'];











    // // entete_table_accessoires($position_entete_produits);

    // $positionLigneAchat = 8;


    //    //     'document' => $document,
    //     //     'docLine_items' => $results['docLine_items'],
    //     //     'docLine_occasions' => $results['docLine_occasions'],
    //     //     'docLine_boites' => $results['docLine_boites'],
    //     //     'tva' => $results['tauxTva']

    // if(count($results['docLine_occasions']) > 0){
    //     foreach($results['docLine_occasions'] as $ligneAchat){
    //         $pdf->SetY($position_entete_produits + $positionLigneAchat);
    //         $pdf->SetX(10);
    //         $pdf->MultiCell(168,8,utf8_decode("Jeu d'occasion: ".$ligneAchat->getOccasion()->getBoite()->getName().' - '.$ligneAchat->getOccasion()->getBoite()->getEditor()),1,'C');
    //         $pdf->SetY($position_entete_produits + $positionLigneAchat);
    //         $pdf->SetX(176);
    //         $pdf->MultiCell(26,8,number_format($ligneAchat->getPriceExcludingTax() / 100 ,2),1,'R');
    //         $positionLigneAchat += 6;
    //     }
    // }

    // $position_detail = $position_entete_produits + $positionLigneAchat; // Position à 9mm de l'entête

    //LIGNE FOURNITURES
    // if(count($boites) > 0){
    //     $pdf->SetY($position_detail);
    //     $pdf->SetX(8);
    //     $pdf->MultiCell(168,8,utf8_decode("Fourniture(s) de pièce(s)"),1,'C');
    //     $pdf->SetY($position_detail);
    //     $pdf->SetX(176);
    //     $pdf->MultiCell(24,8,number_format($totalDetachees / 100,2),1,'R');
    // }else{
    //     $position_detail -= 8;
    // }


    //LIGNE LIVRAISON
    // $livraisons = explode('<br/>',$document->getDeliveryAddress());

    // $destinationLivraisonOuRetrait = '';

    // foreach($livraisons as $livraison){
    //     $destinationLivraisonOuRetrait .= $livraison.' ';
    // }

    // $pdf->SetY($position_detail + 16);
    // $pdf->SetX(8);
    // if($document->getDeliveryPriceExcludingTax() == 0){
    //     $pdf->MultiCell(168,8,utf8_decode("RETRAIT: ".$destinationLivraisonOuRetrait),1,'R');
    // }else{
    //     $pdf->MultiCell(168,8,utf8_decode("LIVRAISON: ".$destinationLivraisonOuRetrait),1,'R');
    // }
    // $pdf->SetY($position_detail + 16);
    // $pdf->SetX(176);
    // $pdf->MultiCell(24,8,number_format($document->getDeliveryPriceExcludingTax() / 100,2),1,'R');



    //tableau des totaux
    // $totalsTableHeader = ['Occasions','Pièces D.','Articles','Livraison','Préparation','HT','TVA (%)','TTC'];
    // $totalsTableDatas = $document->getDocumentLineTotals();

    // $tableauTotauxY = $position_detail + 50;       

    // // Couleurs, épaisseur du trait et police grasse
    // $pdf->SetFillColor(255,0,0);
    // $pdf->SetTextColor(255);
    // $pdf->SetDrawColor(128,0,0);
    // $pdf->SetLineWidth(.3);
    // $pdf->SetFont('','B');
    // //positionnement à partir du bas
    // $pdf->SetY(-15); 
    // // En-tête 
    // $w = array(25, 25, 25, 25, 25, 25, 25, 25);
    // for($i=0;$i<count($totalsTableHeader);$i++)
    //     $pdf->Cell($w[$i],7,$totalsTableHeader[$i],1,0,'C',true);
    // $pdf->Ln();
    // // Restauration des couleurs et de la police
    // $pdf->SetFillColor(224,235,255);
    // $pdf->SetTextColor(0);
    // $pdf->SetFont('');
    // // Données
    // $fill = false;

    // // -itemsWeigth: 0
    // // -itemsPriceWithoutTax: 0
    // // -occasionsWeigth: 139
    // // -occasionsPriceWithoutTax: 300
    // // -boitesWeigth: 0
    // // -boitesPriceWithoutTax: 0
    // $pdf->Cell($w[0],6,number_format($totalsTableDatas->getItemsPriceWithoutTax() / 100 ,2),'LR',0,'C',$fill);
    // $pdf->Cell($w[1],6,number_format($totalsTableDatas->getOccasionsPriceWithoutTax() / 100 ,2),'LR',0,'C',$fill);
    // $pdf->Cell($w[2],6,number_format($totalsTableDatas->getBoitesPriceWithoutTax() / 100 ,2),'LR',0,'C',$fill);
    // $pdf->Cell($w[3],6,number_format($document->getDeliveryPriceExcludingTax() / 100 ,2),'LR',0,'C',$fill);
    // $pdf->Cell($w[4],6,number_format($document->getCost() / 100 ,2),'LR',0,'C',$fill);
    // $pdf->Cell($w[5],6,number_format($document->getTotalExcludingTax() / 100 ,2),'LR',0,'C',$fill);
    // $pdf->Cell($w[6],6,number_format(($document->getTotalWithTax() - $document->getTotalExcludingTax()) / 100 ,2),'LR',0,'C',$fill);
    // $pdf->Cell($w[7],6,number_format($document->getTotalWithTax() / 100 ,2),'LR',0,'C',$fill);
    // $pdf->Ln();
    // $fill = !$fill;
    
    // // Trait de terminaison
    // $pdf->Cell(array_sum($w),0,'','T');

    // $pdf->SetY($tableauTotauxY);
    // $pdf->SetX(148);
    // $pdf->MultiCell(28,8,"Total HT:",1,'L');
    // $pdf->SetY($tableauTotauxY);
    // $pdf->SetX(176);
    // $pdf->MultiCell(24,8,number_format($document->getTotalExcludingTax() / 100,"2",".",""),1,'R');
    // $pdf->SetY($tableauTotauxY + 8);
    // $pdf->SetX(148);
    // $pdf->MultiCell(28,8,"TVA:",1,'L');
    // $pdf->SetY($tableauTotauxY + 8);
    // $pdf->SetX(176);
    // $pdf->MultiCell(24,8,number_format(($document->getTotalWithTax() - $document->getTotalExcludingTax())  / 100,"2",".",""),1,'R');
    // $pdf->SetY($tableauTotauxY + 16);
    // $pdf->SetX(148);
    // $pdf->MultiCell(28,8,"Total TTC:",1,'L');
    // $pdf->SetY($tableauTotauxY + 16);
    // $pdf->SetX(176);
    // $pdf->MultiCell(24,8,number_format($document->getTotalWithTax() / 100,"2",".",""),1,'R');

    //LIGNE REMERCIEMENT
    // $pdf->SetFont('Helvetica','',12);
    // $pdf->SetY(250);
    // $pdf->SetX(10);
    // $pdf->MultiCell(190,8,utf8_decode("MERCI POUR VOTRE COMMANDE."),0,'C');

    // //ligne TVA dans table de config vaut 1 = PAS DE TVA
    // if($document->getDeliveryPriceExcludingTax() == 0){
    // $pdf->SetFont('Helvetica','',8);
    // $pdf->SetY(262);
    // $pdf->SetX(10);
    // $pdf->MultiCell(190,8,utf8_decode("TVA non applicable, article 293B du code général des impôts."),0,'C');
    

}