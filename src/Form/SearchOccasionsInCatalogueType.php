<?php

namespace App\Form;

use App\Repository\DurationOfGameRepository;
use App\Repository\NumbersOfPlayersRepository;
use App\Service\OccasionService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

class SearchOccasionsInCatalogueType extends AbstractType
{
    public function __construct(
        private NumbersOfPlayersRepository $numbersOfPlayersRepository,
        private OccasionService $occasionService,
        private DurationOfGameRepository $durationOfGameRepository
        ){
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $category = $options['category'];

        [$playerChoices,$ageChoices,$durationsChoices] = $this->formOccasionChoices($category);

        $builder
            ->add('search', TextType::class, [
                'label' => 'Je recherche:',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Un jeu, un éditeur...',
                    'class' => 'form-control'
                ]
            ])
            ->add('playerMin', ChoiceType::class, [
                'label' => 'Nombre de joueur(s):',
                'attr' => [
                    'class' => 'form-control'
                ],
                'choices' => $playerChoices,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('duration',  ChoiceType::class, [
                'label' => 'Durée de la partie:',
                'attr' => [
                    'class' => 'form-control'
                ],
                'choices' => $durationsChoices,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('age_start', ChoiceType::class, [
                'label' => 'Âge minimum:',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,
                'choices' => $ageChoices,
                'placeholder' => '- Tous les âges -',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'category' => null
            // Configure your form options here
        ]);
    }

    public function formOccasionChoices($category)
    {
        $playerChoices = [];
        $playersChecked = $this->numbersOfPlayersRepository->findBy(['isInOccasionFormSearch' => true],['orderOfAppearance' => 'ASC']);

        foreach($playersChecked as $playerChecked){
            $playerChoices[$playerChecked->getName()] = $playerChecked->getName();
        }

        $choices = $this->occasionService->returnAgesChoicesAndPageTitle($category);

        $durations = [];
        $durationsChecked = $this->durationOfGameRepository->findBy([],['name' => 'ASC']);
        foreach($durationsChecked as $durationChecked){
            $durations[$durationChecked->getName()] = $durationChecked->getName();
        }

        return [$playerChoices,$choices['form_options'],$durations];
    }
}
