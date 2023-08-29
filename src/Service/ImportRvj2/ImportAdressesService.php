<?php

namespace App\Service\ImportRvj2;

use App\Entity\Address;
use App\Repository\AddressRepository;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use League\Csv\Reader;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportAdressesService
{
    public function __construct(
        private AddressRepository $addressRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private CountryRepository $countryRepository,
        private CityRepository $cityRepository
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
        $ville = $this->cityRepository->findOneBy(['postalcode' => $arrayClient['cpFacturation'], 'name' => $arrayClient['villeFacturation']]);

        if(!is_null($ville)){

            $adresse = $this->addressRepository->findOneBy(['rvj2id' => $arrayClient['idClient'], 'isFacturation' => true]);

            if(!$adresse){
                $adresse = new Address();
            }

            if($arrayClient['organismeFacturation'] == "NULL"){
                $organismeFacturation = null;
            }else{
                $organismeFacturation = $arrayClient['organismeFacturation'];
            }

            $adresse->setIsFacturation(true)
                    ->setLastName($arrayClient['nomFacturation'])
                    ->setFirstName($arrayClient['prenomFacturation'])
                    ->setStreet($arrayClient['adresseFacturation'])
                    ->setOrganization($organismeFacturation)
                    ->setUser($this->userRepository->findOneBy(['rvj2id' => $arrayClient['idClient']]))
                    ->setCity($ville)
                    ->setRvj2id($arrayClient['idClient']);
            $this->em->persist($adresse);
        }
    }

    private function createOrUpdateAdressesLivraison(array $arrayClient)
    {

        $ville = $this->cityRepository->findOneBy(['postalcode' => $arrayClient['cpLivraison'], 'name' => $arrayClient['villeLivraison']]);

        if(!is_null($ville)){
            $adresse = $this->addressRepository->findOneBy(['rvj2id' => $arrayClient['idClient'], 'isFacturation' => false]);

            if(!$adresse){
                $adresse = new Address();
            }

            if($arrayClient['organismeLivraison'] == "NULL"){
                $organismeLivraison = null;
            }else{
                $organismeLivraison = $arrayClient['organismeLivraison'];
            }

            $adresse->setIsFacturation(false)
                    ->setLastName($arrayClient['nomLivraison'])
                    ->setFirstName($arrayClient['prenomLivraison'])
                    ->setStreet($arrayClient['adresseLivraison'])
                    ->setOrganization($organismeLivraison)
                    ->setUser($this->userRepository->findOneBy(['rvj2id' => $arrayClient['idClient']]))
                    ->setCity($ville)
                    ->setRvj2id($arrayClient['idClient']);
            $this->em->persist($adresse);
        }
    }
}