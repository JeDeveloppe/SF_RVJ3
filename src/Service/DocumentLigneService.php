<?php

namespace App\Service;

use League\Csv\Reader;
use App\Entity\DocumentLine;
use App\Service\UtilitiesService;
use App\Entity\DocumentLineTotals;
use App\Repository\UserRepository;
use App\Repository\BoiteRepository;
use App\Repository\DocumentRepository;
use App\Repository\OccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DocumentLineRepository;
use App\Repository\DocumentLineTotalsRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class DocumentLigneService
{
    public function __construct(
        private EntityManagerInterface $em,
        private DocumentRepository $documentRepository,
        private UserRepository $userRepository,
        private OccasionRepository $occasionRepository,
        private BoiteRepository $boiteRepository,
        private DocumentLineRepository $documentLineRepository,
        private UtilitiesService $utilitiesService,
        private DocumentLineTotalsRepository $documentLineTotalsRepository
        ){
    }

    //importation des lines de documents
    public function importDocumentsLigneBoites(SymfonyStyle $io): void
    {
        $io->title('Importation des lines "boite"');

        $lines = $this->readCsvFileDocumentslinesBoite();
        
        $io->progressStart(count($lines));

        foreach($lines as $arrayLines){
            $io->progressAdvance();
            $line = $this->createOrUpdateDocumentLigneBoite($arrayLines);
            $this->em->persist($line);
        }
        $this->em->flush();

        unset($lines);
        $io->progressFinish();
        $io->success('Importation terminée');

    }

    //importation des lines occasion
    public function importDocumentsLigneOccasion(SymfonyStyle $io): void
    {
        $io->title('Importation des lines "occasion"');

        $lines = $this->readCsvFileDocumentslinesOccasion();
        
        $io->progressStart(count($lines));

        foreach($lines as $arrayLines){
            $io->progressAdvance();
            $docLine= $this->createOrUpdateDocumentLigneOccasion($arrayLines);
            $this->em->persist($docLine);
        }

        $this->em->flush();
        unset($lines);

        $io->progressFinish();
        $io->success('Importation terminée');

    }

    private function readCsvFileDocumentslinesBoite(): Reader
    {
        $csvDocuments = Reader::createFromPath('%kernel.root.dir%/../import/_table_documents_lignes.csv','r');
        $csvDocuments->setHeaderOffset(0);

        return $csvDocuments;
    }

    private function readCsvFileDocumentslinesOccasion(): Reader
    {
        $csvDocuments = Reader::createFromPath('%kernel.root.dir%/../import/_table_documents_lignes_achats.csv','r');
        $csvDocuments->setHeaderOffset(0);

        return $csvDocuments;
    }

    //lines des boite pour chaque document
    private function createOrUpdateDocumentLigneBoite(array $arrayLines): DocumentLine
    {
    
        $docLine = $this->documentLineRepository->findOneBy(['rvj2idboite' => $arrayLines['idDocLigne']]);

        if(!$docLine){
            $docLine = new DocumentLine();
        }

        //"idDocLigne","idDocument","idJeu","question","reponse","prix"
        $document = $this->documentRepository->findOneBy(['rvj2id' => $arrayLines['idDocument']]) ?? $this->documentRepository->findOneBy(['rvj2id' => 1]); // sur Antoine pr defaut
        if(!$document){
            dd('Document: '.$arrayLines['idDocument']);
        }

        $boite = $this->boiteRepository->findOneBy(['id' => $arrayLines['idJeu']]) ?? $this->boiteRepository->findOneBy(['name' => 'BOITE SUPPRIMEE']);

        if(!$boite){
            dd('Boite: '.$arrayLines['idJeu']);
        }

        $docLine
            ->setDocument($document)
            ->setBoite($boite)
            ->setOccasion(null)
            ->setQuestion($arrayLines['question'])
            ->setQuantity(1)
            ->setAnswer($arrayLines['reponse'])
            ->setRvj2idboite($arrayLines['idDocLigne'])
            ->setPriceExcludingTax($arrayLines['prix']);

        return $docLine;
    }

    //lines des boite pour chaque document
    private function createOrUpdateDocumentLigneOccasion(array $arrayLines): DocumentLine
    {

        $docLine = $this->documentLineRepository->findOneBy(['rvj2idoccasion' => $arrayLines['idDocLigneAchat']]);

        if(!$docLine){
            $docLine = new DocumentLine();
        }

        //"idDocLigneAchat","idDocument","idJeuComplet","idCatalogue","detailsComplet","qte","prix"

        $document = $this->documentRepository->findOneBy(['rvj2id' => $arrayLines['idDocument']]);
        if(!$document){
            dd('Doc: '.$arrayLines['idDocument']);
        }
        $occasion = $this->occasionRepository->findOneBy(['rvj2id' => $arrayLines['idJeuComplet']]);
        if(!$occasion){
            dd('Occasion: '.$arrayLines['idJeuComplet']);
        }

        $docLine
            ->setDocument($document)
            ->setOccasion($occasion)
            ->setBoite(null)
            ->setQuantity(1)
            ->setRvj2idoccasion($arrayLines['idDocLigneAchat'])
            ->setPriceExcludingTax($arrayLines['prix']);

        return $docLine;
    }

    public function generateDocumentsTotals(SymfonyStyle $io){

        $io->title('Création des totaux de document (v2 => v3)');

        $documents = $this->documentRepository->findAll();

        $io->progressStart(count($documents));

        foreach($documents as $document){

            $itemsFromDocLines = $this->documentLineRepository->findBy(['document' => $document, 'occasion' => null, 'boite' => null]);
            $occasionsFromDocLines = $this->documentLineRepository->findBy(['document' => $document, 'item' => null, 'boite' => null]);
            $boitesFromDocLines = $this->documentLineRepository->findBy(['document' => $document, 'item' => null, 'occasion' => null]);

            $itemsTotals = $this->utilitiesService->totauxItemsImportV2($itemsFromDocLines);
            $occasionsTotals = $this->utilitiesService->totauxOccasionsImportV2($occasionsFromDocLines);
            $boitesTotals = $this->utilitiesService->totauxBoitesImportV2($boitesFromDocLines);

            $docLineTotals = $this->documentLineTotalsRepository->findOneBy(['document' => $document]);

            if(!$docLineTotals){

                $docLineTotals = new DocumentLineTotals();
            }

            $docLineTotals
                ->setDocument($document)
                ->setBoitesWeigth($boitesTotals['weigth'])->setBoitesPriceWithoutTax($boitesTotals['price'])
                ->setItemsPriceWithoutTax($itemsTotals['price'])->setItemsWeigth($itemsTotals['weigth'])
                ->setOccasionsPriceWithoutTax($occasionsTotals['price'])->setOccasionsWeigth($occasionsTotals['weigth'])
                ->setDiscountonpurchase(0)->setDiscountonpurchaseinpurcentage(0)
                ->setVoucherDiscountValueUsed(0);
            $this->em->persist($docLineTotals);

            $io->progressAdvance();

        }

        $this->em->flush();
        $io->progressFinish();
        $io->success('Création terminée');
    }
}