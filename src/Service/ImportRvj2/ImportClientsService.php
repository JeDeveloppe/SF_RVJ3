<?php

namespace App\Service\ImportRvj2;

use App\Entity\User;
use League\Csv\Reader;
use App\Service\Utilities;
use App\Repository\UserRepository;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportClientsService
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private CountryRepository $countryRepository,
        private Utilities $utilities
        ){
    }

    public function importClients(SymfonyStyle $io): void
    {
        $io->title('Importation des clients');

        $clients = $this->readCsvFileClients();
        
        $io->progressStart(count($clients));

        foreach($clients as $arrayClient){
            $io->progressAdvance();
            $client = $this->createOrUpdateClient($arrayClient);
            $this->em->persist($client);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation terminée');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileClients(): Reader
    {
        $csvClients = Reader::createFromPath('%kernel.root.dir%/../import/clients.csv','r');
        $csvClients->setHeaderOffset(0);

        return $csvClients;
    }

    private function createOrUpdateClient(array $arrayClient): User
    {
        $client = $this->userRepository->findOneBy(['email' => $arrayClient['email']]);

        if(!$client){
            $client = new User();
        }

        if($arrayClient['pseudo'] == "NULL"){
            $pseudo = null;
        }else{
            $pseudo = $arrayClient['pseudo'];
        }

        switch($arrayClient['userLevel']){
            case 4:
                $role = ['ROLE_ADMIN'];
                break;
            case 5:
                $role = ['ROLE_SUPER_ADMIN'];
                break;
            default:
                $role = ['ROLE_USER'];
        };
        

        $client->setEmail($arrayClient['email'])
                ->setRvj2Id($arrayClient['idClient'])
                ->setPassword($arrayClient['password'])
                ->setRoles($role)
                ->setNickname($pseudo)
                ->setPhone($arrayClient['telephone'])
                ->setMembership($this->utilities->getDateTimeImmutableFromTimestamp($arrayClient['isAssociation']))
                ->setCountry($this->countryRepository->findOneBy(['isocode' => $arrayClient['paysFacturation']]) ?? $this->countryRepository->findOneBy(['isocode' => 'INC']) );

                if($arrayClient['timeInscription'] != 0){
                    $time = $arrayClient['timeInscription'];
                    $client->setCreatedAt($this->utilities->getDateTimeImmutableFromTimestamp($time));
                }

                $time = $arrayClient['lastVisite'];
                $client->setLastvisite($this->utilities->getDateTimeImmutableFromTimestamp($time));


        return $client;
    }
}