<?php

namespace App\Service;


class AdresseService
{
    public function constructAdresseForSaveInDatabase($adresse)
    {

        return
        $adresse->getOrganization().'<br/>'
        .$adresse->getlastname().'<br/>'
        .$adresse->getFirstname().'<br/>'
        .$adresse->getStreet().'<br/>'
        .$adresse->getCity().'<br/>'
        .$adresse->getCity()->getCountry()->getIsocode();
        
    }
}