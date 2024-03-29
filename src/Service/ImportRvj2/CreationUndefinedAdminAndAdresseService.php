<?php

namespace App\Service\ImportRvj2;

use App\Entity\Address;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Adresse;
use App\Entity\Boite;
use App\Entity\ShippingMethod;
use App\Repository\AddressRepository;
use App\Repository\PaysRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use App\Repository\AdresseRepository;
use App\Repository\BoiteRepository;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\DocumentRepository;
use App\Repository\NumbersOfPlayersRepository;
use App\Repository\ShippingMethodRepository;
use App\Service\DocumentService;
use App\Service\UtilitiesService;
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
        private UtilitiesService $utilitiesService,
        private AddressRepository $addressRepository,
        private CityRepository $cityRepository,
        private ShippingMethodRepository $shippingMethodRepository,
        private BoiteRepository $boiteRepository,
        private NumbersOfPlayersRepository $numbersOfPlayersRepository
        ){
    }

    public function creationAdminAdresseAndShippingMethod(SymfonyStyle $io): void
    {
        $io->title('Création / mise à jour des modes de livraison');

        $shippingMethods = [];
        $shippingMethods[] = [
            'name' => $_ENV['SHIPPING_METHOD_BY_MONDIAL_RELAY_NAME'],
            'price' => 'PAYANT',
            'actifInCart' => false,
            'forOccasionOnly' => false
        ];
        $shippingMethods[] = [
            'name' => $_ENV['SHIPPING_METHOD_BY_POSTE_NAME'],
            'price' => 'PAYANT',
            'actifInCart' => true,
            'forOccasionOnly' => false
        ];
        $shippingMethods[] = [
            'name' => $_ENV['SHIPPING_METHOD_BY_COLISSIMO_NAME'],
            'price' => 'PAYANT',
            'actifInCart' => false,
            'forOccasionOnly' => false
        ];
        $shippingMethods[] = [
            'name' => $_ENV['SHIPPING_METHOD_BY_INDEFINED'],
            'price' => 'PAYANT',
            'actifInCart' => false,
            'forOccasionOnly' => false
        ];
        $shippingMethods[] = [
            'name' => $_ENV['SHIPPING_METHOD_BY_IN_RVJ_DEPOT_NAME'],
            'price' => 'GRATUIT',
            'actifInCart' => true,
            'forOccasionOnly' => true
        ];
        $shippingMethods[] = [
            'name' => $_ENV['SHIPPING_METHOD_BY_IN_FAIR_NAME'],
            'price' => 'GRATUIT',
            'actifInCart' => false,
            'forOccasionOnly' => true
        ];

        foreach($shippingMethods as $shippingMethodArray){
            $shipping = $this->shippingMethodRepository->findOneBy(['name' => $shippingMethodArray['name']]);

            if(!$shipping){
                $shipping = new ShippingMethod();
            }

            $shipping->setName($shippingMethodArray['name'])->setForOccasionOnly($shippingMethodArray['forOccasionOnly'])->setIsActivedInCart($shippingMethodArray['actifInCart'])->setPrice($shippingMethodArray['price']);
            $this->manager->persist($shipping);
        }
        $this->manager->flush();
        
        
        //on vérifié si pn a déjà créer l'administrateur spécial
        $user = $this->userRepository->findOneBy(['email' => $_ENV['UNDEFINED_USER_EMAIL']]);

        if(!$user){
            $user = new User();
        }

        $io->title('Création / mise à jour de l\'UNDEFINED_USER');

        $random = $this->utilitiesService->generateRandomString(25);

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
        $io->success('Création undéfined user ok');


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


        $io->title('Création / mise à jour boite virtuelle');

        $boite = $this->boiteRepository->findOneBy(['name' => 'BOITE SUPPRIMEE']);

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
            ->setPlayers($this->numbersOfPlayersRepository->findOneBy(['name' => 'A définir']))
            ->setHtPrice(0)
            ->setCreatedBy($this->userRepository->findOneBy(['nickname' => 'Je Développe']))
            ->setIsDeee(false)
            ->setCreatedAt(new DateTimeImmutable('now'))
            ->setIsOnLine(false)
            ->setImage('aucune image');

        $boite->setUpdatedAt(new DateTimeImmutable('now'))
            ->setCreatedBy($this->userRepository->findOneBy(['nickname' => 'Je Développe']));

        $this->manager->persist($boite);
        $this->manager->flush();

        $io->success('Terminée');


        $io->title('Création / mise à jour boite virtuelle');

        $boite = $this->boiteRepository->findOneBy(['name' => 'BOITE SUPPRIMEE']);

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
            ->setPlayers($this->numbersOfPlayersRepository->findOneBy(['name' => 'A définir']))
            ->setHtPrice(0)
            ->setCreatedBy($this->userRepository->findOneBy(['nickname' => 'Je Développe']))
            ->setIsDeee(false)
            ->setCreatedAt(new DateTimeImmutable('now'))
            ->setIsOnLine(false)
            ->setImage('aucune image');

        $boite->setUpdatedAt(new DateTimeImmutable('now'))
            ->setCreatedBy($this->userRepository->findOneBy(['nickname' => 'Je Développe']));

        $this->manager->persist($boite);
        $this->manager->flush();

        $io->success('Terminée');
    }

}