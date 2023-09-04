<?php

namespace App\Service;

use App\Repository\ConfigurationRepository;
use App\Repository\InformationsLegalesRepository;
use DateTimeImmutable;

class Utilities
{
    public function getDateTimeImmutableFromTimestamp($timestamp)
    {
        $tps = (int) $timestamp;
        $date = new DateTimeImmutable();

        if($timestamp !== null){
            return $date->setTimestamp($tps);
        }else{
            return null;
        }
    }

    public function generateRandomString($length = 250, $characters = '0123456789abcdefghijklmnopqrstuvwxyz@!_ABCDEFGHIJKLMNOPQRSTUVWXYZ'){
        $charactersLength = strlen($characters);
        $randomString = "";
        for($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength-1)];
        }
        return $randomString;
    }

    public function calculTauxTva($taux){

        return ($taux + 100) / 100;

    }

    public function prixTtcToCentsHt($ttc,$taux){

        $tauxTva = $this->calculTauxTva($taux);

        $ht = round($ttc * 100 / $tauxTva,2);

        return $ht;
    }

    public function stringToNull($value){

        if($value == 'NULL' ){
            $value = NULL;
        }

        return $value;
    }
}