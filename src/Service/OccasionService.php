<?php

namespace App\Service;

use App\Form\SearchOccasionInCatalogueType;
use App\Repository\OccasionRepository;
use Symfony\Component\Form\Form;

class OccasionService
{
    public function __construct(
        private SearchOccasionInCatalogueType $searchOccasionInCatalogueType,
        private OccasionRepository $occasionRepository
    )
    {
    }

    public function findOccasionsFromOccasionForm(Form $form)
    {
        //le texte saisie
        $search = $form->get('search')->getData();
        $phrase = str_replace(" ","%",$search);

        //age choisie
        $age = $form->get('age')->getData() ?? 0;

        //nombre de joueurs
        $players = $form->get('playerMin')->getData();
        //si pas de choix du nombre de joueurs
        if(count($players) == 0){
            [$playerChoices,$ageChoices,$lengthChoices] = $this->searchOccasionInCatalogueType->formOccasionChoices();

            foreach($playerChoices as $playerChoice){
                $players[] = $playerChoice;
            }
        }

        $occasionsStepOne = $this->occasionRepository->findOccasionsFromSearchByPhraseAndAge($phrase,$age);

        $occasions = [];

        foreach($players as $player){
            foreach($occasionsStepOne as $occasion){
                if($occasion->getBoite()->getPlayersMin() < $player OR $occasion->getBoite()->getPlayersMax() > $player){
                    $occasions[] = $occasion;
                }
            }
        }

       return array_unique($occasions);
    }
}