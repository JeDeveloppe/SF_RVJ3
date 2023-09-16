<?php

namespace App\Service\ImportRvj2;

use App\Entity\Address;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Adresse;
use App\Entity\ShippingMethod;
use App\Repository\AddressRepository;
use App\Repository\PaysRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use App\Repository\AdresseRepository;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\DocumentRepository;
use App\Repository\ShippingMethodRepository;
use App\Service\DocumentService;
use App\Service\Utilities;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreationUndefinedAdminAndAdresseService
{
    public function __construct(
        private CountryRepository $countryRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $manager,
        private UserPasswordHasherInterface $userPasswordHasher,
        private Utilities $utilities,
        private AddressRepository $addressRepository,
        private CityRepository $cityRepository,
        private ShippingMethodRepository $shippingMethodRepository
        ){
    }

    public function creationAdminAdresseAndShippingMethod(SymfonyStyle $io): void
    {
        $io->title('Création / mise à jour des modes de livraison');

        $shippingMethods = [];
        $shippingMethods[] = [
            'name' => 'MONDIAL RELAY',
            'actif' => false
        ];
        $shippingMethods[] = [
            'name' => 'POSTE',
            'actif' => true
        ];
        $shippingMethods[] = [
            'name' => 'COLISSIMO',
            'actif' => false
        ];
        $shippingMethods[] = [
            'name' => 'INDEFINI',
            'actif' => false
        ];
        $shippingMethods[] = [
            'name' => 'RETRAIT A LA COOP 100%',
            'actif' => true
        ];

        foreach($shippingMethods as $shippingMethodArray){
            $shipping = $this->shippingMethodRepository->findOneBy(['name' => $shippingMethodArray['name']]);

            if(!$shipping){
                $shipping = new ShippingMethod();
            }

            $shipping->setName($shippingMethodArray['name'])->setIsActivedInCart($shippingMethodArray['actif']);
            $this->manager->persist($shipping);
        }
        $this->manager->flush();
        
        
        //on vérifié si pn a déjà créer l'administrateur spécial
        $user = $this->userRepository->findOneBy(['email' => $_ENV['UNDEFINED_USER_EMAIL']]);

        if(!$user){
            $user = new User();
        }

        $io->title('Création / mise à jour de l\'UNDEFINED_USER');

        $random = $this->utilities->generateRandomString(25);

            $user->setCreatedAt(new DateTimeImmutable('now'))
            ->setLastvisite(new DateTimeImmutable('now'))
            ->setEmail($_ENV['UNDEFINED_USER_EMAIL'])
            ->setRoles(['ROLE_USER'])
            ->setNickname('Undefined_user')
            ->setPhone(0000000000)
            ->setCountry($this->countryRepository->findOneBy(['isocode' => 'FR']))
            ->setPassword(
                $this->userPasswordHasher->hashPassword(
                        $user,
                        $random
                    )
                );

        $this->manager->persist($user);
        $this->manager->flush();


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

        $this->manager->persist($address);
        $this->manager->flush();

        $io->success('Terminée');

    }

}