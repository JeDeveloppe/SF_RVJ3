<?php

namespace App\Service\ImportRvj2;

use App\Entity\Partenaire;
use App\Entity\Partner;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use League\Csv\Reader;
use App\Repository\PartenaireRepository;
use App\Repository\PartnerRepository;
use App\Repository\PaysRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportPartenairesService
{
    public function __construct(
        private EntityManagerInterface $em,
        private CityRepository $cityRepository,
        private CountryRepository $countryRepository,
        private PartnerRepository $partnerRepository
        ){
    }

    public function importPartenaires(SymfonyStyle $io): void
    {
        $io->title('Importation des partenaires');

        $partenaires = $this->readCsvFilePartenaire();
        
        $io->progressStart(count($partenaires));

        foreach($partenaires as $arrayPartenaire){
            $io->progressAdvance();
            $partenaire = $this->createOrUpdatePartner($arrayPartenaire);
            $this->em->persist($partenaire);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation terminée');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFilePartenaire(): Reader
    {
        $csvCatalogue = Reader::createFromPath('%kernel.root.dir%/../import/partenaires.csv','r');
        $csvCatalogue->setHeaderOffset(0);

        return $csvCatalogue;
    }

    private function createOrUpdatePartner(array $arrayPartenaire): Partner
    {
        $partenaire = $this->partnerRepository->findOneBy(['id' => $arrayPartenaire['idPartenaire']]);

        if(!$partenaire){
            $partenaire = new Partner();
        }

        if($arrayPartenaire['complet'] == 1){
            $complet = true;
        }else{
            $complet = false;
        }
        if($arrayPartenaire['detachee'] == 1){
            $detachee = true;
        }else{
            $detachee = false;
        }
        if($arrayPartenaire['isActif'] == 1){
            $online = true;
        }else{
            $online = false;
        }


        $partenaire->setName($arrayPartenaire['nom'])
                ->setDescription($arrayPartenaire['description'])
                ->setCollect($arrayPartenaire['collecte'])
                ->setSells($arrayPartenaire['vend'])
                ->setIsAcceptDonations($arrayPartenaire['don'])
                ->setFullUrl($arrayPartenaire['url'])
                ->setImageBlob($arrayPartenaire['image'])
                ->setIsSellsSpareParts($detachee)
                ->setIsSellFullGames($complet)
                ->setIsWebShop($arrayPartenaire['ecommerce'])
                ->setIsOnLine($online)
                ->setIsDisplayOnCatalogueWhenSearchIsNull(true)
                ->setCity($this->cityRepository->findOneBy(['rvj2id' => $arrayPartenaire['id_villes_free']]) ?? $this->cityRepository->findOneBy(['rvj2id' => 1]));

        return $partenaire;

    }
}