<?php

namespace App\Form;

use App\Entity\CollectionPoint;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionPointType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('addressDelivery', EntityType::class, [
                'class' => CollectionPoint::class,
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'placeholder' => '-- Choisir un lieu de retrait --',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.isActivedInCart = :value')
                        ->setParameter('value', true)
                        ->orderBy('c.name', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CollectionPoint::class,
        ]);
    }
}
