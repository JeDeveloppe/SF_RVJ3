<?php

namespace App\Service\ImportRvj2;

use App\Entity\Country;
use App\Entity\SiteSetting;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SiteSettingRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class SiteSettingsService
{
    public function __construct(
        private SiteSettingRepository $siteSettingRepository,
        private EntityManagerInterface $em
        ){
    }

    public function addSettings(SymfonyStyle $io){

        $io->title('Importation / mise à jour des settings du site');

        $siteSettings = $this->siteSettingRepository->findOneBy([]);

        if(!$siteSettings){
            $siteSettings = new SiteSetting();
        }

        $siteSettings->setBlockEmailSending(true)->setMarquee(null);
        $this->em->persist($siteSettings);
        $this->em->flush();

        $io->success('Terminé !');
    }
}