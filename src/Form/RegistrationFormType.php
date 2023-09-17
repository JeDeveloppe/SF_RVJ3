<?php

namespace App\Form;

use App\Entity\Country;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Validator\Constraints\Expression;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse email:',
                'attr' => [
                    'class' => 'form-control mb-3'
                ] 
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone:',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Téléphone obligatoire !',
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]\d*$/',
                        'message' => 'Téléphone: que des chiffres...'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control mb-3'
                ]
            ])
            ->add('country', EntityType::class, [
                'label' => 'Pays:',
                'class' => Country::class,
                'placeholder' => 'Choisir un pays...',
                'attr' => [
                    'class' => 'form-control mb-3'
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Mot de passe:',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password','class' => 'form-control mb-3'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Mot de passe obligatoire !',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit faire minimum {{ limit }} charactères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('plainPasswordVerification', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Vérification du mot de passe:',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password','class' => 'form-control mb-3'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Mot de passe obligatoire !',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit faire minimum {{ limit }} charactères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
