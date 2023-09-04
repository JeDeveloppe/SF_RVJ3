<?php

namespace App\Service\ImportRvj2;

use League\Csv\Reader;
use App\Repository\BoiteRepository;
use App\Service\Utilities;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportPiecesService
{
    public function __construct(
        private EntityManagerInterface $em,
        private BoiteRepository $boiteRepository,
        private Utilities $utilities
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

        $boite = $this->boiteRepository->findOneBy(['rvj2id' => $arrayPiece['idJeu']]);

        if($boite){
            $boite->setContent($arrayPiece['contenu_total'])
            ->setContentMessage($this->utilities->stringToNull($arrayPiece['message']));
            $this->em->persist($boite);
        }

    }

}