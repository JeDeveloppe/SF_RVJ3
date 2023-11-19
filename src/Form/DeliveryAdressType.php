<?php

namespace App\Form;

use App\Entity\Address;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryAdressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder
            ->add('addressDelivery', EntityType::class, [
                'class' => Address::class,
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'placeholder' => '-- Choisir une adresse de livraison --',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('a')
                        ->where('a.isFacturation = :value')
                        ->andWhere('a.user = :user')
                        ->setParameter('user', $user)
                        ->setParameter('value', false)
                        ->orderBy('a.id', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'user' =>  null
        ]);
    }
}
