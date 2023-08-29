<?php

namespace App\Service\ImportRvj2;

use DateTimeImmutable;
use League\Csv\Reader;
use App\Entity\DocumentLignes;
use App\Repository\PaysRepository;
use App\Repository\UserRepository;
use App\Repository\BoiteRepository;
use App\Repository\DocumentLignesRepository;
use App\Repository\DocumentRepository;
use App\Repository\OccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MethodeEnvoiRepository;
use App\Repository\InformationsLegalesRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportDocumentsLignesService
{
    public function __construct(
        private EntityManagerInterface $em,
        private DocumentRepository $documentRepository,
        private PaysRepository $paysRepository,
        private MethodeEnvoiRepository $methodeEnvoiRepository,
        private InformationsLegalesRepository $informationsLegalesRepository,
        private UserRepository $userRepository,
        private OccasionRepository $occasionRepository,
        private BoiteRepository $boiteRepository,
        private DocumentLignesRepository $documentLignesRepository
        ){
    }

    //importation des lignes de documents
    public function importDocumentsLigneBoites(SymfonyStyle $io): void
    {
        $io->title('Importation des lignes "boite"');

        //on vide la table
        //CHERCHER TRUNCABLE
        $lignes = $this->documentLignesRepository->findAll();
        foreach($lignes as $ligne){
            $this->em->remove($ligne);
        }
        $this->em->flush();

        //puis on inclu les lignes
        $docs = $this->readCsvFileDocumentsLignesBoite();
        
        $io->progressStart(count($docs));

        foreach($docs as $arrayDoc){
            $io->progressAdvance();
            $ligne = $this->createOrUpdateDocumentLigneBoite($arrayDoc);
            $this->em->persist($ligne);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation terminée');
    }

    //importation des lignes occasion
    public function importDocumentsLigneOccasion(SymfonyStyle $io): void
    {
        $io->title('Importation des lignes "occasion"');

        $docs = $this->readCsvFileDocumentsLignesOccasion();
        
        $io->progressStart(count($docs));

        foreach($docs as $arrayDoc){
            $io->progressAdvance();
            $docLigne= $this->createOrUpdateDocumentLigneOccasion($arrayDoc);
            $this->em->persist($docLigne);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation terminée');
    }

    private function readCsvFileDocumentsLignesBoite(): Reader
    {
        $csvDocuments = Reader::createFromPath('%kernel.root.dir%/../import/documents_lignes.csv','r');
        $csvDocuments->setHeaderOffset(0);

        return $csvDocuments;
    }

    private function readCsvFileDocumentsLignesOccasion(): Reader
    {
        $csvDocuments = Reader::createFromPath('%kernel.root.dir%/../import/documents_lignes_achats.csv','r');
        $csvDocuments->setHeaderOffset(0);

        return $csvDocuments;
    }

    //lignes des boite pour chaque document
    private function createOrUpdateDocumentLigneBoite(array $arrayDoc): DocumentLignes
    {
    
        //la TABLE a était vidée avant
        $docLigne = new DocumentLignes();

        // "idDocLigne","idDocument","idJeu","question","reponse","prix"

        $docLigne->setDocument($this->documentRepository->findOneBy(['rvj2Id' => $arrayDoc['idDocument']]))
        ->setBoite($this->boiteRepository->findOneBy(['rvj2Id' => $arrayDoc['idJeu']]))
        ->setMessage($arrayDoc['question'])
        ->setReponse($arrayDoc['reponse'])
        ->setPrixVente($arrayDoc['prix']);

        return $docLigne;
    }

    //lignes des boite pour chaque document
    private function createOrUpdateDocumentLigneOccasion(array $arrayDoc): DocumentLignes
    {
        //TABLE DEJA VIDER AVANT
        
        $docLigne = new DocumentLignes();

        // "idDocLigne","idDocument","idJeu","question","reponse","prix"

        $docLigne->setDocument($this->documentRepository->findOneBy(['rvj2Id' => $arrayDoc['idDocument']]))
        ->setOccasion($this->occasionRepository->findOneBy(['rvj2Id' => $arrayDoc['idJeuComplet']]))
        ->setPrixVente($arrayDoc['prix']);

        return $docLigne;
    }

}