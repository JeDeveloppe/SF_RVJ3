<?php

namespace App\Form;

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
        private OccasionService $occasionService
        ){
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $category = $options['category'];

        [$playerChoices,$ageChoices,$lengthChoices] = $this->formOccasionChoices($category);

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
            ->add('age_start', ChoiceType::class, [
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

        $choices = $this->occasionService->returnAgesChoices($category);

        //TODO
        $lengthChoices = [];
        $lengthChoices['Moins de 30min'] = 29;
        $lengthChoices['De 30min à 1hr'] = 29;

        return [$playerChoices,$choices['form_options'],$lengthChoices];
    }
}
