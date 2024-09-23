<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Country;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Adresse email (identifiant):',
                'attr' => [
                    'readonly' => true,
                    'class' => 'form-control mb-3'
                ]
            ])
            // ->add('roles')
            ->add('phone', TelType::class, [
                'label' => 'Téléphone:',
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Téléphone'
                ],

            ])
            ->add('country', EntityType::class, [
                'label' => 'Pays:',
                'choice_label' => 'name',
                'class' => Country::class,
                'query_builder' => function (EntityRepository $c) {
                    return $c->createQueryBuilder('c')
                        ->where('c.actifInInscriptionForm = :value')
                        ->setParameter('value', true)
                        ->orderBy('c.name','ASC');
                },
                'attr' => [
                    'class' => 'form-control mb-3'
                ]
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
