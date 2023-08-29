<?php

namespace App\Service\ImportRvj2;

use App\Entity\Adresse;
use App\Repository\AdresseRepository;
use League\Csv\Reader;
use App\Repository\UserRepository;
use App\Repository\PaysRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportAdressesService
{
    public function __construct(
        private AdresseRepository $adresseRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private PaysRepository $paysRepository,
        private VilleRepository $villeRepository
        ){
    }

    public function importAdresses(SymfonyStyle $io): void
    {
        $io->title('Importation des adresses de facturation');
        $clients = $this->readCsvFileClients();
        $io->progressStart(count($clients));

        foreach($clients as $arrayClient){
            $io->progressAdvance();
            $this->createOrUpdateAdressesFacturation($arrayClient);
        }

        $this->em->flush();
        $io->progressFinish();
        $io->success('Importation terminée');

        //ON FAIT LES ADRESSES DE LIVRAISON
        $io->title('Importation des adresses de livraison');
        $clients = $this->readCsvFileClients();
        $io->progressStart(count($clients));

        foreach($clients as $arrayClient){
            $io->progressAdvance();
            $this->createOrUpdateAdressesLivraison($arrayClient);
        }

        $this->em->flush();
        $io->progressFinish();
        $io->success('Importation des adresses terminée');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileClients(): Reader
    {
        $csvClients = Reader::createFromPath('%kernel.root.dir%/../import/clients.csv','r');
        $csvClients->setHeaderOffset(0);

        return $csvClients;
    }

    private function createOrUpdateAdressesFacturation(array $arrayClient)
    {

        //on regarde si la ville existe
        $ville = $this->villeRepository->findOneBy(['villeCodePostal' => $arrayClient['cpFacturation'], 'villeNom' => $arrayClient['villeFacturation']]);

        if(!is_null($ville)){
            $adresse = $this->adresseRepository->findOneBy(['token' => $arrayClient['idUser']]);

            if(!$adresse){
                $adresse = new Adresse();
            }

            if($arrayClient['organismeFacturation'] == "NULL"){
                $organismeFacturation = null;
            }else{
                $organismeFacturation = $arrayClient['organismeFacturation'];
            }

            $adresse->setIsFacturation(true)
                    ->setFirstName($arrayClient['nomFacturation'])
                    ->setLastName($arrayClient['prenomFacturation'])
                    ->setAdresse($arrayClient['adresseFacturation'])
                    ->setOrganisation($organismeFacturation)
                    ->setUser($this->userRepository->findOneBy(['token' => $arrayClient['idUser']]))
                    ->setToken($arrayClient['idUser'])
                    ->setVille($ville);
            $this->em->persist($adresse);
        }
    }

    private function createOrUpdateAdressesLivraison(array $arrayClient)
    {

        $ville = $this->villeRepository->findOneBy(['villeCodePostal' => $arrayClient['cpLivraison'], 'villeNom' => $arrayClient['villeLivraison']]);

        if(!is_null($ville)){
            $adresse = $this->adresseRepository->findOneBy(['token' => $arrayClient['idUser'], 'isFacturation' => false]);

            if(!$adresse){
                $adresse = new Adresse();
            }

            if($arrayClient['organismeLivraison'] == "NULL"){
                $organismeLivraison = null;
            }else{
                $organismeLivraison = $arrayClient['organismeLivraison'];
            }

            $adresse->setIsFacturation(null)
                    ->setFirstName($arrayClient['nomLivraison'])
                    ->setLastName($arrayClient['prenomLivraison'])
                    ->setAdresse($arrayClient['adresseLivraison'])
                    ->setOrganisation($organismeLivraison)
                    ->setUser($this->userRepository->findOneBy(['token' => $arrayClient['idUser']]))
                    ->setToken($arrayClient['idUser'])
                    ->setVille($ville);
            $this->em->persist($adresse);
        }
    }
}