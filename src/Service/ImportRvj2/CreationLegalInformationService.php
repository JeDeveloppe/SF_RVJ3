<?php

namespace App\Service\ImportRvj2;

use App\Entity\LegalInformation;
use App\Entity\Tax;
use App\Repository\CountryRepository;
use App\Repository\LegalInformationRepository;
use App\Repository\TaxRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreationLegalInformationService
{
    public function __construct(
        private TaxRepository $taxRepository,
        private LegalInformationRepository $legalInformationRepository,
        private EntityManagerInterface $manager,
        private CountryRepository $countryRepository,
        private UserRepository $userRepository
        ){
    }

    public function creationLegalInformation(SymfonyStyle $io): void
    {
        $tax = $this->taxRepository->findOneBy(['value' => 0]);

        if(!$tax){
            $tax = new Tax();
        }
        
        $tax->setValue(0);
        $this->manager->persist($tax);
        $this->manager->flush($tax);
        
        //on vérifié si pn a déjà créer l'administrateur spécial
        $legal = $this->legalInformationRepository->findOneBy(['companyName' => 'REFAITES VOS JEUX']);

        if(!$legal){
            $legal = new LegalInformation();
        }

            $io->title('Création / mise à jour des informations légales');

            $legal
                ->setCompanyName('REFAITES VOS JEUX')
                ->setStreetCompany('24 rue froide')
                ->setPostalCodeCompany(14980)
                ->setCityCompany('ROTS')
                ->setPublicationManagerFirstName('Antoine')
                ->setPublicationManagerLastName('GALLÉE')
                ->setSiretCompany('88847646200013')
                ->setEmailCompany('contact@refaitesvosjeux.fr')
                ->setFullUrlCompany('http://www.refaitesvosjeux.fr')
                ->setCountryCompany($this->countryRepository->findOneBy(['isocode' => 'FR']))
                ->setHostName('IONOS SARL')
                ->setHostStreet('7 place de la gare')
                ->setHostPostalCode(57200)
                ->setHostCity('SARREGUEMINES')
                ->setHostPhone('09.70.80.89.11')
                ->setWebmasterCompanyName('Je-Développe')
                ->setWebmasterFistName('René')
                ->setWebmasterLastName('WETTA')
                ->setTax($this->taxRepository->findOneBy(['value' => 0]))
                ->setIsOnline(true)
                ->setUpdatedBy($this->userRepository->findOneBy(['email' => $_ENV['ADMIN_EMAIL']]))
                ->setUpdatedAt(new DateTimeImmutable('now'));
    
            $this->manager->persist($legal);
            $this->manager->flush();

            $io->success('Importation terminée');

    }

}