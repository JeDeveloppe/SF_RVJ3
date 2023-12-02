<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\City;
use Symfony\Component\Form\AbstractType;
use App\Form\AdressesVilleAutocompleteField;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isFacturation', ChoiceType::class, [
                'label' => 'Adresse de:',
                'attr' => [
                    'placeholder' => 'Faire un choix'
                ],
                'choices' => [
                    'FACTURATION' => true,
                    'LIVRAISON' => false
                    ]
                ])
            ->add('organization')
            ->add('firstname')
            ->add('lastname')
            ->add('street')
            // ->add('city', AdressesVilleAutocompleteField::class)
            ->add('city', EntityType::class, [
                'class' => City::class,
                'autocomplete' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
