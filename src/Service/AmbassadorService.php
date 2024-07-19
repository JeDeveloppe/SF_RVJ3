<?php

namespace App\Service;

use App\Repository\AmbassadorRepository;

class AmbassadorService
{
    public function __construct(
        private AmbassadorRepository $ambassadorRepository
        ){
    }


    public function constructionMapOfFranceWithAmbassadors($baseUrl, array $ambassadors)
    {

        $stores = []; //? toutes les réponses seront dans ce tableau final

        foreach($ambassadors as $ambassador)
        {

            if(is_null($ambassador->getOrganization())){

                $name = '';

            }else{

                $name = $ambassador->getOrganization().' à ';
            }

            $nameAdress = $ambassador->getLastname().' '.$ambassador->getFirstname().'<br/>'.$ambassador->getStreet().'<br/>';

            if(is_null($ambassador->getDescription())){

                $description_detail = '';

            }else{

                $description_detail = '<p style="margin-top:10px; padding:10px; width:100%; text-align:justify;">'.$ambassador->getDescription().'</p>';
            }
            
            if(is_null($ambassador->getFullurl())){

                $url = '';

            }else{

                $url = $ambassador->getFullurl();
            }

            if(is_null($ambassador->getPhone())){

                $phone = '';

            }else{

                $phone = '<i class="fa-solid fa-phone"></i> : '.$ambassador->getPhone().'<br/>';
            }

            if(is_null($ambassador->getEmail())){

                $email = '';

            }else{

                $email = '<i class="fa-solid fa-envelope"></i> : '.$ambassador->getEmail().'<br/>';
            }

            if(is_null($ambassador->getFacebookLink())){

                $facebook = '';

            }else{

                $facebook = '<i class="fa-brands fa-facebook"></i> :<a href="'.$ambassador->getFacebookLink().'">Lien vers Facebook</a><br/>';
            }

            if(is_null($ambassador->getInstagramLink())){

                $instagram = '';

            }else{

                $instagram = '<i class="fa-brands fa-instagram"></i> :<a href="'.$ambassador->getInstagramLink().'">Lien vers Instagram</a><br/>';
            }

            $description = $nameAdress;
            $description .= $description_detail;
            $description .= '<p>';
            $description .= $phone;
            $description .= $email;
            $description .= $facebook;
            $description .= $instagram;
            $description .= '</p>';

            $stores[] = 
            [
                "lat" => $ambassador->getCity()->getLatitude(),
                "lng" => $ambassador->getCity()->getLongitude(),
                "color" => "#000000",
                "name" => $name.$ambassador->getCity()->getName().' ('.$ambassador->getCity()->getDepartment()->getName().')',
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