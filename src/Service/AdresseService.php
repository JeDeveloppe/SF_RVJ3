<?php

namespace App\Service;

use App\Entity\Address;

class AdresseService
{
    public function constructAdresseForSaveInDatabase($adresse)
    {
        $completeAdresse = '';

            if(!is_null($adresse->getOrganization())){
                $completeAdresse .= $adresse->getOrganization().'<br/>';
            }
            
            return $completeAdresse.$adresse->getlastname().' '.$adresse->getFirstname().'<br/>'
            .$adresse->getStreet().'<br/>'
            .$adresse->getCity().'<br/>'
            .$adresse->getCity()->getCountry()->getIsocode();
        
    }
}