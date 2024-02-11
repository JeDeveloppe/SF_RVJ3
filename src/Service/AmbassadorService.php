<?php

namespace App\Service;

use App\Repository\AmbassadorRepository;
use App\Repository\PartnerRepository;

class AmbassadorService
{
    public function __construct(
        private AmbassadorRepository $ambassadorRepository
        ){
    }


    public function constructionMapOfFranceWithAmbassadors($baseUrl)
    {

        $stores = []; //? toutes les réponses seront dans ce tableau final
        $ambassadors = $this->ambassadorRepository->findAll();


        foreach($ambassadors as $ambassador)
        {

            if(is_null($ambassador->getOrganization())){

                $name = $ambassador->getLastname().' '.$ambassador->getFirstname();

            }else{

                $name = $ambassador->getOrganization();
            }

            if(is_null($ambassador->getFullurl())){

                $url = '';

            }else{

                $url = $ambassador->getFullurl();
            }

            $description = '<p style="margin-top:10px; padding:10px; width:100%; text-align:justify;">'.$ambassador->getDescription().'</p>';
            $description .= '<p>'.$ambassador->getLastname().' '.$ambassador->getFirstname().'<br/>';
            $description .= 'Tél:'.$ambassador->getPhone().'<br/>';
            $description .= '@: '.$ambassador->getEmail().'</p>';

            $stores[] = 
            [
                "lat" => $ambassador->getCity()->getLatitude(),
                "lng" => $ambassador->getCity()->getLongitude(),
                "color" => "#000000",
                "name" => $name.' à '.$ambassador->getCity()->getName().' - '.$ambassador->getCity()->getDepartment()->getName(),
                "description" => $description,
                "url" => $url,
                "size" => 15,
            ];
        }
    

        $jsonStores = json_encode($stores, JSON_FORCE_OBJECT); 

        $donnees = $jsonStores;

        return $donnees;
    }
}