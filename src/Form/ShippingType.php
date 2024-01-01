<?php

namespace App\Form;

use App\Entity\ShippingMethod;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShippingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //? s'il n'y a pas de jeux d'occasion dans le panier
        if($options['occasionInPanier'] == 0){
            $builder
                ->add('shipping', EntityType::class, [
                    'class' => ShippingMethod::class,
                    'placeholder' => '-- RETRAIT OU ENVOI ? --',
                    'query_builder' => function (EntityRepository $s) {
                        return $s->createQueryBuilder('s')
                            ->where('s.isActivedInCart = :value')
                            ->setParameter('value', true)
                            ->orderBy('s.name', 'ASC');
                    },
                    'mapped' => false
                ]);
        }else{
            $builder
            ->add('shipping', EntityType::class, [
                'class' => ShippingMethod::class,
                'placeholder' => '-- RETRAIT OU ENVOI ? --',
                'query_builder' => function (EntityRepository $s) {
                    return $s->createQueryBuilder('s')
                        ->where('s.isActivedInCart = :value')
                        ->andWhere('s.forOccasionOnly = :value')
                        ->setParameter('value', true)
                        ->orderBy('s.name', 'ASC');
                },
                'mapped' => false
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShippingMethod::class,
            'occasionInPanier' => null
        ]);
    }
}
