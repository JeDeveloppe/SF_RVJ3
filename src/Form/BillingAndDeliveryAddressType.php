<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\ShippingMethod;
use App\Entity\CollectionPoint;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BillingAndDeliveryAddressType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $shipping = $options['shipping'];
        
        $builder
            ->add('billingAddress', EntityType::class, [
                'class' => Address::class,
                'label' => false,
                'attr' => [
                    'class' => 'form-control text-center',
                ],
                'placeholder' => '-- Choisir une adresse de facturation --',
                'multiple' => false,
                'expanded' => false,
                'mapped' => false,
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('a')
                        ->where('a.isFacturation = :value')
                        ->andWhere('a.user = :user')
                        ->setParameter('user', $user)
                        ->setParameter('value', true)
                        ->orderBy('a.id', 'ASC');
                },
            ]);
        
            if($shipping->getPrice() == 'GRATUIT'){

                $builder
                    ->add('deliveryAddress', EntityType::class, [
                        'class' => CollectionPoint::class,
                        'label' => false,
                        'attr' => [
                            'class' => 'form-control text-center',
                        ],
                        'placeholder' => '-- Choisir une adresse de retrait --',
                        'multiple' => false,
                        'expanded' => false,
                        'mapped' => false,
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('c')
                                ->where('c.isActivedInCart = :value')
                                ->setParameter('value', true)
                                ->orderBy('c.firstname', 'ASC');
                        },
                    ]);

            }else{

                $builder
                    ->add('deliveryAddress', EntityType::class, [
                        'class' => Address::class,
                        'label' => false,
                        'attr' => [
                            'class' => 'form-control text-center',
                        ],
                        'placeholder' => '-- Choisir une adresse de livraison --',
                        'mapped' => false,
                        'multiple' => false,
                        'expanded' => false,
                        'query_builder' => function (EntityRepository $er) use ($user) {
                            return $er->createQueryBuilder('a')
                                ->where('a.isFacturation = :value')
                                ->andWhere('a.user = :user')
                                ->setParameter('user', $user)
                                ->setParameter('value', false)
                                ->orderBy('a.id', 'ASC');
                        },
                    ]);
            }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'user' =>  null,
            'shipping' => null,
        ]);
    }
}
