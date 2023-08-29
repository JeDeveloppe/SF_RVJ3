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

    public function calculTauxTva($taux){

        return ($taux + 100) / 100;

    }

    public function prixTtcToCentsHt($ttc,$taux){

        $tauxTva = $this->calculTauxTva($taux);

        $ht = round($ttc * 100 / $tauxTva,2);

        return $ht;
    }
}