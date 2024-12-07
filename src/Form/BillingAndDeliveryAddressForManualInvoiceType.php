<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\CollectionPoint;
use App\Repository\CollectionPointRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use App\Repository\ShippingMethodRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillingAndDeliveryAddressForManualInvoiceType extends AbstractType
{

    public function __construct(
        private ShippingMethodRepository $shippingMethodRepository,
        private CollectionPointRepository $collectionPointRepository
        )
    {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $shippingMethod = $this->shippingMethodRepository->findOneById($options['shippingMethodId']);

        $builder
            ->add('billingAddress', EntityType::class, [
                'class' => Address::class,
                'label' => false,
                'attr' => [
                    'class' => 'col-12',
                ],
                'placeholder' => '-- Adresse de facturation --',
                'multiple' => false,
                'expanded' => true,
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

            if($shippingMethod->getPrice() == 'GRATUIT'){

                $builder
                    ->add('deliveryAddress', EntityType::class, [
                        'class' => CollectionPoint::class,
                        'label' => false,
                        'attr' => [
                            'class' => 'form-control text-center col-12',
                        ],
                        'placeholder' => '-- Adresse de retrait --',
                        'multiple' => false,
                        'expanded' => true,
                        'mapped' => false,
                        'query_builder' => function (EntityRepository $er) use ($shippingMethod) {
                            return $er->createQueryBuilder('c')
                                ->where('c.isActivedInCart = :value')
                                ->andWhere('c.shippingmethod = :shippingmethod')
                                ->setParameter('value', true)
                                ->setParameter('shippingmethod', $shippingMethod)
                                ->orderBy('c.firstname', 'ASC');
                        },
                    ]);

            }else{

                $builder
                    ->add('deliveryAddress', EntityType::class, [
                        'class' => Address::class,
                        'label' => false,
                        'attr' => [
                            'class' => 'form-control text-center col-12',
                        ],
                        'placeholder' => '-- Adresse de livraison --',
                        'mapped' => false,
                        'multiple' => false,
                        'expanded' => true,
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

            $builder->add('confirm', CheckboxType::class, [
                'label' => 'Je confirme les prix et les addresses ci-dessus',
                'required' => true,
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'user' =>  null,
            'shippingMethodId' => null,
        ]);
    }
}
