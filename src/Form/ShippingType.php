<?php

namespace App\Form;

use App\Entity\ShippingMethod;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class ShippingType extends AbstractType
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
        
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        //? s'il n'y a pas de jeux d'occasion dans le panier
        if(count($options['occasionInPanier']) == 0){
            $builder
                ->add('shipping', EntityType::class, [
                    'class' => ShippingMethod::class,
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'placeholder' => '-- RETRAIT OU ENVOI ? --',
                    'query_builder' => function (EntityRepository $s) {
                        return $s->createQueryBuilder('s')
                            ->where('s.isActivedInCart = :value')
                            ->setParameter('value', true)
                            ->orderBy('s.name', 'ASC');
                    },
                    'mapped' => false,
                    'expanded' => true,
                    'multiple' => false,
                ]);
        }else{
            $builder
                ->add('shipping', EntityType::class, [
                    'class' => ShippingMethod::class,
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'placeholder' => '-- LIEU DE RETRAIT  --',
                    'query_builder' => function (EntityRepository $s) {
                        return $s->createQueryBuilder('s')
                            ->where('s.isActivedInCart = :value')
                            ->andWhere('s.forOccasionOnly = :value')
                            ->setParameter('value', true)
                            ->orderBy('s.name', 'DESC');
                    },
                    'mapped' => false,
                    'expanded' => true,
                    'multiple' => false,
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
