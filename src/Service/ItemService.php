<?php

namespace App\Service;

use App\Entity\Boite;
use App\Entity\Item;

class ItemService
{
    public function creationReference(Boite $boite, Item $item){

        $reference = $boite->getId().'-A-'.$item->getId();

        return $reference;
    }
}