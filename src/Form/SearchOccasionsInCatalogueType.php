<?php

namespace App\Form;

use App\Repository\DurationOfGameRepository;
use App\Repository\NumbersOfPlayersRepository;
use App\Service\OccasionService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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

        $builder
            ->add('players', ChoiceType::class, [
                'label' => 'Nombre de joueur(s)',
                'attr' => [
                    'class' => 'form-control'
                ],
                'choices' => $options['playersOptions'],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('durations',  ChoiceType::class, [
                'label' => 'Durée de la partie',
                'attr' => [
                    'class' => 'form-control'
                ],
                'choices' => $options['durationsOptions'],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('ages', ChoiceType::class, [
                'label' => 'Âge minimum',
                'attr' => [
                    'class' => 'form-control'
                ],
                'choices' => $options['agesOptions'],
                'expanded' => true,
                'multiple' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'agesOptions' => null,
            'playersOptions' => null,
            'durationsOptions' => null,
            // Configure your form options here
        ]);
    }
}
