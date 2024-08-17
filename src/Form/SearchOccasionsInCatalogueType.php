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
                'choices' => $options['playersOptions'],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('duration',  ChoiceType::class, [
                'label' => 'Durée de la partie:',
                'attr' => [
                    'class' => 'form-control'
                ],
                'choices' => $options['durationsOptions'],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('age_start', ChoiceType::class, [
                'label' => 'Âge minimum:',
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
