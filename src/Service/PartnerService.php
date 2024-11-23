<?php

namespace App\Service;

use League\Csv\Reader;
use App\Entity\Partner;
use App\Repository\CityRepository;
use App\Repository\PartnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PartnerService
{
    public function __construct(
        private PartnerRepository $partnerRepository,
        private EntityManagerInterface $em,
        private CityRepository $cityRepository
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

    private function readCsvFilePartenaire(): Reader
    {
        $csvCatalogue = Reader::createFromPath('%kernel.root.dir%/../import/_table_partenaires.csv','r');
        $csvCatalogue->setHeaderOffset(0);

        return $csvCatalogue;
    }

    private function createOrUpdatePartner(array $arrayPartenaire): Partner
    {
        $partenaire = $this->partnerRepository->findOneBy(['rvj2id' => $arrayPartenaire['idPartenaire']]);

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

        $this->saveImageOnServeur($arrayPartenaire['idPartenaire'], $arrayPartenaire['image']);

    
        $partenaire->setName($arrayPartenaire['nom'])
                ->setDescription($arrayPartenaire['description'])
                ->setCollect($arrayPartenaire['collecte'])
                ->setSells($arrayPartenaire['vend'])
                ->setIsAcceptDonations($arrayPartenaire['don'])
                ->setFullUrl($arrayPartenaire['url'])
                ->setImage($this->constructImagePath($arrayPartenaire['idPartenaire']));
        $partenaire
                ->setIsSellsSpareParts($detachee)
                ->setIsSellFullGames($complet)
                ->setIsWebShop($arrayPartenaire['ecommerce'])
                ->setIsOnLine($online)
                ->setIsDisplayOnCatalogueWhenSearchIsNull(true)
                ->setRvj2id($arrayPartenaire['idPartenaire'])
                ->setCity($this->cityRepository->findOneBy(['id' => $arrayPartenaire['id_villes_free']]) ?? $this->cityRepository->findOneBy(['id' => 5022]));

        return $partenaire;

    }

    public function saveImageOnServeur($uniqueName,$imageBlob)
    {

        if (!file_exists($this->pathForImagesPartners())) {
            mkdir($this->pathForImagesPartners(), 0777, true);
        }

        if(!empty($imageBlob)){

            $save_path = $this->pathForImagesPartners().$this->constructImagePath($uniqueName);

            $im = imagecreatefromstring(base64_decode($imageBlob));
            imagepng($im,$save_path);
            imagedestroy($im);
            
        }

    }

    public function constructImagePath($unique_id)
    {

        return 'partner_'.$unique_id.'.png';
    }

    public function pathForImagesPartners()
    {

        return './public/uploads/images/partners/';

    }

    public function constructionMapOfFranceWithPartners($baseUrl)
    {

        $stores = []; //? toutes les réponses seront dans ce tableau final
        $partners = $this->partnerRepository->findBy(['isOnline' => true], ['name' => 'ASC']);
    

        foreach($partners as $partner){
 
            $imgUrl = $baseUrl.'/uploads/images/partners/'.$partner->getImage();
            $stores[] = 
            [
                "lat" => $partner->getCity()->getLatitude(),
                "lng" => $partner->getCity()->getLongitude(),
                "color" => "#000000",
                "name" => $partner->getName().' à '.$partner->getCity()->getName().' ('.$partner->getCity()->getDepartment().')',
                "description" => '<p style="margin-top:10px; width:100%; text-align:center;"><img src="'.$imgUrl.'" style="width: 75px" /></p><p>'.$partner->getDescription().'</p><p>Le service collecte:<br/>'.$partner->getCollect().'</p><p>Le service vend:<br/>'.$partner->getSells().'</p>',
                "url" => $partner->getFullUrl(),
                "size" => 15,
            ];
        }
    

        $jsonStores = json_encode($stores, JSON_FORCE_OBJECT); 

        $donnees = $jsonStores;

        return $donnees;
    }
}