<?php

namespace App\Service\ImportRvj2;

use League\Csv\Reader;
use App\Repository\BoiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportPiecesService
{
    public function __construct(
        private BoiteRepository $boiteRepository,
        private EntityManagerInterface $em
        ){
    }

    public function importPieces(SymfonyStyle $io): void
    {
        $io->title('Importation des pieces dans les boites');

        $pieces = $this->readCsvFilePieces();
        
        $io->progressStart(count($pieces));

        foreach($pieces as $arrayPiece){
            $io->progressAdvance();
            $this->createOrUpdateBoite($arrayPiece);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation terminée');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFilePieces(): Reader
    {
        $csvPieces = Reader::createFromPath('%kernel.root.dir%/../import/pieces.csv','r');
        $csvPieces->setHeaderOffset(0);

        return $csvPieces;
    }

    private function createOrUpdateBoite(array $arrayPiece): void
    {

        $boite = $this->boiteRepository->findOneBy(['id' => $arrayPiece['idJeu']]);

        if($boite){
            $boite->setContenu($arrayPiece['contenu_total'])
            ->setMessage($this->stringToNull($arrayPiece['message']));
            $this->em->persist($boite);
        }

    }
    private function stringToNull($value){
        
        if($value == "NULL"){
            $value = NULL;
        }

        return $value;
    }

}