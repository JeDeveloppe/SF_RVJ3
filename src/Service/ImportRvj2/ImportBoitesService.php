<?php

namespace App\Service\ImportRvj2;

use App\Entity\Boite;
use DateTimeImmutable;
use League\Csv\Reader;
use League\Csv\Statement;
use App\Repository\BoiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportBoitesService
{
    public function __construct(
        private BoiteRepository $boiteRepository,
        private EntityManagerInterface $em
        ){
    }

    public function importBoites(SymfonyStyle $io): void
    {
        $io->title('Importation des boites 1/3');
        $boites = $this->readCsvFileCatalogue1_3();
        $io->progressStart(count($boites));
        foreach($boites as $arrayBoite){
            $io->progressAdvance();
            $boite = $this->createOrUpdateBoite($arrayBoite);
            $this->em->persist($boite);
        }
        $this->em->flush();
        $io->progressFinish();
        $io->success('Importation 1/3 terminée');

        $io->title('Importation des boites 2/3');
        $boites = $this->readCsvFileCatalogue2_3();
        $io->progressStart(count($boites));
        foreach($boites as $arrayBoite){
            $io->progressAdvance();
            $boite = $this->createOrUpdateBoite($arrayBoite);
            $this->em->persist($boite);
        }
        $this->em->flush();
        $io->progressFinish();
        $io->success('Importation 2/3 terminée');

        $io->title('Importation des boites 3/3');
        $boites = $this->readCsvFileCatalogue3_3();
        $io->progressStart(count($boites));
        foreach($boites as $arrayBoite){
            $io->progressAdvance();
            $boite = $this->createOrUpdateBoite($arrayBoite);
            $this->em->persist($boite);
        }
        $this->em->flush();
        $io->progressFinish();
        $io->success('Importation 3/3 terminée');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileCatalogue1_3()
    {
        $csvCatalogue = Reader::createFromPath('%kernel.root.dir%/../import/catalogue.csv','r');
        $csvCatalogue->setHeaderOffset(0);

        //Calcul du milieu
        $lastIndex = count($csvCatalogue) - 1; //3
        $divided = $lastIndex / 3;
        $index = floor($divided);

        //on fait un mini tableau avec les données jusqu'au milieu
        $stmt = Statement::create()
        ->offset(0)
        ->limit($index);

        return $stmt->process($csvCatalogue);
    }

    private function readCsvFileCatalogue2_3()
    {
        $csvCatalogue = Reader::createFromPath('%kernel.root.dir%/../import/catalogue.csv','r');
        $csvCatalogue->setHeaderOffset(0);

        //Calcul du milieu
        $lastIndex = count($csvCatalogue) - 1; //3
        $divided = $lastIndex / 3;
        $index = floor($divided);

        //on fait un mini tableau de résultats du milieu à la fin des donnees...
        $stmt = Statement::create()
        ->offset($index)
        ->limit($index * 2);

        return $stmt->process($csvCatalogue);
    }

    private function readCsvFileCatalogue3_3()
    {
        $csvCatalogue = Reader::createFromPath('%kernel.root.dir%/../import/catalogue.csv','r');
        $csvCatalogue->setHeaderOffset(0);

        //Calcul du milieu
        $lastIndex = count($csvCatalogue) - 1; //3
        $divided = $lastIndex / 3;
        $middleIndex = floor($divided);

        //on fait un mini tableau de résultats du milieu à la fin des donnees...
        $stmt = Statement::create()
        ->offset($middleIndex * 2);

        return $stmt->process($csvCatalogue);
    }

    private function createOrUpdateBoite(array $arrayBoite): Boite
    {
        $boite = $this->boiteRepository->findOneBy(['id' => $arrayBoite['idCatalogue']]);

        if(!$boite){
            $boite = new Boite();
        }

        if($arrayBoite['actif'] == 0 || $arrayBoite['actif'] == ""){
            $actif = false;
        }else{
            $actif = true;
        }
        if($arrayBoite['v3'] == "NON" || $arrayBoite['nom'] == ""){
            $isV3 = false;
        }else{
            $isV3 = true;
        }

        $boite->setNom($arrayBoite['nom'])
            ->setEditeur($arrayBoite['editeur'])
            ->setAnnee($arrayBoite['annee'])
            ->setImageblob($arrayBoite['imageBlob'])
            ->setSlug($arrayBoite['urlNom'])
            ->setIsLivrable($arrayBoite['isLivrable'])
            ->setIsComplet($this->stringToNull($arrayBoite['isComplet']))
            ->setPoidBoite($this->stringToNull($arrayBoite['poidBoite']))
            ->setAge($this->stringToNull($arrayBoite['age']))
            ->setNbrJoueurs($this->stringToNull($arrayBoite['nbrJoueurs']))
            ->setPrixHt($this->stringToNull($arrayBoite['prix_HT']))
            ->setCreator($arrayBoite['createur'])
            ->setIsDeee($arrayBoite['deee'])
            ->setCreatedAt(new DateTimeImmutable($arrayBoite['created_at']))
            ->setIsOnLine($actif)
            ->setVenteDirecte($isV3)
            ->setRvj2Id($arrayBoite['idCatalogue']);

        return $boite;
    }

    private function stringToNull($value){
        
        if($value == "NULL"){
            $value = NULL;
        }

        return $value;
    }
}