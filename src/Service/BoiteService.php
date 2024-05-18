<?php

namespace App\Service;

use App\Entity\Boite;
use DateTimeImmutable;
use League\Csv\Reader;
use League\Csv\Statement;
use App\Repository\BoiteRepository;
use App\Repository\NumbersOfPlayersRepository;
use App\Repository\UserRepository;
use App\Service\UtilitiesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BoiteService
{
    public function __construct(
        private BoiteRepository $boiteRepository,
        private EntityManagerInterface $em,
        private UserRepository $userRepository,
        private UtilitiesService $utilitiesService,
        private NumbersOfPlayersRepository $numbersOfPlayersRepository
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
        unset($boites);
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
        unset($boites);
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
        unset($boites);

        $io->progressFinish();
        $io->success('Importation 3/3 terminée');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileCatalogue1_3()
    {
        $csvCatalogue = Reader::createFromPath('%kernel.root.dir%/../import/_table_catalogue.csv','r');
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
        $csvCatalogue = Reader::createFromPath('%kernel.root.dir%/../import/_table_catalogue.csv','r');
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
        $csvCatalogue = Reader::createFromPath('%kernel.root.dir%/../import/_table_catalogue.csv','r');
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
        $boite = $this->boiteRepository->findOneBy(['rvj2id' => $arrayBoite['idCatalogue']]);

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
        if($arrayBoite['annee'] == "Année inconnue"){
            $year = NULL;
        }else{
            $year = (int) $arrayBoite['annee'];
        }

        $this->saveImageOnServeur($arrayBoite['urlNom'],$arrayBoite['idCatalogue'], $arrayBoite['imageBlob']);

        $boite->setName($arrayBoite['nom'])
            ->setIniteditor($arrayBoite['editeur'])
            ->setYear($year)
            ->setSlug($arrayBoite['urlNom'])
            ->setIsDeliverable($arrayBoite['isLivrable'])
            ->setIsOccasion($arrayBoite['isComplet'])
            ->setWeigth($this->nullTo0($arrayBoite['poidBoite']))
            ->setAge((int) $arrayBoite['age'])
            ->setPlayersMin($this->numbersOfPlayersRepository->findOneBy(['keyword' => (int) $arrayBoite['nbrJoueurs']]) ?? $this->numbersOfPlayersRepository->findOneBy(['name' => 'A définir']))
            ->setHtPrice($this->utilitiesService->stringToNull($arrayBoite['prix_HT']))
            ->setCreatedBy($this->userRepository->findOneBy(['nickname' => $arrayBoite['createur']]))
            ->setIsDeee($this->nullToBoolean($arrayBoite['deee']))
            ->setCreatedAt(new DateTimeImmutable($arrayBoite['created_at']))
            ->setIsOnLine($isV3)
            ->setPlayersMax($this->numbersOfPlayersRepository->findOneBy(['keyword' => (int) $arrayBoite['nbrJoueurs'] +1]) ?? $this->numbersOfPlayersRepository->findOneBy(['name' => 'A définir']))
            ->setImage($this->constructImagePath($arrayBoite['urlNom'], $arrayBoite['idCatalogue']));
        $boite->setRvj2id($arrayBoite['idCatalogue'])->setUpdatedAt(new DateTimeImmutable('now'));


        return $boite;
    }

    private function nullToBoolean($value)
    {
        
        if($value == "NULL"){
            $value = 0;
        }else{
            $value = 1;
        }

        return $value;
    }

    private function nullTo0($value)
    {
        
        if($value == "NULL"){
            $value = 0;
        }

        return $value;
    }

    public function saveImageOnServeur($slug, $uniqueName,$imageBlob)
    {

        if (!file_exists($this->pathForImagesBoites())) {
            mkdir($this->pathForImagesBoites(), 0777, true);
        }

        if(!empty($imageBlob)){

            $save_path = $this->pathForImagesBoites().$this->constructImagePath($slug, $uniqueName);

            $im = imagecreatefromstring(base64_decode($imageBlob));
            imagepng($im,$save_path);
            imagedestroy($im);
            
        }

    }

    public function constructImagePath($slug,$unique_id)
    {

        return $slug.'_'.$unique_id.'.png';
    }

    public function pathForImagesBoites()
    {

        return './public/uploads/images/boites/';
    }

    public function importPieces(SymfonyStyle $io): void
    {
        $io->title('Importation des pieces dans les boites');

        $pieces = $this->readCsvFilePieces();
        
        $io->progressStart(count($pieces));

        foreach($pieces as $arrayPiece){
            $io->progressAdvance();
            $this->createOrUpdateContentBoite($arrayPiece);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation terminée');
    }

    private function readCsvFilePieces(): Reader
    {
        $csvPieces = Reader::createFromPath('%kernel.root.dir%/../import/_table_pieces.csv','r');
        $csvPieces->setHeaderOffset(0);

        return $csvPieces;
    }

    private function createOrUpdateContentBoite(array $arrayPiece): void
    {

        $boite = $this->boiteRepository->findOneBy(['rvj2id' => $arrayPiece['idJeu']]);

        if($boite){
            $boite->setContent($arrayPiece['contenu_total'])
            ->setContentMessage($this->utilitiesService->stringToNull($arrayPiece['message']));
            $this->em->persist($boite);
        }

    }

    public function createUndefinedBoite($io)
    {
        $io->title('Création / mise à jour boite virtuelle');

        $boite = $this->boiteRepository->findOneBy(['name' => 'BOITE SUPPRIMEE']); //! ne pas changer intitulé

        if(!$boite){
            $boite = new Boite();
        }
        
        //on rentre la boite
        $boite->setName('BOITE SUPPRIMEE')
            ->setIniteditor('EDITEUR SUPPRIMER')
            ->setYear(2100)
            ->setSlug('boite-supprimee')
            ->setIsDeliverable(false)
            ->setIsOccasion(false)
            ->setWeigth(0)
            ->setAge((int) 0)
            ->setPlayersMin($this->numbersOfPlayersRepository->findOneBy(['name' => 'A définir']))
            ->setHtPrice(0)
            ->setCreatedBy($this->userRepository->findOneBy(['nickname' => 'Je Développe']))
            ->setIsDeee(false)
            ->setCreatedAt(new DateTimeImmutable('now'))
            ->setIsOnLine(false)
            ->setImage('aucune image');

        $boite->setUpdatedAt(new DateTimeImmutable('now'))
            ->setCreatedBy($this->userRepository->findOneBy(['nickname' => 'Je Développe']));

        $this->em->persist($boite);
        $this->em->flush();

        $io->success('Terminée');
    }
}