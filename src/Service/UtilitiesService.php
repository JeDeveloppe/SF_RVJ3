<?php

namespace App\Service;

use App\Entity\Panier;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class UtilitiesService
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

    public function generateRandomString($length = 250, $characters = '0123456789abcdefghijklmnopqrstuvwxyz@!_ABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $charactersLength = strlen($characters);
        $randomString = "";
        for($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength-1)];
        }
        return $randomString;
    }

    public function generateRandomHtmlColor($length = 6, $characters = '0123456789')
    {
        $charactersLength = strlen($characters);
        $randomString = "";
        for($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength-1)];
        }
        return $randomString;
    }

    public function calculTauxTva($taux)
    {

        return ($taux + 100) / 100;

    }

    public function prixTtcToCentsHt($ttc,$taux)
    {

        $tauxTva = $this->calculTauxTva($taux);

        $ht = round($ttc * 100 / $tauxTva,2);

        return $ht;
    }

    public function stringToNull($value)
    {

        if($value == 'NULL' ){
            $value = NULL;
        }

        return $value;
    }

    public function htToTTC($ht,$tax)
    {
        return
        $ht * (1 + ($tax / 100));
    }

    public function totauxByPanierGroup($items)
    {

        $totaux = [];
        $price = 0;
        $weigth = 0;


        foreach($items as $item){

            //en fonction du calcul voulu des articles ou occasions
            if(!empty($item->getOccasion())){
                $weigth += $item->getOccasion()->getBoite()->getWeigth(); 

            }else{
                
                $weigth += $item->getItem()->getWeigth() * $item->getQte();  
            }

            $price += $item->getPriceWithoutTax();
        }
        

        $totaux['price'] = $price;
        $totaux['weigth'] = $weigth;

        return $totaux;
    }

    public function totauxItemsImportV2($items)
    {

        $totaux = [];
        $price = 0;
        $weigth = 0;

        foreach($items as $item){

            if(is_null($item->getBoite())){
                
                $boiteWeigth = 0;

            }else{

                $boiteWeigth = $item->getBoite()->getWeigth();
            }

            $weigth += $boiteWeigth * $item->getQuantity();  
            $price += $item->getPriceExcludingTax();
        }

        $totaux['price'] = $price;
        $totaux['weigth'] = $weigth;

        return $totaux;
    }

    public function totauxOccasionsImportV2($items)
    {

        $totaux = [];
        $price = 0;
        $weigth = 0;

        foreach($items as $item){

            $weigth += $item->getOccasion()->getBoite()->getWeigth() * $item->getQuantity();  
            $price += $item->getPriceExcludingTax();
        }

        $totaux['price'] = $price;
        $totaux['weigth'] = $weigth;

        return $totaux;
    }

    public function totauxBoitesImportV2($items)
    {

        $totaux = [];
        $price = 0;
        $weigth = 0;

        foreach($items as $item){

            $weigth += $item->getBoite()->getWeigth() * $item->getQuantity();  
            $price += $item->getPriceExcludingTax();
        }

        $totaux['price'] = $price;
        $totaux['weigth'] = $weigth;

        return $totaux;
    }

    public function easyAdminLogicWhenBilling(RequestStack $requestStack, $repository)
    {
        //?edition logic
        $id = $requestStack->getCurrentRequest()->get('entityId');
        if($id){
            $occasion = $repository->find($id);
            if($occasion->getIsOnline() == false){
                $disabledAfterBilling = true;
            }else{
                $disabledAfterBilling = false;
            }
            $disabled = true;
        }else{
            $disabled = false;
            $disabledAfterBilling = false;
        }

        return [$disabled,$disabledAfterBilling];
    }

    public function generateAccountNumber(int $id)
    {

        $accountTag = 'C';

        $accountTagLength = 5;
        $idLength = strlen($id);
        $zeros = str_repeat("0", $accountTagLength - $idLength);

        return $accountTag.$zeros.$id;

    }

    public function countNumberOfProductsInSessionCart(array $paniersInSession)
    {

        $count = 0;
        foreach($paniersInSession as $panier) {
            $count += count($panier);
        }

        return $count;
    }
}