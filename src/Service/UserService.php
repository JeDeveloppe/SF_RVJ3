<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\AddressRepository;
use App\Repository\CountryRepository;
use App\Repository\DocumentParametreRepository;
use App\Repository\DocumentRepository;
use App\Repository\PartnerRepository;
use DateTimeImmutable;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $userPasswordHasher,
        private UserRepository $userRepository,
        private UtilitiesService $utilitiesService,
        private CountryRepository $countryRepository,
        private DocumentRepository $documentRepository,
        private AddressRepository $addressRepository,
        private PartnerRepository $partnerRepository,
        private DocumentParametreRepository $documentParametreRepository
        ){
    }

    public function initForProd_adminUser($io): void
    {
        $io->title('Création / mise à jour de l\'user ADMIN');

        //on vérifié si on a déjà créé l'admin
        $user = $this->userRepository->findOneBy(['email' => $_ENV['ADMIN_EMAIL']]);

        if(!$user){

            $user = new User();
        }

        $user->setCreatedAt(new DateTimeImmutable('now'))
            ->setLastvisite(new DateTimeImmutable('now'))
            ->setEmail($_ENV['ADMIN_EMAIL'])
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setNickname('Je Développe')
            ->setPhone($_ENV['ADMIN_PHONE'])
            ->setCountry($this->countryRepository->findOneBy(['isocode' => 'FR']))
            ->setPassword(
                $this->userPasswordHasher->hashPassword(
                        $user,
                        $_ENV['ADMIN_PASSWORD']
                    )
                );

        $this->em->persist($user);
        $this->em->flush();

        //on vérifié si on a déjà créé l'admin
        $user = $this->userRepository->findOneBy(['email' => 'client_de_passage@refaitesvosjeux.fr']);

        if(!$user){

            $user = new User();
        }

        $user->setCreatedAt(new DateTimeImmutable('now'))
            ->setLastvisite(new DateTimeImmutable('now'))
            ->setEmail('client_de_passage@refaitesvosjeux.fr')
            ->setRoles(['ROLE_USER'])
            ->setPhone(0000000000)
            ->setCountry($this->countryRepository->findOneBy(['isocode' => 'FR']))
            ->setPassword(
                $this->userPasswordHasher->hashPassword(
                        $user,
                        $this->utilitiesService->generateRandomString(20)
                    )
                );

        $this->em->persist($user);
        $this->em->flush();

        $io->success('Admin créé / mise à jour!');

    }

    public function constructionMapOfFranceWithUserWhoHaveCommanded()
    {

        $donnees = []; //? toutes les réponses seront dans ce tableau final
        $docParams = $this->documentParametreRepository->findOneBy(['isOnline' => true]);
        $now = new DateTimeImmutable('now');
        $year = $now->format('Y');
        //TODO affichage / année en cours / année passée / ou total ?
        $year = null; //null
        
        $documents = $this->documentRepository->findByDocumentWithPaiementInYear($docParams->getBillingTag(), $year);

        $users = [];

        foreach($documents as $document){
            $users[] = $document->getUser();
        }
    
        $count = 0;
        foreach($users as $user){
            if(!is_null($user->getaddresses())){

                $adress = $this->addressRepository->findOneBy(['user' => $user, 'isFacturation' => false]);
                if(!is_null($adress) AND $adress->getCity()->getCountry()->getIsocode() == "FR"){
                    $count += 1;
                    $color = $this->utilitiesService->generateRandomHtmlColor();
                    
                    $places[] = 
                    [
                        "lat" => $adress->getCity()->getLatitude(),
                        "lng" => $adress->getCity()->getLongitude(),
                        "color" => "#".$color,
                        "name" => "MERCI"
                    ];
                }
            }
        }

        $jsonPlaces = json_encode($places, JSON_FORCE_OBJECT); 

        $donnees['places'] = $jsonPlaces;
        $donnees['count'] = $count;
        $donnees['year'] = $year;

        return $donnees;
    }
}