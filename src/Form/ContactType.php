<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse email:',
                'required' => true
            ])
            ->add('sujet', ChoiceType::class, [
                'label' => 'Sujet de votre demande:',
                'choices' => [
                    'PARTENARIAT' => 'PARTENARIAT',
                    'PRESSE'      => 'PRESSE',
                    'DON DE JEUX' => 'DON DE JEUX',
                    'DONNEES PERSONNELLES' => 'DONNEES PERSONNELLES',
                    'AUTRE' => 'AUTRE'
                ],
                'placeholder' => 'Choisir...',
                'required' => true
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message:',
                'required' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer votre message'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
