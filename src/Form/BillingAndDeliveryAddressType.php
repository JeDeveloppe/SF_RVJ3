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

        if($options['redirectAfterSubmitPanierForPaiement'] == false){
            $labelButtonPanier = 'Demander un devis';
        }else{
            $labelButtonPanier = 'Payer';
        }
        
        $builder
            ->add('billingAddress', EntityType::class, [
                'class' => Address::class,
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'mapped' => false,
                'placeholder' => '-- Choisir une adresse de facturation --',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('a')
                        ->where('a.isFacturation = :value')
                        ->andWhere('a.user = :user')
                        ->setParameter('user', $user)
                        ->setParameter('value', true)
                        ->orderBy('a.id', 'ASC');
                },
            ])
            ->add('shipping', EntityType::class, [
                'class' => ShippingMethod::class,
                'data' => $shipping,
                'mapped' => false,
                'label' => false,
                'attr' => [
                    'class' => 'd-none'
                ]

            ]);
        
        if(!is_null($shipping)){
            if($shipping->getPrice() == 'GRATUIT'){

                $builder
                    ->add('deliveryAddress', EntityType::class, [
                        'class' => CollectionPoint::class,
                        'label' => false,
                        'attr' => [
                            'class' => 'form-control',
                        ],
                        'mapped' => false,
                        'placeholder' => '-- Choisir un lieu de retrait --',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('c')
                                ->where('c.isActivedInCart = :value')
                                ->setParameter('value', true)
                                ->orderBy('c.name', 'ASC');
                        },
                    ]);

            }else{

                $builder
                    ->add('deliveryAddress', EntityType::class, [
                        'class' => Address::class,
                        'label' => false,
                        'attr' => [
                            'class' => 'form-control',
                        ],
                        'mapped' => false,
                        'placeholder' => '-- Choisir une adresse de livraison --',
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

        $builder
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-success mt-3 text-center col-12'
                ],
                'label' => $labelButtonPanier
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'user' =>  null,
            'shipping' => null,
            'redirectAfterSubmitPanierForPaiement' => null
        ]);
    }
}
