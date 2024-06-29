<?php

namespace App\Service;

use League\Csv\Reader;
use App\Entity\Address;
use App\Entity\CollectionPoint;
use App\Repository\AddressRepository;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AdresseService
{

    public function __construct(
        private AddressRepository $addressRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private CountryRepository $countryRepository,
        private CityRepository $cityRepository
        ){
    }
    
    public function constructAdresseForSaveInDatabase($adresse)
    {
        $completeAdresse = '';

            if(!is_null($adresse->getOrganization())){
                $completeAdresse .= $adresse->getOrganization().'<br/>';
            }
            
            return $completeAdresse.$adresse->getlastname().' '.$adresse->getFirstname().'<br/>'
            .$adresse->getStreet().'<br/>'
            .$adresse->getCity().'<br/>'
            .$adresse->getCity()->getCountry()->getIsocode();
        
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
        unset($clients);
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
        unset($clients);
        $io->progressFinish();
        $io->success('Importation des adresses terminée');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileClients(): Reader
    {
        $csvClients = Reader::createFromPath('%kernel.root.dir%/../import/_table_clients.csv','r');
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

    public function createRetredAddress($io)
    {
        $io->title('Création / mise à jour de l\'adresse de retrait');

        $address = $this->addressRepository->findOneBy(['user' => $this->userRepository->findOneBy(['email' => $_ENV['UNDEFINED_USER_EMAIL']])]);

        if(!$address){
            $address = new Address();
        }
        
        //on rentre l'adresse de retrait
        $address->setIsFacturation(true)
            ->setLastName('100%')
            ->setFirstName('Retrait à COOP')
            ->setStreet('33 route de Trouville')
            ->setOrganization(null)
            ->setUser($this->userRepository->findOneBy(['email' => $_ENV['UNDEFINED_USER_EMAIL']]))
            ->setCity($this->cityRepository->findOneBy(['postalcode' => 14000, 'name' => 'CAEN']));

        $this->em->persist($address);
        $this->em->flush();

        $io->success('Terminée');
    }

    //return distance en metre entre 2 points gps
    public function get_distance_from_collectePoint(CollectionPoint $collectionPoint, Address $deliveryAdresse){
        $lat1 = $collectionPoint->getCity()->getLatitude();
        $lng1 = $collectionPoint->getCity()->getLongitude();
        $lat2 = $deliveryAdresse->getCity()->getLatitude();
        $lng2 = $deliveryAdresse->getCity()->getLongitude();
        $earth_radius = 6378137;   // Terre = sphère de 6378km de rayon
        $rlo1 = deg2rad($lng1);
        $rla1 = deg2rad($lat1);
        $rlo2 = deg2rad($lng2);
        $rla2 = deg2rad($lat2);
        $dlo = ($rlo2 - $rlo1) / 2;
        $dla = ($rla2 - $rla1) / 2;
        $a = (sin($dla) * sin($dla)) + cos($rla1) * cos($rla2) * (sin($dlo) * sin($dlo));
        $d = 2 * atan2(sqrt($a), sqrt(1 - $a));

      return ($earth_radius * $d / 1000);
    }
}