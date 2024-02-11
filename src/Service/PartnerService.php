<?php

namespace App\Service;

use App\Repository\PartnerRepository;

class PartnerService
{
    public function __construct(
        private PartnerRepository $partnerRepository
        ){
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