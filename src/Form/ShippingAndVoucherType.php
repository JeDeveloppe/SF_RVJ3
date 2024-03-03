<?php

namespace App\Form;

use App\Entity\ShippingMethod;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UuidType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class ShippingAndVoucherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('voucherDiscount', TextType::class, [
                'label' => 'Un code de réduction ?',
                'help' => 'Exemple: 0124-1234567890',
                'help_attr' => ['class' => 'small'],
                'attr' => [
                    'class' => 'form-control text-center',
                    'placeholder' => 'XXXX-XXXXXXXXXX',
                ],
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Regex('/\d{4}-\d{10}/m', 'Format non correct !')
                ],
            ]);
        //? s'il n'y a pas de jeux d'occasion dans le panier
        if($options['occasionInPanier'] == 0){
            $builder
                ->add('shipping', EntityType::class, [
                    'class' => ShippingMethod::class,
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control text-center mt-2',
                    ],
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
                    'attr' => [
                        'class' => 'form-control',
                    ],
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
