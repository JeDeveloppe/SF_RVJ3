<?php

namespace App\Service;

use App\Entity\Address;

class AdresseService
{
    public function constructAdresseForSaveInDatabase($adresse)
    {
        $completeAdresse = '';

        if($adresse instanceof Address){

            if(!is_null($adresse->getOrganization())){
                $completeAdresse .= $adresse->getOrganization().'<br/>';
            }
            
            return $completeAdresse.$adresse->getlastname().' '.$adresse->getFirstname().'<br/>'
            .$adresse->getStreet().'<br/>'
            .$adresse->getCity().'<br/>'
            .$adresse->getCity()->getCountry()->getIsocode();

        }else{

            return $completeAdresse.$adresse->getName().'<br/>'
            .$adresse->getStreet().'<br/>'
            .$adresse->getCity().'<br/>'
            .$adresse->getCity()->getCountry()->getIsocode();
    
        }
        
    }
}