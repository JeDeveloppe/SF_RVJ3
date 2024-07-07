<?php

namespace App\Form;

use App\Repository\NumbersOfPlayersRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

class SearchOccasionInCatalogueType extends AbstractType
{
    public function __construct(
        private NumbersOfPlayersRepository $numbersOfPlayersRepository
        ){
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        [$playerChoices,$ageChoices,$lengthChoices] = $this->formOccasionChoices();

        $builder
            ->add('search', TextType::class, [
                'label' => 'Nom:',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Nom de jeu OU d\'éditeur...',
                    'class' => 'form-control'
                ],
                // 'constraints' => [
                //     new Assert\NotBlank([
                //         'message' => 'Ne peut pas être vide...',
                //     ]),
                //     new Assert\Length([
                //         'min' => 3,
                //         'minMessage' => 'Minimum {{ limit }} charactères',
                //         // max length allowed by Symfony for security reasons
                //         'max' => 25,
                //     ])
                // ]
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
            ->add('age', ChoiceType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,
                'choices' => $ageChoices,
                'placeholder' => 'Âge minimum ?',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }

    public function formOccasionChoices()
    {
        $playerChoices = [];
        $playersChecked = $this->numbersOfPlayersRepository->findBy(['isInOccasionFormSearch' => true],['orderOfAppearance' => 'ASC']);

        foreach($playersChecked as $playerChecked){
            $playerChoices[$playerChecked->getName()] = $playerChecked->getName();
        }


        $ageChoices = [];
        $ageChoices['A partir de 1 an'] = 1;
        for($i = 2; $i <= 18; $i++){
            $ageChoices['A partir de '.$i.' ans'] = $i;
        }

        $lengthChoices = [];
        $lengthChoices['Moins de 30min'] = 29;
        $lengthChoices['De 30min à 1hr'] = 29;

        return [$playerChoices,$ageChoices,$lengthChoices];
    }
}
